<?php require_once __DIR__ . '/../../config/paths.php'; ?>

<?php
$currentPath = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isActivePath = static function (string $url) use ($currentPath): bool {
    return $currentPath === (string) parse_url($url, PHP_URL_PATH);
};
$adminDashboardUrl = appUrl('admin/dashboard');
$adminCategoriesUrl = appUrl('admin/categories');
$adminCategoriesCreateUrl = appUrl('admin/categories/create');
$adminCategoryEditUrl = appUrl('admin/categories/edit');
$adminEditionsUrl = appUrl('admin/editions');
$adminEditionsCreateUrl = appUrl('admin/editions/create');
$adminCandidaturesUrl = appUrl('admin/candidatures');
$adminCandidatureViewUrl = appUrl('admin/candidatures/view');
$adminNominationsUrl = appUrl('admin/nominations');
$adminNominationViewUrl = appUrl('admin/nominations/view');
$adminNominationEditUrl = appUrl('admin/nominations/edit');
$resultsUrl = appUrl('results');
$isAdminCategoriesSection = in_array($currentPath, [
    (string) parse_url($adminCategoriesUrl, PHP_URL_PATH),
    (string) parse_url($adminCategoryEditUrl, PHP_URL_PATH),
], true);
$isAdminEditionsSection = in_array($currentPath, [
    (string) parse_url($adminEditionsUrl, PHP_URL_PATH),
    (string) parse_url($adminEditionsCreateUrl, PHP_URL_PATH),
], true);
$isAdminCandidaturesSection = in_array($currentPath, [
    (string) parse_url($adminCandidaturesUrl, PHP_URL_PATH),
    (string) parse_url($adminCandidatureViewUrl, PHP_URL_PATH),
], true);
$isAdminNominationsSection = in_array($currentPath, [
    (string) parse_url($adminNominationsUrl, PHP_URL_PATH),
    (string) parse_url($adminNominationViewUrl, PHP_URL_PATH),
    (string) parse_url($adminNominationEditUrl, PHP_URL_PATH),
], true);
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2><i class="fas fa-crown"></i> Social Media Awards</h2>
        <p>Admin Panel</p>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <li class="<?php echo $isActivePath($adminDashboardUrl) ? 'active' : ''; ?>">
                <a href="<?php echo htmlspecialchars($adminDashboardUrl); ?>">
                    <i class="fas fa-tachometer-alt"></i> Tableau de Bord
                </a>
            </li>

            <li class="nav-section">
                <span class="section-title">Gestion des Editions</span>
                <ul>
                    <li class="<?php echo $isAdminEditionsSection ? 'active' : ''; ?>">
                        <a href="<?php echo htmlspecialchars($adminEditionsUrl); ?>">
                            <i class="fas fa-users"></i> Liste des Editions
                        </a>
                    </li>
                    <li class="<?php echo $isActivePath($adminEditionsCreateUrl) ? 'active' : ''; ?>">
                        <a href="<?php echo htmlspecialchars($adminEditionsCreateUrl); ?>">
                            <i class="fas fa-user-plus"></i> Ajouter une Edition
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-section">
                <span class="section-title">Gestion des Catégories</span>
                <ul>
                    <li class="<?php echo $isAdminCategoriesSection ? 'active' : ''; ?>">
                        <a href="<?php echo htmlspecialchars($adminCategoriesUrl); ?>">
                            <i class="fas fa-tags"></i> Liste des catégories
                        </a>
                    </li>
                    <li class="<?php echo $isActivePath($adminCategoriesCreateUrl) ? 'active' : ''; ?>">
                        <a href="<?php echo htmlspecialchars($adminCategoriesCreateUrl); ?>">
                            <i class="fas fa-plus-circle"></i> Ajouter une catégorie
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-section">
                <span class="section-title">Gestion des Candidatures</span>
                <ul>
                    <li class="<?php echo $isAdminCandidaturesSection ? 'active' : ''; ?>">
                        <a href="<?php echo htmlspecialchars($adminCandidaturesUrl); ?>">
                             <i class="fas fa-gavel"></i> Candidatures
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-section">
                <span class="section-title">Gestion des Nominations</span>
                <ul>
                    <li class="<?php echo $isAdminNominationsSection ? 'active' : ''; ?>">
                        <a href="<?php echo htmlspecialchars($adminNominationsUrl); ?>">
                            <i class="fas fa-user-tie"></i> Liste des Nomination
                        </a>
                    </li>
                </ul>
            </li>


            <li class="nav-section">
                <span class="section-title">Resultats</span>
                <ul>
                    <li class="<?php echo $isActivePath($resultsUrl) ? 'active' : ''; ?>">
                        <a href="<?php echo htmlspecialchars($resultsUrl); ?>">
                            <i class="fas fa-vote-yea"></i> Resultats
                        </a>
                    </li>
                </ul>
            </li>

            <li class="logout">
                <form method="post" action="<?php echo htmlspecialchars(appUrl('logout')); ?>" style="margin: 0;">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Services\CsrfService::token('logout'), ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</aside>