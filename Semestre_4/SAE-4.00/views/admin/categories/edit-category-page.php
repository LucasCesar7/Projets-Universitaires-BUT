<?php require_once __DIR__ . '/../../partials/admin-header.php'; ?>

<link rel="stylesheet" href="<?php echo htmlspecialchars($adminCategoryFormCssUrl); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<section class="admin-category-form-page">
    <div class="admin-page-header">
        <div class="page-title">
            <h1><i class="fas fa-edit"></i> Modifier la catégorie</h1>
            <p>Mettez à jour les informations de "<?php echo htmlspecialchars($category->getNom()); ?>"</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo htmlspecialchars($manageCategoriesUrl); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <button id="deleteBtn"
                    type="button"
                    class="btn btn-danger"
                    data-category-id="<?php echo (int) $categoryId; ?>"
                    data-category-name="<?php echo htmlspecialchars($category->getNom()); ?>">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
    </div>

    <div class="form-container">
        <?php if ($errorMessage !== ''): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <?php if ($category->getImage()): ?>
            <div class="current-image-section" style="margin-bottom: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: var(--border-radius);">
                <h3 style="margin-bottom: 1rem; color: var(--dark-color); font-size: 1.1rem;">
                    <i class="fas fa-image"></i> Image actuelle
                </h3>
                <div id="currentImageContainer" style="text-align: center;">
                    <img src="<?php echo htmlspecialchars(appUrl('public/' . $category->getImage())); ?>"
                         alt="<?php echo htmlspecialchars($category->getNom()); ?>"
                         style="max-width: 100%; max-height: 300px; border-radius: var(--border-radius); border: 2px solid #e2e8f0;">
                    <div style="margin-top: 0.5rem; color: #64748b; font-size: 0.875rem;">
                        Chemin: <?php echo htmlspecialchars($category->getImage()); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($formActionUrl); ?>" enctype="multipart/form-data" id="categoryForm" class="category-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($updateCategoryToken, ENT_QUOTES, 'UTF-8'); ?>">

            <div class="form-group">
                <label for="nom" class="form-label required">
                    <i class="fas fa-heading"></i> Nom de la catégorie
                </label>
                <input type="text"
                       id="nom"
                       name="nom"
                       class="form-control"
                       required
                       value="<?php echo htmlspecialchars((string) $formData['nom']); ?>"
                       placeholder="Ex: Meilleur Créateur de Contenu"
                       maxlength="100">
                <small class="char-counter"><?php echo strlen((string) $formData['nom']); ?> / 100 caractères</small>
            </div>

            <div class="form-group">
                <label for="description" class="form-label required">
                    <i class="fas fa-align-left"></i> Description
                </label>
                <textarea id="description"
                          name="description"
                          class="form-control"
                          required
                          rows="5"
                          placeholder="Décrivez cette catégorie en détail..."
                          maxlength="2000"><?php echo htmlspecialchars((string) $formData['description']); ?></textarea>
                <div id="charCounter" class="char-counter"><?php echo strlen((string) $formData['description']); ?> / 2000 caractères</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="id_edition" class="form-label required">
                        <i class="fas fa-calendar-star"></i> Édition
                    </label>
                    <select id="id_edition" name="id_edition" class="form-control" required>
                        <option value="">Sélectionner une édition</option>
                        <?php foreach ($editions as $edition): ?>
                            <option value="<?php echo (int) $edition->getIdEdition(); ?>"
                                <?php echo ((int) $formData['id_edition'] === $edition->getIdEdition()) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($edition->getNom()); ?> (<?php echo htmlspecialchars((string) $edition->getAnnee()); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-mobile-screen"></i> Plateforme cible
                    </label>
                    <input type="hidden" id="plateforme_cible" name="plateforme_cible" value="<?php echo htmlspecialchars((string) $formData['plateforme_cible']); ?>">

                    <div class="platform-options">
                        <div class="platform-option" data-value="Toutes">
                            <i class="fas fa-globe platform-icon"></i>
                            <span>Toutes</span>
                        </div>
                        <div class="platform-option" data-value="TikTok">
                            <i class="fab fa-tiktok platform-icon platform-tiktok"></i>
                            <span>TikTok</span>
                        </div>
                        <div class="platform-option" data-value="Instagram">
                            <i class="fab fa-instagram platform-icon platform-instagram"></i>
                            <span>Instagram</span>
                        </div>
                        <div class="platform-option" data-value="YouTube">
                            <i class="fab fa-youtube platform-icon platform-youtube"></i>
                            <span>YouTube</span>
                        </div>
                        <div class="platform-option" data-value="Facebook">
                            <i class="fab fa-facebook platform-icon platform-facebook"></i>
                            <span>Facebook</span>
                        </div>
                        <div class="platform-option" data-value="X">
                            <i class="fab fa-x-twitter platform-icon platform-x"></i>
                            <span>X (Twitter)</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="limite_nomines" class="form-label">
                        <i class="fas fa-users"></i> Limite de nominés
                    </label>
                    <input type="number"
                           id="limite_nomines"
                           name="limite_nomines"
                           class="form-control"
                           min="1"
                           max="50"
                           value="<?php echo htmlspecialchars((string) $formData['limite_nomines']); ?>"
                           placeholder="Nombre maximum de nominés">
                    <small>Entre 1 et 50 nominés maximum</small>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-calendar-days"></i> Période de votes
                    </label>
                    <div class="date-picker-group">
                        <input type="datetime-local"
                               id="date_debut_votes"
                               name="date_debut_votes"
                               class="form-control"
                               value="<?php echo !empty($formData['date_debut_votes']) ? htmlspecialchars(str_replace(' ', 'T', (string) $formData['date_debut_votes'])) : ''; ?>">
                        <span class="date-separator">à</span>
                        <input type="datetime-local"
                               id="date_fin_votes"
                               name="date_fin_votes"
                               class="form-control"
                               value="<?php echo !empty($formData['date_fin_votes']) ? htmlspecialchars(str_replace(' ', 'T', (string) $formData['date_fin_votes'])) : ''; ?>">
                    </div>
                    <small>Optionnel - Laisser vide si pas de période spécifique</small>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-image"></i> Nouvelle image (optionnel)
                </label>
                <div class="file-upload">
                    <input type="file" id="image" name="image" accept="image/*">
                    <div class="file-upload-content">
                        <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                        <div class="file-upload-text">Cliquez ou glissez-déposez une nouvelle image</div>
                        <div class="file-upload-hint">JPG, PNG ou GIF (max. 2MB)</div>
                    </div>
                </div>
                <div id="filePreview" class="file-preview">
                    <img loading="lazy" id="previewImage" src="" alt="Aperçu de la nouvelle image">
                </div>
                <small>Laisser vide pour conserver l'image actuelle</small>

                <?php if ($category->getImage()): ?>
                    <div style="margin-top: 0.75rem;">
                        <label style="display: inline-flex; align-items: center; gap: 0.5rem; color: #64748b; font-size: 0.875rem; cursor: pointer;">
                            <input type="checkbox" name="remove_image" value="1">
                            <span>Supprimer cette image</span>
                        </label>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <a href="<?php echo htmlspecialchars($manageCategoriesUrl); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" id="submitBtn" class="btn btn-primary">
                    <i class="fas fa-save"></i> Sauvegarder les modifications
                </button>
            </div>
        </form>

        <form method="POST" action="<?php echo htmlspecialchars($manageCategoriesUrl); ?>" id="deleteCategoryForm" style="display: none;">
            <input type="hidden" name="delete_category_id" value="<?php echo (int) $categoryId; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($deleteCategoryToken, ENT_QUOTES, 'UTF-8'); ?>">
        </form>
    </div>
</section>

<script src="<?php echo htmlspecialchars($adminCategoryEditJsUrl); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const platformValue = <?php echo json_encode((string) $formData['plateforme_cible']); ?>;
    document.querySelectorAll('.platform-option').forEach(function(option) {
        if (option.getAttribute('data-value') === platformValue) {
            option.classList.add('selected');
        }
    });
});
</script>
</div>
</main>
</body>
</html>