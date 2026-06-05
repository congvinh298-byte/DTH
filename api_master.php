<?php
declare(strict_types=1);

/*
 * Dien Tu Hieu - THE BRAIN.
 * Pure PHP + PDO + Telegram webhook router.
 *
 * Public actions:
 *   get_products, create_order, create_job, check_voucher, save_wheel_prize,
 *   gemini_chat, telegram_webhook, sepay_webhook, momo_worker_payment, momo_ipn, cron_*
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
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(self)');
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

function signed_money_int($value): int
{
    if (is_numeric($value)) {
        return (int)round((float)$value);
    }
    $raw = trim((string)$value);
    $negative = strpos($raw, '-') !== false;
    $amount = (int)(preg_replace('/[^\d]/', '', $raw) ?: 0);
    return $negative ? -$amount : $amount;
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
        member_rank VARCHAR(50) NOT NULL DEFAULT 'Thanh vien',
        total_spent BIGINT NOT NULL DEFAULT 0,
        loyalty_points INT NOT NULL DEFAULT 0,
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
        map_lat DECIMAL(10,7) NULL,
        map_lng DECIMAL(10,7) NULL,
        description TEXT NULL,
        quantity INT NOT NULL DEFAULT 1,
        customer_total INT NOT NULL DEFAULT 0,
        discount INT NOT NULL DEFAULT 0,
        final_total INT NOT NULL DEFAULT 0,
        worker_id BIGINT NULL,
        telegram_worker_id BIGINT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
        spam_count INT NOT NULL DEFAULT 0,
        cancel_reason TEXT NULL,
        assigned_at DATETIME NULL,
        completed_at DATETIME NULL,
        cancelled_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT NULL,
        review_score INT NULL
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
        paid_amount INT NOT NULL DEFAULT 0,
        tech_net_income INT NOT NULL DEFAULT 0,
        payment_status VARCHAR(30) NOT NULL DEFAULT 'unpaid',
        payment_method VARCHAR(40) NULL,
        payment_reference VARCHAR(150) NULL,
        paid_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_job_pricing_job (job_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS worker_profiles (
        telegram_user_id BIGINT PRIMARY KEY,
        telegram_name VARCHAR(150) NULL,
        telegram_username VARCHAR(150) NULL,
        phone VARCHAR(30) NULL,
        identity_code VARCHAR(100) NULL,
        worker_type VARCHAR(80) NULL DEFAULT 'ho_kinh_doanh',
        role VARCHAR(30) NOT NULL DEFAULT 'worker',
        is_admin TINYINT(1) NOT NULL DEFAULT 0,
        registered_by BIGINT NULL,
        last_seen_bot VARCHAR(30) NULL,
        last_seen_at DATETIME NULL,
        cancel_count INT NOT NULL DEFAULT 0,
        abuse_count INT NOT NULL DEFAULT 0,
        jobs_claimed INT NOT NULL DEFAULT 0,
        jobs_completed INT NOT NULL DEFAULT 0,
        is_receive_blocked TINYINT(1) NOT NULL DEFAULT 0,
        payment_blocked TINYINT(1) NOT NULL DEFAULT 0,
        blocked_until DATETIME NULL,
        block_reason VARCHAR(255) NULL,
        last_fee_notice_at DATETIME NULL,
        total_paid_fee INT NOT NULL DEFAULT 0,
        last_payment_amount INT NOT NULL DEFAULT 0,
        last_payment_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS worker_payments (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        worker_id BIGINT NOT NULL,
        amount INT NOT NULL DEFAULT 0,
        applied_amount INT NOT NULL DEFAULT 0,
        method VARCHAR(40) NOT NULL DEFAULT 'manual',
        reference_code VARCHAR(150) NULL,
        external_transaction_id VARCHAR(150) NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
        note TEXT NULL,
        confirmed_by VARCHAR(150) NULL,
        confirmed_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_worker_payment_external (external_transaction_id),
        INDEX idx_worker_payments_worker (worker_id),
        INDEX idx_worker_payments_status (status)
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
        customer_id INT NULL,
        customer_name VARCHAR(150) NULL,
        customer_phone VARCHAR(30) NULL,
        customer_tax_code VARCHAR(50) NULL,
        customer_address TEXT NULL,
        product_name VARCHAR(255) NULL,
        quantity INT NOT NULL DEFAULT 1,
        unit_gross_amount BIGINT NOT NULL DEFAULT 0,
        gross_before_discount BIGINT NOT NULL DEFAULT 0,
        discount_amount BIGINT NOT NULL DEFAULT 0,
        promo_code VARCHAR(80) NULL,
        gift_name VARCHAR(500) NULL,
        invoice_date DATE NULL,
        subtotal_amount BIGINT NOT NULL DEFAULT 0,
        vat_amount BIGINT NOT NULL DEFAULT 0,
        vat_rate DECIMAL(5,2) NOT NULL DEFAULT 10.00,
        adjustment_amount BIGINT NOT NULL DEFAULT 0,
        total_amount BIGINT NOT NULL DEFAULT 0,
        total_price INT NOT NULL DEFAULT 0,
        company_name VARCHAR(255) NULL,
        company_tax_code VARCHAR(50) NULL,
        company_address TEXT NULL,
        company_phone VARCHAR(50) NULL,
        company_email VARCHAR(190) NULL,
        company_website VARCHAR(255) NULL,
        loyalty_points_earned INT NOT NULL DEFAULT 0,
        payment_method VARCHAR(40) NULL,
        note TEXT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'active',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_invoices_order (order_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS input_invoices (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        invoice_number VARCHAR(120) NOT NULL,
        invoice_series VARCHAR(80) NOT NULL DEFAULT '',
        invoice_date DATE NOT NULL,
        seller_name VARCHAR(255) NOT NULL,
        seller_tax_code VARCHAR(50) NOT NULL,
        subtotal_amount BIGINT NOT NULL DEFAULT 0,
        vat_amount BIGINT NOT NULL DEFAULT 0,
        adjustment_amount BIGINT NOT NULL DEFAULT 0,
        total_amount BIGINT NOT NULL DEFAULT 0,
        currency VARCHAR(10) NOT NULL DEFAULT 'VND',
        pdf_path VARCHAR(500) NOT NULL,
        pdf_original_name VARCHAR(255) NOT NULL,
        pdf_sha256 CHAR(64) NOT NULL,
        pdf_size BIGINT NOT NULL DEFAULT 0,
        status VARCHAR(30) NOT NULL DEFAULT 'active',
        note TEXT NULL,
        uploaded_by VARCHAR(150) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT NULL,
        UNIQUE KEY uniq_input_invoice_document (seller_tax_code, invoice_series, invoice_number),
        UNIQUE KEY uniq_input_invoice_pdf (pdf_sha256),
        INDEX idx_input_invoice_date (invoice_date),
        INDEX idx_input_invoice_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS bct_report_access_log (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(190) NULL,
        auth_mode VARCHAR(30) NULL,
        period_from DATE NULL,
        period_to DATE NULL,
        response_sha256 CHAR(64) NULL,
        client_ip VARCHAR(64) NULL,
        user_agent VARCHAR(500) NULL,
        success TINYINT(1) NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_bct_access_created (created_at),
        INDEX idx_bct_access_user (username)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS marketplace_stores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        phone VARCHAR(30) NOT NULL,
        tax_code VARCHAR(30) NOT NULL,
        owner_name VARCHAR(150) NULL,
        email VARCHAR(190) NULL,
        store_name VARCHAR(150) NOT NULL,
        address TEXT NULL,
        lat DECIMAL(10,7) NULL,
        lng DECIMAL(10,7) NULL,
        store_type VARCHAR(50) NULL,
        note TEXT NULL,
        login_key VARCHAR(128) NULL,
        approved_at DATETIME NULL,
        approved_by VARCHAR(150) NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
        rating_score DECIMAL(3,1) NOT NULL DEFAULT 5.0,
        rating_count INT NOT NULL DEFAULT 0,
        report_token VARCHAR(64) NULL,
        last_login_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT NULL,
        INDEX idx_store_phone (phone),
        INDEX idx_store_tax (tax_code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS marketplace_products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        store_id INT NOT NULL,
        name VARCHAR(150) NOT NULL,
        price INT NOT NULL DEFAULT 0,
        image_url TEXT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'active',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_product_store (store_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS marketplace_orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        store_id INT NOT NULL,
        customer_phone VARCHAR(30) NULL,
        customer_address TEXT NULL,
        total_amount INT NOT NULL DEFAULT 0,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        completed_at DATETIME NULL DEFAULT NULL,
        INDEX idx_order_store (store_id),
        INDEX idx_order_customer (customer_phone),
        INDEX idx_order_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    foreach ([
        'users' => [
            'role' => "VARCHAR(30) NOT NULL DEFAULT 'buyer'",
            'fullname' => 'VARCHAR(150) NOT NULL',
            'phone' => 'VARCHAR(30) NOT NULL',
            'password_hash' => 'VARCHAR(255) NULL',
            'telegram_chat_id' => 'VARCHAR(60) NULL',
            'telegram_username' => 'VARCHAR(150) NULL',
            'login_key' => 'VARCHAR(128) NULL',
            'is_active' => 'TINYINT(1) NOT NULL DEFAULT 1',
            'member_rank' => "VARCHAR(50) NOT NULL DEFAULT 'Thành viên'",
            'total_spent' => 'INT NOT NULL DEFAULT 0',
            'loyalty_points' => 'INT NOT NULL DEFAULT 0',
            'last_login_at' => 'DATETIME NULL',
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
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
            'status' => "VARCHAR(30) NOT NULL DEFAULT 'pending'",
            'payment_method' => "VARCHAR(40) NULL DEFAULT 'cod'",
            'coupon_code' => 'VARCHAR(80) NULL',
            'note' => 'TEXT NULL',
            'confirmed_by' => 'VARCHAR(150) NULL',
            'confirmed_at' => 'DATETIME NULL',
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'job_posts' => [
            'customer_name' => 'VARCHAR(150) NULL',
            'customer_phone' => 'VARCHAR(30) NULL',
            'service_type' => 'VARCHAR(150) NULL',
            'address' => 'TEXT NULL',
            'map_lat' => 'DECIMAL(10,7) NULL',
            'map_lng' => 'DECIMAL(10,7) NULL',
            'description' => 'TEXT NULL',
            'quantity' => 'INT NOT NULL DEFAULT 1',
            'customer_total' => 'INT NOT NULL DEFAULT 0',
            'discount' => 'INT NOT NULL DEFAULT 0',
            'final_total' => 'INT NOT NULL DEFAULT 0',
            'worker_id' => 'BIGINT NULL',
            'telegram_worker_id' => 'BIGINT NULL',
            'status' => "VARCHAR(30) NOT NULL DEFAULT 'pending'",
            'spam_count' => 'INT NOT NULL DEFAULT 0',
            'cancel_reason' => 'TEXT NULL',
            'assigned_at' => 'DATETIME NULL',
            'completed_at' => 'DATETIME NULL',
            'cancelled_at' => 'DATETIME NULL',
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'vouchers' => [
            'discount_percent' => 'INT NOT NULL DEFAULT 0',
            'discount_amount' => 'INT NOT NULL DEFAULT 0',
            'type' => "VARCHAR(30) NULL DEFAULT 'percent'",
            'value' => 'INT NOT NULL DEFAULT 0',
            'max_uses' => 'INT NOT NULL DEFAULT 100',
            'usage_limit' => 'INT NOT NULL DEFAULT 100',
            'used_count' => 'INT NOT NULL DEFAULT 0',
            'is_active' => 'TINYINT(1) NOT NULL DEFAULT 1',
            'expires_at' => 'DATETIME NULL',
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
        ],
        'qr_coupons' => [
            'discount_amount' => 'INT NOT NULL DEFAULT 0',
            'quantity_left' => 'INT NOT NULL DEFAULT 0',
            'type' => "VARCHAR(30) NOT NULL DEFAULT 'discount'",
            'value' => 'INT NOT NULL DEFAULT 0',
            'description' => 'TEXT NULL',
            'is_used' => 'TINYINT(1) NOT NULL DEFAULT 0',
            'used_by' => 'VARCHAR(80) NULL',
            'order_ref' => 'VARCHAR(80) NULL',
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
        ],
        'worker_profiles' => [
            'telegram_name' => 'VARCHAR(150) NULL',
            'telegram_username' => 'VARCHAR(150) NULL',
            'phone' => 'VARCHAR(30) NULL',
            'identity_code' => 'VARCHAR(100) NULL',
            'worker_type' => "VARCHAR(80) NULL DEFAULT 'ho_kinh_doanh'",
            'role' => "VARCHAR(30) NOT NULL DEFAULT 'worker'",
            'is_admin' => 'TINYINT(1) NOT NULL DEFAULT 0',
            'registered_by' => 'BIGINT NULL',
            'last_seen_bot' => 'VARCHAR(30) NULL',
            'last_seen_at' => 'DATETIME NULL',
            'cancel_count' => 'INT NOT NULL DEFAULT 0',
            'abuse_count' => 'INT NOT NULL DEFAULT 0',
            'jobs_claimed' => 'INT NOT NULL DEFAULT 0',
            'jobs_completed' => 'INT NOT NULL DEFAULT 0',
            'is_receive_blocked' => 'TINYINT(1) NOT NULL DEFAULT 0',
            'payment_blocked' => 'TINYINT(1) NOT NULL DEFAULT 0',
            'blocked_until' => 'DATETIME NULL',
            'block_reason' => 'VARCHAR(255) NULL',
            'last_fee_notice_at' => 'DATETIME NULL',
            'total_paid_fee' => 'INT NOT NULL DEFAULT 0',
            'last_payment_amount' => 'INT NOT NULL DEFAULT 0',
            'last_payment_at' => 'DATETIME NULL',
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'job_pricing' => [
            'tech_target_base' => 'INT NOT NULL DEFAULT 0',
            'vat_amount' => 'INT NOT NULL DEFAULT 0',
            'profit_amount' => 'INT NOT NULL DEFAULT 0',
            'gross_customer_price' => 'INT NOT NULL DEFAULT 0',
            'discount_amount' => 'INT NOT NULL DEFAULT 0',
            'final_customer_price' => 'INT NOT NULL DEFAULT 0',
            'platform_fee' => 'INT NOT NULL DEFAULT 0',
            'tech_net_income' => 'INT NOT NULL DEFAULT 0',
            'payment_status' => "VARCHAR(30) NOT NULL DEFAULT 'unpaid'",
            'paid_amount' => 'INT NOT NULL DEFAULT 0',
            'payment_method' => 'VARCHAR(40) NULL',
            'payment_reference' => 'VARCHAR(150) NULL',
            'paid_at' => 'DATETIME NULL',
        ],
        'worker_payments' => [
            'worker_id' => 'BIGINT NOT NULL',
            'amount' => 'INT NOT NULL DEFAULT 0',
            'applied_amount' => 'INT NOT NULL DEFAULT 0',
            'method' => "VARCHAR(40) NOT NULL DEFAULT 'manual'",
            'reference_code' => 'VARCHAR(150) NULL',
            'external_transaction_id' => 'VARCHAR(150) NULL',
            'status' => "VARCHAR(30) NOT NULL DEFAULT 'pending'",
            'note' => 'TEXT NULL',
            'confirmed_by' => 'VARCHAR(150) NULL',
            'confirmed_at' => 'DATETIME NULL',
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
        ],
        'client_abuse' => [
            'identifier' => 'VARCHAR(255) NOT NULL',
            'identifier_type' => 'VARCHAR(30) NOT NULL',
            'request_count' => 'INT NOT NULL DEFAULT 0',
            'fake_count' => 'INT NOT NULL DEFAULT 0',
            'last_job_id' => 'INT NULL',
            'banned_at' => 'DATETIME NULL',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
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
            'customer_id' => 'INT NULL',
            'customer_name' => 'VARCHAR(150) NULL',
            'customer_phone' => 'VARCHAR(30) NULL',
            'customer_tax_code' => 'VARCHAR(50) NULL',
            'customer_address' => 'TEXT NULL',
            'product_name' => 'VARCHAR(255) NULL',
            'quantity' => 'INT NOT NULL DEFAULT 1',
            'unit_gross_amount' => 'BIGINT NOT NULL DEFAULT 0',
            'gross_before_discount' => 'BIGINT NOT NULL DEFAULT 0',
            'discount_amount' => 'BIGINT NOT NULL DEFAULT 0',
            'promo_code' => 'VARCHAR(80) NULL',
            'gift_name' => 'VARCHAR(500) NULL',
            'invoice_date' => 'DATE NULL',
            'subtotal_amount' => 'BIGINT NOT NULL DEFAULT 0',
            'vat_amount' => 'BIGINT NOT NULL DEFAULT 0',
            'vat_rate' => 'DECIMAL(5,2) NOT NULL DEFAULT 10.00',
            'adjustment_amount' => 'BIGINT NOT NULL DEFAULT 0',
            'total_amount' => 'BIGINT NOT NULL DEFAULT 0',
            'total_price' => 'INT NOT NULL DEFAULT 0',
            'company_name' => 'VARCHAR(255) NULL',
            'company_tax_code' => 'VARCHAR(50) NULL',
            'company_address' => 'TEXT NULL',
            'company_phone' => 'VARCHAR(50) NULL',
            'company_email' => 'VARCHAR(190) NULL',
            'company_website' => 'VARCHAR(255) NULL',
            'loyalty_points_earned' => 'INT NOT NULL DEFAULT 0',
            'payment_method' => 'VARCHAR(40) NULL',
            'note' => 'TEXT NULL',
            'status' => "VARCHAR(30) NOT NULL DEFAULT 'active'",
        ],
        'input_invoices' => [
            'invoice_number' => 'VARCHAR(120) NOT NULL',
            'invoice_series' => "VARCHAR(80) NOT NULL DEFAULT ''",
            'invoice_date' => 'DATE NOT NULL',
            'seller_name' => 'VARCHAR(255) NOT NULL',
            'seller_tax_code' => 'VARCHAR(50) NOT NULL',
            'subtotal_amount' => 'BIGINT NOT NULL DEFAULT 0',
            'vat_amount' => 'BIGINT NOT NULL DEFAULT 0',
            'adjustment_amount' => 'BIGINT NOT NULL DEFAULT 0',
            'total_amount' => 'BIGINT NOT NULL DEFAULT 0',
            'currency' => "VARCHAR(10) NOT NULL DEFAULT 'VND'",
            'pdf_path' => 'VARCHAR(500) NOT NULL',
            'pdf_original_name' => 'VARCHAR(255) NOT NULL',
            'pdf_sha256' => 'CHAR(64) NOT NULL',
            'pdf_size' => 'BIGINT NOT NULL DEFAULT 0',
            'status' => "VARCHAR(30) NOT NULL DEFAULT 'active'",
            'note' => 'TEXT NULL',
            'uploaded_by' => 'VARCHAR(150) NULL',
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'bct_report_access_log' => [
            'username' => 'VARCHAR(190) NULL',
            'auth_mode' => 'VARCHAR(30) NULL',
            'period_from' => 'DATE NULL',
            'period_to' => 'DATE NULL',
            'response_sha256' => 'CHAR(64) NULL',
            'client_ip' => 'VARCHAR(64) NULL',
            'user_agent' => 'VARCHAR(500) NULL',
            'success' => 'TINYINT(1) NOT NULL DEFAULT 0',
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
        ],
        'marketplace_stores' => [
            'phone' => 'VARCHAR(30) NOT NULL',
            'tax_code' => 'VARCHAR(30) NOT NULL',
            'owner_name' => 'VARCHAR(150) NULL',
            'email' => 'VARCHAR(190) NULL',
            'store_name' => 'VARCHAR(150) NOT NULL',
            'address' => 'TEXT NULL',
            'lat' => 'DECIMAL(10,7) NULL',
            'lng' => 'DECIMAL(10,7) NULL',
            'store_type' => 'VARCHAR(50) NULL',
            'note' => 'TEXT NULL',
            'login_key' => 'VARCHAR(128) NULL',
            'approved_at' => 'DATETIME NULL',
            'approved_by' => 'VARCHAR(150) NULL',
            'status' => "VARCHAR(30) NOT NULL DEFAULT 'active'",
            'last_login_at' => 'DATETIME NULL',
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
    ] as $table => $columns) {
        foreach ($columns as $column => $definition) {
            add_column_if_missing($pdo, $table, $column, $definition);
        }
    }

    add_index_if_missing($pdo, 'products', 'idx_products_category', '(category)');
    add_index_if_missing($pdo, 'products', 'idx_products_price', '(price)');
    add_index_if_missing($pdo, 'users', 'idx_users_phone', '(phone)');
    add_index_if_missing($pdo, 'users', 'idx_users_login_key', '(login_key)');
    add_index_if_missing($pdo, 'orders', 'idx_orders_customer_phone', '(customer_phone)');
    add_index_if_missing($pdo, 'orders', 'idx_orders_status', '(status)');
    add_index_if_missing($pdo, 'job_posts', 'idx_job_posts_customer_phone', '(customer_phone)');
    add_index_if_missing($pdo, 'job_posts', 'idx_job_posts_status', '(status)');
    add_index_if_missing($pdo, 'job_posts', 'idx_job_posts_worker', '(worker_id)');
    add_index_if_missing($pdo, 'job_posts', 'idx_job_posts_telegram_worker', '(telegram_worker_id)');
    add_index_if_missing($pdo, 'job_posts', 'idx_job_posts_completed', '(completed_at)');
    add_index_if_missing($pdo, 'qr_coupons', 'idx_qr_coupons_code_lookup', '(code)');
    add_index_if_missing($pdo, 'vouchers', 'idx_vouchers_code_lookup', '(code)');
    add_index_if_missing($pdo, 'invoices', 'idx_invoices_customer', '(customer_id)');
    add_index_if_missing($pdo, 'worker_profiles', 'idx_worker_profiles_blocked', '(is_receive_blocked, payment_blocked)');
    add_index_if_missing($pdo, 'worker_profiles', 'idx_worker_profiles_role', '(role, is_admin)');
    add_index_if_missing($pdo, 'worker_payments', 'idx_worker_payments_worker', '(worker_id)');
    add_index_if_missing($pdo, 'worker_payments', 'idx_worker_payments_status', '(status)');
    add_index_if_missing($pdo, 'input_invoices', 'idx_input_invoice_date', '(invoice_date)');
    add_index_if_missing($pdo, 'input_invoices', 'idx_input_invoice_status', '(status)');
    add_index_if_missing($pdo, 'bct_report_access_log', 'idx_bct_access_created', '(created_at)');
    add_index_if_missing($pdo, 'bct_report_access_log', 'idx_bct_access_user', '(username)');
    add_index_if_missing($pdo, 'marketplace_stores', 'idx_store_phone', '(phone)');
    add_index_if_missing($pdo, 'marketplace_stores', 'idx_store_tax', '(tax_code)');
    add_index_if_missing($pdo, 'marketplace_stores', 'idx_store_status', '(status)');
    add_index_if_missing($pdo, 'marketplace_stores', 'idx_store_login_key', '(login_key)');

    $pdo->exec("UPDATE job_pricing SET paid_amount = platform_fee, paid_at = COALESCE(paid_at, created_at)
        WHERE payment_status = 'paid' AND paid_amount = 0");
    try {
        $pdo->exec("UPDATE job_posts j
            JOIN job_claims jc ON jc.id = (
                SELECT MAX(jc2.id) FROM job_claims jc2
                WHERE jc2.job_id = j.id AND jc2.outcome = 'claimed'
            )
            SET j.telegram_worker_id = jc.telegram_user_id
            WHERE j.telegram_worker_id IS NULL");
    } catch (Throwable $e) {
        error_log('[schema] telegram worker backfill skipped: ' . $e->getMessage());
    }

    seed_known_telegram_profiles($pdo);
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

function public_service_catalog(): array
{
    $services = [
        ['group' => 'Thợ điện lạnh', 'name' => 'Vệ sinh máy lạnh', 'base' => 150000, 'note' => 'Giá công khai đã gồm VAT.'],
        ['group' => 'Thợ điện lạnh', 'name' => 'Lắp đặt máy lạnh 1HP / 1.5HP', 'base' => 400000, 'note' => 'Chưa gồm vật tư phát sinh.'],
        ['group' => 'Thợ điện lạnh', 'name' => 'Lắp đặt máy lạnh 2HP / 3HP', 'base' => 500000, 'note' => 'Chưa gồm vật tư phát sinh.'],
        ['group' => 'Thợ điện lạnh', 'name' => 'Máy lạnh âm trần', 'base' => 0, 'note' => 'Báo giá sau khi tư vấn.'],
        ['group' => 'Thợ điện lạnh', 'name' => 'Sửa chữa điện lạnh', 'base' => 200000, 'note' => 'Linh kiện phát sinh được báo riêng.'],
        ['group' => 'Thợ tivi', 'name' => 'Treo tivi', 'base' => 200000, 'note' => 'Chưa gồm khung treo.'],
        ['group' => 'Thợ máy lọc nước', 'name' => 'Lắp máy lọc nước', 'base' => 200000, 'note' => 'Phụ kiện phát sinh được báo riêng.'],
        ['group' => 'Thợ gia dụng', 'name' => 'Lắp máy giặt', 'base' => 200000, 'note' => 'Phụ kiện phát sinh được báo riêng.'],
        ['group' => 'Thợ điện thoại', 'name' => 'Kiểm tra / sửa điện thoại', 'base' => 200000, 'note' => 'Linh kiện phát sinh được báo riêng.'],
    ];

    foreach ($services as $index => &$service) {
        $service['id'] = 'service-' . ($index + 1);
        $service['tech_base'] = (int)$service['base'];
        $service['public_price'] = $service['base'] > 0 ? (int)round($service['base'] * 1.10) : 0;
    }
    unset($service);
    return $services;
}

function service_name_key(string $value): string
{
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    $ascii = $ascii !== false ? $ascii : $value;
    return trim((string)preg_replace('/[^a-z0-9]+/', ' ', strtolower($ascii)));
}

function public_service_by_name(string $name): ?array
{
    $needle = service_name_key(clean_string($name, 150));
    if ($needle === '') {
        return null;
    }
    foreach (public_service_catalog() as $service) {
        if (service_name_key((string)$service['name']) === $needle) {
            return $service;
        }
    }
    return null;
}

function calculate_job_pricing(int $techTargetBase, int $estimatedCustomerPrice, int $quantity = 1): array
{
    $quantity = max(1, $quantity);
    if ($techTargetBase > 0) {
        $techBaseTotal = $techTargetBase * $quantity;
        $vatAmount = (int)round($techBaseTotal * 0.10);
        $profitAmount = (int)round($techBaseTotal * 0.05);
        $grossCustomerPrice = $techBaseTotal + $vatAmount;
    } else {
        $grossCustomerPrice = max(0, $estimatedCustomerPrice);
        $techBaseTotal = (int)round($grossCustomerPrice / 1.10);
        $vatAmount = max(0, $grossCustomerPrice - $techBaseTotal);
        $profitAmount = (int)round($techBaseTotal * 0.05);
    }

    $finalCustomerPrice = $grossCustomerPrice;
    $platformFee = $profitAmount;

    return [
        'tech_target_base' => $techBaseTotal,
        'vat_amount' => $vatAmount,
        'profit_amount' => $profitAmount,
        'gross_customer_price' => $grossCustomerPrice,
        'discount_amount' => 0,
        'discount_roll' => 0,
        'discount_label' => 'Khong ap dung',
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

function admin_telegram_id(): int
{
    return (int)app_env('ADMIN_TELEGRAM_ID', '648065292');
}

function is_admin_telegram_id(int $telegramUserId): bool
{
    return $telegramUserId > 0 && $telegramUserId === admin_telegram_id();
}

function seed_known_telegram_profiles(PDO $pdo)
{
    $adminId = admin_telegram_id();
    if ($adminId > 0) {
        $pdo->prepare("INSERT INTO worker_profiles (telegram_user_id, telegram_name, identity_code, role, is_admin, created_at, updated_at)
            VALUES (?, 'Vinh Tran.2908', 'ADMIN', 'admin', 1, NOW(), NOW())
            ON DUPLICATE KEY UPDATE role = 'admin', is_admin = 1, identity_code = COALESCE(identity_code, 'ADMIN')")
            ->execute([$adminId]);
    }

    $workerId = (int)app_env('INITIAL_WORKER_TELEGRAM_ID', '8729878070');
    if ($workerId > 0 && $workerId !== $adminId) {
        $pdo->prepare("INSERT INTO worker_profiles (telegram_user_id, telegram_name, identity_code, worker_type, role, is_admin, registered_by, created_at, updated_at)
            VALUES (?, ?, ?, 'ho_kinh_doanh', 'worker', 0, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE role = 'worker', is_admin = 0, worker_type = COALESCE(worker_type, 'ho_kinh_doanh')")
            ->execute([$workerId, "Ho kinh doanh {$workerId}", (string)$workerId, $adminId]);
    }
}

function app_public_url(): string
{
    return rtrim(app_env('APP_URL', 'https://dienmayhieu.com'), '/');
}

function worker_payment_code(int $workerId): string
{
    return 'DTHP' . $workerId;
}

function vietqr_payment_url(int $amount, int $workerId): string
{
    $bank = rawurlencode(app_env('VNB_BIN', 'ICB'));
    $account = rawurlencode(app_env('VNB_ACC', ''));
    $holder = rawurlencode(app_env('VNB_HOLDER', 'DIEN TU HIEU'));
    $code = rawurlencode(worker_payment_code($workerId));
    if ($account === '') {
        return app_public_url() . '/QR_THANH_TOAN.jpg';
    }
    return "https://img.vietqr.io/image/{$bank}-{$account}-compact2.jpg?amount={$amount}&addInfo={$code}&accountName={$holder}";
}

function vietqr_bank_deeplink(int $amount, int $workerId): string
{
    $account = app_env('VNB_ACC', '');
    $bank = app_env('VNB_BIN', 'ICB');
    $holder = app_env('VNB_HOLDER', 'DIEN TU HIEU');
    if ($account === '') {
        return vietqr_payment_url($amount, $workerId);
    }
    return 'https://dl.vietqr.io/pay?ba=' . rawurlencode($account . '@' . $bank)
        . '&am=' . $amount
        . '&tn=' . rawurlencode(worker_payment_code($workerId))
        . '&bn=' . rawurlencode($holder);
}

function payment_url_from_template(string $template, int $amount, int $workerId): string
{
    return strtr($template, [
        '{amount}' => (string)$amount,
        '{code}' => rawurlencode(worker_payment_code($workerId)),
        '{worker_id}' => (string)$workerId,
    ]);
}

function momo_payment_configured(): bool
{
    foreach (['MOMO_PARTNER_CODE', 'MOMO_ACCESS_KEY', 'MOMO_SECRET_KEY'] as $key) {
        if (trim(app_env($key, '')) === '') {
            return false;
        }
    }
    return true;
}

function momo_worker_payment_signature(int $workerId): string
{
    return hash_hmac('sha256', 'worker_payment|' . $workerId, app_env('MOMO_SECRET_KEY', ''));
}

function momo_worker_payment_link(int $workerId): string
{
    return app_public_url() . '/api_master.php?action=momo_worker_payment&worker_id=' . $workerId
        . '&token=' . rawurlencode(momo_worker_payment_signature($workerId));
}

function worker_payment_keyboard(int $workerId, int $amount): array
{
    $bankUrl = trim(app_env('BANK_PAYMENT_URL', ''));
    if ($bankUrl === '') {
        $bankUrl = vietqr_bank_deeplink($amount, $workerId);
    } else {
        $bankUrl = payment_url_from_template($bankUrl, $amount, $workerId);
    }
    $row = [['text' => 'Thanh toan ngan hang', 'url' => $bankUrl]];
    $momoUrl = momo_payment_configured() ? momo_worker_payment_link($workerId) : trim(app_env('MOMO_PAYMENT_URL', ''));
    if ($momoUrl !== '') {
        $row[] = [
            'text' => 'Thanh toan MoMo',
            'url' => momo_payment_configured() ? $momoUrl : payment_url_from_template($momoUrl, $amount, $workerId),
        ];
    }
    return [
        'inline_keyboard' => [
            $row,
            [['text' => 'Xem QR chuyen khoan', 'url' => vietqr_payment_url($amount, $workerId)]],
            [['text' => 'Toi da chuyen khoan', 'callback_data' => "paid_notice_{$workerId}"]],
        ],
    ];
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

function tg_send_photo(string $role, string $chatId, string $photoUrl, string $caption, $replyMarkup = null): array
{
    $payload = [
        'chat_id' => $chatId,
        'photo' => $photoUrl,
        'caption' => $caption,
        'parse_mode' => 'HTML',
    ];
    if ($replyMarkup !== null) {
        $payload['reply_markup'] = $replyMarkup;
    }
    return tg_api(telegram_token($role), 'sendPhoto', $payload);
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

function upsert_worker(PDO $pdo, int $telegramUserId, string $name, string $username = '', string $botRole = 'worker')
{
    if ($telegramUserId <= 0) {
        return;
    }
    $isAdmin = is_admin_telegram_id($telegramUserId) ? 1 : 0;
    $role = $isAdmin === 1 ? 'admin' : 'worker';
    $stmt = $pdo->prepare("INSERT INTO worker_profiles (telegram_user_id, telegram_name, telegram_username, role, is_admin, last_seen_bot, last_seen_at, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())
        ON DUPLICATE KEY UPDATE telegram_name = VALUES(telegram_name), telegram_username = VALUES(telegram_username),
            role = IF(is_admin = 1, role, VALUES(role)), is_admin = GREATEST(is_admin, VALUES(is_admin)),
            last_seen_bot = VALUES(last_seen_bot), last_seen_at = NOW(), updated_at = NOW()");
    $stmt->execute([$telegramUserId, $name, $username, $role, $isAdmin, $botRole]);
}

function get_worker_profile(PDO $pdo, int $telegramUserId): array
{
    $stmt = $pdo->prepare('SELECT * FROM worker_profiles WHERE telegram_user_id = ? LIMIT 1');
    $stmt->execute([$telegramUserId]);
    return $stmt->fetch() ?: [];
}

function worker_fee_debt(PDO $pdo, int $workerId): int
{
    if ($workerId <= 0) {
        return 0;
    }
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(GREATEST(jp.platform_fee - COALESCE(jp.paid_amount, 0), 0)), 0)
        FROM job_pricing jp
        JOIN job_posts j ON j.id = jp.job_id
        WHERE COALESCE(j.telegram_worker_id, j.worker_id) = ? AND j.completed_at IS NOT NULL");
    $stmt->execute([$workerId]);
    return max(0, (int)$stmt->fetchColumn());
}

function worker_map_coordinates(array $job): array
{
    $lat = isset($job['map_lat']) && is_numeric($job['map_lat']) ? (float)$job['map_lat'] : null;
    $lng = isset($job['map_lng']) && is_numeric($job['map_lng']) ? (float)$job['map_lng'] : null;
    if ($lat === null || $lng === null) {
        $address = (string)($job['address'] ?? $job['location'] ?? '');
        if (preg_match('/(?:Toa do|Tọa độ)\s*:\s*(-?\d{1,3}(?:\.\d+)?),\s*(-?\d{1,3}(?:\.\d+)?)/iu', $address, $m)) {
            $lat = (float)$m[1];
            $lng = (float)$m[2];
        }
    }
    if ($lat === null || $lng === null || abs($lat) > 90 || abs($lng) > 180) {
        return [];
    }
    return ['lat' => $lat, 'lng' => $lng, 'text' => number_format($lat, 6, '.', '') . ',' . number_format($lng, 6, '.', '')];
}

function worker_google_maps_url(array $job): string
{
    $coords = worker_map_coordinates($job);
    if ($coords === []) {
        return '';
    }
    return 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode($coords['text']);
}

function payment_status_message(int $amount, int $remaining, bool $wasBlocked): string
{
    if ($remaining > 0) {
        return 'Da ghi nhan thanh toan ' . fmt_money($amount) . '. No phi nen tang con lai: ' . fmt_money($remaining) . '.';
    }
    if ($wasBlocked || (int)date('N') >= 2) {
        return 'Da ghi nhan thanh toan phi nen tang ' . fmt_money($amount) . '. Da mo khoa chuc nang nhan ca.';
    }
    return 'Da ghi nhan thanh toan phi nen tang ' . fmt_money($amount) . '. Tai khoan nhan ca hoat dong binh thuong.';
}

function settle_worker_payment(PDO $pdo, int $workerId, int $receivedAmount, string $method, string $reference, string $confirmedBy, string $externalId = ''): array
{
    if ($workerId <= 0 || $receivedAmount <= 0) {
        return ['ok' => false, 'message' => 'Thong tin thanh toan khong hop le.'];
    }
    if ($externalId !== '') {
        $check = $pdo->prepare('SELECT * FROM worker_payments WHERE external_transaction_id = ? LIMIT 1');
        $check->execute([$externalId]);
        $existing = $check->fetch();
        if ($existing) {
            return ['ok' => true, 'message' => 'Giao dich da duoc ghi nhan truoc do.', 'payment_id' => (int)$existing['id'], 'duplicate' => true];
        }
    }

    $before = worker_fee_debt($pdo, $workerId);
    if ($before <= 0) {
        return ['ok' => false, 'message' => 'Tho khong con no phi nen tang.', 'remaining' => 0];
    }
    $profile = get_worker_profile($pdo, $workerId);
    $wasBlocked = (int)($profile['payment_blocked'] ?? 0) === 1;
    $plannedApply = min($before, $receivedAmount);
    $remainingToApply = $plannedApply;
    $appliedActual = 0;

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("SELECT jp.id, jp.platform_fee, COALESCE(jp.paid_amount, 0) paid_amount
            FROM job_pricing jp
            JOIN job_posts j ON j.id = jp.job_id
            WHERE COALESCE(j.telegram_worker_id, j.worker_id) = ? AND j.completed_at IS NOT NULL AND jp.platform_fee > COALESCE(jp.paid_amount, 0)
            ORDER BY j.completed_at ASC, jp.id ASC FOR UPDATE");
        $stmt->execute([$workerId]);
        foreach ($stmt->fetchAll() as $fee) {
            if ($remainingToApply <= 0) {
                break;
            }
            $balance = max(0, (int)$fee['platform_fee'] - (int)$fee['paid_amount']);
            $allocated = min($balance, $remainingToApply);
            $newPaid = (int)$fee['paid_amount'] + $allocated;
            $newStatus = $newPaid >= (int)$fee['platform_fee'] ? 'paid' : 'partial';
            $paidAtExpr = $newStatus === 'paid' ? 'NOW()' : 'paid_at';
            $update = $pdo->prepare("UPDATE job_pricing SET paid_amount = ?, payment_status = ?, payment_method = ?, payment_reference = ?, paid_at = {$paidAtExpr} WHERE id = ?");
            $update->execute([$newPaid, $newStatus, $method, $reference, (int)$fee['id']]);
            $remainingToApply -= $allocated;
            $appliedActual += $allocated;
        }

        $paymentId = insert_compat($pdo, 'worker_payments', [
            'worker_id' => $workerId,
            'amount' => $receivedAmount,
            'applied_amount' => $appliedActual,
            'method' => $method,
            'reference_code' => $reference,
            'external_transaction_id' => $externalId !== '' ? $externalId : null,
            'status' => 'confirmed',
            'note' => $receivedAmount > $appliedActual ? 'Received amount exceeds current platform fee debt or was reconciled concurrently.' : '',
            'confirmed_by' => $confirmedBy,
        ], ['confirmed_at' => 'NOW()', 'created_at' => 'NOW()']);

        $pdo->prepare("UPDATE worker_profiles SET total_paid_fee = total_paid_fee + ?, last_payment_amount = ?,
            last_payment_at = NOW(), updated_at = NOW()
            WHERE telegram_user_id = ?")->execute([$appliedActual, $appliedActual, $workerId]);
        $pdo->prepare("UPDATE worker_payments SET status = 'superseded' WHERE worker_id = ? AND status = 'pending' AND id <> ?")
            ->execute([$workerId, $paymentId]);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    $remaining = worker_fee_debt($pdo, $workerId);
    if ($remaining <= 0) {
        $pdo->prepare("UPDATE worker_profiles SET payment_blocked = 0,
            blocked_until = IF(is_receive_blocked = 0 AND block_reason LIKE 'platform_fee%', NULL, blocked_until),
            block_reason = IF(is_receive_blocked = 0 AND block_reason LIKE 'platform_fee%', NULL, block_reason), updated_at = NOW()
            WHERE telegram_user_id = ?")->execute([$workerId]);
    } elseif ((int)date('N') >= 2) {
        enforce_worker_payment_lock($pdo, $workerId);
    }
    $finalProfile = get_worker_profile($pdo, $workerId);
    if ($appliedActual <= 0) {
        $message = 'Da ghi nhan giao dich ' . fmt_money($receivedAmount) . ' nhung khong con cong no de phan bo. Admin se kiem tra phan tien du.';
    } else {
        $message = $remaining === 0 && worker_is_blocked($finalProfile)
            ? 'Da ghi nhan thanh toan phi nen tang ' . fmt_money($appliedActual) . '. Tai khoan van dang bi khoa vi ly do khac; vui long lien he admin.'
            : payment_status_message($appliedActual, $remaining, $wasBlocked);
    }
    tg_send('worker', (string)$workerId, $message);
    return [
        'ok' => true,
        'message' => $message,
        'payment_id' => $paymentId,
        'received_amount' => $receivedAmount,
        'applied_amount' => $appliedActual,
        'remaining' => $remaining,
    ];
}

function send_worker_debt_notice(PDO $pdo, int $workerId, string $reason = 'Nhac phi nen tang', bool $sendZeroBalance = false): array
{
    $debt = worker_fee_debt($pdo, $workerId);
    if ($debt <= 0 && !$sendZeroBalance) {
        return ['ok' => true, 'message' => 'Khong co cong no.', 'debt' => 0];
    }
    $profile = get_worker_profile($pdo, $workerId);
    $name = (string)($profile['telegram_name'] ?? "Tho {$workerId}");
    if ($debt <= 0) {
        $text = "<b>{$reason}</b>\n"
            . "Tho: " . esc_html($name) . " ({$workerId})\n"
            . "Phi nen tang can nop: <b>0 VND</b>\n"
            . "Ban khong co cong no va van tiep tuc nhan ca binh thuong.";
        $response = tg_send('worker', (string)$workerId, $text);
        if (!empty($response['ok'])) {
            $pdo->prepare('UPDATE worker_profiles SET last_fee_notice_at = NOW(), updated_at = NOW() WHERE telegram_user_id = ?')->execute([$workerId]);
        }
        return ['ok' => !empty($response['ok']), 'message' => !empty($response['ok']) ? 'Da gui thong bao 0 VND.' : 'Khong gui duoc thong bao.', 'debt' => 0];
    }
    $code = worker_payment_code($workerId);
    $caption = "<b>{$reason}</b>\n"
        . "Tho: " . esc_html($name) . " ({$workerId})\n"
        . "Tong phi nen tang con no den hien tai: <b>" . fmt_money($debt) . "</b>\n"
        . "Noi dung chuyen khoan: <code>{$code}</code>\n"
        . "Thanh toan thu 2: tai khoan hoat dong binh thuong. Tu thu 3 neu con no: khoa nhan ca.";
    $response = tg_send_photo('worker', (string)$workerId, vietqr_payment_url($debt, $workerId), $caption, worker_payment_keyboard($workerId, $debt));
    if (empty($response['ok'])) {
        $response = tg_send('worker', (string)$workerId, $caption, worker_payment_keyboard($workerId, $debt));
    }
    if (!empty($response['ok'])) {
        $pdo->prepare('UPDATE worker_profiles SET last_fee_notice_at = NOW(), updated_at = NOW() WHERE telegram_user_id = ?')->execute([$workerId]);
    }
    return ['ok' => !empty($response['ok']), 'message' => !empty($response['ok']) ? 'Da gui nhac phi.' : 'Khong gui duoc nhac phi.', 'debt' => $debt];
}

function notify_all_worker_debts(PDO $pdo, string $reason = 'Nhac phi nen tang'): array
{
    $stmt = $pdo->query("SELECT telegram_user_id FROM worker_profiles WHERE is_admin = 0 AND role = 'worker' ORDER BY telegram_user_id");
    $sent = 0;
    $failed = 0;
    $totalDebt = 0;
    foreach ($stmt->fetchAll() as $row) {
        $workerId = (int)$row['telegram_user_id'];
        $debt = worker_fee_debt($pdo, $workerId);
        $result = send_worker_debt_notice($pdo, $workerId, $reason, true);
        $totalDebt += $debt;
        if ($result['ok']) {
            $sent++;
        } else {
            $failed++;
        }
    }
    return ['sent' => $sent, 'failed' => $failed, 'total_debt' => $totalDebt];
}

function enforce_worker_payment_lock(PDO $pdo, int $workerId): array
{
    $debt = worker_fee_debt($pdo, $workerId);
    if ($debt > 0 && (int)date('N') >= 2) {
        $pdo->prepare("UPDATE worker_profiles SET payment_blocked = 1,
            block_reason = IF(is_receive_blocked = 1, block_reason, ?),
            blocked_until = IF(is_receive_blocked = 1, blocked_until, NULL), updated_at = NOW() WHERE telegram_user_id = ?")
            ->execute(['platform_fee_debt: ' . $debt, $workerId]);
    }
    return get_worker_profile($pdo, $workerId);
}

function lock_all_workers_with_debt(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT telegram_user_id FROM worker_profiles WHERE is_admin = 0 AND role = 'worker'");
    $locked = 0;
    $totalDebt = 0;
    foreach ($stmt->fetchAll() as $row) {
        $workerId = (int)$row['telegram_user_id'];
        $debt = worker_fee_debt($pdo, $workerId);
        if ($debt <= 0) {
            continue;
        }
        $pdo->prepare("UPDATE worker_profiles SET payment_blocked = 1,
            block_reason = IF(is_receive_blocked = 1, block_reason, ?),
            blocked_until = IF(is_receive_blocked = 1, blocked_until, NULL), updated_at = NOW() WHERE telegram_user_id = ?")
            ->execute(['platform_fee_debt: ' . $debt, $workerId]);
        tg_send('worker', (string)$workerId, 'Tai khoan da bi khoa nhan ca do con no phi nen tang ' . fmt_money($debt) . '. Thanh toan xong he thong se mo khoa.');
        send_worker_debt_notice($pdo, $workerId, 'Yeu cau thanh toan de mo khoa nhan ca');
        $locked++;
        $totalDebt += $debt;
    }
    return ['locked' => $locked, 'total_debt' => $totalDebt];
}

function record_worker_payment_notice(PDO $pdo, int $workerId): array
{
    $debt = worker_fee_debt($pdo, $workerId);
    if ($debt <= 0) {
        return ['ok' => true, 'message' => 'He thong khong ghi nhan cong no can thanh toan.'];
    }
    $check = $pdo->prepare("SELECT COUNT(*) FROM worker_payments WHERE worker_id = ? AND status = 'pending' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
    $check->execute([$workerId]);
    if ((int)$check->fetchColumn() > 0) {
        return ['ok' => true, 'message' => 'Yeu cau doi soat da duoc gui. Vui long cho admin hoac SePay xac nhan.'];
    }
    $reference = 'NOTICE-' . $workerId . '-' . date('YmdHis');
    insert_compat($pdo, 'worker_payments', [
        'worker_id' => $workerId,
        'amount' => $debt,
        'applied_amount' => 0,
        'method' => 'worker_notice',
        'reference_code' => $reference,
        'status' => 'pending',
        'note' => 'Worker clicked paid notice; waiting for SePay or admin confirmation.',
    ], ['created_at' => 'NOW()']);
    $profile = get_worker_profile($pdo, $workerId);
    $name = (string)($profile['telegram_name'] ?? "Tho {$workerId}");
    $bossChat = telegram_chat('sales');
    if ($bossChat !== '') {
        tg_send('sales', $bossChat, "<b>THO BAO DA THANH TOAN</b>\nTho: " . esc_html($name) . " ({$workerId})\nCong no dang cho doi soat: <b>" . fmt_money($debt) . "</b>", [
            'inline_keyboard' => [[
                ['text' => 'Xac nhan da thu', 'callback_data' => "confirm_worker_pay_{$workerId}"],
            ]],
        ]);
    }
    return ['ok' => true, 'message' => 'Da bao admin kiem tra giao dich. He thong se mo khoa sau khi xac nhan thanh toan.'];
}

function register_worker_from_admin_command(PDO $pdo, int $senderId, string $text, string $botRole): array
{
    if (!is_admin_telegram_id($senderId)) {
        return ['ok' => false, 'message' => 'Chi admin duoc dung lenh /idtelegram.'];
    }
    $parts = array_map('trim', preg_split('/\|/u', $text) ?: []);
    $workerId = isset($parts[1]) ? (int)digits_only($parts[1]) : 0;
    $phone = isset($parts[2]) ? digits_only($parts[2]) : '';
    $name = clean_string($parts[3] ?? "Ho kinh doanh {$workerId}", 150);
    if ($workerId <= 0 || strlen($phone) < 8) {
        return ['ok' => false, 'message' => 'Dung cu phap: /idtelegram | TELEGRAM_ID | SO_DIEN_THOAI | TEN_THO (ten co the bo trong).'];
    }
    if ($workerId === admin_telegram_id()) {
        return ['ok' => false, 'message' => 'Telegram ID nay la admin, khong dang ky thanh tho.'];
    }
    $stmt = $pdo->prepare("INSERT INTO worker_profiles (telegram_user_id, telegram_name, phone, identity_code, worker_type, role, is_admin, registered_by, last_seen_bot, created_at, updated_at)
        VALUES (?, ?, ?, ?, 'ho_kinh_doanh', 'worker', 0, ?, ?, NOW(), NOW())
        ON DUPLICATE KEY UPDATE telegram_name = VALUES(telegram_name), phone = VALUES(phone), identity_code = VALUES(identity_code),
            worker_type = 'ho_kinh_doanh', role = 'worker', is_admin = 0, registered_by = VALUES(registered_by), last_seen_bot = VALUES(last_seen_bot), updated_at = NOW()");
    $stmt->execute([$workerId, $name, $phone, (string)$workerId, $senderId, $botRole]);
    $count = (int)$pdo->query("SELECT COUNT(*) FROM worker_profiles WHERE is_admin = 0 AND role = 'worker'")->fetchColumn();
    return ['ok' => true, 'message' => "Da dang ky tho {$name}. Telegram ID: {$workerId}. SDT: {$phone}. Tong so tho: {$count}.", 'worker_id' => $workerId, 'worker_count' => $count];
}

function tech_cancel_limit(): int
{
    $limit = (int)app_env('TECH_CANCEL_LIMIT', '3');
    return max(3, min(10, $limit));
}

function worker_is_blocked(array $profile): bool
{
    if ((int)($profile['payment_blocked'] ?? 0) === 1) {
        return true;
    }
    if ((int)($profile['is_receive_blocked'] ?? 0) === 1) {
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
    $device = clean_string((string)($input['device_fingerprint'] ?? $input['fingerprint'] ?? $input['device_id'] ?? ''), 255);
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

function job_worker_telegram_id(array $job): int
{
    $telegramWorkerId = (int)($job['telegram_worker_id'] ?? 0);
    return $telegramWorkerId > 0 ? $telegramWorkerId : (int)($job['worker_id'] ?? 0);
}

function job_assignment_values(PDO $pdo, int $workerId): array
{
    $values = ['telegram_worker_id' => $workerId];
    $workerColumnType = strtolower(column_type($pdo, 'job_posts', 'worker_id'));
    $legacyLimit = strpos($workerColumnType, 'unsigned') !== false ? 4294967295 : 2147483647;
    $workerIdFitsLegacyColumn = $workerId <= $legacyLimit;
    $values['worker_id'] = strpos($workerColumnType, 'bigint') !== false || $workerIdFitsLegacyColumn ? $workerId : null;
    return $values;
}

function job_belongs_to_worker(PDO $pdo, array $job, int $workerId): bool
{
    if ($workerId <= 0) {
        return false;
    }
    if (job_worker_telegram_id($job) === $workerId) {
        return true;
    }
    $stmt = $pdo->prepare("SELECT telegram_user_id FROM job_claims
        WHERE job_id = ? AND outcome = 'claimed' ORDER BY id DESC LIMIT 1");
    $stmt->execute([(int)($job['id'] ?? 0)]);
    if ((int)$stmt->fetchColumn() !== $workerId) {
        return false;
    }
    update_compat($pdo, 'job_posts', job_assignment_values($pdo, $workerId), 'id = ?', [(int)$job['id']]);
    return true;
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
    if (job_worker_telegram_id($job) > 0) {
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
    $mapLat = isset($job['map_lat']) && is_numeric($job['map_lat']) ? (float)$job['map_lat'] : null;
    $mapLng = isset($job['map_lng']) && is_numeric($job['map_lng']) ? (float)$job['map_lng'] : null;
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
        'map_lat' => $mapLat,
        'map_lng' => $mapLng,
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
        'paid_amount' => 0,
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
    $coordinates = worker_map_coordinates($job);
    $mapsUrl = worker_google_maps_url($job);
    $text = "<b>CA GOI THO #{$jobId}</b>\n"
        . "Dich vu: " . esc_html($job['service_type'] ?? '') . "\n"
        . "Dia chi: " . esc_html($job['address'] ?? $job['location'] ?? '') . "\n"
        . ($coordinates !== [] ? "Toa do da xac nhan: <code>" . esc_html($coordinates['text']) . "</code>\n" : "Toa do: chua duoc khach xac nhan tren ban do\n")
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
    if ($mapsUrl !== '') {
        $keyboard['inline_keyboard'][] = [['text' => 'Mo Google Maps den nha khach', 'url' => $mapsUrl]];
    }
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
    upsert_worker($pdo, $workerId, $workerName, $username, 'worker');
    $profile = enforce_worker_payment_lock($pdo, $workerId);
    if (worker_is_blocked($profile)) {
        $debt = worker_fee_debt($pdo, $workerId);
        return ['ok' => false, 'message' => $debt > 0 ? 'Tai khoan dang bi khoa nhan ca. No phi nen tang: ' . fmt_money($debt) . '.' : 'Tai khoan tho dang bi khoa nhan ca. Lien he admin.'];
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

        update_compat($pdo, 'job_posts', job_assignment_values($pdo, $workerId) + [
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
    $coordinates = worker_map_coordinates($job);
    $mapsUrl = worker_google_maps_url($job);
    $dm = "<b>BAN DA NHAN CA #{$jobId}</b>\n"
        . "Khach: " . esc_html($job['customer_name'] ?? '') . "\n"
        . "SDT day du: <b>" . esc_html($job['customer_phone'] ?? '') . "</b>\n"
        . "Dia chi: " . esc_html($job['address'] ?? $job['location'] ?? '') . "\n"
        . ($coordinates !== [] ? "Toa do da xac nhan: <code>" . esc_html($coordinates['text']) . "</code>\n" : '')
        . "Mo ta: " . esc_html($job['description'] ?? '') . "\n"
        . "Tien tho muc tieu: " . fmt_money((int)($pricing['tech_net_income'] ?? 0)) . "\n\n"
        . "Lam xong: REPLY vao tin nhan nay voi chu XONG.\n"
        . "Neu huy ca: REPLY voi chu HUY.";
    $dmKeyboard = [
        'inline_keyboard' => [
            [
                ['text' => 'Da xong', 'callback_data' => "done_job_{$jobId}"],
                ['text' => 'Huy ca', 'callback_data' => "cancel_job_{$jobId}"],
            ],
        ],
    ];
    if ($mapsUrl !== '') {
        $dmKeyboard['inline_keyboard'][] = [['text' => 'Mo Google Maps den nha khach', 'url' => $mapsUrl]];
    }
    $resp = tg_send('worker', (string)$workerId, $dm, $dmKeyboard);
    $messageId = (int)($resp['result']['message_id'] ?? 0);
    if (empty($resp['ok']) || $messageId <= 0) {
        update_compat($pdo, 'job_posts', [
            'worker_id' => null,
            'telegram_worker_id' => null,
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
    if (!$job || !job_belongs_to_worker($pdo, $job, $workerId)) {
        return ['ok' => false, 'message' => 'Ca khong thuoc tho nay.'];
    }
    update_compat($pdo, 'job_posts', [
        'worker_id' => null,
        'telegram_worker_id' => null,
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
    $pdo->beginTransaction();
    try {
        $job = get_job_row($pdo, $jobId, true);
        if (!$job || !job_belongs_to_worker($pdo, $job, $workerId)) {
            $pdo->rollBack();
            return ['ok' => false, 'message' => 'Ca khong thuoc tho nay.'];
        }
        if (job_display_status($job) === 'completed') {
            $pdo->commit();
            return ['ok' => true, 'message' => "Ca #{$jobId} da duoc ghi nhan hoan thanh truoc do."];
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
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    $cumulativeDebt = worker_fee_debt($pdo, $workerId);
    $groupChat = telegram_chat('worker');
    if ($groupChat !== '') {
        tg_send('worker', $groupChat, "Ca #{$jobId} da hoan thanh boi {$workerName}. Phi ca nay: " . fmt_money((int)($pricing['platform_fee'] ?? 0)) . ". Tong no phi den hien tai: " . fmt_money($cumulativeDebt));
    }
    tg_send('worker', (string)$workerId, "<b>PHI NEN TANG CONG DON</b>\n"
        . "Ca vua hoan thanh: #{$jobId}\n"
        . "Phi ca nay: <b>" . fmt_money((int)($pricing['platform_fee'] ?? 0)) . "</b>\n"
        . "Tong phi nen tang den hien tai: <b>" . fmt_money($cumulativeDebt) . "</b>\n"
        . "Thong bao nop phi va QR thanh toan se duoc gui rieng vao 06:00 sang thu 2.");
    return ['ok' => true, 'message' => "Da danh dau ca #{$jobId} hoan thanh. Tong no phi nen tang: " . fmt_money($cumulativeDebt) . '.'];
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

function invoice_company_profile(): array
{
    return [
        'name' => app_env('COMPANY_NAME', app_env('VNB_HOLDER', app_env('BCT_COMPANY_NAME', 'CONG TY TNHH MTV DIEN TU HIEU'))),
        'tax_code' => app_env('COMPANY_TAX_CODE', app_env('BCT_TAX_CODE', '1402228630')),
        'address' => app_env('COMPANY_ADDRESS', '166, Ap Binh Thanh 1, Xa Lap Vo, Tinh Dong Thap'),
        'phone' => app_env('COMPANY_PHONE', '0979.553.289'),
        'email' => app_env('COMPANY_EMAIL', ''),
        'website' => app_env('COMPANY_WEBSITE', app_env('BCT_WEBSITE', app_env('APP_URL', 'https://dienmayhieu.com'))),
    ];
}

function loyalty_points_for_amount(int $amount): int
{
    $vndPerPoint = max(1, (int)app_env('LOYALTY_VND_PER_POINT', '10000'));
    return max(0, (int)floor($amount / $vndPerPoint));
}

function loyalty_member_rank(int $points): string
{
    if ($points >= 1000) {
        return 'Kim cuong';
    }
    if ($points >= 500) {
        return 'Vang';
    }
    if ($points >= 100) {
        return 'Bac';
    }
    return 'Thanh vien';
}

function qr_image_url_for_payload(string $payload, int $size = 180): string
{
    if ($payload === '') {
        return '';
    }
    $safeSize = max(120, min(360, $size));
    return "https://api.qrserver.com/v1/create-qr-code/?size={$safeSize}x{$safeSize}&data=" . rawurlencode($payload);
}

function customer_qr_payload(string $loginKey): string
{
    return 'DTH-CUSTOMER:' . $loginKey;
}

function customer_normalize_login_key(string $value): string
{
    $value = trim($value);
    if (stripos($value, 'DTH-CUSTOMER:') === 0) {
        $value = substr($value, strlen('DTH-CUSTOMER:'));
    }
    return strtoupper(clean_string($value, 128));
}

function customer_generate_login_key(PDO $pdo): string
{
    do {
        $key = 'DTHC-' . strtoupper(bin2hex(random_bytes(8)));
        $stmt = $pdo->prepare('SELECT id FROM users WHERE login_key = ? LIMIT 1');
        $stmt->execute([$key]);
    } while ($stmt->fetch());
    return $key;
}

function retail_customer_row(array $row): array
{
    foreach (['id', 'is_active', 'total_spent', 'loyalty_points'] as $field) {
        $row[$field] = (int)($row[$field] ?? 0);
    }
    $loginKey = (string)($row['login_key'] ?? '');
    $row['login_key'] = $loginKey;
    $row['qr_payload'] = $loginKey !== '' ? customer_qr_payload($loginKey) : '';
    $row['qr_image_url'] = $loginKey !== '' ? qr_image_url_for_payload(customer_qr_payload($loginKey), 160) : '';
    return $row;
}

function retail_customer_by_phone(PDO $pdo, string $phone, bool $lock = false): ?array
{
    $phone = digits_only($phone);
    if (strlen($phone) < 8) {
        return null;
    }
    $suffix = $lock ? ' FOR UPDATE' : '';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE phone = ? ORDER BY id ASC LIMIT 1' . $suffix);
    $stmt->execute([$phone]);
    $row = $stmt->fetch();
    return $row ? retail_customer_row($row) : null;
}

function reward_retail_customer(PDO $pdo, string $fullname, string $phone, int $saleAmount): array
{
    $fullname = clean_string($fullname, 150);
    $phone = digits_only($phone);
    if ($fullname === '' || strlen($phone) < 8) {
        throw new InvalidArgumentException('Ban hang tich diem can ten khach va so dien thoai hop le.');
    }

    $earned = loyalty_points_for_amount($saleAmount);
    $customer = retail_customer_by_phone($pdo, $phone, true);
    if ($customer) {
        $newTotalSpent = (int)$customer['total_spent'] + $saleAmount;
        $newPoints = (int)$customer['loyalty_points'] + $earned;
        $updates = [
            'fullname' => $fullname,
            'is_active' => 1,
            'member_rank' => loyalty_member_rank($newPoints),
            'total_spent' => $newTotalSpent,
            'loyalty_points' => $newPoints,
        ];
        if ((string)($customer['login_key'] ?? '') === '') {
            $updates['login_key'] = customer_generate_login_key($pdo);
        }
        update_compat($pdo, 'users', $updates, 'id = ?', [(int)$customer['id']], ['updated_at' => 'NOW()']);
        $customer = retail_customer_by_phone($pdo, $phone, false) ?: $customer;
    } else {
        $customerId = insert_compat($pdo, 'users', [
            'role' => 'buyer',
            'fullname' => $fullname,
            'phone' => $phone,
            'login_key' => customer_generate_login_key($pdo),
            'is_active' => 1,
            'member_rank' => loyalty_member_rank($earned),
            'total_spent' => $saleAmount,
            'loyalty_points' => $earned,
        ], ['created_at' => 'NOW()']);
        $customer = retail_customer_by_phone($pdo, $phone, false) ?: [
            'id' => $customerId,
            'fullname' => $fullname,
            'phone' => $phone,
            'member_rank' => loyalty_member_rank($earned),
            'total_spent' => $saleAmount,
            'loyalty_points' => $earned,
        ];
    }

    $customer = retail_customer_row($customer);
    $customer['points_earned'] = $earned;
    return $customer;
}

function app_customer_register_action(PDO $pdo, array $input): array
{
    $phone = digits_only((string)($input['phone'] ?? $input['customer_phone'] ?? ''));
    $fullname = clean_string($input['name'] ?? $input['fullname'] ?? $input['customer_name'] ?? '', 150);
    if (strlen($phone) < 8) {
        json_out(['status' => 'error', 'message' => 'Vui long nhap so dien thoai khach hang hop le.'], 400);
    }
    if ($fullname === '') {
        $fullname = 'Khach ' . substr($phone, -4);
    }

    $existing = retail_customer_by_phone($pdo, $phone, false);
    if ($existing) {
        if ((string)($existing['login_key'] ?? '') === '') {
            update_compat($pdo, 'users', [
                'fullname' => $fullname,
                'login_key' => customer_generate_login_key($pdo),
                'is_active' => 1,
            ], 'id = ?', [(int)$existing['id']], ['updated_at' => 'NOW()']);
            $existing = retail_customer_by_phone($pdo, $phone, false) ?: $existing;
            return [
                'status' => 'success',
                'message' => 'So dien thoai nay da co tren he thong. Da cap QR dang nhap rieng cho khach hang.',
                'data' => $existing,
            ];
        }
        json_out([
            'status' => 'error',
            'message' => 'So dien thoai nay da co tai khoan. Vui long dang nhap bang QR da cap.',
            'data' => $existing,
        ], 409);
    }

    $customerId = insert_compat($pdo, 'users', [
        'role' => 'buyer',
        'fullname' => $fullname,
        'phone' => $phone,
        'login_key' => customer_generate_login_key($pdo),
        'is_active' => 1,
        'member_rank' => loyalty_member_rank(0),
        'total_spent' => 0,
        'loyalty_points' => 0,
    ], [
        'created_at' => 'NOW()',
        'updated_at' => 'NOW()',
    ]);

    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$customerId]);
    return [
        'status' => 'success',
        'message' => 'Da cap QR dang nhap rieng cho khach hang. Hay giu QR nay de dang nhap ve sau.',
        'data' => retail_customer_row($stmt->fetch() ?: []),
    ];
}

function app_customer_login_qr_action(PDO $pdo, array $input): array
{
    $loginKey = customer_normalize_login_key((string)($input['login_key'] ?? $input['qr_data'] ?? $input['key'] ?? ''));
    if ($loginKey === '') {
        json_out(['status' => 'error', 'message' => 'Vui long quet QR hoac nhap key khach hang.'], 400);
    }

    $stmt = $pdo->prepare('SELECT * FROM users WHERE login_key = ? LIMIT 1');
    $stmt->execute([$loginKey]);
    $customer = $stmt->fetch();
    if (!$customer) {
        json_out(['status' => 'error', 'message' => 'QR/key khach hang khong hop le.'], 404);
    }
    if ((int)($customer['is_active'] ?? 0) !== 1) {
        json_out(['status' => 'error', 'message' => 'Tai khoan khach hang dang bi tam dung.'], 403);
    }

    update_compat($pdo, 'users', [], 'id = ?', [(int)$customer['id']], [
        'last_login_at' => 'NOW()',
        'updated_at' => 'NOW()',
    ]);

    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([(int)$customer['id']]);
    return [
        'status' => 'success',
        'message' => 'Dang nhap khach hang thanh cong.',
        'data' => retail_customer_row($stmt->fetch() ?: []),
    ];
}

function decrement_retail_stock(PDO $pdo, array $input, int $quantity)
{
    $productId = (int)($input['product_id'] ?? 0);
    $source = clean_string($input['product_source'] ?? '', 30);
    if ($productId <= 0 || $quantity <= 0) {
        return;
    }
    if ($source === 'product' && table_exists($pdo, 'products')) {
        $columns = legacy_product_columns($pdo);
        $stockColumn = $columns['stock'] ?? null;
        if ($stockColumn !== null) {
            $updated = column_exists($pdo, 'products', 'updated_at') ? ', updated_at = NOW()' : '';
            $pdo->prepare('UPDATE products SET ' . db_ident($stockColumn) . ' = GREATEST(' . db_ident($stockColumn) . ' - ?, 0)' . $updated . ' WHERE id = ?')
                ->execute([$quantity, $productId]);
        }
        return;
    }
    if ($source === 'marketplace' && table_exists($pdo, 'marketplace_products') && column_exists($pdo, 'marketplace_products', 'stock')) {
        $updated = column_exists($pdo, 'marketplace_products', 'updated_at') ? ', updated_at = NOW()' : '';
        $pdo->prepare('UPDATE marketplace_products SET stock = GREATEST(stock - ?, 0)' . $updated . ' WHERE id = ?')
            ->execute([$quantity, $productId]);
    }
}

function manual_invoice_discount(PDO $pdo, string $rawCode, int $grossAmount, bool $consume = false): array
{
    $code = strtoupper(clean_string($rawCode, 80));
    if ($code === '') {
        return ['code' => '', 'source' => 'none', 'label' => 'Khong ap ma', 'amount' => 0];
    }

    $suffix = $consume ? ' FOR UPDATE' : '';
    $stmt = $pdo->prepare('SELECT * FROM vouchers WHERE code = ? LIMIT 1' . $suffix);
    $stmt->execute([$code]);
    $voucher = $stmt->fetch();
    if ($voucher) {
        $maxUses = max(0, (int)($voucher['max_uses'] ?? $voucher['usage_limit'] ?? 0));
        $used = max(0, (int)($voucher['used_count'] ?? 0));
        $active = !array_key_exists('is_active', $voucher) || (int)$voucher['is_active'] === 1;
        $expires = (string)($voucher['expires_at'] ?? '');
        if (!$active || $maxUses <= 0 || $used >= $maxUses || ($expires !== '' && strtotime($expires) !== false && strtotime($expires) < time())) {
            throw new DomainException('Ma khuyen mai da het han hoac het luot su dung.');
        }
        $percent = max(0, min(100, (int)($voucher['discount_percent'] ?? 0)));
        $fixed = money_int($voucher['discount_amount'] ?? 0);
        if ($fixed <= 0 && $percent <= 0 && isset($voucher['type'], $voucher['value'])) {
            if ((string)$voucher['type'] === 'percent') {
                $percent = max(0, min(100, (int)$voucher['value']));
            } else {
                $fixed = money_int($voucher['value']);
            }
        }
        $amount = $percent > 0 ? max($fixed, (int)round($grossAmount * $percent / 100)) : $fixed;
        $amount = min($grossAmount, max(0, $amount));
        if ($consume) {
            $pdo->prepare('UPDATE vouchers SET used_count = used_count + 1 WHERE id = ?')->execute([(int)$voucher['id']]);
        }
        return [
            'code' => $code,
            'source' => 'voucher',
            'label' => $percent > 0 ? "Giam {$percent}%" : 'Giam tien truc tiep',
            'amount' => $amount,
        ];
    }

    $stmt = $pdo->prepare('SELECT * FROM qr_coupons WHERE code = ? LIMIT 1' . $suffix);
    $stmt->execute([$code]);
    $coupon = $stmt->fetch();
    if (!$coupon) {
        throw new DomainException('Khong tim thay ma khuyen mai.');
    }
    $quantityLeft = (int)($coupon['quantity_left'] ?? 0);
    if ((int)($coupon['is_used'] ?? 0) === 1 || $quantityLeft <= 0) {
        throw new DomainException('Ma khuyen mai da duoc su dung hoac het luot.');
    }
    $type = strtolower((string)($coupon['type'] ?? 'discount'));
    $value = money_int($coupon['discount_amount'] ?? $coupon['value'] ?? 0);
    $percent = in_array($type, ['percent', 'prize'], true) ? max(0, min(100, (int)($coupon['value'] ?? 0))) : 0;
    $amount = $percent > 0 ? (int)round($grossAmount * $percent / 100) : $value;
    $amount = min($grossAmount, max(0, $amount));
    if ($consume) {
        $pdo->prepare('UPDATE qr_coupons
            SET is_used = IF(quantity_left <= 1, 1, is_used), quantity_left = GREATEST(quantity_left - 1, 0), used_by = ?, order_ref = ?
            WHERE id = ?')->execute(['admin_invoice', 'manual_invoice', (int)$coupon['id']]);
    }
    return [
        'code' => $code,
        'source' => 'promo',
        'label' => $percent > 0 ? "Giam {$percent}%" : 'Giam tien truc tiep',
        'amount' => $amount,
    ];
}

function manual_invoice_calculation(PDO $pdo, array $input, bool $consumeDiscount = false): array
{
    $quantity = max(1, min(10000, (int)($input['quantity'] ?? 1)));
    $unitGross = money_int($input['unit_gross_amount'] ?? $input['gross_amount'] ?? 0);
    if ($unitGross <= 0) {
        throw new InvalidArgumentException('Gia da gom VAT phai lon hon 0.');
    }
    $grossBeforeDiscount = $unitGross * $quantity;
    $discount = manual_invoice_discount($pdo, (string)($input['promo_code'] ?? ''), $grossBeforeDiscount, $consumeDiscount);
    $total = max(0, $grossBeforeDiscount - (int)$discount['amount']);
    $subtotal = (int)round($total * 100 / 110);
    $vat = $total - $subtotal;
    return [
        'quantity' => $quantity,
        'unit_gross_amount' => $unitGross,
        'gross_before_discount' => $grossBeforeDiscount,
        'discount' => $discount,
        'discount_amount' => (int)$discount['amount'],
        'subtotal_amount' => $subtotal,
        'vat_rate' => 10,
        'vat_amount' => $vat,
        'total_amount' => $total,
        'loyalty_points_earned' => loyalty_points_for_amount($total),
    ];
}

function sales_invoice_row(array $row): array
{
    foreach ([
        'id', 'order_id', 'customer_id', 'quantity', 'unit_gross_amount', 'gross_before_discount', 'discount_amount',
        'subtotal_amount', 'vat_amount', 'adjustment_amount', 'total_amount', 'total_price', 'loyalty_points_earned',
        'customer_loyalty_points', 'customer_total_spent',
    ] as $field) {
        $row[$field] = (int)($row[$field] ?? 0);
    }
    $row['vat_rate'] = (float)($row['vat_rate'] ?? 10);
    $profile = invoice_company_profile();
    foreach ($profile as $key => $value) {
        $field = 'company_' . $key;
        if (empty($row[$field])) {
            $row[$field] = $value;
        }
    }
    return $row;
}

function admin_sales_invoice_rows(PDO $pdo, int $limit = 300): array
{
    $limit = max(1, min(1000, $limit));
    $stmt = $pdo->query("SELECT i.*, u.loyalty_points AS customer_loyalty_points, u.total_spent AS customer_total_spent,
        u.member_rank AS customer_member_rank
        FROM invoices i LEFT JOIN users u ON u.id = i.customer_id
        WHERE i.status = 'active' ORDER BY i.id DESC LIMIT {$limit}");
    return array_map('sales_invoice_row', $stmt->fetchAll());
}

function create_manual_sales_invoice(PDO $pdo, array $input, bool $rewardCustomer = false): array
{
    $productName = clean_string($input['product_name'] ?? '', 255);
    if ($productName === '') {
        throw new InvalidArgumentException('Ten hang hoa khong duoc de trong.');
    }
    $customerName = clean_string($input['customer_name'] ?? ($rewardCustomer ? '' : 'Khach le'), 150);
    $customerPhone = digits_only((string)($input['customer_phone'] ?? ''));
    $customerTaxCode = clean_string($input['customer_tax_code'] ?? '', 50);
    $customerAddress = clean_string($input['customer_address'] ?? '', 1000);
    $giftName = clean_string($input['gift_name'] ?? '', 500);
    $note = clean_string($input['note'] ?? '', 2000);
    $paymentMethod = clean_string($input['payment_method'] ?? 'cash', 40);
    $profile = invoice_company_profile();
    $customer = null;
    $orderId = null;

    $pdo->beginTransaction();
    try {
        $calculation = manual_invoice_calculation($pdo, $input, true);
        if ($rewardCustomer) {
            $customer = reward_retail_customer($pdo, $customerName, $customerPhone, (int)$calculation['total_amount']);
            $orderId = insert_compat($pdo, 'orders', [
                'order_code' => next_order_code(),
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'product_id' => (int)($input['product_id'] ?? 0),
                'product_name' => $productName,
                'total_price' => $calculation['total_amount'],
                'total' => $calculation['total_amount'],
                'subtotal' => $calculation['gross_before_discount'],
                'discount' => $calculation['discount_amount'],
                'status' => order_status($pdo, 'confirmed'),
                'payment_method' => $paymentMethod,
                'payment_status' => 'paid',
                'coupon_code' => $calculation['discount']['code'],
                'voucher_code' => $calculation['discount']['code'],
                'note' => $note,
                'confirmed_by' => 'admin_pos',
            ], ['created_at' => 'NOW()', 'confirmed_at' => 'NOW()']);
            if (table_exists($pdo, 'order_items')) {
                insert_compat($pdo, 'order_items', [
                    'order_id' => $orderId,
                    'product_id' => (int)($input['product_id'] ?? 0),
                    'product_name' => $productName,
                    'product_type' => clean_string($input['product_source'] ?? 'input', 30),
                    'quantity' => $calculation['quantity'],
                    'price' => $calculation['unit_gross_amount'],
                    'subtotal' => $calculation['gross_before_discount'],
                ], ['created_at' => 'NOW()']);
            }
            decrement_retail_stock($pdo, $input, (int)$calculation['quantity']);
        }
        $invoiceCode = 'HD-' . date('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(4)));
        $invoiceId = insert_compat($pdo, 'invoices', [
            'invoice_code' => $invoiceCode,
            'order_id' => $orderId,
            'customer_id' => $customer['id'] ?? null,
            'customer_name' => $customerName !== '' ? $customerName : 'Khach le',
            'customer_phone' => $customerPhone,
            'customer_tax_code' => $customerTaxCode,
            'customer_address' => $customerAddress,
            'product_name' => $productName,
            'quantity' => $calculation['quantity'],
            'unit_gross_amount' => $calculation['unit_gross_amount'],
            'gross_before_discount' => $calculation['gross_before_discount'],
            'discount_amount' => $calculation['discount_amount'],
            'promo_code' => $calculation['discount']['code'],
            'gift_name' => $giftName,
            'invoice_date' => date('Y-m-d'),
            'subtotal_amount' => $calculation['subtotal_amount'],
            'vat_amount' => $calculation['vat_amount'],
            'vat_rate' => 10,
            'adjustment_amount' => 0,
            'total_amount' => $calculation['total_amount'],
            'total_price' => $calculation['total_amount'],
            'company_name' => $profile['name'],
            'company_tax_code' => $profile['tax_code'],
            'company_address' => $profile['address'],
            'company_phone' => $profile['phone'],
            'company_email' => $profile['email'],
            'company_website' => $profile['website'],
            'loyalty_points_earned' => $rewardCustomer ? $calculation['loyalty_points_earned'] : 0,
            'payment_method' => $paymentMethod,
            'note' => $note,
            'status' => 'active',
        ], ['created_at' => 'NOW()']);
        insert_compat($pdo, 'finances', [
            'type' => 'sales_invoice',
            'amount' => $calculation['total_amount'],
            'source_type' => 'invoice',
            'source_id' => $invoiceId,
            'note' => ($rewardCustomer ? 'Retail POS sale invoice ' : 'Manual sales invoice ') . $invoiceCode,
        ], ['created_at' => 'NOW()']);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    $stmt = $pdo->prepare('SELECT * FROM invoices WHERE id = ? LIMIT 1');
    $stmt->execute([$invoiceId]);
    $invoice = sales_invoice_row($stmt->fetch() ?: ['id' => $invoiceId]);
    if ($customer) {
        $invoice['customer_loyalty_points'] = (int)($customer['loyalty_points'] ?? 0);
        $invoice['customer_total_spent'] = (int)($customer['total_spent'] ?? 0);
        $invoice['customer_member_rank'] = (string)($customer['member_rank'] ?? 'Thanh vien');
    }
    return $invoice;
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
        if ($workerId > 0) {
            upsert_worker($pdo, $workerId, $name, $username, 'worker');
        }
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
        if (preg_match('/^paid_notice_(\d+)$/', $data, $m)) {
            if ((int)$m[1] !== $workerId) {
                $r = ['ok' => false, 'message' => 'Nut thanh toan nay khong thuoc tai khoan cua ban.'];
            } else {
                $r = record_worker_payment_notice($pdo, $workerId);
            }
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
        upsert_worker($pdo, $workerId, $name, $username, 'worker');
    }

    if (dth_starts_with(strtolower($text), '/idtelegram')) {
        $result = register_worker_from_admin_command($pdo, $workerId, $text, 'worker');
        tg_send('worker', (string)$workerId, $result['message']);
        return $result;
    }

    if (dth_starts_with($text, '/start')) {
        $message = is_admin_telegram_id($workerId)
            ? "Bot Anh Thien 1 da nhan dien admin {$workerId}. Lenh dang ky tho: /idtelegram | TELEGRAM_ID | SO_DIEN_THOAI | TEN_THO"
            : "Bot Anh Thien 1 da ket noi. Reply vao tin ca trong nhom de nhan viec. Khi bot DM, reply XONG de ket thuc ca.";
        tg_send('worker', (string)$workerId, $message);
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
        $senderId = (int)($from['id'] ?? 0);
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
        if (preg_match('/^confirm_worker_pay_(\d+)$/', $data, $m)) {
            if (!is_admin_telegram_id($senderId)) {
                $r = ['ok' => false, 'message' => 'Chi admin duoc xac nhan thanh toan.'];
            } else {
                $workerId = (int)$m[1];
                $debt = worker_fee_debt($pdo, $workerId);
                $r = $debt > 0
                    ? settle_worker_payment($pdo, $workerId, $debt, 'telegram_admin', 'ADMIN-' . date('YmdHis'), (string)$senderId)
                    : ['ok' => true, 'message' => 'Tho khong con no phi nen tang.'];
            }
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
    $senderId = (int)($from['id'] ?? 0);
    $name = worker_name($from);
    $username = (string)($from['username'] ?? '');
    $knownProfile = $senderId > 0 ? get_worker_profile($pdo, $senderId) : [];
    if ($senderId > 0 && (is_admin_telegram_id($senderId) || $knownProfile !== [])) {
        upsert_worker($pdo, $senderId, $name, $username, 'sales');
    }
    if (dth_starts_with(strtolower($text), '/idtelegram')) {
        $result = register_worker_from_admin_command($pdo, $senderId, $text, 'sales');
        tg_send('sales', (string)$senderId, $result['message']);
        return $result;
    }
    if (dth_starts_with($text, '/start')) {
        if (is_admin_telegram_id($senderId)) {
            $message = "Bot Anh Thien 2 da nhan dien admin {$senderId}. Lenh dang ky tho: /idtelegram | TELEGRAM_ID | SO_DIEN_THOAI | TEN_THO";
        } elseif ($knownProfile !== []) {
            $message = "Bot Anh Thien 2 da nhan dien tho {$senderId}. Ho so cua ban se duoc tong hop tren Dashboard.";
        } else {
            $message = 'Bot Anh Thien 2 da ket noi. Admin can dang ky Telegram ID cua ban truoc khi tong hop vao Dashboard.';
        }
        tg_send('sales', (string)$senderId, $message);
        return ['ok' => true, 'message' => 'started'];
    }
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

function verify_cron_secret()
{
    $expected = app_env('CRON_SECRET', '');
    $actual = (string)($_GET['secret'] ?? $_SERVER['HTTP_X_CRON_SECRET'] ?? '');
    if ($expected === '' || !hash_equals($expected, $actual)) {
        json_out(['status' => 'error', 'message' => 'Invalid cron secret.'], 403);
    }
}

function verify_sepay_webhook()
{
    $expected = app_env('SEPAY_API_KEY', '');
    $authorization = trim((string)($_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? ''));
    if ($expected === '' || (!hash_equals('Apikey ' . $expected, $authorization) && !hash_equals('Bearer ' . $expected, $authorization))) {
        json_out(['success' => false, 'message' => 'Invalid SePay authorization.'], 401);
    }
}

function momo_ipn_signature(array $payload): string
{
    $raw = 'accessKey=' . app_env('MOMO_ACCESS_KEY', '');
    foreach (['amount', 'extraData', 'message', 'orderId', 'orderInfo', 'orderType', 'partnerCode', 'payType', 'requestId', 'responseTime', 'resultCode', 'transId'] as $key) {
        $raw .= '&' . $key . '=' . (string)($payload[$key] ?? '');
    }
    return hash_hmac('sha256', $raw, app_env('MOMO_SECRET_KEY', ''));
}

function handle_momo_worker_payment()
{
    if (!momo_payment_configured()) {
        json_out(['status' => 'error', 'message' => 'MoMo merchant chua duoc cau hinh.'], 503);
    }
    $workerId = (int)digits_only($_GET['worker_id'] ?? '');
    $token = clean_string($_GET['token'] ?? '', 200);
    if ($workerId <= 0 || $token === '' || !hash_equals(momo_worker_payment_signature($workerId), $token)) {
        json_out(['status' => 'error', 'message' => 'Lien ket thanh toan MoMo khong hop le.'], 403);
    }

    $pdo = pdo();
    $amount = worker_fee_debt($pdo, $workerId);
    if ($amount <= 0) {
        json_out(['status' => 'success', 'message' => 'Tho khong con no phi nen tang.']);
    }

    $partnerCode = trim(app_env('MOMO_PARTNER_CODE', ''));
    $accessKey = trim(app_env('MOMO_ACCESS_KEY', ''));
    $secretKey = app_env('MOMO_SECRET_KEY', '');
    $requestType = trim(app_env('MOMO_REQUEST_TYPE', 'captureWallet')) ?: 'captureWallet';
    $endpoint = trim(app_env('MOMO_CREATE_ENDPOINT', 'https://payment.momo.vn/v2/gateway/api/create'));
    $orderId = worker_payment_code($workerId) . '-' . date('YmdHis') . '-' . random_int(100, 999);
    $requestId = $orderId;
    $orderInfo = 'Phi nen tang ' . worker_payment_code($workerId);
    $redirectUrl = trim(app_env('MOMO_REDIRECT_URL', app_public_url() . '/?payment=momo'));
    $ipnUrl = app_public_url() . '/api_master.php?action=momo_ipn';
    $extraData = base64_encode(json_encode(['worker_id' => $workerId], JSON_UNESCAPED_SLASHES) ?: '{}');
    $rawSignature = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}&ipnUrl={$ipnUrl}&orderId={$orderId}"
        . "&orderInfo={$orderInfo}&partnerCode={$partnerCode}&redirectUrl={$redirectUrl}&requestId={$requestId}&requestType={$requestType}";
    $payload = [
        'partnerCode' => $partnerCode,
        'requestType' => $requestType,
        'ipnUrl' => $ipnUrl,
        'redirectUrl' => $redirectUrl,
        'orderId' => $orderId,
        'amount' => $amount,
        'orderInfo' => $orderInfo,
        'requestId' => $requestId,
        'extraData' => $extraData,
        'lang' => 'vi',
        'autoCapture' => true,
        'signature' => hash_hmac('sha256', $rawSignature, $secretKey),
    ];

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 35,
    ]);
    $raw = curl_exec($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    $response = json_decode((string)$raw, true);
    $payUrl = is_array($response) ? (string)($response['payUrl'] ?? '') : '';
    $payHost = strtolower((string)parse_url($payUrl, PHP_URL_HOST));
    if ($http !== 200 || (int)($response['resultCode'] ?? -1) !== 0 || !filter_var($payUrl, FILTER_VALIDATE_URL)
        || !preg_match('/(^|\.)momo\.vn$/i', $payHost)) {
        error_log('[momo_create] HTTP=' . $http . ' ' . ($error !== '' ? $error : substr((string)$raw, 0, 500)));
        json_out(['status' => 'error', 'message' => 'Khong tao duoc lien ket MoMo luc nay.'], 502);
    }

    insert_compat($pdo, 'worker_payments', [
        'worker_id' => $workerId,
        'amount' => $amount,
        'applied_amount' => 0,
        'method' => 'momo',
        'reference_code' => $orderId,
        'status' => 'pending',
        'note' => 'MoMo payment link created; waiting for verified IPN.',
    ], ['created_at' => 'NOW()']);

    header('Location: ' . $payUrl, true, 302);
    exit;
}

function handle_momo_ipn()
{
    if (!momo_payment_configured()) {
        json_out(['status' => 'error', 'message' => 'MoMo merchant chua duoc cau hinh.'], 503);
    }
    $payload = request_data();
    $partnerCode = (string)($payload['partnerCode'] ?? '');
    $signature = strtolower((string)($payload['signature'] ?? ''));
    if ($partnerCode !== app_env('MOMO_PARTNER_CODE', '') || $signature === ''
        || !hash_equals(momo_ipn_signature($payload), $signature)) {
        json_out(['status' => 'error', 'message' => 'MoMo IPN signature khong hop le.'], 401);
    }
    $orderId = clean_string($payload['orderId'] ?? '', 150);
    $pdo = pdo();
    if ((int)($payload['resultCode'] ?? -1) !== 0) {
        $message = clean_string($payload['message'] ?? 'MoMo payment failed.', 500);
        $pdo->prepare("UPDATE worker_payments SET status = 'failed', note = ?, confirmed_at = NOW()
            WHERE reference_code = ? AND method = 'momo' AND status = 'pending'")
            ->execute([$message, $orderId]);
        http_response_code(204);
        exit;
    }

    if (!preg_match('/^DTHP(\d{5,20})-/', $orderId, $match)) {
        json_out(['status' => 'error', 'message' => 'Khong tim thay ma tho trong giao dich MoMo.'], 400);
    }
    $workerId = (int)$match[1];
    $amount = money_int($payload['amount'] ?? 0);
    $pending = $pdo->prepare("SELECT * FROM worker_payments WHERE worker_id = ? AND reference_code = ? AND method = 'momo' ORDER BY id DESC LIMIT 1");
    $pending->execute([$workerId, $orderId]);
    $payment = $pending->fetch();
    if (!$payment || (int)$payment['amount'] !== $amount) {
        json_out(['status' => 'error', 'message' => 'Giao dich MoMo khong khop yeu cau dang cho.'], 409);
    }

    $transId = clean_string($payload['transId'] ?? '', 120);
    if ($transId === '') {
        json_out(['status' => 'error', 'message' => 'MoMo IPN thieu ma giao dich.'], 400);
    }
    settle_worker_payment($pdo, $workerId, $amount, 'momo', $orderId, 'momo_ipn', 'MOMO-' . $transId);
    http_response_code(204);
    exit;
}

function handle_sepay_webhook()
{
    verify_sepay_webhook();
    $pdo = pdo();
    $payload = request_data();
    $transferType = strtolower(clean_string($payload['transferType'] ?? $payload['transfer_type'] ?? '', 30));
    if ($transferType !== '' && !in_array($transferType, ['in', 'credit', 'incoming'], true)) {
        json_out(['success' => true, 'message' => 'Outgoing transaction ignored.']);
    }
    $content = clean_string($payload['content'] ?? $payload['description'] ?? $payload['transaction_content'] ?? '', 1000);
    if (!preg_match('/DTHP\s*(\d{5,20})/i', $content, $match)) {
        json_out(['success' => true, 'message' => 'Payment code not found; transaction ignored.']);
    }
    $workerId = (int)$match[1];
    $amount = money_int($payload['transferAmount'] ?? $payload['transfer_amount'] ?? $payload['amount'] ?? 0);
    $transactionId = clean_string($payload['id'] ?? $payload['transaction_id'] ?? $payload['referenceCode'] ?? '', 120);
    $reference = clean_string($payload['referenceCode'] ?? $payload['reference_code'] ?? worker_payment_code($workerId), 150);
    $externalId = $transactionId !== '' ? 'SEPAY-' . $transactionId : 'SEPAY-' . hash('sha256', $content . '|' . $amount . '|' . ($payload['transactionDate'] ?? ''));
    $result = settle_worker_payment($pdo, $workerId, $amount, 'sepay', $reference, 'sepay_webhook', $externalId);
    json_out(['success' => true, 'result' => $result]);
}

function send_daily_business_report(PDO $pdo): array
{
    $stats = admin_stats($pdo);
    $paidToday = 0;
    $platformFeeToday = 0;
    if (table_exists($pdo, 'worker_payments')) {
        $paidToday = (int)$pdo->query("SELECT COALESCE(SUM(applied_amount),0) FROM worker_payments WHERE status = 'confirmed' AND DATE(confirmed_at) = CURDATE()")->fetchColumn();
    }
    if (table_exists($pdo, 'job_pricing') && table_exists($pdo, 'job_posts')) {
        $platformFeeToday = (int)$pdo->query("SELECT COALESCE(SUM(jp.platform_fee),0)
            FROM job_pricing jp JOIN job_posts j ON j.id = jp.job_id
            WHERE DATE(j.completed_at) = CURDATE()")->fetchColumn();
    }
    $text = "<b>BAO CAO NGAY " . date('d/m/Y') . "</b>\n"
        . "Don hang: " . (int)$stats['today_orders'] . "\n"
        . "Doanh thu don hang: " . fmt_money((int)$stats['today_revenue']) . "\n"
        . "Ca goi tho hom nay: " . (int)$stats['today_jobs'] . "\n"
        . "Tong tho: " . (int)($stats['total_workers'] ?? 0) . "\n"
        . "Phi nen tang phat sinh hom nay: " . fmt_money($platformFeeToday) . "\n"
        . "Phi nen tang da thu hom nay: " . fmt_money($paidToday) . "\n"
        . "Tong no phi nen tang: " . fmt_money((int)$stats['unpaid_total']);
    $chatId = telegram_chat('sales');
    $response = $chatId !== '' ? tg_send('sales', $chatId, $text) : ['ok' => false];
    return ['sent' => !empty($response['ok']), 'stats' => $stats, 'platform_fee_today' => $platformFeeToday, 'paid_today' => $paidToday];
}

function distance_km(float $lat1, float $lng1, float $lat2, float $lng2): float
{
    $earthRadiusKm = 6371;
    $latDelta = deg2rad($lat2 - $lat1);
    $lngDelta = deg2rad($lng2 - $lng1);
    $a = sin($latDelta / 2) ** 2
        + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lngDelta / 2) ** 2;
    $a = min(1, max(0, $a));
    return $earthRadiusKm * 2 * atan2(sqrt($a), sqrt(1 - $a));
}

function service_area_distance_km(float $lat, float $lng): float
{
    $centerLat = (float)app_env('SERVICE_CENTER_LAT', '10.357422');
    $centerLng = (float)app_env('SERVICE_CENTER_LNG', '105.522124');
    return distance_km($centerLat, $centerLng, $lat, $lng);
}

function create_job_action(array $input): array
{
    $pdo = pdo();
    $phone = digits_only((string)($input['phone'] ?? $input['sdt'] ?? $input['customer_phone'] ?? ''));
    $address = clean_string((string)($input['address'] ?? $input['dia_chi'] ?? ''), 1000);
    $mapLocation = clean_string((string)($input['map_location'] ?? ''), 80);
    $mapLat = isset($input['map_lat']) && is_numeric($input['map_lat']) ? (float)$input['map_lat'] : null;
    $mapLng = isset($input['map_lng']) && is_numeric($input['map_lng']) ? (float)$input['map_lng'] : null;
    if (($mapLat === null || $mapLng === null) && preg_match('/^(-?\d{1,3}(?:\.\d+)?),(-?\d{1,3}(?:\.\d+)?)$/', $mapLocation, $coords)) {
        $mapLat = (float)$coords[1];
        $mapLng = (float)$coords[2];
    }
    if ($mapLat !== null && $mapLng !== null && (abs($mapLat) > 90 || abs($mapLng) > 180)) {
        $mapLat = null;
        $mapLng = null;
    }
    if ($mapLocation !== '' && preg_match('/^-?\d{1,3}(?:\.\d+)?,-?\d{1,3}(?:\.\d+)?$/', $mapLocation) && strpos($address, $mapLocation) === false) {
        $address = clean_string($address . ' | Tọa độ: ' . $mapLocation, 1000);
    }
    $description = clean_string((string)($input['issue_description'] ?? $input['mo_ta'] ?? $input['description'] ?? ''), 3000);
    $serviceType = clean_string((string)($input['service_type'] ?? $input['loai_tho'] ?? 'Dich vu dien lanh'), 150);
    $customerName = clean_string((string)($input['customer_name'] ?? $input['name'] ?? 'Khach'), 150);
    $quantity = max(1, (int)($input['quantity'] ?? $input['qty'] ?? 1));
    $techBase = money_int($input['tech_target_base'] ?? $input['tech_base'] ?? 0);
    $estimated = money_int($input['estimated_price'] ?? $input['customer_price'] ?? $input['final_total'] ?? 0);
    $selectedService = public_service_by_name((string)($input['selected_service_name'] ?? $input['selected_service'] ?? ''));
    if ($selectedService !== null) {
        $techBase = (int)$selectedService['tech_base'];
        $estimated = (int)$selectedService['public_price'] * $quantity;
    }

    if (strlen($phone) < 8 || $address === '' || $description === '') {
        json_out(['status' => 'error', 'message' => 'Thieu phone, dia chi hoac mo ta su co.'], 400);
    }
    if ($mapLat === null || $mapLng === null) {
        json_out(['status' => 'error', 'message' => 'Vui long bam chon va xac nhan toa do tren ban do truoc khi gui yeu cau.'], 400);
    }
    $serviceRadiusKm = max(1, (float)app_env('SERVICE_RADIUS_KM', '15'));
    $serviceDistanceKm = service_area_distance_km($mapLat, $mapLng);
    if ($serviceDistanceKm > $serviceRadiusKm) {
        json_out([
            'status' => 'error',
            'message' => 'Vi tri nam ngoai ban kinh phuc vu ' . $serviceRadiusKm . ' km tinh tu Cho Lap Vo.',
            'distance_km' => round($serviceDistanceKm, 2),
        ], 400);
    }

    $identifiers = active_identifiers($input, $phone);
    ensure_not_banned($pdo, $identifiers);
    $pricing = calculate_job_pricing($techBase, $estimated, $quantity);

    $jobId = insert_repair_job($pdo, [
        'customer_name' => $customerName,
        'customer_phone' => $phone,
        'service_type' => $serviceType,
        'address' => $address,
        'map_lat' => $mapLat,
        'map_lng' => $mapLng,
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
        'message' => 'Da tao yeu cau goi tho. Gia khach da khop bang gia cong khai va da gom VAT.',
        'job_id' => $jobId,
        'telegram_sent' => $sent,
        'service_distance_km' => round($serviceDistanceKm, 2),
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
            'image' => (string)$row['image_url'] ?? '',
            'image_url' => (string)$row['image_url'] ?? '',
            'category' => (string)($row['category'] ?? 'Store'),
            'created_at' => (string)$row['created_at'] ?? '',
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
            'created_at' => (string)$row['created_at'] ?? '',
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

function marketplace_default_coordinates(string $seed = ''): array
{
    $centerLat = (float)app_env('SERVICE_CENTER_LAT', '10.357422');
    $centerLng = (float)app_env('SERVICE_CENTER_LNG', '105.522124');
    $hash = crc32($seed !== '' ? $seed : 'lap-vo-market');
    $latOffset = (((int)($hash % 7000)) - 3500) / 1000000;
    $lngOffset = (((int)(($hash >> 8) % 7000)) - 3500) / 1000000;

    return [
        'lat' => round($centerLat + $latOffset, 7),
        'lng' => round($centerLng + $lngOffset, 7),
    ];
}

function marketplace_business_lookup(string $taxCode): array
{
    $taxCode = digits_only($taxCode);
    if ($taxCode === '' || !function_exists('curl_init')) {
        return [];
    }

    $ch = curl_init('https://api.vietqr.io/v2/business/' . rawurlencode($taxCode));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 8,
    ]);
    $raw = curl_exec($ch);
    curl_close($ch);
    $data = json_decode((string)$raw, true);
    if (!is_array($data) || (string)($data['code'] ?? '') !== '00' || !is_array($data['data'] ?? null)) {
        return [];
    }

    return [
        'name' => clean_string($data['data']['name'] ?? '', 150),
        'address' => clean_string($data['data']['address'] ?? '', 500),
    ];
}

function marketplace_generate_login_key(PDO $pdo): string
{
    do {
        $key = 'DTHS-' . strtoupper(bin2hex(random_bytes(12)));
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM marketplace_stores WHERE login_key = ?');
        $stmt->execute([$key]);
    } while ((int)$stmt->fetchColumn() > 0);

    return $key;
}

function marketplace_qr_payload(string $loginKey): string
{
    return 'DTH-STORE:' . trim($loginKey);
}

function marketplace_qr_image_url(string $loginKey, int $size = 180): string
{
    if ($loginKey === '') {
        return '';
    }
    return qr_image_url_for_payload(marketplace_qr_payload($loginKey), $size);
}

function marketplace_normalize_login_key(string $value): string
{
    $value = trim($value);
    if (stripos($value, 'DTH-STORE:') === 0) {
        $value = substr($value, strlen('DTH-STORE:'));
    }
    return strtoupper(clean_string($value, 128));
}

function marketplace_store_report_token(array $store): string
{
    $secret = app_env('APP_SECRET', app_env('ADMIN_PASS_HASH', 'dien-tu-hieu-store-report'));
    $payload = 'store_report|' . (int)($store['id'] ?? 0) . '|' . (string)($store['tax_code'] ?? '') . '|' . (string)($store['created_at'] ?? '');
    return hash_hmac('sha256', $payload, $secret);
}

function marketplace_store_report_url(array $store): string
{
    $id = (int)($store['id'] ?? 0);
    if ($id <= 0) {
        return '';
    }
    return app_public_url() . '/api_master.php?action=store_public_report&id=' . $id . '&token=' . marketplace_store_report_token($store);
}

function marketplace_store_report_qr_url(array $store, int $size = 160): string
{
    $url = marketplace_store_report_url($store);
    return $url !== '' ? qr_image_url_for_payload($url, $size) : '';
}

function marketplace_store_row(array $row): array
{
    $loginKey = (string)($row['login_key'] ?? '');
    $reportUrl = marketplace_store_report_url($row);
    return [
        'id' => (int)($row['id'] ?? 0),
        'phone' => (string)($row['phone'] ?? ''),
        'tax_code' => (string)($row['tax_code'] ?? ''),
        'owner_name' => (string)($row['owner_name'] ?? ''),
        'email' => (string)($row['email'] ?? ''),
        'store_name' => (string)($row['store_name'] ?? ''),
        'address' => (string)($row['address'] ?? ''),
        'lat' => isset($row['lat']) ? (float)$row['lat'] : null,
        'lng' => isset($row['lng']) ? (float)$row['lng'] : null,
        'store_type' => (string)($row['store_type'] ?? 'Cua hang'),
        'status' => (string)($row['status'] ?? 'active'),
        'login_key' => $loginKey,
        'qr_payload' => $loginKey !== '' ? marketplace_qr_payload($loginKey) : '',
        'qr_image_url' => marketplace_qr_image_url($loginKey, 160),
        'report_url' => $reportUrl,
        'report_qr_image_url' => $reportUrl !== '' ? qr_image_url_for_payload($reportUrl, 150) : '',
        'approved_at' => (string)($row['approved_at'] ?? ''),
        'approved_by' => (string)($row['approved_by'] ?? ''),
        'order_count' => (int)($row['order_count'] ?? 0),
        'pending_orders' => (int)($row['pending_orders'] ?? 0),
        'total_sales' => money_int($row['total_sales'] ?? 0),
        'created_at' => (string)($row['created_at'] ?? ''),
        'last_login_at' => (string)($row['last_login_at'] ?? ''),
    ];
}

function app_store_register_action(PDO $pdo, array $input): array
{
    $phone = digits_only((string)($input['phone'] ?? $input['contact_phone'] ?? ''));
    $taxCode = strtoupper(clean_string($input['tax_code'] ?? $input['mst'] ?? '', 30));
    $ownerName = clean_string($input['owner_name'] ?? $input['contact_name'] ?? '', 150);
    $email = clean_string($input['email'] ?? '', 190);
    $storeName = clean_string($input['store_name'] ?? $input['shop_name'] ?? $input['name'] ?? '', 150);
    $storeType = clean_string($input['store_type'] ?? $input['category'] ?? 'Cua hang', 50);
    $address = clean_string($input['address'] ?? '', 500);
    $note = clean_string($input['note'] ?? '', 1000);

    if (strlen($phone) < 8) {
        json_out(['status' => 'error', 'message' => 'Vui long nhap so dien thoai cua chu cua hang.'], 400);
    }
    if (strlen(digits_only($taxCode)) < 8) {
        json_out(['status' => 'error', 'message' => 'Vui long nhap ma so thue de gui don dang ky cho giam doc.'], 400);
    }

    $business = marketplace_business_lookup($taxCode);
    if ($storeName === '' && !empty($business['name'])) {
        $storeName = $business['name'];
    }
    if ($address === '' && !empty($business['address'])) {
        $address = $business['address'];
    }
    if ($storeName === '') {
        json_out(['status' => 'error', 'message' => 'Vui long nhap ten cua hang de dong bo len van phong.'], 400);
    }
    if ($storeType === '') {
        $storeType = 'Cua hang';
    }

    $storageTaxCode = $taxCode;
    $lat = isset($input['lat']) && is_numeric($input['lat']) ? (float)$input['lat'] : null;
    $lng = isset($input['lng']) && is_numeric($input['lng']) ? (float)$input['lng'] : null;
    if (($lat === null || $lng === null) && isset($input['latitude'], $input['longitude']) && is_numeric($input['latitude']) && is_numeric($input['longitude'])) {
        $lat = (float)$input['latitude'];
        $lng = (float)$input['longitude'];
    }
    if ($lat !== null && $lng !== null && (abs($lat) > 90 || abs($lng) > 180)) {
        $lat = null;
        $lng = null;
    }
    if ($lat !== null && $lng !== null) {
        $radius = max(1, (float)app_env('SERVICE_RADIUS_KM', '15'));
        $distance = service_area_distance_km($lat, $lng);
        if ($distance > $radius) {
            json_out([
                'status' => 'error',
                'message' => 'Cua hang nam ngoai ban kinh 15 km tinh tu Cho Lap Vo.',
                'distance_km' => round($distance, 2),
            ], 400);
        }
    } else {
        $defaultCoordinates = marketplace_default_coordinates($storageTaxCode . '|' . $phone);
        $lat = $defaultCoordinates['lat'];
        $lng = $defaultCoordinates['lng'];
    }

    $values = [
        'phone' => $phone,
        'tax_code' => $storageTaxCode,
        'owner_name' => $ownerName,
        'email' => $email,
        'store_name' => $storeName,
        'address' => $address,
        'lat' => $lat,
        'lng' => $lng,
        'store_type' => $storeType,
        'note' => $note,
        'status' => 'pending',
    ];

    $stmt = $pdo->prepare('SELECT id, status, login_key FROM marketplace_stores WHERE tax_code = ? ORDER BY id DESC LIMIT 1');
    $stmt->execute([$storageTaxCode]);
    $existing = $stmt->fetch();
    $storeId = (int)($existing['id'] ?? 0);
    if ($storeId > 0) {
        if ((string)($existing['status'] ?? '') === 'active' && (string)($existing['login_key'] ?? '') !== '') {
            $stmt = $pdo->prepare('SELECT * FROM marketplace_stores WHERE id = ? LIMIT 1');
            $stmt->execute([$storeId]);
            return [
                'status' => 'success',
                'approval_status' => 'active',
                'message' => 'Cua hang da duoc duyet. Vui long dang nhap bang QR/key giam doc da cap.',
                'data' => marketplace_store_row($stmt->fetch() ?: []),
            ];
        }
        update_compat($pdo, 'marketplace_stores', $values, 'id = ?', [$storeId], [
            'updated_at' => 'NOW()',
        ]);
    } else {
        $storeId = insert_compat($pdo, 'marketplace_stores', $values, [
            'created_at' => 'NOW()',
            'updated_at' => 'NOW()',
        ]);
    }

    $stmt = $pdo->prepare('SELECT * FROM marketplace_stores WHERE id = ? LIMIT 1');
    $stmt->execute([$storeId]);
    return [
        'status' => 'success',
        'approval_status' => 'pending',
        'message' => 'Da gui don dang ky cua hang len Van phong giam doc. Cho giam doc duyet va cap QR dang nhap.',
        'data' => marketplace_store_row($stmt->fetch() ?: []),
    ];
}

function app_store_login_qr_action(PDO $pdo, array $input): array
{
    $loginKey = marketplace_normalize_login_key((string)($input['login_key'] ?? $input['qr_data'] ?? $input['key'] ?? ''));
    if ($loginKey === '') {
        json_out(['status' => 'error', 'message' => 'Vui long quet QR hoac nhap key dang nhap cua cua hang.'], 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM marketplace_stores WHERE login_key = ? LIMIT 1");
    $stmt->execute([$loginKey]);
    $store = $stmt->fetch();
    if (!$store) {
        json_out(['status' => 'error', 'message' => 'QR/key cua hang khong hop le.'], 404);
    }
    if ((string)($store['status'] ?? '') !== 'active') {
        json_out(['status' => 'error', 'message' => 'Cua hang chua duoc duyet hoac dang bi tam dung.'], 403);
    }

    update_compat($pdo, 'marketplace_stores', [], 'id = ?', [(int)$store['id']], [
        'last_login_at' => 'NOW()',
        'updated_at' => 'NOW()',
    ]);

    $stmt = $pdo->prepare('SELECT * FROM marketplace_stores WHERE id = ? LIMIT 1');
    $stmt->execute([(int)$store['id']]);
    return [
        'status' => 'success',
        'message' => 'Dang nhap cua hang thanh cong.',
        'data' => marketplace_store_row($stmt->fetch() ?: []),
    ];
}

function admin_store_rows(PDO $pdo): array
{
    if (!table_exists($pdo, 'marketplace_stores')) {
        return [];
    }

    $sql = "SELECT s.*,
            COUNT(o.id) AS order_count,
            SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) AS pending_orders,
            COALESCE(SUM(CASE WHEN o.status IN ('pending','completed','confirmed','paid') THEN o.total_amount ELSE 0 END), 0) AS total_sales
        FROM marketplace_stores s
        LEFT JOIN marketplace_orders o ON o.store_id = s.id
        GROUP BY s.id
        ORDER BY s.id DESC";
    $stmt = $pdo->query($sql);

    return array_map('marketplace_store_row', $stmt->fetchAll());
}

function admin_settle_stores_action(PDO $pdo): array
{
    $stores = admin_store_rows($pdo);
    $totalSales = 0;
    foreach ($stores as $store) {
        $totalSales += (int)$store['total_sales'];
    }

    return [
        'status' => 'success',
        'message' => 'Da chot doi soat cua hang: ' . count($stores) . ' cua hang, tong giao dich ' . fmt_money($totalSales) . '.',
        'store_count' => count($stores),
        'total_sales' => $totalSales,
        'data' => $stores,
    ];
}

function admin_approve_store_action(PDO $pdo, array $input): array
{
    $storeId = (int)($input['id'] ?? $input['store_id'] ?? 0);
    if ($storeId <= 0) {
        json_out(['status' => 'error', 'message' => 'ID cua hang khong hop le.'], 400);
    }

    $stmt = $pdo->prepare('SELECT * FROM marketplace_stores WHERE id = ? LIMIT 1');
    $stmt->execute([$storeId]);
    $store = $stmt->fetch();
    if (!$store) {
        json_out(['status' => 'error', 'message' => 'Khong tim thay cua hang.'], 404);
    }

    $loginKey = (string)($store['login_key'] ?? '');
    if ($loginKey === '') {
        $loginKey = marketplace_generate_login_key($pdo);
    }

    update_compat($pdo, 'marketplace_stores', [
        'status' => 'active',
        'login_key' => $loginKey,
        'approved_by' => 'admin',
    ], 'id = ?', [$storeId], [
        'approved_at' => 'NOW()',
        'updated_at' => 'NOW()',
    ]);

    $stmt = $pdo->prepare('SELECT * FROM marketplace_stores WHERE id = ? LIMIT 1');
    $stmt->execute([$storeId]);
    $row = marketplace_store_row($stmt->fetch() ?: []);

    return [
        'status' => 'success',
        'message' => 'Da duyet cua hang va cap QR/key dang nhap.',
        'data' => $row,
    ];
}

function store_public_report_action(PDO $pdo, array $input): void
{
    $storeId = (int)($input['id'] ?? $_GET['id'] ?? 0);
    $token = clean_string($input['token'] ?? $_GET['token'] ?? '', 128);

    $sendError = static function (int $status, string $message): void {
        http_response_code($status);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>QR doi soat</title></head><body style="font-family:Arial,sans-serif;padding:24px"><h1>Khong mo duoc bao cao</h1><p>' . esc_html($message) . '</p></body></html>';
        exit;
    };

    if ($storeId <= 0 || $token === '') {
        $sendError(400, 'Thieu ID cua hang hoac token QR.');
    }

    $stmt = $pdo->prepare('SELECT * FROM marketplace_stores WHERE id = ? LIMIT 1');
    $stmt->execute([$storeId]);
    $store = $stmt->fetch();
    if (!$store) {
        $sendError(404, 'Khong tim thay cua hang.');
    }
    if (!hash_equals(marketplace_store_report_token($store), $token)) {
        $sendError(403, 'Token QR doi soat khong hop le.');
    }

    $monthStart = new DateTimeImmutable('first day of last month 00:00:00');
    $monthEnd = new DateTimeImmutable('first day of this month 00:00:00');
    $stmt = $pdo->prepare('SELECT * FROM marketplace_orders WHERE store_id = ? AND created_at >= ? AND created_at < ? ORDER BY created_at DESC, id DESC');
    $stmt->execute([
        $storeId,
        $monthStart->format('Y-m-d H:i:s'),
        $monthEnd->format('Y-m-d H:i:s'),
    ]);
    $orders = $stmt->fetchAll();
    $total = 0;
    foreach ($orders as $order) {
        $total += money_int($order['total_amount'] ?? 0);
    }

    $storeRow = marketplace_store_row($store);
    $period = $monthStart->format('d/m/Y') . ' - ' . $monthEnd->modify('-1 second')->format('d/m/Y');

    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Doi soat ' . esc_html($storeRow['store_name']) . '</title><style>
        body{font-family:Arial,sans-serif;margin:0;background:#f4f6f8;color:#0f172a}
        .wrap{max-width:980px;margin:0 auto;padding:20px}
        .card{background:#fff;border:1px solid #dfe3e8;border-radius:8px;padding:18px;margin-bottom:14px}
        h1{font-size:24px;margin:0 0 8px} h2{font-size:18px;margin:0 0 10px}
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px}
        .label{color:#64748b;font-size:13px}.value{font-weight:800;margin-top:3px}
        table{width:100%;border-collapse:collapse;background:#fff} th,td{border-bottom:1px solid #e5e7eb;text-align:left;padding:9px;font-size:14px} th{background:#f1f5f9}
        .actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:12px}.btn{display:inline-block;border:1px solid #d1d5db;border-radius:6px;padding:9px 12px;background:#fff;color:#111827;text-decoration:none;font-weight:700}.primary{background:#dc2626;border-color:#dc2626;color:#fff}
        @media print{body{background:#fff}.actions{display:none}.wrap{padding:0}.card{border:0}}
    </style></head><body><main class="wrap">';
    echo '<section class="card"><h1>Thong tin doi soat cua hang</h1><div class="grid">';
    echo '<div><div class="label">Ten co so</div><div class="value">' . esc_html($storeRow['store_name']) . '</div></div>';
    echo '<div><div class="label">Ma so thue</div><div class="value">' . esc_html($storeRow['tax_code']) . '</div></div>';
    echo '<div><div class="label">So dien thoai</div><div class="value">' . esc_html($storeRow['phone']) . '</div></div>';
    echo '<div><div class="label">Chu co so</div><div class="value">' . esc_html($storeRow['owner_name'] ?: '-') . '</div></div>';
    echo '<div><div class="label">Loai cua hang</div><div class="value">' . esc_html($storeRow['store_type']) . '</div></div>';
    echo '<div><div class="label">Trang thai</div><div class="value">' . esc_html($storeRow['status']) . '</div></div>';
    echo '</div><p><b>Dia chi:</b> ' . esc_html($storeRow['address']) . '</p>';
    echo '<div class="actions"><a class="btn primary" href="' . esc_html($storeRow['report_qr_image_url']) . '" target="_blank" download>Tải hinh QR doi soat</a><button class="btn" onclick="window.print()">In bao cao</button></div></section>';
    echo '<section class="card"><h2>Doanh thu thang truoc</h2><div class="grid">';
    echo '<div><div class="label">Ky bao cao</div><div class="value">' . esc_html($period) . '</div></div>';
    echo '<div><div class="label">So don</div><div class="value">' . count($orders) . '</div></div>';
    echo '<div><div class="label">Tong doanh thu</div><div class="value">' . esc_html(fmt_money($total)) . '</div></div>';
    echo '</div></section><section class="card"><h2>Chi tiet giao dich</h2><table><thead><tr><th>ID</th><th>Ngay</th><th>Khach</th><th>Dia chi</th><th>Trang thai</th><th>Tien</th></tr></thead><tbody>';
    if (!$orders) {
        echo '<tr><td colspan="6">Thang truoc chua co giao dich.</td></tr>';
    }
    foreach ($orders as $order) {
        echo '<tr>';
        echo '<td>#' . (int)($order['id'] ?? 0) . '</td>';
        echo '<td>' . esc_html((string)($order['created_at'] ?? '')) . '</td>';
        echo '<td>' . esc_html((string)($order['customer_name'] ?? $order['customer_phone'] ?? '-')) . '</td>';
        echo '<td>' . esc_html((string)($order['customer_address'] ?? '-')) . '</td>';
        echo '<td>' . esc_html((string)($order['status'] ?? '-')) . '</td>';
        echo '<td><b>' . esc_html(fmt_money(money_int($order['total_amount'] ?? 0))) . '</b></td>';
        echo '</tr>';
    }
    echo '</tbody></table></section></main></body></html>';
    exit;
}

function app_store_counts(PDO $pdo): array
{
    if (!table_exists($pdo, 'marketplace_stores')) {
        return [
            'active_total' => 0,
            'pending_total' => 0,
            'types' => [],
        ];
    }

    $activeTotal = (int)$pdo->query("SELECT COUNT(*) FROM marketplace_stores WHERE status = 'active'")->fetchColumn();
    $pendingTotal = (int)$pdo->query("SELECT COUNT(*) FROM marketplace_stores WHERE status = 'pending'")->fetchColumn();
    $stmt = $pdo->query("SELECT store_type, COUNT(*) AS count FROM marketplace_stores WHERE status = 'active' GROUP BY store_type");
    $types = [];
    foreach ($stmt->fetchAll() as $row) {
        $type = (string)($row['store_type'] ?? 'Cua hang');
        $types[$type] = (int)($row['count'] ?? 0);
    }

    return [
        'active_total' => $activeTotal,
        'pending_total' => $pendingTotal,
        'types' => $types,
    ];
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
        $coordinates = worker_map_coordinates($row);
        $rows[] = [
            'id' => (int)$row['id'],
            'customer_name' => (string)($row['customer_name'] ?? ''),
            'customer_phone' => (string)($row['customer_phone'] ?? ''),
            'service_type' => (string)($row['service_type'] ?? $row['title'] ?? ''),
            'address' => (string)($row['address'] ?? $row['location'] ?? ''),
            'map_location' => (string)($coordinates['text'] ?? ''),
            'maps_url' => worker_google_maps_url($row),
            'description' => (string)($row['description'] ?? ''),
            'final_total' => money_int($row['final_total'] ?? $row['customer_total'] ?? $row['salary_max'] ?? 0),
            'platform_fee' => money_int($pricing['platform_fee'] ?? 0),
            'tech_net_income' => money_int($pricing['tech_net_income'] ?? 0),
            'worker_id' => job_worker_telegram_id($row) ?: null,
            'status' => job_display_status($row),
            'spam_count' => (int)($row['spam_count'] ?? 0),
            'created_at' => (string)($row['created_at'] ?? ''),
            'completed_at' => (string)($row['completed_at'] ?? ''),
        ];
    }
    return $rows;
}

function admin_worker_rows(PDO $pdo): array
{
    if (!table_exists($pdo, 'worker_profiles')) {
        return [];
    }
    $stmt = $pdo->query("SELECT wp.telegram_user_id AS worker_id, wp.telegram_name, wp.telegram_username, wp.phone, wp.identity_code,
        wp.worker_type, wp.role, wp.is_admin, wp.cancel_count, wp.is_receive_blocked, wp.payment_blocked, wp.block_reason,
        wp.jobs_claimed, wp.jobs_completed, wp.total_paid_fee, wp.last_payment_amount, wp.last_payment_at, wp.last_fee_notice_at,
        wp.last_seen_bot, wp.last_seen_at, wp.created_at,
        COALESCE((SELECT COUNT(*) FROM job_posts j WHERE COALESCE(j.telegram_worker_id, j.worker_id) = wp.telegram_user_id), 0) AS job_count,
        COALESCE((SELECT SUM(jp.tech_net_income) FROM job_pricing jp JOIN job_posts j ON j.id = jp.job_id WHERE COALESCE(j.telegram_worker_id, j.worker_id) = wp.telegram_user_id AND j.completed_at IS NOT NULL), 0) AS total_earned,
        COALESCE((SELECT SUM(GREATEST(jp.platform_fee - COALESCE(jp.paid_amount, 0), 0)) FROM job_pricing jp JOIN job_posts j ON j.id = jp.job_id WHERE COALESCE(j.telegram_worker_id, j.worker_id) = wp.telegram_user_id AND j.completed_at IS NOT NULL), 0) AS unpaid_fee,
        COALESCE((SELECT SUM(p.applied_amount) FROM worker_payments p WHERE p.worker_id = wp.telegram_user_id AND p.status = 'confirmed'), 0) AS confirmed_paid_fee,
        COALESCE((SELECT COUNT(*) FROM worker_payments p WHERE p.worker_id = wp.telegram_user_id AND p.status = 'pending'), 0) AS pending_payment_count
        FROM worker_profiles wp
        ORDER BY wp.is_admin DESC, unpaid_fee DESC, wp.updated_at DESC, wp.created_at DESC LIMIT 500");
    return $stmt->fetchAll();
}

function admin_worker_payments(PDO $pdo, int $limit = 200): array
{
    if (!table_exists($pdo, 'worker_payments')) {
        return [];
    }
    $limit = max(1, min(500, $limit));
    $stmt = $pdo->query("SELECT p.*, wp.telegram_name, wp.phone
        FROM worker_payments p LEFT JOIN worker_profiles wp ON wp.telegram_user_id = p.worker_id
        ORDER BY p.id DESC LIMIT {$limit}");
    return $stmt->fetchAll();
}

function bct_validate_period(string $from, string $to): array
{
    $from = trim($from);
    $to = trim($to);
    $start = DateTimeImmutable::createFromFormat('!Y-m-d', $from);
    $startErrors = DateTimeImmutable::getLastErrors();
    $end = DateTimeImmutable::createFromFormat('!Y-m-d', $to);
    $endErrors = DateTimeImmutable::getLastErrors();
    $startInvalid = !$start || ($startErrors !== false && ((int)$startErrors['warning_count'] > 0 || (int)$startErrors['error_count'] > 0));
    $endInvalid = !$end || ($endErrors !== false && ((int)$endErrors['warning_count'] > 0 || (int)$endErrors['error_count'] > 0));
    if ($startInvalid || $endInvalid || $start->format('Y-m-d') !== $from || $end->format('Y-m-d') !== $to) {
        throw new InvalidArgumentException('Ky bao cao phai dung dinh dang YYYY-MM-DD.');
    }
    if ($start > $end) {
        throw new InvalidArgumentException('Ngay bat dau khong duoc sau ngay ket thuc.');
    }
    $maxDays = max(1, (int)app_env('BCT_REPORT_MAX_DAYS', '370'));
    $days = (int)$start->diff($end)->format('%a') + 1;
    if ($days > $maxDays) {
        throw new InvalidArgumentException("Ky bao cao toi da {$maxDays} ngay.");
    }
    return [$start->format('Y-m-d'), $end->format('Y-m-d')];
}

function input_invoice_storage_root(): string
{
    $root = __DIR__ . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'bct-invoices';
    if (!is_dir($root) && !mkdir($root, 0700, true) && !is_dir($root)) {
        throw new RuntimeException('Khong tao duoc thu muc luu hoa don dau vao.');
    }
    $real = realpath($root);
    if ($real === false) {
        throw new RuntimeException('Khong xac dinh duoc thu muc luu hoa don dau vao.');
    }
    return $real;
}

function input_invoice_row_for_admin(array $row): array
{
    foreach (['id', 'subtotal_amount', 'vat_amount', 'adjustment_amount', 'total_amount', 'pdf_size'] as $field) {
        $row[$field] = (int)($row[$field] ?? 0);
    }
    $row['download_url'] = 'api_master.php?action=admin_input_invoice_file&id=' . (int)$row['id'];
    unset($row['pdf_path']);
    return $row;
}

function admin_input_invoice_rows(PDO $pdo, int $limit = 300): array
{
    $limit = max(1, min(1000, $limit));
    $stmt = $pdo->query("SELECT * FROM input_invoices ORDER BY invoice_date DESC, id DESC LIMIT {$limit}");
    return array_map('input_invoice_row_for_admin', $stmt->fetchAll());
}

function admin_save_input_invoice_pdf(PDO $pdo, array $input, array $file): array
{
    $invoiceNumber = clean_string($input['invoice_number'] ?? '', 120);
    $invoiceSeries = strtoupper(clean_string($input['invoice_series'] ?? '', 80));
    $invoiceDate = clean_string($input['invoice_date'] ?? '', 10);
    $sellerName = clean_string($input['seller_name'] ?? '', 255);
    $sellerTaxCode = strtoupper(clean_string($input['seller_tax_code'] ?? '', 50));
    $note = clean_string($input['note'] ?? '', 2000);
    bct_validate_period($invoiceDate, $invoiceDate);
    if ($invoiceNumber === '' || $invoiceSeries === '' || $sellerName === '' || $sellerTaxCode === '') {
        throw new InvalidArgumentException('So hoa don, ky hieu hoa don, ngay hoa don, don vi ban va ma so thue la bat buoc.');
    }
    if (!preg_match('/^\d{10}(?:-\d{3})?$/', $sellerTaxCode)) {
        throw new InvalidArgumentException('Ma so thue don vi ban phai gom 10 chu so hoac 10 chu so kem - va 3 chu so don vi phu thuoc.');
    }

    $subtotal = money_int($input['subtotal_amount'] ?? 0);
    $vat = money_int($input['vat_amount'] ?? 0);
    $adjustment = signed_money_int($input['adjustment_amount'] ?? 0);
    $total = money_int($input['total_amount'] ?? 0);
    if ($subtotal + $vat + $adjustment !== $total) {
        throw new InvalidArgumentException('Tong hoa don phai bang tien truoc thue + VAT + dieu chinh.');
    }

    $uploadError = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($uploadError !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('File PDF khong duoc tai len hop le. Ma loi: ' . $uploadError);
    }
    $tmp = (string)($file['tmp_name'] ?? '');
    $originalName = clean_string(basename((string)($file['name'] ?? 'hoa-don.pdf')), 255);
    $size = (int)($file['size'] ?? 0);
    $maxBytes = max(1, (int)app_env('BCT_INPUT_PDF_MAX_MB', '20')) * 1024 * 1024;
    if ($tmp === '' || !is_uploaded_file($tmp) || $size <= 0 || $size > $maxBytes) {
        throw new InvalidArgumentException('PDF rong, qua dung luong cho phep hoac khong phai file upload hop le.');
    }
    if (strtolower((string)pathinfo($originalName, PATHINFO_EXTENSION)) !== 'pdf') {
        throw new InvalidArgumentException('Chi chap nhan file PDF.');
    }
    $head = file_get_contents($tmp, false, null, 0, 1024);
    if (!is_string($head) || strpos($head, '%PDF-') === false) {
        throw new InvalidArgumentException('Noi dung file khong phai dinh dang PDF.');
    }
    if (class_exists('finfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = (string)$finfo->file($tmp);
        if (!in_array($mime, ['application/pdf', 'application/x-pdf', 'application/octet-stream'], true)) {
            throw new InvalidArgumentException('MIME cua file khong phai PDF.');
        }
    }
    $sha256 = hash_file('sha256', $tmp);
    if (!is_string($sha256) || strlen($sha256) !== 64) {
        throw new RuntimeException('Khong tao duoc SHA-256 cho PDF.');
    }

    $duplicate = $pdo->prepare('SELECT id FROM input_invoices WHERE pdf_sha256 = ? OR (seller_tax_code = ? AND invoice_series = ? AND invoice_number = ?) LIMIT 1');
    $duplicate->execute([$sha256, $sellerTaxCode, $invoiceSeries, $invoiceNumber]);
    if ($duplicate->fetchColumn()) {
        throw new DomainException('Hoa don hoac PDF nay da ton tai, he thong khong ghi trung.');
    }

    $root = input_invoice_storage_root();
    $subdir = date('Y/m', strtotime($invoiceDate));
    $targetDir = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $subdir);
    if (!is_dir($targetDir) && !mkdir($targetDir, 0700, true) && !is_dir($targetDir)) {
        throw new RuntimeException('Khong tao duoc thu muc luu PDF theo ky.');
    }
    $storedName = date('Ymd', strtotime($invoiceDate)) . '-' . bin2hex(random_bytes(16)) . '.pdf';
    $target = $targetDir . DIRECTORY_SEPARATOR . $storedName;
    if (!move_uploaded_file($tmp, $target)) {
        throw new RuntimeException('Khong luu duoc PDF hoa don.');
    }
    @chmod($target, 0600);
    $relativePath = $subdir . '/' . $storedName;

    try {
        $stmt = $pdo->prepare("INSERT INTO input_invoices
            (invoice_number, invoice_series, invoice_date, seller_name, seller_tax_code, subtotal_amount, vat_amount, adjustment_amount,
             total_amount, currency, pdf_path, pdf_original_name, pdf_sha256, pdf_size, status, note, uploaded_by, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'VND', ?, ?, ?, ?, 'active', ?, 'admin', NOW())");
        $stmt->execute([
            $invoiceNumber, $invoiceSeries, $invoiceDate, $sellerName, $sellerTaxCode, $subtotal, $vat, $adjustment,
            $total, $relativePath, $originalName, $sha256, $size, $note,
        ]);
        $id = (int)$pdo->lastInsertId();
    } catch (Throwable $e) {
        @unlink($target);
        throw $e;
    }

    $stmt = $pdo->prepare('SELECT * FROM input_invoices WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    return input_invoice_row_for_admin($stmt->fetch() ?: ['id' => $id]);
}

function admin_stream_input_invoice(PDO $pdo, int $id)
{
    $stmt = $pdo->prepare('SELECT pdf_path, pdf_original_name, pdf_sha256 FROM input_invoices WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        json_out(['status' => 'error', 'message' => 'Khong tim thay PDF hoa don.'], 404);
    }
    $root = input_invoice_storage_root();
    $relative = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, (string)$row['pdf_path']);
    $path = realpath($root . DIRECTORY_SEPARATOR . $relative);
    $rootPrefix = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if ($path === false || strpos($path, $rootPrefix) !== 0 || !is_file($path)) {
        json_out(['status' => 'error', 'message' => 'PDF hoa don khong con tren kho luu tru.'], 404);
    }
    if (!hash_equals((string)$row['pdf_sha256'], hash_file('sha256', $path))) {
        json_out(['status' => 'error', 'message' => 'PDF khong vuot qua kiem tra toan ven SHA-256.'], 409);
    }
    $original = basename((string)$row['pdf_original_name']);
    $asciiName = preg_replace('/[^A-Za-z0-9._-]+/', '-', $original) ?: 'hoa-don.pdf';
    if (ob_get_length()) {
        ob_clean();
    }
    header('Content-Type: application/pdf');
    header('Content-Length: ' . filesize($path));
    header('Content-Disposition: inline; filename="' . $asciiName . '"; filename*=UTF-8\'\'' . rawurlencode($original));
    header('Cache-Control: private, no-store, no-cache, must-revalidate');
    readfile($path);
    exit;
}

function bct_money_summary(array $rows): array
{
    $summary = [
        'document_count' => count($rows),
        'subtotal_amount' => 0,
        'vat_amount' => 0,
        'adjustment_amount' => 0,
        'total_amount' => 0,
    ];
    foreach ($rows as $row) {
        foreach (['subtotal_amount', 'vat_amount', 'adjustment_amount', 'total_amount'] as $field) {
            $summary[$field] += (int)($row[$field] ?? 0);
        }
    }
    return $summary;
}

function bct_reconciliation_report(PDO $pdo, string $from, string $to, bool $includeDetails = true): array
{
    list($from, $to) = bct_validate_period($from, $to);
    $issues = [];
    $companyProfile = invoice_company_profile();
    $companyName = app_env('BCT_COMPANY_NAME', $companyProfile['name']);
    $companyTaxCode = app_env('BCT_TAX_CODE', $companyProfile['tax_code']);
    $companyWebsite = app_env('BCT_WEBSITE', $companyProfile['website']);
    if (!preg_match('/^\d{10}(?:-\d{3})?$/', $companyTaxCode)) {
        $issues[] = ['severity' => 'blocking', 'code' => 'company_tax_code_missing_or_invalid', 'count' => 1, 'ids' => []];
    }

    $stmt = $pdo->prepare("SELECT id, invoice_number, invoice_series, invoice_date, seller_name, seller_tax_code,
        subtotal_amount, vat_amount, adjustment_amount, total_amount, currency, pdf_original_name, pdf_sha256, pdf_size, created_at
        FROM input_invoices WHERE status = 'active' AND invoice_date BETWEEN ? AND ? ORDER BY invoice_date, id");
    $stmt->execute([$from, $to]);
    $inputRows = $stmt->fetchAll();
    $inputFormulaIds = [];
    $inputIntegrityIds = [];
    foreach ($inputRows as &$row) {
        foreach (['id', 'subtotal_amount', 'vat_amount', 'adjustment_amount', 'total_amount', 'pdf_size'] as $field) {
            $row[$field] = (int)($row[$field] ?? 0);
        }
        if ($row['subtotal_amount'] + $row['vat_amount'] + $row['adjustment_amount'] !== $row['total_amount']) {
            $inputFormulaIds[] = $row['id'];
        }
        if (!preg_match('/^[a-f0-9]{64}$/i', (string)$row['pdf_sha256']) || $row['pdf_size'] <= 0) {
            $inputIntegrityIds[] = $row['id'];
        }
    }
    unset($row);
    if ($inputFormulaIds) {
        $issues[] = ['severity' => 'blocking', 'code' => 'input_invoice_formula_mismatch', 'count' => count($inputFormulaIds), 'ids' => $inputFormulaIds];
    }
    if ($inputIntegrityIds) {
        $issues[] = ['severity' => 'blocking', 'code' => 'input_invoice_pdf_integrity_missing', 'count' => count($inputIntegrityIds), 'ids' => $inputIntegrityIds];
    }
    if ($inputRows) {
        $issues[] = ['severity' => 'warning', 'code' => 'input_invoice_pdf_values_require_manual_attestation', 'count' => count($inputRows), 'ids' => array_column($inputRows, 'id')];
    }

    $stmt = $pdo->prepare("SELECT i.id, i.invoice_code, i.order_id, COALESCE(i.invoice_date, DATE(i.created_at)) invoice_date,
        i.subtotal_amount, i.vat_amount, i.adjustment_amount,
        CASE WHEN i.total_amount > 0 THEN i.total_amount ELSE i.total_price END total_amount,
        i.total_amount recorded_total_amount, i.total_price legacy_total_price, i.status, i.created_at,
        o.total_price order_total, o.status order_status
        FROM invoices i LEFT JOIN orders o ON o.id = i.order_id
        WHERE i.status = 'active' AND COALESCE(i.invoice_date, DATE(i.created_at)) BETWEEN ? AND ?
        ORDER BY COALESCE(i.invoice_date, DATE(i.created_at)), i.id");
    $stmt->execute([$from, $to]);
    $outputRows = $stmt->fetchAll();
    $outputFormulaIds = [];
    $outputBreakdownMissingIds = [];
    $outputVatZeroIds = [];
    $outputOrderMismatchIds = [];
    foreach ($outputRows as &$row) {
        foreach (['id', 'order_id', 'subtotal_amount', 'vat_amount', 'adjustment_amount', 'total_amount', 'recorded_total_amount', 'legacy_total_price', 'order_total'] as $field) {
            $row[$field] = (int)($row[$field] ?? 0);
        }
        if ($row['recorded_total_amount'] > 0 && $row['subtotal_amount'] + $row['vat_amount'] + $row['adjustment_amount'] !== $row['recorded_total_amount']) {
            $outputFormulaIds[] = $row['id'];
        }
        if ($row['recorded_total_amount'] <= 0 && $row['legacy_total_price'] > 0) {
            $outputBreakdownMissingIds[] = $row['id'];
        }
        if ($row['total_amount'] > 0 && $row['vat_amount'] === 0) {
            $outputVatZeroIds[] = $row['id'];
        }
        if ($row['order_id'] > 0 && $row['order_total'] !== $row['total_amount']) {
            $outputOrderMismatchIds[] = $row['id'];
        }
    }
    unset($row);
    if ($outputFormulaIds) {
        $issues[] = ['severity' => 'blocking', 'code' => 'output_invoice_formula_mismatch', 'count' => count($outputFormulaIds), 'ids' => $outputFormulaIds];
    }
    if ($outputOrderMismatchIds) {
        $issues[] = ['severity' => 'blocking', 'code' => 'output_invoice_order_total_mismatch', 'count' => count($outputOrderMismatchIds), 'ids' => $outputOrderMismatchIds];
    }
    if ($outputBreakdownMissingIds) {
        $issues[] = ['severity' => 'warning', 'code' => 'legacy_output_invoice_missing_vat_breakdown', 'count' => count($outputBreakdownMissingIds), 'ids' => $outputBreakdownMissingIds];
    }
    if ($outputVatZeroIds) {
        $issues[] = ['severity' => 'warning', 'code' => 'output_invoice_vat_zero_or_not_separated', 'count' => count($outputVatZeroIds), 'ids' => $outputVatZeroIds];
    }
    if ($outputRows) {
        $issues[] = ['severity' => 'warning', 'code' => 'output_invoice_signed_einvoice_document_not_stored', 'count' => count($outputRows), 'ids' => array_column($outputRows, 'id')];
    }

    $stmt = $pdo->prepare("SELECT id, order_code, total_price, status, COALESCE(confirmed_at, created_at) accounting_time
        FROM orders
        WHERE status IN ('confirmed','shipped','processing','completed')
          AND DATE(COALESCE(confirmed_at, created_at)) BETWEEN ? AND ?
        ORDER BY id");
    $stmt->execute([$from, $to]);
    $orderRows = $stmt->fetchAll();
    $orderTotal = 0;
    $missingInvoiceOrderIds = [];
    $invoiceOutsidePeriodOrderIds = [];
    $confirmedOrderInvoiceCount = 0;
    $outputOrderIds = [];
    foreach ($outputRows as $invoice) {
        if ((int)$invoice['order_id'] > 0) {
            $outputOrderIds[(int)$invoice['order_id']] = true;
        }
    }
    $allActiveInvoiceOrderIds = [];
    $allInvoiceStmt = $pdo->query("SELECT DISTINCT order_id FROM invoices WHERE status = 'active' AND order_id IS NOT NULL");
    foreach ($allInvoiceStmt->fetchAll() as $invoice) {
        $allActiveInvoiceOrderIds[(int)$invoice['order_id']] = true;
    }
    foreach ($orderRows as &$row) {
        $row['id'] = (int)$row['id'];
        $row['total_price'] = (int)$row['total_price'];
        $orderTotal += $row['total_price'];
        if (isset($outputOrderIds[$row['id']])) {
            $confirmedOrderInvoiceCount++;
        } elseif (isset($allActiveInvoiceOrderIds[$row['id']])) {
            $invoiceOutsidePeriodOrderIds[] = $row['id'];
        } else {
            $missingInvoiceOrderIds[] = $row['id'];
        }
    }
    unset($row);
    if ($missingInvoiceOrderIds) {
        $issues[] = ['severity' => 'blocking', 'code' => 'confirmed_order_missing_output_invoice', 'count' => count($missingInvoiceOrderIds), 'ids' => $missingInvoiceOrderIds];
    }
    if ($invoiceOutsidePeriodOrderIds) {
        $issues[] = ['severity' => 'blocking', 'code' => 'confirmed_order_output_invoice_outside_report_period', 'count' => count($invoiceOutsidePeriodOrderIds), 'ids' => $invoiceOutsidePeriodOrderIds];
    }

    $stmt = $pdo->prepare("SELECT j.id, DATE(j.completed_at) completed_date, j.service_type, j.final_total customer_total,
        COALESCE((SELECT jp.platform_fee FROM job_pricing jp WHERE jp.job_id = j.id ORDER BY jp.id DESC LIMIT 1), 0) platform_fee,
        COALESCE((SELECT jp.vat_amount FROM job_pricing jp WHERE jp.job_id = j.id ORDER BY jp.id DESC LIMIT 1), 0) vat_amount
        FROM job_posts j WHERE j.completed_at IS NOT NULL AND DATE(j.completed_at) BETWEEN ? AND ? ORDER BY j.id");
    $stmt->execute([$from, $to]);
    $jobRows = $stmt->fetchAll();
    $jobCustomerTotal = 0;
    $platformFeeTotal = 0;
    $jobVatTotal = 0;
    foreach ($jobRows as &$row) {
        foreach (['id', 'customer_total', 'platform_fee', 'vat_amount'] as $field) {
            $row[$field] = (int)($row[$field] ?? 0);
        }
        $jobCustomerTotal += $row['customer_total'];
        $platformFeeTotal += $row['platform_fee'];
        $jobVatTotal += $row['vat_amount'];
    }
    unset($row);

    $inputSummary = bct_money_summary($inputRows);
    $outputSummary = bct_money_summary($outputRows);
    $blockingCount = count(array_filter($issues, static function (array $issue): bool {
        return ($issue['severity'] ?? '') === 'blocking';
    }));
    $warningCount = count(array_filter($issues, static function (array $issue): bool {
        return ($issue['severity'] ?? '') === 'warning';
    }));

    $report = [
        'schema_version' => 'dth-bct-report-1.0',
        'generated_at' => date(DATE_ATOM),
        'period' => ['from' => $from, 'to' => $to, 'timezone' => 'Asia/Ho_Chi_Minh'],
        'company' => [
            'name' => $companyName,
            'tax_code' => $companyTaxCode,
            'website' => $companyWebsite,
        ],
        'submission_status' => [
            'ready_for_submission' => $blockingCount === 0,
            'blocking_issue_count' => $blockingCount,
            'warning_count' => $warningCount,
        ],
        'invoice_registers' => [
            'input_purchase_invoices' => $inputSummary,
            'output_sales_invoices' => $outputSummary,
        ],
        'operational_records' => [
            'confirmed_product_orders' => ['count' => count($orderRows), 'total_amount' => $orderTotal],
            'completed_service_jobs' => ['count' => count($jobRows), 'customer_total' => $jobCustomerTotal, 'vat_amount' => $jobVatTotal],
            'platform_fee_accrual' => ['count' => count($jobRows), 'total_amount' => $platformFeeTotal],
        ],
        'reconciliation' => [
            'output_invoice_minus_confirmed_order_amount' => $outputSummary['total_amount'] - $orderTotal,
            'output_invoice_count_for_confirmed_orders' => $confirmedOrderInvoiceCount,
            'confirmed_orders_missing_output_invoice_count' => count($missingInvoiceOrderIds),
            'confirmed_orders_invoice_outside_report_period_count' => count($invoiceOutsidePeriodOrderIds),
            'input_output_invoice_value_difference' => $outputSummary['total_amount'] - $inputSummary['total_amount'],
        ],
        'issues' => $issues,
        'transparency_notes' => [
            'Input invoice values are admin-entered metadata linked to immutable PDF files and SHA-256 hashes; PDF monetary content is not automatically OCR-verified.',
            'Output invoice totals are reconciled against confirmed product orders. Legacy invoices without VAT breakdown and missing signed e-invoice documents are disclosed as warnings.',
            'Service job customer totals and platform fees are operational records, not represented as legally issued electronic invoices by this system.',
            'This export is a system reconciliation report and does not replace legally issued electronic invoices or an official authority-specific schema.',
        ],
    ];
    if ($includeDetails) {
        $report['details'] = [
            'input_purchase_invoices' => $inputRows,
            'output_sales_invoices' => $outputRows,
            'confirmed_product_orders' => $orderRows,
            'completed_service_jobs' => $jobRows,
        ];
    }
    return $report;
}

function bct_log_report_access(PDO $pdo, string $username, string $authMode, ?string $from, ?string $to, ?string $responseHash, bool $success)
{
    $stmt = $pdo->prepare("INSERT INTO bct_report_access_log
        (username, auth_mode, period_from, period_to, response_sha256, client_ip, user_agent, success, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        clean_string($username, 190),
        clean_string($authMode, 30),
        $from,
        $to,
        $responseHash,
        client_ip(),
        clean_string($_SERVER['HTTP_USER_AGENT'] ?? '', 500),
        $success ? 1 : 0,
    ]);
}

function xml_cell($value, string $type = 'String'): string
{
    $escaped = htmlspecialchars((string)$value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    return '<Cell><Data ss:Type="' . $type . '">' . $escaped . '</Data></Cell>';
}

function output_daily_settlement_excel(PDO $pdo)
{
    $stats = admin_stats($pdo);
    $workers = admin_worker_rows($pdo);
    $payments = admin_worker_payments($pdo, 500);
    $today = date('Y-m-d');
    $todayPayments = array_values(array_filter($payments, static function (array $payment) use ($today): bool {
        return dth_starts_with((string)($payment['confirmed_at'] ?? ''), $today);
    }));
    $todayJobs = array_values(array_filter(admin_jobs($pdo), static function (array $job) use ($today): bool {
        return $job['status'] === 'completed' && dth_starts_with((string)$job['completed_at'], $today);
    }));

    if (ob_get_length()) {
        ob_clean();
    }
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="ket-toan-ngay-' . $today . '.xls"');
    header('Cache-Control: no-store, no-cache, must-revalidate');

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<?mso-application progid="Excel.Sheet"?>';
    echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
    echo '<Worksheet ss:Name="Ket toan ngay"><Table>';
    echo '<Row>' . xml_cell('KET TOAN NGAY ' . date('d/m/Y')) . '</Row>';
    foreach ([
        ['Don hang hom nay', (int)$stats['today_orders']],
        ['Doanh thu don hang hom nay', (int)$stats['today_revenue']],
        ['Ca goi tho hom nay', (int)$stats['today_jobs']],
        ['Tong so tho', (int)($stats['total_workers'] ?? 0)],
        ['Phi nen tang da thu hom nay', array_sum(array_map(static function (array $p): int { return (int)($p['applied_amount'] ?? 0); }, $todayPayments))],
        ['Tong no phi nen tang hien tai', (int)$stats['unpaid_total']],
    ] as $item) {
        echo '<Row>' . xml_cell($item[0]) . xml_cell($item[1], 'Number') . '</Row>';
    }

    echo '<Row></Row><Row>' . xml_cell('TONG HOP THO VA CONG NO') . '</Row>';
    echo '<Row>' . xml_cell('Telegram ID') . xml_cell('Ten tho') . xml_cell('So dien thoai') . xml_cell('Loai') . xml_cell('So ca xong') . xml_cell('Thu nhap') . xml_cell('Da dong phi') . xml_cell('Con no phi') . xml_cell('Trang thai') . '</Row>';
    foreach ($workers as $worker) {
        if ((int)($worker['is_admin'] ?? 0) === 1) {
            continue;
        }
        echo '<Row>'
            . xml_cell($worker['worker_id'])
            . xml_cell($worker['telegram_name'])
            . xml_cell($worker['phone'])
            . xml_cell($worker['worker_type'])
            . xml_cell((int)$worker['jobs_completed'], 'Number')
            . xml_cell((int)$worker['total_earned'], 'Number')
            . xml_cell((int)$worker['confirmed_paid_fee'], 'Number')
            . xml_cell((int)$worker['unpaid_fee'], 'Number')
            . xml_cell(((int)$worker['is_receive_blocked'] === 1 || (int)$worker['payment_blocked'] === 1) ? 'Khoa' : 'Hoat dong')
            . '</Row>';
    }

    echo '<Row></Row><Row>' . xml_cell('CA HOAN THANH HOM NAY') . '</Row>';
    echo '<Row>' . xml_cell('Ma ca') . xml_cell('Dich vu') . xml_cell('Tho') . xml_cell('Gia khach') . xml_cell('Phi nen tang') . xml_cell('Dia chi') . '</Row>';
    foreach ($todayJobs as $job) {
        echo '<Row>'
            . xml_cell('#' . $job['id'])
            . xml_cell($job['service_type'])
            . xml_cell($job['worker_id'])
            . xml_cell((int)$job['final_total'], 'Number')
            . xml_cell((int)$job['platform_fee'], 'Number')
            . xml_cell($job['address'])
            . '</Row>';
    }
    echo '</Table></Worksheet></Workbook>';
    exit;
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
        $stmt = $pdo->query("SELECT COUNT(*) c, COALESCE(SUM(GREATEST(jp.platform_fee - COALESCE(jp.paid_amount, 0), 0)),0) s
            FROM job_pricing jp JOIN job_posts j ON j.id = jp.job_id
            WHERE j.completed_at IS NOT NULL AND jp.platform_fee > COALESCE(jp.paid_amount, 0)");
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
    $activeWorkers = 0;
    $totalWorkers = 0;
    $blockedWorkers = 0;
    if (table_exists($pdo, 'worker_profiles')) {
        $totalWorkers = (int)$pdo->query("SELECT COUNT(*) FROM worker_profiles WHERE is_admin = 0 AND role = 'worker'")->fetchColumn();
        $activeWorkers = (int)$pdo->query("SELECT COUNT(*) FROM worker_profiles WHERE is_admin = 0 AND role = 'worker' AND is_receive_blocked = 0 AND payment_blocked = 0")->fetchColumn();
        $blockedWorkers = (int)$pdo->query("SELECT COUNT(*) FROM worker_profiles WHERE is_admin = 0 AND role = 'worker' AND (is_receive_blocked = 1 OR payment_blocked = 1)")->fetchColumn();
    }
    $feesPaidToday = table_exists($pdo, 'worker_payments')
        ? (int)$pdo->query("SELECT COALESCE(SUM(applied_amount),0) FROM worker_payments WHERE status = 'confirmed' AND DATE(confirmed_at) = CURDATE()")->fetchColumn()
        : 0;
    $pendingPayments = table_exists($pdo, 'worker_payments')
        ? (int)$pdo->query("SELECT COUNT(*) FROM worker_payments WHERE status = 'pending'")->fetchColumn()
        : 0;

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
        'total_workers' => $totalWorkers,
        'blocked_workers' => $blockedWorkers,
        'unpaid_count' => $unpaidCount,
        'unpaid_total' => $unpaidTotal,
        'fees_paid_today' => $feesPaidToday,
        'pending_worker_payments' => $pendingPayments,
        'banned_devices' => $banCount,
    ];
}

function gemini_fallback_reply(string $message, array $input = []): string
{
    $publicPrice = clean_string($input['public_price'] ?? '', 120);
    $service = clean_string($input['selected_service'] ?? $input['service_type'] ?? '', 150);
    $line = $service !== '' ? "Dịch vụ đang chọn: {$service}." : 'Bạn có thể chọn dịch vụ trong bảng giá trước.';
    $price = $publicPrice !== '' ? " Giá tham khảo: {$publicPrice}." : '';
    return "{$line}{$price} Giá đã gồm VAT; vật tư/linh kiện phát sinh sẽ được báo riêng trước khi làm. Để chốt nhanh, vui lòng gửi form Gọi Thợ hoặc gọi 0979.553.289.";
}

function gemini_quote_reply(array $input): string
{
    $message = clean_string($input['message'] ?? '', 1000);
    if ($message === '') {
        return gemini_fallback_reply($message, $input);
    }

    $key = app_env('GEMINI_API_KEY', '');
    if ($key === '' || !function_exists('curl_init')) {
        return gemini_fallback_reply($message, $input);
    }

    $model = trim(app_env('GEMINI_MODEL', 'gemini-1.5-flash'));
    $model = preg_replace('/^models\//', '', $model) ?: 'gemini-1.5-flash';
    $serviceType = clean_string($input['service_type'] ?? '', 150);
    $selected = clean_string($input['selected_service'] ?? '', 150);
    $publicPrice = clean_string($input['public_price'] ?? '', 120);
    $address = clean_string($input['address'] ?? '', 500);
    $prompt = "Bạn là trợ lí báo giá của Điện Tử Hiếu. Trả lời tiếng Việt, ngắn gọn, thực tế, không hứa giảm giá ngoài bảng. "
        . "Nhấn mạnh giá công khai đã gồm VAT, vật tư/linh kiện phát sinh báo riêng trước khi làm. "
        . "Bảng tham khảo: vệ sinh máy lạnh 165.000 VND; lắp máy lạnh 1HP/1.5HP 440.000 VND; lắp máy lạnh 2HP/3HP 550.000 VND; sửa chữa điện lạnh, treo tivi, lắp máy lọc nước, lắp máy giặt, kiểm tra/sửa điện thoại 220.000 VND. "
        . "Nhóm: {$serviceType}. Dịch vụ chọn: {$selected}. Giá đang hiển thị: {$publicPrice}. Địa chỉ: {$address}. Câu hỏi khách: {$message}";

    $payload = [
        'contents' => [[
            'role' => 'user',
            'parts' => [['text' => $prompt]],
        ]],
        'generationConfig' => [
            'temperature' => 0.25,
            'maxOutputTokens' => 360,
        ],
    ];

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($model) . ':generateContent?key=' . rawurlencode($key);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 18,
    ]);
    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode((string)$raw, true);
    $reply = '';
    if (is_array($decoded)) {
        $reply = (string)($decoded['candidates'][0]['content']['parts'][0]['text'] ?? '');
    }
    if ($http !== 200 || $reply === '') {
        error_log('[gemini_chat] HTTP=' . $http . ' ' . ($err !== '' ? $err : substr((string)$raw, 0, 300)));
        return gemini_fallback_reply($message, $input);
    }
    return clean_string($reply, 1400);
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

if (defined('DTH_API_LIBRARY_ONLY') && DTH_API_LIBRARY_ONLY) {
    return;
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

if ($action === 'sepay_webhook') {
    try {
        handle_sepay_webhook();
    } catch (Throwable $e) {
        api_exception_out($e);
    }
}

if ($action === 'momo_worker_payment') {
    try {
        handle_momo_worker_payment();
    } catch (Throwable $e) {
        api_exception_out($e);
    }
}

if ($action === 'momo_ipn') {
    try {
        handle_momo_ipn();
    } catch (Throwable $e) {
        api_exception_out($e);
    }
}

if (in_array($action, ['cron_worker_fee_notice', 'cron_worker_fee_lock', 'cron_baocao_ngay'], true)) {
    try {
        verify_cron_secret();
        $pdo = pdo();
        if ($action === 'cron_worker_fee_notice') {
            json_out(['status' => 'success', 'result' => notify_all_worker_debts($pdo, 'Nhac phi nen tang thu 2')]);
        }
        if ($action === 'cron_worker_fee_lock') {
            json_out(['status' => 'success', 'result' => lock_all_workers_with_debt($pdo)]);
        }
        json_out(['status' => 'success', 'result' => send_daily_business_report($pdo)]);
    } catch (Throwable $e) {
        api_exception_out($e);
    }
}

try {
    require_admin_for_action($action);
    $input = request_data();
    if ($action === 'gemini_chat') {
        json_out(['status' => 'success', 'reply' => gemini_quote_reply($input)]);
    }
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

    case 'app_services':
        json_out(['status' => 'success', 'data' => public_service_catalog()]);

    case 'app_book_job':
        $input['phone'] = $input['phone'] ?? $input['customer_phone'] ?? '';
        $input['service_type'] = $input['service_type'] ?? $input['service_name'] ?? '';
        $input['selected_service_name'] = $input['selected_service_name'] ?? $input['service_name'] ?? '';
        $input['issue_description'] = $input['issue_description'] ?? $input['description'] ?? $input['service_name'] ?? '';
        $input['estimated_price'] = $input['estimated_price'] ?? $input['price'] ?? 0;
        json_out(create_job_action($input));

    case 'app_job_status':
        $booking_id = (int)($input['booking_id'] ?? 0);
        $job = get_job_row($pdo, $booking_id);
        if (!$job) {
            json_out(['status' => 'error', 'message' => 'Khong tim thay don.'], 404);
        }
        $statusCode = job_display_status($job);
        $statusText = [
            'pending' => 'Chờ thợ nhận ca',
            'assigned' => 'Thợ đang đến',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'spam' => 'Yêu cầu không hợp lệ',
        ][$statusCode] ?? 'Đang cập nhật';
        $worker = null;
        $workerId = job_worker_telegram_id($job);
        if ($workerId > 0) {
            $profile = get_worker_profile($pdo, $workerId);
            $worker = [
                'id' => $workerId,
                'name' => (string)($profile['telegram_name'] ?? "Tho {$workerId}"),
                'phone' => (string)($profile['phone'] ?? ''),
                'rating_score' => (float)($profile['rating_score'] ?? 5.0),
                'rating_count' => (int)($profile['rating_count'] ?? 0),
            ];
        }
        json_out([
            'status' => 'success',
            'data' => [
                'status_code' => $statusCode,
                'status_text' => $statusText,
                'amount' => fmt_money((int)($job['final_total'] ?? $job['customer_total'] ?? 0)),
                'worker' => $worker
            ]
        ]);

    case 'app_rate_job':
        // API để App gửi đánh giá sau khi hoàn thành
        $booking_id = (int)($input['booking_id'] ?? 0);
        $rating = (int)($input['rating'] ?? 5);
        $stmt = $pdo->prepare('UPDATE job_posts SET review_score = ? WHERE id = ?');
        $stmt->execute([$rating, $booking_id]);
        json_out(['status' => 'success']);

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
                'discount_amount' => money_int($input['value'] ?? 0),
                'quantity_left' => 1,
                'description' => clean_string($input['description'] ?? '', 500),
                'is_used' => 0,
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

    case 'admin_daily_settlement_excel':
        output_daily_settlement_excel($pdo);

    case 'admin_notify_worker_fees':
        $result = notify_all_worker_debts($pdo, 'Admin nhac phi nen tang');
        json_out(['status' => 'success', 'message' => "Da gui nhac phi cho {$result['sent']} tho; loi {$result['failed']}.", 'result' => $result]);

    case 'admin_notify_worker_fee':
        $workerId = (int)($input['worker_id'] ?? 0);
        if ($workerId <= 0) {
            json_out(['status' => 'error', 'message' => 'Worker ID khong hop le.'], 400);
        }
        $result = send_worker_debt_notice($pdo, $workerId, 'Admin nhac phi nen tang');
        json_out(['status' => $result['ok'] ? 'success' : 'error'] + $result);

    case 'admin_enforce_worker_fee_lock':
        $result = lock_all_workers_with_debt($pdo);
        json_out(['status' => 'success', 'message' => "Da khoa {$result['locked']} tho con no.", 'result' => $result]);

    case 'admin_users':
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            if ((string)($row['login_key'] ?? '') === '') {
                update_compat($pdo, 'users', ['login_key' => customer_generate_login_key($pdo)], 'id = ?', [(int)$row['id']], ['updated_at' => 'NOW()']);
            }
        }
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
        json_out(['status' => 'success', 'data' => array_map('retail_customer_row', $stmt->fetchAll())]);

    case 'admin_customer_lookup':
        $phone = digits_only((string)($input['phone'] ?? $_GET['phone'] ?? ''));
        json_out(['status' => 'success', 'data' => retail_customer_by_phone($pdo, $phone, false)]);

    case 'admin_save_user':
        $id = (int)($input['id'] ?? 0);
        $role = clean_string($input['role'] ?? 'buyer', 30);
        $fullname = clean_string($input['fullname'] ?? '', 150);
        $phone = digits_only((string)($input['phone'] ?? ''));
        $isActive = (int)($input['is_active'] ?? 1);
        $memberRank = clean_string($input['member_rank'] ?? 'Thành viên', 50);
        $totalSpent = (int)($input['total_spent'] ?? 0);
        $loyaltyPoints = max(0, (int)($input['loyalty_points'] ?? 0));

        if ($fullname === '' || $phone === '') {
            json_out(['status' => 'error', 'message' => 'Tên và Số điện thoại không được để trống.'], 400);
        }

        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE users SET role = ?, fullname = ?, phone = ?, is_active = ?, member_rank = ?, total_spent = ?, loyalty_points = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$role, $fullname, $phone, $isActive, $memberRank, $totalSpent, $loyaltyPoints, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (role, fullname, phone, login_key, is_active, member_rank, total_spent, loyalty_points, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$role, $fullname, $phone, customer_generate_login_key($pdo), $isActive, $memberRank, $totalSpent, $loyaltyPoints]);
            $id = (int)$pdo->lastInsertId();
        }
        json_out(['status' => 'success', 'message' => 'Đã lưu khách hàng.', 'id' => $id]);

    case 'admin_delete_user':
        $id = (int)($input['id'] ?? 0);
        if ($id <= 0) {
            json_out(['status' => 'error', 'message' => 'ID không hợp lệ.'], 400);
        }
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        json_out(['status' => 'success', 'message' => 'Đã xóa khách hàng.']);

    case 'admin_orders':
        json_out(['status' => 'success', 'data' => admin_orders($pdo)]);

    case 'admin_sales_invoices':
        json_out(['status' => 'success', 'data' => admin_sales_invoice_rows($pdo), 'company' => invoice_company_profile()]);

    case 'admin_invoice_quote':
        try {
            json_out([
                'status' => 'success',
                'calculation' => manual_invoice_calculation($pdo, $input, false),
                'company' => invoice_company_profile(),
            ]);
        } catch (DomainException $e) {
            json_out(['status' => 'error', 'message' => $e->getMessage()], 409);
        } catch (InvalidArgumentException $e) {
            json_out(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

    case 'admin_create_sales_invoice':
        try {
            $invoice = create_manual_sales_invoice($pdo, $input);
            json_out(['status' => 'success', 'message' => 'Da tao hoa don ban hang.', 'invoice' => $invoice]);
        } catch (DomainException $e) {
            json_out(['status' => 'error', 'message' => $e->getMessage()], 409);
        } catch (InvalidArgumentException $e) {
            json_out(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

    case 'admin_retail_sale':
        try {
            $invoice = create_manual_sales_invoice($pdo, $input, true);
            json_out([
                'status' => 'success',
                'message' => 'Da ban hang, cong diem va tao hoa don dien tu.',
                'invoice' => $invoice,
            ]);
        } catch (DomainException $e) {
            json_out(['status' => 'error', 'message' => $e->getMessage()], 409);
        } catch (InvalidArgumentException $e) {
            json_out(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

    case 'admin_invoice':
        $orderId = (int)($input['order_id'] ?? $_GET['order_id'] ?? 0);
        $order = get_order_row($pdo, $orderId);
        if (!$order) {
            json_out(['status' => 'error', 'message' => 'Khong tim thay don hang.'], 404);
        }
        $invoiceCode = 'INV-' . date('Ymd') . '-' . str_pad((string)$orderId, 5, '0', STR_PAD_LEFT);
        $total = money_int($order['total_price'] ?? $order['total'] ?? 0);
        $subtotal = (int)round($total * 100 / 110);
        $vat = $total - $subtotal;
        $profile = invoice_company_profile();
        $pdo->prepare("INSERT IGNORE INTO invoices
            (invoice_code, order_id, customer_name, customer_phone, product_name, quantity, unit_gross_amount,
             gross_before_discount, discount_amount, promo_code, invoice_date, subtotal_amount, vat_amount, vat_rate,
             adjustment_amount, total_amount, total_price, company_name, company_tax_code, company_address,
             company_phone, company_email, company_website, status, created_at)
            VALUES (?, ?, ?, ?, ?, 1, ?, ?, 0, ?, CURDATE(), ?, ?, 10, 0, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())")->execute([
                $invoiceCode,
                $orderId,
                (string)($order['customer_name'] ?? ''),
                (string)($order['customer_phone'] ?? ''),
                (string)($order['product_name'] ?? ''),
                $total,
                $total,
                (string)($order['coupon_code'] ?? $order['voucher_code'] ?? ''),
                $subtotal,
                $vat,
                $total,
                $total,
                $profile['name'],
                $profile['tax_code'],
                $profile['address'],
                $profile['phone'],
                $profile['email'],
                $profile['website'],
            ]);
        update_compat($pdo, 'invoices', [
            'subtotal_amount' => $subtotal,
            'vat_amount' => $vat,
            'vat_rate' => 10,
            'total_amount' => $total,
            'total_price' => $total,
            'company_name' => $profile['name'],
            'company_tax_code' => $profile['tax_code'],
            'company_address' => $profile['address'],
            'company_phone' => $profile['phone'],
            'company_email' => $profile['email'],
            'company_website' => $profile['website'],
        ], 'invoice_code = ?', [$invoiceCode]);
        $order['invoice_code'] = $invoiceCode;
        $order['total_price'] = $total;
        $stmt = $pdo->prepare('SELECT * FROM invoices WHERE invoice_code = ? LIMIT 1');
        $stmt->execute([$invoiceCode]);
        json_out(['status' => 'success', 'order' => $order, 'invoice' => sales_invoice_row($stmt->fetch() ?: [])]);

    case 'admin_input_invoices':
        json_out(['status' => 'success', 'data' => admin_input_invoice_rows($pdo)]);

    case 'admin_upload_input_invoice':
        try {
            $saved = admin_save_input_invoice_pdf($pdo, $input, (array)($_FILES['pdf'] ?? []));
            json_out(['status' => 'success', 'message' => 'Da luu PDF hoa don dau vao va SHA-256.', 'data' => $saved]);
        } catch (DomainException $e) {
            json_out(['status' => 'error', 'message' => $e->getMessage()], 409);
        } catch (InvalidArgumentException $e) {
            json_out(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

    case 'admin_input_invoice_file':
        $invoiceId = (int)($input['id'] ?? $_GET['id'] ?? 0);
        if ($invoiceId <= 0) {
            json_out(['status' => 'error', 'message' => 'ID hoa don khong hop le.'], 400);
        }
        admin_stream_input_invoice($pdo, $invoiceId);

    case 'admin_bct_reconciliation':
        try {
            $from = clean_string($input['from'] ?? date('Y-01-01'), 10);
            $to = clean_string($input['to'] ?? date('Y-m-d'), 10);
            $includeDetails = (string)($input['detail'] ?? '1') !== '0';
            json_out(['status' => 'success', 'report' => bct_reconciliation_report($pdo, $from, $to, $includeDetails)]);
        } catch (InvalidArgumentException $e) {
            json_out(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

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

    case 'admin_get_stores':
        json_out(['status' => 'success', 'data' => admin_store_rows($pdo)]);

    case 'admin_settle_stores':
        json_out(admin_settle_stores_action($pdo));

    case 'admin_approve_store':
        json_out(admin_approve_store_action($pdo, $input));

    case 'store_public_report':
        store_public_report_action($pdo, $input);

    case 'admin_jobs':
        json_out(['status' => 'success', 'data' => admin_jobs($pdo)]);

    case 'admin_test_worker_job':
        $pricing = calculate_job_pricing(150000, 0, 1);
        $jobId = insert_repair_job($pdo, [
            'customer_name' => 'TEST BOT',
            'customer_phone' => clean_string($input['phone'] ?? '0979553289', 30),
            'service_type' => clean_string($input['service_type'] ?? 'Dien lanh - Test', 150),
            'address' => clean_string($input['address'] ?? 'Ap Binh Thanh 1, Lap Vo', 500),
            'map_lat' => isset($input['map_lat']) ? (float)$input['map_lat'] : 10.357422,
            'map_lng' => isset($input['map_lng']) ? (float)$input['map_lng'] : 105.522124,
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
        json_out(['status' => 'success', 'data' => admin_worker_rows($pdo)]);

    case 'admin_worker_payments':
        json_out(['status' => 'success', 'data' => admin_worker_payments($pdo)]);

    case 'admin_register_worker':
        $workerId = (int)digits_only($input['worker_id'] ?? '');
        $phone = digits_only($input['phone'] ?? '');
        $name = clean_string($input['name'] ?? "Ho kinh doanh {$workerId}", 150);
        if ($workerId <= 0 || strlen($phone) < 8 || $workerId === admin_telegram_id()) {
            json_out(['status' => 'error', 'message' => 'Telegram ID hoac so dien thoai khong hop le.'], 400);
        }
        $pdo->prepare("INSERT INTO worker_profiles (telegram_user_id, telegram_name, phone, identity_code, worker_type, role, is_admin, registered_by, created_at, updated_at)
            VALUES (?, ?, ?, ?, 'ho_kinh_doanh', 'worker', 0, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE telegram_name = VALUES(telegram_name), phone = VALUES(phone), identity_code = VALUES(identity_code),
                worker_type = 'ho_kinh_doanh', role = 'worker', is_admin = 0, registered_by = VALUES(registered_by), updated_at = NOW()")
            ->execute([$workerId, $name, $phone, (string)$workerId, admin_telegram_id()]);
        json_out(['status' => 'success', 'message' => 'Da dang ky/cap nhat tho.']);

    case 'admin_mark_worker_paid':
        $workerId = (int)($input['worker_id'] ?? 0);
        if ($workerId <= 0) {
            json_out(['status' => 'error', 'message' => 'Worker ID khong hop le.'], 400);
        }
        $debt = worker_fee_debt($pdo, $workerId);
        if ($debt <= 0) {
            json_out(['status' => 'success', 'message' => 'Tho khong con no phi nen tang.', 'remaining' => 0]);
        }
        $amount = money_int($input['amount'] ?? $debt);
        $method = clean_string($input['method'] ?? 'admin_manual', 40);
        $reference = clean_string($input['reference'] ?? ('ADMIN-' . date('YmdHis')), 150);
        $result = settle_worker_payment($pdo, $workerId, $amount, $method, $reference, 'admin_dashboard');
        json_out(['status' => $result['ok'] ? 'success' : 'error'] + $result);

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

    case 'app_store_counts':
        json_out(['status' => 'success', 'data' => app_store_counts($pdo)]);

    case 'app_store_register':
        json_out(app_store_register_action($pdo, $input));

    case 'app_store_login_qr':
        json_out(app_store_login_qr_action($pdo, $input));

    case 'app_store_login':
        if (isset($input['login_key']) || isset($input['qr_data']) || isset($input['key'])) {
            json_out(app_store_login_qr_action($pdo, $input));
        }
        json_out(app_store_register_action($pdo, $input));

    case 'app_customer_register':
        json_out(app_customer_register_action($pdo, $input));

    case 'app_customer_login_qr':
        json_out(app_customer_login_qr_action($pdo, $input));

    case 'app_services_legacy_products_disabled':
        $services = products_for_store($pdo, []);
        json_out(['status' => 'success', 'data' => $services]);

    case 'app_store_login_legacy_disabled':
        $phone = clean_string($input['phone'] ?? '', 30);
        $tax_code = clean_string($input['tax_code'] ?? '', 30);
        if ($phone === '' || $tax_code === '') {
            json_out(['status' => 'error', 'message' => 'Vui lòng nhập SĐT và Mã Số Thuế.']);
        }
        $ch = curl_init('https://api.vietqr.io/v2/business/' . urlencode($tax_code));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        $bizData = json_decode($res, true);
        if (!$bizData || ($bizData['code'] ?? '') !== '00') {
            json_out(['status' => 'error', 'message' => 'Mã Số Thuế không hợp lệ hoặc không tồn tại.']);
        }
        $store_name = clean_string($bizData['data']['name'] ?? 'Cửa hàng không tên', 150);
        $address = clean_string($bizData['data']['address'] ?? '', 255);
        $stmt = $pdo->prepare('SELECT * FROM marketplace_stores WHERE tax_code = ?');
        $stmt->execute([$tax_code]);
        $store = $stmt->fetch();
        if (!$store) {
            $lat = 10.3547 + (mt_rand(-50, 50) / 10000);
            $lng = 105.5298 + (mt_rand(-50, 50) / 10000);
            insert_compat($pdo, 'marketplace_stores', [
                'phone' => $phone,
                'tax_code' => $tax_code,
                'store_name' => $store_name,
                'address' => $address,
                'lat' => $lat,
                'lng' => $lng,
                'store_type' => 'Cửa hàng',
                'status' => 'pending',
                'report_token' => bin2hex(random_bytes(16))
            ], ['created_at' => 'NOW()']);
            $store_id = $pdo->lastInsertId();
            $store = [
                'id' => $store_id,
                'phone' => $phone,
                'tax_code' => $tax_code,
                'store_name' => $store_name,
                'address' => $address
            ];
        } else {
            $pdo->prepare('UPDATE marketplace_stores SET phone = ? WHERE id = ?')->execute([$phone, $store['id']]);
        }
        json_out(['status' => 'success', 'data' => $store]);

    case 'app_get_map_pins':
        $stmt = $pdo->query("SELECT id, store_name, lat, lng, store_type, address FROM marketplace_stores WHERE status = 'active'");
        $stores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_out(['status' => 'success', 'data' => $stores]);

    case 'app_store_menu':
        $store_id = (int)($input['store_id'] ?? 0);
        $stmt = $pdo->prepare("SELECT * FROM marketplace_products WHERE store_id = ? AND status = 'active'");
        $stmt->execute([$store_id]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_out(['status' => 'success', 'data' => $products]);

    case 'app_store_checkout':
        $store_id = (int)($input['store_id'] ?? 0);
        $customer_phone = clean_string($input['customer_phone'] ?? '', 30);
        $customer_address = clean_string($input['customer_address'] ?? '', 255);
        $total_amount = (int)($input['total_amount'] ?? 0);
        insert_compat($pdo, 'marketplace_orders', [
            'store_id' => $store_id,
            'customer_phone' => $customer_phone,
            'customer_address' => $customer_address,
            'total_amount' => $total_amount,
            'status' => 'pending'
        ], ['created_at' => 'NOW()']);
        $order_id = $pdo->lastInsertId();
        telegram_notify_worker("CO DON HANG CHO XA LAP VO!\nCua hang ID: $store_id\nSDT Khach: $customer_phone\nDia chi: $customer_address\nTong: " . number_format($total_amount) . " d");
        json_out(['status' => 'success', 'order_id' => $order_id]);

    case 'admin_approve_store':
        $store_id = (int)($input['store_id'] ?? 0);
        $pdo->prepare("UPDATE marketplace_stores SET status = 'active' WHERE id = ?")->execute([$store_id]);
        json_out(['status' => 'success', 'message' => 'Đã duyệt cửa hàng thành công']);

    case 'app_submit_rating':
        $target_type = $input['target_type'] ?? ''; // 'store' or 'worker'
        $target_id = (int)($input['target_id'] ?? 0);
        $stars = (int)($input['stars'] ?? 5);
        if ($stars < 1) $stars = 1;
        if ($stars > 5) $stars = 5;
        
        if ($target_type === 'store') {
            $pdo->prepare("UPDATE marketplace_stores SET rating_score = ((rating_score * rating_count) + ?) / (rating_count + 1), rating_count = rating_count + 1 WHERE id = ?")->execute([$stars, $target_id]);
        } else if ($target_type === 'worker') {
            $pdo->prepare("UPDATE worker_profiles SET rating_score = ((rating_score * rating_count) + ?) / (rating_count + 1), rating_count = rating_count + 1 WHERE telegram_user_id = ?")->execute([$stars, $target_id]);
        }
        json_out(['status' => 'success']);

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
