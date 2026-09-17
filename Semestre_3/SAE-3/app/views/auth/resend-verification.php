<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../helpers/CsrfHelper.php';
require_once __DIR__ . '/../../config/config.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Inclure et exécuter le contrôleur
    require_once '../../app/controllers/ResendVerificationController.php';
}

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);


// Nettoyer après affichage
unset($_SESSION['success'], $_SESSION['error']);

include __DIR__ . '/../layouts/header.php';


?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/auth.css">

<div class="container">
    <div class="register-form">
    <h1>📧 Renvoyer l'email de vérification</h1>
    <p>Entrez votre adresse email pour recevoir un nouveau lien de vérification.</p>
    
    <!-- Messages de succès -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    
    <!-- Messages d'erreur -->
    <?php if ($error): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <!-- Formulaire -->
    <form action="../../controllers/ResendVerificationController.php" method="POST">
        <?= CsrfHelper::generateField() ?>
        <div class="form-group">
            <label for="email">Adresse email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                placeholder="exemple@email.com"
                required
            >
        </div>
        
        <button type="submit" class="btn btn-primary">
            📧 Renvoyer l'email de vérification
        </button>
    </form>
    
    <p class="text-center">
        <a href="login.php">← Retour à la connexion</a>
    </p>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
