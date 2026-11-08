<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;

Route::get('/products', function () {
    return Product::select([
        'sku',
        'category_id',
        'brand_id',
        'name',
        'price',
        'stock_quantity',
        'grade',
        'scale',
        'series',
        'image',
        'status',
    ])
    ->with(['category', 'brand'])
    ->where('status', 'Còn hàng')
    ->get();
});