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
<?php $pageTitle='Espace laboratoire';$assetPrefix='../';$navContext='laboratoire';$loadInterFont=true;$pageStyles=['assets/css/laboratoire.css'];require __DIR__.'/../includes/header.php';require __DIR__.'/../includes/navbar.php'; ?>
<main class="lab-main">
    <section class="lab-hero">
        <div><p class="lab-eyebrow">Espace laboratoire</p><h1>Bonjour <?=e($lab['nom_laboratoire'])?></h1><p class="lab-muted"><?=e($messages[$status]??'')?> · <?=e($lab['ville'])?></p></div>
        <div class="lab-actions"><a class="lab-button lab-primary" href="profile.php">Modifier le profil</a><a class="lab-button lab-secondary" href="images.php">Gérer les photos</a><a class="lab-button" href="upload-document.php">Document</a></div>
    </section>
    <section class="lab-grid">
        <article class="lab-card lab-stat"><span>Statut du compte</span><strong><span class="lab-badge <?=$status?>"><?=e($schema->statusLabel($lab['statut']))?></span></strong></article>
        <article class="lab-card lab-stat"><span>Services proposés</span><strong><?=$servicesCount?></strong></article>
        <article class="lab-card lab-stat"><span>Photos enregistrées</span><strong><?=$imagesCount?></strong></article>
        <article class="lab-card lab-stat"><span>Date d'inscription</span><strong style="font-size:22px"><?=e($registered)?></strong></article>
    </section>
    <section class="lab-layout">
        <div>
            <article class="lab-card lab-panel">
                <div class="lab-profile-head"><img class="lab-logo" src="../<?=e($cover)?>" alt="Logo ou photo du laboratoire"><div><h2>Résumé du profil</h2><p><?=e($lab['description']?:'Aucune description renseignée pour le moment.')?></p></div></div>
            </article>
            <article class="lab-card lab-panel"><h2>Services</h2><?php if($services):?><ul class="lab-list"><?php foreach($services as $s):?><li><?=e($s['nom'])?></li><?php endforeach;?></ul><?php else:?><p class="empty-state">Aucun service enregistré.</p><?php endif;?></article>
        </div>
        <aside>
            <article class="lab-card lab-panel"><h2>Coordonnées</h2><p><strong>Gérant :</strong> <?=e($lab['nom_gerant'])?></p><p><strong>Téléphone :</strong> <?=e($lab['telephone'])?></p><p><strong>E-mail :</strong> <?=e($lab['email'])?></p><p><strong>Adresse :</strong> <?=e($lab['adresse'])?></p></article>
            <article class="lab-card lab-panel"><h2>Dernières photos</h2><?php if($photos):?><div class="lab-photos"><?php foreach($photos as $im):?><img class="lab-photo" src="../<?=e($im['chemin'])?>" alt="Photo du laboratoire"><?php endforeach;?></div><?php else:?><img class="lab-photo" src="../assets/images/lab-placeholder.svg" alt="Aucune photo enregistrée"><?php endif;?></article>
        </aside>
    </section>
</main><?php require_once __DIR__ . "/../includes/footer.php"; ?>
