@extends('layouts.app')

@section('title', '用户登录')

@section('content')
    <h2>用户登录</h2>

    <div class="card mb-2">
        <h3 style="margin-top: 0;">演示账号（写死）</h3>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>用户名</th>
                        <th>密码</th>
                        <th>姓名</th>
                        <th>联系方式</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($accounts as $account)
                        <tr>
                            <td><code>{{ $account['username'] }}</code></td>
                            <td><code>{{ $account['password'] }}</code></td>
                            <td>{{ $account['name'] }}</td>
                            <td>{{ $account['contact'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" style="max-width: 520px;">
        <form action="{{ route('customer.login.submit', [], false) }}" method="POST">
            @csrf
            <label for="username">用户名</label>
            <input class="input mt-1 mb-2" id="username" name="username" value="{{ old('username') }}" required>

            <label for="password">密码</label>
            <input class="input mt-1" id="password" name="password" type="password" required>

            <button class="btn btn-primary mt-2" type="submit">登录用户</button>
        </form>
    </div>
@endsection
