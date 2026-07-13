<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$tieude = 'Quản lý thợ | Điện Máy Hiếu';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");

$msg = '';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_tho'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    if ($username == '' || $password == '') {
        $err = 'Vui lòng nhập đầy đủ tài khoản và mật khẩu.';
    } elseif (strlen($password) < 6) {
        $err = 'Mật khẩu phải có ít nhất 6 ký tự.';
    } else {
        $DMH->connect();
        $u = mysqli_real_escape_string($DMH->ketnoi, $username);
        $exists = $DMH->get_row("SELECT id FROM `users` WHERE `username` = '$u'");
        if ($exists) {
            $err = 'Tài khoản này đã tồn tại.';
        } else {
            $ok = $DMH->insert('users', [
                'username' => $username,
                'password' => md5($password),
                'name' => $name,
                'fullname' => $name,
                'phone' => $phone,
                'level' => 'tho',
                'banned' => 'ON',
                'money' => 0,
                'verify' => 0,
            ]);
            if ($ok) {
                $msg = 'Đã tạo tài khoản thợ thành công.';
            } else {
                $err = 'Tạo tài khoản thất bại, vui lòng thử lại.';
            }
        }
    }
}

$tho = $DMH->get_list("SELECT * FROM `users` WHERE `level` = 'tho' ORDER BY id DESC LIMIT 200");
?>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;"><i class="fa-solid fa-wrench" style="color:#0ea5e9;"></i> Quản lý thợ</h2>

<div class="card">
    <div class="card-header"><h3><i class="fa-solid fa-user-plus"></i> Tạo tài khoản thợ mới</h3></div>
    <div class="card-body">
        <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>

        <form method="POST" action="">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                <div class="form-group">
                    <label>Tài khoản <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="username" class="form-control" placeholder="Nhập username" required>
                </div>
                <div class="form-group">
                    <label>Mật khẩu <span style="color:#dc2626;">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Ít nhất 6 ký tự" required>
                </div>
                <div class="form-group">
                    <label>Họ tên</label>
                    <input type="text" name="name" class="form-control" placeholder="Họ tên thợ">
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" placeholder="Số điện thoại">
                </div>
            </div>
            <button type="submit" name="create_tho" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tạo tài khoản</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Danh sách thợ</h3></div>
    <div class="card-body" style="padding:0;">
        <?php if (empty($tho)): ?>
            <div style="text-align:center; padding: 40px 20px; color:#64748b;">Chưa có thợ nào.</div>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table class="table">
                    <thead><tr><th>ID</th><th>Tài khoản</th><th>Họ tên</th><th>Số điện thoại</th><th>Trạng thái</th></tr></thead>
                    <tbody>
                    <?php foreach ($tho as $t): ?>
                        <tr>
                            <td><?= (int)$t['id'] ?></td>
                            <td><strong><?= htmlspecialchars($t['username']) ?></strong></td>
                            <td><?= htmlspecialchars($t['name'] ?? $t['fullname'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($t['phone'] ?? '-') ?></td>
                            <td>
                                <?php if (!empty($t['banned']) && $t['banned'] == 'ON'): ?>
                                    <span class="badge badge-completed">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge badge-cancelled">Khóa</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
