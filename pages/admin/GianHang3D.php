<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$tieude = 'Gian hàng 3D / Mô hình | Điện Máy Hiếu';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");

$type = '3d';
$view = in_array($_GET['view'] ?? '', ['categories', 'products']) ? ($_GET['view'] ?? 'products') : 'products';
$catFilter = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;

$categories = $DMH->get_list("SELECT * FROM `product_categories` WHERE `type` = '$type' ORDER BY `sort_order`, `name` ");
if (!is_array($categories)) $categories = [];

$editProduct = null;
if ($editId > 0) {
    $editProduct = $DMH->get_row("SELECT * FROM `products` WHERE `id` = $editId AND `type` = '$type'");
    if (!$editProduct) { $editId = 0; }
}

$whereProduct = "`type` = '$type'";
if ($catFilter > 0) $whereProduct .= " AND `category_id` = $catFilter";
$products = $DMH->get_list("SELECT p.*, c.name AS cat_name FROM `products` p LEFT JOIN `product_categories` c ON c.id = p.category_id WHERE $whereProduct ORDER BY p.id DESC LIMIT 200");
if (!is_array($products)) $products = [];

function flash() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $color = ($f['type'] == 'success') ? '#16a34a' : '#dc2626';
        $bg = ($f['type'] == 'success') ? '#dcfce7' : '#fee2e2';
        echo '<div style="padding:12px 16px;border-radius:8px;margin-bottom:16px;background:'.$bg.';color:'.$color.';font-weight:700;">'.htmlspecialchars($f['msg']).'</div>';
    }
}
?>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;"><i class="fa-solid fa-cube" style="color:#0ea5e9;"></i> Gian hàng 3D / Mô hình Store</h2>

<div class="tabs">
    <a href="?view=products" class="<?= $view == 'products' ? 'active' : '' ?>">Sản phẩm</a>
    <a href="?view=categories" class="<?= $view == 'categories' ? 'active' : '' ?>">Danh mục</a>
</div>

<?php if ($view == 'products'): ?>

