<?php
/**
 * Công cụ đồng bộ mã nguồn 1-Click từ Github (DTH)
 * Code bởi Antigravity Agent
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(300); // Allow up to 5 minutes for download and extraction

$repoUrl = 'https://github.com/congvinh298-byte/DTH/archive/refs/heads/main.zip';
$zipFile = __DIR__ . '/dth_master_update.zip';
$extractPath = __DIR__;
$subFolder = 'DTH-main';

echo "<h1>Đang tiến hành đồng bộ mã nguồn từ Github...</h1>";

// 1. Download ZIP
echo "<p>1. Đang tải mã nguồn mới nhất...</p>";
$zipContent = file_get_contents($repoUrl);
if ($zipContent === false) {
    die("<p style='color:red'>LỖI: Không thể tải mã nguồn từ Github. Kiểm tra lại kết nối mạng của máy chủ.</p>");
}
file_put_contents($zipFile, $zipContent);
echo "<p style='color:green'>Tải xuống thành công (" . round(filesize($zipFile) / 1024 / 1024, 2) . " MB).</p>";

// 2. Extract ZIP
echo "<p>2. Đang giải nén mã nguồn...</p>";
$zip = new ZipArchive;
$res = $zip->open($zipFile);
if ($res === TRUE) {
    $zip->extractTo($extractPath);
    $zip->close();
    echo "<p style='color:green'>Giải nén thành công.</p>";
} else {
    die("<p style='color:red'>LỖI: Không thể giải nén file ZIP. Mã lỗi: $res</p>");
}

// 3. Move files from subfolder to root
echo "<p>3. Đang cập nhật các file (ghi đè file cũ)...</p>";
$srcDir = $extractPath . '/' . $subFolder;
if (is_dir($srcDir)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($srcDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    $movedCount = 0;
    foreach ($iterator as $item) {
        $destPath = str_replace($srcDir, $extractPath, $item->getPathname());
        
        if ($item->isDir()) {
            if (!is_dir($destPath)) {
                mkdir($destPath, 0755, true);
            }
        } else {
            // Ensure parent directory exists before renaming
            $parentDir = dirname($destPath);
            if (!is_dir($parentDir)) {
                mkdir($parentDir, 0755, true);
            }
            if (rename($item->getPathname(), $destPath)) {
                $movedCount++;
            }
        }
    }
    
    // Clean up empty directories in extracted folder
    foreach ($iterator as $item) {
        if ($item->isDir()) {
            @rmdir($item->getPathname());
        }
    }
    @rmdir($srcDir);
    echo "<p style='color:green'>Đã cập nhật thành công $movedCount file.</p>";
} else {
    echo "<p style='color:orange'>Cảnh báo: Không tìm thấy thư mục giải nén $subFolder.</p>";
}

// 4. Cleanup ZIP
echo "<p>4. Dọn dẹp file tạm...</p>";
@unlink($zipFile);

// 5. Special Fix for the stubborn logo (just in case it still causes issues)
$stubbornLogo = $extractPath . '/public/assets/logo.png';
if (file_exists($stubbornLogo)) {
    @chmod(dirname($stubbornLogo), 0755);
    @chmod($stubbornLogo, 0666);
    // Codebase might not have this file anymore, so we don't necessarily delete it here 
    // unless it's blocking something. The new index doesn't use it anyway.
}

echo "<h2>🎉 ĐỒNG BỘ HOÀN TẤT!</h2>";
echo "<p>Giao diện mới (Xanh Navy) và toàn bộ tính năng đã được cập nhật.</p>";
echo "<p><a href='/' style='padding: 10px 20px; background: #0a192f; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;'>Quay lại trang chủ</a></p>";
?>
