<?php
$pageTitle = "Politique de Confidentialité - CineVote";
require_once __DIR__ . '/../layouts/header.php';
?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/legal-pages.css">

<div class="legal-container">
    <h1>Politique de Confidentialité</h1>
    
    <p>
        Chez CineVote, nous prenons très au sérieux la protection de vos données personnelles. 
        Cette politique de confidentialité explique comment nous collectons, utilisons et protégeons vos informations.
    </p>

    <section class="legal-section">
        <h2>1. Responsable du traitement des données</h2>
        <p>
            Les responsable du traitement des données sont :<br>
            <strong>TAS Tom & CESAR Lucas</strong><br>
            Email : <a href="mailto:cinevote@proton.me">cinevote@proton.me</a>
        </p>
    </section>

    <section class="legal-section">
        <h2>2. Données collectées</h2>
        <p>Nous collectons les données personnelles suivantes :</p>
        
        <h3>Pour les électeurs :</h3>
        <ul>
            <li>Nom et prénom</li>
            <li>Adresse email</li>
            <li>Pseudo</li>
            <li>Date de naissance</li>
            <li>Mot de passe (hashé avec bcrypt)</li>
        </ul>

        <h3>Pour les candidats :</h3>
        <ul>
            <li>Nom et prénom</li>
            <li>Adresse email</li>
            <li>Pseudo</li>
            <li>Mot de passe (hashé avec bcrypt)</li>
        </ul>

        <h3>Données de navigation :</h3>
        <ul>
            <li>Adresse IP (hashée pour les votes)</li>
            <li>Date et heure de connexion</li>
            <li>Cookies de session</li>
        </ul>
    </section>

    <section class="legal-section">
        <h2>3. Finalité du traitement</h2>
        <p>Vos données sont collectées pour les finalités suivantes :</p>
        <ul>
            <li><strong>Gestion des comptes utilisateurs</strong> : création, authentification, gestion de profil</li>
            <li><strong>Système de vote</strong> : enregistrement anonyme des votes</li>
            <li><strong>Commentaires</strong> : publication et modération des commentaires</li>
            <li><strong>Vérification d'email</strong> : validation de l'adresse email lors de l'inscription</li>
            <li><strong>Sécurité</strong> : prévention des abus, traçabilité des actions administratives</li>
        </ul>
    </section>

    <section class="legal-section">
        <h2>4. Système de vote anonyme</h2>
        <p>
            <strong>Important :</strong> Les votes sont totalement anonymes grâce à un système de cryptographie.
        </p>
        <ul>
            <li>Votre email est transformé en une <strong>empreinte cryptographique</strong> (hash SHA-256)</li>
            <li>Cette empreinte ne permet <strong>jamais</strong> de remonter à votre identité</li>
            <li>Même les administrateurs ne peuvent pas savoir qui a voté pour qui</li>
            <li>L'empreinte garantit qu'un électeur ne peut voter qu'une fois par catégorie</li>
        </ul>
        <p>
            🔒 <strong>Garantie d'anonymat :</strong> Il est techniquement impossible de lier un vote à un électeur.
        </p>
    </section>

    <section class="legal-section">
        <h2>5. Base légale du traitement</h2>
        <p>Le traitement de vos données repose sur :</p>
        <ul>
            <li><strong>Votre consentement</strong> lors de la création de votre compte</li>
            <li><strong>L'exécution d'un contrat</strong> (utilisation de la plateforme)</li>
            <li><strong>L'intérêt légitime</strong> (sécurité, prévention des fraudes)</li>
        </ul>
    </section>

    <section class="legal-section">
        <h2>6. Durée de conservation</h2>
        <table class="sanctions-table">
            <thead>
                <tr>
                    <th>Type de données</th>
                    <th>Durée de conservation</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Comptes utilisateurs</td>
                    <td>Jusqu'à suppression du compte</td>
                </tr>
                <tr>
                    <td>Votes</td>
                    <td>Durée du festival + 1 an</td>
                </tr>
                <tr>
                    <td>Commentaires</td>
                    <td>Jusqu'à suppression par l'utilisateur ou l'admin</td>
                </tr>
                <tr>
                    <td>Logs de sécurité</td>
                    <td>6 mois maximum</td>
                </tr>
            </tbody>
        </table>
    </section>

    <section class="legal-section">
        <h2>7. Destinataires des données</h2>
        <p>Vos données personnelles sont accessibles uniquement par :</p>
        <ul>
            <li>Les <strong>administrateurs</strong> de la plateforme (pour la gestion des comptes)</li>
            <li>Le <strong>personnel technique</strong> (maintenance et sécurité)</li>
        </ul>
        <p>
            Vos données ne sont <strong>jamais</strong> vendues, louées ou partagées avec des tiers à des fins commerciales.
        </p>
    </section>

    <section class="legal-section">
        <h2>8. Vos droits (RGPD)</h2>
        <p>Conformément au RGPD, vous disposez des droits suivants :</p>
        <ul>
            <li><strong>Droit d'accès :</strong> obtenir une copie de vos données personnelles</li>
            <li><strong>Droit de rectification :</strong> corriger vos données inexactes</li>
            <li><strong>Droit à l'effacement :</strong> supprimer vos données ("droit à l'oubli")</li>
            <li><strong>Droit à la limitation :</strong> limiter le traitement de vos données</li>
            <li><strong>Droit d'opposition :</strong> vous opposer au traitement de vos données</li>
            <li><strong>Droit à la portabilité :</strong> récupérer vos données dans un format structuré</li>
        </ul>
        <p>
            Pour exercer ces droits, contactez-nous à : 
            <a href="mailto:cinevote@proton.me">cinevote@proton.me</a>
        </p>
    </section>

    <section class="legal-section">
        <h2>9. Sécurité des données</h2>
        <p>Nous mettons en œuvre les mesures de sécurité suivantes :</p>
        <ul>
            <li>Chiffrement des mots de passe avec <strong>bcrypt</strong></li>
            <li>Connexions sécurisées (HTTPS)</li>
            <li>Hashage des empreintes de vote et des adresses IP</li>
            <li>Protection contre les injections SQL</li>
            <li>Validation et échappement des données utilisateur</li>
            <li>Système de tokens CSRF pour les formulaires</li>
        </ul>
    </section>

    <section class="legal-section">
        <h2>10. Cookies</h2>
        <p>Ce site utilise uniquement des <strong>cookies de session</strong> nécessaires au fonctionnement :</p>
        <ul>
            <li><strong>PHPSESSID :</strong> cookie de session PHP (obligatoire pour l'authentification)</li>
        </ul>
        <p>
            Ces cookies sont <strong>strictement nécessaires</strong> et ne peuvent être désactivés sans compromettre 
            le fonctionnement du site.
        </p>
        <p>Nous n'utilisons <strong>aucun cookie publicitaire ou de traçage</strong>.</p>
    </section>

    <section class="legal-section">
        <h2>11. Modifications de cette politique</h2>
        <p>
            Nous nous réservons le droit de modifier cette politique de confidentialité à tout moment. 
            Les modifications seront publiées sur cette page avec une date de mise à jour.
        </p>
    </section>

    <section class="legal-section">
        <h2>12. Contact</h2>
        <p>
            Pour toute question concernant cette politique de confidentialité ou vos données personnelles, 
            vous pouvez nous contacter :
        </p>
        <ul>
            <li>Email : <a href="mailto:cinevote@proton.me">cinevote@proton.me</a></li>
            <li><a href="contact.php">Formulaire de contact</a></li>
        </ul>
    </section>

    <p class="legal-footer">
        <small>Dernière mise à jour : <?= date('d/m/Y') ?></small>
    </p>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
