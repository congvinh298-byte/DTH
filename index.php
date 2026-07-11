<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");
$title = "Điện Máy Hiếu - Bán lẻ, Sửa chữa & In 3D | Lấp Vò, Đồng Tháp";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");
?>

<style>
/* ============================================
   ĐIỆN MÁY HIẾU - CORE DESIGN SYSTEM
   ADHD: High contrast, dopamine-boosting colors
   OCD: Pixel-perfect grid, consistent spacing
   ============================================ */

:root {
    --dmh-primary:   #1a56db;   /* Royal Blue - Logo */
    --dmh-accent:    #ff5a1f;   /* DMH Orange  - Logo */
    --dmh-dark:      #0a0f1d;
    --dmh-green:     #0ea271;
    --dmh-purple:    #7c3aed;
    --dmh-pink:      #db2777;
    --dmh-yellow:    #d97706;
    --dmh-teal:      #0891b2;
}

/* Custom dropdown styling for ADHD service selector */
.service-dropdown {
    display: none;
    position: absolute;
    left: 0; right: 0;
    top: calc(100% + 6px);
    z-index: 999;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.18);
    border: 2px solid #e5e7eb;
    overflow: hidden;
    animation: dropIn 0.2s ease;
}
.service-dropdown.open { display: block; }

@keyframes dropIn {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

.service-option {
    display: flex;
    align-items: center;
    padding: 14px 20px;
    cursor: pointer;
    transition: all 0.15s ease;
    border-left: 5px solid transparent;
    gap: 14px;
}
.service-option:hover { filter: brightness(0.95); }

.service-option .svc-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: #fff; flex-shrink: 0;
}

.service-option .svc-info { flex: 1; }
.service-option .svc-name { font-weight: 900; font-size: 15px; }
.service-option .svc-price { font-size: 12px; font-weight: 700; opacity: 0.8; }
.service-option .svc-badge {
    font-size: 11px; font-weight: 800;
    padding: 3px 8px; border-radius: 999px; color: #fff;
}

