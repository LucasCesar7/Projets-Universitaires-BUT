<?php
require_once __DIR__ . '/../../controllers/CommentaireController.php';
require_once __DIR__ . '/../../helpers/CsrfHelper.php';

$commentaireController = new CommentaireController();
$commentaires = $commentaireController->getCommentaires($cibleId, $cibleType);
$nbCommentaires = count($commentaires);

// Récupérer les messages
$success = $_SESSION['success'] ?? null;
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['success'], $_SESSION['errors']);
?>


<div class="commentaires-section" id="commentaires">
    <h2>Commentaires (<?= $nbCommentaires ?>)</h2>
    
    <!-- Messages de succès/erreur -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
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
    
    <!-- Formulaire d'ajout de commentaire -->
    <?php if (isset($_SESSION['user_id']) && isset($_SESSION['pseudo'])): ?>
        <div class="commentaire-form-container">
            <h3>Laisser un commentaire</h3>
            <form action="/sae-3-festivote-tas-cesar/app/controllers/CommentaireController.php" method="POST" class="commentaire-form">
                <?= CsrfHelper::generateField() ?>
                <input type="hidden" name="action" value="add_commentaire">
                <input type="hidden" name="cible_id" value="<?= htmlspecialchars($cibleId) ?>">
                <input type="hidden" name="cible_type" value="<?= htmlspecialchars($cibleType) ?>">
                
                <div class="form-group">
                    <label for="contenu">Votre commentaire</label>
                    <textarea 
                        id="contenu" 
                        name="contenu" 
                        rows="4" 
                        placeholder="Partagez votre avis sur <?= htmlspecialchars($cibleNom) ?>..."
                        maxlength="1000"
                        required
                    ></textarea>
                    <small class="char-counter">0 / 1000 caractères</small>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    Publier le commentaire
                </button>
            </form>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <p>Vous devez être <a href="/sae-3-festivote-tas-cesar/app/views/auth/login.php">connecté</a> pour laisser un commentaire.</p>
        </div>
    <?php endif; ?>
    
    <!-- Liste des commentaires -->
    <div class="commentaires-liste">
        <?php if (empty($commentaires)): ?>
            <p class="no-comments">Aucun commentaire pour le moment. Soyez le premier à donner votre avis !</p>
        <?php else: ?>
            <?php foreach ($commentaires as $commentaire): ?>
                <div class="commentaire-item">
                    <div class="commentaire-header">
                        <span class="commentaire-auteur"><img src="<?= ImageHelper::url('img/logo/logo-profile.png') ?>" alt="logo-profile" width="30" class="img-container"> <?= htmlspecialchars($commentaire['pseudo']) ?></span>
                        <span class="commentaire-date">
                            🕒 <?= date('d/m/Y à H:i', strtotime($commentaire['date_publication'])) ?>
                        </span>
                    </div>
                    <div class="commentaire-contenu">
                        <?= nl2br(htmlspecialchars($commentaire['contenu'])) ?>
                    </div>
                    
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <div class="commentaire-admin">
                            <form action="/sae-3-festivote-tas-cesar/app/controllers/CommentaireController.php" method="POST" class="form-moderation" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">
                                <?= CsrfHelper::generateField() ?>
                                <input type="hidden" name="action" value="delete_commentaire">
                                <input type="hidden" name="commentaire_id" value="<?= $commentaire['id_commentaire'] ?>">
                                <input type="hidden" name="raison" value="Supprimé par l'administrateur">
                                <button type="submit" class="btn-moderation">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Script pour le compteur de caractères -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('contenu');
    const counter = document.querySelector('.char-counter');
    
    if (textarea && counter) {
        textarea.addEventListener('input', function() {
            const length = this.value.length;
            counter.textContent = `${length} / 1000 caractères`;
            
            if (length > 800) {
                counter.style.color = 'var(--danger-color)';
            } else {
                counter.style.color = 'var(--grey-color)';
            }
        });
    }
});
</script>
