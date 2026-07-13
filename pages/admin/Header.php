<?php
  if (!defined('IN_SITE')) die('The Request Not Found');

  // Tính badges cho các menu d?c bi?t
  $badge_xuly_card = $DMH->num_rows(" SELECT * FROM `napcard` WHERE `status` = 'xuly' ") ?? 0;
  $badge_xuly_domain = $DMH->num_rows(" SELECT * FROM `lichsumuamien` WHERE `status` = 'xuly' ") ?? 0;
  $badge_xuly_giahan = $DMH->num_rows(" SELECT * FROM `lichsugiahan` WHERE `status` = 'xuly' ") ?? 0;
  $badge_xuly_hoso = $DMH->num_rows(" SELECT * FROM `upload_hoso` WHERE `status` = 'xuly' ") ?? 0;
  $badge_tsr = $DMH->num_rows(" SELECT * FROM `hoadon_vi` WHERE `status` = 'xuly' ") ?? 0;
  $badge_lichsu_total = $badge_xuly_card + $badge_xuly_domain + $badge_xuly_giahan + $badge_xuly_hoso;

  $menuBadges = [
      'Lịch sử' => '<span class="badge badge-info">'.$badge_lichsu_total.'</span>',
      'Hóa đơn' => '<span class="badge badge-danger">'.$badge_tsr.'</span>',
      'Đơn gia hạn website' => '<span class="badge badge-danger">'.$badge_xuly_giahan.'</span>',
      'Lịch sử mua miền' => '<span class="badge badge-danger">'.$badge_xuly_domain.'</span>',
      'Lịch sử nạp thẻ' => '<span class="badge badge-danger">'.$badge_xuly_card.'</span>',
      'Hồ sơ xác minh' => '<span class="badge badge-danger">'.$badge_xuly_hoso.'</span>',
      'Hóa đơn TSR' => '<span class="badge badge-danger">'.$badge_tsr.'</span>',
  ];

  function renderAdminMenu($DMH, $menuBadges) {
      $currentUri = $_SERVER['REQUEST_URI'] ?? '';
      $menus = $DMH->get_list("SELECT * FROM `admin_menus` WHERE `parent_id` = 0 AND `is_active` = 1 ORDER BY `sort_order` ASC, `id` ASC");
      
      foreach ($menus as $menu) {
          $children = $DMH->get_list("SELECT * FROM `admin_menus` WHERE `parent_id` = '{$menu['id']}' AND `is_active` = 1 ORDER BY `sort_order` ASC, `id` ASC");
          $isActiveParent = isAdminMenuActive($menu, $children, $currentUri);
          $hasSub = !empty($children);
          $menuUrl = $menu['url'] == '#' ? 'javascript:void(0)' : htmlspecialchars($menu['url']);
          $icon = !empty($menu['icon']) ? $menu['icon'] : 'entypo-doc-text';
          $badge = $menuBadges[$menu['title']] ?? '';

          // Special: T?o s? ki?n thm trng thi ?en
          $extraIcon = '';
          if ($menu['title'] == 'Tạo sự kiện') {
              $extraIcon = ($DMH->site('sukien') == 'OFF') ? ' ?' : ' ?';
          }

          $liClass = '';
          if ($hasSub) {
              $liClass = 'has-sub ' . ($isActiveParent ? 'opened active' : '');
          } else {
              $liClass = $isActiveParent ? 'active' : '';
          }

          echo "\n\t\t\t\t<li class=\"$liClass\"\u003e";
          echo "\n\t\t\t\t\t<a href=\"$menuUrl\"\u003e";
          echo "\n\t\t\t\t\t\t<i class=\"$icon\"></i>";
          echo "\n\t\t\t\t\t\t<span class=\"title\">" . htmlspecialchars($menu['title']) . $extraIcon . "</span>";
          echo $badge;
          echo "\n\t\t\t\t\t</a>";

          if ($hasSub) {
              echo "\n\t\t\t\t\t<ul>";
              foreach ($children as $child) {
                  $childActive = isAdminMenuActive($child, [], $currentUri);
                  $childUrl = $child['url'] == '#' ? 'javascript:void(0)' : htmlspecialchars($child['url']);
                  $childBadge = $menuBadges[$child['title']] ?? '';
                  echo "\n\t\t\t\t\t\t<li class=\"" . ($childActive ? 'active' : '') . "\"\u003e";
                  echo "\n\t\t\t\t\t\t\t<a href=\"$childUrl\"\u003e";
                  echo "\n\t\t\t\t\t\t\t\t<span class=\"title\">" . htmlspecialchars($child['title']) . "</span>";
                  echo $childBadge;
                  echo "\n\t\t\t\t\t\t\t</a>";
                  echo "\n\t\t\t\t\t\t</li>";
              }
              echo "\n\t\t\t\t\t</ul>";
          }

          echo "\n\t\t\t\t</li>";
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
<body class="page-body  page-fade" data-url="https//dmh.vn">

<div class="page-container">
	
	<div class="sidebar-menu">

		<div class="sidebar-menu-inner">
			
			<header class="logo-env">

				<!-- logo -->
				<div class="logo">
					<a href="/Admin">
						<img src="/public/assets/logo.png" width="140px" alt="Điện Máy Hiếu" />
					</a>
				</div>

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
			
								
			<ul id="main-menu" class="main-menu">
				<style>
					/* Custom clean sidebar menu styles */
					.sidebar-menu .logo-env { padding: 20px 15px !important; border-bottom: 1px solid rgba(255,255,255,0.1) !important; }
					.sidebar-menu .logo img { max-height: 55px !important; width: auto !important; }
					
					#main-menu.main-menu { padding-top: 10px !important; }
					#main-menu.main-menu li a {
						font-size: 14px !important;
						font-weight: 600 !important;
						padding: 14px 18px !important;
						color: #e2e8f0 !important;
						border-bottom: 1px solid rgba(255,255,255,0.05) !important;
						transition: all 0.2s ease !important;
					}
					#main-menu.main-menu li a i {
						margin-right: 12px !important;
						font-size: 16px !important;
						width: 22px !important;
						text-align: center !important;
						color: #94a3b8 !important;
					}
					#main-menu.main-menu li a:hover {
						background: rgba(14,165,233,0.15) !important;
						color: #38bdf8 !important;
					}
					#main-menu.main-menu li a:hover i { color: #38bdf8 !important; }
					#main-menu.main-menu li.active > a,
					#main-menu.main-menu li.opened > a {
						background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%) !important;
						color: #fff !important;
						box-shadow: 0 4px 12px rgba(14,165,233,0.3) !important;
					}
					#main-menu.main-menu li.active > a i,
					#main-menu.main-menu li.opened > a i { color: #fff !important; }
					#main-menu.main-menu li.has-sub > a:after {
						content: '\f107' !important;
						font-family: 'FontAwesome' !important;
						float: right !important;
						font-size: 12px !important;
						color: #94a3b8 !important;
					}
					#main-menu.main-menu li.has-sub.opened > a:after { color: #fff !important; }
					
					#main-menu.main-menu ul {
						background: rgba(15,23,42,0.5) !important;
						border-left: 3px solid #0ea5e9 !important;
						margin: 0 !important;
						padding: 8px 0 !important;
					}
					#main-menu.main-menu ul li a {
						font-size: 13px !important;
						padding: 10px 18px 10px 50px !important;
						color: #cbd5e1 !important;
						border-bottom: none !important;
					}
					#main-menu.main-menu ul li a:hover {
						background: rgba(14,165,233,0.1) !important;
						color: #38bdf8 !important;
						padding-left: 55px !important;
					}
					#main-menu.main-menu ul li.active > a {
						background: rgba(14,165,233,0.2) !important;
						color: #38bdf8 !important;
						font-weight: 700 !important;
					}
					
					#main-menu.main-menu .badge {
						font-size: 10px !important;
						padding: 3px 7px !important;
						border-radius: 999px !important;
						margin-left: 8px !important;
						float: right !important;
						margin-top: 2px !important;
					}
					
					@media (max-width: 768px) {
						#main-menu.main-menu li a { font-size: 13px !important; padding: 12px 14px !important; }
						#main-menu.main-menu ul li a { padding-left: 35px !important; }
					}
				</style>
				<?php renderAdminMenu($DMH, $menuBadges); ?>
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
		
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<img src="/public/assets/logo.png" alt="Điện Máy Hiếu" class="img-circle" width="44" />
							Điện Máy Hiếu
						</a>
		
					
					</li>
		
				</ul>
				
			
		
			</div>

			<!-- Raw Links -->
			<div class="col-md-6 col-sm-4 clearfix hidden-xs">		
				<ul class="list-inline links-list pull-right">
					<li>
						<a href="/pages/admin/Logout.php">
							Log Out <i class="entypo-logout right"></i>
						</a>
					</li>
				</ul>
		
			</div>
		
		</div>
		
		<hr />
