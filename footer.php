<?php
/**
 * The footer template file - MODERN PROFESSIONAL DESIGN
 *
 * @package Dawnbridge
 */
?>

<footer class="bg-white border-t border-gray-100 pt-12 pb-6 mt-16" id="contact">
	<div class="container mx-auto px-6 lg:px-8">
		
		<!-- MAIN FOOTER CONTENT -->
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
			
			<!-- Column 1: Brand & Contact -->
			<div class="lg:col-span-1 space-y-4">
				<!-- Logo -->
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center gap-3 group">
					<div class="w-10 h-10 bg-primary-900 rounded-lg flex items-center justify-center">
						<i class="fas fa-leaf text-white text-lg"></i>
					</div>
					<div class="flex flex-col">
						<span class="text-xl font-bold text-primary-900 leading-none">DawnBridge</span>
						<span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Health & Wellness</span>
					</div>
				</a>

				<!-- Tagline -->
				<p class="text-sm text-gray-600 leading-relaxed">
					Cung cấp các sản phẩm dinh dưỡng chất lượng cao, đáng tin cậy cho sức khỏe của bạn.
				</p>

				<!-- Contact -->
				<div class="space-y-2 text-sm">
					<a href="tel:+84985391881" class="flex items-center gap-2 text-gray-700 hover:text-accent-600 transition">
						<i class="fas fa-phone text-accent-600"></i>
						<span class="font-medium">(+84) 985 39 18 81</span>
					</a>
					<a href="mailto:cskh@npfood.vn" class="flex items-center gap-2 text-gray-700 hover:text-accent-600 transition">
						<i class="fas fa-envelope text-accent-600"></i>
						<span>cskh@npfood.vn</span>
					</a>
				</div>

				<!-- Social Media -->
				<div class="flex items-center gap-3 pt-2">
					<a href="https://www.facebook.com/caremilvietnam/" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-accent-600 hover:text-white transition">
						<i class="fab fa-facebook-f"></i>
					</a>
					<a href="https://caremil.dawnbridge.vn" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-accent-600 hover:text-white transition">
						<i class="fas fa-globe"></i>
					</a>
				</div>
			</div>

			<!-- Column 2: Quick Links -->
			<div class="space-y-4">
				<h4 class="text-sm font-bold text-primary-900 uppercase tracking-wider">Liên kết nhanh</h4>
				<ul class="space-y-2 text-sm">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-gray-600 hover:text-accent-600 transition">Trang chủ</a></li>
					<li><a href="<?php echo esc_url( home_url( '/cua-hang' ) ); ?>" class="text-gray-600 hover:text-accent-600 transition">Sản phẩm</a></li>
					<li><a href="<?php echo esc_url( home_url( '/cau-chuyen' ) ); ?>" class="text-gray-600 hover:text-accent-600 transition">Câu chuyện</a></li>
					<li><a href="<?php echo esc_url( home_url( '/lien-he' ) ); ?>" class="text-gray-600 hover:text-accent-600 transition">Liên hệ</a></li>
				</ul>
			</div>

			<!-- Column 3: Legal -->
			<div class="space-y-4">
				<h4 class="text-sm font-bold text-primary-900 uppercase tracking-wider">Hỗ trợ</h4>
				<ul class="space-y-2 text-sm">
					<li><a href="#" class="text-gray-600 hover:text-accent-600 transition">Chính sách bảo mật</a></li>
					<li><a href="#" class="text-gray-600 hover:text-accent-600 transition">Điều khoản sử dụng</a></li>
					<li><a href="#" class="text-gray-600 hover:text-accent-600 transition">Chính sách đổi trả</a></li>
					<li><a href="#" class="text-gray-600 hover:text-accent-600 transition">Hướng dẫn mua hàng</a></li>
				</ul>
			</div>

			<!-- Column 4: Company Info -->
			<div class="space-y-4">
				<h4 class="text-sm font-bold text-primary-900 uppercase tracking-wider">Công ty</h4>
				<div class="text-sm text-gray-600 space-y-2">
					<p class="font-semibold text-gray-800">CÔNG TY TNHH NP FOOD</p>
					<p class="flex items-start gap-2">
						<i class="fas fa-map-marker-alt text-accent-600 mt-1"></i>
						<span>Group 4, Quang Minh, Ha Noi, Vietnam</span>
					</p>
					<p class="flex items-center gap-2">
						<i class="fas fa-id-card text-accent-600"></i>
						<span>MST: 0109082378</span>
					</p>
				</div>

				<!-- Certifications -->
				<div class="flex items-center gap-2 pt-2">
					<div class="w-10 h-10 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center" title="Halal Certified">
						<span class="text-xs font-bold text-green-700">حلال</span>
					</div>
					<div class="w-10 h-10 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center" title="GMP">
						<span class="text-xs font-bold text-blue-700">GMP</span>
					</div>
					<div class="w-10 h-10 bg-gray-50 border border-gray-200 rounded-lg flex flex-col items-center justify-center text-[8px] font-bold text-gray-700">
						<span>Made in</span>
						<span>Malaysia</span>
					</div>
				</div>
			</div>
		</div>

		<!-- Footer Bottom - Copyright -->
		<div class="border-t border-gray-100 mt-10 pt-6">
			<div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500">
				<p>&copy; 2024 DawnBridge Vietnam. All rights reserved.</p>
				<p class="bg-yellow-50 text-yellow-700 px-3 py-1 rounded-lg text-xs font-medium border border-yellow-200">
					⚠ Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.
				</p>
			</div>
		</div>
	</div>
</footer>

</body>
</html>
