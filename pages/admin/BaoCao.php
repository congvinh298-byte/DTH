<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$tieude = 'Báo cáo tổng hợp | Điện Máy Hiếu';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");

function fmt($n) { return number_format((int)$n, 0, ',', '.'); }

$totalProductOrders = $DMH->num_rows("SELECT * FROM `store_orders`");
$totalServiceOrders = $DMH->num_rows("SELECT * FROM `dat_lich`");
$totalCustomers = $DMH->num_rows("SELECT * FROM `users` WHERE `level` = 'user' OR `level` IS NULL OR `level` = ''");
$totalWorkers = $DMH->num_rows("SELECT * FROM `users` WHERE `level` = 'tho'");
$productRevenue = $DMH->get_row("SELECT SUM(`total_amount`) as s FROM `store_orders` WHERE `status` = 'completed'")['s'] ?? 0;
$serviceRevenue = $DMH->get_row("SELECT SUM(`phatsinh_gia`) as s FROM `dat_lich` WHERE `trangthai` = 'HOAN_THANH' AND `phatsinh_duyet` = 1")['s'] ?? 0;
$lowStock = $DMH->num_rows("SELECT * FROM `products` WHERE `stock` <= 5");

// Thống kê hiệu suất từng thợ
$tho_stats = $DMH->get_list(
    "SELECT u.id, u.name, u.fullname, u.username, u.phone, u.banned,
            COUNT(d.id) AS tong_don,
            SUM(CASE WHEN d.trangthai = 'HOAN_THANH' THEN 1 ELSE 0 END) AS hoan_thanh,
            SUM(CASE WHEN d.trangthai = 'DANG_XU_LY' THEN 1 ELSE 0 END) AS dang_xu_ly,
            SUM(CASE WHEN d.trangthai = 'HOAN_THANH' AND d.phatsinh_duyet = 1 THEN d.phatsinh_gia ELSE 0 END) AS phatsinh_duyet,
            ROUND(AVG(CASE WHEN d.danhgia_sao > 0 THEN d.danhgia_sao END), 1) AS diem_tb
     FROM `users` u
     LEFT JOIN `dat_lich` d ON d.tho_id = u.id
     WHERE u.level = 'tho'
     GROUP BY u.id
     ORDER BY hoan_thanh DESC, tong_don DESC"
);
if (!is_array($tho_stats)) $tho_stats = [];

$stats = [
    ['label' => 'Tổng đơn hàng sản phẩm', 'value' => fmt($totalProductOrders), 'icon' => 'fa-box', 'color' => '#2563eb'],
    ['label' => 'Tổng đơn gọi thợ', 'value' => fmt($totalServiceOrders), 'icon' => 'fa-calendar-check', 'color' => '#f59e0b'],
    ['label' => 'Tổng khách hàng', 'value' => fmt($totalCustomers), 'icon' => 'fa-users', 'color' => '#0ea5e9'],
    ['label' => 'Tổng thợ', 'value' => fmt($totalWorkers), 'icon' => 'fa-wrench', 'color' => '#6366f1'],
    ['label' => 'Doanh thu sản phẩm', 'value' => fmt($productRevenue), 'icon' => 'fa-sack-dollar', 'color' => '#16a34a'],
    ['label' => 'Doanh thu dịch vụ đã duyệt', 'value' => fmt($serviceRevenue), 'icon' => 'fa-file-invoice-dollar', 'color' => '#9333ea'],
    ['label' => 'Sản phẩm sắp hết hàng', 'value' => fmt($lowStock), 'icon' => 'fa-triangle-exclamation', 'color' => '#dc2626'],
];
?>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;"><i class="fa-solid fa-chart-line" style="color:#0ea5e9;"></i> Báo cáo tổng hợp</h2>

