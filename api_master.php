<?php
declare(strict_types=1);

/*
 * Dien Tu Hieu - THE BRAIN.
 * Pure PHP + PDO + Telegram webhook router.
 *
 * Public actions:
 *   get_products, create_order, create_job, check_voucher, save_wheel_prize,
 *   gemini_chat, telegram_webhook
 *
 * Admin actions:
 *   admin_*, generate_qr, generate_voucher
 */

date_default_timezone_set('Asia/Ho_Chi_Minh');

function app_security_headers()
{
    if (headers_sent()) {
        return;
    }
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}

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

function app_env(string $key, $default = ''): string
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    if ($value === false || $value === null || $value === '') {
        return (string)$default;
    }
    return (string)$value;
}

function app_bool_env(string $key, bool $default = false): bool
{
    $value = strtolower(app_env($key, $default ? 'true' : 'false'));
    return in_array($value, ['1', 'true', 'yes', 'on'], true);
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

function app_admin_is_authenticated(): bool
{
    app_ensure_session();
    return !empty($_SESSION['admin_logged_in']);
}

function app_require_admin_json()
{
    if (app_admin_is_authenticated()) {
        return;
    }
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'Admin login required.'], JSON_UNESCAPED_UNICODE);
    exit;
}

function app_request_data(): array
{
    $data = $_POST;
    $raw = file_get_contents('php://input');
    if (is_string($raw) && trim($raw) !== '') {
        $json = json_decode($raw, true);
        if (is_array($json)) {
            $data = array_merge($data, $json);
        }
    }
    return $data;
}

app_load_env();
app_security_headers();

function dth_starts_with(string $haystack, string $needle): bool
{
    return $needle === '' || strncmp($haystack, $needle, strlen($needle)) === 0;
}

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

function api_exception_out(Throwable $e)
{
    error_log('[api_master] ' . $e->getMessage() . "\n" . $e->getTraceAsString());
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }
    $message = app_bool_env('APP_DEBUG', false) ? $e->getMessage() : 'Server error. Check PHP error_log.';
    echo json_encode(['status' => 'error', 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

set_exception_handler('api_exception_out');

function json_out(array $data, int $status = 200)
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function request_data(): array
{
    return app_request_data();
}

function clean_string($value, int $max = 500): string
{
    $value = trim((string)$value);
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';
    return function_exists('mb_substr') ? mb_substr($value, 0, $max, 'UTF-8') : substr($value, 0, $max);
}

function digits_only($value): string
{
    return preg_replace('/\D+/', '', (string)$value) ?? '';
}

function money_int($value): int
{
    if (is_numeric($value)) {
        return max(0, (int)round((float)$value));
    }
    return max(0, (int)(preg_replace('/[^\d]/', '', (string)$value) ?: 0));
}

function fmt_money($amount): string
{
    return number_format((float)$amount, 0, ',', '.') . ' VND';
}

function client_ip(): string
{
    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $key) {
        $value = (string)($_SERVER[$key] ?? '');
        if ($value === '') {
            continue;
        }
        $first = trim(explode(',', $value)[0]);
        if (filter_var($first, FILTER_VALIDATE_IP)) {
            return $first;
        }
    }
    return '0.0.0.0';
}

function esc_html($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function mask_phone(string $phone): string
{
    $digits = digits_only($phone);
    $len = strlen($digits);
    if ($len <= 6) {
        return str_repeat('*', max(0, $len));
    }
    return substr($digits, 0, 3) . str_repeat('*', max(3, $len - 6)) . substr($digits, -3);
}

function mask_phone_like_text(string $text): string
{
    return preg_replace_callback('/\b(?:\+?84|0)?\d{8,11}\b/u', static function (array $m): string {
        return mask_phone($m[0]);
    }, $text) ?? $text;
}

function generate_code(string $prefix, int $bytes = 4): string
{
    return strtoupper($prefix . '-' . bin2hex(random_bytes($bytes)));
}

final class DB
{
    private static $pdo = null;

    public static function conn(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $name = app_env('DB_NAME', 'kwkrbcce_Goixelapvo');
        $user = app_env('DB_USER', 'kwkrbcce_baocao');
        $pass = app_env('DB_PASS', 'SayTHC369@');

        $fallbackUser = app_env('DB_USER_BAOCAO', 'kwkrbcce_baocao');
        $fallbackPass = app_env('DB_PASS_BAOCAO', 'SayTHC369@');
        if (($user === '' || ($name !== '' && $user === $name)) && $fallbackUser !== '') {
            $user = $fallbackUser;
            if ($fallbackPass !== '') {
                $pass = $fallbackPass;
            }
        }

        if ($name === '') { $name = 'kwkrbcce_Goixelapvo'; }
        if ($user === '') { $user = 'kwkrbcce_baocao'; }
        if ($pass === '') { $pass = 'SayTHC369@'; }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            app_env('DB_HOST', 'localhost'),
            $name,
            app_env('DB_CHARSET', 'utf8mb4')
        );

        self::$pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        self::$pdo->exec("SET time_zone = '+07:00'");
        return self::$pdo;
    }
}

function pdo(): PDO
{
    $pdo = DB::conn();
    ensure_core_schema($pdo);
    return $pdo;
}

function db_ident(string $identifier): string
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', $identifier)) {
        throw new InvalidArgumentException('Unsafe database identifier.');
    }
    return "`{$identifier}`";
}

function table_exists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?');
    $stmt->execute([$table]);
    return (int)$stmt->fetchColumn() > 0;
}

function column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function column_type(PDO $pdo, string $table, string $column): string
{
    $stmt = $pdo->prepare('SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? LIMIT 1');
    $stmt->execute([$table, $column]);
    return (string)($stmt->fetchColumn() ?: '');
}

function column_allows_value(PDO $pdo, string $table, string $column, string $value): bool
{
    if (!column_exists($pdo, $table, $column)) {
        return false;
    }
    $type = strtolower(column_type($pdo, $table, $column));
    if (!dth_starts_with($type, 'enum(')) {
        return true;
    }
    preg_match_all("/'((?:[^'\\\\]|\\\\.)*)'/", $type, $matches);
    $values = array_map('stripcslashes', $matches[1] ?? []);
    return in_array($value, $values, true);
}

function first_existing_column(PDO $pdo, string $table, array $columns)
{
    foreach ($columns as $column) {
        if (column_exists($pdo, $table, $column)) {
            return $column;
        }
    }
    return null;
}

function add_column_if_missing(PDO $pdo, string $table, string $column, string $definition)
{
    if (!table_exists($pdo, $table) || column_exists($pdo, $table, $column)) {
        return;
    }
    try {
        $pdo->exec('ALTER TABLE ' . db_ident($table) . ' ADD COLUMN ' . db_ident($column) . ' ' . $definition);
    } catch (Throwable $e) {
        error_log("[schema] add column skipped {$table}.{$column}: " . $e->getMessage());
    }
}

function index_exists(PDO $pdo, string $table, string $index): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?');
    $stmt->execute([$table, $index]);
    return (int)$stmt->fetchColumn() > 0;
}

function add_index_if_missing(PDO $pdo, string $table, string $index, string $definition)
{
    if (!table_exists($pdo, $table) || index_exists($pdo, $table, $index)) {
        return;
    }
    try {
        $pdo->exec('ALTER TABLE ' . db_ident($table) . ' ADD INDEX ' . db_ident($index) . ' ' . $definition);
    } catch (Throwable $e) {
        error_log("[schema] add index skipped {$table}.{$index}: " . $e->getMessage());
    }
}

