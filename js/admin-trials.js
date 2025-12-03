(function () {
    if (typeof wp === 'undefined' || !document.getElementById('caremil-trial-admin-app')) {
        return;
    }

    const { createElement: h, Fragment, useState, useMemo, useEffect } = wp.element;

    const createIcon = (children) => {
        return function Icon({ size = 18, className = '' }) {
            return h(
                'svg',
                {
                    xmlns: 'http://www.w3.org/2000/svg',
                    fill: 'none',
                    viewBox: '0 0 24 24',
                    stroke: 'currentColor',
                    strokeWidth: 1.6,
                    className,
                    width: size,
                    height: size,
                },
                children.map((child, index) =>
                    h(child.tag || 'path', {
                        key: index,
                        strokeLinecap: child.strokeLinecap || 'round',
                        strokeLinejoin: child.strokeLinejoin || 'round',
                        ...child,
                    })
                )
            );
        };
    };

    const icons = {
        Users: createIcon([
            { d: 'M15 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2' },
            { d: 'M10 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z' },
            { d: 'M22 21v-2a4 4 0 0 0-3-3.87' },
            { d: 'M16 3.13a4 4 0 0 1 0 7.75' },
        ]),
        Search: createIcon([
            { d: 'm21 21-4.35-4.35' },
            { d: 'M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z' },
        ]),
        Download: createIcon([
            { d: 'M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4' },
            { d: 'M7 10l5 5 5-5' },
            { d: 'M12 15V3' },
        ]),
        Eye: createIcon([
            { d: 'M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z' },
            { d: 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z' },
        ]),
        Trash2: createIcon([
            { d: 'M3 6h18' },
            { d: 'm14 6-.34-2a2 2 0 0 0-2-2h-1.32a2 2 0 0 0-2 1.68L8 6' },
            { d: 'M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6' },
            { d: 'M10 11v6' },
            { d: 'M14 11v6' },
        ]),
        CheckCircle: createIcon([
            { tag: 'circle', cx: 12, cy: 12, r: 10 },
            { d: 'm9 12 2 2 4-4' },
        ]),
        XCircle: createIcon([
            { tag: 'circle', cx: 12, cy: 12, r: 10 },
            { d: 'm15 9-6 6' },
            { d: 'm9 9 6 6' },
        ]),
        Clock: createIcon([
            { tag: 'circle', cx: 12, cy: 12, r: 10 },
            { d: 'M12 6v6l3 3' },
        ]),
        ChevronLeft: createIcon([{ d: 'm15 18-6-6 6-6' }]),
        ChevronRight: createIcon([{ d: 'm9 18 6-6-6-6' }]),
        Phone: createIcon([
            { d: 'M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.18 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92Z' },
        ]),
        MapPin: createIcon([
            { d: 'M20.39 18.39 12 21l-8.39-2.61a2 2 0 0 1-1.39-2V5a2 2 0 0 1 1.39-2L12 0l8.39 3a2 2 0 0 1 1.39 2v10.39a2 2 0 0 1-1.39 2Z', strokeWidth: 1.2 },
            { tag: 'circle', cx: 12, cy: 10, r: 3 },
        ]),
        User: createIcon([
            { d: 'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2' },
            { tag: 'circle', cx: 12, cy: 7, r: 4 },
        ]),
        Activity: createIcon([
            { d: 'M22 12h-4l-3 8-4-16-3 8H2' },
        ]),
        ArrowUpRight: createIcon([
            { d: 'M7 17 17 7' },
            { d: 'M7 7h10v10' },
        ]),
        X: createIcon([
            { d: 'M18 6 6 18' },
            { d: 'm6 6 12 12' },
        ]),
    };

    const Icon = ({ name, size = 18, className = '' }) => {
        const Component = icons[name];
        return Component ? h(Component, { size, className }) : null;
    };

    const StatusBadge = ({ status }) => {
        const configs = {
            new: {
                label: 'Mới đăng ký',
                classes: 'bg-blue-50 text-blue-700 border-blue-200',
                icon: 'Clock',
            },
            contacted: {
                label: 'Đang liên hệ',
                classes: 'bg-amber-50 text-amber-700 border-amber-200',
                icon: 'Phone',
            },
            verified: {
                label: 'Đã gửi quà',
                classes: 'bg-emerald-50 text-emerald-700 border-emerald-200',
                icon: 'CheckCircle',
            },
            spam: {
                label: 'Spam/Hủy',
                classes: 'bg-red-50 text-red-700 border-red-200',
                icon: 'XCircle',
            },
        };

        const config = configs[status] || configs.new;

        return h(
            'span',
            {
                className: `inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border ${config.classes}`,
            },
            [
                h(Icon, { key: 'icon', name: config.icon, size: 12, className: 'mr-1.5' }),
                config.label,
            ]
        );
    };

    const StatCard = ({ title, value, subtext, icon, trend }) =>
        h(
            'div',
            { className: 'bg-white p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow' },
            [
                h(
                    'div',
                    { className: 'flex justify-between items-start mb-2' },
                    [
                        h(
                            'div',
                            { className: 'p-2 bg-blue-50 rounded-lg text-blue-600' },
                            h(Icon, { name: icon, size: 20 })
                        ),
                        trend
                            ? h(
                                  'span',
                                  { className: 'flex items-center text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full' },
                                  [
                                      h(Icon, { key: 'trend-icon', name: 'ArrowUpRight', size: 12, className: 'mr-0.5' }),
                                      trend,
                                  ]
                              )
                            : null,
                    ]
                ),
                h('div', { className: 'text-3xl font-bold text-gray-800 mb-1' }, value),
                h('div', { className: 'text-sm text-gray-500 font-medium' }, title),
                subtext ? h('div', { className: 'text-xs text-gray-400 mt-1' }, subtext) : null,
            ]
        );

    const Toast = ({ message }) =>
        h(
            'div',
            { className: 'fixed bottom-6 right-6 bg-gray-900 text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-bounce-up z-50' },
            [
                h(
                    'div',
                    { className: 'bg-emerald-500 rounded-full p-1' },
                    h(Icon, { name: 'CheckCircle', size: 14, className: 'text-white' })
                ),
                h('div', { className: 'text-sm font-medium' }, message),
            ]
        );

    const Drawer = ({ customer, onClose, onStatusChange, onDelete }) => {
        if (!customer) {
            return null;
        }

        const statusButtons = [
            { value: 'new', label: 'Mới đăng ký', dot: 'bg-blue-500', classes: 'border-blue-500 bg-blue-50', activeIcon: 'text-blue-500' },
            { value: 'contacted', label: 'Đang liên hệ', dot: 'bg-amber-500', classes: 'border-amber-500 bg-amber-50', activeIcon: 'text-amber-500' },
            { value: 'verified', label: 'Hoàn tất gửi quà', dot: 'bg-emerald-500', classes: 'border-emerald-500 bg-emerald-50', activeIcon: 'text-emerald-500' },
            { value: 'spam', label: 'Spam/Hủy', dot: 'bg-red-500', classes: 'border-red-500 bg-red-50', activeIcon: 'text-red-500' },
        ];

        return h(
            Fragment,
            null,
            [
                h('div', {
                    key: 'backdrop',
                    className: 'fixed inset-0 bg-gray-900/20 backdrop-blur-sm z-40',

                    onClick: onClose,
                    style: { zIndex: 99999 },
                }),
                h(
                    'div',
                    {
                        key: 'drawer',
                        className: `fixed top-0 right-0 h-full w-full sm:w-[480px] bg-white shadow-2xl transition-transform duration-300 ease-in-out translate-x-0`,
                        style: { zIndex: 100000 },
                    },
                    [
                        h(
                            'div',
                            { className: 'h-full flex flex-col' },
                            [
                                h(
                                    'div',
                                    { className: 'px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50' },
                                    [
                                        h('div', null, [
                                            h('h2', { className: 'text-xl font-bold text-gray-900' }, 'Chi tiết khách hàng'),
                                            h(
                                                'span',
                                                { className: 'text-sm text-gray-500' },
                                                `ID: #${customer.id} • Đăng ký: ${customer.registered_at}`
                                            ),
                                        ]),
                                        h(
                                            'button',
                                            {
                                                className: 'p-2 hover:bg-gray-200 rounded-full transition-colors text-gray-500',
                                                onClick: onClose,
                                            },
                                            h(Icon, { name: 'X', size: 20 })
                                        ),
                                    ]
                                ),
                                h(
                                    'div',
                                    { className: 'flex-1 overflow-y-auto p-6 space-y-8' },
                                    [
                                        h(
                                            'div',
                                            { className: 'space-y-4' },
                                            [
                                                h(
                                                    'div',
                                                    { className: 'flex items-center gap-2 text-sm font-semibold text-blue-600 uppercase tracking-wide' },
                                                    [h(Icon, { name: 'User', size: 16 }), 'Thông tin cá nhân']
                                                ),
                                                h(
                                                    'div',
                                                    { className: 'bg-white rounded-xl border border-gray-100 p-4 space-y-4 shadow-sm' },
                                                    [
                                                        h(
                                                            'div',
                                                            { className: 'grid grid-cols-2 gap-4' },
                                                            [
                                                                h('div', null, [
                                                                    h('label', { className: 'text-xs text-gray-400 block mb-1' }, 'Họ và tên'),
                                                                    h('div', { className: 'font-medium text-gray-900 text-lg' }, customer.name || '—'),
                                                                ]),
                                                                h('div', null, [
                                                                    h('label', { className: 'text-xs text-gray-400 block mb-1' }, 'Số điện thoại'),
                                                                    h(
                                                                        'div',
                                                                        { className: 'font-medium text-gray-900 flex items-center gap-2' },
                                                                        [
                                                                            h(Icon, { name: 'Phone', size: 14, className: 'text-gray-400' }),
                                                                            customer.phone,
                                                                        ]
                                                                    ),
                                                                ]),
                                                            ]
                                                        ),
                                                    ]
                                                ),
                                            ]
                                        ),
                                        h(
                                            'div',
                                            { className: 'space-y-4' },
                                            [
                                                h(
                                                    'div',
                                                    { className: 'flex items-center gap-2 text-sm font-semibold text-blue-600 uppercase tracking-wide' },
                                                    [h(Icon, { name: 'MapPin', size: 16 }), 'Địa chỉ nhận quà']
                                                ),
                                                h(
                                                    'div',
                                                    { className: 'bg-gray-50 rounded-xl p-4 border border-gray-100' },
                                                    h(
                                                        'div',
                                                        { className: 'flex items-start gap-3' },
                                                        [
                                                            h(Icon, { name: 'MapPin', size: 20, className: 'text-gray-400 mt-1 shrink-0' }),
                                                            h('div', null, [
                                                                h('div', { className: 'font-medium text-gray-900' }, customer.address || '—'),
                                                                h('div', { className: 'text-sm text-gray-500 mt-1' }, customer.city || ''),
                                                            ]),
                                                        ]
                                                    )
                                                ),
                                            ]
                                        ),
                                        h(
                                            'div',
                                            { className: 'space-y-4' },
                                            [
                                                h(
                                                    'div',
                                                    { className: 'flex items-center gap-2 text-sm font-semibold text-blue-600 uppercase tracking-wide' },
                                                    [h(Icon, { name: 'Activity', size: 16 }), 'Xử lý trạng thái']
                                                ),
                                                h(
                                                    'div',
                                                    { className: 'grid grid-cols-1 gap-3' },
                                                    statusButtons.map((btn) =>
                                                        h(
                                                            'button',
                                                            {
                                                                key: btn.value,
                                                                className: `relative p-3 rounded-lg border-2 text-left transition-all ${
                                                                    customer.status === btn.value
                                                                        ? `${btn.classes}`
                                                                        : 'border-gray-100 hover:border-blue-200'
                                                                }`,
                                                                onClick: () => onStatusChange(customer.id, btn.value),
                                                            },
                                                            [
                                                                h(
                                                                    'div',
                                                                    { className: 'flex items-center justify-between' },
                                                                    [
                                                                        h(
                                                                            'div',
                                                                            { className: 'flex items-center gap-2 font-medium text-gray-900' },
                                                                            [
                                                                                h('div', { className: `w-2 h-2 rounded-full ${btn.dot}` }),
                                                                                btn.label,
                                                                            ]
                                                                        ),
                                                                        customer.status === btn.value
                                                                            ? h(Icon, { name: 'CheckCircle', size: 16, className: btn.activeIcon })
                                                                            : null,
                                                                    ]
                                                                ),
                                                            ]
                                                        )
                                                    )
                                                ),
                                            ]
                                        ),
                                        h(
                                            'div',
                                            { className: 'pt-4 border-t border-gray-100' },
                                            [
                                                h('h4', { className: 'text-xs font-semibold text-gray-400 uppercase mb-3' }, 'Thông tin pháp lý'),
                                                h(
                                                    'div',
                                                    { className: 'space-y-2 text-sm' },
                                                    [
                                                        h(
                                                            'div',
                                                            { className: 'flex items-center gap-2 text-gray-600' },
                                                            [
                                                                h(Icon, {
                                                                    name: customer.consent_terms ? 'CheckCircle' : 'XCircle',
                                                                    size: 14,
                                                                    className: customer.consent_terms ? 'text-emerald-500' : 'text-red-500',
                                                                }),
                                                                'Đã đồng ý điều khoản chương trình',
                                                            ]
                                                        ),
                                                        h(
                                                            'div',
                                                            { className: 'flex items-center gap-2 text-gray-600' },
                                                            [
                                                                h(Icon, {
                                                                    name: customer.consent_privacy ? 'CheckCircle' : 'XCircle',
                                                                    size: 14,
                                                                    className: customer.consent_privacy ? 'text-emerald-500' : 'text-red-500',
                                                                }),
                                                                'Đã chấp thuận chính sách quyền riêng tư',
                                                            ]
                                                        ),
                                                    ]
                                                ),
                                            ]
                                        ),
                                    ]
                                ),
                                h(
                                    'div',
                                    { className: 'p-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center' },
                                    [
                                        h(
                                            'button',
                                            {
                                                className: 'text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                                                onClick: () => onDelete(customer.id),
                                            },
                                            'Xóa dữ liệu'
                                        ),
                                        h(
                                            'button',
                                            {
                                                className: 'bg-gray-900 hover:bg-gray-800 text-white px-6 py-2 rounded-lg text-sm font-medium shadow-lg shadow-gray-200 transition-all',
                                                onClick: onClose,
                                            },
                                            'Đóng'
                                        ),
                                    ]
                                ),
                            ]
                        ),
                    ]
                ),
            ]
        );
    };

    function App() {
        const [customers, setCustomers] = useState([]);
        const [loading, setLoading] = useState(true);
        const [error, setError] = useState('');
        const [filterStatus, setFilterStatus] = useState('all');
        const [searchTerm, setSearchTerm] = useState('');
        const [selectedCustomer, setSelectedCustomer] = useState(null);
        const [toastMessage, setToastMessage] = useState('');

        const fetchCustomers = async () => {
            setLoading(true);
            setError('');
            try {
                const response = await fetch(caremilTrialAdmin.restUrl, {
                    headers: {
                        'X-WP-Nonce': caremilTrialAdmin.nonce,
                    },
                });
                if (!response.ok) {
                    throw new Error('Không thể tải dữ liệu.');
                }
                const data = await response.json();
                setCustomers(data);
            } catch (err) {
                setError(err.message || 'Có lỗi xảy ra.');
            } finally {
                setLoading(false);
            }
        };

        useEffect(() => {
            fetchCustomers();
        }, []);

        const filteredCustomers = useMemo(() => {
            return customers.filter((customer) => {
                const matchesStatus = filterStatus === 'all' || customer.status === filterStatus;
                const keyword = searchTerm.trim().toLowerCase();
                if (!keyword) {
                    return matchesStatus;
                }
                const matchesSearch =
                    (customer.name || '').toLowerCase().includes(keyword) ||
                    (customer.phone || '').toLowerCase().includes(keyword) ||
                    (customer.city || '').toLowerCase().includes(keyword);
                return matchesStatus && matchesSearch;
            });
        }, [customers, filterStatus, searchTerm]);

        const stats = useMemo(() => {
            const total = customers.length;
            const fresh = customers.filter((c) => c.status === 'new').length;
            const verified = customers.filter((c) => c.status === 'verified').length;
            return { total, fresh, verified };
        }, [customers]);

        const showToast = (message) => {
            setToastMessage(message);
            setTimeout(() => setToastMessage(''), 3000);
        };

        const handleStatusChange = async (id, newStatus) => {
            try {
                const response = await fetch(`${caremilTrialAdmin.restUrl}/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': caremilTrialAdmin.nonce,
                    },
                    body: JSON.stringify({ status: newStatus }),
                });
                if (!response.ok) {
                    throw new Error('Không thể cập nhật trạng thái.');
                }
                const updated = await response.json();
                setCustomers((prev) => prev.map((customer) => (customer.id === updated.id ? updated : customer)));
                if (selectedCustomer && selectedCustomer.id === updated.id) {
                    setSelectedCustomer(updated);
                }
                showToast('Cập nhật trạng thái thành công!');
            } catch (err) {
                alert(err.message || 'Có lỗi xảy ra.');
            }
        };

        const handleDelete = async (id) => {
            if (!confirm('Bạn có chắc chắn muốn xóa khách hàng này không?')) {
                return;
            }
            try {
                const response = await fetch(`${caremilTrialAdmin.restUrl}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-WP-Nonce': caremilTrialAdmin.nonce,
                    },
                });
                if (!response.ok) {
                    throw new Error('Không thể xóa dữ liệu.');
                }
                setCustomers((prev) => prev.filter((customer) => customer.id !== id));
                if (selectedCustomer && selectedCustomer.id === id) {
                    setSelectedCustomer(null);
                }
                showToast('Đã xóa khách hàng.');
            } catch (err) {
                alert(err.message || 'Có lỗi xảy ra.');
            }
        };

        const handleExport = () => {
            if (!customers.length) {
                alert('Chưa có dữ liệu để xuất.');
                return;
            }
            const headers = ['ID', 'Tên', 'Điện thoại', 'Thành phố', 'Địa chỉ', 'Trạng thái', 'Ngày đăng ký'];
            const rows = customers.map((c) => [
                c.id,
                `"${(c.name || '').replace(/"/g, '""')}"`,
                c.phone,
                c.city,
                `"${(c.address || '').replace(/"/g, '""')}"`,
                c.status,
                c.registered_at,
            ]);
            const csv = [headers.join(','), ...rows.map((row) => row.join(','))].join('\n');
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `caremil-trials-${Date.now()}.csv`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        };

        const renderTableBody = () => {
            if (!filteredCustomers.length) {
                return h(
                    'tr',
                    null,
                    h(
                        'td',
                        { className: 'p-6 text-center text-gray-500 text-sm', colSpan: 6 },
                        'Chưa có dữ liệu phù hợp.'
                    )
                );
            }

            return filteredCustomers.map((customer) =>
                h(
                    'tr',
                    {
                        key: customer.id,
                        className: 'hover:bg-blue-50/30 transition-colors group cursor-pointer',
                        onClick: () => setSelectedCustomer(customer),
                    },
                    [
                        h('td', { className: 'p-4 text-center text-gray-400 font-medium' }, `#${customer.id}`),
                        h(
                            'td',
                            { className: 'p-4' },
                            [
                                h('div', { className: 'font-medium text-gray-900' }, customer.name || '—'),
                                h('div', { className: 'text-xs text-gray-500 mt-0.5' }, customer.registered_at),
                            ]
                        ),
                        h('td', { className: 'p-4 font-medium text-gray-600 font-mono' }, customer.phone || '—'),
                        h('td', { className: 'p-4 text-gray-600' }, customer.city || '—'),
                        h('td', { className: 'p-4' }, h(StatusBadge, { status: customer.status })),
                        h(
                            'td',
                            { className: 'p-4 text-right' },
                            h(
                                'div',
                                { className: 'flex items-center justify-end gap-2 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity' },
                                [
                                    h(
                                        'button',
                                        {
                                            className: 'p-1.5 text-blue-600 hover:bg-blue-50 rounded-md',
                                            title: 'Xem chi tiết',
                                            onClick: (event) => {
                                                event.stopPropagation();
                                                setSelectedCustomer(customer);
                                            },
                                        },
                                        h(Icon, { name: 'Eye', size: 18 })
                                    ),
                                    h(
                                        'button',
                                        {
                                            className: 'p-1.5 text-red-500 hover:bg-red-50 rounded-md',
                                            title: 'Xóa',
                                            onClick: (event) => {
                                                event.stopPropagation();
                                                handleDelete(customer.id);
                                            },
                                        },
                                        h(Icon, { name: 'Trash2', size: 18 })
                                    ),
                                ]
                            )
                        ),
                    ]
                )
            );
        };

        return h(
            'div',
            { className: 'bg-[#f0f0f1] font-sans text-slate-800 min-h-screen pb-12' },
            h(
                'main',
                { className: 'w-full' },
                h(
                    'div',
                    { className: 'p-4 md:p-6 lg:p-8 max-w-[1600px] mx-auto space-y-6' },
                    [
                        h(
                            'div',
                            { className: 'flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2' },
                            [
                                h('div', null, [
                                    h(
                                        'h1',
                                        { className: 'text-2xl font-bold text-gray-800 flex items-center gap-2' },
                                        'Quản lý Đăng ký Quà tặng'
                                    ),
                                    h('p', { className: 'text-sm text-gray-500 mt-1' }, 'Danh sách khách hàng đăng ký từ Landing Page'),
                                ]),
                            ]
                        ),
                        loading
                            ? h(
                                  'div',
                                  { className: 'bg-white rounded-xl border border-gray-100 shadow-sm p-8 text-center text-gray-500' },
                                  'Đang tải dữ liệu...'
                              )
                            : error
                            ? h(
                                  'div',
                                  { className: 'bg-red-50 border border-red-100 text-red-600 rounded-xl p-4 text-sm font-medium' },
                                  error
                              )
                            : h(
                                  Fragment,
                                  null,
                                  [
                                      h(
                                          'div',
                                          { className: 'grid grid-cols-1 md:grid-cols-3 gap-4' },
                                          [
                                              h(StatCard, {
                                                  title: 'Tổng Đăng Ký',
                                                  value: stats.total,
                                                  subtext: 'Toàn bộ thời gian',
                                                  icon: 'Users',
                                                  trend: '+12%',
                                              }),
                                              h(StatCard, {
                                                  title: 'Cần Xử Lý',
                                                  value: stats.fresh,
                                                  subtext: 'Khách hàng mới',
                                                  icon: 'Clock',
                                              }),
                                              h(StatCard, {
                                                  title: 'Đã Gửi Quà',
                                                  value: stats.verified,
                                                  subtext: 'Chiến dịch tháng 10',
                                                  icon: 'CheckCircle',
                                              }),
                                          ]
                                      ),
                                      h(
                                          'div',
                                          { className: 'flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100' },
                                          [
                                              h(
                                                  'div',
                                                  { className: 'relative w-full sm:w-96' },
                                                  [
                                                      h(Icon, {
                                                          name: 'Search',
                                                          size: 18,
                                                          className: 'absolute left-3 top-1/2 -translate-y-1/2 text-gray-400',
                                                      }),
                                                      h('input', {
                                                          type: 'text',
                                                          className:
                                                              'w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all',
                                                          placeholder: 'Tìm kiếm theo tên, SĐT...',
                                                          value: searchTerm,
                                                          onChange: (event) => setSearchTerm(event.target.value),
                                                      }),
                                                  ]
                                              ),
                                              h(
                                                  'div',
                                                  { className: 'flex items-center gap-3 w-full sm:w-auto' },
                                                  [
                                                      h(
                                                          'select',
                                                          {
                                                              className:
                                                                  'px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer',
                                                              value: filterStatus,
                                                              onChange: (event) => setFilterStatus(event.target.value),
                                                          },
                                                          [
                                                              h('option', { value: 'all' }, 'Tất cả trạng thái'),
                                                              h('option', { value: 'new' }, 'Mới đăng ký'),
                                                              h('option', { value: 'contacted' }, 'Đang liên hệ'),
                                                              h('option', { value: 'verified' }, 'Đã gửi quà'),
                                                              h('option', { value: 'spam' }, 'Spam'),
                                                          ]
                                                      ),
                                                      h(
                                                          'button',
                                                          {
                                                              className:
                                                                  'flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-colors shadow-sm',
                                                              onClick: handleExport,
                                                          },
                                                          [
                                                              h(Icon, { name: 'Download', size: 16 }),
                                                              h('span', { className: 'hidden sm:inline' }, 'Xuất Excel'),
                                                          ]
                                                      ),
                                                  ]
                                              ),
                                          ]
                                      ),
                                      h(
                                          'div',
                                          { className: 'bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden' },
                                          [
                                              h(
                                                  'div',
                                                  { className: 'overflow-x-auto' },
                                                  h(
                                                      'table',
                                                      { className: 'w-full text-left border-collapse' },
                                                      [
                                                          h(
                                                              'thead',
                                                              null,
                                                              h(
                                                                  'tr',
                                                                  { className: 'bg-gray-50/50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider' },
                                                                  [
                                                                      h('th', { className: 'p-4 w-12 text-center' }, '#'),
                                                                      h('th', { className: 'p-4' }, 'Khách hàng'),
                                                                      h('th', { className: 'p-4' }, 'Liên hệ'),
                                                                      h('th', { className: 'p-4' }, 'Khu vực'),
                                                                      h('th', { className: 'p-4' }, 'Trạng thái'),
                                                                      h('th', { className: 'p-4 text-right' }, 'Hành động'),
                                                                  ]
                                                              )
                                                          ),
                                                          h('tbody', { className: 'divide-y divide-gray-50 text-sm' }, renderTableBody()),
                                                      ]
                                                  )
                                              ),
                                              h(
                                                  'div',
                                                  { className: 'p-4 border-t border-gray-100 flex items-center justify-between' },
                                                  [
                                                      h(
                                                          'span',
                                                          { className: 'text-sm text-gray-500' },
                                                          `Hiển thị ${filteredCustomers.length} kết quả`
                                                      ),
                                                      h('div', { className: 'flex gap-2' }, [
                                                          h(
                                                              'button',
                                                              { className: 'p-2 border rounded-lg hover:bg-gray-50 opacity-40 cursor-not-allowed', disabled: true },
                                                              h(Icon, { name: 'ChevronLeft', size: 16 })
                                                          ),
                                                          h(
                                                              'button',
                                                              { className: 'p-2 border rounded-lg hover:bg-gray-50 opacity-40 cursor-not-allowed', disabled: true },
                                                              h(Icon, { name: 'ChevronRight', size: 16 })
                                                          ),
                                                      ]),
                                                  ]
                                              ),
                                          ]
                                      ),
                                  ]
                              ),
                        h(Drawer, {
                            customer: selectedCustomer,
                            onClose: () => setSelectedCustomer(null),
                            onStatusChange: handleStatusChange,
                            onDelete: handleDelete,
                        }),
                        toastMessage ? h(Toast, { message: toastMessage }) : null,
                    ]
                )
            )
        );
    }

    const rootEl = document.getElementById('caremil-trial-admin-app');
    if (typeof wp.element.render === 'function') {
        wp.element.render(h(App), rootEl);
    } else if (typeof ReactDOM !== 'undefined') {
        ReactDOM.render(h(App), rootEl);
    }
})();

