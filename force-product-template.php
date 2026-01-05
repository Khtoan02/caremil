<?php
/**
 * Force single-product.php template for product post type
 */
function caremil_force_product_template($template) {
    if (is_singular('product')) {
        $product_template = locate_template('single-product.php');
        if ($product_template) {
            error_log('Loading single-product.php for product post type');
            return $product_template;
        }
    }
    return $template;
}
add_filter('template_include', 'caremil_force_product_template', 99);
