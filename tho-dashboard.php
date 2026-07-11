<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");

// Bắt buộc đăng nhập với quyền thợ
if(!isset($_COOKIE['token']) || empty($getUser)) {
    header("Location: /login.php");
    exit;
}
if($getUser['level'] != 'tho' && $getUser['level'] != 'admin') {
    header("Location: /");
    exit;
}

$my_id = $getUser['id'];
$tho_name = $getUser['name'] ?? $getUser['username'];

// Tắt report exception của mysqli để không văng 500
mysqli_report(MYSQLI_REPORT_OFF);

// Lấy danh sách đơn chờ xử lý
$don_cho_xu_ly = [];
$don_cua_toi = [];
try {
    $res_cho = $DMH->get_list("SELECT * FROM `dat_lich` WHERE `trangthai` = 'CHO_XU_LY' ORDER BY `id` DESC");
    if (is_array($res_cho)) $don_cho_xu_ly = $res_cho;
} catch (Throwable $e) {
    die("Lỗi Database khi lấy đơn chờ xử lý: " . $e->getMessage() . ". Có thể thiếu cột 'trangthai' trong bảng 'dat_lich'. Vui lòng báo cho kỹ thuật.");
}

// Lấy danh sách đơn đang nhận của thợ này
try {
    $res_toi = $DMH->get_list("SELECT * FROM `dat_lich` WHERE `trangthai` = 'DANG_XU_LY' AND `tho_id` = '$my_id' ORDER BY `id` DESC");
    if (is_array($res_toi)) $don_cua_toi = $res_toi;
} catch (Throwable $e) {
    die("Lỗi Database khi lấy đơn của tôi: " . $e->getMessage());
}

