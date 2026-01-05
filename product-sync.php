<?php
/**
 * =============================================================================
 * PRODUCT SYNC FROM PANCAKE POS
 * Đồng bộ sản phẩm từ Pancake về WordPress
 * =============================================================================
 */

/**
 * Lấy danh sách sản phẩm từ Pancake THEO WAREHOUSE
 * Endpoint: /shops/{shop_id}/products/variations
 * Filter theo: manipulation_warehouses
 */
function caremil_get_pancake_products( $force_refresh = false ) {
    $warehouse_id = caremil_get_pancake_warehouse_id();
    $cache_key = 'caremil_pancake_products_' . $warehouse_id;
    $cache_time = 1800; // 30 minutes
    
    if ( ! $force_refresh ) {
        $cached = get_transient( $cache_key );
        if ( false !== $cached ) {
            error_log( 'Pancake Products: Loaded from cache (' . count( $cached ) . ' products for warehouse ' . $warehouse_id . ')' );
            return $cached;
        }
    }
    
    $shop_id = caremil_get_pancake_shop_id();
    
    if ( empty( $shop_id ) ) {
        error_log( 'Pancake Products: Shop ID is empty' );
        return array();
    }
    
    if ( empty( $warehouse_id ) ) {
        error_log( 'Pancake Products: Warehouse ID is empty - please select warehouse in settings' );
        return array();
    }
    
    // Endpoint: /shops/{shop_id}/products/variations
    $path = '/shops/' . $shop_id . '/products/variations';
    $params = array(
        'page' => 1,
        'page_size' => 100, // Max per page
    );
    
    error_log( 'Pancake Products: Fetching from API for warehouse: ' . $warehouse_id );
    $start_time = microtime( true );
    
    $response = caremil_pancake_request( $path, $params );
    
    $elapsed = round( ( microtime( true ) - $start_time ), 2 );
    error_log( 'Pancake Products: API request took ' . $elapsed . 's' );
    
    if ( empty( $response ) ) {
        error_log( 'Pancake Products: Empty response from API' );
        return array();
    }
    
    // Get all products from response
    $all_products = array();
    if ( isset( $response['data'] ) && is_array( $response['data'] ) ) {
        $all_products = $response['data'];
        error_log( 'Pancake Products: Got ' . count( $all_products ) . ' total products from API' );
    } else {
        error_log( 'Pancake Products: Invalid response structure' );
        return array();
    }
    
    // Filter products by warehouse
    $filtered_products = array();
    foreach ( $all_products as $product ) {
        // Check if product belongs to selected warehouse
        if ( isset( $product['product']['manipulation_warehouses'] ) && is_array( $product['product']['manipulation_warehouses'] ) ) {
            if ( in_array( $warehouse_id, $product['product']['manipulation_warehouses'], true ) ) {
                $filtered_products[] = $product;
            }
        }
    }
    
    error_log( 'Pancake Products: Filtered to ' . count( $filtered_products ) . ' products for warehouse ' . $warehouse_id );
    
    // Cache the filtered result
    if ( ! empty( $filtered_products ) ) {
        set_transient( $cache_key, $filtered_products, $cache_time );
        error_log( 'Pancake Products: Cached ' . count( $filtered_products ) . ' products for ' . $cache_time . 's' );
    }
    
    return $filtered_products;
}


/**
 * Đồng bộ một sản phẩm từ Pancake vào WordPress
 * CHỈ ĐỒNG BỘ: ID, Giá, Mã
 * KHÔNG ĐỒNG BỘ: Tên, Mô tả (do website tự custom)
 */
