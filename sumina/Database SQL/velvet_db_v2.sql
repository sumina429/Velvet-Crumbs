-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2026 at 07:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `velvet_db`
CREATE DATABASE velvet_db;
USE velvet_db;
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `uid`, `pid`, `quantity`) VALUES
(15, 4, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `oid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `items_total` int(11) NOT NULL,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `tax` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`oid`, `uid`, `items_total`, `shipping_fee`, `tax`, `total`, `status`, `payment_method`, `created_at`, `updated_at`) VALUES
(1, 3, 0, 0.00, 32, 347.00, 'Pending', 'Cash on Delivery', '2026-01-06 16:15:20', '2026-01-07 02:21:28'),
(2, 3, 0, 0.00, 18, 198.00, 'Delivered', 'Cash on Delivery', '2026-01-06 16:18:45', '2026-01-07 02:50:42'),
(3, 3, 0, 200.00, 45, 695.00, 'Delivered', 'Cash on Delivery', '2026-01-06 16:23:33', '2026-01-08 11:21:40'),
(4, 3, 0, 200.00, 153, 1883.00, 'Pending', 'Cash on Delivery', '2026-01-06 16:26:32', '2026-01-07 02:50:13'),
(5, 3, 0, 200.00, 60, 860.00, 'Processing', 'Cash on Delivery', '2026-01-07 01:15:48', '2026-01-07 02:16:09'),
(6, 3, 0, 0.00, 45, 495.00, 'Pending', 'Cash on Delivery', '2026-01-07 03:39:37', '2026-01-07 03:39:37'),
(7, 3, 0, 200.00, 48, 728.00, 'Shipped', 'Cash on Delivery', '2026-01-07 13:22:47', '2026-01-08 16:18:29'),
(8, 3, 0, 200.00, 45, 695.00, 'Pending', 'Cash on Delivery', '2026-01-07 14:23:54', '2026-01-07 14:23:54'),
(9, 3, 0, 200.00, 15, 365.00, 'Pending', 'Cash on Delivery', '2026-01-08 17:05:58', '2026-01-08 17:05:58'),
(10, 3, 0, 200.00, 135, 1685.00, 'Pending', 'Cash on Delivery', '2026-01-08 17:21:17', '2026-01-08 17:21:17'),
(11, 3, 0, 200.00, 15, 365.00, 'Pending', 'Cash on Delivery', '2026-01-08 17:21:50', '2026-01-08 17:21:50'),
(12, 7, 130, 200.00, 13, 343.00, '', 'Cash on Delivery', '2026-01-29 17:26:28', '2026-01-29 17:26:28'),
(13, 7, 125, 200.00, 12, 337.50, '', 'Cash on Delivery', '2026-01-29 17:54:22', '2026-01-29 17:54:22'),
(14, 7, 30, 200.00, 3, 233.00, '', 'Cash on Delivery', '2026-01-29 18:05:40', '2026-01-29 18:05:40'),
(15, 7, 150, 200.00, 15, 365.00, '', 'Online Payment', '2026-01-29 18:13:27', '2026-01-29 18:13:27'),
(16, 7, 150, 200.00, 15, 365.00, '', 'Online Payment', '2026-01-29 18:15:50', '2026-01-29 18:15:50'),
(17, 7, 75, 200.00, 8, 282.50, '', 'Online Payment', '2026-01-29 18:19:17', '2026-01-29 18:19:17'),
(18, 7, 30, 200.00, 3, 233.00, '', 'COD', '2026-01-29 18:22:21', '2026-01-29 18:22:21'),
(19, 7, 50, 200.00, 5, 255.00, '', 'Online Payment', '2026-01-29 18:22:36', '2026-01-29 18:22:36');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `oid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `oid`, `pid`, `quantity`) VALUES
(1, 1, 5, 1),
(2, 1, 6, 1),
(3, 1, 9, 3),
(4, 2, 5, 1),
(5, 2, 6, 1),
(6, 3, 4, 1),
(7, 3, 8, 1),
(8, 4, 4, 1),
(9, 4, 3, 1),
(10, 4, 6, 1),
(11, 5, 4, 1),
(12, 5, 5, 1),
(13, 5, 5, 1),
(14, 6, 5, 3),
(15, 7, 5, 2),
(16, 7, 6, 1),
(17, 7, 8, 1),
(18, 8, 4, 1),
(19, 8, 5, 1),
(20, 9, 5, 1),
(21, 10, 4, 1),
(22, 10, 5, 1),
(23, 10, 4, 1),
(24, 10, 4, 1),
(25, 10, 4, 1),
(26, 11, 5, 1),
(27, 12, 7, 2),
(28, 12, 6, 1),
(29, 13, 6, 1),
(30, 13, 7, 1),
(31, 13, 9, 1),
(32, 14, 6, 1),
(33, 15, 5, 1),
(34, 16, 5, 1),
(35, 17, 6, 1),
(36, 17, 9, 1),
(37, 18, 6, 1),
(38, 19, 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `pid` int(11) NOT NULL,
  `p_name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `stock` int(11) DEFAULT NULL,
  `image` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`pid`, `p_name`, `price`, `stock`, `image`, `category`, `description`) VALUES
(4, 'Doughnut2', 300, 15, 'doughnut2.webp', 'doughnut', 'creamy and shot doughnut with sprinkles'),
(5, 'Cupcake1', 150, 30, 'cupcake1.jpg', 'cupcake', 'cup cake with sprinkles'),
(6, 'Doughnut', 30, 35, 'doughnut1.jpg', 'dougnut', 'creamy dougnut'),
(7, 'Cupcake3', 50, 25, 'cupcakes.webp', 'cupcake', 'warm cupcake'),
(8, 'Eggless Black Forest Pastry', 150, 50, 'images.jpg', 'cake', 'dark chocolate cake'),
(9, '5cups', 45, 46, 'cupcake2.jpg', 'hkjhjk', 'khjkb');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `role` enum('admin','customer','','') NOT NULL DEFAULT 'customer',
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact` int(11) DEFAULT NULL,
  `gender` enum('male','female','','') NOT NULL,
  `dob` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `role`, `username`, `password`, `fname`, `email`, `address`, `contact`, `gender`, `dob`, `created_at`, `updated_at`) VALUES
