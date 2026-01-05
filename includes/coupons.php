<?php
/**
 * CareMIL Coupon Management
 * Handles CPT registration, metadata, and validation logic.
 */

// 1. Register Coupon CPT
function caremil_register_coupon_cpt() {
    $labels = array(
        'name'               => 'Mã Giảm Giá',
        'singular_name'      => 'Mã Giảm Giá',
        'add_new'            => 'Thêm Mã Mới',
        'add_new_item'       => 'Thêm Mã Giảm Giá Mới',
        'edit_item'          => 'Sửa Mã Giảm Giá',
        'new_item'           => 'Mã Mới',
        'view_item'          => 'Xem Mã',
        'search_items'       => 'Tìm Mã',
        'not_found'          => 'Không tìm thấy mã nào',
        'menu_name'          => 'Kho Ưu Đãi (Coupons)',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => false, // Internal use mainly
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-tickets-alt',
        'supports'            => array('title'), // Title is the Coupon Code
        'rewrite'             => false,
        'capability_type'     => 'post',
    );

    register_post_type('caremil_coupon', $args);
}
add_action('init', 'caremil_register_coupon_cpt');

// 2. Add Meta Boxes for Coupon Data
function caremil_coupon_add_meta_boxes() {
    add_meta_box(
        'caremil_coupon_data',
        'Thông Tin Ưu Đãi',
        'caremil_coupon_meta_box_callback',
        'caremil_coupon',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'caremil_coupon_add_meta_boxes');

function caremil_coupon_meta_box_callback($post) {
    // Nonce
    wp_nonce_field('caremil_coupon_save', 'caremil_coupon_nonce');

    // Retrieve existing values
    $type = get_post_meta($post->ID, '_coupon_type', true) ?: 'fixed'; // fixed | percent
    $amount = get_post_meta($post->ID, '_coupon_amount', true) ?: 0;
    $min_order = get_post_meta($post->ID, '_coupon_min_order', true) ?: 0;
    $expiry = get_post_meta($post->ID, '_coupon_expiry', true) ?: '';
    $desc = get_post_meta($post->ID, '_coupon_description', true) ?: '';

    ?>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div>
            <label><strong>Loại Giảm Giá:</strong></label><br>
            <select name="coupon_type" style="width: 100%;" onchange="updateCouponCategory(this.value)">
                <option value="fixed" <?php selected($type, 'fixed'); ?>>Trừ tiền trực tiếp (VNĐ)</option>
                <option value="percent" <?php selected($type, 'percent'); ?>>Theo phần trăm (%)</option>
                <option value="freeship" <?php selected($type, 'freeship'); ?>>Miễn phí vận chuyển</option>
            </select>
            <p class="description" id="coupon-category-label" style="margin-top: 5px; color: #0284c7; font-weight: bold;">
                📦 Loại: <span id="category-text">
                    <?php 
                    echo ($type === 'freeship') ? 'Giảm Giá Vận Chuyển' : 'Giảm Giá Đơn Hàng';
                    ?>
                </span>
            </p>
        </div>
        <div>
            <label><strong>Giá Trị:</strong></label><br>
            <input type="number" name="coupon_amount" value="<?php echo esc_attr($amount); ?>" style="width: 100%;" step="0.01" id="coupon-amount-input">
            <p class="description" id="amount-hint">
                <?php echo ($type === 'freeship') ? 'Để 0 cho freeship' : 'Nhập số tiền hoặc %'; ?>
            </p>
            <script>
            function updateCouponCategory(type) {
                const categoryText = document.getElementById('category-text');
                const amountInput = document.getElementById('coupon-amount-input');
                const amountHint = document.getElementById('amount-hint');
                
                if (type === 'freeship') {
                    categoryText.textContent = 'Giảm Giá Vận Chuyển';
                    amountInput.value = '0';
                    amountInput.disabled = true;
                    amountHint.textContent = 'Freeship không cần giá trị';
                } else {
                    categoryText.textContent = 'Giảm Giá Đơn Hàng';
                    amountInput.disabled = false;
                    amountHint.textContent = type === 'percent' ? 'Nhập % giảm giá' : 'Nhập số tiền giảm';
                }
            }
            // Run on load
            document.addEventListener('DOMContentLoaded', function() {
                const typeSelect = document.querySelector('select[name="coupon_type"]');
                if (typeSelect) updateCouponCategory(typeSelect.value);
            });
            </script>
        </div>
        
        <!-- Stacking Rules Info -->
        <div style="grid-column: span 2; background: #f0f9ff; padding: 12px; border-radius: 8px; border-left: 4px solid #0284c7;">
            <strong style="color: #0284c7;">📚 Quy Tắc Chồng Chéo Mã:</strong>
            <ul style="margin: 8px 0 0 20px; font-size: 13px; color: #475569;">
                <li><strong>Giảm Giá Đơn Hàng</strong> (Fixed/Percent): <span style="color: #16a34a;">✅ Cho phép nhiều mã cùng lúc</span></li>
                <li><strong>Giảm Phí Ship</strong> (Freeship): <span style="color: #dc2626;">❌ Chỉ được áp 1 mã duy nhất</span></li>
                <li>Ví dụ: <code>FREESHIP + MK213 + SALE50</code> = OK ✅</li>
            </ul>
        </div>
        
        <div>
            <label><strong>Đơn Tối Thiểu:</strong></label><br>
            <input type="number" name="coupon_min_order" value="<?php echo esc_attr($min_order); ?>" style="width: 100%;">
        </div>
        <div>
            <label><strong>Hạn Sử Dụng:</strong></label><br>
            <input type="date" name="coupon_expiry" value="<?php echo esc_attr($expiry); ?>" style="width: 100%;">
        </div>
        <div style="grid-column: span 2; border-top: 1px solid #eee; margin-top: 15px; padding-top: 15px;">
             <strong>Cấu Hình Nâng Cao (Giới Hạn & Riêng Tư)</strong>
        </div>
        <div>
            <label><strong>Giới hạn mỗi người (theo SĐT):</strong></label><br>
            <input type="number" name="coupon_limit_user" value="<?php echo esc_attr(get_post_meta($post->ID, '_coupon_limit_user', true)); ?>" style="width: 100%;" placeholder="0 = Không giới hạn">
            <p class="description">Số lần tối đa 1 khách được dùng.</p>
        </div>
        <div>
            <label><strong>Áp dụng riêng cho SĐT (Ngăn cách phẩy):</strong></label><br>
            <input type="text" name="coupon_allowed_phones" value="<?php echo esc_attr(get_post_meta($post->ID, '_coupon_allowed_phones', true)); ?>" style="width: 100%;" placeholder="Ví dụ: 0912345678, 0987654321">
            <p class="description">Để trống nếu áp dụng cho tất cả.</p>
        </div>
        
        <div style="grid-column: span 2; border-top: 1px solid #eee; margin-top: 15px; padding-top: 15px;">
             <strong>Cấu Hình Tiếp Thị Liên Kết (KOL / Affiliate)</strong>
        </div>
        <div>
            <label><strong>Tên KOL / Partner:</strong></label><br>
            <input type="text" name="coupon_kol_name" value="<?php echo esc_attr(get_post_meta($post->ID, '_coupon_kol_name', true)); ?>" style="width: 100%;" placeholder="Ví dụ: Nam Khánh, Reviewer A...">
            <p class="description">Gắn mã này cho một KOL để tính doanh thu.</p>
        </div>
        <div>
            <?php
            // Calculate Stats
            $code = $post->post_title;
            $stats_usage = 0;
            $stats_revenue = 0;
            if ($code) {
                global $wpdb;
                $logs = $wpdb->get_results($wpdb->prepare(
                    "SELECT meta_value FROM $wpdb->postmeta 
                     WHERE meta_key = '_log_order_total' 
                     AND post_id IN (
                        SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_log_coupon_code' AND meta_value = %s
                     )",
                    $code
                ));
                $stats_usage = count($logs);
                foreach ($logs as $l) $stats_revenue += floatval($l->meta_value);
            }
            ?>
            <label><strong>Thống Kê Hiệu Quả:</strong></label><br>
            <div style="background: #f0f9ff; padding: 10px; border-radius: 8px; border: 1px solid #bae6fd;">
                <strong>Doanh Thu:</strong> <span style="color: #0284c7; font-size: 16px;"><?php echo number_format($stats_revenue); ?>đ</span><br>
                <strong>Số Lượt Dùng:</strong> <?php echo $stats_usage; ?> đơn
            </div>
        </div>

        <div style="grid-column: span 2;">
            <label><strong>Mô Tả Ngắn (Hiển thị cho khách):</strong></label><br>
            <textarea name="coupon_description" style="width: 100%;" rows="2"><?php echo esc_textarea($desc); ?></textarea>
        </div>
    </div>
    <?php
}

function caremil_save_coupon_meta($post_id) {
    if (!isset($_POST['caremil_coupon_nonce']) || !wp_verify_nonce($_POST['caremil_coupon_nonce'], 'caremil_coupon_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['coupon_type'])) update_post_meta($post_id, '_coupon_type', sanitize_text_field($_POST['coupon_type']));
    if (isset($_POST['coupon_amount'])) update_post_meta($post_id, '_coupon_amount', floatval($_POST['coupon_amount']));
    if (isset($_POST['coupon_min_order'])) update_post_meta($post_id, '_coupon_min_order', floatval($_POST['coupon_min_order']));
    if (isset($_POST['coupon_expiry'])) update_post_meta($post_id, '_coupon_expiry', sanitize_text_field($_POST['coupon_expiry']));
    if (isset($_POST['coupon_description'])) update_post_meta($post_id, '_coupon_description', sanitize_textarea_field($_POST['coupon_description']));
    
    // New fields
    if (isset($_POST['coupon_limit_user'])) update_post_meta($post_id, '_coupon_limit_user', intval($_POST['coupon_limit_user']));
    if (isset($_POST['coupon_allowed_phones'])) update_post_meta($post_id, '_coupon_allowed_phones', sanitize_text_field($_POST['coupon_allowed_phones']));
    if (isset($_POST['coupon_kol_name'])) update_post_meta($post_id, '_coupon_kol_name', sanitize_text_field($_POST['coupon_kol_name']));
}
add_action('save_post', 'caremil_save_coupon_meta');

// 3. Helper Functions

// Register Usage Log CPT (Invisible)
function caremil_register_coupon_log_cpt() {
    register_post_type('caremil_coupon_log', [
        'public' => false,
        'supports' => ['title', 'custom-fields'],
        'label' => 'Coupon Logs'
    ]);
}
add_action('init', 'caremil_register_coupon_log_cpt');

function caremil_log_coupon_usage($coupon_code, $phone, $order_id, $order_total = 0) {
    if (!$coupon_code || !$phone) return;
    $log_id = wp_insert_post([
        'post_type' => 'caremil_coupon_log',
        'post_title' => "Usage: $coupon_code by $phone",
        'post_status' => 'publish'
    ]);
    update_post_meta($log_id, '_log_coupon_code', $coupon_code);
    update_post_meta($log_id, '_log_phone', $phone);
    update_post_meta($log_id, '_log_order_id', $order_id);
    update_post_meta($log_id, '_log_order_total', $order_total);
    update_post_meta($log_id, '_log_date', current_time('mysql'));
}

function caremil_check_user_usage_count($coupon_code, $phone) {
    if (!$phone) return 0;
    
    // Query logs to count how many times this phone used this code
    $args = [
        'post_type' => 'caremil_coupon_log',
        'posts_per_page' => -1, // count all
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => '_log_coupon_code',
                'value' => $coupon_code,
                'compare' => '='
            ],
            [
                'key' => '_log_phone',
                'value' => $phone,
                'compare' => '='
            ]
        ]
    ];
    $query = new WP_Query($args);
    return $query->found_posts;
}

function caremil_get_available_coupons() {
    // This function returns PUBLIC coupons only.
    // Private coupons (assigned to specific phones) might be hidden from the general list
    // OR we show them but they are locked?
    // Let's filter: only return coupons that are either [Public (no phone list)] OR [Assigned to current user]
    
    $current_phone = isset($_SESSION['pancake_phone']) ? $_SESSION['pancake_phone'] : '';

    $args = array(
        'post_type'      => 'caremil_coupon',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'relation' => 'OR',
                array(
                    'key' => '_coupon_expiry',
                    'value' => date('Y-m-d'),
                    'compare' => '>=',
                    'type' => 'DATE'
                ),
                array(
                    'key' => '_coupon_expiry',
                    'value' => '',
                    'compare' => '='
                )
            )
        )
    );
    
    $all = get_posts($args);
    $filtered = [];
    
    foreach ($all as $c) {
        $allowed = get_post_meta($c->ID, '_coupon_allowed_phones', true);
        if (!empty($allowed)) {
            // Private coupon
            if (!$current_phone) continue; // Guest can't see private
            $phones = array_map('trim', explode(',', $allowed));
            if (!in_array($current_phone, $phones)) continue; // Not for this user
        }
        $filtered[] = $c;
    }
    
    return $filtered;
}

