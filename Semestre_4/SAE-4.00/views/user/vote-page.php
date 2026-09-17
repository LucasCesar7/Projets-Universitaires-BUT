<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Voter - Social Media Awards</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($userDashboardCssUrl); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($voteCssUrl); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --progress-percentage: <?php echo $overviewProgressPercentage; ?>%;
        }

        .progress-fill {
            width: var(--progress-percentage) !important;
        }
    </style>
</head>

<body>
    <header class="dashboard-header">
        <div class="header-content">
            <div class="logo-section">
                <img loading="lazy" src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Social Media Awards" class="logo-image">
                <h1>Social Media <span class="highlight">Awards</span></h1>
            </div>

            <nav class="user-nav">
                <div class="user-info-nav">
                    <div class="avatar-nav"><?php echo htmlspecialchars($initials); ?></div>
                    <div class="user-details-nav">
                        <span class="user-name-nav"><?php echo htmlspecialchars($userPseudonyme); ?></span>
                        <span class="user-role-nav">Électeur</span>
                    </div>
                </div>

                <a href="<?php echo htmlspecialchars($userDashboardUrl); ?>" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Retour au dashboard
                </a>
            </nav>
        </div>
    </header>

    <main class="dashboard-container">
        <div class="dashboard-main">
            <?php if ($success && $successMessage): ?>
                <div class="voting-alert alert-success">
                    <i class="fas fa-check-circle fa-2x"></i>
                    <div>
                        <strong>Succès!</strong>
                        <p><?php echo htmlspecialchars($successMessage); ?></p>
                        <?php if ($lastVote && !empty($lastVote['vote_id'])): ?>
                            <p class="vote-details">
                                <i class="fas fa-fingerprint"></i>
                                ID de vote: <code><?php echo htmlspecialchars((string) $lastVote['vote_id']); ?></code>
                            </p>
                        <?php endif; ?>
                        <?php if ($lastVoteCertificate): ?>
                            <p class="vote-details">
                                <i class="fas fa-certificate"></i>
                                Certificat de participation généré
                                <?php if (!empty($lastVoteCertificate['id_certificat'])): ?>
                                    <span> n° <code><?php echo htmlspecialchars((string) $lastVoteCertificate['id_certificat']); ?></code></span>
                                <?php endif; ?>
                                <?php if ($lastVoteCertificateDate): ?>
                                    <span> le <?php echo htmlspecialchars($lastVoteCertificateDate); ?></span>
                                <?php endif; ?>
                                <?php if ($lastVoteCertificateReference): ?>
                                    <span title="<?php echo htmlspecialchars($lastVoteCertificate['hash_certificat']); ?>">
                                        · Réf. <code><?php echo htmlspecialchars($lastVoteCertificateReference); ?></code>
                                    </span>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <?php if ($alreadyVoted): ?>
                    <div class="already-voted-alert">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                        <div>
                            <strong>Attention</strong>
                            <p><?php echo htmlspecialchars($error); ?></p>
                            <p><small>Vous ne pouvez voter qu'une seule fois par catégorie.</small></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="voting-alert alert-error">
                        <i class="fas fa-exclamation-circle fa-2x"></i>
                        <div>
                            <strong>Erreur</strong>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="vote-page-container">
                <?php if (!$categoryId): ?>
                    <section class="categories-overview">
                        <div class="section-header">
                            <div class="section-title">
                                <i class="fas fa-vote-yea"></i>
                                <h2>Voter dans les catégories</h2>
                            </div>
                            <p>Sélectionnez une catégorie pour commencer à voter</p>
                        </div>

                        <?php if (empty($pageData['available_categories'])): ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h3>Aucune catégorie disponible pour le moment</h3>
                                <p>Les votes ne sont pas encore ouverts ou vous avez déjà voté dans toutes les catégories.</p>
                                <div class="empty-state-actions">
                                    <a href="<?php echo htmlspecialchars($userDashboardUrl); ?>" class="btn btn-primary">
                                        <i class="fas fa-home"></i>
                                        Retour au tableau de bord
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="categories-grid">
                                <?php foreach ($pageData['available_categories'] as $category): ?>
                                    <?php
                                    $status = null;
                                    foreach ($pageData['voting_status'] as $statusOption) {
                                        if ($statusOption['category_id'] == $category['id_categorie']) {
                                            $status = $statusOption;
                                            break;
                                        }
                                    }

                                    $actualNominationCount = (int) ($category['nomination_count'] ?? 0);
                                    $hasNominations = $actualNominationCount > 0;
                                    $canVote = $status && !$status['has_voted'] && $status['is_active'] && $hasNominations;
                                    ?>
                                    <div class="category-card <?php echo $status && $status['has_voted'] ? 'voted' : ''; ?>"
                                        data-start-date="<?php echo htmlspecialchars((string) ($category['date_debut_votes'] ?? '')); ?>"
                                        data-end-date="<?php echo htmlspecialchars((string) ($category['date_fin_votes'] ?? '')); ?>"
                                        data-nominations="<?php echo htmlspecialchars((string) ($category['nomination_count'] ?? 0)); ?>">
                                        <div class="category-header">
                                            <div class="category-icon">
                                                <i class="fas fa-trophy"></i>
                                            </div>
                                            <div class="category-badge">
                                                <?php if ($status && $status['has_voted']): ?>
                                                    <span class="badge voted-badge">
                                                        <i class="fas fa-check-circle"></i>
                                                        Voté
                                                    </span>
                                                <?php elseif ($canVote): ?>
                                                    <span class="badge active-badge">
                                                        <i class="fas fa-vote-yea"></i>
                                                        Disponible
                                                    </span>
                                                <?php elseif (!$hasNominations): ?>
                                                    <span class="badge no-nominations-badge">
                                                        <i class="fas fa-users-slash"></i>
                                                        Pas de nominés
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge inactive-badge">
                                                        <i class="fas fa-info-circle"></i>
                                                        <?php echo $status['has_voted'] ? 'Déjà voté' : 'Indisponible'; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="category-body">
                                            <h3><?php echo htmlspecialchars($category['nom']); ?></h3>

                                            <div class="category-meta">
                                                <div class="meta-item">
                                                    <i class="fas fa-calendar"></i>
                                                    <span>
                                                        <?php if (!empty($category['date_debut_votes']) && !empty($category['date_fin_votes'])): ?>
                                                            <?php echo date('d/m/Y', strtotime($category['date_debut_votes'])); ?> -
                                                            <?php echo date('d/m/Y', strtotime($category['date_fin_votes'])); ?>
                                                        <?php elseif (isset($category['vote_start_formatted'])): ?>
                                                            <?php echo htmlspecialchars((string) $category['vote_start_formatted']); ?> -
                                                            <?php echo htmlspecialchars((string) $category['vote_end_formatted']); ?>
                                                        <?php else: ?>
                                                            Période d'édition
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="fas fa-users"></i>
                                                    <span><?php echo htmlspecialchars((string) $category['nomination_count']); ?> nominés</span>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="fas fa-hashtag"></i>
                                                    <span><?php echo htmlspecialchars((string) ($category['plateforme_cible'] ?? 'Toutes plateformes')); ?></span>
                                                </div>
                                            </div>

                                            <?php if (!empty($category['description'])): ?>
                                                <p class="category-desc"><?php echo htmlspecialchars(substr((string) $category['description'], 0, 100)); ?>...</p>
                                            <?php endif; ?>

                                            <div class="category-stats">
                                                <div class="stat">
                                                    <div class="number"><?php echo htmlspecialchars((string) $category['nomination_count']); ?></div>
                                                    <div class="label">Nominés</div>
                                                </div>
                                                <div class="stat">
                                                    <div class="number"><?php echo $status && $status['has_voted'] ? '1' : '0'; ?></div>
                                                    <div class="label">Votes</div>
                                                </div>
                                            </div>

                                            <div class="category-actions">
                                                <?php if ($status && $status['has_voted']): ?>
                                                    <button class="btn btn-success btn-block" disabled>
                                                        <i class="fas fa-check-circle"></i>
                                                        Déjà voté
                                                    </button>
                                                <?php elseif ($canVote): ?>
                                                    <form method="POST" action="" class="category-form">
                                                        <input type="hidden" name="action" value="start_voting">
                                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($startVotingCsrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                                        <input type="hidden" name="category_id" value="<?php echo htmlspecialchars((string) $category['id_categorie']); ?>">
                                                        <button type="submit" class="btn btn-primary btn-block">
                                                            <i class="fas fa-vote-yea"></i>
                                                            Voter maintenant
                                                        </button>
                                                    </form>
                                                <?php elseif (!$hasNominations): ?>
                                                    <button class="btn btn-disabled btn-block" disabled>
                                                        <i class="fas fa-users-slash"></i>
                                                        Pas de nominés
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-disabled btn-block" disabled>
                                                        <i class="fas fa-clock"></i>
                                                        Indisponible
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </section>

                    <section class="voting-progress">
                        <div class="section-header">
                            <div class="section-title">
                                <i class="fas fa-chart-line"></i>
                                <h2>Votre progression</h2>
                            </div>
                        </div>

                        <div class="progress-stats">
                            <?php $percentage = $overviewProgressPercentage; ?>
                            <div class="progress-circle">
                                <svg width="140" height="140" viewBox="0 0 140 140">
                                    <defs>
                                        <linearGradient id="progress-gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#4FBDAB" />
                                            <stop offset="100%" stop-color="#3da895" />
                                        </linearGradient>
                                    </defs>
                                    <circle class="progress-bg" cx="70" cy="70" r="65"></circle>
                                    <circle class="progress-bar" cx="70" cy="70" r="65"
                                        stroke-dasharray="<?php echo 2 * 3.14159 * 65; ?>"
                                        stroke-dashoffset="<?php echo 2 * 3.14159 * 65 * (1 - $percentage / 100); ?>"></circle>
                                </svg>
                                <div class="progress-text">
                                    <span class="percentage"><?php echo htmlspecialchars((string) $percentage); ?>%</span>
                                    <span class="progress-label">Complété</span>
                                </div>
                            </div>

                            <div class="progress-details">
                                <div class="detail-item">
                                    <span class="detail-value"><?php echo htmlspecialchars((string) $totalCategories); ?></span>
                                    <span class="detail-label">Catégories totales</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-value"><?php echo htmlspecialchars((string) $votedCategories); ?></span>
                                    <span class="detail-label">Votes effectués</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-value"><?php echo htmlspecialchars((string) $availableCategories); ?></span>
                                    <span class="detail-label">Disponibles</span>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php else: ?>
                    <section class="voting-interface">
                        <div class="voting-header">
                            <a href="<?php echo htmlspecialchars($votePageUrl); ?>" class="back-to-categories">
                                <i class="fas fa-arrow-left"></i>
                                Retour aux catégories
                            </a>

                            <div class="voting-title">
                                <h2><?php echo htmlspecialchars((string) ($currentCategory['nom'] ?? 'Catégorie')); ?></h2>
                                <p><?php echo htmlspecialchars((string) ($currentCategory['description'] ?? 'Sélectionnez votre favori parmi les nominés')); ?></p>

                                <?php if (!empty($currentCategory['date_debut_votes']) && !empty($currentCategory['date_fin_votes'])): ?>
                                    <div class="voting-period">
                                        <i class="fas fa-clock"></i>
                                        <span>Période de vote: du <?php echo date('d/m/Y', strtotime($currentCategory['date_debut_votes'])); ?> au <?php echo date('d/m/Y', strtotime($currentCategory['date_fin_votes'])); ?></span>
                                    </div>
                                <?php elseif (!empty($currentCategory['id_edition'])): ?>
                                    <div class="voting-period">
                                        <i class="fas fa-clock"></i>
                                        <span>Période de vote: édition <?php echo htmlspecialchars((string) ($currentCategory['annee'] ?? '')); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!$viewResults && $categoryResultsUrl !== null && !empty($nominations)): ?>
                                    <div class="voting-title-actions">
                                        <a href="<?php echo htmlspecialchars($categoryResultsUrl); ?>" class="btn btn-outline">
                                            <i class="fas fa-chart-bar"></i>
                                            Voir les résultats actuels
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="voting-info">
                                <div class="info-card">
                                    <i class="fas fa-info-circle"></i>
                                    <div class="info-content">
                                        <h4>Informations importantes</h4>
                                        <p>Vous ne pouvez voter qu'une seule fois dans cette catégorie. Votre vote est anonyme et sécurisé.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($alreadyVoted): ?>
                            <div class="empty-state">
                                <i class="fas fa-check-circle fa-3x" style="color: #32D583;"></i>
                                <h3>Vous avez déjà voté dans cette catégorie</h3>
                                <p>Votre vote a déjà été enregistré. Vous ne pouvez voter qu'une seule fois par catégorie.</p>
                                <div class="empty-state-actions">
                                    <a href="<?php echo htmlspecialchars($votePageUrl); ?>" class="btn btn-primary">
                                        <i class="fas fa-arrow-left"></i>
                                        Retour aux catégories
                                    </a>
                                </div>
                            </div>
                        <?php elseif (!$categoryVotingAvailable): ?>
                            <div class="empty-state">
                                <i class="fas fa-ban"></i>
                                <h3>Vote indisponible</h3>
                                <p><?php echo htmlspecialchars($error ?? 'Cette catégorie ne peut pas être ouverte pour le moment.'); ?></p>
                                <div class="empty-state-actions">
                                    <a href="<?php echo htmlspecialchars($votePageUrl); ?>" class="btn btn-primary">
                                        <i class="fas fa-arrow-left"></i>
                                        Retour aux catégories
                                    </a>
                                </div>
                            </div>
                        <?php elseif (empty($nominations)): ?>
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <h3>Aucun nominé disponible</h3>
                                <p>Il n'y a actuellement aucun nominé dans cette catégorie.</p>
                                <div class="empty-state-actions">
                                    <a href="<?php echo htmlspecialchars($votePageUrl); ?>" class="btn btn-primary">
                                        <i class="fas fa-arrow-left"></i>
                                        Retour aux catégories
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if (!$viewResults): ?>
                                <form method="POST" action="" class="voting-form" id="voteForm">
                                    <input type="hidden" name="action" value="cast_vote">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($castVoteCsrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="category_id" value="<?php echo htmlspecialchars((string) $categoryId); ?>">
                                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($votingToken); ?>">

                                    <div class="nominations-grid">
                                        <?php foreach ($nominations as $index => $nomination): ?>
                                            <?php $nominationImageUrl = resolveAppAssetUrl($nomination['url_image'] ?? null, 'assets/images/Nominees/nominee1.jpg'); ?>
                                            <?php $isPreselectedNomination = $preselectedNominationId !== null && (int) $nomination['id_nomination'] === $preselectedNominationId; ?>
                                            <div class="nomination-card<?php echo $isPreselectedNomination ? ' selected' : ''; ?>" onclick="selectNomination(<?php echo (int) $nomination['id_nomination']; ?>, '<?php echo addslashes((string) $nomination['libelle']); ?>')">
                                                <label for="nomination_<?php echo (int) $nomination['id_nomination']; ?>" style="cursor: pointer; display: block; width: 100%; height: 100%;">
                                                    <input type="radio" name="nomination_id" id="nomination_<?php echo (int) $nomination['id_nomination']; ?>" value="<?php echo (int) $nomination['id_nomination']; ?>" class="nomination-radio" <?php echo $isPreselectedNomination ? 'checked' : ''; ?> style="display: none;">
                                                    <div class="nomination-content">
                                                        <div class="nomination-header">
                                                            <div class="nomination-rank">#<?php echo $index + 1; ?></div>
                                                            <div class="nomination-title">
                                                                <h3><?php echo htmlspecialchars((string) $nomination['libelle']); ?></h3>
                                                                <div class="nomination-candidate">
                                                                    <i class="fas fa-user"></i>
                                                                    <span><?php echo htmlspecialchars((string) ($nomination['candidate_name'] ?? 'Candidat')); ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="nomination-platform">
                                                                <span class="platform-badge <?php echo htmlspecialchars(strtolower((string) ($nomination['plateforme'] ?? 'all'))); ?>">
                                                                    <i class="fab fa-<?php echo htmlspecialchars(strtolower((string) ($nomination['plateforme'] ?? 'users'))); ?>"></i>
                                                                    <?php echo htmlspecialchars((string) ($nomination['plateforme'] ?? 'All')); ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="nomination-body">
                                                            <?php if ($nominationImageUrl !== null): ?>
                                                                <div class="nomination-image">
                                                                    <img loading="lazy" src="<?php echo htmlspecialchars($nominationImageUrl); ?>"
                                                                        alt="Image de la nomination pour <?php echo htmlspecialchars((string) $nomination['libelle']); ?> - Candidat : <?php echo htmlspecialchars((string) ($nomination['candidate_name'] ?? 'Inconnu')); ?>" onerror="this.src='<?php echo htmlspecialchars(appUrl('assets/images/Nominees/nominee1.jpg'), ENT_QUOTES, 'UTF-8'); ?>'">
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if (!empty($nomination['argumentaire'])): ?>
                                                                <div class="nomination-description">
                                                                    <p><?php echo htmlspecialchars(substr((string) $nomination['argumentaire'], 0, 200)); ?>...</p>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="nomination-footer">
                                                            <div class="nomination-stats">
                                                                <div class="stat">
                                                                    <i class="fas fa-vote-yea"></i>
                                                                    <span><?php echo htmlspecialchars((string) ($nomination['vote_count'] ?? 0)); ?> votes</span>
                                                                </div>
                                                                <?php if (!empty($nomination['url_content'])): ?>
                                                                    <div class="stat">
                                                                        <i class="fas fa-eye"></i>
                                                                        <a href="<?php echo htmlspecialchars((string) $nomination['url_content']); ?>" target="_blank" class="btn-link">
                                                                            Voir le contenu
                                                                        </a>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="nomination-select">
                                                                <div class="select-indicator">
                                                                    <i class="fas fa-check-circle"></i>
                                                                    <span>Sélectionner</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="voting-actions">
                                        <div class="voting-security">
                                            <div class="security-notice">
                                                <i class="fas fa-shield-alt"></i>
                                                <div class="notice-content">
                                                    <h4>Vote sécurisé et anonyme</h4>
                                                    <p>Votre vote est chiffré et ne peut être associé à votre identité.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="voting-submit">
                                            <button type="button" class="btn btn-lg" onclick="confirmVote()" id="submitVoteBtn" disabled>
                                                <i class="fas fa-paper-plane"></i>
                                                <span class="btn-text">Envoyer mon vote</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            <?php else: ?>
                                <div class="results-view">
                                    <div class="results-header">
                                        <h3><i class="fas fa-chart-bar"></i> Résultats pour cette catégorie</h3>
                                        <p>Statistiques actuelles des votes</p>
                                    </div>

                                    <div class="results-grid">
                                        <?php
                                        usort($nominations, function ($left, $right) {
                                            return (($right['vote_count'] ?? 0) <=> ($left['vote_count'] ?? 0));
                                        });

                                        $totalVotes = array_sum(array_column($nominations, 'vote_count'));
                                        ?>
                                        <?php foreach ($nominations as $index => $nomination): ?>
                                            <?php $percentage = $totalVotes > 0 ? round((($nomination['vote_count'] ?? 0) / $totalVotes) * 100) : 0; ?>
                                            <div class="result-item <?php echo $index < 3 ? 'top-' . ($index + 1) : ''; ?>">
                                                <div class="result-rank">
                                                    <?php if ($index === 0): ?>
                                                        <i class="fas fa-crown gold"></i>
                                                    <?php elseif ($index === 1): ?>
                                                        <i class="fas fa-award silver"></i>
                                                    <?php elseif ($index === 2): ?>
                                                        <i class="fas fa-award bronze"></i>
                                                    <?php else: ?>
                                                        <span class="rank-number">#<?php echo $index + 1; ?></span>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="result-content">
                                                    <h4><?php echo htmlspecialchars((string) $nomination['libelle']); ?></h4>
                                                    <div class="result-meta">
                                                        <span class="candidate">
                                                            <i class="fas fa-user"></i>
                                                            <?php echo htmlspecialchars((string) ($nomination['candidate_name'] ?? 'Candidat')); ?>
                                                        </span>
                                                        <span class="platform">
                                                            <i class="fab fa-<?php echo htmlspecialchars(strtolower((string) ($nomination['plateforme'] ?? 'users'))); ?>"></i>
                                                            <?php echo htmlspecialchars((string) ($nomination['plateforme'] ?? 'All')); ?>
                                                        </span>
                                                    </div>

                                                    <div class="result-bar">
                                                        <div class="bar-fill" style="width: <?php echo $percentage; ?>%;"></div>
                                                    </div>

                                                    <div class="result-stats">
                                                        <span class="vote-count">
                                                            <i class="fas fa-vote-yea"></i>
                                                            <?php echo htmlspecialchars((string) ($nomination['vote_count'] ?? 0)); ?> votes
                                                        </span>
                                                        <span class="vote-percentage">
                                                            <?php echo htmlspecialchars((string) $percentage); ?>%
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="results-actions">
                                        <a href="<?php echo htmlspecialchars($categoryVoteUrl ?? ($votePageUrl . '?category_id=' . $categoryId)); ?>" class="btn btn-outline">
                                            <i class="fas fa-vote-yea"></i>
                                            Retour au vote
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </section>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <div class="vote-confirm-modal" id="confirmModal">
        <div class="modal-content">
            <h3><i class="fas fa-question-circle"></i> Confirmer votre vote</h3>
            <p id="selectedNomineeName">Êtes-vous sûr de vouloir voter pour ce nominé?</p>
            <p class="text-muted">
                <i class="fas fa-exclamation-triangle"></i>
                Cette action est irréversible. Vous ne pourrez pas modifier votre vote.
            </p>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal()">
                    Annuler
                </button>
                <button type="button" class="btn btn-primary" onclick="submitVote()">
                    <i class="fas fa-check"></i>
                    Confirmer le vote
                </button>
            </div>
        </div>
    </div>

    <footer class="dashboard-footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="<?php echo htmlspecialchars($categoriesUrl); ?>">Catégories</a>
                <a href="<?php echo htmlspecialchars($nomineesUrl); ?>">Nominés</a>
                <a href="<?php echo htmlspecialchars($resultsUrl); ?>">Résultats</a>
                <a href="<?php echo htmlspecialchars($contactUrl); ?>">Contact</a>
                <a href="<?php echo htmlspecialchars($aboutUrl); ?>">À propos</a>
                <a href="<?php echo htmlspecialchars($faqUrl); ?>">FAQ</a>
            </div>
            <div class="copyright">
                &copy; <?php echo htmlspecialchars((string) $copyrightYear); ?> Social Media Awards. Tous droits réservés.
            </div>
        </div>
    </footer>

    <script>
        let selectedNominationId = <?php echo $preselectedNominationId !== null ? (int) $preselectedNominationId : 'null'; ?>;
        let selectedNominationName = <?php echo json_encode($preselectedNominationName, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

        function selectNomination(nominationId, nomineeName) {
            document.querySelectorAll('.nomination-card').forEach(card => {
                card.classList.remove('selected');
            });

            const card = document.querySelector(`input[value="${nominationId}"]`).closest('.nomination-card');
            if (card) {
                card.classList.add('selected');
                document.getElementById(`nomination_${nominationId}`).checked = true;
                selectedNominationId = nominationId;
                selectedNominationName = nomineeName;

                const submitBtn = document.getElementById('submitVoteBtn');
                submitBtn.disabled = false;

                const btnText = submitBtn.querySelector('.btn-text');
                const shortName = nomineeName.length > 30 ? nomineeName.substring(0, 30) + '...' : nomineeName;
                btnText.textContent = `Voter pour "${shortName}"`;
            }
        }

        function confirmVote() {
            if (!selectedNominationId) {
                showToast('Veuillez sélectionner un nominé avant de voter.', 'error');
                return;
            }

            document.getElementById('selectedNomineeName').textContent =
                `Êtes-vous sûr de vouloir voter pour "${selectedNominationName}"?`;

            document.getElementById('confirmModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }

        function submitVote() {
            const submitBtn = document.querySelector('.modal-actions .btn-primary');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
            submitBtn.disabled = true;

            setTimeout(() => {
                document.getElementById('voteForm').submit();
            }, 1000);
        }

        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        function showToast(message, type = 'info') {
            const existingToast = document.querySelector('.toast');
            if (existingToast) {
                existingToast.remove();
            }

            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;

            document.body.appendChild(toast);

            setTimeout(() => toast.classList.add('show'), 10);

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (selectedNominationId !== null) {
                selectNomination(selectedNominationId, selectedNominationName);
            }

            const alerts = document.querySelectorAll('.voting-alert, .already-voted-alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 300);
                }, 5000);
            });

            const url = new URL(window.location);
            if (url.searchParams.has('success') || url.searchParams.has('error')) {
                url.searchParams.delete('success');
                url.searchParams.delete('error');
                window.history.replaceState({}, '', url.toString());
            }

            setInterval(() => {
                fetch('<?php echo addslashes($checkSessionUrl); ?>')
                    .then(response => response.json())
                    .then(data => {
                        if (!data.authenticated) {
                            window.location.href = '<?php echo addslashes($loginUrl); ?>';
                        }
                    })
                    .catch(() => console.log('Erreur de vérification de session'));
            }, 300000);

            const barFills = document.querySelectorAll('.bar-fill');
            barFills.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>

</html>