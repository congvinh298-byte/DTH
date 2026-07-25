<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");

header('Content-Type: text/plain; charset=utf-8');

$DMH = new DMH;
$DMH->connect();

// 1. product_categories
$DMH->query("CREATE TABLE IF NOT EXISTS `product_categories` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `type` ENUM('dienmay','3d') NOT NULL DEFAULT 'dienmay',
    `sort_order` INT(11) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `slug_type` (`slug`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
echo "Bang product_categories OK\n";

// 2. products
$DMH->query("CREATE TABLE IF NOT EXISTS `products` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT(11) UNSIGNED DEFAULT 0,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `price` INT(11) DEFAULT 0,
    `stock` INT(11) DEFAULT 0,
    `image` VARCHAR(500) DEFAULT NULL,
    `type` ENUM('dienmay','3d') NOT NULL DEFAULT 'dienmay',
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `slug_type` (`slug`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
echo "Bang products OK\n";

// 2b. Ensure products columns exist (in case table was created earlier without all columns)
$productCols = [
    'category_id' => "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `category_id` INT(11) UNSIGNED DEFAULT 0",
    'name' => "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `name` VARCHAR(255) NOT NULL",
    'slug' => "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `slug` VARCHAR(255) NOT NULL",
    'description' => "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `description` TEXT DEFAULT NULL",
    'price' => "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `price` INT(11) DEFAULT 0",
    'stock' => "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `stock` INT(11) DEFAULT 0",
    'image' => "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `image` VARCHAR(500) DEFAULT NULL",
    'video' => "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `video` VARCHAR(500) DEFAULT NULL",
    'type' => "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `type` ENUM('dienmay','3d') NOT NULL DEFAULT 'dienmay'",
    'status' => "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `status` TINYINT(1) DEFAULT 1",
    'created_at' => "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
];
foreach ($productCols as $col => $sql) {
    try {
        $DMH->query($sql);
        echo "products cot $col OK\n";
    } catch (Throwable $e) {
        echo "products cot $col: " . $e->getMessage() . "\n";
    }
}

// 3. Ensure store_orders has needed columns
$storeCols = [
    'customer_name' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `customer_name` VARCHAR(255) DEFAULT NULL",
    'phone' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `phone` VARCHAR(20) DEFAULT NULL",
    'address' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `address` TEXT DEFAULT NULL",
    'total_amount' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `total_amount` INT(11) DEFAULT 0",
    'payment_method' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `payment_method` VARCHAR(50) DEFAULT 'COD'",
    'vat_requested' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `vat_requested` TINYINT(1) DEFAULT 0",
    'note' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `note` TEXT DEFAULT NULL",
    'status' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `status` VARCHAR(50) DEFAULT 'pending'",
    'created_at' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP",
    'shipper_name' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `shipper_name` VARCHAR(255) DEFAULT NULL",
    'shipper_phone' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `shipper_phone` VARCHAR(20) DEFAULT NULL",
    'tracking_code' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `tracking_code` VARCHAR(100) DEFAULT NULL",
    'shipped_at' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `shipped_at` DATETIME DEFAULT NULL",
    'delivered_at' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `delivered_at` DATETIME DEFAULT NULL",
    'delivery_note' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `delivery_note` TEXT DEFAULT NULL",
];
foreach ($storeCols as $col => $sql) {
    try {
        $DMH->query($sql);
        echo "store_orders cot $col OK\n";
    } catch (Throwable $e) {
        echo "store_orders cot $col: " . $e->getMessage() . "\n";
    }
}

// 4. Ensure store_order_items
$DMH->query("CREATE TABLE IF NOT EXISTS `store_order_items` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT(11) UNSIGNED NOT NULL,
    `product_id` INT(11) UNSIGNED DEFAULT 0,
    `product_name` VARCHAR(255) DEFAULT NULL,
    `quantity` INT(11) DEFAULT 1,
    `price` INT(11) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
echo "Bang store_order_items OK\n";

// 5. Ensure dat_lich has needed columns
$datLichCols = [
    'ten' => "ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `ten` VARCHAR(255) DEFAULT NULL",
    'sdt' => "ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `sdt` VARCHAR(20) DEFAULT NULL",
    'diachi' => "ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `diachi` TEXT DEFAULT NULL",
    'yeucau' => "ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `yeucau` TEXT DEFAULT NULL",
    'dichvu' => "ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `dichvu` VARCHAR(255) DEFAULT NULL",
    'thoigian' => "ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `thoigian` INT(11) DEFAULT 0",
    'trangthai' => "ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `trangthai` VARCHAR(50) DEFAULT 'CHO_XU_LY'",
    'tho_id' => "ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `tho_id` INT(11) DEFAULT 0",
];
foreach ($datLichCols as $col => $sql) {
    try {
        $DMH->query($sql);
        echo "dat_lich cot $col OK\n";
    } catch (Throwable $e) {
        echo "dat_lich cot $col: " . $e->getMessage() . "\n";
    }
}

// 6. Ensure users columns
$userCols = [
    'name' => "ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `name` VARCHAR(255) DEFAULT NULL",
    'fullname' => "ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `fullname` VARCHAR(255) DEFAULT NULL",
    'phone' => "ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `phone` VARCHAR(20) DEFAULT NULL",
    'level' => "ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `level` VARCHAR(50) DEFAULT 'user'",
];
foreach ($userCols as $col => $sql) {
    try {
        $DMH->query($sql);
        echo "users cot $col OK\n";
    } catch (Throwable $e) {
        echo "users cot $col: " . $e->getMessage() . "\n";
    }
}

// 7. Seed default product categories
$cats = [
    ['name' => 'TV', 'slug' => 'tv', 'type' => 'dienmay', 'sort_order' => 1],
    ['name' => 'Tu lanh', 'slug' => 'tu-lanh', 'type' => 'dienmay', 'sort_order' => 2],
    ['name' => 'Camera', 'slug' => 'camera', 'type' => 'dienmay', 'sort_order' => 3],
    ['name' => 'Gia dung', 'slug' => 'gia-dung', 'type' => 'dienmay', 'sort_order' => 4],
    ['name' => 'San pham 3D', 'slug' => 'san-pham-3d', 'type' => '3d', 'sort_order' => 1],
];
foreach ($cats as $c) {
    $ex = $DMH->get_row("SELECT id FROM `product_categories` WHERE `slug` = '{$c['slug']}' AND `type` = '{$c['type']}'");
    if (!$ex) {
        $DMH->insert('product_categories', $c);
        echo "Them danh muc: {$c['name']}\n";
    } else {
        echo "Danh muc ton tai: {$c['name']}\n";
    }
}

// 8. Refresh admin_menus to new structure
$DMH->query("CREATE TABLE IF NOT EXISTS `admin_menus` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `parent_id` INT(11) UNSIGNED DEFAULT 0,
    `title` VARCHAR(255) NOT NULL,
    `url` VARCHAR(500) NOT NULL,
    `icon` VARCHAR(100) DEFAULT '',
    `level_required` VARCHAR(50) DEFAULT 'admin',
    `sort_order` INT(11) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Clear old menus and insert new
$DMH->query("DELETE FROM `admin_menus`");

$menus = [
    ['parent_id' => 0, 'title' => 'Dashboard', 'url' => '/Admin', 'icon' => 'fa-gauge', 'sort_order' => 0],
    ['parent_id' => 0, 'title' => 'Don hang san pham', 'url' => '/pages/admin/QuanLyDonHang.php', 'icon' => 'fa-box', 'sort_order' => 1],
    ['parent_id' => 0, 'title' => 'Don goi tho', 'url' => '/pages/admin/QuanLyDatLich.php', 'icon' => 'fa-calendar-check', 'sort_order' => 2],
    ['parent_id' => 0, 'title' => 'Khach hang', 'url' => '/pages/admin/QuanLyKhachHang.php', 'icon' => 'fa-users', 'sort_order' => 3],
    ['parent_id' => 0, 'title' => 'Tho', 'url' => '/pages/admin/QuanLyTho.php', 'icon' => 'fa-wrench', 'sort_order' => 4],
    ['parent_id' => 0, 'title' => 'Gian hang dien may', 'url' => '/pages/admin/GianHangDienMay.php', 'icon' => 'fa-plug', 'sort_order' => 5],
    ['parent_id' => 0, 'title' => 'Gian hang 3D', 'url' => '/pages/admin/GianHang3D.php', 'icon' => 'fa-cube', 'sort_order' => 6],
    ['parent_id' => 0, 'title' => 'Cai dat', 'url' => '/pages/admin/CaiDat.php', 'icon' => 'fa-gear', 'sort_order' => 7],
    ['parent_id' => 0, 'title' => 'Bao cao', 'url' => '/pages/admin/BaoCao.php', 'icon' => 'fa-chart-line', 'sort_order' => 8],
];
foreach ($menus as $m) {
    $DMH->insert('admin_menus', $m);
    echo "Them menu: {$m['title']}\n";
}

// 9. Create demo worker if missing
$demo = $DMH->get_row("SELECT id FROM `users` WHERE `username` = 'tho_demo'");
if (!$demo) {
    $DMH->insert('users', [
        'username' => 'tho_demo',
        'password' => md5('123456'),
        'name' => 'Tho sua chua (Demo)',
        'fullname' => 'Tho sua chua (Demo)',
        'level' => 'tho',
        'banned' => 'ON',
        'money' => 0,
        'verify' => 0,
    ]);
    echo "Tao tho demo: tho_demo / 123456\n";
} else {
    echo "Tho demo da ton tai\n";
}

echo "\nXong setup DTH admin v2.\n";
