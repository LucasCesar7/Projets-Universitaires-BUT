<?php
session_start();
require_once __DIR__ . '/../models/Configuration.php';
require_once __DIR__ . '/../helpers/CsrfHelper.php';

class ConfigurationController {
    private $configModel;
    
    public function __construct() {
        $this->configModel = new Configuration();
    }
    
    /**
     * Affiche la page de configuration
     */
    public function index() {
        // Vérifier que l'utilisateur est admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /sae-3-festivote-tas-cesar/public/index.php');
            exit;
        }
        
        $configs = $this->configModel->getAllConfigs();
        $statut = $this->configModel->getStatutSysteme();
        
        include dirname(dirname(__FILE__)) . '/views/admin/configuration.php';
    }
    
    /**
     * Met à jour les configurations
     */
    public function update() {
        
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /sae-3-festivote-tas-cesar/public/index.php');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /sae-3-festivote-tas-cesar/app/controllers/ConfigurationController.php');
            exit;
        }

        if (!CsrfHelper::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Token de sécurité invalide.";
            header('Location: /sae-3-festivote-tas-cesar/app/controllers/ConfigurationController.php');
            exit;
        }

        $adminId = $_SESSION['id_administrateur'] ?? null;
        $errors = [];

        // Récupérer les données du formulaire
        $periodeAjoutDebut = $_POST['periode_ajout_debut'] ?? '';
        $periodeAjoutFin = $_POST['periode_ajout_fin'] ?? '';
        $periodeVoteDebut = $_POST['periode_vote_debut'] ?? '';
        $periodeVoteFin = $_POST['periode_vote_fin'] ?? '';
        $systemeActif = isset($_POST['systeme_actif']) ? '1' : '0';

        // Validation basique PHP
        if (empty($periodeAjoutDebut) || empty($periodeAjoutFin) || 
            empty($periodeVoteDebut) || empty($periodeVoteFin)) {
            $errors[] = "Toutes les dates sont requises.";
        }

        if (empty($errors)) {
            try {
                // Le trigger validera automatiquement les dates
                $this->configModel->updateConfig('periode_ajout_debut', $periodeAjoutDebut, $adminId);
                $this->configModel->updateConfig('periode_ajout_fin', $periodeAjoutFin, $adminId);
                $this->configModel->updateConfig('periode_vote_debut', $periodeVoteDebut, $adminId);
                $this->configModel->updateConfig('periode_vote_fin', $periodeVoteFin, $adminId);
                $this->configModel->updateConfig('systeme_actif', $systemeActif, $adminId);

                $_SESSION['success'] = "Configuration mise à jour avec succès !";
                
            } catch (PDOException $e) {
                // Attraper les erreurs du trigger
                if (strpos($e->getMessage(), 'SQLSTATE[45000]') !== false) {
                    // Extraire le message d'erreur du trigger
                    preg_match('/SQLSTATE\[45000\]: (.+)/', $e->getMessage(), $matches);
                    $errorMsg = $matches[1] ?? $e->getMessage();
                    $_SESSION['errors'] = [$errorMsg];
                } else {
                    $_SESSION['errors'] = ["Erreur : " . $e->getMessage()];
                }
            }
        } else {
            $_SESSION['errors'] = $errors;
        }
        
        header('Location: /sae-3-festivote-tas-cesar/app/controllers/ConfigurationController.php');
        exit;
    }
}

// Routage
$action = $_GET['action'] ?? 'index';
$controller = new ConfigurationController();

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'update':
        $controller->update();
        break;
    default:
        $controller->index();
        break;
}
?>
