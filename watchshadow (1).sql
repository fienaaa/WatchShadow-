-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 05, 2025 at 06:03 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `watchshadow`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$YXli3HNNYnvEiFwtU1nJfuEHzr45q.gyXUoTHNjAy5srH/KpnhIZ2', '2025-11-03 13:20:14');

-- --------------------------------------------------------

--
-- Table structure for table `breaches`
--

CREATE TABLE `breaches` (
  `id` int(11) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `source` varchar(255) DEFAULT 'unknown',
  `leak_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `breaches`
--

INSERT INTO `breaches` (`id`, `keyword`, `source`, `leak_date`, `description`, `created_at`) VALUES
(1, 'test@example.com', 'sample-leak-2024', '2024-05-10', 'Sample dataset for demonstration', '2025-10-31 15:02:31'),
(2, 'alice@example.com', 'customer-db-2019', '2019-11-20', 'Old leak sample', '2025-10-31 15:02:31'),
(3, 'naokinarukami@gmail.com', 'ExampleLeakSite1', '2025-10-28', 'Example leaked password found.', '2025-11-03 12:34:58'),
(4, 'tor.accmarket@gmail.com', 'TOR Access Market', '2025-10-31', 'Counterfeit email database leak.', '2025-11-03 12:34:58'),
(5, '2zjwjaepx465unk4x63uiabgyl2bzrcyctrcwuynwjdejgzlawtks4qd.onion', 'AnonymousDreams Blog', '2025-10-30', 'Top quality counterfeit emails available.', '2025-11-03 12:34:58'),
(7, 'izzrieqilhan@gmail.com', 'ExampleLeakSite1', '2025-10-28', 'Example leaked password found.', '2025-11-03 12:36:14'),
(8, 'izzrieqilhan@gmail.com', 'ExampleLeakSite2', '2025-10-30', 'Credentials leaked in dark web forum.', '2025-11-03 12:36:14'),
(9, 'izzrieqilhan@gmail.com', 'Dark Web Archive', '2025-11-01', 'Email appeared in recent breach list.', '2025-11-03 12:36:14'),
(10, 'testing', 'Dark Web Notifier', '2019-05-22', 'testing', '2025-11-05 16:38:47'),
(11, 'izzrieqilhan@gmail.com', 'Dark Web Notifier', '2025-11-04', 'hello', '2025-11-05 16:41:42');

-- --------------------------------------------------------

--
-- Table structure for table `search_logs`
--

CREATE TABLE `search_logs` (
  `id` int(11) NOT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `found` tinyint(1) DEFAULT 0,
  `searched_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `search_logs`
--

INSERT INTO `search_logs` (`id`, `keyword`, `found`, `searched_at`) VALUES
(1, 'test', 1, '2025-10-31 15:31:37'),
(2, 'izzrieqilhan@gmail.com', 0, '2025-10-31 15:42:47'),
(3, 'izzrieqilhan@gmail.com', 0, '2025-10-31 15:52:30'),
(4, 'izzrieqilhan@gmail.com', 0, '2025-10-31 15:52:45'),
(5, 'izzrieqilhan@gmail.com', 0, '2025-10-31 15:55:46'),
(6, 'naokinarukami@gmail.com', 0, '2025-10-31 16:56:59'),
(7, 'ALexweed@gmail.com', 0, '2025-10-31 16:57:31'),
(8, 'ALexweeds00@gmail.com', 0, '2025-10-31 16:57:43'),
(9, 'ALexweeds00@gmail.com', 0, '2025-10-31 17:00:03'),
(10, 'izzrieqilhan@gmail.com', 0, '2025-10-31 17:00:14'),
(11, 'test', 1, '2025-11-03 12:03:24'),
(12, 'naokinarukami@gmail.com', 0, '2025-11-03 12:05:20'),
(13, 'naokinarukami@gmail.com', 0, '2025-11-03 12:12:21'),
(14, 'naokinarukami@gmail.com', 0, '2025-11-03 12:13:56'),
(15, 'izzrieqilhan@gmail.com', 1, '2025-11-03 12:36:29'),
(16, 'izzrieqilhan@gmail.com', 1, '2025-11-03 13:00:07'),
(17, 'izzrieqilhan@gmail.com', 1, '2025-11-03 15:40:04'),
(18, 'izzrieqilhan@gmail.com', 1, '2025-11-03 16:20:10'),
(19, 'izzrieqilhan@gmail.com', 1, '2025-11-03 16:20:11'),
(20, 'test', 1, '2025-11-03 16:45:13'),
(21, 'izzrieqilhan@gmail.com', 1, '2025-11-03 16:46:04'),
(22, 'izzrieqilhan@gmail.com', 1, '2025-11-05 12:12:36'),
(23, 'izzrieqilhan@gmail.com', 1, '2025-11-05 12:13:45'),
(24, 'izzrieqilhan@gmail.com', 1, '2025-11-05 12:22:42'),
(25, 'izzrieqilhan@gmail.com', 1, '2025-11-05 12:23:27'),
(26, 'izzrieqilhan@gmail.com', 1, '2025-11-05 12:32:57'),
(27, 'izzrieqilhan@gmail.com', 1, '2025-11-05 13:04:36'),
(28, 'izzrieqilhan@gmail.com', 1, '2025-11-05 13:13:52'),
(29, 'izzrieqilhan@gmail.com', 1, '2025-11-05 13:15:21'),
(30, 'izzrieqilhan@gmail.com', 1, '2025-11-05 13:16:29'),
(31, 'izzrieqilhan@gmail.com', 1, '2025-11-05 13:21:16'),
(32, 'izzrieqilhan@gmail.com', 1, '2025-11-05 13:59:12'),
(33, 'izzrieqilhan@gmail.com', 1, '2025-11-05 14:05:16');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `plan` enum('weekly','monthly','yearly') DEFAULT 'weekly',
  `subscription_start` datetime DEFAULT current_timestamp(),
  `subscription_end` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscribers`
--

INSERT INTO `subscribers` (`id`, `email`, `keyword`, `subscribed_at`, `plan`, `subscription_start`, `subscription_end`) VALUES
(1, 'izzrieqilhan@gmail.com', 'test', '2025-11-03 12:18:37', 'weekly', '2025-11-04 00:27:39', NULL),
(3, 'brone@gmail.com', 'bash', '2025-11-03 13:57:52', 'weekly', '2025-11-04 00:27:39', NULL),
(4, 'ahmad@gmail.com', 'testing', '2025-11-03 09:28:49', 'monthly', '2025-11-04 00:28:49', '2025-12-03 17:28:49'),
(5, 'ahmad@example.com', 'testing', '2025-11-03 09:50:49', 'monthly', '2025-11-04 00:50:49', '2025-12-03 17:50:49'),
(7, 'ahmad@example.com', 'testing', '2025-11-03 11:22:53', 'monthly', '2025-11-04 02:22:53', '2025-12-03 19:22:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `created_at`) VALUES
(1, 'Izz', 'izzrieqilhan@gmail.com', '$2y$10$Vki5a9Jhwf3hFxCpbPeN0uVJ5p.LiNggJ1btghhMCY.TqPBsKyBKe', '2025-11-05 21:25:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `breaches`
--
ALTER TABLE `breaches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `search_logs`
--
ALTER TABLE `search_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `breaches`
--
ALTER TABLE `breaches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `search_logs`
--
ALTER TABLE `search_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
