<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/mailer.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$error = '';
$success = '';
$email_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_db($pdo);

    $email_val = trim((string)($_POST['email'] ?? ''));

    if (!verify_csrf()) {
        $error = __('csrf_error');
    } elseif (!verify_honeypot()) {
        $success = __('resend_sent_generic');
    } elseif (!check_rate_limit($pdo, 'resend_verify', 3, 1800, $email_val)) {
        $error = __('rate_limit_error');
    } elseif (!filter_var($email_val, FILTER_VALIDATE_EMAIL)) {
        $error = __('invalid_email');
    } else {
        record_rate_limit($pdo, 'resend_verify', $email_val);

        $stmt = $pdo->prepare("SELECT id, username, email, email_verified, status FROM users WHERE email = ?");
        $stmt->execute([$email_val]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && (int)$user['email_verified'] === 0 && $user['status'] !== 'rejected') {
            $new_token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 172800);
            $upd = $pdo->prepare("UPDATE users SET verify_token = ?, verify_expires_at = ? WHERE id = ?");
            if ($upd->execute([$new_token, $expires, (int)$user['id']])) {
                send_verification_email_to_user(
                    $user['email'],
                    $user['username'],
                    $new_token,
                    $t_site_title,
                    $t_email_verification_subject,
                    $t_email_verification_message
                );
            }
        }

        $success = __('resend_sent_generic');
        $email_val = '';
    }
}

$page_title = __('resend_title') . ' - ' . $t_site_title;
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
        <h1 class="text-center"><?= htmlspecialchars(__('resend_title'), ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="auth-links">
            <p><?= htmlspecialchars(__('resend_intro'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php if ($error): ?>
                <p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="success" role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <form method="POST" action="resend_verification.php" class="auth-form">
            <?= csrf_field() ?>
            <?= honeypot_field() ?>
            <div class="form-group">
                <label for="email"><?= htmlspecialchars(__('email_label'), ENT_QUOTES, 'UTF-8') ?></label>
                <input type="email" id="email" name="email" autocomplete="email" spellcheck="false" value="<?= htmlspecialchars($email_val, ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <button type="submit" class="btn" style="width: 100%;"><?= htmlspecialchars(__('resend_btn'), ENT_QUOTES, 'UTF-8') ?></button>
        </form>

        <div class="auth-links">
            <p><a href="login.php">&larr; <?= htmlspecialchars(__('go_to_login'), ENT_QUOTES, 'UTF-8') ?></a></p>
        </div>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
