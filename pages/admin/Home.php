<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();
$tieude = 'B?NG DI?U KHI?N | DI?N MAY HI?U';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");

// ===== THONG KE =====
$total_users = $DMH->num_rows("SELECT * FROM `users` WHERE `level` = 'user'") ?? 0;
$new_users_today = $DMH->num_rows("SELECT * FROM `users` WHERE `timereg` >= DATE(NOW()) AND `timereg` < DATE(NOW()) + INTERVAL 1 DAY") ?? 0;
$total_admins = $DMH->num_rows("SELECT * FROM `users` WHERE `level` = 'admin'") ?? 0;

$total_orders = $DMH->num_rows("SELECT * FROM `store_orders`") ?? 0;
$pending_orders = $DMH->num_rows("SELECT * FROM `store_orders` WHERE `status` = 'pending'") ?? 0;
$shipping_orders = $DMH->num_rows("SELECT * FROM `store_orders` WHERE `status` = 'shipping'") ?? 0;
$completed_orders = $DMH->num_rows("SELECT * FROM `store_orders` WHERE `status` = 'completed'") ?? 0;

$total_bookings = $DMH->num_rows("SELECT * FROM `dat_lich`") ?? 0;
$pending_bookings = $DMH->num_rows("SELECT * FROM `dat_lich` WHERE `status` = 'pending'") ?? 0;
$confirmed_bookings = $DMH->num_rows("SELECT * FROM `dat_lich` WHERE `status` = 'confirmed'") ?? 0;

$total_revenue = $DMH->get_row("SELECT SUM(`total_amount`) as sum FROM `store_orders` WHERE `status` = 'completed'")['sum'] ?? 0;
$today_revenue = $DMH->get_row("SELECT SUM(`total_amount`) as sum FROM `store_orders` WHERE `status` = 'completed' AND DATE(`created_at`) = CURDATE()")['sum'] ?? 0;

$tiencard = $DMH->get_row("SELECT SUM(`thucnhan`) as sum FROM `napcard` WHERE `status` = 'thanhcong' AND `thoigian` >= DATE(NOW()) AND `thoigian` < DATE(NOW()) + INTERVAL 1 DAY")['sum'] ?? 0;
$tienatm = $DMH->get_row("SELECT SUM(`sotien`) as sum FROM `napatm` WHERE `thoigian` >= DATE(NOW()) AND `thoigian` < DATE(NOW()) + INTERVAL 1 DAY")['sum'] ?? 0;
$doanhthuhn = $tiencard + $tienatm;

$viewhn = $DMH->num_rows("SELECT * FROM `logclient` WHERE `ip` != '' AND `time` >= DATE(NOW()) AND `time` < DATE(NOW()) + INTERVAL 1 DAY") ?? 0;

$recent_orders = $DMH->get_list("SELECT * FROM `store_orders` ORDER BY id DESC LIMIT 6");
$recent_bookings = $DMH->get_list("SELECT * FROM `dat_lich` ORDER BY id DESC LIMIT 6");

$can_xu_ly = $pending_orders + $shipping_orders + $pending_bookings + $confirmed_bookings;

