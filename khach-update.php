<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");

if(!isset($_COOKIE['token'])) {
    header("Location: /khach-login.php");
    exit;
}

if($getUser['level'] != 'user') {
    header("Location: /");
    exit;
}

if ($getUser['first_login'] != 1) {
    header("Location: /goi-tho.php");
    exit;
}

$title = "Cập nhật Thông tin | Điện Máy Hiếu";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");
?>

<div style="background-color: #f8fafc; min-height: calc(100vh - 80px); display: flex; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: #fff; border: 4px solid #0f172a; border-radius: 24px; padding: 40px; box-shadow: 8px 8px 0px #0f172a; max-width: 500px; width: 100%;">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="width: 80px; height: 80px; background: #38bdf8; border-radius: 50%; border: 4px solid #0f172a; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 20px; box-shadow: 4px 4px 0px #0f172a;">
                📝
            </div>
            <h2 style="font-size: 28px; font-weight: 900; margin-bottom: 8px; color: #0f172a; text-transform: uppercase;">Chào Khách Hàng Mới</h2>
            <p style="color: #475569; font-weight: 600;">Vui lòng thiết lập mật khẩu và thông tin cá nhân để hoàn tất.</p>
        </div>
        
        <div id="thongbao_update" style="margin-bottom: 20px;"></div>
        
        <form id="khachUpdateForm" onsubmit="return false;">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 8px;">Mật Khẩu Mới Của Bạn *</label>
                <input type="password" id="up_password" placeholder="Tạo mật khẩu dễ nhớ..." required style="width: 100%; background: #f8fafc; border: 3px solid #cbd5e1; color: #0f172a; padding: 16px 20px; border-radius: 12px; font-size: 16px; font-weight: 700; outline: none; transition: border 0.2s;" onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='#cbd5e1'">
                <small style="color: #64748b; font-weight: 600; display: block; margin-top: 6px;">(Thay thế cho mã OTP ngẫu nhiên)</small>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 8px;">Họ và Tên *</label>
                <input type="text" id="up_name" placeholder="Tên của bạn..." required style="width: 100%; background: #f8fafc; border: 3px solid #cbd5e1; color: #0f172a; padding: 16px 20px; border-radius: 12px; font-size: 16px; font-weight: 700; outline: none; transition: border 0.2s;" onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='#cbd5e1'">
            </div>
            
            <div style="margin-bottom: 30px;">
                <label style="display: block; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 8px;">Địa chỉ nhà *</label>
                <textarea id="up_address" placeholder="Số nhà, đường, xã/phường..." required rows="3" style="width: 100%; background: #f8fafc; border: 3px solid #cbd5e1; color: #0f172a; padding: 16px 20px; border-radius: 12px; font-size: 16px; font-weight: 700; outline: none; transition: border 0.2s; resize: none;" onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='#cbd5e1'"></textarea>
            </div>
            
            <button type="submit" id="btnUpdate" style="width: 100%; background: #10b981; color: #fff; border: 4px solid #0f172a; padding: 16px; border-radius: 16px; font-size: 18px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: transform 0.1s; box-shadow: 4px 4px 0px #0f172a;" onmousedown="this.style.transform='translate(2px, 2px)'; this.style.boxShadow='2px 2px 0px #0f172a';" onmouseup="this.style.transform='none'; this.style.boxShadow='4px 4px 0px #0f172a';">
                Cập Nhật & Vào Trang
            </button>
        </form>
    </div>
</div>

<script>
$("#khachUpdateForm").on("submit", function() {
    var pass = $("#up_password").val().trim();
    var name = $("#up_name").val().trim();
    var address = $("#up_address").val().trim();
    
    if (!pass || !name || !address) {
        alert("Vui lòng điền đủ thông tin!");
        return;
    }
    
    var originalText = $("#btnUpdate").html();
    $("#btnUpdate").html('<i class="fa-solid fa-spinner fa-spin"></i>').prop('disabled', true);
    
    $.ajax({
        url: "/controller/client/KhachLogin.php",
        method: "POST",
        data: { action: 'update_profile', password: pass, name: name, address: address },
        success: function(r) {
            $("#thongbao_update").html(r);
            $("#btnUpdate").html(originalText).prop('disabled', false);
        }
    });
});
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
