<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Profil - Social Media Awards</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($editProfileCssUrl); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .avatar-large.has-photo {
            background: none !important;
            border: none;
            position: relative;
        }

        .avatar-large.has-photo .avatar-initials {
            display: none;
        }

        .avatar-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--principal);
            display: block;
        }

        .avatar-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--principal);
        }

        .file-upload {
            position: relative;
            border: 2px dashed var(--gray-light);
            border-radius: var(--radius-md);
            padding: var(--space-lg);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--light);
        }

        .file-upload:hover {
            border-color: var(--principal);
            background: rgba(79, 189, 171, 0.05);
        }

        .file-upload input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .current-photo {
            margin-top: var(--space-md);
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            color: var(--dark);
            font-size: 0.9rem;
        }

        .current-photo img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--principal);
        }

        .remove-photo {
            color: var(--danger);
            cursor: pointer;
            font-size: 0.8rem;
            margin-left: var(--space-sm);
        }

        .remove-photo:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <?php require appPath('views/partials/header.php'); ?>

    <main class="edit-profile-container">
        <div class="edit-profile-main">
            <nav class="breadcrumb">
                <ul>
                    <li><a href="<?php echo htmlspecialchars($userDashboardUrl); ?>">Tableau de bord</a></li>
                    <li class="separator"><i class="fas fa-chevron-right"></i></li>
                    <li class="current">Modifier le profil</li>
                </ul>
            </nav>

            <div class="profile-grid">
                <aside class="profile-sidebar">
                    <div class="sidebar-card">
                        <div class="sidebar-avatar">
                            <div class="avatar-large <?php echo ($userData['photo_profil'] ?? null) ? 'has-photo' : ''; ?>" id="avatarContainer">
                                <?php if ($userData['photo_profil'] ?? null): ?>
                                    <?php $photoPath = $upload_dir . $userData['photo_profil']; ?>
                                    <?php if (file_exists($photoPath)): ?>
                                        <img loading="lazy" src="<?php echo $web_path . htmlspecialchars($userData['photo_profil']); ?>"
                                            alt="<?php echo htmlspecialchars($userData['pseudonyme']); ?>"
                                            class="avatar-img">
                                    <?php else: ?>
                                        <div class="avatar-initials"><?php echo htmlspecialchars($initials); ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="avatar-initials"><?php echo htmlspecialchars($initials); ?></div>
                                <?php endif; ?>
                                <div class="avatar-edit">
                                    <i class="fas fa-camera"></i>
                                </div>
                            </div>
                            <h3><?php echo htmlspecialchars($userData['pseudonyme']); ?></h3>
                            <p>Électeur depuis <?php echo htmlspecialchars($memberSinceLabel); ?></p>
                        </div>

                        <nav class="sidebar-menu">
                            <a href="#informations" class="menu-item active">
                                <i class="fas fa-user"></i>
                                Informations personnelles
                            </a>
                            <a href="#securite" class="menu-item">
                                <i class="fas fa-shield-alt"></i>
                                Sécurité du compte
                            </a>
                        </nav>
                    </div>

                    <div class="sidebar-card">
                        <h4 style="margin-bottom: var(--space-md); color: var(--dark);">
                            <i class="fas fa-info-circle" style="color: var(--principal);"></i>
                            Statistiques
                        </h4>
                        <ul style="list-style: none; display: flex; flex-direction: column; gap: var(--space-sm);">
                            <li style="display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-size: 0.9rem; color: var(--gray);">Votes émis</span>
                                <span style="font-weight: 600; color: var(--principal);"><?php echo htmlspecialchars((string) $votesCount); ?></span>
                            </li>
                            <li style="display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-size: 0.9rem; color: var(--gray);">Inscrit depuis</span>
                                <span style="font-weight: 600; color: var(--principal);"><?php echo htmlspecialchars($memberSinceLabel); ?></span>
                            </li>
                            <li style="display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-size: 0.9rem; color: var(--gray);">Dernière connexion</span>
                                <span style="font-weight: 600; color: var(--principal);">Aujourd'hui</span>
                            </li>
                        </ul>
                    </div>
                </aside>

                <section class="edit-section" id="informations">
                    <div class="section-header">
                        <h2><i class="fas fa-user-edit"></i> Modifier votre profil</h2>
                        <p>Mettez à jour vos informations personnelles et gérez vos préférences.</p>
                    </div>

                    <?php if ($success): ?>
                        <div style="background: rgba(50, 213, 131, 0.1); border: 2px solid var(--success); border-radius: var(--radius-md); padding: var(--space-md); margin-bottom: var(--space-lg); display: flex; align-items: center; gap: var(--space-sm);">
                            <i class="fas fa-check-circle" style="color: var(--success); font-size: 1.2rem;"></i>
                            <span style="color: var(--success); font-weight: 500;"><?php echo htmlspecialchars($success); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($errors['general'])): ?>
                        <div style="background: rgba(255, 107, 107, 0.1); border: 2px solid var(--danger); border-radius: var(--radius-md); padding: var(--space-md); margin-bottom: var(--space-lg); display: flex; align-items: center; gap: var(--space-sm);">
                            <i class="fas fa-exclamation-circle" style="color: var(--danger); font-size: 1.2rem;"></i>
                            <span style="color: var(--danger); font-weight: 500;"><?php echo htmlspecialchars($errors['general']); ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="edit-form" id="editProfileForm" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($editProfileCsrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" id="generated_avatar_choice" name="generated_avatar_choice" value="">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="pseudonyme" class="form-label">
                                    <i class="fas fa-user"></i>
                                    Pseudonyme <span class="required">*</span>
                                </label>
                                <input type="text"
                                    id="pseudonyme"
                                    name="pseudonyme"
                                    class="form-control <?php echo isset($errors['pseudonyme']) ? 'error' : ''; ?>"
                                    value="<?php echo htmlspecialchars($userData['pseudonyme']); ?>"
                                    required
                                    minlength="3"
                                    maxlength="50"
                                    placeholder="Votre nom public">
                                <?php if (isset($errors['pseudonyme'])): ?>
                                    <small class="form-text error"><?php echo htmlspecialchars($errors['pseudonyme']); ?></small>
                                <?php else: ?>
                                    <small class="form-text">Votre nom public visible par tous (3-50 caractères)</small>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i>
                                    Adresse email <span class="required">*</span>
                                </label>
                                <input type="email"
                                    id="email"
                                    name="email"
                                    class="form-control <?php echo isset($errors['email']) ? 'error' : ''; ?>"
                                    value="<?php echo htmlspecialchars($userData['email']); ?>"
                                    required
                                    placeholder="exemple@email.com">
                                <?php if (isset($errors['email'])): ?>
                                    <small class="form-text error"><?php echo htmlspecialchars($errors['email']); ?></small>
                                <?php else: ?>
                                    <small class="form-text">Nous ne partagerons jamais votre email</small>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="date_naissance" class="form-label">
                                    <i class="fas fa-birthday-cake"></i>
                                    Date de naissance <span class="required">*</span>
                                </label>
                                <input type="date"
                                    id="date_naissance"
                                    name="date_naissance"
                                    class="form-control <?php echo isset($errors['date_naissance']) ? 'error' : ''; ?>"
                                    value="<?php echo htmlspecialchars($userData['date_naissance']); ?>"
                                    required
                                    max="<?php echo date('Y-m-d', strtotime('-13 years')); ?>">
                                <?php if (isset($errors['date_naissance'])): ?>
                                    <small class="form-text error"><?php echo htmlspecialchars($errors['date_naissance']); ?></small>
                                <?php else: ?>
                                    <small class="form-text">Vous devez avoir au moins 13 ans</small>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="pays" class="form-label">
                                    <i class="fas fa-globe"></i>
                                    Pays <span class="required">*</span>
                                </label>
                                <select id="pays"
                                    name="pays"
                                    class="form-control <?php echo isset($errors['pays']) ? 'error' : ''; ?>"
                                    required>
                                    <option value="" <?php echo $selectedCountry === '' ? 'selected' : ''; ?>>Sélectionnez votre pays</option>
                                    <?php foreach ($countryOptions as $countryOption): ?>
                                        <option value="<?php echo htmlspecialchars($countryOption); ?>" <?php echo $selectedCountry === $countryOption ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($countryOption); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['pays'])): ?>
                                    <small class="form-text error"><?php echo htmlspecialchars($errors['pays']); ?></small>
                                <?php endif; ?>
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label">
                                    <i class="fas fa-venus-mars"></i> Genre
                                </label>
                                <fieldset>
                                    <legend class="visually-hidden">Choisissez votre genre</legend>
                                    <div class="radio-group">
                                        <label class="radio-label" for="genre_homme">
                                            <input type="radio" name="genre" id="genre_homme" value="Homme" <?php echo ($userData['genre'] ?? '') === 'Homme' ? 'checked' : ''; ?>>
                                            <span>Homme</span>
                                        </label>
                                        <label class="radio-label" for="genre_femme">
                                            <input type="radio" name="genre" id="genre_femme" value="Femme" <?php echo ($userData['genre'] ?? '') === 'Femme' ? 'checked' : ''; ?>>
                                            <span>Femme</span>
                                        </label>
                                        <label class="radio-label" for="genre_autre">
                                            <input type="radio" name="genre" id="genre_autre" value="Autre" <?php echo ($userData['genre'] ?? '') === 'Autre' ? 'checked' : ''; ?>>
                                            <span>Autre</span>
                                        </label>
                                        <label class="radio-label" for="genre_prefere_pas_dire">
                                            <input type="radio" name="genre" id="genre_prefere_pas_dire" value="" <?php echo empty($userData['genre'] ?? '') ? 'checked' : ''; ?>>
                                            <span>Préfère ne pas dire</span>
                                        </label>
                                    </div>
                                </fieldset>
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label">
                                    <i class="fas fa-camera"></i>
                                    Photo de profil
                                </label>
                                <?php if (isset($errors['photo_profil'])): ?>
                                    <div style="color: var(--danger); margin-bottom: var(--space-sm);">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <?php echo htmlspecialchars($errors['photo_profil']); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="file-upload">
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: var(--principal); margin-bottom: var(--space-sm);"></i>
                                    <h4 style="color: var(--dark); margin-bottom: var(--space-xs);">Changer de photo</h4>
                                    <p style="color: var(--gray); margin-bottom: var(--space-md);">
                                        Cliquez pour télécharger ou glissez-déposez une image
                                    </p>
                                    <small style="color: var(--gray); display: block;">
                                        PNG, JPG ou GIF jusqu'à 5MB
                                    </small>
                                    <input type="file" id="photo_profil" name="photo_profil" accept="image/*">
                                </div>

                                <?php if (!empty($userData['photo_profil']) && file_exists($upload_dir . $userData['photo_profil'])): ?>
                                    <div class="current-photo">
                                        <img loading="lazy" src="<?php echo $web_path . htmlspecialchars($userData['photo_profil']); ?>"
                                            alt="Photo actuelle">
                                        <div>
                                            <strong>Photo actuelle:</strong> <?php echo htmlspecialchars($userData['photo_profil']); ?>
                                            <br>
                                            <span class="remove-photo" onclick="removePhoto()">
                                                <i class="fas fa-trash"></i> Supprimer cette photo
                                            </span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div id="photoPreview" style="margin-top: var(--space-md); display: none;">
                                    <strong>Nouvelle photo sélectionnée:</strong>
                                    <div style="margin-top: var(--space-sm);">
                                        <img loading="lazy" id="previewImage" src="" alt="Preview" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid var(--principal);">
                                        <div style="margin-top: var(--space-xs); font-size: 0.9rem; color: var(--gray);" id="fileInfo"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="security-section" id="securite">
                            <h3 style="color: var(--dark); margin-bottom: var(--space-lg); display: flex; align-items: center; gap: var(--space-md);">
                                <i class="fas fa-shield-alt" style="color: var(--principal);"></i>
                                Sécurité du compte
                            </h3>

                            <div class="security-grid">
                                <div class="security-card">
                                    <i class="fas fa-lock"></i>
                                    <div class="security-content">
                                        <h4>Mot de passe</h4>
                                        <p>Mettez à jour votre mot de passe régulièrement</p>
                                    </div>
                                </div>

                                <div class="security-card">
                                    <i class="fas fa-user-shield"></i>
                                    <div class="security-content">
                                        <h4>Authentification</h4>
                                        <p>Activez l'authentification à deux facteurs</p>
                                    </div>
                                </div>

                                <div class="security-card">
                                    <i class="fas fa-history"></i>
                                    <div class="security-content">
                                        <h4>Activité récente</h4>
                                        <p>Consultez les connexions récentes à votre compte</p>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top: var(--space-lg); display: flex; gap: var(--space-md);">
                                <a href="<?php echo htmlspecialchars($changePasswordUrl); ?>" class="btn btn-outline">
                                    <i class="fas fa-key"></i>
                                    Changer le mot de passe
                                </a>
                            </div>
                        </div>

                        <div class="form-actions" style="gap: var(--space-md); margin-top: var(--space-lg); display: flex; flex-direction: column;">
                            <div class="btn-left-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Enregistrer les modifications
                                </button>
                                <a href="<?php echo htmlspecialchars($userDashboardUrl); ?>" class="btn btn-outline">
                                    <i class="fas fa-times"></i>
                                    Annuler
                                </a>
                            </div>
                        </div>
                    </form>
                    <section class="danger-zone" style="margin-top: 40px; padding: 20px; border: 1px solid #dc3545; border-radius: 8px;">
                        <h3 style="color: #dc3545;">Zone dangereuse</h3>
                        <p>La suppression de votre compte est irréversible et supprimera toutes vos données associées.</p>

                        <form id="deleteAccountForm" method="POST" action="<?php echo appUrl('user/delete'); ?>">
                            <input type="hidden" name="action" value="delete_account">
                            <input type="hidden" name="csrf_token" value="<?php echo \App\Services\CsrfService::token('delete_account'); ?>">
                            <button type="button" id="deleteBtn" class="btn btn-danger" aria-label="Supprimer définitivement mon compte">
                                Supprimer mon compte
                            </button>
                        </form>
                    </section>
                </section>
            </div>
        </div>
    </main>

    <?php require appPath('views/partials/footer.php'); ?>

    <div class="avatar-modal" id="avatarModal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-camera"></i> Changer la photo de profil</h3>
            </div>
            <div class="modal-body">
                <p style="color: var(--gray); margin-bottom: var(--space-lg);">
                    Choisissez une méthode pour mettre à jour votre photo de profil
                </p>

                <div class="modal-options">
                    <div class="avatar-option" onclick="document.getElementById('photo_profil').click();">
                        <i class="fas fa-upload"></i>
                        <span>Télécharger</span>
                    </div>

                    <div class="avatar-option" onclick="generateAvatar();">
                        <i class="fas fa-palette"></i>
                        <span>Générer un avatar</span>
                    </div>
                </div>

                <div style="text-align: center; margin-top: var(--space-lg);">
                    <p style="color: var(--gray); font-size: 0.9rem;">
                        <i class="fas fa-info-circle"></i>
                        Votre photo sera visible par tous les utilisateurs
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeAvatarModal();">
                    Annuler
                </button>
                <button type="button" class="btn btn-primary" onclick="saveAvatar();">
                    Enregistrer
                </button>
            </div>
        </div>
    </div>

    <script>
        const avatarPresets = {
            avatar_teal: 'linear-gradient(135deg, #4FBDAB, #3da895)',
            avatar_pink: 'linear-gradient(135deg, #FF5A79, #ff3d5e)',
            avatar_gold: 'linear-gradient(135deg, #FFD166, #ffc145)',
            avatar_green: 'linear-gradient(135deg, #32D583, #2bc174)'
        };

        const avatarContainer = document.getElementById('avatarContainer');
        const photoInput = document.getElementById('photo_profil');
        const photoPreview = document.getElementById('photoPreview');
        const previewImage = document.getElementById('previewImage');
        const fileInfo = document.getElementById('fileInfo');
        const generatedAvatarInput = document.getElementById('generated_avatar_choice');

        document.querySelector('.avatar-large').addEventListener('click', function() {
            document.getElementById('avatarModal').style.display = 'flex';
        });

        function closeAvatarModal() {
            document.getElementById('avatarModal').style.display = 'none';
        }

        function generateAvatar() {
            const presetKeys = Object.keys(avatarPresets);
            const randomPreset = presetKeys[Math.floor(Math.random() * presetKeys.length)];
            const currentPseudonyme = document.getElementById('pseudonyme').value.trim() || '<?php echo addslashes($userData['pseudonyme']); ?>';
            const currentInitials = currentPseudonyme.substring(0, 2).toUpperCase();

            generatedAvatarInput.value = randomPreset;
            photoInput.value = '';
            photoPreview.style.display = 'none';
            avatarContainer.style.background = avatarPresets[randomPreset];
            avatarContainer.classList.remove('has-photo');
            avatarContainer.innerHTML = '<div class="avatar-initials">' + currentInitials + '</div><div class="avatar-edit"><i class="fas fa-camera"></i></div>';

            alert('Avatar généré avec succès ! Cliquez sur "Enregistrer" pour appliquer les modifications.');
        }

        function saveAvatar() {
            const hasUpload = photoInput.files.length > 0;
            const hasGeneratedAvatar = generatedAvatarInput.value !== '';

            if (!hasUpload && !hasGeneratedAvatar) {
                alert('Choisissez une image ou générez un avatar avant d\'enregistrer.');
                return;
            }

            closeAvatarModal();
            document.getElementById('editProfileForm').requestSubmit();
        }

        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                generatedAvatarInput.value = '';

                if (file.size > 5 * 1024 * 1024) {
                    alert('Le fichier est trop volumineux. Maximum 5MB.');
                    e.target.value = '';
                    return;
                }

                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Type de fichier non autorisé. Formats acceptés: JPEG, PNG, GIF.');
                    e.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImage.src = event.target.result;
                    fileInfo.textContent = file.name + ' (' + Math.round(file.size / 1024) + 'KB)';
                    photoPreview.style.display = 'block';
                    avatarContainer.innerHTML = '<img loading="lazy" src="' + event.target.result + '" class="avatar-preview"><div class="avatar-edit"><i class="fas fa-camera"></i></div>';
                    avatarContainer.classList.add('has-photo');
                };
                reader.readAsDataURL(file);
            } else {
                photoPreview.style.display = 'none';
            }
        });

        function removePhoto() {
            if (confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil?')) {
                generatedAvatarInput.value = '';

                const form = document.getElementById('editProfileForm');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_photo';
                input.value = '1';
                form.appendChild(input);
                form.submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // On cible le bouton par son ID
            const deleteBtn = document.getElementById('deleteBtn');

            if (deleteBtn) {
                deleteBtn.addEventListener('click', function(e) {
                    // On récupère le formulaire parent du bouton cliqué
                    const deleteForm = document.getElementById('deleteAccountForm');

                    if (!deleteForm) {
                        console.error("Erreur : Formulaire deleteAccountForm introuvable !");
                        return;
                    }

                    const confirmDelete = confirm("Êtes-vous absolument sûr de vouloir supprimer votre compte ? C'est irréversible.");
                    if (confirmDelete) {
                        const finalCheck = confirm("Confirmez-vous la suppression définitive de toutes vos données ?");
                        if (finalCheck) {
                            deleteForm.submit();
                        }
                    }
                });
            }
        });

        document.getElementById('editProfileForm').addEventListener('submit', function(e) {
            const pseudonyme = document.getElementById('pseudonyme').value.trim();
            const email = document.getElementById('email').value.trim();
            const dateNaissance = document.getElementById('date_naissance').value;

            let isValid = true;
            let errorMessage = '';

            if (pseudonyme.length < 3) {
                isValid = false;
                errorMessage += '• Le pseudonyme doit contenir au moins 3 caractères\n';
            }

            if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                isValid = false;
                errorMessage += '• Veuillez entrer une adresse email valide\n';
            }

            if (!dateNaissance) {
                isValid = false;
                errorMessage += '• La date de naissance est requise\n';
            } else {
                const birthDate = new Date(dateNaissance);
                const minDate = new Date();
                minDate.setFullYear(minDate.getFullYear() - 13);

                if (birthDate > minDate) {
                    isValid = false;
                    errorMessage += '• Vous devez avoir au moins 13 ans\n';
                }
            }

            const fileInput = document.getElementById('photo_profil');
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    isValid = false;
                    errorMessage += '• L\'image est trop volumineuse (max 5MB)\n';
                }

                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    isValid = false;
                    errorMessage += '• Type de fichier non autorisé (JPEG, PNG, GIF seulement)\n';
                }
            }

            if (!isValid) {
                e.preventDefault();
                alert('Veuillez corriger les erreurs suivantes:\n\n' + errorMessage);
            }
        });

        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('href').startsWith('#')) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        document.querySelectorAll('.sidebar-menu a').forEach(item => {
                            item.classList.remove('active');
                        });

                        this.classList.add('active');
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });

        document.getElementById('avatarModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAvatarModal();
            }
        });

        const fileUpload = document.querySelector('.file-upload');
        const fileInput = document.getElementById('photo_profil');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileUpload.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function highlight() {
            fileUpload.style.borderColor = 'var(--principal)';
            fileUpload.style.backgroundColor = 'rgba(79, 189, 171, 0.1)';
        }

        function unhighlight() {
            fileUpload.style.borderColor = 'var(--gray-light)';
            fileUpload.style.backgroundColor = 'var(--light)';
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            fileUpload.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileUpload.addEventListener(eventName, unhighlight, false);
        });

        fileUpload.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>

</html>