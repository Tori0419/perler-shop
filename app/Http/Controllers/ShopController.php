<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, ProductService $productService)
    {
        $keyword = trim((string) $request->query('q', ''));
        $products = $productService->getActiveProducts($keyword);

        return view('shop.index', [
            'products' => $products,
            'keyword' => $keyword,
        ]);
    }
}
