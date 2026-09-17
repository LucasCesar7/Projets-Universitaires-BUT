<?php require_once __DIR__ . '/../../partials/admin-header.php'; ?>

<link rel="stylesheet" href="<?php echo htmlspecialchars($adminNominationsCssUrl); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/brands.min.css">

<section class="admin-content">
    <header class="admin-header mb-4">
        <div class="header-left">
            <h1><i class="fas fa-eye"></i> Détails de la nomination</h1>
            <nav class="breadcrumb">
                <a href="<?php echo htmlspecialchars($adminDashboardUrl); ?>">Tableau de bord</a>
                <span> &gt; </span>
                <a href="<?php echo htmlspecialchars($manageNominationsUrl); ?>">Nominations</a>
                <span> &gt; </span>
                <span>Détails</span>
            </nav>
        </div>
        <div class="header-actions">
            <a href="<?php echo htmlspecialchars($manageNominationsUrl); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="<?php echo htmlspecialchars($editNominationBaseUrl . '?id=' . $nominationId); ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
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

    <div class="detail-grid">
        <div class="card card-main">
            <div class="card-header">
                <h3><i class="fas fa-info-circle"></i> Informations principales</h3>
            </div>
            <div class="card-body">
                <div class="detail-main">
                    <div class="detail-image">
                        <img loading="lazy" src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars($nomination->getLibelle()); ?>" class="nomination-image">
                    </div>
                    <div class="detail-info">
                        <h2 class="nomination-title"><?php echo htmlspecialchars($nomination->getLibelle()); ?></h2>

                        <div class="detail-meta">
                            <div class="meta-item">
                                <span class="meta-label"><i class="fas fa-layer-group"></i> Catégorie :</span>
                                <span class="meta-value"><span class="badge badge-teal"><?php echo htmlspecialchars($categoryName); ?></span></span>
                            </div>

                            <div class="meta-item">
                                <span class="meta-label"><i class="fas fa-thumbs-up"></i> Votes :</span>
                                <span class="meta-value"><span class="badge badge-blue"><i class="fas fa-vote-yea"></i> <?php echo number_format($voteCount); ?> votes</span></span>
                            </div>

                            <div class="meta-item">
                                <span class="meta-label"><i class="fas fa-calendar-check"></i> Date d'approbation :</span>
                                <span class="meta-value"><?php echo htmlspecialchars($approvalDate); ?></span>
                            </div>

                            <div class="meta-item">
                                <span class="meta-label"><i class="fas fa-user-check"></i> Approuvé par :</span>
                                <span class="meta-value">
                                    <?php if ($approvedBy): ?>
                                        <?php echo htmlspecialchars((string) ($approvedBy['pseudonyme'] ?? 'Inconnu')); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Inconnu</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-desktop"></i> Plateforme et contenu</h3>
            </div>
            <div class="card-body">
                <div class="detail-section">
                    <div class="platform-display">
                        <span class="platform-badge large">
                            <i class="fab <?php echo htmlspecialchars($platformIcon); ?>"></i>
                            <?php echo htmlspecialchars($nomination->getPlateforme()); ?>
                        </span>
                    </div>

                    <div class="content-link">
                        <h4><i class="fas fa-link"></i> Lien du contenu</h4>
                        <a href="<?php echo htmlspecialchars($nomination->getUrlContenu()); ?>" target="_blank" class="content-url">
                            <i class="fas fa-external-link-alt"></i>
                            <?php echo htmlspecialchars($nomination->getUrlContenu()); ?>
                        </a>
                    </div>

                    <?php if ($submittedBy): ?>
                    <div class="submitter-info">
                        <h4><i class="fas fa-user-plus"></i> Soumis par</h4>
                        <div class="user-card">
                            <i class="fas fa-user-circle user-icon"></i>
                            <div class="user-details">
                                <strong><?php echo htmlspecialchars((string) ($submittedBy['pseudonyme'] ?? 'Inconnu')); ?></strong>
                                <div class="text-muted small"><?php echo htmlspecialchars((string) ($submittedBy['email'] ?? '')); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card card-argument">
            <div class="card-header">
                <h3><i class="fas fa-comment-dots"></i> Argumentaire</h3>
            </div>
            <div class="card-body">
                <div class="argument-content">
                    <?php echo nl2br(htmlspecialchars($nomination->getArgumentaire())); ?>
                </div>
                <div class="argument-stats">
                    <span class="text-muted">
                        <i class="fas fa-text-height"></i>
                        <?php echo (int) $argumentLength; ?> caractères
                    </span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-cogs"></i> Actions</h3>
            </div>
            <div class="card-body">
                <div class="action-buttons-grid">
                    <a href="<?php echo htmlspecialchars($editNominationBaseUrl . '?id=' . $nominationId); ?>" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Modifier cette nomination
                    </a>

                    <a href="<?php echo htmlspecialchars($publicNominationUrl); ?>" class="btn btn-info btn-block" target="_blank" rel="noopener noreferrer">
                        <i class="fas fa-eye"></i> Voir en mode public
                    </a>

                    <form method="post" action="<?php echo htmlspecialchars($deleteNominationUrl); ?>" class="delete-form" onsubmit="return confirmDelete()">
                        <input type="hidden" name="id" value="<?php echo (int) $nominationId; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($deleteNominationToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> Supprimer cette nomination
                        </button>
                    </form>

                    <a href="<?php echo htmlspecialchars($manageNominationsUrl); ?>" class="btn btn-secondary btn-block">
                        <i class="fas fa-list"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>

        <div class="card card-technical">
            <div class="card-header">
                <h3><i class="fas fa-code"></i> Informations techniques</h3>
            </div>
            <div class="card-body">
                <div class="technical-grid">
                    <div class="tech-item">
                        <span class="tech-label">ID Nomination :</span>
                        <span class="tech-value"><?php echo (int) $nomination->getIdNomination(); ?></span>
                    </div>
                    <div class="tech-item">
                        <span class="tech-label">ID Candidature :</span>
                        <span class="tech-value"><?php echo (int) $nomination->getIdCandidature(); ?></span>
                    </div>
                    <div class="tech-item">
                        <span class="tech-label">ID Catégorie :</span>
                        <span class="tech-value"><?php echo (int) $nomination->getIdCategorie(); ?></span>
                    </div>
                    <div class="tech-item">
                        <span class="tech-label">ID Compte :</span>
                        <span class="tech-value"><?php echo $nomination->getIdCompte() ? (int) $nomination->getIdCompte() : 'N/A'; ?></span>
                    </div>
                    <div class="tech-item">
                        <span class="tech-label">ID Admin :</span>
                        <span class="tech-value"><?php echo $nomination->getIdAdmin() ? (int) $nomination->getIdAdmin() : 'N/A'; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
</main>

<script>
    function confirmDelete() {
        return confirm('Êtes-vous sûr de vouloir supprimer cette nomination ? Cette action est irréversible.');
    }
</script>
</body>
</html>