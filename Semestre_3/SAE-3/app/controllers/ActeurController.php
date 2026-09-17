<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Acteur.php';
require_once __DIR__ . '/../helpers/CsrfHelper.php';

/*
 * traite les informations reçus pour les acteurs
 */
class ActeurController
{
    private $acteurModel;


    public function __construct()
    {
        $this->acteurModel = new Acteur();
    }

    // Afficher le formulaire
    public function afficherFormulaire()
    {
        require __DIR__ . '/../views/admin/add-acteur.php';
    }

    // Traiter le POST
    public function traiterPropositionActeur()
    {
        $errors = [];
        $success = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !CsrfHelper::validateToken($_POST['csrf_token'])) {
                $_SESSION['error'] = "Token de sécurité invalide.";
                header('Location: /sae-3-festivote-tas-cesar/app/views/pages/acteurs.php');
                exit;
            }
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $nationalite = trim($_POST['nationalite'] ?? '');
            $date_naissance = $_POST['date_naissance'] ?? null;
            $url_photo = null;

            if (empty($nom)) $errors[] = "Le nom est requis.";
            if (empty($prenom)) $errors[] = "Le prénom est requis.";
            if (empty($nationalite)) $errors[] = "La nationalité est requise.";

            // Upload photo
            if (!empty($_FILES['photo']['name'])) {
                $file = $_FILES['photo'];
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/sae-3-festivote-tas-cesar/public/uploads/acteurs/";

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $filename = "acteur_" . uniqid() . "." . $ext;
                $filepath = $upload_dir . $filename;

                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $url_photo = "/sae-3-festivote-tas-cesar/public/uploads/acteurs/" . $filename;
                } else {
                    $errors[] = "Erreur lors du téléversement du fichier.";
                }
            }

            if (empty($errors)) {
                try {
                    $pdo = dbconnect();
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $candidat_id = ($_SESSION['role'] === 'candidat') ? $_SESSION['user_id'] : null;

                    $stmt = $pdo->prepare("INSERT INTO acteur 
                    (nom, prenom, nationalite, date_naissance, url_photo, statut_validation, candidat_id_candidat)
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nom, $prenom, $nationalite, $date_naissance, $url_photo, 'EN_ATTENTE', $candidat_id]);

                    $_SESSION['success'] = "acteur ajouté avec succès.";
                    header("Location: /sae-3-festivote-tas-cesar/app/views/pages/acteurs.php");
                    exit;
                } catch (PDOException $e) {
                    $errors[] = "Erreur lors de l'ajout de l'acteur: " . $e->getMessage();
                }
            }
        }

        require __DIR__ . '/../views/admin/add-acteur.php';
    }
}

$controller = new ActeurController();

$action = $_GET['action'] ?? null;

if ($action === 'add') {
    $controller->traiterPropositionActeur();
} elseif ($action === 'form') {
    $controller->afficherFormulaire();
} else {
    echo "Action non reconnue.";
}
