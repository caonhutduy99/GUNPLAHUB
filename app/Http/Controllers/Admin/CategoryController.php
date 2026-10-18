<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Support\ValidationMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            ['name' => ['required', 'string', 'max:50', 'unique:categories,name']],
            ValidationMessages::common(),
            ['name' => 'Tên danh mục']
        );

        $category = Category::create($data);
        AuditLog::record("Thêm danh mục #{$category->id}: {$category->name}");

        return redirect()->route('admin.categories.index')->with('success', 'Đã thêm danh mục.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate(
            ['name' => ['required', 'string', 'max:50', 'unique:categories,name,' . $category->id]],
            ValidationMessages::common(),
            ['name' => 'Tên danh mục']
        );

        $oldName = $category->name;
        $category->update($data);
        AuditLog::record("Sửa danh mục #{$category->id}: {$oldName} -> {$category->name}");

        return redirect()->route('admin.categories.index')->with('success', 'Đã cập nhật danh mục.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Không thể xóa danh mục đang có sản phẩm.');
        }

        $name = $category->name;
        $category->delete();
        AuditLog::record("Xóa danh mục: {$name}");

        return redirect()->route('admin.categories.index')->with('success', 'Đã xóa danh mục.');
    }
}
