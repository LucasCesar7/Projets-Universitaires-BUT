<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\RateLimiterService;
use Tests\TestCase;

/**
 * Tests unitaires pour RateLimiterService.
 *
 * Ce service est entièrement pur (pas de BDD, pas de session) :
 * il persiste l'état dans des fichiers JSON dans un répertoire temporaire.
 * Tous les tests utilisent un répertoire isolé qui est nettoyé après chaque test.
 */
class RateLimiterServiceTest extends TestCase
{
    private string $storageDir;
    private RateLimiterService $limiter;

    protected function setUp(): void
    {
        $this->storageDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sma_rl_test_' . uniqid();
        mkdir($this->storageDir, 0755, true);

        // 3 tentatives max, fenêtre de 60 secondes
        $this->limiter = new RateLimiterService(3, 60, $this->storageDir);
    }

    protected function tearDown(): void
    {
        // Supprime les fichiers de state créés pendant le test
        foreach (glob($this->storageDir . DIRECTORY_SEPARATOR . '*.json') ?: [] as $file) {
            unlink($file);
        }
        @rmdir($this->storageDir);
    }

    // ── resolveClientIp ────────────────────────────────────────────────────

    public function testResolveClientIpReturnsRemoteAddr(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.42';
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);

        $this->assertSame('192.168.1.42', RateLimiterService::resolveClientIp());
    }

    public function testResolveClientIpPrefersTrustedForwardedFor(): void
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '10.0.0.1, 172.16.0.5';
        $_SERVER['REMOTE_ADDR'] = '192.168.1.1';

        // Le premier IP valide de la liste doit être renvoyé
        $this->assertSame('10.0.0.1', RateLimiterService::resolveClientIp());

        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
    }

    public function testResolveClientIpReturnsUnknownWhenNoneAvailable(): void
    {
        unset($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_X_FORWARDED_FOR']);

        $this->assertSame('unknown', RateLimiterService::resolveClientIp());

        // Restaurer pour ne pas polluer les autres tests
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    }

    public function testResolveClientIpIgnoresInvalidIp(): void
    {
        $_SERVER['REMOTE_ADDR'] = 'not-an-ip';
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);

        $this->assertSame('unknown', RateLimiterService::resolveClientIp());

        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    }

    // ── hit ────────────────────────────────────────────────────────────────

    public function testHitReturnZeroBeforeThreshold(): void
    {
        $retryAfter = $this->limiter->hit('login', 'user@example.com');

        // Premier hit (1 sur 3) → pas encore bloqué
        $this->assertSame(0, $retryAfter);
    }

    public function testHitReturnZeroUpToMaxMinusOne(): void
    {
        $this->limiter->hit('login', 'user@example.com');
        $retryAfter = $this->limiter->hit('login', 'user@example.com'); // 2e sur 3

        $this->assertSame(0, $retryAfter);
    }

    public function testHitReturnPositiveWhenThresholdReached(): void
    {
        $this->limiter->hit('login', 'user@example.com');
        $this->limiter->hit('login', 'user@example.com');
        $retryAfter = $this->limiter->hit('login', 'user@example.com'); // 3e → bloqué

        $this->assertGreaterThan(0, $retryAfter, 'Doit retourner un délai > 0 une fois le seuil atteint');
        $this->assertLessThanOrEqual(60, $retryAfter);
    }

    public function testHitContinuesToReturnPositiveAfterThreshold(): void
    {
        $this->limiter->hit('login', 'user@example.com');
        $this->limiter->hit('login', 'user@example.com');
        $this->limiter->hit('login', 'user@example.com'); // bloqué

        $extra = $this->limiter->hit('login', 'user@example.com'); // 4e hit
        $this->assertGreaterThan(0, $extra);
    }

    // ── getRetryAfter ─────────────────────────────────────────────────────

    public function testGetRetryAfterReturnsZeroWhenNotBlocked(): void
    {
        $this->limiter->hit('login', 'fresh@example.com');

        $this->assertSame(0, $this->limiter->getRetryAfter('login', 'fresh@example.com'));
    }

    public function testGetRetryAfterReturnsPositiveWhenBlocked(): void
    {
        $this->limiter->hit('login', 'blocked@example.com');
        $this->limiter->hit('login', 'blocked@example.com');
        $this->limiter->hit('login', 'blocked@example.com');

        $this->assertGreaterThan(0, $this->limiter->getRetryAfter('login', 'blocked@example.com'));
    }

    // ── clear ─────────────────────────────────────────────────────────────

    public function testClearResetsCounterAfterBlock(): void
    {
        $this->limiter->hit('login', 'reset@example.com');
        $this->limiter->hit('login', 'reset@example.com');
        $this->limiter->hit('login', 'reset@example.com'); // bloqué

        $this->limiter->clear('login', 'reset@example.com');

        // Après clear, getRetryAfter doit retourner 0
        $this->assertSame(0, $this->limiter->getRetryAfter('login', 'reset@example.com'));
    }

    public function testClearOnNonExistentIdentifierDoesNotThrow(): void
    {
        $this->expectNotToPerformAssertions();
        $this->limiter->clear('login', 'nobody@example.com');
    }

    // ── isolation des scopes ──────────────────────────────────────────────

    public function testDifferentScopesAreIsolated(): void
    {
        $this->limiter->hit('login', 'same@example.com');
        $this->limiter->hit('login', 'same@example.com');
        $this->limiter->hit('login', 'same@example.com'); // bloqué sur 'login'

        // Le scope 'register' doit être indépendant
        $retryAfter = $this->limiter->getRetryAfter('register', 'same@example.com');
        $this->assertSame(0, $retryAfter);
    }

    public function testDifferentIdentifiersAreIsolated(): void
    {
        $this->limiter->hit('login', 'alice@example.com');
        $this->limiter->hit('login', 'alice@example.com');
        $this->limiter->hit('login', 'alice@example.com'); // alice bloquée

        // bob ne doit pas être affecté
        $this->assertSame(0, $this->limiter->getRetryAfter('login', 'bob@example.com'));
    }
}
