<?php
/**
 * Product Menu Integration
 * Merge "Sản phẩm" with Pancake products
 */

// ============================================================================
// STEP 1: UPDATE MENU STRUCTURE
// ============================================================================

/**
 * Remove old custom menus (but keep 'product' CPT menu)
 */
function caremil_remove_old_custom_product_menu() {
    // Only remove the old custom menus, NOT the product menu
    remove_menu_page( 'caremil-products-app' );
    // Note: We don't remove 'edit.php?post_type=product' - it will show automatically
}
add_action( 'admin_menu', 'caremil_remove_old_custom_product_menu', 999 );

/**
 * Add Pancake sync as submenu under Products
 */
function caremil_add_pancake_sync_to_products() {
    add_submenu_page(
        'edit.php?post_type=product',
        'Đồng bộ từ Pancake POS',
        '🔄 Đồng bộ Pancake',
        'manage_options',
        'pancake-product-sync',
        'caremil_render_product_sync_page',
        99
    );
}
add_action( 'admin_menu', 'caremil_add_pancake_sync_to_products', 25 );

// ============================================================================
// STEP 2: CUSTOM COLUMNS FOR PRODUCT LIST
// ============================================================================

/**
 * Add custom columns to product list table
 */
function caremil_add_product_list_columns( $columns ) {
    $new_columns = array();
    
    foreach ( $columns as $key => $value ) {
        $new_columns[ $key ] = $value;
        
        // Add custom columns after title
        if ( $key === 'title' ) {
            $new_columns['product_image'] = '📷 Hình';
            $new_columns['pancake_sku'] = '📦 SKU';
            $new_columns['product_price'] = '💰 Giá';
            $new_columns['pancake_status'] = '🔄 Pancake';
        }
    }
    
    return $new_columns;
}
add_filter( 'manage_product_posts_columns', 'caremil_add_product_list_columns' );

/**
 * Make custom columns sortable
 */
function caremil_product_sortable_columns( $columns ) {
    $columns['product_price'] = 'pancake_price_raw';
    $columns['pancake_sku'] = 'pancake_sku';
    return $columns;
}
add_filter( 'manage_edit-product_sortable_columns', 'caremil_product_sortable_columns' );

/**
 * Populate custom column content
 */
function caremil_product_column_content( $column, $post_id ) {
    switch ( $column ) {
        case 'product_image':
            if ( has_post_thumbnail( $post_id ) ) {
                echo get_the_post_thumbnail( $post_id, array( 50, 50 ), array( 'style' => 'border-radius: 4px;' ) );
            } else {
                echo '<div style="width:50px;height:50px;background:#f0f0f1;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#9ca3af;">📷</div>';
            }
            break;
            
        case 'pancake_sku':
            $sku = get_post_meta( $post_id, 'pancake_sku', true );
            if ( $sku ) {
                echo '<code style="background:#f0f0f1;padding:3px 8px;border-radius:3px;font-size:11px;">' . esc_html( $sku ) . '</code>';
            } else {
                echo '<span style="color:#9ca3af;">—</span>';
            }
            break;
            
        case 'product_price':
            $price = get_post_meta( $post_id, 'caremil_price', true );
            if ( $price ) {
                echo '<strong style="color:#10b981;font-size:14px;">' . esc_html( $price ) . 'đ</strong>';
            } else {
                echo '<span style="color:#9ca3af;">Chưa có</span>';
            }
            break;
            
        case 'pancake_status':
            $pancake_id = get_post_meta( $post_id, 'pancake_product_id', true );
            if ( $pancake_id ) {
                echo '<span style="display:inline-block;padding:4px 10px;background:#d1fae5;color:#065f46;border-radius:12px;font-size:11px;font-weight:600;">✓ Synced</span>';
            } else {
                echo '<span style="display:inline-block;padding:4px 10px;background:#f3f4f6;color:#6b7280;border-radius:12px;font-size:11px;font-weight:600;">Manual</span>';
            }
            break;
    }
}
add_action( 'manage_product_posts_custom_column', 'caremil_product_column_content', 10, 2 );

