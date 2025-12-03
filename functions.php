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

