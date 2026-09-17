<?php

declare(strict_types=1);

namespace Tests\Unit\Helpers;

use Tests\TestCase;

/**
 * Tests unitaires pour les fonctions utilitaires de config/paths.php.
 *
 * Ces fonctions sont chargées par l'autoloader Composer via la clé "files".
 * Elles sont purement fonctionnelles (pas de side-effects globaux dans les tests).
 */
class AppUrlTest extends TestCase
{
    // ── sanitizeAppRedirect ───────────────────────────────────────────────

    /**
     * Un chemin relatif valide doit être accepté et transformé en URL applicative.
     */
    public function testSanitizeAcceptsValidRelativePath(): void
    {
        $result = sanitizeAppRedirect('views/login.php');

        $this->assertIsString($result);
        $this->assertStringContainsString('views/login.php', $result);
    }

    /**
     * Une URL absolue externe doit être rejetée.
     */
    public function testSanitizeRejectsExternalUrl(): void
    {
        $result = sanitizeAppRedirect('https://evil.com/phishing');

        $this->assertNull($result);
    }

    /**
     * Un protocole javascript: doit être rejeté.
     */
    public function testSanitizeRejectsJavascriptProtocol(): void
    {
        $result = sanitizeAppRedirect('javascript:alert(1)');

        $this->assertNull($result);
    }

    /**
     * Un chemin avec un retour à la ligne (header injection) doit être rejeté.
     */
    public function testSanitizeRejectsHeaderInjection(): void
    {
        $result = sanitizeAppRedirect("views/login.php\r\nX-Injected: header");

        $this->assertNull($result);
    }

    /**
     * Un chemin contenant `..` (path traversal) doit être rejeté.
     */
    public function testSanitizeRejectsPathTraversal(): void
    {
        $result = sanitizeAppRedirect('../../etc/passwd');

        $this->assertNull($result);
    }

    /**
     * Un double-slash en début de chemin doit être rejeté (protocol-relative URL).
     */
    public function testSanitizeRejectsDoubleSlash(): void
    {
        $result = sanitizeAppRedirect('//evil.com/path');

        $this->assertNull($result);
    }

    /**
     * null doit renvoyer le fallback.
     */
    public function testSanitizeReturnsNullForNullInput(): void
    {
        $this->assertNull(sanitizeAppRedirect(null));
    }

    /**
     * Une chaîne vide doit renvoyer null (aucun chemin valide).
     */
    public function testSanitizeReturnsNullForEmptyString(): void
    {
        $this->assertNull(sanitizeAppRedirect(''));
    }

    /**
     * Le fallback doit être retourné si la valeur est invalide.
     */
    public function testSanitizeReturnsFallbackOnInvalid(): void
    {
        $result = sanitizeAppRedirect('https://evil.com', 'views/login.php');

        $this->assertNotNull($result);
        $this->assertStringContainsString('views/login.php', $result);
    }

    // ── appUrl ────────────────────────────────────────────────────────────

    public function testAppUrlReturnsString(): void
    {
        $this->assertIsString(appUrl('index.php'));
    }

    public function testAppUrlEndsWithGivenPath(): void
    {
        $url = appUrl('views/login.php');

        $this->assertStringEndsWith('views/login.php', $url);
    }

    public function testAppUrlPrefersExistingMinifiedAssets(): void
    {
        $url = appUrl('assets/css/header.css');

        $this->assertStringEndsWith('assets/css/header.min.css', $url);
    }

    public function testPublicRouteUrlUsesCanonicalLoginRoute(): void
    {
        $url = publicRouteUrl('login');

        $this->assertStringEndsWith('/login', $url);
    }

    public function testPublicPlatformUrlUsesNomineesRouteWithPlatformQuery(): void
    {
        $url = publicPlatformUrl('Instagram');

        $this->assertStringContainsString('/nominees', $url);
        $this->assertStringContainsString('platform=instagram', $url);
    }

    public function testAppUrlWithEmptyPathReturnsBaseOrSlash(): void
    {
        $url = appUrl('');

        $this->assertIsString($url);
        // La valeur peut être '' ou '/' ou '/sous-dossier' selon le contexte CLI
        $this->assertMatchesRegularExpression('#^/?$|^/\S+$#', $url);
    }

    // ── resolveBaseUrlFromServerPath ──────────────────────────────────────

    public function testResolveBaseUrlStripViewsSegment(): void
    {
        $result = resolveBaseUrlFromServerPath('/Social-Media-Awards/views/login.php');

        $this->assertSame('/Social-Media-Awards', $result);
    }

    public function testResolveBaseUrlFromRootScript(): void
    {
        $result = resolveBaseUrlFromServerPath('/Social-Media-Awards/index.php');

        $this->assertSame('/Social-Media-Awards', $result);
    }

    public function testResolveBaseUrlFromEmptyPathReturnsEmptyString(): void
    {
        $result = resolveBaseUrlFromServerPath('');

        $this->assertSame('', $result);
    }
}
