<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/admin_auth.php';

$value_id = (int)($_GET['id'] ?? 0);
if ($value_id <= 0) {
    header('Location: core_values.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = __('csrf_error');
    } else {
        $title = trim((string)($_POST['title'] ?? ''));
        $title_sq = trim((string)($_POST['title_sq'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $description_sq = trim((string)($_POST['description_sq'] ?? ''));

        if ($title === '' && $title_sq !== '') {
            $title = $title_sq;
        }

        if ($title === '') {
            $error = 'Title cannot be empty.';
        } else {
            $stmt = $pdo->prepare("UPDATE core_values SET title = ?, title_sq = ?, description = ?, description_sq = ? WHERE id = ?");
            if ($stmt->execute([
                $title,
                $title_sq !== '' ? $title_sq : null,
                $description,
                $description_sq !== '' ? $description_sq : null,
                $value_id,
            ])) {
                $success = 'Core value updated successfully.';
            } else {
                $error = 'Failed to update core value.';
            }
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM core_values WHERE id = ?");
$stmt->execute([$value_id]);
$value = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$value) {
    header('Location: core_values.php');
    exit;
}

$page_title = 'Edit Core Value - ' . $t_site_title;
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
            <h1>Edit Core Value</h1>
            <p><a href="core_values.php" class="btn btn-sm btn-secondary">&larr; Back to Core Values</a></p>

            <?php if ($error): ?><p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if ($success): ?><p class="success" role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

            <form method="POST" action="edit_value.php?id=<?= $value_id ?>" class="auth-form form-wide">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="title_en">Title — English</label>
                    <input type="text" id="title_en" name="title" maxlength="255" value="<?= htmlspecialchars((string)$value['title'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group">
                    <label for="title_sq">Title — Albanian / Shqip (optional)</label>
                    <input type="text" id="title_sq" name="title_sq" maxlength="255" value="<?= htmlspecialchars((string)($value['title_sq'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                    <label for="desc_en">Description — English</label>
                    <textarea id="desc_en" name="description" rows="5"><?= htmlspecialchars((string)($value['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="desc_sq">Description — Albanian / Shqip (optional)</label>
                    <textarea id="desc_sq" name="description_sq" rows="5"><?= htmlspecialchars((string)($value['description_sq'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </main>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="../assets/js/main.js"></script>
</body>
</html>
