-- demo_data.sql
USE ardhmja;

INSERT INTO `users` (`username`, `email`, `password`, `role`, `status`) VALUES
('john_doe', 'john@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'approved'),
('jane_smith', 'jane@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'approved'),
('pending_user', 'pending@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'pending');
-- Password for all demo users is 'password'

INSERT INTO `core_values` (`title`, `description`) VALUES
('A New Digital Future', 'We believe in modernizing infrastructure and expanding high-speed internet access for all citizens.'),
('Green Energy Initiative', 'Transitioning our country to 100% renewable energy by 2040 to protect the environment and create jobs.'),
('Healthcare for All', 'Ensuring that every citizen has access to affordable and high-quality healthcare.');

INSERT INTO `ideas` (`user_id`, `title`, `description`, `status`) VALUES
(2, 'Community Gardens Project', 'We should encourage local communities to start their own gardens to promote sustainability and fresh produce.', 'approved'),
(3, 'Free Public Transport', 'Making public transport free to reduce traffic congestion and lower emissions.', 'approved'),
(4, 'More Funding for Schools', 'Increase funding for public schools to ensure smaller class sizes and better resources for teachers.', 'pending');

INSERT INTO `agreements` (`user_id`, `value_id`) VALUES
(2, 1),
(2, 2),
(3, 1),
(3, 3);

INSERT INTO `messages` (`name`, `email`, `message`) VALUES
('Alice Johnson', 'alice@example.com', 'I would love to learn more about volunteering opportunities in Tirana.'),
('Bob Williams', 'bob@example.com', 'The Green Energy Initiative is exactly what we need. How can I contribute?');
