<?php

declare(strict_types=1);

namespace App\Controllers;

final class FrontController
{
    /**
     * @param array<string, string|array{controller: class-string, action: string}> $routes
     */
    public function __construct(
        private array $routes,
        private string $rootPath
    ) {
        $this->rootPath = rtrim($this->rootPath, DIRECTORY_SEPARATOR);
    }

    public function dispatch(?string $requestUri = null): void
    {
        $target = $this->resolveTarget($requestUri ?? (string) ($_SERVER['REQUEST_URI'] ?? '/'));

        if ($target === null) {
            $this->renderNotFound();
            return;
        }

        if (is_string($target)) {
            require $target;
            return;
        }

        $this->dispatchController($target);
    }

    public function resolve(string $requestUri): ?string
    {
        $target = $this->resolveTarget($requestUri);

        return is_string($target) ? $target : null;
    }

    /**
     * @return array{controller: class-string, action: string}|null
     */
    public function resolveController(string $requestUri): ?array
    {
        $target = $this->resolveTarget($requestUri);

        return is_array($target) ? $target : null;
    }

    /**
     * @return string|array{controller: class-string, action: string}|null
     */
    private function resolveTarget(string $requestUri): string|array|null
    {
        $route = $this->normalizePath($requestUri);
        $relativeTarget = $this->routes[$route] ?? null;

        if (is_array($relativeTarget)) {
            return $this->isValidControllerTarget($relativeTarget) ? $relativeTarget : null;
        }

        if (!is_string($relativeTarget) || $relativeTarget === '') {
            return null;
        }

        $normalizedTarget = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativeTarget), DIRECTORY_SEPARATOR);
        $target = $this->rootPath . DIRECTORY_SEPARATOR . $normalizedTarget;

        return is_file($target) ? $target : null;
    }

    /**
     * @param array<mixed> $target
     */
    private function isValidControllerTarget(array $target): bool
    {
        return isset($target['controller'], $target['action'])
            && is_string($target['controller'])
            && is_string($target['action'])
            && $target['controller'] !== ''
            && $target['action'] !== '';
    }

    /**
     * @param array{controller: class-string, action: string} $target
     */
    private function dispatchController(array $target): void
    {
        $controllerClass = $target['controller'];
        $action = $target['action'];

        if (!class_exists($controllerClass) || !method_exists($controllerClass, $action)) {
            $this->renderNotFound();
            return;
        }

        $response = (new $controllerClass())->{$action}();

        if (is_string($response)) {
            echo $response;
        }
    }

    private function normalizePath(string $requestUri): string
    {
        $path = parse_url($requestUri, PHP_URL_PATH);

        if (!is_string($path) || $path === '') {
            $path = '/';
        }

        $path = str_replace('\\', '/', $path);
        $baseUrl = function_exists('appBaseUrl') ? rtrim(appBaseUrl(), '/') : '';

        if ($baseUrl !== '' && ($path === $baseUrl || str_starts_with($path, $baseUrl . '/'))) {
            $path = substr($path, strlen($baseUrl));
        }

        $path = '/' . trim($path, '/');

        return $path === '/' ? '/' : rtrim($path, '/');
    }

    private function renderNotFound(): void
    {
        if (!headers_sent()) {
            http_response_code(404);
            header('Content-Type: text/html; charset=UTF-8');
        }

        echo '<!doctype html><html lang="fr"><head><meta charset="UTF-8"><title>Page introuvable</title></head><body><h1>Page introuvable</h1></body></html>';
    }
}
