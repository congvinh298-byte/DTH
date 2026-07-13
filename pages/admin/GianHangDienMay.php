<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$tieude = 'Gian hang dien may | Dien May Hieu';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");

$type = 'dienmay';
$view = in_array($_GET['view'] ?? '', ['categories', 'products']) ? ($_GET['view'] ?? 'products') : 'products';
$catFilter = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

$categories = $DMH->get_list("SELECT * FROM `product_categories` WHERE `type` = '$type' ORDER BY `sort_order`, `name`");

$whereProduct = "`type` = '$type'";
if ($catFilter > 0) $whereProduct .= " AND `category_id` = $catFilter";
$products = $DMH->get_list("SELECT p.*, c.name AS cat_name FROM `products` p LEFT JOIN `product_categories` c ON c.id = p.category_id WHERE $whereProduct ORDER BY p.id DESC LIMIT 200");
?>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;"><i class="fa-solid fa-plug" style="color:#0ea5e9;"></i> Gian hang dien may</h2>

<div class="tabs">
    <a href="?view=products" class="<?= $view == 'products' ? 'active' : '' ?>">San pham</a>
    <a href="?view=categories" class="<?= $view == 'categories' ? 'active' : '' ?>">Danh muc</a>
</div>

<?php if ($view == 'categories'): ?>

<div class="card">
    <div class="card-header">
        <h3>Danh muc dien may</h3>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('catModal').classList.add('active')"><i class="fa-solid fa-plus"></i> Them danh muc</button>
    </div>
    <div class="card-body" style="padding:0;">
        <div style="overflow-x:auto;">
            <table class="table">
                <thead><tr><th>ID</th><th>Ten danh muc</th><th>Slug</th><th>Thu tu</th><th>Thao tac</th></tr></thead>
                <tbody>
                <?php foreach ($categories as $c): ?>
                    <tr data-id="<?= (int)$c['id'] ?>" data-name="<?= htmlspecialchars($c['name']) ?>" data-slug="<?= htmlspecialchars($c['slug']) ?>" data-sort="<?= (int)$c['sort_order'] ?>">
                        <td><?= (int)$c['id'] ?></td>
                        <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                        <td><?= htmlspecialchars($c['slug']) ?></td>
                        <td><?= (int)$c['sort_order'] ?></td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="editCat(this)"><i class="fa-solid fa-pen"></i> Sua</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteCat(<?= (int)$c['id'] ?>)"><i class="fa-solid fa-trash"></i> Xoa</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php else: ?>

<div class="card">
    <div class="card-header">
        <h3>San pham dien may</h3>
        <div style="display:flex; gap:10px;">
            <form method="GET" style="display:flex; gap:6px;">
                <input type="hidden" name="view" value="products">
                <select name="cat" class="form-control" style="width:auto;">
                    <option value="0">Tat ca danh muc</option>
                    <?php foreach ($categories as $c) {
                        echo '<option value="' . (int)$c['id'] . '"' . ($catFilter == $c['id'] ? ' selected' : '') . '>' . htmlspecialchars($c['name']) . '</option>';
                    } ?>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Loc</button>
            </form>
            <button class="btn btn-primary btn-sm" onclick="openProductModal()"><i class="fa-solid fa-plus"></i> Them san pham</button>
        </div>
    </div>
    <div class="card-body" style="padding:0;">
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hinh</th>
                        <th>Ten san pham</th>
                        <th>Danh muc</th>
                        <th>Gia</th>
                        <th>Ton kho</th>
                        <th>Noi bat</th>
                        <th>Trang thai</th>
                        <th>Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $p): ?>
                    <tr data-id="<?= (int)$p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>" data-slug="<?= htmlspecialchars($p['slug']) ?>" data-cat="<?= (int)$p['category_id'] ?>" data-price="<?= (int)$p['price'] ?>" data-stock="<?= (int)$p['stock'] ?>" data-desc="<?= htmlspecialchars($p['description'] ?? '') ?>" data-image="<?= htmlspecialchars($p['image'] ?? '') ?>" data-status="<?= (int)$p['status'] ?>" data-featured="<?= (int)$p['featured'] ?>">
                        <td><?= (int)$p['id'] ?></td>
                        <td>
                            <?php if (!empty($p['image'])): ?>
                                <img src="<?= htmlspecialchars($p['image']) ?>" style="width:50px; height:50px; object-fit:cover; border-radius:6px;">
                            <?php else: ?>
                                <span style="color:#94a3b8;">Khong co hinh</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                        <td><?= htmlspecialchars($p['cat_name'] ?? '-') ?></td>
                        <td><?= number_format((int)$p['price']) ?>d</td>
                        <td><?= (int)$p['stock'] ?></td>
                        <td>
                            <?php if (!empty($p['featured'])): ?>
                                <span class="badge badge-warning"><i class="fa-solid fa-star"></i> Trang chu</span>
                            <?php else: ?>
                                <span class="badge badge-pending">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($p['status'])): ?>
                                <span class="badge badge-completed">Hien thi</span>
                            <?php else: ?>
                                <span class="badge badge-cancelled">An</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="editProduct(this)"><i class="fa-solid fa-pen"></i> Sua</button>
                            <button class="btn btn-warning btn-sm" onclick="toggleFeatured(<?= (int)$p['id'] ?>, <?= (int)$p['featured'] ?>)"><i class="fa-solid fa-star"></i> <?= $p['featured'] ? 'Gỡ' : 'Nổi bật' ?></button>
                            <button class="btn btn-danger btn-sm" onclick="deleteProduct(<?= (int)$p['id'] ?>)"><i class="fa-solid fa-trash"></i> Xoa</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php endif; ?>

