<?php
require_once __DIR__ . '/includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['value_id'])) {
    require_db($pdo);
    if (!verify_csrf()) {
        $error = __('csrf_error');
    } elseif (!empty($_SESSION['user_id']) && ($_SESSION['status'] ?? '') === 'approved') {
        $value_id = (int)$_POST['value_id'];
        $user_id = (int)$_SESSION['user_id'];
        $mode = $_POST['mode'] ?? 'agree';

        if ($value_id > 0) {
            if ($mode === 'withdraw') {
                $stmt = $pdo->prepare("DELETE FROM agreements WHERE user_id = ? AND value_id = ?");
                $stmt->execute([$user_id, $value_id]);
                header('Location: core_values.php?msg=withdrawn');
                exit;
            } else {
                $stmt = $pdo->prepare("INSERT IGNORE INTO agreements (user_id, value_id) VALUES (?, ?)");
                $stmt->execute([$user_id, $value_id]);
                header('Location: core_values.php?msg=agreed');
                exit;
            }
        }
    }
}

$values = [];
$agreed_values = [];
$agreement_counts = [];

if ($pdo instanceof PDO) {
    $values = $pdo->query("SELECT * FROM core_values ORDER BY created_at ASC")->fetchAll();

    $count_rows = $pdo->query("SELECT value_id, COUNT(*) AS cnt FROM agreements GROUP BY value_id")->fetchAll();
    foreach ($count_rows as $cr) {
        $agreement_counts[(int)$cr['value_id']] = (int)$cr['cnt'];
    }

    if (!empty($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT value_id FROM agreements WHERE user_id = ?");
        $stmt->execute([(int)$_SESSION['user_id']]);
        $agreed_values = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
}

$lang = current_lang();
$page_title = $t_values_page_title . ' - ' . $t_site_title;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <main id="main-content" class="container main-content">
        <h1><?= htmlspecialchars($t_values_page_title, ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="rich-text-content"><?= sanitize_rich_html($t_values_page_text) ?></div>

        <?php if ($error): ?>
            <p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <?php if (($_GET['msg'] ?? '') === 'agreed'): ?>
            <p class="success" role="status"><?= htmlspecialchars(__('agreed_flash'), ENT_QUOTES, 'UTF-8') ?></p>
        <?php elseif (($_GET['msg'] ?? '') === 'withdrawn'): ?>
            <p class="success" role="status"><?= htmlspecialchars(__('withdrawn_flash'), ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if (empty($_SESSION['user_id'])): ?>
            <p class="info-box"><?= htmlspecialchars(__('login_to_agree_info'), ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <div class="points-grid">
            <?php foreach ($values as $v):
                $vid = (int)$v['id'];
                $v_title = ($lang === 'sq' && !empty(trim((string)($v['title_sq'] ?? '')))) ? $v['title_sq'] : $v['title'];
                $v_desc = ($lang === 'sq' && !empty(trim((string)($v['description_sq'] ?? '')))) ? $v['description_sq'] : ($v['description'] ?? '');
                $supporters = $agreement_counts[$vid] ?? 0;
                $supporters_label = ($supporters === 1)
                    ? __('members_agree_one')
                    : __('members_agree_many', ['count' => $supporters]);
            ?>
                <article class="point-card">
                    <div>
                        <h3><?= htmlspecialchars((string)$v_title, ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= nl2br(htmlspecialchars((string)$v_desc, ENT_QUOTES, 'UTF-8'), false) ?></p>
                    </div>

                    <div class="point-card-footer">
                        <span class="badge badge-neutral"><?= htmlspecialchars($supporters_label, ENT_QUOTES, 'UTF-8') ?></span>

                        <?php if (!empty($_SESSION['user_id'])): ?>
                            <?php if (in_array($vid, $agreed_values, true)): ?>
                                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                    <span class="badge badge-success"><?= htmlspecialchars(__('agreed_badge'), ENT_QUOTES, 'UTF-8') ?></span>
                                    <form method="POST" action="core_values.php" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="value_id" value="<?= $vid ?>">
                                        <input type="hidden" name="mode" value="withdraw">
                                        <button type="submit" class="btn btn-sm btn-secondary"><?= htmlspecialchars(__('withdraw_agreement'), ENT_QUOTES, 'UTF-8') ?></button>
                                    </form>
                                </div>
                            <?php elseif (($_SESSION['status'] ?? '') === 'approved'): ?>
                                <form method="POST" action="core_values.php" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="value_id" value="<?= $vid ?>">
                                    <input type="hidden" name="mode" value="agree">
                                    <button type="submit" class="btn btn-sm"><?= htmlspecialchars(__('i_agree'), ENT_QUOTES, 'UTF-8') ?></button>
                                </form>
                            <?php else: ?>
                                <small class="text-muted"><?= htmlspecialchars(__('approval_pending_to_agree'), ENT_QUOTES, 'UTF-8') ?></small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
            <?php if (empty($values)): ?>
                <p><?= htmlspecialchars(__('no_core_values'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="assets/js/main.js"></script>
</body>
</html>
