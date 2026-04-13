(() => {
    const qtyInputs = Array.from(document.querySelectorAll('.qty-input'));

    if (qtyInputs.length === 0) {
        return;
    }

    const grandTotalElement = document.getElementById('grandTotal');
    const cartUpdateForm    = document.getElementById('cartUpdateForm');
    const checkoutForm      = document.getElementById('checkoutForm');
    const ajaxStatus        = document.getElementById('ajaxStatus');
    const cartUpdateError   = document.getElementById('cartUpdateError');
    const checkoutError     = document.getElementById('checkoutError');
    const csrfToken         = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const pendingTimerMap   = new Map();

    const setAjaxStatus = (text, isError = false) => {
        if (!ajaxStatus) return;
        ajaxStatus.style.color = isError ? '#991b1b' : '#6b7280';
        ajaxStatus.textContent = text;
    };

    const showError = (el, message) => {
        if (!el) return;
        el.textContent = message;
        el.style.display = 'block';
    };

    const hideError = (el) => {
        if (!el) return;
        el.textContent = '';
        el.style.display = 'none';
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

    const calculate = () => {
        let total = 0;
        let valid = true;

        qtyInputs.forEach((input) => {
            const price       = Number(input.dataset.price || 0);
            const qty         = Number(input.value);
            const subtotalCell = document.getElementById(input.dataset.subtotalId);

            if (!Number.isInteger(qty) || qty <= 0) {
                valid = false;
                input.classList.add('invalid');
            } else {
                input.classList.remove('invalid');
                const subtotal = qty * price;
                total += subtotal;
                if (subtotalCell) {
                    subtotalCell.textContent = `HKD ${subtotal.toFixed(2)}`;
                }
            }
        });

        if (grandTotalElement) {
            grandTotalElement.textContent = `HKD ${total.toFixed(2)}`;
        }

        return valid;
    };

    const syncQuantity = async (input) => {
        const quantity  = Number(input.value);
        const productId = Number(input.dataset.productId);
        const updateUrl = input.dataset.updateUrl;

        if (!Number.isInteger(quantity) || quantity <= 0 || !productId || !updateUrl) return;

        try {
            setAjaxStatus('正在同步购物车…');

            const response = await fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_id: productId, qty: quantity }),
            });

            if (!response.ok) throw new Error('同步失败');

            const data = await response.json();
            if (grandTotalElement && typeof data.total === 'number') {
                grandTotalElement.textContent = `HKD ${data.total.toFixed(2)}`;
            }

            setAjaxStatus('已自动同步到服务器');
        } catch {
            setAjaxStatus('自动同步失败，请点击"保存"', true);
        }
    };

    const cancelAllPendingTimers = () => {
        pendingTimerMap.forEach((timer) => clearTimeout(timer));
        pendingTimerMap.clear();
    };

    qtyInputs.forEach((input) => {
        input.addEventListener('input', () => {
            calculate();

            const timer = pendingTimerMap.get(input);
            if (timer) clearTimeout(timer);

            const newTimer = setTimeout(() => {
                if (calculate()) syncQuantity(input);
            }, 450);

            pendingTimerMap.set(input, newTimer);
        });
    });

    document.querySelectorAll('.remove-btn').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const row       = btn.closest('tr');
            const removeUrl = btn.dataset.removeUrl;
            if (!removeUrl || !row) return;

            btn.disabled    = true;
            btn.textContent = '移除中...';

            try {
                const response = await fetch(removeUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) throw new Error('移除失败');

                const data = await response.json();

                // Clean up this row's input before removing the row
                const rowInput = row.querySelector('.qty-input');
                if (rowInput) {
                    const timer = pendingTimerMap.get(rowInput);
                    if (timer) clearTimeout(timer);
                    pendingTimerMap.delete(rowInput);
                    const idx = qtyInputs.indexOf(rowInput);
                    if (idx !== -1) qtyInputs.splice(idx, 1);
                }

                row.remove();
                updateCartNav(data.cart_qty);

                if (grandTotalElement && typeof data.total === 'number') {
                    grandTotalElement.textContent = `HKD ${data.total.toFixed(2)}`;
                }

                // Reload to show empty-cart message when cart becomes empty
                if (qtyInputs.length === 0) {
                    window.location.reload();
                }
            } catch {
                btn.disabled    = false;
                btn.textContent = '移除';
                setAjaxStatus('移除失败，请重试', true);
            }
        });
    });

    if (cartUpdateForm) {
        cartUpdateForm.addEventListener('submit', (event) => {
            hideError(cartUpdateError);
            if (!calculate()) {
                event.preventDefault();
                showError(cartUpdateError, '购物车数量必须是正整数。');
            }
        });
    }

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', (event) => {
            hideError(checkoutError);
            const address = document.getElementById('address')?.value.trim() || '';
            if (!calculate() || !address) {
                event.preventDefault();
                showError(checkoutError, '请填写完整地址，并确保数量输入正确。');
            } else {
                // Cancel any pending AJAX sync before navigating away
                cancelAllPendingTimers();
            }
        });
    }

    calculate();
})();
