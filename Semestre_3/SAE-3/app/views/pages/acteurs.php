<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootPath . '/app/config/config.php';
require_once $rootPath . '/app/models/User.php';
require_once $rootPath . '/app/models/Acteur.php';
require_once $rootPath . '/app/controllers/DeleteController.php';
require_once $rootPath . '/app/helpers/CsrfHelper.php';

$pdo = dbconnect();

// Gestion de la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_acteur') {
    $deleteController = new DeleteController($pdo);
    $deleteController->deleteActeur();
}

// Vérification admin
$isAdmin = false;
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $user = new User($pdo);
    $userData = $user->getUserInfo($_SESSION['user_id'], $_SESSION['role']);
    $isAdmin = ($userData && $_SESSION['role'] === 'admin');
}

$search = $_GET['search'] ?? '';
$categorie_id = $_GET['categorie'] ?? null;

if ($categorie_id === '' || $categorie_id === '0') {
    $categorie_id = null;
}

$acteurs = [];
$categories = [];
$error = null;

try {
    $acteurModel = new Acteur($pdo);
    
    $acteurs = $acteurModel->getActeursWithFilters($search, $categorie_id);
    $categories = $acteurModel->getAllCategories();
    
} catch (PDOException $e) {
    $error = "Erreur: " . $e->getMessage();
}

include dirname(dirname(__FILE__)) . '/layouts/header.php';
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/acteurs.css">
<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/vote-modal.css">

<div class="categories-container">
    <div class="categories-header">
        <h1><img src="<?= ImageHelper::url('img/logo/logo-acteur.png') ?>" alt="logo-acteur" width="60" class="img-container"> Les Acteurs</h1>
        <p><a href="/sae-3-festivote-tas-cesar/app/views/pages/categories.php">← Retour aux catégories</a></p>

        <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'candidat')):
            if ($isInPeriodeAjout || $_SESSION['role'] === 'admin'): ?>
            <a href="/sae-3-festivote-tas-cesar/app/views/admin/add-acteur.php" class="btn-add">➕ Ajouter un acteur</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="filters-section">
        <form method="GET" action="" class="filters-form">
            <div class="filter-group">
                <label for="search">Rechercher un acteur</label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    placeholder="Nom ou prénom..." 
                    value="<?= htmlspecialchars($search) ?>"
                >
            </div>

            <div class="filter-group">
                <label for="categorie">Catégorie</label>
                <select id="categorie" name="categorie">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id_categorie'] ?>" 
                                <?= ($categorie_id == $cat['id_categorie']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="filter-actions">
                <button type="submit" class="btn-search">Filtrer</button>
                <a href="?search=&categorie=" class="btn-reset">Réinitialiser</a>
            </div>
        </form>
    </div>

    <!-- Affichage du nombre de résultats -->
    <?php if (!empty($search) || !empty($categorie_id)): ?>
        <div class="filter-info">
            <p>
                <?= count($acteurs) ?> résultat(s) trouvé(s)
                <?php if ($search): ?>
                    pour "<strong><?= htmlspecialchars($search) ?></strong>"
                <?php endif; ?>
                <?php if ($categorie_id): ?>
                    <?php 
                    $selectedCat = array_filter($categories, fn($c) => $c['id_categorie'] == $categorie_id);
                    $selectedCat = reset($selectedCat);
                    ?>
                    dans la catégorie "<strong><?= htmlspecialchars($selectedCat['libelle']) ?></strong>"
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($acteurs)): ?>
        <div class="no-items">
            <p>😢 Aucun acteur disponible.</p>
        </div>
    <?php else: ?>
        <div class="films-grid">
            <?php foreach ($acteurs as $acteur): ?>
                <?php if ($acteur['statut_validation'] !== 'VALIDE') continue; ?>
                <div class="film-card">
                    <a href="/sae-3-festivote-tas-cesar/app/views/public/detail-acteur.php?id=<?= $acteur['id_acteur'] ?>">
                        <div class="film-poster">
                            <?php if (!empty($acteur['url_photo'])): ?>
                                <img src="<?= ImageHelper::url(htmlspecialchars($acteur['url_photo'])) ?>" alt="<?= htmlspecialchars($acteur['prenom'] . ' ' . $acteur['nom']) ?>">
                            <?php else: ?>
                                <div class="no-poster">👤</div>
                            <?php endif; ?>
                        </div>
                        <div class="film-info">
                            <h3><?= htmlspecialchars($acteur['prenom'] . ' ' . $acteur['nom']) ?></h3>
                            <p class="film-year"><?= htmlspecialchars($acteur['nationalite'] ?? 'Nationalité inconnue') ?></p>
                            <p class="film-genres"><img src="<?= ImageHelper::url('img/logo/logo-bobine.png') ?>" alt="logo-film" width="40" class="img-container"> <?= intval($acteur['nb_films']) ?> film(s)</p>
                        </div>
                    </a>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'electeur' && $isInPeriodeVote): ?>
                        <button class="btn-vote"
                            data-type="acteur"
                            data-id="<?= $acteur['id_acteur'] ?>"
                            data-name="<?= htmlspecialchars($acteur['prenom'] . ' ' . $acteur['nom']) ?>">
                            Voter
                        </button>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <button type="button" class="btn-cancel" onclick="openDeleteModal('acteur', <?= $acteur['id_acteur'] ?>, '<?= htmlspecialchars(addslashes($acteur['prenom'] . ' ' . $acteur['nom'])) ?>')">
                            Supprimer
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div id="modal-vote-confirm" class="modal">
    <div class="modal-content">
        <span id="close-vote-modal">×</span>
        <h3>Confirmer votre vote</h3>
        <p id="modal-vote-question"></p>
        <p style="font-size: 18px; font-weight: bold; color: #333; margin-bottom: 15px;">
            Pour : <span id="modal-element-name" style="color: #007bff;"></span>
        </p>
        <form method="POST" action="/sae-3-festivote-tas-cesar/app/views/vote/voter.php">
            <?= CsrfHelper::generateField() ?>
            <input type="hidden" name="type" id="modal-type" value="">
            <input type="hidden" name="id" id="modal-id" value="">
            <label for="modal-categorie-select">Choisissez une catégorie :</label>
            <select name="categorie" id="modal-categorie-select" required></select>
            <button type="submit" name="confirm_vote">Confirmer</button>
        </form>
    </div>
</div>

<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeDeleteModal()">&times;</span>
        <h2>Confirmation de suppression</h2>
        <p id="deleteMessage"></p>
        <form id="deleteForm" method="POST">
            <?= CsrfHelper::generateField() ?>
            <input type="hidden" name="action" value="delete_acteur">
            <input type="hidden" name="id" id="deleteId">
            <div class="modal-actions">
                <button type="button" class="btn" onclick="closeDeleteModal()">Annuler</button>
                <button type="submit" class="btn-confirm">Supprimer</button>
            </div>
        </form>
    </div>
</div>


<script src="/sae-3-festivote-tas-cesar/public/js/vote.js"></script>
<script src="/sae-3-festivote-tas-cesar/public/js/delete-modal.js"></script>

<?php include dirname(dirname(__FILE__)) . '/layouts/footer.php'; ?>