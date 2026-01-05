<?php
/**
 * Template Name: Checkout
 * Template Post Type: page
 * Description: Template for displaying checkout page
 *
 * @package Caremil
 */
// Kiểm tra giỏ hàng trống - redirect về trang giỏ hàng (BEFORE get_header)
$cart = caremil_get_cart();
if ( empty( $cart ) ) {
    wp_redirect( caremil_get_page_url_by_template( 'Carts' ) );
    exit;
}

get_header();

// Kiểm tra kết nối Pancake trước khi xử lý checkout
if ( function_exists( 'caremil_require_pancake_connection' ) ) {
    caremil_require_pancake_connection();
}

// Lấy thông tin đăng nhập Pancake từ session
if ( ! session_id() ) {
    session_start();
}

$caremil_logged_in     = ! empty( $_SESSION['pancake_logged_in'] );
$caremil_customer_id   = isset( $_SESSION['pancake_customer_id'] ) ? sanitize_text_field( $_SESSION['pancake_customer_id'] ) : '';
$caremil_phone_session = isset( $_SESSION['pancake_phone'] ) ? sanitize_text_field( $_SESSION['pancake_phone'] ) : '';
$caremil_display_name  = isset( $_SESSION['pancake_name'] ) ? sanitize_text_field( $_SESSION['pancake_name'] ) : '';
$caremil_display_phone = $caremil_phone_session;
$caremil_addresses     = array();
$caremil_addr_nonce    = wp_create_nonce( 'caremil_addr_nonce' );

