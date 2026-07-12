<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");

if (!isset($DMH)) {
    $DMH = new DMH();
}

$title = "Điện Máy Hiếu - Marketplace & Gọi Thợ";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");

$productError = '';
$products = $DMH->get_list("SELECT * FROM `store_products` WHERE `status` = 'ACTIVE' ORDER BY id DESC LIMIT 60");
if (!is_array($products)) {
    $products = [];
}

$services = array(
    array('group' => 'Thợ điện lạnh', 'name' => 'Vệ sinh máy lạnh', 'base' => 150000, 'note' => 'Trọn gói (Không phí ẩn)'),
    array('group' => 'Thợ điện lạnh', 'name' => 'Lắp đặt máy lạnh 1HP / 1.5HP', 'base' => 400000, 'note' => 'Chưa gồm vật tư phát sinh'),
    array('group' => 'Thợ điện lạnh', 'name' => 'Lắp đặt máy lạnh 2HP / 3HP', 'base' => 500000, 'note' => 'Chưa gồm vật tư phát sinh'),
    array('group' => 'Thợ điện lạnh', 'name' => 'Máy lạnh âm trần', 'base' => 0, 'note' => 'Khảo sát và báo giá riêng'),
    array('group' => 'Thợ điện lạnh', 'name' => 'Sửa chữa điện lạnh', 'base' => 200000, 'note' => 'Công thợ + linh kiện công khai'),
    array('group' => 'Thợ tivi', 'name' => 'Treo tivi (32-43")', 'base' => 150000, 'note' => 'Công thợ + giá khung treo công khai'),
    array('group' => 'Thợ tivi', 'name' => 'Treo tivi (50-55")', 'base' => 200000, 'note' => 'Công thợ + giá khung treo công khai'),
    array('group' => 'Thợ tivi', 'name' => 'Treo tivi (65-75")', 'base' => 300000, 'note' => 'Công thợ + giá khung treo công khai'),
    array('group' => 'Thợ máy lọc nước', 'name' => 'Lắp máy lọc nước', 'base' => 200000, 'note' => 'Công thợ + phụ kiện'),
    array('group' => 'Thợ gia dụng', 'name' => 'Lắp máy giặt', 'base' => 200000, 'note' => 'Công thợ + phụ kiện'),
    array('group' => 'Thợ gia dụng', 'name' => 'Sửa điện gia dụng', 'base' => 100000, 'note' => 'Công thợ kiểm tra sửa chữa'),
    array('group' => 'Thợ điện thoại', 'name' => 'Kiểm tra / sửa điện thoại', 'base' => 100000, 'note' => 'Công thợ + linh kiện nếu có')
);
?>

