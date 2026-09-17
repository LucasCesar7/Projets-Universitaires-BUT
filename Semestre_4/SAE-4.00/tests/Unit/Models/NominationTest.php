<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Nomination;
use Tests\TestCase;

/**
 * Tests unitaires pour le modèle Nomination.
 */
class NominationTest extends TestCase
{
    public function testConstructorHydratesAllFields(): void
    {
        $data = [
            'id_nomination'   => 10,
            'libelle'         => 'Nomination TikTok',
            'plateforme'      => 'TikTok',
            'url_contenu'     => 'https://tiktok.com/@user/video/1',
            'url_image'       => 'image.jpg',
            'argumentaire'    => 'Excellent contenu tres engageant',  // 20+ chars
            'date_approbation' => '2025-02-01',
            'id_candidature'  => 5,
            'id_categorie'    => 3,
            'id_compte'       => 7,
            'id_admin'        => 1,
        ];

        $n = new Nomination($data);

        $this->assertSame(10, $n->getIdNomination());
        $this->assertSame('Nomination TikTok', $n->getLibelle());
        $this->assertSame('TikTok', $n->getPlateforme());
        $this->assertSame('https://tiktok.com/@user/video/1', $n->getUrlContenu());
        $this->assertSame('image.jpg', $n->getUrlImage());
        $this->assertSame('Excellent contenu tres engageant', $n->getArgumentaire());  // 20+ chars
        $this->assertSame('2025-02-01', $n->getDateApprobation());
        $this->assertSame(5, $n->getIdCandidature());
        $this->assertSame(3, $n->getIdCategorie());
        $this->assertSame(7, $n->getIdCompte());
        $this->assertSame(1, $n->getIdAdmin());
    }

    public function testConstructorAcceptsUrlContentAsAlias(): void
    {
        // La BDD utilise url_content, le modèle doit aussi accepter url_contenu
        $n = new Nomination([
            'libelle'       => 'Test',
            'plateforme'    => 'YouTube',
            'url_content'   => 'https://youtube.com/watch?v=1',
            'argumentaire'  => 'Argumentaire suffisamment long pour la validation du modele.',
            'id_candidature' => 1,
            'id_categorie'  => 1,
        ]);

        $this->assertSame('https://youtube.com/watch?v=1', $n->getUrlContenu());
    }

    public function testOptionalFieldsAreNullByDefault(): void
    {
        $n = new Nomination([
            'libelle'       => 'Test',
            'plateforme'    => 'Instagram',
            'url_contenu'   => 'https://instagram.com/p/1',
            'argumentaire'  => 'Argumentaire suffisamment long pour la validation du modele.',
            'id_candidature' => 1,
            'id_categorie'  => 1,
        ]);

        $this->assertNull($n->getIdNomination());
        $this->assertNull($n->getUrlImage());
        $this->assertNull($n->getDateApprobation());
        $this->assertNull($n->getIdCompte());
        $this->assertNull($n->getIdAdmin());
    }

    public function testConstructorWithEmptyArrayDoesNotThrow(): void
    {
        $this->expectNotToPerformAssertions();
        new Nomination([]);
    }
}
