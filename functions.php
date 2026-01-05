<?php
/**
 * Caremil Theme Functions
 *
 * @package Caremil
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Fallback API Key đặt trong theme (ít an toàn hơn ENV/wp-config, dùng theo yêu cầu).
if ( ! defined( 'CAREMIL_SEPAY_DEFAULT_KEY' ) ) {
    define( 'CAREMIL_SEPAY_DEFAULT_KEY', 'SNTUPDJ9NMDWKOUOIE8YRFGI3CCO7GFJVLN6GDZ20QTPG4T5M1JHBX0MUPB84UHF' );
}

// Include product sync module
require_once get_template_directory() . '/product-sync.php';

// Include product menu integration
require_once get_template_directory() . '/product-menu-integration.php';

// Include custom product editor
require_once get_template_directory() . '/product-editor.php';


// Include Coupon Management System
require_once get_template_directory() . '/includes/coupons.php';

// Include new Order API
require_once get_template_directory() . '/includes/order-api.php';

// Include Viettel Post Shipping Calculator
require_once get_template_directory() . '/includes/viettel-shipping.php';

/**
 * Theme Setup
 */
function caremil_setup() {
    // Add theme support for title tag
    add_theme_support( 'title-tag' );

    // Add theme support for post thumbnails
    add_theme_support( 'post-thumbnails' );

    // Add theme support for automatic feed links
    add_theme_support( 'automatic-feed-links' );

    // Add theme support for HTML5 markup
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );

    // Register navigation menus
    register_nav_menus( array(
        'primary' => __( 'Menu Chính', 'caremil' ),
        'footer'  => __( 'Menu Footer', 'caremil' ),
    ) );

    // Set content width
    if ( ! isset( $content_width ) ) {
        $content_width = 1200;
    }
}
add_action( 'after_setup_theme', 'caremil_setup' );

/**
 * Add Tailwind CDN script directly in head
 */
function caremil_add_tailwind_cdn() {
    ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php
}
add_action( 'wp_head', 'caremil_add_tailwind_cdn', 1 );

/**
 * Enqueue scripts and styles
 */
function caremil_scripts() {
    // Enqueue theme stylesheet (for custom overrides if needed)
    wp_enqueue_style( 'caremil-style', get_stylesheet_uri(), array(), '1.0.0' );

    // Enqueue theme script
    wp_enqueue_script( 'caremil-script', get_template_directory_uri() . '/js/main.js', array(), '1.0.0', true );

    // Enqueue comment reply script on single posts
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'caremil_scripts' );

/**
 * Register widget areas
 */
function caremil_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Sidebar', 'caremil' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Widgets hiển thị ở sidebar', 'caremil' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s mb-6 p-4 bg-white rounded-lg shadow">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title text-xl font-bold mb-3 text-gray-900">',
        'after_title'   => '</h2>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer 1', 'caremil' ),
        'id'            => 'footer-1',
        'description'   => __( 'Widgets hiển thị ở footer cột 1', 'caremil' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title text-lg font-bold mb-3 text-gray-900">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer 2', 'caremil' ),
        'id'            => 'footer-2',
        'description'   => __( 'Widgets hiển thị ở footer cột 2', 'caremil' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title text-lg font-bold mb-3 text-gray-900">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'caremil_widgets_init' );

/**
 * Custom excerpt length
 */
function caremil_excerpt_length( $length ) {
    return 30;
}
add_filter( 'excerpt_length', 'caremil_excerpt_length' );

/**
 * Custom excerpt more
 */
function caremil_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'caremil_excerpt_more' );

/**
 * Custom route: /san-pham/{slug} -> single-product.php
 */
function caremil_product_rewrite_rule() {
    add_rewrite_rule( '^san-pham/([^/]+)/?$', 'index.php?caremil_product=$matches[1]', 'top' );
}
add_action( 'init', 'caremil_product_rewrite_rule' );

function caremil_product_query_vars( $vars ) {
    $vars[] = 'caremil_product';
    return $vars;
}
add_filter( 'query_vars', 'caremil_product_query_vars' );

/**
 * Map slug ở /san-pham/{slug} về CPT caremil_product để dùng dữ liệu động.
 */
function caremil_product_route_to_cpt( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    $slug = $query->get( 'caremil_product' );
    if ( ! empty( $slug ) ) {
        $query->set( 'post_type', 'caremil_product' );
        $query->set( 'name', sanitize_title( $slug ) );
        $query->set( 'post_status', 'publish' );
        $query->is_single = true;
    }
}
add_action( 'pre_get_posts', 'caremil_product_route_to_cpt' );

/**
 * Data cho sản phẩm custom (không dùng Woo).
 * Bạn có thể chỉnh thêm ở đây.
 */
function caremil_get_custom_products() {
    // Ưu tiên lấy từ CPT caremil_product để đồng bộ UI admin.
    $cpt_products = get_posts(
        array(
            'post_type'      => 'caremil_product',
            'posts_per_page' => -1,
            'orderby'        => array(
                'menu_order' => 'ASC',
                'title'      => 'ASC',
            ),
            'post_status'    => 'publish',
        )
    );

    if ( $cpt_products ) {
        $mapped = array();
        foreach ( $cpt_products as $post ) {
            $slug      = $post->post_name;
            $title     = get_the_title( $post );
            $price     = get_post_meta( $post->ID, 'caremil_price', true );
            $old_price = get_post_meta( $post->ID, 'caremil_old_price', true );
            $image     = get_the_post_thumbnail_url( $post->ID, 'large' );
            $image     = $image ? $image : 'https://via.placeholder.com/800x800?text=CareMIL';

            $mapped[ $slug ] = array(
                'name'            => $title,
                'default_variant' => 'main',
                'variants'        => array(
                    'main' => array(
                        'label'    => $title,
                        'price'    => $price ? $price : '',
                        'oldPrice' => $old_price ? $old_price : '',
                        'image'    => $image,
                    ),
                ),
            );
        }

        if ( ! empty( $mapped ) ) {
            return $mapped;
        }
    }

    // Fallback seed data khi chưa có CPT.
    return array(
        'care-mil-lon-800g' => array(
            'name'            => 'CareMIL Hộp Lớn 800g',
            'default_variant' => 'box',
            'variants'        => array(
                'box'    => array(
                    'label'     => 'Hộp Lớn 800g',
                    'price'     => '850.000đ',
                    'oldPrice'  => '1.000.000đ',
                    'image'     => 'https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-chuan.png',
                ),
                'sachet' => array(
                    'label'     => 'Hộp 10 Gói',
                    'price'     => '350.000đ',
                    'oldPrice'  => '450.000đ',
                    'image'     => 'https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Goi-sua.png',
                ),
            ),
        ),
        'hop-10-goi' => array(
            'name'            => 'CareMIL Hộp 10 Gói Tiện Lợi',
            'default_variant' => 'sachet',
            'variants'        => array(
                'box'    => array(
                    'label'     => 'Hộp Lớn 800g',
                    'price'     => '850.000đ',
                    'oldPrice'  => '1.000.000đ',
                    'image'     => 'https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-chuan.png',
                ),
                'sachet' => array(
                    'label'     => 'Hộp 10 Gói',
                    'price'     => '350.000đ',
                    'oldPrice'  => '450.000đ',
                    'image'     => 'https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Goi-sua.png',
                ),
            ),
        ),
        'combo-2-hop-lon' => array(
            'name'            => 'CareMIL Combo 2 Hộp Lớn',
            'default_variant' => 'box',
            'variants'        => array(
                'box'    => array(
                    'label'     => 'Combo 2 Hộp Lớn',
                    'price'     => '1.650.000đ',
                    'oldPrice'  => '1.900.000đ',
                    'image'     => 'https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-chuan.png',
                ),
                'sachet' => array(
                    'label'     => 'Hộp 10 Gói',
                    'price'     => '350.000đ',
                    'oldPrice'  => '450.000đ',
                    'image'     => 'https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Goi-sua.png',
                ),
            ),
        ),
        'goi-le-dung-thu' => array(
            'name'            => 'CareMIL Gói Lẻ Dùng Thử',
            'default_variant' => 'sachet',
            'variants'        => array(
                'box'    => array(
                    'label'     => 'Hộp Lớn 800g',
                    'price'     => '850.000đ',
                    'oldPrice'  => '1.000.000đ',
                    'image'     => 'https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-chuan.png',
                ),
                'sachet' => array(
                    'label'     => 'Gói Lẻ Dùng Thử',
                    'price'     => '40.000đ',
                    'oldPrice'  => '50.000đ',
                    'image'     => 'https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Goi-sua.png',
                ),
            ),
        ),
    );
}

function caremil_get_custom_product_by_slug( $slug ) {
    $all = caremil_get_custom_products();
    return isset( $all[ $slug ] ) ? $all[ $slug ] : null;
}

function caremil_product_template( $template ) {
    $slug = get_query_var( 'caremil_product' );
    if ( ! empty( $slug ) ) {
        // Cho phép template nhận slug sản phẩm tự code tay
        $slug            = sanitize_title( $slug );
        $product_data    = caremil_get_custom_product_by_slug( $slug );

        set_query_var( 'caremil_product', $slug );
        if ( $product_data ) {
            set_query_var( 'caremil_product_data', $product_data );
        }

        $custom_template = get_template_directory() . '/single-product.php';
        if ( file_exists( $custom_template ) ) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'caremil_product_template' );

/**
 * Trial registration table helpers
 */
function caremil_get_trial_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'caremil_trials';
}

function caremil_ensure_trial_table() {
    global $wpdb;
    $table_name   = caremil_get_trial_table_name();
    $table_exists = ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) === $table_name );

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    if ( ! $table_exists ) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql             = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(191) DEFAULT '' NOT NULL,
            phone VARCHAR(50) DEFAULT '' NOT NULL,
            city VARCHAR(191) DEFAULT '' NOT NULL,
            address TEXT,
            source VARCHAR(50) DEFAULT 'landing' NOT NULL,
            status VARCHAR(20) DEFAULT 'new' NOT NULL,
            consent_terms TINYINT(1) DEFAULT 0,
            consent_privacy TINYINT(1) DEFAULT 0,
            registered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY phone (phone)
        ) {$charset_collate};";

        dbDelta( $sql );
    }

    maybe_add_column(
        $table_name,
        'source',
        "ALTER TABLE {$table_name} ADD source VARCHAR(50) DEFAULT 'landing' NOT NULL"
    );
}
add_action( 'after_switch_theme', 'caremil_ensure_trial_table' );
add_action( 'init', 'caremil_ensure_trial_table' );

/**
 * REST API routes
 */
function caremil_trial_admin_permission() {
    return current_user_can( 'manage_options' );
}

function caremil_format_trial_row( $row ) {
    if ( ! $row ) {
        return array();
    }

    return array(
        'id'              => (int) $row->id,
        'name'            => $row->name,
        'city'            => $row->city,
        'address'         => $row->address,
        'phone'           => $row->phone,
        'status'          => $row->status,
        'source'          => isset( $row->source ) ? $row->source : 'landing',
        'registered_at'   => $row->registered_at,
        'consent_terms'   => (bool) $row->consent_terms,
        'consent_privacy' => (bool) $row->consent_privacy,
    );
}

function caremil_register_trial_routes() {
    register_rest_route(
        'caremil/v1',
        '/trials',
        array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'caremil_rest_get_trials',
                'permission_callback' => 'caremil_trial_admin_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'caremil_rest_create_trial',
                'permission_callback' => '__return_true',
                'args'                => array(
                    'name'            => array(
                        'sanitize_callback' => 'sanitize_text_field',
                        'required'          => false,
                    ),
                    'city'            => array(
                        'sanitize_callback' => 'sanitize_text_field',
                        'required'          => false,
                    ),
                    'address'         => array(
                        'sanitize_callback' => 'sanitize_textarea_field',
                        'required'          => false,
                    ),
                    'phone'           => array(
                        'sanitize_callback' => 'sanitize_text_field',
                        'required'          => true,
                    ),
                    'consent_terms'   => array(
                        'sanitize_callback' => 'rest_sanitize_boolean',
                        'required'          => false,
                    ),
                    'consent_privacy' => array(
                        'sanitize_callback' => 'rest_sanitize_boolean',
                        'required'          => false,
                    ),
                ),
            ),
        )
    );

    register_rest_route(
        'caremil/v1',
        '/trials/(?P<id>\d+)',
        array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'caremil_rest_update_trial',
                'permission_callback' => 'caremil_trial_admin_permission',
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => 'caremil_rest_delete_trial',
                'permission_callback' => 'caremil_trial_admin_permission',
            ),
        )
    );
}
add_action( 'rest_api_init', 'caremil_register_trial_routes' );

function caremil_rest_get_trials( WP_REST_Request $request ) {
    global $wpdb;

    $table  = caremil_get_trial_table_name();
    $rows   = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY registered_at DESC" );
    $result = array_map( 'caremil_format_trial_row', $rows );

    return rest_ensure_response( $result );
}

function caremil_rest_create_trial( WP_REST_Request $request ) {
    global $wpdb;

    $phone = $request->get_param( 'phone' );
    if ( empty( $phone ) ) {
        return new WP_REST_Response(
            array( 'message' => __( 'Vui lòng nhập số điện thoại.', 'caremil' ) ),
            400
        );
    }

    $data = array(
        'name'            => $request->get_param( 'name' ) ?: '',
        'city'            => $request->get_param( 'city' ) ?: '',
        'address'         => $request->get_param( 'address' ) ?: '',
        'phone'           => $phone,
        'source'          => sanitize_text_field( $request->get_param( 'source' ) ?: 'landing' ),
        'status'          => 'new',
        'consent_terms'   => $request->get_param( 'consent_terms' ) ? 1 : 0,
        'consent_privacy' => $request->get_param( 'consent_privacy' ) ? 1 : 0,
        'registered_at'   => current_time( 'mysql' ),
    );

    $inserted = $wpdb->insert( caremil_get_trial_table_name(), $data );

    if ( ! $inserted ) {
        return new WP_REST_Response(
            array( 'message' => __( 'Không thể lưu dữ liệu. Vui lòng thử lại.', 'caremil' ) ),
            500
        );
    }

    $data['id']              = $wpdb->insert_id;
    $data['consent_terms']   = (bool) $data['consent_terms'];
    $data['consent_privacy'] = (bool) $data['consent_privacy'];

    return rest_ensure_response( $data );
}

function caremil_rest_update_trial( WP_REST_Request $request ) {
    global $wpdb;

    $id = (int) $request->get_param( 'id' );
    if ( $id <= 0 ) {
        return new WP_REST_Response(
            array( 'message' => __( 'ID không hợp lệ.', 'caremil' ) ),
            400
        );
    }

    $allowed_fields = array( 'status', 'name', 'phone', 'city', 'address' );
    $data           = array();

    foreach ( $allowed_fields as $field ) {
        if ( null !== $request->get_param( $field ) ) {
            $value    = ( 'address' === $field ) ? sanitize_textarea_field( $request->get_param( $field ) ) : sanitize_text_field( $request->get_param( $field ) );
            $data[ $field ] = $value;
        }
    }

    if ( isset( $data['status'] ) ) {
        $allowed_statuses = array( 'new', 'contacted', 'verified', 'spam' );
        if ( ! in_array( $data['status'], $allowed_statuses, true ) ) {
            return new WP_REST_Response(
                array( 'message' => __( 'Trạng thái không hợp lệ.', 'caremil' ) ),
                400
            );
        }
    }

    if ( empty( $data ) ) {
        return new WP_REST_Response(
            array( 'message' => __( 'Không có dữ liệu cập nhật.', 'caremil' ) ),
            400
        );
    }

    $updated = $wpdb->update(
        caremil_get_trial_table_name(),
        $data,
        array( 'id' => $id ),
        null,
        array( '%d' )
    );

    if ( false === $updated ) {
        return new WP_REST_Response(
            array( 'message' => __( 'Không thể cập nhật dữ liệu.', 'caremil' ) ),
            500
        );
    }

    $row = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . caremil_get_trial_table_name() . ' WHERE id = %d', $id ) );

    return rest_ensure_response( caremil_format_trial_row( $row ) );
}

function caremil_rest_delete_trial( WP_REST_Request $request ) {
    global $wpdb;

    $id = (int) $request->get_param( 'id' );
    if ( $id <= 0 ) {
        return new WP_REST_Response(
            array( 'message' => __( 'ID không hợp lệ.', 'caremil' ) ),
            400
        );
    }

    $deleted = $wpdb->delete(
        caremil_get_trial_table_name(),
        array( 'id' => $id ),
        array( '%d' )
    );

    if ( ! $deleted ) {
        return new WP_REST_Response(
            array( 'message' => __( 'Không thể xóa dữ liệu.', 'caremil' ) ),
            500
        );
    }

    return rest_ensure_response(
        array(
            'success' => true,
            'id'      => $id,
        )
    );
}

/**
 * Admin page for trial registrations
 */
