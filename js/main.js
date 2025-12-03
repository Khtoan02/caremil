/**
 * Caremil Theme JavaScript
 */

(function() {
    'use strict';

    // Mobile menu toggle (nếu cần)
    const navToggle = document.querySelector('.nav-toggle');
    const mainNav = document.querySelector('.main-navigation');

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', function() {
            mainNav.classList.toggle('is-open');
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Trial registration forms
    const handleTrialResponse = (form, message, isError) => {
        let feedback = form.querySelector('[data-caremil-feedback]');
        if (!feedback) {
            const sibling = form.nextElementSibling;
            if (sibling && sibling.hasAttribute('data-caremil-feedback')) {
                feedback = sibling;
            }
        }
        if (!feedback) {
            feedback = document.createElement('p');
            feedback.setAttribute('data-caremil-feedback', 'true');
            feedback.className = 'text-sm mt-3';
            form.appendChild(feedback);
        }
        feedback.textContent = message;
        feedback.classList.remove('hidden');
        feedback.classList.toggle('text-emerald-600', !isError);
        feedback.classList.toggle('text-red-600', !!isError);
    };

    const trialForms = document.querySelectorAll('[data-caremil-trial-form]');
    if (trialForms.length && typeof caremilTrialForm !== 'undefined' && caremilTrialForm.restUrl) {
        trialForms.forEach(form => {
            form.addEventListener('submit', async function(event) {
                event.preventDefault();

                const submitBtn = form.querySelector('[data-caremil-submit]');
                const feedback = form.querySelector('[data-caremil-feedback]');
                const requiredFields = form.querySelectorAll('[data-caremil-required]');
                const fieldErrors = form.querySelectorAll('[data-caremil-error]');

                // Reset trạng thái nút & lỗi cũ
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                }
                fieldErrors.forEach(el => el.classList.add('hidden'));
                if (feedback) {
                    feedback.classList.add('hidden');
                    feedback.classList.remove('text-red-600');
                    feedback.classList.remove('text-emerald-600');
                }

                // Lấy dữ liệu form
                const data = new FormData(form);
                const payload = {
                    name: (data.get('caremil_name') || '').trim(),
                    phone: (data.get('caremil_phone') || '').trim(),
                    city: (data.get('caremil_city') || '').trim(),
                    address: (data.get('caremil_address') || '').trim(),
                    consent_terms: data.get('caremil_terms') ? true : false,
                    consent_privacy: data.get('caremil_privacy') ? true : false,
                    source: form.dataset.caremilSource || 'landing',
                };

                // 1. Kiểm tra các trường bắt buộc
                let hasFieldError = false;
                let firstMissingField = null;

                requiredFields.forEach(field => {
                    const value = (field.value || '').trim();
                    if (!value) {
                        hasFieldError = true;
                        if (!firstMissingField) {
                            firstMissingField = field;
                        }
                        const errEl = form.querySelector('[data-caremil-error="' + field.name + '"]');
                        if (errEl) {
                            errEl.classList.remove('hidden');
                        }
                    }
                });

                if (hasFieldError) {
                    handleTrialResponse(form, 'Vui lòng điền đầy đủ các thông tin được yêu cầu trước khi đăng ký.', true);
                    if (firstMissingField) {
                        firstMissingField.focus();
                    }
                    return;
                }

                // 2. Kiểm tra điều khoản
                if (!payload.consent_terms || !payload.consent_privacy) {
                    handleTrialResponse(form, 'Vui lòng đọc kỹ và tích chọn đủ Thể lệ/Điều khoản và Mẫu chấp thuận trước khi đăng ký.', true);
                    return;
                }

                // 3. Nếu hợp lệ, khóa nút và gửi request
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                }

                try {
                    const response = await fetch(caremilTrialForm.restUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    if (!response.ok) {
                        throw new Error();
                    }

                    handleTrialResponse(form, caremilTrialForm.successMessage || 'Đăng ký thành công!', false);
                    form.reset();

                    if (form.dataset.caremilCloseOnSuccess && typeof window.closeTrialModal === 'function') {
                        window.closeTrialModal();
                    }
                } catch (error) {
                    handleTrialResponse(form, caremilTrialForm.errorMessage || 'Có lỗi xảy ra, vui lòng thử lại.', true);
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                    }
                }
            });
        });
    }

})();

