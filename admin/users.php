<?php
require_once '../includes/db.php';
require_once 'admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['user_id'])) {
    $action = $_POST['action'];
    $user_id = $_POST['user_id'];

    if (in_array($action, ['approved', 'rejected'])) {
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$action, $user_id]);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
    }
    header('Location: users.php');
    exit;
}

$status_filter = $_GET['status'] ?? 'pending';
$stmt = $pdo->prepare("SELECT * FROM users WHERE status = ? ORDER BY created_at DESC");
$stmt->execute([$status_filter]);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { display: flex; margin: 0; }
        .admin-content { flex: 1; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f4f4f4; }
        .tabs { margin-bottom: 20px; }
        .tabs a { padding: 10px 20px; text-decoration: none; background: #eee; border-radius: 5px; color: #333;}
        .tabs a.active { background: #0033a0; color: #fff; }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    <div class="admin-content">
        <h1>Manage Users</h1>
        <div class="tabs">
            <a href="?status=pending" class="<?= $status_filter === 'pending' ? 'active' : '' ?>">Pending</a>
            <a href="?status=approved" class="<?= $status_filter === 'approved' ? 'active' : '' ?>">Approved</a>
            <a href="?status=rejected" class="<?= $status_filter === 'rejected' ? 'active' : '' ?>">Rejected</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= $u['role'] ?></td>
                    <td><?= $u['created_at'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <?php if ($u['status'] !== 'approved'): ?>
                                <button type="submit" name="action" value="approved" class="btn btn-sm btn-success">Approve</button>
                            <?php endif; ?>
                            <?php if ($u['status'] !== 'rejected'): ?>
                                <button type="submit" name="action" value="rejected" class="btn btn-sm btn-warning">Reject</button>
                            <?php endif; ?>
                            <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($users)): ?>
                <tr><td colspan="5">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="../assets/js/main.js"></script>
</body>
</html>