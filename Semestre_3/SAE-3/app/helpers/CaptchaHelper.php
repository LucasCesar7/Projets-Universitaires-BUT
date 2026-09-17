<?php

/*
* utilisation de la librairie reCAPTCHA
*/
class CaptchaHelper {
    
    private $siteKey;
    private $secretKey;
    
    public function __construct() {
        require_once __DIR__ . '/../config/Env.php';
        
        $this->siteKey = Env::get('RECAPTCHA_SITE_KEY', '');
        $this->secretKey = Env::get('RECAPTCHA_SECRET_KEY', '');
        
        if (empty($this->siteKey) || empty($this->secretKey)) {
            error_log("reCAPTCHA non configuré dans .env");
        }
    }
    
    /**
     * Afficher le widget reCAPTCHA
     */
    public function renderCaptcha() {
        if (empty($this->siteKey)) {
            return '<p style="color: red;">CAPTCHA non configuré</p>';
        }
        
        return '<div class="g-recaptcha" data-sitekey="' . htmlspecialchars($this->siteKey) . '"></div>';
    }
    
    /**
     * Vérifier la réponse du CAPTCHA
     * @param string $response Le token reCAPTCHA soumis par le formulaire
     * @return bool True si le CAPTCHA est valide, false sinon
     */
    public function verifyCaptcha($response) {
        if (empty($this->secretKey)) {
            error_log("reCAPTCHA Secret Key manquante");
            return false;
        }
        
        if (empty($response)) {
            error_log("Aucune réponse reCAPTCHA fournie");
            return false;
        }
        
        // Appel API Google reCAPTCHA
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $this->secretKey,
            'response' => $response,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        
        if ($result === false) {
            error_log("Erreur lors de l'appel à l'API reCAPTCHA");
            return false;
        }
        
        $resultJson = json_decode($result, true);
        
        // Vérifier la réponse
        if (isset($resultJson['success']) && $resultJson['success'] === true) {
            error_log("✅ reCAPTCHA validé avec succès");
            return true;
        } else {
            error_log("reCAPTCHA invalide : " . json_encode($resultJson));
            return false;
        }
    }
}
