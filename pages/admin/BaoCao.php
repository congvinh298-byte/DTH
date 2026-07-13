<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();
$tieude = 'Bao cao | Dien May Hieu';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");

$totalOrders = $DMH->num_rows("SELECT * FROM `store_orders`");
$totalBookings = $DMH->num_rows("SELECT * FROM `dat_lich`");
$totalUsers = $DMH->num_rows("SELECT * FROM `users` WHERE `level` = 'user'");
$totalRevenue = $DMH->get_row("SELECT SUM(`total_amount`) as s FROM `store_orders` WHERE `status` = 'completed'")['s'] ?? 0;
?>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800;"><i class="fa-solid fa-chart-line" style="color:#0ea5e9;"></i> Bao cao</h2>

<div class="stats-grid">
    <div class="stat-card">
        <div><h4>Tong don hang</h4><div class="value"><?= number_format($totalOrders, 0, ',', '.') ?></div></div>
        <div class="icon" style="background:#dbeafe; color:#2563eb;"><i class="fa-solid fa-box"></i></div>
    </div>
    <div class="stat-card">
        <div><h4>Tong lich hen</h4><div class="value"><?= number_format($totalBookings, 0, ',', '.') ?></div></div>
        <div class="icon" style="background:#fef3c7; color:#d97706;"><i class="fa-solid fa-calendar"></i></div>
    </div>
    <div class="stat-card">
        <div><h4>Tong thanh vien</h4><div class="value"><?= number_format($totalUsers, 0, ',', '.') ?></div></div>
        <div class="icon" style="background:#dcfce7; color:#16a34a;"><i class="fa-solid fa-users"></i></div>
    </div>
    <div class="stat-card">
        <div><h4>Doanh thu hoan thanh</h4><div class="value"><?= number_format($totalRevenue, 0, ',', '.') ?></div></div>
        <div class="icon" style="background:#f3e8ff; color:#9333ea;"><i class="fa-solid fa-sack-dollar"></i></div>
    </div>
</div>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
