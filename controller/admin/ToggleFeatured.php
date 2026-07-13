<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$return = isset($_GET['return']) ? $_GET['return'] : '';
if (!$return || !preg_match('#^/pages/admin/(GianHangDienMay|GianHang3D)\.php#', $return)) {
    $return = '/pages/admin/GianHangDienMay.php';
}

if ($id > 0) {
    $product = $DMH->get_row("SELECT * FROM `products` WHERE `id` = $id");
    if ($product) {
        $new = empty($product['featured']) ? 1 : 0;
        $DMH->query("UPDATE `products` SET `featured` = $new WHERE `id` = $id");
    }
}

header("Location: " . $return);
exit;
?>
