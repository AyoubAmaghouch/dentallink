<?php
require_once __DIR__ . '/../includes/auth.php';require_once __DIR__.'/../includes/schema.php';$schema=dental_schema($pdo);$labIdColumn=$schema->labId();
if(!$schema->hasDocument()){http_response_code(404);exit('Aucun document configuré. Appliquez la migration SQL.');}
$id=require_lab();$q=$pdo->prepare("SELECT document_verification FROM laboratoire WHERE `$labIdColumn`=?");$q->execute([$id]);$relative=$q->fetchColumn();$base=realpath(dirname(__DIR__).'/assets/uploads/documents');$file=$relative?realpath(dirname(__DIR__).'/'.$relative):false;
if(!$base||!$file||!str_starts_with($file,$base.DIRECTORY_SEPARATOR)||!is_file($file)){http_response_code(404);exit('Document introuvable.');}
if((new finfo(FILEINFO_MIME_TYPE))->file($file)!=='application/pdf'){http_response_code(415);exit('Type de document invalide.');}
header('Content-Type: application/pdf');header('Content-Disposition: inline; filename="mon-document-de-verification.pdf"');header('X-Content-Type-Options: nosniff');header('Content-Length: '.filesize($file));readfile($file);
