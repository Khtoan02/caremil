<?php
/**
 * Template Name: Cart Page
 * specific template for 'gio-hang' page
 */

get_header(); echo '<script src="https://cdn.tailwindcss.com"></script>';

// Get cart data from session
$cart_items = caremil_get_cart();
$total_price = caremil_get_cart_total();
?>

<div class="container mx-auto px-4 py-12 max-w-5xl">
    <h1 class="text-3xl font-bold mb-8 text-gray-900 border-b pb-4">Giỏ hàng của bạn</h1>

    <?php if (empty($cart_items)) : ?>
        <div class="bg-gray-50 rounded-lg p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <h2 class="text-xl font-medium text-gray-600 mb-6">Giỏ hàng đang trống</h2>
            <a href="<?php echo home_url('/cua-hang/'); ?>" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-bold transition">
                Tiếp tục mua sắm
            </a>
        </div>
    <?php else : ?>
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Cart Items -->
            <div class="flex-1 space-y-4">
                <?php foreach ($cart_items as $key => $item) : ?>
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex gap-4 items-center">
                        <!-- Image -->
                        <div class="w-20 h-20 flex-shrink-0 bg-gray-50 rounded-md overflow-hidden">
                            <?php if (!empty($item['image'])) : ?>
                                <img src="<?php echo esc_url($item['image']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Img</div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Info -->
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 text-lg">
                                <a href="<?php echo get_permalink($item['product_id']); ?>" class="hover:text-blue-600">
                                    <?php 
                                    echo esc_html(get_the_title($item['product_id'])); 
                                    if (!empty($item['variant_label']) && $item['variant_label'] !== 'main') {
                                        echo ' - ' . esc_html($item['variant_label']);
                                    }
                                    ?>
                                </a>
                            </h3>
                            <div class="text-sm text-gray-500">
                                <?php 
                                $price_str = isset($item['price']) ? $item['price'] : '0';
                                // Extract number if it's formatted
                                $price_val = floatval(str_replace(array('.', ','), '', $price_str));
                                ?>
                                Đơn giá: <span class="font-medium text-gray-900"><?php echo esc_html($price_str); ?>đ</span>
                            </div>
                        </div>
                        
                        <!-- Qty -->
                        <div class="flex flex-col items-center">
                            <span class="text-xs text-gray-500 mb-1">Số lượng</span>
                            <div class="font-bold text-lg bg-gray-100 px-3 py-1 rounded">
                                <?php echo intval($item['quantity']); ?>
                            </div>
                        </div>
                        
                        <!-- Total -->
                        <div class="text-right min-w-[100px]">
                            <div class="text-blue-600 font-bold text-lg">
                                <?php echo number_format($price_val * $item['quantity']); ?>đ
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Summary -->
            <div class="lg:w-80">
                <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 sticky top-4">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">Cộng giỏ hàng</h3>
                    
                    <div class="flex justify-between mb-2 text-gray-600">
                        <span>Tạm tính:</span>
                        <span><?php echo number_format($total_price); ?>đ</span>
                    </div>
                    
                    <div class="flex justify-between mb-6 pt-4 border-t text-xl font-bold text-blue-600">
                        <span>Tổng cộng:</span>
                        <span><?php echo number_format($total_price); ?>đ</span>
                    </div>
                    
                    <button onclick="alert('Tính năng thanh toán đang được phát triển!')" class="w-full bg-blue-600 text-white py-4 rounded-lg font-bold hover:bg-blue-700 transition shadow-md hover:shadow-lg mb-4">
                        Thanh toán ngay
                    </button>
                    
                    <a href="<?php echo home_url('/cua-hang/'); ?>" class="block text-center text-gray-500 hover:text-gray-800 text-sm font-medium">
                        ← Tiếp tục xem sản phẩm
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
