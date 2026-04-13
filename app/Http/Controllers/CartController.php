<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        return view('cart.index', [
            'cartItems' => array_values($cart),
        ]);
    }

    public function add(Request $request, ProductService $productService)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'qty' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = $productService->findActiveById((int) $validated['product_id']);

        if (! $product) {
            throw ValidationException::withMessages(['product_id' => '商品不存在或已下架。']);
        }

        $quantity = (int) ($validated['qty'] ?? 1);
        $cart = $request->session()->get('cart', []);
        $productId = (int) $product['id'];
        $stock = (int) ($product['stock'] ?? 0);
        $existingQty = (int) ($cart[$productId]['qty'] ?? 0);

        if ($stock <= 0) {
            throw ValidationException::withMessages(['product_id' => '库存不足，暂时无法加入购物车。']);
        }

        if (($existingQty + $quantity) > $stock) {
            throw ValidationException::withMessages([
                'qty' => "库存不足，当前最多可购买 {$stock} 件。",
            ]);
        }

        if (isset($cart[$productId])) {
            $cart[$productId]['qty'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'name' => $product['name'],
                'price' => (float) $product['price'],
                'qty' => $quantity,
                'image' => $product['image'] ?? '',
            ];
        }

        $request->session()->put('cart', $cart);

        // Sync to persistent storage
        $this->cartService->syncToStorage($request);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => '已加入购物车。',
                'product_id' => $productId,
                'product_qty' => (int) ($cart[$productId]['qty'] ?? 0),
                'cart_count' => count($cart),
                'cart_qty' => $this->cartService->calculateQuantity($cart),
            ]);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', '已加入购物车。');
    }

    public function update(Request $request, ProductService $productService)
    {
        $validated = $request->validate([
            'quantities' => ['required', 'array'],
        ]);

        $cart = $request->session()->get('cart', []);

        foreach ($validated['quantities'] as $productId => $quantity) {
            $productId = (int) $productId;
            $quantity = (int) $quantity;

            if ($quantity <= 0) {
                throw ValidationException::withMessages([
                    "quantities.$productId" => '数量必须为正整数。',
                ]);
            }

            if (isset($cart[$productId])) {
                $product = $productService->findActiveById($productId);
                $stock = (int) ($product['stock'] ?? 0);

                if (! is_array($product)) {
                    throw ValidationException::withMessages([
                        "quantities.$productId" => '商品不存在或已下架。',
                    ]);
                }

                if ($quantity > $stock) {
                    throw ValidationException::withMessages([
                        "quantities.$productId" => "库存不足，当前最多可购买 {$stock} 件。",
                    ]);
                }

                $cart[$productId]['qty'] = $quantity;
            }
        }

        $request->session()->put('cart', $cart);

        // Sync to persistent storage
        $this->cartService->syncToStorage($request);

        return redirect()
            ->route('cart.index')
            ->with('success', '购物车已更新。');
    }

    public function updateAjax(Request $request, ProductService $productService)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $request->session()->get('cart', []);
        $productId = (int) $validated['product_id'];
        $quantity = (int) $validated['qty'];

        if (! isset($cart[$productId])) {
            throw ValidationException::withMessages([
                'product_id' => '购物车中不存在该商品。',
            ]);
        }

        $product = $productService->findActiveById($productId);
        $stock = (int) ($product['stock'] ?? 0);

        if (! is_array($product)) {
            throw ValidationException::withMessages([
                'product_id' => '商品不存在或已下架。',
            ]);
        }

        if ($quantity > $stock) {
            throw ValidationException::withMessages([
                'qty' => "库存不足，当前最多可购买 {$stock} 件。",
            ]);
        }

        $cart[$productId]['qty'] = $quantity;
        $request->session()->put('cart', $cart);

        // Sync to persistent storage
        $this->cartService->syncToStorage($request);

        $subtotal = round(((float) $cart[$productId]['price']) * $quantity, 2);
        $total = $this->cartService->calculateTotal($cart);

        return response()->json([
            'ok' => true,
            'product_id' => $productId,
            'subtotal' => $subtotal,
            'total' => $total,
        ]);
    }

    public function remove(Request $request, int $id)
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$id]);
        $request->session()->put('cart', $cart);

        // Sync to persistent storage
        $this->cartService->syncToStorage($request);

        if ($request->expectsJson() || $request->ajax()) {
            $total = $this->cartService->calculateTotal($cart);
            $cartQty = $this->cartService->calculateQuantity($cart);

            return response()->json([
                'ok'       => true,
                'cart_qty' => $cartQty,
                'total'    => $total,
            ]);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', '商品已移除。');
    }
}
