<?php
/**
 * Template Name: Payment
 * Template Post Type: page
 * Description: Template for displaying carts page
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
    <title>Thanh Toán CareMIL QR</title>
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
                            pink: '#ef476f',
                            green: '#4ade80'
                        }
                    },
                    fontFamily: {
                        sans: ['Quicksand', 'sans-serif'],
                        display: ['Baloo 2', 'cursive'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f3f4f6; /* Nền xám nhẹ dịu mắt */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        /* Hiệu ứng focus cho nút copy */
        .copy-btn:active {
            transform: scale(0.95);
            background-color: #e0f2fe;
        }

        /* Border nét đứt tạo cảm giác biên lai */
        .receipt-border {
            background-image: linear-gradient(to right, #cbd5e1 50%, rgba(255,255,255,0) 0%);
            background-position: bottom;
            background-size: 10px 1px;
            background-repeat: repeat-x;
        }

        /* Quét sáng QR */
        .scan-line {
            width: 100%;
            height: 2px;
            background: #ef476f;
            box-shadow: 0 0 4px #ef476f;
            position: absolute;
            z-index: 10;
            animation: scan 2.5s infinite linear;
        }
        @keyframes scan {
            0% { top: 0; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
    </style>
</head>
<body class="text-gray-700 font-sans">

    <!-- HEADER (Minimal) -->
    <nav class="bg-white border-b border-gray-200 h-16 flex-none flex items-center sticky top-0 z-50 shadow-sm">
        <div class="container mx-auto px-4 flex justify-between items-center max-w-5xl">
            <a href="caremil_checkout_page.html" class="flex items-center gap-2 text-gray-500 hover:text-brand-navy font-bold text-sm transition group">
                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center group-hover:bg-brand-soft transition">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <span>Quay lại</span>
            </a>
            <div class="flex items-center gap-2">
                <div class="bg-green-50 text-green-600 px-3 py-1 rounded-full text-xs font-bold border border-green-100 flex items-center gap-1">
                    <i class="fas fa-lock"></i> Thanh toán an toàn
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="flex-grow flex items-center justify-center p-4 lg:p-8">
        <div class="max-w-5xl w-full grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
            
            <!-- LEFT: QR CARD (5 Cols) -->
            <div class="lg:col-span-5 w-full">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 relative">
                    <!-- Brand Banner Top -->
                    <div class="bg-brand-navy p-6 text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-10 -mt-10"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full -ml-10 -mb-10"></div>
                        
                        <p class="text-blue-200 text-xs font-bold uppercase tracking-widest mb-1">Tổng thanh toán</p>
                        <h2 class="text-4xl font-display font-black text-white">1.550.000<span class="text-xl align-top">đ</span></h2>
                    </div>

                    <!-- QR Area -->
                    <div class="p-8 flex flex-col items-center">
                        <div class="relative w-64 h-64 bg-white p-2 rounded-2xl shadow-sm border-2 border-brand-soft mb-2 group">
                            <!-- QR Code -->
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=350x350&data=00020101021238570010A0000007270127000697042301131131131131130208QRIBFTTA5303704540715500005802VN62150811ORDER1234563040F1D" 
                                 class="w-full h-full object-contain rounded-xl" 
                                 alt="Payment QR">
                            
                            <!-- Logo Overlay -->
                            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white p-1.5 rounded-lg shadow-md w-12 h-12 flex items-center justify-center">
                                <i class="fas fa-leaf text-brand-gold text-2xl"></i>
                            </div>

                            <!-- Scan Effect -->
                            <div class="absolute top-0 left-0 w-full h-full rounded-xl overflow-hidden pointer-events-none">
                                <div class="scan-line"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Timer Footer -->
                    <div class="bg-gray-50 border-t border-gray-100 p-4 text-center">
                        <p class="text-xs text-gray-500 font-medium flex justify-center items-center gap-2">
                            Đơn hàng hết hạn sau: <span id="timer" class="text-brand-pink font-bold font-mono text-base bg-pink-50 px-2 rounded">15:00</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- RIGHT: TRANSFER INFO (7 Cols) -->
            <div class="lg:col-span-7 flex flex-col justify-center h-full">
                
                <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-6 md:p-8">
                    <h3 class="text-xl font-display font-bold text-brand-navy mb-6 flex items-center gap-2 pb-4 border-b border-gray-100">
                        <span class="w-8 h-8 rounded-full bg-brand-soft flex items-center justify-center text-brand-blue"><i class="fas fa-info"></i></span>
                        Thông Tin Chuyển Khoản
                    </h3>

                    <div class="space-y-6">
                        <!-- Account Number (High Priority) -->
                        <div class="bg-blue-50/50 rounded-2xl p-4 border border-brand-soft hover:border-brand-blue transition group relative">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Số Tài Khoản</span>
                                <span class="text-xs font-bold text-brand-blue bg-white px-2 py-0.5 rounded shadow-sm opacity-0 group-hover:opacity-100 transition">Sao chép</span>
                            </div>
                            <div class="flex justify-between items-center gap-4 cursor-pointer" onclick="copyToClipboard('acc-num', this)">
                                <p class="text-2xl md:text-3xl font-mono font-black text-brand-navy tracking-tight" id="acc-num">0987 654 321</p>
                                <button class="w-10 h-10 rounded-xl bg-white text-brand-blue shadow-sm flex items-center justify-center hover:scale-110 transition active:scale-95 border border-brand-soft">
                                    <i class="far fa-copy text-lg"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Amount & Content Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Bank Info -->
                            <div class="p-4 rounded-2xl border border-gray-200 hover:border-gray-300 transition">
                                <p class="text-xs font-bold text-gray-400 uppercase mb-2">Ngân Hàng</p>
                                <div class="flex items-center gap-3">
                                    <img src="https://img.icons8.com/color/48/mb-bank.png" class="w-8 h-8 object-contain" alt="MB Bank">
                                    <div>
                                        <p class="font-bold text-gray-800 leading-tight">MB Bank</p>
                                        <p class="text-[10px] text-gray-400">Ngân hàng Quân Đội</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Name -->
                            <div class="p-4 rounded-2xl border border-gray-200 hover:border-gray-300 transition">
                                <p class="text-xs font-bold text-gray-400 uppercase mb-2">Chủ Tài Khoản</p>
                                <p class="font-bold text-gray-800 uppercase text-sm md:text-base leading-tight">CÔNG TY TNHH NP FOOD</p>
                            </div>
                        </div>

                        <!-- Transfer Content (Critical) -->
                        <div class="bg-yellow-50 rounded-2xl p-4 border border-yellow-200 hover:border-yellow-400 transition cursor-pointer group" onclick="copyToClipboard('order-id', this)">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-bold text-yellow-700 uppercase tracking-wider flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i> Nội dung chuyển khoản
                                </span>
                                <span class="text-[10px] bg-white text-yellow-600 px-2 py-0.5 rounded font-bold border border-yellow-100">BẮT BUỘC</span>
                            </div>
                            <div class="flex justify-between items-center gap-4">
                                <p class="text-xl font-mono font-bold text-brand-navy" id="order-id">CAREMIL 123456</p>
                                <button class="text-yellow-600 hover:text-yellow-800 transition p-2">
                                    <i class="far fa-copy text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Action -->
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <button onclick="confirmPayment()" class="w-full bg-brand-green text-white font-bold py-4 rounded-2xl shadow-lg hover:bg-green-500 hover:shadow-xl transition transform hover:-translate-y-1 flex items-center justify-center gap-3 text-lg group">
                            <span>Xác Nhận Đã Chuyển</span>
                            <i class="fas fa-check-circle text-xl group-hover:scale-110 transition"></i>
                        </button>
                        <p class="text-center text-xs text-gray-400 mt-3">
                            Hệ thống sẽ tự động xác nhận sau 1-3 phút. <br class="md:hidden"> Cần hỗ trợ? Gọi <a href="#" class="text-brand-blue font-bold">1900 xxxx</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- PROCESSING MODAL -->
    <div id="processing-modal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-white/95 backdrop-blur-md">
        <div class="text-center">
            <div class="relative w-28 h-28 mx-auto mb-6">
                <!-- Outer ring -->
                <div class="absolute inset-0 border-[6px] border-gray-100 rounded-full"></div>
                <!-- Spinning ring -->
                <div class="absolute inset-0 border-[6px] border-brand-blue rounded-full border-t-transparent animate-spin"></div>
                <!-- Icon -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-search-dollar text-4xl text-brand-navy animate-pulse"></i>
                </div>
            </div>
            <h3 class="text-2xl font-display font-bold text-brand-navy mb-2">Đang Đối Soát...</h3>
            <p class="text-gray-500 font-medium">Vui lòng không tắt trình duyệt trong quá trình xử lý.</p>
        </div>
    </div>

    <!-- SUCCESS MODAL -->
    <div id="success-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-[40px] shadow-2xl max-w-md w-full p-8 text-center relative border-4 border-green-100 transform scale-90 transition-all duration-300" id="success-content">
            <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner border border-green-100">
                <i class="fas fa-check text-5xl text-green-500 animate-[bounce_1s_infinite]"></i>
            </div>
            <h3 class="text-3xl font-display font-black text-brand-navy mb-2">Thanh Toán Thành Công!</h3>
            <p class="text-gray-600 mb-8 font-medium px-4">Đơn hàng <strong class="text-brand-blue">#123456</strong> đã được xác nhận. CareMIL sẽ sớm liên hệ để giao hàng cho bạn.</p>
            
            <button onclick="window.location.href='caremil_landing_page.html'" class="w-full bg-brand-navy text-white font-bold py-3.5 rounded-2xl hover:bg-brand-blue transition shadow-lg text-lg">
                Về Trang Chủ
            </button>
        </div>
    </div>

    <script>
        // UX: Copy with Visual Feedback
        function copyToClipboard(elementId, btnElement) {
            const text = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(text).then(() => {
                // Find icon inside the clicked container
                const icon = btnElement.querySelector('i');
                const originalClass = icon.className;
                
                // Change to Check
                icon.className = 'fas fa-check text-green-500 text-lg';
                
                // Revert after 2s
                setTimeout(() => {
                    icon.className = originalClass;
                }, 2000);
            });
        }

        // Timer Logic
        let time = 900; 
        const timerEl = document.getElementById('timer');
        const countdown = setInterval(() => {
            const minutes = Math.floor(time / 60);
            let seconds = time % 60;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            timerEl.innerText = `${minutes}:${seconds}`;
            time--;
            if (time < 0) {
                clearInterval(countdown);
                timerEl.innerText = "Hết hạn";
                timerEl.classList.remove('bg-pink-50', 'text-brand-pink');
                timerEl.classList.add('bg-gray-200', 'text-gray-500');
            }
        }, 1000);

        // Payment Simulation
        function confirmPayment() {
            document.getElementById('processing-modal').classList.remove('hidden');
            document.getElementById('processing-modal').classList.add('flex');

            setTimeout(() => {
                document.getElementById('processing-modal').classList.remove('flex');
                document.getElementById('processing-modal').classList.add('hidden');
                
                document.getElementById('success-modal').classList.remove('hidden');
                document.getElementById('success-modal').classList.add('flex');
                
                setTimeout(() => {
                    document.getElementById('success-content').classList.remove('scale-90');
                    document.getElementById('success-content').classList.add('scale-100');
                }, 50);
            }, 2000); 
        }
    </script>
<?php
get_footer();