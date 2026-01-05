<?php
/**
 * Template Name: User Account
 * Template Post Type: page
 * Description: Template for displaying user account page
 *
 * @package Caremil
 */

// BẮT BUỘC: kiểm tra đăng nhập (dùng session từ login.php)
if (!session_id()) {
    session_start();
}

// Cấu hình URL chuyển hướng
$caremil_login_url   = home_url('/dang-nhap');        // đổi nếu slug khác
$caremil_account_url = home_url('/tai-khoan-cua-toi'); // trang tài khoản này

// Xử lý đăng xuất (clear session rồi chuyển về trang đăng nhập)
if (isset($_GET['caremil_logout']) && $_GET['caremil_logout'] === '1') {
    // Xóa các biến session liên quan đến đăng nhập Pancake
    unset(
        $_SESSION['pancake_logged_in'],
        $_SESSION['pancake_phone'],
        $_SESSION['pancake_name'],
        $_SESSION['pancake_customer_id']
    );

    // Regenerate session ID để tăng bảo mật
    if (function_exists('session_regenerate_id')) {
        @session_regenerate_id(true);
    }

    wp_redirect($caremil_login_url);
    exit;
}

// Chưa đăng nhập thì chuyển về trang login
if (empty($_SESSION['pancake_logged_in'])) {
    wp_redirect($caremil_login_url);
    exit;
}

// Kiểm tra kết nối Pancake trước khi xem tài khoản
if (function_exists('caremil_require_pancake_connection')) {
    caremil_require_pancake_connection();
}

// ====== CẤU HÌNH PANCAKE ======
// Sử dụng helper functions từ functions.php để lấy config từ admin settings
// Hàm caremil_pancake_request() đã được định nghĩa trong functions.php

get_header();

/**
 * Định dạng tiền tệ VNĐ.
 */
if (!function_exists('caremil_format_currency')) {
    function caremil_format_currency($amount) {
        $amount = floatval($amount);
        return number_format($amount, 0, ',', '.') . 'đ';
    }
}

/**
 * Chuẩn hóa SĐT về dạng đầu 0.
 */
if (!function_exists('caremil_normalize_phone')) {
    function caremil_normalize_phone($phone) {
        $clean = preg_replace('/[^0-9+]/', '', $phone ?? '');
        if (strpos($clean, '+84') === 0) {
            return '0' . substr($clean, 3);
        }
        if (strpos($clean, '84') === 0) {
            return '0' . substr($clean, 2);
        }
        return $clean;
    }
}

// Lấy thông tin khách & đơn hàng theo session
$caremil_customer_id = isset($_SESSION['pancake_customer_id']) ? sanitize_text_field($_SESSION['pancake_customer_id']) : '';
$caremil_phone       = isset($_SESSION['pancake_phone']) ? sanitize_text_field($_SESSION['pancake_phone']) : '';

// Xử lý cập nhật thông tin cá nhân (PUT lên Pancake)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $caremil_customer_id && empty($_POST['caremil_addr_action'])) {
    $new_name      = isset($_POST['caremil_name']) ? sanitize_text_field($_POST['caremil_name']) : '';
    $new_phone     = isset($_POST['caremil_phone']) ? sanitize_text_field($_POST['caremil_phone']) : '';
    $new_email     = isset($_POST['caremil_email']) ? sanitize_email($_POST['caremil_email']) : '';
    $new_birthday  = isset($_POST['caremil_birthday']) ? sanitize_text_field($_POST['caremil_birthday']) : '';

    // Chuẩn bị payload theo kiểu Pancake
    $payload = array('customer' => array());
    if ($new_name) {
        $payload['customer']['name'] = $new_name;
    }
    if ($new_phone) {
        // Pancake thường dùng phoneNumber
        $payload['customer']['phoneNumber'] = $new_phone;
    }
    if ($new_email) {
        $payload['customer']['emails'] = array($new_email);
    }
    if ($new_birthday) {
        // Gửi dạng yyyy-mm-dd
        $payload['customer']['birthday'] = $new_birthday;
        $payload['customer']['date_of_birth'] = $new_birthday;
    }

    if (!empty($payload['customer'])) {
        caremil_pancake_request("/shops/" . caremil_get_pancake_shop_id() . "/customers/{$caremil_customer_id}", array(), 'PUT', $payload);
        // Cập nhật session để hiển thị ngay
        if ($new_name) {
            $_SESSION['pancake_name'] = $new_name;
        }
        if ($new_phone) {
            $_SESSION['pancake_phone'] = $new_phone;
        }
    }
}

// CRUD địa chỉ (hỗ trợ xóa, AJAX không reload)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $caremil_customer_id && isset($_POST['caremil_addr_action'])) {
    $addr_action = sanitize_text_field($_POST['caremil_addr_action']);
    $addr_id     = isset($_POST['caremil_addr_id']) ? sanitize_text_field($_POST['caremil_addr_id']) : '';
    $addr_name   = isset($_POST['caremil_addr_name']) ? sanitize_text_field($_POST['caremil_addr_name']) : '';
    $addr_phone  = isset($_POST['caremil_addr_phone']) ? sanitize_text_field($_POST['caremil_addr_phone']) : '';
    $addr_full   = isset($_POST['caremil_addr_full']) ? sanitize_text_field($_POST['caremil_addr_full']) : '';
    $addr_province_id = isset($_POST['caremil_addr_province_id']) ? sanitize_text_field($_POST['caremil_addr_province_id']) : '';
    $addr_district_id = isset($_POST['caremil_addr_district_id']) ? sanitize_text_field($_POST['caremil_addr_district_id']) : '';
    $addr_commune_id  = isset($_POST['caremil_addr_commune_id']) ? sanitize_text_field($_POST['caremil_addr_commune_id']) : '';

    // Lấy danh sách địa chỉ hiện tại
    $existing_customer = caremil_pancake_request("/shops/" . caremil_get_pancake_shop_id() . "/customers/{$caremil_customer_id}");
    $addr_list = array();
    if (is_array($existing_customer)) {
        if (!empty($existing_customer['shop_customer_addresses']) && is_array($existing_customer['shop_customer_addresses'])) {
            $addr_list = $existing_customer['shop_customer_addresses'];
        } elseif (!empty($existing_customer['shop_customer_address']) && is_array($existing_customer['shop_customer_address'])) {
            $addr_list = $existing_customer['shop_customer_address'];
        }
    }

    // Chuẩn hóa item
    $addr_payload_item = array(
        'full_name'    => $addr_name,
        'phone_number' => $addr_phone,
        'full_address' => $addr_full,
        'address'      => $addr_full,
        'province_id'  => $addr_province_id,
        'district_id'  => $addr_district_id,
        'commune_id'   => $addr_commune_id,
        'country_code' => '84',
    );

    $skip_put = false;

    $delete_success = null;

    if ($addr_action === 'add') {
        $addr_list[] = $addr_payload_item;
    } elseif ($addr_action === 'update' && $addr_id) {
        foreach ($addr_list as $idx => $item) {
            if (isset($item['id']) && (string)$item['id'] === (string)$addr_id) {
                $addr_payload_item['id'] = $item['id']; // giữ id cũ
                $addr_list[$idx] = array_merge($item, $addr_payload_item);
                break;
            }
        }
    } elseif ($addr_action === 'delete' && $addr_id) {
        // Xóa mềm với _destroy
        $delete_payload = array(
            'customer' => array(
                'shop_customer_address' => array(
                    array(
                        'id' => $addr_id,
                        '_destroy' => true,
                    ),
                ),
            ),
        );
        $delete_res = caremil_pancake_request("/shops/" . caremil_get_pancake_shop_id() . "/customers/{$caremil_customer_id}", array(), 'PUT', $delete_payload);
        $skip_put = true; // không gửi full list nữa
        // Refetch sau khi xóa để xác nhận
        $after_delete = caremil_pancake_request("/shops/" . caremil_get_pancake_shop_id() . "/customers/{$caremil_customer_id}");
        $addr_list = array();
        if (is_array($after_delete)) {
            if (!empty($after_delete['shop_customer_addresses']) && is_array($after_delete['shop_customer_addresses'])) {
                $addr_list = $after_delete['shop_customer_addresses'];
            } elseif (!empty($after_delete['shop_customer_address']) && is_array($after_delete['shop_customer_address'])) {
                $addr_list = $after_delete['shop_customer_address'];
            }
        }
        // Kiểm tra đã xóa chưa
        $still_exists = false;
        foreach ($addr_list as $a) {
            if (isset($a['id']) && (string)$a['id'] === (string)$addr_id) {
                $still_exists = true;
                break;
            }
        }
        $delete_success = !$still_exists;
        $caremil_customer = $after_delete;
    }

    if (!$skip_put) {
        // PUT lên Pancake (gửi cả key số nhiều và số ít để tương thích)
        caremil_pancake_request("/shops/" . caremil_get_pancake_shop_id() . "/customers/{$caremil_customer_id}", array(), 'PUT', array(
            'customer' => array(
                'shop_customer_address'  => $addr_list,
                'shop_customer_addresses'=> $addr_list,
            ),
        ));
    }

    // Refetch để đồng bộ UI
    $caremil_customer = caremil_pancake_request("/shops/" . caremil_get_pancake_shop_id() . "/customers/{$caremil_customer_id}");

    // Nếu là AJAX (có caremil_addr_ajax), trả JSON và exit để tránh reload
    if (isset($_POST['caremil_addr_ajax'])) {
        $addrs = array();
        if (!empty($caremil_customer['shop_customer_addresses']) && is_array($caremil_customer['shop_customer_addresses'])) {
            $addrs = $caremil_customer['shop_customer_addresses'];
        } elseif (!empty($caremil_customer['shop_customer_address']) && is_array($caremil_customer['shop_customer_address'])) {
            $addrs = $caremil_customer['shop_customer_address'];
        }
        $success_flag = $delete_success !== null ? $delete_success : true;
        header('Content-Type: application/json');
        echo wp_json_encode(array(
            'success'   => $success_flag,
            'addresses' => $addrs,
            'message'   => $success_flag ? '' : 'Xóa địa chỉ không thành công từ POS'
        ));
        exit;
    }
}

