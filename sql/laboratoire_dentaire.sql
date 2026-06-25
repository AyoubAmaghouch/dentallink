-- Structure de la base de données pour DentalLink Morocco
CREATE DATABASE IF NOT EXISTS `dentallink_morocco` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `dentallink_morocco`;

-- Table des services
CREATE TABLE IF NOT EXISTS `service` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des services par défaut
INSERT INTO `service` (`nom`) VALUES
('Couronne'),
('Bridge'),
('Facette'),
('Prothèse complète'),
('Prothèse partielle'),
('Stellite'),
('Prothèse sur implant'),
('Couronne sur implant'),
('Bridge sur implant'),
('Gouttière de contention'),
('Gouttière de blanchiment'),
('Appareils orthodontiques')
ON DUPLICATE KEY UPDATE `nom` = VALUES(`nom`);

-- Table des laboratoires
CREATE TABLE IF NOT EXISTS `laboratoire` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nom_laboratoire` VARCHAR(150) NOT NULL,
    `nom_gerant` VARCHAR(100) NOT NULL,
    `telephone` VARCHAR(30) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `mot_de_passe` VARCHAR(255) NOT NULL,
    `adresse` TEXT NOT NULL,
    `ville` VARCHAR(100) NOT NULL,
    `site_web` VARCHAR(255) DEFAULT NULL,
    `instagram` VARCHAR(255) DEFAULT NULL,
    `description` TEXT NOT NULL,
    `document_verification` VARCHAR(255) NOT NULL,
    `statut` ENUM('en_attente', 'valide', 'rejete') NOT NULL DEFAULT 'en_attente',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table de liaison laboratoire_service
CREATE TABLE IF NOT EXISTS `laboratoire_service` (
    `laboratoire_id` INT NOT NULL,
    `service_id` INT NOT NULL,
    PRIMARY KEY (`laboratoire_id`, `service_id`),
    FOREIGN KEY (`laboratoire_id`) REFERENCES `laboratoire` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des images de laboratoires
CREATE TABLE IF NOT EXISTS `image_laboratoire` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `laboratoire_id` INT NOT NULL,
    `chemin` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`laboratoire_id`) REFERENCES `laboratoire` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
