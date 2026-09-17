<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Categorie;
use App\Models\Nomination;
use App\Services\CategorieService;
use App\Services\NominationService;
use App\Services\ResultsService;
use PDO;
use Throwable;

final class PublicPageController extends Controller
{
    public function home(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/permissions.php');

        return $this->render('public/home', [
            'heroStats' => [
                'categories' => 12,
                'platforms' => 5,
                'votes' => '50K+',
                'candidatures' => 1000,
            ],
            'sectionStats' => [
                'categories' => 50,
                'candidatures' => 1000,
                'votes' => '50K+',
                'platforms' => 5,
            ],
            'aboutUrl' => publicRouteUrl('about'),
            'voteLink' => $this->resolveVoteLink(),
        ]);
    }

    public function about(): string
    {
        require_once appPath('config/session.php');

        return $this->render('public/about');
    }

    public function contact(): string
    {
        require_once appPath('config/session.php');

        return $this->render('public/contact');
    }

    public function faq(): string
    {
        require_once appPath('config/session.php');

        return $this->render('public/faq', [
            'contactUrl' => publicRouteUrl('contact'),
            'categoriesUrl' => publicRouteUrl('categories'),
            'resultsUrl' => publicRouteUrl('results'),
        ]);
    }

    public function cgu(): string
    {
        require_once appPath('config/session.php');

        return $this->render('public/cgu');
    }

    public function privacy(): string
    {
        require_once appPath('config/session.php');

        return $this->render('public/privacy');
    }

    public function categories(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/database.php');

        $page = $this->readPositiveInt($_GET['page'] ?? 1, 1);
        $perPage = $this->sanitizePerPage($_GET['per_page'] ?? 12, [6, 12, 24, 48], 12);
        $activeEdition = ['id_edition' => null, 'annee' => date('Y')];
        $categoryCards = [];
        $platforms = [];
        $platformIcons = [];
        $pageStats = ['categories' => 0, 'platforms' => 0, 'nominees' => 0];
        $pagination = $this->buildPaginationData($page, $perPage, 0, []);
        $pdo = function_exists('tryGetDB') ? tryGetDB() : null;

        if ($pdo === null) {
            return $this->render('public/categories', compact(
                'activeEdition',
                'categoryCards',
                'platforms',
                'platformIcons',
                'pageStats',
                'pagination'
            ));
        }

        try {
            $categoryService = new CategorieService($pdo);
            $activeEdition = $this->getActiveEdition($pdo);
            $editionId = (int) ($activeEdition['id_edition'] ?? 0);
            $categories = $editionId > 0 ? $categoryService->getAllCategoriesByEdition($editionId) : [];
            $platforms = $this->extractPlatforms($categories);
            $platformIcons = $this->buildPlatformIcons($platforms);
            $allCategoryCards = $this->buildCategoryCards($categories, $categoryService, $pdo);

            $pagination = $this->buildPaginationData($page, $perPage, count($allCategoryCards), []);
            $categoryCards = array_slice($allCategoryCards, $pagination['offset'], $pagination['perPage']);

            $pageStats = [
                'categories' => count($categories),
                'platforms' => count($platforms),
                'nominees' => $editionId > 0 ? $this->countTotalNominations($pdo, $editionId) : 0,
            ];
        } catch (Throwable $exception) {
            error_log('Category retrieval error: ' . $exception->getMessage());
            http_response_code(500);
        }

        return $this->render('public/categories', compact(
            'activeEdition',
            'categoryCards',
            'platforms',
            'platformIcons',
            'pageStats',
            'pagination'
        ));
    }

