-- DentalLink migration: optional Google Maps URL for laboratories
-- Run once against the existing database. Existing rows remain unchanged.
ALTER TABLE laboratoire
    ADD COLUMN google_maps_url VARCHAR(2048) NULL DEFAULT NULL AFTER ville;