<main>
    <div class="wrap storefront fade-in">
        
        <!-- Premium Hero Section -->
        <section class="hero">
            <div class="hero-main">
                <div class="blob"></div>
                <h1>Điện Máy Hiếu</h1>
                <p>Hệ sinh thái bán lẻ, dịch vụ sửa chữa, in mô hình 3D dựa trên nền tảng công nghệ số tư nhân do chính công ty phát hành.</p>
                <div class="hero-actions">
                    <a class="btn light" href="#products"><i class="fa-solid fa-shopping-cart"></i> Khám phá Sản phẩm</a>
                    <a class="btn accent" href="#goi-tho"><i class="fa-solid fa-tools"></i> Đặt lịch Gọi Thợ</a>
                    <a class="btn outline" href="/in-3d.php"><i class="fa-solid fa-cube"></i> Dịch vụ In 3D</a>
                </div>
            </div>
        </section>

        <!-- Products Section -->
        <section class="section" id="products">
            <div class="title">
                <h2>Sản phẩm Nổi bật</h2>
                <span class="muted"><?= count($products) ?> sản phẩm</span>
            </div>
            <div class="grid" id="productGrid">
                <?php if (empty($products)): ?>
                    <div class="empty">
                        <i class="fa-solid fa-box-open"></i>
                        Hiện chưa có sản phẩm nào
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <?php
                        $name = isset($p['name']) ? (string)$p['name'] : '';
                        $category = $p['type'] == '3d' ? 'Mô hình In 3D' : 'Điện Máy & Gia Dụng';
                        $image = isset($p['image']) ? (string)$p['image'] : '';
                        $price = isset($p['price']) ? (float)$p['price'] : 0;
                        ?>
                        <article class="product" data-name="<?= htmlspecialchars(strtolower($name)) ?>" data-category="<?= htmlspecialchars(strtolower($category)) ?>">
                            <div class="img">
                                <?php if ($image !== ''): ?>
                                    <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>" onerror="this.parentNode.textContent='Chưa có ảnh'">
                                <?php else: ?>
                                    <div style="color: var(--muted); font-size: 13px;">Chưa có ảnh</div>
                                <?php endif; ?>
                            </div>
                            <div class="body">
                                <div class="cat"><?= htmlspecialchars($category) ?></div>
                                <div class="name" title="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></div>
                                <div class="price"><?= number_format($price, 0, ',', '.') ?>đ</div>
                                <button onclick="addToCart(<?= $p['id'] ?>)" class="btn accent" style="width: 100%; padding: 8px; margin-top: 10px; font-size: 14px;"><i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ</button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Pricing & Services Section -->
        <section class="section" id="bang-gia" style="margin-top: 40px;">
            <div class="title" style="text-align: center; margin-bottom: 40px;">
                <h2 style="font-size: 32px; font-weight: 900; letter-spacing: -1px; color: var(--brand-accent);">BẢNG GIÁ DỊCH VỤ</h2>
                <span class="muted" style="display: block; margin-top: 8px; font-size: 16px;">Minh bạch - Trọn gói - Không phí ẩn</span>
            </div>
            
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; padding: 0 16px;">
                    <?php 
                    $colors = [
                        'Thợ điện lạnh' => '#38bdf8',
                        'Thợ tivi' => '#fbbf24',
                        'Thợ máy lọc nước' => '#10b981',
                        'Thợ gia dụng' => '#f43f5e',
                        'Thợ điện thoại' => '#a855f7'
                    ];
                    
                    foreach ($services as $item): 
                        $base = (int)$item['base']; 
                        $publicPrice = $base > 0 ? number_format($base, 0, ',', '.') . 'đ' : 'Khảo sát';
                        $c = isset($colors[$item['group']]) ? $colors[$item['group']] : '#38bdf8';
                    ?>
                    <div style="background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255,255,255,0.1); border-left: 4px solid <?= $c ?>; border-radius: 12px; padding: 16px; display: flex; flex-direction: column; justify-content: space-between; gap: 12px; transition: all 0.2s ease; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.2);" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='<?= $c ?>'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.1)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.2)'" onclick="document.getElementById('goi-tho').scrollIntoView({behavior: 'smooth'});">
                        <div>
                            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: <?= $c ?>; margin-bottom: 6px;"><?= htmlspecialchars($item['group']) ?></div>
                            <div style="font-size: 16px; font-weight: 700; color: #fff; line-height: 1.3;"><?= htmlspecialchars($item['name']) ?></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                            <div style="font-size: 12px; color: #94a3b8; max-width: 60%; line-height: 1.3;"><i class="fa-solid fa-circle-info" style="font-size: 10px; margin-right: 4px; opacity: 0.7;"></i><?= htmlspecialchars($item['note']) ?></div>
                            <div style="font-size: 17px; font-weight: 900; color: <?= $c ?>; background: rgba(0,0,0,0.3); padding: 4px 10px; border-radius: 6px;"><?= $publicPrice ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
        </section>

        <!-- Booking App Section -->
        <section class="section booking-shell" id="goi-tho" style="margin-top: 60px; max-width: 800px; margin-inline: auto;">
            <div class="title" style="text-align: center; margin-bottom: 30px;">
                <h2 style="font-size: 32px; font-weight: 900; letter-spacing: -1px;">GỌI THỢ NGAY</h2>
                <span class="muted" style="display: block; margin-top: 8px;">Điền thông tin - 15 phút thợ có mặt</span>
            </div>
            
            <form id="bookingForm" autocomplete="off">
                <div id="thongbao_datlich" style="margin-block-end: 24px;"></div>
                <h3>Thông tin Yêu cầu Dịch vụ</h3>
                <div class="form">
                    <div class="field full">
                        <label for="service_selector">1. Bạn cần dịch vụ gì? <span style="color:var(--brand-accent);">*</span></label>
                        <div id="custom-service-selector">
                            <?php 
                            $icons = [
                                'Vệ sinh máy lạnh' => '❄️',
                                'Lắp đặt máy lạnh 1HP / 1.5HP' => '🛠️',
                                'Lắp đặt máy lạnh 2HP / 3HP' => '🛠️',
                                'Máy lạnh âm trần' => '🏢',
                                'Sửa chữa điện lạnh' => '🔧',
                                'Treo tivi (32-43")' => '📺',
                                'Treo tivi (50-55")' => '📺',
                                'Treo tivi (65-75")' => '📺',
                                'Lắp máy lọc nước' => '💧',
                                'Lắp máy giặt' => '🧺',
                                'Sửa điện gia dụng' => '🔌',
                                'Kiểm tra / sửa điện thoại' => '📱'
                            ];
                            $colors = ['#38bdf8', '#fbbf24', '#10b981', '#f43f5e', '#a855f7', '#94a3b8'];
                            $c_idx = -1;
                            $currentGroup = '';
                            
                            foreach ($services as $svc): 
                                if ($svc['group'] !== $currentGroup) {
                                    if ($currentGroup !== '') echo '</div></div>';
                                    $c_idx++;
                                    $accent = $colors[$c_idx % count($colors)];
                                    echo '<div style="margin-bottom: 12px;">';
                                    echo '<div style="font-size: 13px; font-weight: 800; color: ' . $accent . '; margin-bottom: 6px; text-transform: uppercase;">' . htmlspecialchars($svc['group']) . '</div>';
                                    echo '<div style="display: flex; flex-wrap: wrap; gap: 8px;">';
                                    $currentGroup = $svc['group'];
                                }
                                $base = (int)$svc['base']; 
                                $publicPrice = $base > 0 ? number_format($base, 0, ',', '.') . ' VND' : 'Báo giá sau khi khảo sát';
                                $icon = isset($icons[$svc['name']]) ? $icons[$svc['name']] : '✨';
                            ?>
                                <button type="button" class="cute-btn" data-val="<?= htmlspecialchars($svc['name']) ?>" data-price="<?= htmlspecialchars($publicPrice) ?>" data-group="<?= htmlspecialchars($svc['group']) ?>" data-note="<?= htmlspecialchars($svc['note']) ?>" style="background: rgba(255,255,255,0.05); border: 2px solid rgba(255,255,255,0.1); color: #fff; padding: 8px 14px; border-radius: 20px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px;" onmouseover="this.style.background='rgba(255,255,255,0.1)';" onmouseout="if(!this.classList.contains('active')) this.style.background='rgba(255,255,255,0.05)';">
                                    <span style="font-size: 16px;"><?= $icon ?></span> <?= htmlspecialchars($svc['name']) ?>
                                </button>
                            <?php endforeach; ?>
                            <?php if ($currentGroup !== '') echo '</div></div>'; ?>
                        </div>
                    </div>
                    
                    <input type="hidden" id="service_type" name="service_type">
                    <input type="hidden" id="selected_service_name" name="selected_service_name">
                    
                    <div class="field full">
                        <label for="customer_price_display">Giá tham khảo (Đã gồm VAT)</label>
                        <input class="readonly-price" id="customer_price_display" type="text" readonly placeholder="Chọn dịch vụ ở trên để xem giá" style="color: var(--brand-accent); font-weight: bold; background: rgba(0,0,0,0.3);">
                        <small id="service_note" style="color: #94a3b8; display: block; margin-top: 8px; font-weight: 500;"></small>
                    </div>
                    
                    <div class="field">
                        <label for="customer_name">2. Tên của bạn <span style="color:var(--brand-accent);">*</span></label>
                        <input id="customer_name" name="customer_name" required maxlength="150" placeholder="VD: Anh Minh">
                    </div>
                    
                    <div class="field">
                        <label for="phone">3. Số điện thoại liên hệ <span style="color:var(--brand-accent);">*</span></label>
                        <input id="phone" name="phone" type="tel" inputmode="numeric" pattern="[0-9]{8,15}" required maxlength="15" placeholder="09xx.xxx.xxx">
                    </div>
                    
                    <div class="field full">
                        <label for="address">4. Địa chỉ chính xác <span style="color:var(--brand-accent);">*</span></label>
                        <input id="address" name="address" required maxlength="500" placeholder="Số nhà, tên đường, khu vực...">
                        <div class="map-actions">
                            <button class="btn accent" id="useCurrentLocation" type="button" style="padding: 10px 16px; font-size: 13px;"><i class="fa-solid fa-location-crosshairs"></i> Lấy tọa độ GPS hiện tại</button>
                            <button class="btn" id="clearLocation" type="button" style="padding: 10px 16px; font-size: 13px; background: rgba(255,255,255,0.1);"><i class="fa-solid fa-eraser"></i> Xóa</button>
                        </div>
                        <input type="hidden" id="map_location" name="map_location">
                        <input type="hidden" id="map_lat" name="map_lat">
                        <input type="hidden" id="map_lng" name="map_lng">
                        <div class="map-preview">
                            <div class="location-map" id="locationMap" aria-label="Bản đồ chọn vị trí"></div>
                            <div class="location-status" id="locationStatus">Bấm vào bản đồ để chọn điểm hoặc dùng nút Lấy GPS phía trên.</div>
                        </div>
                    </div>
                    
                    <div class="field full">
                        <label for="issue_description">5. Mô tả chi tiết vấn đề <span style="color:var(--brand-accent);">*</span></label>
                        <textarea id="issue_description" name="issue_description" required maxlength="2000" placeholder="Máy bị lỗi gì, hiện tượng như thế nào..."></textarea>
                    </div>
                </div>
                
                <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <p style="color: #94a3b8; font-size: 14px; margin-bottom: 20px;"><i class="fa-solid fa-circle-info"></i> Giá báo trên web là giá công khai. Nếu có phát sinh vật tư linh kiện, thợ sẽ báo giá chi tiết và xin phép bạn trước khi tiến hành sửa chữa.</p>
                    <button id="btnDatLich" class="btn accent" type="button" style="width: 100%; padding: 18px; font-size: 18px; font-weight: 800; letter-spacing: 0.5px; box-shadow: 0 10px 30px rgba(56,189,248,0.3);"><i class="fa-solid fa-paper-plane"></i> GỬI YÊU CẦU NGAY</button>
                </div>
            </form>
        </section>

    </div>
