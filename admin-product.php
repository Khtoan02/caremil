<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm - CareMIL Admin</title>
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
        body { background-color: #f1f5f9; }
        
        /* Form Inputs */
        .admin-input {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background-color: white;
            font-size: 0.9rem;
            transition: all 0.2s;
            color: #1e293b;
        }
        .admin-input:focus {
            border-color: #4cc9f0;
            box-shadow: 0 0 0 3px rgba(76, 201, 240, 0.1);
            outline: none;
        }
        .admin-label {
            font-weight: 700;
            color: #475569;
            font-size: 0.85rem;
            margin-bottom: 6px;
            display: block;
        }

        /* Image Upload Box */
        .upload-box {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            transition: all 0.3s;
        }
        .upload-box:hover {
            border-color: #4cc9f0;
            background-color: #f0f9ff;
        }

        /* Live Preview Card Styles (Copied from Store Layout) */
        .preview-card { transition: all 0.3s ease; background: white; border-radius: 1rem; overflow: hidden; border: 1px solid #f1f5f9; }
        .preview-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="font-sans text-slate-600">

    <!-- MAIN LAYOUT (Removed Header, Adjusted Padding) -->
    <main class="container mx-auto px-4 py-8 flex flex-col lg:flex-row gap-8">
        
        <!-- LEFT: EDITOR FORM -->
        <div class="lg:w-2/3 space-y-6">
            
            <!-- Header Actions -->
            <div class="flex justify-between items-center mb-2">
                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-edit text-brand-blue"></i> Thêm Sản Phẩm Mới
                </h1>
                <div class="flex gap-2">
                    <button class="px-4 py-2 rounded-lg border border-slate-300 font-bold text-slate-600 hover:bg-slate-50 transition text-sm">Hủy</button>
                    <button class="px-6 py-2 rounded-lg bg-brand-green text-white font-bold shadow-md hover:bg-green-500 transition flex items-center gap-2 text-sm">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                </div>
            </div>

            <!-- Block 1: Thông tin cơ bản -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <h2 class="text-lg font-bold text-brand-navy mb-4 border-b border-slate-100 pb-2">Thông Tin Cơ Bản</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="admin-label">Tên sản phẩm</label>
                        <input type="text" class="admin-input" placeholder="Ví dụ: CareMIL Hộp Lớn 800g" value="CareMIL Hộp Lớn 800g" id="p_name" oninput="updatePreview()">
                    </div>
                    <div>
                        <label class="admin-label">Mã sản phẩm (SKU)</label>
                        <input type="text" class="admin-input" placeholder="CM-800G">
                    </div>
                    <div>
                        <label class="admin-label">Phân loại (Unit)</label>
                        <select class="admin-input" id="p_unit">
                            <option value="Hộp thiếc">Hộp thiếc</option>
                            <option value="Gói 36g">Gói 36g</option>
                            <option value="Combo">Combo</option>
                        </select>
                    </div>
                    <div>
                        <label class="admin-label">Giá bán (VNĐ)</label>
                        <input type="number" class="admin-input text-brand-pink font-bold" value="850000" id="p_price" oninput="updatePreview()">
                    </div>
                    <div>
                        <label class="admin-label">Giá gốc (VNĐ) <span class="font-normal text-xs text-gray-400">- Để trống nếu không giảm</span></label>
                        <input type="number" class="admin-input text-gray-400 line-through" value="1000000" id="p_old_price" oninput="updatePreview()">
                    </div>
                </div>
            </div>

            <!-- Block 2: Hình ảnh & Badge -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <h2 class="text-lg font-bold text-brand-navy mb-4 border-b border-slate-100 pb-2">Hình Ảnh & Nhãn</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="admin-label">Ảnh đại diện sản phẩm</label>
                        <div class="upload-box h-48 flex flex-col items-center justify-center cursor-pointer text-center p-4 bg-slate-50 hover:bg-white">
                            <i class="fas fa-cloud-upload-alt text-3xl text-brand-blue mb-2"></i>
                            <p class="text-sm font-bold text-slate-500">Kéo thả ảnh vào đây</p>
                            <p class="text-xs text-slate-400">hoặc bấm để chọn file</p>
                            <!-- Fake Preview Image loaded for demo -->
                            <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-chuan.png" class="h-20 mt-2 object-contain opacity-80" alt="Preview">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="admin-label">Badge (Nhãn nổi bật)</label>
                            <div class="flex gap-2">
                                <input type="text" class="admin-input" placeholder="VD: Best Seller, Mới..." value="Best Seller" id="p_badge" oninput="updatePreview()">
                                <input type="color" class="h-10 w-12 rounded cursor-pointer border-0 p-0" value="#ffd166" id="p_badge_color" onchange="updatePreview()">
                            </div>
                        </div>
                        <div>
                            <label class="admin-label">Trạng thái kho</label>
                            <select class="admin-input">
                                <option value="instock">✅ Còn hàng</option>
                                <option value="lowstock">⚠️ Sắp hết</option>
                                <option value="outofstock">❌ Hết hàng</option>
                            </select>
                        </div>
                         <div>
                            <label class="admin-label">Số lượng tồn kho</label>
                            <input type="number" class="admin-input" value="100">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Block 3: Nội dung chi tiết -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <h2 class="text-lg font-bold text-brand-navy mb-4 border-b border-slate-100 pb-2">Nội Dung Chi Tiết</h2>
                
                <div class="mb-4">
                    <label class="admin-label">Mô tả ngắn (Hiển thị trên thẻ)</label>
                    <textarea class="admin-input h-20 resize-none" id="p_desc" oninput="updatePreview()">Dinh dưỡng chuẩn cho bé dùng hàng ngày tại nhà.</textarea>
                </div>

                <div class="mb-4">
                    <label class="admin-label">Nút Mua Hàng (CTA)</label>
                    <input type="text" class="admin-input" value="Chọn Mua" id="p_cta" oninput="updatePreview()">
                </div>

                <!-- Removed Full Description Section -->
            </div>

        </div>

        <!-- RIGHT: LIVE PREVIEW -->
        <div class="lg:w-1/3">
            <div class="sticky top-8">
                <div class="flex items-center gap-2 mb-4">
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Xem Trước (Live Preview)</h3>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold">Real-time</span>
                </div>
                
                <!-- PREVIEW CARD COMPONENT -->
                <div class="preview-card bg-white p-4 relative group max-w-sm mx-auto shadow-md">
                    
                    <!-- Badge -->
                    <div class="absolute top-4 left-4 z-10">
                        <span id="preview_badge" class="text-brand-navy text-xs font-bold px-2 py-1 rounded shadow-sm" style="background-color: #ffd166;">Best Seller</span>
                    </div>
                    
                    <!-- Wishlist Icon (Static) -->
                    <div class="absolute top-4 right-4 z-10">
                         <button class="w-8 h-8 rounded-full bg-white text-gray-400 shadow-sm flex items-center justify-center"><i class="far fa-heart"></i></button>
                    </div>
                    
                    <!-- Image -->
                    <div class="bg-gray-50 rounded-xl p-6 mb-4 h-64 flex items-center justify-center overflow-hidden">
                        <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-chuan.png" 
                             alt="Product" 
                             class="w-40 h-auto object-contain drop-shadow-lg transition duration-500 hover:scale-110">
                    </div>
                    
                    <div class="text-center">
                        <!-- Ratings -->
                        <div class="flex justify-center gap-1 text-brand-gold text-xs mb-2">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <span class="text-gray-400 ml-1">(0)</span>
                        </div>
                        
                        <!-- Name -->
                        <h3 class="text-lg font-bold text-brand-navy mb-1"><a href="#" id="preview_name" class="hover:text-brand-blue transition">CareMIL Hộp Lớn 800g</a></h3>
                        
                        <!-- Short Desc -->
                        <p class="text-xs text-gray-500 mb-3 line-clamp-2" id="preview_desc">Dinh dưỡng chuẩn cho bé dùng hàng ngày tại nhà.</p>
                        
                        <!-- Price -->
                        <div class="flex items-center justify-center gap-2 mb-4">
                            <span class="text-xl font-bold text-brand-pink" id="preview_price">850.000đ</span>
                            <span class="text-sm text-gray-400 line-through" id="preview_old_price">1.000.000đ</span>
                        </div>
                        
                        <!-- CTA Button -->
                        <button class="w-full bg-brand-navy text-white font-bold py-2.5 rounded-xl shadow-lg flex items-center justify-center gap-2 hover:bg-brand-blue transition">
                            <i class="fas fa-shopping-bag"></i> <span id="preview_cta">Chọn Mua</span>
                        </button>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100 text-xs text-slate-500">
                    <p class="font-bold text-brand-navy mb-1"><i class="fas fa-info-circle"></i> Lưu ý:</p>
                    <p>Hình ảnh xem trước chỉ mang tính chất tham khảo. Giao diện thực tế có thể thay đổi tùy thuộc vào thiết bị của người dùng.</p>
                </div>
            </div>
        </div>

    </main>

    <!-- SCRIPT FOR PREVIEW -->
    <script>
        function formatCurrency(value) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
        }

        function updatePreview() {
            // Get values
            const name = document.getElementById('p_name').value;
            const price = document.getElementById('p_price').value;
            const oldPrice = document.getElementById('p_old_price').value;
            const badge = document.getElementById('p_badge').value;
            const badgeColor = document.getElementById('p_badge_color').value;
            const desc = document.getElementById('p_desc').value;
            const cta = document.getElementById('p_cta').value;

            // Update DOM
            document.getElementById('preview_name').innerText = name || 'Tên sản phẩm';
            document.getElementById('preview_desc').innerText = desc || 'Mô tả ngắn sản phẩm...';
            document.getElementById('preview_price').innerText = price ? parseInt(price).toLocaleString('vi-VN') + 'đ' : '0đ';
            
            const oldPriceEl = document.getElementById('preview_old_price');
            if (oldPrice && parseInt(oldPrice) > parseInt(price)) {
                oldPriceEl.innerText = parseInt(oldPrice).toLocaleString('vi-VN') + 'đ';
                oldPriceEl.style.display = 'inline';
            } else {
                oldPriceEl.style.display = 'none';
            }

            const badgeEl = document.getElementById('preview_badge');
            if (badge) {
                badgeEl.innerText = badge;
                badgeEl.style.backgroundColor = badgeColor;
                // Auto adjust text color for contrast (simple logic)
                badgeEl.style.color = '#1a4f8a'; // Default navy
                badgeEl.parentElement.style.display = 'block';
            } else {
                badgeEl.parentElement.style.display = 'none';
            }

            document.getElementById('preview_cta').innerText = cta || 'Mua Ngay';
        }
        
        // Init preview on load
        window.addEventListener('DOMContentLoaded', updatePreview);
    </script>
</body>
</html>