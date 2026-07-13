<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");

header('Content-Type: text/plain');

$DMH = new DMH;
$DMH->connect();

// 1. Tạo bảng admin_menus
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

echo "Bang admin_menus da tao/chinh sua.\n";

// 2. Seed menu m?c d?nh
$menus = [
    ['parent_id' => 0, 'title' => 'Dashboard', 'url' => '/Admin', 'icon' => 'entypo-gauge', 'sort_order' => 0],
    ['parent_id' => 0, 'title' => 'Quản lý đơn hàng', 'url' => '/pages/admin/QuanLyDonHang.php', 'icon' => 'entypo-basket', 'sort_order' => 1],
    ['parent_id' => 0, 'title' => 'Thành viên', 'url' => '#', 'icon' => 'entypo-user', 'sort_order' => 2],
    ['parent_id' => 0, 'title' => 'Danh mục tạo website', 'url' => '#', 'icon' => 'entypo-layout', 'sort_order' => 3],
    ['parent_id' => 0, 'title' => 'Danh mục bán code', 'url' => '#', 'icon' => 'entypo-layout', 'sort_order' => 4],
    ['parent_id' => 0, 'title' => 'Lịch sử', 'url' => '#', 'icon' => 'entypo-back', 'sort_order' => 5],
    ['parent_id' => 0, 'title' => 'Hóa đơn', 'url' => '#', 'icon' => 'entypo-window', 'sort_order' => 6],
    ['parent_id' => 0, 'title' => 'Mã giảm giá', 'url' => '/Admin/Magiamgia', 'icon' => 'entypo-share', 'sort_order' => 7],
    ['parent_id' => 0, 'title' => 'Quản lý API', 'url' => '/Admin/Quanlyapi', 'icon' => 'entypo-code', 'sort_order' => 8],
    ['parent_id' => 0, 'title' => 'Block IP', 'url' => '/Admin/Blockip', 'icon' => 'entypo-block', 'sort_order' => 9],
    ['parent_id' => 0, 'title' => 'Tạo sự kiện', 'url' => '/Admin/Sukien', 'icon' => 'entypo-tag', 'sort_order' => 10],
    ['parent_id' => 0, 'title' => 'Cài đặt', 'url' => '/Admin/SettingAdmin', 'icon' => 'entypo-cog', 'sort_order' => 11],
    ['parent_id' => 0, 'title' => 'Cấu hình CLF', 'url' => '/Admin/SettingClf', 'icon' => 'entypo-cog', 'sort_order' => 12],
    ['parent_id' => 0, 'title' => 'Cộng tác viên', 'url' => '#', 'icon' => 'entypo-users', 'sort_order' => 13],
];

foreach ($menus as $menu) {
    $exists = $DMH->get_row("SELECT id FROM `admin_menus` WHERE `url` = '{$menu['url']}' AND `title` = '{$menu['title']}'");
    if (!$exists) {
        $DMH->insert('admin_menus', $menu);
        echo "Them menu: {$menu['title']}\n";
    } else {
        echo "Menu da ton tai: {$menu['title']}\n";
    }
}

// Submenu
$parentMap = [
    'Thành viên' => [
        ['title' => 'Tổng thành viên', 'url' => '/Admin/Quanlythanhvien', 'sort_order' => 0],
        ['title' => 'Thành viên bị khóa', 'url' => '/Admin/QuanlythanhvienKhoa', 'sort_order' => 1],
        ['title' => 'Thành viên đang ONLINE', 'url' => '/Admin/ThanhVienON', 'sort_order' => 2],
    ],
    'Danh mục tạo website' => [
        ['title' => 'Quản lý danh mục', 'url' => '/Admin/Danhmuctaoweb', 'sort_order' => 0],
        ['title' => 'Đăng mẫu website', 'url' => '/Admin/ThemMauWeb', 'sort_order' => 1],
    ],
    'Danh mục bán code' => [
        ['title' => 'Quản lý danh mục', 'url' => '/Admin/Danhmucbancode', 'sort_order' => 0],
        ['title' => 'Đăng bán code', 'url' => '/Admin/ThemMaNguon', 'sort_order' => 1],
    ],
    'Lịch sử' => [
        ['title' => 'Lịch sử chuyển tiền', 'url' => '/Admin/HistoryChuyentien', 'sort_order' => 0],
        ['title' => 'Biến động số dư', 'url' => '/Admin/HistoryBiendongsodu', 'sort_order' => 1],
        ['title' => 'Đơn gia hạn website', 'url' => '/Admin/Lichsugiahan', 'sort_order' => 2],
        ['title' => 'Lịch sử tạo website', 'url' => '/Admin/Quanlytaoweb', 'sort_order' => 3],
        ['title' => 'Lịch sử mua miền', 'url' => '/Admin/Quanlymuamien', 'sort_order' => 4],
        ['title' => 'Lịch sử nạp thẻ', 'url' => '/Admin/Lichsunaptien', 'sort_order' => 5],
        ['title' => 'Lịch sử nạp ATM', 'url' => '/Admin/LichsunaptienATM', 'sort_order' => 6],
        ['title' => 'Lịch sử mua Code', 'url' => '/Admin/Lichsumuacode', 'sort_order' => 7],
        ['title' => 'Hồ sơ xác minh', 'url' => '/Admin/HosoXacMinh', 'sort_order' => 8],
    ],
    'Hóa đơn' => [
        ['title' => 'Hóa đơn TSR', 'url' => '/Admin/Hoadontsr', 'sort_order' => 0],
    ],
    'Cộng tác viên' => [
        ['title' => 'Mã nguồn đăng bán', 'url' => '/Admin/Par_manguon', 'sort_order' => 0],
        ['title' => 'Rút tiền', 'url' => '/Admin/Par_ruttien', 'sort_order' => 1],
        ['title' => 'Biến động số dư', 'url' => '/Admin/Par_biendongsodu', 'sort_order' => 2],
    ],
];

foreach ($parentMap as $parentTitle => $children) {
    $parent = $DMH->get_row("SELECT id FROM `admin_menus` WHERE `title` = '$parentTitle' AND `parent_id` = 0");
    if ($parent) {
        foreach ($children as $child) {
            $child['parent_id'] = $parent['id'];
            $child['icon'] = '';
            $child['level_required'] = 'admin';
            $exists = $DMH->get_row("SELECT id FROM `admin_menus` WHERE `url` = '{$child['url']}' AND `parent_id` = {$parent['id']}");
            if (!$exists) {
                $DMH->insert('admin_menus', $child);
                echo "Them submenu {$parentTitle}: {$child['title']}\n";
            } else {
                echo "Submenu da ton tai {$parentTitle}: {$child['title']}\n";
            }
        }
    }
}

// 3. Thêm c?t giao hng vo store_orders
$columns = [
    'shipper_name' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `shipper_name` VARCHAR(255) DEFAULT NULL",
    'shipper_phone' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `shipper_phone` VARCHAR(20) DEFAULT NULL",
    'tracking_code' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `tracking_code` VARCHAR(100) DEFAULT NULL",
    'shipped_at' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `shipped_at` DATETIME DEFAULT NULL",
    'delivered_at' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `delivered_at` DATETIME DEFAULT NULL",
    'delivery_note' => "ALTER TABLE `store_orders` ADD COLUMN IF NOT EXISTS `delivery_note` TEXT DEFAULT NULL",
];

foreach ($columns as $col => $sql) {
    try {
        $DMH->query($sql);
        echo "Cot $col: OK\n";
    } catch (Throwable $e) {
        echo "Cot $col: " . $e->getMessage() . "\n";
    }
}

echo "\nXong.\n";
?>
