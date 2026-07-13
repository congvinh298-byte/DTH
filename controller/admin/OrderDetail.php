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

$items = [];
try {
    $items = $DMH->get_list("SELECT * FROM `store_order_items` WHERE `order_id` = '$id' ORDER BY id ASC");
} catch (Throwable $e) {}

$statusText = [
    'pending' => 'Đang chuẩn bị',
    'shipping' => 'Đang giao hàng',
    'completed' => 'Đã giao xong',
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
    <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">Thời gian đặt:</strong> <?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></p>
</div>

<?php if (!empty($order['shipper_name']) || $order['status'] == 'shipping' || $order['status'] == 'completed'): ?>
    <h4 style="margin: 16px 0 8px; color: #38bdf8;">🚚 Thông tin giao hàng</h4>
    <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 16px; margin-bottom: 16px;">
        <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">Trạng thái:</strong> <span style="color: #3b82f6; font-weight: bold;"><?= $statusText ?></span></p>
        <?php if (!empty($order['shipper_name'])): ?>
            <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">Người giao:</strong> <?= htmlspecialchars($order['shipper_name']) ?></p>
        <?php endif; ?>
        
        <?php if (!empty($order['shipper_phone'])): ?>
            <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">SĐT giao hàng:</strong> <?= htmlspecialchars($order['shipper_phone']) ?></p>
        <?php endif; ?>
        
        <?php if (!empty($order['tracking_code'])): ?>
            <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">Mã vận đơn:</strong> <span style="color: #fbbf24; font-weight: bold;"><?= htmlspecialchars($order['tracking_code']) ?></span></p>
        <?php endif; ?>
        
        <?php if (!empty($order['delivery_note'])): ?>
            <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">Ghi chú giao:</strong> <?= nl2br(htmlspecialchars($order['delivery_note'])) ?></p>
        <?php endif; ?>
        
        <?php if (!empty($order['shipped_at'])): ?>
            <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">Giao lúc:</strong> <?= date('H:i d/m/Y', strtotime($order['shipped_at'])) ?></p>
        <?php endif; ?>
        
        <?php if (!empty($order['delivered_at'])): ?>
            <p><strong style="color: #94a3b8; display:inline-block; min-width: 120px;">Hoàn thành lúc:</strong> <?= date('H:i d/m/Y', strtotime($order['delivered_at'])) ?></p>
        <?php endif; ?>
    </div>
<?php endif; ?>

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
