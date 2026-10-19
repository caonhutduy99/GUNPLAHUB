<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['Bandai', 'BAN'], ['ChangLong', 'CHL'], ['GaoGao', 'GAO'], ['Motor Nuclear', 'MNC'],
            ['SUPA model', 'SUP'], ['ShenShou model', 'SHS'], ['Daban', 'DAB'], ['Moshow model', 'MOS'],
            ['SNAA Model', 'SNA'], ['SMP Model', 'SMP'], ['Eastern Model', 'EAS'], ['SUYATA Model', 'SUY'],
        ];

        foreach ($brands as $index => [$name, $code]) {
            Brand::create(['name' => $name, 'code' => $code, 'sort_order' => $index + 1]);
        }
    }
}
