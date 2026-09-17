-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 15 déc. 2024 à 14:30
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `sae bd`
--

-- --------------------------------------------------------

--
-- Structure de la table `engin`
--

DROP TABLE IF EXISTS `engin`;
CREATE TABLE IF NOT EXISTS `engin` (
  `ID_Engin` int NOT NULL,
  `TypeEngin` varchar(50) NOT NULL,
  `NoEngin` varchar(50) NOT NULL,
  `Marque` varchar(50) NOT NULL,
  `ID_Membre` int DEFAULT NULL,
  PRIMARY KEY (`ID_Engin`),
  KEY `ID_Membre` (`ID_Membre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `engin`
--

INSERT INTO `engin` (`ID_Engin`, `TypeEngin`, `NoEngin`, `Marque`, `ID_Membre`) VALUES
(30, 'dériveur', '1', 'Caravelle', 1),
(31, 'planche', '2', 'Starboard', 2),
(32, 'catamaran', '3', 'Hobie', 3),
(33, 'dériveur', '4', 'Laser', 4);

-- --------------------------------------------------------

--
-- Structure de la table `membre`
--

DROP TABLE IF EXISTS `membre`;
CREATE TABLE IF NOT EXISTS `membre` (
  `ID_Membre` int NOT NULL,
  `NomMembre` varchar(20) NOT NULL,
  `PrenomMembre` varchar(20) NOT NULL,
  `DateNaissance` date NOT NULL,
  `AdresseMembre` varchar(50) DEFAULT NULL,
  `TelMembre` int NOT NULL,
  `MailMembre` varchar(50) NOT NULL,
  PRIMARY KEY (`ID_Membre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `membre`
--

INSERT INTO `membre` (`ID_Membre`, `NomMembre`, `PrenomMembre`, `DateNaissance`, `AdresseMembre`, `TelMembre`, `MailMembre`) VALUES
(1, 'Dupont', 'Jean', '1985-03-15', '12 Rue des Lilas, Paris', 601234567, 'jean.dupont@example.com'),
(2, 'Martin', 'Sophie', '1990-07-22', '34 Avenue de la République’, ‘Lyon', 612345678, 'sophie.martin@example.com'),
(3, 'Leclerc', 'Paul', '1982-11-05', '78 Rue de la Gare, Bordeaux', 623456789, 'paul.leclerc@example.com'),
(4, 'Dufresne', 'Marie', '1975-02-18', '90 Boulevard Haussmann, Paris', 634567890, 'marie.dufresne@example.com'),
(5, 'Bernard', 'Lucie', '2000-06-12', '45 Allée des Fleurs, Nice', 645678901, 'lucie.bernard@example.com');

-- --------------------------------------------------------

--
-- Structure de la table `membre_famille`
--

DROP TABLE IF EXISTS `membre_famille`;
CREATE TABLE IF NOT EXISTS `membre_famille` (
  `ID_Famille` int NOT NULL,
  `PrénomFamille` varchar(100) NOT NULL,
  `DateNaissanceFamille` date NOT NULL,
  `ID_Membre` int DEFAULT NULL,
  PRIMARY KEY (`ID_Famille`),
  KEY `ID_Membre` (`ID_Membre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `membre_famille`
--

INSERT INTO `membre_famille` (`ID_Famille`, `PrénomFamille`, `DateNaissanceFamille`, `ID_Membre`) VALUES
(40, 'Martin', '2001-01-25', 1),
(41, 'Julie', '1995-08-15', 2),
(42, 'Louis', '2008-04-10', 3),
(43, 'Emma', '2010-12-05', 4);

-- --------------------------------------------------------

--
-- Structure de la table `membre_prestation`
--

DROP TABLE IF EXISTS `membre_prestation`;
CREATE TABLE IF NOT EXISTS `membre_prestation` (
  `ID_Membre` int NOT NULL,
  `ID_Prestation` int NOT NULL,
  PRIMARY KEY (`ID_Membre`,`ID_Prestation`),
  KEY `ID_Prestation` (`ID_Prestation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `membre_prestation`
--

INSERT INTO `membre_prestation` (`ID_Membre`, `ID_Prestation`) VALUES
(1, 20),
(2, 21),
(3, 22),
(4, 23),
(5, 20);

-- --------------------------------------------------------

--
-- Structure de la table `prestation`
--

DROP TABLE IF EXISTS `prestation`;
CREATE TABLE IF NOT EXISTS `prestation` (
  `ID_Prestation` int NOT NULL,
  `MembreHonoraire` varchar(20) DEFAULT NULL,
  `MembreActif` varchar(20) DEFAULT NULL,
  `Option` varchar(20) DEFAULT NULL,
  `LSupA` int NOT NULL,
  `LSupJ` int NOT NULL,
  `LTemp` int NOT NULL,
  PRIMARY KEY (`ID_Prestation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `prestation`
--

INSERT INTO `prestation` (`ID_Prestation`, `MembreHonoraire`, `MembreActif`, `Option`, `LSupA`, `LSupJ`, `LTemp`) VALUES
(20, 'familiale', 'adulte', 'Râtelier', 1, 0, 0),
(21, 'adulte', 'adulte', 'Placard', 0, 1, 0),
(22, 'jeune', 'adulte', 'CautionClef', 1, 0, 0),
(23, 'familiale', 'familiale', 'Râtelier', 0, 0, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
