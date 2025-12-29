    <?php
/**
 * Template Name: Single Product
 * Template Post Type: page
 * Description: Template for displaying single product page
 *
 * @package Caremil
 */
get_header();

$product_slug   = get_query_var( 'caremil_product' );
$product_data   = get_query_var( 'caremil_product_data' );

$fallback_data = array(
    'name'            => 'CareMIL Dinh Dưỡng Thực Vật',
    'default_variant' => 'box',
    'variants'        => array(
        'box'    => array(
            'label'     => 'Hộp Lớn 800g',
            'price'     => '850.000đ',
            'oldPrice'  => '1.000.000đ',
            'image'     => 'https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-chuan.png',
        ),
        'sachet' => array(
            'label'     => 'Hộp 10 Gói',
            'price'     => '350.000đ',
            'oldPrice'  => '450.000đ',
            'image'     => 'https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Goi-sua.png',
        ),
    ),
);

$is_caremil_post = is_singular( 'caremil_product' );

// Nếu là CPT caremil_product và chưa có data inject thì lấy từ post & meta
if ( $is_caremil_post && ( empty( $product_data ) || empty( $product_data['variants'] ) ) ) {
    $post_id   = get_the_ID();
    $title     = get_the_title( $post_id );
    $price     = get_post_meta( $post_id, 'caremil_price', true );
    $old_price = get_post_meta( $post_id, 'caremil_old_price', true );
    $image     = get_the_post_thumbnail_url( $post_id, 'large' );
    $image     = $image ? $image : 'https://via.placeholder.com/800x800?text=CareMIL';

    $product_data = array(
        'name'            => $title ?: $fallback_data['name'],
        'default_variant' => 'main',
        'variants'        => array(
            'main' => array(
                'label'    => $title ?: $fallback_data['variants']['box']['label'],
                'price'    => $price ?: $fallback_data['variants']['box']['price'],
                'oldPrice' => $old_price ?: $fallback_data['variants']['box']['oldPrice'],
                'image'    => $image,
            ),
        ),
    );

    if ( empty( $product_slug ) ) {
        $product_slug = get_post_field( 'post_name', $post_id );
    }
}

if ( empty( $product_data ) || empty( $product_data['variants'] ) ) {
    $product_data = $fallback_data;
}

$variants        = $product_data['variants'];
$default_variant = ! empty( $product_data['default_variant'] ) && isset( $variants[ $product_data['default_variant'] ] ) ? $product_data['default_variant'] : array_key_first( $variants );
$active_variant  = $variants[ $default_variant ];
$product_name    = ! empty( $product_data['name'] ) ? $product_data['name'] : $fallback_data['name'];
$related_products = array();

// Related: ưu tiên CPT cùng taxonomy
if ( $is_caremil_post ) {
    $tax_query = array();
    $terms     = wp_get_post_terms( get_the_ID(), 'caremil_product_cat', array( 'fields' => 'slugs' ) );
    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        $tax_query = array(
            array(
                'taxonomy' => 'caremil_product_cat',
                'field'    => 'slug',
                'terms'    => $terms,
            ),
        );
    }

    $related_query = new WP_Query(
        array(
            'post_type'      => 'caremil_product',
            'posts_per_page' => 4,
            'post__not_in'   => array( get_the_ID() ),
            'tax_query'      => $tax_query,
        )
    );

    if ( $related_query->have_posts() ) {
        while ( $related_query->have_posts() ) {
            $related_query->the_post();
            $rid        = get_the_ID();
            $r_slug     = get_post_field( 'post_name', $rid );
            $r_image    = get_the_post_thumbnail_url( $rid, 'medium' );
            $r_image    = $r_image ? $r_image : 'https://via.placeholder.com/400x400?text=CareMIL';
            $r_price    = get_post_meta( $rid, 'caremil_price', true );
            $related_products[ $r_slug ] = array(
                'name'     => get_the_title( $rid ),
                'variants' => array(
                    'main' => array(
                        'price' => $r_price ?: '',
                        'image' => $r_image,
                    ),
                ),
                'default_variant' => 'main',
            );
        }
        wp_reset_postdata();
    }
}

