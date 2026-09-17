<?php
// config/session.php

require_once __DIR__ . '/paths.php';
require_once __DIR__ . '/../vendor/autoload.php';

const AUTHENTICATED_SESSION_IDLE_TIMEOUT = 1800;
const AUTHENTICATED_SESSION_REGENERATION_INTERVAL = 900;

/**
 * Centralized user session management.
 * Provides helper functions to check authentication and roles.
 */

/**
 * Custom error handler - prevents information leakage in production.
 * Logs detailed errors server-side, shows generic message to users.
 */
set_error_handler(function(int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    error_log((string) json_encode([
        'timestamp' => date(DATE_ATOM),
        'level' => getErrorLevelName($severity),
        'type' => 'php_error',
        'message' => $message,
        'file' => $file,
        'line' => $line,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    if (isProductionEnvironment()) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    return true;
});

/**
 * Exception handler - shows generic error page to users.
 */
set_exception_handler(function(Throwable $e): void {
    error_log((string) json_encode([
        'timestamp' => date(DATE_ATOM),
        'level' => 'Exception',
        'type' => get_class($e),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    
    if (headers_sent()) {
        echo "Une erreur est survenue. Veuillez réessayer.";
        exit(1);
    }
    
    http_response_code(500);
    echo "<!DOCTYPE html><html><head><title>Erreur</title></head><body>";
    echo "<h1>Une erreur est survenue</h1>";
    echo "<p>Veuillez réessayer plus tard.</p>";
    echo "</body></html>";
    exit(1);
});

function getErrorLevelName(int $level): string
{
    return match($level) {
        E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR => 'Fatal Error',
        E_WARNING, E_CORE_WARNING, E_COMPILE_WARNING, E_USER_WARNING => 'Warning',
        E_PARSE => 'Parse Error',
        E_NOTICE, E_USER_NOTICE => 'Notice',
        E_STRICT => 'Strict Standards',
        E_RECOVERABLE_ERROR => 'Recoverable Error',
        E_DEPRECATED, E_USER_DEPRECATED => 'Deprecated',
        default => 'Unknown Error'
    };
}

function isProductionEnvironment(): bool
{
    return (bool) (getenv('APP_ENV') === 'production' || getenv('APP_ENV') === 'prod');
}

applySecurityHeaders();
startSecureSession();

function applySecurityHeaders(): void
{
    if (headers_sent()) {
        return;
    }

    // Hide server version information
    header('Server: Apache');
    header('X-Powered-By: PHP');
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; base-uri 'self'; frame-ancestors 'none'; object-src 'none'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; img-src 'self' data: blob:; font-src 'self' https://cdnjs.cloudflare.com data:; connect-src 'self'; form-action 'self'; upgrade-insecure-requests");
    header('Permissions-Policy: accelerometer=(), camera=(), geolocation=(), gyroscope=(), microphone=(), payment=(), usb=()');

    if (isHttpsRequest()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function isHttpsRequest(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        return strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';
    }

    return (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443;
}

function getSessionCookiePath(): string
{
    $baseUrl = rtrim(appBaseUrl(), '/');

    return $baseUrl === '' ? '/' : $baseUrl;
}

function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        refreshAuthenticatedSession();
        return;
    }

    if (headers_sent()) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => getSessionCookiePath(),
        'secure' => isHttpsRequest(),
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    session_start();
    refreshAuthenticatedSession();
}

function startAuthenticatedSession(array $user): void
{
    startSecureSession();
    $now = time();

    session_regenerate_id(true);

    $_SESSION = [
        'user_id' => $user['id'],
        'user_pseudonyme' => $user['pseudonyme'],
        'user_email' => $user['email'],
        'user_role' => $user['role'],
        'logged_in' => true,
        'login_time' => $now,
        'last_activity' => $now,
        'last_regeneration' => $now
    ];
}

function refreshAuthenticatedSession(): void
{
    if (!isAuthenticated() || session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    $now = time();
    $lastActivity = (int) ($_SESSION['last_activity'] ?? $_SESSION['login_time'] ?? $now);

    if (($now - $lastActivity) >= AUTHENTICATED_SESSION_IDLE_TIMEOUT) {
        destroyAuthenticatedSession();
        $GLOBALS['sma_session_expired_notice'] = true;
        return;
    }

    $lastRegeneration = (int) ($_SESSION['last_regeneration'] ?? $_SESSION['login_time'] ?? $now);

    if (($now - $lastRegeneration) >= AUTHENTICATED_SESSION_REGENERATION_INTERVAL) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = $now;
    }

    $_SESSION['last_activity'] = $now;
}

function hasExpiredAuthenticatedSessionNotice(): bool
{
    return !empty($GLOBALS['sma_session_expired_notice']);
}

function destroyAuthenticatedSession(): void
{
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        startSecureSession();
    }

    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies') && !headers_sent()) {
        $params = session_get_cookie_params();
        $cookieOptions = [
            'expires' => time() - 42000,
            'path' => $params['path'] ?? '/',
            'secure' => (bool) ($params['secure'] ?? false),
            'httponly' => (bool) ($params['httponly'] ?? true),
            'samesite' => $params['samesite'] ?? 'Strict'
        ];

        if (!empty($params['domain'])) {
            $cookieOptions['domain'] = $params['domain'];
        }

        setcookie(session_name(), '', $cookieOptions);
    }

    session_destroy();
}

/**
 * Checks whether the user is authenticated.
 * 
 * @return bool True if the user is logged in, otherwise false
 */
function isAuthenticated()
{
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Checks whether the user has the administrator role.
 * 
 * @return bool True if the user is an administrator, otherwise false
 */
function isAdmin()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Retrieves the role of the logged-in user.
 * 
 * @return string|null User role or null if not logged in
 */
function getUserType()
{
    return $_SESSION['user_role'] ?? null;
}

/**
 * Retrieves the ID of the logged-in user.
 * 
 * @return int|null User ID or null if not logged in
 */
function getUserId()
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Retrieves the email of the logged-in user.
 * 
 * @return string|null User email or null if not logged in
 */
function getUserEmail()
{
    return $_SESSION['user_email'] ?? null;
}

/**
 * Retrieves the display name of the logged-in user.
 * 
 * @return string|null User display name or null if not logged in
 */
function getUserPseudo()
{
    return $_SESSION['user_pseudonyme'] ?? null;
}

/**
 * Requires authentication to access a page.
 * Redirects to the login page if the user is not logged in.
 */
function requireAuth()
{
    if (!isAuthenticated()) {
        $loginPath = appUrl('login');

        if (hasExpiredAuthenticatedSessionNotice()) {
            $loginPath .= '?session_expired=1';
        }

        header("Location: $loginPath");
        exit();
    }
}

/**
 * Requires a specific role to access a page.
 * - First checks authentication
 * - Redirects to the appropriate dashboard if the role does not match
 * 
 * @param string $role Required role (admin, candidate, voter)
 */
function requireRole($role)
{
    requireAuth();
    
    if (getUserType() !== $role) {
        // Redirect to the dashboard matching the current role.
        $redirect = match(getUserType()) {
            'admin' => appUrl('admin/dashboard'),
            'candidate' => appUrl('candidate/dashboard'),
            'voter' => appUrl('user/dashboard'),
            default => appUrl('index.php')
        };
        
        header("Location: $redirect");
        exit();
    }
}

/**
 * Sends a JSON response with the given status code, then stops execution.
 *
 * @param array $payload JSON payload.
 * @param int $statusCode HTTP status code.
 */
function respondJson(array $payload, int $statusCode = 200)
{
    if (!headers_sent()) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
    }

    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

/**
 * Requires authentication and a specific role for JSON endpoints.
 *
 * @param string $role Required role.
 */
function requireJsonRole($role)
{
    if (!isAuthenticated()) {
        $payload = ['error' => 'Non autorisé'];

        if (hasExpiredAuthenticatedSessionNotice()) {
            $payload['session_expired'] = true;
        }

        respondJson($payload, 401);
    }

    if (getUserType() !== $role) {
        respondJson(['error' => 'Accès refusé'], 403);
    }
}