-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 18 Des 2025 pada 18.18
-- Versi server: 10.4.11-MariaDB
-- Versi PHP: 7.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `iwl_webgis_v2`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_categories`
--

CREATE TABLE `business_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `business_categories`
--

INSERT INTO `business_categories` (`category_id`, `category_name`, `description`) VALUES
(1, 'Industri Makanan', 'Pabrik makanan, roti, snack'),
(2, 'Industri Tektil', 'Industri batik, dan Lain-lain'),
(3, 'Horeca', 'Hotel, restoran, catering'),
(4, 'Water Treatment', 'Usaha laundry kiloan'),
(5, 'Pertanian dan Peternakan', 'Pertanian dan Peternakan'),
(6, 'Retail', 'Retail'),
(7, 'Industri Detergen\r\n', 'Industri Sabun Pertanian dan Peternakan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `company_capital`
--

CREATE TABLE `company_capital` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `type` enum('capital_in','prive') DEFAULT 'capital_in'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `company_capital`
--

INSERT INTO `company_capital` (`id`, `date`, `description`, `amount`, `type`) VALUES
(1, '2025-12-01', 'Dana Masuk', '20000000.00', 'capital_in');

-- --------------------------------------------------------

--
-- Struktur dari tabel `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `contact_person` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `status` enum('prospect','active','inactive','blacklist') DEFAULT 'prospect',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `customers`
--

INSERT INTO `customers` (`customer_id`, `name`, `category_id`, `address`, `city`, `district`, `latitude`, `longitude`, `contact_person`, `phone`, `email`, `status`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 'Pabrik Roti Maju Jaya', 1, 'Jl Solo Km 8', 'Yogyakarta', 'Kalasan', -7.7732, 110.4311, 'Bapak Andi', '081234000111', 'andi@roti.com', 'active', '2025-12-03 07:11:59', NULL, '2025-12-03 07:11:59'),
(2, 'Rumah Makan Padang Sedap', 2, 'Jl Parangtritis Km 3', 'Yogyakarta', 'Mantrijeron', -7.8221, 110.3671, 'Ibu Rina', '081234000222', 'rina@rmpadang.com', 'active', '2025-12-03 07:11:59', NULL, '2025-12-03 07:11:59'),
(3, 'Batik Canting Indah', 3, 'Jl Imogiri Barat', 'Bantul', 'Jetis', -7.8883, 110.3201, 'Pak Joko', '081234000333', 'joko@batik.com', 'prospect', '2025-12-03 07:11:59', NULL, '2025-12-03 07:11:59'),
(4, 'Laundry Kilat Bersih', 4, 'Jl Gejayan No 12', 'Sleman', 'Depok', -7.769, 110.4083, 'Ibu Sari', '081234000444', 'sari@laundry.com', 'active', '2025-12-03 07:11:59', NULL, '2025-12-03 07:11:59'),
(5, 'Toko Sembako Murah', 5, 'Jl Wonosari Km 7', 'Sleman', 'Ngemplak', -7.7612, 110.4509, 'Pak Bayu', '081234000555', 'bayu@sembako.com', 'prospect', '2025-12-03 07:11:59', NULL, '2025-12-03 07:11:59'),
(6, 'Kodekraf Studio', 1, 'Jl. Taman muria, Pesantren', 'Kota Semarang', 'Banguntapan', -7.805273, 110.403241, 'Ahmad Asrori', '082135362838', 'kodekrafstudio@gmail.com', 'prospect', '2025-12-03 12:43:05', NULL, '2025-12-08 09:43:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `customer_demand_history`
--

CREATE TABLE `customer_demand_history` (
  `demand_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `estimated_usage_kg` decimal(10,2) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `customer_notes`
--

CREATE TABLE `customer_notes` (
  `note_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `next_action` varchar(255) DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `customer_orders`
--

CREATE TABLE `customer_orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('request','preparing','delivering','done','canceled') DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `customer_payments`
--

CREATE TABLE `customer_payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `method` enum('cash','transfer','qris') DEFAULT NULL,
  `status` enum('pending','paid','failed') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `delivery_routes`
--

CREATE TABLE `delivery_routes` (
  `route_id` int(11) NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `vehicle` varchar(100) DEFAULT NULL,
  `route_date` date DEFAULT NULL,
  `status` enum('planned','ongoing','completed') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `delivery_route_points`
--

CREATE TABLE `delivery_route_points` (
  `point_id` int(11) NOT NULL,
  `route_id` int(11) DEFAULT NULL,
  `sales_order_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `sequence_number` int(11) DEFAULT NULL,
  `arrival_time` timestamp NULL DEFAULT NULL,
  `leave_time` timestamp NULL DEFAULT NULL,
  `status` enum('pending','delivered','failed') DEFAULT 'pending',
  `delivery_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `expense_categories`
--

CREATE TABLE `expense_categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('fixed','variable') DEFAULT 'fixed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `expense_categories`
--

INSERT INTO `expense_categories` (`category_id`, `name`, `type`) VALUES
(1, 'Gaji & Tunjangan', 'fixed'),
(2, 'Listrik, Air & Internet', 'fixed'),
(3, 'Sewa Kantor/Gudang', 'fixed'),
(4, 'Pemasaran & Iklan', 'variable'),
(5, 'Transportasi & Bensin', 'variable'),
(6, 'Perawatan Aset', 'variable'),
(7, 'ATK & Perlengkapan', 'variable'),
(8, 'Biaya Lain-lain', 'variable');

-- --------------------------------------------------------

--
-- Struktur dari tabel `operational_expenses`
--

CREATE TABLE `operational_expenses` (
  `expense_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `expense_date` date DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `purchases`
--

CREATE TABLE `purchases` (
  `purchase_id` int(11) NOT NULL,
  `purchase_no` varchar(50) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `total_cost` decimal(15,2) DEFAULT NULL,
  `total_paid` decimal(15,2) DEFAULT 0.00,
  `status` enum('ordered','received','canceled') DEFAULT 'ordered',
  `received_date` datetime DEFAULT NULL,
  `payment_status` enum('unpaid','partial','paid') DEFAULT 'unpaid',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` decimal(10,2) DEFAULT NULL,
  `cost` decimal(15,2) DEFAULT 0.00,
  `subtotal` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `purchase_payments`
--

CREATE TABLE `purchase_payments` (
  `payment_id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `route_gps_logs`
--

CREATE TABLE `route_gps_logs` (
  `gps_id` int(11) NOT NULL,
  `route_id` int(11) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `speed` double DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sales_orders`
--

CREATE TABLE `sales_orders` (
  `id` int(11) NOT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `salesman_id` int(11) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT NULL,
  `shipping_cost` decimal(15,2) DEFAULT 0.00,
  `other_discount` decimal(15,2) DEFAULT 0.00,
  `grand_total` decimal(15,2) DEFAULT 0.00,
  `total_paid` decimal(15,2) DEFAULT 0.00,
  `status` enum('request','preparing','delivering','done','canceled') DEFAULT 'request',
  `note` text DEFAULT NULL,
  `payment_status` enum('unpaid','partial','paid') DEFAULT 'unpaid',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sales_order_items`
--

CREATE TABLE `sales_order_items` (
  `id` int(11) NOT NULL,
  `sales_order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` decimal(10,2) DEFAULT NULL,
  `cost` decimal(15,2) DEFAULT 0.00,
  `price` decimal(15,2) DEFAULT NULL,
  `discount` decimal(15,2) DEFAULT 0.00,
  `subtotal` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sales_payments`
--

CREATE TABLE `sales_payments` (
  `payment_id` int(11) NOT NULL,
  `sales_order_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'Cash',
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `salt_products`
--

CREATE TABLE `salt_products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `type` enum('konsumsi','industri','artisan','bath_salt') DEFAULT NULL,
  `grade` varchar(100) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `last_purchase_price` varchar(20) DEFAULT NULL,
  `base_cost` varchar(20) DEFAULT NULL,
  `price` varchar(20) DEFAULT NULL,
  `sell_price` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `salt_products`
--

INSERT INTO `salt_products` (`product_id`, `name`, `type`, `grade`, `unit`, `last_purchase_price`, `base_cost`, `price`, `sell_price`) VALUES
(1, 'Garam Industri Krosok Premium', 'industri', 'Premium', 'kg', '2700.00', '2700', '0', '3800'),
(2, 'Garam Industri Krosok K1', 'industri', 'Medium', 'kg', '2400.00', '2400', '0', '3500'),
(3, 'Garam Industri Halus - PT. Garam', 'industri', 'Premium', 'kg', '0', '4500', '0', '5800'),
(5, 'Garam Industri Halus - Washing Unicham ', 'industri', 'Premium', 'kg', '0', '6000', '0', '7000'),
(6, 'Garam Konsumsi Cap Anak Sehat @250gr', 'konsumsi', 'Premium', 'pack', '0', '35000', '0', '37500'),
(7, 'Garam Konsumsi Cap Segitiga @250gr', 'konsumsi', 'Premium', 'pack', '0', '35000', '0', '38000'),
(8, 'Garam Krosok – Premium 1kg', 'industri', 'Premium', 'pack', '0', '3200', '0', '4000'),
(9, 'Garam Konsumsi - Bata GM 1kg', 'konsumsi', 'Premium', 'pack', '0', '6000', '0', '7000');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock_logs`
--

CREATE TABLE `stock_logs` (
  `log_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `change_qty` decimal(10,2) DEFAULT NULL,
  `type` enum('in','out') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(150) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `pic_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `contact_person`, `phone`, `address`, `pic_name`, `created_at`) VALUES
(1, 'PT Garam Nasional', NULL, '021-555666', 'Bantul, DI Yogyakarta', 'Pak Aan', '2025-12-06 10:30:38'),
(2, 'Bapak Sugiarto', NULL, '0895360929008', 'Rembang', 'Bapak Sugiarto', '2025-12-06 10:30:38'),
(3, 'Garam Rembang', NULL, '082135362838', 'Rembang', 'Pak Irul', '2025-12-06 15:16:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(200) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `role` enum('admin','sales','driver','gudang','owner') DEFAULT 'sales',
  `is_active` tinyint(1) DEFAULT 1,
  `password_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `phone`, `role`, `is_active`, `password_hash`, `created_at`) VALUES
(1, 'Owner IWL', 'owner@iwl.com', '081234567890', 'owner', 0, 'hash123', '2025-12-03 07:11:59'),
(2, 'Sales A', 'salesa@iwl.com', '081111111111', 'sales', 0, '$2y$10$2pKKMa5Ko1aNY6OBLCCk3eK7/yx3LoNqGT7jiijsPggQFtK8t9OoS', '2025-12-03 07:11:59'),
(3, 'Driver B', 'driverb@iwl.com', '082222222222', 'driver', 0, '$2y$10$KLuoZ44477Sd1YE60TI18.x1pzJschXTe6RDI2STBjjAW7lO0HMGm', '2025-12-03 07:11:59'),
(4, 'Indri Septiani', 'indriseptiani.iwl@gmail.com', '082135471707', 'admin', 0, '$2y$10$2LvJFjPQ8KWdCeQKpCB9peQtTvlKMYK49Gx/U90Qtc/IcFCOpPK.G', '2025-12-03 07:11:59'),
(5, 'Ahmad Asrori', 'insanwahanalestari@gmail.com', '082226922024', 'owner', 1, '$2y$10$lUhWePR.huv0NDqZPZwTn.rZMqn5l48ueikkp5691uHuFGwUEfRbS', '2025-12-08 03:34:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `warehouse_stock`
--

CREATE TABLE `warehouse_stock` (
  `stock_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `business_categories`
--
ALTER TABLE `business_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indeks untuk tabel `company_capital`
--
ALTER TABLE `company_capital`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeks untuk tabel `customer_demand_history`
--
ALTER TABLE `customer_demand_history`
  ADD PRIMARY KEY (`demand_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indeks untuk tabel `customer_notes`
--
ALTER TABLE `customer_notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `customer_orders`
--
ALTER TABLE `customer_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indeks untuk tabel `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indeks untuk tabel `delivery_routes`
--
ALTER TABLE `delivery_routes`
  ADD PRIMARY KEY (`route_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indeks untuk tabel `delivery_route_points`
--
ALTER TABLE `delivery_route_points`
  ADD PRIMARY KEY (`point_id`),
  ADD KEY `route_id` (`route_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indeks untuk tabel `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indeks untuk tabel `operational_expenses`
--
ALTER TABLE `operational_expenses`
  ADD PRIMARY KEY (`expense_id`);

--
-- Indeks untuk tabel `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`purchase_id`),
  ADD UNIQUE KEY `purchase_no` (`purchase_no`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indeks untuk tabel `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_id` (`purchase_id`);

--
-- Indeks untuk tabel `purchase_payments`
--
ALTER TABLE `purchase_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `purchase_id` (`purchase_id`);

--
-- Indeks untuk tabel `route_gps_logs`
--
ALTER TABLE `route_gps_logs`
  ADD PRIMARY KEY (`gps_id`),
  ADD KEY `route_id` (`route_id`);

--
-- Indeks untuk tabel `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`);

--
-- Indeks untuk tabel `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_order_id` (`sales_order_id`);

--
-- Indeks untuk tabel `sales_payments`
--
ALTER TABLE `sales_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `sales_order_id` (`sales_order_id`);

--
-- Indeks untuk tabel `salt_products`
--
ALTER TABLE `salt_products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indeks untuk tabel `stock_logs`
--
ALTER TABLE `stock_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `warehouse_stock`
--
ALTER TABLE `warehouse_stock`
  ADD PRIMARY KEY (`stock_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `business_categories`
--
ALTER TABLE `business_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `company_capital`
--
ALTER TABLE `company_capital`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `customer_demand_history`
--
ALTER TABLE `customer_demand_history`
  MODIFY `demand_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `customer_notes`
--
ALTER TABLE `customer_notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `customer_orders`
--
ALTER TABLE `customer_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `customer_payments`
--
ALTER TABLE `customer_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `delivery_routes`
--
ALTER TABLE `delivery_routes`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `delivery_route_points`
--
ALTER TABLE `delivery_route_points`
  MODIFY `point_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `operational_expenses`
--
ALTER TABLE `operational_expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `purchases`
--
ALTER TABLE `purchases`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `purchase_payments`
--
ALTER TABLE `purchase_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `route_gps_logs`
--
ALTER TABLE `route_gps_logs`
  MODIFY `gps_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sales_payments`
--
ALTER TABLE `sales_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `salt_products`
--
ALTER TABLE `salt_products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `stock_logs`
--
ALTER TABLE `stock_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `warehouse_stock`
--
ALTER TABLE `warehouse_stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `business_categories` (`category_id`);

--
-- Ketidakleluasaan untuk tabel `customer_demand_history`
--
ALTER TABLE `customer_demand_history`
  ADD CONSTRAINT `customer_demand_history_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Ketidakleluasaan untuk tabel `customer_notes`
--
ALTER TABLE `customer_notes`
  ADD CONSTRAINT `customer_notes_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `customer_notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `customer_orders`
--
ALTER TABLE `customer_orders`
  ADD CONSTRAINT `customer_orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `customer_orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `salt_products` (`product_id`),
  ADD CONSTRAINT `customer_orders_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD CONSTRAINT `customer_payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `customer_orders` (`order_id`),
  ADD CONSTRAINT `customer_payments_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Ketidakleluasaan untuk tabel `delivery_routes`
--
ALTER TABLE `delivery_routes`
  ADD CONSTRAINT `delivery_routes_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `users` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `delivery_route_points`
--
ALTER TABLE `delivery_route_points`
  ADD CONSTRAINT `delivery_route_points_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `delivery_routes` (`route_id`),
  ADD CONSTRAINT `delivery_route_points_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Ketidakleluasaan untuk tabel `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- Ketidakleluasaan untuk tabel `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `purchase_payments`
--
ALTER TABLE `purchase_payments`
  ADD CONSTRAINT `purchase_payments_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `route_gps_logs`
--
ALTER TABLE `route_gps_logs`
  ADD CONSTRAINT `route_gps_logs_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `delivery_routes` (`route_id`);

--
-- Ketidakleluasaan untuk tabel `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD CONSTRAINT `sales_order_items_ibfk_1` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sales_payments`
--
ALTER TABLE `sales_payments`
  ADD CONSTRAINT `sales_payments_ibfk_1` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `stock_logs`
--
ALTER TABLE `stock_logs`
  ADD CONSTRAINT `stock_logs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `salt_products` (`product_id`),
  ADD CONSTRAINT `stock_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `warehouse_stock`
--
ALTER TABLE `warehouse_stock`
  ADD CONSTRAINT `warehouse_stock_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `salt_products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
