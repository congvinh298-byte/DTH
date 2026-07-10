
<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Profile";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
CheckLogin();
?>

<div class="content-wrapper transition-all duration-150 ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px]" id="content_wrapper">
    <div class="page-content">
        <div class="container-fluid transition-all duration-150" id="page_layout">
            <main id="content_layout">
                <!-- Page Content -->
                <div class="mb-3"></div>
                <section class="space-y-6">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div class="card">
                            <div class="card-body flex flex-col p-6">
                                <header class="-mx-6 mb-5 flex items-center border-b border-slate-100 px-6 pb-5 dark:border-slate-700">
                                    <div class="flex-1">
                                        <div class="card-title text-slate-900 dark:text-white">Thông Tin Tài Khoản</div>
                                    </div>
                                </header>
                                <div class="card-text h-full space-y-4">
                                    <form class="space-y-3">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="input-area">
                                                <label for="username" class="form-label">Tên Đăng Nhập</label>
                                                <input id="username" name="username" type="text" class="form-control" value="<?=$getUser['username'];?>" disabled="">
                                            </div>
                                            <div class="input-area">
                                                <label for="email" class="form-label">Địa chỉ e-mail</label>
                                                <input id="email" name="email" type="email" class="form-control" value="<?=$getUser['email'];?>">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="input-area">
                                                <label for="created_at" class="form-label">Ngày Đăng Ký</label>
                                                <input id="created_at" name="created_at" type="text" class="form-control" value="<?=$getUser['timereg'];?>" disabled="">
                                            </div>
                                            <div class="input-area">
                                                <label for="updated_at" class="form-label">Ngày Online gần đây</label>
                                                <input id="updated_at" name="updated_at" type="text" class="form-control" value="<?=$getUser['timeon'];?>" disabled="">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="input-area">
                                                <label for="balance" class="form-label">Số Tiền Hiện Có</label>
                                                <input id="balance" name="balance" type="text" class="form-control" value="<?=number_format($getUser['money']);?> ₫" disabled="">
                                            </div>
                                            <div class="input-area">
                                                <label for="total_deposit" class="form-label">Tổng Tiền Đã Nạp</label>
                                                <input id="total_deposit" name="total_deposit" type="text" class="form-control" value="<?=number_format($getUser['total_money']);?> ₫" disabled="">
                                            </div>
                                        </div>
                                        <div class="input-area">
                                            <label for="zalo" class="form-label">Số Zalo</label>
                                            <input id="zalo" type="text" class="form-control" value="<?=$getUser['zalo'];?>">
                                        </div>
                                        <div class="input-area">
                                            <button type="button" id = "Capnhat" class="btn btn-sm btn-primary w-full">Cập nhật</button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <header class="card-header noborder">
                                <h4 class="card-title">Thay đổi mật khẩu</h4>
                            </header>
                            <div class="card-body px-6 pb-6">
                                <form method="POST" class="space-y-3">
                                    <div class="input-area">
                                        <label for="old_password" class="form-label">Mật Khẩu Cũ</label>
                                        <input type="password" class="form-control  py-2" id="mkcu" name="old_password" placeholder="Nhập mật khẩu cũ" required="">
                                    </div>
                                    <div class="input-area">
                                        <label for="new_password" class="form-label">Mật Khẩu Mới</label>
                                        <input type="password" class="form-control  py-2" id="mknew" name="new_password" placeholder="Nhập mật khẩu mới" required="">
                                    </div>
                                    <div class="input-area">
                                        <label for="confirm_password" class="form-label">Xác Nhận Mật Khẩu</label>
                                        <input type="password" class="form-control  py-2" id="mknew2" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required="">
                                    </div>
                                    <div class="input-area">
                                        <button type="button" id = "Doimatkhau" class="btn btn-sm btn-primary w-full">Đổi Mật Khẩu</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript">

                        $("#Capnhat").on("click", function() {

                            $('#Capnhat').html('Đang xử lý...').prop('disabled',
                                true);
                            $.ajax({
                                url: "<?=BASE_URL('controller/client/XulyLog.php');?>",
                                method: "POST",
                                data: {
                                    type: 'UpdateInfo',
                                    email: $("#email").val(),
                                    zalo: $("#zalo").val()
                                },
                                success: function(response) {
                                    $("#thongbao").html(response);
                                    $('#Capnhat').html(
                                            'Cập nhật')
                                        .prop('disabled', false);
                                }
                            });
                        });


                        $("#Doimatkhau").on("click", function() {

                            $('#Doimatkhau').html('Đang xử lý...').prop('disabled',
                                true);
                            $.ajax({
                                url: "<?=BASE_URL('controller/client/XulyLog.php');?>",
                                method: "POST",
                                data: {
                                    type: 'Doimatkhau',
                                    mkcu: $("#mkcu").val(),
                                    mknew: $("#mknew").val(),
                                    mknew2: $("#mknew2").val()
                                },
                                success: function(response) {
                                    $("#thongbao").html(response);
                                    $('#Doimatkhau').html(
                                            'Đổi mật khẩu')
                                        .prop('disabled', false);
                                }
                            });
                        });
                        </script>
                    
                </section>
            </main>
        </div>
    </div>
</div>
</div>

<?php

require_once("../../pages/client/Footer.php");
?>