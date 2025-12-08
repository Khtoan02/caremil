<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Sản Phẩm - CareMIL Admin</title>
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
        body { background-color: #f8fafc; }
        
        /* Table Styles */
        .admin-table th {
            text-align: left;
            padding: 16px;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            background-color: #f1f5f9;
            border-bottom: 2px solid #e2e8f0;
        }
        .admin-table td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover { background-color: #f8fafc; }

        /* Status Badges */
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .status-instock { background-color: #dcfce7; color: #166534; }
        .status-low { background-color: #fef9c3; color: #854d0e; }
        .status-out { background-color: #fee2e2; color: #991b1b; }

        /* Action Button */
        .action-btn {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            color: #64748b;
        }
        .action-btn:hover { background-color: #e2e8f0; color: #1e293b; }
        .action-btn.edit:hover { background-color: #e0f2fe; color: #0284c7; }
        .action-btn.delete:hover { background-color: #fee2e2; color: #dc2626; }
    </style>
</head>
<body class="font-sans text-slate-600">

    <!-- MAIN CONTENT (Removed Header, Adjusted Padding) -->
    <main class="container mx-auto px-4 py-8">
        
        <!-- 1. PAGE HEADER & STATS -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 mb-1">Quản Lý Sản Phẩm</h1>
                <p class="text-sm text-slate-500">Quản lý danh sách, tồn kho và giá bán.</p>
            </div>
            <a href="caremil_admin_product.html" class="bg-brand-navy text-white px-6 py-2.5 rounded-xl font-bold shadow-lg hover:bg-brand-blue transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Thêm Sản Phẩm
            </a>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-blue-50 text-brand-blue flex items-center justify-center text-lg"><i class="fas fa-box"></i></div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Tổng Sản Phẩm</p>
                    <p class="text-xl font-bold text-slate-700">12</p>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-lg"><i class="fas fa-check"></i></div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Đang Bán</p>
                    <p class="text-xl font-bold text-slate-700">10</p>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-lg"><i class="fas fa-exclamation"></i></div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Sắp Hết</p>
                    <p class="text-xl font-bold text-slate-700">1</p>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-lg"><i class="fas fa-times-circle"></i></div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Hết Hàng</p>
                    <p class="text-xl font-bold text-slate-700">1</p>
                </div>
            </div>
        </div>

        <!-- 2. FILTER BAR -->
        <div class="bg-white p-4 rounded-t-2xl border-b border-slate-100 flex flex-col md:flex-row gap-4 justify-between items-center shadow-sm z-10 relative">
            
            <!-- Search -->
            <div class="relative w-full md:w-96">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" placeholder="Tìm kiếm sản phẩm..." class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-blue-50 transition">
            </div>

            <!-- Filters -->
            <div class="flex gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0">
                <select class="px-4 py-2 border border-slate-200 rounded-lg text-sm bg-white text-slate-600 font-bold focus:outline-none cursor-pointer hover:bg-slate-50">
                    <option>Tất cả danh mục</option>
                    <option>Hộp thiếc</option>
                    <option>Gói nhỏ</option>
                    <option>Combo</option>
                </select>
                <select class="px-4 py-2 border border-slate-200 rounded-lg text-sm bg-white text-slate-600 font-bold focus:outline-none cursor-pointer hover:bg-slate-50">
                    <option>Trạng thái: Tất cả</option>
                    <option>Còn hàng</option>
                    <option>Hết hàng</option>
                </select>
            </div>
        </div>

        <!-- 3. DATA TABLE -->
        <div class="bg-white rounded-b-2xl shadow-sm border border-slate-100 overflow-hidden overflow-x-auto">
            <table class="w-full admin-table min-w-[800px]">
                <thead>
                    <tr>
                        <th class="w-16"><input type="checkbox" class="rounded border-gray-300"></th>
                        <th class="w-80">Sản Phẩm</th>
                        <th>Phân Loại</th>
                        <th>Giá Bán</th>
                        <th>Tồn Kho</th>
                        <th>Trạng Thái</th>
                        <th class="text-right w-32">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Item 1 -->
                    <tr>
                        <td><input type="checkbox" class="rounded border-gray-300"></td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-slate-50 border border-slate-100 p-1">
                                    <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Care-Milk-tach-nen-chuan.png" class="w-full h-full object-contain">
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">CareMIL Hộp Lớn 800g</p>
                                    <p class="text-xs text-slate-400">SKU: CM-800G</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold">Hộp thiếc</span></td>
                        <td><span class="font-bold text-brand-pink">850.000đ</span></td>
                        <td>128</td>
                        <td><span class="status-badge status-instock"><i class="fas fa-check-circle"></i> Còn hàng</span></td>
                        <td class="text-right">
                            <div class="flex justify-end gap-1">
                                <button class="action-btn edit" title="Sửa"><i class="fas fa-pen"></i></button>
                                <button class="action-btn delete" title="Xóa"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </td>
                    </tr>

                    <!-- Item 2 -->
                    <tr>
                        <td><input type="checkbox" class="rounded border-gray-300"></td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-slate-50 border border-slate-100 p-1">
                                    <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Goi-sua.png" class="w-full h-full object-contain">
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">Hộp 10 Gói Tiện Lợi</p>
                                    <p class="text-xs text-slate-400">SKU: CM-36G-BOX10</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold">Gói 36g</span></td>
                        <td><span class="font-bold text-brand-pink">350.000đ</span></td>
                        <td>50</td>
                        <td><span class="status-badge status-instock"><i class="fas fa-check-circle"></i> Còn hàng</span></td>
                        <td class="text-right">
                            <div class="flex justify-end gap-1">
                                <button class="action-btn edit"><i class="fas fa-pen"></i></button>
                                <button class="action-btn delete"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </td>
                    </tr>

                    <!-- Item 3 -->
                    <tr>
                        <td><input type="checkbox" class="rounded border-gray-300"></td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-yellow-50 border border-yellow-100 p-1 flex items-center justify-center">
                                    <i class="fas fa-gift text-brand-gold text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">Combo 2 Hộp Lớn</p>
                                    <p class="text-xs text-slate-400">SKU: COMBO-2X800</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold">Combo</span></td>
                        <td><span class="font-bold text-brand-pink">1.650.000đ</span></td>
                        <td>5</td>
                        <td><span class="status-badge status-low"><i class="fas fa-exclamation-triangle"></i> Sắp hết</span></td>
                        <td class="text-right">
                            <div class="flex justify-end gap-1">
                                <button class="action-btn edit"><i class="fas fa-pen"></i></button>
                                <button class="action-btn delete"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </td>
                    </tr>

                     <!-- Item 4 -->
                     <tr class="opacity-60 bg-slate-50">
                        <td><input type="checkbox" class="rounded border-gray-300" disabled></td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-slate-50 border border-slate-100 p-1 grayscale">
                                    <img src="https://caremil.dawnbridge.vn/wp-content/uploads/2025/12/Goi-sua.png" class="w-full h-full object-contain">
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">Gói Lẻ Dùng Thử</p>
                                    <p class="text-xs text-slate-400">SKU: CM-SAMPLE</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="bg-slate-200 text-slate-500 px-2 py-1 rounded text-xs font-bold">Gói lẻ</span></td>
                        <td><span class="font-bold text-slate-500">40.000đ</span></td>
                        <td>0</td>
                        <td><span class="status-badge status-out"><i class="fas fa-times-circle"></i> Hết hàng</span></td>
                        <td class="text-right">
                            <div class="flex justify-end gap-1">
                                <button class="action-btn edit"><i class="fas fa-pen"></i></button>
                                <button class="action-btn delete"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- 4. PAGINATION -->
        <div class="flex justify-between items-center mt-6 text-sm text-slate-500">
            <p>Hiển thị <strong>1-4</strong> trong tổng số <strong>12</strong> sản phẩm</p>
            <div class="flex gap-2">
                <button class="w-8 h-8 rounded border border-slate-200 hover:bg-white hover:text-brand-blue disabled:opacity-50" disabled><i class="fas fa-chevron-left"></i></button>
                <button class="w-8 h-8 rounded bg-brand-navy text-white font-bold">1</button>
                <button class="w-8 h-8 rounded border border-slate-200 hover:bg-white hover:text-brand-blue">2</button>
                <button class="w-8 h-8 rounded border border-slate-200 hover:bg-white hover:text-brand-blue">3</button>
                <button class="w-8 h-8 rounded border border-slate-200 hover:bg-white hover:text-brand-blue"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>

    </main>

</body>
</html>