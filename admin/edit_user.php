<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/admin_auth.php';

$user_id = (int)($_GET['id'] ?? 0);
if ($user_id <= 0) {
    header('Location: users.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: users.php');
    exit;
}

$success = '';
$error = '';
$upload_dir = __DIR__ . '/../uploads';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = __('csrf_error');
    } else {
        $is_public = isset($_POST['is_public']) ? 1 : 0;
        $remove_picture = !empty($_POST['remove_picture']);
        $full_name = trim((string)($_POST['full_name'] ?? ''));
        $party_role = trim((string)($_POST['party_role'] ?? ''));
        $party_role_sq = trim((string)($_POST['party_role_sq'] ?? ''));
        $bio = trim((string)($_POST['bio'] ?? ''));
        $bio_sq = trim((string)($_POST['bio_sq'] ?? ''));

        if (mb_strlen($full_name, 'UTF-8') > 120 || mb_strlen($party_role, 'UTF-8') > 120 || mb_strlen($party_role_sq, 'UTF-8') > 120) {
            $error = 'Name and role titles must be 120 characters or fewer.';
        } elseif (mb_strlen($bio, 'UTF-8') > 2000 || mb_strlen($bio_sq, 'UTF-8') > 2000) {
            $error = 'Biography text must be 2,000 characters or fewer.';
        }

        $new_picture = $user['profile_picture'] ?? null;

        if (!$error && $remove_picture && $new_picture) {
            delete_uploaded_avatar($new_picture, $upload_dir);
            $new_picture = null;
        }

        if (!$error && isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file_err = (int)$_FILES['profile_picture']['error'];
            if ($file_err !== UPLOAD_ERR_OK) {
                $error = 'Image upload failed (the file may exceed the maximum upload size of 2 MB).';
            } elseif ((int)$_FILES['profile_picture']['size'] > 2 * 1024 * 1024) {
                $error = 'Profile picture must be 2 MB or smaller.';
            } else {
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $tmp_name = $_FILES['profile_picture']['tmp_name'];
                $orig_name = basename((string)$_FILES['profile_picture']['name']);
                $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));

                $allowed_map = [
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                ];

                if (!isset($allowed_map[$ext])) {
                    $error = 'Invalid file extension. Allowed formats: JPG, PNG, GIF, WebP.';
                } else {
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime_type = $finfo->file($tmp_name);

                    if (!in_array($mime_type, array_values($allowed_map), true)) {
                        $error = 'Invalid image content. Only valid JPG, PNG, GIF, or WebP files are allowed.';
                    } else {
                        $generated = bin2hex(random_bytes(12)) . '_' . $user_id . '.' . $ext;
                        if (move_uploaded_file($tmp_name, $upload_dir . DIRECTORY_SEPARATOR . $generated)) {
                            if (!empty($user['profile_picture']) && $user['profile_picture'] !== $generated) {
                                delete_uploaded_avatar($user['profile_picture'], $upload_dir);
                            }
                            $new_picture = $generated;
                        } else {
                            $error = 'Failed to save uploaded image on server.';
                        }
                    }
                }
            }
        }

        if (!$error) {
            $upd = $pdo->prepare("
                UPDATE users
                SET is_public = ?, profile_picture = ?, full_name = ?, party_role = ?, party_role_sq = ?, bio = ?, bio_sq = ?
                WHERE id = ?
            ");
            $upd->execute([
                $is_public,
                $new_picture,
                $full_name !== '' ? $full_name : null,
                $party_role !== '' ? $party_role : null,
                $party_role_sq !== '' ? $party_role_sq : null,
                $bio !== '' ? $bio : null,
                $bio_sq !== '' ? $bio_sq : null,
                $user_id,
            ]);
            $success = 'User profile updated successfully.';

            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}

$page_title = 'Edit User - ' . $t_site_title;
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
            <h1>Edit User: <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-muted">Email: <strong><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></strong> &middot; Status: <strong><?= htmlspecialchars($user['status'], ENT_QUOTES, 'UTF-8') ?></strong></p>
            <p><a href="users.php?status=<?= urlencode($user['status']) ?>" class="btn btn-sm btn-secondary">&larr; Back to Users</a></p>

            <?php if ($error): ?><p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if ($success): ?><p class="success" role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

            <form method="POST" action="edit_user.php?id=<?= $user_id ?>" enctype="multipart/form-data" class="auth-form form-wide">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label>Current Profile Picture</label>
                    <?php if (!empty($user['profile_picture'])): ?>
                        <img src="../uploads/<?= htmlspecialchars(basename($user['profile_picture']), ENT_QUOTES, 'UTF-8') ?>" class="profile-picture" style="margin:0 0 12px;" alt="Current avatar">
                        <div class="checkbox-group" style="margin-bottom: 12px;">
                            <label for="remove_picture">
                                <input type="checkbox" id="remove_picture" name="remove_picture" value="1">
                                <span>Remove current picture</span>
                            </label>
                        </div>
                    <?php else: ?>
                        <div class="profile-picture" style="margin:0 0 12px;"><?= htmlspecialchars(__('no_image'), ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>

                    <label for="profile_picture">Upload New Picture (JPG, PNG, GIF, WebP — max 2 MB)</label>
                    <input type="file" name="profile_picture" id="profile_picture" accept="image/jpeg,image/png,image/gif,image/webp">
                </div>

                <div class="form-group">
                    <label for="full_name">Display / Full Name (optional)</label>
                    <input type="text" id="full_name" name="full_name" maxlength="120" value="<?= htmlspecialchars((string)($user['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="form-group">
                    <label for="party_role">Role / Title — English (optional)</label>
                    <input type="text" id="party_role" name="party_role" maxlength="120" placeholder="e.g. Coordinator, Tirana" value="<?= htmlspecialchars((string)($user['party_role'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="form-group">
                    <label for="party_role_sq">Role / Title — Albanian / Shqip (optional)</label>
                    <input type="text" id="party_role_sq" name="party_role_sq" maxlength="120" placeholder="p.sh. Koordinator, Tiranë" value="<?= htmlspecialchars((string)($user['party_role_sq'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="form-group">
                    <label for="bio">Short Biography — English (optional)</label>
                    <textarea id="bio" name="bio" rows="3" maxlength="2000"><?= htmlspecialchars((string)($user['bio'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="bio_sq">Short Biography — Albanian / Shqip (optional)</label>
                    <textarea id="bio_sq" name="bio_sq" rows="3" maxlength="2000"><?= htmlspecialchars((string)($user['bio_sq'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-group checkbox-group">
                    <label for="is_public">
                        <input type="checkbox" id="is_public" name="is_public" value="1" <?= !empty($user['is_public']) ? 'checked' : '' ?>>
                        <span>Show this user on the Public Profiles page (requires Approved status)</span>
                    </label>
                </div>

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </main>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js', '../'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
