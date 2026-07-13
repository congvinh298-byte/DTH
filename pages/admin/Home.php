<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
$tieude = 'TRANG QUẢN TRỊ HỆ THỐNG | ĐIỆN MÁY HIẾU';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");
CheckAdmin();

// ===== THONG KE TONG QUAN =====
$total_users = $DMH->num_rows("SELECT * FROM `users` WHERE `level` = 'user'") ?? 0;
$new_users_today = $DMH->num_rows("SELECT * FROM `users` WHERE `timereg` >= DATE(NOW()) AND `timereg` < DATE(NOW()) + INTERVAL 1 DAY") ?? 0;

$total_orders = $DMH->num_rows("SELECT * FROM `store_orders`") ?? 0;
$pending_orders = $DMH->num_rows("SELECT * FROM `store_orders` WHERE `status` = 'pending'") ?? 0;
$shipping_orders = $DMH->num_rows("SELECT * FROM `store_orders` WHERE `status` = 'shipping'") ?? 0;
$completed_orders = $DMH->num_rows("SELECT * FROM `store_orders` WHERE `status` = 'completed'") ?? 0;

$total_bookings = $DMH->num_rows("SELECT * FROM `dat_lich`") ?? 0;
$pending_bookings = $DMH->num_rows("SELECT * FROM `dat_lich` WHERE `status` = 'pending'") ?? 0;

$total_revenue = $DMH->get_row("SELECT SUM(`total_amount`) as sum FROM `store_orders` WHERE `status` = 'completed'")['sum'] ?? 0;
$today_revenue = $DMH->get_row("SELECT SUM(`total_amount`) as sum FROM `store_orders` WHERE `status` = 'completed' AND DATE(`created_at`) = CURDATE()")['sum'] ?? 0;

$tiencard = $DMH->get_row("SELECT SUM(`thucnhan`) as sum FROM `napcard` WHERE `status` = 'thanhcong' AND `thoigian` >= DATE(NOW()) AND `thoigian` < DATE(NOW()) + INTERVAL 1 DAY")['sum'] ?? 0;
$tienatm = $DMH->get_row("SELECT SUM(`sotien`) as sum FROM `napatm` WHERE `thoigian` >= DATE(NOW()) AND `thoigian` < DATE(NOW()) + INTERVAL 1 DAY")['sum'] ?? 0;
$doanhthuhn = $tiencard + $tienatm;

$viewhn = $DMH->num_rows("SELECT * FROM `logclient` WHERE `ip` != '' AND `time` >= DATE(NOW()) AND `time` < DATE(NOW()) + INTERVAL 1 DAY") ?? 0;

