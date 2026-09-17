<?php

namespace App\Services;

require_once __DIR__ . '/../../config/upload.php';

use PDO;
use PDOException;
use App\Models\Categorie;

/**
 * Category management service.
 */
class CategorieService
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
     * Retrieves all categories for an edition.
     *
     * @param int $editionId Edition ID.
     * @return Categorie[] List of categories as Categorie objects.
     */
    public function getAllCategoriesByEdition(int $editionId): array
    {
        try {
            $sql = "SELECT id_categorie, nom, description, image, plateforme_cible, limite_nomines, date_debut_votes, date_fin_votes, id_edition FROM categorie 
                    WHERE id_edition = :edition_id 
                    ORDER BY nom ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':edition_id' => $editionId]);

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $categories = [];
            foreach ($data as $row) {
                $categories[] = new Categorie($row);
            }
            return $categories;
        } catch (PDOException $e) {
            error_log("Category retrieval error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieves all categories.
     *
     * @return Categorie[] List of categories as Categorie objects.
     */
    public function getAllCategories(): array
    {
        $sql = "
            SELECT 
                c.*,
                e.nom AS edition_nom,
                e.annee AS edition_annee,
                COALESCE(cand.nb_candidatures, 0) AS nb_candidatures,
                COALESCE(nom.nb_nominations, 0) AS nb_nominations
            FROM categorie c
            LEFT JOIN edition e ON c.id_edition = e.id_edition
            LEFT JOIN (SELECT id_categorie, COUNT(*) AS nb_candidatures FROM candidature GROUP BY id_categorie) cand 
                ON c.id_categorie = cand.id_categorie
            LEFT JOIN (SELECT id_categorie, COUNT(*) AS nb_nominations FROM nomination GROUP BY id_categorie) nom 
                ON c.id_categorie = nom.id_categorie
            ORDER BY c.nom ASC
        ";

        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetchAll();
        $categories = [];
        foreach ($data as $row) {
            $categories[] = new Categorie($row);
        }
        return $categories;
    }

    /**
     * Counts nominations by category.
     *
     * @param int $categoryId Category ID.
     * @return int Number of nominations.
     */
    public function countNominationsByCategory(int $categoryId): int
    {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM nomination 
                    WHERE id_categorie = :category_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':category_id' => $categoryId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Nomination count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Retrieves a category by its ID.
     *
     * @param int $categoryId Category ID.
     * @return Categorie|null Categorie object or null if not found.
     */
    public function getCategoryById(int $categoryId): ?Categorie
    {
        try {
            $sql = "SELECT id_categorie, nom, description, image, plateforme_cible, limite_nomines, date_debut_votes, date_fin_votes, id_edition FROM categorie WHERE id_categorie = :category_id LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':category_id' => $categoryId]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? new Categorie($data) : null;
        } catch (PDOException $e) {
            error_log("Category retrieval error: " . $e->getMessage());
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
            $sql = "SELECT DISTINCT plateforme_cible 
                    FROM categorie 
                    WHERE plateforme_cible IS NOT NULL";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Platform retrieval error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Creates a new category.
     *
     * @param array $data Category data.
     * @param array|null $imageFile Image file.
     * @return bool Operation success.
     */
    public function createCategory(array $data, ?array $imageFile = null): bool
    {
        try {
            $category = new Categorie($data);
            $imagePath = $this->uploadImage($imageFile);

            $sql = "INSERT INTO categorie (nom, description, image, plateforme_cible, date_debut_votes, date_fin_votes, id_edition, limite_nomines)
                    VALUES (:nom, :description, :image, :plateforme_cible, :date_debut_votes, :date_fin_votes, :id_edition, :limite_nomines)";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':nom' => $category->getNom(),
                ':description' => $category->getDescription(),
                ':image' => $imagePath,
                ':plateforme_cible' => $category->getPlateformeCible(),
                ':date_debut_votes' => $category->getDateDebutVotes(),
                ':date_fin_votes' => $category->getDateFinVotes(),
                ':id_edition' => $category->getIdEdition(),
                ':limite_nomines' => $category->getLimiteNomines()
            ]);
        } catch (\InvalidArgumentException $e) {
            error_log("Validation failed: " . $e->getMessage());
            return false;
        } catch (PDOException $e) {
            error_log("Category creation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates a category.
     *
     * @param int $id Category ID.
     * @param array $data New data.
     * @param array|null $imageFile New image.
     * @return bool Operation success.
     */
    public function updateCategory(int $id, array $data, ?array $imageFile = null, bool $removeImage = false): bool
    {
        $category = $this->getCategoryById($id);
        if (!$category) return false;

        $existingImagePath = $category->getImage();
        $imagePath = $existingImagePath;

        if ($removeImage && $existingImagePath && file_exists(__DIR__ . '/../../public/' . $existingImagePath)) {
            unlink(__DIR__ . '/../../public/' . $existingImagePath);
            $imagePath = null;
        }

        if ($imageFile && !empty($imageFile['name'])) {
            $newPath = $this->uploadImage($imageFile);
            if ($newPath && !$removeImage && $existingImagePath && file_exists(__DIR__ . '/../../public/' . $existingImagePath)) {
                unlink(__DIR__ . '/../../public/' . $existingImagePath);
            }
            $imagePath = $newPath;
        }

        try {
            $updatedCategory = new Categorie($data);
            $sql = "UPDATE categorie SET
                    nom = :nom,
                    description = :description,
                    image = :image,
                    plateforme_cible = :plateforme_cible,
                    date_debut_votes = :date_debut_votes,
                    date_fin_votes = :date_fin_votes,
                    id_edition = :id_edition,
                    limite_nomines = :limite_nomines
                    WHERE id_categorie = :id";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':nom' => $updatedCategory->getNom(),
                ':description' => $updatedCategory->getDescription(),
                ':image' => $imagePath,
                ':plateforme_cible' => $updatedCategory->getPlateformeCible(),
                ':date_debut_votes' => $updatedCategory->getDateDebutVotes(),
                ':date_fin_votes' => $updatedCategory->getDateFinVotes(),
                ':id_edition' => $updatedCategory->getIdEdition(),
                ':limite_nomines' => $updatedCategory->getLimiteNomines(),
                ':id' => $id
            ]);
        } catch (\InvalidArgumentException $e) {
            error_log("Validation failed: " . $e->getMessage());
            return false;
        } catch (PDOException $e) {
            error_log("Category update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a category.
     *
     * @param int $id Category ID.
     * @return bool Operation success.
     */
    public function deleteCategorie(int $id): bool
    {
        $category = $this->getCategoryById($id);
        if ($category && $category->getImage() && file_exists(__DIR__ . '/../../public/' . $category->getImage())) {
            unlink(__DIR__ . '/../../public/' . $category->getImage());
        }

        $sql = "DELETE FROM categorie WHERE id_categorie = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Uploads an image.
     *
     * @param array|null $file Image file.
     * @return string|null Image path or null on failure.
     */
    private function uploadImage(?array $file): ?string
    {
        if (!$file || empty($file['name']) || $file['error'] !== 0) return null;

        $uploadDir = __DIR__ . '/../../public/uploads/categories/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif'
        ];
        $maxSize = 2 * 1024 * 1024;
        $extension = \uploadedImageExtension($file, $allowed);

        if ($file['size'] > $maxSize || $extension === null) return null;

        $filename = uniqid('cat_') . '.' . $extension;
        $dest = $uploadDir . $filename;

        if (is_uploaded_file($file['tmp_name']) && move_uploaded_file($file['tmp_name'], $dest)) {
            return 'uploads/categories/' . $filename;
        }
        return null;
    }
}