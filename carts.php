<?php
/**
 * Template Name: Carts
 * Template Post Type: page
 * Description: Shopping Cart Page - MODERN PROFESSIONAL
 *
 * @package Dawnbridge
 */
get_header();

$cart = caremil_get_cart();
$cart_total = 0;
?>

<style>
    /* Modern cart styles */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    
    .checkout-btn {
        background: linear-gradient(135deg, #0f172a 0%, #3b82f6 100%);
        transition: all 0.3s ease;
    }
    .checkout-btn:hover {
        box-shadow: 0 12px 24px rgba(59, 130, 246, 0.4);
        transform: translateY(-2px);
    }

    /* Stepper */
    .step.active .step-circle { 
        background-color: #0f172a; 
        color: white; 
        border-color: #0f172a; 
    }
    .step.completed .step-circle { 
        background-color: #10b981; 
        color: white; 
        border-color: #10b981; 
    }
    .step.active .step-text { 
        color: #0f172a; 
        font-weight: 600; 
    }

    /* Accordion */
    .accordion-content {
        transition: max-height 0.3s ease, opacity 0.3s ease, padding 0.3s ease;
        max-height: 0;
        opacity: 0;
        overflow: hidden;
    }
    .accordion-content.open {
        max-height: 300px;
        opacity: 1;
        padding-top: 1rem;
    }
</style>

<!-- PAGE HEADER WITH PROGRESS -->
<section class="bg-gray-50 py-6 border-b border-gray-100">
    <div class="container mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between max-w-4xl mx-auto">
            <!-- Back Button -->
            <a href="<?php echo esc_url( home_url( '/cua-hang' ) ); ?>" class="flex items-center gap-2 text-gray-500 hover:text-primary-900 font-medium text-sm transition">
                <i class="fas fa-chevron-left"></i>
                <span>Continue Shopping</span>
            </a>
            
            <!-- Progress Steps -->
            <div class="hidden md:flex items-center gap-4">
                <div class="step active flex items-center gap-2">
                    <div class="step-circle w-8 h-8 rounded-full border-2 flex items-center justify-center font-semibold text-sm">1</div>
                    <span class="step-text text-sm">Cart</span>
                </div>
                <div class="w-12 h-0.5 bg-gray-200"></div>
                <div class="step flex items-center gap-2 opacity-50">
                    <div class="step-circle w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center font-semibold text-sm text-gray-400">2</div>
                    <span class="step-text text-sm text-gray-500">Checkout</span>
                </div>
                <div class="w-12 h-0.5 bg-gray-200"></div>
                <div class="step flex items-center gap-2 opacity-50">
                    <div class="step-circle w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center font-semibold text-sm text-gray-400">3</div>
                    <span class="step-text text-sm text-gray-500">Complete</span>
                </div>
            </div>

            <!-- Mobile Cart Count -->
            <div class="md:hidden font-semibold text-primary-900">
                Cart (<?php echo esc_html( caremil_get_cart_count() ); ?>)
            </div>
        </div>
    </div>
</section>

<!-- MAIN CART SECTION -->
<div class="container mx-auto px-6 lg:px-8 py-12">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- LEFT: Cart Items -->
        <div class="lg:w-2/3">
            
            <?php if ( !empty( $cart ) ) : ?>
                <!-- Shipping Progress -->
                <div class="bg-gradient-to-r from-accent-50 to-white border border-accent-100 rounded-xl p-4 mb-6 flex items-start gap-4">
                    <div class="bg-white p-2 rounded-lg shadow-sm text-accent-600 flex-shrink-0">
                        <i class="fas fa-truck text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-primary-900 text-sm mb-1">Free Shipping Available!</h4>
                        <p class="text-xs text-gray-600 mb-2">Add <span class="font-bold text-accent-600">350,000₫</span> more to get <span class="font-bold text-success-600">Free Shipping</span></p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-accent-600 h-1.5 rounded-full" style="width: 70%"></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Cart Items List -->
            <div class="space-y-4 mb-8" id="cart-items-container">
                <?php if ( empty( $cart ) ) : ?>
                    <div class="bg-white rounded-xl p-12 border border-gray-100 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shopping-cart text-3xl text-gray-400"></i>
                        </div>
                        <h3 class="font-semibold text-primary-900 text-xl mb-2">Your cart is empty</h3>
                        <p class="text-gray-500 mb-6">Start adding products to your cart</p>
                        <a href="<?php echo esc_url( home_url( '/cua-hang' ) ); ?>" class="inline-flex items-center gap-2 bg-primary-900 text-white font-medium py-3 px-8 rounded-lg hover:bg-accent-600 transition">
                            <i class="fas fa-arrow-left"></i>
                            <span>Continue Shopping</span>
                        </a>
                    </div>
                <?php else : 
                    foreach ( $cart as $cart_key => $item ) :
                        $product_id = isset( $item['product_id'] ) ? intval( $item['product_id'] ) : 0;
                        $quantity = isset( $item['quantity'] ) ? intval( $item['quantity'] ) : 1;
                        $variant_label = isset( $item['variant_label'] ) ? $item['variant_label'] : '';
                        $price = isset( $item['price'] ) ? $item['price'] : '0';
                        $image = isset( $item['image'] ) ? $item['image'] : '';
                        
                        if ( $product_id > 0 ) {
                            $product_title = get_the_title( $product_id );
                            $product_url = get_permalink( $product_id );
                            if ( empty( $image ) ) {
                                $image = get_the_post_thumbnail_url( $product_id, 'medium' );
                            }
                        } else {
                            $product_title = isset( $item['product_name'] ) ? $item['product_name'] : 'Product';
                            $product_url = '#';
                        }
                        
                        $price_numeric = floatval( str_replace( array( '.', ',' ), '', $price ) );
                        $item_total = $price_numeric * $quantity;
                        $cart_total += $item_total;
                ?>
                    <div class="bg-white rounded-xl p-4 border border-gray-100 hover:border-gray-200 transition cart-item" data-cart-key="<?php echo esc_attr( $cart_key ); ?>">
                        <div class="flex gap-4">
                            <!-- Image -->
                            <div class="w-24 h-24 bg-gray-50 rounded-lg p-2 flex-shrink-0 flex items-center justify-center">
                                <?php if ( $image ) : ?>
                                    <img src="<?php echo esc_url( $image ); ?>" class="w-full h-full object-contain" alt="<?php echo esc_attr( $product_title ); ?>" loading="lazy">
                                <?php else : ?>
                                    <i class="fas fa-box text-3xl text-gray-300"></i>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start gap-2">
                                        <h3 class="font-semibold text-primary-900 text-base">
                                            <a href="<?php echo esc_url( $product_url ); ?>" class="hover:text-accent-600 transition">
                                                <?php echo esc_html( $product_title ); ?>
                                            </a>
                                        </h3>
                                        <button onclick="removeCartItem('<?php echo esc_js( $cart_key ); ?>')" class="text-gray-400 hover:text-red-500 transition p-1">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </div>
                                    <?php if ( ! empty( $variant_label ) ) : ?>
                                        <p class="text-xs text-gray-500 mt-1"><?php echo esc_html( $variant_label ); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex items-center justify-between mt-3">
                                    <!-- Quantity -->
                                    <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg">
                                        <button onclick="updateCartQty('<?php echo esc_js( $cart_key ); ?>', -1)" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-primary-900 transition">
                                            <i class="fas fa-minus text-xs"></i>
                                        </button>
                                        <input type="number" id="qty-<?php echo esc_attr( $cart_key ); ?>" value="<?php echo esc_attr( $quantity ); ?>" min="1" class="w-10 text-center font-semibold text-primary-900 bg-transparent focus:outline-none text-sm" readonly>
                                        <button onclick="updateCartQty('<?php echo esc_js( $cart_key ); ?>', 1)" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-primary-900 transition">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Price -->
                                    <div class="text-lg font-bold text-primary-900">
                                        <?php echo esc_html( caremil_format_price( $item_total ) ); ?>
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
        </div>

        <!-- RIGHT: Order Summary -->
        <div class="lg:w-1/3">
            <div class="sticky top-24 space-y-4">
                
                <!-- Coupon Code -->
                <div class="bg-white rounded-xl p-5 border border-gray-100">
                    <button onclick="toggleAccordion('coupon')" class="w-full flex justify-between items-center text-sm font-semibold text-primary-900 hover:text-accent-600 transition">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-ticket-alt text-accent-600"></i>
                            Promo Code
                        </span>
                        <i id="icon-coupon" class="fas fa-chevron-down transition-transform text-gray-400"></i>
                    </button>
                    
                    <div id="content-coupon" class="accordion-content">
                        <div class="flex gap-2">
                            <input type="text" placeholder="Enter code" class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-accent-600 uppercase font-medium">
                            <button class="bg-accent-600 text-white font-medium text-sm px-4 rounded-lg hover:bg-accent-700 transition">
                                Apply
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white rounded-xl p-6 border border-gray-100">
                    <h3 class="font-semibold text-primary-900 mb-4 pb-3 border-b border-gray-100">
                        Order Summary
                    </h3>
                    
                    <?php
                    $subtotal = caremil_get_cart_total();
                    $discount = 0;
                    $shipping = 0;
                    $total = $subtotal - $discount + $shipping;
                    ?>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-gray-900" id="cart-subtotal"><?php echo esc_html( caremil_format_price( $subtotal ) ); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Discount</span>
                            <span class="font-medium text-success-600" id="cart-discount">-<?php echo esc_html( caremil_format_price( $discount ) ); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-medium text-accent-600" id="cart-shipping">
                                <?php echo $shipping > 0 ? esc_html( caremil_format_price( $shipping ) ) : 'Free'; ?>
                            </span>
                        </div>
                        <div class="border-t border-gray-100 pt-3 mt-3">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-primary-900">Total</span>
                                <span class="text-2xl font-bold text-primary-900" id="cart-total"><?php echo esc_html( caremil_format_price( $total ) ); ?></span>
                            </div>
                            <p class="text-xs text-gray-400 text-right mt-1">(VAT included)</p>
                        </div>
                    </div>

                    <a href="<?php echo esc_url( caremil_get_page_url_by_template( 'Checkout' ) ); ?>" class="checkout-btn w-full text-white font-semibold py-4 rounded-lg flex items-center justify-center gap-2 text-base group">
                        <span>Proceed to Checkout</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    <!-- Trust Badges -->
                    <div class="mt-4 flex justify-center gap-4 text-gray-300 text-2xl opacity-50">
                        <i class="fab fa-cc-visa"></i>
                        <i class="fab fa-cc-mastercard"></i>
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Sticky Footer -->
<?php if ( ! isset( $total ) ) : 
    $subtotal = caremil_get_cart_total();
    $total = $subtotal;
endif; ?>

<div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-100 p-4 md:hidden flex items-center justify-between shadow-lg z-40">
    <div>
        <p class="text-xs text-gray-500">Total</p>
        <p class="text-lg font-bold text-primary-900" id="mobile-cart-total"><?php echo esc_html( caremil_format_price( $total ) ); ?></p>
    </div>
    <a href="<?php echo esc_url( caremil_get_page_url_by_template( 'Checkout' ) ); ?>" class="bg-primary-900 text-white font-semibold py-3 px-8 rounded-lg shadow-lg">
        Checkout
    </a>
</div>

<script>
    const cartConfig = {
        ajaxUrl: '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>',
        nonce: '<?php echo esc_js( wp_create_nonce( 'caremil_cart_nonce' ) ); ?>'
    };

    function updateCartQty(cartKey, change) {
        const input = document.getElementById('qty-' + cartKey);
        if (!input) return;
        
        let val = parseInt(input.value) + change;
        if (val < 1) val = 1;
        
        fetch(cartConfig.ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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
            }
        });
    }

    function removeCartItem(cartKey) {
        if (!confirm('Remove this item from cart?')) return;
        
        fetch(cartConfig.ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'caremil_remove_from_cart',
                nonce: cartConfig.nonce,
                cart_key: cartKey
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector(`[data-cart-key="${cartKey}"]`);
                if (item) {
                    item.style.opacity = '0';
                    setTimeout(() => {
                        item.remove();
                        const container = document.getElementById('cart-items-container');
                        if (container && container.querySelectorAll('.cart-item').length === 0) {
                            location.reload();
                        }
                    }, 300);
                }
                updateCartTotals(data.data);
                updateHeaderCartCount(data.data.cart_count);
            }
        });
    }

    function updateCartTotals(data) {
        if (data.cart_total) {
            const els = ['cart-total', 'mobile-cart-total'];
            els.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = data.cart_total;
            });
        }
    }

    function updateHeaderCartCount(count) {
        const el = document.getElementById('header-cart-count');
        if (el) {
            el.textContent = count || '0';
            el.style.display = count > 0 ? 'flex' : 'none';
        }
    }

    function toggleAccordion(id) {
        const content = document.getElementById('content-' + id);
        const icon = document.getElementById('icon-' + id);
        
        content.classList.toggle('open');
        icon.style.transform = content.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0deg)';
    }
</script>

<?php get_footer(); ?>