function caremil_sync_product_from_pancake( $pancake_product ) {
    if ( empty( $pancake_product ) || ! is_array( $pancake_product ) ) {
        return false;
    }
    
    // Extract data from Pancake variations structure
    $variation_id = isset( $pancake_product['id'] ) ? sanitize_text_field( $pancake_product['id'] ) : '';
    $sku = isset( $pancake_product['display_id'] ) ? sanitize_text_field( $pancake_product['display_id'] ) : '';
    $barcode = isset( $pancake_product['barcode'] ) ? sanitize_text_field( $pancake_product['barcode'] ) : '';
    
    // Price priority: retail_price > retail_price_after_discount > price_at_counter
    $price = 0;
    if ( isset( $pancake_product['retail_price'] ) && floatval( $pancake_product['retail_price'] ) > 0 ) {
        $price = floatval( $pancake_product['retail_price'] );
    } elseif ( isset( $pancake_product['retail_price_after_discount'] ) && floatval( $pancake_product['retail_price_after_discount'] ) > 0 ) {
        $price = floatval( $pancake_product['retail_price_after_discount'] );
    } elseif ( isset( $pancake_product['price_at_counter'] ) && floatval( $pancake_product['price_at_counter'] ) > 0 ) {
        $price = floatval( $pancake_product['price_at_counter'] );
    }
    
    error_log( "Pancake Sync: Product $sku - Price: $price (retail_price: " . ($pancake_product['retail_price'] ?? 0) . ")" );
    
    // Product info from nested structure
    $product_name = '';
    $category_id = '';
    $category_name = '';
    
    if ( isset( $pancake_product['product'] ) && is_array( $pancake_product['product'] ) ) {
        $product_info = $pancake_product['product'];
        $product_name = isset( $product_info['name'] ) ? sanitize_text_field( $product_info['name'] ) : '';
        
        if ( isset( $product_info['categories'][0] ) ) {
            $category_id = isset( $product_info['categories'][0]['id'] ) ? sanitize_text_field( $product_info['categories'][0]['id'] ) : '';
            $category_name = isset( $product_info['categories'][0]['name'] ) ? sanitize_text_field( $product_info['categories'][0]['name'] ) : '';
        }
    }
    
    // Use variation_id as unique identifier
    if ( empty( $variation_id ) ) {
        error_log( 'Pancake Sync: Missing variation ID for product' );
        return false;
    }
    
    // Use SKU as product code, fallback to barcode
    $product_code = ! empty( $sku ) ? $sku : $barcode;
    
    // Check if product already exists by variation_id
    $existing_posts = get_posts( array(
        'post_type' => 'caremil_product',
        'meta_key' => 'pancake_product_id',
        'meta_value' => $variation_id,
        'posts_per_page' => 1,
        'post_status' => 'any'
    ) );
    
    $post_id = ! empty( $existing_posts ) ? $existing_posts[0]->ID : 0;
    
    // If product doesn't exist, create with minimal data
    if ( $post_id === 0 ) {
        // Tạo mới: Tên tạm từ product name Pancake hoặc SKU, user sẽ tự đổi sau
        $temp_name = ! empty( $product_name ) ? $product_name : ( ! empty( $product_code ) ? "Sản phẩm $product_code" : "Sản phẩm mới" );
        
        $post_data = array(
            'post_title' => $temp_name,
            'post_content' => '', // Không có mô tả, user tự thêm
            'post_status' => 'draft', // Draft để user có thể edit trước khi publish
            'post_type' => 'caremil_product',
        );
        
        $post_id = wp_insert_post( $post_data );
        
        if ( is_wp_error( $post_id ) || $post_id === 0 ) {
            error_log( 'Pancake Sync: Failed to create product: ' . $temp_name );
            return false;
        }
        
        error_log( 'Pancake Sync: Created new product #' . $post_id . ' - ' . $temp_name );
        
        // Set default meta for new products
        update_post_meta( $post_id, 'caremil_short_desc', '' );
        update_post_meta( $post_id, 'caremil_rating', '5' );
        update_post_meta( $post_id, 'caremil_rating_count', '0' );
    } else {
        error_log( 'Pancake Sync: Updating existing product #' . $post_id );
    }
    // If exists, DON'T update title or content - user manages those
    
    // Update ONLY Pancake-synced meta fields (ID, price, code)
    update_post_meta( $post_id, 'pancake_product_id', $variation_id );
    update_post_meta( $post_id, 'pancake_product_code', $product_code );
    update_post_meta( $post_id, 'pancake_sku', $sku );
    update_post_meta( $post_id, 'pancake_barcode', $barcode );
    update_post_meta( $post_id, 'caremil_price', number_format( $price, 0, ',', '.' ) );
    update_post_meta( $post_id, 'pancake_price_raw', $price );
    update_post_meta( $post_id, 'pancake_category_id', $category_id );
    update_post_meta( $post_id, 'pancake_category_name', $category_name );
    update_post_meta( $post_id, 'pancake_synced_at', current_time( 'mysql' ) );
    
    error_log( 'Pancake Sync: Synced product #' . $post_id . ' - Price: ' . $price );
    
    return $post_id;
}

