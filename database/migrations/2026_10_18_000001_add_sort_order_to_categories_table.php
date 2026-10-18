<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Danh mục mới mặc định xếp cuối (100)
            $table->unsignedSmallInteger('sort_order')->default(100)->after('name');
        });

        // Gán thứ tự cho các danh mục có sẵn: EG, HG, RG, MG, PG, Phụ kiện
        $order = ['Gunpla EG' => 1, 'Gunpla HG' => 2, 'Gunpla RG' => 3, 'Gunpla MG' => 4, 'Gunpla PG' => 5, 'Phụ kiện' => 6];
        foreach ($order as $name => $position) {
            DB::table('categories')->where('name', $name)->update(['sort_order' => $position]);
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
