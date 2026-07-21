# Điện Máy Hiếu - Zalo Mini App

Zalo Mini App chuẩn cho cửa hàng Điện Máy Hiếu tại Lấp Vò, Đồng Tháp.

## Công nghệ

- React 18
- zmp-ui (ZaUI) - UI kit chuẩn Zalo Mini App
- zmp-sdk - Zalo Mini App SDK
- zmp-vite-plugin - Plugin Vite cho ZMP
- Vite 5 + SCSS

## Cấu trúc

- `src/app.jsx` - Root app với ZMPRouter
- `src/pages/` - Các trang Mini App
- `src/components/` - UI components tái sử dụng
- `src/css/app.scss` - Style chung

## Chạy dev

```bash
npm install
npm run dev
```

## Build production

```bash
npm run build
npm run zip
```

File sẵn sàng upload: `zalo-production.zip`

## Triển khai

Xem `DEPLOY.md`.
