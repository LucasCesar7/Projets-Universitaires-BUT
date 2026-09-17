<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use SendGrid\Mail\Mail;
/*
* helper pour l'envoie de mail avec sendgrid
*/
class EmailHelper {
    
    private $sendgridApiKey;
    private $FROMEmail;
    private $FROMName;
    private $baseUrl;
    
    public function __construct() {
        require_once __DIR__ . '/../config/Env.php';
        
        $this->sendgridApiKey = Env::get('SENDGRID_API_KEY');
        $this->FROMEmail = Env::get('SMTP_FROM_EMAIL');
        $this->FROMName = Env::get('SMTP_FROM_NAME');
        
        // Déterminer l'URL de base dynamiquement
        $this->baseUrl = $this->getBaseUrl();
    }
    
    /**
     * Déterminer l'URL de base selon l'environnement
     */
    private function getBaseUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $basePath = '/sae-3-festivote-tas-cesar';
        
        return $protocol . '://' . $host . $basePath;
    }
    
    /**
     * Envoyer un email de vérification
     */
    public function sendVerificationEmail($toEmail, $token, $prenom, $role) {
        // Utiliser l'URL dynamique
        $verificationUrl = $this->baseUrl . "/app/controllers/VerifyEmailController.php?token=" . urlencode($token);
        
        $subject = "Vérifiez votre adresse email - CineVote";
        
        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; padding: 15px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 8px; margin: 20px 0; font-weight: bold; }
                .footer { text-align: center; margin-top: 20px; color: #777; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎬 Bienvenue sur CineVote !</h1>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>{$prenom}</strong>,</p>
                    <p>Merci de vous être inscrit sur CineVote !</p>
                    <p>Pour activer votre compte, veuillez cliquer sur le bouton ci-dessous :</p>
                    <p style='text-align: center;'>
                        <a href='{$verificationUrl}' class='button'>✅ Vérifier mon email</a>
                    </p>
                    <p>Ou copiez ce lien dans votre navigateur :</p>
                    <p style='word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 5px;'>{$verificationUrl}</p>
                    <p><strong>Ce lien expirera dans 24 heures.</strong></p>
                    <p>Si vous n'avez pas créé de compte, ignorez cet email.</p>
                </div>
                <div class='footer'>
                    <p>© 2025 CineVote - Tous droits réservés</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendEmail($toEmail, $subject, $htmlContent);
    }
    
    /**
     * Envoyer un email de contact
     */
    public function sendContactEmail($nom, $email, $sujet, $message) {
        require_once __DIR__ . '/../config/Env.php';
        $toEmail = Env::get('CONTACT_EMAIL');
        
        $subject = "Nouveau message de contact - " . $sujet;
        
        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .info-box { background: white; padding: 15px; border-left: 4px solid #3498db; margin: 15px 0; border-radius: 5px; }
                .message-box { background: white; padding: 20px; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 20px; color: #777; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Nouveau message de contact</h1>
                </div>
                <div class='content'>
                    <div class='info-box'>
                        <p><strong>Nom :</strong> {$nom}</p>
                        <p><strong>mail :</strong> <a href='mailto:{$email}'>{$email}</a></p>
                        <p><strong>Sujet :</strong> {$sujet}</p>
                        <p><strong>Date :</strong> " . date('d/m/Y à H:i') . "</p>
                    </div>
                    
                    <div class='message-box'>
                        <h3>💬 Message :</h3>
                        <p>" . nl2br(htmlspecialchars($message)) . "</p>
                    </div>
                    
                    <p style='background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404;'>
                        <strong>Note :</strong> Pour répondre à cet utilisateur, répondez directement à cet email ou utilisez l'adresse : <a href='mailto:{$email}'>{$email}</a>
                    </p>
                </div>
                <div class='footer'>
                    <p>© 2025 CineVote - Système de contact automatique</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendEmail($toEmail, $subject, $htmlContent, $email);
    }
    
    /**
     * Méthode générique d'envoi d'email via SendGrid
     */
    private function sendEmail($toEmail, $subject, $htmlContent, $replyTo = null) {
        try {
            if (empty($this->sendgridApiKey)) {
                error_log("ERREUR: API Key SendGrid manquante !");
                return false;
            }
            
            if (empty($this->FROMEmail)) {
                error_log("ERREUR: Email FROM manquant !");
                return false;
            }
            
            $email = new Mail();
            $email->setFrom($this->FROMEmail, $this->FROMName);
            $email->setSubject($subject);
            $email->addTo($toEmail);
            $email->addContent("text/html", $htmlContent);
            
            if ($replyTo) {
                $email->setReplyTo($replyTo);
            }
            
            $sendgrid = new \SendGrid($this->sendgridApiKey);
            $response = $sendgrid->send($email);
            
            $statusCode = $response->statusCode();
            $responseBody = $response->body();
            
            if ($statusCode >= 200 && $statusCode < 300) {
                error_log("Email envoyé avec succès à $toEmail");
                return true;
            } else {
                error_log("Erreur SendGrid: $statusCode - $responseBody");
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Exception SendGrid: " . $e->getMessage());
            return false;
        }
    }
    /**
    * Envoie un email de réinitialisation de mot de passe
    */
    public function sendPasswordResetEmail($email, $token, $prenom) {
        // Utiliser l'URL dynamique au lieu de localhost
        $resetLink = $this->baseUrl . "/app/views/auth/reset-password.php?token=" . urlencode($token);
        
        $subject = "Réinitialisation de votre mot de passe - CineVote";
        
        $htmlContent = "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 50px auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #E50914 0%, #B20710 100%); color: #ffffff; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; }
                .content { padding: 30px; color: #333333; line-height: 1.8; }
                .button { display: inline-block; margin: 20px 0; padding: 15px 30px; background: #E50914; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; }
                .button:hover { background: #B20710; }
                .footer { background: #f4f4f4; padding: 20px; text-align: center; font-size: 12px; color: #666666; }
                .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Réinitialisation de mot de passe</h1>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>" . htmlspecialchars($prenom) . "</strong>,</p>
                    
                    <p>Vous avez demandé à réinitialiser votre mot de passe pour votre compte <strong>CineVote</strong>.</p>
                    
                    <p>Pour définir un nouveau mot de passe, cliquez sur le bouton ci-dessous :</p>
                    
                    <p style='text-align: center;'>
                        <a href='$resetLink' class='button'>Réinitialiser mon mot de passe</a>
                    </p>
                    
                    <div class='warning'>
                        <strong>Important :</strong>
                        <ul>
                            <li>Ce lien est valable pendant <strong>1 heure</strong></li>
                            <li>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email</li>
                            <li>Ne partagez jamais ce lien avec qui que ce soit</li>
                        </ul>
                    </div>
                    
                    <p style='font-size: 12px; color: #666;'>
                        Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :<br>
                        <a href='$resetLink' style='color: #E50914; word-break: break-all;'>$resetLink</a>
                    </p>
                    
                    <p>Cordialement,<br><strong>L'équipe CineVote</strong></p>
                </div>
                <div class='footer'>
                    <p>CineVote - Festival de Cinéma 2026</p>
                    <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        try {
            $mail = new Mail();
            $mail->setFrom($this->FROMEmail, $this->FROMName);
            $mail->setSubject($subject);
            $mail->addTo($email, $prenom);
            $mail->addContent("text/html", $htmlContent);
            
            $sendgrid = new SendGrid($this->sendgridApiKey);
            $response = $sendgrid->send($mail);
            
            $statusCode = $response->statusCode();
            
            if ($statusCode >= 200 && $statusCode < 300) {
                error_log("[SUCCESS] Email de réinitialisation envoyé à $email");
                return true;
            } else {
                error_log("[ERROR] Échec envoi email reset: " . $response->statusCode());
                return false;
            }
        } catch (Exception $e) {
            error_log("[EXCEPTION] Erreur SendGrid: " . $e->getMessage());
            return false;
        }
    }

}
