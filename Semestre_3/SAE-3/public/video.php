<?php
session_start();


$file = $_GET['file'] ?? '';

if (empty($file)) {
    http_response_code(400);
    die('Fichier non spécifié');
}

$file = str_replace(['../', '..\\', './', '.\\'], '', $file);
$file = trim($file, '/\\');

$videoPath = __DIR__ . '/video/' . $file;

if (!file_exists($videoPath) || !is_file($videoPath)) {
    http_response_code(404);
    die('Vidéo introuvable');
}

$allowedExtensions = ['mp4', 'webm', 'ogg', 'mov'];
$extension = strtolower(pathinfo($videoPath, PATHINFO_EXTENSION));

if (!in_array($extension, $allowedExtensions)) {
    http_response_code(403);
    die('Type de fichier non autorisé');
}

$fileSize = filesize($videoPath);
$mimeTypes = [
    'mp4' => 'video/mp4',
    'webm' => 'video/webm',
    'ogg' => 'video/ogg',
    'mov' => 'video/quicktime'
];

$mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

$start = 0;
$end = $fileSize - 1;
$length = $fileSize;

// Support du header Range pour le streaming
if (isset($_SERVER['HTTP_RANGE'])) {
    $range = $_SERVER['HTTP_RANGE'];
    
    if (preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
        $start = intval($matches[1]);
        
        if (!empty($matches[2])) {
            $end = intval($matches[2]);
        }
        
        $length = $end - $start + 1;
        
        // Réponse 206 Partial Content
        http_response_code(206);
        header("Content-Range: bytes $start-$end/$fileSize");
    }
}

header("Content-Type: $mimeType");
header("Content-Length: $length");
header("Accept-Ranges: bytes");
header("Cache-Control: public, max-age=86400"); // Cache 24h
header("X-Content-Type-Options: nosniff");

// Headers de sécurité
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'");

if (ob_get_level()) ob_end_clean();

$handle = fopen($videoPath, 'rb');

if ($handle === false) {
    http_response_code(500);
    die('Erreur lecture fichier');
}

// Se positionner au bon endroit
fseek($handle, $start);

// Streamer
$bufferSize = 8192; // 8KB chunks
$bytesRemaining = $length;

while (!feof($handle) && $bytesRemaining > 0 && connection_status() == 0) {
    $bytesToRead = min($bufferSize, $bytesRemaining);
    echo fread($handle, $bytesToRead);
    flush();
    
    $bytesRemaining -= $bytesToRead;
}

fclose($handle);
exit;
?>