$recent_orders = $DMH->get_list("SELECT * FROM `store_orders` ORDER BY id DESC LIMIT 5");
$recent_bookings = $DMH->get_list("SELECT * FROM `dat_lich` ORDER BY id DESC LIMIT 5");
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    .admin-dashboard { padding: 24px; background: #f8fafc; min-height: calc(100vh - 60px); }
    .dash-header { margin-bottom: 28px; }
    .dash-header h2 { font-size: 26px; font-weight: 900; color: #0f172a; margin: 0; }
    .dash-header p { color: #64748b; margin-top: 6px; font-weight: 600; }
    
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 28px; }
    .stat-card { background: #fff; border-radius: 16px; padding: 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 18px; transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    .stat-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
    .stat-icon.blue { background: #dbeafe; color: #2563eb; }
    .stat-icon.green { background: #d1fae5; color: #059669; }
    .stat-icon.orange { background: #ffedd5; color: #ea580c; }
    .stat-icon.red { background: #fee2e2; color: #dc2626; }
    .stat-icon.purple { background: #e9d5ff; color: #7c3aed; }
    .stat-icon.teal { background: #ccfbf1; color: #0d9488; }
    .stat-info h3 { font-size: 28px; font-weight: 900; color: #0f172a; margin: 0; }
    .stat-info p { color: #64748b; font-size: 14px; font-weight: 600; margin: 4px 0 0; }
    
    .modules-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 28px; }
    .module-card { background: #fff; border-radius: 14px; padding: 20px; border: 1px solid #e2e8f0; text-decoration: none; color: inherit; display: flex; align-items: center; gap: 14px; transition: all 0.2s; }
    .module-card:hover { background: #f8fafc; border-color: #0ea5e9; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(14,165,233,0.15); }
    .module-card:hover .module-title { color: #0ea5e9; }
    .module-icon { width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
    .module-title { font-size: 15px; font-weight: 800; color: #0f172a; }
    .module-desc { font-size: 12px; color: #64748b; margin-top: 2px; }
    
    .tables-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 24px; }
    .table-card { background: #fff; border-radius: 16px; padding: 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
    .table-card h4 { font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 16px; display: flex; align-items: center; gap: 10px; }
    .dash-table { width: 100%; border-collapse: collapse; }
    .dash-table th, .dash-table td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
    .dash-table th { color: #475569; font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; background: #f8fafc; }
    .dash-table tr:last-child td { border-bottom: none; }
    .dash-table td { color: #334155; }
    .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 800; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-shipping { background: #dbeafe; color: #1e40af; }
    .badge-completed { background: #d1fae5; color: #065f46; }
    .badge-cancelled { background: #fee2e2; color: #991b1b; }
    .view-all { display: inline-block; margin-top: 14px; color: #0ea5e9; font-weight: 700; text-decoration: none; font-size: 13px; }
    .view-all:hover { text-decoration: underline; }
    
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
        .modules-grid { grid-template-columns: 1fr; }
        .tables-row { grid-template-columns: 1fr; }
    }
</style>

<div class="admin-dashboard">
    
    <div class="dash-header">
        <h2><i class="fa-solid fa-gauge-high"></i> Tổng quan hệ thống</h2>
        <p>Chào mừng quay lại Bảng điều khiển Điện Máy Hiếu. Dưới đây là tình hình hoạt động.</p>
    </div>
    
    <!-- Thong ke -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fa-solid fa-users"></i></div>
            <div class="stat-info">
                <h3><?= number_format($total_users) ?></h3>
                <p>Tổng thành viên</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fa-solid fa-box"></i></div>
            <div class="stat-info">
                <h3><?= number_format($total_orders) ?></h3>
                <p>Tổng đơn hàng SP</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fa-solid fa-screwdriver-wrench"></i></div>
            <div class="stat-info">
                <h3><?= number_format($total_bookings) ?></h3>
                <p>Đơn đặt lịch thợ</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green"><i class="fa-solid fa-money-bill-trend-up"></i></div>
            <div class="stat-info">
                <h3><?= number_format($total_revenue) ?>đ</h3>
                <p>Doanh thu đơn hàng SP</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon red"><i class="fa-solid fa-clock"></i></div>
            <div class="stat-info">
                <h3><?= number_format($pending_orders + $pending_bookings) ?></h3>
                <p>Đơn chờ xử lý</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon teal"><i class="fa-solid fa-eye"></i></div>
            <div class="stat-info">
                <h3><?= number_format($viewhn) ?></h3>
                <p>Lượt truy cập hôm nay</p>
            </div>
        </div>
    </div>
    
    <!-- Cac module chinh -->
    <div class="modules-grid">
        <a href="/pages/admin/QuanLyDonHang.php" class="module-card">
            <div class="module-icon"><i class="fa-solid fa-box"></i></div>
            <div>
                <div class="module-title">Quản lý đơn hàng SP</div>
                <div class="module-desc"><?= number_format($pending_orders) ?> đơn đang chuẩn bị</div>
            </div>
        </a>
        
        <a href="/pages/admin/QuanLyDatLich.php" class="module-card">
            <div class="module-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
            <div>
                <div class="module-title">Quản lý đặt lịch thợ</div>
                <div class="module-desc"><?= number_format($pending_bookings) ?> đơn chờ xử lý</div>
            </div>
        </a>
        
        <a href="/Admin/Quanlythanhvien" class="module-card">
            <div class="module-icon"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="module-title">Quản lý thành viên</div>
                <div class="module-desc">+<?= number_format($new_users_today) ?> thành viên hôm nay</div>
            </div>
        </a>
        
        <a href="/Admin/Hoadontsr" class="module-card">
            <div class="module-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <div>
                <div class="module-title">Hóa đơn & Tài chính</div>
                <div class="module-desc">Doanh thu hôm nay: <?= number_format($doanhthuhn) ?>đ</div>
            </div>
        </a>
        
        <a href="/Admin/Danhmuctaoweb" class="module-card">
            <div class="module-icon"><i class="fa-solid fa-globe"></i></div>
            <div>
                <div class="module-title">Danh mục tạo web</div>
                <div class="module-desc">Quản lý mẫu website</div>
            </div>
        </a>
        
        <a href="/Admin/Danhmucbancode" class="module-card">
            <div class="module-icon"><i class="fa-solid fa-code"></i></div>
            <div>
                <div class="module-title">Danh mục bán code</div>
                <div class="module-desc">Quản lý source code</div>
            </div>
        </a>
    </div>
    
    <!-- Bang gan day -->
    <div class="tables-row">
        <div class="table-card">
            <h4><i class="fa-solid fa-box-open"></i> Đơn hàng sản phẩm mới nhất</h4>
            
            <?php if (empty($recent_orders)): ?>
                <p style="color: #64748b; text-align:center; padding: 20px;">Chưa có đơn hàng nào.</p>
            <?php else: ?>
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#<?= (int)$order['id'] ?></td>
                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td><strong style="color:#dc2626;"><?= number_format($order['total_amount']) ?>đ</strong></td>
                                <td>
                                    <?php if($order['status'] == 'pending'): ?>
                                        <span class="badge badge-pending">Đang chuẩn bị</span>
                                    <?php elseif($order['status'] == 'shipping'): ?>
                                        <span class="badge badge-shipping">Đang giao</span>
                                    <?php elseif($order['status'] == 'completed'): ?>
                                        <span class="badge badge-completed">Hoàn thành</span>
                                    <?php else: ?>
                                        <span class="badge badge-cancelled">Đã hủy</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="/pages/admin/QuanLyDonHang.php" class="view-all">Xem tất cả đơn hàng →</a>
            <?php endif; ?>
        </div>
        
        <div class="table-card">
            <h4><i class="fa-solid fa-calendar-check"></i> Đơn đặt lịch thợ mới nhất</h4>
            
            <?php if (empty($recent_bookings)): ?>
                <p style="color: #64748b; text-align:center; padding: 20px;">Chưa có đơn đặt lịch nào.</p>
            <?php else: ?>
                <table class="dash-table">
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
                                <td>#<?= (int)$booking['id'] ?></td>
                                <td><?= htmlspecialchars($booking['name'] ?? $booking['customer_name'] ?? 'Khách') ?></td>
                                <td><?= htmlspecialchars($booking['service'] ?? $booking['service_name'] ?? 'Sửa chữa') ?></td>
                                <td>
                                    <?php if($booking['status'] == 'pending' || $booking['status'] == 'cho_xac_nhan'): ?>
                                        <span class="badge badge-pending">Chờ xác nhận</span>
                                    <?php elseif($booking['status'] == 'confirmed' || $booking['status'] == 'dang_lam'): ?>
                                        <span class="badge badge-shipping">Đang làm</span>
                                    <?php elseif($booking['status'] == 'completed' || $booking['status'] == 'hoan_thanh'): ?>
                                        <span class="badge badge-completed">Hoàn thành</span>
                                    <?php else: ?>
                                        <span class="badge badge-cancelled"><?= htmlspecialchars($booking['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="/pages/admin/QuanLyDatLich.php" class="view-all">Xem tất cả đơn đặt lịch →</a>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
