<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/schema.php';

function registration_uploads(array $files, string $folder, string $kind, int $maxBytes, int $maxFiles): array
{
    if (!isset($files['name']) || !is_array($files['name'])) return [];
    if (count($files['name']) > $maxFiles) throw new RuntimeException('Trop de fichiers sélectionnés.');
    $destination = dirname(__DIR__) . '/assets/uploads/' . $folder;
    if (!is_dir($destination) && !mkdir($destination, 0755, true) && !is_dir($destination)) throw new RuntimeException('Le stockage des fichiers est indisponible.');
    $saved = [];
    try {
    foreach ($files['name'] as $index => $_originalName) {
        $error = (int)($files['error'][$index] ?? UPLOAD_ERR_NO_FILE);
        if ($error === UPLOAD_ERR_NO_FILE) continue;
        if ($error !== UPLOAD_ERR_OK) throw new RuntimeException('Un fichier n’a pas pu être téléversé. Vérifiez sa taille puis réessayez.');
        $temporary = (string)($files['tmp_name'][$index] ?? '');
        $size = (int)($files['size'][$index] ?? 0);
        if ($size < 1 || $size > $maxBytes || !is_uploaded_file($temporary)) throw new RuntimeException('Un fichier est vide, trop volumineux ou invalide.');
        if ($kind === 'image') {
            $info = @getimagesize($temporary);
            $mime = is_array($info) ? ($info['mime'] ?? '') : '';
            $extensions = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'];
            if (!isset($extensions[$mime])) throw new RuntimeException('Les photos doivent être au format JPEG, PNG ou WebP.');
            $extension = $extensions[$mime];
        } else {
            $handle = @fopen($temporary, 'rb');
            $signature = $handle ? fread($handle, 5) : false;
            if (is_resource($handle)) fclose($handle);
            if ($signature !== '%PDF-') throw new RuntimeException('Le document de vérification doit être un fichier PDF valide.');
            $extension = 'pdf';
        }
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        if (!move_uploaded_file($temporary, $destination . '/' . $filename)) throw new RuntimeException('Impossible d’enregistrer un fichier téléversé.');
        $saved[] = 'assets/uploads/' . $folder . '/' . $filename;
    }
    } catch (Throwable $e) {
        foreach ($saved as $relativePath) {
            $file = dirname(__DIR__) . '/' . $relativePath;
            if (is_file($file)) @unlink($file);
        }
        throw $e;
    }
    return $saved;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../register.php');
}
csrf_check();

