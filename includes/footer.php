<?php
$footer_prefix = $asset_prefix ?? ((strpos(str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '')), '/admin/') !== false) ? '../' : '');
?>
<footer class="main-footer">
    <div class="container footer-inner">
        <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($t_site_title ?? 'AsamblejaKombetare', ENT_QUOTES, 'UTF-8') ?>. <?= htmlspecialchars($t_footer_text ?? __('All rights reserved.'), ENT_QUOTES, 'UTF-8') ?></p>
        <nav class="footer-links" aria-label="Legal">
            <a href="<?= htmlspecialchars($footer_prefix, ENT_QUOTES, 'UTF-8') ?>privacy.php"><?= htmlspecialchars(__('privacy_policy'), ENT_QUOTES, 'UTF-8') ?></a>
            <span aria-hidden="true">&middot;</span>
            <a href="<?= htmlspecialchars($footer_prefix, ENT_QUOTES, 'UTF-8') ?>terms.php"><?= htmlspecialchars(__('terms_of_use'), ENT_QUOTES, 'UTF-8') ?></a>
            <span aria-hidden="true">&middot;</span>
            <a href="<?= htmlspecialchars($footer_prefix, ENT_QUOTES, 'UTF-8') ?>contact.php"><?= htmlspecialchars($t_menu_contact ?? 'Contact', ENT_QUOTES, 'UTF-8') ?></a>
        </nav>
    </div>
</footer>
