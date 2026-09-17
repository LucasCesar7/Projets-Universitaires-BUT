<?php
/**
 * Bootstrap PHPUnit : initialise l'environnement avant l'exécution des tests.
 *
 * - Charge l'autoloader Composer (App\, Tests\ et fonctions globales de config/).
 * - Définit les constantes / superglobales attendues par les fonctions utilitaires
 *   (appUrl, sanitizeAppRedirect, …) sans démarrer de serveur HTTP.
 * - Démarre une session PHP en mode CLI pour que CsrfService puisse écrire
 *   dans $_SESSION sans déclencher d'avertissement sur les en-têtes.
 */

declare(strict_types=1);

// ── Autoloader Composer ────────────────────────────────────────────────────────
require_once __DIR__ . '/../vendor/autoload.php';

putenv('APP_ENV=test');
$_ENV['APP_ENV'] = 'test';
$_SERVER['APP_ENV'] = 'test';

// ── Superglobales minimales ────────────────────────────────────────────────────
// Les fonctions de config/paths.php lisent SCRIPT_NAME / REQUEST_URI pour
// calculer appBaseUrl(). En CLI, ces clés n'existent pas : on les injecte.
$_SERVER['SCRIPT_NAME']   = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../index.php';
$_SERVER['REQUEST_URI']   = '/';
$_SERVER['HTTP_HOST']     = 'localhost';
$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
$_SERVER['REMOTE_ADDR']   = '127.0.0.1';

// ── Session CLI ────────────────────────────────────────────────────────────────
// On démarre une session en mémoire (sans cookie) pour les tests CsrfService.
if (session_status() === PHP_SESSION_NONE) {
    session_set_save_handler(new \SessionHandler(), true);
    // Désactiver l'envoi de cookie de session (inutile en CLI)
    ini_set('session.use_cookies', '0');
    session_cache_limiter('');
    session_start();
}
