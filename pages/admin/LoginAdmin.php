<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

$tieude = "ĐĂNG NHẬP QUẢN TRỊ | ĐIỆN MÁY HIẾU";

// N?u ?a ??ng nhp admin -> chuy?n vo qu?n lý ??n hng
if (!empty($getUser) && $getUser['level'] == 'admin') {
    header("Location: /pages/admin/QuanLyDonHang.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = check_string($_POST['username']);
    $password = check_string($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ tài khoản và mật khẩu.';
    } else {
        $md5pass = md5($password);
        $check = $DMH->get_row("SELECT * FROM `users` WHERE `username` = '$username' AND `password` = '$md5pass' AND `level` = 'admin' AND `banned` = 'ON'");
        
        if ($check) {
            $token = random('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789', 64);
            $DMH->update("users", ['tokenlog' => $token], " `id` = '".$check['id']."' ");
            setcookie('token', $token, time() + 86400 * 30, '/');
            $_SESSION['loginadmin'] = true;
            header("Location: /pages/admin/QuanLyDonHang.php");
            exit;
        } else {
            $error = 'Tài khoản hoặc mật khẩu không chính xác.';
        }
    }
}

require_once(__DIR__."/../../pages/admin/Head.php");
?>
<body class="page-body login-page login-form-fall">

<script type="text/javascript">
var baseurl = '';
</script>

<div class="login-container">
	
	<div class="login-header login-caret">
		
		<div class="login-content">
			
			<a href="/" class="logo">
				<img src="/public/assets/logo.png" width="120" alt="Điện Máy Hiếu" />
			</a>
			
			<p class="description" style="font-size: 18px; font-weight: bold;">ĐIỆN MÁY HIẾU</p>
			<p class="description">Quản lý cửa hàng - Gọi thợ tận nhà</p>
			
		
		</div>
		
	</div>
	
	<div class="login-progressbar">
		<div></div>
	</div>
	
	<div class="login-form">
		
		<div class="login-content">
			
			<?php if ($error): ?>
				<div style="background: #ef4444; color: white; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;"><?= htmlspecialchars($error) ?></div>
			<?php endif; ?>
		
			<form role="form" method="POST" action="">
				
				<div class="form-group">
					
					<div class="input-group">
						<div class="input-group-addon">
							<i class="entypo-user"></i>
						</div>
						
						<input type="text" class="form-control" name="username" id="username" placeholder="Tài khoản admin" autocomplete="off" required value="anhthienvodich" />
					</div>
				
				</div>
				
				<div class="form-group">
					
					<div class="input-group">
						<div class="input-group-addon">
							<i class="entypo-key"></i>
						</div>
						
						<input type="password" class="form-control" name="password" id="password" placeholder="Mật khẩu" autocomplete="off" required />
					</div>
				
				</div>
				
				<div class="form-group">
					<button type="submit" class="btn btn-primary btn-block btn-login" id="btnLogin">
						<i class="entypo-login"></i>
						Đăng nhập hệ thống
					</button>
				</div>

			</form>
			
			
			
		</div>
		
	</div>
	
</div>


	<!-- Bottom scripts (common) -->
	<script src="/assets/js/gsap/TweenMax.min.js"></script>
	<script src="/assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
	<script src="/assets/js/bootstrap.js"></script>
	<script src="/assets/js/joinable.js"></script>
	<script src="/assets/js/resizeable.js"></script>
    <script src="assets/js/toastr.js"></script>
	<script src="/assets/js/neon-api.js"></script>
	<script src="/assets/js/jquery.validate.min.js"></script>
	<script src="/assets/js/neon-login.js"></script>


	<!-- JavaScripts initializations and stuff -->
	<script src="/assets/js/neon-custom.js"></script>


	<!-- Demo Settings -->
	<script src="/assets/js/neon-demo.js"></script>

</body>
</html>
