-- ============================================================================
-- Non-Destructive Database Upgrade Script for AsamblejaKombetare (`upgrade.sql`)
-- ============================================================================
-- Safe to run on an EXISTING database with registered users, ideas, core values,
-- agreements, messages, and settings.
--
-- Note: The PHP application (`includes/migrations.php`) ALSO runs these exact
-- non-destructive checks automatically on first request, so running this file
-- manually in phpMyAdmin is completely optional.
-- ============================================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `rate_limits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(50) NOT NULL,
  `ip_hash` char(64) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_action_ip_time` (`action`, `ip_hash`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP PROCEDURE IF EXISTS `ak_add_col_if_missing`;
DELIMITER $$
CREATE PROCEDURE `ak_add_col_if_missing`(
    IN p_table VARCHAR(64),
    IN p_col VARCHAR(64),
    IN p_def VARCHAR(255)
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = p_table
          AND COLUMN_NAME = p_col
    ) THEN
        SET @ddl = CONCAT('ALTER TABLE `', p_table, '` ADD COLUMN `', p_col, '` ', p_def);
        PREPARE stmt FROM @ddl;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$
DELIMITER ;

-- Preserve existing users while adding password-reset & public-profile fields
CALL `ak_add_col_if_missing`('users', 'verify_expires_at', 'DATETIME DEFAULT NULL');
CALL `ak_add_col_if_missing`('users', 'reset_token', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('users', 'reset_expires_at', 'DATETIME DEFAULT NULL');
CALL `ak_add_col_if_missing`('users', 'full_name', 'VARCHAR(120) DEFAULT NULL');
CALL `ak_add_col_if_missing`('users', 'party_role', 'VARCHAR(120) DEFAULT NULL');
CALL `ak_add_col_if_missing`('users', 'party_role_sq', 'VARCHAR(120) DEFAULT NULL');
CALL `ak_add_col_if_missing`('users', 'bio', 'TEXT DEFAULT NULL');
CALL `ak_add_col_if_missing`('users', 'bio_sq', 'TEXT DEFAULT NULL');
CALL `ak_add_col_if_missing`('users', 'privacy_accepted_at', 'DATETIME DEFAULT NULL');

-- Preserve existing core_values while adding optional Albanian translation columns
CALL `ak_add_col_if_missing`('core_values', 'title_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('core_values', 'description_sq', 'TEXT DEFAULT NULL');

-- Preserve existing settings while adding Albanian columns & schema_version
CALL `ak_add_col_if_missing`('settings', 'schema_version', 'INT(11) NOT NULL DEFAULT 0');
CALL `ak_add_col_if_missing`('settings', 'site_description_sq', 'TEXT DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'home_banner_text_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'join_us_btn_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'home_core_values_title_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'home_core_values_text_sq', 'TEXT DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'view_values_btn_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'home_latest_ideas_title_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'view_ideas_btn_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'values_page_title_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'values_page_text_sq', 'TEXT DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'ideas_page_title_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'ideas_page_text_sq', 'TEXT DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'submit_idea_btn_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'contact_page_title_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'contact_page_text_sq', 'TEXT DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'register_page_title_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'login_page_title_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'footer_text_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'email_verification_subject_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'email_verification_message_sq', 'TEXT DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'menu_home_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'menu_values_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'menu_ideas_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'menu_profiles_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'menu_contact_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'menu_profile_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'menu_admin_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'menu_logout_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'menu_login_sq', 'VARCHAR(255) DEFAULT NULL');
CALL `ak_add_col_if_missing`('settings', 'menu_register_sq', 'VARCHAR(255) DEFAULT NULL');

DROP PROCEDURE IF EXISTS `ak_add_col_if_missing`;

-- Update placeholder brand names to AsamblejaKombetare while preserving custom settings
UPDATE `settings`
SET `site_title` = 'AsamblejaKombetare'
WHERE `id` = 1 AND `site_title` IN ('Frymaere', 'Ardhmja', '');

-- Keep admins who already uploaded an avatar visible on Public Profiles
UPDATE `users`
SET `is_public` = 1
WHERE `role` = 'admin' AND `profile_picture` IS NOT NULL AND `profile_picture` <> '';
