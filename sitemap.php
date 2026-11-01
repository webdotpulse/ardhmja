<?php
/**
 * Dynamic XML Sitemap 0.9 + XHTML hreflang endpoint (routed via `.htaccess` ^sitemap\.xml$).
 * Runs statelessly without sessions, cookies, or Clear-Site-Data headers.
 */

define('AK_STATELESS_REQUEST', true);
require_once __DIR__ . '/includes/db.php';

if (!headers_sent()) {
    header('Content-Type: application/xml; charset=UTF-8');
    header('Cache-Control: public, max-age=3600');
    if (is_https_request()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
    $req_path = (string)(parse_url((string)($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?? '');
    if (substr($req_path, -4) === '.php') {
        header('X-Robots-Tag: noindex');
    }
}

$latest_value_at = null;
$latest_idea_at = null;
$latest_profile_at = null;

if ($pdo instanceof PDO) {
    try {
        $latest_value_at = (string)$pdo->query("SELECT MAX(created_at) FROM core_values")->fetchColumn() ?: null;
    } catch (Throwable $e) {
        $latest_value_at = null;
    }

    try {
        $latest_idea_at = (string)$pdo->query("SELECT MAX(created_at) FROM ideas WHERE status = 'approved'")->fetchColumn() ?: null;
    } catch (Throwable $e) {
        $latest_idea_at = null;
    }

    try {
        $latest_profile_at = (string)$pdo->query("SELECT MAX(created_at) FROM users WHERE is_public = 1 AND status = 'approved'")->fetchColumn() ?: null;
    } catch (Throwable $e) {
        $latest_profile_at = null;
    }
}

$resolve_lastmod = static function (string $relative_file, ?string ...$db_timestamps): string {
    $now = time();
    $file_path = __DIR__ . DIRECTORY_SEPARATOR . $relative_file;
    $best = is_file($file_path) ? (int)@filemtime($file_path) : 0;

    foreach ($db_timestamps as $dt) {
        if ($dt !== null && $dt !== '') {
            $parsed = strtotime($dt);
            if ($parsed !== false && $parsed > $best) {
                $best = $parsed;
            }
        }
    }

    if ($best <= 0 || $best > $now) {
        $best = $now;
    }

    return gmdate('Y-m-d', $best);
};

$base = seo_base_url();

$routes = [
    [
        'path' => '/',
        'lastmod' => $resolve_lastmod('index.php', $latest_value_at, $latest_idea_at),
        'changefreq' => 'daily',
        'priority' => '1.0',
    ],
    [
        'path' => '/core_values.php',
        'lastmod' => $resolve_lastmod('core_values.php', $latest_value_at),
        'changefreq' => 'weekly',
        'priority' => '0.9',
    ],
    [
        'path' => '/ideas.php',
        'lastmod' => $resolve_lastmod('ideas.php', $latest_idea_at),
        'changefreq' => 'daily',
        'priority' => '0.8',
    ],
    [
        'path' => '/public_profiles.php',
        'lastmod' => $resolve_lastmod('public_profiles.php', $latest_profile_at),
        'changefreq' => 'weekly',
        'priority' => '0.7',
    ],
    [
        'path' => '/register.php',
        'lastmod' => $resolve_lastmod('register.php'),
        'changefreq' => 'monthly',
        'priority' => '0.7',
    ],
    [
        'path' => '/contact.php',
        'lastmod' => $resolve_lastmod('contact.php'),
        'changefreq' => 'monthly',
        'priority' => '0.7',
    ],
    [
        'path' => '/privacy.php',
        'lastmod' => $resolve_lastmod('privacy.php'),
        'changefreq' => 'yearly',
        'priority' => '0.4',
    ],
    [
        'path' => '/terms.php',
        'lastmod' => $resolve_lastmod('terms.php'),
        'changefreq' => 'yearly',
        'priority' => '0.4',
    ],
];

$xml_escape = static function (string $value): string {
    return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
};

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
<?php foreach ($routes as $route):
    $sq_url = $base . $route['path'];
    $en_url = $base . $route['path'] . '?lang=en';
    foreach ([$sq_url, $en_url] as $loc_url):
?>
    <url>
        <loc><?= $xml_escape($loc_url) ?></loc>
        <lastmod><?= $xml_escape($route['lastmod']) ?></lastmod>
        <changefreq><?= $xml_escape($route['changefreq']) ?></changefreq>
        <priority><?= $xml_escape($route['priority']) ?></priority>
        <xhtml:link rel="alternate" hreflang="sq" href="<?= $xml_escape($sq_url) ?>" />
        <xhtml:link rel="alternate" hreflang="en" href="<?= $xml_escape($en_url) ?>" />
        <xhtml:link rel="alternate" hreflang="x-default" href="<?= $xml_escape($sq_url) ?>" />
    </url>
<?php
    endforeach;
endforeach; ?>
</urlset>
