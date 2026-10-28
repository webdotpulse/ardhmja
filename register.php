<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/mailer.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$error = '';
$success = '';
$username_val = '';
$email_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_db($pdo);

    $username_val = trim((string)($_POST['username'] ?? ''));
    $email_val = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $consent = !empty($_POST['privacy_consent']);

    if (!verify_csrf()) {
        $error = __('csrf_error');
    } elseif (!verify_honeypot()) {
        $success = __('register_success');
    } elseif (!check_rate_limit($pdo, 'register', 5, 3600)) {
        $error = __('rate_limit_error');
    } elseif ($username_val === '' || $email_val === '' || $password === '') {
        $error = __('fill_all_fields');
    } elseif (!validate_username_format($username_val)) {
        $error = __('invalid_username');
    } elseif (!filter_var($email_val, FILTER_VALIDATE_EMAIL) || mb_strlen($email_val, 'UTF-8') > 191) {
        $error = __('invalid_email');
    } elseif (!validate_password_strength($password)) {
        $error = __('weak_password');
    } elseif (!$consent) {
        $error = __('consent_required');
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username_val, $email_val]);
        if ($stmt->fetch()) {
            record_rate_limit($pdo, 'register');
            $error = __('user_or_email_exists');
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $verify_token = bin2hex(random_bytes(32));
            $verify_expires = date('Y-m-d H:i:s', time() + 172800);
            $consent_time = date('Y-m-d H:i:s');

            $stmt = $pdo->prepare("
                INSERT INTO users (username, password, email, verify_token, verify_expires_at, role, status, privacy_accepted_at)
                VALUES (?, ?, ?, ?, ?, 'user', 'pending', ?)
            ");

            if ($stmt->execute([$username_val, $hashed_password, $email_val, $verify_token, $verify_expires, $consent_time])) {
                record_rate_limit($pdo, 'register');

                $sent = send_verification_email_to_user(
                    $email_val,
                    $username_val,
                    $verify_token,
                    $t_site_title,
                    $t_email_verification_subject,
                    $t_email_verification_message
                );

                $admin_link = app_base_url() . '/admin/users.php?status=pending';
                notify_admins(
                    $pdo,
                    "[{$t_site_title}] New member registration: {$username_val}",
                    "A new user has registered on {$t_site_title} and is awaiting approval:\n\nUsername: {$username_val}\nEmail: {$email_val}\n\nReview in Admin Panel:\n{$admin_link}"
                );

                if ($sent) {
                    $success = __('register_success');
                } else {
                    $error = __('register_mail_fallback');
                }
                $username_val = '';
                $email_val = '';
            } else {
                $error = __('generic_error');
            }
        }
    }
}

$page_title = $t_register_page_title . ' - ' . $t_site_title;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <main id="main-content" class="container main-content">
        <h1 class="text-center"><?= htmlspecialchars($t_register_page_title, ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if ($error): ?>
            <div class="auth-links"><p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="auth-links">
                <p class="success" role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
                <p><a href="login.php" class="btn btn-sm"><?= htmlspecialchars(__('go_to_login'), ENT_QUOTES, 'UTF-8') ?></a></p>
            </div>
        <?php else: ?>
            <form method="POST" action="register.php" class="auth-form">
                <?= csrf_field() ?>
                <?= honeypot_field() ?>
                <div class="form-group">
                    <label for="username"><?= htmlspecialchars(__('username_label'), ENT_QUOTES, 'UTF-8') ?></label>
                    <input type="text" id="username" name="username" minlength="3" maxlength="50" autocomplete="username" autocorrect="off" autocapitalize="off" spellcheck="false" aria-describedby="username-hint" value="<?= htmlspecialchars($username_val, ENT_QUOTES, 'UTF-8') ?>" required>
                    <span id="username-hint" class="field-hint"><?= htmlspecialchars(__('username_hint'), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="form-group">
                    <label for="email"><?= htmlspecialchars(__('email_label'), ENT_QUOTES, 'UTF-8') ?></label>
                    <input type="email" id="email" name="email" maxlength="191" autocomplete="email" spellcheck="false" value="<?= htmlspecialchars($email_val, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group">
                    <label for="password"><?= htmlspecialchars(__('password_label'), ENT_QUOTES, 'UTF-8') ?></label>
                    <div class="password-input-wrap">
                        <input type="password" id="password" name="password" minlength="8" autocomplete="new-password" aria-describedby="password-hint" required>
                        <button type="button" class="btn-password-toggle" data-target="password" data-show-text="<?= htmlspecialchars(__('show_password'), ENT_QUOTES, 'UTF-8') ?>" data-hide-text="<?= htmlspecialchars(__('hide_password'), ENT_QUOTES, 'UTF-8') ?>" aria-pressed="false" aria-label="<?= htmlspecialchars(__('show_password_aria'), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(__('show_password'), ENT_QUOTES, 'UTF-8') ?></button>
                    </div>
                    <span id="password-hint" class="field-hint"><?= htmlspecialchars(__('password_hint'), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="form-group checkbox-group">
                    <label for="privacy_consent">
                        <input type="checkbox" id="privacy_consent" name="privacy_consent" value="1" required>
                        <span><?= __('consent_checkbox_html') ?></span>
                    </label>
                </div>
                <button type="submit" class="btn" style="width: 100%;"><?= htmlspecialchars(__('register_btn'), ENT_QUOTES, 'UTF-8') ?></button>
            </form>
        <?php endif; ?>
        <div class="auth-links">
            <p><?= htmlspecialchars(__('already_have_account'), ENT_QUOTES, 'UTF-8') ?> <a href="login.php"><?= htmlspecialchars(__('login_here'), ENT_QUOTES, 'UTF-8') ?></a></p>
            <p><a href="resend_verification.php"><?= htmlspecialchars(__('resend_verification_link'), ENT_QUOTES, 'UTF-8') ?></a></p>
        </div>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="assets/js/main.js"></script>
</body>
</html>
