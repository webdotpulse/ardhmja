<?php
require_once '../includes/db.php';
require_once 'admin_auth.php';

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_title = $_POST['site_title'] ?? '';
    $site_description = $_POST['site_description'] ?? '';
    $home_banner_text = $_POST['home_banner_text'] ?? '';

    $stmt = $pdo->prepare("UPDATE settings SET site_title = ?, site_description = ?, home_banner_text = ? WHERE id = 1");
    $stmt->execute([$site_title, $site_description, $home_banner_text]);

    // Check if ID 1 exists, if not insert
    if ($stmt->rowCount() == 0) {
        $stmt = $pdo->prepare("SELECT id FROM settings WHERE id = 1");
        $stmt->execute();
        if (!$stmt->fetch()) {
             $stmt = $pdo->prepare("INSERT INTO settings (id, site_title, site_description, home_banner_text) VALUES (1, ?, ?, ?)");
             $stmt->execute([$site_title, $site_description, $home_banner_text]);
        }
    }

    $success = "Settings updated successfully.";
}

$settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch() ?: ['site_title'=>'', 'site_description'=>'', 'home_banner_text'=>''];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Site Settings</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { display: flex; margin: 0; }
        .admin-content { flex: 1; padding: 20px; }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    <div class="admin-content">
        <h1>Site Settings</h1>
        <?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>

        <form method="POST" class="settings-form">
            <div class="form-group">
                <label>Site Title</label>
                <input type="text" name="site_title" value="<?= htmlspecialchars($settings['site_title']) ?>" required>
            </div>
            <div class="form-group">
                <label>Home Banner Text</label>
                <input type="text" name="home_banner_text" value="<?= htmlspecialchars($settings['home_banner_text']) ?>">
            </div>
            <div class="form-group">
                <label>Site Description</label>
                <textarea name="site_description" rows="5"><?= htmlspecialchars($settings['site_description']) ?></textarea>
            </div>
            <button type="submit" class="btn">Save Settings</button>
        </form>
    </div>
    <script src="../assets/js/main.js"></script>
</body>
</html>