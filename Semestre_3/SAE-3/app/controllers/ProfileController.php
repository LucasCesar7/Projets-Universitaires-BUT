<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/CsrfHelper.php';
require_once __DIR__ . '/../helpers/AuditLogger.php';

class ProfileController
{

    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Afficher le profil
     */
    public function showProfile()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
            exit;
        }

        $userInfo = $this->userModel->getUserInfo($_SESSION['user_id'], $_SESSION['role']);

        return [
            'user' => $userInfo,
            'errors' => [],
            'success' => $_SESSION['success'] ?? null
        ];
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile()
    {
        $errors = [];
        $audit = new AuditLogger();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
            exit;
        }

        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $pseudo = trim($_POST['pseudo'] ?? '');
        $date_naissance = $_POST['date_naissance'] ?? null;

        // Validation
        if (empty($nom)) $errors[] = "Le nom est requis.";
        if (empty($prenom)) $errors[] = "Le prénom est requis.";

        // Pseudo requis pour électeur et candidat
        if (($_SESSION['role'] === 'electeur' || $_SESSION['role'] === 'candidat') && empty($pseudo)) {
            $errors[] = "Le pseudo est requis.";
        }

        if (empty($errors)) {
            try {
                $this->userModel->updateProfile($_SESSION['user_id'], $_SESSION['role'], [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'pseudo' => $pseudo,
                    'date_naissance' => $date_naissance
                ]);

                // Mettre à jour la session
                $_SESSION['nom'] = $nom;
                $_SESSION['prenom'] = $prenom;
                if (!empty($pseudo)) {
                    $_SESSION['pseudo'] = $pseudo;
                }
                $audit->logUpdate($_SESSION['role'], $_SESSION['user_id'], "info_profile_update", null, null);
                $_SESSION['success'] = "✅ Profil mis à jour avec succès !";
                header('Location: /sae-3-festivote-tas-cesar/app/views/profile/profil.php');
                exit;
            } catch (PDOException $e) {
                $errors[] = "Erreur lors de la mise à jour : " . $e->getMessage();
            }
        }

        return ['errors' => $errors];
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword()
    {
        $errors = [];
        $audit = new AuditLogger();

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
            exit;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($currentPassword)) $errors[] = "Le mot de passe actuel est requis.";
        if (empty($newPassword)) $errors[] = "Le nouveau mot de passe est requis.";
        if (empty($confirmPassword)) $errors[] = "La confirmation du mot de passe est requise.";

        if ($newPassword !== $confirmPassword) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        if (strlen($newPassword) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }

        // Vérifier le mot de passe actuel
        if (empty($errors)) {
            if (!$this->userModel->verifyPassword($_SESSION['user_id'], $_SESSION['role'], $currentPassword)) {
                $errors[] = "Le mot de passe actuel est incorrect.";
            }
        }

        if (empty($errors)) {
            try {
                $this->userModel->updatePassword($_SESSION['user_id'], $_SESSION['role'], $newPassword);
                $audit->logUpdate($_SESSION['user_id'], "password_change", null, null);
                $_SESSION['success'] = "Mot de passe modifié avec succès !";
                header('Location: /sae-3-festivote-tas-cesar/app/views/profile/profil.php');
                exit;
            } catch (PDOException $e) {
                $errors[] = "Erreur lors du changement de mot de passe : " . $e->getMessage();
            }
        }

        return ['errors' => $errors];
    }
}

// ===== TRAITEMENT DES REQUÊTES POST UNIQUEMENT =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !CsrfHelper::validateToken($_POST['csrf_token'])) {
        $_SESSION['error'] = "Token de sécurité invalide.";
        header('Location: /sae-3-festivote-tas-cesar/app/views/profile/profil.php');
        exit;
    }
    $controller = new ProfileController();

    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_profile') {
            $result = $controller->updateProfile();
            if (!empty($result['errors'])) {
                $_SESSION['errors'] = $result['errors'];
                header('Location: /sae-3-festivote-tas-cesar/app/views/profile/profil.php');
                exit;
            }
        } elseif ($_POST['action'] === 'change_password') {
            $result = $controller->changePassword();
            if (!empty($result['errors'])) {
                $_SESSION['errors'] = $result['errors'];
                header('Location: /sae-3-festivote-tas-cesar/app/views/profile/profil.php');
                exit;
            }
        }
    }
}
