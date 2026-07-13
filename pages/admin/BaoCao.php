<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$tieude = 'Bao cao | Dien May Hieu';
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

$stats = [
    ['label' => 'Tong don hang san pham', 'value' => fmt($totalProductOrders), 'icon' => 'fa-box', 'color' => '#2563eb'],
    ['label' => 'Tong don goi tho', 'value' => fmt($totalServiceOrders), 'icon' => 'fa-calendar-check', 'color' => '#f59e0b'],
    ['label' => 'Tong khach hang', 'value' => fmt($totalCustomers), 'icon' => 'fa-users', 'color' => '#0ea5e9'],
    ['label' => 'Tong tho', 'value' => fmt($totalWorkers), 'icon' => 'fa-wrench', 'color' => '#6366f1'],
    ['label' => 'Doanh thu san pham', 'value' => fmt($productRevenue), 'icon' => 'fa-sack-dollar', 'color' => '#16a34a'],
    ['label' => 'Doanh thu dich vu duyet', 'value' => fmt($serviceRevenue), 'icon' => 'fa-file-invoice-dollar', 'color' => '#9333ea'],
    ['label' => 'San pham sap het hang', 'value' => fmt($lowStock), 'icon' => 'fa-triangle-exclamation', 'color' => '#dc2626'],
];
?>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;"><i class="fa-solid fa-chart-line" style="color:#0ea5e9;"></i> Bao cao tong hop</h2>

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
    <div class="card-header"><h3>Ghi chu</h3></div>
    <div class="card-body">
        <p>Bao cao duoc tong hop tu cac nguon du lieu hien co tren he thong. Doanh thu dich vu chi tinh cac don co phat sinh da duyet.</p>
    </div>
</div>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
