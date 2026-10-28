<?php
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    destroy_user_session(true);
    header('Location: index.php');
    exit;
}

if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$page_title = $t_menu_logout . ' - ' . $t_site_title;
$robots_meta = 'noindex';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <main id="main-content" class="container main-content text-center">
        <h1><?= htmlspecialchars($t_menu_logout, ENT_QUOTES, 'UTF-8') ?></h1>
        <form method="POST" action="logout.php" class="auth-form">
            <?= csrf_field() ?>
            <button type="submit" class="btn" style="width: 100%;"><?= htmlspecialchars($t_menu_logout, ENT_QUOTES, 'UTF-8') ?></button>
        </form>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
