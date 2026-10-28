<?php
require_once __DIR__ . '/includes/db.php';

require_db($pdo);

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$error = '';
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_pending_idea') {
    if (!verify_csrf()) {
        $error = __('csrf_error');
    } else {
        $idea_id = (int)($_POST['idea_id'] ?? 0);
        if ($idea_id > 0) {
            $del = $pdo->prepare("DELETE FROM ideas WHERE id = ? AND user_id = ? AND status = 'pending'");
            $del->execute([$idea_id, $user_id]);
            header('Location: profile.php?msg=idea_deleted');
            exit;
        }
    }
}

if (($_GET['msg'] ?? '') === 'idea_deleted') {
    $flash = __('idea_deleted_flash');
}

// Fetch user ideas
$stmt = $pdo->prepare("SELECT id, title, status, created_at FROM ideas WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$ideas = $stmt->fetchAll();

// Fetch agreed Core Values (bilingual)
$stmt = $pdo->prepare("
    SELECT cv.title, cv.title_sq
    FROM agreements a
    JOIN core_values cv ON a.value_id = cv.id
    WHERE a.user_id = ?
    ORDER BY cv.created_at ASC
");
$stmt->execute([$user_id]);
$agreements = $stmt->fetchAll();

$lang = current_lang();
$status = (string)($_SESSION['status'] ?? 'pending');
$page_title = $t_menu_profile . ' - ' . $t_site_title;
$robots_meta = 'noindex';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <main id="main-content" class="container main-content">
        <div class="profile-header">
            <h1><?= htmlspecialchars(__('welcome_user', ['username' => $_SESSION['username']]), ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="profile-actions">
                <a href="change_username.php" class="btn btn-sm btn-secondary"><?= htmlspecialchars(__('change_username'), ENT_QUOTES, 'UTF-8') ?></a>
                <a href="change_email.php" class="btn btn-sm btn-secondary"><?= htmlspecialchars(__('change_email'), ENT_QUOTES, 'UTF-8') ?></a>
                <a href="change_password.php" class="btn btn-sm btn-secondary"><?= htmlspecialchars(__('change_password'), ENT_QUOTES, 'UTF-8') ?></a>
                <a href="delete_account.php" class="btn btn-sm btn-danger"><?= htmlspecialchars(__('delete_account'), ENT_QUOTES, 'UTF-8') ?></a>
            </div>
        </div>

        <?php if ($error): ?>
            <p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <?php if ($flash): ?>
            <p class="success" role="status"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <div class="status-box <?= $status === 'approved' ? 'status-approved' : 'status-pending' ?>">
            <?= htmlspecialchars(__('account_status_label'), ENT_QUOTES, 'UTF-8') ?>
            <strong><?= htmlspecialchars(translate_status($status), ENT_QUOTES, 'UTF-8') ?></strong>
            <?php if ($status === 'pending'): ?>
                <p style="margin-bottom:0;"><?= htmlspecialchars(__('pending_account_notice'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <div class="profile-sections">
            <section class="section">
                <div class="flex-header" style="margin-bottom: 12px;">
                    <h2 style="margin:0;"><?= htmlspecialchars(__('my_submitted_ideas'), ENT_QUOTES, 'UTF-8') ?></h2>
                    <?php if ($status === 'approved'): ?>
                        <a href="submit_idea.php" class="btn btn-sm"><?= htmlspecialchars(__('submit_new_idea'), ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endif; ?>
                </div>

                <?php if (empty($ideas)): ?>
                    <p><?= htmlspecialchars(__('no_user_ideas'), ENT_QUOTES, 'UTF-8') ?></p>
                <?php else: ?>
                    <ul class="idea-list">
                        <?php foreach ($ideas as $idea): ?>
                            <li>
                                <strong><?= htmlspecialchars($idea['title'], ENT_QUOTES, 'UTF-8') ?></strong> —
                                <span class="badge badge-<?= htmlspecialchars($idea['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(translate_status($idea['status']), ENT_QUOTES, 'UTF-8') ?></span>
                                <small class="text-muted">(<?= htmlspecialchars(format_date_localized($idea['created_at']), ENT_QUOTES, 'UTF-8') ?>)</small>
                                <?php if ($idea['status'] === 'pending'): ?>
                                    <form method="POST" action="profile.php" style="display:inline; margin-inline-start:6px;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete_pending_idea">
                                        <input type="hidden" name="idea_id" value="<?= (int)$idea['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" data-confirm="<?= htmlspecialchars(__('confirm_delete'), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(__('delete_pending_idea'), ENT_QUOTES, 'UTF-8') ?></button>
                                    </form>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <section class="section">
                <h2 style="margin-top:0;"><?= htmlspecialchars(__('agreed_values_heading'), ENT_QUOTES, 'UTF-8') ?></h2>
                <?php if (empty($agreements)): ?>
                    <p><?= htmlspecialchars(__('no_agreed_values'), ENT_QUOTES, 'UTF-8') ?> <a href="core_values.php"><?= htmlspecialchars($t_view_values_btn, ENT_QUOTES, 'UTF-8') ?></a></p>
                <?php else: ?>
                    <ul class="agreement-list">
                        <?php foreach ($agreements as $agreement):
                            $ag_title = ($lang === 'sq' && !empty(trim((string)($agreement['title_sq'] ?? ''))))
                                ? $agreement['title_sq']
                                : $agreement['title'];
                        ?>
                            <li><?= htmlspecialchars((string)$ag_title, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </div>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="assets/js/main.js"></script>
</body>
</html>
