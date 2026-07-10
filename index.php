<?php

define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");
$title = "Điện Máy Hiệu - Hệ Thống Quản Lý & Cửa Hàng";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");
?>

<div class="content-wrapper transition-all duration-300 ease-in-out ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px] bg-gray-50 min-h-screen" id="content_wrapper">
    <div class="page-content p-4 md:p-8">
        <div class="container-fluid mx-auto max-w-7xl" id="page_layout">
            <main id="content_layout" class="space-y-12">
                
                <!-- HERO SECTION (ADHD: Big, Bold, High-Energy) -->
                <section class="relative bg-gradient-to-r from-gray-900 to-black text-white rounded-3xl p-8 md:p-16 shadow-2xl overflow-hidden flex flex-col items-center text-center transform hover:scale-[1.01] transition-transform duration-300">
                    <div class="absolute inset-0 bg-blue-500 opacity-20 blur-3xl rounded-full w-96 h-96 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"></div>
                    <div class="relative z-10">
                        <img src="/public/assets/logo.png" alt="Điện Máy Hiệu" class="max-h-24 md:max-h-32 mx-auto mb-6 drop-shadow-lg" />
                        <h1 class="text-4xl md:text-6xl font-extrabold uppercase tracking-tighter mb-4 text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">CÔNG TY TNHH MTV ĐIỆN TỬ HIẾU</h1>
                        <p class="text-xl md:text-2xl font-light tracking-wide text-gray-300 uppercase">Hệ Thống Quản Lý & Dịch Vụ Toàn Diện</p>
                    </div>
                </section>

                <!-- Bot_EMH Integration -->
                <div id="bot-emh-container">
                     <script src="/bot_emh/public/js/bot_emh_widget.js" async></script>
                </div>

                <!-- ALERTS & BANNERS (OCD: Clean layout, strict padding) -->
                <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="col-span-1 lg:col-span-2 overflow-hidden rounded-2xl shadow-xl">
                        <img src="<?=$DMH->site('banner1');?>" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" alt="Banner" />
                    </div>
                    
                    <div class="col-span-1 flex flex-col gap-6">
                        <div class="bg-white rounded-2xl p-6 shadow-xl border-l-4 border-blue-500 flex flex-col justify-center h-full">
                            <h2 class="text-2xl font-black uppercase text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fa fa-bell text-blue-500"></i> Thông Báo
                            </h2>
                            <div class="prose text-gray-600 font-medium leading-relaxed">
                                <?=$DMH->site('thongbao');?>
                            </div>
                        </div>
                    </div>
                </section>

                <?php if($DMH->site('sukien') == 'ON') { ?>
                <section class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl p-6 md:p-8 shadow-2xl text-white transform hover:-translate-y-1 transition-transform">
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <img src="https://imgur.com/56EFXz7.gif" class="w-16 h-16 md:w-24 md:h-24 rounded-full shadow-lg" alt="Khuyến mãi" />
                        <div class="flex-1 space-y-2">
                            <h2 class="text-3xl font-black uppercase tracking-tight">Sự Kiện Đang Diễn Ra!</h2>
                            <?php if($DMH->site('khuyenmai') > 0) { ?>
                            <p class="text-lg font-bold">🔥 +<?=$DMH->site('ptgiamgia');?>% Giá trị nạp tiền (ATM/Momo/Thesieure)</p>
                            <?php } if($DMH->site('ptgiamgiaweb') > 0) { ?>
                            <p class="text-lg font-bold">🚀 Giảm <?=$DMH->site('ptgiamgiaweb');?>% khi TẠO WEBSITE</p>
                            <?php } if($DMH->site('ptgiamgia') > 0) { ?>
                            <p class="text-lg font-bold">⚡ Giảm <?=$DMH->site('ptgiamgia');?>% khi MUA CODE</p>
                            <?php } ?>
                        </div>
                    </div>
                </section>
                <?php } ?>

                <!-- DỊCH VỤ CỦA CHÚNG TÔI (ADHD: Dynamic grid, hover effects) -->
                <section class="space-y-8">
                    <div class="text-center">
                        <h1 class="text-4xl md:text-5xl font-black uppercase tracking-tighter text-gray-900">Dịch Vụ Nổi Bật</h1>
                        <div class="h-2 w-24 bg-blue-600 mx-auto mt-4 rounded-full"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Service 1 -->
                        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col h-full border border-gray-100 relative">
                            <div class="absolute top-4 left-4 bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase z-10 shadow">Bảo hành trọn đời</div>
                            <div class="h-48 overflow-hidden">
                                <img src="/images/svg/spinner.svg" data-src="<?=$DMH->site('danhmuc1');?>" class="lazyload w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Tạo Trang Web" />
                            </div>
                            <div class="p-6 flex flex-col flex-1">
                                <h2 class="text-2xl font-extrabold text-gray-900 mb-2">Tạo Trang Web</h2>
                                <p class="text-gray-500 font-medium mb-6 flex-1">Nhanh gọn, chuyên nghiệp, tối ưu SEO.</p>
                                <a href="/tao-trang-web" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-bold py-3 px-4 rounded-xl transition-colors uppercase tracking-wide">Xem Ngay <i class="fa fa-arrow-right ml-2"></i></a>
                            </div>
                        </div>

                        <!-- Service 2 -->
                        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col h-full border border-gray-100">
                            <div class="h-48 overflow-hidden">
                                <img src="/images/svg/spinner.svg" data-src="<?=$DMH->site('danhmuc2');?>" class="lazyload w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Mã Nguồn" />
                            </div>
                            <div class="p-6 flex flex-col flex-1">
                                <h2 class="text-2xl font-extrabold text-gray-900 mb-2">Mã Nguồn</h2>
                                <p class="text-gray-500 font-medium mb-6 flex-1">Hệ thống script đa dạng, chất lượng cao.</p>
                                <a href="/mua-source-code" class="block w-full bg-gray-900 hover:bg-black text-white text-center font-bold py-3 px-4 rounded-xl transition-colors uppercase tracking-wide">Khám Phá <i class="fa fa-code ml-2"></i></a>
                            </div>
                        </div>

                        <!-- Service 3 -->
                        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col h-full border border-gray-100">
                            <div class="h-48 overflow-hidden">
                                <img src="/images/svg/spinner.svg" data-src="<?=$DMH->site('danhmuc3');?>" class="lazyload w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Đăng Ký Tên Miền" />
                            </div>
                            <div class="p-6 flex flex-col flex-1">
                                <h2 class="text-2xl font-extrabold text-gray-900 mb-2">Đăng Ký Tên Miền</h2>
                                <p class="text-gray-500 font-medium mb-6 flex-1">Khẳng định thương hiệu độc quyền của bạn.</p>
                                <a href="/Mua-mien" class="block w-full bg-green-500 hover:bg-green-600 text-white text-center font-bold py-3 px-4 rounded-xl transition-colors uppercase tracking-wide">Đăng Ký <i class="fa fa-globe ml-2"></i></a>
                            </div>
                        </div>

                        <!-- Service 4 -->
                        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col h-full border border-gray-100">
                            <div class="h-48 overflow-hidden">
                                <img src="/images/svg/spinner.svg" data-src="<?=$DMH->site('danhmuc4');?>" class="lazyload w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Upload Ảnh" />
                            </div>
                            <div class="p-6 flex flex-col flex-1">
                                <h2 class="text-2xl font-extrabold text-gray-900 mb-2">Lưu Trữ Hình Ảnh</h2>
                                <p class="text-gray-500 font-medium mb-6 flex-1">Upload tốc độ cao, link trực tiếp trọn đời.</p>
                                <a href="/upanh" class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center font-bold py-3 px-4 rounded-xl transition-colors uppercase tracking-wide">Tải Lên <i class="fa fa-cloud-upload ml-2"></i></a>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- SẢN PHẨM MỚI NHẤT (OCD: Clean grid, no garbage divs) -->
                <section class="space-y-8">
                    <div class="text-center">
                        <h1 class="text-4xl md:text-5xl font-black uppercase tracking-tighter text-gray-900">Sản Phẩm Mới Nhất</h1>
                        <div class="h-2 w-24 bg-purple-600 mx-auto mt-4 rounded-full"></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        <?php foreach($DMH->get_list(" SELECT * FROM `danhsachmuacode` WHERE `hienthi` = 'SHOW' AND `money` > 0 ORDER BY id DESC LIMIT 16") as $row){ ?>
                        <div class="group bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col relative border border-gray-100">
                            
                            <?php if($DMH->site('sukien') == 'ON' && $DMH->site('ptgiamgia') > 0) { ?>
                            <div class="absolute top-3 right-3 bg-red-600 text-white text-xs font-black px-2 py-1 rounded-md z-10 shadow-lg">
                                -<?=$DMH->site('ptgiamgia');?>%
                            </div>
                            <?php } ?>

                            <div class="relative h-48 overflow-hidden bg-gray-100">
                                <img src="/images/svg/spinner.svg" data-src="<?=$row['img'];?>" class="lazyload w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="<?=$row['title'];?>" />
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <a href="<?=$row['img'];?>" class="glightbox bg-white text-gray-900 rounded-full w-12 h-12 flex items-center justify-center shadow-xl hover:bg-blue-600 hover:text-white transition-colors" title="<?=$row['title'];?>">
                                        <i class="fa fa-eye text-xl"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="p-5 flex flex-col flex-1">
                                <a href="/mua-code/<?=$row['id'];?>" class="text-lg font-bold text-gray-900 hover:text-blue-600 line-clamp-2 leading-tight mb-3 flex-1 transition-colors">
                                    <?=$row['title'];?>
                                </a>
                                
                                <div class="flex items-center justify-between mb-4 bg-gray-50 p-2 rounded-lg border border-gray-100">
                                    <span class="text-sm font-bold text-gray-500">MÃ: #<?=$row['id'];?></span>
                                    <div class="text-right">
                                        <?php if($DMH->site('sukien') == 'ON' && $DMH->site('ptgiamgia') > 0 && $row['money'] > 0) { ?>
                                            <div class="text-xs text-gray-400 line-through"><?=sotienmua($row['money']);?></div>
                                            <div class="text-lg font-black text-red-600"><?=sotienmua($row['money'] - ($row['money']*$DMH->site('ptgiamgia')/100));?></div>
                                        <?php } else { ?>
                                            <div class="text-lg font-black text-green-600"><?=sotienmua($row['money']);?></div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <?php 
                                    $truedz = false;
                                    if(isset($_COOKIE['token'])) {
                                        if($DMH->get_row(" SELECT * FROM `giohang` WHERE `id_code` = '".$row['id']."' AND `username` = '".$getUser['username']."' ") ) {
                                            $truedz = true;
                                        }
                                    }
                                    if($truedz) { ?>
                                        <button class="bg-gray-200 text-gray-600 font-bold py-2 rounded-lg text-sm w-full cursor-not-allowed">
                                            <i class="fa fa-check-circle"></i> Đã Thêm
                                        </button>
                                    <?php } else { ?>
                                        <button id="GioHang<?=$row['id'];?>" onclick="AddGioHang(<?=$row['id'];?>)" class="bg-blue-100 hover:bg-blue-600 text-blue-600 hover:text-white font-bold py-2 rounded-lg text-sm w-full transition-colors">
                                            <i class="fa fa-cart-plus"></i> Giỏ Hàng
                                        </button>
                                    <?php } ?>
                                    <a href="/mua-code/<?=$row['id'];?>" class="bg-gray-900 hover:bg-black text-white text-center font-bold py-2 rounded-lg text-sm w-full transition-colors">
                                        Chi Tiết
                                    </a>
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

<?php
    
    require_once(__DIR__."/pages/client/Footer.php");
?>
