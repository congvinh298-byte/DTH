<?php
  if (!defined('IN_SITE')) die('The Request Not Found');
?>
<body class="page-body  page-fade" data-url="https//tuanori.vn">

<div class="page-container">
	
	<div class="sidebar-menu">

		<div class="sidebar-menu-inner">
			
			<header class="logo-env">

				<!-- logo -->
				<div class="logo">
					<a href="/Admin">
						<img src="/images/icon/logo_tuanori.png" width="140px" alt="" />
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
				<li>
					<a href="/Admin">
						<i class="entypo-gauge"></i>
						<span class="title">Dashboard</span>
					</a>
				</li>
				<li class="has-sub <?=(in_array($_SERVER['REQUEST_URI'], ['/Admin/Quanlythanhvien', '/Admin/QuanlythanhvienKhoa', '/Admin/ThanhVienON']) ? 'opened active' : ''); ?>">
					<a href="/Admin/Quanlythanhvien">
						<i class="entypo-user"></i>
						<span class="title">Thành viên</span>
					</a>
					<ul>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Quanlythanhvien') ? 'active' : ''; ?>">
							<a href="/Admin/Quanlythanhvien">
								<span class="title">Tổng thành viên</span>
							</a>
						</li>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/QuanlythanhvienKhoa') ? 'active' : ''; ?>">
							<a href="/Admin/QuanlythanhvienKhoa">
								<span class="title">Thành viên bị khóa</span>
							</a>
						</li>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/ThanhVienON') ? 'active' : ''; ?>">
							<a href="/Admin/ThanhVienON">
								<span class="title">Thành viên đang ONLINE</span>
							</a>
						</li>
						
					
					</ul>
				</li>
				<li class="has-sub <?=(in_array($_SERVER['REQUEST_URI'], ['/Admin/Danhmuctaoweb', '/Admin/ThemMauWeb']) ? 'opened active' : ''); ?>">
					<a href="layout-api.html">
						<i class="entypo-layout"></i>
						<span class="title">Danh mục tạo website</span>
					</a>
					<ul>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Danhmuctaoweb') ? 'active' : ''; ?>">
							<a href="/Admin/Danhmuctaoweb">
								<span class="title">Quản lý danh mục</span>
							</a>
						</li>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/ThemMauWeb') ? 'active' : ''; ?>">
							<a href="/Admin/ThemMauWeb">
								<span class="title">Đăng mẫu website</span>
							</a>
						</li>
						
					</ul>
				</li>

				<li class="has-sub <?=(in_array($_SERVER['REQUEST_URI'], ['/Admin/Danhmucbancode', '/Admin/ThemMaNguon']) ? 'opened active' : ''); ?>">
					<a href="layout-api.html">
						<i class="entypo-layout"></i>
						<span class="title">Danh mục bán code</span>
					</a>
					<ul>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Danhmucbancode') ? 'active' : ''; ?>">
							<a href="/Admin/Danhmucbancode">
								<span class="title">Quản lý danh mục</span>
							</a>
						</li>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/ThemMaNguon') ? 'active' : ''; ?>">
							<a href="/Admin/ThemMaNguon">
								<span class="title">Đăng bán code</span>
							</a>
						</li>
						
					</ul>
				</li>

				<li class="has-sub <?=(in_array($_SERVER['REQUEST_URI'], ['/Admin/HistoryChuyentien', '/Admin/HistoryBiendongsodu', '/Admin/Lichsugiahan', '/Admin/Quanlytaoweb', '/Admin/Quanlymuamien', '/Admin/Lichsunaptien', '/Admin/LichsunaptienATM', '/Admin/Lichsumuacode', '/Admin/HosoXacMinh']) ? 'opened active' : ''); ?>">
					<?php 
						$xuly_card = $TUANORI->num_rows(" SELECT * FROM `napcard` WHERE `status` = 'xuly' ") ?? 0;
						$xuly_domain = $TUANORI->num_rows(" SELECT * FROM `lichsumuamien` WHERE `status` = 'xuly' ") ?? 0;
						$xuly_giahan = $TUANORI->num_rows(" SELECT * FROM `lichsugiahan` WHERE `status` = 'xuly' ") ?? 0;
						$xuly_hoso = $TUANORI->num_rows(" SELECT * FROM `upload_hoso` WHERE `status` = 'xuly' ") ?? 0;
					?>
					<a href="layout-api.html">
						<i class="entypo-back"></i>
						<span class="title">Lịch sử</span>
						<span class="badge badge-info"><?=($xuly_card + $xuly_domain +$xuly_giahan + $xuly_hoso);?></span>
					</a>
					<ul>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/HistoryChuyentien') ? 'active' : ''; ?>">
							<a href="/Admin/HistoryChuyentien">
								<span class="title">Lịch sử chuyển tiền</span>
							</a>
						</li>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/HistoryBiendongsodu') ? 'active' : ''; ?>">
							<a href="/Admin/HistoryBiendongsodu">
								<span class="title">Biến động số dư</span>
							</a>
						</li>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Lichsugiahan') ? 'active' : ''; ?>">
							<a href="/Admin/Lichsugiahan">
								<span class="title">Đơn gia hạn website</span>
								<span class="badge badge-danger"><?=($xuly_giahan);?></span>

							</a>
						</li>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Quanlytaoweb') ? 'active' : ''; ?>">
							<a href="/Admin/Quanlytaoweb">
								<span class="title">Lịch sử tạo website</span>
							</a>
						</li>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Quanlymuamien') ? 'active' : ''; ?>">
							<a href="/Admin/Quanlymuamien">
								<span class="title">Lịch sử mua miền</span>
								<span class="badge badge-danger"><?=($xuly_domain);?></span>
							</a>
						</li>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Lichsunaptien') ? 'active' : ''; ?>">
							<a href="/Admin/Lichsunaptien">
								<span class="title">Lịch sử nạp thẻ</span>
								<span class="badge badge-danger"><?=($xuly_card);?></span>
							</a>
						</li>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/LichsunaptienATM') ? 'active' : ''; ?>">
							<a href="/Admin/LichsunaptienATM">
								<span class="title">Lịch sử nạp ATM</span>
							</a>
						</li>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Lichsumuacode') ? 'active' : ''; ?>">
							<a href="/Admin/Lichsumuacode">
								<span class="title">Lịch sử mua Code</span>
							</a>
						</li>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/HosoXacMinh') ? 'active' : ''; ?>">
							<a href="/Admin/HosoXacMinh">
								<span class="title">Hồ sơ xác minh</span>
								<span class="badge badge-danger"><?=($xuly_hoso);?></span>
							</a>
						</li>
					</ul>
				</li>

				<li class="has-sub <?=(in_array($_SERVER['REQUEST_URI'], ['/Admin/Hoadontsr']) ? 'opened active' : ''); ?>">
				<?php 
					$tsr = $TUANORI->num_rows(" SELECT * FROM `hoadon_vi` WHERE `status` = 'xuly' ") ?? 0;
					// $tsr = $TUANORI->num_rows(" SELECT * FROM `hoadon_vi` WHERE `status` = 'xuly' ") ?? 0;

				?>
					<a href="layout-api.html">
						<i class="entypo-window"></i>
						<span class="title">Hóa đơn</span>
						<span class="badge badge-danger"><?=$tsr;?></span>
					</a>
					<ul>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Hoadontsr') ? 'active' : ''; ?>">
							<a href="/Admin/Hoadontsr">
								<span class="title">Hóa đơn TSR</span>
								<span class="badge badge-danger"><?=$tsr;?></span>
							</a>
						</li>
					</ul>
				</li>

				<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Magiamgia') ? 'active' : ''; ?>">
					<a href="/Admin/Magiamgia">
						<i class="entypo-share"></i>
						<span class="title">Mã giảm giá</span>
					</a>
				</li>

				<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Quanlyapi') ? 'active' : ''; ?>">
					<a href="/Admin/Quanlyapi">
						<i class="entypo entypo-code"></i>
						<span class="title">Quản lý API</span>
					</a>
				</li>
				<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Blockip') ? 'active' : ''; ?>">
					<a href="/Admin/Blockip">
						<i class="entypo-block"></i>
						<span class="title">Block IP</span>
					</a>
				</li>
				<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Sukien') ? 'active' : ''; ?>">
					<a href="/Admin/Sukien">
						<i class="entypo-tag"></i>
						<span class="title">Tạo sự kiện	<?=($TUANORI->site('sukien') == 'OFF') ? '🔴' : ' 🟢';?></span>
					</a>
				</li>
				<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/SettingAdmin') ? 'active' : ''; ?>">
					<a href="/Admin/SettingAdmin">
						<i class="entypo-cog"></i>
						<span class="title">Cài đặt</span>
					</a>
				</li>
				<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/SettingClf') ? 'active' : ''; ?>">
					<a href="/Admin/SettingClf">
						<i class="entypo-cog"></i>
						<span class="title">Cấu Hình CLF</span>
					</a>
				</li>

				<li class="has-sub <?=(in_array($_SERVER['REQUEST_URI'], ['/Admin/Par_manguon', '/Admin/Par_ruttien', '/Admin/Par_biendongsodu']) ? 'opened active' : ''); ?>">
					<a href="/Admin/Par_manguon">
						<i class="entypo-users"></i>
						<span class="title">Cộng tác viên</span>
					</a>
					<ul>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Par_manguon') ? 'active' : ''; ?>">
							<a href="/Admin/Par_manguon">
								<span class="title">Mã nguồn đang bán</span>
							</a>
						</li>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Par_ruttien') ? 'active' : ''; ?>">
							<a href="/Admin/Par_ruttien">
								<span class="title">Rút tiền</span>
							</a>
						</li>
						<li class="<?=($_SERVER['REQUEST_URI'] == '/Admin/Par_biendongsodu') ? 'active' : ''; ?>">
							<a href="/Admin/Par_biendongsodu">
								<span class="title">Biến động số dư</span>
							</a>
						</li>
					
					</ul>
				</li>



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
							<img src="/images/logo_tuan.png" alt="" class="img-circle" width="44" />
							Phạm Hoàng Tuấn
						</a>
		
					
					</li>
		
				</ul>
				
			
		
			</div>
	
			<!-- Raw Links -->
			<div class="col-md-6 col-sm-4 clearfix hidden-xs">		
				<ul class="list-inline links-list pull-right">
					<li>
						<a href="/Admin/Logout">
							Log Out <i class="entypo-logout right"></i>
						</a>
					</li>
				</ul>
		
			</div>
		
		</div>
		
		<hr />
	