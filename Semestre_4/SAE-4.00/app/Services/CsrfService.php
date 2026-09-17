<?php

namespace App\Services;

class CsrfService
{
    private static function ensureSessionStarted(): void
    {
        if (session_status() !== PHP_SESSION_NONE || headers_sent()) {
            return;
        }

        if (function_exists('startSecureSession')) {
            \startSecureSession();
            return;
        }

        session_start();
    }

    public static function token(string $context): string
    {
        self::ensureSessionStarted();

        if (!isset($_SESSION['_csrf_tokens']) || !is_array($_SESSION['_csrf_tokens'])) {
            $_SESSION['_csrf_tokens'] = [];
        }

        if (empty($_SESSION['_csrf_tokens'][$context])) {
            $_SESSION['_csrf_tokens'][$context] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_tokens'][$context];
    }

    public static function isValid(?string $token, string $context): bool
    {
        self::ensureSessionStarted();

        $sessionToken = $_SESSION['_csrf_tokens'][$context] ?? null;

        return is_string($token)
            && is_string($sessionToken)
            && $sessionToken !== ''
            && hash_equals($sessionToken, $token);
    }
}