$title = "Dashboard Thợ | Điện Máy Hiếu";
require_once(__DIR__."/pages/client/Head.php");
?>
<style>
    body { background: #0f172a; color: #e2e8f0; font-family: 'Inter', sans-serif; }
    .header-tho { background: #1e293b; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; }
    .header-tho .logo { font-size: 20px; font-weight: 700; color: #38bdf8; text-decoration: none; display: flex; align-items: center; gap: 10px; }
    .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
    .nav-tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid #334155; padding-bottom: 10px; }
    .nav-tabs button { background: none; border: none; color: #94a3b8; font-size: 16px; font-weight: 600; cursor: pointer; padding: 10px 15px; border-radius: 5px; transition: 0.2s; }
    .nav-tabs button.active { background: #38bdf8; color: #0f172a; }
    .nav-tabs button:hover:not(.active) { background: #1e293b; color: white; }
    
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    
    .order-card { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 20px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .order-header { display: flex; justify-content: space-between; border-bottom: 1px solid #334155; padding-bottom: 12px; margin-bottom: 12px; }
    .order-header h3 { margin: 0; color: #f8fafc; font-size: 18px; display: flex; align-items: center; gap: 8px; }
    .order-time { color: #94a3b8; font-size: 14px; }
    
    .order-body p { margin: 8px 0; font-size: 15px; line-height: 1.5; }
    .order-body strong { color: #cbd5e1; display: inline-block; width: 100px; }
    .service-badge { display: inline-block; background: rgba(56, 189, 248, 0.1); color: #38bdf8; padding: 4px 10px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid rgba(56, 189, 248, 0.2); }
    
    .order-actions { margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap; }
    .btn-action { padding: 10px 16px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 6px; text-decoration: none; transition: 0.2s; }
    .btn-nhan { background: #10b981; color: white; }
    .btn-nhan:hover { background: #059669; }
    .btn-map { background: #3b82f6; color: white; }
    .btn-map:hover { background: #2563eb; }
    .btn-call { background: #f59e0b; color: white; }
    .btn-call:hover { background: #d97706; }
    .btn-hoanthanh { background: #8b5cf6; color: white; }
    
    .empty-state { text-align: center; padding: 50px 20px; color: #94a3b8; }
    .empty-state i { font-size: 48px; margin-bottom: 15px; opacity: 0.5; }
</style>

<header class="header-tho">
    <a href="/" class="logo"><i class="fa-solid fa-tools"></i> Portal Thợ</a>
    <div style="display: flex; align-items: center; gap: 15px;">
        <span><i class="fa-solid fa-user"></i> <?= htmlspecialchars($tho_name) ?></span>
        <button onclick="document.cookie='token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'; location.href='/';" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); padding: 6px 12px; border-radius: 6px; cursor: pointer;">Đăng xuất</button>
    </div>
</header>

<div class="container">
    <div class="nav-tabs">
        <button class="active" onclick="switchTab('tab-cho')">Đơn Mới Chờ Nhận (<?= count($don_cho_xu_ly) ?>)</button>
        <button onclick="switchTab('tab-cuatoi')">Đơn Của Tôi (<?= count($don_cua_toi) ?>)</button>
    </div>
    
    <!-- TAB: ĐƠN CHỜ NHẬN -->
    <div id="tab-cho" class="tab-content active">
        <?php if(empty($don_cho_xu_ly)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-mug-hot"></i>
                <h3>Chưa có đơn hàng mới</h3>
                <p>Bạn có thể nghỉ ngơi một chút, khi có đơn mới hệ thống sẽ báo.</p>
            </div>
        <?php else: ?>
            <?php foreach($don_cho_xu_ly as $don): 
                // Phân tích GPS từ chuỗi địa chỉ nếu có
                $hasGps = false;
                $mapLink = "";
                if(strpos($don['diachi'], 'GPS:') !== false) {
                    $parts = explode('GPS:', $don['diachi']);
                    $addrOnly = trim($parts[0], " |");
                    $hasGps = true;
                    // Trích xuất link google maps
                    if(preg_match('/(https:\/\/maps\.google\.com\/\?q=[0-9\.\,\-]+)/', $parts[1], $matches)) {
                        $mapLink = $matches[1];
                    }
                } else {
                    $addrOnly = $don['diachi'];
                    $mapLink = "https://maps.google.com/?q=" . urlencode($addrOnly);
                }
            ?>
            <div class="order-card" id="don_<?= $don['id'] ?>">
                <div class="order-header">
                    <h3><i class="fa-solid fa-bolt" style="color: #fbbf24;"></i> Đơn #<?= $don['id'] ?></h3>
                    <span class="order-time"><i class="fa-regular fa-clock"></i> <?= date('H:i d/m', $don['thoigian']) ?></span>
                </div>
                <div class="order-body">
                    <p><span class="service-badge"><?= htmlspecialchars($don['dichvu']) ?></span></p>
                    <p><strong>Khách hàng:</strong> <?= htmlspecialchars($don['ten']) ?></p>
                    <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($don['sdt']) ?></p>
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($addrOnly) ?></p>
                    <p><strong>Tình trạng:</strong> <?= nl2br(htmlspecialchars($don['yeucau'])) ?></p>
                </div>
                <div class="order-actions">
                    <button class="btn-action btn-nhan" onclick="nhanDon(<?= $don['id'] ?>)"><i class="fa-solid fa-check"></i> Chốt Nhận Đơn Này</button>
                    <a href="<?= $mapLink ?>" target="_blank" class="btn-action btn-map"><i class="fa-solid fa-map-location-dot"></i> Xem Bản Đồ</a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- TAB: ĐƠN CỦA TÔI -->
    <div id="tab-cuatoi" class="tab-content">
        <?php if(empty($don_cua_toi)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-box-open"></i>
                <h3>Bạn chưa nhận đơn nào</h3>
                <p>Hãy qua mục "Đơn Mới" để chốt đơn nhé.</p>
            </div>
        <?php else: ?>
            <?php foreach($don_cua_toi as $don): 
                if(strpos($don['diachi'], 'GPS:') !== false) {
                    $parts = explode('GPS:', $don['diachi']);
                    $addrOnly = trim($parts[0], " |");
                    if(preg_match('/(https:\/\/maps\.google\.com\/\?q=[0-9\.\,\-]+)/', $parts[1], $matches)) {
                        $mapLink = $matches[1];
                    }
                } else {
                    $addrOnly = $don['diachi'];
                    $mapLink = "https://maps.google.com/?q=" . urlencode($addrOnly);
                }
            ?>
            <div class="order-card" style="border-left: 4px solid #10b981;">
                <div class="order-header">
                    <h3><i class="fa-solid fa-person-digging" style="color: #10b981;"></i> Đang Xử Lý - Đơn #<?= $don['id'] ?></h3>
                    <span class="order-time"><i class="fa-regular fa-clock"></i> <?= date('H:i d/m', $don['thoigian']) ?></span>
                </div>
                <div class="order-body">
                    <p><span class="service-badge"><?= htmlspecialchars($don['dichvu']) ?></span></p>
                    <p><strong>Khách hàng:</strong> <?= htmlspecialchars($don['ten']) ?></p>
                    <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($don['sdt']) ?></p>
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($addrOnly) ?></p>
                    <p><strong>Tình trạng:</strong> <?= nl2br(htmlspecialchars($don['yeucau'])) ?></p>
                </div>
                <div class="order-actions">
                    <a href="tel:<?= htmlspecialchars($don['sdt']) ?>" class="btn-action btn-call"><i class="fa-solid fa-phone"></i> Gọi Khách</a>
                    <a href="<?= $mapLink ?>" target="_blank" class="btn-action btn-map"><i class="fa-solid fa-location-arrow"></i> Dẫn Đường</a>
                    <!-- Tính năng hoàn thành đơn sẽ làm ở phase sau, hiện tại chỉ tập trung vào nhận đơn -->
                    <button class="btn-action" style="background: rgba(255,255,255,0.1); color: white;" onclick="alert('Tính năng cập nhật trạng thái Hoàn Thành đang được phát triển.')"><i class="fa-solid fa-clipboard-check"></i> Đánh dấu hoàn thành</button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Nạp jQuery và SweetAlert -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.nav-tabs button').forEach(b => b.classList.remove('active'));
    
    document.getElementById(tabId).classList.add('active');
    event.currentTarget.classList.add('active');
}

function nhanDon(id) {
    Swal.fire({
        title: 'Chốt đơn này?',
        text: "Bạn cam kết sẽ liên hệ và đến xử lý cho khách hàng?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Đồng ý nhận',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/controller/client/NhanDon.php',
                method: 'POST',
                data: { action: 'nhan_don', id: id },
                dataType: 'json',
                success: function(r) {
                    if(r.status == 'success') {
                        Swal.fire('Thành công!', r.msg, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Lỗi', r.msg, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Lỗi', 'Không thể kết nối máy chủ.', 'error');
                }
            });
        }
    });
}
</script>
</body>
</html>