    public function nominees(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/permissions.php');
        require_once appPath('config/database.php');

        $page = $this->readPositiveInt($_GET['page'] ?? 1, 1);
        $perPage = $this->sanitizePerPage($_GET['per_page'] ?? 12, [6, 12, 24, 48], 12);
        $categoryFilter = isset($_GET['category']) ? max(0, (int) $_GET['category']) : 0;
        $categoryName = isset($_GET['name']) ? urldecode((string) $_GET['name']) : '';
        $platformFilter = $this->normalizePlatform((string) ($_GET['platform'] ?? ''));
        $selectedPlatformLabel = '';
        $nomineeCards = [];
        $allCategories = [];
        $platforms = [];
        $nomineesUrl = publicRouteUrl('nominees');
        $defaultNomineeImageUrl = appUrl('assets/images/Nominees/nominee1.jpg');
        $isAuthenticated = isAuthenticated();
        $loginUrl = publicRouteUrl('login');
        $pagination = $this->buildPaginationData($page, $perPage, 0, [
            'category' => $categoryFilter > 0 ? $categoryFilter : null,
            'name' => $categoryName !== '' ? $categoryName : null,
            'platform' => $platformFilter !== '' ? $platformFilter : null,
        ]);
        $pdo = function_exists('tryGetDB') ? tryGetDB() : null;

        if ($pdo === null) {
            return $this->render('public/nominees', compact(
                'categoryFilter',
                'categoryName',
                'platformFilter',
                'selectedPlatformLabel',
                'nomineeCards',
                'allCategories',
                'platforms',
                'nomineesUrl',
                'defaultNomineeImageUrl',
                'isAuthenticated',
                'loginUrl',
                'pagination'
            ));
        }

        try {
            $nominationService = new NominationService($pdo);
            $nominations = $categoryFilter > 0
                ? $nominationService->getNominationsByCategory($categoryFilter)
                : $nominationService->getAllNominations();

            if ($categoryFilter > 0) {
                $currentCategory = $nominationService->getCategoryById($categoryFilter);
                $categoryName = $currentCategory['nom'] ?? $categoryName;
            }

            $allCategories = $nominationService->getAllCategories();
            $platforms = $nominationService->getAllPlatforms();
            $selectedPlatformLabel = $this->resolveSelectedPlatformLabel($platforms, $platformFilter);
            $allNomineeCards = $this->buildNomineeCards($nominations, $nominationService, $pdo, $defaultNomineeImageUrl);

            if ($platformFilter !== '') {
                $allNomineeCards = $this->filterNomineeCardsByPlatform($allNomineeCards, $platformFilter);
            }

            $pagination = $this->buildPaginationData($page, $perPage, count($allNomineeCards), [
                'category' => $categoryFilter > 0 ? $categoryFilter : null,
                'name' => $categoryName !== '' ? $categoryName : null,
                'platform' => $platformFilter !== '' ? $platformFilter : null,
            ]);
            $nomineeCards = array_slice($allNomineeCards, $pagination['offset'], $pagination['perPage']);
        } catch (Throwable $exception) {
            error_log('Erreur récupération nominés: ' . $exception->getMessage());
            http_response_code(500);
        }

        return $this->render('public/nominees', compact(
            'categoryFilter',
            'categoryName',
            'platformFilter',
            'selectedPlatformLabel',
            'nomineeCards',
            'allCategories',
            'platforms',
            'nomineesUrl',
            'defaultNomineeImageUrl',
            'isAuthenticated',
            'loginUrl',
            'pagination'
        ));
    }

