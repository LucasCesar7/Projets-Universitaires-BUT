<?php
session_start();

// RECUPERATION DU FICHIER
$file = $_GET['file'] ?? '';

if (empty($file)) {
    http_response_code(400);
    die('Fichier non spécifié');
}

// CHERCHER DANS PLUSIEURS DOSSIERS
$possiblePaths = [
    __DIR__ . '/uploads/' . $file,
    __DIR__ . '/img/' . $file,
    __DIR__ . '/' . $file
];

$fullPath = null;
foreach ($possiblePaths as $path) {
    if (file_exists($path) && is_file($path)) {
        $fullPath = $path;
        break;
    }
}

// VERIFIER QUE LE FICHIER EXISTE
if ($fullPath === null) {
    http_response_code(404);
    header('Content-Type: text/plain');
    die('Image introuvable: ' . htmlspecialchars($file));
}

// VERIFIER L'EXTENSION
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
$extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

if (!in_array($extension, $allowedExtensions)) {
    http_response_code(403);
    die('Type de fichier non autorisé');
}

// DEFINIR LES HEADERS
$mimeTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'svg' => 'image/svg+xml'
];

header('Content-Type: ' . ($mimeTypes[$extension] ?? 'image/jpeg'));
header('Content-Length: ' . filesize($fullPath));
header('Cache-Control: public, max-age=3600'); // Cache 1h pour les images
header('X-Content-Type-Options: nosniff');
header('Content-Disposition: inline; filename="' . basename($fullPath) . '"');

// STREAMER L'IMAGE
if (ob_get_level()) ob_end_clean();

readfile($fullPath);
exit;
?>
