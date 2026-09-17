<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers;

use App\Controllers\AuthPageController;
use App\Controllers\AdminPageController;
use App\Controllers\CandidatePageController;
use App\Controllers\DashboardRedirectController;
use App\Controllers\FrontController;
use App\Controllers\PublicPageController;
use App\Controllers\VoterPageController;
use Tests\TestCase;

class FrontControllerTest extends TestCase
{
    public function testResolveReturnsMappedFileForRootRoute(): void
    {
        $controller = new FrontController([
            '/' => 'index.php',
        ], dirname(__DIR__, 3));

        $this->assertSame(
            dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'index.php',
            $controller->resolve('/')
        );
    }

    public function testResolveIgnoresQueryString(): void
    {
        $controller = new FrontController([
            '/views/admin/candidatures/manage-candidature.php' => 'views/admin/candidatures/manage-candidature.php',
        ], dirname(__DIR__, 3));

        $this->assertSame(
            dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'candidatures' . DIRECTORY_SEPARATOR . 'manage-candidature.php',
            $controller->resolve('/views/admin/candidatures/manage-candidature.php?tab=overview')
        );
    }

    public function testResolveReturnsNullForUnknownRoute(): void
    {
        $controller = new FrontController([
            '/about' => 'about.php',
        ], dirname(__DIR__, 3));

        $this->assertNull($controller->resolve('/route-inconnue'));
    }

