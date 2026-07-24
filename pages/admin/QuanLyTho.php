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
                    <thead><tr><th>ID</th><th>Tài khoản</th><th>Họ tên</th><th>Số điện thoại</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
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
                            <td>
                                <button onclick="openResetPassword(<?= (int)$t['id'] ?>, '<?= htmlspecialchars(addslashes($t['name'] ?? $t['username']), ENT_QUOTES) ?>')" style="padding:6px 12px; border-radius:6px; border:none; background:#f59e0b; color:#0f172a; font-weight:700; font-size:12px; cursor:pointer; margin-right:4px;"><i class="fa-solid fa-key"></i> Đổi MK</button>
                                <button onclick="xemLichSu(<?= (int)$t['id'] ?>)" style="padding:6px 12px; border-radius:6px; border:none; background:#6366f1; color:#fff; font-weight:700; font-size:12px; cursor:pointer; margin-right:4px;"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử</button>
                                <button id="btn-toggle-<?= (int)$t['id'] ?>" onclick="toggleThoStatus(<?= (int)$t['id'] ?>)" style="padding:6px 12px; border-radius:6px; border:none; background:<?= $t['banned'] == 'ON' ? '#dc2626' : '#16a34a' ?>; color:#fff; font-weight:700; font-size:12px; cursor:pointer;">
                                    <?= $t['banned'] == 'ON' ? '<i class="fa-solid fa-lock"></i> Khóa' : '<i class="fa-solid fa-lock-open"></i> Mở khóa' ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal lịch sử đơn của thợ -->
<div class="modal-backdrop" id="lichSuModal">
    <div class="modal-box" style="max-width:680px; max-height:80vh; overflow-y:auto;">
        <h3 id="lichsu_title"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử đơn</h3>
        <div id="lichsu_stats" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:12px; margin-bottom:16px;"></div>
        <div id="lichsu_table"></div>
        <div class="modal-actions">
            <button class="btn-modal-secondary" onclick="document.getElementById('lichSuModal').classList.remove('active')">Đóng</button>
        </div>
    </div>
</div>

<!-- Modal Đổi Mật Khẩu Thợ -->
<div class="modal-backdrop" id="resetPassModal">
    <div class="modal-box" style="max-width:420px;">
        <h3><i class="fa-solid fa-key"></i> Đặt mật khẩu cho Thợ</h3>
        <p style="color:#64748b; font-size:13px; margin:4px 0 16px;">Thợ: <strong id="resetPass_thoName" style="color:#0f172a;"></strong></p>
        <input type="hidden" id="resetPass_thoId">
        <div class="form-group">
            <label>Mật khẩu mới <span style="color:#dc2626;">*</span></label>
            <input type="password" id="resetPass_newPass" class="form-control" placeholder="Tối thiểu 6 ký tự" minlength="6">
        </div>
        <div class="modal-actions" style="margin-top:20px;">
            <button class="btn-modal-secondary" onclick="document.getElementById('resetPassModal').classList.remove('active')">Đóng</button>
            <button onclick="saveResetPassword()" style="padding:10px 18px; border-radius:8px; border:none; background:#f59e0b; color:#0f172a; font-weight:800; cursor:pointer;"><i class="fa-solid fa-check"></i> Cập nhật MK</button>
        </div>
    </div>
</div>

<script>
function openResetPassword(thoId, thoName) {
    document.getElementById('resetPass_thoId').value = thoId;
    document.getElementById('resetPass_thoName').textContent = thoName;
    document.getElementById('resetPass_newPass').value = '';
    document.getElementById('resetPassModal').classList.add('active');
}

function saveResetPassword() {
    var thoId = document.getElementById('resetPass_thoId').value;
    var pass = document.getElementById('resetPass_newPass').value.trim();
    if (!pass || pass.length < 6) {
        return Swal.fire('Cảnh báo', 'Vui lòng nhập mật khẩu tối thiểu 6 ký tự', 'warning');
    }
    fetch('/controller/admin/ResetThoPassword.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + thoId + '&new_password=' + encodeURIComponent(pass)
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            document.getElementById('resetPassModal').classList.remove('active');
            Swal.fire('Thành công', res.msg, 'success');
        } else {
            Swal.fire('Lỗi', res.msg, 'error');
        }
    })
    .catch(() => Swal.fire('Lỗi', 'Không thể kết nối máy chủ', 'error'));
}

function toggleThoStatus(id) {
    fetch('/controller/admin/ToggleThoStatus.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            Swal.fire('Thành công', res.msg, 'success').then(() => location.reload());
        } else {
            Swal.fire('Lỗi', res.msg, 'error');
        }
    })
    .catch(() => Swal.fire('Lỗi', 'Không thể kết nối', 'error'));
}

