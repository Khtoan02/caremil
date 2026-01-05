<?php
/**
 * Custom Product Editor Page
 * Premium UX/UI for product editing
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render custom product editor page
 */
function caremil_render_custom_product_editor() {
    $product_id = isset($_GET['product_id']) ? absint($_GET['product_id']) : 0;
    
    // If product_id is provided, load that product
    $product = null;
    $is_new = true;
    
    if ($product_id > 0) {
        $product = get_post($product_id);
        if (!$product || $product->post_type !== 'product') {
            wp_die('Invalid product ID');
        }
        $is_new = false;
    }
    
    ?>
    <div id="caremil-product-editor-root"></div>
    
    <script>
        window.caremilProductEditor = {
            productId: <?php echo $product_id; ?>,
            isNew: <?php echo $is_new ? 'true' : 'false'; ?>,
            restUrl: '<?php echo esc_url(rest_url('wp/v2/product')); ?>',
            nonce: '<?php echo wp_create_nonce('caremil_product_editor'); ?>',
            ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
            mediaUploadUrl: '<?php echo admin_url('media-upload.php'); ?>',
            productListUrl: '<?php echo admin_url('edit.php?post_type=product'); ?>',
        };
    </script>
    <?php
}

/**
 * Enqueue assets for custom product editor
 */
function caremil_enqueue_product_editor_assets($hook) {
    // Only load on our custom editor page
    if ($hook !== 'product_page_caremil-product-editor') {
        return;
    }
    
    // Enqueue WordPress dependencies
    wp_enqueue_media();
    wp_enqueue_editor();
    wp_enqueue_script('wp-element');
    wp_enqueue_script('wp-components');
    wp_enqueue_script('wp-api-fetch');
    
    // Enqueue TailwindCSS
    wp_enqueue_script(
        'caremil-tailwind',
        'https://cdn.tailwindcss.com?plugins=forms,typography',
        array(),
        '3.4.1',
        false
    );
    
    // Tailwind config
    wp_add_inline_script(
        'caremil-tailwind',
        'tailwind.config = {
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    colors: {
                        primary: "#667eea",
                        secondary: "#10b981"
                    }
                }
            }
        };',
        'before'
    );
    
    // Custom editor JS
    wp_enqueue_script(
        'caremil-product-editor',
        get_template_directory_uri() . '/js/product-editor-app.js',
        array('wp-element', 'wp-components', 'wp-api-fetch'),
        filemtime(get_template_directory() . '/js/product-editor-app.js'),
        true
    );
    
    // Custom editor CSS
    wp_enqueue_style(
        'caremil-product-editor-css',
        get_template_directory_uri() . '/css/product-editor.css',
        array(),
        filemtime(get_template_directory() . '/css/product-editor.css')
    );
}
add_action('admin_enqueue_scripts', 'caremil_enqueue_product_editor_assets');

/**
 * Add custom editor submenu
 */
function caremil_add_product_editor_submenu() {
    add_submenu_page(
        'edit.php?post_type=product',
        'Edit Product',
        null, // Hidden from menu
        'edit_posts',
        'caremil-product-editor',
        'caremil_render_custom_product_editor'
    );
}
add_action('admin_menu', 'caremil_add_product_editor_submenu');

/**
 * AJAX: Save product
 */
