<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /sae-3-festivote-tas-cesar/app/views/public/login.php");
    exit;
}

$rootPath = dirname(dirname(dirname(__FILE__)));

require_once $rootPath . '/app/config/config.php';
require_once $rootPath . '/app/models/ElecteurModel.php';
require_once $rootPath . '/app/models/CandidatModel.php';
require_once $rootPath . '/app/controllers/AdminUserController.php';
require_once $rootPath . '/app/helpers/CsrfHelper.php';


// Connexion
$pdo = dbconnect();
$adminController = new AdminUserController($pdo);

$data = $adminController->index();

$electeurs = $data['electeurs'];
$candidats = $data['candidats'];

// GESTION DES ACTIONS CRUD
if (isset($_GET['action'])) {

    switch ($_GET['action']) {

        case 'deleteElecteur':
            if (isset($_POST['id'])) {
                $adminController->deleteUser($pdo, $_POST['id'], 'electeur');
            }
            header("Location: GestionnaireUtilisateurController.php");
            exit;

        case 'deleteCandidat':
            if (isset($_POST['id'])) {
                $adminController->deleteUser($pdo, $_POST['id'], 'candidat');
            }
            header("Location: GestionnaireUtilisateurController.php");
            exit;

        case 'addElecteur':
            if ($_POST['mot_de_passe'] !== $_POST['mot_de_passe_confirm']) {
                die("Les mots de passe ne correspondent pas.");
            }

            $data = [
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
                'email' => $_POST['email'],
                'pseudo' => $_POST['pseudo'],
                'date_naissance' => $_POST['date_naissance'],
                'date_inscription' => date('Y-m-d'),
                'accepte_conditions' => 1,
                'mot_de_passe' => password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT),
                'role' => 'electeur'
            ];

            $adminController->addUser($pdo, $data);
            header("Location: GestionnaireUtilisateurController.php");
            exit;

        case 'addCandidat':
            $data = [
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
                'email' => $_POST['email'],
                'pseudo' => $_POST['pseudo'] ?? null,
                'date_inscription' => date('Y-m-d'),
                'accepte_conditions' => 1,
                'mot_de_passe' => password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT),
                'role' => 'candidat'
            ];

            $adminController->addUser($pdo, $data);
            header("Location: GestionnaireUtilisateurController.php");
            exit;
    }
}

$electeursModel = new ElecteurModel($pdo);
$electeurs = $electeursModel->getAllElecteurs();

$candidatsModel = new CandidatModel($pdo);
$candidats = $candidatsModel->getAllCandidats();

include $rootPath . '/app/views/admin/GestionnaireUtilisateur.php';
