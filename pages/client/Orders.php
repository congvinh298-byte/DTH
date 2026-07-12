<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Theo dõi Đơn Hàng";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
CheckLogin();

$user_id = $getUser['id'];
$orders = $DMH->get_list("SELECT * FROM `store_orders` WHERE `user_id` = '$user_id' ORDER BY id DESC");

function getStatusBadge($status) {
    if($status == 'pending') return '<span style="background: #f59e0b; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Chờ xử lý</span>';
    if($status == 'shipping') return '<span style="background: #3b82f6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Đang giao hàng</span>';
    if($status == 'completed') return '<span style="background: #10b981; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Thành công</span>';
    return '<span style="background: #64748b; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Đã hủy</span>';
}
?>

<div style="background: #0f172a; min-height: calc(100vh - 80px); color: #fff; padding: 40px 20px;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <h2 style="font-size: 28px; font-weight: 900; margin-bottom: 24px;"><i class="fa-solid fa-box"></i> ĐƠN HÀNG CỦA TÔI</h2>

        <?php if(empty($orders)): ?>
            <div style="text-align: center; padding: 40px; background: rgba(30,41,59,0.8); border-radius: 16px;">
                <i class="fa-solid fa-box-open" style="font-size: 48px; color: #64748b; margin-bottom: 16px;"></i>
                <p style="font-size: 18px; color: #94a3b8;">Bạn chưa có đơn hàng nào!</p>
                <a href="/" class="btn accent" style="margin-top: 20px; display: inline-block; padding: 10px 24px;">Mua sắm ngay</a>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <?php foreach($orders as $order): ?>
                <div style="background: rgba(30,41,59,0.8); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; margin-bottom: 15px;">
                        <div>
                            <div style="font-size: 18px; font-weight: bold;">Mã ĐH: #<?= $order['id'] ?></div>
                        </div>
                        <div>
                            <?= getStatusBadge($order['status']) ?>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 15px; font-size: 14px; color: #cbd5e1; line-height: 1.6;">
                        <div><i class="fa-solid fa-user" style="width: 20px;"></i> <?= htmlspecialchars($order['customer_name']) ?> - <?= htmlspecialchars($order['phone']) ?></div>
                        <div><i class="fa-solid fa-location-dot" style="width: 20px;"></i> <?= nl2br(htmlspecialchars($order['address'])) ?></div>
                        <div><i class="fa-solid fa-credit-card" style="width: 20px;"></i> Thanh toán: <?= $order['payment_method'] == 'COD' ? 'Khi nhận hàng' : 'Chuyển khoản' ?> <?= $order['vat_requested'] ? '<span style="color: #38bdf8; font-weight: bold;">(Có VAT)</span>' : '' ?></div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.3); padding: 15px; border-radius: 12px;">
                        <div style="font-size: 16px;">
                            Tổng tiền: <strong style="color: #f43f5e; font-size: 20px;"><?= number_format($order['total_amount']) ?>đ</strong>
                        </div>
                        <div>
                            <?php if($order['status'] == 'shipping'): ?>
                                <button onclick="confirmReceived(<?= $order['id'] ?>)" class="btn accent" style="padding: 10px 20px;"><i class="fa-solid fa-check"></i> Đã nhận hàng</button>
                            <?php elseif($order['status'] == 'pending'): ?>
                                <button onclick="alert('Đơn hàng đang chờ shop xác nhận và chuẩn bị gửi đi.')" class="btn outline" style="padding: 10px 20px;">Đang chuẩn bị</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmReceived(id) {
    Swal.fire({
        title: 'Xác nhận',
        text: "Bạn xác nhận đã nhận được hàng và thanh toán đầy đủ?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Đã nhận',
        cancelButtonText: 'Chưa nhận'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/controller/client/OrderAction.php',
                method: 'POST',
                data: { action: 'confirm_received', id: id },
                success: function(r) {
                    try {
                        let res = JSON.parse(r);
                        if(res.status == 'success') {
                            Swal.fire('Thành công', 'Cảm ơn bạn đã mua hàng!', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Lỗi', res.msg, 'error');
                        }
                    } catch(e) {
                        location.reload();
                    }
                }
            });
        }
    })
}
</script>

<?php require_once("../../pages/client/Footer.php"); ?>
