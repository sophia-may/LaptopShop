-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2026 at 08:07 AM
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
-- Database: `laptopshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`user_id`) VALUES
(1);

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `thumbnail_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `published_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `admin_id`, `title`, `slug`, `content`, `meta_title`, `meta_description`, `meta_keywords`, `thumbnail_url`, `created_at`, `published_at`) VALUES
(1, 1, 'Top 5 Laptop Gaming Tot Nhat 2025', 'top-5-laptop-gaming-tot-nhat-2025', '<p>Noi dung chi tiet ve top 5 laptop gaming 2025...</p>', 'Top 5 Laptop Gaming 2025 | LaptopShop VN', 'Danh sach top 5 laptop gaming tot nhat nam 2025 voi hieu nang cao, tan nhiet tot.', 'laptop gaming, laptop gaming 2025, laptop choi game', NULL, '2026-05-08 15:05:19', '2026-05-08 15:05:19'),
(2, 1, 'MacBook Air M2 - Co Dang Mua Khong?', 'macbook-air-m2-co-dang-mua-khong', '<p>Danh gia chi tiet MacBook Air M2...</p>', 'Review MacBook Air M2 | LaptopShop VN', 'Danh gia chi tiet MacBook Air M2 - hieu nang, thiet ke, pin va gia ban.', 'macbook air m2, apple m2, review macbook', NULL, '2026-05-08 15:05:19', '2026-05-08 15:05:19');

-- --------------------------------------------------------

--
-- Table structure for table `article_comments`
--

CREATE TABLE `article_comments` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `slug`, `logo_url`, `created_at`) VALUES
(1, 'Asus', 'asus', NULL, '2026-05-08 15:05:18'),
(2, 'Dell', 'dell', NULL, '2026-05-08 15:05:18'),
(3, 'HP', 'hp', NULL, '2026-05-08 15:05:18'),
(4, 'Apple', 'apple', NULL, '2026-05-08 15:05:18');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `is_featured`, `created_at`) VALUES
(1, 'Laptop Gaming', 'laptop-gaming', 1, '2026-05-08 15:05:18'),
(2, 'Laptop Van Phong', 'laptop-van-phong', 1, '2026-05-08 15:05:18'),
(3, 'Laptop Do Hoa', 'laptop-do-hoa', 0, '2026-05-08 15:05:18'),
(4, 'MacBook', 'macbook', 1, '2026-05-08 15:05:18');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') NOT NULL DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `customer_name`, `customer_email`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'Khach Hang X', 'khachhangx@gmail.com', 'Hoi ve thoi gian giao hang', 'Cho minh hoi don hang giao noi thanh mat bao lau?', 'unread', '2026-05-08 15:05:19'),
(2, 'Nguyen Thi Y', 'nty@gmail.com', 'Yeu cau xuat hoa don VAT', 'Cho toi xin hoa don VAT cho don hang DH240001.', 'replied', '2026-05-08 15:05:19');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Chính sách bảo hành như thế nào?', 'Tất cả sản phẩm được bảo hành chính hàng tối thiểu 12 tháng. Một số dòng máy Apple được bảo hành lên đến 24 tháng.', 1, 1, '2026-05-08 15:05:19', '2026-05-10 06:05:47'),
(2, 'Đổi trả sản phẩm như thế nào?', 'Bạn có thể trả lại hoặc đổi sản phẩm trong vòng 7 ngày nếu sản phẩm có lỗi sản xuất, còn nguyên niêm phong và đầy đủ phụ kiện.', 2, 1, '2026-05-08 15:05:19', '2026-05-10 06:05:01'),
(3, 'Có hỗ trợ trả góp không?', 'Chúng tôi cung cấp hình thức trả góp 0% lãi suất thông qua thẻ tín dụng từ các ngân hàng đối tác: VIB, Techcombank và Sacombank.', 3, 1, '2026-05-08 15:05:19', '2026-05-10 06:05:21'),
(4, 'Thời gian giao hàng bao lâu?', 'Trong Thành phố Hồ Chí Minh: 2-4 giờ. Các tỉnh thành khác: 1-3 ngày làm việc.', 4, 1, '2026-05-08 15:05:19', '2026-05-10 06:05:33');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `user_id` int(11) NOT NULL,
  `tier_id` int(11) DEFAULT NULL,
  `points` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`user_id`, `tier_id`, `points`) VALUES
(2, 1, 10),
(3, 2, 150),
(4, 3, 620);

-- --------------------------------------------------------

--
-- Table structure for table `membership_tiers`
--

CREATE TABLE `membership_tiers` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `min_points` int(11) NOT NULL DEFAULT 0,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `membership_tiers`
--

INSERT INTO `membership_tiers` (`id`, `name`, `min_points`, `discount_percent`) VALUES
(1, 'S-New', 0, 0.00),
(2, 'S-Student', 100, 2.00),
(3, 'S-Mem', 500, 3.00),
(4, 'S-Vip', 2000, 5.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_code` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `final_amount` decimal(15,2) NOT NULL,
  `payment_method` enum('cod','credit_card') NOT NULL DEFAULT 'cod',
  `payment_status` enum('unpaid','paid','refunded') NOT NULL DEFAULT 'unpaid',
  `status` enum('pending','confirmed','shipping','completed','canceled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `total_price` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `detail_description` longtext DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `brand_id`, `name`, `slug`, `short_description`, `detail_description`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Asus ROG Strix G15', 'asus-rog-strix-g15', 'Co may choi game thuc thu', NULL, 1, '2026-05-08 15:05:18', '2026-05-08 15:05:18'),
