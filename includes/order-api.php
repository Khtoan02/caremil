<?php
/**
 * Create API for real order creation
 * 
 * @package Caremil
 */

// 1. Register AJAX Handler
add_action('wp_ajax_caremil_create_order', 'caremil_handle_create_order');
add_action('wp_ajax_nopriv_caremil_create_order', 'caremil_handle_create_order');

function caremil_handle_create_order() {
    // 1. Verify Nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'caremil_create_order_nonce')) {
        wp_send_json_error(['message' => 'Phiên làm việc hết hạn. Vui lòng tải lại trang.']);
    }

    // 2. Get Input Data
    $customer_data = isset($_POST['customer']) ? $_POST['customer'] : array();
    
    // FIX: Get cart directly from session for security and reliability, instead of relying on client-side POST
    if ( ! function_exists( 'caremil_get_cart' ) ) {
        // Fallback or include functions if needed, but normally functions.php is loaded
    }
    $cart_data = caremil_get_cart(); 
    
    $payment_method = isset($_POST['payment_method']) ? sanitize_text_field($_POST['payment_method']) : 'cod';
    $shop_id = caremil_get_pancake_shop_id(); // Use existing helper

    if (empty($shop_id)) {
        wp_send_json_error(['message' => 'Lỗi cấu hình shop ID.']);
    }

    // 3. Prepare Pancake Customer Payload
    // Pancake API expects 'customer' object inside the order payload or create customer first.
    // We will use the 'customer' field in order creation which is standard for POS.
    
    $pancake_customer = array(
        'name' => sanitize_text_field($customer_data['name']),
        'phone_number' => sanitize_text_field($customer_data['phone']),
        'address' => sanitize_text_field($customer_data['address']),
        'province_id' => isset($customer_data['province_id']) ? $customer_data['province_id'] : null,
        'district_id' => isset($customer_data['district_id']) ? $customer_data['district_id'] : null,
        'commune_id' => isset($customer_data['commune_id']) ? $customer_data['commune_id'] : null,
    );

    // 4. Prepare Cart Items
    $items = array();
    $total_price = 0;
    if (is_array($cart_data)) {
        foreach ($cart_data as $item) {
            $product_id = intval($item['product_id']);
            $quantity = intval($item['quantity']);
            
            // Get synced data
            $pancake_variation_id = get_post_meta($product_id, 'pancake_product_id', true);
            $price = get_post_meta($product_id, 'pancake_price_raw', true);
            if (!$price) $price = 0;

            $total_price += $price * $quantity;
            
            if ($pancake_variation_id) {
                $items[] = array(
                    'variation_id' => $pancake_variation_id,
                    'quantity' => $quantity,
                    'price' => (float)$price, // Fix: Cast to float/int
                );
            } else {
                // Nếu sản phẩm chưa đồng bộ, gửi dạng sản phẩm ngoài (custom)
                 $items[] = array(
                    'product_name' => get_the_title($product_id),
                    'quantity' => $quantity,
                    'price' => (float)$price, // Fix: Cast to float/int
                );
            }
        }
    }

    if (empty($items)) {
         wp_send_json_error(['message' => 'Giỏ hàng trống hoặc sản phẩm chưa được đồng bộ.']);
    }

    // 5. Construct Order Payload
    $warehouse_id = caremil_get_pancake_warehouse_id();
    
    // Auto-apply coupon from session (New Logic)
    $discount_amount = 0;
    $coupon_codes_applied = [];
    
    // Normalize session
    $applied_coupons = isset($_SESSION['caremil_applied_coupons']) ? $_SESSION['caremil_applied_coupons'] : ['shipping' => null, 'order' => []];
    
    // 1. Order Discounts
    if (!empty($applied_coupons['order']) && is_array($applied_coupons['order'])) {
        foreach ($applied_coupons['order'] as $c) {
            if (isset($c['discount'])) {
                $discount_amount += floatval($c['discount']);
                $coupon_codes_applied[] = $c['code'];
            }
        }
    }
    
    // 2. Shipping Discount (Freeship)
    // For Pancake, usually shipping fee is handled separately. 
    // If we handle freeship by discounting the order, add it here.
    // If we handled it by setting shipping fee to 0, we should ensure 'shipping_fee' field is 0. 
    // Here we assume discount is total discount.
    
    // Actually, in checkout.php we set shipping = 0.
    // In Pancake order payload, usually we send 'shipping_fee'.
    // But this payload doesn't seem to have 'shipping_fee' field explicit?
    // Let's check payload construction around line 109.
    // It seems we rely on Pancake to calc fee or we rely on 'discount_amount' to cover it?
    // Typically POS calculates shipping. If we want freeship, we might need to send a specific flag or discount equal to shipping.
    // For now, let's stick to 'discount_amount' logic. 
    // If freeship coupon implies 0 shipping fee, we might need to add a note or handle 'shipping_fee' if API supports it.
    
    if (isset($applied_coupons['shipping']) && $applied_coupons['shipping'] !== null) {
        $coupon_codes_applied[] = $applied_coupons['shipping']['code'] . " (Freeship)";
        // If we want to discount shipping fee value from total, we need to know the shipping fee here.
        // But shipping fee is dynamic or fixed 30k.
        // Let's assume for creating order, we just pass the discount note. The actual shipping fee manipulation 
        // depends on how Pancake handles it. 
        // If we want to force subtract 30k:
        // $discount_amount += 30000; 
        // But safer to just note it for now as per previous logic which ignored shipping fee calculation in API.
    }
    
    // Fallback for backward compatibility
    if ($discount_amount == 0 && isset($_SESSION['caremil_applied_coupon'])) {
        $coupon_id = $_SESSION['caremil_applied_coupon']['id'];
        $coupon_code = $_SESSION['caremil_applied_coupon']['code'];
        $discount_amount = caremil_calculate_discount($coupon_id, $total_price);
        $coupon_codes_applied[] = $coupon_code;
    }
    
    $coupon_code_str = implode(', ', $coupon_codes_applied);

    $final_total = $total_price - $discount_amount;
    if ($final_total < 0) $final_total = 0;

    // Calculate shipping fee via Viettel Post API
    $shipping_fee = 30000; // Default fallback
    $shipping_info = ['service' => 'Viettel Post', 'time' => '2-3 ngày'];
    
    // Get shipping info from customer address (from POST data)
    if (isset($customer_data['province_id']) && isset($customer_data['district_id'])) {
        $is_cod = ($payment_method === 'cod');
        $shipping_data = [
            'province_id' => $customer_data['province_id'],
            'district_id' => $customer_data['district_id']
        ];
        
        $shipping_info = caremil_get_shipping_info($shipping_data, $is_cod, $final_total);
        $shipping_fee = $shipping_info['fee'];
    }
    
    // Store original shipping fee for note
    $original_shipping_fee = $shipping_fee;
    
    // Check for freeship voucher
    $has_freeship = isset($applied_coupons['shipping']) && $applied_coupons['shipping'] !== null;
    if ($has_freeship) {
        // When freeship: customer doesn't pay shipping, but shop still pays carrier
        $shipping_fee_for_customer = 0;
        $shipping_fee_shop_pays = $original_shipping_fee; // Shop vẫn phải trả cho đơn vị vận chuyển
        $coupon_codes_applied[] = $applied_coupons['shipping']['code'] . " (Freeship - Khách tiết kiệm: " . number_format($original_shipping_fee) . "đ)";
    } else {
        $shipping_fee_for_customer = $shipping_fee;
        $shipping_fee_shop_pays = 0; // Customer pays shipping, shop doesn't
    }
    
    // Calculate grand total (what customer pays)
    $grand_total = $final_total + $shipping_fee_for_customer;
    if ($grand_total < 0) $grand_total = 0;
    
    // Calculate COD amount (what shipper collects from customer)
    $cod_amount = 0;
    if ($payment_method === 'cod') {
        $cod_amount = $grand_total; // Shipper thu của khách = Tổng đơn (đã - giảm giá, + ship nếu không freeship)
    }
    // Nếu online payment: cod_amount = 0 (shipper không thu)

    $payload = array(
        'shop_id' => intval($shop_id),
        'warehouse_id' => $warehouse_id,
        'items' => $items,
        'customer' => array(
            'name' => $customer_data['name'],
            'phone_number' => $customer_data['phone'],
        ),
        'shipping_address' => array(
             'full_name' => $customer_data['name'],
             'phone_number' => $customer_data['phone'],
             'address' => $customer_data['address'], 
        ),
        'note' => isset($customer_data['note']) ? sanitize_textarea_field($customer_data['note']) : '',
        // Financial fields
        'discount_amount' => $discount_amount,
        'shipping_fee' => $shipping_fee_for_customer, // Explicitly send shipping fee
        'total_amount' => $grand_total, // Send final total (some POS use this)
        'cod_amount' => $cod_amount,
    );
    
    // Add shipping fee info to note (not as separate field since Pancake might not support it)
    if ($has_freeship) {
        $payload['note'] .= " [FREESHIP - Khách không trả ship. Shop trả: " . number_format($original_shipping_fee) . "đ cho ĐVVC]";
    } else if ($shipping_fee > 0) {
        $payload['note'] .= " [Phí vận chuyển: " . number_format($shipping_fee) . "đ ({$shipping_info['service']} - {$shipping_info['time']})]";
        if ($payment_method === 'cod') {
            $payload['note'] .= " [COD đã bao gồm ship]";
        } else {
            $payload['note'] .= " [Khách đã TT online bao gồm ship]";
        }
    }
    
    // Add debug info to note for visibility during testing
    $payload['note'] .= " [DEBUG POS: COD={$cod_amount}, Ship={$shipping_fee_for_customer}, Discount={$discount_amount}]";
    
    if (!empty($coupon_codes_applied)) {
        $payload['note'] .= " [Mã giảm giá: $coupon_code_str, Tiết kiệm: " . number_format($discount_amount) . "đ]";
    }
    
    // DEBUG LOGS TO FILE
    $log_file = '/Applications/ServBay/www/dawnbridge/pancake_order_debug.log';
    $log_message = "--- PANCAKE CREATE ORDER START ---\n";
    $log_message .= "Time: " . date('Y-m-d H:i:s') . "\n";
    $log_message .= "Discount: $discount_amount (Codes: $coupon_code_str)\n";
    $log_message .= "Payload: " . json_encode($payload, JSON_UNESCAPED_UNICODE) . "\n";

    file_put_contents($log_file, $log_message, FILE_APPEND);

    // 6. Call API
    $response = caremil_pancake_request("/shops/{$shop_id}/orders", array(), 'POST', $payload);

    // DEBUG RESPONSE TO FILE
    $log_message = "Response: " . print_r($response, true) . "\n";
    $log_message .= "--- PANCAKE CREATE ORDER END ---\n\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);

    // 7. Handle Response
    if (is_array($response) && (isset($response['id']) || isset($response['inserted_at']) || isset($response['success']) && $response['success'] == true)) {
        // Pancake success check logic
        $order_id = $response['id'] ?? ($response['order']['id'] ?? '');
        $order_code = $response['order_number'] ?? ($response['order']['order_number'] ?? $order_id);
        
        if (!$order_id && isset($response['order_id'])) $order_id = $response['order_id']; // Handle rare case
        
        // Final fallback if ID exists but key is different
        if (!$order_id && isset($response['data']['id'])) $order_id = $response['data']['id'];

        if ($order_id) {
             // LOG COUPON USAGE FOR ALL APPLIED COUPONS
             if (!function_exists('caremil_log_coupon_usage')) {
                 require_once get_template_directory() . '/includes/coupons.php';
             }
             
             // Use session phone or payload phone
             $user_phone = isset($_SESSION['pancake_phone']) ? $_SESSION['pancake_phone'] : $customer_data['phone'];

             foreach ($coupon_codes_applied as $code_item) {
                 // Clean up code string (remove " (Freeship)" suffix for log)
                 $clean_code = explode(' ', $code_item)[0];
                 caremil_log_coupon_usage($clean_code, $user_phone, $order_id, $final_total);
             }
                 
             // Clear all coupon sessions
             unset($_SESSION['caremil_applied_coupon']);
             unset($_SESSION['caremil_applied_coupons']);

             wp_send_json_success(array(
                'message' => 'Tạo đơn hàng thành công',
                'order_id' => $order_id,
                'order_code' => $order_code,
                'total' => $final_total
            ));
        } else {
             // Response OK but no ID found? Treat as error to inspect log
             wp_send_json_error(['message' => 'Lỗi: Đơn hàng tạo thành công nhưng không tìm thấy ID. Vui lòng kiểm tra log.']);
        }

    } else {
        // Error
        $error_msg = isset($response['message']) ? $response['message'] : 'Lỗi không xác định từ Pancake POS.';
        if (isset($response['errors'])) {
             $error_msg .= ' ' . json_encode($response['errors']);
        }
        
        // Trả về dữ liệu debug cho frontend
        wp_send_json_error([
            'message' => 'Không thể tạo đơn hàng: ' . $error_msg,
            'debug_data' => [
                'payload' => $payload,
                'response' => $response
            ]
        ]);
    }
}
