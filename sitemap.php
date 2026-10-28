<?php
require_once __DIR__ . '/includes/db.php';

if (!headers_sent()) {
    header('Content-Type: application/xml; charset=UTF-8');
}

$base = app_base_url();
$pages = [
    ['loc' => $base . '/index.php', 'priority' => '1.0', 'changefreq' => 'daily'],
    ['loc' => $base . '/core_values.php', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['loc' => $base . '/ideas.php', 'priority' => '0.8', 'changefreq' => 'daily'],
    ['loc' => $base . '/public_profiles.php', 'priority' => '0.7', 'changefreq' => 'weekly'],
    ['loc' => $base . '/contact.php', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => $base . '/privacy.php', 'priority' => '0.4', 'changefreq' => 'yearly'],
    ['loc' => $base . '/terms.php', 'priority' => '0.4', 'changefreq' => 'yearly'],
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $p): ?>
    <url>
        <loc><?= htmlspecialchars($p['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') ?></loc>
        <changefreq><?= $p['changefreq'] ?></changefreq>
        <priority><?= $p['priority'] ?></priority>
    </url>
<?php endforeach; ?>
</urlset>
