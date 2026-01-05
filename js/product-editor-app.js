/**
 * Premium Product Editor - React App
 * Modern, beautiful UI for editing products
 */

(function () {
    const { createElement: e, useState, useEffect, Fragment } = wp.element;
    const { Button, TextControl, TextareaControl, SelectControl, Spinner } = wp.components;

    /**
     * Main App Component
     */
    function ProductEditorApp() {
        const [loading, setLoading] = useState(true);
        const [saving, setSaving] = useState(false);
        const [product, setProduct] = useState({
            id: window.caremilProductEditor.productId,
            title: '',
            content: '',
            short_desc: '',
            product_weight: 500,
            status: 'draft',
            rating: 5,
            rating_count: 0,
            featured_image: 0,
            featured_image_url: '',
            gallery: [],
            // Pancake data
            pancake_id: '',
            pancake_sku: '',
            pancake_price: '',
            pancake_category: '',
        });

        // Load product data
        useEffect(() => {
            if (product.id > 0) {
                loadProduct();
            } else {
                setLoading(false);
            }
        }, []);

        const loadProduct = () => {
            jQuery.ajax({
                url: window.caremilProductEditor.ajaxUrl,
                method: 'GET',
                data: {
                    action: 'caremil_get_product',
                    product_id: product.id,
                    nonce: window.caremilProductEditor.nonce
                },
                success: (response) => {
                    if (response.success) {
                        setProduct(prev => ({ ...prev, ...response.data }));
                    }
                    setLoading(false);
                },
                error: () => {
                    setLoading(false);
                    alert('Failed to load product');
                }
            });
        };

        const saveProduct = (newStatus) => {
            setSaving(true);

            const data = new FormData();
            data.append('action', 'caremil_save_product');
            data.append('nonce', window.caremilProductEditor.nonce);
            data.append('product_id', product.id);
            data.append('title', product.title);
            data.append('content', product.content);
            data.append('short_desc', product.short_desc);
            data.append('status', newStatus || product.status);
            data.append('rating', product.rating);
            data.append('rating_count', product.rating_count);
            data.append('product_weight', product.product_weight);
            data.append('featured_image', product.featured_image);
            data.append('gallery', JSON.stringify(product.gallery));

            jQuery.ajax({
                url: window.caremilProductEditor.ajaxUrl,
                method: 'POST',
                data: data,
                processData: false,
                contentType: false,
                success: (response) => {
                    setSaving(false);
                    if (response.success) {
                        alert('✓ Product saved successfully!');
                        if (product.id === 0) {
                            // Redirect to edit page for new product
                            window.location.href = response.data.edit_url;
                        }
                    } else {
                        alert('✗ Failed to save: ' + response.data.message);
                    }
                },
                error: () => {
                    setSaving(false);
                    alert('✗ Failed to save product');
                }
            });
        };

        const uploadImage = (callback) => {
            const frame = wp.media({
                title: 'Select Product Image',
                button: { text: 'Use this image' },
                multiple: false
            });

            frame.on('select', () => {
                const attachment = frame.state().get('selection').first().toJSON();
                callback(attachment);
            });

            frame.open();
        };

        if (loading) {
            return e('div', { className: 'flex items-center justify-center h-screen' },
                e(Spinner)
            );
        }

        const hasPancakeData = product.pancake_id && product.pancake_id !== '';

        return e('div', { className: 'caremil-product-editor bg-gray-50 min-h-screen' },
            // Header
            e('div', {
                className: 'bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm'
            },
                e('div', { className: 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4' },
                    e('div', { className: 'flex items-center justify-between' },
                        // Left: Back button + Title
                        e('div', { className: 'flex items-center space-x-4' },
                            e('a', {
                                href: window.caremilProductEditor.productListUrl,
                                className: 'inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-colors'
                            },
                                e('svg', { className: 'w-4 h-4 mr-2', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' },
                                    e('path', { strokeLinecap: 'round', strokeLinejoin: 'round', strokeWidth: 2, d: 'M15 19l-7-7 7-7' })
                                ),
                                'Back to Products'
                            ),
                            e('h1', { className: 'text-2xl font-bold text-gray-900' },
                                product.id > 0 ? 'Edit Product' : 'New Product'
                            ),
                            product.status === 'draft'
                                ? e('span', {
                                    className: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800'
                                }, '📝 Draft')
                                : e('span', {
                                    className: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800'
                                }, '✅ Published')
                        ),

                        // Right: Actions
                        e('div', { className: 'flex items-center space-x-3' },
                            e(Button, {
                                onClick: () => saveProduct('draft'),
                                disabled: saving,
                                className: 'inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-colors disabled:opacity-50'
                            },
                                saving ? 'Saving...' : '💾 Save Draft'
                            ),
                            e(Button, {
                                onClick: () => saveProduct('publish'),
                                disabled: saving || !product.title,
                                className: 'inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gradient-to-r from-primary to-purple-600 hover:from-purple-600 hover:to-primary focus:outline-none transition-all disabled:opacity-50 transform hover:scale-105'
                            },
                                saving ? 'Publishing...' : '✓ Publish'
                            )
                        )
                    )
                )
            ),

            // Main Content
            e('div', { className: 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8' },
                e('div', { className: 'grid grid-cols-1 lg:grid-cols-3 gap-8' },

                    // Left Sidebar
                    e('div', { className: 'lg:col-span-1 space-y-6' },

                        // Featured Image Card
                        e('div', { className: 'bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden' },
                            e('div', { className: 'px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white' },
                                e('h3', { className: 'text-lg font-semibold text-gray-900' }, '📷 Product Image')
                            ),
                            e('div', { className: 'p-6' },
                                product.featured_image_url
                                    ? e('div', { className: 'relative group' },
                                        e('img', {
                                            src: product.featured_image_url,
                                            className: 'w-full h-64 object-cover rounded-lg'
                                        }),
                                        e('div', {
                                            className: 'absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg'
                                        },
                                            e('button', {
                                                onClick: () => uploadImage((img) => {
                                                    setProduct(prev => ({
                                                        ...prev,
                                                        featured_image: img.id,
                                                        featured_image_url: img.url
                                                    }));
                                                }),
                                                className: 'px-4 py-2 bg-white text-gray-900 rounded-md font-medium hover:bg-gray-100 transition-colors'
                                            }, 'Change Image')
                                        )
                                    )
                                    : e('button', {
                                        onClick: () => uploadImage((img) => {
                                            setProduct(prev => ({
                                                ...prev,
                                                featured_image: img.id,
                                                featured_image_url: img.url
                                            }));
                                        }),
                                        className: 'w-full h-64 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center hover:border-primary hover:bg-primary hover:bg-opacity-5 transition-all group'
                                    },
                                        e('svg', { className: 'w-12 h-12 text-gray-400 group-hover:text-primary transition-colors', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' },
                                            e('path', { strokeLinecap: 'round', strokeLinejoin: 'round', strokeWidth: 2, d: 'M12 6v6m0 0v6m0-6h6m-6 0H6' })
                                        ),
                                        e('span', { className: 'mt-2 text-sm text-gray-600 group-hover:text-primary transition-colors' }, 'Upload Image')
                                    )
                            )
                        ),

                        // Pancake Data Card (if synced)
                        hasPancakeData && e('div', { className: 'bg-gradient-to-br from-purple-600 to-indigo-700 rounded-lg shadow-lg overflow-hidden text-white' },
                            e('div', { className: 'px-6 py-4 bg-black bg-opacity-10' },
                                e('h3', { className: 'text-lg font-semibold flex items-center' },
                                    e('span', { className: 'mr-2' }, '🔒'),
                                    'Pancake Data'
                                )
                            ),
                            e('div', { className: 'p-6 space-y-4' },
                                e('div', { className: 'bg-white bg-opacity-10 rounded-lg p-3' },
                                    e('div', { className: 'text-xs opacity-75 mb-1' }, 'Product ID'),
                                    e('code', { className: 'text-sm font-mono break-all' }, product.pancake_id)
                                ),
                                product.pancake_sku && e('div', { className: 'bg-white bg-opacity-10 rounded-lg p-3' },
                                    e('div', { className: 'text-xs opacity-75 mb-1' }, 'SKU'),
                                    e('code', { className: 'text-sm font-mono' }, product.pancake_sku)
                                ),
                                product.pancake_price && e('div', { className: 'bg-white bg-opacity-15 rounded-lg p-4 text-center' },
                                    e('div', { className: 'text-sm opacity-75 mb-2' }, 'Price'),
                                    e('div', { className: 'text-3xl font-bold' }, product.pancake_price + 'đ')
                                ),
                                product.pancake_category && e('div', {},
                                    e('div', { className: 'text-xs opacity-75 mb-1' }, 'Category'),
                                    e('div', { className: 'text-sm font-medium' }, product.pancake_category)
                                ),
                                e('a', {
                                    href: window.caremilProductEditor.productListUrl.replace('edit.php?post_type=product', 'admin.php?page=pancake-product-sync'),
                                    className: 'block w-full text-center px-4 py-2 bg-white text-purple-700 rounded-md font-medium hover:bg-opacity-90 transition-all mt-4'
                                }, '🔄 Sync from Pancake')
                            )
                        )
                    ),

                    // Main Content Area  
                    e('div', { className: 'lg:col-span-2 space-y-6' },

                        // General Info Card
                        e('div', { className: 'bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden' },
                            e('div', { className: 'px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white' },
                                e('h3', { className: 'text-lg font-semibold text-gray-900' }, '📝 General Information')
                            ),
                            e('div', { className: 'p-6 space-y-5' },
                                e(TextControl, {
                                    label: 'Product Title *',
                                    value: product.title,
                                    onChange: (value) => setProduct(prev => ({ ...prev, title: value })),
                                    placeholder: 'Enter product name...',
                                    className: 'text-lg font-medium'
                                }),

                                // Status selector
                                e('div', {},
                                    e('label', { className: 'block text-sm font-medium text-gray-700 mb-2' }, 'Publication Status'),
                                    e('select', {
                                        value: product.status,
                                        onChange: (e) => setProduct(prev => ({ ...prev, status: e.target.value })),
                                        className: 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary'
                                    },
                                        e('option', { value: 'draft' }, '📝 Draft - Not visible to customers'),
                                        e('option', { value: 'publish' }, '✅ Published - Visible on store')
                                    ),
                                    e('p', { className: 'mt-1 text-sm text-gray-500' },
                                        product.status === 'draft'
                                            ? 'Save as draft to edit before publishing'
                                            : 'Product is live and visible to customers'
                                    )
                                ),

                                e('div', {},
                                    e('label', { className: 'block text-sm font-medium text-gray-700 mb-2' }, 'Full Description'),
                                    e('textarea', {
                                        value: product.content,
                                        onChange: (e) => setProduct(prev => ({ ...prev, content: e.target.value })),
                                        rows: 8,
                                        className: 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary',
                                        placeholder: 'Product description, ingredients, benefits...'
                                    })
                                ),

                                e(TextareaControl, {
                                    label: 'Short Description',
                                    value: product.short_desc,
                                    onChange: (value) => setProduct(prev => ({ ...prev, short_desc: value })),
                                    rows: 3,
                                    placeholder: 'Brief description for product cards...'
                                })
                            )
                        ),

                        // Rating Card
                        e('div', { className: 'bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden' },
                            e('div', { className: 'px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white' },
                                e('h3', { className: 'text-lg font-semibold text-gray-900' }, '⭐ Rating & Reviews')
                            ),
                            e('div', { className: 'p-6' },
                                e('div', { className: 'grid grid-cols-2 gap-6' },
                                    e('div', {},
                                        e('label', { className: 'block text-sm font-medium text-gray-700 mb-2' }, 'Rating'),
                                        e('select', {
                                            value: product.rating,
                                            onChange: (e) => setProduct(prev => ({ ...prev, rating: parseInt(e.target.value) })),
                                            className: 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary'
                                        },
                                            [1, 2, 3, 4, 5].map(i =>
                                                e('option', { key: i, value: i }, '⭐'.repeat(i) + ` (${i} stars)`)
                                            )
                                        )
                                    ),
                                    e(TextControl, {
                                        label: 'Number of Reviews',
                                        type: 'number',
                                        value: product.rating_count,
                                        onChange: (value) => setProduct(prev => ({ ...prev, rating_count: parseInt(value) || 0 })),
                                        min: 0
                                    })
                                )
                            )
                        ),

                        // Shipping Info Card
                        e('div', { className: 'bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden' },
                            e('div', { className: 'px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white' },
                                e('h3', { className: 'text-lg font-semibold text-gray-900' }, '🚚 Shipping Information')
                            ),
                            e('div', { className: 'p-6' },
                                e('div', { className: 'grid grid-cols-1 gap-6' },
                                    e(TextControl, {
                                        label: 'Product Weight (grams) *',
                                        help: 'Used for calculating exact Viettel Post shipping fees.',
                                        type: 'number',
                                        value: product.product_weight,
                                        onChange: (value) => setProduct(prev => ({ ...prev, product_weight: parseInt(value) || 500 })),
                                        min: 1,
                                        className: 'font-mono'
                                    })
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    // Render
    const container = document.getElementById('caremil-product-editor-root');
    if (container) {
        wp.element.render(
            e(ProductEditorApp),
            container
        );
    }
})();
