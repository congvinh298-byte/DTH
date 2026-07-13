<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

// Debug log
$debugLog = '/home/kwkrbcce/public_html/qldh_debug.log';
$log = "=== " . date('Y-m-d H:i:s') . " ===\n";
$log .= "URI: " . ($_SERVER['REQUEST_URI'] ?? 'none') . "\n";
$log .= "Token exists: " . (isset($_COOKIE['token']) ? 'YES ' . substr($_COOKIE['token'], 0, 16) . '...' : 'NO') . "\n";
$log .= "getUser empty: " . (empty($getUser) ? 'YES' : 'NO') . "\n";
if (!empty($getUser)) {
    $log .= "getUser level: " . ($getUser['level'] ?? 'null') . "\n";
    $log .= "getUser id: " . ($getUser['id'] ?? 'null') . "\n";
    $log .= "getUser username: " . ($getUser['username'] ?? 'null') . "\n";
}
$log .= "loginadmin session: " . (isset($_SESSION['loginadmin']) ? 'YES' : 'NO') . "\n";
$log .= "-----\n";
file_put_contents($debugLog, $log, FILE_APPEND | LOCK_EX);

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
        'pending' => ['text' => 'Đang chuẩn bị', 'color' => '#f59e0b'],
        'shipping' => ['text' => 'Đang giao hàng', 'color' => '#3b82f6'],
        'completed' => ['text' => 'Đã giao xong', 'color' => '#10b981'],
        'cancelled' => ['text' => 'Đã hủy', 'color' => '#ef4444'],
    ];
    $st = $map[$status] ?? ['text' => $status, 'color' => '#64748b'];
    return "<span style='background: " . $st['color'] . "20; color: " . $st['color'] . "; border: 1px solid " . $st['color'] . "40; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 800;'>" . htmlspecialchars($st['text']) . "</span>";
}
?>

