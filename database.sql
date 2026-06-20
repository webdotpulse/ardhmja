CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_title` varchar(255) NOT NULL DEFAULT 'Ardhmja',
  `site_description` text,
  `home_banner_text` varchar(255) DEFAULT 'Building a New Republic Together',
  `join_us_btn` varchar(255) DEFAULT 'Join Us Now',
  `home_core_values_title` varchar(255) DEFAULT 'Our Core Values',
  `home_core_values_text` text,
  `view_points_btn` varchar(255) DEFAULT 'View Party Points',
  `home_latest_ideas_title` varchar(255) DEFAULT 'Latest Community Ideas',
  `view_ideas_btn` varchar(255) DEFAULT 'View All Ideas',
  `points_page_title` varchar(255) DEFAULT 'Our Party Points',
  `points_page_text` text,
  `ideas_page_title` varchar(255) DEFAULT 'Community Ideas',
  `ideas_page_text` text,
  `submit_idea_btn` varchar(255) DEFAULT 'Submit an Idea',
  `contact_page_title` varchar(255) DEFAULT 'Contact Us',
  `contact_page_text` text,
  `register_page_title` varchar(255) DEFAULT 'Register',
  `login_page_title` varchar(255) DEFAULT 'Login',
  `footer_text` varchar(255) DEFAULT 'All rights reserved.',
  PRIMARY KEY (`id`)
);

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
);

CREATE TABLE `party_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TABLE `agreements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_id` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_point` (`user_id`,`point_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`point_id`) REFERENCES `party_points`(`id`) ON DELETE CASCADE
);

CREATE TABLE `ideas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

INSERT INTO `settings` (`site_title`, `site_description`, `home_banner_text`, `join_us_btn`, `home_core_values_title`, `home_core_values_text`, `view_points_btn`, `home_latest_ideas_title`, `view_ideas_btn`, `points_page_title`, `points_page_text`, `ideas_page_title`, `ideas_page_text`, `submit_idea_btn`, `contact_page_title`, `contact_page_text`, `register_page_title`, `login_page_title`, `footer_text`) VALUES ('Ardhmja', 'A new Albanian starter party.', 'Join us to build the future', 'Join Us Now', 'Our Core Values', 'We are dedicated to progress, transparency, and giving power back to the people. Explore our Party Points to understand our vision for Albania.', 'View Party Points', 'Latest Community Ideas', 'View All Ideas', 'Our Party Points', 'Discover our core propositions for building a new future.', 'Community Ideas', 'Explore the ideas submitted by our members and approved by our party admins.', 'Submit an Idea', 'Contact Us', 'Have questions, ideas, or want to get involved? Please send us a message below.', 'Register', 'Login', 'All rights reserved.');
INSERT INTO `users` (`username`, `password`, `role`, `status`) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'approved'); -- password is 'password'
