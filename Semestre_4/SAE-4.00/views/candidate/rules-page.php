<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Règlement - Social Media Awards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($candidateCssUrl); ?>">
    <style>
        .article-card { border: none; border-radius: 18px; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08); margin-bottom: 1.5rem; }
        .article-index { width: 42px; height: 42px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #4FBDAB, #45a999); color: white; font-weight: 700; margin-right: 0.8rem; }
        .highlight-box { border-radius: 16px; background: #f8fafc; border: 1px solid rgba(15, 23, 42, 0.08); padding: 1rem 1.25rem; }
    </style>
</head>
<?php
$articles = [
    [
        'title' => 'Objet de la participation',
        'body' => [
            'Les Social Media Awards distinguent des contenus, projets et créateurs pour leur impact sur les plateformes numériques.',
            'Toute candidature déposée doit correspondre à une catégorie ouverte et respecter les dates définies par l’édition active.',
        ],
    ],
    [
        'title' => 'Conditions de recevabilité',
        'body' => [
            'Le candidat doit disposer des droits nécessaires sur les contenus soumis et pouvoir justifier leur exploitation publique.',
            'Les contenus illicites, trompeurs, discriminatoires, diffamatoires ou contraires aux lois en vigueur sont exclus.',
        ],
    ],
    [
        'title' => 'Soumission et examen',
        'body' => [
            'Chaque candidature peut être vérifiée, refusée ou retirée si les informations transmises sont inexactes ou incomplètes.',
            'L’organisation se réserve le droit de demander des éléments complémentaires pour confirmer l’éligibilité.',
        ],
    ],
    [
        'title' => 'Période de votes et communication',
        'body' => [
            'Une fois nommé, le participant peut promouvoir sa nomination via son lien public et le kit de partage mis à disposition.',
            'Toute tentative de fraude, d’achat de votes ou d’automatisation abusive entraîne une exclusion immédiate.',
        ],
    ],
    [
        'title' => 'Données et responsabilités',
        'body' => [
            'Les données affichées publiquement doivent rester exactes, licites et pertinentes au regard de la nomination.',
            'Le candidat demeure responsable du contenu publié via ses propres comptes et de l’usage qu’il fait de sa campagne de communication.',
        ],
    ],
];
?>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo htmlspecialchars($candidateDashboardUrl); ?>">
                <i class="fas fa-trophy me-2"></i>Social Media Awards
            </a>
            <div class="navbar-nav ms-auto align-items-center">
                <a class="nav-link" href="<?php echo htmlspecialchars($candidateDashboardUrl); ?>">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
                <span class="navbar-text me-3"><?php echo htmlspecialchars($userPseudonyme); ?></span>
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
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                    <div>
                        <h1 class="page-title mb-2"><i class="fas fa-file-contract me-2"></i>Règlement candidat</h1>
                        <p class="page-subtitle mb-0">Référez-vous à ces principes avant toute soumission, modification de profil ou campagne de promotion.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Imprimer
                        </button>
                        <a href="<?php echo htmlspecialchars($submitCandidatureUrl); ?>" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Soumettre une candidature
                        </a>
                    </div>
                </div>

                <div class="highlight-box mb-4">
                    <strong class="d-block mb-2">Point d’attention</strong>
                    <p class="mb-0">Le règlement complet opposable peut évoluer selon l’édition active. Cette page reprend les repères opérationnels les plus utiles dans l’espace candidat.</p>
                </div>

                <?php foreach ($articles as $index => $article): ?>
                    <section class="card article-card">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-3 d-flex align-items-center">
                                <span class="article-index"><?php echo (int) ($index + 1); ?></span>
                                <span><?php echo htmlspecialchars((string) $article['title']); ?></span>
                            </h2>
                            <?php foreach ($article['body'] as $paragraph): ?>
                                <p class="mb-3"><?php echo htmlspecialchars((string) $paragraph); ?></p>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>

                <section class="card article-card">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3"><i class="fas fa-list-check me-2 text-primary"></i>Checklist rapide</h2>
                        <ul class="mb-0 ps-3">
                            <li class="mb-2">Vérifier que le contenu soumis vous appartient ou que vous avez l’autorisation de le représenter.</li>
                            <li class="mb-2">Contrôler les liens, les visuels et les informations publiques avant ouverture des votes.</li>
                            <li class="mb-2">Utiliser des communications honnêtes, sans promesse ni automatisation interdite.</li>
                            <li>Conserver une preuve de vos droits et des informations fournies en cas de demande complémentaire.</li>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
</html>