<?php
/**
 * Front Page Template - MODERN ECOMMERCE HOMEPAGE
 * This replaces the CareMIL landing page
 *
 * @package Dawnbridge
 */

get_header();
?>

<!-- HERO SECTION - Professional & Clean -->
<section class="relative bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 text-white py-20 lg:py-28">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0djItaDJ2LTJoLTJ6bTAgNHYyaDJ2LTJoLTJ6bTQtNHYyaDJ2LTJoLTJ6Ii8+PC9nPjwvZz48L3N2Zz4=')] opacity-10"></div>
    
    <div class="container mx-auto px-6 lg:px-8 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!--Left: Content -->
            <div class="space-y-6">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-medium">
                    <span class="w-2 h-2 bg-success-500 rounded-full animate-pulse"></span>
                    Trusted by 10,000+ customers
                </div>
                
                <h1 class="text-4xl lg:text-5xl xl:text-6xl font-bold leading-tight">
                    Premium Health & <br class="hidden lg:block"/>Wellness Products
                </h1>
                
                <p class="text-lg lg:text-xl text-gray-200 leading-relaxed max-w-xl">
                    Discover high-quality nutritional supplements and wellness products designed for your optimal health.
                </p>
                
                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="<?php echo esc_url( home_url( '/cua-hang' ) ); ?>" class="inline-flex items-center gap-2 bg-white text-primary-900 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <span>Shop Now</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="#features" class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/20 transition-all duration-300 border border-white/20">
                        <span>Learn More</span>
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-6 pt-8 border-t border-white/20">
                    <div>
                        <div class="text-3xl font-bold">10K+</div>
                        <div class="text-sm text-gray-300">Happy Customers</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold">50+</div>
                        <div class="text-sm text-gray-300">Products</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold">4.8★</div>
                        <div class="text-sm text-gray-300">Average Rating</div>
                    </div>
                </div>
            </div>

            <!-- Right: Image/Visual -->
            <div class="relative">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1556742111-a301076d9d18?w=800&h=600&fit=crop" 
                         alt="Health Products" 
                         class="w-full h-auto object-cover"
                         loading="lazy" />
                    <div class="absolute inset-0 bg-gradient-to-t from-primary-900/50 to-transparent"></div>
                </div>
                <!-- Floating badge -->
                <div class="absolute -bottom-6 -left-6 bg-white rounded-xl shadow-xl p-6 max-w-xs hidden lg:block">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-success-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shield-alt text-success-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-primary-900">100% Certified</div>
                            <div class="text-sm text-gray-600">International Standards</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES SECTION -->
<section id="features" class="py-16 lg:py-20 bg-gray-50">
    <div class="container mx-auto px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-primary-900 mb-4">Why Choose Us</h2>
            <p class="text-gray-600 text-lg">We're committed to providing the best products and service for your health journey.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="w-14 h-14 bg-accent-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-certificate text-accent-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-primary-900 mb-2">Certified Quality</h3>
                <p class="text-gray-600 text-sm">All products are internationally certified (Halal, GMP) for your safety.</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="w-14 h-14 bg-success-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-shipping-fast text-success-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-primary-900 mb-2">Fast Delivery</h3>
                <p class="text-gray-600 text-sm">Quick and reliable shipping to your doorstep nationwide.</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="w-14 h-14 bg-primary-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-headset text-primary-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-primary-900 mb-2">24/7 Support</h3>
                <p class="text-gray-600 text-sm">Our customer service team is always ready to help you.</p>
            </div>

            <!-- Feature 4 -->
            <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-sync-alt text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-primary-900 mb-2">Easy Returns</h3>
                <p class="text-gray-600 text-sm">30-day return policy for your peace of mind.</p>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED PRODUCTS SECTION -->
<section class="py-16 lg:py-20 bg-white">
    <div class="container mx-auto px-6 lg:px-8">
        <div class="flex items-end justify-between mb-12">
            <div>
                <h2 class="text-3xl lg:text-4xl font-bold text-primary-900 mb-2">Featured Products</h2>
                <p class="text-gray-600">Discover our most popular wellness solutions</p>
            </div>
            <a href="<?php echo esc_url( home_url( '/cua-hang' ) ); ?>" class="hidden md:inline-flex items-center gap-2 text-accent-600 hover:text-accent-700 font-semibold transition">
                View All Products
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <!-- Product Grid -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php
            // Sample products - replace with actual Pancake products later
            $sample_products = array(
                array('name' => 'Multivitamin Complex', 'price' => '599,000₫', 'image' => 'https://images.unsplash.com/photo-1550572017-edd951aa8ca0?w=400&h=400&fit=crop'),
                array('name' => 'Omega-3 Fish Oil', 'price' => '450,000₫', 'image' => 'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?w=400&h=400&fit=crop'),
                array('name' => 'Vitamin D3 Capsules', 'price' => '350,000₫', 'image' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&h=400&fit=crop'),
                array('name' => 'Protein Powder', 'price' => '750,000₫', 'image' => 'https://images.unsplash.com/photo-1579722820308-d74e571900a9?w=400&h=400&fit=crop'),
            );

            foreach ($sample_products as $product) :
            ?>
                <div class="bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 group">
                    <div class="relative overflow-hidden bg-gray-100 aspect-square">
                        <img src="<?php echo esc_url($product['image']); ?>" 
                             alt="<?php echo esc_attr($product['name']); ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                             loading="lazy" />
                        <div class="absolute top-3 right-3 bg-accent-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                            New
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-primary-900 mb-2 line-clamp-2"><?php echo esc_html($product['name']); ?></h3>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-accent-600"><?php echo esc_html($product['price']); ?></span>
                            <button class="w-9 h-9 bg-primary-900 text-white rounded-lg hover:bg-accent-600 transition flex items-center justify-center">
                                <i class="fas fa-plus text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Mobile View All -->
        <div class="md:hidden text-center">
            <a href="<?php echo esc_url( home_url( '/cua-hang' ) ); ?>" class="inline-flex items-center gap-2 text-accent-600 hover:text-accent-700 font-semibold">
                View All Products
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section class="py-16 lg:py-20 bg-primary-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0djItaDJ2LTJoLTJ6bTAgNHYyaDJ2LTJoLTJ6bTQtNHYyaDJ2LTJoLTJ6Ii8+PC9nPjwvZz48L3N2Zz4=')] opacity-10"></div>
    
    <div class="container mx-auto px-6 lg:px-8 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl lg:text-4xl font-bold mb-4">Ready to Start Your Wellness Journey?</h2>
            <p class="text-lg text-gray-200 mb-8">Join thousands of satisfied customers who trust us for their health needs.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="<?php echo esc_url( home_url( '/cua-hang' ) ); ?>" class="inline-flex items-center gap-2 bg-white text-primary-900 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-all duration-300 shadow-lg">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Browse Products</span>
                </a>
                <a href="<?php echo esc_url( home_url( '/lien-he' ) ); ?>" class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/20 transition-all duration-300 border border-white/20">
                    <i class="fas fa-phone"></i>
                    <span>Contact Us</span>
                </a>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
