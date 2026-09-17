<?php
class CandidatModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $rootPath = dirname(dirname(dirname(__FILE__)));
        require_once $rootPath . '/app/config/config.php';
        $this->pdo = $pdo;
    }

    public function getAllCandidats()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM candidat ORDER BY nom');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public static function addCandidat($pdo, $data)
    {
        try {
            $stmt = $pdo->prepare("
            INSERT INTO candidat 
            (nom, prenom, email, pseudo, date_inscription, accepte_conditions, mot_de_passe, email_verifie)
            VALUES (:nom, :prenom, :email, :pseudo, :date_inscription, :accepte_conditions, :mot_de_passe, 0)
        ");

            $params = [
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'pseudo' => $data['pseudo'] ?? null,
                'date_inscription' => $data['date_inscription'] ?? date('Y-m-d H:i:s'),
                'accepte_conditions' => $data['accepte_conditions'] ?? 1,
                'mot_de_passe' => $data['mot_de_passe']
            ];

            $stmt->execute($params);

            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            echo "Erreur PDO : " . $e->getMessage();
            return false;
        }
    }

    public static function deleteCandidat($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM candidat WHERE id_candidat = :id");
        return $stmt->execute(['id' => $id]);
    }
}
