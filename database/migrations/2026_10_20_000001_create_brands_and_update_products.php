<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('code', 10)->unique(); // tiền tố của SKU
            $table->unsignedSmallInteger('sort_order')->default(100);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('category_id')
                ->constrained('brands')->restrictOnDelete();

            // Phụ kiện (decal, sơn, dụng cụ) không có cấp độ, tỷ lệ, series
            $table->string('grade', 20)->nullable()->change();
            $table->string('scale', 20)->nullable()->change();
            $table->string('series', 50)->nullable()->change();
        });

        $brands = [
            ['Bandai', 'BAN'], ['ChangLong', 'CHL'], ['GaoGao', 'GAO'], ['Motor Nuclear', 'MNC'],
            ['SUPA model', 'SUP'], ['ShenShou model', 'SHS'], ['Daban', 'DAB'], ['Moshow model', 'MOS'],
            ['SNAA Model', 'SNA'], ['SMP Model', 'SMP'], ['Eastern Model', 'EAS'], ['SUYATA Model', 'SUY'],
        ];
        foreach ($brands as $index => [$name, $code]) {
            DB::table('brands')->insert([
                'name' => $name,
                'code' => $code,
                'sort_order' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Các sản phẩm hiện có mặc định thuộc hãng Bandai
        $bandaiId = DB::table('brands')->where('code', 'BAN')->value('id');
        DB::table('products')->whereNull('brand_id')->update(['brand_id' => $bandaiId]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('brand_id');
        });

        Schema::dropIfExists('brands');
    }
};
