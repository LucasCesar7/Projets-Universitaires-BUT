<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>


<?php require '../app/views/layouts/header.php'; ?>
<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/accueil.css">

<section class="bienvenue-site">
    <div class="video-wrapper fade-in-div">
        <video autoplay muted loop playsinline>
            <source src="/sae-3-festivote-tas-cesar/public/video.php?file=background presentation.mp4" type="video/mp4">
        </video>
        <div class="content">
            <h1>Bienvenue sur <span class="highlight">CineVote 2026</span></h1>
            <p>
                <strong>CineVote</strong> est une plateforme interactive conçue pour offrir une nouvelle manière de vivre le festival du cinéma.
                Plus qu’un simple outil de vote, c’est un véritable espace d’échange entre passionnés, où chacun peut découvrir les œuvres en compétition,
                soutenir ses artistes favoris et contribuer directement à la construction du palmarès de l’édition 2026.
            </p>
        </div>
    </div>
</section>

<section class="contexte-site">
    <div class="contexte-site-content fade-in-div">
        <h2>À propos de CineVote</h2>
        <p>
            Dans un monde où la diversité cinématographique ne cesse de croître, CineVote a pour ambition de <strong>démocratiser la sélection des films</strong> présentés
            dans les festivals. Chaque membre inscrit peut exprimer son opinion à travers un système de vote <strong>transparent, sécurisé et équitable</strong>,
            garantissant la représentativité des résultats.
        </p>
        <br>
        <p>
            La plateforme s’adresse aussi bien aux <strong>électeurs</strong> — membres du public désireux de voter — qu’aux <strong>candidats</strong>,
            qu’ils soient réalisateurs, acteurs ou producteurs souhaitant proposer leur œuvre.
            Chaque film est accompagné d’informations détaillées et de visuels.
        </p>
        <br>
        <p>
            L’objectif de CineVote est simple : <strong>placer la voix du public au cœur du festival</strong>.
            Grâce à une interface moderne, intuitive et accessible depuis n’importe quel appareil, le site permet de voter en temps réel,
            d’accéder aux classements et de découvrir les lauréats dès la clôture du scrutin.
        </p>
        <br>
        <p>
            En rejoignant CineVote 2026, vous devenez acteur d’une expérience participative unique, où chaque vote compte et chaque opinion façonne le cinéma de demain.
            Ensemble, faisons du festival un espace plus ouvert, collaboratif et passionné. 🎥
        </p>
    </div>

</section>

<section class="action-vote">
    <div class="fade-in-div">
        <h1>Votre avis compte:</h1>
        <h1>Votez pour le</h1>
        <h1>meilleur du </h1>
        <h1>cinéma !</h1>
    </div>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'electeur'): ?>
    <button class="btn1-vote"><a href="#">Commencer à voter</a></button>
    <?php else: ?>
    <button class="btn1-vote"><a href="/sae-3-festivote-tas-cesar/app/views/auth/login.php">Connectez-vous pour voter !</a></button>
    <?php endif; ?>
</section>

<section class="pourquoi-cinevote">
    <div class="pourquoi fade-in-div">
        <h1>Pourquoi CineVote est le roi du vote ?</h1>
        <h2>Nous sommes le site ultime pour les cinéphile qui veulent dire leur dernier mot</h2>
    </div>
    <div class="explication fade-in-div">
        <div class="cards-pourquoi">
            <i class='bx bx-up-arrow-alt'></i>
            <p>
                Des catégories variées pour tous les goûts, de l'horreur qui fait froid dans le dos à la comédie qui vous tord de rire.</p>
        </div>

        <div class="cards-pourquoi">
            <i class='bx bx-up-arrow-alt'></i>
            <p>Des acteurs et réalisateurs légendaires aux nouvelles étoiles montantes, donnez-leur la reconnaissance qu'ils méritent.
            </p>
        </div>

        <div class="cards-pourquoi"><i class='bx bx-up-arrow-alt'></i>
            <p>
                Une interface simple et intuitive pour que votre expérience de vote soit aussi fluide qu'un bon scénario.
            </p>
        </div>
        <div class="cards-pourquoi"><i class='bx bx-up-arrow-alt'></i>
        <p>
            Partagez vos opinions avec une communauté passionnée de vrais connaisseurs dans l'espace commentaire de chaque oeuvre
        </p>
        </div>
        <div class="cards-pourquoi"><i class='bx bx-up-arrow-alt'></i>
            <p>
                Soyez le critique que vous avez toujours voulu être, sans avoir à supporter les critiques des autres.
            </p>
        </div>
    </div>
</section>

