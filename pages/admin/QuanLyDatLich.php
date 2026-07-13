<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

if (!isset($_COOKIE['token']) || empty($getUser)) {
    header("Location: /pages/admin/LoginAdmin.php");
    exit;
}
if ($getUser['level'] != 'admin') {
    header("Location: /");
    exit;
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$where = "1=1";
if (in_array($filter, ['CHO_XU_LY', 'DANG_XU_LY', 'HOAN_THANH', 'DA_HUY'])) {
    $where = "`trangthai` = '" . mysqli_real_escape_string($DMH->ketnoi ?? null, $filter) . "'";
}

$orders = $DMH->get_list("SELECT d.*, u.name AS tho_name, u.username AS tho_username 
    FROM `dat_lich` d 
    LEFT JOIN `users` u ON u.id = d.tho_id 
    WHERE $where 
    ORDER BY d.thoigian DESC LIMIT 200");

$status_map = [
    'CHO_XU_LY' => ['text' => 'Chờ xử lý', 'color' => '#f59e0b'],
    'DANG_XU_LY' => ['text' => 'Đang xử lý', 'color' => '#38bdf8'],
    'HOAN_THANH' => ['text' => 'Hoàn thành', 'color' => '#10b981'],
    'DA_HUY' => ['text' => 'Đã hủy', 'color' => '#ef4444'],
];

$title = "Quản lý đặt lịch dịch vụ";
require_once(__DIR__."/Head.php");
require_once(__DIR__."/Header.php");
?>

<style>
  .qldl-wrap { padding: 24px; max-width: 1400px; margin: 0 auto; }
  .qldl-title { font-size: 26px; font-weight: 900; margin-bottom: 20px; color: #fff; }
  .filter-bar { display: flex; gap: 10px; margin-bottom: 24px; flex-wrap: wrap; }
  .filter-bar a { padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 700; color: #94a3b8; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); }
  .filter-bar a.active { background: #38bdf8; color: #0f172a; border-color: #38bdf8; }
  .qldl-table { width: 100%; border-collapse: collapse; background: rgba(15,23,42,0.5); border-radius: 12px; overflow: hidden; }
  .qldl-table th, .qldl-table td { padding: 14px 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06); color: #cbd5e1; font-size: 14px; }
  .qldl-table th { background: rgba(56,189,248,0.1); color: #38bdf8; font-weight: 800; }
  .qldl-table tr:hover { background: rgba(255,255,255,0.03); }
  .badge-stt { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 800; }
  .action-btn { padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-weight: 700; font-size: 12px; }
  .btn-reassign { background: #6366f1; color: #fff; }
  .btn-cancel { background: #ef4444; color: #fff; }
  .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; }
  .modal-backdrop.active { display: flex; }
  .modal-box { background: #1e293b; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 24px; width: 90%; max-width: 420px; color: #fff; }
  .modal-box select { width: 100%; padding: 12px; margin: 16px 0; background: #0f172a; border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px; }
  .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
  @media (max-width: 900px) { .qldl-table { font-size: 12px; } .qldl-table th, .qldl-table td { padding: 10px 8px; } }
</style>

<div class="qldl-wrap">
  <div class="qldl-title">📋 Quản lý đặt lịch dịch vụ</div>

  <div class="filter-bar">
    <a href="?filter=all" class="<?= $filter == 'all' ? 'active' : '' ?>">Tất cả</a>
    <a href="?filter=CHO_XU_LY" class="<?= $filter == 'CHO_XU_LY' ? 'active' : '' ?>">Chờ xử lý</a>
    <a href="?filter=DANG_XU_LY" class="<?= $filter == 'DANG_XU_LY' ? 'active' : '' ?>">Đang xử lý</a>
    <a href="?filter=HOAN_THANH" class="<?= $filter == 'HOAN_THANH' ? 'active' : '' ?>">Hoàn thành</a>
    <a href="?filter=DA_HUY" class="<?= $filter == 'DA_HUY' ? 'active' : '' ?>">Đã hủy</a>
  </div>

  <?php if (empty($orders)): ?>
    <div style="text-align:center; color:#94a3b8; padding: 40px;">Không có đơn nào.</div>
  <?php else: ?>
    <table class="qldl-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Khách hàng</th>
          <th>SĐT</th>
          <th>Dịch vụ</th>
          <th>Địa chỉ</th>
          <th>Thời gian</th>
          <th>Thợ</th>
          <th>Trạng thái</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $don):
          $st = $status_map[$don['trangthai']] ?? ['text' => $don['trangthai'], 'color' => '#94a3b8'];
        ?>
          <tr>
            <td>#<?= (int)$don['id'] ?></td>
            <td><?= htmlspecialchars($don['ten']) ?></td>
            <td><?= htmlspecialchars($don['sdt']) ?></td>
            <td><?= htmlspecialchars($don['dichvu']) ?></td>
            <td><?= htmlspecialchars(mb_substr($don['diachi'], 0, 60)) ?><?= mb_strlen($don['diachi']) > 60 ? '...' : '' ?></td>
            <td><?= date('H:i d/m/Y', $don['thoigian']) ?></td>
            <td><?= $don['tho_name'] ? htmlspecialchars($don['tho_name']) : '—' ?></td>
            <td>
              <span class="badge-stt" style="background: <?= $st['color'] ?>20; color: <?= $st['color'] ?>; border: 1px solid <?= $st['color'] ?>40;">
                <?= htmlspecialchars($st['text']) ?>
              </span>
            </td>
            <td>
              <?php if ($don['trangthai'] == 'CHO_XU_LY' || $don['trangthai'] == 'DANG_XU_LY'): ?>
                <button class="action-btn btn-reassign" onclick="openAssign(<?= (int)$don['id'] ?>)">Giao thợ</button>
                <button class="action-btn btn-cancel" onclick="adminHuy(<?= (int)$don['id'] ?>)">Hủy</button>
              <?php else: ?>—<?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<!-- Modal giao thợ -->
<div class="modal-backdrop" id="assignModal">
  <div class="modal-box">
    <div style="font-weight:800; font-size:18px; margin-bottom:12px;">Giao đơn cho thợ</div>
    <input type="hidden" id="assignOrderId">
    <select id="assignThoId">
      <option value="">-- Chọn thợ --</option>
      <?php
        $tho_list = $DMH->get_list("SELECT id, name, username FROM `users` WHERE `level` = 'tho' AND `banned` = 'ON' ORDER BY name");
        foreach ($tho_list as $tho) {
          echo '<option value="' . (int)$tho['id'] . '"\u003e' . htmlspecialchars(($tho['name'] ?: $tho['username'])) . '</option\u003e';
        }
      ?>
    </select>
    <div class="modal-actions">
      <button onclick="closeAssign()" style="padding: 8px 16px; border-radius:8px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#fff; cursor:pointer;">Đóng</button>
      <button onclick="saveAssign()" style="padding: 8px 16px; border-radius:8px; border:none; background:#38bdf8; color:#0f172a; font-weight:800; cursor:pointer;">Lưu</button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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

  fetch('/controller/admin/GiaoDonTho.php', {
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
function adminHuy(id) {
  Swal.fire({
    title: 'Hủy đơn?',
    text: 'Đơn sẽ bị đánh dấu hủy.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Hủy đơn',
    cancelButtonText: 'Đóng',
    background: '#1e293b',
    color: '#fff'
  }).then((result) => {
    if (result.isConfirmed) {
      fetch('/controller/admin/HuyDonAdmin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
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

<?php require_once(__DIR__."/Footer.php"); ?>
