<?php
/**
 * WooCommerce Single Product Override
 *
 * Mục tiêu: tái sử dụng giao diện single-product.php có sẵn
 * cho trang chi tiết sản phẩm WooCommerce.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Gọi thẳng template single-product.php đã xây dựng sẵn ở root theme.
// File đó đã include get_header()/get_footer().
require get_template_directory() . '/single-product.php';


