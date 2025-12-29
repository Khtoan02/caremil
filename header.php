<?php
/**
 * The header template file
 *
 * @package Caremil
 */

// Khởi động session để kiểm tra trạng thái đăng nhập
if (!session_id()) {
    session_start();
}
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
    <?php get_template_part( 'template-parts/navbar' ); ?>
    <!-- === KẾT THÚC PHẦN HEADER === -->