<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$type = '3d';
$title = 'Gian hàng mô hình 3D';
$icon = 'fa-cube';
$returnPath = '/pages/admin/GianHang3D.php';
$returnUrl = urlencode($returnPath);

$view = in_array($_GET['view'] ?? '', ['categories', 'products']) ? ($_GET['view'] ?? 'products') : 'products';
$catFilter = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

$categories = $DMH->get_list("SELECT * FROM `product_categories` WHERE `type` = '$type' ORDER BY `sort_order`, `name`");

$whereProduct = "p.`type` = '$type'";
if ($catFilter > 0) $whereProduct .= " AND p.`category_id` = $catFilter";
$products = $DMH->get_list("SELECT p.*, c.name AS cat_name FROM `products` p LEFT JOIN `product_categories` c ON c.id = p.category_id WHERE $whereProduct ORDER BY p.id DESC LIMIT 200");

$tieude = 'Gian hàng mô hình 3D | Điện Máy Hiếu';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");
?>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;">
    <i class="fa-solid <?= $icon ?>" style="color:#0ea5e9;"></i> <?= htmlspecialchars($title) ?>
</h2>

<div class="tabs" style="margin-bottom:20px;">
    <a href="?view=products" class="<?= $view == 'products' ? 'active' : '' ?>">Sản phẩm</a>
    <a href="?view=categories" class="<?= $view == 'categories' ? 'active' : '' ?>">Danh mục</a>
</div>

<?php if ($view == 'categories'): ?>

<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3><i class="fa-solid fa-folder-open"></i> Danh sách danh mục</h3>
        <a href="/pages/admin/DanhMucForm.php?type=<?= $type ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Thêm danh mục</a>
    </div>
    <div class="card-body" style="padding:0;">
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên danh mục</th>
                        <th>Slug</th>
                        <th>Thứ tự</th>
                        <th style="width:160px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($categories)): ?>
                    <tr><td colspan="5" style="text-align:center; color:#64748b; padding:30px;">Chưa có danh mục nào.</td></tr>
                <?php else: foreach ($categories as $c): ?>
                    <tr>
                        <td><?= (int)$c['id'] ?></td>
                        <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                        <td><?= htmlspecialchars($c['slug']) ?></td>
                        <td><?= (int)$c['sort_order'] ?></td>
                        <td style="display:flex; gap:6px;">
                            <a href="/pages/admin/DanhMucForm.php?type=<?= $type ?>&id=<?= (int)$c['id'] ?>" class="btn btn-info btn-sm"><i class="fa-solid fa-pen"></i> Sửa</a>
                            <form method="POST" action="/controller/admin/XoaDanhMuc.php" style="display:inline;" onsubmit="return confirm('Xóa danh mục này?');">
                                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                                <input type="hidden" name="type" value="<?= $type ?>">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php else: ?>

<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <h3><i class="fa-solid fa-boxes-stacked"></i> Danh sách sản phẩm</h3>
        <div style="display:flex; gap:8px; align-items:center;">
            <form method="GET" style="display:flex; gap:6px;">
                <input type="hidden" name="view" value="products">
                <select name="cat" class="form-control" style="width:auto;">
                    <option value="0">Tất cả danh mục</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= $catFilter == (int)$c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-filter"></i> Lọc</button>
            </form>
            <a href="/pages/admin/SanPhamForm.php?type=<?= $type ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Thêm sản phẩm</a>
        </div>
    </div>
    <div class="card-body" style="padding:0;">
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hình</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Tồn kho</th>
                        <th>Trang chủ</th>
                        <th>Trạng thái</th>
                        <th style="width:200px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($products)): ?>
                    <tr><td colspan="9" style="text-align:center; color:#64748b; padding:30px;">Chưa có sản phẩm nào. <a href="/pages/admin/SanPhamForm.php?type=<?= $type ?>">Thêm sản phẩm ngay</a>.</td></tr>
                <?php else: foreach ($products as $p): ?>
                    <tr>
                        <td><?= (int)$p['id'] ?></td>
                        <td>
                            <?php if (!empty($p['image'])): ?>
                                <img src="<?= htmlspecialchars($p['image']) ?>" style="width:56px; height:56px; object-fit:cover; border-radius:8px; border:1px solid #e2e8f0;">
                            <?php else: ?>
                                <span style="color:#94a3b8;">Không có hình</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                        <td><?= htmlspecialchars($p['cat_name'] ?? '-') ?></td>
                        <td><strong style="color:#dc2626;"><?= number_format((int)$p['price']) ?>đ</strong></td>
                        <td><?= (int)$p['stock'] ?></td>
                        <td>
                            <?php if (!empty($p['featured'])): ?>
                                <span class="badge badge-warning"><i class="fa-solid fa-star"></i> Nổi bật</span>
                            <?php else: ?>
                                <span class="badge badge-default">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($p['status'])): ?>
                                <span class="badge badge-completed">Hiển thị</span>
                            <?php else: ?>
                                <span class="badge badge-cancelled">Ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td style="display:flex; gap:6px; flex-wrap:wrap;">
                            <a href="/pages/admin/SanPhamForm.php?type=<?= $type ?>&id=<?= (int)$p['id'] ?>" class="btn btn-info btn-sm"><i class="fa-solid fa-pen"></i> Sửa</a>
                            <a href="/controller/admin/ToggleFeatured.php?id=<?= (int)$p['id'] ?>&return=<?= $returnUrl ?>" class="btn btn-warning btn-sm">
                                <i class="fa-solid <?= !empty($p['featured']) ? 'fa-star-half-stroke' : 'fa-star' ?>"></i> <?= !empty($p['featured']) ? 'Gỡ' : 'Nổi bật' ?>
                            </a>
                            <form method="POST" action="/controller/admin/XoaSanPham.php" style="display:inline;" onsubmit="return confirm('Xóa sản phẩm này?');">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <input type="hidden" name="type" value="<?= $type ?>">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php endif; ?>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
