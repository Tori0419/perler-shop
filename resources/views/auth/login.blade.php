@extends('layouts.app')

@section('title', '用户登录')

@section('content')
    <x-login-panel 
        title="用户登录"
        subtitle="欢迎回到 Perler Shop"
        :action="route('customer.login.submit', [], false)"
        submitText="登录账户"
        gradient="from-indigo-500 to-purple-600"
    >
        <x-slot:demo>
            <div class="space-y-2">
                <p class="font-medium text-amber-700 mb-2">演示账号</p>
                <div class="overflow-x-auto -mx-1">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-amber-200">
                                <th class="px-1 py-1.5 font-medium text-amber-700">用户名</th>
                                <th class="px-1 py-1.5 font-medium text-amber-700">密码</th>
                                <th class="px-1 py-1.5 font-medium text-amber-700">姓名</th>
                            </tr>
                        </thead>
                        <tbody class="text-amber-800">
                            @foreach ($accounts as $account)
                                <tr class="border-b border-amber-100 last:border-0">
                                    <td class="px-1 py-1.5"><code class="bg-amber-100 px-1 rounded">{{ $account['username'] }}</code></td>
                                    <td class="px-1 py-1.5"><code class="bg-amber-100 px-1 rounded">{{ $account['password'] }}</code></td>
                                    <td class="px-1 py-1.5">{{ $account['name'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-slot:demo>

        <x-slot:footer>
            <p class="text-sm text-gray-500">
                <a href="{{ route('shop.index') }}" class="text-indigo-600 hover:text-indigo-700 font-medium transition-colors">
                    返回商城
                </a>
                <span class="mx-2">•</span>
                <a href="{{ route('admin.login') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    管理员入口
                </a>
            </p>
        </x-slot:footer>
    </x-login-panel>
@endsection
