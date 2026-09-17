<?php
namespace App\Services;

use PDO;
use PDOException;

/**
 * Results management service for the Social Media Awards.
 * @description Provides methods to retrieve results, statistics, and winners
 */
class ResultsService
{
    private $pdo;

    private function logError(string $message): void
    {
        $appEnv = getenv('APP_ENV');
        if (is_string($appEnv) && strtolower($appEnv) === 'test') {
            return;
        }
        error_log($message);
    }
    
    /**
     * Constructor with PDO dependency injection.
     * @param PDO $pdo Database connection instance
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * Retrieves the most recent active edition.
     * @return array|null Edition data or null if none exists
     */
    public function getLatestEdition(): ?array
    {
        try {
            $sql = "SELECT id_edition, annee, nom, date_debut_candidatures, date_fin_candidatures, date_debut, date_fin, est_active, theme, image, description FROM edition 
                    WHERE est_active = 1 
                    ORDER BY annee DESC 
                    LIMIT 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $edition = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $edition ?: $this->getDefaultEdition();
            
        } catch (PDOException $e) {
            $this->logError("Error while retrieving the edition: " . $e->getMessage());
            return $this->getDefaultEdition();
        }
    }
    
    /**
     * Default data when no edition is found.
     * @return array Default edition
     */
    private function getDefaultEdition(): array
    {
        return [
            'id_edition' => 1,
            'annee' => date('Y'),
            'nom' => 'Social Media Awards ' . date('Y'),
            'est_active' => 1
        ];
    }
    

    
    /**
     * Retrieves the grand winners (top 3) for an edition.
     * @param int $editionId Edition ID
     * @return array Array of winners with their information
     */
    public function getGrandWinners(int $editionId): array
    {
        return $this->calculateWinnersFromVotes($editionId);
    }
    
    /**
     * Calculates winners from the votes (fallback method).
     * @param int $editionId Edition ID
     * @return array Array of calculated winners
     */
    private function calculateWinnersFromVotes(int $editionId): array
    {
        try {
            // MySQL-compatible version without ROW_NUMBER().
            $sql = "SELECT 
                        n.id_nomination,
                        n.libelle AS nom_nomination,
                        n.url_image AS image,
                        c.nom AS categorie,
                        COUNT(v.id_vote) AS total_votes,
                        c.plateforme_cible AS plateforme
                    FROM nomination n
                    JOIN categorie c ON n.id_categorie = c.id_categorie
                    LEFT JOIN vote v ON n.id_nomination = v.id_nomination
                    WHERE c.id_edition = :edition_id
                    GROUP BY n.id_nomination, n.libelle, n.url_image, c.nom, c.plateforme_cible
                    ORDER BY total_votes DESC
                    LIMIT 3";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':edition_id' => $editionId]);
            
            $winners = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Add the rank manually.
            $rank = 1;
            foreach ($winners as &$winner) {
                $winner['rang'] = $rank;
                $rank++;
            }
            
            return $winners;
            
        } catch (PDOException $e) {
            $this->logError("Error while calculating winners from votes: " . $e->getMessage());
            return $this->getDefaultWinners();
        }
    }
    
    /**
     * Default winners (for demonstration).
     * @return array Default winners
     */
    private function getDefaultWinners(): array
    {
        return [
            [
                'id_nomination' => 0,
                'nom_nomination' => 'En attente des premiers votes',
                'image' => 'assets/images/default-winner.jpg',
                'categorie' => 'Résultats en préparation',
                'total_votes' => 0,
                'plateforme' => 'all',
                'rang' => 1
            ]
        ];
    }
    
    /**
     * Retrieves results by category for an edition.
     * @param int $editionId Edition ID
     * @return array Results array by category
     */
    public function getResultsByCategory(int $editionId): array
    {
        return $this->calculateCategoryResultsFromVotes($editionId);
    }
    
    /**
     * Organizes results by category.
     * @param array $results Raw results from the view
     * @return array Results organized by category
     */
    private function organizeResultsByCategory(array $results): array
    {
        $groupedResults = [];
        
        foreach ($results as $result) {
            $categoryName = $result['categorie_nom'];
            
            if (!isset($groupedResults[$categoryName])) {
                $groupedResults[$categoryName] = [
                    'categorie_nom' => $categoryName,
                    'plateforme' => $result['plateforme'],
                    'id_categorie' => $result['id_categorie'],
                    'winners' => []
                ];
            }

            if (count($groupedResults[$categoryName]['winners']) >= 3) {
                continue;
            }
            
            // Add the winner to the category.
            $rank = count($groupedResults[$categoryName]['winners']) + 1;
            $position = $this->getPositionFromRank($rank);
            $groupedResults[$categoryName]['winners'][] = [
                'nom_nomination' => $result['nom_nomination'],
                'vote_count' => $result['vote_count'],
                'rang' => $rank,
                'position' => $position,
                'medal' => $this->getMedalEmoji($position),
                'vote_percentage' => 0 // Will be calculated later.
            ];
        }
        
        // Calculate totals and percentages.
        foreach ($groupedResults as &$category) {
            $category['total_votes_categorie'] = array_sum(array_column($category['winners'], 'vote_count'));
            $category['nb_nominations'] = count($category['winners']);
            
            // Calculate percentages.
            foreach ($category['winners'] as &$winner) {
                $winner['vote_percentage'] = $category['total_votes_categorie'] > 0 
                    ? round(($winner['vote_count'] / $category['total_votes_categorie']) * 100, 1)
                    : 0;
            }
        }
        
        return array_values($groupedResults);
    }
    
