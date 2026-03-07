<?php

return [
    'data_dir' => env('SHOP_DATA_DIR', storage_path('app/data')),
    'upload_dir' => env('SHOP_UPLOAD_DIR', public_path('images/uploads/products')),
    'upload_public_prefix' => env('SHOP_UPLOAD_PUBLIC_PREFIX', '/images/uploads/products'),
];
