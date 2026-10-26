<?php
require_once __DIR__.'/../includes/admin-functions.php';$schema=dental_schema($pdo);$sid=$schema->serviceId();$sname=$schema->serviceName();$error='';$flash=admin_take_flash();$editId=(int)($_GET['id']??0);$editing=null;
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();$action=(string)($_POST['action']??'');$id=(int)($_POST['id']??0);$name=trim((string)($_POST['nom']??''));try{if($action==='delete'){$pdo->prepare("DELETE FROM service WHERE `$sid`=?")->execute([$id]);admin_flash('success','Service supprimé avec ses associations.');admin_redirect('services.php');}if($action!=='save'||$name===''||utf8_length($name)>100)throw new RuntimeException('Le nom du service est obligatoire (100 caractères maximum).');if($id){$pdo->prepare("UPDATE service SET `$sname`=? WHERE `$sid`=?")->execute([$name,$id]);}else{$pdo->prepare("INSERT INTO service(`$sname`) VALUES(?)")->execute([$name]);}admin_flash('success','Service enregistré.');admin_redirect('services.php');}catch(PDOException $e){$error='Ce service existe déjà ou la base de données a refusé la modification.';}catch(RuntimeException $e){$error=$e->getMessage();}}
if($editId){$q=$pdo->prepare("SELECT `$sid` id,`$sname` nom FROM service WHERE `$sid`=?");$q->execute([$editId]);$editing=$q->fetch();}$search=trim((string)($_GET['q']??''));$params=[];$where='';if($search!==''){$where="WHERE s.`$sname` LIKE ?";$params[]='%'.$search.'%';}$q=$pdo->prepare("SELECT s.`$sid` id,s.`$sname` nom,COUNT(ls.laboratoire_id) lab_count FROM service s LEFT JOIN laboratoire_service ls ON ls.service_id=s.`$sid` $where GROUP BY s.`$sid`,s.`$sname` ORDER BY s.`$sname`");$q->execute($params);$services=$q->fetchAll();
$pageTitle='Services dentaires';$assetPrefix='../';$navContext='admin';$pageStyles=['assets/css/admin.css','assets/css/admin-services.css'];$pageScripts=['assets/js/admin.js'];require __DIR__.'/../includes/header.php';require __DIR__.'/../includes/navbar.php';
?>
<main class="admin-main admin-services-page">
    <div class="admin-heading services-page-heading">
        <div><p class="admin-eyebrow">Catalogue partagé</p><h1>Services dentaires</h1><p class="admin-muted">Gérez les prestations proposées aux laboratoires du réseau.</p></div>
    </div>
    <?php if($flash): ?><div class="admin-alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
    <?php if($error): ?><div class="admin-alert error" role="alert"><?= e($error) ?></div><?php endif; ?>
    <section class="admin-panels services-layout services-workspace">
        <article class="admin-panel service-editor-panel">
            <div class="service-editor-heading">
                <span class="service-editor-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m12 2 9 5v10l-9 5-9-5V7z"/><path d="m3 7 9 5 9-5m-9 5v10"/></svg></span>
                <span class="service-editor-kicker"><?= $editing ? 'Modification' : 'Nouveau service' ?></span>
                <h2><?= $editing ? 'Modifier le service' : 'Ajouter un service' ?></h2>
                <p>Ajoutez une prestation au catalogue partagé des laboratoires.</p>
            </div>
            <form method="post" class="service-editor-form">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">
                <label for="service-name">Nom du service <i>*</i></label>
                <input id="service-name" name="nom" maxlength="100" required placeholder="Ex. Prothèse dentaire" value="<?= e((string)($editing['nom'] ?? $_POST['nom'] ?? '')) ?>">
                <div class="service-input-meta"><span>Un nom court et facile à reconnaître.</span><span>100 caractères maximum</span></div>
                <button class="admin-button admin-primary service-save-button" type="submit"><span aria-hidden="true"><?= $editing ? '✓' : '+' ?></span><?= $editing ? 'Enregistrer les modifications' : 'Ajouter au catalogue' ?></button>
                <?php if($editing): ?><a class="service-cancel-link" href="services.php">Annuler la modification</a><?php endif; ?>
            </form>
            <div class="service-editor-tip"><span aria-hidden="true">i</span><p>Les services du catalogue peuvent ensuite être associés aux fiches des laboratoires.</p></div>
        </article>
        <article class="admin-panel service-catalog-panel">
            <div class="service-catalog-heading">
                <div><p class="service-editor-kicker">Catalogue</p><h2>Services enregistrés</h2><p><?= count($services) ?> prestation<?= count($services) === 1 ? '' : 's' ?> disponible<?= count($services) === 1 ? '' : 's' ?></p></div>
                <span class="service-count-badge"><?= count($services) ?></span>
            </div>
            <form method="get" class="service-search service-catalog-search" role="search">
                <label class="visually-hidden" for="service-search">Rechercher un service</label>
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
                <input id="service-search" type="search" name="q" value="<?= e($search) ?>" placeholder="Rechercher dans le catalogue">
                <?php if($search !== ''): ?><a class="service-clear-search" href="services.php">Effacer</a><?php endif; ?>
                <button class="admin-button admin-secondary" type="submit">Rechercher</button>
            </form>
            <div class="table-wrap service-table-wrap">
                <table class="admin-table service-table">
                    <thead><tr><th>Service</th><th>Laboratoires associés</th><th><span class="visually-hidden">Actions</span></th></tr></thead>
                    <tbody>
                    <?php foreach($services as $item): ?>
                        <tr>
                            <td><div class="service-name-cell"><span class="service-row-icon" aria-hidden="true">✦</span><strong><?= e($item['nom']) ?></strong></div></td>
                            <td><span class="service-lab-count"><?= (int)$item['lab_count'] ?></span><span class="service-lab-label">laboratoire<?= (int)$item['lab_count'] === 1 ? '' : 's' ?></span></td>
                            <td><div class="service-row-actions">
                                <a class="service-edit-action" href="services.php?id=<?= (int)$item['id'] ?>" aria-label="Modifier <?= e($item['nom']) ?>">Modifier</a>
                <form method="post" data-confirm="Supprimer ce service et ses associations ? Cette action est définitive." class="inline-form">
                                    <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                    <button class="service-delete-action" type="submit" aria-label="Supprimer <?= e($item['nom']) ?>">Supprimer</button>
                                </form>
                            </div></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(!$services): ?><tr><td colspan="3"><div class="service-empty-state"><span aria-hidden="true">⌕</span><strong><?= $search !== '' ? 'Aucun résultat trouvé' : 'Le catalogue est vide' ?></strong><p><?= $search !== '' ? 'Essayez un autre terme de recherche.' : 'Ajoutez votre premier service avec le formulaire.' ?></p></div></td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </section>
</main><?php require __DIR__.'/../includes/footer.php'; ?>
