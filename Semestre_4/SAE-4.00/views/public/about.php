<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/header.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/footer.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/about.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>À Propos - Social Media Awards 2025</title>
</head>

<body>
    <?php require appPath('views/partials/header.php'); ?>

    <div class="main-content">
        <section class="about-hero">
            <div class="container">
                <h1>À Propos des Social Media Awards</h1>
                <p>Découvrez notre mission, notre vision et l'équipe derrière la plus grande célébration du digital</p>
            </div>
        </section>

        <section class="mission-section">
            <div class="container">
                <div class="mission-grid">
                    <div class="mission-content">
                        <h2>Notre Mission</h2>
                        <p>Les Social Media Awards ont été créés pour célébrer l'excellence, l'innovation et la créativité dans l'univers des médias sociaux. Nous croyons que chaque créateur mérite d'être reconnu pour son travail et son impact.</p>

                        <div class="mission-stats">
                            <div class="stat">
                                <div class="stat-number">3</div>
                                <div class="stat-label">Éditions</div>
                            </div>
                            <div class="stat">
                                <div class="stat-number">500+</div>
                                <div class="stat-label">Créateurs Récompensés</div>
                            </div>
                            <div class="stat">
                                <div class="stat-number">1M+</div>
                                <div class="stat-label">Votes</div>
                            </div>
                        </div>
                    </div>

                    <div class="mission-visual">
                        <div class="visual-card">
                            <i class="fas fa-trophy"></i>
                            <h3>Reconnaissance</h3>
                            <p>Valoriser le travail des créateurs</p>
                        </div>
                        <div class="visual-card">
                            <i class="fas fa-users"></i>
                            <h3>Communauté</h3>
                            <p>Rassembler les passionnés du digital</p>
                        </div>
                        <div class="visual-card">
                            <i class="fas fa-rocket"></i>
                            <h3>Innovation</h3>
                            <p>Encourager la créativité</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="team-section">
            <div class="container">
                <h2>Notre Équipe</h2>
                <div class="team-grid">
                    <div class="team-member">
                        <div class="member-photo">
                            <img src="<?php echo htmlspecialchars(appUrl('assets/images/team/member1.jpg'), ENT_QUOTES, 'UTF-8'); ?>" alt="Photo de Wendy Mechisso">
                        </div>
                        <h3>Wendy Mechisso</h3>
                        <p class="member-role">Administrateur</p>
                        <p class="member-bio">Passionné de digital avec 10 ans d'expérience dans les médias sociaux</p>
                    </div>

                    <div class="team-member">
                        <div class="member-photo">
                            <img src="<?php echo htmlspecialchars(appUrl('assets/images/team/member2.jpg'), ENT_QUOTES, 'UTF-8'); ?>" alt="Photo de Eunice Ligeiro">
                        </div>
                        <h3>Eunice Ligeiro</h3>
                        <p class="member-role">Administrateur</p>
                        <p class="member-bio">Expert en stratégie digitale et animation de communautés en ligne.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php require appPath('views/partials/footer.php'); ?>
</body>

</html>