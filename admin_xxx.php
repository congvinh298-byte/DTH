<?php
declare(strict_types=1);

/*
 * Dien Tu Hieu - emergency admin console.
 * Keep this file simple: authenticate, render a control panel, let api_master.php
 * do the database work through secured AJAX endpoints.
 */

header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
date_default_timezone_set('Asia/Ho_Chi_Minh');

const DTH_DEFAULT_ADMIN_HASH = '$2y$12$PKMb6p4cl7PeYD7EEfpEg.NF2cqFJdgs/vnAXCHbiUYQbBDePJSOa'; // password_hash('845409', PASSWORD_BCRYPT)

function app_load_env($path = null)
{
    $path = $path ?? __DIR__ . '/.env';
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
        if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') || (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
            $value = substr($value, 1, -1);
        }
        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

function app_env($key, $default = '')
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    if ($value === false || $value === null || $value === '') {
        return (string)$default;
    }
    return (string)$value;
}

function app_is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}

function app_ensure_session()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => app_is_https(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        session_set_cookie_params(0, '/', '', app_is_https(), true);
    }
    session_start();
}

function app_admin_logout()
{
    app_ensure_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', $params['secure'], $params['httponly']);
    }
    session_destroy();
}

app_load_env();

function h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function dth_admin_qr_src($code)
{
    return 'https://api.qrserver.com/v1/create-qr-code/?size=96x96&margin=6&data=' . rawurlencode((string)$code);
}

function dth_admin_password_ok($password)
{
    $hash = app_env('ADMIN_PASS_HASH', '');
    if ($hash !== '' && password_verify($password, $hash)) {
        return true;
    }

    $plain = app_env('ADMIN_PASS', '');
    if ($plain !== '' && hash_equals($plain, $password)) {
        return true;
    }

    return password_verify($password, DTH_DEFAULT_ADMIN_HASH);
}

function dth_admin_csrf()
{
    app_ensure_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string)$_SESSION['csrf_token'];
}

function dth_admin_db()
{
    $dsn = 'mysql:host=' . app_env('DB_HOST', 'localhost') . ';dbname=' . app_env('DB_NAME', 'kwkrbcce_Goixelapvo') . ';charset=' . app_env('DB_CHARSET', 'utf8mb4');
    return new PDO($dsn, app_env('DB_USER', 'kwkrbcce_baocao'), app_env('DB_PASS', 'SayTHC369@'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

function dth_admin_column_exists(PDO $pdo, $table, $column)
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function dth_admin_add_column(PDO $pdo, $table, $column, $definition)
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', (string)$table) || !preg_match('/^[A-Za-z0-9_]+$/', (string)$column)) {
        return;
    }
    if (!dth_admin_column_exists($pdo, $table, $column)) {
        $pdo->exec('ALTER TABLE `' . $table . '` ADD COLUMN `' . $column . '` ' . $definition);
    }
}

function dth_admin_index_exists(PDO $pdo, $table, $index)
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?');
    $stmt->execute([$table, $index]);
    return (int)$stmt->fetchColumn() > 0;
}

function dth_admin_add_index(PDO $pdo, $table, $index, $definition)
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', (string)$table) || !preg_match('/^[A-Za-z0-9_]+$/', (string)$index)) {
        return;
    }
    if (!dth_admin_index_exists($pdo, $table, $index)) {
        $pdo->exec('ALTER TABLE `' . $table . '` ADD INDEX `' . $index . '` ' . $definition);
    }
}