<section class="section-box">
    <div class="box fade-in-div">
        <div class="box-content">
            <div class="box-text">
                <h1>Prêt à faire entendre votre voix cinématographique ?</h1>
            </div>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'electeur'): ?>
            <div class="btn-box">
                <button class="btn2-vote"><a href="#">Votez ici</a></button>
            </div>
            <?php else: ?>
            <div class="btn-box">
                <button class="btn2-vote"><a href="/sae-3-festivote-tas-cesar/app/views/auth/login.php">Votez-ici !</a></button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="categorie-section">
    <div class="categorie">
        <div class="categorie-content fade-in-div">
            <div class="categorie-text">
                <h1>Catégories</h1>
            </div>

            <div class="categorie-box">
                <div class="categorie-box-content">
                    <div class="categorie-box-content-content">
                        <div class="circle-icon">
                            <span class="number">1</span>
                        </div>
                        <h2>Action</h2>
                        <p>Des explosions aux poursuites effrénées, votez pour les scènes qui vous ont tenu en haleine.</p>
                    </div>
                    <div class="categorie-box-content-content">
                        <div class="circle-icon">
                            <span class="number">2</span>
                        </div>
                        <h2>Comédie</h2>
                        <p>Les rires sont garantis ! Qui vous a fait pleurer de rire cette année ?</p>
                    </div>
                    <div class="categorie-box-content-content">
                        <div class="circle-icon">
                            <span class="number">3</span>
                        </div>
                        <h2>Horreur</h2>
                        <p>Pour les amateurs de frissons, votez pour le film qui vous a hanté le plus</p>
                    </div>
                </div>
                <p class="categorie-intro">Action, comédie, horreur… ce n’est qu’un aperçu. D’autres genres vous attendent !</p>
            </div>
        </div>
    </div>
</section>

<section class="affiche-section">

    <div class="titre-film">
        <h1>Voici quelques films d'actualité :</h1>
    </div>
    <div class="affiche-container">
        <div class="affiche-row row-top fade-in-div">
            <img src="<?= ImageHelper::url('img/affiche-film/certain l\'aime chauve.jpg') ?>" alt="Film 1">
            <img src="<?= ImageHelper::url('img/affiche-film/boomerang.webp') ?>" alt="Film 2">
            <img src="<?= ImageHelper::url('img/affiche-film/c\'était mieux demain.jpg') ?>" alt="Film 3">

            <img src="<?= ImageHelper::url('img/affiche-film/kaamelott.jpg') ?>" alt="Film 4">
            <img src="<?= ImageHelper::url('img/affiche-film/la vie pour de vrai.webp') ?>" alt="Film 5">
            <img src="<?= ImageHelper::url('img/affiche-film/une bataille après l\'autre.jpg') ?>" alt="Film 6">

            <img src="<?= ImageHelper::url('img/affiche-film/certain l\'aime chauve.jpg') ?>" alt="Film 1">
            <img src="<?= ImageHelper::url('img/affiche-film/boomerang.webp') ?>" alt="Film 2">
            <img src="<?= ImageHelper::url('img/affiche-film/c\'était mieux demain.jpg') ?>" alt="Film 3">

            <img src="<?= ImageHelper::url('img/affiche-film/kaamelott.jpg') ?>" alt="Film 4">
            <img src="<?= ImageHelper::url('img/affiche-film/la vie pour de vrai.webp') ?>" alt="Film 5">
            <img src="<?= ImageHelper::url('img/affiche-film/une bataille après l\'autre.jpg') ?>" alt="Film 6">

            <img src="<?= ImageHelper::url('img/affiche-film/certain l\'aime chauve.jpg') ?>" alt="Film 1">
            <img src="<?= ImageHelper::url('img/affiche-film/boomerang.webp') ?>" alt="Film 2">
            <img src="<?= ImageHelper::url('img/affiche-film/c\'était mieux demain.jpg') ?>" alt="Film 3">

        </div>
        <div class="affiche-row row-bottom fade-in-div">
            <img src="<?= ImageHelper::url('img/affiche-film/dora.jpg') ?>" alt="Film 7">
            <img src="<?= ImageHelper::url('img/affiche-film/gardien de la galaxie.jpg') ?>" alt="Film 8">
            <img src="<?= ImageHelper::url('img/affiche-film/l\'histoire de souleymane.jpg') ?>" alt="Film 9">

            <img src="<?= ImageHelper::url('img/affiche-film/star wars.webp') ?>" alt="Film 10">
            <img src="<?= ImageHelper::url('img/affiche-film/une bataille après l\'autre.jpg') ?>" alt="Film 11">
            <img src="<?= ImageHelper::url('img/affiche-film/Asterix & Obelix.jpg') ?>" alt="Film 12">

            <img src="<?= ImageHelper::url('img/affiche-film/dora.jpg') ?>" alt="Film 7">
            <img src="<?= ImageHelper::url('img/affiche-film/gardien de la galaxie.jpg') ?>" alt="Film 8">
            <img src="<?= ImageHelper::url('img/affiche-film/l\'histoire de souleymane.jpg') ?>" alt="Film 9">

            <img src="<?= ImageHelper::url('img/affiche-film/star wars.webp') ?>" alt="Film 10">
            <img src="<?= ImageHelper::url('img/affiche-film/une bataille après l\'autre.jpg') ?>" alt="Film 11">
            <img src="<?= ImageHelper::url('img/affiche-film/Asterix & Obelix.jpg') ?>" alt="Film 12">

            <img src="<?= ImageHelper::url('img/affiche-film/dora.jpg') ?>" alt="Film 7">
            <img src="<?= ImageHelper::url('img/affiche-film/gardien de la galaxie.jpg') ?>" alt="Film 8">
            <img src="<?= ImageHelper::url('img/affiche-film/l\'histoire de souleymane.jpg') ?>" alt="Film 9">
        </div>
    </div>
