<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function checkout(Request $request, OrderService $orderService, ProductService $productService, CartService $cartService)
    {
        $customer = $request->session()->get('customer');

        if (! is_array($customer)) {
            return redirect()
                ->route('customer.login')
                ->withErrors(['auth' => '请先登录用户账号后再下单。']);
        }

        $validated = $request->validate([
            'address' => ['required', 'string', 'max:255'],
        ]);

        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            throw ValidationException::withMessages(['cart' => '购物车为空，请先添加商品。']);
        }

        $items = [];
        $total = 0.0;

        foreach ($cart as $cartItem) {
            $quantity = (int) ($cartItem['qty'] ?? 0);

            if ($quantity <= 0) {
                throw ValidationException::withMessages(['cart' => '购物车中存在非法数量。']);
            }

            $price = (float) ($cartItem['price'] ?? 0);
            $subtotal = round($price * $quantity, 2);
            $total += $subtotal;

            $items[] = [
                'product_id' => (int) ($cartItem['product_id'] ?? 0),
                'name' => (string) ($cartItem['name'] ?? ''),
                'price' => $price,
                'qty' => $quantity,
                'subtotal' => $subtotal,
            ];
        }

        $stockResult = $productService->consumeStockByItems($items);

        if (! ($stockResult['ok'] ?? false)) {
            throw ValidationException::withMessages([
                'cart' => (string) ($stockResult['message'] ?? '库存校验失败，请稍后重试。'),
            ]);
        }

        $orderService->createOrder([
            'user_id' => (string) ($customer['id'] ?? ''),
            'user_name' => (string) ($customer['name'] ?? ''),
            'address' => $validated['address'],
            'contact' => (string) ($customer['contact'] ?? ''),
            'items' => $items,
            'total' => round($total, 2),
            'status' => 'pending',
        ]);

        $customer['address'] = $validated['address'];
        $request->session()->put('customer', $customer);
        $request->session()->forget('cart');

        // Clear persisted cart after successful order
        $cartService->clearCart($customer['id']);

        return redirect()
            ->route('orders.history')
            ->with('success', '下单成功，已为你保存订单记录。');
    }

    public function history(Request $request, OrderService $orderService)
    {
        $customer = $request->session()->get('customer');

        if (! is_array($customer)) {
            return view('orders.history', [
                'needLogin' => true,
                'orders' => [],
                'customer' => null,
            ]);
        }

        return view('orders.history', [
            'needLogin' => false,
            'orders' => $orderService->getOrdersByUser((string) ($customer['id'] ?? '')),
            'customer' => $customer,
        ]);
    }
}
