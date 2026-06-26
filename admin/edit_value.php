<?php
require_once '../includes/db.php';
require_once 'admin_auth.php';

if (!isset($_GET['id'])) {
    header('Location: core_values.php');
    exit();
}

$value_id = $_GET['id'];

// Handle form submission
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (empty($title)) {
        $error = "Title cannot be empty.";
    } else {
        $stmt = $pdo->prepare("UPDATE core_values SET title = ?, description = ? WHERE id = ?");
        if ($stmt->execute([$title, $description, $value_id])) {
            $success = "Core value updated successfully.";
        } else {
            $error = "Failed to update core value.";
        }
    }
}

// Fetch value data
$stmt = $pdo->prepare("SELECT * FROM core_values WHERE id = ?");
$stmt->execute([$value_id]);
$value = $stmt->fetch();

if (!$value) {
    header('Location: core_values.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Core Value</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; margin: 0; }
        .admin-wrapper { display: flex; flex: 1; }
        .admin-content { flex: 1; padding: 20px; }
        .edit-form { max-width: 500px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;}
        .edit-form label { display: block; margin-bottom: 5px; font-weight: bold; }
        .edit-form input[type="text"], .edit-form textarea { width: 100%; margin-bottom: 15px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
    </style>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
</head>
<body>
    <div class="admin-wrapper">
    <?php include 'admin_sidebar.php'; ?>
    <div class="admin-content">
        <h1>Edit Core Value</h1>
        <a href="core_values.php" class="btn btn-sm" style="margin-bottom: 20px;">&larr; Back to Core Values</a>

        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>

        <form method="POST" class="edit-form">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($value['title']) ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="5"><?= htmlspecialchars($value['description']) ?></textarea>
            </div>

            <button type="submit" class="btn">Save Changes</button>
        </form>
    </div>
    </div>
    <?php include "../includes/footer.php"; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
