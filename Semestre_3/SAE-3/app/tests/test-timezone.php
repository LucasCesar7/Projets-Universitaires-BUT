<?php
require_once dirname(__DIR__) .'/config/config.php';
require_once dirname(__DIR__) .'/models/Configuration.php';

// Test timezone PHP
echo "Timezone PHP : " . date_default_timezone_get() . "<br>";
echo "Date PHP actuelle : " . date('Y-m-d H:i:s') . "<br><br>";

// Test timezone MySQL
$pdo = dbconnect();
$stmt = $pdo->query("SELECT NOW() as mysql_now, @@session.time_zone as mysql_tz");
$result = $stmt->fetch();
echo "Timezone MySQL : " . $result['mysql_tz'] . "<br>";
echo "Date MySQL actuelle : " . $result['mysql_now'] . "<br><br>";

// Test récupération config
$configModel = new Configuration();
$date = $configModel->getConfig('periode_ajout_debut');
echo "Date brute BDD : " . $date . "<br>";

// Test formatage
$dateObj = new DateTime($date);
echo "Date formatée : " . $dateObj->format('Y-m-d\TH:i') . "<br>";
?>
