<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/admin_auth.php';

$success = '';
$error = '';

$en_defaults = default_settings_en();
$sq_defaults = default_settings_sq();

$rich_text_columns = [
    'site_description', 'site_description_sq',
    'home_core_values_text', 'home_core_values_text_sq',
    'values_page_text', 'values_page_text_sq',
    'ideas_page_text', 'ideas_page_text_sq',
    'contact_page_text', 'contact_page_text_sq',
];

$all_columns = array_merge(array_keys($en_defaults), array_keys($sq_defaults));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = __('csrf_error');
    } else {
        $set_clauses = [];
        $params = [];

        foreach ($all_columns as $col) {
            $raw = trim((string)($_POST[$col] ?? ''));
            if (in_array($col, $rich_text_columns, true)) {
                $raw = sanitize_rich_html($raw);
            }
            $set_clauses[] = "`{$col}` = ?";
            $params[] = $raw;
        }

        $sql = "UPDATE `settings` SET " . implode(', ', $set_clauses) . " WHERE `id` = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $exists = $pdo->query("SELECT `id` FROM `settings` WHERE `id` = 1")->fetch();
        if (!$exists) {
            $cols_sql = '`' . implode('`, `', $all_columns) . '`';
            $ph_sql = implode(', ', array_fill(0, count($all_columns), '?'));
            $ins = $pdo->prepare("INSERT INTO `settings` (`id`, `schema_version`, {$cols_sql}) VALUES (1, " . (int)AK_SCHEMA_VERSION . ", {$ph_sql})");
            $ins->execute($params);
        }

        $success = 'Site settings (English & Albanian) saved successfully.';
    }
}

$settings = $pdo->query("SELECT * FROM `settings` WHERE `id` = 1")->fetch(PDO::FETCH_ASSOC) ?: [];
foreach ($en_defaults as $k => $v) {
    if (!isset($settings[$k]) || trim((string)$settings[$k]) === '') {
        $settings[$k] = $v;
    }
}
foreach ($sq_defaults as $k => $v) {
    if (!isset($settings[$k]) || trim((string)$settings[$k]) === '') {
        $settings[$k] = $v;
    }
}

