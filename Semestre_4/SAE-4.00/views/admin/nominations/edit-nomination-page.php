<?php require_once __DIR__ . '/../../partials/admin-header.php'; ?>

<link rel="stylesheet" href="<?php echo htmlspecialchars($adminNominationsCssUrl); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .admin-form-card {
        background: #fff;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }

    .admin-form-grid {
        display: grid;
        gap: 1.25rem;
    }

    .admin-form-row {
        display: grid;
        gap: 1.25rem;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #0f172a;
    }

    .form-control,
    .form-select,
    .form-textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 0.85rem 1rem;
        font: inherit;
    }

    .form-textarea {
        min-height: 180px;
        resize: vertical;
    }

    .readonly-field {
        background: #f8fafc;
        color: #475569;
    }

    .current-image {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .current-image img {
        width: min(100%, 360px);
        border-radius: 14px;
        border: 1px solid #cbd5e1;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        flex-wrap: wrap;
        margin-top: 1rem;
    }

    .helper-text {
        color: #64748b;
        font-size: 0.9rem;
    }
</style>

<section class="admin-content">
    <header class="admin-header mb-4">
        <div class="header-left">
            <h1><i class="fas fa-edit"></i> Modifier la nomination</h1>
            <nav class="breadcrumb">
                <a href="<?php echo htmlspecialchars($adminDashboardUrl); ?>">Tableau de bord</a>
                <span> &gt; </span>
                <a href="<?php echo htmlspecialchars($manageNominationsUrl); ?>">Nominations</a>
                <span> &gt; </span>
                <span>Modification</span>
            </nav>
        </div>
        <div class="header-actions">
            <a href="<?php echo htmlspecialchars($viewNominationUrl . '?id=' . $nominationId); ?>" class="btn btn-secondary">
                <i class="fas fa-eye"></i> Voir la fiche
            </a>
            <a href="<?php echo htmlspecialchars($manageNominationsUrl); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
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

    <div class="admin-form-card">
        <form method="POST" enctype="multipart/form-data" class="admin-form-grid">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($updateNominationToken, ENT_QUOTES, 'UTF-8'); ?>">

            <div class="admin-form-row">
                <div class="form-group">
                    <label class="form-label" for="titre">Titre</label>
                    <input class="form-control" type="text" id="titre" name="titre" required maxlength="255" value="<?php echo htmlspecialchars($nomination->getLibelle()); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="plateforme">Plateforme</label>
                    <select class="form-select" id="plateforme" name="plateforme" required>
                        <?php foreach ($platforms as $platform): ?>
                            <option value="<?php echo htmlspecialchars($platform); ?>" <?php echo $nomination->getPlateforme() === $platform ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($platform); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="admin-form-row">
                <div class="form-group">
                    <label class="form-label" for="categorie_nom">Catégorie</label>
                    <input class="form-control readonly-field" type="text" id="categorie_nom" value="<?php echo htmlspecialchars($currentCategoryName); ?>" readonly>
                    <span class="helper-text">La création des nominations reste pilotée depuis les candidatures approuvées.</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="lien_contenu">Lien du contenu</label>
                    <input class="form-control" type="url" id="lien_contenu" name="lien_contenu" required value="<?php echo htmlspecialchars($nomination->getUrlContenu()); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="argumentation">Argumentaire</label>
                <textarea class="form-textarea" id="argumentation" name="argumentation" required><?php echo htmlspecialchars($nomination->getArgumentaire()); ?></textarea>
            </div>

            <div class="admin-form-row">
                <div class="form-group current-image">
                    <label class="form-label">Image actuelle</label>
                    <img loading="lazy" src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars($nomination->getLibelle()); ?>">
                    <label>
                        <input type="checkbox" name="remove_image" value="1">
                        Supprimer l'image actuelle
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-label" for="image_file">Nouvelle image</label>
                    <input class="form-control" type="file" id="image_file" name="image_file" accept="image/jpeg,image/png,image/gif">
                    <span class="helper-text">Formats acceptés : JPG, PNG, GIF. Taille maximale : 5 MB.</span>
                </div>
            </div>

            <div class="form-actions">
                <a href="<?php echo htmlspecialchars($viewNominationUrl . '?id=' . $nominationId); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</section>
</div>
</main>
</body>
</html>