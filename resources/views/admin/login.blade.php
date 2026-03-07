@extends('layouts.app')

@section('title', '管理员登录')

@section('content')
    <h2>管理员登录</h2>
    <div class="card" style="max-width: 460px;">
        <p class="text-muted">默认账号：<code>admin</code>，默认密码：<code>admin123</code></p>
        <form method="POST" action="{{ route('admin.login.submit', [], false) }}">
            @csrf
            <label for="username">用户名</label>
            <input class="input mt-1 mb-2" id="username" name="username" value="{{ old('username') }}" required>

            <label for="password">密码</label>
            <input class="input mt-1" id="password" name="password" type="password" required>

            <button type="submit" class="btn btn-primary mt-2">登录后台</button>
        </form>
    </div>
@endsection
