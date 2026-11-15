-- ========================================================
-- SISTEM INFORMASI PEMESANAN & PEMBAYARAN WIJAYA BARBERSHOP
-- File: seed.sql (Data Awal Sesuai Dokumen Skripsi + Home Service)
-- Password default semua akun: password123
-- ========================================================

USE `wijaya_barbershop`;

-- 1. Insert Roles
INSERT INTO `roles` (`id`, `role_name`, `description`) VALUES
(1, 'admin', 'Administrator / Direktur Utama Wijaya Barbershop'),
(2, 'kasir', 'Kasir Pengelola Transaksi dan Pembayaran'),
(3, 'barber', 'Capster / Barber Pemotong Rambut'),
(4, 'customer', 'Pelanggan Wijaya Barbershop')
ON DUPLICATE KEY UPDATE `role_name` = VALUES(`role_name`);

-- 2. Insert Users (Struktur Organisasi Gambar 15 & Data Uji)
-- Default bcrypt untuk 'password123': $2y$10$wgzK3OvXZkbz4KuKUPXUbOd9FkqZf0z2OOhtXR42sb70dftmzJNK2
INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `email`, `phone`, `on_duty`, `role_id`) VALUES
(1, 'agung', '$2y$10$wgzK3OvXZkbz4KuKUPXUbOd9FkqZf0z2OOhtXR42sb70dftmzJNK2', 'Agung Wijaya', 'agung@wijayabarber.com', '081278410001', 1, 1),
(2, 'mely', '$2y$10$wgzK3OvXZkbz4KuKUPXUbOd9FkqZf0z2OOhtXR42sb70dftmzJNK2', 'Mely (Kasir 1)', 'mely@wijayabarber.com', '081278410002', 1, 2),
(3, 'riles', '$2y$10$wgzK3OvXZkbz4KuKUPXUbOd9FkqZf0z2OOhtXR42sb70dftmzJNK2', 'Riles (Kasir 2)', 'riles@wijayabarber.com', '081278410003', 1, 2),
(4, 'doni', '$2y$10$wgzK3OvXZkbz4KuKUPXUbOd9FkqZf0z2OOhtXR42sb70dftmzJNK2', 'Doni (Capster Senior)', 'doni@wijayabarber.com', '081278410004', 1, 3),
(5, 'lana', '$2y$10$wgzK3OvXZkbz4KuKUPXUbOd9FkqZf0z2OOhtXR42sb70dftmzJNK2', 'Lana (Capster Hair Tattoo)', 'lana@wijayabarber.com', '081278410005', 1, 3),
(6, 'yoyo', '$2y$10$wgzK3OvXZkbz4KuKUPXUbOd9FkqZf0z2OOhtXR42sb70dftmzJNK2', 'Yoyo (Capster Classic Cut)', 'yoyo@wijayabarber.com', '081278410006', 1, 3),
(7, 'juni', '$2y$10$wgzK3OvXZkbz4KuKUPXUbOd9FkqZf0z2OOhtXR42sb70dftmzJNK2', 'Juni Sanjaya', 'junisanjaya2026@gmail.com', '081278412312', 0, 4),
(8, 'budi', '$2y$10$wgzK3OvXZkbz4KuKUPXUbOd9FkqZf0z2OOhtXR42sb70dftmzJNK2', 'Budi Santoso', 'budi@gmail.com', '081298765432', 0, 4),
(9, 'direktur', '$2y$10$wgzK3OvXZkbz4KuKUPXUbOd9FkqZf0z2OOhtXR42sb70dftmzJNK2', 'Direktur Utama', 'direktur@wijayabarber.com', '081278410000', 1, 1),
(10, 'reza', '$2y$10$wgzK3OvXZkbz4KuKUPXUbOd9FkqZf0z2OOhtXR42sb70dftmzJNK2', 'Reza Pratama', 'reza@gmail.com', '081935975336', 0, 4)
ON DUPLICATE KEY UPDATE `fullname` = VALUES(`fullname`);

-- 3. Insert Categories
INSERT INTO `categories` (`id`, `category_name`, `description`) VALUES
(1, 'Jasa Pangkas Rambut', 'Layanan potong rambut pria profesional mulai dari model klasik hingga gaya modern'),
(2, 'Perawatan & Treatment Rambut', 'Layanan cuci rambut, creambath, pijat relaksasi kepala, dan semir pewarnaan rambut'),
(3, 'Produk Rambut & Styling', 'Produk minyak rambut pomade, matte clay, dan tonik vitamin penumbuh rambut')
ON DUPLICATE KEY UPDATE `category_name` = VALUES(`category_name`);

