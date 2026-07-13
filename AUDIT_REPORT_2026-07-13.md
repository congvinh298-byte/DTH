# BÁO CÁO RÀ SOÁT TOÀN DIỆN HỆ THỐNG ĐIỆN MÁY HIẾU
**Ngày:** 13/07/2026  
**Người thực hiện:** Thiên (AI Assistant)  
**Dự án:** C:\Users\pcpv\OneDrive\Desktop\DTH

---

## 1. TỔNG QUAN CẤU TRÚC

Hệ thống hiện tại gồm 3 thành phần chính:

| Thành phần | Vị trí | Mô tả |
|------------|--------|-------|
| **Website PHP chính** | `/` (root DTH) | Giao diện khách hàng, admin, partner, thợ |
| **Zalo Mini App (iframe)** | `/zalo/` | Nhúng web qua iframe - đây là phiên bản đúng theo yêu cầu SSOT |
| **Zalo Mini App (React/Vite)** | `/zalo_mini_app/` | React app cũ, đang lộn xộn với nhiều bản build |

---

## 2. LỖI MINI APP ZALO (NGUYÊN NHÂN 404)

### Vấn đề cốt lõi
Zalo Mini App khi quét QR sẽ tìm file `index.html` theo cấu hình `app-config.json`. Hiện tại có **2 bộ cấu hình**:

1. **`/zalo/app-config.json`** → list pages = `["index.html"]` → file `/zalo/index.html` nhúng iframe đúng
2. **`/zalo_mini_app/app-config.json`** → list pages = `["index.html"]` → file `/zalo_mini_app/src/index.html` là template React chưa build (KHÔNG PHẢI bản nhúng web)

### Nguyên nhân 404
- Khi upload Mini App lên Zalo, nếu upload nhầm thư mục `zalo_mini_app/` thì Zalo tìm `index.html` nhưng không tìm thấy nội dung hợp lệ (hoặc file index chưa build).
- File `/zalo.html` ở root và `/zalo/index.html` đều đã nhúng đúng web (`https://dienmayhieu.com/`) nhưng **chưa được khai báo làm entry point chính**.
- Thư mục `zalo_mini_app/` chứa quá nhiều bản build trùng lặp: `dist/`, `src/dist/`, `src/www/`, `www/`.

### Giải pháp
- Chuẩn hóa Mini App về thư mục `/zalo/` với `index.html` + `app-config.json`.
- Xóa hoặc di dời `zalo_mini_app/` để tránh nhầm lẫn.
- Đảm bảo `app-config.json` trỏ đúng đến `index.html`.

---

## 3. CÁC CHỨC NĂNG CÒN THIẾU

### A. Dành cho Khách hàng
| Chức năng | Tình trạng | Mức độ cần thiết |
|-----------|-----------|------------------|
| Xem sản phẩm & đặt mua | ✅ Có | Cao |
| Đặt lịch gọi thợ | ✅ Có | Cao |
| **Theo dõi trạng thái đơn dịch vụ** | ❌ Thiếu | Cao |
| **Đánh giá thợ sau dịch vụ** | ❌ Thiếu | Trung bình |
| **Lịch sử sửa chữa của tôi** | ❌ Thiếu | Cao |
| **Hủy/sửa đơn đã đặt** | ❌ Thiếu | Trung bình |
| Chatbot AI hỗ trợ | ✅ Có | Trung bình |

### B. Dành cho Chủ cửa hàng (Admin)
| Chức năng | Tình trạng | Mức độ cần thiết |
|-----------|-----------|------------------|
| Quản lý sản phẩm | ✅ Có | Cao |
| Quản lý thành viên | ✅ Có | Cao |
| Quản lý API/Partner | ✅ Có | Trung bình |
| **Quản lý đơn đặt lịch dịch vụ** | ❌ Thiếu | Cao |
| **Điều phối thợ, giám sát tiến độ** | ❌ Thiếu | Cao |
| **Báo cáo doanh thu dịch vụ** | ❌ Thiếu | Trung bình |