    public function nominee(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/permissions.php');
        require_once appPath('config/database.php');

        $profileUnavailable = false;
        $nominationId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $nominationFound = false;
        $headerCssUrl = appUrl('assets/css/header.css');
        $footerCssUrl = appUrl('assets/css/footer.css');
        $nomineesCssUrl = appUrl('assets/css/nominees.css');
        $nomineesUrl = publicRouteUrl('nominees');
        $defaultImageUrl = appUrl('assets/images/Nominees/nominee1.jpg');
        $voteLink = $nomineesUrl;
        $voteButtonClass = 'btn-vote btn-disabled';
        $voteButtonText = 'Votes fermés';
        $nominationImageUrl = $defaultImageUrl;
        $contentUrl = null;
        $categoryName = 'Catégorie inconnue';
        $nominationLabel = 'Nomination introuvable';
        $platformName = '';
        $platformIcon = '';
        $argumentaire = '';
        $formattedVotes = '0';
        $emptyStateTitle = 'Nomination introuvable';
        $emptyStateMessage = "Le profil demandé n'existe pas ou n'est plus disponible. Vous pouvez revenir à la liste complète des nominés pour continuer votre navigation.";
        $pdo = function_exists('tryGetDB') ? tryGetDB() : null;

        if ($pdo === null) {
            $profileUnavailable = true;
            $emptyStateTitle = 'Service momentanément indisponible';
            $emptyStateMessage = 'Le profil demandé ne peut pas être chargé pour le moment. Veuillez réessayer un peu plus tard.';
            http_response_code(503);
        }

        if ($pdo !== null && $nominationId > 0) {
            try {
                $nominationService = new NominationService($pdo);
                $nomination = $nominationService->getNominationById($nominationId);

                if ($nomination !== null) {
                    $nominationFound = true;
                    $category = $nominationService->getCategoryById($nomination->getIdCategorie());
                    $votesCount = $nominationService->countVotesForNomination($nomination->getIdNomination() ?? 0);
                    $categoryId = $nomination->getIdCategorie();
                    $isVotingOpen = $this->isCategoryVotingOpen($pdo, $categoryId);

                    $categoryName = $category['nom'] ?? $categoryName;
                    $nominationLabel = $nomination->getLibelle();
                    $platformName = $nomination->getPlateforme();
                    $platformIcon = $this->getPlatformIcon($platformName);
                    $argumentaire = $nomination->getArgumentaire();
                    $formattedVotes = $this->formatVoteCount($votesCount);
                    $voteLink = $this->resolveVoteButtonLink($nomination->getIdNomination() ?? 0, $categoryId, $isVotingOpen);
                    $voteButtonClass = $this->resolveVoteButtonClass($isVotingOpen);
                    $voteButtonText = $this->resolveNomineeVoteButtonText($categoryId, $isVotingOpen);
                    $nominationImageUrl = resolveAppAssetUrl($nomination->getUrlImage(), 'assets/images/Nominees/nominee1.jpg')
                        ?? $defaultImageUrl;
                    $contentUrl = $nomination->getUrlContenu();
                }
            } catch (Throwable $exception) {
                error_log('Erreur profil nominé: ' . $exception->getMessage());
                $profileUnavailable = true;
                $emptyStateTitle = 'Service momentanément indisponible';
                $emptyStateMessage = 'Le profil demandé ne peut pas être chargé pour le moment. Veuillez réessayer un peu plus tard.';
                http_response_code(500);
            }
        }

        if (!$profileUnavailable && !$nominationFound) {
            http_response_code(404);
        }

        return $this->render('public/nominee', compact(
            'nominationFound',
            'headerCssUrl',
            'footerCssUrl',
            'nomineesCssUrl',
            'nomineesUrl',
            'defaultImageUrl',
            'voteLink',
            'voteButtonClass',
            'voteButtonText',
            'nominationImageUrl',
            'contentUrl',
            'categoryName',
            'nominationLabel',
            'platformName',
            'platformIcon',
            'argumentaire',
            'formattedVotes',
            'emptyStateTitle',
            'emptyStateMessage'
        ));
    }

