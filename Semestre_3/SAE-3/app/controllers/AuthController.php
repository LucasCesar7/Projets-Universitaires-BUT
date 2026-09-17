<?php
if (!isset($_SESSION)) {
    session_start();
}

$rootPath = dirname(dirname(dirname(__FILE__)));
require_once $rootPath . '/app/helpers/CsrfHelper.php';
require_once $rootPath . '/app/helpers/AuditLogger.php';
require_once $rootPath . '/app/config/config.php';

class AuthController {
    
    /**
     * Connexion utilisateur (Électeur, Candidat, Admin)
     */
    public function login() {
        $errors = [];
        $audit = new AuditLogger();
        if (!CsrfHelper::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Token de sécurité invalide.";
            header('Location: login.php');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $motDePasse = $_POST['mot_de_passe'] ?? '';
            $captchaResponse = $_POST['g-recaptcha-response'] ?? '';
            
            // Validation basique
            if (empty($email)) {
                $errors[] = "L'email est requis.";
            }
            if (empty($motDePasse)) {
                $errors[] = "Le mot de passe est requis.";
            }
            
            if (!empty($errors)) {
                return ['errors' => $errors];
            }

            require_once __DIR__ . '/../helpers/CaptchaHelper.php';
            $captcha = new CaptchaHelper();

            if (!$captcha->verifyCaptcha($captchaResponse)) {
                $errors[] = "Veuillez valider le CAPTCHA avant de vous connecter.";
                return ['errors' => $errors];
            }
        
                
            try {
                $pdo = dbconnect();
                
                // Utiliser la fonction SQL pour trouver l'utilisateur
                $stmt = $pdo->prepare("SELECT trouver_utilisateur_par_email(?) as user_data");
                $stmt->execute([$email]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$result['user_data']) {
                    $audit->log('LOGIN_FAILED', null, null, null, ['email' => $email], "Utilisateur non trouvé");
                    $errors[] = "Email ou mot de passe incorrect.";
                    return ['errors' => $errors];
                }

                // Décoder les données JSON retournées par la fonction
                $user = json_decode($result['user_data'], true);
                $role = $user['role'];

                // Vérification de l'email
                if ($role !== 'admin' && isset($user['email_verifie']) && $user['email_verifie'] == 0) {
                    $errors[] = "Vous devez vérifier votre email avant de vous connecter. Vérifiez votre boîte de réception.";
                    return ['errors' => $errors];
                }
                
                // Vérification du mot de passe
                if ($user && $user['mot_de_passe']) {
                    $motDePasseValide = password_verify($motDePasse, $user['mot_de_passe']);
                    
                    // Support mot de passe en clair (première connexion)
                    if (!$motDePasseValide && $user['mot_de_passe'] === $motDePasse) {
                        $motDePasseValide = true;
                        $hashedPassword = password_hash($motDePasse, PASSWORD_DEFAULT);
                        
                        if ($role === 'electeur') {
                            $stmt = $pdo->prepare("UPDATE electeur SET mot_de_passe = ? WHERE id_electeur = ?");
                            $stmt->execute([$hashedPassword, $user['id']]);
                        } elseif ($role === 'candidat') {
                            $stmt = $pdo->prepare("UPDATE candidat SET mot_de_passe = ? WHERE id_candidat = ?");
                            $stmt->execute([$hashedPassword, $user['id']]);
                        } elseif ($role === 'admin') {
                            $stmt = $pdo->prepare("UPDATE administrateur SET mot_de_passe = ? WHERE id_administrateur = ?");
                            $stmt->execute([$hashedPassword, $user['id']]);
                        }
                    }
                    
                    if ($motDePasseValide) {
                        // Vérification de l'email
                        if ($role !== 'admin' && isset($user['email_verifie']) && $user['email_verifie'] == 0) {
                            $errors[] = "Veuillez vérifier votre email avant de vous connecter. Consultez votre boîte de réception.";
                            return ['errors' => $errors];
                        }
                        
                        $audit->logLogin($user['id'], $role, true);

                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['nom'] = $user['nom'] ?? '';
                        $_SESSION['prenom'] = $user['prenom'] ?? '';
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['role'] = $role;
                        $_SESSION['pseudo'] = $user['pseudo'] ?? '';
                        $_SESSION['token'] = $user['token'] ?? '';
                        
                        if ($role === 'electeur') {
                            $_SESSION['id_electeur'] = $user['id'];
                        }
                        
                        // Redirection
                        if ($role === 'admin') {
                            $_SESSION['id_administrateur'] = $user['id'];
                            header('Location: /sae-3-festivote-tas-cesar/app/views/admin/dashboard.php');
                        } else {
                            header('Location: /sae-3-festivote-tas-cesar/public/index.php');
                        }
                        
                        exit;
                    } else {
                        $audit->log('LOGIN_FAILED', null, null, null, ['email' => $email], "Mot de passe incorrect");
                        $errors[] = "Email ou mot de passe incorrect.";
                    }
                } else {
                    $errors[] = "Email ou mot de passe incorrect.";
                }
                
            } catch (PDOException $e) {
                $errors[] = "Erreur base de données: " . $e->getMessage();
                error_log("Erreur login: " . $e->getMessage());
            }
        }
        
        return ['errors' => $errors];
    }
    
