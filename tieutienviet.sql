-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2018 at 08:16 PM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 7.1.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tieutienviet`
--

-- --------------------------------------------------------

--
-- Table structure for table `bill`
--

CREATE TABLE `bill` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `cost` float NOT NULL,
  `real_cost` float NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bill_has_product`
--

CREATE TABLE `bill_has_product` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `rule_description` text COLLATE utf8_unicode_ci NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`id`, `name`, `description`, `rule_description`, `active`) VALUES
(1, 'Club 1', 'Mô tả club 1', 'Quyền lợi club 1', 2),
(2, 'Club 2', 'Mô tả câu lạc bộ 2', 'Club 2', 2),
(3, 'Club 3', 'Mô tả club 3', 'Quyền lợi club 3', 1),
(4, 'CLB 4', 'Mô tả CLB 4', 'Quyền CLB 4', 1),
(16, 'CLB 5', 'Mô tả CLB 5', 'Quyền CLB 5', 1),
(17, 'Câu lạc bộ 5', 'Mô tả câu lạc bộ 5', 'Quyền câu lac bộ 5', 1);

-- --------------------------------------------------------

--
-- Table structure for table `club_has_user`
--

CREATE TABLE `club_has_user` (
  `id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `presenter_id` int(11) NOT NULL,
  `level` int(1) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `club_has_user`
--

INSERT INTO `club_has_user` (`id`, `club_id`, `user_id`, `presenter_id`, `level`, `status`) VALUES
(1, 1, 3, 1, 1, 1),
(2, 2, 3, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `images` varchar(1000) COLLATE utf8_unicode_ci NOT NULL COMMENT 'JSON TYPE',
  `price` float NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0: wait active, 1: active, 2: deleted',
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `images`, `price`, `status`, `user_id`) VALUES
(1, 'Sản phẩm 1', 'Sản phẩm 1', '/uploads/1/[ACUS] Guide book for 2018 ACU e-Learning Expert Training_1.0ver-26.jpg', 10000, 0, 0),
(2, 'Sản phẩm 2', 'Sản phẩm 2', '/uploads/1/[ACUS] Guide book for 2018 ACU e-Learning Expert Training_1.0ver-26.jpg', 20000, 0, 0),
(3, 'Sản phẩm 3', 'Mô tả sản phẩm 3', '/uploads/1/2018-05-15.jpg', 0, 1, 0),
(4, 'Sản phẩm 4', 'Mô tả sản phẩm 4', '/uploads/1/[ACUS] Guide book for 2018 ACU e-Learning Expert Training_1.0ver-26.jpg', 0, 0, 1),
(5, 'Sản phẩm 5', 'Mô tả sản phẩm 5', '/uploads/1/[ACUS] Guide book for 2018 ACU e-Learning Expert Training_1.0ver-26.jpg', 1000000, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `rules`
--

CREATE TABLE `rules` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `level` int(11) NOT NULL,
  `percent` float NOT NULL,
  `club_id` int(11) NOT NULL,
  `version` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `store`
--

CREATE TABLE `store` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `store`
--

INSERT INTO `store` (`id`, `name`, `description`, `address`, `user_id`, `group_id`, `active`) VALUES
(1, 'Cửa hàng 1', 'Mô tả cửa hàng 1', 'Ha Noi', 0, 0, 1),
(7, 'Cửa hàng 2', 'Mô tả cửa hàng 2', 'Hà nội', 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `telephone` varchar(13) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_by_system` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `session` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `telephone`, `address`, `type`, `password`, `password_by_system`, `active`, `session`) VALUES
(1, 'Admin', 'tungnt410@gmail.com', '+84986976368', 'Hà Nội', 2, 'ad13c2e9935dc3ceb691666e422cd974', '', 1, NULL),
(2, 'Admin Club', 'tungnt510@gmail.com', '+84986976368', '', 1, 'ad13c2e9935dc3ceb691666e422cd974', '', 1, NULL),
(3, 'Test user', 'tung.nguyenthanh1@hust.edu.vn', '0986976368', 'Hà Nội', 1, 'ad13c2e9935dc3ceb691666e422cd974', '', 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bill`
--
ALTER TABLE `bill`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bill_has_product`
--
ALTER TABLE `bill_has_product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `club_has_user`
--
ALTER TABLE `club_has_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rules`
--
ALTER TABLE `rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `store`
--
ALTER TABLE `store`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bill`
--
ALTER TABLE `bill`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bill_has_product`
--
ALTER TABLE `bill_has_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `club_has_user`
--
ALTER TABLE `club_has_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rules`
--
ALTER TABLE `rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `store`
--
ALTER TABLE `store`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
