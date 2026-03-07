@extends('layouts.app')

@section('title', '购物车与结算')

@php
    $initialTotal = 0;
    foreach ($cartItems as $item) {
        $initialTotal += $item['price'] * $item['qty'];
    }

    $customer = session('customer');
@endphp

@section('content')
    <h2>购物车与结算</h2>

    @if (count($cartItems) === 0)
        <div class="card">
            <p>购物车还是空的，先去首页挑选作品吧。</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary">去选购</a>
        </div>
    @else
        <div class="card">
            <p class="text-muted" style="margin-top: 0;">数量输入后会通过 AJAX 无刷新同步到服务器。</p>

            <form method="POST" action="{{ route('cart.update') }}" id="cartUpdateForm">
                @csrf

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>商品</th>
                                <th>单价</th>
                                <th>数量</th>
                                <th>小计</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $item)
                                <tr>
                                    <td>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width: 56px; height: 56px; border-radius: 8px; border: 1px solid #e5e7eb;">
                                            <strong>{{ $item['name'] }}</strong>
                                        </div>
                                    </td>
                                    <td>HKD {{ number_format($item['price'], 2) }}</td>
                                    <td style="width: 132px;">
                                        <input
                                            type="number"
                                            min="1"
                                            name="quantities[{{ $item['product_id'] }}]"
                                            value="{{ $item['qty'] }}"
                                            class="input qty-input"
                                            data-product-id="{{ $item['product_id'] }}"
                                            data-price="{{ $item['price'] }}"
                                            data-subtotal-id="subtotal-{{ $item['product_id'] }}"
                                            data-update-url="{{ route('cart.update.ajax') }}"
                                        >
                                    </td>
                                    <td id="subtotal-{{ $item['product_id'] }}" class="subtotal">
                                        HKD {{ number_format($item['price'] * $item['qty'], 2) }}
                                    </td>
                                    <td style="width: 84px;">
                                        <button
                                            class="btn btn-danger"
                                            type="submit"
                                            formaction="{{ route('cart.remove', $item['product_id']) }}"
                                            formmethod="POST"
                                            formnovalidate
                                        >
                                            移除
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-2" style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <div>
                        <strong>总价：<span id="grandTotal">HKD {{ number_format($initialTotal, 2) }}</span></strong>
                        <div id="ajaxStatus" class="text-muted mt-1" style="font-size: 12px;"></div>
                    </div>
                    <button type="submit" class="btn btn-success">手动保存（备用）</button>
                </div>
            </form>
        </div>

        <div class="card mt-2">
            <h3 style="margin-top: 0;">提交订单</h3>
            @if (! is_array($customer))
                <p class="text-muted">下单前请先登录用户账号（用于区分不同用户订单）。</p>
                <a href="{{ route('customer.login') }}" class="btn btn-primary">去用户登录</a>
            @else
                <form method="POST" action="{{ route('checkout.submit') }}" id="checkoutForm">
                    @csrf
                    <div class="row">
                        <div class="col-4">
                            <label>姓名</label>
                            <input class="input mt-1" value="{{ $customer['name'] }}" readonly>
                        </div>
                        <div class="col-4">
                            <label>联系方式</label>
                            <input class="input mt-1" value="{{ $customer['contact'] }}" readonly>
                        </div>
                        <div class="col-4">
                            <label for="address">收货地址</label>
                            <input
                                class="input mt-1"
                                id="address"
                                name="address"
                                value="{{ old('address', $customer['address']) }}"
                                required
                            >
                        </div>
                    </div>

                    <button class="btn btn-primary mt-2" type="submit">确认下单</button>
                </form>
            @endif
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        (() => {
            const qtyInputs = Array.from(document.querySelectorAll('.qty-input'));
            const grandTotalElement = document.getElementById('grandTotal');
            const cartUpdateForm = document.getElementById('cartUpdateForm');
            const checkoutForm = document.getElementById('checkoutForm');
            const ajaxStatus = document.getElementById('ajaxStatus');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const pendingTimerMap = new Map();

            if (qtyInputs.length === 0) {
                return;
            }

            const setAjaxStatus = (text, isError = false) => {
                if (!ajaxStatus) {
                    return;
                }
                ajaxStatus.style.color = isError ? '#991b1b' : '#6b7280';
                ajaxStatus.textContent = text;
            };

            const calculate = () => {
                let total = 0;
                let valid = true;

                qtyInputs.forEach((input) => {
                    const price = Number(input.dataset.price || 0);
                    const qty = Number(input.value);
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
                const quantity = Number(input.value);
                const productId = Number(input.dataset.productId);
                const updateUrl = input.dataset.updateUrl;

                if (!Number.isInteger(quantity) || quantity <= 0 || !productId || !updateUrl) {
                    return;
                }

                try {
                    setAjaxStatus('正在同步购物车…');

                    const response = await fetch(updateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            qty: quantity
                        })
                    });

                    if (!response.ok) {
                        throw new Error('同步失败');
                    }

                    const data = await response.json();
                    if (grandTotalElement && typeof data.total === 'number') {
                        grandTotalElement.textContent = `HKD ${data.total.toFixed(2)}`;
                    }

                    setAjaxStatus('已自动同步到服务器');
                } catch (error) {
                    setAjaxStatus('自动同步失败，请点击“手动保存（备用）”', true);
                }
            };

            qtyInputs.forEach((input) => {
                input.addEventListener('input', () => {
                    calculate();

                    const timer = pendingTimerMap.get(input);
                    if (timer) {
                        clearTimeout(timer);
                    }

                    const newTimer = setTimeout(() => {
                        if (calculate()) {
                            syncQuantity(input);
                        }
                    }, 450);

                    pendingTimerMap.set(input, newTimer);
                });
            });

            if (cartUpdateForm) {
                cartUpdateForm.addEventListener('submit', (event) => {
                    if (!calculate()) {
                        event.preventDefault();
                        alert('购物车数量必须是正整数。');
                    }
                });
            }

            if (checkoutForm) {
                checkoutForm.addEventListener('submit', (event) => {
                    const address = document.getElementById('address')?.value.trim() || '';
                    if (!calculate() || !address) {
                        event.preventDefault();
                        alert('请填写完整地址，并确保数量输入正确。');
                    }
                });
            }

            calculate();
        })();
    </script>
@endsection
