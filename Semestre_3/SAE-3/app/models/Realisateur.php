<?php
class Realisateur {
    private $pdo;
    
    public function __construct() {
        $rootPath = dirname(dirname(dirname(__FILE__)));
        require_once $rootPath . '/app/config/config.php';
        $this->pdo = dbconnect();
    }

    public function getAllRealisateursValides()
    {
        try {
            $query = "SELECT * FROM realisateur WHERE statut_validation = 'VALIDE' ORDER BY nom";
            $stmt = $this->pdo->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllRealisateursValides: " . $e->getMessage());
            return [];
        }
    }

    public function getPropositionsRealisateursEnAttente()
    {
        $sql = "SELECT r.*, c.pseudo AS nom_candidat
            FROM realisateur r
            LEFT JOIN candidat c ON r.candidat_id_candidat = c.id_candidat
            WHERE r.statut_validation = 'EN_ATTENTE'
            ORDER BY r.id_realisateur ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateValidationStatus($realisateurId, $adminId, $statut, $motif = null)
    {
        try {
            if (!in_array($statut, ['VALIDE', 'REFUSE'])) {
                throw new InvalidArgumentException("Statut de validation invalide.");
            }

            $sql = "UPDATE realisateur
                SET statut_validation = ?,
                    admin_id_administrateur = ?,
                    motif_refus = ?
                WHERE id_realisateur = ?";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$statut, $adminId, $motif, $realisateurId]);
        } catch (PDOException $e) {
            error_log("Erreur mise à jour statut réalisateur: " . $e->getMessage());
            return false;
        }
    }

    public function addRealisateur($nom, $prenom, $nationalite, $date_naissance, $affiche, $candidatId, $films)
    {
        try {
            $sql = "INSERT INTO realisateur (nom, prenom, nationalite, date_naissance, url_photo, statut_validation, candidat_id_candidat)
                    VALUES (?, ?, ?, ?, ?, 'EN_ATTENTE', ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nom, $prenom, $nationalite, $date_naissance, $affiche, $candidatId]);
            
            $realisateurId = $this->pdo->lastInsertId();
            
            // Relations films
            foreach ($films as $filmId) {
                $this->pdo->prepare("INSERT INTO film_realisateur (film_id_film, realisateur_id_realisateur) VALUES (?, ?)")
                    ->execute([$filmId, $realisateurId]);
            }

            return true;
        } catch (PDOException $e) {
            error_log("Erreur ajout réalisateur: " . $e->getMessage());
            return false;
        }
    }

    public function getRealisateursWithFilters($search = '', $categorie_id = null) {
        $query = "SELECT DISTINCT r.*,
                  COUNT(DISTINCT CASE WHEN f.statut_validation = 'VALIDE' THEN f.id_film END) AS nb_films,
                  GROUP_CONCAT(DISTINCT c.libelle ORDER BY c.libelle SEPARATOR ', ') as categories
                  FROM realisateur r
                  LEFT JOIN film_realisateur fr ON r.id_realisateur = fr.realisateur_id_realisateur
                  LEFT JOIN film f ON fr.film_id_film = f.id_film
                  LEFT JOIN film_categorie fc ON f.id_film = fc.film_id_film
                  LEFT JOIN categorie c ON fc.categorie_id_categorie = c.id_categorie
                  WHERE r.statut_validation = 'VALIDE'";
        
        $params = [];
        
        // Filtre par recherche (nom/prénom)
        if ($search !== '' && $search !== null) {
            $query .= " AND (r.nom LIKE ? OR r.prenom LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }
        
        // Filtre par catégorie
        if ($categorie_id !== null && $categorie_id !== '' && $categorie_id > 0) {
            $query .= " AND f.statut_validation = 'VALIDE' 
                        AND EXISTS (
                            SELECT 1 FROM film_categorie fc2
                            WHERE fc2.film_id_film = f.id_film
                            AND fc2.categorie_id_categorie = ?
                        )";
            $params[] = $categorie_id;
        }
        
        $query .= " GROUP BY r.id_realisateur ORDER BY r.nom, r.prenom";
        
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
