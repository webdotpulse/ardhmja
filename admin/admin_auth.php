<?php
$asset_prefix = '../';
$robots_meta = 'noindex, nofollow';

require_db($pdo, '../');
$current_admin_user = refresh_authenticated_user($pdo);

if (!$current_admin_user || ($current_admin_user['role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}
