<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");

if(isset($_COOKIE['token'])) {
    if($getUser['level'] == 'tho') {
        header("Location: /tho-dashboard.php");
        exit;
    }
}

$title = "Đăng Nhập Thợ | Điện Máy Hiếu";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");
?>

<main>
    <div class="wrap" style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
        <section class="panel" style="width: 100%; max-width: 450px; background: rgba(10, 25, 47, 0.95); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 40px 30px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <i class="fa-solid fa-user-gear" style="font-size: 48px; color: var(--brand); margin-bottom: 15px;"></i>
                <h2 style="color: white; margin: 0;">Đăng Nhập Thợ</h2>
                <p style="color: #94a3b8; margin-top: 10px; font-size: 14px;">Hệ thống nhận đơn Dịch Vụ</p>
            </div>
            
            <div id="thongbao_login"></div>
            
            <form id="loginForm" class="form" onsubmit="return false;">
                <div class="field full">
                    <label style="color: #cbd5e1;">Tài khoản</label>
                    <input type="text" id="username" placeholder="Nhập tên đăng nhập" required style="background: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">
                </div>
                
                <div class="field full">
                    <label style="color: #cbd5e1;">Mật khẩu</label>
                    <input type="password" id="password" placeholder="Nhập mật khẩu" required style="background: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">
                </div>
                
                <button type="submit" id="btnLogin" class="btn" style="width: 100%; margin-top: 20px; padding: 14px; font-size: 16px;">Đăng Nhập</button>
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
    
    $("#btnLogin").html('Đang xử lý...').prop('disabled', true);
    
    $.ajax({
        url: "/controller/client/Login.php",
        method: "POST",
        data: { username: user, password: pass },
        success: function(r) {
            $("#thongbao_login").html(r);
            $("#btnLogin").html('Đăng Nhập').prop('disabled', false);
        },
        error: function() {
            alert("Lỗi kết nối mạng!");
            $("#btnLogin").html('Đăng Nhập').prop('disabled', false);
        }
    });
});
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
