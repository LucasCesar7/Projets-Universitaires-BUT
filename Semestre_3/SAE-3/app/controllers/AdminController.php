<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Film.php';
require_once __DIR__ . '/../models/Acteur.php';
require_once __DIR__ . '/../models/Realisateur.php';

/*
* Gère les actions de l'administrateur
*/
class AdminController
{
    // Pour la gestion des films
    private $filmModel;
    // Pour la gestion des acteurs
    private $acteurModel;
    // Pour la gestion des réalisateurs
    private $realisateurModel;

    public function __construct()
    {
        $this->filmModel = new Film();
        $this->acteurModel = new Acteur();
        $this->realisateurModel = new Realisateur();
    }

    //renvoie la vue du dashboard par son chemin
    public function dashboard()
    {
        include __DIR__ . '/../views/admin/dashboard.php';
    }

    //renvoie la vue de gestion des films avec ceux en attente
    public function gestionFilms()
    {
        $films = $this->filmModel->getPropositionsEnAttente();
        include __DIR__ . '/../views/admin/GestionnaireFilm.php';
    }

    //renvoie la vue de gestion des acteurs avec ceux en attente
    public function gestionActeurs()
    {
        $acteurs = $this->acteurModel->getPropositionsActeursEnAttente();
        include __DIR__ . '/../views/admin/GestionnaireActeur.php';
    }

    //renvoie la vue de gestion des réalisateurs avec ceux en attente
    public function gestionRealisateurs()
    {
        $realisateurs = $this->realisateurModel->getPropositionsRealisateursEnAttente();
        include __DIR__ . '/../views/admin/GestionnaireRealisateur.php';
    }

    //permet la validation d'un film en attente
    public function validerFilm($filmId)
    {
        $adminId = $_SESSION['id_administrateur'];

        $this->filmModel->updateValidationStatus($filmId, $adminId, 'VALIDE', null);

        header("Location: AdminController.php?action=gestionFilms");
        exit;
    }

    //permet la validation d'un acteur en attente
    public function validerActeur($acteurId)
    {
        $adminId = $_SESSION['id_administrateur'];

        $this->acteurModel->updateValidationStatus($acteurId, $adminId, 'VALIDE', null);

        header("Location: AdminController.php?action=gestionActeurs");
        exit;
    }

    //permet la validation d'un réalisateur en attente
    public function validerRealisateur($realisateurId)
    {
        $adminId = $_SESSION['id_administrateur'];

        $this->realisateurModel->updateValidationStatus($realisateurId, $adminId, 'VALIDE', null);

        header("Location: AdminController.php?action=gestionRealisateurs");
        exit;
    }

    //permet de refuser un film en attente
    public function refuserFilm($filmId, $motif)
    {
        $adminId = $_SESSION['id_administrateur'];

        $this->filmModel->updateValidationStatus($filmId, $adminId, 'REFUSE', $motif);

        header("Location: AdminController.php?action=gestionFilms");
        exit;
    }

    //permet de refuser un acteur en attente
    public function refuserActeur($acteurId, $motif)
    {
        $adminId = $_SESSION['id_administrateur'];

        $this->acteurModel->updateValidationStatus($acteurId, $adminId, 'REFUSE', $motif);

        header("Location: AdminController.php?action=gestionActeurs");
        exit;
    }

    //permet de refuser un réalisateur en attente
    public function refuserRealisateur($realisateurId, $motif)
    {
        $adminId = $_SESSION['id_administrateur'];

        $this->realisateurModel->updateValidationStatus($realisateurId, $adminId, 'REFUSE', $motif);

        header("Location: AdminController.php?action=gestionRealisateurs");
        exit;
    }
}

$controller = new AdminController();
$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'dashboard':
        $controller->dashboard();
        break;

    case 'gestionFilms':
        $controller->gestionFilms();
        break;

    case 'gestionActeurs':
        $controller->gestionActeurs();
        break;

    case 'gestionRealisateurs':
        $controller->gestionRealisateurs();
        break;

    case 'validerFilm':
        if (isset($_GET['id'])) {
            $controller->validerFilm((int)$_GET['id']);
        }
        break;

    case 'refuserFilm':
        if (isset($_GET['id'])) {
            $controller->refuserFilm((int)$_GET['id'], $_POST['motif'] ?? '');
        }
        break;

    case 'validerActeur':
        if (isset($_GET['id'])) {
            $controller->validerActeur((int)$_GET['id']);
        }
        break;

    case 'refuserActeur':
        if (isset($_GET['id'])) {
            $controller->refuserActeur((int)$_GET['id'], $_POST['motif'] ?? '');
        }
        break;

    case 'validerRealisateur':
        if (isset($_GET['id'])) {
            $controller->validerRealisateur((int)$_GET['id']);
        }
        break;
    case 'refuserRealisateur':
        if (isset($_GET['id'])) {
            $controller->refuserRealisateur((int)$_GET['id'], $_POST['motif'] ?? '');
        }
        break;

    default:
        echo "Action non reconnue.";
}
