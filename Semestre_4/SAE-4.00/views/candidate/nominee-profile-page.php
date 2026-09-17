<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil Public - Social Media Awards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($candidateCssUrl); ?>">
    <style>
        .profile-shell { display: grid; gap: 1.5rem; }
        .main-card { border: none; border-radius: 18px; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08); overflow: hidden; }
        .profile-hero { background: linear-gradient(135deg, #4FBDAB, #45a999); color: white; }
        .profile-hero .card-body { padding: 2rem; }
        .profile-avatar { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid rgba(255,255,255,0.7); background: rgba(255,255,255,0.15); }
        .profile-avatar-fallback { display: flex; align-items: center; justify-content: center; font-size: 2.75rem; font-weight: 700; }
        .profile-meta { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 1rem; }
        .profile-meta .badge { font-size: 0.95rem; padding: 0.65rem 0.9rem; }
        .status-active { background: rgba(40, 167, 69, 0.18); }
        .status-ended { background: rgba(108, 117, 125, 0.18); }
        .status-pending { background: rgba(255, 193, 7, 0.2); color: #5a4300; }
        .social-link-list { display: flex; flex-wrap: wrap; gap: 0.75rem; }
        .social-link { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.7rem 0.95rem; border-radius: 999px; background: rgba(79, 189, 171, 0.12); color: #12796b; text-decoration: none; }
        .social-link:hover { background: rgba(79, 189, 171, 0.2); color: #0f5f55; }
        .asset-frame { border-radius: 16px; overflow: hidden; background: #f8fafc; border: 1px solid rgba(15, 23, 42, 0.08); }
        .asset-frame img { width: 100%; display: block; object-fit: cover; }
        .copy-row { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .copy-row input { flex: 1 1 320px; }
        .selector-form { max-width: 360px; }
        .info-list { display: grid; gap: 1rem; }
        .info-item strong { display: block; color: #4FBDAB; margin-bottom: 0.2rem; }
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
                        <h1 class="page-title mb-2"><i class="fas fa-id-badge me-2"></i>Mon profil public</h1>
                        <p class="page-subtitle mb-0">Prévisualisez les informations visibles par les votants et vérifiez votre présence publique.</p>
                    </div>

                    <?php if (count($nominations) > 1): ?>
                        <div class="selector-form">
                            <label class="form-label" for="nomination-select">Nomination affichée</label>
                            <select id="nomination-select" class="form-select" onchange="window.location.href=this.value;">
                                <?php foreach ($nominations as $candidateNomination): ?>
                                    <?php $optionId = (int) ($candidateNomination['id_nomination'] ?? 0); ?>
                                    <option value="<?php echo htmlspecialchars($nomineeProfileUrl . '?nomination=' . $optionId); ?>" <?php echo $optionId === $selectedNominationId ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars((string) ($candidateNomination['libelle'] ?? ('Nomination #' . $optionId))); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="profile-shell">
                    <section class="card main-card profile-hero">
                        <div class="card-body">
                            <div class="row g-4 align-items-center">
                                <div class="col-lg-2 col-md-3 text-center text-md-start">
                                    <?php if ($profilePhotoUrl): ?>
                                        <img loading="lazy" src="<?php echo htmlspecialchars($profilePhotoUrl); ?>" alt="<?php echo htmlspecialchars((string) ($nomination['pseudonyme'] ?? 'Nominé')); ?>" class="profile-avatar">
                                    <?php else: ?>
                                        <div class="profile-avatar profile-avatar-fallback"><?php echo htmlspecialchars(substr((string) ($nomination['pseudonyme'] ?? 'N'), 0, 1)); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-lg-7 col-md-9">
                                    <h2 class="mb-2"><?php echo htmlspecialchars((string) ($nomination['pseudonyme'] ?? 'Nominé')); ?></h2>
                                    <p class="mb-3"><?php echo htmlspecialchars((string) ($nomination['bio'] ?? 'Candidat aux Social Media Awards')); ?></p>
                                    <div class="profile-meta">
                                        <span class="badge bg-light text-dark"><i class="fas fa-layer-group me-1"></i><?php echo htmlspecialchars((string) ($nomination['categorie_nom'] ?? 'Catégorie')); ?></span>
                                        <span class="badge bg-light text-dark"><i class="fas fa-display me-1"></i><?php echo htmlspecialchars((string) ($nomination['plateforme'] ?? 'Plateforme')); ?></span>
                                        <span class="badge <?php echo htmlspecialchars($votingStatusClass); ?>"><?php echo htmlspecialchars($votingStatusLabel); ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="d-grid gap-2">
                                        <a href="<?php echo htmlspecialchars($shareNominationUrl . '?nomination=' . $selectedNominationId); ?>" class="btn btn-light">
                                            <i class="fas fa-share-alt me-2"></i>Ouvrir le kit de partage
                                        </a>
                                        <a href="<?php echo htmlspecialchars($candidateProfileUrl); ?>" class="btn btn-outline-light">
                                            <i class="fas fa-user-edit me-2"></i>Modifier mon compte
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="row g-4">
                        <div class="col-lg-8">
                            <div class="card main-card mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-link me-2"></i>Lien public à partager</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Ce lien renvoie vers votre fiche publique visible depuis la page des nominés.</p>
                                    <div class="copy-row">
                                        <input id="public-profile-url" type="text" class="form-control" value="<?php echo htmlspecialchars($publicProfileUrl); ?>" readonly>
                                        <button type="button" class="btn btn-primary" onclick="copyFieldValue('public-profile-url', this)">
                                            <i class="fas fa-copy me-2"></i>Copier
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="card main-card mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-circle-info me-2"></i>Contenu affiché aux votants</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-4 align-items-start">
                                        <div class="col-md-7">
                                            <h3 class="h4 mb-3"><?php echo htmlspecialchars((string) ($nomination['libelle'] ?? 'Nomination')); ?></h3>
                                            <div class="info-list">
                                                <div class="info-item">
                                                    <strong>Catégorie</strong>
                                                    <span><?php echo htmlspecialchars((string) ($nomination['categorie_nom'] ?? 'Catégorie non renseignée')); ?></span>
                                                </div>
                                                <div class="info-item">
                                                    <strong>Plateforme</strong>
                                                    <span><?php echo htmlspecialchars((string) ($nomination['plateforme'] ?? 'Plateforme non renseignée')); ?></span>
                                                </div>
                                                <div class="info-item">
                                                    <strong>Statut des votes</strong>
                                                    <span><?php echo htmlspecialchars($votingStatusLabel); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <?php if ($nominationImageUrl): ?>
                                                <div class="asset-frame">
                                                    <img loading="lazy" src="<?php echo htmlspecialchars($nominationImageUrl); ?>" alt="<?php echo htmlspecialchars((string) ($nomination['libelle'] ?? 'Nomination')); ?>">
                                                </div>
                                            <?php else: ?>
                                                <div class="asset-frame p-4 text-center text-muted">
                                                    <i class="fas fa-image fa-2x mb-3"></i>
                                                    <p class="mb-0">Aucun visuel de nomination enregistré.</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card main-card">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-hashtag me-2"></i>Réseaux sociaux publics</h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($socialLinks !== []): ?>
                                        <div class="social-link-list">
                                            <?php foreach ($socialLinks as $socialLink): ?>
                                                <a href="<?php echo htmlspecialchars((string) $socialLink['url']); ?>" target="_blank" rel="noopener noreferrer" class="social-link">
                                                    <i class="<?php echo htmlspecialchars((string) $socialLink['icon']); ?>"></i>
                                                    <span><?php echo htmlspecialchars((string) $socialLink['label']); ?></span>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted mb-0">Aucun lien social public n'est encore renseigné pour cette nomination.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card main-card mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-shield-heart me-2"></i>Bonnes pratiques</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="mb-0 ps-3">
                                        <li class="mb-2">Gardez une bio courte, lisible et cohérente avec votre positionnement.</li>
                                        <li class="mb-2">Vérifiez que vos liens sociaux mènent bien à vos comptes publics actifs.</li>
                                        <li class="mb-2">Ajoutez un visuel clair pour améliorer la mémorisation de votre nomination.</li>
                                        <li>Utilisez le kit de partage pour diffuser un message homogène sur chaque canal.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card main-card">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Modification du profil</h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($votingStatusClass === 'status-active'): ?>
                                        <div class="alert alert-warning mb-3">
                                            <i class="fas fa-lock me-2"></i>Pendant les votes, certaines modifications de profil peuvent être limitées.
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info mb-3">
                                            <i class="fas fa-pen me-2"></i>Vous pouvez encore affiner vos informations publiques depuis votre espace compte.
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-grid gap-2">
                                        <a href="<?php echo htmlspecialchars($candidateProfileUrl); ?>" class="btn btn-primary">
                                            <i class="fas fa-user-edit me-2"></i>Accéder à mon compte
                                        </a>
                                        <a href="<?php echo htmlspecialchars($rulesUrl); ?>" class="btn btn-outline-primary">
                                            <i class="fas fa-file-contract me-2"></i>Lire le règlement
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function copyFieldValue(fieldId, button) {
            const field = document.getElementById(fieldId);
            const value = field ? field.value : '';

            try {
                await navigator.clipboard.writeText(value);
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check me-2"></i>Copié';
                setTimeout(() => { button.innerHTML = originalText; }, 1800);
            } catch (error) {
                window.prompt('Copiez ce lien manuellement :', value);
            }
        }
    </script>
</body>
</html>