@extends('layouts.app')

@section('title', '历史订单')

@php
    $statusText = [
        'pending' => '待处理',
        'shipped' => '已发货',
        'cancelled' => '已取消',
    ];

    $statusClass = [
        'pending' => 'badge-warning',
        'shipped' => 'badge-success',
        'cancelled' => 'badge-danger',
    ];
@endphp

@section('content')
    <h2>历史订单</h2>

    @if ($needLogin)
        <div class="card">
            <p class="text-muted">请先登录用户账号后查看个人历史订单。</p>
            <a class="btn btn-primary" href="{{ route('customer.login') }}">去用户登录</a>
        </div>
    @else
        <div class="card mb-2">
            <strong>当前用户：</strong>{{ $customer['name'] ?? '未命名用户' }}（{{ $customer['contact'] ?? '-' }}）
            <div class="text-muted mt-1">如需取消订单，请联系管理员处理，用户端不支持自行取消。</div>
        </div>

        @if (count($orders) === 0)
            <div class="card">
                <p>当前账号还没有历史订单。</p>
            </div>
        @else
            <div class="card table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>订单号</th>
                            <th>日期</th>
                            <th>商品明细</th>
                            <th>总金额</th>
                            <th>状态</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            @php
                                $status = (string) ($order['status'] ?? 'pending');
                            @endphp
                            <tr>
                                <td>{{ $order['id'] }}</td>
                                <td>{{ $order['created_at'] }}</td>
                                <td>
                                    @foreach ($order['items'] as $item)
                                        <div>{{ $item['name'] }} × {{ $item['qty'] }}</div>
                                    @endforeach
                                </td>
                                <td>HKD {{ number_format($order['total'], 2) }}</td>
                                <td>
                                    <span class="badge {{ $statusClass[$status] ?? 'badge-warning' }}">
                                        {{ $statusText[$status] ?? $status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif
@endsection
