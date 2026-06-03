<?php
header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
date_default_timezone_set('Asia/Ho_Chi_Minh');

/*
 * Dien Tu Hieu - PUBLIC STOREFRONT.
 * This file never loads api_master.php directly. It only calls api_master.php by JS fetch().
 * DB credentials stay in .env, not in this public PHP file.
 */

function load_env_file($path)
{
    if (!is_readable($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }
    foreach ($lines as $line) {
        $line = trim(str_replace("\xEF\xBB\xBF", '', $line));
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }
        list($key, $value) = array_map('trim', explode('=', $line, 2));
        if ($key === '') {
            continue;
        }
        if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') || (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
            $value = substr($value, 1, -1);
        }
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv($key . '=' . $value);
    }
}

function env_value($key, $default = '')
{
    $value = isset($_ENV[$key]) ? $_ENV[$key] : (isset($_SERVER[$key]) ? $_SERVER[$key] : getenv($key));
    return ($value === false || $value === null || $value === '') ? $default : $value;
}

load_env_file(__DIR__ . '/.env');
$pdo = null;
$products = [];
$productError = '';
$dbOnline = false;

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

function money_int($value)
{
    if (is_numeric($value)) {
        return max(0, (int)round((float)$value));
    }
    return max(0, (int)(preg_replace('/[^\d]/', '', (string)$value) ?: 0));
}

function local_asset_data_uri(array $candidates)
{
    $mimes = [
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
    ];
    foreach ($candidates as $name) {
        $name = basename((string)$name);
        $path = __DIR__ . DIRECTORY_SEPARATOR . $name;
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!isset($mimes[$ext]) || !is_file($path) || !is_readable($path)) {
            continue;
        }
        $raw = file_get_contents($path);
        if ($raw !== false && $raw !== '') {
            return 'data:' . $mimes[$ext] . ';base64,' . base64_encode($raw);
        }
    }
    return '';
}

function suggested_local_component_price($name, $category = '')
{
    $text = lower_text($name . ' ' . $category);
    $has = static function (array $keys) use ($text) {
        foreach ($keys as $key) {
            if (strpos($text, $key) !== false) {
                return true;
            }
        }
        return false;
    };

    if ($has(['composite', 'cột', 'cot', 'khử vôi', 'khu voi'])) {
        return 450000;
    }
    if ($has(['kệ', 'ke']) && $has(['inox'])) {
        return 180000;
    }
    if ($has(['kệ', 'ke']) && $has(['nhựa', 'nhua'])) {
        return 120000;
    }
    if ($has(['lõi lọc', 'loi loc'])) {
        return 80000;
    }
    if ($has(['khung treo'])) {
        return 120000;
    }
    if ($has(['ống đồng', 'ong dong'])) {
        return 150000;
    }
    if ($has(['gas', 'ga lạnh', 'ga lanh'])) {
        return 250000;
    }
    if ($has(['remote', 'điều khiển', 'dieu khien'])) {
        return 120000;
    }
    if ($has(['lọc nước', 'loc nuoc'])) {
        return 160000;
    }
    if ($has(['máy lạnh', 'may lanh', 'điện lạnh', 'dien lanh'])) {
        return 150000;
    }
    if ($has(['tivi', 'tv'])) {
        return 120000;
    }
    if ($has(['điện thoại', 'dien thoai'])) {
        return 99000;
    }
    return 99000;
}

function customer_price_with_vat($base)
{
    return $base > 0 ? (int)round($base * 1.10) : 0;
}

function platform_fee_from_worker_base($base)
{
    return $base > 0 ? (int)round($base * 0.05) : 0;
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
    try {
        if (!column_exists($pdo, $table, $column)) {
            $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
        }
    } catch (Exception $e) {
        error_log('[index schema column] ' . $table . '.' . $column . ': ' . $e->getMessage());
    }
}

function index_exists(PDO $pdo, $table, $index)
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?');
    $stmt->execute([$table, $index]);
    return (int)$stmt->fetchColumn() > 0;
}

