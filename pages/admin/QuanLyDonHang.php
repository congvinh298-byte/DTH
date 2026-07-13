<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$tieude = 'Don hang san pham | Dien May Hieu';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$where = "1=1";
if (in_array($filter, ['pending', 'shipping', 'completed', 'cancelled'])) {
    $DMH->connect();
    $where = "`status` = '" . mysqli_real_escape_string($DMH->ketnoi, $filter) . "'";
}

$orders = $DMH->get_list("SELECT * FROM `store_orders` WHERE $where ORDER BY id DESC LIMIT 200");

function statusBadge($status) {
    $map = [
        'pending' => ['text' => 'Dang chuan bi', 'class' => 'badge-pending'],
        'shipping' => ['text' => 'Dang giao hang', 'class' => 'badge-shipping'],
        'completed' => ['text' => 'Da giao xong', 'class' => 'badge-completed'],
        'cancelled' => ['text' => 'Da huy', 'class' => 'badge-cancelled'],
    ];
    $st = $map[$status] ?? ['text' => $status, 'class' => 'badge-default'];
    return '<span class="status-badge ' . $st['class'] . '">' . htmlspecialchars($st['text']) . '</span>';
}
?>

<style>
    .status-badge { display: inline-block; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 800; }
    .btn-action { padding: 6px 12px; border-radius: 8px; border: none; font-weight: 700; font-size: 12px; cursor: pointer; transition: all 0.15s; margin-right: 4px; color:#fff; }
    .btn-ship { background: #3b82f6; }
    .btn-complete { background: #10b981; }
    .btn-detail { background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }
    .btn-detail:hover { background: #e2e8f0; }
    .table-wrap { overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 12px; }
</style>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;"><i class="fa-solid fa-box" style="color:#0ea5e9;"></i> Quan ly don hang san pham</h2>

<div class="filter-bar">
    <a href="?filter=all" class="<?= $filter == 'all' ? 'active' : '' ?>">Tat ca</a>
    <a href="?filter=pending" class="<?= $filter == 'pending' ? 'active' : '' ?>"><span class="badge badge-pending">Dang chuan bi</span></a>
    <a href="?filter=shipping" class="<?= $filter == 'shipping' ? 'active' : '' ?>"><span class="badge badge-shipping">Dang giao hang</span></a>
    <a href="?filter=completed" class="<?= $filter == 'completed' ? 'active' : '' ?>"><span class="badge badge-completed">Da giao xong</span></a>
    <a href="?filter=cancelled" class="<?= $filter == 'cancelled' ? 'active' : '' ?>"><span class="badge badge-cancelled">Da huy</span></a>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <?php if (empty($orders)): ?>
            <div style="text-align:center; padding: 50px 20px; color:#64748b;">
                <i class="fa-solid fa-box-open" style="font-size:40px; margin-bottom:14px; display:block;"></i>
                <div style="font-size:16px; font-weight:700;">Khong co don hang nao</div>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ma DH</th>
                            <th>Khach hang</th>
                            <th>So dien thoai</th>
                            <th>Tong tien</th>
                            <th>Thanh toan</th>
                            <th>Thoi gian</th>
                            <th>Trang thai</th>
                            <th>Thao tac</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?= (int)$order['id'] ?></strong></td>
                                <td><?= htmlspecialchars($order['customer_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($order['phone'] ?? '-') ?></td>
                                <td><strong style="color:#dc2626;"><?= number_format((int)($order['total_amount'] ?? 0)) ?>d</strong></td>
                                <td><?= htmlspecialchars($order['payment_method'] ?? 'COD') ?><?= !empty($order['vat_requested']) ? ' <span class="badge badge-default">VAT</span>' : '' ?></td>
                                <td><?= !empty($order['created_at']) ? date('H:i d/m/Y', strtotime($order['created_at'])) : '-' ?></td>
                                <td><?= statusBadge($order['status']) ?></td>
                                <td>
                                    <?php if ($order['status'] == 'pending'): ?>
                                        <button class="btn-action btn-ship" onclick="openShipModal(<?= (int)$order['id'] ?>)"><i class="fa-solid fa-truck"></i> Giao hang</button>
                                    <?php elseif ($order['status'] == 'shipping'): ?>
                                        <button class="btn-action btn-complete" onclick="quickComplete(<?= (int)$order['id'] ?>)"><i class="fa-solid fa-check"></i> Hoan thanh</button>
                                    <?php endif; ?>
                                    <button class="btn-action btn-detail" onclick="openOrderDetail(<?= (int)$order['id'] ?>, '<?= htmlspecialchars($order['status']) ?>')"><i class="fa-solid fa-eye"></i> Chi tiet</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal chi tiet -->
<div class="modal-backdrop" id="orderModal">
    <div class="modal-box">
        <h3><i class="fa-solid fa-clipboard-list"></i> Chi tiet don hang #<span id="modalOrderId"></span></h3>
        <input type="hidden" id="modalId">
        <div id="modalContent" style="line-height:1.7; color:#334155;">
            <div style="text-align:center; color:#64748b; padding:20px;"><i class="fa-solid fa-spinner fa-spin"></i> Dang tai...</div>
        </div>
        <label>Cap nhat trang thai</label>
        <select id="statusSelect">
            <option value="pending">Dang chuan bi</option>
            <option value="shipping">Dang giao hang</option>
            <option value="completed">Da giao xong</option>
            <option value="cancelled">Da huy</option>
        </select>
        <div class="modal-actions">
            <button class="btn-modal-secondary" onclick="closeModal('orderModal')">Dong</button>
            <button class="btn-modal-primary" onclick="saveStatus()">Luu trang thai</button>
        </div>
    </div>
</div>

<!-- Modal giao hang -->
<div class="modal-backdrop" id="shipModal">
    <div class="modal-box">
        <h3><i class="fa-solid fa-truck"></i> Giao hang don #<span id="shipModalOrderId"></span></h3>
        <input type="hidden" id="shipOrderId">
        <label>Nguoi giao / Don vi van chuyen</label>
        <input type="text" id="shipperName" placeholder="VD: Giao Hang Nhanh, A Tho...">
        <label>So dien thoai giao hang</label>
        <input type="text" id="shipperPhone" placeholder="VD: 0901234567">
        <label>Ma van don</label>
        <input type="text" id="trackingCode" placeholder="VD: GHN123456">
        <label>Ghi chu giao hang</label>
        <textarea id="deliveryNote" placeholder="Ghi chu cho khach..." rows="3"></textarea>
        <div class="modal-actions">
            <button class="btn-modal-secondary" onclick="closeModal('shipModal')">Dong</button>
            <button class="btn-modal-primary" onclick="saveShipInfo()">Xac nhan giao hang</button>
        </div>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

function openOrderDetail(id, status) {
    document.getElementById('modalId').value = id;
    document.getElementById('modalOrderId').textContent = id;
    document.getElementById('statusSelect').value = status;
    document.getElementById('modalContent').innerHTML = '<div style="text-align:center; color:#64748b; padding:20px;"><i class="fa-solid fa-spinner fa-spin"></i> Dang tai...</div>';
    fetch('/controller/admin/OrderDetail.php?id=' + id)
        .then(r => r.text())
        .then(html => { document.getElementById('modalContent').innerHTML = html; })
        .catch(() => { document.getElementById('modalContent').innerHTML = '<div style="color:#dc2626;">Khong the tai chi tiet.</div>'; });
    openModal('orderModal');
}
function saveStatus() {
    var id = document.getElementById('modalId').value;
    var status = document.getElementById('statusSelect').value;
    updateOrderStatus(id, status);
}
function openShipModal(id) {
    document.getElementById('shipOrderId').value = id;
    document.getElementById('shipModalOrderId').textContent = id;
    document.getElementById('shipperName').value = '';
    document.getElementById('shipperPhone').value = '';
    document.getElementById('trackingCode').value = '';
    document.getElementById('deliveryNote').value = '';
    openModal('shipModal');
}
function saveShipInfo() {
    var id = document.getElementById('shipOrderId').value;
    var shipperName = document.getElementById('shipperName').value.trim();
    if (!shipperName) {
        Swal.fire('Thieu thong tin', 'Vui long nhap nguoi giao / don vi van chuyen', 'warning');
        return;
    }
    var body = 'id=' + id + '&status=shipping&shipper_name=' + encodeURIComponent(shipperName) +
               '&shipper_phone=' + encodeURIComponent(document.getElementById('shipperPhone').value.trim()) +
               '&tracking_code=' + encodeURIComponent(document.getElementById('trackingCode').value.trim()) +
               '&delivery_note=' + encodeURIComponent(document.getElementById('deliveryNote').value.trim());
    fetch('/controller/admin/OrderStatusUpdate.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    })
    .then(r => r.json())
    .then(res => {
        if (res.status == 'success') {
            Swal.fire('Thanh cong', res.msg, 'success').then(() => location.reload());
        } else {
            Swal.fire('Loi', res.msg, 'error');
        }
    })
    .catch(() => Swal.fire('Loi', 'Khong the ket noi may chu', 'error'));
}
function quickComplete(id) {
    Swal.fire({
        title: 'Xac nhan',
        text: 'Hoan thanh don hang nay?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Dong y',
        cancelButtonText: 'Huy'
    }).then((result) => {
        if (result.isConfirmed) updateOrderStatus(id, 'completed');
    });
}
function updateOrderStatus(id, status) {
    fetch('/controller/admin/OrderStatusUpdate.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&status=' + status
    })
    .then(r => r.json())
    .then(res => {
        if (res.status == 'success') {
            Swal.fire('Thanh cong', res.msg, 'success').then(() => location.reload());
        } else {
            Swal.fire('Loi', res.msg, 'error');
        }
    })
    .catch(() => Swal.fire('Loi', 'Khong the ket noi may chu', 'error'));
}
</script>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
