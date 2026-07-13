<?php
  if (!defined('IN_SITE')) die('The Request Not Found');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="description" content="He thong quan tri Dien May Hieu" />
	<meta name="author" content="" />

	<link rel="icon" href="/public/assets/logo.png">

	<title><?=$tieude;?></title>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	<link rel="stylesheet" href="/assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css">
	<link rel="stylesheet" href="/assets/css/font-icons/entypo/css/entypo.css">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
	<link rel="stylesheet" href="/assets/css/bootstrap.css">
	<link rel="stylesheet" href="/assets/css/neon-core.css">
	<link rel="stylesheet" href="/assets/css/neon-theme.css">
	<link rel="stylesheet" href="/assets/css/neon-forms.css">
	<link rel="stylesheet" href="/assets/css/skins/blue.css">
	<link class="main-stylesheet" href="/build/assets/style.css" rel="stylesheet" type="text/css">

	<style>
		/* === DMH Admin Unified Overrides === */
		body {
			font-family: 'Segoe UI', 'Noto Sans', Tahoma, Geneva, Verdana, sans-serif !important;
			background: #f1f5f9 !important;
			color: #334155 !important;
		}
		.page-body {
			background: #f1f5f9 !important;
		}
		.page-container {
			display: flex !important;
			min-height: 100vh !important;
		}
		.sidebar-menu {
			width: 280px !important;
			min-height: 100vh !important;
			position: fixed !important;
			left: 0;
			top: 0;
			bottom: 0;
			z-index: 1000;
			background: linear-gradient(180deg, #0f172a 0%, #1e3a8a 100%) !important;
			border-right: none !important;
			box-shadow: 4px 0 24px rgba(15,23,42,0.25);
		}
		.main-content {
			flex: 1 !important;
			margin-left: 280px !important;
			background: #f1f5f9 !important;
			min-height: 100vh !important;
			padding-bottom: 40px;
		}
		.main-content .row:first-child {
			background: #fff !important;
			border-bottom: 1px solid #e2e8f0 !important;
			padding: 14px 24px !important;
			margin: 0 !important;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}
		.main-content .row:first-child .user-info,
		.main-content .row:first-child .links-list {
			margin: 0 !important;
		}
		.main-content hr {
			display: none;
		}
		.logo-env {
			border-bottom: 1px solid rgba(255,255,255,0.12) !important;
		}
		.logo-env .sidebar-collapse,
		.logo-env .sidebar-mobile-menu {
			display: none !important;
		}
		.main-menu {
			padding: 12px 10px !important;
		}
		.page-title,
		.panel-title,
		h3,
		h4,
		h2 {
			color: #0f172a !important;
			font-weight: 800 !important;
		}
		.panel {
			background: #fff !important;
			border: 1px solid #e2e8f0 !important;
			border-radius: 16px !important;
			box-shadow: 0 4px 12px rgba(15,23,42,0.05) !important;
		}
		.panel-heading {
			background: #fff !important;
			border-bottom: 1px solid #e2e8f0 !important;
			border-radius: 16px 16px 0 0 !important;
			padding: 16px 20px !important;
			font-weight: 800 !important;
		}
		.table {
			background: #fff !important;
			border-radius: 12px;
		}
		.table > thead > tr > th {
			background: #f8fafc !important;
			color: #64748b !important;
			font-weight: 800 !important;
			font-size: 11px !important;
			text-transform: uppercase !important;
			letter-spacing: 0.5px;
			border-bottom: 1px solid #e2e8f0 !important;
		}
		.table > tbody > tr > td {
			border-bottom: 1px solid #f1f5f9 !important;
			color: #334155 !important;
		}
		.btn {
			border-radius: 10px !important;
			font-weight: 700 !important;
			padding: 8px 16px !important;
			transition: all 0.15s !important;
		}
		.btn:hover {
			transform: translateY(-1px);
			box-shadow: 0 4px 10px rgba(0,0,0,0.1);
		}
		.btn-success { background: #16a34a !important; border-color: #16a34a !important; }
		.btn-danger  { background: #dc2626 !important; border-color: #dc2626 !important; }
		.btn-info    { background: #0ea5e9 !important; border-color: #0ea5e9 !important; }
		.btn-warning { background: #f59e0b !important; border-color: #f59e0b !important; }
		.btn-primary { background: #2563eb !important; border-color: #2563eb !important; }
		.badge {
			border-radius: 999px !important;
			padding: 4px 10px !important;
			font-size: 11px !important;
			font-weight: 800 !important;
		}
		.form-control {
			border-radius: 10px !important;
			border: 1px solid #e2e8f0 !important;
			padding: 10px 14px !important;
		}
		.form-control:focus {
			border-color: #38bdf8 !important;
			box-shadow: 0 0 0 4px rgba(56,189,248,0.15) !important;
		}
		@media (max-width: 768px) {
			.sidebar-menu { width: 240px !important; transform: translateX(-100%); transition: transform 0.3s; }
			.sidebar-menu.open { transform: translateX(0); }
			.main-content { margin-left: 0 !important; }
		}
	</style>

	<script src="/assets/js/jquery-1.11.3.min.js"></script>
    <script src="/build/assets/cute-alert.js"></script>
	<script type="text/javascript">
		jQuery( document ).ready( function( $ ) {
			var $table1 = jQuery( '#table-1' );
			if ($table1.length) {
				$table1.DataTable( {
					"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
					"bStateSave": true
				});
				$table1.closest( '.dataTables_wrapper' ).find( 'select' ).select2( {
					minimumResultsForSearch: -1
				});
			}
		} );
	</script>
</head>
