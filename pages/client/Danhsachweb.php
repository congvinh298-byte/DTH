
<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Mẫu Mã Website";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
?>

  
<?php
$id = check_string($_GET['id']);
$check = $DMH->get_row(" SELECT * FROM `danhmuctaoweb` WHERE `id` = '$id' ");
if(!$check)
{
    
    msg_error('Link bạn truy cập không đúng hoặc đã bị xóa', BASE_URL(''), 3000);
}
?>
<div class="content-wrapper transition-all duration-150 ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px]" id="content_wrapper">
    <div class="page-content">
        <div class="container-fluid transition-all duration-150" id="page_layout">
            <main id="content_layout">
                <!-- Page Content -->
                <div class="mb-3">
                </div>

                <section>
                    <div class="text-center mb-5">
                        <h1 class="text-[20px] md:text-[30px] mb-1">Sản phẩm: <?=$check['title'];?></h1>
                        <div class="h-[3px] bg-primary w-[170px] mx-auto"></div>
                    </div>
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
                    <div id="app" data-v-app="">
                        <section>

                            <div class="ant-spin-nested-loading css-eq3tly">
                                <!---->
                                <div class="ant-spin-container">
                                    <div class="ant-row css-eq3tly" style="margin-left: -9px; margin-right: -9px;"></div>
                                </div>
                            </div>
                            <div class="ant-row css-eq3tly" style="margin-left: -9px; margin-right: -9px;">
                                <?php foreach($DMH->get_list(" SELECT * FROM `danhsachtaoweb` WHERE `id_danhmuc` = '$id' AND `hienthi` = 'SHOW' ORDER BY id DESC") as $row){ ?>
                                    <div class="ant-col ant-col-xs-24 ant-col-md-8 ant-col-lg-6 mb-2 mt-2 cursor-pointer css-eq3tly" style="padding-left: 9px; padding-right: 9px;">
                                        <div class="ant-ribbon-wrapper css-eq3tly">
                                            <div class="ant-ribbon-wrapper css-eq3tly">
                                                <div class="border border-primary rounded-lg p-[1px]" style="background: transparent;">
                                                    <div class="ant-image css-eq3tly" style="width: 100%;">
                                                    <!-- <img alt="<?=$row['title'];?>" class="ant-image-img rounded-t-lg" src="<?=$row['img'];?>"> -->
                                                    <img src="/images/svg/spinner.svg" data-src="<?=$row['img'];?>" class="lazyload w-full lg:h-[180px] rounded-t-lg object-fill" alt="<?=$row['title'];?>" />
                                                        <!---->
                                                        <a href="<?=$row['img'];?>" class="glightbox" title ="<?=$row['title'];?>">
                                                            <div class="ant-image-mask">
                                                                <div class="ant-image-mask-info">
                                                                    <span role="img" aria-label="eye" class="anticon anticon-eye"><svg focusable="false" class="" data-icon="eye" width="1em" height="1em" fill="currentColor" aria-hidden="true" viewBox="64 64 896 896"><path d="M942.2 486.2C847.4 286.5 704.1 186 512 186c-192.2 0-335.4 100.5-430.2 300.3a60.3 60.3 0 000 51.5C176.6 737.5 319.9 838 512 838c192.2 0 335.4-100.5 430.2-300.3 7.7-16.2 7.7-35 0-51.5zM512 766c-161.3 0-279.4-81.8-362.7-254C232.6 339.8 350.7 258 512 258c161.3 0 279.4 81.8 362.7 254C791.5 684.2 673.4 766 512 766zm-4-430c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm0 288c-61.9 0-112-50.1-112-112s50.1-112 112-112 112 50.1 112 112-50.1 112-112 112z"></path></svg></span>Xem to hơn
                                                                </div>
                                                            </div>
                                                        </a>
                                                        
                                                        
                                                    </div>
                                                    <!---->

                                                    <div class="p-2">

                                                        <div class="grid grid-cols-10 mb-3 gap-10">

                                                        <div class="text-center w-full">
                                                            <a href="/tao-web/<?=$row['id'];?>"><h6 class="card-title news_title"><?=$row['title'];?></h6></a>
                                                        </div>


                                                        </div>
                                                        <div class="text-center grid grid-cols-2 gap-3">
                                                            <div class="col-span-2">
                                                                <div class="border border-red-500 rounded-lg p-1 font-bold"><i class="fa-solid fa-wallet"></i> 
                                                                    <?php if($DMH->site('sukien') == 'ON' && $DMH->site('ptgiamgiaweb') > 0 && $row['money'] > 0) { ?>
                                                                        <del style="color: red"><?=sotienmua($row['money']);?></del> - 
                                                                        <span class="text-green-600"><?=sotienmua($row['money'] - ($row['money']*$DMH->site('ptgiamgiaweb')/100));?></span>
                                                                    <?php } else { ?>
                                                                        <span class="text-green-600"><?=sotienmua($row['money']);?></span>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <a target="_blank" href="<?=$row['demo'];?>">
                                                                <button class="btn btn-sm btn-primary w-full"><i class="fas fa-eye "> </i><span> Xem DEMO</span></button>
                                                            </a>
                                                            <!---->
                                                            <a href="/tao-web/<?=$row['id'];?>">
                                                                <button class="btn btn-sm btn-dark w-full"><i class="fas fa-shopping-cart me-2"></i><span>Mua Ngay</span></button>
                                                            </a>
                                                            <!---->
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ant-ribbon ant-ribbon-placement-end ant-ribbon-color-black css-eq3tly"><span class="ant-ribbon-text">Mã: <?=$row['id'];?></span>
                                                   <div class="ant-ribbon-corner"></div>
                                                </div>
                                            </div>
                                            <?php if($DMH->site('sukien') == 'ON' && $DMH->site('ptgiamgiaweb') > 0 && $row['money'] > 0) { ?>
                                            <div class="ant-ribbon ant-ribbon-placement-start ant-ribbon-color-red css-eq3tly"><span class="ant-ribbon-text">-<?=$DMH->site('ptgiamgiaweb');?>%</span>
                                                <div class="ant-ribbon-corner"></div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>


                        </section>
                    </div>

                </section>
            </main>
        </div>
    </div>
</div>
</div>
<?php

require_once("../../pages/client/Footer.php");
?>