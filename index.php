<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");
$title = "Điện Máy Hiếu - Bán lẻ, Sửa chữa & In 3D Chuyên Nghiệp";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");
?>

<!-- Add custom CSS for ADHD+OCD aesthetics -->
<style>
    :root {
        --dmh-blue: #0056b3;
        --dmh-orange: #ff6b00;
        --dmh-dark: #0a0f1a;
    }
    .hero-gradient {
        background: radial-gradient(circle at center, #1a253c 0%, var(--dmh-dark) 100%);
    }
    .text-glow {
        text-shadow: 0 0 20px rgba(0, 163, 255, 0.5);
    }
    .pulse-ring {
        animation: pulseRing 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
    }
    @keyframes pulseRing {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 107, 0, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 20px rgba(255, 107, 0, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 107, 0, 0); }
    }
    .pillar-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .pillar-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    .form-input {
        transition: all 0.3s ease;
    }
    .form-input:focus {
        transform: scale(1.01);
        box-shadow: 0 0 0 4px rgba(255, 107, 0, 0.1);
    }
</style>

<div class="content-wrapper bg-gray-50 min-h-screen" id="content_wrapper">
    <div class="page-content pb-12">
        
        <!-- HERO SECTION (Bùng nổ thị giác) -->
        <div class="relative w-full hero-gradient pt-20 pb-24 md:pt-32 md:pb-40 overflow-hidden mb-16">
            <!-- Decorative blobs -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
                <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] bg-blue-600 opacity-20 blur-[120px] rounded-full mix-blend-screen"></div>
                <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] bg-orange-600 opacity-20 blur-[120px] rounded-full mix-blend-screen"></div>
            </div>
            
            <div class="container mx-auto px-4 relative z-10 text-center">
                <img src="/logo.png" alt="Điện Máy Hiếu" class="mx-auto h-28 md:h-40 mb-8 filter drop-shadow-[0_0_15px_rgba(255,255,255,0.3)] brightness-0 invert transition-transform duration-500 hover:scale-105" />
                
                <h1 class="text-5xl md:text-7xl font-black uppercase tracking-tight text-white mb-6 text-glow leading-tight">
                    Giải Pháp Toàn Diện <br/> <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-yellow-300">Điện Máy & Công Nghệ</span>
                </h1>
                
                <p class="text-lg md:text-2xl text-gray-300 max-w-3xl mx-auto mb-10 font-medium">
                    Bán lẻ Điện Máy chính hãng - Cứu hộ Kỹ thuật siêu tốc tại nhà - Công nghệ In 3D tiên phong.
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                    <a href="#dat-lich" class="pulse-ring bg-orange-500 text-white font-black text-lg px-10 py-4 rounded-full uppercase tracking-wider hover:bg-orange-600 transition-colors flex items-center gap-2 w-full sm:w-auto justify-center shadow-[0_0_30px_rgba(255,107,0,0.4)]">
                        <i class="fa fa-phone-alt"></i> Gọi Thợ Khẩn Cấp
                    </a>
                    <a href="#san-pham" class="bg-transparent border-2 border-white text-white font-black text-lg px-10 py-4 rounded-full uppercase tracking-wider hover:bg-white hover:text-gray-900 transition-all w-full sm:w-auto text-center">
                        Khám Phá Sản Phẩm
                    </a>
                </div>
            </div>
        </div>

        <!-- THE 3 PILLARS (OCD Layout) -->
        <div class="container mx-auto max-w-7xl px-4 mb-24">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-black uppercase text-gray-900 mb-4 tracking-tight">Hệ Sinh Thái <span class="text-blue-600">Điện Máy Hiếu</span></h2>
                <div class="h-1 w-24 bg-gradient-to-r from-blue-600 to-orange-500 mx-auto rounded-full"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Pillar 1 -->
                <div class="pillar-card bg-white rounded-3xl p-8 border border-gray-100 shadow-xl relative overflow-hidden group">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300 text-blue-600 text-3xl">
                        <i class="fa fa-tv"></i>
                    </div>
                    <h3 class="text-2xl font-black uppercase text-gray-900 mb-4">Điện Máy & Gia Dụng</h3>
                    <p class="text-gray-600 leading-relaxed mb-8">
                        Phân phối các sản phẩm Tivi, Tủ Lạnh, Máy Giặt, Điều Hòa chính hãng. Cam kết mức giá tốt nhất, lắp đặt chuyên nghiệp và bảo hành tận tâm.
                    </p>
                    <a href="/dien-may" class="inline-flex items-center text-blue-600 font-bold uppercase tracking-wide group-hover:text-blue-800">
                        Xem danh mục <i class="fa fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>

                <!-- Pillar 2 (Hero) -->
                <div class="pillar-card bg-gradient-to-br from-orange-500 to-orange-600 rounded-3xl p-8 shadow-2xl relative overflow-hidden group md:-translate-y-4">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl transform translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute top-4 right-6 bg-white text-orange-600 text-xs font-black px-3 py-1 rounded-full uppercase tracking-widest shadow-md">
                        Mũi Nhọn
                    </div>
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-6 text-white text-3xl backdrop-blur-sm">
                        <i class="fa fa-tools"></i>
                    </div>
                    <h3 class="text-2xl font-black uppercase text-white mb-4">Gọi Thợ Tại Nhà</h3>
                    <p class="text-orange-50 leading-relaxed mb-8">
                        Đội ngũ kỹ thuật viên tinh nhuệ. Khắc phục triệt để mọi sự cố điện lạnh, điện tử. Có mặt thần tốc tại khu vực Lấp Vò chỉ sau 30 phút.
                    </p>
                    <a href="#dat-lich" class="inline-flex items-center text-white font-black uppercase tracking-wide group-hover:underline">
                        Đặt Lịch Ngay <i class="fa fa-bolt ml-2 group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>

                <!-- Pillar 3 -->
                <div class="pillar-card bg-white rounded-3xl p-8 border border-gray-100 shadow-xl relative overflow-hidden group">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-gray-900 group-hover:text-white transition-colors duration-300 text-gray-900 text-3xl">
                        <i class="fa fa-cube"></i>
                    </div>
                    <h3 class="text-2xl font-black uppercase text-gray-900 mb-4">Dịch Vụ In 3D</h3>
                    <p class="text-gray-600 leading-relaxed mb-8">
                        Cung cấp giải pháp chế tạo chi tiết nhựa, linh kiện thay thế độc bản bằng công nghệ In 3D tiên tiến. Độ chính xác tuyệt đối, vật liệu siêu bền.
                    </p>
                    <a href="/in-3d" class="inline-flex items-center text-gray-900 font-bold uppercase tracking-wide group-hover:text-black">
                        Khám phá In 3D <i class="fa fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- PRODUCT SHOWCASE (Hoàn hảo & Cao Cấp) -->
        <div id="san-pham" class="container mx-auto max-w-7xl px-4 mb-24">
            <div class="flex flex-col md:flex-row justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl md:text-4xl font-black uppercase text-gray-900 mb-2 tracking-tight">Sản Phẩm <span class="text-blue-600">Nổi Bật</span></h2>
                    <p class="text-gray-500 font-medium">Tuyển chọn các thiết bị điện máy & gia dụng đáng mua nhất</p>
                </div>
                <a href="/dien-may" class="hidden md:inline-flex items-center text-blue-600 font-bold hover:underline">
                    Xem tất cả <i class="fa fa-angle-right ml-1"></i>
                </a>
            </div>

            <!-- Static Product Grid Replacing the old DB loop -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Product 1 -->
                <div class="bg-white rounded-2xl p-4 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 group flex flex-col">
                    <div class="relative bg-gray-50 rounded-xl h-48 mb-4 flex items-center justify-center overflow-hidden">
                        <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-black px-2 py-1 rounded-lg z-10">-15%</div>
                        <i class="fa fa-tv text-5xl text-gray-300 group-hover:scale-110 transition-transform duration-500"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-2 line-clamp-2 leading-tight flex-1 group-hover:text-blue-600 transition-colors">Smart Tivi Samsung 4K 65 inch UA65AU7002</h4>
                    <div class="mb-4">
                        <span class="text-sm text-gray-400 line-through block">12.500.000đ</span>
                        <span class="text-xl font-black text-red-600">10.625.000đ</span>
                    </div>
                    <button class="w-full bg-gray-100 text-gray-900 font-bold py-3 rounded-xl hover:bg-blue-600 hover:text-white transition-colors">
                        Chi Tiết
                    </button>
                </div>
                
                <!-- Product 2 -->
                <div class="bg-white rounded-2xl p-4 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 group flex flex-col">
                    <div class="relative bg-gray-50 rounded-xl h-48 mb-4 flex items-center justify-center overflow-hidden">
                        <i class="fa fa-snowflake text-5xl text-gray-300 group-hover:scale-110 transition-transform duration-500"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-2 line-clamp-2 leading-tight flex-1 group-hover:text-blue-600 transition-colors">Tủ Lạnh Inverter Panasonic 322 Lít NR-BC320WVN</h4>
                    <div class="mb-4">
                        <span class="text-xl font-black text-blue-600 mt-5 block">9.890.000đ</span>
                    </div>
                    <button class="w-full bg-gray-100 text-gray-900 font-bold py-3 rounded-xl hover:bg-blue-600 hover:text-white transition-colors">
                        Chi Tiết
                    </button>
                </div>

                <!-- Product 3 -->
                <div class="bg-white rounded-2xl p-4 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 group flex flex-col">
                    <div class="relative bg-gray-50 rounded-xl h-48 mb-4 flex items-center justify-center overflow-hidden">
                        <div class="absolute top-2 left-2 bg-blue-500 text-white text-xs font-black px-2 py-1 rounded-lg z-10">Mới</div>
                        <i class="fa fa-wind text-5xl text-gray-300 group-hover:scale-110 transition-transform duration-500"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-2 line-clamp-2 leading-tight flex-1 group-hover:text-blue-600 transition-colors">Máy Lạnh Daikin Inverter 1 HP ATKF25XVMV</h4>
                    <div class="mb-4">
                        <span class="text-xl font-black text-blue-600 mt-5 block">10.490.000đ</span>
                    </div>
                    <button class="w-full bg-gray-100 text-gray-900 font-bold py-3 rounded-xl hover:bg-blue-600 hover:text-white transition-colors">
                        Chi Tiết
                    </button>
                </div>

                <!-- Product 4 (3D Print Example) -->
                <div class="bg-white rounded-2xl p-4 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 group flex flex-col">
                    <div class="relative bg-gray-50 rounded-xl h-48 mb-4 flex items-center justify-center overflow-hidden">
                        <div class="absolute top-2 left-2 bg-gray-800 text-white text-xs font-black px-2 py-1 rounded-lg z-10">In 3D</div>
                        <i class="fa fa-cube text-5xl text-gray-300 group-hover:scale-110 transition-transform duration-500"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-2 line-clamp-2 leading-tight flex-1 group-hover:text-blue-600 transition-colors">Dịch vụ In 3D Chi tiết Máy móc & Linh kiện Nhựa</h4>
                    <div class="mb-4">
                        <span class="text-xl font-black text-blue-600 mt-5 block">Liên Hệ</span>
                    </div>
                    <button class="w-full bg-gray-900 text-white font-bold py-3 rounded-xl hover:bg-black transition-colors">
                        Báo Giá
                    </button>
                </div>
            </div>
            <div class="text-center mt-8 md:hidden">
                <a href="/dien-may" class="inline-block bg-white border border-gray-200 text-gray-900 font-bold px-6 py-3 rounded-xl hover:bg-gray-50">
                    Xem tất cả sản phẩm
                </a>
            </div>
        </div>

        <!-- BOOKING SECTION (Sang Trọng & Tinh Tế) -->
        <div id="dat-lich" class="container mx-auto max-w-5xl px-4 mb-24">
            <div class="bg-white rounded-3xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] overflow-hidden flex flex-col md:flex-row border border-gray-100">
                
                <!-- Left: Info -->
                <div class="w-full md:w-2/5 bg-gradient-to-br from-gray-900 to-black text-white p-10 relative overflow-hidden">
                    <div class="absolute top-[-20%] left-[-20%] w-[140%] h-[140%] bg-blue-600 opacity-20 blur-[100px] z-0"></div>
                    <div class="relative z-10 h-full flex flex-col">
                        <h2 class="text-3xl font-black uppercase mb-4 text-glow">Đặt Lịch<br/><span class="text-orange-500">Sửa Chữa</span></h2>
                        <p class="text-gray-300 mb-10 text-sm leading-relaxed">
                            Cần cấp cứu thiết bị điện lạnh, điện tử? Hãy điền thông tin, kỹ thuật viên Điện Máy Hiếu sẽ liên hệ và có mặt tại nhà bạn trong vòng 30 phút (khu vực Lấp Vò).
                        </p>
                        
                        <div class="space-y-6 mt-auto">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-orange-500 text-xl backdrop-blur-sm">
                                    <i class="fa fa-phone"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-400 uppercase tracking-widest font-bold">Hotline Khẩn Cấp</div>
                                    <div class="text-xl font-black">09xx.xxx.xxx</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-blue-400 text-xl backdrop-blur-sm">
                                    <i class="fa fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-400 uppercase tracking-widest font-bold">Khu Vực Hỗ Trợ</div>
                                    <div class="text-sm font-bold mt-1">Lấp Vò, Đồng Tháp & Lân cận</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Form -->
                <div class="w-full md:w-3/5 p-10">
                    <div id="thongbao_datlich" class="mb-4"></div>
                    <form id="formDatLich" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Họ và Tên *</label>
                                <input type="text" id="dl_ten" class="form-input w-full bg-gray-50 border-0 rounded-xl px-4 py-4 text-gray-900 font-medium placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-orange-500" placeholder="Tên của bạn" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Số Điện Thoại *</label>
                                <input type="tel" id="dl_sdt" class="form-input w-full bg-gray-50 border-0 rounded-xl px-4 py-4 text-gray-900 font-medium placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-orange-500" placeholder="09xx.xxx.xxx" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Dịch Vụ Cần Gọi *</label>
                                <div class="relative">
                                    <select id="dl_dichvu" class="form-input w-full bg-gray-50 border-0 rounded-xl px-4 py-4 text-gray-900 font-medium appearance-none focus:outline-none focus:bg-white focus:ring-2 focus:ring-orange-500">
                                        <option value="Sửa Máy Lạnh">Sửa chữa / Vệ sinh Máy Lạnh</option>
                                        <option value="Sửa Tủ Lạnh">Sửa chữa Tủ Lạnh</option>
                                        <option value="Sửa Máy Giặt">Sửa chữa Máy Giặt</option>
                                        <option value="Sửa Tivi">Sửa chữa Tivi</option>
                                        <option value="In 3D">Đặt thiết kế / In 3D</option>
                                        <option value="Khác">Khác...</option>
                                    </select>
                                    <i class="fa fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Địa Chỉ *</label>
                                <input type="text" id="dl_diachi" class="form-input w-full bg-gray-50 border-0 rounded-xl px-4 py-4 text-gray-900 font-medium placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-orange-500" placeholder="Số nhà, đường, xã..." required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Mô Tả Sự Cố</label>
                            <textarea id="dl_yeucau" rows="3" class="form-input w-full bg-gray-50 border-0 rounded-xl px-4 py-4 text-gray-900 font-medium placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-orange-500 resize-none" placeholder="Ví dụ: Tủ lạnh không đông đá, máy lạnh kêu to..."></textarea>
                        </div>

                        <button type="button" id="btnDatLich" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black text-lg py-4 rounded-xl transition-all shadow-[0_10px_20px_-10px_rgba(255,107,0,0.5)] hover:shadow-[0_15px_25px_-10px_rgba(255,107,0,0.6)] flex items-center justify-center gap-2">
                            <i class="fa fa-paper-plane"></i> GỬI YÊU CẦU ĐẶT LỊCH
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script type="text/javascript">
    $("#btnDatLich").on("click", function() {
        $('#btnDatLich').html('<i class="fa fa-spinner fa-spin mr-2"></i> ĐANG XỬ LÝ...').prop('disabled', true).removeClass('hover:bg-orange-600').addClass('bg-gray-400 cursor-not-allowed');
        
        $.ajax({
            url: "<?=BASE_URL('controller/client/DatLich.php');?>",
            method: "POST",
            data: {
                type: 'DatLich',
                ten: $("#dl_ten").val(),
                sdt: $("#dl_sdt").val(),
                dichvu: $("#dl_dichvu").val(),
                diachi: $("#dl_diachi").val(),
                yeucau: $("#dl_yeucau").val()
            },
            success: function(response) {
                $("#thongbao_datlich").html(response);
                $('#btnDatLich').html('<i class="fa fa-paper-plane mr-2"></i> GỬI YÊU CẦU ĐẶT LỊCH').prop('disabled', false).removeClass('bg-gray-400 cursor-not-allowed').addClass('hover:bg-orange-600');
            },
            error: function() {
                alert("Lỗi kết nối máy chủ! Vui lòng gọi trực tiếp hotline.");
                $('#btnDatLich').html('<i class="fa fa-paper-plane mr-2"></i> GỬI YÊU CẦU ĐẶT LỊCH').prop('disabled', false).removeClass('bg-gray-400 cursor-not-allowed').addClass('hover:bg-orange-600');
            }
        });
    });
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
