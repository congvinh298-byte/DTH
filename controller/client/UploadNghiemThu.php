<?php
/**
 * Controller: Upload ảnh nghiệm thu từ thiết bị thợ
 *
 * POST multipart/form-data:
 *   anh (file)  — File ảnh (jpg/jpeg/png/webp, max 5MB)
 *   id  (int)   — ID đơn dat_lich (optional, để validate quyền)
 *
 * RBAC: Chỉ tho/admin.
 * Response: JSON { status, msg, url: 'đường dẫn ảnh trên server' }
 */
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");

header('Content-Type: application/json; charset=utf-8');

// RBAC Check
if (!isset($_COOKIE['token']) || empty($getUser)) {
    echo json_encode(['status' => 'error', 'msg' => 'Chưa đăng nhập']);
    exit;
}
if ($getUser['level'] != 'tho' && $getUser['level'] != 'admin') {
    echo json_encode(['status' => 'error', 'msg' => 'Không có quyền upload']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['anh'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Không có file được gửi lên']);
    exit;
}

$file = $_FILES['anh'];

// Kiểm tra lỗi upload
if ($file['error'] !== UPLOAD_ERR_OK) {
    $errors = [
        UPLOAD_ERR_INI_SIZE   => 'File quá lớn (vượt giới hạn server)',
        UPLOAD_ERR_FORM_SIZE  => 'File quá lớn (vượt giới hạn form)',
        UPLOAD_ERR_PARTIAL    => 'File chỉ được upload một phần',
        UPLOAD_ERR_NO_FILE    => 'Không có file nào được chọn',
        UPLOAD_ERR_NO_TMP_DIR => 'Thiếu thư mục tạm',
        UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file',
    ];
    $errMsg = $errors[$file['error']] ?? 'Lỗi upload không xác định';
    echo json_encode(['status' => 'error', 'msg' => $errMsg]);
    exit;
}

// Validate kích thước (tối đa 5MB)
$maxSize = 5 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    echo json_encode(['status' => 'error', 'msg' => 'Ảnh quá lớn, vui lòng chọn ảnh dưới 5MB']);
    exit;
}

// Validate MIME type thực sự (không dựa vào extension)
$allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$realMime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($realMime, $allowedMimes)) {
    echo json_encode(['status' => 'error', 'msg' => 'Chỉ chấp nhận ảnh JPG, PNG, WEBP, GIF']);
    exit;
}

// Tạo thư mục lưu ảnh theo tháng (để không bị quá nhiều file 1 folder)
$uploadDir = __DIR__ . '/../../images/nghiemthu/' . date('Y-m') . '/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Tạo tên file an toàn: tho_id + timestamp + random
$ext      = ($realMime === 'image/png') ? 'png' : (($realMime === 'image/webp') ? 'webp' : 'jpg');
$filename = 'nt_' . (int)$getUser['id'] . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$savePath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $savePath)) {
    echo json_encode(['status' => 'error', 'msg' => 'Không thể lưu ảnh, vui lòng thử lại']);
    exit;
}

// Đường dẫn public trả về
$publicUrl = '/images/nghiemthu/' . date('Y-m') . '/' . $filename;

echo json_encode([
    'status' => 'success',
    'msg'    => 'Upload ảnh nghiệm thu thành công',
    'url'    => $publicUrl
]);
?>