<div class="card" style="margin-bottom:24px;">
    <div class="card-header">
        <h3><i class="fa-solid <?= $editProduct ? 'fa-pen' : 'fa-plus' ?>"></i> <?= $editProduct ? 'Sửa sản phẩm 3D' : 'Thêm sản phẩm 3D mới' ?></h3>
        <small style="color:#64748b;">Điền đầy đủ thông tin bên dưới, dán link ảnh/video hoặc upload ảnh từ máy tính.</small>
    </div>
    <div class="card-body">
        <form method="POST" action="/controller/admin/SaveProduct.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $editProduct ? (int)$editProduct['id'] : '' ?>">
            <input type="hidden" name="type" value="3d">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                <div class="form-group">
                    <label>Tên sản phẩm <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= $editProduct ? htmlspecialchars($editProduct['name']) : '' ?>" required placeholder="VD: Mô hình linh kiện 3D">
                </div>
                <div class="form-group">
                    <label>Slug (tùy chọn)</label>
                    <input type="text" name="slug" class="form-control" value="<?= $editProduct ? htmlspecialchars($editProduct['slug']) : '' ?>" placeholder="mo-hinh-linh-kien-3d">
                </div>
                <div class="form-group">
                    <label>Danh mục <span style="color:#dc2626;">*</span></label>
                    <select name="category_id" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int)$c['id'] ?>" <?= ($editProduct && $editProduct['category_id'] == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Giá bán (VNĐ)</label>
                    <input type="number" name="price" class="form-control" value="<?= $editProduct ? (int)$editProduct['price'] : 0 ?>" min="0">
                </div>
                <div class="form-group">
                    <label>Tồn kho</label>
                    <input type="number" name="stock" class="form-control" value="<?= $editProduct ? (int)$editProduct['stock'] : 0 ?>" min="0">
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="1" <?= ($editProduct && $editProduct['status']) ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="0" <?= ($editProduct && !$editProduct['status']) ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Đưa lên trang chủ?</label>
                    <select name="featured" class="form-control">
                        <option value="0" <?= ($editProduct && !$editProduct['featured']) ? 'selected' : '' ?>>Không</option>
                        <option value="1" <?= ($editProduct && $editProduct['featured']) ? 'selected' : '' ?>>Có</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Hình ảnh (URL)</label>
                    <input type="text" name="image_url" class="form-control" value="<?= $editProduct ? htmlspecialchars($editProduct['image'] ?? '') : '' ?>" placeholder="https://example.com/image.jpg">
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Hoặc Upload ảnh từ máy tính</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*">
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Video sản phẩm (Link Youtube / MP4 / Embed)</label>
                    <input type="text" name="video" class="form-control" value="<?= $editProduct ? htmlspecialchars($editProduct['video'] ?? '') : '' ?>" placeholder="https://www.youtube.com/watch?v=... hoặc link mp4">
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Mô tả chi tiết sản phẩm 3D</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả chi tiết sản phẩm 3D..."><?= $editProduct ? htmlspecialchars($editProduct['description'] ?? '') : '' ?></textarea>
                </div>
            </div>
            <div style="margin-top:16px; display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> <?= $editProduct ? 'Cập nhật sản phẩm' : 'Lưu & Đăng sản phẩm' ?></button>
                <?php if ($editProduct): ?>
                    <a href="?view=products" class="btn btn-secondary"><i class="fa-solid fa-plus"></i> Thêm mới</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Danh sách sản phẩm 3D</h3>
        <form method="GET" style="display:flex; gap:8px;">
            <input type="hidden" name="view" value="products">
            <select name="cat" class="form-control" style="width:auto;">
                <option value="0">Tất cả danh mục</option>
                <?php foreach ($categories as $c) {
                    echo '<option value="' . (int)$c['id'] . '"' . ($catFilter == $c['id'] ? ' selected' : '') . '>' . htmlspecialchars($c['name']) . '</option>';
                } ?>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-filter"></i> Lọc</button>
        </form>
    </div>
    <div class="card-body" style="padding:0;">
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá bán</th>
                        <th>Tồn kho</th>
                        <th>Media</th>
                        <th>Nổi bật</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php flash(); ?>
                <?php if (empty($products)): ?>
                    <tr><td colspan="10" style="text-align:center; color:#64748b; padding:30px;">Chưa có sản phẩm nào. Hãy thêm sản phẩm ở form bên trên.</td></tr>
                <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?= (int)$p['id'] ?></td>
                        <td>
                            <?php if (!empty($p['image'])): ?>
                                <img src="<?= htmlspecialchars($p['image']) ?>" style="width:60px; height:60px; object-fit:cover; border-radius:8px; border:1px solid #e2e8f0;">
                            <?php else: ?>
                                <span style="color:#94a3b8; font-size:12px;">Chưa có hình</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($p['name']) ?></strong>
                            <?php if (!empty($p['slug'])): ?>
                                <div style="font-size:11px; color:#64748b;"><?= htmlspecialchars($p['slug']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p['cat_name'] ?? '-') ?></td>
                        <td><strong style="color:#dc2626;"><?= number_format((int)$p['price']) ?>đ</strong></td>
                        <td><?= (int)$p['stock'] ?></td>
                        <td>
                            <?php if (!empty($p['video'])): ?>
                                <span class="badge" style="background:#8b5cf6; color:#fff;" title="<?= htmlspecialchars($p['video']) ?>"><i class="fa-solid fa-video"></i> Video</span>
                            <?php else: ?>
                                <span style="color:#cbd5e1;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($p['featured'])): ?>
                                <span class="badge badge-warning"><i class="fa-solid fa-star"></i> Trang chủ</span>
                            <?php else: ?>
                                <span class="badge badge-pending">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($p['status'])): ?>
                                <span class="badge badge-completed">Hiển thị</span>
                            <?php else: ?>
                                <span class="badge badge-cancelled">Ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?view=products&edit=<?= (int)$p['id'] ?>" class="btn btn-info btn-sm"><i class="fa-solid fa-pen"></i> Sửa</a>
                            <button class="btn btn-warning btn-sm" onclick="toggleFeatured(<?= (int)$p['id'] ?>, <?= (int)$p['featured'] ?>)"><i class="fa-solid fa-star"></i> <?= $p['featured'] ? 'Gỡ' : 'Nổi bật' ?></button>
                            <button class="btn btn-danger btn-sm" onclick="deleteProduct(<?= (int)$p['id'] ?>)"><i class="fa-solid fa-trash"></i> Xóa</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleFeatured(id, current) {
    const action = current ? 'gỡ khỏi' : 'đưa lên';
    if (!confirm('Xác nhận ' + action + ' trang chủ?')) return;
    fetch('/controller/admin/ToggleFeatured.php?id=' + id)
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') location.reload();
            else alert(res.msg || 'Lỗi kết nối');
        })
        .catch(() => alert('Lỗi kết nối'));
}
function deleteProduct(id) {
    if (!confirm('Xác nhận xóa sản phẩm này?')) return;
    fetch('/controller/admin/DeleteProduct.php?id=' + id)
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') location.reload();
            else alert(res.msg || 'Lỗi kết nối');
        })
        .catch(() => alert('Lỗi kết nối'));
}
</script>

