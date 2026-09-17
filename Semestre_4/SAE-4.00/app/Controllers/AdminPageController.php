<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\CandidatureService;
use App\Services\CategorieService;
use App\Services\CsrfService;
use App\Services\EditionService;
use App\Services\NominationService;

final class AdminPageController extends Controller
{
    public function dashboard(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/permissions.php');

        requireAdmin();

        return $this->render('admin/dashboard-page', [
            'addEditionUrl' => appUrl('admin/editions/create'),
            'adminCssUrl' => appUrl('assets/css/admin.css'),
            'adminDashboardJsUrl' => appUrl('assets/js/admin-dashboard.js'),
            'manageCandidaturesUrl' => appUrl('admin/candidatures'),
            'manageCategoriesUrl' => appUrl('admin/categories'),
            'manageEditionsUrl' => appUrl('admin/editions'),
            'manageNominationsUrl' => appUrl('admin/nominations'),
            'userNom' => (string) ($_SESSION['user_nom'] ?? 'Administrateur'),
        ]);
    }

    public function categories(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/permissions.php');
        require_once appPath('config/database.php');

        requireAdmin();

        $manageCategoriesUrl = appUrl('admin/categories');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category_id'])) {
            if (!CsrfService::isValid($_POST['csrf_token'] ?? null, 'delete_category')) {
                header('Location: ' . $manageCategoriesUrl . '?error=1');
                exit;
            }

            $categoryId = (int) ($_POST['delete_category_id'] ?? 0);
            $pdo = \Database::getInstance()->getConnection();
            $categorieService = new CategorieService($pdo);
            $success = $categoryId > 0 && $categorieService->deleteCategorie($categoryId);

            header('Location: ' . $manageCategoriesUrl . '?' . ($success ? 'success=1' : 'error=1'));
            exit;
        }

        $pdo = \Database::getInstance()->getConnection();
        $categorieService = new CategorieService($pdo);
        $editionService = new EditionService($pdo);