-- 4. Insert Items (Jasa & Produk Sesuai Dokumen Skripsi)
INSERT INTO `items` (`id`, `item_name`, `description`, `price`, `category_id`, `image_path`, `is_active`) VALUES
(1, 'Potong Rambut Dewasa', 'Layanan pangkas rambut pria dewasa rapi dan presisi sesuai bentuk wajah + pembersihan rambut & styling pomade.', 1000, 1, 'haircut-regular.jpg', 1),
(2, 'Paket Potong Rambut Lengkap (Cuci + Pijat)', 'Pangkas rambut eksklusif + keramas cuci rambut air hangat + pijat kepala relaksasi + handuk dingin & hair tonic.', 1500, 1, 'haircut-premium.jpg', 1),
(3, 'Potong Rambut Anak & Balita', 'Layanan potong rambut khusus anak-anak dan balita dengan pelayanan ramah, sabar, dan rapi.', 1000, 1, 'haircut-kids.jpg', 1),
(4, 'Cukur Kumis & Jenggot (Shaving)', 'Cukur jenggot dan kumis presisi dengan busa hangat, pisau cukur steril, dan aftershave dingin menyegarkan.', 1000, 1, 'shave.jpg', 1),
(5, 'Hair Tattoo / Ukir Rambut Artistik', 'Seni ukir garis pola dan motif kreatif pada rambut pria (keahlian khusus Capster Lana).', 1500, 1, 'hair-tattoo.jpg', 1),
(6, 'Semir Rambut / Pewarnaan (Hair Color)', 'Layanan pewarnaan rambut pria dengan warna natural (hitam pekat, cokelat tua) maupun fashion bleach aman bagi kulit kepala.', 1500, 2, 'hair-color.jpg', 1),
(7, 'Cuci Rambut & Creambath (Hair Spa)', 'Perawatan nutrisi akar rambut dengan krim creambath, pijatan relaksasi kepala melancarkan sirkulasi darah dan basmi ketombe.', 1200, 2, 'creambath.jpg', 1),
(8, 'Pomade Rambut Wijaya (Water Based)', 'Produk minyak rambut pomade water based aroma segar maskulin, daya rekat tinggi (strong hold), berkilau alami, dan mudah dibilas.', 1500, 3, 'pomade-water.jpg', 1),
(9, 'Clay Rambut (Matte Clay Hold)', 'Produk penata rambut dengan hasil akhir tanpa kilau (matte finish), memberikan volume dan tekstur alami untuk gaya rambut modern.', 1500, 3, 'clay.jpg', 1),
(10, 'Hair Tonic Ginseng (Vitamin Rambut)', 'Tonik rambut dengan ekstrak ginseng alami untuk menguatkan akar rambut, merangsang pertumbuhan rambut sehat, dan sensasi dingin.', 1000, 3, 'tonic.jpg', 1)
ON DUPLICATE KEY UPDATE `item_name` = VALUES(`item_name`);

-- 5. Insert Sample Orders (Sesuai Screenshot Tabel Data Order Gambar 34 + Sample Home Service)
INSERT INTO `orders` (`id`, `order_code`, `user_id`, `subtotal`, `discount`, `tax`, `grand_total`, `status`, `payment_method`, `service_type`, `barber_id`, `customer_address`, `area_zone`, `delivery_fee`, `scheduled_at`, `notes`, `queue_number`, `queue_time`, `created_at`) VALUES
(1, 'ORD1778413577', 7, '88457', '0', '8846', '97303', 'settlement', 'qris', 'barbershop', 4, NULL, NULL, 0, NULL, 'Fade samping tipis nomor 1, atas rapikan sedikit', 1, '2026-11-06 10:00:00', '2026-11-06 09:15:00'),
(2, 'ORD1778413495', 8, '38732', '0', '3873', '42605', 'pending', 'tunai', 'barbershop', 5, NULL, NULL, 0, NULL, 'Minta barber mas Doni kalau ada', 2, '2026-11-06 10:30:00', '2026-11-06 09:30:00'),
(3, 'ORD1778412856', 7, '37728', '0', '3773', '41501', 'pending', 'tunai', 'barbershop', 6, NULL, NULL, 0, NULL, 'Potong cepak rapi dinas', 3, '2026-11-06 11:00:00', '2026-11-06 09:45:00'),
(4, 'ORD1778412628', 8, '2039', '0', '204', '2243', 'pending', 'tunai', 'barbershop', 4, NULL, NULL, 0, NULL, 'Beli produk styling tambahan', 4, '2026-11-06 11:30:00', '2026-11-06 10:00:00'),
(5, 'ORD1778409174', 7, '37728', '0', '3773', '41501', 'settlement', 'tunai', 'barbershop', 5, NULL, NULL, 0, NULL, 'Potong rambut + cuci', 5, '2026-11-06 12:00:00', '2026-11-06 10:15:00'),
(6, 'ORD1778419901', 7, '50000', '0', '5000', '65000', 'settlement', 'qris', 'home_service', 4, 'Jl. AH Nasution No. 45, Yosodadi, Kec. Metro Timur (Rumah pagar hitam sebelah apotek)', 'Metro Timur / Barat / Utara / Selatan', 10000, '2026-11-06 15:00:00', 'Tolong bawa peralatan lengkap & kain penutup, potong model French Crop', 6, '2026-11-06 15:00:00', '2026-11-06 13:00:00')
ON DUPLICATE KEY UPDATE `order_code` = VALUES(`order_code`);

-- 6. Insert Order Items
INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `quantity`, `price`, `tax`, `total_price`) VALUES
(1, 1, 2, 1, 50000, 5000, 55000),
(2, 1, 7, 1, 38457, 3846, 42303),
(3, 2, 1, 1, 35000, 3500, 38500),
(4, 3, 1, 1, 35000, 3500, 38500),
(5, 5, 2, 1, 50000, 5000, 55000),
(6, 6, 2, 1, 50000, 5000, 55000)
ON DUPLICATE KEY UPDATE `order_id` = VALUES(`order_id`);
