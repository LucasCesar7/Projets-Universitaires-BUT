<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Social Media Awards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($candidateCssUrl); ?>">
    <style>
        .opportunity-alert {
            background: linear-gradient(135deg, #f8f9ff 0%, #eef1ff 100%);
            border-left: 4px solid #4361ee;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .opportunity-icon {
            color: #4361ee;
        }

        .available-categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .category-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .category-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
        }

        .category-edition {
            background: #4361ee;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .category-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .category-details {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: #777;
            margin-bottom: 15px;
        }

        .deadline-warning {
            color: #dc3545;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .modal-categories .modal-dialog {
            max-width: 900px;
        }

        .modal-categories .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo htmlspecialchars($homeUrl); ?>">
                <i class="fas fa-trophy me-2"></i>Social Media Awards
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="<?php echo htmlspecialchars($candidateDashboardUrl); ?>">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                    <span class="navbar-text me-3">
                        <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($userPseudonyme); ?>
                    </span>
                    <form method="post" action="<?php echo htmlspecialchars($logoutUrl); ?>" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($logoutToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="nav-link" style="border: none; background: none;">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="sidebar-container">
                    <?php include __DIR__ . '/../partials/sidebar-candidat.php'; ?>
                </div>
            </div>

            <div class="col-md-9">
                <div class="main-content fade-in">
                    <div class="page-header mb-4">
                        <h1 class="page-title">
                            <?php if ($isNominee): ?>
                                <i class="fas fa-trophy me-2"></i>Tableau de bord Nominé
                            <?php else: ?>
                                <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord Candidat
                            <?php endif; ?>
                        </h1>
                        <p class="page-subtitle">Bonjour, <?php echo htmlspecialchars($userPseudonyme); ?> !</p>
                    </div>

                    <?php if ($successFlash): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars((string) $successFlash); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($errorFlash): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars((string) $errorFlash); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($isNominee): ?>
                        <div class="nominee-hero mb-4">
                            <div class="nominee-content">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <span class="nominee-badge">
                                        <i class="fas fa-trophy me-2"></i>NOMINÉ(E) OFFICIEL
                                    </span>
                                    <?php if ($votingStatus === 'in_progress'): ?>
                                        <span class="badge bg-success pulse">
                                            <i class="fas fa-vote-yea me-1"></i> VOTES EN COURS
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <h2>Félicitations ! Vous êtes officiellement nominé(e)</h2>
                                <p>Votre contenu a été sélectionné par notre jury. Vous participez maintenant à la phase de votes.</p>
                            </div>
                            <div class="nominee-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                        </div>

                        <?php foreach ($nominations as $nomination): ?>
                            <?php
                            $startDate = new DateTime($nomination['date_debut_votes'] ?? $nomination['date_debut']);
                            $endDate = new DateTime($nomination['date_fin_votes'] ?? $nomination['date_fin']);
                            $now = new DateTime();
                            ?>
                            <div class="main-card nomination-card mb-4">
                                <div class="card-header">
                                    <div class="nomination-header">
                                        <h5 class="nomination-title">
                                            <i class="fas fa-medal me-2"></i>
                                            Votre nomination officielle
                                        </h5>
                                        <span class="status-badge <?php echo htmlspecialchars($nomination['dashboard_status_class']); ?>">
                                            <?php echo htmlspecialchars($nomination['dashboard_status_label']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="nomination-details">
                                        <div>
                                            <h5 class="mb-3"><?php echo htmlspecialchars($nomination['libelle'] ?? 'Titre non disponible'); ?></h5>
                                            <p class="text-muted mb-4">
                                                <?php if (isset($nomination['categorie_nom'])): ?>
                                                    <strong><i class="fas fa-tag me-1"></i>Catégorie:</strong>
                                                    <?php echo htmlspecialchars($nomination['categorie_nom']); ?><br>
                                                <?php endif; ?>
                                                <?php if (isset($nomination['edition_nom'])): ?>
                                                    <strong><i class="fas fa-calendar-alt me-1"></i>Édition:</strong>
                                                    <?php echo htmlspecialchars($nomination['edition_nom']); ?><br>
                                                <?php endif; ?>
                                                <?php if (isset($nomination['plateforme'])): ?>
                                                    <strong><i class="fas fa-share-alt me-1"></i>Plateforme:</strong>
                                                    <?php echo htmlspecialchars($nomination['plateforme']); ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>

                                        <div>
                                            <?php if (($nomination['dashboard_status'] ?? 'not_started') === 'in_progress'): ?>
                                                <?php $interval = $now->diff($endDate); ?>
                                                <div class="countdown-container">
                                                    <h6 class="countdown-title">Temps restant pour voter</h6>
                                                    <div class="countdown-timer" data-countdown="<?php echo $endDate->format('c'); ?>">
                                                        <div class="countdown-unit">
                                                            <span class="countdown-number countdown-days"><?php echo $interval->days; ?></span>
                                                            <span class="countdown-label">jours</span>
                                                        </div>
                                                        <div class="countdown-unit">
                                                            <span class="countdown-number countdown-hours"><?php echo str_pad((string) $interval->h, 2, '0', STR_PAD_LEFT); ?></span>
                                                            <span class="countdown-label">heures</span>
                                                        </div>
                                                        <div class="countdown-unit">
                                                            <span class="countdown-number countdown-minutes"><?php echo str_pad((string) $interval->i, 2, '0', STR_PAD_LEFT); ?></span>
                                                            <span class="countdown-label">min</span>
                                                        </div>
                                                        <div class="countdown-unit">
                                                            <span class="countdown-number countdown-seconds"><?php echo str_pad((string) $interval->s, 2, '0', STR_PAD_LEFT); ?></span>
                                                            <span class="countdown-label">sec</span>
                                                        </div>
                                                    </div>
                                                    <p class="small text-muted mt-2">avant la fin des votes</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="features-grid mb-4">
                            <div class="feature-card hover-lift">
                                <div class="feature-icon">
                                    <i class="fas fa-id-badge"></i>
                                </div>
                                <h5>Profil Public</h5>
                                <p>Votre profil visible par les votants</p>
                                <a href="<?php echo htmlspecialchars($nomineeProfileUrl); ?>" class="btn btn-primary">Voir mon profil</a>
                            </div>

                            <div class="feature-card hover-lift">
                                <div class="feature-icon">
                                    <i class="fas fa-share-alt"></i>
                                </div>
                                <h5>Partager</h5>
                                <p>Partagez votre nomination sur les réseaux</p>
                                <a href="<?php echo htmlspecialchars($shareNominationUrl); ?>" class="btn btn-success">Partager</a>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="stats-grid mb-4">
                            <div class="stat-card primary hover-lift">
                                <div class="stat-content">
                                    <div class="stat-info">
                                        <h6>Candidatures</h6>
                                        <div class="stat-number" data-count="<?php echo htmlspecialchars((string) ($stats['total'] ?? 0)); ?>">0</div>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card warning hover-lift">
                                <div class="stat-content">
                                    <div class="stat-info">
                                        <h6>En attente</h6>
                                        <div class="stat-number" data-count="<?php echo htmlspecialchars((string) ($stats['pending'] ?? 0)); ?>">0</div>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card success hover-lift">
                                <div class="stat-content">
                                    <div class="stat-info">
                                        <h6>Approuvées</h6>
                                        <div class="stat-number" data-count="<?php echo htmlspecialchars((string) ($stats['approved'] ?? 0)); ?>">0</div>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card danger hover-lift">
                                <div class="stat-content">
                                    <div class="stat-info">
                                        <h6>Rejetées</h6>
                                        <div class="stat-number" data-count="<?php echo htmlspecialchars((string) ($stats['rejected'] ?? 0)); ?>">0</div>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!$hasOpenSubmissionWindow): ?>
                            <div class="alert alert-info mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle fa-2x me-3 text-info"></i>
                                    <div>
                                        <h5 class="mb-1">Aucun appel ouvert pour le moment</h5>
                                        <p class="mb-0">Les prochaines candidatures seront disponibles dès l'ouverture d'une nouvelle édition.</p>
                                    </div>
                                </div>
                            </div>
                        <?php elseif ($availableCount > 0): ?>
                            <div class="opportunity-alert">
                                <div class="d-flex align-items-start">
                                    <div class="opportunity-icon me-3">
                                        <i class="fas fa-bullhorn fa-2x"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <h5 class="mb-2"><?php echo htmlspecialchars((string) $availableCount); ?> nouvelle(s) catégorie(s) disponible(s)</h5>
                                        <p class="mb-3">
                                            Il vous reste <?php echo htmlspecialchars((string) $availableCount); ?> catégorie(s) où vous pouvez encore soumettre une candidature.
                                            Ne manquez pas ces opportunités !
                                        </p>

                                        <div class="d-flex gap-3 mb-3">
                                            <a href="<?php echo htmlspecialchars($submitCandidatureUrl); ?>" class="btn btn-primary">
                                                <i class="fas fa-paper-plane me-1"></i> Soumettre une candidature
                                            </a>
                                        </div>

                                        <?php if (count($availableCategories) > 0): ?>
                                            <?php $displayCategories = array_slice($availableCategories, 0, 3); ?>
                                            <div class="available-categories-grid">
                                                <?php foreach ($displayCategories as $category): ?>
                                                    <?php
                                                    $now = new DateTime();
                                                    if (!empty($category['date_fin_candidatures'])) {
                                                        try {
                                                            $deadline = new DateTime($category['date_fin_candidatures']);
                                                            $daysLeft = $now->diff($deadline)->days;
                                                        } catch (Exception $e) {
                                                            $daysLeft = 0;
                                                            $deadline = new DateTime();
                                                        }
                                                    } else {
                                                        $daysLeft = 0;
                                                        $deadline = new DateTime();
                                                    }
                                                    ?>
                                                    <div class="category-card">
                                                        <div class="category-header">
                                                            <h6 class="category-title"><?php echo htmlspecialchars($category['nom']); ?></h6>
                                                            <span class="category-edition"><?php echo htmlspecialchars($category['edition_nom']); ?></span>
                                                        </div>

                                                        <?php if (!empty($category['description'])): ?>
                                                            <p class="category-description">
                                                                <?php echo htmlspecialchars(substr($category['description'], 0, 100)); ?>
                                                                <?php echo strlen($category['description']) > 100 ? '...' : ''; ?>
                                                            </p>
                                                        <?php endif; ?>

                                                        <div class="category-details">
                                                            <div>
                                                                <?php if (!empty($category['plateforme_cible'])): ?>
                                                                    <div><i class="fas fa-globe me-1"></i> <?php echo htmlspecialchars($category['plateforme_cible']); ?></div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="text-end">
                                                                <div><i class="fas fa-clock me-1"></i> J-<?php echo $daysLeft; ?></div>
                                                                <div class="deadline-warning">
                                                                    <?php echo $deadline->format('d/m/Y'); ?>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <a href="<?php echo htmlspecialchars($submitCandidatureUrl . '?categorie=' . $category['id_categorie']); ?>"
                                                            class="btn btn-primary btn-sm w-100">
                                                            <i class="fas fa-paper-plane me-1"></i> Candidater
                                                        </a>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <?php if (count($availableCategories) > 3): ?>
                                                <div class="text-center mt-3">
                                                    <button type="button" class="btn btn-outline-primary"
                                                        data-bs-toggle="modal" data-bs-target="#categoriesModal">
                                                        <i class="fas fa-arrow-right me-1"></i>
                                                        Voir les <?php echo count($availableCategories) - 3; ?> autres catégories
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                                    <div>
                                        <h5 class="mb-1">Excellent travail !</h5>
                                        <p class="mb-0">Vous avez déjà soumis des candidatures pour toutes les catégories disponibles.</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="main-card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-history me-2"></i>
                                    Vos candidatures récentes
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recentCandidatures)): ?>
                                    <div class="text-center py-4">
                                        <div class="feature-icon mb-3">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <h5>Vous n'avez pas encore soumis de candidature</h5>
                                        <p class="text-muted mb-4">
                                            Commencez par soumettre votre première candidature pour participer aux Social Media Awards.
                                        </p>
                                        <?php if ($hasOpenSubmissionWindow): ?>
                                            <a href="<?php echo htmlspecialchars($submitCandidatureUrl); ?>" class="btn btn-primary btn-lg">
                                                <i class="fas fa-paper-plane me-2"></i> Soumettre une candidature
                                            </a>
                                        <?php else: ?>
                                            <p class="text-muted mb-0">Aucune édition n'accepte actuellement de nouvelles candidatures.</p>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="table-container">
                                        <table class="table table-candidatures">
                                            <thead>
                                                <tr>
                                                    <th>Titre</th>
                                                    <th>Catégorie</th>
                                                    <th>Date</th>
                                                    <th>Statut</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recentCandidatures as $cand): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars(substr($cand['libelle'], 0, 40)); ?><?php echo strlen($cand['libelle']) > 40 ? '...' : ''; ?></strong><br>
                                                            <small class="text-muted"><?php echo htmlspecialchars($cand['plateforme'] ?? ''); ?></small>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($cand['categorie_nom'] ?? 'N/A'); ?></td>
                                                        <td>
                                                            <?php echo date('d/m/Y', strtotime($cand['date_soumission'])); ?><br>
                                                            <small class="text-muted"><?php echo date('H:i', strtotime($cand['date_soumission'])); ?></small>
                                                        </td>
                                                        <td>
                                                            <span class="badge <?php echo $cand['statut'] === 'Approuvée' ? 'bg-success' : ($cand['statut'] === 'Rejetée' ? 'bg-danger' : 'bg-warning'); ?>">
                                                                <?php echo htmlspecialchars($cand['statut']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="<?php echo htmlspecialchars($candidatureDetailsBaseUrl . '?id=' . $cand['id_candidature']); ?>"
                                                                class="btn btn-sm btn-outline-primary"
                                                                data-bs-toggle="tooltip"
                                                                title="Voir les détails">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="<?php echo htmlspecialchars($mesCandidaturesUrl); ?>" class="btn btn-outline-secondary">
                                            Voir toutes les candidatures
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($activeEditions)): ?>
                            <div class="main-card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bullhorn me-2"></i>
                                        Appels à candidatures ouverts
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="features-grid">
                                        <?php foreach ($activeEditions as $edition): ?>
                                            <?php
                                            $dateFin = new DateTime($edition['date_fin_candidatures']);
                                            $now = new DateTime();
                                            $interval = $now->diff($dateFin);
                                            ?>
                                            <div class="feature-card hover-lift">
                                                <div class="feature-icon">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </div>
                                                <h5><?php echo htmlspecialchars($edition['nom']); ?></h5>
                                                <p class="mb-3">
                                                    <small class="text-muted">
                                                        <i class="far fa-calendar me-1"></i>
                                                        Clôture: <?php echo $dateFin->format('d/m/Y H:i'); ?>
                                                    </small>
                                                </p>
                                                <div class="progress mb-3" style="height: 6px;">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: <?php echo min(100, max(10, 100 - ($interval->days * 100 / 30))); ?>%">
                                                    </div>
                                                </div>
                                                <a href="<?php echo htmlspecialchars($submitCandidatureUrl . '?edition=' . $edition['id_edition']); ?>"
                                                    class="btn btn-primary">
                                                    <i class="fas fa-paper-plane me-1"></i> Soumettre
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($hasOpenSubmissionWindow): ?>
                            <div class="cta-section mt-4">
                                <div class="cta-content">
                                    <h3>Prêt à participer ?</h3>
                                    <p>Soumettez votre meilleur contenu et tentez de remporter les Social Media Awards !</p>
                                    <a href="<?php echo htmlspecialchars($submitCandidatureUrl); ?>" class="btn btn-cta">
                                        <i class="fas fa-rocket me-2"></i> Soumettre ma candidature
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (count($availableCategories) > 0): ?>
        <div class="modal fade modal-categories" id="categoriesModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-list me-2"></i>
                            Toutes les catégories disponibles (<?php echo count($availableCategories); ?>)
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Vous pouvez soumettre une candidature dans chacune de ces catégories.
                            Cliquez sur "Candidater" pour commencer.
                        </div>

                        <div class="row">
                            <?php foreach ($availableCategories as $category): ?>
                                <?php
                                $deadline = new DateTime();
                                if (!empty($category['date_fin_candidatures'])) {
                                    try {
                                        $deadline = new DateTime($category['date_fin_candidatures']);
                                        $now = new DateTime();
                                        $interval = $now->diff($deadline);
                                        $daysLeft = $interval->days;
                                    } catch (Exception $e) {
                                        $daysLeft = 0;
                                    }
                                } else {
                                    $daysLeft = 0;
                                }
                                ?>
                                <div class="col-md-6 mb-3">
                                    <div class="category-card h-100">
                                        <div class="category-header">
                                            <h6 class="category-title"><?php echo htmlspecialchars($category['nom']); ?></h6>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($category['edition_nom']); ?></span>
                                        </div>

                                        <?php if (!empty($category['description'])): ?>
                                            <p class="category-description">
                                                <?php echo htmlspecialchars($category['description']); ?>
                                            </p>
                                        <?php endif; ?>

                                        <div class="category-details">
                                            <div>
                                                <?php if (!empty($category['plateforme_cible'])): ?>
                                                    <div class="mb-1">
                                                        <i class="fas fa-globe me-1 text-primary"></i>
                                                        <small><?php echo htmlspecialchars($category['plateforme_cible']); ?></small>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="mb-1">
                                                    <i class="fas fa-user-friends me-1 text-success"></i>
                                                    <small><?php echo htmlspecialchars((string) $category['limite_nomines']); ?> nominés maximum</small>
                                                </div>

                                                <?php if (!empty($category['date_debut_votes']) && !empty($category['date_fin_votes'])): ?>
                                                    <?php
                                                    $startVotes = new DateTime($category['date_debut_votes']);
                                                    $endVotes = new DateTime($category['date_fin_votes']);
                                                    ?>
                                                    <div class="mb-1">
                                                        <i class="fas fa-vote-yea me-1 text-warning"></i>
                                                        <small>Votes: <?php echo $startVotes->format('d/m'); ?> - <?php echo $endVotes->format('d/m/Y'); ?></small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="text-end">
                                                <div class="mb-2 ">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <small>J-<?php echo $daysLeft; ?></small>
                                                </div>
                                                <div class="deadline-warning">
                                                    <?php echo $deadline->format('d/m/Y H:i'); ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2 mt-3">
                                            <a href="<?php echo htmlspecialchars($submitCandidatureUrl . '?categorie=' . $category['id_categorie']); ?>"
                                                class="btn btn-primary">
                                                <i class="fas fa-paper-plane me-1"></i> Candidater maintenant
                                            </a>
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="shareCategory('<?php echo addslashes($category['nom']); ?>', <?php echo (int) $category['id_categorie']; ?>)">
                                                <i class="fas fa-share-alt me-1"></i> Partager
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <a href="<?php echo htmlspecialchars($submitCandidatureUrl); ?>" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Nouvelle candidature
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div>
                    <h5>Social Media Awards</h5>
                    <p>Célébrons la créativité numérique ensemble.</p>
                </div>
                <div>
                    <h5>Liens utiles</h5>
                    <ul class="footer-links">
                        <li><a href="<?php echo htmlspecialchars($rulesUrl); ?>">Règlement</a></li>
                        <li><a href="<?php echo htmlspecialchars($contactUrl); ?>">Contact</a></li>
                        <li><a href="<?php echo htmlspecialchars($aboutUrl); ?>">À propos</a></li>
                    </ul>
                </div>
                <div>
                    <h5>Assistance</h5>
                    <ul class="footer-links">
                        <li><a href="mailto:support@socialmediaawards.fr">support@socialmediaawards.fr</a></li>
                        <li><a href="tel:+33123456789">+33 1 23 45 67 89</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="mb-0">&copy; <?php echo htmlspecialchars((string) $copyrightYear); ?> Social Media Awards. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo htmlspecialchars($candidateJsUrl); ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            const statNumbers = document.querySelectorAll('.stat-number[data-count]');
            statNumbers.forEach(element => {
                const target = parseInt(element.getAttribute('data-count'));
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    element.textContent = Math.floor(current).toLocaleString('fr-FR');
                }, 16);
            });

            function updateCountdown(endDate) {
                const now = new Date().getTime();
                const distance = endDate - now;

                if (distance < 0) return;

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                const elements = {
                    days: document.querySelector('.countdown-days'),
                    hours: document.querySelector('.countdown-hours'),
                    minutes: document.querySelector('.countdown-minutes'),
                    seconds: document.querySelector('.countdown-seconds')
                };

                if (elements.days) elements.days.textContent = days;
                if (elements.hours) elements.hours.textContent = hours.toString().padStart(2, '0');
                if (elements.minutes) elements.minutes.textContent = minutes.toString().padStart(2, '0');
                if (elements.seconds) elements.seconds.textContent = seconds.toString().padStart(2, '0');
            }

            const countdownElement = document.querySelector('[data-countdown]');
            if (countdownElement) {
                const endDate = new Date(countdownElement.getAttribute('data-countdown')).getTime();
                setInterval(() => updateCountdown(endDate), 1000);
            }
        });

        async function copyShareText(text) {
            if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
                try {
                    await navigator.clipboard.writeText(text);
                    return true;
                } catch (error) {
                }
            }

            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.setAttribute('readonly', 'readonly');
            textArea.style.position = 'fixed';
            textArea.style.opacity = '0';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            let copied = false;

            try {
                copied = document.execCommand('copy');
            } catch (error) {
                copied = false;
            }

            document.body.removeChild(textArea);
            return copied;
        }

        async function shareCategory(categoryName, categoryId) {
            const url = '<?php echo addslashes($submitCandidatureUrl); ?>?categorie=' + categoryId;
            const text = `Je vais me candidater à la catégorie "${categoryName}" aux Social Media Awards ! ${url}`;

            if (navigator.share) {
                try {
                    await navigator.share({
                        title: 'Social Media Awards - ' + categoryName,
                        text: text,
                        url: url
                    });
                    return;
                } catch (error) {
                    if (error && error.name === 'AbortError') {
                        return;
                    }
                }
            }

            const copied = await copyShareText(text);

            if (copied) {
                alert('Lien copié dans le presse-papier !');
                return;
            }

            window.prompt('Copiez ce texte manuellement :', text);
        }
    </script>
</body>

</html>