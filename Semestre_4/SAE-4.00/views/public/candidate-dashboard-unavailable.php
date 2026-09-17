<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord indisponible</title>
</head>

<body>
    <main>
        <h1>Tableau de bord Candidat</h1>
        <p>Le dashboard n'est pas encore configuré.</p>
        <form method="post" action="<?php echo htmlspecialchars(publicRouteUrl('logout'), ENT_QUOTES, 'UTF-8'); ?>" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Services\CsrfService::token('logout'), ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit">Déconnexion</button>
        </form>
    </main>
</body>

</html>