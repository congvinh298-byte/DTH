<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

if (!isset($DMH)) {
    $DMH = new DMH();
}

$title = "Điện Máy & Gia Dụng - Điện Máy Hiếu";
require_once(__DIR__."/Head.php");
require_once(__DIR__."/Header.php");

$products = $DMH->get_list("SELECT p.*, c.name AS category_name FROM `products` p LEFT JOIN `product_categories` c ON c.id = p.category_id WHERE p.`type` = 'dienmay' AND p.`status` = 1 ORDER BY p.`featured` DESC, p.`id` DESC LIMIT 100");
if (!is_array($products)) {
    $products = [];
}
?>

<main>
    <div class="wrap storefront fade-in">
        <section class="section" id="products">
            <div class="title" style="margin-top: 40px;">
                <h2>Danh Mục: Điện Máy, Gia Dụng & Lạnh</h2>
                <span class="muted">Khám phá các sản phẩm nổi bật</span>
            </div>
            
            <div style="margin-bottom: 24px;">
                <input id="catSearchInput" type="text" placeholder="Lọc nhanh sản phẩm..." style="width: 100%; max-width: 400px; padding: 12px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: white;">
            </div>

            <div class="grid" id="productGrid">
                <?php if (empty($products)): ?>
                    <div class="empty">
                        <i class="fa-solid fa-box-open"></i>
                        Hiện chưa có sản phẩm nào
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <?php
                        $name = isset($p['name']) ? (string)$p['name'] : '';
                        $category = $p['category_name'] ? (string)$p['category_name'] : 'Điện Máy & Gia Dụng';
                        $image = isset($p['image']) ? (string)$p['image'] : '';
                        $price = isset($p['price']) ? (float)$p['price'] : 0;
                        ?>
                        <article class="product" data-name="<?= htmlspecialchars(strtolower($name)) ?>" data-category="<?= htmlspecialchars(strtolower($category)) ?>">
                            <div class="img">
                                <?php if ($image !== ''): ?>
                                    <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>" onerror="this.parentNode.textContent='Chưa có ảnh'">
                                <?php else: ?>
                                    <div style="color: var(--muted); font-size: 13px;">Chưa có ảnh</div>
                                <?php endif; ?>
                            </div>
                            <div class="body">
                                <div class="cat"><?= htmlspecialchars($category) ?></div>
                                <div class="name" title="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></div>
                                <div class="price"><?= number_format($price, 0, ',', '.') ?>đ</div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 10px;">
                                    <button onclick="addToCart(<?= (int)$p['id'] ?>)" class="btn outline" style="width: 100%; padding: 8px; font-size: 13px; border-color: rgba(255,255,255,0.2);"><i class="fa-solid fa-cart-plus"></i> Thêm giỏ</button>
                                    <button onclick="buyNow(<?= (int)$p['id'] ?>)" class="btn accent" style="width: 100%; padding: 8px; font-size: 13px;"><i class="fa-solid fa-bolt"></i> Mua Ngay</button>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<script>
'use strict';
const cards = Array.from(document.querySelectorAll('.product'));
const searchInput = document.getElementById('catSearchInput');

function filterProducts() {
    const q = String(searchInput.value || '').toLowerCase();
    cards.forEach(card => {
        const okSearch = !q || card.dataset.name.indexOf(q) !== -1 || card.dataset.category.indexOf(q) !== -1;
        card.style.display = okSearch ? '' : 'none';
    });
}

if(searchInput) {
    searchInput.addEventListener('input', filterProducts);
}
</script>

<?php require_once(__DIR__."/Footer.php"); ?>
