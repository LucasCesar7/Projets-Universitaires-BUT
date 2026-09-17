<?php

class Film
{
    private $pdo;

    public function __construct()
    {
        $rootPath = dirname(dirname(dirname(__FILE__)));
        require_once $rootPath . '/app/config/config.php';
        $this->pdo = dbconnect();
    }

    function getAllFilmsValides()
    {
        try {
            $query = "SELECT * FROM film WHERE statut_validation = 'VALIDE' ORDER BY titre";
            $stmt = $this->pdo->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllFilmsValides: " . $e->getMessage());
            return [];
        }
    }

    public function getPropositionsEnAttente() {
        $sql = "SELECT f.*, c.pseudo AS nom_candidat
                FROM film f
                LEFT JOIN candidat c ON f.candidat_id_candidat = c.id_candidat
                WHERE f.statut_validation = 'EN_ATTENTE'
                ORDER BY f.id_film ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    function updateValidationStatus($filmId, $adminId, $statut, $motif = null)
    {
        try {
            if (!in_array($statut, ['VALIDE', 'REFUSE'])) {
                throw new InvalidArgumentException("Statut de validation invalide.");
            }

            $sql = "UPDATE film
            SET statut_validation = ?,
                admin_id_administrateur = ?,
                motif_refus = ?
            WHERE id_film = ?";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$statut, $adminId, $motif, $filmId]);
        } catch (PDOException $e) {
            error_log("Erreur mise à jour statut film: " . $e->getMessage());
            return false;
        }
    }

    function addFilm($titre, $description, $annee, $affiche, $duree, $statut, $candidatId, $categories, $acteurs, $realisateurs) {
    try {
        $sql = "INSERT INTO Film (titre, description, annee, url_affiche, duree, statut_validation, candidat_id_candidat)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$titre, $description, $annee, $affiche, $duree, $statut, $candidatId]);

        $filmId = $this->pdo->lastInsertId();

        // Relations catégories
        foreach ($categories as $catId) {
            $this->pdo->prepare("INSERT INTO film_categorie (film_id_film, categorie_id_categorie) VALUES (?, ?)")
                      ->execute([$filmId, $catId]);
        }

        // Relations acteurs
        foreach ($acteurs as $acteurId) {
            $this->pdo->prepare("INSERT INTO acteur_film (acteur_id_acteur, film_id_film) VALUES (?, ?)")
                      ->execute([$filmId, $acteurId]);
        }

        // Relations réalisateurs
        foreach ($realisateurs as $realId) {
            $this->pdo->prepare("INSERT INTO film_realisateur (film_id_film, realisateur_id_realisateur) VALUES (?, ?)")
                      ->execute([$filmId, $realId]);
        }

        return true;
        } catch (PDOException $e) {
            die("Erreur ajout film: " . $e->getMessage());
            return false;
        }
    }

    public function getFilmsWithFilters($search = '', $categorie_id = null) {
        $query = "SELECT DISTINCT f.*, 
                  GROUP_CONCAT(DISTINCT c.libelle ORDER BY c.libelle SEPARATOR ', ') as categories,
                  GROUP_CONCAT(DISTINCT c.id_categorie ORDER BY c.id_categorie) as categories_ids
                  FROM film f
                  LEFT JOIN film_categorie fc ON f.id_film = fc.film_id_film
                  LEFT JOIN categorie c ON fc.categorie_id_categorie = c.id_categorie
                  WHERE f.statut_validation = 'VALIDE'";
        
        $params = [];
        
        // Filtre par recherche (titre du film)
        if (!empty($search)) {
            $query .= " AND f.titre LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }
        
        // Filtre par catégorie
        if (!empty($categorie_id)) {
            $query .= " AND EXISTS (
                SELECT 1 FROM film_categorie fc2 
                WHERE fc2.film_id_film = f.id_film 
                AND fc2.categorie_id_categorie = :categorie_id
            )";
            $params[':categorie_id'] = $categorie_id;
        }
        
        $query .= " GROUP BY f.id_film ORDER BY f.titre ASC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllCategories() {
        $query = "SELECT * FROM categorie ORDER BY libelle ASC";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

}