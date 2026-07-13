<?php
  if (!defined('IN_SITE')) die('The Request Not Found');

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
            <a href="/Admin"><img src="/public/assets/logo.png" alt="Dien May Hieu"></a>
            <h3>DIEN MAY HIEU</h3>
            <small>He thong quan tri</small>
        </div>
        <ul class="admin-menu">
            <li><a href="/Admin" class="<?= adminMenuActive('/Admin', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-gauge"></i> Dashboard</span></a></li>
            <li><a href="/pages/admin/QuanLyDonHang.php" class="<?= adminMenuActive('QuanLyDonHang', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-box"></i> Don hang san pham</span></a></li>
            <li><a href="/pages/admin/QuanLyDatLich.php" class="<?= adminMenuActive('QuanLyDatLich', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-calendar-check"></i> Don goi tho</span></a></li>
            <li><a href="/pages/admin/QuanLyKhachHang.php" class="<?= adminMenuActive('QuanLyKhachHang', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-users"></i> Khach hang</span></a></li>
            <li><a href="/pages/admin/QuanLyTho.php" class="<?= adminMenuActive('QuanLyTho', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-wrench"></i> Tho</span></a></li>
            <li><a href="/pages/admin/GianHangDienMay.php" class="<?= adminMenuActive('GianHangDienMay', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-plug"></i> Gian hang dien may</span></a></li>
            <li><a href="/pages/admin/GianHang3D.php" class="<?= adminMenuActive('GianHang3D', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-cube"></i> Gian hang 3D</span></a></li>
            <li><a href="/pages/admin/CaiDat.php" class="<?= adminMenuActive('CaiDat', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-gear"></i> Cai dat</span></a></li>
            <li><a href="/pages/admin/BaoCao.php" class="<?= adminMenuActive('BaoCao', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-chart-line"></i> Bao cao</span></a></li>
        </ul>
    </aside>
    <div class="admin-main">
        <header class="admin-topbar">
            <div>
                <h1><?= htmlspecialchars($tieude ?? 'Dashboard') ?></h1>
                <p><?= date('d/m/Y H:i') ?></p>
            </div>
            <div class="admin-user">
                <i class="fa-solid fa-user-shield"></i>
                <span><?= htmlspecialchars($getUser['username'] ?? 'Admin') ?></span>
                <a href="/pages/admin/Logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-sign-out-alt"></i> Dang xuat</a>
            </div>
        </header>
        <main class="admin-content">
