<?php
session_start();
require_once 'includes/db.php';

$ideas = [];
if ($pdo) {
    $ideas = $pdo->query("
        SELECT i.title, i.description, u.username, i.created_at
        FROM ideas i
        JOIN users u ON i.user_id = u.id
        WHERE i.status = 'approved'
        ORDER BY i.created_at DESC
    ")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($t_ideas_page_title) ?> - <?= htmlspecialchars($t_site_title) ?></title>
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
        <div class="flex-header">
            <h2><?= htmlspecialchars($t_ideas_page_title) ?></h2>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['status'] === 'approved'): ?>
                <a href="submit_idea.php" class="btn"><?= htmlspecialchars($t_submit_idea_btn) ?></a>
            <?php endif; ?>
        </div>

        <p><?= $t_ideas_page_text ?></p>

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