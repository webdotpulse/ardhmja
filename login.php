<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: profile.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT id, password, role, status, email_verified FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'rejected') {
                $error = "Your account has been rejected by an admin.";
            } elseif ($user['email_verified'] == 0) {
                $error = "Please verify your email address before logging in.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $user['role'];
                $_SESSION['status'] = $user['status'];

                if ($user['role'] === 'admin') {
                    header('Location: admin/index.php');
                } else {
                    header('Location: profile.php');
                }
                exit;
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($t_login_page_title) ?> - <?= htmlspecialchars($t_site_title) ?></title>
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
        <h2><?= htmlspecialchars($t_login_page_title) ?></h2>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form method="POST" action="" class="auth-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
    <?php include "includes/footer.php"; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>