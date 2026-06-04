<?php
header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=(self)');
date_default_timezone_set('Asia/Ho_Chi_Minh');

/*
 * Dien Tu Hieu - Public Storefront.
 * Do not require api_master.php here. This page calls the backend only by fetch().
 * Database credentials are loaded from .env so they are not exposed in this file.
 */

$pdo = null;
$products = array();
$productError = '';
$dbOnline = false;

function dth_load_env($path)
{
    if (!is_file($path) || !is_readable($path)) {
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

        $first = substr($value, 0, 1);
        $last = substr($value, -1);
        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            $value = substr($value, 1, -1);
        }

        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv($key . '=' . $value);
    }
}

function dth_env($key, $default = '')
{
    $value = isset($_ENV[$key]) ? $_ENV[$key] : (isset($_SERVER[$key]) ? $_SERVER[$key] : getenv($key));
    return ($value === false || $value === null || $value === '') ? $default : $value;
}

function h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function money_vnd($value)
{
    return number_format((float)$value, 0, ',', '.') . ' VND';
}

function money_int($value)
{
    if (is_numeric($value)) {
        return max(0, (int)round((float)$value));
    }
    return max(0, (int)(preg_replace('/[^\d]/', '', (string)$value) ?: 0));
}

function lower_text($value)
{
    $value = (string)$value;
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
}

function dth_has(PDO $pdo, $table, $column)
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute(array($table, $column));
    return (int)$stmt->fetchColumn() > 0;
}

function dth_add_column(PDO $pdo, $table, $column, $definition)
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', $table) || !preg_match('/^[A-Za-z0-9_]+$/', $column)) {
        return;
    }

    try {
        if (!dth_has($pdo, $table, $column)) {
            $pdo->exec('ALTER TABLE `' . $table . '` ADD COLUMN `' . $column . '` ' . $definition);
        }
    } catch (Exception $e) {
        error_log('[index add column] ' . $table . '.' . $column . ': ' . $e->getMessage());
    }
}

function dth_index_exists(PDO $pdo, $table, $index)
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?');
    $stmt->execute(array($table, $index));
    return (int)$stmt->fetchColumn() > 0;
}

function dth_add_index(PDO $pdo, $table, $index, $definition)
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', $table) || !preg_match('/^[A-Za-z0-9_]+$/', $index)) {
        return;
    }

    try {
        if (!dth_index_exists($pdo, $table, $index)) {
            $pdo->exec('ALTER TABLE `' . $table . '` ADD INDEX `' . $index . '` ' . $definition);
        }
    } catch (Exception $e) {
        error_log('[index add index] ' . $table . '.' . $index . ': ' . $e->getMessage());
    }
}