<div class="stats-grid">
    <?php foreach ($stats as $s): ?>
        <div class="stat-card">
            <div>
                <h4><?= htmlspecialchars($s['label']) ?></h4>
                <div class="value"><?= htmlspecialchars($s['value']) ?></div>
            </div>
            <div class="icon" style="background:<?= $s['color'] ?>20; color:<?= $s['color'] ?>;">
                <i class="fa-solid <?= $s['icon'] ?>"></i>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="card-header"><h3>Ghi chú</h3></div>
    <div class="card-body">
        <p>Báo cáo được tổng hợp từ các nguồn dữ liệu hiện có trên hệ thống. Doanh thu dịch vụ chỉ tính các đơn có phát sinh đã được duyệt.</p>
    </div>
</div>

<!-- Section: Hiệu suất từng thợ -->
<h2 style="margin:24px 0 16px; font-size:18px; font-weight:800; color:#0f172a;">
    <i class="fa-solid fa-users-gear" style="color:#9333ea;"></i> Hiệu suất từng thợ
</h2>

<div class="card">
    <div class="card-body" style="padding:0;">
        <?php if (empty($tho_stats)): ?>
            <div style="text-align:center; padding:40px 20px; color:#64748b;">
                <i class="fa-solid fa-wrench" style="font-size:36px; margin-bottom:12px; display:block; opacity:0.3;"></i>
                Chưa có thợ nào trong hệ thống.
            </div>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Thợ</th>
                            <th style="text-align:center;">Tổng đơn</th>
                            <th style="text-align:center;">Hoàn thành</th>
                            <th style="text-align:center;">Đang xử lý</th>
                            <th style="text-align:right;">Phát sinh (Duyệt)</th>
                            <th style="text-align:center;">Điểm đG TB</th>
                            <th style="text-align:center;">Tỉ lệ HT</th>
                            <th style="text-align:center;">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tho_stats as $ts):
                        $name    = $ts['name'] ?: $ts['fullname'] ?: $ts['username'];
                        $total   = (int)$ts['tong_don'];
                        $done    = (int)$ts['hoan_thanh'];
                        $inprog  = (int)$ts['dang_xu_ly'];
                        $phatsinh = (int)$ts['phatsinh_duyet'];
                        $diem    = $ts['diem_tb'] ? (float)$ts['diem_tb'] : null;
                        $ratio   = $total > 0 ? round($done / $total * 100) : 0;
                        $ratioColor = $ratio >= 80 ? '#16a34a' : ($ratio >= 50 ? '#f59e0b' : '#dc2626');
                    ?>
                        <tr>
                            <td>
                                <div style="font-weight:800; color:#0f172a;"><?= htmlspecialchars($name) ?></div>
                                <div style="font-size:12px; color:#64748b;"><?= htmlspecialchars($ts['username']) ?> <?= $ts['phone'] ? '· ' . htmlspecialchars($ts['phone']) : '' ?></div>
                            </td>
                            <td style="text-align:center; font-weight:800; color:#6366f1;"><?= $total ?: '&mdash;' ?></td>
                            <td style="text-align:center; font-weight:800; color:#16a34a;"><?= $done ?: '&mdash;' ?></td>
                            <td style="text-align:center; font-weight:800; color:#f59e0b;"><?= $inprog ?: '&mdash;' ?></td>
                            <td style="text-align:right; font-weight:800; color:#9333ea;">
                                <?= $phatsinh > 0 ? number_format($phatsinh, 0, ',', '.') . 'đ' : '&mdash;' ?>
                            </td>
                            <td style="text-align:center;">
                                <?php if ($diem !== null): ?>
                                    <span style="color:#f59e0b; font-weight:800;"><?= $diem ?> ★</span>
                                <?php else: ?>
                                    <span style="color:#cbd5e1;">Chưa có</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center;">
                                <div style="background:#f1f5f9; border-radius:20px; height:8px; width:80px; margin:0 auto 4px;">
                                    <div style="height:100%; border-radius:20px; background:<?= $ratioColor ?>; width:<?= $ratio ?>%;"></div>
                                </div>
                                <span style="font-size:12px; font-weight:700; color:<?= $ratioColor ?>;"><?= $ratio ?>%</span>
                            </td>
                            <td style="text-align:center;">
                                <?php if ($ts['banned'] === 'ON'): ?>
                                    <span class="badge badge-completed">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge badge-cancelled">Khóa</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