</main>

<script>
'use strict';

// Product Filtering
const cards = Array.from(document.querySelectorAll('.product'));
const searchInput = document.getElementById('searchInput');

function normalize(value) {
    return String(value || '').toLowerCase();
}

function filterProducts() {
    const q = normalize(searchInput.value);
    cards.forEach(card => {
        const okSearch = !q || card.dataset.name.indexOf(q) !== -1 || card.dataset.category.indexOf(q) !== -1;
        card.style.display = okSearch ? '' : 'none';
    });
}

if(searchInput) {
    searchInput.addEventListener('input', filterProducts);
}

// Service Selector Logic
const cuteBtns = document.querySelectorAll('.cute-btn');
cuteBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove active class from all
        cuteBtns.forEach(b => {
            b.classList.remove('active');
            b.style.background = 'rgba(255,255,255,0.05)';
            b.style.borderColor = 'rgba(255,255,255,0.1)';
            b.style.boxShadow = 'none';
        });
        
        // Add active class to clicked
        this.classList.add('active');
        this.style.background = 'var(--brand-accent)';
        this.style.borderColor = 'var(--brand-accent)';
        this.style.boxShadow = '0 4px 15px rgba(56, 189, 248, 0.4)';
        
        const group = this.dataset.group || '';
        const serviceName = this.dataset.val || '';
        const price = this.dataset.price || 'Liên hệ để báo giá chi tiết';
        const note = this.dataset.note || '';
        
        document.getElementById('service_type').value = group;
        document.getElementById('selected_service_name').value = serviceName;
        document.getElementById('customer_price_display').value = price;
        document.getElementById('service_note').innerHTML = `<i class="fa-solid fa-asterisk" style="font-size: 12px; margin-right: 4px;"></i> ${note}`;
        
        // Auto-fill issue description if it's empty
        const issueDesc = document.getElementById('issue_description');
        if (!issueDesc.value || issueDesc.value.length < 5) {
            issueDesc.value = 'Tôi cần ' + serviceName;
        }
    });
});