(2, 2, 2, 'Dell Inspiron 15', 'dell-inspiron-15', 'Laptop van phong ben bi', NULL, 1, '2026-05-08 15:05:18', '2026-05-08 15:05:18'),
(3, 4, 4, 'MacBook Air M2', 'macbook-air-m2', 'Sieu mong nhe, hieu nang manh', NULL, 1, '2026-05-08 15:05:18', '2026-05-08 15:05:18');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `sku_code` varchar(50) NOT NULL,
  `ram` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `storage` varchar(50) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `base_price` decimal(15,2) NOT NULL,
  `img_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `sku_code`, `ram`, `color`, `storage`, `quantity`, `base_price`, `img_url`) VALUES
(1, 1, 'ROG-G15-8GB-BLK', '8GB', 'Black', '512GB SSD', 10, 25000000.00, NULL),
(2, 1, 'ROG-G15-16GB-BLK', '16GB', 'Black', '512GB SSD', 5, 27500000.00, NULL),
(3, 2, 'DELL-INS-8GB-SIL', '8GB', 'Silver', '256GB SSD', 20, 15000000.00, NULL),
(4, 3, 'MAC-M2-8GB-MID', '8GB', 'Midnight', '256GB SSD', 8, 28000000.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','reject') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `rating`, `comment`, `status`, `created_at`) VALUES
(2, 3, 2, 4, 'Phu hop voi cong viec van phong, pin on.', 'approved', '2026-05-08 15:05:19'),
(3, 2, 3, 5, 'Thiet ke dep, mong nhe, dang tien.', 'pending', '2026-05-08 15:05:19');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`key`, `value`, `updated_at`) VALUES
('about.hero_subtitle', 'Shopping Online', '2026-05-10 05:48:52'),
('about.intro', '', '2026-05-10 05:48:52'),
('about.intro_title', '', '2026-05-10 05:48:52'),
('about.mission', '', '2026-05-10 05:48:52'),
('about.mission_title', '', '2026-05-10 05:48:52'),
('about.page_title', 'Black Friday', '2026-05-10 05:48:52'),
('about.stats_customers', '', '2026-05-10 05:48:52'),
('about.stats_customers_label', '', '2026-05-10 05:48:52'),
('about.stats_products', '', '2026-05-10 05:48:52'),
('about.stats_products_label', '', '2026-05-10 05:48:52'),
('about.stats_reviews', '', '2026-05-10 05:48:52'),
('about.stats_reviews_label', '', '2026-05-10 05:48:52'),
('about.stats_years', '', '2026-05-10 05:48:52'),
('about.stats_years_label', '', '2026-05-10 05:48:52'),
('about.values', '', '2026-05-10 05:48:52'),
('about.values_title', '', '2026-05-10 05:48:52'),
('about.vision', '', '2026-05-10 05:48:52'),
('about.vision_title', '', '2026-05-10 05:48:52'),
('address', '123 Duong Cong Nghe, Quan 1, TP.HCM', '2026-05-08 15:05:18'),
('company_name', 'LaptopShop VN', '2026-05-08 15:05:18'),
('email', 'contact@laptopshop.vn', '2026-05-08 15:05:18'),
('homepage_banner_1', '/uploads/settings/banner1.jpg', '2026-05-08 15:05:18'),
('homepage_banner_2', '/uploads/settings/banner2.jpg', '2026-05-08 15:05:18'),
('homepage_intro_text', 'Chung toi chuyen cung cap cac dong laptop chinh hang, gia canh tranh voi dich vu hau mai tot nhat thi truong.', '2026-05-08 15:05:18'),
('homepage_intro_title', 'Ve LaptopShop', '2026-05-08 15:05:18'),
('phone', '1900 1234', '2026-05-08 15:05:18'),
('social_facebook', 'https://facebook.com/laptopshop', '2026-05-08 15:05:18'),
('social_youtube', 'https://youtube.com/laptopshop', '2026-05-08 15:05:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `password_hash`, `avatar_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@laptop.vn', '0901234567', '$2y$10$nGmmkhAr/YzdE25rN6NjGOOX9.SP0VcsdOrHudf3syYuXpmXvS11e', NULL, 1, '2026-05-08 15:05:18', '2026-05-08 15:05:18'),
(2, 'Nguyen Van A', 'nva@gmail.com', '0912345678', '$2y$10$nGmmkhAr/YzdE25rN6NjGOOX9.SP0VcsdOrHudf3syYuXpmXvS11e', NULL, 1, '2026-05-08 15:05:18', '2026-05-08 15:05:18'),
(3, 'Tran Thi B', 'ttb@gmail.com', '0923456789', '$2y$10$nGmmkhAr/YzdE25rN6NjGOOX9.SP0VcsdOrHudf3syYuXpmXvS11e', NULL, 1, '2026-05-08 15:05:18', '2026-05-08 15:05:18'),
(4, 'Le Van C', 'lvc@gmail.com', '0934567890', '$2y$10$nGmmkhAr/YzdE25rN6NjGOOX9.SP0VcsdOrHudf3syYuXpmXvS11e', NULL, 0, '2026-05-08 15:05:18', '2026-05-08 15:05:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `article_comments`
--
ALTER TABLE `article_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_id`,`variant_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `tier_id` (`tier_id`);

--
-- Indexes for table `membership_tiers`
--
ALTER TABLE `membership_tiers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_order_variant` (`order_id`,`variant_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku_code` (`sku_code`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `article_comments`
--
ALTER TABLE `article_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `membership_tiers`
--
ALTER TABLE `membership_tiers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `article_comments`
--
ALTER TABLE `article_comments`
  ADD CONSTRAINT `article_comments_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `members_ibfk_2` FOREIGN KEY (`tier_id`) REFERENCES `membership_tiers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
