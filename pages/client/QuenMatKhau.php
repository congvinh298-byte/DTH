
<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Quên mật khẩu";
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
                        <h4 class="font-medium"> Đặt Lại Mật Khẩu</h4>
                        <div class="text-base text-slate-500">
                            Nhập Email Của Bạn Để Chúng Tôi Gửi Lại Mật Khẩu Mới
                        </div>
                    </div>
                    <form class="space-y-4">
                        <div class="fromGroup">
                            <label for="email" class="form-label block capitalize">Địa Chỉ Email</label>
                            <div class="relative">
                                <input type="text" id="email" class="form-control  py-2" placeholder="Nhập địa chỉ email" autofocus value="">
                            </div>
                        </div>

                        <button type="button" id="Quenpass" class="btn btn-dark block w-full text-center">
                            Đặt lại mật khẩu
                        </button>
                    </form>

                    <div class="mx-auto mt-12 text-sm font-normal uppercase text-slate-500 dark:text-slate-400 md:max-w-[345px]">
                        Bạn đã có lại mật khẩu?
                        <a href="/dang-nhap" class="font-medium text-slate-900 hover:underline dark:text-white">Đăng Nhập Ngay</a>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
$("#Quenpass").on("click", function() {

    $('#Quenpass').html('Đang xử lý...').prop('disabled',
        true);
    $.ajax({
        url: "<?=BASE_URL("controller/client/XulyLog.php");?>",
        method: "POST",
        data: {
            type: 'Quenpass',
            email: $("#email").val()
        },
        success: function(response) {
            $("#thongbao").html(response);
            $('#Quenpass').html(
                    'Đặt lại mật khẩu')
                .prop('disabled', false);
        }
    });
});
</script>
<?php

require_once("../../pages/client/Footer.php");
?>