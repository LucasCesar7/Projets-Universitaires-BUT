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
$categories = [];
$acteurs = [];
$realisateurs = [];

try {
    $pdo = dbconnect();
    
    $stmt = $pdo->query("SELECT id_Categorie, libelle FROM categorie ORDER BY libelle ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query("SELECT id_acteur,statut_validation, CONCAT(prenom, ' ', nom) as nom_complet FROM acteur ORDER BY nom, prenom");
    $acteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query("SELECT id_realisateur,statut_validation, CONCAT(prenom, ' ', nom) as nom_complet FROM realisateur ORDER BY nom, prenom");
    $realisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $errors[] = "Erreur: " . $e->getMessage();
}

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

<div class="add-form-container add-film">
    <div class="add-form-header">
        <div class="emoji"><img src="<?= ImageHelper::url('img/logo/logo-bobine.png') ?>" width="60" alt="logo-film" class="img-container"></div>
        <h1>Ajouter un film</h1>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">
            ✅ Film ajouté avec succès ! Redirection...
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p>❌ <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="/sae-3-festivote-tas-cesar/app/controllers/FilmController.php?action=add"
      method="POST" enctype="multipart/form-data">
        <?= CsrfHelper::generateField() ?>

        <div class="form-group">
            <label>Titre <span class="required">*</span></label>
            <input type="text" name="titre" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Description <span class="required">*</span></label>
            <textarea name="description" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Année <span class="required">*</span></label>
            <input type="number" name="annee" value="<?= htmlspecialchars($_POST['annee'] ?? '') ?>" min="1900" max="2100" required>
        </div>

        <div class="form-group">
            <label>Durée (minutes) <span class="required">*</span></label>
            <input type="number" name="duree" value="<?= htmlspecialchars($_POST['duree'] ?? '') ?>" min="1" required>
        </div>

        <div class="form-group">
            <label>Genres <span class="required">*</span></label>
            <div class="checkbox-group">
                <?php foreach ($categories as $cat): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="categories[]" value="<?= $cat['id_Categorie'] ?>">
                        <span><?= htmlspecialchars($cat['libelle']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <div class="section-header">
                <label>Acteurs</label>
                <a href="/sae-3-festivote-tas-cesar/app/views/admin/add-acteur.php" class="btn-add-inline" target="_blank">➕ Ajouter un acteur</a>
            </div>
            <div class="checkbox-group scrollable">
                <?php if (empty($acteurs)): ?>
                    <p class="no-items">Aucun acteur disponible. <a href="/sae-3-festivote-tas-cesar/app/views/admin/add-acteur.php" target="_blank">Ajoutez-en un</a></p>
                <?php else: ?>
                    <?php foreach ($acteurs as $acteur): ?>
                        <?php if($acteur['statut_validation']!== 'VALIDE') continue; ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="acteurs[]" value="<?= $acteur['id_acteur'] ?>">
                            <span><?= htmlspecialchars($acteur['nom_complet']) ?></span>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <div class="section-header">
                <label>Réalisateurs</label>
                <a href="/sae-3-festivote-tas-cesar/app/views/admin/add-realisateur.php" class="btn-add-inline" target="_blank">➕ Ajouter un réalisateur</a>
            </div>
            <div class="checkbox-group scrollable">
                <?php if (empty($realisateurs)): ?>
                    <p class="no-items">Aucun réalisateur disponible. <a href="/sae-3-festivote-tas-cesar/app/views/admin/add-realisateur.php" target="_blank">Ajoutez-en un</a></p>
                <?php else: ?>
                    <?php foreach ($realisateurs as $real): ?>
                        <?php if($real['statut_validation']!== 'VALIDE') continue; ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="realisateurs[]" value="<?= $real['id_realisateur'] ?>">
                            <span><?= htmlspecialchars($real['nom_complet']) ?></span>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Affiche du film</label>
            <input type="file" name="affiche" accept="image/*">
            <span class="form-info">Formats acceptés: JPG, PNG, GIF, WebP (max 5MB)</span>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">➕ Ajouter le film</button>
            <a href="/sae-3-festivote-tas-cesar/app/views/pages/films.php" class="btn-cancel">❌ Annuler</a>
        </div>
    </form>
</div>

<?php include dirname(dirname(__FILE__)) . '/layouts/footer.php'; ?>
