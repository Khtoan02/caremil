<?php
/**
 * Template Name: Customer Portal (Standard Logic V7 - Fix API Params)
 */

// -----------------------------------------------------------------------------
// 1. CẤU HÌNH & KẾT NỐI API
// -----------------------------------------------------------------------------
// Sử dụng helper functions từ functions.php để lấy config từ admin settings
// Đảm bảo functions.php đã được load (thường tự động load qua WordPress)

// URL đích sau khi đăng nhập thành công
$caremil_account_url = home_url('/tai-khoan-cua-toi'); // đổi slug nếu khác

if (!session_id()) session_start();

// Kiểm tra kết nối Pancake trước khi xử lý (trừ AJAX requests)
if (!isset($_GET['api_action']) && !isset($_POST['auth_case'])) {
    if (function_exists('caremil_require_pancake_connection')) {
        caremil_require_pancake_connection();
    }
}

// Hàm gọi API - wrapper để tương thích với code hiện tại
// Sử dụng hàm caremil_pancake_request() từ functions.php
function call_pancake_api($endpoint, $params = [], $method = 'GET') {
    // Đảm bảo helper functions đã được load
    if (!function_exists('caremil_get_pancake_shop_id')) {
        // Nếu chưa load functions.php, load nó
        if (file_exists(get_template_directory() . '/functions.php')) {
            require_once get_template_directory() . '/functions.php';
        }
    }
    
    // Chuyển đổi endpoint format: '/customers' -> '/shops/{SHOP_ID}/customers'
    $full_path = '/shops/' . caremil_get_pancake_shop_id() . $endpoint;
    
    // Phân biệt query params và body params
    if ($method === 'GET') {
        // Với GET, params là query string
        return caremil_pancake_request($full_path, $params, $method, null);
    } else {
        // Với POST/PUT, params là body
        return caremil_pancake_request($full_path, array(), $method, $params);
    }
}

