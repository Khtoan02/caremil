/* global wp, caremilProductAdmin */
(function () {
    const { useState, useEffect, useMemo } = wp.element;
    const apiFetch = wp.apiFetch;

    apiFetch.use(apiFetch.createNonceMiddleware(caremilProductAdmin.nonce));

    const placeholderImage = caremilProductAdmin.placeholderImage;

    function emptyForm() {
        return {
            id: null,
            title: '',
            price: '',
            old_price: '',
            badge: '',
            badge_class: 'bg-brand-gold text-brand-navy',
            short_desc: '',
            rating: 5,
            rating_count: 0,
            button_label: 'Chọn Mua',
            button_url: '',
            category: '',
            featured_media: 0,
            featured_url: placeholderImage,
        };
    }

    function ProductAdminApp() {
        const [loading, setLoading] = useState(true);
        const [saving, setSaving] = useState(false);
        const [deleting, setDeleting] = useState(false);
        const [products, setProducts] = useState([]);
        const [categories, setCategories] = useState([]);
        const [selectedId, setSelectedId] = useState(null);
        const [form, setForm] = useState(emptyForm());
        const [message, setMessage] = useState('');
        const [error, setError] = useState('');
        const [search, setSearch] = useState('');
        const [filterCat, setFilterCat] = useState('all');

        useEffect(() => {
            loadData();
        }, []);

        async function loadData() {
            setLoading(true);
            setError('');
            try {
                const [cats, prods] = await Promise.all([
                    apiFetch({ url: addParamsUrl(caremilProductAdmin.catRest, { per_page: 100, hide_empty: false }) }),
                    apiFetch({ url: addParamsUrl(caremilProductAdmin.restUrl, { per_page: 100, _embed: 1, orderby: 'menu_order', order: 'asc' }) }),
                ]);
                setCategories(cats);
                setProducts(normalizeProducts(prods));
            } catch (e) {
                setError('Không tải được dữ liệu. Hãy thử lại hoặc kiểm tra quyền.');
            } finally {
                setLoading(false);
            }
        }

        function normalizeProducts(data) {
            return (data || []).map((p) => {
                const embedded = p._embedded || {};
                const media = embedded['wp:featuredmedia'] && embedded['wp:featuredmedia'][0];
                return {
                    id: p.id,
                    title: p.title?.rendered || '(Chưa đặt tên)',
                    category_ids: p.caremil_product_cat || [],
                    price: p.meta?.caremil_price || '',
                    old_price: p.meta?.caremil_old_price || '',
                    badge: p.meta?.caremil_badge || '',
                    badge_class: p.meta?.caremil_badge_class || 'bg-brand-gold text-brand-navy',
                    short_desc: p.meta?.caremil_short_desc || '',
                    rating: p.meta?.caremil_rating || 5,
                    rating_count: p.meta?.caremil_rating_count || 0,
                    button_label: p.meta?.caremil_button_label || 'Chọn Mua',
                    button_url: p.meta?.caremil_button_url || '',
                    featured_media: p.featured_media || 0,
                    featured_url: media?.source_url || placeholderImage,
                };
            });
        }

        function addParamsUrl(base, params) {
            const url = new URL(base, window.location.origin);
            Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));
            return url.toString();
        }

        function selectProduct(prod) {
            setSelectedId(prod.id);
            setForm({
                id: prod.id,
                title: prod.title,
                price: prod.price,
                old_price: prod.old_price,
                badge: prod.badge,
                badge_class: prod.badge_class,
                short_desc: prod.short_desc,
                rating: prod.rating,
                rating_count: prod.rating_count,
                button_label: prod.button_label,
                button_url: prod.button_url,
                category: prod.category_ids?.[0] || '',
                featured_media: prod.featured_media || 0,
                featured_url: prod.featured_url || placeholderImage,
            });
            setMessage('');
            setError('');
        }

        function newProduct() {
            setSelectedId(null);
            setForm(emptyForm());
            setMessage('');
            setError('');
        }

        async function saveProduct(e) {
            e?.preventDefault();
            if (!form.title) {
                setError('Vui lòng nhập tên sản phẩm.');
                return;
            }
            setSaving(true);
            setError('');
            setMessage('');
            const payload = {
                title: form.title,
                status: 'publish',
                meta: {
                    caremil_price: form.price,
                    caremil_old_price: form.old_price,
                    caremil_badge: form.badge,
                    caremil_badge_class: form.badge_class,
                    caremil_short_desc: form.short_desc,
                    caremil_rating: parseFloat(form.rating || 0),
                    caremil_rating_count: parseInt(form.rating_count || 0, 10),
                    caremil_button_label: form.button_label,
                    caremil_button_url: form.button_url,
                },
                caremil_product_cat: form.category ? [parseInt(form.category, 10)] : [],
                featured_media: form.featured_media || undefined,
                content: form.short_desc,
            };

            try {
                const path = form.id ? `${caremilProductAdmin.restUrl}/${form.id}` : caremilProductAdmin.restUrl;
                const method = form.id ? 'POST' : 'POST'; // WP uses POST for create; PUT also works but keep POST.
                const result = await apiFetch({ url: path, method, data: payload });
                const updatedList = form.id
                    ? products.map((p) => (p.id === form.id ? normalizeProducts([result])[0] : p))
                    : [normalizeProducts([result])[0], ...products];
                setProducts(updatedList);
                selectProduct(normalizeProducts([result])[0]);
                setMessage('Đã lưu sản phẩm.');
            } catch (err) {
                setError('Lưu không thành công. Vui lòng thử lại.');
            } finally {
                setSaving(false);
            }
        }

        async function deleteProduct() {
            if (!form.id) return;
            if (!window.confirm('Xóa sản phẩm này?')) return;
            setDeleting(true);
            setError('');
            setMessage('');
            try {
                await apiFetch({ url: `${caremilProductAdmin.restUrl}/${form.id}`, method: 'DELETE', data: { force: true } });
                setProducts(products.filter((p) => p.id !== form.id));
                newProduct();
                setMessage('Đã xóa sản phẩm.');
            } catch (err) {
                setError('Không thể xóa sản phẩm.');
            } finally {
                setDeleting(false);
            }
        }

        function openMedia() {
            const frame = wp.media({
                title: 'Chọn ảnh sản phẩm',
                multiple: false,
            });
            frame.on('select', () => {
                const attachment = frame.state().get('selection').first().toJSON();
                setForm((prev) => ({
                    ...prev,
                    featured_media: attachment.id,
                    featured_url: attachment.sizes?.medium?.url || attachment.url,
                }));
            });
            frame.open();
        }

        const filteredProducts = useMemo(() => {
            return products.filter((p) => {
                const matchSearch = !search || p.title.toLowerCase().includes(search.toLowerCase());
                const matchCat = filterCat === 'all' || (p.category_ids || []).includes(parseInt(filterCat, 10));
                return matchSearch && matchCat;
            });
        }, [products, search, filterCat]);

        return (
            wp.element.createElement('div', { className: 'p-6 bg-white/80 min-h-screen' },
                wp.element.createElement('div', { className: 'max-w-6xl mx-auto' },
                    wp.element.createElement('div', { className: 'flex items-center justify-between mb-6' },
                        wp.element.createElement('div', null,
                            wp.element.createElement('h1', { className: 'text-2xl font-bold text-slate-800' }, 'Trình quản lý sản phẩm CareMIL'),
                            wp.element.createElement('p', { className: 'text-sm text-slate-500' }, 'Tạo/sửa nhanh, tối ưu cho team marketing.')
                        ),
                        wp.element.createElement('div', { className: 'flex gap-3' },
                            wp.element.createElement('button', {
                                className: 'px-4 py-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 text-sm',
                                onClick: loadData,
                                disabled: loading,
                            }, loading ? 'Đang tải...' : 'Tải lại'),
                            wp.element.createElement('button', {
                                className: 'px-4 py-2 rounded-lg bg-brand-navy text-white hover:bg-brand-blue text-sm shadow',
                                onClick: newProduct,
                            }, 'Thêm sản phẩm')
                        )
                    ),

                    message && wp.element.createElement('div', { className: 'mb-4 p-3 rounded-lg bg-green-50 text-green-700 border border-green-200' }, message),
                    error && wp.element.createElement('div', { className: 'mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200' }, error),

                    wp.element.createElement('div', { className: 'grid grid-cols-1 lg:grid-cols-3 gap-6' },
                        // List
                        wp.element.createElement('div', { className: 'bg-white border border-slate-200 rounded-2xl p-4 shadow-sm lg:col-span-1' },
                            wp.element.createElement('div', { className: 'flex gap-3 mb-4' },
                                wp.element.createElement('input', {
                                    type: 'text',
                                    className: 'w-full border border-slate-200 rounded-lg px-3 py-2 text-sm',
                                    placeholder: 'Tìm theo tên...',
                                    value: search,
                                    onChange: (e) => setSearch(e.target.value),
                                })
                            ),
                            wp.element.createElement('div', { className: 'flex flex-wrap gap-2 mb-4' },
                                wp.element.createElement('button', {
                                    className: `px-3 py-1 rounded-full text-xs border ${filterCat === 'all' ? 'bg-brand-navy text-white' : 'bg-white text-slate-700 border-slate-200'}`,
                                    onClick: () => setFilterCat('all'),
                                }, 'Tất cả'),
                                categories.map((cat) =>
                                    wp.element.createElement('button', {
                                        key: cat.id,
                                        className: `px-3 py-1 rounded-full text-xs border ${filterCat === String(cat.id) ? 'bg-brand-navy text-white' : 'bg-white text-slate-700 border-slate-200'}`,
                                        onClick: () => setFilterCat(String(cat.id)),
                                    }, cat.name)
                                )
                            ),
                            loading ? wp.element.createElement('div', { className: 'text-slate-500 text-sm' }, 'Đang tải...') :
                                wp.element.createElement('div', { className: 'space-y-2 max-h-[70vh] overflow-y-auto pr-2' },
                                    filteredProducts.map((p) =>
                                        wp.element.createElement('div', {
                                            key: p.id,
                                            onClick: () => selectProduct(p),
                                            className: `flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:border-brand-blue ${selectedId === p.id ? 'border-brand-blue bg-blue-50/40' : 'border-slate-200 bg-white'}`,
                                        },
                                            wp.element.createElement('img', {
                                                src: p.featured_url || placeholderImage,
                                                alt: p.title,
                                                className: 'w-12 h-12 rounded-lg object-cover border border-slate-200',
                                            }),
                                            wp.element.createElement('div', { className: 'flex-1' },
                                                wp.element.createElement('div', { className: 'font-semibold text-sm text-slate-800' }, p.title),
                                                wp.element.createElement('div', { className: 'text-xs text-slate-500' }, p.price || 'Chưa đặt giá')
                                            )
                                        )
                                    ),
                                    filteredProducts.length === 0 && wp.element.createElement('div', { className: 'text-slate-500 text-sm' }, 'Không có sản phẩm.')
                                )
                        ),

                        // Form
                        wp.element.createElement('div', { className: 'bg-white border border-slate-200 rounded-2xl p-6 shadow-sm lg:col-span-2' },
                            wp.element.createElement('form', { onSubmit: saveProduct, className: 'space-y-5' },
                                wp.element.createElement('div', { className: 'grid grid-cols-1 md:grid-cols-3 gap-4 items-start' },
                                    wp.element.createElement('div', { className: 'md:col-span-2 space-y-4' },
                                        inputField('Tên sản phẩm', 'text', form.title, (v) => setForm((prev) => ({ ...prev, title: v })), true),
                                        textareaField('Mô tả ngắn (hiển thị ngoài trang)', form.short_desc, (v) => setForm((prev) => ({ ...prev, short_desc: v }))),
                                        selectField('Nhóm sản phẩm', form.category, (v) => setForm((prev) => ({ ...prev, category: v })), categories),
                                        twoCols(
                                            inputField('Giá bán', 'text', form.price, (v) => setForm((prev) => ({ ...prev, price: v }))),
                                            inputField('Giá gạch (tuỳ chọn)', 'text', form.old_price, (v) => setForm((prev) => ({ ...prev, old_price: v })))
                                        ),
                                        twoCols(
                                            inputField('Badge (ví dụ: Best Seller)', 'text', form.badge, (v) => setForm((prev) => ({ ...prev, badge: v }))),
                                            inputField('Class màu badge', 'text', form.badge_class, (v) => setForm((prev) => ({ ...prev, badge_class: v })))
                                        ),
                                        twoCols(
                                            inputField('Rating (0-5)', 'number', form.rating, (v) => setForm((prev) => ({ ...prev, rating: v })), false, { step: '0.1', min: '0', max: '5' }),
                                            inputField('Số lượt đánh giá', 'number', form.rating_count, (v) => setForm((prev) => ({ ...prev, rating_count: v })), false, { min: '0' })
                                        ),
                                        twoCols(
                                            inputField('Nút CTA', 'text', form.button_label, (v) => setForm((prev) => ({ ...prev, button_label: v }))),
                                            inputField('Link CTA', 'url', form.button_url, (v) => setForm((prev) => ({ ...prev, button_url: v })))
                                        )
                                    ),
                                    wp.element.createElement('div', { className: 'space-y-3' },
                                        wp.element.createElement('div', { className: 'text-sm font-semibold text-slate-700' }, 'Ảnh đại diện'),
                                        wp.element.createElement('div', { className: 'border border-dashed border-slate-200 rounded-xl p-3 text-center' },
                                            wp.element.createElement('img', {
                                                src: form.featured_url || placeholderImage,
                                                alt: 'Ảnh sản phẩm',
                                                className: 'w-full h-40 object-cover rounded-lg mb-3 border border-slate-200',
                                            }),
                                            wp.element.createElement('button', {
                                                type: 'button',
                                                className: 'px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm hover:bg-slate-50',
                                                onClick: openMedia,
                                            }, 'Chọn / đổi ảnh')
                                        )
                                    )
                                ),
                                wp.element.createElement('div', { className: 'flex flex-wrap gap-3 items-center' },
                                    wp.element.createElement('button', {
                                        type: 'submit',
                                        className: 'px-5 py-2 rounded-lg bg-brand-navy text-white hover:bg-brand-blue shadow',
                                        disabled: saving,
                                    }, saving ? 'Đang lưu...' : 'Lưu sản phẩm'),
                                    form.id && wp.element.createElement('button', {
                                        type: 'button',
                                        className: 'px-4 py-2 rounded-lg bg-white border border-red-200 text-red-600 hover:bg-red-50',
                                        onClick: deleteProduct,
                                        disabled: deleting,
                                    }, deleting ? 'Đang xóa...' : 'Xóa'),
                                    form.id && wp.element.createElement('span', { className: 'text-xs text-slate-500' }, `ID: ${form.id}`)
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    function inputField(label, type, value, onChange, required = false, rest = {}) {
        return wp.element.createElement('div', { className: 'space-y-1' },
            wp.element.createElement('label', { className: 'text-xs font-semibold text-slate-600' }, label),
            wp.element.createElement('input', {
                type,
                value: value ?? '',
                required,
                onChange: (e) => onChange(e.target.value),
                className: 'w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:border-brand-blue focus:ring-1 focus:ring-brand-blue',
                ...rest,
            })
        );
    }

    function textareaField(label, value, onChange) {
        return wp.element.createElement('div', { className: 'space-y-1' },
            wp.element.createElement('label', { className: 'text-xs font-semibold text-slate-600' }, label),
            wp.element.createElement('textarea', {
                value: value ?? '',
                onChange: (e) => onChange(e.target.value),
                rows: 3,
                className: 'w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:border-brand-blue focus:ring-1 focus:ring-brand-blue',
            })
        );
    }

    function selectField(label, value, onChange, options) {
        return wp.element.createElement('div', { className: 'space-y-1' },
            wp.element.createElement('label', { className: 'text-xs font-semibold text-slate-600' }, label),
            wp.element.createElement('select', {
                value: value ?? '',
                onChange: (e) => onChange(e.target.value),
                className: 'w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:border-brand-blue focus:ring-1 focus:ring-brand-blue bg-white',
            },
                wp.element.createElement('option', { value: '' }, '— Chọn nhóm —'),
                options.map((opt) => wp.element.createElement('option', { key: opt.id, value: opt.id }, opt.name))
            )
        );
    }

    function twoCols(left, right) {
        return wp.element.createElement('div', { className: 'grid grid-cols-1 md:grid-cols-2 gap-4' }, left, right);
    }

    wp.element.render(
        wp.element.createElement(ProductAdminApp),
        document.getElementById('caremil-product-admin-app')
    );
})();

