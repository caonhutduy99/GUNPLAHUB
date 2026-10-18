<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Gunpla EG', 'Gunpla HG', 'Gunpla RG', 'Gunpla MG', 'Gunpla PG', 'Phụ kiện'];

        foreach ($names as $index => $name) {
            Category::create(['name' => $name, 'sort_order' => $index + 1]);
        }
    }
}
