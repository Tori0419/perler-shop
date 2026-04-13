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
    <div class="max-w-6xl mx-auto px-4 py-8">
        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">购物车</h1>
            <p class="text-gray-500 mt-1">管理您的购物商品并完成结算</p>
        </div>

        @if (count($cartItems) === 0)
            {{-- Empty Cart State --}}
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-50 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">购物车是空的</h3>
                <p class="text-gray-500 mb-6">先去首页挑选喜欢的作品吧</p>
                <a href="{{ route('shop.index') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:shadow-lg hover:shadow-indigo-500/25 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    去选购
                </a>
            </div>
        @else
            <div class="grid lg:grid-cols-3 gap-8">
                {{-- Cart Items --}}
                <div class="lg:col-span-2">
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                            <div class="flex items-center justify-between">
                                <h2 class="font-semibold text-gray-900">购物商品 ({{ count($cartItems) }})</h2>
                                <span id="ajaxStatus" class="text-xs text-gray-400"></span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('cart.update', [], false) }}" id="cartUpdateForm">
                            @csrf
                            <div class="divide-y divide-gray-100">
                                @foreach ($cartItems as $item)
                                    <div class="p-6 hover:bg-gray-50/50 transition-colors" id="cart-item-{{ $item['product_id'] }}">
                                        <div class="flex gap-4">
                                            {{-- Product Image --}}
                                            <div class="flex-shrink-0">
                                                <img src="{{ $item['image'] }}" 
                                                     alt="{{ $item['name'] }}" 
                                                     class="w-20 h-20 rounded-xl object-cover border border-gray-200 shadow-sm">
                                            </div>
                                            
                                            {{-- Product Info --}}
                                            <div class="flex-1 min-w-0">
                                                <h3 class="font-semibold text-gray-900 truncate">{{ $item['name'] }}</h3>
                                                <p class="text-indigo-600 font-medium mt-1">HKD {{ number_format($item['price'], 2) }}</p>
                                                
                                                <div class="flex items-center gap-4 mt-3">
                                                    {{-- Quantity Control --}}
                                                    <div class="flex items-center gap-2">
                                                        <label class="text-xs text-gray-500">数量:</label>
                                                        <input type="number"
                                                               min="1"
                                                               name="quantities[{{ $item['product_id'] }}]"
                                                               value="{{ $item['qty'] }}"
                                                               class="w-20 px-3 py-2 text-center text-sm border border-gray-200 rounded-lg
                                                                      focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all qty-input"
                                                               data-product-id="{{ $item['product_id'] }}"
                                                               data-price="{{ $item['price'] }}"
                                                               data-subtotal-id="subtotal-{{ $item['product_id'] }}"
                                                               data-update-url="{{ route('cart.update.ajax', [], false) }}">
                                                    </div>
                                                    
                                                    {{-- Remove Button --}}
                                                    <button type="button"
                                                            class="text-gray-400 hover:text-red-500 transition-colors remove-btn"
                                                            data-remove-url="{{ route('cart.remove', $item['product_id'], false) }}"
                                                            title="移除商品">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            {{-- Subtotal --}}
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500 mb-1">小计</p>
                                                <p id="subtotal-{{ $item['product_id'] }}" class="font-semibold text-gray-900 subtotal">
                                                    HKD {{ number_format($item['price'] * $item['qty'], 2) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div id="cartUpdateError" class="mx-6 mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm" style="display:none;"></div>

                            {{-- Cart Footer --}}
                            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-t border-gray-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-gray-500">总计:</span>
                                        <span id="grandTotal" class="text-2xl font-bold text-gray-900 ml-2">HKD {{ number_format($initialTotal, 2) }}</span>
                                    </div>
                                    <button type="submit" 
                                            class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-gray-700 font-medium rounded-xl transition-colors">
                                        保存更改
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Checkout Panel --}}
                <div class="lg:col-span-1">
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                            <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                结算信息
                            </h2>
                        </div>

                        <div class="p-6">
                            @if (! is_array($customer))
                                {{-- Not Logged In --}}
                                <div class="text-center py-4">
                                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-600 mb-4">请先登录以完成结算</p>
                                    <a href="{{ route('customer.login') }}" 
                                       class="inline-flex items-center justify-center w-full px-5 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:shadow-lg hover:shadow-indigo-500/25 transition-all">
                                        登录账户
                                    </a>
                                </div>
                            @else
                                {{-- Checkout Form --}}
                                <form method="POST" action="{{ route('checkout.submit', [], false) }}" id="checkoutForm">
                                    @csrf
                                    
                                    {{-- Customer Info --}}
                                    <div class="space-y-4 mb-6">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1.5">姓名</label>
                                            <div class="px-4 py-2.5 bg-gray-50 rounded-xl text-gray-700 font-medium">
                                                {{ $customer['name'] }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1.5">联系方式</label>
                                            <div class="px-4 py-2.5 bg-gray-50 rounded-xl text-gray-700">
                                                {{ $customer['contact'] }}
                                            </div>
                                        </div>
                                        <div>
                                            <label for="address" class="block text-xs font-medium text-gray-500 mb-1.5">
                                                收货地址 <span class="text-red-400">*</span>
                                            </label>
                                            <input type="text"
                                                   id="address"
                                                   name="address"
                                                   value="{{ old('address', $customer['address']) }}"
                                                   required
                                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl
                                                          focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all"
                                                   placeholder="请输入收货地址">
                                        </div>
                                    </div>

                                    {{-- Order Summary --}}
                                    <div class="border-t border-gray-100 pt-4 mb-6">
                                        <div class="flex justify-between text-sm text-gray-500 mb-2">
                                            <span>商品数量</span>
                                            <span>{{ count($cartItems) }} 件</span>
                                        </div>
                                        <div class="flex justify-between text-lg font-bold text-gray-900">
                                            <span>应付金额</span>
                                            <span class="text-indigo-600">HKD {{ number_format($initialTotal, 2) }}</span>
                                        </div>
                                    </div>

                                    <div id="checkoutError" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm" style="display:none;"></div>

                                    <button type="submit"
                                            class="w-full py-3.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl
                                                   shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30
                                                   hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                                        确认下单
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
