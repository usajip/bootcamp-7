-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 20, 2026 at 02:43 PM
-- Server version: 9.2.0
-- PHP Version: 8.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bootcamp_db3`
--

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `stock` int NOT NULL,
  `price` int NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `stock`, `price`, `image`, `description`, `category`) VALUES
(1, 'Laptop Putih', 23, 4090000, '6830806d19e84.jpg', 'Laptop murah', 'Elektronik'),
(2, 'Kursi Kayu', 434, 200000, '683080eb2bbd0.jpeg', 'Kursi kuat', 'Furniture'),
(4, 'Kaitlin Crosby', 49, 604000, '1748006413_image-5-1-683072a202b3d.webp', 'Vero mollit doloribus nulla ut eius explicabo Inventore mollitia molestiae ullamco', 'Furniture'),
(5, 'Cheyenne Morse', 97, 461, 'whatsapp-image-2022-07-19-at-163304-1024x768-688a22db2397a.webp', 'Ab explicabo Deseru', 'Fashion'),
(7, 'Jersey Timnas Indonesia 2025', 77, 1000000, '68e902da2ccc9.webp', 'Vero mollit doloribus nulla ut eius explicabo Inventore mollitia molestiae ullamcoVero mollit doloribus nulla ut eius explicabo Inventore mollitia molestiae ullamco', 'Fashion');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`) VALUES
(2, 'Budi', 'budi@example.com'),
(4, 'Jane', 'jane@email.com'),
(7, 'Wawan new', 'wawan_new@email.com'),
(9, 'Ali', 'ali@example.com'),
(10, 'John', 'john@example.com'),
(11, 'Abraham Jenkins', 'hohyg@mailinator.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Tugas no 2
INSERT INTO `product` (`id`, `name`, `stock`, `price`, `image`, `description`, `category`) VALUES
(3, 'Meja Kayu', 100, 500000, 'meja-kayu.jpg', 'Meja kuat', 'Furniture');

SELECT * FROM `product`;

UPDATE `product` SET `category` = 'Elektronik' WHERE `category` = 'Elektronik';

DELETE FROM `product` WHERE `id` = 3;