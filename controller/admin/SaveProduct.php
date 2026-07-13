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

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'msg' => 'Khong ho tro']);
        exit;
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price = (int)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $type = in_array($_POST['type'] ?? '', ['dienmay','3d']) ? $_POST['type'] : 'dienmay';
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

    if ($name == '') {
        echo json_encode(['status' => 'error', 'msg' => 'Vui long nhap ten san pham']);
        exit;
    }
    if ($category_id <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Vui long chon danh muc']);
        exit;
    }

    $DMH->connect();

    if ($slug == '') {
        $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(vn_to_ascii($name)));
        $slug = trim($slug, '-');
        if ($slug == '') $slug = 'sp-' . time();
    }

    // Unique slug
    $baseSlug = $slug;
    $counter = 1;
    while (true) {
        $cond = "`slug` = '" . mysqli_real_escape_string($DMH->ketnoi, $slug) . "' AND `type` = '$type'";
        if ($id > 0) $cond .= " AND `id` != $id";
        $ex = $DMH->get_row("SELECT id FROM `products` WHERE $cond");
        if (!$ex) break;
        $slug = $baseSlug . '-' . $counter++;
    }

    $image = trim($_POST['image_url'] ?? '');
    if (!$image && !empty($_FILES['image_file']['tmp_name'])) {
        $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $uploadDir = __DIR__ . '/../../images/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $target = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target)) {
            $image = '/images/products/' . $filename;
        }
    }

    $data = [
        'category_id' => $category_id,
        'name' => $name,
        'slug' => $slug,
        'description' => $description,
        'price' => $price,
        'stock' => $stock,
        'type' => $type,
        'status' => $status,
    ];
    if ($image !== '') {
        $data['image'] = $image;
    }

    if ($id > 0) {
        $ok = $DMH->update("products", $data, "`id` = '$id'");
    } else {
        $ok = $DMH->insert("products", $data);
    }

    if ($ok) {
        echo json_encode(['status' => 'success', 'msg' => 'Da luu san pham.', 'url' => '/pages/admin/GianHang' . ucfirst($type) . '.php', 'time' => 1000]);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Luu san pham that bai.']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Loi he thong: ' . $e->getMessage()]);
}

function vn_to_ascii($str) {
    $a = [
        'a' => 'aAeEoOuUiIdDyY',
        'a' => 'aaaaaaaaaaaaaaaa',
        'e' => 'eeeeeeeeeeee',
        'o' => 'oooooooooooo',
        'u' => 'uuuuuuuuuuuu',
        'i' => 'iiiiiiiiiiii',
        'd' => 'dd',
        'y' => 'yyyyyyyyyyyy',
    ];
    $map = [];
    $orig = 'aAeEoOuUiIdDyY';
    $flat = 'aaaaaaaaaaaaaaaaeeeeeeeeeeeeoooooooooooouuuuuuuuuuuuiiiiiiiiiiiidddyyyyyyyyyyyy';
    for ($i = 0; $i < strlen($orig); $i++) {
        $map[$orig[$i]] = $flat[$i];
    }
    $out = '';
    for ($i = 0; $i < strlen($str); $i++) {
        $ch = $str[$i];
        $out .= isset($map[$ch]) ? $map[$ch] : $ch;
    }
    return $out;
}
