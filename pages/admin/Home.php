<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();
$tieude = 'BẢNG ĐIỀU KHIỂN | ĐIỆN MÁY HIẾU';
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

// ===== DU LIEU GAN DAY =====
$recent_orders = $DMH->get_list("SELECT * FROM `store_orders` ORDER BY id DESC LIMIT 6");
$recent_bookings = $DMH->get_list("SELECT * FROM `dat_lich` ORDER BY id DESC LIMIT 6");

// ===== CAN XU LY =====
$can_xu_ly = $pending_orders + $shipping_orders + $pending_bookings + $confirmed_bookings;
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    .dmh-dashboard { 
        padding: 0; 
        background: #f1f5f9; 
        min-height: calc(100vh - 60px); 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .dmh-dashboard * { box-sizing: border-box; }
    
    /* Header chac chan */
    .dash-topbar {
        background: #fff;
        border-bottom: 2px solid #e2e8f0;
        padding: 20px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .dash-topbar h1 {
        font-size: 22px;
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
    .dash-date {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 16px;
        font-size: 13px;
        color: #475569;
        font-weight: 700;
    }
    
    /* Content container */
    .dash-content {
        padding: 28px;
        max-width: 1600px;
        margin: 0 auto;
    }
    
    /* Section title */
    .dash-section-title {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin: 28px 0 16px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .dash-section-title:before {
        content: '';
        display: inline-block;
        width: 4px;
        height: 18px;
        background: linear-gradient(180deg, #0ea5e9, #2563eb);
        border-radius: 2px;
    }
    
    /* Thong ke cards */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }
    .stat-box {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s;
    }
    .stat-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        border-color: #cbd5e1;
    }
    .stat-box .icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .stat-box .icon.blue { background: #eff6ff; color: #2563eb; }
    .stat-box .icon.green { background: #f0fdf4; color: #16a34a; }
    .stat-box .icon.orange { background: #fff7ed; color: #ea580c; }
    .stat-box .icon.red { background: #fef2f2; color: #dc2626; }
    .stat-box .icon.purple { background: #faf5ff; color: #9333ea; }
    .stat-box .icon.teal { background: #f0fdfa; color: #0d9488; }
    .stat-box .icon.dark { background: #f8fafc; color: #334155; }
    .stat-box .data h3 {
        font-size: 26px;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
        line-height: 1;
    }
    .stat-box .data p {
        font-size: 13px;
        color: #64748b;
        margin: 6px 0 0;
        font-weight: 700;
    }
    .stat-box .data small {
        display: block;
        font-size: 11px;
        color: #94a3b8;
        margin-top: 4px;
        font-weight: 600;
    }
    
    /* Module cards */
    .modules-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }
    .module-box {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 22px 20px;
        text-decoration: none;
        color: inherit;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: all 0.2s;
    }
    .module-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(14,165,233,0.15);
        border-color: #0ea5e9;
    }
    .module-box .icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .module-box .info { flex: 1; }
    .module-box .info h4 {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px;
    }
    .module-box .info p {
        font-size: 12px;
        color: #64748b;
        margin: 0;
        font-weight: 600;
    }
    .module-box .arrow {
        color: #cbd5e1;
        font-size: 14px;
        margin-top: 4px;
    }
    .module-box:hover .arrow { color: #0ea5e9; }
    
    /* Tables */
    .tables-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(460px, 1fr));
        gap: 24px;
    }
    .panel {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .panel-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
    }
    .panel-header h4 {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .panel-body { padding: 0; }
    .panel-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .panel-table th, .panel-table td {
        padding: 13px 16px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }
    .panel-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 800;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .panel-table tr:last-child td { border-bottom: none; }
    .panel-table td { color: #334155; font-weight: 500; }
    .panel-table tr:hover td { background: #f8fafc; }
    .panel-table td strong { color: #0f172a; font-weight: 700; }
    .panel-footer {
        padding: 12px 16px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .panel-footer a {
        color: #0ea5e9;
        font-weight: 800;
        text-decoration: none;
        font-size: 13px;
    }
    .panel-footer a:hover { text-decoration: underline; }
    
    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
    }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-shipping { background: #dbeafe; color: #1e40af; }
    .badge-completed { background: #d1fae5; color: #065f46; }
    .badge-cancelled { background: #fee2e2; color: #991b1b; }
    .badge-waiting { background: #ffedd5; color: #9a3412; }
    .badge-doing { background: #dbeafe; color: #1e40af; }
    .badge-done { background: #d1fae5; color: #065f46; }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #64748b;
        font-weight: 600;
    }
    .empty-state i { font-size: 40px; color: #cbd5e1; margin-bottom: 12px; display: block; }
    
    @media (max-width: 768px) {
        .dash-content { padding: 16px; }
        .stats-row, .modules-row, .tables-row { grid-template-columns: 1fr; }
        .dash-topbar { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="dmh-dashboard">
    
    <div class="dash-topbar">
        <div>
            <h1><i class="fa-solid fa-gauge-high"></i> BẢNG ĐIỀU KHIỂN HỆ THỐNG</h1>
            <p>Công ty TNHH MTV Điện Tử Hiếu — Quản lý toàn diện cửa hàng, đơn hàng, dịch vụ và tài chính</p>
        </div>
        <div class="dash-date">
            <i class="fa-regular fa-calendar"></i> <?= date('H:i d/m/Y') ?>
        </div>
    </div>
    
    <div class="dash-content">
        
        <!-- THONG KE CHINH -->
        <div class="dash-section-title">Thống kê tổng quan</div>
        
        <div class="stats-row">
            <div class="stat-box">
                <div class="icon blue"><i class="fa-solid fa-users"></i></div>
                <div class="data">
                    <h3><?= number_format($total_users) ?></h3>
                    <p>Tổng khách hàng</p>
                    <small>+<?= number_format($new_users_today) ?> hôm nay · <?= number_format($total_admins) ?> admin</small>
                </div>
            </div>
            
            <div class="stat-box">
                <div class="icon orange"><i class="fa-solid fa-box"></i></div>
                <div class="data">
                    <h3><?= number_format($total_orders) ?></h3>
                    <p>Đơn hàng sản phẩm</p>
                    <small><?= number_format($pending_orders) ?> chờ · <?= number_format($shipping_orders) ?> đang giao · <?= number_format($completed_orders) ?> hoàn thành</small>
                </div>
            </div>
            
            <div class="stat-box">
                <div class="icon purple"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                <div class="data">
                    <h3><?= number_format($total_bookings) ?></h3>
                    <p>Đơn đặt lịch thợ</p>
                    <small><?= number_format($pending_bookings) ?> chờ xác nhận · <?= number_format($confirmed_bookings) ?> đang làm</small>
                </div>
            </div>
            
            <div class="stat-box">
                <div class="icon green"><i class="fa-solid fa-sack-dollar"></i></div>
                <div class="data">
                    <h3><?= number_format($total_revenue) ?>đ</h3>
                    <p>Doanh thu đơn hàng SP</p>
                    <small>Hôm nay: +<?= number_format($today_revenue) ?>đ · Nạp tiền: +<?= number_format($doanhthuhn) ?>đ</small>
                </div>
            </div>
            
            <div class="stat-box">
                <div class="icon red"><i class="fa-solid fa-bell"></i></div>
                <div class="data">
                    <h3><?= number_format($can_xu_ly) ?></h3>
                    <p>Việc cần xử lý</p>
                    <small>Đơn hàng + đặt lịch đang chờ</small>
                </div>
            </div>
            
            <div class="stat-box">
                <div class="icon dark"><i class="fa-solid fa-eye"></i></div>
                <div class="data">
                    <h3><?= number_format($viewhn) ?></h3>
                    <p>Lượt truy cập hôm nay</p>
                    <small>Tổng lượt ghé thăm website</small>
                </div>
            </div>
        </div>
        
        <!-- MODULE NHANH -->
        <div class="dash-section-title">Truy cập nhanh</div>
        
        <div class="modules-row">
            <a href="/pages/admin/QuanLyDonHang.php" class="module-box">
                <div class="icon"><i class="fa-solid fa-box"></i></div>
                <div class="info">
                    <h4>Quản lý đơn hàng SP</h4>
                    <p><?= number_format($pending_orders) ?> đơn đang chuẩn bị</p>
                </div>
                <div class="arrow"><i class="fa-solid fa-chevron-right"></i></div>
            </a>
            
            <a href="/pages/admin/QuanLyDatLich.php" class="module-box">
                <div class="icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                <div class="info">
                    <h4>Quản lý đặt lịch thợ</h4>
                    <p><?= number_format($pending_bookings) ?> đơn chờ xử lý</p>
                </div>
                <div class="arrow"><i class="fa-solid fa-chevron-right"></i></div>
            </a>
            
            <a href="/Admin/Quanlythanhvien" class="module-box">
                <div class="icon"><i class="fa-solid fa-users"></i></div>
                <div class="info">
                    <h4>Quản lý thành viên</h4>
                    <p>+<?= number_format($new_users_today) ?> thành viên hôm nay</p>
                </div>
                <div class="arrow"><i class="fa-solid fa-chevron-right"></i></div>
            </a>
            
            <a href="/Admin/Hoadontsr" class="module-box">
                <div class="icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                <div class="info">
                    <h4>Hóa đơn & Tài chính</h4>
                    <p>Doanh thu hôm nay: <?= number_format($doanhthuhn) ?>đ</p>
                </div>
                <div class="arrow"><i class="fa-solid fa-chevron-right"></i></div>
            </a>
            
            <a href="/Admin/Danhmuctaoweb" class="module-box">
                <div class="icon"><i class="fa-solid fa-globe"></i></div>
                <div class="info">
                    <h4>Danh mục tạo web</h4>
                    <p>Quản lý mẫu website</p>
                </div>
                <div class="arrow"><i class="fa-solid fa-chevron-right"></i></div>
            </a>
            
            <a href="/Admin/Danhmucbancode" class="module-box">
                <div class="icon"><i class="fa-solid fa-code"></i></div>
                <div class="info">
                    <h4>Danh mục bán code</h4>
                    <p>Quản lý source code</p>
                </div>
                <div class="arrow"><i class="fa-solid fa-chevron-right"></i></div>
            </a>
            
            <a href="/Admin/Quanlyapi" class="module-box">
                <div class="icon"><i class="fa-solid fa-key"></i></div>
                <div class="info">
                    <h4>Quản lý API</h4>
                    <p>Khóa API và tích hợp</p>
                </div>
                <div class="arrow"><i class="fa-solid fa-chevron-right"></i></div>
            </a>
            
            <a href="/Admin/SettingAdmin" class="module-box">
                <div class="icon"><i class="fa-solid fa-gear"></i></div>
                <div class="info">
                    <h4>Cài đặt hệ thống</h4>
                    <p>Cấu hình website</p>
                </div>
                <div class="arrow"><i class="fa-solid fa-chevron-right"></i></div>
            </a>
        </div>
        
        <!-- BANG GAN DAY -->
        <div class="dash-section-title">Hoạt động gần đây</div>
        
        <div class="tables-row">
            
            <div class="panel">
                <div class="panel-header">
                    <h4><i class="fa-solid fa-box-open"></i> Đơn hàng sản phẩm mới nhất</h4>
                </div>
                <div class="panel-body">
                    <?php if (empty($recent_orders)): ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-box-open"></i>
                            Chưa có đơn hàng nào
                        </div>
                    <?php else: ?>
                        <table class="panel-table">
                            <thead>
                                <tr>
                                    <th>Mã ĐH</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td><strong>#<?= (int)$order['id'] ?></strong></td>
                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td><strong style="color:#dc2626;"><?= number_format($order['total_amount']) ?>đ</strong></td>
                                        <td>
                                            <?php if($order['status'] == 'pending'): ?>
                                                <span class="status-badge badge-pending">Đang chuẩn bị</span>
                                            <?php elseif($order['status'] == 'shipping'): ?>
                                                <span class="status-badge badge-shipping">Đang giao</span>
                                            <?php elseif($order['status'] == 'completed'): ?>
                                                <span class="status-badge badge-completed">Hoàn thành</span>
                                            <?php else: ?>
                                                <span class="status-badge badge-cancelled">Đã hủy</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <div class="panel-footer">
                    <a href="/pages/admin/QuanLyDonHang.php">Xem tất cả đơn hàng sản phẩm →</a>
                </div>
            </div>
            
            
            <div class="panel">
                <div class="panel-header">
                    <h4><i class="fa-solid fa-calendar-check"></i> Đơn đặt lịch thợ mới nhất</h4>
                </div>
                <div class="panel-body">
                    <?php if (empty($recent_bookings)): ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-calendar-xmark"></i>
                            Chưa có đơn đặt lịch nào
                        </div>
                    <?php else: ?>
                        <table class="panel-table">
                            <thead>
                                <tr>
                                    <th>Mã</th>
                                    <th>Khách hàng</th>
                                    <th>Dịch vụ</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_bookings as $booking): ?>
                                    <tr>
                                        <td><strong>#<?= (int)$booking['id'] ?></strong></td>
                                        <td><?= htmlspecialchars($booking['name'] ?? $booking['customer_name'] ?? 'Khách') ?></td>
                                        <td><?= htmlspecialchars($booking['service'] ?? $booking['service_name'] ?? 'Sửa chữa') ?></td>
                                        <td>
                                            <?php if($booking['status'] == 'pending' || $booking['status'] == 'cho_xac_nhan'): ?>
                                                <span class="status-badge badge-waiting">Chờ xác nhận</span>
                                            <?php elseif($booking['status'] == 'confirmed' || $booking['status'] == 'dang_lam'): ?>
                                                <span class="status-badge badge-doing">Đang làm</span>
                                            <?php elseif($booking['status'] == 'completed' || $booking['status'] == 'hoan_thanh'): ?>
                                                <span class="status-badge badge-done">Hoàn thành</span>
                                            <?php else: ?>
                                                <span class="status-badge badge-cancelled"><?= htmlspecialchars($booking['status']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <div class="panel-footer">
                    <a href="/pages/admin/QuanLyDatLich.php">Xem tất cả đơn đặt lịch →</a>
                </div>
            </div>
        </div>
    
    </div>

</div>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
