<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/header.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/footer.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/categories.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Catégories - Social Media Awards <?php echo htmlspecialchars((string) ($activeEdition['annee'] ?? date('Y')), ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        .no-categories-message {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.5s ease 0.3s forwards;
        }

        .empty-state {
            display: inline-block;
            padding: 40px;
            background: var(--light-gray);
            border-radius: 15px;
            border: 2px dashed var(--border-color, #ddd);
            max-width: 500px;
            margin: 0 auto;
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--principal);
            margin-bottom: 20px;
            opacity: 0.7;
        }

        .empty-state h3 {
            color: var(--dark);
            margin-bottom: 10px;
        }

        .pagination-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin: 0 0 24px;
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
            margin-top: 28px;
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

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <?php require appPath('views/partials/header.php'); ?>

    <div class="main-content">
        <section class="categories-hero">
            <div class="hero-container">
                <h1>Catégories du concours <?php echo htmlspecialchars((string) ($activeEdition['annee'] ?? date('Y')), ENT_QUOTES, 'UTF-8'); ?></h1>
                <p>Découvrez les <?php echo htmlspecialchars((string) $pageStats['categories'], ENT_QUOTES, 'UTF-8'); ?> catégories qui célèbrent l'excellence sur chaque plateforme sociale</p>

                <div class="hero-stats">
                    <div class="stat">
                        <div class="stat-number"><?php echo htmlspecialchars((string) $pageStats['categories'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="stat-label">Catégories</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number"><?php echo htmlspecialchars((string) $pageStats['platforms'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="stat-label">Platforms</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number"><?php echo htmlspecialchars((string) $pageStats['nominees'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="stat-label">Nominés</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="categories-section">
            <div class="container">
                <div class="categories-filter">
                    <button class="filter-btn active" data-filter="all">Toutes</button>
                    <?php foreach ($platforms as $platform): ?>
                        <?php if ($platform && $platform !== 'Toutes' && $platform !== 'all'): ?>
                            <button class="filter-btn" data-filter="<?php echo htmlspecialchars($platform, ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="<?php echo htmlspecialchars($platformIcons[$platform] ?? 'fas fa-globe', ENT_QUOTES, 'UTF-8'); ?>"></i>
                                <?php echo htmlspecialchars($platform, ENT_QUOTES, 'UTF-8'); ?>
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

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

                <div class="categories-grid">
                    <?php if ($categoryCards !== []): ?>
                        <?php foreach ($categoryCards as $category): ?>
                            <div class="category-card" data-platform="<?php echo htmlspecialchars($category['platform'] ?? 'all', ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="category-header">
                                    <div class="category-icon">
                                        <i class="<?php echo htmlspecialchars($category['categoryIcon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                                    </div>
                                    <div class="platform-tags">
                                        <?php if (($category['platform'] ?? null) && $category['platform'] !== 'Toutes'): ?>
                                            <span class="platform-tag <?php echo htmlspecialchars($category['platformClass'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <i class="<?php echo htmlspecialchars($category['platformIcon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                                                <?php echo htmlspecialchars($category['platform'], ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php foreach ($category['additionalPlatforms'] as $platform): ?>
                                            <span class="platform-tag <?php echo htmlspecialchars($platform['class'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <i class="<?php echo htmlspecialchars($platform['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                                                <?php echo htmlspecialchars($platform['name'], ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <h2><?php echo htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                                <p><?php echo htmlspecialchars($category['description'], ENT_QUOTES, 'UTF-8'); ?></p>

                                <div class="category-stats">
                                    <div class="stat">
                                        <div class="stat-number"><?php echo htmlspecialchars((string) $category['nomineesCount'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div class="stat-label">Nominés</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-number"><?php echo htmlspecialchars($category['formattedVotes'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div class="stat-label">Votes</div>
                                    </div>
                                </div>

                                <button class="btn-view-nominees"
                                        data-category-id="<?php echo htmlspecialchars((string) $category['id'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-category-name="<?php echo htmlspecialchars($category['encodedName'], ENT_QUOTES, 'UTF-8'); ?>">
                                    Voir les Nominés
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-categories-message">
                            <div class="empty-state">
                                <i class="fas fa-folder-open"></i>
                                <h2>Aucune catégorie disponible</h2>
                                <p>Les catégories de cette édition seront bientôt annoncées.</p>
                                <p><small>Vérifiez que vous avez créé des catégories dans la base de données.</small></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                $currentPage = (int) ($pagination['page'] ?? 1);
                $totalPages = (int) ($pagination['totalPages'] ?? 1);
                $perPage = (int) ($pagination['perPage'] ?? 12);
                ?>
                <?php if ($totalPages > 1): ?>
                    <nav class="pagination-nav" aria-label="Pagination des catégories">
                        <?php if ($currentPage > 1): ?>
                            <a href="?<?php echo htmlspecialchars(http_build_query(['page' => $currentPage - 1, 'per_page' => $perPage]), ENT_QUOTES, 'UTF-8'); ?>" aria-label="Page précédente">&laquo;</a>
                        <?php else: ?>
                            <span class="disabled" aria-hidden="true">&laquo;</span>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <?php if ($p === $currentPage): ?>
                                <span class="active"><?php echo $p; ?></span>
                            <?php else: ?>
                                <a href="?<?php echo htmlspecialchars(http_build_query(['page' => $p, 'per_page' => $perPage]), ENT_QUOTES, 'UTF-8'); ?>"><?php echo $p; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?<?php echo htmlspecialchars(http_build_query(['page' => $currentPage + 1, 'per_page' => $perPage]), ENT_QUOTES, 'UTF-8'); ?>" aria-label="Page suivante">&raquo;</a>
                        <?php else: ?>
                            <span class="disabled" aria-hidden="true">&raquo;</span>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php require appPath('views/partials/footer.php'); ?>
    <script src="<?php echo htmlspecialchars(appUrl('assets/js/categories.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
