<?php
/**
 * The sidebar template file
 *
 * @package Caremil
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
    return;
}
?>

<aside id="secondary" class="widget-area sidebar">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <?php dynamic_sidebar( 'sidebar-1' ); ?>
    </div>
</aside>













