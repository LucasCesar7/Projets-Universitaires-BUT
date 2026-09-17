<!DOCTYPE html>
<html lang="fr">

<?php require_once __DIR__ . '/../../config/paths.php'; ?>

<?php $adminAvatarUrl = resolveAppAssetUrl('assets/images/admin-avatar.png', 'assets/images/logo.png'); ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Social Media Awards</title>
    <!-- CSS da Sidebar -->
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/admin-sidebar.css')); ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- CSS do Header -->
    <link rel="stylesheet" href="<?php echo htmlspecialchars(appUrl('assets/css/admin-header.css')); ?>">
</head>

<body>

    <!-- Botão hamburger para mobile -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Incluir a sidebar -->
    <?php require_once __DIR__ . '/admin-sidebar.php'; ?>

    <!-- Overlay para mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <header class="admin-header">
        <div class="header-profile">
            <img loading="lazy" src="<?php echo htmlspecialchars($adminAvatarUrl ?? appUrl('assets/images/logo.png')); ?>" alt="Admin Avatar" class="admin-avatar">
            <span><?php echo htmlspecialchars($_SESSION['user_pseudonyme'] ?? 'Admin'); ?></span>
            <form method="post" action="<?php echo htmlspecialchars(appUrl('logout')); ?>" style="display: inline; margin: 0;">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Services\CsrfService::token('logout'), ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="logout-button" title="Se Déconnecter">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </header>

    <main class="admin-main-content">
        <div class="admin-container">