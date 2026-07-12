<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");

$title = "Giỏ Hàng | Điện Máy Hiếu";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");

// Kiểm tra đăng nhập
if(!isset($_COOKIE['token'])) {
    echo "<div style='text-align:center; padding: 100px 20px;'>
        <h2 style='color: var(--c-rose);'><i class='fa-solid fa-lock'></i> Vui lòng đăng nhập</h2>
        <p>Bạn cần đăng nhập để xem giỏ hàng và tiến hành đặt hàng.</p>
        <a href='/login.php' class='btn accent'>Đến trang Đăng Nhập</a>
    </div>";
    require_once(__DIR__."/pages/client/Footer.php");
    exit;
}
?>

<main style="padding: 40px 0;">
    <div class="wrap" style="max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        
        <div>
            <h2 style="margin-top: 0; color: var(--c-cyan);"><i class="fa-solid fa-cart-shopping"></i> Giỏ Hàng Của Bạn</h2>
            <div id="cartItemsContainer" style="background: rgba(15, 23, 42, 0.8); border-radius: 16px; padding: 20px; border: 1px solid rgba(255,255,255,0.1);">
                <p style="text-align:center; color: var(--admin-muted);">Đang tải giỏ hàng...</p>
            </div>
        </div>

        <div>
            <div style="background: rgba(15, 23, 42, 0.8); border-radius: 16px; padding: 24px; border: 1px solid rgba(255,255,255,0.1); position: sticky; top: 100px;">
                <h3 style="margin-top: 0; color: var(--c-yellow); border-bottom: 2px dashed rgba(255,255,255,0.1); padding-bottom: 15px;"><i class="fa-solid fa-receipt"></i> Thanh Toán</h3>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 18px; font-weight: bold;">
                    <span>Tổng tiền:</span>
                    <span id="cartTotalPrice" style="color: var(--c-green);">0đ</span>
                </div>

                <form id="checkoutForm" onsubmit="processCheckout(event)">
                    <div class="field" style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom: 5px; color: #cbd5e1; font-size: 14px;">Họ tên người nhận <span style="color:var(--c-rose);">*</span></label>
                        <input type="text" id="co_name" required placeholder="Nhập họ tên" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px;">
                    </div>
                    <div class="field" style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom: 5px; color: #cbd5e1; font-size: 14px;">Số điện thoại <span style="color:var(--c-rose);">*</span></label>
                        <input type="tel" id="co_phone" required placeholder="VD: 09..." style="width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px;">
                    </div>
                    <div class="field" style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom: 5px; color: #cbd5e1; font-size: 14px;">Địa chỉ giao hàng <span style="color:var(--c-rose);">*</span></label>
                        <textarea id="co_address" required placeholder="Số nhà, tên đường, xã, huyện..." rows="3" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px;"></textarea>
                    </div>
                    <div class="field" style="margin-bottom: 25px;">
                        <label style="display:block; margin-bottom: 5px; color: #cbd5e1; font-size: 14px;">Ghi chú thêm</label>
                        <input type="text" id="co_note" placeholder="Giao giờ hành chính..." style="width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px;">
                    </div>
                    
                    <button type="submit" class="btn accent" style="width: 100%; padding: 16px; font-size: 18px; font-weight: 900;" id="btnCheckout"><i class="fa-solid fa-paper-plane"></i> XÁC NHẬN ĐẶT HÀNG</button>
                </form>
            </div>
        </div>

    </div>
</main>

