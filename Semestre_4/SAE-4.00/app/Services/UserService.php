<?php
// app/Services/UserService.php 

namespace App\Services;

require_once __DIR__ . '/../../config/session.php';

use App\Models\UserModel;

/**
 * Service handling user authentication
 * (simplified version without 2FA)
 */
class UserService
{
    private $userModel;

    /**
     * User service constructor.
     */
    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Handles the login process.
     * - Authenticates the user with email and password
     * - Starts the session on success
     * - Returns the authentication result
     * 
     * @param string $email User email
     * @param string $password User password
     * @param string|null $code2fa 2FA code (unused in this version)
     * @return array Authentication result
     */
    public function login($email, $password, $code2fa = null)
    {
        // Direct authentication: email and password.
        $result = $this->userModel->authenticate($email, $password);

        if (!$result['success']) {
            return $result;
        }

        $user = $result['user'];

        // REMOVED: 2FA check.
        // Successful login - start the session directly.
        $this->startSession($user);

        return [
            'success' => true,
            'user' => $user,
            'redirect' => $this->getRedirectPath($user['role'])
        ];
    }

    /**
     * Starts the user session
     * - Initializes the session variables
     * - Stores the user information
     * 
     * @param array $user User information
     */
    private function startSession($user)
    {
        startAuthenticatedSession($user);
    }

    /**
     * Determines the redirect path according to the user's role.
     * 
     * @param string $role User role
     * @return string Appropriate redirect path
     */
    private function getRedirectPath($role)
    {
        switch ($role) {
            case 'admin':
                return appUrl('admin/dashboard');
            case 'candidate':
                return appUrl('candidate/dashboard');
            case 'voter':
                return appUrl('user/dashboard');
            default:
                return appUrl('index.php');
        }
    }

    /**
     * Supprime définitivement le compte utilisateur et ses données associées (RGPD).
     * 
     * @param int $userId ID de l'utilisateur à supprimer
     * @return bool Succès de la suppression
     */
    public function deleteUserAccount(int $userId): bool
    {
        $db = $this->userModel->getDb(); // Accès via votre instance PDO

        try {
            $db->beginTransaction();

            // Supprimer les votes liés ou anonymiser
            // Supprimer les candidatures
            $stmtCandidatures = $db->prepare("DELETE FROM candidature WHERE id_compte = :userId");
            $stmtCandidatures->execute([':userId' => $userId]);

            // Supprimer le profil candidat ou utilisateur
            $stmtCandidat = $db->prepare("DELETE FROM candidat WHERE id_compte = :userId");
            $stmtCandidat->execute([':userId' => $userId]);

            $stmtUtilisateur = $db->prepare("DELETE FROM utilisateur WHERE id_compte = :userId");
            $stmtUtilisateur->execute([':userId' => $userId]);

            // Supprimer le compte principal
            $stmtCompte = $db->prepare("DELETE FROM compte WHERE id_compte = :userId");
            $stmtCompte->execute([':userId' => $userId]);

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("Erreur suppression compte : " . $e->getMessage());
            return false;
        }
    }
}

\class_alias(UserService::class, 'UserService');
