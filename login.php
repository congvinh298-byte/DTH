<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");

if(isset($_COOKIE['token'])) {
    if($getUser['level'] == 'user') {
        if ($getUser['first_login'] == 1) {
            header("Location: /khach-update.php");
            exit;
        }
        header("Location: /goi-tho.php");
        exit;
    }
}

$title = "Khách Hàng | Điện Máy Hiếu";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");
?>

<div style="background-color: #f8fafc; min-height: calc(100vh - 80px); display: flex; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: #fff; border: 4px solid #0f172a; border-radius: 24px; padding: 40px; box-shadow: 8px 8px 0px #0f172a; max-width: 460px; width: 100%;">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="width: 80px; height: 80px; background: #fbbf24; border-radius: 50%; border: 4px solid #0f172a; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 20px; box-shadow: 4px 4px 0px #0f172a;">
                👋
            </div>
            <h2 style="font-size: 28px; font-weight: 900; margin-bottom: 8px; color: #0f172a; text-transform: uppercase;">Khách Hàng</h2>
            <p style="color: #475569; font-weight: 600;">Đăng nhập bằng số điện thoại để gọi thợ hoặc tra cứu điểm tích lũy.</p>
        </div>
        
        <div id="thongbao_login" style="margin-bottom: 20px;"></div>
        
        <form id="khachLoginForm" onsubmit="return false;">
            <div style="margin-bottom: 20px;" id="phoneStep">
                <label style="display: block; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 8px;">Số Điện Thoại</label>
                <input type="tel" id="kl_phone" placeholder="09xxxx..." style="width: 100%; background: #f8fafc; border: 3px solid #cbd5e1; color: #0f172a; padding: 16px 20px; border-radius: 12px; font-size: 18px; font-weight: 800; outline: none; transition: border 0.2s;" onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='#cbd5e1'">
            </div>
            
            <div style="margin-bottom: 24px; display: none;" id="passwordStep">
                <label style="display: block; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 8px;">Mật Khẩu</label>
                <input type="password" id="kl_password" placeholder="Nhập mật khẩu..." style="width: 100%; background: #f8fafc; border: 3px solid #cbd5e1; color: #0f172a; padding: 16px 20px; border-radius: 12px; font-size: 18px; font-weight: 800; outline: none; transition: border 0.2s;" onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='#cbd5e1'">
            </div>
            
            <div id="otpDisplay" style="display: none; background: #dcfce7; border: 2px dashed #22c55e; padding: 16px; border-radius: 12px; margin-bottom: 24px; text-align: center;">
                <p style="font-size: 14px; color: #166534; font-weight: 700; margin-bottom: 8px;">Mật khẩu dùng 1 lần (OTP) của bạn là:</p>
                <div id="otpValue" style="font-size: 32px; font-weight: 900; color: #15803d; letter-spacing: 4px;"></div>
                <p style="font-size: 12px; color: #166534; margin-top: 8px; font-weight: 600;">(Vui lòng nhập mật khẩu này vào ô bên dưới)</p>
            </div>
            
            <button type="submit" id="btnContinue" style="width: 100%; background: #0f172a; color: #fff; border: none; padding: 16px; border-radius: 12px; font-size: 18px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: transform 0.1s; box-shadow: 4px 4px 0px #38bdf8;" onmousedown="this.style.transform='translate(2px, 2px)'; this.style.boxShadow='2px 2px 0px #38bdf8';" onmouseup="this.style.transform='none'; this.style.boxShadow='4px 4px 0px #38bdf8';">
                Tiếp Tục
            </button>
        </form>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px dashed #cbd5e1; display: flex; flex-direction: column; gap: 12px; justify-content: center;">
            <a href="/register.php" style="width: 100%; text-align: center; background: #38bdf8; color: #0f172a; text-decoration: none; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: bold; border: 2px solid #0f172a; transition: all 0.2s; box-shadow: 2px 2px 0px #0f172a;" onmouseover="this.style.background='#0ea5e9'; this.style.color='#fff';" onmouseout="this.style.background='#38bdf8'; this.style.color='#0f172a';"><i class="fa-solid fa-user-plus"></i> Tạo tài khoản mới</a>
            <a href="/tho-login.php" style="width: 100%; text-align: center; background: #e2e8f0; color: #0f172a; text-decoration: none; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: bold; border: 2px solid #cbd5e1; transition: all 0.2s;" onmouseover="this.style.background='#cbd5e1'" onmouseout="this.style.background='#e2e8f0'"><i class="fa-solid fa-screwdriver-wrench"></i> Dành Cho Thợ / Đối Tác</a>
        </div>
    </div>
</div>

<script>
let step = 1;

$("#btnContinue").on("click", function() {
    var phone = $("#kl_phone").val().trim();
    
    if (!phone) {
        alert("Vui lòng nhập số điện thoại!");
        return;
    }
    
    var originalText = $(this).html();
    $(this).html('<i class="fa-solid fa-spinner fa-spin"></i>').prop('disabled', true);
    
    if (step === 1) {
        // Check phone
        $.ajax({
            url: "/controller/client/KhachLogin.php",
            method: "POST",
            data: { action: 'check_phone', phone: phone },
            success: function(r) {
                let res = typeof r === 'string' ? JSON.parse(r) : r;
                $("#btnContinue").html('Đăng Nhập').prop('disabled', false);
                
                if (res.status == 'exists') {
                    // Show password field
                    $("#kl_phone").prop('readonly', true).css('opacity', '0.7');
                    $("#passwordStep").slideDown();
                    step = 2;
                } else if (res.status == 'new') {
                    // Show OTP
                    $("#kl_phone").prop('readonly', true).css('opacity', '0.7');
                    $("#passwordStep").slideDown();
                    $("#otpDisplay").slideDown();
                    $("#otpValue").text(res.otp);
                    step = 2;
                } else {
                    $("#thongbao_login").html('<div style="color:red;font-weight:bold;">' + res.msg + '</div>');
                    $("#btnContinue").html(originalText);
                }
            }
        });
    } else if (step === 2) {
        // Login
        var pass = $("#kl_password").val().trim();
        if (!pass) {
            alert("Vui lòng nhập mật khẩu!");
            $("#btnContinue").html('Đăng Nhập').prop('disabled', false);
            return;
        }
        
        $.ajax({
            url: "/controller/client/KhachLogin.php",
            method: "POST",
            data: { action: 'login', phone: phone, password: pass },
            success: function(r) {
                $("#thongbao_login").html(r);
                $("#btnContinue").html('Đăng Nhập').prop('disabled', false);
            }
        });
    }
});
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
