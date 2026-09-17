<?php
namespace App\Services;

require_once __DIR__ . '/../../config/upload.php';

use PDO;
use PDOException;
use App\Models\Nomination;
require_once __DIR__ . '/../Models/Nomination.php';
/**
 * Nomination management service.
 */
class NominationService
{
    private $pdo;
    
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
     * Retrieves all nominations.
     *
     * @return Nomination[] List of nominations as Nomination objects.
     */
    public function getAllNominations(): array
    {
        try {
            $sql = "SELECT n.*, c.nom as categorie_nom 
                    FROM nomination n
                    JOIN categorie c ON n.id_categorie = c.id_categorie
                    WHERE n.supprime_le IS NULL
                    ORDER BY n.libelle ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $nominations = [];
            foreach ($data as $row) {
                $nominations[] = new Nomination($row);
            }
            return $nominations;
            
        } catch (PDOException $e) {
            error_log("Failed to retrieve nominations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Retrieves nominations by category.
     *
     * @param int $categoryId Category ID.
     * @return Nomination[] List of nominations as Nomination objects.
     */
    public function getNominationsByCategory(int $categoryId): array
    {
        try {
            $sql = "SELECT n.*, c.nom as categorie_nom 
                    FROM nomination n
                    JOIN categorie c ON n.id_categorie = c.id_categorie
                    WHERE n.id_categorie = :category_id
                    AND n.supprime_le IS NULL
                    ORDER BY n.libelle ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':category_id' => $categoryId]);
            
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $nominations = [];
            foreach ($data as $row) {
                $nominations[] = new Nomination($row);
            }
            return $nominations;
            
        } catch (PDOException $e) {
            error_log("Failed to retrieve nominations by category: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Counts the votes for a nomination.
     *
     * @param int $nominationId Nomination ID.
     * @return int Number of votes.
     */
    public function countVotesForNomination(int $nominationId): int
    {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM vote 
                    WHERE id_nomination = :nomination_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':nomination_id' => $nominationId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Failed to count votes: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Retrieves all categories.
     *
     * @return array List of categories.
     */
    public function getAllCategories(): array
    {
        try {
            $sql = "SELECT id_categorie, nom 
                    FROM categorie 
                    ORDER BY nom ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Failed to retrieve categories: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Retrieves a category by ID.
     *
     * @param int $categoryId Category ID.
     * @return array|null Category data or null if not found.
     */
    public function getCategoryById(int $categoryId): ?array
    {
        try {
            $sql = "SELECT id_categorie, nom, description, image, plateforme_cible, limite_nomines, date_debut_votes, date_fin_votes, id_edition FROM categorie WHERE id_categorie = :category_id LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':category_id' => $categoryId]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            
        } catch (PDOException $e) {
            error_log("Failed to retrieve category: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Retrieves all unique platforms.
     *
     * @return array List of platforms.
     */
    public function getAllPlatforms(): array
    {
        try {
            $sql = "SELECT DISTINCT plateforme 
                    FROM nomination 
                    WHERE plateforme IS NOT NULL 
                    AND plateforme != ''
                    UNION
                    SELECT DISTINCT plateforme_cible 
                    FROM categorie 
                    WHERE plateforme_cible IS NOT NULL 
                    AND plateforme_cible != ''
                    ORDER BY 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return array_filter($results);
            
        } catch (PDOException $e) {
            error_log("Failed to retrieve platforms: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Creates a nomination from an application.
     *
     * @param int $candidatureId Application ID.
     * @param int $adminId Administrator ID.
     * @return bool Operation success.
     */
    public function createFromCandidature(int $candidatureId, int $adminId): bool
    {
        try {
            // Retrieve the application data.
            $sqlCandidature = "SELECT id_candidature, libelle, plateforme, url_contenu, image, argumentaire, date_soumission, statut, id_compte, id_categorie FROM candidature WHERE id_candidature = :id";
            $stmt = $this->pdo->prepare($sqlCandidature);
            $stmt->execute([':id' => $candidatureId]);
            $candidature = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$candidature) {
                return false;
            }

            // Insert into nomination.
            $sql = "INSERT INTO nomination 
                    (libelle, plateforme, url_contenu, url_image, argumentaire, date_approbation, 
                     id_candidature, id_categorie, id_compte, id_admin)
                    VALUES (:libelle, :plateforme, :url_contenu, :url_image, :argumentaire, NOW(), 
                            :id_candidature, :id_categorie, :id_compte, :id_admin)";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':libelle' => $candidature['libelle'],
                ':plateforme' => $candidature['plateforme'],
                ':url_contenu' => $candidature['url_contenu'],
                ':url_image' => $candidature['image'] ?? null,
                ':argumentaire' => $candidature['argumentaire'],
                ':id_candidature' => $candidatureId,
                ':id_categorie' => $candidature['id_categorie'],
                ':id_compte' => $candidature['id_compte'],
                ':id_admin' => $adminId
            ]);
        } catch (PDOException $e) {
            error_log("Failed to create nomination from application: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves a nomination by ID.
     *
     * @param int $id Nomination ID.
     * @return Nomination|null Nomination object or null if not found.
     */
    public function getNominationById(int $id): ?Nomination
    {
        try {
            $sql = "SELECT n.*, c.nom as categorie_nom 
                    FROM nomination n
                    JOIN categorie c ON n.id_categorie = c.id_categorie
                    WHERE n.id_nomination = :id
                    AND n.supprime_le IS NULL";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? new Nomination($data) : null;
        } catch (PDOException $e) {
            error_log("Failed to retrieve nomination: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Updates a nomination.
     *
     * @param int $id Nomination ID.
     * @param array $data Data to update.
     * @param array|null $imageFile New image file (optional).
     * @param int $adminId Administrator ID.
     * @return bool Whether the operation succeeded.
     */
    public function updateNomination(int $adminId, int $id, array $data, ?array $imageFile = null): bool
    {
        $nomination = $this->getNominationById($id);
        if (!$nomination) {
            return false;
        }

        $imagePath = $nomination->getUrlImage();

        if ($imageFile && !empty($imageFile['name'])) {
            $newPath = $this->uploadImage($imageFile);
            if ($newPath && $imagePath && file_exists(__DIR__ . '/../../public/' . $imagePath)) {
                unlink(__DIR__ . '/../../public/' . $imagePath);
            }
            $imagePath = $newPath;
        }

        if (isset($data['remove_image']) && $data['remove_image'] == '1') {
            if ($imagePath && file_exists(__DIR__ . '/../../public/' . $imagePath)) {
                unlink(__DIR__ . '/../../public/' . $imagePath);
            }
            $imagePath = null;
        }

        try {
            $updatedNomination = new Nomination($data);

            $sql = "UPDATE nomination SET
                    libelle = :libelle,
                    plateforme = :plateforme,
                    url_contenu = :url_contenu,
                    url_image = :url_image,
                    argumentaire = :argumentaire,
                    id_admin = :id_admin
                    WHERE id_nomination = :id";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':libelle' => $updatedNomination->getLibelle(),
                ':plateforme' => $updatedNomination->getPlateforme(),
                ':url_contenu' => $updatedNomination->getUrlContenu(),
                ':url_image' => $imagePath,
                ':argumentaire' => $updatedNomination->getArgumentaire(),
                ':id_admin' => $adminId,
                ':id' => $id
            ]);
        } catch (\InvalidArgumentException $e) {
            error_log("Validation failed during update: " . $e->getMessage());
            return false;
        } catch (PDOException $e) {
            error_log("Failed to update nomination: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Counts the total number of nominations.
     *
     * @return int Total count.
     */
    public function countAllNominations(): int
    {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM nomination WHERE supprime_le IS NULL");
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Failed to count nominations: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Retrieves a paginated list of nominations.
     *
     * @param int $page  1-based page number.
     * @param int $perPage Number of items per page.
     * @return Nomination[] Paginated list.
     */
    public function getNominationsPaginated(int $page, int $perPage = 20): array
    {
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT n.*, c.nom as categorie_nom
                    FROM nomination n
                    JOIN categorie c ON n.id_categorie = c.id_categorie
                    WHERE n.supprime_le IS NULL
                    ORDER BY n.libelle ASC
                    LIMIT :limit OFFSET :offset";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return array_map(fn($row) => new Nomination($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            error_log("Failed to retrieve paginated nominations: " . $e->getMessage());
            return [];
        }
    }

    public function deleteNomination(int $id): bool
    {
        $nomination = $this->getNominationById($id);
        if ($nomination && $nomination->getUrlImage() && file_exists(__DIR__ . '/../../public/' . $nomination->getUrlImage())) {
            unlink(__DIR__ . '/../../public/' . $nomination->getUrlImage());
        }

        try {
            $sql = "UPDATE nomination SET supprime_le = CURRENT_TIMESTAMP WHERE id_nomination = :id AND supprime_le IS NULL";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Failed to delete nomination: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Uploads an image for the nomination.
     *
     * @param array|null $file Image file.
     * @return string|null Image path or null on failure.
     */
    private function uploadImage(?array $file): ?string
    {
        if (!$file || empty($file['name']) || $file['error'] !== 0) return null;

        $uploadDir = __DIR__ . '/../../public/uploads/nominations/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif'
        ];
        $maxSize = 5 * 1024 * 1024;
        $extension = \uploadedImageExtension($file, $allowed);

        if ($file['size'] > $maxSize || $extension === null) return null;

        $filename = uniqid('nom_') . '.' . $extension;
        $dest = $uploadDir . $filename;

        if (is_uploaded_file($file['tmp_name']) && move_uploaded_file($file['tmp_name'], $dest)) {
            return 'uploads/nominations/' . $filename;
        }
        return null;
    }
}