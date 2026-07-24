<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");

$title = "Tra cứu đơn dịch vụ | Điện Máy Hiếu";
require_once(__DIR__."/pages/client/Head.php");
require_once(__DIR__."/pages/client/Header.php");

$search = isset($_GET['sdt']) ? preg_replace('/[^0-9]/', '', $_GET['sdt']) : '';
$orders = [];
$error = '';

if ($search && strlen($search) >= 9) {
    $DMH->connect();
    $safe_sdt = mysqli_real_escape_string($DMH->ketnoi, $search);
    $orders = $DMH->get_list("SELECT * FROM `dat_lich` WHERE `sdt` = '$safe_sdt' ORDER BY `thoigian` DESC LIMIT 50");
    if (empty($orders)) {
        $error = 'Không tìm thấy đơn nào với số điện thoại này.';
    }
} elseif ($search) {
    $error = 'Số điện thoại không hợp lệ.';
}

$status_map = [
    'CHO_XU_LY' => ['text' => 'Chờ xử lý', 'color' => '#f59e0b'],
    'DANG_XU_LY' => ['text' => 'Đang xử lý', 'color' => '#38bdf8'],
    'HOAN_THANH' => ['text' => 'Hoàn thành', 'color' => '#10b981'],
    'DA_HUY' => ['text' => 'Đã hủy', 'color' => '#ef4444'],
];
?>

