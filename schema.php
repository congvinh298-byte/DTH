<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");

echo "=== dat_lich ===\n";
$q = $DMH->query("SHOW CREATE TABLE dat_lich");
if ($q && $r = $q->fetch_assoc()) {
    print_r($r);
} else {
    echo "Creating dat_lich table...\n";
    $DMH->query("CREATE TABLE `dat_lich` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Created dat_lich.\n";
}

echo "=== users ===\n";
$q2 = $DMH->query("SHOW CREATE TABLE users");
if ($q2 && $r = $q2->fetch_assoc()) {
    print_r($r);
} else {
    echo "users table does not exist.\n";
}
?>
