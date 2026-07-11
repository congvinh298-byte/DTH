<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");
$title = "Đặt Lịch Gọi Thợ | Điện Máy Hiếu";
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

/* Dropdown Styles */
.service-dropdown {
    display: none;
    position: absolute;
    left: 0; right: 0;
    top: calc(100% + 8px);
    z-index: 999;
    background: var(--dmh-white);
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(10, 25, 47, 0.15);
    border: 1px solid #E5E7EB;
    overflow: hidden;
}
.service-dropdown.open { display: block; }

.service-option {
    display: flex;
    align-items: center;
    padding: 16px 20px;
    cursor: pointer;
    border-left: 4px solid transparent;
    border-bottom: 1px solid #F3F4F6;
    gap: 16px;
}
.service-option:hover {
    background: #F8FAFC;
    border-left-color: var(--dmh-navy);
}

.service-option .svc-icon {
    width: 40px; height: 40px;
    border-radius: 8px;
    background: var(--dmh-navy);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; color: #fff; flex-shrink: 0;
}

.svc-trigger {
    width: 100%;
    padding: 16px;
    border: 2px solid #E5E7EB;
    border-radius: 12px;
    background: #FFFFFF;
    cursor: pointer;
    display: flex; align-items: center; gap: 16px;
    transition: border-color 0.2s;
}
.svc-trigger:hover, .svc-trigger.active {
    border-color: var(--dmh-navy);
}
</style>

<div class="page-header py-16 text-center">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-black uppercase tracking-tight mb-4">Gọi Thợ Sửa Chữa</h1>
        <p class="text-lg text-gray-300 font-medium max-w-2xl mx-auto">
            Xử lý nhanh chóng các sự cố Điện Lạnh, Điện Nước. Kỹ thuật viên <strong class="text-white">Điện Máy Hiếu</strong> sẽ có mặt sau 30 phút.
        </p>
    </div>
</div>

