@extends('layouts.app')

@section('title', '管理员登录')

@section('content')
    <x-login-panel 
        title="管理后台"
        subtitle="Perler Shop 管理系统"
        :action="route('admin.login.submit', [], false)"
        submitText="登录后台"
        gradient="from-slate-700 to-slate-900"
        icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
    >
        <x-slot:demo>
            <span class="font-medium text-amber-700">演示账号：</span>
            <code class="px-2 py-0.5 bg-amber-100 rounded text-amber-800 font-mono">admin</code>
            <span class="text-amber-600 mx-1">密码：</span>
            <code class="px-2 py-0.5 bg-amber-100 rounded text-amber-800 font-mono">admin123</code>
        </x-slot:demo>

        <x-slot:footer>
            <a href="{{ route('shop.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">
                ← 返回商城首页
            </a>
        </x-slot:footer>
    </x-login-panel>
@endsection
