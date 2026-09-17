<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CategorieModel;
use App\Models\UserModel;
use App\Models\VoteModel;
use App\Services\CsrfService;

final class VoterPageController extends Controller
{
    public function dashboard(): string
    {
        require_once appPath('config/session.php');

        requireRole('voter');

        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $userPseudonyme = (string) ($_SESSION['user_pseudonyme'] ?? 'Electeur');

        $userModel = new UserModel();
        $voteModel = new VoteModel();
        $categoryModel = new CategorieModel();

        $userData = $userModel->getUserById($userId);

        if (!is_array($userData)) {
            $userData = [
                'id_compte' => $userId,
                'pseudonyme' => $userPseudonyme,
                'email' => (string) ($_SESSION['user_email'] ?? ''),
                'photo_profil' => null,
            ];
        }

        $uploadDir = appPath('assets/images/profiles') . DIRECTORY_SEPARATOR;
        $web_path = appUrl('assets/images/profiles') . '/';
        $votesCount = (int) $voteModel->getUserVotesCount($userId);
        $activeElections = (int) ($categoryModel->getActiveCategoriesCount() ?? 0);
        $categories = $categoryModel->getAllCategoriesWithNominations() ?? [];
        $availableCategories = $this->buildAvailableCategoryCards(
            $categoryModel->getVotingCategoriesForUser($userId) ?? [],
            $voteModel,
            $userId
        );
        $hasVotedInActiveCategories = (bool) ($voteModel->hasUserVotedInActiveCategories($userId) ?? false);
        $initials = strtoupper(substr($userPseudonyme, 0, 2));
        $userPhoto = $userData['photo_profil'] ?? null;
        $hasPhoto = is_string($userPhoto) && $userPhoto !== '' && file_exists($uploadDir . $userPhoto);
        $recentVotes = $voteModel->getUserRecentVotes($userId, 3);
        $electionCards = $this->buildElectionCards($categories);

        return $this->render('user/dashboard', [
            'activeElections' => $activeElections,
            'availableCategories' => $availableCategories,
            'categoriesUrl' => appUrl('categories'),
            'checkSessionUrl' => appUrl('check-session'),
            'contactUrl' => appUrl('contact'),
            'copyrightYear' => date('Y'),
            'editProfileUrl' => appUrl('user/profile'),
            'electionCards' => $electionCards,
            'faqUrl' => appUrl('faq'),
            'hasPhoto' => $hasPhoto,
            'hasVotedInActiveCategories' => $hasVotedInActiveCategories,
            'initials' => $initials,
            'loginUrl' => appUrl('login'),
            'logoUrl' => appUrl('assets/images/logo.png'),
            'logoutToken' => CsrfService::token('logout'),
            'logoutUrl' => appUrl('logout'),
            'nomineesUrl' => appUrl('nominees'),
            'recentVotes' => $recentVotes,
            'resultsUrl' => appUrl('results'),
            'userDashboardCssUrl' => appUrl('assets/css/user-dashboard.css'),
            'userDashboardJsUrl' => appUrl('assets/js/user-dashboard.js'),
            'userPhoto' => $userPhoto,
            'userPseudonyme' => $userPseudonyme,
            'voteUrl' => appUrl('vote'),
            'votesCount' => $votesCount,
            'web_path' => $web_path,
            'aboutUrl' => appUrl('about'),
        ]);
    }

