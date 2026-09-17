<?php
// app/Services/CandidatService.php

namespace App\Services;

require_once __DIR__ . '/../../config/upload.php';

use PDO;
use PDOException;
use App\Models\Candidature;
use App\Models\Categorie;
use App\Models\Edition;

/**
 * Candidate management service.
 */
class CandidatService
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
     * Retrieves candidate data by ID.
     *
     * @param int $userId Candidate ID.
     * @return array|null Candidate data or null if not found.
     */
    public function getCandidatById(int $userId): ?array
    {
        $sql = "SELECT 
                ca.id_compte,
                ca.nom_legal_ou_societe,
                ca.type_candidature,
                ca.est_nomine,
                co.pseudonyme,
                co.email,
                co.date_naissance,
                co.pays,
                co.genre,
                co.photo_profil,
                co.date_creation
                FROM candidat ca
                JOIN compte co ON ca.id_compte = co.id_compte
                WHERE ca.id_compte = :userId";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Updates the candidate data.
     *
     * @param int $userId Candidate ID.
     * @param array $data Data to update.
     * @return bool Operation success.
     */
    public function updateCandidat(int $userId, array $data): bool
    {
        // Update the candidat table.
        $sqlCandidat = "UPDATE candidat SET
            nom_legal_ou_societe = :nom_legal_ou_societe,
            type_candidature = :type_candidature
            WHERE id_compte = :userId";

        $stmtCandidat = $this->pdo->prepare($sqlCandidat);

        $successCandidat = $stmtCandidat->execute([
            ':nom_legal_ou_societe' => $data['nom_legal_ou_societe'] ?? null,
            ':type_candidature' => $data['type_candidature'] ?? 'Créateur',
            ':userId' => $userId
        ]);

        // Update the compte table - ONLY EXISTING FIELDS.
        $sqlCompte = "UPDATE compte SET
            pseudonyme = :pseudonyme,
            email = :email,
            photo_profil = :photo_profil,
            pays = :pays,
            genre = :genre,
            date_modification = NOW()
            WHERE id_compte = :userId";

        $stmtCompte = $this->pdo->prepare($sqlCompte);

        $successCompte = $stmtCompte->execute([
            ':pseudonyme' => $data['pseudonyme'],
            ':email' => $data['email'],
            ':photo_profil' => $data['photo_profil'] ?? null,
            ':pays' => $data['pays'] ?? null,
            ':genre' => $data['genre'] ?? null,
            ':userId' => $userId
        ]);

        return $successCandidat && $successCompte;
    }

    /**
        * Checks whether the user is a nominee (has approved applications).
     *
        * @param int $userId Candidate ID.
        * @return bool True if the user is a nominee, otherwise false.
     */
    public function isNominee(int $userId): bool
    {
        $sql = "SELECT COUNT(*) as count 
                FROM candidature 
                WHERE id_compte = :userId 
                AND statut = 'Approuvée'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        $result = $stmt->fetch();

        return $result['count'] > 0;
    }

    /**
        * Retrieves all active nominations for the user.
     *
        * @param int $userId Candidate ID.
        * @return array List of nominations.
     */
    public function getActiveNominations(int $userId): array
    {
        $sql = "SELECT n.*, cat.nom as categorie_nom, edi.nom as edition_nom,
                       cat.date_debut_votes, cat.date_fin_votes,
                       edi.date_debut, edi.date_fin
                FROM nomination n
                JOIN categorie cat ON n.id_categorie = cat.id_categorie
                JOIN edition edi ON cat.id_edition = edi.id_edition
                WHERE n.id_compte = :userId
                ORDER BY cat.date_fin_votes ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Selects a nomination from a nomination list.
     *
     * @param array $nominations Active nominations.
     * @param int|null $nominationId Requested nomination ID.
     * @return array|null Selected nomination or null if not found.
     */
    public function getSelectedNomination(array $nominations, ?int $nominationId = null): ?array
    {
        if (empty($nominations)) {
            return null;
        }

        if ($nominationId === null) {
            return $nominations[0];
        }

        foreach ($nominations as $nomination) {
            if ((int) ($nomination['id_nomination'] ?? 0) === $nominationId) {
                return $nomination;
            }
        }

        return null;
    }

    /**
     * Retrieves the voting status for a nomination.
     *
     * @param array $nomination Nomination data.
     * @return string Voting status ('in_progress', 'ended', 'not_started').
     */
    public function getVotingStatus(array $nomination): string
    {
        $now = new \DateTime();

        // First check the category-specific voting dates.
        if (isset($nomination['date_debut_votes']) && isset($nomination['date_fin_votes'])) {
            $startVotes = new \DateTime($nomination['date_debut_votes']);
            $endVotes = new \DateTime($nomination['date_fin_votes']);

            if ($now >= $startVotes && $now <= $endVotes) {
                return 'in_progress';
            } elseif ($now > $endVotes) {
                return 'ended';
            } else {
                return 'not_started';
            }
        }

        // Fallback: use the edition dates.
        if (isset($nomination['date_debut']) && isset($nomination['date_fin'])) {
            $startEdition = new \DateTime($nomination['date_debut']);
            $endEdition = new \DateTime($nomination['date_fin']);

            if ($now >= $startEdition && $now <= $endEdition) {
                return 'in_progress';
            } elseif ($now > $endEdition) {
                return 'ended';
            } else {
                return 'not_started';
            }
        }

        return 'not_started';
    }

    /**
     * Retrieves the current voting status from the first available nomination.
     *
     * @param array $nominations Active nominations.
     * @return string Voting status ('in_progress', 'ended', 'not_started').
     */
    public function getPrimaryNominationVotingStatus(array $nominations): string
    {
        $nomination = $this->getSelectedNomination($nominations);

        return $nomination ? $this->getVotingStatus($nomination) : 'not_started';
    }

    /**
     * Checks whether at least one nomination already has published results.
     *
     * @param array $nominations Active nominations.
     * @return bool True when a nomination has ended and exposes results.
     */
    public function hasAvailableNominationResults(array $nominations): bool
    {
        foreach ($nominations as $nomination) {
            if ($this->getVotingStatus($nomination) !== 'ended') {
                continue;
            }

            $nominationId = (int) ($nomination['id_nomination'] ?? 0);
            if ($nominationId > 0 && $this->getNominationResults($nominationId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieves the candidate statistics.
     *
     * @param int $userId Candidate ID.
     * @return array Candidate statistics.
     */
    public function getCandidatStats(int $userId): array
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
     * Retrieves all applications submitted by the user.
     *
     * @param int $userId Candidate ID.
     * @return array List of applications.
     */
    public function getUserCandidatures(int $userId): array
    {
        $sql = "SELECT c.*, cat.nom as categorie_nom, ed.nom as edition_nom
                FROM candidature c
                JOIN categorie cat ON c.id_categorie = cat.id_categorie
                JOIN edition ed ON cat.id_edition = ed.id_edition
                WHERE c.id_compte = :userId
                ORDER BY c.date_soumission DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Retrieves the categories available for an application.
     *
     * @param int $userId Candidate ID.
     * @return Categorie[] List of available categories as Categorie objects.
     */
    public function getAvailableCategoriesForCandidature(int $userId): array
    {
        // 1. Retrieve the active editions.
        $sqlEditions = "SELECT id_edition 
                    FROM edition 
                    WHERE est_active = 1 
                    AND date_fin_candidatures >= NOW()";

        $stmtEditions = $this->pdo->query($sqlEditions);
        $activeEditionIds = $stmtEditions->fetchAll(PDO::FETCH_COLUMN);

        if (empty($activeEditionIds)) {
            return [];
        }

        $editionIdsString = implode(',', $activeEditionIds);

    // 2. Retrieve the categories for those editions.
        $sqlCategories = "SELECT c.*, e.nom as edition_nom, e.date_fin_candidatures
                      FROM categorie c
                      JOIN edition e ON c.id_edition = e.id_edition
                      WHERE c.id_edition IN ($editionIdsString)
                      AND e.date_fin_candidatures >= NOW()
                      ORDER BY e.annee DESC, c.nom ASC";

        $stmtCategories = $this->pdo->prepare($sqlCategories);
        $stmtCategories->execute();
        $allCategoriesData = $stmtCategories->fetchAll();

        $allCategories = [];
        foreach ($allCategoriesData as $data) {
            $allCategories[] = new Categorie($data);
        }

        // 3. Retrieve the categories where the user already applied.
        $sqlUserCandidatures = "SELECT id_categorie 
                            FROM candidature 
                            WHERE id_compte = :userId 
                            AND statut != 'Rejetée'";

        $stmtUser = $this->pdo->prepare($sqlUserCandidatures);
        $stmtUser->execute([':userId' => $userId]);
        $userCategoryIds = $stmtUser->fetchAll(PDO::FETCH_COLUMN);

        // 4. Filter the available categories.
        return array_filter($allCategories, function ($category) use ($userCategoryIds) {
            return !in_array($category->getIdCategorie(), $userCategoryIds);
        });
    }

    /**
     * Counts the available categories.
     *
     * @param int $userId Candidate ID.
     * @return int Number of available categories.
     */
    public function countAvailableCategories(int $userId): int
    {
        $availableCategories = $this->getAvailableCategoriesForCandidature($userId);
        return count($availableCategories);
    }

    /**
        * Checks whether the candidate already has an application in the category for the same platform.
     *
        * @param int $userId Candidate ID.
        * @param int $categoryId Category ID.
    * @param string $platform Platform.
    * @return bool True if an application already exists, false otherwise.
     */
    public function hasCandidatureInCategoryForPlatform(int $userId, int $categoryId, string $platform): bool
    {
        $sql = "SELECT COUNT(*) as count 
            FROM candidature 
            WHERE id_compte = :userId 
            AND id_categorie = :categoryId
            AND plateforme = :platform
            AND statut != 'Rejetée'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':userId' => $userId,
            ':categoryId' => $categoryId,
            ':platform' => $platform
        ]);
        $result = $stmt->fetch();

        return $result['count'] > 0;
    }

    /**
     * Retrieves all user applications in a category.
     *
     * @param int $userId Candidate ID.
     * @param int $categoryId Category ID.
     * @return Candidature[] List of applications as Candidature objects.
     */
    public function getCandidaturesInCategory(int $userId, int $categoryId): array
    {
        $sql = "SELECT id_candidature, libelle, plateforme, url_contenu, image, argumentaire, date_soumission, statut, id_compte, id_categorie 
            FROM candidature 
            WHERE id_compte = :userId 
            AND id_categorie = :categoryId
            AND statut != 'Rejetée'
            ORDER BY date_soumission DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':userId' => $userId,
            ':categoryId' => $categoryId
        ]);
        $data = $stmt->fetchAll();

        $candidatures = [];
        foreach ($data as $row) {
            $candidatures[] = new Candidature($row);
        }
        return $candidatures;
    }

    /**
     * Retrieves a specific application by ID with ownership verification.
     *
     * @param int $candidatureId Application ID.
     * @param int $userId Candidate ID.
     * @return Candidature|null Candidature object or null if not found.
     */
    public function getCandidature(int $candidatureId, int $userId): ?Candidature
    {
        $sql = "SELECT c.*, cat.nom as categorie_nom, ed.nom as edition_nom
                FROM candidature c
                JOIN categorie cat ON c.id_categorie = cat.id_categorie
                JOIN edition ed ON cat.id_edition = ed.id_edition
                WHERE c.id_candidature = :id
                AND c.id_compte = :userId";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $candidatureId,
            ':userId' => $userId
        ]);

        $data = $stmt->fetch();

        return $data ? new Candidature($data) : null;
    }

    /**
     * Creates a new application.
     *
     * @param array $data Application data.
     * @param int $userId Candidate ID.
     * @return bool Operation success.
     */
    public function createCandidature(array $data, int $userId): bool
    {
        try {
            // Check whether an application already exists for this category and platform.
            $checkSql = "SELECT COUNT(*) as count 
                         FROM candidature 
                         WHERE id_compte = :userId 
                         AND id_categorie = :categoryId
                         AND plateforme = :platform
                         AND statut != 'Rejetée'";

            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->execute([
                ':userId' => $userId,
                ':categoryId' => $data['id_categorie'],
                ':platform' => $data['plateforme']
            ]);

            $checkResult = $checkStmt->fetch();

            if ($checkResult['count'] > 0) {
                return false; // Application already exists.
            }

            // Insert the new application.
            $insertSql = "INSERT INTO candidature (
                libelle, 
                plateforme, 
                url_contenu, 
                argumentaire, 
                image,
                id_categorie,
                id_compte,
                date_soumission,
                statut
            ) VALUES (
                :libelle,
                :plateforme,
                :url_contenu,
                :argumentaire,
                :image,
                :id_categorie,
                :id_compte,
                NOW(),
                'En attente'
            )";

            $insertStmt = $this->pdo->prepare($insertSql);

            return $insertStmt->execute([
                ':libelle' => $data['libelle'],
                ':plateforme' => $data['plateforme'],
                ':url_contenu' => $data['url_contenu'],
                ':argumentaire' => $data['argumentaire'],
                ':image' => $data['image'] ?? null,
                ':id_categorie' => $data['id_categorie'],
                ':id_compte' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Application creation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an existing application.
     *
     * @param int $candidatureId Application ID.
     * @param array $data New data.
     * @param int $userId Candidate ID.
     * @return bool Operation success.
     */
    public function updateCandidature(int $id, array $data, int $userId): bool
    {
        try {
            // Retrieve the current application to keep the previous image if no new one is sent.
            $current = $this->getCandidature($id, $userId);
            if (!$current) {
                error_log("Application $id not found or does not belong to user $userId");
                return false;
            }

            if ($current->getStatut() !== 'En attente') {
                error_log("Application $id cannot be updated because its status is " . $current->getStatut());
                return false;
            }

            // Prepare the image: keep the old one if no new one is provided.
            $image = $data['image'] ?? $current->getImage();

            $sql = "UPDATE candidature SET 
                    libelle = :libelle,
                    plateforme = :plateforme,
                    url_contenu = :url_contenu,
                    argumentaire = :argumentaire,
                    id_categorie = :id_categorie,
                    image = :image
                WHERE id_candidature = :id 
                  AND id_compte = :user_id";

            $stmt = $this->pdo->prepare($sql);

            $success = $stmt->execute([
                ':libelle'       => $data['libelle'],
                ':plateforme'    => $data['plateforme'],
                ':url_contenu'   => $data['url_contenu'],
                ':argumentaire'  => $data['argumentaire'],
                ':id_categorie'  => (int)$data['id_categorie'],
                ':image'         => $image,
                ':id'            => $id,
                ':user_id'       => $userId
            ]);

            if (!$success) {
                error_log("Update failed for candidature $id: " . print_r($stmt->errorInfo(), true));
            }

            return $success;
        } catch (PDOException $e) {
            error_log("PDO error in updateCandidature: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes an application.
     *
     * @param int $candidatureId Application ID.
     * @param int $userId Candidate ID.
     * @return bool Operation success.
     */
    public function deleteCandidature(int $candidatureId, int $userId): bool
    {
        try {
            // Check that the application can be deleted.
            $checkSql = "SELECT statut 
                         FROM candidature 
                         WHERE id_candidature = :candidatureId 
                         AND id_compte = :userId";

            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->execute([
                ':candidatureId' => $candidatureId,
                ':userId' => $userId
            ]);

            $checkResult = $checkStmt->fetch();

            if (!$checkResult) {
                return false; // Application not found.
            }

            // Only pending applications can be deleted.
            if ($checkResult['statut'] != 'En attente') {
                return false;
            }

            // Delete the associated image if it exists.
            if (!empty($checkResult['image'])) {
                $imagePath = __DIR__ . '/../../public/' . $checkResult['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            // Delete the application.
            $deleteSql = "DELETE FROM candidature 
                          WHERE id_candidature = :candidatureId 
                          AND id_compte = :userId";

            $deleteStmt = $this->pdo->prepare($deleteSql);

            return $deleteStmt->execute([
                ':candidatureId' => $candidatureId,
                ':userId' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Application deletion error: " . $e->getMessage());
            return false;
        }
    }

    /**
        * Uploads an image for an application.
     *
        * @param array $fileData File data.
        * @return string|null File path or null on error.
     */
    public function uploadImage(array $fileData): ?string
    {
        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];
        $maxSize = 5 * 1024 * 1024; // 5MB

        $extension = \uploadedImageExtension($fileData, $allowedTypes);

        if ($fileData['size'] > $maxSize || $extension === null) {
            return null;
        }

        // Create the target directory if it does not exist.
        $uploadDir = __DIR__ . '/../../public/uploads/candidatures/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate a unique file name.
        $filename = uniqid('cand_', true) . '.' . $extension;
        $dest = $uploadDir . $filename;

        // Move the file.
        if (is_uploaded_file($fileData['tmp_name']) && move_uploaded_file($fileData['tmp_name'], $dest)) {
            return 'uploads/candidatures/' . $filename;
        }

        return null;
    }

    /**
        * Uploads a profile photo.
     *
        * @param array $fileData File data.
        * @param int $userId User ID.
        * @return string|null File path or null on error.
     */
    public function uploadProfilePhoto(array $fileData, int $userId): ?string
    {
        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];
        $maxSize = 5 * 1024 * 1024; // 5MB

        $extension = \uploadedImageExtension($fileData, $allowedTypes);

        if ($fileData['size'] > $maxSize || $extension === null) {
            return null;
        }

        // Create the target directory if it does not exist.
        $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Delete the previous photo if it exists.
        $oldPhotoSql = "SELECT photo_profil FROM compte WHERE id_compte = :userId";
        $oldStmt = $this->pdo->prepare($oldPhotoSql);
        $oldStmt->execute([':userId' => $userId]);
        $oldPhoto = $oldStmt->fetchColumn();

        if ($oldPhoto && file_exists(__DIR__ . '/../../public/' . $oldPhoto)) {
            unlink(__DIR__ . '/../../public/' . $oldPhoto);
        }

        // Generate a unique file name.
        $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
        $dest = $uploadDir . $filename;

        // Move the file.
        if (is_uploaded_file($fileData['tmp_name']) && move_uploaded_file($fileData['tmp_name'], $dest)) {
            return 'uploads/profiles/' . $filename;
        }

        return null;
    }

    /**
     * Checks whether the candidate can edit their profile.
     *
     * @param int $userId Candidate ID.
     * @return bool True if editing is allowed, otherwise false.
     */
    public function canEditProfile(int $userId): bool
    {
        // Check whether the candidate is a nominee.
        $isNominee = $this->isNominee($userId);

        if (!$isNominee) {
            return true; // Non-nominees can always edit.
        }

        // For nominees, check whether voting is in progress.
        $nominations = $this->getActiveNominations($userId);

        foreach ($nominations as $nomination) {
            $status = $this->getVotingStatus($nomination);
            if ($status == 'in_progress') {
                return false; // Editing is not allowed during voting.
            }
        }

        return true;
    }

    /**
        * Checks whether a display name is available.
     *
        * @param string $pseudonyme Display name to check.
        * @param int $userId User ID to exclude.
        * @return bool True if it is available, otherwise false.
     */
    public function isPseudonymeAvailable(string $pseudonyme, int $userId): bool
    {
        $sql = "SELECT COUNT(*) as count 
                FROM compte 
                WHERE pseudonyme = :pseudonyme 
                AND id_compte != :userId";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':pseudonyme' => $pseudonyme,
            ':userId' => $userId
        ]);

        $result = $stmt->fetch();

        return $result['count'] == 0;
    }

    /**
     * Checks whether an email address is available.
     *
     * @param string $email Email to check.
     * @param int $userId User ID to exclude.
     * @return bool True if available, false otherwise.
     */
    public function isEmailAvailable(string $email, int $userId): bool
    {
        $sql = "SELECT COUNT(*) as count 
                FROM compte 
                WHERE email = :email 
                AND id_compte != :userId";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':userId' => $userId
        ]);

        $result = $stmt->fetch();

        return $result['count'] == 0;
    }

    /**
     * Retrieves the nominee public profile data.
     *
     * @param int $userId Candidate ID.
     * @return array|null Profile data or null.
     */
    public function getNomineeData(int $userId): ?array
    {
        $sql = "SELECT 
                c.id_compte,
                c.nom_legal_ou_societe,
                c.type_candidature,
                co.pseudonyme,
                co.email,
                co.photo_profil,
                co.pays,
                co.genre,
                co.bio,
                co.url_instagram,
                co.url_tiktok,
                co.url_youtube,
                co.url_twitter,
                co.date_creation
                FROM candidat c
                JOIN compte co ON c.id_compte = co.id_compte
                WHERE c.id_compte = :userId
                AND c.est_nomine = 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Updates the public profile of the nominee.
     *
     * @param int $userId Candidate ID.
     * @param array $data Data to update.
     * @return bool Operation success.
     */
    public function updateNomineeProfile(int $userId, array $data): bool
    {
        try {
            $sql = "UPDATE compte SET 
                bio = :bio,
                url_instagram = :url_instagram,
                url_tiktok = :url_tiktok,
                url_youtube = :url_youtube,
                url_twitter = :url_twitter,
                date_modification = NOW()";

            // Add the profile photo if provided.
            if (isset($data['photo_profil_path'])) {
                $sql .= ", photo_profil = :photo_profil";
            }

            $sql .= " WHERE id_compte = :userId";

            $stmt = $this->pdo->prepare($sql);

            $params = [
                ':bio' => $data['bio'] ?? null,
                ':url_instagram' => $data['url_instagram'] ?? null,
                ':url_tiktok' => $data['url_tiktok'] ?? null,
                ':url_youtube' => $data['url_youtube'] ?? null,
                ':url_twitter' => $data['url_twitter'] ?? null,
                ':userId' => $userId
            ];

            if (isset($data['photo_profil_path'])) {
                $params[':photo_profil'] = $data['photo_profil_path'];
            }

            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Nominee profile update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves nomination results.
     *
     * @param int $nominationId Nomination ID.
     * @return array|null Results or null.
     */
    public function getNominationResults(int $nominationId): ?array
    {
        $sql = "SELECT 
                r.position,
                r.nombre_votes,
                r.pourcentage,
                r.date_resultat
                FROM resultat_nomination r
                WHERE r.id_nomination = :nominationId
                ORDER BY r.position ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':nominationId' => $nominationId]);

        return $stmt->fetchAll() ?: null;
    }

    /**
     * Retrieves the active editions open for applications.
     *
     * @return Edition[] List of active editions.
     */
    public function getActiveEditionsForCandidature(): array
    {
        $sql = "SELECT id_edition, annee, nom, date_debut_candidatures, date_fin_candidatures, date_debut, date_fin, est_active, theme, image, description 
                FROM edition 
                WHERE est_active = 1 
                AND date_fin_candidatures >= NOW()
                ORDER BY annee DESC, nom ASC";

        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetchAll();

        $editions = [];
        foreach ($data as $row) {
            $editions[] = new Edition($row);
        }

        return $editions;
    }

    /**
     * Retrieves all applications for administration.
     *
     * @return array List of applications.
     */
    public function getAllCandidatures(): array
    {
        $sql = "SELECT 
                c.*,
                co.pseudonyme,
                co.email,
                cat.nom as categorie_nom,
                ed.nom as edition_nom
                FROM candidature c
                JOIN compte co ON c.id_compte = co.id_compte
                JOIN categorie cat ON c.id_categorie = cat.id_categorie
                JOIN edition ed ON cat.id_edition = ed.id_edition
                ORDER BY c.date_soumission DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Retrieves applications by status.
     *
     * @param string $status Status.
     * @return array List of applications.
     */
    public function getCandidaturesByStatus(string $status): array
    {
        $sql = "SELECT 
                c.*,
                co.pseudonyme,
                cat.nom as categorie_nom
                FROM candidature c
                JOIN compte co ON c.id_compte = co.id_compte
                JOIN categorie cat ON c.id_categorie = cat.id_categorie
                WHERE c.statut = :status
                ORDER BY c.date_soumission DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }

    /**
     * Approves an application.
     *
     * @param int $candidatureId Application ID.
     * @param int $adminId Administrator ID.
     * @return bool Operation success.
     */
    public function approveCandidature(int $candidatureId, int $adminId): bool
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Update the application status.
            $sqlUpdate = "UPDATE candidature 
                         SET statut = 'Approuvée',
                             date_modification = NOW()
                         WHERE id_candidature = :id
                         AND statut = 'En attente'";

            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([':id' => $candidatureId]);

            // 2. Create a nomination if needed.
            $sqlNomination = "INSERT INTO nomination (
                id_candidature,
                id_categorie,
                id_compte,
                date_creation
            ) SELECT 
                id_candidature,
                id_categorie,
                id_compte,
                NOW()
              FROM candidature 
              WHERE id_candidature = :id";

            $stmtNomination = $this->pdo->prepare($sqlNomination);
            $stmtNomination->execute([':id' => $candidatureId]);

            // 3. Mark the candidate as a nominee.
            $sqlCandidat = "UPDATE candidat c
                           JOIN candidature ca ON c.id_compte = ca.id_compte
                           SET c.est_nomine = 1
                           WHERE ca.id_candidature = :id";

            $stmtCandidat = $this->pdo->prepare($sqlCandidat);
            $stmtCandidat->execute([':id' => $candidatureId]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Application approval error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Rejects an application.
     *
     * @param int $candidatureId Application ID.
     * @param int $adminId Administrator ID.
     * @return bool Whether the operation succeeded.
     */
    public function rejectCandidature(int $candidatureId, int $adminId): bool
    {
        $sql = "UPDATE candidature 
                SET statut = 'Rejetée',
                    date_modification = NOW()
                WHERE id_candidature = :id
                AND statut = 'En attente'";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $candidatureId]);
    }

    /**
     * Retrieves categories by edition.
     *
     * @param int $editionId Edition ID.
     * @return Categorie[] List of categories.
     */
    public function getCategoriesByEdition(int $editionId): array
    {
        $sql = "SELECT id_categorie, nom, description, image, plateforme_cible, limite_nomines, date_debut_votes, date_fin_votes, id_edition
                FROM categorie
                WHERE id_edition = :editionId
                ORDER BY nom ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':editionId' => $editionId]);
        $data = $stmt->fetchAll();

        $categories = [];
        foreach ($data as $row) {
            $categories[] = new Categorie($row);
        }

        return $categories;
    }

    /**
     * Retrieves statistics for an edition.
     *
     * @param int $editionId Edition ID.
     * @return array Statistics.
     */
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

    /**
     * Retrieves the total number of candidates.
     *
     * @return int Number of candidates.
     */
    public function getTotalCandidates(): int
    {
        $sql = "SELECT COUNT(*) as total FROM candidat";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();

        return $result['total'] ?? 0;
    }

    /**
     * Retrieves the total number of nominees.
     *
     * @return int Number of nominees.
     */
    public function getTotalNominees(): int
    {
        $sql = "SELECT COUNT(*) as total FROM candidat WHERE est_nomine = 1";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();

        return $result['total'] ?? 0;
    }

    /**
     * Retrieves recently registered candidates.
     *
     * @param int $limit Limit.
     * @return array List of candidates.
     */
    public function getRecentCandidates(int $limit = 10): array
    {
        $sql = "SELECT 
                c.id_compte,
                c.nom_legal_ou_societe,
                c.type_candidature,
                co.pseudonyme,
                co.email,
                co.date_creation
                FROM candidat c
                JOIN compte co ON c.id_compte = co.id_compte
                ORDER BY co.date_creation DESC
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Retrieves the candidates with the most applications.
     *
     * @param int $limit Limit.
     * @return array List of candidates.
     */
    public function getTopCandidates(int $limit = 10): array
    {
        $sql = "SELECT 
                c.id_compte,
                c.nom_legal_ou_societe,
                co.pseudonyme,
                COUNT(ca.id_candidature) as total_candidatures,
                SUM(CASE WHEN ca.statut = 'Approuvée' THEN 1 ELSE 0 END) as approved_candidatures
                FROM candidat c
                JOIN compte co ON c.id_compte = co.id_compte
                LEFT JOIN candidature ca ON c.id_compte = ca.id_compte
                GROUP BY c.id_compte, c.nom_legal_ou_societe, co.pseudonyme
                ORDER BY total_candidatures DESC
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Retrieves the PDO connection.
     *
     * @return PDO Database connection.
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
