<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json');

if(!isset($_COOKIE['token']) || $getUser['level'] != 'admin') {
    echo json_encode(['status' => 'error', 'msg' => 'Unauthorized']);
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if($action == 'add_product') {
        $type = $_POST['type'] ?? 'dienmay';
        $name = $_POST['name'] ?? '';
        $price = (int)($_POST['price'] ?? 0);
        $image = $_POST['image'] ?? '';
        $description = $_POST['description'] ?? '';

        if(empty($name) || $price < 0) {
            echo json_encode(['status' => 'error', 'msg' => 'Thiếu thông tin']);
            exit;
        }

        $DMH->insert("store_products", [
            'type' => $type,
            'name' => $name,
            'price' => $price,
            'image' => $image,
            'description' => $description
        ]);

        echo json_encode(['status' => 'success']);
        exit;
    }

    if($action == 'list_products') {
        $type = $_POST['type'] ?? 'all';
        $sql = "SELECT * FROM `store_products` ORDER BY id DESC";
        if($type != 'all') {
            $sql = "SELECT * FROM `store_products` WHERE `type` = '$type' ORDER BY id DESC";
        }
        $products = $DMH->get_list($sql);
        echo json_encode(['status' => 'success', 'data' => $products ? $products : []]);
        exit;
    }

    if($action == 'delete_product') {
        $id = (int)$_POST['id'];
        $DMH->query("DELETE FROM `store_products` WHERE `id` = '$id'");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if($action == 'list_orders') {
        $orders = $DMH->get_list("SELECT * FROM `store_orders` ORDER BY id DESC");
        echo json_encode(['status' => 'success', 'data' => $orders ? $orders : []]);
        exit;
    }

    if($action == 'get_order') {
        $id = (int)$_POST['id'];
        $order = $DMH->get_row("SELECT * FROM `store_orders` WHERE `id` = '$id'");
        if($order) {
            $items = $DMH->get_list("SELECT i.*, p.name FROM `store_order_items` i LEFT JOIN `store_products` p ON i.product_id = p.id WHERE i.order_id = '$id'");
            echo json_encode(['status' => 'success', 'data' => ['order' => $order, 'items' => $items ? $items : []]]);
        } else {
            echo json_encode(['status' => 'error']);
        }
        exit;
    }

    if($action == 'update_order_status') {
        $id = (int)$_POST['id'];
        $status = $_POST['status'];
        $DMH->update("store_orders", ['status' => $status], "`id` = '$id'");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if($action == 'list_techs') {
        $techs = $DMH->get_list("SELECT `id`, `username`, `name`, `phone`, `banned` FROM `users` WHERE `level` = 'tho' ORDER BY id DESC");
        echo json_encode(['status' => 'success', 'data' => $techs ? $techs : []]);
        exit;
    }

    if($action == 'ban_tech') {
        $id = (int)$_POST['id'];
        $banned = $_POST['banned'] == 'ON' ? 'ON' : 'OFF';
        $DMH->update("users", ['banned' => $banned], "`id` = '$id' AND `level` = 'tho'");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if($action == 'list_bookings') {
        $bookings = $DMH->get_list("SELECT d.*, u.name as tho_name FROM `dat_lich` d LEFT JOIN `users` u ON d.tho_id = u.id ORDER BY d.id DESC");
        echo json_encode(['status' => 'success', 'data' => $bookings ? $bookings : []]);
        exit;
    }

    if($action == 'delete_booking') {
        $id = (int)$_POST['id'];
        $DMH->query("DELETE FROM `dat_lich` WHERE `id` = '$id'");
        echo json_encode(['status' => 'success']);
        exit;
    }
}
?>
