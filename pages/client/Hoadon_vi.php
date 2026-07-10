<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Hóa Đơn Thanh Toán";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
CheckLogin();
?>
<?php
if(isset($_GET['magd'])) {
    $magd = check_string($_GET['magd']);
    $row = $TUANORI->get_row(" SELECT * FROM `hoadon_vi` WHERE `magd` = '".check_string($_GET['magd'])."' AND `username` = '".$getUser['username']."' ");
    if(!$row)
    {
        msg_error("Dữ liệu tạo này không hợp lệ", BASE_URL('Nap/Vi'), 500);
    }
} else {
    msg_error("Liên kết của bạn thiếu Dữ Liệu", BASE_URL('Nap/Vi'), 0);
}
?>
<div class="content-wrapper transition-all duration-150 ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px] margin-0" id="content_wrapper">
    <div class="page-content">
        <div class="container-fluid transition-all duration-150" id="page_layout">
            <main id="content_layout">
                <!-- Page Content -->
                <div class="mb-3">
                </div>

                <section class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                        <div class="card">
                            <header class=" card-header noborder">
                                    <h6>Thông tin nạp tiền</h6>
                            </header>
                            <div class="card-body flex flex-col p-6">
                                <div class="flex justify-center mt-5 mb-3">
                                    <img src="/images/logo_thesieure.png" style="width: 200px">
                                </div>
                                <div class="border border-primary p-3 bg-white rounded-lg mb-3">
                                    <p style="text-align:center;">
                                        <span style="font-size:18px;">
                                            <strong>
                                                <span style="font-family:Calibri, sans-serif;">
                                                    <span style="font-family:Arial, sans-serif;"><span style="color:#000000;">Tài khoản nhận: <b id="copyTk" style="color: red">hacknro</b></span></span>
                                                    <i onclick="copy()" data-clipboard-target="#copyTk" class="fas fa-copy copy"></i>
                                                </span>
                                            </strong>
                                        </span>
                                    </p>
                                    <p style="text-align:center;">
                                        <span style="font-size:18px;">
                                            <strong>
                                                <span style="font-family:Calibri, sans-serif;">
                                                    <span style="font-family:Arial, sans-serif;"><span style="color:#000000;">Số tiền: <b id="copyMoney" style="color: red"><?=$row['sotien'];?></b></span></span>
                                                    <i onclick="copy()" data-clipboard-target="#copyMoney" class="fas fa-copy copy"></i>
                                                </span>
                                            </strong>
                                        </span>
                                    </p>
                                    <p style="text-align:center;">
                                        <span style="font-size:18px;">
                                            <strong>
                                                <span style="font-family:Calibri, sans-serif;">
                                                    <span style="font-family:Arial, sans-serif;"><span style="color:#000000;">Nội dung: <b id="copyStk" style="color: red"><?=$magd;?></b></span></span> 
                                                    <i onclick="copy()" data-clipboard-target="#copyStk" class="fas fa-copy copy"></i>
                                                </span>
                                            </strong>
                                        </span>
                                    </p>
                                </div>
                                <div class="border border-primary p-3 bg-white rounded-lg mb-3">
                                    <p style="text-align:center;">
                                        <span style="font-size:16px;">
                                            <span style="font-family:Calibri, sans-serif;">
                                                <span style="font-family:Arial, sans-serif;"><span style="color:#000000;">Hệ thống sẽ tự động xác nhận sau khi bạn chuyển tiền thành công</span></span></span>
                                        
                                        </span>
                                    </p>
                                    <p style="text-align:center;">
                                        <span style="font-size:16px;">
                                            <span style="font-family:Calibri, sans-serif;">
                                                <span style="font-family:Arial, sans-serif;"><span style="color:#000000;">Số tiền thực nhận: <b class="text-green-500">+ <?=number_format($row['thucnhan']);?> ₫</b></span></span></span>
                                                <br/>
                                                <button  id="HuyNap" class="btn btn-outline-primary btn-sm">
                                                    <div class="flex justify-between font-bold">
                                                        <span><i class="fa-sharp fa-solid fa-square-xmark"></i> Hủy Nạp</span>
                                                    </div>
                                                </button>
                                        </span>
                                    </p>
                                </div>
                                <div class="text-center">
                                    Trạng thái: <span id="status_vi"><i class="fa fa-spinner fa-spin"></i> Đang cập nhật</span> <br/>
                                </div>
                            </div>
                          
                        </div>
                    </div>
                    <script type="text/javascript">
                        function getStatusInvoice() {
                            $.ajax({
                                url: "<?=BASE_URL('controller/status/NapVi.php');?>",
                                type: "GET",
                                dataType: "JSON",
                                data: {
                                    magd: "<?=$magd;?>"
                                },
                                success: function(result) {
                                    $('#status_vi').html(result.msg);
                                }
                            });
                        }
                        setInterval(function() {
                            $('#status_vi').load(getStatusInvoice());
                        }, 3000);
                        $("#HuyNap").on("click", function() {

                            $('#HuyNap').html('Đang xử lý...').prop('disabled',
                                true);
                            $.ajax({
                                url: "<?=BASE_URL('controller/client/HuyNap.php');?>",
                                method: "POST",
                                data: {
                                    magd: '<?=$magd;?>'
                                },
                                success: function(response) {
                                    $("#thongbao").html(response);
                                    $('#HuyNap').html(
                                            '<i class="fa-sharp fa-solid fa-square-xmark"></i> Hủy Nạp')
                                        .prop('disabled', false);
                                }
                            });
                            });
                    </script>
                    <div class="text-center">
                        <a href="/Nap/Vi" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Quay Lại</a>
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