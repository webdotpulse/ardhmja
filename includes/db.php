<?php
/**
 * Bootstrap & Database Connection for AsamblejaKombetare.
 * Loads security headers/session, localization, database connection, non-destructive
 * schema migrations, live session validation, and bilingual site settings.
 */

$local_config = __DIR__ . '/config.local.php';
if (is_file($local_config)) {
    require_once $local_config;
}

// Fallback credentials if `includes/config.local.php` is not yet created
$host = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: 'localhost');
$dbname = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'ardhmja');
$username = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: 'root');
$password = defined('DB_PASS') ? DB_PASS : (getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

if (!defined('DB_NAME')) {
    define('DB_NAME', $dbname);
}

// SMTP Configuration
if (!defined('SMTP_HOST')) define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp-auth.mailprotect.be');
if (!defined('SMTP_USERNAME')) define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: 'noreply@asamblejakombetare.al');
if (!defined('SMTP_PASSWORD')) define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: '');
if (!defined('SMTP_PORT')) define('SMTP_PORT', (int)(getenv('SMTP_PORT') ?: 587));
if (!defined('SMTP_ENCRYPTION')) define('SMTP_ENCRYPTION', getenv('SMTP_ENCRYPTION') ?: 'tls');
if (!defined('SMTP_FROM_EMAIL')) define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: SMTP_USERNAME);
if (!defined('SMTP_FROM_NAME')) define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'AsamblejaKombetare');
if (!defined('ADMIN_NOTIFICATION_EMAIL')) define('ADMIN_NOTIFICATION_EMAIL', getenv('ADMIN_NOTIFICATION_EMAIL') ?: '');

require_once __DIR__ . '/security.php';
require_once __DIR__ . '/i18n.php';
require_once __DIR__ . '/migrations.php';

init_session_and_headers();
$app_lang = current_lang();

if (!isset($pdo)) {
    try {
        $pdo = new PDO(
            "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (Throwable $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        $pdo = null;
    }
}

// Fetch global settings & run one-time non-destructive migration if needed
$global_settings = [];
if ($pdo instanceof PDO) {
    try {
        $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
        if ($stmt) {
            $global_settings = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }
        ensure_schema_up_to_date($pdo, $global_settings);
        refresh_authenticated_user($pdo);
    } catch (Throwable $e) {
        error_log('Database bootstrap query failed: ' . $e->getMessage());
    }
}

// Helper to resolve localized setting with graceful fallback to existing content
function setting_localized(string $key, array $settings, array $en_defaults, array $sq_defaults, string $lang): string {
    if ($key === 'site_title') {
        $raw = trim((string)($settings['site_title'] ?? ''));
        if ($raw === '' || in_array($raw, ['Frymaere', 'Ardhmja'], true)) {
            return $en_defaults['site_title'];
        }
        return $raw;
    }

    if ($lang === 'sq') {
        $sq_key = $key . '_sq';
        $sq_val = trim((string)($settings[$sq_key] ?? ''));
        if ($sq_val !== '') {
            return $sq_val;
        }
        if (isset($sq_defaults[$sq_key])) {
            return $sq_defaults[$sq_key];
        }
    }

    $en_val = trim((string)($settings[$key] ?? ''));
    if ($en_val !== '') {
        return $en_val;
    }

    return $en_defaults[$key] ?? '';
}

$_en_defaults = default_settings_en();
$_sq_defaults = default_settings_sq();

$t_site_title = setting_localized('site_title', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_site_description = setting_localized('site_description', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_home_banner_text = setting_localized('home_banner_text', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_join_us_btn = setting_localized('join_us_btn', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_home_core_values_title = setting_localized('home_core_values_title', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_home_core_values_text = setting_localized('home_core_values_text', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_view_values_btn = setting_localized('view_values_btn', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_home_latest_ideas_title = setting_localized('home_latest_ideas_title', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_view_ideas_btn = setting_localized('view_ideas_btn', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_values_page_title = setting_localized('values_page_title', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_values_page_text = setting_localized('values_page_text', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_ideas_page_title = setting_localized('ideas_page_title', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_ideas_page_text = setting_localized('ideas_page_text', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_submit_idea_btn = setting_localized('submit_idea_btn', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_contact_page_title = setting_localized('contact_page_title', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_contact_page_text = setting_localized('contact_page_text', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_register_page_title = setting_localized('register_page_title', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_login_page_title = setting_localized('login_page_title', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_footer_text = setting_localized('footer_text', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_email_verification_subject = setting_localized('email_verification_subject', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_email_verification_message = setting_localized('email_verification_message', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);

$t_menu_home = setting_localized('menu_home', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_menu_values = setting_localized('menu_values', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_menu_ideas = setting_localized('menu_ideas', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_menu_profiles = setting_localized('menu_profiles', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_menu_contact = setting_localized('menu_contact', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_menu_profile = setting_localized('menu_profile', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_menu_admin = setting_localized('menu_admin', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_menu_logout = setting_localized('menu_logout', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_menu_login = setting_localized('menu_login', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);
$t_menu_register = setting_localized('menu_register', $global_settings, $_en_defaults, $_sq_defaults, $app_lang);

/**
 * Ensures `$pdo` is available before running mandatory DB operations; renders a clean
 * HTTP 503 maintenance page instead of throwing fatal errors or silently losing data.
 */
function require_db(?PDO $pdo, string $asset_prefix = ''): void {
    if ($pdo instanceof PDO) {
        return;
    }
    if (!headers_sent()) {
        http_response_code(503);
        header('Retry-After: 60');
    }
    global $t_site_title, $t_footer_text, $t_menu_home, $t_menu_values, $t_menu_ideas,
           $t_menu_profiles, $t_menu_contact, $t_menu_profile, $t_menu_admin,
           $t_menu_logout, $t_menu_login, $t_menu_register;
    $page_title = __('db_unavailable_title') . ' - ' . ($t_site_title ?? 'AsamblejaKombetare');
    ?>
    <!DOCTYPE html>
    <html lang="<?= htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8') ?>">
    <head>
        <?php include __DIR__ . '/head.php'; ?>
    </head>
    <body>
        <?php if ($asset_prefix === '') { include __DIR__ . '/header.php'; } ?>
        <main id="main-content" class="container main-content">
            <h2><?= htmlspecialchars(__('db_unavailable_title'), ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="info-box"><?= htmlspecialchars(__('db_unavailable_text'), ENT_QUOTES, 'UTF-8') ?></p>
        </main>
        <?php include __DIR__ . '/footer.php'; ?>
        <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars($asset_prefix, ENT_QUOTES, 'UTF-8') ?>assets/js/main.js"></script>
    </body>
    </html>
    <?php
    exit;
}