    /**
     * Inscription utilisateur
     */
    public function register() {
        if (!CsrfHelper::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Token de sécurité invalide.";
            header('Location: register.php');
            exit;
        }

        require_once __DIR__ . '/../models/User.php';
        $errors = [];
        $audit = new AuditLogger();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $motDePasse = $_POST['mot_de_passe'] ?? '';
            $motDePasseConfirm = $_POST['mot_de_passe_confirm'] ?? '';
            $role = $_POST['role'] ?? 'electeur';
            $pseudo = trim($_POST['pseudo'] ?? '');
            $dateNaissance = $_POST['date_naissance'] ?? '';
            $accepteConditions = isset($_POST['accepte_conditions']) && $_POST['accepte_conditions'] === '1';
            
            if (!$accepteConditions) {
                $errors[] = "Vous devez accepter les conditions d'utilisation.";
            }
            
            if (empty($nom)) $errors[] = "Le nom est requis.";
            if (empty($prenom)) $errors[] = "Le prénom est requis.";
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Un email valide est requis.";
            }
            
            //exigeance CNIL
            if (empty($motDePasse) || strlen($motDePasse) < 12) {
                $errors[] = "Le mot de passe doit contenir au moins 12 caractères.";
            }

            if ($motDePasse !== $motDePasseConfirm) {
                $errors[] = "Les mots de passe ne correspondent pas.";
            }

            $hasLower = preg_match('/[a-z]/', $motDePasse);
            $hasUpper = preg_match('/[A-Z]/', $motDePasse);
            $hasDigit = preg_match('/[0-9]/', $motDePasse);
            $hasSpecial = preg_match('/[^a-zA-Z0-9]/', $motDePasse);

            if (!$hasLower) {
                $errors[] = "Le mot de passe doit contenir au moins une lettre minuscule.";
            }
            if (!$hasUpper) {
                $errors[] = "Le mot de passe doit contenir au moins une lettre majuscule.";
            }
            if (!$hasDigit) {
                $errors[] = "Le mot de passe doit contenir au moins un chiffre.";
            }
            if (!$hasSpecial) {
                $errors[] = "Le mot de passe doit contenir au moins un caractère spécial.";
            }

            
            if (empty($pseudo)) {
                $errors[] = "Le pseudo est requis.";
            } elseif (strlen($pseudo) < 3) {
                $errors[] = "Le pseudo doit contenir au moins 3 caractères.";
            } elseif (strlen($pseudo) > 20) {
                $errors[] = "Le pseudo ne peut pas dépasser 20 caractères.";
            }
            
            if ($role == 'electeur') {
                if (empty($dateNaissance)) {
                    $errors[] = "La date de naissance est requise.";
                } else {
                    $birthDate = new DateTime($dateNaissance);
                    $today = new DateTime();
                    $age = $today->diff($birthDate)->y;
                    
                    if ($age < 13) {
                        $errors[] = "Vous devez avoir au moins 13 ans.";
                    }
                }
            }
            
            if (empty($errors)) {
                $userModel = new User();
                
                if ($userModel->emailExists($email)) {
                    $errors[] = "Cet email est déjà utilisé.";
                }
                
                if (!empty($pseudo) && $userModel->pseudoExists($pseudo)) {
                    $errors[] = "Ce pseudo est déjà utilisé.";
                }
                
                if (empty($errors)) {
                    try {
                        $hashedPassword = password_hash($motDePasse, PASSWORD_DEFAULT);
                        $success = false;
                        
                        if ($role === 'electeur') {
                            $success = $userModel->createElecteur($nom, $prenom, $email, $hashedPassword, $pseudo, $dateNaissance, $accepteConditions);
                            
                            if ($success) {
                                $idElecteur = $userModel->getLastInsertId();
                                $token = $userModel->getToken($idElecteur, 'electeur');
                                
                                require_once __DIR__ . '/../helpers/EmailHelper.php';
                                $emailHelper = new EmailHelper();
                                $emailHelper->sendVerificationEmail($email, $token, $prenom, 'electeur');
                                error_log("✅ Électeur créé avec succès #$idElecteur - Email envoyé");
                            }
                            
                        } elseif ($role === 'candidat') {
                            $success = $userModel->createCandidat($nom, $prenom, $email, $hashedPassword, $pseudo, $accepteConditions);
                            
                            if ($success) {
                                $idCandidat = $userModel->getLastInsertId();
                                $audit->logRegister($idElecteur, 'electeur', 'electeur', $email);
                                $token = $userModel->getToken($idCandidat, 'candidat');
                                
                                require_once __DIR__ . '/../helpers/EmailHelper.php';
                                $emailHelper = new EmailHelper();
                                $emailHelper->sendVerificationEmail($email, $token, $prenom, 'candidat');
                                error_log("✅ Candidat créé avec succès #$idCandidat - Email envoyé");
                            }
                        }
                        
                        if ($success) {
                            session_start();
                            $_SESSION['success'] = "✅ Compte créé avec succès ! Un email de vérification a été envoyé à $email. Veuillez vérifier votre boîte de réception pour activer votre compte.";
                            header('Location: /sae-3-festivote-tas-cesar/public/index.php');
                            exit;
                        } else {
                            $errors[] = "Erreur lors de l'inscription.";
                        }
                        
                    } catch (Exception $e) {
                        error_log("Exception in register: " . $e->getMessage());
                        $errors[] = "Erreur: " . $e->getMessage();
                    }
                }
            }
        }
        
        return ['errors' => $errors];
    }
    
    /**
     * Déconnexion
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION = array();
        
        $audit = new AuditLogger();
        
        //log deconnexion
        if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
            $audit->logLogout($_SESSION['user_id'], $_SESSION['role']);
        }

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        header('Location: /sae-3-festivote-tas-cesar/public/index.php');
        exit;
    }
}

?>
