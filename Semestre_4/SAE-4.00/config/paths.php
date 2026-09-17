<?php

function resolveBaseUrlFromServerPath(string $path): string
{
    $pathOnly = parse_url($path, PHP_URL_PATH);

    if (!is_string($pathOnly) || $pathOnly === '') {
        return '';
    }

    $scriptDirectory = str_replace('\\', '/', dirname($pathOnly));

    $scriptDirectory = preg_replace(
        '#(?:/public(?:/.*)?|/views(?:/.*)?|/config(?:/.*)?|/app/Controllers(?:/.*)?)$#',
        '',
        $scriptDirectory
    );

    if ($scriptDirectory === null) {
        return '';
    }

    $scriptDirectory = trim($scriptDirectory, '/.');

    return $scriptDirectory === '' ? '' : '/' . $scriptDirectory;
}

function appRootPath(): string
{
    return dirname(__DIR__);
}

function appPath(string $path = ''): string
{
    $normalizedPath = trim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);

    if ($normalizedPath === '') {
        return appRootPath();
    }

    return appRootPath() . DIRECTORY_SEPARATOR . $normalizedPath;
}

function appBaseUrl(): string
{
    static $baseUrl = null;

    if ($baseUrl !== null) {
        return $baseUrl;
    }

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

    if ($scriptName !== '') {
        $baseUrl = resolveBaseUrlFromServerPath($scriptName);
        return $baseUrl;
    }

    $requestUri = $_SERVER['REQUEST_URI'] ?? '';

    if ($requestUri !== '') {
        $baseUrl = resolveBaseUrlFromServerPath($requestUri);
        return $baseUrl;
    }

    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $projectRoot = realpath(appRootPath());

    if ($documentRoot !== false && $projectRoot !== false) {
        $documentRoot = str_replace('\\', '/', rtrim($documentRoot, DIRECTORY_SEPARATOR));
        $projectRoot = str_replace('\\', '/', rtrim($projectRoot, DIRECTORY_SEPARATOR));

        if (str_starts_with($projectRoot, $documentRoot)) {
            $relativePath = trim(substr($projectRoot, strlen($documentRoot)), '/');
            $baseUrl = $relativePath === '' ? '' : '/' . $relativePath;
            return $baseUrl;
        }
    }

    $baseUrl = '';

    return $baseUrl;
}

function resolveAppWebPath(string $path): string
{
    $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');

    if (!str_starts_with($normalizedPath, 'assets/')) {
        return $normalizedPath;
    }

    $extension = strtolower((string) pathinfo($normalizedPath, PATHINFO_EXTENSION));

    if (!in_array($extension, ['css', 'js'], true)) {
        return $normalizedPath;
    }

    $filename = (string) pathinfo($normalizedPath, PATHINFO_FILENAME);

    if (str_ends_with(strtolower($filename), '.min')) {
        return $normalizedPath;
    }

    $directory = str_replace('\\', '/', dirname($normalizedPath));
    $minifiedPath = ($directory === '.' ? '' : $directory . '/') . $filename . '.min.' . $extension;

    if (is_file(appPath($minifiedPath))) {
        return $minifiedPath;
    }

    return $normalizedPath;
}

function appUrl(string $path = ''): string
{
    $normalizedPath = resolveAppWebPath($path);

    if ($normalizedPath === '') {
        return appBaseUrl() !== '' ? appBaseUrl() : '/';
    }

    return appBaseUrl() . '/' . $normalizedPath;
}

function appUrlWithQuery(string $path = '', array $query = []): string
{
    $url = appUrl($path);
    $filteredQuery = array_filter(
        $query,
        static fn (mixed $value): bool => $value !== null && $value !== ''
    );

    if ($filteredQuery === []) {
        return $url;
    }

    $queryString = http_build_query($filteredQuery);

    if ($queryString === '') {
        return $url;
    }

    return $url . (str_contains($url, '?') ? '&' : '?') . $queryString;
}

