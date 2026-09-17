USE social_media_awards;

DROP PROCEDURE IF EXISTS seed_coherent_data;
DELIMITER $$
CREATE PROCEDURE seed_coherent_data(
    IN p_candidates INT,
    IN p_voters INT,
    IN p_editions INT,
    IN p_categories_per_edition INT,
    IN p_nominations_per_category INT,
    IN p_votes_per_category INT
)
BEGIN
    DECLARE v_run_tag VARCHAR(32);
    DECLARE v_admin_id INT;

    DECLARE i INT DEFAULT 1;
    DECLARE j INT DEFAULT 1;
    DECLARE k INT DEFAULT 1;

    DECLARE v_year INT;
    DECLARE v_edition_id INT;
    DECLARE v_category_id INT;
    DECLARE v_candidate_id INT;
    DECLARE v_voter_id INT;
    DECLARE v_candidature_id INT;
    DECLARE v_nomination_id INT;
    DECLARE v_vote_id INT;
    DECLARE v_token_id INT;

    DECLARE v_cat_count INT;
    DECLARE v_candidate_count INT;
    DECLARE v_voter_count INT;
    DECLARE v_nom_count INT;
    DECLARE v_nom_offset INT;

    DECLARE v_token_value VARCHAR(255);
    DECLARE v_vote_dt DATETIME;
    DECLARE v_base_year INT;

    SET v_run_tag = DATE_FORMAT(NOW(), '%Y%m%d%H%i%s');
    SELECT GREATEST(COALESCE(MAX(annee), 2029), 2029) INTO v_base_year FROM edition;

    SELECT id_compte INTO v_admin_id
    FROM administrateur
    ORDER BY id_compte
    LIMIT 1;

    IF v_admin_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Aucun administrateur trouve dans la table administrateur';
    END IF;

    DROP TEMPORARY TABLE IF EXISTS tmp_seed_editions;
    DROP TEMPORARY TABLE IF EXISTS tmp_seed_categories;
    DROP TEMPORARY TABLE IF EXISTS tmp_seed_candidates;
    DROP TEMPORARY TABLE IF EXISTS tmp_seed_voters;

    CREATE TEMPORARY TABLE tmp_seed_editions (
        id INT PRIMARY KEY AUTO_INCREMENT,
        id_edition INT NOT NULL
    );

    CREATE TEMPORARY TABLE tmp_seed_categories (
        id INT PRIMARY KEY AUTO_INCREMENT,
        id_categorie INT NOT NULL,
        date_debut_votes DATETIME NOT NULL,
        date_fin_votes DATETIME NOT NULL
    );

    CREATE TEMPORARY TABLE tmp_seed_candidates (
        id INT PRIMARY KEY AUTO_INCREMENT,
        id_compte INT NOT NULL
    );

    CREATE TEMPORARY TABLE tmp_seed_voters (
        id INT PRIMARY KEY AUTO_INCREMENT,
        id_compte INT NOT NULL
    );

    SET i = 1;
    WHILE i <= p_editions DO
        SET v_year = v_base_year + i;

        INSERT INTO edition (
            annee,
            nom,
            date_debut_candidatures,
            date_fin_candidatures,
            date_debut,
            date_fin,
            est_active,
            theme,
            image,
            description
        ) VALUES (
            v_year,
            CONCAT('Social Media Awards ', v_year, ' - Seed ', v_run_tag),
            DATE_SUB(NOW(), INTERVAL 45 DAY),
            DATE_SUB(NOW(), INTERVAL 7 DAY),
            DATE_SUB(NOW(), INTERVAL 5 DAY),
            DATE_ADD(NOW(), INTERVAL 30 DAY),
            1,
            CONCAT('Theme Seed ', i),
            NULL,
            CONCAT('Edition de test volumique ', v_run_tag)
        );

        SET v_edition_id = LAST_INSERT_ID();
        INSERT INTO tmp_seed_editions (id_edition) VALUES (v_edition_id);

        SET i = i + 1;
    END WHILE;

    SET i = 1;
    WHILE i <= p_candidates DO
        INSERT INTO compte (
            pseudonyme,
            email,
            mot_de_passe,
            date_naissance,
            pays,
            genre,
            code_verification
        ) VALUES (
            CONCAT('seed_cand_', v_run_tag, '_', i),
            CONCAT('seed_cand_', v_run_tag, '_', i, '@example.com'),
            '$2y$10$TsyMVgTIuHkg1t84T1QdrOQnAbDbLUzmgqqu4WW06WX9s7Va3FMza',
            DATE_SUB('2000-01-01', INTERVAL (i % 3650) DAY),
            'France',
            CASE (i % 3)
                WHEN 0 THEN 'Homme'
                WHEN 1 THEN 'Femme'
                ELSE 'Autre'
            END,
            '000000'
        );

        SET v_candidate_id = LAST_INSERT_ID();

        INSERT INTO candidat (id_compte, nom_legal_ou_societe, type_candidature, est_nomine)
        VALUES (
            v_candidate_id,
            CONCAT('Entite Candidate ', i),
            CASE (i % 3)
                WHEN 0 THEN 'Marque'
                WHEN 1 THEN 'Autre'
                ELSE 'Autre'
            END,
            0
        );

        INSERT INTO tmp_seed_candidates (id_compte) VALUES (v_candidate_id);
        SET i = i + 1;
    END WHILE;

    SET i = 1;
    WHILE i <= p_voters DO
        INSERT INTO compte (
            pseudonyme,
            email,
            mot_de_passe,
            date_naissance,
            pays,
            genre,
            code_verification
        ) VALUES (
            CONCAT('seed_user_', v_run_tag, '_', i),
            CONCAT('seed_user_', v_run_tag, '_', i, '@example.com'),
            '$2y$10$EDny3Yh841/x/9.S2xVUxOGzYjAK0wJnlQvodzMIK1X0AdeaJ94Qy',
            DATE_SUB('1998-01-01', INTERVAL (i % 3650) DAY),
            'France',
            CASE (i % 3)
                WHEN 0 THEN 'Homme'
                WHEN 1 THEN 'Femme'
                ELSE 'Autre'
            END,
            '000000'
        );

        SET v_voter_id = LAST_INSERT_ID();

        INSERT INTO utilisateur (id_compte) VALUES (v_voter_id);
        INSERT INTO tmp_seed_voters (id_compte) VALUES (v_voter_id);

        SET i = i + 1;
    END WHILE;

    SET i = 1;
    SELECT COUNT(*) INTO v_candidate_count FROM tmp_seed_candidates;
    SELECT COUNT(*) INTO v_voter_count FROM tmp_seed_voters;

    WHILE i <= (SELECT COUNT(*) FROM tmp_seed_editions) DO
        SELECT id_edition INTO v_edition_id FROM tmp_seed_editions WHERE id = i;

        SET j = 1;
        WHILE j <= p_categories_per_edition DO
            INSERT INTO categorie (
                nom,
                description,
                image,
                plateforme_cible,
                limite_nomines,
                date_debut_votes,
                date_fin_votes,
                id_edition
            ) VALUES (
                CONCAT('Categorie Seed ', i, '-', j, ' [', v_run_tag, ']'),
                CONCAT('Categorie generee automatiquement - edition ', v_edition_id),
                NULL,
                CASE (j % 5)
                    WHEN 0 THEN 'Instagram'
                    WHEN 1 THEN 'TikTok'
                    WHEN 2 THEN 'YouTube'
                    WHEN 3 THEN 'LinkedIn'
                    ELSE 'X'
                END,
                GREATEST(10, p_nominations_per_category + 2),
                DATE_SUB(NOW(), INTERVAL 3 DAY),
                DATE_ADD(NOW(), INTERVAL 25 DAY),
                v_edition_id
            );

            SET v_category_id = LAST_INSERT_ID();

            INSERT INTO tmp_seed_categories (id_categorie, date_debut_votes, date_fin_votes)
            VALUES (
                v_category_id,
                DATE_SUB(NOW(), INTERVAL 3 DAY),
                DATE_ADD(NOW(), INTERVAL 25 DAY)
            );

            SET k = 1;
            WHILE k <= p_nominations_per_category DO
                SELECT id_compte INTO v_candidate_id
                FROM tmp_seed_candidates
                WHERE id = ((i - 1) * p_categories_per_edition * p_nominations_per_category + (j - 1) * p_nominations_per_category + k - 1) % v_candidate_count + 1;

                INSERT INTO candidature (
                    libelle,
                    plateforme,
                    url_contenu,
                    image,
                    argumentaire,
                    date_soumission,
                    statut,
                    id_compte,
                    id_categorie
                ) VALUES (
                    CONCAT('Candidature ', v_run_tag, ' ', i, '-', j, '-', k),
                    CASE (k % 5)
                        WHEN 0 THEN 'Instagram'
                        WHEN 1 THEN 'TikTok'
                        WHEN 2 THEN 'YouTube'
                        WHEN 3 THEN 'LinkedIn'
                        ELSE 'X'
                    END,
                    CONCAT('https://example.com/content/', v_run_tag, '/', i, '/', j, '/', k),
                    CONCAT('uploads/candidatures/seed_', v_run_tag, '_', i, '_', j, '_', k, '.jpg'),
                    CONCAT('Argumentaire coherent pour candidature ', i, '-', j, '-', k),
                    DATE_SUB(NOW(), INTERVAL (10 + (k % 15)) DAY),
                    'En attente',
                    v_candidate_id,
                    v_category_id
                );

                SET v_candidature_id = LAST_INSERT_ID();

                INSERT INTO nomination (
                    libelle,
                    plateforme,
                    url_content,
                    url_image,
                    argumentaire,
                    date_approbation,
                    id_candidature,
                    id_categorie,
                    id_compte,
                    id_admin
                ) VALUES (
                    CONCAT('Nomination ', v_run_tag, ' ', i, '-', j, '-', k),
                    CASE (k % 5)
                        WHEN 0 THEN 'Instagram'
                        WHEN 1 THEN 'TikTok'
                        WHEN 2 THEN 'YouTube'
                        WHEN 3 THEN 'LinkedIn'
                        ELSE 'X'
                    END,
                    CONCAT('https://example.com/content/', v_run_tag, '/', i, '/', j, '/', k),
                    CONCAT('uploads/candidatures/seed_', v_run_tag, '_', i, '_', j, '_', k, '.jpg'),
                    CONCAT('Nomination validee automatiquement ', v_run_tag),
                    DATE_SUB(NOW(), INTERVAL 2 DAY),
                    v_candidature_id,
                    v_category_id,
                    v_candidate_id,
                    v_admin_id
                );

                SET k = k + 1;
            END WHILE;

            SET j = j + 1;
        END WHILE;

        SET i = i + 1;
    END WHILE;

    SELECT COUNT(*) INTO v_cat_count FROM tmp_seed_categories;

    SET i = 1;
    WHILE i <= v_cat_count DO
        SELECT id_categorie, date_debut_votes, date_fin_votes
        INTO v_category_id, v_vote_dt, @v_date_fin_votes
        FROM tmp_seed_categories
        WHERE id = i;

        SET j = 1;
        WHILE j <= p_votes_per_category DO
            SELECT id_compte INTO v_voter_id
            FROM tmp_seed_voters
            WHERE id = ((i - 1) * p_votes_per_category + j - 1) % v_voter_count + 1;

            SET v_nom_offset = ((j - 1) % p_nominations_per_category);

            SELECT n.id_nomination INTO v_nomination_id
            FROM nomination n
            WHERE n.id_categorie = v_category_id
            ORDER BY n.id_nomination
            LIMIT v_nom_offset, 1;

            SET v_token_value = SHA2(CONCAT('seed_token_', v_run_tag, '_', i, '_', j, '_', RAND()), 256);

            INSERT IGNORE INTO controle_presence (id_compte, id_categorie, statut_a_vote, date_controle)
            VALUES (v_voter_id, v_category_id, 0, DATE_SUB(NOW(), INTERVAL 1 HOUR));

            INSERT INTO token_anonyme (
                token_value,
                est_utilise,
                date_creation,
                date_expiration,
                id_compte,
                id_categorie
            ) VALUES (
                v_token_value,
                0,
                DATE_SUB(NOW(), INTERVAL 30 MINUTE),
                DATE_ADD(NOW(), INTERVAL 1 HOUR),
                v_voter_id,
                v_category_id
            );

            SET v_token_id = LAST_INSERT_ID();

            SET v_vote_dt = DATE_SUB(NOW(), INTERVAL (j % 48) HOUR);

            INSERT INTO vote (
                date_heure_vote,
                vote_chiffre,
                horodatage_certifie,
                id_nomination,
                id_token
            ) VALUES (
                v_vote_dt,
                TO_BASE64(CONCAT('{"seed":true,"tag":"', v_run_tag, '","cat":', v_category_id, ',"nom":', v_nomination_id, ',"voter":', v_voter_id, '}')),
                v_vote_dt,
                v_nomination_id,
                v_token_id
            );

            SET v_vote_id = LAST_INSERT_ID();

            INSERT INTO preuve_depot (hash_cryptographique, horodatage_certifie, id_vote)
            VALUES (SHA2(CONCAT('proof_', v_run_tag, '_', v_vote_id), 512), v_vote_dt, v_vote_id);

            INSERT INTO confirmation_vote (horodatage_confirmation, type_annonce, id_vote)
            VALUES (v_vote_dt, 'systeme', v_vote_id);

            INSERT IGNORE INTO signature_electorale (hash_confirmation, date_signature, id_compte, id_categorie)
            VALUES (SHA2(CONCAT('sign_', v_run_tag, '_', v_voter_id, '_', v_category_id), 512), v_vote_dt, v_voter_id, v_category_id);

            INSERT IGNORE INTO certificat_participation (hash_certificat, date_emission, id_compte, id_categorie)
            VALUES (SHA2(CONCAT('cert_', v_run_tag, '_', v_voter_id, '_', v_category_id), 512), v_vote_dt, v_voter_id, v_category_id);

            SET j = j + 1;
        END WHILE;

        SET i = i + 1;
    END WHILE;

    SELECT
        v_run_tag AS run_tag,
        (SELECT COUNT(*) FROM tmp_seed_editions) AS editions_creees,
        (SELECT COUNT(*) FROM tmp_seed_categories) AS categories_creees,
        p_candidates AS candidats_crees,
        p_voters AS votants_crees,
        (SELECT COUNT(*)
         FROM nomination
         WHERE libelle LIKE CONCAT('Nomination ', v_run_tag, ' %')) AS nominations_creees,
        (SELECT COUNT(*)
         FROM vote
         WHERE vote_chiffre LIKE CONCAT('%', v_run_tag, '%')) AS votes_crees;
END$$
DELIMITER ;

CALL seed_coherent_data(90, 260, 3, 6, 8, 35);
