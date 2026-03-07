@extends('layouts.app')

@section('title', '后台订单管理')

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
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;">
        <h2>后台订单管理</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-muted">返回统计页</a>
    </div>

    <div class="card">
        @if (count($orders) === 0)
            <p class="text-muted">暂无订单。</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>订单号</th>
                            <th>用户</th>
                            <th>商品明细</th>
                            <th>订单金额</th>
                            <th>状态</th>
                            <th>下单时间</th>
                            <th>管理操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            @php
                                $status = (string) ($order['status'] ?? 'pending');
                            @endphp
                            <tr>
                                <td>{{ $order['id'] }}</td>
                                <td>
                                    <div>{{ $order['user_name'] }}</div>
                                    <div class="text-muted">{{ $order['contact'] }}</div>
                                </td>
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
                                <td>{{ $order['created_at'] }}</td>
                                <td style="min-width: 130px;">
                                    @if ($status === 'pending')
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <form action="{{ route('admin.orders.ship', $order['id']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success">发货</button>
                                            </form>
                                            <form action="{{ route('admin.orders.cancel', $order['id']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-danger">取消</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted">已处理</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
