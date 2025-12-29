<?php
/**
 * Template Name: Carts
 * Template Post Type: page
 * Description: Template for displaying carts page
 *
 * @package Caremil
 */
get_header();
?>
<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng - CareMIL</title>
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
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        
        .checkout-btn {
            background: linear-gradient(135deg, #1a4f8a 0%, #4cc9f0 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .checkout-btn:hover {
            box-shadow: 0 15px 30px -5px rgba(76, 201, 240, 0.5);
            transform: translateY(-2px) scale(1.01);
        }

        /* Stepper Styles */
        .step.active .step-circle { background-color: #1a4f8a; color: white; border-color: #1a4f8a; }
        .step.completed .step-circle { background-color: #4ade80; color: white; border-color: #4ade80; }
        .step.active .step-text { color: #1a4f8a; font-weight: 700; }

        /* Accordion transition */
        .accordion-content {
            transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out, padding 0.3s ease;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
        .accordion-content.open {
            max-height: 200px;
            opacity: 1;
            padding-top: 1rem;
        }
        
        /* Cart page header positioning - nằm dưới header chính */
        body {
            padding-top: 96px !important; /* 80px (main header) + 16px (cart header) */
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-700 font-sans pb-24">

    <!-- HEADER (Progress Focused) -->
    <nav class="fixed w-full z-40 top-20 bg-white border-b border-gray-100 h-16 flex items-center shadow-sm">
        <div class="container mx-auto px-4 flex justify-between items-center max-w-6xl">
            <a href="<?php echo esc_url( home_url( '/cua-hang' ) ); ?>" class="flex items-center gap-2 text-gray-500 hover:text-brand-navy font-bold text-sm transition">
                <i class="fas fa-chevron-left bg-gray-100 p-1.5 rounded-full text-xs"></i> <span>Quay lại</span>
            </a>
            
            <!-- Progress Stepper (Centered) -->
            <div class="hidden md:flex items-center gap-2 lg:gap-8">
                <div class="step active flex items-center gap-2">
                    <div class="step-circle w-8 h-8 rounded-full border-2 border-brand-navy flex items-center justify-center font-bold text-sm bg-brand-navy text-white">1</div>
                    <span class="step-text text-sm font-bold text-brand-navy">Giỏ Hàng</span>
                </div>
                <div class="w-12 h-0.5 bg-gray-200"></div>
                <div class="step flex items-center gap-2 opacity-50">
                    <div class="step-circle w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center font-bold text-sm text-gray-400">2</div>
                    <span class="step-text text-sm text-gray-500">Thông Tin</span>
                </div>
                <div class="w-12 h-0.5 bg-gray-200"></div>
                <div class="step flex items-center gap-2 opacity-50">
                    <div class="step-circle w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center font-bold text-sm text-gray-400">3</div>
                    <span class="step-text text-sm text-gray-500">Thanh Toán</span>
                </div>
            </div>

            <div class="w-20 hidden md:block"></div> <!-- Spacer -->
            <div class="md:hidden font-display font-black text-brand-navy text-xl">Giỏ Hàng (<?php echo esc_html( caremil_get_cart_count() ); ?>)</div>
        </div>
    </nav>

    <!-- MAIN SECTION -->
    <div class="container mx-auto px-4 max-w-6xl mt-32">
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
            
            <!-- LEFT: CART LIST & UPSELL -->
            <div class="lg:w-2/3">
                
                <!-- Smart Notification -->
                <div class="bg-gradient-to-r from-blue-50 to-white border border-brand-soft rounded-2xl p-4 mb-6 flex items-start gap-4 shadow-sm">
                    <div class="bg-white p-2 rounded-full shadow-sm text-brand-blue flex-shrink-0">
                        <i class="fas fa-gift text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-brand-navy text-sm">Ưu đãi dành riêng cho bạn!</h4>
                        <p class="text-xs text-gray-500 mt-1">Mua thêm <span class="font-bold text-brand-pink">350.000đ</span> để được <span class="font-bold text-green-600">Miễn Phí Vận Chuyển</span> và nhận bộ thìa ăn dặm cao cấp.</p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                            <div class="bg-brand-blue h-1.5 rounded-full" style="width: 70%"></div>
                        </div>
                    </div>
                </div>

                <!-- Product List -->
                <div class="space-y-4 mb-8" id="cart-items-container">
                    <?php
                    $cart = caremil_get_cart();
                    $cart_total = 0;
                    $item_index = 0;
                    
                    if ( empty( $cart ) ) : ?>
                        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-gray-100 shadow-sm text-center">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-shopping-cart text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="font-bold text-brand-navy text-xl mb-2">Giỏ hàng của bạn đang trống</h3>
                            <p class="text-gray-500 mb-6">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                            <a href="<?php echo esc_url( home_url( '/cua-hang' ) ); ?>" class="inline-block bg-brand-navy text-white font-bold py-3 px-8 rounded-full shadow-md hover:bg-brand-blue transition">
                                <i class="fas fa-arrow-left mr-2"></i> Tiếp tục mua sắm
                            </a>
                        </div>
                    <?php else : 
                        foreach ( $cart as $cart_key => $item ) :
                            $product_id = isset( $item['product_id'] ) ? intval( $item['product_id'] ) : 0;
                            $quantity = isset( $item['quantity'] ) ? intval( $item['quantity'] ) : 1;
                            $variant_label = isset( $item['variant_label'] ) ? $item['variant_label'] : '';
                            $price = isset( $item['price'] ) ? $item['price'] : '0';
                            $old_price = isset( $item['old_price'] ) ? $item['old_price'] : '';
                            $image = isset( $item['image'] ) ? $item['image'] : '';
                            
                            // Lấy thông tin sản phẩm từ database nếu cần
                            if ( $product_id > 0 ) {
                                $product = get_post( $product_id );
                                if ( $product ) {
                                    $product_title = get_the_title( $product_id );
                                    $product_url = get_permalink( $product_id );
                                    if ( empty( $image ) ) {
                                        $image = get_the_post_thumbnail_url( $product_id, 'medium' );
                                    }
                                    if ( empty( $variant_label ) ) {
                                        $variant_label = $product_title;
                                    }
                                } else {
                                    $product_title = 'Sản phẩm không tồn tại';
                                    $product_url = '#';
                                }
                            } else {
                                $product_title = isset( $item['product_name'] ) ? $item['product_name'] : 'Sản phẩm';
                                $product_url = '#';
                            }
                            
                            // Tính giá
                            $price_numeric = floatval( str_replace( array( '.', ',' ), '', $price ) );
                            $item_total = $price_numeric * $quantity;
                            $cart_total += $item_total;
                            $item_index++;
                    ?>
                    <div class="bg-white rounded-3xl p-4 sm:p-6 border border-gray-100 shadow-sm hover:shadow-md transition duration-300 relative group cart-item" data-cart-key="<?php echo esc_attr( $cart_key ); ?>">
                        <div class="flex gap-4 sm:gap-6">
                            <!-- Image -->
                            <div class="w-24 h-24 sm:w-32 sm:h-32 bg-brand-soft/20 rounded-2xl p-2 flex-shrink-0 flex items-center justify-center">
                                <?php if ( $image ) : ?>
                                    <img src="<?php echo esc_url( $image ); ?>" class="w-full h-full object-contain" alt="<?php echo esc_attr( $product_title ); ?>">
                                <?php else : ?>
                                    <i class="fas fa-box text-4xl text-gray-300"></i>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-grow flex flex-col justify-between py-1">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <h3 class="font-bold text-brand-navy text-base sm:text-xl line-clamp-1">
                                            <a href="<?php echo esc_url( $product_url ); ?>" class="hover:text-brand-blue transition"><?php echo esc_html( $product_title ); ?></a>
                                        </h3>
                                        <button onclick="removeCartItem('<?php echo esc_js( $cart_key ); ?>')" class="text-gray-300 hover:text-red-500 transition p-1 remove-cart-item">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    <?php if ( ! empty( $variant_label ) ) : ?>
                                        <p class="text-xs text-gray-500 mt-1">Phân loại: <?php echo esc_html( $variant_label ); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-3 mt-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-2 py-1">
                                            <button onclick="updateCartQty('<?php echo esc_js( $cart_key ); ?>', -1)" class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-brand-navy transition">
                                                <i class="fas fa-minus text-[10px]"></i>
                                            </button>
                                            <input type="number" id="qty-<?php echo esc_attr( $cart_key ); ?>" value="<?php echo esc_attr( $quantity ); ?>" min="1" class="w-8 text-center font-bold text-brand-navy bg-transparent focus:outline-none text-sm cart-qty" readonly>
                                            <button onclick="updateCartQty('<?php echo esc_js( $cart_key ); ?>', 1)" class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-brand-navy transition">
                                                <i class="fas fa-plus text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg sm:text-xl font-black text-brand-pink item-total"><?php echo esc_html( caremil_format_price( $item_total ) ); ?></div>
                                        <?php if ( ! empty( $old_price ) ) : ?>
                                            <div class="text-xs text-gray-400 line-through"><?php echo esc_html( $old_price ); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </div>

                <!-- Integrated Upsell (Cleaner) -->
                <div class="mb-8">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2"><i class="fas fa-tags text-brand-gold"></i> Mua thêm giá tốt</h4>
                    <div class="flex gap-4 overflow-x-auto pb-4 hide-scrollbar snap-x">
                        <!-- Upsell Item 1 -->
                        <div class="min-w-[240px] bg-white rounded-2xl p-3 border border-gray-100 flex items-center gap-3 hover:border-brand-blue transition cursor-pointer snap-center shadow-sm">
                            <div class="w-16 h-16 bg-gray-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Goi-sua.png" class="w-10 h-auto object-contain">
                            </div>
                            <div class="flex-grow">
                                <p class="text-xs font-bold text-brand-navy line-clamp-1">Gói Lẻ Dùng Thử</p>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-brand-pink font-bold text-sm">40.000đ</span>
                                    <button class="w-6 h-6 bg-brand-soft text-brand-blue rounded-full flex items-center justify-center hover:bg-brand-blue hover:text-white transition"><i class="fas fa-plus text-xs"></i></button>
                                </div>
                            </div>
                        </div>
                        <!-- Upsell Item 2 -->
                        <div class="min-w-[240px] bg-white rounded-2xl p-3 border border-gray-100 flex items-center gap-3 hover:border-brand-blue transition cursor-pointer snap-center shadow-sm">
                            <div class="w-16 h-16 bg-gray-50 rounded-lg flex items-center justify-center flex-shrink-0 relative">
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[8px] px-1.5 rounded-full">-10%</span>
                                <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-chuan.png" class="w-10 h-auto object-contain">
                            </div>
                            <div class="flex-grow">
                                <p class="text-xs font-bold text-brand-navy line-clamp-1">Combo 2 Hộp Lớn</p>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-brand-pink font-bold text-sm">1.650k</span>
                                    <button class="w-6 h-6 bg-brand-soft text-brand-blue rounded-full flex items-center justify-center hover:bg-brand-blue hover:text-white transition"><i class="fas fa-plus text-xs"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT: SMART SUMMARY (Sticky) -->
            <div class="lg:w-1/3">
                <div class="sticky top-24 space-y-4">
                    
                    <!-- Coupon Accordion (Clean) -->
                    <div class="bg-white rounded-3xl p-5 shadow-card border border-gray-100">
                        <button onclick="toggleAccordion('coupon')" class="w-full flex justify-between items-center text-sm font-bold text-brand-navy hover:text-brand-blue transition">
                            <span class="flex items-center gap-2"><i class="fas fa-ticket-alt text-brand-gold"></i> Mã ưu đãi & Giới thiệu</span>
                            <i id="icon-coupon" class="fas fa-chevron-down transition-transform"></i>
                        </button>
                        
                        <div id="content-coupon" class="accordion-content">
                            <div class="space-y-3">
                                <!-- Voucher -->
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Voucher Code</label>
                                    <div class="flex gap-2">
                                        <input type="text" placeholder="Nhập mã" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-blue uppercase font-bold text-brand-navy">
                                        <button class="text-brand-blue font-bold text-xs hover:bg-blue-50 px-3 rounded-lg transition">ÁP DỤNG</button>
                                    </div>
                                    <!-- Smart suggestion pills -->
                                    <div class="flex gap-2 mt-2 flex-wrap">
                                        <button class="text-[10px] border border-dashed border-green-300 text-green-600 px-2 py-1 rounded bg-green-50 hover:bg-green-100 transition">FREESHIP</button>
                                        <button class="text-[10px] border border-dashed border-blue-300 text-brand-blue px-2 py-1 rounded bg-blue-50 hover:bg-blue-100 transition">GIAM50K</button>
                                    </div>
                                </div>
                                <!-- Referral -->
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Người giới thiệu</label>
                                    <input type="text" placeholder="SĐT/Mã người giới thiệu" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-blue">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Receipt -->
                    <div class="bg-white rounded-3xl p-6 shadow-card border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-brand-blue via-brand-pink to-brand-gold"></div>
                        
                        <div class="space-y-3 mb-6">
                            <?php
                            $subtotal = caremil_get_cart_total();
                            $discount = 0; // Có thể tính từ voucher sau
                            $shipping = 0; // Miễn phí nếu đủ điều kiện
                            $total = $subtotal - $discount + $shipping;
                            ?>
                            <div class="flex justify-between text-sm text-gray-500">
                                <span>Tạm tính</span>
                                <span class="font-bold text-gray-700" id="cart-subtotal"><?php echo esc_html( caremil_format_price( $subtotal ) ); ?></span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-500">
                                <span>Giảm giá</span>
                                <span class="font-bold text-green-500" id="cart-discount">-<?php echo esc_html( caremil_format_price( $discount ) ); ?></span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-500">
                                <span>Vận chuyển</span>
                                <span class="font-bold text-brand-blue" id="cart-shipping">
                                    <?php echo $shipping > 0 ? esc_html( caremil_format_price( $shipping ) ) : 'Miễn phí'; ?>
                                </span>
                            </div>
                            <div class="h-px bg-dashed bg-gray-200 my-2"></div>
                            <div class="flex justify-between items-end">
                                <span class="font-bold text-brand-navy">Tổng thanh toán</span>
                                <span class="text-2xl font-black text-brand-pink" id="cart-total"><?php echo esc_html( caremil_format_price( $total ) ); ?></span>
                            </div>
                            <p class="text-[10px] text-gray-400 text-right">(Đã bao gồm VAT)</p>
                        </div>

                        <a href="<?php echo esc_url( caremil_get_page_url_by_template( 'Checkout' ) ); ?>" class="checkout-btn w-full text-white font-bold py-4 rounded-2xl shadow-lg flex items-center justify-center gap-3 text-lg group">
                            <span>Thanh Toán Ngay</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        
                        <!-- Trust badges -->
                        <div class="mt-4 flex justify-center gap-4 text-gray-300 text-2xl opacity-60">
                            <i class="fab fa-cc-visa"></i>
                            <i class="fab fa-cc-mastercard"></i>
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- STICKY MOBILE BAR -->
    <?php
    // Đảm bảo biến $total được định nghĩa
    if ( ! isset( $total ) ) {
        $subtotal = caremil_get_cart_total();
        $discount = 0;
        $shipping = 0;
        $total = $subtotal - $discount + $shipping;
    }
    ?>
    <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-100 p-4 md:hidden flex items-center justify-between shadow-[0_-5px_20px_rgba(0,0,0,0.05)] z-40 safe-area-pb">
        <div>
            <p class="text-xs text-gray-400">Tổng thanh toán:</p>
            <p class="text-lg font-black text-brand-pink" id="mobile-cart-total"><?php echo esc_html( caremil_format_price( $total ) ); ?></p>
        </div>
        <a href="<?php echo esc_url( caremil_get_page_url_by_template( 'Checkout' ) ); ?>" class="bg-brand-navy text-white font-bold py-2.5 px-8 rounded-xl shadow-lg inline-block text-center">
            Thanh Toán
        </a>
    </div>

    <script>
        // Cart AJAX configuration
        const cartConfig = {
            ajaxUrl: '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>',
            nonce: '<?php echo esc_js( wp_create_nonce( 'caremil_cart_nonce' ) ); ?>'
        };

        function updateCartQty(cartKey, change) {
            const input = document.getElementById('qty-' + cartKey);
            if (!input) return;
            
            let val = parseInt(input.value) + change;
            if (val < 1) val = 1;
            
            // Update via AJAX
            fetch(cartConfig.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'caremil_update_cart',
                    nonce: cartConfig.nonce,
                    cart_key: cartKey,
                    quantity: val
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    input.value = val;
                    updateCartTotals(data.data);
                    updateHeaderCartCount(data.data.cart_count);
                } else {
                    alert(data.data?.message || 'Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi cập nhật giỏ hàng');
            });
        }

        function removeCartItem(cartKey) {
            if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                return;
            }
            
            fetch(cartConfig.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'caremil_remove_from_cart',
                    nonce: cartConfig.nonce,
                    cart_key: cartKey
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove item from DOM
                    const item = document.querySelector(`[data-cart-key="${cartKey}"]`);
                    if (item) {
                        item.style.transition = 'opacity 0.3s';
                        item.style.opacity = '0';
                        setTimeout(() => {
                            item.remove();
                            // Check if cart is empty
                            const container = document.getElementById('cart-items-container');
                            if (container && container.querySelectorAll('.cart-item').length === 0) {
                                location.reload();
                            }
                        }, 300);
                    }
                    updateCartTotals(data.data);
                    updateHeaderCartCount(data.data.cart_count);
                } else {
                    alert(data.data?.message || 'Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa sản phẩm');
            });
        }

        function updateCartTotals(data) {
            if (data.cart_total) {
                const totalEl = document.getElementById('cart-total');
                const mobileTotalEl = document.getElementById('mobile-cart-total');
                if (totalEl) totalEl.textContent = data.cart_total;
                if (mobileTotalEl) mobileTotalEl.textContent = data.cart_total;
            }
        }

        function updateHeaderCartCount(count) {
            const headerCount = document.getElementById('header-cart-count');
            if (headerCount) {
                headerCount.textContent = count || '0';
                if (count > 0) {
                    headerCount.style.display = 'flex';
                } else {
                    headerCount.style.display = 'none';
                }
            }
        }

        function toggleAccordion(id) {
            const content = document.getElementById('content-' + id);
            const icon = document.getElementById('icon-' + id);
            
            if (content.classList.contains('open')) {
                content.classList.remove('open');
                icon.style.transform = 'rotate(0deg)';
            } else {
                content.classList.add('open');
                icon.style.transform = 'rotate(180deg)';
            }
        }
    </script>
<?php
get_footer();