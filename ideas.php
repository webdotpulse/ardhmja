<?php
require_once __DIR__ . '/includes/db.php';

$per_page = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$total_ideas = 0;
$total_pages = 1;
$ideas = [];

if ($pdo instanceof PDO) {
    $total_ideas = (int)$pdo->query("SELECT COUNT(*) FROM ideas WHERE status = 'approved'")->fetchColumn();
    $total_pages = max(1, (int)ceil($total_ideas / $per_page));
    if ($page > $total_pages) {
        $page = $total_pages;
    }
    $offset = ($page - 1) * $per_page;

    $stmt = $pdo->prepare("
        SELECT i.title, i.description, u.username, i.created_at
        FROM ideas i
        JOIN users u ON i.user_id = u.id
        WHERE i.status = 'approved'
        ORDER BY i.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bindValue(1, $per_page, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $ideas = $stmt->fetchAll();
}

$page_title = $t_ideas_page_title . ' - ' . $t_site_title;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <main id="main-content" class="container main-content">
        <div class="flex-header">
            <h1><?= htmlspecialchars($t_ideas_page_title, ENT_QUOTES, 'UTF-8') ?></h1>
            <?php if (!empty($_SESSION['user_id']) && ($_SESSION['status'] ?? '') === 'approved'): ?>
                <a href="submit_idea.php" class="btn"><?= htmlspecialchars($t_submit_idea_btn, ENT_QUOTES, 'UTF-8') ?></a>
            <?php endif; ?>
        </div>

        <div class="rich-text-content"><?= sanitize_rich_html($t_ideas_page_text) ?></div>

        <div class="ideas-list">
            <?php foreach ($ideas as $idea): ?>
                <article class="idea-item">
                    <h3><?= htmlspecialchars($idea['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="idea-meta">
                        <?= htmlspecialchars(__('by_author_on_date', [
                            'author' => $idea['username'],
                            'date' => format_date_localized($idea['created_at']),
                        ]), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <div class="idea-desc">
                        <?= nl2br(htmlspecialchars($idea['description'], ENT_QUOTES, 'UTF-8'), false) ?>
                    </div>
                </article>
            <?php endforeach; ?>
            <?php if (empty($ideas)): ?>
                <p><?= htmlspecialchars(__('no_ideas_yet'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <?php if ($total_pages > 1): ?>
            <nav class="pagination" aria-label="Pagination">
                <?php if ($page > 1): ?>
                    <a href="ideas.php?page=<?= $page - 1 ?>" class="btn btn-sm btn-secondary">&larr; <?= htmlspecialchars(__('page_prev'), ENT_QUOTES, 'UTF-8') ?></a>
                <?php endif; ?>
                <span class="text-muted"><?= htmlspecialchars(__('page_of', ['page' => $page, 'total' => $total_pages]), ENT_QUOTES, 'UTF-8') ?></span>
                <?php if ($page < $total_pages): ?>
                    <a href="ideas.php?page=<?= $page + 1 ?>" class="btn btn-sm btn-secondary"><?= htmlspecialchars(__('page_next'), ENT_QUOTES, 'UTF-8') ?> &rarr;</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
