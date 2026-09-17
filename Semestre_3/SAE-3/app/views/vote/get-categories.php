<?php
session_start();
$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootPath . '/app/config/config.php';
require_once $rootPath . '/app/helpers/VoteToken.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) && !isset($_SESSION['id_electeur'])) {
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Session invalide']);
    exit;
}

$type = $_GET['type'] ?? '';
$id = intval($_GET['id'] ?? 0);

try {
    $pdo = dbconnect();
    $voteToken = new VoteToken($pdo);
    
    //empreinte annonyme
    $fingerprint = $voteToken->getCurrentVoterFingerprint();
    
    $categories = [];
    
    if ($type === 'film') {
        $query = "
            SELECT c.id_categorie, c.libelle,
                   EXISTS(
                       SELECT 1 FROM vote 
                       WHERE voter_fingerprint = :fingerprint 
                       AND type = 'film' 
                       AND categorie COLLATE utf8mb4_0900_ai_ci = c.libelle COLLATE utf8mb4_0900_ai_ci
                   ) as already_voted
            FROM categorie c
            INNER JOIN film_categorie fc ON c.id_categorie = fc.categorie_id_categorie
            WHERE fc.film_id_film = :id
            ORDER BY c.libelle
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $id, 'fingerprint' => $fingerprint]);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } elseif ($type === 'acteur') {
        $query = "
            SELECT DISTINCT c.id_categorie, c.libelle,
                   EXISTS(
                       SELECT 1 FROM vote 
                       WHERE voter_fingerprint = :fingerprint 
                       AND type = 'acteur' 
                       AND categorie COLLATE utf8mb4_0900_ai_ci = c.libelle COLLATE utf8mb4_0900_ai_ci
                   ) as already_voted
            FROM categorie c
            INNER JOIN film_categorie fc ON c.id_categorie = fc.categorie_id_categorie
            INNER JOIN acteur_film af ON fc.film_id_film = af.film_id_film
            WHERE af.acteur_id_acteur = :id
            ORDER BY c.libelle
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $id, 'fingerprint' => $fingerprint]);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } elseif ($type === 'realisateur') {
        $query = "
            SELECT DISTINCT c.id_categorie, c.libelle,
                   EXISTS(
                       SELECT 1 FROM vote 
                       WHERE voter_fingerprint = :fingerprint 
                       AND type = 'realisateur' 
                       AND categorie COLLATE utf8mb4_0900_ai_ci = c.libelle COLLATE utf8mb4_0900_ai_ci
                   ) as already_voted
            FROM categorie c
            INNER JOIN film_categorie fc ON c.id_categorie = fc.categorie_id_categorie
            INNER JOIN film_realisateur fr ON fc.film_id_film = fr.film_id_film
            WHERE fr.realisateur_id_realisateur = :id
            ORDER BY c.libelle
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $id, 'fingerprint' => $fingerprint]);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    foreach ($categories as &$cat) {
        $cat['already_voted'] = (bool)$cat['already_voted'];
    }
    
    echo json_encode([
        'success' => true,
        'categories' => $categories
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
