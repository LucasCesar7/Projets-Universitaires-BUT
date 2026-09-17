<?php

// Charger les variables d'environnement
require_once __DIR__ . '/Env.php';


define('DB_HOST', Env::get('DB_HOST'));
define('DB_PORT', Env::get('DB_PORT'));
define('DB_NAME', Env::required('DB_NAME')); 
define('DB_USER', Env::get('DB_USER'));
define('DB_PASS', Env::get('DB_PASS'));
define('DB_CHARSET', Env::get('DB_CHARSET'));

// Clés secrètes pour l'anonymisation des votes
define('VOTE_SALT', Env::required('VOTE_SALT')); 
define('IP_SALT', Env::required('IP_SALT')); 
date_default_timezone_set('Europe/Paris');
/**
 * Fonction de connexion à la base de données
 */
function dbconnect() {
    $dsn = sprintf(
        "mysql:host=%s;port=%s;dbname=%s;charset=%s",
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
    ];
    
    try {
        $connexion = new PDO($dsn, DB_USER, DB_PASS, $options);
        $connexion->exec("SET time_zone = '+01:00'");
        return $connexion;
    } catch (PDOException $e) {
        error_log("Échec de la connexion BDD: " . $e->getMessage());
        
        // Message générique pour l'utilisateur
        die("Erreur de connexion à la base de données. Veuillez contacter l'administrateur.");
    }
}
