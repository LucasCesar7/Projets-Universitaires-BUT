<?php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

use Database;
use PDO;
use PDOException;

/**
 * Model handling user database operations.
 * - Authentication
 * - Account creation
 * - Profile retrieval and updates
 * - Uniqueness checks
 */
class UserModel {
    private $db;

    /**
     * User model constructor.
     * Initializes the database connection.
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Returns the database connection instance.
     * 
     * @return PDO Database connection instance.
     */
    public function getDb() {
        return $this->db;
    }

    /**
     * Authenticates a user by email and password.
     * - Verifies the credentials
     * - Determines the user role
     * - Returns the user information on success
     * 
     * @param string $email User email address.
     * @param string $password Plain-text password.
     * @return array Authentication result.
     */
    public function authenticate($email, $password) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, 
                    CASE 
                        WHEN a.id_compte IS NOT NULL THEN 'admin'
                        WHEN ca.id_compte IS NOT NULL THEN 'candidate'
                        WHEN u.id_compte IS NOT NULL THEN 'voter'
                        ELSE 'unknown'
                    END as role
                FROM compte c
                LEFT JOIN administrateur a ON c.id_compte = a.id_compte
                LEFT JOIN candidat ca ON c.id_compte = ca.id_compte
                LEFT JOIN utilisateur u ON c.id_compte = u.id_compte
                WHERE c.email = :email
                LIMIT 1
            ");
            
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['mot_de_passe'])) {
                // Upgrade password hash if cost factor changed
                if (password_needs_rehash($user['mot_de_passe'], PASSWORD_DEFAULT)) {
                    $this->updatePassword($user['id_compte'], $password);
                }
                return [
                    'success' => true,
                    'user' => [
                        'id' => $user['id_compte'],
                        'pseudonyme' => $user['pseudonyme'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]
                ];
            }
            
            return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
            
        } catch (PDOException $e) {
            error_log("Authentication error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur de connexion'];
        }
    }

    /**
     * Retrieves a user by email address.
     * 
     * @param string $email Email address to search for.
     * @return array|false User data or false if not found.
     */
    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT id_compte, pseudonyme, email, mot_de_passe, date_naissance, pays, genre, photo_profil, code_verification, date_creation, date_modification FROM compte WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Retrieves a user by pseudonym.
     * 
     * @param string $pseudonyme Pseudonym to search for.
     * @return array|false User data or false if not found.
     */
    public function getUserByPseudonyme($pseudonyme) {
        $stmt = $this->db->prepare("SELECT id_compte, pseudonyme, email, mot_de_passe, date_naissance, pays, genre, photo_profil, code_verification, date_creation, date_modification FROM compte WHERE pseudonyme = :pseudonyme");
        $stmt->execute([':pseudonyme' => $pseudonyme]);
        return $stmt->fetch();
    }

    /**
     * Creates a new user in the database.
     * - Inserts the base information into the 'compte' table
     * - Returns the new user ID
     * 
     * @param array $data User data to create.
     * @return int|false New user ID or false on error.
     */
    public function createUser($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO compte (pseudonyme, email, mot_de_passe, date_naissance, pays, genre, code_verification)
                VALUES (:pseudonyme, :email, :mot_de_passe, :date_naissance, :pays, :genre, :code_verification)
            ");
            
            $stmt->execute($data);
            return $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("createUser error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves a user by ID with its role.
     * - Joins the role tables to determine the user type
     * 
     * @param int $userId User ID to search for.
     * @return array|false Full user data or false if not found.
     */
    public function getUserById($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, 
                    CASE 
                        WHEN a.id_compte IS NOT NULL THEN 'admin'
                        WHEN ca.id_compte IS NOT NULL THEN 'candidate'
                        WHEN u.id_compte IS NOT NULL THEN 'voter'
                        ELSE 'unknown'
                    END as role
                FROM compte c
                LEFT JOIN administrateur a ON c.id_compte = a.id_compte
                LEFT JOIN candidat ca ON c.id_compte = ca.id_compte
                LEFT JOIN utilisateur u ON c.id_compte = u.id_compte
                WHERE c.id_compte = :id_compte
                LIMIT 1
            ");
            
            $stmt->execute([':id_compte' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("getUserById error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates a user profile.
     * - Updates only the provided fields
     * - Handles partial updates
     * 
     * @param int $userId User ID to update.
     * @param array $data Data to update.
     * @return bool Whether the update succeeded.
     */
    public function updateUserProfile($userId, $data) {
        try {
            $fields = [];
            $params = [':id_compte' => $userId];
            
            if (isset($data['pseudonyme'])) {
                $fields[] = 'pseudonyme = :pseudonyme';
                $params[':pseudonyme'] = $data['pseudonyme'];
            }
            
            if (isset($data['email'])) {
                $fields[] = 'email = :email';
                $params[':email'] = $data['email'];
            }
            
            if (isset($data['date_naissance'])) {
                $fields[] = 'date_naissance = :date_naissance';
                $params[':date_naissance'] = $data['date_naissance'];
            }
            
            if (isset($data['pays'])) {
                $fields[] = 'pays = :pays';
                $params[':pays'] = $data['pays'];
            }
            
            if (isset($data['genre'])) {
                $fields[] = 'genre = :genre';
                $params[':genre'] = $data['genre'];
            }
            
            if (isset($data['photo_profil'])) {
                $fields[] = 'photo_profil = :photo_profil';
                $params[':photo_profil'] = $data['photo_profil'];
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $sql = "UPDATE compte SET " . implode(', ', $fields) . " WHERE id_compte = :id_compte";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            error_log("updateUserProfile error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Checks whether an email is already in use.
     * - Optionally excludes a specific user during updates
     * 
     * @param string $email Email to check.
     * @param int|null $excludeUserId User ID to exclude from the check.
     * @return bool True if the email is already in use.
     */
    public function isEmailTaken($email, $excludeUserId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM compte WHERE email = :email";
            $params = [':email' => $email];
            
            if ($excludeUserId) {
                $sql .= " AND id_compte != :exclude_id";
                $params[':exclude_id'] = $excludeUserId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("isEmailTaken error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Checks whether a pseudonym is already in use.
     * - Optionally excludes a specific user during updates
     * 
     * @param string $pseudonyme Pseudonym to check.
     * @param int|null $excludeUserId User ID to exclude from the check.
     * @return bool True if the pseudonym is already in use.
     */
    public function isPseudonymeTaken($pseudonyme, $excludeUserId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM compte WHERE pseudonyme = :pseudonyme";
            $params = [':pseudonyme' => $pseudonyme];
            
            if ($excludeUserId) {
                $sql .= " AND id_compte != :exclude_id";
                $params[':exclude_id'] = $excludeUserId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("isPseudonymeTaken error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifies whether the provided plain-text password matches the current user password.
     *
     * @param int $userId User ID.
     * @param string $password Plain-text password to verify.
     * @return bool True when the password matches.
     */
    public function verifyPassword($userId, $password) {
        try {
            $stmt = $this->db->prepare("SELECT mot_de_passe FROM compte WHERE id_compte = :id_compte LIMIT 1");
            $stmt->execute([':id_compte' => $userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user && password_verify($password, $user['mot_de_passe']);
        } catch (PDOException $e) {
            error_log("verifyPassword error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates the stored password hash for the given user.
     *
     * @param int $userId User ID.
     * @param string $newPassword New plain-text password.
     * @return bool True when the update succeeds.
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($hashedPassword === false) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("UPDATE compte SET mot_de_passe = :mot_de_passe WHERE id_compte = :id_compte");
            return $stmt->execute([
                ':mot_de_passe' => $hashedPassword,
                ':id_compte' => $userId,
            ]);
        } catch (PDOException $e) {
            error_log("updatePassword error: " . $e->getMessage());
            return false;
        }
    }
}

\class_alias(UserModel::class, 'User');
?>