// -----------------------------------------------------------------------------
// 2. AJAX API: PHÂN TÍCH TRẠNG THÁI KHÁCH HÀNG
// -----------------------------------------------------------------------------
if (isset($_GET['api_action'])) {
    // Kiểm tra kết nối cho AJAX requests
    if (function_exists('caremil_check_pancake_connection')) {
        if (!caremil_check_pancake_connection()) {
            header('Content-Type: application/json');
            echo json_encode(array(
                'error' => true,
                'message' => 'Hệ thống đang bảo trì. Vui lòng thử lại sau ít phút hoặc liên hệ Admin để được hỗ trợ sớm nhất.',
                'maintenance' => true
            ));
            exit;
        }
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pancake_customers';

    // FAST LOGIN: Thử đăng nhập nhanh bằng mật khẩu; nếu fail thì trả về case cụ thể
    if ($_GET['api_action'] == 'fast_login') {
        $identifier = sanitize_text_field($_GET['id'] ?? '');
        $password_try = $_GET['password'] ?? '';
        $redirect_url = home_url('/tai-khoan-cua-toi');

        $user_row = null;
        if (!empty($identifier)) {
            if (preg_match('/^\d{8,}$/', $identifier)) {
                $user_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE phone = %s", $identifier));
            } else {
                $user_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE email = %s", $identifier));
            }
        }

        $resp = [
            'success' => false,
            'phone' => '',
            'case' => 'no_account', // Mặc định: chưa có tài khoản
            'message' => 'Tài khoản chưa được kích hoạt',
        ];

        if ($user_row) {
            $resp['phone'] = $user_row->phone;
            
            // Kiểm tra xem có mật khẩu không
            if (empty($user_row->password) || $user_row->password === '') {
                $resp['case'] = 'no_password';
                $resp['message'] = 'Tài khoản chưa có mật khẩu. Tạo mật khẩu ngay.';
            } elseif (!empty($password_try) && wp_check_password($password_try, $user_row->password)) {
                // Đúng mật khẩu
                $_SESSION['pancake_logged_in'] = true;
                $_SESSION['pancake_phone'] = $user_row->phone;
                $_SESSION['pancake_name'] = $user_row->name;
                $_SESSION['pancake_customer_id'] = $user_row->pancake_id;

                $resp['success'] = true;
                $resp['redirect'] = $redirect_url;
                $resp['message'] = 'Đăng nhập thành công.';
            } else {
                // Sai mật khẩu
                $resp['case'] = 'wrong_password';
                $resp['message'] = 'Mật khẩu chưa đúng. Vui lòng kiểm tra lại.';
            }
        }

        header('Content-Type: application/json');
        echo json_encode($resp);
        exit;
    }

    // A. Check trạng thái user (Logic cốt lõi)
    if ($_GET['api_action'] == 'check_status') {
        $phone = sanitize_text_field($_GET['phone']);
        
        // 1. Lấy dữ liệu từ Pancake
        $pancake_res = call_pancake_api('/customers', ['search' => $phone, 'page_size' => 1]);
        $pc_user = (!empty($pancake_res['data'])) ? $pancake_res['data'][0] : null;
        
        // 2. Lấy dữ liệu từ Local WP
        $local_user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE phone = %s", $phone));

        $response = [
            'case' => 3, // Mặc định là TH3 (Mới tinh)
            'welcome_name' => 'Quý Khách',
            'pancake_id' => '',
            'current_email' => '',
            'current_name' => '',
            'missing_email' => true,
            'missing_name' => true
        ];

        // --- PHÂN LOẠI TRƯỜNG HỢP ---

        if ($pc_user) {
            // Đã có trên Pancake
            $response['pancake_id'] = $pc_user['id'];
            $response['welcome_name'] = $pc_user['name'];
            $response['current_name'] = $pc_user['name'];
            
            // Check Email trên Pancake
            $pc_email = '';
            if (!empty($pc_user['emails']) && is_array($pc_user['emails'])) {
                $pc_email = $pc_user['emails'][0];
            }
            if ($pc_email) {
                $response['current_email'] = $pc_email;
                $response['missing_email'] = false;
            }

            // Check Tên trên Pancake
            if ($pc_user['name'] && $pc_user['name'] !== $phone && stripos($pc_user['name'], 'Khách') === false) {
                $response['missing_name'] = false;
            }

            if ($local_user) {
                // TH1: Đã có Local + Pancake => ĐĂNG NHẬP
                $response['case'] = 1;
                if (!empty($local_user->email)) {
                    $response['missing_email'] = false; 
                    $response['current_email'] = $local_user->email;
                } else {
                    if ($pc_email) $response['missing_email'] = false;
                    else $response['missing_email'] = true;
                }
            } else {
                // TH2: Có Pancake + Chưa Local => KÍCH HOẠT
                $response['case'] = 2;
            }
        } else {
            // TH3: Chưa có Pancake => ĐĂNG KÝ MỚI
            $response['case'] = 3;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // B. Check trùng Email
    if ($_GET['api_action'] == 'check_email') {
        $email = sanitize_email($_GET['email']);
        $phone_exclude = sanitize_text_field($_GET['phone_exclude']);
        
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE email = %s AND phone != %s", 
            $email, $phone_exclude
        ));
        
        header('Content-Type: application/json');
        echo json_encode(['exists' => ($exists !== null)]);
        exit;
    }
}

// -----------------------------------------------------------------------------
// 3. XỬ LÝ POST: THỰC THI (LOGIN / UPDATE / CREATE)
// -----------------------------------------------------------------------------

if (isset($_SESSION['pancake_logged_in']) && $_SESSION['pancake_logged_in'] === true) {
    wp_redirect($caremil_account_url);
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pancake_customers';
    
    $phone = sanitize_text_field($_POST['phone_number']);
    $case = $_POST['auth_case'];
    $password = $_POST['password'];
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    // Lấy tên từ input customer_name để tránh lỗi 404
    $name = isset($_POST['customer_name']) ? sanitize_text_field($_POST['customer_name']) : '';
    $pancake_id = isset($_POST['pancake_id']) ? sanitize_text_field($_POST['pancake_id']) : '';
    $is_skip_email = isset($_POST['is_skip_email']) && $_POST['is_skip_email'] == '1';
    $is_reset = isset($_POST['is_reset']) && $_POST['is_reset'] == '1';

    // Clear email nếu chọn bỏ qua
    if ($is_skip_email) $email = '';

    // Validate Email Server-side
    if (!empty($email)) {
        $check = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE email = %s AND phone != %s", $email, $phone));
        if ($check) $error_message = "Email này đã được sử dụng.";
    }

    if (empty($error_message)) {
        
        // --- TH1: ĐĂNG NHẬP / ĐỔI MẬT KHẨU (reset khi đi qua OTP) ---
        if ($case == '1') {
            $db_user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE phone = %s", $phone));
            if ($db_user) {
                // Trường hợp reset mật khẩu sau khi OTP
                if ($is_reset) {
                    $wpdb->update($table_name, ['password' => wp_hash_password($password)], ['id' => $db_user->id]);
                    if (!empty($email)) {
                        $wpdb->update($table_name, ['email' => $email], ['id' => $db_user->id]);
                        if (!empty($pancake_id)) {
                            call_pancake_api('/customers/' . $pancake_id, ['customer' => ['emails' => [$email]]], 'PUT');
                        }
                    }
                    $_SESSION['pancake_logged_in'] = true;
                    $_SESSION['pancake_phone'] = $db_user->phone;
                    $_SESSION['pancake_name'] = $db_user->name;
                    $_SESSION['pancake_customer_id'] = $pancake_id;
                    wp_redirect($caremil_account_url);
                    exit;
                }
                // Đăng nhập thường
                if (wp_check_password($password, $db_user->password)) {
                    if (!empty($email)) {
                        $wpdb->update($table_name, ['email' => $email], ['id' => $db_user->id]);
                        if (!empty($pancake_id)) {
                            call_pancake_api('/customers/' . $pancake_id, ['customer' => ['emails' => [$email]]], 'PUT');
                        }
                    }
                    $_SESSION['pancake_logged_in'] = true;
                    $_SESSION['pancake_phone'] = $db_user->phone;
                    $_SESSION['pancake_name'] = $db_user->name;
                    $_SESSION['pancake_customer_id'] = $pancake_id;
                    wp_redirect($caremil_account_url);
                    exit;
                } else {
                    $error_message = "Mật khẩu không chính xác.";
                }
            }
        }

        // --- TH2: KÍCH HOẠT (Có Pancake -> Tạo Local -> Update Pancake nếu thiếu) ---
        elseif ($case == '2') {
            $final_name = !empty($name) ? $name : 'Khách hàng';
            
            // 1. Tạo User Local
            $wpdb->insert($table_name, [
                'phone' => $phone,
                'password' => wp_hash_password($password),
                'name' => $final_name,
                'email' => $email,
                'pancake_id' => $pancake_id
            ]);

            // 2. Đồng bộ ngược lên Pancake
            $update_data = [];
            if (!empty($email)) $update_data['emails'] = [$email];
            if (!empty($name)) $update_data['name'] = $name;

            if (!empty($update_data) && !empty($pancake_id)) {
                call_pancake_api('/customers/' . $pancake_id, ['customer' => $update_data], 'PUT');
            }

            if ($wpdb->insert_id) {
                $_SESSION['pancake_logged_in'] = true;
                $_SESSION['pancake_phone'] = $phone;
                $_SESSION['pancake_name'] = $final_name;
                $_SESSION['pancake_customer_id'] = $pancake_id;
                wp_redirect($caremil_account_url);
                exit;
            }
        }

        // --- TH3: TẠO MỚI (Tạo Pancake -> Tạo Local) ---
        elseif ($case == '3') {
            $final_name = !empty($name) ? $name : 'Khách ' . $phone;

            // 1. Tạo trên Pancake (FIXED PARAMS)
            // Theo tài liệu, create dùng camelCase: phoneNumber, createType
            $create_params = [
                'name' => $final_name,
                'phoneNumber' => $phone, // Correct: camelCase
                'createType' => 'force'  // Correct: camelCase
            ];
            
            $new_cus = call_pancake_api('/customers', $create_params, 'POST');

            if (isset($new_cus['success']) && $new_cus['success'] == true) {
                $new_pancake_id = $new_cus['data']['id'];

                // 2. Update Email lên Pancake ngay lập tức (vì API create có thể k lưu email)
                if (!empty($email)) {
                    call_pancake_api('/customers/' . $new_pancake_id, [
                        'customer' => ['emails' => [$email]]
                    ], 'PUT');
                }

                // 3. Tạo Local
                $wpdb->insert($table_name, [
                    'phone' => $phone,
                    'password' => wp_hash_password($password),
                    'name' => $final_name,
                    'email' => $email,
                    'pancake_id' => $new_pancake_id
                ]);

                if ($wpdb->insert_id) {
                    $_SESSION['pancake_logged_in'] = true;
                    $_SESSION['pancake_phone'] = $phone;
                    $_SESSION['pancake_name'] = $final_name;
                    $_SESSION['pancake_customer_id'] = $new_pancake_id;
                    wp_redirect($caremil_account_url);
                    exit;
                }
            } else {
                // Lấy thông báo lỗi cụ thể từ Pancake để hiển thị
                $api_error_msg = isset($new_cus['message']) ? $new_cus['message'] : 'Không xác định';
                $error_message = "Không thể tạo khách hàng trên hệ thống POS. Lỗi: " . $api_error_msg;
            }
        }
    }
}

get_header(); 
?>

<!-- FRONTEND UI (Visual nâng cao) -->
<script src="https://unpkg.com/lucide@latest"></script>
<!-- Fonts thêm cho visual -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Manrope:wght@400;500;600&display=swap&subset=vietnamese" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    .grain-overlay {
        position: fixed; inset: 0; pointer-events: none; z-index: 0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.04'/%3E%3C/svg%3E");
        mix-blend-mode: multiply;
    }
    .art-card {
        box-shadow: 0 24px 60px -25px rgba(26, 79, 138, 0.25);
        backdrop-filter: blur(6px);
    }
    .art-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 10px; border-radius: 999px;
        background: #f1f5f9; color: #0f172a; font-weight: 600; font-size: 12px;
        box-shadow: inset 0 0 0 1px rgba(15,23,42,0.04);
    }
    .art-chip i { width: 14px; height: 14px; }
    .art-hero-blur {
        position: absolute; inset: -20%; background: radial-gradient(circle at 30% 30%, rgba(255,209,102,0.25), transparent 40%), radial-gradient(circle at 80% 70%, rgba(76,201,240,0.18), transparent 35%);
        filter: blur(60px); z-index: 0;
    }

    /* CAPTCHA artistic styling */
    :root {
        --captcha-bg: #f8fafc;
        --captcha-border: #cbd5e1;
        --captcha-focus: #1a4f8a;
        --captcha-accent: #4cc9f0;
        --captcha-text: #1a4f8a;
        --captcha-placeholder: #94a3b8;
    }
    .captcha-wrapper {
        background-color: var(--captcha-bg);
        border: 1px dashed var(--captcha-border);
        border-radius: 12px;
        padding: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        position: relative;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        font-family: 'Manrope', sans-serif;
        max-width: 420px;
    }
    .captcha-wrapper:focus-within {
        border-color: var(--captcha-focus);
        box-shadow: 0 0 0 3px rgba(26, 79, 138, 0.1);
    }
    .captcha-input-container {
        flex-grow: 1;
        position: relative;
        height: 48px;
    }
    .captcha-input {
        width: 100%;
        height: 100%;
        padding: 0 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 16px;
        outline: none;
        text-align: center;
        letter-spacing: 2px;
        font-weight: 700;
        color: var(--captcha-text);
        background: #fff;
        transition: all 0.2s;
        box-sizing: border-box;
    }
    .captcha-input::placeholder {
        color: var(--captcha-placeholder);
        font-family: 'Manrope', sans-serif;
        font-weight: 500;
        letter-spacing: 0;
        font-size: 14px;
        text-transform: none;
    }
    .captcha-input:focus {
        border-color: var(--captcha-focus);
        box-shadow: inset 0 0 0 1px rgba(26, 79, 138, 0.1);
    }
    .captcha-status {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1rem;
        display: none;
    }
    .status-success { color: #10b981; display: block; }
    .status-error { color: #ef4444; display: block; }
    .canvas-container {
        position: relative;
        flex-shrink: 0;
        cursor: pointer;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background-color: #fff;
        width: 130px;
        height: 48px;
        transition: all 0.2s;
    }
    .canvas-container:hover {
        border-color: var(--captcha-accent);
        opacity: 0.9;
    }
    canvas.captcha-canvas {
        display: block;
        width: 100%;
        height: 100%;
    }
    .refresh-hint {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        font-size: 10px;
        color: var(--captcha-focus);
        pointer-events: none;
        text-transform: uppercase;
        font-weight: 800;
        background: rgba(255,255,255,0.9);
        padding: 4px 8px;
        border-radius: 4px;
        opacity: 0;
        transition: opacity 0.2s;
        white-space: nowrap;
    }
    .canvas-container:hover .refresh-hint { opacity: 1; }
    @media (max-width: 350px) {
        .captcha-wrapper { flex-wrap: wrap; }
        .canvas-container, .captcha-input-container { width: 100%; flex-basis: 100%; }
    }
</style>

<div class="relative min-h-screen bg-gradient-to-br from-[#f8fafc] via-[#f1f5f9] to-[#eef2ff] overflow-hidden">
    <div class="grain-overlay"></div>
    <div class="absolute top-[-10%] right-[-5%] w-[520px] h-[520px] bg-[#ffd166]/20 rounded-full blur-[90px]"></div>
    <div class="absolute bottom-[-12%] left-[-8%] w-[520px] h-[520px] bg-[#4cc9f0]/18 rounded-full blur-[90px]"></div>

    <div class="relative z-10 max-w-6xl mx-auto px-4 lg:px-8 py-10">
        <div class="art-card bg-white/90 border border-slate-100 rounded-[32px] overflow-hidden flex flex-col lg:flex-row">
            
            <!-- LEFT VISUAL (ẩn trên mobile) -->
            <div class="hidden lg:block lg:w-1/2 relative bg-[#0f2f57] text-white overflow-hidden">
                <div class="art-hero-blur"></div>
                <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/young-mother-spending-time-with-her-baby-scaled.jpg" class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-luminosity" alt="CareMIL">
                <div class="relative z-10 p-12 h-full flex flex-col justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full backdrop-blur text-sm font-semibold">
                            <i data-lucide="leaf" class="w-4 h-4 text-[#ffd166]"></i>
                            CareMIL Member
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="w-12 h-1 bg-[#ffd166]"></div>
                        <h2 class="text-5xl font-serif leading-tight">
                            Nuôi dưỡng <br>
                            <span class="italic text-[#ffd166] text-6xl">yêu thương</span><br>
                            từ gốc rễ.
                        </h2>
                        <p class="text-blue-100/90 text-lg max-w-md leading-relaxed">
                            Tham gia cộng đồng CareMIL để nhận kiến thức khoa học và ưu đãi dành riêng cho bé.
                        </p>
                    </div>
                    <div class="text-xs uppercase tracking-[0.2em] text-blue-100/70">
                        © 2024 DawnBridge Malaysia
                    </div>
                </div>
            </div>

            <!-- RIGHT FORM -->
            <div class="w-full lg:w-1/2 relative p-7 md:p-10 lg:p-12 bg-white">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <div class="text-sm font-semibold text-[#1a4f8a]">CareMIL Portal</div>
                        <h2 class="text-3xl font-serif text-slate-900 mt-1">Chào mừng bạn</h2>
                        <p class="text-sm text-slate-500">Đăng nhập hoặc xác minh để tiếp tục.</p>
                    </div>
                    <div id="step-chip" class="art-chip">
                        <i data-lucide="log-in"></i>
                        <span>Đăng nhập</span>
                    </div>
                </div>

            <!-- ERROR BOX -->
            <?php if ($error_message): ?>
                <div class="bg-red-50/90 text-red-700 p-3 rounded-xl mb-4 text-sm flex items-center gap-2 border border-red-100">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form id="main-form" method="POST" action="">
                <input type="hidden" name="phone_number" id="hidden_phone">
                <input type="hidden" name="auth_case" id="auth_case">
                <input type="hidden" name="pancake_id" id="hidden_pancake_id">
                <input type="hidden" name="is_skip_email" id="is_skip_email" value="0">
                <input type="hidden" name="is_reset" id="is_reset" value="0">

                <!-- STEP 0: LOGIN QUICK -->
                <div id="view-login">
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        SĐT / Email / Tên đăng nhập
                    </label>
                    <input 
                        type="text" 
                        id="input-identifier" 
                        class="block w-full px-4 py-3 mb-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-blue focus:border-brand-blue text-sm placeholder:text-gray-400" 
                        placeholder="Nhập SĐT hoặc email đã dùng" 
                        required
                    >

                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        Mật khẩu
                    </label>
                    <input 
                        type="password" 
                        id="input-password-login" 
                        class="block w-full px-4 py-3 mb-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-blue focus:border-brand-blue text-sm" 
                        placeholder="Nhập mật khẩu"
                        required
                    >
                    <!-- Error message dưới input mật khẩu -->
                    <div id="login-error" class="hidden mb-3"></div>

                    <!-- Captcha placeholder -->
                    <div class="mb-4">
                        <div class="captcha-wrapper" id="captcha-login-wrapper">
                            <div class="captcha-input-container">
                                <input 
                                    type="text" 
                                    id="captcha-login-input" 
                                    class="captcha-input" 
                                    placeholder="Nhập mã xác thực" 
                                    autocomplete="off" 
                                    required
                                >
                                <i class="fas fa-check-circle captcha-status" id="captcha-login-status"></i>
                            </div>
                            <div class="canvas-container" title="Chạm để đổi mã mới" onclick="window.generateCaptcha('login')">
                                <canvas id="captcha-login-canvas" class="captcha-canvas" width="130" height="48"></canvas>
                                <span class="refresh-hint">Chạm đổi mã</span>
                            </div>
                        </div>
                        <p id="captcha-login-msg" class="mt-2 text-xs text-gray-500">Nhập mã để tiếp tục.</p>
                    </div>

                    <button 
                        type="button" 
                        onclick="attemptLogin()" 
                        id="btn-login" 
                        class="w-full py-3 px-4 rounded-xl shadow-md text-sm font-semibold text-white bg-gradient-to-r from-brand-navy to-brand-blue hover:from-brand-blue hover:to-brand-navy transition-all flex justify-center items-center gap-2"
                    >
                        <span>Đăng nhập</span>
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                    </button>

                    <div class="mt-3 text-xs text-gray-600 flex items-center justify-between">
                        <button type="button" onclick="startOtpFlow()" class="text-brand-navy hover:underline">Quên mật khẩu</button>
                        <span class="text-gray-400">Hoặc</span>
                    </div>
                    
                    <!-- Nút Đăng ký ngay -->
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500 text-center mb-2">
                            Chưa có tài khoản?
                        </p>
                        <button 
                            type="button" 
                            onclick="goToRegisterFlow()" 
                            class="w-full py-2 px-4 rounded-xl border-2 border-brand-gold text-sm font-semibold text-brand-navy hover:bg-brand-gold/10 transition-all flex justify-center items-center gap-2"
                        >
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                            <span>Đăng ký tài khoản ngay</span>
                        </button>
                    </div>
                </div>

                <!-- STEP 1: PHONE (OTP flow) -->
                <div id="view-phone" class="hidden mt-6">
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        Số điện thoại
                    </label>
                    <p class="text-xs text-gray-500 mb-3">
                        Dùng số bạn đã đăng ký với Caremil để xác minh OTP và bảo vệ tài khoản.
                    </p>
                    <div class="relative mb-4">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </div>
                        <input 
                            type="tel" 
                            id="input-phone" 
                            class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-blue focus:border-brand-blue text-sm placeholder:text-gray-400" 
                            placeholder="Nhập số điện thoại (VD: 0912345678)" 
                        >
                    </div>
                    <button 
                        type="button" 
                        onclick="processPhone()" 
                        id="btn-next" 
                        class="w-full py-3 px-4 rounded-xl shadow-md text-sm font-semibold text-white bg-gradient-to-r from-brand-navy to-brand-blue hover:from-brand-blue hover:to-brand-navy transition-all flex justify-center items-center gap-2"
                    >
                        <span>Gửi mã OTP</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                    
                    <!-- Nút Quay lại đăng nhập -->
                    <button 
                        type="button" 
                        onclick="resetToLogin()" 
                        class="mt-3 w-full py-2 px-4 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-all flex justify-center items-center gap-2"
                    >
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        <span>Quay lại đăng nhập</span>
                    </button>
                    
                    <p class="mt-3 text-[11px] text-gray-400 text-center flex items-center justify-center gap-1">
                        <i data-lucide="shield" class="w-3 h-3"></i>
                        Caremil cam kết bảo mật tuyệt đối thông tin của bạn.
                    </p>
                </div>

                <!-- STEP 2: OTP -->
                <div id="view-otp" class="hidden">
                    <div class="text-center mb-6">
                        <h3 class="text-sm font-semibold text-gray-800 mb-1">
                            Nhập mã xác minh
                        </h3>
                        <p class="text-xs text-gray-500">
                            Mã OTP (demo: <span class="font-mono font-semibold">123456</span>) đã gửi đến số
                            <span class="font-semibold text-brand-navy" id="lbl-phone"></span>
                        </p>
                    </div>
                    <div class="mb-4">
                        <input 
                            type="text" 
                            id="input-otp" 
                            class="block w-full text-center py-3 border-2 border-gray-200 rounded-2xl text-2xl tracking-[0.5em] font-bold focus:border-brand-blue focus:ring-1 focus:ring-brand-blue outline-none"
                            placeholder="••••••" 
                            maxlength="6"
                        >
                        <p id="otp-error" class="text-red-500 text-xs mt-2 text-center hidden">
                            Mã OTP không chính xác. Vui lòng kiểm tra lại.
                        </p>
                    </div>
                    <button 
                        type="button" 
                        onclick="verifyOTP()" 
                        id="btn-verify" 
                        class="w-full py-3 px-4 rounded-xl shadow-md text-sm font-semibold text-white bg-brand-green hover:bg-emerald-600 transition-all flex items-center justify-center gap-2"
                    >
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                        <span>Xác thực</span>
                    </button>
                    
                    <!-- Nút Quay lại đăng nhập -->
                    <button 
                        type="button" 
                        onclick="resetToLogin()" 
                        class="mt-3 w-full py-2 px-4 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-all flex justify-center items-center gap-2"
                    >
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        <span>Quay lại đăng nhập</span>
                    </button>
                    
                    <div class="text-center mt-4 text-xs text-gray-500 flex flex-col gap-1">
                        <button 
                            type="button" 
                            onclick="goBackToPhone()" 
                            class="inline-flex items-center justify-center gap-1 text-xs text-gray-500 hover:text-gray-800"
                        >
                            <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                            Nhập lại số điện thoại
                        </button>
                        <span>
                            Không nhận được mã? Vui lòng chờ 1–2 phút hoặc kiểm tra lại số điện thoại.
                        </span>
                    </div>
                </div>

                <!-- STEP 3: INFO FORM (Dynamic) -->
                <div id="view-final" class="hidden animate-fade-in">
                    <!-- Welcome Box -->
                    <div class="bg-brand-soft p-4 rounded-2xl mb-6 border border-brand-blue/30 flex items-start gap-3">
                        <div class="w-9 h-9 rounded-2xl bg-white flex items-center justify-center shadow-sm">
                            <i data-lucide="user" class="w-4 h-4 text-brand-navy"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-brand-navy text-sm flex items-center gap-1.5">
                            Xin chào <span id="user-name"></span>
                            </h3>
                            <p class="text-xs text-brand-navy/80 mt-1" id="status-text"></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Password Field -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1" id="lbl-password">Mật khẩu</label>
                            <input 
                                type="password" 
                                name="password" 
                                id="input-password" 
                                class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-blue focus:border-brand-blue text-sm" 
                                placeholder="Nhập mật khẩu của bạn"
                            >
                        </div>

                        <!-- Name Field (Ẩn/Hiện tùy Case) -->
                        <div id="field-name" class="hidden">
                            <label class="block text-sm font-semibold text-gray-800 mb-1">Họ và tên</label>
                            <!-- FIX 404: Đổi name="name" -> name="customer_name" -->
                            <input 
                                type="text" 
                                name="customer_name" 
                                id="input-name" 
                                class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-blue focus:border-brand-blue text-sm" 
                                placeholder="Nhập họ và tên đầy đủ"
                            >
                        </div>

                        <!-- Email Field (Ẩn/Hiện tùy Case) -->
                        <div id="field-email" class="hidden">
                            <label class="block text-sm font-semibold text-gray-800 mb-1">
                                Email <span class="text-gray-400 font-normal">(không bắt buộc)</span>
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                id="input-email" 
                                class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-blue focus:border-brand-blue text-sm" 
                                placeholder="Ví dụ: ban@caremil.com"
                            >
                            <p id="email-error" class="text-xs text-red-500 mt-1 hidden"></p>
                            <p class="text-[11px] text-orange-500 mt-1 flex items-start gap-1">
                                <i data-lucide="shield-check" class="w-3 h-3 mt-0.5 shrink-0"></i>
                                Cập nhật email để nhận ưu đãi, hóa đơn điện tử và hỗ trợ nhanh hơn.
                            </p>
                        </div>
                    </div>

                    <!-- Captcha placeholder cho bước cuối -->
                    <div class="mt-4">
                        <div class="captcha-wrapper" id="captcha-final-wrapper">
                            <div class="captcha-input-container">
                                <input 
                                    type="text" 
                                    id="captcha-final-input" 
                                    name="captcha_final" 
                                    class="captcha-input" 
                                    placeholder="Nhập mã xác thực" 
                                    autocomplete="off"
                                >
                                <i class="fas fa-check-circle captcha-status" id="captcha-final-status"></i>
                            </div>
                            <div class="canvas-container" title="Chạm để đổi mã mới" onclick="window.generateCaptcha('final')">
                                <canvas id="captcha-final-canvas" class="captcha-canvas" width="130" height="48"></canvas>
                                <span class="refresh-hint">Chạm đổi mã</span>
                            </div>
                        </div>
                        <p id="captcha-final-msg" class="mt-2 text-xs text-gray-500">Nhập mã để tiếp tục.</p>
                    </div>

                    <!-- Buttons -->
                    <div class="mt-6 flex flex-col gap-3">
                        <button 
                            type="button" 
                            onclick="validateAndSubmit(false)" 
                            id="btn-submit-main" 
                            class="w-full py-3 px-4 rounded-xl shadow-md text-sm font-semibold text-white bg-gradient-to-r from-brand-navy to-brand-blue hover:from-brand-blue hover:to-brand-navy transition-all"
                        >
                            Xác Nhận
                        </button>
                        
                        <!-- Nút Bỏ Qua (Chỉ hiện khi TH1 thiếu email) -->
                        <button 
                            type="button" 
                            onclick="validateAndSubmit(true)" 
                            id="btn-skip" 
                            class="hidden w-full py-3 px-4 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all flex items-center justify-center gap-2"
                        >
                            <i data-lucide="log-in" class="w-4 h-4"></i>
                            <span>Bỏ qua, đăng nhập ngay</span>
                        </button>
                        
                        <!-- Nút Quay lại đăng nhập -->
                        <button 
                            type="button" 
                            onclick="resetToLogin()" 
                            class="w-full py-2 px-4 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-all flex justify-center items-center gap-2"
                        >
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            <span>Quay lại đăng nhập</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    function setStepChip(label = 'Đăng nhập', icon = 'log-in') {
        const chip = document.getElementById('step-chip');
        if (!chip) return;
        chip.innerHTML = `<i data-lucide="${icon}" class="w-4 h-4"></i><span>${label}</span>`;
        lucide.createIcons();
    }

    // CAPTCHA LOGIC (dùng chung cho login và final)
    const captchaStore = {};
    function generateRandomString(length) {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        let res = '';
        for (let i = 0; i < length; i++) {
            res += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return res;
    }
    function generateCaptcha(prefix) {
        const canvas = document.getElementById(`captcha-${prefix}-canvas`);
        const input = document.getElementById(`captcha-${prefix}-input`);
        const msg = document.getElementById(`captcha-${prefix}-msg`);
        const wrapper = document.getElementById(`captcha-${prefix}-wrapper`);
        const statusIcon = document.getElementById(`captcha-${prefix}-status`);
        if (!canvas) {
            console.warn(`Canvas not found: captcha-${prefix}-canvas`);
            return;
        }
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.warn(`Cannot get 2d context for canvas: captcha-${prefix}-canvas`);
            return;
        }
        const width = canvas.width;
        const height = canvas.height;
        ctx.clearRect(0, 0, width, height);
        ctx.fillStyle = '#f3f4f6';
        ctx.fillRect(0, 0, width, height);

        const code = generateRandomString(4); // dùng 4 ký tự cho captcha
        captchaStore[prefix] = code;

        for (let i = 0; i < 7; i++) {
            ctx.strokeStyle = `rgba(${Math.random()*255}, ${Math.random()*255}, ${Math.random()*255}, 0.5)`;
            ctx.lineWidth = 1 + Math.random();
            ctx.beginPath();
            ctx.moveTo(Math.random() * width, Math.random() * height);
            ctx.lineTo(Math.random() * width, Math.random() * height);
            ctx.stroke();
        }
        for (let i = 0; i < 50; i++) {
            ctx.fillStyle = `rgba(${Math.random()*100}, ${Math.random()*100}, ${Math.random()*100}, 0.2)`;
            ctx.beginPath();
            ctx.arc(Math.random() * width, Math.random() * height, 1.5, 0, 2 * Math.PI);
            ctx.fill();
        }
        ctx.font = 'bold 32px "Courier New", monospace';
        ctx.textBaseline = 'middle';
        for (let i = 0; i < code.length; i++) {
            ctx.save();
            const x = 18 + (i * 26); // canh giữa cho 4 ký tự trong canvas 130px
            const y = height / 2;
            ctx.translate(x, y);
            const rotation = (Math.random() - 0.5) * 0.4;
            ctx.rotate(rotation);
            ctx.fillStyle = Math.random() > 0.5 ? '#1a4f8a' : '#c59d5f';
            ctx.fillText(code[i], 0, 0);
            ctx.restore();
        }
        if (input) {
            input.value = '';
            input.style.borderColor = '#e2e8f0';
        }
        if (wrapper) {
            wrapper.style.borderColor = '#cbd5e1';
            wrapper.style.transform = 'translateX(0)';
        }
        if (statusIcon) {
            statusIcon.className = 'fas fa-check-circle captcha-status';
            statusIcon.style.display = 'none';
        }
        if (msg) {
            msg.textContent = 'Nhập 4 ký tự như hình (không phân biệt hoa/thường).';
            msg.className = 'mt-2 text-xs text-gray-500';
        }
    }
    function speakCaptcha(prefix) {
        const code = captchaStore[prefix] || '';
        if (!code) return;
        if ('speechSynthesis' in window) {
            const utterance = new SpeechSynthesisUtterance(code.split('').join(' '));
            utterance.rate = 0.8;
            window.speechSynthesis.speak(utterance);
        } else {
            alert('Trình duyệt không hỗ trợ đọc mã.');
        }
    }
    function validateCaptcha(prefix) {
        const code = captchaStore[prefix];
        const input = document.getElementById(`captcha-${prefix}-input`);
        const msg = document.getElementById(`captcha-${prefix}-msg`);
        const wrapper = document.getElementById(`captcha-${prefix}-wrapper`);
        const statusIcon = document.getElementById(`captcha-${prefix}-status`);
        if (!input) return true;
        const val = (input.value || '').trim();
        const ok = code && val && code.toLowerCase() === val.toLowerCase();
        if (!ok) {
            if (msg) {
                msg.textContent = 'Sai mã, vui lòng thử lại.';
                msg.className = 'mt-2 text-xs text-red-500';
            }
            input.style.borderColor = '#ef4444';
            if (wrapper) {
                wrapper.style.borderColor = '#ef4444';
                wrapper.style.transform = 'translateX(5px)';
                setTimeout(() => wrapper.style.transform = 'translateX(-5px)', 100);
                setTimeout(() => wrapper.style.transform = 'translateX(0)', 200);
            }
            if (statusIcon) {
                statusIcon.className = 'fas fa-times-circle captcha-status status-error';
                statusIcon.style.display = 'block';
            }
            setTimeout(() => generateCaptcha(prefix), 800);
            return false;
        }
        input.style.borderColor = '#10b981';
        if (wrapper) {
            wrapper.style.borderColor = '#10b981';
            wrapper.style.transform = 'translateX(0)';
        }
        if (msg) {
            msg.textContent = 'Xác thực thành công.';
            msg.className = 'mt-2 text-xs text-emerald-600';
        }
        if (statusIcon) {
            statusIcon.className = 'fas fa-check-circle captcha-status status-success';
            statusIcon.style.display = 'block';
        }
        return true;
    }
    function initCaptchas() {
        ['login', 'final'].forEach(prefix => {
            const canvas = document.getElementById(`captcha-${prefix}-canvas`);
            if (canvas) {
                generateCaptcha(prefix);
                canvas.addEventListener('click', () => generateCaptcha(prefix));
            } else {
                console.warn(`Captcha canvas not found for prefix: ${prefix}`);
            }
        });
    }

    // Expose functions to global scope để có thể gọi từ onclick inline
    window.startOtpFlow = function() {
        const loginView = document.getElementById('view-login');
        const phoneView = document.getElementById('view-phone');
        const otpView = document.getElementById('view-otp');
        const finalView = document.getElementById('view-final');
        const idVal = (document.getElementById('input-identifier')?.value || '').trim();
        const idInput = document.getElementById('input-identifier');
        const passInput = document.getElementById('input-password-login');
        const captchaLogin = document.getElementById('captcha-login-input');

        // Ẩn tất cả các view khác
        if (loginView) loginView.classList.add('hidden');
        if (otpView) otpView.classList.add('hidden');
        if (finalView) finalView.classList.add('hidden');
        
        // Hiện view phone
        if (phoneView) {
            phoneView.classList.remove('hidden');
            phoneView.classList.remove('mt-6'); // Đảm bảo không có margin-top khi hiện
        } else {
            console.error('view-phone not found!');
            return;
        }
        
        if (idInput) idInput.removeAttribute('required');
        if (passInput) passInput.removeAttribute('required');
        if (captchaLogin) captchaLogin.removeAttribute('required');
        const phoneInput = document.getElementById('input-phone');
        if (phoneInput) {
            phoneInput.setAttribute('required', 'required');
            // Clear phone input
            phoneInput.value = '';
        }

        // Nếu người dùng đã nhập SĐT, tự động điền sang bước OTP (sau khi clear)
        if (/^\d{8,}$/.test(idVal)) {
            if (phoneInput) {
                phoneInput.value = idVal;
            }
        }

        setStepChip('Xác minh OTP', 'key-round');
    };

    // Expose attemptLogin to global for inline onclick
    window.attemptLogin = async function() {
        const identifier = document.getElementById('input-identifier').value.trim();
        const password = document.getElementById('input-password-login').value;
        const btn = document.getElementById('btn-login');
        const errorDiv = document.getElementById('login-error');

        if (!identifier || !password) {
            showLoginError('Vui lòng nhập đầy đủ thông tin.', 'warning');
            return;
        }
        if (!validateCaptcha('login')) {
            return;
        }

        // Ẩn error cũ
        if (errorDiv) {
            errorDiv.classList.add('hidden');
        }

        btn.disabled = true;
        const oldHtml = btn.innerHTML;
        btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Đang kiểm tra...';
        lucide.createIcons();

        try {
            const res = await fetch(`?api_action=fast_login&id=${encodeURIComponent(identifier)}&password=${encodeURIComponent(password)}`);
            const data = await res.json();

            if (data.success && data.redirect) {
                setStepChip('Đăng nhập', 'check');
                window.location.href = data.redirect;
                return;
            }

            // Hiển thị message theo case
            let actionBtn = '';
            if (data.case === 'wrong_password') {
                showLoginError('Mật khẩu chưa đúng. Vui lòng kiểm tra lại.', 'error');
                actionBtn = '<button type="button" onclick="window.startOtpFlow()" class="mt-2 text-sm text-brand-navy hover:underline font-semibold">Quên mật khẩu?</button>';
            } else if (data.case === 'no_password') {
                showLoginError('Tài khoản chưa có mật khẩu. Tạo mật khẩu ngay.', 'info');
                actionBtn = '<button type="button" onclick="window.startOtpFlow()" class="mt-2 text-sm text-brand-navy hover:underline font-semibold">Xác minh OTP để tạo mật khẩu</button>';
            } else if (data.case === 'no_account') {
                showLoginError('Tài khoản chưa được kích hoạt', 'info');
                actionBtn = '<button type="button" onclick="window.goToRegisterFlow()" class="mt-2 text-sm text-brand-navy hover:underline font-semibold">Tiến hành kích hoạt</button>';
            }

            // Thêm nút action vào error div
            if (errorDiv && actionBtn) {
                let actionContainer = errorDiv.querySelector('.action-container');
                if (actionContainer) {
                    actionContainer.innerHTML = actionBtn;
                } else {
                    errorDiv.insertAdjacentHTML('beforeend', '<div class="action-container">' + actionBtn + '</div>');
                }
            }

            // Pre-fill phone nếu có
            if (data.phone && document.getElementById('input-phone')) {
                document.getElementById('input-phone').value = data.phone;
            } else if (/^\d{8,}$/.test(identifier)) {
                document.getElementById('input-phone').value = identifier;
            }
        } catch (e) {
            showLoginError('Lỗi kết nối. Vui lòng thử lại.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = oldHtml;
        }
    }

    function showLoginError(message, type = 'error') {
        const errorDiv = document.getElementById('login-error');
        if (!errorDiv) return;

        const bgColor = type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 
                        type === 'info' ? 'bg-blue-50 border-blue-200 text-blue-700' : 
                        'bg-amber-50 border-amber-200 text-amber-700';
        
        errorDiv.className = `${bgColor} p-3 rounded-xl border flex items-start gap-2 text-sm`;
        errorDiv.innerHTML = `
            <i data-lucide="${type === 'error' ? 'alert-circle' : 'info'}" class="w-4 h-4 mt-0.5 flex-shrink-0"></i>
            <div class="flex-1">
                <p>${message}</p>
                <div class="action-container"></div>
            </div>
        `;
        errorDiv.classList.remove('hidden');
        lucide.createIcons();
    }

    // Hàm reset về màn hình đăng nhập ban đầu
    window.resetToLogin = function() {
        // Ẩn tất cả các view khác
        document.getElementById('view-phone')?.classList.add('hidden');
        document.getElementById('view-otp')?.classList.add('hidden');
        document.getElementById('view-final')?.classList.add('hidden');
        
        // Hiện lại view login
        document.getElementById('view-login')?.classList.remove('hidden');
        
        
        // Reset captcha
        if (document.getElementById('captcha-login-canvas')) {
            generateCaptcha('login');
        }
        
        // Reset error messages
        const errorDiv = document.getElementById('login-error');
        if (errorDiv) errorDiv.classList.add('hidden');
        
        // Reset step chip
        setStepChip('Đăng nhập', 'log-in');
        
        // Reset form state
        document.getElementById('auth_case').value = '';
        document.getElementById('hidden_phone').value = '';
        document.getElementById('hidden_pancake_id').value = '';
        document.getElementById('is_skip_email').value = '0';
        document.getElementById('is_reset').value = '0';
    }

    // Hàm quay lại bước nhập SĐT (từ OTP)
    window.goBackToPhone = function() {
        const otpView = document.getElementById('view-otp');
        const phoneView = document.getElementById('view-phone');
        const otpInput = document.getElementById('input-otp');
        const otpError = document.getElementById('otp-error');
        
        if (otpView) otpView.classList.add('hidden');
        if (phoneView) {
            phoneView.classList.remove('hidden');
            phoneView.classList.remove('mt-6');
        }
        if (otpInput) otpInput.value = '';
        if (otpError) otpError.classList.add('hidden');
        setStepChip('Xác minh OTP', 'key-round');
    }

    // Hàm chuyển sang flow đăng ký (từ màn hình login)
    window.goToRegisterFlow = function() {
        // Reset form login
        const idInput = document.getElementById('input-identifier');
        const passInput = document.getElementById('input-password-login');
        const errorDiv = document.getElementById('login-error');
        
        if (idInput) idInput.value = '';
        if (passInput) passInput.value = '';
        if (errorDiv) errorDiv.classList.add('hidden');
        
        // Chuyển sang flow OTP (sẽ tự động tạo tài khoản mới nếu chưa có)
        window.startOtpFlow();
        setStepChip('Đăng ký', 'user-plus');
    };

    // 1. Send OTP Mock
    window.processPhone = function() {
        const phoneInput = document.getElementById('input-phone');
        const phone = phoneInput ? phoneInput.value.trim() : '';
        
        if (!phone || phone.length < 9) { 
            alert('Số điện thoại không hợp lệ. Vui lòng kiểm tra lại.'); 
            return; 
        }
        
        const btn = document.getElementById('btn-next');
        const phoneView = document.getElementById('view-phone');
        const otpView = document.getElementById('view-otp');
        const lblPhone = document.getElementById('lbl-phone');
        const hiddenPhone = document.getElementById('hidden_phone');
        
        if (!btn || !phoneView || !otpView) {
            console.error('Required elements not found for processPhone');
            return;
        }
        
        btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Đang gửi mã...';
        btn.disabled = true;
        lucide.createIcons();

        setTimeout(() => {
            phoneView.classList.add('hidden');
            otpView.classList.remove('hidden');
            if (lblPhone) lblPhone.innerText = phone;
            if (hiddenPhone) hiddenPhone.value = phone;
            alert("OTP: 123456");
            lucide.createIcons();
            setStepChip('Xác minh OTP', 'key-round');
        }, 800);
    }

    // 2. Verify & Check Case
    window.verifyOTP = async function() {
        const otpInput = document.getElementById('input-otp');
        const hiddenPhone = document.getElementById('hidden_phone');
        const otp = otpInput ? otpInput.value.trim() : '';
        const phone = hiddenPhone ? hiddenPhone.value : '';
        
        const otpError = document.getElementById('otp-error');
        if (otpError) otpError.classList.add('hidden');

        if (otp !== '123456') { 
            if (otpError) otpError.classList.remove('hidden'); 
            return; 
        }
        
        const btnVerify = document.getElementById('btn-verify');
        const otpView = document.getElementById('view-otp');
        const finalView = document.getElementById('view-final');
        
        if (!btnVerify || !otpView || !finalView) {
            console.error('Required elements not found for verifyOTP');
            return;
        }
        
        btnVerify.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Đang kiểm tra...';
        btnVerify.disabled = true;
        lucide.createIcons();

        try {
            const res = await fetch(`?api_action=check_status&phone=${encodeURIComponent(phone)}`);
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await res.json();

            // Set Data
            const userName = document.getElementById('user-name');
            const authCase = document.getElementById('auth_case');
            const hiddenPancakeId = document.getElementById('hidden_pancake_id');
            
            if (userName) userName.innerText = data.welcome_name || 'Quý Khách';
            if (authCase) authCase.value = data.case || '3';
            if (hiddenPancakeId) hiddenPancakeId.value = data.pancake_id || '';

            // Switch View
            otpView.classList.add('hidden');
            finalView.classList.remove('hidden');

            // Reset base states
            document.getElementById('btn-skip').classList.add('hidden');
            document.getElementById('field-name').classList.add('hidden');
            document.getElementById('field-email').classList.add('hidden');
            document.getElementById('input-name').required = false;
            document.getElementById('is_reset').value = '0';

            // --- DISPLAY LOGIC ---
            // TH1: Đổi mật khẩu (đã có tài khoản + mật khẩu)
            if (data.case == 1) {
                document.getElementById('is_reset').value = '1';
                document.getElementById('lbl-password').innerText = "Tạo mật khẩu mới";
                document.getElementById('status-text').innerText = "Xác minh OTP và đặt mật khẩu mới để tiếp tục.";
                document.getElementById('btn-submit-main').innerText = "Cập nhật & Đăng nhập";
                setStepChip('Đặt mật khẩu mới', 'key-round');

                if (data.missing_email) {
                    document.getElementById('field-email').classList.remove('hidden');
                }
            } 
            
            // TH2: ACTIVATE (Có Pancake - Thiếu Pass WP)
            else if (data.case == 2) {
                document.getElementById('lbl-password').innerText = "Tạo mật khẩu mới";
                document.getElementById('status-text').innerText = "Kích hoạt tài khoản thành viên.";
                document.getElementById('btn-submit-main').innerText = "Kích Hoạt Tài Khoản";
                setStepChip('Kích hoạt tài khoản', 'shield-check');
                
                if (data.missing_name) {
                    document.getElementById('field-name').classList.remove('hidden');
                    document.getElementById('input-name').required = true;
                }
                if (data.missing_email) {
                    document.getElementById('field-email').classList.remove('hidden');
                }
                // Điền sẵn tên nếu có
                if (data.current_name) document.getElementById('input-name').value = data.current_name;
            }

            // TH3: REGISTER (Mới tinh)
            else if (data.case == 3) {
                document.getElementById('lbl-password').innerText = "Tạo mật khẩu mới";
                document.getElementById('status-text').innerText = "Chào bạn mới! Đăng ký ngay.";
                document.getElementById('btn-submit-main').innerText = "Đăng Ký Thành Viên";
                setStepChip('Đăng ký thành viên', 'sparkles');
                
                document.getElementById('field-name').classList.remove('hidden');
                document.getElementById('input-name').required = true;
                document.getElementById('field-email').classList.remove('hidden');
            }

        } catch (e) {
            console.error('Error in verifyOTP:', e);
            if (btnVerify) {
                btnVerify.disabled = false;
                btnVerify.innerHTML = '<i data-lucide="check-circle-2" class="w-4 h-4"></i><span>Xác thực</span>';
                lucide.createIcons();
            }
            alert("Lỗi kết nối! Vui lòng thử lại.");
        }
    }

    // 3. Final Submit
    window.validateAndSubmit = async function(isSkip = false) {
        const pass = document.getElementById('input-password').value;
        const emailInput = document.getElementById('input-email');
        const emailVal = emailInput.value;
        const phone = document.getElementById('hidden_phone').value;
        const errorEl = document.getElementById('email-error');
        const btnMain = document.getElementById('btn-submit-main');

        // Validate Pass
        if (!pass) { alert("Vui lòng nhập mật khẩu"); return; }
        if (!validateCaptcha('final')) { return; }

        // Logic Skip (TH1)
        if (isSkip) {
            document.getElementById('is_skip_email').value = '1';
            document.getElementById('main-form').submit();
            return;
        }

        // Logic Email Check
        if (!document.getElementById('field-email').classList.contains('hidden') && emailVal) {
            errorEl.classList.add('hidden');
            emailInput.classList.remove('border-red-500');
            
            btnMain.disabled = true;
            btnMain.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Đang kiểm tra...';
            lucide.createIcons();
            
            try {
                const res = await fetch(`?api_action=check_email&email=${encodeURIComponent(emailVal)}&phone_exclude=${phone}`);
                const data = await res.json();
                
                if (data.exists) {
                    errorEl.innerText = "Email này đã được sử dụng.";
                    errorEl.classList.remove('hidden');
                    emailInput.classList.add('border-red-500');
                    btnMain.disabled = false;
                    btnMain.innerText = "Thử lại";
                    lucide.createIcons();
                    return;
                }
            } catch(e) {
                btnMain.disabled = false;
                btnMain.innerText = "Xác Nhận";
                lucide.createIcons();
            }
        }

        document.getElementById('main-form').submit();
    }

    // Khởi tạo captcha khi load trang
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initCaptchas();
            setStepChip('Đăng nhập', 'log-in');
        });
    } else {
        // DOM đã sẵn sàng
        initCaptchas();
        setStepChip('Đăng nhập', 'log-in');
    }
</script>

<?php get_footer(); ?>