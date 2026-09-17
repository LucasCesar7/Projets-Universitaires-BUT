<?php

/*
 * definition de la configuration de cinevote
 */
class Configuration {
    // connection a la BDD
    private $pdo;

    public function __construct() {
        $rootPath = dirname(dirname(dirname(__FILE__)));
        require_once $rootPath . '/app/config/config.php';
        $this->pdo = dbconnect();
    }

    /**
     * Récupère une valeur de configuration
     */
    public function getConfig($cle) {
        try {
            $stmt = $this->pdo->prepare("SELECT valeur FROM configuration WHERE cle = ?");
            $stmt->execute([$cle]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Log pour debug
            if (!$result) {
                error_log("Configuration '$cle' non trouvée dans la BDD");
                return null;
            }
            
            return $result['valeur'];
        } catch (PDOException $e) {
            error_log("Erreur getConfig($cle): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Met à jour une valeur de configuration
     */
    public function updateConfig($cle, $valeur, $adminId = null) {
        try {
            // Convertir les dates datetime-local en format MySQL
            if (in_array($cle, ['periode_ajout_debut', 'periode_ajout_fin', 'periode_vote_debut', 'periode_vote_fin'])) {
                // Format reçu du formulaire : "2025-12-15T08:00"
                $date = DateTime::createFromFormat('Y-m-d\TH:i', $valeur);
                if ($date) {
                    // Conversion en format MySQL : "2025-12-15 08:00:00"
                    $valeur = $date->format('Y-m-d H:i:s');
                }
            }

            $stmt = $this->pdo->prepare("
                UPDATE configuration
                SET valeur = ?, modifiePar = ?, dateModification = NOW()
                WHERE cle = ?
            ");
            
            $result = $stmt->execute([$valeur, $adminId, $cle]);
            
            if ($result) {
                error_log("Config '$cle' mise à jour : $valeur");
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur updateConfig($cle): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère toutes les configurations
     */
    public function getAllConfigs() {
        try {
            $stmt = $this->pdo->query("
                SELECT c.*, a.nom as modifiePar_nom, a.prenom as modifiePar_prenom
                FROM configuration c
                LEFT JOIN Administrateur a ON c.modifiePar = a.id_administrateur
                ORDER BY c.cle ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllConfigs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Vérifie si on est dans la période d'ajout des œuvres
     */
    public function isInPeriodeAjout() {
        $debut = $this->getConfig('periode_ajout_debut');
        $fin = $this->getConfig('periode_ajout_fin');
        
        if (!$debut || !$fin) return false;
        
        try {
            $now = new DateTime('now', new DateTimeZone('Europe/Paris'));
            $dateDebut = new DateTime($debut, new DateTimeZone('Europe/Paris'));
            $dateFin = new DateTime($fin, new DateTimeZone('Europe/Paris'));
            
            return ($now >= $dateDebut && $now <= $dateFin);
        } catch (Exception $e) {
            error_log("Erreur isInPeriodeAjout: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si on est dans la période de vote
     */
    public function isInPeriodeVote() {
        $debut = $this->getConfig('periode_vote_debut');
        $fin = $this->getConfig('periode_vote_fin');
        
        if (!$debut || !$fin) return false;
        
        try {
            $now = new DateTime('now', new DateTimeZone('Europe/Paris'));
            $dateDebut = new DateTime($debut, new DateTimeZone('Europe/Paris'));
            $dateFin = new DateTime($fin, new DateTimeZone('Europe/Paris'));
            
            return ($now >= $dateDebut && $now <= $dateFin);
        } catch (Exception $e) {
            error_log("Erreur isInPeriodeVote: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si les résultats sont disponibles (après la fin du vote)
     */
    public function areResultatsDisponibles() {
        $fin = $this->getConfig('periode_vote_fin');
        
        if (!$fin) return false;
        
        try {
            $now = new DateTime('now', new DateTimeZone('Europe/Paris'));
            $dateFin = new DateTime($fin, new DateTimeZone('Europe/Paris'));
            
            return ($now > $dateFin);
        } catch (Exception $e) {
            error_log("Erreur areResultatsDisponibles: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtient le statut actuel du système
     */
    public function getStatutSysteme() {
        if ($this->areResultatsDisponibles()) {
            return 'RESULTATS';
        } elseif ($this->isInPeriodeVote()) {
            return 'VOTE';
        } elseif ($this->isInPeriodeAjout()) {
            return 'AJOUT';
        } else {
            return 'INACTIF';
        }
    }
}
?>
