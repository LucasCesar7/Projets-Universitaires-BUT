<?php

declare(strict_types=1);

namespace App\Controllers;

final class AuthPageController extends Controller
{
    public function login(): string
    {
        require_once appPath('config/session.php');

        $redirectUrl = sanitizeAppRedirect($_GET['redirect'] ?? null, '');

        if (isAuthenticated()) {
            header('Location: ' . ($redirectUrl !== '' ? $redirectUrl : $this->resolveAuthenticatedRedirect()));
            exit();
        }

        $result = (new UserController())->handleLogin();
        $error = is_string($result['error'] ?? null) ? $result['error'] : '';
        $captcha = is_array($result['captcha'] ?? null) ? $result['captcha'] : ['required' => false, 'question' => ''];
        $hasSessionExpiredNotice = function_exists('hasExpiredAuthenticatedSessionNotice')
            && hasExpiredAuthenticatedSessionNotice();

        if ($error === '' && ($hasSessionExpiredNotice || (($_GET['session_expired'] ?? '') === '1'))) {
            $error = 'Votre session a expiré après une période d\'inactivité. Veuillez vous reconnecter.';
        }

        return $this->render('public/login', [
            'error' => $error,
            'redirectUrl' => $redirectUrl,
            'postedEmail' => (string) ($_POST['email'] ?? ''),
            'captcha' => $captcha,
        ]);
    }

    public function logout(): void
    {
        require_once appPath('config/session.php');

        $userController = new UserController();

        if (!$userController->logout()) {
            header('Location: ' . (isAuthenticated() ? publicRouteUrl('dashboard_redirect') : publicRouteUrl('login')));
            exit();
        }

        header('Location: ' . publicRouteUrl('login'));
        exit();
    }

    public function register(): string
    {
        require_once appPath('config/session.php');

        if (isAuthenticated()) {
            header('Location: ' . $this->resolveAuthenticatedRedirect());
            exit();
        }

        $userController = new UserController();
        $result = $userController->handleRegistration();
        $errors = is_array($result['errors'] ?? null) ? $result['errors'] : [];
        $data = is_array($result['data'] ?? null) ? $result['data'] : [];
        $captcha = is_array($result['captcha'] ?? null) ? $result['captcha'] : ['required' => false, 'question' => ''];

        return $this->render('public/inscription', [
            'errors' => $errors,
            'data' => $data,
            'countries' => $this->registrationCountries(),
            'captcha' => $captcha,
        ]);
    }

    public function checkSession(): string
    {
        require_once appPath('config/session.php');

        if (!headers_sent()) {
            header('Content-Type: application/json; charset=UTF-8');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        }

        return json_encode([
            'authenticated' => isAuthenticated(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{"authenticated":false}';
    }

    private function resolveAuthenticatedRedirect(): string
    {
        return match (getUserType()) {
            'admin' => appUrl('admin/dashboard'),
            'candidate' => appUrl('candidate/dashboard'),
            'voter' => appUrl('user/dashboard'),
            default => publicRouteUrl('home'),
        };
    }

    /**
     * @return array<int, string>
     */
    private function registrationCountries(): array
    {
        return [
            'Afghanistan',
            'Afrique du Sud',
            'Albanie',
            'Algérie',
            'Allemagne',
            'Andorre',
            'Angola',
            'Antigua-et-Barbuda',
            'Arabie saoudite',
            'Argentine',
            'Arménie',
            'Australie',
            'Autriche',
            'Azerbaïdjan',
            'Bahamas',
            'Bahreïn',
            'Bangladesh',
            'Barbade',
            'Belgique',
            'Belize',
            'Bénin',
            'Bhoutan',
            'Biélorussie',
            'Birmanie',
            'Bolivie',
            'Bosnie-Herzégovine',
            'Botswana',
            'Brésil',
            'Brunei',
            'Bulgarie',
            'Burkina Faso',
            'Burundi',
            'Cambodge',
            'Cameroun',
            'Canada',
            'Cap-Vert',
            'Chili',
            'Chine',
            'Chypre',
            'Colombie',
            'Comores',
            'Congo',
            'Corée du Nord',
            'Corée du Sud',
            'Costa Rica',
            'Croatie',
            'Cuba',
            'Danemark',
            'Djibouti',
            'Dominique',
            'Égypte',
            'Émirats arabes unis',
            'Équateur',
            'Érythrée',
            'Espagne',
            'Estonie',
            'États-Unis',
            'Éthiopie',
            'Fidji',
            'Finlande',
            'France',
            'Gabon',
            'Gambie',
            'Géorgie',
            'Ghana',
            'Grèce',
            'Grenade',
            'Guatemala',
            'Guinée',
            'Guinée équatoriale',
            'Guinée-Bissau',
            'Guyana',
            'Haïti',
            'Honduras',
            'Hongrie',
            'Îles Cook',
            'Îles Marshall',
            'Îles Salomon',
            'Inde',
            'Indonésie',
            'Irak',
            'Iran',
            'Irlande',
            'Islande',
            'Israël',
            'Italie',
            'Jamaïque',
            'Japon',
            'Jordanie',
            'Kazakhstan',
            'Kenya',
            'Kirghizistan',
            'Kiribati',
            'Kosovo',
            'Koweït',
            'Laos',
            'Lesotho',
            'Lettonie',
            'Liban',
            'Libéria',
            'Libye',
            'Liechtenstein',
            'Lituanie',
            'Luxembourg',
            'Macédoine du Nord',
            'Madagascar',
            'Malaisie',
            'Malawi',
            'Maldives',
            'Mali',
            'Malte',
            'Maroc',
            'Maurice',
            'Mauritanie',
            'Mexique',
            'Micronésie',
            'Moldavie',
            'Monaco',
            'Mongolie',
            'Monténégro',
            'Mozambique',
            'Namibie',
            'Nauru',
            'Népal',
            'Nicaragua',
            'Niger',
            'Nigéria',
            'Norvège',
            'Nouvelle-Zélande',
            'Oman',
            'Ouganda',
            'Ouzbékistan',
            'Pakistan',
            'Palaos',
            'Palestine',
            'Panama',
            'Papouasie-Nouvelle-Guinée',
            'Paraguay',
            'Pays-Bas',
            'Pérou',
            'Philippines',
            'Pologne',
            'Portugal',
            'Qatar',
            'République centrafricaine',
            'République démocratique du Congo',
            'République dominicaine',
            'République tchèque',
            'Roumanie',
            'Royaume-Uni',
            'Russie',
            'Rwanda',
            'Saint-Christophe-et-Niévès',
            'Sainte-Lucie',
            'Saint-Marin',
            'Saint-Vincent-et-les-Grenadines',
            'Salvador',
            'Samoa',
            'Sao Tomé-et-Principe',
            'Sénégal',
            'Serbie',
            'Seychelles',
            'Sierra Leone',
            'Singapour',
            'Slovaquie',
            'Slovénie',
            'Somalie',
            'Soudan',
            'Soudan du Sud',
            'Sri Lanka',
            'Suède',
            'Suisse',
            'Suriname',
            'Swaziland',
            'Syrie',
            'Tadjikistan',
            'Tanzanie',
            'Tchad',
            'Thaïlande',
            'Timor oriental',
            'Togo',
            'Tonga',
            'Trinité-et-Tobago',
            'Tunisie',
            'Turkménistan',
            'Turquie',
            'Tuvalu',
            'Ukraine',
            'Uruguay',
            'Vanuatu',
            'Vatican',
            'Venezuela',
            'Viêt Nam',
            'Yémen',
            'Zambie',
            'Zimbabwe',
            'Autre',
        ];
    }
}