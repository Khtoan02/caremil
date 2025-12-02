<?php
/**
 * The template for displaying search results
 *
 * @package Caremil
 */

get_header();
?>

<main id="main" class="site-main flex-1 py-8 md:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <?php
        if ( have_posts() ) :
            ?>
            <header class="page-header mb-8">
                <h1 class="page-title text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                    Kết quả tìm kiếm cho: 
                    <span class="text-blue-600"><?php echo get_search_query(); ?></span>
                </h1>
            </header>

            <div class="grid gap-6 md:gap-8">
                <?php
                while ( have_posts() ) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300' ); ?>>
                        <div class="p-6">
                            <header class="entry-header mb-4">
                                <h2 class="entry-title text-2xl font-bold mb-2">
                                    <a href="<?php the_permalink(); ?>" class="text-gray-900 hover:text-blue-600 transition-colors duration-300">
                                        <?php the_title(); ?>
                                    </a>
                                </h2>
                                <div class="entry-meta text-sm text-gray-600">
                                    <span class="posted-on">
                                        <?php echo get_the_date(); ?>
                                    </span>
                                </div>
                            </header>

                            <div class="entry-content text-gray-700">
                                <?php the_excerpt(); ?>
                            </div>
                        </div>
                    </article>
                    <?php
                endwhile;
                ?>
            </div>

            <?php
            the_posts_pagination( array(
                'mid_size'  => 2,
                'prev_text' => '← Trước',
                'next_text' => 'Sau →',
                'class'     => 'mt-8 flex justify-center',
            ) );
            ?>

        <?php
        else :
            ?>
            <header class="page-header text-center mb-8">
                <h1 class="page-title text-3xl md:text-4xl font-bold text-gray-900 mb-4">Không tìm thấy kết quả</h1>
            </header>

            <div class="page-content max-w-md mx-auto text-center">
                <p class="text-gray-600 mb-6">Xin lỗi, không có kết quả nào phù hợp với từ khóa của bạn. Vui lòng thử lại với từ khóa khác.</p>
                <?php get_search_form(); ?>
            </div>
            <?php
        endif;
        ?>
    </div>
</main>

<?php
get_footer();

