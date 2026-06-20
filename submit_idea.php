<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['status'] !== 'approved') {
    header('Location: profile.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (empty($title) || empty($description)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO ideas (user_id, title, description, status) VALUES (?, ?, ?, 'pending')");
        if ($stmt->execute([$_SESSION['user_id'], $title, $description])) {
            $success = "Your idea has been submitted and is waiting for admin approval.";
        } else {
            $error = "Failed to submit idea. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Idea - Ardhmja</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container main-content">
        <h2>Submit a New Idea</h2>
        <p>Share your vision with the party. All submissions will be reviewed by an admin before publication.</p>

        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
            <p><a href="profile.php">Return to Profile</a></p>
        <?php else: ?>
            <form method="POST" class="auth-form" style="max-width: 600px;">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="6" required></textarea>
                </div>
                <button type="submit" class="btn">Submit Idea</button>
            </form>
        <?php endif; ?>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>