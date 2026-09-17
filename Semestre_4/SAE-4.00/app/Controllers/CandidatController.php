<?php
// app/Controllers/CandidatController.php

namespace App\Controllers;

require_once __DIR__ . '/../../config/paths.php';

use App\Services\CandidatService;

class CandidatController
{
    private CandidatService $candidatService;

    /**
     * Controller constructor.
     *
     * @param CandidatService $candidatService Candidate management service
     */
    public function __construct(CandidatService $candidatService)
    {
        $this->candidatService = $candidatService;
    }

    private function requireCandidateAuthentication(): void
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'candidate') {
            $this->redirect('login');
        }
    }

    private function redirect(string $path): void
    {
        header('Location: ' . appUrl($path));
        exit;
    }

    private function absoluteUrl(string $path): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $scheme . '://' . $host . appUrl($path);
    }

    /**
     * Displays the candidate or nominee dashboard.
     *
     * @return void
     */
    public function dashboard(): void
    {
        // Vérification de l'authentification
        $this->requireCandidateAuthentication();

        $userId = $_SESSION['user_id'];

        // Check nominee status
        $isNominee = $this->candidatService->isNominee($userId);
        $nominations = [];
        $votingStatus = 'not_started';

        if ($isNominee) {
            $nominations = $this->candidatService->getActiveNominations($userId);
            $votingStatus = $this->candidatService->getPrimaryNominationVotingStatus($nominations);
        }

        // Candidate statistics
        $stats = $this->candidatService->getCandidatStats($userId);

        // Recent applications
        $candidatures = $this->candidatService->getUserCandidatures($userId);
        $recentCandidatures = array_slice($candidatures, 0, 5);

                    // Active editions
                $activeEditions = $this->candidatService->getActiveEditionsForCandidature();

          // Load the view
        require __DIR__ . '/../../views/candidate/candidate-dashboard.php';
    }

    /**
      * Displays the list of the user's applications.
      *
      * @return void
      */
    public function mesCandidatures(): void
    {
        $this->requireCandidateAuthentication();

        $userId = $_SESSION['user_id'];
        $candidatures = $this->candidatService->getUserCandidatures($userId);
        $stats = $this->candidatService->getCandidatStats($userId);

        require __DIR__ . '/../../views/candidate/mes-candidatures.php';
    }

    
    public function soumettreCandidature(): void
    {
        $this->requireCandidateAuthentication();

        $userId = $_SESSION['user_id'];
        $isEditRequest = isset($_GET['edit']) || !empty($_POST['id_candidature']);

        // Check whether the candidate can still edit during the voting phase.
        $isNominee = $this->candidatService->isNominee($userId);
        if ($isNominee) {
            $canEdit = $this->candidatService->canEditProfile($userId);
            if (!$canEdit) {
                $_SESSION['error'] = "Vous ne pouvez pas soumettre de candidature pendant les votes.";
                $this->redirect('candidate/dashboard');
            }
        }

        if (!$isEditRequest && empty($this->candidatService->getActiveEditionsForCandidature())) {
            $_SESSION['error'] = "Aucun appel à candidatures n'est actuellement ouvert.";
            $this->redirect('candidate/candidatures');
        }

        // Handle submission/update.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!\App\Services\CsrfService::isValid($_POST['csrf_token'] ?? null, 'candidate_submission')) {
                $_SESSION['error'] = "La validation du formulaire a expiré. Veuillez réessayer.";
                return;
            }

            $this->processCandidatureSubmission($userId);
            
            // Redirect after POST handling.
            if (!isset($_SESSION['error'])) {
                exit; // The header() in processCandidatureSubmission should already have been called.
            }
        }

        // Edit mode
        $candidature = null;
        if (isset($_GET['edit'])) {
            $candidature = $this->candidatService->getCandidature($_GET['edit'], $userId);
            if (!$candidature) {
                $_SESSION['error'] = "Candidature non trouvée.";
                $this->redirect('candidate/candidatures');
            }

            if ($candidature->getStatut() !== 'En attente') {
                $_SESSION['error'] = "Seules les candidatures en attente peuvent être modifiées.";
                $this->redirect('candidate/candidatures');
            }
        }
    }

    /**
     * Displays the details of a specific application.
     *
     * @return void
     */
    public function candidatureDetails(): void
    {
        echo (new CandidatePageController())->detail();
    }

    /**
     * Displays the public profile of the nominee (managed from the candidate area).
     *
     * @return void
     */
    public function nomineeProfile(): void
    {
        echo (new CandidatePageController())->nomineeProfile();
    }

    /**
     * Nomination sharing page.
     *
     * @return void
     */
    public function shareNomination(): void
    {
        echo (new CandidatePageController())->share();
    }

    /**
     * Displays the current voting state for active nominations.
     *
     * @return void
     */
    public function statusVotes(): void
    {
        $this->redirect('candidate/nominee-profile');
    }

    /**
     * Displays the contest rules.
     *
     * @return void
     */
    public function reglement(): void
    {
        echo (new CandidatePageController())->rules();
    }

    /**
     * Deletes an application (if authorized).
     *
     * @return void
     */
    public function deleteCandidature(): void
    {
        (new CandidatePageController())->delete();
    }

    /**
     * Processes submission or update of an application.
     *
     * @param int $userId Identifier of the logged-in user
     * @return void
     */
    private function processCandidatureSubmission(int $userId): void
    {
        $data = [
            'libelle' => $_POST['libelle'] ?? '',
            'plateforme' => $_POST['plateforme'] ?? '',
            'url_contenu' => $_POST['url_contenu'] ?? '',
            'argumentaire' => $_POST['argumentaire'] ?? '',
            'id_categorie' => $_POST['id_categorie'] ?? 0
        ];

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $imagePath = $this->candidatService->uploadImage($_FILES['image']);
            if ($imagePath) {
                $data['image'] = $imagePath;
            }
        }

        // Validate required fields
        if (empty($data['libelle']) || empty($data['url_contenu']) || empty($data['argumentaire'])) {
            $_SESSION['error'] = "Tous les champs obligatoires doivent être remplis.";
            return;
        }

        $success = false;

        if (isset($_POST['id_candidature']) && !empty($_POST['id_candidature'])) {
            // Update an existing application
            $success = $this->candidatService->updateCandidature(
                $_POST['id_candidature'],
                $data,
                $userId
            );
            $message = $success ? "Candidature mise à jour avec succès." : "Erreur lors de la mise à jour.";
        } else {
            // Create a new application
            $success = $this->candidatService->createCandidature($data, $userId);
            $message = $success ? "Candidature soumise avec succès." : "Erreur lors de la soumission.";
        }

        if ($success) {
            $_SESSION['success'] = $message;
            $this->redirect('candidate/candidatures');
        } else {
            $_SESSION['error'] = $message;
        }
    }

    /**
     * Allows editing of the nominee's public profile.
     *
     * @return void
     */
    public function editNomineeProfile(): void
    {
        $this->requireCandidateAuthentication();

        $userId = $_SESSION['user_id'];

        $isNominee = $this->candidatService->isNominee($userId);
        if (!$isNominee) {
            $_SESSION['error'] = "Vous devez être nominé pour accéder à cette page.";
            $this->redirect('candidate/dashboard');
        }

        $canEdit = $this->candidatService->canEditProfile($userId);
        if (!$canEdit) {
            $_SESSION['error'] = "Vous ne pouvez pas modifier votre profil pendant les votes.";
            $this->redirect('candidate/nominee-profile');
        }

        $_SESSION['success'] = "La modification du profil se fait désormais depuis Mon compte.";
        $this->redirect('candidate/profile');
    }

    /**
     * Displays the voting results (available after voting ends).
     *
     * @return void
     */
    public function viewResults(): void
    {
        $this->requireCandidateAuthentication();

        $userId = $_SESSION['user_id'];

        $isNominee = $this->candidatService->isNominee($userId);
        if (!$isNominee) {
            $_SESSION['error'] = "Vous devez être nominé pour accéder à cette page.";
            $this->redirect('candidate/dashboard');
        }

        $nominations = $this->candidatService->getActiveNominations($userId);

        if (!$this->candidatService->hasAvailableNominationResults($nominations)) {
            $_SESSION['error'] = "Aucun résultat n'est encore disponible.";
            $this->redirect('candidate/dashboard');
        }

        $this->redirect('results');
    }

    /**
     * Management page for pending / possible applications.
     *
     * @return void
     */
    public function pendingCandidatures(): void
    {
        $this->requireCandidateAuthentication();

        $userId = $_SESSION['user_id'];

        $isNominee = $this->candidatService->isNominee($userId);
        if ($isNominee) {
            $canEdit = $this->candidatService->canEditProfile($userId);
            if (!$canEdit) {
                $_SESSION['error'] = "Vous ne pouvez pas soumettre de candidature pendant les votes.";
                $this->redirect('candidate/dashboard');
            }
        }

        $this->redirect('candidate/submit');
    }

}