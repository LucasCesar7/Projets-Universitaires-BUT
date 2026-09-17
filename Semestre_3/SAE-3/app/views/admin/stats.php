<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
    exit;
}

$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootPath . '/app/config/config.php';
require_once $rootPath . '/app/controllers/StatsController.php';

$pdo = dbconnect();
$statsController = new StatsController($pdo);

// Récupération des données
$generalStats = $statsController->getGeneralStats();
$categoriesStats = $statsController->getCategoriesStats();
$votesTimeline = $statsController->getVotesTimeline();
$contentByCategory = $statsController->getContentByCategory();

include dirname(dirname(__FILE__)) . '/layouts/header.php';


?>


<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/stats.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="stats-container">
    <a href="/sae-3-festivote-tas-cesar/app/views/admin/dashboard.php" class="btn-back">← Retour au dashboard</a>
    <section class="stats-header">
        <h1>Statistiques du Festival</h1>
    </section>

    <!-- Statistiques Utilisateurs -->
    <section class="stats-section">
        <h2>Utilisateurs inscrits</h2>
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon"><img src="<?= ImageHelper::url('img/logo/logo-profile.png') ?>" width="60" alt="logo-film" class="img-container"></div>
                <div class="stat-content">
                    <h3><?= number_format($generalStats['total_electeurs']) ?></h3>
                    <p>Électeurs inscrits</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><img src="<?= ImageHelper::url('img/logo/logo-profile.png') ?>" width="60" alt="logo-film" class="img-container"></div>
                <div class="stat-content">
                    <h3><?= number_format($generalStats['total_candidats']) ?></h3>
                    <p>Candidats inscrits</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🔐</div>
                <div class="stat-content">
                    <h3><?= number_format($generalStats['total_admins']) ?></h3>
                    <p>Administrateurs</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistiques Contenu Validé -->
    <section class="stats-section">
        <h2>Contenu validé</h2>
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon"><img src="<?= ImageHelper::url('img/logo/logo-bobine.png') ?>" width="60" alt="logo-film" class="img-container"></div>
                <div class="stat-content">
                    <h3><?= number_format($generalStats['films_valides']) ?></h3>
                    <p>Films validés</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><img src="<?= ImageHelper::url('img/logo/logo-acteur.png') ?>" width="60" alt="logo-acteur" class="img-container"></div>
                <div class="stat-content">
                    <h3><?= number_format($generalStats['acteurs_valides']) ?></h3>
                    <p>Acteurs validés</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><img src="<?= ImageHelper::url('img/logo/logo-cam.png') ?>" width="60" alt="logo-real" class="img-container"></div>
                <div class="stat-content">
                    <h3><?= number_format($generalStats['realisateurs_valides']) ?></h3>
                    <p>Réalisateurs validés</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistiques En Attente -->
    <section class="stats-section">
        <h2>Propositions en attente de validation</h2>
        <div class="stats-cards">
            <div class="stat-card warning">
                <div class="stat-icon"><img src="<?= ImageHelper::url('img/logo/logo-bobine.png') ?>" width="60" alt="logo-film" class="img-container"></div>
                <div class="stat-content">
                    <h3><?= number_format($generalStats['films_en_attente']) ?></h3>
                    <p>Films en attente</p>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-icon"><img src="<?= ImageHelper::url('img/logo/logo-acteur.png') ?>" width="60" alt="logo-acteur" class="img-container"></div>
                <div class="stat-content">
                    <h3><?= number_format($generalStats['acteurs_en_attente']) ?></h3>
                    <p>Acteurs en attente</p>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-icon"><img src="<?= ImageHelper::url('img/logo/logo-cam.png') ?>" width="60" alt="logo-real" class="img-container"></div>
                <div class="stat-content">
                    <h3><?= number_format($generalStats['realisateurs_en_attente']) ?></h3>
                    <p>Réalisateurs en attente</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistiques Votes -->
    <section class="stats-section">
        <h2>Activité de vote</h2>
        <div class="stats-cards">
            <div class="stat-card highlight">
                <div class="stat-icon"><img src="<?= ImageHelper::url('img/logo/logo-ticket.png') ?>" width="60" alt="logo-ticket" class="img-container"></div>
                <div class="stat-content">
                    <h3><?= number_format($generalStats['total_votes']) ?></h3>
                    <p>Votes enregistrés</p>
                </div>
            </div>

            <div class="stat-card highlight">
                <div class="stat-icon"><img src="<?= ImageHelper::url('img/logo/logo-profile.png') ?>" width="60" alt="logo-film" class="img-container"></div>
                <div class="stat-content">
                    <h3><?= number_format($generalStats['votants_uniques']) ?></h3>
                    <p>Votants uniques</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">💬</div>
                <div class="stat-content">
                    <h3><?= number_format($generalStats['total_commentaires']) ?></h3>
                    <p>Commentaires postés</p>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-icon">⏳</div>
                <div class="stat-content">
                    <h3><?= number_format($generalStats['commentaires_en_attente']) ?></h3>
                    <p>Commentaires en attente</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Graphiques -->
    <section class="charts-section">
        <div class="chart-container">
            <h2>Évolution des votes dans le temps</h2>
            <canvas id="timelineChart"></canvas>
        </div>

        <div class="chart-container">
            <h2><img src="<?= ImageHelper::url('img/logo/logo-bobine.png') ?>" width="60" alt="logo-film" class="img-container"> Films par catégorie</h2>
            <canvas id="contentChart"></canvas>
        </div>
    </section>

    <!-- Répartition des votes par catégorie-->
    <section class="stats-section">
        <h2>Répartition des votes par catégorie</h2>
        <div class="table-container">
            <table class="stats-table">
                <thead>
                    <tr>
                        <th>Catégorie</th>
                        <th>Nombre de votes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categoriesStats as $cat): ?>
                        <tr>
                            <td><?= htmlspecialchars($cat['categorie']) ?></td>
                            <td><strong><?= number_format($cat['nb_votes']) ?></strong> vote(s)</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
// Graphique d'évolution des votes
const timelineData = {
    labels: <?= json_encode(array_column($votesTimeline, 'date')) ?>,
    datasets: [{
        label: 'Nombre de votes',
        data: <?= json_encode(array_column($votesTimeline, 'nb_votes')) ?>,
        borderColor: '#667eea',
        backgroundColor: 'rgba(102, 126, 234, 0.2)',
        fill: true,
        tension: 0.4
    }]
};

new Chart(document.getElementById('timelineChart'), {
    type: 'line',
    data: timelineData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});

// Graphique de contenu par catégorie
const contentData = {
    labels: <?= json_encode(array_column($contentByCategory, 'categorie')) ?>,
    datasets: [{
        label: 'Nombre de films',
        data: <?= json_encode(array_column($contentByCategory, 'nb_films')) ?>,
        backgroundColor: [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#FF6384', '#C9CBCF'
        ]
    }]
};

new Chart(document.getElementById('contentChart'), {
    type: 'bar',
    data: contentData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});
</script>
<script>
const btnBack = document.querySelector('.btn-back');

window.addEventListener('scroll', function() {
    if (window.scrollY > 100) {
        btnBack.classList.add('scrolled');
    } else {
        btnBack.classList.remove('scrolled');
    }
});
</script>

<?php include dirname(dirname(__FILE__)) . '/layouts/footer.php'; ?>
