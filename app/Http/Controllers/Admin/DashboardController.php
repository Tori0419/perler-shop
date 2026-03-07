<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;

class DashboardController extends Controller
{
    public function index(OrderService $orderService)
    {
        return view('admin.dashboard', [
            'stats' => $orderService->getDashboardStats(),
            'pendingOrders' => $orderService->getPendingOrders(),
        ]);
    }
}
