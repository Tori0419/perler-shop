<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(OrderService $orderService)
    {
        return view('admin.orders.index', [
            'orders' => $orderService->getAllOrders(),
        ]);
    }

    public function ship(Request $request, OrderService $orderService, string $id)
    {
        $updated = $orderService->updateStatus($id, 'shipped');

        if (! $updated) {
            return redirect()
                ->route('admin.orders.index')
                ->withErrors(['order' => '订单不存在或当前状态不可发货。']);
        }

        return redirect()
            ->route('admin.orders.index')
            ->with('success', '订单已标记为已发货。');
    }

    public function cancel(Request $request, OrderService $orderService, string $id)
    {
        $updated = $orderService->updateStatus($id, 'cancelled');

        if (! $updated) {
            return redirect()
                ->route('admin.orders.index')
                ->withErrors(['order' => '订单不存在或当前状态不可取消。']);
        }

        return redirect()
            ->route('admin.orders.index')
            ->with('success', '订单已标记为已取消。');
    }
}