<!-- Category Modal -->
<div class="modal-backdrop" id="catModal">
    <div class="modal-box">
        <h3>Danh muc dien may</h3>
        <form id="catForm" method="POST" action="/controller/admin/SaveCategory.php">
            <input type="hidden" name="id" id="catId">
            <input type="hidden" name="type" value="dienmay">
            <div class="form-group">
                <label>Ten danh muc</label>
                <input type="text" name="name" id="catName" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Slug (tuy chon)</label>
                <input type="text" name="slug" id="catSlug" class="form-control" placeholder="tv, tu-lanh...">
            </div>
            <div class="form-group">
                <label>Thu tu</label>
                <input type="number" name="sort_order" id="catSort" class="form-control" value="0">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-modal-secondary" onclick="document.getElementById('catModal').classList.remove('active')">Dong</button>
                <button type="submit" class="btn-modal-primary">Luu</button>
            </div>
        </form>
    </div>
</div>

<!-- Product Modal -->
<div class="modal-backdrop" id="productModal">
    <div class="modal-box" style="max-width:640px;">
        <h3>San pham dien may</h3>
        <form id="productForm" method="POST" action="/controller/admin/SaveProduct.php" enctype="multipart/form-data">
            <input type="hidden" name="id" id="productId">
            <input type="hidden" name="type" value="dienmay">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px;">
                <div class="form-group">
                    <label>Ten san pham</label>
                    <input type="text" name="name" id="productName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Slug (tuy chon)</label>
                    <input type="text" name="slug" id="productSlug" class="form-control">
                </div>
                <div class="form-group">
                    <label>Danh muc</label>
                    <select name="category_id" id="productCategory" class="form-control" required>
                        <option value="">-- Chon danh muc --</option>
                        <?php foreach ($categories as $c) {
                            echo '<option value="' . (int)$c['id'] . '">' . htmlspecialchars($c['name']) . '</option>';
                        } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Gia (VND)</label>
                    <input type="number" name="price" id="productPrice" class="form-control" value="0" min="0">
                </div>
                <div class="form-group">
                    <label>Ton kho</label>
                    <input type="number" name="stock" id="productStock" class="form-control" value="0" min="0">
                </div>
                <div class="form-group">
                    <label>Trang thai</label>
                    <select name="status" id="productStatus" class="form-control">
                        <option value="1">Hien thi</option>
                        <option value="0">An</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Dua len trang chu</label>
                    <select name="featured" id="productFeatured" class="form-control">
                        <option value="0">Khong</option>
                        <option value="1">Co</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Hinh anh (URL hoac upload)</label>
                <input type="text" name="image_url" id="productImage" class="form-control" placeholder="https://...">
                <input type="file" name="image_file" class="form-control" style="margin-top:8px;">
            </div>
            <div class="form-group">
                <label>Mo ta</label>
                <textarea name="description" id="productDesc" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-modal-secondary" onclick="document.getElementById('productModal').classList.remove('active')">Dong</button>
                <button type="submit" class="btn-modal-primary">Luu</button>
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
    Swal.fire({
        title: 'Xoa danh muc?',
        text: 'Han dong nay khong the hoan tac.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xoa',
        cancelButtonText: 'Huy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/controller/admin/DeleteCategory.php?id=' + id + '&type=dienmay', { method: 'GET' })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') Swal.fire('Thanh cong', res.msg, 'success').then(() => location.reload());
                else Swal.fire('Loi', res.msg, 'error');
            })
            .catch(() => Swal.fire('Loi', 'Khong the ket noi', 'error'));
        }
    });
}
function openProductModal() {
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    document.getElementById('productModal').classList.add('active');
}
function editProduct(btn) {
    const row = btn.closest('tr');
    document.getElementById('productId').value = row.dataset.id;
    document.getElementById('productName').value = row.dataset.name;
    document.getElementById('productSlug').value = row.dataset.slug;
    document.getElementById('productCategory').value = row.dataset.cat;
    document.getElementById('productPrice').value = row.dataset.price;
    document.getElementById('productStock').value = row.dataset.stock;
    document.getElementById('productImage').value = row.dataset.image;
    document.getElementById('productDesc').value = row.dataset.desc;
    document.getElementById('productStatus').value = row.dataset.status;
    document.getElementById('productModal').classList.add('active');
}
function deleteProduct(id) {
    Swal.fire({
        title: 'Xoa san pham?',
        text: 'Han dong nay khong the hoan tac.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xoa',
        cancelButtonText: 'Huy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/controller/admin/DeleteProduct.php?id=' + id, { method: 'GET' })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') Swal.fire('Thanh cong', res.msg, 'success').then(() => location.reload());
                else Swal.fire('Loi', res.msg, 'error');
            })
            .catch(() => Swal.fire('Loi', 'Khong the ket noi', 'error'));
        }
    });
}
function toggleFeatured(id, current) {
    const action = current ? 'gỡ' : 'đưa lên';
    if (!confirm('Xac nhan ' + action + ' trang chu?')) return;
    fetch('/controller/admin/ToggleFeatured.php?id=' + id)
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') location.reload();
        else alert(res.msg);
    })
    .catch(() => alert('Loi ket noi'));
}
</script>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
