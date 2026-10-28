<?php
/**
 * Copy this file to `includes/config.local.php` on your Combell server
 * and fill in your production credentials.
 *
 * `includes/config.local.php` is gitignored so future deployments will not
 * overwrite your live database or SMTP settings.
 */

// Canonical public URL of the website (no trailing slash), e.g. 'https://asamblejakombetare.al'
define('APP_URL', 'https://yourdomain.com');

// Database credentials (from Combell Control Panel -> MySQL Databases)
define('DB_HOST', 'your-combell-db-host');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');

// Outgoing SMTP configuration (from Combell Control Panel -> Mailboxes / SMTP)
define('SMTP_HOST', 'smtp-auth.mailprotect.be');
define('SMTP_PORT', 587); // 587 or 2525 with 'tls', or 465 with 'ssl'
define('SMTP_ENCRYPTION', 'tls');
define('SMTP_USERNAME', 'noreply@yourdomain.com');
define('SMTP_PASSWORD', 'your_smtp_password');
define('SMTP_FROM_EMAIL', 'noreply@yourdomain.com');
define('SMTP_FROM_NAME', 'AsamblejaKombetare');

// Optional: comma-separated email(s) to receive admin alerts.
// Leave empty ('') to automatically notify all approved admin accounts in the database.
define('ADMIN_NOTIFICATION_EMAIL', '');

// Keep false in production so PHP errors are logged instead of shown to visitors
define('APP_DEBUG', false);
