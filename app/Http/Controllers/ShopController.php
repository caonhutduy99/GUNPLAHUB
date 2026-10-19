<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\ProductQuery;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        return view('shop.index', ProductQuery::listing($request));
    }

    public function show(Product $product)
    {
        abort_if($product->status === 'Ngừng kinh doanh', 404);

        $product->load('category');

        $related = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('sku', '!=', $product->sku)
            ->where('status', '!=', 'Ngừng kinh doanh')
            ->orderBy('sku')
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'related'));
    }
}
