<?php
require_once '../includes/db.php';
require_once 'admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['idea_id'])) {
    $action = $_POST['action'];
    $idea_id = $_POST['idea_id'];

    if (in_array($action, ['approved', 'rejected'])) {
        $stmt = $pdo->prepare("UPDATE ideas SET status = ? WHERE id = ?");
        $stmt->execute([$action, $idea_id]);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM ideas WHERE id = ?");
        $stmt->execute([$idea_id]);
    }
    header('Location: ideas.php');
    exit;
}

$status_filter = $_GET['status'] ?? 'pending';
$stmt = $pdo->prepare("
    SELECT i.*, u.username
    FROM ideas i
    JOIN users u ON i.user_id = u.id
    WHERE i.status = ?
    ORDER BY i.created_at DESC
");
$stmt->execute([$status_filter]);
$ideas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Ideas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; margin: 0; }
        .admin-wrapper { display: flex; flex: 1; }
        .admin-content { flex: 1; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f4f4f4; }
        .tabs { margin-bottom: 20px; }
        .tabs a { padding: 10px 20px; text-decoration: none; background: #eee; border-radius: 5px; color: #333;}
        .tabs a.active { background: #0033a0; color: #fff; }
    </style>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
</head>
<body>
    <div class="admin-wrapper">
    <?php include 'admin_sidebar.php'; ?>
    <div class="admin-content">
        <h1>Manage Ideas</h1>
        <div class="tabs">
            <a href="?status=pending" class="<?= $status_filter === 'pending' ? 'active' : '' ?>">Pending</a>
            <a href="?status=approved" class="<?= $status_filter === 'approved' ? 'active' : '' ?>">Approved</a>
            <a href="?status=rejected" class="<?= $status_filter === 'rejected' ? 'active' : '' ?>">Rejected</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>User</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ideas as $idea): ?>
                <tr>
                    <td><?= htmlspecialchars($idea['title']) ?></td>
                    <td><?= htmlspecialchars($idea['username']) ?></td>
                    <td><?= nl2br(htmlspecialchars($idea['description'])) ?></td>
                    <td><?= $idea['created_at'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="idea_id" value="<?= $idea['id'] ?>">
                            <?php if ($idea['status'] !== 'approved'): ?>
                                <button type="submit" name="action" value="approved" class="btn btn-sm btn-success">Approve</button>
                            <?php endif; ?>
                            <?php if ($idea['status'] !== 'rejected'): ?>
                                <button type="submit" name="action" value="rejected" class="btn btn-sm btn-warning">Reject</button>
                            <?php endif; ?>
                            <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($ideas)): ?>
                <tr><td colspan="5">No ideas found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>
    <?php include "../includes/footer.php"; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>