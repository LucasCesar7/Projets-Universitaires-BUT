<?php
require_once __DIR__ . '/../helpers/CsrfHelper.php';


class PasswordResetController {
    
    /**
     * Demande de réinitialisation de mot de passe
     */
    public function requestReset() {
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CsrfHelper::validateToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = "Token de sécurité invalide.";
                header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
                exit;
            }

            $email = trim($_POST['email'] ?? '');
            
            // Validation
            if (empty($email)) {
                $errors[] = "L'email est requis.";
                return $errors;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email invalide.";
                return $errors;
            }
            
            try {
                $rootPath = dirname(dirname(dirname(__FILE__)));
                require_once $rootPath . '/app/config/config.php';
                
                $pdo = dbconnect();
                
                // Rechercher l'utilisateur dans les 3 tables
                $user = null;
                $role = null;
                $tableName = null;
                $idColumn = null;
                
                // Chercher dans Electeur
                $stmt = $pdo->prepare("SELECT id_electeur as id, nom, prenom, email FROM electeur WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                if ($user) {
                    $role = 'electeur';
                    $tableName = 'electeur';
                    $idColumn = 'id_electeur';
                }
                
                // Chercher dans Candidat
                if (!$user) {
                    $stmt = $pdo->prepare("SELECT id_candidat as id, nom, prenom, email FROM candidat WHERE email = ?");
                    $stmt->execute([$email]);
                    $user = $stmt->fetch();
                    if ($user) {
                        $role = 'candidat';
                        $tableName = 'candidat';
                        $idColumn = 'id_candidat';
                    }
                }
                
                // Chercher dans Administrateur
                if (!$user) {
                    $stmt = $pdo->prepare("SELECT id_administrateur as id, nom, prenom, email FROM administrateur WHERE email = ?");
                    $stmt->execute([$email]);
                    $user = $stmt->fetch();
                    if ($user) {
                        $role = 'admin';
                        $tableName = 'administrateur';
                        $idColumn = 'id_administrateur';
                    }
                }
                
                // Même si l'utilisateur n'existe pas, on affiche le même message
                // pour ne pas révéler si un email est enregistré ou non (sécurité)
                if ($user) {
                    // Générer un token unique
                    $token = bin2hex(random_bytes(32));
                    
                    // Expiration dans 1 heure
                    $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Stocker le token en BDD
                    $stmt = $pdo->prepare("
                        UPDATE $tableName 
                        SET resetToken = ?, resetTokenExpiration = ? 
                        WHERE $idColumn = ?
                    ");
                    $stmt->execute([$token, $expiration, $user['id']]);
                    
                    // Envoyer l'email
                    require_once $rootPath . '/app/helpers/EmailHelper.php';
                    $emailHelper = new EmailHelper();
                    $emailHelper->sendPasswordResetEmail($email, $token, $user['prenom']);
                    
                    error_log("Token de réinitialisation généré pour $email (rôle: $role)");
                }
                
                // Message générique (même si l'email n'existe pas)
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['success'] = "Si cet email est enregistré, vous recevrez un lien de réinitialisation dans quelques instants.";
                
                header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
                exit;
                
            } catch (PDOException $e) {
                error_log("Erreur lors de la demande de reset: " . $e->getMessage());
                $errors[] = "Une erreur est survenue. Veuillez réessayer.";
            }
        }
        
