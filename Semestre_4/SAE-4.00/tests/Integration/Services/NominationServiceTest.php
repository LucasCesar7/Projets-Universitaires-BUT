<?php

declare(strict_types=1);

namespace Tests\Integration\Services;

use App\Services\NominationService;
use Tests\TestCase;

/**
 * Tests d'intégration pour NominationService.
 *
 * Vérifie les opérations CRUD, la suppression logique (supprime_le)
 * et la pagination sur une base SQLite en mémoire.
 */
class NominationServiceTest extends TestCase
{
    private \PDO $pdo;
    private NominationService $service;
    private int $editionId;
    private int $categorieId;

    protected function setUp(): void
    {
        $this->pdo = $this->createSqlitePdo();
        $this->service = new NominationService($this->pdo);

        $this->editionId   = $this->insertEdition($this->pdo);
        $this->categorieId = $this->insertCategorie($this->pdo, $this->editionId);
    }

    // ── getAllNominations ─────────────────────────────────────────────────

    public function testGetAllNominationsReturnsEmptyWhenNone(): void
    {
        $this->assertSame([], $this->service->getAllNominations());
    }

    public function testGetAllNominationsReturnsItems(): void
    {
        $this->insertNomination($this->pdo, $this->categorieId);
        $this->insertNomination($this->pdo, $this->categorieId, ['libelle' => 'Deuxième']);

        $results = $this->service->getAllNominations();

        $this->assertCount(2, $results);
    }

    public function testGetAllNominationsExcludesSoftDeleted(): void
    {
        $this->insertNomination($this->pdo, $this->categorieId);
        $this->insertNomination($this->pdo, $this->categorieId, [
            'libelle'     => 'Supprimée',
            'supprime_le' => '2025-01-01 00:00:00',
        ]);

        $results = $this->service->getAllNominations();

        $this->assertCount(1, $results);
        $this->assertSame('Ma nomination', $results[0]->getLibelle());
    }

    // ── getNominationById ─────────────────────────────────────────────────

    public function testGetNominationByIdReturnsNullForMissing(): void
    {
        $this->assertNull($this->service->getNominationById(9999));
    }

    public function testGetNominationByIdReturnsCorrectItem(): void
    {
        $id = $this->insertNomination($this->pdo, $this->categorieId, [
            'libelle' => 'Nomination unique',
        ]);

        $n = $this->service->getNominationById($id);

        $this->assertNotNull($n);
        $this->assertSame($id, $n->getIdNomination());
        $this->assertSame('Nomination unique', $n->getLibelle());
    }

    // ── deleteNomination (suppression logique) ────────────────────────────

    public function testDeleteNominationSetsSupprimeLeField(): void
    {
        $id = $this->insertNomination($this->pdo, $this->categorieId);

        $this->service->deleteNomination($id);

        $row = $this->pdo
            ->query("SELECT supprime_le FROM nomination WHERE id_nomination = $id")
            ->fetch();

        $this->assertNotNull($row['supprime_le']);
    }

    public function testDeleteNominationHidesFromGetAll(): void
    {
        $id = $this->insertNomination($this->pdo, $this->categorieId);

        $this->assertCount(1, $this->service->getAllNominations());

        $this->service->deleteNomination($id);

        $this->assertCount(0, $this->service->getAllNominations());
    }

    // ── countAllNominations ───────────────────────────────────────────────

    public function testCountAllNominationsReturnsZeroInitially(): void
    {
        $this->assertSame(0, $this->service->countAllNominations());
    }

    public function testCountAllNominationsReturnsCorrectCount(): void
    {
        $this->insertNomination($this->pdo, $this->categorieId);
        $this->insertNomination($this->pdo, $this->categorieId);

        $this->assertSame(2, $this->service->countAllNominations());
    }

    public function testCountAllNominationsExcludesSoftDeleted(): void
    {
        $this->insertNomination($this->pdo, $this->categorieId);
        $id = $this->insertNomination($this->pdo, $this->categorieId);

        $this->service->deleteNomination($id);

        $this->assertSame(1, $this->service->countAllNominations());
    }

    // ── getNominationsByCategory ──────────────────────────────────────────

    public function testGetNominationsByCategoryFiltersCorrectly(): void
    {
        $categorieB = $this->insertCategorie($this->pdo, $this->editionId, [
            'nom' => 'Catégorie B',
        ]);

        $this->insertNomination($this->pdo, $this->categorieId, ['libelle' => 'N-A']);
        $this->insertNomination($this->pdo, $categorieB, ['libelle' => 'N-B']);

        $results = $this->service->getNominationsByCategory($this->categorieId);

        $this->assertCount(1, $results);
        $this->assertSame('N-A', $results[0]->getLibelle());
    }

    // ── getPaginatedNominations ───────────────────────────────────────────

    public function testGetNominationsPaginatedReturnsFirstPage(): void
    {
        for ($i = 1; $i <= 6; $i++) {
            $this->insertNomination($this->pdo, $this->categorieId, ['libelle' => "N$i"]);
        }

        $results = $this->service->getNominationsPaginated(1, 4);

        $this->assertCount(4, $results);
    }

    public function testGetNominationsPaginatedReturnsSecondPage(): void
    {
        for ($i = 1; $i <= 6; $i++) {
            $this->insertNomination($this->pdo, $this->categorieId, ['libelle' => "N$i"]);
        }

        $results = $this->service->getNominationsPaginated(2, 4);

        $this->assertCount(2, $results);
    }
}
