@extends('layouts.app')

@section('title', '首页 - 拼豆在线订购系统')

@section('content')
    {{-- Page header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-slate-800 sm:text-4xl">
            探索拼豆作品
        </h1>
        <p class="mt-2 text-lg text-slate-500">
            精选手工拼豆艺术品，为您的生活增添色彩
        </p>
    </div>

    {{-- Search bar --}}
    <x-search-bar
        :action="route('shop.index')"
        :value="$keyword"
        label="搜索拼豆作品"
        placeholder="输入商品名称或描述关键字..."
        class="mb-8"
    />

    {{-- Add to cart status toast --}}
    <div
        id="addCartStatus"
        class="mb-6 hidden rounded-xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-teal-50 p-4 shadow-lg shadow-emerald-500/10"
    ></div>

    {{-- Results --}}
    @if ($keyword)
        <div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            搜索 "<span class="font-medium text-slate-700">{{ $keyword }}</span>" 找到 {{ count($products) }} 个结果
        </div>
    @endif

    @if (count($products) === 0)
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white/50 py-16 text-center">
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <h3 class="mb-2 text-lg font-semibold text-slate-700">没有找到商品</h3>
            <p class="mb-6 text-slate-500">尝试使用其他关键字搜索</p>
            @if ($keyword)
                <a
                    href="{{ route('shop.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white transition-all hover:bg-indigo-600"
                >
                    查看全部商品
                </a>
            @endif
        </div>
    @else
        {{-- Product grid --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    @endif
@endsection