(2, 'admin', 'aatish', '$2y$10$HdTgImI01fEQsb968SjdkOBLTpWLRr6Z3gcg5/myNR5xzEDoUzPPS', 'aatish', 'machamasi321@gmail.com', '', 0, '', NULL, '2026-01-05 23:36:48', '2026-01-05 23:36:48'),
(3, 'customer', 'customer', '$2y$10$5N6w2fQ5bHxD8IBXJUnMgud2y5zu2NUTB8z5L9Y3NqYKM2/Xkdy7S', 'Customer', 'customer@gmail.com', '', 0, '', NULL, '2026-01-05 23:41:49', '2026-01-05 23:41:49'),
(4, 'customer', 'ayusha', '$2y$10$2mbChMeuljUuj6IEdz34jeHg.MC/CkkizOvA/1gCVpCnl6CA/Hc8K', 'Ayusha ', 'ayusha@gmail.com', '', 0, '', NULL, '2026-01-06 15:53:04', '2026-01-06 15:53:04'),
(5, 'customer', 'test', '$2y$10$QNGLtaHQhSERl6PI5evWteMGOSZICUw4DsRuP7ORpVMrJOC2On/Ia', 'Test', 'test@gmail.com', '', 0, '', NULL, '2026-01-07 04:13:41', '2026-01-07 04:13:41'),
(6, 'admin', 'admin', '$2y$10$2xIHWzcy.ARtX/J0YP9sDOboFl83pJYnb44jtrlxzhJwzL.6z9zsi', 'Admin', 'admin@gmail.com', '', 0, '', NULL, '2026-01-07 04:41:37', '2026-01-07 04:41:37'),
(7, 'customer', '44T15H', '$2y$10$cQ4VbsrBJTtnGIcNCh0bmO.a0d7n8nps7UXIl8NLQij0k5A30hkyi', 'Aatish Machamsi', 'machamasi987@gmail.com', '', 0, '', NULL, '2026-01-29 16:22:26', '2026-01-29 16:22:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_product_id` (`pid`),
  ADD KEY `fk_user_id` (`uid`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`oid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `oid` (`oid`),
  ADD KEY `pid` (`pid`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`pid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `oid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_product_id` FOREIGN KEY (`pid`) REFERENCES `products` (`pid`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`oid`) REFERENCES `orders` (`oid`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
