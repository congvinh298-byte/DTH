<?php
error_reporting(0);
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json');

$is_logged_in = isset($_COOKIE['token']);
$user_id = $getUser['id'] ?? 0;
$username = $getUser['name'] ?? ($getUser['username'] ?? '');

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if($action == 'check_login') {
        echo json_encode(['logged_in' => $is_logged_in, 'username' => $username]);
        exit;
    }

    if($action == 'cart_count') {
        if(!$is_logged_in) {
            echo json_encode(['count' => 0]);
            exit;
        }
        $count = $DMH->get_row("SELECT SUM(quantity) as c FROM `store_carts` WHERE `user_id` = '$user_id'")['c'];
        echo json_encode(['count' => (int)$count]);
        exit;
    }

    if($action == 'add_to_cart') {
        if(!$is_logged_in) {
            echo json_encode(['status' => 'error', 'msg' => 'Bạn cần đăng nhập để mua hàng!']);
            exit;
        }

        $product_id = (int)$_POST['product_id'];
        $quantity = 1; // Default 1

        // Check if product exists
        $check = $DMH->get_row("SELECT * FROM `store_products` WHERE `id` = '$product_id'");
        if(!$check) {
            echo json_encode(['status' => 'error', 'msg' => 'Sản phẩm không tồn tại']);
            exit;
        }

        // Check if already in cart
        $cart_item = $DMH->get_row("SELECT * FROM `store_carts` WHERE `user_id` = '$user_id' AND `product_id` = '$product_id'");
        if($cart_item) {
            $DMH->query("UPDATE `store_carts` SET `quantity` = `quantity` + 1 WHERE `id` = '".$cart_item['id']."'");
        } else {
            $DMH->insert("store_carts", [
                'user_id' => $user_id,
                'product_id' => $product_id,
                'quantity' => $quantity
            ]);
        }

        echo json_encode(['status' => 'success']);
        exit;
    }

    if($action == 'get_cart') {
        if(!$is_logged_in) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng đăng nhập']);
            exit;
        }

        $items = $DMH->get_list("SELECT c.*, p.name, p.price, p.image FROM `store_carts` c JOIN `store_products` p ON c.product_id = p.id WHERE c.user_id = '$user_id'");
        echo json_encode(['status' => 'success', 'data' => $items ? $items : []]);
        exit;
    }

    if($action == 'remove_item') {
        if(!$is_logged_in) exit;
        $id = (int)$_POST['id'];
        $DMH->query("DELETE FROM `store_carts` WHERE `id` = '$id' AND `user_id` = '$user_id'");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if($action == 'update_qty') {
        if(!$is_logged_in) exit;
        $id = (int)$_POST['id'];
        $qty = (int)$_POST['qty'];
        if ($qty > 0) {
            $DMH->query("UPDATE `store_carts` SET `quantity` = '$qty' WHERE `id` = '$id' AND `user_id` = '$user_id'");
        }
        echo json_encode(['status' => 'success']);
        exit;
    }

    if($action == 'checkout') {
        if(!$is_logged_in) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng đăng nhập']);
            exit;
        }

        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $note = $_POST['note'] ?? '';
        $payment_method = $_POST['payment_method'] ?? 'COD';
        $vat_requested = isset($_POST['vat_requested']) ? (int)$_POST['vat_requested'] : 0;

        if(empty($name) || empty($phone) || empty($address)) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng nhập đủ thông tin giao hàng!']);
            exit;
        }

        $items = $DMH->get_list("SELECT c.*, p.price FROM `store_carts` c JOIN `store_products` p ON c.product_id = p.id WHERE c.user_id = '$user_id'");
        if(!$items) {
            echo json_encode(['status' => 'error', 'msg' => 'Giỏ hàng trống']);
            exit;
        }

        $total_amount = 0;
        foreach($items as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }

        // Tạo order
        $DMH->insert("store_orders", [
            'user_id' => $user_id,
            'total_amount' => $total_amount,
            'status' => 'pending',
            'payment_method' => $payment_method,
            'customer_name' => $name,
            'phone' => $phone,
            'address' => $address,
            'note' => $note,
            'vat_requested' => $vat_requested
        ]);

        // Lấy ID mới nhất vừa insert. PHP MySQLi không trả trực tiếp qua class này, phải ORDER BY id DESC LIMIT 1
        $order_id = $DMH->get_row("SELECT id FROM `store_orders` WHERE `user_id` = '$user_id' ORDER BY id DESC LIMIT 1")['id'];

        // Add items to order_items
        foreach($items as $item) {
            $DMH->insert("store_order_items", [
                'order_id' => $order_id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        // Xóa giỏ hàng
        $DMH->query("DELETE FROM `store_carts` WHERE `user_id` = '$user_id'");

        echo json_encode(['status' => 'success']);
        exit;
    }
}
?>
