<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/admin_auth.php';

$users_count = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pending_users = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();
$ideas_count = (int)$pdo->query("SELECT COUNT(*) FROM ideas")->fetchColumn();
$pending_ideas = (int)$pdo->query("SELECT COUNT(*) FROM ideas WHERE status = 'pending'")->fetchColumn();
$values_count = (int)$pdo->query("SELECT COUNT(*) FROM core_values")->fetchColumn();
$messages_count = (int)$pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();

// Check for default bootstrap credentials or unconfigured SMTP
$stmt = $pdo->prepare("SELECT password, email FROM users WHERE id = ?");
$stmt->execute([(int)$_SESSION['user_id']]);
$me = $stmt->fetch(PDO::FETCH_ASSOC);

$has_default_password = $me && password_verify('password', (string)$me['password']);
$has_placeholder_email = $me && in_array(strtolower((string)$me['email']), ['admin@frymaere.al', 'admin@asamblejakombetare.al'], true);
$smtp_unconfigured = (SMTP_PASSWORD === '');

$page_title = 'Admin Dashboard - ' . $t_site_title;
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
            <div class="flex-header" style="margin-bottom: 20px;">
                <h1 style="margin:0;">Dashboard</h1>
                <div class="profile-actions">
                    <a href="../change_email.php" class="btn btn-sm btn-secondary">Change Email</a>
                    <a href="../change_password.php" class="btn btn-sm btn-secondary">Change Password</a>
                </div>
            </div>

            <?php if ($has_default_password || $has_placeholder_email): ?>
                <div class="warning-banner" role="alert">
                    <strong>Security Action Required:</strong>
                    <?php if ($has_default_password): ?>
                        Your administrator account is still using the default bootstrap password. Please <a href="../change_password.php">change your password immediately</a>.
                    <?php endif; ?>
                    <?php if ($has_placeholder_email): ?>
                        Your administrator email is still set to <code><?= htmlspecialchars((string)$me['email'], ENT_QUOTES, 'UTF-8') ?></code>. Update it on <a href="../change_email.php">Change Email</a> so you receive admin notifications and password-reset emails.
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($smtp_unconfigured): ?>
                <div class="info-box" role="note">
                    <strong>SMTP Note:</strong> Outgoing SMTP password is currently empty. Create <code>includes/config.local.php</code> (from <code>includes/config.example.php</code>) on Combell with your mailbox credentials so verification and notification emails are delivered.
                </div>
            <?php endif; ?>

            <div class="dashboard-cards">
                <div class="admin-card">
                    <h3>Users</h3>
                    <div class="number"><?= $users_count ?></div>
                    <p><a href="users.php?status=pending"><?= $pending_users ?> pending approval</a></p>
                </div>
                <div class="admin-card">
                    <h3>Ideas</h3>
                    <div class="number"><?= $ideas_count ?></div>
                    <p><a href="ideas.php?status=pending"><?= $pending_ideas ?> pending moderation</a></p>
                </div>
                <div class="admin-card">
                    <h3>Core Values</h3>
                    <div class="number"><?= $values_count ?></div>
                    <p><a href="core_values.php">Manage values</a></p>
                </div>
                <div class="admin-card">
                    <h3>Messages</h3>
                    <div class="number"><?= $messages_count ?></div>
                    <p><a href="messages.php">View inbox</a></p>
                </div>
            </div>
        </main>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="../assets/js/main.js"></script>
</body>
</html>