function dth_auto_schema(PDO $pdo)
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NULL,
        category VARCHAR(120) NULL,
        image VARCHAR(700) NULL,
        image_url VARCHAR(700) NULL,
        price INT NOT NULL DEFAULT 0,
        stock_quantity INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    dth_add_column($pdo, 'products', 'name', 'VARCHAR(255) NULL');
    dth_add_column($pdo, 'products', 'category', 'VARCHAR(120) NULL');
    dth_add_column($pdo, 'products', 'image', 'VARCHAR(700) NULL');
    dth_add_column($pdo, 'products', 'image_url', 'VARCHAR(700) NULL');
    dth_add_column($pdo, 'products', 'price', 'INT NOT NULL DEFAULT 0');
    dth_add_column($pdo, 'products', 'stock_quantity', 'INT NOT NULL DEFAULT 0');
    dth_add_column($pdo, 'products', 'created_at', 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
    dth_add_index($pdo, 'products', 'idx_products_category', '(category)');
    dth_add_index($pdo, 'products', 'idx_products_price', '(price)');

    $pdo->exec("CREATE TABLE IF NOT EXISTS qr_coupons (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(80) NOT NULL UNIQUE,
        discount_amount INT NOT NULL DEFAULT 0,
        quantity_left INT NOT NULL DEFAULT 0,
        type VARCHAR(30) NOT NULL DEFAULT 'discount',
        value INT NOT NULL DEFAULT 0,
        description TEXT NULL,
        is_used TINYINT(1) NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    dth_add_column($pdo, 'qr_coupons', 'code', 'VARCHAR(80) NULL');
    dth_add_column($pdo, 'qr_coupons', 'discount_amount', 'INT NOT NULL DEFAULT 0');
    dth_add_column($pdo, 'qr_coupons', 'quantity_left', 'INT NOT NULL DEFAULT 0');
    dth_add_column($pdo, 'qr_coupons', 'type', "VARCHAR(30) NOT NULL DEFAULT 'discount'");
    dth_add_column($pdo, 'qr_coupons', 'value', 'INT NOT NULL DEFAULT 0');
    dth_add_column($pdo, 'qr_coupons', 'description', 'TEXT NULL');
    dth_add_column($pdo, 'qr_coupons', 'is_used', 'TINYINT(1) NOT NULL DEFAULT 0');
    dth_add_index($pdo, 'qr_coupons', 'idx_qr_coupons_code', '(code)');

    $pdo->exec("CREATE TABLE IF NOT EXISTS job_posts (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(150) NULL,
        customer_phone VARCHAR(30) NULL,
        service_type VARCHAR(150) NULL,
        address TEXT NULL,
        issue TEXT NULL,
        description TEXT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
        tech_target_price INT NOT NULL DEFAULT 0,
        final_price INT NOT NULL DEFAULT 0,
        bot_message_id BIGINT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    dth_add_column($pdo, 'job_posts', 'customer_name', 'VARCHAR(150) NULL');
    dth_add_column($pdo, 'job_posts', 'customer_phone', 'VARCHAR(30) NULL');
    dth_add_column($pdo, 'job_posts', 'service_type', 'VARCHAR(150) NULL');
    dth_add_column($pdo, 'job_posts', 'address', 'TEXT NULL');
    dth_add_column($pdo, 'job_posts', 'issue', 'TEXT NULL');
    dth_add_column($pdo, 'job_posts', 'description', 'TEXT NULL');
    dth_add_column($pdo, 'job_posts', 'status', "VARCHAR(30) NOT NULL DEFAULT 'pending'");
    dth_add_column($pdo, 'job_posts', 'tech_target_price', 'INT NOT NULL DEFAULT 0');
    dth_add_column($pdo, 'job_posts', 'final_price', 'INT NOT NULL DEFAULT 0');
    dth_add_column($pdo, 'job_posts', 'bot_message_id', 'BIGINT NULL');
    dth_add_column($pdo, 'job_posts', 'created_at', 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
    dth_add_index($pdo, 'job_posts', 'idx_job_posts_customer_phone', '(customer_phone)');
    dth_add_index($pdo, 'job_posts', 'idx_job_posts_status', '(status)');

    $pdo->exec("CREATE TABLE IF NOT EXISTS finances (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        type VARCHAR(40) NOT NULL,
        amount INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS banned_entities (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        ip_or_phone VARCHAR(255) NOT NULL,
        type VARCHAR(30) NOT NULL DEFAULT 'ip',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_banned_entities (ip_or_phone, type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

function asset_data_uri($names)
{
    $mimes = array(
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
    );

    foreach ($names as $name) {
        $file = basename((string)$name);
        $path = __DIR__ . DIRECTORY_SEPARATOR . $file;
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

function suggested_component_price($name, $category)
{
    $text = lower_text($name . ' ' . $category);
    $has = function ($keys) use ($text) {
        foreach ($keys as $key) {
            if (strpos($text, $key) !== false) {
                return true;
            }
        }
        return false;
    };

    if ($has(array('composite', 'cột', 'cot', 'khử vôi', 'khu voi'))) {
        return 450000;
    }
    if ($has(array('kệ', 'ke')) && $has(array('inox'))) {
        return 180000;
    }
    if ($has(array('kệ', 'ke')) && $has(array('nhựa', 'nhua'))) {
        return 120000;
    }
    if ($has(array('lõi lọc', 'loi loc'))) {
        return 80000;
    }
    if ($has(array('khung treo'))) {
        return 120000;
    }
    if ($has(array('ống đồng', 'ong dong'))) {
        return 150000;
    }
    if ($has(array('remote', 'điều khiển', 'dieu khien'))) {
        return 120000;
    }
    if ($has(array('lọc nước', 'loc nuoc'))) {
        return 160000;
    }
    if ($has(array('tivi', 'tv'))) {
        return 120000;
    }
    return 99000;
}

dth_load_env(__DIR__ . '/.env');

try {
    $dbName = dth_env('DB_NAME', '');
    $dbUser = dth_env('DB_USER', dth_env('DB_USER_BAOCAO', ''));
    $dbPass = dth_env('DB_PASS', dth_env('DB_PASS_BAOCAO', ''));

    if ($dbName === '' || $dbUser === '') {
        throw new Exception('Missing database credentials in .env');
    }

    $pdo = new PDO(
        'mysql:host=' . dth_env('DB_HOST', 'localhost') . ';dbname=' . $dbName . ';charset=' . dth_env('DB_CHARSET', 'utf8mb4'),
        $dbUser,
        $dbPass,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        )
    );
    $dbOnline = true;
    dth_auto_schema($pdo);
} catch (Exception $e) {
    error_log('[index db] ' . $e->getMessage());
    $productError = 'Kho sản phẩm đang được cập nhật.';
}

if ($pdo instanceof PDO) {
    try {
        $stmt = $pdo->query('SELECT * FROM products ORDER BY id DESC LIMIT 60');
        $products = $stmt ? $stmt->fetchAll() : array();
    } catch (Exception $e) {
        error_log('[index products] ' . $e->getMessage());
        $productError = 'Không đọc được kho sản phẩm.';
        $products = array();
    }
}

$qrWeb = asset_data_uri(array('QR.png', 'QR.jpg', 'QR.jpeg', 'QR.webp'));
$qrPay = asset_data_uri(array('QR_THANH_TOAN.png', 'QR_THANH_TOAN.jpg', 'QR_THANH_TOAN.jpeg', 'QR_THANH_TOAN.webp'));
$favicon = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 64 64%22%3E%3Crect width=%2264%22 height=%2264%22 rx=%2212%22 fill=%22%23dc2626%22/%3E%3Ctext x=%2232%22 y=%2241%22 text-anchor=%22middle%22 font-size=%2228%22 font-weight=%22700%22 font-family=%22Arial%22 fill=%22white%22%3EH%3C/text%3E%3C/svg%3E';

$categories = array('Tất cả', 'Điện tử', 'Gia dụng', 'Lạnh', 'Điện thoại', 'Lọc nước', 'Sim');
$services = array(
    array('group' => 'Thợ điện lạnh', 'name' => 'Vệ sinh máy lạnh', 'base' => 150000, 'note' => 'Giá công khai chưa VAT'),
    array('group' => 'Thợ điện lạnh', 'name' => 'Lắp đặt máy lạnh 1HP / 1.5HP', 'base' => 400000, 'note' => 'Chưa gồm vật tư phát sinh'),
    array('group' => 'Thợ điện lạnh', 'name' => 'Lắp đặt máy lạnh 2HP / 3HP', 'base' => 500000, 'note' => 'Chưa gồm vật tư phát sinh'),
    array('group' => 'Thợ điện lạnh', 'name' => 'Máy lạnh âm trần', 'base' => 0, 'note' => 'Hỗ trợ liên hệ hãng'),
    array('group' => 'Thợ điện lạnh', 'name' => 'Sửa chữa điện lạnh', 'base' => 200000, 'note' => 'Công thợ + linh kiện đặt mua công khai'),
    array('group' => 'Thợ tivi', 'name' => 'Treo tivi', 'base' => 200000, 'note' => 'Công thợ + khung treo'),
    array('group' => 'Thợ máy lọc nước', 'name' => 'Lắp máy lọc nước', 'base' => 200000, 'note' => 'Công thợ + phụ kiện'),
    array('group' => 'Thợ gia dụng', 'name' => 'Lắp máy giặt', 'base' => 200000, 'note' => 'Công thợ + phụ kiện'),
    array('group' => 'Thợ điện thoại', 'name' => 'Kiểm tra / sửa điện thoại', 'base' => 200000, 'note' => 'Công thợ + linh kiện nếu có'),
);
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Điện Tử Hiếu - Marketplace & Gọi Thợ</title>
    <meta name="description" content="Điện Tử Hiếu cung cấp sản phẩm điện tử, gia dụng, điện lạnh, sim số và dịch vụ gọi thợ kỹ thuật nhanh tại địa phương.">
    <meta property="og:title" content="Điện Tử Hiếu - Marketplace & Gọi Thợ">
    <meta property="og:description" content="Mua hàng nhanh, gọi thợ nhanh, giá dịch vụ công khai.">
    <meta property="og:type" content="website">
    <link rel="icon" href="<?= h($favicon) ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        :root{--bg:#f6f7f9;--panel:#fff;--line:#e5e7eb;--text:#111827;--muted:#667085;--brand:#dc2626;--dark:#111827;--ok:#047857}
        *{box-sizing:border-box}
        body,button,input,textarea,select{font-family:Arial,Helvetica,sans-serif}
        body{margin:0;background:var(--bg);color:var(--text);line-height:1.5}
        a{text-decoration:none;color:inherit}.wrap{width:min(1180px,calc(100% - 32px));margin:0 auto}
        .top{background:var(--dark);color:#fff;font-size:13px}.top .wrap{min-height:38px;display:flex;justify-content:space-between;align-items:center;gap:12px}.approval-line{background:#fff7ed;color:#9a3412;border-bottom:1px solid #fed7aa;text-align:center;padding:7px 12px;font-size:13px;font-weight:800}
        header{position:sticky;top:0;z-index:10;background:#fff;border-bottom:1px solid var(--line)}
        .head{min-height:74px;display:flex;align-items:center;justify-content:space-between;gap:14px}
        .logo{font-size:22px;font-weight:900;color:var(--brand)}.logo small{display:block;color:var(--muted);font-size:12px}
        .search{display:flex;gap:8px;flex:1;max-width:520px}.search input{width:100%;border:1px solid var(--line);border-radius:8px;padding:11px 12px;font-size:15px}
        .btn,button{border:0;border-radius:8px;background:var(--brand);color:#fff;font-weight:800;padding:11px 16px;cursor:pointer}.btn.dark{background:var(--dark)}
        nav{border-top:1px solid var(--line)}nav .wrap{display:flex;gap:8px;overflow:auto;padding:10px 0}nav button{background:#fff;color:#344054;border:1px solid var(--line);white-space:nowrap}nav button.active{color:var(--brand);border-color:var(--brand);background:#fff5f5}
        main{padding:24px 0 44px}.panel{background:var(--panel);border:1px solid var(--line);border-radius:10px;box-shadow:0 10px 24px rgba(15,23,42,.06)}
        .hero{display:grid;grid-template-columns:1.35fr .9fr;gap:18px}.hero-main{min-height:260px;padding:28px;background:linear-gradient(120deg,#111827,#b42318);color:#fff;display:flex;flex-direction:column;justify-content:center}.hero-main h1{font-size:clamp(32px,4vw,50px);line-height:1;margin:0 0 12px}.hero-main p{max-width:640px;color:#f3f4f6;margin:0 0 18px}.hero-actions{display:flex;gap:8px;flex-wrap:wrap}.hero-actions .btn{text-align:center}
        .qr{padding:18px;display:grid;grid-template-columns:1fr 1fr;gap:12px}.qr-box{border:1px solid var(--line);border-radius:8px;padding:12px}.qr-box h3{margin:0 0 8px;font-size:15px}.qr-box p{margin:0 0 10px;color:var(--muted);font-size:13px}.qr-box img{width:150px;height:150px;object-fit:contain;background:#fff;border:1px solid var(--line);padding:6px}.qr-empty{height:150px;display:grid;place-items:center;background:#fff;border:1px dashed #cbd5e1;border-radius:8px;color:#98a2b3;text-align:center;font-size:13px}
        .storefront{display:flex;flex-direction:column}.storefront>section:nth-of-type(1){order:0}.storefront>section:nth-of-type(2){order:2}.storefront>section:nth-of-type(3){order:1}.storefront>section:nth-of-type(4){order:3}
        .section{margin-top:22px}.title{display:flex;justify-content:space-between;align-items:flex-end;gap:12px;margin-bottom:12px}.title h2{margin:0;font-size:23px}.muted{color:var(--muted);font-size:14px}
        .grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.product{background:#fff;border:1px solid var(--line);border-radius:10px;overflow:hidden}.img{height:145px;display:grid;place-items:center;background:#fff;border-bottom:1px solid var(--line);color:#98a2b3}.img img{max-width:100%;max-height:100%;object-fit:contain;padding:10px}.body{padding:12px}.name{font-weight:900;min-height:42px}.cat{font-size:13px;color:var(--muted)}.price{font-size:17px;color:var(--brand);font-weight:900}.suggest{display:inline-block;color:var(--ok);font-size:12px;font-weight:800}.empty{grid-column:1/-1;background:#fff;border:1px dashed #cbd5e1;border-radius:10px;padding:24px;text-align:center;color:var(--muted)}
        .booking-shell{padding:18px}.service-head{display:grid;grid-template-columns:minmax(220px,1fr) auto;gap:10px;margin-bottom:10px}.service-list{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-bottom:14px}.service-option{display:grid;grid-template-columns:1fr auto;gap:5px 10px;min-height:64px;padding:10px 12px;text-align:left;background:#fff;color:var(--text);border:1px solid var(--line);border-radius:8px}.service-option:hover,.service-option.selected{border-color:var(--brand);background:#fff7f7}.service-option small{color:var(--muted);font-weight:700}.service-option strong{align-self:center;color:var(--brand);white-space:nowrap}.service-option span{font-size:13px;font-weight:900}.service-option.is-contact strong{color:var(--muted)}
        .gemini-panel{display:none;margin:0 0 14px;border:1px solid #c7d2fe;background:#f8fafc;border-radius:8px;padding:12px}.gemini-panel.active{display:grid;gap:10px}.gemini-actions{display:flex;gap:8px;flex-wrap:wrap}.gemini-reply{display:none;border:1px solid var(--line);border-radius:8px;background:#fff;padding:10px;white-space:pre-wrap}.gemini-reply.active{display:block}
        .gemini-message p:last-child { margin-bottom: 0; }
        .gemini-message pre { background: #f1f5f9; padding: 10px; border-radius: 6px; overflow-x: auto; margin: 10px 0; font-size: 13px; }
        .gemini-message code { font-family: monospace; }
        .typing-indicator { display: none; padding: 8px 12px; font-style: italic; color: #64748b; font-size: 13px; }
        .dth-modal { display: none; position: fixed; z-index: 99999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); overflow: auto; }
        .dth-modal-content { background-color: #fff; margin: 5% auto; padding: 24px; border-radius: 12px; width: 90%; max-width: 800px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative; animation: modalFadeIn 0.3s ease; }
        @keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .dth-modal-close { position: absolute; top: 15px; right: 20px; font-size: 28px; font-weight: bold; color: #666; cursor: pointer; }
        .dth-modal-close:hover { color: #dc2626; }
        .dth-modal-title { font-size: 22px; font-weight: 800; margin-bottom: 20px; color: #111827; border-bottom: 2px solid #dc2626; padding-bottom: 10px; display: inline-block; }
        .dth-modal-body { font-size: 15px; line-height: 1.6; color: #374151; }
        .dth-modal-body h4 { margin-top: 15px; margin-bottom: 8px; color: #1f2937; font-size: 16px; }
        .dth-modal-body ul { padding-left: 20px; margin-top: 5px; margin-bottom: 15px; }
        .form{display:grid;grid-template-columns:1fr 1fr;gap:12px}.field{display:grid;gap:6px}.full{grid-column:1/-1}label{font-size:13px;font-weight:800}input,textarea,select{width:100%;border:1px solid var(--line);border-radius:8px;padding:11px 12px;font:inherit;background:#fff}textarea{min-height:100px;resize:vertical}.readonly-price{background:#f8fafc;color:#047857;font-weight:900}.map-actions{display:flex;gap:8px;flex-wrap:wrap}.map-actions .btn{padding:9px 12px}.map-preview{margin-top:4px;border:1px solid var(--line);border-radius:8px;overflow:hidden;background:#f8fafc}.location-map{width:100%;height:260px}.location-status{padding:8px 10px;background:#f8fafc;border-top:1px solid var(--line);font-size:12px;color:var(--muted)}.status{display:none;margin-top:12px;padding:10px 12px;border-radius:8px}.status.ok{display:block;background:#ecfdf3;color:#047857;border:1px solid #a7f3d0}.status.err{display:block;background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}
        .legal-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}.legal-item{background:#fff;border:1px solid var(--line);border-radius:8px;padding:0 12px}.legal-item summary{cursor:pointer;font-weight:900;padding:12px 0}.legal-content{border-top:1px solid var(--line);padding:10px 0 12px;color:#475569;font-size:13px}.legal-content p{margin:0 0 8px}.legal-content p:last-child{margin-bottom:0}
        footer{background:#111827;color:#d1d5db;padding:24px 0;font-size:13px}.footer-grid{display:grid;grid-template-columns:1.5fr 1fr 1fr 1fr;gap:18px}.footer-grid h3{margin:0 0 8px;color:#fff;font-size:15px}.footer-grid p{margin:4px 0}.footer-bottom{border-top:1px solid #374151;margin-top:18px;padding-top:12px;display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap}
        @media(max-width:900px){.head,.hero{display:grid;grid-template-columns:1fr}.search{max-width:none}.grid,.qr,.service-list,.legal-grid,.footer-grid{grid-template-columns:repeat(2,1fr)}}@media(max-width:620px){header{position:static}.top .wrap{min-height:auto;padding:6px 0;flex-wrap:wrap}.head{padding:12px 0}.title{display:grid;grid-template-columns:1fr;align-items:start;gap:2px}.grid,.qr,.form,.service-head,.service-list,.legal-grid,.footer-grid{grid-template-columns:1fr}.hero-actions .btn{flex:1 1 180px}.full{grid-column:auto}.wrap{width:min(100% - 22px,1180px)}.location-map{height:230px}}
    </style>
</head>
<body>
<div class="top"><div class="wrap"><div>Điện Tử Hiếu - Storefront công khai</div><div id="topBarStatus"><a href="javascript:void(0)" onclick="openLoginModal()" style="color: white; font-weight: bold; text-decoration: underline;">Đăng nhập / Đăng ký</a></div></div></div>
<div class="approval-line">Website đang chờ duyệt</div>
<header>
    <div class="wrap head">
        <a class="logo" href="#" style="display:flex; align-items:center; gap: 8px;">
            <img src="logo.jpg" alt="Logo Điện Tử Hiếu" style="height: 48px; border-radius: 6px; object-fit: contain;">
            <div>Điện Tử Hiếu<small>Mua hàng nhanh - Gọi thợ nhanh</small></div>
        </a>
        <form class="search" id="searchForm"><input id="searchInput" type="search" placeholder="Tìm sản phẩm..."><button type="submit">Tìm</button></form>
        <a class="btn dark" href="#goi-tho">Gọi thợ</a>
    </div>
    <nav><div class="wrap">
        <?php foreach ($categories as $idx => $cat): ?>
            <button type="button" class="<?= $idx === 0 ? 'active' : '' ?>" data-category="<?= $idx === 0 ? '' : h($cat) ?>"><?= h($cat) ?></button>
        <?php endforeach; ?>
    </div></nav>
</header>

<main><div class="wrap storefront">
    <section class="hero">
        <div class="panel hero-main">
            <h1>Điện Tử Hiếu</h1>
            <p>LH: 0979.553.289</p>
            <div class="hero-actions"><a class="btn" href="#products">Xem sản phẩm</a><a class="btn dark" href="#goi-tho">Đặt lịch gọi thợ</a></div>
        </div>
    </section>

    <section class="section" id="products">
        <div class="title"><h2>Sản phẩm</h2><span class="muted"><?= h($productError !== '' ? $productError : (count($products) . ' sản phẩm')) ?></span></div>
        <div class="grid" id="productGrid">
            <?php if (empty($products)): ?>
                <div class="empty">Hiện chưa có sản phẩm</div>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <?php
                    $name = isset($p['name']) && $p['name'] !== '' ? $p['name'] : (isset($p['ten_sp']) ? $p['ten_sp'] : 'Sản phẩm');
                    $category = isset($p['category']) && $p['category'] !== '' ? $p['category'] : (isset($p['danh_muc']) ? $p['danh_muc'] : 'Điện Tử Hiếu');
                    $image = isset($p['image']) && $p['image'] !== '' ? $p['image'] : (isset($p['image_url']) ? $p['image_url'] : (isset($p['hinh_anh']) ? $p['hinh_anh'] : ''));
                    $price = money_int(isset($p['price']) ? $p['price'] : (isset($p['gia_ban']) ? $p['gia_ban'] : 0));
                    $suggested = false;
                    if ($price <= 0) {
                        $price = suggested_component_price($name, $category);
                        $suggested = true;
                    }
                    ?>
                    <article class="product" data-name="<?= h(lower_text($name)) ?>" data-category="<?= h(lower_text($category)) ?>">
                        <div class="img"><?php if ($image !== ''): ?><img src="<?= h($image) ?>" alt="<?= h($name) ?>" onerror="this.parentNode.textContent='Chưa có ảnh'"><?php else: ?>Chưa có ảnh<?php endif; ?></div>
                        <div class="body">
                            <div class="name"><?= h($name) ?></div>
                            <div class="cat"><?= h($category) ?></div>
                            <div class="price"><?= money_vnd($price) ?></div>
                            <?php if ($suggested): ?><span class="suggest">Giá đề xuất khu vực xã</span><?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="section panel booking-shell" id="goi-tho">
        <div class="title"><h2>Dịch vụ gọi thợ</h2><span class="muted">Chọn nhóm dịch vụ và giá trước khi điền thông tin</span></div>
        <div class="service-head">
            <input id="serviceSearchInput" type="search" placeholder="Tìm dịch vụ hoặc nhóm thợ">
            <button class="btn dark" id="geminiQuoteButton" type="button">Trợ lí Gemini</button>
        </div>
        <div class="service-list" id="serviceList">
            <?php foreach ($services as $svc): ?>
                <?php $base = (int)$svc['base']; $publicPrice = $base > 0 ? (int)round($base * 1.10) : 0; ?>
                <button class="service-option choose-service<?= $base <= 0 ? ' is-contact' : '' ?>" type="button" data-group="<?= h($svc['group']) ?>" data-service="<?= h($svc['name']) ?>" data-base="<?= h($base) ?>">
                    <small><?= h($svc['group']) ?></small>
                    <strong><?= $publicPrice > 0 ? money_vnd($publicPrice) : 'Liên hệ' ?></strong>
                    <span><?= h($svc['name']) ?></span>
                    <small><?= $publicPrice > 0 ? 'Đã gồm VAT' : 'Báo giá sau khi tư vấn' ?></small>
                </button>
            <?php endforeach; ?>
        </div>
        <div class="gemini-panel" id="geminiPanel">
            <textarea id="geminiQuestion" rows="3" maxlength="1000" placeholder="Nhập nhu cầu, ví dụ: vệ sinh máy lạnh 1HP ở Lấp Vò"></textarea>
            <div class="gemini-actions">
                <button class="btn" id="askGeminiButton" type="button">Tư vấn báo giá</button>
                <button class="btn dark" id="closeGeminiButton" type="button">Đóng</button>
            </div>
            <div class="gemini-reply" id="geminiReply"></div>
        </div>
        <form id="bookingForm">
            <h3>Thông tin yêu cầu</h3>
            <div class="form">
                <div class="field"><label for="service_type">Nhóm dịch vụ</label><select id="service_type" name="service_type"><option>Thợ điện lạnh</option><option>Thợ máy lọc nước</option><option>Thợ tivi</option><option>Thợ điện thoại</option><option>Thợ gia dụng</option></select></div>
                <div class="field"><label for="customer_price_display">Giá tham khảo đã gồm VAT</label><input class="readonly-price" id="customer_price_display" type="text" readonly placeholder="Chọn dịch vụ ở danh sách phía trên"></div>
                <input id="tech_target_base" name="tech_target_base" type="hidden">
                <input id="selected_service_name" name="selected_service_name" type="hidden">
                <div class="field"><label for="customer_name">Tên khách</label><input id="customer_name" name="customer_name" required maxlength="150"></div>
                <div class="field"><label for="phone">Số điện thoại</label><input id="phone" name="phone" type="tel" inputmode="numeric" pattern="[0-9]{8,15}" required maxlength="15" placeholder="09xxxxxxxx"></div>
                <div class="field full">
                    <label for="address">Địa chỉ</label>
                    <input id="address" name="address" required maxlength="500" placeholder="Bấm vào bản đồ để chọn vị trí">
                    <div class="map-actions">
                        <button class="btn dark" id="useCurrentLocation" type="button">Dùng vị trí hiện tại</button>
                        <button class="btn" id="clearLocation" type="button">Xóa vị trí</button>
                    </div>
                    <input type="hidden" id="map_location" name="map_location">
                    <input type="hidden" id="map_lat" name="map_lat">
                    <input type="hidden" id="map_lng" name="map_lng">
                    <div class="map-preview">
                        <div class="location-map" id="locationMap" aria-label="Bản đồ chọn vị trí"></div>
                        <div class="location-status" id="locationStatus">Bấm vào bản đồ hoặc dùng vị trí hiện tại.</div>
                    </div>
                </div>
                <div class="field full"><label for="issue_description">Mô tả sự cố</label><textarea id="issue_description" name="issue_description" required maxlength="2000"></textarea></div>
            </div>
            <input type="hidden" id="description" name="description">
            <input type="hidden" id="device_fingerprint" name="device_fingerprint">
            <p class="muted">Giá công khai đã gồm VAT. Vật tư hoặc linh kiện phát sinh sẽ được báo riêng trước khi làm.</p>
            <button id="bookingSubmit" type="submit">Gửi yêu cầu gọi thợ</button>
            <div id="bookingStatus" class="status"></div>
        </form>
    </section>

    <section class="section" id="quy-che">
        <div class="title"><h2>Thông tin hoạt động</h2><span class="muted">Minh bạch quy trình và dữ liệu</span></div>
        <div class="legal-grid">
            <details class="legal-item">
                <summary>Quy chế hoạt động</summary>
                <div class="legal-content">
                    <p>Quy trình: khách chọn dịch vụ và gửi yêu cầu; hệ thống chuyển ca đến nhóm thợ phù hợp; thợ nhận ca, liên hệ xác nhận; thông tin cần thiết được gửi cho thợ; thợ báo hoàn thành sau khi xử lý.</p>
                    <p>Chi phí vật tư, linh kiện hoặc công việc phát sinh phải được báo và được khách đồng ý trước khi thực hiện.</p>
                    <p>Khách có thể yêu cầu hỗ trợ, bảo hành hoặc phản ánh qua hotline của Điện Tử Hiếu.</p>
                </div>
            </details>
            <details class="legal-item">
                <summary>Đề án hoạt động</summary>
                <div class="legal-content">
                    <p>Điện Tử Hiếu kết nối nhu cầu mua hàng và gọi thợ kỹ thuật tại địa phương, ưu tiên công khai giá, điều phối đúng chuyên môn và xác nhận hoàn thành.</p>
                    <p>Dịch vụ được vận hành theo phạm vi phục vụ thực tế, năng lực thợ và tình trạng hàng hóa tại từng thời điểm.</p>
                </div>
            </details>
            <details class="legal-item">
                <summary>Chính sách bảo mật</summary>
                <div class="legal-content">
                    <p>Thông tin tên, số điện thoại, địa chỉ và vị trí chỉ được dùng để xử lý đơn hàng, điều phối thợ và hỗ trợ sau bán hàng.</p>
                    <p>Khách có quyền yêu cầu kiểm tra, cập nhật hoặc ngừng sử dụng thông tin đã cung cấp bằng cách liên hệ hotline.</p>
                </div>
            </details>
        </div>
    </section>
</div></main>

<footer><div class="wrap">
    <div class="footer-grid">
        <div>
            <h3>CÔNG TY TNHH MTV ĐIỆN TỬ HIẾU</h3>
            <p>MST: 1402228630</p>
            <p>Địa chỉ: 166, Ấp Bình Thạnh 1, Xã Lấp Vò, Tỉnh Đồng Tháp</p>
            <p>Website: dienmayhieu.com</p>
            <p>Khu vực phục vụ: Lấp Vò, Đồng Tháp</p>
        </div>
        <div><h3>Liên hệ</h3><p>Hotline: 0979.553.289</p><p>Mua hàng và gọi thợ kỹ thuật</p></div>
        <div><h3>Thông pháp lý</h3><p><a href="javascript:void(0)" onclick="openModal('quyche')">Quy chế hoạt động</a></p><p><a href="javascript:void(0)" onclick="openModal('dean')">Đề án hoạt động</a></p><p><a href="javascript:void(0)" onclick="openModal('baomat')">Chính sách bảo mật</a></p></div>
        <div>
            <h3>Truy cập nhanh</h3>
            <?php if ($qrWeb !== ''): ?><img src="<?= h($qrWeb) ?>" alt="QR truy cập" style="max-width: 120px; border-radius: 8px; background: white; padding: 5px; margin-top: 5px;"><?php endif; ?>
        </div>
    </div>
    <div class="footer-bottom"><span>© Điện Tử Hiếu</span><span>Website đang chờ duyệt</span></div>
</div></footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
'use strict';

const cards = Array.from(document.querySelectorAll('.product'));
const searchInput = document.getElementById('searchInput');
let activeCategory = '';

function normalize(value) {
    return String(value || '').toLowerCase();
}

function formatVnd(value) {
    return new Intl.NumberFormat('vi-VN').format(Number(value || 0)) + ' VND';
}

function filterProducts() {
    const q = normalize(searchInput.value);
    cards.forEach(card => {
        const okSearch = !q || card.dataset.name.indexOf(q) !== -1 || card.dataset.category.indexOf(q) !== -1;
        const okCategory = !activeCategory || card.dataset.category.indexOf(normalize(activeCategory)) !== -1;
        card.style.display = okSearch && okCategory ? '' : 'none';
    });
}

document.getElementById('searchForm').addEventListener('submit', event => {
    event.preventDefault();
    filterProducts();
});
searchInput.addEventListener('input', filterProducts);

document.querySelectorAll('[data-category]').forEach(button => {
    button.addEventListener('click', () => {
        document.querySelectorAll('[data-category]').forEach(item => item.classList.remove('active'));
        button.classList.add('active');
        activeCategory = button.dataset.category || '';
        filterProducts();
    });
});

const serviceSearchInput = document.getElementById('serviceSearchInput');
if (serviceSearchInput) {
    serviceSearchInput.addEventListener('input', () => {
        const q = normalize(serviceSearchInput.value);
        document.querySelectorAll('.service-option').forEach(option => {
            option.style.display = !q || normalize(option.textContent).indexOf(q) !== -1 ? '' : 'none';
        });
    });
}

document.querySelectorAll('.choose-service').forEach(button => {
    button.addEventListener('click', () => {
        document.querySelectorAll('.choose-service').forEach(item => item.classList.remove('selected'));
        button.classList.add('selected');
        const base = Number(button.dataset.base || 0);
        document.getElementById('service_type').value = button.dataset.group || 'Thợ điện lạnh';
        document.getElementById('tech_target_base').value = button.dataset.base || '';
        document.getElementById('issue_description').value = button.dataset.service || '';
        document.getElementById('selected_service_name').value = button.dataset.service || '';
        document.getElementById('customer_price_display').value = base > 0 ? formatVnd(Math.round(base * 1.10)) + ' - đã gồm VAT' : 'Liên hệ để báo giá';
    });
});

const addressInput = document.getElementById('address');
const locationStatus = document.getElementById('locationStatus');
const defaultLocation = [10.357422, 105.522124];
let locationMap = null;
let locationMarker = null;

function setLocationStatus(text) {
    if (locationStatus) {
        locationStatus.textContent = text;
    }
}

async function syncSelectedLocation(lat, lng, resolveAddress) {
    const latitude = Number(lat).toFixed(6);
    const longitude = Number(lng).toFixed(6);
    const coords = latitude + ',' + longitude;
    document.getElementById('map_location').value = coords;
    document.getElementById('map_lat').value = latitude;
    document.getElementById('map_lng').value = longitude;

    if (locationMap && window.L) {
        if (!locationMarker) {
            locationMarker = L.marker([lat, lng], {draggable: true}).addTo(locationMap);
            locationMarker.on('dragend', event => {
                const point = event.target.getLatLng();
                syncSelectedLocation(point.lat, point.lng, true);
            });
        } else {
            locationMarker.setLatLng([lat, lng]);
        }
    }

    if (!resolveAddress) {
        setLocationStatus('Vị trí đã đồng bộ: ' + coords);
        return;
    }

    addressInput.value = 'Vị trí đã chọn: ' + coords;
    setLocationStatus('Đang lấy địa chỉ cho vị trí ' + coords + '...');
    try {
        const response = await fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&accept-language=vi&lat=' + encodeURIComponent(latitude) + '&lon=' + encodeURIComponent(longitude));
        if (!response.ok) {
            throw new Error('Không lấy được địa chỉ');
        }
        const data = await response.json();
        if (data.display_name) {
            addressInput.value = data.display_name;
        }
        setLocationStatus('Đã đồng bộ vị trí: ' + coords);
    } catch (error) {
        setLocationStatus('Đã đồng bộ tọa độ. Có thể sửa lại địa chỉ nếu cần.');
    }
}

if (window.L && document.getElementById('locationMap')) {
    locationMap = L.map('locationMap').setView(defaultLocation, 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(locationMap);

    const LocateControl = L.Control.extend({
        options: { position: 'topleft' },
        onAdd: function (map) {
            const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
            container.style.backgroundColor = 'white';
            container.style.width = '34px';
            container.style.height = '34px';
            container.style.cursor = 'pointer';
            container.style.backgroundImage = 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'black\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Ccircle cx=\'12\' cy=\'12\' r=\'10\'/%3E%3Ccircle cx=\'12\' cy=\'12\' r=\'3\'/%3E%3C/svg%3E")';
            container.style.backgroundSize = '20px';
            container.style.backgroundRepeat = 'no-repeat';
            container.style.backgroundPosition = 'center';
            container.title = 'Vị trí của tôi';
            container.onclick = function(e){
                e.preventDefault();
                document.getElementById('useCurrentLocation')?.click();
            }
            return container;
        }
    });
    locationMap.addControl(new LocateControl());

    locationMap.on('click', event => syncSelectedLocation(event.latlng.lat, event.latlng.lng, true));
    window.setTimeout(() => locationMap.invalidateSize(), 100);
} else {
    setLocationStatus('Bản đồ chưa tải được. Vui lòng nhập địa chỉ trực tiếp.');
}

document.getElementById('useCurrentLocation')?.addEventListener('click', () => {
    if (!navigator.geolocation) {
        showBookingStatus('err', 'Trình duyệt chưa hỗ trợ lấy vị trí.');
        return;
    }
    navigator.geolocation.getCurrentPosition(position => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        if (locationMap) {
            locationMap.setView([lat, lng], 17);
        }
        syncSelectedLocation(lat, lng, true);
    }, () => showBookingStatus('err', 'Không lấy được vị trí hiện tại.'));
});

document.getElementById('clearLocation')?.addEventListener('click', () => {
    document.getElementById('map_location').value = '';
    document.getElementById('map_lat').value = '';
    document.getElementById('map_lng').value = '';
    addressInput.value = '';
    if (locationMap && locationMarker) {
        locationMap.removeLayer(locationMarker);
        locationMarker = null;
        locationMap.setView(defaultLocation, 14);
    }
    setLocationStatus('Bấm vào bản đồ hoặc dùng vị trí hiện tại.');
});

const geminiPanel = document.getElementById('geminiPanel');
const geminiReply = document.getElementById('geminiReply');
document.getElementById('geminiQuoteButton')?.addEventListener('click', () => {
    geminiPanel.classList.add('active');
    document.getElementById('geminiQuestion').focus();
});
document.getElementById('closeGeminiButton')?.addEventListener('click', () => {
    geminiPanel.classList.remove('active');
});
document.getElementById('askGeminiButton')?.addEventListener('click', async () => {
    const question = document.getElementById('geminiQuestion').value.trim();
    const selected = document.getElementById('issue_description').value.trim();
    geminiReply.classList.add('active');
    geminiReply.textContent = 'Đang tư vấn báo giá...';
    try {
        const response = await fetch('api_master.php?action=gemini_chat', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                message: question || selected || 'Tư vấn giá dịch vụ Điện Tử Hiếu',
                service_type: document.getElementById('service_type').value,
                selected_service: selected,
                public_price: document.getElementById('customer_price_display').value,
                address: addressInput ? addressInput.value : ''
            })
        });
        const data = await response.json();
        if (!response.ok || data.status !== 'success') {
            throw new Error(data.message || 'Không tư vấn được lúc này.');
        }
        geminiReply.textContent = data.reply || 'Chưa có nội dung tư vấn.';
    } catch (error) {
        geminiReply.textContent = error.message || 'Lỗi kết nối trợ lí.';
    }
});

function deviceId() {
    let id = localStorage.getItem('dth_device_id');
    if (!id) {
        id = 'dth-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2);
        localStorage.setItem('dth_device_id', id);
    }
    return id;
}

function showBookingStatus(type, text) {
    const box = document.getElementById('bookingStatus');
    box.className = 'status ' + (type === 'ok' ? 'ok' : 'err');
    box.textContent = text;
}

document.getElementById('bookingForm').addEventListener('submit', async event => {
    event.preventDefault();

    const form = event.currentTarget;
    const submitButton = document.getElementById('bookingSubmit');
    const phone = document.getElementById('phone').value.trim();
    const issue = document.getElementById('issue_description').value.trim();
    const selectedService = document.getElementById('selected_service_name').value.trim();
    const mapLat = document.getElementById('map_lat').value.trim();
    const mapLng = document.getElementById('map_lng').value.trim();

    if (!selectedService) {
        showBookingStatus('err', 'Vui lòng chọn dịch vụ trong danh sách phía trên trước khi gửi form.');
        return;
    }

    if (!/^[0-9]{8,15}$/.test(phone)) {
        showBookingStatus('err', 'Số điện thoại chỉ được nhập số, từ 8 đến 15 chữ số.');
        return;
    }

    if (!mapLat || !mapLng) {
        showBookingStatus('err', 'Vui lòng bấm chọn và xác nhận tọa độ trên bản đồ trước khi gửi yêu cầu.');
        locationStatus?.scrollIntoView({behavior: 'smooth', block: 'center'});
        return;
    }

    document.getElementById('description').value = issue;
    document.getElementById('device_fingerprint').value = deviceId();

    const formData = new FormData(form);
    submitButton.disabled = true;
    submitButton.textContent = 'Đang gửi...';
    showBookingStatus('ok', 'Đang gửi yêu cầu...');

    try {
        const response = await fetch('api_master.php?action=create_job', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (!response.ok || !(data.status === 'success' || data.success === true)) {
            throw new Error(data.message || 'Không gửi được yêu cầu.');
        }
        alert('Đã báo ca thành công!');
        form.reset();
        document.getElementById('customer_price_display').value = '';
        document.getElementById('map_location').value = '';
        document.getElementById('map_lat').value = '';
        document.getElementById('map_lng').value = '';
        document.querySelectorAll('.choose-service').forEach(item => item.classList.remove('selected'));
        if (locationMap && locationMarker) {
            locationMap.removeLayer(locationMarker);
            locationMarker = null;
            locationMap.setView(defaultLocation, 14);
        }
        setLocationStatus('Bấm vào bản đồ hoặc dùng vị trí hiện tại.');
        showBookingStatus('ok', 'Yêu cầu đã gửi thành công.');
    } catch (error) {
        showBookingStatus('err', error.message || 'Lỗi kết nối backend.');
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Gửi yêu cầu gọi thợ';
    }
});
</script>
<div id="modalPolicy" class="dth-modal">
    <div class="dth-modal-content">
        <span class="dth-modal-close" onclick="closeModal()">&times;</span>
        <div id="modalTitle" class="dth-modal-title">Tiêu đề</div>
        <div id="modalBody" class="dth-modal-body">Nội dung</div>
    </div>
</div>
<script>
const policies = {
    'quyche': {
        title: 'Quy Chế Hoạt Động',
        body: `
            <h4>1. Nguyên tắc chung</h4>
            <p>Nền tảng Điện Máy Hiếu là ứng dụng số hỗ trợ kết nối khách hàng với thợ kỹ thuật sửa chữa, lắp đặt thiết bị điện tử, điện lạnh, gia dụng tại địa bàn cấp xã và khu vực lân cận.</p>
            <h4>2. Quy định dành cho Khách Hàng</h4>
            <ul>
                <li>Cung cấp thông tin liên hệ và tình trạng sự cố trung thực, chính xác.</li>
                <li>Thanh toán đầy đủ chi phí dịch vụ và vật tư trực tiếp cho thợ sau khi nghiệm thu công việc.</li>
                <li>Có quyền đánh giá, phản ánh chất lượng dịch vụ trực tiếp lên hệ thống Hotline để công ty xử lý.</li>
            </ul>
            <h4>3. Quy định dành cho Đối Tác Thợ Kỹ Thuật</h4>
            <ul>
                <li><strong>Ký kết hợp tác:</strong> Các thợ tham gia hệ thống phải ký kết hợp đồng hợp tác lao động với CÔNG TY TNHH MTV ĐIỆN TỬ HIẾU, cung cấp đầy đủ hồ sơ nhân thân để đảm bảo an toàn cho khách hàng.</li>
                <li><strong>Tiếp nhận công việc:</strong> Nhận lệnh điều phối tự động qua nền tảng ứng dụng nhóm chat Telegram do công ty quản lý và sử dụng bot Telegram để hỗ trợ báo cáo, cập nhật trạng thái đơn hàng.</li>
                <li><strong>Trách nhiệm:</strong> Tuân thủ đạo đức nghề nghiệp, thái độ phục vụ chuẩn mực. Cam kết bảo hành các linh kiện và dịch vụ đã thi công.</li>
                <li><strong>Nghĩa vụ tài chính:</strong> Tuân thủ nghĩa vụ thanh toán chiết khấu (phí nền tảng) đúng hạn để duy trì quyền lợi nhận ca.</li>
            </ul>
            <h4>4. Giải quyết tranh chấp</h4>
            <p>Mọi tranh chấp phát sinh giữa khách hàng và thợ sẽ được CÔNG TY TNHH MTV ĐIỆN TỬ HIẾU đứng ra làm trung gian tiếp nhận, hòa giải dựa trên quy định pháp luật và quyền lợi chính đáng của người tiêu dùng.</p>
        `
    },
    'dean': {
        title: 'Đề Án Hoạt Động & Tầm Nhìn',
        body: `
            <h4>1. Tên đề án</h4>
            <p><strong>Xây dựng Nền tảng số Dịch vụ Kỹ thuật và Thương mại Điện tử tại địa bàn Nông thôn mới.</strong></p>
            <h4>2. Mục tiêu đề án</h4>
            <ul>
                <li>Ứng dụng công nghệ thông tin vào đời sống thiết thực, mang lại trải nghiệm <em>"gọi thợ số"</em> nhanh chóng, tiện lợi và minh bạch cho người dân trong khu vực xã và huyện.</li>
                <li>Số hóa quy trình làm việc truyền thống của các thợ kỹ thuật tại địa phương.</li>
            </ul>
            <h4>3. Đơn vị phát triển</h4>
            <p>Đề án được đầu tư, nghiên cứu và phát triển bởi Đơn vị tư nhân <strong>CÔNG TY TNHH MTV ĐIỆN TỬ HIẾU</strong>. Chúng tôi mang khát vọng phát triển quê hương bằng tri thức công nghệ, đóng góp vào công cuộc chuyển đổi số quốc gia từ cấp cơ sở.</p>
            <h4>4. Mô hình hoạt động</h4>
            <ul>
                <li><strong>Hệ sinh thái Kinh tế chia sẻ (Sharing Economy):</strong> Nền tảng hoạt động như một cầu nối. Công ty đầu tư hạ tầng phần mềm, máy chủ, marketing. Thợ địa phương tham gia với tư cách đối tác tự do.</li>
                <li>Tạo ra công ăn việc làm ổn định, tăng thu nhập cho lao động có tay nghề tại địa phương mà không gò bó thời gian.</li>
                <li>Ứng dụng tự động hóa thông qua Telegram Bot để tiết giảm tối đa chi phí vận hành, từ đó mang lại mức giá dịch vụ tốt nhất cho bà con.</li>
            </ul>
            <h4>5. Tầm nhìn chiến lược</h4>
            <p>Điện Máy Hiếu hướng tới mục tiêu trở thành nền tảng ứng dụng số kiểu mẫu phục vụ thiết thực cho đời sống, dễ dàng nhân rộng sang các địa bàn cấp xã khác, góp sức kiến tạo nên bức tranh Nông Thôn Mới hiện đại, số hóa và văn minh.</p>
        `
    },
    'baomat': {
        title: 'Chính Sách Bảo Mật',
        body: `
            <h4>1. Mục đích thu thập thông tin</h4>
            <p>Chúng tôi thu thập các thông tin bao gồm Tên, Số điện thoại, Địa chỉ và Tọa độ GPS của khách hàng duy nhất cho mục đích: xử lý đơn đặt hàng, điều phối thợ kỹ thuật đến đúng vị trí, và chăm sóc bảo hành sau dịch vụ.</p>
            <h4>2. Phạm vi sử dụng dữ liệu</h4>
            <ul>
                <li>Thông tin được lưu chuyển nội bộ trên hệ thống máy chủ công ty và gửi thông báo qua kênh Telegram bảo mật riêng của nhóm thợ.</li>
                <li>Tất cả thợ tham gia đều đã ký cam kết bảo mật thông tin khách hàng trong hợp đồng hợp tác lao động.</li>
                <li>Tuyệt đối <strong>KHÔNG</strong> bán, trao đổi hay chia sẻ dữ liệu cá nhân của khách hàng cho bất kỳ bên thứ 3 nào với mục đích thương mại.</li>
            </ul>
            <h4>3. Thời gian lưu trữ</h4>
            <p>Dữ liệu khách hàng được lưu trữ an toàn trên máy chủ cho đến khi khách hàng có yêu cầu hủy bỏ hoặc công ty ngừng cung cấp dịch vụ theo quy định pháp luật.</p>
            <h4>4. Cam kết bảo mật</h4>
            <p>Chúng tôi áp dụng các chuẩn mực bảo mật dữ liệu trên website và hệ thống API để ngăn ngừa mọi hành vi truy cập trái phép, rò rỉ dữ liệu.</p>
            <h4>5. Quyền lợi của khách hàng</h4>
            <p>Khách hàng có quyền yêu cầu tra cứu, chỉnh sửa hoặc xóa bỏ hoàn toàn thông tin cá nhân của mình khỏi hệ thống bằng cách liên hệ trực tiếp qua Hotline của công ty.</p>
        `
    }
};

function openModal(type) {
    const data = policies[type];
    if (data) {
        document.getElementById('modalTitle').innerHTML = data.title;
        document.getElementById('modalBody').innerHTML = data.body;
        document.getElementById('modalPolicy').style.display = 'block';
    }
}

function closeModal() {
    document.getElementById('modalPolicy').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('modalPolicy');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

<div id="modalLogin" class="dth-modal">
    <div class="dth-modal-content" style="max-width: 450px; text-align: center;">
        <span class="dth-modal-close" onclick="document.getElementById('modalLogin').style.display='none'">&times;</span>
        <h2 style="margin-top: 10px; color: #111827; font-size: 22px; font-weight: 800;">Chào mừng bạn đến với<br>Điện Máy Hiếu!</h2>
        <p style="color: #6b7280; font-size: 14px; margin-bottom: 25px;">Đăng nhập để tích điểm và nhận ưu đãi</p>
        
        <div id="loginMethods">
            <button class="btn" style="width: 100%; margin-bottom: 10px; background: #2563eb; color: white;" onclick="showOtpForm()">Tiếp tục với Số điện thoại</button>
            <button class="btn" style="width: 100%; margin-bottom: 10px; background: #1877f2; color: white;" onclick="alert('Tính năng đang phát triển!')">Tiếp tục với Facebook</button>
            <button class="btn" style="width: 100%; margin-bottom: 20px; background: #ea4335; color: white;" onclick="alert('Tính năng đang phát triển!')">Tiếp tục với Google</button>
            <button class="btn" style="width: 100%; margin-bottom: 20px; background: #10b981; color: white;" onclick="showQrLogin()">Đăng nhập bằng QR Thành Viên</button>
            
            <div style="font-size: 12px; color: #6b7280; text-align: left; background: #f9fafb; padding: 10px; border-radius: 6px;">
                <label style="display: flex; gap: 8px; font-weight: normal; cursor: pointer;">
                    <input type="checkbox" id="tosCheck" checked>
                    <span>Bằng việc tiếp tục, tôi xác nhận đã đọc và đồng ý với <a href="javascript:void(0)" onclick="openModal('quyche'); document.getElementById('modalLogin').style.display='none'" style="color: #dc2626;">Điều khoản dịch vụ</a> và <a href="javascript:void(0)" onclick="openModal('baomat'); document.getElementById('modalLogin').style.display='none'" style="color: #dc2626;">Chính sách bảo mật</a> của Điện Máy Hiếu.</span>
                </label>
            </div>
        </div>

        <div id="otpForm" style="display: none; text-align: left;">
            <label style="display:block; margin-bottom: 5px;">Số điện thoại</label>
            <input type="tel" placeholder="Nhập số điện thoại của bạn" style="margin-bottom: 15px;">
            <button class="btn dark" style="width: 100%;" onclick="showMemberQr()">Nhận OTP & Đăng nhập (Demo)</button>
            <button class="btn" style="width: 100%; margin-top: 10px; background: transparent; color: #666; border: none; box-shadow: none;" onclick="document.getElementById('otpForm').style.display='none'; document.getElementById('loginMethods').style.display='block';">Quay lại</button>
        </div>

        <div id="qrLoginForm" style="display: none; text-align: center;">
            <p style="margin-bottom: 15px; font-weight: bold; color: #374151;">Tải lên ảnh QR Thành viên của bạn</p>
            <input type="file" accept="image/*" style="margin-bottom: 15px; border: 1px dashed #ccc; padding: 20px; width: 100%;">
            <button class="btn dark" style="width: 100%;" onclick="showMemberQr()">Xác nhận (Demo)</button>
            <button class="btn" style="width: 100%; margin-top: 10px; background: transparent; color: #666; border: none; box-shadow: none;" onclick="document.getElementById('qrLoginForm').style.display='none'; document.getElementById('loginMethods').style.display='block';">Quay lại</button>
        </div>
        
        <div id="memberQrDisplay" style="display: none;">
            <h3 style="color: #047857; margin-bottom: 10px;">Đăng nhập thành công!</h3>
            <p style="font-size: 14px; font-weight: bold; margin-bottom: 15px; color: #111827;">Mã QR Thành viên của bạn</p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=DIENMAYHIEU-MEMBER-DEMO" alt="Member QR" style="border-radius: 10px; border: 2px solid #dc2626; padding: 10px; margin-bottom: 15px; display: inline-block;">
            <p style="font-size: 13px; color: #666; margin-bottom: 20px;">Vui lòng lưu lại và đưa QR này cho thu ngân/thợ quét khi mua hàng hoặc gọi thợ để tích điểm!</p>
            
            <div style="text-align: left; background: #fef2f2; padding: 15px; border-radius: 8px; font-size: 13px;">
                <strong style="display: block; color: #dc2626; margin-bottom: 5px;">THÔNG TIN PHÁT HÀNH - CÔNG TY TNHH MTV ĐIỆN TỬ HIẾU</strong>
                <p style="margin-bottom: 5px; font-weight: 600; color: #7f1d1d;">Tích điểm thành viên (Tổng giá trị mua hàng + Gọi thợ)</p>
                <ul style="padding-left: 20px; margin-bottom: 10px; color: #991b1b;">
                    <li style="margin-bottom: 4px;">Đạt mốc <strong>10.000.000đ</strong>: Mặc định giảm <strong>3%</strong> khi mua hàng</li>
                    <li style="margin-bottom: 4px;">Đạt mốc <strong>50.000.000đ</strong>: Mặc định giảm <strong>5%</strong> khi mua hàng</li>
                    <li>Đạt mốc <strong>100.000.000đ</strong>: Mặc định giảm <strong>10%</strong> khi mua hàng</li>
                </ul>
                <p style="font-size: 12px; color: #b91c1c; font-style: italic;">* Tích điểm được tính theo năm và sẽ tự động reset vào lúc 23:59 ngày 31/12 hàng năm.</p>
            </div>
        </div>
    </div>
</div>

<script>
function openLoginModal() {
    document.getElementById('modalLogin').style.display = 'block';
    document.getElementById('loginMethods').style.display = 'block';
    document.getElementById('otpForm').style.display = 'none';
    document.getElementById('qrLoginForm').style.display = 'none';
    document.getElementById('memberQrDisplay').style.display = 'none';
}
function showOtpForm() {
    if(!document.getElementById('tosCheck').checked) return alert('Vui lòng đồng ý với Điều khoản dịch vụ và Chính sách bảo mật!');
    document.getElementById('loginMethods').style.display = 'none';
    document.getElementById('otpForm').style.display = 'block';
}
function showQrLogin() {
    if(!document.getElementById('tosCheck').checked) return alert('Vui lòng đồng ý với Điều khoản dịch vụ và Chính sách bảo mật!');
    document.getElementById('loginMethods').style.display = 'none';
    document.getElementById('qrLoginForm').style.display = 'block';
}
function showMemberQr() {
    document.getElementById('otpForm').style.display = 'none';
    document.getElementById('qrLoginForm').style.display = 'none';
    document.getElementById('memberQrDisplay').style.display = 'block';
    
    document.getElementById('topBarStatus').innerHTML = 'Xin chào, Thành Viên! <a href="javascript:void(0)" onclick="openLoginModal()" style="color:#fde047; margin-left:15px; font-weight:bold; text-decoration:underline;">Thẻ thành viên của tôi</a>';
}
</script>
</body>
</html>