    public function results(): string
    {
        require_once appPath('config/session.php');
        require_once appPath('config/database.php');

        $page = $this->readPositiveInt($_GET['page'] ?? 1, 1);
        $perPage = $this->sanitizePerPage($_GET['per_page'] ?? 12, [6, 12, 24, 48], 12);
        $status = 'error';
        $editionId = 1;
        $editionYear = date('Y');
        $editionName = 'Social Media Awards';
        $dateDebut = null;
        $dateFin = null;
        $grandWinners = [];
        $categoryResults = [];
        $globalStats = $this->emptyResultsStats();
        $availableEditions = [];
        $canShowResults = false;
        $showWarning = false;
        $votePageUrl = appUrl('vote');
        $pagination = $this->buildPaginationData($page, $perPage, 0, [
            'edition' => isset($_GET['edition']) && is_numeric((string) $_GET['edition']) ? (int) $_GET['edition'] : null,
        ]);
        $pdo = function_exists('tryGetDB') ? tryGetDB() : null;

        if ($pdo === null) {
            return $this->render('public/results', compact(
                'status',
                'editionId',
                'editionYear',
                'editionName',
                'dateDebut',
                'dateFin',
                'grandWinners',
                'categoryResults',
                'globalStats',
                'availableEditions',
                'canShowResults',
                'showWarning',
                'votePageUrl',
                'pagination'
            ));
        }

        try {
            $resultsService = new ResultsService($pdo);
            $editionId = $this->resolveResultsEditionId($resultsService);
            $edition = $this->getEditionById($pdo, $editionId);

            if ($edition === null) {
                throw new \RuntimeException("Édition non trouvée: $editionId");
            }

            $editionYear = $edition['annee'] ?? date('Y');
            $editionName = $edition['nom'] ?? 'Social Media Awards';
            $dateDebut = $edition['date_debut'] ?? null;
            $dateFin = $edition['date_fin'] ?? null;
            $status = $this->resolveResultsStatus($edition, $dateDebut, $dateFin);
            $canShowResults = $status === 'voting_finished';
            $showWarning = $status === 'voting_active';

            if ($canShowResults) {
                $grandWinners = $this->prepareGrandWinners($resultsService->getGrandWinners($editionId));
                $allCategoryResults = $resultsService->getResultsByCategory($editionId);
                $pagination = $this->buildPaginationData($page, $perPage, count($allCategoryResults), [
                    'edition' => $editionId,
                ]);
                $categoryResults = array_slice($allCategoryResults, $pagination['offset'], $pagination['perPage']);
                $globalStats = $resultsService->getGlobalStatistics($editionId);
            }

            $availableEditions = $this->prepareAvailableEditions($resultsService);
        } catch (Throwable $exception) {
            error_log('Erreur results.php: ' . $exception->getMessage());
            $status = 'error';
            http_response_code(500);
        }

        return $this->render('public/results', compact(
            'status',
            'editionId',
            'editionYear',
            'editionName',
            'dateDebut',
            'dateFin',
            'grandWinners',
            'categoryResults',
            'globalStats',
            'availableEditions',
            'canShowResults',
            'showWarning',
            'votePageUrl',
            'pagination'
        ));
    }

    private function resolveVoteLink(): string
    {
        if (isAuthenticated()) {
            return match (getUserType()) {
                'voter' => publicRouteUrl('vote'),
                'candidate' => appUrl('candidate/dashboard'),
                'admin' => appUrl('admin/dashboard'),
                default => publicRouteUrl('home'),
            };
        }

        $votePath = publicRouteUrl('vote');

        return publicRouteUrl('login', ['redirect' => $votePath]);
    }

    /**
     * @return array<string, mixed>
     */
    private function getActiveEdition(PDO $pdo): array
    {
        $sql = 'SELECT id_edition, annee, nom, date_debut_candidatures, date_fin_candidatures, date_debut, date_fin, est_active, theme, image, description FROM edition WHERE est_active = 1 ORDER BY annee DESC LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['id_edition' => null, 'annee' => date('Y')];
    }

    /**
     * @param array<int, mixed> $categories
     * @return array<int, string>
     */
    private function extractPlatforms(array $categories): array
    {
        $platforms = [];

        foreach ($categories as $category) {
            if (!$category instanceof Categorie) {
                continue;
            }

            $platform = $category->getPlateformeCible();

            if ($platform && !in_array($platform, $platforms, true)) {
                $platforms[] = $platform;
            }
        }

        return $platforms;
    }

    /**
     * @param array<int, string> $platforms
     * @return array<string, string>
     */
    private function buildPlatformIcons(array $platforms): array
    {
        $icons = [];

        foreach ($platforms as $platform) {
            $icons[$platform] = $this->getPlatformIcon($platform);
        }

        return $icons;
    }

    /**
     * @param array<int, mixed> $categories
     * @return array<int, array<string, mixed>>
     */
    private function buildCategoryCards(array $categories, CategorieService $categoryService, PDO $pdo): array
    {
        $cards = [];

        foreach ($categories as $category) {
            if (!$category instanceof Categorie) {
                continue;
            }

            $categoryId = (int) $category->getIdCategorie();
            $name = $category->getNom();
            $platform = $category->getPlateformeCible();
            $description = $category->getDescription()
                ?: $this->generateCategoryDescription($name, $platform ?: 'les réseaux sociaux');

            $cards[] = [
                'id' => $categoryId,
                'name' => $name,
                'encodedName' => rawurlencode($name),
                'description' => $description,
                'platform' => $platform,
                'platformClass' => $platform ? strtolower($platform) : 'all',
                'platformIcon' => $this->getPlatformIcon($platform),
                'categoryIcon' => $this->getCategoryIcon($name),
                'additionalPlatforms' => $this->getAdditionalPlatforms($name, $platform),
                'nomineesCount' => $categoryService->countNominationsByCategory($categoryId),
                'formattedVotes' => $this->formatVoteCount($this->countVotesByCategory($pdo, $categoryId)),
            ];
        }

        return $cards;
    }