function caremil_register_admin_menu() {
    add_menu_page(
        __( 'Đăng ký dùng thử', 'caremil' ),
        __( 'Đăng ký CareMIL', 'caremil' ),
        'manage_options',
        'caremil-trials',
        'caremil_render_trial_admin_page',
        'dashicons-groups',
        3
    );
}
add_action( 'admin_menu', 'caremil_register_admin_menu' );

function caremil_render_trial_admin_page() {
    echo '<div id="caremil-trial-admin-app" class="min-h-screen"></div>';
}

function caremil_enqueue_trial_admin_assets( $hook ) {
    if ( 'toplevel_page_caremil-trials' !== $hook ) {
        return;
    }

    wp_enqueue_script( 'wp-element' );
    wp_enqueue_script( 'wp-api-fetch' );

    wp_enqueue_script(
        'caremil-tailwindcdn',
        'https://cdn.tailwindcss.com?plugins=forms,typography',
        array(),
        '3.4.1',
        true
    );
    wp_add_inline_script(
        'caremil-tailwindcdn',
        'window.tailwind = window.tailwind || {}; tailwind.config = Object.assign({}, tailwind.config, { corePlugins: { preflight: false } });',
        'before'
    );

    $admin_js_path = get_template_directory_uri() . '/js/admin-trials.js';
    wp_enqueue_script(
        'caremil-trial-admin',
        $admin_js_path,
        array( 'wp-element', 'wp-api-fetch' ),
        '1.0.0',
        true
    );

    wp_localize_script(
        'caremil-trial-admin',
        'caremilTrialAdmin',
        array(
            'restUrl' => esc_url_raw( rest_url( 'caremil/v1/trials' ) ),
            'nonce'   => wp_create_nonce( 'wp_rest' ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'caremil_enqueue_trial_admin_assets' );

/**
 * OLD: Admin UI for caremil_product CPT
 * DISABLED - Now using product-menu-integration.php instead
 */
/*
function caremil_register_product_admin_pages() {
    add_submenu_page(
        'edit.php?post_type=caremil_product',
        __( 'Quản lý Sản phẩm', 'caremil' ),
        __( 'Quản lý sản phẩm (UI)', 'caremil' ),
        'edit_posts',
        'caremil-products-app',
        'caremil_render_product_admin_page'
    );

    add_submenu_page(
        'edit.php?post_type=caremil_product',
        __( 'Thêm Sản phẩm', 'caremil' ),
        __( 'Thêm sản phẩm (UI)', 'caremil' ),
        'edit_posts',
        'caremil-product-designer',
        'caremil_render_product_single_page'
    );

    add_submenu_page(
        'edit.php?post_type=caremil_product',
        __( 'Nhóm Sản phẩm', 'caremil' ),
        __( 'Nhóm sản phẩm (UI)', 'caremil' ),
        'edit_posts',
        'caremil-product-groups',
        'caremil_render_product_group_page'
    );
}
add_action( 'admin_menu', 'caremil_register_product_admin_pages' );
*/

/*
 * OLD: Hide default product submenus
 * DISABLED - Not needed with new integration
 */
/*
function caremil_hide_default_product_submenus() {
    remove_submenu_page( 'edit.php?post_type=caremil_product', 'post-new.php?post_type=caremil_product' );
    remove_submenu_page( 'edit.php?post_type=caremil_product', 'edit-tags.php?taxonomy=caremil_product_cat&post_type=caremil_product' );

    // Ẩn menu CPT mặc định khỏi thanh menu (để tránh trùng). Sau đó thêm menu riêng trỏ thẳng UI custom.
    remove_menu_page( 'edit.php?post_type=caremil_product' );
}
add_action( 'admin_menu', 'caremil_hide_default_product_submenus', 999 );
*/

/**
 * OLD: Custom menu for CareMIL Products
 * DISABLED - Now using product-menu-integration.php instead
 */
/*
function caremil_register_custom_product_menu() {
    add_menu_page(
        __( 'Sản phẩm', 'caremil' ),
        __( 'Sản phẩm', 'caremil' ),
        'edit_posts',
        'caremil-products-app',
        'caremil_render_product_admin_page',
        'dashicons-store',
        3
    );

    add_submenu_page(
        'caremil-products-app',
        __( 'Quản lý sản phẩm', 'caremil' ),
        __( 'Quản lý sản phẩm (UI)', 'caremil' ),
        'edit_posts',
        'caremil-products-app',
        'caremil_render_product_admin_page'
    );

    add_submenu_page(
        'caremil-products-app',
        __( 'Thêm Sản phẩm', 'caremil' ),
        __( 'Thêm sản phẩm (UI)', 'caremil' ),
        'edit_posts',
        'caremil-product-designer',
        'caremil_render_product_single_page'
    );

    add_submenu_page(
        'caremil-products-app',
        __( 'Nhóm Sản phẩm', 'caremil' ),
        __( 'Nhóm sản phẩm (UI)', 'caremil' ),
        'edit_posts',
        'caremil-product-groups',
        'caremil_render_product_group_page'
    );
}
add_action( 'admin_menu', 'caremil_register_custom_product_menu', 20 );
*/

/**
 * Thêm body class để style áp dụng đúng cho các trang UI custom.
 */
function caremil_admin_body_class( $classes ) {
    $page = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if ( in_array( $page, array( 'caremil-products-app', 'caremil-product-designer', 'caremil-product-groups' ), true ) ) {
        $classes .= ' caremil-admin';
    }
    return $classes;
}
add_filter( 'admin_body_class', 'caremil_admin_body_class' );

/**
 * Chặn truy cập màn list mặc định và chuyển về UI custom.
 */
function caremil_redirect_default_product_list() {
    if ( ! is_admin() ) {
        return;
    }
    $screen = get_current_screen();
    if ( $screen && 'edit-caremil_product' === $screen->id ) {
        $target = admin_url( 'admin.php?page=caremil-products-app' );
        // Tránh loop nếu đã ở trang custom.
        if ( false === strpos( $_SERVER['REQUEST_URI'], 'caremil-products-app' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            wp_safe_redirect( $target );
            exit;
        }
    }
}
add_action( 'load-edit.php', 'caremil_redirect_default_product_list' );

function caremil_admin_enqueue_tailwind() {
    wp_enqueue_script(
        'caremil-tailwind-admin',
        'https://cdn.tailwindcss.com?plugins=forms,typography',
        array(),
        '3.4.1',
        true
    );
    wp_add_inline_script(
        'caremil-tailwind-admin',
        'window.tailwind = window.tailwind || {}; tailwind.config = Object.assign({}, tailwind.config, { corePlugins: { preflight: false } });',
        'before'
    );
}

function caremil_admin_enqueue_fonts_icons() {
    // Google Fonts & Font Awesome cho admin UI custom.
    wp_enqueue_style(
        'caremil-admin-fonts',
        'https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;700;800&family=Quicksand:wght@400;500;600;700&display=swap',
        array(),
        null
    );
    wp_enqueue_style(
        'caremil-admin-fa',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        array(),
        '6.4.0'
    );
}

function caremil_admin_tailwind_config() {
    ?>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            navy: '#1a4f8a',
                            blue: '#4cc9f0',
                            gold: '#ffd166',
                            soft: '#e0fbfc',
                            cream: '#fffdf2',
                            pink: '#ef476f',
                            green: '#4ade80',
                        }
                    },
                    fontFamily: {
                        sans: ['Quicksand', 'sans-serif'],
                        display: ['Baloo 2', 'cursive'],
                    },
                }
            }
        };
    </script>
    <?php
}

