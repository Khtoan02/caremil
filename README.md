# Caremil WordPress Theme

Theme WordPress hiện đại và tối giản cho Caremil, sử dụng **Tailwind CSS qua CDN**.

## ✨ Tính năng

- ✅ **Tailwind CSS 3.4.1** qua CDN - không cần build process
- ✅ Responsive design, tối ưu cho mọi thiết bị
- ✅ Giao diện hiện đại với card design và shadow effects
- ✅ Hỗ trợ đầy đủ các tính năng WordPress cơ bản
- ✅ Fast và lightweight
- ✅ Dễ dàng tùy chỉnh với Tailwind utility classes

## 🚀 Cài đặt

1. Copy thư mục `caremil` vào `wp-content/themes/`
2. Kích hoạt theme trong **WordPress Admin → Appearance → Themes**

**Xong!** Theme đã sẵn sàng sử dụng. Không cần cài đặt Node.js hay build process.

## 📁 Cấu trúc thư mục

```
caremil/
├── js/
│   └── main.js        # JavaScript chính
├── *.php              # Template files
├── style.css          # Theme header (bắt buộc cho WordPress)
└── functions.php      # Theme functions (enqueue Tailwind CDN)
```

## 🎨 Sử dụng Tailwind CSS

Theme đã được cấu hình để load Tailwind CSS từ CDN. Bạn có thể sử dụng bất kỳ Tailwind utility class nào trong các template files.

### Ví dụ:

```html
<div class="bg-blue-500 text-white p-4 rounded-lg shadow-md">
    Nội dung của bạn
</div>
```

### Tài liệu Tailwind:
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Tailwind Cheat Sheet](https://nerdcave.com/tailwind-cheat-sheet)

## 🎯 Menu

Theme hỗ trợ 2 menu locations:
- **Menu Chính** (Primary) - Hiển thị ở header
- **Menu Footer** - Hiển thị ở footer

Cấu hình menu trong **WordPress Admin → Appearance → Menus**.

## 📦 Widget Areas

Theme có 3 widget areas:
- **Sidebar** - Sidebar chính
- **Footer 1** - Footer cột 1
- **Footer 2** - Footer cột 2

## 🎨 Tùy chỉnh màu sắc

Để thay đổi màu sắc, bạn có thể:
1. Sử dụng Tailwind color classes có sẵn (blue, green, red, etc.)
2. Thêm custom CSS vào `style.css` nếu cần

Ví dụ thay đổi màu primary từ blue sang green:
- Tìm và thay `text-blue-600` thành `text-green-600`
- Tìm và thay `bg-blue-600` thành `bg-green-600`

## 📝 Template Files

- `index.php` - Trang chủ / Danh sách bài viết
- `single.php` - Trang bài viết đơn
- `page.php` - Trang tĩnh
- `404.php` - Trang lỗi 404
- `search.php` - Trang tìm kiếm
- `header.php` - Header
- `footer.php` - Footer
- `sidebar.php` - Sidebar

## 🔧 Tùy chỉnh nâng cao

Nếu bạn muốn thêm custom CSS, thêm vào file `style.css` sau phần theme header comment:

```css
/* Custom styles */
.your-custom-class {
    /* your styles */
}
```

## 📱 Responsive

Theme sử dụng Tailwind responsive prefixes:
- `sm:` - Small devices (640px+)
- `md:` - Medium devices (768px+)
- `lg:` - Large devices (1024px+)
- `xl:` - Extra large devices (1280px+)

## ⚡ Performance

Tailwind CSS được load từ CDN (jsDelivr), giúp:
- Không cần build process
- Load nhanh từ CDN
- Luôn có phiên bản mới nhất

## 📄 License

MIT

---

**Lưu ý:** Theme sử dụng Tailwind CDN, cần kết nối internet để load CSS. Nếu muốn offline, bạn có thể download Tailwind CSS và host local.













