-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 02, 2023 at 05:02 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `suyool_notification`
--

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `id` int(100) NOT NULL,
  `template_id` int(11) NOT NULL,
  `version` int(11) NOT NULL,
  `titleEN` text NOT NULL COMMENT 'notification title(en)',
  `titleAR` text NOT NULL COMMENT 'notification title(ar)',
  `notificationEN` text NOT NULL COMMENT 'notification body (en)',
  `notificationAR` text NOT NULL COMMENT 'notification body(ar)',
  `subjectEN` text NOT NULL COMMENT 'notification title in the app(en)',
  `subjectAR` text NOT NULL COMMENT 'notification title in the app(ar)',
  `bodyEN` text NOT NULL COMMENT 'notification body in the app(en)',
  `bodyAR` text NOT NULL COMMENT 'notification body in the app(ar)',
  `proceedButtonEN` varchar(255) NOT NULL COMMENT 'text button (en)',
  `proceedButtonAR` varchar(255) DEFAULT NULL COMMENT 'text button (ar)',
  `isInbox` tinyint(4) NOT NULL,
  `isPayment` tinyint(4) NOT NULL,
  `isDebit` tinyint(4) NOT NULL,
  `flag` int(100) NOT NULL,
  `notificationType` int(100) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`id`, `template_id`, `version`, `titleEN`, `titleAR`, `notificationEN`, `notificationAR`, `subjectEN`, `subjectAR`, `bodyEN`, `bodyAR`, `proceedButtonEN`, `proceedButtonAR`, `isInbox`, `isPayment`, `isDebit`, `flag`, `notificationType`, `created`) VALUES
