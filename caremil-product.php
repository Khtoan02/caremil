<?php
/**
 * Template Name: Trang CareMIL
 * Template Post Type: page
 * Description: Trang sản phẩm CareMIL với thiết kế đầy đủ
 *
 * @package Caremil
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareMIL - Dinh Dưỡng Chuyên Biệt cho Trẻ Đặc Biệt</title>
    <meta name="description" content="Sữa hạt CareMIL - Giải pháp dinh dưỡng thực vật chuyên biệt cho trẻ có nhu cầu đặc biệt, không Gluten, không Casein, hỗ trợ tiêu hóa và phát triển toàn diện.">
    <?php wp_head(); ?>
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
                            green: '#4ade80',
                            metal: '#C5A028' 
                        }
                    },
                    fontFamily: {
                        sans: ['Quicksand', 'sans-serif'],
                        display: ['Baloo 2', 'cursive'],
                    },
                    animation: {
                        'float': 'float 4s ease-in-out infinite',
                        'bounce-slow': 'bounce 3s infinite',
                        'wiggle': 'wiggle 1s ease-in-out infinite',
                        'spin-slow': 'spin 10s linear infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-15px)' },
                        },
                        wiggle: {
                            '0%, 100%': { transform: 'rotate(-3deg)' },
                            '50%': { transform: 'rotate(3deg)' },
                        }
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(76, 201, 240, 0.3)',
                    }
                }
            }
        }
    </script>
    <style>
        /* --- General Styles --- */
        body/* Space for disclaimer bar */
        .nutrition-table th { font-family: 'Baloo 2', cursive; color: #1a4f8a; white-space: nowrap; }
        .nutrition-table td { font-family: 'Quicksand', sans-serif; font-weight: 600; }
        .overflow-x-auto { -webkit-overflow-scrolling: touch; scrollbar-width: thin; }
        
        /* Mobile Menu */
        #mobile-menu { transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out; max-height: 0; opacity: 0; overflow: hidden; }
        #mobile-menu.open { max-height: 400px; opacity: 1; }
        
        /* Modal & Reveals */
        #expert-modal, #trial-modal { background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); }
        .modal-show { display: flex; animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        /* Scroll Reveal */
        .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
        .reveal.active { opacity: 1; transform: translateY(0); }
        
        /* Utilities */
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .tab-btn { transition: all 0.3s ease; }
        .tab-btn.active { background-color: #4cc9f0; color: white; box-shadow: 0 4px 10px rgba(76, 201, 240, 0.3); }
        .tab-content { display: none; animation: fadeIn 0.5s ease; }
        .tab-content.active { display: block; }
        
        .sticker { background: white; border: 3px solid #f0f4f8; border-radius: 24px; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .sticker:hover { transform: translateY(-5px); border-color: #ffd166; box-shadow: 0 10px 20px rgba(255, 209, 102, 0.2); }
        .disclaimer-bar { background: #fff3cd; color: #856404; font-size: 0.75rem; padding: 0.5rem 0; border-bottom: 1px solid #ffeeba; overflow: hidden; white-space: nowrap; position: relative; }
        .disclaimer-wrapper { display: flex; width: fit-content; }
        .disclaimer-content { display: inline-flex; animation: marquee 40s linear infinite; }
        .disclaimer-content span { display: inline-block; padding-right: 4rem; white-space: nowrap; }
        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-50%); }
        }
        /* Mobile Responsive for Disclaimer Bar */
        @media (max-width: 768px) {
            .disclaimer-bar { font-size: 0.65rem; padding: 0.4rem 0; }
            .disclaimer-content span { padding-right: 2.5rem; }
            .disclaimer-content { animation-duration: 35s; }
        }
        @media (max-width: 480px) {
            .disclaimer-bar { font-size: 0.6rem; padding: 0.35rem 0; }
            .disclaimer-content span { padding-right: 2rem; }
            .disclaimer-content { animation-duration: 30s; }
        }
        
        /* Form Styles (CareMIL Theme) */
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 12px;
            border: 2px solid #e0fbfc; /* Soft Blue Border */
            background-color: #f8fafc;
            outline: none;
            font-family: 'Quicksand', sans-serif;
            font-weight: 600;
            color: #1a4f8a;
            transition: all 0.3s ease;
        }
        .form-input:focus {
            border-color: #4cc9f0;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(76, 201, 240, 0.1);
        }
        .form-input::placeholder { color: #94a3b8; font-weight: 500; }
        .form-label {
            color: #1a4f8a; /* Brand Navy */
            font-weight: 700;
            margin-bottom: 6px;
            display: block;
            font-size: 0.95rem;
        }
        .gold-gradient-btn {
            background: linear-gradient(to bottom, #ffd166, #f59e0b); /* Brand Gold */
            color: #1a4f8a;
            font-weight: 800;
            text-transform: uppercase;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: 2px solid #fff;
        }
        .gold-gradient-btn:hover {
            background: linear-gradient(to bottom, #f59e0b, #ffd166);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(255, 209, 102, 0.4);
        }
        
        /* --- Gut Simulation Animations --- */
        @keyframes organicSway { 
            0% { transform: rotate(0deg) skewX(0deg); } 
            25% { transform: rotate(2deg) skewX(1deg); } 
            50% { transform: rotate(0deg) skewX(0deg); } 
            75% { transform: rotate(-2deg) skewX(-1deg); } 
            100% { transform: rotate(0deg) skewX(0deg); } 
        }
        @keyframes floatChaos { 
            0% { transform: translate(0, 0) rotate(0deg); } 
            20% { transform: translate(5px, -3px) rotate(15deg); } 
            40% { transform: translate(-4px, 5px) rotate(-10deg); } 
            60% { transform: translate(3px, 4px) rotate(5deg); } 
            80% { transform: translate(-3px, -4px) rotate(-15deg); } 
            100% { transform: translate(0, 0) rotate(0deg); } 
        }
        @keyframes flowRight { 
            0% { transform: translateX(-100px) translateY(0) rotate(0deg); opacity: 0; } 
            10% { opacity: 0.5; } 90% { opacity: 0.5; } 
            100% { transform: translateX(120vw) translateY(20px) rotate(180deg); opacity: 0; } 
        }
        @keyframes battleFlow { 0% { left: -20%; } 100% { left: 120%; } }
        @keyframes neutralizeToxin { 
            0% { transform: scale(1) rotate(0deg); opacity: 1; } 
            40% { transform: scale(1) rotate(0deg); } 
            50% { transform: scale(0.8); filter: brightness(1.5); } 
            100% { transform: scale(0); opacity: 0; } 
        }
        @keyframes antibodyBind { 
            0% { transform: translate(60px, -40px) rotate(30deg); opacity: 0; } 
            30% { opacity: 1; } 
            40% { transform: translate(2px, 2px) rotate(0deg); } 
            50% { transform: translate(0, 0) scale(1.1); filter: drop-shadow(0 0 8px white); } 
            100% { transform: translate(0, 0) scale(0); opacity: 0; } 
        }
        @keyframes softGlow { 
            0%, 45% { opacity: 0; transform: scale(0.5); } 
            50% { opacity: 0.8; transform: scale(1.5); background-color: rgba(255, 255, 255, 0.8); box-shadow: 0 0 15px white; } 
            100% { opacity: 0; transform: scale(2); } 
        }
        
        .particle { position: absolute; transition: top 3s cubic-bezier(0.4, 0, 0.2, 1); animation: floatChaos 4s infinite ease-in-out; z-index: 15; }
        .particle.leaking { top: 120% !important; z-index: 50; transition: top 2s ease-in !important; }
        .blood-cell { 
            position: absolute; 
            background: radial-gradient(circle at 30% 30%, #ff6b6b, #991b1b); 
            border-radius: 45% 55% 50% 50% / 45% 50% 55% 55%; 
            box-shadow: inset -2px -2px 6px rgba(0,0,0,0.2); 
            animation-name: flowRight; 
            animation-timing-function: linear; 
            animation-iteration-count: infinite; 
        }
        .cell-container { transform-origin: bottom center; }
        .cell-body { 
            width: 100%; height: 120%; border: 2px solid; border-bottom: none; position: relative; 
            transition: background-color 1s ease, border-color 1s ease; 
            background-image: linear-gradient(to bottom, rgba(255,255,255,0.6), rgba(255,255,255,0.1)); 
            border-top-left-radius: 30px; border-top-right-radius: 30px; 
            transform-origin: bottom center; animation: organicSway 6s infinite ease-in-out; 
        }
        .mode-normal .cell-body { background-color: #fce7f3; border-color: #fbcfe8; } 
        .mode-normal .cell-nucleus { background-color: #db2777; } 
        .mode-normal .tight-junction { opacity: 1; height: 40%; top: 20%; background-color: #3b82f6; width: 3px; box-shadow: 0 0 5px #60a5fa; }
        .mode-leaky #mucosal-cells { gap: 20px; } 
        .mode-leaky .cell-body { background-color: #fee2e2; border-color: #fca5a5; animation-duration: 3s; }
        .mode-leaky .cell-nucleus { background-color: #b91c1c; }
        .mode-leaky .tight-junction { opacity: 0; height: 0; }
        .battle-pair { position: absolute; animation-name: battleFlow; animation-timing-function: linear; animation-iteration-count: infinite; display: flex; align-items: center; justify-content: center; width: 100px; height: 100px; }
        .toxin-target { animation: neutralizeToxin linear infinite; z-index: 10; }
        .antibody-actor { position: absolute; animation: antibodyBind linear infinite; z-index: 20; width: 30px; height: 30px; }
        .antibody-svg { width: 100%; height: 100%; fill: none; stroke: #60a5fa; stroke-width: 4; stroke-linecap: round; stroke-linejoin: round; filter: drop-shadow(0 0 2px rgba(37, 99, 235, 0.5)); }
        .glow-effect { position: absolute; width: 40px; height: 40px; border-radius: 50%; animation: softGlow linear infinite; pointer-events: none; z-index: 5; }
    </style>
    
</head>
<body class="bg-brand-cream text-gray-600 font-sans selection:bg-brand-gold selection:text-white">

    <!-- Legal Disclaimer Bar -->
    <div class="fixed bottom-0 left-0 w-full z-[60] disclaimer-bar font-bold">
        <div class="disclaimer-wrapper">
            <div class="disclaimer-content">
                <span>⚠ Sản phẩm này là thực phẩm tự nhiên, không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.</span>
                <span>💚 Sữa mẹ là thức ăn tốt nhất cho sức khỏe và sự phát triển toàn diện của trẻ sơ sinh và trẻ nhỏ.</span>
            </div>
            <div class="disclaimer-content">
                <span>⚠ Sản phẩm này là thực phẩm tự nhiên, không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.</span>
                <span>💚 Sữa mẹ là thức ăn tốt nhất cho sức khỏe và sự phát triển toàn diện của trẻ sơ sinh và trẻ nhỏ.</span>
            </div>
        </div>
    </div>

    <!-- 1. NAVIGATION -->
    <nav class="fixed w-full z-50 transition-all duration-300 py-2 md:py-3 top-1" id="navbar">
        <div class="container mx-auto px-4">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl md:rounded-full shadow-soft px-4 py-2 md:px-6 md:py-3 border border-white relative z-50">
                <div class="flex justify-between items-center">
                    <div class="flex items-center justify-center lg:justify-start gap-4">
                        <a href="https://caremil.dawnbridge.vn" class="text-2xl font-display font-black text-brand-navy tracking-tight flex items-center gap-2 hover:underline">
                            <i class="fas fa-leaf text-brand-gold"></i> Care<span class="text-brand-blue">MIL</span>
                        </a>
                        <span class="text-2xl font-display font-black text-gray-300">|</span>
                        <a href="https://dawnbridge.vn" class="text-2xl font-display font-black text-brand-navy tracking-tight flex items-center gap-2 hover:underline" target="_blank" rel="noopener">
                            <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Dawnbridge-logo-e1764735620422.png" alt="DawnBridge Logo" class="h-4 w-auto inline-block align-middle" />
                        </a>
                    </div>
                    
                    <div class="hidden md:flex items-center space-x-8 font-bold text-gray-500 text-lg">
                        <a href="#khoa-hoc" class="hover:text-brand-blue transition">Khoa Học</a>
                        <a href="#loi-ich" class="hover:text-brand-blue transition">Lợi Ích</a>
                        <a href="#bang-thanh-phan" class="hover:text-brand-blue transition">Thành Phần</a>
                        <a href="#huong-dan" class="hover:text-brand-blue transition">Cách Dùng</a>
                        <button onclick="openTrialModal()" class="bg-brand-navy text-white font-bold py-2 px-6 rounded-full shadow-lg hover:bg-blue-400 hover:scale-105 transition transform flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-gift"></i> Nhận Quà
                        </button>
                    </div>
                    
                    <button onclick="toggleMobileMenu()" class="md:hidden text-brand-navy text-xl bg-blue-50 p-2 rounded-lg w-10 h-10 flex items-center justify-center focus:outline-none shadow-sm transition active:bg-blue-100">
                        <i class="fas fa-bars" id="menu-icon"></i>
                    </button>
                </div>

                <div id="mobile-menu" class="md:hidden mt-2 border-t border-gray-100">
                    <div class="flex flex-col space-y-2 py-4 font-bold text-gray-600 text-center">
                        <a href="#loi-ich" onclick="toggleMobileMenu()" class="py-3 hover:text-brand-blue hover:bg-blue-50 rounded-xl transition">Lợi Ích</a>
                        <a href="#khoa-hoc" onclick="toggleMobileMenu()" class="py-3 hover:text-brand-blue hover:bg-blue-50 rounded-xl transition">Khoa Học</a>
                        <a href="#bang-thanh-phan" onclick="toggleMobileMenu()" class="py-3 hover:text-brand-blue hover:bg-blue-50 rounded-xl transition">Thành Phần</a>
                        <a href="#huong-dan" onclick="toggleMobileMenu()" class="py-3 hover:text-brand-blue hover:bg-blue-50 rounded-xl transition">Cách Dùng</a>
                        <button onclick="toggleMobileMenu(); openTrialModal()" class="bg-brand-gold text-white py-3 mt-2 rounded-xl shadow-md w-full">
                            Nhận Quà Dùng Thử
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- 2. HERO SECTION -->
    <header class="relative pt-32 pb-16 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-blue-50 via-white to-brand-cream -z-20"></div>
        <div class="absolute top-20 -left-10 text-brand-blue opacity-20 text-6xl lg:text-8xl animate-bounce-slow"><i class="fas fa-cloud"></i></div>
        <div class="absolute top-40 right-0 text-brand-gold opacity-30 text-5xl lg:text-7xl animate-float"><i class="fas fa-sun"></i></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col-reverse lg:flex-row items-center gap-10 lg:gap-20">
                <div class="lg:w-1/2 text-center lg:text-left reveal active space-y-6">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-brand-blue/30 text-brand-navy font-bold shadow-sm text-sm lg:text-base mx-auto lg:mx-0">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Công thức chuyên biệt (Specially Formulated)
                    </div>
                    <h1 class="text-3xl md:text-5xl lg:text-6xl font-display font-black text-brand-navy leading-tight lg:leading-none tracking-tight">
                        Dinh Dưỡng Thực Vật <br>
                        <span class="text-brand-blue relative inline-block mt-2 lg:mt-0">
                            Cho Trẻ Đặc Biệt
                            <svg class="absolute w-full h-3 -bottom-2 left-0 text-brand-gold" viewBox="0 0 200 9" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.00025 6.99997C33.5003 3.49997 73.5002 -2.00003 198 3.99999" stroke="currentColor" stroke-width="4" stroke-linecap="round"/></svg>
                        </span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600 font-medium leading-relaxed max-w-lg mx-auto lg:mx-0">
                        Giải pháp hỗ trợ dinh dưỡng cho trẻ gặp khó khăn về tiêu hóa, hành vi và ngôn ngữ.
                        <br>
                        <strong>Hypoallergenic • Gluten Free • Casein Free</strong>
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-4">
                        <button onclick="openTrialModal()" class="bg-brand-pink text-white text-lg font-bold py-4 px-8 rounded-full shadow-lg shadow-pink-200 hover:bg-pink-600 hover:shadow-pink-300 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2 w-full sm:w-auto group">
                            <i class="fas fa-gift text-brand-gold group-hover:text-white"></i>
                            Đăng Ký Dùng Thử
                        </button>
                        <a href="https://www.facebook.com/profile.php?id=61582136323865" target="_blank" rel="noopener" class="bg-white text-brand-navy border-2 border-brand-gold text-lg font-bold py-4 px-8 rounded-full hover:bg-brand-gold hover:text-white transition-all duration-300 flex items-center justify-center gap-2 w-full sm:w-auto shadow-md group">
                            <i class="fas fa-comments animate-pulse"></i> 
                            Tư vấn
                        </a>
                    </div>

                    <div class="pt-4 flex flex-wrap justify-center lg:justify-start gap-3 text-sm font-bold text-brand-navy/80">
                        <div class="flex items-center gap-2 bg-white/80 px-4 py-2 rounded-xl border border-blue-50 shadow-sm"><i class="fas fa-shield-alt text-brand-blue"></i> Dị Ứng</div>
                        <div class="flex items-center gap-2 bg-white/80 px-4 py-2 rounded-xl border border-blue-50 shadow-sm"><i class="fas fa-brain text-purple-500"></i> Phát Triển</div>
                        <div class="flex items-center gap-2 bg-white/80 px-4 py-2 rounded-xl border border-blue-50 shadow-sm"><i class="fas fa-child text-brand-gold"></i> Special Needs</div>
                    </div>
                </div>
                
                <div class="lg:w-1/2 relative reveal delay-200 text-center w-full max-w-md lg:max-w-full mx-auto">
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[350px] lg:w-[600px] h-[350px] lg:h-[600px] bg-gradient-to-tr from-brand-soft to-white rounded-full filter blur-3xl -z-10 opacity-80"></div>
                    
                    <!-- Floating Badge -->
                    <div class="hidden lg:block absolute -top-10 right-0 bg-white p-4 rounded-2xl shadow-lg z-20 animate-float border-2 border-brand-soft"><span class="text-3xl block text-center mb-1">🇲🇾</span> <span class="font-display font-bold text-brand-navy text-sm">No.1 tại Malaysia</span></div>
                    
                    <div class="relative inline-block">
                        <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Hop-sua.png" 
                             onerror="this.src='https://placehold.co/600x600/e0fbfc/1a4f8a?text=CareMIL+Product&font=baloo2'" 
                             alt="Hộp sữa CareMIL - Plant Based" 
                             class="w-full h-auto max-h-[350px] lg:max-h-[500px] object-contain drop-shadow-2xl transform hover:scale-105 transition duration-500 relative z-10">
                        
                        <!-- Sachet Image -->
                        <div onclick="openTrialModal()" class="absolute -bottom-3 lg:bottom-4 -left-4 lg:-left-16 w-40 lg:w-64 transform rotate-6 hover:rotate-0 transition duration-500 z-20 cursor-pointer group">
                            <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Goi-sua.png" 
                                 alt="Gói dùng thử CareMIL" 
                                 class="w-full drop-shadow-xl group-hover:scale-110 transition">
                            <!-- Badge: Miễn Phí (Shortened, moved left) -->
                            <div class="absolute -top-2 -left-4 lg:-top-4 lg:-left-6 bg-brand-pink text-white text-[10px] lg:text-xs font-display font-bold px-2 py-1 lg:px-3 lg:py-1.5 rounded-full shadow-lg animate-wiggle whitespace-nowrap border-2 border-white z-30 flex items-center gap-1">
                                <i class="fas fa-gift text-brand-gold text-xs lg:text-sm animate-bounce-slow"></i>
                                <span>Miễn Phí</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none"><svg class="relative block w-[calc(100%+1.3px)] h-[60px] lg:h-[120px]" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="#ffffff"></path></svg></div>
    </header>

    <!-- 3. SCIENTIFIC: GUT-BRAIN & LEAKY GUT -->
    <section id="khoa-hoc" class="py-16 lg:py-24 bg-white relative overflow-hidden">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="max-w-4xl mx-auto text-center mb-12 lg:mb-16 reveal">
                <span class="inline-block py-1 px-3 rounded-full bg-brand-blue/10 text-brand-blue font-bold text-sm mb-4">KIẾN THỨC KHOA HỌC</span>
                <h2 class="text-2xl md:text-4xl lg:text-5xl font-display font-black text-brand-navy mb-8 leading-tight">
                    Tại sao nên chọn thức uống <br>
                    <span class="text-brand-pink">Không Gluten</span> & <span class="text-brand-blue">Không Casein</span>?
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                    <div class="bg-red-50 p-6 rounded-2xl border border-red-100 relative group hover:shadow-md transition">
                        <div class="absolute -top-4 -left-4 w-10 h-10 bg-red-500 rounded-full flex items-center justify-center text-white font-bold shadow-md">1</div>
                        <h4 class="font-bold text-brand-navy text-lg mb-2">Vấn Đề Tiêu Hóa</h4>
                        <p class="text-sm text-gray-700">Vấn đề tiêu hóa với <strong>Casein</strong> (protein trong sữa) và <strong>Gluten</strong> (protein trong lúa mì).</p>
                    </div>
                    <div class="bg-orange-50 p-6 rounded-2xl border border-orange-100 relative group hover:shadow-md transition">
                        <div class="absolute -top-4 -left-4 w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold shadow-md">2</div>
                        <h4 class="font-bold text-brand-navy text-lg mb-2">Gây Viêm & Rò Rỉ</h4>
                        <p class="text-sm text-gray-700">Gây viêm nhiễm đường ruột, dẫn đến hội chứng <strong>Rò rỉ ruột (Leaky Gut)</strong>.</p>
                    </div>
                    <div class="bg-purple-50 p-6 rounded-2xl border border-purple-100 relative group hover:shadow-md transition">
                        <div class="absolute -top-4 -left-4 w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold shadow-md">3</div>
                        <h4 class="font-bold text-brand-navy text-lg mb-2">Rối Loạn Hành Vi</h4>
                        <p class="text-sm text-gray-700">Rò rỉ ruột khiến trẻ có hành vi mất kiểm soát do mối liên kết <strong>Não - Ruột</strong>.</p>
                    </div>
                </div>
            </div>
            <div class="mx-auto bg-slate-50 rounded-[40px] overflow-hidden shadow-2xl border border-slate-200 mb-16 reveal delay-100">
                <div class="p-4 lg:p-8 text-center bg-white border-b border-slate-100 relative z-10 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-center md:text-left w-full">
                        <h3 class="text-xl md:text-2xl font-display font-bold text-brand-navy flex items-center gap-2 md:justify-start justify-center">
                            <i class="fas fa-microscope text-brand-blue"></i> Mô Phỏng Cơ Chế
                        </h3>
                        <p class="text-sm text-gray-600 mt-2 mb-1 w-full md:text-left text-center">
                            Ấn vào nút <strong class="text-brand-pink">Rò Rỉ Ruột</strong> để xem mô phỏng khi hàng rào ruột bị tổn thương.
                        </p>
                    </div>
                    <div class="inline-flex bg-slate-100 p-1 rounded-full shadow-inner gap-1">
                        <button onclick="setGutMode('normal')" id="btn-normal" class="flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm transition-all duration-300 bg-white text-brand-blue shadow-md"><i class="fas fa-check-circle"></i> Ruột Khỏe Mạnh</button>
                        <button onclick="setGutMode('leaky')" id="btn-leaky" class="flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm transition-all duration-300 text-gray-500 hover:text-gray-800"><i class="fas fa-exclamation-circle"></i> Rò Rỉ Ruột</button>
                    </div>
                </div>
                <div class="relative h-[500px] md:h-[600px] select-none bg-white w-full overflow-hidden" id="gut-visualizer">
                    <div class="absolute top-0 w-full h-[60%] bg-blue-50/30 z-10 overflow-hidden border-b border-blue-100/50">
                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1.5 rounded-full text-xs font-bold text-brand-navy border border-blue-100 shadow-sm z-20">LÒNG RUỘT (LUMEN)</div>
                        <div class="absolute top-4 right-4 flex flex-col gap-2 text-[10px] md:text-xs font-bold text-slate-500 bg-white/90 p-3 rounded-xl border border-slate-100 z-20 shadow-sm">
                            <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-yellow-400 animate-wiggle"></div> Gluten</div>
                            <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-green-500 animate-wiggle"></div> Độc tố</div>
                            <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-purple-500 animate-wiggle"></div> Vi khuẩn</div>
                            <div class="flex items-center gap-2 border-t pt-1 mt-1"><div class="text-blue-200 text-sm"><i class="fas fa-shield-virus"></i></div> Kháng thể</div>
                        </div>
                        <div id="lumen-particles"></div>
                    </div>
                    <div class="absolute top-[30%] w-full h-[40%] z-0 flex items-end justify-center px-2" style="-webkit-mask-image: linear-gradient(to bottom, black 80%, transparent 100%); mask-image: linear-gradient(to bottom, black 80%, transparent 100%);">
                        <div id="mucosal-cells" class="flex w-full h-full items-end justify-center gap-[1px] transition-all duration-1000"></div>
                    </div>
                    <div class="absolute bottom-0 w-full h-[45%] bg-gradient-to-r from-red-800 via-red-600 to-red-800 z-20 overflow-hidden shadow-[0_-10px_30px_rgba(220,38,38,0.2)]">
                        <!-- Gradient overlay to soften cell base -->
                        <div class="absolute top-0 left-0 w-full h-16 bg-gradient-to-b from-white/50 via-red-100/30 to-transparent z-30 pointer-events-none blur-sm"></div>
                        
                        <div class="absolute top-4 left-4 bg-red-900/40 backdrop-blur text-white/90 text-xs font-bold uppercase tracking-widest border border-white/20 px-3 py-1 rounded-full z-20">Dòng Máu (Bloodstream)</div>
                        <div id="blood-cells-bg" class="w-full h-full relative z-0"></div>
                        <div id="immune-zone" class="absolute inset-0 z-10 opacity-0 transition-opacity duration-500 pointer-events-none"></div>
                        <div id="immune-alert" class="absolute bottom-4 right-4 flex flex-col items-end gap-2 z-20 opacity-0 transition-all duration-500 transform translate-y-4">
                            <div class="flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg text-xs md:text-sm shadow-lg font-bold animate-pulse border border-red-400"><i class="fas fa-heartbeat"></i><span>HỆ MIỄN DỊCH PHẢN ỨNG (IgG/IgA)!</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="max-w-6xl mx-auto reveal delay-200">
                <h3 id="impact-title" class="text-center text-xl md:text-3xl font-display font-bold text-brand-navy mb-8 transition-all duration-500">
                    <span class="bg-green-100 text-green-700 px-4 py-1 rounded-full text-sm font-bold uppercase mr-2 align-middle border border-green-200"><i class="fas fa-smile"></i> Tích Cực</span> Khi Đường Ruột Được Chữa Lành
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4" id="impact-grid"></div>
            </div>
        </div>
        <script>
            const impactData = { normal: { titleHTML: '<span class="bg-green-100 text-green-700 px-4 py-1 rounded-full text-sm font-bold uppercase mr-2 align-middle border border-green-200"><i class="fas fa-smile"></i> Tích Cực</span> Khi Đường Ruột Được Chữa Lành', cards: [ { icon: '😌', title: 'Cảm Xúc Ổn Định', desc: 'Con vui vẻ, giảm cáu gắt, ít các cơn bùng nổ (tantrums) vô cớ.', colorClass: 'border-green-400', iconBg: 'bg-green-50', iconColor: 'text-green-500' }, { icon: '👀', title: 'Tăng Tương Tác', desc: 'Cải thiện giao tiếp mắt (eye contact), chủ động tương tác hơn.', colorClass: 'border-green-400', iconBg: 'bg-green-50', iconColor: 'text-green-500' }, { icon: '🛌', title: 'Ngủ Ngon Giấc', desc: 'Dễ đi vào giấc ngủ, ngủ sâu, ít thức giấc giữa đêm.', colorClass: 'border-green-400', iconBg: 'bg-green-50', iconColor: 'text-green-500' }, { icon: '🧠', title: 'Tăng Tập Trung', desc: 'Giảm "sương mù não", con tỉnh táo và học hỏi tốt hơn.', colorClass: 'border-green-400', iconBg: 'bg-green-50', iconColor: 'text-green-500' }, { icon: '😋', title: 'Tiêu Hóa Khỏe', desc: 'Hết táo bón hoặc tiêu chảy, con chịu ăn đa dạng hơn.', colorClass: 'border-green-400', iconBg: 'bg-green-50', iconColor: 'text-green-500' } ] }, leaky: { titleHTML: '<span class="bg-red-100 text-red-600 px-4 py-1 rounded-full text-sm font-bold uppercase mr-2 align-middle border border-red-200"><i class="fas fa-exclamation-triangle"></i> Cảnh Báo</span> Tác Động Của Rò Rỉ Ruột & Gluten/Casein', cards: [ { icon: '😫', title: 'Rối Loạn Hành Vi', desc: 'Tăng động, giảm chú ý (ADHD), hành vi rập khuôn, tự kích thích.', colorClass: 'border-red-400', iconBg: 'bg-red-50', iconColor: 'text-red-500' }, { icon: '🔊', title: 'Rối Loạn Giác Quan', desc: 'Quá nhạy cảm với âm thanh, ánh sáng hoặc xúc giác.', colorClass: 'border-red-400', iconBg: 'bg-red-50', iconColor: 'text-red-500' }, { icon: '😶', title: 'Chậm Ngôn Ngữ', desc: 'Ảnh hưởng đến vùng ngôn ngữ, trẻ chậm nói hoặc lười giao tiếp.', colorClass: 'border-red-400', iconBg: 'bg-red-50', iconColor: 'text-red-500' }, { icon: '😵‍💫', title: 'Sương Mù Não', desc: 'Trẻ lơ đễnh, thiếu tỉnh táo, khó tiếp thu thông tin mới.', colorClass: 'border-red-400', iconBg: 'bg-red-50', iconColor: 'text-red-500' }, { icon: '🤢', title: 'Vấn Đề Tiêu Hóa', desc: 'Đau bụng, đầy hơi, táo bón mãn tính hoặc nôn trớ.', colorClass: 'border-red-400', iconBg: 'bg-red-50', iconColor: 'text-red-500' } ] } };
            const gutState = { mode: 'normal' };
            const particleTypes = [ { type: 'gluten', color: 'text-yellow-600 bg-yellow-100 border-yellow-300', icon: '<i class="fas fa-bread-slice"></i>' }, { type: 'toxin', color: 'text-green-700 bg-green-100 border-green-300', icon: '<i class="fas fa-biohazard"></i>' }, { type: 'microbe', color: 'text-purple-700 bg-purple-100 border-purple-300', icon: '<i class="fas fa-bug"></i>' } ];
            function initGutSimulation() {
                const lumen = document.getElementById('lumen-particles'); lumen.innerHTML = ''; const gapPositions = [12, 25, 37, 50, 62, 75, 87];
                for(let i=0; i<45; i++) { const p = document.createElement('div'); const pType = particleTypes[Math.floor(Math.random() * particleTypes.length)]; p.className = `particle absolute w-4 h-4 lg:w-6 lg:h-6 rounded-full flex items-center justify-center border ${pType.color} text-[10px] lg:text-xs shadow-sm z-10`; p.innerHTML = pType.icon; const isLeaker = i % 2 !== 0; if (isLeaker) { const gap = gapPositions[Math.floor(Math.random() * gapPositions.length)]; p.style.left = (gap + (Math.random() * 6 - 3)) + '%'; p.style.top = (Math.random() * 30 + 40) + '%'; p.classList.add('will-leak'); } else { p.style.left = Math.random() * 95 + '%'; p.style.top = Math.random() * 40 + '%'; } p.style.animationDuration = (3 + Math.random() * 4) + 's'; p.style.animationDelay = -Math.random() * 5 + 's'; lumen.appendChild(p); }
                const container = document.getElementById('mucosal-cells'); container.innerHTML = '';
                for(let i=0; i<8; i++) { const cellWrapper = document.createElement('div'); cellWrapper.className = "cell-container relative h-full flex-1 flex flex-col justify-end group min-w-[30px] max-w-[100px]"; const swayDuration = 5 + Math.random() * 3; const swayDelay = -Math.random() * 5; cellWrapper.innerHTML = `${i < 7 ? '<div class="tight-junction absolute -right-[2px] z-30 rounded-full transition-all duration-700"></div>' : ''}<div class="cell-body rounded-t-3xl shadow-inner flex items-center justify-center" style="animation-duration: ${swayDuration}s; animation-delay: ${swayDelay}s;"><div class="absolute -top-2 w-full flex justify-center gap-[2px] overflow-hidden px-1 opacity-60"><div class="w-1 h-2 bg-pink-400 rounded-full"></div><div class="w-1 h-3 bg-pink-400 rounded-full"></div><div class="w-1 h-2 bg-pink-400 rounded-full"></div><div class="w-1 h-3 bg-pink-400 rounded-full"></div></div><div class="cell-nucleus w-3 h-3 lg:w-5 lg:h-5 rounded-full shadow-sm border border-white/30 transition-colors duration-1000"></div></div>`; container.appendChild(cellWrapper); }
                const bloodBg = document.getElementById('blood-cells-bg'); bloodBg.innerHTML = '';
                for(let i=0; i<18; i++) { const rbc = document.createElement('div'); rbc.className = "blood-cell flex items-center justify-center opacity-80"; const size = 15 + Math.random()*25; rbc.style.width = size + 'px'; rbc.style.height = size + 'px'; rbc.style.top = Math.random() * 85 + '%'; rbc.style.animationDuration = (8 + Math.random() * 12) + 's'; rbc.style.animationDelay = -Math.random() * 20 + 's'; rbc.innerHTML = '<div class="w-1/2 h-1/2 bg-red-900/20 rounded-full shadow-inner blur-[1px]"></div>'; bloodBg.appendChild(rbc); }
                const immuneZone = document.getElementById('immune-zone'); immuneZone.innerHTML = '';
                for(let i=0; i<12; i++) { const pair = document.createElement('div'); const enemyType = particleTypes[Math.floor(Math.random() * particleTypes.length)]; pair.className = "battle-pair"; pair.style.top = (5 + Math.random() * 75) + '%'; const duration = 5 + Math.random() * 6; pair.style.animationDuration = duration + 's'; pair.style.animationDelay = -Math.random() * 20 + 's'; pair.innerHTML = `<div class="toxin-target w-8 h-8 rounded-full flex items-center justify-center border-2 ${enemyType.color} bg-white shadow-md z-10" style="animation-duration: ${duration}s">${enemyType.icon}</div><div class="antibody-actor" style="animation-duration: ${duration}s"><svg class="antibody-svg" viewBox="0 0 24 24"><path d="M12 22V12 M12 12L5 5 M12 12L19 5" /></svg></div><div class="glow-effect" style="animation-duration: ${duration}s"></div>`; immuneZone.appendChild(pair); }
            }
            function setGutMode(mode) { gutState.mode = mode; const visualizer = document.getElementById('gut-visualizer'); const btnNormal = document.getElementById('btn-normal'); const btnLeaky = document.getElementById('btn-leaky'); const immuneZone = document.getElementById('immune-zone'); const immuneAlert = document.getElementById('immune-alert');
                if (mode === 'normal') { visualizer.classList.remove('mode-leaky'); visualizer.classList.add('mode-normal'); btnNormal.className = "flex items-center gap-2 px-4 py-2 rounded-full font-bold transition-all bg-white text-brand-blue shadow-md transform scale-105"; btnLeaky.className = "flex items-center gap-2 px-4 py-2 rounded-full font-bold transition-all text-gray-500 hover:text-gray-800 hover:bg-slate-50"; immuneZone.style.opacity = '0'; immuneAlert.style.opacity = '0'; immuneAlert.style.transform = 'translateY(1rem)'; document.querySelectorAll('.particle').forEach(p => { p.classList.remove('leaking'); p.style.transitionDelay = '0s'; });
                } else { visualizer.classList.remove('mode-normal'); visualizer.classList.add('mode-leaky'); btnNormal.className = "flex items-center gap-2 px-4 py-2 rounded-full font-bold transition-all text-gray-500 hover:text-gray-800 hover:bg-slate-50"; btnLeaky.className = "flex items-center gap-2 px-4 py-2 rounded-full font-bold transition-all bg-white text-red-500 shadow-md transform scale-105"; immuneZone.style.opacity = '1'; immuneAlert.style.opacity = '1'; immuneAlert.style.transform = 'translateY(0)'; document.querySelectorAll('.particle.will-leak').forEach(p => { setTimeout(() => { if(gutState.mode === 'leaky') { p.classList.add('leaking'); } }, Math.random() * 5000); }); }
                const data = impactData[mode]; const titleEl = document.getElementById('impact-title'); const gridEl = document.getElementById('impact-grid'); titleEl.style.opacity = '0'; gridEl.style.opacity = '0'; setTimeout(() => { titleEl.innerHTML = data.titleHTML; gridEl.innerHTML = data.cards.map((card, index) => `<div class="bg-white rounded-2xl p-6 text-center border-b-4 ${card.colorClass} shadow-sm hover:-translate-y-1 transition duration-300 ${index === 4 ? 'md:col-span-1 lg:col-span-1 col-span-2' : ''}"><div class="w-14 h-14 mx-auto ${card.iconBg} rounded-full flex items-center justify-center text-3xl mb-3 ${card.iconColor}">${card.icon}</div><h4 class="font-bold text-brand-navy text-base mb-2">${card.title}</h4><p class="text-sm text-gray-600">${card.desc}</p></div>`).join(''); titleEl.style.opacity = '1'; gridEl.style.opacity = '1'; }, 300);
            }
            document.addEventListener('DOMContentLoaded', () => { 
                initGutSimulation(); 
                setGutMode('normal');
            });
        </script>
    </section>

    <!-- 4. SOLUTION: 8 NOs -->
    <section id="cam-ket" class="py-12 lg:py-20 bg-brand-soft/30 relative">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-10 lg:mb-16 reveal">
                <div class="inline-block bg-white text-brand-navy px-4 py-1 rounded-full text-xs lg:text-sm font-bold mb-3 border border-brand-blue/20">GIẢI PHÁP AN TOÀN TUYỆT ĐỐI</div>
                <h2 class="text-2xl md:text-3xl lg:text-5xl font-display font-black text-brand-navy mb-3">8 Cam Kết "KHÔNG"</h2>
                <p class="text-sm lg:text-lg text-gray-500">Loại bỏ hoàn toàn các tác nhân gây kích ứng đường ruột và thần kinh.</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6">
                <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-100"><div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-blue-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">🍞</div><h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">Gluten Free</h3><p class="text-xs lg:text-sm text-gray-400 font-medium">Không Gluten</p></div>
                <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-150"><div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-pink-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">💧</div><h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">Casein Free</h3><p class="text-xs lg:text-sm text-gray-400 font-medium">Không Casein</p></div>
                <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-200"><div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-green-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">🌱</div><h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">Soy Free</h3><p class="text-xs lg:text-sm text-gray-400 font-medium">Không Đậu Nành</p></div>
                <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-250"><div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-yellow-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">🍬</div><h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">No Added Sugar</h3><p class="text-xs lg:text-sm text-gray-400 font-medium">Không Thêm Đường</p></div>
                <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-300"><div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-orange-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">🧀</div><h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">Dairy Free</h3><p class="text-xs lg:text-sm text-gray-400 font-medium">Không Sữa Bò</p></div>
                <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-300"><div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-purple-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">🧪</div><h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">No Preservative</h3><p class="text-xs lg:text-sm text-gray-400 font-medium">Không Bảo Quản</p></div>
                <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-300"><div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-red-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">🎨</div><h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">No Colouring</h3><p class="text-xs lg:text-sm text-gray-400 font-medium">Không Phẩm Màu</p></div>
                <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-300"><div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-teal-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">🧬</div><h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">Non GMO</h3><p class="text-xs lg:text-sm text-gray-400 font-medium">Không Biến Đổi Gen</p></div>
            </div>
        </div>
    </section>

    <!-- 5. BENEFITS (Outcomes) -->
    <section id="loi-ich" class="py-12 lg:py-20 bg-white relative">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-10 lg:mb-16 reveal">
                <h2 class="text-2xl md:text-3xl lg:text-5xl font-display font-black text-brand-navy mb-4">Hỗ Trợ Toàn Diện Cho Bé</h2>
                <p class="text-sm lg:text-lg text-gray-500">Công thức dinh dưỡng được thiết kế chuyên biệt để hỗ trợ cải thiện các vấn đề thường gặp.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <div class="bg-blue-50 rounded-3xl p-6 border border-blue-100 hover:shadow-lg transition duration-300 reveal delay-100"><div class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-3xl mb-4 text-brand-blue shadow-sm">🧠</div><h3 class="text-xl font-display font-bold text-brand-navy mb-2">Hành Vi & Ngôn Ngữ</h3><p class="text-sm text-gray-600">Hỗ trợ cải thiện hành vi, nhận thức và phát triển ngôn ngữ (Speech Development) nhờ kết nối Não-Ruột khỏe mạnh.</p></div>
                <div class="bg-pink-50 rounded-3xl p-6 border border-pink-100 hover:shadow-lg transition duration-300 reveal delay-200"><div class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-3xl mb-4 text-brand-pink shadow-sm">😌</div><h3 class="text-xl font-display font-bold text-brand-navy mb-2">Giảm Lo Âu & Cảm Xúc</h3><p class="text-sm text-gray-600">Giúp trẻ giảm căng thẳng, lo âu (Reduce Anxiety) và ổn định cảm xúc hơn.</p></div>
                <div class="bg-yellow-50 rounded-3xl p-6 border border-yellow-100 hover:shadow-lg transition duration-300 reveal delay-300"><div class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-3xl mb-4 text-brand-gold shadow-sm">⚙️</div><h3 class="text-xl font-display font-bold text-brand-navy mb-2">Cải Thiện Giác Quan</h3><p class="text-sm text-gray-600">Hỗ trợ các vấn đề về rối loạn điều hòa giác quan (Sensory Issues) thường gặp ở trẻ nhạy cảm.</p></div>
                <div class="bg-green-50 rounded-3xl p-6 border border-green-100 hover:shadow-lg transition duration-300 reveal delay-400"><div class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-3xl mb-4 text-green-500 shadow-sm">🏀</div><h3 class="text-xl font-display font-bold text-brand-navy mb-2">Kỹ Năng Vận Động</h3><p class="text-sm text-gray-600">Cung cấp năng lượng và dưỡng chất để cải thiện kỹ năng vận động (Motor Skills) và thể chất.</p></div>
                <div class="bg-purple-50 rounded-3xl p-6 border border-purple-100 hover:shadow-lg transition duration-300 reveal delay-500"><div class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-3xl mb-4 text-purple-500 shadow-sm">🍽️</div><h3 class="text-xl font-display font-bold text-brand-navy mb-2">Kích Thích Ăn Ngon</h3><p class="text-sm text-gray-600">Tăng cảm giác thèm ăn (Boost Appetite) tự nhiên nhờ Lysine và Kẽm.</p></div>
                <div class="bg-brand-cream rounded-3xl p-6 border border-orange-100 hover:shadow-lg transition duration-300 reveal delay-600"><div class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-3xl mb-4 text-orange-500 shadow-sm">🛡️</div><h3 class="text-xl font-display font-bold text-brand-navy mb-2">Miễn Dịch Tự Nhiên</h3><p class="text-sm text-gray-600">Tăng cường hệ miễn dịch (Strengthen Immune System) với 24 Vitamin & Khoáng chất.</p></div>
            </div>
        </div>
    </section>

    <!-- 6. EXPERT TEAM -->
    <section id="chuyen-gia" class="py-12 lg:py-20 bg-brand-navy relative overflow-hidden text-white">
        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-10">
                <div class="lg:w-1/3">
                    <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Hop-sua.png" class="w-48 lg:w-64 mx-auto drop-shadow-2xl opacity-90 grayscale-[30%] hover:grayscale-0 transition duration-500" alt="CareMIL Expert">
                </div>
                <div class="lg:w-2/3 text-center lg:text-left">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-display font-bold mb-4 text-brand-gold">Nghiên Cứu Bởi Đội Ngũ Chuyên Gia</h2>
                    <p class="text-blue-100 text-sm lg:text-lg mb-6 leading-relaxed">
                        CareMIL được xây dựng công thức đặc biệt bởi đội ngũ <strong>Bác sĩ, Chuyên gia Dinh dưỡng Lâm sàng & Chuyên viên tư vấn</strong>. Sản phẩm chuyên biệt hỗ trợ quản lý chế độ ăn cho trẻ có nhu cầu đặc biệt.
                    </p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="bg-white/10 p-4 rounded-xl border border-white/20 backdrop-blur-sm"><i class="fas fa-user-md text-2xl mb-2 text-brand-gold"></i><p class="font-bold text-sm">Bác Sĩ</p></div>
                        <div class="bg-white/10 p-4 rounded-xl border border-white/20 backdrop-blur-sm"><i class="fas fa-flask text-2xl mb-2 text-brand-gold"></i><p class="font-bold text-sm">Dinh Dưỡng Lâm Sàng</p></div>
                        <div class="bg-white/10 p-4 rounded-xl border border-white/20 backdrop-blur-sm"><i class="fas fa-clipboard-check text-2xl mb-2 text-brand-gold"></i><p class="font-bold text-sm">Chuyên Viên Tư Vấn</p></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. INGREDIENTS OVERVIEW -->
    <section id="thanh-phan" class="py-12 lg:py-24 bg-brand-soft relative overflow-hidden">
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center mb-10 reveal">
                <h2 class="text-2xl md:text-3xl lg:text-5xl font-display font-black text-brand-navy mb-4">Khu Vườn Dinh Dưỡng</h2>
                <p class="text-sm lg:text-lg text-gray-600">Khám phá những dưỡng chất "vàng" được chắt lọc.</p>
            </div>
            <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12">
                <div class="order-1 lg:order-2 lg:w-1/3 relative flex justify-center py-6 lg:py-0 reveal">
                     <div class="absolute inset-0 flex items-center justify-center"><div class="w-64 h-64 lg:w-[500px] lg:h-[500px] bg-white rounded-full opacity-60 animate-pulse"></div></div>
                     <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Hop-sua.png" onerror="this.src='https://placehold.co/500x700/e0fbfc/1a4f8a?text=Product&font=baloo2'" alt="CareMIL Product" class="relative z-10 w-60 md:w-72 lg:w-96 drop-shadow-2xl transform hover:scale-105 transition duration-500">
                </div>
                <div class="order-2 lg:order-1 lg:w-1/3 space-y-4 lg:space-y-6 w-full">
                    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl shadow-soft flex items-center gap-4 border-l-4 lg:border-l-8 border-green-400 reveal delay-100"><div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-100 rounded-full flex-shrink-0 flex items-center justify-center text-xl lg:text-2xl">🥜</div><div><h3 class="font-display font-bold text-base lg:text-lg text-brand-navy">Pea Protein Isolate</h3><p class="text-xs lg:text-sm text-gray-500">Đạm Đậu Hà Lan giúp bé tăng cân, chắc cơ.</p></div></div>
                    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl shadow-soft flex items-center gap-4 border-l-4 lg:border-l-8 border-yellow-400 reveal delay-200"><div class="w-10 h-10 lg:w-12 lg:h-12 bg-yellow-100 rounded-full flex-shrink-0 flex items-center justify-center text-xl lg:text-2xl">🥥</div><div><h3 class="font-display font-bold text-base lg:text-lg text-brand-navy">MCT Oil</h3><p class="text-xs lg:text-sm text-gray-500">Chất béo tốt từ dừa, hấp thu nhanh, tốt cho não bộ.</p></div></div>
                </div>
                <div class="order-3 lg:order-3 lg:w-1/3 space-y-4 lg:space-y-6 w-full">
                    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl shadow-soft flex items-center gap-4 border-r-4 lg:border-r-8 border-brand-blue reveal delay-100"><div class="lg:hidden w-10 h-10 bg-blue-100 rounded-full flex-shrink-0 flex items-center justify-center text-xl">🛡️</div><div class="flex-grow lg:text-right"><h3 class="font-display font-bold text-base lg:text-lg text-brand-navy">24 Vitamin & Khoáng</h3><p class="text-xs lg:text-sm text-gray-500">Kẽm, Canxi, Vitamin D3 tăng đề kháng.</p></div><div class="hidden lg:flex w-12 h-12 bg-blue-100 rounded-full flex-shrink-0 items-center justify-center text-2xl">🛡️</div></div>
                    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl shadow-soft flex items-center gap-4 border-r-4 lg:border-r-8 border-brand-pink reveal delay-200"><div class="lg:hidden w-10 h-10 bg-pink-100 rounded-full flex-shrink-0 flex items-center justify-center text-xl">🍦</div><div class="flex-grow lg:text-right"><h3 class="font-display font-bold text-base lg:text-lg text-brand-navy">Hương Vani Tự Nhiên</h3><p class="text-xs lg:text-sm text-gray-500">Thơm dịu, ngọt nhẹ, bé nào cũng thích.</p></div><div class="hidden lg:flex w-12 h-12 bg-pink-100 rounded-full flex-shrink-0 items-center justify-center text-2xl">🍦</div></div>
                </div>
            </div>
        </div>
    </section>

    <!-- NEW SECTION: USP TRIO (Lysine, Choline, Glutamine) -->
    <section class="py-16 lg:py-24 bg-gradient-to-b from-white to-blue-50 relative">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-12 lg:mb-16 reveal">
                <h2 class="text-2xl md:text-3xl lg:text-5xl font-display font-black text-brand-navy mb-4">
                    Bộ 3 Dưỡng Chất Vàng <br>
                    <span class="text-brand-blue">Hỗ Trợ Trí Não & Thể Chất</span>
                </h2>
                <p class="text-sm lg:text-lg text-gray-600">Công thức đặc biệt bổ sung các axit amin thiết yếu giúp trẻ phát triển toàn diện.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-10">
                <!-- Card 1 -->
                <div class="bg-white rounded-[30px] p-8 shadow-xl border-t-4 border-brand-blue hover:-translate-y-2 transition duration-300 reveal delay-100 flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center text-4xl mb-6 text-brand-blue shadow-inner"><i class="fas fa-smile-beam"></i></div>
                    <h3 class="text-2xl font-display font-bold text-brand-blue mb-4">L-lysine</h3>
                    <ul class="text-left space-y-3 w-full"><li class="flex items-start gap-3 text-gray-600"><i class="fas fa-check-circle text-brand-gold mt-1 flex-shrink-0"></i><span><strong>Giảm Lo Âu:</strong> Giúp trẻ thư giãn, giảm căng thẳng thần kinh.</span></li><li class="flex items-start gap-3 text-gray-600"><i class="fas fa-check-circle text-brand-gold mt-1 flex-shrink-0"></i><span><strong>Tăng Cơ Bắp:</strong> Hỗ trợ tổng hợp protein, xây dựng khối cơ.</span></li><li class="flex items-start gap-3 text-gray-600"><i class="fas fa-check-circle text-brand-gold mt-1 flex-shrink-0"></i><span><strong>Kích Thích Ăn Ngon:</strong> Tăng cảm giác thèm ăn tự nhiên.</span></li></ul>
                </div>
                <!-- Card 2 -->
                <div class="bg-white rounded-[30px] p-8 shadow-xl border-t-4 border-blue-600 hover:-translate-y-2 transition duration-300 reveal delay-200 flex flex-col items-center text-center transform lg:scale-105 z-10 relative">
                    <div class="absolute -top-5 left-1/2 -translate-x-1/2 bg-brand-gold text-brand-navy text-xs font-bold px-4 py-1 rounded-full shadow-md">QUAN TRỌNG CHO NÃO BỘ</div>
                    <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center text-4xl mb-6 text-white shadow-lg"><i class="fas fa-brain"></i></div>
                    <h3 class="text-2xl font-display font-bold text-blue-800 mb-4">Choline</h3>
                    <ul class="text-left space-y-3 w-full"><li class="flex items-start gap-3 text-gray-600"><i class="fas fa-check-circle text-brand-gold mt-1 flex-shrink-0"></i><span><strong>Kỹ Năng Vận Động:</strong> Cải thiện sự phối hợp giữa não bộ và cơ bắp.</span></li><li class="flex items-start gap-3 text-gray-600"><i class="fas fa-check-circle text-brand-gold mt-1 flex-shrink-0"></i><span><strong>Phát Triển Nhận Thức:</strong> Tăng cường trí nhớ và khả năng tập trung.</span></li></ul>
                </div>
                <!-- Card 3 -->
                <div class="bg-white rounded-[30px] p-8 shadow-xl border-t-4 border-purple-500 hover:-translate-y-2 transition duration-300 reveal delay-300 flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center text-4xl mb-6 text-purple-600 shadow-inner"><i class="fas fa-shield-alt"></i></div>
                    <h3 class="text-2xl font-display font-bold text-purple-600 mb-4">L-glutamine</h3>
                    <ul class="text-left space-y-3 w-full"><li class="flex items-start gap-3 text-gray-600"><i class="fas fa-check-circle text-brand-gold mt-1 flex-shrink-0"></i><span><strong>Tăng Cường Miễn Dịch:</strong> Củng cố hàng rào bảo vệ cơ thể.</span></li><li class="flex items-start gap-3 text-gray-600"><i class="fas fa-check-circle text-brand-gold mt-1 flex-shrink-0"></i><span><strong>Phục Hồi Đường Ruột:</strong> Hỗ trợ tái tạo niêm mạc ruột, giảm viêm nhiễm.</span></li></ul>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. FIBREGUM SPOTLIGHT -->
    <section class="py-16 lg:py-24 bg-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-brand-soft/30 -skew-x-12 transform origin-top-right z-0"></div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                <div class="lg:w-1/2 relative reveal">
                    <div class="relative bg-gradient-to-br from-green-50 to-white border-2 border-green-100 rounded-[40px] p-8 lg:p-12 shadow-xl">
                        <div class="text-center mb-8">
                            <h3 class="text-4xl lg:text-6xl font-sans font-thin text-green-600 tracking-tighter mb-2">Fibregum<span class="text-lg align-top">™</span></h3>
                            <div class="h-1 w-24 bg-brand-gold mx-auto rounded-full"></div>
                        </div>
                        <div class="flex justify-center mb-8">
                            <div class="w-48 h-48 bg-green-100 rounded-full flex items-center justify-center text-8xl text-green-600 relative overflow-hidden border-4 border-white shadow-inner">
                                <i class="fas fa-tree"></i><div class="absolute top-4 right-4 text-brand-gold text-4xl animate-spin-slow"><i class="fas fa-sun"></i></div>
                            </div>
                        </div>
                        <div class="absolute -bottom-6 -right-6 bg-white py-3 px-6 rounded-2xl shadow-lg border border-gray-100 flex items-center gap-3 animate-bounce-slow">
                            <span class="text-3xl">🇫🇷</span>
                            <div class="text-left"><p class="text-xs font-bold text-gray-400 uppercase">Trademark of</p><p class="text-brand-navy font-bold font-display">Nexira, France</p></div>
                        </div>
                    </div>
                </div>
                <div class="lg:w-1/2 space-y-6 reveal delay-200">
                    <div class="inline-block bg-green-100 text-green-700 px-4 py-2 rounded-full font-bold text-sm mb-2"><i class="fas fa-star mr-2"></i> Thành Phần Vàng</div>
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-display font-black text-brand-navy leading-tight">Chất Xơ Thế Hệ Mới <br><span class="text-green-500">Nuôi Dưỡng Đường Ruột</span></h2>
                    <p class="text-lg text-gray-600 leading-relaxed"><strong>Fibregum™</strong> là chất xơ hòa tan 100% từ thực vật (cây Acacia), nhập khẩu từ Pháp. Giải pháp tiêu hóa vượt trội cho trẻ nhạy cảm.</p>
                    <ul class="space-y-4 mt-6">
                        <li class="flex items-start gap-4"><div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-500 flex-shrink-0 text-xl"><i class="fas fa-leaf"></i></div><div><h4 class="font-bold text-brand-navy text-lg">Siêu Prebiotic</h4><p class="text-sm text-gray-500">Thức ăn cho lợi khuẩn, cân bằng hệ vi sinh.</p></div></li>
                        <li class="flex items-start gap-4"><div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-brand-blue flex-shrink-0 text-xl"><i class="fas fa-check-double"></i></div><div><h4 class="font-bold text-brand-navy text-lg">Dung Nạp Tốt</h4><p class="text-sm text-gray-500">Êm bụng, không gây đầy hơi, chướng bụng.</p></div></li>
                        <li class="flex items-start gap-4"><div class="w-10 h-10 rounded-full bg-yellow-50 flex items-center justify-center text-brand-gold flex-shrink-0 text-xl"><i class="fas fa-shield-cat"></i></div><div><h4 class="font-bold text-brand-navy text-lg">Bảo Vệ Niêm Mạc</h4><p class="text-sm text-gray-600">Củng cố hàng rào bảo vệ ruột, ngăn ngừa rò rỉ ruột.</p></div></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- NEW SECTION: PRODUCT VARIANTS (Podium) -->
    <section id="san-pham" class="py-16 lg:py-24 bg-gradient-to-b from-blue-50 to-white relative overflow-hidden">
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center mb-12 lg:mb-16 reveal">
                <span class="inline-block py-1 px-3 rounded-full bg-brand-gold/20 text-brand-navy font-bold text-sm mb-4 border border-brand-gold/30">LỰA CHỌN CỦA MẸ</span>
                <h2 class="text-2xl md:text-3xl lg:text-5xl font-display font-black text-brand-navy mb-4">Phiên Bản Phù Hợp Mọi Nhu Cầu</h2>
                <p class="text-sm lg:text-lg text-gray-600">Dù dùng tại nhà hay mang đi xa, CareMIL luôn sẵn sàng đồng hành cùng bé.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-24 items-end max-w-5xl mx-auto">
                
                <!-- Product 1: Can 800g -->
                <div class="flex flex-col items-center group reveal delay-100 relative">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-blue-100 rounded-full filter blur-3xl opacity-0 group-hover:opacity-50 transition duration-700"></div>
                    <div class="relative w-full flex justify-center items-end h-[350px] lg:h-[450px] mb-8 perspective-1000">
                        <div class="absolute bottom-0 w-56 lg:w-72 h-16 bg-gradient-to-b from-white to-gray-200 rounded-[100%] shadow-[0_20px_50px_rgba(0,0,0,0.1)] border-t border-white z-0 group-hover:scale-110 transition duration-700 ease-out"></div>
                        <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Hop-sua.png" class="relative z-10 w-52 lg:w-72 drop-shadow-2xl transform group-hover:-translate-y-6 transition duration-700 ease-out" alt="CareMIL Hộp 800g">
                        <div class="absolute top-10 right-10 bg-brand-gold text-brand-navy font-bold text-xs px-3 py-1 rounded-full shadow-md animate-bounce-slow z-20">Tiết Kiệm</div>
                    </div>
                    <h3 class="text-2xl lg:text-3xl font-display font-black text-brand-navy mb-2">Hộp 800g</h3>
                    <p class="text-gray-500 text-sm lg:text-base mb-6 text-center px-8 max-w-sm">Giải pháp dinh dưỡng kinh tế cho bé sử dụng hàng ngày tại nhà. Bảo quản tốt, dễ dàng pha chế.</p>
                    <a href="cua-hang" class="bg-white border-2 border-brand-blue text-brand-blue font-bold py-3 px-8 rounded-full shadow-sm hover:bg-brand-blue hover:text-white transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2"><i class="fas fa-cart-plus"></i> Đặt Mua Hộp</a>
                </div>

                <!-- Product 2: Sachet 36g -->
                <div class="flex flex-col items-center group reveal delay-200 relative">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-pink-100 rounded-full filter blur-3xl opacity-0 group-hover:opacity-50 transition duration-700"></div>
                    <div class="relative w-full flex justify-center items-end h-[350px] lg:h-[450px] mb-8 perspective-1000">
                         <div class="absolute bottom-0 w-48 lg:w-64 h-14 bg-gradient-to-b from-white to-gray-200 rounded-[100%] shadow-[0_20px_50px_rgba(0,0,0,0.1)] border-t border-white z-0 group-hover:scale-110 transition duration-700 ease-out"></div>
                        <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/3-Goi-sua.png" class="relative z-10 w-40 lg:w-56 drop-shadow-2xl transform group-hover:rotate-0 group-hover:-translate-y-6 transition duration-700 ease-out" alt="CareMIL Gói 36g">
                        <div class="absolute top-40 right-16 bg-brand-pink text-white font-bold text-xs px-3 py-1 rounded-full shadow-md animate-wiggle z-20">Tiện Lợi</div>
                    </div>
                    <h3 class="text-2xl lg:text-3xl font-display font-black text-brand-navy mb-2">Gói Tiện Lợi 36g</h3>
                    <p class="text-gray-500 text-sm lg:text-base mb-6 text-center px-8 max-w-sm">1 gói = 1 lần pha chuẩn (180ml). Hoàn hảo để mang đi học, đi du lịch hoặc cho bé dùng thử.</p>
                    <button onclick="openTrialModal()" class="bg-brand-pink text-white font-bold py-3 px-8 rounded-full shadow-lg hover:bg-pink-600 transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2"><i class="fas fa-gift"></i> Đăng Ký Dùng Thử</button>
                </div>

            </div>
        </div>
    </section>

    <!-- 9. DETAILED INGREDIENTS (Gatekeeper) -->
    <section id="bang-thanh-phan" class="py-12 lg:py-20 bg-white relative">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="text-center mb-8 lg:mb-10 reveal">
                <h2 class="text-2xl md:text-3xl lg:text-5xl font-display font-black text-brand-navy mb-4">Chi Tiết Thành Phần</h2>
                <p class="text-sm lg:text-lg text-gray-600 max-w-2xl mx-auto">Công bố minh bạch bảng thành phần dinh dưỡng.</p>
            </div>
            <div id="ingredients-lock" class="text-center py-6 lg:py-10">
                <div class="bg-blue-50 border-2 border-brand-blue/20 rounded-3xl p-6 lg:p-8 max-w-3xl mx-auto shadow-sm">
                    <div class="text-3xl lg:text-4xl mb-4 text-brand-blue"><i class="fas fa-user-md"></i></div>
                    <h3 class="text-xl lg:text-2xl font-display font-bold text-brand-navy mb-3 lg:mb-4">Thông Tin Chuyên Sâu</h3>
                    <p class="text-sm lg:text-base text-gray-600 mb-6">Xác nhận bạn quan tâm đến các thông tin dinh dưỡng chuyên sâu.</p>
                    <button onclick="openExpertModal()" class="bg-brand-blue text-white font-bold py-2 lg:py-3 px-6 lg:px-8 rounded-full shadow-lg hover:bg-blue-400 transition transform hover:scale-105 flex items-center gap-2 mx-auto text-sm lg:text-base"><i class="fas fa-eye"></i> Xem Chi Tiết</button>
                </div>
            </div>
            <div id="ingredients-container" class="hidden">
                <div class="flex overflow-x-auto hide-scrollbar pb-4 space-x-3 mb-6 lg:mb-10 lg:justify-center px-1 snap-x">
                    <button onclick="switchTab('nutrition')" id="tab-nutrition" class="tab-btn active flex-shrink-0 snap-center px-5 py-2 lg:px-8 lg:py-3 rounded-full bg-gray-100 text-gray-500 font-bold font-display text-sm lg:text-lg whitespace-nowrap">DINH DƯỠNG</button>
                    <button onclick="switchTab('vitamins')" id="tab-vitamins" class="tab-btn flex-shrink-0 snap-center px-5 py-2 lg:px-8 lg:py-3 rounded-full bg-gray-100 text-gray-500 font-bold font-display text-sm lg:text-lg whitespace-nowrap">VITAMINS</button>
                    <button onclick="switchTab('minerals')" id="tab-minerals" class="tab-btn flex-shrink-0 snap-center px-5 py-2 lg:px-8 lg:py-3 rounded-full bg-gray-100 text-gray-500 font-bold font-display text-sm lg:text-lg whitespace-nowrap">KHOÁNG CHẤT</button>
                    <button onclick="switchTab('other')" id="tab-other" class="tab-btn flex-shrink-0 snap-center px-5 py-2 lg:px-8 lg:py-3 rounded-full bg-gray-100 text-gray-500 font-bold font-display text-sm lg:text-lg whitespace-nowrap">THÀNH PHẦN KHÁC</button>
                </div>
                <div class="bg-brand-soft rounded-3xl lg:rounded-[40px] p-4 lg:p-12 shadow-soft reveal delay-200">
                    <div id="content-nutrition" class="tab-content active">
                        <h3 class="text-lg lg:text-2xl font-display font-bold text-brand-navy mb-2 text-center">Thông Tin Dinh Dưỡng</h3>
                        <p class="text-center text-gray-500 mb-4 lg:mb-6 font-medium text-xs lg:text-base">Serving Size: 3 scoops (36g) • Servings: 22</p>
                        <div class="overflow-x-auto"><table class="w-full text-left border-collapse nutrition-table bg-white rounded-xl lg:rounded-2xl overflow-hidden shadow-sm text-xs md:text-sm lg:text-base min-w-[500px]"><thead class="bg-brand-blue text-white"><tr><th class="p-3 lg:p-4">Thành phần</th><th class="p-3 lg:p-4 text-center">Đơn vị</th><th class="p-3 lg:p-4 text-right">Per 100g</th><th class="p-3 lg:p-4 text-right">Per Serving</th></tr></thead><tbody class="text-gray-600"><tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold text-brand-navy">Energy</td><td class="p-3 lg:p-4 text-center">kcal</td><td class="p-3 lg:p-4 text-right">389</td><td class="p-3 lg:p-4 text-right">140</td></tr><tr class="border-b hover:bg-blue-50 bg-gray-50/50"><td class="p-3 lg:p-4 font-bold">Fat</td><td class="p-3 lg:p-4 text-center">g</td><td class="p-3 lg:p-4 text-right">11.5</td><td class="p-3 lg:p-4 text-right">4.1</td></tr><tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">MCT Oil (added)</td><td class="p-3 lg:p-4 text-center">-</td><td class="p-3 lg:p-4 text-right">Included</td><td class="p-3 lg:p-4 text-right">Included</td></tr></tbody></table></div>
                    </div>
                    <div id="content-vitamins" class="tab-content"><h3 class="text-lg lg:text-2xl font-display font-bold text-brand-navy mb-4 lg:mb-6 text-center">24 Vitamin & Khoáng Chất</h3><div class="overflow-x-auto"><table class="w-full text-left border-collapse nutrition-table bg-white rounded-xl lg:rounded-2xl overflow-hidden shadow-sm text-xs md:text-sm lg:text-base min-w-[500px]"><thead class="bg-brand-blue text-white"><tr><th class="p-3 lg:p-4">Thành phần</th><th class="p-3 lg:p-4 text-center">Đơn vị</th><th class="p-3 lg:p-4 text-right">Per 100g</th><th class="p-3 lg:p-4 text-right">Per Serving</th></tr></thead><tbody class="text-gray-600"><tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Vitamin A</td><td class="p-3 lg:p-4 text-center">mcg</td><td class="p-3 lg:p-4 text-right">540.0</td><td class="p-3 lg:p-4 text-right">194.4</td></tr><tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Folic Acid (Vit B9)</td><td class="p-3 lg:p-4 text-center">mcg</td><td class="p-3 lg:p-4 text-right">165.0</td><td class="p-3 lg:p-4 text-right">59.4</td></tr></tbody></table></div></div>
                    <div id="content-minerals" class="tab-content"><h3 class="text-lg lg:text-2xl font-display font-bold text-brand-navy mb-4 lg:mb-6 text-center">Khoáng Chất Thiết Yếu</h3><div class="overflow-x-auto"><table class="w-full text-left border-collapse nutrition-table bg-white rounded-xl lg:rounded-2xl overflow-hidden shadow-sm text-xs md:text-sm lg:text-base min-w-[500px]"><thead class="bg-brand-blue text-white"><tr><th class="p-3 lg:p-4">Thành phần</th><th class="p-3 lg:p-4 text-center">Đơn vị</th><th class="p-3 lg:p-4 text-right">Per 100g</th><th class="p-3 lg:p-4 text-right">Per Serving</th></tr></thead><tbody class="text-gray-600"><tr class="border-b hover:bg-blue-50 bg-gray-50/50"><td class="p-3 lg:p-4 font-bold text-brand-navy">Calcium</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right font-bold">535.7</td><td class="p-3 lg:p-4 text-right font-bold">192.9</td></tr></tbody></table></div></div>
                    <div id="content-other" class="tab-content"><h3 class="text-lg lg:text-2xl font-display font-bold text-brand-navy mb-4 lg:mb-6 text-center">Thành Phần Khác</h3><div class="overflow-x-auto"><table class="w-full text-left border-collapse nutrition-table bg-white rounded-xl lg:rounded-2xl overflow-hidden shadow-sm text-xs md:text-sm lg:text-base min-w-[500px]"><thead class="bg-brand-blue text-white"><tr><th class="p-3 lg:p-4">Thành phần</th><th class="p-3 lg:p-4 text-center">Đơn vị</th><th class="p-3 lg:p-4 text-right">Per 100g</th><th class="p-3 lg:p-4 text-right">Per Serving</th></tr></thead><tbody class="text-gray-600"><tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold text-brand-navy">Probiotic (L. acidophilus & B. lactis)</td><td class="p-3 lg:p-4 text-center">cfu</td><td class="p-3 lg:p-4 text-right">1 x 10<sup>9</sup></td><td class="p-3 lg:p-4 text-right">360 x 10<sup>6</sup></td></tr></tbody></table></div><div class="mt-4 lg:mt-8 pt-4 border-t border-gray-100 text-center"><p class="text-xs lg:text-sm text-gray-500 italic mb-2">Thành phần: Pea Protein Isolate, Vegetable Oil, Fibregum™, Vanilla.</p></div></div>
                </div>
            </div>
        </div>
    </section>

    <!-- 10. PREPARATION -->
    <section id="huong-dan" class="py-12 lg:py-24 bg-white relative overflow-hidden">
        <!-- ... same content as before ... -->
        <div class="container mx-auto px-6 text-center relative z-10">
            <h2 class="text-2xl md:text-3xl lg:text-5xl font-display font-black text-brand-navy mb-3 lg:mb-4 reveal">Pha Sữa Đúng Chuẩn</h2>
            <p class="text-sm lg:text-lg text-gray-500 mb-8 lg:mb-16 max-w-2xl mx-auto reveal">4 bước đơn giản để có ly sữa thơm ngon.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                <div class="bg-brand-cream rounded-[20px] lg:rounded-[30px] p-6 lg:p-8 border-4 border-white shadow-soft group reveal delay-100"><div class="w-16 h-16 lg:w-20 lg:h-20 mx-auto bg-blue-100 rounded-full flex items-center justify-center text-2xl lg:text-3xl text-brand-blue mb-4 lg:mb-6"><i class="fas fa-hands-wash"></i></div><div class="inline-block bg-brand-blue text-white text-xs lg:text-sm font-bold px-3 py-1 rounded-full mb-3">Bước 1</div><h3 class="text-lg lg:text-xl font-display font-bold text-brand-navy mb-2">Vệ Sinh</h3><p class="text-gray-500 text-sm">Rửa sạch tay và dụng cụ.</p></div>
                <div class="bg-brand-cream rounded-[20px] lg:rounded-[30px] p-6 lg:p-8 border-4 border-white shadow-soft group reveal delay-200"><div class="w-16 h-16 lg:w-20 lg:h-20 mx-auto bg-pink-100 rounded-full flex items-center justify-center text-2xl lg:text-3xl text-brand-pink mb-4 lg:mb-6"><i class="fas fa-temperature-low"></i></div><div class="inline-block bg-brand-pink text-white text-xs lg:text-sm font-bold px-3 py-1 rounded-full mb-3">Bước 2</div><h3 class="text-lg lg:text-xl font-display font-bold text-brand-navy mb-2">Nước Ấm</h3><p class="text-gray-500 text-sm">Đun sôi 5 phút, để nguội 45°C.</p></div>
                <div class="bg-brand-cream rounded-[20px] lg:rounded-[30px] p-6 lg:p-8 border-4 border-white shadow-soft group reveal delay-300"><div class="w-16 h-16 lg:w-20 lg:h-20 mx-auto bg-yellow-100 rounded-full flex items-center justify-center text-2xl lg:text-3xl text-brand-gold mb-4 lg:mb-6"><span class="font-black font-sans">x3</span></div><div class="inline-block bg-brand-gold text-white text-xs lg:text-sm font-bold px-3 py-1 rounded-full mb-3">Bước 3</div><h3 class="text-lg lg:text-xl font-display font-bold text-brand-navy mb-2">Pha Sữa</h3><p class="text-gray-500 text-sm">3 muỗng (36g) + 180ml nước.</p></div>
                <div class="bg-brand-cream rounded-[20px] lg:rounded-[30px] p-6 lg:p-8 border-4 border-white shadow-soft group reveal delay-400"><div class="w-16 h-16 lg:w-20 lg:h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center text-2xl lg:text-3xl text-green-500 mb-4 lg:mb-6"><i class="fas fa-mug-hot"></i></div><div class="inline-block bg-green-500 text-white text-xs lg:text-sm font-bold px-3 py-1 rounded-full mb-3">Bước 4</div><h3 class="text-lg lg:text-xl font-display font-bold text-brand-navy mb-2">Hoàn Tất</h3><p class="text-gray-500 text-sm">Khuấy đều và dùng ngay.</p></div>
            </div>
        </div>
    </section>

    <!-- 11. STORAGE & ORIGIN -->
    <section id="bao-quan" class="py-12 lg:py-20 bg-gray-50 border-t border-gray-100">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="reveal"><h3 class="text-2xl lg:text-3xl font-display font-black text-brand-navy mb-6 flex items-center gap-3"><i class="fas fa-box-open text-brand-gold"></i> Bảo Quản Đúng Cách</h3><div class="space-y-6"><div class="flex items-start gap-4"><div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-brand-blue shadow-sm flex-shrink-0 text-xl"><i class="fas fa-temperature-empty"></i></div><div><h4 class="font-bold text-brand-navy text-lg">Chưa mở nắp</h4><p class="text-sm text-gray-600">Nơi khô ráo, thoáng mát.</p></div></div><div class="flex items-start gap-4"><div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-brand-blue shadow-sm flex-shrink-0 text-xl"><i class="fas fa-calendar-check"></i></div><div><h4 class="font-bold text-brand-navy text-lg">Sau khi mở nắp</h4><p class="text-sm text-gray-600">Dùng hết trong 1 tháng. Không để tủ lạnh.</p></div></div></div></div>
                <div class="bg-white rounded-3xl p-8 shadow-soft border border-brand-soft reveal delay-200">
                    <h3 class="text-2xl lg:text-3xl font-display font-black text-brand-navy mb-6 flex items-center gap-3"><i class="fas fa-certificate text-brand-gold"></i> Nguồn Gốc Xuất Xứ</h3>
                    <div class="space-y-4">
                        <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100 flex items-center gap-4"><div class="text-3xl">🇲🇾</div><div><p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Xuất Xứ</p><p class="text-brand-navy font-bold">Malaysia</p></div></div>
                        <div class="p-4 bg-yellow-50 rounded-2xl border border-yellow-100 flex items-center gap-4"><div class="w-12 h-12 flex items-center justify-center bg-white rounded-full shadow-sm text-brand-gold font-bold border-2 border-brand-gold">GMP</div><div><p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Tiêu Chuẩn</p><p class="text-brand-navy font-bold">GMP & Halal</p></div></div>
                        <div class="pt-4 border-t border-gray-100 text-sm text-gray-500">
                            <p><strong>Owner:</strong> DAWN BRIDGE SDN BHD</p>
                            <p><strong>Manufacturer:</strong> OMEGA HEALTH PRODUCTS SDN BHD</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 12. FOOTER -->
    <footer class="bg-white pt-12 lg:pt-20 pb-8 lg:pb-10 mt-0 relative" id="order">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-brand-blue via-brand-pink to-brand-gold"></div>
        <div class="container mx-auto px-6">
            <!-- Footer Content -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 border-t border-gray-100 pt-8 lg:pt-12 text-center lg:text-left">
                
                <!-- Brand & Contact (Left - 4 cols) -->
                <div class="lg:col-span-4 space-y-4">
                    <div class="flex items-center justify-center lg:justify-start gap-4">
                        <a href="https://caremil.dawnbridge.vn" class="text-2xl font-display font-black text-brand-navy tracking-tight flex items-center gap-2 hover:underline">
                            <i class="fas fa-leaf text-brand-gold"></i> Care<span class="text-brand-blue">MIL</span>
                        </a>
                        <span class="text-2xl font-display font-black text-gray-300">|</span>
                        <a href="https://dawnbridge.vn" class="text-2xl font-display font-black text-brand-navy tracking-tight flex items-center gap-2 hover:underline" target="_blank" rel="noopener">
                            <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Dawnbridge-logo-e1764735620422.png" alt="DawnBridge Logo" class="h-4 w-auto inline-block align-middle" />
                        </a>
                    </div>
                    <div class="bg-blue-50 rounded-2xl p-4 border border-blue-100 inline-block w-full">
                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Tổng đài CSKH</p>
                        <p class="text-2xl font-black text-brand-pink">(+84) 985 39 18 81</p>
                        <p class="text-sm text-brand-navy font-bold mt-1">cskh@npfood.vn</p>
                    </div>
                    <div class="flex justify-center lg:justify-start space-x-3 mt-4">
                        <a href="https://www.facebook.com/caremilvietnam/" target="_blank" class="w-10 h-10 rounded-full bg-brand-navy text-white flex items-center justify-center hover:bg-brand-blue transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://caremil.dawnbridge.vn" target="_blank" class="w-10 h-10 rounded-full bg-brand-navy text-white flex items-center justify-center hover:bg-brand-blue transition"><i class="fas fa-globe"></i></a>
                    </div>
                </div>

                <!-- Distributor Info (Middle - 5 cols) -->
                <div class="lg:col-span-5 text-sm space-y-4 text-gray-600">
                    <div>
                        <h4 class="font-bold text-lg mb-2 text-brand-navy font-display">Nhập Khẩu & Phân Phối Tại Việt Nam</h4>
                        <p class="font-bold text-gray-800">CÔNG TY TNHH NP FOOD (NP FOOD COMPANY LIMITED)</p>
                        <p><i class="fas fa-map-marker-alt text-brand-gold mr-2"></i> Group 4, Quang Minh, Ha Noi, Vietnam.</p>
                        <p><i class="fas fa-id-card text-brand-gold mr-2"></i> TIN: 0109082378</p>
                    </div>
                    <div class="pt-4 border-t border-gray-100 mt-4">
                        <h4 class="font-bold text-base mb-2 text-brand-navy">Chủ Sở Hữu & Sản Xuất (Malaysia)</h4>
                        <p><strong>Owner:</strong> DAWN BRIDGE SDN BHD</p>
                        <p class="text-xs">29 & 29-01, Jalan Kempas Utama 1/3, Taman Kempas Utama, 81300 Johor Bahru, Johor.</p>
                        <p class="mt-2"><strong>Manufacturer:</strong> OMEGA HEALTH PRODUCTS SDN BHD</p>
                        <p class="text-xs">No. 30, Jalan Mega A, Bandar Teknologi Kajang, 43500 Semenyih, Selangor.</p>
                    </div>
                </div>

                <!-- Certifications (Right - 3 cols) -->
                <div class="lg:col-span-3 flex flex-col items-center lg:items-end gap-4">
                    <h4 class="font-bold text-lg text-brand-navy font-display">Chứng Nhận Quốc Tế</h4>
                    <div class="flex gap-3">
                        <div class="w-16 h-16 bg-white border-2 border-green-600 rounded-full flex items-center justify-center p-1" title="Halal Certified">
                            <!-- Simple CSS Halal Icon Representation -->
                            <div class="text-center leading-none">
                                <span class="block text-[8px] font-bold text-green-700">HALAL</span>
                                <span class="block text-xl font-bold text-green-700">حلال</span>
                            </div>
                        </div>
                        <div class="w-16 h-16 bg-white border-2 border-brand-gold rounded-full flex items-center justify-center shadow-sm">
                            <span class="font-bold text-brand-gold">GMP</span>
                        </div>
                        <div class="w-16 h-16 bg-white border-2 border-blue-800 rounded-full flex items-center justify-center shadow-sm text-xs text-center font-bold text-blue-900 p-1">
                            Made in<br>Malaysia
                        </div>
                    </div>
                    <div class="mt-4 p-2 bg-white border border-gray-200 rounded-lg">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=http://www.dawnbridge.com.my" alt="QR Code" class="w-20 h-20">
                        <p class="text-[10px] text-center mt-1 text-gray-500">Scan for info</p>
                    </div>
                </div>
            </div>
            
            <div class="pt-8 text-center text-gray-400 text-xs lg:text-sm font-medium border-t border-gray-100 mt-8">
                &copy; 2025 DawnBridge Vietnam. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- 13. MODALS -->
    
    <!-- Expert Confirmation Modal -->
    <div id="expert-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 lg:p-8 text-center relative border-4 border-brand-blue mx-4">
            <div class="w-16 h-16 lg:w-20 lg:h-20 bg-brand-blue/10 rounded-full flex items-center justify-center mx-auto mb-4 lg:mb-6 text-3xl lg:text-4xl text-brand-blue"><i class="fas fa-user-shield"></i></div>
            <h3 class="text-xl lg:text-2xl font-display font-black text-brand-navy mb-3 lg:mb-4">Thông Báo Quan Trọng</h3>
            <p class="text-sm lg:text-base text-gray-600 mb-6 leading-relaxed">Nội dung dành cho <strong>Nhân viên Y tế</strong> hoặc <strong>Người tìm hiểu chuyên sâu</strong>.</p>
            <div class="flex flex-col sm:flex-row gap-3 lg:gap-4 justify-center"><button onclick="closeExpertModal()" class="px-6 py-2 lg:py-3 rounded-full border-2 border-gray-300 text-gray-500 font-bold hover:bg-gray-100 transition text-sm lg:text-base">Quay Lại</button><button onclick="confirmExpert()" class="px-6 py-2 lg:py-3 rounded-full bg-brand-blue text-white font-bold hover:bg-blue-400 transition shadow-lg text-sm lg:text-base">Xác Nhận</button></div>
        </div>
    </div>

    <!-- TRIAL REGISTRATION FORM MODAL (Updated Visual) -->
    <div id="trial-modal" class="fixed inset-0 z-[110] hidden items-center justify-center p-4 bg-black/70 backdrop-blur-sm overflow-y-auto">
        <div class="bg-white rounded-[30px] shadow-2xl max-w-lg w-full p-6 lg:p-10 relative border-4 border-brand-soft my-8">
            <button onclick="closeTrialModal()" class="absolute top-4 right-4 text-gray-400 hover:text-brand-navy transition text-2xl"><i class="fas fa-times"></i></button>
            
            <h3 class="text-2xl lg:text-3xl font-display font-black text-brand-navy text-center mb-6 border-b-4 border-brand-gold inline-block w-full pb-2">ĐĂNG KÝ NHẬN QUÀ</h3>
            
            <form class="space-y-4" data-caremil-trial-form data-caremil-source="modal" data-caremil-close-on-success="true">
                <div>
                    <label class="form-label">Họ và tên</label>
                    <input type="text" name="caremil_name" class="form-input" placeholder="Vui lòng nhập họ và tên" data-caremil-required>
                    <p class="text-red-500 text-xs mt-1 italic hidden" data-caremil-error="caremil_name">Vui lòng nhập họ và tên.</p>
                </div>
                <div>
                    <label class="form-label">Tỉnh/Thành Phố</label>
                    <input type="text" name="caremil_city" class="form-input" placeholder="Vui lòng nhập Tỉnh/Thành phố bạn đang ở" data-caremil-required>
                    <p class="text-red-500 text-xs mt-1 italic hidden" data-caremil-error="caremil_city">Vui lòng nhập Tỉnh/Thành phố.</p>
                </div>
                <div>
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="caremil_address" class="form-input" placeholder="Số nhà, tên đường, phường" data-caremil-required>
                    <p class="text-red-500 text-xs mt-1 italic hidden" data-caremil-error="caremil_address">Vui lòng nhập địa chỉ.</p>
                </div>
                <div>
                    <label class="form-label">Số điện thoại</label>
                    <input type="tel" name="caremil_phone" class="form-input" placeholder="Nhập số điện thoại" required data-caremil-required>
                    <p class="text-red-500 text-xs mt-1 italic hidden" data-caremil-error="caremil_phone">Vui lòng nhập số điện thoại.</p>
                </div>

                <div class="space-y-2 mt-4 bg-blue-50 p-4 rounded-xl border border-blue-100">
                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" name="caremil_terms" class="mt-1 w-4 h-4 accent-brand-gold">
                        <span class="text-brand-navy text-xs lg:text-sm font-medium">Tôi đồng ý với <u class="hover:text-brand-blue">Thể lệ và Điều khoản chương trình</u></span>
                    </label>
                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" name="caremil_privacy" class="mt-1 w-4 h-4 accent-brand-gold">
                        <span class="text-brand-navy text-xs lg:text-sm font-medium">Tôi đã đọc và đồng ý với <u class="hover:text-brand-blue">mẫu chấp thuận</u> của DawnBridge</u></span>
                    </label>
                </div>

                <div class="pt-4 space-y-2">
                    <button type="submit" data-caremil-submit class="w-full py-3.5 rounded-xl gold-gradient-btn text-lg shadow-lg transform hover:scale-[1.02] transition duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> ĐĂNG KÝ NGAY
                    </button>
                    <p class="text-sm font-medium text-center text-emerald-600 hidden" data-caremil-feedback></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function reveal() { var reveals = document.querySelectorAll(".reveal"); for (var i = 0; i < reveals.length; i++) { var windowHeight = window.innerHeight; var elementTop = reveals[i].getBoundingClientRect().top; var elementVisible = 50; if (elementTop < windowHeight - elementVisible) { reveals[i].classList.add("active"); } } }
        window.addEventListener("scroll", reveal); reveal();
        function switchTab(tabName) { const contents = document.querySelectorAll('.tab-content'); contents.forEach(content => content.classList.remove('active')); const buttons = document.querySelectorAll('.tab-btn'); buttons.forEach(btn => btn.classList.remove('active')); document.getElementById('content-' + tabName).classList.add('active'); document.getElementById('tab-' + tabName).classList.add('active'); }
        
        // Expert Modal Logic
        function openExpertModal() { document.getElementById('expert-modal').classList.remove('hidden'); setTimeout(() => document.getElementById('expert-modal').classList.add('show'), 10); }
        function closeExpertModal() { document.getElementById('expert-modal').classList.remove('show'); setTimeout(() => document.getElementById('expert-modal').classList.add('hidden'), 300); }
        function confirmExpert() { closeExpertModal(); document.getElementById('ingredients-lock').classList.add('hidden'); document.getElementById('ingredients-container').classList.remove('hidden'); document.getElementById('ingredients-container').scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        
        // Trial Form Modal Logic
        function openTrialModal() { document.getElementById('trial-modal').classList.remove('hidden'); setTimeout(() => document.getElementById('trial-modal').classList.add('flex'), 10); }
        function closeTrialModal() { document.getElementById('trial-modal').classList.remove('flex'); setTimeout(() => document.getElementById('trial-modal').classList.add('hidden'), 10); }

        function toggleMobileMenu() { const menu = document.getElementById('mobile-menu'); const icon = document.getElementById('menu-icon'); menu.classList.toggle('open'); if (menu.classList.contains('open')) { icon.classList.remove('fa-bars'); icon.classList.add('fa-times'); } else { icon.classList.remove('fa-times'); icon.classList.add('fa-bars'); } }
    </script>
</body>
<?php wp_footer(); ?>
</html>