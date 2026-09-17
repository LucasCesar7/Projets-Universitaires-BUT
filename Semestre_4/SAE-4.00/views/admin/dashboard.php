<?php

use App\Controllers\AdminPageController;

require_once __DIR__ . '/../../vendor/autoload.php';

echo (new AdminPageController())->dashboard();