if ( $caremil_logged_in ) {
    $caremil_customer = array();

    if ( $caremil_customer_id ) {
        $caremil_customer = caremil_pancake_request(
            '/shops/' . caremil_get_pancake_shop_id() . '/customers/' . $caremil_customer_id
        );
    }

    // Fix: Check for API error response (success => false)
    $customer_invalid = empty( $caremil_customer ) || ! is_array( $caremil_customer );
    if ( ! $customer_invalid && isset( $caremil_customer['success'] ) && ! $caremil_customer['success'] ) {
        $customer_invalid = true;
    }

    if ( $customer_invalid && $caremil_phone_session ) {
        $found_by_phone   = caremil_pancake_request(
            '/shops/' . caremil_get_pancake_shop_id() . '/customers',
            array(
                'search'    => $caremil_phone_session,
                'page_size' => 5,
            )
        );
        $caremil_customer = ( is_array( $found_by_phone ) && isset( $found_by_phone['data'][0] ) ) ? $found_by_phone['data'][0] : array();
    }

    $caremil_addresses_raw = caremil_get_pancake_addresses_from_customer( $caremil_customer );

    // Loại bỏ trùng địa chỉ và địa chỉ rác
    $deduped = array();
    $seen    = array();
    foreach ( $caremil_addresses_raw as $addr ) {
        $full = isset( $addr['full_address'] ) ? $addr['full_address'] : ( $addr['address'] ?? '' );
        $full = trim( $full, " \t\n\r\0\x0B," ); // Trim cả dấu phẩy thừa
        
        // Bỏ qua địa chỉ quá ngắn hoặc không có nghĩa
        if ( strlen( $full ) < 8 ) {
            continue;
        }
        
        $key = strtolower( preg_replace( '/\s+/', ' ', $full ) );
        if ( isset( $seen[ $key ] ) ) {
            continue;
        }
        $seen[ $key ]  = true;
        
        // Ensure ID for JS
        if ( ! isset( $addr['id'] ) ) {
            $addr['id'] = 'addr_' . count($deduped);
        }
        
        $deduped[] = $addr;
    }

    $caremil_addresses = $deduped;
}
?>
<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - CareMIL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            navy: '#0f172a',
                            blue: '#3b82f6',
                            gold: '#3b82f6',
                            soft: '#e0fbfc',
                            cream: '#fffdf2',
                            pink: '#0f172a',
                            green: '#4ade80'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Inter', 'cursive'],
                    }
                }
            }
        }
    </script>
    <style>
        /* Stepper Styles */
        .step.active .step-circle { background-color: #0f172a; color: white; border-color: #0f172a; }
        .step.completed .step-circle { background-color: #4ade80; color: white; border-color: #4ade80; }
        .step.active .step-text { color: #0f172a; font-weight: 700; }

        /* Form Styles */
        .form-label { font-size: 0.85rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; display: block; }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            background-color: #f8fafc;
            transition: all 0.3s ease;
            outline: none;
            font-weight: 600;
            color: #334155;
        }
        .form-input:focus {
            border-color: #3b82f6;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(76, 201, 240, 0.1);
        }
        .form-input:disabled {
            background-color: #e2e8f0;
            color: #94a3b8;
            cursor: not-allowed;
        }
        .form-input::placeholder { color: #94a3b8; font-weight: 500; }

        /* Payment Method Radio */
        .payment-radio:checked + div {
            border-color: #0f172a;
            background-color: #f0f9ff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .payment-radio:checked + div .check-icon { opacity: 1; transform: scale(1); }
        
        /* Disabled Payment */
        .payment-disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background-color: #f9fafb;
        }
        .payment-disabled:hover { border-color: #e2e8f0; }

        /* Order Button */
        .order-btn {
            background: linear-gradient(135deg, #0f172a 0%, #ff758c 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .order-btn:hover {
            box-shadow: 0 15px 30px -5px rgba(239, 71, 111, 0.5);
            transform: translateY(-2px) scale(1.02);
        }
        .order-btn:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        /* Checkout page header positioning - nằm dưới header chính */
        body {
            padding-top: 96px !important; /* 80px (main header) + 16px (checkout header) */
        }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Voucher Card Animation */
        .voucher-item {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .voucher-item:hover {
            transform: translateY(-2px);
        }
        .voucher-item:active {
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-700 font-sans pb-24">

    <!-- HEADER (Checkout Mode) -->
    <nav class="fixed w-full z-40 top-20 bg-white border-b border-gray-100 h-16 flex items-center shadow-sm">
        <div class="container mx-auto px-4 flex justify-between items-center max-w-6xl">
            <!-- Logo -->
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-2 group">
                <i class="fas fa-leaf text-accent-600 text-xl group-hover:rotate-12 transition-transform"></i>
                <span class="text-xl font-sans font-black text-primary-900 tracking-tight">Care<span class="text-accent-600">MIL</span></span>
            </a>
            
            <!-- Progress Stepper -->
            <div class="flex items-center gap-2 md:gap-4 lg:gap-8">
                <!-- Step 1: Cart (Completed) -->
                <div class="step completed flex items-center gap-2 hidden sm:flex">
                    <div class="step-circle w-6 h-6 md:w-8 md:h-8 rounded-full border-2 flex items-center justify-center font-bold text-xs md:text-sm"><i class="fas fa-check"></i></div>
                    <span class="step-text text-xs md:text-sm font-bold text-green-500">Giỏ Hàng</span>
                </div>
                <div class="w-8 md:w-12 h-0.5 bg-green-500 hidden sm:block"></div>
                
                <!-- Step 2: Info & Payment (Active) -->
                <div class="step active flex items-center gap-2">
                    <div class="step-circle w-6 h-6 md:w-8 md:h-8 rounded-full border-2 flex items-center justify-center font-bold text-xs md:text-sm">2</div>
                    <span class="step-text text-xs md:text-sm">Thông Tin & Thanh Toán</span>
                </div>
                <div class="w-8 md:w-12 h-0.5 bg-gray-200 hidden sm:block"></div>

                <!-- Step 3: Done -->
                <div class="step flex items-center gap-2 opacity-40 hidden sm:flex">
                    <div class="step-circle w-6 h-6 md:w-8 md:h-8 rounded-full border-2 border-gray-300 flex items-center justify-center font-bold text-xs md:text-sm">3</div>
                    <span class="step-text text-xs md:text-sm">Hoàn Tất</span>
                </div>
            </div>

            <!-- Secure Badge -->
            <div class="text-green-600 flex items-center gap-1 text-xs font-bold bg-green-50 px-3 py-1 rounded-full border border-green-100">
                <i class="fas fa-lock"></i> <span class="hidden sm:inline">Bảo Mật 100%</span>
            </div>
        </div>
    </nav>

    <!-- MAIN CHECKOUT SECTION -->
    <div class="container mx-auto px-4 max-w-6xl mt-32">
        <form id="checkout-form" onsubmit="event.preventDefault(); submitOrder();">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
                
                <!-- LEFT COLUMN: INFORMATION & PAYMENT -->
                <div class="lg:w-2/3 space-y-8">
                    
                    <!-- 1. Shipping Information -->
                    <div class="bg-white rounded-xl p-6 md:p-8 shadow-sm border border-gray-100 relative overflow-hidden" id="shipping-section">
                        <!-- Loading Overlay -->
                        <div id="address-loading" class="absolute inset-0 bg-white/80 z-20 hidden flex items-center justify-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-circle-notch fa-spin text-3xl text-accent-600 mb-2"></i>
                                <span class="text-sm font-bold text-primary-900">Đang đồng bộ địa chỉ...</span>
                            </div>
                        </div>

                        <div class="absolute top-0 left-0 w-1.5 h-full bg-primary-900"></div>
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-sans font-bold text-primary-900 flex items-center gap-2">
                                <span class="bg-primary-900 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                                Thông Tin Giao Hàng
                            </h2>
                            <!-- Address Book Trigger -->
                            <div class="relative group w-full max-w-md">
                                <?php if ( $caremil_logged_in ) : ?>
                                    <label class="form-label mb-1">Sổ địa chỉ</label>
                                    <select id="saved-address" class="form-input py-2 pl-3 pr-8 text-sm border-accent-600/30 bg-blue-50/50 cursor-pointer text-primary-900 hover:bg-blue-50 transition w-full" onchange="handleSavedAddressChange(this.value)">
                                        <option value="">-- Chọn địa chỉ đã lưu --</option>
                                        <?php foreach ( $caremil_addresses as $idx => $addr ) : 
                                            // IDs now guaranteed by PHP normalization above
                                            $aid  = $addr['id'];
                                            $full = isset( $addr['full_address'] ) ? $addr['full_address'] : ( $addr['address'] ?? '' );
                                            $name = isset( $addr['full_name'] ) ? $addr['full_name'] : $caremil_display_name;
                                            $phone = isset( $addr['phone_number'] ) ? $addr['phone_number'] : $caremil_display_phone;
                                            $prov = isset( $addr['province_id'] ) ? $addr['province_id'] : '';
                                            $dist = isset( $addr['district_id'] ) ? $addr['district_id'] : '';
                                            $comm = isset( $addr['commune_id'] ) ? $addr['commune_id'] : '';
                                        ?>
                                            <option value="<?php echo esc_attr( $aid ); ?>"
                                                data-full="<?php echo esc_attr( $full ); ?>"
                                                data-name="<?php echo esc_attr( $name ); ?>"
                                                data-phone="<?php echo esc_attr( $phone ); ?>"
                                                data-province="<?php echo esc_attr( $prov ); ?>"
                                                data-district="<?php echo esc_attr( $dist ); ?>"
                                                data-commune="<?php echo esc_attr( $comm ); ?>">
                                                <?php echo esc_html( $full ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="new">+ Nhập địa chỉ mới</option>
                                    </select>
                                <?php else : ?>
                                    <p class="text-xs text-gray-500">Đăng nhập để dùng sổ địa chỉ.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <input type="hidden" id="selected-address-id" value="">
                            <div class="md:col-span-2">
                                <label class="form-label">Họ và tên người nhận <span class="text-red-500">*</span></label>
                                <input type="text" id="fullname" class="form-input" placeholder="Ví dụ: Nguyễn Văn A" value="<?php echo esc_attr( $caremil_display_name ); ?>" required>
                            </div>
                            <div>
                                <label class="form-label">Số điện thoại <span class="text-red-500">*</span></label>
                                <input type="tel" id="phone" class="form-input" placeholder="Ví dụ: 0912345678" value="<?php echo esc_attr( $caremil_display_phone ); ?>" required>
                            </div>
                            <div>
                                <label class="form-label">Email (Nhận hóa đơn)</label>
                                <input type="email" id="email" class="form-input" placeholder="example@email.com">
                            </div>
                            
                            <!-- Address Group (Tỉnh/Thành + Phường/Xã theo chuẩn mới) -->
                            <input type="hidden" id="checkout_province_id" value="">
                            <input type="hidden" id="checkout_district_id" value="">
                            <input type="hidden" id="checkout_district_name" value="">
                            <input type="hidden" id="checkout_commune_id" value="">

                            <div class="relative">
                                <label class="form-label flex items-center justify-between">
                                    <span>Tỉnh / Thành phố <span class="text-red-500">*</span></span>
                                    <span class="text-[11px] font-normal text-accent-600">Gõ để tìm nhanh</span>
                                </label>
                                <input
                                    type="text"
                                    id="checkout_province_search"
                                    class="form-input text-sm"
                                    placeholder="Ví dụ: Hồ Chí Minh, Hà Nội..."
                                    autocomplete="off"
                                    required
                                >
                                <div id="checkout_province_suggestions" class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden text-sm"></div>
                            </div>

                            <div class="relative">
                                <label class="form-label flex items-center justify-between">
                                    <span>Quận / Huyện <span class="text-red-500">*</span></span>
                                    <span class="text-[11px] font-normal text-accent-600">Chọn theo tỉnh</span>
                                </label>
                                <input
                                    type="text"
                                    id="checkout_district_search"
                                    class="form-input text-sm"
                                    placeholder="Ví dụ: Quận 1, Huyện Bình Chánh..."
                                    autocomplete="off"
                                    required
                                >
                                <div id="checkout_district_suggestions" class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden text-sm"></div>
                            </div>

                            <div class="relative">
                                <label class="form-label flex items-center justify-between">
                                    <span>Phường / Xã <span class="text-red-500">*</span></span>
                                    <span class="text-[11px] font-normal text-accent-600">Chọn theo quận/huyện</span>
                                </label>
                                <input
                                    type="text"
                                    id="checkout_commune_search"
                                    class="form-input text-sm"
                                    placeholder="Ví dụ: Phường Bến Nghé..."
                                    autocomplete="off"
                                    required
                                >
                                <div id="checkout_commune_suggestions" class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden text-sm"></div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="form-label">Địa chỉ chi tiết <span class="text-red-500">*</span></label>
                                <input type="text" id="address" class="form-input" placeholder="Số nhà, tên đường (hệ thống sẽ tự ghép với phường/xã, tỉnh/thành)" required>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="form-label">Ghi chú giao hàng</label>
                                <textarea class="form-input h-24 resize-none" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Payment Method -->
                    <div class="bg-white rounded-xl p-6 md:p-8 shadow-sm border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-accent-600"></div>
                        <h2 class="text-xl font-sans font-bold text-primary-900 mb-6 flex items-center gap-2">
                            <span class="bg-accent-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                            Phương Thức Thanh Toán
                        </h2>

                        <div class="space-y-4">
                            
                            <!-- 1. QR Code Transfer - RECOMMENDED & DEFAULT -->
                            <label class="block cursor-pointer relative group">
                                <input type="radio" name="payment" value="bank" class="payment-radio sr-only" checked>
                                <div class="p-5 rounded-2xl border-2 border-gray-200 flex items-center gap-4 hover:border-accent-600 transition bg-white relative overflow-hidden">
                                    <!-- Recommended Badge -->
                                    <div class="absolute -right-8 top-4 bg-primary-900 text-white text-[10px] font-bold px-8 py-1 rotate-45 shadow-sm">Khuyên Dùng</div>
                                    
                                    <div class="w-12 h-12 bg-blue-100 text-accent-600 rounded-full flex items-center justify-center text-2xl flex-shrink-0">
                                        <i class="fas fa-qrcode"></i>
                                    </div>
                                    <div class="flex-grow pr-10">
                                        <h4 class="font-bold text-primary-900 text-lg">Chuyển khoản QR Code</h4>
                                        <p class="text-xs text-gray-500">Quét mã QR ngân hàng, xác nhận nhanh chóng.</p>
                                        <div class="flex gap-2 mt-2">
                                            <span class="text-[10px] bg-blue-50 text-accent-600 px-2 py-0.5 rounded border border-blue-100">Nhanh & An toàn</span>
                                        </div>
                                    </div>
                                    <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                        <div class="w-3 h-3 rounded-full bg-primary-900 check-icon opacity-0 transition-all transform scale-0"></div>
                                    </div>
                                </div>
                            </label>

                            <!-- 2. COD -->
                            <label class="block cursor-pointer relative group">
                                <input type="radio" name="payment" value="cod" class="payment-radio sr-only">
                                <div class="p-5 rounded-2xl border-2 border-gray-200 flex items-center gap-4 hover:border-accent-600 transition bg-white">
                                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-2xl flex-shrink-0">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="flex-grow">
                                        <h4 class="font-bold text-primary-900 text-lg">Thanh toán khi nhận hàng (COD)</h4>
                                        <p class="text-xs text-gray-500">Thanh toán tiền mặt cho shipper khi nhận được hàng.</p>
                                    </div>
                                    <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                        <div class="w-3 h-3 rounded-full bg-primary-900 check-icon opacity-0 transition-all transform scale-0"></div>
                                    </div>
                                </div>
                            </label>

                            <!-- 3. Other Methods (Disabled / Coming Soon) -->
                            <div class="relative">
                                <div class="p-5 rounded-2xl border-2 border-gray-100 flex flex-col gap-4 bg-gray-50 opacity-70 cursor-not-allowed">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold text-gray-500 text-sm uppercase tracking-wide flex items-center gap-2">
                                            <i class="fas fa-tools"></i> Các phương thức đang cập nhật
                                        </h4>
                                        <span class="bg-gray-200 text-gray-500 text-[10px] font-bold px-2 py-1 rounded">Bảo trì</span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <!-- VietQR (Added here) -->
                                        <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200 grayscale opacity-60">
                                            <img src="https://img.icons8.com/color/48/vietqr.png" class="w-8 h-8 object-contain rounded" alt="VietQR">
                                            <div>
                                                <p class="font-bold text-sm text-gray-600">VietQR</p>
                                                <p class="text-[10px] text-gray-400">Đang bảo trì</p>
                                            </div>
                                        </div>

                                        <!-- E-Wallets -->
                                        <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200 grayscale opacity-60">
                                            <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" class="w-8 h-8 object-contain rounded" alt="Momo">
                                            <div>
                                                <p class="font-bold text-sm text-gray-600">Ví MoMo</p>
                                                <p class="text-[10px] text-gray-400">Đang bảo trì</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200 grayscale opacity-60">
                                            <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-ZaloPay-Square.png" class="w-8 h-8 object-contain rounded" alt="ZaloPay">
                                            <div>
                                                <p class="font-bold text-sm text-gray-600">ZaloPay</p>
                                                <p class="text-[10px] text-gray-400">Đang bảo trì</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Cards -->
                                        <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200 grayscale opacity-60">
                                            <div class="flex gap-1">
                                                <i class="fab fa-cc-visa text-2xl text-gray-400"></i>
                                                <i class="fab fa-cc-mastercard text-2xl text-gray-400"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-sm text-gray-600">Thẻ Visa/Master</p>
                                                <p class="text-[10px] text-gray-400">Sắp ra mắt</p>
                                            </div>
                                        </div>

                                        <!-- Mobile Pay (Apple/Samsung/Google) -->
                                        <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200 grayscale opacity-60 md:col-span-2">
                                            <div class="flex gap-3 text-xl text-gray-400">
                                                <i class="fab fa-apple"></i>
                                                <i class="fab fa-google"></i>
                                                <span class="font-bold text-xs border border-gray-300 px-1 rounded">Pay</span> <!-- Samsung Pay icon placeholder -->
                                            </div>
                                            <div>
                                                <p class="font-bold text-sm text-gray-600">Apple / Samsung / Google Pay</p>
                                                <p class="text-[10px] text-gray-400">Sắp ra mắt</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: ORDER SUMMARY (Sticky) -->
                <div class="lg:w-1/3">
                    <div class="sticky top-40 bg-white rounded-xl p-6 md:p-8 shadow-card border border-gray-100">
                        <h3 class="text-lg font-bold text-primary-900 mb-6 pb-4 border-b border-gray-100 flex justify-between items-center">
                            Đơn Hàng (<?php echo esc_html( caremil_get_cart_count() ); ?>)
                            <a href="<?php echo esc_url( home_url( '/gio-hang' ) ); ?>" class="text-xs text-accent-600 hover:underline font-normal">Sửa</a>
                        </h3>

                        <!-- Mini Cart List -->
                        <div class="space-y-4 mb-6 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                            <?php
                            $cart = caremil_get_cart();
                            $item_num = 0;
                            
                            if ( empty( $cart ) ) : ?>
                                <p class="text-sm text-gray-500 text-center py-4">Giỏ hàng trống</p>
                            <?php else :
                                foreach ( $cart as $cart_key => $item ) :
                                    $item_num++;
                                    $product_id = isset( $item['product_id'] ) ? intval( $item['product_id'] ) : 0;
                                    $quantity = isset( $item['quantity'] ) ? intval( $item['quantity'] ) : 1;
                                    $variant_label = isset( $item['variant_label'] ) ? $item['variant_label'] : '';
                                    $price = isset( $item['price'] ) ? $item['price'] : '0';
                                    $image = isset( $item['image'] ) ? $item['image'] : '';
                                    
                                    // Lấy thông tin sản phẩm từ database nếu cần
                                    if ( $product_id > 0 ) {
                                        $product = get_post( $product_id );
                                        if ( $product ) {
                                            $product_title = get_the_title( $product_id );
                                            if ( empty( $image ) ) {
                                                $image = get_the_post_thumbnail_url( $product_id, 'medium' );
                                            }
                                            if ( empty( $variant_label ) ) {
                                                $variant_label = $product_title;
                                            }
                                        } else {
                                            $product_title = 'Sản phẩm không tồn tại';
                                        }
                                    } else {
                                        $product_title = isset( $item['product_name'] ) ? $item['product_name'] : 'Sản phẩm';
                                    }
                            ?>
                            <div class="flex gap-3">
                                <div class="w-16 h-16 bg-gray-50 rounded-lg p-1 border border-gray-200 flex-shrink-0 relative">
                                    <?php if ( $image ) : ?>
                                        <img src="<?php echo esc_url( $image ); ?>" class="w-full h-full object-contain" alt="<?php echo esc_attr( $product_title ); ?>">
                                    <?php else : ?>
                                        <i class="fas fa-box text-2xl text-gray-300 w-full h-full flex items-center justify-center"></i>
                                    <?php endif; ?>
                                    <span class="absolute -top-2 -right-2 bg-gray-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border border-white"><?php echo esc_html( $quantity ); ?></span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-primary-900 line-clamp-2"><?php echo esc_html( $product_title ); ?></p>
                                    <?php if ( ! empty( $variant_label ) ) : ?>
                                        <p class="text-xs text-gray-500 mb-1"><?php echo esc_html( $variant_label ); ?></p>
                                    <?php endif; ?>
                                    <p class="text-sm font-bold text-gray-700"><?php echo esc_html( $price ); ?></p>
                                </div>
                            </div>
                            <?php 
                                endforeach;
                            endif; 
                            ?>
                        </div>


                        <!-- Available Coupons Grid -->
                        <?php
                        $available_coupons = caremil_get_available_coupons();
                        if (!empty($available_coupons)):
                        ?>
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-primary-900 mb-3 flex items-center gap-2">
                                <i class="fas fa-ticket-alt text-accent-600"></i>
                                Mã Giảm Giá Khả Dụng
                            </h4>
                            <div class="grid grid-cols-1 gap-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                                <?php foreach ($available_coupons as $coupon):
                                    $code = $coupon->post_title;
                                    $type = get_post_meta($coupon->ID, '_coupon_type', true);
                                    $amount = get_post_meta($coupon->ID, '_coupon_amount', true);
                                    $min_order = get_post_meta($coupon->ID, '_coupon_min_order', true);
                                    $expiry = get_post_meta($coupon->ID, '_coupon_expiry', true);
                                    $desc = get_post_meta($coupon->ID, '_coupon_description', true);
                                    
                                    // Convert to number and handle different types
                                    $amount_num = floatval($amount);
                                    
                                    if ($type === 'freeship') {
                                        $amount_display = 'FREE';
                                        $color_class = 'from-purple-500 to-purple-600';
                                        $border_color = 'border-purple-200';
                                        $bg_light = 'bg-purple-50';
                                        $text_color = 'text-purple-600';
                                    } elseif ($type === 'percent') {
                                        $amount_display = $amount_num . '%';
                                        $color_class = 'from-blue-500 to-blue-600';
                                        $border_color = 'border-blue-200';
                                        $bg_light = 'bg-blue-50';
                                        $text_color = 'text-blue-600';
                                    } else {
                                        $amount_display = number_format($amount_num/1000) . 'k';
                                        $color_class = 'from-green-500 to-green-600';
                                        $border_color = 'border-green-200';
                                        $bg_light = 'bg-green-50';
                                        $text_color = 'text-green-600';
                                    }
                                ?>
                                <div class="voucher-item bg-white rounded-lg border <?php echo $border_color; ?> shadow-sm hover:shadow-md transition cursor-pointer group"
                                     onclick="applyVoucher('<?php echo esc_js($code); ?>')">
                                    <div class="flex items-center gap-3 p-3">
                                        <!-- Icon Badge -->
                                        <div class="w-14 h-14 bg-gradient-to-br <?php echo $color_class; ?> rounded-lg flex flex-col items-center justify-center text-white shadow-sm flex-shrink-0">
                                            <span class="text-xl font-black"><?php echo esc_html($amount_display); ?></span>
                                            <span class="text-[8px] uppercase font-bold tracking-widest">OFF</span>
                                        </div>
                                        
                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-primary-900 text-xs line-clamp-1"><?php echo esc_html($desc ? $desc : $code); ?></p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[9px] <?php echo $bg_light . ' ' . $text_color; ?> px-2 py-0.5 rounded border border-opacity-20 font-mono font-bold">
                                                    <?php echo esc_html($code); ?>
                                                </span>
                                                <?php if ($expiry): ?>
                                                <span class="text-[9px] text-gray-400">
                                                    <i class="far fa-clock"></i> <?php echo date('d/m/Y', strtotime($expiry)); ?>
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($min_order > 0): ?>
                                            <p class="text-[9px] text-gray-500 mt-1">Đơn tối thiểu: <?php echo number_format($min_order); ?>đ</p>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Apply Button -->
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full bg-accent-600 flex items-center justify-center group-hover:scale-110 transition shadow">
                                                <i class="fas fa-plus text-white text-sm"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="mt-3 pt-3 border-t border-dashed border-gray-200">
                                <p class="text-xs text-gray-500 text-center">
                                    <i class="fas fa-info-circle"></i> Nhấn vào mã để áp dụng nhanh
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Manual Coupon Input -->
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-primary-900 mb-3 flex items-center gap-2">
                                <i class="fas fa-keyboard text-gray-500"></i>
                                Hoặc Nhập Mã Thủ Công
                            </h4>
                             <div class="flex gap-2">
                                <input type="text" id="coupon-code" placeholder="Nhập mã giảm giá" class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-accent-600 uppercase font-bold text-primary-900" style="text-transform: uppercase;">
                                <button type="button" onclick="applyCoupon()" id="btn-apply-coupon" class="bg-gray-200 text-gray-600 font-bold text-sm px-4 rounded-lg hover:bg-gray-300 transition">
                                    Áp dụng
                                </button>
                            </div>
                            <div id="coupon-message" class="text-xs mt-2 hidden"></div>
                        </div>

                        <!-- Calculations -->
                        <?php
                        $subtotal = caremil_get_cart_total();
                        
                        // Initialize and normalize coupon session structure
                        if (!isset($_SESSION['caremil_applied_coupons']) || !is_array($_SESSION['caremil_applied_coupons'])) {
                            $_SESSION['caremil_applied_coupons'] = ['shipping' => null, 'order' => []];
                        }
                        
                        $applied_coupons = $_SESSION['caremil_applied_coupons'];
                        
                        // Validate structure (migration from old format)
                        if (!isset($applied_coupons['shipping'])) {
                            $applied_coupons['shipping'] = null;
                        }
                        if (!isset($applied_coupons['order']) || !is_array($applied_coupons['order'])) {
                            $applied_coupons['order'] = [];
                        }
                        
                        require_once get_template_directory() . '/includes/coupons.php';
                        
                        // Calculate total order discounts (can have multiple)
                        $total_order_discount = 0;
                        if (!empty($applied_coupons['order']) && is_array($applied_coupons['order'])) {
                            foreach ($applied_coupons['order'] as $coupon_data) {
                                if (isset($coupon_data['discount'])) {
                                    $total_order_discount += floatval($coupon_data['discount']);
                                }
                            }
                        }
                        
                        // Calculate shipping fee via Viettel Post API
                        $shipping = 0; // Start with 0
                        $shipping_info = ['service' => 'Viettel Post', 'time' => 'Đang cập nhật'];
                        $has_address = false; // Flag to check if address is available
                        
                        // Try to get shipping info from customer data if available
                        if (isset($_SESSION['checkout_customer_data'])) {
                            $customer_data_temp = $_SESSION['checkout_customer_data'];
                            if (!empty($customer_data_temp['province_id']) && !empty($customer_data_temp['district_id'])) {
                                $has_address = true;
                                $is_cod = !isset($_SESSION['payment_completed']) || $_SESSION['payment_completed'] !== true;
                                
                                $shipping_info = caremil_get_shipping_info($customer_data_temp, $is_cod, $subtotal - $total_order_discount);
                                $shipping = $shipping_info['fee'];
                            }
                        }
                        
                        // Apply freeship coupon (overrides calculated fee)
                        $has_freeship = (isset($applied_coupons['shipping']) && $applied_coupons['shipping'] !== null);
                        if ($has_freeship) {
                            $original_shipping = $shipping; // Store for display
                            $shipping = 0;
                        }
                        
                        $total = $subtotal - $total_order_discount + $shipping;
                        if ($total < 0) $total = 0;
                        ?>
                        <div class="space-y-3 text-sm border-t border-dashed border-gray-200 pt-4 mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>Tạm tính</span>
                                <span id="checkout-subtotal"><?php echo esc_html( caremil_format_price( $subtotal ) ); ?></span>
                            </div>
                            
                            <!-- Order Discounts (can have multiple) -->
                            <?php if (!empty($applied_coupons['order']) && is_array($applied_coupons['order'])): ?>
                                <?php foreach ($applied_coupons['order'] as $coupon_data): ?>
                                    <?php if (isset($coupon_data['code']) && isset($coupon_data['discount'])): ?>
                                    <div class="flex justify-between text-gray-600">
                                        <span class="flex items-center gap-2">
                                            <i class="fas fa-tag text-green-500 text-xs"></i>
                                            <span class="text-xs font-bold bg-green-100 text-green-700 px-2 py-0.5 rounded">
                                                <?php echo esc_html(strtoupper($coupon_data['code'])); ?>
                                                <a href="#" onclick="event.preventDefault(); removeCouponByCode('<?php echo esc_js($coupon_data['code']); ?>')" 
                                                   class="text-red-500 hover:text-red-700 ml-1" title="Gỡ mã">×</a>
                                            </span>
                                        </span>
                                        <span class="text-green-500">-<?php echo esc_html( caremil_format_price( $coupon_data['discount'] ) ); ?></span>
                                    </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <div class="flex justify-between text-gray-600">
                                <span>Phí vận chuyển
                                    <?php if ($has_freeship && isset($applied_coupons['shipping']['code'])): ?>
                                    <span class="text-xs font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded ml-1">
                                        <?php echo esc_html(strtoupper($applied_coupons['shipping']['code'])); ?>
                                        <a href="#" onclick="event.preventDefault(); removeCouponByCategory('shipping')" 
                                           class="text-red-500 hover:text-red-700 ml-1" title="Gỡ mã">×</a>
                                    </span>
                                    <?php endif; ?>
                                </span>
                                <span class="font-bold <?php echo $shipping > 0 ? 'text-gray-700' : ($has_address ? 'text-green-500' : 'text-gray-400'); ?>" id="checkout-shipping">
                                    <?php 
                                    if (!$has_address) {
                                        echo '<span class="text-xs italic">Nhập địa chỉ để tính phí</span>';
                                    } else {
                                        echo $shipping > 0 ? esc_html( caremil_format_price( $shipping ) ) : 'Miễn phí'; 
                                    }
                                    ?>
                                </span>
                            </div>
                            <?php if ( $shipping > 0 && $has_address ): ?>
                            <div class="flex items-center gap-2 text-xs pl-4 text-gray-500">
                                <span class="text-base">📮</span>
                                <span><?php echo esc_html($shipping_info['service']); ?> (<?php echo esc_html($shipping_info['time']); ?>)</span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Total -->
                        <div class="flex justify-between items-center mb-8 pt-4 border-t border-gray-100">
                            <span class="text-base font-bold text-primary-900">Tổng thanh toán</span>
                            <div class="text-right">
                                <span class="text-2xl font-black text-primary-900 block leading-none" id="checkout-total"><?php echo esc_html( caremil_format_price( $total ) ); ?></span>
                                <span class="text-[10px] text-gray-400">(VAT included)</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="order-btn w-full text-white font-bold py-4 rounded-xl shadow-lg flex items-center justify-center gap-2 text-lg uppercase tracking-wide group">
                            <span>Đặt Hàng Ngay</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>

                        <p class="text-center text-xs text-gray-400 mt-4 px-4">
                            Bằng việc đặt hàng, bạn đồng ý với <a href="#" class="underline hover:text-accent-600">điều khoản sử dụng</a> của CareMIL.
                        </p>
                        
                        <!-- Shipping Debug Panel -->
                        <?php 
                        $cart_weight = caremil_calculate_cart_weight();
                        $shop_address = "Hà Nội - Cầu Giấy"; // From constants
                        ?>
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl text-xs">
                            <h4 class="font-bold text-blue-900 mb-3 flex items-center gap-2">
                                <i class="fas fa-info-circle"></i>
                                Thông Tin Vận Chuyển (Debug)
                            </h4>
                            <div class="space-y-2 text-blue-800">
                                <div class="flex justify-between">
                                    <span class="font-medium">📦 Cân nặng giỏ hàng:</span>
                                    <span class="font-mono font-bold"><?php echo number_format($cart_weight); ?>g</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">🏪 Địa chỉ shop (gửi):</span>
                                    <span class="font-mono"><?php echo esc_html($shop_address); ?></span>
                                </div>
                                <?php if ($has_address && isset($_SESSION['checkout_customer_data'])): ?>
                                <div class="flex justify-between">
                                    <span class="font-medium">🏠 Địa chỉ khách (nhận):</span>
                                    <span class="font-mono">Province: <?php echo esc_html($_SESSION['checkout_customer_data']['province_id'] ?? 'N/A'); ?>, District: <?php echo esc_html($_SESSION['checkout_customer_data']['district_id'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">💰 Phí vận chuyển tính toán:</span>
                                    <span class="font-mono font-bold text-green-700"><?php echo number_format($shipping); ?>đ</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">🚚 Dịch vụ:</span>
                                    <span class="font-mono"><?php echo esc_html($shipping_info['service'] ?? 'N/A'); ?> - <?php echo esc_html($shipping_info['time'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">📡 Data source:</span>
                                    <span class="font-mono <?php echo isset($shipping_info['source']) && $shipping_info['source'] === 'api' ? 'text-green-700' : 'text-orange-600'; ?>">
                                        <?php echo esc_html(strtoupper($shipping_info['source'] ?? 'unknown')); ?>
                                    </span>
                                </div>
                                <?php else: ?>
                                <div class="text-center py-2 text-gray-500 italic">
                                    Chưa nhập địa chỉ giao hàng
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Debug Area -->
                        <div id="order-error-debug" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-xs font-mono text-red-700 overflow-x-auto">
                            <h4 class="font-bold mb-2">Technical Details:</h4>
                            <pre id="order-error-content" class="whitespace-pre-wrap"></pre>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-[30px] shadow-2xl max-w-md w-full p-8 text-center relative border-4 border-green-100 transform scale-90 transition-all duration-300" id="success-content">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-4xl text-green-500 animate-bounce"></i>
            </div>
            <h3 class="text-2xl font-sans font-black text-primary-900 mb-2">Đặt Hàng Thành Công!</h3>
            <p class="text-gray-600 mb-8">Cảm ơn bạn đã tin chọn CareMIL. Chúng tôi sẽ liên hệ xác nhận đơn hàng trong giây lát.</p>
            <button onclick="window.location.href='<?php echo esc_js( home_url( '/' ) ); ?>'" class="w-full bg-primary-900 text-white font-bold py-3 rounded-xl hover:bg-accent-600 transition shadow-lg">
                Về Trang Chủ
            </button>
        </div>
    </div>

    <script>
        // Cart AJAX configuration
        const cartConfig = {
            ajaxUrl: '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>',
            nonce: '<?php echo esc_js( wp_create_nonce( 'caremil_cart_nonce' ) ); ?>'
        };

        const caremilCheckout = {
            loggedIn: <?php echo $caremil_logged_in ? 'true' : 'false'; ?>,
            addresses: <?php echo wp_json_encode( $caremil_addresses ); ?>,
            ajaxUrl: '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>',
            nonce: '<?php echo esc_js( $caremil_addr_nonce ); ?>',
            createOrderNonce: '<?php echo esc_js( wp_create_nonce( 'caremil_create_order_nonce' ) ); ?>',
            displayName: '<?php echo esc_js( $caremil_display_name ); ?>',
            displayPhone: '<?php echo esc_js( $caremil_display_phone ); ?>'
        };
        let savedAddresses = Array.isArray(caremilCheckout.addresses) ? caremilCheckout.addresses : [];

        // Function để xóa toàn bộ giỏ hàng
        function emptyCart() {
            return fetch(cartConfig.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'caremil_empty_cart',
                    nonce: cartConfig.nonce
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật header cart count
                    const headerCount = document.getElementById('header-cart-count');
                    if (headerCount) {
                        headerCount.textContent = '0';
                        headerCount.style.display = 'none';
                    }
                    return true;
                }
                return false;
            })
            .catch(error => {
                console.error('Error emptying cart:', error);
                return false;
            });
        }

        // Geo API cache (Tỉnh/Thành -> Quận/Huyện -> Phường/Xã)
        const geoCache = {
            provinces: [],
            districtsByProvince: {}, // key: province_id -> list districts
            communesByDistrict: {}   // key: district_id -> list communes
        };

        async function geoFetch(url) {
            try {
                const res = await fetch(url);
                if (!res.ok) throw new Error('Geo API error');
                return await res.json();
            } catch (e) {
                console.error('Geo Fetch Error:', e);
                return { data: [] };
            }
        }

        function sortByName(list) {
            return (list || []).slice().sort((a, b) => {
                const na = (a.name || '').toString();
                const nb = (b.name || '').toString();
                return na.localeCompare(nb, 'vi', { sensitivity: 'base' });
            });
        }

        async function loadProvinces() {
            if (geoCache.provinces.length) return geoCache.provinces;
            const data = await geoFetch('https://pos.pages.fm/api/v1/geo/provinces?country_code=84');
            geoCache.provinces = sortByName(data.data || []);
            return geoCache.provinces;
        }

        async function loadDistricts(provinceId) {
            if (geoCache.districtsByProvince[provinceId]) return geoCache.districtsByProvince[provinceId];
            const data = await geoFetch(`https://pos.pages.fm/api/v1/geo/districts?province_id=${provinceId}`);
            geoCache.districtsByProvince[provinceId] = sortByName(data.data || []);
            return geoCache.districtsByProvince[provinceId];
        }

        async function loadCommunes(districtId) {
            if (geoCache.communesByDistrict[districtId]) return geoCache.communesByDistrict[districtId];
            const data = await geoFetch(`https://pos.pages.fm/api/v1/geo/communes?district_id=${districtId}`);
            geoCache.communesByDistrict[districtId] = sortByName(data.data || []);
            return geoCache.communesByDistrict[districtId];
        }

        function resetDistrict() {
            const hid = document.getElementById('checkout_district_id');
            const hname = document.getElementById('checkout_district_name');
            const search = document.getElementById('checkout_district_search');
            const sugg = document.getElementById('checkout_district_suggestions');
            if(hid) hid.value = '';
            if(hname) hname.value = '';
            if(search) search.value = '';
            if(sugg) sugg.innerHTML = '';
        }

        function resetCommune() {
            const hid = document.getElementById('checkout_commune_id');
            const search = document.getElementById('checkout_commune_search');
            const sugg = document.getElementById('checkout_commune_suggestions');
            if(hid) hid.value = '';
            if(search) search.value = '';
            if(sugg) sugg.innerHTML = '';
        }

        async function ensureProvinces(selectedProvId = '', selectedDistId = '', selectedCommId = '') {
            const provinces = await loadProvinces();
            const hiddenProv = document.getElementById('checkout_province_id');
            const searchProv = document.getElementById('checkout_province_search');
            const suggestProv = document.getElementById('checkout_province_suggestions');
            
            if (!hiddenProv || !searchProv || !suggestProv) return;

            function renderProvSuggestions(keyword = '') {
                const kw = keyword.trim().toLowerCase();
                suggestProv.innerHTML = '';
                let filtered = provinces;
                if (kw) {
                    filtered = provinces.filter(p => (p.name || '').toLowerCase().includes(kw));
                }
                filtered.slice(0, 50).forEach(p => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'w-full text-left px-3 py-2 hover:bg-blue-50 text-sm';
                    btn.textContent = p.name || '';
                    btn.addEventListener('click', async () => {
                        hiddenProv.value = p.id || '';
                        searchProv.value = p.name || '';
                        suggestProv.classList.add('hidden');
                        
                        resetDistrict();
                        resetCommune();
                        
                        await prepareDistrictsForProvince(p.id);
                    });
                    suggestProv.appendChild(btn);
                });
                suggestProv.classList.toggle('hidden', filtered.length === 0);
            }

            if (!searchProv.__caremilProvBound) {
                searchProv.addEventListener('input', () => renderProvSuggestions(searchProv.value));
                searchProv.addEventListener('focus', () => {
                    if(!searchProv.value) renderProvSuggestions('');
                    else renderProvSuggestions(searchProv.value);
                });
                document.addEventListener('click', (e) => {
                    if (!suggestProv.contains(e.target) && e.target !== searchProv) suggestProv.classList.add('hidden');
                });
                searchProv.__caremilProvBound = true;
            }

            // Edit/Init
            if (selectedProvId) {
                const found = provinces.find(p => String(p.id) === String(selectedProvId));
                if (found) {
                    hiddenProv.value = found.id || '';
                    searchProv.value = found.name || '';
                    await prepareDistrictsForProvince(found.id, selectedDistId, selectedCommId);
                }
            }
        }

        async function prepareDistrictsForProvince(provinceId, selectedDistId = '', selectedCommId = '') {
            if (!provinceId) return;
            const districts = await loadDistricts(provinceId);
            
            const hiddenDist = document.getElementById('checkout_district_id');
            const hiddenName = document.getElementById('checkout_district_name'); // We added this hidden input
            const searchDist = document.getElementById('checkout_district_search'); // We added this input
            const suggestDist = document.getElementById('checkout_district_suggestions'); // We added this div

            if (!hiddenDist || !searchDist || !suggestDist) return;

            function renderDistSuggestions(keyword = '') {
                const kw = keyword.trim().toLowerCase();
                suggestDist.innerHTML = '';
                let filtered = districts;
                if (kw) filtered = districts.filter(d => (d.name || '').toLowerCase().includes(kw));
                
                filtered.slice(0, 50).forEach(d => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'w-full text-left px-3 py-2 hover:bg-blue-50 text-sm';
                    btn.textContent = d.name || '';
                    btn.addEventListener('click', async () => {
                        hiddenDist.value = d.id || '';
                        if(hiddenName) hiddenName.value = d.name || '';
                        searchDist.value = d.name || '';
                        suggestDist.classList.add('hidden');
                        
                        resetCommune();
                        await prepareCommunesForDistrict(d.id);
                    });
                    suggestDist.appendChild(btn);
                });
                suggestDist.classList.toggle('hidden', filtered.length === 0);
            }

            if (!searchDist.__caremilDistBound) {
                 searchDist.addEventListener('input', () => renderDistSuggestions(searchDist.value));
                 searchDist.addEventListener('focus', () => {
                    if(!searchDist.value) renderDistSuggestions('');
                    else renderDistSuggestions(searchDist.value);
                 });
                 document.addEventListener('click', (e) => {
                    if (!suggestDist.contains(e.target) && e.target !== searchDist) suggestDist.classList.add('hidden');
                 });
                 searchDist.__caremilDistBound = true;
            }

            if (selectedDistId) {
                const found = districts.find(d => String(d.id) === String(selectedDistId));
                if (found) {
                    hiddenDist.value = found.id;
                    if(hiddenName) hiddenName.value = found.name;
                    searchDist.value = found.name;
                    await prepareCommunesForDistrict(found.id, selectedCommId);
                }
            }
        }

        async function prepareCommunesForDistrict(districtId, selectedCommId = '') {
            if (!districtId) return;
            const communes = await loadCommunes(districtId);
            
            const hiddenComm = document.getElementById('checkout_commune_id');
            const searchComm = document.getElementById('checkout_commune_search');
            const suggestComm = document.getElementById('checkout_commune_suggestions');

            if (!hiddenComm || !searchComm || !suggestComm) return;

            function renderCommSuggestions(keyword = '') {
                const kw = keyword.trim().toLowerCase();
                suggestComm.innerHTML = '';
                let filtered = communes;
                if (kw) filtered = communes.filter(c => (c.name || '').toLowerCase().includes(kw));
                
                filtered.slice(0, 50).forEach(c => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'w-full text-left px-3 py-2 hover:bg-blue-50 text-sm flex flex-col';
                    btn.innerHTML = `<span class="font-semibold text-primary-900">${c.name || ''}</span>`;
                    
                    btn.addEventListener('click', () => {
                        hiddenComm.value = c.id || '';
                        searchComm.value = c.name || '';
                        suggestComm.classList.add('hidden');
                        // Address is complete now? We can trigger shipping but shipping depends on Province+District,
                        // which are already set. The observer on search inputs / hidden inputs will handle it?
                        // Wait, updateShippingFee observes what?
                        // It observes hidden province and district inputs.
                        // So changing district triggers shipping calc.
                        // changing commune triggers nothing? 
                        // Maybe fine, shipping usually is per district.
                    });
                    suggestComm.appendChild(btn);
                });
                suggestComm.classList.toggle('hidden', filtered.length === 0);
            }

            if (!searchComm.__caremilCommBound) {
                 searchComm.addEventListener('input', () => renderCommSuggestions(searchComm.value));
                 searchComm.addEventListener('focus', () => {
                    if(!searchComm.value) renderCommSuggestions('');
                    else renderCommSuggestions(searchComm.value);
                 });
                 document.addEventListener('click', (e) => {
                    if (!suggestComm.contains(e.target) && e.target !== searchComm) suggestComm.classList.add('hidden');
                 });
                 searchComm.__caremilCommBound = true;
            }

            if (selectedCommId) {
                const found = communes.find(c => String(c.id) === String(selectedCommId));
                if (found) {
                    hiddenComm.value = found.id || '';
                    searchComm.value = found.name || '';
                }
            }
        }

        function normalizeAddress(full) {
            return (full || '').toLowerCase().trim().replace(/\s+/g, ' ');
        }

        async function applyAddressToForm(addr = {}) {
            // Fill basic fields
            document.getElementById('fullname').value = addr.full_name || caremilCheckout.displayName || '';
            document.getElementById('phone').value    = addr.phone_number || caremilCheckout.displayPhone || '';
            document.getElementById('address').value  = addr.full_address || addr.address || '';

            const provinceId = addr.province_id || '';
            const communeId  = addr.commune_id || '';

            if (provinceId) {
                await ensureProvinces(provinceId, communeId);
            }
        }

        function rebuildSavedSelect() {
            const select = document.getElementById('saved-address');
            if (!select) return;
            const current = select.value;
            select.innerHTML = '<option value="">-- Chọn địa chỉ đã lưu --</option>';
            savedAddresses.forEach((addr, idx) => {
                const option = document.createElement('option');
                const id = addr.id || `addr_${idx}`;
                const full = addr.full_address || addr.address || '';
                const name = addr.full_name || caremilCheckout.displayName || '';
                const phone = addr.phone_number || caremilCheckout.displayPhone || '';
                option.value = id;
                option.dataset.full = full;
                option.dataset.name = name;
                option.dataset.phone = phone;
                option.dataset.province = addr.province_id || '';
                option.dataset.district = addr.district_id || '';
                option.dataset.commune = addr.commune_id || '';
                option.textContent = `${full}${name ? ' • ' + name : ''}${phone ? ' • ' + phone : ''}`;
                select.appendChild(option);
            });
            const newOpt = document.createElement('option');
            newOpt.value = 'new';
            newOpt.textContent = '+ Nhập địa chỉ mới';
            select.appendChild(newOpt);
            if (current) {
                select.value = current;
            }
        }

        async function handleSavedAddressChange(id) {
            const loading = document.getElementById('address-loading');
            const hidden = document.getElementById('selected-address-id');
            const select = document.getElementById('saved-address');
            
            if (hidden) hidden.value = id || '';
            if (loading) loading.classList.remove('hidden');
            if (select) select.disabled = true;

            try {
                if (!id || id === 'new') {
                    // Reset form when selecting "new"
                    document.getElementById('fullname').value = caremilCheckout.displayName || '';
                    document.getElementById('phone').value = caremilCheckout.displayPhone || '';
                    document.getElementById('address').value = '';
                    document.getElementById('checkout_province_id').value = '';
                    document.getElementById('checkout_district_id').value = '';
                    document.getElementById('checkout_commune_id').value = '';
                    const pSearch = document.getElementById('checkout_province_search');
                    const cSearch = document.getElementById('checkout_commune_search');
                    const pSug    = document.getElementById('checkout_province_suggestions');
                    const cSug    = document.getElementById('checkout_commune_suggestions');
                    if (pSearch) pSearch.value = '';
                    if (cSearch) cSearch.value = '';
                    if (pSug) { pSug.innerHTML = ''; pSug.classList.add('hidden'); }
                    if (cSug) { cSug.innerHTML = ''; cSug.classList.add('hidden'); }
                    await ensureProvinces();
                    return;
                }

                // Find address object
                // Ensure IDs match as strings
                let found = savedAddresses.find(a => String(a.id) === String(id));
                if (!found && select) {
                     const opt = Array.from(select.options).find(o => o.value === id);
                     if (opt) {
                         found = {
                            full_name: opt.dataset.name,
                            phone_number: opt.dataset.phone,
                            full_address: opt.dataset.full,
                            province_id: opt.dataset.province,
                            district_id: opt.dataset.district, // Now we explicitly pass district
                            commune_id: opt.dataset.commune
                         };
                     }
                }

                if (found) {
                    await applyAddressToForm(found);
                }
            } catch (e) {
                console.error("Lỗi khi áp dụng địa chỉ:", e);
                alert("Không thể áp dụng địa chỉ này. Vui lòng nhập thủ công.");
            } finally {
                if (loading) loading.classList.add('hidden');
                if (select) select.disabled = false;
            }
        }

        async function maybeSaveAddress(shippingData) {
            if (!caremilCheckout.loggedIn) return;
            const normalized = normalizeAddress(shippingData.full_address);
            if (!normalized) return;

            const duplicated = savedAddresses.some(addr => normalizeAddress(addr.full_address || addr.address) === normalized);
            if (duplicated) return;

            try {
                const res = await fetch(caremilCheckout.ajaxUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'caremil_save_address',
                        nonce: caremilCheckout.nonce,
                        full_name: shippingData.full_name,
                        phone_number: shippingData.phone_number,
                        full_address: shippingData.full_address,
                        province_id: shippingData.province_id || '',
                        district_id: shippingData.district_id || '',
                        commune_id: shippingData.commune_id || ''
                    })
                });
                const data = await res.json();
                if (data && data.success && data.data && Array.isArray(data.data.addresses)) {
                    savedAddresses = data.data.addresses;
                    rebuildSavedSelect();
                }
            } catch (e) {
                console.warn('Không thể lưu địa chỉ mới', e);
            }
        }

        async function submitOrder() {
            // Validate form
            const form = document.getElementById('checkout-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Validate địa chỉ: Tỉnh/Thành & Phường/Xã theo chuẩn mới
            const provinceId   = document.getElementById('checkout_province_id').value;
            const communeId    = document.getElementById('checkout_commune_id').value;
            const provinceName = document.getElementById('checkout_province_search').value.trim();
            const communeName  = document.getElementById('checkout_commune_search').value.trim();
            
            if (!provinceId || !provinceName) {
                alert('Vui lòng chọn Tỉnh/Thành phố');
                document.getElementById('checkout_province_search').focus();
                return;
            }
            
            if (!communeId || !communeName) {
                alert('Vui lòng chọn Phường/Xã');
                document.getElementById('checkout_commune_search').focus();
                return;
            }

            // Lấy phương thức thanh toán
            const paymentMethod = document.querySelector('input[name="payment"]:checked');
            if (!paymentMethod) {
                alert('Vui lòng chọn phương thức thanh toán');
                return;
            }
            const paymentValue = paymentMethod.value;
            
            // Lấy thông tin form
            const districtId = document.getElementById('checkout_district_id').value || '';
            const commune    = communeId;
            const formData = {
                fullname: document.getElementById('fullname').value.trim(),
                phone: document.getElementById('phone').value.trim(),
                email: document.getElementById('email').value.trim(),
                city: provinceId,
                district: districtId,
                commune: commune,
                address: document.getElementById('address').value.trim(),
                payment: paymentValue
            };

            // Ghép full_address chuẩn: số nhà/đường + phường/xã + tỉnh/thành
            const detailAddr = formData.address;
            const fullAddressParts = [detailAddr, communeName, provinceName].filter(Boolean);
            const fullAddress = fullAddressParts.join(', ');

            const shippingData = {
                full_name: formData.fullname,
                phone_number: formData.phone,
                full_address: fullAddress,
                province_id: provinceId,
                district_id: districtId,
                commune_id: commune
            };

            const btn = document.querySelector('.order-btn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
            btn.disabled = true;

            await maybeSaveAddress(shippingData);

            // Call Real Order API
            try {
                // Get current cart from caremilCheckout or reload?
                // For simplicity, we send necessary customer data. Server has the cart in session.
                
                const response = await fetch(caremilCheckout.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'caremil_create_order',
                        nonce: caremilCheckout.createOrderNonce,
                        'customer[name]': formData.fullname,
                        'customer[phone]': formData.phone,
                        'customer[address]': formData.address,
                        'customer[province_id]': shippingData.province_id,
                        'customer[district_id]': shippingData.district_id,
                        'customer[commune_id]': shippingData.commune_id,
                        'customer[note]': '', // Add note field if needed
                        'payment_method': paymentValue,
                        // We rely on server session for cart items to avoid tampering
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    const orderData = {
                        ...formData,
                        orderId: result.data.order_id,
                        orderCode: result.data.order_code,
                        total: result.data.total
                    };
                    
                    // Nếu là COD (thanh toán khi nhận hàng) -> xóa giỏ hàng và redirect tới order-status
                    if (paymentValue === 'cod') {
                        // Xóa giỏ hàng sau khi đặt hàng thành công
                        await emptyCart();
                        
                        // Đánh dấu đã đặt hàng thành công
                        sessionStorage.setItem('orderCompleted', 'true');
                        sessionStorage.setItem('lastOrder', JSON.stringify(orderData)); // Store for status page
                        
                        window.location.href = '<?php echo esc_js( caremil_get_page_url_by_template( "Order Status" ) ); ?>?order_id=' + orderData.orderId;
                    } 
                    // Nếu là QR Code (bank) -> redirect tới payment
                    else if (paymentValue === 'bank') {
                        // Lưu thông tin đơn hàng vào session/localStorage
                        sessionStorage.setItem('orderData', JSON.stringify(orderData));
                        window.location.href = '<?php echo esc_js( caremil_get_page_url_by_template( "Payment" ) ); ?>?order_id=' + orderData.orderId + '&total=' + orderData.total;
                    }
                } else {
                    // Hiển thị debug info nếu có
                    const debugDiv = document.getElementById('order-error-debug');
                    const debugContent = document.getElementById('order-error-content');
                    
                    if (debugDiv && debugContent) {
                        let log = result.data.message || 'Lỗi không xác định';
                        if (result.data.debug_data) {
                            log += '\n\n--- DEBUG INFO ---\n';
                            log += 'PAYLOAD:\n' + JSON.stringify(result.data.debug_data.payload, null, 2);
                            log += '\n\nRESPONSE:\n' + JSON.stringify(result.data.debug_data.response, null, 2);
                        }
                        debugContent.textContent = log;
                        debugDiv.classList.remove('hidden');
                        debugDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }

                    alert(result.data.message || 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng kiểm tra chi tiết lỗi bên dưới.');
                    btn.disabled = false;
                    btn.innerHTML = 'Đặt Hàng Ngay';
                }
            } catch (err) {
                console.error(err);
                alert('Lỗi kết nối tới hệ thống. Vui lòng thử lại sau.');
                btn.disabled = false;
                btn.innerHTML = 'Đặt Hàng Ngay';
            }
        }

        // Initialize geo autocomplete on page load
        document.addEventListener('DOMContentLoaded', async () => {
            await ensureProvinces();
            
            // Re-fill coupon code if PHP says so (optional, but good for UX)
            // But we already handle it in PHP.
        });
        
        // Coupon Logic
        function applyCoupon() {
            const input = document.getElementById('coupon-code');
            const code = input.value.trim();
            const btn = document.getElementById('btn-apply-coupon');
            const msg = document.getElementById('coupon-message');
            
            if (!code) {
                input.focus();
                return;
            }
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            msg.classList.add('hidden');
            msg.className = 'text-xs mt-2 hidden';
            
            fetch(caremilCheckout.ajaxUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'caremil_apply_coupon',
                    code: code,
                    // valid nonce removed for demo or assumes public if logged in isn't required
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = 'Áp dụng';
                msg.classList.remove('hidden');
                
                if (data.success) {
                    msg.innerHTML = `<span class="text-green-600 font-bold"><i class="fas fa-check-circle"></i> ${data.data.message}</span>`;
                    // Update layout
                    setTimeout(() => location.reload(), 500); // Reload to reflect pricing in PHP (easiest way)
                } else {
                    msg.innerHTML = `<span class="text-red-500 font-bold"><i class="fas fa-exclamation-circle"></i> ${data.data.message}</span>`;
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = 'Áp dụng';
                console.error(err);
            });
        }
        
        // Quick apply voucher from card
        function applyVoucher(code) {
            const input = document.getElementById('coupon-code');
            input.value = code.toUpperCase();
            
            // Smooth scroll to coupon section
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Highlight animation
            input.classList.add('ring-2', 'ring-accent-600');
            setTimeout(() => {
                input.classList.remove('ring-2', 'ring-accent-600');
                // Auto apply
                applyCoupon();
            }, 300);
        }
        
        function removeCoupon() {
            if (!confirm('Bạn muốn gỡ mã giảm giá này?')) return;
            
            fetch(caremilCheckout.ajaxUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'caremil_remove_coupon'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
        
        // Remove coupon by category (order/shipping)
        function removeCouponByCategory(category) {
            const categoryLabel = category === 'order' ? 'giảm giá đơn hàng' : 'freeship';
            if (!confirm(`Bạn muốn gỡ mã ${categoryLabel} này?`)) return;
            
            fetch(caremilCheckout.ajaxUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'caremil_remove_coupon_by_category',
                    category: category
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.data.message || 'Có lỗi xảy ra');
                }
            });
        }
        
        // Remove specific order coupon by code
        function removeCouponByCode(code) {
            if (!confirm(`Bạn muốn gỡ mã ${code}?`)) return;
            
            fetch(caremilCheckout.ajaxUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'caremil_remove_coupon_by_category',
                    category: 'order',
                    code: code
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.data.message || 'Có lỗi xảy ra');
                }
            });
        }

        // --- REALTIME SHIPPING UPDATE LOGIC ---
        let addressUpdateTimeout;
        
        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount).replace('₫', 'đ');
        }

        function updateShippingFee() {
            // Get hidden inputs updated by the address selector
            const provinceId = document.getElementById('checkout_province_id')?.value;
            const districtId = document.getElementById('checkout_district_id')?.value;
            
            // Get Names for accurate mapping
            const provinceName = document.getElementById('checkout_province_search')?.value;
            const districtName = document.getElementById('checkout_district_name')?.value;
            
            const shippingEl = document.getElementById('checkout-shipping');
            const totalEl = document.getElementById('checkout-total');
            const debugPanel = document.querySelector('.bg-blue-50.border-blue-200'); // Access debug panel

            if (!provinceId || !districtId) {
                if(shippingEl) shippingEl.innerHTML = '<span class="text-xs italic">Nhập địa chỉ để tính phí</span>';
                return;
            }
            
            // Show loading state
            if(shippingEl) shippingEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tính...';
            
            // Debounce
            clearTimeout(addressUpdateTimeout);
            addressUpdateTimeout = setTimeout(() => {
                console.log('Calculating shipping for:', provinceName, districtName);
                
                // Determine COD status based on selected payment method
                const isCod = document.querySelector('input[name="payment"]:checked')?.value === 'cod';

                fetch(caremilCheckout.ajaxUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'caremil_calculate_shipping',
                        province_id: provinceId,
                        district_id: districtId,
                        province_name: provinceName,
                        district_name: districtName,
                        is_cod: isCod
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const fee = data.data.fee;
                        const feeFormatted = data.data.fee_formatted;
                        
                        // Update Shipping Text
                        if(shippingEl) {
                            shippingEl.classList.remove('text-green-500', 'text-gray-400');
                            shippingEl.className = 'font-bold text-gray-700'; // Reset class
                            shippingEl.textContent = feeFormatted;
                        }

                        // Update Total (We need current subtotal from DOM or reload page)
                        // For accuracy and coupon recalculation, RELOAD is safest for now
                        // But user wants instant feedback. Let's try reload for correctness.
                        location.reload(); 
                    } else {
                        if(shippingEl) shippingEl.textContent = 'Không tính được phí';
                    }
                })
                .catch(err => {
                    console.error(err);
                    if(shippingEl) shippingEl.textContent = 'Lỗi tính phí';
                });
            }, 500);
        }

        // Attach listeners to hidden inputs (using MutationObserver since their values are set by JS)
        document.addEventListener('DOMContentLoaded', () => {
             const provinceInput = document.getElementById('checkout_province_id');
             const districtInput = document.getElementById('checkout_district_id');
             const paymentInputs = document.querySelectorAll('input[name="payment"]');

             // Observer for Province/District changes
             const observer = new MutationObserver((mutations) => {
                 mutations.forEach((mutation) => {
                     if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                         updateShippingFee();
                     }
                 });
             });

             if (provinceInput) observer.observe(provinceInput, { attributes: true });
             if (districtInput) observer.observe(districtInput, { attributes: true });

             // Listen for Payment Method change (affects COD fee sometimes)
             paymentInputs.forEach(input => {
                 input.addEventListener('change', () => {
                     // Only reload if we have a full address, as payment method might change COD/Shipping logic
                     if (provinceInput.value && districtInput.value) {
                        updateShippingFee();
                     }
                 });
             });
        });
    </script>
<?php
get_footer();