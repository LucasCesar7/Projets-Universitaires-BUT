<?php

namespace App\Controllers;

use App\Services\CategorieService;
use App\Models\Categorie;

/**
 * Controller responsible for administrative category management.
 * 
 * This controller groups all CRUD operations related to categories
 * in the Social Media Awards system, delegating business logic to the dedicated service.
 */
class AdminCategorieController
{
    private CategorieService $categorieService;

    /**
        * Controller constructor.
     *
        * @param CategorieService $categorieService Service handling categorie operations
     */
    public function __construct(CategorieService $categorieService)
    {
        $this->categorieService = $categorieService;
    }

    /**
        * Retrieves the full list of categories.
     *
        * @return Categorie[] Array containing all categories as Categorie objects
     */
    public function getAllCategories(): array
    {
        return $this->categorieService->getAllCategories();
    }

    /**
        * Retrieves a specific category by its identifier.
     *
        * @param int $id Unique category identifier
        * @return Categorie|null Categorie object or null if not found
     */
    public function getCategoryById(int $id): ?Categorie
    {
        return $this->categorieService->getCategoryById($id);
    }

    /**
        * Creates a new category.
     *
        * @param array $data Category data (name, description, etc.)
        * @param array|null $imageFile Uploaded image file (optional)
        * @return bool True on success, otherwise false
     */
    public function createCategory(array $data, ?array $imageFile = null): bool
    {
        return $this->categorieService->createCategory($data, $imageFile);
    }

    /**
        * Updates an existing category.
     *
        * @param int $id Identifier of the category to update
        * @param array $data New category data
        * @param array|null $imageFile New image (optional)
        * @return bool True on success, otherwise false
     */
    public function updateCategory(int $id, array $data, ?array $imageFile = null, bool $removeImage = false): bool
    {
        return $this->categorieService->updateCategory($id, $data, $imageFile, $removeImage);
    }

    /**
     * Deletes a category by its identifier.
     *
     * @param int $id Identifier of the category to delete
     * @return bool True on success, otherwise false
     */
    public function deleteCategory(int $id): bool
    {
        return $this->categorieService->deleteCategorie($id);
    }
}