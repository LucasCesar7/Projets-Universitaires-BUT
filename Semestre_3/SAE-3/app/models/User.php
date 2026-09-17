<?php
/*
* definition d'un utilisateur de notre site
*/
class User {
    // connection a la BDD
    private $pdo;
    private $lastInsertId;

    public function __construct() {
        $rootPath = dirname(dirname(dirname(__FILE__)));
        require_once $rootPath . '/app/config/config.php';
        $this->pdo = dbconnect();
    }

    public function createElecteur($nom, $prenom, $email, $motDePasse, $pseudo, $dateNaissance, $accepteConditions) {
        try {
            $this->pdo->beginTransaction();
            $token = bin2hex(random_bytes(32)); //générer un token aléatoire
            //token expire après 5min
            $tokenExpiration = date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime(date('Y-m-d H:i:s'))));
            
            $stmt = $this->pdo->prepare("
                INSERT INTO electeur
                (nom, prenom, email, mot_de_passe, pseudo, date_naissance, date_inscription, accepte_conditions, token, token_expiration, email_verifie)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, 0)
            ");
            
            $result = $stmt->execute([
                $nom, $prenom, $email, $motDePasse, $pseudo, $dateNaissance,
                $accepteConditions ? 1 : 0,
                $token,
                $tokenExpiration
            ]);
            
            if ($result) {
                $this->lastInsertId = $this->pdo->lastInsertId();
            }
            
            $this->pdo->commit();
            return $result;
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur création électeur: " . $e->getMessage());
            return false;
        }
    }

    public function createCandidat($nom, $prenom, $email, $motDePasse, $pseudo, $accepteConditions) {
        try {
            $this->pdo->beginTransaction();

                // Générer un token de vérification
            $token = bin2hex(random_bytes(32));
            $tokenExpiration = date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            $stmt = $this->pdo->prepare("
                INSERT INTO candidat 
                (nom, prenom, email, pseudo, mot_de_passe, date_inscription, 
                accepte_conditions, token, token_expiration, email_verifie)
                VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, 0)
            ");
            
            $result = $stmt->execute([$nom, $prenom, $email, $pseudo, $motDePasse, $accepteConditions ? 1 : 0, $token, $tokenExpiration]);
            
            //Recuperer l'ID AVANT le commit
            if ($result) {
                $this->lastInsertId = $this->pdo->lastInsertId();
            }
            
            $this->pdo->commit();
            return $result;
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur création candidat: " . $e->getMessage());
            return false;
        }
    }

     /**
     * Récupérer le token pour un utilisateur
     */
    public function getToken($userId, $role) {
        try {
            if ($role === 'electeur') {
                $stmt = $this->pdo->prepare("SELECT token FROM electeur WHERE id_electeur = ?");
            } else {
                $stmt = $this->pdo->prepare("SELECT token FROM candidat WHERE id_candidat = ?");
            }
            
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['token'] ?? null;
            
        } catch (PDOException $e) {
            error_log("Erreur getToken: " . $e->getMessage());
            return null;
        }
    }

    //permet un email unique par utilisateur
    public function emailExists($email) {
        try {
            $tables = ['electeur', 'candidat', 'administrateur'];
            
            foreach ($tables as $table) {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM $table WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->fetchColumn() > 0) {
                    return true;
                }
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Erreur emailExists: " . $e->getMessage());
            return false;
        }
    }

    //permet un pseudo unique par utilisateur
    public function pseudoExists($pseudo) {
        try {
            if (empty($pseudo)) {
                return false;
            }
            
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM electeur WHERE pseudo = ?");
            $stmt->execute([$pseudo]);
            
            if ($stmt->fetchColumn() > 0){
                return true;
            }

            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM candidat WHERE pseudo = ?");
            $stmt->execute([$pseudo]);
            
            if ($stmt->fetchColumn() > 0){
                return true;
            }

            return false;


        } catch (PDOException $e) {
            error_log("Erreur pseudoExists: " . $e->getMessage());
            return false;
        }
    }

    public function getLastInsertId() {
        return $this->lastInsertId;
    }

    /**
     * Récupérer les informations d'un utilisateur en fonction de sa casquette
     */
    public function getUserInfo($userId, $role) {
        if ($role === 'electeur') {
            $stmt = $this->pdo->prepare("
                SELECT id_electeur as id, nom, prenom, email, pseudo, date_naissance, email_verifie
                FROM electeur 
                WHERE id_electeur = ?
            ");
        } elseif ($role === 'candidat') {
            $stmt = $this->pdo->prepare("
                SELECT id_candidat as id, nom, prenom, email, pseudo, email_verifie
                FROM candidat 
                WHERE id_candidat = ?
            ");
        } elseif ($role === 'admin') {
            $stmt = $this->pdo->prepare("
                SELECT id_administrateur as id, nom, prenom, email, email_verifie
                FROM administrateur 
                WHERE id_administrateur = ?
            ");
        } else {
            return null;
        }
        
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Mettre à jour les informations personnelles
     */
    public function updateProfile($userId, $role, $data) {
        if ($role === 'electeur') {
            $stmt = $this->pdo->prepare("
                UPDATE electeur 
                SET nom = ?, prenom = ?, pseudo = ?, date_naissance = ?
                WHERE id_electeur = ?
            ");
            $stmt->execute([
                $data['nom'],
                $data['prenom'],
                $data['pseudo'],
                $data['date_naissance'] ?? null,
                $userId
            ]);
        } elseif ($role === 'candidat') {
            $stmt = $this->pdo->prepare("
                UPDATE candidat 
                SET nom = ?, prenom = ?, pseudo = ?
                WHERE id_candidat = ?
            ");
            $stmt->execute([
                $data['nom'],
                $data['prenom'],
                $data['pseudo'],
                $userId
            ]);
        } elseif ($role === 'admin') {
            $stmt = $this->pdo->prepare("
                UPDATE administrateur 
                SET nom = ?, prenom = ?
                WHERE id_administrateur = ?
            ");
            $stmt->execute([
                $data['nom'],
                $data['prenom'],
                $userId
            ]);
        }
        
        return true;
    }
    
    /**
     * Changer le mot de passe
     */
    public function updatePassword($userId, $role, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        if ($role === 'electeur') {
            $stmt = $this->pdo->prepare("UPDATE electeur SET mot_de_passe = ? WHERE id_electeur = ?");
        } elseif ($role === 'candidat') {
            $stmt = $this->pdo->prepare("UPDATE candidat SET mot_de_passe = ? WHERE id_candidat = ?");
        } elseif ($role === 'admin') {
            $stmt = $this->pdo->prepare("UPDATE administrateur SET mot_de_passe = ? WHERE id_administrateur = ?");
        }
        
        $stmt->execute([$hashedPassword, $userId]);
        return true;
    }
    
    /**
     * Vérifier le mot de passe actuel
     */
    public function verifyPassword($userId, $role, $password) {
        if ($role === 'electeur') {
            $stmt = $this->pdo->prepare("SELECT mot_de_passe FROM electeur WHERE id_electeur = ?");
        } elseif ($role === 'candidat') {
            $stmt = $this->pdo->prepare("SELECT mot_de_passe FROM candidat WHERE id_candidat = ?");
        } elseif ($role === 'admin') {
            $stmt = $this->pdo->prepare("SELECT mot_de_passe FROM administrateur WHERE id_administrateur = ?");
        }
        
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            return password_verify($password, $user['mot_de_passe']);
        }
        
        return false;
    }

}

?>
