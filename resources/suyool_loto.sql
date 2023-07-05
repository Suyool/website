-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2023 at 10:36 AM
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
-- Database: `suyool_loto`
--

-- --------------------------------------------------------

--
-- Table structure for table `draws`
--

CREATE TABLE `draws` (
  `id` int(11) NOT NULL,
  `drawId` int(11) NOT NULL,
  `drawdate` datetime NOT NULL,
  `prize` varchar(30) NOT NULL,
  `zeedprize` varchar(30) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `draws`
--

INSERT INTO `draws` (`id`, `drawId`, `drawdate`, `prize`, `zeedprize`, `created`) VALUES
(7, 2117, '2023-06-12 17:45:00', '15500000000', '400000000', '2023-06-09 11:28:44'),
(8, 2118, '2023-06-15 17:45:00', '17000000000', '100000000', '2023-06-14 07:29:59');

-- --------------------------------------------------------

--
-- Table structure for table `prices`
--

CREATE TABLE `prices` (
  `id` int(11) NOT NULL,
  `numbers` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `zeed` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `prices`
--

INSERT INTO `prices` (`id`, `numbers`, `price`, `zeed`, `created`) VALUES
(52, 6, 20000, 5000, '2023-06-14 07:44:36'),
(53, 7, 140000, 5000, '2023-06-14 07:44:36'),
(54, 8, 560000, 5000, '2023-06-14 07:44:36'),
(55, 9, 1680000, 5000, '2023-06-14 07:44:36'),
(56, 10, 4200000, 5000, '2023-06-14 07:44:36'),
(57, 11, 9240000, 5000, '2023-06-14 07:44:36'),
(58, 12, 18480000, 5000, '2023-06-14 07:44:36');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `drawId` varchar(30) NOT NULL,
  `drawdate` datetime NOT NULL,
  `winner1` varchar(100) NOT NULL,
  `winner2` varchar(100) NOT NULL,
  `winner3` varchar(100) NOT NULL,
  `winner4` varchar(100) NOT NULL,
  `winner5` varchar(100) NOT NULL,
  `numbers` varchar(100) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `drawId`, `drawdate`, `winner1`, `winner2`, `winner3`, `winner4`, `winner5`, `numbers`, `created`) VALUES
(54, '2117', '2023-06-12 00:00:00', '15162499216', '557964375', '32002500', '633295', '80000', '1,11,26,27,32,37,12', '2023-06-15 09:12:40'),
(55, '2116', '2023-06-08 00:00:00', '13902400778', '1046750650', '30665981', '523087', '80000', '3,8,19,27,29,34,39', '2023-06-15 09:12:40'),
(56, '2115', '2023-06-05 00:00:00', '12614429565', '916687750', '23244975', '431662', '80000', '1,2,3,17,21,42,11', '2023-06-15 09:12:40'),
(57, '2114', '2023-06-01 00:00:00', '11394068377', '1417834250', '26378300', '524072', '80000', '9,15,21,22,24,41,16', '2023-06-15 09:12:40'),
(58, '2113', '2023-05-29 00:00:00', '10147693702', '444391950', '13610127', '434366', '80000', '2,8,12,16,18,31,33', '2023-06-15 09:12:40'),
(59, '2112', '2023-05-25 00:00:00', '8968716427', '567043250', '10388744', '295585', '80000', '5,7,9,13,19,22,35', '2023-06-15 09:12:40'),
(60, '2111', '2023-05-22 00:00:00', '7850627864', '923496800', '60667200', '532168', '80000', '4,8,24,26,37,38,6', '2023-06-15 09:12:40'),
(61, '2110', '2023-05-18 00:00:00', '21321472918', '480690350', '20136467', '400947', '80000', '6,16,19,22,24,37,20', '2023-06-15 09:12:40'),
(62, '2109', '2023-05-15 00:00:00', '19418576818', '1307573925', '23494745', '432902', '80000', '5,7,12,29,33,39,41', '2023-06-15 09:12:40'),
(63, '2108', '2023-05-11 00:00:00', '18061755268', '404634125', '13704125', '422387', '80000', '5,17,18,25,28,40,38', '2023-06-15 09:12:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `draws`
--
ALTER TABLE `draws`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prices`
--
ALTER TABLE `prices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `draws`
--
ALTER TABLE `draws`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `prices`
--
ALTER TABLE `prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