function xemLichSu(thoId) {
    document.getElementById('lichsu_stats').innerHTML = '<p style="color:#64748b;">Dang tải...</p>';
    document.getElementById('lichsu_table').innerHTML = '';
    document.getElementById('lichSuModal').classList.add('active');

    fetch('/controller/admin/ThoHistory.php?tho_id=' + thoId)
    .then(r => r.json())
    .then(res => {
        if (res.status !== 'success') {
            document.getElementById('lichsu_stats').innerHTML = '<p style="color:#dc2626">Lỗi: ' + res.msg + '</p>';
            return;
        }
        var t = res.tho;
        var s = res.stats;
        document.getElementById('lichsu_title').innerHTML =
            '<i class="fa-solid fa-clock-rotate-left"></i> ' + (t.name || t.username) + ' — Lịch sử';

        // Thống kê
        var fmtMoney = n => new Intl.NumberFormat('vi-VN').format(n);
        var stats = [
            { label: 'Tổng đơn', val: s.total, color: '#6366f1' },
            { label: 'Hoàn thành', val: s.hoan_thanh, color: '#16a34a' },
            { label: 'Đang xử lý', val: s.dang_xu_ly, color: '#f59e0b' },
            { label: 'Phát sinh', val: fmtMoney(s.tong_phatsinh) + 'đ', color: '#9333ea' },
            { label: 'Điểm TB', val: s.diem_tb ? s.diem_tb + '★' : 'Chưa có', color: '#0ea5e9' },
        ];
        var statsHtml = stats.map(x =>
            '<div style="background:#f8fafc; border-radius:10px; padding:14px; text-align:center; border:1px solid #e2e8f0;">' +
            '<div style="font-size:22px; font-weight:900; color:' + x.color + ';">' + x.val + '</div>' +
            '<div style="font-size:12px; color:#64748b; margin-top:4px;">' + x.label + '</div></div>'
        ).join('');
        // Trạng thái + nợ phí
        var feeColor = t.money < 0 ? '#dc2626' : '#16a34a';
        statsHtml += '<div style="background:#f8fafc; border-radius:10px; padding:14px; text-align:center; border:1px solid ' + feeColor + '20;">' +
            '<div style="font-size:18px; font-weight:900; color:' + feeColor + ';">' + fmtMoney(t.money) + 'đ</div>' +
            '<div style="font-size:12px; color:#64748b; margin-top:4px;">Tiền nợ/Đặt cọc</div></div>';
        document.getElementById('lichsu_stats').innerHTML = statsHtml;

        // Bảng đơn hàng
        if (!res.orders || res.orders.length === 0) {
            document.getElementById('lichsu_table').innerHTML = '<p style="color:#64748b; text-align:center; padding:20px;">Chưa có đơn nào.</p>';
            return;
        }
        var statusMap = {
            'HOAN_THANH': { text: 'Hoàn thành', color: '#16a34a' },
            'DANG_XU_LY': { text: 'Đang xử lý', color: '#f59e0b' },
            'CHO_XU_LY':  { text: 'Chờ', color: '#6366f1' },
            'DA_HUY':     { text: 'Đã hủy', color: '#dc2626' }
        };
        var rows = res.orders.map(o => {
            var st = statusMap[o.trangthai] || { text: o.trangthai, color: '#64748b' };
            var date = o.thoigian ? new Date(o.thoigian * 1000).toLocaleString('vi-VN') : '-';
            var stars = o.danhgia_sao ? '★'.repeat(o.danhgia_sao) : '-';
            return '<tr><td>#' + o.id + '</td><td>' + (o.dichvu || '-') + '</td><td>' + (o.ten || '-') + '</td>' +
                   '<td>' + date + '</td>' +
                   '<td><span style="color:' + st.color + '; font-weight:800; font-size:12px;">' + st.text + '</span></td>' +
                   '<td>' + stars + '</td></tr>';
        }).join('');
        document.getElementById('lichsu_table').innerHTML =
            '<div style="overflow-x:auto;"><table class="table">' +
            '<thead><tr><th>ID</th><th>DV</th><th>Khách</th><th>Ngày</th><th>TT</th><th>ĐG</th></tr></thead>' +
            '<tbody>' + rows + '</tbody></table></div>';
    })
    .catch(() => {
        document.getElementById('lichsu_stats').innerHTML = '<p style="color:#dc2626">Lỗi kết nối</p>';
    });
}
</script>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
