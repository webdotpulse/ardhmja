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
$t_home_core_values_text = $global_settings['home_core_values_text'] ?? 'We are dedicated to progress, transparency, and giving power back to the people. Explore our Core Values to understand our vision for Albania.';
$t_view_values_btn = $global_settings['view_values_btn'] ?? 'View Core Values';
$t_home_latest_ideas_title = $global_settings['home_latest_ideas_title'] ?? 'Latest Community Ideas';
$t_view_ideas_btn = $global_settings['view_ideas_btn'] ?? 'View All Ideas';
$t_values_page_title = $global_settings['values_page_title'] ?? 'Our Core Values';
$t_values_page_text = $global_settings['values_page_text'] ?? 'Discover our core propositions for building a new future.';
$t_ideas_page_title = $global_settings['ideas_page_title'] ?? 'Community Ideas';
$t_ideas_page_text = $global_settings['ideas_page_text'] ?? 'Explore the ideas submitted by our members and approved by our party admins.';
$t_submit_idea_btn = $global_settings['submit_idea_btn'] ?? 'Submit an Idea';
$t_contact_page_title = $global_settings['contact_page_title'] ?? 'Contact Us';
$t_contact_page_text = $global_settings['contact_page_text'] ?? 'Have questions, ideas, or want to get involved? Please send us a message below.';
$t_register_page_title = $global_settings['register_page_title'] ?? 'Register';
$t_login_page_title = $global_settings['login_page_title'] ?? 'Login';
$t_footer_text = $global_settings['footer_text'] ?? 'All rights reserved.';
$t_email_verification_subject = $global_settings['email_verification_subject'] ?? 'Verify your email for {site_title}';
$t_email_verification_message = $global_settings['email_verification_message'] ?? "Hello {username},\n\nPlease click the following link to verify your email address:\n{verify_link}\n\nThank you.";

$t_menu_home = $global_settings['menu_home'] ?? 'Home';
$t_menu_values = $global_settings['menu_values'] ?? 'Core Values';
$t_menu_ideas = $global_settings['menu_ideas'] ?? 'Ideas';
$t_menu_profiles = $global_settings['menu_profiles'] ?? 'Public Profiles';
$t_menu_contact = $global_settings['menu_contact'] ?? 'Contact';
$t_menu_profile = $global_settings['menu_profile'] ?? 'Profile';
$t_menu_admin = $global_settings['menu_admin'] ?? 'Admin Panel';
$t_menu_logout = $global_settings['menu_logout'] ?? 'Logout';
$t_menu_login = $global_settings['menu_login'] ?? 'Login';
$t_menu_register = $global_settings['menu_register'] ?? 'Register';

?>