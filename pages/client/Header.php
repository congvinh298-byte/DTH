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
            <div id="topBarStatus">
                <a href="/login.php" style="color: white; font-weight: bold; text-decoration: underline;">Đăng nhập / Đăng ký</a>
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
            
            <div style="display: flex; gap: 8px;">
                <a class="btn dark" href="/goi-tho.php">Gọi thợ</a>
            </div>
        </div>
        
        <nav>
            <div class="wrap">
                <button type="button" class="active" onclick="window.location.href='/'">Tất cả</button>
                <button type="button" onclick="window.location.href='/dien-may'">Điện tử</button>
                <button type="button" onclick="window.location.href='/dien-may'">Gia dụng</button>
                <button type="button" onclick="window.location.href='/dien-may'">Lạnh</button>
                <button type="button" onclick="window.location.href='/in-3d.php'">In 3D</button>
            </div>
        </nav>
    </header>