/**
 * Đồng bộ tất cả sản phẩm từ Pancake
 * + XÓA sản phẩm không còn trong Pancake
 */
function caremil_sync_all_products_from_pancake() {
    $pancake_products = caremil_get_pancake_products( true ); // Force refresh
    
    if ( empty( $pancake_products ) ) {
        return array(
            'success' => false,
            'message' => 'Không tìm thấy sản phẩm nào từ Pancake',
            'total' => 0,
            'synced' => 0,
            'deleted' => 0
        );
    }
    
    $total = count( $pancake_products );
    $synced = 0;
    $errors = array();
    
    // Collect all Pancake IDs from current sync
    $pancake_ids = array();
    
    // Sync products from Pancake
    foreach ( $pancake_products as $product ) {
        $variation_id = isset( $product['id'] ) ? $product['id'] : '';
        if ( ! empty( $variation_id ) ) {
            $pancake_ids[] = $variation_id;
        }
        
        $result = caremil_sync_product_from_pancake( $product );
        if ( $result ) {
            $synced++;
        } else {
            $product_name = isset( $product['product']['name'] ) ? $product['product']['name'] : 'Unknown';
            $errors[] = 'Failed to sync: ' . $product_name;
        }
    }
    
    // Cleanup: Delete WordPress products that don't exist in Pancake anymore
    $deleted = 0;
    $all_synced_products = get_posts( array(
        'post_type' => 'caremil_product',
        'meta_key' => 'pancake_product_id',
        'posts_per_page' => -1,
        'post_status' => 'any'
    ) );
    
    foreach ( $all_synced_products as $wp_product ) {
        $wp_pancake_id = get_post_meta( $wp_product->ID, 'pancake_product_id', true );
        
        // If this product's Pancake ID is NOT in current Pancake products → DELETE
        if ( ! empty( $wp_pancake_id ) && ! in_array( $wp_pancake_id, $pancake_ids, true ) ) {
            error_log( 'Pancake Cleanup: Deleting product #' . $wp_product->ID . ' (' . $wp_product->post_title . ') - not in Pancake anymore' );
            wp_delete_post( $wp_product->ID, true ); // true = force delete permanently
            $deleted++;
        }
    }
    
    if ( $deleted > 0 ) {
        error_log( 'Pancake Cleanup: Deleted ' . $deleted . ' old products' );
    }
    
    return array(
        'success' => true,
        'message' => "Đã đồng bộ {$synced}/{$total} sản phẩm" . ( $deleted > 0 ? ", xóa {$deleted} sản phẩm cũ" : "" ),
        'total' => $total,
        'synced' => $synced,
        'deleted' => $deleted,
        'errors' => $errors
    );
}

/**
 * AJAX handler for syncing products
 */
function caremil_ajax_sync_products() {
    check_ajax_referer( 'caremil_sync_products_nonce', 'nonce' );
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Bạn không có quyền thực hiện thao tác này' ) );
    }
    
    $result = caremil_sync_all_products_from_pancake();
    
    if ( $result['success'] ) {
        wp_send_json_success( $result );
    } else {
        wp_send_json_error( $result );
    }
}
add_action( 'wp_ajax_caremil_sync_products', 'caremil_ajax_sync_products' );

/**
 * Thêm menu "Đồng Bộ Sản Phẩm" vào admin (dưới Pancake POS)
 */
function caremil_register_product_sync_menu() {
    add_submenu_page(
        'pancake-dashboard',      // Parent slug (updated)
        'Đồng bộ sản phẩm',
        '🔄 Đồng bộ sản phẩm',   // With emoji icon
        'manage_options',
        'pancake-product-sync',
        'caremil_render_product_sync_page'
    );
}
add_action( 'admin_menu', 'caremil_register_product_sync_menu', 20 ); // Priority 20 để load sau parent menu

/**
 * Render trang đồng bộ sản phẩm
 */
