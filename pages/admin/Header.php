<?php
  if (!defined('IN_SITE')) die('The Request Not Found');

  // Tính badges cho các menu ??c bi?t
  $badge_xuly_card = $DMH->num_rows(" SELECT * FROM `napcard` WHERE `status` = 'xuly' ") ?? 0;
  $badge_xuly_domain = $DMH->num_rows(" SELECT * FROM `lichsumuamien` WHERE `status` = 'xuly' ") ?? 0;
  $badge_xuly_giahan = $DMH->num_rows(" SELECT * FROM `lichsugiahan` WHERE `status` = 'xuly' ") ?? 0;
  $badge_xuly_hoso = $DMH->num_rows(" SELECT * FROM `upload_hoso` WHERE `status` = 'xuly' ") ?? 0;
  $badge_tsr = $DMH->num_rows(" SELECT * FROM `hoadon_vi` WHERE `status` = 'xuly' ") ?? 0;
  $badge_lichsu_total = $badge_xuly_card + $badge_xuly_domain + $badge_xuly_giahan + $badge_xuly_hoso;

  $menuBadges = [
      'Lịch sử' => '<span class="badge badge-info" style="font-size:10px; padding:2px 6px; border-radius:999px;"'>'.$badge_lichsu_total.'</span>',
      'Hóa đơn' => '<span class="badge badge-danger" style="font-size:10px; padding:2px 6px; border-radius:999px;"'>'.$badge_tsr.'</span>',
      'Đơn gia hạn website' => '<span class="badge badge-danger" style="font-size:10px; padding:2px 6px; border-radius:999px;"'>'.$badge_xuly_giahan.'</span>',
      'Lịch sử mua miền' => '<span class="badge badge-danger" style="font-size:10px; padding:2px 6px; border-radius:999px;"'>'.$badge_xuly_domain.'</span>',
      'Lịch sử nạp thẻ' => '<span class="badge badge-danger" style="font-size:10px; padding:2px 6px; border-radius:999px;"'>'.$badge_xuly_card.'</span>',
      'Hồ sơ xác minh' => '<span class="badge badge-danger" style="font-size:10px; padding:2px 6px; border-radius:999px;"'>'.$badge_xuly_hoso.'</span>',
      'Hóa đơn TSR' => '<span class="badge badge-danger" style="font-size:10px; padding:2px 6px; border-radius:999px;"'>'.$badge_tsr.'</span>',
  ];

  $menuIcons = [
      'Dashboard' => 'fa-gauge',
      'Quản lý đơn hàng' => 'fa-box',
      'Thành viên' => 'fa-users',
      'Danh mục tạo website' => 'fa-globe',
      'Danh mục bán code' => 'fa-code',
      'Lịch sử' => 'fa-clock-rotate-left',
      'Hóa đơn' => 'fa-file-invoice-dollar',
      'Mã giảm giá' => 'fa-ticket',
      'Quản lý API' => 'fa-key',
      'Block IP' => 'fa-ban',
      'Tạo sự kiện' => 'fa-calendar-star',
      'Cài đặt' => 'fa-gear',
      'Cấu hình CLF' => 'fa-sliders',
      'Cộng tác viên' => 'fa-handshake',
  ];

  function renderAdminMenu($DMH, $menuBadges, $menuIcons) {
      $currentUri = $_SERVER['REQUEST_URI'] ?? '';
      $menus = $DMH->get_list("SELECT * FROM `admin_menus` WHERE `parent_id` = 0 AND `is_active` = 1 ORDER BY `sort_order` ASC, `id` ASC");
      
      foreach ($menus as $menu) {
          $children = $DMH->get_list("SELECT * FROM `admin_menus` WHERE `parent_id` = '{$menu['id']}' AND `is_active` = 1 ORDER BY `sort_order` ASC, `id` ASC");
          $isActiveParent = isAdminMenuActive($menu, $children, $currentUri);
          $hasSub = !empty($children);
          $menuUrl = $menu['url'] == '#' ? 'javascript:void(0)' : htmlspecialchars($menu['url']);
          $icon = $menuIcons[$menu['title']] ?? 'fa-circle';
          $badge = $menuBadges[$menu['title']] ?? '';

          $liStyle = 'display:block; margin-bottom:4px;';
          if ($isActiveParent) {
              $liStyle .= ' background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); border-radius: 10px; box-shadow: 0 4px 12px rgba(14,165,233,0.35);';
          } else {
              $liStyle .= ' border-radius: 10px;';
          }

          $aStyle = 'display:flex; align-items:center; justify-content:space-between; padding: 14px 16px; color: ' . ($isActiveParent ? '#fff' : '#e2e8f0') . '; text-decoration:none; font-size: 14px; font-weight: 600; border-radius: 10px; transition: all 0.2s;';
          if (!$isActiveParent) {
              $aStyle .= ' hover-style';
          }

          echo "\n\t\t\t\t<li class=\"dmh-menu-item\" style=\"$liStyle\"">;
          echo "\n\t\t\t\t\t<a href=\"$menuUrl\" class=\"dmh-menu-link\" style=\"$aStyle\"";
          if (!$isActiveParent) {
              echo " onmouseover=\"this.style.background='rgba(14,165,233,0.15)'; this.style.color='#38bdf8';\" onmouseout=\"this.style.background='transparent'; this.style.color='#e2e8f0';\"";
          }
          echo ">";
          
          echo "\n\t\t\t\t\t\t<span style='display:flex; align-items:center; gap:12px;'\u003e";
          echo "\n\t\t\t\t\t\t\t<i class=\"fa-solid $icon\" style=\"font-size:16px; width:22px; text-align:center; color: " . ($isActiveParent ? '#fff' : '#94a3b8') . ";\"\u003e</i\u003e";
          echo "\n\t\t\t\t\t\t\t<span class=\"title\" style=\"font-weight:700;\"\u003e" . htmlspecialchars($menu['title']) . "</span\u003e";
          echo "\n\t\t\t\t\t\t</span\u003e";
          
          $right = '';
          if ($badge) $right .= $badge;
          if ($hasSub) {
              $right .= "<i class='fa-solid fa-chevron-down' style='font-size:11px; margin-left:8px; color: " . ($isActiveParent ? '#fff' : '#94a3b8') . ";'></i>";
          }
          if ($right) {
              echo "\n\t\t\t\t\t\t<span style='display:flex; align-items:center;'\u003e$right</span\u003e";
          }
          echo "\n\t\t\t\t\t</a>";

          if ($hasSub) {
              $subDisplay = $isActiveParent ? 'block' : 'none';
              echo "\n\t\t\t\t\t<ul class=\"dmh-submenu\" style=\"display:$subDisplay; list-style:none; margin:8px 0 8px 12px; padding:8px 0; background: rgba(15,23,42,0.5); border-left:3px solid #0ea5e9; border-radius:0 10px 10px 0;\"\u003e";
              foreach ($children as $child) {
                  $childActive = isAdminMenuActive($child, [], $currentUri);
                  $childUrl = $child['url'] == '#' ? 'javascript:void(0)' : htmlspecialchars($child['url']);
                  $childBadge = $menuBadges[$child['title']] ?? '';
                  
                  $caStyle = 'display:flex; align-items:center; justify-content:space-between; padding: 10px 14px 10px 24px; color: ' . ($childActive ? '#38bdf8' : '#cbd5e1') . '; text-decoration:none; font-size: 13px; font-weight: ' . ($childActive ? '700' : '500') . '; border-radius: 0 8px 8px 0; transition: all 0.2s;';
                  $caHover = !$childActive ? " onmouseover=\"this.style.background='rgba(14,165,233,0.1)'; this.style.color='#38bdf8'; this.style.paddingLeft='30px';\" onmouseout=\"this.style.background='transparent'; this.style.color='#cbd5e1'; this.style.paddingLeft='24px';\"" : '';
                  
                  echo "\n\t\t\t\t\t\t<li class=\"dmh-submenu-item\" style=\"margin-bottom:2px;\"\u003e";
                  echo "\n\t\t\t\t\t\t\t<a href=\"$childUrl\" class=\"dmh-submenu-link\" style=\"$caStyle\"$caHover\u003e";
                  echo "\n\t\t\t\t\t\t\t\t<span\u003e" . htmlspecialchars($child['title']) . "</span\u003e";
                  if ($childBadge) echo "\n\t\t\t\t\t\t\t\t$childBadge";
                  echo "\n\t\t\t\t\t\t\t</a\u003e";
                  echo "\n\t\t\t\t\t\t</li\u003e";
              }
              echo "\n\t\t\t\t\t</ul>";
          }

          echo "\n\t\t\t\t</li\u003e";
      }
  }

  function isAdminMenuActive($menu, $children, $currentUri) {
      if (strpos($currentUri, $menu['url']) !== false && $menu['url'] != '#') {
          return true;
      }
      foreach ($children as $child) {
          if (strpos($currentUri, $child['url']) !== false && $child['url'] != '#') {
              return true;
          }
      }
      return false;
  }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="page-body  page-fade" data-url="https//dmh.vn">

