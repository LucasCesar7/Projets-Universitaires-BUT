<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editId ? 'Modifier la Candidature' : 'Nouvelle Candidature'; ?> - Social Media Awards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($candidateCssUrl); ?>">
    <style>
        .plateforme-icon { font-size: 1.2em; margin-right: 8px; }
        .tiktok { color: #000000; }
        .instagram { color: #E4405F; }
        .youtube { color: #FF0000; }
        .x { color: #000000; }
        .facebook { color: #1877F2; }
        .twitch { color: #9146FF; }
        .plateforme-badge { position: relative; transition: all 0.3s ease; cursor: pointer; padding: 10px 15px; border: 2px solid #dee2e6; border-radius: 8px; display: inline-flex; align-items: center; margin: 5px; background: white; }
        .plateforme-badge:hover:not(.platform-disabled) { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .plateforme-badge.active { border-color: #4FBDAB !important; background: rgba(79, 189, 171, 0.1); }
        .file-upload-required { border-color: #dc3545 !important; background: linear-gradient(135deg, rgba(220, 53, 69, 0.05), rgba(220, 53, 69, 0.02)) !important; }
        .file-upload-valid { border-color: #28a745 !important; background: linear-gradient(135deg, rgba(40, 167, 69, 0.05), rgba(40, 167, 69, 0.02)) !important; }
        .validation-error-file { color: #dc3545; font-size: 0.875rem; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
        .platform-used-badge { position: absolute; top: -5px; right: -5px; background: #28a745; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 10px; display: flex; align-items: center; justify-content: center; }
        .platform-disabled { opacity: 0.6; cursor: not-allowed; }
        .platform-disabled:hover { border-color: #dee2e6 !important; transform: none !important; box-shadow: none !important; }
        .platform-info { font-size: 0.85rem; margin-top: 5px; padding: 10px; border-radius: 5px; background: #f8f9fa; }
        .category-platform-info { background: #f8f9fa; border-radius: 8px; padding: 10px; margin-top: 10px; font-size: 0.85rem; }
        .platform-badge-used { display: inline-block; background: #28a745; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; margin: 2px; }
        .platform-badge-available { display: inline-block; background: #17a2b8; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; margin: 2px; }
        .category-info { background: #e7f3ff; border-left: 4px solid #0d6efd; padding: 10px; border-radius: 5px; margin-top: 10px; font-size: 0.9rem; }
        .category-info h6 { margin-bottom: 5px; color: #0d6efd; }
        .form-section { background: white; border-radius: 10px; padding: 25px; margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-section h3 { color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #4FBDAB; }
        .form-control-candidature, .form-select-candidature { border: 2px solid #dee2e6; border-radius: 8px; padding: 10px 15px; transition: all 0.3s ease; }
        .form-control-candidature:focus, .form-select-candidature:focus { border-color: #4FBDAB; box-shadow: 0 0 0 0.2rem rgba(79, 189, 171, 0.25); }
        .form-textarea-candidature { min-height: 150px; resize: vertical; }
        .char-counter { text-align: right; font-size: 0.85rem; color: #6c757d; margin-top: 5px; }
        .char-counter.warning { color: #ffc107; }
        .char-counter.danger { color: #dc3545; }
        .required { color: #dc3545; }
        .help-text { font-size: 0.85rem; color: #6c757d; margin-top: 5px; }
        .file-upload-candidature { border: 2px dashed #dee2e6; border-radius: 10px; padding: 40px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease; background: #f8f9fa; }
        .file-upload-candidature:hover { border-color: #4FBDAB; background: rgba(79, 189, 171, 0.05); }
        .file-icon { font-size: 48px; color: #6c757d; margin-bottom: 15px; }
        .image-preview { max-width: 300px; max-height: 200px; border-radius: 8px; margin-top: 10px; border: 2px solid #dee2e6; }
        .btn-submit-candidature { background: linear-gradient(135deg, #4FBDAB, #3A9E8D); color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; }
        .btn-submit-candidature:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(79, 189, 171, 0.3); }
        .btn-submit-candidature:disabled { opacity: 0.6; cursor: not-allowed; }
        .edition-date-info { font-size: 0.8rem; color: #6c757d; margin-top: 2px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="<?php echo htmlspecialchars($candidateDashboardUrl); ?>">
                <i class="fas fa-trophy"></i>
                Social Media Awards
            </a>
            <div class="navbar-nav">
                <a class="nav-link" href="<?php echo htmlspecialchars($candidateDashboardUrl); ?>">
                    <i class="fas fa-home"></i> Tableau de bord
                </a>
                <a class="nav-link" href="<?php echo htmlspecialchars($mesCandidaturesUrl); ?>">
                    <i class="fas fa-list"></i> Mes candidatures
                </a>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="mb-4">
                    <h1 class="h2">
                        <i class="fas fa-file-import"></i>
                        <?php echo $editId ? 'Modifier la Candidature' : 'Nouvelle Candidature'; ?>
                    </h1>
                    <p class="text-muted">Remplissez soigneusement tous les champs pour soumettre votre candidature</p>

                    <?php if ($errorFlash): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo htmlspecialchars((string) $errorFlash); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($successFlash): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i>
                            <?php echo htmlspecialchars((string) $successFlash); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="alert alert-info mb-4">
                    <h5 class="alert-heading">
                        <i class="fas fa-info-circle"></i> Informations importantes
                    </h5>
                    <div class="row">
                        <div class="col-md-8">
                            <ul class="mb-0">
                                <li>Tous les champs sont obligatoires, y compris l'image</li>
                                <li>Vous pouvez soumettre une candidature par plateforme dans chaque catégorie</li>
                                <li>L'image doit être de bonne qualité (max 5MB)</li>
                                <li>L'argumentaire doit contenir au moins 200 caractères</li>
                                <li>Le contenu doit être original et publié récemment</li>
                            </ul>
                        </div>
                        <div class="col-md-4 text-end">
                            <strong>Règles :</strong>
                            <div class="small mt-2">
                                <span class="badge bg-success me-1">✓ Plateforme disponible</span>
                                <span class="badge bg-secondary me-1">✗ Déjà utilisé</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-body p-4">
                        <form method="post" enctype="multipart/form-data" id="candidatureForm">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($candidateSubmissionToken, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if ($editId): ?>
                                <input type="hidden" name="id_candidature" value="<?php echo (int) $editId; ?>">
                            <?php endif; ?>

                            <div class="form-section mb-4">
                                <h3><i class="fas fa-layer-group"></i> Sélection de l'Édition et Catégorie</h3>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="edition" class="form-label fw-bold">
                                            <i class="fas fa-calendar-alt"></i> Édition
                                            <span class="required">*</span>
                                        </label>
                                        <select id="edition" class="form-select" required>
                                            <option value="">Choisir une édition</option>
                                            <?php foreach ($selectableEditions as $edition): ?>
                                                <?php
                                                $dateFin = $edition->getDateFinCandidatures();
                                                $dateFinFormatted = date('d/m/Y', strtotime((string) $dateFin));
                                                $isSelected = ($defaultEditionId == $edition->getIdEdition());
                                                ?>
                                                <option value="<?php echo (int) $edition->getIdEdition(); ?>"
                                                    <?php echo $isSelected ? 'selected' : ''; ?>
                                                    data-date-fin="<?php echo htmlspecialchars((string) $dateFin); ?>"
                                                    data-date-fin-formatted="<?php echo htmlspecialchars($dateFinFormatted); ?>">
                                                    <?php echo htmlspecialchars((string) $edition->getNom()); ?>
                                                    <?php if ($edition->getAnnee()): ?>
                                                        (<?php echo htmlspecialchars((string) $edition->getAnnee()); ?>)
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="help-text">Sélectionnez l'édition à laquelle vous souhaitez participer</div>
                                        <?php if ($defaultEditionDate): ?>
                                            <div class="edition-date-info" id="editionDateInfo">
                                                Date limite de candidature: <?php echo date('d/m/Y', strtotime((string) $defaultEditionDate)); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="categorie" class="form-label fw-bold">
                                            <i class="fas fa-tag"></i> Catégorie
                                            <span class="required">*</span>
                                        </label>
                                        <select name="id_categorie" id="categorie" class="form-select" required>
                                            <option value="">Choisir une catégorie</option>
                                        </select>
                                        <div class="help-text">Choisissez la catégorie qui correspond le mieux à votre contenu</div>
                                        <div id="categoryDetails" class="category-info mt-2" style="display: none;">
                                            <h6>Détails de la catégorie:</h6>
                                            <div id="categoryDescription" class="small"></div>
                                            <div id="categoryPlatform" class="small mt-1"></div>
                                        </div>
                                        <div id="categoryPlatformInfo" class="category-platform-info mt-2" style="display: none;">
                                            <i class="fas fa-info-circle text-info"></i>
                                            <span id="platformInfoText" class="ms-1"></span>
                                        </div>
                                        <div id="loadingCategories" class="text-muted small mt-2" style="display: none;">
                                            <i class="fas fa-spinner fa-spin"></i> Chargement des catégories...
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section mb-4">
                                <h3><i class="fas fa-info-circle"></i> Informations de Base</h3>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="libelle" class="form-label fw-bold">
                                            <i class="fas fa-heading"></i> Titre de la Candidature
                                            <span class="required">*</span>
                                        </label>
                                        <input type="text" id="libelle" name="libelle"
                                            class="form-control"
                                            value="<?php echo $candidatureData ? htmlspecialchars((string) $candidatureData->getLibelle()) : ''; ?>"
                                            placeholder="Ex: Ma meilleure vidéo TikTok de l'année"
                                            required
                                            maxlength="255">
                                        <div class="char-counter" id="libelleCounter">0/255 caractères</div>
                                        <div class="help-text">Titre attractif qui résume votre candidature</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section mb-4">
                                <h3><i class="fas fa-share-alt"></i> Plateforme et Contenu</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-globe"></i> Plateforme
                                            <span class="required">*</span>
                                        </label>
                                        <div class="mb-3" id="platformBadges">
                                            <?php $platforms = [
                                                'TikTok' => ['icon' => 'fab fa-tiktok tiktok'],
                                                'Instagram' => ['icon' => 'fab fa-instagram instagram'],
                                                'YouTube' => ['icon' => 'fab fa-youtube youtube'],
                                                'X' => ['icon' => 'fab fa-x-twitter x'],
                                                'Facebook' => ['icon' => 'fab fa-facebook facebook'],
                                                'Twitch' => ['icon' => 'fab fa-twitch twitch'],
                                            ]; ?>
                                            <?php foreach ($platforms as $platform => $platformData): ?>
                                                <?php $isCurrent = ($currentPlateforme === $platform); ?>
                                                <div class="plateforme-badge"
                                                     data-platform="<?php echo htmlspecialchars($platform); ?>"
                                                     id="platform-badge-<?php echo htmlspecialchars($platform); ?>"
                                                     style="<?php echo $isCurrent ? 'border-color: #4FBDAB;' : ''; ?>">
                                                    <i class="<?php echo htmlspecialchars($platformData['icon']); ?>"></i>
                                                    <?php echo htmlspecialchars($platform); ?>
                                                    <span class="platform-used-badge" id="used-badge-<?php echo htmlspecialchars($platform); ?>" style="display: none;">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <input type="hidden" name="plateforme" id="plateformeInput"
                                            value="<?php echo htmlspecialchars($currentPlateforme); ?>" required>
                                        <div id="platformSelectionInfo" class="platform-info" style="display: none;">
                                            <i class="fas fa-check-circle text-success"></i>
                                            <span id="platformInfoMessage" class="ms-1"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="url_contenu" class="form-label fw-bold">
                                            <i class="fas fa-link"></i> URL du Contenu
                                            <span class="required">*</span>
                                        </label>
                                        <input type="url" id="url_contenu" name="url_contenu"
                                            class="form-control"
                                            value="<?php echo $candidatureData ? htmlspecialchars((string) $candidatureData->getUrlContenu()) : ''; ?>"
                                            placeholder="https://..."
                                            required>
                                        <div class="help-text">Lien direct vers votre publication (vidéo, post, etc.)</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section mb-4">
                                <h3><i class="fas fa-comment-dots"></i> Argumentaire</h3>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="argumentaire" class="form-label fw-bold">
                                            <i class="fas fa-edit"></i> Pourquoi méritez-vous de gagner ?
                                            <span class="required">*</span>
                                        </label>
                                        <textarea id="argumentaire" name="argumentaire"
                                            class="form-control"
                                            rows="6"
                                            placeholder="Décrivez pourquoi votre contenu est exceptionnel (min. 200 caractères)..."
                                            required><?php echo $candidatureData ? htmlspecialchars((string) $candidatureData->getArgumentaire()) : ''; ?></textarea>
                                        <div class="char-counter" id="argumentaireCounter">0/2000 caractères</div>
                                        <div class="help-text">Convainquez le jury en détaillant les points forts de votre contenu</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section mb-4">
                                <h3>
                                    <i class="fas fa-image"></i> Image de Présentation
                                    <span class="required">*</span>
                                </h3>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="alert alert-danger" id="imageError" style="display: none;">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <span id="imageErrorMessage"></span>
                                        </div>

                                        <div class="file-upload-candidature" id="fileUploadArea">
                                            <div class="mb-3">
                                                <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                                            </div>
                                            <h5>Cliquez ou glissez-déposez votre image ici</h5>
                                            <p class="text-muted">L'image est obligatoire pour soumettre la candidature</p>
                                            <p class="text-muted small">Formats acceptés: JPG, PNG, GIF, WebP (max 5MB)</p>
                                            <input type="file" name="image" id="imageInput"
                                                class="d-none"
                                                accept="image/*"
                                                <?php echo !$candidatureData ? 'required' : ''; ?>>
                                        </div>

                                        <div class="alert alert-info" id="fileInfo" style="display: none;">
                                            <i class="fas fa-file-image"></i>
                                            <span id="fileName" class="fw-bold ms-2"></span>
                                            <span id="fileSize" class="text-muted ms-2"></span>
                                        </div>

                                        <div class="text-center" id="imagePreviewContainer" style="display: none;">
                                            <img loading="lazy" src="" alt="Aperçu" class="image-preview" id="imagePreview">
                                            <br>
                                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage()">
                                                <i class="fas fa-trash"></i> Supprimer l'image
                                            </button>
                                        </div>

                                        <?php if ($currentImageUrl): ?>
                                            <div class="alert alert-success mt-3" id="currentImageContainer">
                                                <p class="fw-bold mb-2">
                                                    <i class="fas fa-image"></i> Image actuelle:
                                                </p>
                                                <img loading="lazy" src="<?php echo htmlspecialchars($currentImageUrl); ?>"
                                                    alt="Image actuelle"
                                                    class="image-preview mb-2">
                                                <p class="text-muted small mb-0">
                                                    Cette image sera conservée. Vous pouvez la remplacer en téléchargeant une nouvelle image ci-dessus.
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="<?php echo htmlspecialchars($mesCandidaturesUrl); ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Retour
                                </a>

                                <button type="submit" class="btn btn-submit-candidature" id="submitButton" <?php echo $editId ? '' : 'disabled'; ?>>
                                    <i class="fas fa-paper-plane"></i>
                                    <?php echo $editId ? 'Mettre à jour' : 'Soumettre la candidature'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p class="text-muted mb-0">
                &copy; <?php echo htmlspecialchars((string) $copyrightYear); ?> Social Media Awards. Tous droits réservés.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const categoriesByEdition = <?php echo json_encode($categoriesByEdition); ?>;
        const usedPlatformsByCategory = <?php echo json_encode($usedPlatformsByCategory); ?>;
        const currentCategoryId = <?php echo $currentCategoryId ?? 'null'; ?>;
        const currentPlatform = "<?php echo addslashes($currentPlateforme); ?>";
        const isEditMode = <?php echo $editId ? 'true' : 'false'; ?>;
        let hasValidImage = <?php echo ($candidatureData && $candidatureData->getImage()) ? 'true' : 'false'; ?>;
        let currentSelectedCategory = null;

        document.addEventListener('DOMContentLoaded', function() {
            updateEditionDateInfo();
            const initialDefaultEditionId = <?php echo $defaultEditionId ?: 'null'; ?>;
            if (initialDefaultEditionId) {
                loadCategoriesForEdition(initialDefaultEditionId);
            }
            document.getElementById('edition').addEventListener('change', function() {
                updateEditionDateInfo();
                loadCategoriesForEdition(this.value);
            });
            document.getElementById('categorie').addEventListener('change', function() {
                updateCategoryDetails(this.value);
                updatePlatformAvailability();
                checkFormValidity();
            });
            document.querySelectorAll('.plateforme-badge').forEach(badge => {
                badge.addEventListener('click', function() {
                    if (!this.classList.contains('platform-disabled')) {
                        selectPlatform(this.getAttribute('data-platform'));
                    }
                });
            });
            initCharacterCounters();
            initImageValidation();
            if (isEditMode && currentPlatform) {
                setTimeout(() => { selectPlatform(currentPlatform); }, 500);
            }
            setTimeout(checkFormValidity, 100);
        });

        function updateEditionDateInfo() {
            const editionSelect = document.getElementById('edition');
            let dateInfo = document.getElementById('editionDateInfo');
            if (!dateInfo) {
                const helpText = editionSelect.parentElement.querySelector('.help-text');
                if (!helpText) return;
                dateInfo = document.createElement('div');
                dateInfo.id = 'editionDateInfo';
                dateInfo.className = 'edition-date-info';
                helpText.insertAdjacentElement('afterend', dateInfo);
            }
            const selectedOption = editionSelect.selectedOptions[0];
            const formattedDate = selectedOption?.dataset?.dateFinFormatted;
            if (!editionSelect.value || !formattedDate) {
                dateInfo.remove();
                return;
            }
            dateInfo.textContent = `Date limite de candidature: ${formattedDate}`;
        }

        function loadCategoriesForEdition(editionId) {
            const categorySelect = document.getElementById('categorie');
            categorySelect.innerHTML = '<option value="">Choisir une catégorie</option>';
            categorySelect.disabled = true;
            document.getElementById('categoryDetails').style.display = 'none';
            document.getElementById('categoryPlatformInfo').style.display = 'none';
            if (!editionId) {
                categorySelect.disabled = false;
                resetPlatformSelection();
                return;
            }
            document.getElementById('loadingCategories').style.display = 'block';
            setTimeout(() => {
                if (categoriesByEdition[editionId]) {
                    populateCategories(categoriesByEdition[editionId], editionId);
                } else {
                    categorySelect.innerHTML = '<option value="">Aucune catégorie disponible</option>';
                }
                document.getElementById('loadingCategories').style.display = 'none';
            }, 300);
        }

        function populateCategories(categories, editionId) {
            const categorySelect = document.getElementById('categorie');
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id_categorie;
                option.textContent = category.nom;
                option.dataset.description = category.description || '';
                option.dataset.plateformeCible = category.plateforme_cible || 'Toutes';
                option.dataset.editionId = editionId;
                if (currentCategoryId && currentCategoryId == category.id_categorie) {
                    option.selected = true;
                    currentSelectedCategory = category.id_categorie;
                }
                categorySelect.appendChild(option);
            });
            categorySelect.disabled = false;
            if (currentCategoryId) {
                updateCategoryDetails(currentCategoryId);
                updatePlatformAvailability();
            }
            checkFormValidity();
        }

        function updateCategoryDetails(categoryId) {
            currentSelectedCategory = categoryId;
            const categoryDetails = document.getElementById('categoryDetails');
            if (!categoryId) {
                categoryDetails.style.display = 'none';
                return;
            }
            const selectedOption = document.querySelector(`#categorie option[value="${categoryId}"]`);
            if (selectedOption) {
                categoryDetails.style.display = 'block';
                document.getElementById('categoryDescription').textContent = selectedOption.dataset.description || 'Aucune description disponible';
                document.getElementById('categoryPlatform').innerHTML = `<strong>Plateforme cible:</strong> ${selectedOption.dataset.plateformeCible}`;
            } else {
                categoryDetails.style.display = 'none';
            }
        }

        function resetPlatformSelection() {
            document.getElementById('plateformeInput').value = '';
            document.querySelectorAll('.plateforme-badge').forEach(badge => {
                badge.classList.remove('active');
                badge.style.borderColor = '#dee2e6';
            });
            document.getElementById('platformSelectionInfo').style.display = 'none';
        }

        function selectPlatform(platform) {
            if (!currentSelectedCategory) {
                alert('Veuillez d\'abord sélectionner une catégorie.');
                return;
            }
            const usedPlatforms = usedPlatformsByCategory[currentSelectedCategory] || [];
            if (usedPlatforms.includes(platform) && !isEditMode) {
                showPlatformError(`Vous avez déjà une candidature pour ${platform} dans cette catégorie.`);
                return;
            }
            document.getElementById('plateformeInput').value = platform;
            document.querySelectorAll('.plateforme-badge').forEach(badge => {
                badge.classList.remove('active');
                if (badge.getAttribute('data-platform') === platform) {
                    badge.classList.add('active');
                }
            });
            const platformInfo = document.getElementById('platformSelectionInfo');
            platformInfo.style.display = 'block';
            platformInfo.className = 'platform-info';
            document.getElementById('platformInfoMessage').textContent = `${platform} sélectionné`;
            checkFormValidity();
        }

        function updatePlatformAvailability() {
            const categoryId = document.getElementById('categorie').value;
            const platformInfo = document.getElementById('categoryPlatformInfo');
            document.querySelectorAll('.plateforme-badge').forEach(badge => {
                badge.classList.remove('platform-disabled');
                badge.style.opacity = '1';
                badge.style.cursor = 'pointer';
                const usedBadge = badge.querySelector('.platform-used-badge');
                if (usedBadge) usedBadge.style.display = 'none';
            });
            if (!categoryId) {
                document.querySelectorAll('.plateforme-badge').forEach(badge => badge.classList.add('platform-disabled'));
                platformInfo.style.display = 'none';
                return;
            }
            const usedPlatforms = usedPlatformsByCategory[categoryId] || [];
            if (usedPlatforms.length > 0) {
                platformInfo.style.display = 'block';
                const usedText = usedPlatforms.map(p => `<span class="platform-badge-used">${p}</span>`).join(', ');
                const availablePlatforms = ['TikTok', 'Instagram', 'YouTube', 'X', 'Facebook', 'Twitch'].filter(p => !usedPlatforms.includes(p));
                const availableText = availablePlatforms.map(p => `<span class="platform-badge-available">${p}</span>`).join(', ');
                document.getElementById('platformInfoText').innerHTML = `<strong>Plateformes déjà utilisées :</strong> ${usedText}<br><strong>Plateformes disponibles :</strong> ${availableText}`;
                usedPlatforms.forEach(platform => {
                    const badge = document.getElementById(`platform-badge-${platform}`);
                    if (badge && !isEditMode) {
                        badge.classList.add('platform-disabled');
                        const usedBadge = badge.querySelector('.platform-used-badge');
                        if (usedBadge) usedBadge.style.display = 'flex';
                    }
                });
            } else {
                platformInfo.style.display = 'block';
                document.getElementById('platformInfoText').innerHTML = '<strong>Toutes les plateformes sont disponibles pour cette catégorie</strong>';
            }
            checkFormValidity();
        }

        function showPlatformError(message) {
            const platformInfo = document.getElementById('platformSelectionInfo');
            platformInfo.style.display = 'block';
            platformInfo.className = 'platform-info bg-danger text-white';
            document.getElementById('platformInfoMessage').innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
        }

        function initCharacterCounters() {
            const libelleInput = document.getElementById('libelle');
            const argumentaireInput = document.getElementById('argumentaire');
            libelleInput.addEventListener('input', function() {
                const counter = document.getElementById('libelleCounter');
                const length = this.value.length;
                counter.textContent = `${length}/255 caractères`;
                counter.className = length > 240 ? 'char-counter warning' : 'char-counter';
                checkFormValidity();
            });
            argumentaireInput.addEventListener('input', function() {
                const counter = document.getElementById('argumentaireCounter');
                const length = this.value.length;
                counter.textContent = `${length}/2000 caractères`;
                if (length < 200) {
                    counter.className = 'char-counter danger';
                } else if (length > 1900) {
                    counter.className = 'char-counter warning';
                } else {
                    counter.className = 'char-counter';
                }
                checkFormValidity();
            });
            libelleInput.dispatchEvent(new Event('input'));
            argumentaireInput.dispatchEvent(new Event('input'));
        }

        function initImageValidation() {
            const uploadArea = document.getElementById('fileUploadArea');
            const imageInput = document.getElementById('imageInput');
            uploadArea.addEventListener('click', () => imageInput.click());
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.style.borderColor = '#4FBDAB';
                uploadArea.style.backgroundColor = 'rgba(79, 189, 171, 0.05)';
            });
            uploadArea.addEventListener('dragleave', () => {
                uploadArea.style.borderColor = '#dee2e6';
                uploadArea.style.backgroundColor = '#f8f9fa';
            });
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.style.borderColor = '#dee2e6';
                uploadArea.style.backgroundColor = '#f8f9fa';
                if (e.dataTransfer.files.length) {
                    imageInput.files = e.dataTransfer.files;
                    imageInput.dispatchEvent(new Event('change'));
                }
            });
            imageInput.addEventListener('change', function() {
                if (this.files.length) {
                    validateImage(this.files[0]);
                }
            });
        }

        function validateImage(file) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            const maxSize = 5 * 1024 * 1024;
            const errorDiv = document.getElementById('imageError');
            const uploadArea = document.getElementById('fileUploadArea');
            errorDiv.style.display = 'none';
            uploadArea.classList.remove('file-upload-required', 'file-upload-valid');
            if (!allowedTypes.includes(file.type)) {
                showImageError('Format non supporté. Utilisez JPG, PNG, GIF ou WebP.');
                return false;
            }
            if (file.size > maxSize) {
                showImageError('Fichier trop volumineux. Taille maximale: 5MB.');
                return false;
            }
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = `(${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            document.getElementById('fileInfo').style.display = 'block';
            const currentImageContainer = document.getElementById('currentImageContainer');
            if (currentImageContainer) currentImageContainer.style.display = 'none';
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreviewContainer').style.display = 'block';
            };
            reader.readAsDataURL(file);
            uploadArea.classList.add('file-upload-valid');
            hasValidImage = true;
            checkFormValidity();
            return true;
        }

        function showImageError(message) {
            document.getElementById('imageErrorMessage').textContent = message;
            document.getElementById('imageError').style.display = 'block';
            document.getElementById('fileUploadArea').classList.add('file-upload-required');
            hasValidImage = false;
            checkFormValidity();
        }

        function removeImage() {
            document.getElementById('imageInput').value = '';
            document.getElementById('fileInfo').style.display = 'none';
            document.getElementById('imagePreviewContainer').style.display = 'none';
            document.getElementById('fileUploadArea').classList.remove('file-upload-valid');
            const currentImageContainer = document.getElementById('currentImageContainer');
            if (currentImageContainer) {
                currentImageContainer.style.display = 'block';
                hasValidImage = true;
            } else {
                hasValidImage = false;
            }
            checkFormValidity();
        }

        function checkFormValidity() {
            const libelle = document.getElementById('libelle').value.trim();
            const argumentaire = document.getElementById('argumentaire').value.trim();
            const platform = document.getElementById('plateformeInput').value;
            const category = document.getElementById('categorie').value;
            const url = document.getElementById('url_contenu').value.trim();
            const edition = document.getElementById('edition').value;
            const isFormValid = libelle && argumentaire.length >= 200 && platform && category && url && edition && hasValidImage;
            const submitButton = document.getElementById('submitButton');
            submitButton.disabled = !isFormValid;
            if (isFormValid) {
                submitButton.classList.remove('btn-secondary');
                submitButton.classList.add('btn-primary');
            } else {
                submitButton.classList.remove('btn-primary');
                submitButton.classList.add('btn-secondary');
            }
        }

        document.getElementById('candidatureForm').addEventListener('submit', function(e) {
            const argumentaire = document.getElementById('argumentaire').value.trim();
            if (argumentaire.length < 200) {
                e.preventDefault();
                alert("L'argumentaire doit contenir au moins 200 caractères.");
                document.getElementById('argumentaire').focus();
                return false;
            }
            if (!hasValidImage) {
                e.preventDefault();
                showImageError('Veuillez télécharger une image valide pour votre candidature.');
                return false;
            }
            const categoryId = document.getElementById('categorie').value;
            const platform = document.getElementById('plateformeInput').value;
            if (categoryId && platform) {
                const usedPlatforms = usedPlatformsByCategory[categoryId] || [];
                if (usedPlatforms.includes(platform) && !isEditMode) {
                    e.preventDefault();
                    alert(`Vous avez déjà une candidature dans cette catégorie pour la plateforme ${platform}.`);
                    return false;
                }
            }
            return true;
        });
    </script>
</body>
</html>