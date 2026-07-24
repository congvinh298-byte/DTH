# TECHNICAL_DOC.md — Điện Máy Hiếu
> Tài liệu kỹ thuật hệ thống quản lý thợ & khách hàng.  
> Cập nhật lần cuối: 2026-07-24

---

## 1. Tổng Quan Kiến Trúc

```
Client Browser
    │
    ▼
PHP Pages (Giao diện)          PHP Controllers (Xử lý logic)
  pages/admin/*.php   ──────►   controller/admin/*.php
  pages/client/*.php  ──────►   controller/client/*.php
  tho-dashboard.php   ──────►   api/tho_check_new.php
    │                                    │
    ▼                                    ▼
core/config.php ──► class DMH ──► MySQL Database
core/function.php ──► send_tele() ──► Telegram Bot API
```

### Stack
| Layer | Công nghệ |
|---|---|
| Backend | PHP 7.4+ (không framework) |
| Database | MySQL/MariaDB — charset utf8mb4 |
| DB Access | Class `DMH` bọc `mysqli` (core/config.php) |
| Frontend | HTML5 + Vanilla CSS + jQuery 3.6 + SweetAlert2 + FontAwesome 6 |
| Maps | Leaflet.js + OpenStreetMap + Nominatim Geocoding |
| Auth | Cookie `token` ↔ `users.tokenlog` (session-based token) |
| Notification | Telegram Bot API (hàm `send_tele()` trong core/function.php) |
| Payment | SePay API (VietQR) |
| Integrations | Zalo Mini App API |

---

## 2. Cấu Trúc Thư Mục

```
/
├── core/
│   ├── config.php          # Class DMH, kết nối DB, RBAC middleware, phân quyền
│   └── function.php        # Hàm tiện ích: send_tele(), msg_success(), check_string()...
│
├── pages/
│   ├── admin/              # Giao diện Admin (yêu cầu CheckAdmin())
│   │   ├── QuanLyDatLich.php   # Quản lý đơn gọi thợ + Duyệt báo giá
│   │   ├── QuanLyTho.php       # Quản lý thợ + Lịch sử + Khóa/Mở
│   │   └── BaoCao.php          # Báo cáo tổng hợp + Hiệu suất thợ
│   └── client/             # Giao diện Khách hàng
│
├── controller/
│   ├── admin/              # Controllers Admin-only
│   │   ├── AssignTho.php       # Giao đơn cho thợ
│   │   ├── DuyetBaoGia.php     # Duyệt/từ chối báo giá phát sinh [MỚI]
│   │   ├── ToggleThoStatus.php # Khóa/Mở tài khoản thợ [MỚI]
│   │   └── ThoHistory.php      # API lịch sử đơn của thợ [MỚI]
│   └── client/             # Controllers Thợ/Khách
│       ├── NhanDon.php         # Thợ nhận đơn
│       ├── BaoGiaPhatSinh.php  # Thợ gửi báo giá phát sinh
│       ├── NghiemThu.php       # Lưu ảnh nghiệm thu (path server)
│       ├── UploadNghiemThu.php # Upload ảnh từ camera [MỚI]
│       ├── HoanThanhDon.php    # Thợ mark hoàn thành (trừ 20k phí)
│       ├── DanhGiaDon.php      # Khách đánh giá dịch vụ [MỚI]
│       └── DatLich.php         # Khách đặt lịch gọi thợ
│
├── api/
│   ├── tho_check_new.php   # Polling API: số đơn CHO_XU_LY [MỚI]
│   ├── dat_lich.php        # API đặt lịch (Zalo Mini App)
│   └── products.php        # API sản phẩm
│
├── tho-login.php           # Cổng đăng nhập thợ (riêng biệt)
├── tho-dashboard.php       # Dashboard thợ (SPA 3 tab) + Polling [NÂNG CẤP]
├── goi-tho.php             # Trang đặt lịch khách hàng (GPS + Leaflet)
├── tra-cuu-don.php         # Tra cứu đơn + Form đánh giá sao [NÂNG CẤP]
└── images/
    └── nghiemthu/          # Ảnh nghiệm thu upload (tổ chức theo YYYY-MM/) [MỚI]
```

---

## 3. RBAC — Phân Quyền

```
┌─────────────────────────────────────────────────────────────┐
│  level = 'admin'                                            │
│  ├─ Đăng nhập: /pages/admin/LoginAdmin.php (session)        │
│  ├─ Truy cập: Toàn bộ admin panel                           │
│  └─ Kiểm tra: CheckAdmin() trong core/config.php            │
├─────────────────────────────────────────────────────────────┤
│  level = 'tho'                                              │
│  ├─ Đăng nhập: /tho-login.php (cookie token)                │
│  ├─ Truy cập: /tho-dashboard.php và các controller/client/* │
│  ├─ Giới hạn: Chỉ thấy đơn của mình (tho_id = $my_id)       │
│  └─ Kiểm tra: $getUser['level'] != 'tho' trong mỗi ctrl     │
├─────────────────────────────────────────────────────────────┤
│  level = 'user' (Khách hàng)                                │
│  ├─ Đăng nhập: /login.php (SĐT + OTP)                       │
│  ├─ Truy cập: /goi-tho.php, /tra-cuu-don.php, đặt lịch      │
│  └─ Giới hạn: Chỉ thấy đơn của SĐT mình                    │
└─────────────────────────────────────────────────────────────┘
```

**Rule quan trọng**: Mọi controller mới PHẢI có RBAC check trước khi xử lý dữ liệu.

