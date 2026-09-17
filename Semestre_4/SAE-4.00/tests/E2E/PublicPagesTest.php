<?php

declare(strict_types=1);

namespace Tests\E2E;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Tests\TestCase;

/**
 * Tests de bout en bout (E2E) pour les pages publiques.
 *
 * Ces tests nécessitent que le serveur Docker soit actif sur http://localhost:8080.
 * Si le serveur est indisponible, tous les tests sont ignorés (markTestSkipped).
 *
 * Lancer le serveur : docker compose up -d
 */
class PublicPagesTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $baseUrl = getenv('E2E_BASE_URL') ?: 'http://localhost:8080';

        // Vérifier que le serveur est accessible avant tout test
        try {
            $ping = new Client(['base_uri' => $baseUrl, 'timeout' => 3, 'http_errors' => false]);
            $ping->get('/');
        } catch (ConnectException $e) {
            $this->markTestSkipped("Serveur E2E inaccessible sur $baseUrl — lancez Docker d'abord.");
        }

        $this->client = new Client([
            'base_uri'        => $baseUrl,
            'timeout'         => 10,
            'http_errors'     => false,
            'allow_redirects' => false, // On gère les redirections manuellement
        ]);
    }

    // ── Pages publiques accessibles sans authentification ─────────────────

    public function testIndexPageReturns200(): void
    {
        $response = $this->client->get('/');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testNomineesPageReturns200(): void
    {
        $response = $this->client->get('/nominees');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testCategoriesPageReturns200(): void
    {
        $response = $this->client->get('/categories');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testResultsPageReturns200(): void
    {
        $response = $this->client->get('/results');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testAboutPageReturns200(): void
    {
        $response = $this->client->get('/about');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testContactPageReturns200(): void
    {
        $response = $this->client->get('/contact');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testCguPageReturns200(): void
    {
        $response = $this->client->get('/cgu');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testPrivacyPageReturns200(): void
    {
        $response = $this->client->get('/privacy');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testLoginPageReturns200(): void
    {
        $response = $this->client->get('/login');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testLegacyPublicPhpUrlReturns200(): void
    {
        $response = $this->client->get('/results.php');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testLegacyLoginViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/login.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyDashboardViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/admin/dashboard.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyAdminNominationsViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/admin/nominations/manage-nominations.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyAdminCategoriesViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/admin/categories/gerer-categories.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyAdminCategoryEditViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/admin/categories/modifier-categorie.php?id=1');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyAdminCategoriesDeletePostReturns404(): void
    {
        $response = $this->client->post('/views/admin/categories/gerer-categories.php', [
            'form_params' => ['delete_category_id' => 1],
        ]);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyAdminEditionsViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/admin/editions/gerer-editions.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyAdminEditionsDeletePostReturns404(): void
    {
        $response = $this->client->post('/views/admin/editions/gerer-editions.php', [
            'form_params' => ['delete_edition_id' => 1],
        ]);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyAdminCandidaturesViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/admin/candidatures/manage-candidature.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyAdminCandidatureDetailViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/admin/candidatures/view-candidature.php?id=1');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyAdminCandidatureProcessUrlReturns404(): void
    {
        $response = $this->client->post('/views/admin/candidatures/process-candidature.php', [
            'form_params' => ['id' => 1, 'action' => 'approve'],
        ]);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyAdminCandidatureDeletePostReturns404(): void
    {
        $response = $this->client->post('/views/admin/candidatures/manage-candidature.php', [
            'form_params' => ['delete_candidature_id' => 1],
        ]);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyAdminNominationViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/admin/nominations/view-nomination.php?id=1');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyAdminNominationEditUrlReturns404(): void
    {
        $response = $this->client->get('/views/admin/nominations/edit-nomination.php?id=1');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyAdminNominationDeleteUrlReturns404(): void
    {
        $response = $this->client->post('/views/admin/nominations/delete-nomination.php', [
            'form_params' => ['id' => 1],
        ]);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyUserDashboardViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/user/user-dashboard.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyVoteViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/user/Vote.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyChangePasswordViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/user/change-password.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyEditProfileViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/user/edit-profile.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyCandidateDashboardViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/candidate/candidate-dashboard.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyCandidateAvailableCategoriesEndpointReturns404(): void
    {
        $response = $this->client->get('/views/candidate/get-available-categories.php?user_id=1');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyCandidateCategoriesEndpointReturns404(): void
    {
        $response = $this->client->get('/views/candidate/get-categories-ajax.php?edition_id=1');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyCandidateCheckDuplicateEndpointReturns404(): void
    {
        $response = $this->client->get('/views/candidate/check-candidature-duplicate.php?category_id=1&platform=TikTok');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyCandidateCheckPlatformEndpointReturns404(): void
    {
        $response = $this->client->get('/views/candidate/check-candidature-platform.php?category_id=1&platform=TikTok');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyCandidateApplicationsViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/candidate/mes-candidatures.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyCandidateDetailViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/candidate/candidature-details.php?id=1');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyCandidateProfileViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/candidate/profil-candidat.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyCandidateNomineeProfileViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/candidate/nominee-profile.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyCandidateRulesViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/candidate/reglement.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyCandidateShareViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/candidate/share-nomination.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyCandidateSubmitViewUrlReturns404(): void
    {
        $response = $this->client->get('/views/candidate/soumettre-candidature.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testLegacyCandidateDeleteViewUrlReturns404(): void
    {
        $response = $this->client->post('/views/candidate/delete-candidature.php', [
            'form_params' => ['id' => 1],
        ]);

        $this->assertSame(404, $response->getStatusCode());
    }

    // ── Sécurité — redirection si non authentifié ─────────────────────────

    public function testAdminDashboardRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/admin/dashboard');

        // Doit retourner 302 (redirect vers login) et non pas 200 ou 403
        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testAdminRootRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/admin');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testAdminCategoriesRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/admin/categories');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testAdminCategoryEditRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/admin/categories/edit?id=1');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testAdminCategoriesDeleteRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->post('/admin/categories', [
            'form_params' => ['delete_category_id' => 1],
        ]);

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testAdminEditionsRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/admin/editions');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testAdminEditionsDeleteRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->post('/admin/editions', [
            'form_params' => ['delete_edition_id' => 1],
        ]);

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testAdminCandidaturesRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/admin/candidatures');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testAdminCandidatureViewRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/admin/candidatures/view?id=1');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testAdminCandidatureProcessRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->post('/admin/candidatures/process', [
            'form_params' => ['id' => 1, 'action' => 'approve'],
        ]);

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testAdminCandidatureDeleteRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->post('/admin/candidatures/delete', [
            'form_params' => ['id' => 1],
        ]);

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testAdminNominationsRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/admin/nominations');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testAdminNominationViewRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/admin/nominations/view?id=1');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testAdminNominationEditRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/admin/nominations/edit?id=1');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testAdminNominationDeleteRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->post('/admin/nominations/delete', [
            'form_params' => ['id' => 1],
        ]);

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testUserDashboardRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/user/dashboard');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testVoteRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/vote');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testUserPasswordRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/user/password');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testUserProfileRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/user/profile');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testCandidateDashboardRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/candidate/dashboard');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testCandidateAvailableCategoriesEndpointReturns401WhenNotAuthenticated(): void
    {
        $response = $this->client->get('/candidate/api/available-categories?user_id=1');

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testCandidateCategoriesEndpointReturns401WhenNotAuthenticated(): void
    {
        $response = $this->client->get('/candidate/api/categories?edition_id=1');

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testCandidateCheckDuplicateEndpointReturns401WhenNotAuthenticated(): void
    {
        $response = $this->client->get('/candidate/api/check-duplicate?category_id=1&platform=TikTok');

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testCandidateCheckPlatformEndpointReturns401WhenNotAuthenticated(): void
    {
        $response = $this->client->get('/candidate/api/check-platform?category_id=1&platform=TikTok');

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testCandidateApplicationsRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/candidate/candidatures');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testCandidateDetailRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/candidate/candidatures/view?id=1');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testCandidateProfileRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/candidate/profile');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testCandidateNomineeProfileRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/candidate/nominee-profile');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testCandidateRulesRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/candidate/rules');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testCandidateShareRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/candidate/share');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testCandidateSubmitRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->get('/candidate/submit');

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testCandidateDeleteRedirectsWhenNotAuthenticated(): void
    {
        $response = $this->client->post('/candidate/candidatures/delete', [
            'form_params' => ['id' => 1],
        ]);

        $this->assertContains($response->getStatusCode(), [301, 302, 303]);
    }

    public function testInternalViewUrlIsNotPublic(): void
    {
        $response = $this->client->get('/views/partials/header.php');

        $this->assertSame(404, $response->getStatusCode());
    }

    // ── Formulaire de connexion ───────────────────────────────────────────

    public function testLoginFormWithInvalidCredentialsReturns200(): void
    {
        $response = $this->client->post('/login', [
            'form_params' => [
                'email'    => 'invalide@example.com',
                'password' => 'mauvais_mot_de_passe_123',
            ],
        ]);

        // L'erreur doit être affichée sur la même page (pas de redirection)
        $this->assertSame(200, $response->getStatusCode());
        $body = (string) $response->getBody();
        // Le formulaire doit toujours être présent
        $this->assertStringContainsString('login', strtolower($body));
    }

    // ── Contenu HTML de base ──────────────────────────────────────────────

    public function testIndexPageContainsDoctype(): void
    {
        $response = $this->client->get('/');
        $body     = (string) $response->getBody();

        $this->assertStringContainsString('<!DOCTYPE', strtoupper($body));
    }

    public function testPagesReturnHtmlContentType(): void
    {
        $pages = ['/', '/nominees', '/categories', '/results'];

        foreach ($pages as $page) {
            $response    = $this->client->get($page);
            $contentType = $response->getHeaderLine('Content-Type');

            $this->assertStringContainsString('text/html', $contentType, "Page $page doit retourner text/html");
        }
    }
}
