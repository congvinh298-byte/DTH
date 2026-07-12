<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

$title = "Giỏ Hàng của bạn";
require_once(__DIR__."/Head.php");
require_once(__DIR__."/Header.php");
CheckLogin();

$user_id = $getUser['id'];
$items = $DMH->get_list("SELECT c.*, p.name, p.price, p.image FROM `store_carts` c JOIN `store_products` p ON c.product_id = p.id WHERE c.user_id = '$user_id'");

$sotien = 0;
?>

<div class="wrap fade-in" style="margin-top: 40px; min-height: 60vh;">
    <h2 style="font-size: 32px; font-weight: 900; margin-bottom: 24px; color: var(--brand-accent);">Giỏ Hàng Của Bạn</h2>

    <?php if(empty($items)): ?>
        <div style="text-align: center; padding: 60px 20px; background: rgba(255,255,255,0.05); border-radius: 20px;">
            <i class="fa-solid fa-cart-arrow-down" style="font-size: 60px; color: #64748b; margin-bottom: 20px;"></i>
            <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 10px;">Giỏ hàng trống</h3>
            <p style="color: #94a3b8; margin-bottom: 20px;">Bạn chưa chọn mua sản phẩm nào.</p>
            <a href="/dien-may" class="btn accent">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Cart Items -->
            <div>
                <?php foreach($items as $item): 
                    $sotien += $item['price'] * $item['quantity'];
                ?>
                <div style="display: flex; gap: 20px; background: rgba(30,41,59,0.8); padding: 16px; border-radius: 16px; margin-bottom: 16px; align-items: center;">
                    <div style="width: 100px; height: 100px; background: #fff; border-radius: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <?php if($item['image']): ?>
                            <img src="<?= htmlspecialchars($item['image']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        <?php else: ?>
                            📦
                        <?php endif; ?>
                    </div>
                    <div style="flex-grow: 1;">
                        <h4 style="font-size: 18px; font-weight: bold; margin-bottom: 8px;"><?= htmlspecialchars($item['name']) ?></h4>
                        <div style="color: #f43f5e; font-weight: bold; font-size: 16px; margin-bottom: 8px;"><?= number_format($item['price']) ?>đ</div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="color: #94a3b8; font-size: 14px;">Số lượng: <?= $item['quantity'] ?></span>
                        </div>
                    </div>
                    <div>
                        <button onclick="removeItem(<?= $item['id'] ?>)" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; padding: 10px; border-radius: 8px; cursor: pointer;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Checkout Form -->
            <div style="background: rgba(15,23,42,0.9); border: 1px solid rgba(255,255,255,0.1); padding: 24px; border-radius: 20px; align-self: start; position: sticky; top: 100px;">
                <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);">Thông tin giao hàng</h3>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 18px;">
                    <span>Tổng thanh toán:</span>
                    <span style="color: #f43f5e; font-weight: 900; font-size: 24px;"><?= number_format($sotien) ?>đ</span>
                </div>

                <form id="checkoutForm">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 600;">Họ và Tên</label>
                        <input type="text" id="c_name" value="<?= htmlspecialchars($getUser['name'] ?? $getUser['username'] ?? '') ?>" style="width: 100%; padding: 12px; border-radius: 8px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: white;" required>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 600;">Số điện thoại</label>
                        <input type="text" id="c_phone" value="<?= htmlspecialchars($getUser['phone'] ?? '') ?>" style="width: 100%; padding: 12px; border-radius: 8px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: white;" required>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 600;">Địa chỉ nhận hàng</label>
                        <div style="display: flex; gap: 10px;">
                            <textarea id="c_address" rows="3" style="flex-grow: 1; padding: 12px; border-radius: 8px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: white;" required><?= htmlspecialchars($getUser['address'] ?? '') ?></textarea>
                            <button type="button" id="btnGPS" onclick="getGPSLocation()" style="background: #10b981; color: white; border: none; border-radius: 8px; padding: 0 15px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" title="Lấy định vị tự động">
                                <i class="fa-solid fa-location-crosshairs" style="font-size: 20px;"></i>
                            </button>
                        </div>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 600;">Phương thức thanh toán</label>
                        <select id="c_payment" style="width: 100%; padding: 12px; border-radius: 8px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: white;">
                            <option value="COD">Thanh toán khi nhận hàng (COD)</option>
                            <option value="BANK">Chuyển khoản ngân hàng</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 16px; display: flex; align-items: center; gap: 10px; background: rgba(56, 189, 248, 0.1); padding: 12px; border-radius: 8px; border: 1px dashed rgba(56, 189, 248, 0.3);">
                        <input type="checkbox" id="c_vat" style="width: 20px; height: 20px; accent-color: #38bdf8; cursor: pointer;">
                        <label for="c_vat" style="cursor: pointer; font-weight: bold; color: #38bdf8; user-select: none;">Yêu cầu xuất hóa đơn VAT</label>
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 600;">Ghi chú (Tên Cty, MST nếu có)</label>
                        <textarea id="c_note" rows="2" style="width: 100%; padding: 12px; border-radius: 8px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: white;"></textarea>
                    </div>

                    <button type="button" id="btnCheckout" class="btn accent" style="width: 100%; padding: 16px; font-size: 16px; font-weight: bold;">ĐẶT HÀNG NGAY</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function removeItem(id) {
    if(confirm('Bạn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
        $.ajax({
            url: '/controller/client/CartAction.php',
            method: 'POST',
            data: { action: 'remove_item', id: id },
            success: function(r) {
                location.reload();
            }
        });
    }
}

$('#btnCheckout').click(function() {
    let name = $('#c_name').val().trim();
    let phone = $('#c_phone').val().trim();
    let address = $('#c_address').val().trim();
    let note = $('#c_note').val().trim();
    let payment = $('#c_payment').val();
    let vat = $('#c_vat').is(':checked') ? 1 : 0;

    if(!name || !phone || !address) {
        Swal.fire('Lỗi', 'Vui lòng nhập đủ thông tin (Tên, SĐT, Địa chỉ)', 'error');
        return;
    }

    $(this).html('<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled', true);

    $.ajax({
        url: '/controller/client/CartAction.php',
        method: 'POST',
        data: {
            action: 'checkout',
            name: name,
            phone: phone,
            address: address,
            note: note,
            payment_method: payment,
            vat_requested: vat
        },
        success: function(r) {
            try {
                let res = JSON.parse(r);
                if(res.status == 'success') {
                    Swal.fire('Thành công', 'Đơn hàng của bạn đã được ghi nhận!', 'success').then(() => {
                        window.location.href = '/';
                    });
                } else {
                    Swal.fire('Lỗi', res.msg, 'error');
                    $('#btnCheckout').html('ĐẶT HÀNG NGAY').prop('disabled', false);
                }
            } catch(e) {
                location.reload();
            }
        },
        error: function() {
            Swal.fire('Lỗi', 'Có lỗi xảy ra, vui lòng thử lại', 'error');
            $('#btnCheckout').html('ĐẶT HÀNG NGAY').prop('disabled', false);
        }
    });
});

function getGPSLocation() {
    if (navigator.geolocation) {
        let btn = $('#btnGPS');
        let oldHtml = btn.html();
        btn.html('<i class="fa-solid fa-spinner fa-spin"></i>').prop('disabled', true);
        
        navigator.geolocation.getCurrentPosition(function(position) {
            let lat = position.coords.latitude;
            let lng = position.coords.longitude;
            let currentAddr = $('#c_address').val().trim();
            
            // Dùng Nominatim API để lấy địa chỉ từ tọa độ
            $.ajax({
                url: `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`,
                method: 'GET',
                success: function(data) {
                    let addr = data.display_name;
                    let mapLink = `\n[Tọa độ GPS: https://maps.google.com/?q=${lat},${lng}]`;
                    if (currentAddr && !currentAddr.includes('Tọa độ GPS')) {
                        $('#c_address').val(currentAddr + '\n' + addr + mapLink);
                    } else {
                        $('#c_address').val(addr + mapLink);
                    }
                    btn.html(oldHtml).prop('disabled', false);
                    Swal.fire('Thành công', 'Đã lấy được vị trí của bạn!', 'success');
                },
                error: function() {
                    let mapLink = `[Tọa độ GPS: https://maps.google.com/?q=${lat},${lng}]`;
                    $('#c_address').val((currentAddr ? currentAddr + '\n' : '') + mapLink);
                    btn.html(oldHtml).prop('disabled', false);
                }
            });
        }, function(error) {
            let btn = $('#btnGPS');
            btn.html('<i class="fa-solid fa-location-crosshairs"></i>').prop('disabled', false);
            Swal.fire('Lỗi', 'Không thể lấy vị trí. Vui lòng cho phép quyền truy cập vị trí trên trình duyệt!', 'error');
        }, { timeout: 10000 });
    } else {
        Swal.fire('Lỗi', 'Trình duyệt của bạn không hỗ trợ định vị GPS.', 'error');
    }
}
</script>

<style>
@media (max-width: 768px) {
    .wrap > div {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once(__DIR__."/Footer.php"); ?>