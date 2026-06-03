<?php
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Ho_Chi_Minh');

/*
 * Dien Tu Hieu - PUBLIC STOREFRONT.
 * IMPORTANT: This file is decoupled from api_master.php.
 * It never loads the API router directly. It only calls api_master.php by JS fetch().
 */

$host = 'localhost';
$db   = 'kwkrbcce_Goixelapvo';
$user = 'kwkrbcce_baocao';
$pass = 'SayTHC369@';

function h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function money_vnd($value)
{
    return number_format((float)$value, 0, ',', '.') . ' VND';
}

function lower_text($value)
{
    $value = (string)$value;
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
}

function column_exists(PDO $pdo, $table, $column)
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function add_column_if_missing(PDO $pdo, $table, $column, $definition)
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', $table) || !preg_match('/^[A-Za-z0-9_]+$/', $column)) {
        return;
    }
    if (!column_exists($pdo, $table, $column)) {
        $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
    }
}

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NULL,
        category VARCHAR(120) NULL,
        image VARCHAR(700) NULL,
        price INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    add_column_if_missing($pdo, 'products', 'name', 'VARCHAR(255) NULL');
    add_column_if_missing($pdo, 'products', 'category', 'VARCHAR(120) NULL');
    add_column_if_missing($pdo, 'products', 'image', 'VARCHAR(700) NULL');
    add_column_if_missing($pdo, 'products', 'price', 'INT NOT NULL DEFAULT 0');
    add_column_if_missing($pdo, 'products', 'created_at', 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');

    $pdo->exec("CREATE TABLE IF NOT EXISTS qr_coupons (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(80) NOT NULL UNIQUE,
        discount_amount INT NOT NULL DEFAULT 0,
        quantity_left INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    add_column_if_missing($pdo, 'qr_coupons', 'code', 'VARCHAR(80) NULL');
    add_column_if_missing($pdo, 'qr_coupons', 'discount_amount', 'INT NOT NULL DEFAULT 0');
    add_column_if_missing($pdo, 'qr_coupons', 'quantity_left', 'INT NOT NULL DEFAULT 0');
    add_column_if_missing($pdo, 'qr_coupons', 'created_at', 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');

    $pdo->exec("CREATE TABLE IF NOT EXISTS job_posts (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        customer_phone VARCHAR(30) NULL,
        issue TEXT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
        tech_target_price INT NOT NULL DEFAULT 0,
        final_price INT NOT NULL DEFAULT 0,
        bot_message_id BIGINT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    add_column_if_missing($pdo, 'job_posts', 'customer_phone', 'VARCHAR(30) NULL');
    add_column_if_missing($pdo, 'job_posts', 'issue', 'TEXT NULL');
    add_column_if_missing($pdo, 'job_posts', 'status', "VARCHAR(30) NOT NULL DEFAULT 'pending'");
    add_column_if_missing($pdo, 'job_posts', 'tech_target_price', 'INT NOT NULL DEFAULT 0');
    add_column_if_missing($pdo, 'job_posts', 'final_price', 'INT NOT NULL DEFAULT 0');
    add_column_if_missing($pdo, 'job_posts', 'bot_message_id', 'BIGINT NULL');
    add_column_if_missing($pdo, 'job_posts', 'created_at', 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');

    $pdo->exec("CREATE TABLE IF NOT EXISTS finances (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        type VARCHAR(40) NOT NULL,
        amount INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    add_column_if_missing($pdo, 'finances', 'type', 'VARCHAR(40) NULL');
    add_column_if_missing($pdo, 'finances', 'amount', 'INT NOT NULL DEFAULT 0');
    add_column_if_missing($pdo, 'finances', 'created_at', 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');

    $pdo->exec("CREATE TABLE IF NOT EXISTS banned_entities (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        ip_or_phone VARCHAR(255) NOT NULL,
        type VARCHAR(30) NOT NULL DEFAULT 'ip',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_banned_entities (ip_or_phone, type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    add_column_if_missing($pdo, 'banned_entities', 'ip_or_phone', 'VARCHAR(255) NULL');
    add_column_if_missing($pdo, 'banned_entities', 'type', "VARCHAR(30) NOT NULL DEFAULT 'ip'");
    add_column_if_missing($pdo, 'banned_entities', 'created_at', 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
} catch (Throwable $e) {
    die('DB Error: ' . $e->getMessage());
}

$products = [];
$productError = '';

try {
    $stmt = $pdo->query('SELECT * FROM products ORDER BY id DESC LIMIT 60');
    $products = $stmt ? $stmt->fetchAll() : [];
} catch (Throwable $e) {
    $productError = 'Không đọc được bảng sản phẩm: ' . $e->getMessage();
    $products = [];
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Điện Tử Hiếu - Storefront</title>
    <style>
        :root{--bg:#f6f7f9;--panel:#fff;--line:#e5e7eb;--text:#172033;--muted:#667085;--brand:#d9362b;--dark:#111827;--ok:#047857}
        *{box-sizing:border-box}
        body{margin:0;background:var(--bg);color:var(--text);font-family:Arial,Helvetica,sans-serif;line-height:1.5}
        a{text-decoration:none;color:inherit}.wrap{width:min(1180px,calc(100% - 32px));margin:0 auto}
        .top{background:var(--dark);color:#e5e7eb;font-size:13px}.top .wrap{min-height:38px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}
        header{background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:10}
        .head{min-height:74px;display:flex;align-items:center;justify-content:space-between;gap:16px}
        .logo{font-size:22px;font-weight:900;color:var(--brand)}.logo span{display:block;font-size:12px;color:var(--muted);font-weight:600}
        .search{display:flex;gap:8px;flex:1;max-width:520px}.search input{width:100%;border:1px solid var(--line);border-radius:8px;padding:11px 12px;font-size:15px}
        button,.btn{border:0;border-radius:8px;background:var(--brand);color:#fff;font-weight:800;padding:11px 16px;cursor:pointer}.btn.dark{background:var(--dark)}
        nav{border-top:1px solid var(--line)}nav .wrap{display:flex;gap:8px;overflow-x:auto;padding:10px 0}nav button{background:#fff;color:#344054;border:1px solid var(--line);white-space:nowrap}nav button.active{border-color:var(--brand);color:var(--brand);background:#fff5f4}
        main{padding:24px 0 42px}.hero{display:grid;grid-template-columns:1.35fr .9fr;gap:18px}.panel{background:var(--panel);border:1px solid var(--line);border-radius:10px;box-shadow:0 10px 28px rgba(15,23,42,.07)}
        .hero-main{padding:28px;background:linear-gradient(120deg,rgba(17,24,39,.94),rgba(169,39,29,.9)),url('logo.jpg') center/cover;color:#fff;min-height:260px;display:flex;flex-direction:column;justify-content:center}.hero-main h1{font-size:clamp(30px,4vw,48px);line-height:1.05;margin:0 0 12px}.hero-main p{max-width:620px;margin:0 0 18px;color:#f3f4f6}
        .qr{padding:18px;display:grid;grid-template-columns:1fr 1fr;gap:12px}.qr div{border:1px solid var(--line);border-radius:8px;padding:12px}.qr h3{margin:0 0 8px;font-size:15px}.qr p{margin:0 0 10px;color:var(--muted);font-size:13px}.qr img{width:150px;height:150px;object-fit:contain;margin:auto;background:#fff;border:1px solid var(--line);padding:6px}
        .section{margin-top:22px}.title{display:flex;align-items:end;justify-content:space-between;gap:12px;margin-bottom:12px}.title h2{margin:0;font-size:23px}.muted{color:var(--muted);font-size:14px}
        .grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.product{background:#fff;border:1px solid var(--line);border-radius:10px;overflow:hidden}.img{height:145px;background:#fff;border-bottom:1px solid var(--line);display:grid;place-items:center;color:#98a2b3}.img img{max-width:100%;max-height:100%;object-fit:contain;padding:10px}.body{padding:12px}.name{font-weight:900;min-height:42px}.cat{color:var(--muted);font-size:13px}.price{color:var(--brand);font-size:17px;font-weight:900}.empty{grid-column:1/-1;background:#fff;border:1px dashed #cbd5e1;border-radius:10px;padding:24px;text-align:center;color:var(--muted)}
        .booking{display:grid;grid-template-columns:1fr .85fr;gap:18px}.card{padding:18px}.form{display:grid;grid-template-columns:1fr 1fr;gap:12px}.field{display:grid;gap:6px}.full{grid-column:1/-1}label{font-size:13px;font-weight:800}input,textarea,select{width:100%;border:1px solid var(--line);border-radius:8px;padding:11px 12px;font:inherit;background:#fff}textarea{min-height:110px;resize:vertical}.status{display:none;margin-top:12px;padding:10px 12px;border-radius:8px}.status.ok{display:block;background:#ecfdf3;color:#047857;border:1px solid #a7f3d0}.status.err{display:block;background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}
        .policies{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}.policy{padding:14px;background:#fff;border:1px solid var(--line);border-radius:8px}.policy h3{margin:0 0 7px}.policy p{margin:0;color:var(--muted);font-size:13px}
        footer{background:var(--dark);color:#d1d5db;padding:24px 0;font-size:13px}
        @media(max-width:900px){.head,.hero,.booking{display:grid;grid-template-columns:1fr}.search{max-width:none}.grid,.policies,.qr{grid-template-columns:repeat(2,1fr)}}@media(max-width:620px){.grid,.policies,.qr,.form{grid-template-columns:1fr}.full{grid-column:auto}.wrap{width:min(100% - 22px,1180px)}}
    </style>
</head>
<body>
<div class="top"><div class="wrap"><div>Điện Tử Hiếu - Storefront công khai</div><div>Không cần đăng nhập</div></div></div>
<header>
    <div class="wrap head">
        <a class="logo" href="#">Điện Tử Hiếu<span>Mua hàng nhanh - Gọi thợ nhanh</span></a>
        <form class="search" id="searchForm"><input id="searchInput" type="search" placeholder="Tìm sản phẩm..."><button type="submit">Tìm</button></form>
        <a class="btn dark" href="#goi-tho">Gọi thợ</a>
    </div>
    <nav><div class="wrap">
        <button class="active" type="button" data-category="">Tất cả</button>
        <button type="button" data-category="Điện tử">Điện tử</button>
        <button type="button" data-category="Gia dụng">Gia dụng</button>
        <button type="button" data-category="Lạnh">Lạnh</button>
        <button type="button" data-category="Điện thoại">Điện thoại</button>
        <button type="button" data-category="Lọc nước">Lọc nước</button>
        <button type="button" data-category="Sim">Sim</button>
    </div></nav>
</header>
<main><div class="wrap">
    <section class="hero">
        <div class="panel hero-main">
            <h1>Điện Tử Hiếu</h1>
            <p>Cửa hàng điện tử, điện lạnh, gia dụng, sim số và điều phối thợ kỹ thuật. Form gọi thợ bên dưới gửi trực tiếp về backend Anh Thiên.</p>
            <div><a class="btn" href="#products">Xem sản phẩm</a> <a class="btn dark" href="#goi-tho">Đặt lịch gọi thợ</a></div>
        </div>
        <div class="panel qr">
            <div><h3>QR truy cập</h3><p>Quét để mở nhanh website.</p><img src="QR.png" alt="QR truy cập" onerror="this.style.display='none'"></div>
            <div><h3>QR thanh toán</h3><p>Dùng khi khách chuyển khoản.</p><img src="QR_THANH_TOAN.png" alt="QR thanh toán" onerror="this.onerror=null;this.src='QR_THANH_TOAN.jpg'"></div>
        </div>
    </section>

    <section class="section" id="products">
        <div class="title"><h2>Sản phẩm</h2><span class="muted"><?= h($productError ?: (count($products) . ' sản phẩm')) ?></span></div>
        <div class="grid" id="productGrid">
            <?php if (!$products): ?>
                <div class="empty">Hiện chưa có sản phẩm</div>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <?php
                        $name = $p['name'] ?? ($p['ten_sp'] ?? 'Sản phẩm');
                        $category = $p['category'] ?? ($p['danh_muc'] ?? '');
                        $image = $p['image'] ?? ($p['image_url'] ?? ($p['hinh_anh'] ?? ''));
                        $price = $p['price'] ?? ($p['gia_ban'] ?? 0);
                    ?>
                    <article class="product" data-name="<?= h(lower_text($name)) ?>" data-category="<?= h(lower_text($category)) ?>">
                        <div class="img">
                            <?php if ($image !== ''): ?><img src="<?= h($image) ?>" alt="<?= h($name) ?>" onerror="this.parentNode.textContent='Chưa có ảnh'"><?php else: ?>Chưa có ảnh<?php endif; ?>
                        </div>
                        <div class="body"><div class="name"><?= h($name) ?></div><div class="cat"><?= h($category ?: 'Điện Tử Hiếu') ?></div><div class="price"><?= money_vnd($price) ?></div></div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="section booking" id="goi-tho">
        <form class="panel card" id="bookingForm">
            <h2>Gọi thợ</h2>
            <div class="form">
                <div class="field"><label>Tên khách</label><input id="customerName" required maxlength="150"></div>
                <div class="field"><label>Số điện thoại</label><input id="customerPhone" required maxlength="20"></div>
                <div class="field full"><label>Địa chỉ</label><input id="customerAddress" required maxlength="500"></div>
                <div class="field full"><label>Mô tả sự cố</label><textarea id="issueDescription" required maxlength="2000"></textarea></div>
                <div class="field"><label>Estimated Price / Target Base</label><input id="estimatedPrice" type="number" min="0" step="1000" required placeholder="150000"></div>
                <div class="field"><label>Nhóm dịch vụ</label><select id="serviceType"><option>Điện lạnh</option><option>Điện tử</option><option>Gia dụng</option><option>Lọc nước</option><option>Điện thoại</option></select></div>
            </div>
            <p class="muted">Backend sẽ tự tính giá khách = target base + VAT + lợi nhuận, rồi áp dụng random discount.</p>
            <button id="bookingSubmit" type="submit">Gửi yêu cầu gọi thợ</button>
            <div id="bookingStatus" class="status"></div>
        </form>
        <aside class="panel card">
            <h3>Quy trình</h3>
            <p>1. Khách gửi form.</p><p>2. Bot 1 gửi ca lên nhóm thợ.</p><p>3. Thợ reply để nhận ca.</p><p>4. Bot DM số điện thoại đầy đủ.</p><p>5. Thợ reply DM để báo hoàn thành.</p>
        </aside>
    </section>

    <section class="section panel card">
        <div class="title"><h2>Chính sách pháp lý BCT</h2><span class="muted">Minh bạch thông tin</span></div>
        <div class="policies">
            <div class="policy"><h3>Chính sách bán hàng</h3><p>Giá bán và tồn kho được xác nhận trước khi hoàn tất giao dịch.</p></div>
            <div class="policy"><h3>Chính sách bảo hành</h3><p>Bảo hành theo điều kiện thực tế của từng sản phẩm hoặc dịch vụ.</p></div>
            <div class="policy"><h3>Bảo vệ dữ liệu</h3><p>Thông tin khách chỉ dùng để xử lý đơn hàng, điều phối thợ và hỗ trợ sau bán hàng.</p></div>
        </div>
    </section>
</div></main>
<footer><div class="wrap">© Điện Tử Hiếu - Public Storefront</div></footer>
<script>
'use strict';
const cards = Array.from(document.querySelectorAll('.product'));
const searchInput = document.getElementById('searchInput');
let category = '';
function normalize(s){ return String(s || '').toLowerCase(); }
function filterProducts(){
    const q = normalize(searchInput.value);
    cards.forEach(card => {
        const okQ = !q || card.dataset.name.indexOf(q) !== -1 || card.dataset.category.indexOf(q) !== -1;
        const okC = !category || card.dataset.category.indexOf(normalize(category)) !== -1;
        card.style.display = okQ && okC ? '' : 'none';
    });
}
document.getElementById('searchForm').addEventListener('submit', e => { e.preventDefault(); filterProducts(); });
searchInput.addEventListener('input', filterProducts);
document.querySelectorAll('[data-category]').forEach(btn => btn.addEventListener('click', () => {
    document.querySelectorAll('[data-category]').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    category = btn.dataset.category || '';
    filterProducts();
}));
function deviceId(){
    let id = localStorage.getItem('dth_device_id');
    if (!id) { id = 'dth-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2); localStorage.setItem('dth_device_id', id); }
    return id;
}
function statusBox(type, text){
    const el = document.getElementById('bookingStatus');
    el.className = 'status ' + (type === 'ok' ? 'ok' : 'err');
    el.textContent = text;
}
document.getElementById('bookingForm').addEventListener('submit', async e => {
    e.preventDefault();
    const btn = document.getElementById('bookingSubmit');
    const issue = document.getElementById('issueDescription').value.trim();
    const payload = {
        customer_name: document.getElementById('customerName').value.trim(),
        phone: document.getElementById('customerPhone').value.trim(),
        address: document.getElementById('customerAddress').value.trim(),
        description: issue,
        issue_description: issue,
        tech_target_base: document.getElementById('estimatedPrice').value,
        service_type: document.getElementById('serviceType').value,
        device_fingerprint: deviceId()
    };
    btn.disabled = true;
    btn.textContent = 'Đang gửi...';
    statusBox('ok', 'Đang gửi yêu cầu...');
    try {
        const res = await fetch('api_master.php?action=create_job', { method: 'POST', headers: {'Content-Type':'application/json; charset=utf-8'}, body: JSON.stringify(payload) });
        const data = await res.json();
        if (!res.ok || !(data.status === 'success' || data.success === true)) throw new Error(data.message || 'Không gửi được yêu cầu.');
        alert('Đã gửi yêu cầu gọi thợ thành công! Thợ sẽ liên hệ sớm nhất.');
        e.target.reset();
        statusBox('ok', 'Yêu cầu đã gửi thành công.');
    } catch (err) {
        statusBox('err', err.message || 'Lỗi kết nối. Vui lòng thử lại.');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Gửi yêu cầu gọi thợ';
    }
});
</script>
</body>
</html>
