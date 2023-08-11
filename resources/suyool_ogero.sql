-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2023 at 10:14 AM
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
  `rounding` varchar(50) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `landline`
--

INSERT INTO `landline` (`id`, `suyoolUserId`, `gsmNumber`, `transactionId`, `transactionDescription`, `referenceNumber`, `ogeroBills`, `ogeroPenalty`, `ogeroInitiationDate`, `ogeroClientName`, `ogeroAddress`, `currency`, `amount`, `amount1`, `amount2`, `totalAmount`, `ogeroTotalAmount`, `ogeroFees`, `additionalFees`, `fees`, `fees1`, `rounding`, `created`, `updated`) VALUES
(1, 89, 1123120, 1735641, '20230800000196', '20230800000196', '[{\"Year\":\"2019\",\"Amount\":\"50,000\",\"Month\":\"5\"}]', '', '15/08/1945', 'ملحم بركات', 'كفرشيما ش. نزار فرنسيس ب. ماجدة الرومي / ملكه', 'LBP', '52000', '50000', '0', '53000', '52000', '2000', '0', '1000', '0', '0', '2023-08-11 10:48:12', '2023-08-11 10:48:12');

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
  `rounding` varchar(50) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `landlinerequest`
--

INSERT INTO `landlinerequest` (`id`, `suyoolUserId`, `gsmNumber`, `transactionId`, `ogeroBills`, `ogeroPenalty`, `ogeroInitiationDate`, `ogeroClientName`, `ogeroAddress`, `currency`, `amount`, `amount1`, `amount2`, `totalAmount`, `ogeroTotalAmount`, `ogeroFees`, `additionalFees`, `fees`, `fees1`, `rounding`, `created`, `updated`) VALUES
(1, 89, '01123120', 1735641, '[{\"Year\":\"2019\",\"Amount\":\"50,000\",\"Month\":\"5\"}]', '', '15/08/1945', 'ملحم بركات', 'كفرشيما ش. نزار فرنسيس ب. ماجدة الرومي / ملكه', 'LBP', '52000', '50000', '0', '53000', '52000', '2000', '0', '1000', '0', '0', '2023-08-11 10:48:04', '2023-08-11 10:48:04');

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
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `update_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `suyoolUserId`, `transId`, `landline_id`, `status`, `amount`, `currency`, `create_date`, `update_date`) VALUES
(1, 89, NULL, NULL, 'pending', 53000, 'LBP', '2023-08-11 10:45:18', '2023-08-11 10:45:18'),
(2, 89, 1234, 1, 'completed', 53000, 'LBP', '2023-08-11 10:48:06', '2023-08-11 10:48:13');

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
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
