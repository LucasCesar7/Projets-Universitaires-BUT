<?php

class ResultatModel {
    private $pdo;
    
    public function __construct() {
        $rootPath = dirname(dirname(dirname(__FILE__)));
        require_once $rootPath . '/app/config/config.php';
        $this->pdo = dbconnect();
    }
    
    /**
     * Récupère les résultats des films par catégorie
     */
    public function getResultatsFilms() {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    c.libelle as categorie,
                    f.id_film,
                    f.titre,
                    f.annee,
                    f.url_affiche,
                    COUNT(v.id_vote) as nb_votes,
                    (COUNT(v.id_vote) * 100.0 / NULLIF(
                        (SELECT COUNT(*) 
                         FROM vote v2 
                         JOIN film_categorie fc2 ON v2.id_cible = fc2.film_id_film 
                         WHERE fc2.categorie_id_categorie = c.id_categorie 
                         AND v2.type = 'film'
                         AND v2.categorie COLLATE utf8mb4_0900_ai_ci = c.libelle COLLATE utf8mb4_0900_ai_ci), 0
                    )) as pourcentage
                FROM film f
                INNER JOIN film_categorie fc ON f.id_film = fc.film_id_film
                INNER JOIN categorie c ON fc.categorie_id_categorie = c.id_categorie
                LEFT JOIN vote v ON v.id_cible = f.id_film 
                    AND v.type = 'film' 
                    AND v.categorie COLLATE utf8mb4_0900_ai_ci = c.libelle COLLATE utf8mb4_0900_ai_ci
                WHERE f.statut_validation = 'VALIDE'
                GROUP BY f.id_film, f.titre, f.annee, f.url_affiche, c.libelle, c.id_categorie
                ORDER BY c.libelle ASC, nb_votes DESC, f.titre ASC
            ");
            
            $results = $stmt->fetchAll();
            
            // Grouper par catégorie
            $grouped = [];
            foreach ($results as $row) {
                $categorie = $row['categorie'];
                if (!isset($grouped[$categorie])) {
                    $grouped[$categorie] = [];
                }
                $grouped[$categorie][] = $row;
            }
            
            return $grouped;
            
        } catch (PDOException $e) {
            error_log("Erreur getResultatsFilms: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère les résultats des acteurs par catégorie
     */
    public function getResultatsActeurs() {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    v.categorie,
                    a.id_acteur,
                    a.nom,
                    a.prenom,
                    a.nationalite,
                    a.url_photo,
                    COUNT(v.id_vote) as nb_votes,
                    (COUNT(v.id_vote) * 100.0 / NULLIF(
                        (SELECT COUNT(*) 
                         FROM vote v2 
                         WHERE v2.type = 'acteur' 
                         AND v2.categorie COLLATE utf8mb4_0900_ai_ci = v.categorie COLLATE utf8mb4_0900_ai_ci), 0
                    )) as pourcentage
                FROM acteur a
                INNER JOIN vote v ON v.id_cible = a.id_acteur AND v.type = 'acteur'
                WHERE a.statut_validation = 'VALIDE'
                GROUP BY a.id_acteur, a.nom, a.prenom, a.nationalite, a.url_photo, v.categorie
                ORDER BY v.categorie ASC, nb_votes DESC, a.nom ASC
            ");
            
            $results = $stmt->fetchAll();
            
            // Grouper par catégorie
            $grouped = [];
            foreach ($results as $row) {
                $categorie = $row['categorie'];
                if (!isset($grouped[$categorie])) {
                    $grouped[$categorie] = [];
                }
                $grouped[$categorie][] = $row;
            }
            
            return $grouped;
            
        } catch (PDOException $e) {
            error_log("Erreur getResultatsActeurs: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère les résultats des réalisateurs par catégorie
     */
    public function getResultatsRealisateurs() {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    v.categorie,
                    r.id_realisateur,
                    r.nom,
                    r.prenom,
                    r.nationalite,
                    r.url_photo,
                    COUNT(v.id_vote) as nb_votes,
                    (COUNT(v.id_vote) * 100.0 / NULLIF(
                        (SELECT COUNT(*) 
                         FROM vote v2 
                         WHERE v2.type = 'realisateur' 
                         AND v2.categorie COLLATE utf8mb4_0900_ai_ci = v.categorie COLLATE utf8mb4_0900_ai_ci), 0
                    )) as pourcentage
                FROM realisateur r
                INNER JOIN vote v ON v.id_cible = r.id_realisateur AND v.type = 'realisateur'
                WHERE r.statut_validation = 'VALIDE'
                GROUP BY r.id_realisateur, r.nom, r.prenom, r.nationalite, r.url_photo, v.categorie
                ORDER BY v.categorie ASC, nb_votes DESC, r.nom ASC
            ");
            
            $results = $stmt->fetchAll();
            
            // Grouper par catégorie
            $grouped = [];
            foreach ($results as $row) {
                $categorie = $row['categorie'];
                if (!isset($grouped[$categorie])) {
                    $grouped[$categorie] = [];
                }
                $grouped[$categorie][] = $row;
            }
            
            return $grouped;
            
        } catch (PDOException $e) {
            error_log("Erreur getResultatsRealisateurs: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère les statistiques globales
     */
    public function getStatistiques() {
        try {
            // Total des votes
            $stmtVotes = $this->pdo->query("SELECT COUNT(*) as total FROM vote");
            $totalVotes = $stmtVotes->fetch()['total'];
            
            // Total des électeurs vérifiés
            $stmtElecteurs = $this->pdo->query("SELECT COUNT(*) as total FROM electeur WHERE email_verifie = 1");
            $totalElecteurs = $stmtElecteurs->fetch()['total'];
            
            // Total des catégories
            $stmtCategories = $this->pdo->query("SELECT COUNT(*) as total FROM categorie");
            $totalCategories = $stmtCategories->fetch()['total'];
            
            // Électeurs ayant voté (basé sur voter_fingerprint unique)
            $stmtVotants = $this->pdo->query("SELECT COUNT(DISTINCT voter_fingerprint) as total FROM vote");
            $totalVotants = $stmtVotants->fetch()['total'];
            
            // Taux de participation
            $tauxParticipation = $totalElecteurs > 0 ? ($totalVotants / $totalElecteurs) * 100 : 0;
            
            return [
                'total_votes' => $totalVotes,
                'total_electeurs' => $totalElecteurs,
                'total_categories' => $totalCategories,
                'total_votants' => $totalVotants,
                'taux_participation' => round($tauxParticipation, 2)
            ];
            
        } catch (PDOException $e) {
            error_log("Erreur getStatistiques: " . $e->getMessage());
            return [
                'total_votes' => 0,
                'total_electeurs' => 0,
                'total_categories' => 0,
                'total_votants' => 0,
                'taux_participation' => 0
            ];
        }
    }
    
    /**
     * Récupère le top 3 global (tous genres confondus)
     */
    public function getTop3Global() {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    f.titre,
                    f.annee,
                    f.url_affiche,
                    c.libelle as categorie,
                    COUNT(v.id_vote) as nb_votes
                FROM film f
                INNER JOIN film_categorie fc ON f.id_film = fc.film_id_film
                INNER JOIN categorie c ON fc.categorie_id_categorie = c.id_categorie
                LEFT JOIN vote v ON v.id_cible = f.id_film AND v.type = 'film'
                WHERE f.statut_validation = 'VALIDE'
                GROUP BY f.id_film, f.titre, f.annee, f.url_affiche, c.libelle
                ORDER BY nb_votes DESC
                LIMIT 3
            ");
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Erreur getTop3Global: " . $e->getMessage());
            return [];
        }
    }
}
?>