        return $this->render('admin/categories/manage-categories-page', [
            'addCategoryUrl' => appUrl('admin/categories/create'),
            'adminCategoriesCssUrl' => appUrl('assets/css/admin-categories.css'),
            'adminCategoriesJsUrl' => appUrl('assets/js/admin-categories.js'),
            'categories' => $categorieService->getAllCategories(),
            'deleteCategoryToken' => CsrfService::token('delete_category'),
            'editCategoryBaseUrl' => appUrl('admin/categories/edit'),
            'editions' => $editionService->getAllEditions(),
            'errorCode' => isset($_GET['error']) ? (string) $_GET['error'] : null,
            'manageCategoriesUrl' => $manageCategoriesUrl,
            'successCode' => isset($_GET['success']) ? (string) $_GET['success'] : null,
        ]);
    }

    public function editCategory(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/permissions.php');
        require_once appPath('config/database.php');

        requireAdmin();

        $manageCategoriesUrl = appUrl('admin/categories');
        $categoryId = isset($_GET['id']) && ctype_digit((string) $_GET['id'])
            ? (int) $_GET['id']
            : 0;

        if ($categoryId <= 0) {
            header('Location: ' . $manageCategoriesUrl);
            exit;
        }

        $pdo = \Database::getInstance()->getConnection();
        $categorieService = new CategorieService($pdo);
        $editionService = new EditionService($pdo);
        $categorie = $categorieService->getCategoryById($categoryId);

        if (!$categorie) {
            header('Location: ' . $manageCategoriesUrl);
            exit;
        }

        $errorMessage = '';
        $formData = [
            'nom' => $_POST['nom'] ?? $categorie->getNom(),
            'description' => $_POST['description'] ?? $categorie->getDescription(),
            'plateforme_cible' => $_POST['plateforme_cible'] ?? $categorie->getPlateformeCible(),
            'date_debut_votes' => $_POST['date_debut_votes'] ?? $categorie->getDateDebutVotes(),
            'date_fin_votes' => $_POST['date_fin_votes'] ?? $categorie->getDateFinVotes(),
            'id_edition' => $_POST['id_edition'] ?? $categorie->getIdEdition(),
            'limite_nomines' => $_POST['limite_nomines'] ?? $categorie->getLimiteNomines(),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => trim((string) ($_POST['nom'] ?? '')),
                'description' => trim((string) ($_POST['description'] ?? '')),
                'plateforme_cible' => (string) ($_POST['plateforme_cible'] ?? 'Toutes'),
                'date_debut_votes' => !empty($_POST['date_debut_votes']) ? (string) $_POST['date_debut_votes'] : null,
                'date_fin_votes' => !empty($_POST['date_fin_votes']) ? (string) $_POST['date_fin_votes'] : null,
                'id_edition' => (int) ($_POST['id_edition'] ?? 0),
                'limite_nomines' => (int) ($_POST['limite_nomines'] ?? 10),
            ];
            $validationErrors = [];

            if (!CsrfService::isValid($_POST['csrf_token'] ?? null, 'update_category')) {
                $validationErrors[] = 'La validation du formulaire a expiré. Veuillez réessayer.';
            }
            if ($data['nom'] === '') {
                $validationErrors[] = 'Le nom de la catégorie est requis.';
            } elseif (strlen($data['nom']) > 100) {
                $validationErrors[] = 'Le nom ne doit pas dépasser 100 caractères.';
            }
            if (trim((string) $data['description']) === '') {
                $validationErrors[] = 'La description est requise.';
            } elseif (strlen((string) $data['description']) > 2000) {
                $validationErrors[] = 'La description ne doit pas dépasser 2000 caractères.';
            }
            if ($data['id_edition'] <= 0) {
                $validationErrors[] = 'Veuillez sélectionner une édition.';
            }

            if (!empty($data['date_debut_votes']) && !empty($data['date_fin_votes'])) {
                $startDate = strtotime((string) $data['date_debut_votes']);
                $endDate = strtotime((string) $data['date_fin_votes']);

                if ($startDate !== false && $endDate !== false && $startDate >= $endDate) {
                    $validationErrors[] = 'La date de début doit être avant la date de fin.';
                }
            }

            if ($validationErrors === []) {
                $success = $categoryService->updateCategory(
                    $categoryId,
                    $data,
                    $_FILES['image'] ?? null,
                    isset($_POST['remove_image']) && $_POST['remove_image'] === '1'
                );

                if ($success) {
                    header('Location: ' . $manageCategoriesUrl . '?success=1');
                    exit;
                }

                $errorMessage = 'Une erreur est survenue lors de la mise à jour de la catégorie.';
            } else {
                $errorMessage = implode('<br>', $validationErrors);
            }
        }

        return $this->render('admin/categories/edit-category-page', [
            'adminCategoryFormCssUrl' => appUrl('assets/css/admin-add-categorie.css'),
            'adminCategoryEditJsUrl' => appUrl('assets/js/admin-categorie-edit.js'),
            'categorie' => $categorie,
            'categoryId' => $categoryId,
            'deleteCategoryToken' => CsrfService::token('delete_category'),
            'editions' => $editionService->getAllEditions(),
            'errorMessage' => $errorMessage,
            'formActionUrl' => appUrl('admin/categories/edit') . '?id=' . $categoryId,
            'formData' => $formData,
            'manageCategoriesUrl' => $manageCategoriesUrl,
            'updateCategoryToken' => CsrfService::token('update_category'),
        ]);
    }

    public function editions(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/permissions.php');
        require_once appPath('config/database.php');

        requireAdmin();

        $manageEditionsUrl = appUrl('admin/editions');
        $pdo = \Database::getInstance()->getConnection();
        $editionService = new EditionService($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_edition_id'])) {
            if (!CsrfService::isValid($_POST['csrf_token'] ?? null, 'delete_edition')) {
                header('Location: ' . $manageEditionsUrl . '?error=1');
                exit;
            }

            $editionId = (int) ($_POST['delete_edition_id'] ?? 0);
            $success = $editionId > 0 && $editionService->deleteEdition($editionId);

            header('Location: ' . $manageEditionsUrl . '?' . ($success ? 'success=1' : 'error=1'));
            exit;
        }

        $editionService->updateAllEditionStatus();

        return $this->render('admin/editions/manage-editions-page', [
            'addEditionUrl' => appUrl('admin/editions/create'),
            'adminEditionsCssUrl' => appUrl('assets/css/admin-editions.css'),
            'adminEditionsJsUrl' => appUrl('assets/js/admin-editions.js'),
            'deleteEditionToken' => CsrfService::token('delete_edition'),
            'editEditionBaseUrl' => appUrl('admin/editions/edit'),
            'editions' => $editionService->getAllEditions(),
            'errorCode' => isset($_GET['error']) ? (string) $_GET['error'] : null,
            'manageEditionsUrl' => $manageEditionsUrl,
            'successCode' => isset($_GET['success']) ? (string) $_GET['success'] : null,
        ]);
    }

    public function createCategory(): string
    {
        return $this->render('admin/categories/ajouter-categorie');
    }

    public function createEdition(): string
    {
        return $this->render('admin/editions/ajouter-edition');
    }

    public function editEdition(): string
    {
        return $this->render('admin/editions/modifier-edition');
    }

    public function candidatures(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/permissions.php');
        require_once appPath('config/database.php');

        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_candidature_id'])) {
            $this->deleteCandidature();

            return '';
        }

        $pdo = \Database::getInstance()->getConnection();
        $candidatureService = new CandidatureService($pdo);
        $categorieService = new CategorieService($pdo);
        $perPage = 20;
        $currentPage = max(1, (int) ($_GET['page'] ?? 1));
        $totalCandidatures = $candidatureService->countAllCandidatures();
        $totalPages = max(1, (int) ceil($totalCandidatures / $perPage));
        $currentPage = min($currentPage, $totalPages);
        $successFlash = $_SESSION['success'] ?? null;
        $errorFlash = $_SESSION['error'] ?? null;

        unset($_SESSION['success'], $_SESSION['error']);

        return $this->render('admin/candidatures/manage-candidature-page', [
            'adminCandidaturesCssUrl' => appUrl('assets/css/admin-candidatures.css'),
            'adminCandidaturesJsUrl' => appUrl('assets/js/admin-candidatures.js'),
            'adminDashboardUrl' => appUrl('admin/dashboard'),
            'candidatures' => $candidatureService->getCandidaturesPaginated($currentPage, $perPage),
            'categories' => $categorieService->getAllCategories(),
            'currentPage' => $currentPage,
            'deleteCandidatureToken' => CsrfService::token('delete_candidature'),
            'deleteCandidatureUrl' => appUrl('admin/candidatures/delete'),
            'errorFlash' => $errorFlash,
            'manageCandidaturesUrl' => appUrl('admin/candidatures'),
            'processCandidatureToken' => CsrfService::token('process_candidature'),
            'processCandidatureUrl' => appUrl('admin/candidatures/process'),
            'stats' => $candidatureService->getStatusStats(),
            'successFlash' => $successFlash,
            'totalCandidatures' => $totalCandidatures,
            'totalPages' => $totalPages,
            'viewCandidatureBaseUrl' => appUrl('admin/candidatures/view'),
        ]);
    }

    public function viewCandidature(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/permissions.php');
        require_once appPath('config/database.php');

        requireAdmin();

        $candidatureId = isset($_GET['id']) && ctype_digit((string) $_GET['id'])
            ? (int) $_GET['id']
            : 0;

        if ($candidatureId <= 0) {
            $this->redirectWithError('admin/candidatures', 'ID de candidature invalide.');
        }

        $pdo = \Database::getInstance()->getConnection();
        $candidatureService = new CandidatureService($pdo);
        $candidature = $candidatureService->getCandidatureById($candidatureId);

        if (!$candidature) {
            $this->redirectWithError('admin/candidatures', 'Candidature introuvable.');
        }

        $successFlash = $_SESSION['success'] ?? null;
        $errorFlash = $_SESSION['error'] ?? null;

        unset($_SESSION['success'], $_SESSION['error']);

        return $this->render('admin/candidatures/view-candidature-page', [
            'adminCandidaturesCssUrl' => appUrl('assets/css/admin-candidatures.css'),
            'adminCandidaturesJsUrl' => appUrl('assets/js/admin-candidatures.js'),
            'adminDashboardUrl' => appUrl('admin/dashboard'),
            'candidature' => $candidature,
            'candidatureId' => $candidatureId,
            'deleteCandidatureToken' => CsrfService::token('delete_candidature'),
            'deleteCandidatureUrl' => appUrl('admin/candidatures/delete'),
            'errorFlash' => $errorFlash,
            'imageUrl' => $this->resolveCandidatureImageUrl($candidature->getImage(), appUrl('assets/images/default-image.jpg')),
            'manageCandidaturesUrl' => appUrl('admin/candidatures'),
            'processCandidatureToken' => CsrfService::token('process_candidature'),
            'processCandidatureUrl' => appUrl('admin/candidatures/process'),
            'successFlash' => $successFlash,
        ]);
    }

    public function processCandidature(): void
    {
        require_once appPath('config/session.php');
        require_once appPath('config/permissions.php');
        require_once appPath('config/database.php');

        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithError('admin/candidatures', 'Méthode non autorisée.');
        }

        $action = (string) ($_POST['action'] ?? '');
        $candidatureId = (int) ($_POST['id'] ?? 0);
        $comment = trim((string) ($_POST['comment'] ?? ''));

        if (!CsrfService::isValid($_POST['csrf_token'] ?? null, 'process_candidature')) {
            if ($candidatureId > 0) {
                $this->redirectToCandidatureView($candidatureId, 'error', 'Jeton CSRF invalide.');
            }

            $this->redirectWithError('admin/candidatures', 'Jeton CSRF invalide.');
        }

        if ($candidatureId <= 0 || !in_array($action, ['approve', 'reject'], true)) {
            if ($candidatureId > 0) {
                $this->redirectToCandidatureView($candidatureId, 'error', 'Action de traitement invalide.');
            }

            $this->redirectWithError('admin/candidatures', 'Action de traitement invalide.');
        }

        $pdo = \Database::getInstance()->getConnection();
        $candidatureService = new CandidatureService($pdo);
        $candidature = $candidatureService->getCandidatureById($candidatureId);

        if (!$candidature) {
            $this->redirectWithError('admin/candidatures', 'Candidature introuvable.');
        }

        if ($candidature->getStatut() !== 'En attente') {
            $this->redirectToCandidatureView($candidatureId, 'error', 'Cette candidature a déjà été traitée.');
        }

        try {
            $adminId = (int) ($_SESSION['admin_id'] ?? $_SESSION['user_id'] ?? 0);
            $pdo->beginTransaction();

            if ($action === 'approve') {
                $updateCandidature = $pdo->prepare("UPDATE candidature SET statut = 'Approuvée' WHERE id_candidature = :id");
                $updateCandidature->execute(['id' => $candidatureId]);

                $checkNomination = $pdo->prepare('SELECT id_nomination FROM nomination WHERE id_candidature = :id');
                $checkNomination->execute(['id' => $candidatureId]);

                if (!$checkNomination->fetch()) {
                    $insertNomination = $pdo->prepare(
                        "INSERT INTO nomination (
                            libelle,
                            plateforme,
                            url_content,
                            url_image,
                            argumentaire,
                            id_candidature,
                            id_categorie,
                            id_compte,
                            id_admin,
                            date_approbation
                        ) VALUES (
                            :libelle,
                            :plateforme,
                            :url_content,
                            :url_image,
                            :argumentaire,
                            :id_candidature,
                            :id_categorie,
                            :id_compte,
                            :id_admin,
                            NOW()
                        )"
                    );
                    $insertNomination->execute([
                        'argumentaire' => $candidature->getArgumentaire() . ($comment !== '' ? "\n\nNote admin : $comment" : ''),
                        'id_admin' => $adminId,
                        'id_candidature' => $candidatureId,
                        'id_categorie' => $candidature->getIdCategorie(),
                        'id_compte' => $candidature->getIdCompte(),
                        'libelle' => $candidature->getLibelle(),
                        'plateforme' => $candidature->getPlateforme(),
                        'url_content' => $candidature->getUrlContenu(),
                        'url_image' => $candidature->getImage(),
                    ]);
                }

                $updateCandidate = $pdo->prepare('UPDATE candidat SET est_nomine = 1 WHERE id_compte = :id_compte');
                $updateCandidate->execute(['id_compte' => $candidature->getIdCompte()]);

                $pdo->commit();

                $this->redirectToCandidatureView($candidatureId, 'success', 'Candidature approuvée et transformée en nomination.');
            }

            $updateCandidature = $pdo->prepare("UPDATE candidature SET statut = 'Rejetée' WHERE id_candidature = :id");
            $updateCandidature->execute(['id' => $candidatureId]);

            if ($comment !== '') {
                $insertNote = $pdo->prepare(
                    'INSERT INTO candidature_notes (id_candidature, note, id_admin, date_note) VALUES (:id, :note, :id_admin, NOW())'
                );
                $insertNote->execute([
                    'id' => $candidatureId,
                    'id_admin' => $adminId,
                    'note' => $comment,
                ]);
            }

            $pdo->commit();

            $this->redirectToCandidatureView($candidatureId, 'success', 'Candidature rejetée.');
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log('Erreur process candidatures admin: ' . $exception->getMessage());
            $this->redirectToCandidatureView($candidatureId, 'error', 'Erreur lors du traitement de la candidature.');
        }
    }

    public function deleteCandidature(): void
    {
        require_once appPath('config/session.php');
        require_once appPath('config/permissions.php');
        require_once appPath('config/database.php');

        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithError('admin/candidatures', 'Méthode non autorisée.');
        }

        $candidatureId = isset($_POST['delete_candidature_id'])
            ? (int) $_POST['delete_candidature_id']
            : (int) ($_POST['id'] ?? 0);
        $returnToDetail = ($_POST['return_to_detail'] ?? '') === '1';

        if (!CsrfService::isValid($_POST['csrf_token'] ?? null, 'delete_candidature')) {
            if ($returnToDetail && $candidatureId > 0) {
                $this->redirectToCandidatureView($candidatureId, 'error', 'Jeton CSRF invalide.');
            }

            $this->redirectWithError('admin/candidatures', 'Jeton CSRF invalide.');
        }

        if ($candidatureId <= 0) {
            $this->redirectWithError('admin/candidatures', 'Candidature introuvable.');
        }

        $pdo = \Database::getInstance()->getConnection();
        $candidatureService = new CandidatureService($pdo);

        if (!$candidatureService->getCandidatureById($candidatureId)) {
            $this->redirectWithError('admin/candidatures', 'Candidature introuvable.');
        }

        if ($candidatureService->deleteCandidature($candidatureId)) {
            $_SESSION['success'] = 'Candidature supprimée avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression de la candidature.';
        }

        header('Location: ' . appUrl('admin/candidatures'));
        exit;
    }

    public function nominations(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/permissions.php');
        require_once appPath('config/database.php');

        requireAdmin();

        $pdo = \Database::getInstance()->getConnection();
        $nominationService = new NominationService($pdo);
        $perPage = 20;
        $currentPage = max(1, (int) ($_GET['page'] ?? 1));
        $totalNominations = $nominationService->countAllNominations();
        $totalPages = max(1, (int) ceil($totalNominations / $perPage));
        $currentPage = min($currentPage, $totalPages);
        $successFlash = $_SESSION['success'] ?? null;
        $errorFlash = $_SESSION['error'] ?? null;

        unset($_SESSION['success'], $_SESSION['error']);

        return $this->render('admin/nominations-page', [
            'adminDashboardUrl' => appUrl('admin/dashboard'),
            'adminNominationsCssUrl' => appUrl('assets/css/admin-nominations.css'),
            'currentPage' => $currentPage,
            'defaultAvatarUrl' => appUrl('assets/images/logo.png'),
            'deleteNominationToken' => CsrfService::token('delete_nomination'),
            'deleteNominationUrl' => appUrl('admin/nominations/delete'),
            'errorFlash' => $errorFlash,
            'manageCandidaturesUrl' => appUrl('admin/candidatures'),
            'nominations' => $nominationService->getNominationsPaginated($currentPage, $perPage),
            'totalNominations' => $totalNominations,
            'totalPages' => $totalPages,
            'successFlash' => $successFlash,
            'viewNominationBaseUrl' => appUrl('admin/nominations/view'),
        ]);
    }

    public function viewNomination(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/permissions.php');
        require_once appPath('config/database.php');

        requireAdmin();

        $nominationId = isset($_GET['id']) && ctype_digit((string) $_GET['id'])
            ? (int) $_GET['id']
            : 0;

        if ($nominationId <= 0) {
            $this->redirectWithError('admin/nominations', 'ID de nomination invalide.');
        }

        $pdo = \Database::getInstance()->getConnection();
        $nominationService = new NominationService($pdo);
        $nomination = $nominationService->getNominationById($nominationId);

        if (!$nomination) {
            $this->redirectWithError('admin/nominations', 'Nomination non trouvée.');
        }

        $categorieService = new CategorieService($pdo);
        $categorie = $categorieService->getCategoryById($nomination->getIdCategorie());
        $voteCount = $nominationService->countVotesForNomination($nominationId);

        $submittedBy = null;
        if ($nomination->getIdCompte()) {
            $submittedByStmt = $pdo->prepare('SELECT pseudonyme, email FROM compte WHERE id_compte = ?');
            $submittedByStmt->execute([$nomination->getIdCompte()]);
            $submittedBy = $submittedByStmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        }

        $approvedBy = null;
        if ($nomination->getIdAdmin()) {
            $approvedByStmt = $pdo->prepare('SELECT pseudonyme FROM compte WHERE id_compte = ?');
            $approvedByStmt->execute([$nomination->getIdAdmin()]);
            $approvedBy = $approvedByStmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        }

        $successFlash = $_SESSION['success'] ?? null;
        $errorFlash = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        return $this->render('admin/nominations/view-nomination-page', [
            'adminDashboardUrl' => appUrl('admin/dashboard'),
            'adminNominationsCssUrl' => appUrl('assets/css/admin-nominations.css'),
            'approvedBy' => $approvedBy,
            'approvalDate' => $nomination->getDateApprobation()
                ? date('d/m/Y H:i', strtotime((string) $nomination->getDateApprobation()))
                : 'Non approuvé',
            'argumentLength' => strlen((string) $nomination->getArgumentaire()),
            'categoryName' => $categorie ? $categorie->getNom() : 'Catégorie #' . $nomination->getIdCategorie(),
            'deleteNominationToken' => CsrfService::token('delete_nomination'),
            'deleteNominationUrl' => appUrl('admin/nominations/delete'),
            'editNominationBaseUrl' => appUrl('admin/nominations/edit'),
            'errorFlash' => $errorFlash,
            'imageUrl' => $this->resolveNominationImageUrl($nomination->getUrlImage(), appUrl('assets/images/logo.png')),
            'manageNominationsUrl' => appUrl('admin/nominations'),
            'nomination' => $nomination,
            'nominationId' => $nominationId,
            'platformIcon' => $this->resolvePlatformIcon((string) $nomination->getPlateforme()),
            'publicNominationUrl' => appUrl('nominee') . '?id=' . $nominationId,
            'submittedBy' => $submittedBy,
            'successFlash' => $successFlash,
            'voteCount' => $voteCount,
        ]);
    }

    public function editNomination(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/permissions.php');
        require_once appPath('config/database.php');

        requireAdmin();

        $nominationId = isset($_GET['id']) && ctype_digit((string) $_GET['id'])
            ? (int) $_GET['id']
            : 0;

        if ($nominationId <= 0) {
            $this->redirectWithError('admin/nominations', 'ID de nomination invalide.');
        }

        $pdo = \Database::getInstance()->getConnection();
        $nominationService = new NominationService($pdo);
        $nominationController = new NominationController($pdo, $nominationService);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nominationController->update($nominationId);
        }

        $nomination = $nominationService->getNominationById($nominationId);

        if (!$nomination) {
            $this->redirectWithError('admin/nominations', 'Nomination non trouvée.');
        }

        $currentCategoryName = 'Catégorie #' . $nomination->getIdCategorie();
        foreach ($nominationService->getAllCategories() as $categorie) {
            if ((int) ($categorie['id_categorie'] ?? 0) === $nomination->getIdCategorie()) {
                $currentCategoryName = (string) ($categorie['nom'] ?? $currentCategoryName);
                break;
            }
        }

        $successFlash = $_SESSION['success'] ?? null;
        $errorFlash = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        return $this->render('admin/nominations/edit-nomination-page', [
            'adminDashboardUrl' => appUrl('admin/dashboard'),
            'adminNominationsCssUrl' => appUrl('assets/css/admin-nominations.css'),
            'currentCategoryName' => $currentCategoryName,
            'errorFlash' => $errorFlash,
            'imageUrl' => $this->resolveNominationImageUrl($nomination->getUrlImage(), appUrl('assets/images/logo.png')),
            'manageNominationsUrl' => appUrl('admin/nominations'),
            'nomination' => $nomination,
            'nominationId' => $nominationId,
            'platforms' => ['TikTok', 'Instagram', 'YouTube', 'Twitch', 'Spotify', 'Facebook', 'X'],
            'successFlash' => $successFlash,
            'updateNominationToken' => CsrfService::token('update_nomination'),
            'viewNominationUrl' => appUrl('admin/nominations/view'),
        ]);
    }

    public function deleteNomination(): void
    {
        require_once appPath('config/session.php');
        require_once appPath('config/permissions.php');
        require_once appPath('config/database.php');

        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithError('admin/nominations', 'Méthode non autorisée.');
        }

        if (!CsrfService::isValid($_POST['csrf_token'] ?? null, 'delete_nomination')) {
            $this->redirectWithError('admin/nominations', 'Jeton CSRF invalide.');
        }

        $nominationId = isset($_POST['id']) && is_numeric((string) $_POST['id'])
            ? (int) $_POST['id']
            : 0;

        if ($nominationId <= 0) {
            $this->redirectWithError('admin/nominations', 'ID de nomination invalide.');
        }

        $adminId = (int) (getUserId() ?? 0);
        $pdo = \Database::getInstance()->getConnection();
        $nominationService = new NominationService($pdo);

        try {
            $nomination = $nominationService->getNominationById($nominationId);

            if (!$nomination) {
                $this->redirectWithError('admin/nominations', 'Nomination non trouvée.');
            }

            $success = $nominationService->deleteNomination($nominationId);

            if ($success) {
                $_SESSION['success'] = 'Nomination supprimée avec succès.';
                error_log("Admin {$adminId} deleted nomination {$nominationId} - " . date('Y-m-d H:i:s'));

                try {
                    $candidatureStmt = $pdo->prepare("UPDATE candidature SET statut = 'Rejetée' WHERE id_candidature = ?");
                    $candidatureStmt->execute([$nomination->getIdCandidature()]);

                    $candidateStmt = $pdo->prepare('UPDATE candidat SET est_nomine = FALSE WHERE id_compte = ?');
                    $candidateStmt->execute([$nomination->getIdCompte()]);
                } catch (\Throwable $exception) {
                    error_log('Error updating related records: ' . $exception->getMessage());
                }
            } else {
                $_SESSION['error'] = 'Erreur lors de la suppression de la nomination.';
            }
        } catch (\Throwable $exception) {
            $_SESSION['error'] = 'Erreur technique: ' . $exception->getMessage();
            error_log('Error deleting nomination ' . $nominationId . ': ' . $exception->getMessage());
        }

        header('Location: ' . appUrl('admin/nominations'));
        exit;
    }

    private function resolveNominationImageUrl(?string $imagePath, string $defaultUrl): string
    {
        $normalizedPath = ltrim((string) $imagePath, '/');

        if ($normalizedPath === '') {
            return $defaultUrl;
        }

        if (str_starts_with($normalizedPath, 'uploads/')) {
            return appUrl('public/' . $normalizedPath);
        }

        return appUrl($normalizedPath);
    }

    private function resolveCandidatureImageUrl(?string $imagePath, string $defaultUrl): string
    {
        $normalizedPath = ltrim((string) $imagePath, '/');

        if ($normalizedPath === '') {
            return $defaultUrl;
        }

        if (str_starts_with($normalizedPath, 'uploads/')) {
            return appUrl('public/' . $normalizedPath);
        }

        if (str_starts_with($normalizedPath, 'public/')) {
            return appUrl($normalizedPath);
        }

        return appUrl('public/' . $normalizedPath);
    }

    private function resolvePlatformIcon(string $platform): string
    {
        return match (strtolower($platform)) {
            'tiktok' => 'fa-tiktok',
            'instagram' => 'fa-instagram',
            'youtube' => 'fa-youtube',
            'facebook' => 'fa-facebook',
            'x', 'twitter' => 'fa-x-twitter',
            'twitch' => 'fa-twitch',
            'spotify' => 'fa-spotify',
            default => 'fa-globe',
        };
    }

    private function redirectWithError(string $route, string $message): never
    {
        $_SESSION['error'] = $message;
        header('Location: ' . appUrl($route));
        exit;
    }

    private function redirectToCandidatureView(int $candidatureId, string $flashType, string $message): never
    {
        $_SESSION[$flashType] = $message;
        header('Location: ' . appUrl('admin/candidatures/view') . '?id=' . $candidatureId);
        exit;
    }
}