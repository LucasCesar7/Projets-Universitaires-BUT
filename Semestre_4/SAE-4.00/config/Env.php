<?php

class Env {
    
    private static $loaded = false;
    
    /**
     * Loads environment variables from config/.env or root .env.
     */
    public static function load() {
        // Avoid loading the file more than once.
        if (self::$loaded) {
            return true;
        }
        
        $envCandidates = [
            __DIR__ . '/.env',
            dirname(__DIR__) . '/.env',
        ];

        $envFile = null;
        foreach ($envCandidates as $candidate) {
            if (file_exists($candidate)) {
                $envFile = $candidate;
                break;
            }
        }

        if ($envFile === null) {
            error_log('Fichier .env introuvable (config/.env ou racine du projet).');
            return false;
        }
        
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Trim surrounding whitespace and line endings.
            $line = trim($line);
            
            // Ignore empty lines.
            if (empty($line)) {
                continue;
            }
            
            // Ignore comments.
            if (strpos($line, '#') === 0) {
                continue;
            }
            
            // Parse a KEY=VALUE line.
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                
                // Clean the key and value.
                $key = trim($key);
                $value = trim($value);
                
                // Remove surrounding quotes from the value.
                $value = trim($value, '"\'');
                
                // Set the environment variable.
                if (!empty($key)) {
                    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
                        continue;
                    }

                    $existingValue = getenv($key);

                    if ($existingValue !== false && $existingValue !== '') {
                        $_ENV[$key] = $existingValue;
                        continue;
                    }

                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
        
        self::$loaded = true;
        return true;
    }
    
    /**
     * Retrieves an environment variable.
     */
    public static function get($key, $default = null) {
        // Ensure the variables are loaded.
        self::load();
        
        // Check in $_ENV.
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        // Check with getenv.
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        // Return the default value.
        return $default;
    }
    
    /**
     * Retrieves a REQUIRED environment variable.
     * Raises an error if the variable does not exist or is empty.
     */
    public static function required($key) {
        self::load();
        
        $value = self::get($key);
        
        // Check that the value exists and is not empty.
        if ($value === null || $value === '' || $value === false) {
            error_log("ERROR: Environment variable '$key' is missing or empty in file.env!");
            error_log("File: " . __DIR__ . '/file.env');
            error_log("Loaded variables: " . print_r($_ENV, true));
            die("Configuration manquante: $key. Vérifiez votre fichier .env");
        }
        
        return $value;
    }
}

// Automatically load variables when this file is included.
Env::load();
