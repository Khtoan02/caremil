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
    <div class="site-container">
        <?php dynamic_sidebar( 'sidebar-1' ); ?>
    </div>
</aside>

