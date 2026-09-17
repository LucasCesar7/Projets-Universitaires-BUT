<?php

use App\Controllers\AdminPageController;

require_once __DIR__ . '/../../../vendor/autoload.php';

(new AdminPageController())->deleteNomination();
