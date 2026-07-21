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

$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$featured = isset($_GET['featured']) ? (int)$_GET['featured'] : 0;

$sql = "SELECT p.*, c.name as category_name FROM `products` p LEFT JOIN `product_categories` c ON c.id = p.category_id WHERE p.`status` = 1";

if ($featured === 1) {
    $sql .= " AND p.`featured` = 1";
}

if ($category !== '') {
    $category_escaped = mysqli_real_escape_string($DMH->ketnoi, $category);
    $sql .= " AND (c.name = '{$category_escaped}' OR p.`type` = '{$category_escaped}')";
}

if ($search !== '') {
    $search_escaped = mysqli_real_escape_string($DMH->ketnoi, $search);
    $sql .= " AND p.`name` LIKE '%{$search_escaped}%'";
}

$sql .= " ORDER BY p.`featured` DESC, p.`id` DESC LIMIT 200";

$products = $DMH->get_list($sql);
if (!is_array($products)) {
    $products = [];
}

$result = [];
foreach ($products as $p) {
    $result[] = [
        'id' => (int)$p['id'],
        'name' => isset($p['name']) ? (string)$p['name'] : '',
        'category' => !empty($p['category_name']) ? $p['category_name'] : (isset($p['type']) && $p['type'] == '3d' ? 'Mô hình In 3D' : 'Điện Máy & Gia Dụng'),
        'image' => isset($p['image']) ? (string)$p['image'] : '',
        'price' => isset($p['price']) ? (float)$p['price'] : 0,
        'description' => isset($p['description']) ? (string)$p['description'] : '',
    ];
}

echo json_encode([
    'status' => 'success',
    'count' => count($result),
    'data' => $result,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