        return $errors;
    }
    
    /**
     * Réinitialisation du mot de passe
     */
    public function resetPassword() {
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CsrfHelper::validateToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = "Token de sécurité invalide.";
                header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
                exit;
            }
            $token = trim($_POST['token'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validation
            if (empty($token)) {
                $errors[] = "Token invalide.";
                return $errors;
            }
            
            if (empty($newPassword) || strlen($newPassword) < 8) {
                $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
                return $errors;
            }
            
            if ($newPassword !== $confirmPassword) {
                $errors[] = "Les mots de passe ne correspondent pas.";
                return $errors;
            }
            
            try {
                $rootPath = dirname(dirname(dirname(__FILE__)));
                require_once $rootPath . '/app/config/config.php';
                
                $pdo = dbconnect();
                
                // Rechercher le token dans les 3 tables
                $user = null;
                $tableName = null;
                $idColumn = null;
                
                // Chercher dans Electeur
                $stmt = $pdo->prepare("
                    SELECT id_electeur as id, nom, prenom, email, resetTokenExpiration 
                    FROM electeur 
                    WHERE resetToken = ?
                ");
                $stmt->execute([$token]);
                $user = $stmt->fetch();
                if ($user) {
                    $tableName = 'electeur';
                    $idColumn = 'id_electeur';
                }
                
                // Chercher dans Candidat
                if (!$user) {
                    $stmt = $pdo->prepare("
                        SELECT id_candidat as id, nom, prenom, email, resetTokenExpiration 
                        FROM candidat 
                        WHERE resetToken = ?
                    ");
                    $stmt->execute([$token]);
                    $user = $stmt->fetch();
                    if ($user) {
                        $tableName = 'candidat';
                        $idColumn = 'id_candidat';
                    }
                }
                
                // Chercher dans Administrateur
                if (!$user) {
                    $stmt = $pdo->prepare("
                        SELECT id_administrateur as id, nom, prenom, email, resetTokenExpiration 
                        FROM administrateur 
                        WHERE resetToken = ?
                    ");
                    $stmt->execute([$token]);
                    $user = $stmt->fetch();
                    if ($user) {
                        $tableName = 'administrateur';
                        $idColumn = 'id_administrateur';
                    }
                }
                
                if (!$user) {
                    $errors[] = "Lien de réinitialisation invalide ou expiré.";
                    return $errors;
                }
                
                // Vérifier l'expiration
                $now = new DateTime();
                $expiration = new DateTime($user['resetTokenExpiration']);
                
                if ($now > $expiration) {
                    $errors[] = "Ce lien de réinitialisation a expiré. Veuillez en demander un nouveau.";
                    return $errors;
                }
                
                // Hasher le nouveau mot de passe
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                // Mettre à jour le mot de passe et supprimer le token
                $stmt = $pdo->prepare("
                    UPDATE $tableName 
                    SET mot_de_passe = ?, resetToken = NULL, resetTokenExpiration = NULL 
                    WHERE $idColumn = ?
                ");
                $stmt->execute([$hashedPassword, $user['id']]);
                
                error_log("Mot de passe réinitialisé avec succès pour {$user['email']}");
                
                // Redirection avec message de succès
                session_start();
                $_SESSION['success'] = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
                
                header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
                exit;
                
            } catch (PDOException $e) {
                error_log("Erreur lors de la réinitialisation: " . $e->getMessage());
                $errors[] = "Une erreur est survenue. Veuillez réessayer.";
            }
        }
        
        return $errors;
    }
    
    /**
     * Vérifier la validité du token
     */
    public function verifyToken($token) {
        try {
            $rootPath = dirname(dirname(dirname(__FILE__)));
            require_once $rootPath . '/app/config/config.php';
            
            $pdo = dbconnect();
            
            // Chercher le token dans les 3 tables
            $tables = [
                'electeur' => 'id_electeur',
                'candidat' => 'id_candidat',
                'administrateur' => 'id_administrateur'
            ];
            
            foreach ($tables as $table => $idCol) {
                $stmt = $pdo->prepare("
                    SELECT resetTokenExpiration 
                    FROM $table 
                    WHERE resetToken = ?
                ");
                $stmt->execute([$token]);
                $result = $stmt->fetch();
                
                if ($result) {
                    // Vérifier l'expiration
                    $now = new DateTime();
                    $expiration = new DateTime($result['resetTokenExpiration']);
                    
                    return $now <= $expiration;
                }
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Erreur vérification token: " . $e->getMessage());
            return false;
        }
    }
}
?>
