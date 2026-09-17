<?php
require_once dirname(dirname(dirname(__FILE__))) . '/helpers/CsrfHelper.php';


include dirname(dirname(__FILE__)) . '/layouts/header.php';
?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/gestionnaire.css">
<h1>Gestionnaire de Film 🎬</h1>


<?php if (empty($films)): ?>
    <p>Aucune proposition en attente.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Affiche film</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Candidat</th>
                <th>Année</th>
                <th>Durée</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($films as $film): ?>
                <tr>
                    <td data-label="Affiche"><img src="<?= ImageHelper::url(htmlspecialchars($film['url_affiche'])) ?>" alt="<?= htmlspecialchars($film['titre']) ?>"></td>
                    <td data-label="Titre"><?= htmlspecialchars($film['titre']) ?></td>
                    <td data-label="Description"><?= htmlspecialchars($film['description']) ?></td>
                    <td data-label="Candidat"><?= htmlspecialchars($film['nom_candidat'] ?? 'Admin') ?></td>
                    <td data-label="Année"><?= htmlspecialchars($film['annee']) ?></td>
                    <td data-label="Durée"><?= htmlspecialchars($film['duree']) ?> min</td>
                    <td data-label="Actions" class="actions">
                        <a href="/sae-3-festivote-tas-cesar/app/controllers/AdminController.php?action=validerFilm&id=<?= $film['id_film'] ?>" class="btn btn-success">✅ Valider</a>

                        <form action="/sae-3-festivote-tas-cesar/app/controllers/AdminController.php?action=refuserFilm&id=<?= $film['id_film'] ?>" method="POST" style="display:inline;">
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