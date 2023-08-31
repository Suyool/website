-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 29, 2023 at 09:22 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `suyool_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `emailsubscriber`
--

CREATE TABLE `emailsubscriber` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `emailsubscriber`
--

INSERT INTO `emailsubscriber` (`id`, `email`, `created`, `updated`) VALUES
(21, 'anthony@gmail.com', '2023-06-16 12:36:41', '2023-06-30 14:16:03'),
(23, 'admin@admin.com', '2023-06-16 12:54:35', '2023-06-30 14:16:03'),
(25, 'trh@elbarid.com', '2023-06-16 12:59:13', '2023-06-30 14:16:03'),
(26, 'trh1@elbarid.com', '2023-06-16 12:59:48', '2023-06-30 14:16:03'),
(27, 'trh12@elbarid.com', '2023-06-16 13:03:11', '2023-06-30 14:16:03'),
(29, 'admin@qq', '2023-06-16 14:25:40', '2023-06-30 14:16:03'),
(30, 'admin@qq.lb', '2023-06-16 14:31:45', '2023-06-30 14:16:03'),
(31, 'an@qq', '2023-06-16 14:32:06', '2023-06-30 14:16:03'),
(32, 'abccd@qq', '2023-06-16 14:32:18', '2023-06-30 14:16:03'),
(35, 'jaghalanthonysaliba@hotmail.com', '2023-06-16 16:34:24', '2023-06-30 14:16:03'),
(39, 'anthony.saliba@elbarid.com', '2023-06-20 07:43:41', '2023-06-30 14:16:03'),
(41, 'anthony.saliban@gmail.com', '2023-07-25 14:54:57', '2023-07-25 14:54:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isVerified` tinyint(1) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `roles`, `password`, `isVerified`, `created`, `updated`) VALUES
(6, 'anthony.saliban@gmail.com', 'anthony', '[\"ROLE_ADMIN\"]', '$argon2id$v=19$m=65536,t=4,p=1$dDhScVpOZ204NVhjeXhubQ$Kpdcz/muuHpHCeP+Q8sq1X5YIkJml1iGoUoQYKjH+5k', 1, '2023-08-23 09:40:28', '2023-08-23 09:40:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `emailsubscriber`
--
ALTER TABLE `emailsubscriber`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `emailsubscriber`
--
ALTER TABLE `emailsubscriber`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
