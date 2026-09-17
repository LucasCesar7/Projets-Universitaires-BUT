<?php
$errors = isset($errors) && is_array($errors) ? $errors : [];
$data = isset($data) && is_array($data) ? $data : [];
$countries = isset($countries) && is_array($countries) ? $countries : [];
$captchaData = isset($captcha) && is_array($captcha) ? $captcha : ['required' => false, 'question' => ''];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Social Media Awards</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/base.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/inscription.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <main class="inscription-container">
        <div class="container">
            <div class="inscription-header">
                <h1><i class="fas fa-user-plus"></i> Créer un Compte</h1>
                <p>Rejoignez la communauté Social Media Awards</p>
            </div>

            <?php if ($errors !== []): ?>
                <!-- Ajout de role="alert" pour l'accessibilité -->
                <div class="alert alert-danger" role="alert" id="error-summary">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <h3>Erreurs de validation</h3>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <div class="inscription-card">
                <form method="POST" action="" class="inscription-form" id="inscriptionForm">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Services\CsrfService::token('register'), ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="pseudonyme" class="form-label">
                                <i class="fas fa-user"></i> Pseudonyme *
                            </label>
                            <!-- Ajout autocomplete et aria-describedby -->
                            <input type="text"
                                id="pseudonyme"
                                name="pseudonyme"
                                class="form-control"
                                value="<?php echo htmlspecialchars((string) ($data['pseudonyme'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                required
                                minlength="3"
                                maxlength="50"
                                autocomplete="username"
                                aria-describedby="pseudonymeHelp"
                                placeholder="Votre nom public">
                            <small id="pseudonymeHelp" class="form-text">Votre nom public (3-50 caractères)</small>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Adresse email *
                            </label>
                            <!-- Ajout autocomplete et aria-describedby -->
                            <input type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                value="<?php echo htmlspecialchars((string) ($data['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                required
                                autocomplete="email"
                                aria-describedby="emailHelp"
                                placeholder="exemple@email.com">
                            <small id="emailHelp" class="form-text">Nous ne partagerons jamais votre email</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="mot_de_passe" class="form-label">
                                <i class="fas fa-lock"></i> Mot de passe *
                            </label>
                            <!-- Ajout autocomplete et aria-describedby -->
                            <input type="password"
                                id="mot_de_passe"
                                name="mot_de_passe"
                                class="form-control"
                                required
                                minlength="6"
                                autocomplete="new-password"
                                aria-describedby="passwordHelp"
                                placeholder="••••••">
                            <small id="passwordHelp" class="form-text">Minimum 8 caractères (majuscule, minuscule, chiffre)</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_mot_de_passe" class="form-label">
                                <i class="fas fa-lock"></i> Confirmer le mot de passe *
                            </label>
                            <!-- Ajout autocomplete -->
                            <input type="password"
                                id="confirm_mot_de_passe"
                                name="confirm_mot_de_passe"
                                class="form-control"
                                required
                                autocomplete="new-password"
                                placeholder="••••••">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="type_user" class="form-label">
                                <i class="fas fa-user-tag"></i> Type de compte *
                            </label>
                            <!-- Ajout aria-describedby -->
                            <select id="type_user" name="type_user" class="form-control" required aria-describedby="typeUserHelp">
                                <option value="">Sélectionnez un type</option>
                                <option value="voter" <?php echo ($data['type_user'] ?? '') === 'voter' ? 'selected' : ''; ?>>
                                    Électeur - Je veux voter pour mes favoris
                                </option>
                                <option value="candidate" <?php echo ($data['type_user'] ?? '') === 'candidate' ? 'selected' : ''; ?>>
                                    Candidat - Je veux participer aux élections
                                </option>
                            </select>
                            <small id="typeUserHelp" class="form-text">Vous pourrez modifier ce choix plus tard</small>
                        </div>

                        <div class="form-group">
                            <label for="date_naissance" class="form-label">
                                <i class="fas fa-birthday-cake"></i> Date de naissance *
                            </label>
                            <!-- Ajout autocomplete et aria-describedby -->
                            <input type="date"
                                id="date_naissance"
                                name="date_naissance"
                                class="form-control"
                                value="<?php echo htmlspecialchars((string) ($data['date_naissance'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                required
                                autocomplete="bday"
                                aria-describedby="bdayHelp"
                                max="<?php echo date('Y-m-d', strtotime('-13 years')); ?>">
                            <small id="bdayHelp" class="form-text">Vous devez avoir au moins 13 ans</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="pays" class="form-label">
                                <i class="fas fa-globe"></i> Pays *
                            </label>
                            <!-- Ajout autocomplete -->
                            <select id="pays" name="pays" class="form-control" required autocomplete="country">
                                <option value="">Sélectionnez votre pays</option>
                                <?php foreach ($countries as $country): ?>
                                    <option value="<?php echo htmlspecialchars($country, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($data['pays'] ?? '') === $country ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($country, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Transformation en fieldset pour l'accessibilité -->
                        <fieldset class="form-group" style="border: none; padding: 0; margin: 0;">
                            <legend class="form-label" style="margin-bottom: 0.5rem; display: block; width: 100%;">
                                <i class="fas fa-venus-mars"></i> Genre
                            </legend>
                            <div class="radio-group">
                                <label class="radio-label" for="inscription_genre_homme">
                                    <input type="radio" name="genre" id="inscription_genre_homme" value="Homme" <?php echo ($data['genre'] ?? '') === 'Homme' ? 'checked' : ''; ?>>
                                    <span>Homme</span>
                                </label>
                                <label class="radio-label" for="inscription_genre_femme">
                                    <input type="radio" name="genre" id="inscription_genre_femme" value="Femme" <?php echo ($data['genre'] ?? '') === 'Femme' ? 'checked' : ''; ?>>
                                    <span>Femme</span>
                                </label>
                                <label class="radio-label" for="inscription_genre_autre">
                                    <input type="radio" name="genre" id="inscription_genre_autre" value="Autre" <?php echo ($data['genre'] ?? '') === 'Autre' ? 'checked' : ''; ?>>
                                    <span>Autre</span>
                                </label>
                                <label class="radio-label" for="inscription_genre_prefere_pas_dire">
                                    <input type="radio" name="genre" id="inscription_genre_prefere_pas_dire" value="" <?php echo empty($data['genre'] ?? '') ? 'checked' : ''; ?>>
                                    <span>Préfère ne pas dire</span>
                                </label>
                            </div>
                        </fieldset>
                    </div>

                    <div class="form-group terms-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" id="terms" required>
                            <span>J'accepte les <a href="<?php echo htmlspecialchars(publicRouteUrl('cgu'), ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer" class="terms-link">conditions d'utilisation</a> et la <a href="<?php echo htmlspecialchars(publicRouteUrl('privacy'), ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer" class="privacy-link">politique de confidentialité</a> *</span>
                        </label>
                    </div>

                    <?php if (!empty($captchaData['required'])): ?>
                        <div class="form-group">
                            <label for="captcha_answer" class="form-label">
                                <i class="fas fa-shield-alt"></i> Vérification CAPTCHA *
                            </label>
                            <input type="text"
                                   id="captcha_answer"
                                   name="captcha_answer"
                                   class="form-control"
                                   required
                                   autocomplete="off"
                                   inputmode="numeric"
                                   pattern="[0-9]+"
                                   aria-describedby="captchaHelp"
                                   placeholder="<?php echo htmlspecialchars((string) ($captchaData['question'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                            <small id="captchaHelp" class="form-text"><?php echo htmlspecialchars((string) ($captchaData['question'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></small>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-user-plus"></i> Créer mon compte
                    </button>
                </form>

                <div class="inscription-footer">
                    <p>Déjà un compte ? <a href="<?php echo htmlspecialchars(publicRouteUrl('login'), ENT_QUOTES, 'UTF-8'); ?>" class="login-link">Connectez-vous ici</a></p>
                    <p><a href="<?php echo htmlspecialchars(publicRouteUrl('home'), ENT_QUOTES, 'UTF-8'); ?>" class="back-link"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a></p>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('inscriptionForm').addEventListener('submit', function(e) {
            const password = document.getElementById('mot_de_passe').value;
            const confirmPassword = document.getElementById('confirm_mot_de_passe').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                return false;
            }

            const terms = document.getElementById('terms');
            if (!terms.checked) {
                e.preventDefault();
                alert('Vous devez accepter les conditions d\'utilisation.');
                return false;
            }

            return true;
        });
    </script>
</body>

</html>