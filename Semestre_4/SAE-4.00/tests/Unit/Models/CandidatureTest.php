<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Candidature;
use Tests\TestCase;

class CandidatureTest extends TestCase
{
    public function testConstructorHydratesAllFields(): void
    {
        $data = [
            'id_candidature'      => 42,
            'libelle'             => 'Mon super post',
            'plateforme'          => 'YouTube',
            'url_contenu'         => 'https://youtube.com/watch?v=abc',
            'image'               => 'post.jpg',
            'argumentaire'        => 'Argumentaire assez long pour passer la validation du modele.',
            'date_soumission'     => '2025-01-15 10:00:00',
            'statut'              => 'En attente',
            'id_compte'           => 7,
            'id_categorie'        => 3,
            'candidat_pseudonyme' => 'alice',
            'candidat_email'      => 'alice@example.com',
            'categorie_nom'       => 'Meilleure video',
            'edition_nom'         => 'Edition 2025',
        ];

        $c = new Candidature($data);

        $this->assertSame(42, $c->getIdCandidature());
        $this->assertSame('Mon super post', $c->getLibelle());
        $this->assertSame('YouTube', $c->getPlateforme());
        $this->assertSame('https://youtube.com/watch?v=abc', $c->getUrlContenu());
        $this->assertSame('post.jpg', $c->getImage());
        $this->assertSame('En attente', $c->getStatut());
        $this->assertSame(7, $c->getIdCompte());
        $this->assertSame(3, $c->getIdCategorie());
        $this->assertSame('alice', $c->getCandidatPseudonyme());
        $this->assertSame('alice@example.com', $c->getCandidatEmail());
        $this->assertSame('Meilleure video', $c->getCategorieNom());
        $this->assertSame('Edition 2025', $c->getEditionNom());
    }

    public function testStatutDefaultsToEnAttente(): void
    {
        $c = new Candidature([
            'libelle'      => 'Test',
            'plateforme'   => 'TikTok',
            'url_contenu'  => 'https://tiktok.com/@user/video/1',
            'argumentaire' => 'Argumentaire assez long pour passer la validation.',
            'id_compte'    => 1,
            'id_categorie' => 1,
        ]);

        $this->assertSame('En attente', $c->getStatut());
    }

    public function testIdCandidatureIsNullByDefault(): void
    {
        $c = new Candidature([
            'libelle'      => 'Test',
            'plateforme'   => 'TikTok',
            'url_contenu'  => 'https://tiktok.com/@user/video/1',
            'argumentaire' => 'Argumentaire assez long pour passer la validation.',
            'id_compte'    => 1,
            'id_categorie' => 1,
        ]);

        $this->assertNull($c->getIdCandidature());
    }

    public function testConstructorWithEmptyArrayDoesNotThrow(): void
    {
        $this->expectNotToPerformAssertions();
        new Candidature([]);
    }

    public function testImageIsNullWhenNotProvided(): void
    {
        $c = new Candidature([
            'libelle'      => 'Test',
            'plateforme'   => 'Instagram',
            'url_contenu'  => 'https://instagram.com/p/1',
            'argumentaire' => 'Argumentaire assez long pour passer la validation.',
            'id_compte'    => 1,
            'id_categorie' => 1,
        ]);

        $this->assertNull($c->getImage());
    }

    public function testSetStatutThrowsForInvalidStatut(): void
    {
        $c = new Candidature([
            'libelle'      => 'Test',
            'plateforme'   => 'YouTube',
            'url_contenu'  => 'https://youtube.com',
            'argumentaire' => 'Argumentaire assez long pour passer la validation.',
            'id_compte'    => 1,
            'id_categorie' => 1,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $c->setStatut('Invalide');
    }

    public function testJoinFieldsAreNullWhenAbsent(): void
    {
        $c = new Candidature([
            'libelle'      => 'Test',
            'plateforme'   => 'YouTube',
            'url_contenu'  => 'https://youtube.com',
            'argumentaire' => 'Argumentaire assez long pour passer la validation.',
            'id_compte'    => 1,
            'id_categorie' => 1,
        ]);

        $this->assertNull($c->getCandidatPseudonyme());
        $this->assertNull($c->getCandidatEmail());
        $this->assertNull($c->getCategorieNom());
        $this->assertNull($c->getEditionNom());
    }
}
