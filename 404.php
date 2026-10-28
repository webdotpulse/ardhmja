<?php
require_once __DIR__ . '/includes/db.php';
if (!headers_sent()) {
    http_response_code(404);
}
$page_title = __('404_title') . ' - ' . $t_site_title;
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
        <h1><?= htmlspecialchars(__('404_title'), ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars(__('404_text'), ENT_QUOTES, 'UTF-8') ?></p>
        <p style="margin-top: 24px;"><a href="index.php" class="btn"><?= htmlspecialchars(__('back_home_btn'), ENT_QUOTES, 'UTF-8') ?></a></p>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="assets/js/main.js"></script>
</body>
</html>
