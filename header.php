<?php
/**
 * The header template file
 *
 * @package Caremil
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
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
        /* --- COPY TỪ ĐÂY VÀO FILE CSS CỦA BẠN --- */
        
        /* Đẩy nội dung xuống để không bị header che mất */
        body { padding-top: 80px; } 

        /* Hiệu ứng Menu Mobile */
        #mobile-menu {
            transition: all 0.3s ease-in-out;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
        #mobile-menu.open {
            max-height: 400px;
            opacity: 1;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        /* Active Link Style */
        .nav-link {
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: #ffd166;
            transition: width 0.3s;
        }
        .nav-link:hover::after, .nav-link.active::after {
            width: 100%;
        }
        .footer-gradient-border {
            background: linear-gradient(to right, #4cc9f0, #ef476f, #ffd166);
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- === BẮT ĐẦU PHẦN HEADER === -->

    <!-- NAVIGATION BAR (Full Width, Sticky) -->
    <nav class="fixed w-full z-50 top-0 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm transition-all duration-300 h-20 flex items-center" id="navbar">
        <div class="container mx-auto px-4 md:px-6">
            <div class="flex justify-between items-center w-full">
                
                <!-- Logo -->
                <a href="/" class="flex items-center gap-2 group">
                    <!-- Icon Lá (Thay bằng ảnh logo thật nếu có) -->
                    <div class="w-10 h-10 bg-brand-soft rounded-full flex items-center justify-center text-brand-gold group-hover:rotate-12 transition-transform duration-300">
                        <i class="fas fa-leaf text-xl"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-2xl font-display font-black text-brand-navy leading-none tracking-tight">Care<span class="text-brand-blue">MIL</span></span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Plant Nutrition</span>
                    </div>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8 font-bold text-gray-500 text-base font-sans">
                    <a href="/" class="nav-link active text-brand-navy hover:text-brand-blue transition">Trang Chủ</a>
                    <a href="/cua-hang" class="nav-link hover:text-brand-blue transition">Sản Phẩm</a>
                    <a href="/cau-chuyen" class="nav-link hover:text-brand-blue transition">Câu Chuyện</a>
                    <a href="/lien-he" class="nav-link hover:text-brand-blue transition">Liên Hệ</a>
                </div>

                <!-- Action Buttons -->
                <div class="hidden md:flex items-center gap-4">
                    <!-- Search Icon -->
                    <button class="text-gray-400 hover:text-brand-blue transition">
                        <i class="fas fa-search text-lg"></i>
                    </button>
                    
                    <!-- Cart Icon -->
                    <a href="/gio-hang" class="relative text-gray-400 hover:text-brand-blue transition mr-2">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <span class="absolute -top-2 -right-2 bg-brand-pink text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">0</span>
                    </a>

                    <!-- Buy Button -->
                    <a href="/cua-hang" class="bg-brand-navy text-white font-bold py-2.5 px-6 rounded-full shadow-md hover:bg-brand-blue hover:shadow-lg transition transform hover:-translate-y-0.5 text-sm">
                        Mua Ngay
                    </a>
                </div>
                
                <!-- Mobile Menu Button (Hamburger) -->
                <button onclick="toggleMobileMenu()" class="md:hidden text-brand-navy text-2xl p-2 focus:outline-none transition">
                    <i class="fas fa-bars" id="menu-icon"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Dropdown (Full Width) -->
        <div id="mobile-menu" class="absolute top-20 left-0 w-full bg-white border-t border-gray-100 font-sans shadow-lg md:hidden">
            <div class="flex flex-col p-4 space-y-2">
                <a href="/" class="py-3 px-4 text-brand-navy bg-brand-soft/50 rounded-xl font-bold">Trang Chủ</a>
                <a href="/cua-hang" class="py-3 px-4 hover:bg-gray-50 rounded-xl font-bold text-gray-600">Sản Phẩm</a>
                <a href="/cau-chuyen" class="py-3 px-4 hover:bg-gray-50 rounded-xl font-bold text-gray-600">Câu Chuyện</a>
                <a href="/lien-he" class="py-3 px-4 hover:bg-gray-50 rounded-xl font-bold text-gray-600">Liên Hệ</a>
                <div class="h-px bg-gray-100 my-2"></div>
                <a href="/cua-hang" class="py-3 px-4 bg-brand-gold text-brand-navy rounded-xl font-bold text-center shadow-sm">
                    <i class="fas fa-shopping-cart mr-2"></i> Đặt Hàng Ngay
                </a>
            </div>
        </div>
    </nav>
    <!-- === KẾT THÚC PHẦN HEADER === -->

    <!-- Script điều khiển Mobile Menu -->
    <script>
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