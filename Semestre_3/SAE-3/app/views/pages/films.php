<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootPath . '/app/config/config.php';
require_once $rootPath . '/app/models/User.php';
require_once $rootPath . '/app/models/Film.php';
require_once $rootPath . '/app/controllers/DeleteController.php';
require_once $rootPath . '/app/helpers/CsrfHelper.php';

$pdo = dbconnect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_film') {
    if (!CsrfHelper::validateToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token de sécurité invalide.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    $deleteController = new DeleteController($pdo);
    $deleteController->deleteFilm();
}

$isAdmin = false;
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $user = new User($pdo);
    $userData = $user->getUserInfo($_SESSION['user_id'], $_SESSION['role']);
    $isAdmin = ($userData && $_SESSION['role'] === 'admin');
}

// Récupération des paramètres de filtres
$search = $_GET['search'] ?? '';
$categorie_id = $_GET['categorie'] ?? null;

try {
    $filmModel = new Film($pdo);
    
    // Utilisation de la méthode avec filtres
    $films = $filmModel->getFilmsWithFilters($search, $categorie_id);
    $categories = $filmModel->getAllCategories();
    
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

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/films.css">
<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/vote-modal.css">

<div class="categories-container">
    <div class="categories-header">
        <h1><img src="<?= ImageHelper::url('img/logo/logo-bobine.png') ?>" width="60" alt="logo-film" class="img-container"> Les Films</h1>
        <p><a href="/sae-3-festivote-tas-cesar/app/views/pages/categories.php">← Retour aux catégories</a></p>

        <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'candidat')):
            if ($isInPeriodeAjout || $_SESSION['role'] === 'admin'): ?>
            <a href="/sae-3-festivote-tas-cesar/app/views/admin/add-film.php" class="btn-add">➕ Ajouter un film</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- SECTION FILTRES -->
    <div class="filters-section">
        <form method="GET" action="" class="filters-form">
            <div class="filter-group">
                <label for="search">Rechercher un film</label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    placeholder="Titre du film..." 
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
                <button type="submit" class="btn btn-search">Filtrer</button>
                <a href="?search=&categorie=" class="btn btn-reset">Réinitialiser</a>
            </div>
        </form>
    </div>

    <!-- Affichage du nombre de résultats -->
    <?php if (!empty($search) || !empty($categorie_id)): ?>
        <div class="filter-info">
            <p>
                <?= count($films) ?> résultat(s) trouvé(s)
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

    <?php if (empty($films)): ?>
        <div class="no-items">
            <p>😢 Aucun film disponible.</p>
        </div>
    <?php else: ?>
        <div class="films-grid">
            <?php foreach ($films as $film): ?>
                <?php if($film['statut_validation'] !== 'VALIDE') continue; ?>
                <div class="film-card">
                    <a href="/sae-3-festivote-tas-cesar/app/views/public/detail-film.php?id=<?= $film['id_film'] ?>">
                        <div class="film-poster">
                            <?php if (!empty($film['url_affiche'])): ?>
                                <img src="<?= ImageHelper::url(htmlspecialchars($film['url_affiche'])) ?>" alt="<?= htmlspecialchars($film['titre']) ?>">
                            <?php else: ?>
                                <div class="no-poster">🎬</div>
                            <?php endif; ?>
                        </div>
                        <div class="film-info">
                            <h3><?= htmlspecialchars($film['titre']) ?></h3>
                            <p class="film-year"><?= htmlspecialchars($film['annee'] ?? 'Année inconnue') ?></p>
                            <p class="film-genres"><?= htmlspecialchars($film['duree'] ?? 'N/A') ?> min</p>
                        </div>
                    </a>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'electeur' && $isInPeriodeVote): ?>
                        <button class="btn-vote"
                            data-type="film"
                            data-id="<?= $film['id_film'] ?>"
                            data-name="<?= htmlspecialchars($film['titre']) ?>">
                            Voter
                        </button>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <button type="button" class="btn-cancel" onclick="openDeleteModal('film', <?= $film['id_film'] ?>, '<?= htmlspecialchars(addslashes($film['titre'])) ?>')">
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
            <input type="hidden" name="action" value="delete_film">
            <input type="hidden" name="id" id="deleteId">
            <div class="modal-actions">
                <button type="button" class="btn" onclick="closeDeleteModal()">Annuler</button>
                <button type="submit" class="btn-cancel">Supprimer</button>
            </div>
        </form>
    </div>
</div>

<script src="/sae-3-festivote-tas-cesar/public/js/vote.js"></script>
<script src="/sae-3-festivote-tas-cesar/public/js/delete-modal.js"></script>

<?php include dirname(dirname(__FILE__)) . '/layouts/footer.php'; ?>