<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$id = check_string($_GET['id']);
$tt = $TUANORI->get_row(" SELECT * FROM `danhsachmuacode` WHERE `id` = '$id' AND `hienthi` = 'SHOW'");
$anhbia = $tt['img'];
$title = '['.strtoupper($_SERVER['SERVER_NAME']).'] '.$tt['title'];
$mota = $tt['mota'];
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
?>
<?php
if(!$tt)
{
	msg_error('Link bạn truy cập không đúng hoặc đã bị xóa', BASE_URL(''), 3000);
}
$TUANORI->cong("danhsachmuacode", "luotxem", 1, " `id` = '$id'");
?>
<!-- <script type="text/javascript">Swal.fire("Thành Công", "12344", "success");</script> -->
<div class="content-wrapper transition-all duration-150 ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px]" id="content_wrapper">
    <div class="page-content">
        <div class="container-fluid transition-all duration-150" id="page_layout">
            <main id="content_layout">
                <!-- Page Content -->
                <div class="mb-3">
                </div>

                
                <section class="space-y-6">
                    <div>
                        <div class="card">
                            <div class="card-body grid grid-cols-1 gap-x-6 p-6 md:grid-cols-2">
                                <div class="ant-ribbon-wrapper css-eq3tly">
                                    <div class="mb-5 md:mb-0" >
                                        <img src="<?=$tt['img']?>" class="h-[200px] w-full cursor-pointer rounded-lg md:h-[300px]" alt="<?=$tt['title']?>">
                                    </div>
                                    <div class="ant-ribbon ant-ribbon-placement-end ant-ribbon-color-black css-eq3tly"><span class="ant-ribbon-text">Mã: <?=$tt['id'];?></span>
                                        <div class="ant-ribbon-corner"></div>
                                    </div>
                                    <?php if($TUANORI->site('sukien') == 'ON' && $TUANORI->site('ptgiamgia') > 0) { ?>
                                        <div class="ant-ribbon ant-ribbon-placement-start ant-ribbon-color-red css-eq3tly"><span class="ant-ribbon-text">-<?=$TUANORI->site('ptgiamgia');?>%</span>
                                            <div class="ant-ribbon-corner"></div>
                                        </div>
                                    <?php } ?>
                                </div>
                                
                                <div class="flex flex-col justify-center space-y-3 text-center">
                                    <div class="mb-2">
                                        <h1 class="mb-1 text-[20px] md:text-[30px] ">[Mã code <span class="cursor-pointer text-red-600" itemprop="productID">#<?=$tt['id'];?></span>]</h1>
                                        <div class="mx-auto h-[3px] w-[200px] bg-primary"></div>
                                    </div>
                                    <h1 class="text-[12.4px] md:text-lg ">Tên: <?=$tt['title'];?></a></h1><br/>
                                    <h1 class="text-[12.4px] md:text-lg ">Mô tả: <b><?=$tt['mota'];?></b></a></h1>
                                    <div>
                                        <h2 class="text-[18px] md:text-[24px] text-primary">Giá: 
                                            <?php if($TUANORI->site('sukien') == 'ON' && $TUANORI->site('ptgiamgia') > 0 && $tt['money'] > 0) {
                                                $sotien = sotienmua($tt['money'] - $tt['money']*$TUANORI->site('ptgiamgia')/100);
                                                echo $sotien;
                                            } else {
                                                echo sotienmua($tt['money']);
                                            }?>
                                        </h2>
                                        <?php if($TUANORI->site('sukien') == 'ON' && $TUANORI->site('ptgiamgia') > 0 && $tt['money'] > 0) { ?> <br/>
                                            <p>Giá: <del><b style="color: red"><?=sotienmua($tt['money']);?></b></del> chỉ còn <b style="color: green"><?=$sotien;?></b></p>
                                        <?php } ?>
                                    </div>
                                    
                                    <form method="POST" class="space-y-3">
                                        <div class="input-area">
                                            <input type="text" class="form-control  py-2" id="magiamgia" placeholder="Mã giảm giá (Nếu có)" required="">
                                        </div>
                                        
                                    </form>
                                    <div class="text-center  hover:scale-110 transition-all">
                                        <button class="btn mt-5 btn-primary relative bg-cover bg-center bg-no-repeat " style="background-image: url(/images/matrix.jpg)" id="Muacode">
                                            <i class="fas fa-credit-card me-2"></i> Thanh Toán</button>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                        
                    </div>
                    
                    <script type="text/javascript">
                        $("#Muacode").on("click", function() {

                            $('#Muacode').html('Đang xử lý...').prop('disabled',
                                true);
                            $.ajax({
                                url: "<?=BASE_URL('controller/client/Muahang.php');?>",
                                method: "POST",
                                data: {
                                    type: 'Muacode',
                                    id: '<?=$id;?>',
                                    magiamgia: $("#magiamgia").val()
                                },
                                success: function(response) {
                                    $("#thongbao").html(response);
                                    $('#Muacode').html(
                                            '<i class="fas fa-credit-card me-2"></i> Thanh Toán')
                                        .prop('disabled', false);
                                }
                            });
                        });
                    </script>
                    <div>
                        <div class="card">
                            <div class="card-body flex flex-col p-6">
                                <header class="-mx-6 mb-5 flex items-center border-b border-slate-100 px-6 pb-5 dark:border-slate-700">
                                    <div class="flex-1">
                                        <div class="card-title text-slate-900 dark:text-white">Chi tiết sản phẩm : </div>
                                    </div>
                                </header>
                                <div class="card-text h-full">
                                    <div>
                                        <div class="text-center">
                                            <ul class="nav nav-pills flex items-center flex-wrap list-none pl-0 mb-6 space-x-4 justify-center" id="pills-tabHorizontal" role="tablist">
                                                <li class="nav-item text-center" role="presentation">
                                                    <a href="<?=$TUANORI->site('fbadmin');?>" target="_blank" class="nav-link block font-medium font-Inter text-sm leading-tight capitalize rounded-md px-6 py-3 focus:outline-none focus:ring-0 active dark:bg-slate-900 dark:text-slate-300">
                                                        <i class="fa-brands fa-facebook"></i> FB ADMIN </a>
                                                </li> 
                                                <li class="nav-item text-center" role="presentation">
                                                    <a href="https://zalo.me/<?=$TUANORI->site('zaloadmin');?>" target="_blank"  class="nav-link block font-medium font-Inter text-sm leading-tight capitalize rounded-md px-6 py-3 focus:outline-none focus:ring-0 active dark:bg-slate-900 dark:text-slate-300">
                                                        <i class="fab fa-facebook-messenger"></i> Zalo ADMIN</a>
                                                </li> 
                                            </ul>
                                        </div>
                                        <div class="tab-content" id="pills-tabContentHorizontal">
                                            <div class="tab-pane fade show active" id="pills-infomation" role="tabpanel" aria-labelledby="pills-home-tabHorizontal">
                                                <div class="space-y-5">
                                                    <div class="card border border-red-400 p-3">
                                                        <div class="card-body">
                                                            <p>CAM KẾT CODE GIỐNG NHƯ ẢNH 100%</p>
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-3">
                                                        <?php foreach(explode("\n", $tt['listimg']) as $ok) { ?>
                                                            <div class="gallery cursor-pointer">
                                                                <a href="<?=$ok;?>" class="glightbox" >
                                                                    <img class="w-full h-full rounded-sm lazyload" src="/images/svg/spinner.svg" data-src="<?=$ok;?>" alt="<?=$tt['title'];?>">
                                                                </a>
                                                            </div>
                                                        <?php } ?>
                                                    </div>



                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="fullpage" onclick="this.style.display='none';"></div>
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