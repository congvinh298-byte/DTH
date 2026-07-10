<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Thanh toán giỏ hàng";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
CheckLogin();
$sotien = $cnt = 0;
?>
<?php foreach($TUANORI->get_list(" SELECT * FROM `giohang` WHERE `username` = '".$getUser['username']."' ") as $ok) {
    if($TUANORI->site('sukien') == 'ON' && $TUANORI->site('ptgiamgia') > 0) {
        $sotien +=$ok['sotien'] - ($ok['sotien']*$TUANORI->site('ptgiamgia')/100);
    } else {
        $sotien +=$ok['sotien'];
    }
    ++$cnt;
} ?>
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
                                    <h6>Thanh toán giỏ hàng</h6>
                            </header>
                            <div class="card-body flex flex-col p-6">
                              
                                <div class="border border-primary p-3 bg-white rounded-lg mb-3">
                                    <p style="text-align:center;">
                                        <span style="font-size:18px;">
                                            <strong>
                                                <span style="font-family:Calibri, sans-serif;">
                                                    <span style="font-family:Arial, sans-serif;"><span style="color:#000000;">Tổng tiền: <b style="color: red"><?=sotienmua($sotien);?></b></span></span><br/>
                                                    <span style="font-family:Arial, sans-serif;"><span style="color:#000000;">Số lượng: 
                                                        <b style="color: red"><?=number_format($cnt);?></b></span></span>
                                                </span>
                                            </strong>
                                            <form class="space-y-3">
                                                <div class="input-area">
                                                    <label for="magiamgia" class="form-label">Mã giảm giá (nếu có)</label>
                                                    <input type="text" class="form-control  py-2" id="magiamgia" name="magiamgia" placeholder="Mã giảm giá" required="">
                                                    <i>Áp dụng cho từng sản phẩm</i>
                                                </div>
                                                <div class="flex justify-center">
                                                    <a>
                                                        <button class="btn btn-secondary w-full" type="button" id="Thanhtoan" title="Xem Tất Cả"><i class="fas fa-share"></i> <span>Thanh toán</span></button>
                                                    </a>
                                                </div>
                                            </form>
                                        </span>
                                    </p>
                                </div>
                             
                               
                            </div>
                          
                        </div>
                    </div>
                
                    <div class="text-center">
                        <a href="/GioHang" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Quay Lại</a>
                    </div>
                </section>
            </main>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
$("#Thanhtoan").on("click", function() {

    $('#Thanhtoan').html('Đang xử lý...').prop('disabled',
        true);
    $.ajax({
        url: "<?=BASE_URL('controller/client/XulyGioHang.php');?>",
        method: "POST",
        data: {
            mgg: $("#magiamgia").val()
        },
        success: function(response) {
            $("#thongbao").html(response);
            $('#Thanhtoan').html(
                    '<i class="fas fa-share"></i> <span>Thanh toán</span>')
                .prop('disabled', false);
        }
    });
});
</script>
<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/client/Footer.php");
?>