function formatMoney($n) { return number_format($n, 0, ',', '.'); }
function statusBadge($status) {
    switch($status) {
        case 'pending': return '<span class="badge badge-pending">Ch? x? l?</span>';
        case 'shipping': return '<span class="badge badge-shipping">?ang giao</span>';
        case 'completed': return '<span class="badge badge-completed">Hon thnh</span>';
        case 'cancelled': return '<span class="badge badge-cancelled">? h?y</span>';
        case 'confirmed': return '<span class="badge badge-completed">Xc nh?n</span>';
        default: return '<span class="badge badge-pending">'.$status.'</span>';
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    .dmh-dashboard { 
        padding: 0; 
        background: #f8fafc; 
        min-height: calc(100vh - 60px); 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .dmh-dashboard * { box-sizing: border-box; }
    
    .dash-topbar {
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 22px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .dash-topbar h1 {
        font-size: 24px;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
        letter-spacing: 0.3px;
    }
    .dash-topbar p {
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        margin: 4px 0 0;
    }
    .dash-user {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f1f5f9;
        padding: 10px 16px;
        border-radius: 12px;
        font-weight: 700;
        color: #334155;
    }
    .dash-user i { color: #0ea5e9; font-size: 18px; }
    
    .dash-container {
        padding: 24px 28px;
    }
    
    .section-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title i { color: #0ea5e9; }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 18px;
        margin-bottom: 26px;
    }
    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(15,23,42,0.06);
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(15,23,42,0.10);
    }
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 14px;
    }
    .stat-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .stat-value {
        font-size: 28px;
        font-weight: 900;
        color: #0f172a;
        margin: 0 0 6px;
    }
    .stat-note {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }
    .stat-note strong { color: #0f172a; }
    
    .icon-blue { background: #eff6ff; color: #2563eb; }
    .icon-cyan { background: #ecfeff; color: #0891b2; }
    .icon-green { background: #f0fdf4; color: #16a34a; }
    .icon-yellow { background: #fefce8; color: #ca8a04; }
    .icon-red { background: #fef2f2; color: #dc2626; }
    .icon-purple { background: #faf5ff; color: #9333ea; }
    
    .dash-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 22px;
        margin-bottom: 26px;
    }
    @media (max-width: 1100px) {
        .dash-row { grid-template-columns: 1fr; }
    }
    
    .panel {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(15,23,42,0.05);
        overflow: hidden;
    }
    .panel-header {
        padding: 18px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .panel-header h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 900;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .panel-header h3 i { color: #0ea5e9; }
    .panel-header a {
        color: #0ea5e9;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }
    .panel-header a:hover { text-decoration: underline; }
    .panel-body { padding: 0; }
    
    .dash-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .dash-table th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }
    .dash-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-weight: 600;
    }
    .dash-table tr:last-child td { border-bottom: none; }
    .dash-table tr:hover td { background: #f8fafc; }
    .text-right { text-align: right; }
    .text-muted { color: #94a3b8; }
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
    }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-shipping { background: #dbeafe; color: #1e40af; }
    .badge-completed { background: #dcfce7; color: #166534; }
    .badge-cancelled { background: #fee2e2; color: #991b1b; }
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 14px;
    }
    .quick-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        color: #334155;
        text-decoration: none;
        font-weight: 700;
        font-size: 13px;
        box-shadow: 0 2px 8px rgba(15,23,42,0.04);
        transition: all 0.15s;
    }
    .quick-btn:hover {
        border-color: #0ea5e9;
        color: #0ea5e9;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(14,165,233,0.15);
    }
    .quick-btn i { font-size: 18px; width: 22px; text-align: center; }
    
    .alert-bar {
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
        border: 1px solid #fdba74;
        border-radius: 12px;
        padding: 14px 18px;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #9a3412;
        font-weight: 700;
        font-size: 13px;
    }
    .alert-bar i { font-size: 18px; }
    
    .empty-state {
        text-align: center;
        padding: 30px;
        color: #94a3b8;
        font-weight: 600;
    }
</style>

<div class="dmh-dashboard">
    
    <div class="dash-topbar">
        <div>
            <h1>B?NG DI?U KHI?N</h1>
            <p>T?ng quan h? th?ng ?i?n My Hi?u</p>
        </div>
        <div class="dash-user">
            <i class="fa-solid fa-user-shield"></i>
            <span><?=htmlspecialchars($getUser['username'] ?? 'Admin')?></span>
        </div>
    </div>
    
    <div class="dash-container">
        
        <?php if ($can_xu_ly > 0): ?>
        <div class="alert-bar">
            <i class="fa-solid fa-bell"></i>
            Cn <strong><?=$can_xu_ly?> vi?c</strong> c?n x? l? ngay hm nay (?<?=$pending_orders?> ??n hng ch? x? l?, <?=$shipping_orders?> ??n ?ang giao, <?=$pending_bookings?> l?ch h?n ch?, <?=$confirmed_bookings?> l?ch ? xc nh?n).
        </div>
        <?php endif; ?>
        
        <h2 class="section-title"><i class="fa-solid fa-chart-pie"></i> Th?ng k t?ng quan</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">T?ng khch hng</div>
                    <div class="stat-icon icon-blue"><i class="fa-solid fa-users"></i></div>
                </div>
                <div class="stat-value"><?=formatMoney($total_users)?></div>
                <div class="stat-note"><strong>+<?=$new_users_today?></strong> khch m?i hm nay</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">?on hng s?n ph?m</div>
                    <div class="stat-icon icon-cyan"><i class="fa-solid fa-box-open"></i></div>
                </div>
                <div class="stat-value"><?=formatMoney($total_orders)?></div>
                <div class="stat-note"><strong><?=$pending_orders?></strong> ch? x? l?, <strong><?=$shipping_orders?></strong> ?ang giao</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">L?ch h?n g?i th?</div>
                    <div class="stat-icon icon-yellow"><i class="fa-solid fa-calendar-check"></i></div>
                </div>
                <div class="stat-value"><?=formatMoney($total_bookings)?></div>
                <div class="stat-note"><strong><?=$pending_bookings?></strong> ch?, <strong><?=$confirmed_bookings?></strong> ? xc nh?n</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Doanh thu tch l?y</div>
                    <div class="stat-icon icon-green"><i class="fa-solid fa-sack-dollar"></i></div>
                </div>
                <div class="stat-value"><?=formatMoney($total_revenue)?></sup></div>
                <div class="stat-note">Hm nay: <strong><?=formatMoney($today_revenue)?>?</strong></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">N?p ti?n hm nay</div>
                    <div class="stat-icon icon-purple"><i class="fa-solid fa-wallet"></i></div>
                </div>
                <div class="stat-value"><?=formatMoney($doanhthuhn)?></sup></div>
                <div class="stat-note">Th? + ATM h?p nh?t</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">L??t truy c?p hm nay</div>
                    <div class="stat-icon icon-red"><i class="fa-solid fa-eye"></i></div>
                </div>
                <div class="stat-value"><?=formatMoney($viewhn)?></div>
                <div class="stat-note">Khch vng lai + thnh vin</div>
            </div>
        </div>
        
        <div class="dash-row">
            
            <div class="panel">
                <div class="panel-header">
                    <h3><i class="fa-solid fa-cart-shopping"></i> ??n hng g?n ??y</h3>
                    <a href="/pages/admin/QuanLyDonHang.php">Xem t?t c?</a>
                </div>
                <div class="panel-body">
                    <?php if (empty($recent_orders)): ?>
                        <div class="empty-state">Ch?a c ??n hng no</div>
                    <?php else: ?>
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>M ??n</th>
                                <th>Khch hng</th>
                                <th class="text-right">T?ng ti?n</th>
                                <th>Tr?ng thi</th>
                                <th>Th?i gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#ORD<?=$order['id']?></td>
                                <td><?=htmlspecialchars($order['customer_name'] ?? $order['username'] ?? 'Khch vng lai')?></td>
                                <td class="text-right"><?=formatMoney($order['total_amount'] ?? 0)?>?</td>
                                <td><?=statusBadge($order['status'])?></td>
                                <td class="text-muted"><?=htmlspecialchars($order['created_at'] ?? '-')?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="panel">
                <div class="panel-header">
                    <h3><i class="fa-solid fa-wrench"></i> L?ch h?n g?n ??y</h3>
                    <a href="/pages/admin/QuanLyDatLich.php">Xem t?t c?</a>
                </div>
                <div class="panel-body">
                    <?php if (empty($recent_bookings)): ?>
                        <div class="empty-state">Ch?a c l?ch h?n no</div>
                    <?php else: ?>
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>M l?ch</th>
                                <th>Khch hng</th>
                                <th>D?ch v?</th>
                                <th>Tr?ng thi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_bookings as $booking): ?>
                            <tr>
                                <td>#LIC<?=$booking['id']?></td>
                                <td><?=htmlspecialchars($booking['name'] ?? $booking['username'] ?? '-')?></td>
                                <td><?=htmlspecialchars($booking['service'] ?? '-')?></td>
                                <td><?=statusBadge($booking['status'])?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
        
        
        <h2 class="section-title"><i class="fa-solid fa-bolt"></i> Thao tc nhanh</h2>
        <div class="quick-actions">
            <a href="/pages/admin/QuanLyDonHang.php" class="quick-btn">
                <i class="fa-solid fa-box"></i> Qu?n l ??n hng
            </a>
            <a href="/pages/admin/QuanLyDatLich.php" class="quick-btn">
                <i class="fa-solid fa-calendar-check"></i> L?ch g?i th?
            </a>
            <a href="/Admin/Quanlythanhvien" class="quick-btn">
                <i class="fa-solid fa-users"></i> Thnh vin
            </a>
            <a href="/Admin/Magiamgia" class="quick-btn">
                <i class="fa-solid fa-ticket"></i> M? gi?m gi
            </a>
            <a href="/Admin/SettingAdmin" class="quick-btn">
                <i class="fa-solid fa-gear"></i> Ci d?t h? th?ng
            </a>
            <a href="/pages/admin/Logout.php" class="quick-btn" style="color:#dc2626;">
                <i class="fa-solid fa-power-off"></i> ?ang xu?t
            </a>
        </div>
        
    </div>
    
</div>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
