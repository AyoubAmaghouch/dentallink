<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/schema.php';

$schema = dental_schema($pdo);
$serviceIdColumn = $schema->serviceId();
$serviceNameColumn = $schema->serviceName();
$services = $pdo->query("SELECT `$serviceIdColumn` id, `$serviceNameColumn` nom FROM service ORDER BY `$serviceNameColumn`")->fetchAll();
$hasDocument = $schema->hasDocument();
$error = (string)($_SESSION['register_error'] ?? '');
unset($_SESSION['register_error']);

$pageTitle = 'Inscription — DentalLink';
$pageStyles = ['assets/css/register.css'];
require __DIR__ . '/includes/header.php';
?>
<div class="page-wrapper register-page">
    <header class="page-header">
        <a class="register-brand" href="index.php" aria-label="DentalLink Morocco, accueil">DentalLink</a>
        <p class="header-sub">Plateforme des laboratoires dentaires au Maroc</p>
    </header>

    <main class="card register-card">
        <div class="card-header">
            <p class="register-eyebrow">Rejoignez le réseau DentalLink</p>
            <h1 class="card-title">Créer un compte laboratoire</h1>
            <p class="card-subtitle">Complétez les quatre étapes. Votre dossier sera vérifié par l’administration.</p>
        </div>

        <?php if ($error !== ''): ?>
            <p role="alert" class="form-error"><?= e($error) ?></p>
        <?php endif; ?>

        <form action="crud/create.php" method="post" enctype="multipart/form-data" id="register-form" data-registration-wizard novalidate>
            <?= csrf_field() ?>

            <div class="wizard-progress-wrap">
                <ol class="wizard-steps" aria-label="Étapes d’inscription">
                    <li class="wizard-step is-current" data-step-indicator aria-current="step"><span class="wizard-step-number">1/4</span><span class="wizard-step-label">Identité</span></li>
                    <li class="wizard-step" data-step-indicator><span class="wizard-step-number">2/4</span><span class="wizard-step-label">Présentation</span></li>
                    <li class="wizard-step" data-step-indicator><span class="wizard-step-number">3/4</span><span class="wizard-step-label">Services</span></li>
                    <li class="wizard-step" data-step-indicator><span class="wizard-step-number">4/4</span><span class="wizard-step-label">Documents</span></li>
                </ol>
                <div class="wizard-progress-track" role="progressbar" aria-label="Progression de l’inscription" aria-valuemin="1" aria-valuemax="4" aria-valuenow="1">
                    <span class="wizard-progress-fill"></span>
                </div>
                <p class="wizard-current-step" data-current-step aria-live="polite">Étape 1 sur 4 — Identité et contact</p>
            </div>

            <section class="wizard-panel is-entering" data-form-step="0" aria-labelledby="step-identity-title">
                <div class="wizard-panel-heading">
                    <p class="wizard-kicker">Étape 1/4</p>
                    <h2 class="section-title" id="step-identity-title">Identité et contact</h2>
                    <p>Les coordonnées utilisées pour présenter et contacter votre laboratoire.</p>
                </div>
                <div class="form-grid">
                    <div class="field-group"><label for="nom_laboratoire">Nom du laboratoire</label><input id="nom_laboratoire" name="nom_laboratoire" type="text" autocomplete="organization" required></div>
                    <div class="field-group"><label for="nom_gerant">Nom du gérant</label><input id="nom_gerant" name="nom_gerant" type="text" autocomplete="name" required></div>
                    <div class="field-group"><label for="telephone">Téléphone</label><input id="telephone" name="telephone" type="tel" autocomplete="tel" required></div>
                    <div class="field-group"><label for="email">Adresse e-mail</label><input id="email" name="email" type="email" autocomplete="email" required></div>
                    <div class="field-group"><label for="adresse">Adresse</label><input id="adresse" name="adresse" type="text" autocomplete="street-address" required></div>
                    <div class="field-group"><label for="ville">Ville</label><input id="ville" name="ville" type="text" autocomplete="address-level2" required></div>
                    <div class="field-group"><label for="site_web">Site web <span class="optional">(optionnel)</span></label><input id="site_web" name="site_web" type="url" inputmode="url" placeholder="https://"></div>
                    <div class="field-group"><label for="instagram">Instagram <span class="optional">(optionnel)</span></label><input id="instagram" name="instagram" type="url" inputmode="url" placeholder="https://instagram.com/…"></div>
                </div>
                <p class="wizard-validation-message" data-step-message role="alert" hidden></p>
                <div class="wizard-actions wizard-actions-end"><button type="button" class="btn btn-primary" data-next>Suivant <span aria-hidden="true">→</span></button></div>
            </section>

            <section class="wizard-panel" data-form-step="1" aria-labelledby="step-security-title" hidden>
                <div class="wizard-panel-heading">
                    <p class="wizard-kicker">Étape 2/4</p>
                    <h2 class="section-title" id="step-security-title">Sécurité et présentation</h2>
                    <p>Choisissez un mot de passe sécurisé et présentez votre laboratoire.</p>
                </div>
                <div class="form-grid">
                    <div class="field-group"><label for="mot_de_passe">Mot de passe <span class="optional">(8 caractères minimum)</span></label><input id="mot_de_passe" name="mot_de_passe" type="password" minlength="8" autocomplete="new-password" required></div>
                    <div class="field-group"><label for="confirmer_mot_de_passe">Confirmation du mot de passe</label><input id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" type="password" minlength="8" autocomplete="new-password" required></div>
                    <div class="field-group full-width"><label for="description">Description du laboratoire</label><textarea id="description" name="description" rows="5" required></textarea></div>
                </div>
                <p class="wizard-validation-message" data-step-message role="alert" hidden></p>
                <div class="wizard-actions wizard-actions-between"><button type="button" class="btn btn-secondary" data-previous>← Précédent</button><button type="button" class="btn btn-primary" data-next>Suivant <span aria-hidden="true">→</span></button></div>
            </section>

            <section class="wizard-panel" data-form-step="2" aria-labelledby="step-services-title" hidden>
                <div class="wizard-panel-heading">
                    <p class="wizard-kicker">Étape 3/4</p>
                    <h2 class="section-title" id="step-services-title">Services proposés</h2>
                    <p>Sélectionnez les spécialités proposées par votre laboratoire.</p>
                </div>
                <fieldset class="service-options-wizard" data-services-group>
                    <legend class="sr-only">Choisir au moins un service</legend>
                    <div class="service-options-grid">
                        <?php foreach ($services as $service): ?>
                            <label class="service-option"><input type="checkbox" name="services[]" value="<?= (int)$service['id'] ?>"><span><?= e($service['nom']) ?></span></label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>
                <p class="wizard-validation-message" data-step-message role="alert" hidden></p>
                <div class="wizard-actions wizard-actions-between"><button type="button" class="btn btn-secondary" data-previous>← Précédent</button><button type="button" class="btn btn-primary" data-next>Suivant <span aria-hidden="true">→</span></button></div>
            </section>

            <section class="wizard-panel" data-form-step="3" aria-labelledby="step-files-title" hidden>
                <div class="wizard-panel-heading">
                    <p class="wizard-kicker">Étape 4/4</p>
                    <h2 class="section-title" id="step-files-title">Documents et photos</h2>
                    <p>Ajoutez les éléments utiles à la vérification et à la présentation de votre laboratoire.</p>
                </div>
                <div class="file-field-group">
                    <?php if ($hasDocument): ?>
                        <label for="document_verification">Document de vérification <span class="optional">(PDF requis)</span></label>
                        <input id="document_verification" name="document_verification[]" type="file" accept="application/pdf,.pdf" data-document-file required>
                        <small>PDF, 10 Mo maximum.</small>
                    <?php else: ?>
                        <span class="file-field-label">Document de vérification <span class="optional">(facultatif)</span></span>
                        <p class="file-selection-summary">Le système actuel ne stocke pas les documents de vérification. Vous pouvez continuer sans PDF.</p>
                    <?php endif; ?>
                </div>
                <div class="file-field-group">
                    <label for="images">Photos du laboratoire</label>
                    <input id="images" name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple required data-images-file>
                    <small>JPEG, PNG ou WebP, 5 Mo par image, 12 images maximum. Ajoutez au moins une photo.</small>
                    <p class="file-selection-summary" data-image-summary aria-live="polite">Aucune photo sélectionnée</p>
                </div>
                <p class="wizard-validation-message" data-step-message role="alert" hidden></p>
                <div class="wizard-actions wizard-actions-between"><button type="button" class="btn btn-secondary" data-previous>← Précédent</button><button type="submit" class="btn btn-primary">Créer mon compte</button></div>
            </section>
        </form>
    </main>

    <footer class="page-footer"><p>Déjà inscrit ? <a href="login.php">Se connecter</a></p></footer>
</div>
<script src="assets/js/register.js" defer></script>
</body>
</html>
