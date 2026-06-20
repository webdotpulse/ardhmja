<?php
session_start();
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($message)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        if ($pdo) {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $message])) {
                $success = "Thank you! Your message has been received.";
            } else {
                $error = "Something went wrong. Please try again later.";
            }
        } else {
            // For environments without DB
            $success = "Thank you! Your message has been received (test mode).";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - Ardhmja</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container main-content">
        <h2>Contact Us</h2>
        <p>Have questions, ideas, or want to get involved? Please send us a message below.</p>

        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php else: ?>
            <form method="POST" class="auth-form" style="max-width: 600px; margin-left: 0;">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="6" required></textarea>
                </div>
                <button type="submit" class="btn">Send Message</button>
            </form>
        <?php endif; ?>
    </div>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Ardhmja. All rights reserved.</p>
        </div>
    </footer>
    <script src="assets/js/main.js"></script>
</body>
</html>