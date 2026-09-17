<?php
session_start();

require_once dirname(dirname(dirname(__FILE__))) . '/controllers/PasswordResetController.php';
require_once dirname(dirname(dirname(__FILE__))) .  '/helpers/CsrfHelper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CsrfHelper::validateToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token de sécurité invalide.";
        header('Location: forgot-password.php');
        exit;
    }
    
}

$controller = new PasswordResetController();
$errors = [];

// Récupérer le token depuis l'URL
$token = $_GET['token'] ?? '';

// Vérifier la validité du token
if (empty($token) || !$controller->verifyToken($token)) {
    $_SESSION['error'] = "Lien de réinitialisation invalide ou expiré.";
    header('Location: /sae-3-festivote-tas-cesar/app/views/auth/reset-password.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = $controller->resetPassword();
}

include dirname(__FILE__) . '/../layouts/header.php';

// Background aléatoire
require_once dirname(dirname(dirname(__FILE__))) . '/helpers/BackgroundHelper.php';
$backgroundHelper = new BackgroundHelper();
$backgroundImage = $backgroundHelper->getRandomBackground();
?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/auth.css">

<div class="container">
    <div class="register-form">
        <h2>Nouveau mot de passe</h2>
        
        <p style="text-align: center color">
            Choisissez un nouveau mot de passe sécurisé pour votre compte.
        </p>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="password-requirements">
            <strong>📋 Exigences du mot de passe :</strong>
            <ul>
                <li>Au moins 8 caractères</li>
                <li>Utilisez un mot de passe unique</li>
                <li>Évitez les informations personnelles évidentes</li>
            </ul>
        </div>
        
        <form method="POST" action="">
            <?= CsrfHelper::generateField() ?>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            
            <div class="form-group">
                <label for="new_password">🔒 Nouveau mot de passe</label>
                <input 
                    type="password" 
                    id="new_password" 
                    name="new_password" 
                    placeholder="Entrez votre nouveau mot de passe"
                    required
                    minlength="8"
                    autocomplete="new-password"
                >
            </div>
            
            <div class="form-group">
                <label for="confirm_password">🔒 Confirmer le mot de passe</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    placeholder="Confirmez votre nouveau mot de passe"
                    required
                    minlength="8"
                    autocomplete="new-password"
                >
            </div>
            
            <button type="submit" class="btn btn-primary">
                ✅ Réinitialiser mon mot de passe
            </button>
        </form>
        
        <div class="text-center">
            <a href="/sae-3-festivote-tas-cesar/app/views/auth/login.php">
                ← Retour à la connexion
            </a>
        </div>
    </div>
</div>

<script>
// Validation en temps réel - Les mots de passe doivent correspondre
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (newPassword && confirmPassword && newPassword !== confirmPassword) {
        this.setCustomValidity('Les mots de passe ne correspondent pas');
    } else {
        this.setCustomValidity('');
    }
});

// Vérifier aussi quand on change le premier champ
document.getElementById('new_password').addEventListener('input', function() {
    const confirmPassword = document.getElementById('confirm_password');
    if (confirmPassword.value) {
        confirmPassword.dispatchEvent(new Event('input'));
    }
});
</script>

<?php include dirname(__FILE__) . '/../layouts/footer.php'; ?>
