<?php

declare(strict_types=1);

use App\Controllers\FrontController;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$routes = require appPath('config/routes.php');

(new FrontController($routes, appRootPath()))->dispatch();