    public function vote(): string
    {
        require_once appPath('config/session.php');

        requireRole('voter');

        $voteController = new VoteController();
        $votePageUrl = appUrl('vote');
        $userDashboardUrl = appUrl('user/dashboard');
        $pageData = $voteController->showVotingPage();

        $categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;
        if (($categoryId === null || $categoryId <= 0) && isset($_GET['category'])) {
            $categoryId = (int) $_GET['category'];
        }
        if ($categoryId !== null && $categoryId <= 0) {
            $categoryId = null;
        }

        $requestedNominationId = isset($_GET['nomination']) ? (int) $_GET['nomination'] : null;
        if ($requestedNominationId !== null && $requestedNominationId <= 0) {
            $requestedNominationId = null;
        }

        $viewResults = isset($_GET['view']) && $_GET['view'] === 'results';
        $nominations = [];
        $currentCategory = null;
        $categoryVoteUrl = null;
        $categoryResultsUrl = null;
        $error = null;
        $success = false;
        $successMessage = null;
        $alreadyVoted = false;
        $categoryVotingAvailable = true;
        $preselectedNominationId = null;
        $preselectedNominationName = '';

        if ($categoryId !== null) {
            $categoryPageData = $voteController->showCategoryVotingPage($categoryId);
            $nominations = $categoryPageData['nominations'] ?? [];
            $currentCategory = $categoryPageData['current_category'] ?? null;
            $categoryVoteUrl = $votePageUrl . '?category_id=' . $categoryId;
            $categoryResultsUrl = $categoryVoteUrl . '&view=results';

            if (!$currentCategory) {
                header('Location: ' . $votePageUrl . '?error=invalid_category');
                exit();
            }

            $categoryVotingAvailable = (bool) ($categoryPageData['success'] ?? false);

            if (!$categoryVotingAvailable) {
                $error = $categoryPageData['message'] ?? 'Cette catégorie de vote n\'est pas disponible pour le moment.';
                $alreadyVoted = (bool) ($categoryPageData['already_voted'] ?? false);
            }

            if ($requestedNominationId !== null && $nominations !== []) {
                foreach ($nominations as $nominationOption) {
                    if ((int) ($nominationOption['id_nomination'] ?? 0) === $requestedNominationId) {
                        $preselectedNominationId = $requestedNominationId;
                        $preselectedNominationName = (string) ($nominationOption['libelle'] ?? '');
                        break;
                    }
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] === 'start_voting' && isset($_POST['category_id'])) {
                $result = $voteController->startCategoryVoting();

                if ($result['success']) {
                    header('Location: ' . $votePageUrl . '?' . http_build_query([
                        'category_id' => (int) $_POST['category_id'],
                    ]));
                    exit();
                }

                $error = $result['message'] ?? 'Impossible de démarrer le vote.';
                $alreadyVoted = (bool) ($result['already_voted'] ?? false);
            } elseif ($_POST['action'] === 'cast_vote' && isset($_POST['nomination_id'])) {
                $result = $voteController->castVote();

                if ($result['success']) {
                    $_SESSION['vote_success'] = true;
                    $_SESSION['vote_message'] = $result['message'];

                    $redirectParams = ['success' => 1];
                    if (!empty($_POST['category_id'])) {
                        $redirectParams['category_id'] = (int) $_POST['category_id'];
                    }

                    header('Location: ' . $votePageUrl . '?' . http_build_query($redirectParams));
                    exit();
                }

                $error = $result['message'] ?? 'Le vote n\'a pas pu être enregistré.';
                $alreadyVoted = (bool) ($result['already_voted'] ?? false);

                if ($alreadyVoted) {
                    $redirectParams = ['error' => 'already_voted'];
                    if (!empty($_POST['category_id'])) {
                        $redirectParams['category_id'] = (int) $_POST['category_id'];
                    }

                    header('Location: ' . $votePageUrl . '?' . http_build_query($redirectParams));
                    exit();
                }
            }
        }

        if (isset($_GET['success']) || isset($_SESSION['vote_success'])) {
            $success = true;
            $successMessage = $_SESSION['vote_message'] ?? 'Votre vote a été enregistré avec succès!';

            if (isset($_SESSION['vote_success'])) {
                unset($_SESSION['vote_success'], $_SESSION['vote_message']);
            }
        }

        $lastVote = null;
        $lastVoteCertificate = null;
        $lastVoteCertificateDate = null;
        $lastVoteCertificateReference = null;
        $votingStatuses = $pageData['voting_status'] ?? [];
        $totalCategories = count($votingStatuses);
        $votedCategories = 0;
        $availableCategories = 0;

        foreach ($votingStatuses as $status) {
            if (!empty($status['has_voted'])) {
                $votedCategories++;
            }

            if (!empty($status['can_vote'])) {
                $availableCategories++;
            }
        }

        $overviewProgressPercentage = $totalCategories > 0
            ? min(100, max(0, (int) round(($votedCategories / $totalCategories) * 100)))
            : 0;

        if (isset($_SESSION['last_vote']) && is_array($_SESSION['last_vote']) && ($_SESSION['last_vote']['is_current'] ?? false)) {
            $lastVote = $_SESSION['last_vote'];

            if (isset($lastVote['certificate']) && is_array($lastVote['certificate'])) {
                $lastVoteCertificate = $lastVote['certificate'];

                if (!empty($lastVoteCertificate['date_emission'])) {
                    $certificateTimestamp = strtotime((string) $lastVoteCertificate['date_emission']);
                    if ($certificateTimestamp !== false) {
                        $lastVoteCertificateDate = date('d/m/Y à H:i', $certificateTimestamp);
                    }
                }

                if (!empty($lastVoteCertificate['hash_certificat'])) {
                    $lastVoteCertificateReference = substr((string) $lastVoteCertificate['hash_certificat'], 0, 16) . '...';
                }
            }
        }

        if (isset($_GET['error'])) {
            switch ($_GET['error']) {
                case 'invalid_category':
                    $error = 'Catégorie invalide ou non trouvée.';
                    break;
                case 'already_voted':
                    $error = 'Vous avez déjà voté dans cette catégorie.';
                    $alreadyVoted = true;
                    break;
                case 'voting_closed':
                    $error = 'Les votes sont fermés pour cette catégorie.';
                    break;
                default:
                    $error = 'Une erreur est survenue.';
                    break;
            }
        }

        $userPseudonyme = (string) ($_SESSION['user_pseudonyme'] ?? 'Électeur');

        return $this->render('user/vote-page', [
            'aboutUrl' => appUrl('about'),
            'alreadyVoted' => $alreadyVoted,
            'availableCategories' => $availableCategories,
            'categoriesUrl' => appUrl('categories'),
            'categoryId' => $categoryId,
            'categoryResultsUrl' => $categoryResultsUrl,
            'categoryVoteUrl' => $categoryVoteUrl,
            'categoryVotingAvailable' => $categoryVotingAvailable,
            'castVoteCsrfToken' => CsrfService::token('cast_vote_voter'),
            'checkSessionUrl' => appUrl('check-session'),
            'contactUrl' => appUrl('contact'),
            'copyrightYear' => date('Y'),
            'currentCategory' => $currentCategory,
            'error' => $error,
            'faqUrl' => appUrl('faq'),
            'initials' => strtoupper(substr($userPseudonyme, 0, 2)),
            'lastVote' => $lastVote,
            'lastVoteCertificate' => $lastVoteCertificate,
            'lastVoteCertificateDate' => $lastVoteCertificateDate,
            'lastVoteCertificateReference' => $lastVoteCertificateReference,
            'loginUrl' => appUrl('login'),
            'logoUrl' => appUrl('assets/images/logo.png'),
            'nominations' => $nominations,
            'nomineesUrl' => appUrl('nominees'),
            'overviewProgressPercentage' => $overviewProgressPercentage,
            'pageData' => $pageData,
            'preselectedNominationId' => $preselectedNominationId,
            'preselectedNominationName' => $preselectedNominationName,
            'resultsUrl' => appUrl('results'),
            'startVotingCsrfToken' => CsrfService::token('start_voting_voter'),
            'success' => $success,
            'successMessage' => $successMessage,
            'totalCategories' => $totalCategories,
            'userDashboardCssUrl' => appUrl('assets/css/user-dashboard.css'),
            'userDashboardUrl' => $userDashboardUrl,
            'userPseudonyme' => $userPseudonyme,
            'voteCssUrl' => appUrl('assets/css/vote.css'),
            'votePageUrl' => $votePageUrl,
            'votedCategories' => $votedCategories,
            'viewResults' => $viewResults,
            'votingToken' => (string) ($_SESSION['voting_token'] ?? ''),
        ]);
    }

