<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");

if(isset($_COOKIE['token'])) {
    if($getUser['level'] == 'tho' || $getUser['level'] == 'admin') {
        header("Location: /tho-dashboard.php");
        exit;
    }
}

$title = "Đăng Nhập Thợ | Điện Máy Hiếu";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");
?>

<main>
    <div class="wrap fade-in" style="display: flex; justify-content: center; align-items: center; min-height: 75vh;">
        <section class="panel" style="width: 100%; max-width: 460px; background: var(--dark); border: 1px solid rgba(255,255,255,0.1); padding: 48px 40px; border-radius: var(--radius-xl); box-shadow: var(--shadow-xl); position: relative; overflow: hidden;">
            <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: var(--brand-accent); filter: blur(80px); opacity: 0.3;"></div>
            
            <div style="text-align: center; margin-bottom: 32px; position: relative; z-index: 2;">
                <div style="width: 80px; height: 80px; background: rgba(56, 189, 248, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; border: 1px solid rgba(56, 189, 248, 0.2);">
                    <i class="fa-solid fa-user-shield" style="font-size: 32px; color: var(--brand-accent);"></i>
                </div>
                <h2 style="color: white; margin: 0; font-size: 28px; font-weight: 900; letter-spacing: -0.5px;">Đăng Nhập</h2>
                <p style="color: #94a3b8; margin-top: 8px; font-size: 15px;">Cổng thông tin nội bộ dành cho Thợ</p>
            </div>
            
            <div id="thongbao_login" style="position: relative; z-index: 2;"></div>
            
            <form id="loginForm" class="form" onsubmit="return false;" style="position: relative; z-index: 2; grid-template-columns: 1fr; gap: 20px;">
                <div class="field full">
                    <label style="color: #cbd5e1; margin-bottom: 4px;">Tài khoản</label>
                    <input type="text" id="username" placeholder="Nhập tên đăng nhập..." required style="background: rgba(15,23,42,0.8); color: white; border: 1px solid rgba(255,255,255,0.15); padding: 16px 20px;">
                </div>
                
                <div class="field full">
                    <label style="color: #cbd5e1; margin-bottom: 4px;">Mật khẩu</label>
                    <input type="password" id="password" placeholder="Nhập mật khẩu..." required style="background: rgba(15,23,42,0.8); color: white; border: 1px solid rgba(255,255,255,0.15); padding: 16px 20px;">
                </div>
                
                <button type="submit" id="btnLogin" class="btn accent" style="width: 100%; margin-top: 12px; padding: 16px; font-size: 16px; font-weight: 800; display: flex; justify-content: center;"><i class="fa-solid fa-right-to-bracket" style="margin-right: 8px;"></i> VÀO HỆ THỐNG</button>
            </form>
        </section>
    </div>
</main>

<script>
$("#loginForm").submit(function(e) {
    e.preventDefault();
    var user = $("#username").val().trim();
    var pass = $("#password").val().trim();
    
    if(!user || !pass) return;
    
    let originalText = $("#btnLogin").html();
    $("#btnLogin").html('<i class="fa-solid fa-circle-notch fa-spin"></i> Đang xác thực...').prop('disabled', true);
    
    $.ajax({
        url: "/controller/client/Login.php",
        method: "POST",
        data: { username: user, password: pass },
        success: function(r) {
            $("#thongbao_login").html(r);
            $("#btnLogin").html(originalText).prop('disabled', false);
        },
        error: function() {
            Swal.fire("Lỗi", "Không thể kết nối máy chủ!", "error");
            $("#btnLogin").html(originalText).prop('disabled', false);
        }
    });
});
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
