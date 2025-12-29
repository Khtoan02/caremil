<?php
/**
 * The footer template file
 *
 * @package Caremil
 */
?>

<footer class="bg-white pt-12 lg:pt-16 pb-8 lg:pb-10 mt-10 relative" id="contact">
        <!-- Đường viền trang trí trên cùng -->
        <div class="absolute top-0 left-0 w-full h-2 footer-gradient-border"></div>

        <div class="container mx-auto px-6">
            
            <!-- MAIN FOOTER CONTENT -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 text-center lg:text-left">
                
                <!-- Cột 1: Brand & CSKH (4 phần) -->
                <div class="lg:col-span-4 space-y-6">
                <div class="flex items-center justify-center lg:justify-start gap-4">
                        <a href="https://caremil.dawnbridge.vn" class="text-2xl font-display font-black text-brand-navy tracking-tight flex items-center gap-2 hover:underline">
                            <i class="fas fa-leaf text-brand-gold"></i> Care<span class="text-brand-blue">MIL</span>
                        </a>
                        <span class="text-2xl font-display font-black text-gray-300">|</span>
                        <a href="https://dawnbridge.vn" class="text-2xl font-display font-black text-brand-navy tracking-tight flex items-center gap-2 hover:underline" target="_blank" rel="noopener">
                            <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Dawnbridge-logo-e1764735620422.png" alt="DawnBridge Logo" class="h-4 w-auto inline-block align-middle" />
                        </a>
                    </div>
                    
                    <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100 inline-block w-full text-left">
                        <p class="text-xs text-gray-500 uppercase font-bold mb-1 tracking-wider">Tổng đài Chăm Sóc Khách Hàng</p>
                        <a href="tel:+84985391881" class="text-2xl lg:text-3xl font-black text-brand-pink hover:text-brand-navy transition block mb-1">(+84) 985 39 18 81</a>
                        <a href="mailto:cskh@npfood.vn" class="text-sm text-brand-navy font-bold hover:text-brand-blue transition flex items-center gap-2">
                            <i class="fas fa-envelope"></i> cskh@npfood.vn
                        </a>
                    </div>

                    <div class="flex justify-center lg:justify-start space-x-3">
                        <a href="https://www.facebook.com/caremilvietnam/" target="_blank" class="w-10 h-10 rounded-full bg-brand-navy text-white flex items-center justify-center hover:bg-brand-blue transition transform hover:-translate-y-1"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://caremil.dawnbridge.vn" target="_blank" class="w-10 h-10 rounded-full bg-brand-navy text-white flex items-center justify-center hover:bg-brand-blue transition transform hover:-translate-y-1"><i class="fas fa-globe"></i></a>
                    </div>
                </div>

                <!-- Cột 2: Thông Tin Pháp Lý & Phân Phối (5 phần) -->
                <div class="lg:col-span-5 text-sm space-y-6 text-gray-600 text-left">
                    <!-- Nhà phân phối -->
                    <div>
                        <h4 class="font-bold text-base lg:text-lg mb-3 text-brand-navy font-display border-b border-gray-100 pb-2 inline-block">Nhập Khẩu & Phân Phối Tại Việt Nam</h4>
                        <p class="font-bold text-gray-800 uppercase mb-1">CÔNG TY TNHH NP FOOD (NP FOOD COMPANY LIMITED)</p>
                        <p class="mb-1"><i class="fas fa-map-marker-alt text-brand-gold mr-2 w-4"></i> Group 4, Quang Minh, Ha Noi, Vietnam.</p>
                        <p><i class="fas fa-id-card text-brand-gold mr-2 w-4"></i> Mã số thuế: 0109082378</p>
                    </div>

                    <!-- Nhà sản xuất -->
                    <div class="pt-2">
                        <h4 class="font-bold text-base lg:text-lg mb-3 text-brand-navy font-display border-b border-gray-100 pb-2 inline-block">Chủ Sở Hữu & Sản Xuất (Malaysia)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="font-bold text-gray-800 text-xs uppercase mb-1">Thương Hiệu</p>
                                <p class="font-bold text-brand-blue">DAWN BRIDGE SDN BHD</p>
                                <p class="text-xs text-gray-400 mt-1">Johor Bahru, Johor, Malaysia.</p>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-xs uppercase mb-1">Sản Xuất Bởi</p>
                                <p class="font-bold text-brand-navy">OMEGA HEALTH PRODUCTS</p>
                                <p class="text-xs text-gray-400 mt-1">Selangor, Malaysia.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cột 3: Chứng Nhận & Link (3 phần) -->
                <div class="lg:col-span-3 flex flex-col items-center lg:items-end gap-6">
                    <!-- Certifications -->
                    <div class="text-center lg:text-right w-full">
                        <h4 class="font-bold text-lg text-brand-navy font-display mb-4">Chứng Nhận Quốc Tế</h4>
                        <div class="flex justify-center lg:justify-end gap-3">
                            <!-- Halal -->
                            <div class="w-14 h-14 bg-white border-2 border-green-600 rounded-full flex flex-col items-center justify-center p-1 shadow-sm" title="Halal Certified">
                                <span class="text-[7px] font-bold text-green-700 leading-none">HALAL</span>
                                <span class="text-lg font-bold text-green-700 leading-none mt-1">حلال</span>
                            </div>
                            <!-- GMP -->
                            <div class="w-14 h-14 bg-white border-2 border-brand-gold rounded-full flex items-center justify-center shadow-sm">
                                <span class="font-bold text-brand-gold text-lg">GMP</span>
                            </div>
                            <!-- Origin -->
                            <div class="w-14 h-14 bg-white border-2 border-blue-800 rounded-full flex flex-col items-center justify-center shadow-sm p-1">
                                <span class="text-[8px] font-bold text-blue-900 leading-none uppercase">Made in</span>
                                <span class="text-[10px] font-bold text-blue-900 leading-none mt-1">Malaysia</span>
                            </div>
                        </div>
                    </div>

                    <!-- Links -->
                    <div class="text-center lg:text-right w-full">
                        <h4 class="font-bold text-brand-navy mb-2">Hỗ Trợ</h4>
                        <ul class="space-y-1 text-sm text-gray-500">
                            <li><a href="#" class="hover:text-brand-blue transition">Chính sách bảo mật</a></li>
                            <li><a href="#" class="hover:text-brand-blue transition">Điều khoản sử dụng</a></li>
                            <li><a href="#" class="hover:text-brand-blue transition">Chính sách đổi trả</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- 3. COPYRIGHT -->
            <div class="pt-8 text-center border-t border-gray-100 mt-10 pb-4">
                <p class="text-gray-400 text-xs lg:text-sm font-medium mb-2">
                    &copy; 2024 CareMIL Vietnam. All rights reserved.
                </p>
                <p class="text-brand-pink text-xs font-bold bg-pink-50 inline-block px-3 py-1 rounded-full border border-pink-100">
                    ⚠ Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.
                </p>
            </div>
        </div>
    </footer>
    <!-- === KẾT THÚC PHẦN FOOTER === -->

</body>
</html>













