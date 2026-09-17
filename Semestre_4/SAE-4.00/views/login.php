<?php declare(strict_types=1);

use App\Controllers\AuthPageController;

require_once __DIR__ . '/../vendor/autoload.php';

echo (new AuthPageController())->login();