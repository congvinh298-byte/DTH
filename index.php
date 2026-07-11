<?php
if (isset($_GET['do_fix_logo'])) {
    $targetFile = '/home/kwkrbcce/public_html/public/assets/logo.png';
    $sourceFiles = [
        '/home/kwkrbcce/public_html/public/logo.png',
        __DIR__ . '/logo.png',
        __DIR__ . '/public/assets/logo.png'
    ];
    
    $sourceFile = null;
    foreach ($sourceFiles as $sf) {
        if (file_exists($sf)) {
            $sourceFile = $sf;
            break;
        }
    }
    
    echo "<h1>Fixing Logo</h1>";
    if (file_exists($targetFile)) {
        echo "Deleting target... " . (unlink($targetFile) ? 'OK' : 'FAIL') . "<br>";
    }
    if ($sourceFile) {
        echo "Copying source ($sourceFile) to target... " . (copy($sourceFile, $targetFile) ? 'OK' : 'FAIL') . "<br>";
    } else {
        echo "Source file does not exist in any of the checked paths!<br>";
    }
    exit;
}
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

<main><div class="wrap storefront">
    <!-- Hero Section -->
    <section class="hero">
        <div class="panel hero-main">
            <h1>Điện Máy Hiếu</h1>
            <p>Hệ sinh thái Điện Máy Hiếu phục vụ bà con trong xã Lấp Vò và khu vực bán kính 15 km tính từ Chợ Lấp Vò. LH: 0939.354.937</p>
            <div class="hero-actions">
                <a class="btn" href="#products">Xem sản phẩm</a>
                <a class="btn dark" href="#goi-tho">Đặt lịch gọi thợ</a>
                <a class="btn" style="background:#0a192f" href="/in-3d.php">Dịch vụ In 3D</a>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="section" id="products">
        <div class="title">
            <h2>Sản phẩm nổi bật</h2>
            <span class="muted"><?= count($products) ?> sản phẩm</span>
        </div>
        <div class="grid" id="productGrid">
            <?php if (empty($products)): ?>
                <div class="empty">Hiện chưa có sản phẩm</div>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <?php
                    $name = $p['title'];
                    $category = 'Điện Máy & Gia Dụng';
                    $image = $p['img'];
                    $price = $p['money'];
                    $suggested = false;
                    ?>
                    <article class="product" data-name="<?= htmlspecialchars(strtolower($name)) ?>" data-category="<?= htmlspecialchars(strtolower($category)) ?>">
                        <div class="img">
                            <?php if ($image !== ''): ?>
                                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>" onerror="this.parentNode.textContent='Chưa có ảnh'">
                            <?php else: ?>
                                Chưa có ảnh
                            <?php endif; ?>
                        </div>
                        <div class="body">
                            <div class="name"><?= htmlspecialchars($name) ?></div>
                            <div class="cat"><?= htmlspecialchars($category) ?></div>
                            <div class="price"><?= number_format($price, 0, ',', '.') ?> VND</div>
                            <a href="/mua-code/<?=$p['id'];?>" style="display:block; margin-block-start:8px; text-align:center; padding: 6px; background:#f6f7f9; border-radius:4px; font-size:12px; font-weight:bold;">Xem chi tiết</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Booking App Section (Redirects to separate pages) -->
    <section class="section panel booking-shell" id="goi-tho" style="background-color: #0a192f; border: 1px solid #1a365d;">
        <div class="title">
            <h2 style="color: #ffffff;">Dịch vụ gọi thợ & In 3D</h2>
            <span class="muted" style="color: #94a3b8;">Chọn dịch vụ bạn cần để đi tới trang đăng ký</span>
        </div>
        
        <div class="service-list" style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; padding: 20px 0;">
            <a href="/goi-tho.php" class="btn" style="background-color: #ffffff; color: #0a192f; font-weight: bold; padding: 15px 30px; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; border-radius: 8px;">
                <i class="fa-solid fa-wrench"></i> Đặt lịch gọi thợ
            </a>
            <a href="/in-3d.php" class="btn" style="background-color: #2563eb; color: #ffffff; font-weight: bold; padding: 15px 30px; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; border-radius: 8px;">
                <i class="fa-solid fa-cube"></i> Dịch vụ In 3D
            </a>
        </div>
    </section>

</div></main>

<script>
'use strict';

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

const serviceSearchInput = document.getElementById('serviceSearchInput');
if (serviceSearchInput) {
    serviceSearchInput.addEventListener('input', () => {
        const q = normalize(serviceSearchInput.value);
        document.querySelectorAll('.service-option').forEach(option => {
            option.style.display = !q || normalize(option.textContent).indexOf(q) !== -1 ? '' : 'none';
        });
    });
}

document.querySelectorAll('.choose-service').forEach(button => {
    button.addEventListener('click', () => {
        document.querySelectorAll('.choose-service').forEach(item => item.classList.remove('selected'));
        button.classList.add('selected');
        
        const base = Number(button.dataset.base || 0);
        document.getElementById('service_type').value = button.dataset.group || 'Thợ điện lạnh';
        document.getElementById('issue_description').value = button.dataset.service || '';
        document.getElementById('selected_service_name').value = button.dataset.service || '';
        
        if (base > 0) {
            document.getElementById('customer_price_display').value = new Intl.NumberFormat('vi-VN').format(Math.round(base * 1.10)) + ' VND - đã gồm VAT';
        } else {
            document.getElementById('customer_price_display').value = 'Liên hệ để báo giá chi tiết';
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

/* AJAX Submit for Booking/In 3D */
$("#btnDatLich").on("click", function() {
    var ten = $("#customer_name").val().trim();
    var sdt = $("#phone").val().trim();
    var diachi = $("#address").val().trim();
    var dichvu = $("#service_type").val() + " - " + $("#selected_service_name").val();
    var yeucau = $("#issue_description").val().trim();
    var lat = $("#map_lat").val();
    var lng = $("#map_lng").val();
    
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
            $('#btnDatLich').html('Gửi yêu cầu gọi thợ / Đặt In').prop('disabled',false).css('opacity','1');
        }
    });
});
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