if ($caremil_customer_id) {
    if (empty($caremil_customer)) {
        $caremil_customer = caremil_pancake_request("/shops/" . caremil_get_pancake_shop_id() . "/customers/{$caremil_customer_id}");
    }
    $caremil_orders   = caremil_pancake_request("/shops/" . caremil_get_pancake_shop_id() . "/customers/{$caremil_customer_id}/orders", array('limit' => 5));
} elseif ($caremil_phone) {
    // Fallback: tìm theo SĐT nếu chưa có ID
    $caremil_customer = caremil_pancake_request("/shops/" . caremil_get_pancake_shop_id() . "/customers", array('search' => $caremil_phone, 'page_size' => 10));
    $caremil_customer = is_array($caremil_customer) && isset($caremil_customer['data'][0]) ? $caremil_customer['data'][0] : $caremil_customer;
    $cid = $caremil_customer['id'] ?? '';
    if ($cid) {
        // Đồng bộ lại customer_id theo đúng khách tìm được từ SĐT
        $caremil_customer_id = (string) $cid;
        $_SESSION['pancake_customer_id'] = $caremil_customer_id;
        $caremil_orders = caremil_pancake_request("/shops/" . caremil_get_pancake_shop_id() . "/customers/{$caremil_customer_id}/orders", array('limit' => 5));
    } else {
        $caremil_orders = array();
    }
} else {
    $caremil_customer = array();
    $caremil_orders   = array();
}

// Nếu thiếu email/dob hoặc sai phone, tìm kỹ theo SĐT để lấy đúng bản ghi
$caremil_normalized_phone = caremil_normalize_phone($caremil_phone);
$caremil_customer_search  = caremil_pancake_request("/shops/" . caremil_get_pancake_shop_id() . "/customers", array('search' => $caremil_phone, 'page_size' => 10));
$caremil_found_match = false;
if (is_array($caremil_customer_search) && isset($caremil_customer_search['data'])) {
    foreach ($caremil_customer_search['data'] as $candidate) {
        $phones = array();
        if (!empty($candidate['phone_numbers']) && is_array($candidate['phone_numbers'])) {
            $phones = array_merge($phones, $candidate['phone_numbers']);
        }
        if (!empty($candidate['phone_number'])) {
            $phones[] = $candidate['phone_number'];
        }
        $match = false;
        foreach ($phones as $p) {
            if (caremil_normalize_phone($p) === $caremil_normalized_phone) {
                $match = true;
                break;
            }
        }
        if ($match) {
            // ưu tiên dữ liệu đầy đủ nhất, đảm bảo khớp đúng SĐT đăng nhập
            $caremil_customer    = $candidate;
            $caremil_found_match = true;

            // Đồng bộ lại customer_id đúng với khách khớp SĐT
            if (!empty($candidate['id'])) {
                $caremil_customer_id = (string) $candidate['id'];
                $_SESSION['pancake_customer_id'] = $caremil_customer_id;
            }

            // lấy thêm orders theo SĐT nếu chưa có
            if (empty($caremil_orders)) {
                $orders_by_phone = caremil_pancake_request("/shops/" . caremil_get_pancake_shop_id() . "/orders", array('search' => $caremil_phone, 'page_size' => 50));
                if (is_array($orders_by_phone) && isset($orders_by_phone['data'])) {
                    // lọc đúng sđt
                    $filtered = array();
                    foreach ($orders_by_phone['data'] as $o) {
                        $bill_phone = caremil_normalize_phone($o['bill_phone_number'] ?? '');
                        $ship_phone = caremil_normalize_phone($o['shipping_address']['phone_number'] ?? '');
                        if ($bill_phone === $caremil_normalized_phone || $ship_phone === $caremil_normalized_phone) {
                            $filtered[] = $o;
                        }
                    }
                    $caremil_orders = array('data' => $filtered);
                }
            }
            break;
        }
    }
}

// Nếu không tìm được bản ghi nào khớp chính xác với SĐT đăng nhập
// thì không dùng lại ID/dữ liệu cũ (tránh đổ nhầm sang khách khác – ví dụ 0329249536)
if ($caremil_phone && !$caremil_found_match) {
    $caremil_customer     = array();
    $caremil_orders       = array();
    $caremil_customer_id  = '';
    unset($_SESSION['pancake_customer_id']);
}

// Chuẩn hóa dữ liệu hiển thị
$caremil_orders_list = array();
if (is_array($caremil_orders)) {
    // Một số API trả về dạng ['data' => [...]], fallback nếu không.
    $caremil_orders_list = $caremil_orders['data'] ?? $caremil_orders;
}

// Lấy địa chỉ
$caremil_addresses = array();
if (is_array($caremil_customer)) {
    if (!empty($caremil_customer['shop_customer_addresses']) && is_array($caremil_customer['shop_customer_addresses'])) {
        $caremil_addresses = $caremil_customer['shop_customer_addresses'];
    } elseif (!empty($caremil_customer['shop_customer_address']) && is_array($caremil_customer['shop_customer_address'])) {
        $caremil_addresses = $caremil_customer['shop_customer_address'];
    }
}

$caremil_order_count = isset($caremil_customer['orders_count']) ? intval($caremil_customer['orders_count']) : (is_array($caremil_orders_list) ? count($caremil_orders_list) : 0);
// Lấy tổng chi tiêu từ nhiều field có thể có
$caremil_total_spent = 0;
if (isset($caremil_customer['purchased_amount'])) {
    $caremil_total_spent = floatval($caremil_customer['purchased_amount']);
} elseif (isset($caremil_customer['total_spent'])) {
    $caremil_total_spent = floatval($caremil_customer['total_spent']);
} elseif (isset($caremil_customer['total_purchased'])) {
    $caremil_total_spent = floatval($caremil_customer['total_purchased']);
}
// Lấy điểm từ nhiều field có thể có
$caremil_points = 0;
if (isset($caremil_customer['reward_point'])) {
    $caremil_points = intval($caremil_customer['reward_point']);
} elseif (isset($caremil_customer['points'])) {
    $caremil_points = intval($caremil_customer['points']);
} elseif (isset($caremil_customer['reward_points'])) {
    $caremil_points = intval($caremil_customer['reward_points']);
} elseif (isset($caremil_customer['point_balance'])) {
    $caremil_points = intval($caremil_customer['point_balance']);
}

// Đơn gần nhất
$caremil_latest_order = !empty($caremil_orders_list) ? $caremil_orders_list[0] : null;