    private function countTotalNominations(PDO $pdo, int $editionId): int
    {
        try {
            $sql = 'SELECT COUNT(*) as total FROM nomination n JOIN categorie c ON n.id_categorie = c.id_categorie WHERE c.id_edition = :edition_id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':edition_id' => $editionId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) ($result['total'] ?? 0);
        } catch (Throwable) {
            return 0;
        }
    }

    private function countVotesByCategory(PDO $pdo, int $categoryId): int
    {
        try {
            $sql = 'SELECT COUNT(*) as total FROM vote v JOIN nomination n ON v.id_nomination = n.id_nomination WHERE n.id_categorie = :category_id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':category_id' => $categoryId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) ($result['total'] ?? 0);
        } catch (Throwable) {
            return 0;
        }
    }

    private function formatVoteCount(int $count): string
    {
        if ($count >= 1000000) {
            return round($count / 1000000, 1) . 'M';
        }

        if ($count >= 1000) {
            return round($count / 1000, 1) . 'K';
        }

        return (string) $count;
    }

    private function getPlatformIcon(?string $platform): string
    {
        if ($platform === null || $platform === '') {
            return 'fas fa-globe';
        }

        $icons = [
            'tiktok' => 'fab fa-tiktok',
            'instagram' => 'fab fa-instagram',
            'youtube' => 'fab fa-youtube',
            'twitter' => 'fab fa-twitter',
            'facebook' => 'fab fa-facebook',
            'spotify' => 'fab fa-spotify',
            'twitch' => 'fab fa-twitch',
            'x' => 'fab fa-x-twitter',
        ];

        return $icons[strtolower($platform)] ?? 'fas fa-globe';
    }

    private function getCategoryIcon(string $categoryName): string
    {
        $keywords = [
            'révélation' => 'fas fa-star',
            'podcast' => 'fas fa-podcast',
            'vidéo' => 'fas fa-video',
            'photo' => 'fas fa-camera',
            'influenceur' => 'fas fa-user-friends',
            'branded' => 'fas fa-briefcase',
            'éducatif' => 'fas fa-graduation-cap',
            'humoristique' => 'fas fa-laugh',
            'tiktok' => 'fab fa-tiktok',
            'instagram' => 'fab fa-instagram',
            'youtube' => 'fab fa-youtube',
        ];

        $nameLower = strtolower($categoryName);

        foreach ($keywords as $keyword => $icon) {
            if (str_contains($nameLower, $keyword)) {
                return $icon;
            }
        }

        return 'fas fa-trophy';
    }

    private function generateCategoryDescription(string $categoryName, string $platform): string
    {
        $descriptions = [
            'tiktok' => "Les talents TikTok les plus innovants et créatifs qui ont marqué l'année par leur contenu unique",
            'instagram' => 'Les créateurs Instagram qui transforment le quotidien en art visuel et fédèrent leur communauté',
            'youtube' => 'Les YouTubeurs dont les contenus éducatifs, divertissants ou innovants ont captivé des millions de spectateurs',
            'podcast' => 'Les podcasts les plus influents qui ont marqué leur audience par des échanges à fort impact',
            'révélation' => 'Les nouveaux talents qui ont connu une progression exceptionnelle et redéfini leur univers',
            'branded' => "Les collaborations marque-créateur les plus réussies et authentiques de l'année",
        ];

        $nameLower = strtolower($categoryName);

        foreach ($descriptions as $keyword => $description) {
            if (str_contains($nameLower, $keyword)) {
                return $description;
            }
        }

        $platformText = ($platform !== '' && $platform !== 'Toutes') ? 'sur ' . ucfirst($platform) : 'sur les réseaux sociaux';

        return "Catégorie qui célèbre l'excellence et l'innovation dans la création de contenu " . $platformText;
    }

