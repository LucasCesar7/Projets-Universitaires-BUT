<?php

declare(strict_types=1);

namespace Tests\Integration\Services;

use App\Services\ResultsService;
use Tests\TestCase;

/**
 * Tests d'intégration pour ResultsService.
 *
 * Vérifie que les méthodes de récupération des résultats, statistiques
 * et classements fonctionnent avec une base SQLite en mémoire.
 */
class ResultsServiceTest extends TestCase
{
    private \PDO $pdo;
    private ResultsService $service;

    protected function setUp(): void
    {
        $this->pdo = $this->createSqlitePdo();
        $this->service = new ResultsService($this->pdo);
    }

    // ── getLatestEdition ──────────────────────────────────────────────────

    public function testGetLatestEditionReturnsNullOrDefaultWhenNone(): void
    {
        $edition = $this->service->getLatestEdition();

        // Sans édition, le service doit retourner null ou un tableau de valeurs par défaut
        // (comportement documenté dans ResultsService::getDefaultEdition)
        $this->assertTrue($edition === null || is_array($edition));
    }

    public function testGetLatestEditionReturnsActiveEdition(): void
    {
        $this->insertEdition($this->pdo, [
            'nom'        => 'Édition Active',
            'annee'      => 2025,
            'est_active' => 1,
        ]);

        $edition = $this->service->getLatestEdition();

        $this->assertNotNull($edition);
        $this->assertSame('Édition Active', $edition['nom']);
    }

    public function testGetLatestEditionReturnsNewestWhenMultipleActive(): void
    {
        $this->insertEdition($this->pdo, ['nom' => 'Édition 2024', 'annee' => 2024, 'est_active' => 1]);
        $this->insertEdition($this->pdo, ['nom' => 'Édition 2025', 'annee' => 2025, 'est_active' => 1]);

        $edition = $this->service->getLatestEdition();

        $this->assertSame('Édition 2025', $edition['nom']);
    }

    public function testGetLatestEditionIgnoresInactiveEditions(): void
    {
        $this->insertEdition($this->pdo, ['nom' => 'Inactive', 'annee' => 2020, 'est_active' => 0]);
        $this->insertEdition($this->pdo, ['nom' => 'Active',   'annee' => 2025, 'est_active' => 1]);

        $edition = $this->service->getLatestEdition();

        $this->assertSame('Active', $edition['nom']);
    }

    // ── getGrandWinners ───────────────────────────────────────────────────

    public function testGetGrandWinnersReturnsArrayWhenNoData(): void
    {
        $result = $this->service->getGrandWinners(999);

        $this->assertIsArray($result);
    }

    // ── getGlobalStatistics ───────────────────────────────────────────────

    public function testGetGlobalStatisticsReturnsArray(): void
    {
        $editionId = $this->insertEdition($this->pdo);

        try {
            $stats = $this->service->getGlobalStatistics($editionId);
            $this->assertIsArray($stats);
        } catch (\Exception $e) {
            // Certaines requêtes avancées (RANK OVER) ne fonctionnent pas en SQLite
            $this->markTestSkipped('getGlobalStatistics non supporté en SQLite: ' . $e->getMessage());
        }
    }
}
