<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
    || (($_POST['ajax'] ?? '') === '1');

if (!$isAjax) {
    header('Content-Type: text/html; charset=utf-8');
} else {
    header('Content-Type: application/json');
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'msg' => 'Không hỗ trợ phương thức này']);
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Không hỗ trợ phương thức này'];
            header('Location: /pages/admin/GianHangDienMay.php');
        }
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
    $featured = isset($_POST['featured']) ? (int)$_POST['featured'] : 0;

    if ($name == '') {
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng nhập tên sản phẩm']);
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Vui lòng nhập tên sản phẩm'];
            header('Location: /pages/admin/GianHang' . ucfirst($type) . '.php' . ($id ? '?edit='.$id : ''));
        }
        exit;
    }
    if ($category_id <= 0) {
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng chọn danh mục']);
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Vui lòng chọn danh mục'];
            header('Location: /pages/admin/GianHang' . ucfirst($type) . '.php' . ($id ? '?edit='.$id : ''));
        }
        exit;
    }

    $DMH->connect();

    if ($slug == '') {
        $slug = createSlug($name);
    } else {
        $slug = createSlug($slug);
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
        'featured' => $featured,
    ];
    if ($image !== '') {
        $data['image'] = $image;
    }

    if ($id > 0) {
        $ok = $DMH->update("products", $data, "`id` = '$id'");
    } else {
        $ok = $DMH->insert("products", $data);
    }

    $backUrl = '/pages/admin/GianHang' . ($type == '3d' ? '3D' : 'DienMay') . '.php';
    if ($ok) {
        if ($isAjax) {
            echo json_encode(['status' => 'success', 'msg' => 'Đã lưu sản phẩm.', 'url' => $backUrl, 'time' => 1000]);
        } else {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Đã lưu sản phẩm thành công'];
            header('Location: ' . $backUrl);
        }
    } else {
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'msg' => 'Lưu sản phẩm thất bại']);
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Lưu sản phẩm thất bại'];
            header('Location: ' . $backUrl . ($id ? '?edit='.$id : ''));
        }
    }
} catch (Throwable $e) {
    if ($isAjax) {
        echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()];
        header('Location: /pages/admin/GianHangDienMay.php');
    }
}
exit;

function createSlug($str) {
    $str = strtolower(trim($str));
    $str = preg_replace('/[àáạảãâầấậẩẫăằắặẳẵ]/u', 'a', $str);
    $str = preg_replace('/[èéẹẻẽêềếệểễ]/u', 'e', $str);
    $str = preg_replace('/[ìíịỉĩ]/u', 'i', $str);
    $str = preg_replace('/[òóọỏõôồốộổỗơờớợởỡ]/u', 'o', $str);
    $str = preg_replace('/[ùúụủũưừứựửữ]/u', 'u', $str);
    $str = preg_replace('/[ỳýỵỷỹ]/u', 'y', $str);
    $str = preg_replace('/đ/u', 'd', $str);
    $str = preg_replace('/[^a-z0-9]+/u', '-', $str);
    $str = trim($str, '-');
    if ($str == '') $str = 'sp-' . time();
    return $str;
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
