<?php

/*
* Gestion des votes anonymes
*/
class VoteToken {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Générer une empreinte anonyme stable pour un électeur
     */
    public function generateVoterFingerprint($email) {
        if (empty($email)) {
            throw new Exception("Email requis pour générer l'empreinte");
        }
        
        $salt = VOTE_SALT;
        
        // Normaliser l'email
        $normalizedEmail = strtolower(trim($email));
        
        // Générer un hash à sens unique
        $fingerprint = hash('sha256', $normalizedEmail . $salt);
        
        return $fingerprint;
    }
    
    /**
     * Récupérer l'empreinte de l'électeur connecté
     */
    public function getCurrentVoterFingerprint() {
        if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
            throw new Exception("Électeur non authentifié");
        }
        
        return $this->generateVoterFingerprint($_SESSION['email']);
    }
    
    /**
     * Vérifier si l'électeur a déjà voté
     */
    public function hasVoted($type, $categorie) {
        try {
            $fingerprint = $this->getCurrentVoterFingerprint();
            
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM vote 
                WHERE voter_fingerprint = :fingerprint 
                AND type = :type 
                AND categorie = :categorie
            ");
            $stmt->execute([
                'fingerprint' => $fingerprint,
                'type' => $type,
                'categorie' => $categorie
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (Exception $e) {
            error_log("Erreur hasVoted: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Enregistrer un vote anonyme
     */
    public function recordVote($type, $idCible, $categorie) {
        try {
            $fingerprint = $this->getCurrentVoterFingerprint();
            $ipHash = $this->hashIP();
            
            $stmt = $this->pdo->prepare("
                INSERT INTO vote (voter_fingerprint, type, id_cible, categorie, ip_hash, date_vote) 
                VALUES (:fingerprint, :type, :id_cible, :categorie, :ip_hash, NOW())
            ");
            
            return $stmt->execute([
                'fingerprint' => $fingerprint,
                'type' => $type,
                'id_cible' => $idCible,
                'categorie' => $categorie,
                'ip_hash' => $ipHash
            ]);
            
        } catch (PDOException $e) {
            error_log("Erreur recordVote: " . $e->getMessage());
            throw new Exception("Erreur lors de l'enregistrement du vote");
        }
    }
    
    /**
     * Hash de l'IP (pour prévenir les abus)
     */
    public function hashIP() {
        $ipSalt = IP_SALT;
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        return hash('sha256', $ip . $ipSalt);
    }
    
    /**
     * Statistiques anonymes
     */
    public function getVoteStats() {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    type,
                    categorie,
                    COUNT(DISTINCT voter_fingerprint) as nb_votants,
                    COUNT(*) as nb_votes
                FROM vote
                GROUP BY type, categorie
                ORDER BY type, categorie
            ");
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur getVoteStats: " . $e->getMessage());
            return [];
        }
    }
}
