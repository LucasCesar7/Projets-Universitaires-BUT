<?php

echo "<h1>Test SendGrid</h1>";

require_once __DIR__ . '/../helpers/EmailHelper.php';


$emailHelper = new EmailHelper();

echo "<h2>Configuration :</h2>";
echo "<pre>";
echo "API Key configurée : " . (strlen(Env::get('SMTP_FROM_EMAIL') ?? '') > 10 ? 'Oui' : 'Non') . "\n";
echo "Email FROM configuré : " . (Env::get('SMTP_FROM_NAME') ?? 'Non configuré') . "\n";
echo "</pre>";

echo "<hr><h2>Envoi de l'email de test...</h2>";

$token = bin2hex(random_bytes(32));
$result = $emailHelper->sendVerificationEmail(
    'tas.tom6@gmail.com', 
    $token,
    'TestUser',
    'electeur'
);

echo "<hr>";

if ($result) {
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; color: #155724;'>";
    echo "<h2>Email envoyé avec succès !</h2>";
    echo "<p>Vérifie ta boîte de réception (et les spams).</p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 5px; color: #721c24;'>";
    echo "<h2>Erreur lors de l'envoi</h2>";
    echo "<p>Vérifie les logs : <code>C:\\wamp64\\logs\\php_error.log</code></p>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>Checklist :</h3>";
echo "<ul>";
echo "<li>SendGrid installé : " . (class_exists('\SendGrid') ? 'OUI' : 'NON') . "</li>";
echo "<li>vendor/autoload.php existe : " . (file_exists(__DIR__ . '/../../vendor/autoload.php') ? 'OUI' : 'NON') . "</li>";
echo "</ul>";
