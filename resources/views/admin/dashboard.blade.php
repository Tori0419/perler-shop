@extends('layouts.app')

@section('title', '后台统计报表')

@section('content')
    <div class="row-between">
        <h2>订单统计与报表</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('admin.products.index') }}" class="btn btn-muted">去商品管理</a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">去订单管理</a>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-4">
            <div class="card">
                <div class="text-muted">总订单数</div>
                <div style="font-size: 28px; font-weight: 700;">{{ $stats['order_count'] }}</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card">
                <div class="text-muted">待处理订单</div>
                <div style="font-size: 28px; font-weight: 700;">{{ $stats['pending_count'] }}</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card">
                <div class="text-muted">总营业额</div>
                <div style="font-size: 28px; font-weight: 700;">HKD {{ number_format($stats['total_revenue'], 2) }}</div>
            </div>
        </div>
    </div>

    <div class="card mb-2">
        <h3 style="margin-top: 0;">热门商品 Top 5</h3>
        @if (count($stats['popular_products']) === 0)
            <p class="text-muted">暂无销售数据。</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>商品名</th>
                            <th>销量</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stats['popular_products'] as $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['qty'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="card">
        <h3 style="margin-top: 0;">待处理订单列表</h3>
        @if (count($pendingOrders) === 0)
            <p class="text-muted">当前没有待处理订单。</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>订单号</th>
                            <th>用户</th>
                            <th>商品</th>
                            <th>金额</th>
                            <th>时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingOrders as $order)
                            <tr>
                                <td>{{ $order['id'] }}</td>
                                <td>{{ $order['user_name'] }}（{{ $order['contact'] }}）</td>
                                <td>
                                    @foreach ($order['items'] as $item)
                                        <div>{{ $item['name'] }} × {{ $item['qty'] }}</div>
                                    @endforeach
                                </td>
                                <td>HKD {{ number_format($order['total'], 2) }}</td>
                                <td>{{ $order['created_at'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
