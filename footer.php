<?php
/**
 * The footer template file
 *
 * @package Caremil
 */
?>

    <footer id="colophon" class="site-footer bg-gray-50 border-t border-gray-200 mt-auto">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <?php
            if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) ) :
                ?>
                <div class="footer-widgets grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="footer-widget-area">
                        <?php dynamic_sidebar( 'footer-1' ); ?>
                    </div>
                    <div class="footer-widget-area">
                        <?php dynamic_sidebar( 'footer-2' ); ?>
                    </div>
                </div>
                <?php
            endif;
            ?>

            <div class="site-info text-center text-sm text-gray-600 pt-6 border-t border-gray-200">
                <p>
                    &copy; <?php echo date( 'Y' ); ?> 
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-blue-600 hover:text-blue-700 transition-colors duration-300">
                        <?php bloginfo( 'name' ); ?>
                    </a>
                    . Tất cả quyền được bảo lưu.
                </p>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>

