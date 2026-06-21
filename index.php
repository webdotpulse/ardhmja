<?php
session_start();
require_once 'includes/db.php';

if ($pdo) {
    // Get latest public ideas
    $latest_ideas = $pdo->query("
        SELECT i.title, u.username, i.created_at
        FROM ideas i
        JOIN users u ON i.user_id = u.id
        WHERE i.status = 'approved'
        ORDER BY i.created_at DESC
        LIMIT 3
    ")->fetchAll();
} else {
    $latest_ideas = [];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($t_site_title) ?> - Home</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="hero-banner">
        <div class="container hero-content">
            <h2><?= htmlspecialchars($t_home_banner_text) ?></h2>
            <p><?= $t_site_description ?></p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn btn-large"><?= htmlspecialchars($t_join_us_btn) ?></a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container main-content">
        <div class="split-section">
            <div class="half-section">
                <h3><?= htmlspecialchars($t_home_core_values_title) ?></h3>
                <p><?= $t_home_core_values_text ?></p>
                <a href="points.php" class="btn"><?= htmlspecialchars($t_view_points_btn) ?></a>
            </div>
            <div class="half-section">
                <h3><?= htmlspecialchars($t_home_latest_ideas_title) ?></h3>
                <?php if (empty($latest_ideas)): ?>
                    <p>No ideas published yet.</p>
                <?php else: ?>
                    <ul class="home-ideas-list">
                        <?php foreach ($latest_ideas as $idea): ?>
                            <li>
                                <strong><?= htmlspecialchars($idea['title']) ?></strong>
                                <div><small>By <?= htmlspecialchars($idea['username']) ?> on <?= date('M d, Y', strtotime($idea['created_at'])) ?></small></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="ideas.php" class="btn"><?= htmlspecialchars($t_view_ideas_btn) ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($t_site_title) ?>. <?= htmlspecialchars($t_footer_text) ?></p>
        </div>
    </footer>
    <script src="assets/js/main.js"></script>
</body>
</html>