function caremil_admin_inline_styles() {
    ?>
    <style>
        body.caremil-admin { background-color: #f8fafc; font-family: 'Quicksand', sans-serif; color: #334155; }
        .caremil-admin h1, .caremil-admin h2, .caremil-admin h3, .caremil-admin h4 { font-family: 'Baloo 2', cursive; }
        .caremil-admin .wrap { margin: 0; padding: 0; }
        .caremil-shell { padding: 16px 24px 32px; }
        .caremil-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 8px 30px -12px rgba(15, 23, 42, 0.18);
        }
        .caremil-card-soft {
            background: linear-gradient(135deg, #fffdf2, #f8fafc);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 8px 30px -12px rgba(15, 23, 42, 0.18);
        }
        .caremil-section-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
        .caremil-section-sub { color: #64748b; font-size: 13px; margin-bottom: 16px; }
        .caremil-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (max-width: 1024px) { .caremil-grid { grid-template-columns: 1fr; } }
        .caremil-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 10px 16px; border-radius: 12px; font-weight: 700; font-size: 14px;
            border: 1px solid #1a4f8a; background: #1a4f8a; color: #fff;
            box-shadow: 0 10px 20px -10px rgba(26, 79, 138, 0.55);
            transition: all 0.2s ease;
        }
        .caremil-btn:hover { background: #4cc9f0; border-color: #4cc9f0; box-shadow: 0 12px 24px -12px rgba(76, 201, 240, 0.55); color: #0f172a; }
        .caremil-btn-ghost {
            background: #fff; color: #1a4f8a; border: 1px solid #cbd5e1; box-shadow: none;
        }
        .caremil-btn-ghost:hover { background: #f8fafc; border-color: #1a4f8a; }
        .caremil-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; }
        .caremil-stat {
            padding: 14px 16px; border-radius: 14px; border: 1px solid #e2e8f0; background: #fff;
            box-shadow: 0 8px 24px -14px rgba(15, 23, 42, 0.22);
            display: flex; align-items: center; gap: 12px;
        }
        .caremil-badge-pill {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700;
            background: #e0f2fe; color: #0369a1;
        }
        .admin-table th {
            text-align: left;
            padding: 12px 16px;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            background-color: #f1f5f9;
            border-bottom: 2px solid #e2e8f0;
            letter-spacing: 0.02em;
        }
        .admin-table td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover { background-color: #f8fafc; }
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .action-btn {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            color: #64748b;
        }
        .action-btn:hover { background-color: #e2e8f0; color: #1e293b; text-decoration: none; }
        .action-btn.edit:hover { background-color: #e0f2fe; color: #0284c7; }
        .action-btn.delete:hover { background-color: #fee2e2; color: #dc2626; }
        .admin-input {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background-color: white;
            font-size: 0.9rem;
            transition: all 0.2s;
            color: #1e293b;
        }
        .admin-input:focus {
            border-color: #4cc9f0;
            box-shadow: 0 0 0 3px rgba(76, 201, 240, 0.1);
            outline: none;
        }
        .admin-label {
            font-weight: 700;
            color: #475569;
            font-size: 0.85rem;
            margin-bottom: 6px;
            display: block;
        }
        .preview-card { transition: all 0.3s ease; background: white; border-radius: 1rem; overflow: hidden; border: 1px solid #f1f5f9; }
        .preview-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08); }
    </style>
    <?php
}

function caremil_render_product_admin_page() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        return;
    }

    caremil_admin_enqueue_tailwind();
    caremil_admin_enqueue_fonts_icons();
    caremil_admin_tailwind_config();
    caremil_admin_inline_styles();

    $search      = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
    $filter_cat  = isset( $_GET['cat'] ) ? sanitize_text_field( wp_unslash( $_GET['cat'] ) ) : '';
    $cat_terms   = get_terms(
        array(
            'taxonomy'   => 'caremil_product_cat',
            'hide_empty' => false,
        )
    );

    $query_args = array(
        'post_type'      => 'caremil_product',
        'posts_per_page' => -1,
        'orderby'        => array(
            'menu_order' => 'ASC',
            'title'      => 'ASC',
        ),
        's'              => $search,
    );

    if ( $filter_cat ) {
        $query_args['tax_query'] = array(
            array(
                'taxonomy' => 'caremil_product_cat',
                'field'    => 'slug',
                'terms'    => $filter_cat,
            ),
        );
    }

    $products = get_posts( $query_args );
    $total    = wp_count_posts( 'caremil_product' )->publish;
    ?>
    <div class="wrap caremil-shell">
        <div class="flex items-center justify-between mb-4">
            <div>
                <div class="caremil-section-title">Quản Lý Sản Phẩm</div>
                <div class="caremil-section-sub">Quản lý danh sách, tồn kho và giá bán.</div>
            </div>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=caremil-product-designer' ) ); ?>" class="caremil-btn"><i class="fas fa-plus"></i> Thêm Sản Phẩm</a>
        </div>

        <div class="caremil-stats mb-4">
            <div class="caremil-stat">
                <div class="w-10 h-10 rounded-full bg-blue-50 text-brand-blue flex items-center justify-center text-lg"><i class="fas fa-box"></i></div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Tổng Sản Phẩm</p>
                    <p class="text-xl font-bold text-slate-700"><?php echo esc_html( (int) $total ); ?></p>
                </div>
            </div>
            <div class="caremil-stat">
                <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-lg"><i class="fas fa-check"></i></div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Đang Bán</p>
                    <p class="text-xl font-bold text-slate-700"><?php echo esc_html( (int) $total ); ?></p>
                </div>
            </div>
            <div class="caremil-stat">
                <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-lg"><i class="fas fa-exclamation"></i></div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Sắp Hết</p>
                    <p class="text-xl font-bold text-slate-700">0</p>
                </div>
            </div>
            <div class="caremil-stat">
                <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-lg"><i class="fas fa-times-circle"></i></div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Hết Hàng</p>
                    <p class="text-xl font-bold text-slate-700">0</p>
                </div>
            </div>
        </div>

        <form method="get" class="caremil-card p-4 flex flex-col md:flex-row gap-3 justify-between items-center mb-3">
            <input type="hidden" name="post_type" value="caremil_product" />
            <input type="hidden" name="page" value="caremil-products-app" />
            <div class="relative w-full md:w-96">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input name="s" value="<?php echo esc_attr( $search ); ?>" type="text" placeholder="Tìm kiếm sản phẩm..." class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-blue-50 transition">
            </div>
            <div class="flex gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0">
                <select name="cat" class="px-4 py-2 border border-slate-200 rounded-lg text-sm bg-white text-slate-600 font-bold focus:outline-none cursor-pointer hover:bg-slate-50">
                    <option value=""><?php esc_html_e( 'Tất cả danh mục', 'caremil' ); ?></option>
                    <?php foreach ( $cat_terms as $term ) : ?>
                        <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $filter_cat, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="caremil-btn caremil-btn-ghost text-sm">Lọc</button>
            </div>
        </form>

        <div class="caremil-card overflow-hidden">
            <table class="w-full admin-table min-w-[800px]">
                <thead>
                    <tr>
                        <th class="w-16"><input type="checkbox" disabled class="rounded border-gray-300"></th>
                        <th class="w-80">Sản Phẩm</th>
                        <th>Phân Loại</th>
                        <th>Giá Bán</th>
                        <th>Badge</th>
                        <th class="text-right w-32">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( $products ) : ?>
                        <?php foreach ( $products as $post ) : ?>
                            <?php
                            $price       = get_post_meta( $post->ID, 'caremil_price', true );
                            $badge       = get_post_meta( $post->ID, 'caremil_badge', true );
                            $badge_class = get_post_meta( $post->ID, 'caremil_badge_class', true );
                            $badge_class = $badge_class ? $badge_class : 'bg-slate-100 text-slate-600';
                            $terms       = wp_get_post_terms( $post->ID, 'caremil_product_cat', array( 'fields' => 'names' ) );
                            $thumb       = get_the_post_thumbnail_url( $post->ID, 'thumbnail' );
                            $thumb       = $thumb ? $thumb : 'https://via.placeholder.com/120x120?text=CareMIL';
                            ?>
                            <tr>
                                <td><input type="checkbox" class="rounded border-gray-300" disabled></td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg bg-slate-50 border border-slate-100 p-1">
                                            <img src="<?php echo esc_url( $thumb ); ?>" class="w-full h-full object-contain">
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800"><?php echo esc_html( get_the_title( $post ) ); ?></p>
                                            <p class="text-xs text-slate-400">ID: <?php echo (int) $post->ID; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold"><?php echo esc_html( implode( ', ', $terms ) ); ?></span></td>
                                <td><span class="font-bold text-brand-pink"><?php echo esc_html( $price ); ?></span></td>
                                <td>
                                    <?php if ( $badge ) : ?>
                                        <span class="<?php echo esc_attr( $badge_class ); ?> px-2 py-1 rounded text-xs font-bold inline-block"><?php echo esc_html( $badge ); ?></span>
                                    <?php else : ?>
                                        <span class="text-xs text-slate-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-1">
                                        <a class="action-btn edit" title="Sửa" href="<?php echo esc_url( admin_url( 'admin.php?page=caremil-product-designer&product_id=' . $post->ID ) ); ?>"><i class="fas fa-pen"></i></a>
                                        <?php
                                        $del_link = get_delete_post_link( $post->ID, '', true );
                                        ?>
                                        <a class="action-btn delete" title="Xóa" href="<?php echo esc_url( $del_link ); ?>" onclick="return confirm('Xóa sản phẩm này?');"><i class="fas fa-trash-alt"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="6" class="text-center py-6 text-slate-500">Chưa có sản phẩm.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

function caremil_render_product_single_page() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        return;
    }

    caremil_admin_enqueue_tailwind();
    caremil_admin_enqueue_fonts_icons();
    caremil_admin_tailwind_config();
    caremil_admin_inline_styles();
    wp_enqueue_media();

    $post_id   = isset( $_GET['product_id'] ) ? absint( $_GET['product_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $is_edit   = $post_id > 0;
    $post      = $is_edit ? get_post( $post_id ) : null;
    $title     = $post ? $post->post_title : '';
    $price     = $post ? get_post_meta( $post_id, 'caremil_price', true ) : '850000';
    $old_price = $post ? get_post_meta( $post_id, 'caremil_old_price', true ) : '1000000';
    $badge     = $post ? get_post_meta( $post_id, 'caremil_badge', true ) : 'Best Seller';
    $badge_cl  = $post ? get_post_meta( $post_id, 'caremil_badge_class', true ) : 'bg-brand-gold text-brand-navy';
    $desc      = $post ? get_post_meta( $post_id, 'caremil_short_desc', true ) : 'Dinh dưỡng chuẩn cho bé dùng hàng ngày tại nhà.';
    $cta       = $post ? get_post_meta( $post_id, 'caremil_button_label', true ) : 'Chọn Mua';
    $cta_url   = $post ? get_post_meta( $post_id, 'caremil_button_url', true ) : '';
    $rating    = $post ? get_post_meta( $post_id, 'caremil_rating', true ) : 5;
    $rating_ct = $post ? get_post_meta( $post_id, 'caremil_rating_count', true ) : 0;
    $thumb     = $post ? get_the_post_thumbnail_url( $post_id, 'large' ) : '';
    $thumb     = $thumb ? $thumb : 'https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-chuan.png';
    $cat_terms = get_terms(
        array(
            'taxonomy'   => 'caremil_product_cat',
            'hide_empty' => false,
        )
    );
    $current_cat = '';
    if ( $post ) {
        $cats        = wp_get_post_terms( $post_id, 'caremil_product_cat', array( 'fields' => 'ids' ) );
        $current_cat = $cats ? $cats[0] : '';
    }
    ?>
    <div class="wrap caremil-shell">
        <main class="container mx-auto px-0 md:px-2 py-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <div class="caremil-section-title"><?php echo $is_edit ? 'Sửa Sản Phẩm' : 'Thêm Sản Phẩm Mới'; ?></div>
                    <div class="caremil-section-sub">Điền thông tin, chọn nhóm, badge và xem live preview ngay.</div>
                </div>
                <div class="flex gap-2">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=caremil-products-app' ) ); ?>" class="caremil-btn caremil-btn-ghost"><i class="fas fa-arrow-left"></i> Quay lại danh sách</a>
                    <button type="submit" form="caremil-product-form" class="caremil-btn"><i class="fas fa-save"></i> Lưu</button>
                </div>
            </div>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="grid grid-cols-1 lg:grid-cols-3 gap-4" id="caremil-product-form">
                    <?php wp_nonce_field( 'caremil_save_product', 'caremil_save_product_nonce' ); ?>
                    <input type="hidden" name="action" value="caremil_save_product">
                    <?php if ( $is_edit ) : ?>
                        <input type="hidden" name="product_id" value="<?php echo (int) $post_id; ?>">
                    <?php endif; ?>

                    <div class="lg:col-span-2 space-y-3">
                        <div class="caremil-card p-4 space-y-4">
                            <div>
                                <label class="admin-label">Tên sản phẩm</label>
                                <input type="text" name="p_name" id="p_name" value="<?php echo esc_attr( $title ); ?>" class="admin-input" required oninput="caremilPreview()">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="admin-label">Mã sản phẩm (SKU)</label>
                                    <input type="text" name="p_sku" class="admin-input" placeholder="CM-800G">
                                </div>
                                <div>
                                    <label class="admin-label">Phân loại (Unit)</label>
                                    <select class="admin-input" name="p_unit" id="p_unit">
                                        <option value="">— Chọn nhóm —</option>
                                        <?php foreach ( $cat_terms as $term ) : ?>
                                            <option value="<?php echo (int) $term->term_id; ?>" <?php selected( $current_cat, $term->term_id ); ?>><?php echo esc_html( $term->name ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="admin-label">Giá bán (VNĐ)</label>
                                    <input type="number" name="p_price" id="p_price" class="admin-input text-brand-pink font-bold" value="<?php echo esc_attr( $price ); ?>" oninput="caremilPreview()">
                                </div>
                                <div>
                                    <label class="admin-label">Giá gốc (VNĐ) <span class="font-normal text-xs text-gray-400">- Để trống nếu không giảm</span></label>
                                    <input type="number" name="p_old_price" id="p_old_price" class="admin-input text-gray-400 line-through" value="<?php echo esc_attr( $old_price ); ?>" oninput="caremilPreview()">
                                </div>
                            </div>
                        </div>

                        <div class="caremil-card p-4 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="admin-label">Badge (Nhãn nổi bật)</label>
                                    <input type="text" name="p_badge" id="p_badge" class="admin-input" value="<?php echo esc_attr( $badge ); ?>" oninput="caremilPreview()">
                                </div>
                                <div>
                                    <label class="admin-label">Class màu badge</label>
                                    <input type="text" name="p_badge_class" id="p_badge_class" class="admin-input" value="<?php echo esc_attr( $badge_cl ); ?>" oninput="caremilPreview()">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="admin-label">Rating (0-5)</label>
                                    <input type="number" step="0.1" min="0" max="5" name="p_rating" class="admin-input" value="<?php echo esc_attr( $rating ); ?>">
                                </div>
                                <div>
                                    <label class="admin-label">Số lượt đánh giá</label>
                                    <input type="number" min="0" name="p_rating_count" class="admin-input" value="<?php echo esc_attr( $rating_ct ); ?>">
                                </div>
                            </div>
                            <div>
                                <label class="admin-label">Mô tả ngắn (Hiển thị trên thẻ)</label>
                                <textarea name="p_desc" id="p_desc" class="admin-input h-20 resize-none" oninput="caremilPreview()"><?php echo esc_textarea( $desc ); ?></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="admin-label">Nút Mua Hàng (CTA)</label>
                                    <input type="text" name="p_cta" id="p_cta" class="admin-input" value="<?php echo esc_attr( $cta ); ?>" oninput="caremilPreview()">
                                </div>
                                <div>
                                    <label class="admin-label">Link CTA</label>
                                    <input type="url" name="p_cta_url" class="admin-input" value="<?php echo esc_url( $cta_url ); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="caremil-card p-4 space-y-3">
                            <div class="text-sm font-semibold text-slate-700">Ảnh đại diện</div>
                            <div class="border border-dashed border-slate-200 rounded-xl p-3 text-center">
                                <img id="p_thumb_preview" src="<?php echo esc_url( $thumb ); ?>" alt="Ảnh sản phẩm" class="w-full h-44 object-cover rounded-lg mb-3 border border-slate-200">
                                <input type="hidden" name="p_featured_id" id="p_featured_id" value="<?php echo esc_attr( $post ? $post->ID : 0 ); ?>">
                                <button type="button" class="px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm hover:bg-slate-50" onclick="caremilOpenMedia()">Chọn / đổi ảnh</button>
                            </div>
                        </div>
                        <div class="caremil-card-soft p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-semibold text-slate-700">Live Preview</div>
                                    <div class="text-xs text-slate-400">Xem trước thẻ sản phẩm ngoài cửa hàng.</div>
                                </div>
                                <div class="caremil-badge-pill"><i class="fas fa-bolt"></i> Real-time</div>
                            </div>
                            <div class="preview-card bg-white p-4 relative group max-w-sm mx-auto shadow-md">
                                <div class="absolute top-4 left-4 z-10">
                                    <span id="preview_badge" class="text-brand-navy text-xs font-bold px-2 py-1 rounded shadow-sm <?php echo esc_attr( $badge_cl ); ?>"><?php echo esc_html( $badge ); ?></span>
                                </div>
                                <div class="absolute top-4 right-4 z-10">
                                     <button class="w-8 h-8 rounded-full bg-white text-gray-400 shadow-sm flex items-center justify-center"><i class="far fa-heart"></i></button>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-6 mb-4 h-64 flex items-center justify-center overflow-hidden">
                                    <img id="preview_image" src="<?php echo esc_url( $thumb ); ?>" alt="Product" class="w-40 h-auto object-contain drop-shadow-lg transition duration-500 hover:scale-110">
                                </div>
                                <div class="text-center">
                                    <div class="flex justify-center gap-1 text-brand-gold text-xs mb-2">
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                        <span class="text-gray-400 ml-1">(<?php echo esc_html( $rating_ct ); ?>)</span>
                                    </div>
                                    <h3 class="text-lg font-bold text-brand-navy mb-1"><span id="preview_name"><?php echo esc_html( $title ? $title : 'Tên sản phẩm' ); ?></span></h3>
                                    <p class="text-xs text-gray-500 mb-3 line-clamp-2" id="preview_desc"><?php echo esc_html( $desc ); ?></p>
                                    <div class="flex items-center justify-center gap-2 mb-4">
                                        <span class="text-xl font-bold text-brand-pink" id="preview_price"><?php echo esc_html( $price ); ?>đ</span>
                                        <span class="text-sm text-gray-400 line-through" id="preview_old_price"><?php echo esc_html( $old_price ); ?>đ</span>
                                    </div>
                                    <button class="w-full bg-brand-navy text-white font-bold py-2.5 rounded-xl shadow-lg flex items-center justify-center gap-2 hover:bg-brand-blue transition">
                                        <i class="fas fa-shopping-bag"></i> <span id="preview_cta"><?php echo esc_html( $cta ); ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="submit" class="caremil-btn" name="save_product"><i class="fas fa-save"></i> Lưu sản phẩm</button>
                            <?php if ( $is_edit ) : ?>
                                <a class="caremil-btn caremil-btn-ghost border-red-200 text-red-600 hover:text-red-700" href="<?php echo esc_url( get_delete_post_link( $post_id, '', true ) ); ?>" onclick="return confirm('Xóa sản phẩm này?');"><i class="fas fa-trash-alt"></i> Xóa</a>
                            <?php endif; ?>
                        </div>
                    </div>
            </form>
        </main>
    </div>
    <script>
        function caremilOpenMedia() {
            const frame = wp.media({ title: 'Chọn ảnh sản phẩm', multiple: false });
            frame.on('select', () => {
                const attachment = frame.state().get('selection').first().toJSON();
                document.getElementById('p_featured_id').value = attachment.id;
                document.getElementById('p_thumb_preview').src = attachment.sizes?.medium?.url || attachment.url;
                document.getElementById('preview_image').src = attachment.sizes?.medium?.url || attachment.url;
            });
            frame.open();
        }
        function caremilPreview() {
            const name = document.getElementById('p_name').value;
            const price = document.getElementById('p_price').value;
            const oldPrice = document.getElementById('p_old_price').value;
            const badge = document.getElementById('p_badge').value;
            const badgeClass = document.getElementById('p_badge_class').value;
            const desc = document.getElementById('p_desc').value;
            const cta = document.getElementById('p_cta').value;
            document.getElementById('preview_name').innerText = name || 'Tên sản phẩm';
            document.getElementById('preview_desc').innerText = desc || 'Mô tả ngắn sản phẩm...';
            document.getElementById('preview_price').innerText = price ? (parseInt(price).toLocaleString('vi-VN') + 'đ') : '0đ';
            const oldEl = document.getElementById('preview_old_price');
            if (oldPrice && parseInt(oldPrice) > parseInt(price || 0)) {
                oldEl.style.display = 'inline';
                oldEl.innerText = parseInt(oldPrice).toLocaleString('vi-VN') + 'đ';
            } else {
                oldEl.style.display = 'none';
            }
            const badgeEl = document.getElementById('preview_badge');
            if (badge) {
                badgeEl.innerText = badge;
                badgeEl.className = 'text-brand-navy text-xs font-bold px-2 py-1 rounded shadow-sm ' + (badgeClass || '');
                badgeEl.parentElement.style.display = 'block';
            } else {
                badgeEl.parentElement.style.display = 'none';
            }
            document.getElementById('preview_cta').innerText = cta || 'Mua Ngay';
        }
        document.addEventListener('DOMContentLoaded', caremilPreview);
    </script>
    <?php
}

function caremil_handle_product_save() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( esc_html__( 'Không đủ quyền.', 'caremil' ) );
    }
    if ( ! isset( $_POST['caremil_save_product_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['caremil_save_product_nonce'] ), 'caremil_save_product' ) ) {
        wp_die( esc_html__( 'Nonce không hợp lệ.', 'caremil' ) );
    }

    $post_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
    $title   = sanitize_text_field( wp_unslash( $_POST['p_name'] ?? '' ) );
    $price   = sanitize_text_field( wp_unslash( $_POST['p_price'] ?? '' ) );
    $old     = sanitize_text_field( wp_unslash( $_POST['p_old_price'] ?? '' ) );
    $badge   = sanitize_text_field( wp_unslash( $_POST['p_badge'] ?? '' ) );
    $badge_c = sanitize_text_field( wp_unslash( $_POST['p_badge_class'] ?? '' ) );
    $desc    = wp_kses_post( wp_unslash( $_POST['p_desc'] ?? '' ) );
    $cta     = sanitize_text_field( wp_unslash( $_POST['p_cta'] ?? '' ) );
    $cta_url = esc_url_raw( wp_unslash( $_POST['p_cta_url'] ?? '' ) );
    $cat     = isset( $_POST['p_unit'] ) ? absint( $_POST['p_unit'] ) : 0;
    $rating  = isset( $_POST['p_rating'] ) ? floatval( $_POST['p_rating'] ) : 5;
    $rating_ct = isset( $_POST['p_rating_count'] ) ? intval( $_POST['p_rating_count'] ) : 0;

    $data = array(
        'post_title'   => $title,
        'post_type'    => 'caremil_product',
        'post_status'  => 'publish',
        'post_content' => $desc,
    );

    if ( $post_id ) {
        $data['ID'] = $post_id;
        $post_id    = wp_update_post( $data );
    } else {
        $post_id = wp_insert_post( $data );
    }

    if ( is_wp_error( $post_id ) ) {
        wp_die( esc_html__( 'Không thể lưu sản phẩm.', 'caremil' ) );
    }

    update_post_meta( $post_id, 'caremil_price', $price );
    update_post_meta( $post_id, 'caremil_old_price', $old );
    update_post_meta( $post_id, 'caremil_badge', $badge );
    update_post_meta( $post_id, 'caremil_badge_class', $badge_c );
    update_post_meta( $post_id, 'caremil_short_desc', $desc );
    update_post_meta( $post_id, 'caremil_button_label', $cta );
    update_post_meta( $post_id, 'caremil_button_url', $cta_url );
    update_post_meta( $post_id, 'caremil_rating', $rating );
    update_post_meta( $post_id, 'caremil_rating_count', $rating_ct );

    if ( $cat ) {
        wp_set_post_terms( $post_id, array( $cat ), 'caremil_product_cat', false );
    }

    if ( ! empty( $_POST['p_featured_id'] ) ) {
        $media_id = absint( $_POST['p_featured_id'] );
        set_post_thumbnail( $post_id, $media_id );
    }

    wp_safe_redirect( admin_url( 'admin.php?page=caremil-product-designer&product_id=' . $post_id . '&updated=1' ) );
    exit;
}
add_action( 'admin_post_caremil_save_product', 'caremil_handle_product_save' );

function caremil_render_product_group_page() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        return;
    }
    caremil_admin_enqueue_tailwind();
    caremil_admin_enqueue_fonts_icons();
    caremil_admin_tailwind_config();
    caremil_admin_inline_styles();

    $terms = get_terms(
        array(
            'taxonomy'   => 'caremil_product_cat',
            'hide_empty' => false,
        )
    );

    $edit_id   = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $edit_term = $edit_id ? get_term( $edit_id, 'caremil_product_cat' ) : null;
    ?>
    <div class="wrap">
        <main class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Quản lý nhóm sản phẩm</h1>
                    <p class="text-sm text-slate-500">Tạo / sửa nhóm để dùng trong sản phẩm.</p>
                </div>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=caremil-products-app' ) ); ?>" class="px-4 py-2 rounded-lg bg-brand-navy text-white hover:bg-brand-blue text-sm shadow">Về danh sách</a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-800 mb-3"><?php echo $edit_term ? 'Sửa nhóm' : 'Thêm nhóm mới'; ?></h2>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="space-y-3">
                        <?php if ( $edit_term ) : ?>
                            <?php wp_nonce_field( 'caremil_edit_group_' . $edit_term->term_id, 'caremil_edit_group_nonce' ); ?>
                            <input type="hidden" name="action" value="caremil_edit_group">
                            <input type="hidden" name="term_id" value="<?php echo (int) $edit_term->term_id; ?>">
                        <?php else : ?>
                            <?php wp_nonce_field( 'caremil_add_group', 'caremil_add_group_nonce' ); ?>
                            <input type="hidden" name="action" value="caremil_add_group">
                        <?php endif; ?>
                        <div>
                            <label class="text-sm font-semibold text-slate-600">Tên nhóm</label>
                            <input type="text" name="group_name" class="admin-input" value="<?php echo esc_attr( $edit_term ? $edit_term->name : '' ); ?>" required>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-600">Slug (tùy chọn)</label>
                            <input type="text" name="group_slug" class="admin-input" placeholder="vd: box" value="<?php echo esc_attr( $edit_term ? $edit_term->slug : '' ); ?>">
                        </div>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-brand-navy text-white hover:bg-brand-blue text-sm shadow"><?php echo $edit_term ? 'Cập nhật nhóm' : 'Thêm nhóm'; ?></button>
                    </form>
                </div>

                <div class="lg:col-span-2 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-800 mb-3">Danh sách nhóm</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-500 uppercase text-xs border-b">
                                    <th class="py-2">Tên</th>
                                    <th class="py-2">Slug</th>
                                    <th class="py-2 text-right">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ( $terms ) : ?>
                                    <?php foreach ( $terms as $term ) : ?>
                                        <tr class="border-b last:border-0">
                                            <td class="py-2 font-semibold text-slate-800"><?php echo esc_html( $term->name ); ?></td>
                                            <td class="py-2 text-slate-500"><?php echo esc_html( $term->slug ); ?></td>
                                            <td class="py-2 text-right space-x-2">
                                                <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=caremil-product-groups&edit=' . $term->term_id ), 'caremil_edit_group_' . $term->term_id ) ); ?>" class="text-brand-blue font-semibold">Sửa</a>
                                                <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=caremil_delete_group&term_id=' . $term->term_id ), 'caremil_delete_group_' . $term->term_id ) ); ?>" class="text-red-600 font-semibold" onclick="return confirm('Xóa nhóm?');">Xóa</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr><td colspan="3" class="py-4 text-center text-slate-500">Chưa có nhóm.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php
}

function caremil_handle_add_group() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( esc_html__( 'Không đủ quyền.', 'caremil' ) );
    }
    if ( ! isset( $_POST['caremil_add_group_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['caremil_add_group_nonce'] ), 'caremil_add_group' ) ) {
        wp_die( esc_html__( 'Nonce không hợp lệ.', 'caremil' ) );
    }
    $name = sanitize_text_field( wp_unslash( $_POST['group_name'] ?? '' ) );
    $slug = sanitize_title( wp_unslash( $_POST['group_slug'] ?? '' ) );
    if ( $name ) {
        wp_insert_term(
            $name,
            'caremil_product_cat',
            array(
                'slug' => $slug ? $slug : '',
            )
        );
    }
    wp_safe_redirect( admin_url( 'admin.php?page=caremil-product-groups' ) );
    exit;
}
add_action( 'admin_post_caremil_add_group', 'caremil_handle_add_group' );

function caremil_handle_edit_group() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( esc_html__( 'Không đủ quyền.', 'caremil' ) );
    }
    $term_id = isset( $_POST['term_id'] ) ? absint( $_POST['term_id'] ) : 0;
    if ( ! $term_id || ! isset( $_POST['caremil_edit_group_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['caremil_edit_group_nonce'] ), 'caremil_edit_group_' . $term_id ) ) {
        wp_die( esc_html__( 'Nonce không hợp lệ.', 'caremil' ) );
    }
    $name = sanitize_text_field( wp_unslash( $_POST['group_name'] ?? '' ) );
    $slug = sanitize_title( wp_unslash( $_POST['group_slug'] ?? '' ) );
    if ( $name ) {
        wp_update_term(
            $term_id,
            'caremil_product_cat',
            array(
                'name' => $name,
                'slug' => $slug ? $slug : '',
            )
        );
    }
    wp_safe_redirect( admin_url( 'admin.php?page=caremil-product-groups' ) );
    exit;
}
add_action( 'admin_post_caremil_edit_group', 'caremil_handle_edit_group' );

function caremil_handle_delete_group() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( esc_html__( 'Không đủ quyền.', 'caremil' ) );
    }
    $term_id = isset( $_GET['term_id'] ) ? absint( $_GET['term_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if ( ! $term_id || ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ?? '' ), 'caremil_delete_group_' . $term_id ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        wp_die( esc_html__( 'Nonce không hợp lệ.', 'caremil' ) );
    }
    wp_delete_term( $term_id, 'caremil_product_cat' );
    wp_safe_redirect( admin_url( 'admin.php?page=caremil-product-groups' ) );
    exit;
}
add_action( 'admin_post_caremil_delete_group', 'caremil_handle_delete_group' );

/**
 * Localize data for public form submissions
 */
function caremil_localize_frontend_trials() {
    wp_localize_script(
        'caremil-script',
        'caremilTrialForm',
        array(
            'restUrl'        => esc_url_raw( rest_url( 'caremil/v1/trials' ) ),
            'successMessage' => __( 'Cảm ơn mẹ đã đăng ký! Chúng tôi sẽ liên hệ trong thời gian sớm nhất.', 'caremil' ),
            'errorMessage'   => __( 'Có lỗi xảy ra, vui lòng thử lại.', 'caremil' ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'caremil_localize_frontend_trials' );

/**
 * Remove conflicting WP styles on full-bleed landing template
 */
function caremil_strip_wp_styles_for_landing() {
    if ( ! is_page_template( 'caremil-product.php' ) ) {
        return;
    }

    $handles = array(
        'wp-block-library',
        'wp-block-library-theme',
        'wc-block-style',
        'global-styles',
        'classic-theme-styles',
        'dashicons',
    );

    foreach ( $handles as $handle ) {
        if ( wp_style_is( $handle, 'enqueued' ) ) {
            wp_dequeue_style( $handle );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'caremil_strip_wp_styles_for_landing', 100 );

/**
 * Custom Post Type: Products (for Pancake sync + manual)
 * Changed from 'caremil_product' to 'product' for integration
 */
function caremil_register_product_cpt() {
    $labels = array(
        'name'               => __( 'Sản phẩm', 'caremil' ),
        'singular_name'      => __( 'Sản phẩm', 'caremil' ),
        'add_new'            => __( 'Thêm sản phẩm', 'caremil' ),
        'add_new_item'       => __( 'Thêm sản phẩm mới', 'caremil' ),
        'edit_item'          => __( 'Sửa sản phẩm', 'caremil' ),
        'new_item'           => __( 'Sản phẩm mới', 'caremil' ),
        'view_item'          => __( 'Xem sản phẩm', 'caremil' ),
        'search_items'       => __( 'Tìm sản phẩm', 'caremil' ),
        'not_found'          => __( 'Không tìm thấy sản phẩm.', 'caremil' ),
        'not_found_in_trash' => __( 'Không có sản phẩm trong thùng rác.', 'caremil' ),
        'menu_name'          => __( 'Sản phẩm', 'caremil' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_in_rest'       => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'san-pham' ),
        'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
        'menu_icon'          => 'dashicons-store',
        'menu_position'      => 3,
        'show_in_menu'       => true,
        'capability_type'    => 'post',
        'hierarchical'       => false,
    );

    register_post_type( 'product', $args ); // Changed from 'caremil_product' to 'product'

    $tax_labels = array(
        'name'          => __( 'Danh mục sản phẩm', 'caremil' ),
        'singular_name' => __( 'Danh mục', 'caremil' ),
        'menu_name'     => __( 'Danh mục', 'caremil' ),
        'add_new_item'  => __( 'Thêm danh mục mới', 'caremil' ),
        'edit_item'     => __( 'Sửa danh mục', 'caremil' ),
    );

    register_taxonomy(
        'product_category', // Changed from 'caremil_product_cat'
        'product',          // Changed from 'caremil_product'
        array(
            'labels'       => $tax_labels,
            'hierarchical' => true,
            'show_ui'      => true,
            'show_in_rest' => true,
            'show_in_menu' => true,
        )
    );

    // Seed 3 nhóm mặc định nếu chưa có.
    $default_terms = array(
        'box'    => __( 'Hộp Lớn', 'caremil' ),
        'sachet' => __( 'Gói Tiện Lợi', 'caremil' ),
        'combo'  => __( 'Combo Ưu Đãi', 'caremil' ),
    );

    foreach ( $default_terms as $slug => $name ) {
        if ( ! term_exists( $slug, 'caremil_product_cat' ) ) {
            wp_insert_term( $name, 'caremil_product_cat', array( 'slug' => $slug ) );
        }
    }
}
add_action( 'init', 'caremil_register_product_cpt' );

/**
 * Meta boxes cho sản phẩm
 */
function caremil_product_register_metabox() {
    add_meta_box(
        'caremil_product_meta',
        __( 'Thông tin Sản phẩm', 'caremil' ),
        'caremil_product_render_metabox',
        'caremil_product',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'caremil_product_register_metabox' );

function caremil_product_render_metabox( $post ) {
    $fields = array(
        'price'         => get_post_meta( $post->ID, 'caremil_price', true ),
        'old_price'     => get_post_meta( $post->ID, 'caremil_old_price', true ),
        'badge'         => get_post_meta( $post->ID, 'caremil_badge', true ),
        'badge_class'   => get_post_meta( $post->ID, 'caremil_badge_class', true ),
        'short_desc'    => get_post_meta( $post->ID, 'caremil_short_desc', true ),
        'rating'        => get_post_meta( $post->ID, 'caremil_rating', true ),
        'rating_count'  => get_post_meta( $post->ID, 'caremil_rating_count', true ),
        'button_label'  => get_post_meta( $post->ID, 'caremil_button_label', true ),
        'button_url'    => get_post_meta( $post->ID, 'caremil_button_url', true ),
    );

    wp_nonce_field( 'caremil_product_meta_nonce', 'caremil_product_meta_nonce_field' );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="caremil_price"><?php esc_html_e( 'Giá bán', 'caremil' ); ?></label></th>
            <td><input type="text" id="caremil_price" name="caremil_price" value="<?php echo esc_attr( $fields['price'] ); ?>" class="regular-text" placeholder="850.000đ" /></td>
        </tr>
        <tr>
            <th><label for="caremil_old_price"><?php esc_html_e( 'Giá gạch (tuỳ chọn)', 'caremil' ); ?></label></th>
            <td><input type="text" id="caremil_old_price" name="caremil_old_price" value="<?php echo esc_attr( $fields['old_price'] ); ?>" class="regular-text" placeholder="1.000.000đ" /></td>
        </tr>
        <tr>
            <th><label for="caremil_badge"><?php esc_html_e( 'Nhãn nổi bật (ví dụ: Best Seller)', 'caremil' ); ?></label></th>
            <td><input type="text" id="caremil_badge" name="caremil_badge" value="<?php echo esc_attr( $fields['badge'] ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="caremil_badge_class"><?php esc_html_e( 'Class màu nhãn (Tailwind/utility)', 'caremil' ); ?></label></th>
            <td><input type="text" id="caremil_badge_class" name="caremil_badge_class" value="<?php echo esc_attr( $fields['badge_class'] ); ?>" class="regular-text" placeholder="bg-brand-gold text-brand-navy" /></td>
        </tr>
        <tr>
            <th><label for="caremil_short_desc"><?php esc_html_e( 'Mô tả ngắn', 'caremil' ); ?></label></th>
            <td><textarea id="caremil_short_desc" name="caremil_short_desc" rows="3" class="large-text"><?php echo esc_textarea( $fields['short_desc'] ); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="caremil_rating"><?php esc_html_e( 'Điểm rating (0-5)', 'caremil' ); ?></label></th>
            <td><input type="number" id="caremil_rating" name="caremil_rating" value="<?php echo esc_attr( $fields['rating'] ? $fields['rating'] : 5 ); ?>" min="0" max="5" step="0.1" /></td>
        </tr>
        <tr>
            <th><label for="caremil_rating_count"><?php esc_html_e( 'Số lượt đánh giá', 'caremil' ); ?></label></th>
            <td><input type="number" id="caremil_rating_count" name="caremil_rating_count" value="<?php echo esc_attr( $fields['rating_count'] ? $fields['rating_count'] : 0 ); ?>" min="0" /></td>
        </tr>
        <tr>
            <th><label for="caremil_button_label"><?php esc_html_e( 'Nút CTA', 'caremil' ); ?></label></th>
            <td><input type="text" id="caremil_button_label" name="caremil_button_label" value="<?php echo esc_attr( $fields['button_label'] ? $fields['button_label'] : __( 'Chọn Mua', 'caremil' ) ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="caremil_button_url"><?php esc_html_e( 'Link CTA', 'caremil' ); ?></label></th>
            <td><input type="url" id="caremil_button_url" name="caremil_button_url" value="<?php echo esc_attr( $fields['button_url'] ); ?>" class="regular-text code" placeholder="https://..." /></td>
        </tr>
    </table>
    <p class="description"><?php esc_html_e( 'Chọn Nhóm sản phẩm (Hộp Lớn / Gói Tiện Lợi / Combo) ở khung phân loại bên phải.', 'caremil' ); ?></p>
    <?php
}

function caremil_product_save_metabox( $post_id ) {
    if ( ! isset( $_POST['caremil_product_meta_nonce_field'] ) || ! wp_verify_nonce( sanitize_key( $_POST['caremil_product_meta_nonce_field'] ), 'caremil_product_meta_nonce' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( isset( $_POST['post_type'] ) && 'caremil_product' === $_POST['post_type'] && ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $fields = array(
        'caremil_price'        => isset( $_POST['caremil_price'] ) ? sanitize_text_field( wp_unslash( $_POST['caremil_price'] ) ) : '',
        'caremil_old_price'    => isset( $_POST['caremil_old_price'] ) ? sanitize_text_field( wp_unslash( $_POST['caremil_old_price'] ) ) : '',
        'caremil_badge'        => isset( $_POST['caremil_badge'] ) ? sanitize_text_field( wp_unslash( $_POST['caremil_badge'] ) ) : '',
        'caremil_badge_class'  => isset( $_POST['caremil_badge_class'] ) ? sanitize_text_field( wp_unslash( $_POST['caremil_badge_class'] ) ) : '',
        'caremil_short_desc'   => isset( $_POST['caremil_short_desc'] ) ? wp_kses_post( wp_unslash( $_POST['caremil_short_desc'] ) ) : '',
        'caremil_rating'       => isset( $_POST['caremil_rating'] ) ? floatval( $_POST['caremil_rating'] ) : 0,
        'caremil_rating_count' => isset( $_POST['caremil_rating_count'] ) ? intval( $_POST['caremil_rating_count'] ) : 0,
        'caremil_button_label' => isset( $_POST['caremil_button_label'] ) ? sanitize_text_field( wp_unslash( $_POST['caremil_button_label'] ) ) : '',
        'caremil_button_url'   => isset( $_POST['caremil_button_url'] ) ? esc_url_raw( wp_unslash( $_POST['caremil_button_url'] ) ) : '',
    );

    foreach ( $fields as $key => $value ) {
        if ( '' === $value || ( is_numeric( $value ) && 0 === $value ) ) {
            delete_post_meta( $post_id, $key );
            continue;
        }
        update_post_meta( $post_id, $key, $value );
    }
}
add_action( 'save_post', 'caremil_product_save_metabox' );

/**
 * Hiển thị cột giá và nhóm trong admin list
 */
function caremil_product_columns( $columns ) {
    $columns['caremil_price'] = __( 'Giá', 'caremil' );
    $columns['caremil_group'] = __( 'Nhóm', 'caremil' );
    return $columns;
}
add_filter( 'manage_caremil_product_posts_columns', 'caremil_product_columns' );

function caremil_product_columns_content( $column, $post_id ) {
    if ( 'caremil_price' === $column ) {
        echo esc_html( get_post_meta( $post_id, 'caremil_price', true ) );
    }

    if ( 'caremil_group' === $column ) {
        $terms = wp_get_post_terms( $post_id, 'caremil_product_cat', array( 'fields' => 'names' ) );
        echo esc_html( implode( ', ', $terms ) );
    }
}
add_action( 'manage_caremil_product_posts_custom_column', 'caremil_product_columns_content', 10, 2 );

/**
 * Đặt menu Products ngay dưới mục đăng ký dùng thử
 */
function caremil_product_menu_position( $args, $post_type ) {
    if ( 'caremil_product' === $post_type ) {
        $args['menu_position'] = 4;
    }
    return $args;
}
add_filter( 'register_post_type_args', 'caremil_product_menu_position', 10, 2 );

/**
 * Đăng ký meta cho REST
 */
function caremil_register_product_meta() {
    $meta_fields = array(
        'caremil_price'        => 'string',
        'caremil_old_price'    => 'string',
        'caremil_badge'        => 'string',
        'caremil_badge_class'  => 'string',
        'caremil_short_desc'   => 'string',
        'caremil_rating'       => 'number',
        'caremil_rating_count' => 'integer',
        'caremil_button_label' => 'string',
        'caremil_button_url'   => 'string',
    );

    foreach ( $meta_fields as $key => $type ) {
        register_post_meta(
            'caremil_product',
            $key,
            array(
                'type'         => $type,
                'single'       => true,
                'show_in_rest' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                },
            )
        );
    }
}
add_action( 'init', 'caremil_register_product_meta' );

/**
 * (Đã bỏ) trang submenu UX – chuyển sang override trực tiếp màn add/edit.
 */

/**
 * (Custom add/edit UI removed; using WordPress default editor)
 */

/**
 * ============================================
 * HỆ THỐNG GIỎ HÀNG CUSTOM
 * ============================================
 */

/**
 * Khởi tạo session cho giỏ hàng
 */
function caremil_init_cart_session() {
    if ( ! session_id() ) {
        session_start();
    }
    
    if ( ! isset( $_SESSION['caremil_cart'] ) ) {
        $_SESSION['caremil_cart'] = array();
    }
}
add_action( 'init', 'caremil_init_cart_session', 1 );

/**
 * Lấy giỏ hàng hiện tại
 */
function caremil_get_cart() {
    caremil_init_cart_session();
    return isset( $_SESSION['caremil_cart'] ) ? $_SESSION['caremil_cart'] : array();
}

/**
 * Lấy tổng số lượng sản phẩm trong giỏ hàng
 */
function caremil_get_cart_count() {
    $cart = caremil_get_cart();
    $count = 0;
    foreach ( $cart as $item ) {
        $count += isset( $item['quantity'] ) ? intval( $item['quantity'] ) : 0;
    }
    return $count;
}

/**
 * Lấy tổng tiền giỏ hàng
 */
function caremil_get_cart_total() {
    $cart = caremil_get_cart();
    $total = 0;
    foreach ( $cart as $item ) {
        $price = isset( $item['price'] ) ? floatval( str_replace( array( '.', ',' ), '', $item['price'] ) ) : 0;
        $qty = isset( $item['quantity'] ) ? intval( $item['quantity'] ) : 0;
        $total += $price * $qty;
    }
    return $total;
}

/**
 * Thêm sản phẩm vào giỏ hàng
 */
function caremil_add_to_cart( $product_id, $quantity = 1, $variant = 'main', $variant_label = '', $price = '', $old_price = '', $image = '' ) {
    caremil_init_cart_session();
    
    $cart_key = $product_id . '_' . $variant;
    
    if ( isset( $_SESSION['caremil_cart'][ $cart_key ] ) ) {
        // Nếu đã có, tăng số lượng
        $_SESSION['caremil_cart'][ $cart_key ]['quantity'] += intval( $quantity );
    } else {
        // Thêm mới
        $_SESSION['caremil_cart'][ $cart_key ] = array(
            'product_id'   => intval( $product_id ),
            'quantity'     => intval( $quantity ),
            'variant'      => sanitize_text_field( $variant ),
            'variant_label' => sanitize_text_field( $variant_label ),
            'price'        => sanitize_text_field( $price ),
            'old_price'    => sanitize_text_field( $old_price ),
            'image'        => esc_url_raw( $image ),
        );
    }
    
    return true;
}

/**
 * Cập nhật số lượng sản phẩm trong giỏ hàng
 */
function caremil_update_cart_item( $cart_key, $quantity ) {
    caremil_init_cart_session();
    
    if ( isset( $_SESSION['caremil_cart'][ $cart_key ] ) ) {
        if ( intval( $quantity ) <= 0 ) {
            unset( $_SESSION['caremil_cart'][ $cart_key ] );
        } else {
            $_SESSION['caremil_cart'][ $cart_key ]['quantity'] = intval( $quantity );
        }
        return true;
    }
    
    return false;
}

/**
 * Xóa sản phẩm khỏi giỏ hàng
 */
function caremil_remove_from_cart( $cart_key ) {
    caremil_init_cart_session();
    
    if ( isset( $_SESSION['caremil_cart'][ $cart_key ] ) ) {
        unset( $_SESSION['caremil_cart'][ $cart_key ] );
        return true;
    }
    
    return false;
}

/**
 * Xóa toàn bộ giỏ hàng
 */
function caremil_empty_cart() {
    caremil_init_cart_session();
    $_SESSION['caremil_cart'] = array();
    return true;
}

/**
 * AJAX: Thêm vào giỏ hàng
 */
function caremil_ajax_add_to_cart() {
    check_ajax_referer( 'caremil_cart_nonce', 'nonce' );
    
    $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
    $quantity = isset( $_POST['quantity'] ) ? intval( $_POST['quantity'] ) : 1;
    $variant = isset( $_POST['variant'] ) ? sanitize_text_field( $_POST['variant'] ) : 'main';
    $variant_label = isset( $_POST['variant_label'] ) ? sanitize_text_field( $_POST['variant_label'] ) : '';
    $price = isset( $_POST['price'] ) ? sanitize_text_field( $_POST['price'] ) : '';
    $old_price = isset( $_POST['old_price'] ) ? sanitize_text_field( $_POST['old_price'] ) : '';
    $image = isset( $_POST['image'] ) ? esc_url_raw( $_POST['image'] ) : '';
    
    if ( $product_id <= 0 ) {
        wp_send_json_error( array( 'message' => 'Sản phẩm không hợp lệ' ) );
    }
    
    // Nếu không có thông tin sản phẩm từ POST, lấy từ database
    if ( empty( $price ) || empty( $image ) ) {
        $post = get_post( $product_id );
        if ( ! $post || ! in_array( $post->post_type, array( 'caremil_product', 'product' ) ) ) {
            wp_send_json_error( array( 'message' => 'Sản phẩm không tồn tại' ) );
        }
        
        if ( empty( $price ) ) {
            $price = get_post_meta( $product_id, 'caremil_price', true );
        }
        if ( empty( $old_price ) ) {
            $old_price = get_post_meta( $product_id, 'caremil_old_price', true );
        }
        if ( empty( $image ) ) {
            $image = get_the_post_thumbnail_url( $product_id, 'medium' );
        }
        if ( empty( $variant_label ) ) {
            $variant_label = get_the_title( $product_id );
        }
    }
    
    caremil_add_to_cart( $product_id, $quantity, $variant, $variant_label, $price, $old_price, $image );
    
    wp_send_json_success( array(
        'message'    => 'Đã thêm vào giỏ hàng',
        'cart_count' => caremil_get_cart_count(),
        'cart_total' => caremil_format_price( caremil_get_cart_total() ),
    ) );
}
add_action( 'wp_ajax_caremil_add_to_cart', 'caremil_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_caremil_add_to_cart', 'caremil_ajax_add_to_cart' );

/**
 * AJAX: Cập nhật số lượng
 */
function caremil_ajax_update_cart() {
    check_ajax_referer( 'caremil_cart_nonce', 'nonce' );
    
    $cart_key = isset( $_POST['cart_key'] ) ? sanitize_text_field( $_POST['cart_key'] ) : '';
    $quantity = isset( $_POST['quantity'] ) ? intval( $_POST['quantity'] ) : 0;
    
    if ( empty( $cart_key ) ) {
        wp_send_json_error( array( 'message' => 'Không hợp lệ' ) );
    }
    
    caremil_update_cart_item( $cart_key, $quantity );
    
    wp_send_json_success( array(
        'cart_count' => caremil_get_cart_count(),
        'cart_total' => caremil_format_price( caremil_get_cart_total() ),
    ) );
}
add_action( 'wp_ajax_caremil_update_cart', 'caremil_ajax_update_cart' );
add_action( 'wp_ajax_nopriv_caremil_update_cart', 'caremil_ajax_update_cart' );

/**
 * AJAX: Xóa khỏi giỏ hàng
 */
function caremil_ajax_remove_from_cart() {
    check_ajax_referer( 'caremil_cart_nonce', 'nonce' );
    
    $cart_key = isset( $_POST['cart_key'] ) ? sanitize_text_field( $_POST['cart_key'] ) : '';
    
    if ( empty( $cart_key ) ) {
        wp_send_json_error( array( 'message' => 'Không hợp lệ' ) );
    }
    
    caremil_remove_from_cart( $cart_key );
    
    wp_send_json_success( array(
        'message'    => 'Đã xóa khỏi giỏ hàng',
        'cart_count' => caremil_get_cart_count(),
        'cart_total' => caremil_format_price( caremil_get_cart_total() ),
    ) );
}
add_action( 'wp_ajax_caremil_remove_from_cart', 'caremil_ajax_remove_from_cart' );
add_action( 'wp_ajax_nopriv_caremil_remove_from_cart', 'caremil_ajax_remove_from_cart' );

/**
 * AJAX: Xóa toàn bộ giỏ hàng
 */
function caremil_ajax_empty_cart() {
    check_ajax_referer( 'caremil_cart_nonce', 'nonce' );
    
    caremil_empty_cart();
    
    wp_send_json_success( array(
        'message'    => 'Đã xóa toàn bộ giỏ hàng',
        'cart_count' => 0,
        'cart_total' => caremil_format_price( 0 ),
    ) );
}
add_action( 'wp_ajax_caremil_empty_cart', 'caremil_ajax_empty_cart' );
add_action( 'wp_ajax_nopriv_caremil_empty_cart', 'caremil_ajax_empty_cart' );

/**
 * AJAX: Lấy số lượng giỏ hàng
 */
function caremil_ajax_get_cart_count() {
    wp_send_json_success( array(
        'cart_count' => caremil_get_cart_count(),
        'cart_total' => caremil_format_price( caremil_get_cart_total() ),
    ) );
}
add_action( 'wp_ajax_caremil_get_cart_count', 'caremil_ajax_get_cart_count' );
add_action( 'wp_ajax_nopriv_caremil_get_cart_count', 'caremil_ajax_get_cart_count' );

/**
 * Format giá tiền
 */
function caremil_format_price( $amount ) {
    return number_format( $amount, 0, ',', '.' ) . 'đ';
}

/**
 * Lấy URL của page theo template name
 */
function caremil_get_page_url_by_template( $template_name ) {
    $pages = get_pages( array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => $template_name . '.php'
    ) );
    
    if ( ! empty( $pages ) ) {
        return get_permalink( $pages[0]->ID );
    }
    
    // Fallback: thử tìm theo slug
    $slug_map = array(
        'Payment' => 'thanh-toan',
        'Order Status' => 'trang-thai-don-hang',
        'Checkout' => 'thanh-toan',
        'Carts' => 'gio-hang',
    );
    
    if ( isset( $slug_map[ $template_name ] ) ) {
        return home_url( '/' . $slug_map[ $template_name ] . '/' );
    }
    
    return home_url( '/' );
}

/**
 * Localize script cho cart AJAX
 */
function caremil_localize_cart_script() {
    wp_localize_script(
        'caremil-script',
        'caremilCart',
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'caremil_cart_nonce' ),
            'cart_count' => caremil_get_cart_count(),
            'cart_total' => caremil_format_price( caremil_get_cart_total() ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'caremil_localize_cart_script' );

/**
 * ====== PANCAKE HELPERS (chia sẻ cho checkout & account) ======
 */

/**
 * Lấy Pancake API Key từ options (ưu tiên) hoặc constants (fallback)
 */
if ( ! function_exists( 'caremil_get_pancake_api_key' ) ) {
    function caremil_get_pancake_api_key() {
        $api_key = get_option( 'caremil_pancake_api_key', '' );
        if ( ! empty( $api_key ) ) {
            return $api_key;
        }
        // Fallback về constant nếu chưa cấu hình trong admin
        if ( defined( 'CAREMIL_PANCAKE_API_KEY' ) ) {
            return CAREMIL_PANCAKE_API_KEY;
        }
        return '';
    }
}

/**
 * Lấy Pancake Shop ID từ options (ưu tiên) hoặc constants (fallback)
 */
if ( ! function_exists( 'caremil_get_pancake_shop_id' ) ) {
    function caremil_get_pancake_shop_id() {
        $shop_id = get_option( 'caremil_pancake_shop_id', '' );
        if ( ! empty( $shop_id ) ) {
            return $shop_id;
        }
        // Fallback về constant nếu chưa cấu hình trong admin
        if ( defined( 'CAREMIL_PANCAKE_SHOP_ID' ) ) {
            return CAREMIL_PANCAKE_SHOP_ID;
        }
        return '';
    }
}

/**
 * Lấy Pancake Base URL từ options (ưu tiên) hoặc constants (fallback)
 */
if ( ! function_exists( 'caremil_get_pancake_base_url' ) ) {
    function caremil_get_pancake_base_url() {
        $base_url = get_option( 'caremil_pancake_base_url', '' );
        if ( ! empty( $base_url ) ) {
            return $base_url;
        }
        // Fallback về constant nếu chưa cấu hình trong admin
        if ( defined( 'CAREMIL_PANCAKE_BASE_URL' ) ) {
            return CAREMIL_PANCAKE_BASE_URL;
        }
        return 'https://pos.pages.fm/api/v1';
    }
}

/**
 * Lấy Pancake Warehouse ID từ options
 */
if ( ! function_exists( 'caremil_get_pancake_warehouse_id' ) ) {
    function caremil_get_pancake_warehouse_id() {
        $warehouse_id = get_option( 'caremil_pancake_warehouse_id', '' );
        return $warehouse_id;
    }
}

// Định nghĩa constants để tương thích với code cũ (chỉ dùng làm fallback)
if ( ! defined( 'CAREMIL_PANCAKE_API_KEY' ) ) {
    define( 'CAREMIL_PANCAKE_API_KEY', '5a5e73eca1c14dacb904e75cfb8e98a2' );
}

if ( ! defined( 'CAREMIL_PANCAKE_SHOP_ID' ) ) {
    define( 'CAREMIL_PANCAKE_SHOP_ID', '1942324124' );
}

if ( ! defined( 'CAREMIL_PANCAKE_BASE_URL' ) ) {
    define( 'CAREMIL_PANCAKE_BASE_URL', 'https://pos.pages.fm/api/v1' );
}

/**
 * Kiểm tra kết nối Pancake API với cache (tránh check quá nhiều)
 * Cache trong 5 phút
 */
if ( ! function_exists( 'caremil_check_pancake_connection' ) ) {
    function caremil_check_pancake_connection( $force_check = false ) {
        // Kiểm tra cache trước
        $cache_key = 'caremil_pancake_connection_status';
        $cache_time = 5 * MINUTE_IN_SECONDS; // Cache 5 phút
        
        if ( ! $force_check ) {
            $cached = get_transient( $cache_key );
            if ( $cached !== false ) {
                return $cached === 'connected';
            }
        }
        
        // Kiểm tra config có đầy đủ không
        $api_key = caremil_get_pancake_api_key();
        $shop_id = caremil_get_pancake_shop_id();
        $base_url = caremil_get_pancake_base_url();
        
        // Lưu thông tin debug
        $debug_info = array(
            'timestamp' => current_time( 'mysql' ),
            'api_key_set' => ! empty( $api_key ),
            'shop_id_set' => ! empty( $shop_id ),
            'base_url_set' => ! empty( $base_url ),
            'base_url' => $base_url,
            'shop_id' => $shop_id,
        );
        
        if ( empty( $api_key ) || empty( $shop_id ) || empty( $base_url ) ) {
            $debug_info['error'] = 'Missing configuration';
            $debug_info['missing'] = array();
            if ( empty( $api_key ) ) $debug_info['missing'][] = 'API Key';
            if ( empty( $shop_id ) ) $debug_info['missing'][] = 'Shop ID';
            if ( empty( $base_url ) ) $debug_info['missing'][] = 'Base URL';
            update_option( 'caremil_pancake_debug_info', $debug_info );
            set_transient( $cache_key, 'disconnected', $cache_time );
            return false;
        }
        
        // Thử gọi API đơn giản để test connection
        $test_path = '/shops/' . $shop_id . '/customers';
        $test_query = array( 'page_size' => 1 );
        
        $api_key_val = $api_key;
        $test_query['api_key'] = $api_key_val;
        
        $test_url = rtrim( $base_url, '/' ) . '/' . ltrim( $test_path, '/' );
        $test_url = add_query_arg( $test_query, $test_url );
        
        $debug_info['test_url'] = str_replace( $api_key_val, '***HIDDEN***', $test_url );
        
        $args = array(
            'timeout' => 10, // Timeout ngắn hơn cho test
            'method'  => 'GET',
            'headers' => array(
                'Accept' => 'application/json',
            ),
        );
        
        $response = wp_remote_request( $test_url, $args );
        
        if ( is_wp_error( $response ) ) {
            $debug_info['error'] = 'WP_Error: ' . $response->get_error_message();
            $debug_info['error_code'] = $response->get_error_code();
            update_option( 'caremil_pancake_debug_info', $debug_info );
            set_transient( $cache_key, 'disconnected', $cache_time );
            return false;
        }
        
        $status = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );
        
        $debug_info['http_status'] = $status;
        $debug_info['response_body'] = substr( $body, 0, 500 ); // Lưu 500 ký tự đầu
        
        // Nếu status code là 200-299 hoặc 400-499 (có thể là lỗi validation nhưng API đang hoạt động)
        if ( $status >= 200 && $status < 500 ) {
            $debug_info['status'] = 'connected';
            update_option( 'caremil_pancake_debug_info', $debug_info );
            set_transient( $cache_key, 'connected', $cache_time );
            return true;
        }
        
        $debug_info['error'] = 'Invalid HTTP status: ' . $status;
        $debug_info['status'] = 'disconnected';
        update_option( 'caremil_pancake_debug_info', $debug_info );
        set_transient( $cache_key, 'disconnected', $cache_time );
        return false;
    }
}

/**
 * Hiển thị thông báo hệ thống đang bảo trì khi không kết nối được Pancake
 */
if ( ! function_exists( 'caremil_display_maintenance_message' ) ) {
    function caremil_display_maintenance_message() {
        ?>
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Hệ thống đang bảo trì - <?php bloginfo('name'); ?></title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
            <style>
                body {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                }
            </style>
        </head>
        <body>
            <div class="max-w-md w-full mx-4">
                <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
                    <div class="mb-6">
                        <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-tools text-yellow-600 text-3xl"></i>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Hệ thống đang bảo trì</h1>
                        <p class="text-gray-600 text-lg">Chúng tôi đang nâng cấp hệ thống để phục vụ bạn tốt hơn</p>
                    </div>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 text-left rounded">
                        <p class="text-blue-800 text-sm leading-relaxed">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Vui lòng thử lại sau ít phút</strong> hoặc liên hệ Admin để được hỗ trợ sớm nhất.
                        </p>
                    </div>
                    
                    <div class="space-y-3">
                        <a href="<?php echo esc_url( home_url() ); ?>" 
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                            <i class="fas fa-home mr-2"></i> Về trang chủ
                        </a>
                        
                        <a href="mailto:<?php echo esc_attr( get_option('admin_email') ); ?>?subject=Hỗ trợ hệ thống" 
                           class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg transition duration-200">
                            <i class="fas fa-envelope mr-2"></i> Liên hệ Admin
                        </a>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-clock mr-1"></i>
                            Thời gian dự kiến: 5-10 phút
                        </p>
                    </div>
                </div>
                
                <div class="text-center mt-6">
                    <p class="text-white text-sm opacity-90">
                        <?php bloginfo('name'); ?> &copy; <?php echo date('Y'); ?>
                    </p>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

/**
 * Kiểm tra và chặn nếu không kết nối được Pancake
 */
if ( ! function_exists( 'caremil_require_pancake_connection' ) ) {
    function caremil_require_pancake_connection( $force_check = false ) {
        if ( ! caremil_check_pancake_connection( $force_check ) ) {
            caremil_display_maintenance_message();
        }
    }
}

if ( ! function_exists( 'caremil_pancake_request' ) ) {
    /**
     * Gọi API Pancake (v1) kèm api_key dạng query param.
     */
    function caremil_pancake_request( $path, $query = array(), $method = 'GET', $body = null ) {
        $api_key = caremil_get_pancake_api_key();
        if ( empty( $api_key ) ) {
            return array('success' => false, 'message' => 'Missing API Key');
        }

        $query = array_merge(
            array( 'api_key' => $api_key ),
            $query
        );

        $base_url = caremil_get_pancake_base_url();
        $url = rtrim( $base_url, '/' ) . '/' . ltrim( $path, '/' );
        $url = add_query_arg( $query, $url );

        $args = array(
            'timeout' => 20,
            'method'  => $method,
            'headers' => array(
                'Accept' => 'application/json',
            ),
        );

        if ( 'GET' !== $method && ! is_null( $body ) ) {
            $args['headers']['Content-Type'] = 'application/json';
            $args['body']                    = wp_json_encode( $body );
        }

        $response = wp_remote_request( $url, $args );
        if ( is_wp_error( $response ) ) {
            error_log('Pancake API Error (WP_Error): ' . $response->get_error_message());
            return array('success' => false, 'message' => $response->get_error_message());
        }

        $status = wp_remote_retrieve_response_code( $response );
        $payload = wp_remote_retrieve_body( $response );
        $data    = json_decode( $payload, true );

        if ( $status < 200 || $status >= 300 ) {
            error_log("Pancake API Failed ($status): " . print_r($payload, true));
            return isset($data) && is_array($data) ? $data : array('success' => false, 'message' => "HTTP Error $status", 'body' => $payload);
        }

        return $data ? $data : null;
    }
}

/**
 * Lấy danh sách kho hàng từ Pancake API
 */
if ( ! function_exists( 'caremil_get_pancake_warehouses' ) ) {
    function caremil_get_pancake_warehouses( $force_refresh = false ) {
        // Kiểm tra cache trước
        $cache_key = 'caremil_pancake_warehouses';
        $cache_time = 10 * MINUTE_IN_SECONDS; // Cache 10 phút
        
        if ( ! $force_refresh ) {
            $cached = get_transient( $cache_key );
            if ( $cached !== false ) {
                return $cached;
            }
        }
        
        // Lấy shop ID
        $shop_id = caremil_get_pancake_shop_id();
        if ( empty( $shop_id ) ) {
            return array();
        }
        
        // Gọi API để lấy danh sách kho
        $path = '/shops/' . $shop_id . '/warehouses';
        $response = caremil_pancake_request( $path );
        
        if ( ! $response || empty( $response['data'] ) ) {
            return array();
        }
        
        $warehouses = array();
        foreach ( $response['data'] as $wh ) {
            $warehouses[] = array(
                'id' => $wh['id'] ?? '',
                'name' => $wh['name'] ?? 'Không có tên',
                'address' => $wh['full_address'] ?? '',
                'phone_number' => $wh['phone_number'] ?? '',
            );
        }
        
        // Lưu vào cache
        set_transient( $cache_key, $warehouses, $cache_time );
        
        return $warehouses;
    }
}

if ( ! function_exists( 'caremil_normalize_phone' ) ) {
    /**
     * Chuẩn hóa SĐT về 0xxxxxxxx.
     */
    function caremil_normalize_phone( $phone ) {
        $clean = preg_replace( '/[^0-9+]/', '', $phone ?? '' );
        if ( strpos( $clean, '+84' ) === 0 ) {
            return '0' . substr( $clean, 3 );
        }
        if ( strpos( $clean, '84' ) === 0 ) {
            return '0' . substr( $clean, 2 );
        }
        return $clean;
    }
}

/**
 * Rút danh sách địa chỉ từ cấu trúc trả về của Pancake.
 */
function caremil_get_pancake_addresses_from_customer( $customer ) {
    if ( ! is_array( $customer ) ) {
        return array();
    }

    if ( ! empty( $customer['shop_customer_addresses'] ) && is_array( $customer['shop_customer_addresses'] ) ) {
        return $customer['shop_customer_addresses'];
    }

    if ( ! empty( $customer['shop_customer_address'] ) && is_array( $customer['shop_customer_address'] ) ) {
        return $customer['shop_customer_address'];
    }

    return array();
}

/**
 * AJAX: Lưu địa chỉ mới cho khách Pancake (dùng cho checkout).
 */
function caremil_ajax_save_address() {
    // Kiểm tra kết nối Pancake trước
    if ( function_exists( 'caremil_check_pancake_connection' ) ) {
        if ( ! caremil_check_pancake_connection() ) {
            wp_send_json_error( array(
                'message' => 'Hệ thống đang bảo trì. Vui lòng thử lại sau ít phút hoặc liên hệ Admin để được hỗ trợ sớm nhất.',
                'maintenance' => true
            ), 503 );
        }
    }
    
    if ( ! session_id() ) {
        session_start();
    }

    $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
    if ( ! wp_verify_nonce( $nonce, 'caremil_addr_nonce' ) ) {
        wp_send_json_error(
            array( 'message' => 'Phiên không hợp lệ.' ),
            403
        );
    }

    if ( empty( $_SESSION['pancake_logged_in'] ) || empty( $_SESSION['pancake_customer_id'] ) ) {
        wp_send_json_error( array( 'message' => 'Bạn cần đăng nhập để lưu địa chỉ.' ), 401 );
    }

    $customer_id = sanitize_text_field( $_SESSION['pancake_customer_id'] );
    $full_name   = isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';
    $phone       = isset( $_POST['phone_number'] ) ? sanitize_text_field( wp_unslash( $_POST['phone_number'] ) ) : '';
    $full_addr   = isset( $_POST['full_address'] ) ? sanitize_text_field( wp_unslash( $_POST['full_address'] ) ) : '';
    $province_id = isset( $_POST['province_id'] ) ? sanitize_text_field( wp_unslash( $_POST['province_id'] ) ) : '';
    $district_id = isset( $_POST['district_id'] ) ? sanitize_text_field( wp_unslash( $_POST['district_id'] ) ) : '';
    $commune_id  = isset( $_POST['commune_id'] ) ? sanitize_text_field( wp_unslash( $_POST['commune_id'] ) ) : '';

    if ( empty( $full_addr ) ) {
        wp_send_json_error( array( 'message' => 'Vui lòng nhập địa chỉ đầy đủ.' ) );
    }

    $customer = caremil_pancake_request(
        "/shops/" . caremil_get_pancake_shop_id() . "/customers/{$customer_id}"
    );

    $addr_list = caremil_get_pancake_addresses_from_customer( $customer );
    $normalize = function ( $value ) {
        $value = strtolower( trim( $value ?? '' ) );
        $value = preg_replace( '/\s+/', ' ', $value );
        return $value;
    };

    $new_addr_normalized = $normalize( $full_addr );

    foreach ( $addr_list as $item ) {
        $existing = $normalize( $item['full_address'] ?? $item['address'] ?? '' );
        if ( $existing && $existing === $new_addr_normalized ) {
            wp_send_json_success( array( 'addresses' => $addr_list, 'duplicated' => true ) );
        }
    }

    $addr_payload_item = array(
        'full_name'    => $full_name,
        'phone_number' => $phone,
        'full_address' => $full_addr,
        'address'      => $full_addr,
        'province_id'  => $province_id,
        'district_id'  => $district_id,
        'commune_id'   => $commune_id,
        'country_code' => '84',
    );

    $addr_list[] = $addr_payload_item;

    caremil_pancake_request(
        "/shops/" . caremil_get_pancake_shop_id() . "/customers/{$customer_id}",
        array(),
        'PUT',
        array(
            'customer' => array(
                'shop_customer_address'   => $addr_list,
                'shop_customer_addresses' => $addr_list,
            ),
        )
    );

    $updated_customer = caremil_pancake_request(
        "/shops/" . caremil_get_pancake_shop_id() . "/customers/{$customer_id}"
    );
    $updated_addresses = caremil_get_pancake_addresses_from_customer( $updated_customer );

    wp_send_json_success(
        array(
            'addresses' => $updated_addresses,
            'duplicated' => false,
        )
    );
}
add_action( 'wp_ajax_caremil_save_address', 'caremil_ajax_save_address' );
add_action( 'wp_ajax_nopriv_caremil_save_address', 'caremil_ajax_save_address' );

// 1. Tự động tạo bảng khi kích hoạt theme hoặc truy cập admin
function create_pancake_customers_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pancake_customers';
    $charset_collate = $wpdb->get_charset_collate();

    // SQL tạo bảng: Lưu SĐT, Mật khẩu (đã mã hóa), Tên, Email, Pancake ID
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        phone varchar(20) NOT NULL,
        password varchar(255) NOT NULL,
        name varchar(100) DEFAULT '' NOT NULL,
        email varchar(100) DEFAULT '' NOT NULL,
        pancake_id varchar(50) DEFAULT '' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY phone (phone)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql); // Hàm này của WP sẽ tự tạo bảng nếu chưa có, hoặc cập nhật nếu thiếu cột
}
// Chạy hàm này khi admin được khởi tạo (đảm bảo bảng luôn tồn tại)
add_action('admin_init', 'create_pancake_customers_table');

/**
 * AJAX handler: Lấy danh sách kho hàng
 */
function caremil_ajax_get_warehouses() {
    // Verify nonce
    check_ajax_referer('caremil_get_warehouses_nonce', 'nonce');
    
    // Kiểm tra quyền
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền truy cập'));
    }
    
    // Lấy danh sách kho (force refresh = true)
    $warehouses = caremil_get_pancake_warehouses(true);
    
    if (empty($warehouses)) {
        wp_send_json_error(array('message' => 'Không thể lấy danh sách kho. Vui lòng kiểm tra kết nối Pancake.'));
    }
    
    wp_send_json_success($warehouses);
}
add_action('wp_ajax_caremil_get_warehouses', 'caremil_ajax_get_warehouses');

// 2. Thêm Menu Pancake POS vào Admin Dashboard (Gộp tất cả vào 1 menu)
function register_pancake_menu_page() {
    // Menu cha: Pancake POS
    add_menu_page(
        'Pancake POS',           // Page Title
        'Pancake POS',           // Menu Title
        'manage_options',        // Capability
        'pancake-dashboard',     // Menu Slug (parent)
        'render_pancake_dashboard_page', // Callback
        'dashicons-store',       // Icon (Store icon)
        6                        // Position
    );
    
    // Submenu 1: Cài đặt (Settings)
    add_submenu_page(
        'pancake-dashboard',     // Parent slug
        'Cài đặt Pancake',      // Page title
        '⚙️ Cài đặt',           // Menu title (with icon)
        'manage_options',
        'pancake-settings',
        'render_pancake_settings_page'
    );
    
    // Submenu 2: Khách hàng (Customers)
    add_submenu_page(
        'pancake-dashboard',
        'Khách hàng Pancake',
        '👥 Khách hàng',
        'manage_options',
        'pancake-customers',
        'render_pancake_customers_page'
    );
    
    // Đổi tên submenu đầu tiên (Dashboard) thành "Tổng quan"
    global $submenu;
    if (isset($submenu['pancake-dashboard'])) {
        $submenu['pancake-dashboard'][0][0] = '📊 Tổng quan';
    }
}
add_action('admin_menu', 'register_pancake_menu_page');

// Dashboard page callback
function render_pancake_dashboard_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('Bạn không có quyền truy cập trang này.'));
    }
    
    $is_connected = function_exists('caremil_check_pancake_connection') && caremil_check_pancake_connection();
    $shop_id = caremil_get_pancake_shop_id();
    $warehouse_id = get_option('caremil_pancake_warehouse_id', '');
    
    // Count synced products
    $synced_products = get_posts(array(
        'post_type' => 'product',
        'meta_key' => 'pancake_product_id',
        'posts_per_page' => -1,
        'post_status' => 'any'
    ));
    
    ?>
    <div class="wrap">
        <h1>📦 Pancake POS - Tổng Quan</h1>
        <p class="description">Quản lý tích hợp Pancake POS với WordPress</p>
        
        <div class="pancake-dashboard" style="margin-top: 30px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                
                <!-- Connection Status Card -->
                <div class="card" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h2 style="margin-top: 0; display: flex; align-items: center; gap: 10px;">
                        <span class="dashicons dashicons-admin-plugins" style="font-size: 24px; color: <?php echo $is_connected ? '#10b981' : '#ef4444'; ?>;"></span>
                        Trạng thái kết nối
                    </h2>
                    <?php if ($is_connected): ?>
                        <p style="font-size: 18px; color: #10b981; font-weight: bold;">
                            ✓ Đã kết nối
                        </p>
                        <p style="color: #64748b;">Shop ID: <code><?php echo esc_html($shop_id); ?></code></p>
                    <?php else: ?>
                        <p style="font-size: 18px; color: #ef4444; font-weight: bold;">
                            ✗ Chưa kết nối
                        </p>
                        <p><a href="<?php echo admin_url('admin.php?page=pancake-settings'); ?>" class="button button-primary">Cấu hình ngay</a></p>
                    <?php endif; ?>
                </div>
                
                <!-- Products Card -->
                <div class="card" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h2 style="margin-top: 0; display: flex; align-items: center; gap: 10px;">
                        <span class="dashicons dashicons-products" style="font-size: 24px; color: #3b82f6;"></span>
                        Sản phẩm đã đồng bộ
                    </h2>
                    <p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #0f172a;">
                        <?php echo count($synced_products); ?>
                    </p>
                    <p>
                        <a href="<?php echo admin_url('admin.php?page=pancake-product-sync'); ?>" class="button">
                            🔄 Đồng bộ sản phẩm
                        </a>
                    </p>
                </div>
                
                <!-- Warehouse Card -->
                <div class="card" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h2 style="margin-top: 0; display: flex; align-items: center; gap: 10px;">
                        <span class="dashicons dashicons-building" style="font-size: 24px; color: #8b5cf6;"></span>
                        Kho hàng
                    </h2>
                    <?php if ($warehouse_id): 
                        $warehouse_name = '';
                        if (function_exists('caremil_get_pancake_warehouses')) {
                            $warehouses = caremil_get_pancake_warehouses();
                            foreach ($warehouses as $wh) {
                                if ($wh['id'] === $warehouse_id) {
                                    $warehouse_name = $wh['name'];
                                    break;
                                }
                            }
                        }
                    ?>
                        <p style="font-size: 18px; font-weight: bold; color: #0f172a;">
                            <?php echo esc_html($warehouse_name ? $warehouse_name : $warehouse_id); ?>
                        </p>
                        <p style="color: #64748b; font-size: 12px;">ID: <?php echo esc_html($warehouse_id); ?></p>
                    <?php else: ?>
                        <p style="color: #ef4444;">Chưa chọn kho</p>
                        <p><a href="<?php echo admin_url('admin.php?page=pancake-settings'); ?>" class="button">Chọn kho</a></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card" style="background: #fff; padding: 20px; margin-top: 20px; border: 1px solid #ccd0d4; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h2 style="margin-top: 0;">🚀 Thao tác nhanh</h2>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="<?php echo admin_url('admin.php?page=pancake-settings'); ?>" class="button button-secondary">
                        ⚙️ Cài đặt Pancake
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=pancake-customers'); ?>" class="button button-secondary">
                        👥 Xem khách hàng
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=pancake-product-sync'); ?>" class="button button-secondary">
                        🔄 Đồng bộ sản phẩm
                    </a>
                    <?php if ($is_connected): ?>
                    <a href="<?php echo admin_url('admin.php?page=pancake-settings'); ?>" class="button button-secondary">
                        🔍 Kiểm tra kết nối
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!$is_connected): ?>
            <!-- Setup Guide -->
            <div class="card" style="background: #fef3c7; padding: 20px; margin-top: 20px; border: 1px solid #fbbf24; border-radius: 8px;">
                <h2 style="margin-top: 0; color: #92400e;">⚡ Hướng dẫn cài đặt</h2>
                <ol style="margin-left: 20px; color: #92400e;">
                    <li>Vào <strong>Cài đặt Pancake</strong></li>
                    <li>Nhập <strong>API Key</strong> và <strong>Shop ID</strong></li>
                    <li>Chọn <strong>Kho hàng</strong></li>
                    <li>Click <strong>Kiểm tra kết nối</strong></li>
                    <li>Sau khi kết nối thành công → <strong>Đồng bộ sản phẩm</strong></li>
                </ol>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
        .pancake-dashboard .card h2 {
            font-size: 16px;
            font-weight: 600;
        }
        .pancake-dashboard code {
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
        }
    </style>
    <?php
}
add_action('admin_menu', 'register_pancake_menu_page');

// 3. Đăng ký Settings cho Pancake
function caremil_register_pancake_settings() {
    register_setting(
        'caremil_pancake_settings_group',
        'caremil_pancake_api_key',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );
    
    register_setting(
        'caremil_pancake_settings_group',
        'caremil_pancake_shop_id',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );
    
    register_setting(
        'caremil_pancake_settings_group',
        'caremil_pancake_base_url',
        array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default' => 'https://pos.pages.fm/api/v1'
        )
    );
    
    register_setting(
        'caremil_pancake_settings_group',
        'caremil_pancake_warehouse_id',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );

}
add_action('admin_init', 'caremil_register_pancake_settings');

// 4. Render trang Settings
function render_pancake_settings_page() {
    // Kiểm tra quyền
    if (!current_user_can('manage_options')) {
        wp_die(__('Bạn không có quyền truy cập trang này.'));
    }
    
    // Xử lý test connection (AJAX)
    if (isset($_POST['test_connection']) && check_admin_referer('caremil_pancake_settings_nonce')) {
        // Lưu tạm để test
        $temp_api_key = sanitize_text_field($_POST['caremil_pancake_api_key']);
        $temp_shop_id = sanitize_text_field($_POST['caremil_pancake_shop_id']);
        $temp_base_url = esc_url_raw($_POST['caremil_pancake_base_url']);
        
        // Test với giá trị tạm
        $old_api_key = get_option('caremil_pancake_api_key', '');
        $old_shop_id = get_option('caremil_pancake_shop_id', '');
        $old_base_url = get_option('caremil_pancake_base_url', '');
        
        update_option('caremil_pancake_api_key', $temp_api_key);
        update_option('caremil_pancake_shop_id', $temp_shop_id);
        update_option('caremil_pancake_base_url', $temp_base_url);
        
        // Test connection
        $is_connected = false;
        if (function_exists('caremil_check_pancake_connection')) {
            $is_connected = caremil_check_pancake_connection(true); // Force check
        }
        
        // Khôi phục giá trị cũ nếu test thất bại
        if (!$is_connected) {
            update_option('caremil_pancake_api_key', $old_api_key);
            update_option('caremil_pancake_shop_id', $old_shop_id);
            update_option('caremil_pancake_base_url', $old_base_url);
        }
        
        if ($is_connected) {
            echo '<div class="notice notice-success is-dismissible"><p><strong>✓ Kết nối thành công!</strong> Cài đặt đã được lưu và kết nối với Pancake POS hoạt động bình thường.</p></div>';
        } else {
            $debug_info = get_option('caremil_pancake_debug_info', array());
            $error_msg = 'Vui lòng kiểm tra lại API Key, Shop ID và Base URL.';
            if (!empty($debug_info['error'])) {
                $error_msg .= '<br><strong>Chi tiết lỗi:</strong> ' . esc_html($debug_info['error']);
            }
            if (!empty($debug_info['missing'])) {
                $error_msg .= '<br><strong>Thiếu:</strong> ' . esc_html(implode(', ', $debug_info['missing']));
            }
            echo '<div class="notice notice-error is-dismissible"><p><strong>✗ Kết nối thất bại!</strong><br>' . $error_msg . '<br>Cài đặt chưa được lưu. Xem thông tin debug bên dưới để biết thêm chi tiết.</p></div>';
        }
    }
    
    // Xử lý lưu settings
    if (isset($_POST['submit']) && check_admin_referer('caremil_pancake_settings_nonce')) {
        update_option('caremil_pancake_api_key', sanitize_text_field($_POST['caremil_pancake_api_key']));
        update_option('caremil_pancake_shop_id', sanitize_text_field($_POST['caremil_pancake_shop_id']));
        update_option('caremil_pancake_base_url', esc_url_raw($_POST['caremil_pancake_base_url']));
        update_option('caremil_pancake_warehouse_id', sanitize_text_field($_POST['caremil_pancake_warehouse_id']));
        
        // Clear cache connection status sau khi lưu
        delete_transient('caremil_pancake_connection_status');
        
        echo '<div class="notice notice-success is-dismissible"><p>Cài đặt đã được lưu thành công!</p></div>';
    }
    
    // Lấy giá trị hiện tại
    $api_key = get_option('caremil_pancake_api_key', '');
    $shop_id = get_option('caremil_pancake_shop_id', '');
    $base_url = get_option('caremil_pancake_base_url', 'https://pos.pages.fm/api/v1');
    $warehouse_id = get_option('caremil_pancake_warehouse_id', '');

    
    ?>
    <div class="wrap">
        <h1>Cài đặt Pancake POS</h1>
        <p class="description">Cấu hình kết nối với hệ thống Pancake POS. Thông tin này được lưu trữ an toàn trong database.</p>
        
        <form method="post" action="">
            <?php wp_nonce_field('caremil_pancake_settings_nonce'); ?>
            
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="caremil_pancake_api_key">API Key</label>
                        </th>
                        <td>
                            <input 
                                type="text" 
                                id="caremil_pancake_api_key" 
                                name="caremil_pancake_api_key" 
                                value="<?php echo esc_attr($api_key); ?>" 
                                class="regular-text"
                                placeholder="Nhập API Key từ Pancake POS"
                            />
                            <p class="description">API Key để xác thực với Pancake POS API. Lấy từ tài khoản Pancake của bạn.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="caremil_pancake_shop_id">Shop ID</label>
                        </th>
                        <td>
                            <input 
                                type="text" 
                                id="caremil_pancake_shop_id" 
                                name="caremil_pancake_shop_id" 
                                value="<?php echo esc_attr($shop_id); ?>" 
                                class="regular-text"
                                placeholder="Nhập Shop ID"
                            />
                            <p class="description">ID của cửa hàng trên hệ thống Pancake POS.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="caremil_pancake_base_url">API Base URL</label>
                        </th>
                        <td>
                            <input 
                                type="url" 
                                id="caremil_pancake_base_url" 
                                name="caremil_pancake_base_url" 
                                value="<?php echo esc_attr($base_url); ?>" 
                                class="regular-text"
                                placeholder="https://pos.pages.fm/api/v1"
                            />
                            <p class="description">URL cơ sở của Pancake POS API. Mặc định: <code>https://pos.pages.fm/api/v1</code></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="caremil_pancake_warehouse_id">Kho hàng (Warehouse)</label>
                        </th>
                        <td>
                            <select 
                                id="caremil_pancake_warehouse_id" 
                                name="caremil_pancake_warehouse_id" 
                                class="regular-text"
                            >
                                <option value="">-- Chọn kho hàng --</option>
                                <?php
                                // Lấy danh sách kho từ Pancake nếu đã kết nối
                                if (function_exists('caremil_check_pancake_connection') && caremil_check_pancake_connection()) {
                                    $warehouses = caremil_get_pancake_warehouses();
                                    if (!empty($warehouses)) {
                                        foreach ($warehouses as $wh) {
                                            $selected = ($warehouse_id === $wh['id']) ? 'selected' : '';
                                            echo '<option value="' . esc_attr($wh['id']) . '" ' . $selected . '>' . esc_html($wh['name']) . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                            <button type="button" id="reload_warehouses" class="button button-secondary" style="margin-left: 10px;">
                                <span class="dashicons dashicons-update" style="margin-top: 3px;"></span> Tải lại danh sách
                            </button>
                            <p class="description">Chọn kho hàng mặc định để lên đơn. Danh sách sẽ tự động tải khi kết nối thành công.</p>
                            <div id="warehouse_loading" style="display: none; margin-top: 10px;">
                                <span class="spinner is-active" style="float: none;"></span> Đang tải danh sách kho...
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <p class="submit">
                <input type="submit" name="test_connection" id="test_connection" class="button button-secondary" value="Kiểm Tra Kết Nối" style="margin-right: 10px;">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Lưu Cài Đặt">
            </p>
        </form>
        
        <?php
        // Hiển thị trạng thái kết nối hiện tại
        if (function_exists('caremil_check_pancake_connection')) {
            $current_status = caremil_check_pancake_connection();
            $status_class = $current_status ? 'notice-success' : 'notice-error';
            $status_text = $current_status ? '✓ Đang kết nối' : '✗ Không kết nối được';
            $status_message = $current_status 
                ? 'Hệ thống đang kết nối với Pancake POS thành công.' 
                : 'Hệ thống không thể kết nối với Pancake POS. Vui lòng kiểm tra cài đặt.';
            ?>
            <div class="notice <?php echo $status_class; ?> is-dismissible" style="margin-top: 20px;">
                <p><strong><?php echo $status_text; ?>:</strong> <?php echo $status_message; ?></p>
            </div>
            <?php
            
            // Hiển thị thông tin debug nếu không kết nối được
            if (!$current_status) {
                $debug_info = get_option('caremil_pancake_debug_info', array());
                if (!empty($debug_info)) {
                    ?>
                    <div class="notice notice-info" style="margin-top: 20px;">
                        <h3 style="margin-top: 0;">Thông tin Debug (Lần kiểm tra cuối: <?php echo isset($debug_info['timestamp']) ? esc_html($debug_info['timestamp']) : 'N/A'; ?>)</h3>
                        <table class="widefat" style="margin-top: 10px;">
                            <tbody>
                                <tr>
                                    <th style="width: 200px;">API Key</th>
                                    <td><?php echo !empty($debug_info['api_key_set']) ? '<span style="color: green;">✓ Đã cấu hình</span>' : '<span style="color: red;">✗ Chưa cấu hình</span>'; ?></td>
                                </tr>
                                <tr>
                                    <th>Shop ID</th>
                                    <td><?php echo !empty($debug_info['shop_id_set']) ? '<span style="color: green;">✓ Đã cấu hình</span> (' . esc_html($debug_info['shop_id'] ?? 'N/A') . ')' : '<span style="color: red;">✗ Chưa cấu hình</span>'; ?></td>
                                </tr>
                                <tr>
                                    <th>Base URL</th>
                                    <td><?php echo !empty($debug_info['base_url_set']) ? '<span style="color: green;">✓ Đã cấu hình</span> (' . esc_html($debug_info['base_url'] ?? 'N/A') . ')' : '<span style="color: red;">✗ Chưa cấu hình</span>'; ?></td>
                                </tr>
                                <?php if (!empty($debug_info['missing'])): ?>
                                <tr>
                                    <th>Thiếu cấu hình</th>
                                    <td><span style="color: red;"><?php echo esc_html(implode(', ', $debug_info['missing'])); ?></span></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($debug_info['error'])): ?>
                                <tr>
                                    <th>Lỗi</th>
                                    <td><span style="color: red;"><?php echo esc_html($debug_info['error']); ?></span></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($debug_info['error_code'])): ?>
                                <tr>
                                    <th>Mã lỗi</th>
                                    <td><code><?php echo esc_html($debug_info['error_code']); ?></code></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($debug_info['http_status'])): ?>
                                <tr>
                                    <th>HTTP Status</th>
                                    <td><code><?php echo esc_html($debug_info['http_status']); ?></code></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($debug_info['test_url'])): ?>
                                <tr>
                                    <th>Test URL</th>
                                    <td><code style="word-break: break-all;"><?php echo esc_html($debug_info['test_url']); ?></code></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($debug_info['response_body'])): ?>
                                <tr>
                                    <th>Response Body</th>
                                    <td><pre style="max-height: 200px; overflow: auto; background: #f5f5f5; padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($debug_info['response_body']); ?></pre></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <p style="margin-top: 10px;">
                            <button type="button" class="button button-secondary" onclick="location.reload();">Làm mới trang</button>
                            <button type="button" class="button button-secondary" onclick="document.getElementById('test_connection').click();">Kiểm tra lại kết nối</button>
                        </p>
                    </div>
                    <?php
                }
            }
        }
        ?>
        
        <hr>
        
        <div class="card" style="max-width: 800px;">
            <h2>Hướng dẫn</h2>
            <ol>
                <li><strong>API Key:</strong> Đăng nhập vào tài khoản Pancake POS của bạn, vào phần Settings/API để lấy API Key.</li>
                <li><strong>Shop ID:</strong> ID cửa hàng của bạn trên Pancake POS. Thường hiển thị trong URL hoặc Settings.</li>
                <li><strong>API Base URL:</strong> URL cơ sở của API. Nếu bạn dùng Pancake Pages, URL mặc định là <code>https://pos.pages.fm/api/v1</code></li>
            </ol>
            <p><strong>Lưu ý:</strong> Sau khi thay đổi cài đặt, hãy kiểm tra kết nối bằng cách thử đăng nhập hoặc xem danh sách khách hàng.</p>
        </div>
    </div>
    
    <style>
        .form-table th {
            width: 200px;
        }
        .card {
            background: #fff;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
            padding: 20px;
            margin-top: 20px;
        }
        .card h2 {
            margin-top: 0;
        }
        .card ol {
            margin-left: 20px;
        }
        .card code {
            background: #f0f0f1;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        $('#reload_warehouses').on('click', function() {
            var $button = $(this);
            var $select = $('#caremil_pancake_warehouse_id');
            var $loading = $('#warehouse_loading');
            var currentValue = $select.val();
            
            // Disable button và hiển thị loading
            $button.prop('disabled', true);
            $loading.show();
            
            // Gọi AJAX để lấy danh sách kho mới
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'caremil_get_warehouses',
                    nonce: '<?php echo wp_create_nonce('caremil_get_warehouses_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        // Xóa tất cả options trừ option đầu tiên
                        $select.find('option:not(:first)').remove();
                        
                        // Thêm warehouses mới
                        $.each(response.data, function(index, warehouse) {
                            var selected = (warehouse.id === currentValue) ? 'selected' : '';
                            $select.append('<option value="' + warehouse.id + '" ' + selected + '>' + warehouse.name + '</option>');
                        });
                        
                        alert('Đã tải lại danh sách kho thành công!');
                    } else {
                        alert('Không thể tải danh sách kho. Vui lòng kiểm tra kết nối Pancake.');
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi tải danh sách kho.');
                },
                complete: function() {
                    $button.prop('disabled', false);
                    $loading.hide();
                }
            });
        });
    });
    </script>
    <?php
}


