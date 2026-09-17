<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(dirname(dirname(__FILE__))) . '/controllers/PasswordResetController.php';
require_once dirname(dirname(dirname(__FILE__))) . '/helpers/CsrfHelper.php';

$controller = new PasswordResetController();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = $controller->requestReset();
}

// Récupérer le message de succès s'il existe
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

include dirname(__FILE__) . '/../layouts/header.php';

// Background aléatoire
require_once dirname(dirname(dirname(__FILE__))) . '/helpers/BackgroundHelper.php';
$backgroundHelper = new BackgroundHelper();
$backgroundImage = $backgroundHelper->getRandomBackground();
?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/auth.css">

<style>
body.auth-page::before {
    background-image: url('<?= $backgroundImage ?>');
}
</style>

<div class="container">
    <div class="login-form">
        <h2>Mot de passe oublié ?</h2>
        
        <p style="text-align: center">
            Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
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
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <?= CsrfHelper::generateField() ?>
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="votre@email.com"
                    required
                    autocomplete="email"
                >
            </div>
            
            <button type="submit" class="btn btn-primary">
                Envoyer le lien de réinitialisation
            </button>
        </form>
        
        <div class="text-center">
            <a href="/sae-3-festivote-tas-cesar/app/views/auth/login.php">
                ← Retour à la connexion
            </a>
        </div>
    </div>
</div>

<?php include dirname(__FILE__) . '/../layouts/footer.php'; ?>
