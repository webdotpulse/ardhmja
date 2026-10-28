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
    $new_username = trim((string)($_POST['new_username'] ?? ''));

    if (!verify_csrf()) {
        $error = __('csrf_error');
    } elseif ($new_username === '') {
        $error = __('fill_all_fields');
    } elseif (!validate_username_format($new_username)) {
        $error = __('invalid_username');
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$new_username, $user_id]);

        if ($stmt->fetch()) {
            $error = __('username_taken');
        } else {
            $update_stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            if ($update_stmt->execute([$new_username, $user_id])) {
                $_SESSION['username'] = $new_username;
                $success = __('username_changed_success');
            } else {
                $error = __('generic_error');
            }
        }
    }
}

$page_title = __('change_username') . ' - ' . $t_site_title;
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
        <h1><?= htmlspecialchars(__('change_username'), ENT_QUOTES, 'UTF-8') ?></h1>
        <p><a href="<?= ($_SESSION['role'] ?? '') === 'admin' ? 'admin/index.php' : 'profile.php' ?>" class="btn btn-sm btn-secondary">&larr; <?= htmlspecialchars(__('back'), ENT_QUOTES, 'UTF-8') ?></a></p>

        <div class="auth-links">
            <?php if ($error): ?><p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if ($success): ?><p class="success" role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        </div>

        <form method="POST" action="change_username.php" class="auth-form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="new_username"><?= htmlspecialchars(__('new_username_label'), ENT_QUOTES, 'UTF-8') ?></label>
                <input type="text" id="new_username" name="new_username" minlength="3" maxlength="50" autocomplete="username" autocorrect="off" autocapitalize="off" spellcheck="false" aria-describedby="user-hint" value="<?= htmlspecialchars((string)$_SESSION['username'], ENT_QUOTES, 'UTF-8') ?>" required>
                <span id="user-hint" class="field-hint"><?= htmlspecialchars(__('username_hint'), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <button type="submit" class="btn" style="width: 100%;"><?= htmlspecialchars(__('change_username'), ENT_QUOTES, 'UTF-8') ?></button>
        </form>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="assets/js/main.js"></script>
</body>
</html>
