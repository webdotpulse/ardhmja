<?php
if (session_status() === PHP_SESSION_NONE) {
    init_session_and_headers();
}
$nav_lang = current_lang();
?>
<a href="#main-content" class="skip-link"><?= htmlspecialchars(__('skip_to_content'), ENT_QUOTES, 'UTF-8') ?></a>
<header class="main-header">
    <div class="container header-container">
        <div class="logo">
            <a href="index.php"><span class="logo-text"><?= htmlspecialchars($t_site_title ?? 'AsamblejaKombetare', ENT_QUOTES, 'UTF-8') ?></span></a>
        </div>

        <div class="header-controls">
            <div class="lang-switcher" role="group" aria-label="<?= htmlspecialchars(__('lang_label'), ENT_QUOTES, 'UTF-8') ?>">
                <a href="<?= htmlspecialchars(lang_switch_url('sq'), ENT_QUOTES, 'UTF-8') ?>" class="lang-pill <?= $nav_lang === 'sq' ? 'active' : '' ?>" lang="sq" hreflang="sq" <?= $nav_lang === 'sq' ? 'aria-current="true"' : '' ?>>SQ</a>
                <a href="<?= htmlspecialchars(lang_switch_url('en'), ENT_QUOTES, 'UTF-8') ?>" class="lang-pill <?= $nav_lang === 'en' ? 'active' : '' ?>" lang="en" hreflang="en" <?= $nav_lang === 'en' ? 'aria-current="true"' : '' ?>>EN</a>
            </div>
            <button type="button" class="mobile-menu-toggle" aria-expanded="false" aria-controls="primary-navigation" aria-label="<?= htmlspecialchars(__('toggle_nav'), ENT_QUOTES, 'UTF-8') ?>">
                <span aria-hidden="true">☰</span>
            </button>
        </div>

        <nav id="primary-navigation" class="main-nav" aria-label="Primary">
            <ul>
                <li><a href="index.php"><?= htmlspecialchars($t_menu_home, ENT_QUOTES, 'UTF-8') ?></a></li>
                <li><a href="core_values.php"><?= htmlspecialchars($t_menu_values, ENT_QUOTES, 'UTF-8') ?></a></li>
                <li><a href="ideas.php"><?= htmlspecialchars($t_menu_ideas, ENT_QUOTES, 'UTF-8') ?></a></li>
                <li><a href="public_profiles.php"><?= htmlspecialchars($t_menu_profiles, ENT_QUOTES, 'UTF-8') ?></a></li>
                <li><a href="contact.php"><?= htmlspecialchars($t_menu_contact, ENT_QUOTES, 'UTF-8') ?></a></li>
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <li><a href="profile.php"><?= htmlspecialchars($t_menu_profile, ENT_QUOTES, 'UTF-8') ?></a></li>
                    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                        <li><a href="admin/index.php"><?= htmlspecialchars($t_menu_admin, ENT_QUOTES, 'UTF-8') ?></a></li>
                    <?php endif; ?>
                    <li>
                        <form method="POST" action="logout.php" class="inline-logout-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-logout-link">
                                <?= htmlspecialchars($t_menu_logout, ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string)($_SESSION['username'] ?? ''), ENT_QUOTES, 'UTF-8') ?>)
                            </button>
                        </form>
                    </li>
                <?php else: ?>
                    <li><a href="login.php"><?= htmlspecialchars($t_menu_login, ENT_QUOTES, 'UTF-8') ?></a></li>
                    <li><a href="register.php" class="btn btn-nav-cta"><?= htmlspecialchars($t_menu_register, ENT_QUOTES, 'UTF-8') ?></a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
