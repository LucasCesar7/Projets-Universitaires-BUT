<?php

namespace App\Controllers;

require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../config/session.php';

use App\Models\UserModel;
use App\Services\CsrfService;
use App\Services\RateLimiterService;
use App\Services\UserService;
use Exception;

/**
 * Controller handling user authentication and registration.
 */
class UserController
{
    private const CAPTCHA_FROM_ATTEMPT = 5;

    private ?UserService $userService = null;
    private ?UserModel $userModel = null;
    private RateLimiterService $rateLimiter;

    /**
     * User controller constructor.
     */
    public function __construct()
    {
        $this->rateLimiter = new RateLimiterService();
    }

    /**
     * Handles the login process.
     * - Authenticates the user
     * - Sets session variables
     * - Redirects to the appropriate page
     * 
     * @return array Authentication result
     */
    public function handleLogin()
    {
        $rateLimitIdentifier = RateLimiterService::resolveClientIp();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [
                'captcha' => $this->buildCaptchaPayload('login', $rateLimitIdentifier)
            ];
        }

        if (!CsrfService::isValid($_POST['csrf_token'] ?? null, 'login')) {
            return [
                'error' => 'La validation du formulaire a expiré. Veuillez réessayer.'
            ];
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['mot_de_passe'] ?? '';
        $redirectParam = $_POST['redirect'] ?? ''; // Retrieve the requested redirect.

        if ($this->isCaptchaRequired('login', $rateLimitIdentifier)
            && !$this->validateCaptchaAnswer('login', $_POST['captcha_answer'] ?? null)) {
            $retryAfter = $this->rateLimiter->hit('login', $rateLimitIdentifier);

            if ($retryAfter > 0) {
                return [
                    'error' => $this->formatRateLimitMessage('de connexion', $retryAfter),
                    'captcha' => $this->buildCaptchaPayload('login', $rateLimitIdentifier),
                ];
            }

            return [
                'error' => 'CAPTCHA invalide. Veuillez réessayer.',
                'captcha' => $this->buildCaptchaPayload('login', $rateLimitIdentifier),
            ];
        }

        $retryAfter = $this->rateLimiter->getRetryAfter('login', $rateLimitIdentifier);

        if ($retryAfter > 0) {
            return [
                'error' => $this->formatRateLimitMessage('de connexion', $retryAfter),
                'captcha' => $this->buildCaptchaPayload('login', $rateLimitIdentifier),
            ];
        }

        $result = $this->userService()->login($email, $password);

        if ($result['success']) {
            $this->rateLimiter->clear('login', $rateLimitIdentifier);
            $this->clearCaptcha('login');

            // REDIRECTION PRIORITIES:
            // 1. The form 'redirect' parameter (coming from login.php)
            // 2. The default dashboard based on the role
            $redirect = sanitizeAppRedirect(
                $redirectParam,
                $this->getDashboardPath($result['user']['role'])
            );
            
            header('Location: ' . $redirect);
            exit();
        }

        $retryAfter = $this->rateLimiter->hit('login', $rateLimitIdentifier);

        if ($retryAfter > 0) {
            return [
                'error' => $this->formatRateLimitMessage('de connexion', $retryAfter),
                'captcha' => $this->buildCaptchaPayload('login', $rateLimitIdentifier),
            ];
        }

        return [
            'error' => $result['message'],
            'captcha' => $this->buildCaptchaPayload('login', $rateLimitIdentifier),
        ];
    }

    private function formatRateLimitMessage(string $attemptLabel, int $retryAfter): string
    {
        if ($retryAfter >= 60) {
            $minutes = (int) ceil($retryAfter / 60);

            return sprintf(
                'Trop de tentatives %s. Réessayez dans %d minute%s.',
                $attemptLabel,
                $minutes,
                $minutes > 1 ? 's' : ''
            );
        }

        return sprintf(
            'Trop de tentatives %s. Réessayez dans %d seconde%s.',
            $attemptLabel,
            $retryAfter,
            $retryAfter > 1 ? 's' : ''
        );
    }

