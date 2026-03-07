@extends('layouts.app')

@section('title', '首页 - 拼豆在线订购系统')

@section('content')
    <div class="card mb-2">
        <form action="{{ route('shop.index') }}" method="GET" class="row" style="align-items: end;">
            <div class="col-8">
                <label for="q" class="text-muted">搜索拼豆作品</label>
                <input class="input mt-1" id="q" name="q" value="{{ $keyword }}" placeholder="输入关键字后点“搜索”">
            </div>
            <div class="col-2">
                <button type="submit" class="btn btn-primary mt-1" style="width: 100%;">搜索</button>
            </div>
            <div class="col-2">
                <a class="btn btn-muted mt-1" href="{{ route('shop.index') }}" style="width: 100%;">清空</a>
            </div>
        </form>
    </div>

    <div id="addCartStatus" class="card mb-2 text-muted" style="display: none; padding: 10px 12px;"></div>

    @if (count($products) === 0)
        <div class="card">
            <p>没有找到符合条件的拼豆作品。</p>
        </div>
    @else
        <div class="grid">
            @foreach ($products as $product)
                <div class="card">
                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="product-image">
                    <h3 style="margin: 0 0 6px;">{{ $product['name'] }}</h3>
                    <div class="text-muted">库存：{{ $product['stock'] }}</div>
                    <p class="text-muted" style="min-height: 44px;">{{ $product['description'] }}</p>
                    <div style="font-weight: 700; margin-bottom: 10px;">HKD {{ number_format($product['price'], 2) }}</div>

                    <form action="{{ route('cart.add') }}" method="POST" class="row add-to-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                        <div class="col-6">
                            <input
                                type="number"
                                class="input shop-qty-input"
                                name="qty"
                                min="1"
                                value="1"
                                aria-label="数量"
                            >
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary add-to-cart-btn" style="width: 100%;">加入购物车</button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        (() => {
            const forms = Array.from(document.querySelectorAll('.add-to-cart-form'));
            const qtyInputs = Array.from(document.querySelectorAll('.shop-qty-input'));
            const statusBox = document.getElementById('addCartStatus');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const cartNavLink = document.getElementById('cartNavLink');

            if (forms.length === 0) {
                return;
            }

            const setStatus = (text, isError = false) => {
                if (!statusBox) {
                    return;
                }
                statusBox.style.display = 'block';
                statusBox.style.background = isError ? '#fee2e2' : '#ecfeff';
                statusBox.style.color = isError ? '#991b1b' : '#155e75';
                statusBox.textContent = text;
            };

            const updateCartNav = (cartQty) => {
                if (!cartNavLink || typeof cartQty !== 'number') {
                    return;
                }
                cartNavLink.textContent = cartQty > 0 ? `购物车（${cartQty}）` : '购物车';
            };

            const normalizeQuantity = (inputElement) => {
                if (!inputElement) {
                    return 1;
                }

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

                    const button = form.querySelector('.add-to-cart-btn');
                    const qtyInput = form.querySelector('input[name="qty"]');
                    const quantity = normalizeQuantity(qtyInput);

                    if (!Number.isInteger(quantity) || quantity <= 0) {
                        setStatus('数量必须为正整数。', true);
                        qtyInput?.focus();
                        return;
                    }

                    try {
                        if (button) {
                            button.disabled = true;
                            button.textContent = '加入中...';
                        }

                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: new FormData(form)
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
                        if (qtyInput) {
                            qtyInput.value = '1';
                        }
                        setStatus(`已加入购物车：${data.cart_qty ?? 0} 件商品`, false);
                    } catch (error) {
                        setStatus(error.message || '加入购物车失败，请重试。', true);
                    } finally {
                        if (button) {
                            button.disabled = false;
                            button.textContent = '加入购物车';
                        }
                    }
                });
            });
        })();
    </script>
@endsection
