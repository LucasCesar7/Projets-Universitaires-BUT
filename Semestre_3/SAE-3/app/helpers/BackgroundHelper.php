<?php

/*
* Recupere un fichier gif aleatoire destiner a un fond d'ecran
*/
class BackgroundHelper {
    
    /**
     * Récupérer un GIF de fond aléatoire
     */
    public static function getRandomBackground($directory = '/public/assets/backgrounds/') {
        $rootPath = dirname(dirname(dirname(__FILE__)));
        $fullPath = $rootPath . $directory;
        
        if (!is_dir($fullPath)) {
            error_log("Dossier backgrounds introuvable: $fullPath");
            return '';
        }
        
        $files = glob($fullPath . '*.{gif,GIF}', GLOB_BRACE);
        
        if (empty($files)) {
            error_log("Aucun fichier GIF trouvé dans: $fullPath");
            return '';
        }
        
        $randomFile = $files[array_rand($files)];
        $filename = basename($randomFile);
        
        return '/sae-3-festivote-tas-cesar/public/assets/backgrounds/' . $filename;
    }
    
    /**
     * Générer le CSS inline pour le background
     */
    public static function getBackgroundStyle($opacity = 1) {
        $backgroundUrl = self::getRandomBackground();
        
        if (empty($backgroundUrl)) {
            return '';
        }
        
        return "
            body {
                position: relative;
                min-height: 100vh;
            }

            body::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: url('$backgroundUrl') center/cover no-repeat;
                opacity: $opacity;
                z-index: -1;
            }
        ";
    }
}
