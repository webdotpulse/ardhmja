<?php
$current_script = basename((string)($_SERVER['SCRIPT_NAME'] ?? ''));
?>
<aside class="admin-sidebar" aria-label="Admin Navigation">
    <h2><?= htmlspecialchars($t_site_title ?? 'AsamblejaKombetare', ENT_QUOTES, 'UTF-8') ?></h2>
    <ul class="admin-nav">
        <li><a href="index.php" class="<?= $current_script === 'index.php' ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="users.php" class="<?= in_array($current_script, ['users.php', 'edit_user.php'], true) ? 'active' : '' ?>">Manage Users</a></li>
        <li><a href="ideas.php" class="<?= in_array($current_script, ['ideas.php', 'edit_idea.php'], true) ? 'active' : '' ?>">Manage Ideas</a></li>
        <li><a href="core_values.php" class="<?= in_array($current_script, ['core_values.php', 'edit_value.php'], true) ? 'active' : '' ?>">Core Values</a></li>
        <li><a href="messages.php" class="<?= $current_script === 'messages.php' ? 'active' : '' ?>">Messages</a></li>
        <li><a href="settings.php" class="<?= $current_script === 'settings.php' ? 'active' : '' ?>">Site Settings (EN / SQ)</a></li>
        <li><a href="../index.php">View Public Site &rarr;</a></li>
        <li>
            <form method="POST" action="../logout.php" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit"><?= htmlspecialchars($t_menu_logout ?? 'Logout', ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string)($_SESSION['username'] ?? ''), ENT_QUOTES, 'UTF-8') ?>)</button>
            </form>
        </li>
    </ul>
</aside>
