<?php
// config/candidat-states.php

require_once __DIR__ . '/session.php';

/**
 * State system for candidates/nominees.
 */
class CandidatStateManager
{
    private $pdo;
    
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * Checks whether the current user has approved applications.
     */
    public function isNominee(int $userId): bool
    {
        $sql = "SELECT COUNT(*) as count 
                FROM candidature 
                WHERE id_compte = :userId 
                AND statut = 'Approuvée'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
    
    /**
     * Retrieves all active nominations for the user.
     */
    public function getActiveNominations(int $userId): array
    {
        $sql = "SELECT n.*, cat.nom as categorie_nom, edi.nom as edition_nom,
                       cat.date_debut_votes, cat.date_fin_votes,
                       edi.date_debut, edi.date_fin
                FROM nomination n
                JOIN candidature c ON n.id_candidature = c.id_candidature
                JOIN categorie cat ON n.id_categorie = cat.id_categorie
                JOIN edition edi ON cat.id_edition = edi.id_edition
                WHERE c.id_compte = :userId
                ORDER BY n.date_approbation DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Retrieves the current voting status for a nomination.
     */
    public function getVotingStatus(array $nomination): string
    {
        $now = date('Y-m-d H:i:s');
        $start = $nomination['date_debut_votes'] ?? $nomination['date_debut'];
        $end = $nomination['date_fin_votes'] ?? $nomination['date_fin'];
        
        if (!$end) return 'not_started';
        
        if ($now < $start) return 'not_started';
        if ($now <= $end) return 'in_progress';
        return 'ended';
    }
    
    /**
     * Checks whether the user can edit the profile.
     */
    public function canEditProfile(int $userId): bool
    {
        $nominations = $this->getActiveNominations($userId);
        
        foreach ($nominations as $nom) {
            $status = $this->getVotingStatus($nom);
            if ($status === 'in_progress') {
                return false; // Editing is not allowed during active voting.
            }
        }
        
        return true;
    }
}