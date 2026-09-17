<?php
$error = is_string($error ?? null) ? $error : '';
$redirectUrl = isset($redirectUrl) && is_string($redirectUrl) ? $redirectUrl : '';
$postedEmail = isset($postedEmail) && is_string($postedEmail) ? $postedEmail : '';
$captchaData = isset($captcha) && is_array($captcha) ? $captcha : ['required' => false, 'question' => ''];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Social Media Awards</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/login.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php require appPath('views/partials/header.php'); ?>

    <div class="login-container">
        <div class="login-header">
            <h1>Connexion</h1>
            <p>Accédez à votre compte Social Media Awards</p>
        </div>

        <div class="login-card">
            <?php if ($error !== ''): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Services\CsrfService::token('login'), ENT_QUOTES, 'UTF-8'); ?>">
                <?php if ($redirectUrl !== ''): ?>
                    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirectUrl, ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Adresse email
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           placeholder="votre@email.com"
                           required
                           value="<?php echo htmlspecialchars($postedEmail, ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="form-group">
                    <label for="mot_de_passe">
                        <i class="fas fa-lock"></i> Mot de passe
                    </label>
                    <input type="password"
                           id="mot_de_passe"
                           name="mot_de_passe"
                           placeholder="Votre mot de passe"
                           required>
                </div>

                <?php if (!empty($captchaData['required'])): ?>
                    <div class="form-group">
                        <label for="captcha_answer">
                            <i class="fas fa-shield-alt"></i> Vérification CAPTCHA *
                        </label>
                        <input type="text"
                               id="captcha_answer"
                               name="captcha_answer"
                               placeholder="<?php echo htmlspecialchars((string) ($captchaData['question'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                               required
                               autocomplete="off"
                               inputmode="numeric"
                               pattern="[0-9]+">
                        <small class="form-text"><?php echo htmlspecialchars((string) ($captchaData['question'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></small>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>

            <div class="login-footer">
                <p>Pas encore de compte ?
                   <a href="<?php echo htmlspecialchars(publicRouteUrl('register'), ENT_QUOTES, 'UTF-8'); ?>" class="register-link">S'inscrire</a>
                </p>
                <p>
                    <a href="<?php echo htmlspecialchars(publicRouteUrl('home'), ENT_QUOTES, 'UTF-8'); ?>">
                        <i class="fas fa-arrow-left"></i> Retour à l'accueil
                    </a>
                </p>
            </div>
        </div>

        <div class="login-security">
            <h3><i class="fas fa-shield-alt"></i> Sécurité</h3>
            <ul>
                <li><i class="fas fa-check-circle"></i> Connexion sécurisée</li>
                <li><i class="fas fa-check-circle"></i> Données chiffrées</li>
                <li><i class="fas fa-check-circle"></i> Sessions protégées</li>
            </ul>
        </div>
    </div>

    <script src="<?php echo htmlspecialchars(appUrl('assets/js/login.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>

</html>