$page_title = 'Site Settings - ' . $t_site_title;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/../includes/head.php'; ?>
    <style>
        .settings-section { margin-bottom: 28px; border: 1px solid #e2e8f0; padding: 24px; border-radius: 12px; background: #fff; }
        .settings-section h2 { margin-top: 0; color: var(--primary-color); border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include __DIR__ . '/admin_sidebar.php'; ?>
        <main id="main-content" class="admin-content">
            <h1>Site Settings</h1>

            <?php if ($error): ?><p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if ($success): ?><p class="success" role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

            <form method="POST" action="settings.php" class="settings-form">
                <?= csrf_field() ?>

                <div class="settings-section">
                    <h2>Brand Title</h2>
                    <div class="form-group">
                        <label for="site_title">Site Title (Global Brand Name)</label>
                        <input type="text" id="site_title" name="site_title" value="<?= htmlspecialchars((string)$settings['site_title'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>

                <div class="tabs" role="tablist" aria-label="Content Language">
                    <button type="button" class="lang-tab-btn active" role="tab" aria-selected="true" data-lang-tab="sq">Shqip (SQ)</button>
                    <button type="button" class="lang-tab-btn" role="tab" aria-selected="false" data-lang-tab="en">English (EN)</button>
                </div>

                <?php foreach (['sq' => '_sq', 'en' => ''] as $lang_code => $suffix):
                    $label_tag = ($lang_code === 'sq') ? 'SQ' : 'EN';
                ?>
                <div data-lang-panel="<?= $lang_code ?>" <?= $lang_code === 'en' ? 'hidden' : '' ?>>
                    <div class="settings-section">
                        <h2>Navigation Menu (<?= $label_tag ?>)</h2>
                        <?php
                        $menu_fields = [
                            'menu_home' => 'Home',
                            'menu_values' => 'Core Values',
                            'menu_ideas' => 'Ideas',
                            'menu_profiles' => 'Public Profiles',
                            'menu_contact' => 'Contact',
                            'menu_profile' => 'Profile (Logged In)',
                            'menu_admin' => 'Admin Panel (Admin Only)',
                            'menu_logout' => 'Logout',
                            'menu_login' => 'Login (Logged Out)',
                            'menu_register' => 'Register (Logged Out)',
                        ];
                        foreach ($menu_fields as $base_col => $human_label):
                            $col = $base_col . $suffix;
                        ?>
                            <div class="form-group">
                                <label for="<?= $col ?>"><?= htmlspecialchars($human_label, ENT_QUOTES, 'UTF-8') ?> (<?= $label_tag ?>)</label>
                                <input type="text" id="<?= $col ?>" name="<?= $col ?>" value="<?= htmlspecialchars((string)$settings[$col], ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        <?php endforeach; ?>
                        <div class="form-group">
                            <label for="footer_text<?= $suffix ?>">Footer Copyright Text (<?= $label_tag ?>)</label>
                            <input type="text" id="footer_text<?= $suffix ?>" name="footer_text<?= $suffix ?>" value="<?= htmlspecialchars((string)$settings['footer_text' . $suffix], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>

                    <div class="settings-section">
                        <h2>Home Page (<?= $label_tag ?>)</h2>
                        <div class="form-group">
                            <label for="home_banner_text<?= $suffix ?>">Hero Banner Heading (<?= $label_tag ?>)</label>
                            <input type="text" id="home_banner_text<?= $suffix ?>" name="home_banner_text<?= $suffix ?>" value="<?= htmlspecialchars((string)$settings['home_banner_text' . $suffix], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="form-group">
                            <label for="site_description<?= $suffix ?>">Hero Subtext / Site Description (<?= $label_tag ?>)</label>
                            <textarea id="site_description<?= $suffix ?>" name="site_description<?= $suffix ?>" class="rich-text" rows="3"><?= htmlspecialchars((string)$settings['site_description' . $suffix], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="join_us_btn<?= $suffix ?>">Join Us Button (<?= $label_tag ?>)</label>
                            <input type="text" id="join_us_btn<?= $suffix ?>" name="join_us_btn<?= $suffix ?>" value="<?= htmlspecialchars((string)$settings['join_us_btn' . $suffix], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="form-group">
                            <label for="home_core_values_title<?= $suffix ?>">Core Values Section Title (<?= $label_tag ?>)</label>
                            <input type="text" id="home_core_values_title<?= $suffix ?>" name="home_core_values_title<?= $suffix ?>" value="<?= htmlspecialchars((string)$settings['home_core_values_title' . $suffix], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="form-group">
                            <label for="home_core_values_text<?= $suffix ?>">Core Values Section Intro (<?= $label_tag ?>)</label>
                            <textarea id="home_core_values_text<?= $suffix ?>" name="home_core_values_text<?= $suffix ?>" class="rich-text" rows="3"><?= htmlspecialchars((string)$settings['home_core_values_text' . $suffix], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="view_values_btn<?= $suffix ?>">View Values Button (<?= $label_tag ?>)</label>
                            <input type="text" id="view_values_btn<?= $suffix ?>" name="view_values_btn<?= $suffix ?>" value="<?= htmlspecialchars((string)$settings['view_values_btn' . $suffix], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="form-group">
                            <label for="home_latest_ideas_title<?= $suffix ?>">Latest Ideas Section Title (<?= $label_tag ?>)</label>
                            <input type="text" id="home_latest_ideas_title<?= $suffix ?>" name="home_latest_ideas_title<?= $suffix ?>" value="<?= htmlspecialchars((string)$settings['home_latest_ideas_title' . $suffix], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="form-group">
                            <label for="view_ideas_btn<?= $suffix ?>">View Ideas Button (<?= $label_tag ?>)</label>
                            <input type="text" id="view_ideas_btn<?= $suffix ?>" name="view_ideas_btn<?= $suffix ?>" value="<?= htmlspecialchars((string)$settings['view_ideas_btn' . $suffix], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>

                    <div class="settings-section">
                        <h2>Inner Pages (<?= $label_tag ?>)</h2>
                        <div class="form-group">
                            <label for="values_page_title<?= $suffix ?>">Core Values Page Title (<?= $label_tag ?>)</label>
                            <input type="text" id="values_page_title<?= $suffix ?>" name="values_page_title<?= $suffix ?>" value="<?= htmlspecialchars((string)$settings['values_page_title' . $suffix], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="form-group">
                            <label for="values_page_text<?= $suffix ?>">Core Values Page Intro (<?= $label_tag ?>)</label>
                            <textarea id="values_page_text<?= $suffix ?>" name="values_page_text<?= $suffix ?>" class="rich-text" rows="2"><?= htmlspecialchars((string)$settings['values_page_text' . $suffix], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="ideas_page_title<?= $suffix ?>">Ideas Page Title (<?= $label_tag ?>)</label>
                            <input type="text" id="ideas_page_title<?= $suffix ?>" name="ideas_page_title<?= $suffix ?>" value="<?= htmlspecialchars((string)$settings['ideas_page_title' . $suffix], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="form-group">
                            <label for="ideas_page_text<?= $suffix ?>">Ideas Page Intro (<?= $label_tag ?>)</label>
                            <textarea id="ideas_page_text<?= $suffix ?>" name="ideas_page_text<?= $suffix ?>" class="rich-text" rows="2"><?= htmlspecialchars((string)$settings['ideas_page_text' . $suffix], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="submit_idea_btn<?= $suffix ?>">Submit Idea Button (<?= $label_tag ?>)</label>
                            <input type="text" id="submit_idea_btn<?= $suffix ?>" name="submit_idea_btn<?= $suffix ?>" value="<?= htmlspecialchars((string)$settings['submit_idea_btn' . $suffix], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="form-group">
                            <label for="contact_page_title<?= $suffix ?>">Contact Page Title (<?= $label_tag ?>)</label>
                            <input type="text" id="contact_page_title<?= $suffix ?>" name="contact_page_title<?= $suffix ?>" value="<?= htmlspecialchars((string)$settings['contact_page_title' . $suffix], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="form-group">
                            <label for="contact_page_text<?= $suffix ?>">Contact Page Intro (<?= $label_tag ?>)</label>
                            <textarea id="contact_page_text<?= $suffix ?>" name="contact_page_text<?= $suffix ?>" class="rich-text" rows="2"><?= htmlspecialchars((string)$settings['contact_page_text' . $suffix], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="register_page_title<?= $suffix ?>">Register Page Title (<?= $label_tag ?>)</label>
                            <input type="text" id="register_page_title<?= $suffix ?>" name="register_page_title<?= $suffix ?>" value="<?= htmlspecialchars((string)$settings['register_page_title' . $suffix], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="form-group">
                            <label for="login_page_title<?= $suffix ?>">Login Page Title (<?= $label_tag ?>)</label>
                            <input type="text" id="login_page_title<?= $suffix ?>" name="login_page_title<?= $suffix ?>" value="<?= htmlspecialchars((string)$settings['login_page_title' . $suffix], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>

                    <div class="settings-section">
                        <h2>Email Verification Template — Plain Text (<?= $label_tag ?>)</h2>
                        <p class="field-hint" style="margin-bottom: 12px;">Available placeholders: <code>{site_title}</code>, <code>{username}</code>, <code>{verify_link}</code></p>
                        <div class="form-group">
                            <label for="email_verification_subject<?= $suffix ?>">Email Subject (<?= $label_tag ?>)</label>
                            <input type="text" id="email_verification_subject<?= $suffix ?>" name="email_verification_subject<?= $suffix ?>" value="<?= htmlspecialchars((string)$settings['email_verification_subject' . $suffix], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="form-group">
                            <label for="email_verification_message<?= $suffix ?>">Email Body — Plain Text (<?= $label_tag ?>)</label>
                            <textarea id="email_verification_message<?= $suffix ?>" name="email_verification_message<?= $suffix ?>" rows="6"><?= htmlspecialchars((string)$settings['email_verification_message' . $suffix], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <button type="submit" class="btn btn-large" style="width: 100%;">Save All Settings</button>
            </form>
        </main>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="../assets/js/main.js"></script>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="../assets/js/admin-editor.js"></script>
</body>
</html>
