<?php
/**
 * 🚀 DATABASE MIGRATION SCRIPT (TECHNICIAN IDENTITY)
 * Tạo bởi Antigravity Agent
 */
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");

echo "<html><head><title>Database Migration</title><style>body { font-family: 'Inter', sans-serif; background: #0f172a; color: #e2e8f0; padding: 40px; } .box { background: #1e293b; padding: 20px; border-radius: 10px; margin-bottom: 20px; } .ok { color: #10b981; font-weight:bold; } .warn { color: #f59e0b; } .err { color: #ef4444; } h1 { color: #38bdf8; } </style></head><body>";
echo "<h1>🚀 Đồng Bộ Cấu Trúc Cơ Sở Dữ Liệu</h1>";
echo "<p>Hệ thống Đăng Nhập & Quản lý Thợ Điện Máy Hiếu</p>";

// Tắt report exception của mysqli để xử lý bằng try-catch tùy chỉnh (tránh Fatal Error làm sập trang)
mysqli_report(MYSQLI_REPORT_OFF);

// Function to safely execute altering tables
function add_column_safe($DMH, $table, $column, $def) {
    $sql = "ALTER TABLE `$table` ADD COLUMN `$column` $def";
    $res = @mysqli_query($DMH->ketnoi, $sql);
    if ($res) {
        echo "<div class='ok'>✔ Bảng $table: Đã thêm cột <b>$column</b></div>";
    } else {
        $err = mysqli_error($DMH->ketnoi);
        if (strpos($err, 'Duplicate column name') !== false) {
            echo "<div class='ok'>✔ Bảng $table: Cột <b>$column</b> đã tồn tại</div>";
        } else {
            echo "<div class='warn'>⚠ Bảng $table: Lỗi thêm cột <b>$column</b> - $err</div>";
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
    'verify' => "int(1) DEFAULT '0'"
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
echo "<div class='box'><h3>5. Chuẩn hóa bảng 'dat_lich'</h3>";
add_column_safe($DMH, 'dat_lich', 'tho_id', "int(11) DEFAULT '0'");
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

echo "<h2>🎉 Hoàn tất quá trình đồng bộ (Migration)!</h2>";
echo "<a href='/login.php' style='display:inline-block; margin-top: 15px; padding: 12px 24px; background: #3b82f6; color: white; border-radius: 8px; font-weight: bold; text-decoration: none;'>Về Trang Đăng Nhập Thợ</a>";
echo "</body></html>";
?>
