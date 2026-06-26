<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['new_username'] ?? '');
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $error = "Invalid CSRF token.";
    } elseif (empty($new_username)) {
        $error = "Please enter a new username.";
    } elseif (strlen($new_username) < 3) {
        $error = "Username must be at least 3 characters long.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$new_username, $user_id]);

        if ($stmt->fetch()) {
            $error = "Username is already taken.";
        } else {
            $update_stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            if ($update_stmt->execute([$new_username, $user_id])) {
                $_SESSION['username'] = $new_username;
                $success = "Username changed successfully.";
            } else {
                $error = "Failed to update username. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Username - <?= htmlspecialchars($t_site_title) ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container main-content">
        <h2>Change Username</h2>
        <a href="<?= $_SESSION['role'] === 'admin' ? 'admin/index.php' : 'profile.php' ?>" class="btn btn-sm" style="margin-bottom: 20px;">&larr; Back</a>

        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>

        <form method="POST" action="" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="form-group">
                <label for="new_username">New Username</label>
                <input type="text" id="new_username" name="new_username" value="<?= htmlspecialchars($_SESSION['username']) ?>" required>
            </div>
            <button type="submit" class="btn">Change Username</button>
        </form>
    </div>
    <?php include "includes/footer.php"; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
