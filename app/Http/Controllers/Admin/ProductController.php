<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(ProductService $productService)
    {
        return view('admin.products.index', [
            'products' => $productService->getAllProducts(),
        ]);
    }

    public function store(Request $request, ProductService $productService)
    {
        $validated = $this->validateProduct($request);
        $validated['image'] = $this->resolveImagePath($request, (string) ($validated['image'] ?? ''), null);
        $productService->create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', '商品创建成功。');
    }

    public function update(Request $request, ProductService $productService, int $id)
    {
        $validated = $this->validateProduct($request, true);
        $validated['image'] = $this->resolveImagePath(
            $request,
            (string) ($validated['image'] ?? ''),
            (string) ($validated['old_image'] ?? '')
        );
        unset($validated['old_image']);

        $updated = $productService->update($id, $validated);

        if (! $updated) {
            return redirect()
                ->route('admin.products.index')
                ->withErrors(['product' => '未找到需要更新的商品。']);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', '商品更新成功。');
    }

    public function destroy(ProductService $productService, int $id)
    {
        $deleted = $productService->delete($id);

        if (! $deleted) {
            return redirect()
                ->route('admin.products.index')
                ->withErrors(['product' => '未找到需要删除的商品。']);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', '商品删除成功。');
    }

    private function validateProduct(Request $request, bool $isUpdate = false): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:80'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];

        if ($isUpdate) {
            $rules['old_image'] = ['nullable', 'string', 'max:255'];
        }

        return $request->validate($rules);
    }

    private function resolveImagePath(Request $request, string $imageInput, ?string $oldImage): string
    {
        $uploadedFile = $request->file('image_file');

        if ($uploadedFile instanceof UploadedFile && $uploadedFile->isValid()) {
            $targetDir = public_path('images/uploads/products');

            if (! is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $extension = strtolower($uploadedFile->getClientOriginalExtension());
            $fileName = now()->format('YmdHis').'_'.bin2hex(random_bytes(4)).'.'.$extension;
            $uploadedFile->move($targetDir, $fileName);

            return '/images/uploads/products/'.$fileName;
        }

        $trimmedInput = trim($imageInput);

        if ($trimmedInput !== '') {
            return $trimmedInput;
        }

        if (is_string($oldImage) && trim($oldImage) !== '') {
            return trim($oldImage);
        }

        return '/images/products/default.svg';
    }
}
