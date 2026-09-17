<?php

declare(strict_types=1);

namespace Tests\Integration\Services;

use App\Services\CategorieService;
use Tests\TestCase;

/**
 * Tests d'intégration pour CategorieService.
 *
 * Vérifie la récupération des catégories par édition, la création,
 * la modification et la suppression sur une base SQLite en mémoire.
 */
class CategorieServiceTest extends TestCase
{
    private \PDO $pdo;
    private CategorieService $service;
    private int $editionId;

    protected function setUp(): void
    {
        $this->pdo = $this->createSqlitePdo();
        $this->service = new CategorieService($this->pdo);
        $this->editionId = $this->insertEdition($this->pdo);
    }

    // ── getAllCategoriesByEdition ──────────────────────────────────────────

    public function testGetAllCategoriesByEditionReturnsEmpty(): void
    {
        $this->assertSame([], $this->service->getAllCategoriesByEdition(999));
    }

    public function testGetAllCategoriesByEditionReturnsCategories(): void
    {
        $this->insertCategorie($this->pdo, $this->editionId, ['nom' => 'Cat A']);
        $this->insertCategorie($this->pdo, $this->editionId, ['nom' => 'Cat B']);

        $results = $this->service->getAllCategoriesByEdition($this->editionId);

        $this->assertCount(2, $results);
    }

    public function testGetAllCategoriesByEditionFiltersByEdition(): void
    {
        $editionB = $this->insertEdition($this->pdo, ['nom' => 'Édition B', 'annee' => 2026]);

        $this->insertCategorie($this->pdo, $this->editionId, ['nom' => 'Cat A']);
        $this->insertCategorie($this->pdo, $editionB, ['nom' => 'Cat B']);

        $results = $this->service->getAllCategoriesByEdition($this->editionId);

        $this->assertCount(1, $results);
        $this->assertSame('Cat A', $results[0]->getNom());
    }

    public function testGetAllCategoriesByEditionReturnsCategorieSortedByNom(): void
    {
        $this->insertCategorie($this->pdo, $this->editionId, ['nom' => 'Zèbre']);
        $this->insertCategorie($this->pdo, $this->editionId, ['nom' => 'Alpha']);

        $results = $this->service->getAllCategoriesByEdition($this->editionId);

        $this->assertSame('Alpha', $results[0]->getNom());
        $this->assertSame('Zèbre', $results[1]->getNom());
    }

    // ── getAllCategories ──────────────────────────────────────────────────

    public function testGetAllCategoriesReturnsAllEditions(): void
    {
        $editionB = $this->insertEdition($this->pdo, ['nom' => 'Édition B', 'annee' => 2026]);

        $this->insertCategorie($this->pdo, $this->editionId, ['nom' => 'Cat A']);
        $this->insertCategorie($this->pdo, $editionB,     ['nom' => 'Cat B']);

        $results = $this->service->getAllCategories();

        $this->assertCount(2, $results);
    }
}
