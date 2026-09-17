<?php

declare(strict_types=1);

$rootPath = dirname(__DIR__);
$assetDirs = [
    'css' => $rootPath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css',
    'js' => $rootPath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js',
];

$written = 0;

foreach ($assetDirs as $type => $directory) {
    if (!is_dir($directory)) {
        fwrite(STDERR, "Dossier introuvable: {$directory}" . PHP_EOL);
        exit(1);
    }

    $files = glob($directory . DIRECTORY_SEPARATOR . '*.'.$type) ?: [];

    foreach ($files as $sourcePath) {
        if (str_ends_with($sourcePath, '.min.' . $type)) {
            continue;
        }

        $contents = file_get_contents($sourcePath);

        if ($contents === false) {
            fwrite(STDERR, "Lecture impossible: {$sourcePath}" . PHP_EOL);
            exit(1);
        }

        $minified = $type === 'css' ? minifyCss($contents) : minifyJs($contents);
        $targetPath = preg_replace('/\.' . preg_quote($type, '/') . '$/', '.min.' . $type, $sourcePath);

        if (!is_string($targetPath)) {
            fwrite(STDERR, "Chemin cible invalide: {$sourcePath}" . PHP_EOL);
            exit(1);
        }

        if (file_put_contents($targetPath, $minified) === false) {
            fwrite(STDERR, "Ecriture impossible: {$targetPath}" . PHP_EOL);
            exit(1);
        }

        $written++;
    }
}

echo "Assets minifies generes: {$written}" . PHP_EOL;

function minifyCss(string $contents): string
{
    $contents = preg_replace('#/\*.*?\*/#s', '', $contents) ?? $contents;
    $contents = preg_replace('/\s+/', ' ', $contents) ?? $contents;
    $contents = preg_replace('/\s*([{}:;,>+~])\s*/', '$1', $contents) ?? $contents;
    $contents = str_replace(';}', '}', $contents);

    return trim($contents) . PHP_EOL;
}

function minifyJs(string $contents): string
{
    $contents = preg_replace('#/\*(?!\!).*?\*/#s', '', $contents) ?? $contents;
    $lines = preg_split('/\R/u', $contents) ?: [];
    $minifiedLines = [];

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '//')) {
            continue;
        }

        $line = preg_replace('/[ \t]+/', ' ', $line) ?? $line;
        $minifiedLines[] = $line;
    }

    return implode("\n", $minifiedLines) . PHP_EOL;
}