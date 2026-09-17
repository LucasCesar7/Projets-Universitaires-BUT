<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../helpers/CaptchaHelper.php';
require_once __DIR__ . '/../../helpers/BackgroundHelper.php';
require_once __DIR__ . '/../../helpers/CsrfHelper.php';

$csrfToken = CsrfHelper::generateToken();

// Si déjà connecté, rediriger
if (isset($_SESSION['user_id'])) {
    header('Location: /sae-3-festivote-tas-cesar/public/index.php');
    exit;
}

$captcha = new CaptchaHelper();
$authController = new AuthController();

// Récupérer les messages
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;

// Nettoyer après affichage
unset($_SESSION['success'], $_SESSION['error']);

$errors = [];

$email = $_POST['email'] ?? ($_GET['email'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !CsrfHelper::validateToken($_POST['csrf_token'])) {
        $_SESSION['error'] = "Token de sécurité invalide.";
        header('Location: login.php');
        exit;
    }
    $result = $authController->login();
    if (isset($result['errors'])) {
        $errors = $result['errors'];
    }
}

include __DIR__ . '/../layouts/header.php';
?>
<!--script reCAPTCHA de Google -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<style>
    <?= BackgroundHelper::getBackgroundStyle(1) ?>
</style>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/auth.css">

<div class="container">
    <div class="register-form">
        <h2>Connexion</h2>
        
          <!-- Messages de succès -->
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <!-- Messages d'erreur -->
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <!-- Messages d'erreur du controller -->
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <?= CsrfHelper::generateField() ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>

            <div class="form-group" style="margin: 20px 0;">
                <?= $captcha->renderCaptcha() ?>
            </div>
            
            <p class="text-center">
                ⚠️ <strong>CAPTCHA obligatoire</strong> 
            </p>
            
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>

        <div class="text-center" style="margin-top: 15px;">
            <a href="/sae-3-festivote-tas-cesar/app/views/auth/forgot-password.php" >
                🔑 Mot de passe oublié ?
            </a>
        </div>
        
        <p class="text-center">
            Pas encore de compte ? <a href="register.php">S'inscrire</a>
        </p>
        <p class="text-center">Email non vérifié ? <a href="resend-verification.php">Renvoyer l'email de vérification</a></p>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
