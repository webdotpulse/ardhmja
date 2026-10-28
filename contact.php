<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/mailer.php';

$error = '';
$success = '';
$name_val = '';
$email_val = '';
$message_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_db($pdo);

    $name_val = trim((string)($_POST['name'] ?? ''));
    $email_val = trim((string)($_POST['email'] ?? ''));
    $message_val = trim((string)($_POST['message'] ?? ''));
    $consent = !empty($_POST['privacy_consent']);

    if (!verify_csrf()) {
        $error = __('csrf_error');
    } elseif (!verify_honeypot()) {
        // Silently absorb automated spam bots without storing
        $success = __('contact_success');
    } elseif (!check_rate_limit($pdo, 'contact', 5, 3600)) {
        $error = __('rate_limit_error');
    } elseif ($name_val === '' || $email_val === '' || $message_val === '') {
        $error = __('fill_all_fields');
    } elseif (!filter_var($email_val, FILTER_VALIDATE_EMAIL) || mb_strlen($email_val, 'UTF-8') > 150) {
        $error = __('invalid_email');
    } elseif (mb_strlen($name_val, 'UTF-8') > 100 || mb_strlen($message_val, 'UTF-8') < 10 || mb_strlen($message_val, 'UTF-8') > 5000) {
        $error = __('contact_length_error');
    } elseif (!$consent) {
        $error = __('consent_required');
    } else {
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        if ($stmt->execute([$name_val, $email_val, $message_val])) {
            record_rate_limit($pdo, 'contact');
            $success = __('contact_success');

            $admin_link = app_base_url() . '/admin/messages.php';
            notify_admins(
                $pdo,
                "[{$t_site_title}] New contact message from {$name_val}",
                "You received a new message via the {$t_site_title} contact form:\n\nName: {$name_val}\nEmail: {$email_val}\n\nMessage:\n{$message_val}\n\nView in Admin Panel:\n{$admin_link}",
                $email_val,
                $name_val
            );

            $name_val = '';
            $email_val = '';
            $message_val = '';
        } else {
            $error = __('generic_error');
        }
    }
}

$page_title = $t_contact_page_title . ' - ' . $t_site_title;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <main id="main-content" class="container main-content">
        <h1><?= htmlspecialchars($t_contact_page_title, ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="rich-text-content"><?= sanitize_rich_html($t_contact_page_text) ?></div>

        <?php if ($error): ?>
            <p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success" role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
        <?php else: ?>
            <form method="POST" action="contact.php" class="auth-form form-wide">
                <?= csrf_field() ?>
                <?= honeypot_field() ?>
                <div class="form-group">
                    <label for="name"><?= htmlspecialchars(__('contact_name_label'), ENT_QUOTES, 'UTF-8') ?></label>
                    <input type="text" id="name" name="name" maxlength="100" autocomplete="name" value="<?= htmlspecialchars($name_val, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group">
                    <label for="email"><?= htmlspecialchars(__('contact_email_label'), ENT_QUOTES, 'UTF-8') ?></label>
                    <input type="email" id="email" name="email" maxlength="150" autocomplete="email" spellcheck="false" value="<?= htmlspecialchars($email_val, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group">
                    <label for="message"><?= htmlspecialchars(__('contact_message_label'), ENT_QUOTES, 'UTF-8') ?></label>
                    <textarea id="message" name="message" rows="6" minlength="10" maxlength="5000" required><?= htmlspecialchars($message_val, ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div class="form-group checkbox-group">
                    <label for="privacy_consent">
                        <input type="checkbox" id="privacy_consent" name="privacy_consent" value="1" required>
                        <span><?= __('contact_consent_html') ?></span>
                    </label>
                </div>
                <button type="submit" class="btn"><?= htmlspecialchars(__('contact_send_btn'), ENT_QUOTES, 'UTF-8') ?></button>
            </form>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="assets/js/main.js"></script>
</body>
</html>
