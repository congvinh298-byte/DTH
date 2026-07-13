# CHANGELOG - Rà soát & Hoàn thiện Hệ thống DTH
**Ngày:** 13/07/2026  
**Người thực hiện:** Thiên

---

## ✅ ĐÃ XONG

### 1. Zalo Mini App
- Chuẩn hóa thư mục `/zalo/` làm bản Mini App chính thức.
- Sửa `index.html` thêm loader, sandbox, postMessage, offline check.
- Tạo `manifest.json` chuẩn PWA.
- Sửa `app-config.json` encoding UTF-8 no BOM (JSON valid).
- Cập nhật `zalo.html` ở root đồng bộ với `/zalo/index.html`.
- **Xây dựng `/zalo/` đủ mạnh** để VS Code extension nhận diện:
  - `package.json`
  - `vite.config.js`
  - `src/App.jsx`, `src/main.jsx`, `src/pages/index.jsx`, `src/index.html`
  - `.vscode/settings.json`
  - `.cursor/rules/dmctn-taste-gate.mdc`
- Đánh dấu `zalo_mini_app/` là cần xóa (chưa xóa được do bị process khóa).
- Tạo `ZALO_MINIAPP_DEPLOY.md` hướng dẫn upload đúng.

### 2. Khách hàng
- Tạo `/tra-cuu-don.php`: tra cứu đơn dịch vụ bằng SĐT.
- Thêm chức năng hủy đơn (nếu còn CHO_XU_LY).
- Thêm link "Tra cứu đơn" vào Header.

### 3. Admin / Chủ cửa hàng
- Tạo `/pages/admin/QuanLyDatLich.php`: dashboard quản lý toàn bộ đơn đặt lịch.
- Thêm filter theo trạng thái.
- Thêm chức năng giao đơn cho thợ.
- Thêm chức năng hủy đơn từ admin.

### 4. Thợ lao động
- Cập nhật `/tho-dashboard.php` thêm 2 modal:
  - Báo giá phát sinh (mô tả + giá).
  - Nghiệm thu công việc (link ảnh + ghi chú).
- Tạo controller `/controller/client/BaoGiaPhatSinh.php`.
- Tạo controller `/controller/client/NghiemThu.php`.

### 5. Cơ sở dữ liệu
- Cập nhật `/setup_tho.php` và `/schema.php` thêm các cột mới:
  - `phatsinh_mota`, `phatsinh_gia`, `phatsinh_duyet`
  - `nghiemthu_note`, `nghiemthu_anh`
  - `danhgia_sao`, `danhgia_noidung`

### 6. Bảo mật
- Sửa `/core/config.php`: thêm hàm `prepared_get_row()`.
- Dùng prepared statement cho xác thực `tokenlog`.
- Tránh lỗi PHP notice khi `$getUser` null.
- Tạo `SECURITY_ALERT.md` cảnh báo `.env` lộ credentials.
- Tạo `.env.example` để dùng làm mẫu triển khai.

### 7. Tài liệu
- `AUDIT_REPORT_2026-07-13.md`: báo cáo rà soát toàn diện.
- `CHANGELOG_2026-07-13.md`: file này.
- `ZALO_MINIAPP_DEPLOY.md`: hướng dẫn triển khai Mini App.
- `SECURITY_ALERT.md`: cảnh báo bảo mật.

---

## ⚠️ CẦN ANH VINH LÀM TIẾP

1. **Bảo mật .env:** Di chuyển `.env` ra ngoài web root, đổi toàn bộ credentials.
2. **Chạy `/setup_tho.php` trên server** để cập nhật cấu trúc bảng `dat_lich`.
3. **Upload Mini App đúng:** Chỉ upload thư mục `/zalo/` dưới dạng ZIP.
4. **Kiểm tra các trang mới trên server:**
   - `/tra-cuu-don.php`
   - `/pages/admin/QuanLyDatLich.php`
   - `/tho-dashboard.php`
5. **Xóa `zalo_mini_app/` sau khi Mini App mới chạy ổn định** (hiện tại đang bị khóa bởi process khác, cần restart máy rồi xóa).

---

## 📊 KẾT QUẢ KIỂM TRA
- ✅ 10/10 file PHP pass syntax check.
- ✅ `zalo/app-config.json` JSON valid.
- ⚠️ `.env` vẫn còn trong workspace, cần xử lý bảo mật.
