<!DOCTYPE html>
<html lang="zh-CN" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '拼豆在线订购系统')</title>
    {{-- Inter font for premium SaaS look --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-gradient-to-br from-slate-50 via-white to-indigo-50/30 font-sans text-slate-800 antialiased">
    @php
        $customer = session('customer');
        $isAdmin = session('is_admin', false);
        $adminUsername = session('admin_username', 'admin');
        $cart = session('cart', []);
        $cartQty = 0;

        if (is_array($cart)) {
            foreach ($cart as $cartItem) {
                $cartQty += (int) ($cartItem['qty'] ?? 0);
            }
        }

        $currentRoute = request()->route()?->getName() ?? '';
    @endphp

    {{-- Navigation: Transparent → Glassmorphism on scroll --}}
    <nav id="mainNav" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 ease-out">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between lg:h-20">
                {{-- Brand --}}
                <a href="{{ route('shop.index') }}" class="group flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/25 transition-transform duration-300 group-hover:scale-110">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="nav-brand-text text-lg font-bold tracking-tight text-slate-800 transition-colors duration-300 sm:text-xl">
                        拼豆订购
                    </span>
                </a>

                {{-- Desktop Navigation --}}
                <div class="hidden items-center gap-1 md:flex">
                    @if ($isAdmin)
                        <a href="{{ route('admin.dashboard') }}"
                           class="nav-link rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $currentRoute === 'admin.dashboard' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            📊 报表
                        </a>
                        <a href="{{ route('admin.orders.index') }}"
                           class="nav-link rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $currentRoute === 'admin.orders.index' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            📦 订单
                        </a>
                        <a href="{{ route('admin.products.index') }}"
                           class="nav-link rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $currentRoute === 'admin.products.index' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            🏷️ 商品
                        </a>
                        <div class="mx-2 h-6 w-px bg-slate-200"></div>
                        <span class="nav-user-label rounded-full bg-amber-100 px-3 py-1.5 text-xs font-medium text-amber-700">
                            👑 {{ $adminUsername }}
                        </span>
                        <form action="{{ route('admin.logout', [], false) }}" method="POST" class="ml-1">
                            @csrf
                            <button type="submit" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-500 transition-all duration-200 hover:bg-red-50 hover:text-red-600">
                                退出
                            </button>
                        </form>
                    @elseif (is_array($customer))
                        <a href="{{ route('shop.index') }}"
                           class="nav-link rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $currentRoute === 'shop.index' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            🏠 首页
                        </a>
                        {{-- Cart with hover dropdown --}}
                        <div class="group/cart relative">
                            <a href="{{ route('cart.index') }}" id="cartNavLink"
                               class="nav-link relative flex items-center rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $currentRoute === 'cart.index' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                🛒 购物车
                                <span id="cartBadge" class="{{ $cartQty > 0 ? '' : 'hidden' }} absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 text-[10px] font-bold text-white shadow-lg shadow-indigo-500/30 transition-transform duration-300 group-hover/cart:scale-110">
                                    {{ $cartQty > 99 ? '99+' : $cartQty }}
                                </span>
                            </a>

                            {{-- Cart dropdown preview --}}
                            <div class="pointer-events-none absolute right-0 top-full z-50 w-80 pt-2 opacity-0 transition-all duration-200 group-hover/cart:pointer-events-auto group-hover/cart:opacity-100">
                                <div class="overflow-hidden rounded-2xl border border-slate-200/60 bg-white/95 shadow-xl shadow-slate-900/10 backdrop-blur-xl">
                                    @if ($cartQty > 0)
                                        {{-- Cart items --}}
                                        <div class="max-h-80 overflow-y-auto">
                                            <div class="p-3">
                                                <h4 class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                                    <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                    购物车 ({{ $cartQty }} 件)
                                                </h4>
                                                <div class="space-y-2">
                                                    @php $cartTotal = 0; @endphp
                                                    @foreach ($cart as $cartItem)
                                                        @php
                                                            $itemSubtotal = ($cartItem['price'] ?? 0) * ($cartItem['qty'] ?? 0);
                                                            $cartTotal += $itemSubtotal;
                                                        @endphp
                                                        <div class="group/item flex items-center gap-3 rounded-xl bg-slate-50/80 p-2 transition-colors hover:bg-slate-100">
                                                            {{-- Product image --}}
                                                            <div class="relative h-12 w-12 flex-shrink-0 overflow-hidden rounded-lg bg-white shadow-sm">
                                                                @if (!empty($cartItem['image']))
                                                                    <img src="{{ $cartItem['image'] }}"
                                                                         alt="{{ $cartItem['name'] ?? '商品' }}"
                                                                         class="h-full w-full object-cover">
                                                                @else
                                                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-indigo-100 to-purple-100">
                                                                        <svg class="h-5 w-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                                        </svg>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            {{-- Product info --}}
                                                            <div class="min-w-0 flex-1">
                                                                <p class="truncate text-sm font-medium text-slate-700">{{ $cartItem['name'] ?? '未知商品' }}</p>
                                                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                                                    <span>¥{{ number_format($cartItem['price'] ?? 0, 2) }}</span>
                                                                    <span class="text-slate-300">×</span>
                                                                    <span>{{ $cartItem['qty'] ?? 0 }}</span>
                                                                </div>
                                                            </div>
                                                            {{-- Subtotal --}}
                                                            <div class="flex-shrink-0 text-right">
                                                                <span class="text-sm font-semibold text-indigo-600">¥{{ number_format($itemSubtotal, 2) }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Cart footer --}}
                                        <div class="border-t border-slate-100 bg-gradient-to-r from-slate-50 to-indigo-50/50 p-3">
                                            <div class="mb-3 flex items-center justify-between">
                                                <span class="text-sm text-slate-500">合计</span>
                                                <span class="text-lg font-bold text-slate-800">¥{{ number_format($cartTotal, 2) }}</span>
                                            </div>
                                            <a href="{{ route('cart.index') }}"
                                               class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl hover:shadow-indigo-500/30">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                                查看购物车
                                            </a>
                                        </div>
                                    @else
                                        {{-- Empty cart --}}
                                        <div class="p-6 text-center">
                                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                                                <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                            </div>
                                            <p class="mb-1 text-sm font-medium text-slate-700">购物车是空的</p>
                                            <p class="text-xs text-slate-400">快去挑选心仪的拼豆作品吧！</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('orders.history') }}"
                           class="nav-link rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $currentRoute === 'orders.history' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            📋 订单
                        </a>
                        <div class="mx-2 h-6 w-px bg-slate-200"></div>
                        <span class="nav-user-label rounded-full bg-indigo-100 px-3 py-1.5 text-xs font-medium text-indigo-700">
                            {{ $customer['name'] }}
                        </span>
                        <form action="{{ route('customer.logout', [], false) }}" method="POST" class="ml-1">
                            @csrf
                            <button type="submit" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-500 transition-all duration-200 hover:bg-red-50 hover:text-red-600">
                                退出
                            </button>
                        </form>
                    @else
                        <a href="{{ route('shop.index') }}"
                           class="nav-link rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $currentRoute === 'shop.index' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            🏠 首页
                        </a>
                        <div class="mx-2 h-6 w-px bg-slate-200"></div>
                        <a href="{{ route('customer.login') }}"
                           class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-100 hover:text-slate-900">
                            登录
                        </a>
                        <a href="{{ route('admin.login') }}"
                           class="ml-1 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-indigo-500/30">
                            管理入口
                        </a>
                    @endif
                </div>

                {{-- Mobile menu button --}}
                <button id="mobileMenuBtn" type="button" class="inline-flex items-center justify-center rounded-lg p-2 text-slate-600 transition-colors hover:bg-slate-100 md:hidden">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path id="menuIconOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        <path id="menuIconClose" class="hidden" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu panel --}}
        <div id="mobileMenu" class="hidden border-t border-slate-200/50 bg-white/95 backdrop-blur-xl md:hidden">
            <div class="space-y-1 px-4 py-4">
                @if ($isAdmin)
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-4 py-3 text-base font-medium {{ $currentRoute === 'admin.dashboard' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">📊 报表</a>
                    <a href="{{ route('admin.orders.index') }}" class="block rounded-lg px-4 py-3 text-base font-medium {{ $currentRoute === 'admin.orders.index' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">📦 订单</a>
                    <a href="{{ route('admin.products.index') }}" class="block rounded-lg px-4 py-3 text-base font-medium {{ $currentRoute === 'admin.products.index' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">🏷️ 商品</a>
                    <div class="my-2 border-t border-slate-200"></div>
                    <div class="flex items-center justify-between px-4 py-2">
                        <span class="text-sm text-slate-500">👑 {{ $adminUsername }}</span>
                        <form action="{{ route('admin.logout', [], false) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-red-600">退出</button>
                        </form>
                    </div>
                @elseif (is_array($customer))
                    <a href="{{ route('shop.index') }}" class="block rounded-lg px-4 py-3 text-base font-medium {{ $currentRoute === 'shop.index' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">🏠 首页</a>
                    <a href="{{ route('cart.index') }}" class="block rounded-lg px-4 py-3 text-base font-medium {{ $currentRoute === 'cart.index' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">
                        🛒 购物车 @if($cartQty > 0)<span class="ml-2 rounded-full bg-indigo-500 px-2 py-0.5 text-xs text-white">{{ $cartQty }}</span>@endif
                    </a>
                    <a href="{{ route('orders.history') }}" class="block rounded-lg px-4 py-3 text-base font-medium {{ $currentRoute === 'orders.history' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">📋 订单</a>
                    <div class="my-2 border-t border-slate-200"></div>
                    <div class="flex items-center justify-between px-4 py-2">
                        <span class="text-sm text-slate-500">{{ $customer['name'] }}</span>
                        <form action="{{ route('customer.logout', [], false) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-red-600">退出</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('shop.index') }}" class="block rounded-lg px-4 py-3 text-base font-medium {{ $currentRoute === 'shop.index' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">🏠 首页</a>
                    <div class="my-2 border-t border-slate-200"></div>
                    <a href="{{ route('customer.login') }}" class="block rounded-lg px-4 py-3 text-base font-medium text-slate-700 hover:bg-slate-100">登录</a>
                    <a href="{{ route('admin.login') }}" class="block rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-center text-base font-semibold text-white">管理入口</a>
                @endif
            </div>
        </div>
    </nav>

    {{-- Main content with top padding for fixed nav --}}
    <main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-16 pt-24 sm:px-6 lg:px-8 lg:pt-28">
        {{-- Success alert --}}
        @if (session('success'))
            <div class="mb-6 animate-fade-in rounded-xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-teal-50 p-4 shadow-lg shadow-emerald-500/10">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100">
                        <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Error alerts --}}
        @if ($errors->any())
            <div class="mb-6 animate-fade-in rounded-xl border border-red-200 bg-gradient-to-r from-red-50 to-rose-50 p-4 shadow-lg shadow-red-500/10">
                <div class="flex gap-3">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-slate-200/50 bg-white/50 backdrop-blur-sm">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <p class="text-sm text-slate-500">© {{ date('Y') }} 拼豆在线订购系统. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="text-sm text-slate-500 transition-colors hover:text-indigo-600">帮助中心</a>
                    <a href="#" class="text-sm text-slate-500 transition-colors hover:text-indigo-600">联系我们</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Nav scroll effect + mobile menu toggle --}}
    <script>
        (function() {
            const nav = document.getElementById('mainNav');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            const menuIconOpen = document.getElementById('menuIconOpen');
            const menuIconClose = document.getElementById('menuIconClose');

            // Scroll effect: transparent → glassmorphism
            function updateNav() {
                if (window.scrollY > 20) {
                    nav.classList.add('bg-white/80', 'backdrop-blur-xl', 'shadow-lg', 'shadow-slate-900/5');
                } else {
                    nav.classList.remove('bg-white/80', 'backdrop-blur-xl', 'shadow-lg', 'shadow-slate-900/5');
                }
            }
            window.addEventListener('scroll', updateNav, { passive: true });
            updateNav();

            // Mobile menu toggle
            mobileMenuBtn?.addEventListener('click', function() {
                const isHidden = mobileMenu.classList.contains('hidden');
                mobileMenu.classList.toggle('hidden', !isHidden);
                menuIconOpen.classList.toggle('hidden', isHidden);
                menuIconClose.classList.toggle('hidden', !isHidden);
            });
        })();
    </script>

    @yield('scripts')
</body>
</html>
