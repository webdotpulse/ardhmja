<?php
/**
 * Non-destructive, idempotent database schema upgrader for AsamblejaKombetare.
 * Preserves 100% of existing users, settings, core values, ideas, agreements, and messages.
 * Works on both MySQL 5.7/8.0+ (Combell) and MariaDB 10.x+.
 */

define('AK_SCHEMA_VERSION', 2);

function default_settings_en(): array {
    return [
        'site_title' => 'AsamblejaKombetare',
        'site_description' => 'A civic and political movement dedicated to democratic renewal, transparency, and building Albania\'s future together.',
        'home_banner_text' => 'Building Our National Future Together',
        'join_us_btn' => 'Join Us Now',
        'home_core_values_title' => 'Our Core Values',
        'home_core_values_text' => 'We stand for accountability, rule of law, modern public services, and giving citizens a real voice. Explore our Core Values to see our vision.',
        'view_values_btn' => 'View Core Values',
        'home_latest_ideas_title' => 'Latest Community Ideas',
        'view_ideas_btn' => 'View All Ideas',
        'values_page_title' => 'Our Core Values',
        'values_page_text' => 'Discover our foundational principles and express your support as an approved member.',
        'ideas_page_title' => 'Community Ideas',
        'ideas_page_text' => 'Explore policy proposals and civic ideas submitted by our members.',
        'submit_idea_btn' => 'Submit an Idea',
        'contact_page_title' => 'Contact Us',
        'contact_page_text' => 'Have questions, proposals, or want to get involved with AsamblejaKombetare? Send us a message below.',
        'register_page_title' => 'Register',
        'login_page_title' => 'Login',
        'footer_text' => 'All rights reserved.',
        'email_verification_subject' => 'Verify your email for {site_title}',
        'email_verification_message' => "Hello {username},\n\nThank you for registering with {site_title}.\nPlease click the link below to verify your email address:\n{verify_link}\n\nThank you,\n{site_title}",
        'menu_home' => 'Home',
        'menu_values' => 'Core Values',
        'menu_ideas' => 'Ideas',
        'menu_profiles' => 'Public Profiles',
        'menu_contact' => 'Contact',
        'menu_profile' => 'Profile',
        'menu_admin' => 'Admin Panel',
        'menu_logout' => 'Logout',
        'menu_login' => 'Login',
        'menu_register' => 'Register',
    ];
}

function default_settings_sq(): array {
    return [
        'site_description_sq' => 'Një lëvizje qytetare dhe politike e përkushtuar ndaj ripërtëritjes demokratike, transparencës dhe ndërtimit të së ardhmes së përbashkët.',
        'home_banner_text_sq' => 'Ndërtojmë të Ardhmen Tonë Kombëtare Së Bashku',
        'join_us_btn_sq' => 'Bashkohuni Tani',
        'home_core_values_title_sq' => 'Vlerat Tona Themelore',
        'home_core_values_text_sq' => 'Ne angazhohemi për llogaridhënie, shtet ligjor, shërbime publike moderne dhe fuqizimin e zërit të qytetarëve. Njihuni me Vlerat tona Themelore.',
        'view_values_btn_sq' => 'Shiko Vlerat Themelore',
        'home_latest_ideas_title_sq' => 'Idetë e Fundit të Komunitetit',
        'view_ideas_btn_sq' => 'Shiko të Gjitha Idetë',
        'values_page_title_sq' => 'Vlerat Tona Themelore',
        'values_page_text_sq' => 'Zbuloni parimet tona themelore dhe shprehni mbështetjen tuaj si anëtar i miratuar.',
        'ideas_page_title_sq' => 'Idetë e Komunitetit',
        'ideas_page_text_sq' => 'Shfletoni propozimet dhe idetë qytetare të dërguara nga anëtarët tanë.',
        'submit_idea_btn_sq' => 'Dërgo një Ide',
        'contact_page_title_sq' => 'Na Kontaktoni',
        'contact_page_text_sq' => 'Keni pyetje, propozime apo dëshironi të angazhoheni në AsamblejaKombetare? Na dërgoni një mesazh më poshtë.',
        'register_page_title_sq' => 'Regjistrohu',
        'login_page_title_sq' => 'Hyrje',
        'footer_text_sq' => 'Të gjitha të drejtat e rezervuara.',
        'email_verification_subject_sq' => 'Verifikoni email-in tuaj për {site_title}',
        'email_verification_message_sq' => "Përshëndetje {username},\n\nFaleminderit që u regjistruat në {site_title}.\nJu lutemi klikoni lidhjen e mëposhtme për të verifikuar adresën tuaj të email-it:\n{verify_link}\n\nFaleminderit,\n{site_title}",
        'menu_home_sq' => 'Ballina',
        'menu_values_sq' => 'Vlerat Themelore',
        'menu_ideas_sq' => 'Idetë',
        'menu_profiles_sq' => 'Profilet Publike',
        'menu_contact_sq' => 'Kontakt',
        'menu_profile_sq' => 'Profili',
        'menu_admin_sq' => 'Administrimi',
        'menu_logout_sq' => 'Dil',
        'menu_login_sq' => 'Hyr',
        'menu_register_sq' => 'Regjistrohu',
    ];
}

function table_columns_map(PDO $pdo, string $table): array {
    $stmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
    ");
    $stmt->execute([$table]);
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    return array_fill_keys(array_map('strtolower', $cols ?: []), true);
}

