<?php
/**
 * Template Name: CareMIL Home Page
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
    <!-- Import 'Quicksand' and 'Baloo 2' for a friendlier, rounded look -->
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;800&family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            navy: '#1a4f8a',   /* Softer Navy */
                            blue: '#4cc9f0',   /* Playful Cyan */
                            gold: '#ffd166',   /* Warm Yellow */
                            soft: '#e0fbfc',   /* Very light blue */
                            cream: '#fffdf2',  /* Warm creamy background */
                            pink: '#ef476f',   /* Accent for love/care */
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
                        'card': '0 8px 0px 0px rgba(0,0,0,0.05)', /* Cartoon-like shadow */
                    }
                }
            }
        }
    </script>
    <style>
        /* Decorative Clouds */
        .cloud-shape {
            background: #fff;
            border-radius: 50%;
            position: absolute;
            z-index: 0;
        }
        .cloud-shape:after, .cloud-shape:before {
            content: '';
            position: absolute;
            background: inherit;
            border-radius: 50%;
        }
        
        /* Soft Wave Divider */
        .custom-wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            transform: rotate(180deg);
        }
        .custom-wave svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 80px;
        }

        /* Cute Sticker Effect */
        .sticker {
            background: white;
            border: 3px solid #f0f4f8;
            border-radius: 24px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .sticker:hover {
            transform: translateY(-8px) scale(1.02);
            border-color: #ffd166;
            box-shadow: 0 15px 30px rgba(255, 209, 102, 0.3);
        }

        /* Reveal Animation */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s ease-out;
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        .blob-bg {
             background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill='%23E0FBFC' d='M44.7,-76.4C58.9,-69.2,71.8,-59.1,79.6,-46.3C87.4,-33.5,90.1,-18,87.9,-3.3C85.7,11.3,78.6,25.1,69.5,37.3C60.4,49.5,49.3,60.1,36.5,67.3C23.7,74.5,9.2,78.3,-4.2,85.5C-17.6,92.7,-29.9,103.3,-40.8,100.2C-51.7,97.1,-61.2,80.3,-69.3,64.9C-77.4,49.5,-84.1,35.5,-86.3,20.7C-88.5,5.9,-86.2,-9.7,-79.9,-23.6C-73.6,-37.5,-63.3,-49.7,-51.2,-58.1C-39.1,-66.5,-25.2,-71.1,-11.1,-70.9C3,-70.7,5.9,-65.7,44.7,-76.4Z' transform='translate(100 100)' /%3E%3C/svg%3E");
             background-repeat: no-repeat;
             background-position: center;
             background-size: cover;
        }

        /* Custom Tab Styles */
        .tab-btn {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .tab-btn.active {
            background-color: #4cc9f0; /* brand-blue */
            color: white;
            box-shadow: 0 4px 15px rgba(76, 201, 240, 0.4);
            transform: translateY(-2px);
        }
        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        .tab-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .nutrition-table th {
            font-family: 'Baloo 2', cursive;
            color: #1a4f8a;
        }
        .nutrition-table td {
            font-family: 'Quicksand', sans-serif;
            font-weight: 600;
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
        
        /* Lock Screen Style */
        .locked-content {
            filter: blur(8px);
            pointer-events: none;
            user-select: none;
        }
        @media (max-width: 1024px) {
            .hero-grid {
                gap: 2.5rem;
            }
        }
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.25rem;
                line-height: 1.3;
            }
            .hero-grid .sticker {
                padding: 1.25rem;
            }
            header .custom-wave svg {
                height: 50px;
            }
        }
        @media (max-width: 640px) {
            .hero-title {
                font-size: 1.9rem;
            }
            .hero-grid {
                gap: 2rem;
            }
            .nutrition-table {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body class="bg-brand-cream text-gray-600 font-sans selection:bg-brand-gold selection:text-white">

    <!-- Navigation (Rounded & Cute) -->
    <nav class="fixed w-full z-50 transition-all duration-300 py-3" id="navbar">
        <div class="container mx-auto px-4 md:px-6">
            <div class="bg-white/90 backdrop-blur-md rounded-full shadow-soft px-6 py-3 flex justify-between items-center border border-white">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-2xl md:text-3xl font-display font-black text-brand-navy tracking-tight flex items-center gap-2">
                    <i class="fas fa-leaf text-brand-gold"></i>
                    Care<span class="text-brand-blue">MIL</span>
                </a>
                
                <div class="hidden md:flex items-center space-x-8 font-bold text-gray-500 text-lg">
                    <a href="#loi-ich" class="hover:text-brand-blue transition">Bé Yêu</a>
                    <a href="#thanh-phan" class="hover:text-brand-blue transition">Dinh Dưỡng</a>
                    <a href="#bang-thanh-phan" class="hover:text-brand-blue transition">Chi Tiết</a>
                    <a href="#huong-dan" class="hover:text-brand-blue transition">Pha Chế</a>
                    <button onclick="document.getElementById('order').scrollIntoView()" class="bg-brand-gold text-white font-bold py-2 px-6 rounded-full shadow-lg hover:bg-yellow-400 hover:scale-105 transition transform flex items-center gap-2">
                        <i class="fas fa-shopping-cart"></i> Mua Ngay
                    </button>
                </div>
                
                <button id="nav-toggle" aria-label="Mở menu" class="md:hidden text-brand-navy text-2xl bg-blue-50 p-2 rounded-full w-10 h-10 flex items-center justify-center">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div id="mobile-menu" class="md:hidden mt-3 hidden bg-white/95 backdrop-blur-lg rounded-3xl shadow-soft border border-white px-6 py-4 space-y-3 font-bold text-gray-600">
                <a href="#loi-ich" class="block hover:text-brand-blue transition">Bé Yêu</a>
                <a href="#thanh-phan" class="block hover:text-brand-blue transition">Dinh Dưỡng</a>
                <a href="#bang-thanh-phan" class="block hover:text-brand-blue transition">Chi Tiết</a>
                <a href="#huong-dan" class="block hover:text-brand-blue transition">Pha Chế</a>
                <button onclick="document.getElementById('order').scrollIntoView()" class="w-full bg-brand-gold text-white font-bold py-3 rounded-2xl shadow-lg hover:bg-yellow-400 transition flex items-center justify-center gap-2">
                    <i class="fas fa-shopping-cart"></i> Mua Ngay
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-32 pb-24 lg:pt-48 lg:pb-32 overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 bg-gradient-to-b from-blue-50 to-brand-cream -z-20"></div>
        <div class="absolute top-20 left-10 text-brand-blue opacity-20 text-6xl animate-bounce-slow"><i class="fas fa-cloud"></i></div>
        <div class="absolute top-40 right-20 text-brand-gold opacity-30 text-5xl animate-float"><i class="fas fa-sun"></i></div>
        <div class="absolute bottom-20 left-1/4 text-green-300 opacity-30 text-4xl animate-wiggle"><i class="fas fa-leaf"></i></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="hero-grid flex flex-col lg:flex-row items-center gap-10">
                <div class="lg:w-1/2 text-center lg:text-left reveal active space-y-6">
                    <div class="inline-block px-4 py-2 rounded-full bg-white border-2 border-brand-blue/30 text-brand-navy font-bold shadow-sm mb-2">
                        🌱 Dinh dưỡng chuẩn Clean Label
                    </div>
                    
                    <h1 class="hero-title text-4xl lg:text-6xl font-display font-black text-brand-navy leading-tight">
                        Mát Lành Như <br>
                        <span class="text-brand-blue relative inline-block">
                            Dòng Sữa Mẹ
                            <svg class="absolute w-full h-3 -bottom-1 left-0 text-brand-gold" viewBox="0 0 200 9" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.00025 6.99997C33.5003 3.49997 73.5002 -2.00003 198 3.99999" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                        </span>
                    </h1>
                    
                    <p class="text-xl text-gray-500 font-medium leading-relaxed">
                        Từ <strong>Đạm Đậu Hà Lan</strong> tinh khiết & <strong>Fibregum™</strong>. Giúp bé tiêu hóa khỏe, bụng êm, không lo dị ứng sữa bò. Thơm ngon cả nhà đều mê!
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-4">
                        <a href="#order" class="bg-brand-pink text-white text-lg font-bold py-4 px-8 rounded-full shadow-lg hover:bg-pink-500 hover:shadow-pink-300/50 transition transform hover:-translate-y-1 flex items-center justify-center gap-3">
                            <i class="fas fa-heart animate-pulse"></i> Ưu Đãi Cho Mẹ & Bé
                        </a>
                    </div>

                    <div class="pt-6 flex flex-wrap justify-center lg:justify-start gap-4">
                        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-full border border-blue-100 shadow-sm text-brand-navy font-bold text-sm">
                            <i class="fas fa-shield-alt text-brand-blue"></i> Không Dị Ứng
                        </div>
                        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-full border border-blue-100 shadow-sm text-brand-navy font-bold text-sm">
                            <i class="fas fa-seedling text-green-400"></i> 100% Thực Vật
                        </div>
                    </div>
                </div>
                
                <div class="lg:w-1/2 relative reveal delay-200 text-center">
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-brand-soft rounded-full filter blur-2xl -z-10 opacity-70"></div>
                    
                    <div class="absolute top-0 right-10 bg-white p-3 rounded-2xl shadow-lg z-20 animate-float">
                        <span class="text-2xl">🥛</span> <span class="font-display font-bold text-brand-navy">Giàu Protein</span>
                    </div>
                    <div class="absolute bottom-10 left-0 bg-white p-3 rounded-2xl shadow-lg z-20 animate-float" style="animation-delay: 1s;">
                        <span class="text-2xl">😋</span> <span class="font-display font-bold text-brand-navy">Vị Vani Dịu</span>
                    </div>

                    <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-scaled.png" 
                         onerror="this.src='https://placehold.co/600x600/e0fbfc/1a4f8a?text=CareMIL+Mockup&font=baloo2'"
                         alt="Hộp sữa CareMIL" 
                         class="w-3/4 mx-auto relative z-10 drop-shadow-2xl hover:scale-105 transition duration-500">
                </div>
            </div>
        </div>

        <div class="custom-wave text-white">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="#ffffff"></path>
            </svg>
        </div>
    </header>

    <!-- 8 NOs Section -->
    <section id="loi-ich" class="py-20 bg-white relative">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-16 reveal">
                <div class="inline-block bg-brand-gold/20 text-brand-navy px-4 py-1 rounded-full text-sm font-bold mb-3 uppercase tracking-wider">
                    An Toàn Tuyệt Đối
                </div>
                <h2 class="text-3xl md:text-5xl font-display font-black text-brand-navy mb-4">Cam Kết <span class="text-brand-pink">"8 KHÔNG"</span></h2>
                <p class="text-lg text-gray-500">Mẹ yên tâm, bé khỏe mạnh. Chúng tôi loại bỏ mọi yếu tố gây hại để mang lại dòng sữa lành tính nhất.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Items... -->
                <div class="sticker p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-100">
                    <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mb-4 text-3xl group-hover:rotate-12 transition transform">🍞</div>
                    <h3 class="font-display font-bold text-xl text-brand-navy">Gluten Free</h3>
                    <p class="text-sm text-gray-400 font-medium">Không Gluten</p>
                </div>
                <div class="sticker p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-150">
                    <div class="w-16 h-16 rounded-full bg-pink-100 flex items-center justify-center mb-4 text-3xl group-hover:rotate-12 transition transform">💧</div>
                    <h3 class="font-display font-bold text-xl text-brand-navy">Lactose Free</h3>
                    <p class="text-sm text-gray-400 font-medium">Không Lactose</p>
                </div>
                <div class="sticker p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-200">
                    <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mb-4 text-3xl group-hover:rotate-12 transition transform">🌱</div>
                    <h3 class="font-display font-bold text-xl text-brand-navy">Soy Free</h3>
                    <p class="text-sm text-gray-400 font-medium">Không Đậu Nành</p>
                </div>
                <div class="sticker p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-250">
                    <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center mb-4 text-3xl group-hover:rotate-12 transition transform">🍬</div>
                    <h3 class="font-display font-bold text-xl text-brand-navy">No Sugar</h3>
                    <p class="text-sm text-gray-400 font-medium">Không Đường Thêm</p>
                </div>
                <div class="sticker p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-300">
                    <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center mb-4 text-3xl group-hover:rotate-12 transition transform">🧀</div>
                    <h3 class="font-display font-bold text-xl text-brand-navy">Dairy Free</h3>
                    <p class="text-sm text-gray-400 font-medium">Không Sữa Bò</p>
                </div>
                 <div class="sticker p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-300">
                    <div class="w-16 h-16 rounded-full bg-purple-100 flex items-center justify-center mb-4 text-3xl group-hover:rotate-12 transition transform">🧪</div>
                    <h3 class="font-display font-bold text-xl text-brand-navy">No Preservative</h3>
                    <p class="text-sm text-gray-400 font-medium">Không Chất Bảo Quản</p>
                </div>
                 <div class="sticker p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-300">
                    <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mb-4 text-3xl group-hover:rotate-12 transition transform">🎨</div>
                    <h3 class="font-display font-bold text-xl text-brand-navy">No Colouring</h3>
                    <p class="text-sm text-gray-400 font-medium">Không Phẩm Màu</p>
                </div>
                 <div class="sticker p-6 flex flex-col items-center text-center group cursor-pointer reveal delay-300">
                    <div class="w-16 h-16 rounded-full bg-teal-100 flex items-center justify-center mb-4 text-3xl group-hover:rotate-12 transition transform">🧬</div>
                    <h3 class="font-display font-bold text-xl text-brand-navy">Non GMO</h3>
                    <p class="text-sm text-gray-400 font-medium">Không Biến Đổi Gen</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Ingredients -->
    <section id="thanh-phan" class="py-24 bg-brand-soft relative overflow-hidden">
        <div class="absolute top-10 left-10 w-24 h-24 bg-white rounded-full opacity-40 animate-float"></div>
        <div class="absolute bottom-20 right-20 w-32 h-32 bg-brand-gold rounded-full opacity-20 animate-float" style="animation-delay: 2s;"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center mb-16 reveal">
                <h2 class="text-3xl md:text-5xl font-display font-black text-brand-navy mb-4">Khu Vườn Dinh Dưỡng</h2>
                <p class="text-lg text-gray-600">Khám phá những dưỡng chất "vàng" được chắt lọc trong mỗi lon CareMIL.</p>
            </div>

            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/3 space-y-6">
                    <div class="bg-white p-6 rounded-3xl shadow-soft flex items-center gap-4 transform hover:scale-105 transition duration-300 reveal delay-100 border-l-8 border-green-400">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-2xl">🥜</div>
                        <div>
                            <h3 class="font-display font-bold text-lg text-brand-navy">Pea Protein Isolate</h3>
                            <p class="text-sm text-gray-500">Đạm Đậu Hà Lan giúp bé tăng cân, chắc cơ.</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl shadow-soft flex items-center gap-4 transform hover:scale-105 transition duration-300 reveal delay-200 border-l-8 border-brand-gold">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center text-2xl">🦠</div>
                        <div>
                            <h3 class="font-display font-bold text-lg text-brand-navy">Fibregum™ (Prebiotic)</h3>
                            <p class="text-sm text-gray-500">Chất xơ nuôi lợi khuẩn, cho bụng êm ru.</p>
                        </div>
                    </div>
                </div>

                <div class="lg:w-1/3 relative flex justify-center py-10 lg:py-0 reveal">
                     <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-64 h-64 bg-white rounded-full opacity-60 animate-pulse"></div>
                     </div>
                     <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-scaled.png" 
                          onerror="this.src='https://placehold.co/500x700/e0fbfc/1a4f8a?text=Product&font=baloo2'"
                          alt="CareMIL Product" 
                          class="relative z-10 w-48 lg:w-64 drop-shadow-2xl hover:rotate-3 transition duration-500">
                </div>

                <div class="lg:w-1/3 space-y-6">
                    <div class="bg-white p-6 rounded-3xl shadow-soft flex items-center gap-4 transform hover:scale-105 transition duration-300 reveal delay-100 border-r-8 border-brand-blue">
                         <div>
                            <h3 class="font-display font-bold text-lg text-brand-navy text-right">23 Vitamin & Khoáng</h3>
                            <p class="text-sm text-gray-500 text-right">Kẽm, Canxi, Vitamin D3 tăng đề kháng.</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-2xl">🛡️</div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl shadow-soft flex items-center gap-4 transform hover:scale-105 transition duration-300 reveal delay-200 border-r-8 border-brand-pink">
                         <div>
                            <h3 class="font-display font-bold text-lg text-brand-navy text-right">Hương Vani Tự Nhiên</h3>
                            <p class="text-sm text-gray-500 text-right">Thơm dịu, ngọt nhẹ, bé nào cũng thích.</p>
                        </div>
                        <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center text-2xl">🍦</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NEW SECTION: Detailed Ingredients Tabs with GATEKEEPER -->
    <section id="bang-thanh-phan" class="py-20 bg-white relative">
        <div class="container mx-auto px-6">
            <div class="text-center mb-10 reveal">
                <h2 class="text-3xl md:text-5xl font-display font-black text-brand-navy mb-4">Chi Tiết Thành Phần</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Công bố minh bạch bảng thành phần dinh dưỡng để mẹ hoàn toàn yên tâm khi lựa chọn cho bé.</p>
            </div>

            <!-- GATEKEEPER BUTTON (Initial View) -->
            <div id="ingredients-lock" class="text-center py-10">
                <div class="bg-blue-50 border-2 border-brand-blue/20 rounded-3xl p-8 max-w-3xl mx-auto shadow-sm">
                    <div class="text-4xl mb-4 text-brand-blue"><i class="fas fa-user-md"></i></div>
                    <h3 class="text-2xl font-display font-bold text-brand-navy mb-4">Thông Tin Chuyên Sâu</h3>
                    <p class="text-gray-600 mb-6">Để xem bảng thành phần chi tiết (Vitamin, Khoáng chất...), vui lòng xác nhận bạn quan tâm đến các thông tin dinh dưỡng chuyên sâu.</p>
                    <button onclick="openExpertModal()" class="bg-brand-blue text-white font-bold py-3 px-8 rounded-full shadow-lg hover:bg-blue-400 transition transform hover:scale-105 flex items-center gap-2 mx-auto">
                        <i class="fas fa-eye"></i> Xem Chi Tiết Thành Phần
                    </button>
                </div>
            </div>

            <!-- HIDDEN CONTENT (Revealed after confirmation) -->
            <div id="ingredients-container" class="hidden">
                <!-- Tab Navigation -->
                <div class="flex flex-wrap justify-center gap-4 mb-10 reveal delay-100">
                    <button onclick="switchTab('nutrition')" id="tab-nutrition" class="tab-btn active px-8 py-3 rounded-full bg-gray-100 text-gray-500 font-bold font-display text-lg hover:bg-gray-200">
                        NUTRITIONAL INFORMATION
                    </button>
                    <button onclick="switchTab('vitamins')" id="tab-vitamins" class="tab-btn px-8 py-3 rounded-full bg-gray-100 text-gray-500 font-bold font-display text-lg hover:bg-gray-200">
                        VITAMINS
                    </button>
                    <button onclick="switchTab('minerals')" id="tab-minerals" class="tab-btn px-8 py-3 rounded-full bg-gray-100 text-gray-500 font-bold font-display text-lg hover:bg-gray-200">
                        MINERALS
                    </button>
                    <button onclick="switchTab('other')" id="tab-other" class="tab-btn px-8 py-3 rounded-full bg-gray-100 text-gray-500 font-bold font-display text-lg hover:bg-gray-200">
                        OTHER INGREDIENTS
                    </button>
                </div>

                <!-- Tab Contents -->
                <div class="bg-brand-soft rounded-[40px] p-8 md:p-12 shadow-soft reveal delay-200 min-h-[400px]">
                    <!-- Tab 1: Nutritional Info -->
                    <div id="content-nutrition" class="tab-content active">
                        <h3 class="text-2xl font-display font-bold text-brand-navy mb-2 text-center">Thông Tin Dinh Dưỡng</h3>
                        <p class="text-center text-gray-500 mb-6 font-medium">Serving Size: 3 scoops (36g) • Servings Per Container: 22</p>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse nutrition-table bg-white rounded-2xl overflow-hidden shadow-sm text-sm md:text-base">
                                <thead class="bg-brand-blue text-white">
                                    <tr>
                                        <th class="p-4">Thành phần (Component)</th>
                                        <th class="p-4 text-center">Đơn vị</th>
                                        <th class="p-4 text-right">Per 100g</th>
                                        <th class="p-4 text-right">Per Serving (36g)</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600">
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold text-brand-navy">Energy</td>
                                        <td class="p-4 text-center">kcal</td>
                                        <td class="p-4 text-right">389</td>
                                        <td class="p-4 text-right">140</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50 bg-gray-50/50">
                                        <td class="p-4 font-bold">Fat</td>
                                        <td class="p-4 text-center">g</td>
                                        <td class="p-4 text-right">11.5</td>
                                        <td class="p-4 text-right">4.1</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 pl-8 italic">- Monounsaturated Fatty Acids</td>
                                        <td class="p-4 text-center">g</td>
                                        <td class="p-4 text-right">0.4</td>
                                        <td class="p-4 text-right">0.1</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 pl-8 italic">- Polyunsaturated Fatty Acids</td>
                                        <td class="p-4 text-center">g</td>
                                        <td class="p-4 text-right">1.3</td>
                                        <td class="p-4 text-right">0.5</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 pl-12 text-xs md:text-sm text-gray-500">• Alpha-Linolenic Acid (ALA)</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">420.0</td>
                                        <td class="p-4 text-right">151.2</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 pl-12 text-xs md:text-sm text-gray-500">• Linoleic Acid (LA)</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">3360.0</td>
                                        <td class="p-4 text-right">1209.6</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 pl-8 italic">- Saturated Fatty Acids</td>
                                        <td class="p-4 text-center">g</td>
                                        <td class="p-4 text-right">9.3</td>
                                        <td class="p-4 text-right">3.3</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 pl-8 italic">- Trans Fatty Acids</td>
                                        <td class="p-4 text-center">g</td>
                                        <td class="p-4 text-right">0</td>
                                        <td class="p-4 text-right">0</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Cholesterol</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">0</td>
                                        <td class="p-4 text-right">0</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50 bg-gray-50/50">
                                        <td class="p-4 font-bold text-brand-navy">Protein</td>
                                        <td class="p-4 text-center">g</td>
                                        <td class="p-4 text-right font-bold">17.8</td>
                                        <td class="p-4 text-right font-bold">6.4</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Carbohydrate</td>
                                        <td class="p-4 text-center">g</td>
                                        <td class="p-4 text-right">46.1</td>
                                        <td class="p-4 text-right">16.6</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Dietary Fibre</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">10500.0</td>
                                        <td class="p-4 text-right">3780.0</td>
                                    </tr>
                                    <tr class="hover:bg-blue-50">
                                        <td class="p-4 pl-8 italic">- Fructooligosaccharides (FOS)</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">1100.0</td>
                                        <td class="p-4 text-right">396.0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 2: Vitamins -->
                    <div id="content-vitamins" class="tab-content">
                        <h3 class="text-2xl font-display font-bold text-brand-navy mb-6 text-center">Hệ Vitamin Phong Phú</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse nutrition-table bg-white rounded-2xl overflow-hidden shadow-sm text-sm md:text-base">
                                <thead class="bg-brand-blue text-white">
                                    <tr>
                                        <th class="p-4">Thành phần (Component)</th>
                                        <th class="p-4 text-center">Đơn vị</th>
                                        <th class="p-4 text-right">Per 100g</th>
                                        <th class="p-4 text-right">Per Serving (36g)</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600">
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Vitamin A</td>
                                        <td class="p-4 text-center">mcg</td>
                                        <td class="p-4 text-right">540.0</td>
                                        <td class="p-4 text-right">194.4</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Vitamin D3</td>
                                        <td class="p-4 text-center">mcg</td>
                                        <td class="p-4 text-right">9.4</td>
                                        <td class="p-4 text-right">3.4</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Vitamin E</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">6.3</td>
                                        <td class="p-4 text-right">2.3</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Vitamin K1</td>
                                        <td class="p-4 text-center">mcg</td>
                                        <td class="p-4 text-right">30.0</td>
                                        <td class="p-4 text-right">10.8</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Vitamin C</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">67.5</td>
                                        <td class="p-4 text-right">24.3</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Vitamin B9 (Folic Acid)</td>
                                        <td class="p-4 text-center">mcg</td>
                                        <td class="p-4 text-right">165.0</td>
                                        <td class="p-4 text-right">59.4</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Vitamin B1</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">0.8</td>
                                        <td class="p-4 text-right">0.3</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Vitamin B2</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">1.1</td>
                                        <td class="p-4 text-right">0.4</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Vitamin B3</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">6.0</td>
                                        <td class="p-4 text-right">2.2</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Vitamin B5</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">4.2</td>
                                        <td class="p-4 text-right">1.5</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Vitamin B6</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">7.2</td>
                                        <td class="p-4 text-right">2.6</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Vitamin B7</td>
                                        <td class="p-4 text-center">mcg</td>
                                        <td class="p-4 text-right">16.5</td>
                                        <td class="p-4 text-right">5.9</td>
                                    </tr>
                                    <tr class="hover:bg-blue-50">
                                        <td class="p-4 font-bold">Vitamin B12</td>
                                        <td class="p-4 text-center">mcg</td>
                                        <td class="p-4 text-right">2.7</td>
                                        <td class="p-4 text-right">1.0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 3: Minerals -->
                    <div id="content-minerals" class="tab-content">
                        <h3 class="text-2xl font-display font-bold text-brand-navy mb-6 text-center">Khoáng Chất Thiết Yếu</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse nutrition-table bg-white rounded-2xl overflow-hidden shadow-sm text-sm md:text-base">
                                <thead class="bg-brand-blue text-white">
                                    <tr>
                                        <th class="p-4">Thành phần (Component)</th>
                                        <th class="p-4 text-center">Đơn vị</th>
                                        <th class="p-4 text-right">Per 100g</th>
                                        <th class="p-4 text-right">Per Serving (36g)</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600">
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Sodium</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">76</td>
                                        <td class="p-4 text-right">27</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Potassium</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">370.0</td>
                                        <td class="p-4 text-right">133.2</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Chloride</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">215</td>
                                        <td class="p-4 text-right">77</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50 bg-gray-50/50">
                                        <td class="p-4 font-bold text-brand-navy">Calcium</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right font-bold">535.7</td>
                                        <td class="p-4 text-right font-bold">192.9</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Phosphorus</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">263.0</td>
                                        <td class="p-4 text-right">94.7</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Magnesium</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">88.9</td>
                                        <td class="p-4 text-right">32.0</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Iron</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">6.3</td>
                                        <td class="p-4 text-right">2.3</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Zinc</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">8.1</td>
                                        <td class="p-4 text-right">2.9</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Iodine</td>
                                        <td class="p-4 text-center">mcg</td>
                                        <td class="p-4 text-right">80.4</td>
                                        <td class="p-4 text-right">28.9</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">Selenium</td>
                                        <td class="p-4 text-center">mcg</td>
                                        <td class="p-4 text-right">12.9</td>
                                        <td class="p-4 text-right">4.6</td>
                                    </tr>
                                    <tr class="hover:bg-blue-50 bg-gray-50/50">
                                        <td class="p-4 font-bold text-brand-navy">Choline</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">150.0</td>
                                        <td class="p-4 text-right">54.0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 4: Other Ingredients -->
                    <div id="content-other" class="tab-content">
                        <h3 class="text-2xl font-display font-bold text-brand-navy mb-6 text-center">Thành Phần Khác</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse nutrition-table bg-white rounded-2xl overflow-hidden shadow-sm text-sm md:text-base">
                                <thead class="bg-brand-blue text-white">
                                    <tr>
                                        <th class="p-4">Thành phần (Component)</th>
                                        <th class="p-4 text-center">Đơn vị</th>
                                        <th class="p-4 text-right">Per 100g</th>
                                        <th class="p-4 text-right">Per Serving (36g)</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600">
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold text-brand-navy">Probiotic <br><span class="text-xs md:text-sm font-normal text-gray-500 italic">(L. acidophilus NCFM; B. lactis Bi-07)</span></td>
                                        <td class="p-4 text-center">cfu</td>
                                        <td class="p-4 text-right">1 x 10<sup>9</sup></td>
                                        <td class="p-4 text-right">360 x 10<sup>6</sup></td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">L-lysine</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">67.0</td>
                                        <td class="p-4 text-right">24.1</td>
                                    </tr>
                                    <tr class="border-b border-gray-100 hover:bg-blue-50">
                                        <td class="p-4 font-bold">L-glutamine</td>
                                        <td class="p-4 text-center">mg</td>
                                        <td class="p-4 text-right">5000</td>
                                        <td class="p-4 text-right">1800</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-8 pt-4 border-t border-gray-100 text-center">
                            <p class="text-sm text-gray-500 italic mb-2">Thành phần khác: Pea Protein Isolate, Vegetable Oil (Sunflower, MCT form Coconut, Canola), Fibregum™, Vanilla Bean Powder.</p>
                            <p class="text-xs text-gray-400">Sản phẩm không chứa thành phần biến đổi gen, không chất bảo quản.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Fun Steps Section -->
    <section id="huong-dan" class="py-24 bg-white relative overflow-hidden">
        <!-- Background Decor -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
             <div class="absolute top-10 left-10 text-brand-blue/10 text-8xl transform -rotate-12"><i class="fas fa-tint"></i></div>
             <div class="absolute bottom-10 right-10 text-brand-gold/10 text-8xl transform rotate-12"><i class="fas fa-cookie-bite"></i></div>
        </div>

        <div class="container mx-auto px-6 text-center relative z-10">
            <h2 class="text-3xl md:text-5xl font-display font-black text-brand-navy mb-4 reveal">Pha Sữa Đúng Chuẩn</h2>
            <p class="text-lg text-gray-500 mb-16 max-w-2xl mx-auto reveal">4 bước đơn giản để có ly sữa thơm ngon, trọn vẹn dưỡng chất cho bé.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                
                <!-- Step 1 -->
                <div class="bg-brand-cream rounded-[30px] p-8 border-4 border-white shadow-soft group hover:-translate-y-2 transition duration-300 reveal delay-100">
                    <div class="w-20 h-20 mx-auto bg-blue-100 rounded-full flex items-center justify-center text-3xl text-brand-blue mb-6 group-hover:scale-110 transition duration-300">
                        <i class="fas fa-hands-wash"></i>
                    </div>
                    <div class="inline-block bg-brand-blue text-white text-sm font-bold px-3 py-1 rounded-full mb-3">Bước 1</div>
                    <h3 class="text-xl font-display font-bold text-brand-navy mb-2">Vệ Sinh Sạch Sẽ</h3>
                    <p class="text-gray-500 text-sm">Rửa sạch tay và tiệt trùng dụng cụ (ly, thìa) kỹ lưỡng trước khi pha.</p>
                </div>

                <!-- Step 2 -->
                <div class="bg-brand-cream rounded-[30px] p-8 border-4 border-white shadow-soft group hover:-translate-y-2 transition duration-300 reveal delay-200">
                    <div class="w-20 h-20 mx-auto bg-pink-100 rounded-full flex items-center justify-center text-3xl text-brand-pink mb-6 group-hover:scale-110 transition duration-300">
                        <i class="fas fa-temperature-low"></i>
                    </div>
                    <div class="inline-block bg-brand-pink text-white text-sm font-bold px-3 py-1 rounded-full mb-3">Bước 2</div>
                    <h3 class="text-xl font-display font-bold text-brand-navy mb-2">Chuẩn Bị Nước</h3>
                    <p class="text-gray-500 text-sm">Đun sôi nước trong 5 phút, sau đó để nguội xuống khoảng <strong>45°C</strong>.</p>
                </div>

                <!-- Step 3 -->
                <div class="bg-brand-cream rounded-[30px] p-8 border-4 border-white shadow-soft group hover:-translate-y-2 transition duration-300 reveal delay-300">
                    <div class="w-20 h-20 mx-auto bg-yellow-100 rounded-full flex items-center justify-center text-3xl text-brand-gold mb-6 group-hover:scale-110 transition duration-300">
                        <span class="font-black font-sans">x3</span>
                    </div>
                    <div class="inline-block bg-brand-gold text-white text-sm font-bold px-3 py-1 rounded-full mb-3">Bước 3</div>
                    <h3 class="text-xl font-display font-bold text-brand-navy mb-2">Pha Sữa</h3>
                    <p class="text-gray-500 text-sm">Cho <strong>3 muỗng gạt (36g)</strong> bột CareMIL vào <strong>180ml</strong> nước ấm đã chuẩn bị.</p>
                </div>

                <!-- Step 4 -->
                <div class="bg-brand-cream rounded-[30px] p-8 border-4 border-white shadow-soft group hover:-translate-y-2 transition duration-300 reveal delay-400">
                    <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center text-3xl text-green-500 mb-6 group-hover:scale-110 transition duration-300">
                        <i class="fas fa-mug-hot"></i>
                    </div>
                    <div class="inline-block bg-green-500 text-white text-sm font-bold px-3 py-1 rounded-full mb-3">Bước 4</div>
                    <h3 class="text-xl font-display font-bold text-brand-navy mb-2">Khuấy & Dùng Ngay</h3>
                    <p class="text-gray-500 text-sm">Khuấy đều đến khi tan hết. Đổ bỏ phần thừa nếu không dùng hết sau 1 giờ.</p>
                </div>

            </div>
            
            <!-- Tip Box -->
            <div class="mt-12 bg-blue-50 border-2 border-brand-blue/20 rounded-2xl p-6 inline-block max-w-3xl reveal delay-500">
                <p class="text-brand-navy font-medium"><i class="fas fa-lightbulb text-brand-gold mr-2"></i> <strong>Mách nhỏ:</strong> Để giữ trọn vẹn lợi khuẩn Probiotic, mẹ nhớ đừng pha nước quá nóng (trên 50°C) nhé!</p>
            </div>
        </div>
    </section>

    <!-- Footer: Warm & Clean -->
    <footer class="bg-white pt-20 pb-10 mt-10 relative" id="order">
        <!-- Top Border decoration -->
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-brand-blue via-brand-pink to-brand-gold"></div>

        <div class="container mx-auto px-6">
            <!-- CTA Box: Rounded & Friendly -->
            <div class="bg-brand-soft rounded-[50px] p-10 md:p-16 relative overflow-hidden mb-20 shadow-soft text-center reveal">
                <div class="relative z-10 max-w-2xl mx-auto">
                    <h2 class="text-3xl md:text-5xl font-display font-black text-brand-navy mb-4">Dành Tặng Điều Tốt Nhất Cho Con</h2>
                    <p class="text-xl text-gray-600 mb-8 font-medium">Để lại thông tin để nhận tư vấn dinh dưỡng miễn phí từ chuyên gia CareMIL nhé!</p>
                    
                    <form class="flex flex-col sm:flex-row gap-4 justify-center">
                        <input type="text" placeholder="Số điện thoại của mẹ..." class="px-6 py-4 rounded-full text-gray-700 bg-white border-2 border-white focus:outline-none focus:border-brand-blue shadow-sm w-full sm:w-80 font-bold">
                        <button type="button" class="bg-brand-pink text-white font-bold py-4 px-10 rounded-full shadow-lg hover:bg-pink-500 transition hover:scale-105 whitespace-nowrap">
                            ĐĂNG KÝ NGAY
                        </button>
                    </form>
                </div>
                <!-- Decor Circles -->
                <div class="absolute top-0 left-0 w-32 h-32 bg-white rounded-full opacity-50 -ml-10 -mt-10"></div>
                <div class="absolute bottom-0 right-0 w-40 h-40 bg-brand-gold rounded-full opacity-20 -mr-10 -mb-10"></div>
            </div>

            <!-- Footer Info -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 border-t border-gray-100 pt-12">
                <div class="col-span-1 md:col-span-2">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-3xl font-display font-black text-brand-navy tracking-tight flex items-center gap-2 mb-4">
                        <i class="fas fa-leaf text-brand-gold"></i> Care<span class="text-brand-blue">MIL</span>
                    </a>
                    <p class="text-gray-500 mb-6 max-w-sm font-medium">Nguồn dinh dưỡng thực vật tinh khiết, được hàng ngàn bà mẹ tin dùng.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-brand-blue hover:bg-brand-blue hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-pink-50 flex items-center justify-center text-brand-pink hover:bg-brand-pink hover:text-white transition"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-black hover:text-white transition"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-6 text-brand-navy font-display">Về Chúng Tôi</h4>
                    <ul class="space-y-3 text-gray-500 font-medium">
                        <li><a href="#" class="hover:text-brand-blue transition">Câu chuyện thương hiệu</a></li>
                        <li><a href="#" class="hover:text-brand-blue transition">Chứng nhận chất lượng</a></li>
                        <li><a href="#" class="hover:text-brand-blue transition">Góc chuyên gia</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-6 text-brand-navy font-display">Hỗ Trợ</h4>
                    <ul class="space-y-3 text-gray-500 font-medium">
                        <li class="flex items-center gap-3"><i class="fas fa-phone-alt text-brand-pink"></i> 1900 xxxx</li>
                        <li class="flex items-center gap-3"><i class="fas fa-envelope text-brand-pink"></i> cskh@caremil.vn</li>
                        <li><a href="#" class="hover:text-brand-blue transition">Chính sách đổi trả</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 text-center text-gray-400 text-sm font-medium">
                &copy; 2024 CareMIL Vietnam. Made with love for kids.
            </div>
        </div>
    </footer>

    <!-- Expert Confirmation Modal -->
    <div id="expert-modal" class="fixed inset-0 z-[100] items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-8 text-center relative border-4 border-brand-blue">
            <div class="w-20 h-20 bg-brand-blue/10 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl text-brand-blue">
                <i class="fas fa-user-shield"></i>
            </div>
            <h3 class="text-2xl font-display font-black text-brand-navy mb-4">Thông Báo Quan Trọng</h3>
            <p class="text-gray-600 mb-6 leading-relaxed">
                Nội dung dưới đây cung cấp các thông tin chi tiết về thành phần dinh dưỡng chuyên sâu, được thiết kế để tham khảo cho <strong>Nhân viên Y tế</strong> hoặc <strong>Người có nhu cầu tìm hiểu kỹ</strong>.
                <br><br>
                Bằng cách nhấn "Xác Nhận", bạn đồng ý rằng bạn đang tìm hiểu thông tin này với mục đích tham khảo chuyên môn hoặc cá nhân.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="closeExpertModal()" class="px-6 py-3 rounded-full border-2 border-gray-300 text-gray-500 font-bold hover:bg-gray-100 transition">
                    Quay Lại
                </button>
                <button onclick="confirmExpert()" class="px-8 py-3 rounded-full bg-brand-blue text-white font-bold hover:bg-blue-400 transition shadow-lg">
                    Xác Nhận Xem Tiếp
                </button>
            </div>
        </div>
    </div>

    <script>
        const navToggle = document.getElementById('nav-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        if (navToggle && mobileMenu) {
            navToggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
            mobileMenu.querySelectorAll('a, button').forEach((item) => {
                item.addEventListener('click', () => {
                    mobileMenu.classList.add('hidden');
                });
            });
            document.addEventListener('click', (event) => {
                if (!mobileMenu.contains(event.target) && event.target !== navToggle && !navToggle.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                }
            });
        }

        function reveal() {
            var reveals = document.querySelectorAll(".reveal");
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 100;
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add("active");
                }
            }
        }
        window.addEventListener("scroll", reveal);
        reveal();

        // New Tab Logic
        function switchTab(tabName) {
            // Hide all content
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));
            
            // Deactivate all buttons
            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            // Show selected content and activate button
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
            // Slight scroll adjustment to show content
            document.getElementById('ingredients-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    </script>
    <?php wp_footer(); ?>
</body>
</html>