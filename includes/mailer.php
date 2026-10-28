<?php
/**
 * Outgoing email & notification helpers for AsamblejaKombetare using PHPMailer.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

function send_app_email(string $to_email, string $to_name, string $subject, string $plain_body, ?string $reply_to_email = null, ?string $reply_to_name = null): bool {
    // Allow test harness override
    if (isset($GLOBALS['__ak_test_mail_sink']) && is_callable($GLOBALS['__ak_test_mail_sink'])) {
        return (bool)call_user_func($GLOBALS['__ak_test_mail_sink'], $to_email, $to_name, $subject, $plain_body, $reply_to_email, $reply_to_name);
    }

    if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    // Strip any stray HTML tags if an admin entered rich text into an email template
    $clean_body = html_entity_decode(strip_tags($plain_body), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $clean_subject = trim(preg_replace('/[\r\n]+/', ' ', strip_tags($subject)));

    $mail = new PHPMailer(true);
    try {
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = (SMTP_PASSWORD !== '');
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port = (int)SMTP_PORT;
        $mail->Timeout = 10;

        $from_email = filter_var(SMTP_FROM_EMAIL, FILTER_VALIDATE_EMAIL) ? SMTP_FROM_EMAIL : 'info@asamblejakombetare.com';
        $from_name = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'AsamblejaKombetare';

        $mail->setFrom($from_email, $from_name);
        $mail->addAddress($to_email, $to_name);

        if ($reply_to_email && filter_var($reply_to_email, FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo($reply_to_email, $reply_to_name ?? '');
        }

        $mail->isHTML(false);
        $mail->Subject = $clean_subject;
        $mail->Body = $clean_body;

        return $mail->send();
    } catch (MailException $e) {
        error_log('PHPMailer error sending to ' . $to_email . ': ' . $mail->ErrorInfo);
        return false;
    } catch (Throwable $e) {
        error_log('Unexpected mail error: ' . $e->getMessage());
        return false;
    }
}

function get_admin_notification_recipients(?PDO $pdo): array {
    $recipients = [];

    if (defined('ADMIN_NOTIFICATION_EMAIL') && trim(ADMIN_NOTIFICATION_EMAIL) !== '') {
        foreach (explode(',', ADMIN_NOTIFICATION_EMAIL) as $addr) {
            $addr = trim($addr);
            if (filter_var($addr, FILTER_VALIDATE_EMAIL)) {
                $recipients[strtolower($addr)] = $addr;
            }
        }
    }

    if (empty($recipients) && $pdo instanceof PDO) {
        try {
            $stmt = $pdo->query("SELECT email FROM users WHERE role = 'admin' AND status = 'approved' AND email_verified = 1");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $addr = trim((string)($row['email'] ?? ''));
                if ($addr !== '' && strtolower($addr) !== 'admin@frymaere.al' && filter_var($addr, FILTER_VALIDATE_EMAIL)) {
                    $recipients[strtolower($addr)] = $addr;
                }
            }
        } catch (Throwable $e) {
            error_log('Failed to query admin notification emails: ' . $e->getMessage());
        }
    }

    return array_values($recipients);
}

function notify_admins(?PDO $pdo, string $subject, string $body, ?string $reply_to_email = null, ?string $reply_to_name = null): void {
    $recipients = get_admin_notification_recipients($pdo);
    foreach ($recipients as $admin_email) {
        send_app_email($admin_email, 'Admin', $subject, $body, $reply_to_email, $reply_to_name);
    }
}

function send_verification_email_to_user(string $email, string $username, string $verify_token, string $site_title, string $subject_tpl, string $message_tpl): bool {
    $verify_link = app_base_url() . '/verify.php?token=' . urlencode($verify_token);
    $replacements_from = ['{site_title}', '{username}', '{verify_link}'];
    $replacements_to = [$site_title, $username, $verify_link];

    $subject = str_replace($replacements_from, $replacements_to, $subject_tpl);
    $message = str_replace($replacements_from, $replacements_to, $message_tpl);

    return send_app_email($email, $username, $subject, $message);
}
