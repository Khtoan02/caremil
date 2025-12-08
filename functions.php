<?php
/**
 * Caremil Theme Functions
 *
 * @package Caremil
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

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
 * Admin UI: trang quản lý nhanh & form thêm/sửa sản phẩm (tham khảo product-list/admin-product).
 */
function caremil_register_product_admin_pages() {
    add_submenu_page(
        'edit.php?post_type=caremil_product',
        __( 'Quản lý sản phẩm CareMIL', 'caremil' ),
        __( 'Quản lý sản phẩm (UI)', 'caremil' ),
        'edit_posts',
        'caremil-products-app',
        'caremil_render_product_admin_page'
    );

    add_submenu_page(
        'edit.php?post_type=caremil_product',
        __( 'Thêm sản phẩm CareMIL', 'caremil' ),
        __( 'Thêm sản phẩm (UI)', 'caremil' ),
        'edit_posts',
        'caremil-product-designer',
        'caremil_render_product_single_page'
    );

    add_submenu_page(
        'edit.php?post_type=caremil_product',
        __( 'Nhóm sản phẩm CareMIL', 'caremil' ),
        __( 'Nhóm sản phẩm (UI)', 'caremil' ),
        'edit_posts',
        'caremil-product-groups',
        'caremil_render_product_group_page'
    );
}
add_action( 'admin_menu', 'caremil_register_product_admin_pages' );

/**
 * Ẩn các submenu mặc định của CPT (Add new, taxonomy) để tránh trùng với UI custom.
 */
function caremil_hide_default_product_submenus() {
    remove_submenu_page( 'edit.php?post_type=caremil_product', 'post-new.php?post_type=caremil_product' );
    remove_submenu_page( 'edit.php?post_type=caremil_product', 'edit-tags.php?taxonomy=caremil_product_cat&post_type=caremil_product' );

    // Ẩn menu CPT mặc định khỏi thanh menu (để tránh trùng). Sau đó thêm menu riêng trỏ thẳng UI custom.
    remove_menu_page( 'edit.php?post_type=caremil_product' );
}
add_action( 'admin_menu', 'caremil_hide_default_product_submenus', 999 );

/**
 * Tạo menu chính riêng cho CareMIL Products, trỏ trực tiếp tới UI custom.
 */
function caremil_register_custom_product_menu() {
    add_menu_page(
        __( 'Sản phẩm CareMIL', 'caremil' ),
        __( 'Sản phẩm CareMIL', 'caremil' ),
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
        __( 'Thêm sản phẩm CareMIL', 'caremil' ),
        __( 'Thêm sản phẩm (UI)', 'caremil' ),
        'edit_posts',
        'caremil-product-designer',
        'caremil_render_product_single_page'
    );

    add_submenu_page(
        'caremil-products-app',
        __( 'Nhóm sản phẩm CareMIL', 'caremil' ),
        __( 'Nhóm sản phẩm (UI)', 'caremil' ),
        'edit_posts',
        'caremil-product-groups',
        'caremil_render_product_group_page'
    );
}
add_action( 'admin_menu', 'caremil_register_custom_product_menu', 20 );

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
 * Custom Post Type: CareMIL Products
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
        'menu_name'          => __( 'Sản phẩm CareMIL', 'caremil' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_rest'       => true,
        'has_archive'        => false,
        'rewrite'            => array( 'slug' => 'san-pham' ),
        'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
        'menu_icon'          => 'dashicons-products',
    );

    register_post_type( 'caremil_product', $args );

    $tax_labels = array(
        'name'          => __( 'Nhóm sản phẩm', 'caremil' ),
        'singular_name' => __( 'Nhóm sản phẩm', 'caremil' ),
        'menu_name'     => __( 'Nhóm sản phẩm', 'caremil' ),
        'add_new_item'  => __( 'Thêm nhóm mới', 'caremil' ),
        'edit_item'     => __( 'Sửa nhóm', 'caremil' ),
    );

    register_taxonomy(
        'caremil_product_cat',
        'caremil_product',
        array(
            'labels'       => $tax_labels,
            'hierarchical' => true,
            'show_ui'      => true,
            'show_in_rest' => true,
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
        __( 'Thông tin sản phẩm CareMIL', 'caremil' ),
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

