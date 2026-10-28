<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/mailer.php';

require_db($pdo);

if (empty($_SESSION['user_id']) || ($_SESSION['status'] ?? '') !== 'approved') {
    header('Location: profile.php');
    exit;
}

$error = '';
$success = '';
$title_val = '';
$desc_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title_val = trim((string)($_POST['title'] ?? ''));
    $desc_val = trim((string)($_POST['description'] ?? ''));

    if (!verify_csrf()) {
        $error = __('csrf_error');
    } elseif (!check_rate_limit($pdo, 'submit_idea', 10, 3600, (string)$_SESSION['user_id'])) {
        $error = __('rate_limit_error');
    } elseif ($title_val === '' || $desc_val === '') {
        $error = __('fill_all_fields');
    } elseif (mb_strlen($title_val, 'UTF-8') < 5 || mb_strlen($title_val, 'UTF-8') > 200 || mb_strlen($desc_val, 'UTF-8') < 20 || mb_strlen($desc_val, 'UTF-8') > 5000) {
        $error = __('idea_length_error');
    } else {
        $stmt = $pdo->prepare("INSERT INTO ideas (user_id, title, description, status) VALUES (?, ?, ?, 'pending')");
        if ($stmt->execute([(int)$_SESSION['user_id'], $title_val, $desc_val])) {
            record_rate_limit($pdo, 'submit_idea', (string)$_SESSION['user_id']);
            $success = __('idea_submitted_success');

            $admin_link = app_base_url() . '/admin/ideas.php?status=pending';
            notify_admins(
                $pdo,
                "[{$t_site_title}] New idea submitted: {$title_val}",
                "Member {$_SESSION['username']} submitted a new idea awaiting moderation:\n\nTitle: {$title_val}\n\n{$desc_val}\n\nReview in Admin Panel:\n{$admin_link}"
            );
            $title_val = '';
            $desc_val = '';
        } else {
            $error = __('idea_submitted_error');
        }
    }
}

$page_title = $t_submit_idea_btn . ' - ' . $t_site_title;
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
        <h1><?= htmlspecialchars($t_submit_idea_btn, ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars(__('submit_idea_intro'), ENT_QUOTES, 'UTF-8') ?></p>

        <?php if ($error): ?>
            <p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success" role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
            <p><a href="profile.php" class="btn btn-sm"><?= htmlspecialchars(__('return_to_profile'), ENT_QUOTES, 'UTF-8') ?></a></p>
        <?php else: ?>
            <form method="POST" action="submit_idea.php" class="auth-form form-wide">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="idea_title"><?= htmlspecialchars(__('idea_title_label'), ENT_QUOTES, 'UTF-8') ?></label>
                    <input type="text" id="idea_title" name="title" minlength="5" maxlength="200" value="<?= htmlspecialchars($title_val, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group">
                    <label for="idea_description"><?= htmlspecialchars(__('idea_desc_label'), ENT_QUOTES, 'UTF-8') ?></label>
                    <textarea id="idea_description" name="description" rows="6" minlength="20" maxlength="5000" required><?= htmlspecialchars($desc_val, ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <button type="submit" class="btn"><?= htmlspecialchars($t_submit_idea_btn, ENT_QUOTES, 'UTF-8') ?></button>
            </form>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
