<?php
require_once __DIR__.'/../includes/admin-functions.php';
$schema=dental_schema($pdo);$labId=$schema->labId();$serviceId=$schema->serviceId();$serviceName=$schema->serviceName();$imageId=$schema->imageId();$imagePath=$schema->imagePath();$hasDocument=$schema->hasDocument();$hasLabDate=$schema->hasCreatedAt();$hasImageDate=$schema->hasCreatedAt('image_laboratoire');
$docSelect=$hasDocument?'l.document_verification':'NULL AS document_verification';$dateSelect=$hasLabDate?'l.created_at':'NULL AS created_at';$imageSort=$hasImageDate?'i.created_at,':'';$labSort=$hasLabDate?'l.created_at ASC,':'';
$sql="SELECT l.`$labId` id,l.nom_laboratoire,l.nom_gerant,l.email,l.telephone,l.adresse,l.ville,l.description,$docSelect,$dateSelect,(SELECT GROUP_CONCAT(s.`$serviceName` ORDER BY s.`$serviceName` SEPARATOR ', ') FROM laboratoire_service ls JOIN service s ON s.`$serviceId`=ls.service_id WHERE ls.laboratoire_id=l.`$labId`) services FROM laboratoire l WHERE l.statut=? ORDER BY $labSort l.`$labId` ASC";
$q=$pdo->prepare($sql);$q->execute([$schema->status('en_attente')]);$labs=$q->fetchAll();
$pageTitle='Validation des inscriptions';$assetPrefix='../';$navContext='admin';$pageStyles=['assets/css/admin.css','assets/css/admin-validation.css'];$pageScripts=['assets/js/admin.js'];require __DIR__.'/../includes/header.php';require __DIR__.'/../includes/navbar.php';$flash=admin_take_flash();
?>
<main class="admin-main admin-validation-page">
    <div class="admin-heading validation-page-heading">
        <div><p class="admin-eyebrow">Modération</p><h1>Inscriptions à vérifier</h1><p class="admin-muted">Examinez les informations avant de publier un laboratoire dans l’annuaire.</p></div>
        <div class="validation-queue-count"><strong><?= count($labs) ?></strong><span><?= count($labs) === 1 ? 'dossier en attente' : 'dossiers en attente' ?></span></div>
    </div>
    <?php if($flash): ?><div class="admin-alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
    <?php if(!$labs): ?>
        <section class="admin-panel validation-empty-state"><span aria-hidden="true">✓</span><h2>La file est à jour</h2><p>Aucune inscription n’attend votre validation. Les nouveaux dossiers apparaîtront ici.</p><a class="admin-button admin-secondary" href="laboratoires.php">Voir les laboratoires</a></section>
    <?php else: ?>
        <section class="review-list" aria-label="Dossiers à vérifier">
        <?php foreach($labs as $lab): ?>
            <?php $imagesQuery=$pdo->prepare("SELECT `$imagePath` FROM image_laboratoire WHERE laboratoire_id=? ORDER BY `$imageId` DESC");$imagesQuery->execute([$lab['id']]);$labImages=$imagesQuery->fetchAll(PDO::FETCH_COLUMN);$serviceNames=array_values(array_filter(array_map('trim',explode(',',(string)($lab['services']??'')))));$labNameChars=preg_split('//u',(string)$lab['nom_laboratoire'],-1,PREG_SPLIT_NO_EMPTY)?:[];$labInitial=strtoupper($labNameChars[0]??'L'); ?>
            <article class="admin-panel review-card">
                <header class="review-card-head">
                    <div class="review-identity">
                        <span class="review-lab-avatar" aria-hidden="true"><?= e($labInitial) ?></span>
                        <div class="review-identity-copy">
                            <div class="review-topline"><span class="status-badge status-en_attente">En attente</span><span class="review-date"><?= $lab['created_at'] ? 'Inscrit le '.e(date('d/m/Y',strtotime($lab['created_at']))) : 'Date indisponible' ?></span></div>
                            <h2><?= e($lab['nom_laboratoire']) ?></h2>
                            <p><?= e($lab['ville']) ?><?= $lab['adresse'] ? ' · '.e($lab['adresse']) : '' ?></p>
                        </div>
                    </div>
                    <a class="review-open-link" href="laboratoires.php?id=<?= (int)$lab['id'] ?>">Ouvrir le dossier <span aria-hidden="true">↗</span></a>
                </header>
                <div class="review-card-body">
                    <section class="review-info-section">
                        <h3>Coordonnées</h3>
                        <div class="review-contact-grid">
                            <div class="review-contact-item"><span class="review-contact-icon" aria-hidden="true">G</span><span><small>Gérant</small><strong><?= e($lab['nom_gerant']) ?></strong></span></div>
                            <a class="review-contact-item" href="mailto:<?= e($lab['email']) ?>"><span class="review-contact-icon" aria-hidden="true">@</span><span><small>E-mail</small><strong><?= e($lab['email']) ?></strong></span></a>
                            <a class="review-contact-item" href="tel:<?= e($lab['telephone']) ?>"><span class="review-contact-icon" aria-hidden="true">⌕</span><span><small>Téléphone</small><strong><?= e($lab['telephone']) ?></strong></span></a>
                        </div>
                    </section>
                    <section class="review-info-section review-services-section">
                        <div class="review-section-title"><h3>Services proposés</h3><span><?= count($serviceNames) ?></span></div>
                        <?php if($serviceNames): ?><div class="review-service-tags"><?php foreach($serviceNames as $serviceName): ?><span><?= e($serviceName) ?></span><?php endforeach; ?></div><?php else: ?><p class="review-no-services">Aucun service renseigné.</p><?php endif; ?>
                    </section>
                    <?php if(trim((string)$lab['description']) !== ''): ?><section class="review-description-section"><h3>Présentation</h3><p><?= nl2br(e($lab['description'])) ?></p></section><?php endif; ?>
                </div>
                <section class="review-media-section">
                    <div class="review-section-title"><div><h3>Documents et photos</h3><p>Pièces jointes fournies avec le dossier.</p></div><span><?= count($labImages) + (!empty($lab['document_verification']) ? 1 : 0) ?> élément(s)</span></div>
                    <div class="review-media-grid">
                        <?php if(!empty($lab['document_verification'])): ?><a class="review-document-card" href="document.php?id=<?= (int)$lab['id'] ?>" target="_blank" rel="noopener"><span class="review-document-icon" aria-hidden="true">PDF</span><span><strong>Document de vérification</strong><small>Ouvrir le document privé</small></span><span class="review-media-arrow" aria-hidden="true">↗</span></a><?php else: ?><div class="review-no-document"><span aria-hidden="true">▤</span><span><strong>Aucun document joint</strong><small>Le dossier ne contient pas de document de vérification.</small></span></div><?php endif; ?>
                        <?php foreach($labImages as $image): ?><a class="review-photo-link" href="../<?= e($image) ?>" target="_blank" rel="noopener"><img src="../<?= e($image) ?>" alt="Photo du laboratoire <?= e($lab['nom_laboratoire']) ?>"><span aria-hidden="true">↗</span></a><?php endforeach; ?>
                        <?php if(!$labImages): ?><p class="review-no-photos">Aucune photo jointe.</p><?php endif; ?>
                    </div>
                </section>
                <footer class="review-card-footer">
                    <span>Choisissez une décision après avoir vérifié le dossier.</span>
                    <div class="review-actions">
                        <form method="post" action="../crud/update.php" data-confirm="Valider ce laboratoire et le rendre visible dans l’annuaire ?"><?= csrf_field() ?><input type="hidden" name="laboratoire_id" value="<?= (int)$lab['id'] ?>"><button class="admin-button admin-green" name="statut" value="valide"><span aria-hidden="true">✓</span> Valider le laboratoire</button></form>
                        <form method="post" action="../crud/update.php" data-confirm="Rejeter cette inscription ?"><?= csrf_field() ?><input type="hidden" name="laboratoire_id" value="<?= (int)$lab['id'] ?>"><button class="admin-button admin-red" name="statut" value="rejete"><span aria-hidden="true">×</span> Rejeter</button></form>
                    </div>
                </footer>
            </article>
        <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main><?php require __DIR__.'/../includes/footer.php'; ?>
