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
$base_url = app_base_url();
$og_locale = (current_lang() === 'sq') ? 'sq_AL' : 'en_US';
$og_image = $base_url . '/assets/images/og-image.jpg';
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($resolved_title, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($clean_desc, ENT_QUOTES, 'UTF-8') ?>">
    <?php if (!empty($robots_meta)): ?>
    <meta name="robots" content="<?= htmlspecialchars($robots_meta, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= htmlspecialchars($t_site_title ?? 'AsamblejaKombetare', ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($resolved_title, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($clean_desc, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($og_image, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:locale" content="<?= htmlspecialchars($og_locale, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($resolved_title, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($clean_desc, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($og_image, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($head_prefix, ENT_QUOTES, 'UTF-8') ?>assets/css/style.css">
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($head_prefix, ENT_QUOTES, 'UTF-8') ?>assets/images/favicon.png">
    <link rel="icon" type="image/x-icon" href="<?= htmlspecialchars($head_prefix, ENT_QUOTES, 'UTF-8') ?>assets/images/favicon.ico">
