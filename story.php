<?php
/**
 * Template Name: Story
 * Template Post Type: page
 * Description: Template for displaying story page
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
    <title>Chuyện Của CareMIL - Khởi Nguồn Yêu Thương</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Manrope:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            navy: '#1a4f8a',   /* Primary */
                            blue: '#4cc9f0',   /* Secondary */
                            gold: '#ffd166',   /* Accent */
                            cream: '#fdfbf7',  /* Background Warm */
                            sand: '#f5f0eb',   /* Section Alt */
                            green: '#eef5f0',  /* Nature/Fibregum */
                            dark: '#0f172a'
                        }
                    },
                    fontFamily: {
                        sans: ['Manrope', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                        hand: ['Dancing Script', 'cursive'],
                    },
                    letterSpacing: {
                        'cinema': '0.2em',
                    },
                    animation: {
                        'float-slow': 'float 8s ease-in-out infinite',
                        'fade-in': 'fadeIn 1s ease-out forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-15px)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        }
                    },
                    screens: {
                        'xs': '475px', // Custom breakpoint for very small screens
                    }
                }
            }
        }
    </script>
    <style>
        /* --- CORE STYLES --- */
        ::-webkit-scrollbar { width: 0px; background: transparent; }

        .text-justify-art { text-align: justify; text-justify: inter-word; line-height: 1.8; }
        
        .drop-cap::first-letter {
            float: left;
            font-family: 'Playfair Display', serif;
            font-size: 4rem; /* Adjusted for better mobile fit */
            line-height: 0.8;
            font-weight: 700;
            padding-right: 12px;
            color: #1a4f8a;
            margin-top: 4px;
        }
        @media (min-width: 768px) {
            .drop-cap::first-letter { font-size: 5rem; padding-right: 16px; }
        }

        /* --- ANIMATIONS --- */
        .reveal { opacity: 0; transform: translateY(40px); transition: all 1s cubic-bezier(0.2, 0.8, 0.2, 1); }
        .reveal.active { opacity: 1; transform: translateY(0); }
        
        .reveal-img { opacity: 0; transform: scale(0.98); transition: all 1.2s cubic-bezier(0.2, 0.8, 0.2, 1); }
        .reveal-img.active { opacity: 1; transform: scale(1); }

        /* --- VISUAL EFFECTS --- */
        .grain-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none; z-index: 50; mix-blend-mode: multiply;
        }

        .line-draw { stroke-dasharray: 1000; stroke-dashoffset: 1000; transition: stroke-dashoffset 2s ease-out; }
        .line-draw.active { stroke-dashoffset: 0; }

        /* Gallery Card */
        .gallery-card {
            position: relative; overflow: hidden; border-radius: 12px;
            transition: transform 0.5s ease, box-shadow 0.5s ease;
        }
        .gallery-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -10px rgba(0,0,0,0.15); z-index: 10; }
        .gallery-card img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.7s ease; }
        .gallery-card:hover img { transform: scale(1.05); }

        #loader {
            position: fixed; inset: 0; background: #fdfbf7; z-index: 100;
            display: flex; align-items: center; justify-content: center;
            transition: opacity 0.5s ease-out, visibility 0.5s;
        }
    </style>