    public function changePassword(): string
    {
        require_once appPath('config/session.php');

        requireRole('voter');

        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $userPseudonyme = (string) ($_SESSION['user_pseudonyme'] ?? 'Électeur');
        $userModel = new UserModel();
        $errors = [];
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CsrfService::isValid($_POST['csrf_token'] ?? null, 'change_password_voter')) {
                $errors[] = 'La validation du formulaire a expiré. Veuillez réessayer.';
            }

            $currentPassword = (string) ($_POST['current_password'] ?? '');
            $newPassword = (string) ($_POST['new_password'] ?? '');
            $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

            if ($currentPassword === '') {
                $errors[] = 'Le mot de passe actuel est requis';
            }

            if ($newPassword === '') {
                $errors[] = 'Le nouveau mot de passe est requis';
            } elseif (strlen($newPassword) < 6) {
                $errors[] = 'Le nouveau mot de passe doit contenir au moins 6 caractères';
            } elseif ($currentPassword !== '' && $currentPassword === $newPassword) {
                $errors[] = 'Le nouveau mot de passe doit être différent du mot de passe actuel';
            }

            if ($newPassword !== $confirmPassword) {
                $errors[] = 'Les nouveaux mots de passe ne correspondent pas';
            }

            if ($errors === []) {
                if (!$userModel->verifyPassword($userId, $currentPassword)) {
                    $errors[] = 'Le mot de passe actuel est incorrect';
                } elseif ($userModel->updatePassword($userId, $newPassword)) {
                    $success = 'Mot de passe changé avec succès !';
                } else {
                    $errors[] = 'Une erreur est survenue lors de la mise à jour du mot de passe';
                }
            }
        }

        return $this->render('user/change-password-page', [
            'changePasswordCsrfToken' => CsrfService::token('change_password_voter'),
            'editProfileCssUrl' => appUrl('assets/css/edit-profile.css'),
            'editProfileUrl' => appUrl('user/profile'),
            'errors' => $errors,
            'initials' => strtoupper(substr($userPseudonyme, 0, 2)),
            'logoUrl' => appUrl('assets/images/logo.png'),
            'success' => $success,
            'userDashboardUrl' => appUrl('user/dashboard'),
            'userPseudonyme' => $userPseudonyme,
        ]);
    }

    public function editProfile(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/upload.php');

        requireRole('voter');

        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $userModel = new UserModel();
        $voteModel = new VoteModel();
        $userData = $userModel->getUserById($userId);

        if (!$userData) {
            $userData = [
                'date_naissance' => '1990-01-01',
                'email' => (string) ($_SESSION['user_email'] ?? ''),
                'genre' => '',
                'id_compte' => $userId,
                'pays' => 'France',
                'photo_profil' => null,
                'pseudonyme' => (string) ($_SESSION['user_pseudonyme'] ?? 'Électeur'),
                'role' => 'voter',
            ];
        }

        $errors = [];
        $success = '';
        $upload_dir = appPath('assets/images/profiles') . DIRECTORY_SEPARATOR;
        $web_path = appUrl('assets/images/profiles') . '/';
        $generatedAvatarPresets = [
            'avatar_teal' => ['#4FBDAB', '#3da895'],
            'avatar_pink' => ['#FF5A79', '#ff3d5e'],
            'avatar_gold' => ['#FFD166', '#ffc145'],
            'avatar_green' => ['#32D583', '#2bc174'],
        ];

        $countryOptions = ['France', 'Belgique', 'Suisse', 'Canada', 'Luxembourg', 'Autre'];
        $selectedCountry = trim((string) ($userData['pays'] ?? ''));

        if ($selectedCountry !== '' && !in_array($selectedCountry, $countryOptions, true)) {
            array_unshift($countryOptions, $selectedCountry);
        }

        if (!file_exists($upload_dir) && !mkdir($upload_dir, 0755, true)) {
            $errors['general'] = 'Erreur de configuration: Impossible de créer le dossier de téléchargement';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CsrfService::isValid($_POST['csrf_token'] ?? null, 'edit_profile_voter')) {
                $errors['general'] = 'La validation du formulaire a expiré. Veuillez réessayer.';
            }

            $pseudonyme = trim((string) ($_POST['pseudonyme'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $date_naissance = (string) ($_POST['date_naissance'] ?? '');
            $pays = (string) ($_POST['pays'] ?? '');
            $genre = (string) ($_POST['genre'] ?? '');

            if ($pseudonyme === '') {
                $errors['pseudonyme'] = 'Le pseudonyme est requis';
            } elseif (strlen($pseudonyme) < 3) {
                $errors['pseudonyme'] = 'Le pseudonyme doit contenir au moins 3 caractères';
            } elseif ($userModel->isPseudonymeTaken($pseudonyme, $userId)) {
                $errors['pseudonyme'] = 'Ce pseudonyme est déjà utilisé';
            }

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email invalide';
            } elseif ($userModel->isEmailTaken($email, $userId)) {
                $errors['email'] = 'Cet email est déjà utilisé';
            }

            if ($date_naissance === '') {
                $errors['date_naissance'] = 'La date de naissance est requise';
            } elseif (strtotime($date_naissance) > strtotime('-13 years')) {
                $errors['date_naissance'] = 'Vous devez avoir au moins 13 ans';
            }

            if ($pays === '') {
                $errors['pays'] = 'Le pays est requis';
            }

            $photo_profil = $userData['photo_profil'] ?? null;
            $photoChanged = false;
            $uploadedPhoto = $_FILES['photo_profil'] ?? null;
            $generatedAvatarChoice = trim((string) ($_POST['generated_avatar_choice'] ?? ''));
            $removePhoto = ($_POST['remove_photo'] ?? '') === '1';
            $avatarInitials = strtoupper(substr($pseudonyme !== '' ? $pseudonyme : (string) ($userData['pseudonyme'] ?? ''), 0, 2));

            $deleteCurrentPhoto = static function (?string $filename) use ($upload_dir): void {
                if ($filename && file_exists($upload_dir . $filename)) {
                    unlink($upload_dir . $filename);
                }
            };

            $buildGeneratedAvatar = static function (string $initials, array $colors): string {
                $gradientId = 'gradient_' . uniqid();
                $safeInitials = htmlspecialchars($initials, ENT_QUOTES | ENT_XML1, 'UTF-8');

                return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="256" height="256" viewBox="0 0 256 256" role="img" aria-label="Avatar {$safeInitials}">
    <defs>
        <linearGradient id="{$gradientId}" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="{$colors[0]}" />
            <stop offset="100%" stop-color="{$colors[1]}" />
        </linearGradient>
    </defs>
    <circle cx="128" cy="128" r="128" fill="url(#{$gradientId})" />
    <text x="50%" y="54%" text-anchor="middle" dominant-baseline="middle" font-family="Montserrat, Arial, sans-serif" font-size="88" font-weight="700" fill="#ffffff">{$safeInitials}</text>
</svg>
SVG;
            };

            if ($uploadedPhoto !== null && ($uploadedPhoto['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $allowed_types = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                ];
                $file_extension = uploadedImageExtension($uploadedPhoto, $allowed_types);

                if ($file_extension !== null) {
                    if (($uploadedPhoto['size'] ?? 0) <= 5 * 1024 * 1024) {
                        $new_filename = 'profile_' . $userId . '_' . time() . '.' . $file_extension;
                        $upload_path = $upload_dir . $new_filename;

                        if (is_uploaded_file((string) $uploadedPhoto['tmp_name']) && move_uploaded_file((string) $uploadedPhoto['tmp_name'], $upload_path)) {
                            $deleteCurrentPhoto($userData['photo_profil'] ?? null);
                            $photo_profil = $new_filename;
                            $photoChanged = true;
                        } else {
                            $errors['photo_profil'] = 'Erreur lors du téléchargement de l\'image';
                        }
                    } else {
                        $errors['photo_profil'] = 'L\'image est trop volumineuse (max 5MB)';
                    }
                } else {
                    $errors['photo_profil'] = 'Type de fichier non autorisé (JPEG, PNG, GIF seulement)';
                }
            } elseif ($generatedAvatarChoice !== '') {
                if (!isset($generatedAvatarPresets[$generatedAvatarChoice])) {
                    $errors['photo_profil'] = 'Avatar généré invalide';
                } else {
                    $new_filename = 'profile_' . $userId . '_' . time() . '.svg';
                    $upload_path = $upload_dir . $new_filename;
                    $svgContent = $buildGeneratedAvatar($avatarInitials, $generatedAvatarPresets[$generatedAvatarChoice]);

                    if (file_put_contents($upload_path, $svgContent) !== false) {
                        $deleteCurrentPhoto($userData['photo_profil'] ?? null);
                        $photo_profil = $new_filename;
                        $photoChanged = true;
                    } else {
                        $errors['photo_profil'] = 'Impossible d\'enregistrer l\'avatar généré';
                    }
                }
            } elseif ($removePhoto) {
                $deleteCurrentPhoto($userData['photo_profil'] ?? null);
                $photo_profil = null;
                $photoChanged = true;
            } elseif ($uploadedPhoto !== null && ($uploadedPhoto['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $upload_errors = [
                    UPLOAD_ERR_INI_SIZE => 'Le fichier dépasse la taille maximale autorisée par le serveur',
                    UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la taille maximale autorisée par le formulaire',
                    UPLOAD_ERR_PARTIAL => 'Le téléchargement a été interrompu',
                    UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant',
                    UPLOAD_ERR_CANT_WRITE => 'Erreur d\'écriture sur le disque',
                    UPLOAD_ERR_EXTENSION => 'Une extension PHP a arrêté le téléchargement',
                ];

                $error_code = (int) $uploadedPhoto['error'];
                $errors['photo_profil'] = $upload_errors[$error_code] ?? 'Erreur inconnue lors du téléchargement';
            }

            if ($errors === [] || ($errors === ['general' => 'La validation du formulaire a expiré. Veuillez réessayer.'] && !CsrfService::isValid($_POST['csrf_token'] ?? null, 'edit_profile_voter'))) {
                // noop, handled by existing errors
            }

            if (empty($errors)) {
                $updateData = [
                    'date_naissance' => $date_naissance,
                    'email' => $email,
                    'genre' => $genre,
                    'pays' => $pays,
                    'pseudonyme' => $pseudonyme,
                ];

                if ($photoChanged || $photo_profil) {
                    $updateData['photo_profil'] = $photo_profil;
                }

                if ($userModel->updateUserProfile($userId, $updateData)) {
                    $_SESSION['user_pseudonyme'] = $pseudonyme;
                    $_SESSION['user_email'] = $email;

                    $userData['pseudonyme'] = $pseudonyme;
                    $userData['email'] = $email;
                    $userData['date_naissance'] = $date_naissance;
                    $userData['pays'] = $pays;
                    $userData['genre'] = $genre;
                    $userData['photo_profil'] = $photo_profil;

                    $success = 'Profil mis à jour avec succès!';
                } else {
                    $errors['general'] = 'Une erreur est survenue lors de la mise à jour du profil';
                }
            }

            $selectedCountry = trim((string) ($userData['pays'] ?? ''));
            if ($selectedCountry !== '' && !in_array($selectedCountry, $countryOptions, true)) {
                array_unshift($countryOptions, $selectedCountry);
            }
        }

        $initials = strtoupper(substr((string) ($userData['pseudonyme'] ?? 'Électeur'), 0, 2));

        return $this->render('user/profile-page', [
            'aboutUrl' => appUrl('about'),
            'categoriesUrl' => appUrl('categories'),
            'changePasswordUrl' => appUrl('user/password'),
            'contactUrl' => appUrl('contact'),
            'copyrightYear' => date('Y'),
            'countryOptions' => $countryOptions,
            'editProfileCssUrl' => appUrl('assets/css/edit-profile.css'),
            'editProfileCsrfToken' => CsrfService::token('edit_profile_voter'),
            'errors' => $errors,
            'faqUrl' => appUrl('faq'),
            'generatedAvatarPresets' => $generatedAvatarPresets,
            'initials' => $initials,
            'logoUrl' => appUrl('assets/images/logo.png'),
            'memberSinceLabel' => date('m/Y'),
            'nomineesUrl' => appUrl('nominees'),
            'resultsUrl' => appUrl('results'),
            'selectedCountry' => $selectedCountry,
            'success' => $success,
            'upload_dir' => $upload_dir,
            'userDashboardUrl' => appUrl('user/dashboard'),
            'userData' => $userData,
            'votesCount' => (int) $voteModel->getUserVotesCount($userId),
            'web_path' => $web_path,
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $categories
     * @return array<int, array<string, mixed>>
     */
    private function buildAvailableCategoryCards(array $categories, VoteModel $voteModel, int $userId): array
    {
        $cards = [];

        foreach ($categories as $category) {
            $categoryId = (int) ($category['id_categorie'] ?? 0);
            $nominationCount = (int) ($category['nomination_count'] ?? 0);
            $hasVoted = $categoryId > 0 ? $voteModel->hasUserVotedInCategory($userId, $categoryId) : false;

            $cards[] = [
                'can_vote' => !$hasVoted && $nominationCount > 0,
                'date_fin_votes' => (string) ($category['date_fin_votes'] ?? ''),
                'description' => (string) ($category['description'] ?? ''),
                'has_voted' => $hasVoted,
                'icon' => $this->platformIcon((string) ($category['plateforme_cible'] ?? 'General')),
                'id' => $categoryId,
                'name' => (string) ($category['nom'] ?? 'Categorie sans nom'),
                'nomination_count' => $nominationCount,
                'platform' => (string) ($category['plateforme_cible'] ?? 'General'),
            ];
        }

        return $cards;
    }

    /**
     * @param array<int, array<string, mixed>> $categories
     * @return array<int, array<string, mixed>>
     */
    private function buildElectionCards(array $categories): array
    {
        $editions = [];

        foreach ($categories as $category) {
            if (!isset($category['edition_nom'], $category['edition_annee'])) {
                continue;
            }

            $editionName = (string) $category['edition_nom'];
            $editionYear = (string) $category['edition_annee'];
            $editionKey = $editionName . '_' . $editionYear;

            if (!isset($editions[$editionKey])) {
                $editions[$editionKey] = [
                    'annee' => $editionYear,
                    'categories' => [],
                    'date_fin' => (string) ($category['date_fin'] ?? ''),
                    'nom' => $editionName,
                    'total_candidates' => 0,
                ];
            }

            $editions[$editionKey]['categories'][] = (string) ($category['nom'] ?? 'Categorie');
            $editions[$editionKey]['total_candidates'] += (int) ($category['nomination_count'] ?? 0);
        }

        return array_values($editions);
    }

    private function platformIcon(string $platform): string
    {
        return match ($platform) {
            'Photographe' => 'fa-camera',
            'Streamer' => 'fa-gamepad',
            'Musicien' => 'fa-music',
            'Youtuber', 'YouTube' => 'fa-youtube',
            'Instagram' => 'fa-instagram',
            'TikTok' => 'fa-tiktok',
            'Twitter' => 'fa-twitter',
            'Facebook' => 'fa-facebook',
            'Twitch' => 'fa-twitch',
            'Spotify' => 'fa-spotify',
            default => 'fa-tag',
        };
    }
}
