<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $items = $request->user()
            ->wishlistItems()
            ->with('product.category', 'product.brand')
            ->latest()
            ->get();

        return view('wishlist.index', compact('items'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        if ($product->status === 'Ngừng kinh doanh') {
            abort(404);
        }

        $request->user()->wishlistItems()->firstOrCreate([
            'product_sku' => $product->sku,
        ]);

        return back()->with('success', 'Đã thêm sản phẩm vào danh sách yêu thích.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $request->user()->wishlistItems()->where('product_sku', $product->sku)->delete();

        return back()->with('success', 'Đã xóa sản phẩm khỏi danh sách yêu thích.');
    }
}
