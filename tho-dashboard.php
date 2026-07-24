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

    /* Notification pulse khi có đơn mới */
    @keyframes pulse-ring { 0%{transform:scale(.8);opacity:1} 100%{transform:scale(2);opacity:0} }
    .notif-dot { position:relative; display:inline-flex; }
    .notif-dot::after { content:''; position:absolute; top:-3px; right:-3px; width:10px; height:10px; background:#f59e0b; border-radius:50%; animation:pulse-ring 1.2s ease-out infinite; }
    
    /* Upload ảnh nghiệm thu */
    .upload-preview { width:100%; height:160px; background:#0f172a; border:2px dashed rgba(139,92,246,0.4); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-direction:column; gap:8px; color:#6b7280; cursor:pointer; transition:0.3s; overflow:hidden; position:relative; }
    .upload-preview:hover { border-color:#8b5cf6; color:#a78bfa; }
    .upload-preview img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; border-radius:8px; }
    .upload-preview .upload-overlay { position:absolute; inset:0; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; opacity:0; transition:0.3s; border-radius:8px; }
    .upload-preview:hover .upload-overlay { opacity:1; }
    
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
                    <button class="btn-action" onclick="baoGiaPhatSinh(<?= $don['id'] ?>)" style="background: rgba(245,158,11,0.15); color:#fbbf24; border:1px solid rgba(245,158,11,0.3);"><i class="fa-solid fa-file-invoice-dollar"></i> Báo giá phát sinh</button>
                    <button class="btn-action" onclick="nghiemThu(<?= $don['id'] ?>)" style="background: rgba(99,102,241,0.15); color:#a5b4fc; border:1px solid rgba(99,102,241,0.3);"><i class="fa-solid fa-camera"></i> Nghiệm thu</button>
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

<!-- Modal Báo giá phát sinh -->
<div id="modalBaogia" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#1e293b; border:1px solid rgba(255,255,255,0.1); border-radius:16px; padding:24px; width:90%; max-width:420px; color:#fff;">
        <div style="font-weight:800; font-size:18px; margin-bottom:12px;">📝 Báo giá phát sinh</div>
        <input type="hidden" id="bg_id">
        <label style="display:block; margin-bottom:6px; color:#94a3b8; font-size:13px;">Mô tả vật tư / công việc phát sinh</label>
        <textarea id="bg_mota" rows="3" style="width:100%; background:#0f172a; border:1px solid rgba(255,255,255,0.1); color:#fff; padding:12px; border-radius:8px; margin-bottom:12px;"></textarea>
        <label style="display:block; margin-bottom:6px; color:#94a3b8; font-size:13px;">Giá phát sinh (VND)</label>
        <input type="number" id="bg_gia" style="width:100%; background:#0f172a; border:1px solid rgba(255,255,255,0.1); color:#fff; padding:12px; border-radius:8px; margin-bottom:16px;">
        <div style="display:flex; gap:10px; justify-content:flex-end;">
            <button onclick="closeModal('modalBaogia')" style="padding:8px 16px; border-radius:8px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#fff; cursor:pointer;">Đóng</button>
            <button onclick="saveBaogia()" style="padding:8px 16px; border-radius:8px; border:none; background:#38bdf8; color:#0f172a; font-weight:800; cursor:pointer;">Gửi báo giá</button>
        </div>
    </div>
</div>

<!-- Modal Nghiệm thu — Upload ảnh từ camera -->
<div id="modalNghiemthu" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.75); z-index:1000; align-items:center; justify-content:center; padding:16px;">
    <div style="background:#1e293b; border:1px solid rgba(139,92,246,0.3); border-radius:20px; padding:24px; width:100%; max-width:440px; color:#fff; max-height:90vh; overflow-y:auto;">
        <div style="font-weight:800; font-size:18px; margin-bottom:4px;">📸 Nghiệm thu công việc</div>
        <p style="color:#64748b; font-size:13px; margin:0 0 16px;">Chụp ảnh trực tiếp hoặc chọn từ thư viện</p>
        <input type="hidden" id="nt_id">
        
        <!-- Khu vực upload ảnh -->
        <label for="nt_file_input" class="upload-preview" id="nt_preview_area">
            <i class="fa-solid fa-camera-retro" style="font-size:36px;"></i>
            <span style="font-size:13px; font-weight:700;">Nhấn để chụp / chọn ảnh</span>
            <span style="font-size:11px; color:#4b5563;">JPG, PNG, WEBP · Tối đa 5MB</span>
            <div class="upload-overlay"><i class="fa-solid fa-rotate" style="font-size:24px; color:#fff;"></i></div>
        </label>
        <!-- capture=environment → mở camera sau trên điện thoại -->
        <input type="file" id="nt_file_input" accept="image/*" capture="environment" style="display:none;" onchange="previewNghiemThuFile(this)">
        <input type="hidden" id="nt_anh_url">
        
        <label style="display:block; margin:14px 0 6px; color:#94a3b8; font-size:13px;">Ghi chú nghiệm thu</label>
        <textarea id="nt_note" rows="2" placeholder="Mô tả công việc đã làm..." style="width:100%; background:#0f172a; border:1px solid rgba(255,255,255,0.1); color:#fff; padding:12px; border-radius:8px; margin-bottom:16px; resize:none; font-size:14px;"></textarea>
        
        <div id="nt_upload_progress" style="display:none; margin-bottom:12px;">
            <div style="background:#0f172a; border-radius:8px; overflow:hidden; height:6px;">
                <div id="nt_progress_bar" style="height:100%; background:linear-gradient(90deg,#8b5cf6,#a78bfa); width:0%; transition:width 0.3s;"></div>
            </div>
            <p style="color:#94a3b8; font-size:12px; margin-top:6px; text-align:center;">Đang upload ảnh...</p>
        </div>
        
        <div style="display:flex; gap:10px; justify-content:flex-end;">
            <button onclick="closeModal('modalNghiemthu')" style="padding:10px 18px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#fff; cursor:pointer; font-weight:600;">Đóng</button>
            <button id="btn_save_nghiemthu" onclick="saveNghiemthu()" style="padding:10px 20px; border-radius:10px; border:none; background:#8b5cf6; color:#fff; font-weight:800; cursor:pointer; display:flex; align-items:center; gap:8px;"><i class="fa-solid fa-cloud-arrow-up"></i> Lưu nghiệm thu</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// =====================================================
// POLLING — Kiểm tra đơn mới mỗi 30 giây
// =====================================================
var _lastMaxId = <?= !empty($don_cho_xu_ly) ? (int)$don_cho_xu_ly[0]['id'] : 0 ?>;
var _lastCount = <?= count($don_cho_xu_ly) ?>;
var _pollingActive = true;

// Tạo audio cảnh báo (beep tổng hợp bằng Web Audio API)
function playAlert() {
    try {
        var ctx = new (window.AudioContext || window.webkitAudioContext)();
        [0, 0.15, 0.3].forEach(function(t) {
            var osc = ctx.createOscillator();
            var gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, ctx.currentTime + t);
            gain.gain.setValueAtTime(0.4, ctx.currentTime + t);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + t + 0.12);
            osc.start(ctx.currentTime + t);
            osc.stop(ctx.currentTime + t + 0.15);
        });
    } catch(e) {}
}

