-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2025 at 04:14 AM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `undertale_game`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`, `last_login`, `is_active`) VALUES
(1, 'admin', '$2y$10$xhI5y3IfY/bDjUHSunRmA.THiFFS5YGwX8QGtN1rt88fmUITUXId2', '2025-12-10 17:31:49', '2025-12-13 19:11:52', 1);

-- --------------------------------------------------------

--
-- Table structure for table `characters`
--

CREATE TABLE `characters` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `characters`
--

INSERT INTO `characters` (`id`, `name`, `description`, `role`, `image_url`, `bio`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Frisk', 'The protagonist', 'Main Character', 'assets/uploads/characters/1765612886_693d1d562a786.png', 'You are Frisk, a human child who has fallen into the Underground.', 1, '2025-12-10 17:31:49', '2025-12-13 08:01:26'),
(2, 'Flowey', 'A small golden flower', 'Antagonist', 'assets/uploads/characters/1765640502_693d893601c9a.png', 'A golden flower that greets you in the Ruins.', 1, '2025-12-10 17:31:49', '2025-12-13 15:41:42'),
(3, 'Toriel', 'A majestic goat-like creature', 'Guardian', 'assets/uploads/characters/1765640672_693d89e0d9944.png', 'The caretaker of the Ruins.', 1, '2025-12-10 17:31:49', '2025-12-13 15:44:32'),
(4, 'Sans', 'A skeleton wearing a blue hoodie', 'Ally', 'assets/uploads/characters/1765640657_693d89d18d5e1.png', 'A comedic skeleton who appears throughout your journey.', 1, '2025-12-10 17:31:49', '2025-12-13 15:44:17'),
(5, 'Papyrus', 'A tall skeleton with a deep voice', 'Ally', 'assets/uploads/characters/1765640684_693d89ec9c229.png', 'Sans brother, Papyrus is enthusiastic and energetic.', 1, '2025-12-10 17:31:49', '2025-12-13 15:44:44'),
(6, 'Undyne', 'Undyne chases the protagonist into Hotland', 'protagonist', 'assets/uploads/characters/1765640819_693d8a7388aef.png', 'Undyne is hot-blooded and passionate about everything that she does.Undyne loves to help others and mentors Shyren and Papyrus in various skills. She dislikes puzzles, loves japes,[6] enjoys playing the piano.', 1, '2025-12-12 13:29:49', '2025-12-13 15:46:59'),
(7, 'Alpys', 'Alphys is passionate about her work and interests', 'protagonist', 'assets/uploads/characters/1765641004_693d8b2c145ce.png', 'Alphys is a shy introvert who frequently stutters during conversation. She has low self-esteem, makes self-deprecating comments.', 1, '2025-12-12 13:49:05', '2025-12-13 15:50:04'),
(8, 'Asriel', 'Asriel\'s name has several origins and meanings behind it.', 'protaghois', 'assets/uploads/characters/1765641086_693d8b7ea2654.png', 'Asriel\'s body becomes more geometric, his horns are longer, and his claws and teeth get sharper. He has a pair of wings that continuously change color, and his lower body becomes sharp and heart-shaped.', 1, '2025-12-12 15:43:17', '2025-12-13 15:51:26'),
(9, 'Chara', 'Chara is first seen in Undertale\'s prologue', 'protagonist', 'assets/uploads/characters/1765643776_693d96008743d.png', 'Chara became terminally ill from consuming buttercups. Their final wish was to see the Golden Flowers at their home village, although it could not be fulfilled.', 1, '2025-12-13 16:36:16', '2025-12-13 16:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `characters_unlocked`
--

CREATE TABLE `characters_unlocked` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `character_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unlocked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `description`, `image_url`, `created_at`) VALUES
(1, 'THE RUINS', 'The starting area where humans fall.', 'uploads/1765649815_Ruins.png', '2025-12-13 17:17:54'),
(2, 'SNOWDIN', 'A cold, snowy forest town.', 'uploads/1765649840_Snowdin.png', '2025-12-13 17:17:54'),
(3, 'WATERFALL', 'A dark, marshy cavern filled with glowing stones.', 'uploads/1765649927_Waterfall.png', '2025-12-13 17:17:54'),
(4, 'HOTLAND', 'A volcanic region filled with heat and technology.', 'uploads/1765649942_Hotland.png', '2025-12-13 17:17:54'),
(5, 'NEW HOME', 'The capital of the Underground.', 'uploads/1765649953_New_Home.png', '2025-12-13 17:17:54'),
(6, 'True Lab', 'Where conflicts are resolved (peacefully or otherwise).', 'uploads/1765649975_True_Lab.png', '2025-12-13 17:17:54');

-- --------------------------------------------------------

--
-- Table structure for table `game_scores`
--

CREATE TABLE `game_scores` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `turns_used` int(11) NOT NULL DEFAULT 0,
  `damage_dealt` int(11) NOT NULL DEFAULT 0,
  `hp_remaining` int(11) NOT NULL DEFAULT 20,
  `route_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'neutral',
  `played_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `game_scores`
--

INSERT INTO `game_scores` (`id`, `user_id`, `score`, `turns_used`, `damage_dealt`, `hp_remaining`, `route_type`, `played_at`) VALUES
(1, 1, 1000, 5, 50, 15, 'pacifist', '2025-12-10 05:37:07'),
(2, 1, 1500, 8, 80, 12, 'neutral', '2025-12-10 05:37:07');

-- --------------------------------------------------------

--
-- Table structure for table `music`
--

