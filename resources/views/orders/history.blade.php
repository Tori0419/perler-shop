@extends('layouts.app')

@section('title', '历史订单')

@include('partials.order-status-maps')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-8">
        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">我的订单</h1>
            <p class="text-gray-500 mt-1">查看您的历史购买记录</p>
        </div>

        @if ($needLogin)
            {{-- Not Logged In --}}
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">请先登录</h3>
                <p class="text-gray-500 mb-6">登录后查看您的历史订单</p>
                <a href="{{ route('customer.login') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:shadow-lg hover:shadow-indigo-500/25 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    去登录
                </a>
            </div>
        @else
            {{-- User Info Card --}}
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 mb-8 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">{{ $customer['name'] ?? '未命名用户' }}</h3>
                        <p class="text-white/80 text-sm">{{ $customer['contact'] ?? '-' }}</p>
                    </div>
                </div>
                <p class="text-white/70 text-sm mt-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    如需取消订单，请联系管理员处理
                </p>
            </div>

            @if (count($orders) === 0)
                {{-- No Orders --}}
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-50 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">暂无订单</h3>
                    <p class="text-gray-500 mb-6">您还没有任何订单记录</p>
                    <a href="{{ route('shop.index') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:shadow-lg hover:shadow-indigo-500/25 transition-all duration-200">
                        去逛逛
                    </a>
                </div>
            @else
                {{-- Orders List --}}
                <div class="space-y-4">
                    @foreach ($orders as $order)
                        @php
                            $status = (string) ($order['status'] ?? 'pending');
                            $statusColors = [
                                'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'shipped' => 'bg-green-100 text-green-700 border-green-200',
                                'cancelled' => 'bg-red-100 text-red-700 border-red-200',
                            ];
                        @endphp
                        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                            {{-- Order Header --}}
                            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <span class="text-sm text-gray-500">订单号</span>
                                        <span class="font-mono font-semibold text-gray-900">{{ $order['id'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="text-sm text-gray-500">{{ $order['created_at'] }}</span>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full border {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $statusText[$status] ?? $status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Order Items --}}
                            <div class="p-6">
                                <div class="space-y-3">
                                    @foreach ($order['items'] as $item)
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-700">{{ $item['name'] }}</span>
                                            <span class="text-gray-500">× {{ $item['qty'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
                                    <div class="text-right">
                                        <span class="text-sm text-gray-500">订单金额</span>
                                        <p class="text-xl font-bold text-indigo-600">HKD {{ number_format($order['total'], 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
@endsection
