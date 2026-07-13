<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$type = in_array($_POST['type'] ?? '', ['dienmay','3d']) ? $_POST['type'] : 'dienmay';
$return = ($type === '3d' ? '/pages/admin/GianHang3D.php' : '/pages/admin/GianHangDienMay.php') . '?view=categories';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $count = $DMH->num_rows("SELECT * FROM `products` WHERE `category_id` = '$id'");
        if ($count == 0) {
            $DMH->remove("product_categories", "`id` = '$id' AND `type` = '$type'");
        }
    }
}

header("Location: " . $return);
exit;
?>