</head>
<body class="bg-brand-cream text-slate-700 font-sans antialiased overflow-x-hidden selection:bg-brand-gold selection:text-white">

    <!-- PRELOADER -->
    <div id="loader">
        <div class="text-center px-4">
            <h1 class="text-4xl md:text-5xl font-serif italic text-brand-navy animate-pulse tracking-wide mb-2">CareMIL</h1>
            <div class="h-0.5 w-12 bg-brand-gold mx-auto rounded-full"></div>
            <p class="text-[10px] md:text-xs font-sans tracking-[0.4em] text-brand-navy uppercase mt-4 opacity-50">DawnBridge Story</p>
        </div>
    </div>

    <div class="grain-overlay"></div>

    <!-- 1. PROLOGUE: THE ORIGIN (Responsive Hero) -->
    <section class="relative h-screen min-h-[600px] flex items-center justify-center overflow-hidden bg-brand-navy">
        <!-- Background Art -->
        <div class="absolute inset-0 z-0 opacity-40">
             <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/happy-mothers-day-celebration-scaled.png" 
                 alt="Atmosphere" 
                 class="w-full h-full object-cover scale-110 grayscale mix-blend-luminosity animate-float-slow" style="animation-duration: 30s;">
        </div>
        <div class="absolute inset-0 bg-gradient-to-b from-brand-navy/60 via-brand-navy/40 to-brand-cream z-1"></div>
        
        <div class="relative z-10 px-6 text-center max-w-4xl w-full">
            <p class="text-brand-gold font-sans text-[10px] md:text-xs tracking-[0.3em] uppercase mb-6 md:mb-8 reveal delay-300 font-bold border-b border-brand-gold/30 inline-block pb-2">
                Câu Chuyện Thương Hiệu
            </p>
            
            <h1 class="text-5xl md:text-7xl lg:text-9xl font-serif text-white mb-8 md:mb-10 leading-none reveal delay-500 tracking-tight drop-shadow-2xl">
                <span class="block italic font-light opacity-80 text-3xl md:text-5xl lg:text-7xl mb-2 md:mb-4 text-brand-blue font-hand">Từ những đêm</span>
                Thức Trắng
            </h1>
            
            <p class="text-lg md:text-2xl lg:text-3xl font-serif italic text-blue-100 max-w-xl mx-auto reveal delay-700 leading-relaxed font-light px-4">
                "Đâu là gốc rễ để con được yên giấc, ăn ngon và nụ cười lại nở trên môi?"
            </p>
        </div>
    </section>

    <!-- 2. CHAPTER 1: THE PAIN (Sticky on Desktop, Stacked on Mobile) -->
    <section class="py-16 md:py-24 lg:py-40 bg-brand-cream relative">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-32 items-center">
                
                <!-- Image Art (Left) -->
                <div class="reveal-img relative order-1">
                    <div class="absolute -top-4 -left-4 md:-top-6 md:-left-6 w-full h-full border border-brand-navy/10 rounded-tr-[60px] rounded-bl-[60px] md:rounded-tr-[100px] md:rounded-bl-[100px] z-0"></div>
                    <div class="relative rounded-tr-[60px] rounded-bl-[60px] md:rounded-tr-[100px] md:rounded-bl-[100px] overflow-hidden shadow-2xl aspect-[3/4] md:aspect-[4/5] lg:aspect-[3/4]">
                        <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/medium-shot-woman-holding-cute-baby-scaled.jpg" 
                             class="w-full h-full object-cover filter contrast-[1.05]" alt="Mother's Love">
                        <div class="absolute inset-0 bg-brand-navy/10 mix-blend-multiply"></div>
                    </div>
                </div>

                <!-- Text (Right) -->
                <div class="reveal order-2">
                    <span class="text-8xl md:text-9xl font-serif text-brand-navy/5 absolute -top-16 -right-4 md:-top-20 md:-right-10 select-none pointer-events-none z-0">01</span>
                    <h2 class="text-3xl md:text-5xl lg:text-6xl font-serif text-brand-navy mb-8 md:mb-12 leading-tight relative z-10">
                        Khoảng Lặng <br> <span class="italic text-brand-gold">Đầy Bão Tố</span>
                    </h2>
                    
                    <div class="space-y-6 md:space-y-8 text-base md:text-lg font-light text-slate-700 text-justify-art relative z-10">
                        <p class="drop-cap">
                            Khi ánh đèn của những ngôi nhà khác đã tắt, thì đâu đó trong căn phòng của những gia đình có con đặc biệt (ASD, ADHD), ánh đèn vẫn sáng.
                        </p>
                        <p>
                            Đó không phải là sự tĩnh lặng của bình yên, mà là khoảng lặng sau những cơn khủng hoảng cảm xúc. Chúng tôi đã thấy những giọt nước mắt bất lực của người mẹ khi bát cháo trên tay nguội lạnh mà con vẫn kiên quyết chối từ.
                        </p>
                        <div class="pl-6 md:pl-8 border-l-4 border-brand-gold py-4 bg-brand-sand/50 rounded-r-2xl my-6 md:my-8">
                            <p class="font-hand text-xl md:text-2xl text-brand-navy/90">"Làm sao để bước vào thế giới của con?"</p>
                        </div>
                        <p class="text-xs md:text-sm font-bold uppercase tracking-widest text-brand-navy/60 pt-6 md:pt-8 border-t border-brand-navy/10">
                            Mệnh lệnh từ trái tim những người sáng lập DawnBridge.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. CHAPTER 2: THE DISCOVERY (Science & Malaysia) -->
    <section class="py-16 md:py-24 lg:py-40 bg-brand-sand relative overflow-hidden">
        <!-- SVG Line -->
        <svg class="absolute top-0 right-0 h-full w-full pointer-events-none opacity-20" viewBox="0 0 1000 1000" preserveAspectRatio="none">
            <path d="M1000 0 C 800 200 600 400 0 1000" stroke="#1a4f8a" stroke-width="1.5" fill="none" class="line-draw" />
        </svg>

        <div class="container mx-auto px-6 max-w-7xl relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 lg:gap-24 items-center">
                <!-- Text (5 cols) -->
                <div class="md:col-span-5 reveal pl-0 md:pl-8 order-2 md:order-1">
                    <p class="text-[10px] md:text-xs font-bold tracking-[0.3em] uppercase text-brand-navy/40 mb-4">Chương 02</p>
                    <h2 class="text-3xl md:text-5xl lg:text-7xl font-serif text-brand-navy mb-8 md:mb-10 leading-none">
                        Chân Lý Từ <br> <span class="italic text-brand-blue border-b-4 border-brand-gold/30">Malaysia</span>
                    </h2>
                    <p class="text-base md:text-lg text-slate-600 font-light leading-relaxed mb-6 md:mb-8 text-justify-art">
                        Tại Malaysia – "trái tim" của tiêu chuẩn thực phẩm khắt khe, chúng tôi tìm thấy chìa khóa trong những phòng Lab hiện đại: <strong>Sự kết nối mật thiết của Trục Não - Ruột</strong>.
                    </p>
                    <ul class="space-y-4 font-serif text-lg md:text-xl text-brand-navy italic">
                        <li class="flex items-center gap-4"><span class="w-1.5 h-1.5 bg-brand-gold rounded-full flex-shrink-0"></span> Hệ tiêu hóa là bộ não thứ hai.</li>
                        <li class="flex items-center gap-4"><span class="w-1.5 h-1.5 bg-brand-gold rounded-full flex-shrink-0"></span> Bụng êm thì trí mới sáng.</li>
                    </ul>
                </div>

                <!-- Image (7 cols) -->
                <div class="md:col-span-7 reveal-img order-1 md:order-2">
                    <div class="relative w-full aspect-[16/9] md:aspect-[4/3] overflow-hidden shadow-2xl rounded-t-full rounded-b-2xl border-4 border-white">
                        <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?q=80&w=2070&auto=format&fit=crop" 
                             class="w-full h-full object-cover transform hover:scale-105 transition duration-[2s]" alt="Science Lab">
                        <div class="absolute bottom-0 right-0 bg-white p-4 md:p-8 border-t border-l border-brand-sand">
                            <span class="block text-2xl md:text-4xl font-serif font-bold text-brand-navy">1000+</span>
                            <span class="text-[10px] md:text-xs uppercase tracking-widest text-slate-500">Giờ nghiên cứu</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. CHAPTER 3: THE GIFT - FIBREGUM (Distinct Section) -->
    <section class="py-20 md:py-32 bg-brand-green relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-20 mix-blend-multiply"></div>
        
        <div class="container mx-auto px-6 max-w-6xl relative z-10">
            <div class="text-center mb-12 md:mb-20 reveal">
                <i class="fas fa-leaf text-4xl md:text-5xl text-green-700/30 mb-4 md:mb-6 block mx-auto animate-float-slow"></i>
                <h2 class="text-4xl md:text-5xl lg:text-7xl font-serif text-brand-navy mb-4 md:mb-6 leading-tight">
                    <span class="italic font-hand text-5xl md:text-6xl lg:text-8xl text-brand-gold mr-2 md:mr-4">Fibregum™</span>
                </h2>
                <p class="text-lg md:text-2xl font-serif italic text-brand-navy/70">"Cái ôm dịu dàng từ thiên nhiên nước Pháp"</p>
                <div class="w-24 md:w-32 h-1 bg-brand-gold mx-auto mt-6 md:mt-8 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <!-- Visual Story (Card Effect) -->
                <div class="relative reveal-img group cursor-none w-full max-w-md mx-auto order-1">
                    <div class="absolute -inset-3 border-2 border-brand-navy/10 rounded-full animate-[spin_30s_linear_infinite]"></div>
                    <div class="relative rounded-full overflow-hidden shadow-2xl aspect-square border-8 border-white/50">
                        <img src="https://www.jardineriaon.com/wp-content/uploads/2022/02/cuidados-acacia-espinosa.jpg" 
                             class="w-full h-full object-cover transform hover:scale-110 transition duration-[2s]" alt="Acacia Tree">
                        <div class="absolute inset-0 bg-brand-navy/20 mix-blend-overlay"></div>
                    </div>
                    <!-- Badge -->
                    <div class="absolute top-0 right-0 bg-white p-2 md:p-4 rounded-full shadow-lg w-20 h-20 md:w-28 md:h-28 flex flex-col items-center justify-center text-center border-4 border-brand-sand transform rotate-12">
                        <span class="text-xl md:text-3xl"><img src="https://upload.wikimedia.org/wikipedia/en/c/c3/Flag_of_France.svg" alt="France Flag" class="inline w-7 md:w-10 h-auto shadow"/></span>
                        <span class="text-[8px] md:text-[10px] font-bold text-brand-navy uppercase mt-1 tracking-widest">France</span>
                    </div>
                </div>

                <!-- Narrative -->
                <div class="reveal space-y-8 pl-0 lg:pl-10 order-2">
                    <p class="text-base md:text-xl text-slate-700 font-light leading-relaxed italic border-l-4 border-brand-blue pl-6 bg-white/50 py-4 rounded-r-xl">
                        "Hãy tưởng tượng về những cây Acacia vươn mình mạnh mẽ dưới ánh nắng châu Âu. Fibregum™ là chất xơ thế hệ mới được chiết xuất 100% từ loài cây ấy."
                    </p>
                    
                    <div class="grid grid-cols-1 gap-4 md:gap-6">
                        <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-brand-green flex items-start gap-4 md:gap-5 hover:shadow-md transition">
                            <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-full flex items-center justify-center text-xl md:text-2xl flex-shrink-0">🌿</div>
                            <div>
                                <h4 class="font-serif text-lg md:text-xl text-brand-navy font-bold">Người Mẹ Hiền</h4>
                                <p class="text-sm text-slate-600 mt-2">Nhẹ nhàng tráng qua thành ruột, nuôi dưỡng hệ vi sinh mà không gây kích ứng.</p>
                            </div>
                        </div>
                        <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-brand-blue flex items-start gap-4 md:gap-5 hover:shadow-md transition">
                            <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-full flex items-center justify-center text-xl md:text-2xl flex-shrink-0">🛡️</div>
                            <div>
                                <h4 class="font-serif text-lg md:text-xl text-brand-navy font-bold">Hàn Gắn Tổn Thương</h4>
                                <p class="text-sm text-slate-600 mt-2">Giúp phục hồi niêm mạc ruột, ngăn ngừa hội chứng rò rỉ ruột hiệu quả.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. CHAPTER 4: THE MASTERPIECE - PRODUCT -->
    <section class="py-20 md:py-24 bg-brand-cream relative overflow-hidden">
        <div class="container mx-auto px-6 max-w-6xl relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Text Info -->
                <div class="lg:col-span-5 reveal order-2 lg:order-1">
                    <h2 class="text-3xl md:text-4xl lg:text-6xl font-serif text-brand-navy mb-6 md:mb-8 leading-tight">
                        Không Đại Trà. <br> 
                        Là <span class="italic text-brand-gold font-hand">"May Đo"</span>
                    </h2>
                    <p class="text-base md:text-lg text-slate-600 leading-relaxed font-light mb-8 md:mb-10 text-justify-art">
                        CareMIL ra đời không phải để lấp đầy kệ siêu thị. Nó là kết tinh của hàng ngàn giờ nghiên cứu để tạo ra công thức <strong>"vừa vặn nhất"</strong> cho cơ địa nhạy cảm của trẻ đặc biệt.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <span class="px-4 md:px-5 py-2 bg-brand-navy text-white rounded-full text-xs md:text-sm font-bold shadow-lg tracking-wide">Tinh Khiết</span>
                        <span class="px-4 md:px-5 py-2 bg-white border border-brand-navy text-brand-navy rounded-full text-xs md:text-sm font-bold">Khoa Học</span>
                        <span class="px-4 md:px-5 py-2 bg-white border border-brand-navy text-brand-navy rounded-full text-xs md:text-sm font-bold">Yêu Thương</span>
                    </div>
                </div>
                
                <!-- Product Image (Fixed Size & Ratio) -->
                <div class="lg:col-span-7 relative flex justify-center lg:justify-end reveal-img order-1 lg:order-2 mb-10 lg:mb-0">
                    <div class="relative w-full max-w-xs md:max-w-md aspect-square flex items-center justify-center">
                        <div class="absolute inset-0 bg-gradient-radial from-brand-gold/20 to-transparent rounded-full filter blur-[60px] transform scale-75 translate-y-10"></div>
                        <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Hop-sua.png" 
                             alt="CareMIL Product" 
                             class="relative z-10 w-full h-auto object-contain drop-shadow-2xl hover:scale-105 transition duration-700 ease-out">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. CHAPTER 5: THE ECOSYSTEM & HEART (Community Gallery - FIXED) -->
    <section class="py-24 md:py-32 bg-brand-navy text-white relative">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-brand-blue/5 skew-x-12 transform origin-top-right hidden sm:block"></div>
        <div class="absolute top-0 right-0 w-full h-32 bg-brand-blue/10 sm:hidden"></div>
        
        <div class="container mx-auto px-6 max-w-7xl relative z-10">
            <!-- Dr Yee Quote -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-24 md:mb-32 items-center reveal">
                <div class="lg:col-span-5 relative">
                    <div class="relative rounded-t-full rounded-b-2xl overflow-hidden shadow-2xl border-2 border-brand-gold/50 aspect-[3/4] max-w-sm mx-auto lg:mx-0">
                        <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Dr-Yee-1.webp" 
                             class="w-full h-full object-cover filter grayscale-[10%]" alt="Dr Yee">
                        <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-brand-dark to-transparent p-6 md:p-8 pt-32">
                            <p class="font-serif text-brand-gold text-xl md:text-2xl font-bold">Datuk Yee Kok Wah</p>
                            <p class="text-xs uppercase tracking-widest opacity-80 mt-1 font-sans">Linh hồn của DawnBridge</p>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-7 pl-0 lg:pl-10">
                    <i class="fas fa-quote-left text-4xl md:text-6xl text-brand-gold/10 absolute -top-8 md:-top-10 -left-4 md:-left-10"></i>
                    <p class="font-serif italic text-xl md:text-3xl lg:text-4xl leading-relaxed mb-8 md:mb-10 text-white/90">
                        "I just love seeing those smiles! When I see them, I also see all the hard work and worries that Mom and Dad went through."
                    </p>
                    <div class="pl-6 border-l-2 border-brand-gold/50">
                        <p class="text-blue-100 font-light text-base md:text-lg">"Tôi yêu biết bao những nụ cười rạng rỡ ấy! Bởi khi nhìn vào gương mặt con, tôi thấu cảm được trọn vẹn những nhọc nhằn và âu lo mà Bố Mẹ đã kiên cường vượt qua."</p>
                    </div>
                </div>
            </div>

            <!-- COMMUNITY GALLERY EXHIBITION -->
            <div class="border-t border-white/10 pt-16 md:pt-24">
                <h2 class="text-center text-3xl md:text-4xl lg:text-6xl font-serif mb-16 md:mb-20 reveal">Hành Động Vì <span class="italic text-brand-gold font-hand text-4xl md:text-6xl">Cộng Đồng</span></h2>

                <!-- 2025 -->
                <div class="mb-24 md:mb-32 reveal">
                    <div class="flex flex-col md:flex-row items-end gap-2 md:gap-6 mb-8 border-b border-white/10 pb-4">
                        <span class="text-6xl md:text-7xl lg:text-9xl font-serif text-brand-gold font-bold opacity-30 leading-none">2025</span>
                        <div class="mb-3">
                            <h3 class="text-xl md:text-2xl lg:text-3xl font-bold text-white mb-2">SEAMEO SEN Kuching</h3>
                            <p class="text-blue-200 text-xs md:text-sm lg:text-base font-light border-l border-brand-gold pl-4 italic">
                                "Tại Kuching, chúng tôi khẳng định cam kết đồng hành cùng giáo dục đặc biệt Đông Nam Á."
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 h-auto md:h-[500px]">
                        <div class="md:col-span-8 h-full gallery-card aspect-video md:aspect-auto">
                            <img src="https://www.dawnbridge.com.my/wp-content/uploads/2025/07/seameo-sen-db-D-1024x683.jpg" alt="Main Event">
                            <div class="absolute bottom-4 left-4 text-white text-xs md:text-sm font-bold opacity-0 group-hover:opacity-100 transition">Hội nghị quốc tế</div>
                        </div>
                        <div class="md:col-span-4 flex flex-col gap-4 h-full">
                            <div class="h-1/2 gallery-card aspect-video md:aspect-auto">
                                <img src="https://www.dawnbridge.com.my/wp-content/uploads/2025/07/seameo-sen-db-3-scaled-e1753942726459-1024x653.jpg" alt="Discussion">
                            </div>
                            <div class="h-1/2 gallery-card aspect-video md:aspect-auto">
                                <img src="https://www.dawnbridge.com.my/wp-content/uploads/2025/07/seameo-sen-db-4-scaled-e1753944615350-1024x656.jpg" alt="Networking">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2024 -->
                <div class="mb-24 md:mb-32 reveal">
                    <div class="flex flex-col md:flex-row items-end gap-2 md:gap-6 mb-8 border-b border-white/10 pb-4 md:flex-row-reverse md:text-right">
                        <span class="text-6xl md:text-7xl lg:text-9xl font-serif text-brand-pink font-bold opacity-30 leading-none">2024</span>
                        <div class="mb-2 md:mb-4 max-w-2xl ml-auto">
                            <h3 class="text-xl md:text-2xl lg:text-3xl font-bold text-white mb-2">Kanner Melaka Run</h3>
                            <p class="text-blue-200 text-xs md:text-sm lg:text-base font-light border-l md:border-l-0 md:border-r border-brand-pink pl-4 md:pr-4 italic">
                                "Những bước chạy xóa bỏ rào cản. Tự hào được tiếp sức cho những đôi chân nhỏ bé."
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="aspect-[3/4] gallery-card transform md:translate-y-8"><img src="https://www.dawnbridge.com.my/wp-content/uploads/2025/04/Kanner-Melaka-Event-5-e1745292911764-892x1024.jpg"></div>
                        <div class="aspect-[3/4] gallery-card"><img src="https://www.dawnbridge.com.my/wp-content/uploads/2025/04/Kanner-Melaka-Event-4-e1745292715288-772x1024.jpg"></div>
                        <div class="aspect-[3/4] gallery-card transform md:translate-y-8"><img src="https://www.dawnbridge.com.my/wp-content/uploads/2025/04/Kanner-Melaka-Event-3-e1745292535776-1024x938.jpg"></div>
                        <div class="aspect-[3/4] gallery-card"><img src="https://www.dawnbridge.com.my/wp-content/uploads/2025/04/Kanner-Melaka-Event-1-1024x1024.jpg"></div>
                    </div>
                </div>

                <!-- 2023 -->
                <div class="mb-24 md:mb-32 reveal">
                     <div class="flex flex-col md:flex-row items-end gap-2 md:gap-6 mb-8 border-b border-white/10 pb-6">
                        <span class="text-6xl md:text-7xl lg:text-9xl font-serif text-brand-green font-bold opacity-30 leading-none">2023</span>
                        <div class="mb-2 md:mb-4 max-w-2xl">
                            <h3 class="text-xl md:text-2xl lg:text-3xl font-bold text-white mb-2">SEAMEO SEN KL</h3>
                            <p class="text-blue-200 text-xs md:text-sm lg:text-base font-light border-l border-brand-green pl-4 italic">
                                "Dấu ấn Việt Nam. Khởi điểm cho hành trình 'Mang yêu thương về nhà' của CareMIL."
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="aspect-video gallery-card"><img src="https://www.dawnbridge.com.my/wp-content/uploads/2025/04/SEAMEO-SEN-7-e1745312360493.jpg"></div>
                        <div class="aspect-video gallery-card"><img src="https://www.dawnbridge.com.my/wp-content/uploads/2025/04/SEAMEO-SEN-6-1024x768.jpg"></div>
                        <div class="aspect-video gallery-card"><img src="https://www.dawnbridge.com.my/wp-content/uploads/2025/04/SEAMEO-SEN-8-1024x768.jpg"></div>
                    </div>
                </div>

                <!-- 2022 -->
                <div class="reveal">
                    <div class="flex flex-col md:flex-row items-end gap-2 md:gap-6 mb-8 border-b border-white/10 pb-4 md:flex-row-reverse md:text-right">
                        <span class="text-6xl md:text-7xl lg:text-9xl font-serif text-brand-blue font-bold opacity-30 leading-none">2022</span>
                        <div class="mb-2 md:mb-4 max-w-2xl ml-auto">
                            <h3 class="text-xl md:text-2xl lg:text-3xl font-bold text-white mb-2">Doll Donation</h3>
                            <p class="text-blue-200 text-xs md:text-sm lg:text-base font-light border-l md:border-l-0 md:border-r border-brand-blue pl-4 md:pr-4 italic">
                                "Khởi đầu giản dị từ những món quà nhỏ. Nhìn nụ cười ngây ngô khi ôm búp bê, chúng tôi biết mình đã chọn đúng con đường."
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-4 md:gap-6 overflow-x-auto pb-6 snap-x hide-scrollbar">
                        <div class="w-56 md:w-80 flex-shrink-0 aspect-square rounded-full overflow-hidden border-4 border-white/10 gallery-card snap-center"><img src="https://www.dawnbridge.com.my/wp-content/uploads/2025/04/LOVE-Pass-It-On-1-1024x717.jpg"></div>
                        <div class="w-56 md:w-80 flex-shrink-0 aspect-square rounded-full overflow-hidden border-4 border-white/10 gallery-card snap-center mt-0 md:mt-12"><img src="https://www.dawnbridge.com.my/wp-content/uploads/2025/04/LOVE-Pass-It-On-3-1024x1024.jpg"></div>
                        <div class="w-56 md:w-80 flex-shrink-0 aspect-square rounded-full overflow-hidden border-4 border-white/10 gallery-card snap-center"><img src="https://www.dawnbridge.com.my/wp-content/uploads/2025/04/LOVE-Pass-It-On-5-1024x1024.png"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. EPILOGUE: THE PROMISE -->
    <section class="h-screen min-h-[800px] relative flex items-center justify-center bg-white overflow-hidden">
        <!-- Background -->
        <div class="absolute inset-0 z-0">
            <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/black-white-emotional-portrait-sad-kid-sitting-alone-playing-with-tank-toys_39190-904.jpg" 
                 class="w-full h-full object-cover opacity-10 filter grayscale contrast-125 scale-105 scroll-parallax" alt="Kid">
            <div class="absolute inset-0 bg-gradient-to-t from-white via-white/80 to-transparent"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10 text-center max-w-4xl flex flex-col items-center">
            <div class="w-16 h-16 md:w-20 md:h-20 border border-brand-navy/10 rounded-full flex items-center justify-center mb-8 md:mb-12 reveal bg-white/50 backdrop-blur-md shadow-sm">
                <i class="fas fa-infinity text-brand-navy/60 text-xl md:text-2xl"></i>
            </div>

            <h2 class="text-2xl md:text-6xl lg:text-5xl font-serif text-brand-navy mb-8 md:mb-12 leading-tight reveal px-2">
                "Khi cái bụng con được <span class="text-brand-gold font-hand">Yên</span>,<br>
                Tâm trí con sẽ <span class="text-brand-blue font-hand">Sáng</span>."
            </h2>
            
            <p class="text-base md:text-xl lg:text-2xl font-light text-slate-600/80 mb-16 md:mb-20 leading-relaxed reveal delay-200 max-w-2xl text-center px-4">
                Gửi những người cha, người mẹ Việt: <strong>Bạn không đơn độc.</strong><br>
                Hãy để CareMIL san sẻ bớt gánh nặng trên đôi vai bạn.
            </p>
            
            <div class="reveal delay-300">
                <a href="https://caremil.dawnbridge.vn/" class="inline-flex items-center gap-3 md:gap-4 px-10 md:px-12 py-4 md:py-5 rounded-full bg-brand-navy text-white font-bold text-base md:text-lg shadow-2xl hover:bg-brand-blue transition transform hover:-translate-y-2 hover:shadow-brand-blue/30 tracking-widest uppercase">
                    Xem Thêm Về Sản Phẩm 
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="mt-20 md:mt-24 opacity-40 font-serif italic text-xs md:text-sm reveal delay-400">
                <p>Trân trọng,</p>
                <div class="text-base md:text-lg border-b border-brand-navy/20 pb-1 inline-block text-brand-navy font-bold">The CareMIL & DawnBridge Team</div>
            </div>
        </div>
    </section>

    <!-- JS: Observer -->
    <script>
        // Load in under 1s: hide loader after 300ms
        window.addEventListener('load', () => {
            setTimeout(() => {
                const loader = document.getElementById('loader');
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 100);
            }, 100);
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, { threshold: 0.15 });

        document.querySelectorAll('.reveal, .reveal-up, .reveal-img').forEach(el => observer.observe(el));
        
        window.addEventListener('scroll', () => {
            const scroll = window.pageYOffset;
            const heroImgs = document.querySelectorAll('.scroll-parallax');
            heroImgs.forEach(img => { img.style.transform = `scale(${1.05 + scroll * 0.0001})`; });
        });
    </script>
<?php
get_footer();