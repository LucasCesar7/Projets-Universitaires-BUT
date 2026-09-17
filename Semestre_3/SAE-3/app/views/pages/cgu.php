<?php
$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
$pageTitle = "Conditions Générales d'Utilisation - CineVote";
include $rootPath . '/app/views/layouts/header.php';
?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/legal-pages.css">

<div class="legal-container">
    <div class="legal-header">
        <h1>Conditions Générales d'Utilisation</h1>
        <p class="last-update">Dernière mise à jour : 14 décembre 2025</p>
    </div>

    <div class="legal-content">
        
        <section class="legal-section">
            <h2>1. Objet</h2>
            <p>
                Les présentes Conditions Générales d'Utilisation (CGU) régissent l'accès et l'utilisation 
                de la plateforme <strong>CineVote</strong>, un système de vote en ligne développé dans le cadre 
                d'un projet académique (BUT Informatique - IUT de Saint-Dié-des-Vosges).
            </p>
            <p>
                En accédant au site et en créant un compte, vous acceptez sans réserve les présentes CGU.
            </p>
        </section>

        <section class="legal-section">
            <h2>2. Accès au service</h2>
            
            <h3>2.1 Inscription</h3>
            <p>L'utilisation de CineVote nécessite la création d'un compte selon votre profil :</p>
            <ul>
                <li><strong>Électeur</strong> : Peut voter pour les films, acteurs et réalisateurs</li>
                <li><strong>Candidat</strong> : Peut proposer du contenu (films, acteurs, réalisateurs)</li>
            </ul>
            
            <h3>2.2 Informations requises</h3>
            <p>Vous vous engagez à fournir des informations exactes et à jour lors de votre inscription.</p>
            
            <h3>2.3 Sécurité du compte</h3>
            <p>
                Vous êtes responsable de la confidentialité de vos identifiants de connexion. 
                Toute activité réalisée via votre compte est présumée effectuée par vous.
            </p>
        </section>

        <section class="legal-section">
            <h2>3. Utilisation du service</h2>
            
            <h3>3.1 Règles de vote</h3>
            <ul>
                <li>Chaque électeur ne peut voter <strong>qu'une seule fois par catégorie</strong></li>
                <li>Les votes sont <strong>totalement anonymes</strong> et ne peuvent être modifiés</li>
                <li>Le vote n'est possible que pendant la <strong>période de vote définie</strong></li>
                <li>Toute tentative de vote multiple entraînera la <strong>suspension du compte</strong></li>
            </ul>
            
            <h3>3.2 Proposition de contenu (Candidats)</h3>
            <ul>
                <li>Les propositions doivent être <strong>en lien avec le cinéma</strong></li>
                <li>Les informations fournies doivent être <strong>véridiques et vérifiables</strong></li>
                <li>Les images doivent respecter les <strong>droits d'auteur</strong></li>
                <li>Les propositions sont soumises à <strong>validation par les administrateurs</strong></li>
            </ul>
            
            <h3>3.3 Commentaires</h3>
            <p>Vous pouvez laisser des commentaires sur les films, acteurs et réalisateurs. Vous vous engagez à :</p>
            <ul>
                <li><strong>Respecter</strong> les autres utilisateurs</li>
                <li>Ne pas publier de contenu <strong>offensant, discriminatoire ou illégal</strong></li>
                <li>Ne pas publier de <strong>spam ou publicité</strong></li>
                <li>Respecter les <strong>droits d'auteur</strong></li>
            </ul>
            <p class="warning-box">
                Les commentaires sont modérés automatiquement par et manuellement par les administrateurs. 
                Tout contenu inapproprié sera supprimé.
            </p>
        </section>

        <section class="legal-section">
            <h2>4. Comportements interdits</h2>
            <p>Il est strictement interdit de :</p>
            <ul>
                <li>Tenter de <strong>contourner le système de vote unique</strong></li>
                <li>Utiliser des <strong>bots, scripts ou automatisations</strong></li>
                <li>Tenter d'<strong>accéder aux comptes d'autres utilisateurs</strong></li>
                <li>Exploiter des <strong>failles de sécurité</strong> (merci de les signaler à cinevote@proton.me)</li>
                <li>Publier du contenu <strong>haineux, violent ou pornographique</strong></li>
                <li>Usurper l'identité d'une autre personne</li>
                <li>Spammer ou harceler d'autres utilisateurs</li>
            </ul>
        </section>

        <section class="legal-section">
            <h2>5. Modération et sanctions</h2>
            
            <h3>5.1 Modération</h3>
            <p>Les administrateurs se réservent le droit de :</p>
            <ul>
                <li><strong>Refuser ou supprimer</strong> toute proposition de contenu</li>
                <li><strong>Supprimer</strong> tout commentaire inapproprié</li>
                <li><strong>Suspendre ou supprimer</strong> tout compte ne respectant pas les CGU</li>
            </ul>
            
            <h3>5.2 Sanctions</h3>
            <table class="sanctions-table">
                <thead>
                    <tr>
                        <th>Infraction</th>
                        <th>Sanction</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Commentaire inapproprié</td>
                        <td>Suppression du commentaire</td>
                    </tr>
                    <tr>
                        <td>Tentative de vote multiple</td>
                        <td>Suspension temporaire du compte (7 jours)</td>
                    </tr>
                    <tr>
                        <td>Spam répété</td>
                        <td>Suspension temporaire (7-30 jours)</td>
                    </tr>
                    <tr>
                        <td>Contenu illégal / haineux</td>
                        <td>Suppression immédiate du compte</td>
                    </tr>
                    <tr>
                        <td>Tentative de piratage</td>
                        <td>Suppression + signalement aux autorités</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="legal-section">
            <h2>6. Propriété intellectuelle</h2>
            <p>
                Le site CineVote (design, code, logo) est la propriété exclusive de TAS Tom et CESAR Lucas.
            </p>
            <p>
                Les contenus proposés par les candidats (affiches, photos, descriptions) restent la propriété 
                de leurs auteurs respectifs. En les publiant, vous garantissez détenir les droits nécessaires 
                ou avoir obtenu les autorisations requises.
            </p>
        </section>

        <section class="legal-section">
            <h2>7. Protection des données personnelles</h2>
            <p>
                Vos données personnelles sont traitées conformément au RGPD. 
                Consultez notre <a href="/sae-3-festivote-tas-cesar/app/views/pages/confidentialite.php">Politique de Confidentialité</a> 
                pour plus d'informations.
            </p>
            <div class="info-box">
                <strong>Garantie d'anonymat :</strong> Vos votes sont totalement anonymes grâce à un système 
                de cryptographie. Il est techniquement impossible de lier un vote à un électeur.
            </div>
        </section>

        <section class="legal-section">
            <h2>8. Responsabilité</h2>
            
            <h3>8.1 Disponibilité du service</h3>
            <p>
                CineVote s'efforce d'assurer une disponibilité optimale du site, mais ne peut garantir 
                un accès ininterrompu. Le service peut être temporairement indisponible pour maintenance.
            </p>
            
            <h3>8.2 Limitation de responsabilité</h3>
            <p>
                CineVote ne pourra être tenu responsable des dommages directs ou indirects résultant 
                de l'utilisation du site, sauf en cas de faute lourde ou intentionnelle.
            </p>
            
            <h3>8.3 Contenu généré par les utilisateurs</h3>
            <p>
                CineVote n'est pas responsable du contenu publié par les utilisateurs (propositions, commentaires). 
                Cependant, nous mettons en place une modération active pour garantir la qualité du site.
            </p>
        </section>

        <section class="legal-section">
            <h2>9. Modification des CGU</h2>
            <p>
                CineVote se réserve le droit de modifier les présentes CGU à tout moment. 
                Les utilisateurs seront informés des modifications majeures par email ou notification sur le site.
            </p>
            <p>
                En continuant à utiliser le site après modification, vous acceptez les nouvelles CGU.
            </p>
        </section>

        <section class="legal-section">
            <h2>10. Résiliation</h2>
            
            <h3>10.1 Résiliation par l'utilisateur</h3>
            <p>
                Vous pouvez supprimer votre compte à tout moment depuis votre profil. 
                La suppression est définitive et irréversible.
            </p>
            
            <h3>10.2 Résiliation par CineVote</h3>
            <p>
                CineVote peut suspendre ou supprimer votre compte en cas de violation des présentes CGU, 
                sans préavis ni indemnité.
            </p>
        </section>

        <section class="legal-section">
            <h2>11. Droit applicable et juridiction</h2>
            <p>
                Les présentes CGU sont régies par le droit français. 
                Tout litige relatif à l'utilisation du site sera soumis aux tribunaux compétents de France.
            </p>
        </section>

        <section class="legal-section">
            <h2>12. Contact</h2>
            <p>Pour toute question concernant ces CGU, vous pouvez nous contacter :</p>
            <div class="contact-info">
                <p><strong>Email :</strong> <a href="mailto:cinevote@proton.me">cinevote@proton.me</a></p>
                <p><strong>Adresse :</strong> IUT de Saint-Dié-des-Vosges<br>
                11 Rue de l'Université, 88100 Saint-Dié-des-Vosges</p>
            </div>
        </section>

        <div class="cgu-acceptance">
            <p>
                En créant un compte sur CineVote, vous déclarez avoir lu, compris et accepté 
                les présentes Conditions Générales d'Utilisation.
            </p>
        </div>

    </div>
</div>

<?php include $rootPath . '/app/views/layouts/footer.php'; ?>
