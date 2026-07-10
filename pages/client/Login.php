
<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Đăng Nhập";
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
                        <h4 class="font-medium"> Đăng Nhập</h4>
                        <div class="text-base text-slate-500">
                            Đăng nhập vào hệ thống
                        </div>
                    </div>
                   
                    <br/>
                    <form method="POST" class="space-y-4">
                        <div class="fromGroup">
                            <label for="username" class="form-label block capitalize">Tài Khoản</label>
                            <div class="relative">
                                <input type="text" name="username" id="taikhoan" class="form-control  py-2" placeholder="Nhập Tên Tài Khoản" autofocus value="">
                            </div>
                        </div>


                        <div class="fromGroup">
                            <label for="password" class="form-label block capitalize">Mật Khẩu</label>
                            <div class="relative">
                                <input type="password" name="password" id ="matkhau" class="form-control  py-2" placeholder="Nhập Mật Khẩu Của Bạn" id="password" autocomplete="current-password">
                            </div>
                        </div>


                        <div class="flex justify-between">
                            <div class="checkbox-area">
                                <label class="inline-flex cursor-pointer items-center" for="remember_me">
                                    <input type="checkbox" class="hidden" name="remember" id="remember_me">
                                    <span class="relative inline-flex h-4 w-4 flex-none rounded border border-slate-100 bg-slate-100 transition-all duration-150 ltr:mr-3 rtl:ml-3 dark:border-slate-800 dark:bg-slate-900">
                                        <img src="images/icon/ck-white.svg" alt="" class="m-auto block h-[10px] w-[10px] opacity-0"></span>
                                    <span class="text-sm leading-6 text-slate-500 dark:text-slate-400">Ghi Nhớ Tài Khoản</span>
                                </label>
                            </div>
                            <a href="/quen-mat-khau" class="text-sm font-medium leading-6 text-slate-800 dark:text-slate-400">-->
                             Bạn Quên Mật Khẩu?
                            </a>
                        </div>

                        <button type="button" id="Login" class="btn btn-dark block w-full text-center">
                            Đăng Nhập
                        </button>
                    </form>
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


                    <div class="mx-auto mt-12 text-sm font-normal uppercase text-slate-500 dark:text-slate-400 md:max-w-[345px]">
                        Bạn Chưa Có Tài Khoản?
                        <a href="/dang-ky" class="font-medium text-slate-900 hover:underline dark:text-white">Đăng Ký Ngay</a>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
    $("#Login").on("click", function() {

        $('#Login').html('Đang xử lý...').prop('disabled',
            true);
        $.ajax({
            url: "<?=BASE_URL('controller/client/XulyLog.php');?>",
            method: "POST",
            data: {
                type: 'Login',
                taikhoan: $("#taikhoan").val(),
                matkhau: $("#matkhau").val()
            },
            success: function(response) {
                $("#thongbao").html(response);
                $('#Login').html(
                        '  Đăng Nhập')
                    .prop('disabled', false);
            }
        });
    });
</script>
<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/client/Footer.php");
?>