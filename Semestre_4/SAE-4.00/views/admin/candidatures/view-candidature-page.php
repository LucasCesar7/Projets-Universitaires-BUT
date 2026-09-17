<?php require_once __DIR__ . '/../../partials/admin-header.php'; ?>

<link rel="stylesheet" href="<?php echo htmlspecialchars($adminCandidaturesCssUrl); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<section class="admin-candidature-detail-page">
    <div class="admin-page-header">
        <div class="page-title">
            <h1><i class="fas fa-eye"></i> Détails de la candidature #<?php echo (int) $candidatureId; ?></h1>
            <p><?php echo htmlspecialchars($candidature->getLibelle()); ?></p>
        </div>
        <div class="header-actions">
            <a href="<?php echo htmlspecialchars($manageCandidaturesUrl); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <?php if ($successFlash): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars((string) $successFlash); ?>
            </div>
        <?php endif; ?>

        <?php if ($errorFlash): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars((string) $errorFlash); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="admin-content">
        <div class="detail-grid">
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-info-circle"></i> Informations générales</h3>
                    <span class="status-badge status-<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $candidature->getStatut()))); ?>">
                        <i class="fas fa-circle"></i> <?php echo htmlspecialchars($candidature->getStatut()); ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <strong><i class="fas fa-user"></i> Candidat :</strong>
                        <div class="info-content">
                            <span class="candidate-name"><?php echo htmlspecialchars((string) ($candidature->getCandidatPseudonyme() ?? '')); ?></span>
                            <span class="candidate-email"><?php echo htmlspecialchars((string) ($candidature->getCandidatEmail() ?? '')); ?></span>
                        </div>
                    </div>

                    <div class="info-item">
                        <strong><i class="fas fa-globe"></i> Plateforme :</strong>
                        <span class="platform-badge platform-<?php echo htmlspecialchars(strtolower($candidature->getPlateforme())); ?>">
                            <?php echo htmlspecialchars($candidature->getPlateforme()); ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <strong><i class="fas fa-link"></i> Lien :</strong>
                        <a href="<?php echo htmlspecialchars($candidature->getUrlContenu()); ?>" target="_blank" rel="noreferrer" class="content-link">
                            <i class="fas fa-external-link-alt"></i> Ouvrir le contenu
                        </a>
                    </div>

                    <div class="info-item">
                        <strong><i class="fas fa-tag"></i> Catégorie :</strong>
                        <span class="category-tag"><?php echo htmlspecialchars((string) ($candidature->getCategorieNom() ?? '')); ?></span>
                    </div>

                    <div class="info-item">
                        <strong><i class="fas fa-trophy"></i> Édition :</strong>
                        <?php echo htmlspecialchars((string) ($candidature->getEditionNom() ?? '')); ?>
                    </div>

                    <div class="info-item">
                        <strong><i class="fas fa-calendar-alt"></i> Soumission :</strong>
                        <?php echo htmlspecialchars(date('d/m/Y à H:i', strtotime($candidature->getDateSoumission()))); ?>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-comment-alt"></i> Argumentaire</h3>
                </div>
                <div class="card-body">
                    <div class="argumentaire-content">
                        <?php echo nl2br(htmlspecialchars($candidature->getArgumentaire())); ?>
                    </div>
                </div>
            </div>

            <?php if ($candidature->getImage()): ?>
                <div class="info-card full-width">
                    <div class="card-header">
                        <h3><i class="fas fa-image"></i> Image soumise</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="image-container">
                            <img src="<?php echo htmlspecialchars($imageUrl); ?>"
                                alt="Image candidature"
                                class="submitted-image"
                                onerror="this.src='<?php echo htmlspecialchars(appUrl('assets/images/default-image.jpg')); ?>'">
                            <div class="image-actions">
                                <a href="<?php echo htmlspecialchars($imageUrl); ?>" target="_blank" rel="noreferrer" class="btn btn-sm btn-primary">
                                    <i class="fas fa-expand"></i> Agrandir
                                </a>
                                <button onclick="downloadImage()" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-download"></i> Télécharger
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <?php if ($candidature->getStatut() === 'En attente'): ?>
                <button class="btn btn-success" onclick="openProcessModal(<?php echo (int) $candidatureId; ?>, '<?php echo addslashes($candidature->getLibelle()); ?>', 'approve')">
                    <i class="fas fa-check"></i> Approuver
                </button>
                <button class="btn btn-danger" onclick="openProcessModal(<?php echo (int) $candidatureId; ?>, '<?php echo addslashes($candidature->getLibelle()); ?>', 'reject')">
                    <i class="fas fa-times"></i> Rejeter
                </button>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Cette candidature a déjà été traitée.
                </div>
            <?php endif; ?>

            <button onclick="openDeleteModal(<?php echo (int) $candidatureId; ?>, '<?php echo addslashes($candidature->getLibelle()); ?>')" class="btn btn-outline-danger">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
    </div>
