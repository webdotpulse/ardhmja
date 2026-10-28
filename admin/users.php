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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['user_id'])) {
    if (!verify_csrf()) {
        $error = __('csrf_error');
    } else {
        $action = (string)$_POST['action'];
        $target_id = (int)$_POST['user_id'];
        $self_id = (int)$_SESSION['user_id'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$target_id]);
        $target_user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($target_user) {
            if ($target_id === $self_id && in_array($action, ['rejected', 'delete', 'toggle_role'], true)) {
                $error = 'For safety, you cannot delete, reject, or demote your own administrator account.';
            } elseif (in_array($action, ['approved', 'rejected'], true)) {
                $upd = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
                $upd->execute([$action, $target_id]);

                if ($action === 'approved' && $target_user['status'] !== 'approved') {
                    $login_link = app_base_url() . '/login.php';
                    send_app_email(
                        $target_user['email'],
                        $target_user['username'],
                        __('email_account_approved_subject', ['site_title' => $t_site_title]),
                        __('email_account_approved_body', [
                            'username' => $target_user['username'],
                            'site_title' => $t_site_title,
                            'login_link' => $login_link,
                        ])
                    );
                }
            } elseif ($action === 'verify_email') {
                $upd = $pdo->prepare("UPDATE users SET email_verified = 1, verify_token = NULL, verify_expires_at = NULL WHERE id = ?");
                $upd->execute([$target_id]);
            } elseif ($action === 'delete') {
                delete_uploaded_avatar($target_user['profile_picture'] ?? null, __DIR__ . '/../uploads');
                $del = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $del->execute([$target_id]);
            } elseif ($action === 'toggle_role') {
                $new_role = ($target_user['role'] === 'admin') ? 'user' : 'admin';
                $upd = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
                $upd->execute([$new_role, $target_id]);
            }
        }

        if ($error === '') {
            header('Location: users.php?status=' . urlencode($status_filter));
            exit;
        }
    }
}

$per_page = 20;
$page = max(1, (int)($_GET['page'] ?? 1));
$cnt_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE status = ?");
$cnt_stmt->execute([$status_filter]);
$total_users = (int)$cnt_stmt->fetchColumn();
$total_pages = max(1, (int)ceil($total_users / $per_page));
if ($page > $total_pages) {
    $page = $total_pages;
}
$offset = ($page - 1) * $per_page;

$stmt = $pdo->prepare("SELECT * FROM users WHERE status = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $status_filter, PDO::PARAM_STR);
$stmt->bindValue(2, $per_page, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();

$page_title = 'Manage Users - ' . $t_site_title;
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
            <h1>Manage Users</h1>

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
                            <th>ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Verified</th>
                            <th>Role</th>
                            <th>Public</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u):
                            $uid = (int)$u['id'];
                            $is_self = ($uid === (int)$_SESSION['user_id']);
                        ?>
                        <tr>
                            <td><?= $uid ?></td>
                            <td>
                                <strong><?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <?php if (!empty($u['full_name'])): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($u['full_name'], ENT_QUOTES, 'UTF-8') ?></small>
                                <?php endif; ?>
                            </td>
                            <td><a href="mailto:<?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></a></td>
                            <td>
                                <?php if ((int)$u['email_verified'] === 1): ?>
                                    <span class="badge badge-approved">Verified</span>
                                <?php else: ?>
                                    <span class="badge badge-pending">Unverified</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= !empty($u['is_public']) ? 'Yes' : 'No' ?><?= !empty($u['profile_picture']) ? ' (Photo)' : '' ?></td>
                            <td><?= htmlspecialchars(format_date_localized($u['created_at'], 'en'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <form method="POST" action="users.php?status=<?= urlencode($status_filter) ?>" style="display:flex; flex-wrap:wrap; gap:6px;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="user_id" value="<?= $uid ?>">
                                    <?php if ($u['status'] !== 'approved'): ?>
                                        <button type="submit" name="action" value="approved" class="btn btn-sm btn-success">Approve</button>
                                    <?php endif; ?>
                                    <?php if ((int)$u['email_verified'] === 0): ?>
                                        <button type="submit" name="action" value="verify_email" class="btn btn-sm btn-secondary">Verify Email</button>
                                    <?php endif; ?>
                                    <?php if ($u['status'] !== 'rejected' && !$is_self): ?>
                                        <button type="submit" name="action" value="rejected" class="btn btn-sm btn-warning">Reject</button>
                                    <?php endif; ?>
                                    <?php if (!$is_self): ?>
                                        <button type="submit" name="action" value="toggle_role" class="btn btn-sm btn-secondary">Make <?= $u['role'] === 'admin' ? 'User' : 'Admin' ?></button>
                                    <?php endif; ?>
                                    <a href="edit_user.php?id=<?= $uid ?>" class="btn btn-sm">Edit Profile</a>
                                    <?php if (!$is_self): ?>
                                        <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger" data-confirm="Delete user <?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?> permanently?">Delete</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?>
                        <tr><td colspan="8">No users found in this tab.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <nav class="pagination" aria-label="Pagination">
                    <?php if ($page > 1): ?>
                        <a href="users.php?status=<?= urlencode($status_filter) ?>&page=<?= $page - 1 ?>" class="btn btn-sm btn-secondary">&larr; Previous</a>
                    <?php endif; ?>
                    <span class="text-muted">Page <?= $page ?> of <?= $total_pages ?></span>
                    <?php if ($page < $total_pages): ?>
                        <a href="users.php?status=<?= urlencode($status_filter) ?>&page=<?= $page + 1 ?>" class="btn btn-sm btn-secondary">Next &rarr;</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </main>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="../assets/js/main.js"></script>
</body>
</html>
