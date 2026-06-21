<?php
session_start();
require_once 'includes/db.php';

// Fetch users who are either admins or have been made public by an admin
$stmt = $pdo->query("SELECT username, role, profile_picture FROM users WHERE role = 'admin' OR is_public = 1 ORDER BY role ASC, username ASC");
$profiles = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Public Profiles - <?= htmlspecialchars($t_site_title) ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .profiles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .profile-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            background-color: #eee;
            display: inline-block;
        }
        .profile-card h3 {
            margin: 0 0 5px 0;
            font-size: 1.2em;
        }
        .profile-card p {
            margin: 0;
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container main-content">
        <h2>Public Profiles</h2>
        <p>Meet our team members and public figures.</p>

        <?php if (empty($profiles)): ?>
            <p>No public profiles available at the moment.</p>
        <?php else: ?>
            <div class="profiles-grid">
                <?php foreach ($profiles as $profile): ?>
                    <div class="profile-card">
                        <?php if ($profile['profile_picture']): ?>
                            <img src="uploads/<?= htmlspecialchars($profile['profile_picture']) ?>" alt="Profile Picture" class="profile-picture">
                        <?php else: ?>
                            <div class="profile-picture" style="line-height: 100px; color: #999;">No Image</div>
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($profile['username']) ?></h3>
                        <p><?= $profile['role'] === 'admin' ? 'Admin' : 'Member' ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>