// Thông tin hiển thị user
$caremil_display_name  = '';
$caremil_display_phone = '';
if (!empty($caremil_customer['name'])) {
    $caremil_display_name = $caremil_customer['name'];
} elseif (!empty($_SESSION['pancake_name'])) {
    $caremil_display_name = sanitize_text_field($_SESSION['pancake_name']);
}

// Các key phone có thể là phoneNumber hoặc phone
if (!empty($caremil_customer['phoneNumber'])) {
    $caremil_display_phone = $caremil_customer['phoneNumber'];
} elseif (!empty($caremil_customer['phone'])) {
    $caremil_display_phone = $caremil_customer['phone'];
} elseif (!empty($_SESSION['pancake_phone'])) {
    $caremil_display_phone = sanitize_text_field($_SESSION['pancake_phone']);
}

// Email & ngày sinh
$caremil_email = '';
if (!empty($caremil_customer['emails']) && is_array($caremil_customer['emails'])) {
    $caremil_email = $caremil_customer['emails'][0];
} elseif (!empty($caremil_customer['email'])) {
    $caremil_email = $caremil_customer['email'];
} elseif (!empty($caremil_customer['primaryEmail'])) {
    $caremil_email = $caremil_customer['primaryEmail'];
} elseif (!empty($caremil_customer['contactEmails']) && is_array($caremil_customer['contactEmails'])) {
    $caremil_email = $caremil_customer['contactEmails'][0];
} elseif (!empty($caremil_customer['emailList']) && is_array($caremil_customer['emailList'])) {
    $caremil_email = $caremil_customer['emailList'][0];
} elseif (!empty($caremil_customer['email_list']) && is_array($caremil_customer['email_list'])) {
    $caremil_email = $caremil_customer['email_list'][0];
}

$caremil_birthday_raw = '';
if (!empty($caremil_customer['birthday'])) {
    $caremil_birthday_raw = $caremil_customer['birthday'];
} elseif (!empty($caremil_customer['birthDate'])) {
    $caremil_birthday_raw = $caremil_customer['birthDate'];
} elseif (!empty($caremil_customer['dob'])) {
    $caremil_birthday_raw = $caremil_customer['dob'];
} elseif (!empty($caremil_customer['birth_date'])) {
    $caremil_birthday_raw = $caremil_customer['birth_date'];
} elseif (!empty($caremil_customer['dateOfBirth'])) {
    $caremil_birthday_raw = $caremil_customer['dateOfBirth'];
} elseif (!empty($caremil_customer['date_of_birth'])) {
    $caremil_birthday_raw = $caremil_customer['date_of_birth'];
} elseif (!empty($caremil_customer['birthday_at'])) {
    $caremil_birthday_raw = $caremil_customer['birthday_at'];
}