function publicRouteUrl(string $routeName, array $query = []): string
{
    $routes = [
        'home' => '',
        'about' => 'about',
        'categories' => 'categories',
        'cgu' => 'cgu',
        'contact' => 'contact',
        'faq' => 'faq',
        'login' => 'login',
        'logout' => 'logout',
        'nominee' => 'nominee',
        'nominees' => 'nominees',
        'privacy' => 'privacy',
        'register' => 'inscription',
        'results' => 'results',
        'vote' => 'vote',
        'dashboard_redirect' => 'check_dashboards',
    ];

    return appUrlWithQuery($routes[$routeName] ?? ltrim($routeName, '/'), $query);
}

function publicPlatformUrl(string $platform, array $query = []): string
{
    $normalizedPlatform = strtolower(trim($platform));

    if ($normalizedPlatform !== '') {
        $query['platform'] = $normalizedPlatform;
    }

    return publicRouteUrl('nominees', $query);
}

/**
 * @return array<int, array{label: string, icon: string, url: string}>
 */
function publicSocialLinks(): array
{
    return [
        [
            'label' => 'Facebook',
            'icon' => 'fab fa-facebook-f',
            'url' => publicPlatformUrl('Facebook'),
        ],
        [
            'label' => 'Twitter',
            'icon' => 'fab fa-twitter',
            'url' => publicPlatformUrl('Twitter'),
        ],
        [
            'label' => 'Instagram',
            'icon' => 'fab fa-instagram',
            'url' => publicPlatformUrl('Instagram'),
        ],
        [
            'label' => 'LinkedIn',
            'icon' => 'fab fa-linkedin-in',
            'url' => publicPlatformUrl('LinkedIn'),
        ],
        [
            'label' => 'YouTube',
            'icon' => 'fab fa-youtube',
            'url' => publicPlatformUrl('YouTube'),
        ],
    ];
}

function appDocumentRootIsPublic(): bool
{
    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $publicRoot = realpath(appPath('public'));

    if ($documentRoot === false || $publicRoot === false) {
        return false;
    }

    return rtrim(str_replace('\\', '/', $documentRoot), '/') === rtrim(str_replace('\\', '/', $publicRoot), '/');
}

function sanitizeAppRedirect(?string $redirect, ?string $fallback = null): ?string
{
    if (!is_string($redirect)) {
        return $fallback;
    }

    $redirect = trim(urldecode($redirect));

    if ($redirect === '' || preg_match('/[\r\n]/', $redirect) === 1 || str_starts_with($redirect, '//')) {
        return $fallback;
    }

    $parts = parse_url($redirect);

    if ($parts === false || isset($parts['scheme']) || isset($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
        return $fallback;
    }

    $path = str_replace('\\', '/', $parts['path'] ?? '');

    if ($path === '') {
        return $fallback;
    }

    $segments = array_filter(explode('/', trim($path, '/')), static fn (string $segment): bool => $segment !== '');

    if (in_array('.', $segments, true) || in_array('..', $segments, true)) {
        return $fallback;
    }

    $query = isset($parts['query']) ? '?' . $parts['query'] : '';

    if (!str_starts_with($path, '/')) {
        return appUrl(ltrim($path, '/')) . $query;
    }

    $normalizedPath = '/' . ltrim($path, '/');
    $baseUrl = appBaseUrl();

    if ($baseUrl === '' || $normalizedPath === $baseUrl || str_starts_with($normalizedPath, $baseUrl . '/')) {
        return $normalizedPath . $query;
    }

    return $fallback;
}

function resolveAppAssetUrl(?string $path, ?string $defaultPath = null): ?string
{
    if (!empty($path) && filter_var($path, FILTER_VALIDATE_URL)) {
        return $path;
    }

    $candidates = [];

    if (!empty($path)) {
        $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');

        if ($normalizedPath !== '') {
            if (str_starts_with($normalizedPath, 'public/') || str_starts_with($normalizedPath, 'assets/')) {
                $candidates[] = $normalizedPath;
            } else {
                $candidates[] = $normalizedPath;
                $candidates[] = 'public/' . $normalizedPath;
            }
        }
    }

    foreach ($candidates as $candidate) {
        if (is_file(appPath($candidate))) {
            if (appDocumentRootIsPublic() && str_starts_with($candidate, 'public/')) {
                return appUrl(substr($candidate, strlen('public/')));
            }

            return appUrl($candidate);
        }
    }

    if ($defaultPath === null || $defaultPath === '') {
        return null;
    }

    return appUrl(ltrim(str_replace('\\', '/', $defaultPath), '/'));
}