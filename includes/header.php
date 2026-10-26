<?php
$pageTitle = $pageTitle ?? 'DentalLink Morocco';
$pageDescription = $pageDescription ?? 'Réseau professionnel des laboratoires dentaires au Maroc.';
$assetPrefix = $assetPrefix ?? '';
$pageStyles = $pageStyles ?? [];
$loadInterFont = $loadInterFont ?? false;
$bodyClass = $bodyClass ?? '';
$escapeHead = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= $escapeHead($pageDescription) ?>">
    <title><?= $escapeHead($pageTitle) ?></title>
    <?php if ($loadInterFont): ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php endif; ?>
    <?php foreach ($pageStyles as $stylesheet): ?>
    <link rel="stylesheet" href="<?= $escapeHead($assetPrefix . ltrim($stylesheet, '/')) ?>">
    <?php endforeach; ?>
    <?php if (in_array('assets/css/admin.css', $pageStyles, true)): ?>
    <link rel="stylesheet" href="<?= $escapeHead($assetPrefix . 'assets/css/admin-brand.css') ?>">
    <?php if (!in_array('assets/css/admin-dashboard-layout.css', $pageStyles, true)): ?><link rel="stylesheet" href="<?= $escapeHead($assetPrefix . 'assets/css/admin-dashboard-layout.css') ?>"><?php endif; ?>
    <link rel="stylesheet" href="<?= $escapeHead($assetPrefix . 'assets/css/admin-shared-layout.css') ?>">
    <link rel="stylesheet" href="<?= $escapeHead($assetPrefix . 'assets/css/admin-confirm-dialog.css') ?>">
    <?php endif; ?>
    <?php if (!empty($pageStyles) && in_array('assets/css/admin.css', $pageStyles, true)): ?><script>document.documentElement.dataset.theme='dark'</script><?php endif; ?>
</head>
<body<?= $bodyClass !== '' ? ' class="' . $escapeHead($bodyClass) . '"' : '' ?>>