$caremil_birthday = '';
if ($caremil_birthday_raw) {
    $ts = strtotime($caremil_birthday_raw);
    if ($ts) {
        $caremil_birthday = date('Y-m-d', $ts); // định dạng cho input type date
    }
}
?>
<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài Khoản Của Tôi - CareMIL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;700;800&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                            green: '#4ade80'
                        }
                    },
                    fontFamily: {
                        sans: ['Quicksand', 'sans-serif'],
                        display: ['Baloo 2', 'cursive'],
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(76, 201, 240, 0.2)',
                        'card': '0 4px 15px rgba(0,0,0,0.05)',
                    }
                }
            }
        }
    </script>
    <style>
        /* Removed padding-top: 80px since header is gone */
        body { background-color: #f8fafc; }
        
        /* Sidebar Navigation Active State */
        .nav-item { transition: all 0.3s ease; border-left: 4px solid transparent; }
        .nav-item.active {
            background-color: #f0f9ff;
            color: #1a4f8a;
            border-left-color: #4cc9f0;
            font-weight: 700;
        }
        .nav-item:hover:not(.active) { background-color: #f8fafc; color: #1a4f8a; }
        .nav-item i { width: 24px; text-align: center; }

        /* Content Transition */
        .tab-panel { display: none; animation: fadeIn 0.4s ease-out; }
        .tab-panel.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* Voucher Ticket Style */
        .voucher-card {
            background-image: radial-gradient(circle at 0 50%, transparent 10px, white 11px), radial-gradient(circle at 100% 50%, transparent 10px, white 11px);
            background-position: 0 0, 0 0;
            background-size: 100% 100%;
            mask-image: radial-gradient(circle at 0 50%, transparent 10px, black 11px), radial-gradient(circle at 100% 50%, transparent 10px, black 11px);
            -webkit-mask-image: radial-gradient(circle at 0 50%, transparent 10px, black 11px), radial-gradient(circle at 100% 50%, transparent 10px, black 11px);
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05));
        }

        /* Order Status Badge Colors */
        .status-processing { background-color: #e0f2fe; color: #0284c7; }
        .status-shipping { background-color: #fef9c3; color: #854d0e; }
        .status-completed { background-color: #dcfce7; color: #166534; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body class="bg-gray-50 text-gray-700 font-sans">

    <!-- REMOVED HEADER COMPONENT -->

    <!-- MAIN DASHBOARD -->
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- LEFT: SIDEBAR NAVIGATION -->
            <div class="lg:w-1/4 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-card border border-gray-100 overflow-hidden sticky top-4"> <!-- Adjusted sticky top -->
                    <!-- User Info Block -->
                    <div class="p-6 border-b border-gray-100 flex items-center gap-4 bg-gradient-to-r from-blue-50 to-white">
                        <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center border-2 border-brand-soft shadow-inner text-2xl text-brand-pink">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-brand-navy text-lg leading-tight">
                                <?php echo esc_html($caremil_display_name ?: 'Khách hàng'); ?>
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">
                                <?php echo esc_html($caremil_display_phone ?: ''); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Menu Items -->
                    <nav class="py-2">
                        <button onclick="switchTab('dashboard')" id="nav-dashboard" class="nav-item active w-full text-left px-6 py-3.5 text-sm font-medium text-gray-500 flex items-center gap-3">
                            <i class="fas fa-home"></i> Tổng Quan
                        </button>
                        <button onclick="switchTab('orders')" id="nav-orders" class="nav-item w-full text-left px-6 py-3.5 text-sm font-medium text-gray-500 flex items-center gap-3">
                            <i class="fas fa-file-invoice"></i> Đơn Hàng Của Tôi
                        </button>
                        <button onclick="switchTab('address')" id="nav-address" class="nav-item w-full text-left px-6 py-3.5 text-sm font-medium text-gray-500 flex items-center gap-3">
                            <i class="fas fa-map-marker-alt"></i> Sổ Địa Chỉ
                        </button>
                        <button onclick="switchTab('vouchers')" id="nav-vouchers" class="nav-item w-full text-left px-6 py-3.5 text-sm font-medium text-gray-500 flex items-center gap-3">
                            <i class="fas fa-ticket-alt"></i> Kho Ưu Đãi <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full ml-auto font-bold">2</span>
                        </button>
                        <button onclick="switchTab('profile')" id="nav-profile" class="nav-item w-full text-left px-6 py-3.5 text-sm font-medium text-gray-500 flex items-center gap-3">
                            <i class="fas fa-user-cog"></i> Thông Tin Cá Nhân
                        </button>
                        <div class="my-2 border-t border-gray-100"></div>
                        <a href="<?php echo esc_url( add_query_arg( 'caremil_logout', '1', $caremil_account_url ) ); ?>" class="w-full text-left px-6 py-3.5 text-sm font-bold text-red-500 hover:bg-red-50 flex items-center gap-3 transition">
                            <i class="fas fa-sign-out-alt"></i> Đăng Xuất
                        </a>
                    </nav>
                </div>
            </div>

            <!-- RIGHT: CONTENT AREA -->
            <div class="lg:w-3/4">
                
                <!-- 1. DASHBOARD TAB -->
                <div id="panel-dashboard" class="tab-panel active">
                    <h2 class="text-2xl font-display font-bold text-brand-navy mb-6">Tổng Quan Tài Khoản</h2>
                    
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-white p-5 rounded-2xl shadow-card border border-gray-100 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-50 text-brand-blue flex items-center justify-center text-xl"><i class="fas fa-box-open"></i></div>
                            <div>
                                <p class="text-2xl font-black text-brand-navy">
                                    <?php echo esc_html($caremil_order_count); ?>
                                </p>
                                <p class="text-xs text-gray-500 font-bold uppercase">Đơn hàng đã mua</p>
                            </div>
                        </div>
                        <div class="bg-white p-5 rounded-2xl shadow-card border border-gray-100 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-pink-50 text-brand-pink flex items-center justify-center text-xl"><i class="fas fa-wallet"></i></div>
                            <div>
                                <p class="text-2xl font-black text-brand-navy">
                                    <?php echo esc_html(caremil_format_currency($caremil_total_spent)); ?>
                                </p>
                                <p class="text-xs text-gray-500 font-bold uppercase">Tổng chi tiêu</p>
                            </div>
                        </div>
                        <div class="bg-white p-5 rounded-2xl shadow-card border border-gray-100 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-yellow-50 text-brand-gold flex items-center justify-center text-xl"><i class="fas fa-coins"></i></div>
                            <div>
                                <p class="text-2xl font-black text-brand-navy">
                                    <?php echo esc_html($caremil_points); ?>
                                </p>
                                <p class="text-xs text-gray-500 font-bold uppercase">Xu tích lũy</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Order -->
                    <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-brand-navy text-lg">Đơn Hàng Gần Nhất</h3>
                            <a href="#" onclick="switchTab('orders')" class="text-xs font-bold text-brand-blue hover:underline">Xem tất cả</a>
                        </div>
                        <?php if ($caremil_latest_order): ?>
                            <?php
                                $order_code   = $caremil_latest_order['code'] ?? ($caremil_latest_order['id'] ?? '');
                                $order_status = $caremil_latest_order['status'] ?? '';
                                $order_total  = $caremil_latest_order['total'] ?? 0;
                                $order_items  = $caremil_latest_order['items'] ?? array();
                                $order_date   = $caremil_latest_order['created_at'] ?? '';
                                $first_item   = is_array($order_items) && count($order_items) ? $order_items[0] : null;
                                $product_name = $first_item['name'] ?? 'Sản phẩm';
                                $product_qty  = $first_item['quantity'] ?? '';
                                $order_image  = $first_item['image'] ?? 'https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-chuan.png';
                            ?>
                        <div class="flex flex-col md:flex-row gap-4 items-center bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <div class="w-16 h-16 bg-white rounded-lg p-1 border border-gray-200 flex-shrink-0">
                                    <img src="<?php echo esc_url($order_image); ?>" class="w-full h-full object-contain">
                            </div>
                            <div class="flex-grow text-center md:text-left">
                                <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-3 mb-1">
                                        <span class="font-bold text-brand-navy text-sm">#<?php echo esc_html($order_code); ?></span>
                                        <?php if ($order_status): ?>
                                            <span class="text-xs px-2 py-0.5 rounded font-bold bg-gray-100">
                                                <?php echo esc_html($order_status); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        <?php
                                            $items_count = is_array($order_items) ? count($order_items) : 0;
                                            echo esc_html($items_count . ' sản phẩm - ' . ($product_name ?: ''));
                                        ?>
                                        <?php if ($order_date): ?>
                                            • Đặt ngày: <?php echo esc_html(date_i18n('d/m/Y', strtotime($order_date))); ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-brand-pink text-lg"><?php echo esc_html(caremil_format_currency($order_total)); ?></p>
                                    <div class="flex gap-2 justify-end">
                                        <button type="button" data-order-id="<?php echo esc_attr($caremil_latest_order['id'] ?? $order_code); ?>" class="js-order-detail mt-1 text-xs font-bold text-brand-navy border border-brand-navy px-3 py-1 rounded hover:bg-brand-navy hover:text-white transition">Chi tiết</button>
                                        <button class="mt-1 text-xs font-bold text-brand-navy border border-brand-navy px-3 py-1 rounded hover:bg-brand-navy hover:text-white transition">Theo dõi</button>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 text-center text-sm text-gray-500">
                                Chưa có đơn hàng.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 2. ORDERS TAB -->
                <div id="panel-orders" class="tab-panel">
                    <h2 class="text-2xl font-display font-bold text-brand-navy mb-6">Lịch Sử Đơn Hàng</h2>
                    
                    <div class="space-y-4">
                        <?php if (!empty($caremil_orders_list)): ?>
                            <?php foreach ($caremil_orders_list as $order): ?>
                                <?php
                                    $order_code   = $order['code'] ?? ($order['id'] ?? '');
                                    $order_status = $order['status'] ?? '';
                                    $order_total  = $order['total'] ?? 0;
                                    $order_date   = $order['created_at'] ?? '';
                                    $order_items  = $order['items'] ?? array();
                                    $first_item   = is_array($order_items) && count($order_items) ? $order_items[0] : null;
                                    $product_name = $first_item['name'] ?? 'Sản phẩm';
                                    $product_qty  = $first_item['quantity'] ?? '';
                                    $product_image= $first_item['image'] ?? 'https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-chuan.png';
                                ?>
                        <div class="bg-white rounded-2xl shadow-card border border-gray-100 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-3 border-b border-gray-100 flex justify-between items-center text-xs md:text-sm">
                                        <span class="font-bold text-gray-500">
                                            Mã đơn: <span class="text-brand-navy">#<?php echo esc_html($order_code); ?></span>
                                            <?php if ($order_date): ?>
                                                • <?php echo esc_html(date_i18n('d/m/Y', strtotime($order_date))); ?>
                                            <?php endif; ?>
                                        </span>
                                        <?php if ($order_status): ?>
                                            <span class="font-bold px-3 py-1 rounded-full bg-gray-100 text-gray-700"><?php echo esc_html($order_status); ?></span>
                                        <?php endif; ?>
                            </div>
                            <div class="p-6">
                                <div class="flex gap-4 items-start">
                                            <img src="<?php echo esc_url($product_image); ?>" class="w-20 h-20 object-contain border border-gray-100 rounded-lg p-1 bg-gray-50">
                                    <div class="flex-grow">
                                                <h4 class="font-bold text-brand-navy text-sm md:text-base"><?php echo esc_html($product_name); ?></h4>
                                                <p class="text-xs text-gray-500"><?php echo esc_html('x' . $product_qty); ?></p>
                                    </div>
                                    <div class="text-right">
                                                <p class="font-bold text-brand-pink"><?php echo esc_html(caremil_format_currency($order_total)); ?></p>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 pt-4 mt-4 border-t border-dashed border-gray-200">
                                            <span class="text-sm font-bold text-gray-500 self-center mr-auto">
                                                Tổng tiền: <span class="text-brand-navy text-lg"><?php echo esc_html(caremil_format_currency($order_total)); ?></span>
                                            </span>
                                            <button type="button" data-order-id="<?php echo esc_attr($order['id'] ?? $order_code); ?>" class="js-order-detail px-4 py-2 border border-gray-300 rounded-lg text-xs font-bold hover:bg-gray-50 text-gray-600 transition">Chi Tiết</button>
                                    <button class="px-4 py-2 bg-brand-navy text-white rounded-lg text-xs font-bold hover:bg-brand-blue transition shadow-md">Mua Lại</button>
                                </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6 text-center text-sm text-gray-500">
                                Chưa có đơn hàng.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 3. ADDRESS TAB -->
                <div id="panel-address" class="tab-panel">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-display font-bold text-brand-navy">Sổ Địa Chỉ</h2>
                        <button onclick="document.getElementById('caremil-addr-modal').classList.remove('hidden')" class="px-4 py-2 bg-brand-gold text-brand-navy rounded-lg text-sm font-bold hover:bg-yellow-400 shadow-md transition flex items-center gap-2">
                            <i class="fas fa-plus"></i> Thêm Mới
                        </button>
                    </div>

                    <!-- Form thêm / sửa địa chỉ -->
                    <div id="caremil-addr-modal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center px-4">
                        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden">
                            <div class="flex justify-between items-center px-6 py-4 border-b">
                                <h3 class="text-lg font-bold text-brand-navy" id="caremil-addr-modal-title">Thêm địa chỉ</h3>
                                <button type="button" class="text-gray-500 text-xl" onclick="caremilAddrClose()">&times;</button>
                            </div>
                            <form method="POST" class="p-6 space-y-4">
                                <input type="hidden" name="caremil_addr_action" id="caremil_addr_action" value="add">
                                <input type="hidden" name="caremil_addr_id" id="caremil_addr_id" value="">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Họ tên</label>
                                        <input type="text" name="caremil_addr_name" id="caremil_addr_name" class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-brand-blue focus:outline-none" placeholder="Họ tên người nhận">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Số điện thoại</label>
                                        <input type="text" name="caremil_addr_phone" id="caremil_addr_phone" class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-brand-blue focus:outline-none" placeholder="SĐT người nhận">
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="relative">
                                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase flex items-center justify-between">
                                                <span>Tỉnh/Thành</span>
                                                <span class="text-[11px] font-normal text-brand-blue">Gõ tên để gợi ý</span>
                                            </label>
                                            <input type="hidden" name="caremil_addr_province_id" id="caremil_addr_province_id">
                                            <input 
                                                type="text" 
                                                id="caremil_addr_province_search" 
                                                class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-brand-blue focus:outline-none text-sm" 
                                                placeholder="Ví dụ: Hồ Chí Minh, Hà Nội..."
                                                autocomplete="off"
                                            >
                                            <div id="caremil_addr_province_suggestions" class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden text-sm"></div>
                                        </div>
                                        <div class="relative">
                                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase flex items-center justify-between">
                                                <span>Phường/Xã</span>
                                                <span class="text-[11px] font-normal text-brand-blue">Gõ tên để gợi ý</span>
                                            </label>
                                            <input type="hidden" name="caremil_addr_district_id" id="caremil_addr_district_id">
                                            <input type="hidden" name="caremil_addr_commune_id" id="caremil_addr_commune_id">
                                            <input 
                                                type="text" 
                                                id="caremil_addr_commune_search" 
                                                class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-brand-blue focus:outline-none text-sm" 
                                                placeholder="Ví dụ: Phường 1, Xã Bình An..."
                                                autocomplete="off"
                                            >
                                            <div id="caremil_addr_commune_suggestions" class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden text-sm"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Địa chỉ đầy đủ</label>
                                        <input type="text" name="caremil_addr_full" id="caremil_addr_full" class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-brand-blue focus:outline-none text-sm" placeholder="Số nhà, tên đường (hệ thống sẽ tự thêm phường/xã, tỉnh/thành)">
                                        <p class="mt-1 text-[11px] text-gray-400">Ví dụ: 25 Nguyễn Huệ. Hệ thống sẽ ghép với phường/xã và tỉnh/thành để tạo địa chỉ hoàn chỉnh.</p>
                                    </div>
                                </div>
                                <div class="flex gap-3 justify-end">
                                    <button type="button" onclick="caremilAddrClose()" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition">Hủy</button>
                                    <button type="submit" class="bg-brand-navy text-white px-4 py-2 rounded-lg font-bold shadow-md hover:bg-brand-blue transition"><i class="fas fa-save mr-2"></i><span id="caremil_addr_submit_label">Lưu địa chỉ</span></button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Danh sách địa chỉ (render bằng JS) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="caremil-address-list"></div>
                </div>

                <!-- 4. VOUCHERS TAB -->
                <div id="panel-vouchers" class="tab-panel">
                    <h2 class="text-2xl font-display font-bold text-brand-navy mb-6">Kho Ưu Đãi</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Voucher 1 -->
                        <div class="bg-white rounded-xl shadow-sm flex overflow-hidden border border-gray-100 voucher-card hover:-translate-y-1 transition duration-300 group">
                            <div class="bg-brand-blue w-28 flex flex-col items-center justify-center text-white p-2 text-center group-hover:bg-blue-500 transition">
                                <span class="font-black text-2xl">10%</span>
                                <span class="text-[10px] uppercase font-bold tracking-widest">OFF</span>
                            </div>
                            <div class="p-4 flex-grow flex flex-col justify-center">
                                <p class="font-bold text-brand-navy text-sm">Giảm 10% đơn từ 500k</p>
                                <p class="text-xs text-gray-400 mt-1 mb-3">HSD: 30/12/2024</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] bg-blue-50 text-brand-blue px-2 py-0.5 rounded border border-blue-100">Mã: CARE10</span>
                                    <button class="text-xs font-bold text-white bg-brand-blue px-3 py-1.5 rounded-lg hover:bg-blue-600 transition shadow-sm">Dùng ngay</button>
                                </div>
                            </div>
                        </div>

                        <!-- Voucher 2 -->
                        <div class="bg-white rounded-xl shadow-sm flex overflow-hidden border border-gray-100 voucher-card hover:-translate-y-1 transition duration-300 group">
                            <div class="bg-brand-green w-28 flex flex-col items-center justify-center text-white p-2 text-center group-hover:bg-green-500 transition">
                                <i class="fas fa-truck text-2xl mb-1"></i>
                                <span class="text-[10px] uppercase font-bold tracking-widest">FREESHIP</span>
                            </div>
                            <div class="p-4 flex-grow flex flex-col justify-center">
                                <p class="font-bold text-brand-navy text-sm">Miễn phí vận chuyển</p>
                                <p class="text-xs text-gray-400 mt-1 mb-3">HSD: 15/06/2024</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] bg-green-50 text-green-600 px-2 py-0.5 rounded border border-green-100">Mã: FREESHIP</span>
                                    <button class="text-xs font-bold text-white bg-brand-green px-3 py-1.5 rounded-lg hover:bg-green-600 transition shadow-sm">Dùng ngay</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. PROFILE TAB -->
                <div id="panel-profile" class="tab-panel">
                    <h2 class="text-2xl font-display font-bold text-brand-navy mb-6">Thông Tin Cá Nhân</h2>
                    <div class="bg-white rounded-3xl shadow-card p-6 md:p-8 border border-gray-100">
                        <form method="POST">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">Họ và tên</label>
                                    <div class="relative">
                                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                        <input type="text" name="caremil_name" value="<?php echo esc_attr($caremil_display_name); ?>" class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:border-brand-blue focus:bg-white transition font-bold text-brand-navy">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">Số điện thoại</label>
                                    <div class="relative">
                                        <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                        <input type="tel" name="caremil_phone" value="<?php echo esc_attr($caremil_display_phone); ?>" class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:border-brand-blue focus:bg-white transition font-bold text-brand-navy">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">Email</label>
                                    <div class="relative">
                                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                        <input type="email" name="caremil_email" value="<?php echo esc_attr($caremil_email); ?>" class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:border-brand-blue focus:bg-white transition font-bold text-brand-navy">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">Ngày sinh <span class="text-[10px] text-brand-blue normal-case">(Nhận quà sinh nhật)</span></label>
                                    <div class="relative">
                                        <i class="fas fa-birthday-cake absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                        <input type="date" name="caremil_birthday" value="<?php echo esc_attr($caremil_birthday); ?>" class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:border-brand-blue focus:bg-white transition font-bold text-brand-navy text-sm">
                                    </div>
                                </div>
                                
                                <div class="md:col-span-2 pt-4 flex justify-end">
                                    <button type="submit" class="bg-brand-navy text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:bg-brand-blue transition transform hover:-translate-y-1 flex items-center gap-2">
                                        <i class="fas fa-save"></i> Lưu Thay Đổi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ORDER DETAIL MODAL -->
    <div id="order-detail-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 px-4">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <div>
                    <p class="text-xs uppercase text-gray-500 font-bold">Chi tiết đơn hàng</p>
                    <h3 class="text-lg font-bold text-brand-navy" id="odm-code">#--</h3>
                </div>
                <button type="button" id="odm-close" class="text-gray-500 hover:text-brand-navy text-xl">&times;</button>
            </div>
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <p class="text-xs font-bold text-gray-500 uppercase">Trạng thái</p>
                        <p class="font-bold text-brand-navy" id="odm-status">--</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <p class="text-xs font-bold text-gray-500 uppercase">Ngày đặt</p>
                        <p class="font-bold text-brand-navy" id="odm-date">--</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 md:col-span-2">
                        <p class="text-xs font-bold text-gray-500 uppercase">Địa chỉ giao</p>
                        <p class="font-bold text-brand-navy" id="odm-shipping">--</p>
                    </div>
                </div>

                <div class="border rounded-xl">
                    <div class="flex justify-between items-center px-4 py-3 border-b bg-gray-50">
                        <p class="text-sm font-bold text-brand-navy">Sản phẩm</p>
                        <p class="text-sm font-bold text-brand-navy">Thành tiền</p>
                    </div>
                    <div id="odm-items" class="divide-y">
                        <div class="px-4 py-3 text-sm text-gray-500">Đang tải...</div>
                    </div>
                </div>

                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Tổng tạm tính</span>
                        <span id="odm-subtotal" class="font-bold text-brand-navy">--</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Phí vận chuyển</span>
                        <span id="odm-shipping-fee" class="font-bold text-brand-navy">--</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Giảm giá</span>
                        <span id="odm-discount" class="font-bold text-brand-navy">--</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-brand-navy pt-2 border-t">
                        <span>Tổng thanh toán</span>
                        <span id="odm-total" class="text-brand-pink">--</span>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end">
                <button type="button" id="odm-close-btn" class="px-4 py-2 text-sm font-bold text-brand-navy border border-brand-navy rounded-lg hover:bg-brand-navy hover:text-white transition">Đóng</button>
            </div>
        </div>
    </div>

    <script>
        // JS constants for Pancake fetch (dùng cho refetch địa chỉ sau khi thêm)
        const CAREMIL_SHOP_ID = <?php echo wp_json_encode(caremil_get_pancake_shop_id()); ?>;
        const CAREMIL_API_KEY = <?php echo wp_json_encode(caremil_get_pancake_api_key()); ?>;
        const CAREMIL_CUSTOMER_ID = <?php echo wp_json_encode($caremil_customer_id); ?>;

        function switchTab(tabId) {
            // Update Sidebar
            document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
            document.getElementById('nav-' + tabId).classList.add('active');
            
            // Update Content
            document.querySelectorAll('.tab-panel').forEach(el => el.classList.remove('active'));
            document.getElementById('panel-' + tabId).classList.add('active');
            
            // Scroll top on mobile
            if (window.innerWidth < 1024) {
                document.getElementById('panel-' + tabId).scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // ===== ORDER DETAIL POPUP =====
        const caremilOrders = <?php echo wp_json_encode($caremil_orders_list); ?>;
        const modal = document.getElementById('order-detail-modal');
        const closeBtns = [document.getElementById('odm-close'), document.getElementById('odm-close-btn')];

        function formatVnd(amount) {
            const n = parseFloat(amount || 0);
            return n.toLocaleString('vi-VN') + 'đ';
        }

        function fillOrderModal(order) {
            const code = order.code || order.id || '';
            document.getElementById('odm-code').innerText = '#' + code;
            document.getElementById('odm-status').innerText = order.status_name || order.status || '--';
            document.getElementById('odm-date').innerText = order.created_at ? new Date(order.created_at).toLocaleString('vi-VN') : (order.inserted_at ? new Date(order.inserted_at).toLocaleString('vi-VN') : '--');

            const shipAddress = order.shipping_address || {};
            const fullAddr = shipAddress.full_address || shipAddress.address || shipAddress.street || '';
            const shipPhone = shipAddress.phone_number || shipAddress.phone || '';
            document.getElementById('odm-shipping').innerText = fullAddr ? (fullAddr + (shipPhone ? ' | ' + shipPhone : '')) : 'Tại cửa hàng';

            const itemsWrap = document.getElementById('odm-items');
            itemsWrap.innerHTML = '';
            const items = order.items || [];
            if (items.length === 0) {
                itemsWrap.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 italic">Không có sản phẩm</div>';
            } else {
                items.forEach(item => {
                    const name = item.variation_info?.name || item.product_name || item.name || 'Sản phẩm';
                    const qty  = item.quantity || 1;
                    const price= item.retail_price || item.price || 0;
                    const line = price * qty;
                    const img  = item.image || '';
                    itemsWrap.innerHTML += `
                        <div class="px-4 py-3 flex items-start gap-3">
                            ${img ? `<img src="${img}" class="w-12 h-12 rounded border object-contain bg-white">` : ''}
                            <div class="flex-1">
                                <p class="font-bold text-sm text-brand-navy">${name}</p>
                                <p class="text-xs text-gray-500">SL: ${qty} x ${formatVnd(price)}</p>
                            </div>
                            <div class="font-bold text-brand-navy">${formatVnd(line)}</div>
                        </div>
                    `;
                });
            }

            const subtotal = items.reduce((s, it) => s + (it.quantity || 1) * (it.retail_price || it.price || 0), 0);
            const shipFee  = order.shipping_fee || 0;
            const discount = order.total_discount || 0;
            const total    = order.total_price || order.total || (subtotal + shipFee - discount);

            document.getElementById('odm-subtotal').innerText = formatVnd(subtotal);
            document.getElementById('odm-shipping-fee').innerText = formatVnd(shipFee);
            document.getElementById('odm-discount').innerText = '-' + formatVnd(discount);
            document.getElementById('odm-total').innerText = formatVnd(total);
        }

        function openOrderModal(orderId) {
            if (!orderId) return;
            const found = (caremilOrders || []).find(o => String(o.id) === String(orderId) || String(o.code) === String(orderId));
            if (!found) return;
            fillOrderModal(found);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        document.querySelectorAll('.js-order-detail').forEach(btn => {
            btn.addEventListener('click', () => {
                const oid = btn.getAttribute('data-order-id');
                openOrderModal(oid);
            });
        });

        closeBtns.forEach(btn => btn.addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }));

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });

        // ===== ADDRESS HELPERS + RENDER =====
        let caremilAddresses = <?php echo wp_json_encode($caremil_addresses); ?>;
        const caremilGeoCache = {
            provinces: [],
            districts: {}, // key: province_id -> list
            communesByProvince: {} // key: province_id -> list (flattened từ nhiều quận/huyện)
        };

        async function geoFetch(url) {
            const res = await fetch(url);
            if (!res.ok) throw new Error('Geo API error');
            return await res.json();
        }

        function sortByName(list) {
            return (list || []).slice().sort((a, b) => {
                const na = (a.name || '').localeCompare ? a.name : String(a.name || '');
                const nb = (b.name || '').localeCompare ? b.name : String(b.name || '');
                return na.localeCompare(nb, 'vi', { sensitivity: 'base' });
            });
        }

        async function loadProvinces() {
            if (caremilGeoCache.provinces.length) return caremilGeoCache.provinces;
            const data = await geoFetch('https://pos.pages.fm/api/v1/geo/provinces?country_code=84');
            caremilGeoCache.provinces = sortByName(data.data || []);
            return caremilGeoCache.provinces;
        }

        async function loadCommunesByProvince(provinceId) {
            if (caremilGeoCache.communesByProvince[provinceId]) {
                return caremilGeoCache.communesByProvince[provinceId];
            }
            // 1. Lấy danh sách quận/huyện (ẩn trong UI)
            if (!caremilGeoCache.districts[provinceId]) {
                const dRes = await geoFetch(`https://pos.pages.fm/api/v1/geo/districts?province_id=${provinceId}`);
                caremilGeoCache.districts[provinceId] = dRes.data || [];
            }
            const districts = caremilGeoCache.districts[provinceId];
            // 2. Lấy toàn bộ phường/xã theo từng quận/huyện rồi gộp lại
            let allCommunes = [];
            for (const d of districts) {
                const did = d.id;
                if (!did) continue;
                const cRes = await geoFetch(`https://pos.pages.fm/api/v1/geo/communes?district_id=${did}&province_id=${provinceId}`);
                const communes = (cRes.data || []).map(c => ({
                    ...c,
                    district_id: did,
                    district_name: d.name || ''
                }));
                allCommunes = allCommunes.concat(communes);
            }
            caremilGeoCache.communesByProvince[provinceId] = sortByName(allCommunes);
            return caremilGeoCache.communesByProvince[provinceId];
        }

        function fillSelect(el, list, placeholder = 'Chọn') {
            el.innerHTML = '';
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = placeholder;
            el.appendChild(opt);
            list.forEach(item => {
                const o = document.createElement('option');
                o.value = item.id;
                o.textContent = item.name;
                el.appendChild(o);
            });
        }

        async function ensureProvinces(selectedId = '') {
            const provinces = await loadProvinces();
            const hiddenProv = document.getElementById('caremil_addr_province_id');
            const searchInput = document.getElementById('caremil_addr_province_search');
            const suggestions = document.getElementById('caremil_addr_province_suggestions');
            if (!hiddenProv || !searchInput || !suggestions) return;

            function renderProvinceSuggestions(keyword = '') {
                const kw = keyword.trim().toLowerCase();
                suggestions.innerHTML = '';
                let filtered = provinces;
                if (kw) {
                    filtered = provinces.filter(p => (p.name || '').toLowerCase().includes(kw));
                }
                filtered.slice(0, 50).forEach(p => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'w-full text-left px-3 py-2 hover:bg-blue-50 text-sm';
                    btn.textContent = p.name || '';
                    btn.addEventListener('click', async () => {
                        hiddenProv.value = p.id || '';
                        searchInput.value = p.name || '';
                        suggestions.classList.add('hidden');
                        // Khi chọn tỉnh thì chuẩn bị lại danh sách phường/xã
                        await prepareCommunesForProvince(p.id);
                    });
                    suggestions.appendChild(btn);
                });
                suggestions.classList.toggle('hidden', filtered.length === 0);
            }

            if (!searchInput.__caremilProvBound) {
                searchInput.addEventListener('input', () => {
                    renderProvinceSuggestions(searchInput.value);
                });
                searchInput.addEventListener('focus', () => {
                    if (searchInput.value.trim() === '') {
                        renderProvinceSuggestions('');
                    } else {
                        renderProvinceSuggestions(searchInput.value);
                    }
                });
                document.addEventListener('click', (e) => {
                    if (!suggestions.contains(e.target) && e.target !== searchInput) {
                        suggestions.classList.add('hidden');
                    }
                });
                searchInput.__caremilProvBound = true;
            }

            // Nếu truyền sẵn selectedId (khi edit), gán lại giá trị
            if (selectedId) {
                const found = provinces.find(p => String(p.id) === String(selectedId));
                if (found) {
                    hiddenProv.value = found.id || '';
                    searchInput.value = found.name || '';
                    await prepareCommunesForProvince(found.id);
                }
            }
        }

        async function prepareCommunesForProvince(provinceId) {
            const searchInput = document.getElementById('caremil_addr_commune_search');
            const suggestionsBox = document.getElementById('caremil_addr_commune_suggestions');
            const hiddenDistrict = document.getElementById('caremil_addr_district_id');
            const hiddenCommune  = document.getElementById('caremil_addr_commune_id');
            if (!searchInput || !suggestionsBox) return;

            // Clear current
            searchInput.value = '';
            hiddenDistrict.value = '';
            hiddenCommune.value = '';
            suggestionsBox.innerHTML = '';
            suggestionsBox.classList.add('hidden');

            if (!provinceId) return;

            const communes = await loadCommunesByProvince(provinceId);

            function renderSuggestions(keyword = '') {
                const kw = keyword.trim().toLowerCase();
                suggestionsBox.innerHTML = '';
                let filtered = communes;
                if (kw) {
                    filtered = communes.filter(c => {
                        const combo = `${c.name || ''} ${c.district_name || ''}`.toLowerCase();
                        return combo.includes(kw);
                    });
                }
                filtered.slice(0, 50).forEach(c => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'w-full text-left px-3 py-2 hover:bg-blue-50 text-sm flex flex-col';
                    const line1 = c.name || '';
                    const line2 = c.district_name ? `Quận/Huyện cũ  : ${c.district_name}` : '';
                    item.innerHTML = `<span class="font-semibold text-brand-navy">${line1}</span>${line2 ? `<span class="text-[11px] text-gray-500">${line2}</span>` : ''}`;
                    item.addEventListener('click', () => {
                        searchInput.value = c.name || '';
                        hiddenCommune.value = c.id || '';
                        hiddenDistrict.value = c.district_id || '';
                        suggestionsBox.classList.add('hidden');
                    });
                    suggestionsBox.appendChild(item);
                });
                suggestionsBox.classList.toggle('hidden', filtered.length === 0);
            }

            // Gắn handler một lần
            if (!searchInput.__caremilBound) {
                searchInput.addEventListener('input', () => {
                    renderSuggestions(searchInput.value);
                });
                searchInput.addEventListener('focus', () => {
                    if (searchInput.value.trim() === '') {
                        renderSuggestions('');
                    } else {
                        renderSuggestions(searchInput.value);
                    }
                });
                document.addEventListener('click', (e) => {
                    if (!suggestionsBox.contains(e.target) && e.target !== searchInput) {
                        suggestionsBox.classList.add('hidden');
                    }
                });
                searchInput.__caremilBound = true;
            }

            renderSuggestions('');
        }

        function renderAddressList() {
            const container = document.getElementById('caremil-address-list');
            if (!container) return;
            container.innerHTML = '';

            let list = Array.isArray(caremilAddresses) ? [...caremilAddresses] : [];

            // Hiển thị tất cả địa chỉ, chỉ bỏ qua những bản ghi quá thiếu dữ liệu
            list = list.filter(addr => {
                const full = (addr.full_address || addr.address || '').trim();
                return full && full.length >= 3;
            });

            // Sắp xếp theo tên địa chỉ (theo alphabet, chuẩn tiếng Việt)
            list.sort((a, b) => {
                const fa = (a.full_address || a.address || '').toString();
                const fb = (b.full_address || b.address || '').toString();
                return fa.localeCompare(fb, 'vi', { sensitivity: 'base' });
            });

            if (!list.length) {
                container.innerHTML = '<div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6 text-center text-sm text-gray-500 col-span-2">Chưa có địa chỉ nào.</div>';
                return;
            }

            const safe = (v) => String(v ?? '');
            list.forEach(addr => {
                const id    = safe(addr.id);
                const name  = safe(addr.full_name || <?php echo wp_json_encode($caremil_display_name); ?>);
                const phone = safe(addr.phone_number || <?php echo wp_json_encode($caremil_display_phone); ?>);
                const full  = safe(addr.full_address || addr.address || '');
                const prov  = safe(addr.province_id);
                const dist  = safe(addr.district_id);
                const comm  = safe(addr.commune_id);
                container.innerHTML += `
                    <div class="bg-white p-6 rounded-2xl shadow-card border border-gray-100 relative">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-brand-blue"><i class="fas fa-home"></i></div>
                            <div class="flex-1">
                                <p class="font-bold text-brand-navy text-sm">${name} <span class="text-gray-500 font-normal">| ${phone}</span></p>
                                <p class="text-sm text-gray-700 mt-1 break-words">${full}</p>
                                <div class="flex gap-3 mt-3 text-xs font-bold">
                                    <button type="button" class="text-brand-blue hover:underline" onclick='caremilAddrEdit(${JSON.stringify(id)}, ${JSON.stringify(name)}, ${JSON.stringify(phone)}, ${JSON.stringify(full)}, ${JSON.stringify(prov)}, ${JSON.stringify(dist)}, ${JSON.stringify(comm)})'>Sửa</button>
                                    ${id ? `<button type="button" class="text-red-500 hover:underline" onclick='caremilAddrDelete(${JSON.stringify(id)})'>Xóa</button>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        function caremilAddrClose() {
            document.getElementById('caremil-addr-modal').classList.add('hidden');
            document.getElementById('caremil_addr_action').value = 'add';
            document.getElementById('caremil_addr_id').value = '';
            document.getElementById('caremil_addr_name').value = '';
            document.getElementById('caremil_addr_phone').value = '';
            document.getElementById('caremil_addr_full').value = '';
            document.getElementById('caremil_addr_province_id').value = '';
            document.getElementById('caremil_addr_district_id').value = '';
            document.getElementById('caremil_addr_commune_id').value = '';
            const communeSearch = document.getElementById('caremil_addr_commune_search');
            const communeSuggestions = document.getElementById('caremil_addr_commune_suggestions');
            const provinceSearch = document.getElementById('caremil_addr_province_search');
            const provinceSuggestions = document.getElementById('caremil_addr_province_suggestions');
            if (communeSearch) communeSearch.value = '';
            if (communeSuggestions) {
                communeSuggestions.innerHTML = '';
                communeSuggestions.classList.add('hidden');
            }
            if (provinceSearch) provinceSearch.value = '';
            if (provinceSuggestions) {
                provinceSuggestions.innerHTML = '';
                provinceSuggestions.classList.add('hidden');
            }
            document.getElementById('caremil_addr_submit_label').innerText = 'Lưu địa chỉ';
            document.getElementById('caremil-addr-modal-title').innerText = 'Thêm địa chỉ';
        }

        function caremilAddrEdit(id, name, phone, full, prov, dist, comm) {
            document.getElementById('caremil-addr-modal').classList.remove('hidden');
            document.getElementById('caremil_addr_action').value = 'update';
            document.getElementById('caremil_addr_id').value = id;
            document.getElementById('caremil_addr_name').value = name;
            document.getElementById('caremil_addr_phone').value = phone;
            document.getElementById('caremil_addr_full').value = full;
            // set selects async
            (async () => {
                await ensureProvinces(prov);
                if (prov) {
                    await prepareCommunesForProvince(prov);
                    // Sau khi load xong communes, cố gắng gán lại commune theo ID
                    const hiddenDistrict = document.getElementById('caremil_addr_district_id');
                    const hiddenCommune  = document.getElementById('caremil_addr_commune_id');
                    const searchInput    = document.getElementById('caremil_addr_commune_search');
                    hiddenDistrict.value = dist || '';
                    hiddenCommune.value  = comm || '';
                    // Nếu có tên phường trong full_address thì giữ nguyên, nếu không thì chỉ hiện tên phường
                    if (searchInput && comm) {
                        // Tìm lại trong cache để lấy tên
                        const communes = caremilGeoCache.communesByProvince[prov] || [];
                        const found = communes.find(c => String(c.id) === String(comm));
                        if (found && !searchInput.value) {
                            searchInput.value = found.name || '';
                        }
                    }
                }
            })();
            document.getElementById('caremil_addr_submit_label').innerText = 'Cập nhật';
            document.getElementById('caremil-addr-modal-title').innerText = 'Cập nhật địa chỉ';
        }

        // Submit form địa chỉ qua AJAX để tránh reload
        (function initAddrForm() {
            const modal = document.getElementById('caremil-addr-modal');
            const form = modal ? modal.querySelector('form') : null;
            if (!form) return;
            // preload provinces
            ensureProvinces();
            // provinces được load sẵn bởi ensureProvinces()
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                formData.append('caremil_addr_ajax', '1');
                const submitBtn = form.querySelector('button[type="submit"]');
                const label = form.querySelector('#caremil_addr_submit_label');
                const oldText = label ? label.innerText : '';
                if (submitBtn) submitBtn.disabled = true;
                if (label) label.innerText = 'Đang lưu...';
                // build full address nếu trống
                const detail = document.getElementById('caremil_addr_full').value.trim();
                const provinceSearch = document.getElementById('caremil_addr_province_search');
                const provinceName = provinceSearch ? provinceSearch.value.trim() : '';
                const communeNameInput = document.getElementById('caremil_addr_commune_search');
                const communeName = communeNameInput ? communeNameInput.value.trim() : '';
                if (!detail || detail.split(',').length < 2) {
                    const parts = [detail, communeName, provinceName].filter(Boolean);
                    document.getElementById('caremil_addr_full').value = parts.join(', ');
                }
                // optimistic add/update để UI phản hồi tức thì
                const optimisticAddr = {
                    id: formData.get('caremil_addr_id') || '',
                    full_name: formData.get('caremil_addr_name'),
                    phone_number: formData.get('caremil_addr_phone'),
                    province_id: formData.get('caremil_addr_province_id'),
                    district_id: formData.get('caremil_addr_district_id'),
                    commune_id: formData.get('caremil_addr_commune_id'),
                    full_address: document.getElementById('caremil_addr_full').value,
                    address: document.getElementById('caremil_addr_full').value,
                };
                const isUpdate = !!optimisticAddr.id;
                const oldList = [...(caremilAddresses || [])];
                if (isUpdate) {
                    caremilAddresses = caremilAddresses.map(a => (String(a.id) === String(optimisticAddr.id) ? optimisticAddr : a));
                } else {
                    caremilAddresses = [...caremilAddresses, optimisticAddr];
                }
                renderAddressList();

                // hàm refetch địa chỉ từ Pancake để lấy ID chính xác sau khi tạo mới
                async function refetchAddressesFromApi() {
                    if (!CAREMIL_SHOP_ID || !CAREMIL_CUSTOMER_ID) return null;
                    const url = `https://pos.pages.fm/api/v1/shops/${CAREMIL_SHOP_ID}/customers/${CAREMIL_CUSTOMER_ID}?api_key=${CAREMIL_API_KEY}`;
                    try {
                        const res = await fetch(url);
                        if (!res.ok) return null;
                        const json = await res.json();
                        if (!json) return null;
                        // Thử nhiều key có thể có
                        if (json.data && json.data.shop_customer_addresses && Array.isArray(json.data.shop_customer_addresses)) {
                            return json.data.shop_customer_addresses;
                        }
                        if (json.data && json.data.shop_customer_address && Array.isArray(json.data.shop_customer_address)) {
                            return json.data.shop_customer_address;
                        }
                        if (json.shop_customer_addresses && Array.isArray(json.shop_customer_addresses)) {
                            return json.shop_customer_addresses;
                        }
                        if (json.shop_customer_address && Array.isArray(json.shop_customer_address)) {
                            return json.shop_customer_address;
                        }
                        if (json.customer && json.customer.shop_customer_addresses && Array.isArray(json.customer.shop_customer_addresses)) {
                            return json.customer.shop_customer_addresses;
                        }
                        if (json.customer && json.customer.shop_customer_address && Array.isArray(json.customer.shop_customer_address)) {
                            return json.customer.shop_customer_address;
                        }
                        return null;
                    } catch (e) {
                        console.error('Refetch addresses error:', e);
                        return null;
                    }
                }

                try {
                    const res = await fetch(window.location.href, { method: 'POST', body: formData });
                    let data = null;
                    try {
                        data = await res.json();
                    } catch (_) {
                        // Nếu server trả HTML nhưng status ok, coi như thành công
                        if (res.ok) data = { success: true };
                    }
                    if (data && data.success) {
                        // Đợi một chút để server xử lý xong, rồi refetch để lấy đầy đủ thông tin (ID, các field khác)
                        await new Promise(resolve => setTimeout(resolve, 500));
                        const refetched = await refetchAddressesFromApi();
                        if (refetched && refetched.length) {
                            caremilAddresses = refetched;
                        } else if (data.addresses && data.addresses.length) {
                            // Fallback: dùng addresses từ response nếu refetch thất bại
                            caremilAddresses = data.addresses;
                        }
                        renderAddressList();
                        caremilAddrClose();
                    } else if (data && data.message) {
                        // rollback nếu thất bại
                        caremilAddresses = oldList;
                        renderAddressList();
                        alert(data.message);
                    } else {
                        caremilAddresses = oldList;
                        renderAddressList();
                        alert('Không thể lưu địa chỉ');
                    }
                } catch (err) {
                    caremilAddresses = oldList;
                    renderAddressList();
                    alert('Lỗi kết nối khi lưu địa chỉ');
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                    if (label) label.innerText = oldText || 'Lưu địa chỉ';
                }
            });
        })();

        async function caremilAddrDelete(id) {
            if (!id) return;
            if (!confirm('Xóa địa chỉ này?')) return;
            const formData = new FormData();
            formData.append('caremil_addr_action', 'delete');
            formData.append('caremil_addr_id', id);
            formData.append('caremil_addr_ajax', '1');
            // Optimistic remove để UI phản hồi ngay
            caremilAddresses = (caremilAddresses || []).filter(a => String(a.id) !== String(id));
            renderAddressList();
            try {
                const res = await fetch(window.location.href, { method: 'POST', body: formData });
                let data = null;
                try {
                    data = await res.json();
                } catch (_) {
                    // Nếu không parse được JSON nhưng HTTP ok, coi như thành công
                    data = { success: res.ok, addresses: caremilAddresses };
                }
                if (data && data.success) {
                    caremilAddresses = data.addresses || caremilAddresses;
                    renderAddressList();
                } else if (data && data.message) {
                    alert(data.message);
                } else {
                    alert('Không thể xóa địa chỉ');
                }
            } catch (err) {
                // Dù lỗi kết nối, vẫn giữ trạng thái đã bỏ khỏi UI; có thể reload sau nếu cần
                alert('Lỗi kết nối khi xóa địa chỉ');
            }
        }

        // Render initial
        renderAddressList();
    </script>
<?php
get_footer();