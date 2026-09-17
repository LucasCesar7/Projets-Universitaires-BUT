<?php
session_start();

$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootPath . '/app/config/config.php';
require_once __DIR__ . '/../../helpers/CsrfHelper.php';

$csrfToken = CsrfHelper::generateToken();

if (!isset($_GET['id'])) {
    echo "<!DOCTYPE html><html><body>Réalisateur non trouvé</body></html>";
    exit;
}

$realisateur_id = intval($_GET['id']);

try {
    $pdo = dbconnect();
    
    // Récupérer le réalisateur avec catégories
    $stmt = $pdo->prepare("
        SELECT r.*, 
               GROUP_CONCAT(DISTINCT c.libelle SEPARATOR ', ') as categories
        FROM realisateur r
        LEFT JOIN film_realisateur fr ON r.id_realisateur = fr.realisateur_id_realisateur
        LEFT JOIN film f ON fr.film_id_film = f.id_Film
        LEFT JOIN film_categorie fc ON f.id_Film = fc.film_id_film
        LEFT JOIN categorie c ON fc.categorie_id_categorie = c.id_Categorie
        WHERE r.id_realisateur = ?
        GROUP BY r.id_realisateur
    ");
    $stmt->execute([$realisateur_id]);
    $realisateur = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$realisateur) {
        echo "<!DOCTYPE html><html><body>Réalisateur non trouvé</body></html>";
        exit;
    }
    
    // Films
    $stmt = $pdo->prepare("
        SELECT DISTINCT f.id_Film, f.titre, f.url_affiche, f.annee, f.statut_validation
        FROM film f
        JOIN film_realisateur fr ON f.id_Film = fr.film_id_film
        WHERE fr.realisateur_id_realisateur = ?
        ORDER BY f.annee DESC
    ");
    $stmt->execute([$realisateur_id]);
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
        <a href="/sae-3-festivote-tas-cesar/app/views/pages/realisateurs.php" class="back-link">← Retour aux réalisateurs</a>
    </div>
    
    <div class="detail-content">
        <div class="detail-poster">
            <?php if (!empty($realisateur['url_photo'])): ?>
                <img src="<?= ImageHelper::url(htmlspecialchars($realisateur['url_photo'])) ?>" alt="<?= htmlspecialchars($realisateur['prenom'] . ' ' . $realisateur['nom']) ?>">
            <?php else: ?>
                <div class="no-poster">🎬</div>
            <?php endif; ?>
        </div>
        
        <div class="detail-info">
            <h1><?= htmlspecialchars($realisateur['prenom'] . ' ' . $realisateur['nom']) ?></h1>
            
            <!-- BOUTON DE VOTE -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'electeur'  && $isInPeriodeVote): ?>
                <button class="btn-vote" 
                        data-type="realisateur" 
                        data-id="<?= $realisateur['id_realisateur'] ?>" 
                        data-name="<?= htmlspecialchars($realisateur['prenom'] . ' ' . $realisateur['nom']) ?>">
                        <img src="<?= ImageHelper::url('img/logo/logo-ticket.png') ?>" alt="logo-film" width="40" class="img-container">
                        Voter pour ce réalisateur
                </button>
            <?php endif; ?>
            
            <div class="info-section">
                <p><strong>Nationalité :</strong> <?= htmlspecialchars($realisateur['nationalite'] ?? 'N/A') ?></p>
                
                <?php if (!empty($realisateur['date_naissance'])): ?>
                    <p><strong>Date de naissance :</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($realisateur['date_naissance']))) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($realisateur['categories'])): ?>
                    <p><strong>Genres :</strong> <?= htmlspecialchars($realisateur['categories']) ?></p>
                <?php endif; ?>
            </div>
            
            <!-- SECTION FILMS -->
            <?php if (!empty($films)): ?>
                <div class="films-section">
                    <h2><img src="<?= ImageHelper::url('img/logo/logo-bobine.png') ?>" alt="logo-film" width="60" class="img-container"> Films réalisés (<?= count(array_filter($films, fn($film) => $film['statut_validation'] === 'VALIDE' )) ?>)</h2>
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
                <p><em>Aucun film disponible pour ce réalisateur.</em></p>
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
$cibleId = $realisateur['id_realisateur'];
$cibleType = 'realisateur';
$cibleNom = $realisateur['prenom'] . ' ' . $realisateur['nom'];

include $rootPath . '/app/views/partials/commentaires.php';
?>

<script src="/sae-3-festivote-tas-cesar/public/js/vote.js"></script>

<?php include dirname(dirname(__FILE__)) . '/layouts/footer.php'; ?>
