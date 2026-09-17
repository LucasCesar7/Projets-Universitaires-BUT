<?php

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
    exit;
}

include dirname(dirname(__FILE__)) . '/layouts/header.php';
?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/dashboard.css">

<div class="admin-container">
    <section class="admin-header">
        <h1>🔐 Tableau de bord Administrateur</h1>
        <p>Bienvenue <?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></p>
    </section>

    <!-- Menu admin -->
    <section class="admin-menu">
        <div class="admin-card">
            <h3>📊 Statistiques</h3>
            <a href="/sae-3-festivote-tas-cesar/app/views/admin/stats.php">
                <p>Voir les statistiques</p>
                <button class="btn-admin">Accéder</button>
            </a>
        </div>

        <div class="admin-card">
            <h3>🎬 Gérer les films</h3>
            <a href="/sae-3-festivote-tas-cesar/app/controllers/AdminController.php?action=gestionFilms">
                <p>Ajouter/modifier/valider les films</p>
                <button class="btn-admin">Accéder</button>
            </a>


        </div>

        <div class="admin-card">
            <h3>🤵🏻 Gérer les acteurs</h3>
            <a href="/sae-3-festivote-tas-cesar/app/controllers/AdminController.php?action=gestionActeurs">
                <p>Ajouter/modifier/valider les acteurs</p>
                <button class="btn-admin">Accéder</button>

            </a>

        </div>

        <div class="admin-card">
            <h3>🎥 Gérer les réalisateurs</h3>
            <a href="/sae-3-festivote-tas-cesar/app/controllers/AdminController.php?action=gestionRealisateurs">
                <p>Ajouter/modifier/valider les réalisateurs</p>
                <button class="btn-admin">Accéder</button>

            </a>

        </div>

        <div class="admin-card">
            <h3>👥 Gérer les utilisateurs</h3>
            <a href="/sae-3-festivote-tas-cesar/app/controllers/GestionnaireUtilisateurController.php">
                <p>Voir et modérer les utilisateurs</p>
                <button class="btn-admin">Accéder</button>

            </a>

        </div>

        <div class="admin-card">
            <h3>⚙️ Paramètres</h3>
            <a href="/sae-3-festivote-tas-cesar/app/controllers/ConfigurationController.php" class="dashboard-card">
                <div class="card-icon"></div>
                <h3>Configurer le système</h3>
                <p>Gérer les périodes de vote et d'ajout</p>
            </a>
        </div>

        <!-- Consultation des logs -->
        <div class="admin-card">
            <h3>Logs</h3>
            <a href="/sae-3-festivote-tas-cesar/app/views/admin/audit-logs.php">
                <p>Voir les logs</p>
                <button class="btn-admin">Accéder</button>
            </a>
        </div>

    </section>

</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>