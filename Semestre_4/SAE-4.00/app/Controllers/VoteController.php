<?php

namespace App\Controllers;

require_once __DIR__ . '/../../config/session.php';

use App\Models\VoteModel;
use App\Services\CsrfService;
use App\Services\VoteService;
use Exception;

/**
 * Controller handling the user voting process.
 * - Displays the voting page
 * - Handles starting a vote by category
 * - Processes submitted votes
 * - Generates participation certificates
 */
class VoteController {
    private $voteService; // Vote processing service.

    /**
     * Vote controller constructor.
     * - Initializes the voting service
     */
    public function __construct() {
        $this->voteService = new VoteService();
    }

    /**
     * Displays the main voting page.
     * - Checks user authentication
     * - Retrieves the user's voting status
     * - Gets the categories available to the user
     * 
     * @return array Data used to display the voting page
     */
    public function showVotingPage() {
        startSecureSession();
        
        // Redirect if the user is not authenticated or is not a voter.
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'voter') {
            header('Location: ' . publicRouteUrl('login'));
            exit();
        }

        $userId = $_SESSION['user_id'];
        
        // Get the user's voting status.
        $votingStatus = $this->voteService->getUserVotingStatus($userId);
        
        // Get the categories available to the user.
        $availableCategories = $this->voteService->getAvailableCategoriesForUser($userId);
        