// 3. Hiển thị danh sách khách hàng trong Admin
function render_pancake_customers_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pancake_customers';
    
    // Xử lý xóa nếu có
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $wpdb->delete($table_name, ['id' => intval($_GET['id'])]);
        echo '<div class="notice notice-success is-dismissible"><p>Đã xóa khách hàng.</p></div>';
    }

    // Lấy dữ liệu
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Danh Sách Khách Hàng (Pancake POS)</h1>
        <hr class="wp-header-end">
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th width="50">ID</th>
                    <th>Họ Tên</th>
                    <th>Số Điện Thoại</th>
                    <th>Email</th>
                    <th>Pancake ID</th>
                    <th>Ngày Tạo</th>
                    <th width="100">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($results)): ?>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo $row->id; ?></td>
                            <td><strong><?php echo esc_html($row->name); ?></strong></td>
                            <td><?php echo esc_html($row->phone); ?></td>
                            <td><?php echo esc_html($row->email); ?></td>
                            <td><?php echo esc_html($row->pancake_id); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row->created_at)); ?></td>
                            <td>
                                <a href="?page=pancake-customers&action=delete&id=<?php echo $row->id; ?>" 
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa?')" 
                                   style="color:red;">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7">Chưa có khách hàng nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * --------------------------------------------------------------------------
 * MODULE: SEPAY DATABASE MIGRATION
 * Tự động tạo bảng giao dịch SePay khi theme được load.
 * --------------------------------------------------------------------------
 */
