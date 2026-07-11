<?php
/**
 * Host Audit & Integrity Check
 * Dien May Hieu System
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Fix for CLI running
if (!isset($_SERVER['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = 'localhost';
}

$configPath = __DIR__ . '/core/config.php';
$config_exists = file_exists($configPath);
if ($config_exists) {
    define('IN_SITE', true);
    // Buffer output to catch any rogue echos from config.php
    ob_start();
    require_once($configPath);
    ob_end_clean();
}

header('Content-Type: text/html; charset=utf-8');
echo "<h2>KẾT QUẢ KIỂM TRA HỆ THỐNG (HOST AUDIT)</h2>";
echo "<ul>";

// 1. Kiểm tra CSDL
if (!$config_exists) {
    echo "<li><span style='color:red'>❌ THẤT BẠI</span>: Không tìm thấy file `core/config.php`.</li>";
} else {
    echo "<li><span style='color:green'>✅ THÀNH CÔNG</span>: Đã tìm thấy `core/config.php`.</li>";
    
    // Test connection without fully loading the app if possible, or just instantiate DMH
    try {
        $DMH = new DMH();
        $DMH->connect();
        echo "<li><span style='color:green'>✅ THÀNH CÔNG</span>: Kết nối Cơ sở dữ liệu hoạt động bình thường.</li>";
    } catch (Throwable $e) {
        echo "<li><span style='color:red'>❌ LỖI KẾT NỐI CSDL</span>: " . $e->getMessage() . "</li>";
    }
}

// 2. Kiểm tra thư mục cốt lõi
$coreDirs = ['core', 'assets', 'controller', 'pages', 'api'];
foreach ($coreDirs as $dir) {
    if (is_dir(__DIR__ . '/' . $dir)) {
        echo "<li><span style='color:green'>✅ THÀNH CÔNG</span>: Thư mục `$dir` tồn tại. Quyền (Permissions): " . substr(sprintf('%o', fileperms(__DIR__ . '/' . $dir)), -4) . "</li>";
    } else {
        echo "<li><span style='color:red'>❌ THẤT BẠI</span>: Thiếu thư mục cốt lõi `$dir`.</li>";
    }
}

// 3. Kiểm tra htaccess
if (file_exists(__DIR__ . '/.htaccess')) {
    echo "<li><span style='color:green'>✅ THÀNH CÔNG</span>: Đã tìm thấy file `.htaccess`. Đảm bảo URL Routing (như /dien-may) sẽ hoạt động.</li>";
} else {
    echo "<li><span style='color:red'>❌ CẢNH BÁO</span>: Không tìm thấy file `.htaccess`. URL Rewrite sẽ không hoạt động (lỗi 404).</li>";
}

echo "</ul>";
echo "<p><em>Audit hoàn tất lúc " . date('H:i:s d/m/Y') . "</em></p>";
?>
