<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\CsrfService;
use Tests\TestCase;

/**
 * Tests unitaires pour CsrfService.
 *
 * Valide la génération et la vérification des tokens CSRF.
 * La session PHP est démarrée par tests/bootstrap.php en mode CLI.
 */
class CsrfServiceTest extends TestCase
{
    protected function setUp(): void
    {
        // Nettoyer les tokens de session entre chaque test
        $_SESSION['_csrf_tokens'] = [];
    }

    // ── token() ──────────────────────────────────────────────────────────

    public function testTokenReturnsNonEmptyString(): void
    {
        $token = CsrfService::token('login');

        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function testTokenIs64HexCharacters(): void
    {
        $token = CsrfService::token('login');

        // bin2hex(random_bytes(32)) → 64 caractères hexadécimaux
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $token);
    }

    public function testTokenIsSameOnConsecutiveCallsForSameContext(): void
    {
        $first  = CsrfService::token('checkout');
        $second = CsrfService::token('checkout');

        $this->assertSame($first, $second, 'Le token doit être stable pour un même contexte dans la même session');
    }

    public function testTokenDiffersAcrossContexts(): void
    {
        $tokenA = CsrfService::token('context_a');
        $tokenB = CsrfService::token('context_b');

        $this->assertNotSame($tokenA, $tokenB, 'Des contextes différents doivent produire des tokens différents');
    }

    // ── isValid() ────────────────────────────────────────────────────────

    public function testIsValidReturnsTrueForCorrectToken(): void
    {
        $token = CsrfService::token('delete');

        $this->assertTrue(CsrfService::isValid($token, 'delete'));
    }

    public function testIsValidReturnsFalseForWrongToken(): void
    {
        CsrfService::token('delete');

        $this->assertFalse(CsrfService::isValid('mauvais_token', 'delete'));
    }

    public function testIsValidReturnsFalseForNull(): void
    {
        CsrfService::token('delete');

        $this->assertFalse(CsrfService::isValid(null, 'delete'));
    }

    public function testIsValidReturnsFalseForUnknownContext(): void
    {
        $this->assertFalse(CsrfService::isValid('quelquetoken', 'contexte_inexistant'));
    }

    public function testIsValidReturnsFalseForEmptyString(): void
    {
        CsrfService::token('form');

        $this->assertFalse(CsrfService::isValid('', 'form'));
    }

    public function testIsValidReturnsFalseAfterSessionClear(): void
    {
        $token = CsrfService::token('form');

        // Simuler une expiration de session
        unset($_SESSION['_csrf_tokens']['form']);

        $this->assertFalse(CsrfService::isValid($token, 'form'));
    }

    public function testTokensFromDifferentContextsDoNotCrossValidate(): void
    {
        $tokenA = CsrfService::token('form_a');
        $tokenB = CsrfService::token('form_b');

        // Le token de A ne doit pas valider le contexte B et vice versa
        $this->assertFalse(CsrfService::isValid($tokenA, 'form_b'));
        $this->assertFalse(CsrfService::isValid($tokenB, 'form_a'));
    }
}
