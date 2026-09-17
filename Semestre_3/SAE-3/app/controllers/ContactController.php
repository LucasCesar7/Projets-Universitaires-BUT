<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../helpers/EmailHelper.php';
require_once __DIR__ . '/../helpers/CsrfHelper.php';

/*
* Gere l'envoie de mail
*/
class ContactController {
    
    private $emailHelper;
    
    public function __construct() {
        $this->emailHelper = new EmailHelper();
    }
    
    /**
     * Traiter le formulaire de contact
     */
    public function envoyerMessage() {
        $errors = [];
      
        
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $sujet = trim($_POST['sujet'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        // Validation
        if (empty($nom)) {
            $errors[] = "Le nom est requis.";
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Un email valide est requis.";
        }
        
        if (empty($sujet)) {
            $errors[] = "Le sujet est requis.";
        }
        
        if (empty($message)) {
            $errors[] = "Le message est requis.";
        } elseif (strlen($message) < 10) {
            $errors[] = "Le message doit contenir au moins 10 caractères.";
        } elseif (strlen($message) > 2000) {
            $errors[] = "Le message ne peut pas dépasser 2000 caractères.";
        }
        
        if (empty($errors)) {
            try {
                $success = $this->emailHelper->sendContactEmail($nom, $email, $sujet, $message);
                
                if ($success) {
                    $_SESSION['success'] = "Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.";
                    header('Location: /sae-3-festivote-tas-cesar/app/views/pages/contact.php');
                    exit;
                } else {
                    $errors[] = "Une erreur est survenue lors de l'envoi de votre message. Veuillez réessayer.";
                }
            } catch (Exception $e) {
                error_log("Erreur envoi contact: " . $e->getMessage());
                $errors[] = "Une erreur technique est survenue. Veuillez réessayer plus tard.";
            }
        }
        
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST; // Garder les données saisies
        header('Location: /sae-3-festivote-tas-cesar/app/views/pages/contact.php');
        exit;
    }
}

// ===== TRAITEMENT DES REQUÊTES POST =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_contact') {
    if (!CsrfHelper::validateToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token de sécurité invalide.";
        header('Location: /sae-3-festivote-tas-cesar/app/views/pages/contact.php');
        exit;
    }
    $controller = new ContactController();
    $controller->envoyerMessage();
}
