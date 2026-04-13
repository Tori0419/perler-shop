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

            <form method="POST" action="{{ route('cart.update', [], false) }}" id="cartUpdateForm">
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
                                            data-update-url="{{ route('cart.update.ajax', [], false) }}"
                                        >
                                    </td>
                                    <td id="subtotal-{{ $item['product_id'] }}" class="subtotal">
                                        HKD {{ number_format($item['price'] * $item['qty'], 2) }}
                                    </td>
                                    <td style="width: 84px;">
                                        <button
                                            class="btn btn-danger remove-btn"
                                            type="button"
                                            data-remove-url="{{ route('cart.remove', $item['product_id'], false) }}"
                                        >
                                            移除
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div id="cartUpdateError" class="alert alert-error" style="display:none;"></div>
                <div class="row-between mt-2">
                    <div>
                        <strong>总价：<span id="grandTotal">HKD {{ number_format($initialTotal, 2) }}</span></strong>
                        <div id="ajaxStatus" class="text-muted mt-1" style="font-size: 12px;"></div>
                    </div>
                    <button type="submit" class="btn btn-success">保存</button>
                </div>
            </form>
        </div>

        <div class="card mt-2">
            <h3 style="margin-top: 0;">提交订单</h3>
            @if (! is_array($customer))
                <p class="text-muted">下单前请先登录用户账号（用于区分不同用户订单）。</p>
                <a href="{{ route('customer.login') }}" class="btn btn-primary">去用户登录</a>
            @else
                <form method="POST" action="{{ route('checkout.submit', [], false) }}" id="checkoutForm">
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

                    <div id="checkoutError" class="alert alert-error mt-2" style="display:none;"></div>
                    <button class="btn btn-primary mt-2" type="submit">确认下单</button>
                </form>
            @endif
        </div>
    @endif
@endsection
