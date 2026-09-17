-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 27 mai 2025 à 09:33
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
-- Base de données : `supermarche`
--

-- --------------------------------------------------------

--
-- Structure de la table `basket`
--

DROP TABLE IF EXISTS `basket`;
CREATE TABLE IF NOT EXISTS `basket` (
  `idBasket` int NOT NULL AUTO_INCREMENT,
  `CreationDate` date DEFAULT NULL,
  `Status` varchar(45) DEFAULT NULL,
  `Customers_idCustomers` int NOT NULL,
  PRIMARY KEY (`idBasket`),
  KEY `fk_Basket_Customers1_idx` (`Customers_idCustomers`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `basket`
--

INSERT INTO `basket` (`idBasket`, `CreationDate`, `Status`, `Customers_idCustomers`) VALUES
(51, '2024-01-03', 'payé', 1),
(52, '2024-01-05', 'en attente', 2),
(53, '2024-01-07', 'payé', 3),
(54, '2024-01-09', 'annulé', 4),
(55, '2024-01-11', 'payé', 5),
(56, '2024-01-13', 'payé', 6),
(57, '2024-01-15', 'en attente', 7),
(58, '2024-01-17', 'payé', 8),
(59, '2024-01-19', 'annulé', 9),
(60, '2024-01-21', 'payé', 10),
(61, '2024-01-23', 'en attente', 11),
(62, '2024-01-25', 'payé', 12),
(63, '2024-01-27', 'payé', 13),
(64, '2024-01-29', 'annulé', 14),
(65, '2024-01-31', 'en attente', 15),
(66, '2024-02-02', 'payé', 16),
(67, '2024-02-04', 'payé', 17),
(68, '2024-02-06', 'annulé', 18),
(69, '2024-02-08', 'payé', 19),
(70, '2024-02-10', 'payé', 20),
(71, '2024-02-12', 'en attente', 21),
(72, '2024-02-14', 'payé', 22),
(73, '2024-02-16', 'annulé', 23),
(74, '2024-02-18', 'payé', 24),
(75, '2024-02-20', 'en attente', 25),
(76, '2024-02-22', 'payé', 26),
(77, '2024-02-24', 'payé', 27),
(78, '2024-02-26', 'annulé', 28),
(79, '2024-02-28', 'payé', 29),
(80, '2024-03-01', 'en attente', 30),
(81, '2024-03-03', 'payé', 31),
(82, '2024-03-05', 'payé', 32),
(83, '2024-03-07', 'annulé', 33),
(84, '2024-03-09', 'en attente', 34),
(85, '2024-03-11', 'payé', 35),
(86, '2024-03-13', 'payé', 36),
(87, '2024-03-15', 'annulé', 37),
(88, '2024-03-17', 'payé', 38),
(89, '2024-03-19', 'en attente', 39),
(90, '2024-03-21', 'payé', 40),
(91, '2024-03-23', 'payé', 41),
(92, '2024-03-25', 'annulé', 42),
(93, '2024-03-27', 'payé', 43),
(94, '2024-03-29', 'payé', 44),
(95, '2024-03-31', 'payé', 45),
(96, '2024-04-02', 'en attente', 46),
(97, '2024-04-04', 'payé', 47),
(98, '2024-04-06', 'payé', 48),
(99, '2024-04-08', 'annulé', 49),
(100, '2024-04-10', 'payé', 50),
(101, '2025-05-27', 'open', 1);

-- --------------------------------------------------------

--
-- Structure de la table `basketline`
--

DROP TABLE IF EXISTS `basketline`;
CREATE TABLE IF NOT EXISTS `basketline` (
  `idBasketLine` int NOT NULL AUTO_INCREMENT,
  `Quantity` int DEFAULT NULL,
  `UnitPrice` decimal(10,2) DEFAULT NULL,
  `Basket_idBasket` int NOT NULL,
  `Product_idProduct` int NOT NULL,
  PRIMARY KEY (`idBasketLine`),
  KEY `fk_BasketLine_Basket1_idx` (`Basket_idBasket`),
  KEY `fk_BasketLine_Product1_idx` (`Product_idProduct`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `basketline`
--

INSERT INTO `basketline` (`idBasketLine`, `Quantity`, `UnitPrice`, `Basket_idBasket`, `Product_idProduct`) VALUES
(51, 2, 4.99, 1, 10),
(52, 1, 2.50, 2, 7),
(53, 3, 1.29, 3, 4),
(54, 2, 3.75, 4, 12),
(55, 5, 0.99, 5, 8),
(56, 1, 6.49, 6, 13),
(57, 4, 2.30, 7, 3),
(58, 2, 1.75, 8, 14),
(59, 1, 8.99, 9, 21),
(60, 6, 1.09, 10, 5),
(61, 3, 2.49, 11, 16),
(62, 2, 3.39, 12, 6),
(63, 1, 7.25, 13, 19),
(64, 4, 1.50, 14, 22),
(65, 2, 2.00, 15, 11),
(66, 3, 5.50, 16, 18),
(67, 1, 0.89, 17, 9),
(68, 2, 4.29, 18, 24),
(69, 3, 3.69, 19, 2),
(70, 1, 9.49, 20, 15),
(71, 4, 2.75, 21, 17),
(72, 2, 5.10, 22, 20),
(73, 1, 3.99, 23, 25),
(74, 2, 6.29, 24, 26),
(75, 3, 2.99, 25, 1),
(76, 2, 1.89, 26, 23),
(77, 4, 3.00, 27, 27),
(78, 1, 4.99, 28, 30),
(79, 3, 2.79, 29, 28),
(80, 5, 0.99, 30, 29),
(81, 2, 1.59, 31, 31),
(82, 1, 3.45, 32, 33),
(83, 3, 2.20, 33, 34),
(84, 2, 4.60, 34, 35),
(85, 1, 7.89, 35, 36),
(86, 4, 1.20, 36, 37),
(87, 2, 5.75, 37, 38),
(88, 3, 2.95, 38, 39),
(89, 1, 6.49, 39, 40),
(90, 2, 2.79, 40, 41),
(91, 1, 3.25, 41, 42),
(92, 2, 4.10, 42, 43),
(93, 3, 1.99, 43, 44),
(94, 4, 0.99, 44, 45),
(95, 2, 6.25, 45, 46),
(96, 1, 5.99, 46, 47),
(97, 2, 3.10, 47, 48),
(98, 3, 2.45, 48, 49),
(99, 1, 4.30, 49, 50),
(100, 2, 2.00, 50, 32);

--
-- Déclencheurs `basketline`
--
DROP TRIGGER IF EXISTS `checkStockBeforeBasketInsert`;
DELIMITER $$
CREATE TRIGGER `checkStockBeforeBasketInsert` BEFORE INSERT ON `basketline` FOR EACH ROW BEGIN
    DECLARE availableQuantity INT;

    SELECT SUM(QuantityAvailable) INTO availableQuantity
    FROM Stock
    WHERE Product_idProduct = NEW.Product_idProduct;

    IF NEW.Quantity > IFNULL(availableQuantity, 0) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Not enough stock available for this product.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `bill`
--

DROP TABLE IF EXISTS `bill`;
CREATE TABLE IF NOT EXISTS `bill` (
  `idBill` int NOT NULL AUTO_INCREMENT,
  `Date` date DEFAULT NULL,
  `TotalAmount` decimal(2,0) DEFAULT NULL,
  `PaymentMethod` varchar(45) NOT NULL,
  `Customers_idCustomers` int NOT NULL,
  PRIMARY KEY (`idBill`),
  KEY `fk_Bill_Customers1_idx` (`Customers_idCustomers`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `bill`
--

INSERT INTO `bill` (`idBill`, `Date`, `TotalAmount`, `PaymentMethod`, `Customers_idCustomers`) VALUES
(1, '2024-01-03', 55, 'CB', 1),
(2, '2024-01-05', 15, 'Espèces', 2),
(3, '2024-01-07', 64, 'CB', 3),
(4, '2024-01-09', 13, 'Chèque', 4),
(5, '2024-01-11', 33, 'CB', 5),
(6, '2024-01-13', 28, 'Espèces', 6),
(7, '2024-01-15', 42, 'CB', 7),
(8, '2024-01-17', 39, 'Chèque', 8),
(9, '2024-01-19', 22, 'CB', 9),
(10, '2024-01-21', 16, 'Espèces', 10),
(11, '2024-01-23', 56, 'CB', 11),
(12, '2024-01-25', 9, 'CB', 12),
(13, '2024-01-27', 35, 'Espèces', 13),
(14, '2024-01-29', 50, 'CB', 14),
(15, '2024-01-31', 20, 'Chèque', 15),
(16, '2024-02-02', 27, 'CB', 16),
(17, '2024-02-04', 41, 'CB', 17),
(18, '2024-02-06', 10, 'Espèces', 18),
(19, '2024-02-08', 32, 'CB', 19),
(20, '2024-02-10', 22, 'Chèque', 20),
(21, '2024-02-12', 18, 'CB', 21),
(22, '2024-02-14', 37, 'Espèces', 22),
(23, '2024-02-16', 10, 'CB', 23),
(24, '2024-02-18', 45, 'Chèque', 24),
(25, '2024-02-20', 51, 'CB', 25),
(26, '2024-02-22', 13, 'Espèces', 26),
(27, '2024-02-24', 27, 'CB', 27),
(28, '2024-02-26', 7, 'CB', 28),
(29, '2024-02-28', 19, 'Espèces', 29),
(30, '2024-03-01', 31, 'CB', 30),
(31, '2024-03-03', 40, 'CB', 31),
(32, '2024-03-05', 25, 'Espèces', 32),
(33, '2024-03-07', 36, 'CB', 33),
(34, '2024-03-09', 9, 'Chèque', 34),
(35, '2024-03-11', 13, 'CB', 35),
(36, '2024-03-13', 19, 'Espèces', 36),
(37, '2024-03-15', 60, 'CB', 37),
(38, '2024-03-17', 45, 'Chèque', 38),
(39, '2024-03-19', 16, 'CB', 39),
(40, '2024-03-21', 20, 'CB', 40),
(41, '2024-03-23', 36, 'Espèces', 41),
(42, '2024-03-25', 10, 'CB', 42),
(43, '2024-03-27', 28, 'CB', 43),
(44, '2024-03-29', 14, 'Espèces', 44),
(45, '2024-03-31', 23, 'CB', 45),
(46, '2024-04-02', 38, 'Chèque', 46),
(47, '2024-04-04', 11, 'CB', 47),
(48, '2024-04-06', 11, 'CB', 48),
(49, '2024-04-08', 29, 'Espèces', 49),
(50, '2024-04-10', 56, 'CB', 50);

-- --------------------------------------------------------

--
-- Structure de la table `billline`
--

DROP TABLE IF EXISTS `billline`;
CREATE TABLE IF NOT EXISTS `billline` (
  `idBillLine` int NOT NULL AUTO_INCREMENT,
  `Quantity` int DEFAULT NULL,
  `UnitPrice` decimal(10,2) DEFAULT NULL,
  `SubTotal` decimal(10,2) DEFAULT NULL,
  `Bill_idBill` int NOT NULL,
  `Product_idProduct` int NOT NULL,
  PRIMARY KEY (`idBillLine`),
  KEY `fk_BillLine_Bill1_idx` (`Bill_idBill`),
  KEY `fk_BillLine_Product1_idx` (`Product_idProduct`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `billline`
--

INSERT INTO `billline` (`idBillLine`, `Quantity`, `UnitPrice`, `SubTotal`, `Bill_idBill`, `Product_idProduct`) VALUES
(1, 2, 5.90, 11.80, 1, 3),
(2, 1, 12.30, 12.30, 2, 8),
(3, 3, 4.75, 14.25, 3, 15),
(4, 4, 6.10, 24.40, 4, 22),
(5, 1, 2.99, 2.99, 5, 10),
(6, 5, 3.50, 17.50, 6, 6),
(7, 2, 8.20, 16.40, 7, 12),
(8, 1, 15.90, 15.90, 8, 19),
(9, 2, 7.35, 14.70, 9, 24),
(10, 3, 9.10, 27.30, 10, 1),
(11, 1, 18.40, 18.40, 11, 4),
(12, 2, 4.20, 8.40, 12, 9),
(13, 4, 6.50, 26.00, 13, 30),
(14, 1, 10.99, 10.99, 14, 18),
(15, 5, 2.10, 10.50, 15, 2),
(16, 3, 11.70, 35.10, 16, 20),
(17, 2, 13.00, 26.00, 17, 25),
(18, 4, 7.80, 31.20, 18, 27),
(19, 1, 5.99, 5.99, 19, 5),
(20, 2, 6.60, 13.20, 20, 11),
(21, 1, 8.30, 8.30, 21, 7),
(22, 3, 3.60, 10.80, 22, 13),
(23, 2, 12.40, 24.80, 23, 14),
(24, 4, 9.99, 39.96, 24, 16),
(25, 1, 14.50, 14.50, 25, 17),
(26, 3, 7.10, 21.30, 26, 21),
(27, 2, 4.50, 9.00, 27, 23),
(28, 5, 3.75, 18.75, 28, 26),
(29, 1, 6.20, 6.20, 29, 28),
(30, 2, 7.90, 15.80, 30, 29),
(31, 4, 5.10, 20.40, 31, 31),
(32, 3, 6.30, 18.90, 32, 32),
(33, 2, 9.40, 18.80, 33, 33),
(34, 1, 11.60, 11.60, 34, 34),
(35, 2, 3.30, 6.60, 35, 35),
(36, 5, 4.20, 21.00, 36, 36),
(37, 4, 2.50, 10.00, 37, 37),
(38, 3, 8.80, 26.40, 38, 38),
(39, 1, 13.90, 13.90, 39, 39),
(40, 2, 6.00, 12.00, 40, 40),
(41, 3, 7.50, 22.50, 41, 41),
(42, 4, 9.70, 38.80, 42, 42),
(43, 1, 5.30, 5.30, 43, 43),
(44, 2, 7.40, 14.80, 44, 44),
(45, 3, 10.25, 30.75, 45, 45),
(46, 1, 6.70, 6.70, 46, 46),
(47, 2, 12.10, 24.20, 47, 47),
(48, 4, 4.90, 19.60, 48, 48),
(49, 3, 5.80, 17.40, 49, 49),
(50, 1, 9.99, 9.99, 50, 50),
(51, 2, 12.50, 25.00, 1, 3);

--
-- Déclencheurs `billline`
--
DROP TRIGGER IF EXISTS `updateBillTotalAfterAddingBillLine`;
DELIMITER $$
CREATE TRIGGER `updateBillTotalAfterAddingBillLine` AFTER INSERT ON `billline` FOR EACH ROW BEGIN
    UPDATE Bill
    SET TotalAmount = TotalAmount + NEW.SubTotal
    WHERE idBill = NEW.Bill_idBill;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `idCustomers` int NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(45) DEFAULT NULL,
  `LastName` varchar(45) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Address` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idCustomers`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `customers`
--

INSERT INTO `customers` (`idCustomers`, `FirstName`, `LastName`, `PhoneNumber`, `Email`, `Address`) VALUES
(1, 'Alice', 'Dupont', '0612345678', 'alice.dupont@gmail.com', '12 rue des Lilas, Lyon'),
(2, 'Lucas', 'Martin', '0678123456', 'lucas.martin@hotmail.fr', '8 avenue Victor Hugo, Paris'),
(3, 'Emma', 'Leroy', '0623456789', 'emma.leroy@yahoo.fr', '15 chemin des Vignes, Marseille'),
(4, 'Nathan', 'Petit', '0654321876', 'nathan.petit@orange.fr', '27 rue Pasteur, Toulouse'),
(5, 'Chloé', 'Roux', '0645678912', 'chloe.roux@gmail.com', '33 boulevard Haussmann, Paris'),
(6, 'Louis', 'Moreau', '0667890123', 'louis.moreau@free.fr', '5 rue de Bretagne, Nantes'),
(7, 'Léa', 'Garnier', '0634567890', 'lea.garnier@sfr.fr', '19 rue du Château, Strasbourg'),
(8, 'Hugo', 'Chevalier', '0689012345', 'hugo.chevalier@gmail.com', '22 rue Nationale, Bordeaux'),
(9, 'Manon', 'Lambert', '0611223344', 'manon.lambert@wanadoo.fr', '17 rue des Fleurs, Montpellier'),
(10, 'Enzo', 'Schmitt', '0677001122', 'enzo.schmitt@gmail.com', '44 rue Alsace Lorraine, Metz'),
(11, 'Camille', 'Colin', '0655443322', 'camille.colin@hotmail.com', '3 rue Saint-Honoré, Paris'),
(12, 'Gabriel', 'Marchand', '0622113344', 'gabriel.marchand@live.fr', '88 rue de la République, Reims'),
(13, 'Jade', 'Perrot', '0699887766', 'jade.perrot@gmail.com', '11 rue des Acacias, Lille'),
(14, 'Mathis', 'Barbier', '0666007788', 'mathis.barbier@orange.fr', '24 rue du Bac, Tours'),
(15, 'Clara', 'Noël', '0644778899', 'clara.noel@outlook.fr', '29 avenue des Champs, Dijon'),
(16, 'Tom', 'Renard', '0611445566', 'tom.renard@gmail.com', '77 rue Jean Jaurès, Nice'),
(17, 'Sarah', 'Carpentier', '0633221100', 'sarah.carpentier@free.fr', '10 rue de la Liberté, Grenoble'),
(18, 'Axel', 'Leclerc', '0699332211', 'axel.leclerc@laposte.net', '41 rue des Frères Lumière, Caen'),
(19, 'Léna', 'Giraud', '0677996655', 'lena.giraud@gmail.com', '6 rue Saint-Nicolas, Avignon'),
(20, 'Noah', 'Perrin', '0655442211', 'noah.perrin@sfr.fr', '13 rue Carnot, Rennes'),
(21, 'Eva', 'Rodriguez', '0688991122', 'eva.rodriguez@hotmail.fr', '9 rue Jules Ferry, Le Mans'),
(22, 'Léo', 'Blanc', '0677889900', 'leo.blanc@gmail.com', '2 rue des Lilas, Nîmes'),
(23, 'Julie', 'Henry', '0611335577', 'julie.henry@orange.fr', '25 avenue Jean Moulin, Poitiers'),
(24, 'Arthur', 'Roy', '0633009988', 'arthur.roy@gmail.com', '18 rue de l’Église, Pau'),
(25, 'Anna', 'Bonnet', '0622887766', 'anna.bonnet@wanadoo.fr', '56 avenue Gambetta, Clermont-Ferrand'),
(26, 'Paul', 'Bertrand', '0644112233', 'paul.bertrand@gmail.com', '35 rue de Sèvres, Angers'),
(27, 'Elisa', 'Moulin', '0655990088', 'elisa.moulin@yahoo.fr', '14 rue Victor Hugo, Limoges'),
(28, 'Théo', 'Guillot', '0688007766', 'theo.guillot@gmail.com', '90 boulevard de la Paix, Perpignan'),
(29, 'Inès', 'Faure', '0699775544', 'ines.faure@sfr.fr', '61 rue des Platanes, Saint-Étienne'),
(30, 'Romain', 'Navarro', '0622446677', 'romain.navarro@gmail.com', '20 rue Neuve, Besançon'),
(31, 'Zoé', 'Pires', '0633557799', 'zoe.pires@free.fr', '7 rue du Faubourg, Amiens'),
(32, 'Ethan', 'Lopez', '0612889900', 'ethan.lopez@live.fr', '38 rue Henri Barbusse, La Rochelle'),
(33, 'Mélanie', 'Fernandez', '0677008899', 'melanie.fernandez@gmail.com', '16 rue de Lorraine, Reims'),
(34, 'Baptiste', 'Masson', '0655223377', 'baptiste.masson@sfr.fr', '55 avenue de la Gare, Troyes'),
(35, 'Sophie', 'Loiseau', '0699221100', 'sophie.loiseau@gmail.com', '30 rue de Paris, Orléans'),
(36, 'Yanis', 'Gilbert', '0622003344', 'yanis.gilbert@orange.fr', '45 rue Lafayette, Rouen'),
(37, 'Isabelle', 'Maillard', '0688112244', 'isabelle.maillard@live.fr', '36 rue des Champs, Valence'),
(38, 'Antoine', 'Benoit', '0666110099', 'antoine.benoit@hotmail.com', '70 rue Jules Vallès, Annecy'),
(39, 'Lina', 'Hoarau', '0655887766', 'lina.hoarau@laposte.net', '48 boulevard Léon Blum, Albi'),
(40, 'Victor', 'Baron', '0611554433', 'victor.baron@gmail.com', '23 rue du 11 Novembre, Mulhouse'),
(41, 'Maëlle', 'Philippe', '0677886677', 'maelle.philippe@orange.fr', '42 rue des Peupliers, Béziers'),
(42, 'Loris', 'Chapel', '0633778899', 'loris.chapel@gmail.com', '67 avenue des Alliés, Arles'),
(43, 'Carla', 'Robin', '0699001122', 'carla.robin@yahoo.fr', '39 rue Marceau, Bayonne'),
(44, 'Julien', 'Dufour', '0611667788', 'julien.dufour@gmail.com', '26 rue du Faubourg Saint-Antoine, Nancy'),
(45, 'Amélie', 'Paul', '0622334455', 'amelie.paul@hotmail.fr', '10 rue des Lilas, Colmar'),
(46, 'Kylian', 'Gomez', '0644225566', 'kylian.gomez@gmail.com', '71 rue Voltaire, Brive-la-Gaillarde'),
(47, 'Célia', 'Rolland', '0655778899', 'celia.rolland@sfr.fr', '13 rue des Jardins, Bastia'),
(48, 'Mickaël', 'Lejeune', '0688223344', 'mickael.lejeune@gmail.com', '6 rue du Port, Tarbes'),
(49, 'Florian', 'Gauthier', '0666991122', 'florian.gauthier@orange.fr', '33 rue Saint-Martin, Niort'),
(50, 'Nina', 'Texier', '0633445566', 'nina.texier@gmail.com', '79 avenue Foch, Chartres');

-- --------------------------------------------------------

--
-- Structure de la table `departement`
--

DROP TABLE IF EXISTS `departement`;
CREATE TABLE IF NOT EXISTS `departement` (
  `idDepartement` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `Description` varchar(100) NOT NULL,
  PRIMARY KEY (`idDepartement`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `departement`
--

INSERT INTO `departement` (`idDepartement`, `Name`, `Description`) VALUES
(1, 'Fruits & Légumes', 'Rayon des produits frais issus de l’agriculture'),
(2, 'Boucherie', 'Viandes fraîches de bœuf, porc, volaille, etc.'),
(3, 'Poissonnerie', 'Produits de la mer frais ou surgelés'),
(4, 'Boulangerie', 'Pains, viennoiseries et pâtisseries artisanales'),
(5, 'Pâtisserie', 'Gâteaux, tartes et douceurs sucrées'),
(6, 'Charcuterie', 'Jambons, saucissons, terrines et produits de porc'),
(7, 'Fromagerie', 'Sélection de fromages locaux et internationaux'),
(8, 'Épicerie salée', 'Pâtes, riz, conserves, sauces, huiles...'),
(9, 'Épicerie sucrée', 'Biscuits, chocolats, confiseries et desserts'),
(10, 'Produits surgelés', 'Plats préparés, glaces et légumes congelés'),
(11, 'Boissons', 'Sodas, jus, eaux minérales et boissons non alcoolisées'),
(12, 'Vins & spiritueux', 'Vins, bières, champagnes et alcools forts'),
(13, 'Produits du monde', 'Cuisine asiatique, mexicaine, indienne...'),
(14, 'Produits bio', 'Alimentation et soins issus de l’agriculture biologique'),
(15, 'Produits locaux', 'Spécialités et produits régionaux'),
(16, 'Petit déjeuner', 'Céréales, confitures, tartines, boissons chaudes'),
(17, 'Bébé', 'Laits, couches, petits pots et soins pour bébés'),
(18, 'Animaux', 'Nourriture et accessoires pour chiens, chats, etc.'),
(19, 'Entretien', 'Produits ménagers, lessive, vaisselle, etc.'),
(20, 'Hygiène & beauté', 'Shampooing, savon, dentifrice, maquillage'),
(21, 'Pharmacie', 'Parapharmacie et premiers soins'),
(22, 'Papeterie', 'Fournitures scolaires, cahiers, stylos'),
(23, 'Textile', 'Vêtements, sous-vêtements, chaussettes'),
(24, 'Électroménager', 'Petits appareils : grille-pains, cafetières...'),
(25, 'Multimédia', 'Accessoires pour téléphones, écouteurs, piles'),
(26, 'Jouets', 'Jeux, peluches, jeux de société pour enfants'),
(27, 'Loisirs créatifs', 'Peinture, loisirs manuels, fournitures DIY'),
(28, 'Auto', 'Accessoires et entretien pour voiture'),
(29, 'Jardin', 'Outils, plantes, terreaux, décoration extérieure'),
(30, 'Bricolage', 'Vis, tournevis, colle, peinture, outils'),
(31, 'Cuisine', 'Casseroles, poêles, ustensiles et gadgets de cuisine'),
(32, 'Décoration', 'Objets déco, bougies, cadres photo, etc.'),
(33, 'Linge de maison', 'Draps, serviettes, nappes, oreillers'),
(34, 'Literie', 'Matelas, couettes, protège-matelas'),
(35, 'Mobilier', 'Chaises, petites étagères, tables basses'),
(36, 'Bagagerie', 'Valises, sacs, trousses de toilette'),
(37, 'Chaussures', 'Pour hommes, femmes et enfants'),
(38, 'Sport', 'Vêtements de sport, accessoires fitness'),
(39, 'Équipement bébé', 'Poussettes, sièges auto, biberons'),
(40, 'Parfumerie', 'Parfums, déodorants, eaux de toilette'),
(41, 'Produits ménagers écolos', 'Entretien respectueux de l’environnement'),
(42, 'Santé naturelle', 'Compléments, tisanes, huiles essentielles'),
(43, 'Informatique', 'Claviers, souris, clés USB'),
(44, 'Téléphonie', 'Coques, câbles, chargeurs'),
(45, 'Librairie', 'Romans, magazines, bandes dessinées'),
(46, 'Horlogerie', 'Montres, piles, réveils'),
(47, 'Bijouterie', 'Bagues, colliers, boucles d’oreilles'),
(48, 'Produits festifs', 'Décorations de fêtes, déguisements');

-- --------------------------------------------------------

--
-- Structure de la table `employee`
--

DROP TABLE IF EXISTS `employee`;
CREATE TABLE IF NOT EXISTS `employee` (
  `idEmployee` int NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(45) NOT NULL,
  `LastName` varchar(45) NOT NULL,
  `Position` varchar(45) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Departement_idDepartement` int NOT NULL,
  PRIMARY KEY (`idEmployee`),
  KEY `fk_Employee_Departement1_idx` (`Departement_idDepartement`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `employee`
--

INSERT INTO `employee` (`idEmployee`, `FirstName`, `LastName`, `Position`, `PhoneNumber`, `Email`, `Departement_idDepartement`) VALUES
(1, 'Julie', 'Martin', 'Responsable rayon', '0601020304', 'julie.martin@supermarche.fr', 1),
(2, 'Paul', 'Durand', 'Employé polyvalent', '0611223344', 'paul.durand@supermarche.fr', 2),
(3, 'Emma', 'Bernard', 'Chef de rayon', '0622334455', 'emma.bernard@supermarche.fr', 3),
(4, 'Lucas', 'Petit', 'Caissier', '0633445566', 'lucas.petit@supermarche.fr', 4),
(5, 'Chloé', 'Robert', 'Employée libre service', '0644556677', 'chloe.robert@supermarche.fr', 5),
(6, 'Léo', 'Richard', 'Responsable stock', '0655667788', 'leo.richard@supermarche.fr', 6),
(7, 'Manon', 'Dubois', 'Chef caisse', '0666778899', 'manon.dubois@supermarche.fr', 7),
(8, 'Hugo', 'Moreau', 'Employé drive', '0677889900', 'hugo.moreau@supermarche.fr', 8),
(9, 'Camille', 'Fournier', 'Responsable sécurité', '0688990011', 'camille.fournier@supermarche.fr', 9),
(10, 'Nathan', 'Girard', 'Responsable rayon', '0699001122', 'nathan.girard@supermarche.fr', 10),
(11, 'Laura', 'Lemoine', 'Caissière', '0600011122', 'laura.lemoine@supermarche.fr', 1),
(12, 'Antoine', 'Roux', 'Chef de secteur', '0600022233', 'antoine.roux@supermarche.fr', 2),
(13, 'Sophie', 'Marchand', 'Responsable commandes', '0600033344', 'sophie.marchand@supermarche.fr', 3),
(14, 'Thomas', 'Guillot', 'Employé libre service', '0600044455', 'thomas.guillot@supermarche.fr', 4),
(15, 'Marie', 'Perez', 'Responsable rayon', '0600055566', 'marie.perez@supermarche.fr', 5),
(16, 'Alexandre', 'Muller', 'Magasinier', '0600066677', 'alexandre.muller@supermarche.fr', 6),
(17, 'Clara', 'Leclerc', 'Réceptionnaire', '0600077788', 'clara.leclerc@supermarche.fr', 7),
(18, 'Matthieu', 'Colin', 'Caissier', '0600088899', 'matthieu.colin@supermarche.fr', 8),
(19, 'Julie', 'Vidal', 'Chef caisse', '0600099900', 'julie.vidal@supermarche.fr', 9),
(20, 'Baptiste', 'Lopez', 'Chef rayon', '0600101010', 'baptiste.lopez@supermarche.fr', 10),
(21, 'Inès', 'Fontaine', 'Caissière', '0600111122', 'ines.fontaine@supermarche.fr', 11),
(22, 'Victor', 'Lambert', 'Responsable stock', '0600122233', 'victor.lambert@supermarche.fr', 12),
(23, 'Lucie', 'Blanchard', 'Employée rayon', '0600133344', 'lucie.blanchard@supermarche.fr', 13),
(24, 'Noah', 'Garnier', 'Réception marchandise', '0600144455', 'noah.garnier@supermarche.fr', 14),
(25, 'Léa', 'Chevalier', 'Employée polyvalente', '0600155566', 'lea.chevalier@supermarche.fr', 15),
(26, 'Louis', 'Faure', 'Responsable rayon', '0600166677', 'louis.faure@supermarche.fr', 16),
(27, 'Émilie', 'Roy', 'Caissière', '0600177788', 'emilie.roy@supermarche.fr', 17),
(28, 'Gabriel', 'Gomez', 'Manager', '0600188899', 'gabriel.gomez@supermarche.fr', 18),
(29, 'Sarah', 'Barbier', 'Conseillère beauté', '0600199900', 'sarah.barbier@supermarche.fr', 19),
(30, 'Maxime', 'Renaud', 'Livreur drive', '0600202020', 'maxime.renaud@supermarche.fr', 20),
(31, 'Clément', 'Charles', 'Boucher', '0600212121', 'clement.charles@supermarche.fr', 1),
(32, 'Élodie', 'Maillard', 'Poissonnière', '0600223232', 'elodie.maillard@supermarche.fr', 2),
(33, 'Axel', 'Perrot', 'Responsable hygiène', '0600234343', 'axel.perrot@supermarche.fr', 3),
(34, 'Amélie', 'Payet', 'Chef de rayon', '0600245454', 'amelie.payet@supermarche.fr', 4),
(35, 'Romain', 'Pires', 'Chef de secteur', '0600256565', 'romain.pires@supermarche.fr', 5),
(36, 'Océane', 'Adam', 'Hôtesse de caisse', '0600267676', 'oceane.adam@supermarche.fr', 6),
(37, 'Kévin', 'Benoît', 'Magasinier', '0600278787', 'kevin.benoit@supermarche.fr', 7),
(38, 'Nina', 'Grondin', 'Réceptionniste', '0600289898', 'nina.grondin@supermarche.fr', 8),
(39, 'Yanis', 'Le Gall', 'Employé drive', '0600299999', 'yanis.legall@supermarche.fr', 9),
(40, 'Amandine', 'Noël', 'Animatrice promo', '0600301010', 'amandine.noel@supermarche.fr', 10);

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `idProduct` int NOT NULL AUTO_INCREMENT,
  `NameProduct` varchar(45) NOT NULL,
  `DescriptionProduct` varchar(100) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `ExpirationDate` date NOT NULL,
  `StockQuantity` int NOT NULL,
  `Departement_idDepartement` int NOT NULL,
  PRIMARY KEY (`idProduct`),
  KEY `fk_Product_Departement1_idx` (`Departement_idDepartement`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `product`
--

INSERT INTO `product` (`idProduct`, `NameProduct`, `DescriptionProduct`, `Price`, `ExpirationDate`, `StockQuantity`, `Departement_idDepartement`) VALUES
(1, 'Pâtes Barilla 500g', 'Pâtes alimentaires en sachet de 500g', 1.29, '2025-12-01', 200, 1),
(2, 'Riz Basmati 1kg', 'Riz basmati parfumé qualité premium', 2.99, '2026-01-15', 150, 1),
(3, 'Steak haché 15% MG', 'Boîte de 4 steaks hachés surgelés', 4.49, '2025-10-30', 100, 2),
(4, 'Yaourt nature x12', 'Yaourts nature sans sucre ajouté', 3.89, '2025-06-15', 250, 3),
(5, 'Beurre doux 250g', 'Beurre de baratte doux', 2.29, '2025-09-10', 180, 3),
(6, 'Jambon blanc x6', 'Tranches de jambon supérieur', 3.49, '2025-05-25', 120, 2),
(7, 'Pain de mie complet', 'Pain de mie tranché sans croûte', 1.99, '2025-05-22', 300, 4),
(8, 'Lait demi-écrémé 1L', 'Bouteille de lait UHT demi-écrémé', 0.89, '2025-09-01', 400, 3),
(9, 'Coca-Cola 1.5L', 'Soda au cola bouteille plastique', 1.75, '2026-03-01', 300, 5),
(10, 'Eau minérale Evian 1.5L', 'Eau minérale naturelle', 0.99, '2026-12-31', 500, 5),
(11, 'Shampooing Elsève 250ml', 'Shampooing pour cheveux normaux', 3.29, '2027-01-01', 120, 6),
(12, 'Gel douche Axe 250ml', 'Gel douche homme parfum intense', 2.75, '2026-11-20', 140, 6),
(13, 'Brosse à dents souple', 'Brosse à dents souple adulte', 1.99, '2030-12-31', 80, 6),
(14, 'Dentifrice Signal 75ml', 'Dentifrice blancheur éclat', 2.49, '2026-08-10', 160, 6),
(15, 'Liquide vaisselle Paic 500ml', 'Détergent pour la vaisselle main', 2.19, '2027-07-20', 130, 7),
(16, 'Lessive Ariel 2L', 'Lessive liquide pour 30 lavages', 7.99, '2026-06-01', 90, 7),
(17, 'Éponge grattante x3', 'Éponge double face usage cuisine', 1.59, '2030-12-31', 200, 7),
(18, 'Tablette lave-vaisselle x30', 'Pastilles lave-vaisselle tout-en-un', 5.99, '2026-09-30', 110, 7),
(19, 'Farine de blé 1kg', 'Farine T45 pour pâtisserie', 1.15, '2026-02-01', 300, 1),
(20, 'Sucre en poudre 1kg', 'Sucre blanc raffiné', 1.10, '2026-04-01', 320, 1),
(21, 'Boîte de thon naturel 140g', 'Thon au naturel sans conservateurs', 1.99, '2027-01-01', 220, 2),
(22, 'Maïs doux en boîte', 'Maïs croquant sous vide', 1.49, '2026-12-15', 180, 2),
(23, 'Haricots verts 400g', 'Conserve de haricots extra-fins', 1.79, '2026-10-10', 190, 2),
(24, 'Pomme Golden x6', 'Pommes Golden France 1kg', 2.99, '2025-05-21', 170, 8),
(25, 'Banane Cavendish x5', 'Bananes mûres prêtes à consommer', 2.49, '2025-05-23', 200, 8),
(26, 'Orange à jus 1kg', 'Oranges juteuses pour jus frais', 3.29, '2025-05-27', 160, 8),
(27, 'Carottes lavées 1kg', 'Carottes origine France', 1.39, '2025-05-30', 250, 8),
(28, 'Pommes de terre 2.5kg', 'Pommes de terre à chair ferme', 3.19, '2025-06-15', 180, 8),
(29, 'Salade verte', 'Laitue fraîche en sachet', 1.49, '2025-05-20', 140, 8),
(30, 'Oignons jaunes 1kg', 'Oignons calibrés pour cuisine', 1.79, '2025-06-01', 200, 8),
(31, 'Biscuits Petit Beurre', 'Biscuits secs pur beurre', 1.69, '2026-08-10', 210, 9),
(32, 'Chocolat noir 70% 100g', 'Tablette chocolat noir intense', 1.89, '2026-10-01', 160, 9),
(33, 'Compote pomme x4', 'Gourdes de compote sans sucre', 2.39, '2025-11-01', 180, 3),
(34, 'Yaourt à boire fraise x6', 'Boisson lactée aux fruits', 2.99, '2025-06-10', 170, 3),
(35, 'Crème fraîche 20cl', 'Crème épaisse légère', 1.29, '2025-06-05', 160, 3),
(36, 'Fromage râpé 200g', 'Emmental râpé sachet', 2.49, '2025-06-12', 150, 3),
(37, 'Œufs x12', 'Œufs frais plein air catégorie A', 3.49, '2025-06-07', 200, 3),
(38, 'Pizza surgelée 4 fromages', 'Pizza à cuire au four', 3.99, '2026-01-01', 140, 2),
(39, 'Glace vanille 1L', 'Crème glacée saveur vanille', 4.29, '2026-02-01', 130, 2),
(40, 'Crème dessert chocolat x4', 'Crèmes dessert au lait entier', 2.89, '2025-06-09', 150, 3),
(41, 'Tisane verveine menthe', 'Infusion naturelle relaxante', 2.59, '2026-12-01', 110, 10),
(42, 'Café moulu 250g', 'Café pur arabica', 3.99, '2026-11-10', 130, 10),
(43, 'Thé vert à la menthe', 'Boîte de 25 sachets', 2.49, '2027-01-01', 140, 10),
(44, 'Huile d\'olive 1L', 'Huile extra vierge première pression', 6.49, '2027-03-01', 100, 1),
(45, 'Vinaigre balsamique', 'Vinaigre de Modène 50cl', 2.29, '2027-01-15', 120, 1),
(46, 'Cornichons extra-fins', 'Bocal 370ml', 1.99, '2026-08-15', 90, 2),
(47, 'Moutarde de Dijon 250g', 'Moutarde forte de Dijon', 1.45, '2027-02-01', 100, 2),
(48, 'Mayonnaise en tube', 'Sauce mayonnaise 200g', 1.85, '2026-11-20', 110, 2),
(49, 'Chips nature 150g', 'Chips croustillantes nature', 1.99, '2026-07-01', 180, 9),
(50, 'Bonbons fruités', 'Sachet de bonbons assortis', 2.49, '2027-01-01', 160, 9);

--
-- Déclencheurs `product`
--
DROP TRIGGER IF EXISTS `preventProductDeletion`;
DELIMITER $$
CREATE TRIGGER `preventProductDeletion` BEFORE DELETE ON `product` FOR EACH ROW BEGIN
    DECLARE countBillLines INT DEFAULT 0;
    DECLARE countSaleLines INT DEFAULT 0;
    DECLARE countPromotions INT DEFAULT 0;

    -- Vérifie dans BillLine
    SELECT COUNT(*) INTO countBillLines
    FROM BillLine
    WHERE Product_idProduct = OLD.idProduct;

    IF countBillLines > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot delete product: it is used in a bill.';
    END IF;

    -- Vérifie dans SaleLine
    SELECT COUNT(*) INTO countSaleLines
    FROM SaleLine
    WHERE Product_idProduct = OLD.idProduct;

    IF countSaleLines > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot delete product: it is used in a sale.';
    END IF;

    -- Vérifie dans PromotionProduct
    SELECT COUNT(*) INTO countPromotions
    FROM PromotionProduct
    WHERE Product_idProduct = OLD.idProduct;

    IF countPromotions > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot delete product: it is linked to a promotion.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `productrating`
--

DROP TABLE IF EXISTS `productrating`;
CREATE TABLE IF NOT EXISTS `productrating` (
  `Customers_idCustomers` int NOT NULL,
  `RatingID` int NOT NULL,
  `RatingValue` int DEFAULT NULL,
  `RatingDate` date DEFAULT NULL,
  `Comment` varchar(100) DEFAULT NULL,
  `Product_idProduct` int NOT NULL,
  PRIMARY KEY (`Customers_idCustomers`,`RatingID`,`Product_idProduct`),
  KEY `fk_Customers_has_Product_Customers1_idx` (`Customers_idCustomers`),
  KEY `fk_ProductRating_Product1_idx` (`Product_idProduct`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `productrating`
--

INSERT INTO `productrating` (`Customers_idCustomers`, `RatingID`, `RatingValue`, `RatingDate`, `Comment`, `Product_idProduct`) VALUES
(1, 1, 5, '2025-04-10', 'Excellent produit, je recommande.', 1),
(1, 2, 4, '2025-04-11', 'Bon rapport qualité-prix.', 3),
(1, 3, 4, '2025-05-11', 'Je rachèterai.', 31),
(2, 1, 3, '2025-04-15', 'Correct mais pourrait être meilleur.', 2),
(2, 2, 5, '2025-04-20', 'Délicieux, achat régulier.', 5),
(2, 3, 5, '2025-05-12', 'Produit vraiment excellent.', 32),
(3, 1, 1, '2025-04-21', 'Mauvais goût, très déçu.', 6),
(3, 2, 4, '2025-04-23', 'Très bon pour le petit déjeuner.', 4),
(3, 3, 3, '2025-05-12', 'Trop petit format.', 33),
(4, 1, 5, '2025-04-24', 'Indispensable dans ma cuisine.', 10),
(4, 2, 4, '2025-05-13', 'Très utile.', 34),
(5, 1, 2, '2025-04-25', 'Moyen, pas conforme à mes attentes.', 9),
(5, 2, 2, '2025-05-13', 'Trop cher.', 35),
(6, 1, 4, '2025-04-25', 'Bon mais un peu cher.', 7),
(6, 2, 5, '2025-04-26', 'Très pratique au quotidien.', 8),
(6, 3, 5, '2025-05-14', 'Nickel !', 36),
(7, 1, 5, '2025-04-26', 'Produit fidèle à la description.', 12),
(7, 2, 3, '2025-05-14', 'Bof.', 37),
(8, 1, 3, '2025-04-27', 'Fait le job mais pas incroyable.', 11),
(8, 2, 5, '2025-05-15', 'Très bon.', 38),
(9, 1, 2, '2025-04-28', 'Pas satisfait de la qualité.', 13),
(9, 2, 1, '2025-05-15', 'Je déconseille.', 39),
(10, 1, 4, '2025-04-29', 'Odeur agréable, bon produit.', 14),
(10, 2, 5, '2025-05-16', 'Mes enfants adorent.', 40),
(11, 1, 4, '2025-04-30', 'Très efficace.', 15),
(11, 2, 4, '2025-05-16', 'Très bon goût.', 41),
(12, 1, 5, '2025-05-01', 'Produit top, je rachèterai.', 16),
(12, 2, 3, '2025-05-17', 'Pas mal.', 42),
(13, 1, 3, '2025-05-01', 'Bien mais peut mieux faire.', 17),
(13, 2, 2, '2025-05-17', 'Peu efficace.', 43),
(14, 1, 4, '2025-05-02', 'Utilisé régulièrement.', 18),
(14, 2, 5, '2025-05-18', 'Très bon produit.', 44),
(15, 1, 1, '2025-05-02', 'Très mauvaise expérience.', 19),
(15, 2, 4, '2025-05-18', 'Je recommande.', 45),
(16, 1, 5, '2025-05-03', 'Toujours satisfait.', 20),
(16, 2, 2, '2025-05-19', 'Peut mieux faire.', 46),
(17, 1, 3, '2025-05-03', 'Pas mal mais emballage fragile.', 21),
(17, 2, 5, '2025-05-19', 'Top qualité.', 47),
(18, 1, 4, '2025-05-04', 'Super goût.', 22),
(18, 2, 5, '2025-05-05', 'Très bon produit.', 24),
(18, 3, 3, '2025-05-20', 'Ne vaut pas le prix.', 48),
(19, 1, 2, '2025-05-05', 'Pas conforme à mes attentes.', 23),
(19, 2, 5, '2025-05-20', 'Très satisfait.', 49),
(20, 1, 4, '2025-05-06', 'Bon conditionnement.', 25),
(20, 2, 1, '2025-05-20', 'Expérience désastreuse.', 50),
(21, 1, 5, '2025-05-07', 'Produit frais et savoureux.', 26),
(22, 1, 3, '2025-05-08', 'Un peu trop sucré à mon goût.', 27),
(23, 1, 4, '2025-05-08', 'Crème délicieuse.', 28),
(24, 1, 5, '2025-05-09', 'Bon rapport qualité/prix.', 29),
(25, 1, 2, '2025-05-10', 'Pas satisfait.', 30);

-- --------------------------------------------------------

--
-- Structure de la table `promotion`
--

DROP TABLE IF EXISTS `promotion`;
CREATE TABLE IF NOT EXISTS `promotion` (
  `idPromotion` int NOT NULL AUTO_INCREMENT,
  `DiscountRate` int DEFAULT NULL,
  PRIMARY KEY (`idPromotion`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `promotion`
--

INSERT INTO `promotion` (`idPromotion`, `DiscountRate`) VALUES
(1, 10),
(2, 15),
(3, 5),
(4, 20),
(5, 25),
(6, 30),
(7, 35),
(8, 40),
(9, 50),
(10, 12),
(11, 8),
(12, 18),
(13, 22),
(14, 28),
(15, 33),
(16, 38),
(17, 45),
(18, 5),
(19, 13),
(20, 7),
(21, 17),
(22, 21),
(23, 26),
(24, 31),
(25, 36),
(26, 42),
(27, 47),
(28, 6),
(29, 11),
(30, 16),
(31, 23),
(32, 27),
(33, 32),
(34, 37),
(35, 43),
(36, 48),
(37, 9),
(38, 14),
(39, 19),
(40, 24),
(41, 29),
(42, 34),
(43, 39),
(44, 44),
(45, 49),
(46, 50),
(47, 3),
(48, 2),
(49, 1),
(50, 4);

-- --------------------------------------------------------

--
-- Structure de la table `promotionproduct`
--

DROP TABLE IF EXISTS `promotionproduct`;
CREATE TABLE IF NOT EXISTS `promotionproduct` (
  `Promotion_idPromotion` int NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `Product_idProduct` int NOT NULL,
  PRIMARY KEY (`Promotion_idPromotion`),
  KEY `fk_Promotion_has_Product_Promotion_idx` (`Promotion_idPromotion`),
  KEY `fk_PromotionProduct_Product1_idx` (`Product_idProduct`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `promotionproduct`
--

INSERT INTO `promotionproduct` (`Promotion_idPromotion`, `StartDate`, `EndDate`, `Product_idProduct`) VALUES
(1, '2025-03-01', '2025-03-15', 1),
(2, '2025-03-10', '2025-03-25', 2),
(3, '2025-03-20', '2025-04-05', 3),
(4, '2025-03-25', '2025-04-10', 4),
(5, '2025-04-01', '2025-04-16', 5),
(6, '2025-04-05', '2025-04-20', 6),
(7, '2025-04-10', '2025-04-30', 7),
(8, '2025-04-15', '2025-05-01', 8),
(9, '2025-04-20', '2025-05-05', 9),
(10, '2025-04-25', '2025-05-10', 10),
(11, '2025-05-01', '2025-05-15', 11),
(12, '2025-05-05', '2025-05-20', 12),
(13, '2025-05-10', '2025-05-25', 13),
(14, '2025-05-15', '2025-05-30', 14),
(15, '2025-05-20', '2025-06-04', 15),
(16, '2025-05-25', '2025-06-10', 16),
(17, '2025-06-01', '2025-06-16', 17),
(18, '2025-06-05', '2025-06-20', 18),
(19, '2025-06-10', '2025-06-25', 19),
(20, '2025-06-15', '2025-06-30', 20),
(21, '2025-06-20', '2025-07-05', 21),
(22, '2025-06-25', '2025-07-10', 22),
(23, '2025-07-01', '2025-07-16', 23),
(24, '2025-07-05', '2025-07-20', 24),
(25, '2025-07-10', '2025-07-25', 25),
(26, '2025-03-01', '2025-03-20', 26),
(27, '2025-03-10', '2025-03-30', 27),
(28, '2025-04-01', '2025-04-21', 28),
(29, '2025-04-11', '2025-04-30', 29),
(30, '2025-05-01', '2025-05-21', 30),
(31, '2025-05-11', '2025-06-01', 31),
(32, '2025-06-01', '2025-06-21', 32),
(33, '2025-06-11', '2025-07-01', 33),
(34, '2025-07-01', '2025-07-21', 34),
(35, '2025-07-11', '2025-07-31', 35),
(36, '2025-03-15', '2025-03-30', 36),
(37, '2025-04-01', '2025-04-15', 37),
(38, '2025-04-16', '2025-05-01', 38),
(39, '2025-05-02', '2025-05-17', 39),
(40, '2025-05-18', '2025-06-02', 40),
(41, '2025-06-03', '2025-06-18', 41),
(42, '2025-06-19', '2025-07-04', 42),
(43, '2025-07-05', '2025-07-20', 43),
(44, '2025-07-06', '2025-07-21', 44),
(45, '2025-07-07', '2025-07-22', 45),
(46, '2025-07-08', '2025-07-23', 46),
(47, '2025-07-09', '2025-07-24', 47),
(48, '2025-07-10', '2025-07-25', 48),
(49, '2025-07-11', '2025-07-26', 49),
(50, '2025-07-12', '2025-07-27', 50);

-- --------------------------------------------------------

--
-- Structure de la table `purchaseordersupplier`
--

DROP TABLE IF EXISTS `purchaseordersupplier`;
CREATE TABLE IF NOT EXISTS `purchaseordersupplier` (
  `idPurchaseOrderSupplier` int NOT NULL AUTO_INCREMENT,
  `QuantityOrdered` int DEFAULT NULL,
  `UnitPrice` decimal(10,2) DEFAULT NULL,
  `OrderDate` date DEFAULT NULL,
  `Supplier_idSupplier` int NOT NULL,
  `Product_idProduct` int NOT NULL,
  PRIMARY KEY (`idPurchaseOrderSupplier`),
  KEY `fk_PurchaseOrderSupplier_Supplier1_idx` (`Supplier_idSupplier`),
  KEY `fk_PurchaseOrderSupplier_Product1_idx` (`Product_idProduct`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `purchaseordersupplier`
--

INSERT INTO `purchaseordersupplier` (`idPurchaseOrderSupplier`, `QuantityOrdered`, `UnitPrice`, `OrderDate`, `Supplier_idSupplier`, `Product_idProduct`) VALUES
(51, 100, 1.50, '2025-01-05', 1, 1),
(52, 75, 2.20, '2025-01-07', 2, 2),
(53, 200, 0.90, '2025-01-10', 3, 3),
(54, 150, 1.10, '2025-01-12', 4, 4),
(55, 180, 0.80, '2025-01-14', 5, 5),
(56, 120, 1.30, '2025-01-16', 6, 6),
(57, 95, 3.00, '2025-01-18', 7, 7),
(58, 60, 2.40, '2025-01-20', 8, 8),
(59, 50, 4.10, '2025-01-22', 9, 9),
(60, 110, 5.00, '2025-01-24', 10, 10),
(61, 100, 0.75, '2025-02-01', 1, 11),
(62, 140, 1.20, '2025-02-03', 2, 12),
(63, 160, 1.60, '2025-02-05', 3, 13),
(64, 130, 2.10, '2025-02-07', 4, 14),
(65, 150, 2.25, '2025-02-09', 5, 15),
(66, 170, 1.90, '2025-02-11', 6, 16),
(67, 200, 0.95, '2025-02-13', 7, 17),
(68, 120, 1.75, '2025-02-15', 8, 18),
(69, 135, 2.60, '2025-02-17', 9, 19),
(70, 90, 2.80, '2025-02-19', 10, 20),
(71, 125, 3.50, '2025-03-01', 1, 21),
(72, 100, 1.85, '2025-03-03', 2, 22),
(73, 180, 1.95, '2025-03-05', 3, 23),
(74, 160, 0.85, '2025-03-07', 4, 24),
(75, 145, 2.20, '2025-03-09', 5, 25),
(76, 105, 2.00, '2025-03-11', 6, 26),
(77, 95, 1.15, '2025-03-13', 7, 27),
(78, 200, 0.99, '2025-03-15', 8, 28),
(79, 85, 3.10, '2025-03-17', 9, 29),
(80, 90, 2.90, '2025-03-19', 10, 30),
(81, 170, 4.25, '2025-04-01', 1, 31),
(82, 190, 1.50, '2025-04-03', 2, 32),
(83, 145, 1.00, '2025-04-05', 3, 33),
(84, 165, 2.80, '2025-04-07', 4, 34),
(85, 100, 2.30, '2025-04-09', 5, 35),
(86, 75, 1.45, '2025-04-11', 6, 36),
(87, 135, 2.65, '2025-04-13', 7, 37),
(88, 155, 1.20, '2025-04-15', 8, 38),
(89, 115, 2.55, '2025-04-17', 9, 39),
(90, 130, 3.75, '2025-04-19', 10, 40),
(91, 145, 1.60, '2025-05-01', 1, 41),
(92, 165, 1.30, '2025-05-03', 2, 42),
(93, 175, 0.85, '2025-05-05', 3, 43),
(94, 90, 2.10, '2025-05-07', 4, 44),
(95, 95, 3.60, '2025-05-09', 5, 45),
(96, 110, 2.45, '2025-05-11', 6, 46),
(97, 125, 1.75, '2025-05-13', 7, 47),
(98, 150, 2.35, '2025-05-15', 8, 48),
(99, 180, 1.10, '2025-05-17', 9, 49),
(100, 100, 1.95, '2025-05-19', 10, 50);

-- --------------------------------------------------------

--
-- Structure de la table `saleline`
--

DROP TABLE IF EXISTS `saleline`;
CREATE TABLE IF NOT EXISTS `saleline` (
  `Sales_idSales` int NOT NULL,
  `Quantity` int DEFAULT NULL,
  `UnitPrice` decimal(10,2) DEFAULT NULL,
  `Subtotal` decimal(10,2) DEFAULT NULL,
  `Product_idProduct` int NOT NULL,
  PRIMARY KEY (`Sales_idSales`,`Product_idProduct`),
  KEY `fk_Product_has_Sales_Sales1_idx` (`Sales_idSales`),
  KEY `fk_SaleLine_Product1_idx` (`Product_idProduct`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `saleline`
--

INSERT INTO `saleline` (`Sales_idSales`, `Quantity`, `UnitPrice`, `Subtotal`, `Product_idProduct`) VALUES
(1, 5, 3.50, 17.50, 1),
(1, 3, 2.99, 8.97, 2),
(2, 10, 1.20, 12.00, 2),
(3, 8, 2.75, 22.00, 3),
(4, 3, 6.90, 20.70, 4),
(5, 6, 2.50, 15.00, 5),
(6, 2, 10.00, 20.00, 6),
(7, 4, 4.30, 17.20, 7),
(8, 12, 1.10, 13.20, 8),
(9, 7, 2.80, 19.60, 9),
(10, 9, 2.40, 21.60, 10),
(11, 10, 0.99, 9.90, 11),
(12, 6, 3.50, 21.00, 12),
(13, 4, 6.25, 25.00, 13),
(14, 2, 9.99, 19.98, 14),
(15, 8, 1.70, 13.60, 15),
(16, 5, 2.60, 13.00, 16),
(17, 3, 5.10, 15.30, 17),
(18, 10, 1.30, 13.00, 18),
(19, 6, 4.40, 26.40, 19),
(20, 9, 2.20, 19.80, 20),
(21, 7, 3.90, 27.30, 21),
(22, 4, 7.50, 30.00, 22),
(23, 6, 2.25, 13.50, 23),
(24, 2, 8.80, 17.60, 24),
(25, 5, 3.00, 15.00, 25),
(26, 10, 1.90, 19.00, 26),
(27, 8, 1.75, 14.00, 27),
(28, 4, 6.60, 26.40, 28),
(29, 3, 5.40, 16.20, 29),
(30, 7, 3.30, 23.10, 30),
(31, 6, 2.80, 16.80, 31),
(32, 9, 1.60, 14.40, 32),
(33, 5, 4.00, 20.00, 33),
(34, 8, 1.45, 11.60, 34),
(35, 10, 0.85, 8.50, 35),
(36, 6, 3.75, 22.50, 36),
(37, 2, 9.25, 18.50, 37),
(38, 7, 2.10, 14.70, 38),
(39, 5, 3.60, 18.00, 39),
(40, 4, 7.25, 29.00, 40),
(41, 3, 6.00, 18.00, 41),
(42, 5, 2.95, 14.75, 42),
(43, 7, 1.80, 12.60, 43),
(44, 6, 3.40, 20.40, 44),
(45, 8, 2.00, 16.00, 45),
(46, 9, 1.50, 13.50, 46),
(47, 10, 0.75, 7.50, 47),
(48, 5, 2.85, 14.25, 48),
(49, 4, 5.20, 20.80, 49),
(50, 2, 10.50, 21.00, 50);

--
-- Déclencheurs `saleline`
--
DROP TRIGGER IF EXISTS `applyPromotionAutomatically`;
DELIMITER $$
CREATE TRIGGER `applyPromotionAutomatically` BEFORE INSERT ON `saleline` FOR EACH ROW BEGIN
    DECLARE productPrice DECIMAL(10,2);
    DECLARE discountRate DECIMAL(5,2) DEFAULT 0;

    SELECT Price INTO productPrice
    FROM Product
    WHERE idProduct = NEW.Product_idProduct;

    SELECT p.DiscountRate INTO discountRate
    FROM Promotion p
    JOIN PromotionProduct pp ON pp.Promotion_idPromotion = p.idPromotion
    WHERE pp.Product_idProduct = NEW.Product_idProduct
      AND CURRENT_DATE BETWEEN pp.StartDate AND pp.EndDate
    LIMIT 1;

    SET NEW.UnitPrice = productPrice * (1 - discountRate);
    SET NEW.Subtotal = NEW.UnitPrice * NEW.Quantity;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `updateStockAfterSale`;
DELIMITER $$
CREATE TRIGGER `updateStockAfterSale` AFTER INSERT ON `saleline` FOR EACH ROW BEGIN
    UPDATE Stock
    SET QuantityAvailable = QuantityAvailable - NEW.Quantity
    WHERE Product_idProduct = NEW.Product_idProduct;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `sales`
--

DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
  `idSales` int NOT NULL AUTO_INCREMENT,
  `Date` date NOT NULL,
  `QuantitySold` int DEFAULT NULL,
  `TotalAmount` int DEFAULT NULL,
  PRIMARY KEY (`idSales`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `sales`
--

INSERT INTO `sales` (`idSales`, `Date`, `QuantitySold`, `TotalAmount`) VALUES
(1, '2025-01-02', 120, 341),
(2, '2025-01-03', 98, 275),
(3, '2025-01-04', 145, 400),
(4, '2025-01-05', 210, 621),
(5, '2025-01-06', 65, 185),
(6, '2025-01-07', 78, 210),
(7, '2025-01-08', 95, 230),
(8, '2025-01-09', 180, 501),
(9, '2025-01-10', 220, 750),
(10, '2025-01-11', 250, 810),
(11, '2025-01-12', 190, 560),
(12, '2025-01-13', 170, 431),
(13, '2025-01-14', 160, 490),
(14, '2025-01-15', 75, 200),
(15, '2025-01-16', 60, 145),
(16, '2025-01-17', 85, 206),
(17, '2025-01-18', 100, 300),
(18, '2025-01-19', 220, 600),
(19, '2025-01-20', 310, 870),
(20, '2025-01-21', 270, 725),
(21, '2025-02-01', 150, 420),
(22, '2025-02-02', 140, 411),
(23, '2025-02-03', 130, 385),
(24, '2025-02-04', 100, 291),
(25, '2025-02-05', 90, 270),
(26, '2025-02-06', 200, 650),
(27, '2025-02-07', 240, 761),
(28, '2025-02-08', 300, 900),
(29, '2025-02-09', 310, 950),
(30, '2025-02-10', 320, 980),
(31, '2025-03-01', 110, 305),
(32, '2025-03-02', 120, 331),
(33, '2025-03-03', 135, 355),
(34, '2025-03-04', 140, 366),
(35, '2025-03-05', 100, 280),
(36, '2025-03-06', 90, 265),
(37, '2025-03-07', 80, 245),
(38, '2025-03-08', 95, 250),
(39, '2025-03-09', 105, 310),
(40, '2025-03-10', 115, 325),
(41, '2025-04-01', 210, 700),
(42, '2025-04-02', 220, 720),
(43, '2025-04-03', 190, 560),
(44, '2025-04-04', 180, 510),
(45, '2025-04-05', 170, 475),
(46, '2025-04-06', 160, 460),
(47, '2025-04-07', 150, 450),
(48, '2025-04-08', 140, 420),
(49, '2025-04-09', 130, 410),
(50, '2025-04-10', 120, 390);

-- --------------------------------------------------------

--
-- Structure de la table `stock`
--

DROP TABLE IF EXISTS `stock`;
CREATE TABLE IF NOT EXISTS `stock` (
  `idStock` int NOT NULL AUTO_INCREMENT,
  `QuantityAvailable` int DEFAULT NULL,
  `Location` varchar(45) DEFAULT NULL,
  `LastRestockDate` date DEFAULT NULL,
  `Product_idProduct` int NOT NULL,
  PRIMARY KEY (`idStock`),
  KEY `fk_Stock_Product1_idx` (`Product_idProduct`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `stock`
--

INSERT INTO `stock` (`idStock`, `QuantityAvailable`, `Location`, `LastRestockDate`, `Product_idProduct`) VALUES
(1, 250, 'Entrepôt A1', '2025-01-03', 1),
(2, 117, 'Rayon B2', '2025-01-06', 2),
(3, 300, 'Entrepôt C3', '2025-01-09', 3),
(4, 180, 'Rayon A5', '2025-01-12', 4),
(5, 90, 'Rayon D1', '2025-01-14', 5),
(6, 400, 'Entrepôt A2', '2025-01-17', 6),
(7, 70, 'Rayon C2', '2025-01-20', 7),
(8, 60, 'Rayon B1', '2025-01-22', 8),
(9, 200, 'Entrepôt B4', '2025-01-25', 9),
(10, 310, 'Rayon D4', '2025-01-28', 10),
(11, 230, 'Entrepôt A3', '2025-02-01', 11),
(12, 150, 'Rayon C1', '2025-02-04', 12),
(13, 320, 'Rayon A2', '2025-02-06', 13),
(14, 115, 'Entrepôt D2', '2025-02-08', 14),
(15, 400, 'Rayon C5', '2025-02-11', 15),
(16, 170, 'Rayon B3', '2025-02-14', 16),
(17, 260, 'Entrepôt A4', '2025-02-17', 17),
(18, 180, 'Rayon D3', '2025-02-19', 18),
(19, 300, 'Rayon C4', '2025-02-21', 19),
(20, 90, 'Rayon B5', '2025-02-23', 20),
(21, 280, 'Entrepôt A5', '2025-03-01', 21),
(22, 140, 'Rayon D2', '2025-03-03', 22),
(23, 250, 'Rayon C1', '2025-03-05', 23),
(24, 70, 'Entrepôt B1', '2025-03-08', 24),
(25, 100, 'Rayon A3', '2025-03-10', 25),
(26, 330, 'Entrepôt D5', '2025-03-12', 26),
(27, 110, 'Rayon B4', '2025-03-14', 27),
(28, 210, 'Rayon C2', '2025-03-17', 28),
(29, 185, 'Entrepôt A1', '2025-03-19', 29),
(30, 95, 'Rayon D1', '2025-03-21', 30),
(31, 305, 'Rayon A1', '2025-04-01', 31),
(32, 135, 'Entrepôt B3', '2025-04-03', 32),
(33, 240, 'Rayon C5', '2025-04-05', 33),
(34, 175, 'Rayon B2', '2025-04-07', 34),
(35, 210, 'Entrepôt D3', '2025-04-09', 35),
(36, 400, 'Rayon A4', '2025-04-11', 36),
(37, 150, 'Rayon C1', '2025-04-13', 37),
(38, 260, 'Entrepôt B2', '2025-04-15', 38),
(39, 220, 'Rayon D5', '2025-04-17', 39),
(40, 180, 'Rayon A2', '2025-04-19', 40),
(41, 190, 'Rayon C3', '2025-05-01', 41),
(42, 145, 'Entrepôt A2', '2025-05-03', 42),
(43, 170, 'Rayon B1', '2025-05-05', 43),
(44, 250, 'Rayon D4', '2025-05-07', 44),
(45, 230, 'Rayon A5', '2025-05-09', 45),
(46, 160, 'Rayon C2', '2025-05-11', 46),
(47, 290, 'Entrepôt B5', '2025-05-13', 47),
(48, 135, 'Rayon B3', '2025-05-15', 48),
(49, 210, 'Rayon D3', '2025-05-17', 49),
(50, 120, 'Entrepôt C4', '2025-05-19', 50);

-- --------------------------------------------------------

--
-- Structure de la table `supplier`
--

DROP TABLE IF EXISTS `supplier`;
CREATE TABLE IF NOT EXISTS `supplier` (
  `idSupplier` int NOT NULL AUTO_INCREMENT,
  `CompagnyName` varchar(45) NOT NULL,
  `ContactName` varchar(45) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Address` varchar(150) NOT NULL,
  PRIMARY KEY (`idSupplier`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `supplier`
--

INSERT INTO `supplier` (`idSupplier`, `CompagnyName`, `ContactName`, `PhoneNumber`, `Email`, `Address`) VALUES
(1, 'Frais Délices', 'Luc Martin', '0601020304', 'luc.martin@fraisdelices.fr', '23 rue des Maraîchers, 75012 Paris'),
(2, 'Boissons & Cie', 'Julie Bernard', '0602030405', 'j.bernard@boissonscie.fr', '12 avenue des Vignes, 33000 Bordeaux'),
(3, 'Maison Fromagère', 'Alain Dupuis', '0611223344', 'a.dupuis@fromages.fr', '48 rue du Lait, 69007 Lyon'),
(4, 'Fournil du Coin', 'Claire Petit', '0655443322', 'claire.petit@fournil.fr', '7 impasse du Levain, 13006 Marseille'),
(5, 'Bio Vrac', 'Romain Lefevre', '0688776655', 'r.lefevre@biovrac.fr', '9 avenue Verte, 31000 Toulouse'),
(6, 'Viande & Terroir', 'Élodie Masson', '0677889900', 'elodie.masson@viandeterroir.fr', '17 rue de la Boucherie, 21000 Dijon'),
(7, 'Savon et Santé', 'Benoît Giraud', '0612345678', 'benoit.giraud@savonet.fr', '21 chemin du Mistral, 84000 Avignon'),
(8, 'Biscuiterie Bonnefour', 'Manon Caron', '0623456789', 'manon.caron@bonnefour.fr', '10 rue des Délices, 67000 Strasbourg'),
(9, 'Primeurs Express', 'Thierry Blanchard', '0699887766', 'thierry.b@primeursx.fr', '3 rue du Marché, 59000 Lille'),
(10, 'Océan Frais', 'Caroline Robert', '0655123498', 'caro.robert@oceanfrais.fr', '2 allée Neptune, 44000 Nantes'),
(11, 'Surgelés Gourmands', 'Paul Lefrançois', '0632123456', 'paul.l@sg.fr', '18 rue des Glaces, 31000 Toulouse'),
(12, 'Nettoie Propre', 'Isabelle Dumas', '0621123344', 'i.dumas@netpro.fr', '44 rue du Savon, 75014 Paris'),
(13, 'Condiments Sud', 'Jean Roche', '0611002200', 'j.roche@condsud.fr', '6 avenue Paprika, 13008 Marseille'),
(14, 'Dépôt Hygiène', 'Anne Girard', '0600667788', 'a.girard@depot-hygiene.fr', '19 rue du Lavage, 72000 Le Mans'),
(15, 'Chocolaterie Marcel', 'Marc Faure', '0688991122', 'marc.faure@chocmarcel.fr', '55 rue du Cacao, 69006 Lyon'),
(16, 'Pasta Bonito', 'Gianni Rossi', '0677889901', 'gianni.rossi@pastabonito.it', 'Via Roma 42, 00100 Roma, Italie'),
(17, 'Saveurs du Nord', 'Lucas Moreau', '0633557799', 'lucas.moreau@saveursnord.fr', '27 rue des Épices, 59000 Lille'),
(18, 'Boissons Breizh', 'Anna Le Goff', '0611445566', 'a.legoff@breizhdrink.fr', '8 route du cidre, 35000 Rennes'),
(19, 'Éco Papier', 'Pierre Noël', '0655889977', 'p.noel@ecopapier.fr', '11 rue du Recyclage, 75010 Paris'),
(20, 'Maison du Café', 'Chantal Leroy', '0677334455', 'chantal.leroy@cafemaison.fr', '5 allée Arabica, 64000 Pau'),
(21, 'FruitExotica', 'Nina Delcroix', '0611223345', 'n.delcroix@fruitexotica.com', '45 boulevard des Tropiques, 97400 Saint-Denis'),
(22, 'Tissus Express', 'Olivier Lambert', '0622446688', 'olivier.l@tex.fr', '29 rue des Drapiers, 25000 Besançon'),
(23, 'Épices & Co', 'Sabine André', '0600778899', 'sabine.andre@epicesco.fr', '31 place du Safran, 34000 Montpellier'),
(24, 'Boulangerie Centrale', 'Antoine Maillot', '0688990011', 'antoine.maillot@bcentrale.fr', '99 rue de la Brioche, 57000 Metz'),
(25, 'DistriViandes', 'Catherine Blanc', '0699775544', 'c.blanc@distriviandes.fr', '2 avenue du Bœuf, 35000 Rennes'),
(26, 'Huiles du Soleil', 'Julien Navarro', '0677112233', 'julien.n@huile.fr', '20 avenue de l’Huile, 13000 Marseille'),
(27, 'Légumes Bio France', 'Sarah Denis', '0644332211', 'sarah.denis@legbio.fr', '13 impasse des Jardins, 73000 Chambéry'),
(28, 'Fromagerie Authentique', 'Hugo Michon', '0633445566', 'hugo.michon@authentique.fr', '28 rue du Roquefort, 81000 Albi'),
(29, 'Brasserie Artisanale', 'Lucie Perret', '0622668899', 'lucie.p@brasserart.fr', '17 rue de la Houblonnière, 62000 Arras'),
(30, 'ProviNord', 'Damien Bourgeois', '0600223344', 'd.bourgeois@provinord.fr', '22 boulevard des Alpes, 74000 Annecy'),
(31, 'Matériaux Pro', 'Alexandre Garnier', '0699445566', 'alex.garnier@matpro.fr', '66 route de l’Usine, 10000 Troyes'),
(32, 'Sucres & Sucreries', 'Nathalie Meunier', '0611557799', 'n.meunier@sucresucrerie.fr', '4 rue du Sucre, 60000 Beauvais'),
(33, 'Fraîcheur Laitière', 'Vincent Bouchard', '0622778899', 'vincent.bouchard@laitfraiche.fr', '3 place du Fromage, 71000 Mâcon'),
(34, 'Riz & Céréales', 'Claire Millet', '0677885566', 'claire.millet@ricereal.fr', '8 rue des Moissons, 45000 Orléans'),
(35, 'Maison Biscotte', 'Sophie Roche', '0655332211', 'sophie.roche@mbiscotte.fr', '19 avenue Croquante, 54000 Nancy'),
(36, 'Papeterie Max', 'Maxime Collet', '0644556677', 'max.collet@papmax.fr', '21 boulevard du Bureau, 76000 Rouen'),
(37, 'Doux Nettoyant', 'Émilie Thomas', '0611889900', 'emilie.thomas@douxnet.fr', '12 rue Propreté, 92000 Nanterre'),
(38, 'Viandes Hallal Express', 'Youssef Benali', '0633558822', 'y.benali@vhallal.fr', '77 rue de la Mosquée, 91000 Évry'),
(39, 'Soins & Bien-être', 'Laure Vasseur', '0666554422', 'laure.vasseur@soinplus.fr', '59 chemin du Zen, 38000 Grenoble'),
(40, 'Produits de la Mer', 'Jean-Paul Darcet', '0699223344', 'jp.darcet@merplus.fr', '6 quai Atlantique, 56100 Lorient');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `basket`
--
ALTER TABLE `basket`
  ADD CONSTRAINT `fk_Basket_Customers1` FOREIGN KEY (`Customers_idCustomers`) REFERENCES `customers` (`idCustomers`);

--
-- Contraintes pour la table `basketline`
--
ALTER TABLE `basketline`
  ADD CONSTRAINT `fk_BasketLine_Basket1` FOREIGN KEY (`Basket_idBasket`) REFERENCES `basket` (`idBasket`),
  ADD CONSTRAINT `fk_BasketLine_Product1` FOREIGN KEY (`Product_idProduct`) REFERENCES `product` (`idProduct`);

--
-- Contraintes pour la table `bill`
--
ALTER TABLE `bill`
  ADD CONSTRAINT `fk_Bill_Customers1` FOREIGN KEY (`Customers_idCustomers`) REFERENCES `customers` (`idCustomers`);

--
-- Contraintes pour la table `billline`
--
ALTER TABLE `billline`
  ADD CONSTRAINT `fk_BillLine_Bill1` FOREIGN KEY (`Bill_idBill`) REFERENCES `bill` (`idBill`),
  ADD CONSTRAINT `fk_BillLine_Product1` FOREIGN KEY (`Product_idProduct`) REFERENCES `product` (`idProduct`);

--
-- Contraintes pour la table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `fk_Employee_Departement1` FOREIGN KEY (`Departement_idDepartement`) REFERENCES `departement` (`idDepartement`);

--
-- Contraintes pour la table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_Product_Departement1` FOREIGN KEY (`Departement_idDepartement`) REFERENCES `departement` (`idDepartement`);

--
-- Contraintes pour la table `productrating`
--
ALTER TABLE `productrating`
  ADD CONSTRAINT `fk_Customers_has_Product_Customers1` FOREIGN KEY (`Customers_idCustomers`) REFERENCES `customers` (`idCustomers`),
  ADD CONSTRAINT `fk_ProductRating_Product1` FOREIGN KEY (`Product_idProduct`) REFERENCES `product` (`idProduct`);

--
-- Contraintes pour la table `promotionproduct`
--
ALTER TABLE `promotionproduct`
  ADD CONSTRAINT `fk_Promotion_has_Product_Promotion` FOREIGN KEY (`Promotion_idPromotion`) REFERENCES `promotion` (`idPromotion`),
  ADD CONSTRAINT `fk_PromotionProduct_Product1` FOREIGN KEY (`Product_idProduct`) REFERENCES `product` (`idProduct`);

--
-- Contraintes pour la table `purchaseordersupplier`
--
ALTER TABLE `purchaseordersupplier`
  ADD CONSTRAINT `fk_PurchaseOrderSupplier_Product1` FOREIGN KEY (`Product_idProduct`) REFERENCES `product` (`idProduct`),
  ADD CONSTRAINT `fk_PurchaseOrderSupplier_Supplier1` FOREIGN KEY (`Supplier_idSupplier`) REFERENCES `supplier` (`idSupplier`);

--
-- Contraintes pour la table `saleline`
--
ALTER TABLE `saleline`
  ADD CONSTRAINT `fk_Product_has_Sales_Sales1` FOREIGN KEY (`Sales_idSales`) REFERENCES `sales` (`idSales`),
  ADD CONSTRAINT `fk_SaleLine_Product1` FOREIGN KEY (`Product_idProduct`) REFERENCES `product` (`idProduct`);

--
-- Contraintes pour la table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `fk_Stock_Product1` FOREIGN KEY (`Product_idProduct`) REFERENCES `product` (`idProduct`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