CREATE TABLE `music` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_number` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `music`
--

INSERT INTO `music` (`id`, `title`, `file_path`, `order_number`, `created_at`) VALUES
(1, 'gaming session', 'assets/uploads/music/1765557032_693c43289dc67.mp3', 1, '2025-12-12 16:30:32'),
(2, 'upbetMusic', 'assets/uploads/music/1765559653_693c4d65e91a7.mp3', 2, '2025-12-12 17:14:13'),
(3, 'Retro Arcade', 'assets/uploads/music/1765643919_693d968fb1f7d.mp3', 1, '2025-12-13 16:38:39'),
(4, 'TataMusic', 'assets/uploads/music/1765643958_693d96b640eb7.mp3', 2, '2025-12-13 16:39:18'),
(5, 'Jumpingbunny', 'assets/uploads/music/1765643986_693d96d2654e8.mp3', 3, '2025-12-13 16:39:46');

-- --------------------------------------------------------

--
-- Table structure for table `stories`
--

CREATE TABLE `stories` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_number` int(11) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stories`
--

INSERT INTO `stories` (`id`, `title`, `content`, `description`, `order_number`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Prologue: The Fall', 'You wake up in the ruins. The light from your phone guides you through the darkness.', 'The beginning of your journey', 1, 1, '2025-12-10 17:31:49', '2025-12-10 17:31:49'),
(2, 'Encounter', 'You meet a small flower. It introduces itself as Flowey.', 'Your first encounter', 2, 1, '2025-12-10 17:31:49', '2025-12-10 17:31:49'),
(3, 'The Ruins', 'You venture deeper into the Ruins. Ancient architecture surrounds you.', 'Exploring the ancient ruins', 3, 1, '2025-12-10 17:31:49', '2025-12-10 17:31:49'),
(4, 'AngkringanBae', 'angkringan bae adalah angkringan yang buka 24 jam', 'Penjual Sate dan Nasi Bakar', 6, 1, '2025-12-12 15:58:11', '2025-12-12 15:58:11'),
(6, 'NasgorIpul', 'nasgor ipul itu enak banget deh ga boong', 'pedagang nasgor', 1, 1, '2025-12-12 16:01:01', '2025-12-12 16:01:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `last_login`, `is_active`) VALUES
(1, 'player1', 'player1@undertale.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-12-10 05:37:07', NULL, 1),
(2, 'frisk', 'frisk@underground.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-12-10 05:37:07', NULL, 1),
(3, 'sans', 'sans@snowdin.com', '$2y$10$VI5o/V5TMQ2LC.wBJ4dyNO2.L4A4WphC.wUGSFGjC/NSMT21D9jTe', '2025-12-10 16:43:25', '2025-12-13 19:13:12', 1),
(4, 'yunan', 'yunanpkl1@gmail.com', '$2y$10$C9XIba7vr.ER4JSnO5D0jOjruXuY02jjWuJNaA7Rik71o/vovbzym', '2025-12-11 07:16:58', '2025-12-14 03:13:24', 1),
(5, 'ejend', 'ejend@gmail.com', '$2y$10$aBlJMq0gTts54gMGWnebkeecY/TfPpnRFvw4iCGiJGsyi0pzXXrly', '2025-12-11 07:36:47', NULL, 1),
(6, 'tsaqif', 'tsaqif17@gmail.com', '$2y$10$kAnpU2iu/bh.MmuP6HZmtuYZnlrkjv28mV1qQiVPs4XdouslrbClq', '2025-12-13 03:43:34', '2025-12-13 04:44:33', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `level` int(11) DEFAULT 1,
  `exp` int(11) DEFAULT 0,
  `gold` int(11) DEFAULT 0,
  `battles_won` int(11) DEFAULT 0,
  `battles_lost` int(11) DEFAULT 0,
  `pacifist_count` int(11) DEFAULT 0,
  `genocide_count` int(11) DEFAULT 0,
  `items_collected` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `achievements` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_progress`
--

INSERT INTO `user_progress` (`id`, `user_id`, `level`, `exp`, `gold`, `battles_won`, `battles_lost`, `pacifist_count`, `genocide_count`, `items_collected`, `achievements`, `last_updated`, `status`) VALUES
(1, 1, 1, 0, 0, 0, 0, 0, 0, NULL, NULL, '2025-12-10 05:37:07', NULL),
(2, 3, 1, 0, 0, 0, 0, 0, 0, NULL, NULL, '2025-12-10 16:43:25', NULL),
(3, 4, 1, 0, 0, 16, 11, 0, 0, NULL, NULL, '2025-12-13 18:56:52', 'mantap pokoknya');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`);

--
-- Indexes for table `characters`
--
ALTER TABLE `characters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `characters_unlocked`
--
ALTER TABLE `characters_unlocked`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_character` (`user_id`,`character_name`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `game_scores`
--
ALTER TABLE `game_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_score` (`score`);

--
-- Indexes for table `music`
--
ALTER TABLE `music`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_order` (`order_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `characters`
--
ALTER TABLE `characters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `characters_unlocked`
--
ALTER TABLE `characters_unlocked`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `game_scores`
--
ALTER TABLE `game_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `music`
--
ALTER TABLE `music`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `characters`
--
ALTER TABLE `characters`
  ADD CONSTRAINT `characters_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `characters_unlocked`
--
ALTER TABLE `characters_unlocked`
  ADD CONSTRAINT `characters_unlocked_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_scores`
--
ALTER TABLE `game_scores`
  ADD CONSTRAINT `game_scores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stories`
--
ALTER TABLE `stories`
  ADD CONSTRAINT `stories_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