    /**
     * @return array<int, array{name: string, class: string, icon: string}>
     */
    private function getAdditionalPlatforms(string $categoryName, ?string $primaryPlatform): array
    {
        $categoryNameLower = strtolower($categoryName);

        if (!str_contains($categoryNameLower, 'multi') && !str_contains($categoryNameLower, 'cross')) {
            return [];
        }

        $platforms = [];

        foreach (['Instagram', 'TikTok', 'YouTube', 'Facebook'] as $platform) {
            if ($platform === $primaryPlatform) {
                continue;
            }

            $platforms[] = [
                'name' => $platform,
                'class' => strtolower($platform),
                'icon' => $this->getPlatformIcon($platform),
            ];
        }

        return $platforms;
    }

    /**
     * @param array<int, mixed> $nominations
     * @return array<int, array<string, mixed>>
     */
    private function buildNomineeCards(
        array $nominations,
        NominationService $nominationService,
        PDO $pdo,
        string $defaultNomineeImageUrl
    ): array {
        $cards = [];
        $categoryCache = [];
        $votingAvailability = [];

        foreach ($nominations as $nomination) {
            if (!$nomination instanceof Nomination) {
                continue;
            }

            $nominationId = (int) ($nomination->getIdNomination() ?? 0);
            $categoryId = $nomination->getIdCategorie();
            $categoryCache[$categoryId] ??= $nominationService->getCategoryById($categoryId) ?? [];
            $categoryInfo = $categoryCache[$categoryId];
            $categoryName = (string) ($categoryInfo['nom'] ?? '');
            $platform = $nomination->getPlateforme();
            $isVotingOpen = $votingAvailability[$categoryId] ??= $this->isCategoryVotingOpen($pdo, $categoryId);

            $cards[] = [
                'id' => $nominationId,
                'name' => $nomination->getLibelle(),
                'platform' => $platform,
                'platformSlug' => $this->normalizePlatform($platform),
                'platformIcon' => $this->getPlatformIcon($platform),
                'categoryId' => $categoryId,
                'categoryName' => $categoryName,
                'description' => $this->resolveNomineeDescription($nomination, $categoryName, $platform),
                'imageUrl' => resolveAppAssetUrl($nomination->getUrlImage(), 'assets/images/Nominees/nominee1.jpg')
                    ?? $defaultNomineeImageUrl,
                'formattedVotes' => $this->formatVoteCount($nominationService->countVotesForNomination($nominationId)),
                'isVotingOpen' => $isVotingOpen,
                'voteLink' => $this->resolveVoteButtonLink($nominationId, $categoryId, $isVotingOpen),
                'voteButtonText' => $this->resolveVoteButtonText($categoryId, $isVotingOpen),
                'voteButtonClass' => $this->resolveVoteButtonClass($isVotingOpen),
            ];
        }

        return $cards;
    }

    /**
     * @param array<int, array<string, mixed>> $nomineeCards
     * @return array<int, array<string, mixed>>
     */
    private function filterNomineeCardsByPlatform(array $nomineeCards, string $platformFilter): array
    {
        if ($platformFilter === '') {
            return $nomineeCards;
        }

        return array_values(array_filter(
            $nomineeCards,
            fn (array $card): bool => $this->normalizePlatform((string) ($card['platformSlug'] ?? $card['platform'] ?? '')) === $platformFilter
        ));
    }

    /**
     * @param array<int, string> $platforms
     */
    private function resolveSelectedPlatformLabel(array $platforms, string $platformFilter): string
    {
        if ($platformFilter === '') {
            return '';
        }

        foreach ($platforms as $platform) {
            if ($this->normalizePlatform($platform) === $platformFilter) {
                return $platform;
            }
        }

        return ucfirst($platformFilter);
    }

    private function normalizePlatform(?string $platform): string
    {
        return strtolower(trim((string) $platform));
    }

