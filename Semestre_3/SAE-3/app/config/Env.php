<?php

class Env {
    
    private static $loaded = false;
    
    /**
     * Charge les variables d'environnement depuis le fichier .env
     */
    public static function load() {
        // Éviter de charger plusieurs fois
        if (self::$loaded) {
            return true;
        }
        
        $envFile = __DIR__ . '/.env';
        
        if (!file_exists($envFile)) {
            error_log("❌ Fichier .env introuvable : $envFile");
            return false;
        }
        
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Supprimer les espaces en début/fin et les retours chariot
            $line = trim($line);
            
            // Ignorer les lignes vides
            if (empty($line)) {
                continue;
            }
            
            // Ignorer les commentaires
            if (strpos($line, '#') === 0) {
                continue;
            }
            
            // Parser la ligne KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                
                // Nettoyer la clé et la valeur
                $key = trim($key);
                $value = trim($value);
                
                // Supprimer les guillemets autour de la valeur
                $value = trim($value, '"\'');
                
                // Définir la variable d'environnement
                if (!empty($key)) {
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
        
        self::$loaded = true;
        return true;
    }
    
    /**
     * Récupérer une variable d'environnement
     */
    public static function get($key, $default = null) {
        // S'assurer que les variables sont chargées
        self::load();
        
        // Vérifier dans $_ENV
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        // Vérifier avec getenv
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        // Retourner la valeur par défaut
        return $default;
    }
    
    /**
     * Récupérer une variable d'environnement OBLIGATOIRE
     * Lance une erreur si la variable n'existe pas ou est vide
     */
    public static function required($key) {
        self::load();
        
        $value = self::get($key);
        
        // Vérifier que la valeur existe et n'est pas vide
        if ($value === null || $value === '' || $value === false) {
            error_log("❌ ERREUR: Variable d'environnement '$key' manquante ou vide dans file.env !");
            error_log("📍 Fichier: " . __DIR__ . '/file.env');
            error_log("🔍 Variables chargées: " . print_r($_ENV, true));
            die("Configuration manquante: $key. Vérifiez votre fichier .env");
        }
        
        return $value;
    }
}

// Charger automatiquement les variables au chargement du fichier
Env::load();
