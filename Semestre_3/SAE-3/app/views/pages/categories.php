<?php
session_start();
include __DIR__ . '/../layouts/header.php';
?>

<!-- Lier le CSS externe -->
<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/categories.css">

<div class="categories-container">
    <!-- En-tête de la page -->
    <section class="categories-header">
        <h1>Catégories</h1>
        <p>Explorez notre catalogue par type</p>
    </section>

    <!-- 3 Cartes principales avec design moderne -->
    <section class="category-cards">
        <!-- Carte Films -->
        <div class="card-item">
            <a href="/sae-3-festivote-tas-cesar/app/views/pages/films.php" class="card-link">
                <div class="card-image">
                    <div class="card-icon-large">
                        <img src="<?= ImageHelper::url('img/logo/logo-bobine.png') ?>" alt="Réalisateurs" width="200" height="200">
                    </div>
                </div>
                <div class="card-body">
                    <h2>Films</h2>
                    <p>Découvrez tous les films en compétition</p>
                    <div class="card-footer">
                        <button class="btn-explore">Voir plus</button>
                    </div>
                </div>
            </a>
        </div>

        <!-- Carte Acteurs -->
        <div class="card-item">
            <a href="/sae-3-festivote-tas-cesar/app/views/pages/acteurs.php" class="card-link">
                <div class="card-image">
                    <div class="card-icon-large">
                        <img src="<?= ImageHelper::url('img/logo/logo-acteur.png') ?>" alt="Réalisateurs" width="200" height="200">
                    </div>
                </div>
                <div class="card-body">
                    <h2>Acteurs</h2>
                    <p>Parcourez les acteurs</p>
                    <div class="card-footer">
                        <button class="btn-explore">Voir plus</button>
                    </div>
                </div>
            </a>
        </div>

        <!-- Carte Réalisateurs -->
        <div class="card-item">
            <a href="/sae-3-festivote-tas-cesar/app/views/pages/realisateurs.php" class="card-link">
                <div class="card-image">
                    <div class="card-icon-large">
                        <img src="<?= ImageHelper::url('img/logo/logo-cam.png') ?>" alt="Réalisateurs" width="200" height="200">
                    </div>
                </div>
                <div class="card-body">
                    <h2>Réalisateurs</h2>
                    <p>Explorez les réalisateurs</p>
                    <div class="card-footer">
                        <button class="btn-explore">Voir plus</button>
                    </div>
                </div>
            </a>
        </div>

    </section>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'candidat'): ?>
        <div class="proposition">
            <a href="/sae-3-festivote-tas-cesar/app/views/pages/mes-propositions.php" class="btn-proposition">Voir mes propositions !</a>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>