<?php else: ?>

<!-- Categories view -->
<div class="card">
    <div class="card-header">
        <h3>Danh mục Gian hàng 3D</h3>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('catModal').classList.add('active')"><i class="fa-solid fa-plus"></i> Thêm danh mục</button>
    </div>
    <div class="card-body" style="padding:0;">
        <div style="overflow-x:auto;">
            <table class="table">
                <thead><tr><th>ID</th><th>Tên danh mục</th><th>Slug</th><th>Thứ tự</th><th>Thao tác</th></tr></thead>
                <tbody>
                <?php foreach ($categories as $c): ?>
                    <tr data-id="<?= (int)$c['id'] ?>" data-name="<?= htmlspecialchars($c['name']) ?>" data-slug="<?= htmlspecialchars($c['slug']) ?>" data-sort="<?= (int)$c['sort_order'] ?>">
                        <td><?= (int)$c['id'] ?></td>
                        <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                        <td><?= htmlspecialchars($c['slug']) ?></td>
                        <td><?= (int)$c['sort_order'] ?></td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="editCat(this)"><i class="fa-solid fa-pen"></i> Sửa</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteCat(<?= (int)$c['id'] ?>)"><i class="fa-solid fa-trash"></i> Xóa</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-backdrop" id="catModal">
    <div class="modal-box">
        <h3>Danh mục Gian hàng 3D</h3>
        <form id="catForm" method="POST" action="/controller/admin/SaveCategory.php">
            <input type="hidden" name="id" id="catId">
            <input type="hidden" name="type" value="3d">
            <div class="form-group">
                <label>Tên danh mục *</label>
                <input type="text" name="name" id="catName" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Slug (tùy chọn)</label>
                <input type="text" name="slug" id="catSlug" class="form-control" placeholder="danh-muc-abc">
            </div>
            <div class="form-group">
                <label>Thứ tự</label>
                <input type="number" name="sort_order" id="catSort" class="form-control" value="0">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-modal-secondary" onclick="document.getElementById('catModal').classList.remove('active')">Đóng</button>
                <button type="submit" class="btn-modal-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCat(btn) {
    const row = btn.closest('tr');
    document.getElementById('catId').value = row.dataset.id;
    document.getElementById('catName').value = row.dataset.name;
    document.getElementById('catSlug').value = row.dataset.slug;
    document.getElementById('catSort').value = row.dataset.sort;
    document.getElementById('catModal').classList.add('active');
}
function deleteCat(id) {
    if (!confirm('Xác nhận xóa danh mục này?')) return;
    fetch('/controller/admin/DeleteCategory.php?id=' + id + '&type=3d')
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') location.reload();
            else alert(res.msg || 'Lỗi kết nối');
        })
        .catch(() => alert('Lỗi kết nối'));
}
</script>

<?php endif; ?>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
