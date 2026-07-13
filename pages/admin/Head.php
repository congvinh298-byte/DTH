<?php
  if (!defined('IN_SITE')) die('The Request Not Found');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="He thong quan tri Dien May Hieu">
    <link rel="icon" href="/public/assets/logo.png">
    <title><?=htmlspecialchars($tieude ?? 'Admin | Dien May Hieu')?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Inter:400,600,700,800">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, 'Segoe UI', Tahoma, sans-serif;
            background: #f1f5f9;
            color: #334155;
            font-size: 14px;
        }
        a { text-decoration: none; }
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        .admin-sidebar {
            width: 260px;
            background: linear-gradient(180deg, #0f172a 0%, #1e3a8a 100%);
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(15,23,42,0.25);
        }
        .admin-brand {
            padding: 24px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }
        .admin-brand img {
            max-height: 60px;
            background: #fff;
            border-radius: 12px;
            padding: 6px;
        }
        .admin-brand h3 {
            margin: 12px 0 0;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .admin-brand small {
            color: #94a3b8;
            font-size: 11px;
            font-weight: 600;
        }
        .admin-menu {
            list-style: none;
            margin: 0;
            padding: 12px 10px;
        }
        .admin-menu li {
            margin-bottom: 4px;
        }
        .admin-menu a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 14px;
            color: #e2e8f0;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.15s;
        }
        .admin-menu a:hover, .admin-menu a.active {
            background: rgba(14,165,233,0.18);
            color: #38bdf8;
        }
        .admin-menu a.active {
            background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(14,165,233,0.35);
        }
        .admin-menu a i:first-child {
            width: 22px;
            text-align: center;
            margin-right: 10px;
            font-size: 15px;
        }
        .admin-submenu {
            list-style: none;
            margin: 6px 0 6px 16px;
            padding: 6px 0;
            border-left: 2px solid #0ea5e9;
            display: none;
        }
        .admin-submenu.open { display: block; }
        .admin-submenu a {
            padding: 9px 12px 9px 18px;
            font-size: 13px;
            font-weight: 500;
            color: #cbd5e1;
        }
        .admin-submenu a.active, .admin-submenu a:hover {
            color: #38bdf8;
            background: rgba(14,165,233,0.1);
        }
        .admin-main {
            flex: 1;
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .admin-topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 26px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 900;
        }
        .admin-topbar h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
        }
        .admin-topbar p {
            margin: 3px 0 0;
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
        }
        .admin-user {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            color: #334155;
        }
        .admin-user i {
            color: #0ea5e9;
            font-size: 18px;
        }
        .admin-content {
            padding: 24px 26px;
            flex: 1;
        }
        .card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(15,23,42,0.04);
            margin-bottom: 22px;
            overflow: hidden;
        }
        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-header h3 {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }
        .card-body {
            padding: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(15,23,42,0.04);
        }
        .stat-card h4 {
            margin: 0 0 8px;
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-card .value {
            font-size: 26px;
            font-weight: 900;
            color: #0f172a;
        }
        .stat-card .icon {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .table th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .table td {
            padding: 13px 14px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-weight: 600;
        }
        .table tr:hover td { background: #f8fafc; }
        .table tr:last-child td { border-bottom: none; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 9px;
            border: none;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.15s;
            color: #fff;
        }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(0,0,0,0.12); }
        .btn-primary { background: #2563eb; }
        .btn-success { background: #16a34a; }
        .btn-danger { background: #dc2626; }
        .btn-info { background: #0ea5e9; }
        .btn-warning { background: #f59e0b; color: #fff; }
        .btn-secondary { background: #64748b; }
        .btn-sm { padding: 6px 10px; font-size: 12px; }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
        }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-shipping { background: #dbeafe; color: #1e40af; }
        .badge-completed { background: #dcfce7; color: #166534; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .badge-default { background: #f1f5f9; color: #475569; }
        .form-group { margin-bottom: 14px; }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 700;
            color: #475569;
            font-size: 13px;
        }
        .form-control {
            width: 100%;
            padding: 10px 13px;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            font-size: 14px;
            font-family: inherit;
        }
        .form-control:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56,189,248,0.15);
        }
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        }
        .login-card {
            background: #fff;
            border-radius: 18px;
            padding: 38px 34px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            text-align: center;
        }
        .login-card img {
            max-height: 80px;
            margin-bottom: 18px;
            border-radius: 14px;
            background: #f1f5f9;
            padding: 8px;
        }
        .login-card h2 {
            margin: 0 0 8px;
            font-size: 22px;
            color: #0f172a;
        }
        .login-card p {
            color: #64748b;
            margin-bottom: 26px;
            font-weight: 600;
        }
        .login-card .btn {
            width: 100%;
            justify-content: center;
            padding: 12px;
            font-size: 15px;
        }
        .alert {
            padding: 12px 14px;
            border-radius: 9px;
            margin-bottom: 16px;
            font-weight: 700;
            font-size: 13px;
        }
        .alert-danger { background: #fee2e2; color: #991b1b; }
        .alert-success { background: #dcfce7; color: #166534; }
        .filter-bar { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
        .filter-bar a {
            padding: 8px 14px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            color: #475569;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            font-size: 13px;
        }
        .filter-bar a:hover { background: #e2e8f0; }
        .filter-bar a.active {
            background: #0ea5e9;
            color: #fff;
            border-color: #0ea5e9;
            box-shadow: 0 4px 12px rgba(14,165,233,0.25);
        }
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,23,42,0.6);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-backdrop.active { display: flex; }
        .modal-box {
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            width: 100%;
            max-width: 560px;
            color: #0f172a;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        }
        .modal-box h3 { margin-top: 0; margin-bottom: 20px; font-size: 20px; }
        .modal-box label {
            display: block;
            color: #475569;
            font-size: 13px;
            font-weight: 700;
            margin: 14px 0 6px;
            text-transform: uppercase;
        }
        .modal-box select, .modal-box input, .modal-box textarea {
            width: 100%;
            padding: 12px 14px;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            color: #0f172a;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            box-sizing: border-box;
        }
        .modal-box select:focus, .modal-box input:focus, .modal-box textarea:focus {
            outline: none;
            border-color: #38bdf8;
            background: #fff;
        }
        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 22px;
        }
        .modal-actions button {
            padding: 10px 18px;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            font-size: 13px;
        }
        .btn-modal-primary { background: #0ea5e9; color: #fff; }
        .btn-modal-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .tabs { display: flex; gap: 8px; margin-bottom: 18px; border-bottom: 1px solid #e2e8f0; }
        .tabs a {
            padding: 10px 18px;
            font-weight: 700;
            color: #64748b;
            border-bottom: 3px solid transparent;
        }
        .tabs a.active { color: #0ea5e9; border-bottom-color: #0ea5e9; }
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 18px;
        }
        .search-box input {
            flex: 1;
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            font-size: 14px;
        }
        .search-box button { white-space: nowrap; }
        @media (max-width: 768px) {
            .admin-sidebar { transform: translateX(-100%); transition: transform .3s; }
            .admin-sidebar.open { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
