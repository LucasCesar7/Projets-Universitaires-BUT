<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Social Media Awards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($candidateCssUrl); ?>">
    <style>
        .profile-card { border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .avatar-circle { width: 120px; height: 120px; border-radius: 50%; border: 4px solid #4FBDAB; overflow: hidden; margin: 0 auto 20px; position: relative; }
        .avatar-img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-initials { width: 100%; height: 100%; background: linear-gradient(135deg, #4FBDAB, #45a999); color: white; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: bold; }
        .avatar-status { position: absolute; bottom: 10px; right: 10px; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; }
        .avatar-status.online { background-color: #28a745; }
        .profile-name { color: #333; font-weight: 700; margin-bottom: 5px; }
        .profile-role { color: #4FBDAB; font-weight: 500; margin-bottom: 20px; }
        .profile-badges .badge { margin: 2px; padding: 8px 12px; border-radius: 20px; font-weight: 500; }
        .contact-item { display: flex; align-items: center; gap: 10px; color: #666; }
        .contact-item i { width: 20px; color: #4FBDAB; }
        .stat-card-profile { border-radius: 12px; padding: 20px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s ease; }
        .stat-card-profile:hover { transform: translateY(-5px); }
        .stat-card-profile.total { background: linear-gradient(135deg, #4FBDAB, #45a999); }
        .stat-card-profile.pending { background: linear-gradient(135deg, #ffc107, #e0a800); }
        .stat-card-profile.approved { background: linear-gradient(135deg, #28a745, #218838); }
        .stat-card-profile.rejected { background: linear-gradient(135deg, #dc3545, #c82333); }
        .stat-content { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .stat-icon { font-size: 2rem; opacity: 0.8; }
        .stat-number { font-size: 2.5rem; font-weight: 700; }
        .stat-desc { font-size: 0.9rem; opacity: 0.9; margin-bottom: 0; }
        .activity-item { display: flex; gap: 15px; padding: 15px; border-bottom: 1px solid #eee; transition: background-color 0.3s ease; }
        .activity-item:hover { background-color: #f8f9fa; }
        .activity-item:last-child { border-bottom: none; }
        .activity-icon { width: 40px; height: 40px; background: linear-gradient(135deg, rgba(79, 189, 171, 0.1), rgba(79, 189, 171, 0.05)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #4FBDAB; }
        .activity-content { flex: 1; }
        .activity-title { font-weight: 600; color: #333; margin-bottom: 5px; }
        .activity-desc { color: #666; font-size: 0.9rem; }
        .empty-state { padding: 40px 20px; text-align: center; }
        .empty-state i { color: #dee2e6; margin-bottom: 15px; }
        .info-box { margin-bottom: 20px; }
        .info-label { font-weight: 600; color: #4FBDAB; display: block; margin-bottom: 5px; }
        .info-value { color: #333; font-size: 1.1rem; }
        .modal-profile .modal-header { background: linear-gradient(135deg, #4FBDAB, #45a999); color: white; border-bottom: none; }
        .modal-profile .modal-title { font-weight: 600; }
        .modal-profile .modal-body { padding: 30px; }
        .modal-profile .form-label { font-weight: 600; color: #333; margin-bottom: 8px; }
        .modal-profile .form-control, .modal-profile .form-select { border-radius: 8px; border: 2px solid #e9ecef; padding: 10px 15px; transition: all 0.3s ease; }
        .modal-profile .form-control:focus, .modal-profile .form-select:focus { border-color: #4FBDAB; box-shadow: 0 0 0 3px rgba(79, 189, 171, 0.2); }
        .file-upload-profile { border: 2px dashed #dee2e6; border-radius: 10px; padding: 30px; text-align: center; cursor: pointer; transition: all 0.3s ease; background: #f8f9fa; }
        .file-upload-profile:hover { border-color: #4FBDAB; background: rgba(79, 189, 171, 0.05); }
        .file-upload-profile i { font-size: 3rem; color: #4FBDAB; margin-bottom: 15px; }
        .file-upload-profile p { color: #666; margin-bottom: 5px; }
        .file-upload-profile input { display: none; }
        @media (max-width: 768px) {
            .stat-number { font-size: 2rem; }
            .stat-icon { font-size: 1.5rem; }
            .modal-profile .modal-body { padding: 20px; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo htmlspecialchars($candidateDashboardUrl); ?>">
                <i class="fas fa-trophy"></i>
                Social Media Awards
            </a>
        </div>
    </nav>

    <main class="main-content">
        <div class="container-fluid">
            <div class="page-header mb-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-user-circle"></i> Mon Profil
                        </h1>
                        <p class="page-subtitle">Gérez vos informations personnelles et suivez vos statistiques</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-edit"></i> Modifier le Profil
                    </button>
                </div>
            </div>

            <?php if ($profileSuccess): ?>
                <div class="alert alert-success mb-4" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars((string) $profileSuccess); ?>
                </div>
            <?php endif; ?>

            <?php if ($profileError): ?>
                <div class="alert alert-danger mb-4" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo nl2br(htmlspecialchars(str_replace(['<br />', '<br/>', '<br>'], "\n", (string) $profileError))); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-4">
                    <div class="main-card profile-card mb-4">
                        <div class="card-body text-center">
                            <div class="profile-avatar mb-4">
                                <div class="avatar-circle">
                                    <?php if ($candidatePhotoUrl): ?>
                                        <img loading="lazy" src="<?php echo htmlspecialchars($candidatePhotoUrl); ?>"
                                             alt="<?php echo htmlspecialchars((string) ($candidat['pseudonyme'] ?? '')); ?>"
                                             class="avatar-img">
                                    <?php else: ?>
                                        <div class="avatar-initials">
                                            <?php echo htmlspecialchars(substr((string) ($candidat['pseudonyme'] ?? 'U'), 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="avatar-status online"></div>
                                </div>
                            </div>

                            <h3 class="profile-name"><?php echo htmlspecialchars((string) ($candidat['pseudonyme'] ?? '')); ?></h3>
                            <p class="profile-role">
                                <i class="fas fa-award"></i> <?php echo htmlspecialchars((string) ($candidat['type_candidature'] ?? 'Candidat')); ?>
                            </p>

                            <div class="profile-badges mb-4">
                                <?php if (!empty($candidat['est_nomine'])): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-star"></i> Nommé
                                    </span>
                                <?php endif; ?>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar"></i> Membre depuis <?php echo htmlspecialchars((string) $dateInscription); ?>
                                </span>
                            </div>

                            <div class="profile-contact">
                                <div class="contact-item mb-3">
                                    <i class="fas fa-envelope"></i>
                                    <span><?php echo htmlspecialchars((string) ($candidat['email'] ?? '')); ?></span>
                                </div>
                                <?php if (!empty($candidat['nom_legal_ou_societe'])): ?>
                                <div class="contact-item mb-3">
                                    <i class="fas fa-building"></i>
                                    <span><?php echo htmlspecialchars((string) $candidat['nom_legal_ou_societe']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($candidat['pays'])): ?>
                                <div class="contact-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars((string) $candidat['pays']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="main-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user-shield"></i> Informations du Compte
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="account-info">
                                <div class="info-item d-flex justify-content-between mb-3">
                                    <span class="info-label">
                                        <i class="fas fa-user-tag"></i> Type
                                    </span>
                                    <span class="info-value"><?php echo htmlspecialchars((string) ($candidat['type_candidature'] ?? 'Candidat')); ?></span>
                                </div>
                                <div class="info-item d-flex justify-content-between mb-3">
                                    <span class="info-label">
                                        <i class="fas fa-calendar-plus"></i> Inscrit le
                                    </span>
                                    <span class="info-value"><?php echo htmlspecialchars((string) $dateInscription); ?></span>
                                </div>
                                <div class="info-item d-flex justify-content-between">
                                    <span class="info-label">
                                        <i class="fas fa-id-card"></i> ID Utilisateur
                                    </span>
                                    <span class="info-value text-muted">#<?php echo (int) ($candidat['id_compte'] ?? 0); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card-profile total">
                                <div class="stat-content">
                                    <div class="stat-info">
                                        <h6>Total</h6>
                                        <div class="stat-number"><?php echo htmlspecialchars((string) ($stats['total'] ?? 0)); ?></div>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                </div>
                                <p class="stat-desc">Candidatures</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card-profile approved">
                                <div class="stat-content">
                                    <div class="stat-info">
                                        <h6>Approuvées</h6>
                                        <div class="stat-number"><?php echo htmlspecialchars((string) ($stats['approved'] ?? 0)); ?></div>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                <p class="stat-desc">Candidatures validées</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card-profile pending">
                                <div class="stat-content">
                                    <div class="stat-info">
                                        <h6>En attente</h6>
                                        <div class="stat-number"><?php echo htmlspecialchars((string) ($stats['pending'] ?? 0)); ?></div>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                                <p class="stat-desc">En évaluation</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card-profile rejected">
                                <div class="stat-content">
                                    <div class="stat-info">
                                        <h6>Rejetées</h6>
                                        <div class="stat-number"><?php echo htmlspecialchars((string) ($stats['rejected'] ?? 0)); ?></div>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                </div>
                                <p class="stat-desc">Non retenues</p>
                            </div>
                        </div>
                    </div>

                    <div class="main-card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-history"></i> Activité Récente
                            </h5>
                            <a href="<?php echo htmlspecialchars($mesCandidaturesUrl); ?>" class="btn btn-sm btn-outline-primary">
                                Voir toutes <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($lastCandidatures)): ?>
                                <div class="activity-list">
                                    <?php foreach ($lastCandidatures as $candidature): ?>
                                        <?php
                                        $badgeStatusClass = $statusColors[$candidature['statut']] ?? 'secondary';
                                        $dateFormatted = date('d/m/Y', strtotime((string) ($candidature['date_soumission'] ?? 'now')));
                                        ?>
                                        <div class="activity-item">
                                            <div class="activity-icon">
                                                <i class="fas fa-file-import"></i>
                                            </div>
                                            <div class="activity-content">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="activity-title"><?php echo htmlspecialchars((string) ($candidature['libelle'] ?? '')); ?></h6>
                                                        <p class="activity-desc">
                                                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars((string) ($candidature['categorie_nom'] ?? '')); ?>
                                                            <span class="mx-2">•</span>
                                                            <i class="fas fa-calendar"></i> <?php echo htmlspecialchars($dateFormatted); ?>
                                                        </p>
                                                    </div>
                                                    <span class="badge bg-<?php echo htmlspecialchars((string) $badgeStatusClass); ?>">
                                                        <?php echo htmlspecialchars((string) ($candidature['statut'] ?? '')); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state text-center py-5">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucune activité récente</h5>
                                    <p class="text-muted mb-4">Vous n'avez pas encore soumis de candidature</p>
                                    <a href="<?php echo htmlspecialchars($submitCandidatureUrl); ?>" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Soumettre une candidature
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="main-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle"></i> Informations de Candidature
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box mb-3">
                                        <label class="info-label">
                                            <i class="fas fa-user-tie"></i> Type de Candidature
                                        </label>
                                        <p class="info-value"><?php echo htmlspecialchars((string) ($candidat['type_candidature'] ?? 'Non spécifié')); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box mb-3">
                                        <label class="info-label">
                                            <i class="fas fa-building"></i> Nom Légal / Société
                                        </label>
                                        <p class="info-value"><?php echo htmlspecialchars((string) ($candidat['nom_legal_ou_societe'] ?? 'Non spécifié')); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($candidat['est_nomine'])): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-star"></i>
                                    <strong>Félicitations !</strong> Vous êtes nominé pour les Social Media Awards.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container-fluid">
            <div class="footer-bottom">
                <p>&copy; <?php echo htmlspecialchars((string) $copyrightYear); ?> Social Media Awards. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-profile">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit"></i> Modifier le Profil
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" action="<?php echo htmlspecialchars($updateProfileUrl); ?>" enctype="multipart/form-data" id="editProfileForm">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($updateProfileToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Pseudonyme *</label>
                                <input type="text" name="pseudonyme" class="form-control"
                                       value="<?php echo htmlspecialchars((string) ($candidat['pseudonyme'] ?? '')); ?>"
                                       required
                                       minlength="3"
                                       maxlength="50">
                                <div class="form-text">3-50 caractères</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control"
                                       value="<?php echo htmlspecialchars((string) ($candidat['email'] ?? '')); ?>"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Type de Candidature</label>
                                <select name="type_candidature" class="form-select">
                                    <option value="Créateur" <?php echo (($candidat['type_candidature'] ?? '') === 'Créateur') ? 'selected' : ''; ?>>Créateur</option>
                                    <option value="Marque" <?php echo (($candidat['type_candidature'] ?? '') === 'Marque') ? 'selected' : ''; ?>>Marque</option>
                                    <option value="Autre" <?php echo (($candidat['type_candidature'] ?? '') === 'Autre') ? 'selected' : ''; ?>>Autre</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nom Légal / Société</label>
                                <input type="text" name="nom_legal_ou_societe" class="form-control"
                                       value="<?php echo htmlspecialchars((string) ($candidat['nom_legal_ou_societe'] ?? '')); ?>"
                                       maxlength="100">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Pays</label>
                                <select name="pays" class="form-select">
                                    <option value="" <?php echo $selectedCountry === '' ? 'selected' : ''; ?>>Sélectionnez un pays</option>
                                    <?php foreach ($countryOptions as $countryOption): ?>
                                        <option value="<?php echo htmlspecialchars($countryOption); ?>" <?php echo $selectedCountry === $countryOption ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($countryOption); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Genre</label>
                                <select name="genre" class="form-select">
                                    <option value="">Non spécifié</option>
                                    <option value="Homme" <?php echo (($candidat['genre'] ?? '') === 'Homme') ? 'selected' : ''; ?>>Homme</option>
                                    <option value="Femme" <?php echo (($candidat['genre'] ?? '') === 'Femme') ? 'selected' : ''; ?>>Femme</option>
                                    <option value="Autre" <?php echo (($candidat['genre'] ?? '') === 'Autre') ? 'selected' : ''; ?>>Autre</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Photo de profil</label>
                                <div class="file-upload-profile" onclick="document.getElementById('photoInput').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Cliquez pour télécharger une nouvelle photo</p>
                                    <p class="small">Formats acceptés: JPG, PNG, GIF, WebP (max 5MB)</p>
                                    <input type="file" name="photo_profil" id="photoInput"
                                           class="form-control"
                                           accept="image/*"
                                           style="display: none;">
                                </div>
                                <?php if (!empty($candidat['photo_profil'])): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">Photo actuelle :</small>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <img loading="lazy" src="<?php echo htmlspecialchars((string) ($candidatePhotoUrl ?? '')); ?>"
                                                 alt="Photo actuelle"
                                                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                            <span><?php echo htmlspecialchars(basename((string) $candidat['photo_profil'])); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div id="photoPreview" class="mt-3" style="display: none;">
                                    <small class="text-muted">Nouvelle photo :</small>
                                    <img loading="lazy" id="previewImage" src="" alt="Aperçu"
                                         style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-top: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('photoInput').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(loadEvent) {
                    const previewImage = document.getElementById('previewImage');
                    const photoPreview = document.getElementById('photoPreview');

                    previewImage.src = loadEvent.target.result;
                    photoPreview.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        document.getElementById('editProfileForm').addEventListener('submit', function(e) {
            const pseudonyme = this.querySelector('input[name="pseudonyme"]').value.trim();
            const email = this.querySelector('input[name="email"]').value.trim();

            if (pseudonyme.length < 3 || pseudonyme.length > 50) {
                e.preventDefault();
                alert('Le pseudonyme doit contenir entre 3 et 50 caractères.');
                return false;
            }

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                e.preventDefault();
                alert('Veuillez entrer une adresse email valide.');
                return false;
            }

            const photoInput = this.querySelector('input[name="photo_profil"]');
            if (photoInput.files.length > 0) {
                const file = photoInput.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                const maxSize = 5 * 1024 * 1024;

                if (!allowedTypes.includes(file.type)) {
                    e.preventDefault();
                    alert('Format d\'image non supporté. Utilisez JPG, PNG, GIF ou WebP.');
                    return false;
                }

                if (file.size > maxSize) {
                    e.preventDefault();
                    alert('L\'image est trop volumineuse. Taille maximale: 5MB.');
                    return false;
                }
            }

            return true;
        });

        const editProfileModal = document.getElementById('editProfileModal');
        if (editProfileModal) {
            editProfileModal.addEventListener('hidden.bs.modal', function() {
                const alerts = this.querySelectorAll('.alert');
                alerts.forEach(alert => alert.remove());
            });
        }
    </script>
</body>
</html>