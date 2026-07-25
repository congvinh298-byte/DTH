<?php
define("IN_SITE", true);
require_once(__DIR__."/../core/config.php");
require_once(__DIR__."/../core/function.php");
CheckAdmin();

$act = $_GET['act'] ?? 'products';
$type = in_array($_GET['type'] ?? '', ['dienmay','3d']) ? $_GET['type'] : 'dienmay';
$editId = (int)($_GET['edit'] ?? 0);
$catFilter = (int)($_GET['cat'] ?? 0);

$tieude = 'Quản lý hàng hóa | Điện Máy Hiếu Admin';

// Lấy danh sách danh mục
$categories = $DMH->get_list("SELECT * FROM `product_categories` WHERE `type` = '$type' ORDER BY `sort_order`, `name` ");
if (!is_array($categories)) $categories = [];

// Xử lý sửa sản phẩm
$editProduct = null;
if ($editId > 0) {
    $editProduct = $DMH->get_row("SELECT * FROM `products` WHERE `id` = $editId AND `type` = '$type'");
    if (!$editProduct) $editId = 0;
}

// Lấy danh sách sản phẩm
$whereProduct = "`type` = '$type'";
if ($catFilter > 0) $whereProduct .= " AND `category_id` = $catFilter";
$products = $DMH->get_list("SELECT p.*, c.name AS cat_name FROM `products` p LEFT JOIN `product_categories` c ON c.id = p.category_id WHERE $whereProduct ORDER BY p.id DESC LIMIT 200");
if (!is_array($products)) $products = [];

