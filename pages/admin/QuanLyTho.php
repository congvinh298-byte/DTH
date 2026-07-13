<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();
$tieude = 'Quan ly tho | Dien May Hieu';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");

$tho = $DMH->get_list("SELECT * FROM `users` WHERE `level` = 'tho' ORDER BY id DESC LIMIT 100");
?>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800;"><i class="fa-solid fa-wrench" style="color:#0ea5e9;"></i> Quan ly tho</h2>

<div class="card">
    <div class="card-header"><h3>Danh sach tho</h3></div>
    <div class="card-body" style="padding:0;">
        <table class="table">
            <thead><tr><th>ID</th><th>Tai khoan</th><th>Ho ten</th><th>So dien thoai</th><th>Trang thai</th></tr></thead>
            <tbody>
            <?php foreach ($tho as $t): ?>
                <tr>
                    <td><?= $t['id'] ?></td>
                    <td><?= htmlspecialchars($t['username']) ?></td>
                    <td><?= htmlspecialchars($t['fullname'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($t['phone'] ?? '-') ?></td>
                    <td><span class="badge badge-completed">Hoat dong</span></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($tho)): ?>
                <tr><td colspan="5" style="text-align:center; color:#94a3b8;">Chua co tho nao</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
