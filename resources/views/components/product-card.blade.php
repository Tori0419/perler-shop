@props([
    'product',
    'showAddToCart' => true,
])

<div {{ $attributes->merge(['class' => 'group relative overflow-hidden rounded-2xl border border-slate-200/60 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/10']) }}>
    {{-- Image container with overlay --}}
    <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-slate-100 to-slate-50">
        <img
            src="{{ $product['image'] }}"
            alt="{{ $product['name'] }}"
            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
        >
        {{-- Stock badge --}}
        @if ($product['stock'] <= 5 && $product['stock'] > 0)
            <div class="absolute right-3 top-3 rounded-full bg-amber-500/90 px-2.5 py-1 text-xs font-semibold text-white backdrop-blur-sm">
                仅剩 {{ $product['stock'] }} 件
            </div>
        @elseif ($product['stock'] === 0)
            <div class="absolute inset-0 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
                <span class="rounded-full bg-white/90 px-4 py-2 text-sm font-semibold text-slate-700">暂时缺货</span>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-5">
        {{-- Title --}}
        <h3 class="mb-2 line-clamp-1 text-lg font-semibold text-slate-800 transition-colors group-hover:text-indigo-600">
            {{ $product['name'] }}
        </h3>

        {{-- Description --}}
        <p class="mb-4 line-clamp-2 min-h-[2.5rem] text-sm leading-relaxed text-slate-500">
            {{ $product['description'] ?: '暂无描述' }}
        </p>

        {{-- Price & Stock row --}}
        <div class="mb-4 flex items-end justify-between">
            <div>
                <span class="text-xs text-slate-400">价格</span>
                <div class="text-2xl font-bold tracking-tight text-slate-800">
                    <span class="text-base font-medium text-slate-500">HKD</span>
                    {{ number_format($product['price'], 2) }}
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400">库存</span>
                <div class="text-sm font-medium {{ $product['stock'] > 10 ? 'text-emerald-600' : ($product['stock'] > 0 ? 'text-amber-600' : 'text-red-500') }}">
                    {{ $product['stock'] }} 件
                </div>
            </div>
        </div>

        {{-- Add to cart / Login --}}
        @if ($showAddToCart && $product['stock'] > 0)
            @if (session('customer'))
                <form action="{{ route('cart.add', [], false) }}" method="POST" class="add-to-cart-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                    <div class="flex gap-2">
                        <div class="relative w-24">
                            <input
                                type="number"
                                class="shop-qty-input h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-center text-sm font-medium text-slate-700 transition-all focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                                name="qty"
                                min="1"
                                max="{{ $product['stock'] }}"
                                value="1"
                                aria-label="数量"
                            >
                        </div>
                        <button
                            type="submit"
                            class="add-to-cart-btn flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl hover:shadow-indigo-500/30 active:scale-[0.98]"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            加入购物车
                        </button>
                    </div>
                </form>
            @else
                <a
                    href="{{ route('customer.login') }}"
                    class="flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl hover:shadow-indigo-500/30"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    登录后购买
                </a>
            @endif
        @elseif ($product['stock'] === 0)
            <button
                disabled
                class="flex h-11 w-full cursor-not-allowed items-center justify-center rounded-xl bg-slate-100 text-sm font-medium text-slate-400"
            >
                暂时缺货
            </button>
        @endif
    </div>
</div>
