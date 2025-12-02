<?php
/**
 * The header template file
 *
 * @package Caremil
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class( 'bg-gray-50 text-gray-900' ); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site min-h-screen flex flex-col">
    <header id="masthead" class="site-header bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between py-4 gap-4">
                <div class="site-branding">
                    <?php
                    if ( is_front_page() && is_home() ) :
                        ?>
                        <h1 class="site-title text-2xl md:text-3xl font-bold mb-2">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="text-gray-900 hover:text-blue-600 transition-colors duration-300">
                                <?php bloginfo( 'name' ); ?>
                            </a>
                        </h1>
                        <?php
                    else :
                        ?>
                        <p class="site-title text-2xl md:text-3xl font-bold mb-2">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="text-gray-900 hover:text-blue-600 transition-colors duration-300">
                                <?php bloginfo( 'name' ); ?>
                            </a>
                        </p>
                        <?php
                    endif;

                    $description = get_bloginfo( 'description', 'display' );
                    if ( $description || is_customize_preview() ) :
                        ?>
                        <p class="site-description text-sm text-gray-600"><?php echo $description; ?></p>
                        <?php
                    endif;
                    ?>
                </div>

                <nav id="site-navigation" class="main-navigation">
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'menu_class'     => 'flex flex-wrap gap-6 md:gap-8',
                        'container'      => false,
                        'fallback_cb'    => false,
                        'link_before'    => '<span class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-300">',
                        'link_after'     => '</span>',
                    ) );
                    ?>
                </nav>
            </div>
        </div>
    </header>

