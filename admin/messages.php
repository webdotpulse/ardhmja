<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/admin_auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['message_id'])) {
    if (!verify_csrf()) {
        $error = __('csrf_error');
    } elseif ($_POST['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([(int)$_POST['message_id']]);
        header('Location: messages.php');
        exit;
    }
}

$per_page = 20;
$page = max(1, (int)($_GET['page'] ?? 1));
$total_messages = (int)$pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$total_pages = max(1, (int)ceil($total_messages / $per_page));
if ($page > $total_pages) {
    $page = $total_pages;
}
$offset = ($page - 1) * $per_page;

$stmt = $pdo->prepare("SELECT * FROM messages ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $per_page, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll();

$page_title = 'Contact Messages - ' . $t_site_title;
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
            <h1>Contact Messages</h1>

            <?php if ($error): ?>
                <p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars(format_date_localized($m['created_at'], 'en'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><a href="mailto:<?= htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8') ?></a></td>
                            <td style="white-space: pre-wrap; word-break: break-word; max-width: 520px;"><?= htmlspecialchars($m['message'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <form method="POST" action="messages.php" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="message_id" value="<?= (int)$m['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" data-confirm="Delete this message permanently?">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($messages)): ?>
                        <tr><td colspan="5">No contact messages received yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <nav class="pagination" aria-label="Pagination">
                    <?php if ($page > 1): ?>
                        <a href="messages.php?page=<?= $page - 1 ?>" class="btn btn-sm btn-secondary">&larr; Previous</a>
                    <?php endif; ?>
                    <span class="text-muted">Page <?= $page ?> of <?= $total_pages ?></span>
                    <?php if ($page < $total_pages): ?>
                        <a href="messages.php?page=<?= $page + 1 ?>" class="btn btn-sm btn-secondary">Next &rarr;</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </main>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js', '../'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