function dth_admin_auto_schema(PDO $pdo)
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(120) NULL,
        image VARCHAR(700) NULL,
        price INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    dth_admin_add_column($pdo, 'products', 'name', 'VARCHAR(255) NOT NULL DEFAULT ""');
    dth_admin_add_column($pdo, 'products', 'category', 'VARCHAR(120) NULL');
    dth_admin_add_column($pdo, 'products', 'image', 'VARCHAR(700) NULL');
    dth_admin_add_column($pdo, 'products', 'price', 'INT NOT NULL DEFAULT 0');
    dth_admin_add_index($pdo, 'products', 'idx_products_category', '(category)');
    dth_admin_add_index($pdo, 'products', 'idx_products_price', '(price)');

    $pdo->exec("CREATE TABLE IF NOT EXISTS qr_coupons (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(80) NOT NULL UNIQUE,
        discount_amount INT NOT NULL DEFAULT 0,
        quantity_left INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    dth_admin_add_column($pdo, 'qr_coupons', 'code', 'VARCHAR(80) NOT NULL');
    dth_admin_add_column($pdo, 'qr_coupons', 'discount_amount', 'INT NOT NULL DEFAULT 0');
    dth_admin_add_column($pdo, 'qr_coupons', 'quantity_left', 'INT NOT NULL DEFAULT 0');
    dth_admin_add_column($pdo, 'qr_coupons', 'type', "VARCHAR(30) NOT NULL DEFAULT 'discount'");
    dth_admin_add_column($pdo, 'qr_coupons', 'value', 'INT NOT NULL DEFAULT 0');
    dth_admin_add_column($pdo, 'qr_coupons', 'description', 'TEXT NULL');
    dth_admin_add_column($pdo, 'qr_coupons', 'is_used', 'TINYINT(1) NOT NULL DEFAULT 0');
    dth_admin_add_index($pdo, 'qr_coupons', 'idx_qr_coupons_code', '(code)');

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
    dth_admin_add_column($pdo, 'job_posts', 'customer_phone', 'VARCHAR(30) NULL');
    dth_admin_add_column($pdo, 'job_posts', 'issue', 'TEXT NULL');
    dth_admin_add_column($pdo, 'job_posts', 'status', "VARCHAR(30) NOT NULL DEFAULT 'pending'");
    dth_admin_add_column($pdo, 'job_posts', 'tech_target_price', 'INT NOT NULL DEFAULT 0');
    dth_admin_add_column($pdo, 'job_posts', 'final_price', 'INT NOT NULL DEFAULT 0');
    dth_admin_add_column($pdo, 'job_posts', 'bot_message_id', 'BIGINT NULL');
    dth_admin_add_index($pdo, 'job_posts', 'idx_job_posts_customer_phone', '(customer_phone)');
    dth_admin_add_index($pdo, 'job_posts', 'idx_job_posts_status', '(status)');

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
    dth_admin_add_index($pdo, 'banned_entities', 'idx_banned_entities_lookup', '(ip_or_phone, type)');
}

function dth_admin_money_int($value)
{
    return max(0, (int)(preg_replace('/[^\d]/', '', (string)$value) ?: 0));
}

function dth_admin_handle_promo(PDO $pdo)
{
    $action = (string)($_POST['promo_action'] ?? '');
    if ($action === '') {
        return '';
    }
    if (!hash_equals(dth_admin_csrf(), (string)($_POST['csrf'] ?? ''))) {
        return 'CSRF khong hop le.';
    }
    if ($action === 'create') {
        $code = strtoupper(trim((string)($_POST['code'] ?? '')));
        $discount = dth_admin_money_int($_POST['discount_amount'] ?? 0);
        $qty = max(0, (int)($_POST['quantity_left'] ?? 0));
        if ($code === '') {
            return 'Vui long nhap ma promo.';
        }
        $stmt = $pdo->prepare("INSERT INTO qr_coupons (code, discount_amount, quantity_left, type, value, description, created_at)
            VALUES (?, ?, ?, 'discount', ?, ?, NOW())
            ON DUPLICATE KEY UPDATE discount_amount = VALUES(discount_amount), quantity_left = VALUES(quantity_left), value = VALUES(value), description = VALUES(description)");
        $stmt->execute([$code, $discount, $qty, $discount, 'Promo code manual']);
        return 'Da luu promo code.';
    }
    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $qty = max(0, (int)($_POST['quantity_left'] ?? 0));
        $discount = dth_admin_money_int($_POST['discount_amount'] ?? 0);
        if ($id <= 0) {
            return 'ID promo khong hop le.';
        }
        $stmt = $pdo->prepare('UPDATE qr_coupons SET quantity_left = ?, discount_amount = ?, value = ? WHERE id = ?');
        $stmt->execute([$qty, $discount, $discount, $id]);
        return 'Da cap nhat promo.';
    }
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            return 'ID promo khong hop le.';
        }
        $stmt = $pdo->prepare('DELETE FROM qr_coupons WHERE id = ?');
        $stmt->execute([$id]);
        return 'Da xoa promo.';
    }
    return '';
}

function dth_admin_promos(PDO $pdo)
{
    try {
        $stmt = $pdo->query('SELECT id, code, discount_amount, quantity_left, type, value, description, is_used, created_at FROM qr_coupons ORDER BY id DESC LIMIT 300');
        return $stmt ? $stmt->fetchAll() : [];
    } catch (Throwable $e) {
        error_log('[admin promos] ' . $e->getMessage());
        return [];
    }
}

app_ensure_session();

$error = '';
$schemaError = '';
$promoMessage = '';
$promoRows = [];

if (isset($_GET['logout'])) {
    app_admin_logout();
    header('Location: admin_xxx.php');
    exit;
}

$loggedIn = !empty($_SESSION['admin_logged_in']);

if (!$loggedIn && (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST')) {
    $password = (string)($_POST['password'] ?? '');
    if (dth_admin_password_ok($password)) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_login_at'] = time();
        dth_admin_csrf();
        header('Location: admin_xxx.php');
        exit;
    }
    $error = 'Sai mat khau admin.';
}

