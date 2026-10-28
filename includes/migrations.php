<?php
/**
 * Non-destructive, idempotent database schema & bilingual content upgrader for AsamblejaKombetare.
 * Preserves 100% of existing users, agreements, ideas, messages, and custom texts.
 * Compatible with both MySQL 5.7/8.0+ (Combell) and MariaDB 10.x+.
 */

define('AK_SCHEMA_VERSION', 3);

function default_settings_en(): array {
    return [
        'site_title' => 'AsamblejaKombetare',
        'site_description' => '<p>This is a civic and political movement that puts the citizen and the national interest first, with a clear vision for a democratic and European Albania.</p>',
        'home_banner_text' => 'Join Us for a European Albania',
        'join_us_btn' => 'Join Us Now',
        'home_core_values_title' => 'Our Core Values',
        'home_core_values_text' => '<p>To all those who see themselves in these foundational principles and do not feel represented by any of the other alternatives, WE CALL ON YOU to join us, because our country needs the strength, energy, knowledge, mind, and talent of everyone.</p>',
        'view_values_btn' => 'View Core Values',
        'home_latest_ideas_title' => 'Latest Community Ideas',
        'view_ideas_btn' => 'View All Ideas',
        'values_page_title' => 'Our Core Values',
        'values_page_text' => '<p>Discover our core propositions for building a new future.</p>',
        'ideas_page_title' => 'Community Ideas',
        'ideas_page_text' => '<p>Explore the ideas submitted by our members and approved by our movement\'s administrators.</p>',
        'submit_idea_btn' => 'Submit an Idea',
        'contact_page_title' => 'Contact Us',
        'contact_page_text' => '<p>Have questions, ideas, or want to get involved? Please send us a message below.</p>',
        'register_page_title' => 'Register',
        'login_page_title' => 'Login',
        'footer_text' => 'All rights reserved.',
        'email_verification_subject' => 'Verify your email for {site_title}',
        'email_verification_message' => "Hello {username},\n\nPlease click the link below to verify your email address:\n{verify_link}\n\nThank you,\n{site_title}",
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
        'site_description_sq' => '<p>Kjo është një lëvizje qytetare dhe politike që vendos qytetarin dhe interesin kombëtar në plan të parë, me një vizion të qartë për një Shqipëri demokratike dhe europiane.</p>',
        'home_banner_text_sq' => 'Bashkohuni me ne për një Shqipëri Europiane',
        'join_us_btn_sq' => 'Bashkohuni Tani',
        'home_core_values_title_sq' => 'Vlerat Tona Themelore',
        'home_core_values_text_sq' => '<p>Për të gjithë ata që e shohin veten në këto parime themelore dhe që nuk ndihen mirë me asnjë prej alternativave të tjera, JU BËJMË THIRRJE që të na bashkoheni, sepse vendi ynë ka nevojë për forcën, energjinë, diturinë, mendjen dhe talentin e të gjithëve.</p>',
        'view_values_btn_sq' => 'Shiko Vlerat Themelore',
        'home_latest_ideas_title_sq' => 'Idetë e Fundit të Komunitetit',
        'view_ideas_btn_sq' => 'Shiko të Gjitha Idetë',
        'values_page_title_sq' => 'Vlerat Tona Themelore',
        'values_page_text_sq' => '<p>Zbuloni propozimet tona thelbësore për ndërtimin e një të ardhmeje të re.</p>',
        'ideas_page_title_sq' => 'Idetë e Komunitetit',
        'ideas_page_text_sq' => '<p>Eksploroni idetë e paraqitura nga anëtarët tanë dhe të miratuara nga administratorët e partisë sonë.</p>',
        'submit_idea_btn_sq' => 'Dërgo një Ide',
        'contact_page_title_sq' => 'Na Kontaktoni',
        'contact_page_text_sq' => '<p>Keni pyetje, ide, ose dëshironi të përfshiheni? Ju lutemi na dërgoni një mesazh më poshtë.</p>',
        'register_page_title_sq' => 'Regjistrohu',
        'login_page_title_sq' => 'Hyr',
        'footer_text_sq' => 'Të gjitha të drejtat e rezervuara.',
        'email_verification_subject_sq' => 'Verifikoni email-in tuaj për {site_title}',
        'email_verification_message_sq' => "Përshëndetje {username},\n\nJu lutemi klikoni në lidhjen e mëposhtme për të verifikuar adresën tuaj të email-it:\n{verify_link}\n\nFaleminderit,\n{site_title}",
        'menu_home_sq' => 'Kreu',
        'menu_values_sq' => 'Vlerat Themelore',
        'menu_ideas_sq' => 'Idetë',
        'menu_profiles_sq' => 'Profilet',
        'menu_contact_sq' => 'Kontakt',
        'menu_profile_sq' => 'Profili',
        'menu_admin_sq' => 'Admin',
        'menu_logout_sq' => 'Dil',
        'menu_login_sq' => 'Hyr',
        'menu_register_sq' => 'Regjistrohu',
    ];
}

