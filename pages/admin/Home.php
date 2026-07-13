<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();
$tieude = 'Dashboard | Dien May Hieu';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");

function fmt($n) { return number_format($n, 0, ',', '.'); }

$stats = [
    ['label' => 'Don hang cho xu ly', 'value' => $DMH->num_rows("SELECT * FROM `store_orders` WHERE `status` = 'pending'"), 'icon' => 'fa-box', 'color' => '#2563eb'],
    ['label' => 'Don hang dang giao', 'value' => $DMH->num_rows("SELECT * FROM `store_orders` WHERE `status` = 'shipping'"), 'icon' => 'fa-truck', 'color' => '#0891b2'],
    ['label' => 'Lich hen cho xac nhan', 'value' => $DMH->num_rows("SELECT * FROM `dat_lich` WHERE `status` = 'pending'"), 'icon' => 'fa-calendar-check', 'color' => '#f59e0b'],
    ['label' => 'Lich hen da xac nhan', 'value' => $DMH->num_rows("SELECT * FROM `dat_lich` WHERE `status` = 'confirmed'"), 'icon' => 'fa-circle-check', 'color' => '#16a34a'],
];

$recentOrders = $DMH->get_list("SELECT * FROM `store_orders` ORDER BY id DESC LIMIT 5");
$recentBookings = $DMH->get_list("SELECT * FROM `dat_lich` ORDER BY id DESC LIMIT 5");

function statusBadge($s) {
    switch ($s) {
        case 'pending': return '<span class="badge badge-pending">Cho xu ly</span>';
        case 'shipping': return '<span class="badge badge-shipping">Dang giao</span>';
        case 'completed': return '<span class="badge badge-completed">Hoan thanh</span>';
        case 'cancelled': return '<span class="badge badge-cancelled">Da huy</span>';
        case 'confirmed': return '<span class="badge badge-completed">Xac nhan</span>';
        default: return '<span class="badge badge-pending">'.$s.'</span>';
    }
}
?>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;"><i class="fa-solid fa-chart-pie" style="color:#0ea5e9;"></i> Tong quan</h2>

<div class="stats-grid">
<?php foreach ($stats as $s): ?>
    <div class="stat-card">
        <div>
            <h4><?= $s['label'] ?></h4>
            <div class="value"><?= fmt($s['value']) ?></div>
        </div>
        <div class="icon" style="background:<?= $s['color'] ?>20; color:<?= $s['color'] ?>;">
            <i class="fa-solid <?= $s['icon'] ?>"></i>
        </div>
    </div>
<?php endforeach; ?>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:22px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-cart-shopping" style="color:#0ea5e9;"></i> Don hang gan day</h3>
            <a href="/pages/admin/QuanLyDonHang.php" class="btn btn-primary btn-sm">Xem tat ca</a>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead><tr><th>Ma</th><th>Khach hang</th><th>Tong tien</th><th>Trang thai</th></tr></thead>
                <tbody>
                <?php foreach ($recentOrders as $o): ?>
                    <tr>
                        <td>#<?= $o['id'] ?></td>
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
            <h3><i class="fa-solid fa-wrench" style="color:#0ea5e9;"></i> Lich hen gan day</h3>
            <a href="/pages/admin/QuanLyDatLich.php" class="btn btn-primary btn-sm">Xem tat ca</a>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead><tr><th>Ma</th><th>Khach hang</th><th>Dich vu</th><th>Trang thai</th></tr></thead>
                <tbody>
                <?php foreach ($recentBookings as $b): ?>
                    <tr>
                        <td>#<?= $b['id'] ?></td>
                        <td><?= htmlspecialchars($b['name'] ?? $b['username'] ?? 'Khach') ?></td>
                        <td><?= htmlspecialchars($b['service'] ?? '-') ?></td>
                        <td><?= statusBadge($b['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($recentBookings)): ?><tr><td colspan="4" style="text-align:center; color:#94a3b8;">Chua co lich hen</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