</section>

 <div class="btn-box2">
        <a href="../app/views/pages/films.php" class="btn3-vote">Voir plus !</a>
</div>

<section class="faq-section">
    <div class="faq-title fade-in-div">
        <h1>Questions Fréquentes</h1>
    </div>

    <div class="faq-list">

        <div class="faq-item">
            <button class="faq-question">Comment puis-je voter ? <span class="plus">+</span></button>
            <div class="faq-answer">
                Pour voter, vous devez être connecté en tant qu’électeur.
                Une fois connecté, accédez à la section <strong>“Categorie”</strong> pour découvrir les films, acteurs et réalisateurs en compétition.
                Sélectionnez vos favoris dans chaque catégorie, puis validez vos choix en votant pendant la phase de vote.
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">Puis-je changer mon vote ? <span class="plus">+</span></button>
            <div class="faq-answer">
                Non. Dès que vous validez et soumettez votre bulletin, le vote est enregistré de façon définitive et anonymisée : il n’est plus possible de le modifier.
                Assurez-vous donc de vérifier attentivement votre choix avant la validation.
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">Quelles sont les catégories disponibles ? <span class="plus">+</span></button>
            <div class="faq-answer">
                Les catégories principales sont : <strong>Films</strong>, <strong>Acteurs</strong> et <strong>Réalisateurs</strong>.
                Chaque catégorie regroupe plusieurs œuvres ou artistes proposés par les candidats.
                Vous pouvez consulter la liste complète des catégories dans la page dédiée.
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">Comment devenir candidat ? <span class="plus">+</span></button>
            <div class="faq-answer">
                Pour devenir candidat, vous devez créer un compte et vous inscrire en tant que <strong>candidat</strong>.
                Une fois connecté, vous pourrez soumettre vos œuvres (films, courts métrages, ou prestations d’acteurs/réalisateurs)
                via la page dédié  <strong>“Film/Acteur/Réalisateur”</strong> avant la date limite fixée par les organisateurs.
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">Est-ce gratuit ? <span class="plus">+</span></button>
            <div class="faq-answer">
                Oui, la participation à CineVote est entièrement gratuite, que ce soit pour voter ou pour soumettre une candidature.
                Aucun paiement ni abonnement n’est requis pour utiliser la plateforme. De plus aucune information n'est exploité à des fins commerciales !
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">Qui peut voter ? <span class="plus">+</span></button>
            <div class="faq-answer">
                Seules les personnes inscrites et connectées en tant qu’<strong>électeur</strong> peuvent voter.
                Chaque compte est unique et permet un vote par catégorie afin d’assurer un système équitable et transparent.
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">Quand se terminent les votes ? <span class="plus">+</span></button>
            <div class="faq-answer">
                La période de vote s’étend sur un mois.
                Pour l’édition <strong>CineVote 2026</strong>, la clôture des votes est prévue pour le <strong>31 janvier 2026 à minuit</strong>.
                Les résultats officiels seront publiés automatiquement et accèssible dès la cloture des votes sur la plateforme.
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">Qui gère la plateforme ? <span class="plus">+</span></button>
            <div class="faq-answer">
                CineVote est administré par une équipe dédiée d’organisateurs qui veillent à la sécurité des données,
                à la validation des candidatures et au bon déroulement des votes.
                Les administrateurs peuvent également modérer les contenus proposés par les candidats.
            </div>
        </div>
    </div>
</section>

<script>
    const faders = document.querySelectorAll('.fade-in-div');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('appear');
            }
        });
    }, {
        threshold: 0.1
    });

    faders.forEach(el => observer.observe(el));

    //-------------------------------------------------------------------------------------------------------//

    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;
        document.querySelector('.row-top').style.transform = `translateX(${scrollY * 0.3}px)`;
        document.querySelector('.row-bottom').style.transform = `translateX(-${scrollY * 0.3}px)`;
    });

    //-------------------------------------------------------------------------------------------------------//

    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        question.addEventListener('click', () => {
            item.classList.toggle('active');
        });
    });
</script>

<?php require '../app/views/layouts/footer.php'; ?>



