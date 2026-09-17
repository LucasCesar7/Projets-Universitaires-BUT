<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/header.css')); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/footer.css')); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/results.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <title>
        <?php 
        switch ($status) {
            case 'voting_active': echo 'Votes en cours'; break;
            case 'voting_not_started': echo 'Votes à venir'; break;
            case 'voting_finished': echo "Résultats $editionYear"; break;
            case 'edition_inactive': echo "Édition $editionYear"; break;
            default: echo "Social Media Awards";
        }
        ?>
    </title>
    
    <style>
        /* Styles pour la sécurité des résultats */
        .security-warning {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.2);
            border-left: 5px solid #c0392b;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 107, 107, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(255, 107, 107, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 107, 107, 0); }
        }
        
        .security-warning i {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
        }
        
        .voting-period-box {
            background: linear-gradient(135deg, #4FBDAB, #3da895);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }
        
        .vote-countdown {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .results-locked {
            text-align: center;
            padding: 50px 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 30px 0;
            border: 2px dashed #dee2e6;
        }
        
        .results-locked i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 20px;
        }
        
        /* Styles pour les résultats quand disponibles */
        .winner-card {
            transition: transform 0.3s ease;
        }
        
        .winner-card:hover {
            transform: translateY(-5px);
        }

        .pagination-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin: 18px 0 20px;
            flex-wrap: wrap;
        }

        .pagination-meta {
            color: var(--gray);
            font-size: 0.95rem;
        }

        .pagination-size-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pagination-size-form select {
            padding: 8px 10px;
            border: 1px solid #d8d8d8;
            border-radius: 8px;
            background: #fff;
        }

        .pagination-nav {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .pagination-nav a,
        .pagination-nav span {
            min-width: 38px;
            height: 38px;
            padding: 0 10px;
            border-radius: 8px;
            border: 1px solid #d8d8d8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--dark);
            background: #fff;
            font-weight: 600;
        }

        .pagination-nav .active {
            border-color: var(--principal);
            background: var(--principal);
            color: #fff;
        }

        .pagination-nav .disabled {
            opacity: 0.45;
        }
    </style>
</head>
<body class="status-<?php echo $status; ?>">

<?php require appPath('views/partials/header.php'); ?>

<div class="main-content">
    <!-- SECTION HERO -->
    <section class="results-hero">
        <div class="global-container">
            <h1>
                <?php 
                switch ($status) {
                    case 'voting_active': 
                        echo '<i class="fas fa-vote-yea"></i> Votes en cours';
                        break;
                    case 'voting_not_started': 
                        echo '<i class="fas fa-clock"></i> Votes à venir';
                        break;
                    case 'voting_finished': 
                        echo '<i class="fas fa-trophy"></i> Résultats ' . htmlspecialchars($editionYear);
                        break;
                    case 'edition_inactive':
                        echo '<i class="fas fa-info-circle"></i> Édition ' . htmlspecialchars($editionYear);
                        break;
                    default:
                        echo '<i class="fas fa-trophy"></i> Social Media Awards';
                }
                ?>
            </h1>
            
            <p class="subtitle">
                <?php 
                switch ($status) {
                    case 'voting_active':
                        echo 'Les votes sont actuellement ouverts. Les résultats seront disponibles après la clôture.';
                        break;
                    case 'voting_not_started':
                        echo 'Les votes commenceront bientôt. Préparez-vous à participer!';
                        break;
                    case 'voting_finished':
                        echo 'Découvrez les gagnants officiels de cette édition';
                        break;
                    case 'edition_inactive':
                        echo 'Cette édition est actuellement inactive';
                        break;
                    default:
                        echo 'Informations sur les Social Media Awards';
                }
                ?>
            </p>
            
            <!-- Dates importantes -->
            <?php if ($dateDebut || $dateFin): ?>
            <div class="edition-dates">
                <div class="date-card">
                    <i class="fas fa-calendar-plus"></i>
                    <div>
                        <small>Début</small>
                        <div><?php echo $dateDebut ? date('d/m/Y à H:i', strtotime($dateDebut)) : 'Non définie'; ?></div>
                    </div>
                </div>
                
                <div class="date-card">
                    <i class="fas fa-calendar-check"></i>
                    <div>
                        <small>Fin</small>
                        <div><?php echo $dateFin ? date('d/m/Y à H:i', strtotime($dateFin)) : 'Non définie'; ?></div>
                    </div>
                </div>
                
                <div class="date-card">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <small>Statut</small>
                        <div class="status-badge status-<?php echo $status; ?>">
                            <?php 
                            switch ($status) {
                                case 'voting_active': echo 'En cours'; break;
                                case 'voting_not_started': echo 'À venir'; break;
                                case 'voting_finished': echo 'Terminé'; break;
                                case 'edition_inactive': echo 'Inactive'; break;
                                default: echo 'Inconnu';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- AVERTISSEMENT SI VOTES EN COURS -->
            <?php if ($showWarning): ?>
            <div class="security-warning" id="securityWarning">
                <i class="fas fa-lock"></i>
                <h2>Résultats temporairement indisponibles</h2>
                <p>Pour garantir l'intégrité du vote, les résultats ne sont pas accessibles pendant la période de vote.</p>
                <p>Ils seront dévoilés automatiquement après la clôture des votes.</p>
                
                <?php if ($dateFin): ?>
                <div class="vote-countdown">
                    <i class="fas fa-hourglass-half"></i>
                    Fin des votes: <?php echo date('d/m/Y à H:i', strtotime($dateFin)); ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Sélecteur d'édition -->
            <div class="edition-selector">
                <label for="editionSelect">
                    <i class="fas fa-calendar-alt"></i> Choisir une édition:
                </label>
                <select id="editionSelect">
                    <?php foreach ($availableEditions as $ed): 
                        $isCurrent = ($ed['id_edition'] == $editionId);
                        $isFinished = $ed['is_finished'] ?? false;
                        $isActive = $ed['is_active'] ?? false;
                        $notStarted = $ed['not_started'] ?? false;
                    ?>
                    <option value="<?php echo $ed['id_edition']; ?>" 
                            <?php echo $isCurrent ? 'selected' : ''; ?>
                            data-status="<?php echo $isActive ? 'active' : ($isFinished ? 'finished' : ($notStarted ? 'not_started' : 'inactive')); ?>">
                        <?php echo htmlspecialchars($ed['annee']); ?> - <?php echo htmlspecialchars($ed['nom']); ?>
                        <?php if ($isActive): ?> (Votes en cours)<?php endif; ?>
                        <?php if ($isFinished): ?> (Terminée)<?php endif; ?>
                        <?php if ($notStarted): ?> (À venir)<?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="button" id="changeEditionBtn" class="btn-small">
                    <i class="fas fa-sync-alt"></i> Voir
                </button>
            </div>
        </div>
    </section>

    <!-- SECTION AVANT LES RÉSULTATS (quand votes en cours) -->
    <?php if (!$canShowResults && $status === 'voting_active'): ?>
    <section class="before-results">
        <div class="global-container">
            <div class="results-locked">
                <i class="fas fa-lock fa-4x"></i>
                <h2>Les résultats sont sécurisés</h2>
                <p>Pour maintenir l'équité et la transparence du processus, les résultats ne sont pas accessibles pendant la période de vote.</p>
                
                <div class="call-to-action">
                    <h3>Vous voulez participer?</h3>
                    <p>Votez pour vos favoris avant la fin de la période!</p>
                    <a href="<?php echo htmlspecialchars($votePageUrl); ?>" class="btn-primary">
                        <i class="fas fa-vote-yea"></i> Voter maintenant
                    </a>
                </div>
                
                <?php if ($dateFin): ?>
                <div class="countdown-container">
                    <h4>Temps restant pour voter:</h4>
                    <div id="liveCountdown" class="live-countdown">
                        <span id="countdownDays">--</span> jours
                        <span id="countdownHours">--</span> heures
                        <span id="countdownMinutes">--</span> minutes
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Statistiques "safe" (sans révéler les gagnants) -->
            <div class="safe-stats">
                <h3><i class="fas fa-chart-line"></i> Participation en temps réel</h3>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo number_format($globalStats['total_voters']); ?></div>
                            <div class="stat-label">Participants</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo $globalStats['total_categories']; ?></div>
                            <div class="stat-label">Catégories</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo $globalStats['total_nominations']; ?></div>
                            <div class="stat-label">Candidats</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">En cours</div>
                            <div class="stat-label">Votes actifs</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- SECTION RÉSULTATS (SEULEMENT SI AUTORISÉ) -->
    <?php if ($canShowResults): ?>
    <section class="results-section" id="officialResults">
        <div class="global-container">
            <div class="results-header">
                <h2><i class="fas fa-medal"></i> Palmarès officiel - Édition <?php echo htmlspecialchars($editionYear); ?></h2>
                <p class="results-announcement">
                    <i class="fas fa-bullhorn"></i> 
                    Les résultats ont été officiellement validés après la clôture des votes.
                </p>
                <div class="results-timestamp">
                    <i class="fas fa-calendar-check"></i>
                    Date de publication: <?php echo date('d/m/Y à H:i'); ?>
                </div>
            </div>
            
            <!-- Grands gagnants -->
            <?php if (!empty($grandWinners)): ?>
            <div class="winners-showcase">
                <h3><i class="fas fa-crown"></i> Grands Gagnants</h3>
                <div class="winners-grid">
                    <?php foreach ($grandWinners as $winner): 
                        $rank = $winner['rang'] ?? 1;
                        $rankClass = ($rank == 1) ? 'gold' : (($rank == 2) ? 'silver' : 'bronze');
                        $winnerImageUrl = resolveAppAssetUrl($winner['image'] ?? null, $winner['fallback_image'] ?? 'assets/images/Winners/winner1.jpg');
                    ?>
                    <div class="winner-card <?php echo $rankClass; ?>">
                        <div class="winner-rank rank-<?php echo $rank; ?>">
                            <?php if ($rank == 1): ?>
                                <i class="fas fa-crown"></i>
                            <?php else: ?>
                                <?php echo $rank; ?><sup><?php echo ($rank == 1) ? 'ère' : 'ème'; ?></sup>
                            <?php endif; ?>
                        </div>
                        <div class="winner-image">
                            <?php if ($winnerImageUrl !== null): ?>
                                <img loading="lazy" src="<?php echo htmlspecialchars($winnerImageUrl); ?>" 
                                     alt="<?php echo htmlspecialchars($winner['nom_nomination']); ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="default-winner-img">
                                    <i class="fas fa-trophy"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="winner-info">
                            <h4><?php echo htmlspecialchars($winner['nom_nomination']); ?></h4>
                            <p class="winner-category">
                                <i class="fas fa-tag"></i>
                                <?php echo htmlspecialchars($winner['categorie'] ?? 'Catégorie'); ?>
                            </p>
                            <div class="winner-stats">
                                <span class="stat-votes">
                                    <i class="fas fa-vote-yea"></i>
                                    <?php echo number_format($winner['total_votes'] ?? 0); ?> votes
                                </span>
                                <?php if (!empty($winner['plateforme'])): ?>
                                <span class="stat-platform">
                                    <i class="fab fa-<?php echo strtolower($winner['plateforme']); ?>"></i>
                                    <?php echo htmlspecialchars($winner['plateforme']); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="no-results">
                <i class="fas fa-info-circle"></i>
                <h3>Aucun gagnant enregistré</h3>
                <p>Il n'y a pas encore de résultats disponibles pour cette édition.</p>
            </div>
            <?php endif; ?>
            
            <!-- Résultats par catégorie -->
            <?php if (!empty($categoryResults)): ?>
            <div class="category-results">
                <h3><i class="fas fa-list-ol"></i> Résultats par Catégorie</h3>

                <div class="pagination-toolbar">
                    <div class="pagination-meta">
                        <?php if (($pagination['totalItems'] ?? 0) > 0): ?>
                            Affichage de <?php echo htmlspecialchars((string) $pagination['startItem'], ENT_QUOTES, 'UTF-8'); ?>
                            à <?php echo htmlspecialchars((string) $pagination['endItem'], ENT_QUOTES, 'UTF-8'); ?>
                            sur <?php echo htmlspecialchars((string) $pagination['totalItems'], ENT_QUOTES, 'UTF-8'); ?> catégories
                        <?php else: ?>
                            Aucune catégorie à afficher
                        <?php endif; ?>
                    </div>

                    <form method="get" class="pagination-size-form">
                        <input type="hidden" name="page" value="1">
                        <input type="hidden" name="edition" value="<?php echo htmlspecialchars((string) $editionId, ENT_QUOTES, 'UTF-8'); ?>">
                        <label for="per_page">Par page :</label>
                        <select id="per_page" name="per_page" onchange="this.form.submit()">
                            <?php foreach (($pagination['perPageOptions'] ?? [6, 12, 24, 48]) as $option): ?>
                                <option value="<?php echo (int) $option; ?>" <?php echo ((int) ($pagination['perPage'] ?? 12) === (int) $option) ? 'selected' : ''; ?>>
                                    <?php echo (int) $option; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                
                <!-- Filtres de plateforme -->
                <div class="category-filters">
                    <button class="filter-btn active" data-filter="all">Toutes</button>
                    <?php 
                    // Obter plateformes únicas
                    $platforms = [];
                    foreach ($categoryResults as $cat) {
                        if (!empty($cat['plateforme'])) {
                            $platforms[$cat['plateforme']] = true;
                        }
                    }
                    ksort($platforms);
                    ?>
                    <?php foreach (array_keys($platforms) as $platform): ?>
                    <button class="filter-btn" data-filter="<?php echo strtolower($platform); ?>">
                        <?php echo htmlspecialchars($platform); ?>
                    </button>
                    <?php endforeach; ?>
                </div>
                
                <div class="categories-grid">
                    <?php foreach ($categoryResults as $category): ?>
                    <div class="category-result-card" 
                         data-platform="<?php echo strtolower($category['plateforme'] ?? 'all'); ?>">
                        <div class="category-header">
                            <h4><?php echo htmlspecialchars($category['categorie_nom']); ?></h4>
                            <?php if (!empty($category['plateforme'])): ?>
                            <span class="platform-badge <?php echo strtolower($category['plateforme']); ?>">
                                <i class="fab fa-<?php echo strtolower($category['plateforme']); ?>"></i>
                                <?php echo htmlspecialchars($category['plateforme']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($category['winners'])): ?>
                        <div class="category-winners">
                            <?php foreach ($category['winners'] as $winner): ?>
                            <div class="category-winner position-<?php echo $winner['position']; ?>">
                                <span class="winner-medal"><?php echo $winner['medal']; ?></span>
                                <span class="winner-name">
                                    <?php echo htmlspecialchars($winner['nom_nomination']); ?>
                                </span>
                                <div class="winner-details">
                                    <span class="winner-votes">
                                        <i class="fas fa-chart-bar"></i>
                                        <?php echo number_format($winner['vote_count']); ?> votes
                                    </span>
                                    <span class="winner-percentage">
                                        <?php echo $winner['vote_percentage']; ?>%
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="no-winners">Aucun vote enregistré dans cette catégorie</p>
                        <?php endif; ?>
                        
                        <div class="category-total">
                            <i class="fas fa-calculator"></i>
                            Total: <?php echo number_format($category['total_votes_categorie'] ?? 0); ?> votes
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php
                $currentPage = (int) ($pagination['page'] ?? 1);
                $totalPages = (int) ($pagination['totalPages'] ?? 1);
                $perPage = (int) ($pagination['perPage'] ?? 12);
                ?>
                <?php if ($totalPages > 1): ?>
                    <nav class="pagination-nav" aria-label="Pagination des résultats par catégorie">
                        <?php if ($currentPage > 1): ?>
                            <?php $previousQuery = ['edition' => $editionId, 'per_page' => $perPage, 'page' => $currentPage - 1]; ?>
                            <a href="?<?php echo htmlspecialchars(http_build_query($previousQuery), ENT_QUOTES, 'UTF-8'); ?>" aria-label="Page précédente">&laquo;</a>
                        <?php else: ?>
                            <span class="disabled" aria-hidden="true">&laquo;</span>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <?php if ($p === $currentPage): ?>
                                <span class="active"><?php echo $p; ?></span>
                            <?php else: ?>
                                <?php $pageQuery = ['edition' => $editionId, 'per_page' => $perPage, 'page' => $p]; ?>
                                <a href="?<?php echo htmlspecialchars(http_build_query($pageQuery), ENT_QUOTES, 'UTF-8'); ?>"><?php echo $p; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <?php $nextQuery = ['edition' => $editionId, 'per_page' => $perPage, 'page' => $currentPage + 1]; ?>
                            <a href="?<?php echo htmlspecialchars(http_build_query($nextQuery), ENT_QUOTES, 'UTF-8'); ?>" aria-label="Page suivante">&raquo;</a>
                        <?php else: ?>
                            <span class="disabled" aria-hidden="true">&raquo;</span>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Statistiques détaillées -->
            <div class="detailed-stats">
                <h3><i class="fas fa-chart-pie"></i> Statistiques détaillées</h3>
                <div class="stats-grid detailed">
                    <div class="stat-card large">
                        <div class="stat-icon">
                            <i class="fas fa-vote-yea"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo number_format($globalStats['total_votes']); ?></div>
                            <div class="stat-label">Votes Totaux</div>
                            <div class="stat-desc">Nombre total de votes enregistrés</div>
                        </div>
                    </div>
                    
                    <div class="stat-card large">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo number_format($globalStats['total_voters']); ?></div>
                            <div class="stat-label">Électeurs</div>
                            <div class="stat-desc">Participants uniques</div>
                        </div>
                    </div>
                    
                    <div class="stat-card large">
                        <div class="stat-icon">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo $globalStats['participation_rate']; ?>%</div>
                            <div class="stat-label">Taux de Participation</div>
                            <div class="stat-desc">Pourcentage d'électeurs actifs</div>
                        </div>
                    </div>
                    
                    <div class="stat-card large">
                        <div class="stat-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo $globalStats['total_categories']; ?></div>
                            <div class="stat-label">Catégories</div>
                            <div class="stat-desc">Nombre de catégories actives</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php elseif ($status === 'voting_not_started'): ?>
    <!-- Message pour votes pas encore commencés -->
    <section class="not-started-section">
        <div class="global-container">
            <div class="coming-soon">
                <i class="fas fa-hourglass-start fa-4x"></i>
                <h2>Les votes n'ont pas encore commencé</h2>
                <p>L'édition <?php echo htmlspecialchars($editionYear); ?> est en préparation.</p>
                
                <?php if ($dateDebut): ?>
                <div class="start-date">
                    <i class="fas fa-calendar-day"></i>
                    <h3>Date d'ouverture des votes:</h3>
                    <div class="date-display">
                        <?php echo date('d/m/Y à H:i', strtotime($dateDebut)); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="preparation-info">
                    <h3><i class="fas fa-info-circle"></i> En attendant...</h3>
                    <ul>
                        <li><i class="fas fa-check-circle"></i> Les catégories sont en cours de finalisation</li>
                        <li><i class="fas fa-check-circle"></i> Les candidats sont en sélection</li>
                        <li><i class="fas fa-check-circle"></i> Le système de vote est en test</li>
                        <li><i class="fas fa-check-circle"></i> Tout sera prêt pour la date d'ouverture!</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- SECTION STATISTIQUES GÉNÉRALES (toujours visible) -->
    <section class="general-statistics">
        <div class="global-container">
            <h2><i class="fas fa-chart-line"></i> Vue d'ensemble</h2>
            <div class="stats-grid">
                <?php if ($status === 'voting_active'): ?>
                <!-- Pendant les votes -->
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">En cours</div>
                        <div class="stat-label">Période de vote active</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">Participez!</div>
                        <div class="stat-label">Votez pour vos favoris</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">Sécurisé</div>
                        <div class="stat-label">Vote anonyme et transparent</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">Édition <?php echo $editionYear; ?></div>
                        <div class="stat-label">Social Media Awards</div>
                    </div>
                </div>
                
                <?php elseif ($status === 'voting_finished'): ?>
                <!-- Après les votes -->
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">Terminé</div>
                        <div class="stat-label">Votes clôturés</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo number_format($globalStats['total_voters']); ?></div>
                        <div class="stat-label">Participants</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-vote-yea"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo number_format($globalStats['total_votes']); ?></div>
                        <div class="stat-label">Votes</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $globalStats['participation_rate']; ?>%</div>
                        <div class="stat-label">Participation</div>
                    </div>
                </div>
                
                <?php else: ?>
                <!-- Avant les votes -->
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">À venir</div>
                        <div class="stat-label">Bientôt</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">Préparez-vous</div>
                        <div class="stat-label">Votez bientôt</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">Informations</div>
                        <div class="stat-label">À suivre</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">Édition <?php echo $editionYear; ?></div>
                        <div class="stat-label">Social Media Awards</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php require appPath('views/partials/footer.php'); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du sélecteur d'édition
    const editionSelect = document.getElementById('editionSelect');
    const changeBtn = document.getElementById('changeEditionBtn');
    
    if (editionSelect && changeBtn) {
        changeBtn.addEventListener('click', function() {
            const selectedValue = editionSelect.value;
            const selectedOption = editionSelect.options[editionSelect.selectedIndex];
            const status = selectedOption.getAttribute('data-status');
            const currentParams = new URLSearchParams(window.location.search);
            const currentPerPage = currentParams.get('per_page');
            
            // Avertissement selon le statut
            if (status === 'active') {
                if (!confirm("Cette édition est en cours de vote. Les résultats ne seront visibles qu'après la clôture. Voulez-vous continuer?")) {
                    return;
                }
            } else if (status === 'not_started') {
                if (!confirm("Les votes de cette édition n'ont pas encore commencé. Voulez-vous continuer?")) {
                    return;
                }
            }
            
            const nextUrl = new URL('results', window.location.href);
            nextUrl.searchParams.set('edition', selectedValue);
            if (currentPerPage && !Number.isNaN(Number(currentPerPage))) {
                nextUrl.searchParams.set('per_page', currentPerPage);
            }
            window.location.href = nextUrl.toString();
        });
        
        editionSelect.addEventListener('change', function() {
            changeBtn.style.display = 'inline-block';
        });
    }
    
    // Filtres des catégories (si résultats disponibles)
    const filterButtons = document.querySelectorAll('.filter-btn');
    if (filterButtons.length > 0) {
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Retirer la classe active de tous les boutons
                filterButtons.forEach(b => b.classList.remove('active'));
                // Ajouter la classe active au bouton cliqué
                this.classList.add('active');
                
                const filterValue = this.getAttribute('data-filter');
                const cards = document.querySelectorAll('.category-result-card');
                
                cards.forEach(card => {
                    const cardPlatform = card.getAttribute('data-platform');
                    if (filterValue === 'all' || cardPlatform === filterValue) {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 10);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(10px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });
    }
    
    // Compteur à rebours pour les votes en cours
    <?php if ($status === 'voting_active' && $dateFin): ?>
    function updateLiveCountdown() {
        const endDate = new Date("<?php echo $dateFin; ?>".replace(' ', 'T'));
        const now = new Date();
        const distance = endDate - now;
        
        if (distance < 0) {
            // Temps écoulé, recharger la page
            window.location.reload();
            return;
        }
        
        // Calculer le temps restant
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Mettre à jour l'affichage
        const daysEl = document.getElementById('countdownDays');
        const hoursEl = document.getElementById('countdownHours');
        const minutesEl = document.getElementById('countdownMinutes');
        
        if (daysEl) daysEl.textContent = days;
        if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
        if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
        
        // Changer la couleur si moins de 24h
        if (days === 0 && hours < 24) {
            const container = document.querySelector('.live-countdown');
            if (container) {
                container.style.color = '#ff6b6b';
                container.style.fontWeight = 'bold';
            }
        }
    }
    
    updateLiveCountdown();
    const countdownInterval = setInterval(updateLiveCountdown, 1000);
    <?php endif; ?>
    
    // Animation des cartes de résultats
    const resultCards = document.querySelectorAll('.winner-card, .category-result-card');
    resultCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Message de sécurité en surbrillance
    const securityWarning = document.getElementById('securityWarning');
    if (securityWarning) {
        setInterval(() => {
            securityWarning.style.transform = securityWarning.style.transform === 'scale(1.02)' ? 'scale(1)' : 'scale(1.02)';
        }, 2000);
    }
});
</script>
</body>
</html>