<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();
header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    echo json_encode(['status'=>'error', 'msg'=>'ID khong hop le']);
    exit;
}

$product = $DMH->get_row("SELECT * FROM `products` WHERE `id` = $id");
if (!$product) {
    echo json_encode(['status'=>'error', 'msg'=>'Khong tim thay san pham']);
    exit;
}

$newFeatured = empty($product['featured']) ? 1 : 0;
$DMH->query("UPDATE `products` SET `featured` = $newFeatured WHERE `id` = $id");
echo json_encode(['status'=>'success', 'msg'=>'Da cap nhat', 'featured'=>$newFeatured]);
?>
