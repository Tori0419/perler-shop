<?php

namespace App\Services;

class ProductService
{
    private const FILE = 'products.json';

    public function __construct(private readonly FileStorageService $storageService)
    {
    }

    public function getActiveProducts(?string $keyword = null): array
    {
        $products = array_values(array_filter(
            $this->readProducts(),
            fn (array $product): bool => ($product['status'] ?? 'active') === 'active'
        ));

        if (! $keyword) {
            return $products;
        }

        $keyword = mb_strtolower(trim($keyword));

        return array_values(array_filter(
            $products,
            function (array $product) use ($keyword): bool {
                $name = mb_strtolower((string) ($product['name'] ?? ''));

                return str_contains($name, $keyword);
            }
        ));
    }

    public function getAllProducts(): array
    {
        return $this->readProducts();
    }

    public function findActiveById(int $id): ?array
    {
        foreach ($this->getActiveProducts() as $product) {
            if ((int) ($product['id'] ?? 0) === $id) {
                return $product;
            }
        }

        return null;
    }

    public function create(array $input): array
    {
        $products = $this->readProducts();
        $nextId = (int) max(array_column($products, 'id') ?: [0]) + 1;

        $product = [
            'id' => $nextId,
            'name' => trim((string) $input['name']),
            'price' => (float) $input['price'],
            'description' => trim((string) $input['description']),
            'image' => trim((string) ($input['image'] ?? '/images/products/default.svg')),
            'stock' => (int) ($input['stock'] ?? 0),
            'status' => (string) ($input['status'] ?? 'active'),
        ];

        $products[] = $product;
        $this->storageService->writeJson(self::FILE, $products);

        return $product;
    }

    public function update(int $id, array $input): bool
    {
        $products = $this->readProducts();

        foreach ($products as $index => $product) {
            if ((int) ($product['id'] ?? 0) !== $id) {
                continue;
            }

            $products[$index] = [
                'id' => $id,
                'name' => trim((string) $input['name']),
                'price' => (float) $input['price'],
                'description' => trim((string) $input['description']),
                'image' => trim((string) ($input['image'] ?? '/images/products/default.svg')),
                'stock' => (int) ($input['stock'] ?? 0),
                'status' => (string) ($input['status'] ?? 'active'),
            ];

            $this->storageService->writeJson(self::FILE, array_values($products));

            return true;
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $products = $this->readProducts();
        $filtered = array_values(array_filter(
            $products,
            fn (array $product): bool => (int) ($product['id'] ?? 0) !== $id
        ));

        if (count($filtered) === count($products)) {
            return false;
        }

        $this->storageService->writeJson(self::FILE, $filtered);

        return true;
    }

    public function consumeStockByItems(array $items): array
    {
        $requiredQuantities = [];

        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['qty'] ?? 0);

            if ($productId <= 0 || $quantity <= 0) {
                return [
                    'ok' => false,
                    'message' => '订单商品数据无效，请刷新页面后重试。',
                ];
            }

            $requiredQuantities[$productId] = ($requiredQuantities[$productId] ?? 0) + $quantity;
        }

        $products = $this->readProducts();
        $productIndexById = [];

        foreach ($products as $index => $product) {
            $productIndexById[(int) ($product['id'] ?? 0)] = $index;
        }

        foreach ($requiredQuantities as $productId => $requiredQuantity) {
            if (! isset($productIndexById[$productId])) {
                return [
                    'ok' => false,
                    'message' => "商品 #{$productId} 不存在或已下架。",
                ];
            }

            $index = $productIndexById[$productId];
            $stock = (int) ($products[$index]['stock'] ?? 0);
            $name = (string) ($products[$index]['name'] ?? "商品 #{$productId}");

            if ($stock < $requiredQuantity) {
                return [
                    'ok' => false,
                    'message' => "库存不足：{$name} 仅剩 {$stock} 件。",
                ];
            }
        }

        foreach ($requiredQuantities as $productId => $requiredQuantity) {
            $index = $productIndexById[$productId];
            $products[$index]['stock'] = (int) ($products[$index]['stock'] ?? 0) - $requiredQuantity;
        }

        $this->storageService->writeJson(self::FILE, array_values($products));

        return ['ok' => true];
    }

    private function readProducts(): array
    {
        return $this->storageService->readJson(self::FILE, $this->defaultProducts());
    }

    private function defaultProducts(): array
    {
        return [
            [
                'id' => 1,
                'name' => '布丁狗拼豆挂件',
                'price' => 18.0,
                'description' => '来自网络素材的真实拼豆像素图样，适合新手复刻。',
                'image' => '/images/products/perler-heart.jpg',
                'stock' => 40,
                'status' => 'active',
            ],
            [
                'id' => 2,
                'name' => '复古像素拼豆拼板',
                'price' => 25.0,
                'description' => '像素角色主题拼豆样式，适合展示墙。',
                'image' => '/images/products/perler-mario.jpg',
                'stock' => 25,
                'status' => 'active',
            ],
            [
                'id' => 3,
                'name' => '皮卡丘拼豆图样',
                'price' => 22.0,
                'description' => '经典角色像素拼豆，适合做钥匙扣或摆件。',
                'image' => '/images/products/perler-pikachu.jpg',
                'stock' => 30,
                'status' => 'active',
            ],
        ];
    }
}
