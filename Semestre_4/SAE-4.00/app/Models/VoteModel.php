<?php
// app/Models/VoteModel.php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

use Database;
use Exception;
use PDO;
use PDOException;

/**
 * Model handling all operations related to the voting system.
 * - Anonymous token generation
 * - Vote recording
 * - Voting rights checks
 * - Statistics retrieval
 */
class VoteModel
{
    private $db;

    /**
     * Vote model constructor.
     * Initializes the database connection.
     */
    public function __construct()
    {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    /**
        * Retrieves the database instance.
     * 
        * @return PDO Database connection instance
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
        * Generates an anonymous token for a user in a category.
     * 
        * @param int $userId User account ID
        * @param int $categoryId Category ID
        * @return string|null Generated token or null on error
     */
    public function generateToken($userId, $categoryId)
    {
        try {
            $stmt = $this->db->prepare("CALL generate_anonymous_token(:id_compte, :id_categorie, @token_value)");
            $stmt->execute([
                ':id_compte' => $userId,
                ':id_categorie' => $categoryId
            ]);

            // Retrieve the generated token.
            $stmt = $this->db->query("SELECT @token_value as token_value");
            $result = $stmt->fetch();

            return $result ? $result['token_value'] : null;
        } catch (Exception $e) {
            error_log("Token generation error: " . $e->getMessage());
            return null;
        }
    }

    /**
        * Records a vote in the system.
     * 
        * @param string $token Anonymous user token
        * @param string $encryptedVote Encrypted vote
        * @param int $nominationId Voted nomination ID
        * @return int|false Saved vote ID or false on error
     */
    public function castVote($token, $encryptedVote, $nominationId)
    {
        try {
            $stmt = $this->db->prepare("CALL process_vote(:token, :vote_chiffre, :id_nomination, @id_vote)");
            $stmt->execute([
                ':token' => $token,
                ':vote_chiffre' => $encryptedVote,
                ':id_nomination' => $nominationId
            ]);

            // Retrieve the saved vote ID.
            $stmt = $this->db->query("SELECT @id_vote as id_vote");
            $result = $stmt->fetch();

            return $result ? $result['id_vote'] : null;
        } catch (Exception $e) {
            error_log("Vote recording error: " . $e->getMessage());
            return false;
        }
    }

    /**
        * Checks whether a user has already voted in a category.
     * 
        * @param int $userId User account ID
        * @param int $categoryId Category ID
        * @return bool True if the user has already voted
     */
    public function hasUserVoted($userId, $categoryId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT statut_a_vote 
                FROM controle_presence 
                WHERE id_compte = :id_compte 
                AND id_categorie = :id_categorie
            ");
            $stmt->execute([
                ':id_compte' => $userId,
                ':id_categorie' => $categoryId
            ]);

            $result = $stmt->fetch();
            return $result && $result['statut_a_vote'] == 1;
        } catch (Exception $e) {
            error_log("Vote check error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves the categories available for voting.
     * Takes into account the voting periods for both categories and editions.
     * 
     * @return array List of available categories.
     */
    public function getVotingCategories()
    {
        try {
            $now = date('Y-m-d H:i:s');

            $stmt = $this->db->prepare("
                SELECT c.*, e.annee as edition_year,
                       COUNT(DISTINCT n.id_nomination) as nomination_count
                FROM categorie c
                JOIN edition e ON c.id_edition = e.id_edition
                LEFT JOIN nomination n ON c.id_categorie = n.id_categorie
                WHERE (
                    (c.date_debut_votes IS NULL AND c.date_fin_votes IS NULL AND e.date_debut <= :now AND e.date_fin >= :now)
                    OR
                    (c.date_debut_votes IS NOT NULL AND c.date_fin_votes IS NOT NULL 
                     AND c.date_debut_votes <= :now AND c.date_fin_votes >= :now)
                )
                AND e.est_active = 1
                GROUP BY c.id_categorie
                ORDER BY c.nom ASC
            ");

            $stmt->execute([':now' => $now]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Failed to retrieve categories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieves the approved nominations for a category.
     * 
     * @param int $categoryId Category ID.
     * @return array List of nominations with vote counts.
     */
    public function getNominationsForCategory($categoryId)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT n.*, c.pseudonyme as candidate_name,
                   (SELECT COUNT(*) FROM vote v WHERE v.id_nomination = n.id_nomination) as vote_count
            FROM nomination n
            JOIN compte c ON n.id_compte = c.id_compte
            WHERE n.id_categorie = :id_categorie
            AND n.date_approbation IS NOT NULL
            ORDER BY n.libelle ASC
        ");

            $stmt->execute([':id_categorie' => $categoryId]);
            $nominations = $stmt->fetchAll();

            return $nominations;
        } catch (Exception $e) {
            error_log("Failed to retrieve nominations: " . $e->getMessage());
            return []; // Returns an empty array, NOT placeholders.
        }
    }

    /**
     * Retrieves a user's participation certificate for a category.
     * 
     * @param int $userId User account ID
     * @param int $categoryId Category ID
     * @return array|null Participation certificate or null
     */
    public function getParticipationCertificate($userId, $categoryId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id_certificat, hash_certificat, date_emission, id_compte, id_categorie FROM certificat_participation
                WHERE id_compte = :id_compte 
                AND id_categorie = :id_categorie
                ORDER BY date_emission DESC
                LIMIT 1
            ");

            $stmt->execute([
                ':id_compte' => $userId,
                ':id_categorie' => $categoryId
            ]);

            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Failed to retrieve certificate: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieves a user's voting history (anonymous information).
     * 
     * @param int $userId User account ID.
     * @return array Voting history.
     */
    public function getUserVotingHistory($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.nom as category_name,
                    cp.date_controle as vote_date,
                    cp.statut_a_vote as has_voted
                FROM controle_presence cp
                JOIN categorie c ON cp.id_categorie = c.id_categorie
                WHERE cp.id_compte = :id_compte
                ORDER BY cp.date_controle DESC
            ");

            $stmt->execute([':id_compte' => $userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Voting history error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Checks whether a category is active for voting.
     * 
     * @param int $categoryId Category ID.
     * @return bool True if the category is active.
     */
    public function isCategoryActive($categoryId)
    {
        try {
            $now = date('Y-m-d H:i:s');

            $stmt = $this->db->prepare("
                SELECT 
                    CASE 
                        WHEN date_debut_votes IS NOT NULL AND date_fin_votes IS NOT NULL THEN
                            :now BETWEEN date_debut_votes AND date_fin_votes
                        ELSE
                            :now BETWEEN e.date_debut AND e.date_fin
                    END as is_active
                FROM categorie c
                JOIN edition e ON c.id_edition = e.id_edition
                WHERE c.id_categorie = :id_categorie
                AND e.est_active = 1
            ");

            $stmt->execute([
                ':id_categorie' => $categoryId,
                ':now' => $now
            ]);

            $result = $stmt->fetch();
            return $result && $result['is_active'] == 1;
        } catch (Exception $e) {
            error_log("Category status check error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Encrypts a vote (simulation - use a more secure method in production).
     * 
     * @param int $nominationId Nomination ID
     * @param int $userId User ID
     * @return string Base64-encoded encrypted vote
     */
    public function encryptVote($nominationId, $userId)
    {
        // In production, use asymmetric encryption.
        // Here, we use a simulation for demonstration purposes.
        $data = [
            'nomination_id' => $nominationId,
            'timestamp' => time(),
            'user_hash' => hash('sha256', $userId . 'salt_' . time())
        ];

        return base64_encode(json_encode($data));
    }

    /**
     * Retrieves the category ID for a nomination.
     * 
     * @param int $nominationId Nomination ID.
     * @return int|null Category ID or null.
     */
    public function getCategoryIdFromNomination($nominationId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id_categorie FROM nomination 
                WHERE id_nomination = :id_nomination
            ");

            $stmt->execute([':id_nomination' => $nominationId]);
            $result = $stmt->fetch();

            return $result ? $result['id_categorie'] : null;
        } catch (Exception $e) {
            error_log("Failed to retrieve nomination category: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieves the information for a category.
     * 
     * @param int $categoryId Category ID.
     * @return array|null Category information or null.
     */
    public function getCategoryInfo($categoryId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, e.annee, e.nom as edition_nom
                FROM categorie c
                JOIN edition e ON c.id_edition = e.id_edition
                WHERE c.id_categorie = :id_categorie
            ");

            $stmt->execute([':id_categorie' => $categoryId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Failed to retrieve category information: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Validates an anonymous token.
     * 
     * @param string $token Token to validate.
     * @param int $userId User ID.
     * @param int $categoryId Category ID.
     * @return bool True if the token is valid.
     */
    public function validateToken($token, $userId, $categoryId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id_token FROM token_anonyme 
                WHERE token_value = :token 
                AND id_compte = :user_id 
                AND id_categorie = :category_id
                AND est_utilise = FALSE 
                AND date_expiration > NOW()
            ");

            $stmt->execute([
                ':token' => $token,
                ':user_id' => $userId,
                ':category_id' => $categoryId
            ]);

            return $stmt->fetch() !== false;
        } catch (Exception $e) {
            error_log("Token validation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves voting statistics.
     * 
     * @param int|null $userId User ID (optional).
     * @return array Voting statistics.
     */
    public function getVotingStatistics($userId = null)
    {
        try {
            $stats = [];

            // Total categories.
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM categorie");
            $result = $stmt->fetch();
            $stats['total_categories'] = $result['total'] ?? 0;

            // Total nominations.
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM nomination");
            $result = $stmt->fetch();
            $stats['total_nominations'] = $result['total'] ?? 0;

            // Total votes.
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM vote");
            $result = $stmt->fetch();
            $stats['total_votes'] = $result['total'] ?? 0;

            // User statistics if a user ID is provided.
            if ($userId) {
                // User votes.
                $stmt = $this->db->prepare("
                    SELECT COUNT(DISTINCT cp.id_categorie) as voted_categories
                    FROM controle_presence cp
                    WHERE cp.id_compte = :user_id AND cp.statut_a_vote = 1
                ");
                $stmt->execute([':user_id' => $userId]);
                $result = $stmt->fetch();
                $stats['user_voted_categories'] = $result['voted_categories'] ?? 0;

                // User certificates.
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as certificates
                    FROM certificat_participation
                    WHERE id_compte = :user_id
                ");
                $stmt->execute([':user_id' => $userId]);
                $result = $stmt->fetch();
                $stats['user_certificates'] = $result['certificates'] ?? 0;
            }

            return $stats;
        } catch (Exception $e) {
            error_log("Voting statistics error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieves the number of votes cast by a user.
     * 
     * @param int $userId User account ID.
     * @return int Number of votes.
     */
    public function getUserVotesCount($userId)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT v.id_vote) as count
            FROM vote v
            JOIN token_anonyme ta ON v.id_token = ta.id_token
            WHERE ta.id_compte = :id_compte
        ");

            $stmt->execute([':id_compte' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("getUserVotesCount error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Checks whether a user has voted in a specific category.
     * 
     * @param int $userId User account ID.
     * @param int $categoryId Category ID.
     * @return bool True if the user has voted in this category.
     */
    public function hasUserVotedInCategory($userId, $categoryId)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM vote v
            JOIN token_anonyme ta ON v.id_token = ta.id_token
            JOIN nomination n ON v.id_nomination = n.id_nomination
            WHERE ta.id_compte = :id_compte 
            AND n.id_categorie = :id_categorie
        ");

            $stmt->execute([
                ':id_compte' => $userId,
                ':id_categorie' => $categoryId
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($result['count'] ?? 0) > 0;
        } catch (PDOException $e) {
            error_log("hasUserVotedInCategory error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Checks whether a user has voted in active categories.
     * 
     * @param int $userId User account ID.
     * @return bool True if the user has voted in active categories.
     */
    public function hasUserVotedInActiveCategories($userId)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM vote v
            JOIN token_anonyme ta ON v.id_token = ta.id_token
            JOIN nomination n ON v.id_nomination = n.id_nomination
            JOIN categorie c ON n.id_categorie = c.id_categorie
            WHERE ta.id_compte = :id_compte 
            AND c.date_fin_votes > NOW()
        ");

            $stmt->execute([':id_compte' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return ($result['count'] ?? 0) > 0;
        } catch (PDOException $e) {
            error_log("hasUserVotedInActiveCategories error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves a user's recent votes.
     * 
     * @param int $userId User account ID.
     * @param int $limit Maximum number of votes to retrieve.
     * @return array List of recent votes.
     */
    public function getUserRecentVotes($userId, $limit = 5)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT 
                v.id_vote,
                v.date_heure_vote,
                n.libelle as nomination_libelle,
                c.nom as category_nom,
                c.id_categorie
            FROM vote v
            JOIN token_anonyme ta ON v.id_token = ta.id_token
            JOIN nomination n ON v.id_nomination = n.id_nomination
            JOIN categorie c ON n.id_categorie = c.id_categorie
            WHERE ta.id_compte = :id_compte
            ORDER BY v.date_heure_vote DESC
            LIMIT :limit
        ");

            $stmt->bindValue(':id_compte', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getUserRecentVotes error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Checks whether a user can vote in a category.
     * - Checks whether the voting period is open
     * - Checks whether the user has not already voted
     * - Checks whether nominations are available
     * 
     * @param int $userId User account ID.
     * @param int $categoryId Category ID.
     * @return array Detailed eligibility result.
     */
    public function canVoteInCategory($userId, $categoryId)
    {
        try {
            $now = date('Y-m-d H:i:s');

            $stmt = $this->db->prepare("
            SELECT 
                c.id_categorie,
                c.nom,
                CASE 
                    WHEN c.date_debut_votes IS NOT NULL AND c.date_fin_votes IS NOT NULL THEN
                        :now BETWEEN c.date_debut_votes AND c.date_fin_votes
                    ELSE
                        :now BETWEEN e.date_debut AND e.date_fin
                END as voting_open,
                
                IFNULL(cp.statut_a_vote, 0) as has_voted,
                
                COUNT(DISTINCT n.id_nomination) as nomination_count,
                
                -- Debug info
                c.date_debut_votes as cat_start,
                c.date_fin_votes as cat_end,
                e.date_debut as edition_start,
                e.date_fin as edition_end
                
            FROM categorie c
            JOIN edition e ON c.id_edition = e.id_edition
            LEFT JOIN controle_presence cp ON (
                cp.id_categorie = c.id_categorie 
                AND cp.id_compte = :user_id
            )
            LEFT JOIN nomination n ON c.id_categorie = n.id_categorie
            WHERE c.id_categorie = :category_id
            AND e.est_active = 1
            GROUP BY c.id_categorie
        ");

            $stmt->execute([
                ':category_id' => $categoryId,
                ':user_id' => $userId,
                ':now' => $now
            ]);

            $result = $stmt->fetch();

            if (!$result) {
                error_log("DEBUG: Category $categoryId not found or edition inactive");
                return [
                    'can_vote' => false,
                    'reason' => 'Catégorie introuvable ou édition inactive',
                    'debug' => ['category_id' => $categoryId, 'user_id' => $userId]
                ];
            }

            $canVote = ($result['voting_open'] == 1)
                && ($result['has_voted'] == 0)
                && ($result['nomination_count'] > 0);

            error_log("DEBUG canVoteInCategory: " . json_encode([
                'category_id' => $categoryId,
                'category_name' => $result['nom'],
                'voting_open' => $result['voting_open'],
                'has_voted' => $result['has_voted'],
                'nomination_count' => $result['nomination_count'],
                'can_vote' => $canVote,
                'dates' => [
                    'category' => ['start' => $result['cat_start'], 'end' => $result['cat_end']],
                    'edition' => ['start' => $result['edition_start'], 'end' => $result['edition_end']],
                    'now' => $now
                ]
            ]));

            return [
                'can_vote' => $canVote,
                'voting_open' => $result['voting_open'] == 1,
                'has_voted' => $result['has_voted'] == 1,
                'nominations_available' => $result['nomination_count'] > 0,
                'nomination_count' => $result['nomination_count'],
                'category_name' => $result['nom']
            ];
        } catch (Exception $e) {
            error_log("Vote eligibility check error: " . $e->getMessage());
            return [
                'can_vote' => false,
                'reason' => 'Erreur technique : ' . $e->getMessage()
            ];
        }
    }

    /**
     * Counts the number of categories in which a user has voted.
     * 
     * @param int $userId Voter ID.
     * @return int Number of voted categories.
     */
    public function getCategoriesVotedCount($userId)
    {
        try {
            $sql = "SELECT COUNT(DISTINCT id_categorie) as count 
                    FROM votes 
                    WHERE id_electeur = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            return $result ? $result['count'] : 0;
        } catch (Exception $e) {
            error_log("Failed to count voted categories: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Retrieves nominations for a category together with candidate images.
     * Returns a placeholder if no nomination has been approved.
     * 
     * @param int $categoryId Category ID.
     * @return array List of nominations with images.
     */
    public function getNominationsForCategoryWithImages($categoryId)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT n.*, c.pseudonyme as candidate_name,
                   co.photo_profil as candidate_image,
                   (SELECT COUNT(*) FROM vote v WHERE v.id_nomination = n.id_nomination) as vote_count
            FROM nomination n
            JOIN compte c ON n.id_compte = c.id_compte
            LEFT JOIN compte co ON n.id_compte = co.id_compte
            WHERE n.id_categorie = :id_categorie
            AND n.date_approbation IS NOT NULL
            ORDER BY n.libelle ASC
        ");

            $stmt->execute([':id_categorie' => $categoryId]);
            $nominations = $stmt->fetchAll();

            // If there is no nomination, create a placeholder.
            if (empty($nominations)) {
                return [
                    [
                        'id_nomination' => 0,
                        'libelle' => 'Aucune nomination approuvée',
                        'candidate_name' => 'Administrateur',
                        'vote_count' => 0,
                        'url_image' => 'assets/images/Nominees/nominee1.jpg',
                        'plateforme' => 'all'
                    ]
                ];
            }

            return $nominations;
        } catch (Exception $e) {
            error_log("Failed to retrieve nominations: " . $e->getMessage());
            return [];
        }
    }
}

\class_alias(VoteModel::class, 'Vote');