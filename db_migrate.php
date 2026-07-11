<?php
/**
 * 🚀 DATABASE MIGRATION SCRIPT (TECHNICIAN IDENTITY)
 * Tạo bởi Antigravity Agent
 */
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");

if (!isset($DMH) || $DMH === null) {
    $DMH = new DMH();
}

echo "<html><head><title>Database Migration</title><style>body { font-family: 'Inter', sans-serif; background: #0f172a; color: #e2e8f0; padding: 40px; } .box { background: #1e293b; padding: 20px; border-radius: 10px; margin-bottom: 20px; } .ok { color: #10b981; font-weight:bold; } .warn { color: #f59e0b; } .err { color: #ef4444; } h1 { color: #38bdf8; } </style></head><body>";
echo "<h1>🚀 Đồng Bộ Cấu Trúc Cơ Sở Dữ Liệu</h1>";
echo "<p>Hệ thống Đăng Nhập & Quản lý Thợ Điện Máy Hiếu</p>";

// Tắt report exception của mysqli để xử lý bằng try-catch tùy chỉnh (tránh Fatal Error làm sập trang)
mysqli_report(MYSQLI_REPORT_OFF);

try {

// Function to safely execute altering tables
function add_column_safe($DMH, $table, $column, $def) {
    $check_col = $DMH->get_row("SHOW COLUMNS FROM `$table` LIKE '$column'");
    if ($check_col) {
        echo "<div class='ok'>✔ Bảng $table: Cột <b>$column</b> đã tồn tại</div>";
    } else {
        $sql = "ALTER TABLE `$table` ADD COLUMN `$column` $def";
        if ($DMH->query($sql)) {
            echo "<div class='ok'>✔ Bảng $table: Đã thêm cột <b>$column</b></div>";
        } else {
            echo "<div class='warn'>⚠ Bảng $table: Lỗi thêm cột <b>$column</b></div>";
        }
    }
}

// --- 1. USERS Table ---
echo "<div class='box'><h3>1. Chuẩn Hóa Bảng 'users'</h3>";
$sql_create_users = "CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if ($DMH->query($sql_create_users)) {
    echo "<div class='ok'>✔ Đảm bảo bảng `users` tồn tại</div>";
}

$columns_users = [
    'username' => 'varchar(255) DEFAULT NULL',
    'password' => 'varchar(255) DEFAULT NULL',
    'name' => 'varchar(255) DEFAULT NULL',
    'level' => "varchar(50) DEFAULT 'user'",
    'tokenlog' => 'varchar(255) DEFAULT NULL',
    'banned' => "varchar(10) DEFAULT 'ON'",
    'money' => "int(11) DEFAULT '0'",
    'verify' => "int(1) DEFAULT '0'",
    'timeon' => 'datetime DEFAULT NULL',
    'online' => "varchar(25) DEFAULT 'OFFLINE'",
    'user_agent' => 'text',
    'ip' => 'varchar(255) DEFAULT NULL',
    'phone' => 'varchar(20) DEFAULT NULL',
    'address' => 'text DEFAULT NULL',
    'points' => "int(11) DEFAULT '0'",
    'first_login' => "int(1) DEFAULT '1'"
];

foreach ($columns_users as $col => $def) {
    add_column_safe($DMH, 'users', $col, $def);
}
echo "</div>";

// --- 2. TECHNICIANS_ROLES Table ---
echo "<div class='box'><h3>2. Khởi tạo 'technicians_roles'</h3>";
$sql_roles = "CREATE TABLE IF NOT EXISTS `technicians_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if ($DMH->query($sql_roles)) {
    echo "<div class='ok'>✔ Bảng technicians_roles đã sẵn sàng.</div>";
}
echo "</div>";

// --- 3. PARTNERS Table ---
echo "<div class='box'><h3>3. Khởi tạo 'partners' (Chi tiết Thợ)</h3>";
$sql_partners = "CREATE TABLE IF NOT EXISTS `partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT '5.00',
  `status` varchar(20) DEFAULT 'ACTIVE',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if ($DMH->query($sql_partners)) {
    echo "<div class='ok'>✔ Bảng partners đã sẵn sàng.</div>";
}
echo "</div>";

// --- 4. AUTH_LOGS Table ---
echo "<div class='box'><h3>4. Khởi tạo 'auth_logs' (Bảo mật)</h3>";
$sql_auth_logs = "CREATE TABLE IF NOT EXISTS `auth_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if ($DMH->query($sql_auth_logs)) {
    echo "<div class='ok'>✔ Bảng auth_logs đã sẵn sàng.</div>";
}
echo "</div>";

