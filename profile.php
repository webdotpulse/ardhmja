<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Refresh user status from DB
$stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$_SESSION['status'] = $user['status'];

// Fetch user ideas
$stmt = $pdo->prepare("SELECT title, status, created_at FROM ideas WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$ideas = $stmt->fetchAll();

// Fetch agreed points
$stmt = $pdo->prepare("
    SELECT pp.title
    FROM agreements a
    JOIN party_points pp ON a.point_id = pp.id
    WHERE a.user_id = ?
");
$stmt->execute([$user_id]);
$agreements = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - Ardhmja</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container main-content">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h2>
        <a href="change_password.php" class="btn btn-sm" style="float: right;">Change Password</a>

        <div class="status-box <?= $_SESSION['status'] === 'approved' ? 'status-approved' : 'status-pending' ?>">
            Account Status: <strong><?= ucfirst(htmlspecialchars($_SESSION['status'])) ?></strong>
            <?php if ($_SESSION['status'] === 'pending'): ?>
                <p>Your account is waiting for admin approval. Once approved, you can submit ideas and agree to party points.</p>
            <?php endif; ?>
        </div>

        <div class="profile-sections">
            <div class="section">
                <h3>My Submitted Ideas</h3>
                <?php if ($_SESSION['status'] === 'approved'): ?>
                    <a href="submit_idea.php" class="btn btn-sm">Submit New Idea</a>
                <?php endif; ?>

                <?php if (empty($ideas)): ?>
                    <p>You haven't submitted any ideas yet.</p>
                <?php else: ?>
                    <ul class="idea-list">
                        <?php foreach ($ideas as $idea): ?>
                            <li>
                                <strong><?= htmlspecialchars($idea['title']) ?></strong> -
                                <span class="badge badge-<?= $idea['status'] ?>"><?= ucfirst(htmlspecialchars($idea['status'])) ?></span>
                                <small>(<?= $idea['created_at'] ?>)</small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="section">
                <h3>Party Points I Agree With</h3>
                <?php if (empty($agreements)): ?>
                    <p>You haven't agreed to any party points yet. <a href="points.php">View points</a></p>
                <?php else: ?>
                    <ul class="agreement-list">
                        <?php foreach ($agreements as $agreement): ?>
                            <li><?= htmlspecialchars($agreement['title']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include "includes/footer.php"; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>