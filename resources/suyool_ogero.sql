-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 22, 2023 at 09:18 AM
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
-- Database: `suyool_ogero`
--

-- --------------------------------------------------------

--
-- Table structure for table `landline`
--

CREATE TABLE `landline` (
  `id` int(100) NOT NULL,
  `suyoolUserId` int(100) NOT NULL,
  `gsmNumber` int(10) NOT NULL,
  `transactionId` int(50) NOT NULL,
  `transactionDescription` varchar(50) NOT NULL,
  `referenceNumber` varchar(50) NOT NULL,
  `ogeroBills` text NOT NULL,
  `ogeroPenalty` varchar(255) NOT NULL,
  `ogeroInitiationDate` varchar(50) NOT NULL,
  `ogeroClientName` varchar(255) NOT NULL,
  `ogeroAddress` varchar(255) NOT NULL,
  `currency` varchar(50) NOT NULL,
  `amount` varchar(50) NOT NULL,
  `amount1` varchar(50) NOT NULL,
  `amount2` varchar(50) NOT NULL,
  `totalAmount` varchar(50) NOT NULL,
  `ogeroTotalAmount` varchar(50) NOT NULL,
  `ogeroFees` varchar(50) NOT NULL,
  `additionalFees` varchar(50) NOT NULL,
  `fees` varchar(50) NOT NULL,
  `fees1` varchar(50) NOT NULL,
  `displayedFees` int(50) DEFAULT NULL,
  `rounding` varchar(50) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `landline`
--

INSERT INTO `landline` (`id`, `suyoolUserId`, `gsmNumber`, `transactionId`, `transactionDescription`, `referenceNumber`, `ogeroBills`, `ogeroPenalty`, `ogeroInitiationDate`, `ogeroClientName`, `ogeroAddress`, `currency`, `amount`, `amount1`, `amount2`, `totalAmount`, `ogeroTotalAmount`, `ogeroFees`, `additionalFees`, `fees`, `fees1`, `displayedFees`, `rounding`, `created`, `updated`) VALUES
(1, 155, 1123120, 1735835, '20230800000363', '20230800000363', '[{\"Year\":\"2019\",\"Amount\":\"50,000\",\"Month\":\"5\"}]', '', '15/08/1945', 'ملحم بركات', 'كفرشيما ش. نزار فرنسيس ب. ماجدة الرومي / ملكه', 'LBP', '52000', '50000', '0', '53000', '52000', '2000', '0', '1000', '0', NULL, '0', '2023-08-21 10:10:01', '2023-08-21 10:10:01');

-- --------------------------------------------------------

--
-- Table structure for table `landlinerequest`
--

CREATE TABLE `landlinerequest` (
  `id` int(100) NOT NULL,
  `suyoolUserId` int(100) NOT NULL,
  `gsmNumber` varchar(10) NOT NULL,
  `transactionId` int(50) NOT NULL,
  `ogeroBills` text NOT NULL,
  `ogeroPenalty` varchar(255) NOT NULL,
  `ogeroInitiationDate` varchar(50) NOT NULL,
  `ogeroClientName` varchar(255) NOT NULL,
  `ogeroAddress` varchar(255) NOT NULL,
  `currency` varchar(50) NOT NULL,
  `amount` varchar(50) NOT NULL,
  `amount1` varchar(50) NOT NULL,
  `amount2` varchar(50) NOT NULL,
  `totalAmount` varchar(50) NOT NULL,
  `ogeroTotalAmount` varchar(50) NOT NULL,
  `ogeroFees` varchar(50) NOT NULL,
  `additionalFees` varchar(50) NOT NULL,
  `fees` varchar(50) NOT NULL,
  `fees1` varchar(50) NOT NULL,
  `displayedFees` int(50) DEFAULT NULL,
  `rounding` varchar(50) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `landlinerequest`
--

INSERT INTO `landlinerequest` (`id`, `suyoolUserId`, `gsmNumber`, `transactionId`, `ogeroBills`, `ogeroPenalty`, `ogeroInitiationDate`, `ogeroClientName`, `ogeroAddress`, `currency`, `amount`, `amount1`, `amount2`, `totalAmount`, `ogeroTotalAmount`, `ogeroFees`, `additionalFees`, `fees`, `fees1`, `displayedFees`, `rounding`, `created`, `updated`) VALUES
(1, 89, '01123120', 1735835, '[{\"Year\":\"2019\",\"Amount\":\"50,000\",\"Month\":\"5\"}]', '', '15/08/1945', 'ملحم بركات', 'كفرشيما ش. نزار فرنسيس ب. ماجدة الرومي / ملكه', 'LBP', '52000', '50000', '0', '53000', '52000', '2000', '0', '1000', '0', NULL, '0', '2023-08-21 10:09:37', '2023-08-21 10:09:37'),
(2, 89, '01123120', 1735842, '[{\"Year\":\"2019\",\"Amount\":\"50,000\",\"Month\":\"5\"}]', '', '15/08/1945', 'ملحم بركات', 'كفرشيما ش. نزار فرنسيس ب. ماجدة الرومي / ملكه', 'LBP', '52000', '50000', '0', '53000', '52000', '2000', '0', '1000', '0', NULL, '0', '2023-08-21 12:28:04', '2023-08-21 12:28:04'),
(3, 89, '01123120', 1735843, '[{\"Year\":\"2019\",\"Amount\":\"50,000\",\"Month\":\"5\"}]', '', '15/08/1945', 'ملحم بركات', 'كفرشيما ش. نزار فرنسيس ب. ماجدة الرومي / ملكه', 'LBP', '52000', '50000', '0', '53000', '52000', '2000', '0', '1000', '0', NULL, '0', '2023-08-21 13:36:30', '2023-08-21 13:36:30'),
(4, 89, '01123120', 1735844, '[{\"Year\":\"2019\",\"Amount\":\"50,000\",\"Month\":\"5\"}]', '', '15/08/1945', 'ملحم بركات', 'كفرشيما ش. نزار فرنسيس ب. ماجدة الرومي / ملكه', 'LBP', '52000', '50000', '0', '53000', '52000', '2000', '0', '1000', '0', NULL, '0', '2023-08-21 13:39:47', '2023-08-21 13:39:47'),
(5, 89, '01123120', 1735845, '[{\"Year\":\"2019\",\"Amount\":\"50,000\",\"Month\":\"5\"}]', '', '15/08/1945', 'ملحم بركات', 'كفرشيما ش. نزار فرنسيس ب. ماجدة الرومي / ملكه', 'LBP', '52000', '50000', '0', '53000', '52000', '2000', '0', '1000', '0', NULL, '0', '2023-08-21 13:43:00', '2023-08-21 13:43:00'),
(6, 89, '01123120', 1735846, '[{\"Year\":\"2019\",\"Amount\":\"50,000\",\"Month\":\"5\"}]', '', '15/08/1945', 'ملحم بركات', 'كفرشيما ش. نزار فرنسيس ب. ماجدة الرومي / ملكه', 'LBP', '52000', '50000', '0', '53000', '52000', '2000', '0', '1000', '0', NULL, '0', '2023-08-21 13:44:23', '2023-08-21 13:44:23'),
(7, 89, '01123120', 1735847, '[{\"Year\":\"2019\",\"Amount\":\"50,000\",\"Month\":\"5\"}]', '', '15/08/1945', 'ملحم بركات', 'كفرشيما ش. نزار فرنسيس ب. ماجدة الرومي / ملكه', 'LBP', '52000', '50000', '0', '53000', '52000', '2000', '0', '1000', '0', NULL, '0', '2023-08-21 13:45:52', '2023-08-21 13:45:52');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `suyoolUserId` int(11) NOT NULL,
  `transId` int(11) DEFAULT NULL,
  `landline_id` int(11) DEFAULT NULL,
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

INSERT INTO `orders` (`id`, `suyoolUserId`, `transId`, `landline_id`, `status`, `amount`, `currency`, `errorInfo`, `create_date`, `update_date`) VALUES
(1, 155, 9150, 1, 'completed', 53000, 'LBP', NULL, '2023-08-21 10:09:46', '2023-08-21 10:10:01'),
(3, 155, NULL, NULL, 'canceled', 53000, 'LBP', 'HTTP/1.1 404 Not Found returned for \"http://10.20.80.62/SuyoolGlobalAPIs/api/Utilities/PushUtilityPayments\".', '2023-08-21 13:36:32', '2023-08-21 13:36:34'),
(4, 155, NULL, NULL, 'canceled', 53000, 'LBP', 'HTTP/1.1 404 Not Found returned for \"http://10.20.80.62/SuyoolGlobalAPIs/api/Utilities/PushUtilityPayments\".', '2023-08-21 13:39:50', '2023-08-21 13:39:50'),
(5, 155, NULL, NULL, 'canceled', 53000, 'LBP', 'HTTP/1.1 404 Not Found returned for \"http://10.20.80.62/SuyoolGlobalAPIs/api/Utilities/PushUtilityPayments\".', '2023-08-21 13:43:02', '2023-08-21 13:43:02'),
(6, 155, NULL, NULL, 'canceled', 53000, 'LBP', 'HTTP/1.1 404 Not Found returned for \"http://10.20.80.62/SuyoolGlobalAPIs/api/Utilities/PushUtilityPayments\".', '2023-08-21 13:44:25', '2023-08-21 13:44:25'),
(7, 155, NULL, NULL, 'canceled', 53000, 'LBP', 'HTTP/1.1 404 Not Found returned for \"http://10.20.80.62/SuyoolGlobalAPIs/api/Utilities/PushUtilityPayments\".', '2023-08-21 13:45:53', '2023-08-21 13:45:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `landline`
--
ALTER TABLE `landline`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `landlinerequest`
--
ALTER TABLE `landlinerequest`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `postpaid_id` (`landline_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `landline`
--
ALTER TABLE `landline`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `landlinerequest`
--
ALTER TABLE `landlinerequest`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
