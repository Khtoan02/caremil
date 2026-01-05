# HƯỚNG DẪN HỆ THỐNG TÍNH PHÍ VẬN CHUYỂN VIETTEL POST

## 📋 Tổng Quan

Hệ thống tích hợp API Viettel Post v2 để tính phí vận chuyển realtime cho đơn hàng. Khi API lỗi, hệ thống tự động dùng bảng phí dự phòng (fallback).

---

## 🔑 Cấu Hình

### Token API
Token được lưu trong `/includes/viettel-shipping.php`:
```php
CAREMIL_VTP_TOKEN = "eyJhbGciOiJFUzI1NiJ9..."
```
**Hết hạn**: 2026-09-01

### Vị trí Shop Mặc Định
```php
CAREMIL_SHOP_PROVINCE_ID = 1  // Hà Nội
CAREMIL_SHOP_DISTRICT_ID = 5  // Cầu Giấy
```

Nếu bạn cần đổi vị trí shop, sửa 2 constants này.

---

## ⚙️ Cách Hoạt Động

### 1. **Tự Động Tính Phí Khi Checkout**

Khi user điền địa chỉ giao hàng:
```
1. User chọn Tỉnh/Thành và Quận/Huyện
2. JavaScript gọi AJAX `caremil_calculate_shipping`
3. Server gọi API Viettel Post với:
   - Địa chỉ gửi: Shop (Hà Nội - Cầu Giấy)
   - Địa chỉ nhận: User address
   - Trọng lượng: Tính từ giỏ hàng
   - COD: Tổng tiền (nếu COD) hoặc 0 (nếu online)
4. API trả về phí ship → Hiển thị trên checkout
5. Cache kết quả 1 giờ
```

### 2. **Tính Trọng Lượng Giỏ Hàng**

Hàm `caremil_calculate_cart_weight()` sẽ:
- Lấy `product_weight` meta của từng sản phẩm
- Nếu không có → Mặc định 500g (sản phẩm sữa)
- Tổng trọng lượng = Σ(weight × quantity)
- Tối thiểu: 100g

**Lưu ý**: Hiện tại product meta chưa có `product_weight`. Bạn cần:
- Thêm field trong admin product editor, HOẶC
- Hệ thống sẽ dùng 500g/sản phẩm

### 3. **Logic Freeship**

```php
if (có mã freeship) {
    $original_fee = API_fee; // VD: 35.000đ
    $shipping_fee = 0;
    $note = "Freeship - Saved: 35.000đ";
}
```

### 4. **Logic COD vs Online Payment**

**COD (Thu hộ):**
```php
$cod_amount = $order_total + $shipping_fee;
$note = "COD: Shipper thu của khách";
```

**Online (Đã thanh toán):**
```php
$cod_amount = 0;
$shipping_fee = X đ; // Shop tự trả
$note = "Đã thanh toán online";
```

---

## 📦 Hàm API Chính

### `caremil_vtp_calculate_shipping($province_id, $district_id, $weight, $cod, $value)`
Gọi API Viettel Post để tính phí.

**Return:**
```php
[
    'fee' => 35000,
    'service' => 'Viettel Chuyển Nhanh',
    'time' => '2-3 ngày',
    'service_code' => 'VCN',
    'all_services' => [...] // Tất cả dịch vụ khả dụng
]
```

### `caremil_get_shipping_info($customer_data, $is_cod, $order_total)`
Wrapper function - Tự động fallback nếu API lỗi.

### `caremil_vtp_get_fallback_fee($province_id)`
Bảng phí dự phòng:
- Hà Nội: 30.000đ
- HCM: 35.000đ
- Đà Nẵng: 40.000đ
- Khác: 45.000đ

---

## 🔄 Flow Tạo Đơn Hàng

```
1. User điền thông tin → Tính phí ship realtime
2. User áp mã voucher (nếu có)
3. User chọn COD hoặc Online Payment
4. Click "Đặt Hàng"
5. Server tính lại:
   - Subtotal
   - Discount (order + freeship)
   - Shipping fee
   - Grand Total
6. Gửi payload đến Pancake POS:
   {
     "items": [...],
     "discount_amount": X,
     "shipping_fee": Y,
     "cod_amount": Z, // 0 nếu online, Grand Total nếu COD
     "note": "Voucher: ABC, Vận chuyển: VCN, Phí: 35k"
   }
```

---

## 🧪 Test & Debug

### Kiểm tra Cache
Cache được lưu trong WordPress Transients (1 giờ):
```php
delete_transient('vtp_shipping_' . md5(...)); // Clear cache
```

### Debug Log
File: `/Applications/ServBay/www/dawnbridge/pancake_order_debug.log`

Mỗi lần tạo đơn sẽ ghi:
- Payload gửi API
- Response nhận về
- Discount & Shipping info

### Test API Trực Tiếp
```bash
curl -X POST https://partner.viettelpost.vn/v2/order/getPriceAll \
  -H "Content-Type: application/json" \
  -H "Token: YOUR_TOKEN" \
  -d '{
    "SENDER_PROVINCE": 1,
    "SENDER_DISTRICT": 5,
    "RECEIVER_PROVINCE": 2,
    "RECEIVER_DISTRICT": 18,
    "PRODUCT_TYPE": "HH",
    "PRODUCT_WEIGHT": 1000,
    "MONEY_COLLECTION": "500000"
  }'
```

---

## ⚠️ Lưu Ý Quan Trọng

### 1. Token Hết Hạn (2026)
- Khi hết hạn, cần lấy token mới từ Viettel Post
- Update constant `CAREMIL_VTP_TOKEN`

### 2. Product Weight
Hiện tại products chưa có meta `product_weight`. 
**Giải pháp tạm thời**: Mặc định 500g/sản phẩm
**Giải pháp dài hạn**: Thêm field trong product editor

### 3. API Rate Limit
Viettel Post có thể giới hạn số request/giây.
Hệ thống đã cache 1 giờ để giảm load.

### 4. Fallback Luôn Hoạt Động
Nếu API lỗi → Tự động dùng bảng phí cố định
→ Đơn hàng vẫn được tạo bình thường

---

## 🎯 Các Trường Hợp Đặc Biệt

### Freeship + COD
```
Tạm tính: 500.000đ
Giảm giá: -50.000đ
Phí ship: 0đ (Freeship)
→ COD: 450.000đ
→ Note: "Freeship - Saved: 35.000đ"
```

### Multi Voucher + Online Payment
```
Tạm tính: 1.000.000đ
Voucher 1: -100.000đ
Voucher 2: -50.000đ
Phí ship: 35.000đ
→ Total: 885.000đ (Đã thanh toán)
→ COD: 0đ
→ Note: "Voucher: V1, V2 | Vận chuyển: VCN 35k | Đã TT Online"
```

---

## 📞 Support

Nếu cần hỗ trợ:
1. Check log file `/pancake_order_debug.log`
2. Test API trực tiếp với curl
3. Kiểm tra cache transients
4. Verify token chưa hết hạn

**API Documentation**: https://partner.viettelpost.vn/docs/v2