</section>

<div id="processModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-cogs"></i> Traiter la candidature</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <p id="processModalTitle"></p>
            <form id="processForm" action="<?php echo htmlspecialchars($processCandidatureUrl); ?>" method="POST">
                <input type="hidden" id="processId" name="id">
                <input type="hidden" id="processAction" name="action">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($processCandidatureToken, ENT_QUOTES, 'UTF-8'); ?>">

                <div class="form-group">
                    <label for="processComment">Commentaire (optionnel):</label>
                    <textarea id="processComment" name="comment" class="form-control" rows="4" placeholder="Ajoutez un commentaire pour le candidat..."></textarea>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <small>Ce commentaire sera visible par le candidat.</small>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary close-modal">Annuler</button>
            <button class="btn btn-success" id="confirmProcessBtn">
                <i class="fas fa-check"></i> Confirmer
            </button>
        </div>
    </div>
</div>

<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-trash"></i> Confirmer la suppression</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <p id="deleteModalText"></p>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Attention:</strong> Cette action est irréversible.
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary close-modal">Annuler</button>
            <form id="deleteForm" action="<?php echo htmlspecialchars($deleteCandidatureUrl); ?>" method="POST">
                <input type="hidden" id="deleteCandidatureId" name="id">
                <input type="hidden" name="return_to_detail" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($deleteCandidatureToken, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo htmlspecialchars($adminCandidaturesJsUrl); ?>"></script>
<script>
    function downloadImage() {
        const link = document.createElement('a');
        link.href = '<?php echo addslashes($imageUrl); ?>';
        link.download = 'candidature-<?php echo (int) $candidatureId; ?>-' + new Date().getTime() + '.jpg';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function openProcessModal(id, libelle, action) {
        const modal = document.getElementById('processModal');
        const title = document.getElementById('processModalTitle');
        const processId = document.getElementById('processId');
        const processAction = document.getElementById('processAction');

        processId.value = id;
        processAction.value = action;

        if (action === 'approve') {
            title.innerHTML = `Approuver la candidature: <strong>"${libelle}"</strong>`;
            document.getElementById('confirmProcessBtn').className = 'btn btn-success';
            document.getElementById('confirmProcessBtn').innerHTML = '<i class="fas fa-check"></i> Approuver';
        } else {
            title.innerHTML = `Rejeter la candidature: <strong>"${libelle}"</strong>`;
            document.getElementById('confirmProcessBtn').className = 'btn btn-danger';
            document.getElementById('confirmProcessBtn').innerHTML = '<i class="fas fa-times"></i> Rejeter';
        }

        modal.style.display = 'flex';
    }

    function openDeleteModal(id, libelle) {
        const modal = document.getElementById('deleteModal');
        const text = document.getElementById('deleteModalText');
        const deleteId = document.getElementById('deleteCandidatureId');

        text.innerHTML = `Êtes-vous sûr de vouloir supprimer définitivement la candidature <strong>"${libelle}"</strong> ?`;
        deleteId.value = id;

        modal.style.display = 'flex';
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('confirmProcessBtn').addEventListener('click', function () {
            document.getElementById('processForm').submit();
        });

        document.querySelectorAll('.close-modal').forEach(function (button) {
            button.addEventListener('click', function () {
                document.querySelectorAll('.modal').forEach(function (modal) {
                    modal.style.display = 'none';
                });
            });
        });
    });
</script>
</div>
</main>
</body>
</html>