// Nếu không có CPT related, fallback custom map
if ( empty( $related_products ) && function_exists( 'caremil_get_custom_products' ) ) {
    $related_products = caremil_get_custom_products();
    unset( $related_products[ $product_slug ] );
    // Giới hạn 4 item
    $related_products = array_slice( $related_products, 0, 4, true );
}
?>
<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( $product_name ); ?> - CareMIL</title>
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
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(76, 201, 240, 0.3)',
                        'card': '0 5px 15px rgba(0,0,0,0.05)',
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom Input Number */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        
        /* Variant Selection */
        .variant-option { transition: all 0.3s ease; border: 2px solid transparent; }
        .variant-option.active { border-color: #4cc9f0; background-color: #f0f9ff; }
        
        /* Tab Animations */
        .tab-content { display: none; animation: fadeIn 0.4s ease; }
        .tab-content.active { display: block; }
        .tab-btn { position: relative; color: #94a3b8; }
        .tab-btn.active { color: #1a4f8a; }
        .tab-btn.active::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 100%; height: 3px; background-color: #ffd166; border-radius: 3px; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Zoom Effect */
        .zoom-container { overflow: hidden; cursor: zoom-in; }
        .zoom-container img { transition: transform 0.5s ease; }
        .zoom-container:hover img { transform: scale(1.5); }
    </style>
</head>
<body class="bg-gray-50 text-gray-700 font-sans pb-20">

    <!-- BREADCRUMB & BACK -->
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center gap-2 text-sm font-bold text-gray-400">
            <a href="<?php echo esc_url( home_url( '/san-pham' ) ); ?>" class="hover:text-brand-blue transition"><i class="fas fa-arrow-left mr-1"></i> Quay lại Cửa Hàng</a>
            <span class="text-gray-300">/</span>
            <span class="text-brand-navy"><?php echo esc_html( $product_name ); ?></span>
        </div>
    </div>

    <!-- MAIN PRODUCT DETAIL -->
    <section class="mb-12">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-[40px] shadow-card border border-gray-100 p-6 lg:p-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">
                    
                    <!-- LEFT: PRODUCT IMAGES -->
                    <div class="space-y-6">
                        <!-- Main Image Stage -->
                        <div class="relative bg-brand-soft/20 rounded-3xl p-8 aspect-square flex items-center justify-center group border border-brand-soft/50">
                            <!-- Discount Badge -->
                            <div class="absolute top-4 left-4 z-20 flex flex-col gap-2">
                                <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md animate-pulse">-15%</span>
                                <span class="bg-brand-gold text-brand-navy text-xs font-bold px-3 py-1 rounded-full shadow-md">Freeship</span>
                            </div>
                            
                            <!-- Image with Zoom -->
                            <div class="zoom-container w-full h-full flex items-center justify-center relative z-10" id="main-image-container">
                                <img id="main-product-image" src="<?php echo esc_url( $active_variant['image'] ); ?>" 
                                     alt="<?php echo esc_attr( $product_name ); ?>" 
                                     class="h-auto object-contain drop-shadow-2xl <?php echo ( 'sachet' === $default_variant ) ? 'rotate-12 w-1/2' : 'w-3/4'; ?>">
                            </div>
                            
                            <!-- Background Decor -->
                            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-2/3 h-2/3 bg-white rounded-full filter blur-3xl opacity-60 -z-0"></div>
                        </div>

                        <!-- Thumbnails -->
                         <div class="flex justify-center gap-4">
                             <?php foreach ( $variants as $key => $variant ) : ?>
                                 <button onclick="changeVariant('<?php echo esc_attr( $key ); ?>')" class="thumb-btn w-20 h-20 rounded-2xl border-2 <?php echo $key === $default_variant ? 'border-brand-blue' : 'border-transparent'; ?> p-2 cursor-pointer bg-white shadow-sm hover:scale-105 transition <?php echo $key === $default_variant ? 'active' : ''; ?>">
                                     <img src="<?php echo esc_url( $variant['image'] ); ?>" loading="lazy" decoding="async" class="w-full h-full object-contain <?php echo 'sachet' === $key ? 'transform rotate-12' : ''; ?>">
                                 </button>
                             <?php endforeach; ?>
                         </div>
                    </div>

                    <!-- RIGHT: INFO & ACTIONS -->
                    <div class="flex flex-col justify-center">
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="flex text-yellow-400 text-sm">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <span class="text-xs text-gray-400 font-bold underline cursor-pointer">Xem 128 đánh giá</span>
                            </div>
                            <h1 class="text-3xl md:text-4xl lg:text-5xl font-display font-black text-brand-navy leading-tight mb-3"><?php echo esc_html( $product_name ); ?></h1>
                            <p class="text-gray-500 font-medium text-sm md:text-base leading-relaxed">
                                Công thức chuyên biệt cho trẻ nhạy cảm. <span class="text-brand-pink font-bold">Không Gluten - Không Casein</span>. Giúp tiêu hóa êm, ngủ ngon và phát triển hành vi tích cực.
                            </p>
                        </div>

                        <!-- Price Block -->
                        <div class="bg-gray-50 rounded-2xl p-5 mb-8 border border-gray-100">
                            <p class="text-sm text-gray-500 font-bold mb-1">Giá ưu đãi:</p>
                            <div class="flex items-end gap-3">
                                <span id="display-price" class="text-4xl font-display font-black text-brand-pink"><?php echo esc_html( $active_variant['price'] ); ?></span>
                                <span id="display-old-price" class="text-xl text-gray-400 line-through mb-1 font-bold"><?php echo esc_html( $active_variant['oldPrice'] ); ?></span>
                                <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded mb-2">Tiết kiệm 15%</span>
                            </div>
                        </div>

                        <!-- Variants -->
                        <div class="mb-8">
                            <label class="block text-sm font-bold text-brand-navy mb-3 uppercase tracking-wider">Chọn Quy Cách:</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <?php foreach ( $variants as $key => $variant ) : ?>
                                    <button onclick="changeVariant('<?php echo esc_attr( $key ); ?>')" id="btn-<?php echo esc_attr( $key ); ?>" class="variant-option <?php echo $key === $default_variant ? 'active' : ''; ?> relative flex items-center gap-3 p-3 rounded-xl cursor-pointer bg-white hover:shadow-md text-left">
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg p-1 flex-shrink-0"><img src="<?php echo esc_url( $variant['image'] ); ?>" class="w-full h-full object-contain <?php echo 'sachet' === $key ? 'rotate-12' : ''; ?>"></div>
                                        <div>
                                            <span class="block font-bold text-brand-navy"><?php echo esc_html( $variant['label'] ); ?></span>
                                            <span class="block text-xs text-gray-500"><?php echo esc_html( $variant['price'] ); ?></span>
                                        </div>
                                        <div class="absolute top-2 right-2 text-brand-blue <?php echo $key === $default_variant ? 'opacity-100' : 'opacity-0'; ?> check-icon"><i class="fas fa-check-circle"></i></div>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-4">
                            <!-- Quantity -->
                            <div class="flex items-center bg-white border-2 border-gray-200 rounded-full h-14 px-4 w-32">
                                <button onclick="updateQty(-1)" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-brand-navy"><i class="fas fa-minus"></i></button>
                                <input type="number" id="qty" value="1" min="1" class="w-full text-center font-bold text-lg bg-transparent focus:outline-none" readonly>
                                <button onclick="updateQty(1)" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-brand-navy"><i class="fas fa-plus"></i></button>
                            </div>
                            
                            <!-- Buy Button -->
                            <button onclick="addToCart()" class="flex-1 bg-brand-navy text-white font-bold text-lg rounded-full h-14 shadow-lg hover:bg-brand-blue transition transform hover:-translate-y-1 flex items-center justify-center gap-3 add-to-cart-btn">
                                <i class="fas fa-shopping-bag"></i> 
                                <span>Mua Ngay</span>
                            </button>
                        </div>
                        
                        <!-- Trust Badges -->
                        <div class="mt-8 grid grid-cols-2 gap-4 text-xs font-bold text-gray-500">
                            <div class="flex items-center gap-2"><i class="fas fa-truck-fast text-brand-blue text-lg"></i> Giao hàng toàn quốc 2-3 ngày</div>
                            <div class="flex items-center gap-2"><i class="fas fa-medal text-brand-gold text-lg"></i> Hàng chính hãng 100%</div>
                            <div class="flex items-center gap-2"><i class="fas fa-box-open text-brand-pink text-lg"></i> Kiểm tra hàng trước khi nhận</div>
                            <div class="flex items-center gap-2"><i class="fas fa-headset text-green-500 text-lg"></i> Dược sĩ tư vấn 24/7</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- DETAILS TABS (Simplified) -->
    <section class="mb-16">
        <div class="container mx-auto px-4 max-w-5xl">
            <!-- Tabs Nav -->
            <div class="flex flex-wrap justify-center border-b-2 border-gray-100 mb-8">
                <button onclick="openTab('desc')" id="tab-desc" class="tab-btn active px-6 py-3 font-bold text-lg hover:text-brand-navy transition">Mô Tả Sản Phẩm</button>
                <button onclick="openTab('usage')" id="tab-usage" class="tab-btn px-6 py-3 font-bold text-lg hover:text-brand-navy transition">Hướng Dẫn Sử Dụng</button>
            </div>

            <!-- Tab: Description -->
            <div id="content-desc" class="tab-content active bg-white p-6 lg:p-10 rounded-3xl border border-gray-100 shadow-sm text-gray-600 leading-relaxed space-y-6">
                <p><strong>CareMIL</strong> là thức uống dinh dưỡng công thức thực vật hoàn chỉnh đầu tiên tại Malaysia được thiết kế chuyên biệt cho trẻ em có nhu cầu đặc biệt (Special Needs Children). Với công thức <strong>Hypoallergenic (Ít gây dị ứng)</strong>, CareMIL loại bỏ hoàn toàn các tác nhân gây viêm như Gluten, Casein, Đậu nành, giúp làm dịu đường ruột và hỗ trợ kết nối Não - Ruột khỏe mạnh.</p>
                
                <h3 class="text-xl font-bold text-brand-navy mt-4">Công Dụng Vượt Trội:</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex gap-3 bg-blue-50 p-4 rounded-xl">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-brand-blue shadow-sm flex-shrink-0"><i class="fas fa-brain"></i></div>
                        <div><strong class="block text-brand-navy">Hỗ trợ hành vi</strong><span class="text-sm">Giảm tăng động, lo âu, cải thiện sự tập trung nhờ Lysine & Choline.</span></div>
                    </div>
                    <div class="flex gap-3 bg-green-50 p-4 rounded-xl">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-green-500 shadow-sm flex-shrink-0"><i class="fas fa-leaf"></i></div>
                        <div><strong class="block text-brand-navy">Tiêu hóa khỏe</strong><span class="text-sm">Chất xơ Fibregum™ nuôi dưỡng lợi khuẩn, giảm táo bón/tiêu chảy.</span></div>
                    </div>
                    <div class="flex gap-3 bg-yellow-50 p-4 rounded-xl">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-brand-gold shadow-sm flex-shrink-0"><i class="fas fa-shield-virus"></i></div>
                        <div><strong class="block text-brand-navy">Tăng đề kháng</strong><span class="text-sm">24 Vitamin & Khoáng chất cùng L-Glutamine bảo vệ cơ thể.</span></div>
                    </div>
                    <div class="flex gap-3 bg-pink-50 p-4 rounded-xl">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-brand-pink shadow-sm flex-shrink-0"><i class="fas fa-utensils"></i></div>
                        <div><strong class="block text-brand-navy">Kích thích ăn ngon</strong><span class="text-sm">Vị Vani thơm nhẹ, dễ uống, giúp bé hợp tác ngay lần đầu.</span></div>
                    </div>
                </div>
            </div>

            <!-- Tab: Usage -->
            <div id="content-usage" class="tab-content bg-white p-6 lg:p-10 rounded-3xl border border-gray-100 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                    <div>
                        <h3 class="text-xl font-bold text-brand-navy mb-4">Cách Pha Chuẩn (180ml sữa)</h3>
                        <ol class="space-y-4">
                            <li class="flex gap-4">
                                <div class="w-8 h-8 rounded-full bg-brand-blue text-white flex items-center justify-center font-bold flex-shrink-0">1</div>
                                <p>Rửa sạch tay và tiệt trùng dụng cụ pha chế.</p>
                            </li>
                            <li class="flex gap-4">
                                <div class="w-8 h-8 rounded-full bg-brand-pink text-white flex items-center justify-center font-bold flex-shrink-0">2</div>
                                <p>Chuẩn bị <strong>180ml</strong> nước ấm (khoảng <strong>45-50°C</strong>). <br><em class="text-xs text-red-500">*Không dùng nước sôi làm chết lợi khuẩn.</em></p>
                            </li>
                            <li class="flex gap-4">
                                <div class="w-8 h-8 rounded-full bg-brand-gold text-white flex items-center justify-center font-bold flex-shrink-0">3</div>
                                <p>Cho <strong>3 muỗng gạt (36g)</strong> hoặc <strong>1 gói</strong> bột vào ly.</p>
                            </li>
                            <li class="flex gap-4">
                                <div class="w-8 h-8 rounded-full bg-brand-green text-white flex items-center justify-center font-bold flex-shrink-0">4</div>
                                <p>Khuấy đều cho tan hết và cho bé dùng ngay.</p>
                            </li>
                        </ol>
                    </div>
                    <div class="bg-brand-cream p-6 rounded-2xl border-2 border-white shadow-inner text-center">
                        <i class="fas fa-box-open text-4xl text-brand-gold mb-3"></i>
                        <h4 class="font-bold text-brand-navy mb-2">Bảo Quản</h4>
                        <p class="text-sm text-gray-600 mb-2">Bảo quản nơi khô ráo, thoáng mát.</p>
                        <p class="text-sm text-gray-600"><strong>Hộp đã mở:</strong> Dùng hết trong 3 tuần. Đóng kín nắp sau khi dùng.</p>
                        <p class="text-sm text-gray-600 mt-2 font-bold text-red-500">KHÔNG BẢO QUẢN TRONG TỦ LẠNH</p>
                    </div>
                </div>
            </div>
            
        </div>
    </section>

    <!-- RELATED PRODUCTS -->
     <?php if ( ! empty( $related_products ) ) : ?>
         <section class="py-12 bg-white border-t border-gray-100">
             <div class="container mx-auto px-4">
                 <h3 class="text-2xl font-display font-bold text-brand-navy text-center mb-8">Có Thể Mẹ Quan Tâm</h3>
                 <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                     <?php foreach ( $related_products as $slug => $item ) : 
                         $item_variants = isset( $item['variants'] ) ? $item['variants'] : array();
                         $item_variant  = ! empty( $item['default_variant'] ) && isset( $item_variants[ $item['default_variant'] ] )
                            ? $item_variants[ $item['default_variant'] ]
                            : ( $item_variants ? reset( $item_variants ) : array( 'price' => '', 'image' => '' ) );
                     ?>
                         <a href="<?php echo esc_url( home_url( '/san-pham/' . $slug ) ); ?>" class="group bg-white rounded-2xl border border-gray-100 p-4 hover:shadow-lg transition">
                             <div class="bg-gray-50 rounded-xl p-4 mb-4 flex items-center justify-center h-48">
                                 <?php if ( ! empty( $item_variant['image'] ) ) : ?>
                                     <img src="<?php echo esc_url( $item_variant['image'] ); ?>" class="w-24 h-auto object-contain group-hover:scale-110 transition duration-500" loading="lazy" decoding="async">
                                 <?php endif; ?>
                             </div>
                             <h4 class="font-bold text-brand-navy mb-1 group-hover:text-brand-blue"><?php echo esc_html( $item['name'] ); ?></h4>
                             <?php if ( ! empty( $item_variant['price'] ) ) : ?>
                                 <span class="text-brand-pink font-bold"><?php echo esc_html( $item_variant['price'] ); ?></span>
                             <?php endif; ?>
                         </a>
                     <?php endforeach; ?>
                 </div>
             </div>
         </section>
     <?php endif; ?>

    <!-- STICKY MOBILE BOTTOM BAR -->
    <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 p-3 md:hidden flex items-center justify-between shadow-[0_-5px_15px_rgba(0,0,0,0.05)] z-50">
        <div class="flex flex-col">
            <span class="text-xs text-gray-500">Tổng cộng:</span>
            <span class="text-lg font-bold text-brand-pink" id="sticky-price"><?php echo esc_html( $active_variant['price'] ); ?></span>
        </div>
        <button class="bg-brand-navy text-white font-bold py-2 px-8 rounded-full shadow-md hover:bg-blue-900 transition" onclick="addToCart()">
            Mua Ngay
        </button>
    </div>

    <script>
        // PRODUCT DATA (render từ PHP)
        const products = <?php echo wp_json_encode( $variants ); ?>;
        let currentVariant = '<?php echo esc_js( $default_variant ); ?>';

        // Handle Variant Selection
        function changeVariant(variant) {
            currentVariant = variant;
            const data = products[variant];

            // Update UI Buttons
            document.querySelectorAll('.variant-option').forEach(el => {
                el.classList.remove('active');
                el.querySelector('.check-icon').style.opacity = '0';
            });
            document.getElementById('btn-' + variant).classList.add('active');
            document.getElementById('btn-' + variant).querySelector('.check-icon').style.opacity = '1';

            // Update Price
            document.getElementById('display-price').textContent = data.price;
            document.getElementById('sticky-price').textContent = data.price;
            document.getElementById('display-old-price').textContent = data.oldPrice;

            // Update Image with Animation
            const img = document.getElementById('main-product-image');
            img.style.opacity = '0';
            img.style.transform = 'scale(0.9)';
            
            setTimeout(() => {
                img.src = data.image;
                img.style.opacity = '1';
                img.style.transform = 'scale(1)';
                
                if(variant === 'sachet') {
                    img.classList.add('rotate-12', 'w-1/2'); 
                    img.classList.remove('w-3/4');
                } else {
                    img.classList.remove('rotate-12', 'w-1/2');
                    img.classList.add('w-3/4');
                }
            }, 200);
        }

        // Handle Image Thumb Click
        function selectImage(btn, variant) {
            changeVariant(variant);
        }

        // Handle Quantity
        function updateQty(change) {
            const input = document.getElementById('qty');
            let val = parseInt(input.value) + change;
            if(val < 1) val = 1;
            input.value = val;
        }

        // Handle Info Tabs
        function openTab(tabName) {
            // Hide all
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active', 'text-brand-navy'));
            
            // Show active
            document.getElementById('content-' + tabName).classList.add('active');
            const btn = document.getElementById('tab-' + tabName);
            btn.classList.add('active', 'text-brand-navy');
        }

        // Add to Cart Function
        function addToCart() {
            const qty = parseInt(document.getElementById('qty').value) || 1;
            const variantData = products[currentVariant];
            const productId = <?php echo $is_caremil_post ? get_the_ID() : 0; ?>;
            
            if (productId <= 0) {
                alert('Sản phẩm không hợp lệ');
                return;
            }
            
            const btn = document.querySelector('.add-to-cart-btn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
            
            // Prepare data
            const formData = new FormData();
            formData.append('action', 'caremil_add_to_cart');
            formData.append('nonce', '<?php echo esc_js( wp_create_nonce( "caremil_cart_nonce" ) ); ?>');
            formData.append('product_id', productId);
            formData.append('quantity', qty);
            formData.append('variant', currentVariant);
            formData.append('variant_label', variantData.label);
            formData.append('price', variantData.price);
            formData.append('old_price', variantData.oldPrice || '');
            formData.append('image', variantData.image);
            
            fetch('<?php echo esc_js( admin_url( "admin-ajax.php" ) ); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    btn.innerHTML = '<i class="fas fa-check"></i> Đã thêm!';
                    btn.style.background = '#4ade80';
                    
                    // Update header cart count
                    const headerCount = document.getElementById('header-cart-count');
                    if (headerCount) {
                        headerCount.textContent = data.data.cart_count || '0';
                        headerCount.style.display = 'flex';
                    }
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.style.background = '';
                        btn.disabled = false;
                    }, 2000);
                    
                    // Optional: Redirect to cart or show notification
                    // window.location.href = '<?php echo esc_js( home_url( "/gio-hang" ) ); ?>';
                } else {
                    alert(data.data?.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thêm vào giỏ hàng');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }

        // Initialize with default variant from PHP
        window.addEventListener('DOMContentLoaded', () => {
            changeVariant(currentVariant);
        });
    </script>
<?php
get_footer();