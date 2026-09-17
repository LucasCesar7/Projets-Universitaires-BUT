<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PDO;

/**
 * Classe de base pour tous les tests du projet Social Media Awards.
 *
 * Fournit :
 *  - createSqlitePdo() : PDO SQLite en mémoire avec le schéma minimal nécessaire
 *    aux services (candidature, nomination, categorie, compte, edition).
 *  - insert*() : méthodes d'aide pour insérer des fixtures.
 */
abstract class TestCase extends BaseTestCase
{
    // ── Helpers PDO ──────────────────────────────────────────────────────────

    /**
     * Crée une connexion PDO SQLite en mémoire et initialise le schéma minimal.
     */
    protected function createSqlitePdo(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $pdo->exec($this->sqliteSchema());

        return $pdo;
    }

    /**
     * Schéma SQLite minimal utilisé par les tests d'intégration.
     */
    private function sqliteSchema(): string
    {
        return <<<'SQL'
            CREATE TABLE IF NOT EXISTS edition (
                id_edition   INTEGER PRIMARY KEY AUTOINCREMENT,
                nom          TEXT    NOT NULL,
                annee        INTEGER,
                date_debut   TEXT,
                date_fin     TEXT,
                date_debut_candidatures TEXT,
                date_fin_candidatures   TEXT,
                est_active   INTEGER DEFAULT 0,
                theme        TEXT,
                image        TEXT,
                description  TEXT
            );

            CREATE TABLE IF NOT EXISTS compte (
                id_compte    INTEGER PRIMARY KEY AUTOINCREMENT,
                pseudonyme   TEXT,
                email        TEXT,
                mot_de_passe TEXT,
                role         TEXT DEFAULT 'voter'
            );

            CREATE TABLE IF NOT EXISTS categorie (
                id_categorie         INTEGER PRIMARY KEY AUTOINCREMENT,
                nom                  TEXT    NOT NULL,
                description          TEXT,
                image                TEXT,
                plateforme_cible     TEXT    DEFAULT 'Toutes',
                limite_nomines       INTEGER DEFAULT 10,
                date_debut_votes     TEXT,
                date_fin_votes       TEXT,
                date_fin_candidatures TEXT,
                id_edition           INTEGER,
                FOREIGN KEY (id_edition) REFERENCES edition(id_edition)
            );

            CREATE TABLE IF NOT EXISTS candidature (
                id_candidature INTEGER PRIMARY KEY AUTOINCREMENT,
                libelle        TEXT    NOT NULL,
                plateforme     TEXT    NOT NULL,
                url_contenu    TEXT    NOT NULL,
                image          TEXT,
                argumentaire   TEXT    NOT NULL,
                date_soumission TEXT   DEFAULT (datetime('now')),
                statut         TEXT    DEFAULT 'En attente',
                supprime_le    TEXT,
                id_compte      INTEGER NOT NULL,
                id_categorie   INTEGER NOT NULL,
                FOREIGN KEY (id_compte)    REFERENCES compte(id_compte),
                FOREIGN KEY (id_categorie) REFERENCES categorie(id_categorie)
            );

            CREATE TABLE IF NOT EXISTS nomination (
                id_nomination   INTEGER PRIMARY KEY AUTOINCREMENT,
                libelle         TEXT    NOT NULL,
                plateforme      TEXT    NOT NULL,
                url_content     TEXT    NOT NULL,
                url_image       TEXT,
                argumentaire    TEXT    NOT NULL,
                date_approbation TEXT,
                supprime_le     TEXT,
                id_candidature  INTEGER,
                id_categorie    INTEGER NOT NULL,
                id_compte       INTEGER,
                id_admin        INTEGER,
                FOREIGN KEY (id_categorie) REFERENCES categorie(id_categorie)
            );
        SQL;
    }

    // ── Fixtures ─────────────────────────────────────────────────────────────

    protected function insertEdition(PDO $pdo, array $overrides = []): int
    {
        $row = array_merge([
            'nom'        => 'Édition Test 2025',
            'annee'      => 2025,
            'est_active' => 1,
        ], $overrides);

        $pdo->prepare(
            'INSERT INTO edition (nom, annee, est_active) VALUES (:nom, :annee, :est_active)'
        )->execute($row);

        return (int) $pdo->lastInsertId();
    }

    protected function insertCompte(PDO $pdo, array $overrides = []): int
    {
        $row = array_merge([
            'pseudonyme'   => 'user_test',
            'email'        => 'test@example.com',
            'mot_de_passe' => password_hash('secret', PASSWORD_BCRYPT),
            'role'         => 'candidate',
        ], $overrides);

        $pdo->prepare(
            'INSERT INTO compte (pseudonyme, email, mot_de_passe, role)
             VALUES (:pseudonyme, :email, :mot_de_passe, :role)'
        )->execute($row);

        return (int) $pdo->lastInsertId();
    }

    protected function insertCategorie(PDO $pdo, int $editionId, array $overrides = []): int
    {
        $row = array_merge([
            'nom'        => 'Catégorie Test',
            'id_edition' => $editionId,
        ], $overrides);

        $pdo->prepare(
            'INSERT INTO categorie (nom, id_edition) VALUES (:nom, :id_edition)'
        )->execute($row);

        return (int) $pdo->lastInsertId();
    }

    protected function insertCandidature(PDO $pdo, int $compteId, int $categorieId, array $overrides = []): int
    {
        $row = array_merge([
            'libelle'      => 'Ma candidature',
            'plateforme'   => 'YouTube',
            'url_contenu'  => 'https://youtube.com/watch?v=test',
            'argumentaire' => 'Mon argumentaire',
            'statut'       => 'En attente',
            'supprime_le'  => null,
            'id_compte'    => $compteId,
            'id_categorie' => $categorieId,
        ], $overrides);

        $pdo->prepare(
            'INSERT INTO candidature
                (libelle, plateforme, url_contenu, argumentaire, statut, supprime_le, id_compte, id_categorie)
             VALUES
                (:libelle, :plateforme, :url_contenu, :argumentaire, :statut, :supprime_le, :id_compte, :id_categorie)'
        )->execute($row);

        return (int) $pdo->lastInsertId();
    }

    protected function insertNomination(PDO $pdo, int $categorieId, array $overrides = []): int
    {
        $row = array_merge([
            'libelle'      => 'Ma nomination',
            'plateforme'   => 'Instagram',
            'url_content'  => 'https://instagram.com/p/test',
            'argumentaire' => 'Argumentaire nomination',
            'supprime_le'  => null,
            'id_candidature' => null,
            'id_categorie' => $categorieId,
        ], $overrides);

        $pdo->prepare(
            'INSERT INTO nomination
                (libelle, plateforme, url_content, argumentaire, supprime_le, id_candidature, id_categorie)
             VALUES
                (:libelle, :plateforme, :url_content, :argumentaire, :supprime_le, :id_candidature, :id_categorie)'
        )->execute($row);

        return (int) $pdo->lastInsertId();
    }
}