// Map Logic
const addressInput = document.getElementById('address');
const locationStatus = document.getElementById('locationStatus');
const defaultLocation = [10.357422, 105.522124];
let locationMap = null;
let locationMarker = null;

function setLocationStatus(text) {
    if (locationStatus) locationStatus.textContent = text;
}

function syncSelectedLocation(lat, lng, resolveAddress) {
    const latitude = Number(lat).toFixed(6);
    const longitude = Number(lng).toFixed(6);
    const coords = latitude + ',' + longitude;
    document.getElementById('map_location').value = coords;
    document.getElementById('map_lat').value = latitude;
    document.getElementById('map_lng').value = longitude;

    if (locationMap && window.L) {
        if (!locationMarker) {
            locationMarker = L.marker([lat, lng], {draggable: true}).addTo(locationMap);
            locationMarker.on('dragend', event => {
                const point = event.target.getLatLng();
                syncSelectedLocation(point.lat, point.lng, true);
            });
        } else {
            locationMarker.setLatLng([lat, lng]);
        }
    }

    if (!resolveAddress) return;

    setLocationStatus('Đang lấy địa chỉ từ GPS...');
    fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&accept-language=vi&lat=' + latitude + '&lon=' + longitude)
        .then(res => res.json())
        .then(data => {
            if (data.display_name) addressInput.value = data.display_name;
            setLocationStatus('Đã đồng bộ GPS: ' + coords);
        })
        .catch(() => setLocationStatus('Đã đồng bộ tọa độ. Vui lòng nhập thủ công phần địa chỉ chi tiết.'));
}

if (window.L && document.getElementById('locationMap')) {
    locationMap = L.map('locationMap').setView(defaultLocation, 14);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(locationMap);

    locationMap.on('click', event => syncSelectedLocation(event.latlng.lat, event.latlng.lng, true));
    window.setTimeout(() => locationMap.invalidateSize(), 100);
}

document.getElementById('useCurrentLocation')?.addEventListener('click', () => {
    if (!navigator.geolocation) {
        Swal.fire('Lỗi', 'Trình duyệt của bạn không hỗ trợ định vị GPS', 'error');
        return;
    }
    setLocationStatus('Đang xác định vị trí của bạn...');
    navigator.geolocation.getCurrentPosition(position => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        if (locationMap) locationMap.setView([lat, lng], 17);
        syncSelectedLocation(lat, lng, true);
    }, () => {
        Swal.fire('Lỗi', 'Không thể lấy được vị trí GPS. Vui lòng cấp quyền vị trí cho trang web.', 'error');
        setLocationStatus('Không thể định vị.');
    });
});

document.getElementById('clearLocation')?.addEventListener('click', () => {
    document.getElementById('map_location').value = '';
    document.getElementById('map_lat').value = '';
    document.getElementById('map_lng').value = '';
    addressInput.value = '';
    setLocationStatus('Đã xóa dữ liệu vị trí.');
    if (locationMap && locationMarker) {
        locationMap.removeLayer(locationMarker);
        locationMarker = null;
    }
});