    private function resolveNomineeDescription(Nomination $nomination, string $categoryName, string $platform): string
    {
        $argument = trim($nomination->getArgumentaire());

        if ($argument !== '') {
            return strlen($argument) > 120 ? substr($argument, 0, 120) . '...' : $argument;
        }

        return $this->generateNomineeDescription(
            $categoryName !== '' ? $categoryName : 'Catégorie',
            $platform !== '' ? $platform : 'plateforme',
            $nomination->getLibelle()
        );
    }

    private function generateNomineeDescription(string $categoryName, string $platform, string $nomineeName): string
    {
        $descriptions = [
            'tiktok' => "Créateur TikTok innovant connu pour $nomineeName",
            'instagram' => "Influenceur Instagram avec un contenu visuel unique pour $nomineeName",
            'youtube' => "YouTubeur produisant du contenu de qualité sur $nomineeName",
            'podcast' => "Podcast engageant et informatif par $nomineeName",
            'révélation' => "Nouveau talent en pleine ascension : $nomineeName",
        ];

        $nameLower = strtolower($categoryName . ' ' . $platform);

        foreach ($descriptions as $keyword => $description) {
            if (str_contains($nameLower, $keyword)) {
                return $description;
            }
        }

        return "Candidat exceptionnel dans la catégorie $categoryName";
    }

    private function isCategoryVotingOpen(PDO $pdo, int $categoryId): bool
    {
        if ($categoryId <= 0) {
            return false;
        }

        try {
            $sql = '
                SELECT CASE
                    WHEN c.date_debut_votes IS NOT NULL AND c.date_fin_votes IS NOT NULL THEN
                        :now BETWEEN c.date_debut_votes AND c.date_fin_votes
                    ELSE
                        :now BETWEEN e.date_debut AND e.date_fin
                END as is_active
                FROM categorie c
                JOIN edition e ON c.id_edition = e.id_edition
                WHERE c.id_categorie = :id_categorie
                AND e.est_active = 1
            ';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_categorie' => $categoryId,
                ':now' => date('Y-m-d H:i:s'),
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result && (int) $result['is_active'] === 1;
        } catch (Throwable $exception) {
            error_log('Erreur vérification période de vote public: ' . $exception->getMessage());

            return false;
        }
    }

    private function resolveVoteButtonLink(int $nominationId, int $categoryId, bool $isVotingOpen): string
    {
        if (!$isVotingOpen) {
            return 'javascript:void(0)';
        }

        $params = [];

        if ($nominationId > 0) {
            $params['nomination'] = $nominationId;
        }

        if ($categoryId > 0) {
            $params['category_id'] = $categoryId;
        }

        $votePath = appUrl('vote');
        $voteUrl = $votePath . ($params !== [] ? '?' . http_build_query($params) : '');

        if (isAuthenticated()) {
            return getUserType() === 'voter' ? $voteUrl : 'javascript:void(0)';
        }

        return publicRouteUrl('login', ['redirect' => $voteUrl]);
    }

    private function resolveVoteButtonText(int $categoryId, bool $isVotingOpen): string
    {
        if (!$isVotingOpen || $categoryId <= 0) {
            return 'Votes fermés';
        }

        if (!isAuthenticated()) {
            return 'Connectez-vous pour voter';
        }

        return getUserType() === 'voter' ? 'Voter' : 'Non éligible pour voter';
    }

    private function resolveNomineeVoteButtonText(int $categoryId, bool $isVotingOpen): string
    {
        if (!$isVotingOpen || $categoryId <= 0) {
            return 'Votes fermés';
        }

        if (!isAuthenticated()) {
            return 'Connectez-vous pour voter';
        }

        return getUserType() === 'voter' ? 'Voter pour ce nominé' : 'Non éligible pour voter';
    }

    private function resolveVoteButtonClass(bool $isVotingOpen): string
    {
        if (!$isVotingOpen || !isAuthenticated() || getUserType() !== 'voter') {
            return 'btn-vote btn-disabled';
        }

        return 'btn-vote';
    }

    /**
     * @return array<string, int|float>
     */
    private function emptyResultsStats(): array
    {
        return [
            'total_votes' => 0,
            'total_categories' => 0,
            'total_nominations' => 0,
            'participation_rate' => 0,
            'total_voters' => 0,
        ];
    }

