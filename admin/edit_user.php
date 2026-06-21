<?php
require_once '../includes/db.php';
require_once 'admin_auth.php';

if (!isset($_GET['id'])) {
    header('Location: users.php');
    exit();
}

$user_id = $_GET['id'];

// Handle form submission
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_public = isset($_POST['is_public']) ? 1 : 0;

    // Handle file upload
    $profile_picture = null;
    $upload_dir = '../uploads/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['profile_picture']['tmp_name'];
        $name = basename($_FILES['profile_picture']['name']);
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed_exts)) {
            $new_filename = uniqid() . '_' . $user_id . '.' . $ext;
            if (move_uploaded_file($tmp_name, $upload_dir . $new_filename)) {
                $profile_picture = $new_filename;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        }
    }

    if (!$error) {
        if ($profile_picture) {
            $stmt = $pdo->prepare("UPDATE users SET is_public = ?, profile_picture = ? WHERE id = ?");
            $stmt->execute([$is_public, $profile_picture, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET is_public = ? WHERE id = ?");
            $stmt->execute([$is_public, $user_id]);
        }
        $success = "User updated successfully.";
    }
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: users.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { display: flex; margin: 0; }
        .admin-content { flex: 1; padding: 20px; }
        .edit-form { max-width: 500px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;}
        .edit-form label { display: block; margin-bottom: 5px; font-weight: bold; }
        .edit-form input[type="file"], .edit-form input[type="checkbox"] { margin-bottom: 15px; }
        .current-picture { width: 100px; height: 100px; object-fit: cover; border-radius: 50%; margin-bottom: 15px; display: block; background: #eee;}
    </style>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    <div class="admin-content">
        <h1>Edit User: <?= htmlspecialchars($user['username']) ?></h1>
        <a href="users.php" class="btn btn-sm" style="margin-bottom: 20px;">&larr; Back to Users</a>

        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="edit-form">
            <div class="form-group">
                <label>Current Profile Picture</label>
                <?php if ($user['profile_picture']): ?>
                    <img src="../uploads/<?= htmlspecialchars($user['profile_picture']) ?>" class="current-picture" alt="Profile Picture">
                <?php else: ?>
                    <div class="current-picture" style="line-height: 100px; text-align: center; color: #999;">No Image</div>
                <?php endif; ?>

                <label for="profile_picture">Upload New Picture</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_public" value="1" <?= $user['is_public'] ? 'checked' : '' ?>>
                    Show this user on the Public Profiles page
                </label>
            </div>

            <button type="submit" class="btn">Save Changes</button>
        </form>
    </div>
    <script src="../assets/js/main.js"></script>
</body>
</html>
