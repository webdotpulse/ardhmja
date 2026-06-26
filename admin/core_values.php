<?php
require_once '../includes/db.php';
require_once 'admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        if (!empty($title)) {
            $stmt = $pdo->prepare("INSERT INTO core_values (title, description) VALUES (?, ?)");
            $stmt->execute([$title, $description]);
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['value_id'])) {
        $stmt = $pdo->prepare("DELETE FROM core_values WHERE id = ?");
        $stmt->execute([$_POST['value_id']]);
    }
    header('Location: core_values.php');
    exit;
}

$values = $pdo->query("SELECT * FROM core_values ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Core Values</title>
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
        <h1>Core Values</h1>

        <div class="form-box">
            <h3>Add New Value</h3>
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
                <button type="submit" class="btn">Add Value</button>
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
                <?php foreach ($values as $v): ?>
                <tr>
                    <td><?= htmlspecialchars($v['title']) ?></td>
                    <td><?= nl2br(htmlspecialchars($v['description'])) ?></td>
                    <td><?= $v['created_at'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="value_id" value="<?= $v['id'] ?>">
                            <a href="edit_value.php?id=<?= $v['id'] ?>" class="btn btn-sm">Edit</a>
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($values)): ?>
                <tr><td colspan="4">No Core Values defined.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>
    <?php include "../includes/footer.php"; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>