<?php

declare(strict_types=1);

namespace Tests\Integration\Services;

use App\Services\CandidatureService;
use Tests\TestCase;

/**
 * Tests d'intégration pour CandidatureService.
 *
 * Utilise SQLite en mémoire pour simuler la base de données.
 * Vérifie que les requêtes SQL, la suppression logique et la pagination
 * fonctionnent correctement sans base MySQL externe.
 */
class CandidatureServiceTest extends TestCase
{
    private \PDO $pdo;
    private CandidatureService $service;
    private int $editionId;
    private int $compteId;
    private int $categorieId;

    protected function setUp(): void
    {
        $this->pdo = $this->createSqlitePdo();
        $this->service = new CandidatureService($this->pdo);

        // Fixtures de base
        $this->editionId   = $this->insertEdition($this->pdo);
        $this->compteId    = $this->insertCompte($this->pdo);
        $this->categorieId = $this->insertCategorie($this->pdo, $this->editionId);
    }

    // ── getAllCandidatures ────────────────────────────────────────────────

    public function testGetAllCandidaturesReturnsEmptyWhenNone(): void
    {
        $this->assertSame([], $this->service->getAllCandidatures());
    }

    public function testGetAllCandidaturesReturnsCandidature(): void
    {
        $this->insertCandidature($this->pdo, $this->compteId, $this->categorieId);

        $results = $this->service->getAllCandidatures();

        $this->assertCount(1, $results);
        $this->assertSame('Ma candidature', $results[0]->getLibelle());
    }

    public function testGetAllCandidaturesExcludesSoftDeleted(): void
    {
        $this->insertCandidature($this->pdo, $this->compteId, $this->categorieId);
        $this->insertCandidature($this->pdo, $this->compteId, $this->categorieId, [
            'libelle'     => 'Supprimée',
            'supprime_le' => '2025-01-01 00:00:00',
        ]);

        $results = $this->service->getAllCandidatures();

        $this->assertCount(1, $results);
        $this->assertSame('Ma candidature', $results[0]->getLibelle());
    }

    // ── getCandidatureById ────────────────────────────────────────────────

    public function testGetCandidatureByIdReturnsNull(): void
    {
        $this->assertNull($this->service->getCandidatureById(999));
    }

    public function testGetCandidatureByIdReturnsCorrectItem(): void
    {
        $id = $this->insertCandidature($this->pdo, $this->compteId, $this->categorieId, [
            'libelle' => 'Candidature spécifique',
        ]);

        $c = $this->service->getCandidatureById($id);

        $this->assertNotNull($c);
        $this->assertSame($id, $c->getIdCandidature());
        $this->assertSame('Candidature spécifique', $c->getLibelle());
    }

    // ── deleteCandidature (suppression logique) ───────────────────────────

    public function testDeleteCandidatureSetsSupprimeLeField(): void
    {
        $id = $this->insertCandidature($this->pdo, $this->compteId, $this->categorieId);

        $this->service->deleteCandidature($id);

        // Vérifier directement dans la BDD que supprime_le est renseigné
        $row = $this->pdo
            ->query("SELECT supprime_le FROM candidature WHERE id_candidature = $id")
            ->fetch();

        $this->assertNotNull($row['supprime_le'], 'supprime_le doit être non-null après soft delete');
    }

    public function testDeleteCandidatureHidesFromGetAll(): void
    {
        $id = $this->insertCandidature($this->pdo, $this->compteId, $this->categorieId);

        $this->assertCount(1, $this->service->getAllCandidatures());

        $this->service->deleteCandidature($id);

        $this->assertCount(0, $this->service->getAllCandidatures());
    }

    // ── countAllCandidatures ──────────────────────────────────────────────

    public function testCountAllCandidaturesReturnsZeroInitially(): void
    {
        $this->assertSame(0, $this->service->countAllCandidatures());
    }

    public function testCountAllCandidaturesReturnsCorrectNumber(): void
    {
        $this->insertCandidature($this->pdo, $this->compteId, $this->categorieId);
        $this->insertCandidature($this->pdo, $this->compteId, $this->categorieId);

        $this->assertSame(2, $this->service->countAllCandidatures());
    }

    public function testCountAllCandidaturesExcludesSoftDeleted(): void
    {
        $this->insertCandidature($this->pdo, $this->compteId, $this->categorieId);
        $id = $this->insertCandidature($this->pdo, $this->compteId, $this->categorieId);

        $this->service->deleteCandidature($id);

        $this->assertSame(1, $this->service->countAllCandidatures());
    }

    // ── getCandidaturesPaginated ──────────────────────────────────────────

    public function testGetCandidaturesPaginatedReturnsFirstPage(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->insertCandidature($this->pdo, $this->compteId, $this->categorieId, [
                'libelle' => "Candidature $i",
            ]);
        }

        $results = $this->service->getCandidaturesPaginated(1, 3);

        $this->assertCount(3, $results);
    }

    public function testGetCandidaturesPaginatedReturnsSecondPage(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->insertCandidature($this->pdo, $this->compteId, $this->categorieId, [
                'libelle' => "Candidature $i",
            ]);
        }

        $results = $this->service->getCandidaturesPaginated(2, 3);

        $this->assertCount(2, $results);
    }
}
