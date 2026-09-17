<?php
// views/partials/sidebar-candidat.php

// Inicializar serviços
require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance()->getConnection();
$candidatService = new App\Services\CandidatService($pdo);
$openSubmissionWindowStmt = $pdo->query("SELECT COUNT(*) FROM edition WHERE est_active = 1 AND date_fin_candidatures >= NOW()");
$hasOpenSubmissionWindow = ((int) $openSubmissionWindowStmt->fetchColumn()) > 0;

// Check status
$userId = $_SESSION['user_id'] ?? null;
$isNominee = $userId ? $candidatService->isNominee($userId) : false;
$canEditProfile = $isNominee ? $candidatService->canEditProfile($userId) : true;

// Get active nominations (if the user is a nominee)
$nominations = [];
if ($isNominee) {
    $nominations = $candidatService->getActiveNominations($userId);
}

$currentPath = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isActivePath = static function (string $url) use ($currentPath): bool {
    return $currentPath === (string) parse_url($url, PHP_URL_PATH);
};

$candidateDashboardUrl = appUrl('candidate/dashboard');
$candidateApplicationsUrl = appUrl('candidate/candidatures');
$nomineeProfileUrl = appUrl('candidate/nominee-profile');
$candidateProfileUrl = appUrl('candidate/profile');
$shareNominationUrl = appUrl('candidate/share');
$resultsUrl = appUrl('results');
$rulesUrl = appUrl('candidate/rules');
$myCandidaturesUrl = $candidateApplicationsUrl;
$submitCandidatureUrl = appUrl('candidate/submit');
$logoutUrl = appUrl('logout');
$logoutToken = \App\Services\CsrfService::token('logout');
?>

<div class="sidebar-card mb-4">
    <div class="card-body">
        <div class="sidebar-title">
            <i class="fas <?= $isNominee ? 'fa-trophy' : 'fa-user' ?>"></i>
            <?= $isNominee ? 'MENU NOMINÉ' : 'MENU CANDIDAT' ?>
        </div>

        <ul class="nav-candidat">
            <!-- Dashboard -->
            <li>
                <a class="nav-link <?= $isActivePath($candidateDashboardUrl) ? 'active' : '' ?>"
                    href="<?= htmlspecialchars($candidateDashboardUrl) ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Tableau de bord</span>
                </a>
            </li>

            <?php if ($isNominee): ?>
                <!-- ======================= -->
                <!-- NOMINEE MENU -->
                <!-- ======================= -->

                <!-- Public profile -->
                <li>
                    <a class="nav-link <?= $isActivePath($nomineeProfileUrl) ? 'active' : '' ?>"
                        href="<?= htmlspecialchars($nomineeProfileUrl) ?>">
                        <i class="fas fa-id-badge"></i>
                        <span>Mon profil public</span>
                        <?php if (!$canEditProfile): ?>
                            <span class="badge bg-warning ms-auto" title="Modification désactivée pendant les votes">
                                <i class="fas fa-lock"></i>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>

                <li>
                    <a class="nav-link <?= $isActivePath($candidateProfileUrl) ? 'active' : '' ?>"
                        href="<?= htmlspecialchars($candidateProfileUrl) ?>">
                        <i class="fas fa-id-badge"></i>
                        <span>Mon compte</span>
                    </a>
                </li>

                <!-- Share -->
                <li>
                    <a class="nav-link <?= $isActivePath($shareNominationUrl) ? 'active' : '' ?>"
                        href="<?= htmlspecialchars($shareNominationUrl) ?>">
                        <i class="fas fa-share-alt"></i>
                        <span>Partager ma nomination</span>
                        <?php if (!empty($nominations)): ?>
                            <span class="badge bg-success ms-auto" title="Kit promotionnel disponible">
                                <i class="fas fa-rocket"></i>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>

                <!-- Results -->
                <li>
                    <a class="nav-link <?= $isActivePath($resultsUrl) ? 'active' : '' ?>"
                        href="<?= htmlspecialchars($resultsUrl) ?>">
                        <i class="fas fa-chart-line"></i>
                        <span>Resultats</span>
                    </a>
                </li>

                <!-- Rules -->
                <li>
                    <a class="nav-link <?= $isActivePath($rulesUrl) ? 'active' : '' ?>"
                        href="<?= htmlspecialchars($rulesUrl) ?>">
                        <i class="fas fa-file-contract"></i>
                        <span>Règlement</span>
                    </a>
                </li>


            <?php else: ?>
                <!-- ======================= -->
                <!-- CANDIDATE MENU -->
                <!-- ======================= -->

                <!-- Applications -->
                <li>
                    <a class="nav-link <?= $isActivePath($myCandidaturesUrl) ? 'active' : '' ?>"
                        href="<?= htmlspecialchars($myCandidaturesUrl) ?>">
                        <i class="fas fa-file-alt"></i>
                        <span>Mes candidatures</span>
                    </a>
                </li>

                <!-- Submit application -->
                <li>
                    <?php if ($hasOpenSubmissionWindow): ?>
                        <a class="nav-link <?= $isActivePath($submitCandidatureUrl) ? 'active' : '' ?>"
                            href="<?= htmlspecialchars($submitCandidatureUrl) ?>">
                            <i class="fas fa-paper-plane"></i>
                            <span>Soumettre candidature</span>
                        </a>
                    <?php else: ?>
                        <span class="nav-link disabled" aria-disabled="true">
                            <i class="fas fa-lock"></i>
                            <span>Soumissions fermées</span>
                        </span>
                    <?php endif; ?>
                </li>

                <!-- Profile -->
                <li>
                    <a class="nav-link <?= $isActivePath($candidateProfileUrl) ? 'active' : '' ?>" href="<?= htmlspecialchars($candidateProfileUrl) ?>">
                        <i class="fas fa-user-edit"></i>
                        <span>Mon profil</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Separator -->
            <li class="nav-separator"></li>

            <!-- Logout -->
            <li>
                <form method="post" action="<?= htmlspecialchars($logoutUrl) ?>" style="margin: 0;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($logoutToken, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="nav-link" style="border: none; background: none; width: 100%; text-align: left;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>

<!-- Status badge -->
<div class="status-badge-card text-center">
    <?php if ($isNominee): ?>
        <span class="nominee-badge">
            <i class="fas fa-trophy"></i> NOMINÉ(E)
        </span>
        <p class="mt-2 mb-0 small">
            Félicitations !<br>
            Vous participez aux votes.
        </p>

        <?php if (!empty($nominations)):
            $firstNomination = $nominations[0];
            $votingStatus = $candidatService->getVotingStatus($firstNomination);
        ?>
            <div class="mt-3">
                <small class="d-block text-muted">Statut des votes :</small>
                <span class="badge 
            <?= $votingStatus == 'in_progress' ? 'bg-success' : ($votingStatus == 'ended' ? 'bg-secondary' : 'bg-warning') ?>">
                    <?= $votingStatus == 'in_progress' ? 'En cours' : ($votingStatus == 'ended' ? 'Terminés' : 'À venir') ?>
                </span>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <span class="candidate-badge">
            <i class="fas fa-user"></i> CANDIDAT
        </span>
        <p class="mt-2 mb-0 small">
            <?php if ($hasOpenSubmissionWindow): ?>
                Soumettez votre<br>
                première candidature !
            <?php else: ?>
                Aucun appel n'est<br>
                ouvert actuellement.
            <?php endif; ?>
        </p>
    <?php endif; ?>
</div>

<style>
    .nav-separator {
        height: 1px;
        background: var(--border-color);
        margin: var(--spacing-sm) 0;
        list-style: none;
    }
</style>