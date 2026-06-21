<?php
require_once '../includes/db.php';
require_once 'admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['message_id'])) {
    if ($_POST['action'] === 'delete') {
        if ($pdo) {
            $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
            $stmt->execute([$_POST['message_id']]);
        }
    }
    header('Location: messages.php');
    exit;
}

$messages = [];
if ($pdo) {
    $messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
} else {
    // Mock data for test environment
    $messages = [
        ['id' => 1, 'name' => 'Alice Johnson', 'email' => 'alice@example.com', 'message' => 'I would love to learn more about volunteering opportunities in Tirana.', 'created_at' => date('Y-m-d H:i:s')],
        ['id' => 2, 'name' => 'Bob Williams', 'email' => 'bob@example.com', 'message' => 'The Green Energy Initiative is exactly what we need. How can I contribute?', 'created_at' => date('Y-m-d H:i:s')]
    ];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Messages</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { display: flex; margin: 0; }
        .admin-content { flex: 1; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f4f4f4; }
        .message-content { white-space: pre-wrap; word-break: break-word; }
    </style>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    <div class="admin-content">
        <h1>Contact Messages</h1>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $m): ?>
                <tr>
                    <td><?= $m['created_at'] ?></td>
                    <td><?= htmlspecialchars($m['name']) ?></td>
                    <td><a href="mailto:<?= htmlspecialchars($m['email']) ?>"><?= htmlspecialchars($m['email']) ?></a></td>
                    <td class="message-content"><?= htmlspecialchars($m['message']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="message_id" value="<?= $m['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($messages)): ?>
                <tr><td colspan="5">No messages found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="../assets/js/main.js"></script>
</body>
</html>