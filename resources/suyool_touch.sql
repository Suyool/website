-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 22, 2023 at 09:23 AM
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(12, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"315d0b42-4d43-45ab-84cd-fd66f2fd9a2b\",\"category\":\"MTC\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-08 12:33:49'),
(13, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"4a1028af-da17-456b-916d-7eb3ec525ef6\",\"category\":\"MTC\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 11:11:43'),
(14, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"9f0c86be-340d-4540-9f47-f9fa75d6d7e4\",\"category\":\"MTC\",\"type\":\"31\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 11:11:53'),
(15, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"f02c4403-2ade-4ca3-b1e1-bb2d255bc922\",\"category\":\"MTC\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 11:12:02'),
(16, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"915f654e-df1c-4d8e-8762-14908be1b761\",\"category\":\"MTC\",\"type\":\"10\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 11:12:06'),
(17, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"649833b5-ae75-4d0d-b6b1-15257deb67b4\",\"category\":\"MTC\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 11:12:14'),
(18, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"7f7f7690-0148-4cb0-9ff0-24a8ddd2ffb7\",\"category\":\"MTC\",\"type\":\"31\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 11:12:18'),
(19, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"9829708a-cb1d-4121-ba9b-c804dcad50e9\",\"category\":\"MTC\",\"type\":\"29\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 11:15:28'),
(20, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"d53871b2-ce0c-456c-977f-09f665866bc3\",\"category\":\"MTC\",\"type\":\"10\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 11:15:32'),
(21, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"5207b785-36c3-4849-8b83-569537a131c5\",\"category\":\"MTC\",\"type\":\"10\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 11:15:45'),
(22, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"b1152916-0011-4958-8bea-caaf5d99d619\",\"category\":\"MTC\",\"type\":\"30\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 11:15:56'),
(23, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"1557e7e3-021a-44dd-ad36-d529e1a8f176\",\"category\":\"MTC\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 11:15:59'),
(24, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"166200d5-bf5d-467d-86c9-4296210f1022\",\"category\":\"MTC\",\"type\":\"29\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 11:21:43'),
(25, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"c70a54bc-167c-41ca-a0b9-9db623d270fb\",\"category\":\"MTC\",\"type\":\"29\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 15:27:16'),
(26, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"1f2a65fd-e980-40f3-931b-76f95a88085f\",\"category\":\"MTC\",\"type\":\"29\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 15:28:41'),
(27, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"3e55b699-81b7-4904-b8cc-286c86794746\",\"category\":\"MTC\",\"type\":\"29\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-11 12:49:04'),
(28, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"67f857b9-05a8-49c4-9196-5aa3fa60a857\",\"category\":\"MTC\",\"type\":\"60\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-11 12:52:53'),
(29, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"6ae91263-816d-40d1-85f8-bd0a739e5b8c\",\"category\":\"MTC\",\"type\":\"31\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-11 13:54:11'),
(30, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"522b402d-c868-4164-8b58-3331477f27e9\",\"category\":\"MTC\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-11 13:54:16'),
(31, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"fb66a397-157e-4e84-80c4-0f18b0881915\",\"category\":\"MTC\",\"type\":\"10\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-11 13:54:22'),
(32, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"8a5eec35-1505-4635-a62e-bc535a4fcc37\",\"category\":\"MTC\",\"type\":\"29\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-11 13:54:27'),
(33, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"91f84a3d-8ec6-4b6d-b24e-b07b6c05e776\",\"category\":\"MTC\",\"type\":\"29\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-11 13:56:10'),
(34, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"4b695d57-05cf-47b0-ac0e-7f5bd9819950\",\"category\":\"MTC\",\"type\":\"30\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-11 13:56:14'),
(35, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"9ee78cab-1b5a-4ddb-b3b3-1cc191926cec\",\"category\":\"MTC\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-11 14:01:14'),
(36, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"8c9d1fc1-8e64-41c0-b668-94caa89cfc4a\",\"category\":\"MTC\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-21 17:07:31'),
(37, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"437e369e-9d08-4729-9e71-c1be866432d4\",\"category\":\"MTC\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-21 17:09:50'),
(38, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"11c5fdd4-8865-4403-8d4c-8996aa545dbe\",\"category\":\"MTC\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-21 17:11:03');

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
  `errorInfo` text DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `update_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `suyoolUserId`, `transId`, `postpaid_id`, `prepaid_id`, `status`, `amount`, `currency`, `errorInfo`, `create_date`, `update_date`) VALUES
(1, 155, NULL, NULL, NULL, 'canceled', 134000, 'LBP', 'HTTP/1.1 404 Not Found returned for \"http://10.20.80.62/SuyoolGlobalAPIs/api/Utilities/PushUtilityPayments\".', '2023-08-21 16:10:27', '2023-08-21 16:10:29'),
(2, 155, 10168, 1, NULL, 'completed', 134000, 'LBP', NULL, '2023-08-21 16:11:07', '2023-08-21 16:11:16'),
(3, 155, 10169, 2, NULL, 'canceled', 134000, 'LBP', 'Response body is empty.', '2023-08-21 16:12:10', '2023-08-21 16:12:13'),
(4, 155, 10170, 3, NULL, 'canceled', 134000, 'LBP', 'Response body is empty.', '2023-08-21 16:17:01', '2023-08-21 16:17:06'),
(5, 155, 10171, 4, NULL, 'completed', 134000, 'LBP', NULL, '2023-08-21 16:22:18', '2023-08-21 16:22:23'),
(6, 155, 10172, 5, NULL, 'canceled', 134000, 'LBP', 'Response body is empty.', '2023-08-21 16:23:35', '2023-08-21 16:23:41'),
(7, 155, NULL, NULL, NULL, 'canceled', 134000, 'LBP', 'HTTP/1.1 404 Not Found returned for \"http://10.20.80.62/SuyoolGlobalAPIs/api/Utilities/PushUtilityPayments\".', '2023-08-21 16:25:03', '2023-08-21 16:25:03'),
(8, 89, NULL, NULL, NULL, 'canceled', 360810, 'LBP', 'HTTP/1.1 404 Not Found returned for \"http://10.20.80.62/SuyoolGlobalAPIs/api/Utilities/PushUtilityPayments\".', '2023-08-21 17:01:52', '2023-08-21 17:01:54'),
(9, 89, NULL, NULL, NULL, 'canceled', 360810, 'LBP', 'HTTP/1.1 404 Not Found returned for \"http://10.20.80.62/SuyoolGlobalAPIs/api/Utilities/PushUtilityPayments\".', '2023-08-21 17:03:20', '2023-08-21 17:03:20'),
(10, 89, NULL, NULL, NULL, 'canceled', 1171350000, 'LBP', '53', '2023-08-21 17:06:08', '2023-08-21 17:06:09'),
(11, 89, NULL, NULL, NULL, 'canceled', 1171350000, 'LBP', '53', '2023-08-21 17:06:23', '2023-08-21 17:06:24'),
(12, 89, NULL, NULL, NULL, 'canceled', 11713500, 'LBP', '{\"Title\":\"Insufficient Funds\",\"SubTitle\":\"Top up your wallet with L.L7,586,351 to proceed with this transaction.\",\"ButtonOne\":{\"Text\":\"Top up\",\"Flag\":90,\"AdditionalInfo\":null,\"isShow\":true},\"ButtonTwo\":{\"Text\":\"Cancel\",\"Flag\":0,\"AdditionalInfo\":null,\"isShow\":true}}', '2023-08-21 17:06:39', '2023-08-21 17:06:39'),
(13, 89, 10194, NULL, NULL, 'canceled', 11713, 'LBP', '{reversed There are no vouchers of this type currently availalble.}', '2023-08-21 17:07:28', '2023-08-21 17:07:31'),
(14, 89, 10195, NULL, NULL, 'canceled', 117135, 'LBP', '{reversed There are no vouchers of this type currently availalble.}', '2023-08-21 17:09:46', '2023-08-21 17:09:50'),
(15, 155, 10197, NULL, NULL, 'canceled', 117135, 'LBP', '{reversed There are no vouchers of this type currently availalble.}', '2023-08-21 17:11:00', '2023-08-21 17:11:03');

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
  `additionalFees` double DEFAULT NULL,
  `displayedFees` int(50) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `amount1` double DEFAULT NULL,
  `amount2` double DEFAULT NULL,
  `referenceNumber` text DEFAULT NULL,
  `informativeOriginalWSAmount` double DEFAULT NULL,
  `totalAmount` double DEFAULT NULL,
  `rounding` int(11) DEFAULT NULL,
  `invoiceNumber` varchar(255) DEFAULT NULL,
  `paymentId` varchar(255) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `update_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `postpaid`
--

INSERT INTO `postpaid` (`id`, `suyoolUserId`, `gsmNumber`, `token`, `transactionDescription`, `transactionReference`, `error`, `pin`, `transactionId`, `currency`, `fees`, `fees1`, `additionalFees`, `displayedFees`, `amount`, `amount1`, `amount2`, `referenceNumber`, `informativeOriginalWSAmount`, `totalAmount`, `rounding`, `invoiceNumber`, `paymentId`, `create_date`, `update_date`) VALUES
(1, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', '20230800000387', '20230800000387', 'Transaction Paid successfully.', 1234, 1735859, 'LBP', 1300, 0, 0, NULL, 132750, 0, 0, '20230800000387', 88.5, 134000, -50, '201936524785', '12', '2023-08-21 16:11:15', '2023-08-21 16:11:15'),
(2, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', '20230800000388', '20230800000388', 'Transaction Paid successfully.', 1234, 1735860, 'LBP', 1300, 0, 0, NULL, 132750, 0, 0, '20230800000388', 88.5, 134000, -50, '201936524785', '12', '2023-08-21 16:12:13', '2023-08-21 16:12:13'),
(3, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', '20230800000389', '20230800000389', 'Transaction Paid successfully.', 1234, 1735861, 'LBP', 1300, 0, 0, NULL, 132750, 0, 0, '20230800000389', 88.5, 134000, -50, '201936524785', '12', '2023-08-21 16:17:06', '2023-08-21 16:17:06'),
(4, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', '20230800000390', '20230800000390', 'Transaction Paid successfully.', 1234, 1735862, 'LBP', 1300, 0, 0, NULL, 132750, 0, 0, '20230800000390', 88.5, 134000, -50, '201936524785', '12', '2023-08-21 16:22:23', '2023-08-21 16:22:23'),
(5, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', '20230800000391', '20230800000391', 'Transaction Paid successfully.', 1234, 1735863, 'LBP', 1300, 0, 0, NULL, 132750, 0, 0, '20230800000391', 88.5, 134000, -50, '201936524785', '12', '2023-08-21 16:23:41', '2023-08-21 16:23:41');

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
  `additionalFees` double DEFAULT NULL,
  `displayedFees` int(50) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `amount1` double DEFAULT NULL,
  `amount2` double DEFAULT NULL,
  `referenceNumber` text DEFAULT NULL,
  `informativeOriginalWSAmount` double DEFAULT NULL,
  `totalAmount` double DEFAULT NULL,
  `rounding` int(11) DEFAULT NULL,
  `invoiceNumber` varchar(255) DEFAULT NULL,
  `paymentId` varchar(255) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `update_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `postpaidrequest`
--

INSERT INTO `postpaidrequest` (`id`, `suyoolUserId`, `gsmNumber`, `token`, `error`, `s2error`, `requestId`, `pin`, `transactionId`, `currency`, `fees`, `fees1`, `additionalFees`, `displayedFees`, `amount`, `amount1`, `amount2`, `referenceNumber`, `informativeOriginalWSAmount`, `totalAmount`, `rounding`, `invoiceNumber`, `paymentId`, `create_date`, `update_date`) VALUES
(1, 89, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735857, 'LBP', 1300, 0, 0, NULL, 132750, 0, 0, '20230800000385', 88.5, 134000, -50, '201936524785', '12', '2023-08-21 16:04:43', '2023-08-21 16:06:39'),
(2, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735858, 'LBP', 1300, 0, 0, NULL, 132750, 0, 0, '20230800000386', 88.5, 134000, -50, '201936524785', '12', '2023-08-21 16:08:26', '2023-08-21 16:10:22'),
(3, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735859, 'LBP', 1300, 0, 0, NULL, 132750, 0, 0, '20230800000387', 88.5, 134000, -50, '201936524785', '12', '2023-08-21 16:11:01', '2023-08-21 16:11:06'),
(4, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735860, 'LBP', 1300, 0, 0, NULL, 132750, 0, 0, '20230800000388', 88.5, 134000, -50, '201936524785', '12', '2023-08-21 16:12:02', '2023-08-21 16:12:07'),
(5, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735861, 'LBP', 1300, 0, 0, NULL, 132750, 0, 0, '20230800000389', 88.5, 134000, -50, '201936524785', '12', '2023-08-21 16:16:54', '2023-08-21 16:16:59'),
(6, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735862, 'LBP', 1300, 0, 0, NULL, 132750, 0, 0, '20230800000390', 88.5, 134000, -50, '201936524785', '12', '2023-08-21 16:22:11', '2023-08-21 16:22:16'),
(7, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735863, 'LBP', 1300, 0, 0, NULL, 132750, 0, 0, '20230800000391', 88.5, 134000, -50, '201936524785', '12', '2023-08-21 16:23:15', '2023-08-21 16:23:20'),
(8, 155, '03030405', 'token928374651RsRLJAPasrqlmv56nipoqw', 'Data received successfully.', 'Data received successfully.', NULL, 1234, 1735864, 'LBP', 1300, 0, 0, NULL, 132750, 0, 0, '20230800000392', 88.5, 134000, -50, '201936524785', '12', '2023-08-21 16:24:58', '2023-08-21 16:25:01'),
(9, 155, '04270008', '', 'Mobile Number not found.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-21 16:27:01', '2023-08-21 16:27:01'),
(10, 155, '04270008', '', 'Mobile Number not found.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-21 16:35:32', '2023-08-21 16:35:32'),
(11, 155, '04270008', '', 'Mobile Number not found.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-21 16:40:45', '2023-08-21 16:40:45');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `postpaid`
--
ALTER TABLE `postpaid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `postpaidrequest`
--
ALTER TABLE `postpaidrequest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `prepaid`
--
ALTER TABLE `prepaid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
