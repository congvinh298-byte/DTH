<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();
$tieude = 'Dashboard | Dien May Hieu';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");

function fmt($n) { return number_format((int)$n, 0, ',', '.'); }
function safeNum($sql) { global $DMH; $r = $DMH->num_rows($sql); return $r ?: 0; }
function safeSum($sql) { global $DMH; $r = $DMH->get_row($sql); return (int)($r['s'] ?? 0); }

$pendingProductOrders = safeNum("SELECT * FROM `store_orders` WHERE `status` = 'pending'");
$pendingServiceOrders = safeNum("SELECT * FROM `dat_lich` WHERE `trangthai` = 'CHO_XU_LY'");
$totalRevenue = safeSum("SELECT SUM(`total_amount`) as s FROM `store_orders` WHERE `status` = 'completed'");
$totalCustomers = safeNum("SELECT * FROM `users` WHERE `level` = 'user' OR `level` IS NULL OR `level` = ''");
$totalWorkers = safeNum("SELECT * FROM `users` WHERE `level` = 'tho'");
$lowStock = safeNum("SELECT * FROM `products` WHERE `stock` <= 5");

$stats = [
    ['label' => 'Don hang san pham cho xu ly', 'value' => $pendingProductOrders, 'icon' => 'fa-box', 'color' => '#2563eb'],
    ['label' => 'Don goi tho cho xu ly', 'value' => $pendingServiceOrders, 'icon' => 'fa-calendar-check', 'color' => '#f59e0b'],
    ['label' => 'Tong doanh thu hoan thanh', 'value' => fmt($totalRevenue), 'icon' => 'fa-sack-dollar', 'color' => '#16a34a'],
    ['label' => 'Tong khach hang', 'value' => $totalCustomers, 'icon' => 'fa-users', 'color' => '#0ea5e9'],
    ['label' => 'Tong tho', 'value' => $totalWorkers, 'icon' => 'fa-wrench', 'color' => '#6366f1'],
    ['label' => 'San pham sap het hang', 'value' => $lowStock, 'icon' => 'fa-triangle-exclamation', 'color' => '#dc2626'],
];

$recentOrders = $DMH->get_list("SELECT * FROM `store_orders` ORDER BY id DESC LIMIT 5");
$recentBookings = $DMH->get_list("SELECT * FROM `dat_lich` ORDER BY id DESC LIMIT 5");

function statusBadge($s) {
    switch ($s) {
        case 'pending': case 'CHO_XU_LY': return '<span class="badge badge-pending">Cho xu ly</span>';
        case 'shipping': case 'DANG_XU_LY': return '<span class="badge badge-shipping">Dang xu ly</span>';
        case 'completed': case 'HOAN_THANH': return '<span class="badge badge-completed">Hoan thanh</span>';
        case 'cancelled': case 'DA_HUY': return '<span class="badge badge-cancelled">Da huy</span>';
        default: return '<span class="badge badge-default">'.htmlspecialchars($s).'</span>';
    }
}
?>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;"><i class="fa-solid fa-chart-pie" style="color:#0ea5e9;"></i> Tong quan hoat dong cong ty</h2>

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

<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:22px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-cart-shopping" style="color:#0ea5e9;"></i> Don hang san pham gan day</h3>
            <a href="/pages/admin/QuanLyDonHang.php" class="btn btn-primary btn-sm">Xem tat ca</a>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead><tr><th>Ma</th><th>Khach hang</th><th>Tong tien</th><th>Trang thai</th></tr></thead>
                <tbody>
                <?php foreach ($recentOrders as $o): ?>
                    <tr>
                        <td>#<?= (int)$o['id'] ?></td>
                        <td><?= htmlspecialchars($o['customer_name'] ?? $o['username'] ?? 'Khach') ?></td>
                        <td><?= fmt($o['total_amount'] ?? 0) ?> VND</td>
                        <td><?= statusBadge($o['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($recentOrders)): ?><tr><td colspan="4" style="text-align:center; color:#94a3b8;">Chua co don hang</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-wrench" style="color:#0ea5e9;"></i> Don goi tho gan day</h3>
            <a href="/pages/admin/QuanLyDatLich.php" class="btn btn-primary btn-sm">Xem tat ca</a>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead><tr><th>Ma</th><th>Khach hang</th><th>Dich vu</th><th>Trang thai</th></tr></thead>
                <tbody>
                <?php foreach ($recentBookings as $b): ?>
                    <tr>
                        <td>#<?= (int)$b['id'] ?></td>
                        <td><?= htmlspecialchars($b['ten'] ?? $b['username'] ?? 'Khach') ?></td>
                        <td><?= htmlspecialchars($b['dichvu'] ?? '-') ?></td>
                        <td><?= statusBadge($b['trangthai']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($recentBookings)): ?><tr><td colspan="4" style="text-align:center; color:#94a3b8;">Chua co lich hen</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
