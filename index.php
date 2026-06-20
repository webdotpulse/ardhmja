<?php
session_start();
require_once 'includes/db.php';

if ($pdo) {
    $settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch();
    $site_title = $settings['site_title'] ?? 'Ardhmja';
    $banner_text = $settings['home_banner_text'] ?? 'Building a New Republic Together';
    $description = $settings['site_description'] ?? 'Welcome to our political party platform.';

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
    $site_title = 'Ardhmja';
    $banner_text = 'Building a New Republic Together';
    $description = 'Welcome to our political party platform.';
    $latest_ideas = [];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($site_title) ?> - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="hero-banner">
        <div class="container hero-content">
            <h2><?= htmlspecialchars($banner_text) ?></h2>
            <p><?= nl2br(htmlspecialchars($description)) ?></p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn btn-large btn-primary">Join Us Now</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container main-content">
        <div class="split-section">
            <div class="half-section">
                <h3>Our Core Values</h3>
                <p>We are dedicated to progress, transparency, and giving power back to the people. Explore our <a href="points.php">Party Points</a> to understand our vision for Albania.</p>
                <a href="points.php" class="btn">View Party Points</a>
            </div>
            <div class="half-section">
                <h3>Latest Community Ideas</h3>
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
                    <a href="ideas.php" class="btn">View All Ideas</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($site_title) ?>. All rights reserved.</p>
        </div>
    </footer>
    <script src="assets/js/main.js"></script>
</body>
</html>