-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 19 jan. 2026 à 11:44
-- Version du serveur : 8.0.43
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `social_media_awards`
--

DELIMITER $$
--
-- Procédures
--
DROP PROCEDURE IF EXISTS `generate_anonymous_token`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generate_anonymous_token` (IN `p_id_compte` INT, IN `p_id_categorie` INT, OUT `p_token_value` VARCHAR(255))   BEGIN
    DECLARE v_token VARCHAR(255);
    DECLARE v_expiration DATETIME;
    
    -- Gerar token único
    SET v_token = SHA2(CONCAT(p_id_compte, p_id_categorie, NOW(), RAND()), 256);
    SET v_expiration = DATE_ADD(NOW(), INTERVAL 1 HOUR); -- Token válido por 1 hora
    
    -- Inserir token
    INSERT INTO token_anonyme (token_value, date_expiration, id_compte, id_categorie)
    VALUES (v_token, v_expiration, p_id_compte, p_id_categorie);
    
    SET p_token_value = v_token;
    
    -- Criar registro de controle de presença se não existir
    INSERT IGNORE INTO controle_presence (id_compte, id_categorie)
    VALUES (p_id_compte, p_id_categorie);
END$$

  DROP PROCEDURE IF EXISTS `process_vote`$$
  CREATE DEFINER=`root`@`localhost` PROCEDURE `process_vote` (IN `p_token_value` VARCHAR(255), IN `p_vote_chiffre` TEXT, IN `p_id_nomination` INT, OUT `p_id_vote` INT)   BEGIN
    DECLARE v_token_id INT;
    DECLARE v_compte_id INT;
    DECLARE v_categorie_id INT;
    DECLARE v_token_valido BOOLEAN;
    
    -- Verificar token
    SELECT id_token, id_compte, id_categorie, 
           (est_utilise = FALSE AND date_expiration > NOW()) 
    INTO v_token_id, v_compte_id, v_categorie_id, v_token_valido
    FROM token_anonyme
    WHERE token_value = p_token_value;
    
    IF v_token_valido THEN
        -- Inserir voto
        INSERT INTO vote (vote_chiffre, id_nomination, id_token)
        VALUES (p_vote_chiffre, p_id_nomination, v_token_id);
        
        SET p_id_vote = LAST_INSERT_ID();
        
        -- Gerar prova de depósito
        INSERT INTO preuve_depot (hash_cryptographique, id_vote)
        VALUES (SHA2(CONCAT(p_id_vote, p_vote_chiffre, NOW()), 512), p_id_vote);
        
        -- Gerar assinatura eleitoral
        INSERT INTO signature_electorale (hash_confirmation, id_compte, id_categorie)
        VALUES (SHA2(CONCAT(v_compte_id, v_categorie_id, NOW()), 512), v_compte_id, v_categorie_id);
        
        -- Gerar confirmação
        INSERT INTO confirmation_vote (id_vote)
        VALUES (p_id_vote);
        
        -- Gerar certificado de participação
        INSERT INTO certificat_participation (hash_certificat, id_compte, id_categorie)
        VALUES (SHA2(CONCAT(v_compte_id, v_categorie_id, p_id_vote, NOW()), 512), v_compte_id, v_categorie_id);
        
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Token inválido, expirado ou já utilizado';
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `compte`
--

DROP TABLE IF EXISTS `compte`;
CREATE TABLE IF NOT EXISTS `compte` (
  `id_compte` int NOT NULL AUTO_INCREMENT,
  `pseudonyme` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `date_naissance` date NOT NULL,
  `pays` varchar(100) NOT NULL,
  `genre` enum('Homme','Femme','Autre') DEFAULT NULL,
  `photo_profil` varchar(255) DEFAULT NULL,
  `code_verification` varchar(10) DEFAULT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modification` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_compte`),
  UNIQUE KEY `pseudonyme` (`pseudonyme`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_compte_email` (`email`),
  KEY `idx_compte_pseudonyme` (`pseudonyme`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `compte`
--

INSERT INTO `compte` (`id_compte`, `pseudonyme`, `email`, `mot_de_passe`, `date_naissance`, `pays`, `genre`, `photo_profil`, `code_verification`, `date_creation`, `date_modification`) VALUES
(15, 'admin', 'admin@gmail.com', '$2y$10$egYNXhUT.wMwMo1DH3Vwou3KtQDUk2qO4GjQwEVYEhXIyozhaRUaO', '2013-01-09', 'Liban', 'Homme', NULL, '000000', '2026-01-18 16:14:26', '2026-01-18 16:14:26'),
(16, 'candidat', 'candidat@gmail.com', '$2y$10$TsyMVgTIuHkg1t84T1QdrOQnAbDbLUzmgqqu4WW06WX9s7Va3FMza', '2013-01-09', 'Autriche', 'Femme', NULL, '000000', '2026-01-18 16:56:55', '2026-01-18 16:56:55'),
(17, 'elector', 'elector@gmail.com', '$2y$10$EDny3Yh841/x/9.S2xVUxOGzYjAK0wJnlQvodzMIK1X0AdeaJ94Qy', '2013-01-18', 'Barbade', 'Femme', NULL, '000000', '2026-01-18 17:11:05', '2026-01-18 17:11:05'),
(18, 'candidat1', 'candidat1@gmail.com', '$2y$10$/IHW7yfvw5QkzmU8HBWPkO4lZF.SAmnhJhjdr1iUM4YFrdC4Civwa', '2013-01-09', 'Belize', 'Homme', NULL, '000000', '2026-01-18 17:20:25', '2026-01-18 17:20:25'),
(19, 'candidat2', 'candidat2@gmail.com', '$2y$10$RCIHYF.jkolPbVBYF8WHXOBwOGna9b/cdTIcQByO1krKwfejvTwFG', '2013-01-09', 'Afrique du Sud', 'Femme', NULL, '000000', '2026-01-18 17:21:21', '2026-01-18 17:21:21'),
(20, 'candidat3>', 'candidat3@gmail.com', '$2y$10$sSAdIbeo.ZKd5iekZb9AT.bBg2iYfiVrPnUdguc7C53Q16.KQDXpu', '2013-01-01', 'Bangladesh', 'Homme', NULL, '000000', '2026-01-18 17:22:20', '2026-01-18 17:22:20'),
(21, 'wendy', 'Wendymechisso@gmail.com', '$2y$10$TAZQ7TnExEqQgQ58RALcVuoIgWu1brdpDNrdZ6iAfYQX1BWFnmrge', '2005-03-07', 'Belgique', 'Femme', NULL, '000000', '2026-01-19 07:39:25', '2026-01-19 11:42:03'),
(24, 'Eunice', 'EuniceL@gmail.com', '$2y$10$OvjMf5x2H.AsxNlYqzuy2OKl2Gaolix0bNLBb7A2vf8CFrRd4JN5i', '2004-03-29', 'Belgique', 'Femme', 'profile_24_1768805776.jpg', '000000', '2026-01-19 07:52:24', '2026-01-19 11:41:31');

-- --------------------------------------------------------

--
-- Structure de la table `administrateur`
--

DROP TABLE IF EXISTS `administrateur`;
CREATE TABLE IF NOT EXISTS `administrateur` (
  `id_compte` int NOT NULL,
  `niveau_privileges` int DEFAULT '1',
  PRIMARY KEY (`id_compte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `administrateur`
--

INSERT INTO `administrateur` (`id_compte`, `niveau_privileges`) VALUES
(15, 1);

-- --------------------------------------------------------

--
-- Structure de la table `candidat`
--

DROP TABLE IF EXISTS `candidat`;
CREATE TABLE IF NOT EXISTS `candidat` (
  `id_compte` int NOT NULL,
  `nom_legal_ou_societe` varchar(255) DEFAULT NULL,
  `type_candidature` enum('Créateur','Marque','Autre') NOT NULL,
  `est_nomine` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_compte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `candidat`
--

INSERT INTO `candidat` (`id_compte`, `nom_legal_ou_societe`, `type_candidature`, `est_nomine`) VALUES
(16, NULL, 'Autre', 1),
(18, NULL, 'Autre', 0),
(20, NULL, 'Autre', 1),
(21, '', 'Marque', 1);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_compte` int NOT NULL,
  PRIMARY KEY (`id_compte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_compte`) VALUES
(15),
(17),
(19),
(24);

-- --------------------------------------------------------

--
-- Structure de la table `edition`
--

DROP TABLE IF EXISTS `edition`;
CREATE TABLE IF NOT EXISTS `edition` (
  `id_edition` int NOT NULL AUTO_INCREMENT,
  `annee` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `date_debut_candidatures` datetime NOT NULL,
  `date_fin_candidatures` datetime NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `est_active` tinyint(1) DEFAULT '0',
  `theme` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id_edition`),
  UNIQUE KEY `annee` (`annee`),
  KEY `idx_edition_annee` (`annee`),
  KEY `idx_edition_active` (`est_active`),
  KEY `idx_edition_dates` (`date_debut`,`date_fin`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `edition`
--

INSERT INTO `edition` (`id_edition`, `annee`, `nom`, `date_debut_candidatures`, `date_fin_candidatures`, `date_debut`, `date_fin`, `est_active`, `theme`, `image`, `description`) VALUES
(12, 2025, 'Social Media Awards 2025', '2026-01-03 16:17:00', '2026-01-10 16:17:00', '2026-01-16 16:17:00', '2026-01-17 16:17:00', 0, 'l\'influence d\'IA', 'uploads/editions/edi_696cf9a449fd7.jpg', NULL),
(14, 2026, 'Social Media Awards 2026', '2026-01-05 16:52:00', '2026-01-16 22:52:00', '2026-01-17 16:52:00', '2026-01-17 17:57:00', 0, 'Inovation digitale', NULL, NULL),
(20, 2027, 'Social Media Awards 2027', '2026-01-05 12:32:00', '2026-01-10 12:32:00', '2026-01-15 12:32:00', '2026-01-17 12:32:00', 0, 'XCFSEDRFGHJUKLNB?', NULL, NULL);

--
-- Déclencheurs `edition`
--
DROP TRIGGER IF EXISTS `edition_after_update_sync_vote_dates`;
DELIMITER $$
CREATE TRIGGER `edition_after_update_sync_vote_dates` AFTER UPDATE ON `edition` FOR EACH ROW BEGIN
    IF OLD.date_debut <> NEW.date_debut 
       OR OLD.date_fin   <> NEW.date_fin THEN
        UPDATE categorie
        SET 
            date_debut_votes = NEW.date_debut,
            date_fin_votes   = NEW.date_fin
        WHERE id_edition = NEW.id_edition;
    END IF;
END
$$
DELIMITER ;

DROP TRIGGER IF EXISTS `edition_before_insert`;
DELIMITER $$
CREATE TRIGGER `edition_before_insert` BEFORE INSERT ON `edition` FOR EACH ROW BEGIN
    IF NOW() >= NEW.date_debut_candidatures 
       AND NOW() <= NEW.date_fin THEN
        SET NEW.est_active = 1;
    ELSE
        SET NEW.est_active = 0;
    END IF;
END
$$
DELIMITER ;

DROP TRIGGER IF EXISTS `edition_before_update`;
DELIMITER $$
CREATE TRIGGER `edition_before_update` BEFORE UPDATE ON `edition` FOR EACH ROW BEGIN
    IF NOW() >= NEW.date_debut_candidatures 
       AND NOW() <= NEW.date_fin THEN
        SET NEW.est_active = 1;
    ELSE
        SET NEW.est_active = 0;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id_categorie` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `plateforme_cible` varchar(50) DEFAULT NULL,
  `limite_nomines` int NOT NULL DEFAULT '10',
  `date_debut_votes` datetime DEFAULT NULL,
  `date_fin_votes` datetime DEFAULT NULL,
  `id_edition` int NOT NULL,
  PRIMARY KEY (`id_categorie`),
  KEY `idx_categorie_edition` (`id_edition`),
  KEY `idx_categorie_dates` (`date_debut_votes`,`date_fin_votes`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id_categorie`, `nom`, `description`, `image`, `plateforme_cible`, `limite_nomines`, `date_debut_votes`, `date_fin_votes`, `id_edition`) VALUES
(28, 'EUNICE', 'EVHFZGU', NULL, 'TikTok', 10, '2026-01-15 12:32:00', '2026-01-17 12:32:00', 20);

--
-- Déclencheurs `categorie`
--
DROP TRIGGER IF EXISTS `categorie_before_insert_sync_dates`;
DELIMITER $$
CREATE TRIGGER `categorie_before_insert_sync_dates` BEFORE INSERT ON `categorie` FOR EACH ROW BEGIN
    DECLARE edition_debut DATETIME;
    DECLARE edition_fin   DATETIME;

    SELECT 
        date_debut,
        date_fin
    INTO 
        edition_debut,
        edition_fin
    FROM edition
    WHERE id_edition = NEW.id_edition
    LIMIT 1;

    IF edition_debut IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Edição não encontrada para esta categoria';
    END IF;

    IF NEW.date_debut_votes IS NULL THEN
        SET NEW.date_debut_votes = edition_debut;
    END IF;

    IF NEW.date_fin_votes IS NULL THEN
        SET NEW.date_fin_votes = edition_fin;
    END IF;
END
$$
DELIMITER ;

DROP TRIGGER IF EXISTS `categorie_before_update_sync_dates`;
DELIMITER $$
CREATE TRIGGER `categorie_before_update_sync_dates` BEFORE UPDATE ON `categorie` FOR EACH ROW BEGIN
    DECLARE edition_debut DATETIME;
    DECLARE edition_fin   DATETIME;

    IF NEW.date_debut_votes IS NULL OR NEW.date_fin_votes IS NULL THEN
        SELECT 
            date_debut,
            date_fin
        INTO 
            edition_debut,
            edition_fin
        FROM edition
        WHERE id_edition = NEW.id_edition
        LIMIT 1;

        IF edition_debut IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Edição não encontrada para esta categoria';
        END IF;

        IF NEW.date_debut_votes IS NULL THEN
            SET NEW.date_debut_votes = edition_debut;
        END IF;

        IF NEW.date_fin_votes IS NULL THEN
            SET NEW.date_fin_votes = edition_fin;
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `candidature`
--

DROP TABLE IF EXISTS `candidature`;
CREATE TABLE IF NOT EXISTS `candidature` (
  `id_candidature` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `plateforme` varchar(50) NOT NULL,
  `url_contenu` varchar(500) NOT NULL,
  `image` varchar(255) NOT NULL,
  `argumentaire` text NOT NULL,
  `date_soumission` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('En attente','Approuvée','Rejetée') DEFAULT 'En attente',
  `supprime_le` timestamp NULL DEFAULT NULL,
  `id_compte` int NOT NULL,
  `id_categorie` int NOT NULL,
  PRIMARY KEY (`id_candidature`),
  KEY `idx_candidature_statut` (`statut`),
  KEY `idx_candidature_categorie` (`id_categorie`),
  KEY `idx_candidature_candidat` (`id_compte`),
  KEY `idx_candidature_date` (`date_soumission`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `candidature`
--

INSERT INTO `candidature` (`id_candidature`, `libelle`, `plateforme`, `url_contenu`, `image`, `argumentaire`, `date_soumission`, `statut`, `id_compte`, `id_categorie`) VALUES
(27, 'LINDA', 'Instagram', 'https://github.com/', 'uploads/candidatures/cand_696e178ee27886.66129452.jpg', 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllll', '2026-01-19 12:37:50', 'Approuvée', 21, 28);

-- --------------------------------------------------------

--
-- Structure de la table `nomination`
--

DROP TABLE IF EXISTS `nomination`;
CREATE TABLE IF NOT EXISTS `nomination` (
  `id_nomination` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `plateforme` varchar(50) NOT NULL,
  `url_content` varchar(500) NOT NULL,
  `url_image` varchar(255) NOT NULL,
  `argumentaire` text NOT NULL,
  `date_approbation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_candidature` int NOT NULL,
  `id_categorie` int NOT NULL,
  `id_compte` int NOT NULL,
  `id_admin` int DEFAULT NULL,
  `supprime_le` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_nomination`),
  UNIQUE KEY `id_candidature` (`id_candidature`),
  KEY `id_compte` (`id_compte`),
  KEY `id_admin` (`id_admin`),
  KEY `idx_nomination_categorie` (`id_categorie`),
  KEY `idx_nomination_candidature` (`id_candidature`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `nomination`
--

INSERT INTO `nomination` (`id_nomination`, `libelle`, `plateforme`, `url_content`, `url_image`, `argumentaire`, `date_approbation`, `id_candidature`, `id_categorie`, `id_compte`, `id_admin`) VALUES
(19, 'LINDA', 'Instagram', 'https://github.com/', 'uploads/candidatures/cand_696e178ee27886.66129452.jpg', 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllll', '2026-01-19 12:38:11', 27, 28, 21, 15);

--
-- Déclencheurs `nomination`
--
DROP TRIGGER IF EXISTS `after_nomination_approval`;
DELIMITER $$
CREATE TRIGGER `after_nomination_approval` AFTER INSERT ON `nomination` FOR EACH ROW BEGIN
  UPDATE candidat 
    SET est_nomine = TRUE 
    WHERE id_compte = NEW.id_compte;
    
  UPDATE candidature 
    SET statut = 'Approuvée' 
    WHERE id_candidature = NEW.id_candidature;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `token_anonyme`
--

DROP TABLE IF EXISTS `token_anonyme`;
CREATE TABLE IF NOT EXISTS `token_anonyme` (
  `id_token` int NOT NULL AUTO_INCREMENT,
  `token_value` varchar(255) NOT NULL,
  `est_utilise` tinyint(1) DEFAULT '0',
  `date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_expiration` datetime NOT NULL,
  `id_compte` int NOT NULL,
  `id_categorie` int NOT NULL,
  PRIMARY KEY (`id_token`),
  UNIQUE KEY `token_value` (`token_value`),
  KEY `id_compte` (`id_compte`),
  KEY `id_categorie` (`id_categorie`),
  KEY `idx_token_value` (`token_value`),
  KEY `idx_token_expiration` (`date_expiration`),
  KEY `idx_token_utilise` (`est_utilise`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `token_anonyme`
--

INSERT INTO `token_anonyme` (`id_token`, `token_value`, `est_utilise`, `date_creation`, `date_expiration`, `id_compte`, `id_categorie`) VALUES
(10, 'f2564945291fe82f22b890212cfc523ec2c318e1fad437301c9cba5c9714026a', 1, '2026-01-19 12:38:56', '2026-01-19 13:38:56', 24, 28);

-- --------------------------------------------------------

--
-- Structure de la table `vote`
--

DROP TABLE IF EXISTS `vote`;
CREATE TABLE IF NOT EXISTS `vote` (
  `id_vote` int NOT NULL AUTO_INCREMENT,
  `date_heure_vote` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vote_chiffre` text NOT NULL,
  `horodatage_certifie` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_nomination` int NOT NULL,
  `id_token` int NOT NULL,
  PRIMARY KEY (`id_vote`),
  UNIQUE KEY `id_token` (`id_token`),
  KEY `idx_vote_nomination` (`id_nomination`),
  KEY `idx_vote_date` (`date_heure_vote`),
  KEY `idx_vote_token` (`id_token`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `vote`
--

INSERT INTO `vote` (`id_vote`, `date_heure_vote`, `vote_chiffre`, `horodatage_certifie`, `id_nomination`, `id_token`) VALUES
(9, '2026-01-19 12:39:09', 'eyJub21pbmF0aW9uX2lkIjoxOSwidGltZXN0YW1wIjoxNzY4ODIyNzQ5LCJ1c2VyX2hhc2giOiI2MTEyYTI4MTM5YThkZjhlMjRjMTQ0YjhmZDBlZjY5N2Y0ZWZjNWFkNzc5MDFhZDNhNTJhNWNlZmFlZmYyMWQ5In0=', '2026-01-19 12:39:09', 19, 10);

--
-- Déclencheurs `vote`
--
DROP TRIGGER IF EXISTS `after_vote_insert`;
DELIMITER $$
CREATE TRIGGER `after_vote_insert` AFTER INSERT ON `vote` FOR EACH ROW BEGIN
  UPDATE token_anonyme 
    SET est_utilise = TRUE 
    WHERE id_token = NEW.id_token;
    
  UPDATE controle_presence cp
  JOIN token_anonyme ta ON cp.id_compte = ta.id_compte AND cp.id_categorie = ta.id_categorie
    SET cp.statut_a_vote = TRUE, cp.date_controle = NEW.date_heure_vote
    WHERE ta.id_token = NEW.id_token;
END
$$
DELIMITER ;

DROP TRIGGER IF EXISTS `before_vote_insert`;
DELIMITER $$
CREATE TRIGGER `before_vote_insert` BEFORE INSERT ON `vote` FOR EACH ROW BEGIN
    DECLARE vote_period_start DATETIME;
    DECLARE vote_period_end DATETIME;
    DECLARE category_id INT;
    
    SELECT n.id_categorie INTO category_id
    FROM nomination n
    WHERE n.id_nomination = NEW.id_nomination;
    
    SELECT 
        COALESCE(c.date_debut_votes, e.date_debut),
        COALESCE(c.date_fin_votes, e.date_fin)
    INTO vote_period_start, vote_period_end
    FROM categorie c
    JOIN edition e ON c.id_edition = e.id_edition
    WHERE c.id_categorie = category_id;
    
    IF NEW.date_heure_vote < vote_period_start OR NEW.date_heure_vote > vote_period_end THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Voto fora do período permitido';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `certificat_participation`
--

DROP TABLE IF EXISTS `certificat_participation`;
CREATE TABLE IF NOT EXISTS `certificat_participation` (
  `id_certificat` int NOT NULL AUTO_INCREMENT,
  `hash_certificat` varchar(512) NOT NULL,
  `date_emission` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_compte` int NOT NULL,
  `id_categorie` int NOT NULL,
  PRIMARY KEY (`id_certificat`),
  UNIQUE KEY `hash_certificat` (`hash_certificat`),
  KEY `id_compte` (`id_compte`),
  KEY `id_categorie` (`id_categorie`),
  KEY `idx_certificat_hash` (`hash_certificat`(255))
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `certificat_participation`
--

INSERT INTO `certificat_participation` (`id_certificat`, `hash_certificat`, `date_emission`, `id_compte`, `id_categorie`) VALUES
(9, '57a9860d6e4f52a4994d2f662891428f0810663a3816a4d936e5482c6bf4e760b089cc13f7a446db692d29f0712fb4f9a77232cd5cd91dfe6c58c7f080673fea', '2026-01-19 12:39:09', 24, 28);

-- --------------------------------------------------------

--
-- Structure de la table `confirmation_vote`
--

DROP TABLE IF EXISTS `confirmation_vote`;
CREATE TABLE IF NOT EXISTS `confirmation_vote` (
  `id_confirmation` int NOT NULL AUTO_INCREMENT,
  `horodatage_confirmation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type_annonce` enum('systeme','email','both') DEFAULT 'systeme',
  `id_vote` int NOT NULL,
  PRIMARY KEY (`id_confirmation`),
  UNIQUE KEY `id_vote` (`id_vote`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `confirmation_vote`
--

INSERT INTO `confirmation_vote` (`id_confirmation`, `horodatage_confirmation`, `type_annonce`, `id_vote`) VALUES
(9, '2026-01-19 12:39:09', 'systeme', 9);

-- --------------------------------------------------------

--
-- Structure de la table `controle_presence`
--

DROP TABLE IF EXISTS `controle_presence`;
CREATE TABLE IF NOT EXISTS `controle_presence` (
  `id_controle` int NOT NULL AUTO_INCREMENT,
  `statut_a_vote` tinyint(1) DEFAULT '0',
  `date_controle` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_compte` int NOT NULL,
  `id_categorie` int NOT NULL,
  PRIMARY KEY (`id_controle`),
  UNIQUE KEY `unique_controle` (`id_compte`,`id_categorie`),
  KEY `id_categorie` (`id_categorie`),
  KEY `idx_controle_vote` (`statut_a_vote`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `controle_presence`
--

INSERT INTO `controle_presence` (`id_controle`, `statut_a_vote`, `date_controle`, `id_compte`, `id_categorie`) VALUES
(10, 1, '2026-01-19 12:39:09', 24, 28);

-- --------------------------------------------------------

--
-- Structure de la table `preuve_depot`
--

DROP TABLE IF EXISTS `preuve_depot`;
CREATE TABLE IF NOT EXISTS `preuve_depot` (
  `id_preuve` int NOT NULL AUTO_INCREMENT,
  `hash_cryptographique` varchar(512) NOT NULL,
  `horodatage_certifie` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_vote` int NOT NULL,
  PRIMARY KEY (`id_preuve`),
  UNIQUE KEY `id_vote` (`id_vote`),
  KEY `idx_preuve_hash` (`hash_cryptographique`(255))
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `preuve_depot`
--

INSERT INTO `preuve_depot` (`id_preuve`, `hash_cryptographique`, `horodatage_certifie`, `id_vote`) VALUES
(9, 'a3df10d938231fcf0d49a0199f70ca566c9962b154f0786239fcd4a41896d81f326e5e81136127fc69d705e0b2a1fff130fd768947a9d176862a4dca16f1a8aa', '2026-01-19 12:39:09', 9);

-- --------------------------------------------------------

--
-- Structure de la table `signature_electorale`
--

DROP TABLE IF EXISTS `signature_electorale`;
CREATE TABLE IF NOT EXISTS `signature_electorale` (
  `id_signature` int NOT NULL AUTO_INCREMENT,
  `hash_confirmation` varchar(512) NOT NULL,
  `date_signature` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_compte` int NOT NULL,
  `id_categorie` int NOT NULL,
  PRIMARY KEY (`id_signature`),
  UNIQUE KEY `unique_signature` (`id_compte`,`id_categorie`),
  KEY `id_categorie` (`id_categorie`),
  KEY `idx_signature_hash` (`hash_confirmation`(255))
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `signature_electorale`
--

INSERT INTO `signature_electorale` (`id_signature`, `hash_confirmation`, `date_signature`, `id_compte`, `id_categorie`) VALUES
(9, '07b8baadc13b1715560d9f6d99cf78767c465553990b6ddcb8f7fbaad4b5da06203a36b6f3bf6d4076614bc329e06572ef574e8d954525ecb9e080570bea1123', '2026-01-19 12:39:09', 24, 28);

-- --------------------------------------------------------

--
-- Structure de la vue `vue_candidatures_par_candidat`
--

CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vue_candidatures_par_candidat` AS 
SELECT 
  `co`.`pseudonyme` AS `candidat`, 
  `ca`.`type_candidature` AS `type_candidature`, 
  `ca`.`est_nomine` AS `est_nomine`, 
  COUNT(DISTINCT `cand`.`id_candidature`) AS `total_candidaturas`, 
  COUNT(DISTINCT `n`.`id_nomination`) AS `candidaturas_aprovadas`, 
  GROUP_CONCAT(DISTINCT `cat`.`nom` SEPARATOR ',') AS `categorias_participadas` 
FROM 
  ((((`candidat` `ca` JOIN `compte` `co` ON((`ca`.`id_compte` = `co`.`id_compte`))) 
  LEFT JOIN `candidature` `cand` ON((`ca`.`id_compte` = `cand`.`id_compte`))) 
  LEFT JOIN `nomination` `n` ON((`cand`.`id_candidature` = `n`.`id_candidature`))) 
  LEFT JOIN `categorie` `cat` ON((`cand`.`id_categorie` = `cat`.`id_categorie`))) 
GROUP BY `ca`.`id_compte`;

-- --------------------------------------------------------

--
-- Structure de la vue `vue_resultats_categorie`
--

CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vue_resultats_categorie` AS 
SELECT 
  `c`.`nom` AS `categorie`, 
  `n`.`libelle` AS `nomination`, 
  COUNT(`v`.`id_vote`) AS `total_votes`, 
  `e`.`annee` AS `edition` 
FROM 
  (((`categorie` `c` JOIN `nomination` `n` ON((`c`.`id_categorie` = `n`.`id_categorie`))) 
  LEFT JOIN `vote` `v` ON((`n`.`id_nomination` = `v`.`id_nomination`))) 
  JOIN `edition` `e` ON((`c`.`id_edition` = `e`.`id_edition`))) 
GROUP BY `c`.`id_categorie`, `n`.`id_nomination`, `e`.`id_edition` 
ORDER BY `c`.`nom` ASC, `total_votes` DESC;

-- --------------------------------------------------------

--
-- Structure de la vue `vue_statistiques_participation`
--

CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vue_statistiques_participation` AS 
SELECT 
  `e`.`annee` AS `edition`, 
  COUNT(DISTINCT `cp`.`id_compte`) AS `votantes`, 
  COUNT(DISTINCT `v`.`id_vote`) AS `total_votos`, 
  COUNT(DISTINCT `c`.`id_categorie`) AS `categorias_ativas` 
FROM 
  (((`edition` `e` 
  LEFT JOIN `categorie` `c` ON((`e`.`id_edition` = `c`.`id_edition`))) 
  LEFT JOIN `controle_presence` `cp` ON(((`c`.`id_categorie` = `cp`.`id_categorie`) AND (`cp`.`statut_a_vote` = TRUE)))) 
  LEFT JOIN `nomination` `n` ON((`c`.`id_categorie` = `n`.`id_categorie`))) 
  LEFT JOIN `vote` `v` ON((`n`.`id_nomination` = `v`.`id_nomination`)) 
GROUP BY `e`.`id_edition`;

-- --------------------------------------------------------

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `administrateur`
--
ALTER TABLE `administrateur`
  ADD CONSTRAINT `administrateur_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `compte` (`id_compte`) ON DELETE CASCADE;

--
-- Contraintes pour la table `candidat`
--
ALTER TABLE `candidat`
  ADD CONSTRAINT `candidat_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `compte` (`id_compte`) ON DELETE CASCADE;

--
-- Contraintes pour la table `candidature`
--
ALTER TABLE `candidature`
  ADD CONSTRAINT `candidature_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `candidat` (`id_compte`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidature_ibfk_2` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE CASCADE;

--
-- Contraintes pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD CONSTRAINT `categorie_ibfk_1` FOREIGN KEY (`id_edition`) REFERENCES `edition` (`id_edition`) ON DELETE CASCADE;

--
-- Contraintes pour la table `certificat_participation`
--
ALTER TABLE `certificat_participation`
  ADD CONSTRAINT `certificat_participation_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `utilisateur` (`id_compte`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificat_participation_ibfk_2` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE CASCADE;

--
-- Contraintes pour la table `confirmation_vote`
--
ALTER TABLE `confirmation_vote`
  ADD CONSTRAINT `confirmation_vote_ibfk_1` FOREIGN KEY (`id_vote`) REFERENCES `vote` (`id_vote`) ON DELETE CASCADE;

--
-- Contraintes pour la table `controle_presence`
--
ALTER TABLE `controle_presence`
  ADD CONSTRAINT `controle_presence_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `utilisateur` (`id_compte`) ON DELETE CASCADE,
  ADD CONSTRAINT `controle_presence_ibfk_2` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE CASCADE;

--
-- Contraintes pour la table `nomination`
--
ALTER TABLE `nomination`
  ADD CONSTRAINT `nomination_ibfk_1` FOREIGN KEY (`id_candidature`) REFERENCES `candidature` (`id_candidature`) ON DELETE CASCADE,
  ADD CONSTRAINT `nomination_ibfk_2` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE CASCADE,
  ADD CONSTRAINT `nomination_ibfk_3` FOREIGN KEY (`id_compte`) REFERENCES `candidat` (`id_compte`) ON DELETE CASCADE,
  ADD CONSTRAINT `nomination_ibfk_4` FOREIGN KEY (`id_admin`) REFERENCES `administrateur` (`id_compte`) ON DELETE SET NULL;

--
-- Contraintes pour la table `preuve_depot`
--
ALTER TABLE `preuve_depot`
  ADD CONSTRAINT `preuve_depot_ibfk_1` FOREIGN KEY (`id_vote`) REFERENCES `vote` (`id_vote`) ON DELETE CASCADE;

--
-- Contraintes pour la table `signature_electorale`
--
ALTER TABLE `signature_electorale`
  ADD CONSTRAINT `signature_electorale_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `utilisateur` (`id_compte`) ON DELETE CASCADE,
  ADD CONSTRAINT `signature_electorale_ibfk_2` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE CASCADE;

--
-- Contraintes pour la table `token_anonyme`
--
ALTER TABLE `token_anonyme`
  ADD CONSTRAINT `token_anonyme_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `utilisateur` (`id_compte`) ON DELETE CASCADE,
  ADD CONSTRAINT `token_anonyme_ibfk_2` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE CASCADE;

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `compte` (`id_compte`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vote`
--
ALTER TABLE `vote`
  ADD CONSTRAINT `vote_ibfk_1` FOREIGN KEY (`id_nomination`) REFERENCES `nomination` (`id_nomination`) ON DELETE CASCADE,
  ADD CONSTRAINT `vote_ibfk_2` FOREIGN KEY (`id_token`) REFERENCES `token_anonyme` (`id_token`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
