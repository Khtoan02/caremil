# Caremil WordPress Theme

Theme WordPress hiện đại và tối giản cho Caremil, được xây dựng với Tailwind CSS.

## Tính năng

- ✅ Tailwind CSS 3.4+ để styling hiện đại
- ✅ Responsive design, tối ưu cho mọi thiết bị
- ✅ Hỗ trợ đầy đủ các tính năng WordPress cơ bản
- ✅ Customizable colors và typography
- ✅ SEO friendly
- ✅ Fast và lightweight

## Yêu cầu

- WordPress 5.0+
- Node.js 16+ và npm (để build Tailwind CSS)

## Cài đặt

### 1. Cài đặt theme

1. Copy thư mục `caremil` vào `wp-content/themes/`
2. Kích hoạt theme trong WordPress Admin → Appearance → Themes

### 2. Cài đặt và build Tailwind CSS

Mở terminal trong thư mục theme và chạy:

```bash
cd wp-content/themes/caremil
npm install
npm run build
```

Lệnh này sẽ:
- Cài đặt Tailwind CSS và các dependencies
- Build file CSS từ `src/input.css` thành `dist/style.css`

### 3. Phát triển (Development)

Để tự động build CSS khi bạn chỉnh sửa, chạy:

```bash
npm run dev
```

Lệnh này sẽ watch các file và tự động rebuild CSS khi có thay đổi.

## Cấu trúc thư mục

```
caremil/
├── dist/              # CSS đã build (tự động tạo)
├── src/
│   └── input.css      # File CSS input cho Tailwind
├── js/
│   └── main.js        # JavaScript chính
├── *.php              # Template files
├── style.css          # Theme header (bắt buộc cho WordPress)
├── functions.php      # Theme functions
├── tailwind.config.js # Cấu hình Tailwind
└── package.json       # Dependencies
```

## Tùy chỉnh

### Màu sắc

Chỉnh sửa màu sắc trong `tailwind.config.js`:

```javascript
colors: {
  primary: {
    DEFAULT: '#0073aa',
    dark: '#005177',
    light: '#0085ba',
  },
}
```

### Custom CSS

Thêm custom CSS vào `src/input.css` trong các layer:

- `@layer base` - Base styles
- `@layer components` - Component styles
- `@layer utilities` - Utility classes

Sau đó chạy `npm run build` để rebuild.

## Menu

Theme hỗ trợ 2 menu locations:
- **Menu Chính** (Primary) - Hiển thị ở header
- **Menu Footer** - Hiển thị ở footer

Cấu hình menu trong WordPress Admin → Appearance → Menus.

## Widget Areas

Theme có 3 widget areas:
- **Sidebar** - Sidebar chính
- **Footer 1** - Footer cột 1
- **Footer 2** - Footer cột 2

## Hỗ trợ

Nếu gặp vấn đề, vui lòng kiểm tra:
1. Đã cài đặt Node.js và npm chưa
2. Đã chạy `npm install` và `npm run build` chưa
3. File `dist/style.css` đã được tạo chưa

## License

MIT

