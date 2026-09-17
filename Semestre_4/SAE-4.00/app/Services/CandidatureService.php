<?php
namespace App\Services;

use PDO;
use App\Models\Candidature;

/**
 * Service for managing applications.
 */
class CandidatureService
{
    private PDO $pdo;

    /**
        * Service constructor.
     *
        * @param PDO $pdo Database connection.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
        * Retrieves all applications.
     *
        * @return Candidature[] List of applications as Candidature objects.
     */
    public function getAllCandidatures(): array
    {
        $sql = "
            SELECT 
                ca.*,
                co.pseudonyme AS candidat_pseudonyme,
                co.email AS candidat_email,
                cat.nom AS categorie_nom,
                e.nom AS edition_nom
            FROM candidature ca
            JOIN compte co ON ca.id_compte = co.id_compte
            JOIN categorie cat ON ca.id_categorie = cat.id_categorie
            JOIN edition e ON cat.id_edition = e.id_edition
            WHERE ca.supprime_le IS NULL
            ORDER BY ca.date_soumission DESC
        ";
        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetchAll();
        $candidatures = [];
        foreach ($data as $row) {
            $candidatures[] = new Candidature($row);
        }
        return $candidatures;
    }

    /**
        * Retrieves an application by ID.
     *
        * @param int $id Application ID.
        * @return Candidature|null Candidature object or null if not found.
     */
    public function getCandidatureById(int $id): ?Candidature
    {
        $sql = "
            SELECT 
                ca.*,
                co.pseudonyme AS candidat_pseudonyme,
                co.email AS candidat_email,
                cat.nom AS categorie_nom,
                e.nom AS edition_nom
            FROM candidature ca
            JOIN compte co ON ca.id_compte = co.id_compte
            JOIN categorie cat ON ca.id_categorie = cat.id_categorie
            JOIN edition e ON cat.id_edition = e.id_edition
                        WHERE ca.id_candidature = :id
                            AND ca.supprime_le IS NULL
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();
        return $data ? new Candidature($data) : null;
    }

    /**
        * Updates an application's status.
     *
        * @param int $id Application ID.
        * @param string $statut New status.
        * @return bool Operation success.
     */
    public function updateStatus(int $id, string $statut): bool
    {
        $sql = "UPDATE candidature SET statut = :statut WHERE id_candidature = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['statut' => $statut, 'id' => $id]);
    }

