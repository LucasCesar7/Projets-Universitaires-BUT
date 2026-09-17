<?php

require_once dirname(__DIR__) . '/helpers/ModerationHelper.php';

/*
* Definition d'un commentaire
*/
class Commentaire {
    // connection a la BDD
    private $pdo;
    // helper de moderation
    private $moderationHelper;
    
    public function __construct() {
        require_once dirname(__DIR__) . '/config/config.php';
        $this->pdo = dbconnect();
        $this->moderationHelper = new ModerationHelper();
    }
    
    /**
     * Ajouter un commentaire avec modération automatique
     */
    public function addCommentaire($pseudo, $contenu, $cibleId, $cibleType) {
        try {
            $moderationResult = $this->moderationHelper->moderateContent($contenu);
            
            $status = 'PENDING';
            $isAiModerated = 1;
            
            if ($moderationResult['is_safe']) {
                // Contenu bon
                $status = 'AUTO_APPROVED';
            } elseif ($this->moderationHelper->shouldAutoReject($moderationResult)) {
                $status = 'REJECTED';
            } else {
                // Contenu suspect
                $status = 'PENDING';
            }
            
            // Insertion en BDD
            $stmt = $this->pdo->prepare("
                INSERT INTO commentaire (
                    pseudo, contenu, cible_id, cible_type,
                    moderation_status, moderation_score, 
                    moderation_date, is_ai_moderated
                ) VALUES (
                    :pseudo, :contenu, :cibleId, :cibleType,
                    :status, :score, NOW(), :isAiModerated
                )
            ");
            
            $stmt->execute([
                'pseudo' => $pseudo,
                'contenu' => $contenu,
                'cibleId' => $cibleId,
                'cibleType' => $cibleType,
                'status' => $status,
                'score' => json_encode($moderationResult),
                'isAiModerated' => $isAiModerated
            ]);
            
            $commentId = $this->pdo->lastInsertId();
            
            $report = $this->moderationHelper->getModerationReport($moderationResult);
            error_log("Commentaire #$commentId - Statut: $status - $report");
            
            // Message pour l'utilisateur
            $_SESSION['moderation_status'] = $status;
            $_SESSION['moderation_message'] = $this->getStatusMessage($status);
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Erreur ajout commentaire: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer tous les commentaires APPROUVÉS (publics)
     */
    public function getCommentaires($cibleId, $cibleType) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM commentaire 
                WHERE cible_id = :cibleId 
                AND cible_type = :cibleType 
                AND moderation_status IN ('AUTO_APPROVED', 'APPROVED')
                AND admin_moderateur_id IS NULL
                ORDER BY date_publication DESC
            ");
            
            $stmt->execute([
                'cibleId' => $cibleId,
                'cibleType' => $cibleType
            ]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Erreur récupération commentaires: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Compter les commentaires approuvés
     */
    public function countCommentaires($cibleId, $cibleType) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as total FROM commentaire 
                WHERE cible_id = :cibleId 
                AND cible_type = :cibleType 
                AND moderation_status IN ('AUTO_APPROVED', 'APPROVED')
                AND admin_moderateur_id IS NULL
            ");
            
            $stmt->execute([
                'cibleId' => $cibleId,
                'cibleType' => $cibleType
            ]);
            
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Erreur comptage commentaires: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Supprimer (marquer comme supprimé) un commentaire par un admin
     */
    public function supprimerCommentaire($commentaireId, $adminId, $raison) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE commentaire 
                SET admin_moderateur_id = :adminId,
                    date_moderation = NOW(),
                    raison_suppression = :raison,
                    moderation_status = 'REJECTED'
                WHERE id_commentaire = :commentaireId
            ");
            
            return $stmt->execute([
                'commentaireId' => $commentaireId,
                'adminId' => $adminId,
                'raison' => $raison
            ]);
            
        } catch (PDOException $e) {
            error_log("Erreur suppression commentaire: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer les commentaires en attente de modération (pour admin)
     */
    public function getPendingComments() {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    c.*,
                    CASE 
                        WHEN c.cible_type = 'film' THEN f.titre
                        WHEN c.cible_type = 'acteur' THEN CONCAT(a.prenom, ' ', a.nom)
                        WHEN c.cible_type = 'realisateur' THEN CONCAT(r.prenom, ' ', r.nom)
                    END as cible_nom
                FROM commentaire c
                LEFT JOIN film f ON c.cible_type = 'film' AND c.cible_id = f.id_film
                LEFT JOIN acteur a ON c.cible_type = 'acteur' AND c.cible_id = a.id_acteur
                LEFT JOIN realisateur r ON c.cible_type = 'realisateur' AND c.cible_id = r.id_realisateur
                WHERE c.moderation_status = 'PENDING'
                ORDER BY c.date_publication DESC
            ");
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Erreur récupération commentaires en attente: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Approuver manuellement un commentaire (admin)
     */
    public function approveComment($commentId, $adminId) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE commentaire 
                SET moderation_status = 'APPROVED',
                    admin_moderateur_id = :adminId,
                    date_moderation = NOW()
                WHERE id_commentaire = :commentId
            ");
            
            return $stmt->execute([
                'commentId' => $commentId,
                'adminId' => $adminId
            ]);
            
        } catch (PDOException $e) {
            error_log("Erreur approbation commentaire: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Rejeter manuellement un commentaire (admin)
     */
    public function rejectComment($commentId, $adminId, $raison) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE commentaire 
                SET moderation_status = 'REJECTED',
                    admin_moderateur_id = :adminId,
                    date_moderation = NOW(),
                    raison_suppression = :raison
                WHERE id_commentaire = :commentId
            ");
            
            return $stmt->execute([
                'commentId' => $commentId,
                'adminId' => $adminId,
                'raison' => $raison
            ]);
            
        } catch (PDOException $e) {
            error_log("Erreur rejet commentaire: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Messages utilisateur selon le statut de modération
     */
    private function getStatusMessage($status) {
        $messages = [
            'AUTO_APPROVED' => 'Votre commentaire a été publié avec succès !',
            'PENDING' => 'Votre commentaire est en attente de validation par un modérateur.',
            'REJECTED' => 'Votre commentaire a été refusé car il ne respecte pas nos règles de bienséance.'
        ];
        
        return $messages[$status] ?? 'Commentaire enregistré';
    }

    /**
     * Récupérer le dernier ID inséré
     */
    public function getLastInsertId() {
        return $this->pdo->lastInsertId();
    }
}
?>
