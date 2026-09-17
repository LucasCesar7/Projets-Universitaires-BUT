<?php
// app/Models/CategorieModel.php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

use Database;
use PDO;
use PDOException;

class CategorieModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getActiveCategoriesCount()
    {
        $sql = "
        SELECT COUNT(*) as count
        FROM categorie c
        JOIN edition e ON c.id_edition = e.id_edition
        WHERE 
            -- Usa datas da categoria se existirem, senão usa da edição
            COALESCE(c.date_fin_votes, e.date_fin) > NOW()
            AND COALESCE(c.date_debut_votes, e.date_debut) <= NOW()
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] ?? 0;
    }

    public function getAllCategoriesWithNominations()
    {
        $sql = "
        SELECT 
            c.*,
            e.nom as edition_nom,
            e.annee as edition_annee,
            COUNT(n.id_nomination) as nomination_count
        FROM categorie c
        JOIN edition e ON c.id_edition = e.id_edition
        LEFT JOIN nomination n ON c.id_categorie = n.id_categorie
        WHERE 
            COALESCE(c.date_fin_votes, e.date_fin) > NOW()
            AND COALESCE(c.date_debut_votes, e.date_debut) <= NOW()
        GROUP BY c.id_categorie
        ORDER BY COALESCE(c.date_fin_votes, e.date_fin) ASC
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVotingCategoriesForUser($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.*,
                    e.nom as edition_nom,
                    COUNT(DISTINCT n.id_nomination) as nomination_count
                FROM CATEGORIE c
                JOIN EDITION e ON c.id_edition = e.id_edition
                LEFT JOIN NOMINATION n ON c.id_categorie = n.id_categorie
                WHERE c.date_fin_votes > NOW()
                AND NOT EXISTS (
                    SELECT 1 
                    FROM VOTE v
                    JOIN TOKEN_ANONYME ta ON v.id_token = ta.id_token
                    JOIN NOMINATION n2 ON v.id_nomination = n2.id_nomination
                    WHERE ta.id_compte = :id_compte
                    AND n2.id_categorie = c.id_categorie
                )
                GROUP BY c.id_categorie
                ORDER BY c.date_fin_votes ASC
            ");

            $stmt->execute([':id_compte' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getVotingCategoriesForUser error: " . $e->getMessage());
            return [];
        }
    }

    public function getCategoryById($categoryId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, e.nom as edition_nom, e.annee as edition_annee
                FROM CATEGORIE c
                JOIN EDITION e ON c.id_edition = e.id_edition
                WHERE c.id_categorie = :id_categorie
            ");

            $stmt->execute([':id_categorie' => $categoryId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getCategoryById error: " . $e->getMessage());
            return false;
        }
    }

    public function getAllCategories()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, e.nom as edition_nom, e.annee as edition_annee
                FROM CATEGORIE c
                JOIN EDITION e ON c.id_edition = e.id_edition
                ORDER BY c.date_fin_votes DESC
            ");

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getAllCategories error: " . $e->getMessage());
            return [];
        }
    }

    public function getCategoriesByEdition($editionId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, COUNT(n.id_nomination) as nomination_count
                FROM CATEGORIE c
                LEFT JOIN NOMINATION n ON c.id_categorie = n.id_categorie
                WHERE c.id_edition = :id_edition
                GROUP BY c.id_categorie
                ORDER BY c.nom
            ");

            $stmt->execute([':id_edition' => $editionId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getCategoriesByEdition error: " . $e->getMessage());
            return [];
        }
    }
}

\class_alias(CategorieModel::class, 'CategorieModel');