add_action( 'init', 'caremil_sepay_auto_create_table' );

function caremil_sepay_auto_create_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'sepay_transactions';

    // Nếu bảng đã tồn tại thì bỏ qua để tối ưu.
    $existing = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );
    if ( $existing === $table_name ) {
        return;
    }

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        gateway varchar(100) NOT NULL,
        transaction_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        account_number varchar(100) DEFAULT '' NOT NULL,
        sub_account varchar(250) DEFAULT '' NOT NULL,
        amount_in decimal(20,2) DEFAULT '0.00' NOT NULL,
        amount_out decimal(20,2) DEFAULT '0.00' NOT NULL,
        accumulated decimal(20,2) DEFAULT '0.00' NOT NULL,
        code varchar(250) DEFAULT '' NOT NULL,
        transaction_content text DEFAULT '' NOT NULL,
        reference_number varchar(255) DEFAULT '' NOT NULL,
        body text DEFAULT '' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );

    // Ghi log để tiện debug khi deploy.
    error_log( 'SePay table ensured: ' . $table_name );
}

/**
 * --------------------------------------------------------------------------
 * MODULE: SEPAY WEBHOOK ENDPOINT
 * URL: /wp-json/sepay/v1/webhook
 * --------------------------------------------------------------------------
 */
add_action(
    'rest_api_init',
    function () {
        register_rest_route(
            'sepay/v1',
            '/webhook',
            array(
                'methods'             => 'POST',
                'callback'            => 'caremil_handle_sepay_webhook',
                'permission_callback' => '__return_true',
            )
        );
    }
);