function add_index_if_missing(PDO $pdo, $table, $index, $definition)
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', $table) || !preg_match('/^[A-Za-z0-9_]+$/', $index)) {
        return;
    }
    try {
        if (!index_exists($pdo, $table, $index)) {
            $pdo->exec("ALTER TABLE `{$table}` ADD INDEX `{$index}` {$definition}");
        }
    } catch (Exception $e) {
        error_log('[index schema index] ' . $table . '.' . $index . ': ' . $e->getMessage());
    }
}

try {
    $host = env_value('DB_HOST', 'localhost');
    $db = env_value('DB_NAME', '');
    $user = env_value('DB_USER', env_value('DB_USER_BAOCAO', ''));
    $pass = env_value('DB_PASS', env_value('DB_PASS_BAOCAO', ''));
    $charset = env_value('DB_CHARSET', 'utf8mb4');

    if ($db === '' || $user === '') {
        throw new Exception('Missing DB_NAME or DB_USER in .env');
    }

    $pdo = new PDO(
        "mysql:host={$host};dbname={$db};charset={$charset}",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    $dbOnline = true;

    try { $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NULL,
        category VARCHAR(120) NULL,
        image VARCHAR(700) NULL,
        price INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"); } catch (Exception $e) { error_log('[index schema products] ' . $e->getMessage()); }
    add_column_if_missing($pdo, 'products', 'name', 'VARCHAR(255) NULL');
    add_column_if_missing($pdo, 'products', 'category', 'VARCHAR(120) NULL');
    add_column_if_missing($pdo, 'products', 'image', 'VARCHAR(700) NULL');
    add_column_if_missing($pdo, 'products', 'price', 'INT NOT NULL DEFAULT 0');
    add_column_if_missing($pdo, 'products', 'created_at', 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
    add_index_if_missing($pdo, 'products', 'idx_products_category', '(category)');
    add_index_if_missing($pdo, 'products', 'idx_products_price', '(price)');

    try { $pdo->exec("CREATE TABLE IF NOT EXISTS qr_coupons (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(80) NOT NULL UNIQUE,
        discount_amount INT NOT NULL DEFAULT 0,
        quantity_left INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"); } catch (Exception $e) { error_log('[index schema qr_coupons] ' . $e->getMessage()); }
    add_column_if_missing($pdo, 'qr_coupons', 'code', 'VARCHAR(80) NULL');
    add_column_if_missing($pdo, 'qr_coupons', 'discount_amount', 'INT NOT NULL DEFAULT 0');
    add_column_if_missing($pdo, 'qr_coupons', 'quantity_left', 'INT NOT NULL DEFAULT 0');
    add_column_if_missing($pdo, 'qr_coupons', 'created_at', 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
    add_index_if_missing($pdo, 'qr_coupons', 'idx_qr_coupons_code', '(code)');

    try { $pdo->exec("CREATE TABLE IF NOT EXISTS job_posts (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        customer_phone VARCHAR(30) NULL,
        issue TEXT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
        tech_target_price INT NOT NULL DEFAULT 0,
        final_price INT NOT NULL DEFAULT 0,
        bot_message_id BIGINT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"); } catch (Exception $e) { error_log('[index schema job_posts] ' . $e->getMessage()); }
    add_column_if_missing($pdo, 'job_posts', 'customer_phone', 'VARCHAR(30) NULL');
    add_column_if_missing($pdo, 'job_posts', 'issue', 'TEXT NULL');
    add_column_if_missing($pdo, 'job_posts', 'status', "VARCHAR(30) NOT NULL DEFAULT 'pending'");
    add_column_if_missing($pdo, 'job_posts', 'tech_target_price', 'INT NOT NULL DEFAULT 0');
    add_column_if_missing($pdo, 'job_posts', 'final_price', 'INT NOT NULL DEFAULT 0');
    add_column_if_missing($pdo, 'job_posts', 'bot_message_id', 'BIGINT NULL');
    add_column_if_missing($pdo, 'job_posts', 'created_at', 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
    add_index_if_missing($pdo, 'job_posts', 'idx_job_posts_customer_phone', '(customer_phone)');
    add_index_if_missing($pdo, 'job_posts', 'idx_job_posts_status', '(status)');

    try { $pdo->exec("CREATE TABLE IF NOT EXISTS finances (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        type VARCHAR(40) NOT NULL,
        amount INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"); } catch (Exception $e) { error_log('[index schema finances] ' . $e->getMessage()); }
    add_column_if_missing($pdo, 'finances', 'type', 'VARCHAR(40) NULL');
    add_column_if_missing($pdo, 'finances', 'amount', 'INT NOT NULL DEFAULT 0');
    add_column_if_missing($pdo, 'finances', 'created_at', 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');

    try { $pdo->exec("CREATE TABLE IF NOT EXISTS banned_entities (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        ip_or_phone VARCHAR(255) NOT NULL,
        type VARCHAR(30) NOT NULL DEFAULT 'ip',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_banned_entities (ip_or_phone, type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"); } catch (Exception $e) { error_log('[index schema banned_entities] ' . $e->getMessage()); }
    add_column_if_missing($pdo, 'banned_entities', 'ip_or_phone', 'VARCHAR(255) NULL');
    add_column_if_missing($pdo, 'banned_entities', 'type', "VARCHAR(30) NOT NULL DEFAULT 'ip'");
    add_column_if_missing($pdo, 'banned_entities', 'created_at', 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
    add_index_if_missing($pdo, 'banned_entities', 'idx_banned_entities_lookup', '(ip_or_phone, type)');
} catch (Exception $e) {
    error_log('[index db] ' . $e->getMessage());
    $productError = 'Kho sản phẩm đang được cập nhật.';
}

if ($pdo instanceof PDO) {
    try {
        $stmt = $pdo->query('SELECT * FROM products ORDER BY id DESC LIMIT 60');
        $products = $stmt ? $stmt->fetchAll() : [];
    } catch (Exception $e) {
        error_log('[index products] ' . $e->getMessage());
        $productError = 'Kho sản phẩm đang được cập nhật.';
        $products = [];
    }
}

$qrWebSrc = local_asset_data_uri(['QR.png', 'QR.jpg', 'QR.jpeg', 'QR.webp']);
$qrPaymentSrc = local_asset_data_uri(['QR_THANH_TOAN.png', 'QR_THANH_TOAN.jpg', 'QR_THANH_TOAN.jpeg', 'QR_THANH_TOAN.webp']);
$faviconSrc = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 64 64%22%3E%3Crect width=%2264%22 height=%2264%22 rx=%2212%22 fill=%22%23d9362b%22/%3E%3Ctext x=%2232%22 y=%2241%22 font-size=%2228%22 text-anchor=%22middle%22 fill=%22white%22 font-family=%22Arial%22 font-weight=%22700%22%3EH%3C/text%3E%3C/svg%3E';

$servicePrices = [
    ['group' => 'Thợ điện lạnh', 'service' => 'Vệ sinh máy lạnh', 'base' => 150000, 'note' => 'Giá công khai chưa VAT'],
    ['group' => 'Thợ điện lạnh', 'service' => 'Lắp đặt máy lạnh 1HP / 1.5HP', 'base' => 400000, 'note' => 'Chưa gồm vật tư phát sinh'],
    ['group' => 'Thợ điện lạnh', 'service' => 'Lắp đặt máy lạnh 2HP / 3HP', 'base' => 500000, 'note' => 'Chưa gồm vật tư phát sinh'],
    ['group' => 'Thợ điện lạnh', 'service' => 'Máy lạnh âm trần', 'base' => 0, 'note' => 'Hỗ trợ liên hệ hãng'],
    ['group' => 'Thợ điện lạnh', 'service' => 'Sửa chữa điện lạnh', 'base' => 200000, 'note' => 'Công thợ + linh kiện đặt mua công khai'],
    ['group' => 'Thợ tivi', 'service' => 'Treo tivi', 'base' => 200000, 'note' => 'Công thợ + khung treo'],
    ['group' => 'Thợ máy lọc nước', 'service' => 'Lắp máy lọc nước', 'base' => 200000, 'note' => 'Công thợ + phụ kiện'],
    ['group' => 'Thợ gia dụng', 'service' => 'Lắp máy giặt', 'base' => 200000, 'note' => 'Công thợ + phụ kiện'],
    ['group' => 'Thợ điện thoại', 'service' => 'Kiểm tra / sửa điện thoại', 'base' => 200000, 'note' => 'Công thợ + linh kiện nếu có'],
];

$workerGroups = ['Thợ điện lạnh', 'Thợ máy lọc nước', 'Thợ tivi', 'Thợ điện thoại'];
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Điện Tử Hiếu - Marketplace, Gọi Thợ & Việc Làm Nhanh</title>
    <meta name="description" content="Điện Tử Hiếu là nền tảng mua bán điện tử, điện lạnh, gia dụng, sim số và kết nối thợ kỹ thuật nhanh tại địa phương.">
    <meta property="og:title" content="Điện Tử Hiếu - Marketplace & Gọi Thợ">
    <meta property="og:description" content="Mua hàng, đặt lịch gọi thợ và kết nối dịch vụ kỹ thuật nhanh qua hệ sinh thái Điện Tử Hiếu.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="/">
    <link rel="icon" href="<?= h($faviconSrc) ?>">
    <meta name="robots" content="index,follow">
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
        .hero-main{padding:28px;background:linear-gradient(120deg,rgba(17,24,39,.96),rgba(169,39,29,.9));color:#fff;min-height:260px;display:flex;flex-direction:column;justify-content:center}.hero-main h1{font-size:clamp(30px,4vw,48px);line-height:1.05;margin:0 0 12px}.hero-main p{max-width:620px;margin:0 0 18px;color:#f3f4f6}
        .qr{padding:18px;display:grid;grid-template-columns:1fr 1fr;gap:12px}.qr div{border:1px solid var(--line);border-radius:8px;padding:12px}.qr h3{margin:0 0 8px;font-size:15px}.qr p{margin:0 0 10px;color:var(--muted);font-size:13px}.qr img{width:150px;height:150px;object-fit:contain;margin:auto;background:#fff;border:1px solid var(--line);padding:6px}.qr-empty{height:150px;display:grid;place-items:center;background:#fff;border:1px dashed #cbd5e1;border-radius:8px;color:#98a2b3;text-align:center;font-size:13px}
        .section{margin-top:22px}.title{display:flex;align-items:end;justify-content:space-between;gap:12px;margin-bottom:12px}.title h2{margin:0;font-size:23px}.muted{color:var(--muted);font-size:14px}
        .grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.product{background:#fff;border:1px solid var(--line);border-radius:10px;overflow:hidden}.img{height:145px;background:#fff;border-bottom:1px solid var(--line);display:grid;place-items:center;color:#98a2b3}.img img{max-width:100%;max-height:100%;object-fit:contain;padding:10px}.body{padding:12px}.name{font-weight:900;min-height:42px}.cat{color:var(--muted);font-size:13px}.price{color:var(--brand);font-size:17px;font-weight:900}.suggest{display:inline-block;margin-top:4px;color:#047857;font-size:12px;font-weight:800}.empty{grid-column:1/-1;background:#fff;border:1px dashed #cbd5e1;border-radius:10px;padding:24px;text-align:center;color:var(--muted)}
        .worker-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}.worker{background:#fff;border:1px solid var(--line);border-radius:10px;padding:14px;font-weight:900}.worker span{display:block;margin-top:5px;color:var(--muted);font-weight:600;font-size:13px}
        .price-table{overflow:auto}.price-table table{width:100%;min-width:820px;border-collapse:collapse;background:#fff;border:1px solid var(--line);border-radius:10px;overflow:hidden}.price-table th,.price-table td{padding:10px;border-bottom:1px solid var(--line);text-align:left;font-size:13px}.price-table th{background:#f1f5f9;color:#334155;text-transform:uppercase;font-size:12px}.price-table tr:last-child td{border-bottom:0}.mini-btn{padding:7px 10px;border-radius:6px;font-size:12px}
        .booking{display:grid;grid-template-columns:1fr .85fr;gap:18px}.card{padding:18px}.form{display:grid;grid-template-columns:1fr 1fr;gap:12px}.field{display:grid;gap:6px}.full{grid-column:1/-1}label{font-size:13px;font-weight:800}input,textarea,select{width:100%;border:1px solid var(--line);border-radius:8px;padding:11px 12px;font:inherit;background:#fff}textarea{min-height:110px;resize:vertical}.status{display:none;margin-top:12px;padding:10px 12px;border-radius:8px}.status.ok{display:block;background:#ecfdf3;color:#047857;border:1px solid #a7f3d0}.status.err{display:block;background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}
        .policies{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}.policy{padding:14px;background:#fff;border:1px solid var(--line);border-radius:8px}.policy h3{margin:0 0 7px}.policy p{margin:0;color:var(--muted);font-size:13px}
        footer{background:var(--dark);color:#d1d5db;padding:24px 0;font-size:13px}
        @media(max-width:900px){.head,.hero,.booking{display:grid;grid-template-columns:1fr}.search{max-width:none}.grid,.policies,.qr,.worker-grid{grid-template-columns:repeat(2,1fr)}}@media(max-width:620px){.grid,.policies,.qr,.form,.worker-grid{grid-template-columns:1fr}.full{grid-column:auto}.wrap{width:min(100% - 22px,1180px)}}
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
            <div><h3>QR truy cập</h3><p>Quét để mở nhanh website.</p><?php if ($qrWebSrc !== ''): ?><img src="<?= h($qrWebSrc) ?>" alt="QR truy cập"><?php else: ?><div class="qr-empty">Chưa tìm thấy QR.png / QR.jpg</div><?php endif; ?></div>
            <div><h3>QR thanh toán</h3><p>Dùng khi khách chuyển khoản.</p><?php if ($qrPaymentSrc !== ''): ?><img src="<?= h($qrPaymentSrc) ?>" alt="QR thanh toán"><?php else: ?><div class="qr-empty">Chưa tìm thấy QR_THANH_TOAN.png / .jpg</div><?php endif; ?></div>
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
                        $name = isset($p['name']) && $p['name'] !== '' ? $p['name'] : (isset($p['ten_sp']) ? $p['ten_sp'] : 'Sản phẩm');
                        $category = isset($p['category']) ? $p['category'] : (isset($p['danh_muc']) ? $p['danh_muc'] : '');
                        $image = isset($p['image']) ? $p['image'] : (isset($p['image_url']) ? $p['image_url'] : (isset($p['hinh_anh']) ? $p['hinh_anh'] : ''));
                        $rawPrice = isset($p['price']) ? $p['price'] : (isset($p['gia_ban']) ? $p['gia_ban'] : 0);
                        $price = money_int($rawPrice);
                        $suggested = false;
                        if ($price <= 0) {
                            $price = suggested_local_component_price($name, $category);
                            $suggested = true;
                        }
                    ?>
                    <article class="product" data-name="<?= h(lower_text($name)) ?>" data-category="<?= h(lower_text($category)) ?>">
                        <div class="img">
                            <?php if ($image !== ''): ?><img src="<?= h($image) ?>" alt="<?= h($name) ?>" onerror="this.parentNode.textContent='Chưa có ảnh'"><?php else: ?>Chưa có ảnh<?php endif; ?>
                        </div>
                        <div class="body"><div class="name"><?= h($name) ?></div><div class="cat"><?= h($category ?: 'Điện Tử Hiếu') ?></div><div class="price"><?= money_vnd($price) ?></div><?php if ($suggested): ?><span class="suggest">Giá đề xuất khu vực xã</span><?php endif; ?></div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="section panel card">
        <div class="title"><h2>Nhóm thợ kỹ thuật</h2><span class="muted">Điều phối theo đúng chuyên môn</span></div>
        <div class="worker-grid">
            <?php foreach ($workerGroups as $group): ?>
                <div class="worker"><?= h($group) ?><span>Nhận ca qua Bot Anh Thiên 1</span></div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section panel card">
        <div class="title"><h2>Bảng giá dịch vụ công khai</h2><span class="muted">Giá công khai chưa VAT. Khi báo khách cộng 10% VAT.</span></div>
        <div class="price-table">
            <table>
                <thead><tr><th>Nhóm thợ</th><th>Dịch vụ</th><th>Giá công khai</th><th>Báo khách + VAT 10%</th><th>Thợ nhận</th><th>Phí nền tảng 5%</th><th>Ghi chú</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($servicePrices as $row): ?>
                    <tr>
                        <td><?= h($row['group']) ?></td>
                        <td><b><?= h($row['service']) ?></b></td>
                        <td><?= $row['base'] > 0 ? money_vnd($row['base']) : 'Liên hệ' ?></td>
                        <td><?= $row['base'] > 0 ? money_vnd(customer_price_with_vat($row['base'])) : 'Theo hãng' ?></td>
                        <td><?= $row['base'] > 0 ? money_vnd($row['base']) : '-' ?></td>
                        <td><?= $row['base'] > 0 ? money_vnd(platform_fee_from_worker_base($row['base'])) : '-' ?></td>
                        <td><?= h($row['note']) ?></td>
                        <td><?php if ($row['base'] > 0): ?><button class="mini-btn choose-service" type="button" data-service="<?= h($row['group'] . ' - ' . $row['service']) ?>" data-base="<?= h($row['base']) ?>">Chọn</button><?php endif; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="section booking" id="goi-tho">
        <form class="panel card" id="bookingForm">
            <h2>Gọi thợ</h2>
            <div class="form">
                <div class="field"><label>Tên khách</label><input id="customerName" required maxlength="150"></div>
                <div class="field"><label>Số điện thoại</label><input id="customerPhone" type="tel" inputmode="numeric" pattern="[0-9]{8,15}" required maxlength="15" placeholder="09xxxxxxxx"></div>
                <div class="field full"><label>Địa chỉ</label><input id="customerAddress" required maxlength="500"></div>
                <div class="field full"><label>Mô tả sự cố</label><textarea id="issueDescription" required maxlength="2000"></textarea></div>
                <div class="field"><label>Estimated Price / Target Base</label><input id="estimatedPrice" type="number" min="0" step="1000" required placeholder="150000"></div>
                <div class="field"><label>Nhóm dịch vụ</label><select id="serviceType"><option>Thợ điện lạnh</option><option>Thợ máy lọc nước</option><option>Thợ tivi</option><option>Thợ điện thoại</option><option>Thợ gia dụng</option></select></div>
            </div>
            <p class="muted">Giá form gửi về backend là giá thợ nhận. Backend sẽ tính giá khách = giá thợ + 10% VAT + 5% phí nền tảng, rồi áp dụng random discount.</p>
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
document.querySelectorAll('.choose-service').forEach(btn => btn.addEventListener('click', () => {
    const serviceType = document.getElementById('serviceType');
    document.getElementById('estimatedPrice').value = btn.dataset.base || '';
    document.getElementById('issueDescription').value = btn.dataset.service || '';
    serviceType.value = (btn.dataset.service || '').split(' - ')[0] || serviceType.value;
    location.hash = '#goi-tho';
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
    if (!/^[0-9]{8,15}$/.test(payload.phone)) {
        statusBox('err', 'Số điện thoại chỉ được nhập số, từ 8 đến 15 chữ số.');
        return;
    }
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