    /**
     * Calculates category results from the votes.
     * @param int $editionId Edition ID
     * @return array Calculated results
     */
    private function calculateCategoryResultsFromVotes(int $editionId): array
    {
        try {
            // Single query: retrieve all categories with their top 3 nominations (avoids N+1).
            $sql = "SELECT
                        id_categorie,
                        categorie_nom,
                        plateforme,
                        id_nomination,
                        nom_nomination,
                        vote_count,
                        rang_in_cat
                    FROM (
                        SELECT
                            c.id_categorie,
                            c.nom          AS categorie_nom,
                            c.plateforme_cible AS plateforme,
                            n.id_nomination,
                            n.libelle      AS nom_nomination,
                            COUNT(v.id_vote) AS vote_count,
                            RANK() OVER (
                                PARTITION BY c.id_categorie
                                ORDER BY COUNT(v.id_vote) DESC
                            ) AS rang_in_cat
                        FROM categorie c
                        LEFT JOIN nomination n ON n.id_categorie = c.id_categorie
                        LEFT JOIN vote v ON v.id_nomination = n.id_nomination
                        WHERE c.id_edition = :edition_id
                        GROUP BY c.id_categorie, c.nom, c.plateforme_cible,
                                 n.id_nomination, n.libelle
                    ) ranked
                    WHERE rang_in_cat <= 3
                    ORDER BY id_categorie, rang_in_cat";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':edition_id' => $editionId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group rows by category and compute totals/percentages.
            $byCategory = [];
            foreach ($rows as $row) {
                $catId = $row['id_categorie'];
                if (!isset($byCategory[$catId])) {
                    $byCategory[$catId] = [
                        'categorie_nom' => $row['categorie_nom'],
                        'plateforme'    => $row['plateforme'],
                        'id_categorie'  => $catId,
                        'winners'       => [],
                    ];
                }
                if ($row['id_nomination'] === null) {
                    continue;
                }
                $rank = (int) $row['rang_in_cat'];
                $position = $this->getPositionFromRank($rank);
                $byCategory[$catId]['winners'][] = [
                    'nom_nomination' => $row['nom_nomination'],
                    'vote_count'     => (int) $row['vote_count'],
                    'rang'           => $rank,
                    'position'       => $position,
                    'medal'          => $this->getMedalEmoji($position),
                    'vote_percentage' => 0,
                ];
            }

            $results = [];
            foreach ($byCategory as &$category) {
                $totalVotes = array_sum(array_column($category['winners'], 'vote_count'));
                foreach ($category['winners'] as &$winner) {
                    $winner['vote_percentage'] = $totalVotes > 0
                        ? round(($winner['vote_count'] / $totalVotes) * 100, 1)
                        : 0;
                }
                $category['total_votes_categorie'] = $totalVotes;
                $category['nb_nominations']        = count($category['winners']);
                $results[] = $category;
            }

            return $results;

        } catch (PDOException $e) {
            $this->logError("Error while calculating categories from votes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Converts a numeric rank into a textual position.
     * @param int $rank Numeric rank (1, 2, 3...)
     * @return string Textual position
     */
    private function getPositionFromRank(int $rank): string
    {
        $positions = [1 => 'gold', 2 => 'silver', 3 => 'bronze'];
        return $positions[$rank] ?? 'participant';
    }
    
    /**
     * Returns the emoji matching the position.
     * @param string $position Position (gold, silver, bronze)
     * @return string Matching emoji
     */
    private function getMedalEmoji(string $position): string
    {
        $emojis = [
            'gold' => '🥇',
            'silver' => '🥈', 
            'bronze' => '🥉'
        ];
        
        return $emojis[$position] ?? '🏅';
    }
    
    /**
    * Calculates global statistics for an edition.
    * @param int $editionId Edition ID
    * @return array Statistics array
    */
    public function getGlobalStatistics(int $editionId): array
    {
        try {
            $sql = "SELECT 
                        COUNT(DISTINCT v.id_vote) AS total_votes,
                        COUNT(DISTINCT c.id_categorie) AS total_categories,
                        COUNT(DISTINCT n.id_nomination) AS total_nominations,
                        COUNT(DISTINCT cp.id_compte) AS total_voters,
                        COALESCE(
                            ROUND(
                                (COUNT(DISTINCT cp.id_compte) * 100.0) / 
                                NULLIF(
                                    (SELECT COUNT(DISTINCT id_compte) 
                                     FROM controle_presence 
                                     WHERE statut_a_vote = 1 
                                       AND id_categorie IN (
                                         SELECT id_categorie FROM categorie WHERE id_edition = :edition_id
                                       )), 
                                    0
                                ), 
                                1
                            ),
                            0
                        ) AS participation_rate
                    FROM categorie c
                    LEFT JOIN nomination n ON c.id_categorie = n.id_categorie
                    LEFT JOIN vote v ON n.id_nomination = v.id_nomination
                    LEFT JOIN controle_presence cp ON cp.id_categorie = c.id_categorie AND cp.statut_a_vote = 1
                    WHERE c.id_edition = :edition_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':edition_id' => $editionId]);
            
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // If it is still 0, try an alternative calculation.
            if (($stats['participation_rate'] ?? 0) == 0 && ($stats['total_voters'] ?? 0) > 0) {
                // Alternative calculation: (total voters / total registered accounts) * 100.
                $sqlAlternative = "SELECT 
                                    COUNT(DISTINCT cp.id_compte) as total_voters,
                                    (SELECT COUNT(DISTINCT id_compte) 
                                     FROM controle_presence 
                                     WHERE id_categorie IN (
                                       SELECT id_categorie FROM categorie WHERE id_edition = :edition_id
                                     )) as total_accounts
                                   FROM controle_presence cp
                                   WHERE cp.statut_a_vote = 1 
                                     AND cp.id_categorie IN (
                                       SELECT id_categorie FROM categorie WHERE id_edition = :edition_id2
                                     )";
                
                $stmt2 = $this->pdo->prepare($sqlAlternative);
                $stmt2->execute([
                    ':edition_id' => $editionId,
                    ':edition_id2' => $editionId
                ]);
                $altStats = $stmt2->fetch(PDO::FETCH_ASSOC);
                
                if (($altStats['total_accounts'] ?? 0) > 0) {
                    $participationRate = round(($altStats['total_voters'] / $altStats['total_accounts']) * 100, 1);
                    $stats['participation_rate'] = $participationRate;
                }
            }
            
            // Default values if null.
            return [
                'total_votes' => $stats['total_votes'] ?? 0,
                'total_categories' => $stats['total_categories'] ?? 0,
                'total_nominations' => $stats['total_nominations'] ?? 0,
                'total_voters' => $stats['total_voters'] ?? 0,
                'participation_rate' => $stats['participation_rate'] ?? 0
            ];
            
        } catch (PDOException $e) {
            $this->logError("Statistics calculation error: " . $e->getMessage());
            return [
                'total_votes' => 0,
                'total_categories' => 0,
                'total_nominations' => 0,
                'total_voters' => 0,
                'participation_rate' => 0
            ];
        }
    }
    
    /**
     * Retrieves the list of available editions.
     * @return array List of editions
     */
    public function getAvailableEditions(): array
    {
        try {
            $sql = "SELECT id_edition, annee, nom 
                    FROM edition 
                    ORDER BY annee DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $editions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // If no edition exists, create a default one.
            if (empty($editions)) {
                return [
                    [
                        'id_edition' => 1,
                        'annee' => date('Y'),
                        'nom' => 'Social Media Awards ' . date('Y')
                    ]
                ];
            }
            
            return $editions;
            
        } catch (PDOException $e) {
            $this->logError("Edition retrieval error: " . $e->getMessage());
            return [
                [
                    'id_edition' => 1,
                    'annee' => date('Y'),
                    'nom' => 'Social Media Awards ' . date('Y')
                ]
            ];
        }
    }
    
    /**
     * Updates the results in the resultat table.
     * @param int $editionId Edition ID
     * @return bool Operation success
     */
    public function updateResultsTable(int $editionId): bool
    {
        try {
            // Simple MySQL-compatible version.
            $sql = "INSERT INTO resultat (nombre_votes, rang, id_nomination, id_edition)
                    SELECT 
                        vote_counts.vote_count,
                        @row_number := CASE 
                            WHEN @current_category = vote_counts.id_categorie 
                            THEN @row_number + 1 
                            ELSE 1 
                        END AS rang,
                        vote_counts.id_nomination,
                        :edition_id
                    FROM (
                        SELECT 
                            n.id_nomination,
                            n.id_categorie,
                            COUNT(v.id_vote) AS vote_count
                        FROM nomination n
                        LEFT JOIN vote v ON n.id_nomination = v.id_nomination
                        JOIN categorie c ON n.id_categorie = c.id_categorie
                        WHERE c.id_edition = :edition_id2
                        GROUP BY n.id_nomination, n.id_categorie
                    ) AS vote_counts
                    CROSS JOIN (SELECT @row_number := 0, @current_category := 0) AS vars
                    ORDER BY vote_counts.id_categorie, vote_counts.vote_count DESC";
            
            // First remove the old results.
            $deleteSql = "DELETE FROM resultat WHERE id_edition = :edition_id";
            $deleteStmt = $this->pdo->prepare($deleteSql);
            $deleteStmt->execute([':edition_id' => $editionId]);
            
            // Then insert the new ones.
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':edition_id' => $editionId,
                ':edition_id2' => $editionId
            ]);
            
            return true;
            
        } catch (PDOException $e) {
            $this->logError("Results update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Retrieves all editions with their statistics.
     * @return array Editions with statistics
     */
    public function getAllEditionsWithStats(): array
    {
        try {
            $sql = "SELECT 
                        e.id_edition,
                        e.annee,
                        e.nom,
                        e.est_active,
                        COUNT(DISTINCT c.id_categorie) AS nb_categories,
                        COUNT(DISTINCT n.id_nomination) AS nb_nominations,
                        COUNT(DISTINCT v.id_vote) AS nb_votes
                    FROM edition e
                    LEFT JOIN categorie c ON e.id_edition = c.id_edition
                    LEFT JOIN nomination n ON c.id_categorie = n.id_categorie
                    LEFT JOIN vote v ON n.id_nomination = v.id_nomination
                    GROUP BY e.id_edition, e.annee, e.nom, e.est_active
                    ORDER BY e.annee DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->logError("Edition retrieval with stats error: " . $e->getMessage());
            return [];
        }
    }
    // Add these methods to the existing ResultsService.php file.

/**
 * Checks whether the edition is active (voting in progress).
 * @param int $editionId Edition ID
 * @return array Information about the edition status
 */
public function getEditionStatus(int $editionId): array
{
    try {
        $sql = "SELECT 
                    e.id_edition,
                    e.annee,
                    e.nom,
                    e.est_active,
                    e.date_debut,
                    e.date_fin,
                    e.date_debut_candidatures,
                    e.date_fin_candidatures,
                    NOW() as now_date
                FROM edition e
                WHERE e.id_edition = :edition_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':edition_id' => $editionId]);
        $edition = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$edition) {
            return [
                'active' => false,
                'message' => 'Edition not found',
                'dates' => null
            ];
        }

        // Use strtotime instead of DateTime to avoid namespace issues.
        $now = strtotime($edition['now_date']);
        $dateDebut = strtotime($edition['date_debut']);
        $dateFin = strtotime($edition['date_fin']);

        // The edition is active if: est_active = 1 AND now is between date_debut and date_fin.
        $isActive = ($edition['est_active'] == 1 && 
                    $now >= $dateDebut && 
                    $now <= $dateFin);

        // Check whether voting is finished.
        $votesFinished = ($now > $dateFin);
        
        // Check whether it is before voting starts.
        $votesNotStarted = ($now < $dateDebut);

        return [
            'active' => $isActive,
            'votes_finished' => $votesFinished,
            'votes_not_started' => $votesNotStarted,
            'status' => $edition['est_active'] ? 'active' : 'inactive',
            'date_debut' => $edition['date_debut'],
            'date_fin' => $edition['date_fin'],
            'date_debut_candidatures' => $edition['date_debut_candidatures'],
            'date_fin_candidatures' => $edition['date_fin_candidatures'],
            'nom' => $edition['nom'],
            'annee' => $edition['annee'],
            'message' => $isActive ? 'Voting in progress' : ($votesFinished ? 'Voting finished' : ($votesNotStarted ? 'Voting has not started yet' : 'Inactive edition'))
        ];
        
    } catch (PDOException $e) {
        $this->logError("Edition status retrieval error: " . $e->getMessage());
        return [
            'active' => false,
            'votes_finished' => false,
            'votes_not_started' => false,
            'message' => 'System error'
        ];
    }
}

/**
 * Retrieves only finished editions (for results)
 * @return array List of finished editions
 */
public function getFinishedEditions(): array
{
    try {
        $sql = "SELECT id_edition, annee, nom 
                FROM edition 
                WHERE (date_fin < NOW() OR est_active = 0)
                ORDER BY annee DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        $editions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // If no ended edition exists, return all editions.
        if (empty($editions)) {
            return $this->getAvailableEditions();
        }
        
        return $editions;
        
    } catch (PDOException $e) {
        $this->logError("Failed to retrieve ended editions: " . $e->getMessage());
        return $this->getAvailableEditions();
    }
}
}