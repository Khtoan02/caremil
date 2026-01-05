<?php
/**
 * Product Archive Template
 */

get_header();
?>

<div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold mb-8">Sản phẩm</h1>
    
    <?php if (have_posts()) : ?>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php while (have_posts()) : the_post(); 
                $price = get_post_meta(get_the_ID(), 'caremil_price', true);
                $sku = get_post_meta(get_the_ID(), 'pancake_sku', true);
                $image = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <a href="<?php the_permalink(); ?>">
                        <?php if ($image) : ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>" class="w-full h-64 object-cover">
                        <?php else : ?>
                            <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">No Image</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-4">
                            <h2 class="text-lg font-semibold mb-2"><?php the_title(); ?></h2>
                            
                            <?php if ($price) : ?>
                                <p class="text-2xl font-bold text-blue-600"><?php echo esc_html($price); ?>đ</p>
                            <?php endif; ?>
                            
                            <?php if ($sku) : ?>
                                <p class="text-sm text-gray-500 mt-2">SKU: <?php echo esc_html($sku); ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <p class="text-center text-gray-500">Chưa có sản phẩm nào.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
