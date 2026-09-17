<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Film.php';
require_once __DIR__ . '/../helpers/CsrfHelper.php';

/*
 * traite les informations reçus pour les films
 */
class FilmController
{
    private $filmModel;


    public function __construct()
    {
        $this->filmModel = new Film();
    }

    // Afficher le formulaire
    public function afficherFormulaire()
    {
        require __DIR__ . '/../views/admin/add-film.php';
    }

    // Traiter le POST
    public function traiterProposition()
    {
        $errors = [];
        $success = "";
        $rootPath = dirname(dirname(__DIR__, 2));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !CsrfHelper::validateToken($_POST['csrf_token'])) {
                $_SESSION['error'] = "Token de sécurité invalide.";
                header("Location: /sae-3-festivote-tas-cesar/app/views/pages/films.php");
                exit;
            }
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $annee = intval($_POST['annee'] ?? 0);
            $duree = intval($_POST['duree'] ?? 0);
            $categories_selected = $_POST['categories'] ?? [];
            $acteurs_selected = $_POST['acteurs'] ?? [];
            $realisateurs_selected = $_POST['realisateurs'] ?? [];
            $url_affiche = '';

            if (empty($titre)) $errors[] = "Le titre est requis.";
            if (empty($description)) $errors[] = "La description est requise.";
            if ($annee < 1900 || $annee > 2100) $errors[] = "L'année doit être entre 1900 et 2100.";
            if ($duree <= 0) $errors[] = "La durée doit être supérieure à 0.";
            if (empty($categories_selected)) $errors[] = "Veuillez sélectionner au moins un genre.";

            if (!empty($_FILES['affiche']['name'])) {

                $file = $_FILES['affiche'];

                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/sae-3-festivote-tas-cesar/public/uploads/affiches/";

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $file_type = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

                if (!in_array($file_type, $allowed_types)) {
                    $errors[] = "Type de fichier non valide.";
                }

                if ($file['size'] > 5000000) {
                    $errors[] = "Image trop lourde (max 5 Mo).";
                }

                if (empty($errors)) {

                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $filename = "affiche_" . uniqid() . "." . $ext;

                    $filepath = $upload_dir . $filename;

                    if (move_uploaded_file($file['tmp_name'], $filepath)) {

                        $url_affiche = "/sae-3-festivote-tas-cesar/public/uploads/affiches/" . $filename;
                    } else {
                        $errors[] = "Erreur lors du téléversement du fichier.";
                    }
                }
            }


            if (empty($errors)) {
                try {
                    $pdo = dbconnect();

                    $candidat_id = ($_SESSION['role'] === 'candidat') ? $_SESSION['user_id'] : null;

                    $stmt = $pdo->prepare("INSERT INTO Film (titre, description, annee, url_affiche, duree, candidat_id_candidat) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$titre, $description, $annee, $url_affiche, $duree, $candidat_id]);

                    $filmId = $pdo->lastInsertId();

                    $stmt = $pdo->prepare("INSERT INTO film_categorie (film_id_film, categorie_id_categorie) VALUES (?, ?)");
                    foreach ($categories_selected as $cat_id) {
                        $stmt->execute([$filmId, intval($cat_id)]);
                    }

                    if (!empty($acteurs_selected)) {
                        $stmt = $pdo->prepare("INSERT INTO acteur_film (acteur_id_acteur, film_id_film) VALUES (?, ?)");
                        foreach ($acteurs_selected as $acteur_id) {
                            $stmt->execute([$acteur_id, $filmId]);
                        }
                    }

                    if (!empty($realisateurs_selected)) {
                        $stmt = $pdo->prepare("INSERT INTO film_realisateur (film_id_film, realisateur_id_realisateur) VALUES (?, ?)");
                        foreach ($realisateurs_selected as $real_id) {
                            $stmt->execute([$filmId, $real_id]);
                        }
                    }

                    $success = "Film ajouté avec succès.";
                    echo "<script>
                setTimeout(function() {
                    window.location.href = '/sae-3-festivote-tas-cesar/app/views/pages/films.php';
                }, 2000);
            </script>";
                } catch (PDOException $e) {
                    $errors[] = "Erreur lors de l'ajout du film: " . $e->getMessage();
                }
            }
        }

        $_SESSION['success'] = "Film ajouté avec succès.";
        header("Location: /sae-3-festivote-tas-cesar/app/views/pages/films.php");
        exit;
    }
}

$controller = new FilmController();

$action = $_GET['action'] ?? null;

if ($action === 'add') {
    $controller->traiterProposition();
} elseif ($action === 'form') {
    $controller->afficherFormulaire();
} else {
    echo "Action non reconnue.";
}
