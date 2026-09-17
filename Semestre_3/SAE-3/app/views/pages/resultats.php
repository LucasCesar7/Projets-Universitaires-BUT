<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier que les résultats sont disponibles
require_once dirname(dirname(dirname(__FILE__))) . '/models/Configuration.php';
$configModel = new Configuration();

if (!$configModel->areResultatsDisponibles()) {
    $_SESSION['error'] = "Les résultats ne sont pas encore disponibles. La période de vote doit être terminée.";
    header('Location: /sae-3-festivote-tas-cesar/public/index.php');
    exit;
}

include dirname(dirname(__FILE__)) . '/layouts/header.php';

// Récupérer les résultats
require_once dirname(dirname(dirname(__FILE__))) . '/models/ResultatModel.php';
$resultatModel = new ResultatModel();

// Résultats par catégorie
$resultatsFilms = $resultatModel->getResultatsFilms();
$resultatsActeurs = $resultatModel->getResultatsActeurs();
$resultatsRealisateurs = $resultatModel->getResultatsRealisateurs();

// Statistiques globales
$stats = $resultatModel->getStatistiques();
?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/resultats.css">

<div class="resultats-container">
    <div class="resultats-header">
        <h1>🏆 Résultats du Festival CineVote 2026</h1>
        <p class="subtitle">Les votes sont clos ! Découvrez les gagnants de chaque catégorie.</p>
    </div>
    
    <!-- Statistiques globales -->
    <div class="stats-section">
        <div class="stat-card">
            <div class="stat-icon"><img src="<?= ImageHelper::url('img/logo/logo-ticket.png') ?>" alt="img-vote" width="80" height="80"></div>
            <div class="stat-number"><?= number_format($stats['total_votes'] ?? 0) ?></div>
            <div class="stat-label">Votes totaux</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><img src="<?= ImageHelper::url('img/logo/logo-profile.png') ?>" alt="imm-electeur" width="80" height="80"></div>
            <div class="stat-number"><?= number_format($stats['total_electeurs'] ?? 0) ?></div>
            <div class="stat-label">Électeurs</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><img src="<?= ImageHelper::url('img/logo/logo-acteur.png') ?>" alt="img-categorie" width="80" height="80"></div>
            <div class="stat-number"><?= number_format($stats['total_categories'] ?? 0) ?></div>
            <div class="stat-label">Catégories</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><img src="<?= ImageHelper::url('img/logo/logo-etoile.png') ?>" alt="img-participation" width="80" height="80"></div>
            <div class="stat-number"><?= number_format($stats['taux_participation'] ?? 0, 1) ?>%</div>
            <div class="stat-label">Participation</div>
        </div>
    </div>
    
    <!-- Résultats Films -->
    <section class="resultats-section">
        <h2><img src="<?= ImageHelper::url('img/logo/logo-bobine.png') ?>" alt="img-participation" width="50" height="50"> Films</h2>
        <?php if (!empty($resultatsFilms)): ?>
            <?php foreach ($resultatsFilms as $categorie => $films): ?>
                <div class="categorie-block">
                    <h3 class="categorie-title"><?= htmlspecialchars($categorie) ?></h3>
                    <div class="podium">
                        <?php foreach (array_slice($films, 0, 3) as $index => $film): ?>
                            <div class="podium-item position-<?= $index + 1 ?>">
                                <div class="medal">
                                    <?php
                                    $medals = ['🥇', '🥈', '🥉'];
                                    echo $medals[$index] ?? '';
                                    ?>
                                </div>
                                <div class="item-info">
                                    <?php if (!empty($film['url_affiche'])): ?>
                                        <img src="<?=ImageHelper::url(htmlspecialchars($film['url_affiche'])) ?>" 
                                             alt="<?= htmlspecialchars($film['titre']) ?>" 
                                             class="item-image">
                                    <?php else: ?>
                                        <img src="<?= ImageHelper::url('img/default-film.png') ?>" 
                                             alt="Pas d'affiche" 
                                             class="item-image">
                                    <?php endif; ?>
                                    <h4><?= htmlspecialchars($film['titre']) ?></h4>
                                    <p class="item-year"><?= htmlspecialchars($film['annee']) ?></p>
                                    <div class="votes-bar">
                                        <div class="votes-progress" style="width: <?= min($film['pourcentage'] ?? 0, 100) ?>%;"></div>
                                    </div>
                                    <p class="votes-count">
                                        <?= number_format($film['nb_votes'] ?? 0) ?> votes (<?= number_format($film['pourcentage'] ?? 0, 1) ?>%)
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-results">Aucun résultat disponible pour les films.</p>
        <?php endif; ?>
    </section>
    
    <!-- Résultats Acteurs -->
    <section class="resultats-section">
        <h2><img src="<?= ImageHelper::url('img/logo/logo-acteur.png') ?>" alt="img-participation" width="50" height="50"> Acteurs</h2>
        <?php if (!empty($resultatsActeurs)): ?>
            <?php foreach ($resultatsActeurs as $categorie => $acteurs): ?>
                <div class="categorie-block">
                    <h3 class="categorie-title"><?= htmlspecialchars($categorie) ?></h3>
                    <div class="podium">
                        <?php foreach (array_slice($acteurs, 0, 3) as $index => $acteur): ?>
                            <div class="podium-item position-<?= $index + 1 ?>">
                                <div class="medal">
                                    <?php
                                    $medals = ['🥇', '🥈', '🥉'];
                                    echo $medals[$index] ?? '';
                                    ?>
                                </div>
                                <div class="item-info">
                                    <?php if (!empty($acteur['url_photo'])): ?>
                                        <img src="<?=ImageHelper::url(htmlspecialchars($acteur['url_photo'])) ?>" 
                                             alt="<?= htmlspecialchars($acteur['nom'] . ' ' . $acteur['prenom']) ?>" 
                                             class="item-image">
                                    <?php else: ?>
                                        <img src="<?= ImageHelper::url('img/default-actor.png') ?>" 
                                             alt="Pas de photo" 
                                             class="item-image">
                                    <?php endif; ?>
                                    <h4><?= htmlspecialchars($acteur['prenom'] . ' ' . $acteur['nom']) ?></h4>
                                    <p class="item-year"><?= htmlspecialchars($acteur['nationalite'] ?? 'Non renseigné') ?></p>
                                    <div class="votes-bar">
                                        <div class="votes-progress" style="width: <?= min($acteur['pourcentage'] ?? 0, 100) ?>%;"></div>
                                    </div>
                                    <p class="votes-count">
                                        <?= number_format($acteur['nb_votes'] ?? 0) ?> votes (<?= number_format($acteur['pourcentage'] ?? 0, 1) ?>%)
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-results">Aucun résultat disponible pour les acteurs.</p>
        <?php endif; ?>
    </section>
    
    <!-- Résultats Réalisateurs -->
    <section class="resultats-section">
        <h2><img src="<?= ImageHelper::url('img/logo/logo-cam.png') ?>" alt="img-participation" width="50" height="50"> Réalisateurs</h2>
        <?php if (!empty($resultatsRealisateurs)): ?>
            <?php foreach ($resultatsRealisateurs as $categorie => $realisateurs): ?>
                <div class="categorie-block">
                    <h3 class="categorie-title"><?= htmlspecialchars($categorie) ?></h3>
                    <div class="podium">
                        <?php foreach (array_slice($realisateurs, 0, 3) as $index => $realisateur): ?>
                            <div class="podium-item position-<?= $index + 1 ?>">
                                <div class="medal">
                                    <?php
                                    $medals = ['🥇', '🥈', '🥉'];
                                    echo $medals[$index] ?? '';
                                    ?>
                                </div>
                                <div class="item-info">
                                    <?php if (!empty($realisateur['url_photo'])): ?>
                                        <img src="<?= ImageHelper::url(htmlspecialchars($realisateur['url_photo'])) ?>" 
                                             alt="<?= htmlspecialchars($realisateur['nom'] . ' ' . $realisateur['prenom']) ?>" 
                                             class="item-image">
                                    <?php else: ?>
                                        <img src="<?= ImageHelper::url('img/default-director.png') ?>" 
                                             alt="Pas de photo" 
                                             class="item-image">
                                    <?php endif; ?>
                                    <h4><?= htmlspecialchars($realisateur['prenom'] . ' ' . $realisateur['nom']) ?></h4>
                                    <p class="item-year"><?= htmlspecialchars($realisateur['nationalite'] ?? 'Non renseigné') ?></p>
                                    <div class="votes-bar">
                                        <div class="votes-progress" style="width: <?= min($realisateur['pourcentage'] ?? 0, 100) ?>%;"></div>
                                    </div>
                                    <p class="votes-count">
                                        <?= number_format($realisateur['nb_votes'] ?? 0) ?> votes (<?= number_format($realisateur['pourcentage'] ?? 0, 1) ?>%)
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-results">Aucun résultat disponible pour les réalisateurs.</p>
        <?php endif; ?>
    </section>
</div>

<?php include dirname(dirname(__FILE__)) . '/layouts/footer.php'; ?>
