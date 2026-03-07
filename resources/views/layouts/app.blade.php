<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '拼豆在线订购系统')</title>
    <style>
        :root {
            --bg: #f4f6fb;
            --card: #ffffff;
            --text: #111827;
            --muted: #6b7280;
            --primary: #4f46e5;
            --danger: #dc2626;
            --success: #16a34a;
            --border: #e5e7eb;
        }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Noto Sans SC", sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        .container {
            width: min(1100px, 92vw);
            margin: 0 auto;
        }
        .nav {
            background: #111827;
            color: #fff;
            margin-bottom: 24px;
        }
        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 0;
        }
        .brand {
            font-weight: 700;
            letter-spacing: 0.4px;
        }
        .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        .nav a {
            color: #fff;
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 14px;
        }
        .nav a:hover {
            background: rgba(255,255,255,0.14);
        }
        .nav form {
            margin: 0;
        }
        .inline-form-button {
            border: none;
            border-radius: 8px;
            background: rgba(255,255,255,0.14);
            color: #fff;
            font-size: 13px;
            padding: 6px 10px;
            cursor: pointer;
        }
        .inline-form-button:hover {
            background: rgba(255,255,255,0.22);
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 16px;
        }
        .product-image {
            width: 100%;
            height: 160px;
            object-fit: cover;
            background: #f9fafb;
            border-radius: 10px;
            border: 1px solid var(--border);
            margin-bottom: 10px;
        }
        .btn {
            border: none;
            cursor: pointer;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-muted { background: #e5e7eb; color: #111827; }
        .btn-success { background: var(--success); color: #fff; }
        .input, .select, .textarea {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 9px 10px;
            font-size: 14px;
            background: #fff;
        }
        .textarea { resize: vertical; min-height: 76px; }
        .row {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 12px;
        }
        .col-2 { grid-column: span 2; }
        .col-3 { grid-column: span 3; }
        .col-4 { grid-column: span 4; }
        .col-6 { grid-column: span 6; }
        .col-8 { grid-column: span 8; }
        .col-9 { grid-column: span 9; }
        .col-12 { grid-column: span 12; }
        .mt-1 { margin-top: 8px; }
        .mt-2 { margin-top: 16px; }
        .mt-3 { margin-top: 24px; }
        .mb-2 { margin-bottom: 16px; }
        .text-muted { color: var(--muted); }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 12px;
            border-radius: 100px;
            background: #e5e7eb;
        }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .table-wrap { overflow: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            border: 1px solid var(--border);
            padding: 10px;
            font-size: 14px;
            vertical-align: top;
        }
        th {
            background: #f9fafb;
            text-align: left;
        }
        .alert {
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 12px;
            font-size: 14px;
        }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .invalid { border-color: #dc2626 !important; }
        @media (max-width: 760px) {
            .row { grid-template-columns: 1fr; }
            .col-2, .col-3, .col-4, .col-6, .col-8, .col-9, .col-12 { grid-column: span 1; }
        }
    </style>
</head>
<body>
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
    @endphp
    <nav class="nav">
        <div class="container nav-inner">
            <div class="brand">拼豆在线订购系统</div>
            <div class="nav-links">
                @if ($isAdmin)
                    <a href="{{ route('admin.dashboard') }}">后台报表</a>
                    <a href="{{ route('admin.orders.index') }}">后台订单</a>
                    <a href="{{ route('admin.products.index') }}">后台商品</a>
                    <span style="font-size: 13px; opacity: 0.9;">管理员：{{ $adminUsername }}</span>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-form-button">退出管理员</button>
                    </form>
                @elseif (is_array($customer))
                    <a href="{{ route('shop.index') }}">首页</a>
                    <a href="{{ route('cart.index') }}" id="cartNavLink">购物车{{ $cartQty > 0 ? "（{$cartQty}）" : '' }}</a>
                    <a href="{{ route('orders.history') }}">历史订单</a>
                    <span style="font-size: 13px; opacity: 0.9;">用户：{{ $customer['name'] }}</span>
                    <form action="{{ route('customer.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-form-button">退出用户</button>
                    </form>
                @else
                    <a href="{{ route('customer.login') }}">用户登录</a>
                    <a href="{{ route('admin.login') }}">管理员登录</a>
                @endif
            </div>
        </div>
    </nav>

    <main class="container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