// Booking Submit Logic
$("#btnDatLich").on("click", function() {
    var ten = $("#customer_name").val().trim();
    var sdt = $("#phone").val().trim();
    var diachi = $("#address").val().trim();
    var dichvu = $("#service_type").val() + " - " + $("#selected_service_name").val();
    var yeucau = $("#issue_description").val().trim();
    var lat = $("#map_lat").val();
    var lng = $("#map_lng").val();
    
    if (!$("#service_type").val()) {
        Swal.fire('Thiếu thông tin', 'Vui lòng chọn một dịch vụ!', 'warning');
        return;
    }
    
    if (!ten || !sdt || !diachi || !yeucau) {
        Swal.fire('Thiếu thông tin', 'Vui lòng điền đầy đủ Tên, SĐT, Địa chỉ và Mô tả!', 'warning');
        return;
    }
    
    let originalText = $(this).html();
    $(this).html('<i class="fa-solid fa-circle-notch fa-spin"></i> ĐANG GỬI...').prop('disabled', true).css('opacity','0.7');
    
    if (lat && lng) {
        diachi += " | GPS: " + lat + "," + lng + " (https://maps.google.com/?q=" + lat + "," + lng + ")";
    }
    
    $.ajax({
        url: "/controller/client/DatLich.php",
        method: "POST",
        data: { type:'DatLich', ten:ten, sdt:sdt, dichvu:dichvu, diachi:diachi, yeucau:yeucau },
        success: function(r) {
            $("#thongbao_datlich").html(r);
            $('#btnDatLich').html('<i class="fa-solid fa-check"></i> Đã Gửi Thành Công').prop('disabled', false).css('opacity','1');
            
            // Clear form after 2 seconds
            setTimeout(() => {
                $('#bookingForm')[0].reset();
                $('#btnDatLich').html(originalText);
                if (locationMap && locationMarker) {
                    locationMap.removeLayer(locationMarker);
                    locationMarker = null;
                }
            }, 3000);
        },
        error: function() {
            Swal.fire('Lỗi', 'Không thể kết nối đến máy chủ. Vui lòng gọi trực tiếp!', 'error');
            $('#btnDatLich').html(originalText).prop('disabled', false).css('opacity','1');
        }
    });
});

// Giỏ hàng - Add to Cart
function addToCart(productId) {
    $.ajax({
        url: "/controller/client/CartAction.php",
        method: "POST",
        data: { action: 'add_to_cart', product_id: productId },
        success: function(r) {
            try {
                let res = JSON.parse(r);
                if(res.status == 'success') {
                    showToast('Đã thêm vào giỏ hàng!', 'success');
                    if(typeof updateCartCount === "function") updateCartCount();
                } else {
                    Swal.fire('Thông báo', res.msg, 'warning');
                }
            } catch(e) {}
        }
    });
}
</script>

<div style="text-align: center; margin: 40px 0 20px; position: relative; z-index: 5;">
    <a href="/login.php" class="btn dark" style="padding: 10px 24px; font-size: 14px; border: 1px solid rgba(15,23,42,0.1);"><i class="fa-solid fa-user-gear"></i> Khu Vực Dành Cho Thợ (Portal)</a>
</div>

