<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");
$title = "Sản Phẩm & Dịch Vụ In 3D | Điện Máy Hiếu";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");
?>

<style>
:root {
    --dmh-navy:      #0A192F;
    --dmh-black:     #111111;
    --dmh-white:     #FFFFFF;
    --dmh-gray:      #F8F9FA;
}

body {
    background-color: var(--dmh-gray);
}

.page-header {
    background-color: var(--dmh-navy);
    color: var(--dmh-white);
}

.section-title::after {
    content: '';
    display: block;
    width: 60px;
    height: 4px;
    background-color: var(--dmh-navy);
    margin: 15px auto 0;
    border-radius: 2px;
}
</style>

<div class="page-header py-16 text-center border-b-4 border-black">
    <div class="container mx-auto px-4">
        <div class="inline-block bg-white text-[#0A192F] text-xs font-black px-4 py-1.5 rounded-full uppercase tracking-widest mb-4 border-2 border-black">
            Dịch vụ Độc quyền tại Đồng Tháp
        </div>
        <h1 class="text-4xl md:text-5xl font-black uppercase tracking-tight mb-4">Sản Phẩm & Dịch Vụ In 3D</h1>
        <p class="text-lg text-gray-300 font-medium max-w-2xl mx-auto">
            Thiết kế và chế tạo linh kiện, mô hình kỹ thuật độ chính xác cao.<br>
            <strong class="text-white bg-black px-3 py-1 mt-2 inline-block rounded-md">BẢNG GIÁ MINH BẠCH: 500 VNĐ / 1 GRAM NHỰA</strong>
        </p>
    </div>
</div>

<!-- Showcase 3D -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-[#0A192F] uppercase tracking-tight section-title">
                Sản Phẩm Mẫu & Ứng Dụng
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 text-center hover:border-[#0A192F] transition-colors">
                <div class="w-full h-48 bg-gray-200 rounded-xl mb-6 flex items-center justify-center">
                    <i class="fa fa-cogs text-5xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-black text-[#0A192F] mb-2 uppercase">Linh Kiện Thay Thế</h3>
                <p class="text-gray-600 font-medium">Bánh răng, giá đỡ, ngàm nhựa máy công nghiệp, thiết bị gia dụng không còn sản xuất trên thị trường.</p>
            </div>
            
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 text-center hover:border-[#0A192F] transition-colors">
                <div class="w-full h-48 bg-gray-200 rounded-xl mb-6 flex items-center justify-center">
                    <i class="fa fa-cubes text-5xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-black text-[#0A192F] mb-2 uppercase">Mô Hình Khởi Nghiệp</h3>
                <p class="text-gray-600 font-medium">In tạo dáng sản phẩm thử nghiệm (prototype), vỏ hộp mạch điện tử, thiết kế công nghiệp.</p>
            </div>

            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 text-center hover:border-[#0A192F] transition-colors">
                <div class="w-full h-48 bg-gray-200 rounded-xl mb-6 flex items-center justify-center">
                    <i class="fa fa-robot text-5xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-black text-[#0A192F] mb-2 uppercase">Trang Trí & Decor</h3>
                <p class="text-gray-600 font-medium">Tượng nhân vật, logo doanh nghiệp nổi 3D, chậu cây phong thủy độc bản.</p>
            </div>
        </div>
    </div>
</section>

<!-- Form Đặt In 3D -->
<section class="py-16">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 md:p-12">
            
            <div class="text-center mb-8">
                <h2 class="text-2xl font-black text-[#0A192F] uppercase tracking-tight">Gửi Yêu Cầu Đặt In</h2>
                <p class="text-gray-500 font-medium mt-2">Điền thông tin hoặc gửi file 3D (STL/OBJ). Chúng tôi sẽ báo giá ngay.</p>
            </div>

            <div id="thongbao_datin" class="mb-6"></div>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-[#0A192F] uppercase tracking-widest mb-2">Họ và Tên *</label>
                        <input type="text" id="di_ten" class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3.5 font-bold focus:border-[#0A192F] focus:bg-white outline-none transition-all" placeholder="Tên của bạn">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-[#0A192F] uppercase tracking-widest mb-2">Số Điện Thoại / Zalo *</label>
                        <input type="tel" id="di_sdt" class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3.5 font-bold focus:border-[#0A192F] focus:bg-white outline-none transition-all" placeholder="0939.354.937">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-[#0A192F] uppercase tracking-widest mb-2">Mô tả sản phẩm cần in *</label>
                    <textarea id="di_mota" rows="4" class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3.5 font-bold focus:border-[#0A192F] focus:bg-white outline-none transition-all resize-none" placeholder="Ví dụ: Kích thước dài x rộng x cao, màu sắc nhựa mong muốn, đã có file 3D chưa..."></textarea>
                </div>
                
                <div class="bg-blue-50 border-2 border-blue-100 rounded-xl p-4 flex gap-4 items-start">
                    <i class="fa fa-info-circle text-blue-500 text-xl mt-1"></i>
                    <div class="text-sm font-medium text-blue-800">
                        <strong class="font-black">Quy trình làm việc:</strong><br>
                        1. Bạn gửi yêu cầu.<br>
                        2. Kỹ thuật viên lên phần mềm tính toán trọng lượng thực tế.<br>
                        3. Báo giá tự động (Số gram x 500đ).<br>
                        4. Chốt đơn & Bắt đầu in.
                    </div>
                </div>

                <button type="button" id="btnDatIn" class="w-full bg-[#0A192F] hover:bg-black text-white font-black text-lg py-4 rounded-xl flex items-center justify-center gap-3 uppercase tracking-widest transition-colors mt-4">
                    <i class="fa fa-paper-plane"></i> Gửi Yêu Cầu Báo Giá
                </button>
            </div>
            
        </div>
    </div>
</section>

<script>
$("#btnDatIn").on("click", function() {
    var ten = $("#di_ten").val().trim();
    var sdt = $("#di_sdt").val().trim();
    var mota = $("#di_mota").val().trim();
    
    if (!ten || !sdt || !mota) {
        alert("Vui lòng điền đầy đủ Tên, SĐT và Mô tả sản phẩm!");
        return;
    }
    
    $('#btnDatIn').html('<i class="fa fa-spinner fa-spin mr-2"></i>ĐANG GỬI...').prop('disabled', true).css('opacity','0.7');
    
    // Sử dụng chung API Đặt Lịch nhưng có header riêng biệt để phân loại
    $.ajax({
        url: "/controller/client/DatLich.php",
        method: "POST",
        data: { 
            type: 'DatLich', 
            ten: ten, 
            sdt: sdt, 
            dichvu: "YÊU CẦU ĐẶT IN 3D (500đ/g)", 
            diachi: "Zalo/Online", 
            yeucau: mota 
        },
        success: function(r) {
            $("#thongbao_datin").html(r);
            $('#btnDatIn').html('<i class="fa fa-paper-plane"></i> Gửi Yêu Cầu Báo Giá').prop('disabled',false).css('opacity','1');
            $("#di_mota").val('');
        },
        error: function() {
            alert("Lỗi hệ thống! Vui lòng gửi Zalo trực tiếp: 0939.354.937");
            $('#btnDatIn').html('<i class="fa fa-paper-plane"></i> Gửi Yêu Cầu Báo Giá').prop('disabled',false).css('opacity','1');
        }
    });
});
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
