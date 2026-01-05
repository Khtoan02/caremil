<?php
/**
 * Template Name: Checkout
 * Template Post Type: page
 * Description: Template for displaying checkout page
 *
 * @package Caremil
 */
get_header();

// Kiểm tra kết nối Pancake trước khi xử lý checkout
if ( function_exists( 'caremil_require_pancake_connection' ) ) {
    caremil_require_pancake_connection();
}

// Kiểm tra giỏ hàng trống - redirect về trang giỏ hàng
$cart = caremil_get_cart();
if ( empty( $cart ) ) {
    wp_redirect( caremil_get_page_url_by_template( 'Carts' ) );
    exit;
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

    if ( ( empty( $caremil_customer ) || ! is_array( $caremil_customer ) ) && $caremil_phone_session ) {
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

    // Loại bỏ trùng địa chỉ theo full_address
    $deduped = array();
    $seen    = array();
    foreach ( $caremil_addresses_raw as $addr ) {
        $full = isset( $addr['full_address'] ) ? $addr['full_address'] : ( $addr['address'] ?? '' );
        $full = trim( $full );
        if ( strlen( $full ) < 5 ) {
            continue;
        }
        $key = strtolower( preg_replace( '/\s+/', ' ', $full ) );
        if ( isset( $seen[ $key ] ) ) {
            continue;
        }
        $seen[ $key ]  = true;
        $deduped[]     = $addr;
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
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;700;800&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            navy: '#1a4f8a',
                            blue: '#4cc9f0',
                            gold: '#ffd166',
                            soft: '#e0fbfc',
                            cream: '#fffdf2',
                            pink: '#ef476f',
                            green: '#4ade80'
                        }
                    },
                    fontFamily: {
                        sans: ['Quicksand', 'sans-serif'],
                        display: ['Baloo 2', 'cursive'],
                    }
                }
            }
        }
    </script>
    <style>
        /* Stepper Styles */
        .step.active .step-circle { background-color: #1a4f8a; color: white; border-color: #1a4f8a; }
        .step.completed .step-circle { background-color: #4ade80; color: white; border-color: #4ade80; }
        .step.active .step-text { color: #1a4f8a; font-weight: 700; }

        /* Form Styles */
        .form-label { font-size: 0.85rem; font-weight: 700; color: #1a4f8a; margin-bottom: 0.5rem; display: block; }
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
            border-color: #4cc9f0;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(76, 201, 240, 0.1);
        }
        .form-input::placeholder { color: #94a3b8; font-weight: 500; }

        /* Payment Method Radio */
        .payment-radio:checked + div {
            border-color: #1a4f8a;
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
            background: linear-gradient(135deg, #ef476f 0%, #ff758c 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .order-btn:hover {
            box-shadow: 0 15px 30px -5px rgba(239, 71, 111, 0.5);
            transform: translateY(-2px) scale(1.02);
        }
        
        /* Checkout page header positioning - nằm dưới header chính */
        body {
            padding-top: 96px !important; /* 80px (main header) + 16px (checkout header) */
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-700 font-sans pb-24">

    <!-- HEADER (Checkout Mode) -->
    <nav class="fixed w-full z-40 top-20 bg-white border-b border-gray-100 h-16 flex items-center shadow-sm">
        <div class="container mx-auto px-4 flex justify-between items-center max-w-6xl">
            <!-- Logo -->
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-2 group">
                <i class="fas fa-leaf text-brand-gold text-xl group-hover:rotate-12 transition-transform"></i>
                <span class="text-xl font-display font-black text-brand-navy tracking-tight">Care<span class="text-brand-blue">MIL</span></span>
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
                    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-brand-navy"></div>
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-display font-bold text-brand-navy flex items-center gap-2">
                                <span class="bg-brand-navy text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                                Thông Tin Giao Hàng
                            </h2>
                            <!-- Address Book Trigger -->
                            <div class="relative group w-full max-w-md">
                                <?php if ( $caremil_logged_in ) : ?>
                                    <label class="form-label mb-1">Sổ địa chỉ (nếu muốn dùng nhanh)</label>
                                    <select id="saved-address" class="form-input py-2 pl-3 pr-8 text-sm border-brand-blue/30 bg-blue-50/50 cursor-pointer text-brand-navy hover:bg-blue-50 transition w-full" onchange="handleSavedAddressChange(this.value)">
                                        <option value="">-- Chọn địa chỉ đã lưu --</option>
                                        <?php foreach ( $caremil_addresses as $idx => $addr ) : 
                                            $aid  = isset( $addr['id'] ) ? $addr['id'] : 'addr_' . $idx;
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
                                                <?php echo esc_html( $full . ( $name ? ' • ' . $name : '' ) . ( $phone ? ' • ' . $phone : '' ) ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="new">+ Nhập địa chỉ mới</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-2">Chọn địa chỉ đã lưu hoặc để trống rồi điền form bên dưới.</p>
                                <?php else : ?>
                                    <p class="text-xs text-gray-500">Bạn chưa đăng nhập, hãy điền địa chỉ giao hàng bên dưới.</p>
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
                            <input type="hidden" id="checkout_commune_id" value="">

                            <div class="relative">
                                <label class="form-label flex items-center justify-between">
                                    <span>Tỉnh / Thành phố <span class="text-red-500">*</span></span>
                                    <span class="text-[11px] font-normal text-brand-blue">Gõ để tìm nhanh</span>
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
                                    <span>Phường / Xã <span class="text-red-500">*</span></span>
                                    <span class="text-[11px] font-normal text-brand-blue">Chọn theo tỉnh</span>
                                </label>
                                <input
                                    type="text"
                                    id="checkout_commune_search"
                                    class="form-input text-sm"
                                    placeholder="Ví dụ: Phường 1, Xã Bình An..."
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
                    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-brand-blue"></div>
                        <h2 class="text-xl font-display font-bold text-brand-navy mb-6 flex items-center gap-2">
                            <span class="bg-brand-blue text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                            Phương Thức Thanh Toán
                        </h2>

                        <div class="space-y-4">
                            
                            <!-- 1. QR Code Transfer - RECOMMENDED & DEFAULT -->
                            <label class="block cursor-pointer relative group">
                                <input type="radio" name="payment" value="bank" class="payment-radio sr-only" checked>
                                <div class="p-5 rounded-2xl border-2 border-gray-200 flex items-center gap-4 hover:border-brand-blue transition bg-white relative overflow-hidden">
                                    <!-- Recommended Badge -->
                                    <div class="absolute -right-8 top-4 bg-brand-pink text-white text-[10px] font-bold px-8 py-1 rotate-45 shadow-sm">Khuyên Dùng</div>
                                    
                                    <div class="w-12 h-12 bg-blue-100 text-brand-blue rounded-full flex items-center justify-center text-2xl flex-shrink-0">
                                        <i class="fas fa-qrcode"></i>
                                    </div>
                                    <div class="flex-grow pr-10">
                                        <h4 class="font-bold text-brand-navy text-lg">Chuyển khoản QR Code</h4>
                                        <p class="text-xs text-gray-500">Quét mã QR ngân hàng, xác nhận nhanh chóng.</p>
                                        <div class="flex gap-2 mt-2">
                                            <span class="text-[10px] bg-blue-50 text-brand-blue px-2 py-0.5 rounded border border-blue-100">Nhanh & An toàn</span>
                                        </div>
                                    </div>
                                    <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                        <div class="w-3 h-3 rounded-full bg-brand-navy check-icon opacity-0 transition-all transform scale-0"></div>
                                    </div>
                                </div>
                            </label>

                            <!-- 2. COD -->
                            <label class="block cursor-pointer relative group">
                                <input type="radio" name="payment" value="cod" class="payment-radio sr-only">
                                <div class="p-5 rounded-2xl border-2 border-gray-200 flex items-center gap-4 hover:border-brand-blue transition bg-white">
                                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-2xl flex-shrink-0">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="flex-grow">
                                        <h4 class="font-bold text-brand-navy text-lg">Thanh toán khi nhận hàng (COD)</h4>
                                        <p class="text-xs text-gray-500">Thanh toán tiền mặt cho shipper khi nhận được hàng.</p>
                                    </div>
                                    <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                        <div class="w-3 h-3 rounded-full bg-brand-navy check-icon opacity-0 transition-all transform scale-0"></div>
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
                    <div class="sticky top-40 bg-white rounded-3xl p-6 md:p-8 shadow-card border border-gray-100">
                        <h3 class="text-lg font-bold text-brand-navy mb-6 pb-4 border-b border-gray-100 flex justify-between items-center">
                            Đơn Hàng (<?php echo esc_html( caremil_get_cart_count() ); ?>)
                            <a href="<?php echo esc_url( home_url( '/gio-hang' ) ); ?>" class="text-xs text-brand-blue hover:underline font-normal">Sửa</a>
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
                                    <p class="text-sm font-bold text-brand-navy line-clamp-2"><?php echo esc_html( $product_title ); ?></p>
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

                        <!-- Calculations -->
                        <?php
                        $subtotal = caremil_get_cart_total();
                        $discount = 0; // Có thể tính từ voucher sau
                        $shipping = 0; // Miễn phí nếu đủ điều kiện
                        $total = $subtotal - $discount + $shipping;
                        ?>
                        <div class="space-y-3 text-sm border-t border-dashed border-gray-200 pt-4 mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>Tạm tính</span>
                                <span id="checkout-subtotal"><?php echo esc_html( caremil_format_price( $subtotal ) ); ?></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Giảm giá</span>
                                <span class="text-green-500" id="checkout-discount">-<?php echo esc_html( caremil_format_price( $discount ) ); ?></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Phí vận chuyển</span>
                                <span class="font-bold text-green-500" id="checkout-shipping">
                                    <?php echo $shipping > 0 ? esc_html( caremil_format_price( $shipping ) ) : 'Miễn phí'; ?>
                                </span>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="flex justify-between items-center mb-8 pt-4 border-t border-gray-100">
                            <span class="text-base font-bold text-brand-navy">Tổng thanh toán</span>
                            <div class="text-right">
                                <span class="text-2xl font-black text-brand-pink block leading-none" id="checkout-total"><?php echo esc_html( caremil_format_price( $total ) ); ?></span>
                                <span class="text-[10px] text-gray-400">(VAT included)</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="order-btn w-full text-white font-bold py-4 rounded-xl shadow-lg flex items-center justify-center gap-2 text-lg uppercase tracking-wide group">
                            <span>Đặt Hàng Ngay</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>

                        <p class="text-center text-xs text-gray-400 mt-4 px-4">
                            Bằng việc đặt hàng, bạn đồng ý với <a href="#" class="underline hover:text-brand-blue">điều khoản sử dụng</a> của CareMIL.
                        </p>
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
            <h3 class="text-2xl font-display font-black text-brand-navy mb-2">Đặt Hàng Thành Công!</h3>
            <p class="text-gray-600 mb-8">Cảm ơn bạn đã tin chọn CareMIL. Chúng tôi sẽ liên hệ xác nhận đơn hàng trong giây lát.</p>
            <button onclick="window.location.href='<?php echo esc_js( home_url( '/' ) ); ?>'" class="w-full bg-brand-navy text-white font-bold py-3 rounded-xl hover:bg-brand-blue transition shadow-lg">
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

        // Geo API cache (Tỉnh/Thành + Phường/Xã, quận/huyện ẩn nội bộ)
        const geoCache = {
            provinces: [],
            districts: {},         // key: province_id -> list
            communesByProvince: {} // key: province_id -> list communes (kèm district_id)
        };

        async function geoFetch(url) {
            const res = await fetch(url);
            if (!res.ok) throw new Error('Geo API error');
            return await res.json();
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

        async function loadCommunesByProvince(provinceId) {
            if (geoCache.communesByProvince[provinceId]) {
                return geoCache.communesByProvince[provinceId];
            }
            // Lấy districts trước (ẩn trong UI)
            if (!geoCache.districts[provinceId]) {
                const dRes = await geoFetch(`https://pos.pages.fm/api/v1/geo/districts?province_id=${provinceId}`);
                geoCache.districts[provinceId] = dRes.data || [];
            }
            const districts = geoCache.districts[provinceId];

            let allCommunes = [];
            for (const d of districts) {
                const did = d.id;
                if (!did) continue;
                const cRes = await geoFetch(`https://pos.pages.fm/api/v1/geo/communes?district_id=${did}&province_id=${provinceId}`);
                const communes = (cRes.data || []).map(c => ({
                    ...c,
                    district_id: did,
                    district_name: d.name || ''
                }));
                allCommunes = allCommunes.concat(communes);
            }
            geoCache.communesByProvince[provinceId] = sortByName(allCommunes);
            return geoCache.communesByProvince[provinceId];
        }

        async function ensureProvinces(selectedId = '', selectedCommuneId = '') {
            const provinces = await loadProvinces();
            const hiddenProv   = document.getElementById('checkout_province_id');
            const searchInput  = document.getElementById('checkout_province_search');
            const suggestions  = document.getElementById('checkout_province_suggestions');
            if (!hiddenProv || !searchInput || !suggestions) return;

            function renderProvinceSuggestions(keyword = '') {
                const kw = keyword.trim().toLowerCase();
                suggestions.innerHTML = '';
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
                        searchInput.value = p.name || '';
                        suggestions.classList.add('hidden');
                        // Reset commune trước khi load mới
                        const hiddenComm = document.getElementById('checkout_commune_id');
                        const hiddenDist = document.getElementById('checkout_district_id');
                        const communeInput = document.getElementById('checkout_commune_search');
                        const communeSug   = document.getElementById('checkout_commune_suggestions');
                        if (hiddenComm) hiddenComm.value = '';
                        if (hiddenDist) hiddenDist.value = '';
                        if (communeInput) communeInput.value = '';
                        if (communeSug) { communeSug.innerHTML = ''; communeSug.classList.add('hidden'); }
                        await prepareCommunesForProvince(p.id);
                    });
                    suggestions.appendChild(btn);
                });
                suggestions.classList.toggle('hidden', filtered.length === 0);
            }

            if (!searchInput.__caremilProvBound) {
                searchInput.addEventListener('input', () => {
                    renderProvinceSuggestions(searchInput.value);
                });
                searchInput.addEventListener('focus', () => {
                    if (searchInput.value.trim() === '') {
                        renderProvinceSuggestions('');
                    } else {
                        renderProvinceSuggestions(searchInput.value);
                    }
                });
                document.addEventListener('click', (e) => {
                    if (!suggestions.contains(e.target) && e.target !== searchInput) {
                        suggestions.classList.add('hidden');
                    }
                });
                searchInput.__caremilProvBound = true;
            }

            // Gán lại khi edit / chọn từ sổ địa chỉ
            if (selectedId) {
                const found = provinces.find(p => String(p.id) === String(selectedId));
                if (found) {
                    hiddenProv.value = found.id || '';
                    searchInput.value = found.name || '';
                    await prepareCommunesForProvince(found.id, selectedCommuneId);
                }
            }
        }

        async function prepareCommunesForProvince(provinceId, selectedCommuneId = '') {
            const hiddenProv   = document.getElementById('checkout_province_id');
            const hiddenComm   = document.getElementById('checkout_commune_id');
            const hiddenDist   = document.getElementById('checkout_district_id');
            const searchInput  = document.getElementById('checkout_commune_search');
            const suggestions  = document.getElementById('checkout_commune_suggestions');
            if (!provinceId || !hiddenProv || !searchInput || !suggestions) return;

            const communes = await loadCommunesByProvince(provinceId);

            function renderCommuneSuggestions(keyword = '') {
                const kw = keyword.trim().toLowerCase();
                suggestions.innerHTML = '';
                let filtered = communes;
                if (kw) {
                    filtered = communes.filter(c => {
                        const combo = `${c.name || ''} ${c.district_name || ''}`.toLowerCase();
                        return combo.includes(kw);
                    });
                }
                filtered.slice(0, 80).forEach(c => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'w-full text-left px-3 py-2 hover:bg-blue-50 text-sm flex flex-col';
                    btn.innerHTML = `
                        <span class="font-semibold text-brand-navy">${c.name || ''}</span>
                        ${c.district_name ? `<span class="text-[11px] text-gray-500">Quận/Huyện (nội bộ): ${c.district_name}</span>` : ''}
                    `;
                    btn.addEventListener('click', () => {
                        searchInput.value   = c.name || '';
                        hiddenComm.value    = c.id || '';
                        hiddenDist.value    = c.district_id || '';
                        suggestions.classList.add('hidden');
                    });
                    suggestions.appendChild(btn);
                });
                suggestions.classList.toggle('hidden', filtered.length === 0);
            }

            if (!searchInput.__caremilCommBound) {
                searchInput.addEventListener('input', () => {
                    renderCommuneSuggestions(searchInput.value);
                });
                searchInput.addEventListener('focus', () => {
                    if (searchInput.value.trim() === '') {
                        renderCommuneSuggestions('');
                    } else {
                        renderCommuneSuggestions(searchInput.value);
                    }
                });
                document.addEventListener('click', (e) => {
                    if (!suggestions.contains(e.target) && e.target !== searchInput) {
                        suggestions.classList.add('hidden');
                    }
                });
                searchInput.__caremilCommBound = true;
            }

            // Gán sẵn phường/xã khi edit
            if (selectedCommuneId) {
                const found = communes.find(c => String(c.id) === String(selectedCommuneId));
                if (found) {
                    hiddenComm.value  = found.id || '';
                    hiddenDist.value  = found.district_id || '';
                    if (!searchInput.value) {
                        searchInput.value = found.name || '';
                    }
                }
            } else {
                // Lần đầu load, hiển thị danh sách gợi ý rỗng (top)
                renderCommuneSuggestions('');
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
            const hidden = document.getElementById('selected-address-id');
            if (hidden) hidden.value = id || '';
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
            const found = savedAddresses.find(a => String(a.id) === String(id));
            if (found) {
                await applyAddressToForm(found);
                return;
            }
            const select = document.getElementById('saved-address');
            if (select) {
                const opt = Array.from(select.options).find(o => o.value === id);
                if (opt) {
                    await applyAddressToForm({
                        full_name: opt.dataset.name,
                        phone_number: opt.dataset.phone,
                        full_address: opt.dataset.full,
                        province_id: opt.dataset.province,
                        commune_id: opt.dataset.commune
                    });
                }
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

            // Simulate processing
            setTimeout(async () => {
                // Nếu là COD (thanh toán khi nhận hàng) -> xóa giỏ hàng và redirect tới order-status
                if (paymentValue === 'cod') {
                    // Xóa giỏ hàng sau khi đặt hàng thành công
                    const cartEmptied = await emptyCart();
                    if (cartEmptied) {
                        // Đánh dấu đã đặt hàng thành công
                        sessionStorage.setItem('orderCompleted', 'true');
                        window.location.href = '<?php echo esc_js( caremil_get_page_url_by_template( "Order Status" ) ); ?>';
                    } else {
                        // Nếu xóa giỏ hàng thất bại, vẫn redirect nhưng cảnh báo
                        console.warn('Không thể xóa giỏ hàng, nhưng vẫn tiếp tục');
                        window.location.href = '<?php echo esc_js( caremil_get_page_url_by_template( "Order Status" ) ); ?>';
                    }
                } 
                // Nếu là QR Code (bank) -> redirect tới payment (chưa xóa giỏ hàng, sẽ xóa sau khi thanh toán thành công)
                else if (paymentValue === 'bank') {
                    // Lưu thông tin đơn hàng vào session/localStorage hoặc truyền qua URL
                    const orderData = {
                        ...formData,
                        total: document.getElementById('checkout-total').innerText.trim()
                    };
                    // Có thể lưu vào sessionStorage để payment page lấy
                    sessionStorage.setItem('orderData', JSON.stringify(orderData));
                    window.location.href = '<?php echo esc_js( caremil_get_page_url_by_template( "Payment" ) ); ?>';
                }
            }, 1500);
        }

        // Initialize geo autocomplete on page load
        document.addEventListener('DOMContentLoaded', async () => {
            await ensureProvinces();
        });
    </script>
<?php
get_footer();