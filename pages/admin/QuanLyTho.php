<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$tieude = 'Tho | Dien May Hieu';
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
        $err = 'Vui long nhap day du tai khoan va mat khau.';
    } elseif (strlen($password) < 6) {
        $err = 'Mat khau phai co it nhat 6 ky tu.';
    } else {
        $DMH->connect();
        $u = mysqli_real_escape_string($DMH->ketnoi, $username);
        $exists = $DMH->get_row("SELECT id FROM `users` WHERE `username` = '$u'");
        if ($exists) {
            $err = 'Tai khoan nay da ton tai.';
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
                $msg = 'Da tao tai khoan tho thanh cong.';
            } else {
                $err = 'Tao tai khoan that bai, vui long thu lai.';
            }
        }
    }
}

$tho = $DMH->get_list("SELECT * FROM `users` WHERE `level` = 'tho' ORDER BY id DESC LIMIT 200");
?>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;"><i class="fa-solid fa-wrench" style="color:#0ea5e9;"></i> Quan ly tho</h2>

<div class="card">
    <div class="card-header"><h3><i class="fa-solid fa-user-plus"></i> Tao tai khoan tho moi</h3></div>
    <div class="card-body">
        <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>

        <form method="POST" action="">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                <div class="form-group">
                    <label>Tai khoan</label>
                    <input type="text" name="username" class="form-control" placeholder="Nhap username" required>
                </div>
                <div class="form-group">
                    <label>Mat khau</label>
                    <input type="password" name="password" class="form-control" placeholder="It nhat 6 ky tu" required>
                </div>
                <div class="form-group">
                    <label>Ho ten</label>
                    <input type="text" name="name" class="form-control" placeholder="Ho ten tho">
                </div>
                <div class="form-group">
                    <label>So dien thoai</label>
                    <input type="text" name="phone" class="form-control" placeholder="So dien thoai">
                </div>
            </div>
            <button type="submit" name="create_tho" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tao tai khoan</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Danh sach tho</h3></div>
    <div class="card-body" style="padding:0;">
        <?php if (empty($tho)): ?>
            <div style="text-align:center; padding: 40px 20px; color:#64748b;">Chua co tho nao.</div>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table class="table">
                    <thead><tr><th>ID</th><th>Tai khoan</th><th>Ho ten</th><th>So dien thoai</th><th>Trang thai</th></tr></thead>
                    <tbody>
                    <?php foreach ($tho as $t): ?>
                        <tr>
                            <td><?= (int)$t['id'] ?></td>
                            <td><strong><?= htmlspecialchars($t['username']) ?></strong></td>
                            <td><?= htmlspecialchars($t['name'] ?? $t['fullname'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($t['phone'] ?? '-') ?></td>
                            <td>
                                <?php if (!empty($t['banned']) && $t['banned'] == 'ON'): ?>
                                    <span class="badge badge-completed">Hoat dong</span>
                                <?php else: ?>
                                    <span class="badge badge-cancelled">Khoa</span>
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
