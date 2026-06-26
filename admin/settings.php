<?php
require_once '../includes/db.php';
require_once 'admin_auth.php';

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $columns = [
        'site_title', 'site_description', 'home_banner_text', 'join_us_btn',
        'home_core_values_title', 'home_core_values_text', 'view_values_btn',
        'home_latest_ideas_title', 'view_ideas_btn', 'values_page_title',
        'values_page_text', 'ideas_page_title', 'ideas_page_text', 'submit_idea_btn',
        'contact_page_title', 'contact_page_text', 'register_page_title', 'login_page_title', 'footer_text', 'email_verification_subject', 'email_verification_message',
        'menu_home', 'menu_values', 'menu_ideas', 'menu_profiles', 'menu_contact', 'menu_profile', 'menu_admin', 'menu_logout', 'menu_login', 'menu_register'
    ];

    $set_clauses = [];
    $params = [];
    foreach ($columns as $col) {
        $set_clauses[] = "$col = ?";
        $params[] = $_POST[$col] ?? '';
    }

    $sql = "UPDATE settings SET " . implode(", ", $set_clauses) . " WHERE id = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Check if ID 1 exists, if not insert
    if ($stmt->rowCount() == 0) {
        $stmt = $pdo->prepare("SELECT id FROM settings WHERE id = 1");
        $stmt->execute();
        if (!$stmt->fetch()) {
             $placeholders = implode(", ", array_fill(0, count($columns), "?"));
             $insert_sql = "INSERT INTO settings (id, " . implode(", ", $columns) . ") VALUES (1, $placeholders)";
             $stmt = $pdo->prepare($insert_sql);
             $stmt->execute($params);
        }
    }

    $success = "Settings updated successfully.";
}

// Ensure defaults
$settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch() ?: [];
$defaults = [
    'site_title' => 'Ardhmja', 'site_description' => 'Welcome to our political party platform.',
    'home_banner_text' => 'Building a New Republic Together', 'join_us_btn' => 'Join Us Now',
    'home_core_values_title' => 'Our Core Values', 'home_core_values_text' => 'We are dedicated to progress, transparency, and giving power back to the people. Explore our Core Values to understand our vision for Albania.',
    'view_values_btn' => 'View Core Values', 'home_latest_ideas_title' => 'Latest Community Ideas',
    'view_ideas_btn' => 'View All Ideas', 'values_page_title' => 'Our Core Values',
    'values_page_text' => 'Discover our core propositions for building a new future.', 'ideas_page_title' => 'Community Ideas',
    'ideas_page_text' => 'Explore the ideas submitted by our members and approved by our party admins.', 'submit_idea_btn' => 'Submit an Idea',
    'contact_page_title' => 'Contact Us', 'contact_page_text' => 'Have questions, ideas, or want to get involved? Please send us a message below.',
    'register_page_title' => 'Register', 'login_page_title' => 'Login', 'footer_text' => 'All rights reserved.', 'email_verification_subject' => 'Verify your email for {site_title}', 'email_verification_message' => "Hello {username},

Please click the following link to verify your email address:
{verify_link}

Thank you.",
    'menu_home' => 'Home', 'menu_values' => 'Core Values', 'menu_ideas' => 'Ideas',
    'menu_profiles' => 'Public Profiles', 'menu_contact' => 'Contact', 'menu_profile' => 'Profile',
    'menu_admin' => 'Admin Panel', 'menu_logout' => 'Logout', 'menu_login' => 'Login', 'menu_register' => 'Register'
];

