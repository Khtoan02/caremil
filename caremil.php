<?php
/**
 * Template Name: CareMIL Product Page
 * Template Post Type: page
 * Description: Trang chủ sản phẩm CareMIL với thiết kế đầy đủ
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
    <title>CareMIL - Dinh Dưỡng Mát Lành Cho Bé & Cả Nhà</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;800&family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            navy: '#1a4f8a',   /* Xanh Navy dịu */
                            blue: '#4cc9f0',   /* Xanh Cyan tươi sáng */
                            gold: '#ffd166',   /* Vàng ấm áp */
                            soft: '#e0fbfc',   /* Xanh nhạt nền nã */
                            cream: '#fffdf2',  /* Màu kem sữa */
                            pink: '#ef476f',   /* Hồng yêu thương */
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
                        'card': '0 8px 0px 0px rgba(0,0,0,0.05)',
                    }
                }
            }
        }
    </script>
    <style>
        /* Tinh chỉnh hiển thị bảng trên mobile */
        .nutrition-table th {
            font-family: 'Baloo 2', cursive;
            color: #1a4f8a;
            white-space: nowrap;
        }
        .nutrition-table td {
            font-family: 'Quicksand', sans-serif;
            font-weight: 600;
        }
        
        /* Cuộn mượt cho bảng */
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }
        
        /* Hiệu ứng Menu Mobile */
        #mobile-menu {
            transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
        #mobile-menu.open {
            max-height: 400px;
            opacity: 1;
        }

        /* Modal Styles */
        #expert-modal {
            display: none;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
        }
        #expert-modal.show {
            display: flex;
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Ẩn thanh cuộn cho tab ngang trên mobile */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        /* Tab Button Styles */
        .tab-btn {
            transition: all 0.3s ease;
        }
        .tab-btn.active {
            background-color: #4cc9f0;
            color: white;
            box-shadow: 0 4px 10px rgba(76, 201, 240, 0.3);
        }
        .tab-content { display: none; animation: fadeIn 0.5s ease; }
        .tab-content.active { display: block; }

        /* Sticker Effect */
        .sticker {
            background: white;
            border: 3px solid #f0f4f8;
            border-radius: 24px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .sticker:hover {
            transform: translateY(-5px);
            border-color: #ffd166;
            box-shadow: 0 10px 20px rgba(255, 209, 102, 0.2);
        }
    </style>
</head>
<body class="bg-brand-cream text-gray-600 font-sans selection:bg-brand-gold selection:text-white">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-300 py-2 md:py-3" id="navbar">
        <div class="container mx-auto px-4">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl md:rounded-full shadow-soft px-4 py-2 md:px-6 md:py-3 border border-white relative z-50">
                <div class="flex justify-between items-center">
                    <a href="#" class="text-xl md:text-3xl font-display font-black text-brand-navy tracking-tight flex items-center gap-2">
                        <i class="fas fa-leaf text-brand-gold text-lg md:text-2xl"></i>
                        Care<span class="text-brand-blue">MIL</span>
                    </a>
                    
                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center space-x-8 font-bold text-gray-500 text-lg">
                        <a href="#loi-ich" class="hover:text-brand-blue transition">Lợi Ích</a>
                        <a href="#khoa-hoc" class="hover:text-brand-blue transition">Khoa Học</a>
                        <a href="#bang-thanh-phan" class="hover:text-brand-blue transition">Thành Phần</a>
                        <a href="#huong-dan" class="hover:text-brand-blue transition">Pha Chế</a>
                        <button onclick="document.getElementById('order').scrollIntoView()" class="bg-brand-gold text-white font-bold py-2 px-6 rounded-full shadow-lg hover:bg-yellow-400 hover:scale-105 transition transform flex items-center gap-2">
                            <i class="fas fa-shopping-cart"></i> Mua Ngay
                        </button>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <button onclick="toggleMobileMenu()" class="md:hidden text-brand-navy text-xl bg-blue-50 p-2 rounded-lg w-10 h-10 flex items-center justify-center focus:outline-none shadow-sm transition active:bg-blue-100">
                        <i class="fas fa-bars" id="menu-icon"></i>
                    </button>
                </div>

                <!-- Mobile Menu Dropdown -->
                <div id="mobile-menu" class="md:hidden mt-2 border-t border-gray-100">
                    <div class="flex flex-col space-y-2 py-4 font-bold text-gray-600 text-center">
                        <a href="#loi-ich" onclick="toggleMobileMenu()" class="py-3 hover:text-brand-blue hover:bg-blue-50 rounded-xl transition">Lợi Ích</a>
                        <a href="#khoa-hoc" onclick="toggleMobileMenu()" class="py-3 hover:text-brand-blue hover:bg-blue-50 rounded-xl transition">Khoa Học</a>
                        <a href="#bang-thanh-phan" onclick="toggleMobileMenu()" class="py-3 hover:text-brand-blue hover:bg-blue-50 rounded-xl transition">Thành Phần</a>
                        <a href="#huong-dan" onclick="toggleMobileMenu()" class="py-3 hover:text-brand-blue hover:bg-blue-50 rounded-xl transition">Cách Pha</a>
                        <button onclick="toggleMobileMenu(); document.getElementById('order').scrollIntoView()" class="bg-brand-gold text-white py-3 mt-2 rounded-xl shadow-md w-full">
                            Đặt Mua Ngay
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-32 pb-16 lg:pt-48 lg:pb-32 overflow-hidden">
        <!-- Background Gradients -->
        <div class="absolute inset-0 bg-gradient-to-b from-blue-50 via-white to-brand-cream -z-20"></div>
        
        <!-- Decorative Floating Elements -->
        <div class="absolute top-20 -left-10 text-brand-blue opacity-20 text-6xl lg:text-8xl animate-bounce-slow"><i class="fas fa-cloud"></i></div>
        <div class="absolute top-40 right-0 text-brand-gold opacity-30 text-5xl lg:text-7xl animate-float"><i class="fas fa-sun"></i></div>
        <div class="absolute bottom-20 left-10 text-green-300 opacity-20 text-4xl animate-wiggle"><i class="fas fa-leaf"></i></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col-reverse lg:flex-row items-center gap-10 lg:gap-20">
                
                <!-- Text Content (Left on Desktop, Bottom on Mobile) -->
                <div class="lg:w-1/2 text-center lg:text-left reveal active space-y-6">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-brand-blue/30 text-brand-navy font-bold shadow-sm text-sm lg:text-base mx-auto lg:mx-0">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Dinh dưỡng chuẩn Clean Label
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl lg:text-7xl font-display font-black text-brand-navy leading-tight lg:leading-none tracking-tight">
                        Mát Lành Như <br>
                        <span class="text-brand-blue relative inline-block mt-2 lg:mt-0">
                            Dòng Sữa Mẹ
                            <svg class="absolute w-full h-3 -bottom-2 left-0 text-brand-gold" viewBox="0 0 200 9" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.00025 6.99997C33.5003 3.49997 73.5002 -2.00003 198 3.99999" stroke="currentColor" stroke-width="4" stroke-linecap="round"/></svg>
                        </span>
                    </h1>
                    
                    <p class="text-lg md:text-xl text-gray-600 font-medium leading-relaxed max-w-lg mx-auto lg:mx-0">
                        Từ <strong>Đạm Đậu Hà Lan</strong> tinh khiết & <strong>Fibregum™</strong>. Giúp bé tiêu hóa khỏe, bụng êm, không lo dị ứng sữa bò.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-4">
                        <a href="#order" class="bg-brand-pink text-white text-lg font-bold py-4 px-10 rounded-full shadow-lg shadow-pink-200 hover:bg-pink-600 hover:shadow-pink-300 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3 w-full sm:w-auto group">
                            <i class="fas fa-heart animate-pulse group-hover:scale-110 transition-transform"></i> 
                            Ưu Đãi Cho Mẹ & Bé
                        </a>
                        <a href="#thanh-phan" class="bg-white text-brand-navy border-2 border-brand-soft text-lg font-bold py-4 px-8 rounded-full hover:border-brand-blue hover:text-brand-blue transition-colors w-full sm:w-auto">
                            Tìm Hiểu Thêm
                        </a>
                    </div>

                    <div class="pt-4 flex flex-wrap justify-center lg:justify-start gap-3 text-sm font-bold text-brand-navy/80">
                        <div class="flex items-center gap-2 bg-white/80 px-4 py-2 rounded-xl border border-blue-50 shadow-sm">
                            <i class="fas fa-shield-alt text-brand-blue"></i> Không Dị Ứng
                        </div>
                        <div class="flex items-center gap-2 bg-white/80 px-4 py-2 rounded-xl border border-blue-50 shadow-sm">
                            <i class="fas fa-seedling text-green-500"></i> 100% Thực Vật
                        </div>
                    </div>
                </div>
                
                <!-- Product Image (Right on Desktop, Top on Mobile) -->
                <div class="lg:w-1/2 relative reveal delay-200 text-center w-full max-w-md lg:max-w-full mx-auto">
                    <!-- Glow Effect Background -->
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[350px] lg:w-[600px] h-[350px] lg:h-[600px] bg-gradient-to-tr from-brand-soft to-white rounded-full filter blur-3xl -z-10 opacity-80"></div>
                    
                    <!-- Decorative Elements attached to image -->
                    <div class="hidden lg:block absolute -top-10 right-0 bg-white p-4 rounded-2xl shadow-lg z-20 animate-float border-2 border-brand-soft">
                        <span class="text-3xl block text-center mb-1">🥛</span> 
                        <span class="font-display font-bold text-brand-navy text-sm">Giàu Protein</span>
                    </div>
                    <div class="hidden lg:block absolute bottom-10 -left-10 bg-white p-4 rounded-2xl shadow-lg z-20 animate-float" style="animation-delay: 2s;">
                        <span class="text-3xl block text-center mb-1">😋</span> 
                        <span class="font-display font-bold text-brand-navy text-sm">Vị Vani Thơm Ngon</span>
                    </div>
                    
                    <!-- Correct Image Source Updated Here -->
                    <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-scaled.png" 
                         onerror="this.src='https://placehold.co/600x600/e0fbfc/1a4f8a?text=CareMIL+Product&font=baloo2'"
                         alt="Hộp sữa CareMIL Plant-Based" 
                         class="w-full h-auto object-contain drop-shadow-2xl transform hover:scale-105 transition duration-500">
                </div>
            </div>
        </div>
        
        <!-- Bottom Wave Decoration -->
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
            <svg class="relative block w-[calc(100%+1.3px)] h-[60px] lg:h-[120px]" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="#ffffff"></path>
            </svg>
        </div>
    </header>

    <!-- NEW SECTION: Leaky Gut Mechanism Simulation -->
    <section id="co-che-ro-ri" class="py-16 lg:py-24 bg-white relative">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="mx-auto bg-slate-50 rounded-[40px] overflow-hidden shadow-2xl border border-slate-200">
                
                <!-- Header & Controls -->
                <div class="p-6 lg:p-10 text-center bg-white border-b border-slate-100 relative z-10">
                    <h2 class="text-2xl md:text-4xl font-display font-black text-brand-navy mb-3">Mô Phỏng Cơ Chế Rò Rỉ Ruột</h2>
                    <p class="text-gray-600 mb-8 max-w-2xl mx-auto text-sm md:text-base">
                        Hãy chọn chế độ bên dưới để quan sát sự khác biệt giữa đường ruột khỏe mạnh và khi bị tổn thương bởi Gluten/Casein.
                    </p>
                    
                    <div class="inline-flex bg-slate-100 p-1.5 rounded-full shadow-inner gap-2">
                        <button onclick="setGutMode('normal')" id="btn-normal" class="flex items-center gap-2 px-6 py-3 rounded-full font-bold text-sm lg:text-base transition-all duration-300 bg-white text-brand-blue shadow-md">
                            <i class="fas fa-check-circle"></i> Ruột Khỏe Mạnh
                        </button>
                        <button onclick="setGutMode('leaky')" id="btn-leaky" class="flex items-center gap-2 px-6 py-3 rounded-full font-bold text-sm lg:text-base transition-all duration-300 text-gray-500 hover:text-gray-800">
                            <i class="fas fa-exclamation-circle"></i> Rò Rỉ Ruột
                        </button>
                    </div>
                </div>

                <!-- Simulation Visualizer -->
                <div class="relative h-[500px] md:h-[600px] select-none bg-white w-full overflow-hidden" id="gut-visualizer">
                    
                    <!-- LAYER 1: LUMEN (Lòng ruột) -->
                    <!-- Giữ nguyên z-index -->
                    <div class="absolute top-0 w-full h-[60%] bg-blue-50/30 z-10 overflow-hidden border-b border-blue-100/50">
                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1.5 rounded-full text-xs font-bold text-brand-navy border border-blue-100 shadow-sm z-20">
                            LÒNG RUỘT (LUMEN)
                        </div>
                        
                        <!-- Legend -->
                        <div class="absolute top-4 right-4 flex flex-col gap-2 text-[10px] md:text-xs font-bold text-slate-500 bg-white/90 p-3 rounded-xl border border-slate-100 z-20 shadow-sm">
                            <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-yellow-400 animate-wiggle"></div> Gluten</div>
                            <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-green-500 animate-wiggle"></div> Độc tố</div>
                            <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-purple-500 animate-wiggle"></div> Vi khuẩn</div>
                            <div class="flex items-center gap-2 border-t pt-1 mt-1"><div class="text-blue-200 text-sm"><i class="fas fa-shield-virus"></i></div> Kháng thể</div>
                        </div>

                        <!-- Particles Container -->
                        <div id="lumen-particles"></div>
                    </div>

                    <!-- LAYER 2: MUCOSAL BARRIER (Niêm mạc) -->
                    <!-- Kéo dài chiều cao (h-40%) để chân cắm sâu xuống -->
                    <!-- Thêm mask-image để làm mờ chân tế bào -->
                    <div class="absolute top-[30%] w-full h-[40%] z-0 flex items-end justify-center px-2" style="-webkit-mask-image: linear-gradient(to bottom, black 80%, transparent 100%); mask-image: linear-gradient(to bottom, black 80%, transparent 100%);">
                        <!-- Cells Container -->
                        <div id="mucosal-cells" class="flex w-full h-full items-end justify-center gap-[1px] transition-all duration-1000"> 
                            <!-- Cells will be generated by JS -->
                        </div>
                    </div>

                    <!-- LAYER 3: BLOODSTREAM (Mạch máu) -->
                    <!-- Tăng chiều cao lên 45% để che phủ tốt hơn -->
                    <div class="absolute bottom-0 w-full h-[45%] bg-gradient-to-r from-red-800 via-red-600 to-red-800 z-20 overflow-hidden shadow-[0_-10px_30px_rgba(220,38,38,0.2)]">
                        
                        <!-- Soft Transition Gradient Overlay (New!) -->
                        <!-- Lớp này tạo hiệu ứng mờ ảo giữa niêm mạc và máu, che đi đường cắt cứng -->
                        <div class="absolute top-0 left-0 w-full h-16 bg-gradient-to-b from-white/50 via-red-100/30 to-transparent z-30 pointer-events-none blur-sm"></div>
                        
                        <div class="absolute top-4 left-4 bg-red-900/40 backdrop-blur text-white/90 text-xs font-bold uppercase tracking-widest border border-white/20 px-3 py-1 rounded-full z-20">
                            Dòng Máu (Bloodstream)
                        </div>
                        
                        <!-- Background Blood Cells Flowing -->
                        <div id="blood-cells-bg" class="w-full h-full relative z-0"></div>

                        <!-- Immune Reaction Zone -->
                        <div id="immune-zone" class="absolute inset-0 z-10 opacity-0 transition-opacity duration-500 pointer-events-none"></div>

                        <!-- Alert Box -->
                        <div id="immune-alert" class="absolute bottom-4 right-4 flex flex-col items-end gap-2 z-20 opacity-0 transition-all duration-500 transform translate-y-4">
                            <div class="flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg text-xs md:text-sm shadow-lg font-bold animate-pulse border border-red-400">
                                <i class="fas fa-heartbeat"></i>
                                <span>HỆ MIỄN DỊCH ĐANG CHIẾN ĐẤU!</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Labels -->
                <div class="py-4 bg-slate-50 border-t border-slate-200 text-center">
                    <p class="text-xs text-gray-500 italic">Mô hình đơn giản hóa để minh họa cơ chế sinh học.</p>
                </div>
            </div>
        </div>

        <!-- Styles for Animation -->
        <style>
            /* Smoother, Seaweed-like Sway */
            @keyframes organicSway {
                0% { transform: rotate(0deg) skewX(0deg); }
                25% { transform: rotate(2deg) skewX(1deg); } 
                50% { transform: rotate(0deg) skewX(0deg); }
                75% { transform: rotate(-2deg) skewX(-1deg); } 
                100% { transform: rotate(0deg) skewX(0deg); }
            }

            /* ... (Other animations keep same) ... */
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
                10% { opacity: 0.5; }
                90% { opacity: 0.5; }
                100% { transform: translateX(120vw) translateY(20px) rotate(180deg); opacity: 0; }
            }

            @keyframes battleFlow {
                0% { left: -20%; }
                100% { left: 120%; }
            }
            
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

            /* --- CLASSES --- */
            
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

            .cell-container {
                transform-origin: bottom center;
                /* Animation is now applied to cell-body for better control, container is static anchor */
            }

            .cell-body {
                width: 100%; 
                height: 120%; /* Height > 100% to extend below visibility */
                border: 2px solid;
                border-bottom: none; /* Hide bottom border */
                position: relative;
                transition: background-color 1s ease, border-color 1s ease;
                background-image: linear-gradient(to bottom, rgba(255,255,255,0.6), rgba(255,255,255,0.1));
                
                /* Organic Shape Morphing */
                border-top-left-radius: 30px;
                border-top-right-radius: 30px;
                transform-origin: bottom center;
                animation: organicSway 6s infinite ease-in-out; 
            }
            
            /* Normal Mode Colors */
            .mode-normal .cell-body { background-color: #fce7f3; border-color: #fbcfe8; } 
            .mode-normal .cell-nucleus { background-color: #db2777; }
            .mode-normal .tight-junction { opacity: 1; height: 40%; top: 20%; background-color: #3b82f6; width: 3px; box-shadow: 0 0 5px #60a5fa; }
            
            /* Leaky Mode Colors */
            .mode-leaky #mucosal-cells { gap: 20px; } 
            .mode-leaky .cell-body { background-color: #fee2e2; border-color: #fca5a5; animation-duration: 3s; } /* Faster agitation */
            .mode-leaky .cell-nucleus { background-color: #b91c1c; }
            .mode-leaky .tight-junction { opacity: 0; height: 0; }

            .battle-pair { 
                position: absolute; 
                animation-name: battleFlow;
                animation-timing-function: linear;
                animation-iteration-count: infinite;
                display: flex; 
                align-items: center; 
                justify-content: center;
                width: 100px;
                height: 100px;
            }
            
            .toxin-target { animation: neutralizeToxin linear infinite; z-index: 10; }
            .antibody-actor { position: absolute; animation: antibodyBind linear infinite; z-index: 20; width: 30px; height: 30px; }
            .antibody-svg { width: 100%; height: 100%; fill: none; stroke: #60a5fa; stroke-width: 4; stroke-linecap: round; stroke-linejoin: round; filter: drop-shadow(0 0 2px rgba(37, 99, 235, 0.5)); }
            .glow-effect { position: absolute; width: 40px; height: 40px; border-radius: 50%; animation: softGlow linear infinite; pointer-events: none; z-index: 5; }

        </style>

        <script>
            // ... (Javascript logic keeps same structure, re-rendering for context) ...
            const gutState = { mode: 'normal' };
            const particleTypes = [
                { type: 'gluten', color: 'text-yellow-600 bg-yellow-100 border-yellow-300', icon: '<i class="fas fa-bread-slice"></i>' },
                { type: 'toxin', color: 'text-green-700 bg-green-100 border-green-300', icon: '<i class="fas fa-biohazard"></i>' },
                { type: 'microbe', color: 'text-purple-700 bg-purple-100 border-purple-300', icon: '<i class="fas fa-bug"></i>' }
            ];

            function initGutSimulation() {
                // 1. Lumen Particles
                const lumen = document.getElementById('lumen-particles');
                lumen.innerHTML = '';
                const gapPositions = [12, 25, 37, 50, 62, 75, 87];
                
                for(let i=0; i<45; i++) { 
                    const p = document.createElement('div');
                    const pType = particleTypes[Math.floor(Math.random() * particleTypes.length)];
                    p.className = `particle absolute w-4 h-4 lg:w-6 lg:h-6 rounded-full flex items-center justify-center border ${pType.color} text-[10px] lg:text-xs shadow-sm z-10`;
                    p.innerHTML = pType.icon;
                    
                    const isLeaker = i % 2 !== 0; 
                    
                    if (isLeaker) {
                        const gap = gapPositions[Math.floor(Math.random() * gapPositions.length)];
                        p.style.left = (gap + (Math.random() * 6 - 3)) + '%';
                        p.style.top = (Math.random() * 30 + 40) + '%';
                        p.classList.add('will-leak');
                    } else {
                        p.style.left = Math.random() * 95 + '%';
                        p.style.top = Math.random() * 40 + '%';
                    }
                    
                    p.style.animationDuration = (3 + Math.random() * 4) + 's'; 
                    p.style.animationDelay = -Math.random() * 5 + 's';
                    
                    lumen.appendChild(p);
                }

                // 2. Mucosal Cells
                const container = document.getElementById('mucosal-cells');
                container.innerHTML = '';
                for(let i=0; i<8; i++) {
                    const cellWrapper = document.createElement('div');
                    cellWrapper.className = "cell-container relative h-full flex-1 flex flex-col justify-end group min-w-[30px] max-w-[100px]";
                    
                    const swayDuration = 5 + Math.random() * 3; // Slower, more organic sway
                    const swayDelay = -Math.random() * 5;
                    
                    cellWrapper.innerHTML = `
                        ${i < 7 ? '<div class="tight-junction absolute -right-[2px] z-30 rounded-full transition-all duration-700"></div>' : ''}
                        <div class="cell-body rounded-t-3xl shadow-inner flex items-center justify-center" style="animation-duration: ${swayDuration}s; animation-delay: ${swayDelay}s;">
                            <div class="absolute -top-2 w-full flex justify-center gap-[2px] overflow-hidden px-1 opacity-60">
                                <div class="w-1 h-2 bg-pink-400 rounded-full"></div>
                                <div class="w-1 h-3 bg-pink-400 rounded-full"></div>
                                <div class="w-1 h-2 bg-pink-400 rounded-full"></div>
                                <div class="w-1 h-3 bg-pink-400 rounded-full"></div>
                            </div>
                            <div class="cell-nucleus w-3 h-3 lg:w-5 lg:h-5 rounded-full shadow-sm border border-white/30 transition-colors duration-1000"></div>
                        </div>
                    `;
                    container.appendChild(cellWrapper);
                }

                // 3. Blood Cells
                const bloodBg = document.getElementById('blood-cells-bg');
                bloodBg.innerHTML = '';
                for(let i=0; i<18; i++) {
                    const rbc = document.createElement('div');
                    rbc.className = "blood-cell flex items-center justify-center opacity-80";
                    const size = 15 + Math.random()*25;
                    rbc.style.width = size + 'px';
                    rbc.style.height = size + 'px';
                    rbc.style.top = Math.random() * 85 + '%';
                    rbc.style.animationDuration = (8 + Math.random() * 12) + 's';
                    rbc.style.animationDelay = -Math.random() * 20 + 's';
                    rbc.innerHTML = '<div class="w-1/2 h-1/2 bg-red-900/20 rounded-full shadow-inner blur-[1px]"></div>';
                    bloodBg.appendChild(rbc);
                }

                // 4. Immune Response
                const immuneZone = document.getElementById('immune-zone');
                immuneZone.innerHTML = '';
                for(let i=0; i<12; i++) { 
                    const pair = document.createElement('div');
                    const enemyType = particleTypes[Math.floor(Math.random() * particleTypes.length)];
                    pair.className = "battle-pair";
                    pair.style.top = (5 + Math.random() * 75) + '%';
                    const duration = 5 + Math.random() * 6; 
                    pair.style.animationDuration = duration + 's';
                    pair.style.animationDelay = -Math.random() * 20 + 's';
                    pair.innerHTML = `
                        <div class="toxin-target w-8 h-8 rounded-full flex items-center justify-center border-2 ${enemyType.color} bg-white shadow-md z-10" style="animation-duration: ${duration}s">
                            ${enemyType.icon}
                        </div>
                        <div class="antibody-actor" style="animation-duration: ${duration}s">
                            <svg class="antibody-svg" viewBox="0 0 24 24"><path d="M12 22V12 M12 12L5 5 M12 12L19 5" /></svg>
                        </div>
                        <div class="glow-effect" style="animation-duration: ${duration}s"></div>
                    `;
                    immuneZone.appendChild(pair);
                }
            }

            function setGutMode(mode) {
                gutState.mode = mode;
                const visualizer = document.getElementById('gut-visualizer');
                const btnNormal = document.getElementById('btn-normal');
                const btnLeaky = document.getElementById('btn-leaky');
                const immuneZone = document.getElementById('immune-zone');
                const immuneAlert = document.getElementById('immune-alert');

                if (mode === 'normal') {
                    visualizer.classList.remove('mode-leaky');
                    visualizer.classList.add('mode-normal');
                    
                    btnNormal.className = "flex items-center gap-2 px-6 py-3 rounded-full font-bold transition-all bg-white text-brand-blue shadow-md transform scale-105";
                    btnLeaky.className = "flex items-center gap-2 px-6 py-3 rounded-full font-bold transition-all text-gray-500 hover:text-gray-800 hover:bg-slate-50";
                    
                    immuneZone.style.opacity = '0'; 
                    immuneAlert.style.opacity = '0';
                    immuneAlert.style.transform = 'translateY(1rem)';

                    document.querySelectorAll('.particle').forEach(p => {
                        p.classList.remove('leaking');
                        p.style.transitionDelay = '0s'; 
                    });

                } else {
                    visualizer.classList.remove('mode-normal');
                    visualizer.classList.add('mode-leaky');

                    btnNormal.className = "flex items-center gap-2 px-6 py-3 rounded-full font-bold transition-all text-gray-500 hover:text-gray-800 hover:bg-slate-50";
                    btnLeaky.className = "flex items-center gap-2 px-6 py-3 rounded-full font-bold transition-all bg-white text-red-500 shadow-md transform scale-105";

                    immuneZone.style.opacity = '1'; 
                    immuneAlert.style.opacity = '1';
                    immuneAlert.style.transform = 'translateY(0)';

                    document.querySelectorAll('.particle.will-leak').forEach(p => {
                        setTimeout(() => {
                            if(gutState.mode === 'leaky') {
                                p.classList.add('leaking');
                            }
                        }, Math.random() * 5000);
                    });
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                initGutSimulation();
                setGutMode('normal');
            });
        </script>
    </section>

    <!-- 8 NOs Section -->
    <section id="loi-ich" class="py-12 lg:py-20 bg-white relative">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-10 lg:mb-16 reveal">
                <div class="inline-block bg-brand-gold/20 text-brand-navy px-3 py-1 rounded-full text-xs lg:text-sm font-bold mb-3 uppercase tracking-wider">
                    An Toàn Tuyệt Đối
                </div>
                <h2 class="text-2xl md:text-3xl lg:text-5xl font-display font-black text-brand-navy mb-3 lg:mb-4">Cam Kết <span class="text-brand-pink">"8 KHÔNG"</span></h2>
                <p class="text-sm lg:text-lg text-gray-500">Mẹ yên tâm, bé khỏe mạnh. Loại bỏ mọi yếu tố gây hại.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6">
                <!-- Features List -->
                <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-100">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-blue-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">🍞</div>
                    <h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">Gluten Free</h3>
                    <p class="text-xs lg:text-sm text-gray-400 font-medium">Không Gluten</p>
                </div>
                <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-150">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-pink-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">💧</div>
                    <h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">Lactose Free</h3>
                    <p class="text-xs lg:text-sm text-gray-400 font-medium">Không Lactose</p>
                </div>
                <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-200">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-green-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">🌱</div>
                    <h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">Soy Free</h3>
                    <p class="text-xs lg:text-sm text-gray-400 font-medium">Không Đậu Nành</p>
                </div>
                <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-250">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-yellow-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">🍬</div>
                    <h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">No Sugar</h3>
                    <p class="text-xs lg:text-sm text-gray-400 font-medium">Không Đường Thêm</p>
                </div>
                <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-300">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-orange-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">🧀</div>
                    <h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">Dairy Free</h3>
                    <p class="text-xs lg:text-sm text-gray-400 font-medium">Không Sữa Bò</p>
                </div>
                 <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-300">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-purple-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">🧪</div>
                    <h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">No Preservative</h3>
                    <p class="text-xs lg:text-sm text-gray-400 font-medium">Không Bảo Quản</p>
                </div>
                 <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-300">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-red-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">🎨</div>
                    <h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">No Colouring</h3>
                    <p class="text-xs lg:text-sm text-gray-400 font-medium">Không Phẩm Màu</p>
                </div>
                 <div class="sticker p-4 lg:p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-300">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-teal-100 flex items-center justify-center mb-3 lg:mb-4 text-2xl lg:text-3xl">🧬</div>
                    <h3 class="font-display font-bold text-base lg:text-xl text-brand-navy">Non GMO</h3>
                    <p class="text-xs lg:text-sm text-gray-400 font-medium">Không Biến Đổi Gen</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SCIENTIFIC SECTION: Gut-Brain Connection -->
    <section id="khoa-hoc" class="py-16 lg:py-24 bg-gradient-to-b from-blue-50 to-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-blue/5 rounded-full blur-3xl -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-brand-pink/5 rounded-full blur-3xl -ml-20 -mb-20"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center max-w-4xl mx-auto mb-12 lg:mb-16 reveal">
                <h2 class="text-2xl md:text-3xl lg:text-5xl font-display font-black text-brand-navy mb-6 leading-tight">
                    Khoa Học Về <br>
                    <span class="text-brand-blue">Đường Ruột</span> & <span class="text-brand-pink">Não Bộ</span>
                </h2>
                <p class="text-sm lg:text-lg text-gray-600">
                    Hiểu về mối liên hệ mật thiết để bảo vệ sức khỏe toàn diện cho bé yêu.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center mb-16">
                <!-- Problem Illustration -->
                <div class="bg-white rounded-[40px] p-6 lg:p-10 shadow-soft border border-brand-soft relative reveal">
                    <div class="absolute -top-4 -left-4 bg-brand-pink text-white px-4 py-2 rounded-full font-bold shadow-md">Vấn Đề (The Problem)</div>
                    <div class="space-y-8">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center text-3xl flex-shrink-0">🦠</div>
                            <div>
                                <h4 class="font-bold text-brand-navy text-lg mb-2">Hội Chứng Rò Rỉ Ruột (Leaky Gut)</h4>
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    <strong>Casein</strong> (sữa bò) và <strong>Gluten</strong> (lúa mì) có thể gây viêm, làm hở niêm mạc ruột, khiến độc tố xâm nhập vào máu.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center text-3xl flex-shrink-0">🧠</div>
                            <div>
                                <h4 class="font-bold text-brand-navy text-lg mb-2">Kết Nối Não - Ruột</h4>
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    Tổn thương ruột ảnh hưởng trực tiếp đến hệ thần kinh, có thể dẫn đến <strong>rối loạn hành vi, cảm xúc</strong> ở trẻ.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CareMIL Solution -->
                <div class="space-y-6 reveal delay-200">
                    <h3 class="text-xl lg:text-2xl font-display font-bold text-brand-navy mb-4">Giải Pháp Từ CareMIL</h3>
                    
                    <div class="bg-brand-cream p-5 rounded-2xl border-l-4 border-brand-gold flex gap-4">
                        <i class="fas fa-check-circle text-brand-gold text-2xl mt-1"></i>
                        <div>
                            <strong class="block text-brand-navy text-lg">Loại Bỏ Tác Nhân</strong>
                            <span class="text-sm text-gray-600">Công thức 100% không chứa Gluten & Casein giúp ngăn ngừa viêm nhiễm.</span>
                        </div>
                    </div>

                    <div class="bg-brand-soft p-5 rounded-2xl border-l-4 border-brand-blue flex gap-4">
                        <i class="fas fa-shield-virus text-brand-blue text-2xl mt-1"></i>
                        <div>
                            <strong class="block text-brand-navy text-lg">Chữa Lành Đường Ruột</strong>
                            <span class="text-sm text-gray-600">Bổ sung <strong>2 chủng Probiotics</strong>: <i>L. acidophilus NCFM</i> & <i>B. lactis Bi-07</i>.</span>
                        </div>
                    </div>

                    <div class="bg-pink-50 p-5 rounded-2xl border-l-4 border-brand-pink flex gap-4">
                        <i class="fas fa-brain text-brand-pink text-2xl mt-1"></i>
                        <div>
                            <strong class="block text-brand-navy text-lg">Hỗ Trợ Hành Vi</strong>
                            <span class="text-sm text-gray-600">Đường ruột khỏe mạnh giúp bảo vệ hệ thần kinh và ổn định tâm lý cho trẻ.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expert Team Box -->
            <div class="bg-brand-navy rounded-3xl p-8 lg:p-12 text-white relative overflow-hidden reveal delay-300">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-16 -mt-16"></div>
                
                <div class="flex flex-col lg:flex-row items-center gap-8 relative z-10">
                    <div class="lg:w-1/3 flex justify-center">
                        <div class="w-32 h-32 bg-white/10 rounded-full flex items-center justify-center text-6xl text-brand-gold border-4 border-white/20">
                            <i class="fas fa-user-doctor"></i>
                        </div>
                    </div>
                    <div class="lg:w-2/3 text-center lg:text-left">
                        <h3 class="text-2xl lg:text-3xl font-display font-bold mb-4">Được Nghiên Cứu Bởi Đội Ngũ Chuyên Gia</h3>
                        <p class="text-blue-100 text-sm lg:text-base leading-relaxed mb-6">
                            CareMIL được thiết kế công thức đặc biệt bởi đội ngũ <strong>Bác sĩ, Chuyên gia Dinh dưỡng Lâm sàng & Chuyên viên tư vấn</strong>. Sản phẩm chuyên biệt hỗ trợ quản lý chế độ ăn cho trẻ có nhu cầu đặc biệt (Special Needs).
                        </p>
                        <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                            <span class="px-4 py-2 bg-white/10 rounded-full text-xs font-bold border border-white/20">🩺 Doctors</span>
                            <span class="px-4 py-2 bg-white/10 rounded-full text-xs font-bold border border-white/20">🥗 Clinical Nutritionists</span>
                            <span class="px-4 py-2 bg-white/10 rounded-full text-xs font-bold border border-white/20">🔬 Dietitians</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Ingredients Overview -->
    <section id="thanh-phan" class="py-12 lg:py-24 bg-brand-soft relative overflow-hidden">
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center mb-10 reveal">
                <h2 class="text-2xl md:text-3xl lg:text-5xl font-display font-black text-brand-navy mb-4">Khu Vườn Dinh Dưỡng</h2>
                <p class="text-sm lg:text-lg text-gray-600">Khám phá những dưỡng chất "vàng" được chắt lọc.</p>
            </div>

            <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12">
                <!-- ... existing content ... -->
                <div class="order-1 lg:order-2 lg:w-1/3 relative flex justify-center py-4 lg:py-0 reveal">
                     <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-48 h-48 lg:w-64 lg:h-64 bg-white rounded-full opacity-60 animate-pulse"></div>
                     </div>
                     <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-scaled.png" 
                          onerror="this.src='https://placehold.co/500x700/e0fbfc/1a4f8a?text=Product&font=baloo2'"
                          alt="CareMIL Product" 
                          class="relative z-10 w-40 lg:w-48 xl:w-64 drop-shadow-2xl">
                </div>

                <!-- Benefits List Left -->
                <div class="order-2 lg:order-1 lg:w-1/3 space-y-4 lg:space-y-6 w-full">
                    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl shadow-soft flex items-center gap-4 border-l-4 lg:border-l-8 border-green-400 reveal delay-100">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-100 rounded-full flex-shrink-0 flex items-center justify-center text-xl lg:text-2xl">🥜</div>
                        <div>
                            <h3 class="font-display font-bold text-base lg:text-lg text-brand-navy">Pea Protein Isolate</h3>
                            <p class="text-xs lg:text-sm text-gray-500">Đạm Đậu Hà Lan giúp bé tăng cân, chắc cơ.</p>
                        </div>
                    </div>
                    <!-- Fibregum item removed from here to move to spotlight section below for emphasis -->
                    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl shadow-soft flex items-center gap-4 border-l-4 lg:border-l-8 border-yellow-400 reveal delay-200">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-yellow-100 rounded-full flex-shrink-0 flex items-center justify-center text-xl lg:text-2xl">⚡</div>
                        <div>
                            <h3 class="font-display font-bold text-base lg:text-lg text-brand-navy">Năng Lượng Sạch</h3>
                            <p class="text-xs lg:text-sm text-gray-500">Dầu thực vật cao cấp cung cấp năng lượng bền bỉ.</p>
                        </div>
                    </div>
                </div>

                <!-- Benefits List Right -->
                <div class="order-3 lg:order-3 lg:w-1/3 space-y-4 lg:space-y-6 w-full">
                    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl shadow-soft flex items-center gap-4 border-r-4 lg:border-r-8 border-brand-blue reveal delay-100">
                         <div class="lg:hidden w-10 h-10 bg-blue-100 rounded-full flex-shrink-0 flex items-center justify-center text-xl">🛡️</div>
                         <div class="flex-grow lg:text-right">
                            <h3 class="font-display font-bold text-base lg:text-lg text-brand-navy">23 Vitamin & Khoáng</h3>
                            <p class="text-xs lg:text-sm text-gray-500">Kẽm, Canxi, Vitamin D3 tăng đề kháng.</p>
                        </div>
                        <div class="hidden lg:flex w-12 h-12 bg-blue-100 rounded-full flex-shrink-0 items-center justify-center text-2xl">🛡️</div>
                    </div>
                    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl shadow-soft flex items-center gap-4 border-r-4 lg:border-r-8 border-brand-pink reveal delay-200">
                         <div class="lg:hidden w-10 h-10 bg-pink-100 rounded-full flex-shrink-0 flex items-center justify-center text-xl">🍦</div>
                         <div class="flex-grow lg:text-right">
                            <h3 class="font-display font-bold text-base lg:text-lg text-brand-navy">Hương Vani Tự Nhiên</h3>
                            <p class="text-xs lg:text-sm text-gray-500">Thơm dịu, ngọt nhẹ, bé nào cũng thích.</p>
                        </div>
                        <div class="hidden lg:flex w-12 h-12 bg-pink-100 rounded-full flex-shrink-0 items-center justify-center text-2xl">🍦</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NEW FEATURE: FIBREGUM SPOTLIGHT -->
    <section class="py-16 lg:py-24 bg-white relative overflow-hidden">
        <!-- French vibe decoration -->
        <div class="absolute top-0 right-0 w-1/2 h-full bg-brand-soft/30 -skew-x-12 transform origin-top-right z-0"></div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                <!-- Left: Visual & Badges -->
                <div class="lg:w-1/2 relative reveal">
                    <div class="relative bg-gradient-to-br from-green-50 to-white border-2 border-green-100 rounded-[40px] p-8 lg:p-12 shadow-xl">
                        <!-- Stylized Fibregum Logo Representation -->
                        <div class="text-center mb-8">
                            <h3 class="text-4xl lg:text-6xl font-sans font-thin text-green-600 tracking-tighter mb-2">
                                Fibregum<span class="text-lg align-top">™</span>
                            </h3>
                            <div class="h-1 w-24 bg-brand-gold mx-auto rounded-full"></div>
                        </div>
                        
                        <!-- Illustration (Acacia Tree Concept) -->
                        <div class="flex justify-center mb-8">
                            <div class="w-48 h-48 bg-green-100 rounded-full flex items-center justify-center text-8xl text-green-600 relative overflow-hidden border-4 border-white shadow-inner">
                                <i class="fas fa-tree"></i>
                                <!-- Sun -->
                                <div class="absolute top-4 right-4 text-brand-gold text-4xl animate-spin-slow"><i class="fas fa-sun"></i></div>
                            </div>
                        </div>

                        <!-- Origin Badge -->
                        <div class="absolute -bottom-6 -right-6 bg-white py-3 px-6 rounded-2xl shadow-lg border border-gray-100 flex items-center gap-3 animate-bounce-slow">
                            <span class="text-3xl">🇫🇷</span>
                            <div class="text-left">
                                <p class="text-xs font-bold text-gray-400 uppercase">Trademark of</p>
                                <p class="text-brand-navy font-bold font-display">Nexira, France</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Content -->
                <div class="lg:w-1/2 space-y-6 reveal delay-200">
                    <div class="inline-block bg-green-100 text-green-700 px-4 py-2 rounded-full font-bold text-sm mb-2">
                        <i class="fas fa-star mr-2"></i> Thành Phần Vàng
                    </div>
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-display font-black text-brand-navy leading-tight">
                        Chất Xơ Thế Hệ Mới <br>
                        <span class="text-green-500">Nuôi Dưỡng Đường Ruột</span>
                    </h2>
                    <p class="text-lg text-gray-600 leading-relaxed">
                        <strong>Fibregum™</strong> là chất xơ hòa tan 100% từ thực vật (cây Acacia), được nhập khẩu trực tiếp từ Pháp. Đây là giải pháp tiêu hóa vượt trội dành cho cơ địa nhạy cảm.
                    </p>
                    
                    <ul class="space-y-4 mt-6">
                        <li class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-500 flex-shrink-0 text-xl"><i class="fas fa-leaf"></i></div>
                            <div>
                                <h4 class="font-bold text-brand-navy text-lg">Siêu Prebiotic</h4>
                                <p class="text-sm text-gray-500">Thức ăn yêu thích của lợi khuẩn, giúp cân bằng hệ vi sinh đường ruột.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-brand-blue flex-shrink-0 text-xl"><i class="fas fa-check-double"></i></div>
                            <div>
                                <h4 class="font-bold text-brand-navy text-lg">Dung Nạp Tốt (High Tolerance)</h4>
                                <p class="text-sm text-gray-500">Không gây đầy hơi, chướng bụng như các loại chất xơ thông thường (Inulin/FOS).</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-yellow-50 flex items-center justify-center text-brand-gold flex-shrink-0 text-xl"><i class="fas fa-shield-cat"></i></div>
                            <div>
                                <h4 class="font-bold text-brand-navy text-lg">Bảo Vệ Niêm Mạc</h4>
                                <p class="text-sm text-gray-500">Giúp củng cố hàng rào bảo vệ ruột, ngăn ngừa hội chứng rò rỉ ruột.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed Ingredients Tabs (Gatekeeper) -->
    <section id="bang-thanh-phan" class="py-12 lg:py-20 bg-white relative">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="text-center mb-8 lg:mb-10 reveal">
                <h2 class="text-2xl md:text-3xl lg:text-5xl font-display font-black text-brand-navy mb-4">Chi Tiết Thành Phần</h2>
                <p class="text-sm lg:text-lg text-gray-600 max-w-2xl mx-auto">Công bố minh bạch bảng thành phần dinh dưỡng.</p>
            </div>

            <!-- GATEKEEPER BUTTON -->
            <div id="ingredients-lock" class="text-center py-6 lg:py-10">
                <div class="bg-blue-50 border-2 border-brand-blue/20 rounded-3xl p-6 lg:p-8 max-w-3xl mx-auto shadow-sm">
                    <div class="text-3xl lg:text-4xl mb-4 text-brand-blue"><i class="fas fa-user-md"></i></div>
                    <h3 class="text-xl lg:text-2xl font-display font-bold text-brand-navy mb-3 lg:mb-4">Thông Tin Chuyên Sâu</h3>
                    <p class="text-sm lg:text-base text-gray-600 mb-6">Xác nhận bạn quan tâm đến các thông tin dinh dưỡng chuyên sâu.</p>
                    <button onclick="openExpertModal()" class="bg-brand-blue text-white font-bold py-2 lg:py-3 px-6 lg:px-8 rounded-full shadow-lg hover:bg-blue-400 transition transform hover:scale-105 flex items-center gap-2 mx-auto text-sm lg:text-base">
                        <i class="fas fa-eye"></i> Xem Chi Tiết
                    </button>
                </div>
            </div>

            <!-- HIDDEN CONTENT -->
            <div id="ingredients-container" class="hidden">
                <div class="flex overflow-x-auto hide-scrollbar pb-4 space-x-3 mb-6 lg:mb-10 lg:justify-center px-1 snap-x">
                    <button onclick="switchTab('nutrition')" id="tab-nutrition" class="tab-btn active flex-shrink-0 snap-center px-5 py-2 lg:px-8 lg:py-3 rounded-full bg-gray-100 text-gray-500 font-bold font-display text-sm lg:text-lg whitespace-nowrap">DINH DƯỠNG</button>
                    <button onclick="switchTab('vitamins')" id="tab-vitamins" class="tab-btn flex-shrink-0 snap-center px-5 py-2 lg:px-8 lg:py-3 rounded-full bg-gray-100 text-gray-500 font-bold font-display text-sm lg:text-lg whitespace-nowrap">VITAMINS</button>
                    <button onclick="switchTab('minerals')" id="tab-minerals" class="tab-btn flex-shrink-0 snap-center px-5 py-2 lg:px-8 lg:py-3 rounded-full bg-gray-100 text-gray-500 font-bold font-display text-sm lg:text-lg whitespace-nowrap">KHOÁNG CHẤT</button>
                    <button onclick="switchTab('other')" id="tab-other" class="tab-btn flex-shrink-0 snap-center px-5 py-2 lg:px-8 lg:py-3 rounded-full bg-gray-100 text-gray-500 font-bold font-display text-sm lg:text-lg whitespace-nowrap">THÀNH PHẦN KHÁC</button>
                </div>

                <div class="bg-brand-soft rounded-3xl lg:rounded-[40px] p-4 lg:p-12 shadow-soft reveal delay-200">
                    <!-- Tab 1: Nutrition -->
                    <div id="content-nutrition" class="tab-content active">
                        <h3 class="text-lg lg:text-2xl font-display font-bold text-brand-navy mb-2 text-center">Thông Tin Dinh Dưỡng</h3>
                        <p class="text-center text-gray-500 mb-4 lg:mb-6 font-medium text-xs lg:text-base">Serving Size: 3 scoops (36g) • Servings: 22</p>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse nutrition-table bg-white rounded-xl lg:rounded-2xl overflow-hidden shadow-sm text-xs md:text-sm lg:text-base min-w-[500px]">
                                <thead class="bg-brand-blue text-white"><tr><th class="p-3 lg:p-4">Thành phần</th><th class="p-3 lg:p-4 text-center">Đơn vị</th><th class="p-3 lg:p-4 text-right">Per 100g</th><th class="p-3 lg:p-4 text-right">Per Serving</th></tr></thead>
                                <tbody class="text-gray-600">
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold text-brand-navy">Energy</td><td class="p-3 lg:p-4 text-center">kcal</td><td class="p-3 lg:p-4 text-right">389</td><td class="p-3 lg:p-4 text-right">140</td></tr>
                                    <tr class="border-b hover:bg-blue-50 bg-gray-50/50"><td class="p-3 lg:p-4 font-bold">Fat</td><td class="p-3 lg:p-4 text-center">g</td><td class="p-3 lg:p-4 text-right">11.5</td><td class="p-3 lg:p-4 text-right">4.1</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 pl-6 italic text-xs">- Monounsaturated</td><td class="p-3 lg:p-4 text-center">g</td><td class="p-3 lg:p-4 text-right">0.4</td><td class="p-3 lg:p-4 text-right">0.1</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 pl-6 italic text-xs">- Polyunsaturated</td><td class="p-3 lg:p-4 text-center">g</td><td class="p-3 lg:p-4 text-right">1.3</td><td class="p-3 lg:p-4 text-right">0.5</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 pl-8 text-xs text-gray-500">• ALA</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">420.0</td><td class="p-3 lg:p-4 text-right">151.2</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 pl-8 text-xs text-gray-500">• LA</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">3360.0</td><td class="p-3 lg:p-4 text-right">1209.6</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 pl-6 italic text-xs">- Saturated</td><td class="p-3 lg:p-4 text-center">g</td><td class="p-3 lg:p-4 text-right">9.3</td><td class="p-3 lg:p-4 text-right">3.3</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 pl-6 italic text-xs">- Trans</td><td class="p-3 lg:p-4 text-center">g</td><td class="p-3 lg:p-4 text-right">0</td><td class="p-3 lg:p-4 text-right">0</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Cholesterol</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">0</td><td class="p-3 lg:p-4 text-right">0</td></tr>
                                    <tr class="border-b hover:bg-blue-50 bg-gray-50/50"><td class="p-3 lg:p-4 font-bold text-brand-navy">Protein</td><td class="p-3 lg:p-4 text-center">g</td><td class="p-3 lg:p-4 text-right font-bold">17.8</td><td class="p-3 lg:p-4 text-right font-bold">6.4</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Carbohydrate</td><td class="p-3 lg:p-4 text-center">g</td><td class="p-3 lg:p-4 text-right">46.1</td><td class="p-3 lg:p-4 text-right">16.6</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Dietary Fibre</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">10500.0</td><td class="p-3 lg:p-4 text-right">3780.0</td></tr>
                                    <tr class="hover:bg-blue-50"><td class="p-3 lg:p-4 pl-6 italic text-xs">- FOS</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">1100.0</td><td class="p-3 lg:p-4 text-right">396.0</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 text-center lg:hidden italic">Vuốt ngang để xem hết bảng</p>
                    </div>

                    <!-- Tab 2: Vitamins -->
                    <div id="content-vitamins" class="tab-content">
                        <h3 class="text-lg lg:text-2xl font-display font-bold text-brand-navy mb-4 lg:mb-6 text-center">Hệ Vitamin</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse nutrition-table bg-white rounded-xl lg:rounded-2xl overflow-hidden shadow-sm text-xs md:text-sm lg:text-base min-w-[500px]">
                                <thead class="bg-brand-blue text-white"><tr><th class="p-3 lg:p-4">Thành phần</th><th class="p-3 lg:p-4 text-center">Đơn vị</th><th class="p-3 lg:p-4 text-right">Per 100g</th><th class="p-3 lg:p-4 text-right">Per Serving</th></tr></thead>
                                <tbody class="text-gray-600">
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Vitamin A</td><td class="p-3 lg:p-4 text-center">mcg</td><td class="p-3 lg:p-4 text-right">540.0</td><td class="p-3 lg:p-4 text-right">194.4</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Vitamin D3</td><td class="p-3 lg:p-4 text-center">mcg</td><td class="p-3 lg:p-4 text-right">9.4</td><td class="p-3 lg:p-4 text-right">3.4</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Vitamin E</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">6.3</td><td class="p-3 lg:p-4 text-right">2.3</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Vitamin K1</td><td class="p-3 lg:p-4 text-center">mcg</td><td class="p-3 lg:p-4 text-right">30.0</td><td class="p-3 lg:p-4 text-right">10.8</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Vitamin C</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">67.5</td><td class="p-3 lg:p-4 text-right">24.3</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Vitamin B9 (Folic)</td><td class="p-3 lg:p-4 text-center">mcg</td><td class="p-3 lg:p-4 text-right">165.0</td><td class="p-3 lg:p-4 text-right">59.4</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Vitamin B1</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">0.8</td><td class="p-3 lg:p-4 text-right">0.3</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Vitamin B2</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">1.1</td><td class="p-3 lg:p-4 text-right">0.4</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Vitamin B3</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">6.0</td><td class="p-3 lg:p-4 text-right">2.2</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Vitamin B5</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">4.2</td><td class="p-3 lg:p-4 text-right">1.5</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Vitamin B6</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">7.2</td><td class="p-3 lg:p-4 text-right">2.6</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Vitamin B7</td><td class="p-3 lg:p-4 text-center">mcg</td><td class="p-3 lg:p-4 text-right">16.5</td><td class="p-3 lg:p-4 text-right">5.9</td></tr>
                                    <tr class="hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Vitamin B12</td><td class="p-3 lg:p-4 text-center">mcg</td><td class="p-3 lg:p-4 text-right">2.7</td><td class="p-3 lg:p-4 text-right">1.0</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 text-center lg:hidden italic">Vuốt ngang để xem hết bảng</p>
                    </div>

                    <!-- Tab 3: Minerals -->
                    <div id="content-minerals" class="tab-content">
                        <h3 class="text-lg lg:text-2xl font-display font-bold text-brand-navy mb-4 lg:mb-6 text-center">Khoáng Chất</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse nutrition-table bg-white rounded-xl lg:rounded-2xl overflow-hidden shadow-sm text-xs md:text-sm lg:text-base min-w-[500px]">
                                <thead class="bg-brand-blue text-white"><tr><th class="p-3 lg:p-4">Thành phần</th><th class="p-3 lg:p-4 text-center">Đơn vị</th><th class="p-3 lg:p-4 text-right">Per 100g</th><th class="p-3 lg:p-4 text-right">Per Serving</th></tr></thead>
                                <tbody class="text-gray-600">
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Sodium</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">76</td><td class="p-3 lg:p-4 text-right">27</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Potassium</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">370.0</td><td class="p-3 lg:p-4 text-right">133.2</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Chloride</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">215</td><td class="p-3 lg:p-4 text-right">77</td></tr>
                                    <tr class="border-b hover:bg-blue-50 bg-gray-50/50"><td class="p-3 lg:p-4 font-bold text-brand-navy">Calcium</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right font-bold">535.7</td><td class="p-3 lg:p-4 text-right font-bold">192.9</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Phosphorus</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">263.0</td><td class="p-3 lg:p-4 text-right">94.7</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Magnesium</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">88.9</td><td class="p-3 lg:p-4 text-right">32.0</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Iron</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">6.3</td><td class="p-3 lg:p-4 text-right">2.3</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Zinc</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">8.1</td><td class="p-3 lg:p-4 text-right">2.9</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Iodine</td><td class="p-3 lg:p-4 text-center">mcg</td><td class="p-3 lg:p-4 text-right">80.4</td><td class="p-3 lg:p-4 text-right">28.9</td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">Selenium</td><td class="p-3 lg:p-4 text-center">mcg</td><td class="p-3 lg:p-4 text-right">12.9</td><td class="p-3 lg:p-4 text-right">4.6</td></tr>
                                    <tr class="hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold text-brand-navy">Choline</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">150.0</td><td class="p-3 lg:p-4 text-right">54.0</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 text-center lg:hidden italic">Vuốt ngang để xem hết bảng</p>
                    </div>

                    <!-- Tab 4: Other -->
                    <div id="content-other" class="tab-content">
                        <h3 class="text-lg lg:text-2xl font-display font-bold text-brand-navy mb-4 lg:mb-6 text-center">Thành Phần Khác</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse nutrition-table bg-white rounded-xl lg:rounded-2xl overflow-hidden shadow-sm text-xs md:text-sm lg:text-base min-w-[500px]">
                                <thead class="bg-brand-blue text-white"><tr><th class="p-3 lg:p-4">Thành phần</th><th class="p-3 lg:p-4 text-center">Đơn vị</th><th class="p-3 lg:p-4 text-right">Per 100g</th><th class="p-3 lg:p-4 text-right">Per Serving</th></tr></thead>
                                <tbody class="text-gray-600">
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold text-brand-navy">Probiotic <br><span class="text-xs font-normal text-gray-500 italic">(L. acidophilus NCFM; B. lactis Bi-07)</span></td><td class="p-3 lg:p-4 text-center">cfu</td><td class="p-3 lg:p-4 text-right">1 x 10<sup>9</sup></td><td class="p-3 lg:p-4 text-right">360 x 10<sup>6</sup></td></tr>
                                    <tr class="border-b hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">L-lysine</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">67.0</td><td class="p-3 lg:p-4 text-right">24.1</td></tr>
                                    <tr class="hover:bg-blue-50"><td class="p-3 lg:p-4 font-bold">L-glutamine</td><td class="p-3 lg:p-4 text-center">mg</td><td class="p-3 lg:p-4 text-right">5000</td><td class="p-3 lg:p-4 text-right">1800</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 text-center lg:hidden italic">Vuốt ngang để xem hết bảng</p>
                        <div class="mt-4 lg:mt-8 pt-4 border-t border-gray-100 text-center">
                            <p class="text-xs lg:text-sm text-gray-500 italic mb-2">Thành phần khác: Pea Protein Isolate, Vegetable Oil, Fibregum™, Vanilla Bean Powder.</p>
                            <p class="text-[10px] lg:text-xs text-gray-400">Sản phẩm không chứa thành phần biến đổi gen, không chất bảo quản.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Preparation Steps -->
    <section id="huong-dan" class="py-12 lg:py-24 bg-white relative overflow-hidden">
        <div class="container mx-auto px-6 text-center relative z-10">
            <h2 class="text-2xl md:text-3xl lg:text-5xl font-display font-black text-brand-navy mb-3 lg:mb-4 reveal">Pha Sữa Đúng Chuẩn</h2>
            <p class="text-sm lg:text-lg text-gray-500 mb-8 lg:mb-16 max-w-2xl mx-auto reveal">4 bước đơn giản để có ly sữa thơm ngon.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                <!-- Step 1 -->
                <div class="bg-brand-cream rounded-[20px] lg:rounded-[30px] p-6 lg:p-8 border-4 border-white shadow-soft group reveal delay-100">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 mx-auto bg-blue-100 rounded-full flex items-center justify-center text-2xl lg:text-3xl text-brand-blue mb-4 lg:mb-6">
                        <i class="fas fa-hands-wash"></i>
                    </div>
                    <div class="inline-block bg-brand-blue text-white text-xs lg:text-sm font-bold px-3 py-1 rounded-full mb-3">Bước 1</div>
                    <h3 class="text-lg lg:text-xl font-display font-bold text-brand-navy mb-2">Vệ Sinh</h3>
                    <p class="text-gray-500 text-sm">Rửa sạch tay và tiệt trùng dụng cụ (ly, thìa).</p>
                </div>
                <!-- Step 2 -->
                <div class="bg-brand-cream rounded-[20px] lg:rounded-[30px] p-6 lg:p-8 border-4 border-white shadow-soft group reveal delay-200">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 mx-auto bg-pink-100 rounded-full flex items-center justify-center text-2xl lg:text-3xl text-brand-pink mb-4 lg:mb-6">
                        <i class="fas fa-temperature-low"></i>
                    </div>
                    <div class="inline-block bg-brand-pink text-white text-xs lg:text-sm font-bold px-3 py-1 rounded-full mb-3">Bước 2</div>
                    <h3 class="text-lg lg:text-xl font-display font-bold text-brand-navy mb-2">Nước Ấm</h3>
                    <p class="text-gray-500 text-sm">Đun sôi 5 phút, để nguội xuống <strong>45°C</strong>.</p>
                </div>
                <!-- Step 3 -->
                <div class="bg-brand-cream rounded-[20px] lg:rounded-[30px] p-6 lg:p-8 border-4 border-white shadow-soft group reveal delay-300">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 mx-auto bg-yellow-100 rounded-full flex items-center justify-center text-2xl lg:text-3xl text-brand-gold mb-4 lg:mb-6">
                        <span class="font-black font-sans">x3</span>
                    </div>
                    <div class="inline-block bg-brand-gold text-white text-xs lg:text-sm font-bold px-3 py-1 rounded-full mb-3">Bước 3</div>
                    <h3 class="text-lg lg:text-xl font-display font-bold text-brand-navy mb-2">Pha Sữa</h3>
                    <p class="text-gray-500 text-sm">Cho <strong>3 muỗng (36g)</strong> vào <strong>180ml</strong> nước ấm.</p>
                </div>
                <!-- Step 4 -->
                <div class="bg-brand-cream rounded-[20px] lg:rounded-[30px] p-6 lg:p-8 border-4 border-white shadow-soft group reveal delay-400">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center text-2xl lg:text-3xl text-green-500 mb-4 lg:mb-6">
                        <i class="fas fa-mug-hot"></i>
                    </div>
                    <div class="inline-block bg-green-500 text-white text-xs lg:text-sm font-bold px-3 py-1 rounded-full mb-3">Bước 4</div>
                    <h3 class="text-lg lg:text-xl font-display font-bold text-brand-navy mb-2">Hoàn Tất</h3>
                    <p class="text-gray-500 text-sm">Khuấy đều và dùng ngay trong vòng 1 giờ.</p>
                </div>
            </div>
            
            <div class="mt-8 lg:mt-12 bg-blue-50 border-2 border-brand-blue/20 rounded-2xl p-4 lg:p-6 inline-block max-w-3xl reveal delay-500">
                <p class="text-brand-navy font-medium text-xs lg:text-base"><i class="fas fa-lightbulb text-brand-gold mr-2"></i> <strong>Mách nhỏ:</strong> Để giữ trọn vẹn lợi khuẩn, đừng pha nước quá nóng (trên 50°C)!</p>
            </div>
        </div>
    </section>

    <!-- Storage & Origin -->
    <section id="bao-quan" class="py-12 lg:py-20 bg-gray-50 border-t border-gray-100">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left: Storage -->
                <div class="reveal">
                    <h3 class="text-2xl lg:text-3xl font-display font-black text-brand-navy mb-6 flex items-center gap-3">
                        <i class="fas fa-box-open text-brand-gold"></i> Bảo Quản Đúng Cách
                    </h3>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-brand-blue shadow-sm flex-shrink-0 text-xl"><i class="fas fa-temperature-empty"></i></div>
                            <div>
                                <h4 class="font-bold text-brand-navy text-lg">Chưa mở nắp</h4>
                                <p class="text-sm text-gray-600">Bảo quản nơi khô ráo, thoáng mát, tránh ánh nắng trực tiếp.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-brand-blue shadow-sm flex-shrink-0 text-xl"><i class="fas fa-calendar-check"></i></div>
                            <div>
                                <h4 class="font-bold text-brand-navy text-lg">Sau khi mở nắp</h4>
                                <p class="text-sm text-gray-600">Sử dụng hết trong vòng <strong>1 tháng</strong>. Không để tủ lạnh. Đóng chặt nắp sau khi dùng.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Origin -->
                <div class="bg-white rounded-3xl p-8 shadow-soft border border-brand-soft reveal delay-200">
                    <h3 class="text-2xl lg:text-3xl font-display font-black text-brand-navy mb-6 flex items-center gap-3">
                        <i class="fas fa-certificate text-brand-gold"></i> Nguồn Gốc Xuất Xứ
                    </h3>
                    <div class="space-y-4">
                        <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100 flex items-center gap-4">
                            <div class="text-3xl">🇲🇾</div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Xuất Xứ</p>
                                <p class="text-brand-navy font-bold">Nhập khẩu nguyên hộp từ Malaysia</p>
                            </div>
                        </div>
                        <div class="p-4 bg-yellow-50 rounded-2xl border border-yellow-100 flex items-center gap-4">
                            <div class="w-12 h-12 flex items-center justify-center bg-white rounded-full shadow-sm text-brand-gold font-bold border-2 border-brand-gold">GMP</div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Tiêu Chuẩn Sản Xuất</p>
                                <p class="text-brand-navy font-bold">Đạt chuẩn GMP & Halal</p>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-gray-100 text-sm text-gray-500">
                            <p><strong>Sở hữu bản quyền:</strong> DAWN BRIDGE SDN BHD</p>
                            <p><strong>Sản xuất bởi:</strong> OMEGA HEALTH PRODUCTS SDN BHD</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white pt-12 lg:pt-20 pb-8 lg:pb-10 mt-0 relative" id="order">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-brand-blue via-brand-pink to-brand-gold"></div>
        <div class="container mx-auto px-6">
            <!-- CTA Box -->
            <div class="bg-brand-soft rounded-3xl lg:rounded-[50px] p-8 lg:p-16 relative overflow-hidden mb-12 shadow-soft text-center reveal">
                <div class="relative z-10 max-w-2xl mx-auto">
                    <h2 class="text-2xl md:text-3xl lg:text-5xl font-display font-black text-brand-navy mb-3 lg:mb-4">Tư Vấn Dinh Dưỡng</h2>
                    <p class="text-sm lg:text-xl text-gray-600 mb-6 lg:mb-8 font-medium">Để lại thông tin để nhận tư vấn miễn phí từ chuyên gia CareMIL.</p>
                    <form class="flex flex-col sm:flex-row gap-4 justify-center">
                        <input type="text" placeholder="Số điện thoại của mẹ..." class="px-6 py-3 lg:py-4 rounded-full text-gray-700 bg-white border-2 border-white focus:outline-none focus:border-brand-blue shadow-sm w-full sm:w-80 font-bold">
                        <button type="button" class="bg-brand-pink text-white font-bold py-3 lg:py-4 px-8 lg:px-10 rounded-full shadow-lg hover:bg-pink-500 transition hover:scale-105 whitespace-nowrap mt-2 sm:mt-0">
                            ĐĂNG KÝ NGAY
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Footer Content -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 border-t border-gray-100 pt-8 lg:pt-12 text-center lg:text-left">
                
                <!-- Brand & Contact (Left - 4 cols) -->
                <div class="lg:col-span-4 space-y-4">
                    <a href="#" class="text-2xl font-display font-black text-brand-navy tracking-tight flex items-center justify-center lg:justify-start gap-2 mb-2">
                        <i class="fas fa-leaf text-brand-gold"></i> Care<span class="text-brand-blue">MIL</span>
                    </a>
                    <div class="bg-blue-50 rounded-2xl p-4 border border-blue-100 inline-block w-full">
                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Tổng đài CSKH</p>
                        <p class="text-2xl font-black text-brand-pink">(+84) 985 39 18 81</p>
                        <p class="text-sm text-brand-navy font-bold mt-1">cskh@npfood.vn</p>
                    </div>
                    <div class="flex justify-center lg:justify-start space-x-3 mt-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-brand-navy text-white flex items-center justify-center hover:bg-brand-blue transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-brand-navy text-white flex items-center justify-center hover:bg-brand-blue transition"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-brand-navy text-white flex items-center justify-center hover:bg-brand-blue transition"><i class="fas fa-globe"></i></a>
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
                &copy; 2024 CareMIL Vietnam. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Expert Confirmation Modal -->
    <div id="expert-modal" class="fixed inset-0 z-[100] items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 lg:p-8 text-center relative border-4 border-brand-blue mx-4">
            <div class="w-16 h-16 lg:w-20 lg:h-20 bg-brand-blue/10 rounded-full flex items-center justify-center mx-auto mb-4 lg:mb-6 text-3xl lg:text-4xl text-brand-blue">
                <i class="fas fa-user-shield"></i>
            </div>
            <h3 class="text-xl lg:text-2xl font-display font-black text-brand-navy mb-3 lg:mb-4">Thông Báo Quan Trọng</h3>
            <p class="text-sm lg:text-base text-gray-600 mb-6 leading-relaxed">
                Nội dung dành cho <strong>Nhân viên Y tế</strong> hoặc <strong>Người tìm hiểu chuyên sâu</strong>.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 lg:gap-4 justify-center">
                <button onclick="closeExpertModal()" class="px-6 py-2 lg:py-3 rounded-full border-2 border-gray-300 text-gray-500 font-bold hover:bg-gray-100 transition text-sm lg:text-base">
                    Quay Lại
                </button>
                <button onclick="confirmExpert()" class="px-6 py-2 lg:py-3 rounded-full bg-brand-blue text-white font-bold hover:bg-blue-400 transition shadow-lg text-sm lg:text-base">
                    Xác Nhận
                </button>
            </div>
        </div>
    </div>

    <script>
        // Scroll Reveal Animation
        function reveal() {
            var reveals = document.querySelectorAll(".reveal");
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 50;
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add("active");
                }
            }
        }
        window.addEventListener("scroll", reveal);
        reveal(); // Trigger on load

        // Tab Switching Logic
        function switchTab(tabName) {
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));
            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            document.getElementById('content-' + tabName).classList.add('active');
            document.getElementById('tab-' + tabName).classList.add('active');
        }

        // Modal Logic
        function openExpertModal() {
            document.getElementById('expert-modal').classList.add('show');
        }

        function closeExpertModal() {
            document.getElementById('expert-modal').classList.remove('show');
        }

        function confirmExpert() {
            closeExpertModal();
            document.getElementById('ingredients-lock').classList.add('hidden');
            document.getElementById('ingredients-container').classList.remove('hidden');
            document.getElementById('ingredients-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Mobile Menu Logic
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('menu-icon');
            menu.classList.toggle('open');
            if (menu.classList.contains('open')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    </script>
    <?php wp_footer(); ?>
</body>
</html>