<?php
/**
 * Navigation bar - MODERN PROFESSIONAL DESIGN
 */

// Session handled by parent template

// Normalize current path
$current_url = strtok( $_SERVER['REQUEST_URI'], '?' );
$current_url = rtrim( $current_url, '/' );
if ( empty( $current_url ) ) {
	$current_url = '/';
}

$is_caremil_landing = function_exists( 'is_page_template' ) && is_page_template( 'caremil-product.php' );

// Build menu
if ( $is_caremil_landing ) {
	$menu_items = array(
		array( 'url' => '#khoa-hoc', 'label' => 'Khoa Học' ),
		array( 'url' => '#loi-ich', 'label' => 'Lợi Ích' ),
		array( 'url' => '#bang-thanh-phan', 'label' => 'Thành Phần' ),
		array( 'url' => '#huong-dan', 'label' => 'Cách Dùng' ),
		array( 'url' => home_url( '/cua-hang' ), 'label' => 'Sản Phẩm' ),
		array( 'url' => home_url( '/lien-he' ), 'label' => 'Liên Hệ' ),
	);
} else {
	$menu_items = array(
		array( 'url' => home_url( '/' ), 'label' => 'Trang Chủ' ),
		array( 'url' => home_url( '/cua-hang' ), 'label' => 'Sản Phẩm' ),
		array( 'url' => home_url( '/cau-chuyen' ), 'label' => 'Câu Chuyện' ),
		array( 'url' => home_url( '/lien-he' ), 'label' => 'Liên Hệ' ),
	);
}
?>