<style>
    .qldh-wrap { padding: 24px; max-width: 1400px; margin: 0 auto; color: #fff; }
    .qldh-title { font-size: 26px; font-weight: 900; margin-bottom: 20px; }
    .filter-bar { display: flex; gap: 10px; margin-bottom: 24px; flex-wrap: wrap; }
    .filter-bar a { padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 700; color: #94a3b8; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); }
    .filter-bar a.active { background: #38bdf8; color: #0f172a; border-color: #38bdf8; }
    .qldh-table { width: 100%; border-collapse: collapse; background: rgba(15,23,42,0.5); border-radius: 12px; overflow: hidden; }
    .qldh-table th, .qldh-table td { padding: 14px 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06); color: #cbd5e1; font-size: 14px; }
    .qldh-table th { background: rgba(56,189,248,0.1); color: #38bdf8; font-weight: 800; }
    .qldh-table tr:hover { background: rgba(255,255,255,0.03); }
    .btn-detail { padding: 6px 12px; border-radius: 6px; border: none; background: #38bdf8; color: #0f172a; font-weight: 800; cursor: pointer; font-size: 12px; }
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; }
    .modal-backdrop.active { display: flex; }
    .modal-box { background: #1e293b; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 24px; width: 90%; max-width: 520px; color: #fff; max-height: 90vh; overflow-y: auto; }
    .modal-box h3 { margin-top: 0; }
    .modal-box select, .modal-box input, .modal-box textarea { width: 100%; padding: 12px; margin: 8px 0; background: #0f172a; border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px; box-sizing: border-box; }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 16px; }
    @media (max-width: 900px) { .qldh-table { font-size: 12px; } .qldh-table th, .qldh-table td { padding: 10px 8px; } }
</style>

<div class="qldh-wrap">
    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
        <img src="/public/assets/logo.png" alt="Điện Máy Hiếu" style="width: 60px; height: 60px; object-fit: contain;">
        <div>
            <div class="qldh-title" style="margin-bottom: 4px;">📦 Quản Lý Đơn Hàng</div>
            <div style="color: #94a3b8; font-size: 14px;">Điện Máy Hiếu - Lấp Vò, Đồng Tháp</div>
        </div>
    </div>

    <div class="filter-bar">
        <a href="?filter=all" class="<?= $filter == 'all' ? 'active' : '' ?>">Tất cả</a>
        <a href="?filter=pending" class="<?= $filter == 'pending' ? 'active' : '' ?>">Đang chuẩn bị</a>
        <a href="?filter=shipping" class="<?= $filter == 'shipping' ? 'active' : '' ?>">Đang giao hàng</a>
        <a href="?filter=completed" class="<?= $filter == 'completed' ? 'active' : '' ?>">Đã giao xong</a>
        <a href="?filter=cancelled" class="<?= $filter == 'cancelled' ? 'active' : '' ?>">Đã hủy</a>
    </div>

    <?php if (empty($orders)): ?>
        <div style="text-align:center; color:#94a3b8; padding: 40px;">Không có đơn hàng nào.</div>
    <?php else: ?>
        <table class="qldh-table">
            <thead>
                <tr>
                    <th>Mã ĐH</th>
                    <th>Khách hàng</th>
                    <th>SĐT</th>
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
                        <td>#<?= (int)$order['id'] ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= htmlspecialchars($order['phone']) ?></td>
                        <td><strong style="color:#f43f5e;"><?= number_format($order['total_amount']) ?>đ</strong></td>
                        <td><?= $order['payment_method'] == 'COD' ? 'Khi nhận hàng' : 'Chuyển khoản' ?> <?= $order['vat_requested'] ? '(VAT)' : '' ?></td>
                        <td><?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></td>
                        <td><?= statusBadge($order['status']) ?></td>
                        <td>
                            <?php if ($order['status'] == 'pending'): ?>
                                <button class="btn-detail" style="background:#3b82f6;" onclick="openShipModal(<?= (int)$order['id'] ?>)">🚚 Giao hàng</button>
                            <?php elseif ($order['status'] == 'shipping'): ?>
                                <button class="btn-detail" style="background:#10b981;" onclick="quickComplete(<?= (int)$order['id'] ?>)">✅ Hoàn thành</button>
                            <?php elseif ($order['status'] == 'completed'): ?>
                                <span style="color:#10b981; font-weight:700;">Đã giao xong</span>
                            <?php elseif ($order['status'] == 'cancelled'): ?>
                                <span style="color:#ef4444; font-weight:700;">Đã hủy</span>
                            <?php endif; ?>
                            <button class="btn-detail" onclick="openOrderDetail(<?= (int)$order['id'] ?>, '<?= htmlspecialchars($order['status']) ?>')" style="margin-left: 6px;">Chi tiết</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Modal chi tiết đơn -->
<div class="modal-backdrop" id="orderModal">
    <div class="modal-box">
        <h3>📋 Chi tiết đơn hàng #<span id="modalOrderId"></span></h3>
        <input type="hidden" id="modalId">
        <div id="modalContent" style="margin: 16px 0; line-height: 1.6; color: #cbd5e1;">
            <!-- Nội dung chi tiết sẽ load bằng AJAX -->
        </div>
        
        <label style="color: #94a3b8; font-size: 13px;">Cập nhật trạng thái:</label>
        <select id="statusSelect">
            <option value="pending">Đang chuẩn bị</option>
            <option value="shipping">Đang giao hàng</option>
            <option value="completed">Đã giao xong</option>
            <option value="cancelled">Đã hủy</option>
        </select>
        
        <div class="modal-actions">
            <button onclick="closeModal('orderModal')" style="padding: 8px 16px; border-radius:8px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#fff; cursor:pointer;">Đóng</button>
            <button onclick="saveStatus()" style="padding: 8px 16px; border-radius:8px; border:none; background:#38bdf8; color:#0f172a; font-weight:800; cursor:pointer;">Lưu trạng thái</button>
        </div>
    </div>
</div>

<!-- Modal nhập thông tin giao hàng -->
<div class="modal-backdrop" id="shipModal">
    <div class="modal-box">
        <h3>🚚 Giao hàng đơn #<span id="shipModalOrderId"></span></h3>
        <input type="hidden" id="shipOrderId">
        
        <label style="color: #94a3b8; font-size: 13px;">Tên người giao / đơn vị vận chuyển:</label>
        <input type="text" id="shipperName" placeholder="VD: Giao Hàng Nhanh, A Thợ...">
        
        <label style="color: #94a3b8; font-size: 13px;">Số điện thoại giao hàng:</label>
        <input type="text" id="shipperPhone" placeholder="VD: 0901234567">
        
        <label style="color: #94a3b8; font-size: 13px;">Mã vận đơn:</label>
        <input type="text" id="trackingCode" placeholder="VD: GHN123456">
        
        <label style="color: #94a3b8; font-size: 13px;">Ghi chú giao hàng:</label>
        <textarea id="deliveryNote" placeholder="Ghi chú cho khách..." rows="3"></textarea>
        
        <div class="modal-actions">
            <button onclick="closeModal('shipModal')" style="padding: 8px 16px; border-radius:8px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#fff; cursor:pointer;">Đóng</button>
            <button onclick="saveShipInfo()" style="padding: 8px 16px; border-radius:8px; border:none; background:#3b82f6; color:#fff; font-weight:800; cursor:pointer;">🚚 Xác nhận giao hàng</button>
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
    document.getElementById('modalContent').innerHTML = '<div style="text-align:center; color:#94a3b8;">Đang tải...</div>';
    
    fetch('/controller/admin/OrderDetail.php?id=' + id)
        .then(r => r.text())
        .then(html => {
            document.getElementById('modalContent').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('modalContent').innerHTML = 'Không thể tải chi tiết.';
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
