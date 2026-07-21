<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
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

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$action = isset($input['action']) ? trim($input['action']) : '';
$device_id = isset($input['device_id']) ? trim($input['device_id']) : '';
$product_id = isset($input['product_id']) ? (int)$input['product_id'] : 0;
$cart_id = isset($input['cart_id']) ? (int)$input['cart_id'] : 0;
$qty = isset($input['qty']) ? (int)$input['qty'] : 1;

if (empty($device_id) || strlen($device_id) < 8) {
    echo json_encode(['status' => 'error', 'msg' => 'Thiếu device_id']);
    exit;
}

// Sanitize device_id
$device_id_escaped = mysqli_real_escape_string($DMH->ketnoi, $device_id);

function getCartCount($DMH, $device_id) {
    $row = $DMH->get_row("SELECT SUM(quantity) as c FROM `miniapp_carts` WHERE `device_id` = '$device_id'");
    return (int)($row['c'] ?? 0);
}

function getCartItems($DMH, $device_id) {
    return $DMH->get_list("SELECT c.*, p.name, p.price, p.image FROM `miniapp_carts` c JOIN `products` p ON c.product_id = p.id WHERE c.device_id = '$device_id' ORDER BY c.id DESC");
}

if ($action === 'add') {
    if ($product_id <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Sản phẩm không hợp lệ']);
        exit;
    }
    $check = $DMH->get_row("SELECT * FROM `products` WHERE `id` = '$product_id' AND `status` = 1");
    if (!$check) {
        echo json_encode(['status' => 'error', 'msg' => 'Sản phẩm không tồn tại']);
        exit;
    }
    $existing = $DMH->get_row("SELECT * FROM `miniapp_carts` WHERE `device_id` = '$device_id_escaped' AND `product_id` = '$product_id'");
    if ($existing) {
        $DMH->query("UPDATE `miniapp_carts` SET `quantity` = `quantity` + $qty WHERE `id` = '".$existing['id']."'");
    } else {
        $DMH->insert("miniapp_carts", [
            'device_id' => $device_id,
            'product_id' => $product_id,
            'quantity' => $qty
        ]);
    }
    echo json_encode(['status' => 'success', 'count' => getCartCount($DMH, $device_id_escaped)]);
    exit;
}

if ($action === 'get') {
    echo json_encode(['status' => 'success', 'data' => getCartItems($DMH, $device_id_escaped), 'count' => getCartCount($DMH, $device_id_escaped)]);
    exit;
}

if ($action === 'count') {
    echo json_encode(['status' => 'success', 'count' => getCartCount($DMH, $device_id_escaped)]);
    exit;
}

if ($action === 'remove') {
    $DMH->query("DELETE FROM `miniapp_carts` WHERE `id` = '$cart_id' AND `device_id` = '$device_id_escaped'");
    echo json_encode(['status' => 'success', 'count' => getCartCount($DMH, $device_id_escaped)]);
    exit;
}

if ($action === 'update') {
    if ($qty <= 0) {
        $DMH->query("DELETE FROM `miniapp_carts` WHERE `id` = '$cart_id' AND `device_id` = '$device_id_escaped'");
    } else {
        $DMH->query("UPDATE `miniapp_carts` SET `quantity` = '$qty' WHERE `id` = '$cart_id' AND `device_id` = '$device_id_escaped'");
    }
    echo json_encode(['status' => 'success', 'count' => getCartCount($DMH, $device_id_escaped)]);
    exit;
}

if ($action === 'checkout') {
    $name = isset($input['name']) ? trim($input['name']) : '';
    $phone = isset($input['phone']) ? trim($input['phone']) : '';
    $address = isset($input['address']) ? trim($input['address']) : '';
    $note = isset($input['note']) ? trim($input['note']) : '';

    if (empty($name) || empty($phone) || empty($address)) {
        echo json_encode(['status' => 'error', 'msg' => 'Vui lòng nhập đủ thông tin giao hàng']);
        exit;
    }

    $items = getCartItems($DMH, $device_id_escaped);
    if (!$items) {
        echo json_encode(['status' => 'error', 'msg' => 'Giỏ hàng trống']);
        exit;
    }

    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $DMH->insert("store_orders", [
        'user_id' => 0,
        'total_amount' => $total,
        'status' => 'pending',
        'payment_method' => 'COD',
        'customer_name' => $name,
        'phone' => $phone,
        'address' => $address,
        'note' => $note,
        'vat_requested' => 0,
        'source' => 'miniapp',
        'device_id' => $device_id
    ]);

    $order_id = $DMH->get_row("SELECT id FROM `store_orders` WHERE `device_id` = '$device_id_escaped' ORDER BY id DESC LIMIT 1")['id'];

    foreach ($items as $item) {
        $DMH->insert("store_order_items", [
            'order_id' => $order_id,
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'price' => $item['price']
        ]);
    }

    $DMH->query("DELETE FROM `miniapp_carts` WHERE `device_id` = '$device_id_escaped'");

    echo json_encode(['status' => 'success', 'order_id' => $order_id]);
    exit;
}

echo json_encode(['status' => 'error', 'msg' => 'Hành động không hợp lệ']);
