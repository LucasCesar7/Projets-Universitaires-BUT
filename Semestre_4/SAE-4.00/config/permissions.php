<?php
// config/permissions.php

require_once __DIR__ . '/session.php';

/**
 * Checks whether the user is currently logged in.
 * 
 * @return bool True if the user is logged in, otherwise false
 */
function isLoggedIn(): bool
{
    return isAuthenticated(); // Reuse the existing function
}

/**
 * Redirects to the login page if the user is not authenticated.
 */
function requireLogin()
{
    requireAuth(); // Reuse the existing function
}

/**
 * Requires the user to be an administrator.
 * Delegates to the generic role guard for consistent redirection behavior.
 */
function requireAdmin()
{
    requireRole('admin');
}

/**
 * Checks whether a user can vote in a specific category.
 * 
 * @param int $userId User ID
 * @param int $categoryId Category ID
 * @return bool True if the user can vote, otherwise false
 */
function canUserVote($userId, $categoryId)
{
    require_once __DIR__ . '/../app/Services/VoteService.php';
    $voteService = new VoteService();
    return $voteService->canUserVote($userId, $categoryId);
}

/**
 * Checks whether a category is active for voting.
 * 
 * @param int $categoryId Category ID
 * @return bool True if the category is active, otherwise false
 */
function isCategoryActive($categoryId)
{
    require_once __DIR__ . '/../app/Models/VoteModel.php';
    $voteModel = new Vote();
    return $voteModel->isCategoryActive($categoryId);
}

/**
 * Checks voting permissions and redirects if needed.
 * 
 * @param int $userId User ID
 * @param int $categoryId Category ID
 */
function requireVotingPermission($userId, $categoryId)
{
    if (!canUserVote($userId, $categoryId)) {
        $_SESSION['error'] = 'Vous ne pouvez pas voter dans cette catégorie';
        header('Location: ' . appUrl('vote'));
        exit();
    }
}

/**
 * Validates an anonymous voting token.
 * 
 * @param string $token Token value
 * @param int $userId User ID
 * @param int $categoryId Category ID
 * @return bool True if the token is valid, otherwise false
 */
function validateVotingToken($token, $userId, $categoryId)
{
    require_once __DIR__ . '/../app/Models/VoteModel.php';
    $voteModel = new Vote();
    
    try {
        // Check whether the token exists, is unused, and has not expired.
        $stmt = $voteModel->getDb()->prepare("
            SELECT id_token FROM TOKEN_ANONYME 
            WHERE token_value = :token 
            AND id_compte = :user_id 
            AND id_categorie = :category_id
            AND est_utilise = FALSE 
            AND date_expiration > NOW()
        ");
        
        $stmt->execute([
            ':token' => $token,
            ':user_id' => $userId,
            ':category_id' => $categoryId
        ]);
        
        return $stmt->fetch() !== false;
        
    } catch (PDOException $e) {
        error_log("Token validation error: " . $e->getMessage());
        return false;
    }
}
?>