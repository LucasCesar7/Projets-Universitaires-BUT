<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Edition;
use App\Services\CandidatureService;
use App\Services\CategorieService;
use App\Services\CandidatService;
use App\Services\CsrfService;
use App\Services\EditionService;

final class CandidatePageController extends Controller
{
    public function dashboard(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/database.php');

        requireRole('candidate');

        $candidatService = new CandidatService(\Database::getInstance()->getConnection());
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $userPseudonyme = (string) ($_SESSION['user_pseudonyme'] ?? 'Candidat');

        $isNominee = $candidatService->isNominee($userId);
        $nominations = [];
        $votingStatus = 'not_started';

        if ($isNominee) {
            foreach ($candidatService->getActiveNominations($userId) as $nomination) {
                $status = $candidatService->getVotingStatus($nomination);
                $nomination['dashboard_status'] = $status;
                $nomination['dashboard_status_class'] = $this->statusClass($status);
                $nomination['dashboard_status_label'] = $this->statusLabel($status);
                $nominations[] = $nomination;
            }

            if ($nominations !== []) {
                $votingStatus = (string) ($nominations[0]['dashboard_status'] ?? 'not_started');
            }
        }

        $stats = $candidatService->getCandidatStats($userId);
        $candidatures = $candidatService->getUserCandidatures($userId);
        $recentCandidatures = array_slice($candidatures, 0, 5);

        $activeEditions = [];
        try {
            foreach (array_slice($candidatService->getActiveEditionsForCandidature(), 0, 2) as $edition) {
                $activeEditions[] = [
                    'date_fin_candidatures' => $edition->getDateFinCandidatures(),
                    'id_edition' => $edition->getIdEdition(),
                    'nom' => $edition->getNom(),
                ];
            }
        } catch (\Throwable $exception) {
            $activeEditions = [];
        }

        $availableCategories = [];
        $availableCount = 0;
        try {
            foreach ($candidatService->getAvailableCategoriesForCandidature($userId) as $categoryObject) {
                $availableCategories[] = [
                    'date_debut_votes' => $categoryObject->getDateDebutVotes(),
                    'date_fin_candidatures' => $categoryObject->getDateFinCandidatures(),
                    'date_fin_votes' => $categoryObject->getDateFinVotes(),
                    'description' => $categoryObject->getDescription(),
                    'edition_nom' => $categoryObject->getEditionNom(),
                    'id_categorie' => $categoryObject->getIdCategorie(),
                    'id_edition' => $categoryObject->getIdEdition(),
                    'limite_nomines' => $categoryObject->getLimiteNomines(),
                    'nom' => $categoryObject->getNom(),
                    'plateforme_cible' => $categoryObject->getPlateformeCible(),
                ];
            }

            $availableCount = count($availableCategories);
        } catch (\Throwable $exception) {
            error_log('Available category retrieval error: ' . $exception->getMessage());
            $availableCategories = [];
            $availableCount = 0;
        }

        $successFlash = $_SESSION['success'] ?? null;
        $errorFlash = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        return $this->render('candidate/dashboard-page', [
            'aboutUrl' => appUrl('about'),
            'activeEditions' => $activeEditions,
            'availableCategories' => $availableCategories,
            'availableCount' => $availableCount,
            'candidateCssUrl' => appUrl('assets/css/candidat.css'),
            'candidateDashboardUrl' => appUrl('candidate/dashboard'),
            'candidateJsUrl' => appUrl('assets/js/candidat.js'),
            'candidatureDetailsBaseUrl' => appUrl('candidate/candidatures/view'),
            'contactUrl' => appUrl('contact'),
            'copyrightYear' => date('Y'),
            'errorFlash' => $errorFlash,
            'hasOpenSubmissionWindow' => $activeEditions !== [],
            'homeUrl' => publicRouteUrl('home'),
            'isNominee' => $isNominee,
            'logoutToken' => CsrfService::token('logout'),
            'logoutUrl' => appUrl('logout'),
            'mesCandidaturesUrl' => appUrl('candidate/candidatures'),
            'nominations' => $nominations,
            'nomineeProfileUrl' => appUrl('candidate/nominee-profile'),
            'recentCandidatures' => $recentCandidatures,
            'rulesUrl' => appUrl('candidate/rules'),
            'shareNominationUrl' => appUrl('candidate/share'),
            'stats' => $stats,
            'submitCandidatureUrl' => appUrl('candidate/submit'),
            'successFlash' => $successFlash,
            'userPseudonyme' => $userPseudonyme,
            'votingStatus' => $votingStatus,
        ]);
    }

