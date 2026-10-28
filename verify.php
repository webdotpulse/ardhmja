<?php
require_once __DIR__ . '/includes/db.php';

require_db($pdo);

$error = '';
$success = '';

$token = trim((string)($_GET['token'] ?? ''));
if ($token !== '' && strlen($token) <= 128) {
    $stmt = $pdo->prepare("SELECT id, verify_expires_at FROM users WHERE verify_token = ? AND email_verified = 0");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $expired = !empty($user['verify_expires_at']) && strtotime((string)$user['verify_expires_at']) < time();
        if ($expired) {
            $error = __('verify_invalid');
        } else {
            $updateStmt = $pdo->prepare("UPDATE users SET email_verified = 1, verify_token = NULL, verify_expires_at = NULL WHERE id = ?");
            if ($updateStmt->execute([(int)$user['id']])) {
                $success = __('verify_success');
            } else {
                $error = __('generic_error');
            }
        }
    } else {
        $error = __('verify_invalid');
    }
} else {
    $error = __('verify_missing');
}

$page_title = __('verify_title') . ' - ' . $t_site_title;
$robots_meta = 'noindex';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <main id="main-content" class="container main-content">
        <h1><?= htmlspecialchars(__('verify_title'), ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if ($error): ?>
            <div class="status-box status-pending" role="alert">
                <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                <p><a href="resend_verification.php" class="btn btn-sm"><?= htmlspecialchars(__('resend_btn'), ENT_QUOTES, 'UTF-8') ?></a></p>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="status-box status-approved" role="status">
                <p><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
                <p><a href="login.php" class="btn"><?= htmlspecialchars(__('go_to_login'), ENT_QUOTES, 'UTF-8') ?></a></p>
            </div>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