---

## 4. Luồng Xử Lý Chính

### Luồng A: Đặt lịch → Hoàn thành
```
Khách → goi-tho.php
  → POST /controller/client/DatLich.php
    → INSERT dat_lich (trangthai='CHO_XU_LY')
    → send_tele() → Telegram Admin + Thợ
  
Thợ → tho-dashboard.php (polling 30s)
  → Nhận alert đơn mới
  → Nhấn "Chốt Nhận" → POST /controller/client/NhanDon.php
    → UPDATE trangthai='DANG_XU_LY', tho_id=$my_id
  
Thợ → Báo giá phát sinh (nếu cần)
  → POST /controller/client/BaoGiaPhatSinh.php
    → UPDATE phatsinh_gia, phatsinh_mota, phatsinh_duyet=0
    → send_tele() → Admin
  
Admin → pages/admin/QuanLyDatLich.php
  → Nhấn "Duyệt" → POST /controller/admin/DuyetBaoGia.php
    → UPDATE phatsinh_duyet=1
  
Thợ → Upload ảnh nghiệm thu
  → POST /controller/client/UploadNghiemThu.php (multipart)
    → Lưu images/nghiemthu/YYYY-MM/nt_*.jpg
  → POST /controller/client/NghiemThu.php
    → UPDATE nghiemthu_anh (path), nghiemthu_note
  
Thợ → Nhấn "Hoàn thành" → POST /controller/client/HoanThanhDon.php
  → UPDATE trangthai='HOAN_THANH'
  → tru(users, money, 20000, tho_id)  ← Trừ phí nền tảng
  → send_tele() → Admin (kèm link đánh giá)

Khách → tra-cuu-don.php?sdt=...
  → Thấy form đánh giá sao (nếu chưa đánh giá)
  → POST /controller/client/DanhGiaDon.php
    → UPDATE danhgia_sao, danhgia_noidung
```

### Luồng B: Admin giao thợ thủ công
```
Admin → pages/admin/QuanLyDatLich.php
  → Nhấn "Giao thợ" → chọn thợ → POST /controller/admin/AssignTho.php
    → UPDATE dat_lich SET tho_id=..., trangthai='DANG_XU_LY'
```

---

## 5. API Endpoints

### Thợ/Khách dùng
| Method | Endpoint | Mô tả | Auth |
|---|---|---|---|
| GET | `/api/tho_check_new.php` | Polling số đơn mới | tho/admin |
| POST | `/controller/client/NhanDon.php` | Nhận đơn | tho/admin |
| POST | `/controller/client/BaoGiaPhatSinh.php` | Báo giá phát sinh | tho/admin |
| POST | `/controller/client/UploadNghiemThu.php` | Upload ảnh nghiệm thu | tho/admin |
| POST | `/controller/client/NghiemThu.php` | Lưu nghiệm thu | tho/admin |
| POST | `/controller/client/HoanThanhDon.php` | Hoàn thành đơn | tho/admin |
| POST | `/controller/client/DatLich.php` | Đặt lịch gọi thợ | user |
| POST | `/controller/client/DanhGiaDon.php` | Đánh giá dịch vụ | public (SĐT-based) |

### Admin dùng
| Method | Endpoint | Mô tả |
|---|---|---|
| POST | `/controller/admin/AssignTho.php` | Giao đơn cho thợ |
| POST | `/controller/admin/DuyetBaoGia.php` | Duyệt/từ chối báo giá |
| POST | `/controller/admin/ToggleThoStatus.php` | Khóa/Mở tài khoản thợ |
| GET | `/controller/admin/ThoHistory.php?tho_id=X` | Lịch sử + thống kê thợ |
| POST | `/controller/admin/ServiceStatusUpdate.php` | Cập nhật trạng thái đơn |

---

## 6. Cơ Chế Phí Nền Tảng

- Khi thợ hoàn thành đơn → `users.money -= 20000`
- Nếu `users.money < 0` → Bị chặn nhận đơn mới (check trong `NhanDon.php`)
- Admin có thể nạp tiền vào tài khoản thợ thông qua `controller/admin/`

---

## 7. Telegram Notification

Hàm `send_tele($text)` trong `core/function.php`:
- Hardcoded token + chat_id trong function (xem `.env.example` để cấu hình qua biến môi trường nếu cần)
- Gửi đến Admin khi: đơn mới, báo giá phát sinh, hoàn thành đơn, đánh giá mới

---

## 8. Technical Debt & Lưu Ý Bảo Mật

> ⚠️ **MD5 Password**: Hệ thống đang dùng `md5()` cho mật khẩu. Cần migrate sang `password_hash()` / `password_verify()` trong tương lai. Ưu tiên thấp vì hệ thống nội bộ, nhưng cần xử lý trước khi scale.

> ⚠️ **Raw SQL**: Nhiều query vẫn dùng `mysqli_real_escape_string()` thay vì prepared statements. Class DMH có `prepared_get_row()` nhưng chưa được áp dụng toàn bộ.

> ℹ️ **Legacy Code**: Các file trong `api/Listcode.php`, `api/Muacode.php`... là code cũ từ hệ thống bán domain, không ảnh hưởng đến chức năng hiện tại nhưng nên dọn dẹp sau.

> ℹ️ **Upload Ảnh**: Ảnh nghiệm thu lưu tại `/images/nghiemthu/YYYY-MM/`. Cần giám sát disk space trên hosting định kỳ.
