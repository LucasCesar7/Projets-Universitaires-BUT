<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de Confidentialité - Social Media Awards</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/base.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/privacy.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="<?php echo htmlspecialchars((string) resolveAppAssetUrl('assets/images/favicon.ico', 'assets/images/logo.png'), ENT_QUOTES, 'UTF-8'); ?>">
</head>

<body>
    <?php require appPath('views/partials/header.php'); ?>

    <main class="privacy-container">
        <div class="container">
            <nav class="breadcrumb">
                <ol>
                    <li><a href="<?php echo htmlspecialchars(publicRouteUrl('home'), ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-home"></i> Accueil</a></li>
                    <li><i class="fas fa-chevron-right"></i></li>
                    <li>Politique de Confidentialité</li>
                </ol>
            </nav>

            <div class="privacy-header">
                <h1><i class="fas fa-shield-alt"></i> Politique de Confidentialité</h1>
                <p class="privacy-subtitle">Conforme au RGPD - Dernière mise à jour : Novembre 2025</p>
                <div class="rgpd-badge">
                    <span class="badge"><i class="fas fa-check-circle"></i> RGPD Compliant</span>
                    <span class="badge"><i class="fas fa-lock"></i> Sécurité des données</span>
                    <span class="badge"><i class="fas fa-user-shield"></i> Vie privée respectée</span>
                </div>
            </div>

            <div class="privacy-content">
                <div class="privacy-card">
                    <div class="privacy-intro">
                        <p>Cette politique de confidentialité décrit comment Social Media Awards collecte, utilise et protège vos données personnelles conformément au Règlement Général sur la Protection des Données (RGPD) et à la législation française.</p>
                    </div>

                    <div class="privacy-section">
                        <h2><span class="section-number">1</span> Responsable du traitement</h2>
                        <div class="info-box">
                            <ul>
                                <li><strong>Nom :</strong> Social Media Awards</li>
                                <li><strong>Email DPO :</strong> <a href="mailto:dpo@socialmediaawards.fr">dpo@socialmediaawards.fr</a></li>
                                <li><strong>Siège social :</strong> Saint-Dié-Des-Vosges, France</li>
                                <li><strong>Contact général :</strong> <a href="mailto:contact@socialmediaawards.fr">contact@socialmediaawards.fr</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="privacy-section">
                        <h2><span class="section-number">2</span> Données collectées</h2>
                        <div class="data-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Type de données</th>
                                        <th>Exemples</th>
                                        <th>Sensibilité</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Données d'identification</td>
                                        <td>Pseudonyme, email, date de naissance, pays, genre</td>
                                        <td><span class="badge-normal">Normale</span></td>
                                    </tr>
                                    <tr>
                                        <td>Données de candidature</td>
                                        <td>Nom légal, profils sociaux, contenus soumis</td>
                                        <td><span class="badge-normal">Normale</span></td>
                                    </tr>
                                    <tr>
                                        <td>Données de vote</td>
                                        <td>Horodatage, identifiant de session, choix</td>
                                        <td><span class="badge-sensitive">Sensible</span></td>
                                    </tr>
                                    <tr>
                                        <td>Données techniques</td>
                                        <td>Adresse IP, logs de connexion</td>
                                        <td><span class="badge-normal">Normale</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="privacy-section">
                        <h2><span class="section-number">3</span> Finalités du traitement</h2>
                        <div class="purpose-grid">
                            <div class="purpose-item">
                                <div class="purpose-icon"><i class="fas fa-user-check"></i></div>
                                <h4>Gestion des comptes</h4>
                                <p>Création et gestion des comptes utilisateurs</p>
                            </div>
                            <div class="purpose-item">
                                <div class="purpose-icon"><i class="fas fa-vote-yea"></i></div>
                                <h4>Organisation du vote</h4>
                                <p>Gestion du processus électoral et publication des résultats</p>
                            </div>
                            <div class="purpose-item">
                                <div class="purpose-icon"><i class="fas fa-user-tie"></i></div>
                                <h4>Gestion candidatures</h4>
                                <p>Évaluation et publication des nominations</p>
                            </div>
                            <div class="purpose-item">
                                <div class="purpose-icon"><i class="fas fa-shield-alt"></i></div>
                                <h4>Sécurité</h4>
                                <p>Prévention des fraudes et sécurisation du système</p>
                            </div>
                        </div>
                    </div>

                    <div class="privacy-section">
                        <h2><span class="section-number">4</span> Bases légales (RGPD)</h2>
                        <div class="legal-bases">
                            <div class="legal-item">
                                <h4><i class="fas fa-handshake"></i> Consentement (Art. 6.1.a)</h4>
                                <p>Pour l'inscription et la participation au vote</p>
                            </div>
                            <div class="legal-item">
                                <h4><i class="fas fa-file-contract"></i> Exécution du contrat (Art. 6.1.b)</h4>
                                <p>Pour la fourniture du service de vote</p>
                            </div>
                            <div class="legal-item">
                                <h4><i class="fas fa-balance-scale"></i> Intérêt légitime (Art. 6.1.f)</h4>
                                <p>Pour la sécurité et prévention des fraudes</p>
                            </div>
                            <div class="legal-item">
                                <h4><i class="fas fa-landmark"></i> Mission d'intérêt public (Art. 6.1.e)</h4>
                                <p>Pour l'organisation du scrutin</p>
                            </div>
                        </div>
                    </div>

                    <div class="privacy-section">
                        <h2><span class="section-number">5</span> Sécurité et anonymat du vote</h2>
                        <div class="security-features">
                            <div class="security-item"><i class="fas fa-user-secret"></i><h4>Anonymat irréversible</h4><p>Dissociation entre identité et choix de vote</p></div>
                            <div class="security-item"><i class="fas fa-lock"></i><h4>Chiffrement</h4><p>Données sensibles chiffrées de bout en bout</p></div>
                            <div class="security-item"><i class="fas fa-clipboard-list"></i><h4>Journalisation</h4><p>Traçabilité sécurisée des opérations</p></div>
                            <div class="security-item"><i class="fas fa-shield-virus"></i><h4>Protection</h4><p>Mesures contre les accès non autorisés</p></div>
                        </div>
                        <p class="security-note"><i class="fas fa-info-circle"></i> Conforme aux recommandations CNIL 2019-158 sur les systèmes de vote électronique.</p>
                    </div>

                    <div class="privacy-section">
                        <h2><span class="section-number">6</span> Durée de conservation</h2>
                        <div class="retention-chart">
                            <div class="retention-item"><div class="retention-bar" style="width: 40%"></div><div class="retention-info"><h4>Données de compte</h4><p>1 an après dernière connexion</p></div></div>
                            <div class="retention-item"><div class="retention-bar" style="width: 20%"></div><div class="retention-info"><h4>Données de vote</h4><p>Anonymisées après 6 mois</p></div></div>
                            <div class="retention-item"><div class="retention-bar" style="width: 80%"></div><div class="retention-info"><h4>Données candidature</h4><p>2 ans pour les nominés</p></div></div>
                            <div class="retention-item"><div class="retention-bar" style="width: 10%"></div><div class="retention-info"><h4>Logs techniques</h4><p>3 mois maximum</p></div></div>
                        </div>
                    </div>

                    <div class="privacy-section">
                        <h2><span class="section-number">7</span> Vos droits (RGPD)</h2>
                        <div class="rights-grid">
                            <div class="right-item"><div class="right-icon access"><i class="fas fa-eye"></i></div><h4>Droit d'accès</h4><p>Connaître les données vous concernant</p></div>
                            <div class="right-item"><div class="right-icon rectification"><i class="fas fa-edit"></i></div><h4>Droit de rectification</h4><p>Corriger des données inexactes</p></div>
                            <div class="right-item"><div class="right-icon deletion"><i class="fas fa-trash-alt"></i></div><h4>Droit à l'effacement</h4><p>Supprimer vos données</p></div>
                            <div class="right-item"><div class="right-icon portability"><i class="fas fa-download"></i></div><h4>Droit à la portabilité</h4><p>Récupérer vos données</p></div>
                            <div class="right-item"><div class="right-icon opposition"><i class="fas fa-ban"></i></div><h4>Droit d'opposition</h4><p>Vous opposer au traitement</p></div>
                            <div class="right-item"><div class="right-icon limitation"><i class="fas fa-pause"></i></div><h4>Droit à la limitation</h4><p>Restreindre le traitement</p></div>
                        </div>
                        <div class="rights-exercise">
                            <h4><i class="fas fa-envelope"></i> Comment exercer vos droits ?</h4>
                            <p>Envoyez votre demande à : <a href="mailto:dpo@socialmediaawards.fr">dpo@socialmediaawards.fr</a></p>
                            <p>Nous répondrons dans un délai maximum d'1 mois.</p>
                        </div>
                    </div>

                    <div class="privacy-section">
                        <h2><span class="section-number">8</span> Transferts de données</h2>
                        <div class="transfer-info">
                            <div class="transfer-icon"><i class="fas fa-globe-europe"></i></div>
                            <div class="transfer-details">
                                <h4>Hébergement dans l'UE</h4>
                                <p>Vos données sont hébergées exclusivement dans l'Union Européenne et ne sont pas transférées en dehors de l'UE.</p>
                                <p><i class="fas fa-exclamation-triangle"></i> Nous ne vendons ni ne louons vos données personnelles à des tiers.</p>
                            </div>
                        </div>
                    </div>

                    <div class="privacy-section">
                        <h2><span class="section-number">9</span> Cookies</h2>
                        <div class="cookies-info">
                            <div class="cookie-type"><h4><i class="fas fa-cookie-bite"></i> Cookies techniques</h4><p>Essentiels au fonctionnement de la plateforme</p><span class="cookie-required">Obligatoire</span></div>
                            <div class="cookie-type"><h4><i class="fas fa-chart-line"></i> Cookies analytiques</h4><p>Anonymisés pour améliorer nos services</p><span class="cookie-optional">Optionnel</span></div>
                        </div>
                        <p class="cookies-note">Vous pouvez configurer vos préférences cookies dans les paramètres de votre navigateur.</p>
                    </div>

                    <div class="privacy-section">
                        <h2><span class="section-number">10</span> Modifications</h2>
                        <div class="modification-alert"><i class="fas fa-bell"></i><p>Cette politique peut être mise à jour pour rester conforme à la réglementation. Les modifications importantes seront notifiées par email aux utilisateurs.</p></div>
                    </div>

                    <div class="privacy-contact">
                        <div class="contact-card">
                            <h3><i class="fas fa-headset"></i> Contact et assistance</h3>
                            <div class="contact-details">
                                <div class="contact-item"><i class="fas fa-user-shield"></i><div><h4>Délégué à la Protection des Données (DPO)</h4><p><a href="mailto:dpo@socialmediaawards.fr">dpo@socialmediaawards.fr</a></p></div></div>
                                <div class="contact-item"><i class="fas fa-landmark"></i><div><h4>Autorité de contrôle</h4><p>Commission Nationale de l'Informatique et des Libertés (CNIL)</p><p><a href="https://www.cnil.fr" target="_blank" rel="noreferrer">www.cnil.fr</a></p></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="privacy-navigation">
                <a href="<?php echo htmlspecialchars(publicRouteUrl('cgu'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-file-contract"></i> Voir les Conditions Générales
                </a>
                <a href="<?php echo htmlspecialchars(publicRouteUrl('home'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                    <i class="fas fa-home"></i> Retour à l'accueil
                </a>
            </div>
        </div>
    </main>

    <?php require appPath('views/partials/footer.php'); ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bars = document.querySelectorAll('.retention-bar');
            bars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.transition = 'width 1.5s ease-in-out';
                    bar.style.width = width;
                }, 300);
            });

            const sections = document.querySelectorAll('.privacy-section');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('highlighted');
                    }
                });
            }, { threshold: 0.2 });

            sections.forEach(section => observer.observe(section));
        });
    </script>
</body>

</html>