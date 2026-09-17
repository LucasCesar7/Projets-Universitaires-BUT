<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Categorie;
use Tests\TestCase;

/**
 * Tests unitaires pour le modèle Categorie.
 */
class CategorieTest extends TestCase
{
    public function testConstructorHydratesAllFields(): void
    {
        $data = [
            'id_categorie'        => 5,
            'nom'                 => 'Meilleure vidéo',
            'description'         => 'La meilleure vidéo de l\'année',
            'image'               => 'video.jpg',
            'plateforme_cible'    => 'YouTube',
            'limite_nomines'      => 8,
            'date_debut_votes'    => '2025-03-01',
            'date_fin_votes'      => '2025-03-31',
            'id_edition'          => 1,
            'edition_nom'         => 'Édition 2025',
            'nb_candidatures'     => 12,
            'nb_nominations'      => 4,
        ];

        $cat = new Categorie($data);

        $this->assertSame(5, $cat->getIdCategorie());
        $this->assertSame('Meilleure vidéo', $cat->getNom());
        $this->assertSame('La meilleure vidéo de l\'année', $cat->getDescription());
        $this->assertSame('video.jpg', $cat->getImage());
        $this->assertSame('YouTube', $cat->getPlateformeCible());
        $this->assertSame(8, $cat->getLimiteNomines());
        $this->assertSame('2025-03-01', $cat->getDateDebutVotes());
        $this->assertSame('2025-03-31', $cat->getDateFinVotes());
        $this->assertSame(1, $cat->getIdEdition());
        $this->assertSame('Édition 2025', $cat->getEditionNom());
        $this->assertSame(12, $cat->getNbCandidatures());
        $this->assertSame(4, $cat->getNbNominations());
    }

    public function testDefaultPlateformeCible(): void
    {
        $cat = new Categorie([
            'nom'        => 'Catégorie X',
            'id_edition' => 1,
        ]);

        $this->assertSame('Toutes', $cat->getPlateformeCible());
    }

    public function testDefaultLimiteNomines(): void
    {
        $cat = new Categorie([
            'nom'        => 'Catégorie X',
            'id_edition' => 1,
        ]);

        $this->assertSame(10, $cat->getLimiteNomines());
    }

    public function testDefaultCounts(): void
    {
        $cat = new Categorie([
            'nom'        => 'Catégorie X',
            'id_edition' => 1,
        ]);

        $this->assertSame(0, $cat->getNbCandidatures());
        $this->assertSame(0, $cat->getNbNominations());
    }

    public function testIdCategorieIsNullByDefault(): void
    {
        $cat = new Categorie([
            'nom'        => 'Catégorie X',
            'id_edition' => 1,
        ]);

        $this->assertNull($cat->getIdCategorie());
    }

    public function testConstructorWithEmptyArrayDoesNotThrow(): void
    {
        $this->expectNotToPerformAssertions();
        new Categorie([]);
    }
}
