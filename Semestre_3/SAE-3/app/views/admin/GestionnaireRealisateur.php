<?php
require_once dirname(dirname(dirname(__FILE__))) . '/helpers/CsrfHelper.php';
include dirname(dirname(__FILE__)) . '/layouts/header.php';
?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/gestionnaire.css">
<h1>Gestionnaire de Réalisateur 🎥</h1>


<?php if (empty($realisateurs)): ?>
    <p>Aucune proposition en attente.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Affiche réalisateur</th>
                <th>Nom</th>
                <th>Prenom</th>
                <th>Nationalité</th>
                <th>Date de naissance</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($realisateurs as $realisateur): ?>
                <tr>
                    <td data-label="Affiche">
                        <img src="<?= ImageHelper::url(htmlspecialchars($realisateur['url_photo'] ?? '/sae-3-festivote-tas-cesar/public/img/default.png')) ?>"
                            alt="<?= htmlspecialchars($realisateur['nom'] ?? 'Realisateur') ?>">
                    </td>

                    <td data-label="Nom"><?= htmlspecialchars($realisateur['nom']) ?></td>
                    <td data-label="Prénom"><?= htmlspecialchars($realisateur['prenom']) ?></td>
                    <td data-label="Nationalité"><?= htmlspecialchars($realisateur['nationalite']) ?></td>
                    <td data-label="Date_de_naissance">
                        <?= htmlspecialchars($realisateur['date_naissance'] ?? 'Non renseignée') ?>
                    </td>
                    <td data-label="Actions" class="actions">
                        <a href="/sae-3-festivote-tas-cesar/app/controllers/AdminController.php?action=validerRealisateur&id=<?= $realisateur['id_realisateur'] ?>" class="btn btn-success">✅ Valider</a>

                        <form action="/sae-3-festivote-tas-cesar/app/controllers/AdminController.php?action=refuserRealisateur&id=<?= $realisateur['id_realisateur'] ?>" method="POST" style="display:inline;">
                            <?= CsrfHelper::generateField() ?>
                            <button type="submit" class="btn btn-danger">❌ Refuser</button>
                            <input type="text" name="motif" placeholder="Motif du refus" required>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include dirname(dirname(__FILE__)) . '/layouts/footer.php'; ?>