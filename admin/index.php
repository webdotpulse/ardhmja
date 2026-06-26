<?php
require_once '../includes/db.php';
require_once 'admin_auth.php';

// Get counts
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pending_users = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();
$ideas_count = $pdo->query("SELECT COUNT(*) FROM ideas")->fetchColumn();
$pending_ideas = $pdo->query("SELECT COUNT(*) FROM ideas WHERE status = 'pending'")->fetchColumn();
$points_count = $pdo->query("SELECT COUNT(*) FROM party_points")->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; margin: 0; }
        .admin-wrapper { display: flex; flex: 1; }
        .admin-content { flex: 1; padding: 20px; }
        .dashboard-cards { display: flex; gap: 20px; }
        .card { background: #f4f4f4; padding: 20px; border-radius: 5px; flex: 1; text-align: center; }
        .card h3 { margin-top: 0; }
        .card .number { font-size: 2em; font-weight: bold; color: #0033a0; }
    </style>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
</head>
<body>
    <div class="admin-wrapper">
    <?php include 'admin_sidebar.php'; ?>
    <div class="admin-content">
        <h1>Dashboard</h1>
        <a href="../change_password.php" class="btn btn-sm" style="float: right;">Change Password</a>
        <div class="dashboard-cards">
            <div class="card">
                <h3>Users</h3>
                <div class="number"><?= $users_count ?></div>
                <p><?= $pending_users ?> pending</p>
            </div>
            <div class="card">
                <h3>Ideas</h3>
                <div class="number"><?= $ideas_count ?></div>
                <p><?= $pending_ideas ?> pending</p>
            </div>
            <div class="card">
                <h3>Party Points</h3>
                <div class="number"><?= $points_count ?></div>
            </div>
        </div>
    </div>
    </div>
    <?php include "../includes/footer.php"; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>