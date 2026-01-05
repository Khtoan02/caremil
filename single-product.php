<?php
/**
 * Single Product Template - Robust Version
 * Manually queries product to bypass template hierarchy issues
 */

get_header(); echo '<script src="https://cdn.tailwindcss.com"></script>';

// 1. Get Product Slug
global $wp_query;
$slug = $wp_query->get('name'); // Standard WP way

if (empty($slug)) {
    // Fallback: Parse URL
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $parts = explode('/', trim($path, '/'));
    $slug = end($parts);
}

// 2. Query Product
$product = null;
if ($slug) {
    $args = array(
        'name'           => $slug,
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 1
    );
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        $product = $query->posts[0];
    }
}

if (!$product) {
    // Fallback search by ID if slug fails but ID is in query vars (rare)
    if (get_query_var('p')) {
        $product = get_post(get_query_var('p'));
    }
}

// 3. Render Product
if ($product) :
    $product_id = $product->ID;
    $title = $product->post_title;
    $content = $product->post_content;
    $short_desc = get_post_meta($product_id, 'caremil_short_desc', true);
    
    // Pancake synced data
    $price_formatted = get_post_meta($product_id, 'caremil_price', true);
    $price_raw = get_post_meta($product_id, 'pancake_price_raw', true);
    $sku = get_post_meta($product_id, 'pancake_sku', true);
    $pancake_category = get_post_meta($product_id, 'pancake_category_name', true);
    
    $rating = get_post_meta($product_id, 'caremil_rating', true) ?: 5;
    $rating_count = get_post_meta($product_id, 'caremil_rating_count', true) ?: 0;
    
    $featured_image = get_the_post_thumbnail_url($product_id, 'large');
    if (!$featured_image) {
        $featured_image = 'https://via.placeholder.com/800x800?text=' . urlencode($title);
    }
    ?>

    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Breadcrumb -->
        <div class="mb-6 text-sm">
            <a href="<?php echo home_url('/'); ?>" class="text-blue-600 hover:underline">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="<?php echo home_url('/cua-hang/'); ?>" class="text-blue-600 hover:underline">Sản phẩm</a>
            <span class="mx-2">/</span>
            <span class="text-gray-600"><?php echo esc_html($title); ?></span>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="grid md:grid-cols-2 gap-12">
                <!-- Product Image -->
                <div class="space-y-4">
                    <div class="relative aspect-square bg-gray-50 rounded-lg overflow-hidden group">
                        <img src="<?php echo esc_url($featured_image); ?>" 
                             alt="<?php echo esc_attr($title); ?>"
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    </div>
                    <?php if ($sku) : ?>
                    <div class="text-sm text-gray-500 font-mono">
                        SKU: <?php echo esc_html($sku); ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Product Info -->
                <div class="space-y-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2 leading-tight">
                            <?php echo esc_html($title); ?>
                        </h1>
                        
                        <!-- Rating -->
                        <div class="flex items-center gap-2 text-sm">
                            <div class="flex text-yellow-400">
                                <?php for ($i = 0; $i < 5; $i++) : ?>
                                    <svg class="w-5 h-5 <?php echo $i < $rating ? 'fill-current' : 'fill-gray-300'; ?>" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                <?php endfor; ?>
                            </div>
                            <span class="text-gray-500">(<?php echo number_format($rating_count); ?> đánh giá)</span>
                        </div>
                    </div>
                    
                    <!-- Price -->
                    <?php if ($price_formatted) : ?>
                    <div class="border-t border-b py-4 bg-blue-50/50 -mx-4 px-4 rounded-lg">
                        <div class="flex items-baseline gap-3">
                             <div class="text-4xl font-bold text-blue-600">
                                <?php echo esc_html($price_formatted); ?>đ
                            </div>
                            <?php if ($price_raw && $price_raw > 0) : ?>
                                <div class="text-sm text-gray-400 line-through">
                                    <?php echo number_format($price_raw * 1.2); ?>đ
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Short Description -->
                    <?php if ($short_desc) : ?>
                    <div class="text-gray-700 leading-relaxed text-lg">
                        <?php echo nl2br(esc_html($short_desc)); ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Add to Cart Section -->
                    <div class="space-y-4 pt-4">
                        <div class="flex items-center gap-4">
                            <!-- Quantity -->
                            <div class="flex items-center border border-gray-300 rounded-lg h-12 w-32 bg-white">
                                <button onclick="updateQty(-1)" class="w-10 h-full flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded-l-lg transition">-</button>
                                <input type="number" id="qty" value="1" min="1" class="w-full text-center font-bold text-lg border-none focus:ring-0 p-0 text-gray-800" readonly>
                                <button onclick="updateQty(1)" class="w-10 h-full flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded-r-lg transition">+</button>
                            </div>
                            
                            <!-- Add Button -->
                            <button onclick="addToCart(<?php echo $product_id; ?>)" id="add-btn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg h-12 rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span>Thêm vào giỏ</span>
                            </button>
                        </div>
                        
                        <!-- Success Message -->
                        <div id="cart-message" class="hidden p-4 rounded-lg bg-green-100 text-green-700 font-medium text-center">
                            <i class="fas fa-check-circle mr-2"></i> Đã thêm vào giỏ hàng!
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Long Description -->
            <?php if ($content) : ?>
            <div class="mt-16 pt-10 border-t border-gray-200">
                <h2 class="text-2xl font-bold mb-6 text-gray-900">Chi tiết sản phẩm</h2>
                <div class="prose prose-lg max-w-none text-gray-700">
                    <?php echo wpautop($content); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script>
    function updateQty(change) {
        const input = document.getElementById('qty');
        let val = parseInt(input.value) + change;
        if (val < 1) val = 1;
        input.value = val;
    }

    function addToCart(productId) {
        const btn = document.getElementById('add-btn');
        const originalText = btn.innerHTML;
        const qty = parseInt(document.getElementById('qty').value);
        const msg = document.getElementById('cart-message');
        
        // Loading state
        btn.innerHTML = '<i class="fas fa-spinner fa-spin animate-spin"></i> Đang xử lý...';
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        
        // Data
        const formData = new FormData();
        formData.append('action', 'caremil_add_to_cart');
        formData.append('product_id', productId);
        formData.append('quantity', qty);
        formData.append('variant', 'default');
        
        // Nonce
        const nonce = '<?php echo wp_create_nonce("caremil_cart_nonce"); ?>';
        formData.append('nonce', nonce);
        
        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success UI
                btn.innerHTML = '<i class="fas fa-check"></i> Đang chuyển đến giỏ hàng...';
                btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                btn.classList.add('bg-green-600', 'hover:bg-green-700');
                
                msg.classList.remove('hidden');
                
                // Redirect to cart (optional) or update header
                setTimeout(() => {
                   window.location.href = '/gio-hang'; // Redirect to Cart
                }, 1000);
            } else {
                alert(data.data.message || 'Lỗi khi thêm vào giỏ hàng');
                resetBtn();
            }
        })
        .catch(err => {
            console.error(err);
            alert('Có lỗi xảy ra');
            resetBtn();
        });
        
        function resetBtn() {
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('opacity-75', 'cursor-not-allowed', 'bg-green-600', 'hover:bg-green-700');
            btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }
    }
    </script>

<?php else : ?>
    <!-- Product Not Found State -->
    <div class="container mx-auto px-4 py-20 text-center">
        <h1 class="text-4xl font-bold text-gray-300 mb-4">404</h1>
        <p class="text-xl text-gray-600 mb-8">Không tìm thấy sản phẩm này.</p>
        <a href="<?php echo home_url('/cua-hang/'); ?>" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-bold transition">
            Quay lại cửa hàng
        </a>
    </div>
<?php endif;

get_footer();