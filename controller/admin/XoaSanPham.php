<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$type = in_array($_POST['type'] ?? '', ['dienmay','3d']) ? $_POST['type'] : 'dienmay';
$return = $type === '3d' ? '/pages/admin/GianHang3D.php' : '/pages/admin/GianHangDienMay.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $DMH->remove("products", "`id` = '$id'");
    }
}

header("Location: " . $return);
exit;
?>
