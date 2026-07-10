
<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Đăng Ký";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
?>
<div class="content-wrapper transition-all duration-150 ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px]" id="content_wrapper">
    <div class="page-content">
        <div class="container-fluid transition-all duration-150" id="page_layout">
            <main id="content_layout">
                <!-- Page Content -->
                <div class="mb-3">
                </div>

                <div class="card auth-box flex h-full flex-col justify-center">
                    <div class="mb-4 text-center 2xl:mb-10">
                        <h4 class="font-medium">Tạo Tài Khoản</h4>
                        <div class="text-base text-slate-500">
                            Đăng ký tài khoản mới miễn phí
                        </div>
                    </div>
                  
                    <br/>
                    <!-- START::LOGIN FORM -->
                    <form method="POST" class="space-y-4">
                        <div class="fromGroup">
                            <label for="username" class="form-label block capitalize">
                                Tên tài khoản
                            </label>
                            <input type="text" name="username" id="taikhoan" class="form-control  py-2" placeholder="Nhập tên tài khoản của bạn" required autofocus value="">
                        </div>
                        <div class="fromGroup">
                         <label for="email" class="form-label block capitalize">
                           Địa chỉ e-mail
                         </label>
                         <input type="text" name="email" id="email" class="form-control  py-2" placeholder="Dùng để cấp lại mật khẩu" value="">
                        
                        </div>

                        <div class="fromGroup">
                            <label for="password" class="form-label block capitalize">
                                Mật khẩu
                            </label>
                            <input type="password" name="password" id="matkhau" class="form-control  py-2" placeholder="Nhập mật khẩu của bạn" required autocomplete="new-password">
                        </div>
                        <div class="fromGroup">
                            <label for="password" class="form-label block capitalize">
                                Nhập lại Mật khẩu
                            </label>
                            <input type="password" name="password" id="matkhau2" class="form-control  py-2" placeholder="Nhập lại mật khẩu của bạn" required autocomplete="new-password">
                        </div>
                        <button type="button" id="Register" class="btn btn-dark block w-full text-center">
                            Đăng Ký Ngay
                        </button>
                    </form>
                    <!-- END::LOGIN FORM -->
                    <!-- END::LOGIN FORM -->
                    <div class="relative border-b border-b-[#9AA2AF] border-opacity-[16%] pt-6">
                        <div class="absolute left-1/2 top-1/2 inline-block min-w-max -translate-x-1/2 transform bg-white px-4 text-sm font-normal text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                            Or continue with
                        </div>
                    </div>
                    <div class="mx-auto mt-8 w-full max-w-[242px]">
                        <ul class="flex justify-center space-x-3">
                            <li>
                                <a href="/LoginGoogle" class="inline-flex h-10 w-10 flex-col items-center justify-center rounded-full bg-[#EA4335] text-2xl text-white">
                                    <img src="/images/icon/gp.svg" alt="Google">
                                </a>
                            </li>
                            <li>
                                <a href="/LoginFacebook" class="inline-flex h-10 w-10 flex-col items-center justify-center rounded-full bg-[#EA4335] text-2xl text-white">
                                    <img src="/images/svg/fb.svg" alt="FaceBook">
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="mx-auto mt-8 text-sm font-normal uppercase text-slate-500 dark:text-slate-400 md:max-w-[345px]">
                        <span> BẠN ĐÃ CÓ TÀI KHOẢN?</span>
                        <a href="<?=BASE_URL('dang-nhap');?>" class="font-medium text-slate-900 hover:underline dark:text-white">ĐĂNG NHẬP NGAY</a>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
$("#Register").on("click", function() {

    $('#Register').html('Đang xử lý...').prop('disabled',
        true);
    $.ajax({
        url: "<?=BASE_URL('controller/client/XulyLog.php');?>",
        method: "POST",
        data: {
            type: 'Register',
            email: $("#email").val(),
            taikhoan: $("#taikhoan").val(),
            matkhau: $("#matkhau").val(),
            matkhau2: $("#matkhau2").val()
        },
        success: function(response) {
            $("#thongbao").html(response);
            $('#Register').html(
                    ' Đăng Ký Ngay')
                .prop('disabled', false);
        }
    });
});
</script>
<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/client/Footer.php");
?>