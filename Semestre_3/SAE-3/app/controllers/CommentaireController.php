<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/Commentaire.php';
require_once dirname(__DIR__) .  '/helpers/CsrfHelper.php';
require_once dirname(__DIR__)  . '/helpers/AuditLogger.php';

/*
* Gestion des commentaires
*/
class CommentaireController {
    
    private $commentaireModel;
    private $audit;
    
    public function __construct() {
        $this->commentaireModel = new Commentaire();
        $this->audit = new AuditLogger();
    }
    
    /**
     * Ajouter un commentaire
     */
    public function ajouterCommentaire() {
        $errors = [];
        if (!CsrfHelper::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Token de sécurité invalide.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        
        if (!isset($_SESSION['user_id'])) {
            $errors[] = "Vous devez être connecté pour commenter.";
            $this->audit->log(
                'COMMENT_UNAUTHORIZED',
                null,
                null,
                null,
                null,
                "Tentative d'ajout de commentaire sans authentification"
            );
            return ['success' => false, 'errors' => $errors];
        }
        
        $contenu = trim($_POST['contenu'] ?? '');
        $cibleId = (int)($_POST['cible_id'] ?? 0);
        $cibleType = $_POST['cible_type'] ?? '';
        
        // Validation
        if (empty($contenu)) {
            $errors[] = "Le commentaire ne peut pas être vide.";
        }
        
        if (strlen($contenu) < 3) {
            $errors[] = "Le commentaire doit contenir au moins 3 caractères.";
        }
        
        if (strlen($contenu) > 1000) {
            $errors[] = "Le commentaire ne peut pas dépasser 1000 caractères.";
        }
        
        if ($cibleId <= 0) {
            $errors[] = "Cible invalide.";
        }
        
        if (!in_array($cibleType, ['film', 'acteur', 'realisateur'])) {
            $errors[] = "Type de cible invalide.";
        }
        
        if (empty($errors)) {
            // Gérer le pseudo
            $pseudo = $_SESSION['pseudo'] ?? '';
            
            if (empty($pseudo) && isset($_SESSION['prenom']) && isset($_SESSION['nom'])) {
                $pseudo = $_SESSION['prenom'] . ' ' . $_SESSION['nom'];
            }
            
            if (empty($pseudo)) {
                $pseudo = 'Utilisateur #' . $_SESSION['user_id'];
            }
            
            // Ajout avec modération IA
            if ($this->commentaireModel->addCommentaire($pseudo, $contenu, $cibleId, $cibleType)) {
                $message = $_SESSION['moderation_message'] ?? 'Commentaire ajouté avec succès !';

                $this->audit->log(
                    'COMMENT_ADD',
                    'commentaire',
                    $this->commentaireModel->getLastInsertId(), // ID du commentaire
                    null,
                    [
                        'pseudo' => $pseudo,
                        'cible_type' => $cibleType,
                        'cible_id' => $cibleId,
                        'contenu_length' => strlen($contenu)
                    ],
                    "Commentaire ajouté sur $cibleType #$cibleId"
                );
                return ['success' => true, 'message' => $message];
            } else {
                $this->audit->log(
                    'COMMENT_ADD_FAILED',
                    'commentaire',
                    null,
                    null,
                    [
                        'cible_type' => $cibleType,
                        'cible_id' => $cibleId
                    ],
                    "Échec de l'ajout de commentaire"
                );
                $errors[] = "Erreur lors de l'ajout du commentaire.";
            }
        }
        
        return ['success' => false, 'errors' => $errors];
    }
    
    public function supprimerCommentaire() {
        $errors = [];
        if (!CsrfHelper::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Token de sécurité invalide.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        
        // Vérifier que c'est un admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $errors[] = "Accès refusé.";
            return ['success' => false, 'errors' => $errors];
        }
        
        $commentaireId = (int)($_POST['commentaire_id'] ?? 0);
        $raison = trim($_POST['raison'] ?? 'Supprimé par un administrateur');
        
        if ($commentaireId <= 0) {
            $errors[] = "Commentaire invalide.";
        }
        
        if (empty($errors)) {
            $adminId = $_SESSION['user_id'];
            if ($this->commentaireModel->supprimerCommentaire($commentaireId, $adminId, $raison)) {
                return ['success' => true, 'message' => 'Commentaire supprimé avec succès.'];
            } else {
                $errors[] = "Erreur lors de la suppression du commentaire.";
            }
        }
        
        return ['success' => false, 'errors' => $errors];
    }
    
    /**
     * Récupérer les commentaires
     */
    public function getCommentaires($cibleId, $cibleType) {
        return $this->commentaireModel->getCommentaires($cibleId, $cibleType);
    }
    
    /**
     * Compter les commentaires
     */
    public function countCommentaires($cibleId, $cibleType) {
        return $this->commentaireModel->countCommentaires($cibleId, $cibleType);
    }
}

// TRAITEMENT DES REQUETES POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CsrfHelper::validateToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token de sécurité invalide.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    $controller = new CommentaireController();
    
    // Ajouter un commentaire
    if (isset($_POST['action']) && $_POST['action'] === 'add_commentaire') {
        $result = $controller->ajouterCommentaire();
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['errors'] = $result['errors'];
        }
        
        // Redirection
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            header('Location: /sae-3-festivote-tas-cesar/public/index.php');
        }
        exit;
    }
    
    // Supprimer un commentaire
    if (isset($_POST['action']) && $_POST['action'] === 'delete_commentaire') {
        $result = $controller->supprimerCommentaire();
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['errors'] = $result['errors'];
        }
        
        // Redirection
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            header('Location: /sae-3-festivote-tas-cesar/public/index.php');
        }
        exit;
    }
}
?>
