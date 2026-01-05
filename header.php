<?php
/**
 * The header template file - MODERN PROFESSIONAL DESIGN
 *
 * @package Dawnbridge
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Modern Professional Color Palette
                        primary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        },
                        accent: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        success: {
                            500: '#10b981',
                            600: '#059669',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft': '0 2px 15px rgba(0, 0, 0, 0.05)',
                        'card': '0 1px 3px rgba(0, 0, 0, 0.05)',
                    },
                }
            }
        }
    </script>
    <style>
        /* Modern Professional Styles */
        body { 
            padding-top: 72px;
            font-family: 'Inter', sans-serif;
        }

        /* Smooth nav transition */
        nav {
            transition: all 0.3s ease;
        }

        /* Mobile menu animation */
        #mobile-menu {
            transition: all 0.3s ease-in-out;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
        #mobile-menu.open {
            max-height: 500px;
            opacity: 1;
        }

        /* Nav link hover effect - Modern underline */
        .nav-link {
            position: relative;
            transition: color 0.2s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #3b82f6;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
        .nav-link.active {
            color: #1e293b;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- === MODERN PROFESSIONAL HEADER === -->
    <?php get_template_part( 'template-parts/navbar' ); ?>
    <!-- === END HEADER === -->