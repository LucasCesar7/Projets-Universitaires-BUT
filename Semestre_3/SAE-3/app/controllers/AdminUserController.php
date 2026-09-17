<?php
/*
* controller pour la gestion des utilisateurs par l'administrateur
*/
class AdminUserController
{
    // contient les informations des electeurs
    private $electeurModel;
    // contient les informations des candidats
    private $candidatModel;


    public function __construct($pdo)
    {
        $this->electeurModel = new ElecteurModel($pdo);
        $this->candidatModel = new CandidatModel($pdo);
    }

    public function index()
    {
        $electeurs = $this->electeurModel->getAllElecteurs();
        $candidats = $this->candidatModel->getAllCandidats();

        return [
            'electeurs' => $electeurs,
            'candidats' => $candidats
        ];
    }

    // ajout d'un utilisateur 
    public function addUser($pdo, $data)
    {
        if ($data['role'] === 'electeur') {
            return ElecteurModel::addElecteur($pdo, $data);
        } elseif ($data['role'] === 'candidat') {
            return CandidatModel::addCandidat($pdo, $data);
        }
    }

    // suppression d'un utilisateur 
    public function deleteUser($pdo, $id, $role)
    {
        if ($role === 'electeur') {
            return ElecteurModel::deleteElecteur($pdo, $id);
        } elseif ($role === 'candidat') {
            return CandidatModel::deleteCandidat($pdo, $id);
        }
    }
}