<!-- MODERN PROFESSIONAL NAVBAR -->
<nav class="fixed w-full z-50 top-0 bg-white border-b border-gray-100 shadow-sm transition-all duration-300 h-18" id="navbar">
	<div class="container mx-auto px-4 lg:px-6 h-full">
		<div class="flex justify-between items-center h-full">

			<!-- Logo - Simple & Professional -->
			<div class="flex items-center">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-3 group">
					<div class="flex items-center gap-3">
						<!-- Simple Icon -->
						<div class="w-10 h-10 bg-primary-900 rounded-lg flex items-center justify-center group-hover:bg-accent-600 transition-all duration-300">
							<i class="fas fa-leaf text-white text-lg"></i>
						</div>
						<!-- Logo Text -->
						<div class="flex flex-col">
							<span class="text-xl lg:text-2xl font-bold text-primary-900 leading-none tracking-tight">
								DawnBridge
							</span>
							<span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider leading-none">
								Health & Wellness
							</span>
						</div>
					</div>
				</a>
			</div>

			<!-- Desktop Menu - Clean & Minimal -->
			<div class="hidden md:flex items-center space-x-8 text-sm font-medium text-gray-600">
				<?php
				foreach ( $menu_items as $item ) {
					$is_anchor  = strpos( $item['url'], '#' ) === 0;
					$item_path  = $is_anchor ? '' : rtrim( parse_url( $item['url'], PHP_URL_PATH ) ?? '', '/' );
					$item_path  = empty( $item_path ) ? '/' : $item_path;
					$is_active  = ! $is_anchor && ( $current_url === $item_path || ( '/' !== $item_path && strpos( $current_url, $item_path ) === 0 ) );
					$active_css = $is_active ? 'active text-primary-900' : 'hover:text-primary-900';

					echo '<a href="' . esc_url( $item['url'] ) . '" class="nav-link ' . esc_attr( $active_css ) . ' transition">' . esc_html( $item['label'] ) . '</a>';
				}
				?>
			</div>

			<!-- Action Buttons - Professional CTAs -->
			<div class="hidden md:flex items-center gap-3">
				<!-- Search -->
				<button class="text-gray-400 hover:text-accent-600 transition p-2" aria-label="Search">
					<i class="fas fa-search text-lg"></i>
				</button>

				<!-- Cart -->
				<a href="<?php echo esc_url( home_url( '/gio-hang' ) ); ?>" class="relative text-gray-400 hover:text-accent-600 transition p-2" id="header-cart-link">
					<i class="fas fa-shopping-cart text-lg"></i>
					<span class="absolute -top-1 -right-1 bg-accent-600 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center" id="header-cart-count">
						<?php echo esc_html( caremil_get_cart_count() ); ?>
					</span>
				</a>

				<!-- Login/Account -->
				<?php
				$is_logged_in = isset( $_SESSION['pancake_logged_in'] ) && true === $_SESSION['pancake_logged_in'];
				if ( $is_logged_in ) :
					?>
					<a href="<?php echo esc_url( home_url( '/tai-khoan-cua-toi' ) ); ?>" class="bg-primary-900 text-white font-medium py-2 px-5 rounded-lg hover:bg-accent-600 transition duration-300 text-sm">
						<i class="fas fa-user mr-2"></i>Tài khoản
					</a>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/dang-nhap' ) ); ?>" class="bg-primary-900 text-white font-medium py-2 px-5 rounded-lg hover:bg-accent-600 transition duration-300 text-sm">
						Đăng nhập
					</a>
				<?php endif; ?>
			</div>

			<!-- Mobile Menu Button -->
			<button onclick="toggleMobileMenu()" class="md:hidden text-primary-900 text-2xl p-2 focus:outline-none transition">
				<i class="fas fa-bars" id="menu-icon"></i>
			</button>
		</div>
	</div>

	<!-- Mobile Menu -->
	<div id="mobile-menu" class="absolute top-full left-0 w-full bg-white border-t border-gray-100 shadow-lg md:hidden">
		<div class="flex flex-col p-4 space-y-1">
			<?php
			foreach ( $menu_items as $item ) {
				$is_anchor = strpos( $item['url'], '#' ) === 0;
				$item_path = $is_anchor ? '' : rtrim( parse_url( $item['url'], PHP_URL_PATH ) ?? '', '/' );
				$item_path = empty( $item_path ) ? '/' : $item_path;
				$is_active = ! $is_anchor && ( $current_url === $item_path || ( '/' !== $item_path && strpos( $current_url, $item_path ) === 0 ) );

				$mobile_class = $is_active
					? 'py-3 px-4 text-primary-900 bg-gray-50 rounded-lg font-medium'
					: 'py-3 px-4 hover:bg-gray-50 rounded-lg font-medium text-gray-600';

				echo '<a href="' . esc_url( $item['url'] ) . '" class="' . esc_attr( $mobile_class ) . '">' . esc_html( $item['label'] ) . '</a>';
			}
			?>

			<div class="h-px bg-gray-100 my-2"></div>

			<!-- Mobile Actions -->
			<a href="<?php echo esc_url( home_url( '/gio-hang' ) ); ?>" class="py-3 px-4 bg-gray-100 text-primary-900 rounded-lg font-medium text-center relative flex items-center justify-center gap-2">
				<i class="fas fa-shopping-cart"></i> Giỏ Hàng
				<?php if ( caremil_get_cart_count() > 0 ) : ?>
					<span class="bg-accent-600 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center">
						<?php echo esc_html( caremil_get_cart_count() ); ?>
					</span>
				<?php endif; ?>
			</a>

			<?php if ( $is_logged_in ) : ?>
				<a href="<?php echo esc_url( home_url( '/tai-khoan-cua-toi' ) ); ?>" class="py-3 px-4 bg-primary-900 text-white rounded-lg font-medium text-center">
					<i class="fas fa-user mr-2"></i>Tài khoản
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/dang-nhap' ) ); ?>" class="py-3 px-4 bg-primary-900 text-white rounded-lg font-medium text-center">
					Đăng nhập
				</a>
			<?php endif; ?>
		</div>
	</div>
</nav>

<!-- Navigation Scripts -->
<script>
	function toggleMobileMenu() {
		const menu = document.getElementById('mobile-menu');
		const icon = document.getElementById('menu-icon');

		if (!menu || !icon) return;

		menu.classList.toggle('open');

		if (menu.classList.contains('open')) {
			icon.classList.remove('fa-bars');
			icon.classList.add('fa-times');
		} else {
			icon.classList.remove('fa-times');
			icon.classList.add('fa-bars');
		}
	}

	document.addEventListener('DOMContentLoaded', function() {
		const cartCountEl = document.getElementById('header-cart-count');
		if (cartCountEl) {
			const count = parseInt(cartCountEl.textContent, 10) || 0;
			cartCountEl.style.display = count > 0 ? 'flex' : 'none';
		}
	});
</script>
