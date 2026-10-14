<?php
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/schema.php';
$schema=dental_schema($pdo);$labIdColumn=$schema->labId();$serviceIdColumn=$schema->serviceId();$serviceNameColumn=$schema->serviceName();$imagePathColumn=$schema->imagePath();$imageIdColumn=$schema->imageId();$id=require_lab();
$select="SELECT `$labIdColumn` id,nom_laboratoire,nom_gerant,telephone,email,adresse,ville,site_web,instagram,description,statut".($schema->hasCreatedAt()?',created_at':'')." FROM laboratoire WHERE `$labIdColumn`=?";
$q=$pdo->prepare($select);$q->execute([$id]);$lab=$q->fetch();if(!$lab){session_destroy();redirect('../login.php');}
$q=$pdo->prepare('SELECT COUNT(*) FROM laboratoire_service WHERE laboratoire_id=?');$q->execute([$id]);$servicesCount=(int)$q->fetchColumn();
$q=$pdo->prepare('SELECT COUNT(*) FROM image_laboratoire WHERE laboratoire_id=?');$q->execute([$id]);$imagesCount=(int)$q->fetchColumn();
$q=$pdo->prepare("SELECT s.`$serviceNameColumn` nom FROM service s INNER JOIN laboratoire_service ls ON ls.service_id=s.`$serviceIdColumn` WHERE ls.laboratoire_id=? ORDER BY s.`$serviceNameColumn`");$q->execute([$id]);$services=$q->fetchAll();
$q=$pdo->prepare("SELECT `$imagePathColumn` chemin FROM image_laboratoire WHERE laboratoire_id=? ORDER BY `$imageIdColumn` DESC LIMIT 6");$q->execute([$id]);$photos=$q->fetchAll();
$cover=$photos[0]['chemin']??'assets/images/lab-placeholder.svg';$status=$schema->canonicalStatus($lab['statut']);$messages=['en_attente'=>'Votre dossier est en cours de vérification.','rejete'=>'Votre dossier a été rejeté. Contactez l’administration.','valide'=>'Votre laboratoire est visible dans l’annuaire public.'];
$registered=$schema->hasCreatedAt()&&!empty($lab['created_at'])?date('d/m/Y',strtotime($lab['created_at'])):'Non disponible';
?>
<?php $pageTitle='Espace laboratoire';$assetPrefix='../';$navContext='laboratoire';$loadInterFont=true;$bodyClass='lab-page lab-dashboard-page';$pageStyles=['assets/css/laboratoire.css'];require __DIR__.'/../includes/header.php'; ?>
<div class="dashboard-header">
    <a class="private-brand lab-private-brand" href="dashboard.php" aria-label="DentalLink Morocco, tableau de bord"><img src="../assets/images/dental_link.png" alt="DentalLink Morocco"></a>
    <?php $dashboardNavbarShell=true; require __DIR__.'/../includes/navbar.php'; unset($dashboardNavbarShell); ?>
    <div class="dashboard-nav-user"><span class="dashboard-avatar" aria-hidden="true"><?=e(substr((string)$lab['nom_laboratoire'],0,1))?></span><span><?=e($lab['nom_laboratoire'])?></span></div>
