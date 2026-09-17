<?php

use App\Controllers\VoterPageController;

require_once __DIR__ . '/../../vendor/autoload.php';

echo (new VoterPageController())->dashboard();