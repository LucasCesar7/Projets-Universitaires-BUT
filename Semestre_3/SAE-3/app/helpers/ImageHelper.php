<?php
/**
 * Helper pour générer les URLs d'images sécurisées
 */

class ImageHelper {
    
    private static $baseUrl = '/sae-3-festivote-tas-cesar/public/image.php?file=';
    
    /**
     * Générer l'URL sécurisée d'une image
     */
    public static function url($path) {
        if (empty($path)) {
            return '';
        }
        
        // NETTOYER LE CHEMIN (enlever les préfixes)
        $cleanPath = $path;
        
        // Enlever le préfixe complet s'il existe
        $cleanPath = str_replace('/sae-3-festivote-tas-cesar/public/uploads/', '', $cleanPath);
        $cleanPath = str_replace('/sae-3-festivote-tas-cesar/public/img/', '', $cleanPath);
        $cleanPath = str_replace('/sae-3-festivote-tas-cesar/public/', '', $cleanPath);
        
        // Enlever les slashes du début
        $cleanPath = ltrim($cleanPath, '/');
        
        // Si le chemin est vide après nettoyage, retourner vide
        if (empty($cleanPath)) {
            return '';
        }
        
        return self::$baseUrl . urlencode($cleanPath);
    }
    
    /**
     * Générer une balise <img> sécurisée
     */
    public static function img($path, $alt = '', $class = '', $attributes = []) {
        $url = self::url($path);
        
        if (empty($url)) {
            return '<div class="no-image" style="width:100px;height:100px;background:#ddd;display:flex;align-items:center;justify-content:center;">Pas d\'image</div>';
        }
        
        $attrString = '';
        foreach ($attributes as $key => $value) {
            $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
        }
        
        return sprintf(
            '<img src="%s" alt="%s" class="%s" draggable="false" oncontextmenu="return false;"%s>',
            htmlspecialchars($url),
            htmlspecialchars($alt),
            htmlspecialchars($class),
            $attrString
        );
    }
}
?>
