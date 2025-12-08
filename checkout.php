<?php
/**
 * Template Name: Checkout
 * Template Post Type: page
 * Description: Template for displaying checkout page
 *
 * @package Caremil
 */
get_header();
?>
<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - CareMIL</title>
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
        /* Stepper Styles */
        .step.active .step-circle { background-color: #1a4f8a; color: white; border-color: #1a4f8a; }
        .step.completed .step-circle { background-color: #4ade80; color: white; border-color: #4ade80; }
        .step.active .step-text { color: #1a4f8a; font-weight: 700; }

        /* Form Styles */
        .form-label { font-size: 0.85rem; font-weight: 700; color: #1a4f8a; margin-bottom: 0.5rem; display: block; }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            background-color: #f8fafc;
            transition: all 0.3s ease;
            outline: none;
            font-weight: 600;
            color: #334155;
        }
        .form-input:focus {
            border-color: #4cc9f0;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(76, 201, 240, 0.1);
        }
        .form-input::placeholder { color: #94a3b8; font-weight: 500; }

        /* Payment Method Radio */
        .payment-radio:checked + div {
            border-color: #1a4f8a;
            background-color: #f0f9ff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .payment-radio:checked + div .check-icon { opacity: 1; transform: scale(1); }
        
        /* Disabled Payment */
        .payment-disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background-color: #f9fafb;
        }
        .payment-disabled:hover { border-color: #e2e8f0; }

        /* Order Button */
        .order-btn {
            background: linear-gradient(135deg, #ef476f 0%, #ff758c 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .order-btn:hover {
            box-shadow: 0 15px 30px -5px rgba(239, 71, 111, 0.5);
            transform: translateY(-2px) scale(1.02);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-700 font-sans pb-24 pt-20">

    <!-- HEADER (Checkout Mode) -->
    <nav class="fixed w-full z-50 top-0 bg-white border-b border-gray-100 h-16 flex items-center shadow-sm">
        <div class="container mx-auto px-4 flex justify-between items-center max-w-6xl">
            <!-- Logo -->
            <a href="caremil_landing_page.html" class="flex items-center gap-2 group">
                <i class="fas fa-leaf text-brand-gold text-xl group-hover:rotate-12 transition-transform"></i>
                <span class="text-xl font-display font-black text-brand-navy tracking-tight">Care<span class="text-brand-blue">MIL</span></span>
            </a>
            
            <!-- Progress Stepper -->
            <div class="flex items-center gap-2 md:gap-4 lg:gap-8">
                <!-- Step 1: Cart (Completed) -->
                <div class="step completed flex items-center gap-2 hidden sm:flex">
                    <div class="step-circle w-6 h-6 md:w-8 md:h-8 rounded-full border-2 flex items-center justify-center font-bold text-xs md:text-sm"><i class="fas fa-check"></i></div>
                    <span class="step-text text-xs md:text-sm font-bold text-green-500">Giỏ Hàng</span>
                </div>
                <div class="w-8 md:w-12 h-0.5 bg-green-500 hidden sm:block"></div>
                
                <!-- Step 2: Info & Payment (Active) -->
                <div class="step active flex items-center gap-2">
                    <div class="step-circle w-6 h-6 md:w-8 md:h-8 rounded-full border-2 flex items-center justify-center font-bold text-xs md:text-sm">2</div>
                    <span class="step-text text-xs md:text-sm">Thông Tin & Thanh Toán</span>
                </div>
                <div class="w-8 md:w-12 h-0.5 bg-gray-200 hidden sm:block"></div>

                <!-- Step 3: Done -->
                <div class="step flex items-center gap-2 opacity-40 hidden sm:flex">
                    <div class="step-circle w-6 h-6 md:w-8 md:h-8 rounded-full border-2 border-gray-300 flex items-center justify-center font-bold text-xs md:text-sm">3</div>
                    <span class="step-text text-xs md:text-sm">Hoàn Tất</span>
                </div>
            </div>

            <!-- Secure Badge -->
            <div class="text-green-600 flex items-center gap-1 text-xs font-bold bg-green-50 px-3 py-1 rounded-full border border-green-100">
                <i class="fas fa-lock"></i> <span class="hidden sm:inline">Bảo Mật 100%</span>
            </div>
        </div>
    </nav>

    <!-- MAIN CHECKOUT SECTION -->
    <div class="container mx-auto px-4 max-w-6xl mt-8">
        <form id="checkout-form" onsubmit="event.preventDefault(); submitOrder();">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
                
                <!-- LEFT COLUMN: INFORMATION & PAYMENT -->
                <div class="lg:w-2/3 space-y-8">
                    
                    <!-- 1. Shipping Information -->
                    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-brand-navy"></div>
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-display font-bold text-brand-navy flex items-center gap-2">
                                <span class="bg-brand-navy text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                                Thông Tin Giao Hàng
                            </h2>
                            <!-- Address Book Trigger -->
                            <div class="relative group">
                                <select class="form-input py-2 pl-3 pr-8 text-sm border-brand-blue/30 bg-blue-50/50 cursor-pointer text-brand-navy hover:bg-blue-50 transition w-auto" onchange="fillAddress(this.value)">
                                    <option value="" disabled selected>📂 Chọn từ Sổ Địa Chỉ</option>
                                    <option value="home">🏠 Nhà riêng (Mặc định)</option>
                                    <option value="office">🏢 Văn phòng công ty</option>
                                    <option value="parents">👴 Nhà ông bà</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="form-label">Họ và tên người nhận <span class="text-red-500">*</span></label>
                                <input type="text" id="fullname" class="form-input" placeholder="Ví dụ: Nguyễn Văn A" required>
                            </div>
                            <div>
                                <label class="form-label">Số điện thoại <span class="text-red-500">*</span></label>
                                <input type="tel" id="phone" class="form-input" placeholder="Ví dụ: 0912345678" required>
                            </div>
                            <div>
                                <label class="form-label">Email (Nhận hóa đơn)</label>
                                <input type="email" id="email" class="form-input" placeholder="example@email.com">
                            </div>
                            
                            <!-- Address Group -->
                            <div>
                                <label class="form-label">Tỉnh / Thành phố <span class="text-red-500">*</span></label>
                                <select id="city" class="form-input appearance-none cursor-pointer">
                                    <option value="" disabled selected>Chọn Tỉnh/Thành</option>
                                    <option value="hn">Hà Nội</option>
                                    <option value="hcm">Hồ Chí Minh</option>
                                    <option value="dn">Đà Nẵng</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Quận / Huyện <span class="text-red-500">*</span></label>
                                <select id="district" class="form-input appearance-none cursor-pointer">
                                    <option value="" disabled selected>Chọn Quận/Huyện</option>
                                    <option value="q1">Quận 1</option>
                                    <option value="q3">Quận 3</option>
                                    <option value="qbt">Quận Bình Thạnh</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="form-label">Địa chỉ chi tiết <span class="text-red-500">*</span></label>
                                <input type="text" id="address" class="form-input" placeholder="Số nhà, tên đường, phường/xã" required>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="form-label">Ghi chú giao hàng</label>
                                <textarea class="form-input h-24 resize-none" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Payment Method -->
                    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-brand-blue"></div>
                        <h2 class="text-xl font-display font-bold text-brand-navy mb-6 flex items-center gap-2">
                            <span class="bg-brand-blue text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                            Phương Thức Thanh Toán
                        </h2>

                        <div class="space-y-4">
                            
                            <!-- 1. QR Code Transfer - RECOMMENDED & DEFAULT -->
                            <label class="block cursor-pointer relative group">
                                <input type="radio" name="payment" value="bank" class="payment-radio sr-only" checked>
                                <div class="p-5 rounded-2xl border-2 border-gray-200 flex items-center gap-4 hover:border-brand-blue transition bg-white relative overflow-hidden">
                                    <!-- Recommended Badge -->
                                    <div class="absolute -right-8 top-4 bg-brand-pink text-white text-[10px] font-bold px-8 py-1 rotate-45 shadow-sm">Khuyên Dùng</div>
                                    
                                    <div class="w-12 h-12 bg-blue-100 text-brand-blue rounded-full flex items-center justify-center text-2xl flex-shrink-0">
                                        <i class="fas fa-qrcode"></i>
                                    </div>
                                    <div class="flex-grow pr-10">
                                        <h4 class="font-bold text-brand-navy text-lg">Chuyển khoản QR Code</h4>
                                        <p class="text-xs text-gray-500">Quét mã QR ngân hàng, xác nhận nhanh chóng.</p>
                                        <div class="flex gap-2 mt-2">
                                            <span class="text-[10px] bg-blue-50 text-brand-blue px-2 py-0.5 rounded border border-blue-100">Nhanh & An toàn</span>
                                        </div>
                                    </div>
                                    <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                        <div class="w-3 h-3 rounded-full bg-brand-navy check-icon opacity-0 transition-all transform scale-0"></div>
                                    </div>
                                </div>
                            </label>

                            <!-- 2. COD -->
                            <label class="block cursor-pointer relative group">
                                <input type="radio" name="payment" value="cod" class="payment-radio sr-only">
                                <div class="p-5 rounded-2xl border-2 border-gray-200 flex items-center gap-4 hover:border-brand-blue transition bg-white">
                                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-2xl flex-shrink-0">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="flex-grow">
                                        <h4 class="font-bold text-brand-navy text-lg">Thanh toán khi nhận hàng (COD)</h4>
                                        <p class="text-xs text-gray-500">Thanh toán tiền mặt cho shipper khi nhận được hàng.</p>
                                    </div>
                                    <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                        <div class="w-3 h-3 rounded-full bg-brand-navy check-icon opacity-0 transition-all transform scale-0"></div>
                                    </div>
                                </div>
                            </label>

                            <!-- 3. Other Methods (Disabled / Coming Soon) -->
                            <div class="relative">
                                <div class="p-5 rounded-2xl border-2 border-gray-100 flex flex-col gap-4 bg-gray-50 opacity-70 cursor-not-allowed">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold text-gray-500 text-sm uppercase tracking-wide flex items-center gap-2">
                                            <i class="fas fa-tools"></i> Các phương thức đang cập nhật
                                        </h4>
                                        <span class="bg-gray-200 text-gray-500 text-[10px] font-bold px-2 py-1 rounded">Bảo trì</span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <!-- VietQR (Added here) -->
                                        <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200 grayscale opacity-60">
                                            <img src="https://img.icons8.com/color/48/vietqr.png" class="w-8 h-8 object-contain rounded" alt="VietQR">
                                            <div>
                                                <p class="font-bold text-sm text-gray-600">VietQR</p>
                                                <p class="text-[10px] text-gray-400">Đang bảo trì</p>
                                            </div>
                                        </div>

                                        <!-- E-Wallets -->
                                        <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200 grayscale opacity-60">
                                            <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" class="w-8 h-8 object-contain rounded" alt="Momo">
                                            <div>
                                                <p class="font-bold text-sm text-gray-600">Ví MoMo</p>
                                                <p class="text-[10px] text-gray-400">Đang bảo trì</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200 grayscale opacity-60">
                                            <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-ZaloPay-Square.png" class="w-8 h-8 object-contain rounded" alt="ZaloPay">
                                            <div>
                                                <p class="font-bold text-sm text-gray-600">ZaloPay</p>
                                                <p class="text-[10px] text-gray-400">Đang bảo trì</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Cards -->
                                        <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200 grayscale opacity-60">
                                            <div class="flex gap-1">
                                                <i class="fab fa-cc-visa text-2xl text-gray-400"></i>
                                                <i class="fab fa-cc-mastercard text-2xl text-gray-400"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-sm text-gray-600">Thẻ Visa/Master</p>
                                                <p class="text-[10px] text-gray-400">Sắp ra mắt</p>
                                            </div>
                                        </div>

                                        <!-- Mobile Pay (Apple/Samsung/Google) -->
                                        <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200 grayscale opacity-60 md:col-span-2">
                                            <div class="flex gap-3 text-xl text-gray-400">
                                                <i class="fab fa-apple"></i>
                                                <i class="fab fa-google"></i>
                                                <span class="font-bold text-xs border border-gray-300 px-1 rounded">Pay</span> <!-- Samsung Pay icon placeholder -->
                                            </div>
                                            <div>
                                                <p class="font-bold text-sm text-gray-600">Apple / Samsung / Google Pay</p>
                                                <p class="text-[10px] text-gray-400">Sắp ra mắt</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: ORDER SUMMARY (Sticky) -->
                <div class="lg:w-1/3">
                    <div class="sticky top-24 bg-white rounded-3xl p-6 md:p-8 shadow-card border border-gray-100">
                        <h3 class="text-lg font-bold text-brand-navy mb-6 pb-4 border-b border-gray-100 flex justify-between items-center">
                            Đơn Hàng (2)
                            <a href="caremil_cart_page.html" class="text-xs text-brand-blue hover:underline font-normal">Sửa</a>
                        </h3>

                        <!-- Mini Cart List -->
                        <div class="space-y-4 mb-6 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                            <!-- Item 1 -->
                            <div class="flex gap-3">
                                <div class="w-16 h-16 bg-gray-50 rounded-lg p-1 border border-gray-200 flex-shrink-0 relative">
                                    <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-chuan.png" class="w-full h-full object-contain">
                                    <span class="absolute -top-2 -right-2 bg-gray-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border border-white">1</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-brand-navy line-clamp-2">CareMIL Hộp Lớn 800g</p>
                                    <p class="text-xs text-gray-500 mb-1">Hộp thiếc</p>
                                    <p class="text-sm font-bold text-gray-700">850.000đ</p>
                                </div>
                            </div>
                            
                            <!-- Item 2 -->
                            <div class="flex gap-3">
                                <div class="w-16 h-16 bg-gray-50 rounded-lg p-1 border border-gray-200 flex-shrink-0 relative">
                                    <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Goi-sua.png" class="w-full h-full object-contain rotate-6">
                                    <span class="absolute -top-2 -right-2 bg-gray-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border border-white">2</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-brand-navy line-clamp-2">Hộp 10 Gói Tiện Lợi</p>
                                    <p class="text-xs text-gray-500 mb-1">Gói 36g</p>
                                    <p class="text-sm font-bold text-gray-700">700.000đ</p>
                                </div>
                            </div>
                        </div>

                        <!-- Calculations -->
                        <div class="space-y-3 text-sm border-t border-dashed border-gray-200 pt-4 mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>Tạm tính</span>
                                <span>1.550.000đ</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Giảm giá</span>
                                <span class="text-green-500">-0đ</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Phí vận chuyển</span>
                                <span class="font-bold text-green-500">Miễn phí</span>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="flex justify-between items-center mb-8 pt-4 border-t border-gray-100">
                            <span class="text-base font-bold text-brand-navy">Tổng thanh toán</span>
                            <div class="text-right">
                                <span class="text-2xl font-black text-brand-pink block leading-none">1.550.000đ</span>
                                <span class="text-[10px] text-gray-400">(VAT included)</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="order-btn w-full text-white font-bold py-4 rounded-xl shadow-lg flex items-center justify-center gap-2 text-lg uppercase tracking-wide group">
                            <span>Đặt Hàng Ngay</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>

                        <p class="text-center text-xs text-gray-400 mt-4 px-4">
                            Bằng việc đặt hàng, bạn đồng ý với <a href="#" class="underline hover:text-brand-blue">điều khoản sử dụng</a> của CareMIL.
                        </p>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-[30px] shadow-2xl max-w-md w-full p-8 text-center relative border-4 border-green-100 transform scale-90 transition-all duration-300" id="success-content">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-4xl text-green-500 animate-bounce"></i>
            </div>
            <h3 class="text-2xl font-display font-black text-brand-navy mb-2">Đặt Hàng Thành Công!</h3>
            <p class="text-gray-600 mb-8">Cảm ơn bạn đã tin chọn CareMIL. Chúng tôi sẽ liên hệ xác nhận đơn hàng trong giây lát.</p>
            <button onclick="window.location.href='caremil_landing_page.html'" class="w-full bg-brand-navy text-white font-bold py-3 rounded-xl hover:bg-brand-blue transition shadow-lg">
                Về Trang Chủ
            </button>
        </div>
    </div>

    <script>
        // MOCK ADDRESS BOOK DATA
        const addresses = {
            home: {
                fullname: 'Nguyễn Văn A',
                phone: '0912345678',
                email: 'nguyenvana@gmail.com',
                city: 'hn',
                district: 'q1',
                address: '123 Đường Láng, Đống Đa'
            },
            office: {
                fullname: 'Nguyễn Văn A (Công ty)',
                phone: '0912345678',
                email: 'work@email.com',
                city: 'hcm',
                district: 'qbt',
                address: 'Tòa nhà Landmark 81, Bình Thạnh'
            },
            parents: {
                fullname: 'Ông Bà B',
                phone: '0987654321',
                email: '',
                city: 'dn',
                district: 'q3',
                address: '456 Lê Duẩn, Hải Châu'
            }
        };

        function fillAddress(key) {
            if (!key || !addresses[key]) return;
            const data = addresses[key];
            
            document.getElementById('fullname').value = data.fullname;
            document.getElementById('phone').value = data.phone;
            document.getElementById('email').value = data.email;
            document.getElementById('address').value = data.address;
            document.getElementById('city').value = data.city;
            // Trigger visual update if custom select used, or just native
            // In real app, district needs to load based on city first
            document.getElementById('district').value = data.district; 
        }

        function submitOrder() {
            const btn = document.querySelector('.order-btn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
            btn.disabled = true;

            setTimeout(() => {
                document.getElementById('success-modal').classList.remove('hidden');
                document.getElementById('success-modal').classList.add('flex');
                setTimeout(() => {
                    document.getElementById('success-content').classList.remove('scale-90');
                    document.getElementById('success-content').classList.add('scale-100');
                }, 10);
            }, 1500);
        }
    </script>
<?php
get_footer();