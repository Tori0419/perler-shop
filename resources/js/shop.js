(() => {
    const forms      = Array.from(document.querySelectorAll('.add-to-cart-form'));
    const qtyInputs  = Array.from(document.querySelectorAll('.shop-qty-input'));
    const statusBox  = document.getElementById('addCartStatus');
    const csrfToken  = document.querySelector('meta[name="csrf-token"]')?.content || '';

    if (forms.length === 0) {
        return;
    }

    const setStatus = (text, isError = false) => {
        if (!statusBox) return;
        statusBox.style.display  = 'block';
        statusBox.style.background = isError ? '#fee2e2' : '#ecfeff';
        statusBox.style.color    = isError ? '#991b1b' : '#155e75';
        statusBox.textContent    = text;
    };

    const updateCartNav = (cartQty) => {
        if (typeof cartQty !== 'number') return;
        const badge = document.getElementById('cartBadge');
        if (badge) {
            if (cartQty > 0) {
                badge.textContent = cartQty > 99 ? '99+' : cartQty;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    };

    const normalizeQuantity = (inputElement) => {
        if (!inputElement) return 1;
        const quantity = Number(inputElement.value);
        if (!Number.isInteger(quantity) || quantity <= 0) {
            inputElement.value = '1';
            return 1;
        }
        return quantity;
    };

    qtyInputs.forEach((inputElement) => {
        normalizeQuantity(inputElement);
        inputElement.addEventListener('blur', () => {
            normalizeQuantity(inputElement);
        });
    });

    forms.forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const button   = form.querySelector('.add-to-cart-btn');
            const qtyInput = form.querySelector('input[name="qty"]');
            const quantity = normalizeQuantity(qtyInput);

            if (!Number.isInteger(quantity) || quantity <= 0) {
                setStatus('数量必须为正整数。', true);
                qtyInput?.focus();
                return;
            }

            try {
                if (button) {
                    button.disabled    = true;
                    button.textContent = '加入中...';
                }

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(form),
                });

                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                const data = await response.json();

                if (!response.ok || !data.ok) {
                    const message = data.message
                        || data.errors?.product_id?.[0]
                        || data.errors?.qty?.[0]
                        || '加入购物车失败，请重试。';
                    throw new Error(message);
                }

                updateCartNav(data.cart_qty);
                if (qtyInput) qtyInput.value = '1';
                setStatus(`已加入购物车：${data.cart_qty ?? 0} 件商品`, false);
            } catch (error) {
                setStatus(error.message || '加入购物车失败，请重试。', true);
            } finally {
                if (button) {
                    button.disabled    = false;
                    button.textContent = '加入购物车';
                }
            }
        });
    });
})();
