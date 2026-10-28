<?php
require_once __DIR__ . '/includes/db.php';

require_db($pdo);

$raw_token = trim((string)($_GET['token'] ?? $_POST['token'] ?? ''));
$error = '';
$valid_user = null;

if ($raw_token !== '' && strlen($raw_token) === 64 && ctype_xdigit($raw_token)) {
    $token_hash = hash('sha256', $raw_token);
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE reset_token = ? AND reset_expires_at > ?");
    $stmt->execute([$token_hash, date('Y-m-d H:i:s')]);
    $valid_user = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

if (!$valid_user) {
    $error = __('reset_invalid_token');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = (string)($_POST['new_password'] ?? '');
    $confirm_password = (string)($_POST['confirm_password'] ?? '');

    if (!verify_csrf()) {
        $error = __('csrf_error');
    } elseif ($new_password === '' || $confirm_password === '') {
        $error = __('fill_all_fields');
    } elseif (!validate_password_strength($new_password)) {
        $error = __('weak_password');
    } elseif ($new_password !== $confirm_password) {
        $error = __('passwords_mismatch');
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $upd = $pdo->prepare("
            UPDATE users
            SET password = ?, reset_token = NULL, reset_expires_at = NULL, email_verified = 1, verify_token = NULL, verify_expires_at = NULL
            WHERE id = ?
        ");
        if ($upd->execute([$hashed, (int)$valid_user['id']])) {
            header('Location: login.php?reset=1');
            exit;
        }
        $error = __('generic_error');
    }
}

$page_title = __('reset_title') . ' - ' . $t_site_title;
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
        <h1 class="text-center"><?= htmlspecialchars(__('reset_title'), ENT_QUOTES, 'UTF-8') ?></h1>

        <div class="auth-links">
            <?php if ($error): ?>
                <p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <?php if ($valid_user): ?>
            <form method="POST" action="reset_password.php" class="auth-form">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($raw_token, ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-group">
                    <label for="new_password"><?= htmlspecialchars(__('new_password_label'), ENT_QUOTES, 'UTF-8') ?></label>
                    <div class="password-input-wrap">
                        <input type="password" id="new_password" name="new_password" minlength="8" autocomplete="new-password" aria-describedby="reset-pw-hint" required>
                        <button type="button" class="btn-password-toggle" data-target="new_password" data-show-text="<?= htmlspecialchars(__('show_password'), ENT_QUOTES, 'UTF-8') ?>" data-hide-text="<?= htmlspecialchars(__('hide_password'), ENT_QUOTES, 'UTF-8') ?>" aria-pressed="false" aria-label="<?= htmlspecialchars(__('show_password_aria'), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(__('show_password'), ENT_QUOTES, 'UTF-8') ?></button>
                    </div>
                    <span id="reset-pw-hint" class="field-hint"><?= htmlspecialchars(__('password_hint'), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="form-group">
                    <label for="confirm_password"><?= htmlspecialchars(__('confirm_password_label'), ENT_QUOTES, 'UTF-8') ?></label>
                    <input type="password" id="confirm_password" name="confirm_password" minlength="8" autocomplete="new-password" required>
                </div>
                <button type="submit" class="btn" style="width: 100%;"><?= htmlspecialchars(__('reset_btn'), ENT_QUOTES, 'UTF-8') ?></button>
            </form>
        <?php else: ?>
            <div class="auth-links">
                <p><a href="forgot_password.php" class="btn btn-sm"><?= htmlspecialchars(__('forgot_btn'), ENT_QUOTES, 'UTF-8') ?></a></p>
            </div>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="assets/js/main.js"></script>
</body>
</html>
