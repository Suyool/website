-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 22, 2023 at 09:27 AM
-- Server version: 8.0.34-0ubuntu0.22.04.1
-- PHP Version: 8.1.2-1ubuntu2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `suyool_shopify`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merchant_credentials`
--

CREATE TABLE `merchant_credentials` (
  `id` bigint UNSIGNED NOT NULL,
  `shop` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `accessToken` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `test_checked` int NOT NULL DEFAULT '0',
  `test_merchant_id` int DEFAULT NULL,
  `test_certificate_key` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `live_checked` int NOT NULL DEFAULT '0',
  `live_merchant_id` int DEFAULT NULL,
  `live_certificate_key` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `merchant_credentials`
--

INSERT INTO `merchant_credentials` (`id`, `shop`, `accessToken`, `test_checked`, `test_merchant_id`, `test_certificate_key`, `live_checked`, `live_merchant_id`, `live_certificate_key`, `created`, `updated`) VALUES
(1, 'suyool-store.myshopify.com', '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', 1, 6, 'FuawNgIwDKYkPZhuIScrcwMXlmnAlS95bjITnyJufWSyKLL3EokqlBqGaBsMqRBoH8vVEbeNmRe0mpoSpRedbEDE8wMIsQgFxcLq', 0, 190, 'lkfdmod02ed9q290jkq0k2dqkdk', '2023-08-02 06:42:02', '2023-08-02 06:42:02');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_08_19_000000_create_failed_jobs_table', 1),
(2, '2021_05_03_050717_create_sessions_table', 1),
(3, '2021_05_05_071311_add_scope_expires_access_token_to_sessions', 1),
(4, '2021_05_11_151158_add_online_access_info_to_sessions', 1),
(5, '2021_05_17_152611_change_sessions_user_id_type', 1),
(6, '2023_07_31_134642_create_merchant_credentials_table', 1),
(7, '2023_07_31_134908_create_requested_data_table', 1),
(8, '2023_07_31_135100_create_orders_table', 1),
(9, '2023_07_31_135209_create_transaction_log_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shop_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,0) NOT NULL,
  `currency` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int DEFAULT NULL,
  `callback_url` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_url` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `env` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `merchant_id` int NOT NULL,
  `flag` int NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `shop_name`, `amount`, `currency`, `status`, `callback_url`, `error_url`, `env`, `merchant_id`, `flag`, `created`, `updated`) VALUES
(2, '5357786956050', 'https://eliostorelb.myshopify.com', '1', 'USD', 0, 'https://eliostorelb.myshopify.com/pages/thank-you-for-your-order', 'https://eliostorelb.myshopify.com/pages/payment-error', 'live', 21, 1, '2023-08-02 09:32:40', '2023-08-02 09:32:40'),
(3, '5357786956051', 'https://eliostorelb.myshopify.com', '1', 'USD', 0, 'https://eliostorelb.myshopify.com/pages/thank-you-for-your-order', 'https://eliostorelb.myshopify.com/pages/payment-error', 'live', 21, 0, '2023-08-09 09:37:26', '2023-08-09 09:37:26');

-- --------------------------------------------------------

--
-- Table structure for table `orders_test`
--

CREATE TABLE `orders_test` (
  `id` int NOT NULL,
  `order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shop_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `currency` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int DEFAULT NULL,
  `callback_url` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_url` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `env` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `merchant_id` int NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders_test`
--

INSERT INTO `orders_test` (`id`, `order_id`, `shop_name`, `amount`, `currency`, `status`, `callback_url`, `error_url`, `env`, `merchant_id`, `created`, `updated`) VALUES
(1, '5357784498450', 'https://eliostorelb.myshopify.com', '100', 'USD', 0, 'https://eliostorelb.myshopify.com/pages/thank-you-for-your-order', 'https://eliostorelb.myshopify.com/pages/payment-error', 'test', 21, '2023-08-02 04:48:10', '2023-08-02 04:48:10');

-- --------------------------------------------------------

--
-- Table structure for table `requested_data`
--

CREATE TABLE `requested_data` (
  `id` bigint UNSIGNED NOT NULL,
  `request_id` int DEFAULT NULL,
  `shop` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shop` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_online` tinyint(1) NOT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `scope` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `user_first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_email_verified` tinyint(1) DEFAULT NULL,
  `account_owner` tinyint(1) DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collaborator` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `session_id`, `shop`, `is_online`, `state`, `created_at`, `updated_at`, `scope`, `access_token`, `expires_at`, `user_id`, `user_first_name`, `user_last_name`, `user_email`, `user_email_verified`, `account_owner`, `locale`, `collaborator`) VALUES
(7, 'offline_suyool-store.myshopify.com', 'suyool-store.myshopify.com', 0, '06fdfd7c-edab-4922-ad6c-7979d5453a51', '2023-08-01 12:45:12', '2023-08-01 12:45:50', 'write_orders', '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', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'offline_charbelss-store.myshopify.com', 'charbelss-store.myshopify.com', 0, '82b0db6f-e012-4ac3-9871-c32f3b49f845', '2023-08-16 03:55:51', '2023-08-16 03:56:34', 'write_orders', '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', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shopify_transaction_log`
--

CREATE TABLE `shopify_transaction_log` (
  `id` bigint UNSIGNED NOT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `merchant_credentials`
--
ALTER TABLE `merchant_credentials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `merchant_credentials_shop_unique` (`shop`),
  ADD UNIQUE KEY `merchant_credentials_accesstoken_unique` (`accessToken`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders_test`
--
ALTER TABLE `orders_test`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requested_data`
--
ALTER TABLE `requested_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `requested_data_shop_unique` (`shop`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sessions_session_id_unique` (`session_id`);

--
-- Indexes for table `shopify_transaction_log`
--
ALTER TABLE `shopify_transaction_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `merchant_credentials`
--
ALTER TABLE `merchant_credentials`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders_test`
--
ALTER TABLE `orders_test`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `requested_data`
--
ALTER TABLE `requested_data`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `shopify_transaction_log`
--
ALTER TABLE `shopify_transaction_log`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
