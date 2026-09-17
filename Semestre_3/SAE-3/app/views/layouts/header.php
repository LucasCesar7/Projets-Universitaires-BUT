<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootPath . '/app/helpers/ImageHelper.php';
require_once $rootPath . '/app/models/Configuration.php';
$configModel = new Configuration();

// Vérifier si les résultats sont disponibles
$resultatsDisponibles = $configModel->areResultatsDisponibles();
$isInPeriodeVote = $configModel->isInPeriodeVote();
$isInPeriodeAjout = $configModel->isInPeriodeAjout();

$isLoggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self'; 
        img-src 'self' data:; 
        script-src 'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com; 
        style-src 'self' 'unsafe-inline' https://unpkg.com https://fonts.googleapis.com https://cdnjs.cloudflare.com;
        font-src 'self' https://unpkg.com https://fonts.gstatic.com https://cdnjs.cloudflare.com;
        frame-src https://www.google.com https://www.recaptcha.net;
        connect-src 'self' https://www.google.com;
    ">



    <!-- logo site -->
    <link rel="icon" href="<?= ImageHelper::url('img/logo/logo-etoile2.png') ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>CineVote 2026 - Festival du Cinéma</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/header.css">
    <link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/app.css">
   

</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-container">
                <div class="logo">
                    <a href="/sae-3-festivote-tas-cesar/public/index.php">
                        <img class="logo-cine" src="<?= ImageHelper::url('img/logo/logo-cinevote.png') ?>" alt="Logo CineVote 2026">
                        <span class="logo-text">CineVote 2026</span>
                    </a>
                </div>
                
                <ul class="nav-links">
                    <li><a href="/sae-3-festivote-tas-cesar/public/index.php">Accueil</a></li>
                    <li><a href="/sae-3-festivote-tas-cesar/app/views/pages/categories.php">Catégories</a></li>
                    <?php if ($resultatsDisponibles): ?>
                        <li>
                            <a href="/sae-3-festivote-tas-cesar/app/views/pages/resultats.php" class="nav-resultats">
                                🏆 Résultats
                            </a>
                        </li>
                    <?php endif; ?>
                    <li><a href="/sae-3-festivote-tas-cesar/app/views/pages/contact.php">Contact</a></li>
                    
                    <?php if ($isLoggedIn): ?>
                        <?php if ($role === 'admin'): ?>
                            <li><a href="/sae-3-festivote-tas-cesar/app/controllers/AdminController.php?action=dashboard">Administration</a></li>
                        <?php endif; ?>
                        <li class="user-menu">
                            <a href="/sae-3-festivote-tas-cesar/app/views/profile/profil.php">
                            <img src="<?= ImageHelper::url('img/logo/logo-profile.png') ?>" alt="" width="40" height="40">
                            <span class="username">
                                 <?= htmlspecialchars($_SESSION['pseudo'] ?? $_SESSION['prenom']) ?>
                            </span>
                            </a>
                            
                        </li>
                        <li>
                            <a href="/sae-3-festivote-tas-cesar/app/views/auth/logout.php">
                                🚪 Se déconnecter
                            </a>
                        </li>
                    <?php else: ?>
                        <li><a href="/sae-3-festivote-tas-cesar/app/views/auth/login.php" class="btn-login">Connexion</a></li>
                        <li><a href="/sae-3-festivote-tas-cesar/app/views/auth/register.php" class="btn-register">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
         <!-- Bandeau d'information sur le statut du système -->
        <?php if ($isInPeriodeAjout): ?>
            <div class="system-banner ajout">
                Période d'ajout des œuvres en cours - Les candidats peuvent proposer des films, acteurs et réalisateurs
            </div>
        <?php elseif ($isInPeriodeVote): ?>
            <div class="system-banner vote">
                Période de vote en cours - Votez pour vos favoris !
            </div>
        <?php elseif ($resultatsDisponibles): ?>
            <div class="system-banner resultats">
                Les résultats sont disponibles ! <a href="/sae-3-festivote-tas-cesar/app/views/pages/resultats.php">Voir les résultats</a>
            </div>
        <?php endif; ?>

    <!-- Bannière Cookies -->
    <?php if (!isset($_COOKIE['cookie_notice_accepted'])): ?>
    <div id="cookie-notice">
        <p>
            Ce site utilise uniquement des <strong>cookies de session strictement nécessaires</strong> 
            au fonctionnement (authentification). Aucun cookie publicitaire ou de traçage.
            <a href="/sae-3-festivote-tas-cesar/app/views/pages/confidentialite.php">En savoir plus</a>
        </p>
        <button class="cookie-notice-btn" onclick="acceptCookieNotice()">J'ai compris</button>
    </div>

    <script>
    function acceptCookieNotice() {
        document.cookie = "cookie_notice_accepted=1; path=/; max-age=31536000; SameSite=Strict";
        document.getElementById('cookie-notice').style.display = 'none';
    }
    </script>
    <?php endif; ?>

    </header>
    
    <main>