/* Selected trigger button */
.svc-trigger {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    background: #f9fafb;
    font-weight: 800;
    cursor: pointer;
    display: flex; align-items: center; gap: 12px;
    transition: border-color 0.2s;
    user-select: none;
}
.svc-trigger:hover { border-color: var(--dmh-primary); }
.svc-trigger.active { border-color: var(--dmh-primary); background: #eff6ff; }

.btn-adhd {
    background: linear-gradient(90deg, var(--dmh-accent) 0%, #e11d48 100%);
    box-shadow: 0 4px 20px rgba(255,90,31,0.4);
    transition: all 0.25s ease;
}
.btn-adhd:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(255,90,31,0.55); }

.glass-panel {
    background: rgba(255,255,255,0.97);
    backdrop-filter: blur(12px);
    box-shadow: 0 10px 40px rgba(26,86,219,0.12);
}

.compact-row {
    transition: background 0.15s, border-color 0.15s, transform 0.15s;
    border-left: 4px solid transparent;
}
.compact-row:hover {
    background: #f0f7ff;
    border-left-color: var(--dmh-primary);
    transform: translateX(3px);
}

.hero-bg {
    background: linear-gradient(135deg, #0a0f1d 0%, #0f2045 60%, #1a1035 100%);
}
</style>

<!-- =============== HERO =============== -->
<section class="hero-bg relative w-full pt-14 pb-20 overflow-hidden">
    <!-- Glows -->
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-600 rounded-full filter blur-[140px] opacity-20 pointer-events-none" style="transform:translate(30%,-30%)"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-orange-500 rounded-full filter blur-[140px] opacity-15 pointer-events-none" style="transform:translate(-30%,30%)"></div>

    <div class="container mx-auto px-4 max-w-6xl relative z-10">
        <div class="flex flex-col lg:flex-row items-center gap-12">

            <!-- Left: Text -->
            <div class="flex-1 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/15 border border-blue-400/25 text-cyan-400 text-xs font-black uppercase tracking-widest mb-6">
                    <i class="fa fa-certificate"></i>
                    Mã DN: 1402228630 · Đồng Tháp
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-5 uppercase">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">ĐIỆN MÁY</span>
                    <span class="text-orange-400"> HIẾU</span>
                    <br/>
                    <span class="text-2xl md:text-3xl text-gray-300 font-bold normal-case">Giải pháp toàn diện cho ngôi nhà bạn</span>
                </h1>

                <p class="text-gray-300 text-base md:text-lg mb-8 max-w-lg mx-auto lg:mx-0 leading-relaxed font-medium">
                    📍 Số 166 Ấp Bình Thạnh 1, Xã Lấp Vò, Tỉnh Đồng Tháp<br/>
                    📞 <a href="tel:0939354937" class="text-orange-400 font-black hover:text-orange-300">0939.354.937</a> · Phục vụ 6:00 – 22:00 (Kể cả CN)
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="#dat-lich"
                       onclick="document.getElementById('dat-lich').scrollIntoView({behavior:'smooth'}); return false;"
                       class="btn-adhd text-white font-black px-8 py-4 rounded-2xl uppercase tracking-wide flex items-center justify-center gap-2 text-base">
                        <i class="fa fa-tools"></i> Gọi Thợ Khẩn Cấp
                    </a>
                    <a href="#bang-gia"
                       onclick="document.getElementById('bang-gia').scrollIntoView({behavior:'smooth'}); return false;"
                       class="border-2 border-white/20 bg-white/8 hover:bg-white/15 text-white font-black px-8 py-4 rounded-2xl uppercase tracking-wide flex items-center justify-center gap-2 transition-all">
                        <i class="fa fa-list-ul"></i> Xem Bảng Giá
                    </a>
                </div>

                <!-- Stats -->
                <div class="mt-10 grid grid-cols-3 gap-4 max-w-lg mx-auto lg:mx-0">
                    <div class="bg-white/8 border border-white/10 rounded-2xl p-4 text-center">
                        <div class="text-2xl font-black text-orange-400">30'</div>
                        <div class="text-gray-400 text-xs font-bold mt-1">Có mặt tại nhà</div>
                    </div>
                    <div class="bg-white/8 border border-white/10 rounded-2xl p-4 text-center">
                        <div class="text-2xl font-black text-cyan-400">100%</div>
                        <div class="text-gray-400 text-xs font-bold mt-1">Chính hãng</div>
                    </div>
                    <div class="bg-white/8 border border-white/10 rounded-2xl p-4 text-center">
                        <div class="text-2xl font-black text-green-400">BH</div>
                        <div class="text-gray-400 text-xs font-bold mt-1">Bảo hành dài hạn</div>
                    </div>
                </div>
            </div>

            <!-- Right: Logo + QR -->
            <div class="flex-shrink-0 flex flex-col items-center gap-6">
                <!-- Logo -->
                <div class="relative">
                    <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-blue-500 to-orange-400 blur-xl opacity-50 scale-110"></div>
                    <div class="relative bg-white rounded-3xl p-4 shadow-2xl" style="width:220px">
                        <img src="/logo.png" alt="Logo Điện Máy Hiếu" class="w-full rounded-2xl object-contain">
                    </div>
                </div>
                <!-- QR Code -->
                <div class="bg-white rounded-2xl p-3 shadow-lg text-center">
                    <img src="/QR.png" alt="QR Truy cập Điện Máy Hiếu" class="w-28 h-28 object-contain">
                    <p class="text-gray-600 font-bold text-xs mt-2">Quét QR truy cập nhanh</p>
                    <p class="text-blue-600 font-black text-xs">dienmayhieu.com</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =============== COMPACT PRICE LIST =============== -->
<section id="bang-gia" class="container mx-auto px-4 max-w-5xl py-20">
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 border-b-4 border-blue-100 pb-4 gap-4">
        <div>
            <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tight">
                Bảng Giá <span class="text-blue-600">Sản Phẩm & Dịch Vụ</span>
            </h2>
            <p class="text-gray-500 mt-1 font-semibold text-sm">Báo giá minh bạch · Không phí ẩn · Cập nhật liên tục</p>
        </div>
        <a href="/dien-may" class="hidden md:flex items-center gap-1 font-black text-orange-500 hover:text-orange-600 uppercase text-xs tracking-wider">
            Toàn bộ sản phẩm <i class="fa fa-chevron-right text-[10px]"></i>
        </a>
    </div>

    <!-- Bảng Giá Dạng Grid Thu Gọn (ADHD/OCD) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        <?php
        $rows = $DMH->get_list("SELECT * FROM `danhsachmuacode` WHERE `hienthi` = 'SHOW' AND `money` > 0 ORDER BY id DESC LIMIT 8");
        if(empty($rows)): ?>
        <div class="col-span-full p-12 text-center text-gray-400 font-bold bg-white rounded-2xl shadow border border-gray-100">
            <i class="fa fa-box-open text-5xl mb-4 block"></i>
            Chưa có sản phẩm/dịch vụ nào được đăng.
        </div>
        <?php else: foreach($rows as $row):
            $price = $row['money'];
            $finalPrice = $price;
            $isEvent = ($DMH->site('sukien') == 'ON' && $DMH->site('ptgiamgia') > 0);
            if($isEvent) $finalPrice = $price - ($price * $DMH->site('ptgiamgia') / 100);
        ?>
        <div class="bg-white rounded-2xl p-4 md:p-5 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 flex flex-col hover:-translate-y-1 hover:shadow-xl transition-all duration-300 relative group overflow-hidden">
            <!-- Event Badge -->
            <?php if($isEvent): ?>
                <div class="absolute top-3 left-3 z-10 bg-red-500 text-white text-[10px] font-black px-2 py-1 rounded-md shadow-sm">
                    GIẢM <?=$DMH->site('ptgiamgia');?>%
                </div>
            <?php endif; ?>
            
            <!-- Image (OCD Box) -->
            <div class="w-full aspect-square bg-gray-50 rounded-xl mb-4 flex items-center justify-center p-2 relative">
                <img src="<?=$row['img'];?>" class="max-h-full max-w-full object-contain mix-blend-multiply transition-transform group-hover:scale-105" alt="<?=$row['title'];?>">
            </div>
            
            <!-- Title -->
            <h4 class="font-black text-gray-800 text-sm md:text-base leading-snug line-clamp-2 mb-2"><?=$row['title'];?></h4>
            
            <div class="mt-auto pt-3 border-t border-gray-100">
                <div class="flex items-end justify-between mb-3">
                    <div>
                        <?php if($price != $finalPrice): ?>
                            <div class="text-[11px] text-gray-400 line-through font-semibold mb-0.5"><?=sotienmua($price);?></div>
                        <?php endif; ?>
                        <div class="text-base md:text-lg font-black text-blue-600 leading-none"><?=sotienmua($finalPrice);?></div>
                    </div>
                </div>
                
                <a href="/mua-code/<?=$row['id'];?>" class="w-full block text-center bg-gray-900 hover:bg-blue-600 text-white text-xs font-black py-2.5 px-4 rounded-lg transition-colors uppercase tracking-wider">
                    ĐẶT MUA
                </a>
            </div>
        </div>
        <?php endforeach; endif; ?>
    </div>

    <div class="mt-6 text-center md:hidden">
        <a href="/dien-may" class="inline-block bg-blue-50 border border-blue-200 text-blue-700 font-black px-6 py-3 rounded-xl uppercase text-sm">
            Xem Tất Cả <i class="fa fa-arrow-right ml-1"></i>
        </a>
    </div>
</section>

<!-- =============== BOOKING SECTION =============== -->
<section id="dat-lich" class="relative py-24 bg-gray-900 overflow-hidden">
    <div class="absolute inset-0 opacity-5" style="background-image:radial-gradient(#ffffff 1px, transparent 1px);background-size:28px 28px;"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600 rounded-full filter blur-[120px] opacity-20 pointer-events-none"></div>

    <div class="container mx-auto px-4 max-w-4xl relative z-10">
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl font-black text-white uppercase tracking-tight mb-3">
                Đặt Lịch <span class="text-orange-400">Gọi Thợ Ngay</span>
            </h2>
            <p class="text-gray-400 font-medium max-w-xl mx-auto text-sm">
                Kỹ thuật viên <strong class="text-white">Điện Máy Hiếu</strong> có mặt trong 30 phút tại Lấp Vò và vùng lân cận.
            </p>
        </div>

        <div class="glass-panel rounded-[28px] p-8 md:p-10 border border-white/10">
            <div id="thongbao_datlich" class="mb-6"></div>

            <div class="space-y-6">
                <!-- Row 1: Tên + SĐT -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-black text-gray-600 uppercase tracking-widest mb-2">
                            <i class="fa fa-user mr-1 text-blue-400"></i> Họ và Tên *
                        </label>
                        <input type="text" id="dl_ten"
                               class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 font-bold focus:border-blue-500 focus:bg-white outline-none transition-all"
                               placeholder="Tên của bạn" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-600 uppercase tracking-widest mb-2">
                            <i class="fa fa-phone mr-1 text-green-500"></i> Số Điện Thoại *
                        </label>
                        <input type="tel" id="dl_sdt"
                               class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 font-bold focus:border-blue-500 focus:bg-white outline-none transition-all"
                               placeholder="0939.354.937" required>
                    </div>
                </div>

                <!-- Row 2: Service Custom Dropdown (ADHD màu sắc) -->
                <div>
                    <label class="block text-xs font-black text-gray-600 uppercase tracking-widest mb-2">
                        <i class="fa fa-tools mr-1 text-orange-500"></i> Dịch Vụ Cần Xử Lý *
                    </label>
                    <div class="relative" id="svcWrapper">
                        <!-- Trigger -->
                        <div id="svcTrigger" class="svc-trigger" onclick="toggleServiceDropdown()">
                            <div class="svc-icon rounded-xl" style="background:#1a56db;width:36px;height:36px;font-size:16px;">
                                <i class="fa fa-snowflake" style="color:#fff"></i>
                            </div>
                            <div class="flex-1 text-left">
                                <div class="font-black text-gray-900 text-sm" id="svcName">Sửa chữa / Vệ sinh Máy Lạnh</div>
                                <div class="font-bold text-gray-400 text-xs" id="svcPrice">Từ 150.000đ · Vệ sinh cơ bản</div>
                            </div>
                            <i class="fa fa-chevron-down text-gray-400 text-xs transition-transform" id="svcArrow"></i>
                        </div>
                        <input type="hidden" id="dl_dichvu" value="Sửa Máy Lạnh - Từ 150.000đ">

                        <!-- Dropdown -->
                        <div class="service-dropdown" id="svcDropdown">

                            <!-- 🔵 Máy Lạnh -->
                            <div class="service-option" style="background:#eff6ff;border-left-color:#1a56db"
                                 onclick="pickService(this,'Sửa Máy Lạnh - Từ 150.000đ','#1a56db','fa-snowflake','Sửa chữa / Vệ sinh Máy Lạnh','Từ 150.000đ · Vệ sinh | 300.000đ+ Sửa lớn')">
                                <div class="svc-icon" style="background:#1a56db"><i class="fa fa-snowflake"></i></div>
                                <div class="svc-info">
                                    <div class="svc-name" style="color:#1a56db">❄️ Sửa chữa / Vệ sinh Máy Lạnh</div>
                                    <div class="svc-price text-blue-600">🔵 Vệ sinh cơ bản: 150.000đ &nbsp;|&nbsp; Sửa chữa: 300.000đ+</div>
                                </div>
                                <span class="svc-badge" style="background:#1a56db">HOT</span>
                            </div>

                            <!-- 🟠 Tủ Lạnh -->
                            <div class="service-option" style="background:#fff7ed;border-left-color:#ea580c"
                                 onclick="pickService(this,'Sửa Tủ Lạnh - Từ 200.000đ','#ea580c','fa-temperature-low','Sửa chữa Tủ Lạnh','Từ 200.000đ · Coi máy miễn phí')">
                                <div class="svc-icon" style="background:#ea580c"><i class="fa fa-temperature-low"></i></div>
                                <div class="svc-info">
                                    <div class="svc-name" style="color:#ea580c">🟠 Sửa chữa Tủ Lạnh</div>
                                    <div class="svc-price text-orange-600">🔧 Từ 200.000đ &nbsp;|&nbsp; Coi máy miễn phí tại nhà</div>
                                </div>
                            </div>

                            <!-- 🟢 Máy Giặt -->
                            <div class="service-option" style="background:#f0fdf4;border-left-color:#16a34a"
                                 onclick="pickService(this,'Sửa Máy Giặt - Từ 150.000đ','#16a34a','fa-tshirt','Sửa chữa Máy Giặt','Từ 150.000đ · Vệ sinh lồng giặt: 100.000đ')">
                                <div class="svc-icon" style="background:#16a34a"><i class="fa fa-tshirt"></i></div>
                                <div class="svc-info">
                                    <div class="svc-name" style="color:#16a34a">🟢 Sửa chữa Máy Giặt</div>
                                    <div class="svc-price text-green-700">🧺 Từ 150.000đ &nbsp;|&nbsp; Vệ sinh lồng giặt: 100.000đ</div>
                                </div>
                            </div>

                            <!-- 🟣 Tivi -->
                            <div class="service-option" style="background:#faf5ff;border-left-color:#7c3aed"
                                 onclick="pickService(this,'Sửa Tivi - Từ 200.000đ','#7c3aed','fa-tv','Sửa chữa Tivi / Smart TV','Từ 200.000đ · Hỗ trợ mọi hãng')">
                                <div class="svc-icon" style="background:#7c3aed"><i class="fa fa-tv"></i></div>
                                <div class="svc-info">
                                    <div class="svc-name" style="color:#7c3aed">🟣 Sửa chữa Tivi / Smart TV</div>
                                    <div class="svc-price text-purple-700">📺 Từ 200.000đ &nbsp;|&nbsp; Samsung, LG, Sony, Xiaomi...</div>
                                </div>
                            </div>

                            <!-- 🩷 Máy Bơm / Điện Nước -->
                            <div class="service-option" style="background:#fdf2f8;border-left-color:#db2777"
                                 onclick="pickService(this,'Sửa Máy Bơm / Điện Nước - Từ 100.000đ','#db2777','fa-tint','Sửa Máy Bơm / Điện Nước','Từ 100.000đ · Lắp đặt báo giá miễn phí')">
                                <div class="svc-icon" style="background:#db2777"><i class="fa fa-tint"></i></div>
                                <div class="svc-info">
                                    <div class="svc-name" style="color:#db2777">🩷 Sửa Máy Bơm / Điện Nước</div>
                                    <div class="svc-price text-pink-700">💧 Từ 100.000đ &nbsp;|&nbsp; Lắp đặt: báo giá miễn phí</div>
                                </div>
                            </div>

                            <!-- 🟡 In 3D -->
                            <div class="service-option" style="background:#fefce8;border-left-color:#d97706"
                                 onclick="pickService(this,'Đặt In 3D - Báo giá miễn phí','#d97706','fa-cube','Đặt thiết kế / In 3D','Giá theo thiết kế · Báo giá miễn phí trong 5 phút')">
                                <div class="svc-icon" style="background:#d97706"><i class="fa fa-cube"></i></div>
                                <div class="svc-info">
                                    <div class="svc-name" style="color:#d97706">🟡 Đặt thiết kế / In 3D</div>
                                    <div class="svc-price text-yellow-700">🎨 Theo thiết kế &nbsp;|&nbsp; Báo giá trong 5 phút</div>
                                </div>
                                <span class="svc-badge" style="background:#d97706">MỚI</span>
                            </div>

                            <!-- ⚫ Khác -->
                            <div class="service-option" style="background:#f9fafb;border-left-color:#6b7280"
                                 onclick="pickService(this,'Dịch vụ khác - Liên hệ báo giá','#6b7280','fa-question-circle','Dịch vụ khác...','Liên hệ để được báo giá tốt nhất')">
                                <div class="svc-icon" style="background:#6b7280"><i class="fa fa-question-circle"></i></div>
                                <div class="svc-info">
                                    <div class="svc-name" style="color:#374151">⚫ Dịch vụ khác</div>
                                    <div class="svc-price text-gray-600">📞 Liên hệ để được tư vấn & báo giá</div>
                                </div>
                            </div>

                        </div><!-- /dropdown -->
                    </div><!-- /relative wrapper -->
                </div>

                <!-- Row 3: Address + GPS -->
                <div>
                    <label class="block text-xs font-black text-gray-600 uppercase tracking-widest mb-2">
                        <i class="fa fa-map-marker-alt mr-1 text-red-500"></i> Địa Chỉ Giao Dịch *
                    </label>
                    <input type="text" id="dl_diachi"
                           class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 font-bold focus:border-blue-500 focus:bg-white outline-none transition-all mb-3"
                           placeholder="Số nhà, Tên đường, Xã/Phường, Huyện, Tỉnh" required>
                    <div class="flex flex-col sm:flex-row items-center justify-between bg-blue-50 border border-blue-100 p-3 rounded-xl gap-3">
                        <div id="location_status" class="text-sm font-bold text-gray-600 flex items-center">
                            <i class="fa fa-satellite-dish text-blue-400 mr-2"></i> Chưa định vị GPS (giúp thợ tìm nhà nhanh hơn)
                        </div>
                        <button type="button" onclick="getLocation()"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2 px-5 rounded-lg text-xs flex items-center gap-2 transition-colors w-full sm:w-auto justify-center">
                            <i class="fa fa-location-arrow"></i> Chia sẻ GPS
                        </button>
                    </div>
                </div>

                <!-- Row 4: Mô tả -->
                <div>
                    <label class="block text-xs font-black text-gray-600 uppercase tracking-widest mb-2">
                        <i class="fa fa-comment-alt mr-1 text-purple-400"></i> Mô tả tình trạng (không bắt buộc)
                    </label>
                    <textarea id="dl_yeucau" rows="3"
                              class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 font-bold focus:border-blue-500 focus:bg-white outline-none transition-all resize-none"
                              placeholder="VD: Máy lạnh chảy nước, không lạnh, có tiếng ồn lạ..."></textarea>
                </div>

                <!-- Submit -->
                <button type="button" id="btnDatLich"
                        class="w-full btn-adhd text-white font-black text-lg py-4 rounded-2xl flex items-center justify-center gap-3 uppercase tracking-wide">
                    <i class="fa fa-paper-plane"></i> Chốt Đơn & Gọi Thợ Ngay
                </button>
            </div>
        </div>
    </div>
</section>

<script>
/* ---- Custom Service Dropdown ---- */
function toggleServiceDropdown() {
    var dd = document.getElementById('svcDropdown');
    var trigger = document.getElementById('svcTrigger');
    var arrow = document.getElementById('svcArrow');
    dd.classList.toggle('open');
    trigger.classList.toggle('active');
    arrow.style.transform = dd.classList.contains('open') ? 'rotate(180deg)' : '';
}

function pickService(el, value, color, icon, name, price) {
    document.getElementById('dl_dichvu').value = value;
    document.getElementById('svcName').textContent = name;
    document.getElementById('svcPrice').textContent = price;

    var iconEl = document.getElementById('svcTrigger').querySelector('.svc-icon');
    iconEl.style.background = color;
    iconEl.className = 'svc-icon rounded-xl';
    iconEl.innerHTML = '<i class="fa ' + icon + '" style="color:#fff"></i>';

    document.getElementById('svcDropdown').classList.remove('open');
    document.getElementById('svcTrigger').classList.remove('active');
    document.getElementById('svcArrow').style.transform = '';
}

// Close dropdown on outside click
document.addEventListener('click', function(e) {
    if (!document.getElementById('svcWrapper').contains(e.target)) {
        document.getElementById('svcDropdown').classList.remove('open');
        document.getElementById('svcTrigger').classList.remove('active');
        document.getElementById('svcArrow').style.transform = '';
    }
});

/* ---- Geolocation ---- */
let userLat = "", userLng = "";
function getLocation() {
    $("#location_status").html('<span class="text-blue-500 font-bold"><i class="fa fa-spinner fa-spin mr-2"></i>Đang định vị...</span>');
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            userLat = pos.coords.latitude;
            userLng = pos.coords.longitude;
            $("#location_status").html('<span class="text-green-600 font-black"><i class="fa fa-check-circle mr-2"></i>Định vị thành công!</span>');
        }, function() {
            $("#location_status").html('<span class="text-red-500 font-bold"><i class="fa fa-times-circle mr-2"></i>Không thể lấy vị trí.</span>');
        });
    } else {
        $("#location_status").html('<span class="text-red-500 font-bold">Trình duyệt không hỗ trợ GPS.</span>');
    }
}

/* ---- Booking Ajax ---- */
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
        url: "<?=BASE_URL('controller/client/DatLich.php');?>",
        method: "POST",
        data: { type:'DatLich', ten:ten, sdt:sdt, dichvu:$("#dl_dichvu").val(), diachi:diachi, yeucau:$("#dl_yeucau").val() },
        success: function(r) {
            $("#thongbao_datlich").html(r);
            $('#btnDatLich').html('<i class="fa fa-paper-plane"></i> Chốt Đơn & Gọi Thợ Ngay').prop('disabled',false).css('opacity','1');
        },
        error: function() {
            alert("Lỗi kết nối! Gọi trực tiếp: 0939.354.937");
            $('#btnDatLich').html('<i class="fa fa-paper-plane"></i> Chốt Đơn & Gọi Thợ Ngay').prop('disabled',false).css('opacity','1');
        }
    });
});
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
