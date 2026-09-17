<?php require_once __DIR__ . '/../../partials/admin-header.php'; ?>

<link rel="stylesheet" href="<?php echo htmlspecialchars($adminCandidaturesCssUrl); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<section class="admin-candidatures-page">
    <div class="admin-page-header">
        <div class="page-title">
            <h1><i class="fas fa-file-alt"></i> Gestion des candidatures</h1>
            <p><?php echo (int) $totalCandidatures; ?> candidature(s) trouvée(s)</p>
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
        <div class="stats-cards">
            <div class="stat-card total">
                <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo (int) ($stats['total'] ?? 0); ?></div>
                    <h3>Total</h3>
                </div>
            </div>
            <div class="stat-card pending">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo (int) ($stats['pending'] ?? 0); ?></div>
                    <h3>En attente</h3>
                </div>
            </div>
            <div class="stat-card approved">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo (int) ($stats['approved'] ?? 0); ?></div>
                    <h3>Approuvées</h3>
                </div>
            </div>
            <div class="stat-card rejected">
                <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo (int) ($stats['rejected'] ?? 0); ?></div>
                    <h3>Rejetées</h3>
                </div>
            </div>
        </div>

        <div class="table-controls">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Rechercher par nom, email, plateforme...">
            </div>
            <div class="filter-options">
                <select id="statusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="En attente">En attente</option>
                    <option value="Approuvée">Approuvées</option>
                    <option value="Rejetée">Rejetées</option>
                </select>
                <select id="categoryFilter">
                    <option value="">Toutes catégories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars((string) $category->getNom()); ?>"><?php echo htmlspecialchars((string) $category->getNom()); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="enhanced-table" id="candidaturesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Candidature</th>
                        <th>Candidat</th>
                        <th>Plateforme</th>
                        <th>Catégorie</th>
                        <th>Soumission</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($candidatures === []): ?>
                        <tr>
                            <td colspan="8" class="text-center">Aucune candidature trouvée</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($candidatures as $candidature): ?>
                            <?php
                            $platform = strtolower($candidature->getPlateforme());
                            $icon = match ($platform) {
                                'instagram' => 'instagram',
                                'tiktok' => 'tiktok',
                                'youtube' => 'youtube',
                                'facebook' => 'facebook',
                                'x', 'twitter' => 'x-twitter',
                                default => 'globe',
                            };
                            ?>
                            <tr data-status="<?php echo htmlspecialchars($candidature->getStatut()); ?>" data-category="<?php echo htmlspecialchars((string) ($candidature->getCategorieNom() ?? '')); ?>">
                                <td class="id-column">#<?php echo (int) $candidature->getIdCandidature(); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($candidature->getLibelle()); ?></strong><br>
                                    <small class="text-muted">Édition: <?php echo htmlspecialchars((string) ($candidature->getEditionNom() ?? '')); ?></small>
                                </td>
                                <td>
                                    <div class="candidate-info">
                                        <div class="candidate-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars((string) ($candidature->getCandidatPseudonyme() ?? '')); ?></strong><br>
                                            <small><?php echo htmlspecialchars((string) ($candidature->getCandidatEmail() ?? '')); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="platform-badge platform-<?php echo htmlspecialchars($platform); ?>">
                                        <i class="fab fa-<?php echo htmlspecialchars($icon); ?>"></i>
                                        <?php echo htmlspecialchars($candidature->getPlateforme()); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="category-tag"><?php echo htmlspecialchars((string) ($candidature->getCategorieNom() ?? '')); ?></span>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <div><?php echo htmlspecialchars(date('d/m/Y', strtotime($candidature->getDateSoumission()))); ?></div>
                                        <small><?php echo htmlspecialchars(date('H:i', strtotime($candidature->getDateSoumission()))); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $candidature->getStatut()))); ?>">
                                        <i class="fas fa-circle"></i>
                                        <?php echo htmlspecialchars($candidature->getStatut()); ?>
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <a href="<?php echo htmlspecialchars($viewCandidatureBaseUrl . '?id=' . $candidature->getIdCandidature()); ?>" class="action-btn view" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($candidature->getStatut() === 'En attente'): ?>
                                            <button class="action-btn process" onclick="openProcessModal(<?php echo (int) $candidature->getIdCandidature(); ?>, '<?php echo addslashes($candidature->getLibelle()); ?>', 'approve')" title="Approuver">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="action-btn reject" onclick="openProcessModal(<?php echo (int) $candidature->getIdCandidature(); ?>, '<?php echo addslashes($candidature->getLibelle()); ?>', 'reject')" title="Rejeter">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="action-btn status" disabled title="Déjà traité">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button onclick="openDeleteModal(<?php echo (int) $candidature->getIdCandidature(); ?>, '<?php echo addslashes($candidature->getLibelle()); ?>')" class="action-btn delete" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="pagination-nav" aria-label="Pagination des candidatures">
                <ul class="pagination">
                    <?php if ($currentPage > 1): ?>
                        <li><a href="<?php echo htmlspecialchars($manageCandidaturesUrl . '?page=' . ($currentPage - 1)); ?>" class="pagination-btn">&laquo; Préc.</a></li>
                    <?php endif; ?>
                    <?php for ($page = max(1, $currentPage - 2); $page <= min($totalPages, $currentPage + 2); $page++): ?>
                        <li><a href="<?php echo htmlspecialchars($manageCandidaturesUrl . '?page=' . $page); ?>" class="pagination-btn <?php echo $page === $currentPage ? 'active' : ''; ?>"><?php echo (int) $page; ?></a></li>
                    <?php endfor; ?>
                    <?php if ($currentPage < $totalPages): ?>
                        <li><a href="<?php echo htmlspecialchars($manageCandidaturesUrl . '?page=' . ($currentPage + 1)); ?>" class="pagination-btn">Suiv. &raquo;</a></li>
                    <?php endif; ?>
                </ul>
                <p class="pagination-info">Page <?php echo (int) $currentPage; ?> sur <?php echo (int) $totalPages; ?> · <?php echo (int) $totalCandidatures; ?> candidature(s) au total</p>
            </nav>
        <?php endif; ?>
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