function caremil_get_coupon_by_code($code) {
    $args = array(
        'post_type'  => 'caremil_coupon',
        'title'      => $code,
        'post_status'=> 'publish',
        'numberposts'=> 1
    );
    $posts = get_posts($args);
    return !empty($posts) ? $posts[0] : null;
}

function caremil_calculate_discount($coupon_id, $cart_total) {
    $type = get_post_meta($coupon_id, '_coupon_type', true);
    $amount = floatval(get_post_meta($coupon_id, '_coupon_amount', true));
    $min_order = floatval(get_post_meta($coupon_id, '_coupon_min_order', true));

    if ($cart_total < $min_order) return 0;

    $discount = 0;
    if ($type === 'percent') {
        $discount = ($cart_total * $amount) / 100;
    } else {
        $discount = $amount;
    }

    // Ensure discount doesn't exceed total
    return min($discount, $cart_total);
}

// 4. AJAX Handler: Apply Coupon
function caremil_handle_apply_coupon() {
    // Check nonce
    // if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'caremil_coupon_nonce')) {
    //     wp_send_json_error(['message' => 'Phiên làm việc hết hạn.']);
    // }

    $code = isset($_POST['code']) ? sanitize_text_field($_POST['code']) : '';
    if (!$code) {
        wp_send_json_error(['message' => 'Vui lòng nhập mã giảm giá.']);
    }

    $coupon = caremil_get_coupon_by_code($code);
    if (!$coupon) {
        wp_send_json_error(['message' => 'Mã giảm giá không tồn tại.']);
    }

    // Check expiry
    $expiry = get_post_meta($coupon->ID, '_coupon_expiry', true);
    if ($expiry && strtotime($expiry) < time()) {
        wp_send_json_error(['message' => 'Mã giảm giá đã hết hạn.']);
    }

    // --- NEW VALIDATION: Check Allowed Phones (Private Coupon) ---
    $allowed_phones_str = get_post_meta($coupon->ID, '_coupon_allowed_phones', true);
    $current_phone = isset($_SESSION['pancake_phone']) ? $_SESSION['pancake_phone'] : '';

    if (!empty($allowed_phones_str)) {
        if (!$current_phone) {
            wp_send_json_error(['message' => 'Bạn cần đăng nhập để dùng mã này.']);
        }
        $allowed = array_map('trim', explode(',', $allowed_phones_str));
        if (!in_array($current_phone, $allowed)) {
            wp_send_json_error(['message' => 'Mã này không áp dụng cho tài khoản của bạn.']);
        }
    }

    // --- NEW VALIDATION: Usage Limit Per User ---
    $limit_user = get_post_meta($coupon->ID, '_coupon_limit_user', true);
    if ($limit_user > 0) {
        if (!$current_phone) {
             wp_send_json_error(['message' => 'Bạn cần đăng nhập để dùng mã này.']);
        }
        $used_count = caremil_check_user_usage_count($code, $current_phone);
        if ($used_count >= $limit_user) {
            wp_send_json_error(['message' => "Bạn đã dùng mã này hết số lần quy định ($limit_user lần)."]);
        }
    }
    // -----------------------------------------------------------

    // Calculate discount for preview
    if (!function_exists('caremil_get_cart_total')) {
        // Fallback or verify session cart manually if needed
        // Assuming this function is available or we re-calculate
        require_once get_template_directory() . '/includes/order-api.php'; // ensure dependencies if needed
    }
    
    // We need cart total. 
    // If caremil_get_cart_total isn't available here, we might need to manually calc from session
    $cart = isset($_SESSION['caremil_cart']) ? $_SESSION['caremil_cart'] : [];
    $subtotal = 0;
    if (is_array($cart)) {
         foreach ($cart as $item) {
             $price = floatval(str_replace([',','.'], '', $item['price']));
             $subtotal += $price * intval($item['quantity']);
         }
    }

    $min_order = floatval(get_post_meta($coupon->ID, '_coupon_min_order', true));

    if ($subtotal < $min_order) {
        wp_send_json_error(['message' => 'Đơn hàng chưa đạt giá trị tối thiểu: ' . number_format($min_order) . 'đ']);
    }

    // Determine coupon type
    $coupon_type = get_post_meta($coupon->ID, '_coupon_type', true);
    $is_freeship = ($coupon_type === 'freeship');
    
    // Initialize session structure FIRST (before any checks)
    if (!isset($_SESSION['caremil_applied_coupons']) || !is_array($_SESSION['caremil_applied_coupons'])) {
        $_SESSION['caremil_applied_coupons'] = [
            'shipping' => null,      // Only 1 freeship allowed
            'order' => []            // Multiple order discounts allowed
        ];
    }
    
    // Ensure structure integrity
    if (!isset($_SESSION['caremil_applied_coupons']['shipping'])) {
        $_SESSION['caremil_applied_coupons']['shipping'] = null;
    }
    if (!isset($_SESSION['caremil_applied_coupons']['order']) || !is_array($_SESSION['caremil_applied_coupons']['order'])) {
        $_SESSION['caremil_applied_coupons']['order'] = [];
    }
    
    // Check if coupon already applied
    if ($is_freeship) {
        // FREESHIP: Only allow 1
        if ($_SESSION['caremil_applied_coupons']['shipping'] !== null) {
            $existing = $_SESSION['caremil_applied_coupons']['shipping'];
            $existing_code = isset($existing['code']) ? $existing['code'] : 'Unknown';
            wp_send_json_error([
                'message' => "Đã có mã freeship ({$existing_code}). Vui lòng gỡ mã cũ trước."
            ]);
        }
    } else {
        // ORDER DISCOUNT: Check if this code already in list
        if (is_array($_SESSION['caremil_applied_coupons']['order'])) {
            foreach ($_SESSION['caremil_applied_coupons']['order'] as $existing) {
                if (isset($existing['code']) && $existing['code'] === $code) {
                    wp_send_json_error(['message' => 'Mã này đã được áp dụng rồi.']);
                }
            }
        }
    }
    
    // Calculate discount
    $discount = caremil_calculate_discount($coupon->ID, $subtotal);
    
    // Save to session
    if ($is_freeship) {
        $_SESSION['caremil_applied_coupons']['shipping'] = [
            'code' => $code,
            'id' => $coupon->ID,
            'type' => $coupon_type
        ];
    } else {
        $_SESSION['caremil_applied_coupons']['order'][] = [
            'code' => $code,
            'id' => $coupon->ID,
            'type' => $coupon_type,
            'discount' => $discount
        ];
    }
    
    // Backward compatibility
    $_SESSION['caremil_applied_coupon'] = [
        'code' => $code,
        'id' => $coupon->ID,
        'discount' => $discount
    ];

    wp_send_json_success([
        'message' => 'Áp dụng mã thành công!',
        'category' => $is_freeship ? 'shipping' : 'order',
        'discount' => $discount,
        'formatted_discount' => number_format($discount) . 'đ'
    ]);
}
add_action('wp_ajax_caremil_apply_coupon', 'caremil_handle_apply_coupon');
add_action('wp_ajax_nopriv_caremil_apply_coupon', 'caremil_handle_apply_coupon');

