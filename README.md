# CÔNG TY TNHH MTV ĐIỆN TỬ HIẾU

## GIỚI THIỆU

Đây là hệ thống nền tảng quản lý kinh doanh chuyên biệt, được phát triển nội bộ cho **Công ty TNHH MTV Điện Tử Hiếu**. Hệ thống được thiết kế với ngôn ngữ **PHP thuần**, tích hợp **MySQL** và giao diện tối ưu hóa (ADHD & OCD styles) để mang lại trải nghiệm người dùng tối ưu nhất.

Hệ thống hoạt động với 3 lĩnh vực mũi nhọn:
1. **Điện máy & Gia dụng**: Kinh doanh, phân phối thiết bị gia đình, giải pháp điện tử.
2. **Dịch vụ Kỹ thuật (Gọi thợ)**: Hệ thống điều phối thợ kỹ thuật tại nhà (tốc độ cao, chuyên nghiệp).
3. **Sản phẩm In 3D**: Thiết kế và cung cấp các sản phẩm in ấn công nghệ đặc thù.

Từ khóa cốt lõi của nền tảng: **"Mua hàng nhanh", "Gọi thợ ngay", "Tư vấn tận tâm".**

---

## TÍNH NĂNG CHI TIẾT CỐT LÕI

### 1. CỬA HÀNG ĐIỆN MÁY & GIA DỤNG
- Danh mục sản phẩm phong phú, hiển thị dưới dạng Grid/Accordion tinh gọn (OCD).
- Hệ thống làm nổi bật các sản phẩm đang có sự kiện hoặc mã giảm giá (ADHD).
- Quy trình Đặt hàng - Thanh toán khép kín, an toàn.
- Hệ thống giỏ hàng và lịch sử mua sắm trực quan.

### 2. DỊCH VỤ KỸ THUẬT & LẮP ĐẶT (GỌI THỢ NGAY)
- Form "Gọi Thợ" tích hợp lấy định vị GPS tự động.
- Điều phối đơn hàng realtime: Khách hàng nhấn "Chốt đơn", hệ thống lập tức thông báo qua **Telegram Bot** cho đội ngũ thợ kỹ thuật.
- Trạng thái ca trực minh bạch: Chờ xử lý, Đang xử lý, Hoàn thành.

### 3. HỆ THỐNG ĐĂNG KÝ & QUẢN LÝ TÀI KHOẢN KHÁCH HÀNG
- Quản lý thông tin khách hàng, số dư và lịch sử giao dịch.
- Đăng ký và Đăng nhập bảo mật.
- Theo dõi cấp độ thành viên và điểm tích lũy.

### 4. THANH TOÁN TỰ ĐỘNG & ĐA KÊNH
- Nạp tiền tự động qua API Ngân hàng (Quét QR nội dung tự động).
- Hỗ trợ thanh toán qua Ví điện tử Momo.
- Lịch sử dòng tiền và giao dịch được lưu trữ an toàn, minh bạch.

### 5. QUẢN TRỊ HỆ THỐNG (ADMIN DASHBOARD)
- **Dashboard Thống Kê**: Thống kê doanh thu, số lượng đơn hàng gọi thợ, số lượng sản phẩm điện máy bán ra.
- **Quản Lý Dịch Vụ**: Thêm/Sửa/Xóa cấu hình danh mục: [Điện Máy] | [Dịch vụ thợ] | [Sản phẩm 3D].
- **Cấu Hình Bộ Công Thương**: Cập nhật thông tin pháp lý, chính sách bảo mật, quy chế hoạt động minh bạch.
- **Quản Lý Giao Dịch**: Đối soát hóa đơn thanh toán và các giao dịch ngân hàng.

---

## CÔNG NGHỆ SỬ DỤNG

### Backend
- **PHP 7.4+** (Pure PHP - Module architecture)
- **MySQL/MariaDB** - Quản lý database
- **Telegram Bot API** - Realtime notification
- **PHPMailer** - Gửi email

### Frontend
- **Tailwind CSS / Bootstrap** - Giao diện kết hợp (ADHD & OCD)
- **jQuery & AJAX** - Tương tác thời gian thực không reload
- **Geolocation API** - Định vị tọa độ khách hàng trên web

---

## CHÍNH SÁCH VÀ PHÁP LÝ

Hệ thống tuân thủ chặt chẽ các quy định của **Bộ Công Thương**:
- **Bảo mật thông tin**: Mã hóa mật khẩu người dùng, bảo vệ dữ liệu cá nhân theo quy chuẩn.
- **Minh bạch**: Báo giá niêm yết rõ ràng, không có phí ẩn trong dịch vụ gọi thợ và mua bán.
- **An toàn giao dịch**: Tích hợp các tính năng phòng chống SQL Injection, XSS, CSRF và Rate limiting.

---

_Phát triển và sở hữu bản quyền bởi **CÔNG TY TNHH MTV ĐIỆN TỬ HIẾU**_
