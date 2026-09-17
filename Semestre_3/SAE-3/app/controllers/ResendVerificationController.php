<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/EmailHelper.php';
require_once __DIR__ . '/../helpers/CsrfHelper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CsrfHelper::validateToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token de sécurité invalide.";
        header('Location: ../views/auth/resend-verification.php');
        exit;
    }

    $email = trim($_POST['email'] ?? '');

    // Validation basique
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Veuillez entrer une adresse email valide.";
        header('Location: ../views/auth/resend-verification.php');
        exit;
    }

    try {
        $pdo = dbconnect();
        $user = null;
        $role = null;

        // ===== CHERCHER DANS ELECTEUR =====
        error_log("DEBUG: Recherche de l'email: " . $email);
        
        $stmt = $pdo->prepare("SELECT id_electeur as id, email, prenom, email_verifie, token FROM electeur WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $role = 'electeur';
            error_log("DEBUG: Utilisateur trouvé comme electeur");
        }

        // ===== CHERCHER DANS CANDIDAT =====
        if (!$user) {
            $stmt = $pdo->prepare("SELECT id_candidat as id, email, prenom, email_verifie, token FROM candidat WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $role = 'candidat';
                error_log("DEBUG: Utilisateur trouvé comme candidat");
            }
        }

        // ===== CHERCHER DANS ADMINISTRATEUR =====
        if (!$user) {
            $stmt = $pdo->prepare("SELECT id_administrateur as id, email, prenom, email_verifie, token FROM administrateur WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $role = 'admin';
                error_log("DEBUG: Utilisateur trouvé comme admin");
            }
        }

        // ===== UTILISATEUR INTROUVABLE =====
        if (!$user) {
            error_log("DEBUG: Aucun utilisateur trouvé");
            $_SESSION['error'] = "Aucun compte trouvé avec cet email.";
            header('Location: ../views/auth/resend-verification.php');
            exit;
        }

        // ===== DÉJÀ VÉRIFIÉ =====
        if ($user['email_verifie'] == 1) {
            error_log("DEBUG: Email déjà vérifié");
            $_SESSION['error'] = "Votre compte est déjà vérifié. Vous pouvez vous connecter.";
            header('Location: ../views/auth/login.php');
            exit;
        }

        // ===== GÉNÉRER UN NOUVEAU TOKEN =====
        error_log("DEBUG: Génération du token pour rôle: " . $role);
        $token = bin2hex(random_bytes(32));
        $expiration = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Mettre à jour selon le rôle
        if ($role === 'electeur') {
            $stmt = $pdo->prepare("UPDATE electeur SET token = ?, token_expiration = ? WHERE id_electeur = ?");
            $stmt->execute([$token, $expiration, $user['id']]);
        } elseif ($role === 'candidat') {
            $stmt = $pdo->prepare("UPDATE candidat SET token = ?, token_expiration = ? WHERE id_candidat = ?");
            $stmt->execute([$token, $expiration, $user['id']]);
        } elseif ($role === 'admin') {
            $stmt = $pdo->prepare("UPDATE administrateur SET token = ?, token_expiration = ? WHERE id_administrateur = ?");
            $stmt->execute([$token, $expiration, $user['id']]);
        }
        
        error_log("DEBUG: Token mis à jour en BDD");

        // ===== ENVOYER L'EMAIL =====
        error_log("DEBUG: Tentative d'envoi email");
        $emailHelper = new EmailHelper();
        $emailHelper->sendVerificationEmail($email, $token, $user['prenom'], $role);
        
        error_log("DEBUG: Email envoyé avec succès");
        $_SESSION['success'] = "Un nouvel email de vérification a été envoyé ! Vérifiez votre boîte de réception.";

        header('Location: ../views/auth/resend-verification.php');
        exit;

    } catch (PDOException $e) {
        error_log("ERREUR PDO: " . $e->getMessage());
        error_log("ERREUR PDO Trace: " . $e->getTraceAsString());
        $_SESSION['error'] = "Une erreur s'est produite. Réessayez plus tard.";
        header('Location: ../views/auth/resend-verification.php');
        exit;
    } catch (Exception $e) {
        error_log("ERREUR GENERALE: " . $e->getMessage());
        error_log("ERREUR GENERALE Trace: " . $e->getTraceAsString());
        $_SESSION['error'] = "Une erreur s'est produite lors de l'envoi de l'email.";
        header('Location: ../views/auth/resend-verification.php');
        exit;
    }

} else {
    header('Location: ../views/auth/resend-verification.php');
    exit;
}
?>
