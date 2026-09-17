<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer le mot de passe - Social Media Awards</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($editProfileCssUrl); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <header class="dashboard-header">
        <div class="header-content">
            <div class="logo-section">
                <img loading="lazy" src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Social Media Awards" class="logo-image">
                <h1>Social Media <span class="highlight">Awards</span></h1>
            </div>

            <nav class="user-nav">
                <div class="user-info-nav">
                    <div class="avatar-nav"><?php echo htmlspecialchars($initials); ?></div>
                    <div class="user-details-nav">
                        <span class="user-name-nav"><?php echo htmlspecialchars($userPseudonyme); ?></span>
                        <span class="user-role-nav">Électeur</span>
                    </div>
                </div>

                <a href="<?php echo htmlspecialchars($editProfileUrl); ?>" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
            </nav>
        </div>
    </header>

    <main class="edit-profile-container">
        <div class="edit-profile-main">
            <nav class="breadcrumb">
                <ul>
                    <li><a href="<?php echo htmlspecialchars($userDashboardUrl); ?>">Tableau de bord</a></li>
                    <li class="separator"><i class="fas fa-chevron-right"></i></li>
                    <li><a href="<?php echo htmlspecialchars($editProfileUrl); ?>">Profil</a></li>
                    <li class="separator"><i class="fas fa-chevron-right"></i></li>
                    <li class="current">Changer le mot de passe</li>
                </ul>
            </nav>

            <div class="profile-grid">
                <aside class="profile-sidebar">
                    <div class="sidebar-card">
                        <nav class="sidebar-menu">
                            <a href="<?php echo htmlspecialchars($editProfileUrl); ?>" class="menu-item">
                                <i class="fas fa-user"></i>
                                Informations personnelles
                            </a>
                            <a href="#current" class="menu-item active">
                                <i class="fas fa-key"></i>
                                Changer mot de passe
                            </a>
                            <a href="<?php echo htmlspecialchars($editProfileUrl . '#securite'); ?>" class="menu-item">
                                <i class="fas fa-shield-alt"></i>
                                Sécurité
                            </a>
                        </nav>
                    </div>
                </aside>

                <section class="edit-section">
                    <div class="section-header">
                        <h2><i class="fas fa-key"></i> Changer le mot de passe</h2>
                        <p>Mettez à jour votre mot de passe pour renforcer la sécurité de votre compte.</p>
                    </div>

                    <?php if ($success): ?>
                        <div style="background: rgba(50, 213, 131, 0.1); border: 2px solid var(--success); border-radius: var(--radius-md); padding: var(--space-md); margin-bottom: var(--space-lg);">
                            <i class="fas fa-check-circle" style="color: var(--success); margin-right: var(--space-sm);"></i>
                            <span style="color: var(--success); font-weight: 500;"><?php echo htmlspecialchars($success); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div style="background: rgba(255, 107, 107, 0.1); border: 2px solid var(--danger); border-radius: var(--radius-md); padding: var(--space-md); margin-bottom: var(--space-lg);">
                            <div style="color: var(--danger); margin-bottom: var(--space-sm);">
                                <i class="fas fa-exclamation-circle"></i>
                                <strong>Erreurs:</strong>
                            </div>
                            <ul style="color: var(--danger); padding-left: var(--space-md);">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="edit-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($changePasswordCsrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label for="current_password" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Mot de passe actuel <span class="required">*</span>
                                </label>
                                <input type="password"
                                       id="current_password"
                                       name="current_password"
                                       class="form-control"
                                       required
                                       placeholder="Entrez votre mot de passe actuel">
                                <small class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Confirmez votre identité avec votre mot de passe actuel
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="new_password" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Nouveau mot de passe <span class="required">*</span>
                                </label>
                                <input type="password"
                                       id="new_password"
                                       name="new_password"
                                       class="form-control"
                                       required
                                       minlength="6"
                                       placeholder="Minimum 6 caractères">
                                <small class="form-text">
                                    <i class="fas fa-shield-alt"></i>
                                    Utilisez un mot de passe fort et unique
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Confirmer le mot de passe <span class="required">*</span>
                                </label>
                                <input type="password"
                                       id="confirm_password"
                                       name="confirm_password"
                                       class="form-control"
                                       required
                                       placeholder="Répétez le nouveau mot de passe">
                                <small class="form-text">
                                    <i class="fas fa-check-circle"></i>
                                    Les deux mots de passe doivent correspondre
                                </small>
                            </div>

                            <div class="form-group full-width">
                                <div style="background: var(--light); border-radius: var(--radius-md); padding: var(--space-md); margin-top: var(--space-sm);">
                                    <h4 style="color: var(--dark); margin-bottom: var(--space-sm);">
                                        <i class="fas fa-lightbulb"></i>
                                        Conseils pour un mot de passe sécurisé
                                    </h4>
                                    <ul style="color: var(--gray); font-size: 0.9rem; padding-left: var(--space-md);">
                                        <li>Utilisez au moins 8 caractères</li>
                                        <li>Combinez lettres majuscules et minuscules</li>
                                        <li>Ajoutez des chiffres et caractères spéciaux</li>
                                        <li>Évitez les mots du dictionnaire</li>
                                        <li>Ne réutilisez pas d'anciens mots de passe</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="btn-left-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Enregistrer le nouveau mot de passe
                                </button>
                                <a href="<?php echo htmlspecialchars($editProfileUrl); ?>" class="btn btn-outline">
                                    <i class="fas fa-times"></i>
                                    Annuler
                                </a>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </main>

    <script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (newPassword.length < 6) {
            e.preventDefault();
            alert('Le nouveau mot de passe doit contenir au moins 6 caractères.');
            return false;
        }

        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('Les nouveaux mots de passe ne correspondent pas.');
            return false;
        }

        return true;
    });

    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        if (field.type === 'password') {
            field.type = 'text';
        } else {
            field.type = 'password';
        }
    }
    </script>
</body>
</html>