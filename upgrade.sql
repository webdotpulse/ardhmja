-- =============================================================================
-- AsamblejaKombetare — Non-Destructive Bilingual Alignment & UTF-8 Upgrade SQL
-- Target: Combell MySQL 8.0 (`ID189547_albania`) via phpMyAdmin Import or SQL tab
--
-- SAFE FOR LIVE PRODUCTION:
--   * Preserves 100% of registered `users`, `agreements`, `ideas`, and `messages`
--   * Converts legacy `latin1` tables cleanly to `utf8mb4`
--   * Moves/places all Albanian texts into `*_sq` fields (`settings` & `core_values`)
--   * Populates accurate English translations into the base English fields
--   * Adds/increments `cache_version` to bust cached assets immediately
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Convert legacy latin1 tables cleanly to utf8mb4
ALTER TABLE `agreements` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `core_values` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `ideas` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `messages` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `settings` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- 2. Ensure `cache_version` column exists on `settings` (no routine privileges needed)
SET @has_cache_ver := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'settings'
      AND COLUMN_NAME = 'cache_version'
);
SET @ddl_cache_ver := IF(
    @has_cache_ver = 0,
    'ALTER TABLE `settings` ADD COLUMN `cache_version` INT NOT NULL DEFAULT 1 AFTER `schema_version`',
    'SELECT 1'
);
PREPARE ak_stmt FROM @ddl_cache_ver;
EXECUTE ak_stmt;
DEALLOCATE PREPARE ak_stmt;

START TRANSACTION;

-- 3. Align `settings` (id = 1): English in base columns, Albanian in `*_sq` columns
UPDATE `settings`
SET
    `site_title` = 'AsamblejaKombetare',

    -- English fields
    `site_description` = '<p>This is a civic and political movement that puts the citizen and the national interest first, with a clear vision for a democratic and European Albania.</p>',
    `home_banner_text` = 'Join Us for a European Albania',
    `join_us_btn` = 'Join Us Now',
    `home_core_values_title` = 'Our Core Values',
    `home_core_values_text` = '<p>To all those who see themselves in these foundational principles and do not feel represented by any of the other alternatives, WE CALL ON YOU to join us, because our country needs the strength, energy, knowledge, mind, and talent of everyone.</p>',
    `view_values_btn` = 'View Core Values',
    `home_latest_ideas_title` = 'Latest Community Ideas',
    `view_ideas_btn` = 'View All Ideas',
    `values_page_title` = 'Our Core Values',
    `values_page_text` = '<p>Discover our core propositions for building a new future.</p>',
    `ideas_page_title` = 'Community Ideas',
    `ideas_page_text` = '<p>Explore the ideas submitted by our members and approved by our movement''s administrators.</p>',
    `submit_idea_btn` = 'Submit an Idea',
    `contact_page_title` = 'Contact Us',
    `contact_page_text` = '<p>Have questions, ideas, or want to get involved? Please send us a message below.</p>',
    `register_page_title` = 'Register',
    `login_page_title` = 'Login',
    `footer_text` = 'All rights reserved.',
    `email_verification_subject` = 'Verify your email for {site_title}',
    `email_verification_message` = 'Hello {username},\n\nPlease click the link below to verify your email address:\n{verify_link}\n\nThank you,\n{site_title}',
    `menu_home` = 'Home',
    `menu_values` = 'Core Values',
    `menu_ideas` = 'Ideas',
    `menu_profiles` = 'Public Profiles',
    `menu_contact` = 'Contact',
    `menu_profile` = 'Profile',
    `menu_admin` = 'Admin Panel',
    `menu_logout` = 'Logout',
    `menu_login` = 'Login',
    `menu_register` = 'Register',

    -- Albanian (`*_sq`) fields
    `site_description_sq` = '<p>Kjo është një lëvizje qytetare dhe politike që vendos qytetarin dhe interesin kombëtar në plan të parë, me një vizion të qartë për një Shqipëri demokratike dhe europiane.</p>',
    `home_banner_text_sq` = 'Bashkohuni me ne për një Shqipëri Europiane',
    `join_us_btn_sq` = 'Bashkohuni Tani',
    `home_core_values_title_sq` = 'Vlerat Tona Themelore',
    `home_core_values_text_sq` = '<p>Për të gjithë ata që e shohin veten në këto parime themelore dhe që nuk ndihen mirë me asnjë prej alternativave të tjera, JU BËJMË THIRRJE që të na bashkoheni, sepse vendi ynë ka nevojë për forcën, energjinë, diturinë, mendjen dhe talentin e të gjithëve.</p>',
    `view_values_btn_sq` = 'Shiko Vlerat Themelore',
    `home_latest_ideas_title_sq` = 'Idetë e Fundit të Komunitetit',
    `view_ideas_btn_sq` = 'Shiko të Gjitha Idetë',
    `values_page_title_sq` = 'Vlerat Tona Themelore',
    `values_page_text_sq` = '<p>Zbuloni propozimet tona thelbësore për ndërtimin e një të ardhmeje të re.</p>',
    `ideas_page_title_sq` = 'Idetë e Komunitetit',
    `ideas_page_text_sq` = '<p>Eksploroni idetë e paraqitura nga anëtarët tanë dhe të miratuara nga administratorët e partisë sonë.</p>',
    `submit_idea_btn_sq` = 'Dërgo një Ide',
    `contact_page_title_sq` = 'Na Kontaktoni',
    `contact_page_text_sq` = '<p>Keni pyetje, ide, ose dëshironi të përfshiheni? Ju lutemi na dërgoni një mesazh më poshtë.</p>',
    `register_page_title_sq` = 'Regjistrohu',
    `login_page_title_sq` = 'Hyr',
    `footer_text_sq` = 'Të gjitha të drejtat e rezervuara.',
    `email_verification_subject_sq` = 'Verifikoni email-in tuaj për {site_title}',
    `email_verification_message_sq` = 'Përshëndetje {username},\n\nJu lutemi klikoni në lidhjen e mëposhtme për të verifikuar adresën tuaj të email-it:\n{verify_link}\n\nFaleminderit,\n{site_title}',
    `menu_home_sq` = 'Kreu',
    `menu_values_sq` = 'Vlerat Themelore',
    `menu_ideas_sq` = 'Idetë',
    `menu_profiles_sq` = 'Profilet',
    `menu_contact_sq` = 'Kontakt',
    `menu_profile_sq` = 'Profili',
    `menu_admin_sq` = 'Admin',
    `menu_logout_sq` = 'Dil',
    `menu_login_sq` = 'Hyr',
    `menu_register_sq` = 'Regjistrohu',

    -- Advance schema & asset cache version
    `schema_version` = 3,
    `cache_version` = COALESCE(`cache_version`, 1) + 1
