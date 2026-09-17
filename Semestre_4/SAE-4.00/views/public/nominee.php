<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($nominationLabel, ENT_QUOTES, 'UTF-8'); ?> - Social Media Awards</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($headerCssUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($footerCssUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($nomineesCssUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .nominee-profile-page {
            padding: 48px 0 64px;
            background: linear-gradient(180deg, rgba(79, 189, 171, 0.08) 0%, rgba(255, 255, 255, 1) 35%);
        }

        .nominee-profile-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(18, 38, 63, 0.12);
            overflow: hidden;
        }

        .nominee-profile-hero {
            display: grid;
            grid-template-columns: minmax(280px, 360px) minmax(0, 1fr);
            gap: 32px;
            padding: 32px;
        }

        .nominee-profile-image {
            background: #f6f8fb;
            border-radius: 20px;
            overflow: hidden;
            min-height: 320px;
        }

        .nominee-profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .nominee-profile-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .nominee-meta-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(79, 189, 171, 0.12);
            color: #1f4f49;
            font-weight: 600;
        }

        .nominee-profile-title {
            font-size: clamp(2rem, 3vw, 3rem);
            line-height: 1.1;
            margin-bottom: 16px;
            color: #16323b;
        }

        .nominee-profile-summary {
            font-size: 1.05rem;
            line-height: 1.7;
            color: #415361;
            margin-bottom: 24px;
        }

        .nominee-profile-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 28px;
        }

        .nominee-profile-stat {
            min-width: 140px;
            padding: 16px 18px;
            border-radius: 16px;
            background: #f6f8fb;
        }

        .nominee-profile-stat strong {
            display: block;
            font-size: 1.4rem;
            color: #16323b;
        }

        .nominee-profile-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
        }

        .nominee-secondary-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 22px;
            border-radius: 999px;
            background: #16323b;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }

        .nominee-profile-not-found {
            padding: 48px 32px;
            text-align: center;
        }

        .nominee-profile-not-found h1 {
            margin-bottom: 12px;
            color: #16323b;
        }

        .nominee-profile-not-found p {
            max-width: 640px;
            margin: 0 auto 24px;
            color: #52606d;
        }

        @media (max-width: 860px) {
            .nominee-profile-hero {
                grid-template-columns: 1fr;
                padding: 24px;
            }

            .nominee-profile-image {
                min-height: 260px;
            }
        }
    </style>
</head>
<body>
    <?php require appPath('views/partials/header.php'); ?>

    <div class="main-content nominee-profile-page">
        <div class="container">
            <div class="nominee-profile-card">
                <?php if (!$nominationFound): ?>
                    <div class="nominee-profile-not-found">
                        <h1><?php echo htmlspecialchars($emptyStateTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
                        <p><?php echo htmlspecialchars($emptyStateMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                        <a href="<?php echo htmlspecialchars($nomineesUrl, ENT_QUOTES, 'UTF-8'); ?>" class="nominee-secondary-link">
                            <i class="fas fa-arrow-left"></i>
                            Retour aux nominés
                        </a>
                    </div>
                <?php else: ?>
                    <div class="nominee-profile-hero">
                        <div class="nominee-profile-image">
                            <img loading="lazy" src="<?php echo htmlspecialchars($nominationImageUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($nominationLabel, ENT_QUOTES, 'UTF-8'); ?>" onerror="this.src='<?php echo htmlspecialchars($defaultImageUrl, ENT_QUOTES, 'UTF-8'); ?>'">
                        </div>

                        <div>
                            <div class="nominee-profile-meta">
                                <span class="nominee-meta-badge">
                                    <i class="fas fa-tag"></i>
                                    <?php echo htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                                <?php if ($platformName !== ''): ?>
                                    <span class="nominee-meta-badge">
                                        <i class="<?php echo htmlspecialchars($platformIcon, ENT_QUOTES, 'UTF-8'); ?>"></i>
                                        <?php echo htmlspecialchars(ucfirst($platformName), ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <h1 class="nominee-profile-title"><?php echo htmlspecialchars($nominationLabel, ENT_QUOTES, 'UTF-8'); ?></h1>

                            <p class="nominee-profile-summary">
                                <?php echo nl2br(htmlspecialchars($argumentaire, ENT_QUOTES, 'UTF-8')); ?>
                            </p>

                            <div class="nominee-profile-stats">
                                <div class="nominee-profile-stat">
                                    <span>Votes</span>
                                    <strong><?php echo htmlspecialchars($formattedVotes, ENT_QUOTES, 'UTF-8'); ?></strong>
                                </div>
                                <div class="nominee-profile-stat">
                                    <span>Plateforme</span>
                                    <strong><?php echo htmlspecialchars($platformName !== '' ? ucfirst($platformName) : 'Non précisée', ENT_QUOTES, 'UTF-8'); ?></strong>
                                </div>
                            </div>

                            <div class="nominee-profile-actions">
                                <a href="<?php echo htmlspecialchars($voteLink, ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo htmlspecialchars($voteButtonClass, ENT_QUOTES, 'UTF-8'); ?>">
                                    <i class="fas fa-vote-yea"></i>
                                    <?php echo htmlspecialchars($voteButtonText, ENT_QUOTES, 'UTF-8'); ?>
                                </a>

                                <?php if (!empty($contentUrl)): ?>
                                    <a href="<?php echo htmlspecialchars($contentUrl, ENT_QUOTES, 'UTF-8'); ?>" class="nominee-secondary-link" target="_blank" rel="noopener noreferrer">
                                        <i class="fas fa-external-link-alt"></i>
                                        Voir le contenu nominé
                                    </a>
                                <?php endif; ?>

                                <a href="<?php echo htmlspecialchars($nomineesUrl, ENT_QUOTES, 'UTF-8'); ?>" class="nominee-secondary-link">
                                    <i class="fas fa-users"></i>
                                    Voir tous les nominés
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php require appPath('views/partials/footer.php'); ?>
</body>
</html>
