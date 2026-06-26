<?php
require_once '../includes/db.php';
require_once 'admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        if (!empty($title)) {
            $stmt = $pdo->prepare("INSERT INTO party_points (title, description) VALUES (?, ?)");
            $stmt->execute([$title, $description]);
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['point_id'])) {
        $stmt = $pdo->prepare("DELETE FROM party_points WHERE id = ?");
        $stmt->execute([$_POST['point_id']]);
    }
    header('Location: points.php');
    exit;
}

$points = $pdo->query("SELECT * FROM party_points ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Party Points</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; margin: 0; }
        .admin-wrapper { display: flex; flex: 1; }
        .admin-content { flex: 1; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f4f4f4; }
        .form-box { background: #f9f9f9; padding: 20px; margin-bottom: 20px; border-radius: 5px; }
    </style>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
</head>
<body>
    <div class="admin-wrapper">
    <?php include 'admin_sidebar.php'; ?>
    <div class="admin-content">
        <h1>Party Points</h1>

        <div class="form-box">
            <h3>Add New Point</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <button type="submit" class="btn">Add Point</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($points as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['title']) ?></td>
                    <td><?= nl2br(htmlspecialchars($p['description'])) ?></td>
                    <td><?= $p['created_at'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="point_id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($points)): ?>
                <tr><td colspan="4">No party points defined.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>
    <?php include "../includes/footer.php"; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>