(3, 1, 1, 'Accepted Alfa Payment', 'قبول الدفع Alfa', 'Your payment of $currency $amount for the mobile number $mobilenumber has been accepted.', 'تم قبول دفعتك البالغة $currency $amount لرقم الجوال $mobilenumber.', 'Accepted Alfa Payment', 'قبول الدفع Alfa', '$userFirstname, your payment of $currency $amount for the mobile number $mobilenumber has been accepted.', '$userFirstname ، تم قبول دفعتك البالغة $currency $amount لرقم الجوال $mobilenumber.', 'View Balance', 'مشاهدة الرصيد', 1, 1, 1, 1, 1, '2023-07-28 09:34:35'),
(4, 2, 1, ' Alfa Card Purchased Successfully', 'تم شراء بطاقة ألفا بنجاح', 'You have successfully purchased the $plan Alfa recharge card.\nCopy the code to recharge your mobile line. ', 'لقد اشتريت بنجاح بطاقة إعادة الشحن Alfa $ $plan.\nانسخ الرمز لإعادة شحن خط هاتفك المحمول.\nتم قبول دفع $currency $amount لرقم الجوال $mobilenumber.', ' Alfa Card Purchased Successfully', 'بطاقة ألفا تم شراؤها بنجاح', '$userFirstname , you have successfully purchased the $plan Alfa recharge card.\nCopy the code to recharge your mobile line: *14* $code # ', '$userFirstname ، لقد اشتريت بنجاح بطاقة إعادة شحن Alfa $plan.\nانسخ الكود لإعادة شحن خط هاتفك المحمول: * 14 * $code #', 'Copy code', 'رمز النسخ\n', 0, 1, 1, 94, 1, '2023-07-28 09:38:02'),
(5, 3, 1, 'LOTO Purchased Successfully', '', 'You have successfully paid $currency $amount to purchase $numgrids Grids', '', 'LOTO Purchased Successfully', '', '$userFirstname , you have successfully paid $currency $amount to purchase $numgrids Grids', '', '', NULL, 1, 1, 1, 0, 1, '2023-08-01 10:32:27'),
(6, 4, 1, 'Reversed LOTO Payment', '', 'LOTO has reversed your Suyool payment of $currency $amount related the draw $draw', '', 'Reversed LOTO Payment', '', '$userFirstname , LOTO has reversed your Suyool payment of $currency $amount related the draw $draw', '', 'View Balance', NULL, 0, 1, 1, 1, 1, '2023-08-01 11:38:41'),
(7, 5, 1, 'LOTO Ticket Confirmed', '', 'You have successfully purchased a LOTO ticket.\r\nTap to see your grid details. ', '', 'LOTO Ticket Confirmed ', '', 'Draw $draw \n$grids \nResult Date: $result\nTicket ID: $ticket', '', 'View My Grid', NULL, 0, 1, 1, 1, 1, '2023-08-01 11:45:10'),
(8, 6, 1, 'LOTO Ticket Confirmed ', '', 'You have successfully purchased a LOTO ticket with Zeed \r\nTap to see your grid details. ', '', 'LOTO Ticket Confirmed ', '', 'Draw $draw\n$grids\nResult Date: $result\nTicket Id: $ticket\nZeed: $zeed\n', '', 'View My Grid', NULL, 0, 1, 1, 1, 1, '2023-08-01 14:12:11'),
(9, 7, 1, 'LOTO Bouquet Confirmed', '', 'You have successfully purchased the Bouquet of $grids Grids with Zeed.', '', 'LOTO Bouquet Confirmed ', '', 'Draw $draw\nTotal Grids: $grids\nResult Date: $result\nTicket ID: $ticket\nZeed: $zeed\n', '', 'View My Bouquet', NULL, 0, 1, 1, 1, 1, '2023-08-01 15:01:21'),
(10, 8, 1, 'LOTO Bouquet Confirmed ', '', 'You have successfully purchased the Bouquet of $grids Grids. ', '', 'LOTO Bouquet Confirmed ', '', 'Draw $draw\r\nTotal Grids: $grids\r\nResult Date: $result\r\nTicket ID: $ticket', '', 'View My Bouquet', NULL, 0, 1, 1, 1, 1, '2023-08-01 15:01:21'),
(11, 9, 1, 'Draw $draw results', '', 'Balls: $balls\nNext Estimate Jackpot $currency $amount', '', 'Draw $draw results', '', 'Balls: $balls\r\nNext Estimate Jackpot $currency $amount', '', 'See Draw Results', NULL, 0, 1, 1, 1, 1, '2023-08-01 16:45:03'),
(12, 10, 1, 'Ready for today\'s LOTO! \r\n', '', 'Enter today\'s draw & get the chance to win today\'s jackpot: $currency $amount', '', 'Ready for today\'s LOTO! ', '', 'Enter today\'s draw & get the chance to win the jackpot of $currency $amount', '', 'Play LOTO', NULL, 0, 1, 1, 1, 1, '2023-08-02 11:46:25'),
(13, 10, 2, 'The Jackpot Awaits! ', '', 'Don\'t miss out on today\'s LOTO draw. \r\nPlay now & get the chance to win today\'s jackpot: $currency $amount', '', 'The Jackpot Awaits! ', '', 'Don\'t miss out on today\'s LOTO draw. \r\nPlay now & get the chance to win today\'s jackpot: $currency $amount.', '', 'Play LOTO', NULL, 0, 1, 1, 1, 1, '2023-08-02 15:30:09'),
(14, 10, 3, 'Feeling lucky today?', '', 'Unleash your luck with today\'s draw.\r\nPlay now & get the chance to win today\'s jackpot: $currency $amount', '', 'Feeling lucky today?', '', 'Unleash your luck with today\'s draw. \r\nPlay now & get the chance to win today\'s jackpot: $currency $amount', '', 'Play LOTO', NULL, 0, 1, 1, 1, 1, '2023-08-02 15:30:09'),
(15, 10, 4, 'Loto Fever is ON!  ', '', 'Join today\'s draw, Play NOW & Get the chance to win $currency $amount', '', 'Loto Fever is ON!  ', '', 'Join today\'s draw, Play NOW & Get the chance to win $currency $amount', '', 'Play LOTO', NULL, 0, 1, 1, 1, 1, '2023-08-02 16:14:29'),
(16, 10, 5, 'Play to Win', '', 'Embrace the excitement to WIN $currency $amount. Play Now to enter today\'s draw!', '', 'Play to Win', '', 'Embrace the excitement to WIN $currency $amount. Play Now to enter today\'s draw!', '', 'Play LOTO', NULL, 0, 1, 1, 1, 1, '2023-08-02 16:16:40'),
(17, 10, 6, 'Play, Win, Repeat ', '', 'Play LOTO now, Enter today\'s draw to Win $currency $amount. ', '', 'Play, Win, Repeat ', '', 'Play LOTO now, Enter today\'s draw to Win $currency $amount. ', '', 'Play LOTO', NULL, 0, 1, 1, 1, 1, '2023-08-02 16:16:40');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(100) NOT NULL,
  `userId` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `errorMsg` varchar(255) DEFAULT NULL,
  `params` text NOT NULL,
  `additionalData` text DEFAULT NULL,
  `titleOut` varchar(255) DEFAULT NULL,
  `bodyOut` text DEFAULT NULL,
  `titleIn` varchar(255) DEFAULT NULL,
  `bodyIn` text DEFAULT NULL,
  `proceedButton` varchar(255) DEFAULT NULL,
  `send_date` datetime DEFAULT NULL,
  `create-date` datetime NOT NULL DEFAULT current_timestamp(),
  `update-date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `userId`, `content_id`, `status`, `errorMsg`, `params`, `additionalData`, `titleOut`, `bodyOut`, `titleIn`, `bodyIn`, `proceedButton`, `send_date`, `create-date`, `update-date`) VALUES
(1, 89, 3, 'send', 'success', '{\"amount\": \"100,000\",   \"currency\": \"LBP\",   \"mobilenumber\": \"79143921\" }', '', 'Accepted Alfa Payment', 'Your payment of LBP 100,000 for the mobile number 79143921 has been accepted.', 'Accepted Alfa Payment', 'Elie, your payment of LBP 100,000 for the mobile number 79143921 has been accepted.', 'View Balance', '2023-07-28 11:33:02', '2023-07-27 16:46:36', '2023-07-28 12:33:02'),
(2, 89, 4, 'send', 'success', '{\"amount\": \"250,000\",   \"currency\": \"LBP\",   \"mobilenumber\": \"70102030\" ,\"plan\":\"alfa10\",\"code\":\"12345678910\"}', '', ' Alfa Card Purchased Successfully', 'You have successfully purchased the alfa10 Alfa recharge card.\nCopy the code to recharge your mobile line. \npayment of LBP 250,000 for the mobile number 70102030 has been accepted. ', ' Alfa Card Purchased Successfully', 'Anthony , you have successfully purchased the alfa10 Alfa recharge card.\nCopy the code to recharge your mobile line: *14* 12345678910 # ', 'Copy code', '2023-07-28 10:54:36', '2023-07-27 16:47:46', '2023-07-28 12:24:00'),
(4, 89, 4, 'send', 'success', '{\"amount\":288990,\"currency\":\"LBP\",\"plan\":\"3.38$ 13 Days\",\"code\":\"1234567890123456\"}', '', ' Alfa Card Purchased Successfully', 'Elie , you have successfully purchased the 3.38$ 13 Days Alfa recharge card.\nCopy the code to recharge your mobile line: *14* 1234567890123456 #', ' Alfa Card Purchased Successfully', 'Elie , you have successfully purchased the 3.38$ 13 Days Alfa recharge card.\nCopy the code to recharge your mobile line: *14* 1234567890123456 # ', 'Copy code', '2023-07-28 12:30:50', '2023-07-28 13:29:54', '2023-07-28 13:30:50'),
(5, 89, 3, 'send', 'success', '{\"amount\":\"158000\",\"currency\":\"LBP\",\"mobilenumber\":\"70102030\"}', '', 'Accepted Alfa Payment', 'Your payment of LBP 158000 for the mobile number 70102030 has been accepted.', 'Accepted Alfa Payment', 'Elie, your payment of LBP 158000 for the mobile number 70102030 has been accepted.', 'View Balance', '2023-07-28 12:42:36', '2023-07-28 13:41:54', '2023-07-28 13:42:36'),
(6, 89, 4, 'send', 'success', '{\"amount\":288990,\"currency\":\"LBP\",\"plan\":\"3.38$ 13 Days\",\"code\":\"1234567890123456\"}', '', ' Alfa Card Purchased Successfully', 'You have successfully purchased the 3.38$ 13 Days Alfa recharge card.\nCopy the code to recharge your mobile line. ', ' Alfa Card Purchased Successfully', 'Elie , you have successfully purchased the 3.38$ 13 Days Alfa recharge card.\nCopy the code to recharge your mobile line: *14* 1234567890123456 # ', 'Copy code', '2023-07-28 16:16:52', '2023-07-28 16:23:02', '2023-07-28 17:16:52'),
(7, 89, 4, 'send', 'success', '{\"amount\":288990,\"currency\":\"LBP\",\"plan\":\"3.38$ 13 Days\",\"code\":\"1234567890123456\"}', '', ' Alfa Card Purchased Successfully', 'You have successfully purchased the 3.38$ 13 Days Alfa recharge card.\nCopy the code to recharge your mobile line. ', ' Alfa Card Purchased Successfully', 'Elie , you have successfully purchased the 3.38$ 13 Days Alfa recharge card.\nCopy the code to recharge your mobile line: *14* 1234567890123456 # ', 'Copy code', '2023-07-31 12:45:25', '2023-07-31 13:44:15', '2023-07-31 13:45:25'),
(8, 89, 4, 'send', 'success', '{\"amount\":288990,\"currency\":\"LBP\",\"plan\":\"3.38$ 13 Days\",\"code\":\"1234567890123456\"}', '', ' Alfa Card Purchased Successfully', 'You have successfully purchased the 3.38$ 13 Days Alfa recharge card.\nCopy the code to recharge your mobile line. ', ' Alfa Card Purchased Successfully', 'Elie , you have successfully purchased the 3.38$ 13 Days Alfa recharge card.\nCopy the code to recharge your mobile line: *14* 1234567890123456 # ', 'Copy code', '2023-07-31 12:45:26', '2023-07-31 13:44:19', '2023-07-31 13:45:26'),
(9, 89, 4, 'send', 'success', '{\"amount\":288990,\"currency\":\"LBP\",\"plan\":\"3.38$ 13 Days\",\"code\":\"1234567890123456\"}', '', ' Alfa Card Purchased Successfully', 'You have successfully purchased the 3.38$ 13 Days Alfa recharge card.\nCopy the code to recharge your mobile line. ', ' Alfa Card Purchased Successfully', 'Elie , you have successfully purchased the 3.38$ 13 Days Alfa recharge card.\nCopy the code to recharge your mobile line: *14* 1234567890123456 # ', 'Copy code', '2023-07-31 14:30:39', '2023-07-31 14:29:28', '2023-07-31 15:30:39'),
(10, 89, 4, 'send', 'success', '{\"amount\":288990,\"currency\":\"LBP\",\"plan\":\"3.38$ 13 Days\",\"code\":\"1234567890123456\"}', '', ' Alfa Card Purchased Successfully', 'You have successfully purchased the 3.38$ 13 Days Alfa recharge card.\nCopy the code to recharge your mobile line. ', ' Alfa Card Purchased Successfully', 'Elie , you have successfully purchased the 3.38$ 13 Days Alfa recharge card.\nCopy the code to recharge your mobile line: *14* 1234567890123456 # ', 'Copy code', '2023-07-31 14:30:40', '2023-07-31 15:27:17', '2023-07-31 15:30:40'),
(11, 155, 4, 'send', 'success', '{\"amount\":288990,\"currency\":\"LBP\",\"plan\":\"3.38$ 13 Days\",\"code\":\"1234567890123456\"}', '*14*1234567890123456#', ' Alfa Card Purchased Successfully', 'You have successfully purchased the 3.38$ 13 Days Alfa recharge card.\nCopy the code to recharge your mobile line. ', ' Alfa Card Purchased Successfully', 'Anthony , you have successfully purchased the 3.38$ 13 Days Alfa recharge card.\nCopy the code to recharge your mobile line: *14* 1234567890123456 # ', 'Copy code', '2023-07-31 15:13:29', '2023-07-31 16:12:49', '2023-07-31 16:13:29'),
(12, 155, 5, 'send', 'success', '{\"amount\":155000,\"currency\":\"LBP\",\"numgrids\":3}', NULL, 'LOTO Purchased Successfully', 'You have successfully paid LBP 155000 to purchase 3 Grids', 'LOTO Purchased Successfully', 'Anthony , you have successfully paid LBP 155000 to purchase 3 Grids', '', '2023-08-01 11:22:02', '2023-08-01 11:18:50', '2023-08-01 11:22:02'),
(13, 155, 7, 'send', 'success', '{\"draw\":\"2132\",\"grids\":\"[\\\"15 25 8 39 24 9\\\",\\\"23 32 27 31 19 5\\\"]\",\"result\":\"03\\/08\\/2023\",\"ticket\":5227340}', NULL, 'LOTO Ticket Confirmed', 'You have successfully purchased a LOTO ticket.\nTap to see your grid details. ', 'LOTO Ticket Confirmed ', 'Draw 2132 <br>\n[\"15 25 8 39 24 9\",\"23 32 27 31 19 5\"] <br>\nResult Date: 03/08/2023\nTicket ID: 5227340', 'View My Grid', '2023-08-01 12:16:50', '2023-08-01 12:12:51', '2023-08-01 12:19:00'),
(14, 155, 6, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":55000,\"draw\":2132}', NULL, 'Reversed LOTO Payment', 'LOTO has reversed your Suyool payment of LBP 55000 related the draw 2132', 'Reversed LOTO Payment', 'Anthony , LOTO has reversed your Suyool payment of LBP 55000 related the draw 2132', 'View Balance', '2023-08-01 12:18:31', '2023-08-01 12:12:53', '2023-08-01 12:18:31'),
(15, 155, 5, 'send', 'success', '{\"amount\":105000,\"currency\":\"LBP\",\"numgrids\":2}', NULL, 'LOTO Purchased Successfully', 'You have successfully paid LBP 105000 to purchase 2 Grids', 'LOTO Purchased Successfully', 'Anthony , you have successfully paid LBP 105000 to purchase 2 Grids', '', '2023-08-01 13:14:46', '2023-08-01 13:11:56', '2023-08-01 13:14:46'),
(16, 155, 7, 'send', 'success', '{\"draw\":\"2132\",\"grids\":\"[\\\"7 6 30 41 40 11\\\"]\",\"result\":\"03\\/08\\/2023\",\"ticket\":5227340}', NULL, 'LOTO Ticket Confirmed', 'You have successfully purchased a LOTO ticket.\r\nTap to see your grid details. ', 'LOTO Ticket Confirmed ', 'Draw 2132 <br>\n[\"7 6 30 41 40 11\"] <br>\nResult Date: 03/08/2023\nTicket ID: 5227340', 'View My Grid', '2023-08-01 13:14:47', '2023-08-01 13:12:55', '2023-08-01 13:14:47'),
(17, 155, 6, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":55000,\"draw\":\"2132\"}', NULL, 'Reversed LOTO Payment', 'LOTO has reversed your Suyool payment of LBP 55000 related the draw 2132', 'Reversed LOTO Payment', 'Anthony , LOTO has reversed your Suyool payment of LBP 55000 related the draw 2132', 'View Balance', '2023-08-01 13:14:48', '2023-08-01 13:12:55', '2023-08-01 13:14:48'),
(18, 155, 5, 'send', 'success', '{\"amount\":105000,\"currency\":\"LBP\",\"numgrids\":2}', NULL, 'LOTO Purchased Successfully', 'You have successfully paid LBP 105000 to purchase 2 Grids', 'LOTO Purchased Successfully', 'Anthony , you have successfully paid LBP 105000 to purchase 2 Grids', '', '2023-08-01 13:57:44', '2023-08-01 13:53:57', '2023-08-01 13:57:44'),
(19, 155, 7, 'send', 'success', '{\"draw\":\"2132\",\"grids\":\"[\\\"30 40 31 42 18 35\\\"]\",\"result\":\"03\\/08\\/2023\",\"ticket\":5227340}', NULL, 'LOTO Ticket Confirmed', 'You have successfully purchased a LOTO ticket.\r\nTap to see your grid details. ', 'LOTO Ticket Confirmed ', 'Draw 2132 <br>\n[\"30 40 31 42 18 35\"] <br>\nResult Date: 03/08/2023\nTicket ID: 5227340', 'View My Grid', '2023-08-01 13:57:44', '2023-08-01 13:55:03', '2023-08-01 13:57:44'),
(20, 155, 6, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":55000,\"draw\":\"2132\"}', NULL, 'Reversed LOTO Payment', 'LOTO has reversed your Suyool payment of LBP 55000 related the draw 2132', 'Reversed LOTO Payment', 'Anthony , LOTO has reversed your Suyool payment of LBP 55000 related the draw 2132', 'View Balance', '2023-08-01 13:57:45', '2023-08-01 13:55:03', '2023-08-01 13:57:45'),
(25, 155, 5, 'send', 'success', '{\"amount\":155000,\"currency\":\"LBP\",\"numgrids\":3}', NULL, 'LOTO Purchased Successfully', 'You have successfully paid LBP 155000 to purchase 3 Grids', 'LOTO Purchased Successfully', 'Anthony , you have successfully paid LBP 155000 to purchase 3 Grids', '', '2023-08-01 15:51:16', '2023-08-01 15:48:30', '2023-08-01 15:51:16'),
(26, 155, 7, 'send', 'success', '{\"draw\":\"2132\",\"grids\":\"[\\\"26 11 41 29 34 12\\\"]\",\"result\":\"03\\/08\\/2023\",\"ticket\":5227340}', NULL, 'LOTO Ticket Confirmed', 'You have successfully purchased a LOTO ticket.\r\nTap to see your grid details. ', 'LOTO Ticket Confirmed ', 'Draw 2132 \n[\"26 11 41 29 34 12\"] \nResult Date: 03/08/2023\nTicket ID: 5227340', 'View My Grid', '2023-08-01 15:51:17', '2023-08-01 15:49:43', '2023-08-01 15:51:17'),
(27, 155, 10, 'send', 'success', '{\"draw\":\"2132\",\"grids\":1,\"result\":\"03\\/08\\/2023\",\"ticket\":5227340}', NULL, 'LOTO Bouquet Confirmed ', 'You have successfully purchased the Bouquet of 1 Grids. ', 'LOTO Bouquet Confirmed ', 'Draw 2132\r\nTotal Grids: 1\r\nResult Date: 03/08/2023\r\nTicket ID: 5227340', 'View My Bouquet', '2023-08-01 15:51:18', '2023-08-01 15:49:45', '2023-08-01 15:51:18'),
(28, 155, 6, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":55000,\"draw\":\"2132\"}', NULL, 'Reversed LOTO Payment', 'LOTO has reversed your Suyool payment of LBP 55000 related the draw 2132', 'Reversed LOTO Payment', 'Anthony , LOTO has reversed your Suyool payment of LBP 55000 related the draw 2132', 'View Balance', '2023-08-01 15:51:19', '2023-08-01 15:49:45', '2023-08-01 15:51:19'),
(135, 89, 11, 'send', 'success', '{\"balls\":\"3,14,17,32,39,40,33\",\"draw\":\"2131\",\"currency\":\"LBP\",\"amount\":23500000000}', NULL, 'Draw 2131 results', 'Balls: 3,14,17,32,39,40,33\nNext Estimate Jackpot LBP 23500000000', 'Draw 2131 results', 'Balls: 3,14,17,32,39,40,33\r\nNext Estimate Jackpot LBP 23500000000', 'See Draw Results', '2023-08-01 17:42:47', '2023-08-01 17:42:12', '2023-08-01 17:42:47'),
(136, 155, 11, 'send', 'success', '{\"balls\":\"3,14,17,32,39,40,33\",\"draw\":\"2131\",\"currency\":\"LBP\",\"amount\":23500000000}', NULL, 'Draw 2131 results', 'Balls: 3,14,17,32,39,40,33\nNext Estimate Jackpot LBP 23500000000', 'Draw 2131 results', 'Balls: 3,14,17,32,39,40,33\r\nNext Estimate Jackpot LBP 23500000000', 'See Draw Results', '2023-08-02 12:05:15', '2023-08-01 17:42:12', '2023-08-02 16:03:17'),
(137, 89, 11, 'send', 'success', '{\"balls\":\"3,14,17,32,39,40,33\",\"draw\":\"2131\",\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'Draw 2131 results', 'Balls: 3,14,17,32,39,40,33\nNext Estimate Jackpot LBP 23,500,000,000', 'Draw 2131 results', 'Balls: 3,14,17,32,39,40,33\r\nNext Estimate Jackpot LBP 23,500,000,000', 'See Draw Results', '2023-08-02 09:28:25', '2023-08-02 09:27:42', '2023-08-02 09:28:25'),
(138, 155, 11, 'send', 'success', '{\"balls\":\"3,14,17,32,39,40,33\",\"draw\":\"2131\",\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'Draw 2131 results', 'Balls: 3,14,17,32,39,40,33\nNext Estimate Jackpot LBP 23,500,000,000', 'Draw 2131 results', 'Balls: 3,14,17,32,39,40,33\r\nNext Estimate Jackpot LBP 23,500,000,000', 'See Draw Results', '2023-08-02 09:28:26', '2023-08-02 09:27:42', '2023-08-02 09:28:26'),
(139, 155, 5, 'send', 'success', '{\"amount\":105000,\"currency\":\"LBP\",\"numgrids\":2}', NULL, 'LOTO Purchased Successfully', 'You have successfully paid LBP 105000 to purchase 2 Grids', 'LOTO Purchased Successfully', 'Anthony , you have successfully paid LBP 105000 to purchase 2 Grids', '', '2023-08-02 11:46:42', '2023-08-02 11:04:48', '2023-08-02 11:46:42'),
(140, 155, 7, 'send', 'success', '{\"draw\":\"2132\",\"grids\":\"[\\\"26 18 11 27 14 17\\\"]\",\"result\":\"03\\/08\\/2023\",\"ticket\":5227340}', NULL, 'LOTO Ticket Confirmed', 'You have successfully purchased a LOTO ticket.\r\nTap to see your grid details. ', 'LOTO Ticket Confirmed ', 'Draw 2132 \n[\"26 18 11 27 14 17\"] \nResult Date: 03/08/2023\nTicket ID: 5227340', 'View My Grid', '2023-08-02 11:46:43', '2023-08-02 11:05:33', '2023-08-02 11:46:43'),
(141, 155, 6, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":55000,\"draw\":\"2132\"}', NULL, 'Reversed LOTO Payment', 'LOTO has reversed your Suyool payment of LBP 55000 related the draw 2132', 'Reversed LOTO Payment', 'Anthony , LOTO has reversed your Suyool payment of LBP 55000 related the draw 2132', 'View Balance', '2023-08-02 11:46:44', '2023-08-02 11:05:33', '2023-08-02 11:46:44'),
(142, 155, 5, 'send', 'success', '{\"amount\":55000,\"currency\":\"LBP\",\"numgrids\":1}', NULL, 'LOTO Purchased Successfully', 'You have successfully paid LBP 55000 to purchase 1 Grids', 'LOTO Purchased Successfully', 'Anthony , you have successfully paid LBP 55000 to purchase 1 Grids', '', '2023-08-02 11:46:45', '2023-08-02 11:08:57', '2023-08-02 11:46:45'),
(143, 155, 6, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":55000,\"draw\":0}', NULL, 'Reversed LOTO Payment', 'LOTO has reversed your Suyool payment of LBP 55000 related the draw 0', 'Reversed LOTO Payment', 'Anthony , LOTO has reversed your Suyool payment of LBP 55000 related the draw 0', 'View Balance', '2023-08-02 11:46:46', '2023-08-02 11:09:24', '2023-08-02 11:46:46'),
(144, 155, 5, 'send', 'success', '{\"amount\":55000,\"currency\":\"LBP\",\"numgrids\":1}', NULL, 'LOTO Purchased Successfully', 'You have successfully paid LBP 55000 to purchase 1 Grids', 'LOTO Purchased Successfully', 'Anthony , you have successfully paid LBP 55000 to purchase 1 Grids', '', '2023-08-02 11:46:47', '2023-08-02 11:30:02', '2023-08-02 11:46:47'),
(145, 155, 6, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":55000,\"draw\":0}', NULL, 'Reversed LOTO Payment', 'LOTO has reversed your Suyool payment of LBP 55000 related the draw 0', 'Reversed LOTO Payment', 'Anthony , LOTO has reversed your Suyool payment of LBP 55000 related the draw 0', 'View Balance', '2023-08-02 11:46:48', '2023-08-02 11:30:30', '2023-08-02 11:46:48'),
(146, 89, 12, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'Ready for today\'s LOTO! \r\n', 'Enter today\'s draw & get the chance to win today\'s jackpot: LBP 23,500,000,000', 'Ready for today\'s LOTO! ', 'Enter today\'s draw & get the chance to win the jackpot of LBP 23,500,000,000', 'Play LOTO', '2023-08-02 12:13:46', '2023-08-02 12:00:36', '2023-08-02 12:13:46'),
(147, 89, 12, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'Ready for today\'s LOTO! \r\n', 'Enter today\'s draw & get the chance to win today\'s jackpot: LBP 23,500,000,000', 'Ready for today\'s LOTO! ', 'Enter today\'s draw & get the chance to win the jackpot of LBP 23,500,000,000', 'Play LOTO', '2023-08-02 15:48:47', '2023-08-02 15:41:48', '2023-08-02 15:48:47'),
(150, 89, 12, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'Ready for today\'s LOTO! \r\n', 'Enter today\'s draw & get the chance to win today\'s jackpot: LBP 23,500,000,000', 'Ready for today\'s LOTO! ', 'Enter today\'s draw & get the chance to win the jackpot of LBP 23,500,000,000', 'Play LOTO', '2023-08-02 16:03:28', '2023-08-02 16:02:21', '2023-08-02 16:03:28'),
(151, 89, 13, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'The Jackpot Awaits! ', 'Don\'t miss out on today\'s LOTO draw. \r\nPlay now & get the chance to win today\'s jackpot: LBP 23,500,000,000', 'The Jackpot Awaits! ', 'Don\'t miss out on today\'s LOTO draw. \r\nPlay now & get the chance to win today\'s jackpot: LBP 23,500,000,000.', 'Play LOTO', '2023-08-02 16:03:57', '2023-08-02 16:03:44', '2023-08-02 16:03:57'),
(152, 89, 14, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'Feeling lucky today?', 'Unleash your luck with today\'s draw.\r\nPlay now & get the chance to win today\'s jackpot: LBP 23,500,000,000', 'Feeling lucky today?', 'Unleash your luck with today\'s draw. \r\nPlay now & get the chance to win today\'s jackpot: LBP 23,500,000,000', 'Play LOTO', '2023-08-02 16:10:09', '2023-08-02 16:10:02', '2023-08-02 16:10:09'),
(153, 89, 12, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'Ready for today\'s LOTO! \r\n', 'Enter today\'s draw & get the chance to win today\'s jackpot: LBP 23,500,000,000', 'Ready for today\'s LOTO! ', 'Enter today\'s draw & get the chance to win the jackpot of LBP 23,500,000,000', 'Play LOTO', '2023-08-02 16:18:44', '2023-08-02 16:17:43', '2023-08-02 16:18:44'),
(154, 89, 13, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'The Jackpot Awaits! ', 'Don\'t miss out on today\'s LOTO draw. \r\nPlay now & get the chance to win today\'s jackpot: LBP 23,500,000,000', 'The Jackpot Awaits! ', 'Don\'t miss out on today\'s LOTO draw. \r\nPlay now & get the chance to win today\'s jackpot: LBP 23,500,000,000.', 'Play LOTO', '2023-08-02 16:18:45', '2023-08-02 16:17:46', '2023-08-02 16:18:45'),
(155, 89, 14, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'Feeling lucky today?', 'Unleash your luck with today\'s draw.\r\nPlay now & get the chance to win today\'s jackpot: LBP 23,500,000,000', 'Feeling lucky today?', 'Unleash your luck with today\'s draw. \r\nPlay now & get the chance to win today\'s jackpot: LBP 23,500,000,000', 'Play LOTO', '2023-08-02 16:18:46', '2023-08-02 16:17:48', '2023-08-02 16:18:46'),
(156, 89, 15, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'Loto Fever is ON!  ', 'Join today\'s draw, Play NOW & Get the chance to win LBP 23,500,000,000', 'Loto Fever is ON!  ', 'Join today\'s draw, Play NOW & Get the chance to win LBP 23,500,000,000', 'Play LOTO', '2023-08-02 16:18:46', '2023-08-02 16:17:50', '2023-08-02 16:18:46'),
(157, 89, 16, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'Play to Win', 'Embrace the excitement to WIN LBP 23,500,000,000. Play Now to enter today\'s draw!', 'Play to Win', 'Embrace the excitement to WIN LBP 23,500,000,000. Play Now to enter today\'s draw!', 'Play LOTO', '2023-08-02 16:18:47', '2023-08-02 16:17:52', '2023-08-02 16:18:47'),
(158, 89, 17, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'Play, Win, Repeat ', 'Play LOTO now, Enter today\'s draw to Win LBP 23,500,000,000. ', 'Play, Win, Repeat ', 'Play LOTO now, Enter today\'s draw to Win LBP 23,500,000,000. ', 'Play LOTO', '2023-08-02 16:18:48', '2023-08-02 16:17:54', '2023-08-02 16:18:48'),
(159, 89, 12, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'Ready for today\'s LOTO! \r\n', 'Enter today\'s draw & get the chance to win today\'s jackpot: LBP 23,500,000,000', 'Ready for today\'s LOTO! ', 'Enter today\'s draw & get the chance to win the jackpot of LBP 23,500,000,000', 'Play LOTO', '2023-08-02 16:18:49', '2023-08-02 16:17:58', '2023-08-02 16:18:49'),
(160, 155, 13, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'The Jackpot Awaits! ', 'Don\'t miss out on today\'s LOTO draw. \r\nPlay now & get the chance to win today\'s jackpot: LBP 23,500,000,000', 'The Jackpot Awaits! ', 'Don\'t miss out on today\'s LOTO draw. \r\nPlay now & get the chance to win today\'s jackpot: LBP 23,500,000,000.', 'Play LOTO', '2023-08-02 16:24:04', '2023-08-02 16:19:45', '2023-08-02 16:24:04'),
(161, 155, 12, 'send', 'success', '{\"currency\":\"LBP\",\"amount\":\"23,500,000,000\"}', NULL, 'Ready for today\'s LOTO! \r\n', 'Enter today\'s draw & get the chance to win today\'s jackpot: LBP 23,500,000,000', 'Ready for today\'s LOTO! ', 'Enter today\'s draw & get the chance to win the jackpot of LBP 23,500,000,000', 'Play LOTO', '2023-08-02 16:24:05', '2023-08-02 16:23:42', '2023-08-02 16:24:05');

-- --------------------------------------------------------

--
-- Table structure for table `template`
--

CREATE TABLE `template` (
  `id` int(11) NOT NULL,
  `identifier` text NOT NULL,
  `versionIndex` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `template`
--

INSERT INTO `template` (`id`, `identifier`, `versionIndex`, `created`, `updated`) VALUES
(1, 'AcceptedAlfaPayment', 1, '2023-08-02 14:51:48', '2023-08-02 15:55:48'),
(2, 'AlfaCardPurchasedSuccessfully', 1, '2023-08-02 14:51:48', '2023-08-02 15:55:48'),
(3, 'Payment taken loto', 1, '2023-08-02 14:52:20', '2023-08-02 15:55:48'),
(4, 'Payment reversed loto', 1, '2023-08-02 14:52:20', '2023-08-02 15:55:48'),
(5, 'without zeed & without bouquet', 1, '2023-08-02 14:52:39', '2023-08-02 15:55:48'),
(6, 'with zeed & without bouquet', 1, '2023-08-02 14:52:39', '2023-08-02 15:55:48'),
(7, 'bouquet with zeed', 1, '2023-08-02 14:53:12', '2023-08-02 15:55:48'),
(8, 'bouquet without zeed', 1, '2023-08-02 14:53:12', '2023-08-02 15:55:48'),
(9, 'result if user has grid in this draw', 1, '2023-08-02 14:53:29', '2023-08-02 15:55:48'),
(10, 'reminder notification', 1, '2023-08-02 14:53:29', '2023-08-02 16:23:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `suyoolUserId` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `lang` int(2) NOT NULL,
  `create-date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`suyoolUserId`, `fname`, `lname`, `lang`, `create-date`) VALUES