function ensure_core_schema(PDO $pdo)
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        role VARCHAR(30) NOT NULL DEFAULT 'buyer',
        fullname VARCHAR(150) NOT NULL,
        phone VARCHAR(30) NOT NULL,
        password_hash VARCHAR(255) NULL,
        telegram_chat_id VARCHAR(60) NULL,
        telegram_username VARCHAR(150) NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Do not create a fake products table. The real project uses marketplace_products
    // and may also have a legacy products table with Vietnamese column names.

    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_code VARCHAR(50) NOT NULL UNIQUE,
        customer_name VARCHAR(150) NULL,
        customer_phone VARCHAR(30) NULL,
        product_id INT NOT NULL DEFAULT 0,
        product_name VARCHAR(255) NULL,
        total_price INT NOT NULL DEFAULT 0,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
        payment_method VARCHAR(40) NULL DEFAULT 'cod',
        coupon_code VARCHAR(80) NULL,
        note TEXT NULL,
        confirmed_by VARCHAR(150) NULL,
        confirmed_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS job_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(150) NULL,
        customer_phone VARCHAR(30) NULL,
        service_type VARCHAR(150) NULL,
        address TEXT NULL,
        description TEXT NULL,
        quantity INT NOT NULL DEFAULT 1,
        customer_total INT NOT NULL DEFAULT 0,
        discount INT NOT NULL DEFAULT 0,
        final_total INT NOT NULL DEFAULT 0,
        worker_id BIGINT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
        spam_count INT NOT NULL DEFAULT 0,
        cancel_reason TEXT NULL,
        assigned_at DATETIME NULL,
        completed_at DATETIME NULL,
        cancelled_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS job_pricing (
        id INT AUTO_INCREMENT PRIMARY KEY,
        job_id INT NOT NULL,
        tech_target_base INT NOT NULL DEFAULT 0,
        vat_amount INT NOT NULL DEFAULT 0,
        profit_amount INT NOT NULL DEFAULT 0,
        gross_customer_price INT NOT NULL DEFAULT 0,
        discount_amount INT NOT NULL DEFAULT 0,
        final_customer_price INT NOT NULL DEFAULT 0,
        platform_fee INT NOT NULL DEFAULT 0,
        tech_net_income INT NOT NULL DEFAULT 0,
        payment_status VARCHAR(30) NOT NULL DEFAULT 'unpaid',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_job_pricing_job (job_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS worker_profiles (
        telegram_user_id BIGINT PRIMARY KEY,
        telegram_name VARCHAR(150) NULL,
        telegram_username VARCHAR(150) NULL,
        cancel_count INT NOT NULL DEFAULT 0,
        abuse_count INT NOT NULL DEFAULT 0,
        jobs_claimed INT NOT NULL DEFAULT 0,
        jobs_completed INT NOT NULL DEFAULT 0,
        is_receive_blocked TINYINT(1) NOT NULL DEFAULT 0,
        payment_blocked TINYINT(1) NOT NULL DEFAULT 0,
        blocked_until DATETIME NULL,
        block_reason VARCHAR(255) NULL,
        last_fee_notice_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS job_claims (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        job_id INT NOT NULL,
        telegram_user_id BIGINT NOT NULL,
        telegram_name VARCHAR(150) NULL,
        outcome VARCHAR(40) NOT NULL DEFAULT 'attempt',
        note TEXT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_job_claims_job (job_id),
        INDEX idx_job_claims_worker (telegram_user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS telegram_message_map (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        bot_role VARCHAR(30) NOT NULL,
        chat_id VARCHAR(80) NOT NULL,
        message_id BIGINT NOT NULL,
        entity_type VARCHAR(40) NOT NULL,
        entity_id INT NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_tg_msg (bot_role, chat_id, message_id),
        INDEX idx_tg_entity (entity_type, entity_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS job_client_identifiers (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        job_id INT NOT NULL,
        identifier VARCHAR(255) NOT NULL,
        identifier_type VARCHAR(30) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_job_identifier (job_id, identifier, identifier_type),
        INDEX idx_client_identifier (identifier, identifier_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS spam_reports (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        job_id INT NOT NULL,
        telegram_user_id BIGINT NOT NULL,
        telegram_name VARCHAR(150) NULL,
        note TEXT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_spam_report (job_id, telegram_user_id),
        INDEX idx_spam_job (job_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS client_abuse (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        identifier VARCHAR(255) NOT NULL,
        identifier_type VARCHAR(30) NOT NULL,
        request_count INT NOT NULL DEFAULT 0,
        fake_count INT NOT NULL DEFAULT 0,
        last_job_id INT NULL,
        banned_at DATETIME NULL,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_client_abuse (identifier, identifier_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS banned_devices (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        identifier VARCHAR(255) NOT NULL,
        ban_type VARCHAR(30) NOT NULL DEFAULT 'device',
        reason TEXT NULL,
        spam_job_id INT NULL,
        spam_count INT NOT NULL DEFAULT 0,
        created_by VARCHAR(100) NULL DEFAULT 'system',
        expires_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_ban (identifier, ban_type),
        INDEX idx_ban_identifier (identifier),
        INDEX idx_ban_expires (expires_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS qr_coupons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(80) NOT NULL UNIQUE,
        type VARCHAR(30) NOT NULL DEFAULT 'discount',
        value INT NOT NULL DEFAULT 0,
        description TEXT NULL,
        is_used TINYINT(1) NOT NULL DEFAULT 0,
        used_by VARCHAR(80) NULL,
        order_ref VARCHAR(80) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS vouchers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(80) NOT NULL UNIQUE,
        discount_percent INT NOT NULL DEFAULT 0,
        discount_amount INT NOT NULL DEFAULT 0,
        max_uses INT NOT NULL DEFAULT 100,
        used_count INT NOT NULL DEFAULT 0,
        expires_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS order_notifications (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        telegram_message_id BIGINT NULL,
        boss_chat_id VARCHAR(80) NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
        confirmed_by VARCHAR(150) NULL,
        confirmed_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_order_notifications_order (order_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS finances (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        type VARCHAR(40) NOT NULL,
        amount INT NOT NULL DEFAULT 0,
        source_type VARCHAR(40) NULL,
        source_id INT NULL,
        note TEXT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_finances_source (source_type, source_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS invoices (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        invoice_code VARCHAR(80) NOT NULL UNIQUE,
        order_id INT NULL,
        customer_name VARCHAR(150) NULL,
        customer_phone VARCHAR(30) NULL,
        product_name VARCHAR(255) NULL,
        total_price INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_invoices_order (order_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    foreach ([
        'products' => [
            'price' => 'INT NOT NULL DEFAULT 0',
            'stock_quantity' => 'INT NOT NULL DEFAULT 0',
            'image_url' => 'VARCHAR(700) NULL',
            'category' => 'VARCHAR(120) NULL',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'orders' => [
            'order_code' => 'VARCHAR(50) NULL',
            'customer_name' => 'VARCHAR(150) NULL',
            'customer_phone' => 'VARCHAR(30) NULL',
            'product_id' => 'INT NOT NULL DEFAULT 0',
            'product_name' => 'VARCHAR(255) NULL',
            'total_price' => 'INT NOT NULL DEFAULT 0',
            'payment_method' => "VARCHAR(40) NULL DEFAULT 'cod'",
            'coupon_code' => 'VARCHAR(80) NULL',
            'note' => 'TEXT NULL',
            'confirmed_by' => 'VARCHAR(150) NULL',
            'confirmed_at' => 'DATETIME NULL',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'job_posts' => [
            'customer_name' => 'VARCHAR(150) NULL',
            'customer_phone' => 'VARCHAR(30) NULL',
            'service_type' => 'VARCHAR(150) NULL',
            'address' => 'TEXT NULL',
            'quantity' => 'INT NOT NULL DEFAULT 1',
            'customer_total' => 'INT NOT NULL DEFAULT 0',
            'discount' => 'INT NOT NULL DEFAULT 0',
            'final_total' => 'INT NOT NULL DEFAULT 0',
            'worker_id' => 'BIGINT NULL',
            'spam_count' => 'INT NOT NULL DEFAULT 0',
            'cancel_reason' => 'TEXT NULL',
            'assigned_at' => 'DATETIME NULL',
            'completed_at' => 'DATETIME NULL',
            'cancelled_at' => 'DATETIME NULL',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'vouchers' => [
            'discount_percent' => 'INT NOT NULL DEFAULT 0',
            'discount_amount' => 'INT NOT NULL DEFAULT 0',
            'max_uses' => 'INT NOT NULL DEFAULT 100',
            'used_count' => 'INT NOT NULL DEFAULT 0',
            'expires_at' => 'DATETIME NULL',
        ],
        'qr_coupons' => [
            'is_used' => 'TINYINT(1) NOT NULL DEFAULT 0',
            'used_by' => 'VARCHAR(80) NULL',
            'order_ref' => 'VARCHAR(80) NULL',
        ],
        'worker_profiles' => [
            'telegram_username' => 'VARCHAR(150) NULL',
            'abuse_count' => 'INT NOT NULL DEFAULT 0',
            'jobs_claimed' => 'INT NOT NULL DEFAULT 0',
            'jobs_completed' => 'INT NOT NULL DEFAULT 0',
            'payment_blocked' => 'TINYINT(1) NOT NULL DEFAULT 0',
            'last_fee_notice_at' => 'DATETIME NULL',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'banned_devices' => [
            'ban_type' => "VARCHAR(30) NOT NULL DEFAULT 'device'",
            'reason' => 'TEXT NULL',
            'spam_job_id' => 'INT NULL',
            'spam_count' => 'INT NOT NULL DEFAULT 0',
            'created_by' => "VARCHAR(100) NULL DEFAULT 'system'",
            'expires_at' => 'DATETIME NULL',
        ],
        'order_notifications' => [
            'telegram_message_id' => 'BIGINT NULL',
            'boss_chat_id' => 'VARCHAR(80) NULL',
            'status' => "VARCHAR(30) NOT NULL DEFAULT 'pending'",
            'confirmed_by' => 'VARCHAR(150) NULL',
            'confirmed_at' => 'DATETIME NULL',
        ],
        'finances' => [
            'type' => 'VARCHAR(40) NOT NULL',
            'amount' => 'INT NOT NULL DEFAULT 0',
            'source_type' => 'VARCHAR(40) NULL',
            'source_id' => 'INT NULL',
            'note' => 'TEXT NULL',
        ],
        'invoices' => [
            'invoice_code' => 'VARCHAR(80) NULL',
            'order_id' => 'INT NULL',
            'customer_name' => 'VARCHAR(150) NULL',
            'customer_phone' => 'VARCHAR(30) NULL',
            'product_name' => 'VARCHAR(255) NULL',
            'total_price' => 'INT NOT NULL DEFAULT 0',
        ],
    ] as $table => $columns) {
        foreach ($columns as $column => $definition) {
            add_column_if_missing($pdo, $table, $column, $definition);
        }
    }

    add_index_if_missing($pdo, 'products', 'idx_products_category', '(category)');
    add_index_if_missing($pdo, 'products', 'idx_products_price', '(price)');
    add_index_if_missing($pdo, 'orders', 'idx_orders_customer_phone', '(customer_phone)');
    add_index_if_missing($pdo, 'orders', 'idx_orders_status', '(status)');
    add_index_if_missing($pdo, 'job_posts', 'idx_job_posts_customer_phone', '(customer_phone)');
    add_index_if_missing($pdo, 'job_posts', 'idx_job_posts_status', '(status)');
    add_index_if_missing($pdo, 'job_posts', 'idx_job_posts_worker', '(worker_id)');
    add_index_if_missing($pdo, 'qr_coupons', 'idx_qr_coupons_code_lookup', '(code)');
    add_index_if_missing($pdo, 'vouchers', 'idx_vouchers_code_lookup', '(code)');
    add_index_if_missing($pdo, 'worker_profiles', 'idx_worker_profiles_blocked', '(is_receive_blocked, payment_blocked)');
}

function insert_compat(PDO $pdo, string $table, array $values, array $expressions = []): int
{
    $columns = [];
    $placeholders = [];
    $params = [];
    foreach ($values as $column => $value) {
        if (!column_exists($pdo, $table, $column)) {
            continue;
        }
        $columns[] = db_ident((string)$column);
        $placeholders[] = '?';
        $params[] = $value;
    }
    foreach ($expressions as $column => $expression) {
        if (!column_exists($pdo, $table, $column)) {
            continue;
        }
        $columns[] = db_ident((string)$column);
        $placeholders[] = (string)$expression;
    }
    if ($columns === []) {
        throw new RuntimeException("No compatible columns for {$table} insert.");
    }
    $sql = 'INSERT INTO ' . db_ident($table) . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$pdo->lastInsertId();
}

function update_compat(PDO $pdo, string $table, array $values, string $where, array $whereParams = [], array $expressions = []): int
{
    $sets = [];
    $params = [];
    foreach ($values as $column => $value) {
        if (!column_exists($pdo, $table, $column)) {
            continue;
        }
        $sets[] = db_ident((string)$column) . ' = ?';
        $params[] = $value;
    }
    foreach ($expressions as $column => $expression) {
        if (!column_exists($pdo, $table, $column)) {
            continue;
        }
        $sets[] = db_ident((string)$column) . ' = ' . (string)$expression;
    }
    if ($sets === []) {
        return 0;
    }
    $stmt = $pdo->prepare('UPDATE ' . db_ident($table) . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where);
    $stmt->execute(array_merge($params, $whereParams));
    return $stmt->rowCount();
}

function order_status(PDO $pdo, string $state): string
{
    switch ($state) {
        case 'pending':
            $candidates = ['pending', 'new', 'processing'];
            break;
        case 'confirmed':
            $candidates = ['confirmed', 'shipped', 'processing', 'completed'];
            break;
        case 'rejected':
        case 'cancelled':
            $candidates = ['cancelled', 'refunded', 'pending'];
            break;
        default:
            $candidates = [$state];
    }
    foreach ($candidates as $candidate) {
        if (column_allows_value($pdo, 'orders', 'status', $candidate)) {
            return $candidate;
        }
    }
    return $state;
}

function job_status(PDO $pdo, string $state): string
{
    switch ($state) {
        case 'pending':
            $candidates = ['pending', 'open'];
            break;
        case 'assigned':
            $candidates = ['assigned', 'open', 'processing'];
            break;
        case 'completed':
            $candidates = ['completed', 'filled', 'closed'];
            break;
        case 'cancelled':
            $candidates = ['cancelled', 'closed'];
            break;
        case 'spam':
            $candidates = ['spam', 'cancelled', 'closed'];
            break;
        default:
            $candidates = [$state];
    }
    foreach ($candidates as $candidate) {
        if (column_allows_value($pdo, 'job_posts', 'status', $candidate)) {
            return $candidate;
        }
    }
    return $state;
}

function get_system_user_id(PDO $pdo)
{
    if (!table_exists($pdo, 'users') || !column_exists($pdo, 'users', 'id')) {
        return null;
    }
    $stmt = $pdo->query("SELECT id FROM users ORDER BY id ASC LIMIT 1");
    $id = $stmt ? (int)$stmt->fetchColumn() : 0;
    if ($id > 0) {
        return $id;
    }

    $values = [
        'role' => 'admin',
        'fullname' => 'Dien Tu Hieu',
        'phone' => '0979553289',
        'is_active' => 1,
    ];
    return insert_compat($pdo, 'users', $values, ['created_at' => 'NOW()']);
}

function random_booking_discount(): array
{
    $roll = random_int(1, 100);
    if ($roll <= 1) {
        return ['roll' => $roll, 'amount' => 10000, 'label' => '-10k'];
    }
    if ($roll <= 20) {
        return ['roll' => $roll, 'amount' => 5000, 'label' => '-5k'];
    }
    if ($roll <= 60) {
        return ['roll' => $roll, 'amount' => 3000, 'label' => '-3k'];
    }
    return ['roll' => $roll, 'amount' => 2000, 'label' => '-2k'];
}

function calculate_job_pricing(int $techTargetBase, int $estimatedCustomerPrice, int $quantity = 1): array
{
    $quantity = max(1, $quantity);
    if ($techTargetBase > 0) {
        $techBaseTotal = $techTargetBase * $quantity;
        $vatAmount = (int)round($techBaseTotal * 0.10);
        $profitAmount = (int)round($techBaseTotal * 0.05);
        $grossCustomerPrice = $techBaseTotal + $vatAmount + $profitAmount;
    } else {
        $grossCustomerPrice = max(0, $estimatedCustomerPrice);
        $techBaseTotal = (int)round($grossCustomerPrice / 1.15);
        $vatAmount = (int)round($techBaseTotal * 0.10);
        $profitAmount = max(0, $grossCustomerPrice - $techBaseTotal - $vatAmount);
    }

    $discount = random_booking_discount();
    $finalCustomerPrice = max(0, $grossCustomerPrice - (int)$discount['amount']);
    $platformFee = max(0, $finalCustomerPrice - $techBaseTotal);

    return [
        'tech_target_base' => $techBaseTotal,
        'vat_amount' => $vatAmount,
        'profit_amount' => $profitAmount,
        'gross_customer_price' => $grossCustomerPrice,
        'discount_amount' => (int)$discount['amount'],
        'discount_roll' => (int)$discount['roll'],
        'discount_label' => (string)$discount['label'],
        'final_customer_price' => $finalCustomerPrice,
        'platform_fee' => $platformFee,
        'tech_net_income' => $techBaseTotal,
    ];
}

function telegram_token(string $role): string
{
    return $role === 'sales' ? app_env('BOT_REPORT_TOKEN', '') : app_env('BOT_WORKER_TOKEN', '');
}

function telegram_chat(string $role): string
{
    if ($role === 'sales') {
        return app_env('BOSS_CHAT_ID', app_env('BOSS_USERNAME', ''));
    }
    return app_env('WORKER_CHAT_ID', '');
}

function tg_api(string $token, string $method, array $payload): array
{
    if ($token === '') {
        return ['ok' => false, 'description' => 'Missing Telegram token.'];
    }
    if (!function_exists('curl_init')) {
        return ['ok' => false, 'description' => 'PHP cURL extension is not enabled.'];
    }
    $ch = curl_init("https://api.telegram.org/bot{$token}/{$method}");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 15,
    ]);
    $raw = curl_exec($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    $decoded = json_decode((string)$raw, true);
    if (!is_array($decoded)) {
        $decoded = ['ok' => false, 'description' => $err !== '' ? $err : 'Invalid Telegram response.'];
    }
    if ($http !== 200 || empty($decoded['ok'])) {
        error_log("[telegram] {$method} HTTP={$http}: " . substr((string)$raw, 0, 500));
    }
    return $decoded;
}

function tg_send(string $role, string $chatId, string $text, $replyMarkup = null): array
{
    $payload = [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true,
    ];
    if ($replyMarkup !== null) {
        $payload['reply_markup'] = $replyMarkup;
    }
    return tg_api(telegram_token($role), 'sendMessage', $payload);
}

function tg_answer_callback(string $role, string $callbackId, string $text, bool $alert = false)
{
    tg_api(telegram_token($role), 'answerCallbackQuery', [
        'callback_query_id' => $callbackId,
        'text' => $text,
        'show_alert' => $alert,
    ]);
}

function save_tg_map(PDO $pdo, string $role, string $chatId, int $messageId, string $entityType, int $entityId)
{
    if ($chatId === '' || $messageId <= 0 || $entityId <= 0) {
        return;
    }
    $stmt = $pdo->prepare("INSERT INTO telegram_message_map (bot_role, chat_id, message_id, entity_type, entity_id)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE entity_type = VALUES(entity_type), entity_id = VALUES(entity_id)");
    $stmt->execute([$role, $chatId, $messageId, $entityType, $entityId]);
}

function find_tg_entity(PDO $pdo, string $role, string $chatId, int $messageId, string $entityType)
{
    $stmt = $pdo->prepare('SELECT entity_id FROM telegram_message_map WHERE bot_role = ? AND chat_id = ? AND message_id = ? AND entity_type = ? LIMIT 1');
    $stmt->execute([$role, $chatId, $messageId, $entityType]);
    $id = (int)($stmt->fetchColumn() ?: 0);
    return $id > 0 ? $id : null;
}

function worker_name(array $from): string
{
    $name = trim((string)($from['first_name'] ?? '') . ' ' . (string)($from['last_name'] ?? ''));
    if ($name === '') {
        $name = (string)($from['username'] ?? 'worker');
    }
    return clean_string($name, 150);
}

function upsert_worker(PDO $pdo, int $telegramUserId, string $name, string $username = '')
{
    if ($telegramUserId <= 0) {
        return;
    }
    $stmt = $pdo->prepare("INSERT INTO worker_profiles (telegram_user_id, telegram_name, telegram_username, created_at, updated_at)
        VALUES (?, ?, ?, NOW(), NOW())
        ON DUPLICATE KEY UPDATE telegram_name = VALUES(telegram_name), telegram_username = VALUES(telegram_username), updated_at = NOW()");
    $stmt->execute([$telegramUserId, $name, $username]);
}

function get_worker_profile(PDO $pdo, int $telegramUserId): array
{
    $stmt = $pdo->prepare('SELECT * FROM worker_profiles WHERE telegram_user_id = ? LIMIT 1');
    $stmt->execute([$telegramUserId]);
    return $stmt->fetch() ?: [];
}

function tech_cancel_limit(): int
{
    $limit = (int)app_env('TECH_CANCEL_LIMIT', '3');
    return max(3, min(10, $limit));
}

function worker_is_blocked(array $profile): bool
{
    if ((int)($profile['is_receive_blocked'] ?? 0) === 1 || (int)($profile['payment_blocked'] ?? 0) === 1) {
        $until = (string)($profile['blocked_until'] ?? '');
        return $until === '' || strtotime($until) === false || strtotime($until) > time();
    }
    return false;
}

function increment_worker_penalty(PDO $pdo, int $workerId, string $reason): array
{
    $limit = tech_cancel_limit();
    $stmt = $pdo->prepare('UPDATE worker_profiles SET cancel_count = cancel_count + 1, abuse_count = abuse_count + 1, updated_at = NOW() WHERE telegram_user_id = ?');
    $stmt->execute([$workerId]);

    $profile = get_worker_profile($pdo, $workerId);
    $count = (int)($profile['cancel_count'] ?? 0);
    if ($count >= $limit) {
        $stmt = $pdo->prepare("UPDATE worker_profiles SET is_receive_blocked = 1, block_reason = ?, blocked_until = NULL, updated_at = NOW() WHERE telegram_user_id = ?");
        $stmt->execute([$reason . " ({$count}/{$limit})", $workerId]);
        $profile = get_worker_profile($pdo, $workerId);
    }
    return $profile;
}

function active_identifiers(array $input, string $phone = ''): array
{
    $items = [];
    $device = clean_string($input['device_fingerprint'] ?? $input['fingerprint'] ?? $input['device_id'] ?? '', 255);
    if ($device !== '') {
        $items[] = ['identifier' => $device, 'type' => 'device'];
    }
    $ip = client_ip();
    if ($ip !== '0.0.0.0') {
        $items[] = ['identifier' => $ip, 'type' => 'ip'];
    }
    $digits = digits_only($phone);
    if ($digits !== '') {
        $items[] = ['identifier' => $digits, 'type' => 'phone'];
    }
    return $items;
}

function ensure_not_banned(PDO $pdo, array $identifiers)
{
    if ($identifiers === []) {
        return;
    }
    $stmt = $pdo->prepare("SELECT identifier, ban_type, reason FROM banned_devices
        WHERE identifier = ? AND ban_type = ? AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1");
    foreach ($identifiers as $item) {
        $stmt->execute([$item['identifier'], $item['type']]);
        $ban = $stmt->fetch();
        if ($ban) {
            json_out([
                'status' => 'error',
                'message' => 'Thiet bi/IP/SDT dang bi khoa do spam. Vui long lien he Dien Tu Hieu.',
                'ban' => $ban,
            ], 403);
        }
    }
}

function record_client_request(PDO $pdo, int $jobId, array $identifiers)
{
    foreach ($identifiers as $item) {
        $pdo->prepare("INSERT IGNORE INTO job_client_identifiers (job_id, identifier, identifier_type) VALUES (?, ?, ?)")
            ->execute([$jobId, $item['identifier'], $item['type']]);
        $pdo->prepare("INSERT INTO client_abuse (identifier, identifier_type, request_count, last_job_id)
            VALUES (?, ?, 1, ?)
            ON DUPLICATE KEY UPDATE request_count = request_count + 1, last_job_id = VALUES(last_job_id), updated_at = NOW()")
            ->execute([$item['identifier'], $item['type'], $jobId]);
    }
}

function get_job_row(PDO $pdo, int $jobId, bool $forUpdate = false)
{
    $sql = 'SELECT * FROM job_posts WHERE id = ?' . ($forUpdate ? ' FOR UPDATE' : '');
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$jobId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function get_job_pricing(PDO $pdo, int $jobId): array
{
    $stmt = $pdo->prepare('SELECT * FROM job_pricing WHERE job_id = ? ORDER BY id DESC LIMIT 1');
    $stmt->execute([$jobId]);
    return $stmt->fetch() ?: [];
}

function job_display_status(array $job): string
{
    $raw = (string)($job['status'] ?? '');
    if (in_array($raw, ['completed', 'filled', 'closed'], true) && !empty($job['completed_at'])) {
        return 'completed';
    }
    if (in_array($raw, ['cancelled', 'spam'], true)) {
        return $raw;
    }
    if (!empty($job['worker_id'])) {
        return 'assigned';
    }
    return 'pending';
}

function insert_repair_job(PDO $pdo, array $job): int
{
    $serviceType = clean_string($job['service_type'] ?? 'Dich vu dien lanh', 150);
    $customerName = clean_string($job['customer_name'] ?? 'Khach', 150);
    $customerPhone = digits_only($job['customer_phone'] ?? '');
    $address = clean_string($job['address'] ?? '', 1000);
    $description = clean_string($job['description'] ?? '', 3000);
    $finalTotal = (int)($job['final_total'] ?? 0);
    $customerTotal = (int)($job['customer_total'] ?? $finalTotal);

    $fullDescription = $description;
    if (!column_exists($pdo, 'job_posts', 'customer_phone') || !column_exists($pdo, 'job_posts', 'address')) {
        $fullDescription = trim($description . "\nCustomer: {$customerName}\nPhone: {$customerPhone}\nAddress: {$address}");
    }

    $values = [
        'customer_name' => $customerName,
        'customer_phone' => $customerPhone,
        'service_type' => $serviceType,
        'address' => $address,
        'description' => $fullDescription,
        'quantity' => max(1, (int)($job['quantity'] ?? 1)),
        'customer_total' => $customerTotal,
        'discount' => (int)($job['discount'] ?? 0),
        'final_total' => $finalTotal,
        'status' => job_status($pdo, 'pending'),
        'spam_count' => 0,
        'title' => $serviceType . ' #' . date('His'),
        'location' => $address,
        'salary_min' => $finalTotal,
        'salary_max' => $finalTotal,
        'worker_count' => 1,
    ];
    $systemUserId = get_system_user_id($pdo);
    if ($systemUserId !== null) {
        $values['employer_id'] = $systemUserId;
    }
    return insert_compat($pdo, 'job_posts', $values, ['created_at' => 'NOW()']);
}

function insert_job_pricing(PDO $pdo, int $jobId, array $pricing)
{
    insert_compat($pdo, 'job_pricing', [
        'job_id' => $jobId,
        'tech_target_base' => $pricing['tech_target_base'],
        'vat_amount' => $pricing['vat_amount'],
        'profit_amount' => $pricing['profit_amount'],
        'gross_customer_price' => $pricing['gross_customer_price'],
        'discount_amount' => $pricing['discount_amount'],
        'final_customer_price' => $pricing['final_customer_price'],
        'platform_fee' => $pricing['platform_fee'],
        'tech_net_income' => $pricing['tech_net_income'],
        'payment_status' => 'unpaid',
    ], ['created_at' => 'NOW()']);
}

function send_worker_job_to_group(PDO $pdo, int $jobId): bool
{
    $job = get_job_row($pdo, $jobId);
    if (!$job) {
        return false;
    }
    $pricing = get_job_pricing($pdo, $jobId);
    $chatId = telegram_chat('worker');
    if ($chatId === '') {
        return false;
    }

    $publicDescription = mask_phone_like_text((string)($job['description'] ?? ''));
    $text = "<b>CA GOI THO #{$jobId}</b>\n"
        . "Dich vu: " . esc_html($job['service_type'] ?? '') . "\n"
        . "Dia chi: " . esc_html($job['address'] ?? $job['location'] ?? '') . "\n"
        . "Mo ta: " . esc_html($publicDescription) . "\n"
        . "SDT an toan: " . esc_html(mask_phone((string)($job['customer_phone'] ?? ''))) . "\n"
        . "Gia khach: <b>" . fmt_money((int)($job['final_total'] ?? $pricing['final_customer_price'] ?? 0)) . "</b>\n"
        . "Tien tho muc tieu: <b>" . fmt_money((int)($pricing['tech_net_income'] ?? 0)) . "</b>\n\n"
        . "Tho REPLY vao tin nay de NHAN ca.\n"
        . "Neu yeu cau ao/spam, reply: SPAM";

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => 'Nhan ca', 'callback_data' => "claim_job_{$jobId}"],
                ['text' => 'Bao spam', 'callback_data' => "spam_job_{$jobId}"],
            ],
        ],
    ];
    $resp = tg_send('worker', $chatId, $text, $keyboard);
    $messageId = (int)($resp['result']['message_id'] ?? 0);
    if (!empty($resp['ok']) && $messageId > 0) {
        save_tg_map($pdo, 'worker', (string)$chatId, $messageId, 'job', $jobId);
        return true;
    }
    return false;
}

function claim_job(PDO $pdo, int $jobId, int $workerId, string $workerName, string $username = ''): array
{
    upsert_worker($pdo, $workerId, $workerName, $username);
    $profile = get_worker_profile($pdo, $workerId);
    if (worker_is_blocked($profile)) {
        return ['ok' => false, 'message' => 'Tai khoan tho dang bi khoa nhan ca. Lien he admin.'];
    }

    $pdo->beginTransaction();
    try {
        $job = get_job_row($pdo, $jobId, true);
        if (!$job) {
            $pdo->rollBack();
            return ['ok' => false, 'message' => 'Khong tim thay ca.'];
        }
        if (job_display_status($job) !== 'pending') {
            insert_compat($pdo, 'job_claims', [
                'job_id' => $jobId,
                'telegram_user_id' => $workerId,
                'telegram_name' => $workerName,
                'outcome' => 'late',
                'note' => 'Job is no longer pending',
            ], ['created_at' => 'NOW()']);
            $pdo->commit();
            return ['ok' => false, 'message' => 'Ca nay da co tho nhan hoac da dong.'];
        }

        update_compat($pdo, 'job_posts', [
            'worker_id' => $workerId,
            'status' => job_status($pdo, 'assigned'),
        ], 'id = ?', [$jobId], ['assigned_at' => 'NOW()', 'updated_at' => 'NOW()']);

        insert_compat($pdo, 'job_claims', [
            'job_id' => $jobId,
            'telegram_user_id' => $workerId,
            'telegram_name' => $workerName,
            'outcome' => 'claimed',
        ], ['created_at' => 'NOW()']);

        $pdo->prepare('UPDATE worker_profiles SET jobs_claimed = jobs_claimed + 1, updated_at = NOW() WHERE telegram_user_id = ?')
            ->execute([$workerId]);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    $job = get_job_row($pdo, $jobId) ?: [];
    $pricing = get_job_pricing($pdo, $jobId);
    $dm = "<b>BAN DA NHAN CA #{$jobId}</b>\n"
        . "Khach: " . esc_html($job['customer_name'] ?? '') . "\n"
        . "SDT day du: <b>" . esc_html($job['customer_phone'] ?? '') . "</b>\n"
        . "Dia chi: " . esc_html($job['address'] ?? $job['location'] ?? '') . "\n"
        . "Mo ta: " . esc_html($job['description'] ?? '') . "\n"
        . "Tien tho muc tieu: " . fmt_money((int)($pricing['tech_net_income'] ?? 0)) . "\n\n"
        . "Lam xong: REPLY vao tin nhan nay voi chu XONG.\n"
        . "Neu huy ca: REPLY voi chu HUY.";
    $resp = tg_send('worker', (string)$workerId, $dm, [
        'inline_keyboard' => [
            [
                ['text' => 'Da xong', 'callback_data' => "done_job_{$jobId}"],
                ['text' => 'Huy ca', 'callback_data' => "cancel_job_{$jobId}"],
            ],
        ],
    ]);
    $messageId = (int)($resp['result']['message_id'] ?? 0);
    if (empty($resp['ok']) || $messageId <= 0) {
        update_compat($pdo, 'job_posts', [
            'worker_id' => null,
            'status' => job_status($pdo, 'pending'),
            'cancel_reason' => 'DM to worker failed; worker may need /start.',
        ], 'id = ?', [$jobId], ['updated_at' => 'NOW()']);
        return ['ok' => false, 'message' => 'Bot khong DM duoc cho tho. Yeu cau tho /start voi Bot 1 truoc.'];
    }
    save_tg_map($pdo, 'worker', (string)$workerId, $messageId, 'job', $jobId);

    $groupChat = telegram_chat('worker');
    if ($groupChat !== '') {
        tg_send('worker', $groupChat, "Ca #{$jobId} da duoc nhan boi " . esc_html($workerName) . " ({$workerId}).");
    }
    return ['ok' => true, 'message' => "Da nhan ca #{$jobId}. Bot da gui SDT day du vao DM."];
}

function cancel_worker_job(PDO $pdo, int $jobId, int $workerId, string $workerName, string $reason): array
{
    $job = get_job_row($pdo, $jobId);
    if (!$job || (int)($job['worker_id'] ?? 0) !== $workerId) {
        return ['ok' => false, 'message' => 'Ca khong thuoc tho nay.'];
    }
    update_compat($pdo, 'job_posts', [
        'worker_id' => null,
        'status' => job_status($pdo, 'pending'),
        'cancel_reason' => $reason,
    ], 'id = ?', [$jobId], ['cancelled_at' => 'NOW()', 'updated_at' => 'NOW()']);

    insert_compat($pdo, 'job_claims', [
        'job_id' => $jobId,
        'telegram_user_id' => $workerId,
        'telegram_name' => $workerName,
        'outcome' => 'cancelled',
        'note' => $reason,
    ], ['created_at' => 'NOW()']);

    $profile = increment_worker_penalty($pdo, $workerId, 'cancel_job');
    $message = 'Da huy ca. So lan vi pham: ' . (int)($profile['cancel_count'] ?? 0) . '/' . tech_cancel_limit() . '.';
    if (worker_is_blocked($profile)) {
        $message .= ' Tai khoan da bi khoa nhan ca.';
    }
    $groupChat = telegram_chat('worker');
    if ($groupChat !== '') {
        tg_send('worker', $groupChat, "Ca #{$jobId} bi huy boi {$workerName}. Ly do: " . esc_html($reason));
    }
    return ['ok' => true, 'message' => $message];
}

function complete_worker_job(PDO $pdo, int $jobId, int $workerId, string $workerName): array
{
    $job = get_job_row($pdo, $jobId);
    if (!$job || (int)($job['worker_id'] ?? 0) !== $workerId) {
        return ['ok' => false, 'message' => 'Ca khong thuoc tho nay.'];
    }
    update_compat($pdo, 'job_posts', [
        'status' => job_status($pdo, 'completed'),
    ], 'id = ?', [$jobId], ['completed_at' => 'NOW()', 'updated_at' => 'NOW()']);

    $pricing = get_job_pricing($pdo, $jobId);
    update_compat($pdo, 'job_pricing', ['payment_status' => 'unpaid'], 'job_id = ?', [$jobId]);
    insert_compat($pdo, 'finances', [
        'type' => 'platform_fee_receivable',
        'amount' => (int)($pricing['platform_fee'] ?? 0),
        'source_type' => 'job',
        'source_id' => $jobId,
        'note' => "Platform fee debt from worker {$workerId}",
    ], ['created_at' => 'NOW()']);

    $pdo->prepare('UPDATE worker_profiles SET jobs_completed = jobs_completed + 1, updated_at = NOW() WHERE telegram_user_id = ?')
        ->execute([$workerId]);

    $groupChat = telegram_chat('worker');
    if ($groupChat !== '') {
        tg_send('worker', $groupChat, "Ca #{$jobId} da hoan thanh boi {$workerName}. Phi nen tang: " . fmt_money((int)($pricing['platform_fee'] ?? 0)));
    }
    return ['ok' => true, 'message' => "Da danh dau ca #{$jobId} hoan thanh."];
}

function text_is_cancel(string $text): bool
{
    return (bool)preg_match('/\b(huy|hủy|cancel|bo ca|bỏ ca)\b/iu', $text);
}

function text_is_spam(string $text): bool
{
    return (bool)preg_match('/\b(spam|fake|ao|ảo|bom|lua dao|lừa đảo|sai so|sai số)\b/iu', $text);
}

function record_spam_report(PDO $pdo, int $jobId, int $workerId, string $workerName, string $note): array
{
    upsert_worker($pdo, $workerId, $workerName);
    $pdo->prepare("INSERT IGNORE INTO spam_reports (job_id, telegram_user_id, telegram_name, note) VALUES (?, ?, ?, ?)")
        ->execute([$jobId, $workerId, $workerName, $note]);
    insert_compat($pdo, 'job_claims', [
        'job_id' => $jobId,
        'telegram_user_id' => $workerId,
        'telegram_name' => $workerName,
        'outcome' => 'spam_report',
        'note' => $note,
    ], ['created_at' => 'NOW()']);

    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM spam_reports WHERE job_id = ?');
    $countStmt->execute([$jobId]);
    $reportCount = (int)$countStmt->fetchColumn();

    $job = get_job_row($pdo, $jobId) ?: [];
    update_compat($pdo, 'job_posts', ['spam_count' => $reportCount], 'id = ?', [$jobId]);

    $alreadyAutoSpam = stripos((string)($job['cancel_reason'] ?? ''), 'Auto spam') !== false;
    if ($reportCount >= 5 && job_display_status($job) !== 'spam' && !$alreadyAutoSpam) {
        update_compat($pdo, 'job_posts', [
            'status' => job_status($pdo, 'spam'),
            'cancel_reason' => 'Auto spam: 5+ worker reports',
        ], 'id = ?', [$jobId], ['cancelled_at' => 'NOW()', 'updated_at' => 'NOW()']);

        $idStmt = $pdo->prepare('SELECT identifier, identifier_type FROM job_client_identifiers WHERE job_id = ?');
        $idStmt->execute([$jobId]);
        foreach ($idStmt->fetchAll() as $item) {
            $pdo->prepare("INSERT INTO client_abuse (identifier, identifier_type, request_count, fake_count, last_job_id)
                VALUES (?, ?, 1, 1, ?)
                ON DUPLICATE KEY UPDATE fake_count = fake_count + 1, last_job_id = VALUES(last_job_id), updated_at = NOW()")
                ->execute([$item['identifier'], $item['identifier_type'], $jobId]);

            $check = $pdo->prepare('SELECT fake_count FROM client_abuse WHERE identifier = ? AND identifier_type = ? LIMIT 1');
            $check->execute([$item['identifier'], $item['identifier_type']]);
            $fakeCount = (int)$check->fetchColumn();
            if ($fakeCount >= 3) {
                $pdo->prepare("INSERT INTO banned_devices (identifier, ban_type, reason, spam_job_id, spam_count, created_by, created_at)
                    VALUES (?, ?, ?, ?, ?, 'system', NOW())
                    ON DUPLICATE KEY UPDATE reason = VALUES(reason), spam_job_id = VALUES(spam_job_id), spam_count = VALUES(spam_count), expires_at = NULL")
                    ->execute([$item['identifier'], $item['identifier_type'], 'Auto-ban: 3 fake jobs and 5+ worker spam replies.', $jobId, $fakeCount]);
                $pdo->prepare('UPDATE client_abuse SET banned_at = NOW() WHERE identifier = ? AND identifier_type = ?')
                    ->execute([$item['identifier'], $item['identifier_type']]);
            }
        }
    }

    return ['ok' => true, 'message' => "Da ghi nhan spam report ({$reportCount}/5)."];
}

function load_product_snapshot(PDO $pdo, int $productId, string $type, array $input): array
{
    $name = clean_string($input['name'] ?? $input['product_name'] ?? 'San pham', 255);
    $price = money_int($input['gia_ban'] ?? $input['price'] ?? 0);

    if ($productId > 0 && $type === 'sim') {
        foreach (['marketplace_sims', 'sims'] as $table) {
            if (!table_exists($pdo, $table)) {
                continue;
            }
            $cols = '*';
            $stmt = $pdo->prepare("SELECT {$cols} FROM " . db_ident($table) . ' WHERE id = ? LIMIT 1');
            $stmt->execute([$productId]);
            $row = $stmt->fetch();
            if ($row) {
                $simNumber = (string)($row['so_sim'] ?? $row['phone_number'] ?? $row['name'] ?? '');
                $network = (string)($row['nha_mang'] ?? $row['network'] ?? '');
                $name = trim('SIM ' . $simNumber . ($network !== '' ? ' - ' . $network : ''));
                $price = money_int($row['gia_ban'] ?? $row['price'] ?? $price);
                return ['name' => $name, 'price' => $price, 'table' => $table];
            }
        }
    }

    if ($productId > 0 && table_exists($pdo, 'products')) {
        $cols = legacy_product_columns($pdo);
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
        $stmt->execute([$productId]);
        $row = $stmt->fetch();
        if ($row && $cols !== []) {
            $name = (string)($row[$cols['name']] ?? $name);
            $price = money_int($row[$cols['price']] ?? $price);
            return ['name' => $name, 'price' => $price, 'table' => 'products'];
        }
    }

    if ($productId > 0 && table_exists($pdo, 'marketplace_products')) {
        $stmt = $pdo->prepare('SELECT * FROM marketplace_products WHERE id = ? LIMIT 1');
        $stmt->execute([$productId]);
        $row = $stmt->fetch();
        if ($row) {
            $name = (string)($row['name'] ?? $name);
            $price = money_int($row['sale_price'] ?? $row['price'] ?? $price);
            return ['name' => $name, 'price' => $price, 'table' => 'marketplace_products'];
        }
    }

    return ['name' => $name, 'price' => $price, 'table' => 'input'];
}

function next_order_code(): string
{
    return 'DTH-' . date('Ymd-His') . '-' . random_int(100, 999);
}

function create_order(array $input): array
{
    $pdo = pdo();
    $name = clean_string($input['ten_khach'] ?? $input['customer_name'] ?? '', 150);
    $phone = digits_only($input['sdt'] ?? $input['phone'] ?? $input['customer_phone'] ?? '');
    $payment = clean_string($input['payment_method'] ?? 'cod', 40);
    $type = clean_string($input['type'] ?? 'product', 30);
    $productId = (int)($input['product_id'] ?? 0);

    if ($name === '' || strlen($phone) < 8) {
        json_out(['status' => 'error', 'message' => 'Nhap ten va so dien thoai hop le.'], 400);
    }

    ensure_not_banned($pdo, active_identifiers($input, $phone));
    $snapshot = load_product_snapshot($pdo, $productId, $type, $input);
    if ($snapshot['price'] <= 0) {
        json_out(['status' => 'error', 'message' => 'Gia san pham khong hop le.'], 400);
    }

    $voucherCode = clean_string($input['coupon_code'] ?? $input['voucher_code'] ?? '', 80);
    $discount = apply_voucher_if_valid($pdo, $voucherCode, (int)$snapshot['price']);
    $total = max(0, (int)$snapshot['price'] - (int)$discount['amount']);
    $orderCode = next_order_code();

    $pdo->beginTransaction();
    try {
        if ($snapshot['table'] === 'products' && $productId > 0) {
            $cols = legacy_product_columns($pdo);
            $stockCol = $cols['stock'] ?? null;
            if ($stockCol !== null) {
                $updatedExpr = column_exists($pdo, 'products', 'updated_at') ? ', updated_at = NOW()' : '';
                $pdo->prepare('UPDATE products SET ' . db_ident($stockCol) . ' = GREATEST(' . db_ident($stockCol) . ' - 1, 0)' . $updatedExpr . ' WHERE id = ?')
                    ->execute([$productId]);
            }
        }

        $orderId = insert_compat($pdo, 'orders', [
            'order_code' => $orderCode,
            'customer_name' => $name,
            'customer_phone' => $phone,
            'product_id' => $productId,
            'product_name' => $snapshot['name'],
            'total_price' => $total,
            'total' => $total,
            'subtotal' => (int)$snapshot['price'],
            'discount' => (int)$discount['amount'],
            'status' => order_status($pdo, 'pending'),
            'payment_method' => $payment,
            'payment_status' => $payment === 'bank' ? 'unpaid' : 'unpaid',
            'coupon_code' => $voucherCode,
            'voucher_code' => $voucherCode,
            'note' => clean_string($input['note'] ?? '', 1000),
        ], ['created_at' => 'NOW()']);

        if (table_exists($pdo, 'order_items')) {
            insert_compat($pdo, 'order_items', [
                'order_id' => $orderId,
                'product_id' => $productId,
                'product_name' => $snapshot['name'],
                'product_type' => $type,
                'quantity' => 1,
                'price' => (int)$snapshot['price'],
                'subtotal' => (int)$snapshot['price'],
            ], ['created_at' => 'NOW()']);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    send_order_to_boss($pdo, $orderId);
    return [
        'status' => 'success',
        'message' => 'Dat hang thanh cong.',
        'order_id' => (string)$orderId,
        'order_code' => $orderCode,
        'product_name' => $snapshot['name'],
        'total_price' => $total,
        'discount' => (int)$discount['amount'],
    ];
}

function apply_voucher_if_valid(PDO $pdo, string $code, int $subtotal): array
{
    if ($code === '') {
        return ['amount' => 0, 'voucher_id' => null];
    }
    $stmt = $pdo->prepare('SELECT * FROM vouchers WHERE code = ? LIMIT 1');
    $stmt->execute([$code]);
    $voucher = $stmt->fetch();
    if (!$voucher) {
        return ['amount' => 0, 'voucher_id' => null];
    }
    $maxUses = (int)($voucher['max_uses'] ?? $voucher['usage_limit'] ?? 1);
    $used = (int)($voucher['used_count'] ?? 0);
    $active = array_key_exists('is_active', $voucher) ? (int)$voucher['is_active'] === 1 : true;
    $expires = (string)($voucher['expires_at'] ?? '');
    if (!$active || $used >= $maxUses || ($expires !== '' && strtotime($expires) !== false && strtotime($expires) < time())) {
        return ['amount' => 0, 'voucher_id' => null];
    }
    $percent = (int)($voucher['discount_percent'] ?? 0);
    $amount = (int)($voucher['discount_amount'] ?? 0);
    if ($amount <= 0 && $percent <= 0 && isset($voucher['type'], $voucher['value'])) {
        if ($voucher['type'] === 'percent') {
            $percent = (int)$voucher['value'];
        } else {
            $amount = (int)$voucher['value'];
        }
    }
    if ($percent > 0) {
        $amount = max($amount, (int)round($subtotal * min(100, $percent) / 100));
    }
    $amount = min($subtotal, max(0, $amount));
    $pdo->prepare('UPDATE vouchers SET used_count = used_count + 1 WHERE id = ?')->execute([(int)$voucher['id']]);
    return ['amount' => $amount, 'voucher_id' => (int)$voucher['id']];
}

function get_order_row(PDO $pdo, int $orderId)
{
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function send_order_to_boss(PDO $pdo, int $orderId): bool
{
    $order = get_order_row($pdo, $orderId);
    $chatId = telegram_chat('sales');
    if (!$order || $chatId === '') {
        return false;
    }
    $text = "<b>DON HANG MOI #{$orderId}</b>\n"
        . "Khach: " . esc_html($order['customer_name'] ?? '') . "\n"
        . "Phone: " . esc_html($order['customer_phone'] ?? '') . "\n"
        . "San pham: " . esc_html($order['product_name'] ?? '') . "\n"
        . "Tong tien: <b>" . fmt_money((int)($order['total_price'] ?? $order['total'] ?? 0)) . "</b>\n"
        . "Thanh toan: " . esc_html($order['payment_method'] ?? 'cod') . "\n\n"
        . "Boss REPLY vao tin nay: OK de xac nhan, HUY de tu choi.";
    $resp = tg_send('sales', $chatId, $text, [
        'inline_keyboard' => [
            [
                ['text' => 'Xac nhan', 'callback_data' => "confirm_order_{$orderId}"],
                ['text' => 'Tu choi', 'callback_data' => "reject_order_{$orderId}"],
            ],
        ],
    ]);
    $messageId = (int)($resp['result']['message_id'] ?? 0);
    if (!empty($resp['ok']) && $messageId > 0) {
        save_tg_map($pdo, 'sales', (string)$chatId, $messageId, 'order', $orderId);
        insert_compat($pdo, 'order_notifications', [
            'order_id' => $orderId,
            'telegram_message_id' => $messageId,
            'boss_chat_id' => (string)$chatId,
            'status' => 'pending',
        ], ['created_at' => 'NOW()']);
        return true;
    }
    return false;
}

function confirm_order(PDO $pdo, int $orderId, string $bossName, bool $accepted): array
{
    $order = get_order_row($pdo, $orderId);
    if (!$order) {
        return ['ok' => false, 'message' => 'Khong tim thay don hang.'];
    }
    $newStatus = $accepted ? order_status($pdo, 'confirmed') : order_status($pdo, 'rejected');
    update_compat($pdo, 'orders', [
        'status' => $newStatus,
        'confirmed_by' => $bossName,
    ], 'id = ?', [$orderId], ['confirmed_at' => 'NOW()', 'updated_at' => 'NOW()']);
    update_compat($pdo, 'order_notifications', [
        'status' => $accepted ? 'confirmed' : 'rejected',
        'confirmed_by' => $bossName,
    ], 'order_id = ?', [$orderId], ['confirmed_at' => 'NOW()']);

    insert_compat($pdo, 'finances', [
        'type' => $accepted ? 'order_confirmed' : 'order_rejected',
        'amount' => (int)($order['total_price'] ?? $order['total'] ?? 0),
        'source_type' => 'order',
        'source_id' => $orderId,
        'note' => $accepted ? 'Boss confirmed order' : 'Boss rejected order',
    ], ['created_at' => 'NOW()']);

    return ['ok' => true, 'message' => $accepted ? "Don #{$orderId} da xac nhan." : "Don #{$orderId} da tu choi."];
}

function verify_webhook_secret()
{
    $secret = app_env('TELEGRAM_WEBHOOK_SECRET', '');
    if ($secret === '') {
        return;
    }
    $actual = (string)($_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? $_GET['secret'] ?? '');
    if (!hash_equals($secret, $actual)) {
        json_out(['status' => 'error', 'message' => 'Invalid webhook secret.'], 403);
    }
}

function handle_worker_webhook(PDO $pdo, array $update): array
{
    if (isset($update['callback_query'])) {
        $cb = $update['callback_query'];
        $data = (string)($cb['data'] ?? '');
        $from = (array)($cb['from'] ?? []);
        $workerId = (int)($from['id'] ?? 0);
        $name = worker_name($from);
        $username = (string)($from['username'] ?? '');
        $callbackId = (string)($cb['id'] ?? '');
        if (preg_match('/^claim_job_(\d+)$/', $data, $m)) {
            $r = claim_job($pdo, (int)$m[1], $workerId, $name, $username);
            tg_answer_callback('worker', $callbackId, $r['message'], !$r['ok']);
            return $r;
        }
        if (preg_match('/^spam_job_(\d+)$/', $data, $m)) {
            $r = record_spam_report($pdo, (int)$m[1], $workerId, $name, 'callback spam');
            tg_answer_callback('worker', $callbackId, $r['message']);
            return $r;
        }
        if (preg_match('/^done_job_(\d+)$/', $data, $m)) {
            $r = complete_worker_job($pdo, (int)$m[1], $workerId, $name);
            tg_answer_callback('worker', $callbackId, $r['message'], !$r['ok']);
            return $r;
        }
        if (preg_match('/^cancel_job_(\d+)$/', $data, $m)) {
            $r = cancel_worker_job($pdo, (int)$m[1], $workerId, $name, 'worker callback cancel');
            tg_answer_callback('worker', $callbackId, $r['message'], !$r['ok']);
            return $r;
        }
        return ['ok' => true, 'message' => 'callback ignored'];
    }

    $msg = (array)($update['message'] ?? $update['edited_message'] ?? []);
    if ($msg === []) {
        return ['ok' => true, 'message' => 'empty update'];
    }
    $chat = (array)($msg['chat'] ?? []);
    $from = (array)($msg['from'] ?? []);
    $chatId = (string)($chat['id'] ?? '');
    $chatType = (string)($chat['type'] ?? '');
    $workerId = (int)($from['id'] ?? 0);
    $name = worker_name($from);
    $username = (string)($from['username'] ?? '');
    $text = clean_string($msg['text'] ?? $msg['caption'] ?? '', 1000);

    if ($workerId > 0) {
        upsert_worker($pdo, $workerId, $name, $username);
    }

    if (dth_starts_with($text, '/start')) {
        tg_send('worker', (string)$workerId, "Bot Anh Thien 1 da ket noi. Reply vao tin ca trong nhom de nhan viec. Khi bot DM, reply XONG de ket thuc ca.");
        return ['ok' => true, 'message' => 'started'];
    }

    $reply = (array)($msg['reply_to_message'] ?? []);
    $replyMessageId = (int)($reply['message_id'] ?? 0);
    if ($replyMessageId <= 0 || $chatId === '') {
        return ['ok' => true, 'message' => 'not a mapped reply'];
    }
    $jobId = find_tg_entity($pdo, 'worker', $chatId, $replyMessageId, 'job');
    if ($jobId === null) {
        return ['ok' => true, 'message' => 'reply not mapped'];
    }

    if ($chatType === 'private') {
        $result = text_is_cancel($text)
            ? cancel_worker_job($pdo, $jobId, $workerId, $name, $text !== '' ? $text : 'worker cancel')
            : complete_worker_job($pdo, $jobId, $workerId, $name);
        tg_send('worker', (string)$workerId, $result['message']);
        return $result;
    }

    if (text_is_spam($text)) {
        $result = record_spam_report($pdo, $jobId, $workerId, $name, $text);
        tg_send('worker', $chatId, $result['message']);
        return $result;
    }

    $result = claim_job($pdo, $jobId, $workerId, $name, $username);
    if (!$result['ok']) {
        tg_send('worker', $chatId, $result['message']);
    }
    return $result;
}

function handle_sales_webhook(PDO $pdo, array $update): array
{
    if (isset($update['callback_query'])) {
        $cb = $update['callback_query'];
        $data = (string)($cb['data'] ?? '');
        $from = (array)($cb['from'] ?? []);
        $name = worker_name($from);
        $callbackId = (string)($cb['id'] ?? '');
        if (preg_match('/^confirm_order_(\d+)$/', $data, $m)) {
            $r = confirm_order($pdo, (int)$m[1], $name, true);
            tg_answer_callback('sales', $callbackId, $r['message'], !$r['ok']);
            return $r;
        }
        if (preg_match('/^reject_order_(\d+)$/', $data, $m)) {
            $r = confirm_order($pdo, (int)$m[1], $name, false);
            tg_answer_callback('sales', $callbackId, $r['message'], !$r['ok']);
            return $r;
        }
        return ['ok' => true, 'message' => 'callback ignored'];
    }

    $msg = (array)($update['message'] ?? $update['edited_message'] ?? []);
    if ($msg === []) {
        return ['ok' => true, 'message' => 'empty update'];
    }
    $chatId = (string)($msg['chat']['id'] ?? '');
    $replyMessageId = (int)($msg['reply_to_message']['message_id'] ?? 0);
    $text = clean_string($msg['text'] ?? $msg['caption'] ?? '', 1000);
    $from = (array)($msg['from'] ?? []);
    $name = worker_name($from);
    if ($chatId === '' || $replyMessageId <= 0) {
        return ['ok' => true, 'message' => 'not a reply'];
    }
    $orderId = find_tg_entity($pdo, 'sales', $chatId, $replyMessageId, 'order');
    if ($orderId === null) {
        return ['ok' => true, 'message' => 'reply not mapped'];
    }
    $reject = (bool)preg_match('/\b(huy|hủy|reject|cancel|khong|không|tu choi|từ chối)\b/iu', $text);
    $confirm = $reject ? false : true;
    $result = confirm_order($pdo, $orderId, $name, $confirm);
    tg_send('sales', $chatId, $result['message']);
    return $result;
}

function handle_telegram_webhook()
{
    verify_webhook_secret();
    $pdo = pdo();
    $raw = file_get_contents('php://input') ?: '{}';
    $update = json_decode($raw, true);
    if (!is_array($update)) {
        json_out(['ok' => false, 'message' => 'Invalid JSON'], 400);
    }
    $bot = strtolower((string)($_GET['bot'] ?? $_GET['role'] ?? 'worker'));
    $role = in_array($bot, ['2', 'sales', 'report', 'boss'], true) ? 'sales' : 'worker';
    $result = $role === 'sales' ? handle_sales_webhook($pdo, $update) : handle_worker_webhook($pdo, $update);
    json_out(['ok' => true, 'result' => $result]);
}

function create_job_action(array $input): array
{
    $pdo = pdo();
    $phone = digits_only($input['phone'] ?? $input['sdt'] ?? $input['customer_phone'] ?? '');
    $address = clean_string($input['address'] ?? $input['dia_chi'] ?? '', 1000);
    $description = clean_string($input['issue_description'] ?? $input['mo_ta'] ?? $input['description'] ?? '', 3000);
    $serviceType = clean_string($input['service_type'] ?? $input['loai_tho'] ?? 'Dich vu dien lanh', 150);
    $customerName = clean_string($input['customer_name'] ?? $input['name'] ?? 'Khach', 150);
    $quantity = max(1, (int)($input['quantity'] ?? $input['qty'] ?? 1));
    $techBase = money_int($input['tech_target_base'] ?? $input['tech_base'] ?? 0);
    $estimated = money_int($input['estimated_price'] ?? $input['customer_price'] ?? $input['final_total'] ?? 0);

    if (strlen($phone) < 8 || $address === '' || $description === '') {
        json_out(['status' => 'error', 'message' => 'Thieu phone, dia chi hoac mo ta su co.'], 400);
    }

    $identifiers = active_identifiers($input, $phone);
    ensure_not_banned($pdo, $identifiers);
    $pricing = calculate_job_pricing($techBase, $estimated, $quantity);

    $jobId = insert_repair_job($pdo, [
        'customer_name' => $customerName,
        'customer_phone' => $phone,
        'service_type' => $serviceType,
        'address' => $address,
        'description' => $description,
        'quantity' => $quantity,
        'customer_total' => $pricing['gross_customer_price'],
        'discount' => $pricing['discount_amount'],
        'final_total' => $pricing['final_customer_price'],
    ]);
    insert_job_pricing($pdo, $jobId, $pricing);
    record_client_request($pdo, $jobId, $identifiers);
    $sent = send_worker_job_to_group($pdo, $jobId);

    return [
        'status' => 'success',
        'success' => true,
        'message' => 'Da tao yeu cau goi tho. Random discount ' . $pricing['discount_label'] . '.',
        'job_id' => $jobId,
        'telegram_sent' => $sent,
        'tech_target_base' => $pricing['tech_target_base'],
        'vat_amount' => $pricing['vat_amount'],
        'profit_amount' => $pricing['profit_amount'],
        'customer_total' => $pricing['gross_customer_price'],
        'discount' => $pricing['discount_amount'],
        'final_total' => $pricing['final_customer_price'],
        'platform_fee' => $pricing['platform_fee'],
        'tech_net_income' => $pricing['tech_net_income'],
        'discount_roll' => $pricing['discount_roll'],
    ];
}

function legacy_product_columns(PDO $pdo): array
{
    if (!table_exists($pdo, 'products')) {
        return [];
    }
    $nameCol = first_existing_column($pdo, 'products', ['name', 'ten_sp', 'product_name', 'title']);
    $priceCol = first_existing_column($pdo, 'products', ['price', 'gia_ban', 'sale_price', 'gia']);
    if ($nameCol === null || $priceCol === null) {
        return [];
    }
    return [
        'name' => $nameCol,
        'price' => $priceCol,
        'stock' => first_existing_column($pdo, 'products', ['stock_quantity', 'ton_kho', 'stock']),
        'image' => first_existing_column($pdo, 'products', ['image_url', 'hinh_anh', 'image', 'thumbnail']),
        'category' => first_existing_column($pdo, 'products', ['category', 'danh_muc', 'loai_sp']),
        'created' => first_existing_column($pdo, 'products', ['created_at', 'ngay_tao', 'date_created']),
    ];
}

function legacy_products_for_store(PDO $pdo, string $keyword = '', string $sort = '', int $limit = 200): array
{
    $cols = legacy_product_columns($pdo);
    if ($cols === []) {
        return [];
    }
    $select = [
        'id',
        db_ident($cols['name']) . ' AS name',
        db_ident($cols['price']) . ' AS price',
        ($cols['stock'] ? db_ident($cols['stock']) : '0') . ' AS stock_quantity',
        ($cols['image'] ? db_ident($cols['image']) : "''") . ' AS image_url',
        ($cols['category'] ? db_ident($cols['category']) : "'Store'") . ' AS category',
        ($cols['created'] ? db_ident($cols['created']) : 'NOW()') . ' AS created_at',
    ];
    $where = [];
    $params = [];
    if ($keyword !== '') {
        $where[] = db_ident($cols['name']) . ' LIKE ?';
        $params[] = "%{$keyword}%";
        if ($cols['category']) {
            $where[] = db_ident($cols['category']) . ' LIKE ?';
            $params[] = "%{$keyword}%";
        }
    }
    $orderCol = $sort === 'asc' || $sort === 'desc' ? db_ident($cols['price']) . ' ' . strtoupper($sort) : 'id DESC';
    $sql = 'SELECT ' . implode(', ', $select) . ' FROM products'
        . ($where ? ' WHERE (' . implode(' OR ', $where) . ')' : '')
        . ' ORDER BY ' . $orderCol . ' LIMIT ' . max(1, min(500, $limit));
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = [];
    foreach ($stmt->fetchAll() as $row) {
        $price = money_int($row['price'] ?? 0);
        $items[] = [
            'id' => (int)$row['id'],
            'name' => (string)$row['name'],
            'price' => $price,
            'gia_ban_fm' => fmt_money($price),
            'stock_quantity' => (int)($row['stock_quantity'] ?? 0),
            'image' => (string)($row['image_url'] ?? ''),
            'image_url' => (string)($row['image_url'] ?? ''),
            'category' => (string)($row['category'] ?? 'Store'),
            'created_at' => (string)($row['created_at'] ?? ''),
            'src' => 'product',
        ];
    }
    return $items;
}

function marketplace_products_for_store(PDO $pdo, string $keyword = '', string $sort = '', int $limit = 200): array
{
    if (!table_exists($pdo, 'marketplace_products')) {
        return [];
    }
    $where = [];
    $params = [];
    if (column_exists($pdo, 'marketplace_products', 'status')) {
        $where[] = "status IN ('active','sold','draft')";
    }
    if ($keyword !== '') {
        if (column_exists($pdo, 'marketplace_products', 'description')) {
            $where[] = '(name LIKE ? OR description LIKE ?)';
            $params[] = "%{$keyword}%";
            $params[] = "%{$keyword}%";
        } else {
            $where[] = 'name LIKE ?';
            $params[] = "%{$keyword}%";
        }
    }
    $order = $sort === 'asc' ? 'price ASC' : ($sort === 'desc' ? 'price DESC' : 'id DESC');
    $sql = 'SELECT * FROM marketplace_products'
        . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
        . ' ORDER BY ' . $order . ' LIMIT ' . max(1, min(500, $limit));
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = [];
    foreach ($stmt->fetchAll() as $row) {
        $price = money_int($row['sale_price'] ?? $row['price'] ?? 0);
        $image = '';
        $images = json_decode((string)($row['images'] ?? ''), true);
        if (is_array($images) && isset($images[0])) {
            $image = (string)$images[0];
        }
        $items[] = [
            'id' => (int)$row['id'],
            'name' => (string)$row['name'],
            'price' => $price,
            'gia_ban_fm' => fmt_money($price),
            'stock_quantity' => (int)($row['stock'] ?? 0),
            'image' => $image,
            'image_url' => $image,
            'category' => (string)($row['type'] ?? 'Marketplace'),
            'created_at' => (string)($row['created_at'] ?? ''),
            'src' => 'marketplace',
        ];
    }
    return $items;
}

function admin_product_target(PDO $pdo): string
{
    return legacy_product_columns($pdo) !== [] ? 'products' : 'marketplace_products';
}

function save_admin_product(PDO $pdo, array $input): int
{
    $id = (int)($input['id'] ?? 0);
    $name = clean_string($input['name'] ?? '', 255);
    if ($name === '') {
        json_out(['status' => 'error', 'message' => 'Ten san pham khong duoc trong.'], 400);
    }

    $price = money_int($input['price'] ?? 0);
    $stock = max(0, (int)($input['stock'] ?? $input['stock_quantity'] ?? 0));
    $category = clean_string($input['category'] ?? '', 120);
    $image = clean_string($input['image_url'] ?? $input['image'] ?? '', 700);

    $legacy = legacy_product_columns($pdo);
    if ($legacy !== []) {
        $values = [
            $legacy['name'] => $name,
            $legacy['price'] => $price,
        ];
        if (!empty($legacy['stock'])) {
            $values[$legacy['stock']] = $stock;
        }
        if (!empty($legacy['category'])) {
            $values[$legacy['category']] = $category;
        }
        if (!empty($legacy['image'])) {
            $values[$legacy['image']] = $image;
        }
        if ($id > 0) {
            update_compat($pdo, 'products', $values, 'id = ?', [$id], ['updated_at' => 'NOW()']);
            return $id;
        }
        return insert_compat($pdo, 'products', $values, ['created_at' => 'NOW()']);
    }

    if (!table_exists($pdo, 'marketplace_products')) {
        json_out(['status' => 'error', 'message' => 'Khong tim thay bang san pham thuc te.'], 500);
    }
    $type = $category !== '' ? $category : 'dien_may';
    if (!column_allows_value($pdo, 'marketplace_products', 'type', $type)) {
        $type = column_allows_value($pdo, 'marketplace_products', 'type', 'dien_may') ? 'dien_may' : 'other';
    }
    $values = [
        'name' => $name,
        'price' => $price,
        'sale_price' => $price,
        'stock' => $stock,
        'type' => $type,
        'status' => 'active',
    ];
    if ($image !== '' && column_exists($pdo, 'marketplace_products', 'images')) {
        $values['images'] = json_encode([$image], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    if ($id > 0) {
        update_compat($pdo, 'marketplace_products', $values, 'id = ?', [$id], ['updated_at' => 'NOW()']);
        return $id;
    }
    return insert_compat($pdo, 'marketplace_products', $values, ['created_at' => 'NOW()']);
}

function products_for_store(PDO $pdo, array $input): array
{
    $keyword = clean_string($input['keyword'] ?? '', 100);
    $sort = clean_string($input['sort'] ?? '', 20);
    $items = [];

    $items = array_merge($items, legacy_products_for_store($pdo, $keyword, $sort, 200));

    $items = array_merge($items, marketplace_products_for_store($pdo, $keyword, $sort, 200));

    foreach (['marketplace_sims', 'sims'] as $table) {
        if (!table_exists($pdo, $table)) {
            continue;
        }
        $stmt = $pdo->query('SELECT * FROM ' . db_ident($table) . ' ORDER BY id DESC LIMIT 100');
        foreach ($stmt->fetchAll() as $row) {
            $number = (string)($row['so_sim'] ?? $row['phone_number'] ?? '');
            if ($keyword !== '' && stripos($number, $keyword) === false) {
                continue;
            }
            $price = money_int($row['gia_ban'] ?? $row['price'] ?? 0);
            $items[] = [
                'id' => (int)$row['id'],
                'name' => $number,
                'price' => $price,
                'gia_ban_fm' => fmt_money($price),
                'category' => (string)($row['loai_sim'] ?? $row['sim_type'] ?? 'SIM'),
                'nha_mang' => (string)($row['nha_mang'] ?? $row['network'] ?? ''),
                'image' => '',
                'src' => 'sim',
            ];
        }
    }

    return $items;
}

function admin_orders(PDO $pdo): array
{
    if (!table_exists($pdo, 'orders')) {
        return [];
    }
    $stmt = $pdo->query('SELECT * FROM orders ORDER BY id DESC LIMIT 200');
    $rows = [];
    foreach ($stmt->fetchAll() as $row) {
        $rows[] = [
            'id' => (int)$row['id'],
            'order_code' => (string)($row['order_code'] ?? ''),
            'customer_name' => (string)($row['customer_name'] ?? ''),
            'customer_phone' => (string)($row['customer_phone'] ?? ''),
            'product_name' => (string)($row['product_name'] ?? ''),
            'total_price' => money_int($row['total_price'] ?? $row['total'] ?? 0),
            'status' => (string)($row['status'] ?? ''),
            'payment_method' => (string)($row['payment_method'] ?? ''),
            'created_at' => (string)($row['created_at'] ?? ''),
        ];
    }
    return $rows;
}

function admin_jobs(PDO $pdo): array
{
    if (!table_exists($pdo, 'job_posts')) {
        return [];
    }
    $stmt = $pdo->query('SELECT * FROM job_posts ORDER BY id DESC LIMIT 200');
    $rows = [];
    foreach ($stmt->fetchAll() as $row) {
        $pricing = get_job_pricing($pdo, (int)$row['id']);
        $rows[] = [
            'id' => (int)$row['id'],
            'customer_name' => (string)($row['customer_name'] ?? ''),
            'customer_phone' => (string)($row['customer_phone'] ?? ''),
            'service_type' => (string)($row['service_type'] ?? $row['title'] ?? ''),
            'address' => (string)($row['address'] ?? $row['location'] ?? ''),
            'description' => (string)($row['description'] ?? ''),
            'final_total' => money_int($row['final_total'] ?? $row['customer_total'] ?? $row['salary_max'] ?? 0),
            'platform_fee' => money_int($pricing['platform_fee'] ?? 0),
            'tech_net_income' => money_int($pricing['tech_net_income'] ?? 0),
            'worker_id' => $row['worker_id'] ?? null,
            'status' => job_display_status($row),
            'spam_count' => (int)($row['spam_count'] ?? 0),
            'created_at' => (string)($row['created_at'] ?? ''),
        ];
    }
    return $rows;
}

function admin_stats(PDO $pdo): array
{
    $orders = admin_orders($pdo);
    $jobs = admin_jobs($pdo);
    $today = date('Y-m-d');
    $todayOrders = array_filter($orders, static function (array $o) use ($today): bool {
        return dth_starts_with((string)$o['created_at'], $today);
    });
    $todayJobs = array_filter($jobs, static function (array $j) use ($today): bool {
        return dth_starts_with((string)$j['created_at'], $today);
    });

    $unpaidTotal = 0;
    $unpaidCount = 0;
    if (table_exists($pdo, 'job_pricing') && table_exists($pdo, 'job_posts')) {
        $stmt = $pdo->query("SELECT COUNT(*) c, COALESCE(SUM(platform_fee),0) s FROM job_pricing WHERE payment_status = 'unpaid'");
        $r = $stmt->fetch() ?: ['c' => 0, 's' => 0];
        $unpaidCount = (int)$r['c'];
        $unpaidTotal = (int)$r['s'];
    }

    $productCount = 0;
    if (legacy_product_columns($pdo) !== []) {
        $productCount += (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    }
    if (table_exists($pdo, 'marketplace_products')) {
        $productCount += (int)$pdo->query('SELECT COUNT(*) FROM marketplace_products')->fetchColumn();
    }
    $banCount = table_exists($pdo, 'banned_devices') ? (int)$pdo->query("SELECT COUNT(*) FROM banned_devices WHERE expires_at IS NULL OR expires_at > NOW()")->fetchColumn() : 0;
    $activeWorkers = table_exists($pdo, 'worker_profiles') ? (int)$pdo->query("SELECT COUNT(*) FROM worker_profiles WHERE is_receive_blocked = 0")->fetchColumn() : 0;

    return [
        'total_orders' => count($orders),
        'total_revenue' => array_sum(array_column($orders, 'total_price')),
        'today_orders' => count($todayOrders),
        'today_revenue' => array_sum(array_column($todayOrders, 'total_price')),
        'total_jobs' => count($jobs),
        'pending_jobs' => count(array_filter($jobs, static function (array $j): bool {
            return $j['status'] === 'pending';
        })),
        'completed_jobs' => count(array_filter($jobs, static function (array $j): bool {
            return $j['status'] === 'completed';
        })),
        'today_jobs' => count($todayJobs),
        'total_products' => $productCount,
        'total_sims' => 0,
        'active_workers' => $activeWorkers,
        'unpaid_count' => $unpaidCount,
        'unpaid_total' => $unpaidTotal,
        'banned_devices' => $banCount,
    ];
}

function require_admin_for_action(string $action)
{
    $adminActions = ['generate_qr', 'generate_voucher'];
    if (!dth_starts_with($action, 'admin_') && !in_array($action, $adminActions, true)) {
        return;
    }
    app_require_admin_json();
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        app_ensure_session();
        $expected = (string)($_SESSION['csrf_token'] ?? '');
        $actual = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if ($expected !== '' && !hash_equals($expected, $actual)) {
            json_out(['status' => 'error', 'message' => 'Invalid CSRF token.'], 403);
        }
    }
}

$action = clean_string($_GET['action'] ?? '', 80);
if ($action === '') {
    json_out(['status' => 'ok', 'message' => 'api_master online']);
}

if ($action === 'telegram_webhook') {
    try {
        handle_telegram_webhook();
    } catch (Throwable $e) {
        api_exception_out($e);
    }
}

try {
    require_admin_for_action($action);
    $input = request_data();
    $pdo = pdo();

    switch ($action) {
    case 'health':
        json_out(['status' => 'ok', 'time' => date('c')]);

    case 'get_products':
        json_out(['status' => 'success', 'data' => products_for_store($pdo, $input)]);

    case 'create_order':
        json_out(create_order($input));

    case 'create_job':
        json_out(create_job_action($input));

    case 'check_voucher':
        $code = clean_string($input['code'] ?? '', 80);
        if ($code === '') {
            json_out(['status' => 'error', 'message' => 'Nhap ma voucher.'], 400);
        }
        $stmt = $pdo->prepare('SELECT * FROM vouchers WHERE code = ? LIMIT 1');
        $stmt->execute([$code]);
        $v = $stmt->fetch();
        if (!$v) {
            json_out(['status' => 'error', 'message' => 'Ma khong hop le.']);
        }
        json_out([
            'status' => 'success',
            'code' => $code,
            'discount_percent' => (int)($v['discount_percent'] ?? 0),
            'discount_amount' => (int)($v['discount_amount'] ?? 0),
        ]);

    case 'save_wheel_prize':
        $code = clean_string($input['code'] ?? '', 80);
        $value = (int)($input['value'] ?? 0);
        if ($code === '' || !in_array($value, [5, 10], true)) {
            json_out(['status' => 'error', 'message' => 'Ma vong quay khong hop le.'], 400);
        }
        insert_compat($pdo, 'qr_coupons', [
            'code' => $code,
            'type' => 'prize',
            'value' => $value,
            'description' => clean_string($input['description'] ?? "Voucher {$value}%", 255),
        ], ['created_at' => 'NOW()']);
        json_out(['status' => 'success', 'message' => 'Da luu prize.']);

    case 'gemini_chat':
        $msg = clean_string($input['message'] ?? '', 1000);
        $reply = $msg === ''
            ? 'Dien Tu Hieu san sang ho tro. Vui long nhap noi dung can tu van.'
            : 'Cam on ban. De xu ly nhanh, vui long goi 0979.553.289 hoac dat yeu cau tren form Goi tho.';
        json_out(['status' => 'success', 'reply' => $reply]);

    case 'generate_qr':
        $count = max(1, min(500, (int)($input['count'] ?? 1)));
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            do {
                $code = generate_code('QR', 4);
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM qr_coupons WHERE code = ?');
                $stmt->execute([$code]);
            } while ((int)$stmt->fetchColumn() > 0);
            insert_compat($pdo, 'qr_coupons', [
                'code' => $code,
                'type' => clean_string($input['type'] ?? 'discount', 30),
                'value' => money_int($input['value'] ?? 0),
                'description' => clean_string($input['description'] ?? '', 500),
            ], ['created_at' => 'NOW()']);
            $codes[] = $code;
        }
        json_out(['status' => 'success', 'codes' => $codes]);

    case 'generate_voucher':
        $count = max(1, min(500, (int)($input['count'] ?? 1)));
        $percent = max(0, min(100, (int)($input['discount_percent'] ?? 0)));
        $amount = money_int($input['discount_amount'] ?? 0);
        $maxUses = max(1, (int)($input['max_uses'] ?? 100));
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            do {
                $code = generate_code('V', 4);
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM vouchers WHERE code = ?');
                $stmt->execute([$code]);
            } while ((int)$stmt->fetchColumn() > 0);
            insert_compat($pdo, 'vouchers', [
                'code' => $code,
                'discount_percent' => $percent,
                'discount_amount' => $amount,
                'type' => $percent > 0 ? 'percent' : 'fixed',
                'value' => $percent > 0 ? $percent : $amount,
                'max_uses' => $maxUses,
                'usage_limit' => $maxUses,
                'used_count' => 0,
                'is_active' => 1,
            ], ['created_at' => 'NOW()']);
            $codes[] = $code;
        }
        json_out(['status' => 'success', 'codes' => $codes]);

    case 'admin_stats':
        json_out(['status' => 'success', 'stats' => admin_stats($pdo)]);

    case 'admin_orders':
        json_out(['status' => 'success', 'data' => admin_orders($pdo)]);

    case 'admin_invoice':
        $orderId = (int)($input['order_id'] ?? $_GET['order_id'] ?? 0);
        $order = get_order_row($pdo, $orderId);
        if (!$order) {
            json_out(['status' => 'error', 'message' => 'Khong tim thay don hang.'], 404);
        }
        $invoiceCode = 'INV-' . date('Ymd') . '-' . str_pad((string)$orderId, 5, '0', STR_PAD_LEFT);
        $pdo->prepare("INSERT IGNORE INTO invoices (invoice_code, order_id, customer_name, customer_phone, product_name, total_price, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())")->execute([
                $invoiceCode,
                $orderId,
                (string)($order['customer_name'] ?? ''),
                (string)($order['customer_phone'] ?? ''),
                (string)($order['product_name'] ?? ''),
                money_int($order['total_price'] ?? $order['total'] ?? 0),
            ]);
        $order['invoice_code'] = $invoiceCode;
        $order['total_price'] = money_int($order['total_price'] ?? $order['total'] ?? 0);
        json_out(['status' => 'success', 'order' => $order]);

    case 'admin_products':
        $target = admin_product_target($pdo);
        $data = $target === 'products'
            ? legacy_products_for_store($pdo, '', '', 500)
            : marketplace_products_for_store($pdo, '', '', 500);
        json_out(['status' => 'success', 'data' => $data, 'source' => $target]);

    case 'admin_save_product':
        $savedId = save_admin_product($pdo, $input);
        json_out(['status' => 'success', 'message' => 'Da luu san pham.', 'id' => $savedId]);

    case 'admin_delete_product':
        $id = (int)($input['id'] ?? 0);
        if ($id <= 0) {
            json_out(['status' => 'error', 'message' => 'ID khong hop le.'], 400);
        }
        if (admin_product_target($pdo) === 'products') {
            $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
            json_out(['status' => 'success', 'message' => 'Da xoa san pham.']);
        }
        if (!table_exists($pdo, 'marketplace_products')) {
            json_out(['status' => 'error', 'message' => 'Khong tim thay bang san pham thuc te.'], 404);
        }
        if (column_exists($pdo, 'marketplace_products', 'status') && column_allows_value($pdo, 'marketplace_products', 'status', 'hidden')) {
            update_compat($pdo, 'marketplace_products', ['status' => 'hidden'], 'id = ?', [$id], ['updated_at' => 'NOW()']);
            json_out(['status' => 'success', 'message' => 'Da an san pham.']);
        }
        $pdo->prepare('DELETE FROM marketplace_products WHERE id = ?')->execute([$id]);
        json_out(['status' => 'success', 'message' => 'Da xoa san pham.']);

    case 'admin_import_excel':
        $rows = is_array($input['data'] ?? null) ? $input['data'] : [];
        $count = 0;
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $name = clean_string($row['Ten_San_Pham'] ?? $row['name'] ?? '', 255);
            if ($name === '') {
                continue;
            }
            save_admin_product($pdo, [
                'name' => $name,
                'price' => money_int($row['Gia_Ban'] ?? $row['price'] ?? 0),
                'stock' => max(1, (int)($row['Ton_Kho'] ?? $row['stock'] ?? 100)),
                'category' => clean_string($row['Danh_Muc'] ?? $row['category'] ?? '', 120),
                'image_url' => clean_string($row['Hinh_Anh'] ?? $row['image_url'] ?? '', 700),
            ]);
            $count++;
        }
        json_out(['status' => 'success', 'message' => "Da import {$count} san pham."]);

    case 'admin_jobs':
        json_out(['status' => 'success', 'data' => admin_jobs($pdo)]);

    case 'admin_test_worker_job':
        $pricing = calculate_job_pricing(150000, 0, 1);
        $jobId = insert_repair_job($pdo, [
            'customer_name' => 'TEST BOT',
            'customer_phone' => clean_string($input['phone'] ?? '0979553289', 30),
            'service_type' => clean_string($input['service_type'] ?? 'Dien lanh - Test', 150),
            'address' => clean_string($input['address'] ?? 'Ap Binh Thanh 1, Lap Vo', 500),
            'description' => clean_string($input['description'] ?? 'Ca test webhook/dispatcher.', 1000),
            'quantity' => 1,
            'customer_total' => $pricing['gross_customer_price'],
            'discount' => $pricing['discount_amount'],
            'final_total' => $pricing['final_customer_price'],
        ]);
        insert_job_pricing($pdo, $jobId, $pricing);
        $sent = send_worker_job_to_group($pdo, $jobId);
        json_out([
            'status' => 'success',
            'message' => $sent ? 'Da gui ca test.' : 'Tao ca test nhung chua gui duoc Telegram.',
            'job_id' => $jobId,
            'platform_fee' => $pricing['platform_fee'],
            'telegram_sent' => $sent,
        ]);

    case 'admin_workers':
        $stmt = $pdo->query("SELECT wp.telegram_user_id AS worker_id, wp.telegram_name, wp.cancel_count, wp.is_receive_blocked, wp.payment_blocked,
            wp.block_reason, wp.jobs_claimed, wp.jobs_completed,
            COALESCE((SELECT COUNT(*) FROM job_posts j WHERE j.worker_id = wp.telegram_user_id), 0) AS job_count,
            COALESCE((SELECT SUM(jp.tech_net_income) FROM job_pricing jp JOIN job_posts j ON j.id = jp.job_id WHERE j.worker_id = wp.telegram_user_id), 0) AS total_earned,
            COALESCE((SELECT SUM(jp.platform_fee) FROM job_pricing jp JOIN job_posts j ON j.id = jp.job_id WHERE j.worker_id = wp.telegram_user_id AND jp.payment_status = 'unpaid'), 0) AS unpaid_fee
            FROM worker_profiles wp ORDER BY wp.updated_at DESC, wp.created_at DESC LIMIT 300");
        json_out(['status' => 'success', 'data' => $stmt->fetchAll()]);

    case 'admin_mark_worker_paid':
        $workerId = (int)($input['worker_id'] ?? 0);
        if ($workerId <= 0) {
            json_out(['status' => 'error', 'message' => 'Worker ID khong hop le.'], 400);
        }
        $stmt = $pdo->prepare("UPDATE job_pricing jp JOIN job_posts j ON j.id = jp.job_id SET jp.payment_status = 'paid' WHERE j.worker_id = ? AND jp.payment_status = 'unpaid'");
        $stmt->execute([$workerId]);
        $pdo->prepare("UPDATE worker_profiles SET payment_blocked = 0, is_receive_blocked = 0, block_reason = NULL, blocked_until = NULL, updated_at = NOW() WHERE telegram_user_id = ?")
            ->execute([$workerId]);
        tg_send('worker', (string)$workerId, 'Admin da ghi nhan thanh toan phi. Ban co the nhan ca lai.');
        json_out(['status' => 'success', 'message' => 'Da ghi nhan thanh toan.', 'updated' => $stmt->rowCount()]);

    case 'admin_unban_worker':
        $workerId = (int)($input['worker_id'] ?? 0);
        if ($workerId <= 0) {
            json_out(['status' => 'error', 'message' => 'Worker ID khong hop le.'], 400);
        }
        $pdo->prepare("INSERT INTO worker_profiles (telegram_user_id, telegram_name, created_at, updated_at)
            VALUES (?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE is_receive_blocked = 0, payment_blocked = 0, blocked_until = NULL, block_reason = NULL, cancel_count = 0, abuse_count = 0, updated_at = NOW()")
            ->execute([$workerId, "Worker {$workerId}"]);
        tg_send('worker', (string)$workerId, 'Admin da mo khoa. Ban co the nhan ca lai.');
        json_out(['status' => 'success', 'message' => 'Da mo khoa tho.']);

    case 'admin_unban_device':
        $identifier = clean_string($input['identifier'] ?? '', 255);
        if ($identifier === '') {
            json_out(['status' => 'error', 'message' => 'Identifier khong hop le.'], 400);
        }
        $pdo->prepare('DELETE FROM banned_devices WHERE identifier = ?')->execute([$identifier]);
        $pdo->prepare('UPDATE client_abuse SET banned_at = NULL WHERE identifier = ?')->execute([$identifier]);
        json_out(['status' => 'success', 'message' => 'Da mo khoa device/IP/phone.']);

    case 'admin_banned_devices':
        $stmt = $pdo->query('SELECT * FROM banned_devices ORDER BY created_at DESC LIMIT 200');
        json_out(['status' => 'success', 'data' => $stmt->fetchAll()]);

    case 'admin_coupons':
        $stmt = $pdo->query('SELECT * FROM qr_coupons ORDER BY id DESC LIMIT 300');
        json_out(['status' => 'success', 'data' => $stmt->fetchAll()]);

    case 'admin_vouchers':
        $stmt = $pdo->query('SELECT * FROM vouchers ORDER BY id DESC LIMIT 300');
        json_out(['status' => 'success', 'data' => $stmt->fetchAll()]);

    default:
        json_out(['status' => 'error', 'message' => "Unknown action: {$action}"], 404);
    }
} catch (Throwable $e) {
    api_exception_out($e);
}
