<?php

namespace App\Services;

class OrderService
{
    private const FILE = 'orders.json';
    private const ALLOWED_STATUSES = ['pending', 'shipped', 'cancelled'];

    public function __construct(private readonly FileStorageService $storageService)
    {
    }

    public function createOrder(array $payload): array
    {
        $orders = $this->storageService->readJson(self::FILE, []);
        $order = [
            'id' => $this->generateOrderId(),
            'user_id' => (string) $payload['user_id'],
            'user_name' => (string) $payload['user_name'],
            'address' => (string) $payload['address'],
            'contact' => (string) $payload['contact'],
            'items' => $payload['items'],
            'total' => (float) $payload['total'],
            'status' => (string) ($payload['status'] ?? 'pending'),
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];

        $orders[] = $order;

        $this->storageService->writeJson(self::FILE, $orders);

        return $order;
    }

    public function getOrdersByUser(string $userId): array
    {
        $orders = $this->storageService->readJson(self::FILE, []);

        $filtered = array_values(array_filter(
            $orders,
            fn (array $order): bool => (string) ($order['user_id'] ?? '') === $userId
        ));

        usort($filtered, fn (array $a, array $b): int => strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? '')));

        return $filtered;
    }

    public function getPendingOrders(): array
    {
        $orders = $this->storageService->readJson(self::FILE, []);

        return array_values(array_filter(
            $orders,
            fn (array $order): bool => (string) ($order['status'] ?? 'pending') === 'pending'
        ));
    }

    public function getAllOrders(): array
    {
        $orders = $this->storageService->readJson(self::FILE, []);

        usort($orders, fn (array $a, array $b): int => strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? '')));

        return $orders;
    }

    public function updateStatus(string $id, string $status): bool
    {
        if (! in_array($status, self::ALLOWED_STATUSES, true)) {
            return false;
        }

        $orders = $this->storageService->readJson(self::FILE, []);

        foreach ($orders as $index => $order) {
            if ((string) ($order['id'] ?? '') !== $id) {
                continue;
            }

            if ((string) ($order['status'] ?? 'pending') !== 'pending') {
                return false;
            }

            $orders[$index]['status'] = $status;
            $orders[$index]['updated_at'] = now()->format('Y-m-d H:i:s');
            $this->storageService->writeJson(self::FILE, $orders);

            return true;
        }

        return false;
    }

    public function getDashboardStats(): array
    {
        $orders = $this->storageService->readJson(self::FILE, []);
        $pendingCount = 0;
        $totalRevenue = 0.0;
        $salesCounter = [];

        foreach ($orders as $order) {
            $totalRevenue += (float) ($order['total'] ?? 0);

            if (($order['status'] ?? 'pending') === 'pending') {
                $pendingCount++;
            }

            foreach ($order['items'] ?? [] as $item) {
                $name = (string) ($item['name'] ?? '未知商品');
                $qty = (int) ($item['qty'] ?? 0);
                $salesCounter[$name] = ($salesCounter[$name] ?? 0) + $qty;
            }
        }

        arsort($salesCounter);

        $popularProducts = [];

        foreach (array_slice($salesCounter, 0, 5, true) as $name => $qty) {
            $popularProducts[] = [
                'name' => $name,
                'qty' => $qty,
            ];
        }

        return [
            'order_count' => count($orders),
            'pending_count' => $pendingCount,
            'total_revenue' => round($totalRevenue, 2),
            'popular_products' => $popularProducts,
        ];
    }

    private function generateOrderId(): string
    {
        return 'ORD'.now()->format('YmdHis').random_int(100, 999);
    }
}
