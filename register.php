<?php
require_once 'config/database.php';

$services = mysqli_query($conn, "SELECT MIN(id_service) AS id_service, nom_service FROM service GROUP BY nom_service ORDER BY nom_service ASC");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — DentalLink</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>

<div class="page-wrapper">

    <header class="page-header">
        <div class="brand">DentalLink</div>
        <p class="header-sub">Plateforme de gestion des laboratoires dentaires au Maroc</p>
    </header>

    <main class="card">

        <div class="card-header">
            <h1 class="card-title">Créer un compte laboratoire</h1>
            <p class="card-subtitle">Remplissez les informations ci-dessous pour rejoindre la plateforme.</p>
        </div>

        <form action="register_process.php" method="POST" enctype="multipart/form-data" id="register-form" novalidate>

            <!-- Section 1: Lab Identity -->
            <section class="form-section">
                <h2 class="section-title">Identité du laboratoire</h2>
                <div class="form-grid">

                    <div class="field-group">
                        <label for="nom_laboratoire">Nom du laboratoire <span class="required">*</span></label>
                        <input type="text" id="nom_laboratoire" name="nom_laboratoire" placeholder="ex. Labo Dentaire Atlas" required>
                    </div>

                    <div class="field-group">
                        <label for="nom_gerant">Nom du gérant <span class="required">*</span></label>
                        <input type="text" id="nom_gerant" name="nom_gerant" placeholder="ex. Dr. Youssef Benali" required>
                    </div>

                    <div class="field-group">
                        <label for="adresse">Adresse <span class="required">*</span></label>
                        <input type="text" id="adresse" name="adresse" placeholder="ex. 12 Rue Mohammed V" required>
                    </div>

                    <div class="field-group">
                        <label for="ville">Ville <span class="required">*</span></label>
                        <input type="text" id="ville" name="ville" placeholder="ex. Casablanca" required>
                    </div>

                </div>
            </section>

            <!-- Section 2: Contact -->
            <section class="form-section">
                <h2 class="section-title">Coordonnées de contact</h2>
                <div class="form-grid">

                    <div class="field-group">
                        <label for="telephone">Téléphone <span class="required">*</span></label>
                        <input type="tel" id="telephone" name="telephone" placeholder="ex. 0661234567" required>
                    </div>

                    <div class="field-group">
                        <label for="email">Adresse e-mail <span class="required">*</span></label>
                        <input type="email" id="email" name="email" placeholder="ex. contact@labo.ma" required>
                    </div>

                    <div class="field-group">
                        <label for="site_web">Site web <span class="optional">(optionnel)</span></label>
                        <input type="url" id="site_web" name="site_web" placeholder="https://www.labo.ma">
                    </div>

                    <div class="field-group">
                        <label for="instagram">Instagram <span class="optional">(optionnel)</span></label>
                        <input type="url" id="instagram" name="instagram" placeholder="https://instagram.com/labo">
                    </div>

                </div>
            </section>

            <!-- Section 3: Security -->
            <section class="form-section">
                <h2 class="section-title">Sécurité du compte</h2>
                <div class="form-grid">

                    <div class="field-group">
                        <label for="mot_de_passe">Mot de passe <span class="required">*</span></label>
                        <div class="input-with-action">
                            <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Minimum 8 caractères" required>
                            <button type="button" class="toggle-pw" data-target="mot_de_passe" aria-label="Afficher le mot de passe">Afficher</button>
                        </div>
                        <div class="strength-bar" id="strength-bar">
                            <div class="strength-fill" id="strength-fill"></div>
                        </div>
                        <span class="strength-label" id="strength-label"></span>
                    </div>

                    <div class="field-group">
                        <label for="confirmer_mot_de_passe">Confirmer le mot de passe <span class="required">*</span></label>
                        <div class="input-with-action">
                            <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" placeholder="Répétez le mot de passe" required>
                            <button type="button" class="toggle-pw" data-target="confirmer_mot_de_passe" aria-label="Afficher">Afficher</button>
                        </div>
                        <span class="match-label" id="match-label"></span>
                    </div>

                </div>
            </section>

            <!-- Section 4: Description -->
            <section class="form-section">
                <h2 class="section-title">Présentation</h2>
                <div class="form-grid full">

                    <div class="field-group full-width">
                        <label for="description">Description du laboratoire</label>
                        <textarea id="description" name="description" rows="4" placeholder="Décrivez votre laboratoire, votre expérience, vos équipements..."></textarea>
                    </div>

                </div>
            </section>

            <!-- Section 5: Services -->
            <section class="form-section">
                <h2 class="section-title">Services proposés <span class="required">*</span></h2>
                <div class="form-grid full">

                    <div class="field-group full-width">
                        <label for="services-trigger" class="sr-only">Services</label>

                        <!-- Custom Multi-Select Dropdown Container -->
                        <div class="custom-select-container" id="services-container">
                            <div class="custom-select-trigger" id="services-trigger" role="button" tabindex="0" aria-haspopup="listbox" aria-expanded="false">
                                <div class="trigger-inner">
                                    <span class="placeholder" id="services-placeholder">Sélectionnez un ou plusieurs services...</span>
                                    <div class="tags-container" id="selected-tags"></div>
                                </div>
                                <span class="arrow" aria-hidden="true"></span>
                            </div>
                            <div class="custom-select-dropdown" id="services-dropdown" role="listbox" aria-multiselectable="true">
                                <?php
                                mysqli_data_seek($services, 0);
                                while ($service = mysqli_fetch_assoc($services)) {
                                ?>
                                    <div class="dropdown-item" data-value="<?= $service['id_service']; ?>" role="option" aria-selected="false" tabindex="-1">
                                        <span class="checkbox-box" aria-hidden="true"></span>
                                        <span class="item-text"><?= htmlspecialchars($service['nom_service']); ?></span>
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- Hidden Native Select for standard form submission -->
                            <select id="services" name="services[]" multiple required style="display:none;" aria-hidden="true">
                                <?php
                                mysqli_data_seek($services, 0);
                                while ($service = mysqli_fetch_assoc($services)) {
                                ?>
                                    <option value="<?= $service['id_service']; ?>">
                                        <?= htmlspecialchars($service['nom_service']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                </div>
            </section>

            <!-- Section 6: Images -->
            <section class="form-section">
                <h2 class="section-title">Photos du laboratoire <span class="required">*</span></h2>
                <div class="form-grid full">

                    <div class="field-group full-width">
                        <div class="upload-area" id="upload-area">
                            <input type="file" id="images" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple required>
                            <div class="upload-content">
                                <div class="upload-title">Déposez vos photos ici</div>
                                <div class="upload-hint">ou cliquez pour parcourir — JPG, PNG, WEBP — Maximum 12 photos</div>
                            </div>
                        </div>
                        <div class="image-counter" id="image-counter" style="display:none;">
                            <span id="counter-text">0 / 12 photos sélectionnées</span>
                        </div>
                        <div class="preview-grid" id="preview-grid"></div>
                    </div>

                </div>
            </section>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="reset" class="btn btn-secondary">Réinitialiser</button>
                <button type="submit" class="btn btn-primary">Créer le compte</button>
            </div>

        </form>

    </main>

    <footer class="page-footer">
        <p>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
    </footer>

</div>

<script src="assets/js/register.js"></script>
</body>
</html>