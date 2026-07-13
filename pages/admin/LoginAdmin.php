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
            $error = 'Tai khoan hoac mat khau khong dung, hoac khong co quyen admin.';
        }
    } else {
        $error = 'Vui long nhap day du thong tin.';
    }
}
$tieude = 'Dang nhap Admin | Dien May Hieu';
require_once(__DIR__."/../../pages/admin/Head.php");
?>
<body>
<div class="login-page">
    <div class="login-card">
        <img src="/public/assets/logo.png" alt="Dien May Hieu">
        <h2>Dang nhap quan tri</h2>
        <p>He thong quan ly Dien May Hieu</p>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Tai khoan</label>
                <input type="text" name="username" class="form-control" placeholder="Nhap tai khoan" required autofocus>
            </div>
            <div class="form-group">
                <label>Mat khau</label>
                <input type="password" name="password" class="form-control" placeholder="Nhap mat khau" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-sign-in-alt"></i> Dang nhap</button>
        </form>
    </div>
</div>
</body>
</html>
