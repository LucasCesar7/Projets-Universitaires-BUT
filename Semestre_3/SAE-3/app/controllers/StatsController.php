<?php
class StatsController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Statistiques générales
     */
    public function getGeneralStats() {
        $stats = [];

        // Utilisateurs
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM electeur");
        $stats['total_electeurs'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM candidat");
        $stats['total_candidats'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM administrateur");
        $stats['total_admins'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Contenu validé
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM film WHERE statut_validation = 'VALIDE'");
        $stats['films_valides'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM acteur WHERE statut_validation = 'VALIDE'");
        $stats['acteurs_valides'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM realisateur WHERE statut_validation = 'VALIDE'");
        $stats['realisateurs_valides'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Propositions en attente de validation
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM film WHERE statut_validation = 'EN_ATTENTE'");
        $stats['films_en_attente'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM acteur WHERE statut_validation = 'EN_ATTENTE'");
        $stats['acteurs_en_attente'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM realisateur WHERE statut_validation = 'EN_ATTENTE'");
        $stats['realisateurs_en_attente'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Votes
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM vote");
        $stats['total_votes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Votants uniques
        $stmt = $this->pdo->query("SELECT COUNT(DISTINCT voter_fingerprint) as total FROM vote");
        $stats['votants_uniques'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Commentaires
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM commentaire");
        $stats['total_commentaires'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM commentaire WHERE moderation_status = 'PENDING'");
        $stats['commentaires_en_attente'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return $stats;
    }

    /**
     * Statistiques de catégories
     */
    public function getCategoriesStats() {
        $query = "
            SELECT 
                categorie,
                COUNT(*) as nb_votes
            FROM vote
            GROUP BY categorie
            ORDER BY categorie ASC
        ";
        
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Évolution des votes dans le temps
     */
    public function getVotesTimeline() {
        $query = "
            SELECT 
                DATE(date_vote) as date,
                COUNT(*) as nb_votes
            FROM vote
            GROUP BY DATE(date_vote)
            ORDER BY date ASC
        ";
        
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Nombre de films par catégorie (pour graphique)
     */
    public function getContentByCategory() {
        $query = "
            SELECT 
                c.libelle as categorie,
                COUNT(DISTINCT fc.film_id_film) as nb_films
            FROM categorie c
            LEFT JOIN film_categorie fc ON c.id_categorie = fc.categorie_id_categorie
            LEFT JOIN film f ON fc.film_id_film = f.id_film
            WHERE f.statut_validation = 'VALIDE'
            GROUP BY c.id_categorie, c.libelle
            ORDER BY c.libelle ASC
        ";
        
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Statistiques de contenu par type de vote
     */
    public function getContentStats() {
        $query = "
            SELECT 
                type,
                COUNT(*) as nb_votes
            FROM vote
            GROUP BY type
            ORDER BY type ASC
        ";
        
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