function caremil_handle_sepay_webhook( WP_REST_Request $request ) {
    global $wpdb;

    // Bảo mật: yêu cầu API Key trong header.
    $expected_api_key = caremil_get_sepay_api_key();
    if ( empty( $expected_api_key ) ) {
        return new WP_Error( 'missing_key', 'SePay API Key is not configured.', array( 'status' => 500 ) );
    }

    $provided_key = $request->get_header( 'x-api-key' );
    if ( empty( $provided_key ) ) {
        $provided_key = $request->get_header( 'x-sepay-api-key' );
    }
    // SePay UI "API Key" thường gửi header Authorization: Apikey <KEY>
    if ( empty( $provided_key ) ) {
        $auth_header = $request->get_header( 'authorization' );
        if ( $auth_header && preg_match( '/Apikey\\s+(.*)/i', $auth_header, $m ) ) {
            $provided_key = trim( $m[1] );
        }
    }

    if ( $expected_api_key !== $provided_key ) {
        return new WP_Error( 'unauthorized', 'Invalid API Key.', array( 'status' => 401 ) );
    }

    $params = $request->get_json_params();
    if ( empty( $params ) ) {
        return new WP_Error( 'no_data', 'No JSON data received', array( 'status' => 400 ) );
    }

    $data = array(
        'gateway'             => isset( $params['gateway'] ) ? sanitize_text_field( $params['gateway'] ) : '',
        'transaction_date'    => isset( $params['transactionDate'] ) ? sanitize_text_field( $params['transactionDate'] ) : '',
        'account_number'      => isset( $params['accountNumber'] ) ? sanitize_text_field( $params['accountNumber'] ) : '',
        'sub_account'         => isset( $params['subAccount'] ) ? sanitize_text_field( $params['subAccount'] ) : '',
        'code'                => isset( $params['code'] ) ? sanitize_text_field( $params['code'] ) : '',
        'transaction_content' => isset( $params['content'] ) ? sanitize_textarea_field( $params['content'] ) : '',
        'reference_number'    => isset( $params['referenceCode'] ) ? sanitize_text_field( $params['referenceCode'] ) : '',
        'body'                => isset( $params['description'] ) ? sanitize_textarea_field( $params['description'] ) : '',
        'accumulated'         => isset( $params['accumulated'] ) ? floatval( $params['accumulated'] ) : 0,
        'amount_in'           => 0,
        'amount_out'          => 0,
    );

    $transfer_amount = isset( $params['transferAmount'] ) ? floatval( $params['transferAmount'] ) : 0;
    $transfer_type   = isset( $params['transferType'] ) ? sanitize_text_field( $params['transferType'] ) : '';

    if ( 'in' === $transfer_type ) {
        $data['amount_in'] = $transfer_amount;
    } elseif ( 'out' === $transfer_type ) {
        $data['amount_out'] = $transfer_amount;
    }

    $table_name = $wpdb->prefix . 'sepay_transactions';
    $inserted   = $wpdb->insert( $table_name, $data );

    if ( $inserted ) {
        $transaction_id = $wpdb->insert_id;

        // Hook mở rộng để xử lý nghiệp vụ riêng.
        do_action( 'sepay_transaction_received', $transaction_id, $data );

        // Đánh dấu trạng thái thanh toán cho mã đơn (nếu có).
        if ( ! empty( $data['code'] ) ) {
            caremil_mark_order_paid( $data['code'], $data );
        }

        return new WP_REST_Response(
            array(
                'success' => true,
                'id'      => $transaction_id,
            ),
            200
        );
    }

    return new WP_Error( 'db_error', 'Cannot insert to database: ' . $wpdb->last_error, array( 'status' => 500 ) );
}

