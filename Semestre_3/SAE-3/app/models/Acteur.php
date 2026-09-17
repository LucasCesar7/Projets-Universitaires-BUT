<?php
class Acteur {
    private $pdo;
    
    public function __construct() {
        $rootPath = dirname(dirname(dirname(__FILE__)));
        require_once $rootPath . '/app/config/config.php';
        $this->pdo = dbconnect();
    }

    public function getAllActeursValides()
    {
        try {
            $query = "SELECT * FROM acteur WHERE statut_validation = 'VALIDE' ORDER BY nom";
            $stmt = $this->pdo->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllActeursValides: " . $e->getMessage());
            return [];
        }
    }

    public function getPropositionsActeursEnAttente()
    {
        $sql = "SELECT a.*, c.pseudo AS nom_candidat
            FROM acteur a
            LEFT JOIN candidat c ON a.candidat_id_candidat = c.id_candidat
            WHERE a.statut_validation = 'EN_ATTENTE'
            ORDER BY a.id_acteur ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateValidationStatus($acteurId, $adminId, $statut, $motif = null)
    {
        try {
            if (!in_array($statut, ['VALIDE', 'REFUSE'])) {
                throw new InvalidArgumentException("Statut de validation invalide.");
            }

            $sql = "UPDATE acteur
                SET statut_validation = ?,
                    admin_id_administrateur = ?,
                    motif_refus = ?
                WHERE id_acteur = ?";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$statut, $adminId, $motif, $acteurId]);
        } catch (PDOException $e) {
            error_log("Erreur mise à jour statut acteur: " . $e->getMessage());
            return false;
        }
    }

    public function addActeur($nom, $prenom, $nationalite, $date_naissance, $affiche, $candidatId, $films)
    {
        try {
            $sql = "INSERT INTO acteur (nom, prenom, nationalite, date_naissance, url_photo, statut_validation, candidat_id_candidat)
                    VALUES (?, ?, ?, ?, ?, 'EN_ATTENTE', ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nom, $prenom, $nationalite, $date_naissance, $affiche, $candidatId]);

            $acteurId = $this->pdo->lastInsertId();

            // Relations films
            foreach ($films as $filmId) {
                $this->pdo->prepare("INSERT INTO acteur_film (acteur_id_acteur, film_id_film) VALUES (?, ?)")
                    ->execute([$acteurId, $filmId]);
            }

            return true;
        } catch (PDOException $e) {
            error_log("Erreur ajout acteur: " . $e->getMessage());
            return false;
        }
    }

    public function getActeursWithFilters($search = '', $categorie_id = null) {
        $query = "SELECT DISTINCT a.*,
            COUNT(DISTINCT CASE WHEN f.statut_validation = 'VALIDE' THEN f.id_film END) AS nb_films,
            GROUP_CONCAT(DISTINCT c.libelle ORDER BY c.libelle SEPARATOR ', ') as categories
            FROM acteur a
            LEFT JOIN acteur_film af ON a.id_acteur = af.acteur_id_acteur
            LEFT JOIN film f ON af.film_id_film = f.id_film
            LEFT JOIN film_categorie fc ON f.id_film = fc.film_id_film
            LEFT JOIN categorie c ON fc.categorie_id_categorie = c.id_categorie
            WHERE a.statut_validation = 'VALIDE'";
        
        $params = [];
        
        // Filtre par recherche (nom/prénom)
        if ($search !== '' && $search !== null) {
            $query .= " AND (a.nom LIKE ? OR a.prenom LIKE ?)";  
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
        
        $query .= " GROUP BY a.id_acteur ORDER BY a.nom, a.prenom";
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
