<?php

namespace App\Controllers;

use App\Services\CandidatureService;
use App\Models\Candidature;

/**
 * Controller responsible for administrative application management.
 * 
 * Allows the administrator to view, update the status of, and delete
 * applications submitted to the Social Media Awards.
 */
class AdminCandidatureController
{
    private CandidatureService $candidatureService;

    /**
     * Controller constructor.
     *
     * @param CandidatureService $candidatureService Service that manages applications
     */
    public function __construct(CandidatureService $candidatureService)
    {
        $this->candidatureService = $candidatureService;
    }

    /**
     * Retrieves the full list of all applications.
     *
     * @return Candidature[] Array containing all applications as Candidature objects
     */
    public function getAllCandidatures(): array
    {
        return $this->candidatureService->getAllCandidatures();
    }

    /**
     * Retrieves a specific application by its identifier.
     *
     * @param int $id Application identifier
     * @return Candidature|null Candidature object or null if not found
     */
    public function getCandidatureById(int $id): ?Candidature
    {
        return $this->candidatureService->getCandidatureById($id);
    }

    /**
     * Updates the status of an application (for example: en_attente, acceptee, refusee).
     *
     * @param int $id Application identifier
     * @param string $statut New status to apply
     * @return bool True on success, false otherwise
     */
    public function updateCandidatureStatus(int $id, string $statut): bool
    {
        return $this->candidatureService->updateStatus($id, $statut);
    }

    /**
     * Deletes an application by its identifier.
     *
     * @param int $id Identifier of the application to delete
     * @return bool True on success, false otherwise
     */
    public function deleteCandidature(int $id): bool
    {
        return $this->candidatureService->deleteCandidature($id);
    }

    public function countAllCandidatures(): int
    {
        return $this->candidatureService->countAllCandidatures();
    }

    public function getCandidaturesPaginated(int $page, int $perPage = 20): array
    {
        return $this->candidatureService->getCandidaturesPaginated($page, $perPage);
    }

    public function getStatusStats(): array
    {
        return $this->candidatureService->getStatusStats();
    }
}