<?php
session_start();

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Test de Débogage Commentaires</h1>";

// Test 1 : Session
echo "<h2>1. Session utilisateur</h2>";
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";

// Test 2 : Charger Env.php
echo "<h2>2. Chargement Env.php</h2>";
try {
    require_once dirname(__DIR__) . '/config/Env.php';
    $apiKey = Env::get('OPENAI_API_KEY');
    echo "Clé API : " . ($apiKey ? substr($apiKey, 0, 15) . "..." : "NON TROUVÉE") . "<br>";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "<br>";
}

// Test 3 : Charger ModerationHelper
echo "<h2>3. Chargement ModerationHelper</h2>";
try {
    require_once dirname(__DIR__) . '/helpers/ModerationHelper.php';
    $helper = new ModerationHelper();
    echo "ModerationHelper chargé<br>";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "<br>";
}

// Test 4 : Test de modération
echo "<h2>4. Test de modération</h2>";
try {
    $result = $helper->moderateContent("Ceci est un test de commentaire normal");
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
    if ($result['error']) {
        echo "Erreur API : " . $result['error'] . "<br>";
    } else {
        echo "Modération fonctionnelle<br>";
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "<br>";
}

// Test 5 : Charger le modèle Commentaire
echo "<h2>5. Chargement du modèle Commentaire</h2>";
try {
    require_once dirname(__DIR__) . '/config/config.php';
    require_once dirname(__DIR__) . '/models/Commentaire.php';
    $commentModel = new Commentaire();
    echo "Modèle Commentaire chargé<br>";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "<br>";
}

// Test 6 : Simuler l'ajout d'un commentaire
echo "<h2>6. Test d'ajout de commentaire</h2>";
try {
    // Simuler une session
    $_SESSION['user_id'] = 1;
    $_SESSION['pseudo'] = 'TestUser';
    
    $result = $commentModel->addCommentaire('TestUser', 'Ceci est un commentaire de test', 1, 'film');
    
    if ($result) {
        echo "Commentaire ajouté avec succès<br>";
        echo "Statut : " . ($_SESSION['moderation_status'] ?? 'inconnu') . "<br>";
        echo "Message : " . ($_SESSION['moderation_message'] ?? 'aucun') . "<br>";
    } else {
        echo "Échec de l'ajout du commentaire<br>";
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "<br>";
}
?>
