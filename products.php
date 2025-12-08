<?php
/**
 * Template Name: Products
 * Template Post Type: page
 * Description: Template for displaying products page
 *
 * @package Caremil
 */
get_header();

$caremil_product_cats = get_terms(
    array(
        'taxonomy'   => 'caremil_product_cat',
        'hide_empty' => false,
        'orderby'    => 'name',
    )
);

$caremil_products = new WP_Query(
    array(
        'post_type'      => 'caremil_product',
        'posts_per_page' => -1,
        'orderby'        => array(
            'menu_order' => 'ASC',
            'title'      => 'ASC',
        ),
    )
);
?>
<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa Hàng CareMIL - Dinh Dưỡng Cho Bé</title>
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
        /* Filter Tab Active State */
        .filter-btn.active {
            background-color: #1a4f8a; /* Brand Navy */
            color: white;
            border-color: #1a4f8a;
            box-shadow: 0 4px 10px rgba(26, 79, 138, 0.3);
            transform: translateY(-1px);
        }

        /* Product Card Hover Effect */
        .product-card { transition: all 0.3s ease; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .product-card:hover .add-to-cart-btn { opacity: 1; transform: translateY(0); }

        /* Add to Cart Button Animation */
        .add-to-cart-btn {
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            opacity: 0;
            transform: translateY(20px);
        }
        /* Always show button on mobile */
        @media (max-width: 768px) {
            .add-to-cart-btn { opacity: 1; transform: translateY(0); }
        }

        /* Animation */
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .animate-bounce-slow { animation: bounce-slow 2s infinite; }
    </style>
</head>
<body class="bg-gray-50 text-gray-700 font-sans">

    <!-- PAGE HEADER / BANNER -->
    <header class="bg-brand-soft relative overflow-hidden py-12 lg:py-16 mb-10">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-white/30 -skew-x-12 transform origin-top-right"></div>
        <div class="absolute bottom-[-20%] left-[-10%] w-64 h-64 bg-brand-blue/10 rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-display font-black text-brand-navy mb-4">Chọn Dinh Dưỡng Tốt Nhất Cho Con</h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto mb-8">Sản phẩm dinh dưỡng thực vật chuyên biệt, hỗ trợ tiêu hóa và phát triển trí não.</p>
            
            <!-- Promotion Banner Inside Header -->
            <div class="inline-flex items-center gap-3 bg-white px-6 py-3 rounded-full shadow-md border border-brand-gold/30 animate-bounce-slow">
                <span class="bg-brand-pink text-white text-xs font-bold px-2 py-1 rounded">HOT</span>
                <span class="text-brand-navy font-bold text-sm">Mua 2 Hộp tặng ngay 1 Gói dùng thử + Freeship toàn quốc!</span>
            </div>
        </div>
    </header>

    <!-- MAIN SHOP SECTION -->
    <div class="container mx-auto px-4 pb-20">
        
        <!-- Filter Tabs (Updated: Hộp Lớn) -->
        <div class="flex flex-wrap justify-center gap-3 mb-10">
            <button onclick="filterProducts('all', this)" class="filter-btn active px-6 py-2.5 rounded-full border-2 border-gray-100 font-bold text-gray-500 hover:border-brand-blue hover:text-brand-blue transition">Tất Cả</button>
            <?php if ( ! empty( $caremil_product_cats ) && ! is_wp_error( $caremil_product_cats ) ) : ?>
                <?php foreach ( $caremil_product_cats as $cat ) : ?>
                    <button onclick="filterProducts('<?php echo esc_attr( $cat->slug ); ?>', this)" class="filter-btn px-6 py-2.5 rounded-full border-2 border-gray-100 font-bold text-gray-500 hover:border-brand-blue hover:text-brand-blue transition">
                        <?php echo esc_html( $cat->name ); ?>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
            <?php if ( $caremil_products->have_posts() ) : ?>
                <?php
                while ( $caremil_products->have_posts() ) :
                    $caremil_products->the_post();

                    $price        = get_post_meta( get_the_ID(), 'caremil_price', true );
                    $old_price    = get_post_meta( get_the_ID(), 'caremil_old_price', true );
                    $badge        = get_post_meta( get_the_ID(), 'caremil_badge', true );
                    $badge_class  = get_post_meta( get_the_ID(), 'caremil_badge_class', true );
                    $short_desc   = get_post_meta( get_the_ID(), 'caremil_short_desc', true );
                    $rating       = get_post_meta( get_the_ID(), 'caremil_rating', true );
                    $rating_count = get_post_meta( get_the_ID(), 'caremil_rating_count', true );
                    $cta_label    = get_post_meta( get_the_ID(), 'caremil_button_label', true );
                    $cta_url      = get_post_meta( get_the_ID(), 'caremil_button_url', true );

                    $rating       = '' === $rating ? 5 : floatval( $rating );
                    $rating_count = '' === $rating_count ? 0 : intval( $rating_count );
                    $cta_label    = $cta_label ? $cta_label : __( 'Chọn Mua', 'caremil' );
                    $cta_url      = $cta_url ? $cta_url : get_permalink();
                    $badge_class  = $badge_class ? $badge_class : 'bg-brand-gold text-brand-navy';

                    $thumbnail = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                    $thumbnail = $thumbnail ? $thumbnail : 'https://via.placeholder.com/400x400?text=CareMIL';

                    $terms      = wp_get_post_terms( get_the_ID(), 'caremil_product_cat', array( 'fields' => 'slugs' ) );
                    $term_names = wp_get_post_terms( get_the_ID(), 'caremil_product_cat', array( 'fields' => 'names' ) );
                    $category   = ! empty( $terms ) ? $terms[0] : 'all';
                    ?>
                    <div class="product-card bg-white rounded-2xl border border-gray-100 p-4 relative group" data-category="<?php echo esc_attr( $category ); ?>">
                        <?php if ( $badge ) : ?>
                            <div class="absolute top-4 left-4 z-10">
                                <span class="<?php echo esc_attr( $badge_class ); ?> text-xs font-bold px-2 py-1 rounded shadow-sm"><?php echo esc_html( $badge ); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="absolute top-4 right-4 z-10">
                            <button class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-brand-pink shadow-sm flex items-center justify-center transition" aria-label="<?php esc_attr_e( 'Yêu thích', 'caremil' ); ?>"><i class="far fa-heart"></i></button>
                        </div>

                        <a href="<?php echo esc_url( $cta_url ); ?>" class="block relative bg-gray-50 rounded-xl p-6 mb-4 h-64 flex items-center justify-center overflow-hidden">
                            <img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="w-40 h-auto object-contain drop-shadow-lg transition duration-500 group-hover:scale-110">
                        </a>

                        <div class="text-center">
                            <div class="flex justify-center gap-1 text-brand-gold text-xs mb-2">
                                <?php
                                $full_stars = floor( $rating );
                                $half_star  = ( $rating - $full_stars ) >= 0.5;
                                for ( $i = 0; $i < $full_stars; $i++ ) {
                                    echo '<i class="fas fa-star"></i>';
                                }
                                if ( $half_star ) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                }
                                $empty = 5 - $full_stars - ( $half_star ? 1 : 0 );
                                for ( $i = 0; $i < $empty; $i++ ) {
                                    echo '<i class="far fa-star"></i>';
                                }
                                ?>
                                <span class="text-gray-400 ml-1">(<?php echo esc_html( $rating_count ); ?>)</span>
                            </div>
                            <h3 class="text-lg font-bold text-brand-navy mb-1 group-hover:text-brand-blue transition"><a href="<?php echo esc_url( $cta_url ); ?>"><?php the_title(); ?></a></h3>
                            <?php if ( $short_desc ) : ?>
                                <p class="text-xs text-gray-500 mb-3 line-clamp-2"><?php echo esc_html( $short_desc ); ?></p>
                            <?php endif; ?>

                            <div class="flex items-center justify-center gap-2 mb-4">
                                <?php if ( $price ) : ?>
                                    <span class="text-xl font-bold text-brand-pink"><?php echo esc_html( $price ); ?></span>
                                <?php endif; ?>
                                <?php if ( $old_price ) : ?>
                                    <span class="text-sm text-gray-400 line-through"><?php echo esc_html( $old_price ); ?></span>
                                <?php endif; ?>
                            </div>

                            <button onclick="window.location.href='<?php echo esc_url( $cta_url ); ?>'" class="add-to-cart-btn w-full bg-brand-navy text-white font-bold py-2.5 rounded-xl hover:bg-brand-blue transition shadow-lg flex items-center justify-center gap-2">
                                <i class="fas fa-shopping-bag"></i> <?php echo esc_html( $cta_label ); ?>
                            </button>
                        </div>
                        <?php if ( ! empty( $term_names ) ) : ?>
                            <span class="sr-only"><?php echo esc_html( implode( ', ', $term_names ) ); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="col-span-full text-center text-gray-500 bg-white rounded-2xl border border-dashed border-gray-200 p-10">
                    <?php esc_html_e( 'Chưa có sản phẩm nào. Hãy thêm sản phẩm trong trang quản trị.', 'caremil' ); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- WHY CHOOSE US -->
    <section class="bg-white py-12 border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="p-4"><div class="w-12 h-12 mx-auto bg-blue-50 rounded-full flex items-center justify-center text-brand-blue text-xl mb-3"><i class="fas fa-check-circle"></i></div><h4 class="font-bold text-brand-navy text-sm">Chính Hãng 100%</h4></div>
                <div class="p-4"><div class="w-12 h-12 mx-auto bg-green-50 rounded-full flex items-center justify-center text-green-500 text-xl mb-3"><i class="fas fa-truck"></i></div><h4 class="font-bold text-brand-navy text-sm">Giao Hàng Nhanh</h4></div>
                <div class="p-4"><div class="w-12 h-12 mx-auto bg-pink-50 rounded-full flex items-center justify-center text-brand-pink text-xl mb-3"><i class="fas fa-user-md"></i></div><h4 class="font-bold text-brand-navy text-sm">Tư Vấn Chuyên Gia</h4></div>
                <div class="p-4"><div class="w-12 h-12 mx-auto bg-yellow-50 rounded-full flex items-center justify-center text-brand-gold text-xl mb-3"><i class="fas fa-undo"></i></div><h4 class="font-bold text-brand-navy text-sm">Đổi Trả Dễ Dàng</h4></div>
            </div>
        </div>
    </section>

    <script>
        function filterProducts(category, btn) {
            document.querySelectorAll('.filter-btn').forEach(button => button.classList.remove('active'));
            if (btn) { btn.classList.add('active'); }

            const items = document.querySelectorAll('.product-card');
            items.forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                    setTimeout(() => { item.style.opacity = '1'; }, 50);
                } else {
                    item.style.opacity = '0';
                    setTimeout(() => { item.style.display = 'none'; }, 300);
                }
            });
        }
    </script>
<?php
get_footer();