<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $newCategories = [
        'Decal nước – WaterSlide Decal' => 6,
        'Sơn – Paint Color' => 7,
        'Dụng cụ – Tools' => 8,
    ];

    public function up(): void
    {
        foreach ($this->newCategories as $name => $order) {
            if (! DB::table('categories')->where('name', $name)->exists()) {
                DB::table('categories')->insert([
                    'name' => $name,
                    'sort_order' => $order,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Danh mục "Phụ kiện" chung được tách thành 3 danh mục con ở trên.
        // Chỉ xóa nếu chưa có sản phẩm nào, tránh mất dữ liệu.
        $old = DB::table('categories')->where('name', 'Phụ kiện')->first();
        if ($old && ! DB::table('products')->where('category_id', $old->id)->exists()) {
            DB::table('categories')->where('id', $old->id)->delete();
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->newCategories) as $name) {
            $category = DB::table('categories')->where('name', $name)->first();
            if ($category && ! DB::table('products')->where('category_id', $category->id)->exists()) {
                DB::table('categories')->where('id', $category->id)->delete();
            }
        }
    }
};
