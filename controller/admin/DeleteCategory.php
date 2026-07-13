<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json');

try {
    if (!isset($_COOKIE['token']) || empty($getUser) || !in_array($getUser['level'], ['admin','bct'])) {
        echo json_encode(['status' => 'error', 'msg' => 'Khong co quyen']);
        exit;
    }
    if (empty($_SESSION['loginadmin'])) {
        echo json_encode(['status' => 'error', 'msg' => 'Vui long dang nhap admin']);
        exit;
    }

    $id = (int)($_GET['id'] ?? 0);
    $type = in_array($_GET['type'] ?? '', ['dienmay','3d']) ? $_GET['type'] : 'dienmay';
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Thieu ma danh muc']);
        exit;
    }

    $count = $DMH->num_rows("SELECT * FROM `products` WHERE `category_id` = '$id'");
    if ($count > 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Khong the xoa danh muc dang co san pham.']);
        exit;
    }

    $ok = $DMH->remove("product_categories", "`id` = '$id' AND `type` = '$type'");
    if ($ok) {
        echo json_encode(['status' => 'success', 'msg' => 'Da xoa danh muc.']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Xoa danh muc that bai.']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Loi he thong: ' . $e->getMessage()]);
}