function add_column_if_missing(PDO $pdo, string $table, array &$existing_cols, string $col_name, string $definition): void {
    $key = strtolower($col_name);
    if (isset($existing_cols[$key])) {
        return;
    }
    $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$col_name}` {$definition}");
    $existing_cols[$key] = true;
}

function ensure_schema_up_to_date(PDO $pdo, array &$global_settings): void {
    $current_version = (int)($global_settings['schema_version'] ?? 0);
    if ($current_version >= AK_SCHEMA_VERSION) {
        return;
    }

    try {
        // 1. Create rate_limits table if missing
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `rate_limits` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `action` VARCHAR(50) NOT NULL,
                `ip_hash` CHAR(64) NOT NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_action_ip_time` (`action`, `ip_hash`, `created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // 2. Add non-destructive columns to `users`
        $user_cols = table_columns_map($pdo, 'users');
        if (!empty($user_cols)) {
            $had_full_name = isset($user_cols['full_name']);
            add_column_if_missing($pdo, 'users', $user_cols, 'verify_expires_at', 'DATETIME DEFAULT NULL');
            add_column_if_missing($pdo, 'users', $user_cols, 'reset_token', 'VARCHAR(255) DEFAULT NULL');
            add_column_if_missing($pdo, 'users', $user_cols, 'reset_expires_at', 'DATETIME DEFAULT NULL');
            add_column_if_missing($pdo, 'users', $user_cols, 'full_name', 'VARCHAR(120) DEFAULT NULL');
            add_column_if_missing($pdo, 'users', $user_cols, 'party_role', 'VARCHAR(120) DEFAULT NULL');
            add_column_if_missing($pdo, 'users', $user_cols, 'party_role_sq', 'VARCHAR(120) DEFAULT NULL');
            add_column_if_missing($pdo, 'users', $user_cols, 'bio', 'TEXT DEFAULT NULL');
            add_column_if_missing($pdo, 'users', $user_cols, 'bio_sq', 'TEXT DEFAULT NULL');
            add_column_if_missing($pdo, 'users', $user_cols, 'privacy_accepted_at', 'DATETIME DEFAULT NULL');

            // Preserve visibility of existing admins who already uploaded a profile picture
            if (!$had_full_name) {
                $pdo->exec("UPDATE `users` SET `is_public` = 1 WHERE `role` = 'admin' AND `profile_picture` IS NOT NULL AND `profile_picture` <> ''");
            }
        }

        // 3. Add non-destructive bilingual columns to `core_values`
        $cv_cols = table_columns_map($pdo, 'core_values');
        if (!empty($cv_cols)) {
            add_column_if_missing($pdo, 'core_values', $cv_cols, 'title_sq', 'VARCHAR(255) DEFAULT NULL');
            add_column_if_missing($pdo, 'core_values', $cv_cols, 'description_sq', 'TEXT DEFAULT NULL');
        }

        // 4. Add non-destructive bilingual & schema_version columns to `settings`
        $settings_cols = table_columns_map($pdo, 'settings');
        if (!empty($settings_cols)) {
            add_column_if_missing($pdo, 'settings', $settings_cols, 'schema_version', 'INT(11) NOT NULL DEFAULT 0');

            $sq_defaults = default_settings_sq();
            $text_cols = [
                'site_description_sq', 'home_core_values_text_sq', 'values_page_text_sq',
                'ideas_page_text_sq', 'contact_page_text_sq', 'email_verification_message_sq',
            ];
            foreach ($sq_defaults as $col => $default_val) {
                $type = in_array($col, $text_cols, true) ? 'TEXT DEFAULT NULL' : 'VARCHAR(255) DEFAULT NULL';
                add_column_if_missing($pdo, 'settings', $settings_cols, $col, $type);
            }

            // Ensure settings row id = 1 exists without overwriting existing data
            $row = $pdo->query("SELECT * FROM `settings` WHERE `id` = 1")->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                $en = default_settings_en();
                $all = array_merge($en, $sq_defaults, ['schema_version' => AK_SCHEMA_VERSION]);
                $cols_sql = '`' . implode('`, `', array_keys($all)) . '`';
                $ph_sql = implode(', ', array_fill(0, count($all), '?'));
                $ins = $pdo->prepare("INSERT INTO `settings` (`id`, {$cols_sql}) VALUES (1, {$ph_sql})");
                $ins->execute(array_values($all));
            } else {
                // Only update placeholder brand names ('Frymaere' / 'Ardhmja' / empty) to 'AsamblejaKombetare'
                if (in_array(trim((string)($row['site_title'] ?? '')), ['', 'Frymaere', 'Ardhmja'], true)) {
                    $stmt = $pdo->prepare("UPDATE `settings` SET `site_title` = ? WHERE `id` = 1");
                    $stmt->execute(['AsamblejaKombetare']);
                }

                // Seed Albanian (_sq) columns ONLY where NULL or empty, preserving any existing translations
                $updates = [];
                $params = [];
                foreach ($sq_defaults as $col => $val) {
                    if (!isset($row[$col]) || trim((string)$row[$col]) === '') {
                        $updates[] = "`{$col}` = ?";
                        $params[] = $val;
                    }
                }
                $updates[] = "`schema_version` = ?";
                $params[] = AK_SCHEMA_VERSION;

                $upd_sql = "UPDATE `settings` SET " . implode(', ', $updates) . " WHERE `id` = 1";
                $stmt = $pdo->prepare($upd_sql);
                $stmt->execute($params);
            }

            // Re-fetch updated global settings
            $stmt = $pdo->query("SELECT * FROM `settings` WHERE `id` = 1");
            if ($stmt) {
                $global_settings = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
            }
        }
    } catch (Throwable $e) {
        error_log('AsamblejaKombetare schema migration error: ' . $e->getMessage());
    }
}
