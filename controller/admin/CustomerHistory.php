<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

if (!isset($_COOKIE['token']) || empty($getUser) || !in_array($getUser['level'], ['admin','bct'])) {
    echo 'Khong co quyen';
    exit;
}
if (empty($_SESSION['loginadmin'])) {
    echo 'Vui long dang nhap admin';
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    echo 'Thieu ma khach hang';
    exit;
}

$user = $DMH->get_row("SELECT * FROM `users` WHERE `id` = '$id'");
if (!$user) {
    echo 'Khong tim thay khach hang';
    exit;
}

$productOrders = $DMH->get_list("SELECT * FROM `store_orders` WHERE `user_id` = '$id' OR `customer_name` LIKE '%" . mysqli_real_escape_string($DMH->ketnoi, $user['username']) . "%' ORDER BY id DESC LIMIT 50");
$serviceOrders = $DMH->get_list("SELECT * FROM `dat_lich` WHERE `user_id` = '$id' OR `ten` LIKE '%" . mysqli_real_escape_string($DMH->ketnoi, ($user['name'] ?? $user['username'])) . "%' ORDER BY id DESC LIMIT 50");

function fmt($n) { return number_format((int)$n, 0, ',', '.'); }
function sttProduct($s) {
    $map = ['pending' => 'Cho xu ly', 'shipping' => 'Dang giao', 'completed' => 'Hoan thanh', 'cancelled' => 'Da huy'];
    return $map[$s] ?? $s;
}
function sttService($s) {
    $map = ['CHO_XU_LY' => 'Cho xu ly', 'DANG_XU_LY' => 'Dang xu ly', 'HOAN_THANH' => 'Hoan thanh', 'DA_HUY' => 'Da huy'];
    return $map[$s] ?? $s;
}
?>

<h4 style="margin:0 0 10px; color:#0f172a;">Thong tin khach hang</h4>
<p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
<p><strong>Ho ten:</strong> <?= htmlspecialchars($user['name'] ?? $user['fullname'] ?? '-') ?></p>
<p><strong>SDT:</strong> <?= htmlspecialchars($user['phone'] ?? '-') ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? '-') ?></p>

<hr style="border:none; border-top:1px solid #e2e8f0; margin:18px 0;">

<h4 style="margin:0 0 12px; color:#0ea5e9;">Don hang san pham (<?= count($productOrders) ?>)</h4>
<?php if (empty($productOrders)): ?>
    <p style="color:#64748b;">Khong co don hang san pham.</p>
<?php else: ?>
    <div style="overflow-x:auto;">
        <table class="table" style="font-size:12px;">
            <thead><tr><th>Ma</th><th>Tong tien</th><th>Trang thai</th><th>Thoi gian</th></tr></thead>
            <tbody>
                <?php foreach ($productOrders as $o): ?>
                    <tr>
                        <td>#<?= (int)$o['id'] ?></td>
                        <td><?= fmt($o['total_amount'] ?? 0) ?>d</td>
                        <td><?= htmlspecialchars(sttProduct($o['status'])) ?></td>
                        <td><?= !empty($o['created_at']) ? date('d/m/Y H:i', strtotime($o['created_at'])) : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<hr style="border:none; border-top:1px solid #e2e8f0; margin:18px 0;">

<h4 style="margin:0 0 12px; color:#0ea5e9;">Don goi tho (<?= count($serviceOrders) ?>)</h4>
<?php if (empty($serviceOrders)): ?>
    <p style="color:#64748b;">Khong co don goi tho.</p>
<?php else: ?>
    <div style="overflow-x:auto;">
        <table class="table" style="font-size:12px;">
            <thead><tr><th>Ma</th><th>Dich vu</th><th>Trang thai</th><th>Thoi gian</th></tr></thead>
            <tbody>
                <?php foreach ($serviceOrders as $s): ?>
                    <tr>
                        <td>#<?= (int)$s['id'] ?></td>
                        <td><?= htmlspecialchars($s['dichvu'] ?? '-') ?></td>
                        <td><?= htmlspecialchars(sttService($s['trangthai'])) ?></td>
                        <td><?= !empty($s['thoigian']) ? date('d/m/Y H:i', (int)$s['thoigian']) : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
