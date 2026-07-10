<?php
  if (!defined('IN_SITE')) die('The Request Not Found');
?>
<body class="page-body  page-fade" data-url="https//tuanori.vn">

<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->
	
	<div class="sidebar-menu">

		<div class="sidebar-menu-inner">
			
			<header class="logo-env">

				<!-- logo -->
				<div class="logo">
					<a href="/Partner">
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
					<a href="/Partner">
						<i class="entypo-gauge"></i>
						<span class="title">Dashboard</span>
					</a>
				</li>
				
				<li class="<?=($_SERVER['REQUEST_URI'] == '/Partner/ThemMaNguon') ? 'active' : ''; ?>">
					<a href="/Partner/ThemMaNguon">
						<i class="entypo-layout"></i>
						<span class="title">Đăng bán code</span>
					</a>	
				</li>

				<?php 
					$xuly_code = $TUANORI->num_rows(" SELECT * FROM `partner_code` WHERE `status` = 'xuly' AND `username` = '".$getUser['username']."' ") ?? 0;
				?>
				<li class="<?=($_SERVER['REQUEST_URI'] == '/Partner/HistoryCode') ? 'active' : ''; ?>">
					<a href="/Partner/HistoryCode">
						<i class="entypo-back"></i>
						<span class="title">Mã nguồn của bạn</span>
						<span class="badge badge-info"><?=($xuly_code );?></span>
					</a>
				</li>
				<li class="<?=($_SERVER['REQUEST_URI'] == '/Partner/Biendongsodu') ? 'active' : ''; ?>">
					<a href="/Partner/Biendongsodu">
						<i class="entypo-back"></i>
						<span class="title">Biến động số dư</span>
					</a>
				</li>
				<li class="<?=($_SERVER['REQUEST_URI'] == '/Partner/HistoryBuyCode') ? 'active' : ''; ?>">
					<a href="/Partner/HistoryBuyCode">
						<i class="entypo-back"></i>
						<span class="title">Lịch sử mua mã nguồn</span>
					</a>
				</li>

				<li class="<?=($_SERVER['REQUEST_URI'] == '/Partner/Ruttien') ? 'active' : ''; ?>">
					<a href="/Partner/Ruttien">
						<i class="entypo-credit-card"></i>
						<span class="title">Rút tiền</span>
					</a>
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
		
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color: green; font-weight: bold;">
							<img src="/images/logo_it.png" alt="" class="img-circle" width="44" />
							<?=$getUser['username'];?> - <?=number_format($getUser['money_partner']);?>đ
						</a>
		
					
					</li>
		
				</ul>
				
			
		
			</div>
	
			<!-- Raw Links -->
			<div class="col-md-6 col-sm-4 clearfix hidden-xs">		
				<ul class="list-inline links-list pull-right">
					<li>
						<a href="/">
							Quay ra <i class="entypo-logout right"></i>
						</a>
					</li>
				</ul>
		
			</div>
		
		</div>
		
		<hr />
	