<!-- ============================================== -->
<!-- HỒ SƠ PHÁP LÝ DÀNH CHO BỘ CÔNG THƯƠNG (BCT)     -->
<!-- ============================================== -->
<section id="bct-policies" style="background: #0f172a; padding: 80px 20px; border-top: 1px solid rgba(255,255,255,0.05); font-family: 'Inter', sans-serif;">
    <div style="max-width: 1200px; margin: 0 auto;">
        
        <div style="text-align: center; margin-bottom: 50px;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: rgba(56, 189, 248, 0.1); border-radius: 50%; color: #38bdf8; font-size: 28px; margin-bottom: 20px;">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <h2 style="font-size: 36px; font-weight: 900; color: #f8fafc; letter-spacing: -1px; margin: 0 0 10px 0;">THÔNG TIN PHÁP LÝ</h2>
            <p style="color: #94a3b8; font-size: 16px; max-width: 600px; margin: 0 auto; line-height: 1.6;">
                Hệ thống Điện Máy Hiếu cam kết tuân thủ đầy đủ quy định của Bộ Công Thương về Thương Mại Điện Tử. Quý khách và Cán bộ có thể tra cứu nhanh các chính sách hoạt động dưới đây.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: 320px 1fr; gap: 40px; align-items: start;">
            
            <!-- Left: Tab Buttons -->
            <div id="bct-tabs" style="display: flex; flex-direction: column; gap: 8px; position: sticky; top: 100px;">
                <button class="bct-tab-btn active" data-target="pol-1"><i class="fa-solid fa-shield-halved"></i> Chính sách bảo mật</button>
                <button class="bct-tab-btn" data-target="pol-2"><i class="fa-solid fa-gavel"></i> Giải quyết khiếu nại</button>
                <button class="bct-tab-btn" data-target="pol-3"><i class="fa-solid fa-tag"></i> Chính sách giá</button>
                <button class="bct-tab-btn" data-target="pol-4"><i class="fa-solid fa-credit-card"></i> Chính sách thanh toán</button>
                <button class="bct-tab-btn" data-target="pol-5"><i class="fa-solid fa-ban"></i> Điều kiện & hạn chế</button>
                <button class="bct-tab-btn" data-target="pol-6"><i class="fa-solid fa-truck-fast"></i> Giao hàng & Đổi trả</button>
                <button class="bct-tab-btn" data-target="pol-7"><i class="fa-solid fa-headset"></i> Hỗ trợ trực tuyến</button>
            </div>

            <!-- Right: Tab Contents -->
            <div id="bct-contents" style="background: #1e293b; border: 1px solid rgba(255,255,255,0.05); border-radius: 24px; padding: 40px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
                
                <!-- 1. Bảo Mật -->
                <div class="bct-pane active" id="pol-1">
                    <h3>1. Chính Sách Bảo Mật Thông Tin (Theo Nghị định 13/2023/NĐ-CP & NĐ 248/2026/NĐ-CP)</h3>
                    <p><strong>a. Mục đích thu thập & Định danh:</strong> Việc thu thập dữ liệu trên website Điện Máy Hiếu bao gồm: Tên, email, số điện thoại, địa chỉ khách hàng. Theo quy định mới, các tài khoản Đối tác (Thợ kỹ thuật) bắt buộc phải thực hiện định danh qua hệ thống VNeID hoặc đối chiếu CCCD nhằm đảm bảo an toàn tuyệt đối cho khách hàng khi giao dịch tại nhà.</p>
                    <p><strong>b. Phạm vi sử dụng:</strong> Hệ thống sử dụng thông tin khách hàng cung cấp để liên hệ xác nhận đơn hàng, điều phối thợ kỹ thuật, gửi thông báo và giải quyết khiếu nại.</p>
                    <p><strong>c. Quyền của Chủ thể dữ liệu:</strong> Khách hàng có toàn quyền yêu cầu Điện Máy Hiếu cung cấp bản sao dữ liệu cá nhân, yêu cầu chỉnh sửa, hoặc rút lại sự đồng ý thu thập dữ liệu và yêu cầu xóa bỏ hoàn toàn tài khoản bằng cách liên hệ CSKH.</p>
                    <p><strong>d. Cam kết bảo mật:</strong> Không sử dụng, không chuyển giao hay tiết lộ cho bên thứ 3 khi không có sự cho phép, trừ trường hợp cơ quan pháp luật yêu cầu.</p>
                </div>

                <!-- 2. Khiếu Nại -->
                <div class="bct-pane" id="pol-2">
                    <h3>2. Phương Thức Tiếp Nhận & Giải Quyết Khiếu Nại</h3>
                    <p>Điện Máy Hiếu hoạt động dưới mô hình "Nền tảng TMĐT tích hợp" và chịu trách nhiệm liên đới trong việc bảo vệ quyền lợi người tiêu dùng theo Luật Bảo vệ quyền lợi người tiêu dùng 2023 và NĐ 248/2026/NĐ-CP.</p>
                    <div class="bct-box">
                        <strong>Quy trình 3 bước giải quyết khiếu nại:</strong>
                        <ul>
                            <li><strong>Bước 1 (Tiếp nhận):</strong> Khách hàng phản ánh qua Hotline: 0979.553.289 hoặc Chatbot Anh thiên Openclaw trên website.</li>
                            <li><strong>Bước 2 (Xử lý):</strong> Bộ phận CSKH xác minh trong vòng 24 giờ. Nếu thiệt hại phát sinh do lỗi hàng hóa hoặc hành vi của thợ thuộc hệ thống quản lý, Điện Máy Hiếu cam kết đứng ra bồi thường và xử lý triệt để.</li>
                            <li><strong>Bước 3 (Khắc phục):</strong> Liên hệ lại khách hàng để đưa ra phương án đền bù, đổi trả hoặc cử thợ khác đến khắc phục hoàn toàn miễn phí (áp dụng trong 48 giờ).</li>
                        </ul>
                    </div>
                </div>

                <!-- 3. Chính sách Giá -->
                <div class="bct-pane" id="pol-3">
                    <h3>3. Chính Sách Giá Cả & Dịch Vụ</h3>
                    <p>Chúng tôi cam kết tính minh bạch tuyệt đối về giá cả đối với toàn bộ hàng hóa và dịch vụ trên nền tảng.</p>
                    <ul>
                        <li><strong>Giá Sản Phẩm (Hàng hóa & In 3D):</strong> Giá niêm yết trên website là giá cuối cùng đã bao gồm Thuế Giá trị gia tăng (VAT). Giá này chưa bao gồm phí vận chuyển (nếu có).</li>
                        <li><strong>Giá Dịch Vụ Gọi Thợ:</strong> Bảng giá dịch vụ gọi thợ (ví dụ: Vệ sinh máy lạnh 150.000đ) là giá tiền công trọn gói cho một hạng mục cơ bản.</li>
                        <li><strong>Phát sinh linh kiện:</strong> Trong trường hợp sửa chữa cần thay thế linh kiện, vật tư, Thợ kỹ thuật bắt buộc phải báo giá cụ thể cho khách hàng và chỉ được tiến hành sửa chữa khi khách hàng đồng ý. Khách hàng có quyền từ chối nếu thấy giá vật tư không hợp lý mà không phải trả bất kỳ khoản phí khảo sát nào.</li>
                    </ul>
                </div>

                <!-- 4. Thanh toán -->
                <div class="bct-pane" id="pol-4">
                    <h3>4. Các Phương Thức Thanh Toán</h3>
                    <p>Nhằm mang đến sự tiện lợi tối đa cho quý khách, Điện Máy Hiếu áp dụng 2 hình thức thanh toán linh hoạt, an toàn và có đầy đủ chứng từ:</p>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                        <div class="bct-box" style="margin:0;">
                            <i class="fa-solid fa-money-bill-wave" style="font-size:24px; color:#10b981; margin-bottom:10px; display:block;"></i>
                            <strong>Thanh toán Tiền Mặt (COD)</strong>
                            <p style="font-size:14px; margin-top:5px; color:#94a3b8;">Khách hàng thanh toán trực tiếp cho Thợ Kỹ Thuật sau khi đã nghiệm thu công việc sửa chữa hoàn tất, hoặc thanh toán cho Shipper khi nhận hàng.</p>
                        </div>
                        <div class="bct-box" style="margin:0;">
                            <i class="fa-solid fa-building-columns" style="font-size:24px; color:#3b82f6; margin-bottom:10px; display:block;"></i>
                            <strong>Chuyển khoản Ngân Hàng (Mã QR)</strong>
                            <p style="font-size:14px; margin-top:5px; color:#94a3b8;">Chuyển khoản qua mã VietQR hoặc Internet Banking vào tài khoản công ty. Áp dụng cho các đơn hàng lớn hoặc khách hàng thanh toán từ xa.</p>
                        </div>
                    </div>
                </div>

                <!-- 5. Điều kiện hạn chế -->
                <div class="bct-pane" id="pol-5">
                    <h3>5. Điều Kiện & Hạn Chế Cung Cấp Dịch Vụ</h3>
                    <p>Để đảm bảo tuân thủ pháp luật và chất lượng dịch vụ, chúng tôi áp dụng các quy định sau:</p>
                    <ul>
                        <li><strong>Giới hạn địa lý (Vùng phục vụ):</strong> Dịch vụ Gọi Thợ tận nơi hiện tại chỉ áp dụng trong phạm vi <strong>Bán kính 15km tính từ Chợ Lấp Vò, Tỉnh Đồng Tháp</strong>. Các đơn hàng nằm ngoài khu vực này, hệ thống sẽ tự động từ chối hoặc thỏa thuận phụ thu phí di chuyển.</li>
                        <li><strong>Tư vấn Tự động (AI Chatbot):</strong> Chatbot "Anh thiên Openclaw" sử dụng trí tuệ nhân tạo để hỗ trợ nhanh 24/7. Các thông tin tư vấn kỹ thuật từ Chatbot mang tính chất tham khảo. Trong trường hợp phức tạp, quyết định cuối cùng phải dựa trên khảo sát thực tế của Thợ có chuyên môn.</li>
                        <li><strong>Hạn chế độ tuổi:</strong> Người mua hàng và đặt lịch yêu cầu dịch vụ phải từ đủ 15 tuổi trở lên. Trẻ em dưới 15 tuổi cần có sự giám sát của người lớn khi thợ đến làm việc tại nhà.</li>
                    </ul>
                </div>

                <!-- 6. Giao hàng Đổi trả -->
                <div class="bct-pane" id="pol-6">
                    <h3>6. Chính Sách Giao Hàng, Đổi Trả & Hoàn Tiền</h3>
                    <p>Chúng tôi luôn nỗ lực mang đến sự an tâm tuyệt đối khi khách hàng sử dụng dịch vụ và mua sắm.</p>
                    <div class="bct-box">
                        <strong style="color:#f8fafc;"><i class="fa-solid fa-truck-fast"></i> Giao hàng (Hàng hóa):</strong>
                        <p style="font-size:14.5px; margin-top:5px;">Thời gian giao hàng chuẩn trong khu vực là 2-4 giờ kể từ lúc chốt đơn. Các sản phẩm In 3D cần thời gian chế tác sẽ được hẹn cụ thể (từ 1-3 ngày). Phí vận chuyển áp dụng biểu phí tiêu chuẩn của các đơn vị GHTK, Viettel Post.</p>
                    </div>
                    <div class="bct-box" style="margin-top:15px;">
                        <strong style="color:#f8fafc;"><i class="fa-solid fa-rotate-left"></i> Đổi Trả & Hoàn Tiền:</strong>
                        <p style="font-size:14.5px; margin-top:5px;">- <strong>Sản phẩm vật lý:</strong> 1 đổi 1 trong vòng 7 ngày nếu có lỗi do nhà sản xuất. Sản phẩm đổi trả phải còn nguyên tem, mác, không bị rơi vỡ hay vào nước.<br>- <strong>Dịch vụ sửa chữa:</strong> Bảo hành linh kiện thay thế theo tiêu chuẩn của hãng (thường từ 1-6 tháng tùy linh kiện). Hoàn tiền 100% nếu thợ sửa không khắc phục được lỗi như đã cam kết ban đầu.</p>
                    </div>
                </div>

                <!-- 7. Hỗ trợ -->
                <div class="bct-pane" id="pol-7">
                    <h3>7. Hình Thức Hỗ Trợ Trực Tuyến</h3>
                    <p>Điện Máy Hiếu ứng dụng công nghệ đa kênh để hỗ trợ khách hàng nhanh nhất có thể:</p>
                    <ul style="font-size:16px;">
                        <li style="margin-bottom: 15px;"><strong>Chatbot Trí Tuệ Nhân Tạo (Anh thiên Openclaw):</strong> Hoạt động 24/7 ngay trên góc phải màn hình website, giải đáp các thắc mắc cơ bản về dịch vụ và hướng dẫn sử dụng.</li>
                        <li style="margin-bottom: 15px;"><strong>Hotline hỗ trợ khẩn cấp:</strong> 0939.354.937 (Hoạt động từ 07:30 đến 18:00). Dành cho các khiếu nại, phản ánh chất lượng hoặc cần điều thợ gấp.</li>
                        <li style="margin-bottom: 15px;"><strong>Zalo OA / Facebook Messenger:</strong> Tích hợp liên kết tại chân trang, giúp khách hàng gửi hình ảnh, video tình trạng hỏng hóc của thiết bị để thợ chẩn đoán từ xa trước khi đến.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* STYLE DÀNH CHO CÁN BỘ BCT (ADHD & OCD FRIENDLY) */
