<?php
/**
 * The template for displaying all pages
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
                    <h1 class="entry-title text-3xl md:text-4xl font-bold text-gray-900">
                        <?php the_title(); ?>
                    </h1>
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

                <?php
                if ( comments_open() || get_comments_number() ) :
                    ?>
                    <footer class="entry-footer p-6 md:p-8 border-t border-gray-200 bg-gray-50">
                        <?php comments_template(); ?>
                    </footer>
                    <?php
                endif;
                ?>
            </article>
            <?php
        endwhile;
        ?>
    </div>
</main>

<?php
get_sidebar();
get_footer();













