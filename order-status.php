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
            <a href="caremil_landing_page.html" class="flex items-center gap-2 group">
                <i class="fas fa-leaf text-brand-gold text-2xl group-hover:rotate-12 transition-transform"></i>
                <span class="text-xl font-display font-black text-brand-navy tracking-tight">Care<span class="text-brand-blue">MIL</span></span>
            </a>
            <a href="caremil_product_list.html" class="text-sm font-bold text-brand-blue hover:text-brand-navy transition">
                Tiếp tục mua sắm
            </a>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="flex-grow container mx-auto px-4 py-8 max-w-4xl relative">
        <!-- Confetti Container -->
        <div id="confetti-container" class="fixed inset-0 pointer-events-none z-0"></div>

        <!-- 1. SUCCESS CARD -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 text-center mb-8 relative z-10 overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-brand-blue via-brand-pink to-brand-gold"></div>
            
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                <i class="fas fa-check text-4xl text-green-500"></i>
            </div>
            <h1 class="text-3xl font-display font-black text-brand-navy mb-2">Đặt Hàng Thành Công!</h1>
            <p class="text-gray-600 mb-6">Cảm ơn mẹ đã tin chọn CareMIL. Đơn hàng <strong class="text-brand-blue">#123456</strong> đã được hệ thống ghi nhận.</p>
            
            <div class="inline-block bg-blue-50 text-brand-navy px-4 py-2 rounded-xl text-sm font-bold border border-blue-100">
                Dự kiến giao hàng: <span class="text-brand-pink">2 - 3 ngày tới</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- LEFT: ORDER DETAILS -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- 2. TRACKING TIMELINE -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-brand-navy mb-6 flex items-center gap-2">
                        <i class="fas fa-shipping-fast text-brand-gold"></i> Trạng Thái Đơn Hàng
                    </h3>
                    
                    <div class="relative px-2">
                        <!-- Connector Lines -->
                        <div class="timeline-connector"></div>
                        <div class="timeline-progress"></div> <!-- Width controls progress -->

                        <div class="flex justify-between relative z-10">
                            <!-- Step 1: Placed -->
                            <div class="flex flex-col items-center w-20">
                                <div class="w-12 h-12 rounded-full bg-green-500 border-4 border-white shadow-sm flex items-center justify-center text-white mb-2">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <span class="text-xs font-bold text-green-600 text-center">Đã Đặt</span>
                                <span class="text-[10px] text-gray-400">14:30</span>
                            </div>

                            <!-- Step 2: Confirmed (Active) -->
                            <div class="flex flex-col items-center w-20">
                                <div class="w-12 h-12 rounded-full bg-brand-blue border-4 border-white shadow-sm flex items-center justify-center text-white mb-2 animate-pulse">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <span class="text-xs font-bold text-brand-blue text-center">Đã Xác Nhận</span>
                                <span class="text-[10px] text-gray-400">Đang xử lý</span>
                            </div>

                            <!-- Step 3: Shipping -->
                            <div class="flex flex-col items-center w-20 opacity-50">
                                <div class="w-12 h-12 rounded-full bg-gray-200 border-4 border-white flex items-center justify-center text-gray-500 mb-2">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <span class="text-xs font-bold text-gray-400 text-center">Đang Giao</span>
                            </div>

                            <!-- Step 4: Delivered -->
                            <div class="flex flex-col items-center w-20 opacity-50">
                                <div class="w-12 h-12 rounded-full bg-gray-200 border-4 border-white flex items-center justify-center text-gray-500 mb-2">
                                    <i class="fas fa-home"></i>
                                </div>
                                <span class="text-xs font-bold text-gray-400 text-center">Đã Giao</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. ITEMS LIST -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-brand-navy mb-4">Sản Phẩm Đã Mua</h3>
                    <div class="space-y-4">
                        <!-- Item 1 -->
                        <div class="flex gap-4 border-b border-dashed border-gray-100 pb-4 last:border-0 last:pb-0">
                            <div class="w-16 h-16 bg-gray-50 rounded-lg p-1 border border-gray-200 flex-shrink-0">
                                <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-chuan.png" class="w-full h-full object-contain">
                            </div>
                            <div class="flex-grow">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-bold text-brand-navy text-sm">CareMIL Hộp Lớn 800g</h4>
                                    <span class="font-bold text-gray-700 text-sm">850.000đ</span>
                                </div>
                                <p class="text-xs text-gray-500">Phân loại: Hộp thiếc</p>
                                <p class="text-xs text-gray-500 mt-1">x1</p>
                            </div>
                        </div>
                        <!-- Item 2 -->
                        <div class="flex gap-4 border-b border-dashed border-gray-100 pb-4 last:border-0 last:pb-0">
                            <div class="w-16 h-16 bg-gray-50 rounded-lg p-1 border border-gray-200 flex-shrink-0">
                                <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Goi-sua.png" class="w-full h-full object-contain rotate-6">
                            </div>
                            <div class="flex-grow">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-bold text-brand-navy text-sm">Hộp 10 Gói Tiện Lợi</h4>
                                    <span class="font-bold text-gray-700 text-sm">700.000đ</span>
                                </div>
                                <p class="text-xs text-gray-500">Phân loại: Gói 36g</p>
                                <p class="text-xs text-gray-500 mt-1">x2</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Tạm tính</span>
                            <span>1.550.000đ</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Phí vận chuyển</span>
                            <span class="font-bold text-green-500">Miễn phí</span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-brand-navy pt-2 border-t border-dashed border-gray-200">
                            <span>Tổng cộng</span>
                            <span class="text-brand-pink">1.550.000đ</span>
                        </div>
                        <div class="text-xs text-gray-400 text-right mt-1">Đã thanh toán qua QR Code</div>
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
                            <p class="font-bold text-gray-800">Nguyễn Văn A</p>
                            <p>0912 345 678</p>
                        </div>
                        <div class="flex gap-2 items-start">
                            <i class="fas fa-map-marker-alt text-brand-blue mt-1"></i>
                            <p>123 Đường Láng, Phường Láng Thượng, Quận Đống Đa, Hà Nội</p>
                        </div>
                        <div class="bg-gray-50 p-2 rounded text-xs text-gray-500 italic border border-gray-100">
                            "Giao giờ hành chính, gọi trước khi giao..."
                        </div>
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
                <a href="caremil_landing_page.html" class="block w-full bg-brand-navy text-white font-bold py-3.5 rounded-2xl text-center shadow-lg hover:bg-brand-blue transition transform hover:-translate-y-1">
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
            const container = document.getElementById('confetti-container');
            const colors = ['#4cc9f0', '#ef476f', '#ffd166', '#4ade80'];
            
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.classList.add('confetti');
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.top = -10 + 'px';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDuration = (Math.random() * 2 + 3) + 's'; // 3-5s
                confetti.style.animationDelay = Math.random() * 2 + 's';
                container.appendChild(confetti);
                
                // Remove after animation
                setTimeout(() => confetti.remove(), 5000);
            }
        }

        window.addEventListener('DOMContentLoaded', createConfetti);
    </script>
<?php
get_footer();