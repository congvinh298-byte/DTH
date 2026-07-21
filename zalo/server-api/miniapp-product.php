<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-cache');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

define("IN_SITE", true);
require_once(__DIR__."/../core/config.php");
require_once(__DIR__."/../core/function.php");

if (!isset($DMH)) {
    $DMH = new DMH();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Thiếu ID sản phẩm.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$product = $DMH->get_row("SELECT p.*, c.name as category_name FROM `products` p LEFT JOIN `product_categories` c ON c.id = p.category_id WHERE p.`id` = {$id} AND p.`status` = 1 LIMIT 1");

if (!$product) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy sản phẩm.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$result = [
    'id' => (int)$product['id'],
    'name' => isset($product['name']) ? (string)$product['name'] : '',
    'category' => !empty($product['category_name']) ? $product['category_name'] : (isset($product['type']) && $product['type'] == '3d' ? 'Mô hình In 3D' : 'Điện Máy & Gia Dụng'),
    'image' => isset($product['image']) ? (string)$product['image'] : '',
    'price' => isset($product['price']) ? (float)$product['price'] : 0,
    'description' => isset($product['description']) ? (string)$product['description'] : '',
    'stock' => isset($product['stock']) ? (int)$product['stock'] : 0,
];

echo json_encode([
    'status' => 'success',
    'data' => $result,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