    public function applications(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/database.php');

        requireRole('candidate');

        $candidatService = new CandidatService(\Database::getInstance()->getConnection());
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $successMessage = $_SESSION['success'] ?? null;
        $errorMessage = $_SESSION['error'] ?? null;

        unset($_SESSION['success'], $_SESSION['error']);

        return $this->render('candidate/applications-page', [
            'candidateCssUrl' => appUrl('assets/css/candidat.css'),
            'candidateDashboardUrl' => appUrl('candidate/dashboard'),
            'candidatureDetailsUrl' => appUrl('candidate/candidatures/view'),
            'candidatures' => $candidatService->getUserCandidatures($userId),
            'contactUrl' => appUrl('contact'),
            'deleteCandidatureToken' => CsrfService::token('delete_candidate_candidature'),
            'deleteCandidatureUrl' => appUrl('candidate/candidatures/delete'),
            'errorMessage' => $errorMessage,
            'hasOpenSubmissionWindow' => !empty($candidatService->getActiveEditionsForCandidature()),
            'homeUrl' => publicRouteUrl('home'),
            'logoutToken' => CsrfService::token('logout'),
            'logoutUrl' => appUrl('logout'),
            'rulesUrl' => appUrl('candidate/rules'),
            'stats' => $candidatService->getCandidatStats($userId),
            'submitCandidatureUrl' => appUrl('candidate/submit'),
            'successMessage' => $successMessage,
            'userPseudonyme' => (string) ($_SESSION['user_pseudonyme'] ?? 'Candidat'),
        ]);
    }

    public function detail(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/database.php');

        requireRole('candidate');

        $candidatureId = isset($_GET['id']) && ctype_digit((string) $_GET['id'])
            ? (int) $_GET['id']
            : 0;

        if ($candidatureId <= 0) {
            $this->redirectWithError('candidate/candidatures', 'Candidature non trouvée.');
        }

        $pdo = \Database::getInstance()->getConnection();
        $candidatureService = new CandidatureService($pdo);
        $categorieService = new CategorieService($pdo);
        $editionService = new EditionService($pdo);
        $candidature = $candidatureService->getCandidatureById($candidatureId);
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        if (!$candidature || $candidature->getIdCompte() !== $userId) {
            $this->redirectWithError('candidate/candidatures', 'Candidature non trouvée ou non autorisée.');
        }

        $categorie = $categorieService->getCategoryById($candidature->getIdCategorie());
        $edition = null;
        if ($categorie) {
            $editionData = $editionService->getEditionById($categorie->getIdEdition());
            $edition = $editionData ? new Edition($editionData) : null;
        }

        $statusColors = [
            'En attente' => 'warning',
            'Approuvée' => 'success',
            'Rejetée' => 'danger',
            'En cours' => 'info',
        ];
        $platformIcons = [
            'TikTok' => 'fab fa-tiktok',
            'Instagram' => 'fab fa-instagram',
            'YouTube' => 'fab fa-youtube',
            'X' => 'fab fa-x-twitter',
            'Facebook' => 'fab fa-facebook',
            'Twitch' => 'fab fa-twitch',
        ];

        return $this->render('candidate/candidature-details-page', [
            'candidateCssUrl' => appUrl('assets/css/candidat.css'),
            'candidateDashboardUrl' => appUrl('candidate/dashboard'),
            'candidature' => $candidature,
            'candidatureId' => $candidatureId,
            'candidatureImageUrl' => $this->toPublicAssetUrl($candidature->getImage()),
            'categorie' => $categorie,
            'dateSoumission' => date('d/m/Y à H:i', strtotime((string) $candidature->getDateSoumission())),
            'edition' => $edition,
            'isEditable' => $candidature->getStatut() === 'En attente',
            'mesCandidaturesUrl' => appUrl('candidate/candidatures'),
            'platformIcon' => $platformIcons[$candidature->getPlateforme()] ?? 'fas fa-globe',
            'statusClass' => $statusColors[$candidature->getStatut()] ?? 'secondary',
            'submitCandidatureUrl' => appUrl('candidate/submit'),
        ]);
    }

    public function delete(): void
    {
        require_once appPath('config/session.php');
        require_once appPath('config/database.php');

        requireRole('candidate');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithError('candidate/candidatures', 'Méthode non autorisée.');
        }

        if (!CsrfService::isValid($_POST['csrf_token'] ?? null, 'delete_candidate_candidature')) {
            $this->redirectWithError('candidate/candidatures', 'Jeton CSRF invalide.');
        }

        $candidatureId = isset($_POST['id']) && is_numeric((string) $_POST['id'])
            ? (int) $_POST['id']
            : 0;

        if ($candidatureId <= 0) {
            $this->redirectWithError('candidate/candidatures', 'Candidature non spécifiée.');
        }

        $candidatService = new CandidatService(\Database::getInstance()->getConnection());
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $success = $candidatService->deleteCandidature($candidatureId, $userId);

        $_SESSION[$success ? 'success' : 'error'] = $success
            ? 'Candidature supprimée avec succès.'
            : 'Erreur lors de la suppression. La candidature est peut-être déjà traitée.';

