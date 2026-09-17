<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/header.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/footer.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/nominees.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Nominés - Social Media Awards</title>
    <style>
        .nominees-hero h1 {
            margin-bottom: 10px;
        }

        .category-badge {
            display: inline-block;
            background: var(--principal);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            margin: 10px 0;
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .nominee-stats {
            display: flex;
            gap: 15px;
            margin: 15px 0;
        }

        .nominee-stat {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray);
        }

        .nominee-stat i {
            color: var(--principal);
        }

        .nominee-stat .stat-number {
            font-weight: bold;
            color: var(--dark);
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
    </style>
</head>
<body>
    <?php require appPath('views/partials/header.php'); ?>

    <div class="main-content">
        <section class="nominees-hero">
            <div class="hero-container">
                <h1>
                    <?php if ($categoryName): ?>
                        Nominés : <?php echo htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8'); ?>
                    <?php elseif ($selectedPlatformLabel !== ''): ?>
                        Nominés sur <?php echo htmlspecialchars($selectedPlatformLabel, ENT_QUOTES, 'UTF-8'); ?>
                    <?php else: ?>
                        Nos Nominés
                    <?php endif; ?>
                </h1>

                <p>
                    <?php if ($categoryName): ?>
                        Découvrez les talents nominés dans cette catégorie
                    <?php elseif ($selectedPlatformLabel !== ''): ?>
                        Découvrez les talents sélectionnés sur <?php echo htmlspecialchars($selectedPlatformLabel, ENT_QUOTES, 'UTF-8'); ?>
                    <?php else: ?>
                        Découvrez tous les talents exceptionnels sélectionnés
                    <?php endif; ?>
                </p>

                <?php if ($categoryName): ?>
                    <div class="category-badge">
                        <i class="fas fa-tag"></i> <?php echo htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php elseif ($selectedPlatformLabel !== ''): ?>
                    <div class="category-badge">
                        <i class="fas fa-hashtag"></i> <?php echo htmlspecialchars($selectedPlatformLabel, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <div class="search-filter">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Rechercher un nominé..." id="searchInput">
                    </div>

                    <select id="categoryFilter">
                        <option value="0">Toutes les catégories</option>
                        <?php foreach ($allCategories as $category): ?>
                            <option value="<?php echo htmlspecialchars((string) $category['id_categorie'], ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo ($categoryFilter === (int) $category['id_categorie']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['nom'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select id="platformFilter">
                        <option value="">Toutes les plateformes</option>
                        <?php foreach ($platforms as $platform): ?>
                            <?php $platformValue = strtolower(trim((string) $platform)); ?>
                            <option value="<?php echo htmlspecialchars($platformValue, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo $platformFilter === $platformValue ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(ucfirst($platform), ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </section>

        <section class="nominees-section">
            <div class="container">
                <div class="pagination-toolbar">
                    <div class="pagination-meta">
                        <?php if (($pagination['totalItems'] ?? 0) > 0): ?>
                            Affichage de <?php echo htmlspecialchars((string) $pagination['startItem'], ENT_QUOTES, 'UTF-8'); ?>
                            à <?php echo htmlspecialchars((string) $pagination['endItem'], ENT_QUOTES, 'UTF-8'); ?>
                            sur <?php echo htmlspecialchars((string) $pagination['totalItems'], ENT_QUOTES, 'UTF-8'); ?> nominés
                        <?php else: ?>
                            Aucun nominé à afficher
                        <?php endif; ?>
                    </div>

                    <form method="get" class="pagination-size-form">
                        <input type="hidden" name="page" value="1">
                        <?php if ($categoryFilter > 0): ?>
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars((string) $categoryFilter, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php endif; ?>
                        <?php if ($categoryName !== ''): ?>
                            <input type="hidden" name="name" value="<?php echo htmlspecialchars((string) $categoryName, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php endif; ?>
                        <?php if ($platformFilter !== ''): ?>
                            <input type="hidden" name="platform" value="<?php echo htmlspecialchars((string) $platformFilter, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php endif; ?>

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

                <?php if ($nomineeCards !== []): ?>
                    <div class="nominees-grid">
                        <?php foreach ($nomineeCards as $nominee): ?>
                            <div class="nominee-card"
                                 data-category="<?php echo htmlspecialchars($nominee['categoryName'], ENT_QUOTES, 'UTF-8'); ?>"
                                 data-platform="<?php echo htmlspecialchars($nominee['platformSlug'], ENT_QUOTES, 'UTF-8'); ?>"
                                 data-name="<?php echo htmlspecialchars(strtolower($nominee['name']), ENT_QUOTES, 'UTF-8'); ?>">

                                <div class="nominee-image">
                                    <img loading="lazy"
                                         src="<?php echo htmlspecialchars($nominee['imageUrl'], ENT_QUOTES, 'UTF-8'); ?>"
                                         alt="<?php echo htmlspecialchars($nominee['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                         onerror="this.src='<?php echo htmlspecialchars($defaultNomineeImageUrl, ENT_QUOTES, 'UTF-8'); ?>'">

                                    <?php if ($nominee['platform'] !== ''): ?>
                                        <div class="platform-badge <?php echo htmlspecialchars($nominee['platform'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <i class="<?php echo htmlspecialchars($nominee['platformIcon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="nominee-info">
                                    <h3><?php echo htmlspecialchars($nominee['name'], ENT_QUOTES, 'UTF-8'); ?></h3>

                                    <?php if ($nominee['categoryName'] !== ''): ?>
                                        <p class="nominee-category">
                                            <i class="fas fa-tag"></i>
                                            <?php echo htmlspecialchars($nominee['categoryName'], ENT_QUOTES, 'UTF-8'); ?>
                                        </p>
                                    <?php endif; ?>

                                    <p class="nominee-description">
                                        <?php echo htmlspecialchars($nominee['description'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>

                                    <div class="nominee-stats">
                                        <div class="nominee-stat">
                                            <i class="fas fa-heart"></i>
                                            <span class="stat-number"><?php echo htmlspecialchars($nominee['formattedVotes'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            <span class="stat-label">Votes</span>
                                        </div>

                                        <?php if ($nominee['platform'] !== ''): ?>
                                            <div class="nominee-stat">
                                                <i class="<?php echo htmlspecialchars($nominee['platformIcon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                                                <span class="stat-label"><?php echo htmlspecialchars(ucfirst($nominee['platform']), ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <a href="<?php echo htmlspecialchars($nominee['voteLink'], ENT_QUOTES, 'UTF-8'); ?>"
                                       class="<?php echo htmlspecialchars($nominee['voteButtonClass'], ENT_QUOTES, 'UTF-8'); ?>"
                                       data-authenticated="<?php echo $isAuthenticated ? 'true' : 'false'; ?>"
                                                    data-voting-open="<?php echo $nominee['isVotingOpen'] ? 'true' : 'false'; ?>">
                                        <i class="fas fa-vote-yea"></i>
                                        <?php echo htmlspecialchars($nominee['voteButtonText'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h3>Aucun nominé disponible</h3>
                        <p>
                            <?php if ($categoryFilter > 0): ?>
                                Aucun nominé dans cette catégorie pour le moment.
                            <?php else: ?>
                                Les nominations pour cette édition seront bientôt annoncées.
                            <?php endif; ?>
                        </p>
                        <?php if ($categoryFilter > 0): ?>
                            <a href="<?php echo htmlspecialchars($nomineesUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn-view-nominees" style="margin-top: 20px;">
                                Voir tous les nominés
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php
                $currentPage = (int) ($pagination['page'] ?? 1);
                $totalPages = (int) ($pagination['totalPages'] ?? 1);
                $perPage = (int) ($pagination['perPage'] ?? 12);
                $baseQuery = [];
                if ($categoryFilter > 0) {
                    $baseQuery['category'] = $categoryFilter;
                }
                if ($categoryName !== '') {
                    $baseQuery['name'] = $categoryName;
                }
                if ($platformFilter !== '') {
                    $baseQuery['platform'] = $platformFilter;
                }
                $baseQuery['per_page'] = $perPage;
                ?>
                <?php if ($totalPages > 1): ?>
                    <nav class="pagination-nav" aria-label="Pagination des nominés">
                        <?php if ($currentPage > 1): ?>
                            <?php $previousQuery = array_merge($baseQuery, ['page' => $currentPage - 1]); ?>
                            <a href="?<?php echo htmlspecialchars(http_build_query($previousQuery), ENT_QUOTES, 'UTF-8'); ?>" aria-label="Page précédente">&laquo;</a>
                        <?php else: ?>
                            <span class="disabled" aria-hidden="true">&laquo;</span>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <?php if ($p === $currentPage): ?>
                                <span class="active"><?php echo $p; ?></span>
                            <?php else: ?>
                                <?php $pageQuery = array_merge($baseQuery, ['page' => $p]); ?>
                                <a href="?<?php echo htmlspecialchars(http_build_query($pageQuery), ENT_QUOTES, 'UTF-8'); ?>"><?php echo $p; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <?php $nextQuery = array_merge($baseQuery, ['page' => $currentPage + 1]); ?>
                            <a href="?<?php echo htmlspecialchars(http_build_query($nextQuery), ENT_QUOTES, 'UTF-8'); ?>" aria-label="Page suivante">&raquo;</a>
                        <?php else: ?>
                            <span class="disabled" aria-hidden="true">&raquo;</span>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php require appPath('views/partials/footer.php'); ?>
    <script src="<?php echo htmlspecialchars(appUrl('assets/js/nominees.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
