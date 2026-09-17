<?php 
require_once dirname(dirname(dirname(__FILE__))) . '/helpers/CsrfHelper.php';
include dirname(dirname(__FILE__)) . '/layouts/header.php'; 
?>
<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/GestionnaireUtilisateur.css">

<main>
    <h2>Liste des électeurs</h2>
    <?php if (empty($electeurs)): ?>
        <p>Aucun électeur trouvé.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Pseudo</th>
                    <th>Date de naissance</th>
                    <th>Date d'inscription</th>
                    <th>Vérification email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($electeurs as $electeur): ?>
                    <tr>
                        <td data-label="ID"><?= htmlspecialchars($electeur['id_electeur']) ?></td>
                        <td data-label="Nom"><?= htmlspecialchars($electeur['nom']) ?></td>
                        <td data-label="Prénom"><?= htmlspecialchars($electeur['prenom']) ?></td>
                        <td data-label="Email"><?= htmlspecialchars($electeur['email']) ?></td>
                        <td data-label="Pseudo"><?= htmlspecialchars($electeur['pseudo']) ?></td>
                        <td data-label="Date de naissance"><?= htmlspecialchars($electeur['date_naissance']) ?></td>
                        <td data-label="Date d'inscription"><?= htmlspecialchars($electeur['date_inscription']) ?></td>
                        <td data-label="Vérification email"><?= htmlspecialchars($electeur['email_verifie']) ?></td>

                        <td>
                            <!-- bouton supprimer -->
                            <form method="POST" action="/sae-3-festivote-tas-cesar/app/controllers/GestionnaireUtilisateurController.php?action=deleteElecteur" style="display:inline;">
                                <?= CsrfHelper::generateField() ?>
                                <input type="hidden" name="id" value="<?= $electeur['id_electeur'] ?>">
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <button onclick="openModal('addElecteurModal')" class="btn btn-primary">Ajouter un électeur</button>

    <!-- modal ajout électeur -->
    <div id="addElecteurModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addElecteurModal')">&times;</span>
            <h3>Ajouter un électeur</h3>
            <form method="POST" action="/sae-3-festivote-tas-cesar/app/controllers/GestionnaireUtilisateurController.php?action=addElecteur">
                <?= CsrfHelper::generateField() ?>
                <input type="text" name="nom" placeholder="Nom" required>
                <input type="text" name="prenom" placeholder="Prénom" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="pseudo" placeholder="Pseudo" required>
                <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
                <input type="password" name="mot_de_passe_confirm" placeholder="Confirmer mot de passe" required>
                <input type="date" name="date_naissance" required>
                <button type="submit" class="btn btn-success">Créer</button>
            </form>
        </div>
    </div>

    <h2>Liste des candidats</h2>
    <?php if (empty($candidats)): ?>
        <p>Aucun candidat trouvé.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Date d'inscription</th>
                    <th>Vérification email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($candidats as $candidat): ?>
                    <tr>
                        <td data-label="ID"><?= htmlspecialchars($candidat['id_candidat']) ?></td>
                        <td data-label="Nom"><?= htmlspecialchars($candidat['nom']) ?></td>
                        <td data-label="Prénom"><?= htmlspecialchars($candidat['prenom']) ?></td>
                        <td data-label="Email"><?= htmlspecialchars($candidat['email']) ?></td>
                        <td data-label="Date d'inscription"><?= htmlspecialchars($candidat['date_inscription']) ?></td>
                        <td data-label="Vérification email"><?= htmlspecialchars($candidat['email_verifie']) ?></td>
                        <td>
                            <!-- bouton supprimer -->
                            <form method="POST" action="/sae-3-festivote-tas-cesar/app/controllers/GestionnaireUtilisateurController.php?action=deleteCandidat" style="display:inline;">
                                <?= CsrfHelper::generateField() ?>
                                <input type="hidden" name="id" value="<?= $candidat['id_candidat'] ?>">
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- bouton ajouter candidat -->
    <button onclick="openModal('addCandidatModal')" class="btn btn-primary">Ajouter un candidat</button>

    <!-- modal ajout candidat -->
    <div id="addCandidatModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addCandidatModal')">&times;</span>
            <h3>Ajouter un candidat</h3>
            <form method="POST" action="/sae-3-festivote-tas-cesar/app/controllers/GestionnaireUtilisateurController.php?action=addCandidat">
                <?= CsrfHelper::generateField() ?>
                <input type="text" name="nom" placeholder="Nom" required>
                <input type="text" name="prenom" placeholder="Prénom" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="pseudo" placeholder="Pseudo">
                <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
                <input type="password" name="mot_de_passe_confirm" placeholder="Confirmer mot de passe" required>

                <button type="submit" class="btn btn-success">Créer</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'block';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
    </script>
</main>



<?php include dirname(dirname(__FILE__)) . '/layouts/footer.php'; ?>