// --- 5. Fix dat_lich ---
echo "<div class='box'><h3>5. Fix Bảng dat_lich</h3>";
add_column_safe($DMH, 'dat_lich', 'tho_id', "int(11) DEFAULT '0'");
add_column_safe($DMH, 'dat_lich', 'trangthai', "varchar(50) DEFAULT 'CHO_XU_LY'");
echo "</div>";

// --- 6. DEMO ACCOUNT ---
echo "<div class='box'><h3>6. Tài khoản Thợ Demo</h3>";
$check_demo = $DMH->get_row("SELECT * FROM `users` WHERE `username` = 'tho_demo'");
if (!$check_demo) {
    $DMH->insert("users", [
        'username' => 'tho_demo',
        'password' => md5('123456'),
        'name'     => 'Thợ Sửa Chữa (Demo)',
        'level'    => 'tho',
        'banned'   => 'ON'
    ]);
    echo "<div class='ok'>✔ Đã tạo tài khoản thợ demo: tho_demo / 123456</div>";
} else {
    $DMH->update("users", ['password' => md5('123456'), 'level' => 'tho', 'banned' => 'ON'], "`username` = 'tho_demo'");
    echo "<div class='ok'>✔ Đã reset/cập nhật tài khoản thợ demo: tho_demo / 123456</div>";
}
echo "</div>";

// --- 7. ADMIN ACCOUNT ---
echo "<div class='box'><h3>7. Tài khoản Giám đốc (Admin)</h3>";
$check_admin = $DMH->get_row("SELECT * FROM `users` WHERE `username` = 'admin'");
if (!$check_admin) {
    $DMH->query("ALTER TABLE `users` ADD `face_descriptor` TEXT NULL;");

    $DMH->insert("users", [
        'username' => 'admin',
        'password' => md5('Anhthien369@'),
        'name'     => 'Giám Đốc (Admin)',
        'level'    => 'admin',
        'banned'   => 'ON'
    ]);
    echo "<div class='ok'>✔ Đã tạo tài khoản Admin: admin / Anhthien369@</div>";
} else {
    $DMH->update("users", ['password' => md5('Anhthien369@'), 'level' => 'admin', 'banned' => 'ON'], "`username` = 'admin'");
    echo "<div class='ok'>✔ Đã reset/cập nhật tài khoản Admin: admin / Anhthien369@</div>";
}
echo "</div>";

// --- 8. STORE_PRODUCTS Table ---
echo "<div class='box'><h3>8. Khởi tạo Bảng 'store_products' (Hàng hóa & In 3D)</h3>";
$sql_products = "CREATE TABLE IF NOT EXISTS `store_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL DEFAULT 'dienmay',
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL DEFAULT '0',
  `image` text,
  `description` text,
  `status` varchar(20) DEFAULT 'ACTIVE',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if ($DMH->query($sql_products)) {
    echo "<div class='ok'>✔ Bảng store_products đã sẵn sàng.</div>";
}
echo "</div>";

// --- 9. STORE_CARTS Table ---
echo "<div class='box'><h3>9. Khởi tạo Bảng 'store_carts' (Giỏ hàng)</h3>";
$sql_carts = "CREATE TABLE IF NOT EXISTS `store_carts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if ($DMH->query($sql_carts)) {
    echo "<div class='ok'>✔ Bảng store_carts đã sẵn sàng.</div>";
}
echo "</div>";

// --- 10. STORE_ORDERS Table ---
echo "<div class='box'><h3>10. Khởi tạo Bảng 'store_orders' (Đơn hàng)</h3>";
$sql_orders = "CREATE TABLE IF NOT EXISTS `store_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT 'COD',
  `customer_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `note` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if ($DMH->query($sql_orders)) {
    echo "<div class='ok'>✔ Bảng store_orders đã sẵn sàng.</div>";
}
echo "</div>";

// --- 11. STORE_ORDER_ITEMS Table ---
echo "<div class='box'><h3>11. Khởi tạo Bảng 'store_order_items' (Chi tiết đơn hàng)</h3>";
$sql_order_items = "CREATE TABLE IF NOT EXISTS `store_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if ($DMH->query($sql_order_items)) {
    echo "<div class='ok'>✔ Bảng store_order_items đã sẵn sàng.</div>";
}
echo "</div>";

echo "<h2>🎉 Hoàn tất quá trình đồng bộ (Migration)!</h2>";
echo "<a href='/login.php' style='display:inline-block; margin-top: 15px; padding: 12px 24px; background: #3b82f6; color: white; border-radius: 8px; font-weight: bold; text-decoration: none;'>Về Trang Đăng Nhập Thợ</a>";

} catch (Throwable $e) {
    echo "<div class='err'><h3>❌ Lỗi nghiêm trọng (Fatal Error):</h3>";
    echo "<pre style='background:#222; padding: 15px; color:#f87171; overflow-x:auto;'>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "</body></html>";
?>
