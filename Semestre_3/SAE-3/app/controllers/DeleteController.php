<?php
/*
* valide ou rejete la demande de suppresion d'elements candidat
*/
class DeleteController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Vérification admin
    private function checkAdmin() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté.";
            return false;
        }
        
        require_once __DIR__ . '/../models/User.php';
        $user = new User($this->pdo);
        
        if (!isset($_SESSION['role'])) {
            $_SESSION['error'] = "Session invalide.";
            return false;
        }
        
        $userData = $user->getUserInfo($_SESSION['user_id'], $_SESSION['role']);
        
        if (!$userData || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = "Accès refusé. Vous devez être administrateur.";
            return false;
        }
        
        return true;
    }
    
    // Vérification CSRF
    private function checkCSRF() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = "Token de sécurité invalide.";
            return false;
        }
        return true;
    }
    
    // Supprimer un film
    public function deleteFilm() {
        if (!$this->checkAdmin() || !$this->checkCSRF()) {
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'films.php');
            exit;
        }
        
        $film_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        
        if (!$film_id) {
            $_SESSION['error'] = "ID de film invalide.";
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'films.php');
            exit;
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT url_affiche, titre FROM film WHERE id_film = ?");
            $stmt->execute([$film_id]);
            $film = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$film) {
                $_SESSION['error'] = "Film introuvable.";
                header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'films.php');
                exit;
            }
            
            // Suppression du film (les relations seront supprimées automatiquement si ON DELETE CASCADE)
            $stmt = $this->pdo->prepare("DELETE FROM film WHERE id_film = ?");
            $stmt->execute([$film_id]);
            
            // Suppression de l'image si elle existe
            if (!empty($film['url_affiche'])) {
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . $film['url_affiche'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $_SESSION['success'] = "Le film \"{$film['titre']}\" a été supprimé avec succès.";
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
            error_log("Erreur suppression film: " . $e->getMessage());
        }
        
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'films.php');
        exit;
    }
    
    // Supprimer un acteur
    public function deleteActeur() {
        if (!$this->checkAdmin() || !$this->checkCSRF()) {
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'acteurs.php');
            exit;
        }
        
        $acteur_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        
        if (!$acteur_id) {
            $_SESSION['error'] = "ID d'acteur invalide.";
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'acteurs.php');
            exit;
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT url_photo, nom, prenom FROM acteur WHERE id_acteur = ?");
            $stmt->execute([$acteur_id]);
            $acteur = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$acteur) {
                $_SESSION['error'] = "Acteur introuvable.";
                header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'acteurs.php');
                exit;
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM acteur WHERE id_acteur = ?");
            $stmt->execute([$acteur_id]);
            
            if (!empty($acteur['url_photo'])) {
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . $acteur['url_photo'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $_SESSION['success'] = "L'acteur {$acteur['prenom']} {$acteur['nom']} a été supprimé avec succès.";
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
            error_log("Erreur suppression acteur: " . $e->getMessage());
        }
        
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'acteurs.php');
        exit;
    }
    
    // Supprimer un réalisateur
    public function deleteRealisateur() {
        if (!$this->checkAdmin() || !$this->checkCSRF()) {
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'realisateurs.php');
            exit;
        }
        
        $realisateur_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        
        if (!$realisateur_id) {
            $_SESSION['error'] = "ID de réalisateur invalide.";
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'realisateurs.php');
            exit;
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT url_photo, nom, prenom FROM realisateur WHERE id_realisateur = ?");
            $stmt->execute([$realisateur_id]);
            $realisateur = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$realisateur) {
                $_SESSION['error'] = "Réalisateur introuvable.";
                header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'realisateurs.php');
                exit;
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM realisateur WHERE id_realisateur = ?");
            $stmt->execute([$realisateur_id]);
            
            if (!empty($realisateur['url_photo'])) {
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . $realisateur['url_photo'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $_SESSION['success'] = "Le réalisateur {$realisateur['prenom']} {$realisateur['nom']} a été supprimé avec succès.";
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
            error_log("Erreur suppression réalisateur: " . $e->getMessage());
        }
        
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'realisateurs.php');
        exit;
    }
}
?>