function official_core_values_bilingual(): array {
    return [
        1 => [
            'title' => '1. WE STAND FOR A DEMOCRATIC ALBANIA',
            'description' => 'We believe that democracy is the fairest and most legitimate form of state organization. Sovereignty stems from the people and is exercised by them through free, fair, and periodic elections, independent institutions, and respect for the rule of law. We defend political pluralism, the separation and balance of powers, freedom of expression, and the active participation of citizens in public life.',
            'title_sq' => '1. NE JEMI PËR NJË SHQIPËRI DEMOKRATIKE',
            'description_sq' => 'Ne besojmë se demokracia është forma më e drejtë dhe më legjitime e organizimit të shtetit. Sovraniteti buron nga populli dhe ushtrohet prej tij nëpërmjet zgjedhjeve të lira, të ndershme dhe periodike, institucioneve të pavarura dhe respektimit të shtetit të së drejtës. Ne mbrojmë pluralizmin politik, ndarjen dhe balancimin e pushteteve, lirinë e shprehjes dhe pjesëmarrjen aktive të qytetarëve në jetën publike.',
        ],
        2 => [
            'title' => '2. WE STAND FOR A SECULAR ALBANIA',
            'description' => 'We believe in a state neutral toward religious beliefs, guaranteeing freedom of conscience and religion for every citizen. Religious belief is an individual right within the boundaries of the private sphere. Public decision-making must rest on the Constitution, secular rather than divine laws, science, and the public interest, maintaining a clear separation between the state and religious institutions.',
            'title_sq' => '2. NE JEMI PËR NJË SHQIPËRI LAIKE',
            'description_sq' => 'Ne besojmë në një shtet neutral ndaj besimeve fetare, i cili garanton lirinë e ndërgjegjes dhe të fesë për çdo qytetar. Besimi fetar është një e drejtë individuale, por deri në kufijtë e sferës publike. Vendimmarrja publike duhet të mbështetet mbi Kushtetutën, ligjet tokësore dhe jo hyjnore, shkencën dhe interesin publik, duke ruajtur ndarjen e qartë ndërmjet shtetit dhe institucioneve fetare.',
        ],
        3 => [
            'title' => '3. WE STAND FOR A EUROPEAN ALBANIA',
            'description' => 'We believe that Albania\'s future is inseparable from the European family. We unequivocally support the country\'s full integration into the European Union, not merely as a political objective, but as a commitment to building strong institutions, a competitive economy, an independent judiciary, and high democratic standards. Europe represents the natural space for Albania\'s development.',
            'title_sq' => '3. NE JEMI PËR NJË SHQIPËRI EUROPIANE',
            'description_sq' => 'Ne besojmë se e ardhmja e Shqipërisë është e pandashme nga familja evropiane. Mbështesim pa mëdyshje integrimin e plotë të vendit në Bashkimin Europian, jo vetëm si një objektiv politik, por si një angazhim për të ndërtuar institucione të forta, ekonomi konkurruese, drejtësi të pavarur dhe standarde të larta demokratike. Evropa përfaqëson hapësirën natyrore të zhvillimit të Shqipërisë.',
        ],
        4 => [
            'title' => '4. WE STAND FOR A JUST ALBANIA',
            'description' => 'We believe that no one should be above the law. Justice must be independent, impartial, and equal for all. The fight against corruption, transparency, and accountability are indispensable conditions for building citizens\' trust in the state.',
            'title_sq' => '4. NE JEMI PËR NJË SHQIPËRI TË DREJTË',
            'description_sq' => 'Ne besojmë se askush nuk duhet të jetë mbi ligjin. Drejtësia duhet të jetë e pavarur, e paanshme dhe e barabartë për të gjithë. Lufta kundër korrupsionit, transparenca dhe llogaridhënia janë kushte të domosdoshme për ndërtimin e besimit të qytetarëve ndaj shtetit.',
        ],
        5 => [
            'title' => '5. WE STAND FOR A HUMANIST ALBANIA',
            'description' => 'We place human dignity at the center of politics. We defend the fundamental rights and freedoms of every individual, without distinction of gender, age, origin, belief, or social status. We believe in international cooperation, dialogue, peace, and solidarity as values that strengthen Albania\'s development and the well-being of its citizens.',
            'title_sq' => '5. NE JEMI PËR NJË SHQIPËRI HUMANISTE',
            'description_sq' => 'Ne vendosim dinjitetin njerëzor në qendër të politikës. Mbrojmë të drejtat dhe liritë themelore të çdo individi, pa dallim gjinie, moshe, origjine, bindjeje apo statusi social. Besojmë në bashkëpunimin ndërkombëtar, dialogun, paqen dhe solidaritetin si vlera që forcojnë zhvillimin e Shqipërisë dhe mirëqenien e qytetarëve të saj.',
        ],
        6 => [
            'title' => '6. WE STAND FOR A GREEN AND SUSTAINABLE ALBANIA',
            'description' => 'We believe that environmental protection is an obligation toward both present and future generations. We are committed to protecting forests, rivers, coastlines, biodiversity, and natural resources, fighting pollution and climate change, and developing a green economy that creates prosperity without harming the ecosystem. Economic development must always be sustainable and in harmony with nature.',
            'title_sq' => '6. NE JEMI PËR NJË SHQIPËRI TË GJELBËR DHE TË QËNDRUESHME',
            'description_sq' => 'Ne besojmë se mbrojtja e mjedisit është detyrim ndaj brezave të sotëm dhe atyre që do të vijnë. Angazhohemi për mbrojtjen e pyjeve, lumenjve, brigjeve, biodiversitetit dhe burimeve natyrore, për luftën kundër ndotjes dhe ndryshimeve klimatike, si dhe për zhvillimin e një ekonomie të gjelbër që krijon mirëqenie pa dëmtuar ekosistemin. Zhvillimi ekonomik duhet të jetë gjithmonë i qëndrueshëm dhe në harmoni me natyrën.',
        ],
        7 => [
            'title' => '7. WE STAND FOR AN ALBANIA OF MERIT AND OPPORTUNITY',
            'description' => 'We believe that success must be built upon merit, ability, and honest work. Every citizen must have equal opportunities for quality education, employment, entrepreneurship, and professional growth, free from privilege, nepotism, or discrimination.',
            'title_sq' => '7. NE JEMI PËR NJË SHQIPËRI TË MERITËS DHE MUNDËSIVE',
            'description_sq' => 'Ne besojmë se suksesi duhet të ndërtohet mbi meritën, aftësinë dhe punën e ndershme. Çdo qytetar duhet të ketë mundësi të barabarta për arsim cilësor, punësim, sipërmarrje dhe zhvillim profesional, pa privilegje, nepotizëm apo diskriminim.',
        ],
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

function ensure_utf8mb4_tables(PDO $pdo): void {
    $stmt = $pdo->query("
        SELECT TABLE_NAME, TABLE_COLLATION
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME IN ('agreements', 'core_values', 'ideas', 'messages', 'settings', 'users')
    ");
    if (!$stmt) {
        return;
    }
    while ($tbl = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $collation = strtolower((string)($tbl['TABLE_COLLATION'] ?? ''));
        $name = (string)($tbl['TABLE_NAME'] ?? '');
        if ($name !== '' && $collation !== '' && strpos($collation, 'utf8mb4') !== 0) {
            $pdo->exec("ALTER TABLE `{$name}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }
    }
}

function ensure_schema_up_to_date(PDO $pdo, array &$global_settings): void {
    $current_version = (int)($global_settings['schema_version'] ?? 0);
    if ($current_version >= AK_SCHEMA_VERSION) {
        return;
    }

    try {
        // 0. Upgrade legacy latin1 tables cleanly to utf8mb4
        ensure_utf8mb4_tables($pdo);

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

            if (!$had_full_name) {
                $pdo->exec("UPDATE `users` SET `is_public` = 1 WHERE `role` = 'admin' AND `profile_picture` IS NOT NULL AND `profile_picture` <> ''");
            }
        }

        // 3. Add non-destructive bilingual columns to `core_values` and align SQ/EN fields
        $cv_cols = table_columns_map($pdo, 'core_values');
        if (!empty($cv_cols)) {
            add_column_if_missing($pdo, 'core_values', $cv_cols, 'title_sq', 'VARCHAR(255) DEFAULT NULL');
            add_column_if_missing($pdo, 'core_values', $cv_cols, 'description_sq', 'TEXT DEFAULT NULL');

            $official_cv = official_core_values_bilingual();
            $cv_rows = $pdo->query("SELECT `id`, `title`, `description`, `title_sq`, `description_sq` FROM `core_values`")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cv_rows as $cv_row) {
                $cid = (int)$cv_row['id'];
                $cur_title = trim((string)($cv_row['title'] ?? ''));
                $cur_sq = trim((string)($cv_row['title_sq'] ?? ''));
                if ($cur_sq === '' && isset($official_cv[$cid]) && mb_stripos($cur_title, 'NE JEMI PËR', 0, 'UTF-8') !== false) {
                    $upd_cv = $pdo->prepare("
                        UPDATE `core_values`
                        SET `title` = ?, `description` = ?, `title_sq` = ?, `description_sq` = ?
                        WHERE `id` = ?
                    ");
                    $upd_cv->execute([
                        $official_cv[$cid]['title'],
                        $official_cv[$cid]['description'],
                        $official_cv[$cid]['title_sq'],
                        $official_cv[$cid]['description_sq'],
                        $cid,
                    ]);
                }
            }
        }

        // 4. Add non-destructive bilingual, schema_version & cache_version columns to `settings`
        $settings_cols = table_columns_map($pdo, 'settings');
        if (!empty($settings_cols)) {
            add_column_if_missing($pdo, 'settings', $settings_cols, 'schema_version', 'INT(11) NOT NULL DEFAULT 0');
            add_column_if_missing($pdo, 'settings', $settings_cols, 'cache_version', 'INT(11) NOT NULL DEFAULT 1');

            $en_defaults = default_settings_en();
            $sq_defaults = default_settings_sq();
            $text_cols = [
                'site_description_sq', 'home_core_values_text_sq', 'values_page_text_sq',
                'ideas_page_text_sq', 'contact_page_text_sq', 'email_verification_message_sq',
            ];
            foreach ($sq_defaults as $col => $default_val) {
                $type = in_array($col, $text_cols, true) ? 'TEXT DEFAULT NULL' : 'VARCHAR(255) DEFAULT NULL';
                add_column_if_missing($pdo, 'settings', $settings_cols, $col, $type);
            }

            $row = $pdo->query("SELECT * FROM `settings` WHERE `id` = 1")->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                $all = array_merge($en_defaults, $sq_defaults, [
                    'schema_version' => AK_SCHEMA_VERSION,
                    'cache_version' => 2,
                ]);
                $cols_sql = '`' . implode('`, `', array_keys($all)) . '`';
                $ph_sql = implode(', ', array_fill(0, count($all), '?'));
                $ins = $pdo->prepare("INSERT INTO `settings` (`id`, {$cols_sql}) VALUES (1, {$ph_sql})");
                $ins->execute(array_values($all));
            } else {
                // Detect if legacy Albanian texts were stored inside the un-suffixed (English) columns
                $en_has_albanian = (
                    trim((string)($row['menu_home'] ?? '')) === 'Kreu'
                    || mb_stripos((string)($row['home_banner_text'] ?? ''), 'Shqip', 0, 'UTF-8') !== false
                    || mb_stripos((string)($row['values_page_title'] ?? ''), 'Vlerat', 0, 'UTF-8') !== false
                );

                $updates = [];
                $params = [];

                if (in_array(trim((string)($row['site_title'] ?? '')), ['', 'Frymaere', 'Ardhmja'], true)) {
                    $updates[] = "`site_title` = ?";
                    $params[] = 'AsamblejaKombetare';
                }

                if ($en_has_albanian) {
                    // Place authentic Albanian texts into *_sq and accurate English translations into base columns
                    foreach ($sq_defaults as $sq_col => $sq_val) {
                        $updates[] = "`{$sq_col}` = ?";
                        $params[] = $sq_val;
                    }
                    foreach ($en_defaults as $en_col => $en_val) {
                        if ($en_col === 'site_title') {
                            continue;
                        }
                        $updates[] = "`{$en_col}` = ?";
                        $params[] = $en_val;
                    }
                } else {
                    foreach ($sq_defaults as $col => $val) {
                        if (!isset($row[$col]) || trim((string)$row[$col]) === '') {
                            $updates[] = "`{$col}` = ?";
                            $params[] = $val;
                        }
                    }
                }

                $next_cache_ver = max(2, ((int)($row['cache_version'] ?? 1)) + 1);
                $updates[] = "`cache_version` = ?";
                $params[] = $next_cache_ver;
                $updates[] = "`schema_version` = ?";
                $params[] = AK_SCHEMA_VERSION;

                $upd_sql = "UPDATE `settings` SET " . implode(', ', $updates) . " WHERE `id` = 1";
                $stmt = $pdo->prepare($upd_sql);
                $stmt->execute($params);
            }

            $stmt = $pdo->query("SELECT * FROM `settings` WHERE `id` = 1");
            if ($stmt) {
                $global_settings = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
            }
        }

        if (function_exists('flush_app_caches')) {
            flush_app_caches($pdo, false);
        }
    } catch (Throwable $e) {
        error_log('AsamblejaKombetare schema migration error: ' . $e->getMessage());
    }
}
