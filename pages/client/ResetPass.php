
<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Cập nhật lại mật khẩu";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
?>
<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
if(isset($_COOKIE['token'])) {
    msg_error("Bạn đã đăng nhập từ trước", "/", 100);
} else {
    if(empty($_GET['token']) || strlen($token) < 6) {
        msg_error("Thông tin không hợp lệ", "/", 100);
    }
    $token = check_string($_GET['token']);
    if(!$TUANORI->get_row(" SELECT * FROM `users` WHERE `token_resetpas` = '$token' ")) {
        msg_error("Thông tin không hợp lệ", "/", 100);
    }
}
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
                        <h4 class="font-medium"> Cập Nhật Mật Khẩu</h4>
                        <div class="text-base text-slate-500">
                            Nhập mật khẩu mới của bạn
                        </div>
                    </div>
                    <form class="space-y-4">
                        <div class="fromGroup">
                            <label for="pass1" class="form-label block capitalize">Nhập mật khẩu</label>
                            <div class="relative">
                                <input type="password" id="pass1" class="form-control  py-2" placeholder="Nhập mật khẩu" autofocus value="">
                            </div>
                        </div>
                        <div class="fromGroup">
                            <label for="pass2" class="form-label block capitalize">Nhập lại mật khẩu</label>
                            <div class="relative">
                                <input type="password" id="pass2" class="form-control  py-2" placeholder="Nhập lại mật khẩu" autofocus value="">
                            </div>
                        </div>

                        <button type="button" id="Quenpass" class="btn btn-dark block w-full text-center">
                            Cập nhật
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
            type: 'DatlaiPass',
            Passnew1: $("#pass1").val(),
            Passnew2: $("#pass2").val()
        },
        success: function(response) {
            $("#thongbao").html(response);
            $('#Quenpass').html(
                    'Cập nhật')
                .prop('disabled', false);
        }
    });
});
</script>
<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/client/Footer.php");
?>