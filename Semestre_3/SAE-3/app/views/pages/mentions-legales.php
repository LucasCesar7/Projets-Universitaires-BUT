<?php
$pageTitle = "Mentions Légales - CineVote";
require_once __DIR__ . '/../layouts/header.php';
?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/legal-pages.css">

<div class="legal-container">
    <h1>Mentions Légales</h1>
    
    <section class="legal-section">
        <h2>1. Éditeur du site</h2>
        <p>
            Le site CineVote est édité dans le cadre d'un projet académique par :<br>
            <strong>TAS Tom & CESAR Lucas</strong><br>
            Directeur de la publication : TAS Tom<br>
            Étudiant en BUT Informatique - 2ème année<br>
            IUT de Saint-Dié-des-Vosges<br>
            11 Rue de l'Université, 88100 Saint-Dié-des-Vosges<br>
            Email : <a href="mailto:cinevote@proton.me">cinevote@proton.me</a>
        </p>
    </section>

    <section class="legal-section">
        <h2>2. Hébergement</h2>
        <p>
            Le site est hébergé par :<br>
            <strong>InfinityFree</strong><br>
            Kwikstaartlaan 42, Unit G1517, 3704 GS Zeist, Pays‑Bas<br>
            Email : hello@infinityfree.com<br>
            Site web : <a href="https://www.infinityfree.com" target="_blank">infinityfree.com</a>
        </p>
    </section>

    <section class="legal-section">
        <h2>3. Propriété intellectuelle</h2>
        <p>
            L'ensemble du contenu de ce site (textes, images, graphismes, logo, icônes, sons, logiciels) 
            est la propriété exclusive de CineVote ou de ses partenaires, sauf mention contraire.
        </p>
        <p>
            Toute reproduction, représentation, modification, publication, adaptation de tout ou partie 
            des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite, 
            sauf autorisation écrite préalable.
        </p>

        <p>
            Toute exploitation non autorisée du site ou de l'un quelconque des éléments qu'il contient 
            sera considérée comme constitutive d'une contrefaçon et poursuivie conformément aux 
            dispositions des articles L.335-2 et suivants du Code de Propriété Intellectuelle.
        </p>
    </section>

    <section class="legal-section">
        <h2>4. Données personnelles</h2>
        <p>
            Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique et Libertés, 
            vous disposez d'un droit d'accès, de rectification, de suppression et d'opposition aux données personnelles vous concernant.
        </p>
        <p>
            Pour exercer ces droits, contactez-nous à : 
            <a href="mailto:cinevote@proton.me">cinevote@proton.me</a>
        </p>
        <p>
            Pour plus d'informations, consultez notre 
            <a href="confidentialite.php">Politique de Confidentialité</a>.
        </p>
    </section>

    <section class="legal-section">
        <h2>5. Cookies</h2>
        <p>
            Ce site utilise des cookies de session nécessaires au bon fonctionnement de l'application 
            (authentification, gestion de session). Ces cookies sont indispensables et ne peuvent être désactivés.
        </p>
        <p>
            Aucun cookie publicitaire ou de traçage tiers n'est utilisé sur ce site.
        </p>
    </section>

    <section class="legal-section">
        <h2>6. Responsabilité</h2>
        <p>
            CineVote s'efforce d'assurer l'exactitude et la mise à jour des informations diffusées sur ce site, 
            dont elle se réserve le droit de corriger, à tout moment et sans préavis, le contenu.
        </p>
        <p>
            CineVote ne pourra être tenue responsable des dommages directs et indirects causés au matériel de l'utilisateur, 
            lors de l'accès au site ou de l'apparition d'un bug ou d'une incompatibilité.
        </p>
    </section>

    
    <section class="legal-section">
        <h2>7. Droit applicable</h2>
        <p>
            Les présentes mentions légales sont régies par le droit français. 
            Tout litige relatif à l'utilisation du site sera soumis aux tribunaux compétents.
        </p>
    </section>

    <section class="legal-section">
        <h2>8. Contact</h2>
        <p>
            Pour toute question ou réclamation, vous pouvez nous contacter :
        </p>
        <ul>
            <li>Par email : <a href="mailto:cinevote@proton.me">cinevote@proton.me</a></li>
            <li>Via le <a href="contact.php">formulaire de contact</a></li>
        </ul>
    </section>

    <p class="legal-footer">
        <small>Dernière mise à jour : <?= date('d/m/Y') ?></small>
    </p>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
