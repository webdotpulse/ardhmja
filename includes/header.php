<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="main-header">
    <div class="container header-container">
        <div class="logo">
            <a href="index.php"><h1>Ardhmja</h1></a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="points.php">Party Points</a></li>
                <li><a href="ideas.php">Ideas</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="admin/index.php">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="btn-logout">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="btn">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
