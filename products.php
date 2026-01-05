<?php
/**
 * Template Name: Products
 * Template Post Type: page
 * Description: Template for displaying products page - MODERN PROFESSIONAL
 *
 * @package Dawnbridge
 */
get_header();

$product_categorys = get_terms(
    array(
        'taxonomy'   => 'product_category',
        'hide_empty' => false,
        'orderby'    => 'name',
    )
);

$caremil_products = new WP_Query(
    array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => array(
            'menu_order' => 'ASC',
            'title'      => 'ASC',
        ),
    )
);
?>

<style>
    /* Modern Filter Tabs */
    .filter-btn {
        transition: all 0.3s ease;
    }
    .filter-btn.active {
        background-color: #0f172a;
        color: white;
        border-color: #0f172a;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.2);
    }

    /* Product Card Animation */
    .product-card {
        transition: all 0.3s ease;
        opacity: 0;
        animation: fadeIn 0.5s ease forwards;
    }
    @keyframes fadeIn {
        to { opacity: 1; }
    }
    
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }

    /* Add to Cart Button */
    .add-to-cart-btn {
        transition: all 0.3s ease;
    }
</style>

<!-- PAGE HEADER -->
<section class="bg-gradient-to-br from-primary-900 to-primary-800 text-white py-16 lg:py-20">
    <div class="container mx-auto px-6 lg:px-8 text-center">
        <h1 class="text-3xl lg:text-4xl xl:text-5xl font-bold mb-4">Our Product Catalog</h1>
        <p class="text-lg text-gray-200 max-w-2xl mx-auto mb-8">
            Browse our selection of premium health and wellness products
        </p>
        
        <?php if ( $caremil_products->found_posts > 0 ) : ?>
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full border border-white/20">
                <i class="fas fa-box text-success-500"></i>
                <span class="font-medium"><?php echo esc_html( $caremil_products->found_posts ); ?> Products Available</span>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- MAIN SHOP SECTION -->
