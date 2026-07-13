<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");

echo "<h1>🛠 Cài đặt Hệ thống Quản Lý Gọi Thợ</h1>";

// 1. Tạo bảng dat_lich nếu chưa có
$sql_dat_lich = "CREATE TABLE IF NOT EXISTS `dat_lich` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ten` varchar(255) NOT NULL,
  `sdt` varchar(20) NOT NULL,
  `diachi` text NOT NULL,
  `yeucau` text,
  `dichvu` varchar(255) DEFAULT NULL,
  `thoigian` int(11) NOT NULL,
  `trangthai` varchar(50) DEFAULT 'CHO_XU_LY',
  `tho_id` int(11) DEFAULT '0',
  `phatsinh_mota` text DEFAULT NULL,
  `phatsinh_gia` int(11) DEFAULT '0',
  `phatsinh_duyet` tinyint(1) DEFAULT '0',
  `nghiemthu_note` text DEFAULT NULL,
  `nghiemthu_anh` text DEFAULT NULL,
  `danhgia_sao` tinyint(1) DEFAULT NULL,
  `danhgia_noidung` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if ($DMH->query($sql_dat_lich)) {
    echo "<p style='color:green'>✔ Bảng dat_lich đã sẵn sàng.</p>";

    // Thêm các cột mới nếu chưa có
    $cols = [
        'phatsinh_mota' => "ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `phatsinh_mota` text DEFAULT NULL",
        'phatsinh_gia' => "ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `phatsinh_gia` int(11) DEFAULT '0'",
        'phatsinh_duyet' => "ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `phatsinh_duyet` tinyint(1) DEFAULT '0'",
        'nghiemthu_note' => "ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `nghiemthu_note` text DEFAULT NULL",
        'nghiemthu_anh' => "ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `nghiemthu_anh` text DEFAULT NULL",
        'danhgia_sao' => "ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `danhgia_sao` tinyint(1) DEFAULT NULL",
        'danhgia_noidung' => "ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `danhgia_noidung` text DEFAULT NULL"
    ];
    foreach ($cols as $col => $sql) {
        $DMH->query($sql);
    }
} else {
    echo "<p style='color:red'>✖ Lỗi tạo bảng dat_lich.</p>";
}

// 2. Đảm bảo bảng users có các cột cần thiết (password, username, level, tokenlog, name)
// Nếu hệ thống đã có bảng users, ta bỏ qua hoặc tạo nếu chưa có.
$sql_users = "CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `level` varchar(50) DEFAULT 'user',
  `tokenlog` varchar(255) DEFAULT NULL,
  `banned` varchar(10) DEFAULT 'ON',
  `money` int(11) DEFAULT '0',
  `verify` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if ($DMH->query($sql_users)) {
    echo "<p style='color:green'>✔ Bảng users đã sẵn sàng.</p>";
    
    // Thêm cột tho_id vào bảng dat_lich nếu trước đây lỡ chưa có
    $DMH->query("ALTER TABLE `dat_lich` ADD COLUMN IF NOT EXISTS `tho_id` int(11) DEFAULT '0'");
    
} else {
    echo "<p style='color:red'>✖ Lỗi tạo bảng users.</p>";
}

// 3. Tạo tài khoản demo cho thợ
$check_demo = $DMH->get_row("SELECT * FROM `users` WHERE `username` = 'tho_demo'");
if (!$check_demo) {
    $insert = $DMH->insert("users", [
        'username' => 'tho_demo',
        'password' => md5('123456'), // Tạm dùng md5 cho hệ thống cũ, nếu hệ thống dùng password_hash thì sẽ update sau. Nhưng config.php ko có hàm encode riêng.
        'name'     => 'Thợ Sửa Chữa (Demo)',
        'level'    => 'tho',
        'banned'   => 'ON'
    ]);
    if ($insert) {
        echo "<p style='color:blue'>✔ Đã tạo tài khoản thợ demo: <b>tho_demo</b> / Mật khẩu: <b>123456</b></p>";
    } else {
        echo "<p style='color:red'>✖ Lỗi tạo tài khoản demo.</p>";
    }
} else {
    // Đảm bảo mật khẩu đúng là 123456 và level là 'tho'
    $DMH->update("users", [
        'password' => md5('123456'),
        'level'    => 'tho'
    ], "`username` = 'tho_demo'");
    echo "<p style='color:orange'>✔ Đã reset tài khoản thợ demo: <b>tho_demo</b> / Mật khẩu: <b>123456</b></p>";
}

echo "<h2>🎉 Hoàn tất cài đặt!</h2>";
echo "<p>Vui lòng xóa file này sau khi dùng xong để bảo mật.</p>";
echo "<a href='/'>Quay lại trang chủ</a>";
?>
