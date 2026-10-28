<?php
require_once __DIR__ . '/includes/db.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: ' . (($_SESSION['role'] ?? '') === 'admin' ? 'admin/index.php' : 'profile.php'));
    exit;
}

$error = '';
$info = '';
$email_val = '';
$show_resend_cta = false;

if (($_GET['deleted'] ?? '') === '1') {
    $info = __('account_deleted_msg');
} elseif (($_GET['reset'] ?? '') === '1') {
    $info = __('reset_success');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_db($pdo);

    $email_val = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if (!verify_csrf()) {
        $error = __('csrf_error');
    } elseif (!check_rate_limit($pdo, 'login', 5, 900, $email_val)) {
        $error = __('rate_limit_error');
    } elseif ($email_val === '' || $password === '') {
        $error = __('fill_all_fields');
    } else {
        $stmt = $pdo->prepare("SELECT id, username, email, password, role, status, email_verified FROM users WHERE email = ?");
        $stmt->execute([$email_val]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Mitigate timing enumeration when user is missing
        $dummy_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $password_ok = $user
            ? password_verify($password, (string)$user['password'])
            : (password_verify($password, $dummy_hash) && false);

        if ($user && $password_ok) {
            if ($user['status'] === 'rejected') {
                record_rate_limit($pdo, 'login', $email_val);
                $error = __('login_rejected');
            } elseif ((int)$user['email_verified'] === 0) {
                $error = __('login_unverified');
                $show_resend_cta = true;
            } else {
                session_regenerate_id(true);
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['status'] = $user['status'];

                header('Location: ' . ($user['role'] === 'admin' ? 'admin/index.php' : 'profile.php'));
                exit;
            }
        } else {
            record_rate_limit($pdo, 'login', $email_val);
            $error = __('login_invalid');
        }
    }
}

$page_title = $t_login_page_title . ' - ' . $t_site_title;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <main id="main-content" class="container main-content">
        <h1 class="text-center"><?= htmlspecialchars($t_login_page_title, ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="auth-links">
            <?php if ($info): ?>
                <p class="success" role="status"><?= htmlspecialchars($info, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <?php if ($error): ?>
                <p class="error" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    <?php if ($show_resend_cta): ?>
                        <br><a href="resend_verification.php"><?= htmlspecialchars(__('resend_verification_link'), ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>

        <form method="POST" action="login.php" class="auth-form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="email"><?= htmlspecialchars(__('email_label'), ENT_QUOTES, 'UTF-8') ?></label>
                <input type="email" id="email" name="email" autocomplete="username" spellcheck="false" value="<?= htmlspecialchars($email_val, ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="form-group">
                <label for="password"><?= htmlspecialchars(__('password_label'), ENT_QUOTES, 'UTF-8') ?></label>
                <div class="password-input-wrap">
                    <input type="password" id="password" name="password" autocomplete="current-password" required>
                    <button type="button" class="btn-password-toggle" data-target="password" data-show-text="<?= htmlspecialchars(__('show_password'), ENT_QUOTES, 'UTF-8') ?>" data-hide-text="<?= htmlspecialchars(__('hide_password'), ENT_QUOTES, 'UTF-8') ?>" aria-pressed="false" aria-label="<?= htmlspecialchars(__('show_password_aria'), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(__('show_password'), ENT_QUOTES, 'UTF-8') ?></button>
                </div>
            </div>
            <button type="submit" class="btn" style="width: 100%;"><?= htmlspecialchars(__('login_btn'), ENT_QUOTES, 'UTF-8') ?></button>
        </form>

        <div class="auth-links">
            <p><a href="forgot_password.php"><?= htmlspecialchars(__('forgot_password_link'), ENT_QUOTES, 'UTF-8') ?></a></p>
            <p><?= htmlspecialchars(__('dont_have_account'), ENT_QUOTES, 'UTF-8') ?> <a href="register.php"><?= htmlspecialchars(__('register_here'), ENT_QUOTES, 'UTF-8') ?></a></p>
        </div>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
