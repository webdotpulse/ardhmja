<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/mailer.php';
require_once __DIR__ . '/admin_auth.php';

$allowed_statuses = ['pending', 'approved', 'rejected'];
$status_filter = $_GET['status'] ?? 'pending';
if (!in_array($status_filter, $allowed_statuses, true)) {
    $status_filter = 'pending';
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['idea_id'])) {
    if (!verify_csrf()) {
        $error = __('csrf_error');
    } else {
        $action = (string)$_POST['action'];
        $idea_id = (int)$_POST['idea_id'];

        $stmt = $pdo->prepare("
            SELECT i.*, u.username, u.email
            FROM ideas i
            JOIN users u ON i.user_id = u.id
            WHERE i.id = ?
        ");
        $stmt->execute([$idea_id]);
        $target_idea = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($target_idea) {
            if (in_array($action, ['approved', 'rejected'], true)) {
                $upd = $pdo->prepare("UPDATE ideas SET status = ? WHERE id = ?");
                $upd->execute([$action, $idea_id]);

                if ($action === 'approved' && $target_idea['status'] !== 'approved') {
                    $ideas_link = app_base_url() . '/ideas.php';
                    send_app_email(
                        $target_idea['email'],
                        $target_idea['username'],
                        __('email_idea_approved_subject', ['site_title' => $t_site_title]),
                        __('email_idea_approved_body', [
                            'username' => $target_idea['username'],
                            'idea_title' => $target_idea['title'],
                            'site_title' => $t_site_title,
                            'ideas_link' => $ideas_link,
                        ])
                    );
                }
            } elseif ($action === 'delete') {
                $del = $pdo->prepare("DELETE FROM ideas WHERE id = ?");
                $del->execute([$idea_id]);
            }
        }

        header('Location: ideas.php?status=' . urlencode($status_filter));
        exit;
    }
}

$per_page = 20;
$page = max(1, (int)($_GET['page'] ?? 1));
$cnt_stmt = $pdo->prepare("SELECT COUNT(*) FROM ideas WHERE status = ?");
$cnt_stmt->execute([$status_filter]);
$total_ideas = (int)$cnt_stmt->fetchColumn();
$total_pages = max(1, (int)ceil($total_ideas / $per_page));
if ($page > $total_pages) {
    $page = $total_pages;
}
$offset = ($page - 1) * $per_page;

$stmt = $pdo->prepare("
    SELECT i.*, u.username
    FROM ideas i
    JOIN users u ON i.user_id = u.id
    WHERE i.status = ?
    ORDER BY i.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $status_filter, PDO::PARAM_STR);
$stmt->bindValue(2, $per_page, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$ideas = $stmt->fetchAll();

$page_title = 'Manage Ideas - ' . $t_site_title;
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
            <h1>Manage Ideas</h1>

            <?php if ($error): ?>
                <p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <div class="tabs">
                <a href="?status=pending" class="<?= $status_filter === 'pending' ? 'active' : '' ?>">Pending</a>
                <a href="?status=approved" class="<?= $status_filter === 'approved' ? 'active' : '' ?>">Approved</a>
                <a href="?status=rejected" class="<?= $status_filter === 'rejected' ? 'active' : '' ?>">Rejected</a>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Description</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ideas as $idea):
                            $iid = (int)$idea['id'];
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($idea['title'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                            <td><?= htmlspecialchars($idea['username'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= nl2br(htmlspecialchars($idea['description'], ENT_QUOTES, 'UTF-8'), false) ?></td>
                            <td><?= htmlspecialchars(format_date_localized($idea['created_at'], 'en'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <form method="POST" action="ideas.php?status=<?= urlencode($status_filter) ?>" style="display:flex; gap:6px; flex-wrap:wrap;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="idea_id" value="<?= $iid ?>">
                                    <?php if ($idea['status'] !== 'approved'): ?>
                                        <button type="submit" name="action" value="approved" class="btn btn-sm btn-success">Approve</button>
                                    <?php endif; ?>
                                    <?php if ($idea['status'] !== 'rejected'): ?>
                                        <button type="submit" name="action" value="rejected" class="btn btn-sm btn-warning">Reject</button>
                                    <?php endif; ?>
                                    <a href="edit_idea.php?id=<?= $iid ?>" class="btn btn-sm">Edit</a>
                                    <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger" data-confirm="Delete this idea permanently?">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($ideas)): ?>
                        <tr><td colspan="5">No ideas found in this tab.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <nav class="pagination" aria-label="Pagination">
                    <?php if ($page > 1): ?>
                        <a href="ideas.php?status=<?= urlencode($status_filter) ?>&page=<?= $page - 1 ?>" class="btn btn-sm btn-secondary">&larr; Previous</a>
                    <?php endif; ?>
                    <span class="text-muted">Page <?= $page ?> of <?= $total_pages ?></span>
                    <?php if ($page < $total_pages): ?>
                        <a href="ideas.php?status=<?= urlencode($status_filter) ?>&page=<?= $page + 1 ?>" class="btn btn-sm btn-secondary">Next &rarr;</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </main>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js', '../'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
