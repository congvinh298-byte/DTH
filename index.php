<?php

define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");
$title = "Điện Máy Hiếu - Marketplace & Gọi Thợ";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");

$productError = '';
$products = $DMH->get_list("SELECT * FROM `danhsachmuacode` WHERE `hienthi` = 'SHOW' AND `money` > 0 ORDER BY id DESC LIMIT 60");

$services = array(
    array('group' => 'Thợ điện lạnh', 'name' => 'Vệ sinh máy lạnh', 'base' => 150000, 'note' => 'Giá công khai chưa VAT'),
    array('group' => 'Thợ điện lạnh', 'name' => 'Lắp đặt máy lạnh 1HP / 1.5HP', 'base' => 400000, 'note' => 'Chưa gồm vật tư phát sinh'),
    array('group' => 'Thợ điện lạnh', 'name' => 'Lắp đặt máy lạnh 2HP / 3HP', 'base' => 500000, 'note' => 'Chưa gồm vật tư phát sinh'),
    array('group' => 'Thợ điện lạnh', 'name' => 'Máy lạnh âm trần', 'base' => 0, 'note' => 'Hỗ trợ liên hệ hãng'),
    array('group' => 'Thợ điện lạnh', 'name' => 'Sửa chữa điện lạnh', 'base' => 200000, 'note' => 'Công thợ + linh kiện đặt mua công khai'),
    array('group' => 'Thợ tivi', 'name' => 'Treo tivi', 'base' => 200000, 'note' => 'Công thợ + khung treo'),
    array('group' => 'Thợ máy lọc nước', 'name' => 'Lắp máy lọc nước', 'base' => 200000, 'note' => 'Công thợ + phụ kiện'),
    array('group' => 'Thợ gia dụng', 'name' => 'Lắp máy giặt', 'base' => 200000, 'note' => 'Công thợ + phụ kiện'),
    array('group' => 'Thợ điện thoại', 'name' => 'Kiểm tra / sửa điện thoại', 'base' => 200000, 'note' => 'Công thợ + linh kiện nếu có'),
    array('group' => 'In 3D', 'name' => 'Đặt in 3D theo mẫu', 'base' => 0, 'note' => 'Báo giá dựa theo khối lượng (500đ/1 gram)'),
);
?>

<main>
    <div class="wrap storefront fade-in">
        
        <!-- Premium Hero Section -->
        <section class="hero">
            <div class="hero-main">
                <div class="blob"></div>
                <h1>Điện Máy Hiếu</h1>
                <p>Hệ sinh thái bán lẻ và dịch vụ sửa chữa Điện Máy, Điện Lạnh, Điện Tử uy tín số 1 tại khu vực miền Tây. Phục vụ tận tâm, nhanh chóng trong bán kính 15 km tính từ Chợ Lấp Vò.</p>
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
                        $name = $p['title'];
                        $category = 'Điện Máy & Gia Dụng';
                        $image = $p['img'];
                        $price = $p['money'];
                        ?>
                        <article class="product" data-name="<?= htmlspecialchars(strtolower($name)) ?>" data-category="<?= htmlspecialchars(strtolower($category)) ?>" onclick="window.location.href='/mua-code/<?=$p['id'];?>'">
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
                                <div class="price"><?= number_format($price, 0, ',', '.') ?></div>
                                <a href="/mua-code/<?=$p['id'];?>"><i class="fa-solid fa-eye"></i> Xem chi tiết</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Booking App Section -->
        <section class="section booking-shell" id="goi-tho">
            <div class="title">
                <h2>Dịch Vụ Gọi Thợ Tận Nơi</h2>
                <span class="muted">Minh bạch giá cả - Gọi là có mặt</span>
            </div>
            
            <form id="bookingForm" autocomplete="off">
                <div id="thongbao_datlich" style="margin-block-end: 24px;"></div>
                <h3>Thông tin Yêu cầu Dịch vụ</h3>
                <div class="form">
                    <div class="field full">
                        <label for="service_selector">1. Bạn cần dịch vụ gì? <span style="color:var(--brand-accent);">*</span></label>
                        <select id="service_selector" name="service_selector" required style="font-weight: 700; color: #fff;">
                            <option value="" disabled selected>-- Bấm vào đây để chọn dịch vụ và xem giá --</option>
                            <?php 
                            $currentGroup = '';
                            foreach ($services as $svc): 
                                if ($svc['group'] !== $currentGroup) {
                                    if ($currentGroup !== '') echo '</optgroup>';
                                    echo '<optgroup label="' . htmlspecialchars($svc['group']) . '">';
                                    $currentGroup = $svc['group'];
                                }
                                $base = (int)$svc['base']; 
                                $publicPrice = $base > 0 ? number_format((int)round($base * 1.10),0,',','.') . ' VND' : 'Báo giá sau khi khảo sát';
                            ?>
                                <option value="<?= htmlspecialchars($svc['name']) ?>" data-price="<?= htmlspecialchars($publicPrice) ?>" data-group="<?= htmlspecialchars($svc['group']) ?>" data-note="<?= htmlspecialchars($svc['note']) ?>">
                                    <?= htmlspecialchars($svc['name']) ?> - <?= $publicPrice ?>
                                </option>
                            <?php endforeach; ?>
                            <?php if ($currentGroup !== '') echo '</optgroup>'; ?>
                        </select>
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
const serviceSelector = document.getElementById('service_selector');
if (serviceSelector) {
    serviceSelector.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        const group = selectedOption.dataset.group || '';
        const serviceName = selectedOption.value || '';
        const price = selectedOption.dataset.price || 'Liên hệ để báo giá chi tiết';
        const note = selectedOption.dataset.note || '';
        
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
}

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
</script>

<div style="text-align: center; margin: 40px 0 20px; position: relative; z-index: 5;">
    <a href="/login.php" class="btn dark" style="padding: 10px 24px; font-size: 14px; border: 1px solid rgba(15,23,42,0.1);"><i class="fa-solid fa-user-gear"></i> Khu Vực Dành Cho Thợ (Portal)</a>
</div>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
