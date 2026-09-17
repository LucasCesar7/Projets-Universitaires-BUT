<?php require_once __DIR__ . '/../../partials/admin-header.php'; ?>

<link rel="stylesheet" href="<?php echo htmlspecialchars($adminCategoriesCssUrl); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php
$totalCategories = count($categories);
$now = time();
$activeCategories = array_filter($categories, static function ($category) use ($now): bool {
    $end = strtotime((string) ($category->getDateFinVotes() ?? ''));
    $start = strtotime((string) ($category->getDateDebutVotes() ?? ''));

    return $end !== false && $start !== false && $now <= $end && $now >= $start;
});
$upcomingCategories = array_filter($categories, static function ($category) use ($now): bool {
    $start = strtotime((string) ($category->getDateDebutVotes() ?? ''));

    return $start !== false && $now < $start;
});
$endedCategories = array_filter($categories, static function ($category) use ($now): bool {
    $end = strtotime((string) ($category->getDateFinVotes() ?? ''));

    return $end !== false && $now > $end;
});
$successMessage = $successCode === '1' ? 'Opération réussie !' : null;
$errorMessage = $errorCode === '1' ? 'Erreur lors de l\'opération.' : null;
?>

<section class="admin-categories-page">
    <div class="admin-page-header">
        <div class="page-title">
            <h1><i class="fas fa-tags"></i> Gestion des catégories</h1>
            <p><?php echo (int) $totalCategories; ?> catégorie(s) au total</p>
        </div>
        <a href="<?php echo htmlspecialchars($addCategoryUrl); ?>" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus-circle"></i> Nouvelle catégorie
        </a>
    </div>

    <div class="admin-content">
        <?php if ($successMessage !== null): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage !== null): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <?php if ($categories !== []): ?>
            <div class="stats-summary">
                <div class="stat-item total">
                    <span class="stat-number"><?php echo (int) $totalCategories; ?></span>
                    <span class="stat-label">Total catégories</span>
                </div>
                <div class="stat-item active">
                    <span class="stat-number"><?php echo count($activeCategories); ?></span>
                    <span class="stat-label">Actives</span>
                </div>
                <div class="stat-item upcoming">
                    <span class="stat-number"><?php echo count($upcomingCategories); ?></span>
                    <span class="stat-label">À venir</span>
                </div>
                <div class="stat-item ended">
                    <span class="stat-number"><?php echo count($endedCategories); ?></span>
                    <span class="stat-label">Terminées</span>
                </div>
            </div>
        <?php endif; ?>

        <div class="table-controls">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Rechercher une catégorie...">
            </div>
            <div class="filters">
                <select class="filter-select" id="platformFilter">
                    <option value="">Toutes les plateformes</option>
                    <option value="Toutes">Toutes</option>
                    <option value="TikTok">TikTok</option>
                    <option value="Instagram">Instagram</option>
                    <option value="YouTube">YouTube</option>
                    <option value="Twitch">Twitch</option>
                    <option value="Spotify">Spotify</option>
                    <option value="Facebook">Facebook</option>
                    <option value="X">X (Twitter)</option>
                    <option value="Autre">Autre</option>
                </select>
                <select class="filter-select" id="editionFilter">
                    <option value="">Toutes les éditions</option>
                    <?php foreach ($editions as $edition): ?>
                        <option value="<?php echo (int) $edition->getIdEdition(); ?>"><?php echo htmlspecialchars($edition->getNom()); ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="filter-select" id="statusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actives</option>
                    <option value="upcoming">À venir</option>
                    <option value="ended">Terminées</option>
                </select>
            </div>
        </div>

        <?php if ($categories === []): ?>
            <div class="empty-state">
                <i class="fas fa-tags"></i>
                <h3>Aucune catégorie pour le moment</h3>
                <p>Commencez par créer votre première catégorie.</p>
                <a href="<?php echo htmlspecialchars($addCategoryUrl); ?>" class="btn btn-primary" style="margin-top: 15px; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-plus"></i> Créer la première catégorie
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="enhanced-table" id="categoriesTable">
                    <thead>
                        <tr>
                            <th width="30%"><i class="fas fa-heading"></i> Catégorie</th>
                            <th width="15%"><i class="fas fa-globe"></i> Plateforme</th>
                            <th width="15%"><i class="fas fa-calendar-alt"></i> Édition</th>
                            <th width="15%"><i class="fas fa-users"></i> Participation</th>
                            <th width="15%"><i class="fas fa-vote-yea"></i> Statut votes</th>
                            <th width="10%"><i class="fas fa-cog"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <?php
                            $start = strtotime((string) ($category->getDateDebutVotes() ?? ''));
                            $end = strtotime((string) ($category->getDateFinVotes() ?? ''));
                            if ($end !== false && $now > $end) {
                                $status = 'ended';
                                $statusText = 'Terminé';
                            } elseif ($start !== false && $now < $start) {
                                $status = 'upcoming';
                                $statusText = 'À venir';
                            } elseif ($start !== false && $end !== false && $now >= $start && $now <= $end) {
                                $status = 'active';
                                $statusText = 'En cours';
                            } else {
                                $status = '';
                                $statusText = 'Non défini';
                            }
                            $platformClass = 'platform-' . strtolower(str_replace([' ', '-'], '', (string) $category->getPlateformeCible()));
                            $percentage = $category->getLimiteNomines() > 0
                                ? min(100, ($category->getNbNominations() / $category->getLimiteNomines()) * 100)
                                : 0;
                            ?>
                            <tr data-id="<?php echo (int) $category->getIdCategorie(); ?>"
                                data-platform="<?php echo htmlspecialchars((string) $category->getPlateformeCible()); ?>"
                                data-edition="<?php echo (int) $category->getIdEdition(); ?>"
                                data-status="<?php echo htmlspecialchars($status); ?>"
                                data-candidatures="<?php echo (int) $category->getNbCandidatures(); ?>"
                                data-nominations="<?php echo (int) $category->getNbNominations(); ?>"
                                data-limite="<?php echo (int) $category->getLimiteNomines(); ?>"
                                data-debut="<?php echo htmlspecialchars((string) ($category->getDateDebutVotes() ?? '')); ?>"
                                data-fin="<?php echo htmlspecialchars((string) ($category->getDateFinVotes() ?? '')); ?>">
                                <td>
                                    <div class="category-name">
                                        <?php if ($category->getImage()): ?>
                                            <img src="<?php echo htmlspecialchars(appUrl('public/' . $category->getImage())); ?>"
                                                 alt="<?php echo htmlspecialchars($category->getNom()); ?>"
                                                 class="category-image">
                                        <?php else: ?>
                                            <div class="category-image" style="background: #f0f2f5; display: flex; align-items: center; justify-content: center; color: #7f8c8d;">
                                                <i class="fas fa-tag"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="category-info">
                                            <h4><?php echo htmlspecialchars($category->getNom()); ?></h4>
                                            <?php if ($category->getDescription()): ?>
                                                <div class="description" title="<?php echo htmlspecialchars((string) $category->getDescription()); ?>">
                                                    <?php echo htmlspecialchars(mb_substr((string) $category->getDescription(), 0, 60)); ?>...
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="platform-badge <?php echo htmlspecialchars($platformClass); ?>">
                                        <?php echo htmlspecialchars((string) $category->getPlateformeCible()); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars((string) ($category->getEditionNom() ?? 'Non définie')); ?></strong>
                                </td>
                                <td class="numbers-cell">
                                    <div style="font-weight: 600; color: var(--dark-color);">
                                        <?php echo (int) $category->getNbCandidatures(); ?> candidatures
                                    </div>
                                    <div style="font-size: 13px; color: var(--success-color);">
                                        <?php echo (int) $category->getNbNominations(); ?> nominés
                                    </div>
                                    <?php if ($category->getLimiteNomines() > 0): ?>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $percentage; ?>%;"></div>
                                        </div>
                                        <small style="color: var(--secondary-color);">
                                            <?php echo (int) $category->getNbNominations(); ?>/<?php echo (int) $category->getLimiteNomines(); ?> places
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($category->getDateDebutVotes() && $category->getDateFinVotes()): ?>
                                        <div style="font-size: 13px;">
                                            <div>Début: <?php echo htmlspecialchars(date('d/m/Y', strtotime((string) $category->getDateDebutVotes()))); ?></div>
                                            <div>Fin: <?php echo htmlspecialchars(date('d/m/Y', strtotime((string) $category->getDateFinVotes()))); ?></div>
                                        </div>
                                        <span class="vote-status status-<?php echo htmlspecialchars($status); ?>">
                                            <?php echo htmlspecialchars($statusText); ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--secondary-color); font-style: italic;">
                                            Dates non définies
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <a href="<?php echo htmlspecialchars($editCategoryBaseUrl . '?id=' . $category->getIdCategorie()); ?>"
                                       class="action-btn edit"
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="<?php echo htmlspecialchars($manageCategoriesUrl); ?>" style="display: inline;"
                                          onsubmit="return confirmDelete(<?php echo (int) $category->getIdCategorie(); ?>, '<?php echo addslashes($category->getNom()); ?>')">
                                        <input type="hidden" name="delete_category_id" value="<?php echo (int) $category->getIdCategorie(); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($deleteCategoryToken, ENT_QUOTES, 'UTF-8'); ?>">
                                        <button type="submit" class="action-btn delete" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <a href="<?php echo htmlspecialchars($editCategoryBaseUrl . '?id=' . $category->getIdCategorie()); ?>"
                                       class="action-btn view"
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <a href="#" class="page-link disabled"><i class="fas fa-chevron-left"></i></a>
                <a href="#" class="page-link active">1</a>
                <a href="#" class="page-link">2</a>
                <a href="#" class="page-link">3</a>
                <a href="#" class="page-link"><i class="fas fa-chevron-right"></i></a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script src="<?php echo htmlspecialchars($adminCategoriesJsUrl); ?>"></script>
</div>
</main>
</body>
</html>