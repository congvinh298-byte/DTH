<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = "ĐĂNG NHẬP TRANG QUẢN TRỊ";
if($getUser['level'] != 'admin') {
	die('<script type="text/javascript">setTimeout(function(){ location.href = "'.BASE_URL().'" }, 0);</script>');
}
require_once("../../pages/admin/Head.php");
?>
<body class="page-body login-page login-form-fall">


<!-- This is needed when you send requests via Ajax -->
<script type="text/javascript">
var baseurl = '';
</script>

<div class="login-container">
	
	<div class="login-header login-caret">
		
		<div class="login-content">
			
			<a href="/" class="logo">
				<img src="/images/logo_tuan.png" width="120" alt="" />
			</a>
			
			<p class="description">Vui lòng đăng nhập ADMIN</p>
			
		
		</div>
		
	</div>
	
	<div class="login-progressbar">
		<div></div>
	</div>
	
	<div class="login-form">
		
		<div class="login-content">
			
		
			
			<form role="form">
				
				<div class="form-group">
					
					<div class="input-group">
						<div class="input-group-addon">
							<i class="entypo-key"></i>
						</div>
						
						<input type="password" class="form-control" name="password" id="password" placeholder="Mật khẩu" autocomplete="off" />
					</div>
				
				</div>
				
				<div class="form-group">
					<button type="submit" class="btn btn-primary btn-block btn-login" id="btnLogin">
						<i class="entypo-login"></i>
						Đăng nhập
					</button>
				</div>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.5/sweetalert2.all.js"></script>

			
                <script type="text/javascript">
                    $("#btnLogin").on("click", function() {
                        $('#btnLogin').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
                            true);
                        $.ajax({
                            url: "<?=BASE_URL('controller/admin/Dangnhap.php');?>",
                            method: "POST",
                            dataType: "JSON",
                            data: {
                                password: $("#password").val()
                            },
                            success: function(respone) {
                                cuteToast({
                                    type: respone.status,
                                    message: respone.msg,
                                    timer: 5000
                                });
                                if(respone.url != '-1') {
                                    setTimeout("location.href = '" + respone.url + "';", respone.time);
                                }
                                $('#btnLogin').html('<i class="entypo-login"></i> Đăng nhập').prop('disabled', false);
                            },
                            error: function() {
                                cuteToast({
                                    type: "error",
                                    message: 'Không thể xử lý',
                                    timer: 5000
                                });
                                $('#btnLogin').html('<i class="entypo-login"></i> Đăng nhập').prop('disabled', false);
                            }

                        });
                    });
                </script>


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
