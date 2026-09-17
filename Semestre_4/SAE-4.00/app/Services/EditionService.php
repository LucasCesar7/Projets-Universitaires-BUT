<?php

namespace App\Services;

require_once __DIR__ . '/../../config/upload.php';

use PDO;
use App\Models\Edition;

/**
 * Edition management service.
 */
class EditionService
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
     * Retrieves all editions.
     *
     * @return Edition[] List of editions as Edition objects.
     */
    public function getAllEditions(): array
    {
        $sql = "
        SELECT 
            e.*,
            COALESCE(cat.nb_categories, 0) AS nb_categories,
            COALESCE(cand.nb_candidatures, 0) AS nb_candidatures,
            COALESCE(vot.nb_votants, 0) AS nb_votants,
            CASE 
                WHEN NOW() >= e.date_debut_candidatures AND NOW() <= e.date_fin THEN 1
                ELSE 0
            END AS est_active_calculated
        FROM edition e
        LEFT JOIN (SELECT id_edition, COUNT(*) AS nb_categories FROM categorie GROUP BY id_edition) cat 
            ON e.id_edition = cat.id_edition
        LEFT JOIN (SELECT c.id_edition, COUNT(*) AS nb_candidatures 
                   FROM candidature cand JOIN categorie c ON cand.id_categorie = c.id_categorie 
                   GROUP BY c.id_edition) cand 
            ON e.id_edition = cand.id_edition
        LEFT JOIN (SELECT c.id_edition, COUNT(DISTINCT ta.id_compte) AS nb_votants 
                   FROM token_anonyme ta 
                   JOIN categorie c ON ta.id_categorie = c.id_categorie
                   GROUP BY c.id_edition) vot 
            ON e.id_edition = vot.id_edition
        GROUP BY e.id_edition
        ORDER BY e.annee DESC
    ";

        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetchAll();

        $editions = [];
        foreach ($data as $row) {
            $row['est_active'] = $row['est_active_calculated'];  // Override with the current calculation.
            unset($row['est_active_calculated']);  // Remove the temporary field.
            $editions[] = new Edition($row);
        }
        return $editions;
    }

    public function getActiveEditions(): array
    {
        $now = date('Y-m-d H:i:s');
        $sql = "
            SELECT 
                e.*,
                COALESCE(cat.nb_categories, 0) AS nb_categories,
                COALESCE(cand.nb_candidatures, 0) AS nb_candidatures,
                COALESCE(vot.nb_votants, 0) AS nb_votants,
                1 AS est_active_calculated
            FROM edition e
            LEFT JOIN (SELECT id_edition, COUNT(*) AS nb_categories FROM categorie GROUP BY id_edition) cat 
                ON e.id_edition = cat.id_edition
            LEFT JOIN (SELECT c.id_edition, COUNT(*) AS nb_candidatures 
                       FROM candidature cand JOIN categorie c ON cand.id_categorie = c.id_categorie 
                       GROUP BY c.id_edition) cand 
                ON e.id_edition = cand.id_edition
            LEFT JOIN (SELECT c.id_edition, COUNT(DISTINCT ta.id_compte) AS nb_votants 
                       FROM token_anonyme ta 
                       JOIN categorie c ON ta.id_categorie = c.id_categorie
                       WHERE ta.est_utilise = 1
                       GROUP BY c.id_edition) vot 
                ON e.id_edition = vot.id_edition
            WHERE e.date_fin_candidatures >= :now
            ORDER BY e.annee DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':now' => $now]);
        $data = $stmt->fetchAll();

        $editions = [];
        foreach ($data as $row) {
            $editions[] = new Edition($row);
        }
        return $editions;
    }

    /**
     * Retrieves an edition by ID.
     *
     * @param int $id Edition ID.
     * @return array|null Edition data or null if not found.
     */
    public function getEditionById(int $id): ?array
    {
        $sql = "SELECT id_edition, annee, nom, date_debut_candidatures, date_fin_candidatures, date_debut, date_fin, est_active, theme, image, description FROM edition WHERE id_edition = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Creates a new edition.
     *
     * @param array $data Edition data.
     * @param array|null $imageFile Image file.
     * @return bool Whether the operation succeeded.
     */
    public function createEdition(array $data, ?array $imageFile = null): bool
    {
        $checkSql = "SELECT id_edition FROM edition WHERE annee = :annee";
        $checkStmt = $this->pdo->prepare($checkSql);
        $checkStmt->execute([':annee' => $data['annee']]);

        if ($checkStmt->fetch()) {
            throw new \Exception("Une édition avec l'année {$data['annee']} existe déjà.");
        }

        $imagePath = $this->uploadImage($imageFile);

        try {
            $edition = new Edition($data);

            // Automatically compute est_active from date_fin_candidatures.
            $dateFinCandidatures = $edition->getDateFinCandidatures();
            $now = new \DateTime();
            $finCandidatures = new \DateTime($dateFinCandidatures);
            $estActive = ($now <= $finCandidatures) ? 1 : 0;

            $sql = "INSERT INTO edition 
                    (annee, nom, description, image, date_debut_candidatures, date_fin_candidatures, 
                     date_debut, date_fin, theme, est_active)
                    VALUES (:annee, :nom, :description, :image, :date_debut_candidatures, 
                            :date_fin_candidatures, :date_debut, :date_fin, :theme, :est_active)";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':annee' => $edition->getAnnee(),
                ':nom' => $edition->getNom(),
                ':description' => $edition->getDescription(),
                ':image' => $imagePath,
                ':date_debut_candidatures' => $edition->getDateDebutCandidatures(),
                ':date_fin_candidatures' => $edition->getDateFinCandidatures(),
                ':date_debut' => $edition->getDateDebut(),
                ':date_fin' => $edition->getDateFin(),
                ':theme' => $edition->getTheme(),
                ':est_active' => $estActive
            ]);
        } catch (\InvalidArgumentException $e) {
            error_log("Validation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an edition.
     *
     * @param int $id Edition ID.
     * @param array $data New data.
     * @param array|null $imageFile New image file.
     * @return bool Whether the operation succeeded.
     */
    public function updateEdition(int $id, array $data, ?array $imageFile = null): bool
    {
        $editionData = $this->getEditionById($id);
        if (!$editionData) return false;

        $edition = new Edition($editionData);
        $imagePath = $edition->getImage();

        if ($imageFile && !empty($imageFile['name'])) {
            $newPath = $this->uploadImage($imageFile);
            if ($newPath && $imagePath && file_exists(__DIR__ . '/../../public/' . $imagePath)) {
                unlink(__DIR__ . '/../../public/' . $imagePath);
            }
            $imagePath = $newPath;
        }

        if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
            if ($imagePath && file_exists(__DIR__ . '/../../public/' . $imagePath)) {
                unlink(__DIR__ . '/../../public/' . $imagePath);
            }
            $imagePath = null;
        }

        try {
            $updatedEdition = new Edition($data);

            // Recompute est_active from the new date_fin_candidatures.
            $dateFinCandidatures = $updatedEdition->getDateFinCandidatures();
            $now = new \DateTime();
            $finCandidatures = new \DateTime($dateFinCandidatures);
            $estActive = ($now <= $finCandidatures) ? 1 : 0;

            $sql = "UPDATE edition SET
                    annee = :annee,
                    nom = :nom,
                    description = :description,
                    image = :image,
                    date_debut_candidatures = :date_debut_candidatures,
                    date_fin_candidatures = :date_fin_candidatures,
                    date_debut = :date_debut,
                    date_fin = :date_fin,
                    theme = :theme,
                    est_active = :est_active
                    WHERE id_edition = :id";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':annee' => $updatedEdition->getAnnee(),
                ':nom' => $updatedEdition->getNom(),
                ':description' => $updatedEdition->getDescription(),
                ':image' => $imagePath,
                ':date_debut_candidatures' => $updatedEdition->getDateDebutCandidatures(),
                ':date_fin_candidatures' => $updatedEdition->getDateFinCandidatures(),
                ':date_debut' => $updatedEdition->getDateDebut(),
                ':date_fin' => $updatedEdition->getDateFin(),
                ':theme' => $updatedEdition->getTheme(),
                ':est_active' => $estActive,
                ':id' => $id
            ]);
        } catch (\InvalidArgumentException $e) {
            error_log("Validation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes an edition.
     *
     * @param int $id Edition ID.
     * @return bool Whether the operation succeeded.
     */
    public function deleteEdition(int $id): bool
    {
        $editionData = $this->getEditionById($id);
        if ($editionData) {
            $edition = new Edition($editionData);
            if ($edition->getImage() && file_exists(__DIR__ . '/../../public/' . $edition->getImage())) {
                unlink(__DIR__ . '/../../public/' . $edition->getImage());
            }
        }

        $sql = "DELETE FROM edition WHERE id_edition = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Uploads an image for the edition.
     *
     * @param array|null $file Image file.
     * @return string|null Image path or null on failure.
     */
    private function uploadImage(?array $file): ?string
    {
        if (!$file || empty($file['name']) || $file['error'] !== 0) return null;

        $uploadDir = __DIR__ . '/../../public/uploads/editions/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif'
        ];
        $maxSize = 5 * 1024 * 1024;
        $extension = \uploadedImageExtension($file, $allowed);

        if ($file['size'] > $maxSize || $extension === null) return null;

        $filename = uniqid('edi_') . '.' . $extension;
        $dest = $uploadDir . $filename;

        if (is_uploaded_file($file['tmp_name']) && move_uploaded_file($file['tmp_name'], $dest)) {
            return 'uploads/editions/' . $filename;
        }
        return null;
    }

    /**
     * Retrieves the current active edition.
     *
     * @return array|null Active edition data or null.
     */
    public function getActiveEdition(): ?Edition
    {
        $now = date('Y-m-d H:i:s');
        $sql = "
            SELECT e.*,
                   COALESCE((SELECT COUNT(*) FROM categorie WHERE id_edition = e.id_edition), 0) AS nb_categories
            FROM edition e 
            WHERE ? >= e.date_debut_candidatures AND ? <= e.date_fin
            ORDER BY e.annee DESC
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$now, $now]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Edition($data) : null;
    }


public function updateAllEditionStatus(): bool
{
    try {
        $sql = "UPDATE edition 
                SET est_active = CASE 
                    WHEN NOW() >= date_debut_candidatures AND NOW() <= date_fin THEN 1 
                    ELSE 0 
                END";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute();
    } catch (\Exception $e) {
        error_log("Failed to update edition statuses: " . $e->getMessage());
        return false;
    }
}
    /**
     * Checks whether an edition is active.
     *
     * @param int $editionId Edition ID.
     * @return bool True if active, false otherwise.
     */
    public function isEditionActive(int $editionId): bool
    {
        $now = date('Y-m-d H:i:s');
        $sql = "
            SELECT COUNT(*) as count 
            FROM edition 
            WHERE id_edition = ? 
            AND ? >= date_debut_candidatures 
            AND ? <= date_fin
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$editionId, $now, $now]);
        return $stmt->fetchColumn() > 0;
    }
}
