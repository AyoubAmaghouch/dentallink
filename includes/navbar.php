<?php
$navContext = $navContext ?? (!empty($_SESSION['id_admin']) ? 'admin' : 'laboratoire');
if ($navContext === 'admin') {
    $navigation = [
        ['dashboard.php', 'Vue d’ensemble'],
        ['laboratoires.php', 'Laboratoires'],
        ['validation.php', 'Validations'],
        ['services.php', 'Services'],
        ['photos.php', 'Photos'],
        ['logout.php', 'Déconnexion'],
    ];
} else {
    $navigation = [
        ['dashboard.php', 'Tableau de bord'],
        ['profile.php', 'Profil et services'],
        ['images.php', 'Photos'],
        ['upload-document.php', 'Document'],
        ['../logout.php', 'Déconnexion'],
    ];
}
?>
<header class="private-header">
    <?php if ($navContext === 'admin'): ?><a class="private-brand" href="dashboard.php"><span class="private-brand-mark">D</span><span>DentalLink<small>ADMINISTRATION</small></span></a><?php endif; ?>
    <?php if ($navContext === 'laboratoire' && empty($dashboardNavbarShell)): ?><a class="private-brand lab-private-brand" href="dashboard.php" aria-label="DentalLink Morocco, tableau de bord"><img src="../assets/images/dental_link.png" alt="DentalLink Morocco"></a><?php endif; ?>
    <nav class="private-navbar" aria-label="Navigation de l’espace privé">
        <?php foreach ($navigation as [$href, $label]): ?>
            <a href="<?= htmlspecialchars($href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"<?= $navContext === 'laboratoire' && basename($_SERVER['SCRIPT_NAME'] ?? '') === basename($href) ? ' class="active" aria-current="page"' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></a>
        <?php endforeach; ?>
    </nav>
    <?php if ($navContext === 'admin'): ?><div class="private-user"><span class="admin-avatar">A</span><span><?= htmlspecialchars((string)($_SESSION['admin_email'] ?? 'Administrateur'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span><button type="button" class="theme-toggle" data-theme-toggle aria-label="Changer de thème">☼</button></div><?php endif; ?>
</header>
