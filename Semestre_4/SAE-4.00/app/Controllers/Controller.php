<?php

declare(strict_types=1);

namespace App\Controllers;

use RuntimeException;

abstract class Controller
{
    /**
     * @param array<string, mixed> $data
     */
    protected function render(string $view, array $data = []): string
    {
        $viewPath = appPath('views/' . trim($view, '/\\') . '.php');

        if (!is_file($viewPath)) {
            throw new RuntimeException('Vue introuvable: ' . $view);
        }

        extract($data, EXTR_SKIP);

        // Enable GZIP compression if supported by client
        if (extension_loaded('zlib') && !headers_sent()) {
            ini_set('zlib.output_compression', 'On');
        }

        ob_start();
        require $viewPath;

        return (string) ob_get_clean();
    }
}