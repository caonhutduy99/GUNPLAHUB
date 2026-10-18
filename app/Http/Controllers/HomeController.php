<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->where('status', 'Còn hàng')
            ->latest()
            ->orderBy('sku')
            ->take(8)
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('home', compact('products', 'categories'));
    }
}