function caremil_ajax_save_product() {
    error_log('=== Product Save Started ===');
    
    try {
        check_ajax_referer('caremil_product_editor', 'nonce');
    } catch (Exception $e) {
        error_log('Nonce check failed: ' . $e->getMessage());
        wp_send_json_error(['message' => 'Security check failed']);
    }
    
    if (!current_user_can('edit_posts')) {
        error_log('Permission denied for user');
        wp_send_json_error(['message' => 'Permission denied']);
    }
    
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $content = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';
    $short_desc = isset($_POST['short_desc']) ? sanitize_textarea_field($_POST['short_desc']) : '';
    $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'draft';
    $featured_image = isset($_POST['featured_image']) ? absint($_POST['featured_image']) : 0;
    
    // Parse gallery - could be JSON string or array
    $gallery = array();
    if (isset($_POST['gallery'])) {
        if (is_string($_POST['gallery'])) {
            $gallery = json_decode($_POST['gallery'], true);
            if (!is_array($gallery)) {
                $gallery = array();
                error_log('Failed to decode gallery JSON or it was not an array.');
            } else {
                $gallery = array_map('absint', $gallery);
            }
        } elseif (is_array($_POST['gallery'])) {
            $gallery = array_map('absint', $_POST['gallery']);
        }
    }
    
    $rating = isset($_POST['rating']) ? absint($_POST['rating']) : 5;
    $rating_count = isset($_POST['rating_count']) ? absint($_POST['rating_count']) : 0;
    
    error_log("Product save data: ID=$product_id, Title=$title, Status=$status");
    
    // Validate required fields
    if (empty($title)) {
        error_log('Title is empty');
        wp_send_json_error(['message' => 'Product title is required']);
    }
    
    // Prepare post data
    $post_data = array(
        'post_title' => $title,
        'post_content' => $content,
        'post_status' => $status,
        'post_type' => 'product',
    );
    
    // Update or create
    if ($product_id > 0) {
        $post_data['ID'] = $product_id;
        error_log("Updating product #$product_id");
        $result = wp_update_post($post_data, true);
    } else {
        error_log("Creating new product");
        $result = wp_insert_post($post_data, true);
    }
    
    if (is_wp_error($result)) {
        error_log('WP Error: ' . $result->get_error_message());
        wp_send_json_error(['message' => $result->get_error_message()]);
    }
    
    $product_id = is_int($result) ? $result : $product_id;
    error_log("Product saved successfully: #$product_id");
    
    // Update meta
    update_post_meta($product_id, 'caremil_short_desc', $short_desc);
    error_log("Short description updated.");
    update_post_meta($product_id, 'caremil_rating', $rating);
    error_log("Rating updated.");
    update_post_meta($product_id, 'caremil_rating_count', $rating_count);
    error_log("Rating count updated.");
    
    // Update Product Weight
    $product_weight = isset($_POST['product_weight']) ? absint($_POST['product_weight']) : 500;
    update_post_meta($product_id, 'product_weight', $product_weight);
    error_log("Product weight updated: $product_weight");
    
    // Update featured image
    if ($featured_image > 0) {
        set_post_thumbnail($product_id, $featured_image);
        error_log("Featured image set: $featured_image");
    } else {
        delete_post_thumbnail($product_id);
        error_log("Featured image removed.");
    }
    
    // Update gallery
    if (!empty($gallery)) {
        update_post_meta($product_id, 'product_gallery', $gallery);
        error_log("Gallery updated: " . count($gallery) . " images");
    } else {
        delete_post_meta($product_id, 'product_gallery');
        error_log("Gallery cleared.");
    }
    
    error_log('=== Product Save Completed ===');
    
    wp_send_json_success([
        'message' => 'Product saved successfully',
        'product_id' => $product_id,
        'edit_url' => admin_url('admin.php?page=caremil-product-editor&product_id=' . $product_id)
    ]);
}
add_action('wp_ajax_caremil_save_product', 'caremil_ajax_save_product');

/**
 * AJAX: Get product data
 */
function caremil_ajax_get_product() {
    check_ajax_referer('caremil_product_editor', 'nonce');
    
    $product_id = isset($_GET['product_id']) ? absint($_GET['product_id']) : 0;
    
    if ($product_id === 0) {
        wp_send_json_error(['message' => 'Invalid product ID']);
    }
    
    $product = get_post($product_id);
    
    if (!$product || $product->post_type !== 'product') {
        wp_send_json_error(['message' => 'Product not found']);
    }
    
    // Get meta data
    $data = array(
        'id' => $product->ID,
        'title' => $product->post_title,
        'content' => $product->post_content,
        'status' => $product->post_status,
        'short_desc' => get_post_meta($product->ID, 'caremil_short_desc', true),
        'product_weight' => get_post_meta($product->ID, 'product_weight', true) ?: 500, // Default 500g
        'rating' => get_post_meta($product->ID, 'caremil_rating', true) ?: 5,
        'rating_count' => get_post_meta($product->ID, 'caremil_rating_count', true) ?: 0,
        'featured_image' => get_post_thumbnail_id($product->ID),
        'featured_image_url' => get_the_post_thumbnail_url($product->ID, 'medium'),
        'gallery' => get_post_meta($product->ID, 'product_gallery', true) ?: array(),
        // Pancake data
        'pancake_id' => get_post_meta($product->ID, 'pancake_product_id', true),
        'pancake_sku' => get_post_meta($product->ID, 'pancake_sku', true),
        'pancake_barcode' => get_post_meta($product->ID, 'pancake_barcode', true),
        'pancake_price' => get_post_meta($product->ID, 'caremil_price', true),
        'pancake_category' => get_post_meta($product->ID, 'pancake_category_name', true),
        'pancake_synced_at' => get_post_meta($product->ID, 'pancake_synced_at', true),
    );
    
    wp_send_json_success($data);
}
add_action('wp_ajax_caremil_get_product', 'caremil_ajax_get_product');

/**
 * Redirect default edit post to custom editor
 */
function caremil_redirect_to_custom_editor() {
    global $pagenow;
    
    if ($pagenow === 'post.php' && isset($_GET['action']) && $_GET['action'] === 'edit') {
        if (isset($_GET['post'])) {
            $post_id = absint($_GET['post']);
            $post = get_post($post_id);
            
            if ($post && $post->post_type === 'product') {
                $redirect_url = admin_url('admin.php?page=caremil-product-editor&product_id=' . $post_id);
                wp_redirect($redirect_url);
                exit;
            }
        }
    }
    
    // Also redirect post-new.php for products
    if ($pagenow === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'product') {
        $redirect_url = admin_url('admin.php?page=caremil-product-editor');
        wp_redirect($redirect_url);
        exit;
    }
}
add_action('admin_init', 'caremil_redirect_to_custom_editor');