// 5. AJAX Handler: Remove Coupon
function caremil_handle_remove_coupon() {
    unset($_SESSION['caremil_applied_coupon']);
    wp_send_json_success(['message' => 'Đã gỡ mã giảm giá.']);
}
add_action('wp_ajax_caremil_remove_coupon', 'caremil_handle_remove_coupon');
add_action('wp_ajax_nopriv_caremil_remove_coupon', 'caremil_handle_remove_coupon');

// 6. AJAX Handler: Remove Coupon By Category or Code
function caremil_handle_remove_coupon_by_category() {
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $code = isset($_POST['code']) ? sanitize_text_field($_POST['code']) : ''; // Optional: remove specific code
    
    // Ensure session structure exists
    if (!isset($_SESSION['caremil_applied_coupons'])) {
        $_SESSION['caremil_applied_coupons'] = ['shipping' => null, 'order' => []];
    }
    
    if ($category === 'shipping') {
        // Remove freeship
        if (isset($_SESSION['caremil_applied_coupons']['shipping']) && $_SESSION['caremil_applied_coupons']['shipping'] !== null) {
            $_SESSION['caremil_applied_coupons']['shipping'] = null;
            
            // Update backward compatible session
            if (empty($_SESSION['caremil_applied_coupons']['order'])) {
                unset($_SESSION['caremil_applied_coupon']);
            }
            
            wp_send_json_success(['message' => 'Đã gỡ mã freeship.']);
            return; // IMPORTANT: prevent fallthrough
        }
    } elseif ($category === 'order') {
        // Remove order discount
        if ($code) {
            // Remove specific code
            $order_coupons = isset($_SESSION['caremil_applied_coupons']['order']) ? $_SESSION['caremil_applied_coupons']['order'] : [];
            $found = false;
            
            $filtered = array_filter($order_coupons, function($c) use ($code, &$found) {
                if (isset($c['code']) && $c['code'] === $code) {
                    $found = true;
                    return false; // Remove this one
                }
                return true; // Keep others
            });
            
            if ($found) {
                $_SESSION['caremil_applied_coupons']['order'] = array_values($filtered);
                
                // Update backward compatible session
                if (empty($_SESSION['caremil_applied_coupons']['order']) && 
                    (!isset($_SESSION['caremil_applied_coupons']['shipping']) || $_SESSION['caremil_applied_coupons']['shipping'] === null)) {
                    unset($_SESSION['caremil_applied_coupon']);
                }
                
                wp_send_json_success(['message' => 'Đã gỡ mã ' . strtoupper($code) . '.']);
                return; // IMPORTANT: prevent fallthrough
            }
        } else {
            // Remove all order coupons
            if (!empty($_SESSION['caremil_applied_coupons']['order'])) {
                $_SESSION['caremil_applied_coupons']['order'] = [];
                
                // Update backward compatible session
                if (!isset($_SESSION['caremil_applied_coupons']['shipping']) || $_SESSION['caremil_applied_coupons']['shipping'] === null) {
                    unset($_SESSION['caremil_applied_coupon']);
                }
                
                wp_send_json_success(['message' => 'Đã gỡ tất cả mã giảm giá đơn hàng.']);
                return; // IMPORTANT: prevent fallthrough
            }
        }
    }
    
    // Only reach here if nothing was removed
    wp_send_json_error(['message' => 'Không tìm thấy mã để gỡ.']);
}
add_action('wp_ajax_caremil_remove_coupon_by_category', 'caremil_handle_remove_coupon_by_category');
add_action('wp_ajax_nopriv_caremil_remove_coupon_by_category', 'caremil_handle_remove_coupon_by_category');
