<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");
$title = "Điện Máy Hiếu - Bán lẻ, Sửa chữa & In 3D Chuyên Nghiệp";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");
?>

<!-- ADHD + OCD CSS -->
<style>
    :root {
        --dmh-dark: #070b14;
        --neon-cyan: #00f3ff;
        --neon-pink: #ff00ea;
        --neon-orange: #ff5e00;
        --neon-green: #39ff14;
    }
    .hero-bg {
        background: radial-gradient(circle at center, #1a1525 0%, var(--dmh-dark) 100%);
        position: relative;
    }
    .text-neon {
        text-shadow: 0 0 10px var(--neon-cyan), 0 0 20px var(--neon-cyan), 0 0 40px var(--neon-cyan);
    }
    .text-neon-orange {
        text-shadow: 0 0 10px var(--neon-orange), 0 0 20px var(--neon-orange);
    }
    .pulse-btn {
        animation: pulseBtn 1.5s infinite;
    }
    @keyframes pulseBtn {
        0% { box-shadow: 0 0 0 0 rgba(255, 94, 0, 0.7); }
        70% { box-shadow: 0 0 0 20px rgba(255, 94, 0, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 94, 0, 0); }
    }
    .card-hover {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    .card-hover:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0, 243, 255, 0.2), 0 10px 10px rgba(255, 0, 234, 0.1);
        border-color: var(--neon-cyan);
    }
    .form-input {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .form-input:focus {
        transform: scale(1.02);
        border-color: var(--neon-orange);
        box-shadow: 0 0 15px rgba(255, 94, 0, 0.3);
    }
    .smooth-scroll {
        scroll-behavior: smooth;
    }
    .logo-container {
        width: 120px;
        height: 120px;
        border-radius: 20px;
        padding: 5px;
        background: linear-gradient(45deg, var(--neon-cyan), var(--neon-pink));
        box-shadow: 0 0 20px rgba(0,243,255,0.5);
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .logo-container img {
        border-radius: 15px;
        background-color: white;
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
</style>

<div class="content-wrapper bg-gray-50 min-h-screen smooth-scroll" id="content_wrapper">
    <div class="page-content pb-12">
        
        <!-- HERO SECTION -->
        <div class="relative w-full hero-bg pt-20 pb-24 md:pt-32 md:pb-40 overflow-hidden mb-16">
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-32 -left-32 w-96 h-96 bg-cyan-500 rounded-full mix-blend-screen filter blur-[120px] opacity-30"></div>
                <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-pink-500 rounded-full mix-blend-screen filter blur-[120px] opacity-30"></div>
            </div>
            
            <div class="container mx-auto px-4 relative z-10 text-center">
                <!-- OCD LOGO -->
                <div class="logo-container mb-8 transition-transform duration-500 hover:scale-110 hover:rotate-3">
                    <img src="/logo.png" alt="Điện Máy Hiếu" />
                </div>
                
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-black uppercase tracking-tight text-white mb-6 leading-tight">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-600 text-neon">HỆ SINH THÁI</span> <br/> 
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-yellow-400 text-neon-orange">ĐIỆN MÁY HIẾU</span>
                </h1>
                
                <p class="text-lg md:text-2xl text-gray-300 max-w-3xl mx-auto mb-10 font-bold tracking-wide">
                    ĐIỆN MÁY CHÍNH HÃNG - CỨU HỘ KỸ THUẬT SIÊU TỐC - IN 3D ĐỘC BẢN
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center items-center gap-6">
                    <a href="#dat-lich" onclick="document.getElementById('dat-lich').scrollIntoView({behavior: 'smooth'}); return false;" class="pulse-btn bg-gradient-to-r from-orange-500 to-red-500 text-white font-black text-xl px-12 py-5 rounded-full uppercase tracking-wider hover:from-orange-400 hover:to-red-400 transition-all flex items-center justify-center w-full sm:w-auto transform hover:-translate-y-1">
                        <i class="fa fa-tools mr-3"></i> Gọi Thợ Khẩn Cấp
                    </a>
                    <a href="#bang-gia" onclick="document.getElementById('bang-gia').scrollIntoView({behavior: 'smooth'}); return false;" class="bg-transparent border-4 border-cyan-400 text-cyan-400 font-black text-xl px-12 py-4 rounded-full uppercase tracking-wider hover:bg-cyan-400 hover:text-gray-900 transition-all w-full sm:w-auto text-center transform hover:-translate-y-1 shadow-[0_0_15px_rgba(0,243,255,0.5)]">
                        <i class="fa fa-list mr-3"></i> Xem Bảng Giá
                    </a>
                </div>
            </div>
        </div>

        <!-- BẢNG GIÁ SẢN PHẨM TỪ DATABASE (Phục hồi) -->
        <div id="bang-gia" class="container mx-auto max-w-7xl px-4 mb-24 pt-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-black uppercase text-gray-900 mb-4 tracking-tight drop-shadow-md">Bảng Giá <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-500 to-blue-600">Sản Phẩm & Dịch Vụ</span></h2>
                <div class="h-2 w-32 bg-gradient-to-r from-pink-500 to-orange-500 mx-auto rounded-full shadow-[0_0_10px_rgba(255,0,234,0.5)]"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach($DMH->get_list(" SELECT * FROM `danhsachmuacode` WHERE `hienthi` = 'SHOW' AND `money` > 0 ORDER BY id DESC LIMIT 8") as $row){ ?>
                <div class="card-hover bg-white rounded-3xl p-5 shadow-lg flex flex-col border-2 border-transparent relative overflow-hidden group">
                    <?php if($DMH->site('sukien') == 'ON' && $DMH->site('ptgiamgia') > 0) { ?>
                    <div class="absolute top-4 right-4 bg-gradient-to-r from-red-600 to-pink-600 text-white text-xs font-black px-3 py-1 rounded-full z-10 shadow-lg transform rotate-3">
                        GIẢM <?=$DMH->site('ptgiamgia');?>%
                    </div>
                    <?php } ?>

                    <div class="relative bg-gray-50 rounded-2xl h-56 mb-5 flex items-center justify-center overflow-hidden border border-gray-100 group-hover:border-cyan-200 transition-colors">
                        <img src="<?=$row['img'];?>" class="max-h-full max-w-full object-contain transform group-hover:scale-110 transition-transform duration-700" alt="<?=$row['title'];?>" />
                    </div>

                    <div class="flex-1 flex flex-col">
                        <div class="text-xs font-bold text-gray-400 mb-2 uppercase tracking-widest">MÃ: #<?=$row['id'];?></div>
                        <h4 class="text-lg font-black text-gray-900 mb-3 line-clamp-2 leading-tight group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-blue-600 group-hover:to-cyan-600 transition-all">
                            <?=$row['title'];?>
                        </h4>
                        
                        <div class="mt-auto bg-gray-50 rounded-xl p-3 border border-gray-100 mb-4 text-center">
                            <?php if($DMH->site('sukien') == 'ON' && $DMH->site('ptgiamgia') > 0 && $row['money'] > 0) { ?>
                                <div class="text-sm text-gray-400 line-through font-bold"><?=sotienmua($row['money']);?></div>
                                <div class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-500 drop-shadow-sm">
                                    <?=sotienmua($row['money'] - ($row['money']*$DMH->site('ptgiamgia')/100));?>
                                </div>
                            <?php } else { ?>
                                <div class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500 drop-shadow-sm">
                                    <?=sotienmua($row['money']);?>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <a href="https://zalo.me/<?=$DMH->site('zaloadmin');?>" target="_blank" class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white text-center font-black py-3 rounded-xl transition-colors text-sm shadow-sm flex justify-center items-center">
                                <i class="fa fa-comment-dots mr-2"></i> Zalo
                            </a>
                            <a href="/mua-code/<?=$row['id'];?>" class="bg-gray-900 text-white hover:bg-black text-center font-black py-3 rounded-xl transition-colors text-sm shadow-md flex justify-center items-center">
                                <i class="fa fa-shopping-cart mr-2"></i> Mua
                            </a>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            
            <div class="text-center mt-12">
                <a href="/dien-may" class="inline-block bg-white border-2 border-gray-200 text-gray-900 font-black px-10 py-4 rounded-full hover:border-cyan-500 hover:text-cyan-600 transition-all shadow-sm uppercase tracking-wide">
                    Xem Toàn Bộ Bảng Giá
                </a>
            </div>
        </div>

        <!-- BOOKING SECTION CÓ ĐỊNH VỊ TỌA ĐỘ -->
        <div id="dat-lich" class="container mx-auto max-w-6xl px-4 mb-24 pt-10">
            <div class="bg-white rounded-[40px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.15)] overflow-hidden flex flex-col lg:flex-row border-4 border-gray-50">
                
                <!-- Left: Info -->
                <div class="w-full lg:w-5/12 bg-gradient-to-br from-gray-900 via-[#111] to-black text-white p-12 relative overflow-hidden flex flex-col justify-between">
                    <div class="absolute top-[-30%] left-[-30%] w-[160%] h-[160%] bg-orange-600 opacity-20 blur-[120px] z-0 pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="inline-block px-4 py-1 rounded-full bg-orange-500/20 text-orange-400 font-black text-xs uppercase tracking-widest mb-6 border border-orange-500/30">
                            Cứu Hộ Siêu Tốc
                        </div>
                        <h2 class="text-4xl md:text-5xl font-black uppercase mb-6 leading-tight">
                            Gọi Thợ <br/> <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-yellow-400 text-neon-orange">Tại Nhà</span>
                        </h2>
                        <p class="text-gray-300 mb-10 text-base leading-relaxed font-medium">
                            Kỹ thuật viên Điện Máy Hiếu sẽ có mặt tại nhà bạn trong vòng 30 phút. Định vị tọa độ giúp chúng tôi tìm nhà bạn nhanh chóng và chính xác nhất!
                        </p>
                    </div>
                    
                    <div class="relative z-10 space-y-6 bg-white/5 p-6 rounded-3xl backdrop-blur-md border border-white/10">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white text-2xl shadow-[0_0_15px_rgba(255,94,0,0.4)]">
                                <i class="fa fa-phone-volume"></i>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 uppercase tracking-widest font-bold">Hotline Khẩn Cấp</div>
                                <div class="text-2xl font-black text-white">09xx.xxx.xxx</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Form -->
                <div class="w-full lg:w-7/12 p-8 md:p-12 bg-gray-50/50 relative">
                    <div id="thongbao_datlich" class="mb-6"></div>
                    
                    <form id="formDatLich" class="space-y-6 relative z-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Họ và Tên <span class="text-red-500">*</span></label>
                                <input type="text" id="dl_ten" class="form-input w-full bg-white rounded-2xl px-5 py-4 text-gray-900 font-bold placeholder-gray-400 shadow-sm" placeholder="Tên của bạn" required>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Số Điện Thoại <span class="text-red-500">*</span></label>
                                <input type="tel" id="dl_sdt" class="form-input w-full bg-white rounded-2xl px-5 py-4 text-gray-900 font-bold placeholder-gray-400 shadow-sm" placeholder="09xx.xxx.xxx" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Dịch Vụ Cần Gọi <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select id="dl_dichvu" class="form-input w-full bg-white rounded-2xl px-5 py-4 text-gray-900 font-bold appearance-none shadow-sm cursor-pointer">
                                    <option value="Sửa Máy Lạnh">Sửa chữa / Vệ sinh Máy Lạnh</option>
                                    <option value="Sửa Tủ Lạnh">Sửa chữa Tủ Lạnh</option>
                                    <option value="Sửa Máy Giặt">Sửa chữa Máy Giặt</option>
                                    <option value="Sửa Tivi">Sửa chữa Tivi</option>
                                    <option value="In 3D">Đặt thiết kế / In 3D</option>
                                    <option value="Khác">Khác...</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-5 pointer-events-none text-gray-500">
                                    <i class="fa fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Địa Chỉ Nhắn Gửi <span class="text-red-500">*</span></label>
                            <input type="text" id="dl_diachi" class="form-input w-full bg-white rounded-2xl px-5 py-4 text-gray-900 font-bold placeholder-gray-400 shadow-sm mb-3" placeholder="Số nhà, đường, xã..." required>
                            
                            <!-- Nút lấy tọa độ -->
                            <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-blue-100 shadow-sm">
                                <div id="location_status" class="text-sm font-bold text-gray-500 flex items-center">
                                    <i class="fa fa-map-marker-alt text-gray-400 mr-2 text-lg"></i> Chưa lấy vị trí (Giúp thợ tìm nhà nhanh hơn)
                                </div>
                                <button type="button" onclick="getLocation()" class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white font-bold py-2 px-4 rounded-lg transition-colors text-sm flex items-center">
                                    <i class="fa fa-location-arrow mr-1"></i> Định Vị
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Mô Tả Lỗi (Nếu Có)</label>
                            <textarea id="dl_yeucau" rows="3" class="form-input w-full bg-white rounded-2xl px-5 py-4 text-gray-900 font-bold placeholder-gray-400 shadow-sm resize-none" placeholder="Tủ lạnh không đông đá, máy lạnh chảy nước..."></textarea>
                        </div>

                        <button type="button" id="btnDatLich" class="w-full bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-black text-xl py-5 rounded-2xl transition-all shadow-[0_10px_25px_rgba(255,94,0,0.4)] hover:shadow-[0_15px_35px_rgba(255,94,0,0.5)] transform hover:-translate-y-1 flex items-center justify-center gap-3">
                            <i class="fa fa-paper-plane"></i> GỬI YÊU CẦU NGAY LẬP TỨC
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script type="text/javascript">
    let userLat = "";
    let userLng = "";

    function getLocation() {
        $("#location_status").html('<span class="text-blue-500 font-bold"><i class="fa fa-spinner fa-spin mr-2"></i> Đang định vị...</span>');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;
                $("#location_status").html('<span class="text-green-500 font-black"><i class="fa fa-check-circle mr-2"></i> Đã bắt được tọa độ thành công!</span>');
            }, function(error) {
                $("#location_status").html('<span class="text-red-500 font-bold"><i class="fa fa-exclamation-triangle mr-2"></i> Bạn chưa cấp quyền định vị hoặc có lỗi.</span>');
            });
        } else {
            $("#location_status").html('<span class="text-red-500 font-bold"><i class="fa fa-exclamation-circle mr-2"></i> Trình duyệt không hỗ trợ định vị.</span>');
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
                $('#btnDatLich').html('<i class="fa fa-paper-plane mr-2"></i> GỬI YÊU CẦU NGAY LẬP TỨC').prop('disabled', false).css('opacity', '1');
            },
            error: function() {
                alert("Lỗi kết nối máy chủ! Vui lòng gọi trực tiếp hotline.");
                $('#btnDatLich').html('<i class="fa fa-paper-plane mr-2"></i> GỬI YÊU CẦU NGAY LẬP TỨC').prop('disabled', false).css('opacity', '1');
            }
        });
    });
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