/**
 * Lấy SePay API Key: ưu tiên ENV -> constant -> option.
 */
function caremil_get_sepay_api_key() {
    $env_key = getenv( 'SEPAY_API_KEY' );
    if ( ! empty( $env_key ) ) {
        return $env_key;
    }

    if ( defined( 'SEPAY_API_KEY' ) && SEPAY_API_KEY ) {
        return SEPAY_API_KEY;
    }

    $option_key = get_option( 'caremil_sepay_api_key', '' );
    if ( ! empty( $option_key ) ) {
        return $option_key;
    }

    // Fallback theo yêu cầu: dùng key đặt sẵn trong theme.
    if ( defined( 'CAREMIL_SEPAY_DEFAULT_KEY' ) && CAREMIL_SEPAY_DEFAULT_KEY ) {
        return CAREMIL_SEPAY_DEFAULT_KEY;
    }

    return '';
}

/**
 * Lưu trạng thái đơn đã thanh toán (dựa trên mã code trong nội dung chuyển khoản).
 */
function caremil_mark_order_paid( $order_code, $transaction ) {
    $normalized = sanitize_text_field( $order_code );
    if ( empty( $normalized ) ) {
        return;
    }

    // Lưu trạng thái đơn theo option; có thể thay bằng bảng riêng nếu cần.
    update_option(
        'caremil_payment_status_' . $normalized,
        array(
            'status'       => 'paid',
            'updated_at'   => current_time( 'mysql' ),
            'transaction'  => $transaction,
        ),
        false
    );

    /**
     * Hook cho dev: bắt sự kiện đơn được đánh dấu paid.
     * Tham số: $normalized (mã đơn), $transaction (array từ webhook).
     */
    do_action( 'caremil_order_paid', $normalized, $transaction );
}

