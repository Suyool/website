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
(17, 10, 6, 'Play, Win, Repeat ', '', 'Play LOTO now, Enter today\'s draw to Win $currency $amount. ', '', 'Play, Win, Repeat ', '', 'Play LOTO now, Enter today\'s draw to Win $currency $amount. ', '', 'Play LOTO', NULL, 0, 1, 1, 1, 1, '2023-08-02 16:16:40'),
(18, 11, 1, 'Accepted Touch Payment', '', 'Your payment of $currency $amount for the mobile number $mobilenumber has been accepted.', '', 'Accepted Touch Payment', '', '$userFirstname, your payment of $currency $amount for the mobile number $mobilenumber has been accepted.', '', 'View Balance', NULL, 0, 1, 1, 1, 1, '2023-08-07 11:31:13'),
(19, 12, 1, 'Congratulations! 🎉', '', 'You won $currency $amount for the draw $number. \r\nThe money was added to your LBP Wallet. ', '', 'Congratulations! 🎉 ', '', '$userFirstname, you won $currency $amount for the draw $number. \nThe money was added to your LBP Wallet. \n', '', 'View Balance', NULL, 0, 1, 1, 1, 1, '2023-08-07 16:39:36');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(100) NOT NULL,
  `bulk` int(11) NOT NULL DEFAULT 0 COMMENT '1 if broadcast / 0 if not\r\n',
  `userId` text NOT NULL,
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

INSERT INTO `notification` (`id`, `bulk`, `userId`, `content_id`, `status`, `errorMsg`, `params`, `additionalData`, `titleOut`, `bodyOut`, `titleIn`, `bodyIn`, `proceedButton`, `send_date`, `create-date`, `update-date`) VALUES
(1, 0, '89', 4, 'pending', NULL, '{\"amount\":288990,\"currency\":\"LBP\",\"plan\":\"3.38$ 13 Days\",\"code\":\"1234567890123456\"}', '*14*1234567890123456#', NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-21 15:01:29', '2023-08-21 15:01:29'),
(2, 0, '155', 18, 'pending', NULL, '{\"amount\":\"134000\",\"currency\":\"LBP\",\"mobilenumber\":\"03030405\"}', '', NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-21 16:11:15', '2023-08-21 16:11:15'),
(3, 0, '155', 18, 'pending', NULL, '{\"amount\":\"134000\",\"currency\":\"LBP\",\"mobilenumber\":\"03030405\"}', '', NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-21 16:12:13', '2023-08-21 16:12:13'),
(4, 0, '155', 18, 'pending', NULL, '{\"amount\":\"134000\",\"currency\":\"LBP\",\"mobilenumber\":\"03030405\"}', '', NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-21 16:17:06', '2023-08-21 16:17:06'),
(5, 0, '155', 18, 'pending', NULL, '{\"amount\":\"134000\",\"currency\":\"LBP\",\"mobilenumber\":\"03030405\"}', '', NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-21 16:22:23', '2023-08-21 16:22:23'),
(6, 0, '155', 18, 'pending', NULL, '{\"amount\":\"134000\",\"currency\":\"LBP\",\"mobilenumber\":\"03030405\"}', '', NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-21 16:23:41', '2023-08-21 16:23:41');

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
(10, 'reminder notification', 3, '2023-08-02 14:53:29', '2023-08-03 11:02:38'),
(11, 'AcceptedTouchPayment', 1, '2023-08-07 11:29:53', '2023-08-07 11:29:53'),
(12, 'won loto added to suyool wallet', 1, '2023-08-07 16:37:33', '2023-08-07 16:37:33');

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
(50, 'Joanna', 'Abikaram', 1, '2023-08-11 14:27:30'),
(89, 'Elie', 'Yammouny', 1, '2023-08-16 16:15:33'),
(155, 'Anthony', 'Saliba', 1, '2023-08-09 11:25:16');

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
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `template`
--
ALTER TABLE `template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