<style>
  .tc-container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
  .tc-card { background: rgba(30,41,59,0.8); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 30px; }
  .tc-title { font-size: 28px; font-weight: 900; color: #f8fafc; margin-bottom: 10px; }
  .tc-sub { color: #94a3b8; margin-bottom: 24px; }
  .tc-form { display: flex; gap: 12px; margin-bottom: 30px; }
  .tc-form input { flex: 1; background: rgba(15,23,42,0.8); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 14px 18px; border-radius: 12px; font-size: 16px; }
  .tc-form button { background: #38bdf8; color: #0f172a; border: none; padding: 14px 28px; border-radius: 12px; font-weight: 800; cursor: pointer; }
  .order-item { background: rgba(15,23,42,0.6); border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; padding: 20px; margin-bottom: 16px; }
  .order-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; flex-wrap: wrap; gap: 10px; }
  .order-id { font-weight: 800; color: #f8fafc; font-size: 18px; }
  .order-status { padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 800; }
  .order-body p { margin: 8px 0; color: #cbd5e1; }
  .order-body strong { color: #94a3b8; display: inline-block; min-width: 110px; }
  .empty-msg { text-align: center; color: #94a3b8; padding: 40px 20px; }
  .cancel-btn { margin-top: 12px; background: rgba(239,68,68,0.15); color: #fca5a5; border: 1px solid rgba(239,68,68,0.3); padding: 10px 18px; border-radius: 10px; cursor: pointer; font-weight: 700; }
  /* Star rating */
  .star-rating { display: flex; gap: 6px; flex-direction: row-reverse; justify-content: flex-end; margin: 8px 0; }
  .star-rating input { display: none; }
  .star-rating label { font-size: 30px; color: #334155; cursor: pointer; transition: color 0.15s; line-height: 1; }
  .star-rating input:checked ~ label,
  .star-rating label:hover,
  .star-rating label:hover ~ label { color: #f59e0b; }
  .review-box { margin-top: 16px; background: rgba(16,185,129,0.05); border: 1px solid rgba(16,185,129,0.2); border-radius: 14px; padding: 16px; }
  .review-box h4 { margin: 0 0 10px; color: #10b981; font-size: 15px; font-weight: 800; }
  .done-review { margin-top: 12px; background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2); border-radius: 10px; padding: 12px; display: flex; align-items: center; gap: 8px; }
  @media (max-width: 600px) { .tc-form { flex-direction: column; } }
</style>

<div class="wrap tc-container">
  <div class="tc-card fade-in">
    <div class="tc-title">🔍 Tra cứu đơn dịch vụ</div>
    <div class="tc-sub">Nhập số điện thoại đã đặt lịch để xem trạng thái đơn hàng.</div>

    <form class="tc-form" method="get" action="/tra-cuu-don.php">
      <input type="tel" name="sdt" value="<?= htmlspecialchars($search) ?>" placeholder="Nhập số điện thoại..." required maxlength="15" inputmode="numeric">
      <button type="submit">Tra cứu</button>
    </form>

    <?php if ($error): ?>
      <div class="empty-msg"><?= htmlspecialchars($error) ?></div>
    <?php elseif (!empty($orders)): ?>
      <div style="margin-bottom: 16px; color: #94a3b8;">Tìm thấy <strong style="color: #38bdf8;"><?= count($orders) ?></strong> đơn.</div>
      <?php foreach ($orders as $don): 
        $st = $status_map[$don['trangthai']] ?? ['text' => $don['trangthai'], 'color' => '#94a3b8'];
      ?>
        <div class="order-item">
          <div class="order-head">
            <div class="order-id">Đơn #<?= (int)$don['id'] ?></div>
            <div class="order-status" style="background: <?= $st['color'] ?>20; color: <?= $st['color'] ?>; border: 1px solid <?= $st['color'] ?>40;">
              <?= htmlspecialchars($st['text']) ?>
            </div>
          </div>
          <div class="order-body">
            <p><strong>Dịch vụ:</strong> <?= htmlspecialchars($don['dichvu']) ?></p>
            <p><strong>Khách hàng:</strong> <?= htmlspecialchars($don['ten']) ?></p>
            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($don['diachi']) ?></p>
            <p><strong>Mô tả:</strong> <?= nl2br(htmlspecialchars($don['yeucau'])) ?></p>
            <p><strong>Thời gian đặt:</strong> <?= date('H:i d/m/Y', $don['thoigian']) ?></p>
            <?php if ($don['trangthai'] == 'CHO_XU_LY'): ?>
              <button class="cancel-btn" onclick="huyDon(<?= (int)$don['id'] ?>)">❌ Hủy đơn này</button>
            <?php endif; ?>
            <?php if ($don['trangthai'] == 'HOAN_THANH'): ?>
              <?php if (!empty($don['danhgia_sao'])): ?>
                <div class="done-review">
                  <span style="font-size:20px;"><?= str_repeat('&#9733;', (int)$don['danhgia_sao']) ?><?= str_repeat('&#9734;', 5 - (int)$don['danhgia_sao']) ?></span>
                  <span style="color:#94a3b8; font-size:13px;">Đã đánh giá <?= (int)$don['danhgia_sao'] ?>/5 sao<?= $don['danhgia_noidung'] ? ' — &ldquo;' . htmlspecialchars($don['danhgia_noidung']) . '&rdquo;' : '' ?></span>
                </div>
              <?php else: ?>
                <div class="review-box" id="review-box-<?= (int)$don['id'] ?>">
                  <h4>❤️ Đánh giá dịch vụ</h4>
                  <p style="color:#94a3b8; font-size:13px; margin:0 0 10px;">Hài lòng với dịch vụ không? Rất mong bạn cho biết ý kiến!</p>
                  <div class="star-rating" id="stars-<?= (int)$don['id'] ?>">
                    <?php for ($s = 5; $s >= 1; $s--): ?>
                      <input type="radio" id="star<?= $s ?>-<?= (int)$don['id'] ?>" name="sao_<?= (int)$don['id'] ?>" value="<?= $s ?>">
                      <label for="star<?= $s ?>-<?= (int)$don['id'] ?>">&#9733;</label>
                    <?php endfor; ?>
                  </div>
                  <textarea id="review-note-<?= (int)$don['id'] ?>" placeholder="Nhận xét (tùy chọn)..." maxlength="500" style="width:100%; background:#0f172a; border:1px solid rgba(255,255,255,0.1); color:#fff; padding:10px; border-radius:8px; margin:10px 0; resize:none; font-size:13px;"></textarea>
                  <button onclick="guiDanhGia(<?= (int)$don['id'] ?>)" style="background:#10b981; color:#fff; border:none; padding:10px 20px; border-radius:10px; font-weight:800; cursor:pointer; font-size:14px;"><i class="fa-solid fa-paper-plane"></i> Gửi đánh giá</button>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function huyDon(id) {
  Swal.fire({
    title: 'Hủy đơn?',
    text: 'Bạn có chắc muốn hủy đơn dịch vụ này?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Hủy đơn',
    cancelButtonText: 'Giữ lại',
    background: '#1e293b',
    color: '#fff'
  }).then((result) => {
    if (result.isConfirmed) {
      fetch('/controller/client/HuyDonDatLich.php', {
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
      .catch(() => Swal.fire('Lỗi', 'Không thể kết nối máy chủ', 'error'));
    }
  });
}

function guiDanhGia(donId) {
  var checked = document.querySelector('input[name="sao_' + donId + '"]:checked');
  if (!checked) {
    return Swal.fire('Chưa chọn sao', 'Vui lòng chọn số sao đánh giá (1–5)', 'warning');
  }
  var sao = checked.value;
  var note = document.getElementById('review-note-' + donId).value.trim();

  fetch('/controller/client/DanhGiaDon.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id=' + donId + '&danhgia_sao=' + sao + '&danhgia_noidung=' + encodeURIComponent(note)
  })
  .then(r => r.json())
  .then(res => {
    if (res.status === 'success') {
      Swal.fire('❤️ Cảm ơn bạn!', res.msg, 'success').then(() => location.reload());
    } else {
      Swal.fire('Lỗi', res.msg, 'error');
    }
  })
  .catch(() => Swal.fire('Lỗi', 'Không thể kết nối máy chủ', 'error'));
}
</script>

<?php require_once(__DIR__."/pages/client/Footer.php"); ?>
