<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$tieude = 'Khach hang | Dien May Hieu';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");

$keyword = trim($_GET['q'] ?? '');
$where = "(`level` = 'user' OR `level` IS NULL OR `level` = '')";
if ($keyword !== '') {
    $DMH->connect();
    $k = mysqli_real_escape_string($DMH->ketnoi, $keyword);
    $where .= " AND (`username` LIKE '%$k%' OR `name` LIKE '%$k%' OR `fullname` LIKE '%$k%' OR `email` LIKE '%$k%' OR `phone` LIKE '%$k%')";
}
$customers = $DMH->get_list("SELECT * FROM `users` WHERE $where ORDER BY id DESC LIMIT 200");
?>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;"><i class="fa-solid fa-users" style="color:#0ea5e9;"></i> Quan ly khach hang</h2>

<div class="card">
    <div class="card-body">
        <form method="GET" class="search-box">
            <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="Tim theo username, ho ten, email, so dien thoai...">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Tim kiem</button>
            <?php if ($keyword !== ''): ?>
                <a href="?" class="btn btn-secondary">Xoa loc</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Danh sach khach hang</h3></div>
    <div class="card-body" style="padding:0;">
        <?php if (empty($customers)): ?>
            <div style="text-align:center; padding: 40px 20px; color:#64748b;">Khong tim thay khach hang.</div>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Ho ten</th>
                            <th>Email</th>
                            <th>So dien thoai</th>
                            <th>Ngay dang ky</th>
                            <th>Trang thai</th>
                            <th>Thao tac</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $c): ?>
                            <tr>
                                <td><?= (int)$c['id'] ?></td>
                                <td><strong><?= htmlspecialchars($c['username']) ?></strong></td>
                                <td><?= htmlspecialchars($c['name'] ?? $c['fullname'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($c['phone'] ?? '-') ?></td>
                                <td><?= !empty($c['timereg']) ? htmlspecialchars($c['timereg']) : (!empty($c['created_at']) ? date('d/m/Y', strtotime($c['created_at'])) : '-') ?></td>
                                <td>
                                    <?php if (!empty($c['banned']) && $c['banned'] == 'ON'): ?>
                                        <span class="badge badge-completed">Hoat dong</span>
                                    <?php else: ?>
                                        <span class="badge badge-cancelled">Khoa</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="#" onclick="viewCustomerOrders(<?= (int)$c['id'] ?>); return false;" class="btn btn-info btn-sm">
                                        <i class="fa-solid fa-eye"></i> Don hang
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal-backdrop" id="customerModal">
    <div class="modal-box" style="max-width:640px;">
        <h3>Lich su khach hang</h3>
        <div id="customerModalContent" style="max-height:60vh; overflow-y:auto;">Dang tai...</div>
        <div class="modal-actions">
            <button class="btn-modal-secondary" onclick="document.getElementById('customerModal').classList.remove('active')">Dong</button>
        </div>
    </div>
</div>

<script>
function viewCustomerOrders(userId) {
    document.getElementById('customerModal').classList.add('active');
    document.getElementById('customerModalContent').innerHTML = '<div style="text-align:center; padding:20px;"><i class="fa-solid fa-spinner fa-spin"></i> Dang tai...</div>';
    fetch('/controller/admin/CustomerHistory.php?id=' + userId)
        .then(r => r.text())
        .then(html => { document.getElementById('customerModalContent').innerHTML = html; })
        .catch(() => { document.getElementById('customerModalContent').innerHTML = '<div style="color:#dc2626;">Khong the tai du lieu.</div>'; });
}
</script>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
