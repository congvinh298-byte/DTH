<?php
/**
 * REST API v1: Upload ảnh nghiệm thu & Lưu nghiệm thu
 * 
 * POST multipart/form-data:
 *   id (int) — ID đơn dat_lich
 *   anh (file) — File ảnh
 *   note (string, optional) — Ghi chú nghiệm thu
 */
define("IN_SITE", true);
require_once(__DIR__."/../../v1/tho/_auth.php");

$user = requireThoAuth();
$tho_id = (int)$user['user_id'];

if (empty($_FILES['anh'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Không có file ảnh được gửi lên']);
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$note = check_string($_POST['note'] ?? '');
$file = $_FILES['anh'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi upload ảnh (Mã: ' . $file['error'] . ')']);
    exit;
}

if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['status' => 'error', 'msg' => 'Kích thước ảnh quá lớn, tối đa 5MB']);
    exit;
}

$allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$realMime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($realMime, $allowedMimes)) {
    echo json_encode(['status' => 'error', 'msg' => 'Chỉ chấp nhận ảnh JPG, PNG, WEBP']);
    exit;
}

$uploadDir = __DIR__ . '/../../../images/nghiemthu/' . date('Y-m') . '/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$ext = ($realMime === 'image/png') ? 'png' : (($realMime === 'image/webp') ? 'webp' : 'jpg');
$filename = 'nt_' . $tho_id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$savePath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $savePath)) {
    echo json_encode(['status' => 'error', 'msg' => 'Không thể lưu ảnh lên server']);
    exit;
}

$publicUrl = '/images/nghiemthu/' . date('Y-m') . '/' . $filename;

if ($id > 0) {
    $where = "`id` = '$id' AND `trangthai` = 'DANG_XU_LY'";
    if ($user['level'] !== 'admin') {
        $where .= " AND `tho_id` = '$tho_id'";
    }
    $DMH->update("dat_lich", [
        'nghiemthu_anh'  => $publicUrl,
        'nghiemthu_note' => $note
    ], $where);
}

echo json_encode([
    'status' => 'success',
    'msg'    => 'Upload ảnh nghiệm thu thành công',
    'url'    => $publicUrl
]);
?>
