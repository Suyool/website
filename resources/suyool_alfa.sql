-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 22, 2023 at 09:17 AM
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
-- Database: `suyool_alfa`
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
(1, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"334de788-6912-4484-835c-7efd48596640\",\"category\":\"ALFA\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-01 13:35:22'),
(2, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"a1066b81-af54-4aa3-bd33-0ea4c87777ab\",\"category\":\"ALFA\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":2,\"errormsg\":\"Token has expired. Please Login Again\"},\"insertId\":null}', 'Token has expired. Please Login Again', '2023-08-01 13:36:27'),
(3, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"a1066b81-af54-4aa3-bd33-0ea4c87777ab\",\"category\":\"ALFA\",\"type\":\"13\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":2,\"errormsg\":\"Token has expired. Please Login Again\"},\"insertId\":null}', 'Token has expired. Please Login Again', '2023-08-01 13:37:01'),
(4, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"c48db49a-4cae-4dc9-89e3-c6a251722bdf\",\"category\":\"ALFA\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-01 13:41:31'),
(5, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"5f45663e-f9a5-4b1b-a862-d7d278c8ae0f\",\"category\":\"ALFA\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-01 14:36:00'),
(6, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"9599ef59-faca-492d-a836-4034dd1c5a6e\",\"category\":\"ALFA\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-08 11:27:01'),
(7, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"1abae306-e767-4ce3-add6-a17b58c8648f\",\"category\":\"ALFA\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 10:48:24'),
(8, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"97562c0f-b844-4470-9e56-18f20dc03386\",\"category\":\"ALFA\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-09 11:21:45'),
(9, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"7229a82d-bf1d-40d9-9155-5b0f5111e15b\",\"category\":\"ALFA\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-11 13:32:59'),
(10, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"f39e701c-7a55-4886-8a92-da16fb7e1042\",\"category\":\"ALFA\",\"type\":\"32\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-11 13:36:13'),
(11, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"b004807f-3c3a-45f6-a688-02486bb339b9\",\"category\":\"ALFA\",\"type\":\"33\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-11 13:39:22'),
(12, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"ac88c54c-42ee-43b4-b284-86ac79c6a1b7\",\"category\":\"ALFA\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-11 13:55:22'),
(13, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"4722827b-2c20-472c-8385-f9da852177ee\",\"category\":\"ALFA\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-21 15:02:43'),
(14, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"d4da96f5-0d4b-478a-8f19-45fc62da8e9d\",\"category\":\"ALFA\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-21 15:08:12'),
(15, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"3f03dcc6-81f6-40ad-bbb7-0f5f97d8ac4d\",\"category\":\"ALFA\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-21 15:21:21'),
(16, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"95cb39a2-ce29-4b6f-ad7f-e5bd25ed7136\",\"category\":\"ALFA\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-21 15:23:31'),
(17, 'Prepaid Request', 'https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher', '{\"Token\":\"683341ea-4c69-4981-90ee-f204ef8fc63c\",\"category\":\"ALFA\",\"type\":\"1\"}', '{\"__type\":\"ServiceClasses.BaseReply\",\"errorinfo\":{\"errorcode\":19,\"errormsg\":\"There are no vouchers of this type currently availalble.\"},\"insertId\":null}', 'There are no vouchers of this type currently availalble.', '2023-08-21 15:29:58');

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
(1, 89, NULL, NULL, NULL, 'canceled', 158000, 'LBP', 'HTTP/1.1 404 Not Found returned for \"http://10.20.80.62/SuyoolGlobalAPIs/api/Utilities/PushUtilityPayments\".', '2023-08-21 14:18:39', '2023-08-21 14:18:39'),
(2, 89, NULL, NULL, NULL, 'canceled', 117135, 'LBP', 'HTTP/1.1 404 Not Found returned for \"http://10.20.80.62/SuyoolGlobalAPIs/api/Utilities/PushUtilityPayments\".', '2023-08-21 14:43:45', '2023-08-21 14:43:47'),
(3, 89, NULL, NULL, NULL, 'canceled', 117135, 'LBP', 'HTTP/1.1 404 Not Found returned for \"http://10.20.80.62/SuyoolGlobalAPIs/api/Utilities/PushUtilityPayments\".', '2023-08-21 14:44:16', '2023-08-21 14:44:16'),
(4, 89, 10148, NULL, 1, 'completed', 288990, 'LBP', NULL, '2023-08-21 15:01:27', '2023-08-21 15:01:29'),
(5, 89, 10149, NULL, NULL, 'canceled', 117135, 'LBP', 'There are no vouchers of this type currently availalble.', '2023-08-21 15:02:40', '2023-08-21 15:02:43'),
(6, 89, 10150, NULL, NULL, 'canceled', 117135, 'LBP', '{reversed There are no vouchers of this type currently availalble.}', '2023-08-21 15:08:09', '2023-08-21 15:08:12'),
(7, 89, 10152, NULL, NULL, 'canceled', 117135, 'LBP', '{reversed {\"Title\":\"There are no vouchers of this type currently availalble.\"}}', '2023-08-21 15:21:18', '2023-08-21 15:21:21'),
(8, 89, 10153, NULL, NULL, 'canceled', 117135, 'LBP', '{reversed There are no vouchers of this type currently availalble.}', '2023-08-21 15:23:28', '2023-08-21 15:23:31'),
(9, 89, 10154, NULL, NULL, 'canceled', 117135, 'LBP', '{reversed There are no vouchers of this type currently availalble.}', '2023-08-21 15:29:54', '2023-08-21 15:29:58'),
(10, 89, NULL, NULL, NULL, 'canceled', 1171350000, 'LBP', '53', '2023-08-21 15:31:29', '2023-08-21 15:31:29'),
(11, 89, NULL, NULL, NULL, 'canceled', 117135000, 'LBP', '{\"Title\":\"Insufficient Funds\",\"SubTitle\":\"Top up your wallet with L.L113,007,851 to proceed with this transaction.\",\"ButtonOne\":{\"Text\":\"Top up\",\"Flag\":90,\"AdditionalInfo\":null,\"isShow\":true},\"ButtonTwo\":{\"Text\":\"Cancel\",\"Flag\":0,\"AdditionalInfo\":null,\"isShow\":true}}', '2023-08-21 15:33:35', '2023-08-21 15:33:35'),
(12, 89, NULL, NULL, NULL, 'canceled', 117135000, 'LBP', '{\"Title\":\"Insufficient Funds\",\"SubTitle\":\"Top up your wallet with L.L113,007,851 to proceed with this transaction.\",\"ButtonOne\":{\"Text\":\"Top up\",\"Flag\":90,\"AdditionalInfo\":null,\"isShow\":true},\"ButtonTwo\":{\"Text\":\"Cancel\",\"Flag\":0,\"AdditionalInfo\":null,\"isShow\":true}}', '2023-08-21 15:33:57', '2023-08-21 15:33:57'),
(13, 89, NULL, NULL, NULL, 'canceled', 4127149, 'LBP', '{\"Title\":\"Insufficient Funds\",\"SubTitle\":\"Exchange from $ to L.L to proceed with this transaction.\",\"ButtonOne\":{\"Text\":\"Exchange\",\"Flag\":84,\"AdditionalInfo\":null,\"isShow\":true},\"ButtonTwo\":{\"Text\":\"Cancel\",\"Flag\":0,\"AdditionalInfo\":null,\"isShow\":true}}', '2023-08-21 15:34:49', '2023-08-21 15:34:49'),
(14, 89, NULL, NULL, NULL, 'canceled', 117135, 'LBP', 'HTTP/1.1 404 Not Found returned for \"http://10.20.80.62/SuyoolGlobalAPIs/api/Utilities/PushUtilityPayments\".', '2023-08-21 17:04:19', '2023-08-21 17:04:19');

-- --------------------------------------------------------

--
-- Table structure for table `postpaid`
--

CREATE TABLE `postpaid` (
  `id` int(11) NOT NULL,
  `suyoolUserId` int(11) NOT NULL,
  `gsmNumber` int(11) NOT NULL,
  `currency` varchar(20) NOT NULL,
  `pin` int(11) NOT NULL,
  `transactionId` int(11) NOT NULL,
  `transactionDescription` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `fees` int(11) NOT NULL,
  `fees1` int(11) NOT NULL,
  `additionalFees` int(11) NOT NULL,
  `displayedFees` int(50) NOT NULL,
  `amount` double NOT NULL,
  `amount1` double NOT NULL,
  `amount2` double NOT NULL,
  `referenceNumber` int(11) NOT NULL,
  `informativeOriginalWSAmount` double NOT NULL,
  `totalAmount` double NOT NULL,
  `rounding` int(11) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `update_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `postpaidrequest`
--

CREATE TABLE `postpaidrequest` (
  `id` int(11) NOT NULL,
  `suyoolUserId` int(11) NOT NULL,
  `gsmNumber` int(11) NOT NULL,
  `transactionId` int(11) DEFAULT NULL,
  `currency` varchar(20) DEFAULT NULL,
  `pin` int(11) DEFAULT NULL,
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
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `update_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `postpaidrequest`
--

INSERT INTO `postpaidrequest` (`id`, `suyoolUserId`, `gsmNumber`, `transactionId`, `currency`, `pin`, `fees`, `fees1`, `additionalFees`, `displayedFees`, `amount`, `amount1`, `amount2`, `referenceNumber`, `informativeOriginalWSAmount`, `totalAmount`, `rounding`, `create_date`, `update_date`) VALUES
(1, 572953132, 70102030, 1735848, 'LBP', 1234, 1300, 0, 0, NULL, 156870, 0, 0, '20230800000376', 104.58, 158000, -170, '2023-08-21 13:50:13', '2023-08-21 13:50:19'),
(2, 89, 70102030, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-21 13:51:24', '2023-08-21 13:51:24'),
(3, 89, 70102030, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-21 13:51:47', '2023-08-21 13:51:47'),
(4, 155, 70102030, 1735849, 'LBP', 1234, 1300, 0, 0, NULL, 156870, 0, 0, '20230800000377', 104.58, 158000, -170, '2023-08-21 13:52:01', '2023-08-21 13:56:52'),
(5, 89, 70102030, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-21 14:16:44', '2023-08-21 14:16:44'),
(6, 89, 70102030, 1735853, 'LBP', 1234, 1300, 0, 0, NULL, 156870, 0, 0, '20230800000381', 104.58, 158000, -170, '2023-08-21 14:18:31', '2023-08-21 14:18:35'),
(7, 89, 70102030, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-21 16:51:37', '2023-08-21 16:51:37');

-- --------------------------------------------------------

--
-- Table structure for table `prepaid`
--

CREATE TABLE `prepaid` (
  `id` int(11) NOT NULL,
  `suyoolUserId` int(11) NOT NULL,
  `voucherSerial` varchar(100) NOT NULL,
  `voucherCode` varchar(100) NOT NULL,
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
-- Dumping data for table `prepaid`
--

INSERT INTO `prepaid` (`id`, `suyoolUserId`, `voucherSerial`, `voucherCode`, `voucherExpiry`, `description`, `displayMessage`, `token`, `balance`, `errorMsg`, `insertId`, `create_date`, `update_date`) VALUES
(1, 89, '1234567890123456', '12345678901234', '2024-07-01', '3.38$ 13 Days', 'You have successfully purchased a \'3.38$ 13 Days\' Voucher code. Please recharge it using the code 12345678901234 before 2024-07-01', '9cc26441-7167-49ce-89aa-06cae354134d', 94805000, 'SUCCESS', NULL, '2023-08-21 15:01:29', '2023-08-21 15:01:29');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `postpaid`
--
ALTER TABLE `postpaid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `postpaidrequest`
--
ALTER TABLE `postpaidrequest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `prepaid`
--
ALTER TABLE `prepaid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