function checkNewOrders() {
    if (!_pollingActive) return;
    fetch('/api/tho_check_new.php', { credentials: 'include' })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.status !== 'success') return;
            var newCount = data.count;
            var newMaxId = data.max_id;

            // Cập nhật badge số đơn trên tab
            var tabBadge = document.querySelector('#tab-cho');
            var navBadge = document.querySelector('.nav-tabs button:first-child .badge');
            if (navBadge) navBadge.textContent = newCount;

            // Phát alert nếu có đơn mới xuất hiện
            if (newMaxId > _lastMaxId && _lastMaxId > 0) {
                playAlert();
                var added = newCount - _lastCount;
                Swal.fire({
                    title: '🔔 Có đơn mới!',
                    html: '<p style="font-size:16px; color:#e2e8f0;">Vừa có <b style="color:#f59e0b;">' + (added > 0 ? added : 1) + ' đơn mới</b> đang chờ nhận.<br>Hãy vào tab <b>Đơn Mới</b> để nhận.</p>',
                    icon: 'info',
                    background: '#1e293b',
                    color: '#fff',
                    confirmButtonColor: '#f59e0b',
                    confirmButtonText: 'Xem ngay',
                    timer: 12000,
                    timerProgressBar: true,
                    showCancelButton: true,
                    cancelButtonText: 'Để sau',
                    cancelButtonColor: 'rgba(100,116,139,0.4)'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            }
            _lastMaxId = newMaxId;
            _lastCount = newCount;
        })
        .catch(function() {}); // Silent fail khi mất mạng
}

// Bắt đầu polling sau 30 giây
setTimeout(function startPolling() {
    checkNewOrders();
    setTimeout(startPolling, 30000);
}, 30000);

// =====================================================
// TAB & MODAL HELPERS
// =====================================================
function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.nav-tabs button').forEach(b => b.classList.remove('active'));
    
    document.getElementById(tabId).classList.add('active');
    btn.classList.add('active');
}

function openModal(id) { document.getElementById(id).style.display = 'flex'; }
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