WHERE `id` = 1;

-- 4. Align all 7 `core_values` in-place (preserving IDs 1..7, timestamps, and member agreements)
INSERT INTO `core_values` (`id`, `title`, `description`, `title_sq`, `description_sq`) VALUES
(
    1,
    '1. WE STAND FOR A DEMOCRATIC ALBANIA',
    'We believe that democracy is the fairest and most legitimate form of state organization. Sovereignty stems from the people and is exercised by them through free, fair, and periodic elections, independent institutions, and respect for the rule of law. We defend political pluralism, the separation and balance of powers, freedom of expression, and the active participation of citizens in public life.',
    '1. NE JEMI PËR NJË SHQIPËRI DEMOKRATIKE',
    'Ne besojmë se demokracia është forma më e drejtë dhe më legjitime e organizimit të shtetit. Sovraniteti buron nga populli dhe ushtrohet prej tij nëpërmjet zgjedhjeve të lira, të ndershme dhe periodike, institucioneve të pavarura dhe respektimit të shtetit të së drejtës. Ne mbrojmë pluralizmin politik, ndarjen dhe balancimin e pushteteve, lirinë e shprehjes dhe pjesëmarrjen aktive të qytetarëve në jetën publike.'
),
(
    2,
    '2. WE STAND FOR A SECULAR ALBANIA',
    'We believe in a state neutral toward religious beliefs, guaranteeing freedom of conscience and religion for every citizen. Religious belief is an individual right within the boundaries of the private sphere. Public decision-making must rest on the Constitution, secular rather than divine laws, science, and the public interest, maintaining a clear separation between the state and religious institutions.',
    '2. NE JEMI PËR NJË SHQIPËRI LAIKE',
    'Ne besojmë në një shtet neutral ndaj besimeve fetare, i cili garanton lirinë e ndërgjegjes dhe të fesë për çdo qytetar. Besimi fetar është një e drejtë individuale, por deri në kufijtë e sferës publike. Vendimmarrja publike duhet të mbështetet mbi Kushtetutën, ligjet tokësore dhe jo hyjnore, shkencën dhe interesin publik, duke ruajtur ndarjen e qartë ndërmjet shtetit dhe institucioneve fetare.'
),
(
    3,
    '3. WE STAND FOR A EUROPEAN ALBANIA',
    'We believe that Albania''s future is inseparable from the European family. We unequivocally support the country''s full integration into the European Union, not merely as a political objective, but as a commitment to building strong institutions, a competitive economy, an independent judiciary, and high democratic standards. Europe represents the natural space for Albania''s development.',
    '3. NE JEMI PËR NJË SHQIPËRI EUROPIANE',
    'Ne besojmë se e ardhmja e Shqipërisë është e pandashme nga familja evropiane. Mbështesim pa mëdyshje integrimin e plotë të vendit në Bashkimin Europian, jo vetëm si një objektiv politik, por si një angazhim për të ndërtuar institucione të forta, ekonomi konkurruese, drejtësi të pavarur dhe standarde të larta demokratike. Evropa përfaqëson hapësirën natyrore të zhvillimit të Shqipërisë.'
),
(
    4,
    '4. WE STAND FOR A JUST ALBANIA',
    'We believe that no one should be above the law. Justice must be independent, impartial, and equal for all. The fight against corruption, transparency, and accountability are indispensable conditions for building citizens'' trust in the state.',
    '4. NE JEMI PËR NJË SHQIPËRI TË DREJTË',
    'Ne besojmë se askush nuk duhet të jetë mbi ligjin. Drejtësia duhet të jetë e pavarur, e paanshme dhe e barabartë për të gjithë. Lufta kundër korrupsionit, transparenca dhe llogaridhënia janë kushte të domosdoshme për ndërtimin e besimit të qytetarëve ndaj shtetit.'
),
(
    5,
    '5. WE STAND FOR A HUMANIST ALBANIA',
    'We place human dignity at the center of politics. We defend the fundamental rights and freedoms of every individual, without distinction of gender, age, origin, belief, or social status. We believe in international cooperation, dialogue, peace, and solidarity as values that strengthen Albania''s development and the well-being of its citizens.',
    '5. NE JEMI PËR NJË SHQIPËRI HUMANISTE',
    'Ne vendosim dinjitetin njerëzor në qendër të politikës. Mbrojmë të drejtat dhe liritë themelore të çdo individi, pa dallim gjinie, moshe, origjine, bindjeje apo statusi social. Besojmë në bashkëpunimin ndërkombëtar, dialogun, paqen dhe solidaritetin si vlera që forcojnë zhvillimin e Shqipërisë dhe mirëqenien e qytetarëve të saj.'
),
(
    6,
    '6. WE STAND FOR A GREEN AND SUSTAINABLE ALBANIA',
    'We believe that environmental protection is an obligation toward both present and future generations. We are committed to protecting forests, rivers, coastlines, biodiversity, and natural resources, fighting pollution and climate change, and developing a green economy that creates prosperity without harming the ecosystem. Economic development must always be sustainable and in harmony with nature.',
    '6. NE JEMI PËR NJË SHQIPËRI TË GJELBËR DHE TË QËNDRUESHME',
    'Ne besojmë se mbrojtja e mjedisit është detyrim ndaj brezave të sotëm dhe atyre që do të vijnë. Angazhohemi për mbrojtjen e pyjeve, lumenjve, brigjeve, biodiversitetit dhe burimeve natyrore, për luftën kundër ndotjes dhe ndryshimeve klimatike, si dhe për zhvillimin e një ekonomie të gjelbër që krijon mirëqenie pa dëmtuar ekosistemin. Zhvillimi ekonomik duhet të jetë gjithmonë i qëndrueshëm dhe në harmoni me natyrën.'
),
(
    7,
    '7. WE STAND FOR AN ALBANIA OF MERIT AND OPPORTUNITY',
    'We believe that success must be built upon merit, ability, and honest work. Every citizen must have equal opportunities for quality education, employment, entrepreneurship, and professional growth, free from privilege, nepotism, or discrimination.',
    '7. NE JEMI PËR NJË SHQIPËRI TË MERITËS DHE MUNDËSIVE',
    'Ne besojmë se suksesi duhet të ndërtohet mbi meritën, aftësinë dhe punën e ndershme. Çdo qytetar duhet të ketë mundësi të barabarta për arsim cilësor, punësim, sipërmarrje dhe zhvillim profesional, pa privilegje, nepotizëm apo diskriminim.'
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `description` = VALUES(`description`),
    `title_sq` = VALUES(`title_sq`),
    `description_sq` = VALUES(`description_sq`);

COMMIT;
