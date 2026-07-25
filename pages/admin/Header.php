<?php
  if (!defined('IN_SITE')) die('Truy cập không hợp lệ');

  $currentUri = $_SERVER['REQUEST_URI'] ?? '';

  function adminMenuActive($url, $currentUri) {
      if ($url == '#' || $url == '') return false;
      return strpos($currentUri, $url) !== false;
  }
?>
<body>
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <a href="/Admin"><img src="/public/assets/logo.png" alt="Điện Máy Hiếu"></a>
            <h3>ĐIỆN MÁY HIẾU</h3>
            <small>Hệ thống quản trị</small>
        </div>
        <ul class="admin-menu">
            <li><a href="/Admin" class="<?= adminMenuActive('/Admin', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-gauge"></i> Tổng quan</span></a></li>
            <li><a href="/pages/admin/QuanLyDonHang.php" class="<?= adminMenuActive('QuanLyDonHang', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-box"></i> Đơn hàng sản phẩm</span></a></li>
            <li><a href="/pages/admin/QuanLyDatLich.php" class="<?= adminMenuActive('QuanLyDatLich', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-calendar-check"></i> Đơn gọi thợ</span></a></li>
            <li><a href="/pages/admin/QuanLyKhachHang.php" class="<?= adminMenuActive('QuanLyKhachHang', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-users"></i> Khách hàng</span></a></li>
            <li><a href="/pages/admin/QuanLyTho.php" class="<?= adminMenuActive('QuanLyTho', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-wrench"></i> Thợ kỹ thuật</span></a></li>
            <li style="margin-top:10px; padding: 4px 16px; font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Quản Lý Hàng Hóa</li>
            <li><a href="/pages/admin/GianHangDienMay.php" class="<?= adminMenuActive('GianHangDienMay', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-plug"></i> Đăng SP Điện máy & Gia dụng</span></a></li>
            <li><a href="/pages/admin/GianHang3D.php" class="<?= adminMenuActive('GianHang3D', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-cube"></i> Đăng SP Mô hình 3D</span></a></li>
            <li style="margin-top:10px; padding: 4px 16px; font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Hệ Thống</li>
            <li><a href="/pages/admin/BaoCao.php" class="<?= adminMenuActive('BaoCao', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-chart-line"></i> Báo cáo doanh thu</span></a></li>
            <li><a href="/pages/admin/CaiDat.php" class="<?= adminMenuActive('CaiDat', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-gear"></i> Cài đặt hệ thống</span></a></li>
        </ul>
    </aside>
    <div class="admin-main">
        <header class="admin-topbar">
            <div>
                <h1><?= htmlspecialchars($tieude ?? 'Bảng điều khiển') ?></h1>
                <p><?= date('d/m/Y H:i') ?></p>
            </div>
            <div class="admin-user">
                <i class="fa-solid fa-user-shield"></i>
                <span><?= htmlspecialchars($getUser['username'] ?? 'Quản trị viên') ?></span>
                <a href="/pages/admin/Logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-sign-out-alt"></i> Đăng xuất</a>
            </div>
        </header>
        <main class="admin-content">
