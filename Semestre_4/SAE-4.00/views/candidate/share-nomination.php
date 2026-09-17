<?php

use App\Controllers\CandidatePageController;

require_once __DIR__ . '/../../vendor/autoload.php';

echo (new CandidatePageController())->share();
