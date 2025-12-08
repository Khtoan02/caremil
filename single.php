<?php
/**
 * The template for displaying single posts
 *
 * @package Caremil
 */

get_header();
?>

<main id="main" class="site-main flex-1 py-8 md:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'bg-white rounded-lg shadow-md overflow-hidden' ); ?>>
                <header class="entry-header p-6 md:p-8 border-b border-gray-200">
                    <h1 class="entry-title text-3xl md:text-4xl font-bold mb-4 text-gray-900">
                        <?php the_title(); ?>
                    </h1>
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
                        <?php
                        $categories = get_the_category();
                        if ( ! empty( $categories ) ) :
                            ?>
                            <span class="cat-links flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <?php
                                foreach ( $categories as $category ) {
                                    echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" class="text-blue-600 hover:text-blue-700 transition-colors duration-300">' . esc_html( $category->name ) . '</a>';
                                }
                                ?>
                            </span>
                            <?php
                        endif;
                        ?>
                    </div>
                </header>

                <?php
                if ( has_post_thumbnail() ) :
                    ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-auto' ) ); ?>
                    </div>
                    <?php
                endif;
                ?>

                <div class="entry-content p-6 md:p-8 prose prose-lg max-w-none">
                    <?php
                    the_content();

                    wp_link_pages( array(
                        'before' => '<div class="page-links mt-8 pt-6 border-t border-gray-200">' . __( 'Trang:', 'caremil' ),
                        'after'  => '</div>',
                    ) );
                    ?>
                </div>

                <footer class="entry-footer p-6 md:p-8 border-t border-gray-200 bg-gray-50">
                    <?php
                    $tags = get_the_tags();
                    if ( $tags ) :
                        ?>
                        <div class="tags-links">
                            <span class="text-sm font-medium text-gray-700 mb-2 block">Tags: </span>
                            <div class="flex flex-wrap gap-2">
                                <?php
                                foreach ( $tags as $tag ) {
                                    echo '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" class="inline-block px-3 py-1 text-sm bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors duration-300">' . esc_html( $tag->name ) . '</a>';
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    endif;
                    ?>
                </footer>
            </article>

            <?php
            // Post navigation
            the_post_navigation( array(
                'prev_text' => '<span class="nav-subtitle text-sm text-gray-600 block mb-1">← Bài trước:</span> <span class="nav-title font-medium">%title</span>',
                'next_text' => '<span class="nav-subtitle text-sm text-gray-600 block mb-1">Bài sau: →</span> <span class="nav-title font-medium">%title</span>',
                'class'     => 'mt-8 flex justify-between gap-4',
            ) );

            // Comments
            if ( comments_open() || get_comments_number() ) :
                ?>
                <div class="mt-8">
                    <?php comments_template(); ?>
                </div>
                <?php
            endif;

        endwhile;
        ?>
    </div>
</main>

<?php
get_sidebar();
get_footer();



