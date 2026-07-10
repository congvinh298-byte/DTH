
<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Nạp Tiền";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
CheckLogin();
?>
<div class="content-wrapper transition-all duration-150 ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px]" id="content_wrapper">
    <div class="page-content">
        <div class="container-fluid transition-all duration-150" id="page_layout">
            <main id="content_layout">
                <!-- Page Content -->
                <div class="mb-3">
                </div>
                <section class="space-y-6">
                    <div class="mx-auto grid max-w-5xl grid-cols-1 gap-6 md:grid-cols-3">
                        <div class="col-span-1 md:col-span-3 card">
                            <div class="card-body flex flex-col p-6">
                                <div class="card-text h-full space-y-4">

                                    <div class="card border border-red-400 p-3">
                                        <div class="card-body">
                                            <p class="text-red-500 text-xl">1. Nạp tiền bằng MOMO, ACB hoặc THESIEURE sẽ được duyệt tự động, nếu bạn chuyển đúng nội dung nạp tiền </p>
                                            <p class="text-red-500 text-xl">2. Trường hợp nếu bạn ghi sai nội dung. Vui lòng liên hệ admin để được giải quyết. Nội dung có Phân Biệt Chữ Hoa, Chữ Thường</p>
                                            <?php if($DMH->site('sukien') == 'ON' && $DMH->site('khuyenmai') > 0) { ?>
                                            <p class="text-xl">
                                                <i style="color: green" class="fa-solid fa-crown"></i> <b style="color: green">Hệ thống đang khuyến mãi thêm <b style="color: red"><?=$DMH->site('khuyenmai');?>%</b> giá trị nạp tiền qua ATM/ MOMO/ THESIEURE.</b>
                                            </p>
                                            <br/>
                                            <?php } ?>

                                            <p class="text-xl" style="font-weight: bold;"><i class="fa fa-arrow-circle-right"></i> Trường hợp nạp tiền quá lâu mà chưa thấy cộng, vui lòng liên hệ admin ở góc bên dưới màn hình.</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        
                        <?php foreach($DMH->get_list(" SELECT * FROM `listbank` WHERE `status` = 'SHOW' ORDER BY id DESC") as $row){ ?>
                        <div>
                            <div class="rounded-b-none border border-none bg-transparent p-4">
                                <img src="<?=$row['img'];?>" alt="<?=$row['bank'];?>"    class="mx-auto w-[90px] cursor-pointer object-cover">
                            </div>
                            <div class="space-y-2 rounded-lg bg-[#002B5B] p-4 text-[18px] font-bold text-white">
                                <div class="flex flex-wrap justify-between">
                                    <span><?=$row['bank'];?>:</span>
                                    <span class=" cursor-pointer"><?=$row['stk'];?></span>
                                </div>
                                <div class="flex flex-wrap justify-between">
                                    <span>Chủ TK:</span>
                                    <span><?=$row['name'];?></span>
                                </div>
                                <div class="flex flex-wrap justify-between">
                                    <span>Nội Dung:</span>
                                    <span class="coy cursor-pointer" ><?=$DMH->site('nd_bank').$getUser['id']; ?></span>
                                </div>
                                <div class="text-center">
                                    Nhập đúng nội dung tiền tự động cộng trong vài phút
                                </div>
                                <div>
                                    <?php if($row['bank'] == 'MOMO') { ?>
                                        <img src="https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=2|99|<?=$row['stk'];?>|||0|0|0|<?=$DMH->site('nd_bank').$getUser['id']; ?>|transfer_myqr" class="mx-auto w-full rounded-lg object-fill">
                                        <?php } else if(in_array($row['bank'], ['MBBANK', 'ACB'])) { ?>
                                            <img src="https://api.vietqr.io/<?=$row['bank'];?>/<?=$row['stk'];?>/0/<?=$DMH->site('nd_bank').$getUser['id']; ?>/qronly2.jpg?accountName=<?=$row['name'];?>&bankName=<?=$row['bank'];?>" class="mx-auto w-full rounded-lg object-fill">
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </section>
            </main>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
$("#Napthe").on("click", function() {

    $('#Napthe').html('Đang xử lý...').prop('disabled',
        true);
    $.ajax({
        url: "<?=BASE_URL('controller/client/Napthe.php');?>",
        method: "POST",
        data: {
            type: 'Napthe',
            loaithe: $("#loaithe").val(),
            menhgia: $("#menhgia").val(),
            mathe: $("#mathe").val(),
            seri: $("#seri").val()
        },
        success: function(response) {
            $("#thongbao").html(response);
            $('#Napthe').html(
                    'Gửi thẻ')
                .prop('disabled', false);
        }
    });
});
function totalPrice(){
    var total = 0;
    var amount =  $("#menhgia").val();
    total = amount - amount * 14 / 100;
    $('#ketqua').html(total.toString().replace(/(.)(?=(\d{3})+$)/g, '$1.'));
}
</script>
<?php

require_once("../../pages/client/Footer.php");
?>