        return [
            'voting_status' => $votingStatus,
            'available_categories' => $availableCategories,
            'user' => [
                'id' => $userId,
                'pseudonyme' => $_SESSION['user_pseudonyme']
            ]
        ];
    }

    /**
     * Loads the data for a category-specific voting page.
     * - Retrieves the category and its nominations
     * - Restores the voting session when the page is reopened directly
     *
     * @param int $categoryId Category ID
     * @return array Data used by the view for the category page
     */
    public function showCategoryVotingPage(int $categoryId): array
    {
        startSecureSession();

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'voter') {
            return [
                'success' => false,
                'message' => 'Non autorisé',
                'current_category' => null,
                'nominations' => []
            ];
        }

        $voteModel = $this->voteService->getVoteModel();
        $currentCategory = $voteModel->getCategoryInfo($categoryId);

        if (!$currentCategory) {
            return [
                'success' => false,
                'message' => 'Catégorie invalide ou non trouvée.',
                'current_category' => null,
                'nominations' => []
            ];
        }

        $nominations = $voteModel->getNominationsForCategory($categoryId);

        if (!isset($_SESSION['voting_token']) || (int) ($_SESSION['voting_category'] ?? 0) !== $categoryId) {
            error_log("DEBUG VoteController: Rétablissement de la session de vote pour catégorie {$categoryId}");
            $startResult = $this->voteService->startVotingProcess((int) $_SESSION['user_id'], $categoryId);

            if ($startResult['success']) {
                $this->initializeVotingSession($startResult, $categoryId, $currentCategory['nom'] ?? 'Catégorie');
                error_log('DEBUG VoteController: Session de vote rétablie avec succès');
            } else {
                return [
                    'success' => false,
                    'message' => $startResult['message'] ?? 'Impossible d\'initialiser cette session de vote.',
                    'current_category' => $currentCategory,
                    'nominations' => $nominations,
                    'already_voted' => $startResult['already_voted'] ?? false
                ];
            }
        }

        return [
            'success' => true,
            'current_category' => $currentCategory,
            'nominations' => $nominations
        ];
    }

    /**
     * Starts the voting process for a specific category - FIXED VERSION.
     * - Checks authentication and permissions
     * - Clears the previous voting session
     * - Checks whether the user can vote
     * - Initializes a new voting process
     * - Stores the information in the session
     * 
     * @return array Vote initialization result
     */
    public function startCategoryVoting() {
        startSecureSession();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'voter') {
            return ['success' => false, 'message' => 'Non autorisé'];
        }

        // Check that the request method is POST.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Méthode non autorisée'];
        }

        if (!CsrfService::isValid($_POST['csrf_token'] ?? null, 'start_voting_voter')) {
            return ['success' => false, 'message' => 'La validation du formulaire a expiré. Veuillez réessayer.'];
        }

        $userId = $_SESSION['user_id'];
        $categoryId = intval($_POST['category_id'] ?? 0);

        // Validate the category ID.
        if ($categoryId <= 0) {
            return ['success' => false, 'message' => 'Catégorie invalide'];
        }
        
        // CLEAR any previous voting session BEFORE the check.
        $this->clearVotingSession();
        
        // Check whether the user can vote (includes the existing-vote check).
        $canVote = $this->voteService->canUserVoteSimple($userId, $categoryId);
        
        if (!$canVote['can_vote']) {
            return [
                'success' => false, 
                'message' => $canVote['reason'],
                'already_voted' => $canVote['already_voted'] ?? false
            ];
        }

        // Start a new voting process.
        $result = $this->voteService->startVotingProcess($userId, $categoryId);
        
        if ($result['success']) {
            $this->initializeVotingSession($result, $categoryId);
            
            // Logging for debugging.
            error_log("DEBUG: New voting session started for category {$categoryId}");
            error_log("DEBUG: Token: " . substr($result['token'], 0, 20) . "...");
        } else {
            error_log("DEBUG: Failed to start voting: " . $result['message']);
        }

        return $result;
    }

    /**
     * Processes a submitted vote - FIXED VERSION.
     * - Checks authentication and permissions
     * - Performs several validations (existing vote, token, session, expiration)
     * - Processes the vote through the service
     * - Clears and updates the session after the vote
     * 
     * @return array Vote processing result
     */
    public function castVote() {
        startSecureSession();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'voter') {
            return ['success' => false, 'message' => 'Non autorisé'];
        }

        // Check that the request method is POST.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Méthode non autorisée'];
        }

        if (!CsrfService::isValid($_POST['csrf_token'] ?? null, 'cast_vote_voter')) {
            return ['success' => false, 'message' => 'La validation du formulaire a expiré. Veuillez réessayer.'];
        }

        $userId = $_SESSION['user_id'];
        $categoryId = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        $nominationId = intval($_POST['nomination_id'] ?? 0);

        // Validate IDs.
        if ($categoryId <= 0) {
            return ['success' => false, 'message' => 'Catégorie invalide'];
        }
        
        if ($nominationId <= 0) {
            return ['success' => false, 'message' => 'Sélection invalide'];
        }
        
        // FIRST CHECK: Has the user already voted in this category?
        $voteModel = new VoteModel();
        $hasVoted = $voteModel->hasUserVoted($userId, $categoryId);
        
        if ($hasVoted) {
            $this->clearVotingSession();
            return [
                'success' => false, 
                'message' => 'Vous avez déjà voté dans cette catégorie',
                'already_voted' => true
            ];
        }
        
        // SECOND CHECK: Are the token and session valid?
        // If the session does not have the token, recreate the voting session.
        if (!isset($_SESSION['voting_token']) || !isset($_SESSION['voting_category'])) {
            // Try to recreate the voting session if the token is present in POST.
            if (isset($_POST['token']) && !empty($_POST['token'])) {
                // Recreate session variables from the POST data.
                $_SESSION['voting_token'] = $_POST['token'];
                $_SESSION['voting_category'] = $categoryId;
                $_SESSION['voting_started'] = time();
                $_SESSION['voting_expires'] = time() + 3600;
                
                error_log("DEBUG: Voting session recreated from POST data");
            } else {
                $this->clearVotingSession();
                return [
                    'success' => false, 
                    'message' => 'Session de vote expirée ou invalide. Veuillez recommencer.',
                    'session_expired' => true
                ];
            }
        }
        
        // THIRD CHECK: Does the token match the category?
        if ($_SESSION['voting_category'] != $categoryId) {
            $this->clearVotingSession();
            return [
                'success' => false, 
                'message' => 'Token de vote invalide pour cette catégorie.',
                'token_mismatch' => true
            ];
        }
        
        // FOURTH CHECK: Has the session expired?
        if (isset($_SESSION['voting_expires']) && time() > $_SESSION['voting_expires']) {
            $this->clearVotingSession();
            return [
                'success' => false, 
                'message' => 'Session de vote expirée. Veuillez recommencer.',
                'session_expired' => true
            ];
        }

        $token = $_SESSION['voting_token'];

        // Process the vote through the service.
        $result = $this->voteService->processVote($token, $nominationId, $userId);
        
        if ($result['success']) {
            $currentVoteCategoryName = $_SESSION['voting_category_name'] ?? $this->getCategoryName($categoryId);

            // COMPLETELY CLEAR the voting session.
            $this->clearVotingSession();
            
            // Clear any previous message.
            if (isset($_SESSION['vote_success'])) unset($_SESSION['vote_success']);
            if (isset($_SESSION['vote_message'])) unset($_SESSION['vote_message']);
            
            // Store ONLY the current vote.
            $_SESSION['last_vote'] = [
                'vote_id' => $result['vote_id'],
                'category_id' => $categoryId,
                'category_name' => $currentVoteCategoryName,
                'certificate' => $result['certificate'] ?? null,
                'message' => $result['message'],
                'timestamp' => time(),
                'is_current' => true // Mark as the current vote.
            ];
            
            // Mark that a vote succeeded.
            $_SESSION['vote_success'] = true;
            $_SESSION['vote_message'] = $result['message'];
            
            error_log("DEBUG: Vote processed successfully. ID: " . $result['vote_id']);
        } else {
            // On failure, clear the session.
            $this->clearVotingSession();
            error_log("DEBUG: Vote processing failed: " . $result['message']);
        }

        return $result;
    }

    /**
     * Retrieves the participation certificate.
     * - Checks authentication
     * - Looks in the session first
     * - Then in the database
     * 
     * @return array Result containing the certificate or an error message
     */
    public function getCertificate() {
        startSecureSession();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'voter') {
            return ['success' => false, 'message' => 'Non autorisé'];
        }

        $userId = $_SESSION['user_id'];
        $categoryId = intval($_GET['category_id'] ?? 0);

        // First check the last vote in the session.
        if (isset($_SESSION['last_vote']) && $_SESSION['last_vote']['is_current']) {
            return [
                'success' => true,
                'certificate' => $_SESSION['last_vote']['certificate']
            ];
        }

        // Otherwise, look in the database.
        if ($categoryId > 0) {
            $voteModel = $this->voteService->getVoteModel();
            $certificate = $voteModel->getParticipationCertificate($userId, $categoryId);
            
            if ($certificate) {
                return [
                    'success' => true,
                    'certificate' => $certificate
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Aucun certificat trouvé'
        ];
    }

    /**
     * Checks voting status in real time.
     * - Checks authentication
     * - Calls the service to retrieve the status
     * 
     * @return array Authentication and voting status
     */
    public function checkVotingStatus() {
        startSecureSession();
        
        if (!isset($_SESSION['user_id'])) {
            return ['authenticated' => false];
        }

        $userId = $_SESSION['user_id'];
        $categoryId = intval($_GET['category_id'] ?? 0);

        return $this->voteService->checkVotingStatus($userId, $categoryId);
    }

    /**
     * Utility function: gets the name of a category.
     * 
     * @param int $categoryId Category ID
     * @return string Category name or default text
     */
    private function getCategoryName($categoryId) {
        try {
            $voteModel = $this->voteService->getVoteModel();
            $category = $voteModel->getCategoryInfo($categoryId);
            return $category ? $category['nom'] : 'Catégorie inconnue';
        } catch (Exception $e) {
            error_log("getCategoryName error: " . $e->getMessage());
            return 'Catégorie';
        }
    }

    /**
     * Initializes the session state used by the voting flow.
     *
     * @param array $result Vote initialization result
     * @param int $categoryId Category ID
     * @param string|null $categoryName Category name when already loaded
     */
    private function initializeVotingSession(array $result, int $categoryId, ?string $categoryName = null): void
    {
        $_SESSION['voting_token'] = $result['token'];
        $_SESSION['voting_category'] = $categoryId;
        $_SESSION['voting_category_name'] = $categoryName ?? $this->getCategoryName($categoryId);
        $_SESSION['voting_nominations'] = $result['nominations'] ?? [];
        $_SESSION['voting_started'] = time();
        $_SESSION['voting_expires'] = time() + 3600;
    }
    
    /**
     * Clears the voting session.
     * - Removes all session variables related to voting
     * - Marks the last vote as no longer current
     */
    private function clearVotingSession() {
        unset($_SESSION['voting_token']);
        unset($_SESSION['voting_category']);
        unset($_SESSION['voting_category_name']);
        unset($_SESSION['voting_nominations']);
        unset($_SESSION['voting_started']);
        unset($_SESSION['voting_expires']);
        
        // Mark the last vote as no longer current.
        if (isset($_SESSION['last_vote'])) {
            $_SESSION['last_vote']['is_current'] = false;
        }
    }
}

\class_alias(VoteController::class, 'VoteController');
?>