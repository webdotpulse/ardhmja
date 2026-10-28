<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/admin_auth.php';

$idea_id = (int)($_GET['id'] ?? 0);
if ($idea_id <= 0) {
    header('Location: ideas.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = __('csrf_error');
    } else {
        $title = trim((string)($_POST['title'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));

        if ($title === '' || $description === '') {
            $error = 'Title and description cannot be empty.';
        } else {
            $stmt = $pdo->prepare("UPDATE ideas SET title = ?, description = ? WHERE id = ?");
            if ($stmt->execute([$title, $description, $idea_id])) {
                $success = 'Idea updated successfully.';
            } else {
                $error = 'Failed to update idea.';
            }
        }
    }
}

$stmt = $pdo->prepare("SELECT i.*, u.username FROM ideas i JOIN users u ON i.user_id = u.id WHERE i.id = ?");
$stmt->execute([$idea_id]);
$idea = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$idea) {
    header('Location: ideas.php');
    exit;
}

$page_title = 'Edit Idea - ' . $t_site_title;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/../includes/head.php'; ?>
</head>
<body>
    <div class="admin-wrapper">
        <?php include __DIR__ . '/admin_sidebar.php'; ?>
        <main id="main-content" class="admin-content">
            <h1>Edit Idea</h1>
            <p class="text-muted">Submitted by <strong><?= htmlspecialchars($idea['username'], ENT_QUOTES, 'UTF-8') ?></strong></p>
            <p><a href="ideas.php?status=<?= urlencode($idea['status']) ?>" class="btn btn-sm btn-secondary">&larr; Back to Ideas</a></p>

            <?php if ($error): ?><p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if ($success): ?><p class="success" role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

            <form method="POST" action="edit_idea.php?id=<?= $idea_id ?>" class="auth-form form-wide">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="idea_title">Title</label>
                    <input type="text" id="idea_title" name="title" maxlength="255" value="<?= htmlspecialchars($idea['title'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group">
                    <label for="idea_desc">Description</label>
                    <textarea id="idea_desc" name="description" rows="7" required><?= htmlspecialchars($idea['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <button type="submit" class="btn">Save Changes</button>
            </form>
        </main>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js', '../'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
