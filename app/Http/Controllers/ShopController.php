<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /** Các kiểu sắp xếp cho phép (khóa => nhãn hiển thị) */
    public const SORTS = [
        'newest' => 'Mới nhất',
        'price_asc' => 'Giá tăng dần',
        'price_desc' => 'Giá giảm dần',
        'name_asc' => 'Tên A-Z',
    ];

    public function index(Request $request)
    {
        // Khách không thấy sản phẩm đã Ngừng kinh doanh
        $query = Product::with('category')->where('status', '!=', 'Ngừng kinh doanh');

        if ($keyword = trim((string) $request->input('q'))) {
            $query->where(function ($w) use ($keyword) {
                $w->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->input('category_id'));
        }
        if ($request->filled('grade')) {
            $query->where('grade', $request->input('grade'));
        }
        if ($request->filled('scale')) {
            $query->where('scale', $request->input('scale'));
        }
        if ($request->filled('series')) {
            $query->where('series', $request->input('series'));
        }
        if (is_numeric($request->input('price_min'))) {
            $query->where('price', '>=', (float) $request->input('price_min'));
        }
        if (is_numeric($request->input('price_max'))) {
            $query->where('price', '<=', (float) $request->input('price_max'));
        }
        if ($request->boolean('in_stock')) {
            $query->where('status', 'Còn hàng');
        }

        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name_asc' => $query->orderBy('name'),
            default => $query->latest()->orderBy('sku'),
        };

        return view('shop.index', [
            'products' => $query->paginate(12)->withQueryString(),
            'categories' => Category::ordered()->get(),
            'seriesList' => Product::where('status', '!=', 'Ngừng kinh doanh')
                ->distinct()->orderBy('series')->pluck('series'),
            'sorts' => self::SORTS,
            'sort' => array_key_exists($sort, self::SORTS) ? $sort : 'newest',
        ]);
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
