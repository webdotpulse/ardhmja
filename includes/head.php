<?php
/**
 * Shared <head> metadata partial (SEO, Open Graph, self-hosted CSS & icons).
 * Expects optional `$page_title`, `$meta_description`, `$asset_prefix`, `$robots_meta`.
 */
$head_prefix = $asset_prefix ?? '';
$resolved_title = $page_title ?? ($t_site_title ?? 'AsamblejaKombetare');
$raw_desc = $meta_description ?? strip_tags((string)($t_site_description ?? 'AsamblejaKombetare'));
$clean_desc = trim(preg_replace('/\s+/', ' ', $raw_desc));
if (mb_strlen($clean_desc, 'UTF-8') > 160) {
    $clean_desc = mb_substr($clean_desc, 0, 157, 'UTF-8') . '...';
}
$base_url = seo_base_url();
$active_lang = current_lang();
$og_locale = ($active_lang === 'sq') ? 'sq_AL' : 'en_US';
$og_image = $base_url . '/' . asset_url('assets/images/og-image.jpg');

$canonical_url = '';
$alt_sq_url = '';
$alt_en_url = '';
if (empty($robots_meta)) {
    $script_file = basename(str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? 'index.php')));
    $canonical_path = ($script_file === '' || $script_file === 'index.php') ? '/' : '/' . $script_file;
    $page_num = (isset($page) && is_int($page) && $page > 1) ? $page : 1;

    $sq_params = ($page_num > 1) ? ['page' => $page_num] : [];
    $en_params = ($page_num > 1) ? ['page' => $page_num, 'lang' => 'en'] : ['lang' => 'en'];

    $alt_sq_url = $base_url . $canonical_path . ($sq_params ? '?' . http_build_query($sq_params) : '');
    $alt_en_url = $base_url . $canonical_path . '?' . http_build_query($en_params);
    $canonical_url = ($active_lang === 'en') ? $alt_en_url : $alt_sq_url;
}
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($resolved_title, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($clean_desc, ENT_QUOTES, 'UTF-8') ?>">
    <?php if (!empty($robots_meta)): ?>
    <meta name="robots" content="<?= htmlspecialchars($robots_meta, ENT_QUOTES, 'UTF-8') ?>">
    <?php elseif ($canonical_url !== ''): ?>
    <link rel="canonical" href="<?= htmlspecialchars($canonical_url, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="alternate" hreflang="sq" href="<?= htmlspecialchars($alt_sq_url, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="alternate" hreflang="en" href="<?= htmlspecialchars($alt_en_url, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="alternate" hreflang="x-default" href="<?= htmlspecialchars($alt_sq_url, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <meta property="og:type" content="website">
    <?php if ($canonical_url !== ''): ?>
    <meta property="og:url" content="<?= htmlspecialchars($canonical_url, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <meta property="og:site_name" content="<?= htmlspecialchars($t_site_title ?? 'AsamblejaKombetare', ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($resolved_title, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($clean_desc, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($og_image, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:locale" content="<?= htmlspecialchars($og_locale, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($resolved_title, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($clean_desc, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($og_image, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('assets/css/style.css', $head_prefix), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="icon" type="image/png" href="<?= htmlspecialchars(asset_url('assets/images/favicon.png', $head_prefix), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="icon" type="image/x-icon" href="<?= htmlspecialchars(asset_url('assets/images/favicon.ico', $head_prefix), ENT_QUOTES, 'UTF-8') ?>">
