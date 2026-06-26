<?php
session_start();
require_once 'includes/db.php';

$error = '';
$success = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE verify_token = ? AND email_verified = 0");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        $updateStmt = $pdo->prepare("UPDATE users SET email_verified = 1, verify_token = NULL WHERE id = ?");
        if ($updateStmt->execute([$user['id']])) {
            $success = "Your email has been successfully verified! You can now wait for admin approval and login.";
        } else {
            $error = "Something went wrong during verification. Please try again.";
        }
    } else {
        $error = "Invalid or expired verification token. Your email might already be verified.";
    }
} else {
    $error = "No verification token provided.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - <?= htmlspecialchars($t_site_title) ?></title>
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
        <h2>Email Verification</h2>
        <?php if ($error): ?>
            <div class="status-box status-pending">
                <p class="error"><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="status-box status-approved">
                <p class="success"><?= htmlspecialchars($success) ?></p>
                <p><a href="login.php" class="btn">Go to Login</a></p>
            </div>
        <?php endif; ?>
    </div>
    <?php include "includes/footer.php"; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>