<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/header.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/footer.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/contact.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>FAQ - Social Media Awards 2026</title>
</head>

<body>
    <?php require appPath('views/partials/header.php'); ?>

    <div class="main-content">
        <section class="contact-hero">
            <div class="container">
                <h1>Foire aux questions</h1>
                <p>Retrouvez ici les réponses aux questions les plus fréquentes sur les votes, les candidatures et le déroulement des Social Media Awards.</p>
            </div>
        </section>

        <section class="faq-section">
            <div class="container">
                <h2>Questions fréquentes</h2>
                <div class="faq-grid">
                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Comment participer aux Social Media Awards ?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Vous pouvez participer soit en soumettant une candidature pendant la période dédiée, soit en votant pour vos créateurs préférés lorsque les catégories sont ouvertes au vote.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Comment savoir si une catégorie est ouverte au vote ?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>La page des catégories affiche uniquement les catégories actives, avec leurs périodes de vote et leur disponibilité. Une catégorie fermée ou sans nominé ne peut pas être votée.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Puis-je voter plusieurs fois dans la même catégorie ?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Non. Chaque électeur ne peut voter qu'une seule fois par catégorie. Une fois votre vote enregistré, la catégorie apparaît comme déjà votée dans votre espace.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Comment sont déterminés les résultats ?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Les résultats reposent sur les votes valides enregistrés pendant la période officielle de vote. Les classements ne sont publiés qu'une fois les votes clos.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Comment soumettre une candidature ?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Les créateurs peuvent déposer leur candidature depuis leur espace candidat pendant la période d'inscription. Les dossiers validés peuvent ensuite être intégrés aux nominations.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Je n'ai pas trouvé ma réponse, que faire ?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Vous pouvez nous contacter directement via la page <a href="<?php echo htmlspecialchars($contactUrl, ENT_QUOTES, 'UTF-8'); ?>">Contact</a>. Nous vous répondrons dans les meilleurs délais.</p>
                        </div>
                    </div>
                </div>

                <div class="contact-info-container" style="margin-top: 3rem;">
                    <h2>Liens utiles</h2>
                    <p class="info-description">Accédez rapidement aux pages les plus consultées pendant l'événement.</p>
                    <div class="contact-methods">
                        <div class="contact-method">
                            <div class="method-icon">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="method-content">
                                <h3>Catégories</h3>
                                <p><a href="<?php echo htmlspecialchars($categoriesUrl, ENT_QUOTES, 'UTF-8'); ?>">Voir les catégories ouvertes</a></p>
                                <span>Consulter les catégories actives</span>
                            </div>
                        </div>
                        <div class="contact-method">
                            <div class="method-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="method-content">
                                <h3>Résultats</h3>
                                <p><a href="<?php echo htmlspecialchars($resultsUrl, ENT_QUOTES, 'UTF-8'); ?>">Consulter les résultats</a></p>
                                <span>Disponible après la clôture des votes</span>
                            </div>
                        </div>
                        <div class="contact-method">
                            <div class="method-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="method-content">
                                <h3>Support</h3>
                                <p><a href="<?php echo htmlspecialchars($contactUrl, ENT_QUOTES, 'UTF-8'); ?>">Contacter l'équipe</a></p>
                                <span>Question technique ou générale</span>
                            </div>
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