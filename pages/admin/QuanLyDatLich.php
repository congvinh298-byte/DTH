<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();

$tieude = 'Quản lý đơn gọi thợ | Điện Máy Hiếu';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$where = "1=1";
if (in_array($filter, ['CHO_XU_LY', 'DANG_XU_LY', 'HOAN_THANH', 'DA_HUY'])) {
    $DMH->connect();
    $where = "`trangthai` = '" . mysqli_real_escape_string($DMH->ketnoi, $filter) . "'";
}

$orders = $DMH->get_list("SELECT d.*, u.name AS tho_name, u.username AS tho_username, u.fullname AS tho_fullname
    FROM `dat_lich` d
    LEFT JOIN `users` u ON u.id = d.tho_id
    WHERE $where
    ORDER BY d.thoigian DESC LIMIT 200");

$status_map = [
    'CHO_XU_LY' => ['text' => 'Chờ xử lý', 'class' => 'badge-pending'],
    'DANG_XU_LY' => ['text' => 'Đang xử lý', 'class' => 'badge-shipping'],
    'HOAN_THANH' => ['text' => 'Hoàn thành', 'class' => 'badge-completed'],
    'DA_HUY' => ['text' => 'Đã hủy', 'class' => 'badge-cancelled'],
];

$thoList = $DMH->get_list("SELECT id, name, username, fullname FROM `users` WHERE `level` = 'tho' AND `banned` = 'ON' ORDER BY name, username");
?>

<style>
    .badge-stt { padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 800; }
    .action-btn { padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-weight: 700; font-size: 12px; color:#fff; margin-right:4px; }
    .btn-assign { background: #6366f1; }
    .btn-cancel { background: #dc2626; }
    .btn-complete { background: #16a34a; }
    .btn-duyet { background: #f59e0b; color:#0f172a; }
    .table-wrap { overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 12px; }
    .badge-phatsinh { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:800; }
    .badge-cho-duyet { background:#fef3c7; color:#92400e; border:1px solid #f59e0b; }
    .badge-da-duyet  { background:#d1fae5; color:#065f46; border:1px solid #10b981; }
</style>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;"><i class="fa-solid fa-calendar-check" style="color:#0ea5e9;"></i> Quản lý đơn gọi thợ</h2>

<div class="filter-bar">
    <a href="?filter=all" class="<?= $filter == 'all' ? 'active' : '' ?>">Tất cả</a>
    <a href="?filter=CHO_XU_LY" class="<?= $filter == 'CHO_XU_LY' ? 'active' : '' ?>"><span class="badge badge-pending">Chờ xử lý</span></a>
    <a href="?filter=DANG_XU_LY" class="<?= $filter == 'DANG_XU_LY' ? 'active' : '' ?>"><span class="badge badge-shipping">Đang xử lý</span></a>
    <a href="?filter=HOAN_THANH" class="<?= $filter == 'HOAN_THANH' ? 'active' : '' ?>"><span class="badge badge-completed">Hoàn thành</span></a>
    <a href="?filter=DA_HUY" class="<?= $filter == 'DA_HUY' ? 'active' : '' ?>"><span class="badge badge-cancelled">Đã hủy</span></a>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <?php if (empty($orders)): ?>
            <div style="text-align:center; padding: 50px 20px; color:#64748b;">
                <i class="fa-solid fa-calendar-xmark" style="font-size:40px; margin-bottom:14px; display:block;"></i>
                <div style="font-size:16px; font-weight:700;">Không có đơn gọi thợ nào</div>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>SĐT</th>
                            <th>Dịch vụ</th>
                            <th>Địa chỉ</th>
                            <th>Thời gian</th>
                            <th>Thợ</th>
                            <th>Phát sinh</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $don):
                            $st = $status_map[$don['trangthai']] ?? ['text' => $don['trangthai'], 'class' => 'badge-default'];
                            $thoDisplay = $don['tho_name'] ?: $don['tho_fullname'] ?: $don['tho_username'];
                        ?>
                            <tr>
                                <td><strong>#<?= (int)$don['id'] ?></strong></td>
                                <td><?= htmlspecialchars($don['ten'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($don['sdt'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($don['dichvu'] ?? '-') ?></td>
                                <td><?= htmlspecialchars(mb_substr($don['diachi'] ?? '', 0, 50)) ?><?= mb_strlen($don['diachi'] ?? '') > 50 ? '...' : '' ?></td>
                                <td><?= !empty($don['thoigian']) ? date('H:i d/m/Y', (int)$don['thoigian']) : '-' ?></td>
                                <td><?= $thoDisplay ? htmlspecialchars($thoDisplay) : '—' ?></td>
                                <td>
                                    <?php if (!empty($don['phatsinh_gia']) && (int)$don['phatsinh_gia'] > 0): ?>
                                        <?php if ($don['phatsinh_duyet'] == 1): ?>
                                            <span class="badge-phatsinh badge-da-duyet">✔ <?= number_format((int)$don['phatsinh_gia'], 0, ',', '.') ?>đ</span>
                                        <?php else: ?>
                                            <span class="badge-phatsinh badge-cho-duyet">⏳ Chờ duyệt</span>
                                            <br><small style="color:#92400e; font-size:11px;"><?= number_format((int)$don['phatsinh_gia'], 0, ',', '.') ?>đ</small>
                                            <br>
                                            <button class="action-btn btn-duyet" style="margin-top:4px;" onclick="openDuyetBaoGia(<?= (int)$don['id'] ?>, <?= (int)$don['phatsinh_gia'] ?>, '<?= htmlspecialchars(addslashes($don['phatsinh_mota'] ?? ''), ENT_QUOTES) ?>')"><i class="fa-solid fa-check-double"></i> Duyệt</button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color:#94a3b8; font-size:12px">&mdash;</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge-stt <?= $st['class'] ?>"><?= htmlspecialchars($st['text']) ?></span></td>
                                <td>
                                    <?php if ($don['trangthai'] == 'CHO_XU_LY' || $don['trangthai'] == 'DANG_XU_LY'): ?>
                                        <button class="action-btn btn-assign" onclick="openAssign(<?= (int)$don['id'] ?>)"><i class="fa-solid fa-user-check"></i> Giao thợ</button>
                                        <button class="action-btn btn-complete" onclick="serviceComplete(<?= (int)$don['id'] ?>)"><i class="fa-solid fa-check"></i> Hoàn thành</button>
                                        <button class="action-btn btn-cancel" onclick="serviceCancel(<?= (int)$don['id'] ?>)"><i class="fa-solid fa-ban"></i> Hủy</button>
                                    <?php else: ?>&mdash;<?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal giao thợ -->
<div class="modal-backdrop" id="assignModal">
    <div class="modal-box">
        <h3><i class="fa-solid fa-user-check"></i> Giao đơn cho thợ</h3>
        <input type="hidden" id="assignOrderId">
        <label>Chọn thợ</label>
        <select id="assignThoId" class="form-control">
            <option value="">-- Chọn thợ --</option>
            <?php foreach ($thoList as $tho) {
                $label = htmlspecialchars(($tho['name'] ?: $tho['fullname'] ?: $tho['username']));
                echo '<option value="' . (int)$tho['id'] . '">' . $label . '</option>';
            } ?>
        </select>
        <div class="modal-actions">
            <button class="btn-modal-secondary" onclick="closeAssign()">Đóng</button>
            <button class="btn-modal-primary" onclick="saveAssign()">Lưu</button>
        </div>
    </div>
</div>

<!-- Modal duyệt báo giá phát sinh -->
<div class="modal-backdrop" id="duyetBaoGiaModal">
    <div class="modal-box">
        <h3><i class="fa-solid fa-file-invoice-dollar"></i> Duyệt báo giá phát sinh</h3>
        <input type="hidden" id="dbg_id">
        <div style="background:#fef3c7; border:1px solid #f59e0b; border-radius:8px; padding:14px; margin-bottom:14px;">
            <div style="font-size:13px; color:#78350f; margin-bottom:6px;"><strong>Mô tả phát sinh:</strong></div>
            <div id="dbg_mota" style="color:#0f172a; font-size:14px; font-weight:600;"></div>
            <div style="margin-top:10px; font-size:13px; color:#78350f;"><strong>Giá báo:</strong> <span id="dbg_gia" style="font-size:16px; font-weight:900; color:#92400e;"></span></div>
        </div>
        <p style="font-size:13px; color:#64748b; margin-bottom:16px;">Duyệt để xác nhận phát sinh hợp lệ, hoặc Từ chối để yêu cầu thợ gửi lại.</p>
        <div class="modal-actions">
            <button class="btn-modal-secondary" onclick="closeDuyetBaoGia()">Đóng</button>
            <button onclick="submitDuyetBaoGia('tuchoi')" style="padding:10px 18px; border-radius:8px; border:none; background:#dc2626; color:#fff; font-weight:700; cursor:pointer;"><i class="fa-solid fa-times"></i> Từ chối</button>
            <button onclick="submitDuyetBaoGia('duyet')" style="padding:10px 18px; border-radius:8px; border:none; background:#16a34a; color:#fff; font-weight:700; cursor:pointer;"><i class="fa-solid fa-check"></i> Duyệt</button>
        </div>
    </div>
</div>

<script>
function openDuyetBaoGia(id, gia, mota) {
    document.getElementById('dbg_id').value = id;
    document.getElementById('dbg_mota').textContent = mota || 'Không có mô tả';
    document.getElementById('dbg_gia').textContent = new Intl.NumberFormat('vi-VN').format(gia) + 'đ';
    document.getElementById('duyetBaoGiaModal').classList.add('active');
}
function closeDuyetBaoGia() {
    document.getElementById('duyetBaoGiaModal').classList.remove('active');
}
function submitDuyetBaoGia(action) {
    const id = document.getElementById('dbg_id').value;
    const label = action === 'duyet' ? 'Duyệt' : 'Từ chối';
    fetch('/controller/admin/DuyetBaoGia.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&action=' + action
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
    closeDuyetBaoGia();
}
function openAssign(id) {
    document.getElementById('assignOrderId').value = id;
    document.getElementById('assignModal').classList.add('active');
}
function closeAssign() {
    document.getElementById('assignModal').classList.remove('active');
}
function saveAssign() {
    const id = document.getElementById('assignOrderId').value;
    const thoId = document.getElementById('assignThoId').value;
    if (!thoId) return Swal.fire('Thiếu thông tin', 'Vui lòng chọn thợ', 'warning');

    fetch('/controller/admin/AssignTho.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&tho_id=' + thoId
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
    closeAssign();
}
function serviceComplete(id) {
    Swal.fire({
        title: 'Hoàn thành?',
        text: 'Xác nhận đơn dịch vụ đã hoàn tất.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Đồng ý',
        cancelButtonText: 'Đóng'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/controller/admin/ServiceStatusUpdate.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id + '&status=HOAN_THANH'
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
    });
}
function serviceCancel(id) {
    Swal.fire({
        title: 'Hủy đơn?',
        text: 'Đơn sẽ bị đánh dấu là đã hủy.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Hủy đơn',
        cancelButtonText: 'Đóng'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/controller/admin/ServiceStatusUpdate.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id + '&status=DA_HUY'
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire('Đã hủy', res.msg, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Lỗi', res.msg, 'error');
                }
            })
            .catch(() => Swal.fire('Lỗi', 'Không thể kết nối', 'error'));
        }
    });
}
</script>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
