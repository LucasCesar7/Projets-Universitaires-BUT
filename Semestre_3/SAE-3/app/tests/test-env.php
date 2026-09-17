<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test des variables d'environnement</h1>";


echo "<h2>DEBUG ENVIRONNEMENT</h2>";

require_once dirname(__DIR__) .'/config/config.php';

// Test 1: Fichier .env existe-t-il ?
$envPath = dirname(__DIR__)  . '/config/.env';
echo "<p>Fichier .env existe : " . (file_exists($envPath) ? "OUI" : "NON") . "</p>";

// Test 2: Fichier Env.php existe-t-il ?
$envPhp = dirname(__DIR__)  . '/config/Env.php';
echo "<p>Fichier Env.php existe : " . (file_exists($envPhp) ? "OUI" : "NON") . "</p>";

// Test 3: Charger et afficher les variables
require_once $envPhp;
echo "<h3>Variables d'environnement chargées :</h3>";
echo "<pre>";
echo "SENDGRID_API_KEY: " . (Env::get('SENDGRID_API_KEY') ? "DÉFINIE" : "NON DÉFINIE") . "\n";
echo "SMTP_FROM_EMAIL: " . (Env::get('SMTP_FROM_EMAIL') ?: "NON DÉFINIE") . "\n";
echo "SMTP_FROM_NAME: " . (Env::get('SMTP_FROM_NAME') ?: "NON DÉFINIE") . "\n";
echo "</pre>";

// Test 4: Vérifier les permissions
if (file_exists($envPath)) {
    echo "<p>Permissions .env: " . substr(sprintf('%o', fileperms($envPath)), -4) . "</p>";
}
?>