    /**
     * Handles registration for a new user.
     * - Validates the data
     * - Creates the account
     * - Redirects to the appropriate dashboard
     * 
     * @return array Registration result
     */
    public function handleRegistration()
    {
        $rateLimitIdentifier = RateLimiterService::resolveClientIp();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [
                'success' => false,
                'errors' => [],
                'data' => [],
                'captcha' => $this->buildCaptchaPayload('register', $rateLimitIdentifier),
            ];
        }

        $data = [
            'pseudonyme' => trim($_POST['pseudonyme'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'mot_de_passe' => $_POST['mot_de_passe'] ?? '',
            'confirm_mot_de_passe' => $_POST['confirm_mot_de_passe'] ?? '',
            'type_user' => $_POST['type_user'] ?? '',
            'date_naissance' => $_POST['date_naissance'] ?? '',
            'pays' => $_POST['pays'] ?? '',
            'genre' => $_POST['genre'] ?? ''
        ];
        if ($this->isCaptchaRequired('register', $rateLimitIdentifier)
            && !$this->validateCaptchaAnswer('register', $_POST['captcha_answer'] ?? null)) {
            $retryAfter = $this->rateLimiter->hit('register', $rateLimitIdentifier);

            if ($retryAfter > 0) {
                return $this->buildRegistrationErrorResponse([
                    $this->formatRateLimitMessage("d'inscription", $retryAfter)
                ], $data);
            }

            return $this->buildRegistrationErrorResponse([
                'CAPTCHA invalide. Veuillez réessayer.'
            ], $data);
        }

        $retryAfter = $this->rateLimiter->getRetryAfter('register', $rateLimitIdentifier);

        if ($retryAfter > 0) {
            return $this->buildRegistrationErrorResponse([
                $this->formatRateLimitMessage("d'inscription", $retryAfter)
            ], $data);
        }

        if (!CsrfService::isValid($_POST['csrf_token'] ?? null, 'register')) {
            return $this->buildRegistrationErrorResponse([
                'La validation du formulaire a expiré. Veuillez réessayer.'
            ], $data);
        }

        $errors = [];

        // Pseudonym validation
        if (empty($data['pseudonyme']) || strlen($data['pseudonyme']) < 3) {
            $errors[] = "The pseudonym must contain at least 3 characters";
        } elseif (strlen($data['pseudonyme']) > 50) {
            $errors[] = "The pseudonym must not exceed 50 characters";
        }

        // Check pseudonym uniqueness
        if (!empty($data['pseudonyme']) && strlen($data['pseudonyme']) >= 3) {
            $existingPseudonyme = $this->userModel()->getUserByPseudonyme($data['pseudonyme']);
            if ($existingPseudonyme) {
                $errors[] = "This pseudonym is already in use. Please choose another one.";
            }
        }

        // Email validation
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email";
        }

        // Check email uniqueness
        $existingUser = $this->userModel()->getUserByEmail($data['email']);
        if ($existingUser) {
            $errors[] = "This email is already in use";
        }

        // Password validation - improved complexity requirements
        if (strlen($data['mot_de_passe']) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
        } elseif (!preg_match('/[A-Z]/', $data['mot_de_passe'])) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule";
        } elseif (!preg_match('/[a-z]/', $data['mot_de_passe'])) {
            $errors[] = "Le mot de passe doit contenir au moins une minuscule";
        } elseif (!preg_match('/[0-9]/', $data['mot_de_passe'])) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre";
        }

        if ($data['mot_de_passe'] !== $data['confirm_mot_de_passe']) {
            $errors[] = "Passwords do not match";
        }

        // User type validation
        if (empty($data['type_user']) || !in_array($data['type_user'], ['voter', 'candidate'])) {
            $errors[] = "Please select a valid user type";
        }

        // Date of birth validation (minimum age: 13)
        if (empty($data['date_naissance'])) {
            $errors[] = "Date of birth is required";
        } elseif (strtotime($data['date_naissance']) > strtotime('-13 years')) {
            $errors[] = "You must be at least 13 years old";
        }

        // COUNTRY VALIDATION
        if (empty($data['pays'])) {
            $errors[] = "Country is required";
        }

        // Check acceptance of the terms
        if (!isset($_POST['terms'])) {
            $errors[] = "You must accept the terms of use";
        }

        // Return errors if validation fails
        if (!empty($errors)) {
            $retryAfter = $this->rateLimiter->hit('register', $rateLimitIdentifier);

            if ($retryAfter > 0) {
                $errors[] = $this->formatRateLimitMessage("d'inscription", $retryAfter);
            }

            return $this->buildRegistrationErrorResponse($errors, $data);
        }

        try {
            // Password hashing
            $hashedPassword = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);

            // Verification code (to be replaced later with a real system)
            $codeVerification = '000000';

            // Prepare data for the compte table
            $userData = [
                ':pseudonyme' => $data['pseudonyme'],
                ':email' => $data['email'],
                ':mot_de_passe' => $hashedPassword,
                ':date_naissance' => $data['date_naissance'],
                ':pays' => $data['pays'],
                ':genre' => $data['genre'] ?? null,
                ':code_verification' => $codeVerification
            ];

            // Create the main account
            $userId = $this->userModel()->createUser($userData);

            if (!$userId) {
                $errors = ['An error occurred while creating the account'];
                $retryAfter = $this->rateLimiter->hit('register', $rateLimitIdentifier);

                if ($retryAfter > 0) {
                    $errors[] = $this->formatRateLimitMessage("d'inscription", $retryAfter);
                }

                return $this->buildRegistrationErrorResponse($errors, $data);
            }

            // Insert into the specific table according to the user type
            if ($data['type_user'] === 'candidate') {
                $this->insertCandidate($userId);
                $role = 'candidate';
            } else {
                $this->insertUtilisateur($userId);
                $role = 'voter';
            }

            startAuthenticatedSession([
                'id' => $userId,
                'pseudonyme' => $data['pseudonyme'],
                'email' => $data['email'],
                'role' => $role
            ]);

            $this->rateLimiter->clear('register', $rateLimitIdentifier);
            $this->clearCaptcha('register');

            // Redirect to the appropriate dashboard
            $redirect = $this->getDashboardPath($role);
            header('Location: ' . $redirect);
            exit();
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $errors = ['A technical error occurred'];
            $retryAfter = $this->rateLimiter->hit('register', $rateLimitIdentifier);

            if ($retryAfter > 0) {
                $errors[] = $this->formatRateLimitMessage("d'inscription", $retryAfter);
            }

            return $this->buildRegistrationErrorResponse($errors, $data);
        }
    }

    private function buildRegistrationErrorResponse(array $errors, array $data): array
    {
        $rateLimitIdentifier = RateLimiterService::resolveClientIp();

        return [
            'success' => false,
            'errors' => $errors,
            'data' => $data,
            'captcha' => $this->buildCaptchaPayload('register', $rateLimitIdentifier),
        ];
    }

    private function isCaptchaRequired(string $scope, string $identifier): bool
    {
        return $this->rateLimiter->getAttempts($scope, $identifier) >= self::CAPTCHA_FROM_ATTEMPT;
    }

    /**
     * @return array{required: bool, question: string}
     */
    private function buildCaptchaPayload(string $context, string $identifier): array
    {
        if (!$this->isCaptchaRequired($context, $identifier)) {
            $this->clearCaptcha($context);

            return [
                'required' => false,
                'question' => '',
            ];
        }

        $challenge = $this->getOrCreateCaptchaChallenge($context);

        return [
            'required' => true,
            'question' => $challenge['question'],
        ];
    }

    /**
     * @return array{answer: int, question: string, created_at: int}
     */
    private function getOrCreateCaptchaChallenge(string $context): array
    {
        $captcha = $_SESSION['_captcha'][$context] ?? null;
        $now = time();

        if (is_array($captcha)
            && isset($captcha['answer'], $captcha['question'], $captcha['created_at'])
            && ($now - (int) $captcha['created_at']) <= 600) {
            return [
                'answer' => (int) $captcha['answer'],
                'question' => (string) $captcha['question'],
                'created_at' => (int) $captcha['created_at'],
            ];
        }

        $left = random_int(1, 9);
        $right = random_int(1, 9);
        $challenge = [
            'answer' => $left + $right,
            'question' => sprintf('Combien font %d + %d ?', $left, $right),
            'created_at' => $now,
        ];

        $_SESSION['_captcha'][$context] = $challenge;

        return $challenge;
    }

    private function validateCaptchaAnswer(string $context, mixed $rawAnswer): bool
    {
        if ($rawAnswer === null || $rawAnswer === '') {
            return false;
        }

        $challenge = $this->getOrCreateCaptchaChallenge($context);

        return ((int) $rawAnswer) === (int) $challenge['answer'];
    }

    private function clearCaptcha(string $context): void
    {
        if (isset($_SESSION['_captcha'][$context])) {
            unset($_SESSION['_captcha'][$context]);
        }
    }

    /**
     * Inserts a new record into the CANDIDAT table.
     * 
     * @param int $userId User account ID
     * @return bool Insertion success
     */
    private function insertCandidate($userId)
    {
        try {
            $db = $this->userModel()->getDb();
            $stmt = $db->prepare("
                INSERT INTO candidat (id_compte, nom_legal_ou_societe, type_candidature, est_nomine) 
                VALUES (:id_compte, NULL, 'Autre', 0)
            ");
            return $stmt->execute([':id_compte' => $userId]);
        } catch (Exception $e) {
            error_log("Candidate insertion error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Inserts a new record into the UTILISATEUR table.
     * 
     * @param int $userId User account ID
     * @return bool Insertion success
     */
    private function insertUtilisateur($userId)
    {
        try {
            $db = $this->userModel()->getDb();
            $stmt = $db->prepare("
                INSERT INTO utilisateur (id_compte) 
                VALUES (:id_compte)
            ");
            return $stmt->execute([':id_compte' => $userId]);
        } catch (Exception $e) {
            error_log("User insertion error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Determines the dashboard path according to the user's role.
     * 
     * @param string $role User role (admin, voter, candidate)
     * @return string Appropriate dashboard path
     */
    private function getDashboardPath($role)
    {
        switch ($role) {
            case 'admin':
                return appUrl('admin/dashboard');
            case 'candidate':
                return appUrl('candidate/dashboard');
            case 'voter':
                return appUrl('user/dashboard');
            default:
                return publicRouteUrl('home');
        }
    }

    /**
     * Handles user logout.
     * - Clears the session
     * - Destroys cookies
     * - Redirects to the home page
     * 
     * @return bool Logout success
     */
    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        if (!CsrfService::isValid($_POST['csrf_token'] ?? null, 'logout')) {
            return false;
        }

        destroyAuthenticatedSession();
        return true;
    }

    /**
        * Retrieves the database instance.
     * 
        * @return PDO Active database connection instance
     */
    private function getDb()
    {
        return $this->userModel()->getDb();
    }

    private function userService(): UserService
    {
        if ($this->userService === null) {
            $this->userService = new UserService();
        }

        return $this->userService;
    }

    private function userModel(): UserModel
    {
        if ($this->userModel === null) {
            $this->userModel = new UserModel();
        }

        return $this->userModel;
    }

    /**
 * Traitement de la suppression du compte utilisateur
 */
public function handleDeleteAccount(): void
{
    require_once appPath('config/session.php');
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . publicRouteUrl('login'));
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_account') {
        if (!\App\Services\CsrfService::isValid($_POST['csrf_token'] ?? null, 'delete_account')) {
            $_SESSION['error'] = 'Jeton de sécurité invalide.';
            header('Location: ' . appUrl('user/profile'));
            exit();
        }

        $userId = (int) $_SESSION['user_id'];
        $userService = new \App\Services\UserService();

        if ($userService->deleteUserAccount($userId)) {
            \destroyAuthenticatedSession();
            header('Location: ' . publicRouteUrl('home') . '?account_deleted=1');
            exit();
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression de votre compte.';
            header('Location: ' . appUrl('user/profile'));
            exit();
        }
    }
}
}

\class_alias(UserController::class, 'UserController');