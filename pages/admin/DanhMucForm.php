<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$type = in_array($_GET['type'] ?? '', ['dienmay','3d']) ? $_GET['type'] : 'dienmay';
$id = (int)($_GET['id'] ?? 0);
$returnUrl = ($type === '3d' ? '/pages/admin/GianHang3D.php' : '/pages/admin/GianHangDienMay.php') . '?view=categories';
$pageTitle = $type === '3d' ? 'Mô hình 3D' : 'Điện máy';

$category = ['id' => 0, 'name' => '', 'slug' => '', 'sort_order' => 0];
if ($id > 0) {
    $row = $DMH->get_row("SELECT * FROM `product_categories` WHERE `id` = $id AND `type` = '$type'");
    if ($row) $category = $row;
    else $id = 0;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    if ($name == '') {
        $error = 'Vui lòng nhập tên danh mục.';
    } else {
        $DMH->connect();
        if ($slug == '') {
            $slug = strtolower(vn_to_ascii($name));
            $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
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
            header("Location: " . $returnUrl);
            exit;
        } else {
            $error = 'Lưu danh mục thất bại, vui lòng thử lại.';
        }
    }
}

function vn_to_ascii($str) {
    $map = [
        'à'=>'a','á'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a','ă'=>'a','ằ'=>'a','ắ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a','â'=>'a','ầ'=>'a','ấ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
        'đ'=>'d','è'=>'e','é'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e','ê'=>'e','ề'=>'e','ế'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
        'ì'=>'i','í'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i','ò'=>'o','ó'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o','ô'=>'o','ồ'=>'o','ố'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o','ơ'=>'o','ờ'=>'o','ớ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
        'ù'=>'u','ú'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u','ư'=>'u','ừ'=>'u','ứ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
        'ỳ'=>'y','ý'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y',
        'À'=>'A','Á'=>'A','Ả'=>'A','Ã'=>'A','Ạ'=>'A','Ă'=>'A','Ằ'=>'A','Ắ'=>'A','Ẳ'=>'A','Ẵ'=>'A','Ặ'=>'A','Â'=>'A','Ầ'=>'A','Ấ'=>'A','Ẩ'=>'A','Ẫ'=>'A','Ậ'=>'A',
        'Đ'=>'D','È'=>'E','É'=>'E','Ẻ'=>'E','Ẽ'=>'E','Ẹ'=>'E','Ê'=>'E','Ề'=>'E','Ế'=>'E','Ể'=>'E','Ễ'=>'E','Ệ'=>'E',
        'Ì'=>'I','Í'=>'I','Ỉ'=>'I','Ĩ'=>'I','Ị'=>'I','Ò'=>'O','Ó'=>'O','Ỏ'=>'O','Õ'=>'O','Ọ'=>'O','Ô'=>'O','Ồ'=>'O','Ố'=>'O','Ổ'=>'O','Ỗ'=>'O','Ộ'=>'O','Ơ'=>'O','Ờ'=>'O','Ớ'=>'O','Ở'=>'O','Ỡ'=>'O','Ợ'=>'O',
        'Ù'=>'U','Ú'=>'U','Ủ'=>'U','Ũ'=>'U','Ụ'=>'U','Ư'=>'U','Ừ'=>'U','Ứ'=>'U','Ử'=>'U','Ữ'=>'U','Ự'=>'U',
        'Ỳ'=>'Y','Ý'=>'Y','Ỷ'=>'Y','Ỹ'=>'Y','Ỵ'=>'Y',
    ];
    $out = '';
    for ($i = 0; $i < mb_strlen($str); $i++) {
        $ch = mb_substr($str, $i, 1);
        $out .= isset($map[$ch]) ? $map[$ch] : $ch;
    }
    return $out;
}

$tieude = ($id > 0 ? 'Sửa danh mục' : 'Thêm danh mục') . ' ' . $pageTitle . ' | Điện Máy Hiếu';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");
?>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;">
    <i class="fa-solid fa-folder-open" style="color:#0ea5e9;"></i>
    <?= $id > 0 ? 'Sửa danh mục' : 'Thêm danh mục' ?> <?= htmlspecialchars($pageTitle) ?>
</h2>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= (int)$category['id'] ?>">
            <div class="form-group">
                <label>Tên danh mục <span style="color:#dc2626;">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($category['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Slug (tùy chọn)</label>
                <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($category['slug']) ?>" placeholder="tu-dong-neu-de-trong">
            </div>
            <div class="form-group">
                <label>Thứ tự hiển thị</label>
                <input type="number" name="sort_order" class="form-control" value="<?= (int)$category['sort_order'] ?>" min="0">
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Lưu danh mục</button>
                <a href="<?= htmlspecialchars($returnUrl) ?>" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
</div>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