</div>
<main class="lab-main dashboard-main">
    <section class="lab-hero dashboard-hero">
        <div><p class="lab-eyebrow">Espace laboratoire</p><h1>Bonjour <span><?=e($lab['nom_laboratoire'])?></span></h1><p class="lab-muted"><?=e($messages[$status]??'Votre espace laboratoire.')?></p></div>
        <div class="lab-actions"><a class="lab-button lab-primary" href="profile.php">Modifier le profil</a><a class="lab-button lab-secondary" href="images.php">Gérer les photos</a><a class="lab-button" href="upload-document.php">Document</a></div>
    </section>
    <section class="lab-grid dashboard-stats" aria-label="Statistiques du laboratoire">
        <article class="lab-card lab-stat"><span class="stat-icon stat-icon-status" aria-hidden="true">✓</span><div><span>Statut du compte</span><strong><span class="lab-badge <?=$status?>"><?=e($schema->statusLabel($lab['statut']))?></span></strong></div></article>
        <article class="lab-card lab-stat"><span class="stat-icon stat-icon-services" aria-hidden="true">✦</span><div><span>Services proposés</span><strong><?=$servicesCount?></strong><small>services associés</small></div></article>
        <article class="lab-card lab-stat"><span class="stat-icon stat-icon-photos" aria-hidden="true">▧</span><div><span>Photos enregistrées</span><strong><?=$imagesCount?></strong><small>photos au total</small></div></article>
        <article class="lab-card lab-stat"><span class="stat-icon stat-icon-date" aria-hidden="true">◷</span><div><span>Inscription</span><strong class="stat-date"><?=e($registered)?></strong><small>Date d'inscription</small></div></article>
    </section>
    <section class="lab-layout dashboard-layout">
        <div>
            <article class="lab-card lab-panel dashboard-profile-card">
                <div class="dashboard-card-heading"><div><p class="dashboard-section-kicker">Votre présence</p><h2>Résumé du profil</h2></div><a class="dashboard-inline-link" href="profile.php">Modifier <span aria-hidden="true">→</span></a></div>
                <div class="lab-profile-head"><img class="lab-logo dashboard-profile-logo" src="../assets/images/dental_link.png" alt="DentalLink Morocco"><div><h3><?=e($lab['nom_laboratoire'])?></h3><p class="profile-description"><?=e($lab['description']?:'Aucune description renseignée pour le moment.')?></p></div></div>
                <div class="profile-highlights"><span><i aria-hidden="true">✓</i> Qualité</span><span><i aria-hidden="true">✓</i> Précision</span><span><i aria-hidden="true">✓</i> Expertise</span></div>
            </article>
            <article class="lab-card lab-panel dashboard-services-card"><div class="dashboard-card-heading"><div><p class="dashboard-section-kicker">Votre offre</p><h2>Services proposés</h2></div><a class="dashboard-inline-link" href="profile.php">Modifier <span aria-hidden="true">→</span></a></div><?php if($services):?><ul class="lab-list"><?php foreach($services as $s):?><li><?=e($s['nom'])?></li><?php endforeach;?></ul><?php else:?><p class="empty-state">Aucun service enregistré.</p><?php endif;?></article>
        </div>
        <aside class="dashboard-aside">
            <article class="lab-card lab-panel dashboard-contact-card"><div class="dashboard-card-heading"><div><p class="dashboard-section-kicker">Restons en contact</p><h2>Coordonnées</h2></div><a class="dashboard-icon-link" href="profile.php" aria-label="Modifier les coordonnées">↗</a></div><dl class="contact-list"><div><dt><span aria-hidden="true">♙</span> Gérant</dt><dd><?=e($lab['nom_gerant']?:'Non renseigné')?></dd></div><div><dt><span aria-hidden="true">☎</span> Téléphone</dt><dd><?=e($lab['telephone']?:'Non renseigné')?></dd></div><div><dt><span aria-hidden="true">✉</span> E-mail</dt><dd><?=e($lab['email']?:'Non renseigné')?></dd></div><div><dt><span aria-hidden="true">⌖</span> Adresse</dt><dd><?php $dashboardAddress=trim(($lab['adresse']??'').' '.($lab['ville']??''));if($dashboardAddress!==''):?><a href="https://www.google.com/maps/search/?api=1&amp;query=<?=rawurlencode($dashboardAddress)?>" target="_blank" rel="noopener noreferrer"><?=e($dashboardAddress)?></a><?php else:?>Non renseignée<?php endif;?></dd></div></dl></article>
            <article class="lab-card lab-panel dashboard-photos-card"><div class="dashboard-card-heading"><div><p class="dashboard-section-kicker">Votre galerie</p><h2>Dernières photos</h2></div><a class="dashboard-inline-link" href="images.php">Voir toutes <span aria-hidden="true">→</span></a></div><?php if($photos):?><div class="lab-photos"><?php foreach(array_slice($photos,0,3) as $im):?><img class="lab-photo" src="../<?=e($im['chemin'])?>" alt="Photo récente du laboratoire" loading="lazy"><?php endforeach;?></div><?php else:?><div class="dashboard-empty-photo"><span aria-hidden="true">▧</span><p>Aucune photo enregistrée</p><a href="images.php">Ajouter des photos</a></div><?php endif;?></article>
        </aside>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
