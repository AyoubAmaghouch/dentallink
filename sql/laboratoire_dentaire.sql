-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 12 oct. 2026 à 01:49
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `laboratoire_dentaire`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`id_admin`, `nom`, `email`, `mot_de_passe`) VALUES
(1, 'Admin1', 'ayoubamaghouche@gmail.com', '$2y$12$jB7yKKpWEi5faPfW6SeVbOZZ5U6CtEFTSCFiwoF/QouPfmvQzYH4C');

-- --------------------------------------------------------

--
-- Structure de la table `image_laboratoire`
--

CREATE TABLE `image_laboratoire` (
  `id_image` int(11) NOT NULL,
  `laboratoire_id` int(11) NOT NULL,
  `url_image` varchar(255) NOT NULL,
  `est_principale` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `image_laboratoire`
--

INSERT INTO `image_laboratoire` (`id_image`, `laboratoire_id`, `url_image`, `est_principale`) VALUES
(5, 8, 'assets/uploads/laboratoires/d90c974feaf11caa3a9576591b0f3a3c.png', 0),
(6, 8, 'assets/uploads/laboratoires/7aa0974dc52381da5fbb63e2458dc40d.png', 0),
(7, 8, 'assets/uploads/laboratoires/6ba58b1ad228b60dfc3394b550e9b046.jpg', 0),
(8, 8, 'assets/uploads/laboratoires/6f038d190ad5559caea6882732c7cd73.jpg', 0),
(9, 8, 'assets/uploads/laboratoires/db50f916d0b8da7a580ed62834f33879.png', 0);

-- --------------------------------------------------------

--
-- Structure de la table `laboratoire`
--

CREATE TABLE `laboratoire` (
  `id_laboratoire` int(11) NOT NULL,
  `nom_laboratoire` varchar(150) NOT NULL,
  `nom_gerant` varchar(100) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `statut` enum('en_attente','accepte','refuse') DEFAULT 'en_attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `laboratoire`
--

INSERT INTO `laboratoire` (`id_laboratoire`, `nom_laboratoire`, `nom_gerant`, `telephone`, `email`, `mot_de_passe`, `adresse`, `ville`, `site_web`, `instagram`, `description`, `statut`) VALUES
(8, 'tanger', 'Tokyo', '0609244835', 'ayoubamaghouche@gmail.com', '$2y$12$p5hGL7/.0.DhcnDHWI20WO0rfWpyjkTawbSgl1/YuJtk9hNHJ7PbS', 'drisia 1444', 'Tanger', NULL, NULL, 'uyrytrytry', 'accepte');

-- --------------------------------------------------------

--
-- Structure de la table `laboratoire_service`
--

CREATE TABLE `laboratoire_service` (
  `laboratoire_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `laboratoire_service`
--

INSERT INTO `laboratoire_service` (`laboratoire_id`, `service_id`) VALUES
(8, 8),
(8, 21);

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

CREATE TABLE `service` (
  `id_service` int(11) NOT NULL,
  `nom_service` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `service`
--

INSERT INTO `service` (`id_service`, `nom_service`) VALUES
(1, 'Couronne'),
(2, 'Bridge'),
(4, 'Prothèse complète'),
(5, 'Prothèse partielle'),
(6, 'Stellite'),
(8, 'Couronne sur implant'),
(12, 'Appareils orthodontiques'),
(15, 'Facette'),
(19, 'Prothèse sur implant'),
(21, 'Bridge sur implant'),
(22, 'Gouttière de contention'),
(23, 'Gouttière de blanchiment');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `image_laboratoire`
--
ALTER TABLE `image_laboratoire`
  ADD PRIMARY KEY (`id_image`),
  ADD KEY `laboratoire_id` (`laboratoire_id`);

--
-- Index pour la table `laboratoire`
--
ALTER TABLE `laboratoire`
  ADD PRIMARY KEY (`id_laboratoire`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `laboratoire_service`
--
ALTER TABLE `laboratoire_service`
  ADD PRIMARY KEY (`laboratoire_id`,`service_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Index pour la table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id_service`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `image_laboratoire`
--
ALTER TABLE `image_laboratoire`
  MODIFY `id_image` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `laboratoire`
--
ALTER TABLE `laboratoire`
  MODIFY `id_laboratoire` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `service`
--
ALTER TABLE `service`
  MODIFY `id_service` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `image_laboratoire`
--
ALTER TABLE `image_laboratoire`
  ADD CONSTRAINT `image_laboratoire_ibfk_1` FOREIGN KEY (`laboratoire_id`) REFERENCES `laboratoire` (`id_laboratoire`) ON DELETE CASCADE;

--
-- Contraintes pour la table `laboratoire_service`
--
ALTER TABLE `laboratoire_service`
  ADD CONSTRAINT `laboratoire_service_ibfk_1` FOREIGN KEY (`laboratoire_id`) REFERENCES `laboratoire` (`id_laboratoire`) ON DELETE CASCADE,
  ADD CONSTRAINT `laboratoire_service_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `service` (`id_service`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
