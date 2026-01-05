<?php
/**
 * Template Name: Order Status
 * Template Post Type: page
 * Description: Template for displaying order status page
 *
 * @package Caremil
 */
get_header();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trạng Thái Đơn Hàng - CareMIL</title>
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
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #f8fafc; }
        .confetti {
            position: absolute;
            width: 10px; height: 10px;
            background-color: #ffd166;
            animation: fall linear forwards;
        }
        @keyframes fall {
            to { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
        
        /* Timeline Connector */
        .timeline-connector {
            position: absolute;
            top: 24px;
            left: 0;
            width: 100%;
            height: 4px;
            background-color: #e2e8f0;
            z-index: 0;
        }
        .timeline-progress {
            position: absolute;
            top: 24px;
            left: 0;
            height: 4px;
            background-color: #4ade80;
            z-index: 0;
            width: 30%; /* Adjust based on status */
            border-radius: 4px;
        }
    </style>
</head>
<body class="text-gray-700 font-sans min-h-screen flex flex-col">


    <!-- HEADER -->
    <nav class="bg-white border-b border-gray-100 h-16 flex items-center sticky top-0 z-50">
        <div class="container mx-auto px-4 flex justify-between items-center max-w-5xl">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-2 group">
                <i class="fas fa-leaf text-brand-gold text-2xl group-hover:rotate-12 transition-transform"></i>
                <span class="text-xl font-display font-black text-brand-navy tracking-tight">Care<span class="text-brand-blue">MIL</span></span>
            </a>
            <a href="<?php echo esc_url( home_url( '/cua-hang' ) ); ?>" class="text-sm font-bold text-brand-blue hover:text-brand-navy transition">
                Tiếp tục mua sắm
            </a>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="flex-grow container mx-auto px-4 py-8 max-w-4xl relative">
        <?php
        $order_id = isset($_GET['order_id']) ? sanitize_text_field($_GET['order_id']) : '';
        $order_details = null;
        if ($order_id) {
            // Fetch order details
            $shop_id = caremil_get_pancake_shop_id();
            if ($shop_id) {
                // Try fetching by ID
                $res = caremil_pancake_request("/shops/{$shop_id}/orders/{$order_id}");
                if ($res && isset($res['data'])) {
                    $order_details = $res['data'];
                } elseif ($res && isset($res['id'])) {
                     $order_details = $res; // Fallback
                }
            }
        }
        
        $display_id = $order_details ? ($order_details['order_number'] ?? $order_details['id']) : ($order_id ? $order_id : '123456');
        
        // ----------------------------------------------------
        // 1. SMART STATUS MAPPING
        // ----------------------------------------------------
        $raw_status = isset($order_details['status']) ? intval($order_details['status']) : 1; 
        $status_name = $order_details['status_name'] ?? 'Mới'; 
        
        // Define Steps
        // 1: Placed, 2: Confirmed, 3: Packing/Ready, 4: Shipping, 5: Delivered
        $current_step = 1; 
        $display_status = $status_name;
        
        // --- LOGIC MAP ---
        
        // STEP 1: Default (Mới, Chờ hàng) - Always true if exists
        
        // STEP 2: Confirmed (Xác nhận, Submitted)
        if ($raw_status == 2 || stripos($status_name, 'Xác nhận') !== false || stripos($status_name, 'Confirmed') !== false || stripos($status_name, 'submitted') !== false) {
            $current_step = 2;
            $display_status = 'Đã Xác Nhận';
        }
        
        // STEP 3: Packing / Waiting for Transfer (Đang đóng hàng, Chờ chuyển)
        if (stripos($status_name, 'đóng hàng') !== false || stripos($status_name, 'packing') !== false || 
            stripos($status_name, 'chờ chuyển') !== false || stripos($status_name, 'pickup') !== false) {
            $current_step = 3;
            $display_status = 'Đang Đóng Gói';
        }

        // STEP 4: Shipping (Gửi hàng đi, Đang giao, Đã gửi hàng)
        if ($raw_status == 3 || $raw_status == 4 || 
            stripos($status_name, 'Gửi hàng') !== false || stripos($status_name, 'Đã gửi hàng') !== false || 
            stripos($status_name, 'shipping') !== false || stripos($status_name, 'Sending') !== false || 
            stripos($status_name, 'Đang giao') !== false) {
            $current_step = 4;
            $display_status = 'Đang Giao Hàng';
        }

        // STEP 5: Success (Đã giao, Hoàn thành, Đã thu tiền, received_money)
        if ($raw_status == 5 || $raw_status == 9 || 
            stripos($status_name, 'Đã giao') !== false || stripos($status_name, 'Success') !== false || 
            stripos($status_name, 'Delivered') !== false || stripos($status_name, 'thu tiền') !== false ||
            stripos($status_name, 'received_money') !== false) {
            $current_step = 5;
            $display_status = 'Giao Thành Công';
        }

        // CANCELLED Check
        $is_cancelled = false;
        if ($raw_status == 7 || stripos($status_name, 'Hủy') !== false || stripos($status_name, 'Cancelled') !== false || stripos($status_name, 'removed') !== false) {
            $current_step = 0;
            $is_cancelled = true;
            $display_status = 'Đã Hủy';
        }
        
        // Final translation/formatting
        if (strtolower($status_name) === 'submitted') $display_status = 'Đã Xác Nhận';
        if (strtolower($status_name) === 'received_money') $display_status = 'Đã Thu Tiền';

        // Prepare Customer Data
        $cus_name = $order_details['shipping_address']['full_name'] ?? ($order_details['bill_full_name'] ?? 'Khách hàng');
        $cus_phone = $order_details['shipping_address']['phone_number'] ?? ($order_details['bill_phone_number'] ?? '');
        $cus_address = $order_details['shipping_address']['address'] ?? '';
        $cus_note = $order_details['note'] ?? '';
        
        // Prepare Shipping Carrier Data
        $partner_info = $order_details['partner'] ?? null;
        $partner_id = $partner_info['partner_id'] ?? null;
        $tracking_code = $partner_info['extend_code'] ?? '';
        $partner_fee = $partner_info['total_fee'] ?? 0;
        
        // Get carrier information using helper functions
        $carrier_name = null;
        $carrier_icon = null;
        $tracking_url = null;
        
        if ( $partner_id !== null ) {
            $carrier_name = caremil_get_carrier_name( $partner_id );
            $carrier_icon = caremil_get_carrier_icon( $partner_id );
            $tracking_url = caremil_get_carrier_tracking_url( $partner_id, $tracking_code );
        }
        ?>

        <!-- Confetti Container -->
        <?php if ($current_step == 5 && !$is_cancelled): ?>
        <div id="confetti-container" class="fixed inset-0 pointer-events-none z-0"></div>
        <?php endif; ?>

        <!-- REMOVED OLD STATUS CARD -->

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- LEFT: TIMELINE & DETAILS -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- NEW COMPACT HEADER inside Left Column -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                         <?php if ($is_cancelled): ?>
                            <h1 class="text-xl font-display font-black text-red-500 mb-1">Đơn Hàng Đã Hủy <i class="fas fa-times-circle ml-1"></i></h1>
                            <p class="text-xs text-gray-500">Đơn hàng <strong class="text-brand-navy">#<?php echo esc_html($display_id); ?></strong> đã bị hủy.</p>
                         <?php else: ?>
                            <h1 class="text-xl font-display font-black text-brand-navy mb-1">
                                <?php echo $current_step == 5 ? 'Giao Hàng Thành Công! <i class="fas fa-check-circle text-green-500 ml-1"></i>' : 'Cảm Ơn Mẹ Đã Đặt Hàng!'; ?>
                            </h1>
                            <p class="text-xs text-gray-500">Đơn hàng <strong class="text-brand-blue">#<?php echo esc_html($display_id); ?></strong> đang được xử lý.</p>
                         <?php endif; ?>
                    </div>
                    <?php if (!$is_cancelled): ?>
                    <div class="inline-block bg-brand-soft/30 text-brand-navy px-3 py-1 rounded-lg text-xs font-bold border border-brand-blue/20">
                        <?php echo esc_html($display_status); ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- 2. MODERN TIMELINE (Redesigned) -->
                <?php if (!$is_cancelled): ?>
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-brand-navy mb-8 flex items-center gap-2 text-lg">
                        <i class="fas fa-route text-brand-gold"></i> Tiến Độ Đơn Hàng
                    </h3>
                    
                    <div class="relative">
                        <!-- Progress Bar Background -->
                        <div class="absolute top-5 left-0 w-full h-1 bg-gray-100 rounded-full -z-0"></div>
                        
                        <!-- Active Progress Bar -->
                        <?php 
                            $bar_width = 0;
                            if ($current_step == 1) $bar_width = 0;
                            if ($current_step == 2) $bar_width = 25;
                            if ($current_step == 3) $bar_width = 50;
                            if ($current_step == 4) $bar_width = 75;
                            if ($current_step == 5) $bar_width = 100;
                        ?>
                        <div class="absolute top-5 left-0 h-1 bg-gradient-to-r from-brand-blue to-brand-green rounded-full -z-0 transition-all duration-1000 ease-out" style="width: <?php echo $bar_width; ?>%"></div>

                        <!-- New Stepper UI -->
                        <div class="flex justify-between items-start relative z-10 w-full">
                            
                            <?php 
                            $steps = [
                                1 => ['label' => 'Đã Đặt', 'icon' => 'fa-clipboard-check'],
                                2 => ['label' => 'Đã Xác Nhận', 'icon' => 'fa-user-check'],
                                3 => ['label' => 'Đang Chuẩn Bị', 'icon' => 'fa-box'],
                                4 => ['label' => 'Đang Giao', 'icon' => 'fa-truck'],
                                5 => ['label' => 'Hoàn Thành', 'icon' => 'fa-star']
                            ];
                            
                            foreach ($steps as $step_num => $step_info): 
                                $is_past = $current_step > $step_num;
                                $is_current = $current_step == $step_num;
                                $is_future = $current_step < $step_num;
                                
                                // Color Logic
                                $circle_class = 'bg-gray-200 text-gray-400'; // Default Future
                                if ($is_past) $circle_class = 'bg-green-500 text-white border-green-500'; // Past = Done (Green)
                                if ($is_current) {
                                    // Current Active Style
                                    if ($step_num == 5) $circle_class = 'bg-green-600 text-white scale-110 shadow-lg ring-4 ring-green-100';
                                    else $circle_class = 'bg-brand-blue text-white scale-110 shadow-lg ring-4 ring-blue-100';
                                }
                                
                                $text_class = 'text-gray-400';
                                if ($is_past) $text_class = 'text-green-600';
                                if ($is_current) $text_class = 'text-brand-navy font-black';
                            ?>
                            <div class="flex flex-col items-center flex-1">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 border-white transition-all <?php echo $circle_class; ?>">
                                    <i class="fas <?php echo $step_info['icon']; ?> text-sm"></i>
                                </div>
                                <span class="mt-3 text-xs font-bold <?php echo $text_class; ?>"><?php echo $step_info['label']; ?></span>
                            </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 3. ITEMS LIST -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-brand-navy mb-4">Sản Phẩm Đã Mua</h3>
                    <div class="space-y-4">
                        <?php 
                        $subtotal_retail = 0; // Tổng giá niêm yết
                        $calculated_total_discount = 0; // Tổng giảm giá tính toán

                        if ( $order_details && !empty($order_details['items']) ) : ?>
                            <?php foreach ( $order_details['items'] as $item ) : 
                                // 1. Item Price (Retail)
                                $i_name = $item['variation_info']['name'] ?? ($item['product_name'] ?? 'Sản phẩm');
                                
                                $i_retail_price = 0;
                                if (isset($item['variation_info']['retail_price']) && $item['variation_info']['retail_price'] > 0) {
                                    $i_retail_price = $item['variation_info']['retail_price'];
                                } elseif (isset($item['retail_price']) && $item['retail_price'] > 0) {
                                    $i_retail_price = $item['retail_price'];
                                } elseif (isset($item['price']) && $item['price'] > 0) {
                                    $i_retail_price = $item['price'];
                                }

                                // 2. Item Discount Logic
                                $i_discount_val = $item['discount_each_product'] ?? ($item['discount'] ?? 0);
                                $is_percent = $item['is_discount_percent'] ?? false;
                                
                                $i_final_price = $i_retail_price;
                                $item_discount_amount = 0;

                                if ($i_discount_val > 0) {
                                    if ($is_percent) {
                                        $item_discount_amount = ($i_retail_price * $i_discount_val) / 100;
                                        $i_final_price = $i_retail_price - $item_discount_amount;
                                    } else {
                                        $item_discount_amount = $i_discount_val;
                                        $i_final_price = $i_retail_price - $i_discount_val;
                                    }
                                }
                                
                                // Alternatively, check if item has 'total_discount' field pre-calculated
                                // But calculating manually is safer consistent with unit price display
                                
                                $i_qty = $item['quantity'] ?? 1;
                                $i_line_total = $i_final_price * $i_qty;
                                
                                $subtotal_retail += ($i_retail_price * $i_qty);
                                $calculated_total_discount += ($item_discount_amount * $i_qty);

                                // Image Lookup Logic 2.0 (Cleaner)
                                $img_url = '';
                                $search_name = preg_split('/[\|\-\(]/', $i_name)[0]; 
                                $search_name = trim($search_name);
                                
                                $args = [
                                    'post_type' => ['caremil_product', 'product'],
                                    's' => $search_name, 
                                    'posts_per_page' => 1,
                                    'post_status' => 'publish'
                                ];
                                $product_query = new WP_Query($args);
                                if ($product_query->have_posts()) {
                                    $product_id = $product_query->posts[0]->ID;
                                    $img_url = get_the_post_thumbnail_url($product_id, 'thumbnail');
                                }
                            ?>
                            <div class="flex gap-4 border-b border-dashed border-gray-100 pb-4 last:border-0 last:pb-0">
                                <div class="w-16 h-16 bg-gray-50 rounded-lg p-1 border border-gray-200 flex-shrink-0 flex items-center justify-center overflow-hidden">
                                     <?php if ($img_url): ?>
                                         <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($i_name); ?>" class="w-full h-full object-cover rounded">
                                     <?php else: ?>
                                         <i class="fas fa-box text-2xl text-brand-blue"></i>
                                     <?php endif; ?>
                                </div>
                                <div class="flex-grow">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-bold text-brand-navy text-sm"><?php echo esc_html($i_name); ?></h4>
                                        <span class="font-bold text-gray-700 text-sm"><?php echo number_format($i_line_total, 0, ',', '.'); ?>đ</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <?php if ($item_discount_amount > 0): ?>
                                            <span class="line-through text-gray-400 mr-1"><?php echo number_format($i_retail_price, 0, ',', '.'); ?>đ</span>
                                        <?php endif; ?>
                                        <span><?php echo number_format($i_final_price, 0, ',', '.'); ?>đ x <?php echo intval($i_qty); ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="text-sm text-gray-500 text-center py-4">Đang cập nhật thông tin sản phẩm...</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                        <!-- Final Calculation Block -->
                        <?php 
                        // Order Level Discounts (Promotion)
                        $order_level_discount = $order_details['discount_amount'] ?? ($order_details['total_discount'] ?? 0);
                        if (isset($order_details['promotion_amount'])) $order_level_discount += $order_details['promotion_amount'];

                        // Total Discount = Sum of Item Discounts + Order Level Discount
                        $total_discount_display = $calculated_total_discount + $order_level_discount;
                        
                        $shipping_fee = $order_details['shipping_fee'] ?? 0;
                        
                        // Final Payment Amount
                        // Priority: money_to_collect (COD) > total_price_after_sub_discount > total_price
                        $final_total = 0;
                        if (isset($order_details['money_to_collect']) && $order_details['money_to_collect'] > 0) {
                            $final_total = $order_details['money_to_collect'];
                        } elseif (isset($order_details['total_price_after_sub_discount']) && $order_details['total_price_after_sub_discount'] > 0) {
                            $final_total = $order_details['total_price_after_sub_discount'];
                        } else {
                            $final_total = $order_details['total_price'] ?? 0;
                        }
                        // Fallback calc if API is weird: $subtotal_retail - $total_discount_display + $shipping_fee
                        
                        // Re-verify Total Discount if API provided pre-calc vs manual
                        // If $final_total is significantly different from ($subtotal_retail - $calc), trust $final_total and adjust displayed discount
                        $implied_discount = ($subtotal_retail + $shipping_fee) - $final_total;
                        if ($implied_discount > 0) $total_discount_display = $implied_discount; 

                        if ($total_discount_display > 0): ?>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Giảm giá</span>
                            <span class="text-green-600">-<?php echo number_format($total_discount_display, 0, ',', '.'); ?>đ</span>
                        </div>
                        <?php endif; ?>

                        <?php if ($shipping_fee > 0): ?>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Phí vận chuyển</span>
                            <span><?php echo number_format($shipping_fee, 0, ',', '.'); ?>đ</span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="flex justify-between text-base font-bold text-brand-navy pt-2 border-t border-dashed border-gray-200">
                            <span>Tổng thanh toán</span>
                            <span class="text-brand-pink"><?php echo number_format($final_total, 0, ',', '.'); ?>đ</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: INFO & ACTIONS -->
            <div class="space-y-6">
                
                <!-- Shipping Info -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-brand-navy mb-4 text-sm uppercase tracking-wide">Thông Tin Nhận Hàng</h3>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div>
                            <p class="font-bold text-gray-800"><?php echo esc_html($cus_name); ?></p>
                            <p><?php echo esc_html($cus_phone); ?></p>
                        </div>
                        <div class="flex gap-2 items-start">
                            <i class="fas fa-map-marker-alt text-brand-blue mt-1"></i>
                            <p><?php echo esc_html($cus_address); ?></p>
                        </div>
                         <!-- Display only safe notes -->
                         <?php 
                            // Only show shipping_address note if it exists. 
                            // Avoid general 'note' from order_details as it might be internal "Khách trả ship" etc.
                            $safe_note = $order_details['shipping_address']['note'] ?? '';
                         ?>
                         <?php if (!empty($safe_note)): ?>
                        <div class="bg-gray-50 p-2 rounded text-xs text-gray-500 italic border border-gray-100">
                            "<?php echo esc_html($safe_note); ?>"
                        </div>
                        <?php endif; ?>
                        
                        <!-- Shipping Carrier Info -->
                        <?php if ( $carrier_name && $shipping_fee > 0 ): ?>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Đơn vị vận chuyển</p>
                            <div class="bg-gradient-to-r from-brand-soft/30 to-brand-blue/10 p-3 rounded-lg border border-brand-blue/20">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl"><?php echo $carrier_icon; ?></span>
                                        <span class="font-bold text-brand-navy text-sm"><?php echo esc_html( $carrier_name ); ?></span>
                                    </div>
                                    <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded">
                                        <?php echo number_format($shipping_fee, 0, ',', '.'); ?>đ
                                    </span>
                                </div>
                                
                                <?php if ( !empty( $tracking_code ) ): ?>
                                <div class="mt-2">
                                    <?php if ( $tracking_url ): ?>
                                        <a href="<?php echo esc_url( $tracking_url ); ?>" target="_blank" rel="noopener" 
                                           class="flex items-center gap-2 text-xs text-brand-blue hover:text-brand-navy transition group">
                                            <i class="fas fa-barcode"></i>
                                            <span class="font-mono font-semibold"><?php echo esc_html( $tracking_code ); ?></span>
                                            <i class="fas fa-external-link-alt text-xs opacity-0 group-hover:opacity-100 transition"></i>
                                        </a>
                                    <?php else: ?>
                                        <div class="flex items-center gap-2 text-xs text-gray-600">
                                            <i class="fas fa-barcode"></i>
                                            <span class="font-mono font-semibold"><?php echo esc_html( $tracking_code ); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ( $partner_fee > 0 && $partner_fee !== $shipping_fee ): ?>
                                <div class="mt-2 pt-2 border-t border-brand-blue/10">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500">Phí gốc đơn vị vận chuyển:</span>
                                        <span class="font-semibold text-gray-700"><?php echo number_format($partner_fee, 0, ',', '.'); ?>đ</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php elseif ( $shipping_fee > 0 ): ?>
                        <!-- Fallback when no carrier info available -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">🚚</span>
                                    <span class="text-gray-600">Phí vận chuyển</span>
                                </div>
                                <span class="font-bold text-green-600"><?php echo number_format($shipping_fee, 0, ',', '.'); ?>đ</span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Support Box -->
                <div class="bg-brand-soft/50 rounded-2xl p-6 border border-brand-soft text-center">
                    <p class="text-sm text-brand-navy font-bold mb-2">Cần hỗ trợ đơn hàng?</p>
                    <p class="text-xs text-gray-600 mb-4">Đội ngũ CareMIL luôn sẵn sàng hỗ trợ mẹ.</p>
                    <div class="flex justify-center gap-3">
                        <a href="tel:1900xxxx" class="bg-white text-brand-navy font-bold py-2 px-4 rounded-lg shadow-sm hover:bg-brand-blue hover:text-white transition text-xs flex items-center gap-2">
                            <i class="fas fa-phone"></i> Gọi Ngay
                        </a>
                        <a href="#" class="bg-brand-blue text-white font-bold py-2 px-4 rounded-lg shadow-sm hover:bg-blue-600 transition text-xs flex items-center gap-2">
                            <i class="fab fa-facebook-messenger"></i> Chat
                        </a>
                    </div>
                </div>

                <!-- Back to Home -->
                <a href="<?php echo esc_url( home_url( '/cua-hang' ) ); ?>" class="block w-full bg-brand-navy text-white font-bold py-3.5 rounded-2xl text-center shadow-lg hover:bg-brand-blue transition transform hover:-translate-y-1">
                    Tiếp Tục Mua Sắm
                </a>
            </div>

        </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-gray-100 py-6 mt-8 text-center text-xs text-gray-400">
        &copy; 2024 CareMIL Vietnam.
    </footer>

    <script>
        // Confetti Effect
        function createConfetti() {
            // ... (keep existing code) ...
        }
        
        // DEBUG DATA FOR AI AGENT (Hidden)
        console.log("DEBUG_ORDER_JSON:", <?php echo json_encode($order_details); ?>);
    </script>
    <!-- RAW DEBUG DATA: 
    <?php echo json_encode($order_details); ?>
    -->

        window.addEventListener('DOMContentLoaded', createConfetti);
    </script>
<?php
get_footer();