<div class="container mx-auto px-6 lg:px-8 py-12 lg:py-16">
    
    <!-- Filter Tabs -->
    <?php if ( ! empty( $product_categorys ) && ! is_wp_error( $product_categorys ) ) : ?>
        <div class="flex flex-wrap justify-center gap-3 mb-10">
            <button onclick="filterProducts('all', this)" class="filter-btn active px-6 py-2.5 rounded-lg border-2 border-gray-200 font-medium text-gray-600 hover:border-primary-900 hover:text-primary-900">
                All Products
            </button>
            <?php foreach ( $product_categorys as $cat ) : ?>
                <button onclick="filterProducts('<?php echo esc_attr( $cat->slug ); ?>', this)" class="filter-btn px-6 py-2.5 rounded-lg border-2 border-gray-200 font-medium text-gray-600 hover:border-primary-900 hover:text-primary-900">
                    <?php echo esc_html( $cat->name ); ?>
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

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
                $cta_label    = $cta_label ? $cta_label : __( 'View Product', 'caremil' );
                $cta_url      = $cta_url ? $cta_url : get_permalink();
                
                // Modern badge color - change old CareMIL colors to professional ones
                if ( strpos($badge_class, 'brand-gold') !== false || strpos($badge_class, 'brand-pink') !== false ) {
                    $badge_class = 'bg-accent-600 text-white';
                } else {
                    $badge_class = $badge_class ? $badge_class : 'bg-accent-600 text-white';
                }

                $thumbnail = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                $thumbnail = $thumbnail ? $thumbnail : 'https://via.placeholder.com/400x400?text=Product';

                $terms      = wp_get_post_terms( get_the_ID(), 'product_category', array( 'fields' => 'slugs' ) );
                $category   = ! empty( $terms ) ? $terms[0] : 'all';
                ?>
                <div class="product-card bg-white rounded-xl border border-gray-100 overflow-hidden group" data-category="<?php echo esc_attr( $category ); ?>">
                    <!-- Badge -->
                    <?php if ( $badge ) : ?>
                        <div class="absolute top-3 left-3 z-10">
                            <span class="<?php echo esc_attr( $badge_class ); ?> text-xs font-bold px-3 py-1 rounded-lg shadow-sm">
                                <?php echo esc_html( $badge ); ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <!-- Product Image -->
                    <a href="<?php echo esc_url( $cta_url ); ?>" class="block relative bg-gray-50 p-6 aspect-square flex items-center justify-center overflow-hidden">
                        <img src="<?php echo esc_url( $thumbnail ); ?>" 
                             alt="<?php echo esc_attr( get_the_title() ); ?>" 
                             class="w-full h-full object-contain transition duration-500 group-hover:scale-110"
                             loading="lazy">
                    </a>

                    <!-- Product Info -->
                    <div class="p-4">
                        <!-- Rating -->
                        <?php if ( $rating > 0 ) : ?>
                            <div class="flex items-center gap-1 text-yellow-500 text-xs mb-2">
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
                                <?php if ( $rating_count > 0 ) : ?>
                                    <span class="text-gray-400 ml-1">(<?php echo esc_html( $rating_count ); ?>)</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Title -->
                        <h3 class="text-base font-semibold text-primary-900 mb-2 line-clamp-2 group-hover:text-accent-600 transition">
                            <a href="<?php echo esc_url( $cta_url ); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h3>

                        <!-- Short Description -->
                        <?php if ( $short_desc ) : ?>
                            <p class="text-xs text-gray-500 mb-3 line-clamp-2"><?php echo esc_html( $short_desc ); ?></p>
                        <?php endif; ?>

                        <!-- Price -->
                        <div class="flex items-center gap-2 mb-4">
                            <?php if ( $price ) : ?>
                                <span class="text-xl font-bold text-primary-900"><?php echo esc_html( $price ); ?></span>
                            <?php endif; ?>
                            <?php if ( $old_price ) : ?>
                                <span class="text-sm text-gray-400 line-through"><?php echo esc_html( $old_price ); ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- CTA Button -->
                        <button onclick="viewProduct(<?php echo get_the_ID(); ?>); return false;" data-href='<?php echo esc_url( $cta_url ); ?>'" 
                                class="add-to-cart-btn w-full bg-primary-900 text-white font-medium py-2.5 rounded-lg hover:bg-accent-600 transition flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-bag text-sm"></i>
                            <span><?php echo esc_html( $cta_label ); ?></span>
                        </button>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        <?php else : ?>
            <div class="col-span-full text-center bg-white rounded-xl border-2 border-dashed border-gray-200 p-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-box-open text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-primary-900 mb-2">No Products Found</h3>
                <p class="text-gray-500">Please add products from the admin panel.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- WHY CHOOSE US SECTION -->
<section class="bg-gray-50 py-12 border-t border-gray-100 mt-12">
    <div class="container mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="p-4">
                <div class="w-14 h-14 mx-auto bg-accent-100 rounded-lg flex items-center justify-center text-accent-600 text-2xl mb-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4 class="font-semibold text-primary-900 text-sm">100% Authentic</h4>
                <p class="text-xs text-gray-500 mt-1">Genuine products only</p>
            </div>
            <div class="p-4">
                <div class="w-14 h-14 mx-auto bg-success-100 rounded-lg flex items-center justify-center text-success-600 text-2xl mb-3">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h4 class="font-semibold text-primary-900 text-sm">Fast Shipping</h4>
                <p class="text-xs text-gray-500 mt-1">Nationwide delivery</p>
            </div>
            <div class="p-4">
                <div class="w-14 h-14 mx-auto bg-primary-100 rounded-lg flex items-center justify-center text-primary-600 text-2xl mb-3">
                    <i class="fas fa-headset"></i>
                </div>
                <h4 class="font-semibold text-primary-900 text-sm">Expert Support</h4>
                <p class="text-xs text-gray-500 mt-1">24/7 assistance</p>
            </div>
            <div class="p-4">
                <div class="w-14 h-14 mx-auto bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600 text-2xl mb-3">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <h4 class="font-semibold text-primary-900 text-sm">Easy Returns</h4>
                <p class="text-xs text-gray-500 mt-1">30-day policy</p>
            </div>
        </div>
    </div>
</section>

<script>
    function filterProducts(category, btn) {
        // Update active button
        document.querySelectorAll('.filter-btn').forEach(button => button.classList.remove('active'));
        if (btn) { btn.classList.add('active'); }

        // Filter products
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
include(get_template_directory() . '/product-modal-script.php');
get_footer();
