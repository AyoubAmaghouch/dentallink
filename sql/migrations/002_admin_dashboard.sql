-- La structure initiale ne fournit aucune table de comptes administrateur.
-- Les comptes initiaux sont ajoutés avec admin/create-admin.php, qui utilise password_hash().
CREATE TABLE IF NOT EXISTS `admin` (
    `id_admin` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(100) NOT NULL DEFAULT 'Administrateur',
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `mot_de_passe` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les documents sont facultatifs lors d'une création manuelle par un administrateur.
ALTER TABLE `laboratoire` MODIFY `document_verification` VARCHAR(255) NULL;
