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

<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 16px;">
    <p style="margin-bottom: 10px;"><strong style="color: #475569; display:inline-block; min-width: 130px;">Khách hàng:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
    <p style="margin-bottom: 10px;"><strong style="color: #475569; display:inline-block; min-width: 130px;">Số điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
    <p style="margin-bottom: 10px;"><strong style="color: #475569; display:inline-block; min-width: 130px;">Địa chỉ:</strong> <?= nl2br(htmlspecialchars($order['address'])) ?></p>
    <p style="margin-bottom: 10px;"><strong style="color: #475569; display:inline-block; min-width: 130px;">Thanh toán:</strong> <?= $order['payment_method'] == 'COD' ? 'Khi nhận hàng' : 'Chuyển khoản' ?></p>
    <p style="margin-bottom: 10px;"><strong style="color: #475569; display:inline-block; min-width: 130px;">Tổng tiền:</strong> <span style="color: #dc2626; font-weight: bold;"><?= number_format($order['total_amount']) ?>đ</span></p>
    <p style="margin-bottom: 10px;"><strong style="color: #475569; display:inline-block; min-width: 130px;">VAT:</strong> <?= $order['vat_requested'] ? 'Có xuất hóa đơn VAT' : 'Không' ?></p>
    <p style="margin-bottom: 10px;"><strong style="color: #475569; display:inline-block; min-width: 130px;">Ghi chú:</strong> <?= nl2br(htmlspecialchars($order['note'] ?: 'Không có')) ?></p>
    <p style="margin-bottom: 0;"><strong style="color: #475569; display:inline-block; min-width: 130px;">Thời gian đặt:</strong> <?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></p>
</div>

<?php if (!empty($order['shipper_name']) || $order['status'] == 'shipping' || $order['status'] == 'completed'): ?>
    <h4 style="margin: 20px 0 12px; color: #0ea5e9; font-size: 16px; font-weight: 800;">🚚 Thông tin giao hàng</h4>
    
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 20px; margin-bottom: 16px;">
        <p style="margin-bottom: 10px;"><strong style="color: #475569; display:inline-block; min-width: 130px;">Trạng thái:</strong> <span style="color: #2563eb; font-weight: bold;"><?= $statusText ?></span></p>
        
        <?php if (!empty($order['shipper_name'])): ?>
            <p style="margin-bottom: 10px;"><strong style="color: #475569; display:inline-block; min-width: 130px;">Người giao:</strong> <?= htmlspecialchars($order['shipper_name']) ?></p>
        <?php endif; ?>
        
        <?php if (!empty($order['shipper_phone'])): ?>
            <p style="margin-bottom: 10px;"><strong style="color: #475569; display:inline-block; min-width: 130px;">SĐT giao hàng:</strong> <?= htmlspecialchars($order['shipper_phone']) ?></p>
        <?php endif; ?>
        
        <?php if (!empty($order['tracking_code'])): ?>
            <p style="margin-bottom: 10px;"><strong style="color: #475569; display:inline-block; min-width: 130px;">Mã vận đơn:</strong> <span style="color: #d97706; font-weight: bold;"><?= htmlspecialchars($order['tracking_code']) ?></span></p>
        <?php endif; ?>
        
        <?php if (!empty($order['delivery_note'])): ?>
            <p style="margin-bottom: 10px;"><strong style="color: #475569; display:inline-block; min-width: 130px;">Ghi chú giao:</strong> <?= nl2br(htmlspecialchars($order['delivery_note'])) ?></p>
        <?php endif; ?>
        
        <?php if (!empty($order['shipped_at'])): ?>
            <p style="margin-bottom: 10px;"><strong style="color: #475569; display:inline-block; min-width: 130px;">Giao lúc:</strong> <?= date('H:i d/m/Y', strtotime($order['shipped_at'])) ?></p>
        <?php endif; ?>
        
        <?php if (!empty($order['delivered_at'])): ?>
            <p style="margin-bottom: 0;"><strong style="color: #475569; display:inline-block; min-width: 130px;">Hoàn thành lúc:</strong> <?= date('H:i d/m/Y', strtotime($order['delivered_at'])) ?></p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (!empty($items)): ?>
    <h4 style="margin: 20px 0 12px; color: #0ea5e9; font-size: 16px; font-weight: 800;">🛒 Sản phẩm trong đơn</h4>
    
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
        <?php foreach ($items as $item): ?>
            <div style="border-bottom: 1px solid #e2e8f0; padding: 10px 0; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="color: #0f172a;"><?= htmlspecialchars($item['product_name'] ?? 'Sản phẩm') ?></strong>
                    <span style="color: #64748b; margin-left: 8px;">x<?= (int)($item['quantity'] ?? 1) ?></span>
                </div>
                <span style="color: #dc2626; font-weight: bold;"><?= number_format($item['price'] ?? 0) ?>đ</span>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
