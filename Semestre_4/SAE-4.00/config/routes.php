<?php

declare(strict_types=1);

use App\Controllers\AuthPageController;
use App\Controllers\AdminPageController;
use App\Controllers\CandidatePageController;
use App\Controllers\DashboardRedirectController;
use App\Controllers\PublicPageController;
use App\Controllers\VoterPageController;
use App\Controllers\UserController;

$routes = [
    '/' => ['controller' => PublicPageController::class, 'action' => 'home'],
    '/index.php' => ['controller' => PublicPageController::class, 'action' => 'home'],
    '/about' => ['controller' => PublicPageController::class, 'action' => 'about'],
    '/about.php' => ['controller' => PublicPageController::class, 'action' => 'about'],
    '/categories' => ['controller' => PublicPageController::class, 'action' => 'categories'],
    '/categories.php' => ['controller' => PublicPageController::class, 'action' => 'categories'],
    '/cgu' => ['controller' => PublicPageController::class, 'action' => 'cgu'],
    '/cgu.php' => ['controller' => PublicPageController::class, 'action' => 'cgu'],
    '/contact' => ['controller' => PublicPageController::class, 'action' => 'contact'],
    '/contact.php' => ['controller' => PublicPageController::class, 'action' => 'contact'],
    '/faq' => ['controller' => PublicPageController::class, 'action' => 'faq'],
    '/faq.php' => ['controller' => PublicPageController::class, 'action' => 'faq'],
    '/check_dashboards' => ['controller' => DashboardRedirectController::class, 'action' => 'redirect'],
    '/check_dashboards.php' => ['controller' => DashboardRedirectController::class, 'action' => 'redirect'],
    '/inscription' => ['controller' => AuthPageController::class, 'action' => 'register'],
    '/inscription.php' => ['controller' => AuthPageController::class, 'action' => 'register'],
    '/login' => ['controller' => AuthPageController::class, 'action' => 'login'],
    '/logout' => ['controller' => AuthPageController::class, 'action' => 'logout'],
    '/logout.php' => ['controller' => AuthPageController::class, 'action' => 'logout'],
    '/nominee' => ['controller' => PublicPageController::class, 'action' => 'nominee'],
    '/nominee.php' => ['controller' => PublicPageController::class, 'action' => 'nominee'],
    '/nominees' => ['controller' => PublicPageController::class, 'action' => 'nominees'],
    '/nominees.php' => ['controller' => PublicPageController::class, 'action' => 'nominees'],
    '/privacy' => ['controller' => PublicPageController::class, 'action' => 'privacy'],
    '/privacy.php' => ['controller' => PublicPageController::class, 'action' => 'privacy'],
    '/results' => ['controller' => PublicPageController::class, 'action' => 'results'],
    '/results.php' => ['controller' => PublicPageController::class, 'action' => 'results'],
    '/admin' => ['controller' => AdminPageController::class, 'action' => 'dashboard'],
    '/admin/categories' => ['controller' => AdminPageController::class, 'action' => 'categories'],
    '/admin/categories/create' => ['controller' => AdminPageController::class, 'action' => 'createCategory'],
    '/admin/categories/edit' => ['controller' => AdminPageController::class, 'action' => 'editCategory'],
    '/admin/candidatures' => ['controller' => AdminPageController::class, 'action' => 'candidatures'],
    '/admin/candidatures/delete' => ['controller' => AdminPageController::class, 'action' => 'deleteCandidature'],
    '/admin/candidatures/process' => ['controller' => AdminPageController::class, 'action' => 'processCandidature'],
    '/admin/candidatures/view' => ['controller' => AdminPageController::class, 'action' => 'viewCandidature'],
    '/admin/dashboard' => ['controller' => AdminPageController::class, 'action' => 'dashboard'],
    '/admin/editions' => ['controller' => AdminPageController::class, 'action' => 'editions'],
    '/admin/editions/create' => ['controller' => AdminPageController::class, 'action' => 'createEdition'],
    '/admin/editions/edit' => ['controller' => AdminPageController::class, 'action' => 'editEdition'],
    '/admin/nominations/delete' => ['controller' => AdminPageController::class, 'action' => 'deleteNomination'],
    '/admin/nominations/edit' => ['controller' => AdminPageController::class, 'action' => 'editNomination'],
    '/admin/nominations' => ['controller' => AdminPageController::class, 'action' => 'nominations'],
    '/admin/nominations/view' => ['controller' => AdminPageController::class, 'action' => 'viewNomination'],
    '/candidate/dashboard' => ['controller' => CandidatePageController::class, 'action' => 'dashboard'],
    '/candidate/api/available-categories' => ['controller' => CandidatePageController::class, 'action' => 'availableCategories'],
    '/candidate/api/categories' => ['controller' => CandidatePageController::class, 'action' => 'categoriesByEdition'],
    '/candidate/api/check-duplicate' => ['controller' => CandidatePageController::class, 'action' => 'checkDuplicate'],
    '/candidate/api/check-platform' => ['controller' => CandidatePageController::class, 'action' => 'checkPlatform'],
    '/candidate/candidatures' => ['controller' => CandidatePageController::class, 'action' => 'applications'],
    '/candidate/candidatures/delete' => ['controller' => CandidatePageController::class, 'action' => 'delete'],
    '/candidate/candidatures/view' => ['controller' => CandidatePageController::class, 'action' => 'detail'],
    '/candidate/nominee-profile' => ['controller' => CandidatePageController::class, 'action' => 'nomineeProfile'],
    '/candidate/profile' => ['controller' => CandidatePageController::class, 'action' => 'profile'],
    '/candidate/rules' => ['controller' => CandidatePageController::class, 'action' => 'rules'],
    '/candidate/share' => ['controller' => CandidatePageController::class, 'action' => 'share'],
    '/candidate/submit' => ['controller' => CandidatePageController::class, 'action' => 'submit'],
    '/user/dashboard' => ['controller' => VoterPageController::class, 'action' => 'dashboard'],
    '/vote' => ['controller' => VoterPageController::class, 'action' => 'vote'],
    '/user/profile' => ['controller' => VoterPageController::class, 'action' => 'editProfile'],
    '/user/password' => ['controller' => VoterPageController::class, 'action' => 'changePassword'],
    '/check-session' => ['controller' => AuthPageController::class, 'action' => 'checkSession'],
    '/user/delete' => ['controller' => UserController::class, 'action' => 'handleDeleteAccount'],
];

return $routes;
