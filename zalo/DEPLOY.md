# TRIỂN KHAI ZALO MINI APP - ĐIỆN MÁY HIẾU

## Cấu trúc chuẩn Zalo Mini App (native UI)

```
zalo/
├── app-config.json              # Cấu hình chuẩn Zalo Mini App
├── index.html                   # Entry point dev (root)
├── vite.config.js               # Cấu hình Vite + zmp-vite-plugin
├── package.json                 # Dependencies & scripts
├── zmp-cli.json                 # Cấu hình ZMP CLI
├── src/
│   ├── index.html               # HTML template cho build
│   ├── app.jsx                  # Root app: ZMPRouter + AnimationRoutes
│   ├── components/              # Các UI component tái sử dụng
│   │   ├── bottom-nav.jsx
│   │   ├── category-list.jsx
│   │   └── product-card.jsx
│   ├── pages/                   # Các trang Mini App
│   │   ├── index.jsx            # Trang chủ
│   │   ├── products.jsx         # Danh sách sản phẩm
│   │   ├── product-detail.jsx   # Chi tiết sản phẩm
│   │   ├── cart.jsx             # Giỏ hàng
│   │   └── contact.jsx          # Liên hệ
│   └── css/
│       └── app.scss             # Style theo brand Điện Máy Hiếu
└── dist/                        # Output production build
```

## Chuẩn app-config.json theo nhà sản xuất

Xem tài liệu chính thức: https://docs.zaloplatforms.com/docs/MA/devtools/app-config

File `app-config.json` chỉ gồm:

- `app`: Cấu hình giao diện (title, headerColor, textColor, statusBar, ...)
- `listCSS`: Danh sách CSS cần load
- `listSyncJS`: Danh sách JS load đồng bộ
- `listAsyncJS`: Danh sách JS load bất đồng bộ

## Nguyên lý hoạt động (native Mini App)

- Khi mở Mini App: hiển thị **trang chủ Điện Máy Hiếu** với banner, danh mục, sản phẩm nổi bật.
- Người dùng điều hướng qua các tab: **Trang chủ / Sản phẩm / Giỏ hàng / Liên hệ**.
- Sử dụng **ZaUI components** (`Page`, `Header`, `Box`, `Button`, `List`, `BottomNavigation`, ...).
- Định tuyến bằng **`ZMPRouter` + `AnimationRoutes`** từ `zmp-ui`.
- Dữ liệu sản phẩm hiện tại là dữ liệu mẫu; sau này tích hợp API từ `dienmayhieu.com`.

## Build production

```bash
cd "C:\Projects\dth-zalo"
npm install      # chỉ cần lần đầu hoặc khi package.json thay đổi
npm run build    # build ra dist/ + copy app-config.json gốc
npm run zip      # tạo file zalo-production.zip
```

Hoặc thủ công:

```powershell
npm run build
Compress-Archive -Path "dist\*" -DestinationPath "C:\Projects\dth-zalo\zalo-production.zip" -Force
```

## File sẵn sàng upload

```
C:\Projects\dth-zalo\zalo-production.zip
```

## Upload lên Zalo Mini App Platform

1. Vào https://miniapp.zaloplatforms.com
2. Chọn app **Điện Máy Hiếu** (App ID: `1271764499975436667`)
3. **Quản lý phiên bản** → **Tạo phiên bản mới**
4. Upload file `zalo-production.zip`
5. Đợi Zalo xử lý → bấm **Preview** để test
6. Kiểm tra:
   - Header màu `#0f172a`
   - Có thanh điều hướng dưới cùng (4 tab)
   - Chuyển trang có hiệu ứng
   - Không còn cảnh báo `robots.txt`, `manifest.json`, hay iframe tự do
7. Bấm **Gửi duyệt** nếu OK

## Lưu ý quan trọng

- **KHÔNG** để file `robots.txt` trong ZIP — Zalo không hỗ trợ `.txt`
- **KHÔNG** dùng `index.html` chứa iframe/webview tự do — phải là native UI
- **KHÔNG** để `manifest.json` tham chiếu file không tồn tại
- Dùng **ZMPRouter + AnimationRoutes** thay vì react-router-dom thuần
- Các hình ảnh sản phẩm tạm dùng ảnh mẫu từ Unsplash; khi có API thay bằng ảnh thật
- Nếu dùng `zmp-cli deploy`, cần Node.js v18 hoặc v20 (Node v26 trên máy anh hiện bị lỗi zmp-cli)

## Điều gì đã sửa so với bản cũ?

| Vấn đề cũ | Giải pháp |
|---|---|
| Chỉ là webview wrapper | Xây dựng native Mini App với ZaUI |
| Một trang duy nhất | Thêm router + 5 trang chuẩn Zalo |
| `openWebview()` mở website ngay | Giữ webview chỉ cho link bên ngoài nếu cần |
| Thiếu navigation | Thêm `BottomNavigation` 4 tab |
| `robots.txt` unsupported extension | Xóa file `robots.txt` |
| `manifest.json` tham chiếu logo không tồn tại | Xóa `manifest.json` |