foreach ($defaults as $k => $v) {
    if (!isset($settings[$k]) || $settings[$k] === '') {
        $settings[$k] = $v;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- TinyMCE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: 'textarea',
        menubar: false,
        plugins: 'lists link preview code',
        toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code'
      });
    </script>
    <meta charset="UTF-8">
    <title>Site Settings</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; margin: 0; }
        .admin-wrapper { display: flex; flex: 1; }
        .admin-content { flex: 1; padding: 20px; }
        .settings-section { margin-bottom: 30px; border: 1px solid #ddd; padding: 20px; border-radius: 8px; background: #fff;}
        .settings-section h2 { margin-top: 0; color: var(--primary-color); border-bottom: 2px solid #eee; padding-bottom: 10px;}
        .settings-form { max-width: 100%; box-shadow: none; padding: 0; background: transparent; }
    </style>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
</head>
<body>
    <div class="admin-wrapper">
    <?php include 'admin_sidebar.php'; ?>
    <div class="admin-content">
        <h1>Site Settings</h1>
        <?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>

        <form method="POST" class="settings-form">
            <div class="settings-section">
                <h2>Navigation Menu</h2>
                <div class="form-group">
                    <label>Home</label>
                    <input type="text" name="menu_home" value="<?= htmlspecialchars($settings['menu_home']) ?>">
                </div>
                <div class="form-group">
                    <label>Core Values</label>
                    <input type="text" name="menu_values" value="<?= htmlspecialchars($settings['menu_values']) ?>">
                </div>
                <div class="form-group">
                    <label>Ideas</label>
                    <input type="text" name="menu_ideas" value="<?= htmlspecialchars($settings['menu_ideas']) ?>">
                </div>
                <div class="form-group">
                    <label>Public Profiles</label>
                    <input type="text" name="menu_profiles" value="<?= htmlspecialchars($settings['menu_profiles']) ?>">
                </div>
                <div class="form-group">
                    <label>Contact</label>
                    <input type="text" name="menu_contact" value="<?= htmlspecialchars($settings['menu_contact']) ?>">
                </div>
                <div class="form-group">
                    <label>Profile (Logged In)</label>
                    <input type="text" name="menu_profile" value="<?= htmlspecialchars($settings['menu_profile']) ?>">
                </div>
                <div class="form-group">
                    <label>Admin Panel (Admin Only)</label>
                    <input type="text" name="menu_admin" value="<?= htmlspecialchars($settings['menu_admin']) ?>">
                </div>
                <div class="form-group">
                    <label>Logout</label>
                    <input type="text" name="menu_logout" value="<?= htmlspecialchars($settings['menu_logout']) ?>">
                </div>
                <div class="form-group">
                    <label>Login (Logged Out)</label>
                    <input type="text" name="menu_login" value="<?= htmlspecialchars($settings['menu_login']) ?>">
                </div>
                <div class="form-group">
                    <label>Register (Logged Out)</label>
                    <input type="text" name="menu_register" value="<?= htmlspecialchars($settings['menu_register']) ?>">
                </div>
            </div>

            <div class="settings-section">
                <h2>Global Settings</h2>
                <div class="form-group">
                    <label>Site Title</label>
                    <input type="text" name="site_title" value="<?= htmlspecialchars($settings['site_title']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Footer Text</label>
                    <input type="text" name="footer_text" value="<?= htmlspecialchars($settings['footer_text']) ?>">
                </div>
            </div>

            <div class="settings-section">
                <h2>Home Page</h2>
                <div class="form-group">
                    <label>Home Banner Text</label>
                    <input type="text" name="home_banner_text" value="<?= htmlspecialchars($settings['home_banner_text']) ?>">
                </div>
                <div class="form-group">
                    <label>Site Description (Hero Subtext)</label>
                    <textarea name="site_description" rows="3"><?= htmlspecialchars($settings['site_description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Join Us Button Text</label>
                    <input type="text" name="join_us_btn" value="<?= htmlspecialchars($settings['join_us_btn']) ?>">
                </div>
                <div class="form-group">
                    <label>Core Values Title</label>
                    <input type="text" name="home_core_values_title" value="<?= htmlspecialchars($settings['home_core_values_title']) ?>">
                </div>
                <div class="form-group">
                    <label>Core Values Text</label>
                    <textarea name="home_core_values_text" rows="3"><?= htmlspecialchars($settings['home_core_values_text']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>View Values Button</label>
                    <input type="text" name="view_values_btn" value="<?= htmlspecialchars($settings['view_values_btn']) ?>">
                </div>
                <div class="form-group">
                    <label>Latest Ideas Title</label>
                    <input type="text" name="home_latest_ideas_title" value="<?= htmlspecialchars($settings['home_latest_ideas_title']) ?>">
                </div>
                <div class="form-group">
                    <label>View Ideas Button</label>
                    <input type="text" name="view_ideas_btn" value="<?= htmlspecialchars($settings['view_ideas_btn']) ?>">
                </div>
            </div>

            <div class="settings-section">
                <h2>Pages Text</h2>
                <div class="form-group">
                    <label>Values Page Title</label>
                    <input type="text" name="values_page_title" value="<?= htmlspecialchars($settings['values_page_title']) ?>">
                </div>
                <div class="form-group">
                    <label>Values Page Description</label>
                    <textarea name="values_page_text" rows="2"><?= htmlspecialchars($settings['values_page_text']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Ideas Page Title</label>
                    <input type="text" name="ideas_page_title" value="<?= htmlspecialchars($settings['ideas_page_title']) ?>">
                </div>
                <div class="form-group">
                    <label>Ideas Page Description</label>
                    <textarea name="ideas_page_text" rows="2"><?= htmlspecialchars($settings['ideas_page_text']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Submit Idea Button Text</label>
                    <input type="text" name="submit_idea_btn" value="<?= htmlspecialchars($settings['submit_idea_btn']) ?>">
                </div>

                <div class="form-group">
                    <label>Contact Page Title</label>
                    <input type="text" name="contact_page_title" value="<?= htmlspecialchars($settings['contact_page_title']) ?>">
                </div>
                <div class="form-group">
                    <label>Contact Page Description</label>
                    <textarea name="contact_page_text" rows="2"><?= htmlspecialchars($settings['contact_page_text']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Register Page Title</label>
                    <input type="text" name="register_page_title" value="<?= htmlspecialchars($settings['register_page_title']) ?>">
                </div>
                <div class="form-group">
                    <label>Login Page Title</label>
                    <input type="text" name="login_page_title" value="<?= htmlspecialchars($settings['login_page_title']) ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-large" style="width: 100%;">Save Settings</button>
            <div class="settings-section">
                <h2>Email Verification Template</h2>
                <p><small>Available placeholders: <code>{site_title}</code>, <code>{username}</code>, <code>{verify_link}</code></small></p>
                <div class="form-group">
                    <label>Email Subject</label>
                    <input type="text" name="email_verification_subject" value="<?= htmlspecialchars($settings['email_verification_subject']) ?>">
                </div>
                <div class="form-group">
                    <label>Email Message</label>
                    <textarea name="email_verification_message" rows="5"><?= htmlspecialchars($settings['email_verification_message']) ?></textarea>
                </div>
            </div>
        </form>
    </div>
    </div>
    <?php include "../includes/footer.php"; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
