<?php
require_once __DIR__ . '/../includes/admin-functions.php';$schema=dental_schema($pdo);$labIdColumn=$schema->labId();
$hasDocument=$schema->hasDocument();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) { http_response_code(404); exit('Document introuvable.'); }
if(!$hasDocument){http_response_code(404);exit('Aucun document de vérification n’est configuré dans le schéma SQL.');}
$q = $pdo->prepare("SELECT document_verification FROM laboratoire WHERE `$labIdColumn`=?"); $q->execute([$id]);
$relative = $q->fetchColumn(); $file = admin_file_path($relative ?: null, 'documents');
if (!$file) { http_response_code(404); exit('Document introuvable.'); }
if ((new finfo(FILEINFO_MIME_TYPE))->file($file) !== 'application/pdf') { http_response_code(415); exit('Type de document invalide.'); }
header('Content-Type: application/pdf'); header('Content-Disposition: inline; filename="verification-' . $id . '.pdf"'); header('X-Content-Type-Options: nosniff'); header('Content-Length: ' . filesize($file)); readfile($file);
