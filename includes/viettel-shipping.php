<?php
/**
 * Viettel Post Shipping Calculator
 * API v2 Integration for realtime shipping fee calculation
 * 
 * @package Caremil
 */

if (!defined('ABSPATH')) exit;

// Define token constant if not already defined
if (!defined('CAREMIL_VTP_TOKEN')) {
    define('CAREMIL_VTP_TOKEN', 'eyJhbGciOiJFUzI1NiJ9.eyJzdWIiOiIwMzI5MjQ5NTM2IiwiVXNlcklkIjoxMTg0MzI4NSwiRnJvbVNvdXJjZSI6NSwiVG9rZW4iOiJNWERIS1ZJMDdXNUszODhCUyIsImV4cCI6MTc2NzI1NDIyNiwiUGFydG5lciI6MTE4NDMyODV9.q-qzfQYG07y7QdAZxgqAaVccnLaKo3ubrrw2ji3tHmWRmUEj3RdSmftyLXol26rxM7yRI2s9wf3nt7UlTS50kA');
}

// Shop default location (Hà Nội - Cầu Giấy)
if (!defined('CAREMIL_SHOP_PROVINCE_ID')) {
    define('CAREMIL_SHOP_PROVINCE_ID', 1); // Hà Nội
}
if (!defined('CAREMIL_SHOP_DISTRICT_ID')) {
    define('CAREMIL_SHOP_DISTRICT_ID', 5); // Cầu Giấy
}

/**
 * Normalizes string for comparison (lowercase, remove accents and common prefixes)
 */
function caremil_vtp_normalize_name($str) {
    if (!$str) return '';
    $str = mb_strtolower($str, 'UTF-8');
    
    // Remove accents
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
    $str = preg_replace("/(đ)/", 'd', $str);
    
    // Remove common prefixes
    $prefixes = ['tinh ', 'thanh pho ', 'tp. ', 'tp ', 'quan ', 'huyen ', 'thi xa ', 'tx. ', 'phuong ', 'xa ', 'tt. ', 'thiet '];
    foreach ($prefixes as $prefix) {
        if (strpos($str, $prefix) === 0) {
            $str = substr($str, strlen($prefix));
            break; 
        }
    }
    
    return trim($str);
}

/**
 * Get Viettel Post Provinces
 */
function caremil_vtp_get_provinces() {
    $cache_key = 'caremil_vtp_provinces_v2';
    $cached = get_transient($cache_key);
    if ($cached) return $cached;

    $response = wp_remote_get('https://partner.viettelpost.vn/v2/categories/listProvince', [
        'headers' => ['Token' => CAREMIL_VTP_TOKEN],
        'timeout' => 10
    ]);

    if (is_wp_error($response)) return [];

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['status']) && $data['status'] === 200 && !empty($data['data'])) {
        set_transient($cache_key, $data['data'], WEEK_IN_SECONDS);
        return $data['data'];
    }
    return [];
}

/**
 * Get Viettel Post Districts
 */
function caremil_vtp_get_districts($province_id) {
    $cache_key = 'caremil_vtp_districts_' . $province_id;
    $cached = get_transient($cache_key);
    if ($cached) return $cached;

    $response = wp_remote_get('https://partner.viettelpost.vn/v2/categories/listDistrict?provinceId=' . $province_id, [
        'headers' => ['Token' => CAREMIL_VTP_TOKEN],
        'timeout' => 10
    ]);

    if (is_wp_error($response)) return [];

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['status']) && $data['status'] === 200 && !empty($data['data'])) {
        set_transient($cache_key, $data['data'], WEEK_IN_SECONDS);
        return $data['data'];
    }
    return [];
}

/**
 * Find ID by Name
 */
