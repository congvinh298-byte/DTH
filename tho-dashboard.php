<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");

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

mysqli_report(MYSQLI_REPORT_OFF);

$don_cho_xu_ly = [];
$don_cua_toi = [];
$don_hoan_thanh = [];

try {
    $res_cho = $DMH->get_list("SELECT * FROM `dat_lich` WHERE `trangthai` = 'CHO_XU_LY' ORDER BY `id` DESC");
    if (is_array($res_cho)) $don_cho_xu_ly = $res_cho;
} catch (Throwable $e) {}

try {
    $res_toi = $DMH->get_list("SELECT * FROM `dat_lich` WHERE `trangthai` = 'DANG_XU_LY' AND `tho_id` = '$my_id' ORDER BY `id` DESC");
    if (is_array($res_toi)) $don_cua_toi = $res_toi;
} catch (Throwable $e) {}

try {
    $res_done = $DMH->get_list("SELECT * FROM `dat_lich` WHERE `trangthai` = 'HOAN_THANH' AND `tho_id` = '$my_id' ORDER BY `thoigian` DESC LIMIT 20");
    if (is_array($res_done)) $don_hoan_thanh = $res_done;
} catch (Throwable $e) {}

$title = "Dashboard Thợ | Điện Máy Hiếu";
require_once(__DIR__."/pages/client/Head.php");
?>
<style>
    body { background: #020617; color: #e2e8f0; font-family: 'Inter', sans-serif; min-height: 100vh; }
    
    .header-tho { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); position: sticky; top: 0; z-index: 100; }
    .header-tho .logo { font-size: 22px; font-weight: 900; color: #38bdf8; text-decoration: none; display: flex; align-items: center; gap: 12px; }
    
    .container { max-width: 1200px; margin: 0 auto; padding: 32px 20px; }
    
    .nav-tabs { display: flex; gap: 12px; margin-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 16px; overflow-x: auto; }
    .nav-tabs::-webkit-scrollbar { display: none; }
    .nav-tabs button { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-size: 15px; font-weight: 700; cursor: pointer; padding: 12px 20px; border-radius: 100px; transition: all 0.3s; white-space: nowrap; }
    .nav-tabs button.active { background: #38bdf8; color: #020617; border-color: #38bdf8; box-shadow: 0 4px 15px rgba(56, 189, 248, 0.3); }
    .nav-tabs button:hover:not(.active) { background: rgba(255,255,255,0.1); color: white; }
    .badge { display: inline-flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2); border-radius: 20px; padding: 2px 8px; margin-left: 6px; font-size: 12px; }
    .active .badge { background: rgba(0,0,0,0.15); }
    
    .tab-content { display: none; animation: fadeIn 0.4s ease forwards; }
    .tab-content.active { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    .order-card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); transition: all 0.3s; display: flex; flex-direction: column; position: relative; overflow: hidden; }
    .order-card:hover { transform: translateY(-4px); box-shadow: 0 15px 35px rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.2); }
    
    .order-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 16px; margin-bottom: 16px; }
    .order-header h3 { margin: 0; color: #f8fafc; font-size: 18px; font-weight: 800; display: flex; align-items: center; gap: 8px; }
    .order-time { color: #64748b; font-size: 13px; font-weight: 600; display: flex; flex-direction: column; align-items: flex-end; }
    
    .order-body p { margin: 10px 0; font-size: 14.5px; line-height: 1.6; display: flex; gap: 12px; }
    .order-body strong { color: #cbd5e1; min-width: 90px; }
    .service-badge { display: inline-block; background: rgba(56, 189, 248, 0.15); color: #38bdf8; padding: 6px 14px; border-radius: 100px; font-size: 13px; font-weight: 700; border: 1px solid rgba(56, 189, 248, 0.3); margin-bottom: 8px; }
    
    .order-actions { margin-top: auto; padding-top: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .order-actions.full { grid-template-columns: 1fr; }
    .btn-action { padding: 12px 16px; border-radius: 12px; border: none; font-weight: 700; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; transition: 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
    .btn-action:hover { transform: translateY(-2px); filter: brightness(1.1); }
    
    .btn-nhan { background: #10b981; color: white; grid-column: 1 / -1; }
    .btn-nhan:hover { box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3); }
    
    .btn-map { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59,130,246,0.3); }
    .btn-call { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
    
    .btn-hoanthanh { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; grid-column: 1 / -1; margin-top: 12px; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4); border-radius: 100px; padding: 14px; }
    .btn-hoanthanh:hover { box-shadow: 0 8px 25px rgba(139, 92, 246, 0.6); }
    
    .empty-state { grid-column: 1 / -1; text-align: center; padding: 80px 20px; background: rgba(30, 41, 59, 0.3); border-radius: 24px; border: 2px dashed rgba(255,255,255,0.05); }
    .empty-state i { font-size: 56px; margin-bottom: 20px; color: #475569; }
    .empty-state h3 { color: #f8fafc; font-size: 24px; margin: 0 0 10px; }
    .empty-state p { color: #94a3b8; font-size: 16px; margin: 0; }
    
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 800; letter-spacing: 0.5px; }
    .status-done { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); }
    
    @media (max-width: 600px) {
        .tab-content.active { grid-template-columns: 1fr; }
        .order-actions { grid-template-columns: 1fr; }
    }
</style>

<header class="header-tho">
    <a href="/" class="logo"><i class="fa-solid fa-tools"></i> Portal Thợ</a>
    <div style="display: flex; align-items: center; gap: 16px;">
        <span style="font-weight: 700; color: #f8fafc;"><i class="fa-solid fa-circle-user" style="color: #94a3b8; margin-right: 6px;"></i> <?= htmlspecialchars($tho_name) ?></span>
        <button onclick="document.cookie='token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'; location.href='/';" style="background: rgba(239,68,68,0.15); color: #fca5a5; border: 1px solid rgba(239,68,68,0.3); padding: 8px 16px; border-radius: 100px; font-weight: 700; cursor: pointer; transition: 0.2s;">Đăng xuất</button>
    </div>
</header>

<div class="container">
    <div class="nav-tabs">
        <button class="active" onclick="switchTab('tab-cho', this)">Đơn Mới Chờ Nhận <span class="badge"><?= count($don_cho_xu_ly) ?></span></button>
        <button onclick="switchTab('tab-cuatoi', this)">Đơn Đang Xử Lý <span class="badge"><?= count($don_cua_toi) ?></span></button>
        <button onclick="switchTab('tab-hoanthanh', this)">Đã Hoàn Thành <span class="badge"><?= count($don_hoan_thanh) ?></span></button>
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
                $hasGps = false;
                $mapLink = "";
                if(strpos($don['diachi'], 'GPS:') !== false) {
                    $parts = explode('GPS:', $don['diachi']);
                    $addrOnly = trim($parts[0], " |");
                    $hasGps = true;
                    if(preg_match('/(https:\/\/maps\.google\.com\/\?q=[0-9\.\,\-]+)/', $parts[1], $matches)) {
                        $mapLink = $matches[1];
                    }
                } else {
                    $addrOnly = $don['diachi'];
                    $mapLink = "https://maps.google.com/?q=" . urlencode($addrOnly);
                }
            ?>
            <div class="order-card" id="don_<?= $don['id'] ?>" style="border-top: 4px solid #f59e0b;">
                <div class="order-header">
                    <h3><i class="fa-solid fa-bolt" style="color: #f59e0b;"></i> Mới #<?= $don['id'] ?></h3>
                    <span class="order-time">
                        <span style="color: #e2e8f0;"><?= date('H:i', $don['thoigian']) ?></span>
                        <small><?= date('d/m/Y', $don['thoigian']) ?></small>
                    </span>
                </div>
                <div class="order-body">
                    <div><span class="service-badge"><?= htmlspecialchars($don['dichvu']) ?></span></div>
                    <p><strong>Khách hàng:</strong> <span style="color: #fff; font-weight: 600;"><?= htmlspecialchars($don['ten']) ?></span></p>
                    <p><strong>Điện thoại:</strong> <a href="tel:<?= htmlspecialchars($don['sdt']) ?>" style="color: #38bdf8; font-weight: 600;"><?= htmlspecialchars($don['sdt']) ?></a></p>
                    <p><strong>Địa chỉ:</strong> <span><?= htmlspecialchars($addrOnly) ?></span></p>
                    <p><strong>Tình trạng:</strong> <span style="background: rgba(255,255,255,0.05); padding: 8px 12px; border-radius: 8px; flex: 1;"><?= nl2br(htmlspecialchars($don['yeucau'])) ?></span></p>
                </div>
                <div class="order-actions">
                    <a href="<?= $mapLink ?>" target="_blank" class="btn-action btn-map"><i class="fa-solid fa-map-location-dot"></i> Xem Bản Đồ</a>
                    <button class="btn-action btn-nhan" onclick="nhanDon(<?= $don['id'] ?>)"><i class="fa-solid fa-check-double"></i> Chốt Nhận Đơn Này</button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- TAB: ĐƠN ĐANG XỬ LÝ -->
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
            <div class="order-card" style="border-top: 4px solid #38bdf8;">
                <div class="order-header">
                    <h3><i class="fa-solid fa-person-digging" style="color: #38bdf8;"></i> Đang Xử Lý #<?= $don['id'] ?></h3>
                    <span class="order-time">
                        <span style="color: #e2e8f0;"><?= date('H:i', $don['thoigian']) ?></span>
                        <small><?= date('d/m/Y', $don['thoigian']) ?></small>
                    </span>
                </div>
                <div class="order-body">
                    <div><span class="service-badge"><?= htmlspecialchars($don['dichvu']) ?></span></div>
                    <p><strong>Khách hàng:</strong> <span style="color: #fff; font-weight: 600;"><?= htmlspecialchars($don['ten']) ?></span></p>
                    <p><strong>Điện thoại:</strong> <span style="color: #fff;"><?= htmlspecialchars($don['sdt']) ?></span></p>
                    <p><strong>Địa chỉ:</strong> <span><?= htmlspecialchars($addrOnly) ?></span></p>
                    <p><strong>Tình trạng:</strong> <span style="background: rgba(255,255,255,0.05); padding: 8px 12px; border-radius: 8px; flex: 1;"><?= nl2br(htmlspecialchars($don['yeucau'])) ?></span></p>
                </div>
                <div class="order-actions">
                    <a href="tel:<?= htmlspecialchars($don['sdt']) ?>" class="btn-action btn-call"><i class="fa-solid fa-phone"></i> Gọi Khách</a>
                    <a href="<?= $mapLink ?>" target="_blank" class="btn-action btn-map"><i class="fa-solid fa-location-arrow"></i> Dẫn Đường</a>
                    <button class="btn-action btn-hoanthanh" onclick="hoanThanhDon(<?= $don['id'] ?>)"><i class="fa-solid fa-clipboard-check"></i> Đánh Dấu Hoàn Thành</button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- TAB: ĐÃ HOÀN THÀNH -->
    <div id="tab-hoanthanh" class="tab-content">
        <?php if(empty($don_hoan_thanh)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-clipboard-check" style="opacity: 0.3;"></i>
                <h3>Chưa có đơn hoàn thành</h3>
                <p>Những đơn bạn đánh dấu hoàn thành sẽ nằm ở đây.</p>
            </div>
        <?php else: ?>
            <?php foreach($don_hoan_thanh as $don): 
                $addrOnly = (strpos($don['diachi'], 'GPS:') !== false) ? trim(explode('GPS:', $don['diachi'])[0], " |") : $don['diachi'];
            ?>
            <div class="order-card" style="opacity: 0.8; border-top: 4px solid #10b981;">
                <div class="order-header">
                    <h3><i class="fa-solid fa-check-circle" style="color: #10b981;"></i> Đơn #<?= $don['id'] ?></h3>
                    <span class="status-badge status-done">HOÀN THÀNH</span>
                </div>
                <div class="order-body">
                    <div><span class="service-badge" style="background: transparent; border-color: rgba(255,255,255,0.1); color: #94a3b8;"><?= htmlspecialchars($don['dichvu']) ?></span></div>
                    <p style="color: #94a3b8;"><strong>Khách hàng:</strong> <?= htmlspecialchars($don['ten']) ?></p>
                    <p style="color: #94a3b8;"><strong>Địa chỉ:</strong> <?= htmlspecialchars($addrOnly) ?></p>
                    <p style="color: #94a3b8;"><strong>Lúc nhận:</strong> <?= date('H:i d/m/Y', $don['thoigian']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.nav-tabs button').forEach(b => b.classList.remove('active'));
    
    document.getElementById(tabId).classList.add('active');
    btn.classList.add('active');
}

function nhanDon(id) {
    Swal.fire({
        title: 'Chốt đơn này?',
        text: "Bạn cam kết sẽ liên hệ và đến xử lý cho khách hàng?",
        icon: 'question',
        showCancelButton: true,
        background: '#1e293b',
        color: '#fff',
        confirmButtonColor: '#10b981',
        cancelButtonColor: 'rgba(255,255,255,0.1)',
        confirmButtonText: 'Đồng ý nhận',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Đang xử lý...',
                background: '#1e293b',
                color: '#fff',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            $.ajax({
                url: '/controller/client/NhanDon.php',
                method: 'POST',
                data: { action: 'nhan_don', id: id },
                dataType: 'json',
                success: function(r) {
                    if(r.status == 'success') {
                        Swal.fire({title: 'Thành công!', text: r.msg, icon: 'success', background: '#1e293b', color: '#fff'}).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({title: 'Lỗi', text: r.msg, icon: 'error', background: '#1e293b', color: '#fff'});
                    }
                },
                error: function() {
                    Swal.fire({title: 'Lỗi', text: 'Không thể kết nối máy chủ.', icon: 'error', background: '#1e293b', color: '#fff'});
                }
            });
        }
    });
}

function hoanThanhDon(id) {
    Swal.fire({
        title: 'Xác nhận hoàn thành?',
        text: "Đơn hàng này đã được xử lý xong và thu tiền thành công?",
        icon: 'success',
        showCancelButton: true,
        background: '#1e293b',
        color: '#fff',
        confirmButtonColor: '#8b5cf6',
        cancelButtonColor: 'rgba(255,255,255,0.1)',
        confirmButtonText: 'Đã Xong!',
        cancelButtonText: 'Chưa xong'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Đang lưu...',
                background: '#1e293b',
                color: '#fff',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            $.ajax({
                url: '/controller/client/HoanThanhDon.php',
                method: 'POST',
                data: { action: 'hoan_thanh', id: id },
                dataType: 'json',
                success: function(r) {
                    if(r.status == 'success') {
                        Swal.fire({title: 'Tuyệt vời!', text: r.msg, icon: 'success', background: '#1e293b', color: '#fff'}).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({title: 'Lỗi', text: r.msg, icon: 'error', background: '#1e293b', color: '#fff'});
                    }
                },
                error: function() {
                    Swal.fire({title: 'Lỗi', text: 'Không thể kết nối máy chủ.', icon: 'error', background: '#1e293b', color: '#fff'});
                }
            });
        }
    });
}
</script>
</body>
</html>
