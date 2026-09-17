<?php
require_once __DIR__ . '/../config/config.php';

$pdo = dbconnect();

$stmt = $pdo->query('SELECT * FROM candidat');
while ($row = $stmt->fetch()) {
    echo $row['nom'] . ' ' . $row['prenom'] . '<br>';
}
