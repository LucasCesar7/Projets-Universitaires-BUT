<?php
// Partial : footer.php
// Pied de page réutilisable pour toutes les pages du site

require_once __DIR__ . '/../../config/paths.php';

$socialLinks = publicSocialLinks();
?>

<head>
    <!-- Métadonnées pour la compatibilité et l'affichage -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Feuilles de style -->
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/footer.css')); ?>">
    <!-- Icônes Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<footer class="footer">
    <!-- Contenu principal du pied de page -->
    <div class="footer-content">

        <!-- Première section : Logo et description -->
        <div class="footer-section">
            <div class="footer-logo">
                <!-- Logo de l'application -->
                <img loading="lazy" src="<?php echo htmlspecialchars(appUrl('assets/images/logo.png')); ?>" alt="Social Media Awards" class="footer-logo-img">
                <span class="footer-logo-text">Social Media Awards</span>
            </div>
            <!-- Description du site -->
            <p class="footer-description">
                Célébrer l'excellence dans les médias sociaux et reconnaître les talents les plus influents de l'ère numérique.
            </p>
            <!-- Liens vers les réseaux sociaux -->
            <div class="social-links">
                <?php foreach ($socialLinks as $socialLink): ?>
                    <a href="<?php echo htmlspecialchars($socialLink['url']); ?>" class="social-link" aria-label="<?php echo htmlspecialchars($socialLink['label']); ?>">
                        <i class="<?php echo htmlspecialchars($socialLink['icon']); ?>"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Deuxième section : Navigation rapide -->
        <div class="footer-section">
            <h3 class="footer-title">Liens Rapides</h3>
            <ul class="footer-links">
                <li><a href="<?php echo htmlspecialchars(publicRouteUrl('home')); ?>">Accueil</a></li>
                <li><a href="<?php echo htmlspecialchars(publicRouteUrl('categories')); ?>">Catégories</a></li>
                <li><a href="<?php echo htmlspecialchars(publicRouteUrl('nominees')); ?>">Nominés</a></li>
                <li><a href="<?php echo htmlspecialchars(publicRouteUrl('results')); ?>">Résultats</a></li>
                <li><a href="<?php echo htmlspecialchars(publicRouteUrl('results')); ?>">Vainqueurs</a></li>
            </ul>
        </div>

        <!-- Troisième section : Informations légales et utilitaires -->
        <div class="footer-section">
            <h3 class="footer-title">Informations</h3>
            <ul class="footer-links">
                <li><a href="<?php echo htmlspecialchars(publicRouteUrl('about')); ?>">À Propos</a></li>
                <li><a href="<?php echo htmlspecialchars(publicRouteUrl('faq')); ?>">FAQ</a></li>
                <li><a href="<?php echo htmlspecialchars(publicRouteUrl('privacy')); ?>">Politique de Confidentialité</a></li>
                <li><a href="<?php echo htmlspecialchars(publicRouteUrl('cgu')); ?>">Conditions d'Utilisation</a></li>
            </ul>
        </div>

        <!-- Quatrième section : Informations de contact -->
        <div class="footer-section">
            <h3 class="footer-title">Contact</h3>
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>contact@socialmediaawards.com</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <span>+33 1 23 45 67 89</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Saint-Dié-des-Vosges, France</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Section inférieure du pied de page (copyright et liens légaux) -->
    <div class="footer-bottom">
        <div class="footer-bottom-content">
            <!-- Copyright -->
            <p>&copy; 2026 Social Media Awards. Tous droits réservés.</p>
            <!-- Liens légaux supplémentaires -->
            <div class="footer-bottom-links">
                <a href="<?php echo htmlspecialchars(publicRouteUrl('privacy')); ?>">Confidentialité</a>
                <a href="<?php echo htmlspecialchars(publicRouteUrl('cgu')); ?>">Conditions</a>
                <a href="<?php echo htmlspecialchars(publicRouteUrl('privacy')); ?>">Cookies</a>
            </div>
        </div>
    </div>
    <?php require appPath('views/partials/cookie-banner.php'); ?>
</footer>