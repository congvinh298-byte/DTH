<?php
  if (!defined('IN_SITE')) die('The Request Not Found');

  $currentUri = $_SERVER['REQUEST_URI'] ?? '';

  $adminMenus = $DMH->get_list("SELECT * FROM `admin_menus` WHERE `parent_id` = 0 AND `is_active` = 1 ORDER BY `sort_order` ASC, `id` ASC");

  $menuIcons = [
      'Dashboard' => 'fa-gauge',
      'Quan ly don hang' => 'fa-box',
      'Quan ly dat lich' => 'fa-calendar-check',
      'Thanh vien' => 'fa-users',
      'Tho' => 'fa-wrench',
      'Danh muc' => 'fa-folder-tree',
      'San pham' => 'fa-boxes-stacked',
      'Dich vu' => 'fa-screwdriver-wrench',
      'Lich su' => 'fa-clock-rotate-left',
      'Hoa don' => 'fa-file-invoice-dollar',
      'Ma giam gia' => 'fa-ticket',
      'Cai dat' => 'fa-gear',
      'Bao cao' => 'fa-chart-line',
  ];

  function adminMenuActive($url, $currentUri) {
      if ($url == '#' || $url == '') return false;
      return strpos($currentUri, $url) !== false;
  }

  function adminMenuOpen($menu, $currentUri, $DMH) {
      $children = $DMH->get_list("SELECT * FROM `admin_menus` WHERE `parent_id" .
          " = '" . (int)$menu['id'] . "' AND `is_active` = 1 ORDER BY `sort_order` ASC");
      foreach ($children as $child) {
          if (adminMenuActive($child['url'], $currentUri)) return true;
      }
      return adminMenuActive($menu['url'], $currentUri);
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
            <li><a href="/pages/admin/QuanLyDonHang.php" class="<?= adminMenuActive('/QuanLyDonHang', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-box"></i> Don hang</span></a></li>
            <li><a href="/pages/admin/QuanLyDatLich.php" class="<?= adminMenuActive('/QuanLyDatLich', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-calendar-check"></i> Dat lich</span></a></li>
            <li><a href="/pages/admin/QuanLyThanhVien.php" class="<?= adminMenuActive('/QuanLyThanhVien', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-users"></i> Thanh vien</span></a></li>
            <li><a href="/pages/admin/QuanLyTho.php" class="<?= adminMenuActive('/QuanLyTho', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-wrench"></i> Tho</span></a></li>
            <li><a href="/pages/admin/CaiDat.php" class="<?= adminMenuActive('/CaiDat', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-gear"></i> Cai dat</span></a></li>
            <li><a href="/pages/admin/BaoCao.php" class="<?= adminMenuActive('/BaoCao', $currentUri) ? 'active' : '' ?>"><span><i class="fa-solid fa-chart-line"></i> Bao cao</span></a></li>
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
