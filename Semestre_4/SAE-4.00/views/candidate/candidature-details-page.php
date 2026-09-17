<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Candidature - Social Media Awards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($candidateCssUrl); ?>">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo htmlspecialchars($candidateDashboardUrl); ?>">
                <i class="fas fa-trophy"></i>
                Social Media Awards
            </a>
            <div class="navbar-nav ms-auto">
                <a href="<?php echo htmlspecialchars($mesCandidaturesUrl); ?>" class="nav-link">
                    <i class="fas fa-arrow-left"></i> Retour aux candidatures
                </a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container-fluid">
            <div class="page-header mb-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-file-alt"></i> Détails de la Candidature
                        </h1>
                        <p class="page-subtitle">Informations complètes sur votre candidature</p>
                    </div>
                    <div class="d-flex gap-3">
                        <?php if ($isEditable): ?>
                        <a href="<?php echo htmlspecialchars($submitCandidatureUrl . '?edit=' . $candidatureId); ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <?php endif; ?>
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Imprimer
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="main-card mb-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle text-primary"></i>
                                        Informations Générales
                                    </h5>
                                </div>
                                <span class="badge bg-<?php echo htmlspecialchars($statusClass); ?>">
                                    <i class="fas fa-circle"></i> <?php echo htmlspecialchars($candidature->getStatut()); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <label for="candidature_titre"><i class="fas fa-heading"></i> Titre</label>
                                        <p id="candidature_titre" class="detail-value"><?php echo htmlspecialchars($candidature->getLibelle()); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <label for="candidature_plateforme"><i class="<?php echo htmlspecialchars($platformIcon); ?>"></i> Plateforme</label>
                                        <p id="candidature_plateforme" class="detail-value"><?php echo htmlspecialchars($candidature->getPlateforme()); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <label for="candidature_url_contenu"><i class="fas fa-link"></i> URL du Contenu</label>
                                        <p id="candidature_url_contenu" class="detail-value">
                                            <a href="<?php echo htmlspecialchars($candidature->getUrlContenu()); ?>" target="_blank" class="text-primary text-decoration-none">
                                                <i class="fas fa-external-link-alt"></i> Voir le contenu
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <label for="candidature_date_soumission"><i class="fas fa-calendar"></i> Date de Soumission</label>
                                        <p id="candidature_date_soumission" class="detail-value"><?php echo htmlspecialchars($dateSoumission); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="main-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-comment-dots text-primary"></i> Argumentaire
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="argumentaire-content">
                                <?php echo nl2br(htmlspecialchars($candidature->getArgumentaire())); ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($candidatureImageUrl): ?>
                    <div class="main-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-image text-primary"></i> Image de Présentation
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="image-preview-detail">
                                <img loading="lazy" src="<?php echo htmlspecialchars($candidatureImageUrl); ?>" alt="Image de présentation pour la candidature <?php echo htmlspecialchars($candidature->getLibelle()); ?>" class="img-fluid rounded shadow">
                                <div class="mt-3">
                                    <a href="<?php echo htmlspecialchars($candidatureImageUrl); ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-expand"></i> Voir en grand
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <div class="sidebar-card mb-4">
                        <div class="card-body">
                            <h6 class="sidebar-title">
                                <i class="fas fa-layer-group"></i> Contexte
                            </h6>
                            <div class="context-info">
                                <?php if ($edition): ?>
                                <div class="context-item mb-3">
                                    <label for="candidature_edition"><i class="fas fa-calendar-alt"></i> Édition</label>
                                    <p id="candidature_edition" class="context-value">
                                        <?php echo htmlspecialchars($edition->getNom()); ?>
                                        <?php if ($edition->getAnnee()): ?>
                                            <span class="text-muted">(<?php echo htmlspecialchars((string) $edition->getAnnee()); ?>)</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <?php endif; ?>
                                <?php if ($category): ?>
                                <div class="context-item">
                                    <label for="candidature_categorie"><i class="fas fa-tag"></i> Catégorie</label>
                                    <p id="candidature_categorie" class="context-value"><?php echo htmlspecialchars($category->getNom()); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-card mb-4">
                        <div class="card-body">
                            <h6 class="sidebar-title">
                                <i class="fas fa-history"></i> Historique du Statut
                            </h6>
                            <div class="status-timeline">
                                <div class="timeline-item completed">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <h6>Soumission</h6>
                                        <p class="text-muted small"><?php echo htmlspecialchars($dateSoumission); ?></p>
                                    </div>
                                </div>
                                <div class="timeline-item <?php echo $candidature->getStatut() !== 'En attente' ? 'completed' : 'current'; ?>">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <h6>Évaluation</h6>
                                        <p class="text-muted small">
                                            <?php if ($candidature->getStatut() === 'En attente'): ?>
                                                En cours d'examen
                                            <?php else: ?>
                                                Terminé le <?php echo date('d/m/Y'); ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="timeline-item <?php echo $candidature->getStatut() !== 'En attente' ? 'completed' : ''; ?>">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <h6>Décision</h6>
                                        <p class="text-muted small"><?php echo htmlspecialchars($candidature->getStatut()); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-card">
                        <div class="card-body">
                            <h6 class="sidebar-title">
                                <i class="fas fa-cogs"></i> Actions
                            </h6>
                            <div class="action-buttons-vertical">
                                <?php if ($isEditable): ?>
                                <a href="<?php echo htmlspecialchars($submitCandidatureUrl . '?edit=' . $candidatureId); ?>" class="btn-action btn-outline-primary w-100 mb-2">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <?php endif; ?>
                                <a href="<?php echo htmlspecialchars($mesCandidaturesUrl); ?>" class="btn-action btn-outline-secondary w-100 mb-2">
                                    <i class="fas fa-list"></i> Retour à la liste
                                </a>
                                <button onclick="window.print()" class="btn-action btn-outline-info w-100 mb-2">
                                    <i class="fas fa-print"></i> Imprimer
                                </button>
                                <button onclick="shareCandidature()" class="btn-action btn-outline-success w-100">
                                    <i class="fas fa-share-alt"></i> Partager
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container-fluid">
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Social Media Awards. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

        async function shareCandidature() {
            const title = <?php echo json_encode($candidature->getLibelle(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
            const text = 'Découvrez ma candidature aux Social Media Awards !';
            const url = window.location.href;

            if (navigator.share) {
                try {
                    await navigator.share({
                        title: title,
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

            const copied = await copyShareText(url);

            if (copied) {
                alert('Lien copié dans le presse-papier !');
                return;
            }

            window.prompt('Copiez ce lien manuellement :', url);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const timelineItems = document.querySelectorAll('.timeline-item');
            timelineItems.forEach((item, index) => {
                item.style.animationDelay = `${index * 0.2}s`;
            });
        });
    </script>
</body>
</html>