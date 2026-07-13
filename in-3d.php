<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");
$title = "Mô hình Store | Điện Máy Hiếu";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");
?>

<div style="background-color: #f8fafc; min-height: 100vh; padding-bottom: 80px; font-family: 'Inter', sans-serif; color: #0f172a;">
    
    <!-- Hero Section -->
    <div style="background: #0ea5e9; border-bottom: 6px solid #0f172a; padding: 60px 20px; text-align: center; position: relative; overflow: hidden;">
        <!-- Pattern background for OCD -->
        <div style="position: absolute; top:0; left:0; right:0; bottom:0; opacity: 0.1; background-image: radial-gradient(#000 2px, transparent 2px); background-size: 20px 20px;"></div>
        
        <div style="position: relative; z-index: 10; max-width: 800px; margin: 0 auto;">
            <div style="display: inline-block; background: #fbbf24; color: #000; font-weight: 900; padding: 6px 16px; border: 3px solid #000; border-radius: 50px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 20px; box-shadow: 4px 4px 0px #000;">
                ⚡ Mô hình Store
            </div>
            <h1 style="font-size: 56px; font-weight: 900; color: #fff; text-shadow: 4px 4px 0px #0f172a; line-height: 1.1; margin-bottom: 20px; text-transform: uppercase; letter-spacing: -2px;">
                Mô hình Store - Gian hàng mô hình & In 3D
            </h1>
            <p style="font-size: 20px; font-weight: 700; color: #fff; background: #0f172a; display: inline-block; padding: 10px 20px; border-radius: 12px;">
                Độ chính xác cao - Vật liệu bền bỉ - Báo giá minh bạch
            </p>
        </div>
    </div>

    <!-- Main Content Container -->
    <div style="max-width: 1200px; margin: -40px auto 0; padding: 0 20px; position: relative; z-index: 20;">
        
        <!-- Service Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; margin-bottom: 80px;">
            
            <!-- Service 1: In bản mẫu có sẵn -->
            <div style="background: #fff; border: 4px solid #0f172a; border-radius: 24px; padding: 30px; box-shadow: 8px 8px 0px #0f172a; display: flex; flex-direction: column; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';">
                <div style="background: #10b981; width: 80px; height: 80px; border-radius: 20px; border: 4px solid #0f172a; display: flex; align-items: center; justify-content: center; font-size: 40px; margin-bottom: 24px; box-shadow: 4px 4px 0px #0f172a;">
                    🖨️
                </div>
                <h2 style="font-size: 28px; font-weight: 900; margin-bottom: 12px; text-transform: uppercase;">In mô hình 3D</h2>
                <p style="font-size: 16px; font-weight: 600; color: #475569; margin-bottom: 24px; flex-grow: 1; line-height: 1.5;">
                    Bạn đã có file STL/OBJ. Chúng tôi in mô hình 3D chất lượng cao, giá minh bạch.
                </p>
                <div style="background: #f1f5f9; padding: 16px; border-radius: 12px; border: 2px dashed #94a3b8; text-align: center; margin-bottom: 24px;">
                    <span style="font-size: 14px; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 4px;">Đơn giá siêu rẻ</span>
                    <span style="font-size: 32px; font-weight: 900; color: #10b981;">400đ <small style="font-size: 16px; font-weight: 700; color: #64748b;">/ gram</small></span>
                </div>
                <button onclick="document.getElementById('di_dichvu').value='In bản mẫu có sẵn (400đ/g)'; document.getElementById('order-form').scrollIntoView({behavior: 'smooth'})" style="background: #0f172a; color: #fff; border: none; padding: 16px; border-radius: 12px; font-size: 18px; font-weight: 800; cursor: pointer; text-transform: uppercase; letter-spacing: 1px;">Đặt In Ngay</button>
            </div>

            <!-- Service 2: Thiết kế bản in -->
            <div style="background: #fff; border: 4px solid #0f172a; border-radius: 24px; padding: 30px; box-shadow: 8px 8px 0px #0f172a; display: flex; flex-direction: column; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';">
                <div style="background: #a855f7; width: 80px; height: 80px; border-radius: 20px; border: 4px solid #0f172a; display: flex; align-items: center; justify-content: center; font-size: 40px; margin-bottom: 24px; box-shadow: 4px 4px 0px #0f172a;">
                    📐
                </div>
                <h2 style="font-size: 28px; font-weight: 900; margin-bottom: 12px; text-transform: uppercase;">Thiết kế mô hình</h2>
                <p style="font-size: 16px; font-weight: 600; color: #475569; margin-bottom: 24px; flex-grow: 1; line-height: 1.5;">
                    Bạn có ý tưởng hoặc vật mẫu cần copy. Kỹ sư sẽ dựng file 3D hoàn chỉnh rồi in cho bạn.
                </p>
                <div style="background: #f1f5f9; padding: 16px; border-radius: 12px; border: 2px dashed #94a3b8; text-align: center; margin-bottom: 24px;">
                    <span style="font-size: 14px; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 4px;">Đơn giá trọn gói</span>
                    <span style="font-size: 32px; font-weight: 900; color: #a855f7;">500đ <small style="font-size: 16px; font-weight: 700; color: #64748b;">/ gram</small></span>
                </div>
                <button onclick="document.getElementById('di_dichvu').value='Thiết kế & In (500đ/g)'; document.getElementById('order-form').scrollIntoView({behavior: 'smooth'})" style="background: #0f172a; color: #fff; border: none; padding: 16px; border-radius: 12px; font-size: 18px; font-weight: 800; cursor: pointer; text-transform: uppercase; letter-spacing: 1px;">Yêu Cầu Thiết Kế</button>
            </div>
            
        </div>

        <!-- Store Section (Gian Hàng Mô Hình In Sẵn) -->
        <div style="margin-bottom: 80px;">
            <div style="text-align: center; margin-bottom: 40px;">
                <h2 style="font-size: 40px; font-weight: 900; text-transform: uppercase; letter-spacing: -1px; margin-bottom: 10px; color: #0f172a;">Mô hình Store</h2>
                <div style="width: 80px; height: 6px; background: #f43f5e; margin: 0 auto; border-radius: 3px;"></div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 24px;">
                <?php
                $products_3d = $DMH->get_list("SELECT p.*, c.name as category_name FROM `products` p LEFT JOIN `product_categories` c ON c.id = p.category_id WHERE p.`type` = '3d' AND p.`status` = 1 ORDER BY p.`id` DESC");
                if ($products_3d):
                    foreach($products_3d as $p):
                ?>
                <div style="background: #fff; border: 4px solid #0f172a; border-radius: 20px; padding: 16px; box-shadow: 6px 6px 0px #0f172a; text-align: center; display: flex; flex-direction: column;">
                    <div style="background: #e2e8f0; height: 180px; border-radius: 12px; margin-bottom: 16px; display: flex; align-items: center; justify-content: center; border: 2px solid #cbd5e1; overflow: hidden;">
                        <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.outerHTML='<div style=\'font-size: 50px;\'>📦</div>'"/>
                    </div>
                    <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 8px; color: #0f172a;"><?= htmlspecialchars($p['name']) ?></h3>
                    <div style="font-size: 24px; font-weight: 900; color: #f43f5e; margin-bottom: 16px; flex-grow: 1;"><?= number_format($p['price']) ?>đ</div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <button onclick="addToCart(<?= $p['id'] ?>)" style="width: 100%; background: #e2e8f0; border: 3px solid #0f172a; color: #0f172a; font-weight: 900; font-size: 14px; padding: 12px 8px; border-radius: 12px; box-shadow: 3px 3px 0px #0f172a; cursor: pointer; transition: transform 0.1s;" onmousedown="this.style.transform='translate(2px, 2px)'; this.style.boxShadow='1px 1px 0px #0f172a';" onmouseup="this.style.transform='none'; this.style.boxShadow='3px 3px 0px #0f172a';"><i class="fa-solid fa-cart-plus"></i> Thêm giỏ</button>
                        <button onclick="buyNow(<?= $p['id'] ?>)" style="width: 100%; background: #fbbf24; border: 3px solid #0f172a; color: #000; font-weight: 900; font-size: 14px; padding: 12px 8px; border-radius: 12px; box-shadow: 3px 3px 0px #0f172a; cursor: pointer; transition: transform 0.1s;" onmousedown="this.style.transform='translate(2px, 2px)'; this.style.boxShadow='1px 1px 0px #0f172a';" onmouseup="this.style.transform='none'; this.style.boxShadow='3px 3px 0px #0f172a';"><i class="fa-solid fa-bolt"></i> Mua Ngay</button>
                    </div>
                </div>
                <?php 
                    endforeach;
                else: 
                ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #64748b; font-weight: bold;">Đang cập nhật thêm mẫu mới...</div>
                <?php endif; ?>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <p style="font-weight: 800; color: #94a3b8; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">(Gian hàng đang liên tục cập nhật thêm mẫu mới...)</p>
            </div>
        </div>

        <!-- Form Đặt In -->
        <div id="order-form" style="background: #0f172a; border: 4px solid #fff; border-radius: 32px; padding: 40px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); color: #fff; position: relative;">
            <div style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); background: #10b981; color: #fff; font-weight: 900; padding: 8px 24px; border-radius: 50px; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.2); text-transform: uppercase; letter-spacing: 1px;">
                Hỗ trợ 24/7
            </div>
            
            <div style="text-align: center; margin-bottom: 40px; margin-top: 20px;">
                <h2 style="font-size: 36px; font-weight: 900; text-transform: uppercase; color: #38bdf8; letter-spacing: -1px; margin-bottom: 8px;">Gửi Yêu Cầu Đặt In / Thiết Kế</h2>
                <p style="font-size: 16px; color: #94a3b8; font-weight: 600;">Điền thông tin bên dưới, chúng tôi sẽ báo giá qua Zalo ngay lập tức!</p>
            </div>

            <div id="thongbao_datin" style="margin-bottom: 24px;"></div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #fbbf24; margin-bottom: 10px;">Dịch vụ bạn cần</label>
                    <select id="di_dichvu" style="width: 100%; background: #1e293b; border: 3px solid #334155; color: #fff; padding: 16px 20px; border-radius: 16px; font-size: 16px; font-weight: 800; outline: none; transition: border 0.2s;" onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='#334155'">
                        <option value="In bản mẫu có sẵn (400đ/g)">In bản mẫu có sẵn (400đ/g)</option>
                        <option value="Thiết kế & In (500đ/g)">Thiết kế & In 3D (500đ/g)</option>
                        <?php
                        if (!empty($products_3d)) {
                            foreach($products_3d as $p) {
                                echo '<option value="Mua: '.htmlspecialchars($p['name'], ENT_QUOTES).'">Mua: '.htmlspecialchars($p['name']).' ('.number_format($p['price']).'đ)</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #fbbf24; margin-bottom: 10px;">Tên của bạn</label>
                    <input type="text" id="di_ten" placeholder="Nhập tên của bạn..." style="width: 100%; background: #1e293b; border: 3px solid #334155; color: #fff; padding: 16px 20px; border-radius: 16px; font-size: 16px; font-weight: 700; outline: none; transition: border 0.2s;" onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='#334155'">
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #fbbf24; margin-bottom: 10px;">Số điện thoại / Zalo</label>
                <input type="tel" id="di_sdt" placeholder="09xx.xxx.xxx" style="width: 100%; background: #1e293b; border: 3px solid #334155; color: #fff; padding: 16px 20px; border-radius: 16px; font-size: 16px; font-weight: 700; outline: none; transition: border 0.2s;" onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='#334155'">
            </div>

            <div style="margin-bottom: 32px;">
                <label style="display: block; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #fbbf24; margin-bottom: 10px;">Yêu cầu chi tiết</label>
                <textarea id="di_mota" rows="4" placeholder="Mô tả kích thước, màu sắc mong muốn hoặc link tải file STL nếu có..." style="width: 100%; background: #1e293b; border: 3px solid #334155; color: #fff; padding: 16px 20px; border-radius: 16px; font-size: 16px; font-weight: 700; outline: none; resize: none; transition: border 0.2s;" onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='#334155'"></textarea>
            </div>

            <button id="btnDatIn" style="width: 100%; background: #38bdf8; color: #0f172a; border: none; padding: 20px; border-radius: 16px; font-size: 20px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 15px rgba(56, 189, 248, 0.4);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(56, 189, 248, 0.6)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(56, 189, 248, 0.4)';">
                <i class="fa-solid fa-paper-plane" style="margin-right: 8px;"></i> GỬI YÊU CẦU NGAY
            </button>
        </div>
        
    </div>
</div>

<script>
$("#btnDatIn").on("click", function() {
    var dichvu = $("#di_dichvu").val();
    var ten = $("#di_ten").val().trim();
    var sdt = $("#di_sdt").val().trim();
    var mota = $("#di_mota").val().trim();
    
    if (!ten || !sdt || !mota) {
        alert("Vui lòng điền đầy đủ Tên, SĐT và Yêu cầu chi tiết!");
        return;
    }
    
    var originalText = $(this).html();
    $(this).html('<i class="fa-solid fa-spinner fa-spin"></i> ĐANG GỬI...').prop('disabled', true).css('opacity','0.7');
    
    $.ajax({
        url: "/controller/client/DatLich.php",
        method: "POST",
        data: { 
            type: 'DatLich', 
            ten: ten, 
            sdt: sdt, 
            dichvu: "DỊCH VỤ 3D: " + dichvu, 
            diachi: "Khách Đặt In 3D Online", 
            yeucau: mota 
        },
        success: function(r) {
            $("#thongbao_datin").html(r);
            $('#btnDatIn').html('<i class="fa-solid fa-check"></i> GỬI THÀNH CÔNG!').prop('disabled',false).css('opacity','1').css('background','#10b981').css('boxShadow','0 4px 15px rgba(16, 185, 129, 0.4)');
            setTimeout(() => {
                $('#btnDatIn').html(originalText).css('background','#38bdf8').css('boxShadow','0 4px 15px rgba(56, 189, 248, 0.4)');
                $("#di_ten").val('');
                $("#di_sdt").val('');
                $("#di_mota").val('');
            }, 3000);
        },
        error: function() {
            alert("Lỗi hệ thống! Vui lòng gửi Zalo trực tiếp: 0939.354.937");
            $('#btnDatIn').html(originalText).prop('disabled',false).css('opacity','1');
        }
    });
});
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
