<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$type = in_array($_GET['type'] ?? '', ['dienmay','3d']) ? $_GET['type'] : 'dienmay';
$id = (int)($_GET['id'] ?? 0);
$returnUrl = $type === '3d' ? '/pages/admin/GianHang3D.php' : '/pages/admin/GianHangDienMay.php';
$pageTitle = $type === '3d' ? 'Mô hình 3D' : 'Điện máy';

$categories = $DMH->get_list("SELECT * FROM `product_categories` WHERE `type` = '$type' ORDER BY `sort_order`, `name`");

$product = [
    'id' => 0, 'category_id' => 0, 'name' => '', 'slug' => '', 'description' => '',
    'price' => 0, 'stock' => 0, 'image' => '', 'status' => 1, 'featured' => 0
];
if ($id > 0) {
    $row = $DMH->get_row("SELECT * FROM `products` WHERE `id` = $id AND `type` = '$type'");
    if ($row) $product = $row;
    else $id = 0;
}

$error = '';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price = (int)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;
    $featured = isset($_POST['featured']) ? 1 : 0;
    $image_url = trim($_POST['image_url'] ?? '');

    if ($name == '') {
        $error = 'Vui lòng nhập tên sản phẩm.';
    } elseif ($category_id <= 0) {
        $error = 'Vui lòng chọn danh mục.';
    } else {
        $DMH->connect();

        if ($slug == '') {
            $slug = strtolower(vn_to_ascii($name));
            $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
            $slug = trim($slug, '-');
            if ($slug == '') $slug = 'sp-' . time();
        }
        $baseSlug = $slug;
        $counter = 1;
        while (true) {
            $cond = "`slug` = '" . mysqli_real_escape_string($DMH->ketnoi, $slug) . "' AND `type` = '$type'";
            if ($id > 0) $cond .= " AND `id` != $id";
            $ex = $DMH->get_row("SELECT id FROM `products` WHERE $cond");
            if (!$ex) break;
            $slug = $baseSlug . '-' . $counter++;
        }

        $image = $image_url;
        if (!$image && !empty($_FILES['image_file']['tmp_name'])) {
            $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array($ext, $allowed)) {
                $filename = 'product_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                $uploadDir = __DIR__ . '/../../images/products/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $target = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target)) {
                    $image = '/images/products/' . $filename;
                }
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
        if ($image !== '') $data['image'] = $image;

        if ($id > 0) {
            $ok = $DMH->update("products", $data, "`id` = '$id'");
        } else {
            $ok = $DMH->insert("products", $data);
        }
        if ($ok) {
            header("Location: " . $returnUrl);
            exit;
        } else {
            $error = 'Lưu sản phẩm thất bại, vui lòng thử lại.';
            $product = compact('id','category_id','name','slug','description','price','stock','status','featured','image') + ['image' => $image_url ?: $product['image']];
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

$tieude = ($id > 0 ? 'Sửa sản phẩm' : 'Thêm sản phẩm') . ' ' . $pageTitle . ' | Điện Máy Hiếu';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");
?>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;">
    <i class="fa-solid <?= $type === '3d' ? 'fa-cube' : 'fa-plug' ?>" style="color:#0ea5e9;"></i>
    <?= $id > 0 ? 'Sửa sản phẩm' : 'Thêm sản phẩm' ?> <?= htmlspecialchars($pageTitle) ?>
</h2>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="card" style="max-width:900px;">
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
                <div class="form-group">
                    <label>Tên sản phẩm <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Slug (tùy chọn)</label>
                    <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($product['slug']) ?>" placeholder="tu-dong-neu-de-trong">
                </div>
                <div class="form-group">
                    <label>Danh mục <span style="color:#dc2626;">*</span></label>
                    <select name="category_id" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int)$c['id'] ?>" <?= (int)$product['category_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Giá (VNĐ)</label>
                    <input type="number" name="price" class="form-control" value="<?= (int)$product['price'] ?>" min="0">
                </div>
                <div class="form-group">
                    <label>Tồn kho</label>
                    <input type="number" name="stock" class="form-control" value="<?= (int)$product['stock'] ?>" min="0">
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="1" <?= (int)$product['status'] ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="0" <?= !(int)$product['status'] ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Đưa lên trang chủ</label>
                    <select name="featured" class="form-control">
                        <option value="1" <?= (int)$product['featured'] ? 'selected' : '' ?>>Có</option>
                        <option value="0" <?= !(int)$product['featured'] ? 'selected' : '' ?>>Không</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Hình ảnh sản phẩm</label>
                <input type="text" name="image_url" class="form-control" value="<?= htmlspecialchars($product['image']) ?>" placeholder="https://example.com/image.jpg">
                <small style="color:#64748b;">Hoặc upload ảnh từ máy tính:</small>
                <input type="file" name="image_file" class="form-control" style="margin-top:6px;">
                <?php if (!empty($product['image'])): ?>
                    <div style="margin-top:10px;">
                        <img src="<?= htmlspecialchars($product['image']) ?>" style="max-height:120px; border-radius:8px; border:1px solid #e2e8f0;">
                    </div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Lưu sản phẩm</button>
                <a href="<?= htmlspecialchars($returnUrl) ?>" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
</div>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
