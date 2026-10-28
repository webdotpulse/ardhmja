<?php
require_once __DIR__ . '/includes/db.php';

require_db($pdo);

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = (string)($_POST['current_password'] ?? '');
    $new_password = (string)($_POST['new_password'] ?? '');
    $confirm_password = (string)($_POST['confirm_password'] ?? '');

    if (!verify_csrf()) {
        $error = __('csrf_error');
    } elseif ($current_password === '' || $new_password === '' || $confirm_password === '') {
        $error = __('fill_all_fields');
    } elseif (!validate_password_strength($new_password)) {
        $error = __('weak_password');
    } elseif ($new_password !== $confirm_password) {
        $error = __('passwords_mismatch');
    } else {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($current_password, (string)$user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($update_stmt->execute([$hashed_password, $user_id])) {
                session_regenerate_id(true);
                $success = __('password_changed_success');
            } else {
                $error = __('generic_error');
            }
        } else {
            $error = __('incorrect_current_password');
        }
    }
}

$page_title = __('change_password') . ' - ' . $t_site_title;
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
        <h1><?= htmlspecialchars(__('change_password'), ENT_QUOTES, 'UTF-8') ?></h1>
        <p><a href="<?= ($_SESSION['role'] ?? '') === 'admin' ? 'admin/index.php' : 'profile.php' ?>" class="btn btn-sm btn-secondary">&larr; <?= htmlspecialchars(__('back'), ENT_QUOTES, 'UTF-8') ?></a></p>

        <div class="auth-links">
            <?php if ($error): ?><p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if ($success): ?><p class="success" role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        </div>

        <form method="POST" action="change_password.php" class="auth-form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="current_password"><?= htmlspecialchars(__('current_password_label'), ENT_QUOTES, 'UTF-8') ?></label>
                <input type="password" id="current_password" name="current_password" autocomplete="current-password" required>
            </div>
            <div class="form-group">
                <label for="new_password"><?= htmlspecialchars(__('new_password_label'), ENT_QUOTES, 'UTF-8') ?></label>
                <div class="password-input-wrap">
                    <input type="password" id="new_password" name="new_password" minlength="8" autocomplete="new-password" aria-describedby="pw-hint" required>
                    <button type="button" class="btn-password-toggle" data-target="new_password" data-show-text="<?= htmlspecialchars(__('show_password'), ENT_QUOTES, 'UTF-8') ?>" data-hide-text="<?= htmlspecialchars(__('hide_password'), ENT_QUOTES, 'UTF-8') ?>" aria-pressed="false" aria-label="<?= htmlspecialchars(__('show_password_aria'), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(__('show_password'), ENT_QUOTES, 'UTF-8') ?></button>
                </div>
                <span id="pw-hint" class="field-hint"><?= htmlspecialchars(__('password_hint'), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="form-group">
                <label for="confirm_password"><?= htmlspecialchars(__('confirm_password_label'), ENT_QUOTES, 'UTF-8') ?></label>
                <input type="password" id="confirm_password" name="confirm_password" minlength="8" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn" style="width: 100%;"><?= htmlspecialchars(__('change_password'), ENT_QUOTES, 'UTF-8') ?></button>
        </form>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="assets/js/main.js"></script>
</body>
</html>
