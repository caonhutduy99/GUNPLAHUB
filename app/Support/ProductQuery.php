<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Xử lý chung việc lọc, tìm kiếm, sắp xếp sản phẩm cho Trang chủ và Cửa hàng.
 */
class ProductQuery
{
    public const PER_PAGE = 12;

    /** Các kiểu sắp xếp (khóa => nhãn hiển thị) */
    public const SORTS = [
        'newest' => 'Mới nhất',
        'bestseller' => 'Bán chạy',
        'price_asc' => 'Giá thấp → cao',
        'price_desc' => 'Giá cao → thấp',
        'name_asc' => 'Tên A → Z',
        'name_desc' => 'Tên Z → A',
    ];

    /** Các mức giá có sẵn dạng checkbox (chọn nhiều mức = hoặc) */
    public const PRICE_RANGES = [
        'under_100' => ['label' => 'Dưới 100.000đ', 'min' => null, 'max' => 100000, 'max_op' => '<'],
        'r_100_300' => ['label' => '100.000đ – 300.000đ', 'min' => 100000, 'max' => 300000, 'max_op' => '<='],
        'r_300_500' => ['label' => '300.000đ – 500.000đ', 'min' => 300000, 'max' => 500000, 'max_op' => '<='],
        'r_500_1000' => ['label' => '500.000đ – 1.000.000đ', 'min' => 500000, 'max' => 1000000, 'max_op' => '<='],
        'over_1000' => ['label' => 'Trên 1.000.000đ', 'min' => 1000000, 'min_op' => '>', 'max' => null],
    ];

    /** Dữ liệu dùng chung cho view danh sách sản phẩm */
    public static function listing(Request $request): array
    {
        $query = Product::with('category');
        self::applyFilters($query, $request);
        $sort = self::applySort($query, (string) $request->input('sort', 'newest'));

        $categories = Category::ordered()->get();
        $isGunpla = fn ($category) => str_starts_with($category->name, 'Gunpla');

        return [
            'products' => $query->paginate(self::PER_PAGE)->withQueryString(),
            'gunplaCategories' => $categories->filter($isGunpla)->values(),
            'accessoryCategories' => $categories->reject($isGunpla)->values(),
            'seriesList' => Product::where('status', '!=', 'Ngừng kinh doanh')
                ->distinct()->orderBy('series')->pluck('series'),
            'sorts' => self::SORTS,
            'sort' => $sort,
            'priceRanges' => self::PRICE_RANGES,
        ];
    }

    public static function applyFilters(Builder $query, Request $request): void
    {
        // Khách không thấy sản phẩm đã Ngừng kinh doanh
        $query->where('status', '!=', 'Ngừng kinh doanh');

        if ($keyword = trim((string) $request->input('q'))) {
            $query->where(function ($w) use ($keyword) {
                $w->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }

        // Danh mục: chọn nhiều
        $categoryIds = collect(Arr::wrap($request->input('category_id')))
            ->filter(fn ($v) => is_numeric($v))
            ->map(fn ($v) => (int) $v)
            ->all();
        if ($categoryIds) {
            $query->whereIn('category_id', $categoryIds);
        }

        foreach (['grade', 'scale', 'series'] as $field) {
            if ($request->filled($field) && is_string($request->input($field))) {
                $query->where($field, $request->input($field));
            }
        }

        // Mức giá có sẵn (checkbox): hợp của các mức đã chọn
        $selected = array_intersect(array_keys(self::PRICE_RANGES), Arr::wrap($request->input('price_range')));
        if ($selected) {
            $query->where(function ($w) use ($selected) {
                foreach ($selected as $key) {
                    $range = self::PRICE_RANGES[$key];
                    $w->orWhere(function ($x) use ($range) {
                        if ($range['min'] !== null) {
                            $x->where('price', $range['min_op'] ?? '>=', $range['min']);
                        }
                        if ($range['max'] !== null) {
                            $x->where('price', $range['max_op'], $range['max']);
                        }
                    });
                }
            });
        }

        // Khoảng giá tự nhập
        if (is_numeric($request->input('price_min'))) {
            $query->where('price', '>=', (float) $request->input('price_min'));
        }
        if (is_numeric($request->input('price_max'))) {
            $query->where('price', '<=', (float) $request->input('price_max'));
        }

        if ($request->boolean('in_stock')) {
            $query->where('status', 'Còn hàng');
        }
    }

    /** Áp dụng sắp xếp, trả về khóa sắp xếp hợp lệ đang dùng */
    public static function applySort(Builder $query, string $sort): string
    {
        if (! array_key_exists($sort, self::SORTS)) {
            $sort = 'newest';
        }

        match ($sort) {
            // Bán chạy: tổng số lượng bán ra ở các đơn đã giao thành công
            'bestseller' => $query
                ->withSum(['orderDetails as sold_quantity' => function ($q) {
                    $q->whereHas('order', fn ($o) => $o->where('status', 'Đã giao'));
                }], 'quantity')
                ->orderByDesc('sold_quantity')
                ->orderBy('name'),
            'price_asc' => $query->orderBy('price')->orderBy('name'),
            'price_desc' => $query->orderByDesc('price')->orderBy('name'),
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            default => $query->latest()->orderBy('sku'),
        };

        return $sort;
    }
}