function caremil_render_product_sync_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'Bạn không có quyền truy cập trang này.' ) );
    }
    
    $warehouse_id = get_option( 'caremil_pancake_warehouse_id', '' );
    $shop_id = caremil_get_pancake_shop_id();
    $is_connected = function_exists( 'caremil_check_pancake_connection' ) && caremil_check_pancake_connection();
    
    // Get current synced products
    $synced_products = get_posts( array(
        'post_type' => 'caremil_product',
        'meta_key' => 'pancake_product_id',
        'posts_per_page' => -1,
        'post_status' => 'any'
    ) );
    
    ?>
    <div class="wrap">
        <h1>Đồng Bộ Sản Phẩm từ Pancake POS</h1>
        <p class="description">Đồng bộ sản phẩm từ Pancake POS về WordPress. Chỉ đồng bộ ID, tên, giá, mô tả. Hình ảnh sẽ được quản lý trực tiếp trên WordPress.</p>
        
        <?php if ( ! $is_connected ) : ?>
            <div class="notice notice-error">
                <p><strong>⚠️ Chưa kết nối với Pancake POS!</strong></p>
                <p>Vui lòng <a href="<?php echo admin_url( 'admin.php?page=pancake-settings' ); ?>">cấu hình kết nối Pancake</a> trước khi đồng bộ sản phẩm.</p>
            </div>
        <?php elseif ( empty( $warehouse_id ) ) : ?>
            <div class="notice notice-warning">
                <p><strong>⚠️ Chưa chọn kho hàng!</strong></p>
                <p>Vui lòng <a href="<?php echo admin_url( 'admin.php?page=pancake-settings' ); ?>">chọn kho hàng</a> trước khi đồng bộ sản phẩm.</p>
            </div>
        <?php else : ?>
            
            <!-- Sync Status -->
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>Trạng Thái Đồng Bộ</h2>
                <table class="widefat">
                    <tbody>
                        <tr>
                            <th style="width: 200px;">Kết nối Pancake:</th>
                            <td><span style="color: green;">✓ Đã kết nối</span></td>
                        </tr>
                        <tr>
                            <th>Shop ID:</th>
                            <td><?php echo esc_html( $shop_id ); ?></td>
                        </tr>
                        <tr>
                            <th>Kho hàng (Warehouse):</th>
                            <td>
                                <?php 
                                $warehouse_name = '';
                                if ( function_exists( 'caremil_get_pancake_warehouses' ) ) {
                                    $warehouses = caremil_get_pancake_warehouses();
                                    foreach ( $warehouses as $wh ) {
                                        if ( $wh['id'] === $warehouse_id ) {
                                            $warehouse_name = $wh['name'];
                                            break;
                                        }
                                    }
                                }
                                echo esc_html( $warehouse_name ? $warehouse_name : $warehouse_id );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Số sản phẩm đã đồng bộ:</th>
                            <td><strong><?php echo count( $synced_products ); ?></strong> sản phẩm</td>
                        </tr>
                        <tr>
                            <th>Lần đồng bộ cuối:</th>
                            <td>
                                <?php 
                                $last_sync = get_option( 'caremil_last_product_sync', '' );
                                echo $last_sync ? esc_html( $last_sync ) : 'Chưa đồng bộ lần nào';
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Sync Button -->
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>Đồng Bộ Sản Phẩm</h2>
                <p>Nhấn nút bên dưới để đồng bộ sản phẩm từ Pancake về WordPress. Quá trình này sẽ:</p>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li>Lấy tất cả sản phẩm active từ kho hàng đã chọn</li>
                    <li>Tạo mới hoặc cập nhật sản phẩm trong WordPress</li>
                    <li>Đồng bộ: ID sản phẩm, tên, mã, giá, mô tả, đơn vị, danh mục</li>
                    <li><strong>KHÔNG</strong> đồng bộ hình ảnh (bạn tự upload trong WordPress)</li>
                </ul>
                
                <p class="submit">
                    <button type="button" id="sync-products-btn" class="button button-primary button-hero">
                        <span class="dashicons dashicons-update" style="margin-top: 7px;"></span>
                        Đồng Bộ Sản Phẩm Ngay
                    </button>
                </p>
                
                <div id="sync-progress" style="display: none; margin-top: 20px;">
                    <div class="notice notice-info">
                        <p><span class="spinner is-active" style="float: none;"></span> <strong>Đang đồng bộ...</strong> Vui lòng đợi.</p>
                    </div>
                </div>
                
                <div id="sync-result" style="margin-top: 20px;"></div>
            </div>
            
            <!-- Synced Products List -->
            <?php if ( ! empty( $synced_products ) ) : ?>
            <div class="card" style="max-width: 100%; margin-top: 20px;">
                <h2>Danh Sách Sản Phẩm Đã Đồng Bộ</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th>Tên sản phẩm</th>
                            <th>Mã Pancake</th>
                            <th>Giá</th>
                            <th>Danh mục Pancake</th>
                            <th>Đồng bộ lần cuối</th>
                            <th width="100">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $synced_products as $product ) : 
                            $pancake_id = get_post_meta( $product->ID, 'pancake_product_id', true );
                            $pancake_code = get_post_meta( $product->ID, 'pancake_product_code', true );
                            $price = get_post_meta( $product->ID, 'caremil_price', true );
                            $category = get_post_meta( $product->ID, 'pancake_category_name', true );
                            $synced_at = get_post_meta( $product->ID, 'pancake_synced_at', true );
                        ?>
                        <tr>
                            <td><?php echo $product->ID; ?></td>
                            <td><strong><?php echo esc_html( $product->post_title ); ?></strong></td>
                            <td><code><?php echo esc_html( $pancake_code ? $pancake_code : $pancake_id ); ?></code></td>
                            <td><?php echo esc_html( $price ? $price . 'đ' : 'N/A' ); ?></td>
                            <td><?php echo esc_html( $category ? $category : 'N/A' ); ?></td>
                            <td><?php echo $synced_at ? esc_html( date( 'd/m/Y H:i', strtotime( $synced_at ) ) ) : 'N/A'; ?></td>
                            <td>
                                <a href="<?php echo get_edit_post_link( $product->ID ); ?>" class="button button-small">Sửa</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
        <?php endif; ?>
    </div>
    
    <style>
        .card {
            background: #fff;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
            padding: 20px;
        }
        .card h2 {
            margin-top: 0;
        }
        .card ul {
            margin: 15px 0;
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Ensure progress is hidden on page load
        $('#sync-progress').hide();
        $('#sync-result').html('');
        
        $('#sync-products-btn').on('click', function() {
            var $btn = $(this);
            var $progress = $('#sync-progress');
            var $result = $('#sync-result');
            
            if (!confirm('Bạn có chắc muốn đồng bộ sản phẩm từ Pancake?\n\nQuá trình này có thể mất vài phút.')) {
                return;
            }
            
            $btn.prop('disabled', true);
            $progress.show();
            $result.html('');
            
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'caremil_sync_products',
                    nonce: '<?php echo wp_create_nonce( 'caremil_sync_products_nonce' ); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        var message = '<div class="notice notice-success"><p><strong>✓ Đồng bộ thành công!</strong><br>' +
                            response.data.message +
                            '<br>Tổng: ' + response.data.total + ' sản phẩm, Đã đồng bộ: ' + response.data.synced;
                        
                        if (response.data.deleted > 0) {
                            message += ', <span style="color: #d63638;">Đã xóa: ' + response.data.deleted + '</span>';
                        }
                        
                        message += '</p></div>';
                        $result.html(message);
                        
                        // Update last sync time
                        <?php update_option( 'caremil_last_product_sync', current_time( 'd/m/Y H:i:s' ) ); ?>
                        
                        // Reload page after 2 seconds
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $result.html(
                            '<div class="notice notice-error"><p><strong>✗ Đồng bộ thất bại!</strong><br>' +
                            (response.data.message || 'Có lỗi xảy ra') + '</p></div>'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Sync error:', error);
                    $result.html(
                        '<div class="notice notice-error"><p><strong>✗ Lỗi kết nối!</strong><br>Không thể kết nối đến server. Vui lòng thử lại.</p></div>'
                    );
                },
                complete: function() {
                    $btn.prop('disabled', false);
                    $progress.hide();
                }
            });
        });
    });
    </script>
    <?php
}
