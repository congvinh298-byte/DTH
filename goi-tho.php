<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");
$title = "Đặt Lịch Gọi Thợ | Điện Máy Hiếu";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");

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
<main><div class="wrap storefront">
    <!-- Booking App Section -->
    <section class="section panel booking-shell" id="goi-tho">
        <div class="title">
            <h2>Dịch vụ gọi thợ</h2>
            <span class="muted">Chọn nhóm dịch vụ và giá trước khi điền thông tin</span>
        </div>
        <form id="bookingForm">
            <h3>Thông tin yêu cầu</h3>
            <div class="form">
                <div class="field full">
                    <label for="service_selector">Chọn dịch vụ *</label>
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
                    <input class="readonly-price" id="customer_price_display" type="text" readonly placeholder="Chọn dịch vụ ở trên để xem giá">
                    <small id="service_note" style="color: var(--muted); display: block; margin-top: 4px;"></small>
                </div>
                
                <?php if(!isset($getUser) || $getUser['level'] != 'user'): ?>
                <div style="grid-column: 1 / -1; background: rgba(255, 0, 0, 0.1); border: 2px dashed #f43f5e; padding: 30px; text-align: center; border-radius: 12px; margin-top: 20px;">
                    <i class="fa-solid fa-lock" style="font-size: 32px; color: #f43f5e; margin-bottom: 12px;"></i>
                    <h3 style="color: #fff; margin-bottom: 8px;">Bạn Cần Đăng Nhập Để Gọi Thợ</h3>
                    <p style="color: #cbd5e1; margin-bottom: 20px;">Vui lòng đăng nhập hoặc tạo tài khoản miễn phí bằng số điện thoại để tiếp tục đặt lịch và tích điểm.</p>
                    <a href="/login.php" class="btn accent" style="display: inline-block; padding: 12px 24px;">ĐĂNG NHẬP / ĐĂNG KÝ NGAY</a>
                </div>
                <?php else: ?>
                <div class="field">
                    <label for="customer_name">Tên khách *</label>
                    <input id="customer_name" name="customer_name" required maxlength="150" value="<?= htmlspecialchars($getUser['name']) ?>" readonly style="background: rgba(255,255,255,0.05); color: #94a3b8; cursor: not-allowed;">
                </div>
                
                <div class="field">
                    <label for="phone">Số điện thoại *</label>
                    <input id="phone" name="phone" type="tel" inputmode="numeric" required maxlength="15" value="<?= htmlspecialchars($getUser['phone']) ?>" readonly style="background: rgba(255,255,255,0.05); color: #94a3b8; cursor: not-allowed;">
                </div>
                
                <div class="field full">
                    <label for="address">Địa chỉ nhà (Đã lưu trong hồ sơ)</label>
                    <input id="address" name="address" required maxlength="500" value="<?= htmlspecialchars($getUser['address']) ?>" readonly style="background: rgba(255,255,255,0.05); color: #94a3b8; cursor: not-allowed;">
                    <small style="color: var(--brand-accent); display: block; margin-top: 4px;">* Nếu địa chỉ sự cố khác địa chỉ này, vui lòng ghi chú ở phần mô tả chi tiết.</small>
                    
                    <div style="margin-top: 16px;">
                        <label style="font-weight: 600; font-size: 14px; margin-bottom: 8px; display: block; color: #cbd5e1;">(Tùy chọn) Chọn tọa độ GPS để thợ tìm nhanh hơn:</label>
                        <div class="map-actions">
                            <button class="btn dark" id="useCurrentLocation" type="button"><i class="fa-solid fa-location-crosshairs"></i> Dùng vị trí GPS hiện tại</button>
                            <button class="btn" id="clearLocation" type="button">Xóa vị trí</button>
                        </div>
                        <input type="hidden" id="map_location" name="map_location">
                        <input type="hidden" id="map_lat" name="map_lat">
                        <input type="hidden" id="map_lng" name="map_lng">
                        <div class="map-preview">
                            <div class="location-map" id="locationMap" aria-label="Bản đồ chọn vị trí"></div>
                            <div class="location-status" id="locationStatus">Bấm vào bản đồ hoặc dùng vị trí hiện tại.</div>
                        </div>
                    </div>
                </div>
                
                <div class="field full">
                    <label for="issue_description">Mô tả chi tiết sự cố *</label>
                    <textarea id="issue_description" name="issue_description" required maxlength="2000" placeholder="VD: Máy lạnh không mát..."></textarea>
                </div>
                <?php endif; ?>
            </div>
            
            <p class="muted" style="margin-block-start: 15px;">Giá công khai đã gồm VAT. Vật tư hoặc linh kiện phát sinh sẽ được báo riêng trước khi làm.</p>
            <?php if(isset($getUser) && $getUser['level'] == 'user'): ?>
            <button id="btnDatLich" class="btn" type="button" style="inline-size: 100%; padding: 14px; font-size: 16px;">Gửi yêu cầu gọi thợ</button>
            <?php endif; ?>
        </form>
    </section>
