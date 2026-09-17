<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../controllers/ProfileController.php';
require_once __DIR__ . '/../../helpers/BackgroundHelper.php';
require_once __DIR__ . '/../../helpers/CsrfHelper.php';

// Vérifier si connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
    exit;
}



$controller = new ProfileController();
$data = $controller->showProfile();
$user = $data['user'];
$success = $data['success'];
$errors = $_SESSION['errors'] ?? [];
$error = $_SESSION['error'] ?? null;


unset($_SESSION['success'], $_SESSION['errors']);

include __DIR__ . '/../layouts/header.php';
?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/profil.css">

<style>
    <?= BackgroundHelper::getBackgroundStyle(1) ?>
</style>

<div class="container">
    <div class="profile-container">
        <h1>👤 Mon Profil</h1>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <!-- Informations du compte -->
        <div class="profile-section">
            <h2>Informations personnelles</h2>
            
            <form action="/sae-3-festivote-tas-cesar/app/controllers/ProfileController.php" method="POST">
                <?= CsrfHelper::generateField() ?>
                <input type="hidden" name="action" value="update_profile">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">Nom *</label>
                        <input 
                            type="text" 
                            id="nom" 
                            name="nom" 
                            value="<?= htmlspecialchars($user['nom']) ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="prenom">Prénom *</label>
                        <input 
                            type="text" 
                            id="prenom" 
                            name="prenom" 
                            value="<?= htmlspecialchars($user['prenom']) ?>"
                            required
                        >
                    </div>
                </div>
                
                <?php if ($_SESSION['role'] !== 'admin'): ?>
                <div class="form-group">
                    <label for="pseudo">Pseudo *</label>
                    <input 
                        type="text" 
                        id="pseudo" 
                        name="pseudo" 
                        value="<?= htmlspecialchars($user['pseudo'] ?? '') ?>"
                        required
                    >
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        value="<?= htmlspecialchars($user['email']) ?>"
                        disabled
                    >
                    <small>L'email ne peut pas être modifié</small>
                </div>
                
                <?php if ($_SESSION['role'] === 'electeur' && isset($user['date_naissance'])): ?>
                <div class="form-group">
                    <label for="date_naissance">Date de naissance</label>
                    <input 
                        type="date" 
                        id="date_naissance" 
                        name="date_naissance" 
                        value="<?= htmlspecialchars($user['date_naissance'] ?? '') ?>"
                    >
                </div>
                <?php endif; ?>
                
                <button type="submit" class="btn btn-primary">
                    💾 Enregistrer les modifications
                </button>
            </form>
        </div>
        
        <!-- Changer le mot de passe -->
        <div class="profile-section">
            <h2>Changer le mot de passe</h2>
            
            <form action="/sae-3-festivote-tas-cesar/app/controllers/ProfileController.php" method="POST">
                <?= CsrfHelper::generateField() ?>
                <input type="hidden" name="action" value="change_password">
                
                <div class="form-group">
                    <label for="current_password">Mot de passe actuel *</label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="new_password">Nouveau mot de passe *</label>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        minlength="12"
                        required
                    >
                    <small>Au moins 12 caractères</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmer le nouveau mot de passe *</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        minlength="8"
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-secondary">
                    🔑 Changer le mot de passe
                </button>
            </form>
        </div>
        
        <!-- Informations du compte -->
        <div class="profile-section">
            <h2>Informations du compte</h2>
            <p><strong>Rôle :</strong> <?= ucfirst($_SESSION['role']) ?></p>
            <p><strong>Email vérifié :</strong> 
                <?php if ($user['email_verifie']): ?>
                    <span class="badge badge-success">✅ Vérifié</span>
                <?php else: ?>
                    <span class="badge badge-warning">⚠️ Non vérifié</span>
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
