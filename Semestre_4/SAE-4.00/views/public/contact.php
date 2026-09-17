<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/header.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/footer.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/contact.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Contact - Social Media Awards 2025</title>
</head>

<body>
    <?php require appPath('views/partials/header.php'); ?>

    <div class="main-content">
        <section class="contact-hero">
            <div class="container">
                <h1>Contactez-nous</h1>
                <p>Nous sommes là pour répondre à toutes vos questions sur les Social Media Awards</p>
            </div>
        </section>

        <section class="contact-section">
            <div class="container">
                <div class="contact-grid">
                    <div class="contact-form-container">
                        <h2>Envoyez-nous un message</h2>
                        <p class="form-description">Remplissez le formulaire ci-dessous et nous vous répondrons dans les plus brefs délais.</p>

                        <form class="contact-form" id="contactForm">
                            <div class="form-group">
                                <label for="name">Nom complet *</label>
                                <input type="text" id="name" name="name" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Adresse email *</label>
                                <input type="email" id="email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label for="subject">Sujet *</label>
                                <select id="subject" name="subject" required>
                                    <option value="">Sélectionnez un sujet</option>
                                    <option value="general">Question générale</option>
                                    <option value="participation">Participation aux awards</option>
                                    <option value="sponsorship">Partenariat et sponsoring</option>
                                    <option value="technical">Problème technique</option>
                                    <option value="press">Presse et médias</option>
                                    <option value="other">Autre</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="message">Message *</label>
                                <textarea id="message" name="message" rows="6" required></textarea>
                            </div>

                            <button type="submit" class="submit-button">
                                <i class="fas fa-paper-plane"></i>
                                Envoyer le message
                            </button>
                        </form>
                    </div>

                    <div class="contact-info-container">
                        <h2>Nos coordonnées</h2>
                        <p class="info-description">N'hésitez pas à nous contacter par l'un des moyens suivants :</p>

                        <div class="contact-methods">
                            <div class="contact-method">
                                <div class="method-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="method-content">
                                    <h3>Email</h3>
                                    <p>contact@socialmediaawards.com</p>
                                    <span>Réponse sous 24h</span>
                                </div>
                            </div>

                            <div class="contact-method">
                                <div class="method-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="method-content">
                                    <h3>Téléphone</h3>
                                    <p>+33 1 23 45 67 89</p>
                                    <span>Lun-Ven, 9h-18h</span>
                                </div>
                            </div>

                            <div class="contact-method">
                                <div class="method-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="method-content">
                                    <h3>Adresse</h3>
                                    <p>123 Avenue des Champs-Élysées<br>88100 Saint-Dié-des-Vosges, France</p>
                                    <span>Sur rendez-vous uniquement</span>
                                </div>
                            </div>

                            <div class="contact-method">
                                <div class="method-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="method-content">
                                    <h3>Horaires</h3>
                                    <p>Lundi - Vendredi<br>9h00 - 18h00</p>
                                    <span>Fermé le week-end</span>
                                </div>
                            </div>
                        </div>

                        <div class="social-contact">
                            <h3>Suivez-nous sur les réseaux</h3>
                            <div class="social-links">
                                <?php foreach (publicSocialLinks() as $socialLink): ?>
                                    <a href="<?php echo htmlspecialchars($socialLink['url']); ?>" class="social-link" aria-label="<?php echo htmlspecialchars($socialLink['label']); ?>">
                                        <i class="<?php echo htmlspecialchars($socialLink['icon']); ?>"></i>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="faq-section" id="faq">
            <div class="container">
                <h2>Questions fréquentes</h2>
                <div class="faq-grid">
                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Comment puis-je participer aux Social Media Awards ?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Vous pouvez participer en soumettant votre candidature pendant la période d'inscription ou en votant pour vos créateurs préférés pendant la période de vote. Consultez notre calendrier pour connaître les dates importantes.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Quelles sont les catégories disponibles ?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Nous avons plus de 12 catégories couvrant différentes plateformes et types de contenu. Vous pouvez consulter toutes les catégories sur notre page dédiée.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Comment sont sélectionnés les gagnants ?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Les gagnants sont déterminés par un système basé sur les votes du public.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Puis-je devenir partenaire ou sponsor ?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Absolument ! Nous sommes toujours ouverts aux partenariats. Contactez-nous via le formulaire en sélectionnant "Partenariat et sponsoring" comme sujet.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Quand seront annoncés les résultats ?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Les résultats seront annoncés lors de la cérémonie de remise des prix. Vous pourrez également les consulter sur notre site web immédiatement après l'événement.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Comment puis-je assister à la cérémonie ?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Les billets pour la cérémonie seront disponibles en vente en ligne environ un mois avant l'événement. Les nominés recevront des invitations spéciales.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php require appPath('views/partials/footer.php'); ?>
    <script src="<?php echo htmlspecialchars(appUrl('assets/js/contact.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>

</html>