.bct-tab-btn {
    width: 100%;
    text-align: left;
    padding: 18px 24px;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 12px;
    color: #94a3b8;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 12px;
}
.bct-tab-btn i {
    font-size: 18px;
    width: 24px;
    text-align: center;
    opacity: 0.7;
    transition: all 0.3s;
}
.bct-tab-btn:hover {
    background: rgba(255,255,255,0.05);
    color: #e2e8f0;
    border-color: rgba(255,255,255,0.1);
    transform: translateX(4px);
}
.bct-tab-btn.active {
    background: linear-gradient(90deg, rgba(56, 189, 248, 0.15) 0%, rgba(56, 189, 248, 0.05) 100%);
    border: 1px solid rgba(56, 189, 248, 0.3);
    border-left: 4px solid #38bdf8;
    color: #38bdf8;
    box-shadow: 0 10px 20px -10px rgba(56,189,248,0.2);
}
.bct-tab-btn.active i {
    opacity: 1;
    color: #38bdf8;
}

.bct-pane {
    display: none;
    animation: fadeInBCT 0.5s ease forwards;
}
.bct-pane.active {
    display: block;
}
@keyframes fadeInBCT {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

.bct-pane h3 {
    font-size: 24px;
    font-weight: 800;
    color: #f8fafc;
    margin: 0 0 24px 0;
    padding-bottom: 16px;
    border-bottom: 2px solid rgba(255,255,255,0.05);
}
.bct-pane p {
    font-size: 15.5px;
    line-height: 1.8;
    color: #cbd5e1;
    margin-bottom: 16px;
}
.bct-pane ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}
.bct-pane ul li {
    position: relative;
    padding-left: 24px;
    font-size: 15.5px;
    line-height: 1.8;
    color: #cbd5e1;
    margin-bottom: 12px;
}
.bct-pane ul li::before {
    content: "•";
    color: #38bdf8;
    font-size: 20px;
    font-weight: bold;
    position: absolute;
    left: 0;
    top: -2px;
}

.bct-box {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 24px;
    margin-top: 24px;
}
.bct-box strong {
    color: #f8fafc !important;
}

@media (max-width: 900px) {
    #bct-policies > div > div { grid-template-columns: 1fr; }
    #bct-tabs { flex-direction: row; overflow-x: auto; position: static; padding-bottom: 10px; }
    .bct-tab-btn { white-space: nowrap; width: auto; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bctBtns = document.querySelectorAll('.bct-tab-btn');
    const bctPanes = document.querySelectorAll('.bct-pane');

    bctBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            bctBtns.forEach(b => b.classList.remove('active'));
            bctPanes.forEach(p => p.classList.remove('active'));
            
            btn.classList.add('active');
            document.getElementById(btn.dataset.target).classList.add('active');
        });
    });

    // Check URL hash to open specific tab
    const hash = window.location.hash;
    if(hash && hash.startsWith('#pol-')) {
        const targetBtn = document.querySelector(`.bct-tab-btn[data-target="${hash.substring(1)}"]`);
        if(targetBtn) {
            targetBtn.click();
            setTimeout(() => {
                document.getElementById('bct-policies').scrollIntoView({behavior: 'smooth'});
            }, 100);
        }
    }
});
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