        header('Location: ' . appUrl('candidate/candidatures'));
        exit;
    }

    public function availableCategories(): void
    {
        require_once appPath('config/session.php');
        require_once appPath('config/database.php');

        header('Content-Type: application/json; charset=UTF-8');
        requireJsonRole('candidate');

        $userId = (int) ($_GET['user_id'] ?? getUserId());
        $currentUserId = (int) (getUserId() ?? 0);

        if ($userId !== $currentUserId) {
            respondJson(['error' => 'ID utilisateur invalide'], 403);
        }

        $candidatService = new CandidatService(\Database::getInstance()->getConnection());

        try {
            $categories = $candidatService->getAvailableCategoriesForCandidature($userId);

            $filteredCategories = array_filter(
                $categories,
                static function ($category) use ($candidatService, $userId): bool {
                    $categoryId = $category->getIdCategorie();
                    if ($categoryId === null) {
                        return false;
                    }

                    return !$candidatService->hasCandidatureInCategoryForPlatform(
                        $userId,
                        $categoryId,
                        $category->getPlateformeCible() ?? 'Toutes'
                    );
                }
            );

            $serializedCategories = array_map(
                static function ($category): array {
                    return [
                        'id_categorie' => $category->getIdCategorie(),
                        'nom' => $category->getNom(),
                        'description' => $category->getDescription(),
                        'image' => $category->getImage(),
                        'plateforme_cible' => $category->getPlateformeCible(),
                        'date_debut_votes' => $category->getDateDebutVotes(),
                        'date_fin_votes' => $category->getDateFinVotes(),
                        'id_edition' => $category->getIdEdition(),
                        'edition_nom' => $category->getEditionNom(),
                        'limite_nomines' => $category->getLimiteNomines(),
                    ];
                },
                array_values($filteredCategories)
            );

            respondJson([
                'success' => true,
                'categories' => $serializedCategories,
            ]);
        } catch (\Throwable $exception) {
            respondJson(['error' => 'Erreur technique: ' . $exception->getMessage()], 500);
        }
    }

    public function categoriesByEdition(): void
    {
        require_once appPath('config/session.php');
        require_once appPath('config/database.php');

        header('Content-Type: application/json; charset=UTF-8');
        requireJsonRole('candidate');

        $editionId = isset($_GET['edition_id']) ? (int) $_GET['edition_id'] : 0;
        if ($editionId <= 0) {
            respondJson(['success' => false, 'categories' => []]);
        }

        try {
            $candidatService = new CandidatService(\Database::getInstance()->getConnection());
            $categories = array_values(array_map(
                static function ($category): array {
                    return [
                        'id_categorie' => $category->getIdCategorie(),
                        'nom' => $category->getNom(),
                        'plateforme_cible' => $category->getPlateformeCible(),
                        'description' => $category->getDescription(),
                        'edition_nom' => $category->getEditionNom(),
                        'date_fin_candidatures' => $category->getDateFinCandidatures(),
                    ];
                },
                array_filter(
                    $candidatService->getAvailableCategoriesForCandidature((int) ($_SESSION['user_id'] ?? 0)),
                    static fn ($category): bool => $category->getIdEdition() === $editionId
                )
            ));

            respondJson([
                'success' => true,
                'categories' => $categories,
            ]);
        } catch (\Throwable $exception) {
            error_log('Erreur candidate categories by edition: ' . $exception->getMessage());
            respondJson([
                'success' => false,
                'message' => 'Erreur interne du serveur',
            ], 500);
        }
    }

    public function checkDuplicate(): void
    {
        require_once appPath('config/session.php');
        require_once appPath('config/database.php');

        header('Content-Type: application/json; charset=UTF-8');
        requireJsonRole('candidate');

        $categoryId = (int) ($_GET['category_id'] ?? 0);
        $platform = trim((string) ($_GET['platform'] ?? ''));

        if ($categoryId <= 0 || $platform === '') {
            respondJson(['error' => 'Paramètres invalides'], 400);
        }

        $candidatService = new CandidatService(\Database::getInstance()->getConnection());
        $hasDuplicate = $candidatService->hasCandidatureInCategoryForPlatform(
            (int) ($_SESSION['user_id'] ?? 0),
            $categoryId,
            $platform
        );

        respondJson([
            'has_duplicate' => $hasDuplicate,
            'message' => $hasDuplicate
                ? 'Vous avez déjà une candidature dans cette catégorie pour ' . $platform
                : 'Plateforme disponible',
        ]);
    }

    public function checkPlatform(): void
    {
        require_once appPath('config/session.php');
        require_once appPath('config/database.php');

        header('Content-Type: application/json; charset=UTF-8');
        requireJsonRole('candidate');

        $categoryId = (int) ($_GET['category_id'] ?? 0);
        $platform = trim((string) ($_GET['platform'] ?? ''));

        if ($categoryId <= 0 || $platform === '') {
            respondJson(['error' => 'Paramètres invalides'], 400);
        }

        $candidatService = new CandidatService(\Database::getInstance()->getConnection());
        $hasCandidature = $candidatService->hasCandidatureInCategoryForPlatform(
            (int) ($_SESSION['user_id'] ?? 0),
            $categoryId,
            $platform
        );
        $existingCandidatures = $candidatService->getCandidaturesInCategory(
            (int) ($_SESSION['user_id'] ?? 0),
            $categoryId
        );

        $usedPlatforms = array_map(
            static fn ($candidature): string => $candidature->getPlateforme(),
            $existingCandidatures
        );

        respondJson([
            'has_candidature' => $hasCandidature,
            'used_platforms' => $usedPlatforms,
            'message' => $hasCandidature
                ? 'Vous avez déjà une candidature dans cette catégorie pour ' . $platform
                : 'Vous pouvez soumettre une candidature pour ' . $platform,
        ]);
    }

    public function profile(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/database.php');
        require_once appPath('config/upload.php');

        requireRole('candidate');

        $candidatService = new CandidatService(\Database::getInstance()->getConnection());
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processProfileUpdate($candidatService, $userId);
        }

        $candidat = $candidatService->getCandidatById($userId);
        if (!$candidat) {
            $_SESSION['error'] = 'Profil non trouvé.';
            header('Location: ' . appUrl('candidate/dashboard'));
            exit;
        }

        $selectedCountry = trim((string) ($candidat['pays'] ?? ''));
        $countryOptions = ['France', 'Belgique', 'Suisse', 'Canada', 'Autre'];
        if ($selectedCountry !== '' && !in_array($selectedCountry, $countryOptions, true)) {
            array_unshift($countryOptions, $selectedCountry);
        }

        $stats = $candidatService->getCandidatStats($userId);
        $lastCandidatures = array_slice($candidatService->getUserCandidatures($userId), 0, 5);
        $profileSuccess = $_SESSION['success'] ?? null;
        $profileError = $_SESSION['error'] ?? null;

        unset($_SESSION['success'], $_SESSION['error']);

        return $this->render('candidate/profile-page', [
            'candidateCssUrl' => appUrl('assets/css/candidat.css'),
            'candidateDashboardUrl' => appUrl('candidate/dashboard'),
            'candidatePhotoUrl' => $this->toPublicAssetUrl($candidat['photo_profil'] ?? null),
            'candidat' => $candidat,
            'copyrightYear' => date('Y'),
            'countryOptions' => $countryOptions,
            'dateInscription' => date('d/m/Y', strtotime((string) ($candidat['date_creation'] ?? 'now'))),
            'lastCandidatures' => $lastCandidatures,
            'mesCandidaturesUrl' => appUrl('candidate/candidatures'),
            'profileError' => $profileError,
            'profileSuccess' => $profileSuccess,
            'selectedCountry' => $selectedCountry,
            'stats' => $stats,
            'statusColors' => [
                'En attente' => 'warning',
                'Approuvée' => 'success',
                'Rejetée' => 'danger',
            ],
            'submitCandidatureUrl' => appUrl('candidate/submit'),
            'updateProfileToken' => CsrfService::token('candidate_profile_update'),
            'updateProfileUrl' => appUrl('candidate/profile'),
        ]);
    }

    public function submit(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/database.php');

        requireRole('candidate');

        $pdo = \Database::getInstance()->getConnection();
        $candidatService = new CandidatService($pdo);
        $categorieService = new CategorieService($pdo);
        $editionService = new EditionService($pdo);
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $legacyController = new CandidatController($candidatService);
            $legacyController->soumettreCandidature();
        }

        $editId = isset($_GET['edit']) && ctype_digit((string) $_GET['edit'])
            ? (int) $_GET['edit']
            : null;
        $candidatureData = null;
        $editionIdFromCandidature = null;

        if ($editId !== null) {
            $candidatureData = $candidatService->getCandidature($editId, $userId);

            if (!$candidatureData) {
                $_SESSION['error'] = 'Candidature non trouvée ou non autorisée.';
                header('Location: ' . appUrl('candidate/candidatures'));
                exit;
            }

            if ($candidatureData->getStatut() !== 'En attente') {
                $_SESSION['error'] = 'Seules les candidatures en attente peuvent être modifiées.';
                header('Location: ' . appUrl('candidate/candidatures'));
                exit;
            }

            $currentCategorie = $categorieService->getCategoryById($candidatureData->getIdCategorie());
            $editionIdFromCandidature = $currentCategorie ? $currentCategorie->getIdEdition() : null;
        }

        $activeEditions = $candidatService->getActiveEditionsForCandidature();
        $selectableEditions = $activeEditions;

        if ($editId === null && empty($activeEditions)) {
            $_SESSION['error'] = "Aucun appel à candidatures n'est actuellement ouvert.";
            header('Location: ' . appUrl('candidate/candidatures'));
            exit;
        }

        if ($editionIdFromCandidature) {
            $hasCurrentEdition = false;

            foreach ($selectableEditions as $edition) {
                if ($edition->getIdEdition() === (int) $editionIdFromCandidature) {
                    $hasCurrentEdition = true;
                    break;
                }
            }

            if (!$hasCurrentEdition) {
                $currentEditionData = $editionService->getEditionById((int) $editionIdFromCandidature);
                if ($currentEditionData) {
                    $selectableEditions[] = new Edition($currentEditionData);
                }
            }
        }

        $categoriesByEdition = [];
        foreach ($selectableEditions as $edition) {
            $categories = array_map(
                static function ($category): array {
                    return [
                        'id_categorie' => $category->getIdCategorie(),
                        'nom' => $category->getNom(),
                        'plateforme_cible' => $category->getPlateformeCible(),
                        'description' => $category->getDescription(),
                    ];
                },
                $categoryService->getAllCategoriesByEdition($edition->getIdEdition())
            );

            if ($categories !== []) {
                $categoriesByEdition[$edition->getIdEdition()] = $categories;
            }
        }

        $currentPlateforme = $candidatureData ? (string) $candidatureData->getPlateforme() : '';
        $requestedEditionId = isset($_GET['edition']) && ctype_digit((string) $_GET['edition'])
            ? (int) $_GET['edition']
            : null;
        $requestedCategoryId = isset($_GET['categorie']) && ctype_digit((string) $_GET['categorie'])
            ? (int) $_GET['categorie']
            : null;

        if ($requestedCategoryId) {
            foreach ($categoriesByEdition as $editionId => $categories) {
                foreach ($categories as $category) {
                    if ((int) $category['id_categorie'] === $requestedCategoryId) {
                        $requestedEditionId = (int) $editionId;
                        break 2;
                    }
                }
            }
        }

        if ($requestedEditionId !== null && !isset($categoriesByEdition[$requestedEditionId])) {
            $requestedEditionId = null;
        }

        $usedPlatformsByCategory = [];
        $existingCandidatures = array_filter(
            $candidatService->getUserCandidatures($userId),
            static function (array $candidature) use ($editId): bool {
                if (in_array($candidature['statut'] ?? null, ['Rejetée', 'Refusée'], true)) {
                    return false;
                }

                if ($editId && (int) ($candidature['id_candidature'] ?? 0) === $editId) {
                    return false;
                }

                return isset($candidature['id_categorie'], $candidature['plateforme']);
            }
        );

        foreach ($existingCandidatures as $cand) {
            $categoryId = (int) $cand['id_categorie'];
            $platform = (string) $cand['plateforme'];

            if (!isset($usedPlatformsByCategory[$categoryId])) {
                $usedPlatformsByCategory[$categoryId] = [];
            }

            if (!in_array($platform, $usedPlatformsByCategory[$categoryId], true)) {
                $usedPlatformsByCategory[$categoryId][] = $platform;
            }
        }

        $defaultEditionId = null;
        if ($editionIdFromCandidature && isset($categoriesByEdition[$editionIdFromCandidature])) {
            $defaultEditionId = $editionIdFromCandidature;
        } elseif ($requestedEditionId !== null) {
            $defaultEditionId = $requestedEditionId;
        } elseif (!empty($activeEditions)) {
            $defaultEditionId = $activeEditions[0]->getIdEdition();
        }

        $currentCategoryId = $candidatureData ? $candidatureData->getIdCategorie() : null;
        if (!$candidatureData && $requestedCategoryId !== null && $requestedEditionId !== null) {
            $currentCategoryId = $requestedCategoryId;
        }

        $defaultEditionDate = null;
        if ($defaultEditionId) {
            foreach ($selectableEditions as $edition) {
                if ($edition->getIdEdition() === (int) $defaultEditionId) {
                    $defaultEditionDate = $edition->getDateFinCandidatures();
                    break;
                }
            }
        }

        $errorFlash = $_SESSION['error'] ?? null;
        $successFlash = $_SESSION['success'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        return $this->render('candidate/submit-page', [
            'candidateCssUrl' => appUrl('assets/css/candidat.css'),
            'candidateDashboardUrl' => appUrl('candidate/dashboard'),
            'candidateSubmissionToken' => CsrfService::token('candidate_submission'),
            'categoriesByEdition' => $categoriesByEdition,
            'candidatureData' => $candidatureData,
            'copyrightYear' => date('Y'),
            'currentCategoryId' => $currentCategoryId,
            'currentImageUrl' => $candidatureData && $candidatureData->getImage() ? $this->toPublicAssetUrl($candidatureData->getImage()) : null,
            'currentPlateforme' => $currentPlateforme,
            'defaultEditionDate' => $defaultEditionDate,
            'defaultEditionId' => $defaultEditionId,
            'editId' => $editId,
            'errorFlash' => $errorFlash,
            'mesCandidaturesUrl' => appUrl('candidate/candidatures'),
            'selectableEditions' => $selectableEditions,
            'successFlash' => $successFlash,
            'usedPlatformsByCategory' => $usedPlatformsByCategory,
        ]);
    }

    public function nomineeProfile(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/database.php');

        requireRole('candidate');

        $candidatService = new CandidatService(\Database::getInstance()->getConnection());
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $requestedNominationId = isset($_GET['nomination']) && ctype_digit((string) $_GET['nomination'])
            ? (int) $_GET['nomination']
            : null;
        ['nomination' => $nomination, 'nominations' => $nominations] = $this->loadNomineeContext(
            $candidatService,
            $userId,
            $requestedNominationId
        );

        $votingStatus = $candidatService->getVotingStatus($nomination);
        $socialLinks = array_values(array_filter([
            [
                'icon' => 'fab fa-instagram',
                'label' => 'Instagram',
                'url' => $nomination['url_instagram'] ?? null,
            ],
            [
                'icon' => 'fab fa-tiktok',
                'label' => 'TikTok',
                'url' => $nomination['url_tiktok'] ?? null,
            ],
            [
                'icon' => 'fab fa-youtube',
                'label' => 'YouTube',
                'url' => $nomination['url_youtube'] ?? null,
            ],
            [
                'icon' => 'fab fa-x-twitter',
                'label' => 'X',
                'url' => $nomination['url_twitter'] ?? null,
            ],
        ], static fn (array $socialLink): bool => !empty($socialLink['url'])));

        return $this->render('candidate/nominee-profile-page', [
            'candidateCssUrl' => appUrl('assets/css/candidat.css'),
            'candidateDashboardUrl' => appUrl('candidate/dashboard'),
            'candidateProfileUrl' => appUrl('candidate/profile'),
            'copyrightYear' => date('Y'),
            'logoutToken' => CsrfService::token('logout'),
            'logoutUrl' => appUrl('logout'),
            'nomination' => $nomination,
            'nominationImageUrl' => $this->toPublicAssetUrl($nomination['url_image'] ?? null),
            'nominations' => $nominations,
            'nomineeProfileUrl' => appUrl('candidate/nominee-profile'),
            'profilePhotoUrl' => $this->toPublicAssetUrl($nomination['photo_profil'] ?? null),
            'publicProfileUrl' => $this->absoluteUrl('nominee?id=' . (int) ($nomination['id_nomination'] ?? 0)),
            'resultsUrl' => appUrl('results'),
            'rulesUrl' => appUrl('candidate/rules'),
            'selectedNominationId' => (int) ($nomination['id_nomination'] ?? 0),
            'shareNominationUrl' => appUrl('candidate/share'),
            'socialLinks' => $socialLinks,
            'userPseudonyme' => (string) ($_SESSION['user_pseudonyme'] ?? 'Nominé'),
            'votingStatusClass' => $this->statusClass($votingStatus),
            'votingStatusLabel' => $this->statusLabel($votingStatus),
        ]);
    }

    public function share(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/database.php');

        requireRole('candidate');

        $candidatService = new CandidatService(\Database::getInstance()->getConnection());
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $requestedNominationId = isset($_GET['nomination']) && ctype_digit((string) $_GET['nomination'])
            ? (int) $_GET['nomination']
            : null;
        ['nomination' => $nomination, 'nominations' => $nominations] = $this->loadNomineeContext(
            $candidatService,
            $userId,
            $requestedNominationId
        );

        $votingStatus = $candidatService->getVotingStatus($nomination);
        $publicProfileUrl = $this->absoluteUrl('nominee?id=' . (int) ($nomination['id_nomination'] ?? 0));
        $shareTexts = $this->buildShareTexts($nomination, $publicProfileUrl);
        $categoryHashtag = '#' . preg_replace('/\s+/', '', (string) ($nomination['categorie_nom'] ?? 'Nomination'));

        return $this->render('candidate/share-nomination-page', [
            'candidateCssUrl' => appUrl('assets/css/candidat.css'),
            'candidateDashboardUrl' => appUrl('candidate/dashboard'),
            'copyrightYear' => date('Y'),
            'hashtags' => [
                '#SocialMediaAwards' . date('Y'),
                '#VotezPourMoi',
                '#Nomination',
                '#TikTokAwards',
                '#InstaAwards',
                '#YouTubeAwards',
                $categoryHashtag,
            ],
            'logoutToken' => CsrfService::token('logout'),
            'logoutUrl' => appUrl('logout'),
            'nomination' => $nomination,
            'nominations' => $nominations,
            'nomineeProfileUrl' => appUrl('candidate/nominee-profile'),
            'publicProfileUrl' => $publicProfileUrl,
            'selectedNominationId' => (int) ($nomination['id_nomination'] ?? 0),
            'shareCards' => [
                [
                    'buttonClass' => 'btn-outline-primary',
                    'buttonLabel' => 'Publier sur X',
                    'color' => '#1DA1F2',
                    'icon' => 'fab fa-x-twitter',
                    'label' => 'X',
                    'shareUrl' => 'https://twitter.com/intent/tweet?text=' . urlencode($shareTexts['twitter']),
                    'text' => $shareTexts['twitter'],
                ],
                [
                    'buttonClass' => null,
                    'buttonLabel' => null,
                    'color' => 'linear-gradient(45deg, #405DE6, #833AB4, #E1306C, #FD1D1D)',
                    'icon' => 'fab fa-instagram',
                    'label' => 'Instagram',
                    'shareUrl' => null,
                    'text' => $shareTexts['instagram'],
                ],
                [
                    'buttonClass' => null,
                    'buttonLabel' => null,
                    'color' => 'linear-gradient(45deg, #000000, #25F4EE, #FE2C55)',
                    'icon' => 'fab fa-tiktok',
                    'label' => 'TikTok',
                    'shareUrl' => null,
                    'text' => $shareTexts['tiktok'],
                ],
                [
                    'buttonClass' => 'btn-outline-success',
                    'buttonLabel' => 'Partager sur WhatsApp',
                    'color' => '#25D366',
                    'icon' => 'fab fa-whatsapp',
                    'label' => 'WhatsApp',
                    'shareUrl' => 'https://wa.me/?text=' . urlencode($shareTexts['whatsapp']),
                    'text' => $shareTexts['whatsapp'],
                ],
                [
                    'buttonClass' => 'btn-outline-danger',
                    'buttonLabel' => 'Ouvrir votre client mail',
                    'color' => '#EA4335',
                    'icon' => 'fas fa-envelope',
                    'label' => 'Email',
                    'shareUrl' => 'mailto:?subject=' . rawurlencode('Je suis nominé aux Social Media Awards ' . date('Y')) . '&body=' . rawurlencode($shareTexts['email']),
                    'text' => $shareTexts['email'],
                ],
            ],
            'shareNominationUrl' => appUrl('candidate/share'),
            'statusNoticeClass' => match ($votingStatus) {
                'in_progress' => 'alert-success',
                'ended' => 'alert-secondary',
                default => 'alert-warning',
            },
            'statusNoticeText' => match ($votingStatus) {
                'in_progress' => 'Les votes sont ouverts : c\'est le bon moment pour relayer votre lien de nomination auprès de votre communauté.',
                'ended' => 'Les votes sont terminés. Vous pouvez encore réutiliser ces contenus pour remercier vos soutiens et partager votre participation.',
                default => 'Les votes n\'ont pas encore commencé. Préparez vos messages et votre planning de diffusion dès maintenant.',
            },
            'statusNoticeTitle' => match ($votingStatus) {
                'in_progress' => 'Période idéale pour communiquer',
                'ended' => 'Campagne terminée',
                default => 'Préparez votre campagne',
            },
            'userPseudonyme' => (string) ($_SESSION['user_pseudonyme'] ?? 'Nominé'),
        ]);
    }

    public function rules(): string
    {
        require_once appPath('config/session.php');

        requireRole('candidate');

        return $this->render('candidate/rules-page', [
            'candidateCssUrl' => appUrl('assets/css/candidat.css'),
            'candidateDashboardUrl' => appUrl('candidate/dashboard'),
            'copyrightYear' => date('Y'),
            'logoutToken' => CsrfService::token('logout'),
            'logoutUrl' => appUrl('logout'),
            'submitCandidatureUrl' => appUrl('candidate/submit'),
            'userPseudonyme' => (string) ($_SESSION['user_pseudonyme'] ?? 'Candidat'),
        ]);
    }

    private function processProfileUpdate(CandidatService $candidatService, int $userId): void
    {
        $profileUrl = appUrl('candidate/profile');
        $publicUploadsRoot = appPath('public');
        $profileUploadDir = appPath('public/uploads/profiles');
        $canEditProfile = $candidatService->canEditProfile($userId);

        if (!$canEditProfile) {
            $_SESSION['error'] = 'Vous ne pouvez pas modifier votre profil pendant une période de votes active.';
            header('Location: ' . $profileUrl);
            exit;
        }

        $candidat = $candidatService->getCandidatById($userId);
        if (!$candidat) {
            $_SESSION['error'] = 'Profil non trouvé.';
            header('Location: ' . $profileUrl);
            exit;
        }

        $data = [
            'pseudonyme' => trim((string) ($_POST['pseudonyme'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'type_candidature' => (string) ($_POST['type_candidature'] ?? 'Créateur'),
            'nom_legal_ou_societe' => trim((string) ($_POST['nom_legal_ou_societe'] ?? '')),
            'pays' => array_key_exists('pays', $_POST) ? trim((string) $_POST['pays']) : ($candidat['pays'] ?? null),
        ];

        if (array_key_exists('genre', $_POST)) {
            $genre = trim((string) $_POST['genre']);
            $allowedGenres = ['Homme', 'Femme', 'Autre', ''];

            if (in_array($genre, $allowedGenres, true)) {
                $data['genre'] = $genre !== '' ? $genre : null;
            }
        } else {
            $data['genre'] = $candidat['genre'] ?? null;
        }

        $errors = [];

        if (!CsrfService::isValid($_POST['csrf_token'] ?? null, 'candidate_profile_update')) {
            $errors[] = 'La validation du formulaire a expiré. Veuillez réessayer.';
        }

        if ($data['pseudonyme'] === '') {
            $errors[] = 'Le pseudonyme est obligatoire.';
        } elseif (strlen($data['pseudonyme']) < 3) {
            $errors[] = 'Le pseudonyme doit contenir au moins 3 caractères.';
        } elseif (strlen($data['pseudonyme']) > 50) {
            $errors[] = 'Le pseudonyme ne peut pas dépasser 50 caractères.';
        } elseif (!$candidatService->isPseudonymeAvailable($data['pseudonyme'], $userId)) {
            $errors[] = 'Ce pseudonyme est déjà utilisé par un autre utilisateur.';
        }

        if ($data['email'] === '') {
            $errors[] = "L'email est obligatoire.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format d'email invalide.";
        } elseif (!$candidatService->isEmailAvailable($data['email'], $userId)) {
            $errors[] = 'Cet email est déjà utilisé par un autre utilisateur.';
        }

        $allowedTypes = ['Créateur', 'Marque', 'Autre'];
        if (!in_array($data['type_candidature'], $allowedTypes, true)) {
            $errors[] = 'Type de candidature invalide.';
        }

        $photoProfilPath = $candidat['photo_profil'] ?? null;
        $hasNewPhoto = !empty($_FILES['photo_profil']['name']) && (int) ($_FILES['photo_profil']['error'] ?? 0) === 0;

        if ($hasNewPhoto) {
            $allowedPhotoTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ];
            $maxSize = 5 * 1024 * 1024;
            $fileSize = (int) ($_FILES['photo_profil']['size'] ?? 0);
            $extension = uploadedImageExtension($_FILES['photo_profil'], $allowedPhotoTypes);

            if ($extension === null) {
                $errors[] = "Format d'image non supporté. Utilisez JPG, PNG, GIF ou WebP.";
            } elseif ($fileSize > $maxSize) {
                $errors[] = "L'image est trop volumineuse. Taille maximale: 5MB.";
            } else {
                if (!is_dir($profileUploadDir)) {
                    mkdir($profileUploadDir, 0755, true);
                }

                if ($photoProfilPath) {
                    $existingPhotoPath = $publicUploadsRoot . DIRECTORY_SEPARATOR . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, (string) $photoProfilPath), DIRECTORY_SEPARATOR);
                    if (file_exists($existingPhotoPath)) {
                        unlink($existingPhotoPath);
                    }
                }

                $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
                $destination = $profileUploadDir . DIRECTORY_SEPARATOR . $filename;

                if (is_uploaded_file($_FILES['photo_profil']['tmp_name']) && move_uploaded_file($_FILES['photo_profil']['tmp_name'], $destination)) {
                    $photoProfilPath = 'uploads/profiles/' . $filename;
                } else {
                    $errors[] = "Erreur lors du téléchargement de l'image.";
                }
            }
        }

        if ($errors === []) {
            $data['photo_profil'] = $photoProfilPath;
            $success = $candidatService->updateCandidat($userId, $data);

            if ($success) {
                $_SESSION['user_pseudonyme'] = $data['pseudonyme'];
                $_SESSION['user_email'] = $data['email'];
                $_SESSION['success'] = 'Profil mis à jour avec succès !';
                header('Location: ' . $profileUrl);
                exit;
            }

            $_SESSION['error'] = "Une erreur s'est produite lors de la mise à jour du profil.";
            header('Location: ' . $profileUrl);
            exit;
        }

        $_SESSION['error'] = implode('<br>', $errors);
        header('Location: ' . $profileUrl);
        exit;
    }

    private function toPublicAssetUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $normalizedPath = ltrim($path, '/');

        if (str_starts_with($normalizedPath, 'public/')) {
            return appUrl($normalizedPath);
        }

        return appUrl('public/' . $normalizedPath);
    }

    /**
     * @return array{nomination: array<string, mixed>, nominations: array<int, array<string, mixed>>}
     */
    private function loadNomineeContext(CandidatService $candidatService, int $userId, ?int $requestedNominationId = null): array
    {
        if (!$candidatService->isNominee($userId)) {
            $this->redirectWithError('candidate/dashboard', 'Vous devez être nominé pour accéder à cette page.');
        }

        $nominations = $candidatService->getActiveNominations($userId);
        if ($nominations === []) {
            $this->redirectWithError('candidate/dashboard', 'Aucune nomination active trouvée.');
        }

        $nomination = $nominations[0];
        if ($requestedNominationId !== null) {
            foreach ($nominations as $candidateNomination) {
                if ((int) ($candidateNomination['id_nomination'] ?? 0) === $requestedNominationId) {
                    $nomination = $candidateNomination;
                    break;
                }
            }

            if ((int) ($nomination['id_nomination'] ?? 0) !== $requestedNominationId) {
                $this->redirectWithError('candidate/dashboard', 'Nomination non trouvée.');
            }
        }

        $nomination += [
            'bio' => 'Candidat aux Social Media Awards',
            'categorie_nom' => 'Catégorie inconnue',
            'libelle' => 'Nomination',
            'photo_profil' => null,
            'plateforme' => 'Plateforme non renseignée',
            'pseudonyme' => $_SESSION['user_pseudonyme'] ?? 'Nominé',
            'url_image' => null,
            'url_instagram' => null,
            'url_tiktok' => null,
            'url_twitter' => null,
            'url_youtube' => null,
        ];

        return [
            'nomination' => $nomination,
            'nominations' => $nominations,
        ];
    }

    /**
     * @return array{twitter: string, instagram: string, tiktok: string, whatsapp: string, email: string}
     */
    private function buildShareTexts(array $nomination, string $publicProfileUrl): array
    {
        $awardYear = date('Y');
        $candidateName = (string) ($nomination['pseudonyme'] ?? 'Votre nominé');
        $categoryName = (string) ($nomination['categorie_nom'] ?? 'sa catégorie');

        return [
            'twitter' => "Votez pour moi aux Social Media Awards {$awardYear} dans la catégorie \"{$categoryName}\" !\n\n{$publicProfileUrl}\n\n#SocialMediaAwards{$awardYear} #VotezPourMoi",
            'instagram' => "Je suis nominé aux Social Media Awards {$awardYear} dans la catégorie \"{$categoryName}\".\n\nRetrouvez mon profil public et soutenez-moi ici : {$publicProfileUrl}\n\n#SocialMediaAwards{$awardYear} #Nomination #Vote",
            'tiktok' => "Je suis nominé aux Social Media Awards {$awardYear} !\nDécouvrez ma nomination ici : {$publicProfileUrl}\n\n#SocialMediaAwards{$awardYear} #Nomination",
            'whatsapp' => "Bonjour ! Je suis nominé aux Social Media Awards {$awardYear} dans la catégorie \"{$categoryName}\".\n\nSi vous voulez me soutenir, voici mon profil public : {$publicProfileUrl}\n\nMerci beaucoup !",
            'email' => "Bonjour,\n\nJe suis nominé aux Social Media Awards {$awardYear} dans la catégorie \"{$categoryName}\".\n\nSi vous souhaitez découvrir ma nomination et me soutenir, voici mon profil public : {$publicProfileUrl}\n\nMerci pour votre aide !\n\n{$candidateName}",
        ];
    }

    private function absoluteUrl(string $path): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');

        return $scheme . '://' . $host . appUrl($path);
    }

    private function redirectWithError(string $route, string $message): never
    {
        $_SESSION['error'] = $message;
        header('Location: ' . appUrl($route));
        exit;
    }

    private function statusClass(string $status): string
    {
        return match ($status) {
            'in_progress' => 'status-active',
            'ended' => 'status-ended',
            default => 'status-pending',
        };
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'in_progress' => 'Votes en cours',
            'ended' => 'Votes terminés',
            default => 'Votes non commencés',
        };
    }
}