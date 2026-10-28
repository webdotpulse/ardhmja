<?php
require_once __DIR__ . '/includes/db.php';

require_db($pdo);

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = (string)($_POST['current_password'] ?? '');

    if (!verify_csrf()) {
        $error = __('csrf_error');
    } elseif ($current_password === '') {
        $error = __('fill_all_fields');
    } else {
        $stmt = $pdo->prepare("SELECT password, role, profile_picture FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($current_password, (string)$user['password'])) {
            $error = __('incorrect_current_password');
        } else {
            if ($user['role'] === 'admin') {
                $admin_count = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND status = 'approved'")->fetchColumn();
                if ($admin_count <= 1) {
                    $error = __('delete_last_admin_error');
                }
            }

            if ($error === '') {
                delete_uploaded_avatar($user['profile_picture'] ?? null, __DIR__ . '/uploads');
                $del = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $del->execute([$user_id]);
                destroy_user_session(true);
                header('Location: login.php?deleted=1');
                exit;
            }
        }
    }
}

$page_title = __('delete_account_title') . ' - ' . $t_site_title;
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
        <h1><?= htmlspecialchars(__('delete_account_title'), ENT_QUOTES, 'UTF-8') ?></h1>
        <p><a href="profile.php" class="btn btn-sm btn-secondary">&larr; <?= htmlspecialchars(__('back'), ENT_QUOTES, 'UTF-8') ?></a></p>

        <div class="warning-banner" role="note">
            <p style="margin:0;"><?= htmlspecialchars(__('delete_account_warning'), ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <?php if ($error): ?>
            <div class="auth-links"><p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p></div>
        <?php endif; ?>

        <form method="POST" action="delete_account.php" class="auth-form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="current_password"><?= htmlspecialchars(__('delete_account_confirm_label'), ENT_QUOTES, 'UTF-8') ?></label>
                <input type="password" id="current_password" name="current_password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-danger" style="width: 100%;" data-confirm="<?= htmlspecialchars(__('confirm_delete'), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(__('delete_account_btn'), ENT_QUOTES, 'UTF-8') ?></button>
        </form>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