$loggedIn = !empty($_SESSION['admin_logged_in']);

if ($loggedIn) {
    try {
        $adminPdo = dth_admin_db();
        dth_admin_auto_schema($adminPdo);
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $promoMessage = dth_admin_handle_promo($adminPdo);
        }
        $promoRows = dth_admin_promos($adminPdo);
    } catch (Throwable $e) {
        error_log('[admin schema] ' . $e->getMessage());
        $schemaError = 'Loi khoi tao database: ' . $e->getMessage();
    }
}

if (!$loggedIn):
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Dien Tu Hieu</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 64 64%22%3E%3Crect width=%2264%22 height=%2264%22 rx=%2212%22 fill=%22%23dc2626%22/%3E%3Ctext x=%2232%22 y=%2241%22 font-size=%2228%22 text-anchor=%22middle%22 fill=%22white%22 font-family=%22Arial%22 font-weight=%22700%22%3EH%3C/text%3E%3C/svg%3E">
    <style>
        body{margin:0;min-height:100vh;display:grid;place-items:center;background:#111827;font-family:Arial,sans-serif;color:#111827}
        .box{width:min(420px,92vw);background:#fff;border:1px solid #e5e7eb;padding:28px;border-radius:8px;box-shadow:0 20px 60px rgba(0,0,0,.25)}
        h1{font-size:22px;margin:0 0 18px}
        input,button{width:100%;box-sizing:border-box;padding:12px 14px;border-radius:6px;font-size:16px}
        input{border:1px solid #cbd5e1;margin-bottom:12px}
        button{border:0;background:#dc2626;color:#fff;font-weight:700;cursor:pointer}
        .err{padding:10px 12px;background:#fee2e2;color:#991b1b;border:1px solid #fecaca;border-radius:6px;margin-bottom:12px}
        .hint{font-size:12px;color:#64748b;margin-top:12px;line-height:1.5}
    </style>
</head>
<body>
<main class="box">
    <h1>Admin Dien Tu Hieu</h1>
    <?php if ($error !== ''): ?><div class="err"><?= h($error) ?></div><?php endif; ?>
    <form method="post" autocomplete="off">
        <input type="password" name="password" placeholder="Mat khau admin" required autofocus>
        <button type="submit">Dang nhap</button>
    </form>
    <div class="hint">Nen dat ADMIN_PASS_HASH trong file .env. File nay co fallback hash cho mat khau cu.</div>
</main>
</body>
</html>
<?php
exit;
endif;

$csrf = dth_admin_csrf();
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Van phong giam doc - Dien Tu Hieu</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 64 64%22%3E%3Crect width=%2264%22 height=%2264%22 rx=%2212%22 fill=%22%23dc2626%22/%3E%3Ctext x=%2232%22 y=%2241%22 font-size=%2228%22 text-anchor=%22middle%22 fill=%22white%22 font-family=%22Arial%22 font-weight=%22700%22%3EH%3C/text%3E%3C/svg%3E">
    <style>
        :root{--bg:#f8fafc;--panel:#fff;--line:#e2e8f0;--text:#0f172a;--muted:#64748b;--brand:#dc2626;--ok:#16a34a;--warn:#d97706}
        *{box-sizing:border-box}
        body{margin:0;background:var(--bg);color:var(--text);font-family:Arial,sans-serif}
        header{position:sticky;top:0;background:#111827;color:#fff;padding:14px 20px;display:flex;gap:12px;align-items:center;justify-content:space-between;z-index:2}
        header h1{font-size:18px;margin:0}
        header a{color:#fecaca;text-decoration:none;font-weight:700}
        main{padding:18px;max-width:1360px;margin:0 auto}
        nav{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px}
        nav button,.btn{border:1px solid var(--line);background:#fff;color:var(--text);padding:9px 12px;border-radius:6px;cursor:pointer;font-weight:700}
        nav button.active,.btn.primary{background:var(--brand);color:#fff;border-color:var(--brand)}
        .grid{display:grid;gap:12px}
        .stats{grid-template-columns:repeat(auto-fit,minmax(160px,1fr));margin-bottom:14px}
        .card{background:var(--panel);border:1px solid var(--line);border-radius:8px;padding:14px}
        .stat-label{font-size:12px;color:var(--muted);text-transform:uppercase}
        .stat-value{font-size:24px;font-weight:800;margin-top:6px}
        section{display:none}
        section.active{display:block}
        table{width:100%;border-collapse:collapse;background:#fff;border:1px solid var(--line);border-radius:8px;overflow:hidden}
        th,td{padding:9px 10px;border-bottom:1px solid var(--line);font-size:13px;text-align:left;vertical-align:top}
        th{background:#f1f5f9;color:#334155;font-size:12px;text-transform:uppercase}
        tr:last-child td{border-bottom:0}
        .thumb{width:46px;height:46px;object-fit:contain;background:#fff;border:1px solid var(--line);border-radius:6px;display:block}
        .qr-mini{width:70px;height:70px;object-fit:contain;background:#fff;border:1px solid var(--line);border-radius:6px;padding:4px;display:block}
        .badge{display:inline-block;padding:4px 8px;border-radius:999px;font-size:12px;font-weight:800;border:1px solid transparent}
        .badge.ok{background:#ecfdf3;color:#047857;border-color:#bbf7d0}.badge.used{background:#fef2f2;color:#b91c1c;border-color:#fecaca}.badge.warn{background:#fff7ed;color:#c2410c;border-color:#fed7aa}
        .price-mini{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:10px;margin-bottom:12px}.price-mini .card b{display:block;margin-bottom:6px}
        input,select,textarea{width:100%;padding:9px 10px;border:1px solid #cbd5e1;border-radius:6px;font:inherit;background:#fff}
        label{display:block;font-size:12px;color:#475569;font-weight:700;margin:10px 0 5px}
        .cols{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;margin-bottom:14px}
        .row-actions{display:flex;gap:6px;flex-wrap:wrap}
        .danger{background:#dc2626!important;color:#fff!important;border-color:#dc2626!important}
        .success{background:#16a34a!important;color:#fff!important;border-color:#16a34a!important}
        .warn{background:#d97706!important;color:#fff!important;border-color:#d97706!important}
        .muted{color:var(--muted)}
        .msg{margin:10px 0;padding:10px 12px;border-radius:6px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;display:none}
        .print-box{background:#fff;border:1px solid var(--line);border-radius:8px;padding:18px;max-width:620px}
        @media print{body>*:not(.print-only){display:none!important}.print-only{display:block!important}.print-box{border:0;max-width:none}}
    </style>
</head>
<body>
<header class="print-only" style="display:none"></header>
<header>
    <h1>Van phong giam doc - Dien Tu Hieu</h1>
    <div><span id="clock"></span> &nbsp; <a href="?logout=1">Dang xuat</a></div>
</header>
<main class="print-only" style="display:none" id="printRoot"></main>
<main class="print-hide">
    <nav>
        <button class="active" data-page="dash">Dashboard</button>
        <button data-page="orders">Don hang</button>
        <button data-page="jobs">Goi tho</button>
        <button data-page="products">Kho san pham</button>
        <button data-page="codes">Promo/QR</button>
        <button data-page="workers">Tho va Unban</button>
        <button data-page="invoices">In hoa don</button>
    </nav>

    <div id="globalMsg" class="msg"></div>
    <?php if ($schemaError !== ''): ?><div class="msg" style="display:block;background:#fef2f2;color:#991b1b;border-color:#fecaca"><?= h($schemaError) ?></div><?php endif; ?>
    <?php if ($promoMessage !== ''): ?><div class="msg" style="display:block"><?= h($promoMessage) ?></div><?php endif; ?>

    <section id="page-dash" class="active">
        <div class="grid stats" id="stats"></div>
    </section>

    <section id="page-orders">
        <h2>Don hang moi nhat</h2>
        <table><thead><tr><th>ID</th><th>Khach</th><th>Phone</th><th>San pham</th><th>Tien</th><th>TT</th><th>Ngay</th><th>Lenh</th></tr></thead><tbody id="ordersBody"></tbody></table>
    </section>

    <section id="page-jobs">
        <div class="row-actions" style="margin-bottom:12px">
            <button class="btn warn" onclick="sendTestJob()">Gui ca test len Bot 1</button>
            <button class="btn" onclick="loadJobs()">Tai lai</button>
        </div>
        <div class="price-mini">
            <div class="card"><b>Ve sinh may lanh</b><span>Tho nhan 150.000 VND, bao khach +10% VAT, phi nen tang 5%.</span></div>
            <div class="card"><b>Lap may lanh 1HP / 1.5HP</b><span>Cong 400.000 VND, chua gom vat tu phat sinh.</span></div>
            <div class="card"><b>Lap may lanh 2HP / 3HP</b><span>Cong 500.000 VND, may am tran lien he hang.</span></div>
            <div class="card"><b>Sua chua / tivi / loc nuoc</b><span>Cong tho 200.000 VND + linh kien/phu kien cong khai.</span></div>
        </div>
        <table><thead><tr><th>ID</th><th>Khach</th><th>Phone</th><th>Dich vu</th><th>Dia chi</th><th>Gia khach</th><th>Tho</th><th>TT</th><th>Ngay</th></tr></thead><tbody id="jobsBody"></tbody></table>
    </section>

    <section id="page-products">
        <div class="cols">
            <div class="card">
                <h2>Luu san pham</h2>
                <input type="hidden" id="prod_id">
                <label>Ten san pham</label><input id="prod_name">
                <label>Gia ban</label><input id="prod_price" type="number" min="0">
                <label>Ton kho</label><input id="prod_stock" type="number" min="0" value="100">
                <label>Danh muc</label><input id="prod_category">
                <label>URL anh</label><input id="prod_image">
                <button class="btn primary" onclick="saveProduct()">Luu</button>
            </div>
            <div class="card">
                <h2>Import nhanh</h2>
                <p class="muted">Moi dong: Danh_Muc, Ten_San_Pham, Gia_Nhap, Gia_Ban</p>
                <textarea id="import_rows" rows="9"></textarea>
                <button class="btn success" onclick="importProducts()">Import</button>
            </div>
        </div>
        <table><thead><tr><th>ID</th><th>Anh</th><th>Ten</th><th>Danh muc</th><th>Gia</th><th>Ton</th><th>Lenh</th></tr></thead><tbody id="productsBody"></tbody></table>
    </section>

    <section id="page-codes">
        <div class="cols">
            <div class="card">
                <h2>Quan ly Promo Code</h2>
                <form method="post">
                    <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
                    <input type="hidden" name="promo_action" value="create">
                    <label>Code</label><input name="code" placeholder="VD: DTH50K" required>
                    <label>Discount VND</label><input name="discount_amount" type="number" value="50000" min="0" required>
                    <label>Quantity</label><input name="quantity_left" type="number" value="10" min="0" required>
                    <button class="btn primary" type="submit">Tao / Cap nhat code</button>
                </form>
            </div>
            <div class="card">
                <h2>Tao voucher</h2>
                <label>Giam %</label><input id="voucher_percent" type="number" value="10" min="0" max="100">
                <label>So luong</label><input id="voucher_count" type="number" value="5" min="1" max="500">
                <button class="btn primary" onclick="generateVoucher()">Tao voucher</button>
                <div id="voucherResult" class="muted"></div>
            </div>
            <div class="card">
                <h2>Tao QR coupon</h2>
                <label>Gia tri VND</label><input id="qr_value" type="number" value="50000" min="0">
                <label>Mo ta</label><input id="qr_desc" value="Giam gia QR">
                <label>So luong</label><input id="qr_count" type="number" value="1" min="1" max="500">
                <button class="btn primary" onclick="generateQR()">Tao QR</button>
                <div id="qrResult" class="muted"></div>
            </div>
        </div>
        <div class="cols">
            <div>
                <h2>Promo Code Manual</h2>
                <table>
                    <thead><tr><th>ID</th><th>QR</th><th>Code</th><th>Discount / Quantity</th><th>Trang thai</th><th>Lenh</th></tr></thead>
                    <tbody>
                    <?php if (!$promoRows): ?>
                        <tr><td colspan="6" class="muted">Chua co promo code.</td></tr>
                    <?php else: ?>
                        <?php foreach ($promoRows as $promo): ?>
                            <tr>
                                <td><?= h($promo['id'] ?? '') ?></td>
                                <td><img class="qr-mini" src="<?= h(dth_admin_qr_src($promo['code'] ?? '')) ?>" alt="QR <?= h($promo['code'] ?? '') ?>"></td>
                                <td><b><?= h($promo['code'] ?? '') ?></b></td>
                                <td>
                                    <form method="post" class="row-actions">
                                        <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
                                        <input type="hidden" name="promo_action" value="update">
                                        <input type="hidden" name="id" value="<?= h($promo['id'] ?? '') ?>">
                                        <input name="discount_amount" type="number" min="0" value="<?= h($promo['discount_amount'] ?? 0) ?>" style="max-width:130px">
                                        <input name="quantity_left" type="number" min="0" value="<?= h($promo['quantity_left'] ?? 0) ?>" style="max-width:110px">
                                        <button class="btn success" type="submit">Luu</button>
                                    </form>
                                </td>
                                <td>
                                    <?php if ((int)($promo['is_used'] ?? 0) === 1): ?>
                                        <span class="badge used">Da dung</span>
                                    <?php elseif ((int)($promo['quantity_left'] ?? 0) <= 0): ?>
                                        <span class="badge warn">Het luot</span>
                                    <?php else: ?>
                                        <span class="badge ok">Chua dung</span>
                                    <?php endif; ?>
                                    <div class="muted"><?= h($promo['created_at'] ?? '') ?></div>
                                </td>
                                <td>
                                    <form method="post" onsubmit="return confirm('Xoa promo <?= h($promo['code'] ?? '') ?>?')">
                                        <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
                                        <input type="hidden" name="promo_action" value="delete">
                                        <input type="hidden" name="id" value="<?= h($promo['id'] ?? '') ?>">
                                        <button class="btn danger" type="submit">Xoa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div><h2>Voucher</h2><table><thead><tr><th>QR</th><th>Code</th><th>Giam</th><th>Su dung</th><th>Trang thai</th><th>Han</th></tr></thead><tbody id="voucherBody"></tbody></table></div>
            <div><h2>QR Coupon</h2><table><thead><tr><th>ID</th><th>QR</th><th>Code</th><th>Loai</th><th>Gia tri</th><th>Mo ta</th><th>Trang thai</th></tr></thead><tbody id="couponBody"></tbody></table></div>
        </div>
    </section>

    <section id="page-workers">
        <div class="cols">
            <div class="card">
                <h2>Unban tho</h2>
                <label>Telegram user ID</label><input id="worker_unban_id" type="number">
                <button class="btn success" onclick="unbanWorker()">Mo khoa tho</button>
            </div>
            <div class="card">
                <h2>Unban IP/device</h2>
                <label>Identifier</label><input id="device_unban_id">
                <button class="btn success" onclick="unbanDevice()">Mo khoa thiet bi</button>
            </div>
        </div>
        <h2>Danh sach tho</h2>
        <table><thead><tr><th>ID</th><th>Ten</th><th>So ca</th><th>Tong tien</th><th>No phi</th><th>Block</th><th>Lenh</th></tr></thead><tbody id="workersBody"></tbody></table>
        <h2>Device/IP bi khoa</h2>
        <table><thead><tr><th>ID</th><th>Identifier</th><th>Loai</th><th>Ly do</th><th>Spam</th><th>Ngay</th><th>Lenh</th></tr></thead><tbody id="bansBody"></tbody></table>
    </section>

    <section id="page-invoices">
        <p class="muted">Chon mot don hang o bang duoi de in hoa don ban le.</p>
        <table><thead><tr><th>ID</th><th>Khach</th><th>Phone</th><th>San pham</th><th>Tien</th><th>Lenh</th></tr></thead><tbody id="invoiceBody"></tbody></table>
    </section>
</main>

<script>
const API = 'api_master.php';
const CSRF = <?= json_encode($csrf) ?>;

function fmt(n){ return new Intl.NumberFormat('vi-VN').format(Number(n || 0)) + ' VND'; }
function esc(s){ return String(s ?? '').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c])); }
function msg(text){ const el=document.getElementById('globalMsg'); el.textContent=text; el.style.display='block'; setTimeout(()=>el.style.display='none',3500); }
function api(action, data={}, method='GET'){
    const url = API + '?action=' + encodeURIComponent(action) + (method === 'GET' ? '&' + new URLSearchParams(data).toString() : '');
    const opt = {method, credentials:'same-origin', headers:{}};
    if (method !== 'GET') { opt.headers['Content-Type']='application/json'; opt.headers['X-CSRF-Token']=CSRF; opt.body=JSON.stringify(data); }
    return fetch(url,opt).then(async r => {
        const t = await r.text();
        try { return JSON.parse(t); } catch(e) { throw new Error(t || 'Invalid JSON'); }
    });
}
function setClock(){ document.getElementById('clock').textContent = new Date().toLocaleString('vi-VN',{hour12:false}); }
setInterval(setClock,1000); setClock();

document.querySelectorAll('nav button').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('nav button').forEach(b=>b.classList.remove('active'));
        document.querySelectorAll('section').forEach(s=>s.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('page-' + btn.dataset.page).classList.add('active');
        loadPage(btn.dataset.page);
    });
});

function loadPage(page){
    if (page === 'dash') loadStats();
    if (page === 'orders') loadOrders();
    if (page === 'jobs') loadJobs();
    if (page === 'products') loadProducts();
    if (page === 'codes') { loadVouchers(); loadCoupons(); }
    if (page === 'workers') { loadWorkers(); loadBans(); }
    if (page === 'invoices') loadInvoices();
}

function loadStats(){
    api('admin_stats').then(d=>{
        const s=d.stats||{};
        const items=[
            ['Don hang',s.total_orders],['Doanh thu',fmt(s.total_revenue)],['Don hom nay',s.today_orders],
            ['Thu hom nay',fmt(s.today_revenue)],['Ca goi tho',s.total_jobs],['Ca cho tho',s.pending_jobs],
            ['Ca xong',s.completed_jobs],['San pham',s.total_products],['Tho active',s.active_workers],
            ['No phi nen tang',fmt(s.unpaid_total)],['Device ban',s.banned_devices]
        ];
        document.getElementById('stats').innerHTML=items.map(i=>`<div class="card"><div class="stat-label">${esc(i[0])}</div><div class="stat-value">${esc(i[1]??0)}</div></div>`).join('');
    }).catch(e=>msg(e.message));
}

function loadOrders(){
    api('admin_orders').then(d=>{
        document.getElementById('ordersBody').innerHTML=(d.data||[]).map(o=>`<tr><td>#${o.id}</td><td>${esc(o.customer_name)}</td><td>${esc(o.customer_phone)}</td><td>${esc(o.product_name)}</td><td>${fmt(o.total_price)}</td><td>${esc(o.status)}</td><td>${esc(o.created_at)}</td><td><button class="btn" onclick="printInvoice(${o.id})">In</button></td></tr>`).join('');
    });
}
function loadInvoices(){ api('admin_orders').then(d=>{ document.getElementById('invoiceBody').innerHTML=(d.data||[]).map(o=>`<tr><td>#${o.id}</td><td>${esc(o.customer_name)}</td><td>${esc(o.customer_phone)}</td><td>${esc(o.product_name)}</td><td>${fmt(o.total_price)}</td><td><button class="btn primary" onclick="printInvoice(${o.id})">In hoa don</button></td></tr>`).join(''); }); }

function printInvoice(id){
    api('admin_invoice',{order_id:id}).then(d=>{
        const o=d.order||{};
        document.getElementById('printRoot').innerHTML=`<div class="print-box"><h1>DIEN TU HIEU</h1><p>Hoa don ban le #${esc(o.invoice_code||o.id)}</p><hr><p><b>Khach:</b> ${esc(o.customer_name||'Khach le')}</p><p><b>Phone:</b> ${esc(o.customer_phone||'')}</p><p><b>San pham:</b> ${esc(o.product_name||'')}</p><p><b>Tong tien:</b> ${fmt(o.total_price||0)}</p><p><b>Ngay:</b> ${esc(o.created_at||'')}</p><hr><p>Cam on quy khach.</p></div>`;
        window.print();
    });
}

function loadJobs(){
    api('admin_jobs').then(d=>{
        document.getElementById('jobsBody').innerHTML=(d.data||[]).map(j=>`<tr><td>#${j.id}</td><td>${esc(j.customer_name)}</td><td>${esc(j.customer_phone)}</td><td>${esc(j.service_type)}</td><td>${esc(j.address)}</td><td>${fmt(j.final_total)}</td><td>${esc(j.worker_id||'-')}</td><td>${esc(j.status)}</td><td>${esc(j.created_at)}</td></tr>`).join('');
    });
}
function sendTestJob(){ if(!confirm('Gui ca test len nhom tho?')) return; api('admin_test_worker_job',{},'POST').then(d=>{ msg(d.message||'Da gui test'); loadJobs(); loadStats(); }); }

let productCache = {};
const BLANK_IMG='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
function imageSrc(src){ src = String(src || '').trim(); return src === '' ? BLANK_IMG : src; }
function loadProducts(){
    api('admin_products').then(d=>{
        productCache = {};
        document.getElementById('productsBody').innerHTML=(d.data||[]).map(p=>{
            productCache[p.id] = p;
            return `<tr><td>${p.id}</td><td><img class="thumb" src="${esc(imageSrc(p.image_url))}" onerror="this.src=BLANK_IMG;this.onerror=null"></td><td>${esc(p.name)}</td><td>${esc(p.category)}</td><td>${fmt(p.price)}</td><td>${esc(p.stock_quantity)}</td><td><button class="btn" onclick="editProductById(${p.id})">Sua</button> <button class="btn danger" onclick="deleteProduct(${p.id})">Xoa</button></td></tr>`;
        }).join('');
    });
}
function editProductById(id){ const p = productCache[id] || {}; prod_id.value=p.id||''; prod_name.value=p.name||''; prod_price.value=p.price||0; prod_stock.value=p.stock_quantity||0; prod_category.value=p.category||''; prod_image.value=p.image_url||''; }
function saveProduct(){ api('admin_save_product',{id:prod_id.value,name:prod_name.value,price:prod_price.value,stock:prod_stock.value,category:prod_category.value,image_url:prod_image.value},'POST').then(d=>{ msg(d.message||'Da luu'); loadProducts(); }); }
function deleteProduct(id){ if(!confirm('Xoa san pham #' + id + '?')) return; api('admin_delete_product',{id},'POST').then(d=>{ msg(d.message||'Da xoa'); loadProducts(); }); }
function importProducts(){
    const rows=import_rows.value.split('\n').filter(Boolean).map(r=>{const c=r.split(',').map(x=>x.trim()); return {Danh_Muc:c[0]||'',Ten_San_Pham:c[1]||'',Gia_Nhap:c[2]||0,Gia_Ban:c[3]||0};});
    api('admin_import_excel',{data:rows},'POST').then(d=>{ msg(d.message||'Da import'); loadProducts(); });
}

function qrCodeUrl(code){ return 'https://api.qrserver.com/v1/create-qr-code/?size=96x96&margin=6&data=' + encodeURIComponent(String(code || '')); }
function statusBadge(kind, text){ return `<span class="badge ${kind}">${esc(text)}</span>`; }
function loadVouchers(){
    api('admin_vouchers').then(d=>{
        voucherBody.innerHTML=(d.data||[]).map(v=>{
            const used = Number(v.used_count || 0);
            const max = Number(v.max_uses || v.usage_limit || 0);
            const exhausted = max > 0 && used >= max;
            const badge = used > 0 ? statusBadge('used','Da dung') : (exhausted ? statusBadge('warn','Het luot') : statusBadge('ok','Chua dung'));
            return `<tr><td><img class="qr-mini" src="${qrCodeUrl(v.code)}" alt="QR ${esc(v.code)}"></td><td><b>${esc(v.code)}</b></td><td>${esc(v.discount_percent||0)}%</td><td>${esc(used)}/${esc(max)}</td><td>${badge}</td><td>${esc(v.expires_at||'')}</td></tr>`;
        }).join('');
    });
}
function loadCoupons(){
    api('admin_coupons').then(d=>{
        couponBody.innerHTML=(d.data||[]).map(c=>{
            const used = Number(c.is_used || 0) === 1;
            const badge = used ? statusBadge('used','Da dung') : statusBadge('ok','Chua dung');
            return `<tr><td>${esc(c.id)}</td><td><img class="qr-mini" src="${qrCodeUrl(c.code)}" alt="QR ${esc(c.code)}"></td><td><b>${esc(c.code)}</b></td><td>${esc(c.type)}</td><td>${esc(c.value || c.discount_amount || 0)}</td><td>${esc(c.description)}</td><td>${badge}</td></tr>`;
        }).join('');
    });
}
function generateVoucher(){ api('generate_voucher',{discount_percent:voucher_percent.value,count:voucher_count.value,max_uses:100},'POST').then(d=>{ voucherResult.textContent='Created: '+(d.codes||[]).join(', '); loadVouchers(); }); }
function generateQR(){ api('generate_qr',{value:qr_value.value,description:qr_desc.value,count:qr_count.value,type:'discount'},'POST').then(d=>{ qrResult.textContent='Created: '+(d.codes||[]).join(', '); loadCoupons(); }); }

function loadWorkers(){ api('admin_workers').then(d=>{ workersBody.innerHTML=(d.data||[]).map(w=>`<tr><td>${esc(w.worker_id)}</td><td>${esc(w.telegram_name)}</td><td>${esc(w.job_count)}</td><td>${fmt(w.total_earned)}</td><td>${fmt(w.unpaid_fee)}</td><td>${w.is_receive_blocked||w.payment_blocked?'Blocked':'OK'}</td><td><button class="btn success" onclick="unbanWorkerId(${w.worker_id})">Unban</button> <button class="btn" onclick="markPaid(${w.worker_id})">Da TT</button></td></tr>`).join(''); }); }
function loadBans(){ api('admin_banned_devices').then(d=>{ bansBody.innerHTML=(d.data||[]).map(b=>`<tr><td>${b.id}</td><td><code>${esc(b.identifier)}</code></td><td>${esc(b.ban_type)}</td><td>${esc(b.reason)}</td><td>${esc(b.spam_count)}</td><td>${esc(b.created_at)}</td><td><button class="btn success" onclick='unbanDeviceValue(${JSON.stringify(b.identifier)})'>Unban</button></td></tr>`).join(''); }); }
function unbanWorker(){ unbanWorkerId(worker_unban_id.value); }
function unbanWorkerId(id){ if(!id) return msg('Nhap Telegram user ID'); api('admin_unban_worker',{worker_id:id},'POST').then(d=>{ msg(d.message||'Da mo khoa'); loadWorkers(); }); }
function unbanDevice(){ unbanDeviceValue(device_unban_id.value); }
function unbanDeviceValue(id){ if(!id) return msg('Nhap identifier'); api('admin_unban_device',{identifier:id},'POST').then(d=>{ msg(d.message||'Da mo khoa'); loadBans(); }); }
function markPaid(id){ api('admin_mark_worker_paid',{worker_id:id},'POST').then(d=>{ msg(d.message||'Da ghi nhan'); loadWorkers(); loadStats(); }); }

if (<?= json_encode($promoMessage !== '') ?>) {
    document.querySelector('nav button[data-page="codes"]').click();
} else {
    loadStats();
}
</script>
</body>
</html>
