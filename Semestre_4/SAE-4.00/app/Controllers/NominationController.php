<?php

namespace App\Controllers;

use App\Services\NominationService;
use PDO;

/**
 * Controller handling nominations in the administration area.
 */
class NominationController
{
    private NominationService $service;

    /**
     * Nomination controller constructor.
     *
     * @param PDO $pdo Database connection
     * @param NominationService|null $nominationService Optional service (for tests/injection)
     */
    public function __construct(PDO $pdo, ?NominationService $nominationService = null)
    {
        // Use the provided service or create a new one.
        $this->service = $nominationService ?: new NominationService($pdo);
    }

    /**
     * Returns the nomination service used by this controller.
     *
     * @return NominationService
     */
    public function getService(): NominationService
    {
        return $this->service;
    }

    /**
     * Creates a nomination from an application validated by an administrator.
     *
     * @param int $candidatureId ID of the application to transform into a nomination
     * @param int $adminId ID of the validating administrator
     * @return bool Operation success
     */
    public function createNominationFromCandidature(int $candidatureId, int $adminId): bool
    {
        return $this->service->createFromCandidature($candidatureId, $adminId);
    }

    /**
     * Displays the list of all nominations (administration interface).
     *
     * @return void
     */
    public function index()
    {
        $nominations = $this->service->getAllNominations();

        $stats = [
            'total' => count($nominations),
            'total_votes' => 0 // To be completed later with the real votes table.
        ];

        header('Location: ' . appUrl('admin/nominations'));
    }

    /**
     * Displays the form to edit an existing nomination.
     *
     * @param int $id Identifier of the nomination to edit
     * @return void
     */
    public function edit(int $id)
    {
        $nomination = $this->service->getNominationById($id);

        if (!$nomination) {
            $_SESSION['error'] = "Nomination non trouvée.";
            header('Location: ' . appUrl('admin/nominations'));
            exit;
        }

        $categories = $this->getCategories();
        $platforms = ['TikTok', 'Instagram', 'YouTube', 'Twitch', 'Spotify', 'Facebook', 'X'];

        header('Location: ' . appUrl('admin/nominations/edit') . '?id=' . $id);
    }

    /**
     * Processes the update of an existing nomination.
     *
     * @param int $id Identifier of the nomination to update
     * @return void
     */
    public function update(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . appUrl('admin/nominations'));
            exit;
        }

        if (!\App\Services\CsrfService::isValid($_POST['csrf_token'] ?? null, 'update_nomination')) {
            $_SESSION['error'] = "La validation du formulaire a expiré. Veuillez réessayer.";
            header('Location: ' . appUrl('admin/nominations/edit') . '?id=' . $id);
            exit;
        }

        $data = [
            'libelle' => $_POST['titre'] ?? '',
            'plateforme' => $_POST['plateforme'] ?? '',
            'url_contenu' => $_POST['lien_contenu'] ?? '',
            'argumentaire' => $_POST['argumentation'] ?? '',
            'remove_image' => $_POST['remove_image'] ?? '0'
        ];

        $success = $this->service->updateNomination(
            $_SESSION['admin_id'] ?? 6,
            $id,
            $data,
            $_FILES['image_file'] ?? null
        );

        if ($success) {
            $_SESSION['success'] = "Nomination mise à jour avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour.";
        }

        header('Location: ' . appUrl('admin/nominations/edit') . '?id=' . $id);
        exit;
    }

    /**
     * Deletes a nomination.
     *
     * @param int $id Identifier of the nomination to delete
     * @return void
     */
    public function delete(int $id)
    {
        $success = $this->service->deleteNomination($id);

        $_SESSION[$success ? 'success' : 'error'] = $success
            ? "Nomination supprimée avec succès."
            : "Erreur lors de la suppression.";

        header('Location: ' . appUrl('admin/nominations'));
        exit;
    }

    /**
     * Retrieves the list of all available categories.
     *
     * @return array List of categories (id_categorie + nom)
     */
    private function getCategories(): array
    {
        return $this->service->getAllCategories();
    }
}
