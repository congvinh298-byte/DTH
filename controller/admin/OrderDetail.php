<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

if (!isset($_COOKIE['token']) || empty($getUser) || $getUser['level'] != 'admin') {
    echo 'Không có quyền';
    exit;
}

if (empty($_GET['id'])) {
    echo 'Thiếu mã đơn hàng';
    exit;
}

$id = (int)$_GET['id'];
$order = $DMH->get_row("SELECT * FROM `store_orders` WHERE `id` = '$id'");

if (!$order) {
    echo 'Đơn hàng không tồn tại';
    exit;
}

// Lấy chi tiết sản phẩm trong đơn (nếu có bảng store_order_items)
$items = [];
try {
    $items = $DMH->get_list("SELECT * FROM `store_order_items` WHERE `order_id` = '$id' ORDER BY id ASC");
} catch (Throwable $e) {
    // Bảng có thể chưa tồn tại
}

$statusText = [
    'pending' => 'Chờ xử lý',
    'shipping' => 'Đang giao hàng',
    'completed' => 'Đã hoàn thành',
    'cancelled' => 'Đã hủy'
][$order['status']] ?? $order['status'];
?>

<div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 16px; margin-bottom: 16px;">
    <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">Khách hàng:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
    <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">Số điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
    <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">Địa chỉ:</strong> <?= nl2br(htmlspecialchars($order['address'])) ?></p>
    <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">Thanh toán:</strong> <?= $order['payment_method'] == 'COD' ? 'Khi nhận hàng' : 'Chuyển khoản' ?></p>
    <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">Tổng tiền:</strong> <span style="color: #f43f5e; font-weight: bold;"><?= number_format($order['total_amount']) ?>đ</span></p>
    <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">VAT:</strong> <?= $order['vat_requested'] ? 'Có xuất hóa đơn VAT' : 'Không' ?></p>
    <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">Ghi chú:</strong> <?= nl2br(htmlspecialchars($order['note'] ?: 'Không có')) ?></p>
    <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">Thời gian:</strong> <?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></p>
</div>

<?php if (!empty($items)): ?>
    <h4 style="margin: 16px 0 8px; color: #38bdf8;">🛒 Sản phẩm trong đơn</h4>
    <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 16px;">
        <?php foreach ($items as $item): ?>
            <div style="border-bottom: 1px solid rgba(255,255,255,0.05); padding: 8px 0;">
                <strong><?= htmlspecialchars($item['product_name'] ?? 'Sản phẩm') ?></strong>
                × <?= (int)($item['quantity'] ?? 1) ?>
                <span style="float: right; color: #f43f5e;"><?= number_format($item['price'] ?? 0) ?>đ</span>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
