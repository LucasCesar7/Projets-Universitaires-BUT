<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /sae-3-festivote-tas-cesar/public/index.php');
    exit;
}

require_once dirname(dirname(__FILE__)) . '/layouts/header.php';
require_once dirname(dirname(dirname(__FILE__))) . '/helpers/CsrfHelper.php';

// Récupérer les messages
$success = $_SESSION['success'] ?? null;
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['success'], $_SESSION['errors']);

// Récupérer les valeurs actuelles
require_once dirname(dirname(dirname(__FILE__))) . '/models/Configuration.php';
$configModel = new Configuration();
$periodeAjoutDebut = $configModel->getConfig('periode_ajout_debut');
$periodeAjoutFin = $configModel->getConfig('periode_ajout_fin');
$periodeVoteDebut = $configModel->getConfig('periode_vote_debut');
$periodeVoteFin = $configModel->getConfig('periode_vote_fin');
$systemeActif = $configModel->getConfig('systeme_actif');


function formatDateTimeLocal($datetime) {
    if (!$datetime) return '';
    try {
        $date = new DateTime($datetime);
        return $date->format('Y-m-d\TH:i'); // Format attendu par datetime-local
    } catch (Exception $e) {
        error_log("Erreur formatage date: " . $e->getMessage());
        return '';
    }
}

$periodeAjoutDebutFormatted = formatDateTimeLocal($periodeAjoutDebut);
$periodeAjoutFinFormatted = formatDateTimeLocal($periodeAjoutFin);
$periodeVoteDebutFormatted = formatDateTimeLocal($periodeVoteDebut);
$periodeVoteFinFormatted = formatDateTimeLocal($periodeVoteFin);

// Statut actuel
$statutActuel = $configModel->getStatutSysteme();
$statutLabels = [
    'INACTIF' => 'Inactif',
    'AJOUT' => 'Période d\'ajout des œuvres',
    'VOTE' => 'Période de vote',
    'RESULTATS' => '🏆 Résultats disponibles'
];
$statutColors = [
    'INACTIF' => '#6c757d',
    'AJOUT' => '#ffc107',
    'VOTE' => '#007bff',
    'RESULTATS' => '#28a745'
];
?>

<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/configuration.css">

<div class="config-container">
    <div class="config-header">
        <h1>Configuration du Système</h1>
        <div class="statut-badge" style="background: <?= $statutColors[$statutActuel] ?>;">
            <?= $statutLabels[$statutActuel] ?>
        </div>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            ✅ <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>❌ Erreurs :</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="info-box">
        <strong>Fonctionnement du système :</strong>
        <ul>
            <li><strong>Période d'ajout</strong> : Les candidats peuvent proposer des films, acteurs et réalisateurs</li>
            <li><strong>Période de vote</strong> : Les électeurs peuvent voter pour leurs favoris</li>
            <li><strong>Après la période de vote</strong> : La page "Résultats" devient automatiquement accessible</li>
        </ul>
    </div>
    
    <form method="POST" action="/sae-3-festivote-tas-cesar/app/controllers/ConfigurationController.php?action=update">
        <?= CsrfHelper::generateField() ?>
        <!-- Période d'ajout des œuvres -->
        <div class="form-section">
            <h3>Période d'ajout des œuvres</h3>
            <p>Les candidats peuvent soumettre des films, acteurs et réalisateurs pendant cette période.</p>
            <div class="form-row">
                <div class="form-group">
                    <label for="periode_ajout_debut">📅 Date de début</label>
                    <input type="datetime-local" 
                        class="form-control" 
                        id="periode_ajout_debut" 
                        name="periode_ajout_debut" 
                        value="<?= htmlspecialchars($periodeAjoutDebutFormatted) ?>" 
                        required
                    >
                </div>
                <div class="form-group">
                    <label for="periode_ajout_fin">📅 Date de fin</label>
                    <input 
                       type="datetime-local" 
                        class="form-control" 
                        id="periode_ajout_fin" 
                        name="periode_ajout_fin" 
                        value="<?= htmlspecialchars($periodeAjoutFinFormatted) ?>" 
                        required
                    >
                </div>
            </div>
        </div>
        
        <!-- Période de vote -->
        <div class="form-section">
            <h3>Période de vote</h3>
            <p>Les électeurs peuvent voter pendant cette période. Les résultats seront disponibles après la date de fin.</p>
            <div class="form-row">
                <div class="form-group">
                    <label for="periode_vote_debut">📅 Date de début du vote</label>
                    <input 
                        type="datetime-local"
                        class="form-control" 
                        id="periode_vote_debut" 
                        name="periode_vote_debut" 
                        value="<?= htmlspecialchars($periodeVoteDebutFormatted) ?>" 
                        required
                    >
                </div>
                <div class="form-group">
                    <label for="periode_vote_fin">📅 Date de fin du vote</label>
                    <input 
                        type="datetime-local" 
                        class="form-control" 
                        id="periode_vote_fin" 
                        name="periode_vote_fin" 
                        value="<?= htmlspecialchars($periodeVoteFinFormatted) ?>" 
                        required
                    >
                </div>
            </div>
        </div>
        
        <!-- Activation du système -->
        <div class="form-section">
            <h3>Paramètres système</h3>
            <div class="checkbox-group">
                <input 
                    type="checkbox" 
                    id="systeme_actif" 
                    name="systeme_actif" 
                    <?= $systemeActif == '1' ? 'checked' : '' ?>
                >
                <label for="systeme_actif">Système actif (décocher pour désactiver temporairement le système)</label>
            </div>
        </div>
        
        <button type="submit" class="btn-submit">
            💾 Enregistrer la configuration
        </button>
    </form>
</div>

<?php include dirname(dirname(__FILE__)) . '/layouts/footer.php'; ?>
