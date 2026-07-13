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
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $type = in_array($_POST['type'] ?? '', ['dienmay','3d']) ? $_POST['type'] : 'dienmay';

    if ($name == '') {
        echo json_encode(['status' => 'error', 'msg' => 'Vui long nhap ten danh muc']);
        exit;
    }

    $DMH->connect();

    if ($slug == '') {
        $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(vn_to_ascii($name)));
        $slug = trim($slug, '-');
        if ($slug == '') $slug = 'dm-' . time();
    }

    $baseSlug = $slug;
    $counter = 1;
    while (true) {
        $cond = "`slug` = '" . mysqli_real_escape_string($DMH->ketnoi, $slug) . "' AND `type` = '$type'";
        if ($id > 0) $cond .= " AND `id` != $id";
        $ex = $DMH->get_row("SELECT id FROM `product_categories` WHERE $cond");
        if (!$ex) break;
        $slug = $baseSlug . '-' . $counter++;
    }

    $data = ['name' => $name, 'slug' => $slug, 'type' => $type, 'sort_order' => $sort_order];

    if ($id > 0) {
        $ok = $DMH->update("product_categories", $data, "`id` = '$id'");
    } else {
        $ok = $DMH->insert("product_categories", $data);
    }

    if ($ok) {
        echo json_encode(['status' => 'success', 'msg' => 'Da luu danh muc.', 'url' => '/pages/admin/GianHang' . ucfirst($type) . '.php?view=categories', 'time' => 1000]);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Luu danh muc that bai.']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Loi he thong: ' . $e->getMessage()]);
}

function vn_to_ascii($str) {
    $orig = 'aAeEoOuUiIdDyY';
    $flat = 'aaaaaaaaaaaaaaaaeeeeeeeeeeeeoooooooooooouuuuuuuuuuuuiiiiiiiiiiiidddyyyyyyyyyyyy';
    $map = [];
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
