<?php
/**
 * AuditLogger - Traçabilité des actions (RGPD / CNIL)
 */

class AuditLogger {
    
    private $pdo;
    
    public function __construct($pdo = null) {
        if ($pdo === null) {
            $rootPath = dirname(dirname(dirname(__FILE__)));
            require_once $rootPath . '/app/config/config.php';
            $this->pdo = dbconnect();
        } else {
            $this->pdo = $pdo;
        }
    }
    
    /**
     * Enregistrer une action dans audit_log
     * 
     * @param string $action Type d'action (LOGIN, REGISTER, VOTE, UPDATE, DELETE, etc.)
     * @param string|null $tableName Nom de la table concernée
     * @param int|null $recordId ID de l'enregistrement concerné
     * @param array|null $oldValues Anciennes valeurs (avant modification)
     * @param array|null $newValues Nouvelles valeurs (après modification)
     * @param string|null $description Description libre de l'action
     * @return bool
     */
    public function log($action, $tableName = null, $recordId = null, $oldValues = null, $newValues = null, $description = null) {
        try {
            // Récupérer infos utilisateur connecté
            $userId = $_SESSION['user_id'] ?? null;
            $userType = $_SESSION['role'] ?? 'invite';
            
            // Convertir le role en format de la table
            $userTypeMapping = [
                'admin' => 'administrateur',
                'candidat' => 'candidat',
                'electeur' => 'electeur',
                'invite' => 'invite'
            ];
            $userType = $userTypeMapping[$userType] ?? 'invite';
            
            // Récupérer IP et User-Agent
            $ipAddress = $this->getClientIp();
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            
            // Convertir les valeurs en JSON
            $oldValuesJson = $oldValues ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null;
            $newValuesJson = $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null;
            
            // Insérer dans audit_log
            $stmt = $this->pdo->prepare("
                INSERT INTO audit_log 
                (user_id, user_type, action, table_name, record_id, old_values, new_values, ip_address, user_agent, description, date_action)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $userId,
                $userType,
                $action,
                $tableName,
                $recordId,
                $oldValuesJson,
                $newValuesJson,
                $ipAddress,
                $userAgent,
                $description
            ]);
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Erreur AuditLogger: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Logger une connexion utilisateur
     */
    public function logLogin($userId, $userType, $success = true) {
        $action = $success ? 'LOGIN_SUCCESS' : 'LOGIN_FAILED';
        $description = $success ? "Connexion réussie" : "Tentative de connexion échouée";
        
        return $this->log($action, null, null, null, null, $description);
    }
    
    /**
     * Logger une déconnexion
     */
    public function logLogout($userId, $userType) {
        return $this->log('LOGOUT', null, null, null, null, "Déconnexion utilisateur");
    }
    
    /**
     * Logger une inscription
     */
    public function logRegister($userId, $userType, $tableName, $email) {
        return $this->log(
            'REGISTER',
            $tableName,
            $userId,
            null,
            ['email' => $email],
            "Nouvelle inscription"
        );
    }
    
    /**
     * Logger un vote
     */
    public function logVote($voteId, $type, $categorie) {
        return $this->log(
            'VOTE',
            'vote',
            $voteId,
            null,
            ['type' => $type, 'categorie' => $categorie],
            "Vote enregistré"
        );
    }
    
    /**
     * Logger une modification de données
     */
    public function logUpdate($tableName, $recordId, $oldValues, $newValues) {
        return $this->log(
            'UPDATE',
            $tableName,
            $recordId,
            $oldValues,
            $newValues,
            "Modification d'enregistrement"
        );
    }
    
    /**
     * Récupérer l'IP réelle du client (gestion proxy/CDN)
     */
    private function getClientIp() {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',    // Cloudflare
            'HTTP_X_FORWARDED_FOR',     // Proxy
            'HTTP_X_REAL_IP',           // Nginx
            'REMOTE_ADDR'               // IP directe
        ];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                
                // Si plusieurs IPs (proxy), prendre la première
                if (strpos($ip, ',') !== false) {
                    $ips = explode(',', $ip);
                    $ip = trim($ips[0]);
                }
                
                // Valider l'IP
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return 'Unknown';
    }
    
    /**
     * Récupérer tous les logs (admin)
     */
    public function getAllLogs($limit = 100, $offset = 0, $filters = []) {
        try {
            $sql = "SELECT * FROM audit_log WHERE 1=1";
            $params = [];
            
            // Filtres optionnels
            if (!empty($filters['action'])) {
                $sql .= " AND action = ?";
                $params[] = $filters['action'];
            }
            
            if (!empty($filters['user_type'])) {
                $sql .= " AND user_type = ?";
                $params[] = $filters['user_type'];
            }
            
            if (!empty($filters['table_name'])) {
                $sql .= " AND table_name = ?";
                $params[] = $filters['table_name'];
            }
            
            if (!empty($filters['date_debut'])) {
                $sql .= " AND date_action >= ?";
                $params[] = $filters['date_debut'];
            }
            
            if (!empty($filters['date_fin'])) {
                $sql .= " AND date_action <= ?";
                $params[] = $filters['date_fin'];
            }
            
            $sql .= " ORDER BY date_action DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur getAllLogs: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Compter les logs (pagination)
     */
    public function countLogs($filters = []) {
        try {
            $sql = "SELECT COUNT(*) as total FROM audit_log WHERE 1=1";
            $params = [];
            
            if (!empty($filters['action'])) {
                $sql .= " AND action = ?";
                $params[] = $filters['action'];
            }
            
            if (!empty($filters['user_type'])) {
                $sql .= " AND user_type = ?";
                $params[] = $filters['user_type'];
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
            
        } catch (PDOException $e) {
            error_log("Erreur countLogs: " . $e->getMessage());
            return 0;
        }
    }
}
?>