<section class="py-12 md:py-20">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 md:p-12">
            
            <div id="thongbao_datlich" class="mb-6"></div>

            <div class="space-y-6">
                <!-- Tên & SĐT -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-[#0A192F] uppercase tracking-widest mb-2">Họ và Tên *</label>
                        <input type="text" id="dl_ten" class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3.5 font-bold focus:border-[#0A192F] focus:bg-white outline-none transition-all" placeholder="Nhập tên của bạn">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-[#0A192F] uppercase tracking-widest mb-2">Số Điện Thoại *</label>
                        <input type="tel" id="dl_sdt" class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3.5 font-bold focus:border-[#0A192F] focus:bg-white outline-none transition-all" placeholder="VD: 0939.354.937">
                    </div>
                </div>

                <!-- Dịch vụ -->
                <div>
                    <label class="block text-xs font-black text-[#0A192F] uppercase tracking-widest mb-2">Loại Dịch Vụ Cần Gọi *</label>
                    <div class="relative" id="svcWrapper">
                        <div id="svcTrigger" class="svc-trigger" onclick="toggleServiceDropdown()">
                            <div class="svc-icon bg-[#0A192F]">
                                <i class="fa fa-snowflake"></i>
                            </div>
                            <div class="flex-1 text-left">
                                <div class="font-black text-[#0A192F] text-base" id="svcName">Vệ Sinh / Sửa Máy Lạnh</div>
                                <div class="font-bold text-gray-500 text-sm" id="svcPrice">Vệ sinh cơ bản từ 150.000đ</div>
                            </div>
                            <i class="fa fa-chevron-down text-gray-400" id="svcArrow"></i>
                        </div>
                        <input type="hidden" id="dl_dichvu" value="Sửa Máy Lạnh - Từ 150.000đ">

                        <div class="service-dropdown" id="svcDropdown">
                            <div class="service-option" onclick="pickService('Sửa Máy Lạnh - Từ 150.000đ','fa-snowflake','Vệ Sinh / Sửa Máy Lạnh','Vệ sinh: 150.000đ | Sửa chữa: Báo giá sau')">
                                <div class="svc-icon"><i class="fa fa-snowflake"></i></div>
                                <div>
                                    <div class="font-black text-[#0A192F]">Vệ Sinh / Sửa Máy Lạnh</div>
                                    <div class="text-gray-500 text-sm font-bold">Vệ sinh: 150k | Sửa: Báo giá sau</div>
                                </div>
                            </div>
                            <div class="service-option" onclick="pickService('Sửa Tủ Lạnh - Coi máy miễn phí','fa-temperature-low','Sửa Chữa Tủ Lạnh','Kiểm tra tận nơi miễn phí')">
                                <div class="svc-icon"><i class="fa fa-temperature-low"></i></div>
                                <div>
                                    <div class="font-black text-[#0A192F]">Sửa Chữa Tủ Lạnh</div>
                                    <div class="text-gray-500 text-sm font-bold">Kiểm tra tận nhà miễn phí</div>
                                </div>
                            </div>
                            <div class="service-option" onclick="pickService('Sửa Máy Giặt - Từ 150.000đ','fa-tshirt','Sửa Chữa / Vệ Sinh Máy Giặt','Vệ sinh lồng giặt: 150.000đ')">
                                <div class="svc-icon"><i class="fa fa-tshirt"></i></div>
                                <div>
                                    <div class="font-black text-[#0A192F]">Vệ Sinh / Sửa Máy Giặt</div>
                                    <div class="text-gray-500 text-sm font-bold">Vệ sinh lồng giặt từ 150k</div>
                                </div>
                            </div>
                            <div class="service-option" onclick="pickService('Sửa Tivi / Smart TV','fa-tv','Sửa Chữa Tivi','Hỗ trợ Samsung, Sony, LG...')">
                                <div class="svc-icon"><i class="fa fa-tv"></i></div>
                                <div>
                                    <div class="font-black text-[#0A192F]">Sửa Chữa Tivi</div>
                                    <div class="text-gray-500 text-sm font-bold">Hỗ trợ Samsung, Sony, LG...</div>
                                </div>
                            </div>
                            <div class="service-option" onclick="pickService('Sửa Điện Nước - Báo giá tận nơi','fa-tint','Sửa Chữa Điện Nước / Máy Bơm','Khắc phục sự cố điện, ống nước')">
                                <div class="svc-icon"><i class="fa fa-tint"></i></div>
                                <div>
                                    <div class="font-black text-[#0A192F]">Sửa Chữa Điện Nước</div>
                                    <div class="text-gray-500 text-sm font-bold">Lắp đặt, sửa chữa ống nước, máy bơm</div>
                                </div>
                            </div>
                            <div class="service-option" onclick="pickService('Khác - Cần tư vấn','fa-question-circle','Dịch Vụ Khác','Liên hệ khảo sát')">
                                <div class="svc-icon"><i class="fa fa-question-circle"></i></div>
                                <div>
                                    <div class="font-black text-[#0A192F]">Dịch Vụ Khác</div>
                                    <div class="text-gray-500 text-sm font-bold">Liên hệ khảo sát & báo giá</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Địa chỉ -->
                <div>
                    <label class="block text-xs font-black text-[#0A192F] uppercase tracking-widest mb-2">Địa Chỉ & Định Vị GPS *</label>
                    <input type="text" id="dl_diachi" class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3.5 font-bold focus:border-[#0A192F] focus:bg-white outline-none transition-all mb-3" placeholder="Nhập địa chỉ nhà bạn">
                    
                    <div class="flex flex-col sm:flex-row items-center justify-between bg-gray-50 border border-gray-200 p-3 rounded-xl gap-4">
                        <div id="location_status" class="text-sm font-bold text-gray-500 flex items-center">
                            <i class="fa fa-map-marker-alt text-gray-400 mr-2"></i> Chưa định vị (Chia sẻ GPS để thợ tìm nhà nhanh hơn)
                        </div>
                        <button type="button" onclick="getLocation()" class="bg-[#0A192F] hover:bg-black text-white font-bold py-2 px-6 rounded-lg text-sm transition-colors w-full sm:w-auto">
                            Định Vị GPS
                        </button>
                    </div>
                </div>

                <!-- Ghi chú -->
                <div>
                    <label class="block text-xs font-black text-[#0A192F] uppercase tracking-widest mb-2">Tình Trạng (Ghi Chú Thêm)</label>
                    <textarea id="dl_yeucau" rows="4" class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3.5 font-bold focus:border-[#0A192F] focus:bg-white outline-none transition-all resize-none" placeholder="Ví dụ: Máy lạnh không mát, tủ lạnh chảy nước..."></textarea>
                </div>

                <!-- Submit -->
                <button type="button" id="btnDatLich" class="w-full bg-[#0A192F] hover:bg-black text-white font-black text-lg py-4 rounded-xl flex items-center justify-center gap-3 uppercase tracking-widest transition-colors mt-4">
                    Xác Nhận Đặt Lịch
                </button>
            </div>
            
            <div class="mt-8 text-center text-sm font-bold text-gray-500">
                <p>Cần hỗ trợ gấp? Gọi ngay: <a href="tel:0939354937" class="text-[#0A192F] font-black underline">0939.354.937</a></p>
            </div>
        </div>
    </div>
</section>

<script>
function toggleServiceDropdown() {
    var dd = document.getElementById('svcDropdown');
    var trigger = document.getElementById('svcTrigger');
    var arrow = document.getElementById('svcArrow');
    dd.classList.toggle('open');
    trigger.classList.toggle('active');
    arrow.style.transform = dd.classList.contains('open') ? 'rotate(180deg)' : '';
}

function pickService(value, icon, name, price) {
    document.getElementById('dl_dichvu').value = value;
    document.getElementById('svcName').textContent = name;
    document.getElementById('svcPrice').textContent = price;

    var iconEl = document.getElementById('svcTrigger').querySelector('.svc-icon');
    iconEl.innerHTML = '<i class="fa ' + icon + '"></i>';

    document.getElementById('svcDropdown').classList.remove('open');
    document.getElementById('svcTrigger').classList.remove('active');
    document.getElementById('svcArrow').style.transform = '';
}

document.addEventListener('click', function(e) {
    if (!document.getElementById('svcWrapper').contains(e.target)) {
        document.getElementById('svcDropdown').classList.remove('open');
        document.getElementById('svcTrigger').classList.remove('active');
        document.getElementById('svcArrow').style.transform = '';
    }
});

let userLat = "", userLng = "";
function getLocation() {
    $("#location_status").html('<span class="text-[#0A192F] font-bold"><i class="fa fa-spinner fa-spin mr-2"></i>Đang lấy vị trí...</span>');
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            userLat = pos.coords.latitude;
            userLng = pos.coords.longitude;
            $("#location_status").html('<span class="text-green-600 font-black"><i class="fa fa-check-circle mr-2"></i>Đã lấy GPS thành công!</span>');
        }, function() {
            $("#location_status").html('<span class="text-red-500 font-bold"><i class="fa fa-times-circle mr-2"></i>Lỗi truy cập GPS.</span>');
        });
    } else {
        $("#location_status").html('<span class="text-red-500 font-bold">Trình duyệt không hỗ trợ.</span>');
    }
}

$("#btnDatLich").on("click", function() {
    var ten = $("#dl_ten").val().trim();
    var sdt = $("#dl_sdt").val().trim();
    var diachi = $("#dl_diachi").val().trim();
    
    if (!ten || !sdt || !diachi) {
        alert("Vui lòng điền đầy đủ: Tên, SĐT và Địa chỉ!");
        return;
    }
    
    $('#btnDatLich').html('<i class="fa fa-spinner fa-spin mr-2"></i>ĐANG XỬ LÝ...').prop('disabled', true).css('opacity','0.7');
    
    if (userLat) diachi += " | GPS: " + userLat + "," + userLng + " (https://maps.google.com/?q="+userLat+","+userLng+")";
    
    $.ajax({
        url: "/controller/client/DatLich.php",
        method: "POST",
        data: { type:'DatLich', ten:ten, sdt:sdt, dichvu:$("#dl_dichvu").val(), diachi:diachi, yeucau:$("#dl_yeucau").val() },
        success: function(r) {
            $("#thongbao_datlich").html(r);
            $('#btnDatLich').html('Xác Nhận Đặt Lịch').prop('disabled',false).css('opacity','1');
        },
        error: function() {
            alert("Lỗi hệ thống! Vui lòng gọi trực tiếp: 0939.354.937");
            $('#btnDatLich').html('Xác Nhận Đặt Lịch').prop('disabled',false).css('opacity','1');
        }
    });
});
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