// Flash message
function flashMsg() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $color = ($f['type'] == 'success') ? '#10b981' : '#ef4444';
        $bg = ($f['type'] == 'success') ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)';
        echo '<div style="padding:12px 16px;border-radius:10px;margin-bottom:20px;background:'.$bg.';color:'.$color.';border:1px solid '.$color.'40;font-weight:700;display:flex;align-items:center;gap:8px;">'.htmlspecialchars($f['msg']).'</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tieude) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: #0b1120; color: #f8fafc; display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 260px; background: #0f172a; border-right: 1px solid rgba(255,255,255,0.08); display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-brand { padding: 20px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar-brand img { width: 36px; height: 36px; border-radius: 8px; }
        .sidebar-brand-title { font-weight: 800; font-size: 16px; color: #fff; }
        .sidebar-menu { list-style: none; padding: 12px 0; flex: 1; overflow-y: auto; }
        .sidebar-menu li a { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 600; transition: 0.2s; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: rgba(56, 189, 248, 0.1); color: #38bdf8; border-left: 3px solid #38bdf8; }
        .sidebar-menu li a.logout { color: #f87171; }
        .sidebar-menu li a.logout:hover { background: rgba(239, 68, 68, 0.1); }
        .sidebar-section { padding: 12px 20px 6px; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Main Content */
        .main-wrapper { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar { height: 64px; background: #0f172a; border-bottom: 1px solid rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; }
        .topbar-title { font-size: 18px; font-weight: 800; color: #fff; }
        .user-info { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #94a3b8; }
        .user-info strong { color: #38bdf8; }
        .content { padding: 24px; flex: 1; overflow-y: auto; }

        /* Cards & Forms */
        .card { background: #1e293b; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 24px; margin-bottom: 24px; }
        .card-title { font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 700; color: #cbd5e1; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 11px 14px; background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff; font-size: 14px; }
        .form-control:focus { outline: none; border-color: #38bdf8; }
        .btn { padding: 10px 18px; border-radius: 10px; border: none; font-weight: 700; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition: 0.2s; }
        .btn-primary { background: #38bdf8; color: #0f172a; }
        .btn-primary:hover { background: #7dd3fc; }
        .btn-secondary { background: rgba(255,255,255,0.1); color: #fff; }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-warning { background: #f59e0b; color: #0f172a; }
        .btn-info { background: #8b5cf6; color: #fff; }
        .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 6px; }

        /* Table */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        th { padding: 12px 16px; background: #0f172a; color: #94a3b8; font-weight: 700; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid rgba(255,255,255,0.08); }
        td { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #e2e8f0; vertical-align: middle; }
        tr:hover td { background: rgba(255,255,255,0.02); }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px; }
        .badge-active { background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); }
        .badge-hidden { background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); }
        .badge-featured { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }

        .type-switcher { display: flex; gap: 8px; margin-bottom: 20px; }
        .type-btn { padding: 10px 20px; border-radius: 10px; background: #0f172a; color: #94a3b8; text-decoration: none; font-weight: 700; font-size: 13px; border: 1px solid rgba(255,255,255,0.08); }
        .type-btn.active { background: #38bdf8; color: #0f172a; border-color: #38bdf8; }
    </style>
</head>
<body>

    <!-- Sidebar Menu -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div style="width:36px; height:36px; background:#38bdf8; color:#0f172a; border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:900; font-size:18px;">⚡</div>
            <div>
                <div class="sidebar-brand-title">Điện Máy Hiếu</div>
                <small style="color:#64748b; font-size:11px;">Hệ thống quản trị Admin</small>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="/Admin"><span><i class="fa-solid fa-gauge"></i> Tổng quan</span></a></li>
            
            <li class="sidebar-section">Quản lý hàng hóa</li>
            <li><a href="/admin/index.php?act=products&type=dienmay" class="<?= $act == 'products' && $type == 'dienmay' ? 'active' : '' ?>"><span><i class="fa-solid fa-boxes-stacked"></i> Hàng hóa Điện máy</span></a></li>
            <li><a href="/admin/index.php?act=products&type=3d" class="<?= $act == 'products' && $type == '3d' ? 'active' : '' ?>"><span><i class="fa-solid fa-cube"></i> Hàng hóa Mô hình 3D</span></a></li>
            <li><a href="/admin/index.php?act=categories" class="<?= $act == 'categories' ? 'active' : '' ?>"><span><i class="fa-solid fa-folder-tree"></i> Danh mục sản phẩm</span></a></li>

            <li class="sidebar-section">Đơn hàng & Dịch vụ</li>
            <li><a href="/pages/admin/QuanLyDonHang.php"><span><i class="fa-solid fa-cart-shopping"></i> Đơn hàng bán lẻ</span></a></li>
            <li><a href="/pages/admin/QuanLyDatLich.php"><span><i class="fa-solid fa-calendar-check"></i> Đơn gọi thợ / dịch vụ</span></a></li>
            <li><a href="/pages/admin/QuanLyTho.php"><span><i class="fa-solid fa-wrench"></i> Quản lý CTV / Thợ</span></a></li>
            <li><a href="/pages/admin/QuanLyKhachHang.php"><span><i class="fa-solid fa-users"></i> Quản lý khách hàng</span></a></li>

            <li class="sidebar-section">Hệ thống & Báo cáo</li>
            <li><a href="/pages/admin/BaoCao.php"><span><i class="fa-solid fa-chart-line"></i> Báo cáo doanh thu</span></a></li>
            <li><a href="/pages/admin/CaiDat.php"><span><i class="fa-solid fa-gear"></i> Cài đặt hệ thống</span></a></li>
            <li><a href="/pages/admin/Logout.php" class="logout"><span><i class="fa-solid fa-sign-out-alt"></i> Đăng xuất</span></a></li>
        </ul>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="topbar">
            <div class="topbar-title">📦 Quản lý hàng hóa & Sản phẩm</div>
            <div class="user-info">
                <i class="fa-solid fa-user-shield" style="color:#38bdf8;"></i>
                Xin chào, <strong><?= htmlspecialchars($getUser['name'] ?? $getUser['username']) ?></strong> (Chủ cửa hàng)
            </div>
        </div>

        <div class="content">
            <?php flashMsg(); ?>

            <?php if ($act === 'categories'): ?>

                <!-- View QUẢN LÝ DANH MỤC -->
                <div class="type-switcher">
                    <a href="?act=categories&type=dienmay" class="type-btn <?= $type == 'dienmay' ? 'active' : '' ?>">Danh mục Điện máy</a>
                    <a href="?act=categories&type=3d" class="type-btn <?= $type == '3d' ? 'active' : '' ?>">Danh mục Mô hình 3D</a>
                </div>

                <div class="card">
                    <div class="card-title">
                        <span><i class="fa-solid fa-folder-plus" style="color:#38bdf8;"></i> Danh mục hàng hóa (<?= $type == '3d' ? 'Mô hình 3D' : 'Điện máy' ?>)</span>
                        <button class="btn btn-primary btn-sm" onclick="openCatModal()"><i class="fa-solid fa-plus"></i> Thêm danh mục mới</button>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead><tr><th>ID</th><th>Tên danh mục</th><th>Slug</th><th>Thứ tự</th><th>Thao tác</th></tr></thead>
                            <tbody>
                            <?php if (empty($categories)): ?>
                                <tr><td colspan="5" style="text-align:center; color:#64748b; padding:30px;">Chưa có danh mục nào.</td></tr>
                            <?php else: ?>
                            <?php foreach ($categories as $c): ?>
                                <tr data-id="<?= (int)$c['id'] ?>" data-name="<?= htmlspecialchars($c['name']) ?>" data-slug="<?= htmlspecialchars($c['slug']) ?>" data-sort="<?= (int)$c['sort_order'] ?>">
                                    <td><?= (int)$c['id'] ?></td>
                                    <td><strong style="color:#fff;"><?= htmlspecialchars($c['name']) ?></strong></td>
                                    <td><span style="color:#94a3b8; font-size:12px;"><?= htmlspecialchars($c['slug']) ?></span></td>
                                    <td><?= (int)$c['sort_order'] ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm" onclick="editCat(this)"><i class="fa-solid fa-pen"></i> Sửa</button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteCat(<?= (int)$c['id'] ?>)"><i class="fa-solid fa-trash"></i> Xóa</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Danh Mục -->
                <div id="catModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); z-index:999; align-items:center; justify-content:center; padding:16px;">
                    <div style="background:#1e293b; border:1px solid rgba(255,255,255,0.1); border-radius:16px; padding:24px; width:100%; max-width:420px; color:#fff;">
                        <h3 id="catModalTitle" style="margin-bottom:16px; font-size:16px; color:#38bdf8;">Thêm danh mục hàng hóa</h3>
                        <form method="POST" action="/controller/admin/SaveCategory.php">
                            <input type="hidden" name="id" id="catId">
                            <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                            <div class="form-group">
                                <label>Tên danh mục *</label>
                                <input type="text" name="name" id="catName" class="form-control" placeholder="VD: Tủ lạnh, Máy giặt..." required>
                            </div>
                            <div class="form-group">
                                <label>Slug (tùy chọn)</label>
                                <input type="text" name="slug" id="catSlug" class="form-control" placeholder="tu-lanh">
                            </div>
                            <div class="form-group">
                                <label>Thứ tự sắp xếp</label>
                                <input type="number" name="sort_order" id="catSort" class="form-control" value="0">
                            </div>
                            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('catModal').style.display='none'">Hủy</button>
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Lưu danh mục</button>
                            </div>
                        </form>
                    </div>
                </div>

            <?php else: ?>

                <!-- View QUẢN LÝ SẢN PHẨM / HÀNG HÓA -->
                <div class="type-switcher">
                    <a href="?act=products&type=dienmay" class="type-btn <?= $type == 'dienmay' ? 'active' : '' ?>">⚡ Hàng Điện Máy & Gia Dụng</a>
                    <a href="?act=products&type=3d" class="type-btn <?= $type == '3d' ? 'active' : '' ?>">🎲 Hàng Mô Hình 3D</a>
                </div>

                <!-- Form Thêm / Chỉnh Sửa Sản Phẩm -->
                <div class="card">
                    <div class="card-title">
                        <span><i class="fa-solid <?= $editProduct ? 'fa-pen-to-square' : 'fa-plus-circle' ?>" style="color:#38bdf8;"></i> <?= $editProduct ? 'Chỉnh sửa sản phẩm #' . (int)$editProduct['id'] : 'Thêm sản phẩm hàng hóa mới' ?></span>
                        <?php if ($editProduct): ?>
                            <a href="?act=products&type=<?= $type ?>" class="btn btn-secondary btn-sm"><i class="fa-solid fa-plus"></i> Thêm mới</a>
                        <?php endif; ?>
                    </div>

                    <form method="POST" action="/controller/admin/SaveProduct.php" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $editProduct ? (int)$editProduct['id'] : '' ?>">
                        <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Tên sản phẩm *</label>
                                <input type="text" name="name" class="form-control" value="<?= $editProduct ? htmlspecialchars($editProduct['name']) : '' ?>" required placeholder="VD: Tủ lạnh Samsung Inverter 300L">
                            </div>

                            <div class="form-group">
                                <label>Danh mục phân loại *</label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach ($categories as $c): ?>
                                        <option value="<?= (int)$c['id'] ?>" <?= ($editProduct && $editProduct['category_id'] == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Giá bán (VNĐ)</label>
                                <input type="number" name="price" class="form-control" value="<?= $editProduct ? (int)$editProduct['price'] : 0 ?>" min="0" placeholder="0">
                            </div>

                            <div class="form-group">
                                <label>Số lượng tồn kho</label>
                                <input type="number" name="stock" class="form-control" value="<?= $editProduct ? (int)$editProduct['stock'] : 0 ?>" min="0" placeholder="0">
                            </div>

                            <div class="form-group">
                                <label>Trạng thái kinh doanh</label>
                                <select name="status" class="form-control">
                                    <option value="1" <?= ($editProduct && $editProduct['status']) ? 'selected' : '' ?>>Hiển thị kinh doanh</option>
                                    <option value="0" <?= ($editProduct && !$editProduct['status']) ? 'selected' : '' ?>>Tạm ẩn / Tắt bán</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Đưa lên trang chủ (Nổi bật)?</label>
                                <select name="featured" class="form-control">
                                    <option value="0" <?= ($editProduct && !$editProduct['featured']) ? 'selected' : '' ?>>Không</option>
                                    <option value="1" <?= ($editProduct && $editProduct['featured']) ? 'selected' : '' ?>>Có (Nổi bật)</option>
                                </select>
                            </div>
                        </div>

                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-top:8px;">
                            <div class="form-group">
                                <label>Hình ảnh sản phẩm (Link URL)</label>
                                <input type="text" name="image_url" class="form-control" value="<?= $editProduct ? htmlspecialchars($editProduct['image'] ?? '') : '' ?>" placeholder="https://example.com/hinh-anh.jpg">
                            </div>

                            <div class="form-group">
                                <label>Hoặc Upload hình ảnh từ máy tính</label>
                                <input type="file" name="image_file" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Video giới thiệu sản phẩm (Link Youtube / MP4 / Embed)</label>
                            <input type="text" name="video" class="form-control" value="<?= $editProduct ? htmlspecialchars($editProduct['video'] ?? '') : '' ?>" placeholder="https://www.youtube.com/watch?v=... hoặc link mp4">
                        </div>

                        <div class="form-group">
                            <label>Mô tả chi tiết sản phẩm</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Nhập chi tiết thông số kỹ thuật, bảo hành, quà tặng..."><?= $editProduct ? htmlspecialchars($editProduct['description'] ?? '') : '' ?></textarea>
                        </div>

                        <div style="display:flex; gap:12px; margin-top:16px;">
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> <?= $editProduct ? 'CẬP NHẬT SẢN PHẨM' : 'LƯU & ĐĂNG SẢN PHẨM' ?></button>
                            <?php if ($editProduct): ?>
                                <a href="?act=products&type=<?= $type ?>" class="btn btn-secondary">Hủy bỏ</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Bảng Danh Sách Sản Phẩm -->
                <div class="card">
                    <div class="card-title">
                        <span><i class="fa-solid fa-list-check" style="color:#38bdf8;"></i> Danh sách hàng hóa kinh doanh (<?= $type == '3d' ? 'Mô hình 3D' : 'Điện máy' ?>)</span>
                        
                        <form method="GET" style="display:flex; gap:8px;">
                            <input type="hidden" name="act" value="products">
                            <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                            <select name="cat" class="form-control" style="width:auto; padding:6px 12px; font-size:12px;" onchange="this.form.submit()">
                                <option value="0">Tất cả danh mục</option>
                                <?php foreach ($categories as $c) {
                                    echo '<option value="' . (int)$c['id'] . '"' . ($catFilter == $c['id'] ? ' selected' : '') . '>' . htmlspecialchars($c['name']) . '</option>';
                                } ?>
                            </select>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Hình ảnh</th>
                                    <th>Tên hàng hóa</th>
                                    <th>Danh mục</th>
                                    <th>Giá bán</th>
                                    <th>Tồn kho</th>
                                    <th>Media</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($products)): ?>
                                <tr><td colspan="9" style="text-align:center; color:#64748b; padding:40px;">Chưa có hàng hóa nào trong danh mục này. Hãy thêm ở form phía trên.</td></tr>
                            <?php else: ?>
                            <?php foreach ($products as $p): ?>
                                <tr>
                                    <td>#<?= (int)$p['id'] ?></td>
                                    <td>
                                        <?php if (!empty($p['image'])): ?>
                                            <img src="<?= htmlspecialchars($p['image']) ?>" style="width:50px; height:50px; object-fit:cover; border-radius:8px; border:1px solid rgba(255,255,255,0.1);">
                                        <?php else: ?>
                                            <span style="color:#64748b; font-size:12px;">Chưa có ảnh</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong style="color:#fff; font-size:14px;"><?= htmlspecialchars($p['name']) ?></strong>
                                        <?php if (!empty($p['featured'])): ?>
                                            <span class="badge badge-featured" style="margin-left:6px;"><i class="fa-solid fa-star"></i> Nổi bật</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($p['cat_name'] ?? 'Chưa phân loại') ?></td>
                                    <td><strong style="color:#ef4444; font-size:15px;"><?= number_format((int)$p['price']) ?>đ</strong></td>
                                    <td><span style="font-weight:700; color:<?= (int)$p['stock'] > 0 ? '#10b981' : '#ef4444' ?>;"><?= (int)$p['stock'] ?></span></td>
                                    <td>
                                        <?php if (!empty($p['video'])): ?>
                                            <span class="badge" style="background:#8b5cf6; color:#fff;" title="<?= htmlspecialchars($p['video']) ?>"><i class="fa-solid fa-video"></i> Video</span>
                                        <?php else: ?>
                                            <span style="color:#475569;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($p['status'])): ?>
                                            <span class="badge badge-active">✔ Hiển thị</span>
                                        <?php else: ?>
                                            <span class="badge badge-hidden">✖ Tạm ẩn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display:flex; gap:6px;">
                                            <a href="?act=products&type=<?= $type ?>&edit=<?= (int)$p['id'] ?>" class="btn btn-info btn-sm"><i class="fa-solid fa-pen-to-square"></i> Sửa</a>
                                            <button class="btn btn-danger btn-sm" onclick="deleteProduct(<?= (int)$p['id'] ?>)"><i class="fa-solid fa-trash-can"></i> Xóa</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </div>

    <script>
    function openCatModal() {
        document.getElementById('catId').value = '';
        document.getElementById('catName').value = '';
        document.getElementById('catSlug').value = '';
        document.getElementById('catSort').value = '0';
        document.getElementById('catModalTitle').textContent = 'Thêm danh mục mới';
        document.getElementById('catModal').style.display = 'flex';
    }

    function editCat(btn) {
        const row = btn.closest('tr');
        document.getElementById('catId').value = row.dataset.id;
        document.getElementById('catName').value = row.dataset.name;
        document.getElementById('catSlug').value = row.dataset.slug;
        document.getElementById('catSort').value = row.dataset.sort;
        document.getElementById('catModalTitle').textContent = 'Chỉnh sửa danh mục';
        document.getElementById('catModal').style.display = 'flex';
    }

    function deleteCat(id) {
        if (!confirm('Xác nhận xóa danh mục này? Tất cả sản phẩm thuộc danh mục sẽ chuyển thành Chưa phân loại.')) return;
        fetch('/controller/admin/DeleteCategory.php?id=' + id + '&type=<?= $type ?>')
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') location.reload();
                else alert(res.msg || 'Lỗi khi xóa danh mục');
            })
            .catch(() => alert('Không thể kết nối máy chủ'));
    }

    function deleteProduct(id) {
        if (!confirm('Bạn có chắc chắn muốn XÓA sản phẩm #' + id + ' này không?')) return;
        fetch('/controller/admin/DeleteProduct.php?id=' + id)
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    location.reload();
                } else {
                    alert(res.msg || 'Lỗi khi xóa sản phẩm');
                }
            })
            .catch(() => alert('Không thể kết nối máy chủ'));
    }
    </script>
</body>
</html>
