<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Gunpla HG', 'Gunpla RG', 'Gunpla MG', 'Gunpla PG', 'Gunpla EG', 'Phụ kiện'] as $name) {
            Category::create(['name' => $name]);
        }
    }
}
