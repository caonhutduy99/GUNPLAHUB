<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Support\ValidationMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand']);

        if ($keyword = trim((string) $request->input('q'))) {
            $query->where(function ($w) use ($keyword) {
                $w->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return view('admin.products.index', [
            'products' => $query->orderBy('sku')->paginate(10)->withQueryString(),
            'categories' => Category::ordered()->get(),
            'brands' => Brand::ordered()->get(),
        ]);
    }

    public function create()
    {
        return view('admin.products.create', [
            'categories' => Category::ordered()->get(),
            'brands' => Brand::ordered()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $category = $this->categoryFrom($request);
        $data = $request->validate($this->rules(null, $category), ValidationMessages::common(), $this->attributes());

        $brand = Brand::findOrFail($data['brand_id']);
        $data['sku'] = strtoupper($data['sku']);

        // SKU phải bắt đầu bằng mã thương hiệu, ví dụ BAN-HG-UC-005
        if (! str_starts_with($data['sku'], $brand->code . '-')) {
            throw ValidationException::withMessages([
                'sku' => "Mã SKU phải bắt đầu bằng \"{$brand->code}-\" (mã của thương hiệu {$brand->name}).",
            ]);
        }
        if (Product::whereKey($data['sku'])->exists()) {
            throw ValidationException::withMessages(['sku' => 'Mã SKU đã tồn tại.']);
        }

        unset($data['image']);
        $data['grade'] = $this->gradeFor($category);
        $data['status'] = $this->syncStatus($data['status'], (int) $data['stock_quantity']);
        if ($path = $this->saveImage($request)) {
            $data['image'] = $path;
        }

        $product = Product::create($data);
        AuditLog::record("Thêm sản phẩm {$product->sku}: {$product->name}");

        return redirect()->route('admin.products.index')->with('success', 'Đã thêm sản phẩm.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::ordered()->get(),
            'brands' => Brand::ordered()->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $category = $this->categoryFrom($request);
        $data = $request->validate($this->rules($product, $category), ValidationMessages::common(), $this->attributes());

        unset($data['image']);
        $data['grade'] = $this->gradeFor($category);
        $data['status'] = $this->syncStatus($data['status'], (int) $data['stock_quantity']);

        if ($path = $this->saveImage($request)) {
            $this->deleteImage($product->image); // xóa ảnh cũ
            $data['image'] = $path;
        }

        $oldPrice = $product->price;
        $oldStock = $product->stock_quantity;
        $product->update($data);

        $note = '';
        if ((float) $oldPrice !== (float) $product->price) {
            $note .= " | giá {$oldPrice} -> {$product->price}";
        }
        if ($oldStock !== $product->stock_quantity) {
            $note .= " | tồn kho {$oldStock} -> {$product->stock_quantity}";
        }
        AuditLog::record("Sửa sản phẩm {$product->sku}{$note}");

        return redirect()->route('admin.products.index')->with('success', 'Đã cập nhật sản phẩm.');
    }

    /**
     * "Xóa" sản phẩm = chuyển sang Ngừng kinh doanh (xóa mềm).
     * Không xóa thật để không làm hỏng lịch sử đơn hàng.
     * Muốn bán lại: vào Sửa và đổi trạng thái.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->update(['status' => 'Ngừng kinh doanh']);
        AuditLog::record("Ngừng kinh doanh sản phẩm {$product->sku}");

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã chuyển sang "Ngừng kinh doanh".');
    }

    // ---------------- Hàm phụ ----------------

    private function categoryFrom(Request $request): ?Category
    {
        $id = $request->input('category_id');

        return is_numeric($id) ? Category::find((int) $id) : null;
    }

    /** Cấp độ lấy từ danh mục "Gunpla XX"; phụ kiện thì không có cấp độ. */
    private function gradeFor(Category $category): ?string
    {
        return preg_match('/^Gunpla\s+(EG|HG|RG|MG|PG)$/', $category->name, $m) ? $m[1] : null;
    }

    private function rules(?Product $product, ?Category $category): array
    {
        // Với mô hình Gunpla thì tỷ lệ và series bắt buộc; phụ kiện thì có thể bỏ trống
        $requiredIfGunpla = ($category && $category->isGunpla()) ? 'required' : 'nullable';

        return [
            // SKU là khóa chính nên chỉ nhập khi thêm mới, không cho sửa
            'sku' => $product ? [] : [
                'required', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/',
            ],
            'brand_id' => ['required', 'exists:brands,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:1', 'max:99999999'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:100000'],
            'scale' => [$requiredIfGunpla, Rule::in(Product::SCALES)],
            'series' => [$requiredIfGunpla, 'string', 'max:50'],
            'status' => ['required', Rule::in(Product::STATUSES)],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    private function attributes(): array
    {
        return [
            'sku' => 'Mã SKU',
            'brand_id' => 'Thương hiệu',
            'category_id' => 'Cấp độ / Loại sản phẩm',
            'name' => 'Tên sản phẩm',
            'price' => 'Giá bán',
            'stock_quantity' => 'Số lượng tồn kho',
            'scale' => 'Tỷ lệ',
            'series' => 'Series',
            'status' => 'Trạng thái',
            'image' => 'Ảnh sản phẩm',
        ];
    }

    /**
     * Trạng thái Còn hàng / Hết hàng tự động theo tồn kho,
     * trừ khi admin chọn Ngừng kinh doanh.
     */
    private function syncStatus(string $status, int $stock): string
    {
        if ($status === 'Ngừng kinh doanh') {
            return $status;
        }

        return $stock > 0 ? 'Còn hàng' : 'Hết hàng';
    }

    private function saveImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');
        $name = Str::uuid() . '.' . $file->extension();
        $file->move(public_path('images/products'), $name);

        return 'images/products/' . $name;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
