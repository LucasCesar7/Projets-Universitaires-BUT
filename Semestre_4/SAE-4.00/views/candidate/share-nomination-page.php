<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kit de Partage - Social Media Awards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($candidateCssUrl); ?>">
    <style>
        .main-card { border: none; border-radius: 18px; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08); }
        .share-card { height: 100%; }
        .share-icon { width: 72px; height: 72px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1.7rem; color: white; margin-bottom: 1rem; }
        .share-preview { background: #f8fafc; border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 14px; padding: 1rem; min-height: 180px; white-space: pre-wrap; }
        .hashtag-list { display: flex; flex-wrap: wrap; gap: 0.6rem; }
        .hashtag-chip { border: none; border-radius: 999px; background: rgba(79, 189, 171, 0.12); color: #12796b; padding: 0.55rem 0.9rem; font-weight: 600; }
        .copy-row { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .copy-row input { flex: 1 1 320px; }
    </style>
</head>
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
                        <h1 class="page-title mb-2"><i class="fas fa-share-alt me-2"></i>Kit de partage</h1>
                        <p class="page-subtitle mb-0">Préparez vos messages, vos hashtags et vos liens pour promouvoir votre nomination.</p>
                    </div>

                    <?php if (count($nominations) > 1): ?>
                        <div style="max-width: 360px;" class="w-100">
                            <label class="form-label" for="nomination-share-select">Nomination ciblée</label>
                            <select id="nomination-share-select" class="form-select" onchange="window.location.href=this.value;">
                                <?php foreach ($nominations as $candidateNomination): ?>
                                    <?php $optionId = (int) ($candidateNomination['id_nomination'] ?? 0); ?>
                                    <option value="<?php echo htmlspecialchars($shareNominationUrl . '?nomination=' . $optionId); ?>" <?php echo $optionId === $selectedNominationId ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars((string) ($candidateNomination['libelle'] ?? ('Nomination #' . $optionId))); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="alert <?php echo htmlspecialchars($statusNoticeClass); ?> mb-4">
                    <h5 class="mb-1"><i class="fas fa-bullhorn me-2"></i><?php echo htmlspecialchars($statusNoticeTitle); ?></h5>
                    <p class="mb-0"><?php echo htmlspecialchars($statusNoticeText); ?></p>
                </div>

                <div class="card main-card mb-4">
                    <div class="card-body">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-8">
                                <h2 class="h4 mb-2"><?php echo htmlspecialchars((string) ($nomination['libelle'] ?? 'Nomination')); ?></h2>
                                <p class="text-muted mb-3">
                                    Catégorie : <?php echo htmlspecialchars((string) ($nomination['categorie_nom'] ?? 'Catégorie')); ?>
                                    · Plateforme : <?php echo htmlspecialchars((string) ($nomination['plateforme'] ?? 'Plateforme')); ?>
                                </p>
                                <div class="copy-row">
                                    <input id="public-share-url" type="text" class="form-control" value="<?php echo htmlspecialchars($publicProfileUrl); ?>" readonly>
                                    <button type="button" class="btn btn-primary" onclick="copyValue('public-share-url', this)">
                                        <i class="fas fa-copy me-2"></i>Copier le lien
                                    </button>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="d-grid gap-2">
                                    <a href="<?php echo htmlspecialchars($nomineeProfileUrl . '?nomination=' . $selectedNominationId); ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-id-badge me-2"></i>Voir le profil public
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary" onclick="copyAllHashtags(this)">
                                        <i class="fas fa-hashtag me-2"></i>Copier les hashtags
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card main-card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-hashtag me-2"></i>Hashtags recommandés</h5>
                    </div>
                    <div class="card-body">
                        <div class="hashtag-list">
                            <?php foreach ($hashtags as $hashtag): ?>
                                <button type="button" class="hashtag-chip" onclick="copyRawText('<?php echo htmlspecialchars((string) $hashtag, ENT_QUOTES, 'UTF-8'); ?>', this)">
                                    <?php echo htmlspecialchars((string) $hashtag); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <?php foreach ($shareCards as $index => $shareCard): ?>
                        <div class="col-xl-4 col-md-6">
                            <div class="card main-card share-card">
                                <div class="card-body d-flex flex-column text-center h-100">
                                    <div class="share-icon mx-auto" style="background: <?php echo htmlspecialchars((string) $shareCard['color']); ?>;">
                                        <i class="<?php echo htmlspecialchars((string) $shareCard['icon']); ?>"></i>
                                    </div>
                                    <h3 class="h5 mb-3"><?php echo htmlspecialchars((string) $shareCard['label']); ?></h3>
                                    <div class="share-preview text-start mb-3"><?php echo htmlspecialchars((string) $shareCard['text']); ?></div>
                                    <div class="mt-auto d-grid gap-2">
                                        <button type="button" class="btn btn-primary" onclick="copyTextBlock('share-text-<?php echo (int) $index; ?>', this)">
                                            <i class="fas fa-copy me-2"></i>Copier le texte
                                        </button>
                                        <?php if (!empty($shareCard['shareUrl']) && !empty($shareCard['buttonClass']) && !empty($shareCard['buttonLabel'])): ?>
                                            <a class="btn <?php echo htmlspecialchars((string) $shareCard['buttonClass']); ?>" href="<?php echo htmlspecialchars((string) $shareCard['shareUrl']); ?>" target="_blank" rel="noopener noreferrer">
                                                <i class="fas fa-arrow-up-right-from-square me-2"></i><?php echo htmlspecialchars((string) $shareCard['buttonLabel']); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <textarea id="share-text-<?php echo (int) $index; ?>" class="d-none"><?php echo htmlspecialchars((string) $shareCard['text']); ?></textarea>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card main-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Conseils de diffusion</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="alert alert-info mb-0">
                                    <h6 class="mb-3"><i class="fas fa-check-circle me-2"></i>À faire</h6>
                                    <ul class="mb-0 ps-3">
                                        <li class="mb-2">Ajoutez le lien public dans votre bio et dans vos stories.</li>
                                        <li class="mb-2">Réutilisez le même message sur plusieurs canaux pour renforcer la mémorisation.</li>
                                        <li class="mb-2">Expliquez en une phrase pourquoi cette nomination compte pour vous.</li>
                                        <li>Remerciez vos soutiens après chaque relai important.</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-warning mb-0">
                                    <h6 class="mb-3"><i class="fas fa-triangle-exclamation me-2"></i>À éviter</h6>
                                    <ul class="mb-0 ps-3">
                                        <li class="mb-2">Ne spammez pas votre communauté avec le même message plusieurs fois par jour.</li>
                                        <li class="mb-2">N'utilisez pas de bots ni d'incitations trompeuses.</li>
                                        <li class="mb-2">Évitez les visuels illisibles ou les liens raccourcis opaques.</li>
                                        <li>Ne promettez pas de contrepartie en échange d'un vote.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function copyText(text) {
            try {
                await navigator.clipboard.writeText(text);
                return true;
            } catch (error) {
                return false;
            }
        }

        function flashButton(button, label) {
            const originalHtml = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check me-2"></i>' + label;
            setTimeout(() => { button.innerHTML = originalHtml; }, 1800);
        }

        async function copyValue(fieldId, button) {
            const field = document.getElementById(fieldId);
            const value = field ? field.value : '';
            if (await copyText(value)) {
                flashButton(button, 'Copié');
                return;
            }

            window.prompt('Copiez ce texte manuellement :', value);
        }

        async function copyTextBlock(fieldId, button) {
            const field = document.getElementById(fieldId);
            const value = field ? field.value : '';
            if (await copyText(value)) {
                flashButton(button, 'Copié');
                return;
            }

            window.prompt('Copiez ce texte manuellement :', value);
        }

        async function copyRawText(value, button) {
            if (await copyText(value)) {
                button.classList.add('btn-success');
                setTimeout(() => { button.classList.remove('btn-success'); }, 1200);
                return;
            }

            window.prompt('Copiez ce texte manuellement :', value);
        }

        async function copyAllHashtags(button) {
            const hashtags = <?php echo json_encode(array_values($hashtags), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>.join(' ');
            if (await copyText(hashtags)) {
                flashButton(button, 'Hashtags copiés');
                return;
            }

            window.prompt('Copiez ces hashtags manuellement :', hashtags);
        }
    </script>
</body>
</html>