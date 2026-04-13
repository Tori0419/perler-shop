<?php

namespace App\Services;

use Illuminate\Http\Request;

class CartService
{
    private const FILENAME = 'carts.json';

    public function __construct(
        private FileStorageService $fileStorage
    ) {}

    /**
     * Get cart for a specific user from persistent storage.
     */
    public function getCart(string $customerId): array
    {
        $carts = $this->fileStorage->readJson(self::FILENAME, []);

        return $carts[$customerId] ?? [];
    }

    /**
     * Save cart for a specific user to persistent storage.
     */
    public function saveCart(string $customerId, array $cart): void
    {
        $carts = $this->fileStorage->readJson(self::FILENAME, []);
        $carts[$customerId] = $cart;
        $this->fileStorage->writeJson(self::FILENAME, $carts);
    }

    /**
     * Clear cart for a specific user.
     */
    public function clearCart(string $customerId): void
    {
        $carts = $this->fileStorage->readJson(self::FILENAME, []);
        unset($carts[$customerId]);
        $this->fileStorage->writeJson(self::FILENAME, $carts);
    }

    /**
     * Load user's persistent cart into session on login.
     */
    public function loadToSession(Request $request, string $customerId): void
    {
        $persistedCart = $this->getCart($customerId);
        $sessionCart = $request->session()->get('cart', []);

        // Merge: session cart takes priority (guest might have added items)
        $mergedCart = $this->mergeCarts($sessionCart, $persistedCart);

        $request->session()->put('cart', $mergedCart);

        // Save merged cart back to storage
        if (!empty($mergedCart)) {
            $this->saveCart($customerId, $mergedCart);
        }
    }

    /**
     * Save session cart to persistent storage on logout or cart change.
     */
    public function saveFromSession(Request $request, string $customerId): void
    {
        $cart = $request->session()->get('cart', []);
        $this->saveCart($customerId, $cart);
    }

    /**
     * Sync session cart to storage (call after cart modifications).
     */
    public function syncToStorage(Request $request): void
    {
        $customer = $request->session()->get('customer');

        if (!is_array($customer) || empty($customer['id'])) {
            return;
        }

        $cart = $request->session()->get('cart', []);
        $this->saveCart($customer['id'], $cart);
    }

    /**
     * Merge two carts, session cart takes priority for existing items.
     */
    private function mergeCarts(array $sessionCart, array $persistedCart): array
    {
        $merged = $sessionCart;

        foreach ($persistedCart as $productId => $item) {
            if (!isset($merged[$productId])) {
                $merged[$productId] = $item;
            }
        }

        return $merged;
    }

    /**
     * Calculate total quantity in cart.
     */
    public function calculateQuantity(array $cart): int
    {
        $quantity = 0;

        foreach ($cart as $item) {
            $quantity += (int) ($item['qty'] ?? 0);
        }

        return $quantity;
    }

    /**
     * Calculate total price of cart.
     */
    public function calculateTotal(array $cart): float
    {
        $total = 0.0;

        foreach ($cart as $item) {
            $total += ((float) ($item['price'] ?? 0)) * ((int) ($item['qty'] ?? 0));
        }

        return round($total, 2);
    }
}
