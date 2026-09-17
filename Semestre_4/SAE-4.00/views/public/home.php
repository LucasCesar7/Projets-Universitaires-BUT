<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/header.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/footer.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/index.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Social Media Awards 2026</title>
</head>

<body>
    <?php require appPath('views/partials/header.php'); ?>

    <div class="main-content">
        <section class="hero-banner">
            <div class="banner-container">
                <img src="<?php echo htmlspecialchars(appUrl('assets/images/banner1.png'), ENT_QUOTES, 'UTF-8'); ?>" alt="Bannière Social Media Awards 2026" class="hero-banner-image">
                <div class="banner-overlay">
                    <div class="banner-text">
                        <h1 class="banner-title">Social Media Awards 2026</h1>
                        <p class="banner-subtitle">Célébrez l'excellence numérique à travers les plateformes sociales</p>
                        <div class="banner-buttons">
                            <a href="<?php echo htmlspecialchars($voteLink, ENT_QUOTES, 'UTF-8'); ?>" class="btn-primary">
                                <i class="fas fa-vote-yea"></i>
                                Commencer à Voter
                            </a>
                            <a href="<?php echo htmlspecialchars($aboutUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn-secondary">
                                <i class="fas fa-info-circle"></i>
                                En savoir plus
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h2 class="hero-title">La Plus Grande Célébration du Digital</h2>
                    <p class="hero-description">
                        Les Social Media Awards récompensent l'innovation, la créativité et l'impact des contenus
                        à travers toutes les plateformes sociales. Rejoignez des milliers de passionnés pour
                        célébrer les talents qui façonnent l'univers numérique.
                    </p>
                    <div class="hero-stats">
                        <div class="hero-stat">
                            <div class="stat-number"><?php echo htmlspecialchars((string) $heroStats['categories'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="stat-label">Catégories</div>
                        </div>
                        <div class="hero-stat">
                            <div class="stat-number"><?php echo htmlspecialchars((string) $heroStats['platforms'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="stat-label">Plateformes</div>
                        </div>
                        <div class="hero-stat">
                            <div class="stat-number"><?php echo htmlspecialchars((string) $heroStats['votes'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="stat-label">Votes</div>
                        </div>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="platforms-showcase">
                        <div class="platform-item">
                            <i class="fab fa-tiktok"></i>
                            <span>TikTok</span>
                        </div>
                        <div class="platform-item">
                            <i class="fab fa-instagram"></i>
                            <span>Instagram</span>
                        </div>
                        <div class="platform-item">
                            <i class="fab fa-youtube"></i>
                            <span>YouTube</span>
                        </div>
                        <div class="platform-item">
                            <i class="fab fa-twitter"></i>
                            <span>Twitter</span>
                        </div>
                        <div class="platform-item">
                            <i class="fab fa-facebook"></i>
                            <span>Facebook</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="stats">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo htmlspecialchars((string) $sectionStats['categories'], ENT_QUOTES, 'UTF-8'); ?>+</div>
                        <div class="stat-label">Catégories</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-number"><?php echo htmlspecialchars((string) $sectionStats['candidatures'], ENT_QUOTES, 'UTF-8'); ?>+</div>
                        <div class="stat-label">Candidatures</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-number"><?php echo htmlspecialchars((string) $sectionStats['votes'], ENT_QUOTES, 'UTF-8'); ?>+</div>
                        <div class="stat-label">Votes</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-number"><?php echo htmlspecialchars((string) $sectionStats['platforms'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="stat-label">Plateformes</div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php require appPath('views/partials/footer.php'); ?>
</body>

</html>