function caremil_vtp_find_id_by_name($list, $name, $id_key = 'PROVINCE_ID', $name_key = 'PROVINCE_NAME') {
    $norm_name = caremil_vtp_normalize_name($name);
    
    // Direct match first
    foreach ($list as $item) {
        if (caremil_vtp_normalize_name($item[$name_key]) === $norm_name) {
            return $item[$id_key];
        }
    }
    
    // Loose match
    foreach ($list as $item) {
        $item_norm = caremil_vtp_normalize_name($item[$name_key]);
        if (strpos($item_norm, $norm_name) !== false || strpos($norm_name, $item_norm) !== false) {
            return $item[$id_key];
        }
    }
    
    return null;
}

/**
 * Calculate shipping fee from Viettel Post API
 */
function caremil_vtp_calculate_shipping($receiver_province_name, $receiver_district_name, $weight = 1000, $cod_amount = 0, $product_value = 0) {
    
    // 1. Map Province
    $provinces = caremil_vtp_get_provinces();
    $province_id = caremil_vtp_find_id_by_name($provinces, $receiver_province_name, 'PROVINCE_ID', 'PROVINCE_NAME');

    if (!$province_id) {
        error_log("VTP Mapping Error: Could not find province '$receiver_province_name'");
        return false;
    }

    // 2. Map District
    $districts = caremil_vtp_get_districts($province_id);
    $district_id = caremil_vtp_find_id_by_name($districts, $receiver_district_name, 'DISTRICT_ID', 'DISTRICT_NAME');

    if (!$district_id) {
        error_log("VTP Mapping Error: Could not find district '$receiver_district_name' in province ID $province_id");
        return false;
    }

    // Validate numeric inputs
    $weight = max(100, intval($weight)); // Minimum 100g
    $cod_amount = max(0, intval($cod_amount));
    $product_value = max(0, intval($product_value));
    
    // Try to get from cache (1 hour)
    $cache_key = 'vtp_shipping_v3_' . md5(json_encode([
        $province_id,
        $district_id,
        $weight,
        $cod_amount
    ]));
    
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }
    
    // Build API request
    $api_url = 'https://partner.viettelpost.vn/v2/order/getPriceAll';
    $payload = [
        'SENDER_PROVINCE' => CAREMIL_SHOP_PROVINCE_ID,
        'SENDER_DISTRICT' => CAREMIL_SHOP_DISTRICT_ID,
        'RECEIVER_PROVINCE' => $province_id,
        'RECEIVER_DISTRICT' => $district_id,
        'PRODUCT_TYPE' => 'HH', // Hàng hóa
        'PRODUCT_WEIGHT' => $weight,
        'PRODUCT_PRICE' => $product_value,
        'MONEY_COLLECTION' => (string)$cod_amount,
        'PRODUCT_LENGTH' => 20,
        'PRODUCT_WIDTH' => 20,
        'PRODUCT_HEIGHT' => 10,
        'TYPE' => 1
    ];
    
    $response = wp_remote_post($api_url, [
        'headers' => [
            'Content-Type' => 'application/json',
            'Token' => CAREMIL_VTP_TOKEN
        ],
        'body' => json_encode($payload),
        'timeout' => 10
    ]);
    
    if (is_wp_error($response)) {
        error_log('VTP API Error: ' . $response->get_error_message());
        return false;
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (is_array($data) && !empty($data)) {
        // Find best service (prefer VCN) or just take the cheapest/first
        $result = [
            'fee' => intval($data[0]['GIA_CUOC']),
            'service' => $data[0]['TEN_DICHVU'],
            'time' => $data[0]['THOI_GIAN'],
            'service_code' => $data[0]['MA_DV_CHINH'],
            'source' => 'api'
        ];
        
        set_transient($cache_key, $result, HOUR_IN_SECONDS);
        return $result;
    }
    
    return false;
}

/**
 * Get fallback shipping fee based on province name (approximate)
 */
function caremil_vtp_get_fallback_fee($province_name) {
    $norm = caremil_vtp_normalize_name($province_name);
    
    if (strpos($norm, 'ha noi') !== false) return 30000;
    if (strpos($norm, 'ho chi minh') !== false) return 35000;
    if (strpos($norm, 'da nang') !== false) return 40000;
    
    return 45000;
}

