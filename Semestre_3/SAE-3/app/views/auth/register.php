<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../helpers/BackgroundHelper.php';
require_once __DIR__ . '/../../helpers/CsrfHelper.php';

$csrfToken = CsrfHelper::generateToken();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    header('Location: /public/index.php');
    exit;
}

$authController = new AuthController();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CsrfHelper::validateToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token de sécurité invalide.";
        header('Location: register.php');
        exit;
    }
    $result = $authController->register();
    if (isset($result['errors'])) {
        $errors = $result['errors'];
    }
}

include __DIR__ . '/../layouts/header.php';
?>
<style>
    <?= BackgroundHelper::getBackgroundStyle(1) ?>
</style>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/auth.css">
<div class="container">
    <div class="register-form">
        <h2>Créer un compte CineVote</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="registerForm">
            <?= CsrfHelper::generateField() ?>
            <div class="form-group">
                <label for="role">Je m'inscris en tant que :</label>
                <select id="role" name="role" onchange="toggleFields()">
                    <option value="electeur" <?= ($_POST['role'] ?? 'electeur') === 'electeur' ? 'selected' : '' ?>>Électeur</option>
                    <option value="candidat" <?= ($_POST['role'] ?? '') === 'candidat' ? 'selected' : '' ?>>Candidat</option>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
                </div>
            </div>
            
            <div class="form-group" id="pseudoField">
                <label for="pseudo">Pseudo * <small>(visible publiquement)</small></label>
                <input type="text" id="pseudo" name="pseudo" value="<?= htmlspecialchars($_POST['pseudo'] ?? '') ?>" required minlength="3" maxlength="20">
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            
            <div class="form-group" id="dateNaissanceField">
                <label for="date_naissance">Date de naissance *</label>
                <input type="date" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($_POST['date_naissance'] ?? '') ?>" max="<?= date('Y-m-d') ?>">
            </div>
            
                    <!-- Exigences CNIL -->
                <div class="password-requirements">
                    <ul id="password-errors">
                        <li>Minimum 12 caractères</li>
                        <li>Au moins une majuscule (A-Z)</li>
                        <li>Au moins une minuscule (a-z)</li>
                        <li>Au moins un chiffre (0-9)</li>
                        <li>Au moins un caractère spécial (!@#$%...)</li>
                    </ul>
                </div>

            <div class="form-row">
        
                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe *</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                </div>
                
                <div class="form-group">
                    <label for="mot_de_passe_confirm">Confirmer le mot de passe *</label>
                    <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" required>
                </div>
            </div>

            <!-- checkbox pour validation des conditions -->
            <div class="form-group checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" id="accepte_conditions" name="accepte_conditions" value="1" required>
                    <span>
                        J'accepte les 
                        <a href="/sae-3-festivote-tas-cesar/app/views/pages/mentions-legales.php" target="_blank">Mentions Légales</a> 
                        les 
                        <a href="/sae-3-festivote-tas-cesar/app/views/pages/cgu.php" target="_blank">Conditions Générales d'Utilisation</a> 
                        et la 
                        <a href="/sae-3-festivote-tas-cesar/app/views/pages/confidentialite.php" target="_blank">Politique de Confidentialité</a> *
                    </span>
                </label>
            </div>
                        
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>
        
        <p class="text-center">
            Déjà un compte ? <a href="login.php">Se connecter</a>
        </p>
    </div>
</div>

<script>
function toggleFields() {
    const role = document.getElementById('role').value;
    const pseudoField = document.getElementById('pseudoField');
    const dateNaissanceField = document.getElementById('dateNaissanceField');
    const pseudo = document.getElementById('pseudo');
    const dateNaissance = document.getElementById('date_naissance');
    
    if (role === 'electeur') {
        pseudoField.style.display = 'block';
        dateNaissanceField.style.display = 'block';
        pseudo.required = true;
        dateNaissance.required = true;
    } else {
        dateNaissanceField.style.display = 'none';
        dateNaissance.required = false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleFields();
});
</script>

<script>
// Activer/désactiver le bouton selon la case à cocher
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('accepte_conditions');
    const submitBtn = document.getElementById('submitBtn');
    
    checkbox.addEventListener('change', function() {
        submitBtn.disabled = !this.checked;
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
