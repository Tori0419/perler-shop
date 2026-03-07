<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UploadController extends Controller
{
    public function productImage(string $filename): BinaryFileResponse
    {
        if ($filename !== basename($filename)) {
            abort(404);
        }

        $baseDir = trim((string) config('shop.upload_dir', ''));

        if ($baseDir === '') {
            $baseDir = public_path('images/uploads/products');
        } elseif (! str_starts_with($baseDir, '/')) {
            $baseDir = base_path($baseDir);
        }

        $fullPath = rtrim($baseDir, '/').'/'.$filename;

        if (! is_file($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
