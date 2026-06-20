<?php
session_start();
require_once 'includes/db.php';

$ideas = $pdo->query("
    SELECT i.title, i.description, u.username, i.created_at
    FROM ideas i
    JOIN users u ON i.user_id = u.id
    WHERE i.status = 'approved'
    ORDER BY i.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ideas - Ardhmja</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container main-content">
        <div class="flex-header">
            <h2>Community Ideas</h2>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['status'] === 'approved'): ?>
                <a href="submit_idea.php" class="btn">Submit an Idea</a>
            <?php endif; ?>
        </div>

        <p>Explore the ideas submitted by our members and approved by our party admins.</p>

        <div class="ideas-list">
            <?php foreach ($ideas as $idea): ?>
                <div class="idea-item">
                    <h3><?= htmlspecialchars($idea['title']) ?></h3>
                    <p class="idea-meta">By <strong><?= htmlspecialchars($idea['username']) ?></strong> on <?= date('M d, Y', strtotime($idea['created_at'])) ?></p>
                    <div class="idea-desc">
                        <?= nl2br(htmlspecialchars($idea['description'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if(empty($ideas)): ?>
                <p>No ideas published yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>