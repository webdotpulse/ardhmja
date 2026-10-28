<?php
require_once __DIR__ . '/includes/db.php';

$latest_ideas = [];
if ($pdo instanceof PDO) {
    $latest_ideas = $pdo->query("
        SELECT i.title, u.username, i.created_at
        FROM ideas i
        JOIN users u ON i.user_id = u.id
        WHERE i.status = 'approved'
        ORDER BY i.created_at DESC
        LIMIT 3
    ")->fetchAll();
}

$page_title = $t_site_title . ' - ' . $t_menu_home;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
    <link rel="preload" as="image" href="<?= htmlspecialchars(asset_url('assets/images/hero-bg-1x.webp'), ENT_QUOTES, 'UTF-8') ?>" type="image/webp">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <section class="hero-banner">
        <div class="container hero-content">
            <h1><?= htmlspecialchars($t_home_banner_text, ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="hero-subtext"><?= sanitize_rich_html($t_site_description) ?></div>
            <?php if (empty($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn btn-large"><?= htmlspecialchars($t_join_us_btn, ENT_QUOTES, 'UTF-8') ?></a>
            <?php endif; ?>
        </div>
    </section>

    <main id="main-content" class="container main-content">
        <div class="split-section">
            <div class="half-section">
                <h2><?= htmlspecialchars($t_home_core_values_title, ENT_QUOTES, 'UTF-8') ?></h2>
                <div class="rich-text-content"><?= sanitize_rich_html($t_home_core_values_text) ?></div>
                <a href="core_values.php" class="btn"><?= htmlspecialchars($t_view_values_btn, ENT_QUOTES, 'UTF-8') ?></a>
            </div>
            <div class="half-section">
                <h2><?= htmlspecialchars($t_home_latest_ideas_title, ENT_QUOTES, 'UTF-8') ?></h2>
                <?php if (empty($latest_ideas)): ?>
                    <p><?= htmlspecialchars(__('no_ideas_yet'), ENT_QUOTES, 'UTF-8') ?></p>
                <?php else: ?>
                    <ul class="home-ideas-list">
                        <?php foreach ($latest_ideas as $idea): ?>
                            <li>
                                <strong><?= htmlspecialchars($idea['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <div>
                                    <small class="text-muted">
                                        <?= htmlspecialchars(__('by_author_on_date', [
                                            'author' => $idea['username'],
                                            'date' => format_date_localized($idea['created_at']),
                                        ]), ENT_QUOTES, 'UTF-8') ?>
                                    </small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="ideas.php" class="btn"><?= htmlspecialchars($t_view_ideas_btn, ENT_QUOTES, 'UTF-8') ?></a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
