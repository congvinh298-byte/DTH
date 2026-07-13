<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

// Neu da dang nhap admin -> chuyen vao trang admin tong quan
if (!empty($getUser) && $getUser['level'] == 'admin') {
    header("Location: /Admin");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = check_string($_POST['username']);
    $password = check_string($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ tài khoản và mật khẩu.';
    } else {
        $md5pass = md5($password);
        $check = $DMH->get_row("SELECT * FROM `users` WHERE `username` = '$username' AND `password` = '$md5pass' AND `level` = 'admin' AND `banned` = 'ON'");
        
        if ($check) {
            $token = random('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789', 64);
            $DMH->update("users", ['tokenlog' => $token], " `id` = '".$check['id']."' ");
            setcookie('token', $token, time() + 86400 * 30, '/');
            $_SESSION['loginadmin'] = true;
            header("Location: /Admin");
            exit;
        } else {
            $error = 'Tài khoản hoặc mật khẩu không chính xác.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Quản Trị | Điện Máy Hiếu</title>
    <link rel="icon" href="/public/assets/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
            width: 100%;
            max-width: 420px;
            padding: 40px 32px;
            text-align: center;
        }
        .logo-wrap {
            margin-bottom: 24px;
        }
        .logo-wrap img {
            width: 90px;
            height: 90px;
            object-fit: contain;
            border-radius: 16px;
            background: #f1f5f9;
            padding: 10px;
        }
        h1 {
            color: #0f172a;
            font-size: 26px;
            font-weight: 900;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }
        .subtitle {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 28px;
            font-weight: 600;
        }
        .error-box {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid #fecaca;
        }
        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }
        label {
            display: block;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 14px 16px 14px 46px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            background: #f8fafc;
            transition: all 0.2s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #38bdf8;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(56,189,248,0.15);
        }
        button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(37,99,235,0.35);
            margin-top: 8px;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37,99,235,0.45);
        }
        button:active {
            transform: translateY(0);
        }
        .back-link {
            display: inline-block;
            margin-top: 24px;
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        .back-link:hover {
            color: #334155;
        }
        @media (max-width: 480px) {
            .login-card { padding: 32px 24px; }
            h1 { font-size: 22px; }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-wrap">
            <img src="/public/assets/logo.png" alt="Điện Máy Hiếu">
        </div>
        
        <h1>ĐIỆN MÁY HIẾU</h1>
        <p class="subtitle">Quản lý cửa hàng - Gọi thợ tận nhà</p>
        
        <?php if ($error): ?>
            <div class="error-box"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" autocomplete="off">
            <div class="form-group">
                <label>Tài khoản</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="username" id="username" placeholder="Nhập tài khoản" required value="anhthienvodich">
                </div>
            </div>
            
            <div class="form-group">
                <label>Mật khẩu</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Nhập mật khẩu" required>
                </div>
            </div>
            
            <button type="submit">
                <i class="fa-solid fa-right-to-bracket"></i> ĐĂNG NHẬP HỆ THỐNG
            </button>
        </form>
        
        <a href="/" class="back-link"><i class="fa-solid fa-arrow-left"></i> Quay lại trang chủ</a>
    </div>

</body>
</html>