</div></main>

<script>
'use strict';

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
        this.style.background = 'var(--brand-accent, #38bdf8)';
        this.style.borderColor = 'var(--brand-accent, #38bdf8)';
        this.style.boxShadow = '0 4px 15px rgba(56, 189, 248, 0.4)';
        
        const group = this.dataset.group || '';
        const serviceName = this.dataset.val || '';
        const price = this.dataset.price || 'Liên hệ để báo giá chi tiết';
        const note = this.dataset.note || '';
        
        document.getElementById('service_type').value = group;
        document.getElementById('selected_service_name').value = serviceName;
        document.getElementById('customer_price_display').value = price;
        document.getElementById('service_note').textContent = note;
        
        // Auto-fill issue description if it's empty
        const issueDesc = document.getElementById('issue_description');
        if (!issueDesc.value || issueDesc.value.length < 5) {
            issueDesc.value = 'Tôi cần ' + serviceName;
        }
    });
});

/* Map Logic */
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

    setLocationStatus('Đang lấy địa chỉ...');
    fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&accept-language=vi&lat=' + latitude + '&lon=' + longitude)
        .then(res => res.json())
        .then(data => {
            if (data.display_name) addressInput.value = data.display_name;
            setLocationStatus('Đã đồng bộ GPS: ' + coords);
        })
        .catch(() => setLocationStatus('Đã đồng bộ tọa độ.'));
}

if (window.L && document.getElementById('locationMap')) {
    locationMap = L.map('locationMap').setView(defaultLocation, 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(locationMap);

    locationMap.on('click', event => syncSelectedLocation(event.latlng.lat, event.latlng.lng, true));
    window.setTimeout(() => locationMap.invalidateSize(), 100);
}

document.getElementById('useCurrentLocation')?.addEventListener('click', () => {
    if (!navigator.geolocation) return;
    setLocationStatus('Đang lấy vị trí GPS...');
    navigator.geolocation.getCurrentPosition(position => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        if (locationMap) locationMap.setView([lat, lng], 17);
        syncSelectedLocation(lat, lng, true);
    }, () => setLocationStatus('Lỗi: Không thể lấy GPS!'));
});

document.getElementById('clearLocation')?.addEventListener('click', () => {
    document.getElementById('map_location').value = '';
    document.getElementById('map_lat').value = '';
    document.getElementById('map_lng').value = '';
    addressInput.value = '';
    if (locationMap && locationMarker) {
        locationMap.removeLayer(locationMarker);
        locationMarker = null;
    }
});

/* AJAX Submit for Booking */
$("#btnDatLich").on("click", function() {
    var ten = $("#customer_name").val().trim();
    var sdt = $("#phone").val().trim();
    var diachi = $("#address").val().trim();
    var dichvu = $("#service_type").val() + " - " + $("#selected_service_name").val();
    var yeucau = $("#issue_description").val().trim();
    var lat = $("#map_lat").val();
    var lng = $("#map_lng").val();
    
    if (!$("#service_type").val()) {
        alert("Vui lòng chọn một dịch vụ!");
        return;
    }
    
    if (!ten || !sdt || !diachi || !yeucau) {
        alert("Vui lòng điền đầy đủ Tên, SĐT, Địa chỉ và Mô tả!");
        return;
    }
    
    $(this).html('ĐANG GỬI...').prop('disabled', true).css('opacity','0.7');
    
    if (lat && lng) {
        diachi += " | GPS: " + lat + "," + lng + " (https://maps.google.com/?q=" + lat + "," + lng + ")";
    }
    
    $.ajax({
        url: "/controller/client/DatLich.php",
        method: "POST",
        data: { type:'DatLich', ten:ten, sdt:sdt, dichvu:dichvu, diachi:diachi, yeucau:yeucau },
        success: function(r) {
            $("#thongbao_datlich").html(r);
            $('#btnDatLich').html('Gửi yêu cầu thành công').prop('disabled',false).css('opacity','1');
        },
        error: function() {
            alert("Lỗi hệ thống! Gọi trực tiếp 0939.354.937");
            $('#btnDatLich').html('Gửi yêu cầu gọi thợ').prop('disabled',false).css('opacity','1');
        }
    });
});
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