$savedFiles = [];
try {
    $schema = dental_schema($pdo);
    $labIdColumn = $schema->labId();
    $serviceIdColumn = $schema->serviceId();
    $imagePathColumn = $schema->imagePath();
    $hasDocument = $schema->hasDocument();

    $requiredFields = ['nom_laboratoire', 'nom_gerant', 'telephone', 'email', 'adresse', 'ville', 'description'];
    $data = [];
    foreach ($requiredFields as $field) {
        $data[$field] = trim((string)($_POST[$field] ?? ''));
        if ($data[$field] === '') {
            throw new RuntimeException('Veuillez remplir tous les champs obligatoires.');
        }
    }
    validate_lab_field_lengths($data);
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('Veuillez saisir une adresse e-mail valide.');
    }

    $password = (string)($_POST['mot_de_passe'] ?? '');
    if (strlen($password) < 8) {
        throw new RuntimeException('Le mot de passe doit contenir au moins 8 caractères.');
    }
    if (!hash_equals($password, (string)($_POST['confirmer_mot_de_passe'] ?? ''))) {
        throw new RuntimeException('Les deux mots de passe ne correspondent pas.');
    }

    $serviceIds = array_values(array_unique(array_filter(
        array_map('intval', (array)($_POST['services'] ?? [])),
        static fn(int $id): bool => $id > 0
    )));
    if (!$serviceIds) {
        throw new RuntimeException('Sélectionnez au moins un service.');
    }

    $q = $pdo->prepare("SELECT `$labIdColumn` FROM laboratoire WHERE email = ? LIMIT 1");
    $q->execute([$data['email']]);
    if ($q->fetchColumn() !== false) {
        throw new RuntimeException('Cette adresse e-mail est déjà utilisée.');
    }

    $serviceMarks = implode(',', array_fill(0, count($serviceIds), '?'));
    $q = $pdo->prepare("SELECT `$serviceIdColumn` FROM service WHERE `$serviceIdColumn` IN ($serviceMarks)");
    $q->execute($serviceIds);
    if (count($q->fetchAll(PDO::FETCH_COLUMN)) !== count($serviceIds)) {
        throw new RuntimeException('Un service sélectionné n’existe plus. Rechargez la page et réessayez.');
    }

    $website = trim((string)($_POST['site_web'] ?? ''));
    $instagram = trim((string)($_POST['instagram'] ?? ''));
    foreach ([$website, $instagram] as $url) {
        if ($url !== '' && (!filter_var($url, FILTER_VALIDATE_URL) || !in_array(strtolower((string)parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true))) {
            throw new RuntimeException('Les liens web doivent être des URL HTTP ou HTTPS valides.');
        }
    }
    validate_lab_field_lengths(['site_web' => $website, 'instagram' => $instagram]);
    $documentPath = null;
    $documentUpload = $_FILES['document_verification'] ?? null;
    $documentRequested = is_array($documentUpload) && isset($documentUpload['name'])
        && (($documentUpload['error'][0] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE);
    if ($documentRequested) {
        if (!$hasDocument) {
            throw new RuntimeException('Le schéma de la base ne stocke pas encore les documents de vérification. Vous pouvez poursuivre sans joindre de PDF.');
        }
        $documents = registration_uploads($documentUpload, 'documents', 'pdf', 10485760, 1);
        if (count($documents) !== 1) {
            throw new RuntimeException('Le document PDF de vérification est invalide ou manquant.');
        }
        $documentPath = $documents[0];
        $savedFiles[] = $documentPath;
    } elseif ($hasDocument) {
        throw new RuntimeException('Sélectionnez le document PDF de vérification.');
    }

    $images = registration_uploads($_FILES['images'] ?? [], 'laboratoires', 'image', 5242880, 12);
    if (!$images) {
        throw new RuntimeException('Ajoutez au moins une photo du laboratoire.');
    }
    array_push($savedFiles, ...$images);

    $columns = ['nom_laboratoire', 'nom_gerant', 'telephone', 'email', 'mot_de_passe', 'adresse', 'ville', 'site_web', 'instagram', 'description', 'statut'];
    $values = [
        $data['nom_laboratoire'], $data['nom_gerant'], $data['telephone'], $data['email'],
        password_hash($password, PASSWORD_DEFAULT), $data['adresse'], $data['ville'],
        $website !== '' ? $website : null, $instagram !== '' ? $instagram : null,
        $data['description'], $schema->status('en_attente'),
    ];
    if ($hasDocument) {
        $columns[] = 'document_verification';
        $values[] = $documentPath;
    }

    $columnSql = implode(',', array_map(static fn(string $column): string => "`$column`", $columns));
    $valueSql = implode(',', array_fill(0, count($values), '?'));
    $pdo->beginTransaction();
    $q = $pdo->prepare("INSERT INTO laboratoire ($columnSql) VALUES ($valueSql)");
    $q->execute($values);
    $labId = (int)$pdo->lastInsertId();

    $link = $pdo->prepare('INSERT INTO laboratoire_service (laboratoire_id, service_id) VALUES (?, ?)');
    foreach ($serviceIds as $serviceId) {
        $link->execute([$labId, $serviceId]);
    }

    $imageInsert = $pdo->prepare("INSERT INTO image_laboratoire (laboratoire_id, `$imagePathColumn`) VALUES (?, ?)");
    foreach ($images as $imagePath) {
        $imageInsert->execute([$labId, $imagePath]);
    }

    $pdo->commit();
    $_SESSION['register_success'] = 'Votre inscription a bien été enregistrée. Votre dossier sera vérifié par l’administration.';
    redirect('../login.php?registered=1');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    foreach ($savedFiles as $relativePath) {
        $file = dirname(__DIR__) . '/' . $relativePath;
        if (is_file($file)) {
            @unlink($file);
        }
    }
    if ($e instanceof RuntimeException) {
        $_SESSION['register_error'] = $e->getMessage();
    } else {
        error_log('DentalLink registration failed: ' . $e->getMessage());
        $_SESSION['register_error'] = 'L’inscription n’a pas pu être enregistrée. Vérifiez les informations et réessayez. Si le problème persiste, contactez l’administration.';
    }
    redirect('../register.php?error=1');
}
