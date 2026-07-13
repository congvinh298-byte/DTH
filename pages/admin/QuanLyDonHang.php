<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$tieude = 'Quản lý đơn hàng sản phẩm | Điện Máy Hiếu';
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
        'pending' => ['text' => 'Đang chuẩn bị', 'class' => 'badge-pending'],
        'shipping' => ['text' => 'Đang giao hàng', 'class' => 'badge-shipping'],
        'completed' => ['text' => 'Đã giao xong', 'class' => 'badge-completed'],
        'cancelled' => ['text' => 'Đã hủy', 'class' => 'badge-cancelled'],
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

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;"><i class="fa-solid fa-box" style="color:#0ea5e9;"></i> Quản lý đơn hàng sản phẩm</h2>

<div class="filter-bar">
    <a href="?filter=all" class="<?= $filter == 'all' ? 'active' : '' ?>">Tất cả</a>
    <a href="?filter=pending" class="<?= $filter == 'pending' ? 'active' : '' ?>"><span class="badge badge-pending">Đang chuẩn bị</span></a>
    <a href="?filter=shipping" class="<?= $filter == 'shipping' ? 'active' : '' ?>"><span class="badge badge-shipping">Đang giao hàng</span></a>
    <a href="?filter=completed" class="<?= $filter == 'completed' ? 'active' : '' ?>"><span class="badge badge-completed">Đã giao xong</span></a>
    <a href="?filter=cancelled" class="<?= $filter == 'cancelled' ? 'active' : '' ?>"><span class="badge badge-cancelled">Đã hủy</span></a>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <?php if (empty($orders)): ?>
            <div style="text-align:center; padding: 50px 20px; color:#64748b;">
                <i class="fa-solid fa-box-open" style="font-size:40px; margin-bottom:14px; display:block;"></i>
                <div style="font-size:16px; font-weight:700;">Không có đơn hàng nào</div>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Khách hàng</th>
                            <th>Số điện thoại</th>
                            <th>Tổng tiền</th>
                            <th>Thanh toán</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?= (int)$order['id'] ?></strong></td>
                                <td><?= htmlspecialchars($order['customer_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($order['phone'] ?? '-') ?></td>
                                <td><strong style="color:#dc2626;"><?= number_format((int)($order['total_amount'] ?? 0)) ?>đ</strong></td>
                                <td><?= htmlspecialchars($order['payment_method'] ?? 'COD') ?><?= !empty($order['vat_requested']) ? ' <span class="badge badge-default">VAT</span>' : '' ?></td>
                                <td><?= !empty($order['created_at']) ? date('H:i d/m/Y', strtotime($order['created_at'])) : '-' ?></td>
                                <td><?= statusBadge($order['status']) ?></td>
                                <td>
                                    <?php if ($order['status'] == 'pending'): ?>
                                        <button class="btn-action btn-ship" onclick="openShipModal(<?= (int)$order['id'] ?>)"><i class="fa-solid fa-truck"></i> Giao hàng</button>
                                    <?php elseif ($order['status'] == 'shipping'): ?>
                                        <button class="btn-action btn-complete" onclick="quickComplete(<?= (int)$order['id'] ?>)"><i class="fa-solid fa-check"></i> Hoàn thành</button>
                                    <?php endif; ?>
                                    <button class="btn-action btn-detail" onclick="openOrderDetail(<?= (int)$order['id'] ?>, '<?= htmlspecialchars($order['status']) ?>')"><i class="fa-solid fa-eye"></i> Chi tiết</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal chi tiết -->
<div class="modal-backdrop" id="orderModal">
    <div class="modal-box">
        <h3><i class="fa-solid fa-clipboard-list"></i> Chi tiết đơn hàng #<span id="modalOrderId"></span></h3>
        <input type="hidden" id="modalId">
        <div id="modalContent" style="line-height:1.7; color:#334155;">
            <div style="text-align:center; color:#64748b; padding:20px;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải...</div>
        </div>
        <label>Cập nhật trạng thái</label>
        <select id="statusSelect" class="form-control" style="margin-bottom:16px;">
            <option value="pending">Đang chuẩn bị</option>
            <option value="shipping">Đang giao hàng</option>
            <option value="completed">Đã giao xong</option>
            <option value="cancelled">Đã hủy</option>
        </select>
        <div class="modal-actions">
            <button class="btn-modal-secondary" onclick="closeModal('orderModal')">Đóng</button>
            <button class="btn-modal-primary" onclick="saveStatus()">Lưu trạng thái</button>
        </div>
    </div>
</div>

<!-- Modal giao hàng -->
<div class="modal-backdrop" id="shipModal">
    <div class="modal-box">
        <h3><i class="fa-solid fa-truck"></i> Giao hàng đơn #<span id="shipModalOrderId"></span></h3>
        <input type="hidden" id="shipOrderId">
        <label>Người giao / Đơn vị vận chuyển</label>
        <input type="text" id="shipperName" class="form-control" placeholder="VD: Giao Hàng Nhanh, A.Thợ...">
        <label>Số điện thoại giao hàng</label>
        <input type="text" id="shipperPhone" class="form-control" placeholder="VD: 0901234567">
        <label>Mã vận đơn</label>
        <input type="text" id="trackingCode" class="form-control" placeholder="VD: GHN123456">
        <label>Ghi chú giao hàng</label>
        <textarea id="deliveryNote" class="form-control" placeholder="Ghi chú cho khách..." rows="3"></textarea>
        <div class="modal-actions">
            <button class="btn-modal-secondary" onclick="closeModal('shipModal')">Đóng</button>
            <button class="btn-modal-primary" onclick="saveShipInfo()">Xác nhận giao hàng</button>
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
    document.getElementById('modalContent').innerHTML = '<div style="text-align:center; color:#64748b; padding:20px;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải...</div>';
    fetch('/controller/admin/OrderDetail.php?id=' + id)
        .then(r => r.text())
        .then(html => { document.getElementById('modalContent').innerHTML = html; })
        .catch(() => { document.getElementById('modalContent').innerHTML = '<div style="color:#dc2626;">Không thể tải chi tiết.</div>'; });
    openModal('orderModal');
}
function saveStatus() {
    var id = document.getElementById('modalId').value;
    var status = document.getElementById('statusSelect').value;
    fetch('/controller/admin/OrderStatusUpdate.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&status=' + status
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            Swal.fire('Thành công', res.msg, 'success').then(() => location.reload());
        } else {
            Swal.fire('Lỗi', res.msg, 'error');
        }
    })
    .catch(() => Swal.fire('Lỗi', 'Không thể kết nối', 'error'));
}
function openShipModal(id) {
    document.getElementById('shipOrderId').value = id;
    document.getElementById('shipModalOrderId').textContent = id;
    openModal('shipModal');
}
function saveShipInfo() {
    var id = document.getElementById('shipOrderId').value;
    var name = document.getElementById('shipperName').value;
    var phone = document.getElementById('shipperPhone').value;
    var code = document.getElementById('trackingCode').value;
    var note = document.getElementById('deliveryNote').value;
    fetch('/controller/admin/OrderStatusUpdate.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&status=shipping&shipper_name=' + encodeURIComponent(name) + '&shipper_phone=' + encodeURIComponent(phone) + '&tracking_code=' + encodeURIComponent(code) + '&delivery_note=' + encodeURIComponent(note)
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            Swal.fire('Thành công', res.msg, 'success').then(() => location.reload());
        } else {
            Swal.fire('Lỗi', res.msg, 'error');
        }
    })
    .catch(() => Swal.fire('Lỗi', 'Không thể kết nối', 'error'));
    closeModal('shipModal');
}
function quickComplete(id) {
    Swal.fire({
        title: 'Hoàn thành đơn?',
        text: 'Xác nhận đơn hàng đã được giao thành công.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Đồng ý',
        cancelButtonText: 'Đóng'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/controller/admin/OrderStatusUpdate.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id + '&status=completed'
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire('Thành công', res.msg, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Lỗi', res.msg, 'error');
                }
            })
            .catch(() => Swal.fire('Lỗi', 'Không thể kết nối', 'error'));
        }
    });
}
</script>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
