# SCHEMA_DEFINITION.md — Điện Máy Hiếu
> Định nghĩa đầy đủ schema database.  
> Cập nhật lần cuối: 2026-07-24

---

## Bảng `users` — Tài khoản người dùng

```sql
CREATE TABLE `users` (
  `id`          int(11) NOT NULL AUTO_INCREMENT,
  `username`    varchar(255) NOT NULL COMMENT 'Tên đăng nhập hoặc SĐT (khách hàng)',
  `password`    varchar(255) NOT NULL COMMENT 'md5(password) — cần migrate sang bcrypt',
  `name`        varchar(255) DEFAULT NULL COMMENT 'Họ tên hiển thị',
  `fullname`    varchar(255) DEFAULT NULL COMMENT 'Họ tên đầy đủ (trường dự phòng)',
  `phone`       varchar(20) DEFAULT NULL COMMENT 'Số điện thoại liên hệ',
  `address`     text DEFAULT NULL COMMENT 'Địa chỉ (khách hàng)',
  `level`       varchar(50) DEFAULT 'user' COMMENT 'user|tho|admin|bct',
  `tokenlog`    varchar(255) DEFAULT NULL COMMENT 'Session token lưu trong cookie',
  `banned`      varchar(10) DEFAULT 'ON' COMMENT 'ON=hoạt động, OFF=bị khóa',
  `money`       int(11) DEFAULT 0 COMMENT 'Số dư thợ (âm = nợ phí nền tảng)',
  `verify`      int(1) DEFAULT 0 COMMENT '1=đã xác minh email',
  `first_login` tinyint(1) DEFAULT 1 COMMENT '1=chưa update profile, 0=đã update',
  `points`      int(11) DEFAULT 0 COMMENT 'Điểm tích lũy (khách hàng)',
  `zalo_id`     varchar(100) DEFAULT NULL COMMENT 'Zalo user ID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Phân loại `level`:
| Giá trị | Vai trò | Quyền truy cập |
|---|---|---|
| `user` | Khách hàng | Đặt lịch, tra cứu, đánh giá |
| `tho` | Thợ kỹ thuật | Dashboard thợ, nhận đơn, báo giá, nghiệm thu |
| `admin` | Quản trị viên | Toàn bộ hệ thống |
| `bct` | Cán bộ Bộ Công Thương | Xem báo cáo kiểm duyệt TMĐT |

---

## Bảng `dat_lich` — Đơn gọi thợ

```sql
CREATE TABLE `dat_lich` (
  `id`                int(11) NOT NULL AUTO_INCREMENT,
  `ten`               varchar(255) NOT NULL COMMENT 'Tên khách hàng',
  `sdt`               varchar(20) NOT NULL COMMENT 'Số điện thoại khách',
  `diachi`            text NOT NULL COMMENT 'Địa chỉ. Nếu có GPS: "Địa chỉ | GPS: lat,lng | https://maps..."',
  `yeucau`            text COMMENT 'Mô tả tình trạng thiết bị / yêu cầu',
  `dichvu`            varchar(255) DEFAULT NULL COMMENT 'Loại dịch vụ (Điện lạnh, Tivi, ...)',
  `thoigian`          int(11) NOT NULL COMMENT 'Unix timestamp khi đặt lịch',
  `trangthai`         varchar(50) DEFAULT 'CHO_XU_LY' COMMENT 'CHO_XU_LY|DANG_XU_LY|HOAN_THANH|DA_HUY',
  `tho_id`            int(11) DEFAULT 0 COMMENT 'FK users.id của thợ nhận đơn (0 = chưa có thợ)',
  `vat_requested`     tinyint(1) DEFAULT 0 COMMENT '1=khách yêu cầu xuất hoá đơn VAT',
  -- Báo giá phát sinh
  `phatsinh_mota`     text DEFAULT NULL COMMENT 'Mô tả vật tư/công việc phát sinh',
  `phatsinh_gia`      int(11) DEFAULT 0 COMMENT 'Giá phát sinh (VND)',
  `phatsinh_duyet`    tinyint(1) DEFAULT 0 COMMENT '0=chờ duyệt, 1=admin đã duyệt',
  -- Nghiệm thu
  `nghiemthu_note`    text DEFAULT NULL COMMENT 'Ghi chú nghiệm thu của thợ',
  `nghiemthu_anh`     text DEFAULT NULL COMMENT 'Path ảnh nghiệm thu trên server (/images/nghiemthu/...)',
  -- Đánh giá dịch vụ (do khách điền sau khi hoàn thành)
  `danhgia_sao`       tinyint(1) DEFAULT NULL COMMENT 'Số sao 1-5 (NULL = chưa đánh giá)',
  `danhgia_noidung`   text DEFAULT NULL COMMENT 'Nội dung nhận xét của khách',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Vòng đời `trangthai`:
```
CHO_XU_LY ──► DANG_XU_LY ──► HOAN_THANH
     │                │
     └────────────────┴──► DA_HUY
```

### Index đề xuất thêm (nếu data lớn):
```sql
ALTER TABLE `dat_lich` ADD INDEX `idx_trangthai` (`trangthai`);
ALTER TABLE `dat_lich` ADD INDEX `idx_tho_id`    (`tho_id`);
ALTER TABLE `dat_lich` ADD INDEX `idx_sdt`        (`sdt`);
```

---

## Bảng `products` — Sản phẩm

```sql
CREATE TABLE `products` (
  `id`          int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL COMMENT 'FK product_categories.id',
  `name`        varchar(500) NOT NULL,
  `slug`        varchar(500) DEFAULT NULL,
  `description` text,
  `price`       decimal(15,0) DEFAULT 0,
  `stock`       int(11) DEFAULT 0 COMMENT 'Số lượng tồn kho',
  `image`       text COMMENT 'URL ảnh sản phẩm',
  `type`        varchar(50) DEFAULT 'dien_may' COMMENT 'dien_may|3d_print',
  `status`      varchar(20) DEFAULT 'active' COMMENT 'active|inactive',
  `featured`    tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## Bảng `product_categories` — Danh mục

```sql
CREATE TABLE `product_categories` (
  `id`         int(11) NOT NULL AUTO_INCREMENT,
  `name`       varchar(255) NOT NULL,
  `slug`       varchar(255) DEFAULT NULL,
  `type`       varchar(50) DEFAULT 'dien_may',
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## Bảng `store_orders` — Đơn hàng sản phẩm

```sql
CREATE TABLE `store_orders` (
  `id`             int(11) NOT NULL AUTO_INCREMENT,
  `user_id`        int(11) NOT NULL COMMENT 'FK users.id',
  `total_amount`   decimal(15,0) DEFAULT 0,
  `status`         varchar(50) DEFAULT 'pending' COMMENT 'pending|processing|shipping|completed|cancelled',
  `customer_name`  varchar(255) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_addr`  text DEFAULT NULL,
  `note`           text DEFAULT NULL,
  `shipper_name`   varchar(255) DEFAULT NULL,
  `shipper_phone`  varchar(20) DEFAULT NULL,
  `tracking_code`  varchar(100) DEFAULT NULL,
  `shipped_at`     int(11) DEFAULT NULL COMMENT 'Unix timestamp',
  `delivered_at`   int(11) DEFAULT NULL COMMENT 'Unix timestamp',
  `created_at`     int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## Bảng `store_order_items` — Chi tiết đơn hàng

```sql
CREATE TABLE `store_order_items` (
  `id`         int(11) NOT NULL AUTO_INCREMENT,
  `order_id`   int(11) NOT NULL COMMENT 'FK store_orders.id',
  `product_id` int(11) NOT NULL COMMENT 'FK products.id',
  `quantity`   int(11) DEFAULT 1,
  `price`      decimal(15,0) DEFAULT 0 COMMENT 'Giá tại thời điểm đặt',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## Bảng `options` — Cấu hình hệ thống

```sql
CREATE TABLE `options` (
  `key`   varchar(100) NOT NULL,
  `value` text,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
Truy cập qua: `$DMH->site('key_name')`.

---

## Bảng `admin_menus` — Menu Admin động

```sql
CREATE TABLE `admin_menus` (
  `id`             int(11) NOT NULL AUTO_INCREMENT,
  `parent_id`      int(11) DEFAULT 0,
  `title`          varchar(255) NOT NULL,
  `url`            varchar(500) DEFAULT NULL,
  `icon`           varchar(100) DEFAULT NULL COMMENT 'FontAwesome class',
  `level_required` varchar(50) DEFAULT 'admin',
  `sort_order`     int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## Thư Mục Upload

```
images/
└── nghiemthu/
    └── YYYY-MM/          # Tổ chức theo tháng
        └── nt_{tho_id}_{timestamp}_{random}.{ext}
```

**Quy tắc upload** (`controller/client/UploadNghiemThu.php`):
- MIME types được phép: `image/jpeg`, `image/png`, `image/webp`, `image/gif`
- Kích thước tối đa: 5MB
- Validate MIME type thực sự bằng `finfo`, không dựa vào extension
- Tên file random 8 hex chars để tránh đoán path
