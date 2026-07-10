<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");
$title = "Điện Máy Hiệu - Hệ Thống Quản Lý & Cửa Hàng";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");
?>

<div class="content-wrapper transition-all duration-150 ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px]" id="content_wrapper">
    <div class="page-content">
        <div class="container-fluid transition-all duration-150" id="page_layout">
            <main id="content_layout">
                <!-- Page Content -->
                <div class="mb-3">
                    <div class="text-center mt-5 mb-5">
                        <img src="/public/assets/logo.png" alt="Điện Máy Hiệu" style="max-height: 120px; margin: 0 auto; display: block;" />
                        <h1 class="text-3xl font-bold mt-4">CÔNG TY TNHH MTV ĐIỆN TỬ HIẾU</h1>
                        <p class="text-lg text-gray-600">Unified Guest & Technician Flow - Quản Lý & Dịch Vụ</p>
                    </div>
                </div>
                <!-- Bot_EMH Integration -->
                <div id="bot-emh-container">
                     <!-- Integration hook for Customer Interaction AI -->
                     <script src="/bot_emh/public/js/bot_emh_widget.js" async></script>
                </div>
                <section>
                    <div class="grid gap-6 sm:grid-cols-1">
                        <div class="lg:col-span-2">
                            <div class="cursor-pointer shadow-lg-lg">
                                <img src="<?=$TUANORI->site('banner1');?>" class="w-full rounded-lg sm:h-auto sm:object-contain lg:h-[360px] lg:object-fill" alt="" />
                            </div>
                        </div>
                       
                    </div>
                    <div class="bg-white border border-primary p-3 mt-3 rounded-lg">
                        <p style="text-align:center;">
                            <span style="font-size:20px;">
                                <span style="color: black;"><i class="fa fa-arrow-circle-right"></i> <strong>THÔNG BÁO WEBSITE</strong> <i class="fa fa-arrow-circle-left"></i></span>
                            </span>
                        </p>
                        <?=$TUANORI->site('thongbao');?>
                    </div>
                    <?php if($TUANORI->site('sukien') == 'ON') { ?>
                    <div class="box-price-led bg-white border border-primary p-3 mt-3 rounded-lg">
                        <center><img src="https://imgur.com/56EFXz7.gif" width="7%" /></center>
                        <?php if($TUANORI->site('khuyenmai') > 0) { ?>
                        <p style="text-align:center; color: red">
                            + Khuyến mãi <?=$TUANORI->site('ptgiamgia');?>% giá trị nạp tiền vào tài khoản chỉ áp dụng cho Atm/Momo/Thesieure.
                        </p>
                        <?php } if($TUANORI->site('ptgiamgiaweb') > 0) { ?>
                        <p style="text-align:center; color: black">
                            + Giảm giá <?=$TUANORI->site('ptgiamgiaweb');?>% khi <b style="color: red">TẠO WEBSITE</b> trên hệ thống TUANORI.VN/TUANORI.COM.
                        </p>
                        <?php } if($TUANORI->site('ptgiamgia') > 0) { ?>
                        <p style="text-align:center; color: black">
                            + Giảm giá <?=$TUANORI->site('ptgiamgia');?>% khi <b style="color: red">MUA CODE</b> trên hệ thống TUANORI.VN/TUANORI.COM.
                        </p>
                    </div>
                    <?php } } ?>
                    <?php
                    $codenew = $TUANORI->num_rows(" SELECT * FROM `danhsachmuacode` WHERE YEAR(ngaydang) = ".date('Y')." AND MONTH(ngaydang) = ".date('m')." AND DAY(ngaydang) = ".date('d')."  ");
                    $codenew1 = $TUANORI->num_rows(" SELECT * FROM `danhsachmuacode` WHERE YEAR(ngaydang) = ".date('Y')." AND MONTH(ngaydang) = ".date('m')."  ");
                    $codenew2 = $TUANORI->num_rows(" SELECT * FROM `danhsachmuacode` WHERE YEAR(timeupdate) = ".date('Y')." AND MONTH(timeupdate) = ".date('m')."  ");
                    if($codenew >= 1 || $codenew1 >=1 || $codenew2>=1) { ?>
                    <div class="bg-white border border-primary p-3 mt-3 rounded-lg">
                        <p style="text-align:center;">
                            <span style="font-size:20px;">
                                <?php if($codenew >= 1) { ?>
                                <span style="color: black;">
                                    + Hôm nay, <?=date('d/m/Y', time());?> hệ thống đã <b style="color: red">ĐĂNG</b> thêm <span style="color: green"><?=format_cash($codenew);?></span> mã nguồn mới
                                </span> <br/>
                                <?php } if($codenew1 >=1) { ?>
                                    <span style="color: black;">
                                        + Hệ thống vừa xuất hiện thêm <span style="color: red"><?=format_cash($codenew1);?></span> loại mã nguồn mới. <a style="color: blue" href="<?=BASE_URL('Manguonthangnay');?>">Xem tất cả</a>
                                    </span> <br/>
                                <?php } if($codenew2 >=1 ) { ?>
                                    <span style="color: black;">
                                        + Trong tháng <b style="color: red"><?=date('m', time());?></b>, hệ thống vừa cập nhật lại dữ liệu (UPDATE NEW) cho  <span style="color: red"><?=format_cash($codenew2);?></span> mã nguồn. <a style="color: blue" href="<?=BASE_URL('Manguonupdate');?>">Xem tất cả</a>
                                    </span> <br/>
                                <?php } ?>
                            </span>
                        </p>
                    </div>
                    <?php } ?>

                </section>
                
                <section class="section-product space-y-3">
                    <div class="space-y-6" style="margin-bottom: 40px">
                        <div class="text-center">
                            <h1 class="text-xl md:text-4xl mb-1"><i class="fa fa-code"></i> DỊCH VỤ CỦA CHÚNG TÔI</h1>
                            <div class="h-[3px] bg-primary w-[170px] mx-auto"></div>
                        </div>
                        <div class="ant-row css-eq3tly" style="margin-left: -9px; margin-right: -9px;">
                            <div class="ant-col ant-col-xs-24 ant-col-md-8 ant-col-lg-6 mb-2 mt-2 cursor-pointer css-eq3tly" style="padding-left: 7px; padding-right: 7px;">
                                <div class="ant-ribbon-wrapper css-eq3tly">
                                    <div class="ant-ribbon-wrapper css-eq3tly">
                                        <div class="border border-primary rounded-lg p-[1px] bg-white">
                                            <div class="ant-image css-eq3tly" style="width: 100%;">
                                                <a href="/tao-trang-web">
                                                    <img src="/images/svg/spinner.svg" data-src="<?=$TUANORI->site('danhmuc1');?>" class="lazyload w-full lg:h-[180px] rounded-t-lg object-fill" alt="Dịch Vụ Tạo Trang We" />
                                                </a>
                                            </div>
                                            <div class="p-2">
                                                <div class="grid grid-cols-10 mb-3 gap-10">
                                                    <div class="text-center w-full">
                                                        <h2 class="text-xl md:text-1xl mb-1">Dịch Vụ Tạo Trang Web</h2>
                                                        <h4 class="text-[16px] md:text-[15px] font-bold text-truncate hover:whitespace-normal"><span class="text-red-500">Nhanh gọn, bảo hành trọn đời</span></h4>
                                                    </div>
                                                </div>
                                                <div class="flex justify-center">
                                                    <a href="/tao-trang-web">
                                                        <button class="btn btn-sm btn-primary w-full" title="Xem Tất Cả"><i class="fa fa-arrow-right"></i> <span>Xem ngay</span></button>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if($TUANORI->site('sukien') == 'ON' && $TUANORI->site('ptgiamgia') > 0) { ?>
                                        <div class="ant-ribbon ant-ribbon-placement-start ant-ribbon-color-green css-eq3tly"><span class="ant-ribbon-text">ĐANG GIẢM GIÁ</span>
                                            <div class="ant-ribbon-corner"></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="ant-col ant-col-xs-24 ant-col-md-8 ant-col-lg-6 mb-2 mt-2 cursor-pointer css-eq3tly" style="padding-left: 7px; padding-right: 7px;">
                                <div class="ant-ribbon-wrapper css-eq3tly">
                                    <div class="ant-ribbon-wrapper css-eq3tly">
                                        <div class="border border-primary rounded-lg p-[1px] bg-white">
                                            <div class="ant-image css-eq3tly" style="width: 100%;">
                                                <a href="/mua-source-code">
                                                    <img src="/images/svg/spinner.svg" data-src="<?=$TUANORI->site('danhmuc2');?>" class="lazyload w-full lg:h-[180px] rounded-t-lg object-fill"
                                                     alt="Dịch vụ mã nguồn" />
                                                </a>
                                            </div>
                                            <div class="p-2">
                                                <div class="grid grid-cols-10 mb-3 gap-10">
                                                    <div class="text-center w-full">
                                                        <h2 class="text-xl md:text-1xl mb-1">Dịch vụ mã nguồn</h2>
                                                        <h4 class="text-[16px] md:text-[15px] font-bold text-truncate hover:whitespace-normal"><span class="text-red-500">Có phí hoặc miễn phí</span></h4>
                                                    </div>
                                                </div>
                                                <div class="flex justify-center">
                                                    <a href="/mua-source-code">
                                                        <button class="btn btn-sm btn-primary w-full" title="Xem Tất Cả"><i class="fa fa-arrow-right"></i> <span>Xem ngay</span>
                                                        </button>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if($TUANORI->site('sukien') == 'ON' && $TUANORI->site('ptgiamgia') > 0) { ?>
                                        <div class="ant-ribbon ant-ribbon-placement-start ant-ribbon-color-green css-eq3tly"><span class="ant-ribbon-text">ĐANG GIẢM GIÁ</span>
                                            <div class="ant-ribbon-corner"></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="ant-col ant-col-xs-24 ant-col-md-8 ant-col-lg-6 mb-2 mt-2 cursor-pointer css-eq3tly" style="padding-left: 7px; padding-right: 7px;">
                                <div class="ant-ribbon-wrapper css-eq3tly">
                                    <div class="ant-ribbon-wrapper css-eq3tly">
                                        <div class="border border-primary rounded-lg p-[1px] bg-white">
                                            <div class="ant-image css-eq3tly" style="width: 100%;">
                                                <a href="/Mua-mien">
                                                    <img src="/images/svg/spinner.svg" data-src="<?=$TUANORI->site('danhmuc3');?>" class="lazyload w-full lg:h-[180px] rounded-t-lg object-fill" 
                                                    alt="Đăng ký tên miền" />
                                                </a>
                                            </div>
                                            <div class="p-2">
                                                <div class="grid grid-cols-10 mb-3 gap-10">
                                                    <div class="text-center w-full">
                                                        <h2 class="text-xl md:text-1xl mb-1">Đăng ký tên miền</h2>
                                                        <h4 class="text-[16px] md:text-[15px] font-bold text-truncate hover:whitespace-normal"><span class="text-red-500">Đăng ký tên miền dành riêng cho bạn</span></h4>
                                                    </div>
                                                </div>
                                                <div class="flex justify-center">
                                                    <a href="/Mua-mien">
                                                        <button class="btn btn-sm btn-primary w-full" title="Đăng ký tên miền"><i class="fas fa-shopping-cart me-2"></i> <span>Đăng ký</span>
                                                        </button>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="ant-col ant-col-xs-24 ant-col-md-8 ant-col-lg-6 mb-2 mt-2 cursor-pointer css-eq3tly" style="padding-left: 7px; padding-right: 7px;">
                                <div class="ant-ribbon-wrapper css-eq3tly">
                                    <div class="ant-ribbon-wrapper css-eq3tly">
                                        <div class="border border-primary rounded-lg p-[1px] bg-white">
                                            <div class="ant-image css-eq3tly" style="width: 100%;">
                                                <a href="/upanh">
                                                    <img src="/images/svg/spinner.svg" data-src="<?=$TUANORI->site('danhmuc4');?>" class="lazyload w-full lg:h-[180px] rounded-t-lg object-fill" 
                                                    alt="Upload Hình Ảnh Lấy Link" />
                                                </a>
                                            </div>
                                            <div class="p-2">
                                                <div class="grid grid-cols-10 mb-3 gap-10">
                                                    <div class="text-center w-full">
                                                        <h2 class="text-xl md:text-1xl mb-1">Upload Hình Ảnh Lấy Link</h2>
                                                        <h4 class="text-[16px] md:text-[15px] font-bold text-truncate hover:whitespace-normal"><span class="text-red-500">Up ảnh tốc độ cao</span></h4>
                                                    </div>
                                                </div>
                                                <div class="flex justify-center">
                                                    <a href="/upanh">
                                                        <button class="btn btn-sm btn-primary w-full" title="Tải ảnh lên"><i class="fa fa-upload"></i> <span>Tải ảnh lên</span>
                                                        </button>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </section>
                <style>
                    .news_title {
                        color:#000000;
                        font-weight:700;
                        font-size:15px;
                        overflow:hidden;
                        text-overflow:ellipsis;
                        line-height:25px;
                        -webkit-line-clamp:3;
                        display:-webkit-box;
                        -webkit-box-orient:vertical;
                    }
                    </style>

                    
                <section class="section-product space-y-3">
                    <div class="space-y-6" style="margin-bottom: 40px">
                        <div class="text-center">
                            <h1 class="text-xl md:text-4xl mb-1"><i class="fa fa-code"></i> SẢN PHẨM (CÓ PHÍ) MỚI NHẤT</h1>
                            <div class="h-[3px] bg-primary w-[170px] mx-auto"></div>
                        </div>
                        <div class="ant-row css-eq3tly" style="margin-left: -9px; margin-right: -9px;">
                            <?php foreach($TUANORI->get_list(" SELECT * FROM `danhsachmuacode` WHERE `hienthi` = 'SHOW' AND `money` > 0 ORDER BY id DESC LIMIT 16") as $row){ ?>
                                <div class="ant-col ant-col-xs-24 ant-col-md-8 ant-col-lg-6 mb-2 mt-2 cursor-pointer css-eq3tly" style="padding-left: 9px; padding-right: 9px;">
                                    <div class="ant-ribbon-wrapper css-eq3tly">
                                        <div class="ant-ribbon-wrapper css-eq3tly">
                                            <div class="border border-primary rounded-lg p-[1px]" style="background: transparent;">
                                                <div class="ant-image css-eq3tly" style="width: 100%;">
                                                <img src="/images/svg/spinner.svg" data-src="<?=$row['img'];?>" class="lazyload w-full lg:h-[180px] rounded-t-lg object-fill" alt="<?=$row['title'];?>" />
                                                    <!---->
                                                    <a href="<?=$row['img'];?>" class="glightbox" title ="<?=$row['title'];?>">
                                                    <div class="ant-image-mask">
                                                        <div class="ant-image-mask-info">
                                                            <span role="img" aria-label="eye" class="anticon anticon-eye">
                                                                <svg focusable="false" class="" data-icon="eye" width="1em" height="1em" fill="currentColor" aria-hidden="true" viewBox="64 64 896 896"><path d="M942.2 486.2C847.4 286.5 704.1 186 512 186c-192.2 0-335.4 100.5-430.2 300.3a60.3 60.3 0 000 51.5C176.6 737.5 319.9 838 512 838c192.2 0 335.4-100.5 430.2-300.3 7.7-16.2 7.7-35 0-51.5zM512 766c-161.3 0-279.4-81.8-362.7-254C232.6 339.8 350.7 258 512 258c161.3 0 279.4 81.8 362.7 254C791.5 684.2 673.4 766 512 766zm-4-430c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm0 288c-61.9 0-112-50.1-112-112s50.1-112 112-112 112 50.1 112 112-50.1 112-112 112z"></path></svg></span>
                                                                Xem to hơn
                                                            </div>
                                                        </div>
                                                    </a>
                                                
                                                </div>
                                            
                                                <div class="p-2">

                                                    <div class="grid grid-cols-10 mb-3 gap-10">

                                                    <div class="text-center w-full">
                                                        <a href="/mua-code/<?=$row['id'];?>"><h6 class="card-title news_title"><?=$row['title'];?></h6></a>
                                                    </div>


                                                    </div>
                                                    <div class="text-center grid grid-cols-2 gap-3">
                                                        <div class="col-span-2">
                                                            <div class="border border-red-500 rounded-lg p-1 font-bold"><i class="fa-solid fa-wallet"></i>   
                                                                <?php if($TUANORI->site('sukien') == 'ON' && $TUANORI->site('ptgiamgia') > 0 && $row['money'] > 0) { ?>
                                                                    <del style="color: red"><?=sotienmua($row['money']);?></del> - 
                                                                    <span class="text-green-600"><?=sotienmua($row['money'] - ($row['money']*$TUANORI->site('ptgiamgia')/100));?></span>
                                                                <?php } else { ?>
                                                                    <span class="text-green-600"><?=sotienmua($row['money']);?></span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <?php 
                                                        $truedz = false;
                                                        if(isset($_COOKIE['token'])) {
                                                            if($TUANORI->get_row(" SELECT * FROM `giohang` WHERE `id_code` = '".$row['id']."' AND `username` = '".$getUser['username']."' ") ) {
                                                                $truedz = true;
                                                            }
                                                        }
                                                        
                                                        if($truedz) { ?>
                                                            <button class="btn btn-sm btn-primary w-full"><i class="fa-sharp fa-regular fa-circle-check"></i> Đã thêm</span></button>
                                                        <?php } else { ?>
                                                            <button id="GioHang<?=$row['id'];?>" onclick="AddGioHang(<?=$row['id'];?>)" class="btn btn-sm btn-primary w-full"><i class="fas fa-shopping-cart me-2"></i> Cho vào giỏ</span></button>
                                                        <?php } ?>
                                                        <!---->
                                                        <a href="/mua-code/<?=$row['id'];?>"> <button class="btn btn-sm btn-dark  w-full"><i class="fas fa-shopping-cart me-2"></i><span>Chi Tiết</span>
                                                        </button></a>
                                                        <!---->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ant-ribbon ant-ribbon-placement-end ant-ribbon-color-black css-eq3tly"><span class="ant-ribbon-text">Mã: <?=$row['id'];?></span>
                                                <div class="ant-ribbon-corner"></div>
                                            </div>
                                        </div>
                                        <?php if($TUANORI->site('sukien') == 'ON' && $TUANORI->site('ptgiamgia') > 0) { ?>
                                        <div class="ant-ribbon ant-ribbon-placement-start ant-ribbon-color-red css-eq3tly"><span class="ant-ribbon-text">-<?=$TUANORI->site('ptgiamgia');?>%</span>
                                            <div class="ant-ribbon-corner"></div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    
                </section>


                <section class="section-product space-y-3">
                </section>


                <section class="section-product space-y-3">
                </section>
            </main>
        </div>
    </div>
</div>
</div>
<?php
    /*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
    require_once(__DIR__."/pages/client/Footer.php");
?>
