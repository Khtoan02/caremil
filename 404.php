<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package Caremil
 */

get_header();
?>

<main id="main" class="site-main flex-1 py-12 md:py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
        <section class="error-404 not-found text-center">
            <header class="page-header mb-8">
                <h1 class="page-title text-6xl md:text-8xl font-bold text-gray-300 mb-4">404</h1>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Trang không tìm thấy</h2>
            </header>

            <div class="page-content">
                <p class="text-lg text-gray-600 mb-8">Xin lỗi, trang bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.</p>
                
                <div class="mb-12 max-w-md mx-auto">
                    <?php get_search_form(); ?>
                </div>

                <div class="grid md:grid-cols-2 gap-8 text-left">
                    <div class="widget bg-white rounded-lg shadow-md p-6">
                        <h2 class="widget-title text-xl font-bold mb-4 text-gray-900">Có thể bạn quan tâm:</h2>
                        <?php the_widget( 'WP_Widget_Recent_Posts' ); ?>
                    </div>

                    <div class="widget bg-white rounded-lg shadow-md p-6">
                        <h2 class="widget-title text-xl font-bold mb-4 text-gray-900">Chuyên mục</h2>
                        <ul class="space-y-2">
                            <?php
                            wp_list_categories( array(
                                'orderby'    => 'count',
                                'order'      => 'DESC',
                                'show_count' => 1,
                                'title_li'   => '',
                                'number'     => 10,
                            ) );
                            ?>
                        </ul>
                    </div>
                </div>

                <div class="mt-8">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary inline-block">
                        Về trang chủ
                    </a>
                </div>
            </div>
        </section>
    </div>
</main>

<?php
get_footer();

