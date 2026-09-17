<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Electeur - Social Media Awards</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($userDashboardCssUrl); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .avatar-nav.has-photo {
            background: none;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .avatar-img-nav {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            display: block;
        }

        .avatar-nav-text {
            display: none;
        }
    </style>
</head>

<body>
    <header class="dashboard-header">
        <div class="header-content">
            <div class="logo-section">
                <img loading="lazy" src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo Social Media Awards" class="logo-image" onerror="this.style.display='none'">
                <h1>Social Media <span class="highlight">Awards</span></h1>
            </div>

            <nav class="user-nav">
                <div class="user-info-nav">
                    <div class="avatar-nav <?php echo $hasPhoto ? 'has-photo' : ''; ?>">
                        <?php if ($hasPhoto): ?>
                            <img loading="lazy" src="<?php echo $web_path . htmlspecialchars((string) $userPhoto); ?>"
                                alt="<?php echo htmlspecialchars($userPseudonyme); ?>"
                                class="avatar-img-nav">
                        <?php else: ?>
                            <span class="avatar-nav-text"><?php echo htmlspecialchars($initials); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="user-details-nav">
                        <span class="user-name-nav"><?php echo htmlspecialchars($userPseudonyme); ?></span>
                        <span class="user-role-nav">Electeur</span>
                    </div>
                </div>

                <form method="post" action="<?php echo htmlspecialchars($logoutUrl); ?>" class="m-0">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($logoutToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        Deconnexion
                    </button>
                </form>
            </nav>
        </div>
    </header>

    <main class="dashboard-container">
        <aside class="dashboard-sidebar">
            <nav class="sidebar-nav">
                <a href="#" class="nav-item active">
                    <i class="fas fa-home"></i>
                    <span>Tableau de Bord</span>
                </a>
                <a href="<?php echo htmlspecialchars($voteUrl); ?>" class="nav-item">
                    <i class="fas fa-vote-yea"></i>
                    <span>Voter Maintenant</span>
                </a>
                <a href="<?php echo htmlspecialchars($categoriesUrl); ?>" class="nav-item">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
                <a href="<?php echo htmlspecialchars($nomineesUrl); ?>" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>Candidats</span>
                </a>
                <a href="<?php echo htmlspecialchars($editProfileUrl); ?>" class="nav-item">
                    <i class="fas fa-user-edit"></i>
                    <span>Mon Profil</span>
                </a>
                <a href="<?php echo htmlspecialchars($resultsUrl); ?>" class="nav-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Resultats</span>
                </a>
            </nav>
        </aside>

        <div class="dashboard-main">
            <section class="hero-section">
                <div class="hero-content">
                    <div class="hero-text">
                        <h1>Bonjour, <?php echo htmlspecialchars($userPseudonyme); ?>!</h1>
                        <p>Votre espace personnel pour participer aux Social Media Awards.</p>
                        <div class="hero-actions">
                            <a href="<?php echo htmlspecialchars($voteUrl); ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-vote-yea"></i>
                                Commencer a Voter
                            </a>
                            <a href="<?php echo htmlspecialchars($categoriesUrl); ?>" class="btn btn-outline btn-lg">
                                <i class="fas fa-search"></i>
                                Explorer Categories
                            </a>
                        </div>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-card">
                            <i class="fas fa-vote-yea"></i>
                            <div class="stat-content">
                                <h3><?php echo htmlspecialchars((string) $votesCount); ?></h3>
                                <p>Votes emis</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-trophy"></i>
                            <div class="stat-content">
                                <h3><?php echo htmlspecialchars((string) $activeElections); ?></h3>
                                <p>Elections actives</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-medal"></i>
                            <div class="stat-content">
                                <h3><?php echo htmlspecialchars((string) count($availableCategories)); ?></h3>
                                <p>Categories disponibles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="voting-section">
                <div class="section-header">
                    <h2><i class="fas fa-vote-yea"></i> Votre Etat de Vote</h2>
                </div>

                <div class="voting-grid">
                    <div class="status-card <?php echo $hasVotedInActiveCategories ? 'voted' : 'not-voted'; ?>">
                        <div class="status-icon">
                            <i class="fas <?php echo $hasVotedInActiveCategories ? 'fa-check-circle' : 'fa-clock'; ?>"></i>
                        </div>
                        <div class="status-content">
                            <h3><?php echo $hasVotedInActiveCategories ? 'Participation en cours' : 'Pret a voter'; ?></h3>
                            <p><?php echo $hasVotedInActiveCategories
                                    ? 'Vous avez deja vote dans certaines categories. Continuez!'
                                    : 'Aucun vote emis. Commencez maintenant!'; ?></p>
                        </div>
                        <a href="<?php echo htmlspecialchars($voteUrl); ?>" class="btn btn-primary">
                            <?php echo $hasVotedInActiveCategories ? 'Continuer a Voter' : 'Voter Maintenant'; ?>
                        </a>
                    </div>
                </div>
            </section>

            <section class="categories-section">
                <div class="section-header">
                    <h2><i class="fas fa-star"></i> Categories a Voter</h2>
                    <a href="<?php echo htmlspecialchars($voteUrl); ?>" class="btn btn-outline">
                        Voir toutes
                    </a>
                </div>

                <div class="categories-grid">
                    <?php if (!empty($availableCategories)): ?>
                        <?php foreach ($availableCategories as $category): ?>
                            <?php
                            $categoryId = (int) ($category['id'] ?? 0);
                            $categoryName = (string) ($category['name'] ?? 'Categorie sans nom');
                            $platform = (string) ($category['platform'] ?? 'General');
                            $description = (string) ($category['description'] ?? '');
                            $nominationCount = (int) ($category['nomination_count'] ?? 0);
                            $dateFin = (string) ($category['date_fin_votes'] ?? '');
                            $hasVoted = !empty($category['has_voted']);
                            $canVote = !empty($category['can_vote']);
                            $icon = (string) ($category['icon'] ?? 'fa-tag');
                            ?>
                            <div class="category-card <?php echo $hasVoted ? 'voted' : ''; ?>">
                                <div class="category-header">
                                    <div class="category-icon">
                                        <i class="fas <?php echo htmlspecialchars($icon); ?>"></i>
                                    </div>
                                    <div class="category-info">
                                        <h4><?php echo htmlspecialchars($categoryName); ?></h4>
                                        <span class="platform"><?php echo htmlspecialchars($platform); ?></span>
                                    </div>
                                    <?php if ($hasVoted): ?>
                                        <span class="vote-badge voted"><i class="fas fa-check"></i> Vote</span>
                                    <?php elseif ($canVote): ?>
                                        <span class="vote-badge available"><i class="fas fa-vote-yea"></i> Disponible</span>
                                    <?php elseif ($nominationCount === 0): ?>
                                        <span class="vote-badge no-nominations"><i class="fas fa-users-slash"></i> Pas de nomines</span>
                                    <?php else: ?>
                                        <span class="vote-badge inactive"><i class="fas fa-info-circle"></i> Indisponible</span>
                                    <?php endif; ?>
                                </div>

                                <p class="category-desc">
                                    <?php echo htmlspecialchars(mb_strlen($description) > 100 ? mb_substr($description, 0, 100) . '...' : $description); ?>
                                </p>

                                <div class="category-stats">
                                    <div class="stat">
                                        <i class="fas fa-users"></i>
                                        <span><?php echo htmlspecialchars((string) $nominationCount); ?> candidats</span>
                                    </div>
                                    <div class="stat">
                                        <i class="fas fa-clock"></i>
                                        <span>Fin: <?php echo $dateFin !== '' ? date('d/m/Y', strtotime($dateFin)) : 'Non definie'; ?></span>
                                    </div>
                                </div>

                                <div class="category-actions">
                                    <?php if ($canVote && $categoryId > 0): ?>
                                        <a href="<?php echo htmlspecialchars($voteUrl . '?category_id=' . $categoryId); ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-vote-yea"></i>
                                            Voter maintenant
                                        </a>
                                    <?php elseif ($hasVoted): ?>
                                        <button class="btn btn-success btn-sm" disabled>
                                            <i class="fas fa-check"></i>
                                            Deja vote
                                        </button>
                                    <?php elseif ($nominationCount === 0): ?>
                                        <button class="btn btn-warning btn-sm" disabled>
                                            <i class="fas fa-users-slash"></i>
                                            Pas de nomines
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-disabled btn-sm" disabled>
                                            <i class="fas fa-clock"></i>
                                            Indisponible
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($categoryId > 0): ?>
                                        <a href="<?php echo htmlspecialchars($nomineesUrl . '?category=' . $categoryId); ?>" class="btn btn-outline btn-sm">
                                            Voir candidats
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h3>Aucune categorie disponible</h3>
                            <p>Il n'y a pas d'elections actives pour le moment.</p>
                            <p><small>Verifiez si des categories ont ete creees avec des dates de vote valides.</small></p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="elections-section">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-alt"></i> Elections Actives</h2>
                </div>

                <div class="elections-list">
                    <?php if (!empty($electionCards)): ?>
                        <?php foreach ($electionCards as $edition): ?>
                            <div class="election-card">
                                <div class="election-header">
                                    <h3><?php echo htmlspecialchars((string) ($edition['nom'] ?? 'Edition')); ?> <?php echo htmlspecialchars((string) ($edition['annee'] ?? '')); ?></h3>
                                    <span class="election-status active">En cours</span>
                                </div>

                                <div class="election-body">
                                    <p class="election-desc">
                                        <?php echo htmlspecialchars((string) count($edition['categories'] ?? [])); ?> categories disponibles
                                    </p>

                                    <div class="election-stats">
                                        <div class="stat-item">
                                            <i class="fas fa-hourglass-end"></i>
                                            <span>Cloture: <?php echo !empty($edition['date_fin']) ? date('d/m/Y', strtotime((string) $edition['date_fin'])) : 'Non definie'; ?></span>
                                        </div>
                                        <div class="stat-item">
                                            <i class="fas fa-users"></i>
                                            <span><?php echo htmlspecialchars((string) ($edition['total_candidates'] ?? 0)); ?> candidats</span>
                                        </div>
                                    </div>

                                    <div class="election-categories">
                                        <?php foreach (array_slice($edition['categories'] ?? [], 0, 3) as $categoryName): ?>
                                            <span class="category-tag"><?php echo htmlspecialchars((string) $categoryName); ?></span>
                                        <?php endforeach; ?>
                                        <?php if (count($edition['categories'] ?? []) > 3): ?>
                                            <span class="category-tag more">+<?php echo count($edition['categories']) - 3; ?> autres</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="election-footer">
                                    <a href="<?php echo htmlspecialchars($voteUrl); ?>" class="btn btn-primary">
                                        <i class="fas fa-play"></i>
                                        Participer
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-alt"></i>
                            <h3>Aucune election active</h3>
                            <p>Aucune edition avec des categories actives n'a ete trouvee.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="recent-votes">
                <div class="section-header">
                    <h2><i class="fas fa-history"></i> Vos Derniers Votes</h2>
                </div>

                <div class="votes-list">
                    <?php if (!empty($recentVotes)): ?>
                        <?php foreach ($recentVotes as $vote): ?>
                            <?php
                            $categoryId = (int) ($vote['id_categorie'] ?? 0);
                            $categoryName = (string) ($vote['category_nom'] ?? 'Categorie inconnue');
                            $nominationName = (string) ($vote['nomination_libelle'] ?? 'Nomination inconnue');
                            $voteDate = (string) ($vote['date_heure_vote'] ?? '');
                            ?>
                            <div class="vote-item">
                                <div class="vote-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="vote-info">
                                    <h4><?php echo htmlspecialchars($categoryName); ?></h4>
                                    <p class="vote-detail">
                                        Vote pour: <strong><?php echo htmlspecialchars($nominationName); ?></strong>
                                    </p>
                                    <span class="vote-date">
                                        <i class="fas fa-clock"></i>
                                        <?php echo $voteDate !== '' ? date('d/m/Y H:i', strtotime($voteDate)) : 'Date inconnue'; ?>
                                    </span>
                                </div>
                                <?php if ($categoryId > 0): ?>
                                    <div class="vote-actions">
                                        <a href="<?php echo htmlspecialchars($nomineesUrl . '?category=' . $categoryId); ?>" class="btn btn-outline btn-sm">
                                            Voir categorie
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-vote-yea"></i>
                            <h3>Aucun vote enregistre</h3>
                            <p>Commencez a voter pour voir votre historique ici.</p>
                            <a href="<?php echo htmlspecialchars($voteUrl); ?>" class="btn btn-primary mt-2">
                                Voter maintenant
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <footer class="dashboard-footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="<?php echo htmlspecialchars($categoriesUrl); ?>">Categories</a>
                <a href="<?php echo htmlspecialchars($nomineesUrl); ?>">Candidats</a>
                <a href="<?php echo htmlspecialchars($resultsUrl); ?>">Resultats</a>
                <a href="<?php echo htmlspecialchars($contactUrl); ?>">Contact</a>
                <a href="<?php echo htmlspecialchars($aboutUrl); ?>">A propos</a>
                <a href="<?php echo htmlspecialchars($faqUrl); ?>">FAQ</a>
            </div>
            <div class="copyright">
                &copy; <?php echo htmlspecialchars((string) $copyrightYear); ?> Social Media Awards. Tous droits reserves.
            </div>
        </div>
    </footer>

    <script src="<?php echo htmlspecialchars($userDashboardJsUrl); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.category-card, .election-card, .vote-item');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';

                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            document.querySelectorAll('.category-card:not(.voted) .btn-primary').forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.05)';
                });

                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            setInterval(() => {
                fetch('<?php echo addslashes($checkSessionUrl); ?>')
                    .then(response => response.json())
                    .then(data => {
                        if (!data.authenticated) {
                            window.location.href = '<?php echo addslashes($loginUrl); ?>';
                        }
                    })
                    .catch(() => {
                        console.log('Erreur de verification de session');
                    });
            }, 300000);
        });
    </script>
</body>

</html>