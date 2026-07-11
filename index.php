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
   ĐIỆN MÁY HIẾU - NAVY BLUE / BLACK / WHITE FENG SHUI
   ============================================ */

:root {
    --dmh-navy:      #0A192F;
    --dmh-black:     #111111;
    --dmh-white:     #FFFFFF;
    --dmh-gray:      #F8F9FA;
    --dmh-light-navy:#112240;
}

body {
    background-color: var(--dmh-gray);
}

.hero-bg {
    background-color: var(--dmh-navy);
    background-image: radial-gradient(circle at top right, var(--dmh-light-navy) 0%, var(--dmh-navy) 70%);
}

.card-hover {
    transition: all 0.3s ease;
}
.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(10, 25, 47, 0.15);
    border-color: var(--dmh-navy);
}

.btn-navy {
    background-color: var(--dmh-navy);
    color: var(--dmh-white);
    transition: all 0.3s ease;
}
.btn-navy:hover {
    background-color: var(--dmh-black);
    transform: translateY(-2px);
}

.btn-outline-navy {
    background-color: transparent;
    border: 2px solid var(--dmh-white);
    color: var(--dmh-white);
    transition: all 0.3s ease;
}
.btn-outline-navy:hover {
    background-color: var(--dmh-white);
    color: var(--dmh-navy);
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

<!-- =============== HERO =============== -->
<section class="hero-bg relative w-full pt-20 pb-28 overflow-hidden">
    <div class="container mx-auto px-4 max-w-6xl relative z-10 text-center">
        <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-white/10 border border-white/20 text-white text-xs font-black uppercase tracking-widest mb-8">
            <i class="fa fa-certificate"></i>
            Mã DN: 1402228630 · Đồng Tháp
        </div>

        <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-white leading-tight mb-6 uppercase tracking-tight">
            ĐIỆN MÁY <span class="text-white">HIẾU</span>
        </h1>
        <p class="text-2xl md:text-3xl text-gray-300 font-bold mb-8 max-w-3xl mx-auto">
            Hệ sinh thái Điện Máy, Sửa Chữa & In 3D hàng đầu tại Lấp Vò
        </p>
        
        <p class="text-white/80 text-lg mb-12 font-medium">
            📍 Số 166 Ấp Bình Thạnh 1, Xã Lấp Vò, Tỉnh Đồng Tháp<br/>
            📞 <a href="tel:0939354937" class="text-white font-black hover:text-gray-300 underline underline-offset-4">0939.354.937</a>
        </p>

        <div class="flex flex-col sm:flex-row gap-6 justify-center">
            <a href="/goi-tho.php" class="bg-white text-[#0A192F] hover:bg-gray-100 font-black px-10 py-5 rounded-xl uppercase tracking-widest text-lg flex items-center justify-center gap-3 transition-all shadow-xl hover:shadow-2xl">
                <i class="fa fa-tools"></i> Đặt Lịch Gọi Thợ
            </a>
            <a href="/in-3d.php" class="btn-outline-navy font-black px-10 py-5 rounded-xl uppercase tracking-widest text-lg flex items-center justify-center gap-3 shadow-xl hover:shadow-2xl">
                <i class="fa fa-cube"></i> Dịch Vụ In 3D
            </a>
        </div>
    </div>
</section>

<!-- =============== BUSINESS PILLARS =============== -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-black text-[#0A192F] uppercase tracking-tight section-title">
                Dịch Vụ Cốt Lõi
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Điện Máy -->
            <div class="bg-white rounded-2xl p-8 border-2 border-gray-100 card-hover text-center">
                <div class="w-20 h-20 bg-[#0A192F] rounded-2xl flex items-center justify-center text-white text-4xl mb-6 mx-auto shadow-lg">
                    <i class="fa fa-tv"></i>
                </div>
                <h3 class="text-2xl font-black text-[#0A192F] uppercase mb-4 tracking-tight">Điện Máy & Gia Dụng</h3>
                <p class="text-gray-600 font-medium text-base mb-8">Tivi, Tủ lạnh, Máy giặt chính hãng. Giá kho, giao hàng tốc hành trong 2h tại Đồng Tháp.</p>
                <a href="/dien-may" class="inline-flex items-center justify-center w-full bg-[#0A192F] hover:bg-black text-white font-bold py-3 px-6 rounded-lg uppercase tracking-wider transition-colors">
                    Xem Sản Phẩm
                </a>
            </div>
            
            <!-- Gọi Thợ -->
            <div class="bg-white rounded-2xl p-8 border-2 border-gray-100 card-hover text-center">
                <div class="w-20 h-20 bg-black rounded-2xl flex items-center justify-center text-white text-4xl mb-6 mx-auto shadow-lg">
                    <i class="fa fa-wrench"></i>
                </div>
                <h3 class="text-2xl font-black text-black uppercase mb-4 tracking-tight">Gọi Thợ Sửa Chữa</h3>
                <p class="text-gray-600 font-medium text-base mb-8">Xử lý thần tốc sự cố điện lạnh, điện nước. Đội ngũ kỹ thuật viên chuyên nghiệp, tận tâm.</p>
                <a href="/goi-tho.php" class="inline-flex items-center justify-center w-full bg-black hover:bg-gray-800 text-white font-bold py-3 px-6 rounded-lg uppercase tracking-wider transition-colors">
                    Đặt Lịch Ngay
                </a>
            </div>
            
            <!-- In 3D -->
            <div class="bg-white rounded-2xl p-8 border-2 border-gray-100 card-hover text-center">
                <div class="w-20 h-20 bg-white border-4 border-[#0A192F] rounded-2xl flex items-center justify-center text-[#0A192F] text-4xl mb-6 mx-auto shadow-lg">
                    <i class="fa fa-cube"></i>
                </div>
                <h3 class="text-2xl font-black text-[#0A192F] uppercase mb-4 tracking-tight">Sản Phẩm In 3D</h3>
                <p class="text-gray-600 font-medium text-base mb-8">Thiết kế, chế tạo linh kiện & mô hình kỹ thuật. Bảng giá minh bạch 500đ/1 gram nhựa.</p>
                <a href="/in-3d.php" class="inline-flex items-center justify-center w-full bg-white border-2 border-[#0A192F] text-[#0A192F] hover:bg-[#0A192F] hover:text-white font-bold py-3 px-6 rounded-lg uppercase tracking-wider transition-colors">
                    Xem Bảng Giá In 3D
                </a>
            </div>
        </div>
    </div>
</section>

<!-- =============== SHOWCASE =============== -->
<section id="bang-gia" class="container mx-auto px-4 max-w-6xl py-20">
    <div class="text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-black text-[#0A192F] uppercase tracking-tight section-title">
            Điện Máy Nổi Bật
        </h2>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php
        $mock_products = [
            ['id'=>101, 'title'=>'Smart Tivi Samsung 4K 65 inch 65AU7700', 'img'=>'https://cdn.tgdd.vn/Products/Images/1942/235889/samsung-ua65au7700-1-600x400.jpg', 'price'=>12500000, 'discount'=>15],
            ['id'=>102, 'title'=>'Máy lạnh Daikin Inverter 1.5 HP ATKF35', 'img'=>'https://cdn.tgdd.vn/Products/Images/2002/272714/daikin-inverter-15-hp-atkf35xvmv-1.jpg', 'price'=>11290000, 'discount'=>10],
            ['id'=>103, 'title'=>'Tủ lạnh Panasonic Inverter 322 lít', 'img'=>'https://cdn.tgdd.vn/Products/Images/1943/220268/panasonic-nr-bc360qkvn-600x400.jpg', 'price'=>9890000, 'discount'=>5],
            ['id'=>104, 'title'=>'Máy giặt LG Inverter 9 kg FV1409S4W', 'img'=>'https://cdn.tgdd.vn/Products/Images/1944/227121/lg-inverter-9-kg-fv1409s4w-1-600x400.jpg', 'price'=>8490000, 'discount'=>12]
        ];

        foreach($mock_products as $row):
            $price = $row['price'];
            $discount = $row['discount'];
            $finalPrice = $price - ($price * $discount / 100);
        ?>
        <div class="bg-white rounded-xl p-5 border border-gray-200 hover:border-[#0A192F] flex flex-col hover:-translate-y-1 hover:shadow-xl transition-all duration-300 relative group overflow-hidden">
            <div class="absolute top-3 left-3 z-10 bg-black text-white text-[10px] font-black px-2 py-1 rounded-sm shadow-sm uppercase">
                Giảm <?=$discount;?>%
            </div>
            
            <div class="w-full aspect-square bg-white mb-4 flex items-center justify-center p-2 relative">
                <img src="<?=$row['img'];?>" class="max-h-full max-w-full object-contain transition-transform duration-500 group-hover:scale-105" alt="<?=$row['title'];?>">
            </div>
            
            <h4 class="font-bold text-[#0A192F] text-sm md:text-base leading-snug line-clamp-2 mb-3"><?=$row['title'];?></h4>
            
            <div class="mt-auto pt-3 border-t border-gray-100">
                <div class="flex flex-col mb-4">
                    <div class="text-xs text-gray-400 line-through font-semibold mb-1"><?=number_format($price,0,',','.');?>đ</div>
                    <div class="text-lg md:text-xl font-black text-black leading-none"><?=number_format($finalPrice,0,',','.');?>đ</div>
                </div>
                
                <a href="#" class="w-full block text-center border-2 border-[#0A192F] text-[#0A192F] hover:bg-[#0A192F] hover:text-white text-xs font-black py-2.5 px-4 rounded-lg transition-colors uppercase tracking-widest">
                    Mua Ngay
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="mt-12 text-center">
        <a href="/dien-may" class="inline-block border-b-2 border-[#0A192F] text-[#0A192F] font-black pb-1 hover:text-black hover:border-black uppercase tracking-widest transition-colors">
            Xem Toàn Bộ Sản Phẩm <i class="fa fa-arrow-right ml-2"></i>
        </a>
    </div>
</section>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
