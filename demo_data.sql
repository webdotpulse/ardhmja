-- demo_data.sql (FOR LOCAL / STAGING TEST ENVIRONMENTS ONLY — DO NOT IMPORT IN PRODUCTION)

INSERT IGNORE INTO `users` (`username`, `full_name`, `email`, `email_verified`, `password`, `role`, `status`, `is_public`, `party_role`, `party_role_sq`) VALUES
('john_doe', 'Arben Krasniqi', 'john@test.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'approved', 1, 'Regional Coordinator', 'Koordinator Rajonal'),
('jane_smith', 'Elira Hoxha', 'jane@test.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'approved', 1, 'Policy Advisor', 'Këshilltare e Politikave'),
('pending_user', 'Blerim Dervishi', 'pending@test.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'pending', 0, NULL, NULL);

INSERT INTO `core_values` (`title`, `title_sq`, `description`, `description_sq`) VALUES
('A New Digital Future', 'Një e Ardhme e Re Digjitale', 'Modernizing public services and expanding high-speed internet access for every citizen.', 'Modernizimi i shërbimeve publike dhe zgjerimi i qasjes në internet të shpejtë për çdo qytetar.'),
('Green Energy Initiative', 'Nisma për Energji të Gjelbër', 'Transitioning to clean renewable energy to protect our environment and create sustainable jobs.', 'Kalimi drejt energjisë së pastër e të rinovueshme për të mbrojtur mjedisin dhe për të hapur vende të reja pune.'),
('Healthcare for All', 'Shëndetësi Cilësore për të Gjithë', 'Ensuring every family has access to dignified, affordable, and modern healthcare.', 'Garantimi që çdo familje të ketë qasje në kujdes shëndetësor dinjitoz, të përballueshëm dhe modern.');
