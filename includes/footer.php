<?php if (!empty($adminSidebarLayout)): ?></div></div><?php endif; ?>
<footer class="private-footer"><span>DentalLink Morocco</span><span>Espace sécurisé</span></footer>
<?php if (!empty($pageScripts)): foreach ($pageScripts as $script): ?><script src="<?= htmlspecialchars($assetPrefix . ltrim($script, '/'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" defer></script><?php endforeach; endif; ?>
</body>
</html>