    /**
        * Deletes an application.
     *
        * @param int $id Application ID.
        * @return bool Operation success.
     */
    public function deleteCandidature(int $id): bool
    {
        $sql = "UPDATE candidature SET supprime_le = CURRENT_TIMESTAMP WHERE id_candidature = :id AND supprime_le IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    /**
        * Retrieves application statistics for a user.
     *
        * @param int $userId User ID.
        * @return array Statistics.
     */
    public function getCandidatureStats(int $userId): array
    {
        $sql = "SELECT 
            COUNT(CASE WHEN statut = 'En attente' THEN 1 END) as pending,
            COUNT(CASE WHEN statut = 'Approuvée' THEN 1 END) as approved,
            COUNT(CASE WHEN statut = 'Rejetée' THEN 1 END) as rejected,
            COUNT(*) as total
            FROM candidature 
            WHERE id_compte = :userId";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        $result = $stmt->fetch();
        
        return $result ?: [
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'total' => 0
        ];
    }

    /**
        * Retrieves applications by category.
     *
        * @param int $categoryId Category ID.
        * @return Candidature[] List of applications.
     */
    public function getCandidaturesByCategory(int $categoryId): array
    {
        $sql = "SELECT c.*, co.pseudonyme
                FROM candidature c
                JOIN compte co ON c.id_compte = co.id_compte
                WHERE c.id_categorie = :categoryId";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':categoryId' => $categoryId]);
        $data = $stmt->fetchAll();
        $candidatures = [];
        foreach ($data as $row) {
            $candidatures[] = new Candidature($row);
        }
        return $candidatures;
    }

    /**
        * Retrieves applications by status.
     *
        * @param string $status Status.
        * @return Candidature[] List of applications.
     */
    public function getCandidaturesByStatus(string $status): array
    {
        $sql = "SELECT c.*, co.pseudonyme, cat.nom as categorie_nom
                FROM candidature c
                JOIN compte co ON c.id_compte = co.id_compte
                JOIN categorie cat ON c.id_categorie = cat.id_categorie
                WHERE c.statut = :status
                ORDER BY c.date_soumission DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':status' => $status]);
        $data = $stmt->fetchAll();
        $candidatures = [];
        foreach ($data as $row) {
            $candidatures[] = new Candidature($row);
        }
        return $candidatures;
    }

    /**
        * Approves an application.
     *
        * @param int $candidatureId Application ID.
        * @param int $adminId Admin ID.
        * @return bool Operation success.
     */
    public function approveCandidature(int $candidatureId, int $adminId): bool
    {
        $sql = "UPDATE candidature 
                SET statut = 'Approuvée' 
                WHERE id_candidature = :id
                AND statut = 'En attente'";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $candidatureId]);
    }

    /**
        * Rejects an application.
     *
        * @param int $candidatureId Application ID.
        * @param int $adminId Admin ID.
        * @return bool Operation success.
     */
    public function rejectCandidature(int $candidatureId, int $adminId): bool
    {
        $sql = "UPDATE candidature 
                SET statut = 'Rejetée' 
                WHERE id_candidature = :id
                AND statut = 'En attente'";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $candidatureId]);
    }

    /**
        * Checks whether the candidate already has an application in the category.
     *
        * @param int $userId Candidate ID.
        * @param int $categoryId Category ID.
     * @return bool
     */
    public function hasCandidatureInCategory(int $userId, int $categoryId): bool
    {
        $sql = "SELECT COUNT(*) as count 
                FROM candidature 
                WHERE id_compte = :userId 
                AND id_categorie = :categoryId
                AND statut != 'Rejetée'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':userId' => $userId,
            ':categoryId' => $categoryId
        ]);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }

    /**
        * Retrieves applications by edition.
     *
        * @param int $editionId Edition ID.
        * @return Candidature[] List of applications.
     */
    public function getCandidaturesByEdition(int $editionId): array
    {
        $sql = "SELECT c.*, co.pseudonyme, cat.nom as categorie_nom
                FROM candidature c
                JOIN compte co ON c.id_compte = co.id_compte
                JOIN categorie cat ON c.id_categorie = cat.id_categorie
                WHERE cat.id_edition = :editionId
                ORDER BY c.date_soumission DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':editionId' => $editionId]);
        $data = $stmt->fetchAll();
        $candidatures = [];
        foreach ($data as $row) {
            $candidatures[] = new Candidature($row);
        }
        return $candidatures;
    }

    /**
        * Retrieves statistics by edition.
     *
        * @param int $editionId Edition ID.
        * @return array Statistics.
     */
    /**
     * Counts the total number of candidatures.
     *
     * @return int Total count.
     */
    public function countAllCandidatures(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM candidature WHERE supprime_le IS NULL");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Returns global status counts for all candidatures.
     *
     * @return array{total: int, pending: int, approved: int, rejected: int}
     */
    public function getStatusStats(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                COUNT(*) AS total,
                COUNT(CASE WHEN statut = 'En attente' THEN 1 END) AS pending,
                COUNT(CASE WHEN statut = 'Approv\u00e9e' THEN 1 END) AS approved,
                COUNT(CASE WHEN statut = 'Rejet\u00e9e' THEN 1 END) AS rejected
            FROM candidature
            WHERE supprime_le IS NULL
        ");
        $row = $stmt->fetch();
        return [
            'total'    => (int) $row['total'],
            'pending'  => (int) $row['pending'],
            'approved' => (int) $row['approved'],
            'rejected' => (int) $row['rejected'],
        ];
    }

    /**
     * Retrieves a paginated list of candidatures.
     *
     * @param int $page  1-based page number.
     * @param int $perPage Number of items per page.
     * @return Candidature[] Paginated list.
     */
    public function getCandidaturesPaginated(int $page, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $sql = "
            SELECT 
                ca.*,
                co.pseudonyme AS candidat_pseudonyme,
                co.email AS candidat_email,
                cat.nom AS categorie_nom,
                e.nom AS edition_nom
            FROM candidature ca
            JOIN compte co ON ca.id_compte = co.id_compte
            JOIN categorie cat ON ca.id_categorie = cat.id_categorie
            JOIN edition e ON cat.id_edition = e.id_edition
            WHERE ca.supprime_le IS NULL
            ORDER BY ca.date_soumission DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return array_map(fn($row) => new Candidature($row), $stmt->fetchAll());
    }

    public function getEditionStats(int $editionId): array
    {
        $sql = "SELECT 
                COUNT(CASE WHEN c.statut = 'En attente' THEN 1 END) as pending,
                COUNT(CASE WHEN c.statut = 'Approuvée' THEN 1 END) as approved,
                COUNT(CASE WHEN c.statut = 'Rejetée' THEN 1 END) as rejected,
                COUNT(DISTINCT c.id_compte) as unique_candidates,
                COUNT(*) as total
                FROM candidature c
                JOIN categorie cat ON c.id_categorie = cat.id_categorie
                WHERE cat.id_edition = :editionId";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':editionId' => $editionId]);
        return $stmt->fetch();
    }


    
}

