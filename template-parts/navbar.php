<?php
/**
 * Shared navigation bar for Caremil theme.
 */

if ( ! session_id() ) {
	session_start();
}

// Normalize current path (strip query + trailing slash).
$current_url = strtok( $_SERVER['REQUEST_URI'], '?' );
$current_url = rtrim( $current_url, '/' );
if ( empty( $current_url ) ) {
	$current_url = '/';
}

$is_caremil_landing = function_exists( 'is_page_template' ) && is_page_template( 'caremil-product.php' );

// Build menu set per context.
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

$nav_outer_class = $is_caremil_landing
	? 'fixed w-full z-50 transition-all duration-300 py-2 md:py-3 top-1'
	: 'fixed w-full z-50 top-0 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm transition-all duration-300 h-20 flex items-center';

$nav_inner_class = $is_caremil_landing
	? 'bg-white/95 backdrop-blur-md rounded-2xl md:rounded-full shadow-soft px-4 py-2 md:px-6 md:py-3 border border-white flex justify-between items-center'
	: 'flex justify-between items-center w-full';
?>

<nav class="<?php echo esc_attr( $nav_outer_class ); ?>" id="navbar">
	<div class="container mx-auto px-4 md:px-6">
		<div class="<?php echo esc_attr( $nav_inner_class ); ?>">

			<!-- Logo -->
			<div class="flex items-center gap-3">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-2 group">
					<div class="w-10 h-10 bg-brand-soft rounded-full flex items-center justify-center text-brand-gold group-hover:rotate-12 transition-transform duration-300">
						<i class="fas fa-leaf text-xl"></i>
					</div>
					<div class="flex flex-col">
						<span class="text-2xl font-display font-black text-brand-navy leading-none tracking-tight">Care<span class="text-brand-blue">MIL</span></span>
						<span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Plant Nutrition</span>
					</div>
				</a>

				<?php if ( $is_caremil_landing ) : ?>
					<div class="hidden lg:flex items-center gap-2 pl-4 ml-2 border-l border-gray-100">
						<span class="text-gray-300">|</span>
						<a href="https://dawnbridge.vn" target="_blank" rel="noopener" class="text-lg font-display font-black text-brand-navy tracking-tight flex items-center gap-2 hover:underline">
							<img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Dawnbridge-logo-e1764735620422.png" alt="DawnBridge" class="h-5 w-auto" />
						</a>
					</div>
				<?php endif; ?>
			</div>

			<!-- Desktop Menu -->
			<div class="hidden md:flex items-center space-x-8 font-bold text-gray-500 text-base font-sans">
				<?php
				foreach ( $menu_items as $item ) {
					$is_anchor  = strpos( $item['url'], '#' ) === 0;
					$item_path  = $is_anchor ? '' : rtrim( parse_url( $item['url'], PHP_URL_PATH ) ?? '', '/' );
					$item_path  = empty( $item_path ) ? '/' : $item_path;
					$is_active  = ! $is_anchor && ( $current_url === $item_path || ( '/' !== $item_path && strpos( $current_url, $item_path ) === 0 ) );
					$active_css = $is_active ? 'active text-brand-navy' : '';

					echo '<a href="' . esc_url( $item['url'] ) . '" class="nav-link ' . esc_attr( $active_css ) . ' hover:text-brand-blue transition">' . esc_html( $item['label'] ) . '</a>';
				}
				?>
			</div>

			<!-- Action Buttons -->
			<div class="hidden md:flex items-center gap-4">
				<?php if ( $is_caremil_landing ) : ?>
					<button type="button" class="bg-brand-gold text-brand-navy font-bold py-2.5 px-5 rounded-full shadow-md hover:bg-brand-blue hover:text-white transition transform hover:-translate-y-0.5 text-sm" onclick="if (typeof openTrialModal === 'function') { openTrialModal(); } else { window.location.hash = '#loi-ich'; }">
						Nhận quà
					</button>
				<?php endif; ?>

				<button class="text-gray-400 hover:text-brand-blue transition" aria-label="Search">
					<i class="fas fa-search text-lg"></i>
				</button>

				<a href="<?php echo esc_url( home_url( '/gio-hang' ) ); ?>" class="relative text-gray-400 hover:text-brand-blue transition mr-2" id="header-cart-link">
					<i class="fas fa-shopping-cart text-lg"></i>
					<span class="absolute -top-2 -right-2 bg-brand-pink text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center" id="header-cart-count"><?php echo esc_html( caremil_get_cart_count() ); ?></span>
				</a>

				<?php
				$is_logged_in = isset( $_SESSION['pancake_logged_in'] ) && true === $_SESSION['pancake_logged_in'];
				if ( $is_logged_in ) :
					?>
					<a href="<?php echo esc_url( home_url( '/tai-khoan-cua-toi' ) ); ?>" class="bg-brand-navy text-white font-bold py-2.5 px-6 rounded-full shadow-md hover:bg-brand-blue hover:shadow-lg transition transform hover:-translate-y-0.5 text-sm">
						Tài khoản
					</a>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/dang-nhap' ) ); ?>" class="bg-brand-navy text-white font-bold py-2.5 px-6 rounded-full shadow-md hover:bg-brand-blue hover:shadow-lg transition transform hover:-translate-y-0.5 text-sm">
						Đăng nhập
					</a>
				<?php endif; ?>
			</div>

			<!-- Mobile Menu Button -->
			<button onclick="toggleMobileMenu()" class="md:hidden text-brand-navy text-2xl p-2 focus:outline-none transition">
				<i class="fas fa-bars" id="menu-icon"></i>
			</button>
		</div>
	</div>

	<!-- Mobile Menu -->
	<div id="mobile-menu" class="absolute top-20 left-0 w-full bg-white border-t border-gray-100 font-sans shadow-lg md:hidden">
		<div class="flex flex-col p-4 space-y-2">
			<?php
			foreach ( $menu_items as $item ) {
				$is_anchor = strpos( $item['url'], '#' ) === 0;
				$item_path = $is_anchor ? '' : rtrim( parse_url( $item['url'], PHP_URL_PATH ) ?? '', '/' );
				$item_path = empty( $item_path ) ? '/' : $item_path;
				$is_active = ! $is_anchor && ( $current_url === $item_path || ( '/' !== $item_path && strpos( $current_url, $item_path ) === 0 ) );

				$mobile_class = $is_active
					? 'py-3 px-4 text-brand-navy bg-brand-soft/50 rounded-xl font-bold'
					: 'py-3 px-4 hover:bg-gray-50 rounded-xl font-bold text-gray-600';

				echo '<a href="' . esc_url( $item['url'] ) . '" class="' . esc_attr( $mobile_class ) . '">' . esc_html( $item['label'] ) . '</a>';
			}
			?>

			<div class="h-px bg-gray-100 my-2"></div>

			<?php if ( $is_caremil_landing ) : ?>
				<button type="button" onclick="toggleMobileMenu(); if (typeof openTrialModal === 'function') { openTrialModal(); } else { window.location.hash = '#loi-ich'; }" class="py-3 px-4 bg-brand-gold text-brand-navy rounded-xl font-bold text-center shadow-sm">
					<i class="fas fa-gift mr-2"></i> Nhận quà
				</button>
			<?php endif; ?>

			<a href="<?php echo esc_url( home_url( '/cua-hang' ) ); ?>" class="py-3 px-4 bg-brand-gold text-brand-navy rounded-xl font-bold text-center shadow-sm">
				<i class="fas fa-shopping-cart mr-2"></i> Đặt Hàng Ngay
			</a>
			<a href="<?php echo esc_url( home_url( '/gio-hang' ) ); ?>" class="py-3 px-4 bg-brand-navy text-white rounded-xl font-bold text-center shadow-sm relative">
				<i class="fas fa-shopping-bag mr-2"></i> Giỏ Hàng
				<?php if ( caremil_get_cart_count() > 0 ) : ?>
					<span class="absolute -top-1 -right-1 bg-brand-pink text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center"><?php echo esc_html( caremil_get_cart_count() ); ?></span>
				<?php endif; ?>
			</a>
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

