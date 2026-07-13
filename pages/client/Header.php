<?php
  if (!defined('IN_SITE')) die('The Request Not Found');
?>
<head>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
</head>
<body id="body_class">
    
    <style>
    #topMenuDropdown a:hover { background: rgba(56,189,248,0.15); color: #38bdf8; }
    #topMenuDropdown a i { margin-right: 8px; }
    #topMenuToggle:hover { background: rgba(255,255,255,0.1); }
    @media (max-width: 768px) {
        #topMenuDropdown { right: 0; }
    }
    </style>
    
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
            
            <div style="display: flex; gap: 8px; align-items: center; position: relative;">
                <a class="btn dark" href="/dien-may">Cửa hàng</a>
                <a class="btn dark" href="/in-3d.php">In 3D</a>
                <a class="btn dark" href="/goi-tho.php">Gọi thợ</a>
                <a class="btn dark" href="/tra-cuu-don.php">Tra cứu đơn</a>
                <a class="btn" href="/GioHang.php" style="background: var(--brand-accent); color: white; position: relative; padding: 10px 15px;">
                    <i class="fa-solid fa-cart-shopping"></i> Giỏ Hàng
                    <span id="cartCountBadge" style="position: absolute; top: -8px; right: -8px; background: #f43f5e; color: white; border-radius: 50%; width: 22px; height: 22px; font-size: 12px; font-weight: bold; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.3); display: none;">0</span>
                </a>
                
                <button id="topMenuToggle" onclick="toggleTopMenu()" style="background: transparent; border: none; color: white; font-size: 20px; cursor: pointer; padding: 6px 10px; border-radius: 8px;">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div id="topMenuDropdown" style="display: none; position: absolute; right: 0; top: calc(100% + 8px); background: #0f172a; border: 1px solid rgba(255,255,255,0.15); border-radius: 10px; min-width: 200px; z-index: 1000; padding: 8px 0; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
                    <a href="/login.php" style="display: block; padding: 10px 16px; color: white; font-weight: 600; text-decoration: none; white-space: nowrap;"><i class="fa-solid fa-sign-in-alt" style="width: 22px;"></i> Đăng nhập / Đăng ký</a>
                </div>
            </div>
        </div>
    </header>

    <script>
    function toggleTopMenu() {
        const menu = document.getElementById('topMenuDropdown');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }

    // Đóng menu khi click ra ngoài
    document.addEventListener('click', function(e) {
        const toggle = document.getElementById('topMenuToggle');
        const menu = document.getElementById('topMenuDropdown');
        if (!toggle || !menu) return;
        if (!toggle.contains(e.target) && !menu.contains(e.target)) {
            menu.style.display = 'none';
        }
    });

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
                        $('#topMenuDropdown').html(`
                            <a href="/pages/client/Orders.php" style="display: block; padding: 10px 16px; color: white; font-weight: 600; text-decoration: none; white-space: nowrap;"><i class="fa-solid fa-box" style="width: 22px;"></i> Đơn hàng của tôi</a>
                            <a href="/profile.php" style="display: block; padding: 10px 16px; color: white; font-weight: 600; text-decoration: none; white-space: nowrap;"><i class="fa-solid fa-user" style="width: 22px;"></i> Thông tin khách hàng</a>
                            <a href="javascript:void(0)" onclick="logout()" style="display: block; padding: 10px 16px; color: #f43f5e; font-weight: 600; text-decoration: none; white-space: nowrap;"><i class="fa-solid fa-sign-out-alt" style="width: 22px;"></i> Đăng xuất</a>
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