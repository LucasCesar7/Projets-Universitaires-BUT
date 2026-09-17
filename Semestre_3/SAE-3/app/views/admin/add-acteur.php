<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'candidat')) {
    header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
    exit();
}



$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootPath . '/app/config/config.php';
require_once $rootPath . '/app/helpers/CsrfHelper.php';

$errors = [];
$success = false;

include dirname(dirname(__FILE__)) . '/layouts/header.php';



if ($isInPeriodeAjout == false) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: /sae-3-festivote-tas-cesar/public/index.php');
        exit();
    }
} else {
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'candidat')) {
        header('Location: /sae-3-festivote-tas-cesar/public/index.php');
        exit();
    }
}
?>


<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/add-form.css">

<div class="add-form-container add-acteur">
    <div class="add-form-header">
        <div class="emoji"><img src="<?= ImageHelper::url('img/logo/logo-acteur.png') ?>" width="60" alt="logo-film" class="img-container"></div>
        <h1>Ajouter un acteur</h1>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">
            ✅ Acteur ajouté avec succès ! Redirection...
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p>❌ <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/sae-3-festivote-tas-cesar/app/controllers/ActeurController.php?action=add" enctype="multipart/form-data">
        <?= CsrfHelper::generateField() ?>
        <div class="form-group">
            <label>Prénom <span class="required">*</span></label>
            <input type="text" name="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" placeholder="Ex: Quentin" required>
        </div>

        <div class="form-group">
            <label>Nom <span class="required">*</span></label>
            <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" placeholder="Ex: Tarantino" required>
        </div>

        <div class="form-group">
            <label>Nationalité <span class="required">*</span></label>
            <input type="text" name="nationalite" value="<?= htmlspecialchars($_POST['nationalite'] ?? '') ?>" placeholder="Ex: Américain" required>
        </div>

        <div class="form-group">
            <label>Date de naissance</label>
            <input type="date" name="date_naissance" value="<?= htmlspecialchars($_POST['date_naissance'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Photo de l'acteur</label>
            <input type="file" name="photo" accept="image/*">
            <span class="form-info">Formats acceptés: JPG, PNG, GIF, WebP (max 5MB)</span>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">➕ Ajouter l'acteur</button>
            <a href="/sae-3-festivote-tas-cesar/app/views/pages/acteurs.php" class="btn-cancel">❌ Annuler</a>
        </div>
    </form>
</div>

<?php include dirname(dirname(__FILE__)) . '/layouts/footer.php'; ?>
