<?php
session_start();
require_once 'includes/db.php';

// Handle agreement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['value_id'])) {
    if (isset($_SESSION['user_id']) && $_SESSION['status'] === 'approved') {
        $value_id = $_POST['value_id'];
        $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("INSERT IGNORE INTO agreements (user_id, value_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $value_id]);

        header('Location: core_values.php?msg=agreed');
        exit;
    }
}

$values = [];
$agreed_values = [];
if ($pdo) {
    $values = $pdo->query("SELECT * FROM core_values ORDER BY created_at ASC")->fetchAll();

    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT value_id FROM agreements WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $agreed_values = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($t_values_page_title) ?> - <?= htmlspecialchars($t_site_title) ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container main-content">
        <h2><?= htmlspecialchars($t_values_page_title) ?></h2>
        <p><?= $t_values_page_text ?></p>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'agreed'): ?>
            <p class="success">Thank you for supporting this value!</p>
        <?php endif; ?>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <p class="info-box">Log in and get approved to show your agreement with our core values.</p>
        <?php endif; ?>

        <div class="points-grid">
            <?php foreach ($values as $v): ?>
                <div class="point-card">
                    <h3><?= htmlspecialchars($v['title']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($v['description'])) ?></p>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (in_array($v['id'], $agreed_values)): ?>
                            <div class="badge badge-success">✓ I agree with this</div>
                        <?php elseif ($_SESSION['status'] === 'approved'): ?>
                            <form method="POST">
                                <input type="hidden" name="value_id" value="<?= $v['id'] ?>">
                                <button type="submit" class="btn btn-sm">I agree</button>
                            </form>
                        <?php else: ?>
                            <small class="text-muted">Account approval pending to agree.</small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if(empty($values)): ?>
                <p>No core values available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php include "includes/footer.php"; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>