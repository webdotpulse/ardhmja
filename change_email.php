<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/mailer.php';

require_db($pdo);

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$is_admin = (($_SESSION['role'] ?? '') === 'admin');
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = trim((string)($_POST['new_email'] ?? ''));
    $current_password = (string)($_POST['current_password'] ?? '');

    if (!verify_csrf()) {
        $error = __('csrf_error');
    } elseif ($new_email === '' || $current_password === '') {
        $error = __('fill_all_fields');
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL) || mb_strlen($new_email, 'UTF-8') > 191) {
        $error = __('invalid_email');
    } else {
        $stmt = $pdo->prepare("SELECT password, username FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($current_password, (string)$user['password'])) {
            $error = __('incorrect_current_password');
        } else {
            $dup = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $dup->execute([$new_email, $user_id]);
            if ($dup->fetch()) {
                $error = __('email_taken');
            } elseif ($is_admin) {
                $upd = $pdo->prepare("UPDATE users SET email = ?, email_verified = 1, verify_token = NULL, verify_expires_at = NULL WHERE id = ?");
                if ($upd->execute([$new_email, $user_id])) {
                    $_SESSION['email'] = $new_email;
                    $success = __('email_changed_admin_success');
                } else {
                    $error = __('generic_error');
                }
            } else {
                $verify_token = bin2hex(random_bytes(32));
                $verify_expires = date('Y-m-d H:i:s', time() + 172800);
                $upd = $pdo->prepare("UPDATE users SET email = ?, email_verified = 0, verify_token = ?, verify_expires_at = ? WHERE id = ?");
                if ($upd->execute([$new_email, $verify_token, $verify_expires, $user_id])) {
                    send_verification_email_to_user(
                        $new_email,
                        $user['username'],
                        $verify_token,
                        $t_site_title,
                        $t_email_verification_subject,
                        $t_email_verification_message
                    );
                    destroy_user_session(false);
                    session_start();
                    $success = __('email_changed_user_success');
                } else {
                    $error = __('generic_error');
                }
            }
        }
    }
}

$page_title = __('change_email') . ' - ' . $t_site_title;
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
        <h1><?= htmlspecialchars(__('change_email'), ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <p><a href="<?= $is_admin ? 'admin/index.php' : 'profile.php' ?>" class="btn btn-sm btn-secondary">&larr; <?= htmlspecialchars(__('back'), ENT_QUOTES, 'UTF-8') ?></a></p>
        <?php endif; ?>

        <div class="auth-links">
            <?php if ($error): ?><p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if ($success): ?><p class="success" role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        </div>

        <?php if (!empty($_SESSION['user_id'])): ?>
            <form method="POST" action="change_email.php" class="auth-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="current_email"><?= htmlspecialchars(__('current_email_label'), ENT_QUOTES, 'UTF-8') ?></label>
                    <input type="email" id="current_email" value="<?= htmlspecialchars((string)($_SESSION['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="new_email"><?= htmlspecialchars(__('new_email_label'), ENT_QUOTES, 'UTF-8') ?></label>
                    <input type="email" id="new_email" name="new_email" maxlength="191" autocomplete="email" spellcheck="false" required>
                </div>
                <div class="form-group">
                    <label for="current_password"><?= htmlspecialchars(__('current_password_label'), ENT_QUOTES, 'UTF-8') ?></label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password" required>
                </div>
                <button type="submit" class="btn" style="width: 100%;"><?= htmlspecialchars(__('change_email'), ENT_QUOTES, 'UTF-8') ?></button>
            </form>
        <?php else: ?>
            <div class="auth-links">
                <p><a href="login.php" class="btn btn-sm"><?= htmlspecialchars(__('go_to_login'), ENT_QUOTES, 'UTF-8') ?></a></p>
            </div>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="assets/js/main.js"></script>
</body>
</html>
