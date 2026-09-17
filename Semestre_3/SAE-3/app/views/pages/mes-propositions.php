<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'candidat') {
    header("Location: /sae-3-festivote-tas-cesar/app/views/public/login.php");
    exit;
}
$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootPath . '/app/config/config.php';


$pdo = dbconnect();

$candidatId = $_SESSION['user_id'];


$stmt = $pdo->prepare('SELECT * FROM film WHERE candidat_id_candidat = ? ORDER BY titre');
$stmt->execute([$candidatId]);
$films = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT * FROM acteur WHERE candidat_id_candidat = ? ORDER BY nom');
$stmt->execute([$candidatId]);
$acteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT * FROM realisateur WHERE candidat_id_candidat = ? ORDER BY nom');
$stmt->execute([$candidatId]);
$realisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);


include dirname(dirname(__FILE__)) . '/layouts/header.php';
?>
<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/mes-propositions.css">

<h1>Mes candidatures</h1>
<?php if (empty($films) && empty($acteurs) && empty($realisateurs)): ?>
    <p>Vous n'avez proposé aucun élément pour le festival.</p>
<?php else: ?>
    <h2>Propositions de films</h2>
    <?php if (!empty($films)): ?>
        <table>
            <thead>
                <tr>
                    <th>Affiche</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Année</th>
                    <th>duree</th>
                    <th>Statut</th>
                    <th>Motif de refus (Si refuser)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($films as $film): ?>
                    <tr>
                        <td>
                            <?php if (!empty($film['url_affiche'])): ?>
                                <img src="<?= ImageHelper::url(htmlspecialchars($film['url_affiche'])) ?>" alt="<?= htmlspecialchars($film['titre']) ?>" width="80">
                            <?php else: ?>
                                🎬
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($film['titre']) ?></td>
                        <td><?= htmlspecialchars($film['description']) ?></td>
                        <td><?= htmlspecialchars($film['annee']) ?></td>
                        <td><?= htmlspecialchars($film['duree']) ?> min</td>
                        <td>
                            <?php if ($film['statut_validation'] === 'VALIDE'): ?>
                                <span class="status valide">Validé</span>
                            <?php elseif ($film['statut_validation'] === 'REFUSE'): ?>
                                <span class="statut refuse">❌ Refusé</span>
                            <?php else: ?>
                                <span class="statut attente">En attente</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($film['motif_refus'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun film proposé.</p>
            <?php endif; ?>
            </tbody>
        </table>

        <h2>Propostion d'acteurs</h2>
        <?php if (!empty($acteurs)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Nationalité</th>
                        <th>Date de naissance</th>
                        <th>Statut</th>
                        <th>Motif de refus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($acteurs as $acteur): ?>
                        <tr>
                            <td>
                                <?php if (!empty($acteur['url_photo'])): ?>
                                   <img src="<?= ImageHelper::url(htmlspecialchars($acteur['url_photo'])) ?>" alt="<?= htmlspecialchars($acteur['nom']) ?>" width="80">
                                <?php else: ?>
                                    👤
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($acteur['nom']) ?></td>
                            <td><?= htmlspecialchars($acteur['prenom']) ?></td>
                            <td><?= htmlspecialchars($acteur['nationalite']) ?></td>
                            <td><?= htmlspecialchars($acteur['date_naissance'] ?? 'Non renseignée') ?></td>
                            <td>
                                <?php if ($acteur['statut_validation'] === 'VALIDE'): ?>
                                    <span class="status valide">Validé</span>
                                <?php elseif ($acteur['statut_validation'] === 'REFUSE'): ?>
                                    <span class="statut refuse">❌ Refusé</span>
                                <?php else: ?>
                                    <span class="statut attente">En attente</span>
                                <?php endif; ?>
                            <td><?= htmlspecialchars($acteur['motif_refus'] ?? '') ?></td>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun acteur proposé.</p>
                <?php endif; ?>
                </tbody>
            </table>

            <h2>Propostion de réalisateurs</h2>
            <?php if (!empty($realisateurs)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Nationalité</th>
                            <th>Date de naissance</th>
                            <th>Statut</th>
                            <th>Motif de refus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($realisateurs as $realisateur): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($realisateur['url_photo'])): ?>
                                        <img src="<?= ImageHelper::url(htmlspecialchars($realisateur['url_photo'])) ?>" alt="<?= htmlspecialchars($realisateur['nom']) ?>" width="80">
                                    <?php else: ?>
                                        👤
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($realisateur['nom']) ?></td>
                                <td><?= htmlspecialchars($realisateur['prenom']) ?></td>
                                <td><?= htmlspecialchars($realisateur['nationalite']) ?></td>
                                <td><?= htmlspecialchars($realisateur['date_naissance'] ?? 'Non renseignée') ?></td>
                                <td>
                                    <?php if ($realisateur['statut_validation'] === 'VALIDE'): ?>
                                        <span class="status valide">Validé</span>
                                    <?php elseif ($realisateur['statut_validation'] === 'REFUSE'): ?>
                                        <span class="statut refuse">❌ Refusé</span>
                                    <?php else: ?>
                                        <span class="statut attente">En attente</span>
                                    <?php endif; ?>
                                <td><?= htmlspecialchars($realisateur['motif_refus'] ?? '') ?></td>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucun réalisateur proposé.</p>
                    <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php include dirname(dirname(__FILE__)) . '/layouts/footer.php'; ?>