<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    try {
        $pdo = dbconnect();
        
        // Chercher le token dans Electeur
        $stmt = $pdo->prepare("
            SELECT id_electeur as id, email, prenom, token_expiration, email_verifie 
            FROM electeur 
            WHERE token = ?
        ");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $role = 'electeur';
        
        // Chercher dans Candidat
        if (!$user) {
            $stmt = $pdo->prepare("
                SELECT id_candidat as id, email, prenom, token_expiration, email_verifie 
                FROM candidat 
                WHERE token = ?
            ");
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $role = 'candidat';
        }
        
        // Chercher dans Administrateur
        if (!$user) {
            $stmt = $pdo->prepare("
                SELECT id_administrateur as id, email, prenom, token_expiration, email_verifie 
                FROM administrateur 
                WHERE token = ?
            ");
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $role = 'admin';
        }
        
        // Token invalide
        if (!$user) {
            $_SESSION['error'] = "Token invalide ou expiré.";
            header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
            exit;
        }
        
        // Email déjà vérifié
        if ($user['email_verifie'] == 1) {
            $_SESSION['success'] = "Votre email est déjà vérifié. Vous pouvez vous connecter.";
            header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
            exit;
        }
        
        // Vérifier l'expiration
        $now = new DateTime();
        $expiration = new DateTime($user['token_expiration']);
        
        if ($now > $expiration) {
            $_SESSION['error'] = "Ce lien a expiré. Veuillez demander un nouveau lien de vérification.";
            header('Location: /sae-3-festivote-tas-cesar/app/views/auth/resend-verification.php');
            exit;
        }
        
        // Marquer l'email comme vérifié
        if ($role === 'electeur') {
            $stmt = $pdo->prepare("UPDATE electeur SET email_verifie = 1, token = NULL WHERE id_electeur = ?");
        } elseif ($role === 'candidat') {
            $stmt = $pdo->prepare("UPDATE candidat SET email_verifie = 1, token = NULL WHERE id_candidat = ?");
        } elseif ($role === 'admin') {
            $stmt = $pdo->prepare("UPDATE administrateur SET email_verifie = 1, token = NULL WHERE id_administrateur = ?");
        }
        
        $stmt->execute([$user['id']]);
        
        $_SESSION['success'] = "✅ Email vérifié avec succès ! Vous pouvez maintenant vous connecter.";
        header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
        exit;
        
    } catch (PDOException $e) {
        error_log("Erreur vérification email: " . $e->getMessage());
        $_SESSION['error'] = "Une erreur s'est produite.";
        header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
        exit;
    }
} else {
    $_SESSION['error'] = "Token manquant.";
    header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
    exit;
}
