<?php
$host = 'localhost';
$dbname = 'ardhmja';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // We are suppressing the error for testing environments without MySQL running
    // In production, you would uncomment the line below.
    // die("Database connection failed: " . $e->getMessage());
    $pdo = null;
}

// Fetch global settings
$global_settings = [];
if ($pdo) {
    $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
    if ($stmt) {
        $global_settings = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}

// Fallbacks for editable text properties
$t_site_title = $global_settings['site_title'] ?? 'Frymaere';
$t_site_description = $global_settings['site_description'] ?? 'Welcome to our political party platform.';
$t_home_banner_text = $global_settings['home_banner_text'] ?? 'Building a New Republic Together';
$t_join_us_btn = $global_settings['join_us_btn'] ?? 'Join Us Now';
$t_home_core_values_title = $global_settings['home_core_values_title'] ?? 'Our Core Values';
$t_home_core_values_text = $global_settings['home_core_values_text'] ?? 'We are dedicated to progress, transparency, and giving power back to the people. Explore our Party Points to understand our vision for Albania.';
$t_view_points_btn = $global_settings['view_points_btn'] ?? 'View Party Points';
$t_home_latest_ideas_title = $global_settings['home_latest_ideas_title'] ?? 'Latest Community Ideas';
$t_view_ideas_btn = $global_settings['view_ideas_btn'] ?? 'View All Ideas';
$t_points_page_title = $global_settings['points_page_title'] ?? 'Our Party Points';
$t_points_page_text = $global_settings['points_page_text'] ?? 'Discover our core propositions for building a new future.';
$t_ideas_page_title = $global_settings['ideas_page_title'] ?? 'Community Ideas';
$t_ideas_page_text = $global_settings['ideas_page_text'] ?? 'Explore the ideas submitted by our members and approved by our party admins.';
$t_submit_idea_btn = $global_settings['submit_idea_btn'] ?? 'Submit an Idea';
$t_contact_page_title = $global_settings['contact_page_title'] ?? 'Contact Us';
$t_contact_page_text = $global_settings['contact_page_text'] ?? 'Have questions, ideas, or want to get involved? Please send us a message below.';
$t_register_page_title = $global_settings['register_page_title'] ?? 'Register';
$t_login_page_title = $global_settings['login_page_title'] ?? 'Login';
$t_footer_text = $global_settings['footer_text'] ?? 'All rights reserved.';

?>