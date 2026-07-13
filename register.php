<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");

if(isset($_COOKIE['token']) && !empty($getUser) && $getUser['level'] == 'user') {
    header("Location: /goi-tho.php");
    exit;
}

$title = "Đăng Ký | Điện Máy Hiếu";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");
?>

<div style="background-color: #f8fafc; min-height: calc(100vh - 80px); display: flex; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: #fff; border: 4px solid #0f172a; border-radius: 24px; padding: 40px; box-shadow: 8px 8px 0px #0f172a; max-width: 460px; width: 100%;">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="width: 80px; height: 80px; background: #38bdf8; border-radius: 50%; border: 4px solid #0f172a; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 20px; box-shadow: 4px 4px 0px #0f172a;">
                📝
            </div>
            <h2 style="font-size: 28px; font-weight: 900; margin-bottom: 8px; color: #0f172a; text-transform: uppercase;">Đăng Ký</h2>
            <p style="color: #475569; font-weight: 600;">Tạo tài khoản để mua sắm, gọi thợ và tra cứu đơn hàng.</p>
        </div>
        
        <div id="thongbao_register" style="margin-bottom: 20px;"></div>
        
        <form id="registerForm" onsubmit="return false;">
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 8px;">Họ và Tên <span style="color: #ef4444;">*</span></label>
                <input type="text" id="reg_name" placeholder="Nguyễn Văn A" style="width: 100%; background: #f8fafc; border: 3px solid #cbd5e1; color: #0f172a; padding: 16px 20px; border-radius: 12px; font-size: 16px; font-weight: 700; outline: none; transition: border 0.2s;" onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='#cbd5e1'">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 8px;">Số Điện Thoại <span style="color: #ef4444;">*</span></label>
                <input type="tel" id="reg_phone" placeholder="09xxxx..." style="width: 100%; background: #f8fafc; border: 3px solid #cbd5e1; color: #0f172a; padding: 16px 20px; border-radius: 12px; font-size: 16px; font-weight: 700; outline: none; transition: border 0.2s;" onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='#cbd5e1'">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 8px;">Mật Khẩu <span style="color: #ef4444;">*</span></label>
                <input type="password" id="reg_password" placeholder="Ít nhất 6 ký tự" style="width: 100%; background: #f8fafc; border: 3px solid #cbd5e1; color: #0f172a; padding: 16px 20px; border-radius: 12px; font-size: 16px; font-weight: 700; outline: none; transition: border 0.2s;" onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='#cbd5e1'">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 8px;">Xác Nhận Mật Khẩu <span style="color: #ef4444;">*</span></label>
                <input type="password" id="reg_confirm" placeholder="Nhập lại mật khẩu" style="width: 100%; background: #f8fafc; border: 3px solid #cbd5e1; color: #0f172a; padding: 16px 20px; border-radius: 12px; font-size: 16px; font-weight: 700; outline: none; transition: border 0.2s;" onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='#cbd5e1'">
            </div>
            
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 8px;">Địa Chỉ</label>
                <textarea id="reg_address" placeholder="Nhập địa chỉ giao hàng (không bắt buộc)" style="width: 100%; background: #f8fafc; border: 3px solid #cbd5e1; color: #0f172a; padding: 16px 20px; border-radius: 12px; font-size: 16px; font-weight: 700; outline: none; transition: border 0.2s; resize: vertical; min-height: 80px;" onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='#cbd5e1'"></textarea>
            </div>
            
            <button type="submit" id="btnRegister" style="width: 100%; background: #0f172a; color: #fff; border: none; padding: 16px; border-radius: 12px; font-size: 18px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: transform 0.1s; box-shadow: 4px 4px 0px #38bdf8;" onmousedown="this.style.transform='translate(2px, 2px)'; this.style.boxShadow='2px 2px 0px #38bdf8';" onmouseup="this.style.transform='none'; this.style.boxShadow='4px 4px 0px #38bdf8';">
                Đăng Ký
            </button>
        </form>
        
        <div style="margin-top: 24px; text-align: center; color: #475569; font-size: 14px;">
            Đã có tài khoản? <a href="/login.php" style="color: #0f172a; font-weight: 800; text-decoration: underline;">Đăng nhập</a>
        </div>
        
        <div style="margin-top: 24px; padding-top: 20px; border-top: 2px dashed #cbd5e1; display: flex; justify-content: center;">
            <a href="/tho-login.php" style="width: 100%; text-align: center; background: #e2e8f0; color: #0f172a; text-decoration: none; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: bold; border: 2px solid #cbd5e1; transition: all 0.2s;" onmouseover="this.style.background='#cbd5e1'" onmouseout="this.style.background='#e2e8f0'"><i class="fa-solid fa-screwdriver-wrench"></i> Dành Cho Thợ / Đối Tác</a>
        </div>
    </div>
</div>

<script>
$("#btnRegister").on("click", function() {
    var btn = $(this);
    var originalText = btn.html();
    
    var name = $("#reg_name").val().trim();
    var phone = $("#reg_phone").val().trim();
    var password = $("#reg_password").val();
    var confirm = $("#reg_confirm").val();
    var address = $("#reg_address").val().trim();
    
    if (!name || !phone || !password || !confirm) {
        alert("Vui lòng điền đầy đủ thông tin bắt buộc!");
        return;
    }
    
    if (!/^[0-9]{10,11}$/.test(phone)) {
        alert("Số điện thoại không hợp lệ!");
        return;
    }
    
    if (password.length < 6) {
        alert("Mật khẩu phải có ít nhất 6 ký tự!");
        return;
    }
    
    if (password !== confirm) {
        alert("Mật khẩu xác nhận không khớp!");
        return;
    }
    
    btn.html('<i class="fa-solid fa-spinner fa-spin"></i>').prop('disabled', true);
    
    $.ajax({
        url: "/controller/client/Register.php",
        method: "POST",
        data: {
            name: name,
            phone: phone,
            password: password,
            confirm_password: confirm,
            address: address
        },
        success: function(r) {
            try {
                var res = typeof r === 'string' ? JSON.parse(r) : r;
                if (res.status === 'success') {
                    $("#thongbao_register").html('<div style="color:#10b981;font-weight:bold;">' + res.msg + '</div>');
                    setTimeout(function() {
                        window.location.href = res.redirect || '/goi-tho.php';
                    }, 1000);
                } else {
                    $("#thongbao_register").html('<div style="color:#ef4444;font-weight:bold;">' + res.msg + '</div>');
                    btn.html(originalText).prop('disabled', false);
                }
            } catch(e) {
                console.error("Lỗi parse:", e, r);
                $("#thongbao_register").html('<div style="color:#ef4444;font-weight:bold;">Lỗi hệ thống, vui lòng thử lại.</div>');
                btn.html(originalText).prop('disabled', false);
            }
        },
        error: function() {
            $("#thongbao_register").html('<div style="color:#ef4444;font-weight:bold;">Không thể kết nối máy chủ.</div>');
            btn.html(originalText).prop('disabled', false);
        }
    });
});
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