(20, 'Geo', 'Ass', 1, '2023-08-01 09:05:16'),
(24, 'Jean Pierre', 'Rahme', 1, '2023-08-01 09:05:16'),
(27, 'Nancy', 'Boughannam', 1, '2023-08-01 09:05:16'),
(32, 'Pierre', 'Sarkis', 1, '2023-08-01 09:05:16'),
(34, 'Support', 'Elbarid', 1, '2023-08-01 09:05:16'),
(36, 'Vicky', 'Sebaaly', 1, '2023-08-01 09:05:16'),
(37, 'Mike', 'Semaan', 1, '2023-08-01 09:05:16'),
(50, 'Joanna', 'Abikaram', 1, '2023-08-01 09:05:16'),
(53, 'Nancy', 'Test2', 1, '2023-08-01 09:05:16'),
(70, 'Ali', 'Hammoud', 1, '2023-08-01 09:05:16'),
(89, 'Elie', 'Yammouny', 1, '2023-08-01 09:05:16'),
(113, 'Marc', 'Srouji', 1, '2023-08-01 09:05:16'),
(124, 'Test', 'Nonsuyooler', 1, '2023-08-01 09:05:16'),
(131, 'Charlyn', 'Hoayek', 1, '2023-08-01 09:05:16'),
(141, 'Lea', 'Sarkis', 1, '2023-08-01 09:05:16'),
(145, 'ZEAL', 'IOS', 1, '2023-08-01 09:05:16'),
(148, 'Georges', 'S9', 1, '2023-08-01 09:05:16'),
(152, 'Hussam', 'Osseiran', 1, '2023-08-01 09:05:16'),
(153, 'HASSAN', 'ANDROIDD', 1, '2023-08-01 09:05:16'),
(154, 'Charbel', 'Test', 1, '2023-08-01 09:05:16'),
(155, 'Anthony', 'Saliba', 1, '2023-08-01 09:05:16'),
(161, 'Marc Tets', 'Guguuv', 1, '2023-08-01 09:05:16'),
(163, 'Test Non Suyooler Req', 'Wallet To Fonee Pull', 1, '2023-08-01 09:05:16'),
(164, 'Testnonsuyooler', 'Testnancy', 1, '2023-08-01 09:05:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `template`
--
ALTER TABLE `template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`suyoolUserId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `template`
--
ALTER TABLE `template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
