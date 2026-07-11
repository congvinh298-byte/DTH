<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");

if(isset($_COOKIE['token'])) {
    if($getUser['level'] == 'tho') {
        header("Location: /tho-dashboard.php");
        exit;
    }
}

$title = "Cổng Thợ & Đối Tác | Điện Máy Hiếu";
require_once(__DIR__."/pages/client/Head.php");
?>

<style>
    body { background-color: #0f172a; margin: 0; font-family: 'Inter', sans-serif; }
    .tho-login-wrap { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
    .tho-login-panel { width: 100%; max-width: 420px; background: rgba(30,41,59,0.8); border: 2px solid #38bdf8; padding: 50px 40px; border-radius: 20px; box-shadow: 8px 8px 0px rgba(56,189,248,0.2); text-align: center; }
    .tho-icon { font-size: 50px; color: #38bdf8; margin-bottom: 15px; }
    .tho-input { width: 100%; background: #0f172a; border: 2px solid #334155; color: #fff; padding: 16px; border-radius: 12px; margin-bottom: 20px; font-size: 16px; font-weight: bold; text-align: center; transition: all 0.3s; }
    .tho-input:focus { border-color: #38bdf8; outline: none; box-shadow: 0 0 10px rgba(56,189,248,0.3); }
    .tho-btn { width: 100%; background: #38bdf8; color: #0f172a; border: none; padding: 18px; border-radius: 12px; font-size: 18px; font-weight: 900; text-transform: uppercase; cursor: pointer; transition: transform 0.1s; box-shadow: 4px 4px 0px rgba(0,0,0,0.3); }
    .tho-btn:active { transform: translate(2px, 2px); box-shadow: 2px 2px 0px rgba(0,0,0,0.3); }
</style>

<div class="tho-login-wrap fade-in">
    <div class="tho-login-panel">
        <i class="fa-solid fa-screwdriver-wrench tho-icon"></i>
        <h2 style="color: #fff; margin: 0 0 10px; font-weight: 900; text-transform: uppercase;">Cổng Thợ & Đối Tác</h2>
        <p style="color: #94a3b8; font-size: 14px; margin-bottom: 30px;">Hệ thống yêu cầu nhập lại mật khẩu mỗi phiên làm việc để bảo mật.</p>
        
        <div id="thongbao_login"></div>
        
        <!-- AUTOCOMPLETE OFF TO PREVENT BROWSER SAVING -->
        <form id="thoLoginForm" autocomplete="off" onsubmit="return false;">
            <!-- Dummy inputs to trick browsers -->
            <input type="text" style="display:none" name="fakeusernameremembered">
            <input type="password" style="display:none" name="fakepasswordremembered">
            
            <input type="text" id="username" class="tho-input" placeholder="Tên Đăng Nhập" autocomplete="new-password" spellcheck="false" required>
            <input type="password" id="password" class="tho-input" placeholder="Mật Khẩu" autocomplete="new-password" required>
            
            <button type="submit" id="btnLogin" class="tho-btn">VÀO HỆ THỐNG</button>
        </form>
        
        <div style="margin-top: 30px;">
            <a href="/login.php" style="color: #64748b; text-decoration: none; font-size: 14px; font-weight: bold;"><i class="fa-solid fa-arrow-left"></i> Quay lại cổng Khách Hàng</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$("#thoLoginForm").submit(function(e) {
    e.preventDefault();
    var user = $("#username").val().trim();
    var pass = $("#password").val().trim();
    
    if(!user || !pass) return;
    
    let originalText = $("#btnLogin").html();
    $("#btnLogin").html('<i class="fa-solid fa-spinner fa-spin"></i> ĐANG XỬ LÝ...').prop('disabled', true);
    
    $.ajax({
        url: "/controller/client/Login.php",
        method: "POST",
        data: { username: user, password: pass },
        success: function(r) {
            $("#thongbao_login").html(r);
            $("#btnLogin").html(originalText).prop('disabled', false);
        }
    });
});
</script>
</body>
</html>