<div class="page-container">
	
	<div class="sidebar-menu" style="background: linear-gradient(180deg, #0f172a 0%, #1e3a8a 100%);">

		<div class="sidebar-menu-inner">
			
			<header class="logo-env" style="padding: 25px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.15);">

				<!-- logo -->
				<div class="logo" style="text-align: center;">
					<a href="/Admin" style="display:inline-block;">
						<img src="/public/assets/logo.png" style="max-height: 70px; width: auto; border-radius: 12px; background: #fff; padding: 6px;" alt="Điện Máy Hiếu" />
					</a>
				</div>
				
				<div style="color: #fff; font-weight: 800; font-size: 15px; margin-top: 10px; letter-spacing: 0.5px;">ĐIỆN MÁY HIẾU</div>
				<div style="color: #94a3b8; font-size: 11px; margin-top: 4px;">Hệ thống quản trị</div>

				<!-- logo collapse icon -->
				<div class="sidebar-collapse">
					<a href="#" class="sidebar-collapse-icon"><!-- add class "with-animation" if you want sidebar to have animation during expanding/collapsing transition -->
						<i class="entypo-menu"></i>
					</a>
				</div>

							
				<!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
				<div class="sidebar-mobile-menu visible-xs">
					<a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
						<i class="entypo-menu"></i>
					</a>
				</div>

			</header>
			
							
			<ul id="main-menu" class="main-menu" style="padding: 15px 12px; list-style: none;">
				<?php renderAdminMenu($DMH, $menuBadges, $menuIcons); ?>
			</ul>
			
			
		</div>

	</div>

	<div class="main-content">
				
		<div class="row">
		
			<!-- Profile Info and Notifications -->
			<div class="col-md-6 col-sm-8 clearfix">
		
				<ul class="user-info pull-left pull-none-xsm">
		
					<!-- Profile Info -->
					<li class="profile-info dropdown"><!-- add class "pull-right" if you want to place this from right -->
		
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="display:flex; align-items:center; gap:10px;">
							<img src="/public/assets/logo.png" alt="Điện Máy Hiếu" class="img-circle" width="40" style="border:2px solid #0ea5e9;" />
							<span style="font-weight:800; color:#0f172a;">Điện Máy Hiếu</span>
						</a>
		
					
					</li>
		
				</ul>
				
			
		
			</div>

			<!-- Raw Links -->
			<div class="col-md-6 col-sm-4 clearfix hidden-xs">		
				<ul class="list-inline links-list pull-right">
					<li>
						<a href="/pages/admin/Logout.php" style="background:#ef4444; color:#fff; padding:8px 16px; border-radius:8px; font-weight:700;">
							<i class="fa-solid fa-sign-out-alt"></i> Đăng xuất
						</a>
					</li>
				</ul>
		
			</div>
		
		</div>
		
		<hr />
