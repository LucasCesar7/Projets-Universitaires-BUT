<?php require_once __DIR__ . '/../partials/admin-header.php'; ?>

<link rel="stylesheet" href="<?php echo htmlspecialchars($adminNominationsCssUrl); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/brands.min.css">

<section class="admin-content">
    <header class="admin-header mb-4">
        <div class="header-left">
            <h1><i class="fas fa-award"></i> Gestion des nominations</h1>
            <nav class="breadcrumb">
                <a href="<?php echo htmlspecialchars($adminDashboardUrl); ?>">Tableau de bord</a>
                <span> &gt; </span>
                <span>Nominations</span>
            </nav>
        </div>
    </header>

    <?php if ($successFlash): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars((string) $successFlash); ?>
        </div>
    <?php endif; ?>

    <?php if ($errorFlash): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars((string) $errorFlash); ?>
        </div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-teal">
                <i class="fas fa-award"></i>
            </div>
            <div class="stat-info">
                <h3>Total nominations</h3>
                <div class="stat-number"><?php echo (int) $totalNominations; ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-blue">
                <i class="fas fa-list"></i>
            </div>
            <div class="stat-info">
                <h3>Page courante</h3>
                <div class="stat-number"><?php echo (int) $currentPage; ?>/<?php echo (int) $totalPages; ?></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Liste des nominations</h3>
        </div>
        <div class="card-body">
            <?php if ($nominations === []): ?>
                <div class="empty-state">
                    <i class="fas fa-award"></i>
                    <h4>Aucune nomination trouvée</h4>
                    <p>Les nominations se créent depuis les candidatures approuvées.</p>
                    <a href="<?php echo htmlspecialchars($manageCandidaturesUrl); ?>" class="btn btn-primary">
                        <i class="fas fa-folder-open"></i> Voir les candidatures
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Candidat</th>
                                <th>Titre</th>
                                <th>Catégorie</th>
                                <th>Plateforme</th>
                                <th>Date d'approbation</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($nominations as $index => $nomination): ?>
                                <?php
                                $imagePath = ltrim((string) ($nomination->getUrlImage() ?? ''), '/');
                                if ($imagePath === '') {
                                    $avatarUrl = $defaultAvatarUrl;
                                } elseif (str_starts_with($imagePath, 'uploads/')) {
                                    $avatarUrl = appUrl('public/' . $imagePath);
                                } else {
                                    $avatarUrl = appUrl($imagePath);
                                }

                                $platform = strtolower($nomination->getPlateforme());
                                $platformIcon = match ($platform) {
                                    'tiktok' => 'fa-tiktok',
                                    'instagram' => 'fa-instagram',
                                    'youtube' => 'fa-youtube',
                                    'facebook' => 'fa-facebook',
                                    'x', 'twitter' => 'fa-x-twitter',
                                    'twitch' => 'fa-twitch',
                                    'spotify' => 'fa-spotify',
                                    default => 'fa-globe',
                                };
                                ?>
                                <tr style="--row-index: <?php echo (int) $index; ?>;">
                                    <td>
                                        <div class="user-info">
                                            <img loading="lazy" src="<?php echo htmlspecialchars($avatarUrl); ?>"
                                                 alt="<?php echo htmlspecialchars($nomination->getLibelle()); ?>"
                                                 class="user-avatar"
                                                 onerror="this.src='<?php echo addslashes($defaultAvatarUrl); ?>'">
                                            <div class="user-details">
                                                <strong><?php echo htmlspecialchars($nomination->getLibelle()); ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($nomination->getLibelle()); ?></strong>
                                        <div class="text-muted small">
                                            <?php echo htmlspecialchars(substr($nomination->getArgumentaire(), 0, 90)); ?>...
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-teal">
                                            <?php echo htmlspecialchars($nomination->getCategorieNom() ?? ('Catégorie #' . $nomination->getIdCategorie())); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="platform-badge">
                                            <i class="fab <?php echo htmlspecialchars($platformIcon); ?>"></i>
                                            <?php echo htmlspecialchars($nomination->getPlateforme()); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars(date('d/m/Y', strtotime($nomination->getDateApprobation() ?? 'now'))); ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="<?php echo htmlspecialchars($viewNominationBaseUrl . '?id=' . $nomination->getIdNomination()); ?>" class="btn-icon btn-view" title="Voir la fiche">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="post" action="<?php echo htmlspecialchars($deleteNominationUrl); ?>" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette nomination ?');">
                                                <input type="hidden" name="id" value="<?php echo (int) $nomination->getIdNomination(); ?>">
                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($deleteNominationToken, ENT_QUOTES, 'UTF-8'); ?>">
                                                <button type="submit" class="btn-icon btn-delete" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                    <nav class="pagination-nav" aria-label="Pagination des nominations">
                        <ul class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <li><a href="?page=<?php echo (int) ($currentPage - 1); ?>" class="pagination-btn">&laquo; Préc.</a></li>
                            <?php endif; ?>
                            <?php for ($page = max(1, $currentPage - 2); $page <= min($totalPages, $currentPage + 2); $page++): ?>
                                <li><a href="?page=<?php echo (int) $page; ?>" class="pagination-btn <?php echo $page === $currentPage ? 'active' : ''; ?>"><?php echo (int) $page; ?></a></li>
                            <?php endfor; ?>
                            <?php if ($currentPage < $totalPages): ?>
                                <li><a href="?page=<?php echo (int) ($currentPage + 1); ?>" class="pagination-btn">Suiv. &raquo;</a></li>
                            <?php endif; ?>
                        </ul>
                        <p class="pagination-info">Page <?php echo (int) $currentPage; ?> sur <?php echo (int) $totalPages; ?> · <?php echo (int) $totalNominations; ?> nomination(s)</p>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
</div>
</main>
</body>
</html>