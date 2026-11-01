<?php
/**
 * Dynamic robots.txt endpoint (routed via `.htaccess` RewriteRule ^robots\.txt$ robots.php [L]).
 * Emits a valid RFC 9309 robots.txt with an absolute Sitemap URL matching the active host.
 */

$local_config = __DIR__ . '/includes/config.local.php';
if (is_file($local_config)) {
    require_once $local_config;
}

$debug = defined('APP_DEBUG') && APP_DEBUG === true;
if (!$debug) {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
}

require_once __DIR__ . '/includes/security.php';

if (!headers_sent()) {
    header('Content-Type: text/plain; charset=UTF-8');
    header('Cache-Control: public, max-age=3600');
    if (is_https_request()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
    $req_path = (string)(parse_url((string)($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?? '');
    if (substr($req_path, -4) === '.php') {
        header('X-Robots-Tag: noindex');
    }
}

$sitemap_url = seo_base_url() . '/sitemap.xml';

echo "User-agent: *\n";
echo "Disallow: /admin/\n";
echo "Disallow: /includes/\n";
echo "Disallow: /vendor/\n";
echo "Disallow: /profile.php\n";
echo "Disallow: /change_password.php\n";
echo "Disallow: /change_username.php\n";
echo "Disallow: /change_email.php\n";
echo "Disallow: /delete_account.php\n";
echo "Disallow: /submit_idea.php\n";
echo "Disallow: /logout.php\n";
echo "Disallow: /verify.php\n";
echo "Disallow: /reset_password.php\n";
echo "\n";
echo "Sitemap: {$sitemap_url}\n";
