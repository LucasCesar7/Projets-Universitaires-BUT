<?php

namespace App\Controllers;

use App\Services\EditionService;
use PDO;
use App\Models\Edition;

/**
 * Controller responsible for administrative edition management.
 * 
 * Handles CRUD operations on Social Media Awards editions
 * as well as a simplified editions list for the admin interface.
 */
class AdminEditionController
{
    private EditionService $editionService;
    private PDO $pdo;

    /**
        * Controller constructor.
     *
        * @param PDO $pdo Database connection
        * @param EditionService $editionService Service handling edition operations
     */
    public function __construct(PDO $pdo, EditionService $editionService)
    {
        $this->pdo = $pdo;
        $this->editionService = $editionService;
    }

    /**
        * Retrieves a simplified list of editions (id, name, year).
     * 
        * Mainly used for dropdown menus in the admin interface.
     *
        * @return array List of editions sorted by descending year
     */
    public function getEditionsList(): array
    {
        $sql = "SELECT id_edition, nom, annee FROM edition ORDER BY annee DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
        * Retrieves all editions with their full data.
     *
        * @return Edition[] Array containing all editions as Edition objects
     */
    public function getAllEditions(): array
    {
        $this->editionService->updateAllEditionStatus();
        return $this->editionService->getAllEditions();
    }

    /**
        * Retrieves a specific edition by its identifier.
     *
        * @param int $id Edition identifier
        * @return Edition|null Edition object or null if not found
     */
    public function getEditionById(int $id): ?Edition
    {
        $data = $this->editionService->getEditionById($id);
        return $data ? new Edition($data) : null;
    }

    /**
        * Creates a new edition.
     *
        * @param array $data Edition data (name, year, dates, etc.)
        * @param array|null $imageFile Related image (optional)
        * @return bool True on success, otherwise false
     */
    public function createEdition(array $data, ?array $imageFile = null): bool
    {
        return $this->editionService->createEdition($data, $imageFile);
    }

    /**
        * Updates an existing edition.
     *
        * @param int $id Identifier of the edition to update
        * @param array $data New data
        * @param array|null $imageFile New image (optional)
        * @param bool $removeImage Delete the current image
        * @return bool True on success, otherwise false
     */
    public function updateEdition(int $id, array $data, ?array $imageFile = null, bool $removeImage = false): bool
    {
        if ($removeImage) {
            $_POST['remove_image'] = '1';
        }
        return $this->editionService->updateEdition($id, $data, $imageFile);
    }

    /**
     * Deletes an edition by its identifier.
     *
     * @param int $id Identifier of the edition to delete
     * @return bool True on success, otherwise false
     */
    public function deleteEdition(int $id): bool
    {
        return $this->editionService->deleteEdition($id);
    }
}