function baoGiaPhatSinh(id) {
    document.getElementById('bg_id').value = id;
    document.getElementById('bg_mota').value = '';
    document.getElementById('bg_gia').value = '';
    openModal('modalBaogia');
}
function saveBaogia() {
    var id = document.getElementById('bg_id').value;
    var mota = document.getElementById('bg_mota').value.trim();
    var gia = document.getElementById('bg_gia').value.trim();
    if(!mota || !gia) return Swal.fire('Thiếu thông tin', 'Vui lòng nhập đầy đủ mô tả và giá', 'warning');
    $.ajax({
        url: '/controller/client/BaoGiaPhatSinh.php',
        method: 'POST',
        data: { id: id, mota: mota, gia: gia },
        dataType: 'json',
        success: function(r) {
            if(r.status == 'success') {
                Swal.fire('Thành công', r.msg, 'success').then(() => location.reload());
            } else {
                Swal.fire('Lỗi', r.msg, 'error');
            }
        },
        error: function() { Swal.fire('Lỗi', 'Không thể kết nối máy chủ', 'error'); }
    });
    closeModal('modalBaogia');
}

// =====================================================
// NGHIỆM THU — Upload ảnh từ camera
// =====================================================
function nghiemThu(id) {
    document.getElementById('nt_id').value = id;
    document.getElementById('nt_anh_url').value = '';
    document.getElementById('nt_note').value = '';
    document.getElementById('nt_file_input').value = '';
    // Reset preview
    var area = document.getElementById('nt_preview_area');
    area.innerHTML = '<i class="fa-solid fa-camera-retro" style="font-size:36px;"></i>' +
        '<span style="font-size:13px; font-weight:700;">Nhấn để chụp / chọn ảnh</span>' +
        '<span style="font-size:11px; color:#4b5563;">JPG, PNG, WEBP · Tối đa 5MB</span>' +
        '<div class="upload-overlay"><i class="fa-solid fa-rotate" style="font-size:24px; color:#fff;"></i></div>';
    document.getElementById('nt_upload_progress').style.display = 'none';
    openModal('modalNghiemthu');
}

function previewNghiemThuFile(input) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    var area = document.getElementById('nt_preview_area');
    var reader = new FileReader();
    reader.onload = function(e) {
        area.innerHTML = '<img src="' + e.target.result + '" alt="preview">' +
            '<div class="upload-overlay"><i class="fa-solid fa-rotate" style="font-size:24px; color:#fff;"></i><span style="color:#fff;font-size:12px;margin-top:4px;">Đổi ảnh</span></div>';
    };
    reader.readAsDataURL(file);
}

function saveNghiemthu() {
    var id   = document.getElementById('nt_id').value;
    var note = document.getElementById('nt_note').value.trim();
    var fileInput = document.getElementById('nt_file_input');
    var existingUrl = document.getElementById('nt_anh_url').value;

    if (!fileInput.files || !fileInput.files[0]) {
        return Swal.fire('Thiếu ảnh', 'Vui lòng chụp hoặc chọn ảnh nghiệm thu', 'warning');
    }

    var btn = document.getElementById('btn_save_nghiemthu');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang upload...';
    document.getElementById('nt_upload_progress').style.display = 'block';

    // Bước 1: Upload ảnh lên server
    var formData = new FormData();
    formData.append('anh', fileInput.files[0]);
    formData.append('id', id);

    var xhr = new XMLHttpRequest();
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            var pct = Math.round(e.loaded / e.total * 100);
            document.getElementById('nt_progress_bar').style.width = pct + '%';
        }
    });
    xhr.addEventListener('load', function() {
        try {
            var res = JSON.parse(xhr.responseText);
            if (res.status !== 'success') {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> Lưu nghiệm thu';
                document.getElementById('nt_upload_progress').style.display = 'none';
                return Swal.fire('Upload thất bại', res.msg || 'Lỗi không xác định', 'error');
            }
            // Bước 2: Lưu path vào dat_lich
            $.ajax({
                url: '/controller/client/NghiemThu.php',
                method: 'POST',
                data: { id: id, anh: res.url, note: note },
                dataType: 'json',
                success: function(r) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> Lưu nghiệm thu';
                    document.getElementById('nt_upload_progress').style.display = 'none';
                    if(r.status == 'success') {
                        closeModal('modalNghiemthu');
                        Swal.fire('Đã lưu!', r.msg, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Lỗi', r.msg, 'error');
                    }
                },
                error: function() {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> Lưu nghiệm thu';
                    Swal.fire('Lỗi', 'Không thể kết nối máy chủ', 'error');
                }
            });
        } catch(e) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> Lưu nghiệm thu';
            Swal.fire('Lỗi', 'Phản hồi server không hợp lệ', 'error');
        }
    });
    xhr.addEventListener('error', function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> Lưu nghiệm thu';
        Swal.fire('Lỗi', 'Mất kết nối khi upload', 'error');
    });
    xhr.open('POST', '/controller/client/UploadNghiemThu.php');
    xhr.send(formData);
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
