-- À exécuter sur la base laboratoire_dentaire existante.
-- La colonne est nullable pour conserver les laboratoires déjà enregistrés.
ALTER TABLE laboratoire ADD COLUMN document_verification VARCHAR(255) NULL AFTER description;
