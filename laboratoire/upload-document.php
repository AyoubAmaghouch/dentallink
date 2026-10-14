<?php
require_once __DIR__.'/../includes/auth.php';require_once __DIR__.'/../includes/schema.php';$schema=dental_schema($pdo);$labIdColumn=$schema->labId();$id=require_lab();$error='';$current=null;$hasDoc=$schema->hasDocument();if($hasDoc){$q=$pdo->prepare("SELECT document_verification FROM laboratoire WHERE `$labIdColumn`=?");$q->execute([$id]);$current=$q->fetchColumn();}else{$error='Appliquez la migration SQL pour gérer les documents.';}if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();$new=[];try{if(!$hasDoc)throw new RuntimeException('Appliquez la migration SQL avant de téléverser le document.');$new=upload_files($_FILES['document_verification']??[],'documents',['application/pdf'],10485760,1);if(count($new)!==1)throw new RuntimeException('Choisissez un PDF.');$pdo->beginTransaction();$q=$pdo->prepare("UPDATE laboratoire SET document_verification=? WHERE `$labIdColumn`=?");$q->execute([$new[0],$id]);$pdo->commit();$old=dirname(__DIR__).'/'.$current;if($current&&is_file($old))unlink($old);$current=$new[0];$error='';}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();foreach($new as $p){$f=dirname(__DIR__).'/'.$p;if(is_file($f))unlink($f);}if($e instanceof RuntimeException)$error=$e->getMessage();else $error='Mise à jour du document impossible.';}}
?>
<?php $pageTitle = 'Document de vérification'; $assetPrefix = '../'; $navContext = 'laboratoire'; $loadInterFont=true; $bodyClass='lab-page'; $pageStyles=['assets/css/laboratoire.css']; require __DIR__ . '/../includes/header.php'; require __DIR__ . '/../includes/navbar.php'; ?>
<main class="lab-main">
    <section class="lab-hero"><div><p class="lab-eyebrow">Espace laboratoire</p><h1>Document de vérification</h1><p class="lab-muted">Consultez ou remplacez le document de votre dossier.</p></div><div class="lab-actions"><a class="lab-button" href="dashboard.php">Tableau de bord</a></div></section>
    <?php if($error):?><p class="lab-alert error" role="alert"><?=e($error)?></p><?php endif;?>
    <section class="lab-card lab-panel document-panel">
        <div class="dashboard-card-heading"><div><p class="dashboard-section-kicker">Dossier laboratoire</p><h2>Document enregistré</h2></div></div>
        <?php if($current):?><p class="lab-muted">Votre document est disponible. <a class="document-view-link" href="document.php" target="_blank" rel="noopener">Consulter le PDF</a></p><?php else:?><p class="lab-muted">Aucun document n’est enregistré pour le moment.</p><?php endif;?>
        <form class="lab-form document-upload-form" method="post" enctype="multipart/form-data"><?=csrf_field()?>
            <label>Choisir un document PDF<input type="file" name="document_verification[]" accept="application/pdf,.pdf" required></label>
            <button class="lab-button lab-primary" type="submit">Remplacer le document</button>
        </form>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
