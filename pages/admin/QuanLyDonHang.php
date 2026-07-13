<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

if (!isset($_COOKIE['token']) || empty($getUser)) {
    header("Location: /pages/admin/LoginAdmin.php");
    exit;
}
if ($getUser['level'] != 'admin') {
    header("Location: /");
    exit;
}
$_SESSION['loginadmin'] = true;

$tieude = 'Quản Lý Đơn Hàng | Điện Máy Hiếu';
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
    .qldh-container { padding: 24px; background: #f8fafc; min-height: calc(100vh - 60px); }
    .qldh-card { background: #fff; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 24px; margin-bottom: 24px; }
    .qldh-header { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 2px solid #e2e8f0; }
    .qldh-logo { width: 70px; height: 70px; object-fit: contain; border-radius: 12px; background: #fff; padding: 6px; border: 2px solid #e2e8f0; }
    .qldh-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; }
    .qldh-subtitle { color: #64748b; font-size: 14px; margin-top: 4px; font-weight: 600; }
    .filter-bar { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
    .filter-bar a { padding: 10px 18px; border-radius: 999px; text-decoration: none; font-weight: 700; color: #475569; background: #f1f5f9; border: 1px solid #e2e8f0; transition: all 0.15s; }
    .filter-bar a:hover { background: #e2e8f0; }
    .filter-bar a.active { background: #0ea5e9; color: #fff; border-color: #0ea5e9; box-shadow: 0 4px 12px rgba(14,165,233,0.25); }
    .qldh-table-wrap { overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 12px; }
    .qldh-table { width: 100%; border-collapse: collapse; background: #fff; }
    .qldh-table th, .qldh-table td { padding: 16px 14px; text-align: left; border-bottom: 1px solid #e2e8f0; color: #334155; font-size: 14px; }
    .qldh-table th { background: #f8fafc; color: #0f172a; font-weight: 800; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
    .qldh-table tr:last-child td { border-bottom: none; }
    .qldh-table tr:hover { background: #f8fafc; }
    .qldh-table td strong { font-weight: 700; }
    .qldh-empty { text-align: center; padding: 60px 20px; color: #64748b; }
    .qldh-empty i { font-size: 48px; color: #cbd5e1; margin-bottom: 16px; display: block; }
    .status-badge { display: inline-block; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 800; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-shipping { background: #dbeafe; color: #1e40af; }
    .badge-completed { background: #d1fae5; color: #065f46; }
    .badge-cancelled { background: #fee2e2; color: #991b1b; }
    .badge-default { background: #f1f5f9; color: #475569; }
    .btn-action { padding: 8px 14px; border-radius: 8px; border: none; font-weight: 700; font-size: 12px; cursor: pointer; transition: all 0.15s; margin-right: 4px; }
    .btn-ship { background: #3b82f6; color: #fff; }
    .btn-ship:hover { background: #2563eb; }
    .btn-complete { background: #10b981; color: #fff; }
    .btn-complete:hover { background: #059669; }
    .btn-detail { background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }
    .btn-detail:hover { background: #e2e8f0; }
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 10000; align-items: center; justify-content: center; padding: 20px; }
    .modal-backdrop.active { display: flex; }
    .modal-box { background: #fff; border-radius: 16px; padding: 28px; width: 100%; max-width: 540px; color: #0f172a; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
    .modal-box h3 { margin-top: 0; margin-bottom: 20px; color: #0f172a; font-size: 20px; }
    .modal-box label { display: block; color: #475569; font-size: 13px; font-weight: 700; margin: 14px 0 6px; text-transform: uppercase; }
    .modal-box select, .modal-box input, .modal-box textarea { width: 100%; padding: 12px 14px; background: #f8fafc; border: 2px solid #e2e8f0; color: #0f172a; border-radius: 10px; font-size: 14px; box-sizing: border-box; }
    .modal-box select:focus, .modal-box input:focus, .modal-box textarea:focus { outline: none; border-color: #38bdf8; background: #fff; }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 22px; }
    .modal-actions button { padding: 10px 18px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer; }
    .btn-modal-primary { background: #0ea5e9; color: #fff; }
    .btn-modal-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    @media (max-width: 768px) {
        .qldh-header { flex-direction: column; text-align: center; }
        .qldh-table th, .qldh-table td { padding: 12px 10px; font-size: 13px; white-space: nowrap; }
    }
</style>

<div class="qldh-container">
    <div class="qldh-card">
        <div class="qldh-header">
            <img src="/public/assets/logo.png" alt="Điện Máy Hiếu" class="qldh-logo">
            <div>
                <h2 class="qldh-title">📦 Quản Lý Đơn Hàng Sản Phẩm</h2>
                <div class="qldh-subtitle">Công ty TNHH MTV Điện Tử Hiếu | Lấp Vò, Đồng Tháp</div>
            </div>
        </div>

        <div class="filter-bar">
            <a href="?filter=all" class="<?= $filter == 'all' ? 'active' : '' ?>">Tất cả</a>
            <a href="?filter=pending" class="<?= $filter == 'pending' ? 'active' : '' ?>"><span class="status-badge badge-pending">Đang chuẩn bị</span></a>
            <a href="?filter=shipping" class="<?= $filter == 'shipping' ? 'active' : '' ?>"><span class="status-badge badge-shipping">Đang giao hàng</span></a>
            <a href="?filter=completed" class="<?= $filter == 'completed' ? 'active' : '' ?>"><span class="status-badge badge-completed">Đã giao xong</span></a>
            <a href="?filter=cancelled" class="<?= $filter == 'cancelled' ? 'active' : '' ?>"><span class="status-badge badge-cancelled">Đã hủy</span></a>
        </div>

        <?php if (empty($orders)): ?>
            <div class="qldh-empty">
                <i class="fa-solid fa-box-open"></i>
                <div style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Không có đơn hàng nào</div>
                <div style="font-size: 14px;">Hiện chưa có đơn hàng sản phẩm nào trong hệ thống.</div>
            </div>
        <?php else: ?>
            <div class="qldh-table-wrap">
                <table class="qldh-table">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Khách hàng</th>
                            <th>Số điện thoại</th>
                            <th>Tổng tiền</th>
                            <th>Thanh toán</th>
                            <th>Thời gian đặt</th>
                            <th>Trạng thái</th>
                            <th style="min-width: 200px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?= (int)$order['id'] ?></strong></td>
                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td><?= htmlspecialchars($order['phone']) ?></td>
                                <td><strong style="color:#dc2626;"><?= number_format($order['total_amount']) ?>đ</strong></td>
                                <td><?= $order['payment_method'] == 'COD' ? 'Khi nhận hàng' : 'Chuyển khoản' ?> <?= $order['vat_requested'] ? '<span class="status-badge badge-default">VAT</span>' : '' ?></td>
                                <td><?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></td>
                                <td><?= statusBadge($order['status']) ?></td>
                                <td>
                                    <?php if ($order['status'] == 'pending'): ?>
                                        <button class="btn-action btn-ship" onclick="openShipModal(<?= (int)$order['id'] ?>)">🚚 Giao hàng</button>
                                    <?php elseif ($order['status'] == 'shipping'): ?>
                                        <button class="btn-action btn-complete" onclick="quickComplete(<?= (int)$order['id'] ?>)">✅ Hoàn thành</button>
                                    <?php elseif ($order['status'] == 'completed'): ?>
                                        <span class="status-badge badge-completed">Đã giao xong</span>
                                    <?php elseif ($order['status'] == 'cancelled'): ?>
                                        <span class="status-badge badge-cancelled">Đã hủy</span>
                                    <?php endif; ?>
                                    <button class="btn-action btn-detail" onclick="openOrderDetail(<?= (int)$order['id'] ?>, '<?= htmlspecialchars($order['status']) ?>')">Chi tiết</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal chi tiết đơn -->
<div class="modal-backdrop" id="orderModal">
    <div class="modal-box">
        <h3>📋 Chi tiết đơn hàng #<span id="modalOrderId"></span></h3>
        <input type="hidden" id="modalId">
        <div id="modalContent" style="line-height: 1.7; color: #334155;">
            <!-- Nội dung chi tiết load bằng AJAX -->
        </div>
        
        <label>Cập nhật trạng thái</label>
        <select id="statusSelect">
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

<!-- Modal nhập thông tin giao hàng -->
<div class="modal-backdrop" id="shipModal">
    <div class="modal-box">
        <h3>🚚 Giao hàng đơn #<span id="shipModalOrderId"></span></h3>
        <input type="hidden" id="shipOrderId">
        
        <label>Tên người giao / Đơn vị vận chuyển</label>
        <input type="text" id="shipperName" placeholder="VD: Giao Hàng Nhanh, A Thợ...">
        
        <label>Số điện thoại giao hàng</label>
        <input type="text" id="shipperPhone" placeholder="VD: 0901234567">
        
        <label>Mã vận đơn</label>
        <input type="text" id="trackingCode" placeholder="VD: GHN123456">
        
        <label>Ghi chú giao hàng</label>
        <textarea id="deliveryNote" placeholder="Ghi chú cho khách..." rows="3"></textarea>
        
        <div class="modal-actions">
            <button class="btn-modal-secondary" onclick="closeModal('shipModal')">Đóng</button>
            <button class="btn-modal-primary" onclick="saveShipInfo()">🚚 Xác nhận giao hàng</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

function openOrderDetail(id, status) {
    document.getElementById('modalId').value = id;
    document.getElementById('modalOrderId').textContent = id;
    document.getElementById('statusSelect').value = status;
    document.getElementById('modalContent').innerHTML = '<div style="text-align:center; color:#64748b; padding: 20px;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải...</div>';
    
    fetch('/controller/admin/OrderDetail.php?id=' + id)
        .then(r => r.text())
        .then(html => {
            document.getElementById('modalContent').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('modalContent').innerHTML = '<div style="color:#dc2626;">Không thể tải chi tiết.</div>';
        });
    
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
    var shipperPhone = document.getElementById('shipperPhone').value.trim();
    var trackingCode = document.getElementById('trackingCode').value.trim();
    var deliveryNote = document.getElementById('deliveryNote').value.trim();
    
    if (!shipperName) {
        Swal.fire('Thiếu thông tin', 'Vui lòng nhập tên người giao / đơn vị vận chuyển', 'warning');
        return;
    }
    
    var body = 'id=' + id + '&status=shipping&shipper_name=' + encodeURIComponent(shipperName) +
               '&shipper_phone=' + encodeURIComponent(shipperPhone) +
               '&tracking_code=' + encodeURIComponent(trackingCode) +
               '&delivery_note=' + encodeURIComponent(deliveryNote);
    
    fetch('/controller/admin/OrderStatusUpdate.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    })
    .then(r => r.json())
    .then(res => {
        if (res.status == 'success') {
            Swal.fire('Thành công', res.msg, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Lỗi', res.msg, 'error');
        }
    })
    .catch(() => Swal.fire('Lỗi', 'Không thể kết nối máy chủ', 'error'));
}

function quickComplete(id) {
    Swal.fire({
        title: 'Xác nhận',
        text: 'Hoàn thành đơn hàng này?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Đồng ý',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            updateOrderStatus(id, 'completed');
        }
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
            Swal.fire('Thành công', res.msg, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Lỗi', res.msg, 'error');
        }
    })
    .catch(() => Swal.fire('Lỗi', 'Không thể kết nối máy chủ', 'error'));
}
</script>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