/**
 * Webhook URL ngắn: /hooks/sepay-payment hoặc /hooks/sepay-payment/{token}
 * Dùng khi không cấu hình được header trên SePay. Bảo vệ bằng token riêng.
 */
add_action(
    'init',
    function () {
        add_rewrite_rule(
            '^hooks/sepay-payment/?([^/]*)/?$',
            'index.php?caremil_sepay_hook=1&token=$matches[1]',
            'top'
        );
        add_rewrite_rule(
            '^hooks/sepay-payment/?$',
            'index.php?caremil_sepay_hook=1',
            'top'
        );
    }
);

add_filter(
    'query_vars',
    function ( $vars ) {
        $vars[] = 'caremil_sepay_hook';
        $vars[] = 'token';
        return $vars;
    }
);

add_action(
    'template_redirect',
    function () {
        if ( intval( get_query_var( 'caremil_sepay_hook', 0 ) ) !== 1 ) {
            return;
        }

        if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
            status_header( 405 );
            wp_send_json_error( array( 'message' => 'Method Not Allowed' ), 405 );
        }

        $token          = get_query_var( 'token', '' );
        $expected_token = caremil_get_sepay_webhook_token();
        if ( $expected_token && $token !== $expected_token ) {
            status_header( 401 );
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 401 );
        }

        $body = file_get_contents( 'php://input' );
        $json = json_decode( $body, true );

        $request = new WP_REST_Request( 'POST', '/sepay/v1/webhook' );
        $request->set_json_params( $json );
        $request->set_header( 'x-api-key', caremil_get_sepay_api_key() );

        $response = caremil_handle_sepay_webhook( $request );

        if ( is_wp_error( $response ) ) {
            $status = $response->get_error_data()['status'] ?? 500;
            status_header( $status );
            wp_send_json_error( array( 'message' => $response->get_error_message() ), $status );
        }

        if ( $response instanceof WP_REST_Response ) {
            $status = $response->get_status();
            $data   = $response->get_data();
            status_header( $status );
            wp_send_json_success( $data, $status );
        }

        wp_send_json( $response );
    }
);

/**
 * Lấy token bảo vệ URL ngắn.
 */
function caremil_get_sepay_webhook_token() {
    $env_token = getenv( 'SEPAY_WEBHOOK_TOKEN' );
    if ( ! empty( $env_token ) ) {
        return $env_token;
    }

    if ( defined( 'SEPAY_WEBHOOK_TOKEN' ) && SEPAY_WEBHOOK_TOKEN ) {
        return SEPAY_WEBHOOK_TOKEN;
    }

    $option_token = get_option( 'caremil_sepay_webhook_token', '' );
    if ( ! empty( $option_token ) ) {
        return $option_token;
    }

    return '';
}

/**
 * REST: Kiểm tra trạng thái thanh toán theo mã code (public để front-end poll).
 * GET /wp-json/caremil/v1/payment-status?code=DH12345
 */
add_action(
    'rest_api_init',
    function () {
        register_rest_route(
            'caremil/v1',
            '/payment-status',
            array(
                'methods'             => WP_REST_Server::READABLE,
                'permission_callback' => '__return_true',
                'callback'            => 'caremil_rest_get_payment_status',
                'args'                => array(
                    'code' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
            )
        );
    }
);

function caremil_rest_get_payment_status( WP_REST_Request $request ) {
    $code = $request->get_param( 'code' );
    if ( empty( $code ) ) {
        return new WP_Error( 'missing_code', 'Thiếu mã đơn (code).', array( 'status' => 400 ) );
    }

    $data = get_option( 'caremil_payment_status_' . $code, null );
    if ( empty( $data ) ) {
        return rest_ensure_response(
            array(
                'paid'  => false,
                'code'  => $code,
                'found' => false,
            )
        );
    }

    return rest_ensure_response(
        array(
            'paid'        => isset( $data['status'] ) && 'paid' === $data['status'],
            'code'        => $code,
            'updated_at'  => isset( $data['updated_at'] ) ? $data['updated_at'] : null,
            'transaction' => isset( $data['transaction'] ) ? $data['transaction'] : null,
        )
    );
}
// Force product template loading
require_once get_template_directory() . '/force-product-template.php';

// Include product orders system
require_once get_template_directory() . '/product-orders.php';

/**
 * Shipping Carrier Helper Functions
 * Map partner_id to carrier information from Pancake POS API
 */

/**
 * Get shipping carrier name by partner_id
 * 
 * @param int $partner_id Partner ID from Pancake API
 * @return string Carrier name in Vietnamese
 */
function caremil_get_carrier_name( $partner_id ) {
    $carriers = array(
        0  => 'Snappy',
        1  => 'Giao hàng tiết kiệm',
        2  => 'EMS',
        4  => '247 Express',
        5  => 'Giao hàng nhanh',
        7  => 'Viettel Post (VTP)',
        8  => 'SPL',
        9  => 'DHL',
        10 => 'J&T Philippines',
        11 => 'Ahamove',
        12 => 'LBC',
        13 => 'Lazada Express',
        15 => 'J&T Express',
        16 => 'Best Inc',
        17 => 'VN Post',
        19 => 'Ninja Van',
        32 => 'SuperShip',
        33 => 'ZTO Express',
        36 => 'NTX',
        37 => 'Grab Express',
        38 => 'Vạn Phúc',
        39 => 'Hola Ship',
        40 => 'LWE Express',
        41 => 'Flash Express',
    );
    
    return isset( $carriers[ (int) $partner_id ] ) ? $carriers[ (int) $partner_id ] : 'Đơn vị vận chuyển';
}

/**
 * Get carrier short code for CSS/icon purposes
 * 
 * @param int $partner_id Partner ID from Pancake API
 * @return string Carrier short code
 */
function caremil_get_carrier_code( $partner_id ) {
    $codes = array(
        0  => 'snappy',
        1  => 'ghtk',
        2  => 'ems',
        4  => '247express',
        5  => 'ghn',
        7  => 'vtp',
        8  => 'spl',
        9  => 'dhl',
        10 => 'jnt-ph',
        11 => 'ahamove',
        12 => 'lbc',
        13 => 'lazada',
        15 => 'jnt',
        16 => 'best',
        17 => 'vnpost',
        19 => 'ninjavan',
        32 => 'supership',
        33 => 'zto',
        36 => 'ntx',
        37 => 'grab',
        38 => 'vanphuc',
        39 => 'holaship',
        40 => 'lwe',
        41 => 'flash',
    );
    
    return isset( $codes[ (int) $partner_id ] ) ? $codes[ (int) $partner_id ] : 'default';
}

/**
 * Get carrier tracking URL
 * 
 * @param int    $partner_id   Partner ID from Pancake API
 * @param string $tracking_code Tracking/extend code
 * @return string|null Tracking URL or null if not available
 */
function caremil_get_carrier_tracking_url( $partner_id, $tracking_code ) {
    if ( empty( $tracking_code ) ) {
        return null;
    }
    
    $tracking_urls = array(
        1  => 'https://giaohangtietkiem.vn/khach-hang/tra-cuu-don-hang?code=' . urlencode( $tracking_code ),
        5  => 'https://donhang.ghn.vn/?order_code=' . urlencode( $tracking_code ),
        9  => 'https://www.dhl.com/vn-en/home/tracking/tracking-express.html?submit=1&tracking-id=' . urlencode( $tracking_code ),
        15 => 'https://www.jtexpress.vn/tracking?billcode=' . urlencode( $tracking_code ),
        17 => 'https://www.vnpost.vn/vi-vn/dinh-vi/buu-pham?key=' . urlencode( $tracking_code ),
        19 => 'https://www.ninjavan.co/vi-vn/tracking?id=' . urlencode( $tracking_code ),
    );
    
    return isset( $tracking_urls[ (int) $partner_id ] ) ? $tracking_urls[ (int) $partner_id ] : null;
}

/**
 * Get carrier icon/emoji
 * 
 * @param int $partner_id Partner ID from Pancake API
 * @return string Icon emoji or default truck icon
 */
function caremil_get_carrier_icon( $partner_id ) {
    $icons = array(
        0  => '🚚', // Snappy
        1  => '📦', // GHTK
        2  => '✉️', // EMS
        4  => '⚡', // 247 Express
        5  => '🚀', // GHN
        7  => '📮', // VTP
        8  => '📦', // SPL
        9  => '✈️', // DHL
        10 => '📦', // J&T PH
        11 => '🛵', // Ahamove
        12 => '📦', // LBC
        13 => '🛍️', // Lazada
        15 => '📦', // J&T
        16 => '📦', // Best Inc
        17 => '📮', // VN Post
        19 => '🥷', // Ninja Van
        32 => '⚡', // SuperShip
        33 => '📦', // ZTO
        36 => '📦', // NTX
        37 => '🚗', // Grab
        38 => '📦', // Vạn Phúc
        39 => '📦', // Hola Ship
        40 => '📦', // LWE
        41 => '⚡', // Flash
    );
    
    return isset( $icons[ (int) $partner_id ] ) ? $icons[ (int) $partner_id ] : '🚚';
}
