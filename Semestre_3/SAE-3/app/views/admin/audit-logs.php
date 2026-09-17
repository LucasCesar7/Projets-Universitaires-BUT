<?php
session_start();

// Vérifier admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../public/index.php');
    exit;
}

$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
require_once $rootPath . '/app/helpers/AuditLogger.php';
$audit = new AuditLogger();

// Pagination
$page = $_GET['page'] ?? 1;
$limit = 50;
$offset = ($page - 1) * $limit;

// Filtres
$filters = [
    'action' => $_GET['action'] ?? '',
    'user_type' => $_GET['user_type'] ?? '',
    'date_debut' => $_GET['date_debut'] ?? '',
    'date_fin' => $_GET['date_fin'] ?? ''
];

// Récupérer logs
$logs = $audit->getAllLogs($limit, $offset, $filters);
$totalLogs = $audit->countLogs($filters);
$totalPages = ceil($totalLogs / $limit);

include dirname(dirname(__FILE__)) . '/layouts/header.php';


?>
<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/audit-logs.css">

<div class="admin-container">
    <h1>Audit des actions</h1>
    
    <!-- Filtres -->
    <form method="GET" class="audit-filters">
        <select name="action">
            <option value="">Toutes les actions</option>
            <option value="LOGIN_SUCCESS">Connexion réussie</option>
            <option value="LOGIN_FAILED">Échec connexion</option>
            <option value="REGISTER">Inscription</option>
            <option value="VOTE">Vote</option>
            <option value="UPDATE">Modification</option>
            <option value="DELETE">Suppression</option>
        </select>
        
        <select name="user_type">
            <option value="">Tous les utilisateurs</option>
            <option value="administrateur">Admin</option>
            <option value="electeur">Électeur</option>
            <option value="candidat">Candidat</option>
            <option value="invite">Invité</option>
        </select>
        
        <input type="date" name="date_debut" placeholder="Date début">
        <input type="date" name="date_fin" placeholder="Date fin">
        
        <button type="submit">Filtrer</button>
    </form>
    
    <!-- Table des logs -->
    <table class="audit-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Utilisateur</th>
                <th>Action</th>
                <th>Table</th>
                <th>IP</th>
                <th>Détails</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= $log['id_log'] ?></td>
                <td><?= date('d/m/Y H:i:s', strtotime($log['date_action'])) ?></td>
                <td><?= $log['user_type'] ?> #<?= $log['user_id'] ?? 'N/A' ?></td>
                <td><span class="badge-<?= $log['action'] ?>"><?= $log['action'] ?></span></td>
                <td><?= $log['table_name'] ?? '-' ?></td>
                <td><?= $log['ip_address'] ?></td>
                <td><?= htmlspecialchars($log['description']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