    public function testResolveControllerReturnsControllerTarget(): void
    {
        $controller = new FrontController([
            '/' => ['controller' => PublicPageController::class, 'action' => 'home'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => PublicPageController::class, 'action' => 'home'],
            $controller->resolveController('/')
        );
    }

    public function testResolveControllerReturnsRegisterTarget(): void
    {
        $controller = new FrontController([
            '/inscription' => ['controller' => AuthPageController::class, 'action' => 'register'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => AuthPageController::class, 'action' => 'register'],
            $controller->resolveController('/inscription')
        );
    }

    public function testResolveControllerReturnsLoginTarget(): void
    {
        $controller = new FrontController([
            '/login' => ['controller' => AuthPageController::class, 'action' => 'login'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => AuthPageController::class, 'action' => 'login'],
            $controller->resolveController('/login')
        );
    }

    public function testResolveControllerReturnsLegacyPhpTarget(): void
    {
        $controller = new FrontController([
            '/results.php' => ['controller' => PublicPageController::class, 'action' => 'results'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => PublicPageController::class, 'action' => 'results'],
            $controller->resolveController('/results.php?edition=2026')
        );
    }

    public function testResolveControllerReturnsLogoutTarget(): void
    {
        $controller = new FrontController([
            '/logout' => ['controller' => AuthPageController::class, 'action' => 'logout'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => AuthPageController::class, 'action' => 'logout'],
            $controller->resolveController('/logout')
        );
    }

    public function testResolveControllerReturnsAdminCandidaturesTarget(): void
    {
        $controller = new FrontController([
            '/admin/candidatures' => ['controller' => AdminPageController::class, 'action' => 'candidatures'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => AdminPageController::class, 'action' => 'candidatures'],
            $controller->resolveController('/admin/candidatures?page=2')
        );
    }

    public function testResolveControllerReturnsAdminCategoriesTarget(): void
    {
        $controller = new FrontController([
            '/admin/categories' => ['controller' => AdminPageController::class, 'action' => 'categories'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => AdminPageController::class, 'action' => 'categories'],
            $controller->resolveController('/admin/categories?filter=all')
        );
    }

    public function testResolveControllerReturnsAdminEditCategoryTarget(): void
    {
        $controller = new FrontController([
            '/admin/categories/edit' => ['controller' => AdminPageController::class, 'action' => 'editCategory'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => AdminPageController::class, 'action' => 'editCategory'],
            $controller->resolveController('/admin/categories/edit?id=3')
        );
    }

    public function testResolveControllerReturnsAdminCandidatureViewTarget(): void
    {
        $controller = new FrontController([
            '/admin/candidatures/view' => ['controller' => AdminPageController::class, 'action' => 'viewCandidature'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => AdminPageController::class, 'action' => 'viewCandidature'],
            $controller->resolveController('/admin/candidatures/view?id=7')
        );
    }

    public function testResolveControllerReturnsAdminCandidatureProcessTarget(): void
    {
        $controller = new FrontController([
            '/admin/candidatures/process' => ['controller' => AdminPageController::class, 'action' => 'processCandidature'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => AdminPageController::class, 'action' => 'processCandidature'],
            $controller->resolveController('/admin/candidatures/process')
        );
    }

    public function testResolveControllerReturnsAdminCandidatureDeleteTarget(): void
    {
        $controller = new FrontController([
            '/admin/candidatures/delete' => ['controller' => AdminPageController::class, 'action' => 'deleteCandidature'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => AdminPageController::class, 'action' => 'deleteCandidature'],
            $controller->resolveController('/admin/candidatures/delete')
        );
    }

    public function testResolveControllerReturnsDashboardRedirectTarget(): void
    {
        $controller = new FrontController([
            '/check_dashboards' => ['controller' => DashboardRedirectController::class, 'action' => 'redirect'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => DashboardRedirectController::class, 'action' => 'redirect'],
            $controller->resolveController('/check_dashboards')
        );
    }

    public function testResolveControllerReturnsAdminDashboardTarget(): void
    {
        $controller = new FrontController([
            '/admin/dashboard' => ['controller' => AdminPageController::class, 'action' => 'dashboard'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => AdminPageController::class, 'action' => 'dashboard'],
            $controller->resolveController('/admin/dashboard')
        );
    }

    public function testResolveControllerReturnsAdminEditionsTarget(): void
    {
        $controller = new FrontController([
            '/admin/editions' => ['controller' => AdminPageController::class, 'action' => 'editions'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => AdminPageController::class, 'action' => 'editions'],
            $controller->resolveController('/admin/editions')
        );
    }

    public function testResolveControllerReturnsAdminNominationsTarget(): void
    {
        $controller = new FrontController([
            '/admin/nominations' => ['controller' => AdminPageController::class, 'action' => 'nominations'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => AdminPageController::class, 'action' => 'nominations'],
            $controller->resolveController('/admin/nominations')
        );
    }

    public function testResolveControllerReturnsAdminEditNominationTarget(): void
    {
        $controller = new FrontController([
            '/admin/nominations/edit' => ['controller' => AdminPageController::class, 'action' => 'editNomination'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => AdminPageController::class, 'action' => 'editNomination'],
            $controller->resolveController('/admin/nominations/edit?id=7')
        );
    }

    public function testResolveControllerReturnsAdminDeleteNominationTarget(): void
    {
        $controller = new FrontController([
            '/admin/nominations/delete' => ['controller' => AdminPageController::class, 'action' => 'deleteNomination'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => AdminPageController::class, 'action' => 'deleteNomination'],
            $controller->resolveController('/admin/nominations/delete')
        );
    }

    public function testResolveControllerReturnsAdminViewNominationTarget(): void
    {
        $controller = new FrontController([
            '/admin/nominations/view' => ['controller' => AdminPageController::class, 'action' => 'viewNomination'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => AdminPageController::class, 'action' => 'viewNomination'],
            $controller->resolveController('/admin/nominations/view?id=7')
        );
    }

    public function testResolveControllerReturnsUserDashboardTarget(): void
    {
        $controller = new FrontController([
            '/user/dashboard' => ['controller' => VoterPageController::class, 'action' => 'dashboard'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => VoterPageController::class, 'action' => 'dashboard'],
            $controller->resolveController('/user/dashboard')
        );
    }

    public function testResolveControllerReturnsVoteTarget(): void
    {
        $controller = new FrontController([
            '/vote' => ['controller' => VoterPageController::class, 'action' => 'vote'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => VoterPageController::class, 'action' => 'vote'],
            $controller->resolveController('/vote')
        );
    }

    public function testResolveControllerReturnsUserPasswordTarget(): void
    {
        $controller = new FrontController([
            '/user/password' => ['controller' => VoterPageController::class, 'action' => 'changePassword'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => VoterPageController::class, 'action' => 'changePassword'],
            $controller->resolveController('/user/password')
        );
    }

    public function testResolveControllerReturnsUserProfileTarget(): void
    {
        $controller = new FrontController([
            '/user/profile' => ['controller' => VoterPageController::class, 'action' => 'editProfile'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => VoterPageController::class, 'action' => 'editProfile'],
            $controller->resolveController('/user/profile')
        );
    }

    public function testResolveControllerReturnsCandidateDashboardTarget(): void
    {
        $controller = new FrontController([
            '/candidate/dashboard' => ['controller' => CandidatePageController::class, 'action' => 'dashboard'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => CandidatePageController::class, 'action' => 'dashboard'],
            $controller->resolveController('/candidate/dashboard')
        );
    }

    public function testResolveControllerReturnsCandidateAvailableCategoriesTarget(): void
    {
        $controller = new FrontController([
            '/candidate/api/available-categories' => ['controller' => CandidatePageController::class, 'action' => 'availableCategories'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => CandidatePageController::class, 'action' => 'availableCategories'],
            $controller->resolveController('/candidate/api/available-categories?user_id=4')
        );
    }

    public function testResolveControllerReturnsCandidateCategoriesTarget(): void
    {
        $controller = new FrontController([
            '/candidate/api/categories' => ['controller' => CandidatePageController::class, 'action' => 'categoriesByEdition'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => CandidatePageController::class, 'action' => 'categoriesByEdition'],
            $controller->resolveController('/candidate/api/categories?edition_id=2')
        );
    }

    public function testResolveControllerReturnsCandidateCheckDuplicateTarget(): void
    {
        $controller = new FrontController([
            '/candidate/api/check-duplicate' => ['controller' => CandidatePageController::class, 'action' => 'checkDuplicate'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => CandidatePageController::class, 'action' => 'checkDuplicate'],
            $controller->resolveController('/candidate/api/check-duplicate?category_id=2&platform=TikTok')
        );
    }

    public function testResolveControllerReturnsCandidateCheckPlatformTarget(): void
    {
        $controller = new FrontController([
            '/candidate/api/check-platform' => ['controller' => CandidatePageController::class, 'action' => 'checkPlatform'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => CandidatePageController::class, 'action' => 'checkPlatform'],
            $controller->resolveController('/candidate/api/check-platform?category_id=2&platform=TikTok')
        );
    }

    public function testResolveControllerReturnsCandidateApplicationsTarget(): void
    {
        $controller = new FrontController([
            '/candidate/candidatures' => ['controller' => CandidatePageController::class, 'action' => 'applications'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => CandidatePageController::class, 'action' => 'applications'],
            $controller->resolveController('/candidate/candidatures')
        );
    }

    public function testResolveControllerReturnsCandidateDeleteTarget(): void
    {
        $controller = new FrontController([
            '/candidate/candidatures/delete' => ['controller' => CandidatePageController::class, 'action' => 'delete'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => CandidatePageController::class, 'action' => 'delete'],
            $controller->resolveController('/candidate/candidatures/delete')
        );
    }

    public function testResolveControllerReturnsCandidateDetailTarget(): void
    {
        $controller = new FrontController([
            '/candidate/candidatures/view' => ['controller' => CandidatePageController::class, 'action' => 'detail'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => CandidatePageController::class, 'action' => 'detail'],
            $controller->resolveController('/candidate/candidatures/view?id=42')
        );
    }

    public function testResolveControllerReturnsCandidateNomineeProfileTarget(): void
    {
        $controller = new FrontController([
            '/candidate/nominee-profile' => ['controller' => CandidatePageController::class, 'action' => 'nomineeProfile'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => CandidatePageController::class, 'action' => 'nomineeProfile'],
            $controller->resolveController('/candidate/nominee-profile')
        );
    }

    public function testResolveControllerReturnsCandidateProfileTarget(): void
    {
        $controller = new FrontController([
            '/candidate/profile' => ['controller' => CandidatePageController::class, 'action' => 'profile'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => CandidatePageController::class, 'action' => 'profile'],
            $controller->resolveController('/candidate/profile')
        );
    }

    public function testResolveControllerReturnsCandidateRulesTarget(): void
    {
        $controller = new FrontController([
            '/candidate/rules' => ['controller' => CandidatePageController::class, 'action' => 'rules'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => CandidatePageController::class, 'action' => 'rules'],
            $controller->resolveController('/candidate/rules')
        );
    }

    public function testResolveControllerReturnsCandidateShareTarget(): void
    {
        $controller = new FrontController([
            '/candidate/share' => ['controller' => CandidatePageController::class, 'action' => 'share'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => CandidatePageController::class, 'action' => 'share'],
            $controller->resolveController('/candidate/share')
        );
    }

    public function testResolveControllerReturnsCandidateSubmitTarget(): void
    {
        $controller = new FrontController([
            '/candidate/submit' => ['controller' => CandidatePageController::class, 'action' => 'submit'],
        ], dirname(__DIR__, 3));

        $this->assertSame(
            ['controller' => CandidatePageController::class, 'action' => 'submit'],
            $controller->resolveController('/candidate/submit')
        );
    }
}
