-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 08, 2023 at 02:31 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `suyool_touch`
--

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `request` text NOT NULL,
  `response` text NOT NULL,
  `error` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `identifier`, `url`, `request`, `response`, `error`, `created`) VALUES
(1, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"283521c4-8862-4cd4-aa7d-f4577b899221\",\"category\":\"MTC\",\"type\":\"10\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-01 14:41:35'),
(2, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"b7cf2f47-0b5c-4ffa-8e86-b0779c55c529\",\"category\":\"MTC\",\"type\":\"10\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-03 11:56:29'),
(3, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"439fcafd-67b8-4de6-9ef7-b5429eed0c67\",\"category\":\"MTC\",\"type\":\"10\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-03 11:57:23'),
(4, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"a0e02499-b249-4c60-9533-c7d5677a8507\",\"category\":\"MTC\",\"type\":\"10\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-03 11:58:21'),
(5, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"b828d2e2-f1a6-4070-baa6-caa9e526e83b\",\"category\":\"MTC\",\"type\":\"10\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-03 11:58:45'),
(6, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"9dc22767-1429-4c70-b0eb-9cfe54088d77\",\"category\":\"MTC\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-08 10:58:14'),
(7, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"a7f3aaa6-2463-4dfc-8bbd-9b6138911987\",\"category\":\"MTC\",\"type\":\"10\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-08 11:40:21'),
(8, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"894f879d-2331-4bbf-b966-7c335143752c\",\"category\":\"MTC\",\"type\":\"29\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-08 11:40:26'),
(9, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"1c06104e-778b-4444-b27c-fbf8666d8833\",\"category\":\"MTC\",\"type\":\"30\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-08 11:40:31'),
(10, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"0564e8af-2488-4106-a101-7c2c5a9e4d77\",\"category\":\"MTC\",\"type\":\"60\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-08 11:40:35'),
(11, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"96c0c596-8846-44d0-ad19-363432c4a973\",\"category\":\"MTC\",\"type\":\"31\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-08 11:40:44'),
(12, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"315d0b42-4d43-45ab-84cd-fd66f2fd9a2b\",\"category\":\"MTC\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-08 12:33:49');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `suyoolUserId` int(11) NOT NULL,
  `transId` int(11) DEFAULT NULL,
  `postpaid_id` int(11) DEFAULT NULL,
  `prepaid_id` int(11) DEFAULT NULL,
  `status` varchar(30) NOT NULL,
  `amount` int(50) DEFAULT NULL,
  `currency` varchar(11) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `update_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `suyoolUserId`, `transId`, `postpaid_id`, `prepaid_id`, `status`, `amount`, `currency`, `create_date`, `update_date`) VALUES
(2000, 89, NULL, NULL, NULL, 'pending', 428355, 'LBP', '2023-07-31 14:27:36', '2023-07-31 14:27:36'),
(2001, 89, 4956, NULL, NULL, 'completed', 428355, 'LBP', '2023-07-31 14:28:06', '2023-07-31 14:28:06'),
(2002, 89, 5065, NULL, NULL, 'held', 360810, 'LBP', '2023-08-01 14:39:14', '2023-08-01 14:39:14'),
(2003, 89, 5066, NULL, NULL, 'completed', 360810, 'LBP', '2023-08-01 14:41:31', '2023-08-01 14:41:35'),
(2004, 89, NULL, NULL, NULL, 'canceled', 134000, 'LBP', '2023-08-02 11:53:53', '2023-08-02 11:53:56'),
(2005, 155, 5147, 1, NULL, 'completed', 134000, 'LBP', '2023-08-02 11:59:17', '2023-08-02 11:59:23'),
(2006, 155, 5155, 2, NULL, 'completed', 134000, 'LBP', '2023-08-02 12:09:34', '2023-08-02 12:09:40'),
(2007, 155, NULL, NULL, NULL, 'canceled', 134000, 'LBP', '2023-08-02 12:14:33', '2023-08-02 12:14:33'),
(2008, 155, NULL, NULL, NULL, 'canceled', 134000, 'LBP', '2023-08-02 12:23:52', '2023-08-02 12:23:53'),
(2009, 155, NULL, NULL, NULL, 'canceled', 134000, 'LBP', '2023-08-02 12:25:48', '2023-08-02 12:25:48'),
(2010, 155, NULL, NULL, NULL, 'canceled', 134000, 'LBP', '2023-08-02 12:27:13', '2023-08-02 12:27:13'),
(2011, 155, 5167, 3, NULL, 'completed', 134000, 'LBP', '2023-08-02 12:28:46', '2023-08-02 12:29:13'),
(2012, 89, NULL, NULL, NULL, 'canceled', 134000, 'LBP', '2023-08-03 09:04:44', '2023-08-03 09:04:47'),
(2013, 89, NULL, NULL, NULL, 'canceled', 134000, 'LBP', '2023-08-03 09:05:46', '2023-08-03 09:05:46'),
(2014, 89, NULL, NULL, NULL, 'canceled', 134000, 'LBP', '2023-08-03 09:06:09', '2023-08-03 09:06:09'),
(2015, 155, NULL, NULL, NULL, 'canceled', 134000, 'LBP', '2023-08-03 09:08:43', '2023-08-03 09:08:43'),
(2016, 155, NULL, NULL, NULL, 'pending', 134000, 'LBP', '2023-08-03 09:10:04', '2023-08-03 09:10:04'),
(2017, 155, NULL, NULL, NULL, 'held', 134000, 'LBP', '2023-08-03 09:13:53', '2023-08-03 09:13:53'),
(2018, 155, NULL, NULL, NULL, 'canceled', 134000, 'LBP', '2023-08-03 10:09:41', '2023-08-03 10:09:44'),
(2019, 155, NULL, NULL, NULL, 'pending', 134000, 'LBP', '2023-08-03 10:10:16', '2023-08-03 10:10:16'),
(2020, 155, NULL, NULL, NULL, 'pending', 134000, 'LBP', '2023-08-03 10:10:42', '2023-08-03 10:10:42'),
(2021, 89, NULL, NULL, NULL, 'pending', 134000, 'LBP', '2023-08-03 10:11:11', '2023-08-03 10:11:11'),
(2022, 89, NULL, NULL, NULL, 'canceled', 134000, 'LBP', '2023-08-03 11:18:03', '2023-08-03 11:18:05'),
(2023, 89, 5288, NULL, NULL, 'held', 134000, 'LBP', '2023-08-03 11:39:09', '2023-08-03 11:39:09'),
(2024, 89, 5289, 4, NULL, 'completed', 134000, 'LBP', '2023-08-03 11:41:30', '2023-08-03 11:41:35'),
(2025, 89, 5295, NULL, NULL, 'held', 360810, 'LBP', '2023-08-03 11:56:26', '2023-08-03 11:56:26'),
(2026, 89, 5296, NULL, NULL, 'held', 360810, 'LBP', '2023-08-03 11:57:19', '2023-08-03 11:57:19'),
(2027, 89, 5297, NULL, NULL, 'held', 360810, 'LBP', '2023-08-03 11:58:18', '2023-08-03 11:58:18'),
(2028, 89, 5298, NULL, NULL, 'held', 360810, 'LBP', '2023-08-03 11:58:42', '2023-08-03 11:58:42'),
(2029, 89, 5303, 5, NULL, 'completed', 134000, 'LBP', '2023-08-03 12:21:01', '2023-08-03 12:21:06'),
(2030, 89, 5409, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-07 09:31:21', '2023-08-07 09:31:48'),
(2031, 89, 5410, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-07 09:33:24', '2023-08-07 09:33:25'),
(2032, 89, 5411, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-07 09:33:52', '2023-08-07 09:33:58'),
(2033, 89, 5412, NULL, NULL, 'held', 134000, 'LBP', '2023-08-07 09:34:49', '2023-08-07 09:34:49'),
(2034, 89, 5413, NULL, NULL, 'held', 134000, 'LBP', '2023-08-07 09:35:52', '2023-08-07 09:35:52'),
(2035, 89, 5430, 6, NULL, 'completed', 134000, 'LBP', '2023-08-07 10:28:19', '2023-08-07 10:28:26'),
(2036, 89, 5431, NULL, NULL, 'held', 134000, 'LBP', '2023-08-07 10:30:53', '2023-08-07 10:30:53'),
(2037, 89, NULL, NULL, NULL, 'canceled', 134000, 'LBP', '2023-08-07 14:46:08', '2023-08-07 14:46:08'),
(2038, 89, NULL, NULL, NULL, 'canceled', 117135, 'LBP', '2023-08-08 10:10:03', '2023-08-08 10:10:03'),
(2039, 89, NULL, NULL, NULL, 'canceled', 117135, 'LBP', '2023-08-08 10:10:04', '2023-08-08 10:10:04'),
(2040, 89, NULL, NULL, NULL, 'canceled', 134000, 'LBP', '2023-08-08 10:13:55', '2023-08-08 10:13:55'),
(2041, 89, 5605, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:18:54', '2023-08-08 10:19:10'),
(2042, 89, 5606, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:18:57', '2023-08-08 10:19:09'),
(2043, 89, 5607, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:18:58', '2023-08-08 10:19:08'),
(2044, 89, 5608, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:18:59', '2023-08-08 10:19:08'),
(2045, 89, 5609, 7, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:19:00', '2023-08-08 10:19:09'),
(2046, 89, 5610, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:19:02', '2023-08-08 10:19:08'),
(2047, 89, 5612, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:19:08', '2023-08-08 10:19:11'),
(2048, 89, 5611, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:19:08', '2023-08-08 10:19:09'),
(2049, 89, 5613, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:19:08', '2023-08-08 10:19:09'),
(2050, 89, 5614, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:19:09', '2023-08-08 10:19:10'),
(2051, 89, 5615, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:19:09', '2023-08-08 10:19:11'),
(2052, 89, 5616, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:19:10', '2023-08-08 10:19:11'),
(2053, 89, 5617, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:19:10', '2023-08-08 10:19:11'),
(2054, 89, 5618, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:19:11', '2023-08-08 10:19:12'),
(2055, 89, 5619, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:19:11', '2023-08-08 10:19:12'),
(2056, 89, 5620, NULL, NULL, 'completed', 134000, 'LBP', '2023-08-08 10:19:11', '2023-08-08 10:19:12'),
(2057, 89, 5646, NULL, NULL, 'held', 117135, 'LBP', '2023-08-08 10:58:10', '2023-08-08 10:58:10'),
(2058, 89, 5654, 8, NULL, 'completed', 134000, 'LBP', '2023-08-08 11:27:55', '2023-08-08 11:28:01'),
(2059, 89, 5656, 9, NULL, 'completed', 134000, 'LBP', '2023-08-08 11:34:54', '2023-08-08 11:34:59'),
(2060, 89, NULL, NULL, NULL, 'canceled', 117135, 'LBP', '2023-08-08 11:40:00', '2023-08-08 11:40:00'),
(2061, 89, 5657, NULL, NULL, 'held', 360810, 'LBP', '2023-08-08 11:40:18', '2023-08-08 11:40:18'),
(2062, 89, 5658, NULL, NULL, 'held', 428355, 'LBP', '2023-08-08 11:40:23', '2023-08-08 11:40:23'),
(2063, 89, 5659, NULL, NULL, 'held', 720765, 'LBP', '2023-08-08 11:40:27', '2023-08-08 11:40:27'),
(2064, 89, 5660, NULL, NULL, 'held', 1438965, 'LBP', '2023-08-08 11:40:32', '2023-08-08 11:40:32'),
(2065, 89, NULL, NULL, NULL, 'canceled', 2158875, 'LBP', '2023-08-08 11:40:37', '2023-08-08 11:40:37'),
(2066, 89, 5662, NULL, NULL, 'held', 428355, 'LBP', '2023-08-08 11:40:41', '2023-08-08 11:40:41'),
(2067, 89, 5670, NULL, NULL, 'held', 117135, 'LBP', '2023-08-08 12:33:46', '2023-08-08 12:33:46');

-- --------------------------------------------------------

--
-- Table structure for table `postpaid`
--

CREATE TABLE `postpaid` (
  `id` int(11) NOT NULL,
  `suyoolUserId` int(11) NOT NULL,
  `gsmNumber` varchar(30) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `transactionDescription` varchar(255) DEFAULT NULL,
  `transactionReference` varchar(255) DEFAULT NULL,
  `error` varchar(255) NOT NULL,
  `pin` int(11) DEFAULT NULL,
  `transactionId` int(11) DEFAULT NULL,
  `currency` varchar(20) DEFAULT NULL,
  `fees` double DEFAULT NULL,
  `fees1` double DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `amount1` double DEFAULT NULL,
  `amount2` double DEFAULT NULL,
  `referenceNumber` text DEFAULT NULL,
  `informativeOriginalWSAmount` double DEFAULT NULL,
  `totalAmount` double DEFAULT NULL,
  `rounding` int(11) DEFAULT NULL,
  `additionalFees` double DEFAULT NULL,
  `invoiceNumber` varchar(255) DEFAULT NULL,
  `paymentId` varchar(255) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `update_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `postpaid`
--

INSERT INTO `postpaid` (`id`, `suyoolUserId`, `gsmNumber`, `token`, `transactionDescription`, `transactionReference`, `error`, `pin`, `transactionId`, `currency`, `fees`, `fees1`, `amount`, `amount1`, `amount2`, `referenceNumber`, `informativeOriginalWSAmount`, `totalAmount`, `rounding`, `additionalFees`, `invoiceNumber`, `paymentId`, `create_date`, `update_date`) VALUES
(1, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', NULL, NULL, 'Transaction Paid successfully.', 1234, 1735464, 'LBP', 1300, 0, 132750, 0, 0, '20230800000024', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-02 11:59:23', '2023-08-02 11:59:23'),
(2, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', NULL, NULL, 'Transaction Paid successfully.', 1234, 1735465, 'LBP', 1300, 0, 132750, 0, 0, '20230800000025', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-02 12:09:40', '2023-08-02 12:09:40'),
(3, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', NULL, NULL, 'Transaction Paid successfully.', 1234, 1735469, 'LBP', 1300, 0, 132750, 0, 0, '20230800000029', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-02 12:29:13', '2023-08-02 12:29:13'),
(4, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', '20230800000039', '20230800000039', 'Transaction Paid successfully.', 1234, 1735479, 'LBP', 1300, 0, 132750, 0, 0, '20230800000039', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-03 11:41:35', '2023-08-03 11:41:35'),
(5, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', '20230800000041', '20230800000041', 'Transaction Paid successfully.', 1234, 1735481, 'LBP', 1300, 0, 132750, 0, 0, '20230800000041', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-03 12:21:06', '2023-08-03 12:21:06'),
(6, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', '20230800000066', '20230800000066', 'Transaction Paid successfully.', 1234, 1735498, 'LBP', 1300, 0, 132750, 0, 0, '20230800000066', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-07 10:28:26', '2023-08-07 10:28:26'),
(7, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', '20230800000093', '20230800000093', 'Transaction Paid successfully.', 1234, 1735533, 'LBP', 1300, 0, 132750, 0, 0, '20230800000093', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-08 10:19:08', '2023-08-08 10:19:08'),
(8, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', '20230800000112', '20230800000112', 'Transaction Paid successfully.', 1234, 1735553, 'LBP', 1300, 0, 132750, 0, 0, '20230800000112', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-08 11:28:01', '2023-08-08 11:28:01'),
(9, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', '20230800000119', '20230800000119', 'Transaction Paid successfully.', 1234, 1735560, 'LBP', 1300, 0, 132750, 0, 0, '20230800000119', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-08 11:34:59', '2023-08-08 11:34:59');

-- --------------------------------------------------------

--
-- Table structure for table `postpaidrequest`
--

CREATE TABLE `postpaidrequest` (
  `id` int(11) NOT NULL,
  `suyoolUserId` int(11) NOT NULL,
  `gsmNumber` varchar(30) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `error` varchar(255) NOT NULL,
  `s2error` varchar(255) DEFAULT NULL,
  `requestId` varchar(255) DEFAULT NULL,
  `pin` int(11) DEFAULT NULL,
  `transactionId` int(11) DEFAULT NULL,
  `currency` varchar(20) DEFAULT NULL,
  `fees` double DEFAULT NULL,
  `fees1` double DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `amount1` double DEFAULT NULL,
  `amount2` double DEFAULT NULL,
  `referenceNumber` text DEFAULT NULL,
  `informativeOriginalWSAmount` double DEFAULT NULL,
  `totalAmount` double DEFAULT NULL,
  `rounding` int(11) DEFAULT NULL,
  `additionalFees` double DEFAULT NULL,
  `invoiceNumber` varchar(255) DEFAULT NULL,
  `paymentId` varchar(255) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `update_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `postpaidrequest`
--

INSERT INTO `postpaidrequest` (`id`, `suyoolUserId`, `gsmNumber`, `token`, `error`, `s2error`, `requestId`, `pin`, `transactionId`, `currency`, `fees`, `fees1`, `amount`, `amount1`, `amount2`, `referenceNumber`, `informativeOriginalWSAmount`, `totalAmount`, `rounding`, `additionalFees`, `invoiceNumber`, `paymentId`, `create_date`, `update_date`) VALUES
(1, 89, '3030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-02 10:15:25', '2023-08-02 10:15:25'),
(2, 89, '3030405', '', 'Mobile Number not found.', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-02 10:15:47', '2023-08-02 10:15:47'),
(3, 89, '03030405', '', 'Mobile Number not found.', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-02 10:19:14', '2023-08-02 10:19:14'),
(4, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-02 10:23:05', '2023-08-02 10:23:05'),
(5, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', '7f89aaaf-ef40-411f-8936-feae66947a87', 1234, 1735460, 'LBP', 1300, 0, 132750, 0, 0, '20230800000020', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-02 10:23:19', '2023-08-02 11:10:23'),
(6, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735461, 'LBP', 1300, 0, 132750, 0, 0, '20230800000021', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-02 11:12:24', '2023-08-02 11:12:30'),
(7, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735463, 'LBP', 1300, 0, 132750, 0, 0, '20230800000023', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-02 11:46:22', '2023-08-02 11:46:27'),
(8, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735464, 'LBP', 1300, 0, 132750, 0, 0, '20230800000024', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-02 11:59:09', '2023-08-02 11:59:14'),
(9, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735465, 'LBP', 1300, 0, 132750, 0, 0, '20230800000025', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-02 12:09:28', '2023-08-02 12:09:33'),
(10, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735466, 'LBP', 1300, 0, 132750, 0, 0, '20230800000026', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-02 12:23:45', '2023-08-02 12:23:50'),
(11, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735467, 'LBP', 1300, 0, 132750, 0, 0, '20230800000027', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-02 12:25:39', '2023-08-02 12:25:44'),
(12, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735469, 'LBP', 1300, 0, 132750, 0, 0, '20230800000029', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-02 12:27:05', '2023-08-02 12:28:41'),
(13, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735470, 'LBP', 1300, 0, 132750, 0, 0, '20230800000030', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-02 13:51:47', '2023-08-02 13:53:04'),
(14, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735471, 'LBP', 1300, 0, 132750, 0, 0, '20230800000031', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-03 08:59:07', '2023-08-03 08:59:12'),
(15, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735472, 'LBP', 1300, 0, 132750, 0, 0, '20230800000032', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-03 09:04:36', '2023-08-03 09:04:41'),
(16, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735473, 'LBP', 1300, 0, 132750, 0, 0, '20230800000033', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-03 09:06:02', '2023-08-03 09:06:07'),
(17, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735474, 'LBP', 1300, 0, 132750, 0, 0, '20230800000034', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-03 09:08:36', '2023-08-03 09:08:41'),
(18, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735475, 'LBP', 1300, 0, 132750, 0, 0, '20230800000035', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-03 10:09:35', '2023-08-03 10:09:39'),
(19, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735476, 'LBP', 1300, 0, 132750, 0, 0, '20230800000036', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-03 11:17:57', '2023-08-03 11:18:01'),
(20, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735477, 'LBP', 1300, 0, 132750, 0, 0, '20230800000037', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-03 11:39:01', '2023-08-03 11:39:08'),
(21, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735478, 'LBP', 1300, 0, 132750, 0, 0, '20230800000038', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-03 11:40:53', '2023-08-03 11:40:57'),
(22, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735479, 'LBP', 1300, 0, 132750, 0, 0, '20230800000039', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-03 11:41:24', '2023-08-03 11:41:27'),
(23, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735481, 'LBP', 1300, 0, 132750, 0, 0, '20230800000041', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-03 12:20:49', '2023-08-03 12:20:58'),
(24, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735493, 'LBP', 1300, 0, 132750, 0, 0, '20230800000061', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-07 09:31:07', '2023-08-07 09:31:17'),
(25, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735494, 'LBP', 1300, 0, 132750, 0, 0, '20230800000062', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-07 09:33:43', '2023-08-07 09:33:48'),
(26, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735495, 'LBP', 1300, 0, 132750, 0, 0, '20230800000063', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-07 09:35:43', '2023-08-07 09:35:49'),
(27, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735498, 'LBP', 1300, 0, 132750, 0, 0, '20230800000066', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-07 10:28:10', '2023-08-07 10:28:17'),
(28, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735499, 'LBP', 1300, 0, 132750, 0, 0, '20230800000067', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-07 10:30:46', '2023-08-07 10:30:51'),
(29, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735521, 'LBP', 1300, 0, 132750, 0, 0, '20230800000083', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-07 14:45:58', '2023-08-07 14:46:05'),
(30, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-07 15:00:52', '2023-08-07 15:00:52'),
(31, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-07 15:01:28', '2023-08-07 15:01:28'),
(32, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735523, 'LBP', 1300, 0, 132750, 0, 0, '20230800000085', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-07 15:01:36', '2023-08-07 15:01:41'),
(33, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-07 15:01:47', '2023-08-07 15:01:47'),
(34, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735525, 'LBP', 1300, 0, 132750, 0, 0, '20230800000087', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-07 15:12:07', '2023-08-07 15:12:12'),
(35, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 10:04:16', '2023-08-08 10:04:16'),
(36, 89, '70618832', '', 'Mobile Number not found.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 10:04:33', '2023-08-08 10:04:33'),
(37, 89, '70618832', '', 'Mobile Number not found.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 10:04:37', '2023-08-08 10:04:37'),
(38, 89, '70618832', '', 'Mobile Number not found.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 10:04:38', '2023-08-08 10:04:38'),
(39, 89, '70618832', '', 'Mobile Number not found.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 10:04:38', '2023-08-08 10:04:38'),
(40, 89, '70618832', '', 'Mobile Number not found.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 10:04:38', '2023-08-08 10:04:38'),
(41, 89, '70618832', '', 'Mobile Number not found.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 10:04:38', '2023-08-08 10:04:38'),
(42, 89, '70618832', '', 'Mobile Number not found.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 10:05:49', '2023-08-08 10:05:49'),
(43, 89, '70618832', '', 'Mobile Number not found.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 10:05:50', '2023-08-08 10:05:50'),
(44, 89, '70618832', '', 'Mobile Number not found.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 10:05:51', '2023-08-08 10:05:51'),
(45, 89, '70618832', '', 'Mobile Number not found.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 10:05:51', '2023-08-08 10:05:51'),
(46, 89, '70618832', '', 'Mobile Number not found.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 10:05:52', '2023-08-08 10:05:52'),
(47, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735531, 'LBP', 1300, 0, 132750, 0, 0, '20230800000091', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-08 10:12:53', '2023-08-08 10:13:19'),
(48, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735533, 'LBP', 1300, 0, 132750, 0, 0, '20230800000093', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-08 10:18:43', '2023-08-08 10:18:48'),
(49, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735540, 'LBP', 1300, 0, 132750, 0, 0, '20230800000099', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-08 10:43:46', '2023-08-08 10:43:50'),
(50, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735542, 'LBP', 1300, 0, 132750, 0, 0, '20230800000101', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-08 10:45:05', '2023-08-08 10:45:11'),
(51, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735543, 'LBP', 1300, 0, 132750, 0, 0, '20230800000102', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-08 10:45:45', '2023-08-08 10:45:49'),
(52, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 10:47:50', '2023-08-08 10:47:50'),
(53, 89, '03030545', '', 'Mobile Number not found.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 11:20:02', '2023-08-08 11:20:02'),
(54, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 11:20:10', '2023-08-08 11:20:10'),
(55, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 11:20:16', '2023-08-08 11:20:16'),
(56, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 11:20:24', '2023-08-08 11:20:24'),
(57, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 11:20:28', '2023-08-08 11:20:28'),
(58, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735553, 'LBP', 1300, 0, 132750, 0, 0, '20230800000112', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-08 11:27:46', '2023-08-08 11:27:52'),
(59, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', '1ec50f9d-a9c7-4ee9-bfdc-a90b558b4327', 1234, 1735560, 'LBP', 1300, 0, 132750, 0, 0, '20230800000119', 88.5, 134000, -50, 0, '201936524785', '12', '2023-08-08 11:33:40', '2023-08-08 11:34:09'),
(60, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-08 13:34:54', '2023-08-08 13:34:54');

-- --------------------------------------------------------

--
-- Table structure for table `prepaid`
--

CREATE TABLE `prepaid` (
  `id` int(11) NOT NULL,
  `suyoolUserId` int(11) NOT NULL,
  `voucherSerial` int(100) NOT NULL,
  `voucherCode` int(100) NOT NULL,
  `voucherExpiry` varchar(40) NOT NULL,
  `description` varchar(100) NOT NULL,
  `displayMessage` text NOT NULL,
  `token` varchar(100) NOT NULL,
  `balance` int(11) NOT NULL,
  `errorMsg` varchar(255) NOT NULL,
  `insertId` int(11) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `update_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `prepaid_id` (`prepaid_id`),
  ADD UNIQUE KEY `postpaid_id` (`postpaid_id`);

--
-- Indexes for table `postpaid`
--
ALTER TABLE `postpaid`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `postpaidrequest`
--
ALTER TABLE `postpaidrequest`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prepaid`
--
ALTER TABLE `prepaid`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2068;

--
-- AUTO_INCREMENT for table `postpaid`
--
ALTER TABLE `postpaid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `postpaidrequest`
--
ALTER TABLE `postpaidrequest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `prepaid`
--
ALTER TABLE `prepaid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
