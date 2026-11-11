<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Gunpla EG',
            'Gunpla HG',
            'Gunpla RG',
            'Gunpla MG',
            'Gunpla PG',
            'Decal nước – WaterSlide Decal',
            'Sơn – Paint Color',
            'Dụng cụ – Tools',
        ];

        foreach ($names as $index => $name) {
            // Migration tách danh mục phụ kiện đã chèn sẵn 3 danh mục cuối, nên không dùng create() để tránh trùng.
            Category::updateOrCreate(['name' => $name], ['sort_order' => $index + 1]);
        }
    }
}