### C. Dành cho Thợ lao động
| Chức năng | Tình trạng | Mức độ cần thiết |
|-----------|-----------|------------------|
| Đăng nhập portal | ✅ Có | Cao |
| Nhận đơn | ✅ Có | Cao |
| Hoàn thành đơn | ✅ Có | Cao |
| **Báo giá phát sinh vật tư** | ❌ Thiếu | Cao |
| **Cập nhật trạng thái tiến độ** | ❌ Thiếu | Trung bình |
| **Upload ảnh nghiệm thu** | ❌ Thiếu | Cao |
| **Lịch sử thu nhập, đối soát** | ❌ Thiếu | Trung bình |

---

## 4. LỖ HỔNG BẢO MẬT NGHIÊM TRỌNG

### 4.1. Lộ credentials trong source code (CRITICAL)
File `.env` ở root chứa:
- Database password plaintext
- SePay API key
- ZMP Token JWT
- Admin password hash
- BCT credentials

**Rủi ro:** Nếu đẩy lên Git hoặc server bị xâm nhập, toàn bộ hệ thống sẽ bị chiếm đoạt.

### 4.2. SQL Injection (CRITICAL)
Nhiều file dùng nối chuỗi SQL trực tiếp với input user:
- `core/config.php`: `$DMH->get_row("SELECT * FROM users WHERE tokenlog = '".$_COOKIE['token']."'")`
- `controller/client/NhanDon.php`: `$id = (int)$_POST['id']` - đã ép kiểu, OK
- `controller/client/HoanThanhDon.php`: `$id = (int)$_POST['id']` - đã ép kiểu, OK
- `controller/client/DatLich.php`: `check_string()` không đủ mạnh, cần prepared statement
- `pages/admin/*`: Nhiều trang admin cũng nối chuỗi SQL.

### 4.3. XSS (HIGH)
Nhiều output từ database không được `htmlspecialchars()` đúng cách.

### 4.4. Cookie bảo mật kém
- Cookie `token` không có `HttpOnly`, `Secure`, `SameSite`.
- Sử dụng `tokenlog` từ DB trực tiếp làm session cookie.

### 4.5. CSRF (MEDIUM)
Không có CSRF token trong các form và AJAX request.

---

## 5. ĐÁNH GIÁ THEO CÔNG THỨC BIG TECH

| Tiêu chí | Điểm | Nhận xét |
|----------|------|----------|
| **Single Source of Truth (SSOT)** | 6/10 | Đã có ý tưởng nhúng iframe nhưng cấu trúc lộn xộn |
| **Separation of Concerns** | 5/10 | Code PHP + HTML + JS lẫn lộn trong cùng file |
| **Security (OWASP Top 10)** | 3/10 | Thiếu prepared statements, CSRF, XSS protection |
| **Scalability** | 4/10 | Không có queue, cache layer, async processing |
| **Observability** | 3/10 | Không có logging tập trung, monitoring |
| **Mobile First / PWA** | 6/10 | Giao diện responsive nhưng chưa phải PWA chuẩn |
| **DevOps / CI-CD** | 2/10 | Không có test, build script tự động |

---

## 6. KẾ HOẠCH HÀNH ĐỘNG

1. **Sửa Mini App:** Chuẩn hóa về `/zalo/`, xóa bớt `zalo_mini_app/`.
2. **Tạo trang theo dõi đơn cho khách:** `/tra-cuu-don.php`.
3. **Tạo admin dashboard booking:** `/pages/admin/QuanLyDatLich.php`.
4. **Bổ sung chức năng thợ:** Báo giá phát sinh, upload ảnh nghiệm thu.
5. **Sửa SQL injection:** Dùng prepared statements cho các controller cốt lõi.
6. **Bảo mật .env:** Hướng dẫn di chuyển ra ngoài web root.
7. **Tạo checklist triển khai Zalo Mini App.**
