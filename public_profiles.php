<?php
require_once __DIR__ . '/includes/db.php';

$per_page = 12;
$page = max(1, (int)($_GET['page'] ?? 1));
$total_profiles = 0;
$total_pages = 1;
$profiles = [];

if ($pdo instanceof PDO) {
    $total_profiles = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE is_public = 1 AND status = 'approved'")->fetchColumn();
    $total_pages = max(1, (int)ceil($total_profiles / $per_page));
    if ($page > $total_pages) {
        $page = $total_pages;
    }
    $offset = ($page - 1) * $per_page;

    $stmt = $pdo->prepare("
        SELECT username, full_name, role, profile_picture, party_role, party_role_sq, bio, bio_sq
        FROM users
        WHERE is_public = 1 AND status = 'approved'
        ORDER BY role DESC, username ASC
        LIMIT ? OFFSET ?
    ");
    $stmt->bindValue(1, $per_page, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $profiles = $stmt->fetchAll();
}

$lang = current_lang();
$page_title = $t_menu_profiles . ' - ' . $t_site_title;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <main id="main-content" class="container main-content">
        <h1><?= htmlspecialchars($t_menu_profiles, ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars(__('public_profiles_intro'), ENT_QUOTES, 'UTF-8') ?></p>

        <?php if (empty($profiles)): ?>
            <p><?= htmlspecialchars(__('no_public_profiles'), ENT_QUOTES, 'UTF-8') ?></p>
        <?php else: ?>
            <div class="profiles-grid">
                <?php foreach ($profiles as $profile):
                    $display_name = !empty(trim((string)($profile['full_name'] ?? '')))
                        ? $profile['full_name']
                        : $profile['username'];

                    $custom_role = ($lang === 'sq' && !empty(trim((string)($profile['party_role_sq'] ?? ''))))
                        ? $profile['party_role_sq']
                        : ($profile['party_role'] ?? '');
                    $display_role = !empty(trim((string)$custom_role))
                        ? $custom_role
                        : ($profile['role'] === 'admin' ? __('role_admin') : __('role_member'));

                    $display_bio = ($lang === 'sq' && !empty(trim((string)($profile['bio_sq'] ?? ''))))
                        ? $profile['bio_sq']
                        : ($profile['bio'] ?? '');
                ?>
                    <article class="profile-card">
                        <?php if (!empty($profile['profile_picture'])): ?>
                            <img src="uploads/<?= htmlspecialchars(basename($profile['profile_picture']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8') ?>" class="profile-picture" width="104" height="104" loading="lazy">
                        <?php else: ?>
                            <div class="profile-picture" aria-hidden="true"><?= htmlspecialchars(__('no_image'), ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="profile-role"><?= htmlspecialchars($display_role, ENT_QUOTES, 'UTF-8') ?></p>
                        <?php if (!empty(trim((string)$display_bio))): ?>
                            <p class="profile-bio"><?= nl2br(htmlspecialchars((string)$display_bio, ENT_QUOTES, 'UTF-8'), false) ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <nav class="pagination" aria-label="Pagination">
                    <?php if ($page > 1): ?>
                        <a href="public_profiles.php?page=<?= $page - 1 ?>" class="btn btn-sm btn-secondary">&larr; <?= htmlspecialchars(__('page_prev'), ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endif; ?>
                    <span class="text-muted"><?= htmlspecialchars(__('page_of', ['page' => $page, 'total' => $total_pages]), ENT_QUOTES, 'UTF-8') ?></span>
                    <?php if ($page < $total_pages): ?>
                        <a href="public_profiles.php?page=<?= $page + 1 ?>" class="btn btn-sm btn-secondary"><?= htmlspecialchars(__('page_next'), ENT_QUOTES, 'UTF-8') ?> &rarr;</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
