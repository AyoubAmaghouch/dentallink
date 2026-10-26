<?php
$adminSidebarActive = basename($_SERVER['SCRIPT_NAME'] ?? 'dashboard.php');
$adminPendingCount = $adminPendingCount ?? null;
if ($adminPendingCount === null && isset($pdo)) {
    $sidebarSchema = dental_schema($pdo);
    $sidebarQuery = $pdo->prepare('SELECT COUNT(*) FROM laboratoire WHERE statut=?');
    $sidebarQuery->execute([$sidebarSchema->status('en_attente')]);
    $adminPendingCount = (int)$sidebarQuery->fetchColumn();
}
$adminPendingCount = (int)($adminPendingCount ?? 0);
$adminNavigation = [
    ['dashboard.php', 'Vue d’ensemble', 'home'],
    ['laboratoires.php', 'Laboratoires', 'labs'],
    ['validation.php', 'Validations', 'check'],
    ['services.php', 'Services', 'services'],
    ['photos.php', 'Photos', 'photos'],
    ['logout.php', 'Déconnexion', 'logout'],
];
?>
<aside class="admin-app-sidebar">
    <a class="app-brand" href="dashboard.php" aria-label="DentalLink Morocco, administration">
        <img class="brand-logo" src="../assets/images/dental_link.png" alt="DentalLink Morocco">
        <span class="brand-admin-label">Administration</span>
    </a>
    <nav class="app-navigation" aria-label="Navigation d’administration">
        <?php foreach ($adminNavigation as [$href, $label, $icon]): ?>
        <a class="app-nav-link<?= $adminSidebarActive === $href ? ' is-active' : '' ?><?= $icon === 'logout' ? ' app-nav-logout' : '' ?>" href="<?= htmlspecialchars($href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"<?= $adminSidebarActive === $href ? ' aria-current="page"' : '' ?>>
            <?php if ($icon === 'home'): ?><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></svg>
            <?php elseif ($icon === 'labs'): ?><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m4 0h1m-6 4h1m4 0h1m-6 8v-4h4v4"/></svg>
            <?php elseif ($icon === 'check'): ?><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="m7 12 3 3 7-7"/></svg>
            <?php elseif ($icon === 'services'): ?><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 2 9 5v10l-9 5-9-5V7z"/><path d="m3 7 9 5 9-5m-9 5v10"/></svg>
            <?php elseif ($icon === 'photos'): ?><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
            <?php else: ?><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3m9-9h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/></svg><?php endif; ?>
            <span><?= htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
            <?php if ($icon === 'check' && $adminPendingCount > 0): ?><span class="nav-count"><?= $adminPendingCount ?></span><?php endif; ?>
        </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-promo">
        <span class="promo-tooth" aria-hidden="true">✦</span>
        <strong>DentalLink Morocco</strong>
        <p>Connectons les laboratoires dentaires avec plus d’opportunités.</p>
        <a href="../index.php" aria-label="Visiter DentalLink">↗</a>
    </div>
</aside>
