<?php
session_start();

$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootPath . '/app/config/config.php';
require_once __DIR__ . '/../../helpers/CsrfHelper.php';

$csrfToken = CsrfHelper::generateToken();

if (!isset($_GET['id'])) {
    echo "<p>Film non trouvé</p>";
    exit;
}

$film_id = intval($_GET['id']);

try {
    $pdo = dbconnect();
    
    // Récupérer le film avec catégories
    $stmt = $pdo->prepare("
        SELECT f.*, GROUP_CONCAT(DISTINCT c.libelle SEPARATOR ', ') as categories
        FROM film f
        LEFT JOIN film_categorie fc ON f.id_film = fc.film_id_film
        LEFT JOIN categorie c ON fc.categorie_id_categorie = c.id_categorie
        WHERE f.id_film = ?
        GROUP BY f.id_film
    ");
    $stmt->execute([$film_id]);
    $film = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$film) {
        echo "<p>Film non trouvé</p>";
        exit;
    }
    
    // Acteurs
    $stmt = $pdo->prepare("
        SELECT DISTINCT a.id_acteur, a.prenom, a.nom, a.url_photo, a.statut_validation
        FROM acteur a
        JOIN acteur_film af ON a.id_acteur = af.acteur_id_acteur
        WHERE af.film_id_film = ?
    ");
    $stmt->execute([$film_id]);
    $acteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Réalisateurs
    $stmt = $pdo->prepare("
        SELECT DISTINCT r.id_realisateur, r.prenom, r.nom, r.url_photo, r.statut_validation
        FROM realisateur r
        JOIN film_realisateur fr ON r.id_realisateur = fr.realisateur_id_realisateur
        WHERE fr.film_id_film = ?
    ");
    $stmt->execute([$film_id]);
    $realisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "<p>Erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
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
        <a href="/sae-3-festivote-tas-cesar/app/views/pages/films.php" class="btn-back">← Retour aux films</a>
    </div>
    
    <div class="film-detail">
        <div class="film-poster-large">
            <?php if (!empty($film['url_affiche'])): ?>
                <img src="<?= ImageHelper::url($film['url_affiche']) ?>" alt="<?= htmlspecialchars($film['titre']) ?>">
            <?php else: ?>
                <div class="no-poster">🎬</div>
            <?php endif; ?>
        </div>
        
        <div class="film-details">
            <h1><?= htmlspecialchars($film['titre']) ?></h1>
            <p><strong>Année :</strong> <?= htmlspecialchars($film['annee'] ?? 'N/A') ?></p>
            <p><strong>Durée :</strong> <?= htmlspecialchars($film['duree'] ?? 'N/A') ?> min</p>
            <p><strong>Catégories :</strong> <?= htmlspecialchars($film['categories'] ?? 'N/A') ?></p>
            <p class="film-description"><?= nl2br(htmlspecialchars($film['description'] ?? '')) ?></p>
            
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'electeur' && $isInPeriodeVote): ?>
                <button class="btn-vote" 
                        data-id="<?= $film['id_film'] ?>" 
                        data-type="film" 
                        data-name="<?= htmlspecialchars($film['titre']) ?>"
                        data-categorie="<?= htmlspecialchars($film['categories'] ?? 'Non catégorisé') ?>">
                        <img src="<?= ImageHelper::url('img/logo/logo-ticket.png') ?>" alt="logo-vote" width="30" class="img-container">
                        Voter pour ce film
                </button>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($acteurs)): ?>
        <section class="cast-section">
            <h2><img src="<?= ImageHelper::url('img/logo/logo-acteur.png') ?>" alt="logo-real" width="50" class="img-container"> Acteurs (<?= count(array_filter($acteurs, fn($acteur) => $acteur['statut_validation'] === 'VALIDE' )) ?>)</h2>
            <div class="cast-grid">
                <?php foreach ($acteurs as $acteur): ?>
                    <?php if ($acteur['statut_validation'] !== 'VALIDE') continue; ?>
                    <div class="cast-member">
                        <a href="/sae-3-festivote-tas-cesar/app/views/public/detail-acteur.php?id=<?= $acteur['id_acteur'] ?>">
                            <div class="cast-photo">
                                <?php if (!empty($acteur['url_photo'])): ?>
                                    <img src="<?= ImageHelper::url(htmlspecialchars($acteur['url_photo'])) ?>" alt="<?= htmlspecialchars($acteur['prenom'] . ' ' . $acteur['nom']) ?>">
                                <?php else: ?>
                                    <div class="no-photo">👤</div>
                                <?php endif; ?>
                            </div>
                            <p><?= htmlspecialchars($acteur['prenom'] . ' ' . $acteur['nom']) ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
    
    <?php if (!empty($realisateurs)): ?>
        <section class="cast-section">
            <h2><img src="<?= ImageHelper::url('img/logo/logo-cam.png') ?>" alt="logo-real" width="50" class="img-container"> Réalisateurs (<?= count(array_filter($realisateurs, fn($realisateur) => $realisateur['statut_validation'] === 'VALIDE' )) ?>)</h2>
            <div class="cast-grid">
                <?php foreach ($realisateurs as $real): ?>
                    <?php if ($real['statut_validation'] !== 'VALIDE') continue; ?>
                    <div class="cast-member">
                        <a href="/sae-3-festivote-tas-cesar/app/views/public/detail-realisateur.php?id=<?= $real['id_realisateur'] ?>">
                            <div class="cast-photo">
                                <?php if (!empty($real['url_photo'])): ?>
                                    <img src="<?= ImageHelper::url(htmlspecialchars($real['url_photo'])) ?>" alt="<?= htmlspecialchars($real['prenom'] . ' ' . $real['nom']) ?>">
                                <?php else: ?>
                                    <div class="no-photo">👤</div>
                                <?php endif; ?>
                            </div>
                            <p><?= htmlspecialchars($real['prenom'] . ' ' . $real['nom']) ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<div id="modal-vote-confirm" class="modal">
  <div class="modal-content">
    <span id="close-vote-modal">×</span>
    <h3>Confirmer votre vote</h3>
    <p id="modal-vote-question"></p>
    <p style="font-size: 18px; font-weight: bold; color: #333; margin-bottom: 15px;">
        Pour : <span id="modal-element-name" style="color: #007bff;"></span>
    </p>
    <form method="POST" action="/sae-3-festivote-tas-cesar/app/views/vote/voter.php">
        <?= CsrfHelper::generateField() ?>
        <input type="hidden" name="type" id="modal-type" value="">
        <input type="hidden" name="id" id="modal-id" value="">
        <label for="modal-categorie-select">Choisissez une catégorie :</label>
        <select name="categorie" id="modal-categorie-select" required></select>
        <button type="submit" name="confirm_vote">Confirmer</button>
    </form>
  </div>
</div>

<?php
$cibleId = $film['id_film'];
$cibleType = 'film';
$cibleNom = $film['titre'];

include $rootPath . '/app/views/partials/commentaires.php';
?>

<script src="/sae-3-festivote-tas-cesar/public/js/vote.js"></script>

<?php include dirname(dirname(__FILE__)) . '/layouts/footer.php'; ?>
