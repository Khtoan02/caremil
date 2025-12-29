<?php
/**
 * The main template file
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
            <div class="grid gap-8 md:gap-12">
                <?php
                while ( have_posts() ) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300' ); ?>>
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-64 object-cover' ) ); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <header class="entry-header mb-4">
                                <h2 class="entry-title text-2xl md:text-3xl font-bold mb-3">
                                    <a href="<?php the_permalink(); ?>" class="text-gray-900 hover:text-blue-600 transition-colors duration-300">
                                        <?php the_title(); ?>
                                    </a>
                                </h2>
                                <div class="entry-meta flex flex-wrap gap-4 text-sm text-gray-600">
                                    <span class="posted-on flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <?php echo get_the_date(); ?>
                                    </span>
                                    <span class="byline flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <?php the_author(); ?>
                                    </span>
                                </div>
                            </header>

                            <div class="entry-content text-gray-700 mb-4">
                                <?php the_excerpt(); ?>
                            </div>

                            <footer class="entry-footer">
                                <a href="<?php the_permalink(); ?>" class="read-more inline-flex items-center text-blue-600 hover:text-blue-700 font-medium transition-colors duration-300">
                                    Đọc thêm
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </footer>
                        </div>
                    </article>
                    <?php
                endwhile;
                ?>
            </div>

            <?php
            // Pagination
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
            <div class="no-posts text-center py-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Không tìm thấy bài viết</h2>
                <p class="text-gray-600">Xin lỗi, không có bài viết nào được tìm thấy.</p>
            </div>
            <?php
        endif;
        ?>
    </div>
</main>

<?php
get_footer();













