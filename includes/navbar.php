<?php
$navContext = $navContext ?? (!empty($_SESSION['id_admin']) ? 'admin' : 'laboratoire');
if ($navContext === 'admin') {
    $adminSidebarLayout = true;
    $adminPendingCount = $adminPendingCount ?? null;
    ?>
    <div class="admin-app-shell">
        <?php require __DIR__ . '/admin-sidebar.php'; ?>
        <div class="admin-app-main">
            <header class="admin-dashboard-topbar">
                <form class="dashboard-search" action="laboratoires.php" method="get" role="search">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
                    <input type="search" name="q" placeholder="Rechercher un laboratoire, un service…" aria-label="Rechercher un laboratoire">
                </form>
                <div class="topbar-actions">
                    <a class="notification-button" href="validation.php" aria-label="Demandes en attente">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9m-8 4h4m-3 5a2 2 0 0 0 4 0"/></svg>
                        <?php if ((int)($adminPendingCount ?? 0) > 0): ?><i></i><?php endif; ?>
                    </a>
                    <div class="account-chip">
                        <span class="account-avatar"><?= htmlspecialchars(strtoupper(substr((string)($_SESSION['admin_email'] ?? 'A'), 0, 1)), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
                        <span class="account-copy"><strong><?= htmlspecialchars((string)($_SESSION['admin_email'] ?? 'Administrateur'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></strong><small>Administrateur</small></span>
                    </div>
                </div>
            </header>
<?php
} else {
    $navigation = [
        ['dashboard.php', 'Tableau de bord'],
        ['profile.php', 'Profil et services'],
        ['images.php', 'Photos'],
        ['upload-document.php', 'Document'],
        ['../logout.php', 'Déconnexion'],
    ];
    ?>
    <header class="private-header">
        <a class="private-brand lab-private-brand" href="dashboard.php" aria-label="DentalLink Morocco, tableau de bord"><img src="../assets/images/dental_link.png" alt="DentalLink Morocco"></a>
        <nav class="private-navbar" aria-label="Navigation de l’espace privé">
            <?php foreach ($navigation as [$href, $label]): ?>
                <a href="<?= htmlspecialchars($href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"<?= basename($_SERVER['SCRIPT_NAME'] ?? '') === basename($href) ? ' class="active" aria-current="page"' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></a>
            <?php endforeach; ?>
        </nav>
    </header>
<?php } ?>