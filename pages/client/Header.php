<?php
  if (!defined('IN_SITE')) die('The Request Not Found');
?>
<head>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
</head>
<body id="body_class">
    
    <div class="top">
        <div class="wrap">
            <div>Điện Tử Hiếu - Storefront công khai</div>
            <div id="topBarStatus" style="display: flex; gap: 15px; align-items: center;">
                <a href="/login.php" style="color: white; font-weight: bold; text-decoration: underline;" id="loginLink">Đăng nhập / Đăng ký</a>
            </div>
        </div>
    </div>
    
    <div class="approval-line">Website đang chờ duyệt</div>
    
    <header>
        <div class="wrap head">
            <!-- Tối ưu hiển thị cho LOGO DỌC/VUÔNG: img height 64px, object-fit contain -->
            <a class="logo" href="/">
                <img src="/public/assets/logo.png" alt="Logo Điện Máy Hiếu">
                <div>Điện Máy Hiếu<small>Mua hàng nhanh - Gọi thợ nhanh</small></div>
            </a>
            
            <form class="search" id="searchForm" action="/">
                <input id="searchInput" name="q" type="search" placeholder="Tìm sản phẩm, dịch vụ...">
                <button type="submit">Tìm</button>
            </form>
            
            <div style="display: flex; gap: 8px; align-items: center;">
                <a class="btn dark" href="/dien-may">Cửa hàng</a>
                <a class="btn dark" href="/in-3d.php">In 3D</a>
                <a class="btn dark" href="/goi-tho.php">Gọi thợ</a>
                <a class="btn dark" href="/tra-cuu-don.php">Tra cứu đơn</a>
                <a class="btn" href="/GioHang.php" style="background: var(--brand-accent); color: white; position: relative; padding: 10px 15px;">
                    <i class="fa-solid fa-cart-shopping"></i> Giỏ Hàng
                    <span id="cartCountBadge" style="position: absolute; top: -8px; right: -8px; background: #f43f5e; color: white; border-radius: 50%; width: 22px; height: 22px; font-size: 12px; font-weight: bold; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.3); display: none;">0</span>
                </a>
            </div>
        </div>
    </header>

    <script>
    // JS check login status for header
    function checkLoginStatus() {
        $.ajax({
            url: "/controller/client/CartAction.php",
            method: "POST",
            data: { action: 'check_login' },
            success: function(r) {
                try {
                    let res = typeof r === 'string' ? JSON.parse(r) : r;
                    if(res.logged_in) {
                        $('#topBarStatus').html(`
                            <a href="/pages/client/Orders.php" style="color: #38bdf8; font-weight: bold; margin-right: 15px;"><i class="fa-solid fa-box"></i> Đơn hàng của tôi</a>
                            <a href="/profile.php" style="color: white; font-weight: bold; margin-right: 15px;"><i class="fa-solid fa-user"></i> Xin chào, ${res.username}</a>
                            <a href="javascript:void(0)" onclick="logout()" style="color: #f43f5e; font-weight: bold;"><i class="fa-solid fa-sign-out-alt"></i> Đăng xuất</a>
                        `);
                        updateCartCount();
                    }
                } catch(e) {}
            }
        });
    }

    function updateCartCount() {
        $.ajax({
            url: "/controller/client/CartAction.php",
            method: "POST",
            data: { action: 'cart_count' },
            success: function(r) {
                try {
                    let res = typeof r === 'string' ? JSON.parse(r) : r;
                    if(res.count > 0) {
                        $('#cartCountBadge').text(res.count).show();
                    } else {
                        $('#cartCountBadge').hide();
                    }
                } catch(e) {}
            }
        });
    }

    function addToCart(productId) {
        $.ajax({
            url: "/controller/client/CartAction.php",
            method: "POST",
            data: { action: 'add_to_cart', product_id: productId },
            success: function(r) {
                try {
                    let res = typeof r === 'string' ? JSON.parse(r) : r;
                    if(res.status == 'success') {
                        if(typeof showToast === 'function') {
                            showToast('Đã thêm vào giỏ hàng!', 'success');
                        } else if(typeof Swal !== 'undefined') {
                            Swal.fire('Thành công', 'Đã thêm vào giỏ hàng!', 'success');
                        } else {
                            alert('Đã thêm vào giỏ hàng!');
                        }
                        updateCartCount();
                    } else {
                        if(typeof Swal !== 'undefined') Swal.fire('Thông báo', res.msg, 'warning');
                        else alert(res.msg);
                    }
                } catch(e) {
                    console.error("Lỗi JSON:", e, r);
                    if(typeof Swal !== 'undefined') Swal.fire('Lỗi', 'Không thể xử lý dữ liệu máy chủ. Hãy thử lại!', 'error');
                }
            }
        });
    }

    function buyNow(productId) {
        $.ajax({
            url: "/controller/client/CartAction.php",
            method: "POST",
            data: { action: 'add_to_cart', product_id: productId },
            success: function(r) {
                try {
                    let res = typeof r === 'string' ? JSON.parse(r) : r;
                    if(res.status == 'success') {
                        window.location.href = '/GioHang.php';
                    } else {
                        if(typeof Swal !== 'undefined') Swal.fire('Thông báo', res.msg, 'warning');
                        else alert(res.msg);
                    }
                } catch(e) {
                    console.error("Lỗi JSON:", e, r);
                    if(typeof Swal !== 'undefined') Swal.fire('Lỗi', 'Không thể xử lý dữ liệu máy chủ. Hãy thử lại!', 'error');
                }
            }
        });
    }

    $(document).ready(function() {
        checkLoginStatus();
    });

    function logout() {
        if(confirm('Bạn có chắc muốn đăng xuất?')) {
            $.ajax({
                url: '/controller/client/Logout.php',
                method: 'POST',
                dataType: 'json',
                success: function(res) {
                    if(res.status == 'success') {
                        window.location.href = '/';
                    } else {
                        alert(res.msg || 'Đăng xuất thất bại');
                    }
                },
                error: function() {
                    window.location.href = '/';
                }
            });
        }
    }
    </script>