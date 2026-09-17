<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Candidatures - Social Media Awards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($candidateCssUrl); ?>">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo htmlspecialchars($homeUrl); ?>">
                <i class="fas fa-trophy me-2"></i>Social Media Awards
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?php echo htmlspecialchars($candidateDashboardUrl); ?>">
                    <i class="fas fa-tachometer-alt me-1"></i> Tableau de bord
                </a>
                <span class="navbar-text me-3">
                    <?php echo htmlspecialchars($userPseudonyme); ?>
                </span>
                <form method="post" action="<?php echo htmlspecialchars($logoutUrl); ?>" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($logoutToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="nav-link" style="border: none; background: none;">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php include __DIR__ . '/../partials/sidebar-candidat.php'; ?>
            </div>

            <div class="col-md-9">
                <div class="page-header-candidatures">
                    <h1>
                        <i class="fas fa-file-alt me-2"></i>
                        Mes Candidatures
                    </h1>
                    <p class="text-muted">Suivez l'état de toutes vos candidatures</p>
                </div>
                <?php if ($successMessage): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars((string) $successMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars((string) $errorMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!$hasOpenSubmissionWindow && !$errorMessage): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        Aucun appel à candidatures n'est actuellement ouvert.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="stats-candidatures">
                    <div class="stat-card-candidature total">
                        <div class="stat-content">
                            <i class="fas fa-file-alt stat-icon"></i>
                            <h6>Total</h6>
                            <div class="stat-number"><?php echo htmlspecialchars((string) ($stats['total'] ?? 0)); ?></div>
                        </div>
                    </div>

                    <div class="stat-card-candidature pending">
                        <div class="stat-content">
                            <i class="fas fa-clock stat-icon"></i>
                            <h6>En attente</h6>
                            <div class="stat-number"><?php echo htmlspecialchars((string) ($stats['pending'] ?? 0)); ?></div>
                        </div>
                    </div>

                    <div class="stat-card-candidature approved">
                        <div class="stat-content">
                            <i class="fas fa-check-circle stat-icon"></i>
                            <h6>Approuvées</h6>
                            <div class="stat-number"><?php echo htmlspecialchars((string) ($stats['approved'] ?? 0)); ?></div>
                        </div>
                    </div>

                    <div class="stat-card-candidature rejected">
                        <div class="stat-content">
                            <i class="fas fa-times-circle stat-icon"></i>
                            <h6>Rejetées</h6>
                            <div class="stat-number"><?php echo htmlspecialchars((string) ($stats['rejected'] ?? 0)); ?></div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Liste de vos candidatures
                        </h5>
                    </div>
                    <div class="table-container-candidatures">
                        <div class="actions-header-candidatures">
                            <div class="filter-group">
                                <span class="filter-label">Filtrer par :</span>
                                <select class="filter-select" id="filter-status">
                                    <option value="">Tous les statuts</option>
                                    <option value="En attente">En attente</option>
                                    <option value="Approuvée">Approuvées</option>
                                    <option value="Rejetée">Rejetées</option>
                                </select>
                            </div>

                            <?php if ($hasOpenSubmissionWindow): ?>
                                <a href="<?php echo htmlspecialchars($submitCandidatureUrl); ?>" class="btn-add-candidature">
                                    <i class="fas fa-plus"></i> Nouvelle candidature
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">Aucune édition ouverte actuellement</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (empty($candidatures)): ?>
                                <div class="empty-state-candidatures">
                                    <i class="fas fa-file-alt"></i>
                                    <h4>Vous n'avez pas encore soumis de candidature</h4>
                                    <p>Commencez par soumettre votre première candidature pour participer aux Social Media Awards.</p>
                                    <?php if ($hasOpenSubmissionWindow): ?>
                                        <a href="<?php echo htmlspecialchars($submitCandidatureUrl); ?>" class="btn btn-primary btn-empty-state">
                                            <i class="fas fa-paper-plane me-2"></i> Soumettre une candidature
                                        </a>
                                    <?php else: ?>
                                        <p class="text-muted mb-0">Une nouvelle candidature sera possible dès l'ouverture d'une prochaine édition.</p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Titre</th>
                                                <th>Catégorie</th>
                                                <th>Date</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($candidatures as $cand): ?>
                                                <?php
                                                $statusClass = '';
                                                if (($cand['statut'] ?? '') === 'Approuvée') {
                                                    $statusClass = 'approved';
                                                } elseif (($cand['statut'] ?? '') === 'Rejetée') {
                                                    $statusClass = 'rejected';
                                                } else {
                                                    $statusClass = 'pending';
                                                }
                                                ?>
                                                <tr class="table-candidatures <?php echo htmlspecialchars($statusClass); ?>">
                                                    <td>#<?php echo (int) ($cand['id_candidature'] ?? 0); ?></td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars(substr((string) ($cand['libelle'] ?? ''), 0, 40)); ?><?php echo strlen((string) ($cand['libelle'] ?? '')) > 40 ? '...' : ''; ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars((string) ($cand['plateforme'] ?? '')); ?></small>
                                                    </td>
                                                    <td><?php echo htmlspecialchars((string) ($cand['categorie_nom'] ?? 'N/A')); ?></td>
                                                    <td>
                                                        <?php echo date('d/m/Y', strtotime((string) ($cand['date_soumission'] ?? 'now'))); ?><br>
                                                        <small class="text-muted"><?php echo date('H:i', strtotime((string) ($cand['date_soumission'] ?? 'now'))); ?></small>
                                                    </td>
                                                    <td>
                                                        <span class="badge <?php echo ($cand['statut'] ?? '') === 'Approuvée' ? 'bg-success' : (($cand['statut'] ?? '') === 'Rejetée' ? 'bg-danger' : 'bg-warning'); ?>">
                                                            <?php echo htmlspecialchars((string) ($cand['statut'] ?? 'En attente')); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="<?php echo htmlspecialchars($candidatureDetailsUrl . '?id=' . ((int) ($cand['id_candidature'] ?? 0))); ?>"
                                                                class="btn btn-sm btn-outline-primary" title="Voir les détails">
                                                                <i class="fas fa-eye"></i>
                                                            </a>

                                                            <?php if (($cand['statut'] ?? '') === 'En attente'): ?>
                                                                <a href="<?php echo htmlspecialchars($submitCandidatureUrl . '?edit=' . ((int) ($cand['id_candidature'] ?? 0))); ?>"
                                                                    class="btn btn-sm btn-outline-warning" title="Modifier">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form method="post" action="<?php echo htmlspecialchars($deleteCandidatureUrl); ?>" class="d-inline">
                                                                    <input type="hidden" name="id" value="<?php echo (int) ($cand['id_candidature'] ?? 0); ?>">
                                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($deleteCandidatureToken, ENT_QUOTES, 'UTF-8'); ?>">
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-outline-danger"
                                                                        title="Supprimer"
                                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette candidature ?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="bg-dark text-white py-4 mt-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Social Media Awards</h5>
                        <p class="mb-0">Célébrons la créativité numérique ensemble.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="<?php echo htmlspecialchars($rulesUrl); ?>" class="text-white me-3">Règlement</a>
                        <a href="<?php echo htmlspecialchars($contactUrl); ?>" class="text-white me-3">Contact</a>
                    </div>
                </div>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>