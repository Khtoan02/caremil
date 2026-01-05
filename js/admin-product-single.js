/* global wp, caremilProductSingle */
(function () {
    const { useState, useEffect } = wp.element;
    const apiFetch = wp.apiFetch;
    apiFetch.use(apiFetch.createNonceMiddleware(caremilProductSingle.nonce));

    const placeholderImage = caremilProductSingle.placeholderImage;

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

    function App() {
        const [form, setForm] = useState(emptyForm());
        const [categories, setCategories] = useState([]);
        const [loading, setLoading] = useState(true);
        const [saving, setSaving] = useState(false);
        const [deleting, setDeleting] = useState(false);
        const [message, setMessage] = useState('');
        const [error, setError] = useState('');

        useEffect(() => {
            load();
        }, []);

        async function load() {
            setLoading(true);
            setError('');
            try {
                const restPath = `${caremilProductSingle.namespace}/${caremilProductSingle.restBase}`;
                const [cats, product] = await Promise.all([
                    apiFetch({ path: addParamsPath(caremilProductSingle.catRest, { per_page: 100, hide_empty: false }) }),
                    caremilProductSingle.postId
                        ? apiFetch({ path: addParamsPath(`/${restPath.replace(/^\/+/, '')}/${caremilProductSingle.postId}`, { _embed: 1 }) })
                        : Promise.resolve(null),
                ]);
                setCategories(cats);
                if (product) {
                    setForm(normalizeProduct(product));
                } else {
                    setForm(emptyForm());
                }
            } catch (e) {
                setError('Không tải được dữ liệu. Kiểm tra quyền hoặc kết nối.');
            } finally {
                setLoading(false);
            }
        }

        function normalizeProduct(p) {
            const embedded = p._embedded || {};
            const media = embedded['wp:featuredmedia'] && embedded['wp:featuredmedia'][0];
            return {
                id: p.id,
                title: p.title?.rendered || '',
                price: p.meta?.caremil_price || '',
                old_price: p.meta?.caremil_old_price || '',
                badge: p.meta?.caremil_badge || '',
                badge_class: p.meta?.caremil_badge_class || 'bg-brand-gold text-brand-navy',
                short_desc: p.meta?.caremil_short_desc || '',
                rating: p.meta?.caremil_rating || 5,
                rating_count: p.meta?.caremil_rating_count || 0,
                button_label: p.meta?.caremil_button_label || 'Chọn Mua',
                button_url: p.meta?.caremil_button_url || '',
                category: (p.caremil_product_cat || [])[0] || '',
                featured_media: p.featured_media || 0,
                featured_url: media?.source_url || placeholderImage,
            };
        }

        function addParamsPath(base, params) {
            const root = caremilProductSingle.restUrlRoot.replace(/\/+$/, '') + '/';
            const url = new URL(base, root);
            Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));
            // return path+query relative to host
            return url.pathname + url.search;
        }

        async function save(e) {
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
                let result;
                const restPath = `${caremilProductSingle.namespace}/${caremilProductSingle.restBase}`;
                const basePath = `/${restPath.replace(/^\/+/, '')}`;

                if (form.id) {
                    result = await apiFetch({ path: `${basePath}/${form.id}`, method: 'POST', data: payload });
                    setMessage('Đã lưu sản phẩm.');
                    setForm(normalizeProduct(result));
                } else {
                    result = await apiFetch({ path: basePath, method: 'POST', data: payload });
                    window.location.href = `${caremilProductSingle.redirectEditBase}${result.id}`;
                    return;
                }
            } catch (err) {
                setError('Lưu không thành công. Vui lòng thử lại.');
            } finally {
                setSaving(false);
            }
        }

        async function deletePost() {
            if (!form.id) return;
            if (!window.confirm('Xóa sản phẩm này?')) return;
            setDeleting(true);
            setError('');
            setMessage('');
            try {
                const restPath = `${caremilProductSingle.namespace}/${caremilProductSingle.restBase}`;
                const basePath = `/${restPath.replace(/^\/+/, '')}`;
                await apiFetch({ path: `${basePath}/${form.id}`, method: 'DELETE', data: { force: true } });
                window.location.href = 'edit.php?post_type=caremil_product';
            } catch (err) {
                setError('Không thể xóa sản phẩm.');
            } finally {
                setDeleting(false);
            }
        }

        function openMedia() {
            const frame = wp.media({ title: 'Chọn ảnh sản phẩm', multiple: false });
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

        return wp.element.createElement('div', { className: 'p-6 bg-white border border-slate-200 rounded-2xl shadow-sm' },
            wp.element.createElement('div', { className: 'flex items-center justify-between mb-4' },
                wp.element.createElement('div', null,
                    wp.element.createElement('h1', { className: 'text-xl font-bold text-slate-800' }, form.id ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới'),
                    wp.element.createElement('p', { className: 'text-sm text-slate-500' }, 'Nhập thông tin Sản phẩm.')
                ),
                wp.element.createElement('div', { className: 'flex gap-2' },
                    wp.element.createElement('button', {
                        type: 'button',
                        className: 'px-3 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm',
                        onClick: load,
                        disabled: loading,
                    }, loading ? 'Đang tải...' : 'Làm mới')
                )
            ),
            message && wp.element.createElement('div', { className: 'mb-3 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm' }, message),
            error && wp.element.createElement('div', { className: 'mb-3 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm' }, error),
            loading
                ? wp.element.createElement('div', { className: 'text-slate-500 text-sm' }, 'Đang tải...')
                : wp.element.createElement('form', { onSubmit: save, className: 'grid grid-cols-1 lg:grid-cols-3 gap-6' },
                    wp.element.createElement('div', { className: 'lg:col-span-2 space-y-4' },
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
                    wp.element.createElement('div', { className: 'space-y-4' },
                        wp.element.createElement('div', { className: 'space-y-2' },
                            wp.element.createElement('div', { className: 'text-sm font-semibold text-slate-700' }, 'Ảnh đại diện'),
                            wp.element.createElement('div', { className: 'border border-dashed border-slate-200 rounded-xl p-3 text-center' },
                                wp.element.createElement('img', {
                                    src: form.featured_url || placeholderImage,
                                    alt: 'Ảnh sản phẩm',
                                    className: 'w-full h-44 object-cover rounded-lg mb-3 border border-slate-200',
                                }),
                                wp.element.createElement('button', {
                                    type: 'button',
                                    className: 'px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm hover:bg-slate-50',
                                    onClick: openMedia,
                                }, 'Chọn / đổi ảnh')
                            )
                        ),
                        wp.element.createElement('div', { className: 'flex flex-wrap gap-3' },
                            wp.element.createElement('button', {
                                type: 'submit',
                                className: 'px-5 py-2 rounded-lg bg-brand-navy text-white hover:bg-brand-blue shadow text-sm',
                                disabled: saving,
                            }, saving ? 'Đang lưu...' : 'Lưu sản phẩm'),
                            form.id && wp.element.createElement('button', {
                                type: 'button',
                                className: 'px-4 py-2 rounded-lg bg-white border border-red-200 text-red-600 hover:bg-red-50 text-sm',
                                onClick: deletePost,
                                disabled: deleting,
                            }, deleting ? 'Đang xóa...' : 'Xóa'),
                            form.id && wp.element.createElement('a', {
                                href: form.id ? `post.php?action=edit&post=${form.id}` : '#',
                                className: 'text-xs text-slate-500 self-center',
                            }, `ID: ${form.id}`)
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

    wp.element.render(wp.element.createElement(App), document.getElementById('caremil-product-single-app'));
})();

