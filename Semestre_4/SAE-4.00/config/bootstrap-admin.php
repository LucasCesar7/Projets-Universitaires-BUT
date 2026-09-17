<?php

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\CategorieService;
use App\Services\EditionService;
use App\Services\CandidatureService;
use App\Services\NominationService;

use App\Controllers\AdminCategorieController;
use App\Controllers\AdminEditionController;
use App\Controllers\AdminCandidatureController;
use App\Controllers\NominationController;



try {
    $pdo = Database::getInstance()->getConnection();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
}

// Services
$categoryService    = new CategorieService($pdo);
$editionService     = new EditionService($pdo);
$candidatureService = new CandidatureService($pdo);
$nominationService  = new NominationService($pdo);

// Controllers
$categoryController    = new AdminCategorieController($categoryService);
$editionController     = new AdminEditionController($pdo, $editionService);
$candidatureController = new AdminCandidatureController($candidatureService);
$nominationController  = new NominationController($pdo, $nominationService); 