<?php

namespace App\Services;

class RateLimiterService
{
    private string $storageDirectory;

    public function __construct(
        private int $maxAttempts = 6,
        private int $windowSeconds = 900,
        ?string $storageDirectory = null
    ) {
        $baseDirectory = $storageDirectory;

        if (!is_string($baseDirectory) || trim($baseDirectory) === '') {
            $baseDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sma-rate-limits';
        }

        $this->storageDirectory = rtrim($baseDirectory, DIRECTORY_SEPARATOR);
    }

    public static function resolveClientIp(): string
    {
        $forwardedFor = trim((string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''));

        if ($forwardedFor !== '') {
            $parts = explode(',', $forwardedFor);
            $candidate = trim($parts[0]);

            if (filter_var($candidate, FILTER_VALIDATE_IP) !== false) {
                return $candidate;
            }
        }

        $remoteAddress = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));

        if ($remoteAddress !== '' && filter_var($remoteAddress, FILTER_VALIDATE_IP) !== false) {
            return $remoteAddress;
        }

        return 'unknown';
    }

    public function getRetryAfter(string $scope, string $identifier): int
    {
        return $this->withState($scope, $identifier, function (array &$state, int $now): int {
            $state = $this->normalizeState($state, $now);

            if ($state['attempts'] < $this->maxAttempts) {
                return 0;
            }

            return max(0, $this->windowSeconds - ($now - $state['window_started_at']));
        });
    }

    public function hit(string $scope, string $identifier): int
    {
        return $this->withState($scope, $identifier, function (array &$state, int $now): int {
            $state = $this->normalizeState($state, $now);

            if ($state['attempts'] === 0) {
                $state['window_started_at'] = $now;
            }

            $state['attempts']++;

            if ($state['attempts'] < $this->maxAttempts) {
                return 0;
            }

            return max(1, $this->windowSeconds - ($now - $state['window_started_at']));
        });
    }

    public function clear(string $scope, string $identifier): void
    {
        $filePath = $this->getFilePath($scope, $identifier);

        if (is_file($filePath)) {
            @unlink($filePath);
        }
    }

    public function getAttempts(string $scope, string $identifier): int
    {
        return $this->withState($scope, $identifier, function (array &$state, int $now): int {
            $state = $this->normalizeState($state, $now);

            return (int) $state['attempts'];
        });
    }

    private function withState(string $scope, string $identifier, callable $callback): int
    {
        $this->ensureStorageDirectory();

        $filePath = $this->getFilePath($scope, $identifier);
        $handle = fopen($filePath, 'c+');

        if ($handle === false) {
            return 0;
        }

        $now = time();

        try {
            if (!flock($handle, LOCK_EX)) {
                fclose($handle);
                return 0;
            }

            rewind($handle);
            $contents = stream_get_contents($handle);
            $state = [];

            if (is_string($contents) && trim($contents) !== '') {
                $decoded = json_decode($contents, true);

                if (is_array($decoded)) {
                    $state = $decoded;
                }
            }

            $result = $callback($state, $now);
            $state = $this->normalizeState($state, $now);

            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, json_encode($state, JSON_THROW_ON_ERROR));
            fflush($handle);
            flock($handle, LOCK_UN);
            fclose($handle);

            return (int) $result;
        } catch (\JsonException) {
            flock($handle, LOCK_UN);
            fclose($handle);
            return 0;
        } catch (\Throwable $exception) {
            flock($handle, LOCK_UN);
            fclose($handle);
            return 0;
        }
    }

    private function normalizeState(array $state, int $now): array
    {
        $attempts = max(0, (int) ($state['attempts'] ?? 0));
        $windowStartedAt = (int) ($state['window_started_at'] ?? 0);

        if ($windowStartedAt <= 0 || ($now - $windowStartedAt) >= $this->windowSeconds) {
            return [
                'attempts' => 0,
                'window_started_at' => $now
            ];
        }

        return [
            'attempts' => $attempts,
            'window_started_at' => $windowStartedAt
        ];
    }

    private function ensureStorageDirectory(): void
    {
        if (!is_dir($this->storageDirectory)) {
            @mkdir($this->storageDirectory, 0755, true);
        }
    }

    private function getFilePath(string $scope, string $identifier): string
    {
        $hashedKey = hash('sha256', $scope . '|' . $identifier);

        return $this->storageDirectory . DIRECTORY_SEPARATOR . $hashedKey . '.json';
    }
}