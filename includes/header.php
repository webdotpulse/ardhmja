<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="main-header">
    <div class="container header-container">
        <div class="logo">
            <a href="index.php"><h1><?= htmlspecialchars($t_site_title ?? 'Ardhmja') ?></h1></a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php"><?= htmlspecialchars($t_menu_home) ?></a></li>
                <li><a href="points.php"><?= htmlspecialchars($t_menu_points) ?></a></li>
                <li><a href="ideas.php"><?= htmlspecialchars($t_menu_ideas) ?></a></li>
                <li><a href="public_profiles.php"><?= htmlspecialchars($t_menu_profiles) ?></a></li>
                <li><a href="contact.php"><?= htmlspecialchars($t_menu_contact) ?></a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php"><?= htmlspecialchars($t_menu_profile) ?></a></li>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="admin/index.php"><?= htmlspecialchars($t_menu_admin) ?></a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="btn-logout"><?= htmlspecialchars($t_menu_logout) ?> (<?= htmlspecialchars($_SESSION['username']) ?>)</a></li>
                <?php else: ?>
                    <li><a href="login.php"><?= htmlspecialchars($t_menu_login) ?></a></li>
                    <li><a href="register.php" class="btn"><?= htmlspecialchars($t_menu_register) ?></a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
