<?php
require_once dirname(dirname(dirname(__FILE__))) . '/helpers/CsrfHelper.php';
include dirname(dirname(__FILE__)) . '/layouts/header.php';
?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/gestionnaire.css">
<h1>Gestionnaire d'Acteur 🤵🏻</h1>


<?php if (empty($acteurs)): ?>
    <p>Aucune proposition en attente.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Affiche acteur</th>
                <th>Nom</th>
                <th>Prenom</th>
                <th>Nationalité</th>
                <th>Date de naissance</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($acteurs as $acteur): ?>
                <tr>
                    <td data-label="Affiche">
                        <img src="<?= ImageHelper::url(htmlspecialchars($acteur['url_photo'] ?? '/sae-3-festivote-tas-cesar/public/img/default.png')) ?>"
                            alt="<?= htmlspecialchars($acteur['nom'] ?? 'Acteur') ?>">
                    </td>

                    <td data-label="Nom"><?= htmlspecialchars($acteur['nom']) ?></td>
                    <td data-label="Prénom"><?= htmlspecialchars($acteur['prenom']) ?></td>
                    <td data-label="Nationalité"><?= htmlspecialchars($acteur['nationalite']) ?></td>
                    <td data-label="Date_de_naissance">
                        <?= htmlspecialchars($acteur['date_naissance'] ?? 'Non renseignée') ?>
                    </td>
                    <td data-label="Actions" class="actions">
                        <a href="/sae-3-festivote-tas-cesar/app/controllers/AdminController.php?action=validerActeur&id=<?= $acteur['id_acteur'] ?>" class="btn btn-success">✅ Valider</a>

                        <form action="/sae-3-festivote-tas-cesar/app/controllers/AdminController.php?action=refuserActeur&id=<?= $acteur['id_acteur'] ?>" method="POST" style="display:inline;">
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