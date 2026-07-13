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
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Thieu ma san pham']);
        exit;
    }

    $ok = $DMH->remove("products", "`id` = '$id'");
    if ($ok) {
        echo json_encode(['status' => 'success', 'msg' => 'Da xoa san pham.']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Xoa san pham that bai.']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Loi he thong: ' . $e->getMessage()]);
}
