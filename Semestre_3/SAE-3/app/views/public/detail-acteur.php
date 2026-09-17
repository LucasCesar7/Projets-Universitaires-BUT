<?php
session_start();

$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootPath. '/app/config/config.php';
require_once $rootPath. '/app/helpers/CsrfHelper.php';
require_once $rootPath. '/app/helpers/ImageHelper.php';

$csrfToken = CsrfHelper::generateToken();

if (!isset($_GET['id'])) {
    echo "<!DOCTYPE html><html><body>Acteur non trouvé</body></html>";
    exit;
}

$acteur_id = intval($_GET['id']);

try {
    $pdo = dbconnect();
    
    // Récupérer l'acteur avec catégories
    $stmt = $pdo->prepare("
        SELECT a.*, 
               GROUP_CONCAT(DISTINCT c.libelle SEPARATOR ', ') as categories
        FROM acteur a
        LEFT JOIN acteur_film af ON a.id_acteur = af.acteur_id_acteur
        LEFT JOIN film f ON af.film_id_film = f.id_Film
        LEFT JOIN film_categorie fc ON f.id_Film = fc.film_id_film
        LEFT JOIN categorie c ON fc.categorie_id_categorie = c.id_categorie
        WHERE a.id_acteur = ?
        GROUP BY a.id_acteur
    ");
    $stmt->execute([$acteur_id]);
    $acteur = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$acteur) {
        echo "<!DOCTYPE html><html><body>Acteur non trouvé</body></html>";
        exit;
    }
    
    // Films
    $stmt = $pdo->prepare("
        SELECT DISTINCT f.id_Film, f.titre, f.url_affiche, f.annee, f.statut_validation
        FROM film f
        JOIN acteur_film af ON f.id_Film = af.film_id_film
        WHERE af.acteur_id_acteur = ?
        ORDER BY f.annee DESC
    ");
    $stmt->execute([$acteur_id]);
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "<!DOCTYPE html><html><body>Erreur: " . htmlspecialchars($e->getMessage()) . "</body></html>";
    exit;
}

include dirname(dirname(__FILE__)) . '/layouts/header.php';
?>
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/detail-film.css">
<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/vote-modal.css">

<div class="detail-container">
    <div class="detail-header">
        <a href="/sae-3-festivote-tas-cesar/app/views/pages/acteurs.php" class="back-link">← Retour aux acteurs</a>
    </div>
    
    <div class="detail-content">
        <div class="detail-poster">
            <?php if (!empty($acteur['url_photo'])): ?>
                <img src="<?= ImageHelper::url($acteur['url_photo']) ?>" alt="<?= htmlspecialchars($acteur['prenom'] . ' ' . $acteur['nom']) ?>">
            <?php else: ?>
                <div class="no-poster">👤</div>
            <?php endif; ?>
        </div>
        
        <div class="detail-info">
            <h1><?= htmlspecialchars($acteur['prenom'] . ' ' . $acteur['nom']) ?></h1>
            
            <!-- BOUTON DE VOTE -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'electeur'  && $isInPeriodeVote): ?>
                <button class="btn-vote" 
                        data-type="acteur" 
                        data-id="<?= $acteur['id_acteur'] ?>" 
                        data-name="<?= htmlspecialchars($acteur['prenom'] . ' ' . $acteur['nom']) ?>">
                        <img src="<?= ImageHelper::url('img/logo/logo-ticket.png') ?>" alt="logo-vote" width="30" class="img-container">
                        Voter pour cet acteur
                </button>
            <?php endif; ?>
            
            <div class="info-section">
                <p><strong>Nationalité :</strong> <?= htmlspecialchars($acteur['nationalite'] ?? 'N/A') ?></p>
                
                <?php if (!empty($acteur['date_naissance'])): ?>
                    <p><strong>Date de naissance :</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($acteur['date_naissance']))) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($acteur['categories'])): ?>
                    <p><strong>Genres :</strong> <?= htmlspecialchars($acteur['categories']) ?></p>
                <?php endif; ?>
            </div>
            
            <!-- SECTION FILMS -->
            <?php if (!empty($films)): ?>
                <div class="films-section">
                    <h2><img src="<?= ImageHelper::url('img/logo/logo-bobine.png') ?>" alt="logo-film" width="60" class="img-container"> Films (<?= count(array_filter($films, fn($film) => $film['statut_validation'] === 'VALIDE' )) ?>)</h2>
                    <div class="films-grid">
                        <?php foreach ($films as $film): ?>
                            <?php if($film['statut_validation'] !== 'VALIDE') continue; ?>
                            <div class="film-card-mini">
                                <a href="/sae-3-festivote-tas-cesar/app/views/public/detail-film.php?id=<?= $film['id_Film'] ?>">
                                    <?php if (!empty($film['url_affiche'])): ?>
                                        <img src="<?= ImageHelper::url($film['url_affiche']) ?>" alt="<?= htmlspecialchars($film['titre']) ?>">
                                    <?php else: ?>
                                        <div class="no-poster-mini">🎬</div>
                                    <?php endif; ?>
                                    <h4><?= htmlspecialchars($film['titre']) ?></h4>
                                    <?php if ($film['annee']): ?>
                                        <p class="film-year"><?= htmlspecialchars($film['annee']) ?></p>
                                    <?php endif; ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <p><em>Aucun film disponible pour cet acteur.</em></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL DE VOTE -->
<div id="modal-vote-confirm" class="modal" style="display: none;">
    <div class="modal-content">
        <span id="close-vote-modal" class="close">&times;</span>
        <h2>Confirmer votre vote</h2>
        
        <p><strong>Pour :</strong></p>
        <p id="modal-element-name" class="highlight"></p>
        
        <p><strong>Choisissez une catégorie :</strong></p>
        
        <form method="POST" action="/sae-3-festivote-tas-cesar/app/views/vote/voter.php">
            <?= CsrfHelper::generateField() ?>
            <input type="hidden" name="type" id="modal-type">
            <input type="hidden" name="id" id="modal-id">
            
            <select name="categorie" id="modal-categorie-select" required>
                <option value="">-- Sélectionnez une catégorie --</option>
            </select>
            
            <div class="modal-buttons">
                <button type="submit" name="confirm_vote" class="btn btn-primary">Confirmer</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-vote-confirm').style.display='none'">Annuler</button>
            </div>
        </form>
    </div>
</div>

<?php
$cibleId = $acteur['id_acteur'];
$cibleType = 'acteur';
$cibleNom = $acteur['prenom'] . ' ' . $acteur['nom'];

include $rootPath . '/app/views/partials/commentaires.php';
?>


<script src="/sae-3-festivote-tas-cesar/public/js/vote.js"></script>

<?php include dirname(dirname(__FILE__)) . '/layouts/footer.php'; ?>
