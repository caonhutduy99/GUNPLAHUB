<?php

namespace App\Support;

use App\Models\Brand;
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

    /**
     * Các mức giá có sẵn dạng checkbox (chọn nhiều mức = hoặc).
     * Mỗi khoảng là (min, max], riêng khoảng đầu là [min, max] để không bị trùng biên.
     */
    public const PRICE_RANGES = [
        'under_100' => ['label' => 'Dưới 100.000đ', 'min' => null, 'max' => 100000, 'max_op' => '<'],
        'r_100_300' => ['label' => '100.000đ – 300.000đ', 'min' => 100000, 'min_op' => '>=', 'max' => 300000, 'max_op' => '<='],
        'r_300_500' => ['label' => '300.000đ – 500.000đ', 'min' => 300000, 'min_op' => '>', 'max' => 500000, 'max_op' => '<='],
        'r_500_1000' => ['label' => '500.000đ – 1.000.000đ', 'min' => 500000, 'min_op' => '>', 'max' => 1000000, 'max_op' => '<='],
        'r_1000_2000' => ['label' => '1.000.000đ – 2.000.000đ', 'min' => 1000000, 'min_op' => '>', 'max' => 2000000, 'max_op' => '<='],
        'r_2000_3000' => ['label' => '2.000.000đ – 3.000.000đ', 'min' => 2000000, 'min_op' => '>', 'max' => 3000000, 'max_op' => '<='],
        'over_3000' => ['label' => 'Trên 3.000.000đ', 'min' => 3000000, 'min_op' => '>', 'max' => null],
    ];

    /** Dữ liệu dùng chung cho view danh sách sản phẩm */
    public static function listing(Request $request): array
    {
        $query = Product::with(['category', 'brand']);
        self::applyFilters($query, $request);
        $sort = self::applySort($query, (string) $request->input('sort', 'newest'));

        return [
            'products' => $query->paginate(self::PER_PAGE)->withQueryString(),
            'brands' => Brand::ordered()->get(),
            'categories' => Category::ordered()->get(),
            'seriesList' => Product::where('status', '!=', 'Ngừng kinh doanh')
                ->whereNotNull('series')->distinct()->orderBy('series')->pluck('series'),
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

        // Cấp độ / loại sản phẩm (chính là danh mục): chọn nhiều
        $categoryIds = collect(Arr::wrap($request->input('category_id')))
            ->filter(fn ($v) => is_numeric($v))
            ->map(fn ($v) => (int) $v)
            ->all();
        if ($categoryIds) {
            $query->whereIn('category_id', $categoryIds);
        }

        // Thương hiệu: chọn nhiều
        $brandIds = collect(Arr::wrap($request->input('brand_id')))
            ->filter(fn ($v) => is_numeric($v))
            ->map(fn ($v) => (int) $v)
            ->all();
        if ($brandIds) {
            $query->whereIn('brand_id', $brandIds);
        }

        if ($request->filled('series') && is_string($request->input('series'))) {
            $query->where('series', $request->input('series'));
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
