<?php
require_once __DIR__ . '/../includes/admin-functions.php';$schema=dental_schema($pdo);$imageIdColumn=$schema->imageId();$imagePathColumn=$schema->imagePath();$labIdColumn=$schema->labId();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Méthode non autorisée.'); }
csrf_check(); $imageId=(int)($_POST['image_id']??0);$labId=(int)($_POST['laboratoire_id']??0);
$q=$pdo->prepare("SELECT `$imagePathColumn` FROM image_laboratoire WHERE `$imageIdColumn`=? AND laboratoire_id=?");$q->execute([$imageId,$labId]);$path=$q->fetchColumn();
if($path){$pdo->beginTransaction();$pdo->prepare("DELETE FROM image_laboratoire WHERE `$imageIdColumn`=? AND laboratoire_id=?")->execute([$imageId,$labId]);$pdo->commit();admin_delete_file($path,'laboratoires');admin_flash('success','Photo supprimée.');}
admin_redirect('laboratoires.php?id='.$labId);
