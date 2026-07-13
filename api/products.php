<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

define("IN_SITE", true);
require_once(__DIR__."/../core/config.php");
require_once(__DIR__."/../core/function.php");

if (!isset($DMH)) {
    $DMH = new DMH();
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
if ($limit > 100) $limit = 100;

$offset = ($page - 1) * $limit;

$category = isset($_GET['category']) ? $_GET['category'] : '';
$where = "WHERE `status` = 1";

if ($category === '3d') {
    $where .= " AND `type` = '3d'";
} elseif ($category === 'dienmay') {
    $where .= " AND `type` = 'dienmay'";
}

$query = "SELECT `id`, `name`, `type`, `price`, `image`, `description` FROM `products` $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
$products = $DMH->get_list($query);

$totalQuery = $DMH->get_row("SELECT COUNT(id) as total FROM `products` $where");
$total = isset($totalQuery['total']) ? (int)$totalQuery['total'] : 0;

$result = [
    'status' => 'success',
    'data' => $products ? $products : [],
    'meta' => [
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'total_pages' => ceil($total / $limit)
    ]
];

echo json_encode($result);
exit;
