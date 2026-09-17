<?php require_once __DIR__ . '/../../partials/admin-header.php'; ?>

<link rel="stylesheet" href="<?php echo htmlspecialchars($adminEditionsCssUrl); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php
$successMessage = $successCode === '1' ? 'Opération réussie !' : null;
$errorMessage = $errorCode === '1' ? 'Erreur lors de l\'opération.' : null;
?>

<section class="admin-editions-page">
    <div class="admin-page-header">
        <div class="page-title">
            <h1><i class="fas fa-calendar-alt"></i> Gestion des éditions</h1>
            <p><?php echo count($editions); ?> édition(s)</p>
        </div>
        <a href="<?php echo htmlspecialchars($addEditionUrl); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle édition
        </a>
    </div>

    <div class="admin-content">
        <?php if ($successMessage !== null): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>

        <?php if ($errorMessage !== null): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Une édition est active entre le début des candidatures et la fin des votes.
        </div>

        <?php if ($editions === []): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times fa-4x"></i>
                <h3>Aucune édition trouvée</h3>
                <p>Créez votre première édition pour commencer.</p>
                <a href="<?php echo htmlspecialchars($addEditionUrl); ?>" class="btn btn-primary">Nouvelle édition</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ÉDITION</th>
                            <th>PÉRIODE</th>
                            <th>STATUT</th>
                            <th>CATÉGORIES</th>
                            <th>CANDIDATURES</th>
                            <th>VOTANTS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($editions as $edition): ?>
                            <?php
                            $statusText = $edition->getEstActive() ? 'Active' : 'Terminée';
                            $statusClass = $edition->getEstActive() ? 'active' : 'finished';
                            ?>
                            <tr>
                                <td>
                                    <div class="edition-name">
                                        <?php echo htmlspecialchars($edition->getNom()); ?>
                                        <small><?php echo htmlspecialchars((string) $edition->getAnnee()); ?></small>
                                    </div>
                                </td>
                                <td>
                                    Du <?php echo htmlspecialchars(date('d/m/Y', strtotime($edition->getDateDebutCandidatures()))); ?>
                                    au <?php echo htmlspecialchars(date('d/m/Y', strtotime($edition->getDateFin()))); ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo htmlspecialchars($statusClass); ?>">
                                        <?php echo htmlspecialchars($statusText); ?>
                                    </span>
                                </td>
                                <td><strong><?php echo (int) $edition->getNbCategories(); ?></strong></td>
                                <td><strong><?php echo (int) $edition->getNbCandidatures(); ?></strong></td>
                                <td><strong><?php echo (int) $edition->getNbVotants(); ?></strong></td>
                                <td class="actions-cell">
                                    <a href="<?php echo htmlspecialchars($editEditionBaseUrl . '?id=' . $edition->getIdEdition()); ?>" class="action-btn edit" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="<?php echo htmlspecialchars($manageEditionsUrl); ?>" style="display: inline;"
                                          onsubmit="return confirmDelete(<?php echo (int) $edition->getIdEdition(); ?>, '<?php echo addslashes($edition->getNom()); ?>')">
                                        <input type="hidden" name="delete_edition_id" value="<?php echo (int) $edition->getIdEdition(); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($deleteEditionToken, ENT_QUOTES, 'UTF-8'); ?>">
                                        <button type="submit" class="action-btn delete" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<script src="<?php echo htmlspecialchars($adminEditionsJsUrl); ?>"></script>
</div>
</main>
</body>
</html>