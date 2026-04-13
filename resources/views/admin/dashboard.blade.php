@extends('layouts.app')

@section('title', '后台统计报表')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">数据统计</h1>
                <p class="text-gray-500 mt-1">实时查看销售数据与订单概况</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.products.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-gray-700 font-medium rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    商品管理
                </a>
                <a href="{{ route('admin.orders.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:shadow-lg hover:shadow-indigo-500/25 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    订单管理
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            {{-- Total Orders --}}
            <div class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6 overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/10 to-cyan-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-blue-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 mb-1">总订单数</p>
                    <p class="text-4xl font-bold text-gray-900">{{ $stats['order_count'] }}</p>
                </div>
            </div>

            {{-- Pending Orders --}}
            <div class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6 overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-500/10 to-orange-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-amber-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 mb-1">待处理订单</p>
                    <p class="text-4xl font-bold text-gray-900">{{ $stats['pending_count'] }}</p>
                </div>
            </div>

            {{-- Total Revenue --}}
            <div class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6 overflow-hidden group hover:shadow-md transition-shadow sm:col-span-2 lg:col-span-1">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-green-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 mb-1">总营业额</p>
                    <p class="text-4xl font-bold text-gray-900">HKD {{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            {{-- Popular Products --}}
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        热门商品 Top 5
                    </h2>
                </div>
                
                @if (count($stats['popular_products']) === 0)
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500">暂无销售数据</p>
                    </div>
                @else
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach ($stats['popular_products'] as $index => $item)
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold
                                                {{ $index === 0 ? 'bg-gradient-to-br from-amber-400 to-orange-500 text-white' : 
                                                   ($index === 1 ? 'bg-gradient-to-br from-gray-300 to-gray-400 text-white' : 
                                                   ($index === 2 ? 'bg-gradient-to-br from-amber-600 to-amber-700 text-white' : 'bg-gray-100 text-gray-600')) }}">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate">{{ $item['name'] }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-full">
                                            {{ $item['qty'] }} 件
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Pending Orders List --}}
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        待处理订单
                    </h2>
                </div>
                
                @if (count($pendingOrders) === 0)
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-gray-500">全部订单已处理完毕</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                        @foreach ($pendingOrders as $order)
                            <div class="p-4 hover:bg-gray-50/50 transition-colors">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <div>
                                        <span class="font-mono text-sm font-semibold text-gray-900">{{ $order['id'] }}</span>
                                        <p class="text-sm text-gray-500 mt-0.5">{{ $order['user_name'] }}</p>
                                    </div>
                                    <span class="text-sm font-semibold text-indigo-600">HKD {{ number_format($order['total'], 2) }}</span>
                                </div>
                                <div class="text-xs text-gray-500">
                                    @foreach ($order['items'] as $item)
                                        <span class="inline-block mr-2">{{ $item['name'] }} × {{ $item['qty'] }}</span>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-400 mt-2">{{ $order['created_at'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
