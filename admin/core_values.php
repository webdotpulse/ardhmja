<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/admin_auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = __('csrf_error');
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'add') {
            $title = trim((string)($_POST['title'] ?? ''));
            $title_sq = trim((string)($_POST['title_sq'] ?? ''));
            $description = trim((string)($_POST['description'] ?? ''));
            $description_sq = trim((string)($_POST['description_sq'] ?? ''));

            if ($title === '' && $title_sq !== '') {
                $title = $title_sq;
            }

            if ($title !== '') {
                $stmt = $pdo->prepare("INSERT INTO core_values (title, title_sq, description, description_sq) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $title,
                    $title_sq !== '' ? $title_sq : null,
                    $description,
                    $description_sq !== '' ? $description_sq : null,
                ]);
                header('Location: core_values.php');
                exit;
            }
            $error = 'Title is required.';
        } elseif ($action === 'delete' && isset($_POST['value_id'])) {
            $stmt = $pdo->prepare("DELETE FROM core_values WHERE id = ?");
            $stmt->execute([(int)$_POST['value_id']]);
            header('Location: core_values.php');
            exit;
        }
    }
}

$values = $pdo->query("
    SELECT cv.*, COUNT(a.id) AS agreement_count
    FROM core_values cv
    LEFT JOIN agreements a ON a.value_id = cv.id
    GROUP BY cv.id
    ORDER BY cv.created_at DESC
")->fetchAll();

$page_title = 'Core Values - ' . $t_site_title;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/../includes/head.php'; ?>
</head>
<body>
    <div class="admin-wrapper">
        <?php include __DIR__ . '/admin_sidebar.php'; ?>
        <main id="main-content" class="admin-content">
            <h1>Core Values</h1>

            <?php if ($error): ?>
                <p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <div class="auth-form form-wide" style="margin-bottom: 28px;">
                <h2 style="margin-top:0;">Add New Core Value</h2>
                <form method="POST" action="core_values.php">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label for="cv_title_en">Title — English</label>
                        <input type="text" id="cv_title_en" name="title" maxlength="255" required>
                    </div>
                    <div class="form-group">
                        <label for="cv_title_sq">Title — Albanian / Shqip (optional, falls back to English if blank)</label>
                        <input type="text" id="cv_title_sq" name="title_sq" maxlength="255">
                    </div>
                    <div class="form-group">
                        <label for="cv_desc_en">Description — English</label>
                        <textarea id="cv_desc_en" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="cv_desc_sq">Description — Albanian / Shqip (optional)</label>
                        <textarea id="cv_desc_sq" name="description_sq" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn">Add Value</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title (EN / SQ)</th>
                            <th>Description</th>
                            <th>Supporters</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($values as $v):
                            $vid = (int)$v['id'];
                        ?>
                        <tr>
                            <td>
                                <strong>EN:</strong> <?= htmlspecialchars($v['title'], ENT_QUOTES, 'UTF-8') ?>
                                <?php if (!empty($v['title_sq'])): ?>
                                    <br><strong>SQ:</strong> <?= htmlspecialchars($v['title_sq'], ENT_QUOTES, 'UTF-8') ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= nl2br(htmlspecialchars((string)($v['description'] ?? ''), ENT_QUOTES, 'UTF-8'), false) ?>
                                <?php if (!empty($v['description_sq'])): ?>
                                    <hr style="border:none; border-top:1px solid #e2e8f0; margin:8px 0;">
                                    <em>SQ:</em> <?= nl2br(htmlspecialchars((string)$v['description_sq'], ENT_QUOTES, 'UTF-8'), false) ?>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge badge-neutral"><?= (int)$v['agreement_count'] ?></span></td>
                            <td><?= htmlspecialchars(format_date_localized($v['created_at'], 'en'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <form method="POST" action="core_values.php" style="display:flex; gap:6px; flex-wrap:wrap;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="value_id" value="<?= $vid ?>">
                                    <a href="edit_value.php?id=<?= $vid ?>" class="btn btn-sm">Edit</a>
                                    <button type="submit" class="btn btn-sm btn-danger" data-confirm="Delete this Core Value and its member endorsements?">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($values)): ?>
                        <tr><td colspan="5">No Core Values defined yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js', '../'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