/**
 * Calculate total cart weight in grams
 */
function caremil_calculate_cart_weight() {
    $cart = isset($_SESSION['caremil_cart']) ? $_SESSION['caremil_cart'] : [];
    $total_weight = 0;
    
    if (empty($cart)) {
        return 500; // Default 500g
    }
    
    foreach ($cart as $item) {
        $product_id = $item['product_id'];
        $quantity = intval($item['quantity']);
        $weight = get_post_meta($product_id, 'product_weight', true);
        if (empty($weight)) $weight = 500;
        $total_weight += intval($weight) * $quantity;
    }
    
    return max(100, $total_weight);
}

/**
 * Get shipping info for checkout display
 */
function caremil_get_shipping_info($customer_data, $is_cod = true, $order_total = 0) {
    // We prefer Name for mapping
    $province_name = isset($customer_data['province_name']) ? $customer_data['province_name'] : '';
    $district_name = isset($customer_data['district_name']) ? $customer_data['district_name'] : '';
    
    // Check if we have IDs but not names (legacy support or partial data)
    // If we only have IDs from Pancake, we CANT find VTP IDs easily without names.
    // So enforcing Names is crucial.
    
    if (empty($province_name) || empty($district_name)) {
        return [
            'fee' => 0, // Don't show confusing default fee
            'service' => 'Viettel Post',
            'time' => '...',
            'source' => 'waiting'
        ];
    }
    
    $weight = caremil_calculate_cart_weight();
    $cod_amount = $is_cod ? $order_total : 0;
    $product_value = $order_total;
    
    // Try API
    $api_result = caremil_vtp_calculate_shipping(
        $province_name,
        $district_name,
        $weight,
        $cod_amount,
        $product_value
    );
    
    if ($api_result !== false) {
        return $api_result;
    }
    
    // Fallback
    return [
        'fee' => caremil_vtp_get_fallback_fee($province_name),
        'service' => 'Viettel Post',
        'time' => '3-5 ngày',
        'source' => 'fallback'
    ];
}

/**
 * AJAX Handler: Calculate shipping fee
 */
function caremil_ajax_calculate_shipping() {
    $province_name = isset($_POST['province_name']) ? sanitize_text_field($_POST['province_name']) : '';
    $district_name = isset($_POST['district_name']) ? sanitize_text_field($_POST['district_name']) : '';
    $is_cod = isset($_POST['is_cod']) ? ($_POST['is_cod'] === 'true') : true;
    
    // Optional: Accept IDs but we rely on names
    $province_id = isset($_POST['province_id']) ? sanitize_text_field($_POST['province_id']) : '';
    $district_id = isset($_POST['district_id']) ? sanitize_text_field($_POST['district_id']) : '';

    if (empty($province_name) || empty($district_name)) {
        wp_send_json_error(['message' => 'Thiếu thông tin địa chỉ']);
    }
    
    // Store customer data in session
    $_SESSION['checkout_customer_data'] = [
        'province_id' => $province_id, // Keep for Pancake POS order
        'district_id' => $district_id,
        'province_name' => $province_name, // Important for Shipping Calc
        'district_name' => $district_name
    ];
    
    $cart_total = caremil_get_cart_total();
    $shipping_info = caremil_get_shipping_info($_SESSION['checkout_customer_data'], $is_cod, $cart_total);
    
    wp_send_json_success([
        'fee' => $shipping_info['fee'],
        'fee_formatted' => number_format($shipping_info['fee']) . 'đ',
        'service' => $shipping_info['service'],
        'time' => $shipping_info['time'],
        'source' => $shipping_info['source']
    ]);
}
add_action('wp_ajax_caremil_calculate_shipping', 'caremil_ajax_calculate_shipping');
add_action('wp_ajax_nopriv_caremil_calculate_shipping', 'caremil_ajax_calculate_shipping');