<script>
function loadCart() {
    $('#cartItemsContainer').html('<p style="text-align:center; color: var(--admin-muted);"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải dữ liệu...</p>');
    
    $.ajax({
        url: "/controller/client/CartAction.php",
        method: "POST",
        data: { action: 'get_cart' },
        success: function(r) {
            try {
                let res = typeof r === 'string' ? JSON.parse(r) : r;
                if(res.status == 'success') {
                    let html = '';
                    let total = 0;
                    
                    if(res.data.length === 0) {
                        html = '<div style="text-align:center; padding: 40px 0;"><i class="fa-solid fa-box-open" style="font-size: 48px; color: #334155; margin-bottom: 15px;"></i><p>Giỏ hàng trống</p><a href="/dien-may" class="btn outline">Tiếp tục mua sắm</a></div>';
                        $('#btnCheckout').prop('disabled', true).css('opacity', '0.5');
                    } else {
                        $('#btnCheckout').prop('disabled', false).css('opacity', '1');
                        res.data.forEach(item => {
                            let priceFormat = new Intl.NumberFormat('vi-VN').format(item.price);
                            let subTotal = item.price * item.quantity;
                            total += subTotal;
                            
                            html += `
                            <div style="display: flex; gap: 15px; padding: 15px 0; border-bottom: 1px dashed rgba(255,255,255,0.1); align-items: center;">
                                <img src="${item.image || '/public/assets/logo.png'}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                <div style="flex-grow: 1;">
                                    <h4 style="margin: 0 0 5px;">${item.name}</h4>
                                    <div style="color: var(--c-yellow); font-weight: bold; margin-bottom: 5px;">${priceFormat}đ</div>
                                    <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                                        <span style="font-size: 13px; color: #94a3b8;">Số lượng:</span>
                                        <div style="display: flex; align-items: center; background: rgba(0,0,0,0.3); border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); overflow: hidden;">
                                            <button onclick="updateQty(${item.id}, ${item.quantity - 1})" style="background: transparent; color: white; border: none; padding: 4px 10px; cursor: pointer;">-</button>
                                            <span style="padding: 4px 10px; font-weight: bold; border-left: 1px solid rgba(255,255,255,0.1); border-right: 1px solid rgba(255,255,255,0.1); font-size: 13px;">${item.quantity}</span>
                                            <button onclick="updateQty(${item.id}, ${item.quantity + 1})" style="background: transparent; color: white; border: none; padding: 4px 10px; cursor: pointer;">+</button>
                                        </div>
                                    </div>
                                </div>
                                <div style="font-weight: 900; color: var(--c-cyan);">
                                    ${new Intl.NumberFormat('vi-VN').format(subTotal)}đ
                                </div>
                                <button onclick="removeItem(${item.id})" style="background: rgba(244, 63, 94, 0.1); border: 1px solid var(--c-rose); color: var(--c-rose); width: 32px; height: 32px; border-radius: 6px; cursor: pointer;"><i class="fa-solid fa-trash"></i></button>
                            </div>`;
                        });
                    }
                    
                    $('#cartItemsContainer').html(html);
                    $('#cartTotalPrice').text(new Intl.NumberFormat('vi-VN').format(total) + 'đ');
                }
            } catch(e) {}
        }
    });
}

function updateQty(id, qty) {
    if (qty < 1) {
        removeItem(id);
        return;
    }
    $.ajax({
        url: '/controller/client/CartAction.php',
        method: 'POST',
        data: { action: 'update_qty', id: id, qty: qty },
        success: function(r) {
            loadCart();
            if(typeof updateCartCount === "function") updateCartCount();
        }
    });
}

function removeItem(id) {
    if(confirm("Xóa sản phẩm này khỏi giỏ hàng?")) {
        $.ajax({
            url: "/controller/client/CartAction.php",
            method: "POST",
            data: { action: 'remove_item', id: id },
            success: function(r) {
                loadCart();
                if(typeof updateCartCount === "function") updateCartCount();
            }
        });
    }
}

function processCheckout(e) {
    e.preventDefault();
    
    let btn = $('#btnCheckout');
    let originalText = btn.html();
    btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled', true);
    
    let data = {
        action: 'checkout',
        name: $('#co_name').val(),
        phone: $('#co_phone').val(),
        address: $('#co_address').val(),
        note: $('#co_note').val()
    };
    
    $.ajax({
        url: "/controller/client/CartAction.php",
        method: "POST",
        data: data,
        success: function(r) {
            try {
                let res = typeof r === 'string' ? JSON.parse(r) : r;
                if(res.status == 'success') {
                    Swal.fire({
                        title: 'Đặt hàng thành công!',
                        text: 'Cửa hàng sẽ liên hệ với bạn trong thời gian sớm nhất.',
                        icon: 'success',
                        confirmButtonText: 'Tuyệt vời'
                    }).then(() => {
                        window.location.href = '/';
                    });
                } else {
                    Swal.fire('Lỗi', res.msg, 'error');
                    btn.html(originalText).prop('disabled', false);
                }
            } catch(e) {
                btn.html(originalText).prop('disabled', false);
            }
        }
    });
}

$(document).ready(function() {
    loadCart();
});
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
