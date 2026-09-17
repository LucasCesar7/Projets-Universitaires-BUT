<?php
// app/Services/VoteService.php

namespace App\Services;

use App\Models\UserModel;
use App\Models\VoteModel;
use Exception;

/**
 * Vote management service.
 * Handles the business logic related to the voting process.
 */
class VoteService
{
    private $voteModel;
    private $userModel;

    /**
     * Vote service constructor.
     * Initializes the required models.
     */
    public function __construct()
    {
        $this->voteModel = new VoteModel();
        $this->userModel = new UserModel();
    }
    
   
    /**
     * Checks whether a user can vote in a category.
     * Wrapper around canUserVoteSimple that returns a boolean.
     * 
     * @param int $userId User ID
     * @param int $categoryId Category ID
     * @return bool True if the user can vote, otherwise false
     */
    public function canUserVote($userId, $categoryId)
    {
        $result = $this->canUserVoteSimple($userId, $categoryId);
        return isset($result['can_vote']) && $result['can_vote'] === true;
    }

    /**
     * Getter used to access the vote model.
     * 
     * @return Vote Vote model
     */
    public function getVoteModel()
    {
        return $this->voteModel;
    }

    /**
     * Retrieves the categories available to the user.
     * FIXED VERSION - Displays all active categories.
     * 
     * @param int $userId User ID
     * @return array Available categories with voting information
     */
    public function getAvailableCategoriesForUser($userId)
    {
        try {
            error_log("=== DEBUG getAvailableCategoriesForUser ===");
            error_log("User ID: " . $userId);

            // 1. Get the active edition.
            $activeEdition = $this->getActiveEdition();

            if (!$activeEdition) {
                error_log("DEBUG: No active edition found!");
                return [];
            }

            error_log("DEBUG: Active edition ID: " . $activeEdition['id_edition']);

            // 2. Get ALL categories for the active edition.
            $categories = $this->getCategoriesForEdition($activeEdition['id_edition']);

            if (empty($categories)) {
                error_log("DEBUG: No categories found for the edition!");
                return [];
            }

            error_log("DEBUG: Total categories in the edition: " . count($categories));

            $available = [];
            $now = date('Y-m-d H:i:s');

            foreach ($categories as $category) {
                $categoryId = $category['id_categorie'] ?? 0;

                if ($categoryId <= 0) {
                    continue;
                }

                error_log("--- Processing category ID: {$categoryId} ---");

                // 3. Check whether it is within the voting period.
                $isActive = $this->isCategoryInVotingPeriod($category, $activeEdition);

                if (!$isActive) {
                    error_log("Category {$categoryId} is not active (outside the period)");
                    continue;
                }

                // 4. Count nominations.
                $nominations = $this->voteModel->getNominationsForCategory($categoryId);
                $nominationCount = count($nominations);

                // 5. Check whether the user has already voted.
                $hasVoted = $this->voteModel->hasUserVoted($userId, $categoryId);

                // 6. Determine whether voting is possible now.
                $canVoteNow = !$hasVoted && $nominationCount > 0;

                // 7. Add the information.
                $category['nomination_count'] = $nominationCount;
                $category['has_voted'] = $hasVoted;
                $category['is_active'] = true;
                $category['can_vote'] = $canVoteNow;
                $category['has_nominations'] = $nominationCount > 0;
                $category['nominations'] = $nominations;

                // 8. Add formatted dates for display.
                if ($category['date_debut_votes'] && $category['date_fin_votes']) {
                    $category['vote_start_formatted'] = date('d/m/Y', strtotime($category['date_debut_votes']));
                    $category['vote_end_formatted'] = date('d/m/Y', strtotime($category['date_fin_votes']));
                } else {
                    $category['vote_start_formatted'] = date('d/m/Y', strtotime($activeEdition['date_debut']));
                    $category['vote_end_formatted'] = date('d/m/Y', strtotime($activeEdition['date_fin']));
                }

                $available[] = $category;

                error_log("✓ Category {$categoryId} '{$category['nom']}' added");
                error_log("  - Nominations: {$nominationCount}");
                error_log("  - Already voted: " . ($hasVoted ? 'YES' : 'NO'));
                error_log("  - Can vote now: " . ($canVoteNow ? 'YES' : 'NO'));
                error_log("  - Has nominations: " . ($nominationCount > 0 ? 'YES' : 'NO'));
            }

            error_log("=== FIN getAvailableCategoriesForUser ===");
            error_log("Available categories: " . count($available));

            return $available;
        } catch (Exception $e) {
            error_log("ERROR in getAvailableCategoriesForUser: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieves the active edition.
     * 
     * @return array|null Active edition information or null
     */
    private function getActiveEdition()
    {
        try {
            $db = $this->voteModel->getDb();

            $stmt = $db->query("
                SELECT id_edition, annee, nom, date_debut_candidatures, date_fin_candidatures, date_debut, date_fin, est_active, theme, image, description FROM edition 
                WHERE est_active = 1 
                ORDER BY annee DESC 
                LIMIT 1
            ");

            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("ERROR in getActiveEdition: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieves the categories for an edition.
     * 
     * @param int $editionId Edition ID
     * @return array List of categories for the edition
     */
    private function getCategoriesForEdition($editionId)
    {
        try {
            $db = $this->voteModel->getDb();

            $stmt = $db->prepare("
                SELECT c.*, e.annee, e.nom as edition_nom
                FROM categorie c
                JOIN edition e ON c.id_edition = e.id_edition
                WHERE c.id_edition = :edition_id
                ORDER BY c.nom ASC
            ");

            $stmt->execute([':edition_id' => $editionId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("ERROR in getCategoriesForEdition: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Checks whether the category is within the voting period.
     * 
     * @param array $category Category information
     * @param array $edition Edition information
     * @return bool True if it is within the voting period
     */
    private function isCategoryInVotingPeriod($category, $edition)
    {
        $now = date('Y-m-d H:i:s');

        // If the category has specific dates, use them.
        if ($category['date_debut_votes'] && $category['date_fin_votes']) {
            $start = $category['date_debut_votes'];
            $end = $category['date_fin_votes'];
            return ($now >= $start && $now <= $end);
        }

        // Otherwise, use the edition dates.
        if ($edition['date_debut'] && $edition['date_fin']) {
            $start = $edition['date_debut'];
            $end = $edition['date_fin'];
            return ($now >= $start && $now <= $end);
        }

        return false;
    }

    /**
     * Starts the voting process for a category.
     * 
     * @param int $userId User ID
     * @param int $categoryId Category ID
     * @return array Vote start result
     */
    public function startVotingProcess($userId, $categoryId)
    {
        error_log("=== DEBUG startVotingProcess ===");
        error_log("User: {$userId}, Category: {$categoryId}");

        // 1. Check whether voting is allowed.
        $canVote = $this->canUserVoteSimple($userId, $categoryId);

        if (!$canVote['can_vote']) {
            error_log("DEBUG: User CANNOT vote. Reason: " . $canVote['reason']);
            return [
                'success' => false,
                'message' => $canVote['reason'],
                'already_voted' => $canVote['already_voted'] ?? false
            ];
        }

        error_log("DEBUG: User CAN vote");

        // 2. Check whether nominations exist.
        $nominations = $this->voteModel->getNominationsForCategory($categoryId);

        if (empty($nominations)) {
            error_log("DEBUG: No nomination found for category {$categoryId}");
            return [
                'success' => false,
                'message' => 'Aucune nomination disponible pour cette catégorie'
            ];
        }

        error_log("DEBUG: Found " . count($nominations) . " nominations");

        // 3. Generate the anonymous token.
        $token = $this->voteModel->generateToken($userId, $categoryId);

        if (!$token) {
            error_log("DEBUG: Token generation failed");
            return [
                'success' => false,
                'message' => 'Erreur lors de la génération du token'
            ];
        }

        error_log("DEBUG: Token generated successfully: " . substr($token, 0, 20) . "...");

        return [
            'success' => true,
            'token' => $token,
            'nominations' => $nominations,
            'category_id' => $categoryId,
            'nomination_count' => count($nominations),
            'message' => 'Prêt à voter!'
        ];
    }

    /**
     * SIMPLIFIED check for whether the user can vote.
     * 
     * @param int $userId User ID
     * @param int $categoryId Category ID
     * @return array Check result
     */
    public function canUserVoteSimple($userId, $categoryId)
    {
        try {
            error_log("=== DEBUG canUserVoteSimple ===");
            error_log("User: {$userId}, Category: {$categoryId}");

            // 1. Get category information.
            $categoryInfo = $this->voteModel->getCategoryInfo($categoryId);

            if (!$categoryInfo) {
                error_log("DEBUG: Category not found");
                return [
                    'can_vote' => false,
                    'reason' => 'Catégorie non trouvée',
                    'already_voted' => false
                ];
            }

            // 2. Get the active edition.
            $activeEdition = $this->getActiveEdition();

            if (!$activeEdition) {
                error_log("DEBUG: No active edition found");
                return [
                    'can_vote' => false,
                    'reason' => 'Aucune édition active',
                    'already_voted' => false
                ];
            }

            // 3. Check whether the category is active (VOTING PERIOD).
            $isActive = $this->isCategoryInVotingPeriod($categoryInfo, $activeEdition);

            if (!$isActive) {
                error_log("DEBUG: Category is not in the voting period");
                return [
                    'can_vote' => false,
                    'reason' => 'Les votes ne sont pas ouverts pour cette catégorie',
                    'already_voted' => false
                ];
            }

            // 4. Check whether the user has already voted.
            $hasVoted = $this->voteModel->hasUserVoted($userId, $categoryId);

            if ($hasVoted) {
                error_log("DEBUG: User has already voted in this category");
                return [
                    'can_vote' => false,
                    'reason' => 'Vous avez déjà voté dans cette catégorie',
                    'already_voted' => true
                ];
            }

            // 5. Check whether nominations exist.
            $nominations = $this->voteModel->getNominationsForCategory($categoryId);

            if (empty($nominations)) {
                error_log("DEBUG: Category has no nominations");
                return [
                    'can_vote' => false,
                    'reason' => 'Aucune nomination disponible',
                    'already_voted' => false
                ];
            }

            error_log("DEBUG: User CAN vote!");
            return [
                'can_vote' => true,
                'reason' => 'Peut voter',
                'already_voted' => false
            ];
        } catch (Exception $e) {
            error_log("ERROR in canUserVoteSimple: " . $e->getMessage());
            return [
                'can_vote' => false,
                'reason' => 'Erreur technique: ' . $e->getMessage(),
                'already_voted' => false
            ];
        }
    }

    /**
     * Retrieves the user's voting status.
     * 
     * @param int $userId User ID
     * @return array Voting status by category
     */
    public function getUserVotingStatus($userId)
    {
        try {
            error_log("=== DEBUG getUserVotingStatus ===");

            // Get the available categories.
            $categories = $this->getAvailableCategoriesForUser($userId);

            if (empty($categories)) {
                error_log("DEBUG: No category available for the status");
                return [];
            }

            $status = [];

            foreach ($categories as $category) {
                $categoryId = $category['id_categorie'];
                $hasVoted = $category['has_voted'] ?? false;
                $hasNominations = $category['has_nominations'] ?? false;

                $status[] = [
                    'category_id' => $categoryId,
                    'category_name' => $category['nom'],
                    'has_voted' => $hasVoted,
                    'is_active' => $category['is_active'] ?? true,
                    'nomination_count' => $category['nomination_count'] ?? 0,
                    'has_nominations' => $hasNominations,
                    'can_vote' => !$hasVoted && $hasNominations,
                    'vote_period' => $category['vote_start_formatted'] . ' - ' . $category['vote_end_formatted']
                ];

                error_log("Category status {$categoryId}: Voted={$hasVoted}, Nominations={$hasNominations}");
            }

            error_log("DEBUG: Returned status for {$userId}: " . count($status) . " categories");
            return $status;
        } catch (Exception $e) {
            error_log("ERROR in getUserVotingStatus: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Processes the vote.
     * 
     * @param string $token Anonymous token
     * @param int $nominationId Nomination ID
     * @param int $userId User ID
     * @return array Vote processing result
     */
    public function processVote($token, $nominationId, $userId)
    {
        try {
            error_log("=== DEBUG processVote ===");
            error_log("Token: " . substr($token, 0, 20) . "...");
            error_log("Nomination: {$nominationId}, User: {$userId}");

            // 1. Check whether the token is still valid.
            if (!$this->isTokenValid($token)) {
                error_log("DEBUG: Invalid or expired token");
                return [
                    'success' => false,
                    'message' => 'Token de vote invalide ou expiré'
                ];
            }

            // 2. Encrypt the vote.
            $encryptedVote = $this->voteModel->encryptVote($nominationId, $userId);
            error_log("DEBUG: Encrypted vote: " . substr($encryptedVote, 0, 50) . "...");

            // 3. Record the vote through the stored procedure.
            $voteId = $this->voteModel->castVote($token, $encryptedVote, $nominationId);

            if ($voteId) {
                error_log("DEBUG: Vote recorded with ID: {$voteId}");

                // 4. Try to get the certificate.
                $certificate = null;
                try {
                    $categoryId = $this->getCategoryIdFromToken($token);
                    if ($categoryId) {
                        $certificate = $this->voteModel->getParticipationCertificate($userId, $categoryId);
                        error_log("DEBUG: Certificate retrieved: " . ($certificate ? 'YES' : 'NO'));
                    }
                } catch (Exception $e) {
                    error_log("WARNING: Unable to retrieve the certificate: " . $e->getMessage());
                    $certificate = null;
                }

                return [
                    'success' => true,
                    'vote_id' => $voteId,
                    'certificate' => $certificate,
                    'message' => 'Votre vote a été enregistré avec succès!'
                ];
            } else {
                error_log("ERROR: Failed to record the vote");
                return [
                    'success' => false,
                    'message' => 'Erreur lors de l\'enregistrement du vote'
                ];
            }
        } catch (Exception $e) {
            error_log("CRITICAL ERROR in processVote: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur technique est survenue: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Checks whether the token is valid.
     * 
     * @param string $token Token to check
     * @return bool True if the token is valid
     */
    private function isTokenValid($token)
    {
        try {
            $db = $this->voteModel->getDb();

            $stmt = $db->prepare("
                SELECT id_token FROM TOKEN_ANONYME 
                WHERE token_value = :token 
                AND est_utilise = FALSE 
                AND date_expiration > NOW()
            ");

            $stmt->execute([':token' => $token]);
            return $stmt->fetch() !== false;
        } catch (Exception $e) {
            error_log("ERROR in isTokenValid: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper: retrieves the category ID from the token.
     * 
     * @param string $token Anonymous token
     * @return int|null Category ID or null
     */
    private function getCategoryIdFromToken($token)
    {
        try {
            $stmt = $this->voteModel->getDb()->prepare("
                SELECT id_categorie FROM token_anonyme
                WHERE token_value = :token
            ");
            $stmt->execute([':token' => $token]);
            $result = $stmt->fetch();

            return $result ? $result['id_categorie'] : null;
        } catch (Exception $e) {
            error_log("ERROR in getCategoryIdFromToken: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Checks voting status in real time.
     * 
     * @param int $userId User ID
     * @param int|null $categoryId Category ID (optional)
     * @return array Voting status
     */
    public function checkVotingStatus($userId, $categoryId = null)
    {
        try {
            if ($categoryId) {
                $canVote = $this->canUserVoteSimple($userId, $categoryId);
                $voteModel = $this->voteModel;
                $hasVoted = $voteModel->hasUserVoted($userId, $categoryId);

                return [
                    'authenticated' => true,
                    'can_vote' => $canVote['can_vote'],
                    'has_voted' => $hasVoted,
                    'already_voted' => $canVote['already_voted'] ?? false,
                    'category_active' => true
                ];
            } else {
                $status = $this->getUserVotingStatus($userId);
                return [
                    'authenticated' => true,
                    'voting_status' => $status
                ];
            }
        } catch (Exception $e) {
            error_log("ERROR in checkVotingStatus: " . $e->getMessage());
            return ['authenticated' => false];
        }
    }
}

\class_alias(VoteService::class, 'VoteService');