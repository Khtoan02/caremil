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
 * Enqueue scripts and styles
 */
function caremil_scripts() {
    // Enqueue Tailwind CSS (built from src/input.css)
    $tailwind_css = get_template_directory() . '/dist/style.css';
    if ( file_exists( $tailwind_css ) ) {
        wp_enqueue_style( 
            'caremil-tailwind', 
            get_template_directory_uri() . '/dist/style.css', 
            array(), 
            filemtime( $tailwind_css ) 
        );
    } else {
        // Fallback to style.css if Tailwind hasn't been built yet
        wp_enqueue_style( 'caremil-style', get_stylesheet_uri(), array(), '1.0.0' );
    }

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
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer 1', 'caremil' ),
        'id'            => 'footer-1',
        'description'   => __( 'Widgets hiển thị ở footer cột 1', 'caremil' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer 2', 'caremil' ),
        'id'            => 'footer-2',
        'description'   => __( 'Widgets hiển thị ở footer cột 2', 'caremil' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
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

