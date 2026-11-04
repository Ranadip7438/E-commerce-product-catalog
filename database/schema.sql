SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `electro_catalog` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `electro_catalog`;

CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  KEY `fk_cart_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`) VALUES
(4, 'Accessories'),
(1, 'Audio'),
(2, 'Computing'),
(3, 'Mobile'),
(5, 'Photography');

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(30) NOT NULL DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_orders_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order` (`order_id`),
  KEY `fk_order_items_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(160) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) UNSIGNED NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_products_name` (`name`),
  KEY `idx_products_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `image`, `stock`, `created_at`) VALUES
(1, 1, 'Studio Wireless Headphones', 'Comfort-first wireless headphones with a balanced sound profile, soft ear cushions, and a clean everyday design.', 5499.00, 'wireless-headphones.jpg', 18, '2026-10-14 05:14:07'),
(2, 1, 'Portable Bluetooth Speaker', 'A small portable speaker for desk listening and casual get-togethers, with a simple, understated form.', 3499.00, 'bluetooth-speaker.jpg', 20, '2026-10-14 05:14:07'),
(3, 1, 'Active Noise Canceling Earbuds', 'True wireless earbuds featuring active noise cancellation and crystal-clear call quality for daily commutes.', 4999.00, 'anc-earbuds.jpg', 15, '2026-10-14 05:14:07'),
(4, 1, 'Soundbar Pro 2.1', 'A sleek soundbar with a wireless subwoofer for immersive home entertainment and movie nights.', 12999.00, 'soundbar.jpg', 7, '2026-10-14 05:14:07'),
(5, 2, 'Everyday Laptop 14', 'A lightweight everyday laptop for study, office work, browsing, and streaming. Compact enough to carry all day.', 58999.00, 'laptop.jpg', 6, '2026-10-14 05:14:07'),
(6, 2, 'Mechanical Gaming Keyboard', 'A tactile mechanical keyboard with customizable RGB backlighting and durable switches for high-performance typing.', 4599.00, 'mechanical-keyboard.jpg', 14, '2026-10-14 05:14:07'),
(7, 2, 'Ergonomic Wireless Mouse', 'A contoured wireless mouse designed to reduce wrist strain during long hours of office or creative work.', 1899.00, 'wireless-mouse.jpg', 29, '2026-10-14 05:14:07'),
(8, 2, 'UltraWide 29-inch Monitor', 'An expansive IPS display offering stunning color accuracy and ample screen real estate for multitasking.', 21999.00, 'ultrawide-monitor.jpg', 5, '2026-10-14 05:14:07'),
(9, 2, 'External SSD 1TB', 'High-speed portable solid-state drive for fast file transfers, backups, and secure data storage on the go.', 8499.00, 'external-ssd.jpg', 22, '2026-10-14 05:14:07'),
(10, 3, 'Nova Smartphone', 'A modern smartphone concept with a vivid display, dependable battery life, and a slim profile for everyday use.', 24999.00, 'smartphone.jpg', 12, '2026-10-14 05:14:07'),
(11, 3, 'Titan Flagship Phone', 'A high-end smartphone equipped with an advanced multi-lens camera system and a powerful processor.', 69999.00, 'flagship-phone.jpg', 6, '2026-10-14 05:14:07'),
(12, 3, 'Rugged Smartwatch', 'A fitness-focused smartwatch featuring continuous heart rate tracking, GPS, and water resistance.', 7999.00, 'smartwatch.jpg', 18, '2026-10-14 05:14:07'),
(13, 3, 'Ultra Slim Power Bank 10000mAh', 'A pocket-sized portable charger providing fast charging support for smartphones and accessories.', 1999.00, 'power-bank.jpg', 40, '2026-10-14 05:14:07'),
(14, 4, 'USB-C Multiport Hub', 'A compact hub for connecting everyday devices, with a portable design for desks, classrooms, and travel.', 2299.00, 'usb-c-hub.jpg', 25, '2026-10-14 05:14:07'),
(15, 4, 'Fast GaN Wall Charger 65W', 'A compact multi-port wall charger utilizing GaN technology to power laptops, tablets, and phones simultaneously.', 2999.00, 'gan-charger.jpg', 18, '2026-10-14 05:14:07'),
(16, 4, 'Adjustable Laptop Stand', 'An ergonomic aluminum stand that elevates your laptop for better airflow and posture alignment.', 1499.00, 'laptop-stand.jpg', 24, '2026-10-14 05:14:07'),
(17, 4, 'Braided USB-C Cable 2m', 'A durable, tangle-resistant braided cable supporting high-speed data transfer and fast charging.', 699.00, 'usb-c-cable.jpg', 50, '2026-10-14 05:14:07'),
(18, 5, 'Compact Mirrorless Camera', 'A compact camera concept for travel, portraits, and everyday photography, designed to fit easily in a small bag.', 64999.00, 'camera.jpg', 5, '2026-10-14 05:14:07'),
(19, 5, 'Professional Tripod Stand', 'A lightweight yet sturdy carbon-fiber tripod with a fluid head for smooth pan shots and stable photography.', 4999.00, 'tripod.jpg', 11, '2026-10-14 05:14:07'),
(20, 5, 'LED Ring Light 18-inch', 'An adjustable brightness ring light with a phone holder, ideal for content creation, makeup tutorials, and video calls.', 3299.00, 'ring-light.jpg', 15, '2026-10-14 05:14:07');

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(254) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
