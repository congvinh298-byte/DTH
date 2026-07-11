<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");
$title = "Điện Máy Hiếu - Bán lẻ, Sửa chữa & In 3D Chuyên Nghiệp";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");
?>

<div class="content-wrapper transition-all duration-300 ease-in-out bg-gray-50 min-h-screen" id="content_wrapper">
    <div class="page-content py-8">
        
        <!-- HERO SECTION (ADHD: Big, Bold, High-Energy) -->
        <div class="container mx-auto px-4 max-w-7xl mb-12">
            <section class="relative bg-gradient-to-r from-gray-900 to-black text-white rounded-3xl p-8 md:p-16 shadow-2xl overflow-hidden flex flex-col items-center text-center">
                <div class="absolute inset-0 bg-blue-600 opacity-20 blur-3xl rounded-full w-96 h-96 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"></div>
                <div class="relative z-10">
                    <img src="<?=$DMH->site('logo');?>" alt="Điện Máy Hiếu" class="max-h-24 md:max-h-32 mx-auto mb-6 drop-shadow-lg filter brightness-0 invert" />
                    <h1 class="text-4xl md:text-6xl font-black uppercase tracking-tighter mb-4 text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-blue-200">
                        ĐIỆN MÁY HIẾU
                    </h1>
                    <p class="text-xl md:text-2xl font-bold tracking-wide text-orange-400 uppercase mb-8">
                        UY TÍN - TẬN TÂM - CHUYÊN NGHIỆP
                    </p>
                    <a href="tel:09xxxxxxx" class="btn-cta-pulse text-xl">
                        <i class="fa fa-phone-alt"></i> GỌI THỢ NGAY: 09xx.xxx.xxx
                    </a>
                </div>
            </section>
        </div>

        <!-- THE 3 PILLARS (OCD Layout, ADHD Hover) -->
        <div class="container mx-auto max-w-7xl">
            <h2 class="section-title">Hệ Sinh Thái Điện Máy Hiếu</h2>
            <p class="section-subtitle">Chuyên phân phối điện máy, khắc phục sự cố tại nhà và cung cấp giải pháp công nghệ In 3D hiện đại nhất tại Lấp Vò, Đồng Tháp.</p>
            
            <div class="grid-3-pillars">
                
                <!-- Pillar 1: Điện Máy & Gia Dụng -->
                <div class="pillar-card group">
                    <div class="pillar-icon-wrapper">
                        <i class="fa fa-tv"></i>
                    </div>
                    <h3 class="text-2xl font-black uppercase text-gray-900 mb-4">Điện Máy & Gia Dụng</h3>
                    <p class="text-gray-600 leading-relaxed mb-8 flex-1">
                        Cung cấp Tivi, Tủ Lạnh, Máy Giặt, Điều Hòa và các thiết bị gia dụng chính hãng. Bảo hành dài hạn, lắp đặt tận nơi miễn phí.
                    </p>
                    <a href="/dien-may" class="inline-block bg-gray-900 text-white font-bold px-8 py-3 rounded-full hover:bg-blue-600 transition-colors uppercase w-full">
                        Xem Sản Phẩm
                    </a>
                </div>

                <!-- Pillar 2: Sửa Chữa Tận Nhà (The Hero Service) -->
                <div class="pillar-card group border-2 border-orange-500 shadow-xl relative overflow-visible transform md:-translate-y-4">
                    <div class="absolute -top-4 bg-orange-500 text-white font-black px-6 py-1 rounded-full uppercase text-sm tracking-wider shadow-lg">
                        Dịch Vụ Mũi Nhọn
                    </div>
                    <div class="pillar-icon-wrapper bg-orange-100 text-orange-600 group-hover:bg-orange-600">
                        <i class="fa fa-tools"></i>
                    </div>
                    <h3 class="text-2xl font-black uppercase text-gray-900 mb-4">Gọi Thợ Tại Nhà</h3>
                    <p class="text-gray-600 leading-relaxed mb-8 flex-1">
                        Đội ngũ kỹ thuật viên dày dặn kinh nghiệm. Có mặt sau 30 phút tại khu vực Lấp Vò. Khắc phục triệt để mọi sự cố điện lạnh, điện tử.
                    </p>
                    <a href="tel:09xxxxxxx" class="btn-cta-pulse w-full text-sm">
                        <i class="fa fa-bolt"></i> Cứu Hộ Ngay
                    </a>
                </div>

                <!-- Pillar 3: In 3D -->
                <div class="pillar-card group">
                    <div class="pillar-icon-wrapper">
                        <i class="fa fa-cube"></i>
                    </div>
                    <h3 class="text-2xl font-black uppercase text-gray-900 mb-4">Công Nghệ In 3D</h3>
                    <p class="text-gray-600 leading-relaxed mb-8 flex-1">
                        Thiết kế, chế tạo chi tiết nhựa, linh kiện thay thế độc bản bằng công nghệ In 3D tiên tiến. Đảm bảo độ bền cao và độ chính xác tuyệt đối.
                    </p>
                    <a href="/in-3d" class="inline-block bg-gray-900 text-white font-bold px-8 py-3 rounded-full hover:bg-blue-600 transition-colors uppercase w-full">
                        Khám Phá In 3D
                    </a>
                </div>

            </div>
        </div>

        <!-- BRAND PROMISES -->
        <div class="bg-white border-y border-gray-200 mt-16 py-16">
            <div class="container mx-auto max-w-7xl px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <i class="fa fa-tachometer-alt text-4xl text-blue-600 mb-4"></i>
                        <h4 class="text-xl font-bold uppercase text-gray-900 mb-2">Tốc Độ</h4>
                        <p class="text-gray-500">Có mặt nhanh chóng, xử lý dứt điểm trong ngày.</p>
                    </div>
                    <div>
                        <i class="fa fa-shield-alt text-4xl text-blue-600 mb-4"></i>
                        <h4 class="text-xl font-bold uppercase text-gray-900 mb-2">Bảo Hành</h4>
                        <p class="text-gray-500">Cam kết bảo hành dài hạn cho mọi thiết bị.</p>
                    </div>
                    <div>
                        <i class="fa fa-hand-holding-usd text-4xl text-blue-600 mb-4"></i>
                        <h4 class="text-xl font-bold uppercase text-gray-900 mb-2">Giá Tốt</h4>
                        <p class="text-gray-500">Minh bạch chi phí, không phát sinh bất thường.</p>
                    </div>
                    <div>
                        <i class="fa fa-star text-4xl text-blue-600 mb-4"></i>
                        <h4 class="text-xl font-bold uppercase text-gray-900 mb-2">Chất Lượng</h4>
                        <p class="text-gray-500">Linh kiện chính hãng, thợ lành nghề chuẩn 5 sao.</p>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
