
<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "KẾT NỐI API";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
CheckLogin();
$row = $TUANORI->get_row(" SELECT * FROM `key_apis` WHERE `username` = '".$getUser['username']."' ");
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
                                        <div class="card-title text-slate-900 dark:text-white">Thông Tin API</div>
                                    </div>
                                </header>
                                <div class="card-text h-full space-y-4">
                                    <form class="space-y-3">
                                    <div class="alert alert-info" role="alert" style="font-size: 15px">Tính năng này dành riêng cho Developer (Lập trình viên)</div>
                                        <div class="input-area">
                                            <div class="card border border-red-400 p-3">
                                                <div class="card-body">
                                                    <span style="font-family:Calibri, sans-serif;">
                                                        <span style="font-family:Arial, sans-serif; font-size:15px"><span style="color:#000000;">TOKEN API: <b id="copyToken" style="color: red"><?=$getUser['token_api'];?></b></span></span> 
                                                        <i onclick="copy()" data-clipboard-target="#copyToken" class="fas fa-copy copy"></i>
                                                    </span>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="input-area">
                                            <button type="button" id = "btnChange" class="btn btn-sm btn-primary w-full"><i class="fa fa-exchange-alt"></i> ĐỔI TOKEN</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            $("#btnChange").on("click", function() {

                                $('#btnChange').html('Đang xử lý...').prop('disabled',
                                    true);
                                $.ajax({
                                    url: "<?=BASE_URL('controller/client/ChangeAPI.php');?>",
                                    method: "POST",
                                    data: {
                                        type: 'ChangeAPI'
                                    },
                                    success: function(response) {
                                        $("#thongbao").html(response);
                                        $('#btnChange').html(
                                                '<i class="fa fa-exchange-alt"></i> ĐỔI TOKEN')
                                            .prop('disabled', false);
                                    }
                                });
                            });
                        </script>
                        <div class="card">
                            <header class="card-header noborder">
                                <h4 class="card-title">Cấu Hình API</h4>
                            </header>
                            <div class="card-body px-6 pb-6">
                                <div class="card border border-red-400 p-3">
                                    <div class="card-body">
                                        <p style="color: #999900">Tài liệu API: <a href="https://documenter.getpostman.com/view/32909581/2sA2r3ZRZP" style="color: blue" target="_blank">Xem tại đây</a></p>
                                    </div>
                                </div> <br/>

                                <?php if($row) { ?>
                                <form class="space-y-3">
                                    <div class="input-area">
                                        <label for="whois" class="form-label">WHOIS DOMAIN <?=token_api($row['whois']);?></label>
                                        <select class="form-control" id="whois" required>
                                            <option value="ON" <?=($row['whois'] == 'ON') ? 'selected': ''?>>ON</option>
                                            <option value="OFF" <?=($row['whois'] == 'OFF') ? 'selected': ''?>>OFF</option>
                                        </select>
                                    </div>
                                    <div class="input-area">
                                        <label for="list_code" class="form-label">List Code <?=token_api($row['list_code']);?></label>
                                        <select class="form-control" id="list_code" required>
                                            <option value="ON" <?=($row['list_code'] == 'ON') ? 'selected': ''?>>ON</option>
                                            <option value="OFF" <?=($row['list_code'] == 'OFF') ? 'selected': ''?>>OFF</option>
                                        </select>
                                    </div>
                                    <div class="input-area">
                                        <label for="buy_code" class="form-label">Mua code <?=token_api($row['buy_code']);?></label>
                                        <select class="form-control" id="buy_code" required>
                                            <option value="ON" <?=($row['buy_code'] == 'ON') ? 'selected': ''?>>ON</option>
                                            <option value="OFF" <?=($row['buy_code'] == 'OFF') ? 'selected': ''?>>OFF</option>
                                        </select>
                                    </div>
                                    <div class="input-area">
                                        <label for="buy_domain" class="form-label">Mua tên miền <?=token_api($row['buy_domain']);?></label>
                                        <select class="form-control" id="buy_domain" required>
                                            <option value="ON" <?=($row['buy_domain'] == 'ON') ? 'selected': ''?>>ON</option>
                                            <option value="OFF" <?=($row['buy_domain'] == 'OFF') ? 'selected': ''?>>OFF</option>
                                        </select>
                                    </div>
                                    <div class="input-area">
                                        <button type="button" id="btnUpdate" class="btn btn-sm btn-primary w-full"><i class="fa-solid fa-up-right-from-square"></i> Cập Nhật</button>
                                    </div>
                                </form>
                                <script type="text/javascript">
                                    $("#btnUpdate").on("click", function() {

                                        $('#btnUpdate').html('Đang xử lý...').prop('disabled',
                                            true);
                                        $.ajax({
                                            url: "<?=BASE_URL('controller/client/UpdateAPI.php');?>",
                                            method: "POST",
                                            data: {
                                                type: 'UpDate',
                                                whois: $("#whois").val(),
                                                list_code: $("#list_code").val(),
                                                buy_code: $("#buy_code").val(),
                                                buy_domain: $("#buy_domain").val()
                                            },
                                            success: function(response) {
                                                $("#thongbao").html(response);
                                                $('#btnUpdate').html(
                                                        '<i class="fa-solid fa-up-right-from-square"></i> Cập Nhật')
                                                    .prop('disabled', false);
                                            }
                                        });
                                    });
                                </script>
                                <?php } else { ?>
                                <div class="card border border-red-400 p-3">
                                    <div class="card-body">
                                        <p class="text-red-500 text-xl">+ API của bạn chưa được kích hoạt. </p>
                                        <p style="color: green">+ Vui lòng liên hệ ADMIN để kích hoạt API.</p>

                                    </div>
                                </div>

                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</div>
</div>

<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/client/Footer.php");
?>