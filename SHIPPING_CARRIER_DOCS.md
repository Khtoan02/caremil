# Tài Liệu Triển Khai: Hiển Thị Phí Ship Theo Đơn Vị Vận Chuyển

## 📋 Tổng Quan

Đã triển khai thành công chức năng hiển thị thông tin đơn vị vận chuyển và phí ship trên theme **CareMIL**. Chức năng bao gồm:

✅ Hiển thị tên đơn vị vận chuyển (GHN, GHTK, J&T, v.v.)  
✅ Hiển thị icon emoji cho từng đơn vị  
✅ Hiển thị mã vận đơn (tracking code)  
✅ Link theo dõi đơn hàng (tracking URL)  
✅ So sánh phí gốc vs phí khách trả  
✅ Giao diện đẹp mắt với gradient và hiệu ứng hover  

---

## 📁 Các File Đã Chỉnh Sửa

### 1. **functions.php** ✨
**Đường dẫn:** `/wp-content/themes/caremil/functions.php`

**Chức năng mới:**
- `caremil_get_carrier_name($partner_id)` - Lấy tên đơn vị vận chuyển
- `caremil_get_carrier_code($partner_id)` - Lấy mã code (ghtk, ghn, j&t...)
- `caremil_get_carrier_tracking_url($partner_id, $tracking_code)` - Tạo link tracking
- `caremil_get_carrier_icon($partner_id)` - Lấy emoji icon

**Danh Sách Đơn Vị Vận Chuyển Được Hỗ Trợ:**
| ID | Tên | Icon | Tracking |
|----|-----|------|----------|
| 0  | Snappy | 🚚 | ❌ |
| 1  | Giao hàng tiết kiệm (GHTK) | 📦 | ✅ |
| 2  | EMS | ✉️ | ❌ |
| 4  | 247 Express | ⚡ | ❌ |
| 5  | Giao hàng nhanh (GHN) | 🚀 | ✅ |
| 7  | **Viettel Post (VTP)** | 📮 | ❌ |
| 9  | DHL | ✈️ | ✅ |
| 11 | Ahamove | 🛵 | ❌ |
| 15 | J&T Express | 📦 | ✅ |
| 17 | VN Post | 📮 | ✅ |
| 19 | Ninja Van | 🥷 | ✅ |
| 32 | SuperShip | ⚡ | ❌ |
| 37 | Grab Express | 🚗 | ❌ |
| 41 | Flash Express | ⚡ | ❌ |

**🆕 VTP (Viettel Post) - Đơn vị chính:**
- Partner ID: `7`
- Phí mặc định: **30.000đ**
- Hiển thị trên trang checkout
- Freeship khi áp mã có type='freeship'


---

### 2. **order-status.php** 🎨
**Đường dẫn:** `/wp-content/themes/caremil/order-status.php`

**Nội dung thay đổi:**
1. **Trích xuất dữ liệu carrier** (dòng 177-194):
   ```php
   $partner_info = $order_details['partner'] ?? null;
   $partner_id = $partner_info['partner_id'] ?? null;
   $tracking_code = $partner_info['extend_code'] ?? '';
   $carrier_name = caremil_get_carrier_name( $partner_id );
   ```

2. **Hiển thị UI carrier** (dòng 462-520):
   - Card đẹp với gradient background
   - Icon + Tên carrier
   - Badge hiển thị phí ship
   - Link tracking với external icon
   - So sánh phí gốc vs phí khách trả

**Giao diện:**
```
┌────────────────────────────────────┐
│ ĐƠN VỊ VẬN CHUYỂN                 │
├────────────────────────────────────┤
│ 🚀 Giao hàng nhanh        35.000đ │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│ 🔢 GHNVN123456789     [External] │
├────────────────────────────────────┤
│ Phí gốc ĐVVC: 40.000đ             │
└────────────────────────────────────┘
```

---

### 3. **user-account.php** 💼
**Đường dẫn:** `/wp-content/themes/caremil/user-account.php`

**Nội dung thay đổi:**
1. **Modal HTML** (dòng 1037-1055):
   - Thêm section hiển thị carrier info
   - Responsive design (2 columns on desktop)
   - Hidden by default, hiện khi có data

2. **JavaScript Logic** (dòng 1168-1236):
   - Carrier mapping trong JS (sync với PHP)
   - Dynamic hiển thị theo dữ liệu order
   - Xử lý tracking URL
   - Toggle visibility thông minh

---

### 4. **checkout.php** 🛒
**Đường dẫn:** `/wp-content/themes/caremil/checkout.php`

**Nội dung thay đổi:**
1. **Logic Phí Ship** (dòng 565-578):
   ```php
   // Default shipping fee - VTP (Viettel Post)
   $shipping = 30000; // 30.000đ phí ship chuẩn
   
   // Apply freeship if coupon type is 'freeship'
   if ( isset( $_SESSION['caremil_applied_coupon'] ) ) {
       $c_id = $_SESSION['caremil_applied_coupon']['id'];
       $coupon_type = get_post_meta($c_id, '_coupon_type', true);
       if ( $coupon_type === 'freeship' ) {
           $shipping = 0;
       }
   }
   ```

2. **Hiển thị Carrier** (dòng 592-602):
   - Hiển thị icon + tên VTP khi có phí ship
   - Ẩn khi freeship (shipping = 0)
   - Dynamic color: xanh (freeship) / xám (có phí)

