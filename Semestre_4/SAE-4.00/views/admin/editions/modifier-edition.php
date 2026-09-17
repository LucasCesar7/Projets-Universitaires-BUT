<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../config/session.php';
require_once __DIR__ . '/../../../config/permissions.php';
requireAdmin();
require_once __DIR__ . '/../../../config/bootstrap-admin.php';

$manageEditionsUrl = appUrl('admin/editions');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . $manageEditionsUrl);
    exit;
}

$edition = $editionController->getEditionById($id);
if (!$edition) {
    header('Location: ' . $manageEditionsUrl);
    exit;
}

$error = '';
$formData = [
    'annee' => $_POST['annee'] ?? $edition->getAnnee(),
    'nom' => $_POST['nom'] ?? $edition->getNom(),
    'description' => $_POST['description'] ?? ($edition->getDescription() ?? ''),
    'theme' => $_POST['theme'] ?? ($edition->getTheme() ?? ''),
    'date_debut_candidatures' => $_POST['date_debut_candidatures'] ?? $edition->getDateDebutCandidatures(),
    'date_fin_candidatures' => $_POST['date_fin_candidatures'] ?? $edition->getDateFinCandidatures(),
    'date_debut' => $_POST['date_debut'] ?? $edition->getDateDebut(),
    'date_fin' => $_POST['date_fin'] ?? $edition->getDateFin()
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'annee' => (int)($_POST['annee'] ?? $edition->getAnnee()),
        'nom' => trim($_POST['nom'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'theme' => trim($_POST['theme'] ?? ''),
        'date_debut_candidatures' => $_POST['date_debut_candidatures'] ?? '',
        'date_fin_candidatures' => $_POST['date_fin_candidatures'] ?? '',
        'date_debut' => $_POST['date_debut'] ?? '',
        'date_fin' => $_POST['date_fin'] ?? ''
    ];

    $validationErrors = [];

    if (!\App\Services\CsrfService::isValid($_POST['csrf_token'] ?? null, 'update_edition')) {
        $validationErrors[] = "La validation du formulaire a expiré. Veuillez réessayer.";
    }

    // Validación básica
    if ($data['annee'] < 2000 || $data['annee'] > 2100) {
        $validationErrors[] = "L'année doit être entre 2000 et 2100.";
    }
    if (empty($data['nom'])) {
        $validationErrors[] = "Le nom de l'édition est obligatoire.";
    }
    if (empty($data['date_debut_candidatures']) || empty($data['date_fin_candidatures'])) {
        $validationErrors[] = "Les dates de candidatures sont obligatoires.";
    }
    if (empty($data['date_debut']) || empty($data['date_fin'])) {
        $validationErrors[] = "Les dates de votes sont obligatoires.";
    }

    // Validación de fechas
    if (!empty($data['date_debut_candidatures']) && !empty($data['date_fin_candidatures'])) {
        $debutCandidatures = DateTime::createFromFormat('Y-m-d\TH:i', $data['date_debut_candidatures']);
        $finCandidatures = DateTime::createFromFormat('Y-m-d\TH:i', $data['date_fin_candidatures']);

        if ($debutCandidatures && $finCandidatures) {
            if ($finCandidatures <= $debutCandidatures) {
                $validationErrors[] = "La date de fin des candidatures doit être après la date de début.";
            }
        }
    }

    if (!empty($data['date_debut']) && !empty($data['date_fin'])) {
        $debutVotes = DateTime::createFromFormat('Y-m-d\TH:i', $data['date_debut']);
        $finVotes = DateTime::createFromFormat('Y-m-d\TH:i', $data['date_fin']);

        if ($debutVotes && $finVotes) {
            if ($finVotes <= $debutVotes) {
                $validationErrors[] = "La date de fin des votes doit être après la date de début.";
            }
        }
    }

    // Validación de que la fecha de inicio de votos debe ser después del fin de candidaturas
    if (!empty($data['date_fin_candidatures']) && !empty($data['date_debut'])) {
        $finCandidatures = DateTime::createFromFormat('Y-m-d\TH:i', $data['date_fin_candidatures']);
        $debutVotes = DateTime::createFromFormat('Y-m-d\TH:i', $data['date_debut']);

        if ($finCandidatures && $debutVotes) {
            if ($debutVotes <= $finCandidatures) {
                $validationErrors[] = "La date de début des votes doit être après la date de fin des candidatures.";
            }
        }
    }

    if (empty($validationErrors)) {
        $success = $editionController->updateEdition($id, $data, $_FILES['image'] ?? null, isset($_POST['remove_image']));
        if ($success) {
            header('Location: ' . $manageEditionsUrl . '?success=1');
            exit;
        } else {
            $error = "Erreur lors de la mise à jour de l'édition.";
        }
    } else {
        $error = implode('<br>', $validationErrors);
    }
}

require_once __DIR__ . '/../../../views/partials/admin-header.php';
?>
<link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/admin-editions.css')); ?>">
<div class="admin-main-content">
    <div class="admin-page-header">
        <h1><i class="fas fa-edit"></i> Modifier l'Édition</h1>
    </div>

    <div class="admin-content">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Services\CsrfService::token('update_edition'), ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-group">
                <label class="form-label">Année</label>
                <input type="number" name="annee" required min="2000" max="2100" value="<?= htmlspecialchars($formData['annee']) ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Nom de l'édition</label>
                <input type="text" name="nom" required value="<?= htmlspecialchars($formData['nom']) ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description"><?= htmlspecialchars($formData['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Thème</label>
                <input type="text" name="theme" value="<?= htmlspecialchars($formData['theme']) ?>">
            </div>

            <div class="form-group date-range">
                <label class="form-label">Période des candidatures</label>
                <div class="date-inputs">
                    <input type="datetime-local" name="date_debut_candidatures" required value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($formData['date_debut_candidatures']))) ?>">
                    <span class="date-separator">à</span>
                    <input type="datetime-local" name="date_fin_candidatures" required value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($formData['date_fin_candidatures']))) ?>">
                </div>
            </div>

            <div class="form-group date-range">
                <label class="form-label">Période des votes</label>
                <div class="date-inputs">
                    <input type="datetime-local" name="date_debut" required value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($formData['date_debut']))) ?>">
                    <span class="date-separator">à</span>
                    <input type="datetime-local" name="date_fin" required value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($formData['date_fin']))) ?>">
                </div>
                <small class="form-text">L'édition sera active entre le début des candidatures et la fin des votes</small>
                <small class="form-text text-warning">⚠️ La date de début des votes doit être après la date de fin des candidatures</small>
            </div>

            <div class="form-group">
                <label class="form-label">Nouvelle bannière (optionnel)</label>
                <div class="file-upload">
                    <input type="file" name="image" id="image" accept="image/*">
                    <div class="file-upload-content">
                        <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                        <div class="file-upload-text">Cliquez ou glissez-déposez une nouvelle image</div>
                        <div class="file-upload-hint">JPG, PNG, GIF – max 5MB</div>
                    </div>
                </div>
                <div id="filePreview" class="file-preview"></div>
                <?php if ($edition->getImage()): ?>
                <div class="current-image">
                    <p>Image actuelle :</p>
                    <img loading="lazy" src="<?= htmlspecialchars(appUrl('public/' . ltrim($edition->getImage(), '/'))) ?>" alt="Bannière actuelle">
                    <label><input type="checkbox" name="remove_image" value="1"> Supprimer l'image actuelle</label>
                </div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <a href="<?= htmlspecialchars($manageEditionsUrl) ?>" class="btn btn-secondary">Annuler</a>
                <button type="button"
                    id="deleteBtn"
                    class="btn btn-danger"
                    data-id="<?= $id ?>"
                    data-name="<?= htmlspecialchars($edition->getNom()) ?>">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </form>

        <form method="POST" action="<?= htmlspecialchars($manageEditionsUrl) ?>" id="deleteEditionForm" style="display: none;">
            <input type="hidden" name="delete_edition_id" value="<?= $id ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Services\CsrfService::token('delete_edition'), ENT_QUOTES, 'UTF-8') ?>">
        </form>
    </div>
</div>

<script src="<?= htmlspecialchars(appUrl('assets/js/admin-add-edition.js')) ?>"></script>
<script src="<?= htmlspecialchars(appUrl('assets/js/admin-edition-edit.js')) ?>"></script>