// ============================================================================
// STEP 3: META BOXES FOR PRODUCT EDIT SCREEN
// ============================================================================

/**
 * Add custom meta boxes to product edit screen
 */
function caremil_add_product_meta_boxes() {
    // Pancake Data Box (Read-only, high priority in sidebar)
    add_meta_box(
        'pancake_product_data',
        '🔒 Dữ liệu Pancake POS',
        'caremil_render_pancake_data_meta_box',
        'product',
        'side',
        'high'
    );
    
    // Product Info Box
    add_meta_box(
        'product_extra_info',
        '📝 Thông tin bổ sung',
        'caremil_render_product_info_meta_box',
        'product',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'caremil_add_product_meta_boxes' );

/**
 * Render Pancake Data Meta Box (Read-only)
 */
function caremil_render_pancake_data_meta_box( $post ) {
    $pancake_id = get_post_meta( $post->ID, 'pancake_product_id', true );
    $sku = get_post_meta( $post->ID, 'pancake_sku', true );
    $barcode = get_post_meta( $post->ID, 'pancake_barcode', true );
    $price = get_post_meta( $post->ID, 'caremil_price', true );
    $price_raw = get_post_meta( $post->ID, 'pancake_price_raw', true );
    $category = get_post_meta( $post->ID, 'pancake_category_name', true );
    $synced_at = get_post_meta( $post->ID, 'pancake_synced_at', true );
    
    ?>
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; margin: -6px -12px; color: white; border-radius: 4px;">
        <?php if ( $pancake_id ) : ?>
            <p style="margin: 0 0 12px; color: rgba(255,255,255,0.9); font-size: 12px;">
                Sản phẩm này được đồng bộ từ Pancake POS. Giá và SKU tự động cập nhật.
            </p>
            
            <div style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 4px; margin-bottom: 12px;">
                <p style="margin: 0 0 8px;"><strong>🔗 Pancake ID:</strong><br>
                <code style="background: rgba(0,0,0,0.2); padding: 4px 8px; border-radius: 3px; font-size: 10px; color: #fff; word-break: break-all;"><?php echo esc_html( $pancake_id ); ?></code></p>
                
                <p style="margin: 0 0 8px;"><strong>📦 SKU:</strong><br>
                <code style="background: rgba(0,0,0,0.2); padding: 4px 8px; border-radius: 3px; color: #fff;"><?php echo esc_html( $sku ); ?></code></p>
                
                <?php if ( $barcode ) : ?>
                <p style="margin: 0 0 8px;"><strong>📊 Barcode:</strong><br>
                <code style="background: rgba(0,0,0,0.2); padding: 4px 8px; border-radius: 3px; color: #fff;"><?php echo esc_html( $barcode ); ?></code></p>
                <?php endif; ?>
            </div>
            
            <div style="background: rgba(255,255,255,0.15); padding: 15px; border-radius: 4px; margin-bottom: 12px; text-align: center;">
                <p style="margin: 0 0 5px; font-size: 12px; opacity: 0.9;">Giá bán:</p>
                <p style="margin: 0; font-size: 28px; font-weight: bold;">
                    <?php echo $price ? esc_html( $price ) . 'đ' : '<span style="font-size: 14px; opacity: 0.7;">Chưa có giá</span>'; ?>
                </p>
                <?php if ( $price_raw > 0 ) : ?>
                <p style="margin: 5px 0 0; font-size: 11px; opacity: 0.7;">
                    (Raw: <?php echo number_format( $price_raw, 0, ',', '.' ); ?>đ)
                </p>
                <?php endif; ?>
            </div>
            
            <?php if ( $category ) : ?>
            <p style="margin: 0 0 12px;"><strong>📁 Danh mục:</strong> <?php echo esc_html( $category ); ?></p>
            <?php endif; ?>
            
            <?php if ( $synced_at ) : ?>
            <p style="margin: 0 0 15px; font-size: 11px; opacity: 0.8;">
                🕐 Đồng bộ lần cuối: <?php echo date( 'd/m/Y H:i', strtotime( $synced_at ) ); ?>
            </p>
            <?php endif; ?>
            
            <a href="<?php echo admin_url( 'admin.php?page=pancake-product-sync' ); ?>" 
               class="button button-secondary" 
               style="width: 100%; text-align: center; background: white; color: #667eea; border: none; padding: 8px; font-weight: 600;">
                🔄 Đồng bộ lại từ Pancake
            </a>
        <?php else : ?>
            <p style="margin: 0; text-align: center; opacity: 0.9;">
                ⚠️ Sản phẩm thủ công<br>
                <small>Không liên kết với Pancake POS</small>
            </p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Render Product Info Meta Box
 */
function caremil_render_product_info_meta_box( $post ) {
    wp_nonce_field( 'caremil_product_meta', 'caremil_product_meta_nonce' );
    
    $short_desc = get_post_meta( $post->ID, 'caremil_short_desc', true );
    $rating = get_post_meta( $post->ID, 'caremil_rating', true );
    $rating_count = get_post_meta( $post->ID, 'caremil_rating_count', true );
    
    ?>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="caremil_short_desc">Mô tả ngắn</label></th>
            <td>
                <textarea 
                    id="caremil_short_desc" 
                    name="caremil_short_desc" 
                    rows="3" 
                    class="large-text"
                    placeholder="Nhập mô tả ngắn gọn (hiển thị trong danh sách sản phẩm)..."
                ><?php echo esc_textarea( $short_desc ); ?></textarea>
                <p class="description">Mô tả ngắn gọn để hiển thị trong card sản phẩm.</p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="caremil_rating">Đánh giá</label></th>
            <td>
                <select id="caremil_rating" name="caremil_rating">
                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                        <option value="<?php echo $i; ?>" <?php selected( $rating, $i ); ?>>
                            <?php echo str_repeat( '⭐', $i ); ?> (<?php echo $i; ?> sao)
                        </option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="caremil_rating_count">Số lượt đánh giá</label></th>
            <td>
                <input 
                    type="number" 
                    id="caremil_rating_count" 
                    name="caremil_rating_count" 
                    value="<?php echo esc_attr( $rating_count ); ?>" 
                    min="0"
                    class="small-text"
                />
                <p class="description">Số lượng người đã đánh giá sản phẩm này.</p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save product meta data
 */
function caremil_save_product_meta( $post_id ) {
    // Security checks
    if ( ! isset( $_POST['caremil_product_meta_nonce'] ) ) {
        return;
    }
    
    if ( ! wp_verify_nonce( $_POST['caremil_product_meta_nonce'], 'caremil_product_meta' ) ) {
        return;
    }
    
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    // Save fields
    if ( isset( $_POST['caremil_short_desc'] ) ) {
        update_post_meta( $post_id, 'caremil_short_desc', sanitize_textarea_field( $_POST['caremil_short_desc'] ) );
    }
    
    if ( isset( $_POST['caremil_rating'] ) ) {
        update_post_meta( $post_id, 'caremil_rating', sanitize_text_field( $_POST['caremil_rating'] ) );
    }
    
    if ( isset( $_POST['caremil_rating_count'] ) ) {
        update_post_meta( $post_id, 'caremil_rating_count', absint( $_POST['caremil_rating_count'] ) );
    }
}
add_action( 'save_post_product', 'caremil_save_product_meta' );

// ============================================================================
// STEP 4: ADMIN NOTICES & HELP
// ============================================================================

/**
 * Add admin notice for product page
 */
function caremil_product_admin_notices() {
    $screen = get_current_screen();
    
    if ( $screen->id === 'edit-product' ) {
        ?>
        <div class="notice notice-info is-dismissible">
            <p>
                <strong>💡 Tip:</strong> 
                Sản phẩm có badge <strong style="color:#065f46;background:#d1fae5;padding:2px 8px;border-radius:8px;">✓ Synced</strong> được đồng bộ từ Pancake POS. 
                Giá tự động cập nhật khi sync. 
                <a href="<?php echo admin_url( 'admin.php?page=pancake-product-sync' ); ?>">Đồng bộ ngay &rarr;</a>
            </p>
        </div>
        <?php
    }
}
add_action( 'admin_notices', 'caremil_product_admin_notices' );
