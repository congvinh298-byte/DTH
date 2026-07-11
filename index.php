<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");
$title = "Điện Máy Hiếu - Bán lẻ, Sửa chữa & In 3D Chuyên Nghiệp";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");
?>

<style>
    /* BẢO HIỂM: Nhúng Tailwind trực tiếp đề phòng sếp quên upload Head.php */
    @import url('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
    
    :root {
        --dmh-dark: #0a0f1d;
        --neon-cyan: #00f3ff;
        --neon-orange: #ff5e00;
    }
    
    body {
        background-color: #f3f4f6;
        font-family: 'Inter', sans-serif;
    }

    .hero-gradient {
        background: linear-gradient(135deg, var(--dmh-dark) 0%, #172136 100%);
    }

    .glass-panel {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
    }

    .btn-adhd {
        background: linear-gradient(90deg, #ff5e00, #ff1361);
        box-shadow: 0 4px 15px rgba(255, 94, 0, 0.4);
        transition: all 0.3s ease;
    }
    .btn-adhd:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(255, 94, 0, 0.6);
    }

    /* OCD Compact List */
    .compact-item {
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
    }
    .compact-item:hover {
        background-color: #f8fafc;
        border-left-color: var(--neon-cyan);
        transform: translateX(4px);
    }
</style>

<!-- HERO SECTION -->
<div class="hero-gradient relative w-full pt-16 pb-20 overflow-hidden">
    <!-- Abstract Shapes -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-screen filter blur-[100px] opacity-20 transform translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-orange-500 rounded-full mix-blend-screen filter blur-[100px] opacity-20 transform -translate-x-1/2 translate-y-1/2"></div>

    <div class="container mx-auto px-4 max-w-6xl relative z-10 flex flex-col md:flex-row items-center gap-10">
        <div class="w-full md:w-1/2 text-center md:text-left">
            <div class="inline-block px-4 py-1 rounded-full bg-blue-500/20 text-cyan-400 font-bold text-xs uppercase tracking-widest mb-6 border border-blue-400/30">
                <i class="fa fa-check-circle mr-1"></i> Đối tác Điện Máy Uy Tín
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-6 uppercase tracking-tight">
                Giải pháp <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">Toàn Diện</span><br/>
                Cho Ngôi Nhà <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-red-500">Của Bạn</span>
            </h1>
            <p class="text-gray-300 text-lg mb-8 font-medium max-w-xl mx-auto md:mx-0">
                Phân phối điện máy chính hãng - Cứu hộ kỹ thuật siêu tốc 30 phút - Thiết kế & In 3D độc quyền.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                <a href="#dat-lich" onclick="document.getElementById('dat-lich').scrollIntoView({behavior: 'smooth'}); return false;" class="btn-adhd text-white font-black px-8 py-4 rounded-xl uppercase tracking-wider flex items-center justify-center">
                    <i class="fa fa-tools mr-2"></i> Gọi Thợ Khẩn Cấp
                </a>
                <a href="#bang-gia" onclick="document.getElementById('bang-gia').scrollIntoView({behavior: 'smooth'}); return false;" class="bg-white/10 hover:bg-white/20 border border-white/20 text-white font-black px-8 py-4 rounded-xl uppercase tracking-wider transition-all flex items-center justify-center">
                    Xem Bảng Giá <i class="fa fa-arrow-down ml-2"></i>
                </a>
            </div>
        </div>
        <div class="w-full md:w-1/2 flex justify-center mt-10 md:mt-0">
            <div class="relative bg-white p-2 rounded-3xl shadow-[0_0_40px_rgba(0,243,255,0.3)] transform hover:rotate-2 transition-transform duration-500 max-w-[300px] w-full">
                <img src="/logo.png" alt="Điện Máy Hiếu Logo" class="w-full rounded-2xl border border-gray-100 bg-white" />
            </div>
        </div>
    </div>
</div>

<!-- COMPACT PRICE LIST (OCD) -->
<div id="bang-gia" class="container mx-auto px-4 max-w-5xl py-20">
    <div class="flex flex-col md:flex-row justify-between items-end mb-10 border-b-2 border-gray-200 pb-4">
        <div>
            <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Bảng Giá <span class="text-blue-600">Dịch Vụ & Sản Phẩm</span></h2>
            <p class="text-gray-500 mt-2 font-medium">Báo giá minh bạch, chuẩn xác, không phí ẩn.</p>
        </div>
        <a href="/dien-may" class="hidden md:inline-flex items-center font-bold text-orange-600 hover:text-orange-700 uppercase text-sm tracking-wider">
            Xem Tất Cả <i class="fa fa-chevron-right ml-2 text-xs"></i>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="hidden md:grid grid-cols-12 gap-4 bg-gray-50 p-4 border-b border-gray-200 text-xs font-black text-gray-500 uppercase tracking-wider">
            <div class="col-span-1 text-center">Mã</div>
            <div class="col-span-2">Hình Ảnh</div>
            <div class="col-span-5">Tên Sản Phẩm / Dịch Vụ</div>
            <div class="col-span-2 text-right">Mức Giá</div>
            <div class="col-span-2 text-center">Thao Tác</div>
        </div>
        
        <div class="divide-y divide-gray-100">
            <?php foreach($DMH->get_list(" SELECT * FROM `danhsachmuacode` WHERE `hienthi` = 'SHOW' AND `money` > 0 ORDER BY id DESC LIMIT 8") as $row){ 
                $price = $row['money'];
                $finalPrice = $price;
                if($DMH->site('sukien') == 'ON' && $DMH->site('ptgiamgia') > 0) {
                    $finalPrice = $price - ($price * $DMH->site('ptgiamgia') / 100);
                }
            ?>
            <div class="compact-item grid grid-cols-1 md:grid-cols-12 gap-4 items-center p-4">
                <div class="col-span-1 text-center hidden md:block text-gray-400 font-bold text-sm">#<?=$row['id'];?></div>
                <div class="col-span-12 md:col-span-2 flex justify-center md:justify-start">
                    <div class="w-20 h-20 bg-gray-50 rounded-lg p-2 border border-gray-100 flex items-center justify-center">
                        <img src="<?=$row['img'];?>" class="max-h-full max-w-full object-contain" alt="<?=$row['title'];?>" />
                    </div>
                </div>
                <div class="col-span-12 md:col-span-5 text-center md:text-left">
                    <h4 class="text-base font-black text-gray-900 line-clamp-2"><?=$row['title'];?></h4>
                    <?php if($DMH->site('sukien') == 'ON' && $DMH->site('ptgiamgia') > 0) { ?>
                        <span class="inline-block mt-1 bg-red-100 text-red-600 text-[10px] font-black px-2 py-0.5 rounded uppercase">Giảm <?=$DMH->site('ptgiamgia');?>%</span>
                    <?php } ?>
                </div>
                <div class="col-span-12 md:col-span-2 text-center md:text-right">
                    <?php if($price != $finalPrice) { ?>
                        <div class="text-xs text-gray-400 line-through font-bold"><?=sotienmua($price);?></div>
                    <?php } ?>
                    <div class="text-lg font-black text-blue-600"><?=sotienmua($finalPrice);?></div>
                </div>
                <div class="col-span-12 md:col-span-2 flex justify-center gap-2">
                    <a href="/mua-code/<?=$row['id'];?>" class="bg-gray-900 text-white hover:bg-blue-600 text-xs font-black py-2 px-4 rounded-lg transition-colors uppercase">
                        <i class="fa fa-shopping-cart"></i> Mua
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    
    <div class="mt-6 text-center md:hidden">
        <a href="/dien-may" class="inline-block bg-gray-100 text-gray-900 font-black px-6 py-3 rounded-xl uppercase text-sm">
            Xem Tất Cả Sản Phẩm
        </a>
    </div>
</div>

<!-- BOOKING SECTION (GLASSMORPHISM) -->
<div id="dat-lich" class="relative py-24 bg-gray-900">
    <!-- Background image or pattern -->
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 30px 30px;"></div>
    
    <div class="container mx-auto px-4 max-w-4xl relative z-10">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-black text-white uppercase tracking-tight mb-4">Khởi Tạo <span class="text-orange-500">Yêu Cầu Dịch Vụ</span></h2>
            <p class="text-gray-400 font-medium max-w-xl mx-auto">Điền thông tin bên dưới để kỹ thuật viên của chúng tôi nắm bắt tình hình và có mặt xử lý ngay lập tức.</p>
        </div>

        <div class="glass-panel p-8 md:p-10 rounded-[30px]">
            <div id="thongbao_datlich" class="mb-6"></div>
            <form id="formDatLich" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-gray-600 uppercase tracking-wider mb-2">Họ và Tên *</label>
                        <input type="text" id="dl_ten" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 font-bold focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all" placeholder="Nhập tên của bạn" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-600 uppercase tracking-wider mb-2">Số Điện Thoại *</label>
                        <input type="tel" id="dl_sdt" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 font-bold focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all" placeholder="09xx.xxx.xxx" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-600 uppercase tracking-wider mb-2">Dịch Vụ Cần Xử Lý *</label>
                    <select id="dl_dichvu" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 font-bold focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all cursor-pointer">
                        <option value="Sửa Máy Lạnh">Sửa chữa / Vệ sinh Máy Lạnh</option>
                        <option value="Sửa Tủ Lạnh">Sửa chữa Tủ Lạnh</option>
                        <option value="Sửa Máy Giặt">Sửa chữa Máy Giặt</option>
                        <option value="Sửa Tivi">Sửa chữa Tivi</option>
                        <option value="In 3D">Đặt thiết kế / In 3D</option>
                        <option value="Khác">Dịch vụ khác...</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-600 uppercase tracking-wider mb-2">Địa Chỉ Giao Dịch *</label>
                    <input type="text" id="dl_diachi" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 font-bold focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all mb-3" placeholder="Số nhà, Tên đường, Xã/Phường, Huyện/Tỉnh" required>
                    
                    <!-- Geolocation Button -->
                    <div class="flex flex-col sm:flex-row items-center justify-between bg-blue-50 p-3 rounded-xl border border-blue-100">
                        <div id="location_status" class="text-sm font-bold text-gray-600 flex items-center mb-2 sm:mb-0">
                            <i class="fa fa-map-marker-alt text-blue-400 mr-2 text-lg"></i> Chưa định vị tọa độ
                        </div>
                        <button type="button" onclick="getLocation()" class="bg-blue-600 text-white hover:bg-blue-700 font-bold py-2 px-4 rounded-lg transition-colors text-sm flex items-center w-full sm:w-auto justify-center">
                            <i class="fa fa-location-arrow mr-2"></i> Chia sẻ vị trí GPS
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-600 uppercase tracking-wider mb-2">Mô Tả Chi Tiết (Không Bắt Buộc)</label>
                    <textarea id="dl_yeucau" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 font-bold focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all resize-none" placeholder="Tình trạng thiết bị hiện tại..."></textarea>
                </div>

                <button type="button" id="btnDatLich" class="w-full btn-adhd text-white font-black text-lg py-4 rounded-xl flex items-center justify-center gap-3 uppercase">
                    <i class="fa fa-paper-plane"></i> Chốt Đơn & Gọi Thợ
                </button>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    let userLat = "";
    let userLng = "";

    function getLocation() {
        $("#location_status").html('<span class="text-blue-600 font-bold"><i class="fa fa-spinner fa-spin mr-2"></i> Đang lấy tọa độ...</span>');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;
                $("#location_status").html('<span class="text-green-600 font-black"><i class="fa fa-check-circle mr-2"></i> Định vị thành công!</span>');
            }, function(error) {
                $("#location_status").html('<span class="text-red-500 font-bold"><i class="fa fa-exclamation-triangle mr-2"></i> Không thể lấy vị trí.</span>');
            });
        } else {
            $("#location_status").html('<span class="text-red-500 font-bold">Trình duyệt không hỗ trợ.</span>');
        }
    }

    $("#btnDatLich").on("click", function() {
        $('#btnDatLich').html('<i class="fa fa-spinner fa-spin mr-2"></i> ĐANG XỬ LÝ...').prop('disabled', true).css('opacity', '0.7');
        
        let diaChiGui = $("#dl_diachi").val();
        if(userLat !== "" && userLng !== "") {
            diaChiGui += " | Tọa độ GPS: " + userLat + ", " + userLng + " (https://maps.google.com/?q="+userLat+","+userLng+")";
        }

        $.ajax({
            url: "<?=BASE_URL('controller/client/DatLich.php');?>",
            method: "POST",
            data: {
                type: 'DatLich',
                ten: $("#dl_ten").val(),
                sdt: $("#dl_sdt").val(),
                dichvu: $("#dl_dichvu").val(),
                diachi: diaChiGui,
                yeucau: $("#dl_yeucau").val()
            },
            success: function(response) {
                $("#thongbao_datlich").html(response);
                $('#btnDatLich').html('<i class="fa fa-paper-plane"></i> Chốt Đơn & Gọi Thợ').prop('disabled', false).css('opacity', '1');
            },
            error: function() {
                alert("Lỗi kết nối! Gọi trực tiếp hotline.");
                $('#btnDatLich').html('<i class="fa fa-paper-plane"></i> Chốt Đơn & Gọi Thợ').prop('disabled', false).css('opacity', '1');
            }
        });
    });
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