**Giao diện:**
```
┌────────────────────────────────┐
│ Phí vận chuyển      30.000đ   │
│   📮 Viettel Post (VTP)       │
└────────────────────────────────┘

Hoặc khi freeship:
┌────────────────────────────────┐
│ Phí vận chuyển      Miễn phí  │
└────────────────────────────────┘
```

---

## 🎯 Cách Hoạt Động

### Luồng Dữ Liệu:

```
┌─────────────────┐
│ Pancake POS API │
│ (order.partner) │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   partner_id    │ ──┐
│  extend_code    │   │
│   total_fee     │   │
└────────┬────────┘   │
         │            │
         ▼            │
┌─────────────────┐   │  ┌──────────────┐
│ Helper Function │◄──┘  │  Mapping DB  │
│ caremil_get_*() │      │ 0=>Snappy... │
└────────┬────────┘      └──────────────┘
         │
         ▼
┌─────────────────┐
│  Display Info   │
│ - Carrier Name  │
│ - Icon          │
│ - Tracking URL  │
│ - Fee Breakdown │
└─────────────────┘
```

### Điều Kiện Hiển Thị:

✅ **Hiển thị Carrier Info khi:**
- `$partner_id !== null`
- `$shipping_fee > 0`

✅ **Hiển thị Tracking Link khi:**
- Có `tracking_code`
- Carrier có hỗ trợ tracking (check mapping)

---

## 🧪 Test Cases

### Test 1: Đơn Hàng GHN
**Input:**
```json
{
  "partner": {
    "partner_id": 5,
    "extend_code": "GHN12345",
    "total_fee": 40000
  },
  "shipping_fee": 35000
}
```

**Expected Output:**
- Tên: "Giao hàng nhanh"
- Icon: 🚀
- Fee: 35.000đ
- Tracking: Link đến `donhang.ghn.vn`
- Phí gốc: 40.000đ

---

### Test 2: Đơn Không Có Carrier
**Input:**
```json
{
  "shipping_fee": 0
}
```

**Expected Output:**
- Carrier section: HIDDEN
- Chỉ hiển thị fallback (nếu có phí ship)

---

### Test 3: Carrier Không Hỗ Trợ Tracking
**Input:**
```json
{
  "partner": {
    "partner_id": 0,
    "extend_code": "SNAPPY123"
  },
  "shipping_fee": 25000
}
```

**Expected Output:**
- Tên: "Snappy"
- Icon: 🚚
- Fee: 25.000đ
- Tracking code hiển thị nhưng không có link

---

## 🎨 UI/UX Features

### Design Highlights:
✨ Gradient background (blue → green)  
✨ Icon size 2xl cho dễ nhìn  
✨ Badge với background color-coded  
✨ Hover effects trên tracking link  
✨ External link icon fade-in on hover  
✨ Mono font cho tracking code  
✨ Border với opacity cho depth  

### Responsive:
📱 Mobile: Stack vertical  
💻 Desktop: 2-column grid trong modal  
✅ Tailwind utility classes  

---

## 📈 Mở Rộng Trong Tương Lai

### Dễ Dàng Thêm Carrier Mới:
1. Thêm vào `$carriers` array trong `functions.php`
2. Thêm icon tương ứng
3. (Optional) Thêm tracking URL pattern

### Ví dụ Thêm Kerry Express:
```php
// In functions.php
42 => 'Kerry Express',  // Carrier name

// Icon
42 => '🚛',

// Tracking URL (if  available)
42 => 'https://kerry.vn/track?code=' . urlencode($tracking_code)
```

### Sync với JavaScript:
```javascript
// In user-account.php fillOrderModal()
42: 'Kerry Express',  // carrierNames
42: '🚛',  // carrierIcons
42: (code) => `https://kerry.vn/track?code=${encodeURIComponent(code)}`
```

---

## 🔧 Troubleshooting

### Issue 1: Carrier Không Hiển Thị
**Kiểm tra:**
1. API có trả về `partner` object không?
2. `partner_id` có giá trị hợp lệ?
3. `shipping_fee > 0`?
4. Console có báo lỗi JS không?

### Issue 2: Tracking Link Không Hoạt Động
**Kiểm tra:**
1. `extend_code` có giá trị không?
2. Carrier ID có trong `trackingUrls` mapping?
3. URL pattern đúng format chưa?

### Issue 3: Icon Không Hiển Thị
**Nguyên nhân:** Browser không hỗ trợ emoji  
**Giải pháp:** Thay bằng Font Awesome icons

---

## 📝 Notes

- ⚠️ Dữ liệu carrier phụ thuộc vào Pancake POS API
- ⚠️ Một số carrier chưa có tracking URL (cần cập nhật sau)
- ✅ Code đã optimize với fallback cho tất cả trường hợp
- ✅ Compatible với existing checkout flow  

---

## 🎉 Kết Luận

Chức năng đã được triển khai hoàn chỉnh với:
- ✅ Backend helpers (PHP)
- ✅ Frontend display (HTML/CSS)
- ✅ Interactive logic (JavaScript)
- ✅ Responsive design
- ✅ User-friendly UI
- ✅ Extensible architecture

**Ready for Production! 🚀**
