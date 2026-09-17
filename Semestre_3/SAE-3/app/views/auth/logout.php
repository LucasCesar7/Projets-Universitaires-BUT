<?php
session_start();

require_once __DIR__ . '/../../controllers/AuthController.php';

$authController = new AuthController();
$authController->logout();