    private function resolveResultsEditionId(ResultsService $resultsService): int
    {
        if (isset($_GET['edition']) && is_numeric($_GET['edition'])) {
            return (int) $_GET['edition'];
        }

        $finishedEditions = $resultsService->getFinishedEditions();

        if ($finishedEditions !== []) {
            return (int) $finishedEditions[0]['id_edition'];
        }

        return 1;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function getEditionById(PDO $pdo, int $editionId): ?array
    {
        $sql = 'SELECT id_edition, annee, nom, date_debut_candidatures, date_fin_candidatures, date_debut, date_fin, est_active, theme, image, description FROM edition WHERE id_edition = :edition_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':edition_id' => $editionId]);
        $edition = $stmt->fetch(PDO::FETCH_ASSOC);

        return $edition ?: null;
    }

    /**
     * @param array<string, mixed> $edition
     */
    private function resolveResultsStatus(array $edition, ?string $dateDebut, ?string $dateFin): string
    {
        $now = date('Y-m-d H:i:s');

        if ($dateFin && $now > $dateFin) {
            return 'voting_finished';
        }

        if ((int) ($edition['est_active'] ?? 0) === 1 && $dateDebut && $now >= $dateDebut && (!$dateFin || $now <= $dateFin)) {
            return 'voting_active';
        }

        if ((int) ($edition['est_active'] ?? 0) === 1 && $dateDebut && $now < $dateDebut) {
            return 'voting_not_started';
        }

        if ((int) ($edition['est_active'] ?? 0) === 0) {
            return 'edition_inactive';
        }

        return 'unknown';
    }

    /**
     * @param array<int, array<string, mixed>> $winners
     * @return array<int, array<string, mixed>>
     */
    private function prepareGrandWinners(array $winners): array
    {
        foreach ($winners as &$winner) {
            $winner['fallback_image'] = $this->getWinnerFallbackImagePath((int) ($winner['rang'] ?? 1));
        }

        unset($winner);

        return $winners;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function prepareAvailableEditions(ResultsService $resultsService): array
    {
        $availableEditions = $resultsService->getAvailableEditions();

        foreach ($availableEditions as &$edition) {
            $editionStatus = $resultsService->getEditionStatus((int) $edition['id_edition']);
            $edition['is_active'] = $editionStatus['active'] ?? false;
            $edition['is_finished'] = $editionStatus['votes_finished'] ?? false;
            $edition['not_started'] = $editionStatus['votes_not_started'] ?? false;
        }

        unset($edition);

        return $availableEditions;
    }

    private function getWinnerFallbackImagePath(int $rank): string
    {
        return match ($rank) {
            2 => 'assets/images/Winners/winner2.jpg',
            3 => 'assets/images/Winners/winner3.jpg',
            default => 'assets/images/Winners/winner1.jpg',
        };
    }

    private function readPositiveInt(mixed $value, int $default): int
    {
        if (!is_numeric((string) $value)) {
            return $default;
        }

        return max(1, (int) $value);
    }

    /**
     * @param array<int, int> $allowedValues
     */
    private function sanitizePerPage(mixed $value, array $allowedValues, int $default): int
    {
        $perPage = is_numeric((string) $value) ? (int) $value : $default;

        if (!in_array($perPage, $allowedValues, true)) {
            return $default;
        }

        return $perPage;
    }

    /**
     * @param array<string, mixed> $baseParams
     * @return array<string, mixed>
     */
    private function buildPaginationData(int $page, int $perPage, int $totalItems, array $baseParams): array
    {
        $totalPages = max(1, (int) ceil($totalItems / max(1, $perPage)));
        $safePage = min($page, $totalPages);
        $offset = ($safePage - 1) * $perPage;

        $startItem = $totalItems > 0 ? ($offset + 1) : 0;
        $endItem = $totalItems > 0 ? min($offset + $perPage, $totalItems) : 0;

        return [
            'page' => $safePage,
            'perPage' => $perPage,
            'perPageOptions' => [6, 12, 24, 48],
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'offset' => $offset,
            'startItem' => $startItem,
            'endItem' => $endItem,
            'queryParams' => array_filter($baseParams, static fn (mixed $param): bool => $param !== null && $param !== ''),
        ];
    }
}