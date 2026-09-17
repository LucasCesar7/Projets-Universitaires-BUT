<?php
// Partial : header.php
// En-tête réutilisable pour toutes les pages du site
// Gère l'affichage conditionnel selon l'état de connexion de l'utilisateur

// Inclure la gestion des sessions pour vérifier l'authentification
require_once __DIR__ . '/../../config/session.php';
?>

<!-- Feuilles de style -->
<link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/header.css')); ?>">
<!-- Icônes Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- En-tête principal -->
<header class="header" role="banner">
    <!-- Barre de navigation -->
    <nav class="navbar" role="navigation" aria-label="Navigation principale">
        <!-- Logo et nom du site -->
        <div class="nav-logo">
            <img loading="lazy" src="<?php echo htmlspecialchars(appUrl('assets/images/logo.png')); ?>" alt="Logo Social Media Awards" class="logo">
            <div class="logo-text">Social Media Awards</div>
        </div>

        <!-- Menu de navigation principal -->
        <ul class="nav-menu">
            <li><a href="<?php echo htmlspecialchars(publicRouteUrl('home')); ?>">Accueil</a></li>
            <li><a href="<?php echo htmlspecialchars(publicRouteUrl('categories')); ?>">Catégories</a></li>
            <li><a href="<?php echo htmlspecialchars(publicRouteUrl('nominees')); ?>">Nominés</a></li>
            <li><a href="<?php echo htmlspecialchars(publicRouteUrl('results')); ?>">Résultats</a></li>
            <li><a href="<?php echo htmlspecialchars(publicRouteUrl('contact')); ?>">Contacts</a></li>
            <li><a href="<?php echo htmlspecialchars(publicRouteUrl('about')); ?>">À propos</a></li>
        </ul>

        <!-- Section des boutons d'authentification -->
        <div class="nav-buttons">
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <!-- Affichage lorsque l'utilisateur est connecté -->
                <!-- Message de bienvenue avec le pseudonyme de l'utilisateur (Contraste corrigé) -->
                <span style="color: #333; font-weight: bold;">Bienvenue <?php echo htmlspecialchars($_SESSION['user_pseudonyme'] ?? 'Utilisateur'); ?> !</span>
                <!-- Bouton de déconnexion -->
                <form method="post" action="<?php echo htmlspecialchars(publicRouteUrl('logout')); ?>" style="display: inline; margin: 0;">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Services\CsrfService::token('logout'), ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="logout-button" aria-label="Se déconnecter de la plateforme">Déconnexion</button>
                </form>
            <?php else: ?>
                <!-- Affichage lorsque l'utilisateur n'est pas connecté -->
                <!-- Bouton de connexion -->
                <a href="<?php echo htmlspecialchars(publicRouteUrl('login')); ?>" class="login-button">Connexion</a>
                <!-- Bouton d'inscription -->
                <a href="<?php echo htmlspecialchars(publicRouteUrl('register')); ?>" class="signup-button">Inscription</a>
            <?php endif; ?>
        </div>

        <!-- Menu hamburger pour la version mobile (ARIA ajouté) -->
        <div class="nav-toggle" role="button" aria-label="Ouvrir le menu mobile" aria-expanded="false" tabindex="0">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
</header>

<!-- Script JavaScript pour la navigation mobile -->
<script src="<?php echo htmlspecialchars(appUrl('assets/js/header.js')); ?>"></script>