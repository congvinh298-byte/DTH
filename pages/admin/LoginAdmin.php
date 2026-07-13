<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

if (!empty($_SESSION['loginadmin'])) {
    header("Location: /Admin");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username && $password) {
        $hash = md5($password);
        $DMH->connect();
        $u = mysqli_real_escape_string($DMH->ketnoi, $username);
        $user = $DMH->get_row("SELECT * FROM `users` WHERE `username` = '$u' AND `password` = '$hash' AND `level` IN ('admin','bct')");
        if ($user) {
            $_SESSION['loginadmin'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            header("Location: /Admin");
            exit;
        } else {
            $error = 'Tài khoản hoặc mật khẩu không đúng, hoặc tài khoản không có quyền quản trị.';
        }
    } else {
        $error = 'Vui lòng nhập đầy đủ tài khoản và mật khẩu.';
    }
}
$tieude = 'Đăng nhập quản trị | Điện Máy Hiếu';
require_once(__DIR__."/../../pages/admin/Head.php");
?>
<body>
<div class="login-page">
    <div class="login-card">
        <img src="/public/assets/logo.png" alt="Điện Máy Hiếu">
        <h2>Đăng nhập quản trị</h2>
        <p>Hệ thống quản lý Điện Máy Hiếu</p>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Tài khoản</label>
                <input type="text" name="username" class="form-control" placeholder="Nhập tài khoản" required autofocus>
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-sign-in-alt"></i> Đăng nhập</button>
        </form>
    </div>
</div>
</body>
</html>
