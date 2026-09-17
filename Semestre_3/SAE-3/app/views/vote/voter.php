<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootPath . '/app/config/config.php';
require_once $rootPath . '/app/helpers/VoteToken.php';
require_once $rootPath . '/app/models/Configuration.php';
require_once $rootPath . '/app/helpers/CsrfHelper.php';
require_once $rootPath . '/app/helpers/AuditLogger.php';

$configModel = new Configuration();
$audit = new AuditLogger();
$resultatsDisponibles = $configModel->areResultatsDisponibles();
$isInPeriodeVote = $configModel->isInPeriodeVote();


if (!isset($_SESSION['user_id']) && !isset($_SESSION['id_electeur'])) {
    header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
    exit;
}

// Vérifier que l'email est en session (requis pour l'empreinte)
if (!isset($_SESSION['email'])) {
    $_SESSION['error'] = "Session invalide. Veuillez vous reconnecter.";
    header('Location: /sae-3-festivote-tas-cesar/app/views/auth/login.php');
    exit;
}

if (!$isInPeriodeVote){
    $_SESSION['error'] = "Vous ne pouvez pas voter en dehors de la période de vote.";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_vote'])) {
    if (!CsrfHelper::validateToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token de sécurité invalide.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    $type = $_POST['type'] ?? '';
    $id_cible = intval($_POST['id'] ?? 0);
    $id_categorie = intval($_POST['categorie'] ?? 0);
    
    try {
        $pdo = dbconnect();
        $voteToken = new VoteToken($pdo);
        
        // Récupérer le libellé de la catégorie
        $stmt = $pdo->prepare("SELECT libelle FROM categorie WHERE id_categorie = :id");
        $stmt->execute(['id' => $id_categorie]);
        $categorie = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$categorie) {
            $_SESSION['error'] = "Catégorie invalide.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        
        $categorie_libelle = $categorie['libelle'];
        
        // VERIFIER SI L'ELECTEUR A DEJA VOTE (via empreinte anonyme)
        if ($voteToken->hasVoted($type, $categorie_libelle)) {
            $_SESSION['error'] = "Vous avez déjà voté pour la catégorie '$categorie_libelle' dans cette section.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        
        // ENREGISTRER LE VOTE ANONYME
        $voteToken->recordVote($type, $id_cible, $categorie_libelle);
        $audit->logVote($voteId, $type, $categorie);
        
        $_SESSION['success'] = "Vote enregistré avec succès pour la catégorie '$categorie_libelle' !";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur lors de l'enregistrement du vote: " . $e->getMessage();
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
} else {
    header('Location: /sae-3-festivote-tas-cesar/app/views/pages/categories.php');
    exit;
}
