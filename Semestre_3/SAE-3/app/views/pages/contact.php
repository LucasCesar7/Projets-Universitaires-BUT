<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../helpers/CsrfHelper.php';

$success = $_SESSION['success'] ?? null;
$errors = $_SESSION['errors'] ?? [];
$oldInput = $_SESSION['old_input'] ?? [];

unset($_SESSION['success'], $_SESSION['errors'], $_SESSION['old_input']);

include __DIR__ . '/../layouts/header.php';
?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/contact.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<section class="contact-section">
    <div class="contact-overlay"></div>
    <div class="contact-container fade-in-div">
        <h1 class="contact-title">Contactez l’équipe CineVote</h1>
        <p class="contact-description">
            Une question, une suggestion ou un problème ? Notre équipe technique et communication vous répond avec plaisir.
            Ensemble, faisons briller le 7<sup>ème</sup> art comme sur le grand écran !
        </p>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

  

        <div class="contact-content">
            <!-- FORMULAIRE -->
            <form action="/sae-3-festivote-tas-cesar/app/controllers/ContactController.php" method="POST" class="contact-form">
                <?= CsrfHelper::generateField() ?>
                <input type="hidden" name="action" value="send_contact">
                
                <label for="nom">Nom</label>
                <input 
                    type="text" 
                    id="nom" 
                    name="nom" 
                    placeholder="Entrez votre nom" 
                    value="<?= htmlspecialchars($oldInput['nom'] ?? '') ?>"
                    required
                >

                <label for="email">Adresse e-mail</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="exemple@gmail.com" 
                    value="<?= htmlspecialchars($oldInput['email'] ?? '') ?>"
                    required
                >

                <label for="sujet">Sujet</label>
                <input 
                    type="text" 
                    id="sujet" 
                    name="sujet" 
                    placeholder="Objet de votre message" 
                    value="<?= htmlspecialchars($oldInput['sujet'] ?? '') ?>"
                    required
                >

                <label for="message">Message</label>
                <textarea 
                    id="message" 
                    name="message" 
                    rows="5" 
                    placeholder="Rédigez votre message ici..." 
                    maxlength="2000"
                    required
                ><?= htmlspecialchars($oldInput['message'] ?? '') ?></textarea>
                <small class="char-counter">0 / 2000 caractères</small>

                <button type="submit" class="send-btn">Envoyer le message</button>
            </form>

            <!-- INFOS DE CONTACT -->
            <aside class="contact-aside">
                <h2>Nos coordonnées</h2>
                <p><strong>Adresse :</strong> 12 rue du Cinéma, 54000 Nancy, France</p><br>
                <p><i class="fa-solid fa-envelope"></i> <strong>Email : </strong><a href="mailto:cinevote@proton.me">cinevote@proton.me</a></p><br>
                <p><i class="fa-solid fa-phone"></i> <strong>Téléphone : </strong><a href="tel:0383456789"> +33 3 83 45 67 89</a></p>

                <div class="social-links">
                    <a href="#"><img src="<?= ImageHelper::url('img/icons/Facebook_logo.png') ?>" alt="Facebook"></a>
                    <a href="#"><img src="<?= ImageHelper::url('img/icons/Logo_Twitter.png') ?>" alt="Twitter"></a>
                    <a href="#"><img src="<?= ImageHelper::url('img/icons/Instagram_icon.png') ?>" alt="Instagram"></a>
                </div>
            </aside>
        </div>
    </div>
</section>
<script src="/sae-3-festivote-tas-cesar/public/js/formulaire-contact.js"></script>
<!-- Script pour le compteur de caractères -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('message');
    const counter = document.querySelector('.char-counter');
    
    function updateCounter() {
        const length = textarea.value.length;
        counter.textContent = `${length} / 2000 caractères`;
        
        if (length > 1800) {
            counter.style.color = 'var(--error-color)';
        } else {
            counter.style.color = 'var(--grey-color)';
        }
    }
    updateCounter();
    
    textarea.addEventListener('input', updateCounter);
});
</script>



<?php include __DIR__ . '/../layouts/footer.php'; ?>