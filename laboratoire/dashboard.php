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
        <article class="lab-card lab-stat"><span class="stat-icon stat-icon-status" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg></span><div><span>Statut du compte</span><strong><span class="lab-badge <?=$status?>"><?=e($schema->statusLabel($lab['statut']))?></span></strong></div></article>
        <article class="lab-card lab-stat"><span class="stat-icon stat-icon-services" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7.5 3.5c1.7 0 2.6 1 4.5 1s2.8-1 4.5-1c2.6 0 4.5 2.2 4.5 5.4 0 4.2-2.2 6.3-3.1 9.6-.5 1.9-1.1 3.5-2.3 3.5-1.5 0-1.5-5.2-3.6-5.2s-2.1 5.2-3.6 5.2c-1.2 0-1.8-1.6-2.3-3.5C5.2 15.2 3 13.1 3 8.9c0-3.2 1.9-5.4 4.5-5.4Z"/></svg></span><div><span>Services proposés</span><strong><?=$servicesCount?></strong><small>services associés</small></div></article>
        <article class="lab-card lab-stat"><span class="stat-icon stat-icon-photos" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9" r="1.5"/><path d="m21 15-5-5L5 20"/></svg></span><div><span>Photos enregistrées</span><strong><?=$imagesCount?></strong><small>photos au total</small></div></article>
        <article class="lab-card lab-stat"><span class="stat-icon stat-icon-date" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg></span><div><span>Inscription</span><strong class="stat-date"><?=e($registered)?></strong><small>Date d'inscription</small></div></article>
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
            <article class="lab-card lab-panel dashboard-contact-card"><div class="dashboard-card-heading"><div><p class="dashboard-section-kicker">Restons en contact</p><h2>Coordonnées</h2></div><a class="dashboard-icon-link" href="profile.php" aria-label="Modifier les coordonnées"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L9 17l-4 1 1-4Z"/></svg></a></div><dl class="contact-list"><div><dt><span aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg></span> Gérant</dt><dd><?=e($lab['nom_gerant']?:'Non renseigné')?></dd></div><div><dt><span aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 3h3l2 5-2 2a15 15 0 0 0 4 4l2-2 5 2v3a2 2 0 0 1-2 2A16 16 0 0 1 5 5a2 2 0 0 1 2-2Z"/></svg></span> Téléphone</dt><dd><?=e($lab['telephone']?:'Non renseigné')?></dd></div><div><dt><span aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg></span> E-mail</dt><dd><?=e($lab['email']?:'Non renseigné')?></dd></div><div><dt><span aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg></span> Adresse</dt><dd><?php $dashboardAddress=trim(($lab['adresse']??'').' '.($lab['ville']??''));if($dashboardAddress!==''):?><a href="https://www.google.com/maps/search/?api=1&amp;query=<?=rawurlencode($dashboardAddress)?>" target="_blank" rel="noopener noreferrer"><?=e($dashboardAddress)?></a><?php else:?>Non renseignée<?php endif;?></dd></div></dl></article>
            <article class="lab-card lab-panel dashboard-photos-card"><div class="dashboard-card-heading"><div><p class="dashboard-section-kicker">Votre galerie</p><h2>Dernières photos</h2></div><a class="dashboard-inline-link" href="images.php">Voir toutes <span aria-hidden="true">→</span></a></div><?php if($photos):?><div class="lab-photos"><?php foreach(array_slice($photos,0,3) as $im):?><img class="lab-photo" src="../<?=e($im['chemin'])?>" alt="Photo récente du laboratoire" loading="lazy"><?php endforeach;?></div><?php else:?><div class="dashboard-empty-photo"><span aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9" r="1.5"/><path d="m21 15-5-5L5 20"/></svg></span><p>Aucune photo enregistrée</p><a href="images.php">Ajouter des photos</a></div><?php endif;?></article>
        </aside>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
